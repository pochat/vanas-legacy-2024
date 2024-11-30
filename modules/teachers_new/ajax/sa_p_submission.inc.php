<?php
  
  # Recupera los alumnos del grupo
  $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
  $Query  = "SELECT a.fl_alumno, ".ConcatenaBD($concat)." 'ds_nombre' ";
  $Query .= "FROM k_alumno_grupo a LEFT JOIN c_usuario b ON(a.fl_alumno=b.fl_usuario) ";
  $Query .= "WHERE a.fl_grupo=$fl_grupo ";
  $Query .= "AND NOT EXISTS (SELECT 1 FROM k_entrega_semanal c JOIN k_entregable d ON(c.fl_entrega_semanal=d.fl_entrega_semanal) WHERE c.fl_alumno=a.fl_alumno AND c.fl_semana=$fl_semana) ";
  $Query .= "AND NOT EXISTS (SELECT 1 FROM k_entrega_semanal e WHERE e.fl_alumno=a.fl_alumno AND e.fl_semana=$fl_semana AND e.fl_promedio_semana IS NOT NULL) ";
  $Query .= "ORDER BY ds_nombre";
  $rs4 = EjecutaQuery($Query);
  while($row4 = RecuperaRegistro($rs4)) {
    $fl_alumno = $row4[0];
    $ds_nombre = str_uso_normal($row4[1]);
    $fg_entregado = '0';
    $ds_status = "<span class='text-danger'>Pending upload</span>";
    $contador ++;
    # Inserta los datos de la entrega semanal si no existen aun
    $Query  = "SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana";
    $row = RecuperaValor($Query);
    $fl_entrega_semanal = $row[0];
    if(empty($fl_entrega_semanal)) {
      $Query  = "INSERT INTO k_entrega_semanal (fl_alumno, fl_grupo, fl_semana) ";
      $Query .= "VALUES($fl_alumno, $fl_grupo, $fl_semana)";
      $fl_entrega_semanal = EjecutaInsert($Query);
    }
    
	
	#Identficamos si es un alumno nuevo.
	$rown=RecuperaValor("SELECT fg_nuevo FROM c_usuario WHERE fl_usuario=$fl_alumno ");
	$fg_nuevo_alumno=$rown[0];
	
	
    # Inicia registro
    echo "
      <tr>
        <td width='80' class='text-center'>
          <a href='#ajax/profile_student.php?profile_id=$fl_alumno' title='View $ds_nombre profile'><img src='".ObtenAvatarUsuario($fl_alumno)."'></a>
        </td>
        <td class='text-center'>
          <a href='#ajax/desktop.php?student=$fl_alumno&week=$no_semana' title='View $ds_nombre desktop'>$ds_nombre</a>
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
      echo "<a href='#ajax/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab'>$ds_tipo$ds_orden</a>&nbsp;&nbsp;$ds_comentario<br>";
    }
    if($tot_entregables == 0)
      echo "(No uploads)";
    echo "</td>";
    
    # Evaluacion
    echo "
      <td>
        <a href='#ajax/rcritique.php?student=$fl_alumno&week=$no_semana'>Record critique</a><br><br>";
    if(!empty($ds_critica_animacion))
      echo "<a href='#ajax/desktop.php?student=$fl_alumno&week=$no_semana&tab=critique'>View critique</a>";
    else
      echo "No critique available";
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
      echo "<span class='text-danger'>Pending evaluation</span><br><br>";
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

	  
	#Recupermaos la leccion
	$Queryk="SELECT fl_leccion FROM k_semana WHERE fl_semana=$fl_semana ";
	$rowk=RecuperaValor($Queryk);
	$fl_leccion=$rowk['fl_leccion'];
	
	#Recupermaos el programa
	$Queryp="SELECT fl_programa,no_grado,no_semana FROM c_leccion WHERE fl_leccion=$fl_leccion ";
	$rowp=RecuperaValor($Queryp);
	$fl_programa=$rowp[0];
	$no_grad=$rowp[1];
	$no_sem=$rowp[2];  
	  
	  
	  
	if($fg_nuevo_alumno==1){ 
	
	        if($contador == 1 ){
			    echo"<div id='presenta_rubics' name='presenta_rubics'></div>";
            }
	
	        echo"<a class='btn btn-primary'  OnClick='AsignarCalificaciones$contador();'><i class='fa fa-pencil'></i> ".ObtenEtiqueta(1976)."</a> ";
	
	        echo"
			
				<script>
				   function AsignarCalificaciones$contador(){
				   
					    $('#presenta_calificacion').empty();
						$('#presenta_calificacion').empty();
					    $('#tab_0').removeClass('active');
						$('#tab_1').removeClass('active');
						$('#tab_2').removeClass('active');
						$('#tab_3').addClass('active');
						
						$('#p_grade').removeClass('active');
						$('#p_incomplete').removeClass('active');
						$('#p_history').removeClass('active');
						$('#p_assignment_grade').addClass('active');

					   var fl_entrega_semanal=$fl_entrega_semanal;
					   var fl_alumno=$fl_alumno;
					   var fl_leccion=$fl_leccion;
					   var fl_grupo=$fl_grupo;
					   var fl_semana=$fl_semana;
					   var fl_programa=$fl_programa;
					   var no_grado=$no_grad;
					   var no_semana=$no_semana;
					   
					   
						$.ajax({
								type: 'POST',
								url: 'ajax/presenta_rubric.php',
								data: 'fl_alumno='+fl_alumno+
									  '&fl_leccion='+fl_leccion+
									  '&fl_semana='+fl_semana+
									  '&no_semana='+no_semana+
									  '&fl_entrega_semanal='+fl_entrega_semanal+
									  '&fl_grupo='+fl_grupo+
									  '&no_grado='+no_grado+
									  
									  '&fl_programa='+fl_programa,
																							  
								async: false,
								success: function (html) {
									 $('#presenta_calificacion').html(html);
									
								}
						});
					   
					   
					   
				   } 
				</script>
				
				";
	
	}else{
	  
    echo "
    <a href='javascript:void(0);' onclick='AssignGrade($fl_entrega_semanal,\"$tab\");'>$ds_calificar</a><br />";
	
	}
	
	
	
    
    # Obtenemos la asistenca s existe la mostrar y sino tendra que asignarla
    $Query_asi  = "SELECT  a.cl_estatus_asistencia,d.nb_estatus, DATE_FORMAT(c.fe_clase,'%M %d, %Y') FROM k_live_session_asistencia a, k_live_session b, k_clase c, c_estatus_asistencia d ";
    $Query_asi .= "WHERE a.fl_live_session = b.fl_live_session AND b.fl_clase=c.fl_clase  AND a.cl_estatus_asistencia=d.cl_estatus_asistencia ";
    $Query_asi .= "AND c.fl_semana=$fl_semana AND a.fl_usuario=$fl_alumno";
    $rs_asi = EjecutaQuery($Query_asi);
    $asistencias = CuentaRegistros($rs_asi);
    if(empty($asistencias)){
      echo $attandance = "<br /><span class='text-danger'>Pending Assign attendace</span><br /><br /> 
          <a href='javascript:AssignAttendace($fl_entrega_semanal);'>".ObtenEtiqueta(261)."</a>";
    }
    else{
      while($row_asi = RecuperaRegistro($rs_asi)){
        $cl_estatus_asistencia = $row_asi[0];
        $nb_estatus = $row_asi[1];
        $fe_clase = $row_asi[2];
        if(!empty($cl_estatus_asistencia))
          $attandance = "<span class='text'>Timely assistance to live session ($fe_clase):</span>&nbsp;<b>".$nb_estatus."</b>";
        else{
          $attandance = "<span class='text-danger'>Pending Assign attendace</span><br><br>
          <a href='javascript:AssignAttendace($fl_entrega_semanal);'>".ObtenEtiqueta(261)."</a>";
        }

        # Si el programa requiere que le asignen una asistencia y es de tipo on site
        if($ds_tipo_programa=='On-Site' OR $ds_tipo_programa=='Online')
          echo "<br /><br />".$attandance;
      }
    }
    echo 
      "</td>
    </tr>";
  }
?>