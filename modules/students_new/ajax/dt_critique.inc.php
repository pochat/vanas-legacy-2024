<?php 

	# Recupera los datos de la entrega de la semana y la calificacion de la asignacion
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);
  $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);
  $Query  = "SELECT a.fl_entrega_semanal, a.fg_entregado, a.ds_critica_animacion, a.fl_promedio_semana, ";
  $Query .= "b.cl_calificacion, b.ds_calificacion, b.fg_aprobado ";
  $Query .= "FROM k_entrega_semanal a LEFT JOIN c_calificacion b ";
  $Query .= "ON (a.fl_promedio_semana=b.fl_calificacion) ";
  $Query .= "WHERE a.fl_alumno=$fl_alumno ";
  $Query .= "AND a.fl_grupo=$fl_grupo ";
  $Query .= "AND a.fl_semana=$fl_semana";
  $row = RecuperaValor($Query);
  $fl_entrega_semanal = $row[0];
  $fg_entregado = $row[1];
  $ds_critica_animacion = str_ascii($row[2]);
  $fl_promedio_semana = $row[3];
  $cl_calificacion = $row[4];
  $ds_calificacion = str_uso_normal($row[5]);
  $fg_aprobado = $row[6];

  # Revisa si ya se califico la asignacion y presenta Critique
  if(!empty($ds_critica_animacion)) {
    # Webcam video
    $videoFile = ObtenNombreArchivo($ds_critica_animacion)."_cam.ogg";

    $result["camera"] = PATH_ALU."/critiques/".$videoFile;
    $result["video"] = PATH_ALU."/critiques/".$ds_critica_animacion;
    $result["video_plugin"] = PATH_COM_JS."/critiquevideos.js";
  } else {
    $result["message"] = "The critique for this lesson is not available.";
  }

  if(!empty($cl_calificacion)) {
    $calificacion = "Week Assignment Grade: $cl_calificacion $ds_calificacion";
  } else {
    $calificacion = "The grade for this week is pending.";
  }

  # Presenta calificacion de la semana
  if(!$fg_otro_alumno){
    $result["grade"] = $calificacion;
  }

  echo json_encode((Object) $result);
?>