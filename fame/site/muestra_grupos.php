<?php 
  # Libreria de funciones	
  require("../lib/self_general.php");
 
 

  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp');
  $fg_tipo=RecibeParametroHTML('fg_tipo');
  
  
  
  #Recuperamos el nombre dle programa.
  $Query="select nb_programa,nb_thumb from c_programa_sp where fl_programa_sp=".$fl_programa_sp." ";
  $row=RecuperaValor($Query);
  $nb_programa_sp=$row[0];
  $nb_thumb = str_texto($row[1]);

 $img = PATH_HOME."/modules/fame/uploads/".$nb_thumb;


  
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.mikel_mkl{
	margin-top:30px;
	
}
@media only screen and (max-width: 600px) {
  .mikel_mkl {
    margin-top: 45px !important;
  }
}
</style>

<!-- Button trigger modal -->
<!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button>-->

<!-- Modal -->
<div class="modal fade" id="exampleModalGroups" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
	    <img src="<?php echo $img; ?>" alt="" style="padding-right:5px;max-width:50px;float:left;" class="img-responsive">
        <h5 class="modal-title" style="padding-top: 8px;" id="exampleModalLabel2"> <?php echo $nb_programa_sp;?></h5><br>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -33px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  
	    <div class="row">

<?php 

if($fg_tipo=='G'){	

?>

						
			<div class="bs-example">
				<div class="panel-group" id="accordion">
				
					<div class="panel panel-default">
					
							 <?php 
							 
						 
							 
							 
								 #Recuperamos el grupos
								 $Queryg  = "SELECT nb_grupo FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
								 $Queryg .= "LEFT JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) ";
								 $Queryg .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." ";
								 $Queryg .= "AND b.fl_programa_sp=".$fl_programa_sp." AND nb_grupo<>'' GROUP BY c.nb_grupo ";
								 $rsg = EjecutaQuery($Queryg);                      
								 $tot_groups = CuentaRegistros($rsg);
								 $contador_id_collapse=0;
								 for($m=0;$rowm=RecuperaRegistro($rsg);$m){
								 $nb_grupo = str_texto($rowm[0]);
							 
								 $contador_id_collapse++;
								 
											 
											 
											 # Obtenemos alumnos de este programa en este curso
											$Queryj = "SELECT fl_alumno_sp, ds_nombres, nb_grupo, nb_programa, fg_activo, fe_ultacc, ds_progreso, no_promedio_t FROM ( ";
											$Queryj .= "(SELECT a.fl_alumno_sp, CONCAT(ds_nombres,' ', ds_apaterno) ds_nombres, a.nb_grupo, d.nb_programa, c.fg_activo, ";
											$Queryj .= "DATE_FORMAT(fe_ultacc, '%Y-%m-%d %H:%i:%s') fe_ultacc, b.ds_progreso, b.no_promedio_t ";
											$Queryj .= "FROM c_alumno_sp a LEFT JOIN c_usuario c ON(c.fl_usuario=a.fl_alumno_sp) ";
											$Queryj .= "LEFT JOIN k_usuario_programa b ON(b.fl_usuario_sp=a.fl_alumno_sp) LEFT JOIN c_programa_sp d ON(d.fl_programa_sp=b.fl_programa_sp) ";
											$Queryj .= "WHERE nb_grupo='".$nb_grupo."' AND b.fl_programa_sp=".$fl_programa_sp." AND c.fl_instituto=".$fl_instituto." ) UNION ";
											$Queryj .= "(SELECT a.fl_alumno_sp, CONCAT(ds_nombres,' ', ds_apaterno) ds_nombres, 'Unassigned' nb_grupo, 'Unassigned' nb_programa, c.fg_activo, ";
											$Queryj .= "DATE_FORMAT(fe_ultacc, '%Y-%m-%d %H:%i:%s') fe_ultacc, '0' ds_progreso, '0' no_promedio_t ";
											$Queryj .= "FROM c_alumno_sp a LEFT JOIN c_usuario c ON(c.fl_usuario=a.fl_alumno_sp) ";
											$Queryj .= "WHERE NOT EXISTS (SELECT 1 FROM k_usuario_programa d WHERE d.fl_usuario_sp= c.fl_usuario) AND a.nb_grupo='' AND c.fl_instituto=".$fl_instituto." ) ";
											$Queryj .= ") as students ";                            
											$rsj = EjecutaQuery($Queryj);
											$rsx = EjecutaQuery($Queryj);
											$tot_alumnos_grupo = CuentaRegistros($rsj);
											$no_unassigned=0;
											$no_assigned=0;
											for($x=0;$rowx=RecuperaRegistro($rsx);$x++){
											  $asi_course = $rowx[2];
											  $asi_group = $rowx[3];
											  if($asi_course=="Unassigned" && $asi_group=="Unassigned")
												$no_unassigned++;
											  else
												$no_assigned++;
											}        
			
							 ?>
					
					
								<div class="panel-heading" style="margin-bottom: 1px;">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne_<?php echo $contador_id_collapse;?>"><i class="fa fa-lg fa-fw fa-plus-circle txt-color-green pull-left" style="padding-top:5px;" aria-hidden="true"></i><strong><?php echo $nb_grupo; ?> </strong>has <?php echo $tot_alumnos_grupo." students ( ".$no_assigned." - ".$no_unassigned.") unassigned"; ?></a>
									</h4>
								</div>
								<div id="collapseOne_<?php echo $contador_id_collapse;?>" class="panel-collapse collapse ">
									<div class="panel-body">
										
										
										 <table class='table table-bordered table-condensed padding-10' id='tbl_usergupo' >
											<thead>
											  <tr>
												<th></th>
												<th><?php echo ObtenEtiqueta(1054); ?></th>
												<th><?php echo ObtenEtiqueta(1055); ?></th>
												<th><?php echo ObtenEtiqueta(1075); ?></th>
												<th><?php echo ObtenEtiqueta(1217); ?></th>
												<th ><?php echo ObtenEtiqueta(1057); ?></th>
												<th><?php echo ObtenEtiqueta(1150); ?></th>
												<th style="width:9%;"><?php echo ObtenEtiqueta(1077); ?></th>
												<th><?php echo ObtenEtiqueta(1078); ?></th>
											  </tr>
											</thead>
										    <tbody>
											
									<?php	
										  for($j=0;$rowj=RecuperaRegistro($rsj);$j++){
												  $tot_alumnos_grupo = $tot_alumnos_grupo;
												  $fl_alumno_sp = $rowj[0];                                
												  $ds_nombres = str_texto($rowj[1]);
												  $nb_grupo_p = str_texto($rowj[2]);
												  $nb_programa_p = str_texto($rowj[3]);
												  $fg_status = $rowj[4];
												  if(!empty($fg_status)){
													$status = ObtenEtiqueta(1041);
													$color_sts = "success";
												  }
												  else{
													$status = ObtenEtiqueta(1042);
													$color_sts = "danger";
												  }
												  $fe_ultacc = $rowj[5];
												  $fe_ultacc = time_elapsed_string($fe_ultacc, true);
												  $ds_progreso = $rowj[6];
												  $no_promedio_t = $rowj[7];
												  # Obtenemos el GPA
												  $Queryp = "SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio_t) AND no_max >= ROUND($no_promedio_t)";
												  $prom_t = RecuperaValor($Queryp);
												  $cl_calificacion = $prom_t[0];
												  $fg_aprbado_grl = $prom_t[1];
												  if(!empty($fg_aprbado_grl))
													$color_gpa = "success";
												  else
													$color_gpa = "danger";
												  if(!empty($ds_progreso))
													$gpa = "<span class='label label-".$GPA."' style='padding: 3px;'>".$cl_calificacion." (".$no_promedio_t."%)</span>";
												  else
													$gpa = ObtenEtiqueta(1039);




									?>
											
												<tr>
												    <td style="width: 3%;">
													<?php 
													    if($nb_grupo_p!='Unassigned' && $nb_programa!='Unassigned'){
														  echo"
														  <label class='checkbox no-padding no-margin'>
															<input class='checkbox' disabled id='ch_".$fl_alumno_sp."' value='".$fl_alumno_sp."' type='checkbox'><span style='left:20px;'></span>
														  </label>";
														}
														else{
														   echo"
														  <label class='checkbox no-padding no-margin'>
															<input class='checkbox' id='ch_".$fl_alumno_sp."' value='".$fl_alumno_sp."' type='checkbox' 
															onchange=\"Assign_Grp_Crs($fl_alumno_sp, $fl_programa_sp_i, '$nb_grupo');\">
															<span style='left:20px;'></span>
														  </label>";
														}
													
													?>
													</td>
													<td><?php 
													      echo"
															<div class='project-members'>                                      
																<a href='javascript:void(0)' rel='tooltip' data-placement='top' data-html='true' data-original-title='".$ds_nombres."'>
																  <img src='".ObtenAvatarUsuario($fl_alumno_sp)."' class='online' alt='".$ds_nombres."' style='width:25px;'>
																</a>
															  </div>
															";
													?></td>
													<td><?php echo $ds_nombres;?></td>
													<td><?php echo $nb_grupo_p;?></td>
													<td><?php echo $nb_programa_p;?></td>
													<td  class="text-center"><span class="label label-<?php echo $color_sts;?>" style="padding: 3px;"><?php echo$status;?></span></td>
													<td><?php echo $fe_ultacc;?></td>
													<td style="width:9%;"><div class="progress progress-xs" data-progressbar-value="<?php echo$ds_progreso;?>"><div class="progress-bar"></div></div></td>
													<td><?php echo $gpa;?></td>
												</tr>
											
											
											
										         <?php } ?>
											</tbody>
										
											</table>
										
										
									</div>
								
								</div>
								
								
								
								<?php
								 }






								 
								?>
		
						
					</div>
					
					
				</div>
				
			</div>
						

			
			
	
	
	<?php
}//end tipo G
if($fg_tipo=='L'){
	
?>	
		<div class="col-md-12">
			<div class="table-responsive">
			<table class='table table-bordered table-condensed'>

					<thead>
						<tr>
						  <th><center><?php echo ObtenEtiqueta(1230);?></center></th>
						  <th><center><?php echo ObtenEtiqueta(1234);?></center></th>
						  <th><center><?php echo ObtenEtiqueta(1297);?></center></th>
						  <th><center><?php echo ObtenEtiqueta(1219);?></center></th>
						  <th><center><?php echo ObtenEtiqueta(1252);?></center></th>
						</tr>
					</thead>
					<tbody>
					
					
					<?php
					
					  # Query para las lecciones de cada programa
					  $Query_l  = " SELECT fl_leccion_sp, no_semana, ds_titulo, a.ds_vl_duracion, a.ds_tiempo_tarea,a.ds_learning ";
					  $Query_l .= " FROM c_leccion_sp a, c_programa_sp b, k_programa_detalle_sp c ";
					  $Query_l .= " WHERE a.fl_programa_sp = $fl_programa_sp AND a.fl_programa_sp = b.fl_programa_sp  AND a.fl_programa_sp = c.fl_programa_sp ";
					  $Query_l .= " ORDER BY no_orden, no_semana ";
					  $rs_l = EjecutaQuery($Query_l);
					  for($i_l=0;$row_l=RecuperaRegistro($rs_l);$i_l++) {
						$fl_leccion_sp = $row_l[0];
						$no_semana = $row_l[1];
						$ds_titulo = str_texto($row_l[2]); 
						$ds_vl_duracion = $row_l[3];
						$ds_tiempo_tarea = str_texto($row_l[4]); 
						$ds_learning_2 = str_texto($row_l[5]);

					?>

					    <tr>
						  <td><center><?php echo $no_semana;?></center></td>
						  <td><?php echo $ds_titulo;?></td>
						  <td><?php echo $ds_learning_2;?></td>
						  <td><center><?php echo $ds_vl_duracion." ".ObtenEtiqueta(1240);?></center></td>
						  <td><center><?php echo $ds_tiempo_tarea;?></center></td>

						</tr>


					<?php
					  }						
					
					?>
					
					
					
					
					
					</tbody>


			</table>
			</div>
		</div>
	

<?php
	
}

	?>		
			
			
			
			
			
			
			
			
			
	
			
			
			
		
		</div><!--end row-->
		
		
	  
	  
        
		

		
		
      </div>
      <div class="modal-footer text-center">
        
		
					<button class="btn btn-primary buttonload" data-dismiss="modal" >
						&nbsp; Close
					</button>
	
		
		
      </div>
    </div>
  </div>
</div>





<script>

$('#exampleModalGroups').modal('show');




</script>

<?php

?>



