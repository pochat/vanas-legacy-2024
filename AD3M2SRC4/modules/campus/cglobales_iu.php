<?php  
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  # Funciones para adobe conect
  require '../../lib/zoom_config.php';
  include ('Sessionadobe.php');

  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
    
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(132, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recibe parametros
  $fg_error = 0;
  # Obtenemos los programas que se seleccionaron
  $programas = $_REQUEST['fl_programa'];  
  $tot_programas = 0;
  if(!empty($programas)){   
    foreach ($programas as $programa){
      $programas_seleccionados++;
      $programa = $programa.",";
    }
  }
  # Maestro y nombre de la clase
  $ds_titulo = RecibeParametroHTML('ds_titulo');
  $ds_clase_topic = RecibeParametroHTML('ds_clase');
  $fl_maestrog = RecibeParametroNumerico('fl_maestrog');
  $fg_dia_sesion = RecibeParametroNumerico('fg_dia_sesion');
  $fe_start_date = RecibeParametroFecha('fe_start_date');
  $hr_sesion = RecibeParametroHoraMin("hr_sesion");
  $fg_mandatory = RecibeParametroBinario("fg_mandatory");
  $fg_zoom=RecibeParametroBinario('optionsRadio2');

  # Recupera las fechas de cada clase
  $tot_semanas = RecibeParametroNumerico('tot_semanas');
  for($i = 0; $i < $tot_semanas; $i++) {
    $fl_clase_cg[$i] = RecibeParametroNumerico('fl_clase_cg'.$i);
    $no_orden[$i] = RecibeParametroNumerico('no_orden_'.$i);
    $ds_clase[$i] = RecibeParametroHTML('ds_titulo_'.($i+1));
    $fe_clase[$i] = RecibeParametroFecha('fe_clase_'.($i+1));
    $hr_clase[$i] = RecibeParametroHoraMin('hr_clase_'.($i+1));
    $fg_obligatorio[$i] = RecibeParametroBinario('fg_obligatorio_'.($i+1));
    $fl_maestro[$i] = RecibeParametroNumerico('fl_maestro_'.($i+1));
  }

  # Valida campos obligatorios
  if(empty($programas_seleccionados))
    $programas_seleccionados_err = 924;
  if(empty($ds_titulo))
    $ds_titulo_err = ERR_REQUERIDO;
  
  # Verifica que el formato de la fecha sea valido
  for($i = 0; $i < $tot_semanas; $i++) {
    if(!empty($fe_clase[$i]) AND !ValidaFecha($fe_clase[$i]))
      $fe_clase_err[$i] = ERR_FORMATO_FECHA;
    if(!empty($hr_clase[$i]) AND !ValidaHoraMin($hr_clase[$i]))
      $hr_clase_err[$i] = ERR_FORMATO_HORAMIN;
    
    
    // MDB
    // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
    // entonces puede agregar la clase, de lo contrario regresa el error a la forma.     
    
    $licenciaService = new LicenciaAdobeService();
    $clasesService = new ClasesService();
          
    $fechaHora = "'" . ValidaFecha($fe_clase[$i]) . ' ' . ValidaHoraMin($hr_clase[$i]) . "'";
    
    $fl_clase_actual = $fl_clase[$i];
    
    $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, 0);
    $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, 0);      
    $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);      
          
    if (!$licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {    
      $fe_clase_err[$i] = ObtenMensaje(230)."<br/>";
    }
    $fg_omitir_actuales=1;

    $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, 0,$fg_omitir_actuales);
    $clavesLicenciasTraslapadasZoom = $clasesService->getClavesLicenciasTraslapadasZoom($fechaHora, 0);      
    $licenciasZoom = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom);

    if($fg_zoom==1){

        if (!$licenciaService->licenciasSuficientesZoom($clasesTraslapadasZoom, sizeof($licenciasZoom))) {    
            $fe_clase_err[$i] = ObtenMensaje(230)."<br/>";
        }
    }


  }
  
	# Regresa a la forma con error
  $fg_error = $programas_seleccionados_err || $ds_titulo_err;
  for($i = 0; $i < $tot_semanas; $i++) {
    $fg_error = $fg_error || $fe_clase_err[$i];
    $fg_error = $fg_error || $hr_clase_err[$i];
  }
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('programas_seleccionados_err', $programas_seleccionados_err);    
    Forma_CampoOculto('ds_titulo', $ds_titulo);
    Forma_CampoOculto('ds_titulo_err', $ds_titulo_err);
    Forma_CampoOculto('ds_clase', $ds_clase_topic);
    Forma_CampoOculto('fl_maestrog', $fl_maestrog);    
    Forma_CampoOculto('fg_dia_sesion', $fg_dia_sesion);
    Forma_CampoOculto('fe_start_date', $fe_start_date);
    Forma_CampoOculto('hr_sesion', $hr_sesion);
    Forma_CampoOculto('fg_mandatory', $fg_mandatory);
    Forma_CampoOculto('tot_semanas', $tot_semanas);
    Forma_CampoOculto('fg_zoom', $fg_zoom);
    for($i = 1; $i < $tot_semanas; $i++) {
      Forma_CampoOculto('ds_titulo_'.$i, $fl_semana[$i]);      
      Forma_CampoOculto('fe_clase_'.$i, $fe_clase[$i]);
      Forma_CampoOculto('fe_clase_err_'.$i, $fe_clase_err[$i]);
      Forma_CampoOculto('hr_clase_'.$i, $hr_clase[$i]);
      Forma_CampoOculto('hr_clase_err_'.$i, $hr_clase_err[$i]);
    }    
    echo "\n</form>
  <script>
    document.datos.submit();
  </script></body></html>";
    exit;
  }
  

  #Eliminamos las clases que esten relacionado con la clase global
  EjecutaQuery("DELETE FROM k_clase_cg WHERE fl_clase_global=$clave");
  # Eliminamos los alumnos de esta clase global
  EjecutaQuery("DELETE FROM k_alumno_cg WHERE fl_clase_global=$clave");

  EjecutaQuery("DELETE FROM k_curso_cg WHERE fl_clase_global=$clave");

  # Insertamos los programas que selecionaron para esta clase global
  # Buscamos los alumnos que  estan inscritos en los programas seleccionados y lo guardamos
  $no_alumnos = 0;
  foreach ($programas as $programa){
      $Queryp = "INSERT INTO k_curso_cg(fl_programa, fl_clase_global) VALUES(".$programa.",$clave)";
      EjecutaQuery($Queryp);
      $Queryc  = "SELECT fl_usuario, b.ds_email FROM k_ses_app_frm_1 a, c_usuario b "; 
      $Queryc .= "WHERE a.cl_sesion = b.cl_sesion AND b.fg_activo='1' AND fl_programa=".$programa." AND fl_perfil='".PFL_ESTUDIANTE."' ";
      $rsc = EjecutaQuery($Queryc);
      for($j=0;$rowc = RecuperaRegistro($rsc);$j++){        
          $fl_usuario = $rowc[0];
          $ds_email = $rowc[1];
          $Querys = "INSERT INTO k_alumno_cg (fl_clase_global,fl_usuario) VALUES ($clave, $fl_usuario) ";
          EjecutaQuery($Querys);
          $no_alumnos++;
      }
  }
 




  # Inserta o actualiza el registro
  if(!empty($clave)){
    # Actualizamos los datos
    $Query  = "UPDATE c_clase_global SET fl_maestro=$fl_maestrog, ds_clase='$ds_titulo', ";
    $Query .= "fg_dia_sesion='$fg_dia_sesion', hr_sesion='$hr_sesion', fg_mandatory='$fg_mandatory' ";
    $Query .= ",fg_zoom='$fg_zoom' ";
    $Query .= "WHERE fl_clase_global=$clave";
    EjecutaQuery($Query);
    # Si en la clase global se agregan o quitan programas eliminara los datos
    # Posteriormente los agregara
   
    # Eliminamos las clases que existen en k_live_sesion_cg con relacion a la clase global
    // $rs = EjecutaQuery("SELECT fl_clase_cg FROM k_clase_cg WHERE fl_clase_global=$clave");
    // for($i=0;$row=RecuperaRegistro($rs);$i++){
      // $fl_clase_cg = $row[0];
      // EjecutaQuery("DELETE FROM k_live_sesion_cg WHERE fl_clase_cg=$fl_clase_cg");
    // }

    
    # Consultamos la table temporal para insertalos en la clase original
    $Query1 = "SELECT fl_clase_cg, no_orden, ds_titulo, DATE_FORMAT(fe_clase, '%d-%m-%Y'), DATE_FORMAT(fe_clase, '%H:%i:%s'), fg_obligatorio, fl_maestro ";
    $Query1 .= "FROM k_clase_cg_temporal WHERE fl_clase_global=$clave";
    $rs = EjecutaQuery($Query1);
    for($k=0;$row1=RecuperaRegistro($rs);$k++){
      $fl_clase_cg = $row1[0];
      $no_orden = $row1[1];
      $ds_titulo = $row1[2];
      $fechaHora = "'".ValidaFecha($row1[3])." ".$row1[4]."'";
      $fg_obligatorio = $row1[5];
      $fl_maestro = $row1[6];
      $fecha_clase=$row1[3];
      $hr_clase=$row1[4];

      # Inicio de proceso adobe
      $fgActualizarRegistroClase = false; 

      // Verifica si tiene o no creada una live session, si le falta, se la crea.
      // Borra la clase anterior y agrega la nueva sobre la licencia que le corresponde por la nueva fecha y hora
      $QueryDel  = "SELECT fl_live_session_cg,zoom_id,zoom_url ";
      $QueryDel .= "FROM k_live_sesion_cg WHERE fl_clase_cg = $fl_clase_cg";   
      $rowDel = RecuperaValor($QueryDel);
      $fl_live_session_actual = $rowDel[0];
      $zoom_id=$rowDel[1];
      $zoom_url=$rowDel[2];

      # Valida sesion existe
      if(empty($fl_live_session_actual) ) { 
        $fgActualizarRegistroClase = true;
      }
      
      // En caso de cambio de fecha/hora, eliminamos el registro para liberar la licencia actual y generar un nuevo registro    
      if ($fgActualizarRegistroClase) {
        // Borra la clase anterior y agrega la nueva sobre la licencia que le corresponde por la nueva fecha y hora
        $QueryDel  = "SELECT fl_live_session_cg, cl_licencia, cl_meeting_id,zoom_id,zoom_url ";
        $QueryDel .= "FROM k_live_sesion_cg where fl_clase_cg = $fl_clase_cg";   
        $rowDel = RecuperaValor($QueryDel);
        
        $fl_live_session_actual = $rowDel[0];
        $cl_licencia_actual = $rowDel[1];
        $cl_meeting_id_actual = $rowDel[2];
        $zoom_id=$rowDel[3];
        $zoom_url=$rowDel[4];

        $LicenciaActual = $licenciaService->getLicenciaByClave($cl_licencia_actual);
                
        #Eliminmaos licencia de zoom si existe.
        //if(!empty($zoom_id)){
        //    DeletedMeetingZoom($fl_live_session_actual,'k_live_sesion_cg',$zoom_id);
        //}
        
        if (!empty($cl_meeting_id_actual)) {
          $Query = "DELETE FROM k_live_sesion_cg WHERE fl_live_session_cg = $fl_live_session_actual";
          EjecutaQuery($Query);
          
          delLiveSession($cl_meeting_id_actual, $LicenciaActual, True);      
        }
      }      
      // MDB
      // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
      // entonces puede agregar la clase, de lo contrario regresa el error a la forma.     
      
      $licenciaService = new LicenciaAdobeService();
      $clasesService = new ClasesService();      
      
      // $fechaHora = $fe_clase[$i];
      
      $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, 0);
      $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, 0);      
      $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas, true);
      $fg_omitir_actuales=1;
      #Zoom
      $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, $fl_clase_cg,$fg_omitir_actuales,1);
      $clavesLicenciasTraslapadasZoom = $clasesService->getClavesLicenciasTraslapadasZoom($fechaHora, 0);      
      $licenciasZoom = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom, true);


      //////////////////////////////////////////////////////////////
      // IMPORTANTE:
      // Las licencias a contar son las que ya estan siendo usadas por clases traslapadas mas las que se necesitan para las clases nuevas
      //
      // Las licencias que se deben usar para las clases nuevas son las que se obtuvieron en $licenciasAdobe, esta excluye las licencias en uso 
      // de ese mismo grupo de clases traslapadas.
      //////////////////////////////////////////////////////////////
      $numLicenciasAdobe = sizeof($licenciasAdobe) + sizeof($clavesLicenciasTraslapadas);
      $numLicenciasZoom = sizeof($licenciasZoom) + sizeof($clavesLicenciasTraslapadasZoom);
      
      if($fg_zoom==1){
          
      }else{
          $clasesTraslapadasZoom=$clasesTraslapadas;
          $numLicenciasZoom=$numLicenciasAdobe;

      }

     #sE CAMBIA POR LA DE ZOOM 
     # if ($licenciaService->licenciasSuficientes($clasesTraslapadas, $numLicenciasAdobe)) {   
      if ($licenciaService->licenciasSuficientesZoom($clasesTraslapadasZoom, $numLicenciasZoom)) {
        # Buscamos la clase si esta la actualizamos y si no la insertamos
        $roww = RecuperaValor("SELECT fl_clase_cg FROM k_clase_cg WHERE fl_clase_cg=$fl_clase_cg AND fl_clase_global=$clave");        
        if(empty($roww[0])) {
          $Query = "INSERT INTO k_clase_cg (fl_clase_cg, fl_clase_global,no_orden,ds_titulo,fe_clase,fg_obligatorio,fl_maestro) ";
          $Query .= "VALUES ($fl_clase_cg, $clave, $no_orden, '$ds_titulo', $fechaHora, '$fg_obligatorio', $fl_maestro ) ";
          EjecutaQuery($Query);
        }
        else {     
          // Modifica el registro solo si tuvo cambios          
          if ($fgActualizarRegistroClase) {
            $Query  = "UPDATE k_clase_cg SET no_orden=$no_orden, ds_titulo='$ds_titulo', fe_clase=$fechaHora, fl_maestro=$fl_maestro, fg_obligatorio='$fg_obligatorio'  ";
            $Query .= "WHERE fl_clase_cg=$fl_clase_cg";			
            EjecutaQuery($Query);      
          }          
        }
        // ECHO $Query;
        // Creacion sesion campus y AdobeConnect
        if (sizeof($licenciasAdobe)> 0) {
          $licenciaAC = $licenciasAdobe[0]; // Usa una nueva licencia, toma la primera del arreglo
          iuLiveSession($fl_clase_cg, $licenciaAC, True);  
        }

        if(empty($zoom_url)){

            $QueryDel  = "SELECT fl_live_session_cg, cl_licencia, cl_meeting_id,zoom_id,zoom_url ";
            $QueryDel .= "FROM k_live_sesion_cg where fl_clase_cg = $fl_clase_cg";   
            $rowDel = RecuperaValor($QueryDel);
            
            $fl_live_session_actual = $rowDel[0];
            $cl_licencia_actual = $rowDel[1];
            $cl_meeting_id_actual = $rowDel[2];
            $zoom_id=$rowDel[3];
            $zoom_url=$rowDel[4];

            if(empty($fl_live_session_actual)){
                $Query="INSERT INTO k_live_sesion_cg (fl_clase_cg,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente)VALUES($fl_clase_cg,'1','','','') ";
                $fl_live_session_actual=EjecutaInsert($Query);
            }

            if(sizeof($licenciasZoom)>0){
            
                #Obtenemos fecha actual :
                $Query = "Select CURDATE() ";
                $row = RecuperaValor($Query);
                $fe_actual = str_texto($row[0]);
                $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                $fe_actual= date('Y-m-d',$fe_actual);

                #Damos formato a la clase para zoom.
                $fe_clase_zoom=strtotime('+0 day',strtotime($fecha_clase));
                $fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
                $fe_clase_actual=$fe_clase_zoom;
                $fe_clase_zoom=$fe_clase_zoom."T".$hr_clase.":00";
                $pass_clase_zoom=rand(99999,5)."i".$fl_live_session_actual;
               
                #Aqui meee quede
                if($fe_clase_actual>=$fe_actual){

                    $licenciaAZ = $licenciasZoom[0];// Usa una nueva licencia, toma la primera del arreglo
                    if((!empty($fl_live_session_actual))&&(!empty($licenciaAZ))){
                        
                        #Creamos la clase en zoom solo si viene seleccionado zoom.
                        if($fg_zoom==1){
                            create_meetingZoom($fl_live_session_actual,'60',$ds_titulo,$fe_clase_zoom,$pass_clase_zoom,'k_live_sesion_cg',$licenciaAZ);
                           
                        }
                    }

                }


            }

        }



      }
      else {
        //$fe_clase_err[$i] = ObtenMensaje(230);
      }
      // logger("======== FIN PROCESO CLASE =================");
    }
  }
  else{
    # Insertamos la nueva clase global
    $Query  = "INSERT INTO c_clase_global (fl_maestro, ds_clase, fg_dia_sesion, hr_sesion, fg_mandatory) ";
    $Query .= "VALUES($fl_maestrog, '$ds_titulo', '$fg_dia_sesion', '$hr_sesion', '$fg_mandatory')";
    $clave = EjecutaInsert($Query);
    
    for($i = 0; $i < $tot_semanas; $i++) {
      // $fe_clase[$i] = "'".ValidaFecha($fe_clase[$i])." ".$hr_clase[$i]."'";
      // MDB
      // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
      // entonces puede agregar la clase, de lo contrario regresa el error a la forma.     
      
      $licenciaService = new LicenciaAdobeService();
      $clasesService = new ClasesService();
      
      $fechaHora = '{'.$fe_clase[$i].'} {'.$hr_clase[$i].'}';
      
      $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora,0);      
      $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora,0);
      $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas); 
      
      $fechaHoraZoom = "'" . ValidaFecha($fe_clase[$i]) . ' ' . ValidaHoraMin($hr_clase[$i]) . "'"; 
      #Para zoom
      $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHoraZoom,0,1);      
      $clavesLicenciasTraslapadasZoom = $clasesService->getClavesLicenciasTraslapadasZoom($fechaHoraZoom,0);
      $licenciasZoom = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom,True);
      $licenciasZoomDisponibles=getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom,True);


      #Se cambia por zooom 
      #if ($licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {    
      if (sizeof($licenciasZoom)>0) {
        $Query  = "INSERT INTO k_clase_cg (fl_clase_global, no_orden, ds_titulo, fe_clase, fg_obligatorio, fl_maestro) ";
        $Query .= "VALUES($clave, $no_orden[$i], '$ds_clase[$i]', '".ValidaFecha($fe_clase[$i])." ".$hr_clase[$i]."', '$fg_obligatorio[$i]', $fl_maestro[$i])";                    
        $fl_clase_insertada = EjecutaInsert($Query);
        
        // Creacion sesion campus y AdobeConnect
        $licenciaAC = $licenciasAdobe[0];
        iuLiveSession($fl_clase_insertada, $licenciaAC, true);
        
        #Recupermos la clase insrrtada
        $Query  = "SELECT fl_live_session_cg, cl_licencia, cl_meeting_id,zoom_id,zoom_url ";
        $Query .= "FROM k_live_sesion_cg where fl_clase_cg = $fl_clase_insertada ";   
        $row = RecuperaValor($Query);
        $fl_live_session_cg=$row[0];
        $zoom_id=$row[3];
        $zoom_url=$row[4];

        if(!empty($fl_live_session_cg)){
            
            if(empty($zoom_url)){

                if(sizeof($licenciasZoom)>0){
                    
                    #Obtenemos fecha actual :
                    $Query = "Select CURDATE() ";
                    $row = RecuperaValor($Query);
                    $fe_actual = str_texto($row[0]);
                    $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                    $fe_actual= date('Y-m-d',$fe_actual);

                    #Damos formato a la clase para zoom.
                    $fe_clase_zoom=strtotime('+0 day',strtotime($fe_clase[$i]));
                    $fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
                    $fe_clase_actual=$fe_clase_zoom;
                    $fe_clase_zoom=$fe_clase_zoom."T".$hr_clase[$i].":00";
                    $pass_clase_zoom=rand(99999,5)."i".$fl_live_session_cg;
                    $ds_titulo=$ds_titulo." ".$ds_clase[$i];
                    #Aqui meee quede
                    if($fe_clase_actual>=$fe_actual){

                        $licenciaAZ = $licenciasZoom[0];// Usa una nueva licencia, toma la primera del arreglo
                        if((!empty($fl_live_session_cg))&&(!empty($licenciaAZ))){
                            #Creamos la clase en zoom
                            create_meetingZoom($fl_live_session_cg,'60',$ds_titulo,$fe_clase_zoom,$pass_clase_zoom,'k_live_sesion_cg',$licenciaAZ);
                        }

                    }



                }


            }







        }


        







      }      
    }
  }
  
  # Guardamos el numero de los alumnos que tendra la clase global
  EjecutaQuery("UPDATE c_clase_global SET no_alumnos=$no_alumnos WHERE fl_clase_global=$clave");
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));  
?>