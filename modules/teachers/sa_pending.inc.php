<?php
  
  # Recupera asignaciones pendientes por calificar
  $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
  $Query  = "SELECT a.fl_entrega_semanal, a.fl_alumno, a.fg_entregado, a.ds_critica_animacion, a.fl_promedio_semana, ";
  $Query .= ConcatenaBD($concat)." 'ds_nombre' ";
  $Query .= "FROM k_entrega_semanal a, c_usuario b ";
  $Query .= "WHERE a.fl_alumno=b.fl_usuario ";
  $Query .= "AND a.fl_grupo=$fl_grupo ";
  $Query .= "AND a.fl_semana=$fl_semana ";
  if($no_tab == 1) { // Assignments to grade - Aqui ves los estudiantes que por lo menos han subido un archivo / MRA: Y que no tienen calificacion
    $Query .= "AND a.fl_promedio_semana IS NULL ";
    $Query .= "AND EXISTS(SELECT 1 FROM k_entregable c WHERE c.fl_entrega_semanal=a.fl_entrega_semanal) ";
  }
  if($no_tab == 2) // Grading History - Aqui esta la historia de los estudiantes ya calificados, esto es para consulta, para re-hacer critiques y para cambiar calificaciones
    $Query .= "AND a.fl_promedio_semana IS NOT NULL ";
  $Query .= "ORDER BY ds_nombre";
  $rs2 = EjecutaQuery($Query);
  while($row2 = RecuperaRegistro($rs2)) {
    $fl_entrega_semanal = $row2[0];
    $fl_alumno = $row2[1];
    $fg_entregado = $row2[2];
    $ds_critica_animacion = str_ascii($row2[3]);
    $fl_promedio_semana = $row2[4];
    if(empty($fl_promedio_semana))
      $fl_promedio_semana = 0;
    $ds_nombre = str_uso_normal($row2[5]);
    if($fg_entregado == '1')
      $ds_status = "Uploaded";
    else
      $ds_status = "<span style='color: red;'>Pending upload</span>";
    
    # Inicia registro
    echo "
                        <tr class='assignments_lesson'>
                          <td width='80' valign='top' align='center'>
                            <a href='profile_student.php?profile_id=$fl_alumno' title='View $ds_nombre profile'><img src='".ObtenAvatarUsuario($fl_alumno)."' border='none'/></a>
                          </td>
                          <td>
                            <a href='desktop.php?student=$fl_alumno&week=$no_semana' title='View $ds_nombre desktop'>$ds_nombre</a>
                            <br><br>
                            $ds_status
                          </td>
                          <td>";
    
    # Recupera los entregables del alumno
    $Query  = "SELECT fl_entregable, fg_tipo, no_orden, ds_comentario ";
    $Query .= "FROM k_entregable ";
    $Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal ";
    $Query .= "ORDER BY fg_tipo, no_orden";
    $rs3 = EjecutaQuery($Query);
    $tot_entregables = CuentaRegistros($rs3);
    while($row3 = RecuperaRegistro($rs3)) {
      $fl_entregable = $row3[0];
      $fg_tipo = $row3[1];
      $no_orden = $row3[2];
      $ds_comentario = str_uso_normal($row3[3]);
      $ds_orden = "";
      switch($fg_tipo) {
        case 'A':  $ds_tipo = "Assignment";           $nb_tab = "assignment"; break;
        case 'AR': $ds_tipo = "Assignment reference"; $nb_tab = "assignment_ref"; break;
        case 'S':  $ds_tipo = "Sketch";               $nb_tab = "sketch";
          $ds_orden = " $no_orden";
          break;
        case 'SR': $ds_tipo = "Sketch reference";     $nb_tab = "sketch_ref"; break;
      }
      echo "<a href='desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab'>$ds_tipo$ds_orden</a>&nbsp;&nbsp;$ds_comentario<br>";
    }
    if($tot_entregables == 0)
      echo "(No uploads)";
    echo "</td>";
    
    # Evaluacion
    echo "
                          <td>
                            <a href='rcritique.php?student=$fl_alumno&week=$no_semana'>Record critique</a><br><br>";
    if(!empty($ds_critica_animacion))
      echo "
                            <a href='desktop.php?student=$fl_alumno&week=$no_semana&tab=critique'>View critique</a>";
    else
      echo "
                            No critique available";
    echo "
                          </td>
                          <td>";
    if(!empty($fl_promedio_semana)) {
      $Query  = "SELECT cl_calificacion, ds_calificacion, fg_aprobado FROM c_calificacion WHERE fl_calificacion=$fl_promedio_semana";
      $row4 = RecuperaValor($Query);
      $cl_calificacion = $row4[0];
      $ds_calificacion = str_uso_normal($row4[1]);
      if($row4[2] <> '1')
        $ds_aprobado = "<span class='text_unread'>Not approved</span>";
      else
        $ds_aprobado = "Approved";
      echo "$cl_calificacion $ds_calificacion<br>$ds_aprobado<br><br>";
      $ds_calificar = "Change grade";
    }
    else {
      echo "<span style='color: red;'>Pending evaluation</span><br><br>";
      $ds_calificar = "Assign grade";
    }
    echo "<a href=\"javascript:AssignGrade($fl_entrega_semanal);\">$ds_calificar</a></td>
                        </tr>
                        <tr><td colspan='5' height='10'></td></tr>";
  }
  
?>