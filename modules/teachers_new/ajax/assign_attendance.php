<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_entrega_semanal = RecibeParametroNumerico('fl_entrega_semanal');
  $cl_estatus_asistencia = RecibeParametroNumerico('cl_estatus_assistencia');
  $fl_alumno = RecibeParametroNumerico('fl_alumno');
  $registros = RecibeParametroNumerico('registros');
  
  for($i=0; $i<$registros; $i++)
    $fl_clase[$i] = RecibeParametroNumerico('fl_clase_'.$i);


  # Si se obtiene un estatus de assitencia la actualiza 
  if(!empty($cl_estatus_asistencia)){
    # Primero Insertamos en k_live_sesion para posteriormente tomar ese valor y guardar la assistencia
    for($i=0;$i<$registros;$i++){
      # Buscamos si el ya existe un registro con esas clases no lo insertamos 
      $Query = "SELECT fl_live_session FROM k_live_session WHERE fl_clase=$fl_clase[$i] ";
      $row = RecuperaValor($Query);
      $fl_live_sesion = $row[0];
      if(empty($fl_live_sesion)){
        $Query  = "INSERT INTO k_live_session (fl_clase,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente)";
        $Query .= " VALUES ($fl_clase[$i],1,'X','X','X') ";
        $fl_live_sesion = EjecutaInsert($Query);
      }
      # Buscamos que no exista registro del alumno
      $Query = "SELECT COUNT(*) FROM k_live_session_asistencia WHERE fl_live_session= $fl_live_sesion AND fl_usuario=$fl_alumno ";
      $row = RecuperaValor($Query);
      $reg = $row[0];
      if(empty($reg)){
        $QueryAss  = "INSERT INTO k_live_session_asistencia (fl_live_session,fl_usuario,cl_estatus_asistencia,fe_asistencia) ";
        $QueryAss .= "VALUES ($fl_live_sesion,$fl_alumno,$cl_estatus_asistencia,CURDATE()) ";
        EjecutaQuery($QueryAss);
      }
    }
  }
  
  # Redirige al listado
  $pag = PATH_N_MAE."/index.php#ajax/submitted_assignments.php";
  header("Location: $pag");

?>