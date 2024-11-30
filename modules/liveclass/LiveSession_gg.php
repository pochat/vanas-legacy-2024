<?php        
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
 // require 'AdobeConnectClient.class.php';
  
 // require PATH_ADM_HOME . "/lib/adobeconnect/LicenciaAdobeService.class.php";
  
  require("bbb_api.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( False, 5 * 60 * 60 );
  
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
    
  /* TODO MDB Dar de alta la función
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }*/
  
  # Recibe parametros
  $fl_clase_cg = RecibeParametroNumerico('folio', True);
  
  if ($fl_clase_cg == '' || $fl_clase_cg == 0)
    exit;
  
  // Valida que la clase esté activa y este en la tolerancia
  // Calcula el estatus de asistencia.
  
  $minutos_tolerancia_antes = -1 * ObtenConfiguracion(34);
  $minutos_tolerancia_despues = ObtenConfiguracion(35);
  $minutos_duracion_sesion = ObtenConfiguracion(37);
  $minutos_link_disponible = ObtenConfiguracion(36);
  
  
  $Query  = "SELECT a.fe_clase ";
  $Query .= "from k_clase_grupo a ";
  $Query .= "where a.fl_clase_grupo = $fl_clase_cg ";
  //$Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $minutos_link_disponible MINUTE)) >= 0 ";  
  //$Query .= "AND TIMESTAMPDIFF(SECOND, DATE_ADD(fe_clase, INTERVAL -$minutos_tolerancia_antes MINUTE), '".ObtenFechaActual( )."') >= 0 ";  
  $row = RecuperaValor($Query); 
  
  $fe_clase = $row[0];
  
  $sesion_activa = 0;
  if ($row[0] != '') 
    $sesion_activa = 1;
  
  if ($sesion_activa == 0) {    
    echo "Session not available yet.";
    exit;
  }
      
  // Valida que el usuario tenga acceso a la clase 
  $Query = "SELECT fl_live_session_grupal, fl_clase_grupo, cl_estatus, ds_meeting_id, ";
  $Query .= "ds_password_admin, ds_password_asistente, ds_mensaje_bienvenida,zoom_url ";
  $Query .= "FROM k_live_session_grupal WHERE fl_clase_grupo = $fl_clase_cg ";
  $live_session_cg = RecuperaValor($Query);  


  // Valida existencia de la sesion
  if ($live_session_cg["fl_live_session_grupal"] == '') {
    /* ID creado en base a los siguientes criterios:
     * Fecha Livesession + Hora Livesession + Group Name + Random (5)
     */
    $meetingID = mt_rand(10000000, 99999999);
    // Estos passwords son creados usando un random
    $attendeePW = mt_rand(10000, 99999 );
    $moderatorPW = mt_rand(10000, 99999 );
    // Mensaje de bienvenida, usamos Lesson title
    $Query = "SELECT nb_clase FROM k_clase_grupo a   ";
    $Query .= "WHERE  a.fl_clase_grupo= $fl_clase_cg ";
    $row = RecuperaValor($Query);
    $welcome=$row['nb_clase'];

    // MDB 25/OCT/2012
    // No quitar este codigo porque se usara cuando regresemos a BBB
    // Dejarlo comentado, de lo contrario si el teacher entra antes de que el administrador
    // guarde los cambios en groups and schedules se creara la sesion como si fuera para BBB
    // y no podran accederla
    
    /*
    $Query  = "INSERT INTO k_live_session ";
    $Query .= "(fl_clase, cl_estatus, ";
    $Query .= "ds_meeting_id, ds_password_admin, ds_password_asistente, ds_mensaje_bienvenida) ";
    $Query .= "VALUES ($fl_clase, 1, '$meetingID', '$moderatorPW', '$attendeePW', '$welcome') ";

    EjecutaQuery($Query);
    */
  }
  
  // Obtiene el registro, por si se acaba de insertar
  $fl_live_session_cg = $live_session_cg["fl_live_session_grupal"];
  $meetingID = $live_session_cg["ds_meeting_id"];        
  $attendeePW = $live_session_cg["ds_password_asistente"];
  $moderatorPW = $live_session_cg["ds_password_admin"];
  $zoom_url=$live_session_cg['zoom_url'];
  // Mensaje de bienvenida, usamos Lesson title
  $Query = "SELECT nb_clase,fl_grupo FROM k_clase_grupo a             ";
  $Query .= "WHERE  a.fl_clase_grupo= $fl_clase_cg ";
  $row = RecuperaValor($Query);
  $welcome=$row['nb_clase'];
  $fl_grupo=$row['fl_grupo'];

  #Verifica si es por zoom
  $QueryZ="SELECT fg_zoom FROM c_grupo WHERE fl_grupo=$fl_grupo ";
  $rowZ=RecuperaValor($QueryZ);
  $fg_zoom=$rowZ['fg_zoom'];


  // Nombre de despliegue para el usuario o profesor (Name)
  $name = ObtenNombreUsuario($fl_usuario);
  
  // URL para redireccionar al estudiante una vez que ha terminado, usamos el que tiene actualmente
  $logoutURL = ObtenConfiguracion(38);
  // Tomar de c_configuracion, es una clave usada para validar seguridad entre el cliente y el servidor
  $SALT = ObtenConfiguracion(33);
  // Tomar de c_configuracion, es el url de la aplicación servidor de bigbluebutton
  $URL = ObtenConfiguracion(32);
  
  //$licenciaService = new LicenciaAdobeService();

  // MDB ADOBECONNECT 14/SEP/2012
  $urlAdobeConnect = ObtenConfiguracion(53);  
  if($fl_perfil == PFL_ESTUDIANTE) {
    $pwdUsuarioActual = $attendeePW;
    $urlAdobe = $urlAdobeConnect . $meetingID . "/?guestName=".$name;

    $QueryLs  = "SELECT fl_live_session_grupal, cl_licencia, cl_meeting_id,zoom_url ";
    $QueryLs .= "FROM k_live_session_grupal WHERE fl_clase_grupo = $fl_clase_cg";   
    $rowLs = RecuperaValor($QueryLs);

    $fl_live_session_actual = $rowLs[0];
    $cl_licencia_actual = $rowLs[1];
    $cl_meeting_id_actual = $rowLs[2];
    $zoom_url=$rowLs[3];

    #2020 se sutituye la url de adobe por la de Zoom
    if($fg_zoom==1){
        $urlAdobe=$zoom_url;
    }

  }
  if($fl_perfil == PFL_MAESTRO) {
    $pwdUsuarioActual = $moderatorPW;
    $urlAdobe = $urlAdobeConnect . $meetingID . "/";

    $QueryLs  = "SELECT fl_live_session_grupal, cl_licencia, cl_meeting_id,zoom_url ";
    $QueryLs .= "FROM k_live_session_grupal WHERE fl_clase_grupo = $fl_clase_cg";   
    $rowLs = RecuperaValor($QueryLs);

    $fl_live_session_actual = $rowLs[0];
    $cl_licencia_actual = $rowLs[1];
    $cl_meeting_id_actual = $rowLs[2];
    $zoom_url=$rowLs[3];
    
  //  $LicenciaActual = $licenciaService->getLicenciaByClave($cl_licencia_actual); 
    
  //  $clienteAdobe = new AdobeConnectClient($LicenciaActual);
  //  $urlAdobe .= "?session=" . $clienteAdobe->getSessionHost($urlAdobe); 
    
    #2020 se sutituye la url de adobe por la de Zoom
    if($fg_zoom==1){
        $urlAdobe=$zoom_url;
    }
  }

  // Estatus asistencia  
  $now = strtotime(ObtenFechaActual());
  $fecha_clase = strtotime($fe_clase);
  $tolerancia_antes = strtotime($minutos_tolerancia_antes . " minutes", strtotime($fe_clase));
  // MDB A la tolerancia le agregamos un minuto, el calculo y la condicion marcan como absent
  // si es que se llega justo en el ultimo minuto
  $tolerancia_despues = strtotime(($minutos_tolerancia_despues + 1) . " minutes", strtotime($fe_clase));
  
  if ( $now <= $tolerancia_despues )
    $estatus_asistencia = 2; // Present
  else
    $estatus_asistencia = 3; // Late

  $Query  = "SELECT fl_usuario FROM k_live_session_asistencia_gg WHERE fl_live_session_gg = $fl_live_session_cg AND fl_usuario = $fl_usuario ";
  $row = RecuperaValor($Query);
  
  if ($row[0] == '')  {
      $Query  = "INSERT INTO k_live_session_asistencia_gg ";
      $Query .= "(fl_live_session_gg, fl_usuario, cl_estatus_asistencia_gg, fe_asistencia_gg)";
    $Query .= "VALUES ($fl_live_session_cg, $fl_usuario, $estatus_asistencia, CURRENT_TIMESTAMP ) ";

    EjecutaQuery($Query);  
  }
  
  /* MDB ADOBECONNECT No borrar este codigo, se usara para regresar a BBB
  $bbbObj = new BigBlueButton();
  $urlJoinMeeting = $bbbObj->createMeetingAndGetJoinURL($name, $meetingID, $welcome, $moderatorPW, $attendeePW, $SALT, $URL, $logoutURL);      

  $urlJoinMeeting = $bbbObj->joinURL($meetingID, $name, $pwdUsuarioActual, $SALT, $URL);      
  */
  
  // MDB ADOBECONNECT 13/SEP/2012
  $urlJoinMeeting = $urlAdobe; 

   #No borrar esto era antes de adobe conect.
  if($fg_zoom==1){
      
?>
<script>
    location.href = "<?php echo $urlAdobe?>";
</script>


<?php
  }else{
      header("Location: $urlJoinMeeting");
  }
?>
