<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../lib/zoom_config.php';

  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion();

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


  $Query="SELECT fg_grupo_global FROM c_grupo WHERE fl_grupo=$clave ";
  $row=RecuperaValor($Query);
  $fg_grupo_global=$row[0];

  if($fg_grupo_global==1){

      $Query="SELECT zoom_id,zoom_url,fl_live_session_grupal FROM k_live_session_grupal WHERE fl_clase_grupo
                IN(SELECT fl_clase_grupo FROM k_clase_grupo WHERE fl_grupo=$clave)";
      $rs = EjecutaQuery($Query);
      while($row = RecuperaRegistro($rs)) {
          $zoom_id = $row[0];
          $fl_live_session_grupal=$row[2];

           if(!empty($zoom_id)){
              #Eliminamos la clase de zoom
               DeletedMeetingZoom($fl_live_session_grupal,'k_live_session_grupal',$zoom_id);
          }

      }


  }else{

      #Eliminamos todas las clases en zoom
      $Query="SELECT zoom_id,zoom_url,fl_live_session FROM k_live_session WHERE fl_clase IN(SELECT fl_clase FROM k_clase WHERE fl_grupo=949)";
      $rs = EjecutaQuery($Query);
      while($row = RecuperaRegistro($rs)) {
          $zoom_id = $row[0];
          $fl_live_session=$row[2];

          if(!empty($zoom_id)){
              #Eliminamos la clase de zoom
              DeletedMeetingZoom($fl_live_session,'k_live_session',$zoom_id);
          }
      }

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
  header("Location: ".ObtenProgramaBase());

?>