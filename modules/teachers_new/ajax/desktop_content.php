<?php 
	
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_alumno = RecibeParametroNumerico('student', True);
  if(empty($fl_alumno)){
  	header("Location: ".PATH_N_MAE."/index.php#ajax/home.php");
  	exit;
  }
	$no_semana = RecibeParametroNumerico('week', True);
  $nb_tab = RecibeParametroHTML('tab', False, True);

  # Revisa si el maestro es supervisor
  $fg_supervisor = EsSupervisor($fl_usuario);
  $fg_otro_alumno = True;
  $semana_act = ObtenSemanaActualAlumno($fl_alumno);
  $max_semana = ObtenSemanaMaximaAlumno($fl_alumno);

  # Identificamos que el usuario es nuevo
  $std_new = StudentNew($fl_alumno);
  # Solo se mostrar para el teacher del alumno
  $teacher_std = ObtenMaestroAlumno($fl_alumno);  

  #Recupermaos el maestro del grupos globales(review).
  $Quer="SELECT fl_grupo FROM k_alumno_grupo WHERE fl_alumno=$fl_alumno AND fg_grupo_global='1' ";
  $rs5=EjecutaQuery($Quer);
  for($im=0;$row=RecuperaRegistro($rs5); $im++){
      $fl_grupo = $row[0];

      $Querg="SELECT COUNT(*) FROM k_clase_grupo WHERE fl_grupo=$fl_grupo AND fl_maestro=$fl_usuario  ";
      $ros=RecuperaValor($Querg);
      if(!empty($ros[0])){
          $teacher_std=$fl_usuario;
          
      }

  }




  if(!empty($std_new) && $teacher_std==$fl_usuario){
    # Determine type of tab
    switch($nb_tab) {
      case "1": $nb_tab = "lecture";            break;
      case "2": $nb_tab = "brief";              break;
      case "3": $nb_tab = "assignment";         break;
      case "4": $nb_tab = "assignment_ref";     break;
      case "5": $nb_tab = "sketch";             break;
      case "6": $nb_tab = "sketch_ref";         break;
      case "7": $nb_tab = "critique";           break;
      case "8": $nb_tab = "assignments_grade";  break;
      case "9": $nb_tab = "Working_Files";      break;
    }
    $fg_tab8=true;
  }
  else{
    # Determine type of tab
    switch($nb_tab) {
      case "1": $nb_tab = "lecture";        break;
      case "2": $nb_tab = "brief";          break;
      case "3": $nb_tab = "assignment";     break;
      case "4": $nb_tab = "assignment_ref"; break;
      case "5": $nb_tab = "sketch";         break;
      case "6": $nb_tab = "sketch_ref";     break;
      case "7": $nb_tab = "critique";       break;
      case "8": $nb_tab = "assignments_grade";  break;
      case "9": $nb_tab = "Working_Files";  break;
    }
    $fg_tab8=false;
  }

  # Determina el tipo de entregable
  switch($nb_tab) {
    case "assignment":     $fg_tipo = "A";  break;
    case "assignment_ref": $fg_tipo = "AR"; break;
    case "sketch":         $fg_tipo = "S";  break;
    case "sketch_ref":     $fg_tipo = "SR"; break;
  }

  # Retrieves the lesson's data
  $fl_programa = ObtenProgramaAlumno($fl_alumno);
	$no_grado = ObtenGradoAlumno($fl_alumno);
	$Query  = "SELECT ds_titulo, ds_leccion, ds_vl_ruta, ds_as_ruta, fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch, fl_leccion ";
	$Query .= "FROM c_leccion ";
	$Query .= "WHERE fl_programa=$fl_programa ";
	$Query .= "AND no_grado=$no_grado ";
	$Query .= "AND no_semana=$no_semana ";
	$row = RecuperaValor($Query);
	$ds_titulo = str_uso_normal($row[0]);
	$ds_leccion = str_uso_normal(html_entity_decode($row[1]));
	$ds_vl_ruta = str_uso_normal($row[2]);
	$ds_as_ruta = str_uso_normal($row[3]);
	$fg_animacion = $row[4];
	$fg_ref_animacion = $row[5];
	$no_sketch = $row[6];
	$fg_ref_sketch = $row[7];
  $fl_leccion = $row[8];
  # Valor para mostrar o no mostrar el tab de animacion 
  $result['tab_animacion'] = $fg_animacion;
  # Valor para mostrar o no mostrar el tab de ref animacion  
  $result['tab_ref_animacion'] = $fg_ref_animacion;
  # Valor para mostrar o no mostrar el tab de sketch  
  if($no_sketch>0)
    $tab_sketch = 1;
  else
    $tab_sketch = 0;  
  $result['tab_sketch'] = $tab_sketch;
  # Valor para mostrar o no mostrar el tab de sketch  
  $result['tab_ref_sketch'] = $fg_ref_sketch;
  # Valor para mostrar o no mostrar el tab de video brief
  if(empty($ds_as_ruta))
    $fg_as_ruta = 0;
  else
    $fg_as_ruta = 1;
  $result['tab_as_ruta'] = $fg_as_ruta;
  # Obtenemos el maestro del alumno a quien solo se le mostrara el Working Files
  $fg_es_teacher = false;
  if($teacher_std==$fl_usuario)
    $fg_es_teacher = true;
  $result['fg_es_teacher'] = $fg_es_teacher;
  $result['fg_otro_alumno'] = $fg_otro_alumno;
  $result['fg_teachers'] = true;
  $result['fg_tab8'] = $fg_tab8;
  
  # Mario es el super usuario y el podra ver todo
  $result['fg_super'] = false;
  if($fl_usuario==13)
    $result['fg_super'] = true;

	# Present assignment tabs
  # Dependiendo si el estudiante esnuevo mostrara tab de assigment
  if(!empty($std_new) && $teacher_std==$fl_usuario){
    switch($nb_tab) {
      case "lecture":        require("dt_lecture.inc.php");    break;
      case "brief":          require("dt_lecture.inc.php");    break;
      case "assignment":     require("dt_assignment.inc.php"); break;
      case "assignment_ref": require("dt_assignment.inc.php"); break;
      case "sketch":         require("dt_assignment.inc.php"); break;
      case "sketch_ref":     require("dt_assignment.inc.php"); break;
      case "critique":       require("dt_critique.inc.php");   break;
      case "assignments_grade": require("assignments_grade.inc.php"); break;
      case "Working_Files":  require("dt_working_file.php"); break;
    }
  }
  else{
    switch($nb_tab) {
      case "lecture":        require("dt_lecture.inc.php");    break;
      case "brief":          require("dt_lecture.inc.php");    break;
      case "assignment":     require("dt_assignment.inc.php"); break;
      case "assignment_ref": require("dt_assignment.inc.php"); break;
      case "sketch":         require("dt_assignment.inc.php"); break;
      case "sketch_ref":     require("dt_assignment.inc.php"); break;
      case "critique":       require("dt_critique.inc.php");   break;
      case "assignments_grade": require("assignments_grade.inc.php"); break;
      case "Working_Files":  require("dt_working_file.php");   break;
    }

  }
	
?>