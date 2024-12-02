<?php

require_once 'zoom/autoload.php';
require_once 'zoom/class_zoom/class-db.php';



$REDIRECT_URI=ObtenConfiguracion(139);
 
#Conexion default
$Query="SELECT client_id_zoom,client_secret_zoom,id FROM zoom WHERE id=1 ";
$row=RecuperaValor($Query);
$CLIENT_ID=$row[0];
$CLIENT_SECRET=$row[1];
$host_id_email_zoom=$row[2];
define('CLIENT_ID', ''.$CLIENT_ID.'');
define('CLIENT_SECRET', ''.$CLIENT_SECRET.'');
define('REDIRECT_URI', ''.$REDIRECT_URI.'');


function create_meetingZoom($fl_live_session,$duration,$topic,$date,$pass='',$tabla_db_clase,$id_licencia_usar) {

    #Conexion default.
    $Query="SELECT client_id_zoom,client_secret_zoom,id,host_id,host_email_zoom FROM zoom WHERE id=$id_licencia_usar ";
    $row=RecuperaValor($Query);
    $CLIENT_ID=$row[0];
    $CLIENT_SECRET=$row[1];
    $host_id_email_zoom=$row[2];
    $host_id=$row[3];
    $emailzoom=$row[4];
    $actualiza_metting="";

    $REDIRECT_URI=ObtenConfiguracion(139);

    switch ($tabla_db_clase){
        case "k_live_session_grupal":
            $tabla="k_live_session_grupal";
            $columna="fl_live_session_grupal";
            break;
        case "k_live_session":
            $tabla="k_live_session";
            $columna="fl_live_session";
            break;
        case "k_live_sesion_cg":
            $tabla="k_live_sesion_cg";
            $columna="fl_live_session_cg";
            break;
    }
    
    //Se le da un respiro para no saturar los request.
    usleep(100);
  
    $db = new DB();
    $refresh_token = $db->get_refersh_token($id_licencia_usar); #new
    //echo"llega aqui";
	$client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
   // echo"llega aqui 2";
    /***Realiza un refresh del token****/
    $response = $client->request('POST', '/oauth/token', [
        "headers" => [
            "Authorization" => "Basic ". base64_encode($CLIENT_ID.':'.$CLIENT_SECRET)
        ],
        'form_params' => [
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token
        ],
    ]);
    $db->update_access_token($response->getBody(),$host_id_email_zoom);
    
/******/
   // echo"llega aqui 3";

    #Primero se hace un refresh del token para conectarse a la API.
    //$actualiza_metting=$db->update_access_token(json_encode($token),$id_licencia_usar);

    $arr_token = $db->get_access_token($host_id_email_zoom);
    $accessToken = $arr_token->access_token;
 
  //  echo"llega aqui 4";
    
    try {
        $response = $client->request('POST', '/v2/users/me/meetings', [
            "headers" => [
                "Authorization" => "Bearer $accessToken"
            ],
            'json' => [
                "host_id"=>$host_id,
                "topic" => $topic,
                "type" => 2,
                "start_time" => $date, //"2020-05-05T20:30:00",
                "enable_waiting_room"=>false, //true /false es para entrar directamente ala junta sin terner que esperar.
                "option_jbh"=>true,  // Join meeting before host start the meeting. Only for scheduled or recurring meetings.
                "option_host_video"=>true,//Start video when host join meeting.
                "option_participants_video"=>true,//Start video when participants join meeting.
                "duration" => $duration // 30 mins
                //"password" => $pass
            ],
        ]);
        
        $data = json_decode($response->getBody());
        $zoom_url= $data->join_url;
        $zoom_password=$data->password;
        $zoom_meeting_id=$data->id;
        $zoom_host_id= $data->host_id;
        //echo "Join URL: ". $data->join_url;
        //echo "<br>";
        //echo "Meeting Password: ". $data->password;
        #Inserta en la BD.
        $Query="UPDATE $tabla SET zoom_password='$zoom_password',zoom_url='$zoom_url',zoom_meeting_id='$zoom_meeting_id',zoom_host_id='$zoom_host_id',zoom_id=$id_licencia_usar  WHERE $columna =$fl_live_session ";
        EjecutaQuery($Query);

        $Query="SELECT no_request FROM zoom WHERE id=$id_licencia_usar";
        $row=RecuperaValor($Query);
        if($row[0]<=100){
            EjecutaQuery("UPDATE zoom SET no_request=(SELECT no_request WHERE id=$id_licencia_usar)+1 , fe_ultmod=CURRENT_TIMESTAMP  WHERE id=$id_licencia_usar ");
        }

        return true;
        
    }
    catch(Exception $e) {
        echo $e->getMessage();
        if( 401 == $e->getCode() ) {
            $refresh_token = $db->get_refersh_token($host_id_email_zoom);
            
            $client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
            $response = $client->request('POST', '/oauth/token', [
                "headers" => [
                    "Authorization" => "Basic ". base64_encode($CLIENT_ID.':'.$CLIENT_SECRET)
                ],
                'form_params' => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refresh_token
                ],
            ]);
            $db->update_access_token($response->getBody(),$host_id_email_zoom);
            
            //create_meeting();
        } else {
            //echo $e->getMessage();
        }

        return false;
    }
}

function DeletedMeetingZoom($fl_live_sesion,$tabla_db_clase,$host_id_email_zoom=''){

    switch ($tabla_db_clase){
        case "k_live_session_grupal":
            $tabla="k_live_session_grupal";
            $columna="fl_live_session_grupal";
            break;
        case "k_live_session":
            $tabla="k_live_session";
            $columna="fl_live_session";
            break;
        case "k_live_sesion_cg":
            $tabla="k_live_sesion_cg";
            $columna="fl_live_session_cg";
            break;
    }
   
    #Recupreamos el meting de la clase y eliminamos
    $QuerySel="SELECT zoom_meeting_id FROM $tabla WHERE $columna=$fl_live_sesion ";
    $rop=RecuperaValor($QuerySel);
    $zoom_meeting_id=$rop[0];

    #Conexion para eliminar.
    $Query="SELECT client_id_zoom,client_secret_zoom,id,host_id,host_email_zoom FROM zoom WHERE id=$host_id_email_zoom ";
    $row=RecuperaValor($Query);
    $CLIENT_ID=$row[0];
    $CLIENT_SECRET=$row[1];
    $host_id_email_zoom=$row[2];
    $host_id=$row[3];
    $emailzoom=$row[4];
    $actualiza_metting="";




    
    $db = new DB();
    
    #/***Realiza un refresh del token****/
    $refresh_token = $db->get_refersh_token($host_id_email_zoom); 
    $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);

    $response = $client->request('POST', '/oauth/token', [
        "headers" => [
            "Authorization" => "Basic ". base64_encode($CLIENT_ID.':'.$CLIENT_SECRET)
        ],
        'form_params' => [
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token
        ],
    ]);
    $db->update_access_token($response->getBody(),$host_id_email_zoom);
    

	
    #$host_id_email_zoom=1; 
    if(!empty($host_id_email_zoom)){
        
        $arr_token = $db->get_access_token($host_id_email_zoom);
        $accessToken = $arr_token->access_token;

        try {
            $response = $client->request('DELETE', '/v2/meetings/'.$zoom_meeting_id.'', [
                "headers" => [
                    "Authorization" => "Bearer $accessToken"
                ]
            ]);

        }
        catch(Exception $e) {
            //echo $e->getMessage();
        }

    }
    

}



function VerifyMeetingZoom($host_id_email_zoom,$zoom_meeting_id){
    


    #Conexion para eliminar.
    $Query="SELECT client_id_zoom,client_secret_zoom,id,host_id,host_email_zoom FROM zoom WHERE id=$host_id_email_zoom ";
    $row=RecuperaValor($Query);
    $CLIENT_ID=$row[0];
    $CLIENT_SECRET=$row[1];
    $host_id_email_zoom=$row[2];
    $host_id=$row[3];
    $emailzoom=$row[4];
    $actualiza_metting="";

    $exists=0;
    
    $db = new DB();
    
    #
    $refresh_token = $db->get_refersh_token($host_id_email_zoom); 
    $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);

    $response = $client->request('POST', '/oauth/token', [
        "headers" => [
            "Authorization" => "Basic ". base64_encode($CLIENT_ID.':'.$CLIENT_SECRET)
        ],
        'form_params' => [
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token
        ],
    ]);
    $db->update_access_token($response->getBody(),$host_id_email_zoom);
    
    
    if(!empty($host_id_email_zoom)){
        
        $arr_token = $db->get_access_token($host_id_email_zoom);
        $accessToken = $arr_token->access_token;

        try {
            $response = $client->request('GET', '/v2/meetings/'.$zoom_meeting_id.'', [
                "headers" => [
                    "Authorization" => "Bearer $accessToken"
                ]
            ]);
            if(!empty($response)){
                $exists=1;
                
            }else{
                $exists=0;
            }
                

        }
        catch(Exception $e) {

            $exists=0;
            #$codigo=$e->getCode();
            $e->getMessage();

        }

    }
    
    return $exists;

}

function getInfoTraslapadasGlobalesGrupales($arr_claves,$array_clavesClasesNormales='',$arrClavesTraslapadasGlobalClass='') {
    
    $claves = implode(",", $arr_claves);     
    $claves_normales=implode(",",$array_clavesClasesNormales);
    $claves_global_class=implode(",",$arrClavesTraslapadasGlobalClass);

    $Query="SELECT cg.fl_grupo,cg.nb_grupo,ks.no_semana,kc.nb_clase ,ks.fl_semana_grupo,
            kc.fl_clase_grupo, DATE_FORMAT(kc.fe_clase, '%d-%m-%Y %H:%i') fe_clase  
            FROM c_grupo cg 
            JOIN k_clase_grupo kc ON kc.fl_grupo=cg.fl_grupo
            JOIN k_semana_grupo ks ON ks.fl_semana_grupo=kc.fl_semana_grupo
            WHERE kc.fl_clase_grupo IN($claves)ORDER BY cg.nb_grupo,ks.no_semana,kc.fl_clase_grupo ";
    $tit_grupo = ObtenEtiqueta(420);
    $tit_semana = ObtenEtiqueta(716);
    $tit_titulo = ObtenEtiqueta(385);
    $tit_fecha = ObtenEtiqueta(425);
    $tit_tipo = ObtenEtiqueta(44);
    
    $rs = EjecutaQuery($Query);
    $tot=CuentaRegistros($rs);
    $info = "<table class=\"tabla_traslapadas\">";
    $info .= "<tr><th>$tit_grupo</th><th>$tit_semana</th><th>$tit_titulo</th><th>$tit_fecha</th><th>$tit_tipo</th></tr>";
    for($i = 0; $row = RecuperaRegistro($rs); $i++) {
        $nb_grupo = $row[1];
        $no_semana = $row[2];
        $titulo = $row[3];
        $fecha = $row[6];
        $fgAdicional = $row[7];

        $adicional = "";
        if ($fgAdicional)
            $adicional = "Extraclass";


        #Recupermos la licencia utilizada.
        $Query="SELECT a.zoom_id,b.host_email_zoom FROM k_live_session_grupal a JOIN zoom b ON a.zoom_id=b.id WHERE a.fl_clase_grupo=$row[5] ";
        $row=RecuperaValor($Query);
        $host_email_zoom=$row['host_email_zoom'];


        
        $info .= "<tr><td>$nb_grupo <br><b><i>$host_email_zoom</i></b></td><td>$no_semana</td><td>$titulo</td><td>$fecha</td><td>$adicional</td></tr>";
    }
    
    if(!empty($claves_normales)){
        
        $Query2="SELECT cg.fl_grupo,cg.nb_grupo,l.no_semana,l.ds_titulo,ks.fl_semana,kc.fl_clase,DATE_FORMAT(kc.fe_clase, '%d-%m-%Y %H:%i') fe_clase,kc.fg_adicional 
				FROM c_grupo cg
				JOIN k_clase kc ON cg.fl_grupo=kc.fl_grupo
				JOIN k_semana ks ON ks.fl_semana=kc.fl_semana
				JOIN k_term t ON t.fl_term=ks.fl_term
				JOIN c_leccion l ON l.fl_leccion=ks.fl_leccion
				WHERE kc.fl_clase IN($claves_normales)
			   ORDER BY cg.nb_grupo,t.no_grado, kc.fl_clase ";
        $rs2 = EjecutaQuery($Query2);
        $tot2=CuentaRegistros($rs2);    
        for($m = 0; $row2 = RecuperaRegistro($rs2); $m++) {
            $nb_grupo = $row2[1];
            $no_semana = $row2[2];
            $titulo = $row2[3];
            $fecha = $row2[6];
            $fgAdicional = $row2[7];

            #Recupermos la licencia utilizada.
            $Query="SELECT a.zoom_id,b.host_email_zoom FROM k_live_session a JOIN zoom b ON a.zoom_id=b.id WHERE a.fl_clase=$row2[5] ";
            $row=RecuperaValor($Query);
            $host_email_zoom=$row['host_email_zoom'];


            $adicional = "";
            if ($fgAdicional)
                $adicional = "Extraclass";
            
            $info .= "<tr><td>$nb_grupo <br><b><i>$host_email_zoom</i></b></td><td>$no_semana</td><td>$titulo</td><td>$fecha</td><td>$adicional</td></tr>";


        }

    }

    if(!empty($claves_global_class)){
        
        $Query3="(  
                SELECT kc.fl_clase_global,ds_titulo,no_orden,ds_titulo,''fl_semana,fl_clase_cg, DATE_FORMAT(kc.fe_clase, '%d-%m-%Y %H:%i') fe_clase,''fg_adicional
		                FROM k_clase_cg kc
		                WHERE fl_clase_global IN(621) 
                )UNION(
                SELECT kc.fl_clase_global,ds_titulo,no_orden,ds_titulo,''fl_semana,fl_clase_cg, DATE_FORMAT(kc.fe_clase, '%d-%m-%Y %H:%i') fe_clase,''fg_adicional
		                FROM k_clase_cg_temporal kc
		                WHERE fl_clase_cg IN($claves_global_class)
                )	";
        $rs3 = EjecutaQuery($Query3);
        $tot3=CuentaRegistros($rs3);    
        for($z = 0; $row3 = RecuperaRegistro($rs3); $z++) {
            $nb_grupo = $row3[1];
            $no_semana = $row3[2];
            $titulo = $row3[3];
            $fecha = $row3[6];
            

            #Recupermos la licencia utilizada.
            $Query="SELECT a.zoom_id,b.host_email_zoom FROM k_live_sesion_cg a JOIN zoom b ON a.zoom_id=b.id WHERE a.fl_clase_cg=$row3[5] ";
            $row=RecuperaValor($Query);
            $host_email_zoom=$row['host_email_zoom'];

            
            $info .= "<tr><td>Global Class <br><b><i>$host_email_zoom</i></b></td><td>$no_semana</td><td>$titulo</td><td>$fecha</td><td></td></tr>";


        }


    }
    
    return $info;
   


    
}






















##############################################################################3


function delLiveSessionZoom($fl_clase) {
    
    // Borra la clase en adobe connect
    $QuerySel  = "select fl_live_session, cl_licencia, cl_meeting_id ";
    $QuerySel .= "from k_live_session where fl_clase = $fl_clase";   
    $rowDel = RecuperaValor($QuerySel);
    
    $fl_live_session_actual = $rowDel[0];
 
    $Query = "DELETE FROM k_live_session WHERE fl_clase = $fl_clase";
    EjecutaQuery($Query);

}  

//Para obtener las clases traslapadas 
function getClavesLicenciasTraslapadasZoom($fechaHora, $clave_clase='') {

      $duracionClasePrevia = (ObtenConfiguracion(94) - 1); // El valor esta en minutos en la tabla de configuracion
      $duracionClase = ObtenConfiguracion(94); // El valor esta en minutos en la tabla de configuracion

      $clavesLicenciasUsadas = array();
      # Cambia el Query para que haga union clases normales y globales
      $Query  = "SELECT DISTINCT cl_licencia, fl_clase_global,zoom_id FROM ( ";
      $Query .= "(SELECT kls.cl_licencia, 0 fl_clase_global,kls.zoom_id FROM k_clase kc, k_live_session kls WHERE kc.fl_clase = kls.fl_clase ";
      $Query .= "AND ((DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
      $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')) OR ";
      $Query .= "(DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
      $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s') )) ORDER BY kls.cl_licencia) UNION ";
      $Query .= "(SELECT kls.cl_licencia, kc.fl_clase_global,kls.zoom_id FROM k_clase_cg kc, k_live_sesion_cg kls WHERE kc.fl_clase_cg = kls.fl_clase_cg ";
      $Query .= "AND ((DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
      $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')) OR ";
      $Query .= "(DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
      $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))) ORDER BY kls.cl_licencia)";
      $Query .="UNION (";
      $Query .="SELECT klg.cl_licencia,klg.fl_clase_grupo,klg.zoom_id 
                FROM k_clase_grupo kcg 
                join  k_live_session_grupal klg ON klg.fl_clase_grupo =kcg.fl_clase_grupo
                WHERE ((DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s')BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
                AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')) OR 
                (DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
                AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))) 
                ORDER BY klg.zoom_id  ";
      $Query .=")";
      $Query .= ") AS main  ";
      //echo "<br> Query para claves lic traslapadas: $Query <br>";

      $rs = EjecutaQuery($Query);
      for($i = 0; $row = RecuperaRegistro($rs); $i++) {
          $zoom_id=!empty($row[2])?$row[2]:0;
          
          array_push($clavesLicenciasUsadas, $zoom_id,$row[1]);   
      }

      return $clavesLicenciasUsadas;        
  }  



function getLicenciasDisponiblesZoom($licenciasUsadas, $enInsertLiveSession='') {    
    
    $licenciasActivas = array();
    
    
    $fgConsideraLicencias = false;
            
    if ($enInsertLiveSession) {
      if (sizeof($licenciasUsadas) > 0) {
        //echo "Tenemos licencias usadas: " . sizeof($licenciasUsadas) . "<br>";
        //echo "Lic usadas: " . $licenciasUsadas[0] . "<br>";
        $arrLicUsadas = implode(',', $licenciasUsadas);
        $fgConsideraLicencias = true;
      }
    }
    
    $Query = "SELECT * FROM zoom WHERE fg_activo = '1' ";
    if ($fgConsideraLicencias)
      $Query .= "AND id NOT IN ({$arrLicUsadas})";
      
    //echo "<br>Licencias disponibles: $Query <br>";  
    $rs = EjecutaQuery($Query);
    for($i = 0; $row = RecuperaRegistro($rs); $i++) {
      $idLicencia = $row[0];
      $host_email_zoom = $row[2];
      
      array_push($licenciasActivas, $idLicencia);
      
    }

    
    return $licenciasActivas;
  }

#Para insertar la clase grupal global.
function iuLiveSessionGGZoom($p_fl_clase, $class_normales = false) {
    #Recuperamos datos
    $Query  = "SELECT kcg.nb_clase, DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') fe_ini, ";
    $Query .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL 2 HOUR), '%Y-%m-%dT%H:%i:%s') fe_fin ";
    $Query .= "FROM k_clase_grupo kcg WHERE kcg.fl_clase_grupo=$p_fl_clase ";       
    $row = RecuperaValor($Query);
    $titulo_leccion = $row[0];
    $fecha_inicio = $row[1];
    $fecha_fin = $row[2];
    
    #Recuperaos datos
    $Query  = "SELECT fl_live_session_grupal, cl_meeting_id ";
    $Query .= "FROM k_live_session_grupal WHERE fl_clase_grupo = $p_fl_clase ";
    $row = RecuperaValor($Query);
    $fl_live_session = !empty($row[0])?$row[0]:NULL;
    $cl_meeting_id = !empty($row[1])?$row[1]:NULL;
    
    // No existe la sesion y la crea, existe y la actualiza
    if (empty($fl_live_session)) { 
        
        $Query  = "INSERT INTO k_live_session_grupal ";
        $Query .= "(fl_clase_grupo,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente,ds_mensaje_bienvenida,cl_meeting_id,cl_licencia) ";
        $Query .= "VALUES ($p_fl_clase, 1, '', '', '', '', '', 0) ";
        EjecutaQuery($Query);
        
    }
    else {
        // $clienteAdobe->updateMeeting($cl_meeting_id, utf8_encode($titulo_leccion), $fecha_inicio, $fecha_fin);
    }	  
    
}

# Se agrega un parametro para identificar si es clase global o normal
function delLiveSessionCGZoom($fl_clase) {
    
    $QuerySel="SELECT fl_live_session_grupal,cl_licencia, cl_meeting_id FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase ";
    $rop=RecuperaValor($QuerySel);
    $rowDel = RecuperaValor($QuerySel);
    $fl_live_session_actual = $rowDel[0];
    $cl_licencia_actual = $rowDel[1];
    $cl_meeting_id_actual = $rowDel[2];

    EjecutaQuery("DELETE FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase ");
}


function iuLiveSessionZoom($p_fl_clase,$class_normales = false) {
    
    // MDB ADOBECONNECT Datos para los parametros Integracion Adobe Connect
    // Se crean las sesiones de Adobe Connect desde este programa
    // Para BBB no es necesario crearlas, se hace al momento en que un teacher 
    // o un student entra a la sesion desde el campus.
    if(!$class_normales){
        $Query  = "select ds_titulo, DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') fe_ini, ";
        $Query .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL 2 HOUR), '%Y-%m-%dT%H:%i:%s') fe_fin ";
        $Query .= "from k_clase a, k_semana b, c_leccion c ";
        $Query .= "where a.fl_semana = b.fl_semana ";
        $Query .= "and b.fl_leccion = c.fl_leccion ";
        $Query .= "and a.fl_clase = $p_fl_clase";
    }
    else{
        $Query  = "SELECT kcg.ds_titulo, DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') fe_ini, ";
        $Query .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL 2 HOUR), '%Y-%m-%dT%H:%i:%s') fe_fin ";
        $Query .= "FROM k_clase_cg kcg WHERE kcg.fl_clase_cg=$p_fl_clase ";
    }
    $row = RecuperaValor($Query);
    $titulo_leccion = $row[0];
    $fecha_inicio = $row[1];
    $fecha_fin = $row[2];
    $id_leccion_url = "VAN_".$clLicencia."_".rand(1000000, 9999999);   

    // Crea o actualiza la reunion en adobe connect
    if(!$class_normales){
        $Query  = "SELECT fl_live_session, cl_meeting_id ";
        $Query .= "FROM k_live_session WHERE fl_clase = $p_fl_clase";
    }
    else{
        $Query  = "SELECT fl_live_session_cg, cl_meeting_id ";
        $Query .= "FROM k_live_sesion_cg WHERE fl_clase_cg = $p_fl_clase";
    }
    // ECHO $Query;
    $row = RecuperaValor($Query);

    $fl_live_session = $row[0];
    $cl_meeting_id = $row[1];

    // El nombre de la leccion para la sesion de adobe connect debe ser de maximo 60 caracteres
    // El titulo tiene concatenado el folio de la clase que es un int de 8 y un gion, por lo que para
    // el titulo de la leccion quedan 51 caracteres disponibles, trunco la cadena a esa longitud.
    $titulo_leccion = substr($titulo_leccion, 0, 51);
    
    $titulo_leccion = $titulo_leccion . "_" . $p_fl_clase;
    
    // No existe la sesion y la crea, existe y la actualiza
    if (empty($fl_live_session)) { 
        // Regresa un arreglo de tipo:
        // $data["estatus"]
        // $data["meeting_id"]
        // $data["url-path"];
        // echo "utf8_encode($titulo_leccion), $fecha_inicio, $fecha_fin, $id_leccion_url";
        //$data = $clienteAdobe->createMeeting('', utf8_encode($titulo_leccion), $fecha_inicio, $fecha_fin, $id_leccion_url);        

        //$meeting_id = $data["meeting_id"];
        
        //if ($data["estatus"] == "existe")
         //   $id_leccion_url = $data["url-path"];
        
        // Se creo la clase en adobe connect
       // if (!empty($meeting_id)) {
            // Permisos para que se puedan conectar a la clase
           // $clienteAdobe->permisosMeeting($meeting_id);    
           // $clienteAdobe->addHostMeeting($meeting_id,0);
            
            if(!$class_normales){
                $Query  = "INSERT INTO k_live_session ";
                $Query .= "(fl_clase, cl_estatus, ";
                $Query .= "ds_meeting_id, ds_password_admin, ds_password_asistente, ds_mensaje_bienvenida, cl_meeting_id, cl_licencia) ";
                $Query .= "VALUES ($p_fl_clase, 1, '', '', '', '', '', 0) ";
            }
            else{
                $Query  = "INSERT INTO k_live_sesion_cg ";
                $Query .= "(fl_clase_cg,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente,ds_mensaje_bienvenida,cl_meeting_id,cl_licencia) ";
                $Query .= "VALUES ($p_fl_clase, 1, '$id_leccion_url', '$moderatorPW', '$attendeePW', '$welcome', '$meeting_id', $clLicencia) ";
            }
            // echo $Query;
            EjecutaQuery($Query);
        //}
        //else {
            // No se pudo crear, puede ser que ya exista        
        //}
    }
    else {

        //$clienteAdobe->updateMeeting($cl_meeting_id, utf8_encode($titulo_leccion), $fecha_inicio, $fecha_fin);
    }	  
    // Termina codigo Adobe connect 
}



?>

