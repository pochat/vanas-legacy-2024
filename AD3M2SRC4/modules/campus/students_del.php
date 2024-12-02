<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ALUMNOS, PERMISO_BAJA)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
  # Verifica que se haya recibido la clave
  if(empty($clave)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Elimina el registro
  $row = RecuperaValor("SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$clave");
  $cl_sesion = $row[0];
  EjecutaQuery("DELETE FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion'");
  EjecutaQuery("DELETE FROM k_ses_app_frm_2 WHERE cl_sesion='$cl_sesion'");
  EjecutaQuery("DELETE FROM k_ses_app_frm_3 WHERE cl_sesion='$cl_sesion'");
  EjecutaQuery("DELETE FROM k_ses_app_frm_4 WHERE cl_sesion='$cl_sesion'");
  EjecutaQuery("DELETE FROM k_app_contrato WHERE cl_sesion='$cl_sesion'");
  EjecutaQuery("DELETE FROM c_sesion WHERE cl_sesion='$cl_sesion'");
  
  EjecutaQuery("DELETE FROM k_pctia WHERE fl_alumno=$clave");
  EjecutaQuery("DELETE FROM k_alumno_grupo WHERE fl_alumno=$clave");
  EjecutaQuery("DELETE FROM k_alumno_term WHERE fl_alumno=$clave");
  EjecutaQuery("DELETE FROM k_alumno_pago WHERE fl_alumno=$clave");
  # verifica si existen detalle de los pagos en k_alumno_pago_det
  $rs = EjecutaQuery("SELECT fl_alumno_pago FROM k_alumno_pago WHERE fl_alumno=$clave");
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $fl_alumno_pago=$row[0];
    EjecutaQuery("DELETE FROM k_alumno_pago_det WHERE fl_alumno_pago=$fl_alumno_pago");
  }
  $Query  = "SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_alumno=$clave";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    EjecutaQuery("DELETE FROM k_com_entregable WHERE fl_entrega_semanal=$row[0]");
    $Query  = "SELECT fl_entregable FROM k_entregable WHERE fl_entrega_semanal=$clave";
    $rs2 = EjecutaQuery($Query);
    while($row = RecuperaRegistro($rs2)) {
      EjecutaQuery("DELETE FROM k_calificacion WHERE fl_entregable=$row2[0]");
    }
    EjecutaQuery("DELETE FROM k_entregable WHERE fl_entrega_semanal=$row[0]");
    EjecutaQuery("DELETE FROM k_record_critique_audio WHERE fl_entrega_semanal=$row[0]");
    EjecutaQuery("DELETE FROM k_record_critique_session WHERE fl_entrega_semanal=$row[0]");
  }
  EjecutaQuery("DELETE FROM k_entrega_semanal WHERE fl_alumno=$clave");
  EjecutaQuery("DELETE FROM k_live_session_asistencia WHERE fl_alumno=$clave");
  EjecutaQuery("DELETE FROM c_alumno WHERE fl_alumno=$clave");
  
  EjecutaQuery("DELETE FROM k_f_comentario WHERE fl_usuario=$clave");
  $Query  = "SELECT fl_post FROM k_f_post WHERE fl_usuario=$clave";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    EjecutaQuery("DELETE FROM k_f_comentario WHERE fl_post=$row[0]");
  }
  EjecutaQuery("DELETE FROM k_f_post WHERE fl_usuario=$clave");
  EjecutaQuery("DELETE FROM k_com_blog WHERE fl_usuario=$clave");
  EjecutaQuery("DELETE FROM k_mensaje_directo WHERE fl_usuario_ori=$clave");
  EjecutaQuery("DELETE FROM k_mensaje_directo WHERE fl_usuario_dest=$clave");
  EjecutaQuery("DELETE FROM k_not_blog WHERE fl_usuario=$clave");
  EjecutaQuery("DELETE FROM k_usu_login WHERE fl_usuario=$clave");
  EjecutaQuery("DELETE FROM c_usuario WHERE fl_usuario=$clave");
  
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>