<?php
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  $fl_alumno=230;
  $fl_term_actual = ObtenTermAlumno($fl_alumno);
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);
  $calificaciones = array(6 => 4,4,4,4,3,3);
  
  $Query = "SELECT fl_semana FROM k_semana WHERE fl_term=$fl_term_actual ";
  $rs = EjecutaQuery($Query);
  for($i=0;$row= RecuperaRegistro($rs);$i++){
    $fl_semana = $row[0];
    $row1 = RecuperaValor("SELECT 1 FROM k_entrega_semanal WHERE fl_alumno=$fl_alumno AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo");
    # Existe
    if(empty($row1[0])){
      $insert = "INSERT INTO k_entrega_semanal (fl_alumno, fl_grupo, fl_semana, fl_promedio_semana) VALUES($fl_alumno, $fl_grupo, $fl_semana, $calificaciones[$i])";
      $fl_entrega_semanal = EjecutaQuery($insert);
      # Obtenemos fl_clase
      $rowc = RecuperaValor("SELECT fl_clase FROM k_clase WHERE fl_semana=$fl_semana AND fl_grupo=$fl_grupo");
      $Query  = "INSERT INTO k_live_session (fl_clase,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente)";
      $Query .= " VALUES ($rowc[0],1,'X','X','X') ";
      $fl_live_sesion = EjecutaInsert($Query);
      # Buscamos que no exista registro del alumno
      $Query = "SELECT COUNT(*) FROM k_live_session_asistencia WHERE fl_live_session= $fl_live_sesion AND fl_usuario=$fl_alumno ";
      $row = RecuperaValor($Query);
      $reg = $row[0];
      if(empty($reg)){
        $QueryAss  = "INSERT INTO k_live_session_asistencia (fl_live_session,fl_usuario,cl_estatus_asistencia) ";
        $QueryAss .= "VALUES ($fl_live_sesion,$fl_alumno,2) ";
        EjecutaQuery($QueryAss);
      }
    }
  }
  
?>