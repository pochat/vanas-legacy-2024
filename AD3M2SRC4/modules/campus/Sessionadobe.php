<?php
  require '../../lib/AdobeConnectClient.class.php';
  
  require '../../lib/adobeconnect/LicenciaAdobe.class.php';
  require '../../lib/adobeconnect/LicenciaAdobeService.class.php';
  require '../../lib/campusclases/ClasesService.class.php';  
  
  # Se agrega un parametro para identificar si es clase global o normal
  function delLiveSession($fl_clase, $p_global_class=False) {
    
    $licenciaServiceAux = new LicenciaAdobeService();      
    
    // Borra la clase en adobe connect
    if(!$p_global_class){
      $QuerySel  = "SELECT fl_live_session, cl_licencia, cl_meeting_id ";
      $QuerySel .= "FROM k_live_session where fl_clase = $fl_clase";
    }
    else{
      $QuerySel  = "SELECT fl_live_session_cg, cl_licencia, cl_meeting_id ";
      $QuerySel .= "FROM k_live_sesion_cg where fl_clase_cg = $fl_clase";
    }
    $rowDel = RecuperaValor($QuerySel);

    $fl_live_session_actual = $rowDel[0];
    $cl_licencia_actual = $rowDel[1];
    $cl_meeting_id_actual = $rowDel[2];

    /* 22-feb-2021 Se comenta ya no se usa METTING ADOBE LA FUNCION SI LA SEGUIMOS OCUPANDO
    $LicenciaActual = $licenciaServiceAux->getLicenciaByClave($cl_licencia_actual);
    if (!empty($cl_meeting_id_actual)) {
      //delLiveSession($cl_meeting_id_actual, $LicenciaActual);              
      //$clLicencia = $licenciaAC->getClLicencia();
      $clienteAdobe = new AdobeConnectClient($LicenciaActual);
      $clienteAdobe->deleteMeeting($cl_meeting_id_actual);
    }
   */ 
    
    if(!$p_global_class){
        $Query = "DELETE FROM k_live_session WHERE fl_clase = $fl_clase";
        EjecutaQuery($Query);
    }else{
        $Query ="DELETE FROM k_live_sesion_cg where fl_clase_cg = $fl_clase ";
        EjecutaQuery($Query);

    }



  }  

  # Informacion de las clases extras traslapadas 
  function getInfoTraslapadasNormales($arr_claves) {
    $claves = implode(",", $arr_claves);
    
    $Query  = "SELECT cg.fl_grupo, cg.nb_grupo, cl.no_semana, cl.ds_titulo, ks.fl_semana, ";
    $Query .= "kc.fl_clase, ";
    $Query .= ConsultaFechaBD('kc.fe_clase', FMT_DATETIME) . " fe_clase, kc.fg_adicional ";
    $Query .= "FROM c_grupo cg, k_semana ks, ";
    $Query .= "c_leccion cl, k_clase kc ";
    $Query .= "WHERE cg.fl_term = ks.fl_term ";
    $Query .= "AND ks.fl_leccion = cl.fl_leccion ";
    $Query .= "AND kc.fl_grupo = cg.fl_grupo ";
    $Query .= "AND kc.fl_semana = ks.fl_semana ";
    $Query .= "AND kc.fl_clase IN ($claves) ";
    $Query .= "ORDER BY cg.nb_grupo, cl.no_semana, kc.fl_clase ";  
    
    // $tit_grupo = ObtenEtiqueta(420);
    // $tit_semana = ObtenEtiqueta(716);
    // $tit_titulo = ObtenEtiqueta(385);
    // $tit_fecha = ObtenEtiqueta(425);
    // $tit_tipo = ObtenEtiqueta(44);    
    // $info = "<table class=\"tabla_traslapadas\">";      
    // if($class_normales){
      // $info .= "<tr><th>$tit_grupo</th><th>$tit_semana</th><th>$tit_titulo</th><th>$tit_fecha</th><th>$tit_tipo</th></tr>";
      $rs = EjecutaQuery($Query);
      for($i = 0; $row = RecuperaRegistro($rs); $i++) {
        $fl_grupo = $row[0];
        $nb_grupo = $row[1];
        $no_semana = $row[2];
        $titulo = $row[3];
        $fecha = $row[6];
        $fgAdicional = $row[7];

        $adicional = "";
        if ($fgAdicional)
          $adicional = "Extraclass";


        #Recupermos la licencia utilizada.
        $Query="SELECT a.zoom_id,b.host_email_zoom FROM k_live_session a JOIN zoom b ON a.zoom_id=b.id WHERE a.fl_clase=$row[5] ";
        $row=RecuperaValor($Query);
        $host_email_zoom=$row['host_email_zoom'];



        
        $info .= "<tr>
          <td><a href=\"".PATH_MODULOS."/campus/groups_frm.php?destino=1&clave=$fl_grupo\" target=\"blank\">$nb_grupo</a><br><b><i>$host_email_zoom</i></b></td>
          <td>$no_semana</td>
          <td>$titulo</td>
          <td>$fecha</td>
          <td>$adicional</td>
        </tr>";
      }
    // }
    // else{
      # Clase Globales
      // if(!$class_normales){      
        // $Queryg  = "SELECT kcg.fl_clase_global, cg.ds_clase, kcg.no_orden, kcg.ds_titulo, ";
        // $Queryg .= "kcg.fl_clase_cg, ";
        // $Queryg .= ConsultaFechaBD('kcg.fe_clase', FMT_DATETIME) . " fe_clase, kcg.fg_obligatorio ";
        // $Queryg .= "FROM k_clase_cg kcg, c_clase_global cg ";
        // $Queryg .= "WHERE kcg.fl_clase_global = cg.fl_clase_global AND kcg.fl_clase_cg IN($claves) ";
        // echo $Queryg .= "ORDER BY cg.ds_clase, kcg.no_orden, kcg.fl_clase_cg ";
        // $rsg = EjecutaQuery($Queryg);
        // for($j = 0; $row = RecuperaRegistro($rsg); $j++) {
          // $fl_clase_global = $row[0];
          // $ds_clase = $row[1];
          // $no_orden = $row[2];
          // $ds_titulo = $row[3];
          // $fl_clase_cg = $row[4];
          // $fe_clase = $row[5];
          // $fg_obligatorio = $row[6];
          // $info .= "<tr>
            // <td style=\"width:32%;\"><a href=\"".PATH_MODULOS."/campus/cglobales_frm.php?destino=1&clave=$fl_clase_global\" target=\"blank\">".strtoupper($ds_clase)."</a></td>
            // <td style=\"width:7%;\">$no_orden</td>
            // <td style=\"width:28%;\">$ds_titulo</td>
            // <td style=\"width:21%;\">$fe_clase</td>
            // <td>Global Class</td></tr>";
        // }
      // }
    // }
    // $info .= "</table>";
      
    return $info;
  }
  
  # Informacion de las clases globales traslapadas 
  function getInfoTraslapadasGlobales($arr_claves) {
    $claves = implode(",", $arr_claves);
    
    $Queryg  = "SELECT fl_clase_global, ds_clase, no_orden, ds_titulo, fl_clase_cg, fe_clase, fg_obligatorio FROM ( ";
    $Queryg .= "(SELECT kcg.fl_clase_global, cg.ds_clase, kcg.no_orden, kcg.ds_titulo, ";
    $Queryg .= "kcg.fl_clase_cg, ";
    $Queryg .= ConsultaFechaBD('kcg.fe_clase', FMT_DATETIME) . " fe_clase, kcg.fg_obligatorio ";
    $Queryg .= "FROM k_clase_cg kcg, c_clase_global cg ";
    $Queryg .= "WHERE kcg.fl_clase_global = cg.fl_clase_global AND kcg.fl_clase_cg IN($claves) ";
    $Queryg .= "ORDER BY cg.ds_clase, kcg.no_orden, kcg.fl_clase_cg) UNION ";
    $Queryg .= "(SELECT kcg.fl_clase_global, cg.ds_clase, kcg.no_orden, kcg.ds_titulo, ";
    $Queryg .= "kcg.fl_clase_cg, ";
    $Queryg .= ConsultaFechaBD('kcg.fe_clase', FMT_DATETIME) . " fe_clase, kcg.fg_obligatorio ";
    $Queryg .= "FROM k_clase_cg_temporal kcg ";
    $Queryg .= "left JOIN c_clase_global cg on kcg.fl_clase_global = cg.fl_clase_global WHERE kcg.fl_clase_cg IN($claves) ";
    $Queryg .= "ORDER BY cg.ds_clase, kcg.no_orden, kcg.fl_clase_cg) ";
    $Queryg .= ") as mainglobales ";
    $rsg = EjecutaQuery($Queryg);
    for($j = 0; $row = RecuperaRegistro($rsg); $j++) {
      $fl_clase_global = $row[0];
      $ds_clase = $row[1];
      $no_orden = $row[2];
      $ds_titulo = $row[3];
      $fl_clase_cg = $row[4];
      $fe_clase = $row[5];
      $fg_obligatorio = $row[6];

      if(empty($fl_clase_global))
          $ds_clase="Global Class";

      #Recupermos la licencia utilizada.
      $Query="SELECT a.zoom_id,b.host_email_zoom FROM k_live_sesion_cg a JOIN zoom b ON a.zoom_id=b.id WHERE a.fl_clase_cg=$row[4] ";
      $row=RecuperaValor($Query);
      $host_email_zoom=$row['host_email_zoom'];



      $info .= "<tr>
        <td style=\"width:32%;\"><a href=\"".PATH_MODULOS."/campus/cglobales_frm.php?destino=1&clave=$fl_clase_global\" target=\"blank\">".$ds_clase."</a><br><b><i>$host_email_zoom</i></b></td>
        <td style=\"width:7%;\">$no_orden</td>
        <td style=\"width:28%;\">$ds_titulo</td>
        <td style=\"width:21%;\">$fe_clase</td>
        <td>Global Class</td></tr>";
    }
      
    return $info;
  }

  
  function delLiveSessionIU($clMeetingId, LicenciaAdobe $licenciaAC) {
    
    $clLicencia = $licenciaAC->getClLicencia();
    
    $clienteAdobe = new AdobeConnectClient($licenciaAC);
    
    $clienteAdobe->deleteMeeting($clMeetingId);
    
  }
  
  function iuLiveSession($p_fl_clase, LicenciaAdobe $licenciaAC, $class_normales = false) {
    
    $clLicencia = $licenciaAC->getClLicencia();
    
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

    // Inserta o actualiza la sesion en adobe connect    
    $clienteAdobe = new AdobeConnectClient($licenciaAC);

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

    // MDB 25/OCT/2012
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
      $data = $clienteAdobe->createMeeting('', utf8_encode($titulo_leccion), $fecha_inicio, $fecha_fin, $id_leccion_url);        

    $meeting_id = $data["meeting_id"];
      
	  if ($data["estatus"] == "existe")
		$id_leccion_url = $data["url-path"];
	        
      // Se creo la clase en adobe connect
      if (!empty($meeting_id)) {
        // Permisos para que se puedan conectar a la clase
        $clienteAdobe->permisosMeeting($meeting_id);    
        $clienteAdobe->addHostMeeting($meeting_id,0);
        
        if(!$class_normales){
          $Query  = "INSERT INTO k_live_session ";
          $Query .= "(fl_clase, cl_estatus, ";
          $Query .= "ds_meeting_id, ds_password_admin, ds_password_asistente, ds_mensaje_bienvenida, cl_meeting_id, cl_licencia) ";
          $Query .= "VALUES ($p_fl_clase, 1, '$id_leccion_url', '$moderatorPW', '$attendeePW', '$welcome', '$meeting_id', $clLicencia) ";
        }
        else{
          $Query  = "INSERT INTO k_live_sesion_cg ";
          $Query .= "(fl_clase_cg,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente,ds_mensaje_bienvenida,cl_meeting_id,cl_licencia) ";
          $Query .= "VALUES ($p_fl_clase, 1, '$id_leccion_url', '$moderatorPW', '$attendeePW', '$welcome', '$meeting_id', $clLicencia) ";
        }
         // echo $Query;
        EjecutaQuery($Query);
      }
      else {
        // No se pudo crear, puede ser que ya exista        
      }
    }
    else {
      $clienteAdobe->updateMeeting($cl_meeting_id, utf8_encode($titulo_leccion), $fecha_inicio, $fecha_fin);
    }	  
    // Termina codigo Adobe connect 
  }
  
  
  $licenciaService = new LicenciaAdobeService();
  $clasesService = new ClasesService(); 
?>