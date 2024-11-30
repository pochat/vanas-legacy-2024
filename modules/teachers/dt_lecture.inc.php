<?php
  
  # Recupera los datos de la entrega de la semana
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);
  $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);
  $Query  = "SELECT fl_entrega_semanal ";
  $Query .= "FROM k_entrega_semanal ";
  $Query .= "WHERE fl_alumno=$fl_alumno ";
  $Query .= "AND fl_grupo=$fl_grupo ";
  $Query .= "AND fl_semana=$fl_semana";
  $row = RecuperaValor($Query);
  $fl_entrega_semanal = $row[0];
  
  # Presenta separador de Video Lecture
  echo "
                    <tr>
                      <td width='10'>&nbsp;</td>
                      <td valign='top' width='720' height='320' align='center' class='video_sketch'>";
  
  # Solo se permite ver la leccion actual y dos hacia atras (excepto el supervisor)
  if($nb_tab == 'lecture') {
    $dif = $semana_act-$no_semana;
    if((($dif >= 0 AND $dif < 3) OR $fg_supervisor) AND !empty($ds_vl_ruta)) {
      PresentaVideoJWP($ds_vl_ruta);
      $ds_matricula = ObtenMatriculaAlumno($fl_usuario);
      PresentaWatermark($ds_matricula);
    }
    else
      echo "<br>The video lecture for this lesson is not available.<br><br>";
  }
  else {
    if(!empty($ds_as_ruta)) {
      PresentaVideoJWP($ds_as_ruta);
      $ds_matricula = ObtenMatriculaAlumno($fl_usuario);
      PresentaWatermark($ds_matricula);
    }
    else
      echo "<br>The video brief for this lesson is not available.<br><br>";
  }
  echo "
                      </td>
                      <td width='10'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='3' height='5'>&nbsp;</td>
                    </tr>";
  
  # Presenta el titulo de la leccion
  echo "
                    <tr>
                      <td>&nbsp;</td>
                      <td valign='top' height='50'>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr>
                            <td width='15' height='15' class='new_comment_corner_TL'>&nbsp;</td>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td width='15' class='new_comment_corner_TR'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td class='lecture_title'>$ds_titulo</td>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td height='15' class='new_comment_corner_BL'>&nbsp;</td>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td class='new_comment_corner_BR'>&nbsp;</td>
                          </tr>
                        </table>
                      </td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='3' height='5'>&nbsp;</td>
                    </tr>";
  
  # Presenta la descripcion de la leccion, solo si es la actual y dos atras (excepto el supervisor)
  if(($dif >= 0 AND $dif < 3) OR $fg_supervisor)
    echo "
                    <tr>
                      <td colspan='3' valign='top' class='division_line'>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr>
                            <td colspan='3'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td class='comment_text'>$ds_leccion</td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td colspan='3'>&nbsp;</td>
                          </tr>
                        </table>
                      </td>
                    </tr>";
  
?>