<?php

 //echo"<div id='presenta_rub' name='presenta_rub'></div>";
 
 echo"<style>
            div.dataTables_filter {
                top: -44px !important;
				 position: ineri inherit;
            }
	
   
        </style>";
 
  
  $fe_actual = ObtenFechaActual( );
  $Query  = "SELECT a.fl_grupo, a.nb_grupo, b.fl_semana, DATE_FORMAT(b.fe_entrega, '%c') 'fe_entrega_m', DATE_FORMAT(b.fe_entrega, '%e, %Y') "; 
  $Query .= "'fe_entrega_da', DATE_FORMAT(b.fe_calificacion, '%c') 'fe_calificacion_m', DATE_FORMAT(b.fe_calificacion, '%e, %Y') 'fe_calificacion_da', ";
  $Query .= "c.no_grado, c.no_semana, c.ds_titulo, c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch, d.nb_programa, ";
  $Query .= "DATEDIFF(b.fe_entrega, '".$fe_actual."') no_dias, d.ds_tipo ds_tipo_programa ";
  $Query .= "FROM c_grupo a LEFT JOIN k_semana b ON(b.fl_term = a.fl_term) LEFT JOIN c_leccion c ON(c.fl_leccion = b.fl_leccion) ";
  $Query .= "LEFT JOIN c_programa d ON(d.fl_programa = c.fl_programa) ";
  $Query .= "WHERE a.fl_maestro=".$fl_maestro." AND (c.fg_animacion = '1' OR c.fg_ref_animacion = '1' OR c.no_sketch > 0 OR c.fg_ref_sketch = '1') ";
  $Query .= "AND c.no_semana <= (SELECT MAX(f.no_semana) FROM k_semana e, c_leccion f WHERE e.fl_leccion = f.fl_leccion ";
  $Query .= "AND TO_DAYS(e.fe_publicacion) <= TO_DAYS('".$fe_actual."') AND f.fl_programa = c.fl_programa AND f.no_grado = c.no_grado AND "; $Query .= "e.fl_term = a.fl_term) ";
  $Query .= "AND EXISTS (SELECT 1 FROM k_alumno_grupo g
  JOIN c_usuario h ON(h.fl_usuario=g.fl_alumno) WHERE h.fg_activo = '1' AND g.fl_grupo =  a.fl_grupo) AND a.no_alumnos>0 ";  
  
  $Query .= "ORDER BY d.no_orden, c.no_grado, c.no_semana DESC, a.nb_grupo ";
  $rs = EjecutaQuery($Query);
  $tot_grupos = CuentaRegistros($rs);
  $contador=200;
   while($row=RecuperaRegistro($rs)) {
      $contador++;
   
              # Verirficamos si el usuario esta activo y ya entrego su trabajos para los tabs ASSIGMENT TO GRADE AND GRADING HISTORY
              $Query3  = "SELECT 1 FROM k_entrega_semanal i LEFT JOIN k_entregable k ON(k.fl_entrega_semanal=i.fl_entrega_semanal) ";
              $Query3 .= "JOIN c_usuario j ON(j.fl_usuario=i.fl_alumno) WHERE  i.fl_grupo = ".$row[0]." AND i.fl_semana = ".$row[2]." ";
              $Query3 .= "AND i.fl_promedio_semana IS NOT NULL ";
			  $row3 = RecuperaValor($Query3);
			  if(!empty($row3[0])){
			  
				$fl_grupo = $row[0];
				$nb_grupo = str_uso_normal($row[1]);
				$fl_semana = $row[2];
				$fe_entrega = ObtenNombreMes($row[3])." ".$row[4];
				$fe_calificacion = ObtenNombreMes($row[5])." ".$row[6];
				$no_grado = $row[7];
				$no_semana = $row[8];
				$ds_titulo = str_uso_normal($row[9]);
				$fg_animacion = $row[10];
				$fg_ref_animacion = $row[11];
				$no_sketch = $row[12];
				$fg_ref_sketch = $row[13];
				$nb_programa = str_uso_normal($row[14]);
				$no_dias = $row[15];
				$ds_tipo_programa = $row[16];
				
				
				
				
				# Requerimientos de la leccion
				$ds_animacion = "No assignment";
				if($fg_animacion == '1')
				  $ds_animacion = "Assignment";
				$ds_ref_animacion = "No assignment reference";
				if($fg_ref_animacion == '1')
				  $ds_ref_animacion = "Assignment reference";
				if($no_sketch == '0')
				  $ds_sketch = "No sketches";
				elseif($no_sketch == '1')
				  $ds_sketch = "1 sketch";
				else
				  $ds_sketch = "$no_sketch sketches";
				$ds_ref_sketch = "No sketch reference";
				if($fg_ref_sketch == '1')
				  $ds_ref_sketch = "Sketch reference";
				
					# Inicia bloque de Programa - Grado
					 if($nb_programa <> $nb_programa_ant OR $no_grado <> $no_grado_ant OR $no_semana <> $no_semana_ant) {
					
						// echo "
							// <tr><td colspan='5' class='text-center' style='font-size:16px; font-weight:600;'>$nb_programa, Term $no_grado<br>Week $no_semana: $ds_titulo</td></tr>
							// <tr>
							  // <td colspan='5' class='text-center'>
								// Submission due date is <b>$fe_entrega</b>, Evaluation due date is <b>$fe_calificacion</b>
								// <br>
								// <b>This lesson requires:</b> $ds_animacion, $ds_ref_animacion, $ds_sketch, $ds_ref_sketch
							  // </td>
							// </tr>";
						echo"<p class='text-center' style='font-size:16px; font-weight:600;'>$nb_programa, Term $no_grado<br>Week $no_semana: $ds_titulo </p>";	
					    echo"<p class='text-center' >Submission due date is <b>$fe_entrega</b>, Evaluation due date is <b>$fe_calificacion</b> <br> <b>This lesson requires:</b> $ds_animacion, $ds_ref_animacion, $ds_sketch, $ds_ref_sketch </p>  ";
					
						  $nb_programa_ant = $nb_programa;
						  $no_grado_ant = $no_grado;
						  $no_semana_ant = $no_semana;
					
					}
					 # Inicia bloque de Grupo
                     //echo "<tr><td colspan='5' class='text-center' style='font-size:14px; font-weight:600;'>Group $nb_grupo</td></tr>";
					 echo "<p class='text-center' style='font-size:14px; font-weight:600;'>Group $nb_grupo</p><hr><br/><br/><br/>";
					
					
					
				    	

						
											  # Recupera asignaciones pendientes por calificar
											  $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
											  $Query  = "SELECT a.fl_entrega_semanal, a.fl_alumno, a.fg_entregado, a.ds_critica_animacion, a.fl_promedio_semana, ";
											  $Query .= ConcatenaBD($concat)." 'ds_nombre' ";
											  $Query .= "FROM k_entrega_semanal a LEFT JOIN c_usuario b ON(a.fl_alumno=b.fl_usuario) ";
											  $Query .= "WHERE a.fl_grupo=$fl_grupo ";
											  $Query .= "AND a.fl_semana=$fl_semana ";
											  $Query .= "AND a.fl_promedio_semana IS NOT NULL ";
											  $Query .= "ORDER BY ds_nombre";
											  $rs2 = EjecutaQuery($Query);
											  $tot_registros = CuentaRegistros($rs2);
											   
													#Presentamos los datatables por cada grupo
													echo "<tr><td colspan='5' class='text-center' style='font-size:14px; font-weight:600;'>";
											 if($tot_registros>0){


												echo"<article class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>  ";
												echo"	<div class='jarviswidget' id='id_m5' data-widget-editbutton='false' data-widget-colorbutton='false' data-widget-togglebutton='false'  >";
												echo" 		<header> <span class='widget-icon'><i class='fa fa-calendar'></i></span><h2><strong>Student list</strong></h2> </header>";
												echo"<!---ini div---><div style='padding-top:50px;'>";
												echo"			<div class='jarviswidget-editbox'><!--- content que tiene el boton searh-----></div> ";								



											 
													echo"
																	<div class='panel-body no-padding'>
																	  <div class='col-sm-12 col-md-12 col-lg-12 no-padding'  style='padding-top: 50px;'>
																	 
																		<table class='table table-bordered table-condensed' style='position: ineri inherit;	' width='100%' id='tbl2_".$contador."'>
																		  <thead>
																			<th class='text-center'>".ObtenEtiqueta(2040)."</th>
																			<th class='text-center'>".ObtenEtiqueta(2041)."</th>
																			<th class='text-center'>".ObtenEtiqueta(2042)."</th>
																			<th class='text-center'>".ObtenEtiqueta(2043)."</th>
																			<th class='text-center'>".ObtenEtiqueta(2044)."</th>
																			
																		  </thead>
																		  <tbody>
														";				  
											  
											}
											  
											  
											  
											  while($row2 = RecuperaRegistro($rs2)) {
											  $contador2++;
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
    
						
						
						
						
						
						
						
					 echo"
						
										  <tr>
											  <td class='text-center'><a href='#ajax/profile_student.php?profile_id=$fl_alumno' title='View $ds_nombre profile'><img src='".ObtenAvatarUsuario($fl_alumno)."'></a></td>
											  <td class='text-center'><a href='#ajax/desktop.php?student=$fl_alumno&week=$no_semana' title='View $ds_nombre desktop'>$ds_nombre</a>
												  <br><br>
												  $ds_status<br />
										      </td>
											  <td>	";
												  
												  
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
																  echo"<a href='#ajax/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab'>$ds_tipo$ds_orden</a>&nbsp;&nbsp;$ds_comentario<br>";

													}						
													if($tot_entregables == 0)
														echo "(No uploads)";					  
												  
					echo"							  
											  </td>";
				   #Evaluacion
					echo"						  
											  <td class='text-center'>
													  <a href='#ajax/rcritique.php?student=$fl_alumno&week=$no_semana'>Record critique</a><br><br>
													  ";
													  if(!empty($ds_critica_animacion))
														  echo "<a href='#ajax/desktop.php?student=$fl_alumno&week=$no_semana&tab=critique'>View critique</a>";
													  else
														  echo "No critique available";
											  
				   echo"							  
											  </td>";
						

											   
			
				  echo "	 <td class='text-left'>
				  
				  ";					  
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
											  
											  if($fg_nuevo_alumno==1){
											  
														 if(!empty($lesson_have_rubric))
																 echo"<a class='btn btn-primary'  OnClick='VerCalificacion$contador();'><i class='fa fa-pencil'></i> ".ObtenEtiqueta(1975)."</a> ";
															else
																 echo "<a href='javascript:void(0);' onclick='AssignGrade($fl_entrega_semanal, \"$tab\");'>$ds_calificar</a><br />"; #SE COLOCA EL BOTON VIEJITO Y QUE vanas no tiene planeado terminar rubridcs 
			    
											  
											  
											  }else{
											  
											       echo "<a href='javascript:void(0);' onclick='AssignGrade($fl_entrega_semanal, \"$tab\");'>$ds_calificar</a><br />";
											  
											  }
											 
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
											 
											 
											  
											  
											  
				  echo"							  </td>
											
											  
										  </tr>
						";				  
							}	


					if($tot_registros>0){
							
					echo"					 
										  </tbody>
										</table>
									
								</div>
								</div>";
								
								echo"			
									<script>
									$(document).ready(function(){
									  $('#tbl2_".$contador."').dataTable({   'bSort': true, 'bLengthChange': true, 'bPaginate': true,iDisplayLength:10});
									});
									</script>
								";
								
								
					echo"		</div><!--end div--->
							</div><!--- end jarviswidget---->
						</article>";									
								
					}			
								
								
					
					echo"
						</td></tr>";	

				
			       #end 
			  
			  }
   
   
   
   }
 
?>