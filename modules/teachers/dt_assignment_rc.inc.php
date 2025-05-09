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
  $Query  = "SELECT ds_ruta_entregable, ds_comentario ";
  $Query .= "FROM k_entregable ";
  $Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal ";
  $Query .= "AND fg_tipo='$fg_tipo' ";
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);
  $tot_entregables = CuentaRegistros($rs);
  $fg_video = '0';
  for($i = 0; $row = RecuperaRegistro($rs); $i++) {
    $ds_ruta_entregable[$i] = str_uso_normal($row[0]);
    $ds_comentario_alu[$i] = str_texto($row[1]);
    $ext = strtolower(ObtenExtensionArchivo($ds_ruta_entregable[$i]));
    if($ext == "ogg")
      $fg_video = '1';
  }
  
  # Presenta los entregables
  if($tot_entregables > 0) {
    echo "
                    <tr>
                      <td width='10'>&nbsp;</td>
                      <td valign='top' width='720' align='center' class='video_sketch'>";
    if($fg_video == '0') {
      echo "
    <script type='text/javascript'>
      $(document).ready(function() {
        $('.jqzoom').jqzoom({
          zoomType: 'drag',
          lens:true,
          preloadImages: true,
          zoomWidth: 425,
          zoomHeight: 305,
          xOffset: 10
        });
      });
    </script>
        <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0' width='720' height='405'>
          <tr>
            <td width='280' height='315' align='center' valign='top'>
              <a href='".PATH_ALU."/sketches/original/".$ds_ruta_entregable[0]."' class='jqzoom' rel='gal1'>
                <img src='".PATH_ALU."/sketches/regular/".$ds_ruta_entregable[0]."' border='0'>
              </a>
            </td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan='2' height='90'>
              <ul id='thumblist' class='clearfix'>";
      for($i = 0; $i < $tot_entregables; $i++) {
        $ds_clase = "";
        if($i == 0)
          $ds_clase = "class='zoomThumbActive'";
        echo "
                <li><a $ds_clase href='javascript:void(0);' rel=\"{
                  gallery: 'gal1',
                  smallimage: '".PATH_ALU."/sketches/regular/".$ds_ruta_entregable[$i]."',
                  largeimage: '".PATH_ALU."/sketches/original/".$ds_ruta_entregable[$i]."'
                }\"><img src='".PATH_ALU."/sketches/thumbs/".$ds_ruta_entregable[$i]."' border='0'></a></li>";
      }
      echo "
              </ul>
            </td>
          </tr>
        </table>";
    }
    else
      PresentaVideoHTML5(PATH_ALU."/videos/", $ds_ruta_entregable[0]);
    echo "
                      </td>
                      <td width='10'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='3' height='5'>&nbsp;</td>
                    </tr>";
    
    # Variables para indicar al proceso de grabacion de critica
    echo "
    <script type='text/javascript'>
      $('#nb_archivo').val('".$ds_ruta_entregable[0]."');
      $('#fg_video').val('$fg_video');
    </script>";
  }
  
  # Revisa si ya se entrego la asignacion
  if($tot_entregables == 0) {
    $ds_mensaje = "This assignment has not been submitted.<br>Submission deadline is ".ObtenLimiteEntregaSemana($fl_alumno, $no_semana).".";
    if(($fg_tipo == 'A' AND empty($fg_animacion)) OR ($fg_tipo == 'S' AND empty($no_sketch)) OR
       ($fg_tipo == 'AR' AND empty($fg_ref_animacion)) OR ($fg_tipo == 'SR' AND empty($fg_ref_sketch)))
       $ds_mensaje = "Not required for this lesson.";
    echo "
                    <tr>
                      <td width='10'>&nbsp;</td>
                      <td valign='top' width='720' height='430' align='center' class='video_sketch'>$ds_mensaje</td>
                      <td width='10'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='3' height='5'>&nbsp;</td>
                    </tr>";
  }
  
  # Referencia de una semana anterior
  if(!empty($no_semana_ant)) {
    echo "
                <tr>
                  <td width='10'>&nbsp;</td>
                  <td valign='top' width='720' align='center' class='comment_text'>
                    Reference is not required for this lesson. Showing reference for week $no_semana_ant, '$ds_titulo_ant'
                  </td>
                  <td width='10'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='3' height='5'>&nbsp;</td>
                </tr>";
  }
  
  # Requerimientos de la entrega para Sketch
  if($fg_tipo == 'S' AND $tot_entregables < $no_sketch) {
    if($no_sketch == 1)
      $ds_mensaje = "$no_sketch sketch is required for this lesson.";
    else
      $ds_mensaje = "$no_sketch sketches are required for this lesson.";
    echo "
                <tr>
                  <td width='10'>&nbsp;</td>
                  <td valign='top' width='720' align='center' class='comment_text'>$ds_mensaje</td>
                  <td width='10'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='3' height='5'>&nbsp;</td>
                </tr>";
  }
  
?>