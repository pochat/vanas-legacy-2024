<?php 
	
  # Librerias
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_alumno = RecibeParametroNumerico('student', True);
  if(empty($fl_alumno))
    $fl_alumno = $fl_usuario;
  $no_semana = RecibeParametroNumerico('week', True);
  $nb_tab = RecibeParametroHTML('tab', False, True);
  $fl_programa =RecibeParametroNumerico('programa', True);
  $preview = RecibeParametroNumerico('preview', True);
  
  $fl_instituto = ObtenInstituto($fl_alumno);

  # Put the user last activity current time satamp when is viewed a course
  lastActivityUser($fl_alumno);
  lastActivityCourse($fl_alumno, $fl_programa);

  # Revisa si el alumno solicitado es el usuario de la sesion
  if($fl_usuario <> $fl_alumno) {
    $fg_otro_alumno = True;
  } else {
    $fg_otro_alumno = False;
  }
  $semana_act = ObtenSessionActualCourse($fl_alumno);
  // $max_semana = ObtenSemanaMaximaAlumno($fl_alumno);

  # Determine type of tab
  switch($nb_tab) {
    case "1": $nb_tab = "lecture";        break;
    // case "2": $nb_tab = "brief";          break;
    case "2": $nb_tab = "assignment";     break;
    case "3": $nb_tab = "assignment_ref"; break;
    case "4": $nb_tab = "sketch";         break;
    case "5": $nb_tab = "sketch_ref";     break;
    case "6": $nb_tab = "assignments_grade";     break;
    case "7": $nb_tab = "working_files";     break;
	case "8": $nb_tab = "student_library";     break;
    // case "7": $nb_tab = "critique";       break;
  }

  # Determina el tipo de entregable
  switch($nb_tab) {
    case "assignment":     $fg_tipo = "A";  break;
    case "assignment_ref": $fg_tipo = "AR"; break;
    case "sketch":         $fg_tipo = "S";  break;
    case "sketch_ref":     $fg_tipo = "SR"; break;
  }

  # Retrieves the lesson's data
  // $fl_programa = ObtenProgramaAlumno($fl_alumno);
	// $no_grado = ObtenGradoAlumno($fl_alumno);
	$Query  = "SELECT ds_titulo".$sufix.", ds_leccion".$sufix.", ds_vl_ruta, ds_as_ruta, fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch, ";
  $Query .= "ds_animacion".$sufix.", ds_ref_animacion".$sufix.", ds_no_sketch".$sufix.", ds_ref_sketch".$sufix.", fl_leccion_sp, fl_leccion_copy, ds_vl_ruta_copy ";
	$Query .= "FROM c_leccion_sp ";
	$Query .= "WHERE fl_programa_sp=$fl_programa ";
	// $Query .= "AND no_grado=$no_grado ";
  $Query .= "AND no_semana=$no_semana ";
	$row = RecuperaValor($Query);
	$ds_titulo = str_uso_normal($row[0]);
	$ds_leccion = str_uso_normal($row[1]);
	$ds_vl_ruta = html_entity_decode($row[2]);
	$ds_as_ruta = str_uso_normal($row[3]);
	$fg_animacion = $row[4];
	$fg_ref_animacion = $row[5];
	$no_sketch = $row[6];
	$fg_ref_sketch = $row[7];
	$ds_animacion = html_entity_decode($row[8]);
	$ds_ref_animacion = str_uso_normal($row[9]);
	$ds_no_sketch = str_uso_normal($row[10]);
	$ds_ref_sketch = str_uso_normal($row[11]);
  $fl_leccion_sp = $row[12];
  $fl_leccion_copy = $row[13];
  $ds_vl_ruta_copy = $row[14];
  # Valor para mostrar o no mostrar el tab de animacion 
  $result['tab_animacion'] = !empty($fg_animacion)?$fg_animacion:0;
  # Valor para mostrar o no mostrar el tab de ref animacion  
  $result['tab_ref_animacion'] = !empty($fg_ref_animacion)?$fg_ref_animacion:0;
  # Valor para mostrar o no mostrar el tab de sketch  
  if($no_sketch>0)
    $tab_sketch = 1;
  else
    $tab_sketch = 0;  
  $result['tab_sketch'] = $tab_sketch;
  # Valor para mostrar o no mostrar el tab de sketch  
  $result['tab_ref_sketch'] = !empty($fg_ref_sketch)?$fg_ref_sketch:0;
  
  
  #Verificamos si existe rubric para la leccion, || es para ocultar tab.
  $Query="SELECT COUNT(*) FROM k_criterio_programa_fame WHERE fl_programa_sp=$fl_leccion_sp ";
  $row=RecuperaValor($Query);
  $existe=$row[0];
  if($existe>0)
      $existe=1;
  else
      $existe=0;
  $result['tab_assignments_grade']=$existe;
  $result['fg_otro_alumno']=$fg_otro_alumno;
  # Obtenemo el teacher del alumno
  $rwg = RecuperaValor("SELECT fl_maestro FROM k_usuario_programa WHERE fl_programa_sp=".$fl_programa." AND fl_usuario_sp=".$fl_alumno);
  $fl_maestro = !empty($rwg[0])?$rwg[0]:NULL;
  if($fl_usuario<>$fl_maestro)
    $fg_otro_maestro = true;
  else
    $fg_otro_maestro = false;
  $result['fg_otro_maestro']=$fg_otro_maestro;
  $result['fl_programa_sp']=$fl_programa;
  
  
	# Present assignment tabs
	switch($nb_tab) {
    case "lecture":           require("dt_lecture.inc.php");    break;
    // case "brief":          require("dt_lecture.inc.php");    break;
    case "assignment":        require("dt_assignment.inc.php"); break;
    case "assignment_ref":    require("dt_assignment.inc.php"); break;
    case "sketch":            require("dt_assignment.inc.php"); break;
    case "sketch_ref":        require("dt_assignment.inc.php"); break;
	case "assignments_grade": require("assignments_grade.inc.php"); break;
	case "working_files":     require("dt_working_file.php"); break;
    case "student_library":   require("student_library.inc.php");   break;
  }
?>
