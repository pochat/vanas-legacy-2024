<?php
  
  # Recupera los alumnos del grupo
  $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
  $Query  = "SELECT a.fl_alumno, ".ConcatenaBD($concat)." 'ds_nombre' ";
  $Query .= "FROM k_alumno_grupo a, c_usuario b ";
  $Query .= "WHERE a.fl_alumno=b.fl_usuario ";
  $Query .= "AND a.fl_grupo=$fl_grupo ";
  $Query .= "AND NOT EXISTS (SELECT 1 FROM k_entrega_semanal c, k_entregable d WHERE c.fl_entrega_semanal=d.fl_entrega_semanal AND c.fl_alumno=a.fl_alumno AND c.fl_semana=$fl_semana) ";
  $Query .= "AND NOT EXISTS (SELECT 1 FROM k_entrega_semanal e WHERE e.fl_alumno=a.fl_alumno AND e.fl_semana=$fl_semana AND e.fl_promedio_semana IS NOT NULL) ";
  $Query .= "ORDER BY ds_nombre";
  $rs4 = EjecutaQuery($Query);
  while($row4 = RecuperaRegistro($rs4)) {
    $fl_alumno = $row4[0];
    $ds_nombre = str_uso_normal($row4[1]);
    $fg_entregado = '0';
    $ds_status = "<span style='color: red;'>Pending upload</span>";
    
    # Inserta los datos de la entrega semanal si no existen aun
    $Query  = "SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana";
    $row = RecuperaValor($Query);
    $fl_entrega_semanal = $row[0];
    if(empty($fl_entrega_semanal)) {
      $Query  = "INSERT INTO k_entrega_semanal (fl_alumno, fl_grupo, fl_semana) ";
      $Query .= "VALUES($fl_alumno, $fl_grupo, $fl_semana)";
      $fl_entrega_semanal = EjecutaInsert($Query);
    }
    
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
    
    /* MRA 3 oct 2014: Ahora los maestros pueden calificar aunque no este completo el trabajo del estudiante
    # A los alumnos que no han entregado solo se les puede calificar si ya paso la fecha limite de entrega
    if($no_dias < 0)
      echo "<a href=\"javascript:AssignGrade($fl_entrega_semanal);\">$ds_calificar</a>";
    else
      echo "On time for upload";
    */
    if($no_dias >= 0)
      echo "On time for upload<br>";
    echo "<a href=\"javascript:AssignGrade($fl_entrega_semanal);\">$ds_calificar</a>";
    echo "</td>
                        </tr>
                        <tr><td colspan='5' height='10'></td></tr>";
  }
  
?>