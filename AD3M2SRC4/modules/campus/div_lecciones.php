<?php

  function logger($msg, $esVarDump=false) {
    $showLog = false;
    if(!$showLog)
      return false;
    
    if($esVarDump)
      var_dump($msg);
    else
      echo $msg . "<br>";
  }

  # Variable initialization
  $arrClavesTraslapadas=array();
  $clase=NULL;
  $fl_maestro_err=NULL;
  $fg_dia_sesion_err=NULL;
  $moderatorPW=NULL;
  $attendeePW=NULL;
  $welcome=NULL;
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../../modules/liveclass/bbb_api.php';
  
  require '../../lib/AdobeConnectClient.class.php';
  
  require '../../lib/adobeconnect/LicenciaAdobe.class.php';
  require '../../lib/adobeconnect/LicenciaAdobeService.class.php';
  require '../../lib/campusclases/ClasesService.class.php'; 
  require '../../lib/zoom_config.php';

  //include ('SessionadobeGG.php');
 

  # Variable initialization, this is to show in future lessons
  $future = "<small class='text-muted'><i>Future</i></small";
  
  # Se agrega un parametro para identificar si es clase global o normal
  function delLiveSessionCG($fl_clase) {
      
      $licenciaServiceAux = new LicenciaAdobeService();      
      
      $QuerySel="SELECT fl_live_session_grupal,cl_licencia, cl_meeting_id FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase ";
      $rop=RecuperaValor($QuerySel);
      $rowDel = RecuperaValor($QuerySel);

      $fl_live_session_actual = $rowDel[0];
      $cl_licencia_actual = $rowDel[1];
      $cl_meeting_id_actual = $rowDel[2];

      $LicenciaActual = $licenciaServiceAux->getLicenciaByClave($cl_licencia_actual);
      if (!empty($cl_meeting_id_actual)) {
          
          $clienteAdobe = new AdobeConnectClient($LicenciaActual);
          $clienteAdobe->deleteMeeting($cl_meeting_id_actual);
      }
         
      EjecutaQuery("DELETE FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase ");
  }

   function iuLiveSessionGG($p_fl_clase, LicenciaAdobe $licenciaAC, $class_normales = false) {
    
    $clLicencia = $licenciaAC->getClLicencia();
    
	  // MJD ADOBECONNECT Datos para los parametros Integracion Adobe Connect
	  // Se crean las sesiones de Adobe Connect desde este programa
	  // Para BBB no es necesario crearlas, se hace al momento en que un teacher 
	  // o un student entra a la sesion desde el campus.
      $Query  = "SELECT kcg.nb_clase, DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') fe_ini, ";
      $Query .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL 2 HOUR), '%Y-%m-%dT%H:%i:%s') fe_fin ";
      $Query .= "FROM k_clase_grupo kcg WHERE kcg.fl_clase_grupo=$p_fl_clase ";
   
    $row = RecuperaValor($Query);

    $titulo_leccion = $row[0];
    $fecha_inicio = $row[1];
    $fecha_fin = $row[2];
    $id_leccion_url = "VAN_".$clLicencia."_".rand(1000000, 9999999);   

    // Inserta o actualiza la sesion en adobe connect    
    $clienteAdobe = new AdobeConnectClient($licenciaAC);

    // Crea o actualiza la reunion en adobe connect
    $Query  = "SELECT fl_live_session_grupal, cl_meeting_id ";
    $Query .= "FROM k_live_session_grupal WHERE fl_clase_grupo = $p_fl_clase ";
    $row = RecuperaValor($Query);
    $fl_live_session = !empty($row[0])?$row[0]:NULL;
    $cl_meeting_id = !empty($row[1])?$row[1]:NULL;

    // MJD 16/OCT/2019
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
        
        $Query  = "INSERT INTO k_live_session_grupal ";
        $Query .= "(fl_clase_grupo,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente,ds_mensaje_bienvenida,cl_meeting_id,cl_licencia) ";
        $Query .= "VALUES ($p_fl_clase, 1, '$id_leccion_url', '$moderatorPW', '$attendeePW', '$welcome', '$meeting_id', $clLicencia) ";
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
  
  function delLiveSession($fl_clase) {
    
    $licenciaServiceAux = new LicenciaAdobeService();      
    
    // Borra la clase en adobe connect
    $QuerySel  = "select fl_live_session, cl_licencia, cl_meeting_id ";
    $QuerySel .= "from k_live_session where fl_clase = $fl_clase";   
    $rowDel = RecuperaValor($QuerySel);
    
    logger("Query select: $QuerySel");

    $fl_live_session_actual = $rowDel[0];
    $cl_licencia_actual = $rowDel[1];
    $cl_meeting_id_actual = $rowDel[2];

    logger("Borrando de adobe connect");
    $LicenciaActual = $licenciaServiceAux->getLicenciaByClave($cl_licencia_actual);
    if (!empty($cl_meeting_id_actual)) {
      //delLiveSession($cl_meeting_id_actual, $LicenciaActual);              
      //$clLicencia = $licenciaAC->getClLicencia();
      $clienteAdobe = new AdobeConnectClient($LicenciaActual);
      $clienteAdobe->deleteMeeting($cl_meeting_id_actual);
    }
    logger("Borrado FIN de adobe connect");
    
    $Query = "DELETE FROM k_live_session WHERE fl_clase = $fl_clase";
    EjecutaQuery($Query);
    
    logger("Query para borrar live session: $Query");

  }  

  function getInfoTraslapadas($arr_claves,$array_clavesClasesGrupales='',$arrClavesTraslapadasGlobalClass='') {
    
    $claves = implode(",", $arr_claves);    
    $claves_grupales=implode(",",$array_clavesClasesGrupales);
    $claves_global_class=implode(",",$arrClavesTraslapadasGlobalClass);

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
    
    //logger("Query info traslapadas: $Query <br>");
    
    $tit_grupo = ObtenEtiqueta(420);
    $tit_semana = ObtenEtiqueta(716);
    $tit_titulo = ObtenEtiqueta(385);
    $tit_fecha = ObtenEtiqueta(425);
    $tit_tipo = ObtenEtiqueta(44);
    
    $rs = EjecutaQuery($Query);
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
      $Query="SELECT a.zoom_id,b.host_email_zoom FROM k_live_session a JOIN zoom b ON a.zoom_id=b.id WHERE a.fl_clase=$row[5] ";
      $row=RecuperaValor($Query);
      $host_email_zoom=$row['host_email_zoom'];

      $info .= "<tr><td>$nb_grupo <br><b><i>$host_email_zoom</i></b></td><td>$no_semana</td><td>$titulo</td><td>$fecha</td><td>$adicional</td></tr>";
    }
    #Se obtiene clases grupales globales.
    if(!empty($claves_grupales)){
        
        $Query2="SELECT cg.fl_grupo,cg.nb_grupo,ks.no_semana,kc.nb_clase ,ks.fl_semana_grupo,
            kc.fl_clase_grupo, DATE_FORMAT(kc.fe_clase, '%d-%m-%Y %H:%i') fe_clase  
            FROM c_grupo cg 
            JOIN k_clase_grupo kc ON kc.fl_grupo=cg.fl_grupo
            JOIN k_semana_grupo ks ON ks.fl_semana_grupo=kc.fl_semana_grupo
            WHERE kc.fl_clase_grupo IN($claves_grupales)ORDER BY cg.nb_grupo,ks.no_semana,kc.fl_clase_grupo ";
        $rs2 = EjecutaQuery($Query2);
        for($x = 0; $row2 = RecuperaRegistro($rs2); $x++) {
            $nb_grupo = $row2[1];
            $no_semana = $row2[2];
            $titulo = $row2[3];
            $fecha = $row2[6];
        
            
            #Recupermos la licencia utilizada.
            $Query="SELECT a.zoom_id,b.host_email_zoom FROM k_live_session_grupal a JOIN zoom b ON a.zoom_id=b.id WHERE a.fl_clase_grupo=$row2[5] ";
            $row=RecuperaValor($Query);
            $host_email_zoom=$row['host_email_zoom'];

            $info .= "<tr><td>$nb_grupo<br><b><i>$host_email_zoom</i></b></td><td>$no_semana</td><td>$titulo</td><td>$fecha</td><td></td></tr>";
        }




    }


    if(!empty($claves_global_class)){
        
        $Query3="(  
                SELECT kc.fl_clase_global,a.ds_clase,no_orden,ds_titulo,''fl_semana,kc.fl_clase_cg, DATE_FORMAT(kc.fe_clase, '%d-%m-%Y %H:%i') fe_clase,''fg_adicional
		                FROM c_clase_global a 
                         JOIN k_clase_cg kc ON kc.fl_clase_global=a.fl_clase_global
		                WHERE kc.fl_clase_global IN(621) 
                )UNION(
                SELECT kc.fl_clase_global,a.ds_clase,no_orden,ds_titulo,''fl_semana,kc.fl_clase_cg, DATE_FORMAT(kc.fe_clase, '%d-%m-%Y %H:%i') fe_clase,''fg_adicional
		                FROM c_clase_global a 
                        JOIN k_clase_cg_temporal kc ON kc.fl_clase_global=a.fl_clase_global
		                WHERE kc.fl_clase_cg IN($claves_global_class)
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


            $info .= "<tr><td>$nb_grupo<br><b><i>$host_email_zoom</i></b></td><td>$no_semana</td><td>$titulo</td><td>$fecha</td><td>Global Class</td></tr>";


        }


    }





    
    $info .= "</table>";
      
    return $info;
  }

  $licenciaService = new LicenciaAdobeService();
  $clasesService = new ClasesService();     
  
  # Recibe parametros
  $accion = RecibeParametroHTML('accion');
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('variable');
  $fg_grupo_global=RecibeParametroNumerico('fg_grupo_global');


  if($fg_grupo_global==1){
      
      #Recibe Parametros
      $nb_clase=RecibeParametroHTML('ds_clase');
      $fe_start_date=RecibeParametroFecha('fe_start_date');
      $hr_sesion=RecibeParametroHTML('hr_sesion');
      $fg_obligatorio=RecibeParametroBinario('fg_mandatory');
      $fl_maestrog=RecibeParametroNumerico('fl_maestrog');
      $fg_dia_sesion=RecibeParametroHTML('fg_dia_sesion');
      $fl_clase_grupo=RecibeParametroNumerico('fl_clase_grupo');
      $fe_clase_d=ValidaFecha($fe_start_date)." ".$hr_sesion;

      switch($accion)
      {
          case 'inserta':

              #Recupermos la fecha de la clase anterior.
              $row1 = RecuperaValor("SELECT MAX(fl_clase_grupo) FROM k_clase_grupo WHERE fl_grupo=$clave ");
              $row0 = RecuperaValor("SELECT DATE_FORMAT(fe_clase,'%Y-%m-%d') FROM k_clase_grupo WHERE fl_clase_grupo=".$row1[0]."");
              $fe_clase_anterior = $row0[0];  

              if(!empty($fe_clase_anterior)){
                  $anio_pub = substr($fe_clase_anterior, 0, 4);
                  $mes_pub = substr($fe_clase_anterior, 5, 2);
                  $dia_pub = substr($fe_clase_anterior, 8, 2); 
                  

                  $fe_publicacion = $dia_pub."-".$mes_pub."-".$anio_pub;
                  $fe_publicacion = date_create( );
                  date_date_set($fe_publicacion, $anio_pub, $mes_pub, $dia_pub);
                  $dia_semana = date('N', date_format($fe_publicacion, 'U')); 

                  #se agregan 7 dias.
                  date_modify($fe_publicacion, "+ 7 day");
                  $fe_clase_d = date_format($fe_publicacion, 'Y-m-d'); // Se toma como valor por omision la fecha de publicacion + n dias     
                  $fe_clase_d = $fe_clase_d." ".$hr_sesion.":00";

              }else{
                  
             //     $fe_clase_d=$fe_clase_d." ".$hr_sesion.":00";

              }

              
              
              




              $Query="SELECT MAX(no_semana) FROM k_semana_grupo WHERE fl_grupo=$clave ";
              $ro=RecuperaValor($Query);
              $no_semana=$ro[0]+1;

              #Se inserta la semana. 
              $Query  = "INSERT INTO k_semana_grupo (fl_grupo, no_semana, fe_publicacion, fe_entrega, fe_calificacion) ";
              $Query .= "VALUES($clave, $no_semana, '$fe_clase_d', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
              $fl_semana=EjecutaInsert($Query);
			  
              #Se inserta la clase. 
              $Query  = "INSERT INTO k_clase_grupo (fl_grupo,fl_maestro, fl_semana_grupo,nb_clase,ds_dia_clase, fe_clase, fg_obligatorio, fg_adicional) ";
              $Query .= "VALUES($clave,$fl_maestrog, $fl_semana,'$nb_clase','$fg_dia_sesion', '$fe_clase_d', '$fg_obligatorio', '0')";
              $fl_clase_grupo=EjecutaInsert($Query);

			  #Verifica que no haya ninguna clase en esa fecha
			  // MDB
			  // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
			  // entonces puede agregar la clase, de lo contrario regresa el error a la forma.
			  $licenciaService = new LicenciaAdobeService();
			  $clasesService = new ClasesService();
					    
			  $fechaHora = "'" . ValidaFecha($fe_start_date) . ' ' . ValidaHoraMin($hr_sesion) . "'";
			  //falata aqui la validacion de clases translapadas etc. estab pensando validarda ntes de hacer el insert para que no haya problemas. y solo se genere el live sesion.
			  //exit;

              $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, 0);
              $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, 0);      
              $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);
              
              #Zoom
              $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, 0);
              $clavesLicenciasTraslapadasZoom = $clasesService->getClavesLicenciasTraslapadasZoom($fechaHora, 0);      
              $licenciasZoom = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom);
              $licenciasZoomDisponible = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom,True);
              
              if (!$licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {    
                  $fe_clase_err = ObtenMensaje(230)."<br/>";  
              }
              if (!$licenciaService->licenciasSuficientes($clasesTraslapadasZoom, sizeof($licenciasZoom))) {    
                  $fe_clase_err = ObtenMensaje(230)."<br/>";  
              }

			  /*AdobeConect 
			   if ($clasesTraslapadas > sizeof($licenciasAdobe)) {  
					$rsClasesTraslapadas = $clasesService->getClasesTraslapadas($fechaHora,0);
					
					$arrClavesTraslapadas = array();
					for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {
					  $arrClavesTraslapadas[$ix] = $rowx[0];
					}
											
				} 
               */

               if ($clasesTraslapadasZoom > sizeof($licenciasZoom)) {  
                   $rsClasesTraslapadas = $clasesService->getClasesTraslapadasZoom($fechaHora,0);
                   
                   $arrClavesTraslapadas = array();
                   for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {
                       $arrClavesTraslapadas[$ix] = $rowx[0];
                   }
                   
               } 


              			
                $existe = array_search($fl_clase_grupo, $arrClavesTraslapadas, false);
				
				if(empty($existe)){
					
					// Creacion sesion campus y AdobeConnect
					if (sizeof($licenciasAdobe)> 0) {
					  $licenciaAC = $licenciasAdobe[0]; // Usa una nueva licencia, toma la primera del arreglo
					  iuLiveSessionGG($fl_clase_grupo, $licenciaAC, True);  
					}

                    #Creacion de zoom class
                    if (sizeof($licenciasZoomDisponible)> 0) {
                        

                        #Recuperamos los datos de la clase que ya fue creada.
                        $Query="SELECT fl_live_session_grupal FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase_grupo ";
                        $row=RecuperaValor($Query);
                        $fl_live_session_grupal=$row[0];

                        if(empty($fl_live_session_grupal)){
                            $Query ="INSERT INTO k_live_session_grupal (fl_clase_grupo,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente,cl_licencia)";
                            $Query.="VALUES($fl_clase_grupo,1,'','','','1')";
                            $fl_live_session_grupal=EjecutaInsert($Query);
                        }



                        if(!empty($fl_live_session_grupal)){

                            #Damos formato a la clase para isertarla en zoom
                            $fe_clase_zoom=strtotime('+0 day',strtotime($fe_start_date));
                            $fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
                            $fe_clase_zoom=$fe_clase_zoom."T".$hr_sesion.":00";
                            $pass_clase_zoom=rand(99999,5)."i".$fl_live_session_grupal;

                            $licenciaAZ = $licenciasZoomDisponible[0]; // Usa una nueva licencia, toma la primera del arreglo
                            create_meetingZoom($fl_live_session_grupal,'60',$nb_clase,$fe_clase_zoom,$pass_clase_zoom,'k_live_session_grupal',$licenciaAZ);
                            

                        }





					}



				}

          break;
          case 'borra':
		       
              $Query="SELECT fl_semana_grupo FROM k_clase_grupo WHERE fl_clase_grupo=$fl_clase_grupo ";
              $rop=RecuperaValor($Query);
              $fl_semana_grupo=$rop['fl_semana_grupo'];

              #Rexuperamos el fl_live_sesion para poder elimnar en zoom api
              $Query="SELECT fl_live_session_grupal,zoom_id FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase_grupo ";
              $row=RecuperaValor($Query);
              $fl_dele_live_session_grupal=$row['fl_live_session_grupal'];
              $zoom_id=$row['zoom_id'];

              #Elimnamos de zoom api
              if((!empty($fl_dele_live_session_grupal))&&(!empty($zoom_id))){
                  DeletedMeetingZoom($fl_dele_live_session_grupal,'k_live_session_grupal',$zoom_id);
              }
              delLiveSessionCGZoom($fl_clase_grupo);

               delLiveSessionCG($fl_clase_grupo);			   
			   EjecutaQuery("DELETE FROM k_semana_grupo WHERE fl_semana_grupo=$fl_semana_grupo ");
			   
		       $Query="DELETE FROM k_clase_grupo WHERE fl_clase_grupo=$fl_clase_grupo  ";
			   EjecutaQuery($Query);

              break;
          case 'actualiza':
		  

              #Rexuperamos el fl_live_sesion para poder elimnar en zoom api
              $Query="SELECT fl_live_session_grupal,zoom_id FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase_grupo ";
              $row=RecuperaValor($Query);
              $fl_live_session_grupal=$row['fl_live_session_grupal'];
              $zoom_id=$row['zoom_id'];

              #Elimnamos de zoom api
              if((!empty($fl_live_session_grupal))&&(!empty($zoom_id))){
                  EjecutaQuery("UPDATE k_live_session_grupal set zoom_id=NULL , zoom_url=NULL WHERE fl_clase_grupo=$fl_clase_grupo ");
                  DeletedMeetingZoom($fl_live_session_grupal,'k_live_session_grupal',$zoom_id);
              }

			  #Verifica que no haya ninguna clase en esa fecha
			  // MDB
			  // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
			  // entonces puede agregar la clase, de lo contrario regresa el error a la forma.
			  $licenciaService = new LicenciaAdobeService();
			  $clasesService = new ClasesService();
					    
			  $fechaHora = "'" . ValidaFecha($fe_start_date) . ' ' . ValidaHoraMin($hr_sesion) . "'";
			  //falata aqui la validacion de clases translapadas etc. estab pensando validarda ntes de hacer el insert para que no haya problemas. y solo se genere el live sesion.
			  //exit;

              $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, 0);
              $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, 0);      
              $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);      
             /* 2020 se comenta esto era para adobe conect.
              if (!$licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {    
                  $fe_clase_err = ObtenMensaje(230)."<br/>";
				   
              }
			  
			  if ($clasesTraslapadas > sizeof($licenciasAdobe)) {  
					$rsClasesTraslapadas = $clasesService->getClasesTraslapadas($fechaHora,0);
					
					$arrClavesTraslapadas = array();
					for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {
					  $arrClavesTraslapadas[$ix] = $rowx[0];
					}
											
			  } 
              */
              #Para zoom.
              $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, 0);
              $clavesLicenciasTraslapadasZoom=$clasesService->getClavesLicenciasTraslapadasZoom($fechaHora,0);
              $licenciasZoom=$licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom);
              $licenciasZoomDisponibles=$licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom,True);

              if (!$licenciaService->licenciasSuficientesZoom($clasesTraslapadas, sizeof($licenciasAdobe))) {    
                  $fe_clase_err = ObtenMensaje(230)."<br/>";
                  
              }
              if ($clasesTraslapadasZoom > sizeof($licenciasZoom)) {  
                  $rsClasesTraslapadas = $clasesService->getClasesTraslapadasZoom($fechaHora,0);
                  
                  $arrClavesTraslapadas = array();
                  for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {
					  $arrClavesTraslapadas[$ix] = $rowx[0];
                  }
                  
			  }

              			
			  $existe = array_search($fl_clase_grupo,$arrClavesTraslapadas,false);
			 
			  $Query="SELECT fl_semana_grupo FROM k_clase_grupo WHERE fl_clase_grupo=$fl_clase_grupo ";
              $ro=RecuperaValor($Query);
              $fl_semana_grupo=$ro['fl_semana_grupo'];

              $Query="UPDATE  k_semana_grupo SET fe_publicacion='$fe_clase_d' WHERE fl_semana_grupo=$fl_semana_grupo ";
              EjecutaQuery($Query);

              
			  $Query  = "UPDATE k_clase_grupo SET fe_clase='$fe_clase_d',ds_dia_clase='$fg_dia_sesion',  fg_obligatorio='$fg_obligatorio',fl_maestro=$fl_maestrog, nb_clase='$nb_clase' ";
			  $Query .= "WHERE fl_clase_grupo = $fl_clase_grupo ";
			  EjecutaQuery($Query);

              #Rexuperamos el fl_live_sesion para poder elimnar en zoom api
              $Query="SELECT fl_live_session_grupal,zoom_id FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase_grupo ";
              $row=RecuperaValor($Query);
              $fl_live_session_grupal=$row['fl_live_session_grupal'];
              $zoom_id=$row['zoom_id'];

              #Elimnamos de zoom api
              if((!empty($fl_live_session_grupal))&&(!empty($zoom_id))){
                  EjecutaQuery("UPDATE k_live_session_grupal set zoom_id=NULL , zoom_url=NULL WHERE fl_clase_grupo=$fl_clase_grupo ");
                  DeletedMeetingZoom($fl_live_session_grupal,'k_live_session_grupal',$zoom_id);
              }




              $existe = array_search($fl_clase_grupo,$arrClavesTraslapadas,false);
              
              if(empty($existe)){
                  
                  // Creacion sesion campus y AdobeConnect actualiza.
                  if (sizeof($licenciasAdobe)> 0) {
					  $licenciaAC = $licenciasAdobe[0]; // Usa una nueva licencia, toma la primera del arreglo
				     iuLiveSessionGG($fl_clase_grupo, $licenciaAC, True);  
                  }

                  # Creacion sesion zoom.
                  if (sizeof($licenciasZoomDisponibles)> 0) {
					 
                      #Damos formato a la clase para isertarla en zoom
                      $fe_clase_zoom=strtotime('+0 day',strtotime($fe_start_date));
                      $fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
                      $fe_clase_zoom=$fe_clase_zoom."T".$hr_sesion.":00";
                      $pass_clase_zoom=rand(99999,5)."i".$fl_live_session_grupal;

                      $licenciaAZ = $licenciasZoomDisponibles[0]; // Usa una nueva licencia, toma la primera del arreglo
                      create_meetingZoom($fl_live_session_grupal,'60',$nb_clase,$fe_clase_zoom,$pass_clase_zoom,'k_live_session_grupal',$licenciaAZ);
                  }




              }

              break;
      }

						$name = ObtenNombre(isset($fl_usuario)?$fl_usuario:NULL); 
                        $SALT = ObtenConfiguracion(33);
                        $URL = ObtenConfiguracion(32);
                        $tit = array(ObtenEtiqueta(390).'|center', ObtenEtiqueta(385),ObtenEtiqueta(1002).'|center',ObtenEtiqueta(427), '* '.ObtenEtiqueta(425), 'Attendance',ObtenEtiqueta(428).'|center', '&nbsp;');
                        $ancho_col = array('5%', '20%','15%','15%', '30%', '7%', '5%');
                        Forma_Tabla_Ini('90%', $tit, $ancho_col);
                        $adicionales = 0;
						
						#Recupera las clases.
						$Query="SELECT no_semana,b.nb_clase,b.fl_maestro, ".ConsultaFechaBD('b.fe_clase', FMT_CAPTURA)." fe_clase,".ConsultaFechaBD('b.fe_clase', FMT_HORAMIN)." hr_clase,b.fg_obligatorio,a.fl_semana_grupo,b.fl_clase_grupo,b.ds_dia_clase  
												  FROM k_semana_grupo a 
												  JOIN k_clase_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo 
                                WHERE a.fl_grupo=$clave 
                                ORDER BY no_semana ASC 
												  
												  ";
						$rs3 = EjecutaQuery($Query);
					    $contador_reg=0;
						for($im=0;$row3=RecuperaRegistro($rs3);$im++){
							       
					        $contador_reg++;
                  $fl_grupo=$clave;
                  $fl_semana_grupo=$row3['fl_semana_grupo'];
                  $fl_clase_grupo=$row3['fl_clase_grupo'];
									$no_semana=$row3['no_semana'];
									$nb_clase=$row3['nb_clase'];
									$fl_maestro=$row3['fl_maestro'];
									$fe_clase=$row3['fe_clase'];
                                    $hr_clase=$row3['hr_clase'];
									$fg_mandatory=$row3['fg_obligatorio'];
									$fg_dia_sesion=$row3['ds_dia_clase'];

                  # Revisa si hay una clase global activa en este momento
                  $Query  = "SELECT fl_live_session_grupal, cl_estatus, ds_meeting_id, ds_password_asistente,zoom_url,zoom_id ";
                  $Query .= "FROM k_live_session_grupal ";
                  $Query .= "WHERE fl_clase_grupo=".$fl_clase_grupo;
                  $row = RecuperaValor($Query);
                  $fl_live_session = $row[0];
                  $cl_estatus = $row[1];
                  $ds_meeting_id = $row[2];
                  $ds_password_asistente = $row[3];    
                  $zoom_url=$row[4];
                  $zoom_id=$row[5];

                  #Recuperamos el host del email.
                  #Recuperamos la cuenta
                  $Query="SELECT host_email_zoom FROM zoom WHERE id=$zoom_id ";
                  $row=RecuperaValor($Query);
                  $ds_host_zoom=$row[0];
                  $fe_clase_err="";
                  if(!empty($fl_live_session) AND $cl_estatus == '1') {      
                      // MDB ADOBECONNECT 
                      $urlAdobeConnect = ObtenConfiguracion(53);
                      $joinURL = $urlAdobeConnect . $ds_meeting_id . "/?guestName=Admin";
                      $ds_liga = "<a href='$joinURL' title='Join Live Classroom' target='_blank'><i class='fa fa-external-link'></i></a>";
                  } else {
                      $ds_liga = "<i class='fa fa-external-link'></i>";
                  }

                  #Verifica que no haya ninguna clase en esa fecha
                  // MDB
                  // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
                  // entonces puede agregar la clase, de lo contrario regresa el error a la forma.
                  $licenciaService = new LicenciaAdobeService();
                  $clasesService = new ClasesService();

                  $fechaHora = "'" . ValidaFecha($fe_clase) . ' ' . ValidaHoraMin($hr_clase) . "'";
                  //falata aqui la validacion de clases translapadas etc. estab pensando validarda ntes de hacer el insert para que no haya problemas. y solo se genere el live sesion.
                  //exit;
                  $clave_clase = $fl_clase_grupo;                            
                  logger("Folio clase: $clave_clase");
                  logger("FechaHora: $fechaHora <br>");

                  $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, 0);
                  $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, 0);      
                  $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);      
                  
                  if (!$licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {    
                      $fe_clase_err = ObtenMensaje(230)."<br/>";
                      
                  }

                  #Para Zoom.
                  $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, 0);
                  $clavesLicenciasTraslapadasZoom=$clasesService->getClavesLicenciasTraslapadasZoom($fechaHora,0);
                  $licenciasZoom=$licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom);
                  //$licenciasZoomDisponibles=getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom,True);
                 
                  if (!$licenciaService->licenciasSuficientesZoom($clasesTraslapadasZoom, sizeof($licenciasZoom))) {    
                      $fe_clase_err = ObtenMensaje(230)."<br/>";
                      
                  }




                  logger("Clases traslapadas: $clasesTraslapadas <br>"); 
								    logger("Licencias disponibles: " . sizeof($licenciasAdobe) . "<br>");
                                    /*2020 se comenta ara adobe connect.
									if ($clasesTraslapadas > sizeof($licenciasAdobe)) {  
										$rsClasesTraslapadas = $clasesService->getClasesTraslapadas($fechaHora,0);
										
										$arrClavesTraslapadas = array();
										for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {
										  $arrClavesTraslapadas[$ix] = $rowx[0];
										}
										$infoTraslapadas = getInfoTraslapadas($arrClavesTraslapadas);
										
										//$fe_clase_err = ObtenMensaje(230);// . " Clases traslapadas: $clasesTraslapadas Licencias disponibles: " . sizeof($licenciasAdobe);
										$fe_clase_err = "<a href='javascript:void(0);' class='traslapadas' style='float:left;' data-trigger='focus' rel='popover-hover' data-placement='top' data-html='true' ";
										$fe_clase_err .= "title='<a href=\"#\" class=\"close\" data-dismiss=\"alert\">x</a> " . $infoTraslapadas . "' data-content=''><span style='color:red;'>" . ObtenMensaje(230) . "</span></a>";
									
										
									}  
                                    */

                                    #Para zoom.
                                    if ($clasesTraslapadasZoom > sizeof($licenciasZoom)) {  
										$rsClasesTraslapadas = $clasesService->getClasesTraslapadasZoom($fechaHora,0);
										
										$arrClavesTraslapadas = array();
                                        $arrClavesTraslapadasNormales=array();
                                        $arrClavesTraslapadasGlobalClass=array();
										for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {

                                            if($rowx[6]==1){#Grupales Globales
                                                $arrClavesTraslapadas[$ix] = $rowx[0];
                                            }
                                            if(($rowx[6]==0)&&($rowx[7]==0)){
                                                #Clases normales
                                                $arrClavesTraslapadasNormales[$ix] = $rowx[0];
                                            }
                                            #Clases_globales
                                            if($rowx[7]==1){
                                                $arrClavesTraslapadasGlobalClass[$ix] = $rowx[0];
                                            }
										}
										$infoTraslapadas = getInfoTraslapadasGlobalesGrupales($arrClavesTraslapadas,$arrClavesTraslapadasNormales,$arrClavesTraslapadasGlobalClass);
										//$infoTraslapadas .="<br><span>Available licenses: ";
                                        //$infoTraslapadas .=</span>";
										//$fe_clase_err = ObtenMensaje(230);// . " Clases traslapadas: $clasesTraslapadas Licencias disponibles: " . sizeof($licenciasAdobe);
										$fe_clase_err = "<a href='javascript:void(0);' class='traslapadas' style='float:left;' data-trigger='focus' rel='popover-hover' data-placement='top' data-html='true' ";
										$fe_clase_err .= "title='<a href=\"#\" class=\"close\" data-dismiss=\"alert\">x</a> " . $infoTraslapadas . "' data-content=''><span style='color:red;'>" . ObtenMensaje(230) . "</span></a>";
                                        
									    	
									} 

                                    $existe = array_search($fl_clase_grupo,$arrClavesTraslapadas);


									if(!empty($fe_clase_err) && !empty($existe)) {
											$ds_clase = 'css_input_error';
											$ds_error = 'state-error';
											$msg_error = "<br/>" . $fe_clase_err;
									}else{
										    $ds_clase = 'form-control';
											$ds_error = '';
                                            $msg_error='';
										
										
									}




      $Qclase_grupal = RecuperaValor("SELECT fl_live_session_grupal, zoom_id FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase_grupo ");

      $fl_live_session_grupal = $Qclase_grupal['fl_live_session_grupal'];

      $Qassist_grupal = RecuperaValor("SELECT cl_estatus_asistencia_gg FROM k_live_session_asistencia_gg WHERE fl_live_session_gg=$fl_live_session_grupal AND fl_usuario=$fl_maestro");

      $statusAsistencia_gg = !empty($Qassist_grupal[0])?$Qassist_grupal[0]:NULL;

      switch ($statusAsistencia_gg) {
        case '1':
          $attendance_gg="<small class='text-danger'>
                      <i>
                      <select id='".$fl_live_session_grupal."' name='".$im."' onchange='change_attendance_gg(this.id, $fl_maestro, this.value, this.name);'>
                      <option value='1' selected>Absent</option>
                      <option value='2'>Present</option>
                      <option value='3'>Late</option>
                      </select>
                      </i>
                      </small>";
          break;
        case '2':
          $attendance_gg="<small class='text-success'>
                      <i>
                      <select id='".$fl_live_session_grupal."' name='".$im."' onchange='change_attendance_gg(this.id, $fl_maestro, this.value, this.name);'>
                      <option value='1'>Absent</option>
                      <option value='2' selected>Present</option>
                      <option value='3'>Late</option>
                      </select>
                      </i>
                      </small>";
          break;
        case '3':
          $attendance_gg="<small class='text-warning'>
                      <i>
                      <select id='".$fl_live_session_grupal."' name='".$im."' onchange='change_attendance_gg(this.id, $fl_maestro, this.value, this.name);'>
                      <option value='1'>Absent</option>
                      <option value='2'>Present</option>
                      <option value='3' selected>Late</option>
                      </select>
                      </i>
                      </small>";
          break;
        default:
          $attendance_gg="<small class='text-danger'>
                      <i>
                      <select id='".$fl_live_session_grupal."' name='".$im."' onchange='change_attendance_gg(this.id, $fl_maestro, this.value, this.name);'>
                      <option value='1' selected>Absent</option>
                      <option value='2'>Present</option>
                      <option value='3'>Late</option>
                      </select>
                      </i>
                      </small>";
          break;
      }



						echo "
								<tr class='$clase' id='reg_lecciones_$im'>
									<td align='center'>$no_semana</td>
									<td align='left'>";
							
						echo"			<div class='col-md-12 form-group smart-form'>
										<label class='input'>
											<span class='icon-append'>".$ds_liga." </span>";
											CampoTexto('ds_clase_'.$im, $nb_clase, 100, 0,'', False, "onchange='ActualizaCG(".$im.",".$fl_grupo.",".$fl_clase_grupo.");' ");
						echo "			</label>
										</div>";
						echo"		    $msg_error</td>
									<td>";
										  $Query  = "SELECT CONCAT(usr.ds_nombres,' ',usr.ds_apaterno), ma.fl_maestro, ma.ds_ruta_avatar ";
										  $Query .= "FROM c_maestro ma LEFT JOIN c_usuario usr ON(usr.fl_usuario=ma.fl_maestro) ";
										  $Query .= "WHERE usr.fg_activo='1' ";             
										  Forma_CampoSelectBD('', False, 'fl_maestro_'.$im, $Query, $fl_maestro, $fl_maestro_err, True, 'onchange=\'ActualizaCG('.$im.','.$fl_grupo.','.$fl_clase_grupo.');\'', 'left hidden', '', '');									
						echo"		</td>";
            echo"       <td>";
                              $opc = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                              $val = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                              $icono = '&nbsp;<a href="javascript:void(0);" rel="popover-hover" data-placement="top" data-original-title="'.ObtenEtiqueta(20).'" data-content="'.ObtenMensaje(231).'"><i class="fa fa-info-circle"></i></a>';
                              Forma_CampoSelect(ObtenEtiqueta(1010).$icono, False, 'fg_dia_sesion_'.$im, $opc, $val, $fg_dia_sesion, $fg_dia_sesion_err, False, "onchange='ActualizaCG(".$im.",".$fl_grupo.",".$fl_clase_grupo.");'", 'left hidden', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12');

            echo"       </td>";
            echo"
									<td align='left'> ";
						echo "			<div class='row form-group smart-form'><div class='col col-sm-9'><label class='input col col-sm-8 no-padding $ds_error'>";
										  CampoTexto('fe_clase_'.$im, $fe_clase, 10, 10, $ds_clase, False, "readonly onchange='ActualizaCG(".$im.",".$fl_grupo.",".$fl_clase_grupo.")'");
										  Forma_Calendario('fe_clase_'.$im);
                        echo "			</label><div class='col col-sm-4 no-padding'><label class='input $ds_error'>";
                                                       
										CampoTexto('hr_clase_'.$im, $hr_clase, 5, 3, $ds_clase, False, "onchange='ActualizaCG(".$im.",".$fl_grupo.",".$fl_clase_grupo.")'");
                        echo "			</label></div></div>";

                        
                        if(!empty($zoom_url)){
                            echo"       <a href='$zoom_url' target='_blank'>zoom <i class='fa fa-external-link' aria-hidden='true'></i> </a><br><small class='text-muted' style='float:right'>$ds_host_zoom</small>";
                        }

                        echo"
									</td>
                  <td>".(strtotime($fe_clase.' '.$hr_clase)<=strtotime(date('Y-m-d H:i'))?$attendance_gg:$future)."</td>
									<td align='center'>";
									 Forma_CampoCheckbox(ObtenEtiqueta(1006), 'fg_mandatory_'.$im, $fg_mandatory, '', '', True, "onchange='ActualizaCG(".$im.",".$fl_grupo.",".$fl_clase_grupo.")'", 'left hidden', '', '');
									
						echo"		</td>
									<td>";
									
									 echo"<a href=\"javascript:void(0);\" onclick=\"BorrarCG(".$im.",".$fl_grupo.",".$fl_clase_grupo.");\"  title=\"Delete\"><i class=\"fa fa-trash-o fa-2x\"></i></a>  ";	
									
						echo"		</td>";
						echo"	</tr>";

						}

  } else {

  if ($fg_error)  
    logger("Regresa con error: $fg_error");


  switch($accion)
  {
    case 'inserta':
      $fl_sem = RecibeParametroNumerico('fl_sem');  
    $fe_clase_d = date("Y-m-d"); 

    $Query="SELECT  date_format(fe_clase,'%H:%i') fe_clase FROM k_clase WHERE fl_semana=$fl_sem ORDER BY fl_clase DESC ";
      $row=RecuperaValor($Query);
      $hrs_clase=$row[0];

      $fe_clase_d=$fe_clase_d." ".$hrs_clase;

      $Query  = "INSERT INTO k_clase (fl_grupo, fl_semana, fe_clase, fg_obligatorio, fg_adicional) ";
      $Query .= "VALUES($clave, $fl_sem, '$fe_clase_d', '1', '1')";
      EjecutaQuery($Query);
    break;
    case 'borra':
      $fl_clas = RecibeParametroNumerico('fl_clas'); 

    // Borra la clase en adobe connect
    /*$QueryDel  = "select fl_live_session, cl_licencia, cl_meeting_id ";
    $QueryDel .= "from k_live_session where fl_clase = $fl_clas";   
    $rowDel = RecuperaValor($QueryDel);

    $fl_live_session_actual = $rowDel[0];
    $cl_licencia_actual = $rowDel[1];
    $cl_meeting_id_actual = $rowDel[2];

    $LicenciaActual = $licenciaService->getLicenciaByClave($cl_licencia_actual);
    if (!empty($cl_meeting_id_actual))
    delLiveSession($cl_meeting_id_actual, $LicenciaActual);      	  

      $Query = "DELETE FROM k_live_session WHERE fl_clase = $fl_clas";
      EjecutaQuery($Query);	  */

     #Recuperamos el live sesion relacionada con la clase.
      $Query="SELECT fl_live_session,zoom_id FROM k_live_session where fl_clase = $fl_clas ";
      $row = RecuperaValor($QueryDel);
      $fl_live_session = $row[0];
      $zoom_id=$row[1];

      #Eliminamos el meting de zoom
      if((!empty($fl_live_session)) &&(!empty($zoom_id))){

          DeletedMeetingZoom($fl_live_session,'k_live_session',$zoom_id);
      }
      delLiveSession($fl_clas);

      $Query = "DELETE FROM k_clase WHERE fl_clase = $fl_clas";
      EjecutaQuery($Query);
    break;
    case 'actualiza':
      $fl_clas = RecibeParametroNumerico('fl_clas'); 
      $fe_clas = RecibeParametroFecha('fe_clas');
      $hr_clas = RecibeParametroHoraMin('hr_clas');
      $fg_obliga = RecibeParametroHTML('fg_obliga');
      $fe_class = "'".ValidaFecha($fe_clas)." ".$hr_clas."'";


      #Eliminamos la clase creada en Zoom.
      $QuerySel  = "SELECT fl_live_session,zoom_id  ";
      $QuerySel .= "from k_live_session where fl_clase = $fl_clas ";
      $row=RecuperaValor($QuerySel);
      $fl_live_session=$row[0];
      $zoom_id=$row[1];

      #Elimnamos de zoom api
      if( (!empty($fl_live_session)) && (!empty($zoom_id)) ){
          DeletedMeetingZoom($fl_live_session,'k_live_session',$zoom_id);
      }



      #Adobeconnect
      delLiveSession($fl_clas);

      $Query  = "UPDATE k_clase SET fe_clase=$fe_class,  fg_obligatorio='$fg_obliga' ";
      $Query .= "WHERE fl_clase = $fl_clas";
      EjecutaQuery($Query);
    break;
  }

  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) // Actualizacion, recupera de la base de datos
    { 
      $Query  = "SELECT a.fl_term ";
      $Query .= "FROM c_grupo a, c_usuario b, k_term c ";
      $Query .= "WHERE a.fl_maestro=b.fl_usuario ";
      $Query .= "AND a.fl_term=c.fl_term ";
      $Query .= "AND fl_grupo=$clave";
      $row = RecuperaValor($Query);
      $fl_term = $row[0];

      # Recupera las fechas de cada clase
      $Query  = "SELECT fl_semana, no_semana, ds_titulo, fe_publicacion ";
      $Query .= "FROM k_semana a, c_leccion b ";
      $Query .= "WHERE a.fl_leccion=b.fl_leccion ";
      $Query .= "AND fl_term=$fl_term ";
      $Query .= "ORDER BY no_semana";
      $rs = EjecutaQuery($Query);
      for($tot_semanas = 0; $row = RecuperaRegistro($rs); $tot_semanas++) 
      {
        $fl_semana[$tot_semanas] = $row[0];
        $no_semana[$tot_semanas] = $row[1];
        $ds_titulo[$tot_semanas] = str_texto($row[2]);
        $anio_pub = substr($row[3], 0, 4);
        $mes_pub = substr($row[3], 5, 2);
        $dia_pub = substr($row[3], 8, 2);
        $fe_publicacion = date_create( );
        $dif_dias = ObtenConfiguracion(25);
        date_date_set($fe_publicacion, $anio_pub, $mes_pub, $dia_pub);
        date_modify($fe_publicacion, "+$dif_dias day");
        $fe_clase[$tot_semanas] = date_format($fe_publicacion, 'd-m-Y'); // Se toma como valor por omision la fecha de publicacion + n dias
        $hr_clase[$tot_semanas] = ObtenConfiguracion(26);
        $Query  = "SELECT fl_clase, ".ConsultaFechaBD('fe_clase', FMT_CAPTURA)." fe_clase, ";
        $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN)." hr_clase, fg_obligatorio, fg_adicional ";
        $Query .= "FROM k_clase ";
        $Query .= "WHERE fl_grupo=$clave ";
        $Query .= "AND fl_semana=$fl_semana[$tot_semanas] ";
        $Query .= "ORDER BY fl_clase";
        $cons = EjecutaQuery($Query);
        $conta = 0;
        while($row2 = RecuperaRegistro($cons))
        {
          if($conta > 0)
            $tot_semanas++;
          $fl_clase[$tot_semanas] = $row2[0];
          if(!empty($row2[1])) # Ya se habia puesto una fecha para la clase
          { 
            $fe_clase[$tot_semanas] = $row2[1];
            $hr_clase[$tot_semanas] = $row2[2];
          }
          $fg_obligatorio[$tot_semanas] = $row2[3];
          $fg_adicional[$tot_semanas] = $row2[4];
          $conta++;
        }
      }
    }
    else 
      $tot_semanas = 0;
  }
  else 
  { 
    $Query  = "SELECT a.fl_term ";
    $Query .= "FROM c_grupo a, c_usuario b, k_term c ";
    $Query .= "WHERE a.fl_maestro=b.fl_usuario ";
    $Query .= "AND a.fl_term=c.fl_term ";
    $Query .= "AND fl_grupo=$clave";
    $row = RecuperaValor($Query);
    $fl_term = $row[0];

    # Recupera las fechas de cada clase
    $Query  = "SELECT fl_semana, no_semana, ds_titulo, fe_publicacion ";
    $Query .= "FROM k_semana a, c_leccion b ";
    $Query .= "WHERE a.fl_leccion=b.fl_leccion ";
    $Query .= "AND fl_term=$fl_term ";
    $Query .= "ORDER BY no_semana";
    $rs = EjecutaQuery($Query);
    for($tot_semanas = 0; $row = RecuperaRegistro($rs); $tot_semanas++) 
    {
      $fl_semana[$tot_semanas] = $row[0];
      $no_semana[$tot_semanas] = $row[1];
      $ds_titulo[$tot_semanas] = str_texto($row[2]);
      $anio_pub = substr($row[3], 0, 4);
      $mes_pub = substr($row[3], 5, 2);
      $dia_pub = substr($row[3], 8, 2);
      $fe_publicacion = date_create( );
      $dif_dias = ObtenConfiguracion(25);
      date_date_set($fe_publicacion, $anio_pub, $mes_pub, $dia_pub);
      date_modify($fe_publicacion, "+$dif_dias day");
      $fe_clase[$tot_semanas] = date_format($fe_publicacion, 'd-m-Y'); // Se toma como valor por omision la fecha de publicacion + n dias
      $hr_clase[$tot_semanas] = ObtenConfiguracion(26);
      $Query  = "SELECT fl_clase, ".ConsultaFechaBD('fe_clase', FMT_CAPTURA)." fe_clase, ";
      $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN)." hr_clase, fg_obligatorio, fg_adicional ";
      $Query .= "FROM k_clase ";
      $Query .= "WHERE fl_grupo=$clave ";
      $Query .= "AND fl_semana=$fl_semana[$tot_semanas] ";
      $Query .= "ORDER BY fl_clase";
      $cons = EjecutaQuery($Query);
      $conta = 0;
      while($row2 = RecuperaRegistro($cons))
      {
        if($conta > 0)
          $tot_semanas++;
        $fl_clase[$tot_semanas] = $row2[0];
        if(!empty($row2[1])) # Ya se habia puesto una fecha para la clase
        { 
          $fe_clase[$tot_semanas] = $row2[1];
          $hr_clase[$tot_semanas] = $row2[2];
        }
        $fg_obligatorio[$tot_semanas] = $row2[3];
        $fg_adicional[$tot_semanas] = $row2[4];
        $conta++;
      }
    }
  }
    $fg_error = False;
    $name = ObtenNombre(isset($fl_usuario)?$fl_usuario:NULL); 
    $SALT = ObtenConfiguracion(33);
    $URL = ObtenConfiguracion(32);
    $tit = array(ObtenEtiqueta(390).'|center', ObtenEtiqueta(385), '* '.ObtenEtiqueta(425), 'Attendance', ObtenEtiqueta(428).'|center', '&nbsp;', '&nbsp;');
    $ancho_col = array('10%', '40%', '35%', '10%', '5%');
    Forma_Tabla_Ini('90%', $tit, $ancho_col);
    $adicionales = 0;

    for($i = 0; $i < $tot_semanas; $i++){
        if($fg_adicional[$i] == '1') {
            $adicionales++;
            // MDB Para generar el titilo de la clase adicional
            $tit_clase_adicional = "Extraclass " . $adicionales;
        }
        if($adicionales % 2 == 0)
        {
            if($i % 2 == 0)
                $clase = "css_tabla_detalle";
            else
                $clase = "css_tabla_detalle_bg";
        }
        else
        {
            if($fg_adicional[$i] == '0')
            {
                if($i % 2 != 0)
                    $clase = "css_tabla_detalle";
                else
                    $clase = "css_tabla_detalle_bg";
            }
            else
                $clase = $clase_anterior;
        }

        # Revisa si hay una clase activa en este momento
        $Query  = "SELECT fl_live_session, cl_estatus, ds_meeting_id, ds_password_asistente,zoom_url,zoom_id ";
        $Query .= "FROM k_live_session ";
        $Query .= "WHERE fl_clase=".$fl_clase[$i];
        
        $row = RecuperaValor($Query);
        $fl_live_session = $row[0];
        $cl_estatus = $row[1];
        $ds_meeting_id = $row[2];
        $ds_password_asistente = $row[3];
        $zoom_url=$row[4];
        $zoom_id=$row[5];

        #Recuperamos la cuenta
        $Query="SELECT host_email_zoom FROM zoom WHERE id=$zoom_id ";
        $row=RecuperaValor($Query);
        $ds_host_zoom=$row[0];

        if(!empty($fl_live_session) AND $cl_estatus == '1') {
            /* MDB - No borrar este codigo, se usara posteriormente cuando se regrese a BBB
            $bbbObj = new BigBlueButton( );
            $joinURL = $bbbObj->joinURL($ds_meeting_id, $name, $ds_password_asistente, $SALT, $URL);
            $ds_liga = "<a href='$joinURL' title='Join Live Classroom' target='_blank'>".$ds_titulo[$i]."</a>";
             */
            // MDB ADOBECONNECT 
            $urlAdobeConnect = ObtenConfiguracion(53);
            $joinURL = $urlAdobeConnect . $ds_meeting_id . "/?guestName=Admin";
            $ds_liga = "<a href='$joinURL' title='Join Live Classroom' target='_blank'>".$ds_titulo[$i]."</a>";
        }
        else
            $ds_liga = $ds_titulo[$i];
        
        // MDB Liga de adobe connect para las adicionales
        // No usamos el titulo de la leccion, solo un texto de Join
        if($fg_adicional[$i] == '1') {
            // MDB ADOBECONNECT 
            $urlAdobeConnect = ObtenConfiguracion(53);
            $joinURL = $urlAdobeConnect . $ds_meeting_id . "/?guestName=Admin";
            $ds_liga = "<a href='$joinURL' title='Join Live Classroom' target='_blank'>" . $tit_clase_adicional . "</a>";                   
        }
        
        // MDB
        // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
        // entonces puede agregar la clase, de lo contrario regresa el error a la forma.     
        
        $licenciaService = new LicenciaAdobeService();
        $clasesService = new ClasesService();      
        
        $fechaHora = "'" . ValidaFecha($fe_clase[$i]) . ' ' . ValidaHoraMin($hr_clase[$i]) . "'";
        
        $clave_clase = $fl_clase[$i];
        
        logger("Folio clase: $clave_clase");
        logger("FechaHora: $fechaHora <br>");

        $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, $clave_clase);
        $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, $clave_clase);      
        $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);  

        #Zoom.
        $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, $clave_clase);
        $clavesLicenciasTraslapadasZoom = $clasesService->getClavesLicenciasTraslapadasZoom($fechaHora, $clave_clase);      
        $licenciasZoom = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom);
        $licenciasZoomDisponibles=$licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom,True);
        logger("Clases traslapadas: $clasesTraslapadas <br>"); 
        logger("Licencias disponibles: " . sizeof($licenciasAdobe) . "<br>");
        
        $fe_clase_err = "";      
        //if (!$licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {
       /* 2020 es la que validaba con adobe connect */

        /*
        if ($clasesTraslapadas > sizeof($licenciasAdobe)) {  
            $rsClasesTraslapadas = $clasesService->getClasesTraslapadas($fechaHora, $clave_clase);
            
            $arrClavesTraslapadas = array();
            for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {
                $arrClavesTraslapadas[$ix] = $rowx[0];
            }
            $infoTraslapadas = getInfoTraslapadas($arrClavesTraslapadas);
            
            //$fe_clase_err = ObtenMensaje(230);// . " Clases traslapadas: $clasesTraslapadas Licencias disponibles: " . sizeof($licenciasAdobe);
            $fe_clase_err = "<a href='javascript:void(0);' class='traslapadas' data-trigger='focus' rel='popover-hover' data-placement='top' data-html='true' ";
            $fe_clase_err .= "title='<a href=\"#\" class=\"close\" data-dismiss=\"alert\">x</a> " . $infoTraslapadas . "' data-content=''><span style='color:red;'>" . ObtenMensaje(230) . "</span></a>";
        }    
       */

        #Validaciones con zoom.
        if ($clasesTraslapadasZoom > sizeof($licenciasZoom)) {  
            $rsClasesTraslapadas = $clasesService->getClasesTraslapadasZoom($fechaHora, $clave_clase);
            
            $arrClavesTraslapadas = array();
            $arrClavesTraslapadasGrupales=array();
            $arrClavesTraslapadasGlobalClass=array();
            for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {

                if($rowx[6]==1){#Grupales globales
                    $arrClavesTraslapadasGrupales[$ix] = $rowx[0];
                }
                if(($rowx[6]==0)&&($rowx[7]==0)){ #Clases normales
                    $arrClavesTraslapadas[$ix] = $rowx[0];
                }
                #Clases_globales
                if($rowx[7]==1){
                    $arrClavesTraslapadasGlobalClass[$ix] = $rowx[0];
                }



            }
            $infoTraslapadas = getInfoTraslapadas($arrClavesTraslapadas,$arrClavesTraslapadasGrupales,$arrClavesTraslapadasGlobalClass);
            
            //$fe_clase_err = ObtenMensaje(230);// . " Clases traslapadas: $clasesTraslapadas Licencias disponibles: " . sizeof($licenciasAdobe);
            $fe_clase_err = "<a href='javascript:void(0);' class='traslapadas' data-trigger='focus' rel='popover-hover' data-placement='top' data-html='true' ";
            $fe_clase_err .= "title='<a href=\"#\" class=\"close\" data-dismiss=\"alert\">x</a> " . $infoTraslapadas . "' data-content=''><span style='color:red;'>" . ObtenMensaje(230) . "</span></a>";
        }    



        $msg_error = "";
        if($fe_clase_err) {
            $ds_clase = 'css_input_error';
            $ds_error = 'state-error';
            $msg_error = "<br/>" . $fe_clase_err;
        }
        else{
            $ds_clase = 'form-control';
            $ds_error = '';
        }

        $fl_maestro = RecuperaValor("SELECT fl_maestro FROM k_clase A JOIN c_grupo B ON(A.fl_grupo=B.fl_grupo) WHERE fl_clase=$fl_clase[$i]");

        # Here the attendance for the live sessions
        $Qassist = RecuperaValor("SELECT cl_estatus_asistencia FROM k_live_session_asistencia WHERE fl_live_session=$fl_live_session AND fl_usuario=$fl_maestro[0]");

        $statusAsistencia = !empty($Qassist[0])?$Qassist[0]:NULL;

        switch ($statusAsistencia) {
          case '1':
            $attendance="<small id='lecture_".$fl_live_session."' class='text-danger'>
                        <i>
                        <select  id='".$fl_live_session."' name='subtract_class".$i."' onchange='change_attendance_lecture(this.id, $fl_maestro[0], this.value, this.name);'>
                        <option value='1' selected>Absent</option>
                        <option value='2'>Present</option>
                        <option value='3'>Late</option>
                        </select>
                        </i>
                        </small>";
            break;
          case '2':
            $attendance="<small id='lecture_".$fl_live_session."' class='text-success'>
                        <i>
                        <select id='".$fl_live_session."' name='subtract_class".$i."' onchange='change_attendance_lecture(this.id, $fl_maestro[0], this.value, this.name);'>
                        <option value='1'>Absent</option>
                        <option value='2' selected>Present</option>
                        <option value='3'>Late</option>
                        </select>
                        </i>
                        </small>";
            break;
          case '3':
            $attendance="<small id='lecture_".$fl_live_session."' class='text-warning'>
                        <i>
                        <select id='".$fl_live_session."' name='subtract_class".$i."' onchange='change_attendance_lecture(this.id, $fl_maestro[0], this.value, this.name);'>
                        <option value='1'>Absent</option>
                        <option value='2'>Present</option>
                        <option value='3' selected>Late</option>
                        </select>
                        </i>
                        </small>";
            break;
          default:
            $attendance="<small id='lecture_".$fl_live_session."' class='text-danger'>
                        <i>
                        <select id='".$fl_live_session."' name='subtract_class".$i."' onchange='change_attendance_lecture(this.id, $fl_maestro[0], this.value, this.name);'>
                        <option value='1' selected>Absent</option>
                        <option value='2'>Present</option>
                        <option value='3'>Late</option>
                        </select>
                        </i>
                        </small>";
            break;
        }
        
        echo "
        <tr class='$clase' id='reg_lecciones_$i'>
        <td align='center'>$no_semana[$i]</td>
        <td align='left'>$ds_liga $msg_error</td>
        <td align='left'>";
        
        // Estos campos se utilizan para actualizar solo los registros que se modificaron
        /*Forma_CampoOculto("fe_original_{$i}", $fe_clase[$i]);
        Forma_CampoOculto("hr_original_{$i}", $hr_clase[$i]);*/
        
        echo "<div class='row form-group smart-form'><div class='col col-sm-9'><label class='input col col-sm-8 no-padding $ds_error'>";
        CampoTexto('fe_clase_'.$i, $fe_clase[$i], 10, 10, $ds_clase, False, "readonly onchange='Actualiza($i, $clave)'");
        Forma_Calendario('fe_clase_'.$i);
        echo "</label><div class='col col-sm-4 no-padding'><label class='input'>";
        if(isset($hr_clase_err[$i]))
            $ds_clase = 'css_input_error';
        else
            $ds_clase = 'form-control';
        
        
        CampoTexto('hr_clase_'.$i, $hr_clase[$i], 5, 3, $ds_clase, False, "onchange='Actualiza($i, $clave)'");
        echo "</label></div></div>";
        if(!empty($zoom_url)){
            echo"<a href='".$zoom_url."' target='_blank' >zoom <i class='fa fa-external-link' aria-hidden='true'></i></a><br><small class='text-muted' style='float:right'>$ds_host_zoom</small>";
        }
        echo"
              </td>
              <td>".(strtotime($fe_clase[$i].' '.$hr_clase[$i])<=strtotime(date('Y-m-d H:i'))?$attendance:$future)."</td>
              <td align='center'>";

        Forma_CampoCheckbox(ObtenEtiqueta(1006), 'fg_mandatory_'.(isset($im)?$im:NULL), '1', '', '', false, "", 'left hidden', '', '');
        echo "</label></div></div></td>
            <td align='center'>";
        
        if($fg_adicional[$i] == '1') {
            CampoCheckbox('fg_obligatorio'.$i, $fg_obligatorio[$i], '', '', $p_editar=True, "onchange='Actualiza($i, $clave)'");
            echo "
              </td>
              <td align='center'>
                <a href=\"javascript:Borra($i, $clave);\"><img src = '".PATH_IMAGES."/".IMG_BORRAR."' title=".ETQ_ELIMINAR."></a>
              </td>";
        } else {
            CampoCheckbox('fg_obligatorio'.$i, $fg_obligatorio[$i], '', '', $p_editar=False);
            echo "
              </td>
              <td align='center'>
                <a href=\"javascript:Inserta($i, $clave);\"><img src = '".PATH_IMAGES."/".IMG_AGREGAR."' title=".ETQ_INSERTAR.">
              </td>";
        }

        echo "
            </td>
      </tr>\n";
        
        $clase_anterior = $clase;
        
        Forma_CampoOculto('fl_semana_'.$i, $fl_semana[$i]);
        Forma_CampoOculto('no_semana_'.$i, $no_semana[$i]);
        Forma_CampoOculto('ds_titulo_'.$i, $ds_titulo[$i]);
        Forma_CampoOculto('fl_clase_'.$i, $fl_clase[$i]);
    }
    Forma_CampoOculto('tot_semanas', $tot_semanas);
    Forma_Tabla_Fin( );
}
    echo "
      <style>
        .popover{
           border:none;
           border-radius:unset;
           min-width:750px;
           width:100%;
           max-width:750px;
           overflow-wrap:break-word;
        }     

        .popover-title .close{
            position: relative;
            bottom: 3px;
        }
        
        .tabla_traslapadas {
          color: #333;
          font-size: 11px;
          width: 100%;
          border-collapse:
          collapse; border-spacing: 0;
        }

        .tabla_traslapadas td, .tabla_traslapadas th {
          border: 1px solid transparent; /* No more visible border */
          height: 30px;
          transition: all 0.3s; /* Simple transition for hover effect */
        }

        .tabla_traslapadas th {
          background: #0092cd; /* Darken header a bit */
          font-weight: bold;
          color: #FFFFFF
        }

        .tabla_traslapadas td {
          background: #FAFAFA;
        }


      </style>


      <script>
	   pageSetUp();
      $( document ).ready(function() {
		 
          $('.traslapadas').popover({
              trigger: 'click',
              html: true,
              animation: false,
              placement: 'top'
           });
       });

       </script>";

    
?>

<!-- Here the script to change the attendance status on clases -->
<script>

  function change_attendance_lecture(live_session, fl_maestro, option, id){
    //alert(live_session+' - '+fl_maestro+' - '+option+' - '+id);
    $.ajax({
      type: 'post',
      url: 'change_attendance_lecture.php',
      data: {
        live_session:live_session,
        fl_maestro:fl_maestro,
        option:option
      },
      async: false,
      success: function (response) {
        location.reload();
      }
    });

  }

  function change_attendance_gg(live_session_gg, fl_maestro_gg, option_gg, id){
    //alert(live_session_gg + ' - ' + fl_maestro_gg + ' - ' + option_gg + ' - ' + id);
    $.ajax({
      type: 'post',
      url: 'change_attendance_global.php',
      data: {
        live_session_gg:live_session_gg,
        fl_maestro_gg:fl_maestro_gg,
        option_gg:option_gg
      },
      success: function (response) {
          location.reload();
      }
    });

  }

</script>
