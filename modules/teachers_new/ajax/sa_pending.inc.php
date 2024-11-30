<?php
  
  # Recupera asignaciones pendientes por calificar
  $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
  $Query  = "SELECT a.fl_entrega_semanal, a.fl_alumno, a.fg_entregado, a.ds_critica_animacion, a.fl_promedio_semana, ";
  $Query .= ConcatenaBD($concat)." 'ds_nombre' ";
  $Query .= "FROM k_entrega_semanal a LEFT JOIN c_usuario b ON(a.fl_alumno=b.fl_usuario) ";
  $Query .= "WHERE a.fl_grupo=$fl_grupo ";
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
      $ds_status = "<span class='text-danger'>Pending upload</span>";
    
	
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
          $ds_status<br />

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
    
    $contador ++;
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
	
	#Verificamos si la leccion tiene rubric para presentar el boton de asigmen
	$lesson_have_rubric=TieneRubricLeccionCampus($fl_leccion,$no_sem);
	
	
	
	if($no_tab==2){
			if($contador == 1 ){
					echo"<div id='presenta_rub' name='presenta_rub'></div>";
             } 
	}

   
	if($fg_nuevo_alumno==1){
        		
		  if($no_tab==1){
		  
				if(!empty($lesson_have_rubric))
					echo"<a class='btn btn-primary'  OnClick='AsignarCalificacion$contador();'><i class='fa fa-pencil'></i> ".ObtenEtiqueta(1976)."</a> ";
				else
					echo"<a href='javascript:void(0);' onclick='AssignGrade($fl_entrega_semanal, \"$tab\");'>$ds_calificar</a><br />";#SE COLOCA EL BOTON VIEJITO Y QUE vanas no tiene planeado terminar rubridcs 
				
		    
			
			echo"
			
			<script>
			   function AsignarCalificacion$contador(){
			   
				   $('#presenta_calificacion').empty();
				   $('#tab_3').removeClass('hidden');
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
            
            
            
            
        }
		

		
        if($no_tab==2){
        
		        if(!empty($lesson_have_rubric))
		             echo"<a class='btn btn-primary'  OnClick='VerCalificacion$contador();'><i class='fa fa-pencil'></i> ".ObtenEtiqueta(1975)."</a> ";
				else
                     echo "<a href='javascript:void(0);' onclick='AssignGrade($fl_entrega_semanal, \"$tab\");'>$ds_calificar</a><br />"; #SE COLOCA EL BOTON VIEJITO Y QUE vanas no tiene planeado terminar rubridcs 
			    
		        echo"
			
			    <script>
			       function VerCalificacion$contador(){
			            $('#presenta_calificacion').empty();
						$('#tab_3').removeClass('hidden');
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
                       var no_semana=$no_semana;
				       var fl_programa=$fl_programa;
				       var no_grado=$no_grad;
				       var fg_calificado=1;
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
                                      '&fg_calificado='+fg_calificado+
								      '&fl_programa='+fl_programa,
																						  
							    async: false,
							    success: function (html) {
								     $('#presenta_calificacion').html(html);
                                
							    }
					    });
				   
			       } 
			    </script>
			
			    ";
            
            
            
        
        }
        
      
 
  
    #Presenta antigua forma para     
	}else{
    	echo "<a href='javascript:void(0);' onclick='AssignGrade($fl_entrega_semanal, \"$tab\");'>$ds_calificar</a><br />";
	}


    # Obtenemos la asistenca s existe la mostrar y sino tendra que asignarla
    $Query_asi  = "SELECT  a.cl_estatus_asistencia,d.nb_estatus, DATE_FORMAT(c.fe_clase,'%M %d, %Y') FROM k_live_session_asistencia a, k_live_session b, k_clase c, c_estatus_asistencia d ";
    $Query_asi .= "WHERE a.fl_live_session = b.fl_live_session AND b.fl_clase=c.fl_clase  AND a.cl_estatus_asistencia=d.cl_estatus_asistencia ";
    $Query_asi .= "AND c.fl_semana=$fl_semana  AND a.fl_usuario=$fl_alumno ";
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
        # Si el programa requiere que le asignen una asistencia y es de tipo on site or Online
        if($ds_tipo_programa=='On-Site' OR $ds_tipo_programa=='Online')
          echo "<br /><br />".$attandance;
      }
    }
    echo "
      </td>
    </tr>";
  }  
?>