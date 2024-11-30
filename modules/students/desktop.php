<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # 06/10/14 jgfl
  $Query  = "SELECT fg_graduacion, fg_activo FROM k_pctia a, c_usuario b ";
  $Query .= "WHERE a.fl_alumno=b.fl_usuario AND b.fl_perfil= ".PFL_ESTUDIANTE." AND fl_alumno = $fl_usuario ";
  $row = RecuperaValor($Query);
  $fg_graduacion = $row[0];
  $fg_activo = $row[1];
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_alumno = RecibeParametroNumerico('student', True);
  if(empty($fl_alumno))
    $fl_alumno = $fl_usuario;
  $no_semana = RecibeParametroNumerico('week', True);
  $nb_tab = RecibeParametroHTML('tab', False, True);
  $no_err = RecibeParametroNumerico('err', True);
  
  # Revisa si el alumno solicitado es el usuario de la sesion
  if($fl_usuario <> $fl_alumno) {
    $nombre = ObtenNombreUsuario($fl_alumno);
    $titulo = "$nombre's Desktop";
    $fg_otro_alumno = True;
  }
  else {
    $titulo = "My Desktop";
    $fg_otro_alumno = False;
  }
  
  # Inicializa variables
  $semana_act = ObtenSemanaActualAlumno($fl_alumno);
  $max_semana = ObtenSemanaMaximaAlumno($fl_alumno);
  if(empty($no_semana) OR $no_semana > $max_semana OR $no_semana > $semana_act)
    $no_semana = $semana_act;
  if($nb_tab <> "brief" AND $nb_tab <> "assignment" AND $nb_tab <> "assignment_ref" AND $nb_tab <> "sketch" AND $nb_tab <> "sketch_ref" AND $nb_tab <> "critique")
    $nb_tab = "lecture";
  if(($no_semana < $semana_act-2 OR $fg_otro_alumno) AND $nb_tab == "lecture")
    $nb_tab = "assignment";
  
  # Determina el tipo de entregable
  switch($nb_tab) {
    case "assignment":     $fg_tipo = "A";  break;
    case "assignment_ref": $fg_tipo = "AR"; break;
    case "sketch":         $fg_tipo = "S";  break;
    case "sketch_ref":     $fg_tipo = "SR"; break;
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
  
  # Inicia pagina
  PresentaHeader($titulo, $fl_alumno);
  if(!empty($fg_activo))#06/10/14 jgfl
    PresentaSeparadores($fl_alumno, $no_semana, $nb_tab, $fg_otro_alumno);
  
  # Inicia area de trabajo
  echo "
              <tr>
                <td colspan='2' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>";
  
  # Presenta el contenido del separador seleccionado
  if(!empty($fg_activo)) { #06/10/14 jgfl
    switch($nb_tab) {
      case "lecture":        require("dt_lecture.inc.php");    break;
      case "brief":          require("dt_lecture.inc.php");    break;
      case "assignment":     require("dt_assignment.inc.php"); break;
      case "assignment_ref": require("dt_assignment.inc.php"); break;
      case "sketch":         require("dt_assignment.inc.php"); break;
      case "sketch_ref":     require("dt_assignment.inc.php"); break;
      case "critique":       require("dt_critique.inc.php");   break;
    }
  }
  
  # Boton para regresar
  if($fg_otro_alumno)
    echo "
                    <tr>
                      <td colspan='3' align='center'>
                        <button type='button' id='buttons' OnClick='javascript:history.go(-1);'>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </button>
                      </td>
                    </tr>";
  echo "
                    <tr><td colspan='3' height='20'></td></tr>";
  
  # Cierra pagina
  if(!$fg_otro_alumno)
    PresentaFooter($no_err, $no_semana, $fg_tipo);
  else
    PresentaFooter( );
  
?>