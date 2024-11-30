<?php

  # Recupera los datos de la entrega de la semana
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);
  $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);
  $Query  = "SELECT fl_entrega_semanal, fg_entregado, ds_critica_animacion, fl_promedio_semana ";
  $Query .= "FROM k_entrega_semanal ";
  $Query .= "WHERE fl_alumno=$fl_alumno ";
  $Query .= "AND fl_grupo=$fl_grupo ";
  $Query .= "AND fl_semana=$fl_semana";
  $row = RecuperaValor($Query);
  $fl_entrega_semanal = $row[0];
  $fg_entregado = $row[1];
  $ds_critica_animacion = str_uso_normal($row[2]);
  $fl_promedio_semana = $row[3];

  # Revisa si ya existe un registro para esta semana
  if(empty($fl_entrega_semanal)) {
    $Query  = "INSERT INTO k_entrega_semanal (fl_alumno, fl_grupo, fl_semana) ";
    $Query .= "VALUES($fl_alumno, $fl_grupo, $fl_semana)";
    $fl_entrega_semanal = EjecutaInsert($Query);
  }

  # Revisa si hay entregables para esta semana
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fl_entrega_semanal=$fl_entrega_semanal AND fg_tipo='$fg_tipo'");
  $tot_entregables = $row[0];

  # Cuando no se requiera referencia, buscar la ultima requerida y decir de que semana es
  if($tot_entregables == 0) {
    if((($fg_tipo == 'AR' AND empty($fg_ref_animacion)) OR ($fg_tipo == 'SR' AND empty($fg_ref_sketch))) AND $no_semana > 1) {
      $Query  = "SELECT max(no_semana), ds_titulo ";
      $Query .= "FROM c_leccion ";
      $Query .= "WHERE fl_programa=$fl_programa ";
      $Query .= "AND no_grado=$no_grado ";
      if($fg_tipo == 'AR')
        $Query .= "AND fg_ref_animacion='1' ";
      else
        $Query .= "AND fg_ref_sketch='1' ";
      $Query .= "AND no_semana < $no_semana";
      $row = RecuperaValor($Query);
      $no_semana_ant = $row[0];
      $ds_titulo_ant = str_uso_normal($row[1]);
      if(!empty($no_semana_ant)) {
        $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana_ant);
        $Query  = "SELECT fl_entrega_semanal ";
        $Query .= "FROM k_entrega_semanal ";
        $Query .= "WHERE fl_alumno=$fl_alumno ";
        $Query .= "AND fl_grupo=$fl_grupo ";
        $Query .= "AND fl_semana=$fl_semana";
        $row = RecuperaValor($Query);
        $fl_entrega_semanal = $row[0];
      }
    }
  }

  # Recupera los entregables
  $Query  = "SELECT ds_ruta_entregable, ds_comentario, fl_gallery_post ";
  $Query .= "FROM k_entregable a ";
  $Query .= "LEFT JOIN k_gallery_post b ON b.fl_entregable=a.fl_entregable ";
  $Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal ";
  $Query .= "AND fg_tipo='$fg_tipo' ";
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);

  $result["size"] = array();

  # Presenta los entregables
  for($tot_entregables = 0; $row = RecuperaRegistro($rs); $tot_entregables++) {
    $ds_ruta_entregable = str_uso_normal($row[0]);
    $ds_comentario_alu = str_texto($row[1]);
    $fl_gallery_post = $row[2];
    $ext = strtolower(ObtenExtensionArchivo($ds_ruta_entregable));

    # Reset assignments array
    $assignment = array();

    /* fl_gallery_post is included as a safety check in case that a student uploaded
     * the file on the old campus, and there was no modal created on the new campus
     * for this file. Modal view is prevented and will not appear.
     */
    // Old Campus upload
    if(empty($fl_gallery_post)){
      # There is no commenting from old campus
      $no_comments = 0;

      // video
      if($ext == "ogg" || $ext == "mp4") {
            $assignment["type"] = "video";
        $assignment["thumbnail"] = PATH_N_COM_UPLOAD."/gallery/thumbs/vanas-board-video-default.jpg";
        $assignment["src"] = PATH_ALU."/videos/".$ds_ruta_entregable;
      }
      // image
      else {
        $assignment["type"] = "image";
        $assignment["thumbnail"] = PATH_ALU."/sketches/$ds_ruta_entregable";
      }
      # Common old campus variables
      $assignment["campus"] = "old";
      $assignment["comments"] = $no_comments;
    }
    // New Campus upload
    else {
      # Find number of comments for this post
      $Query  = "SELECT COUNT(1) FROM k_gallery_comment WHERE fl_gallery_post=$fl_gallery_post";
      $row2 = RecuperaValor($Query);
      $no_comments = $row2[0];

      // video
      if($ext == "ogg" || $ext == "mp4"){
        $assignment["type"] = "video";
        $assignment["thumbnail"] = PATH_N_COM_UPLOAD."/gallery/thumbs/vanas-board-video-default.jpg";
      }
      //image
      else {
        $assignment["type"] = "image";
        $assignment["thumbnail"] = PATH_ALU."/sketches/board_thumbs/$ds_ruta_entregable";
      }
      # Common new campus variables
      $assignment["campus"] = "new";
      $assignment["comments"] = (int) $no_comments;
      $assignment["fl_gallery_post"] = $fl_gallery_post;
    }

    $assignments[$tot_entregables] = $assignment;
  }
  $result["size"] += array("total_assignments" => $tot_entregables);
  if($tot_entregables > 0){
    $result["assignments"] = (Object) $assignments;
  }

  # Revisa si ya se entrego la asignacion
  if($tot_entregables == 0) {
    $ds_mensaje = "Required Assignment.<br>Submission deadline is ".ObtenLimiteEntregaSemana($fl_alumno, $no_semana).".";
    if( ($fg_tipo == 'A' AND empty($fg_animacion)) OR ($fg_tipo == 'S' AND empty($no_sketch)) OR ($fg_tipo == 'AR' AND empty($fg_ref_animacion)) OR ($fg_tipo == 'SR' AND empty($fg_ref_sketch)) ){
      $ds_mensaje = "Not required for this lesson. ";
    }
    $result["message"] .= $ds_mensaje;
  }

  # Referencia de una semana anterior
  if(!empty($no_semana_ant)) {
    $result["message"] .= "Reference is not required for this lesson.<br>Showing reference for week $no_semana_ant, '$ds_titulo_ant'";
  }

  # Requerimientos de la entrega para Sketch
  if($fg_tipo == 'S' AND $tot_entregables < $no_sketch) {
    if($no_sketch == 1){
      $ds_mensaje = "<br>$no_sketch sketch is required for this lesson.";
    } else {
      $ds_mensaje = "<br>$no_sketch sketches are required for this lesson.";
    }
    $result["message"] .= $ds_mensaje;
  }
  echo json_encode((Object) $result);
?>