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
  
  # Presenta los entregables
  for($tot_entregables = 0; $row = RecuperaRegistro($rs); $tot_entregables++) {
    $ds_ruta_entregable = str_uso_normal($row[0]);
    $ds_comentario_alu = str_texto($row[1]);
    echo "
                    <tr>
                      <td width='10'>&nbsp;</td>
                      <td valign='top' width='720' align='center' class='video_sketch'>";
    $ext = strtolower(ObtenExtensionArchivo($ds_ruta_entregable));
    $fg_video = '0';
    switch ($ext) {
      case "ogg":
        PresentaVideoHTML5(PATH_ALU."/videos/", $ds_ruta_entregable);
        $fg_video = '1';
        break;
      case "jpg":
      case "jpeg":
        echo "<img src='".PATH_ALU."/sketches/$ds_ruta_entregable' border='0' />";
        break;
    }
    echo "
                      </td>
                      <td width='10'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='3' height='5'>&nbsp;</td>
                    </tr>";
    echo "
    <script type='text/javascript'>
      $('#nb_archivo').val('$ds_ruta_entregable');
      $('#fg_video').val('$fg_video');
    </script>";
  }
  
  # Revisa si ya se entrego la asignacion
  if($tot_entregables == 0) {
    $ds_mensaje = "Required Assignment.<br>Submission deadline is ".ObtenLimiteEntregaSemana($fl_alumno, $no_semana).".";
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
  
  # Cuando se esta grabando la critica no muestra comentarios
  if($fg_rc == '0') {
    
    # Forma para agregar un nuevo comentario
    if($fg_otro_alumno)
      $ds_otro_alumno = '1';
    else
      $ds_otro_alumno = '0';
    echo "
                    <tr>
                      <td>&nbsp;</td>
                      <td valign='top' height='50'>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                        <form name='comments' method='post' action='comment_iu.php'>
                          <input type='hidden' name='redirect' value='desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab'>
                          <input type='hidden' name='entrega' value='$fl_entrega_semanal'>
                          <input type='hidden' name='tipo' value='$fg_tipo'>
                          <input type='hidden' name='otro_alumno' value='$ds_otro_alumno'>
                          <tr>
                            <td width='15' height='15' class='new_comment_corner_TL'>&nbsp;</td>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td width='55' class='new_comment_blank_cells'>&nbsp;</td>
                            <td width='15' class='new_comment_corner_TR'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td class='new_comment_title'>Add a comment:</td>
                            <td rowspan='2' class='new_comment_blank_cells'>
                              <input type='submit' name='' value='Submit'>
                            </td>
                            <td rowspan='2' class='new_comment_blank_cells'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td height='50' class='new_comment_blank_cells'>&nbsp;</td>
                            <td class='new_comment_content'>
                                <textarea name='comment' rows=2 cols=73></textarea>
                            </td>
                          </tr>
                          <tr>
                            <td height='15' class='new_comment_corner_BL'>&nbsp;</td>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td class='new_comment_corner_BR'>&nbsp;</td>
                          </tr>
                        </form>
                        </table>
                      </td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='3' height='5' class='blank_cells'>&nbsp;</td>
                    </tr>";
    
    # Recupera historial de comentarios
    $diferencia = RecuperaDiferenciaGMT( );
    $Query  = "SELECT fl_com_entregable, fl_usuario, DATE_FORMAT((DATE_ADD(fe_comentario, INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
    $Query .= "DATE_FORMAT((DATE_ADD(fe_comentario, INTERVAL $diferencia HOUR)), '%e, %Y at %l:%i %p') 'fe_dia_anio', ds_comentario, fg_leido ";
    $Query .= "FROM k_com_entregable ";
    $Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal ";
    $Query .= "AND fg_tipo='$fg_tipo' ";
    $Query .= "ORDER BY fe_comentario DESC";
    $rs = EjecutaQuery($Query);
    for($i = 1; $row = RecuperaRegistro($rs); $i++) {
      $fl_com_entregable = $row[0];
      $fl_usuario_msg = $row[1];
      $fe_comentario = ObtenNombreMes($row[2])." ".$row[3];
      $ds_comentario = str_uso_normal($row[4]);
      if($row[5] == '0' AND !$fg_otro_alumno)
        $ds_notificar = "(New comment)";
      else
        $ds_notificar = "";
      $ds_nombre = ObtenNombreUsuario($fl_usuario_msg);
      echo "
                    <tr>
                      <td colspan='3' valign='top' class='division_line'>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr><td colspan='7' class='comment_text'>&nbsp;</td></tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td width='80' valign='top' align='center'>
                              <a href='profile_view.php?profile_id=$fl_usuario_msg'><img src='".ObtenAvatarUsuario($fl_usuario_msg)."' border='none' title='View $ds_nombre profile' /></a>
                            </td> 
                            <td width='10'>&nbsp;</td>
                            <td valign='top'>
                              <div class='comment_text'><b>$ds_nombre</b> <span class='text_unread'>$ds_notificar</span></div>
                              On $fe_comentario wrote:<br><br>
                              <div class='comment_text'>$ds_comentario</div>
                            </td>
                            <td width='10'>&nbsp;</td>
                            <td width='30' valign='middle' align='center'>";
      
      # Solo se permite eliminar comentarios si los hizo el mismo usuario
      if($fl_usuario_msg == $fl_usuario)
        echo "
                            <form name='comment_$i' method='post' action='comment_del.php'>
                              <input type='hidden' name='redirect' value='desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab'>
                              <input type='hidden' name='id' value='$fl_com_entregable'>
                              <a href=\"javascript:document.comment_$i.submit();\"><img src='".PATH_COM_IMAGES."/delete.png' width='15' height='15' border='0' title='Delete comment'></a>
                            </form>";
      else
        echo "&nbsp;";
      echo "</td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                          <tr><td colspan='7' class='comment_text'>&nbsp;</td></tr>
                        </table>
                      </td>
                    </tr>";
    }
  }
  
?>