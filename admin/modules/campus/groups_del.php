<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_GRUPOS, PERMISO_BAJA)) {
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
  
  # Elimina registros referenciados
  EjecutaQuery("DELETE FROM k_com_entregable WHERE fl_entrega_semanal IN(SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_grupo=$clave)");
  EjecutaQuery("DELETE FROM k_entregable WHERE fl_entrega_semanal IN(SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_grupo=$clave)");
  EjecutaQuery("DELETE FROM k_record_critique_audio WHERE fl_entrega_semanal IN(SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_grupo=$clave)");
  EjecutaQuery("DELETE FROM k_record_critique_session WHERE fl_entrega_semanal IN(SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_grupo=$clave)");
  EjecutaQuery("DELETE FROM k_entrega_semanal WHERE fl_grupo=$clave");
  EjecutaQuery("DELETE FROM k_live_session_asistencia WHERE fl_live_session IN(SELECT fl_live_session FROM k_live_session WHERE fl_clase IN(SELECT fl_clase FROM k_clase WHERE fl_grupo=$clave))");
  EjecutaQuery("DELETE FROM k_live_session WHERE fl_clase IN(SELECT fl_clase FROM k_clase WHERE fl_grupo=$clave)");
  EjecutaQuery("DELETE FROM k_clase WHERE fl_grupo=$clave");
  EjecutaQuery("DELETE FROM k_alumno_grupo WHERE fl_grupo=$clave");
  
  # Elimina el registro
  EjecutaQuery("DELETE FROM c_grupo WHERE fl_grupo=$clave");
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>