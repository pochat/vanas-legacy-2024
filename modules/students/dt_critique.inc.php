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
    echo "
    <div id='dlg_camara'>";
    $pathVideo = SP_HOME . "/modules/students/critiques/";
    $videoFile = ObtenNombreArchivo($ds_critica_animacion)."_cam.ogg";
    PresentaVideoHTML5Webcam($pathVideo, $videoFile);
    echo "</div>
    <script type='text/javascript'>
      $(function() {  
        $('#dlg_camara').dialog({
          width: 270,
          height: 225,
          position: [932, 218],
          closeOnEscape: false,
          title: 'Teacher',
          resizable: false,
          beforeClose: function(event, ui) { return false; }
        });
      });
    </script>";
    echo "
                    <tr>
                      <td width='10'>&nbsp;</td>
                      <td valign='top' width='720' align='center' class='video_sketch'>";
    $ext = strtolower(ObtenExtensionArchivo($ds_critica_animacion));
    switch ($ext) {
      case "ogg": PresentaVideoHTML5Critique(PATH_ALU."/critiques/", $ds_critica_animacion); break;
      case "flv": PresentaVideo(PATH_ALU."/critiques/", $ds_critica_animacion); break;
    }
    echo "
                      </td>
                      <td width='10'>&nbsp;</td>
                    </tr>
                    <tr><td colspan='3' height='5'>&nbsp;</td></tr>";
  }
  else {
    echo "
                    <tr>
                      <td width='10'>&nbsp;</td>
                      <td valign='top' width='720' height='320' align='center' class='video_sketch'>
                        The critique for this lesson is not available.
                      </td>
                      <td width='10'>&nbsp;</td>
                    </tr>
                    <tr><td colspan='3' height='5'>&nbsp;</td></tr>";
  }
  if(!empty($cl_calificacion)) {
    if($fg_aprobado <> '1')
      $ds_aprobado = "<span class='text_unread'>Not approved</span>";
    else
      $ds_aprobado = "Approved";
    $calificacion = "Week Assignment Grade: $cl_calificacion $ds_calificacion $ds_aprobado";
  }
  else
    $calificacion = "The grade for this week is pending.";
  
  # Presenta calificacion de la semana
  if(!$fg_otro_alumno)
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
                            <td class='lecture_title'>$calificacion</td>
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
                    </tr>";
  echo "
                    <tr>
                      <td colspan='3' height='5'>&nbsp;</td>
                    </tr>";
  
?>