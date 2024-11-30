<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Recibe parametros generales
  $fl_alumno = RecibeParametroNumerico('alumno');
  $no_semana = RecibeParametroNumerico('semana');
  $nb_tab = RecibeParametroHTML('tab');
  
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
  PresentaSeparadores($fl_alumno, $no_semana, $nb_tab, True, True, $fg_supervisor, True);
  
  # Presenta el contenido del separador seleccionado
  switch($nb_tab) {
    case "lecture":        require("dt_lecture.inc.php");    break;
    case "brief":          require("dt_lecture.inc.php");    break;
    case "assignment":     require("dt_assignment_rc.inc.php"); break;
    case "assignment_ref": require("dt_assignment_rc.inc.php"); break;
    case "sketch":         require("dt_assignment_rc.inc.php"); break;
    case "sketch_ref":     require("dt_assignment_rc.inc.php"); break;
  }
  
?>