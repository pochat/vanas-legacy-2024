<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Recibe parametros generales
  $fl_alumno = RecibeParametroNumerico('student', True);
  if(empty($fl_alumno)){
    header("Location: ".PATH_N_MAE."/index.php#ajax/home.php");
    exit;
  }
  $no_semana = RecibeParametroNumerico('week', True);
  $nb_tab = RecibeParametroHTML('tab', False, True);

  # Determine type of tab
  switch($nb_tab) {
    case "1": $nb_tab = "lecture";        break;
    case "2": $nb_tab = "brief";          break;
    case "3": $nb_tab = "assignment";     break;
    case "4": $nb_tab = "assignment_ref"; break;
    case "5": $nb_tab = "sketch";         break;
    case "6": $nb_tab = "sketch_ref";     break;
    case "7": $nb_tab = "critique";       break;
  }
  
  # Recupera los datos de la leccion
  $fl_programa = ObtenProgramaAlumno($fl_alumno);
  $no_grado = ObtenGradoAlumno($fl_alumno);
  $Query  = "SELECT ds_titulo, ds_leccion, ds_vl_ruta, ds_as_ruta, fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
  $Query .= "FROM c_leccion ";
  $Query .= "WHERE fl_programa=$fl_programa ";
  $Query .= "AND no_grado=$no_grado ";
  $Query .= "AND no_semana=$no_semana ";
  $row = RecuperaValor($Query);
  $ds_titulo = str_uso_normal($row[0]);
  $ds_leccion = str_uso_normal($row[1]);
  $ds_vl_ruta = str_uso_normal($row[2]);
  $ds_as_ruta = str_uso_normal($row[3]);
  $fg_animacion = $row[4];
  $fg_ref_animacion = $row[5];
  $no_sketch = $row[6];
  $fg_ref_sketch = $row[7];
  
  # Determina el tipo de entregable
  switch($nb_tab) {
    case "assignment":     $fg_tipo = "A";  break;
    case "assignment_ref": $fg_tipo = "AR"; break;
    case "sketch":         $fg_tipo = "S";  break;
    case "sketch_ref":     $fg_tipo = "SR"; break;
  }
  
  # Inicia pagina
  $semana_act = ObtenSemanaActualAlumno($fl_alumno);
  $fg_supervisor = EsSupervisor($fl_usuario);
  $fg_rc = '1';
  
  # Presenta el contenido del separador seleccionado
  switch($nb_tab) {
    //case "lecture":        require("dt_lecture.inc.php");    break;
    //case "brief":          require("dt_lecture.inc.php");    break;
    case "assignment":     require("dt_assignment_rc.inc.php"); break;
    case "assignment_ref": require("dt_assignment_rc.inc.php"); break;
    case "sketch":         require("dt_assignment_rc.inc.php"); break;
    case "sketch_ref":     require("dt_assignment_rc.inc.php"); break;
  }
  
?>