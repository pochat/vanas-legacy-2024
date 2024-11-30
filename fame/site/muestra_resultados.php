<?php 
  # Libreria de funciones	
  require("../lib/self_general.php");
 
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  echo"
  <style>
  .nothresh{
	  color:#B63C22;
  }
  .proc_success{
	  color:#B63C22;
	  
  }
  .proc_err{
	  color:#226108;
	  
  }
  
  </style>
  ";
  
  #Recuperamos el isttituo
  $fl_instituto=ObtenInstituto($fl_usuario);
 
  $id=RecibeParametroNumerico('fl_upload');
   
    	#Recuperamos totales de la carga de school.
	#Recuperamos el proceso de carga del archivo.
	$Query="SELECT user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status 
			,TIMESTAMPDIFF(HOUR, start_time, end_time) AS hrs,
			TIMESTAMPDIFF(MINUTE, start_time, end_time) AS minutes,
			TIMESTAMPDIFF(SECOND, start_time, end_time) AS seconds,id,deleted_count,upload_file_name_log,nothresh_modified,nothresh_deleted,nothresh_added,modified_count,deleted_count,added_count  
			FROM stage_uploads WHERE id=$id ORDER BY id DESC ";
	$row=RecuperaValor($Query);
	$id=$row['id'];
	$user_id=$row['user_id'];
	$upload_file_path=$row['upload_file_path'];
	$upload_file_name=$row['upload_file_name'];
	$upload_type=$row['upload_type'];
	$upload_date=$row['upload_date'];
	$status_cd=$row['status_cd'];
	$start_time=GeneraFormatoFecha($row['start_time']);  
	$start_time_=$row['start_time'];
	$end_time=$row['end_time'];
	$proc_status=$row['proc_status'];
	$upload_time_hrs=$row['hrs'];
	$upload_time_minutes=$row['minutes'];
	$upload_time_seconds=$row['seconds'];
    $filename=$row['upload_file_name'];
	$upload_file_name_log=$row['upload_file_name_log'];
    $proc_status=$row['proc_status'];
	$nothresh_added=$row['nothresh_added'];
	$nothresh_modified=$row['nothresh_modified'];
	$nothresh_deleted=$row['nothresh_deleted'];
    $upload_count=$row['upload_count'];
    $modified_count=$row['modified_count'];
    $deleted_count=$row['deleted_count'];
    $added_count=$row['added_count'];

   
    if($proc_status==1){
		$class="proc_success";
	}else{
		
		$class="proc_err";
	}
		
    
		
    $path_filename=$upload_file_path."/".$upload_file_name_log;

    if($upload_type=="SCHOOL"){

        $fecha1 = new DateTime($start_time_);//fecha inicial
        $fecha2 = new DateTime($end_time);//fecha de cierre
		$intervalo = $fecha1->diff($fecha2);
		$runtime=$intervalo->format('%Hh %im %ss');
        
	    if($proc_status==1){
			$finish='<i class=\'fa fa-times-circle-o\' style=\'color:#B63C22;\'></i> <span style=\'color:#B63C22;\'>'.ObtenEtiqueta(2554).'</span>';
	           
		}else{
			 $finish='<i class=\'fa fa-check-circle\' style=\'color:#226108;\'></i><span style=\'color:#226108;\'> '.ObtenEtiqueta(2532).'</span>'; 
		}

        $deleted_count_school=$row['deleted_count'];

	    #Damos formato de fecha ala fecha inicio y fin del proceso (sep 29, 2016 54:00)
	    $date = date_create($start_time_);
	    $start_time_=date_format($date,'F j, Y, g:i:s a');

	    $date = date_create($end_time);
	    $end_time=date_format($date,'F j, Y, g:i:s a');



        #Recuperamos totales.
	    $Query="SELECT COUNT(*)FROM st_school WHERE operation_code='ADD' AND upload_id=$id ";
	    $row=RecuperaValor($Query);
	    $contador_insert_school=$row[0];
        
	    //$Query="SELECT COUNT(*)FROM st_school WHERE operation_code='DELETE' AND upload_id=$id ";
	    //$row=RecuperaValor($Query);
	    //$deleted_count_school=$row['delete_count'];
        
	    $Query="SELECT COUNT(*)FROM st_school WHERE operation_code='NO_CHANGE' AND upload_id=$id ";
	    $row=RecuperaValor($Query);
	    $unchanged_count_school=$row[0];
        
	    $Query="SELECT COUNT(*)FROM st_school WHERE operation_code='MODIFY' AND upload_id=$id ";
	    $row=RecuperaValor($Query);
	    $modified_count_school=$row[0];

	    #cALCULAMOS TOTALES POR RENGLON.
	    $Total_school=$contador_insert_school+$unchanged_count_school+$modified_count_school;
		
		if(!empty($nothresh_added)){
		$nothresh_added_school="<br><small class='text-muted nothresh'>".$nothresh_added."%</small>";
		$presenta_threshold_school="<br><small class='text-muted nothresh'>".ObtenEtiqueta(2553)."<small>";
		}
	    if(!empty($nothresh_modified)){
	    $nothresh_modified_school="<br><small class='text-muted nothresh'>".$nothresh_modified."%</small>";
		$presenta_threshold_school="<br><small class='text-muted nothresh'>".ObtenEtiqueta(2553)."</small>";
		}
	    if(!empty($nothresh_deleted)){
	    $nothresh_deleted_school="<br><small class='text-muted nothresh'>".$nothresh_deleted."%</small>";
		$presenta_threshold_school="<br><small class='text-muted nothresh'>".ObtenEtiqueta(2553)."</small>";
		}
		


    }


##########################rECUPERAMOS TOTALES DE TEACHERS.
    /*
	#Recuperamos el proceso de carga del archivo.
	$Query="SELECT user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status 
			,TIMESTAMPDIFF(HOUR, start_time, end_time) AS hrs,
			TIMESTAMPDIFF(MINUTE, start_time, end_time) AS minutes,
			TIMESTAMPDIFF(SECOND, start_time, end_time) AS seconds,id
			FROM stage_uploads WHERE upload_type='STUDENT' ORDER BY id DESC ";
	$row=RecuperaValor($Query);
	$id=$row['id'];
	$user_id=$row['user_id'];
	$upload_file_path=$row['upload_file_path'];
	$upload_file_name=$row['upload_file_name'];
	$upload_type=$row['upload_type'];
	$upload_date=$row['upload_date'];
	$status_cd=$row['status_cd'];
	$start_time=GeneraFormatoFecha($row['start_time']);
	$start_time_=$row['start_time'];
	$end_time=$row['end_time'];
	$proc_status=$row['proc_status'];
	
	$upload_time_hrs=$row['hrs'];
	$upload_time_minutes=$row['minutes'];
	$upload_time_seconds=$row['seconds'];
	*/

    if($upload_type=="TEACHER"){

	    $fecha1 = new DateTime($start_time_);//fecha inicial
        $fecha2 = new DateTime($end_time);//fecha de cierre
		$intervalo = $fecha1->diff($fecha2);
		$runtime=$intervalo->format('%Hh %im %ss');

	    if($proc_status==1){
			$finish='<i class=\'fa fa-times-circle-o\' style=\'color:#B63C22;\'></i> <span style=\'color:#B63C22;\'>'.ObtenEtiqueta(2554).'</span>';
	           
		}else{
			 $finish='<i class=\'fa fa-check-circle\' style=\'color:#226108;\'></i><span style=\'color:#226108;\'> '.ObtenEtiqueta(2532).'</span>'; 
		}

        $deleted_count_teacher=$row['deleted_count'];

	    #Damos formato de fecha ala fecha inicio y fin del proceso (sep 29, 2016 54:00)
	    $date = date_create($start_time_);
	    $start_time_=date_format($date,'F j, Y, g:i:s a');
        
	    $date = date_create($end_time);
	    $end_time=date_format($date,'F j, Y, g:i:s a');
        
        
        
        
        #Recuperamos totales.
	    #$Query="SELECT COUNT(*)FROM st_teachers WHERE operation_code='ADD' AND upload_id=$id ";
	    #$row=RecuperaValor($Query);
	    $contador_insert_teacher=$added_count;
        
        //$Query="SELECT COUNT(*)FROM st_teachers WHERE operation_code='DELETE' AND upload_id=$id ";
	    //$row=RecuperaValor($Query);
	    //$deleted_count_teacher=$row[0];
        
	    $Query="SELECT COUNT(*)FROM st_teachers WHERE operation_code='NO_CHANGE' AND upload_id=$id ";
	    $row=RecuperaValor($Query);
	    $unchanged_count_teacher=$row[0];
        
	    $Query="SELECT COUNT(*)FROM st_teachers WHERE operation_code='MODIFY' AND upload_id=$id ";
	    $row=RecuperaValor($Query);
	    $modified_count_teacher=$row[0];

        
        
	    #cALCULAMOS TOTALES POR RENGLON.
	    $Total_teacher=$contador_insert_teacher+$modified_count_teacher+$unchanged_count_teacher;
		
		if(!empty($nothresh_added)){
		$nothresh_added_teacher="<br><small class='text-muted nothresh'>".$nothresh_added."%</small>";
		$presenta_threshold_teacher="<br><small class='text-muted nothresh'>".ObtenEtiqueta(2553)."</small>";
		}
	    if(!empty($nothresh_modified)){
		$nothresh_modified_teacher="<br><small class='text-muted nothresh'>".$nothresh_modified."%</small>";
		$presenta_threshold_teacher="<br><small class='text-muted nothresh'>".ObtenEtiqueta(2553)."</small>";
		}
	    if(!empty($nothresh_deleted)){
		$nothresh_deleted_teacher="<br><small class='text-muted nothresh'>".$nothresh_deleted."%</small>";
		$presenta_threshold_teacher="<br><small class='text-muted nothresh'>".ObtenEtiqueta(2553)."</small";
		}
		

    }


##################################RECUPERAMOS TOTALES DE STUDENTS.
    /*
    #Recuperamos el proceso de carga del archivo.
	$Query="SELECT user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status 
			,TIMESTAMPDIFF(HOUR, start_time, end_time) AS hrs,
			TIMESTAMPDIFF(MINUTE, start_time, end_time) AS minutes,
			TIMESTAMPDIFF(SECOND, start_time, end_time) AS seconds,id
			FROM stage_uploads WHERE upload_type='TEACHER' ORDER BY id DESC ";
	$row=RecuperaValor($Query);
	$id=$row['id'];
	$user_id=$row['user_id'];
	$upload_file_path=$row['upload_file_path'];
	$upload_file_name=$row['upload_file_name'];
	$upload_type=$row['upload_type'];
	$upload_date=$row['upload_date'];
	$status_cd=$row['status_cd'];
	$start_time=GeneraFormatoFecha($row['start_time']);
	$start_time_=$row['start_time'];
	$end_time=$row['end_time'];
	$proc_status=$row['proc_status'];
		
	$upload_time_hrs=$row['hrs'];
	$upload_time_minutes=$row['minutes'];
	$upload_time_seconds=$row['seconds'];	

    */
    if($upload_type=='STUDENT'){

		$fecha1 = new DateTime($start_time_);//fecha inicial
        $fecha2 = new DateTime($end_time);//fecha de cierre
		$intervalo = $fecha1->diff($fecha2);
		$runtime=$intervalo->format('%Hh %im %ss');
		

	    if($proc_status==1){
			$finish='<i class=\'fa fa-times-circle-o\' style=\'color:#B63C22;\'></i> <span style=\'color:#B63C22;\'>'.ObtenEtiqueta(2554).'</span>';
	           
		}else{
			 $finish='<i class=\'fa fa-check-circle\' style=\'color:#226108;\'></i><span style=\'color:#226108;\'> '.ObtenEtiqueta(2532).'</span>'; 
		}

	    #Damos formato de fecha ala fecha inicio y fin del proceso (sep 29, 2016 54:00)
	    $date = date_create($start_time_);
	    $start_time_=date_format($date,'F j, Y, g:i:s a');
        
	    $date = date_create($end_time);
	    $end_time=date_format($date,'F j, Y, g:i:s a');

        $deleted_count_student=$row['deleted_count'];

        #Recuperamos totales.
	    #$Query="SELECT COUNT(*)FROM st_students WHERE operation_code='ADD' AND upload_id=$id ";
	    #$row=RecuperaValor($Query);
	    $contador_insert_std=$added_count;
        
        //$Query="SELECT COUNT(*)FROM st_students WHERE operation_code='DELETE' AND upload_id=$id ";
	    //$row=RecuperaValor($Query);
	    //$deleted_count_student=$row[0];
        
	    $Query="SELECT COUNT(*)FROM st_students WHERE operation_code='NO_CHANGE' AND upload_id=$id ";
	    $row=RecuperaValor($Query);
	    $unchanged_count_student=$row[0];
        
	    #$Query="SELECT COUNT(*)FROM st_students WHERE operation_code='MODIFY' AND upload_id=$id ";
	    #$row=RecuperaValor($Query);
	    $modified_count_student=$modified_count;



	    #cALCULAMOS TOTALES POR RENGLON.
	    $Total_student=$contador_insert_std+$unchanged_count_student+$modified_count_student;
		
		if(!empty($nothresh_added)){
		$nothresh_added_student="<br><small class='text-muted nothresh'>".$nothresh_added."%</small>";
		$presenta_threshold_student="<br><small class='text-muted nothresh'>".ObtenEtiqueta(2553)."</small>";
		}
	    if(!empty($nothresh_modified)){
		$nothresh_modified_student="<br><small class='text-muted nothresh'>".$nothresh_modified."%</small>";
		$presenta_threshold_student="<br><small class='text-muted nothresh'>".ObtenEtiqueta(2553)."</small>";
		}
	    if(!empty($nothresh_deleted)){
		$nothresh_deleted_student="<br><small class='text-muted nothresh'>".$nothresh_deleted."%</small>";
		$presenta_threshold_student="<br><small class='text-muted nothresh'>".ObtenEtiqueta(2553)."</small>";
		}
		

    }
                     
                        
?>




<div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-fw fa-lg fa-bars"></i>&nbsp;<?php echo ObtenEtiqueta(2525) ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -22px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" >
        
		
		
								  <div class="row">
                              <div class="col-xs-12 col-sm-12">
							  
							     <div class="panel panel-info" style='border-color: #bce8f1;'>
								  <div class="panel-body" style="background-color: #d9ebf7;"><b><?php echo ObtenEtiqueta(2527);?></b></div>
								  <div class="panel-footer" style="background:#fff;">

								    <div class="table-responsive">
								  
									<table class="table table-borderless">
									
										<tbody> 
											<tr>
												<th class="text-left" style="border:none;" ><?php echo ObtenEtiqueta(2528);?>:</th>
												<td class="text-left" style="border:none;"><?php echo $start_time; ?></td>
												<th class="text-left" style="border:none;"><?php echo ObtenEtiqueta(2531);?>:</b></th>
												<td class="text-left" style="border:none;"><?php echo $start_time_; ?></td>
											</tr>
											
											<tr>
												<th class="text-left" style="border:none;"><?php echo ObtenEtiqueta(2529);?>:</th>
												<td class="text-left" style="border:none;"><?php echo $runtime;?></td>
												<th class="text-left" style="border:none;"><?php echo ObtenEtiqueta(2532);?>:</th>
												<td class="text-left" style="border:none;"><?php echo $end_time;?></td>
											</tr>
											
											<tr>
												<th class="text-left" style="border:none;"><?php echo ObtenEtiqueta(2530);?>:</th>
												<td class="text-left" style="border:none;color:#226108;"><?php echo $finish;?> </td>
												<th style="border:none;"><?php echo ObtenEtiqueta(2548);?>:</th>
												<th style="border:none;"><a class="<?php echo $class;?>" href="<?php echo $path_filename;?>"><?php echo $upload_file_name_log;?></a></th>
											</tr>
										
										</tbody>
									</table>
								    </div>
								  </div>
								</div>
							  
							  
							    <div class="panel panel-info" style='border-color: #bce8f1;'>
								  <div class="panel-body" style="background-color: #d9ebf7;"><b><?php echo ObtenEtiqueta(2533);?></b></div>
								    <div class="panel-footer" style="background:#fbf7f785;">
									
										<div class="table-responsive">
										<table class="table table-striped">	

											
								

										
											<tbody> 
												<tr>
													<td></td>
													<td class="text-center"><b><?php echo ObtenEtiqueta(2534);?></b></td>
													<!--<td><b><?php echo ObtenEtiqueta(2535);?></b></td>-->
													<td class="text-center"><b><?php echo ObtenEtiqueta(2536);?></b></td>
													<td class="text-center"><b><?php echo ObtenEtiqueta(2537);?></b></td>
                                                    <td class="text-center"><b><?php echo ObtenEtiqueta(2549);?></b></td>
													<td class="text-center"><b><?php echo ObtenEtiqueta(2538);?></b></td>
												</tr>
												
												<tr>
													<td><b><?php echo ObtenEtiqueta(2539);?>
													       <?php echo $presenta_threshold_school;?>
													   </b>
													</td>
													<td class="text-center"><?php echo $contador_insert_school;
															if($proc_status==1){
																echo $nothresh_added_school;
															}
														?>
													</td>
													<!--<td><?php echo $Total_school;?></td>-->
													<td class="text-center"><?php echo $deleted_count_school;
															if($proc_status==1){
																echo $nothresh_deleted_school;
															}
														?>
													</td>
													<td class="text-center"><?php echo $unchanged_count_school;?></td>
                                                    <td class="text-center"><?php echo $modified_count_school;
														    if($proc_status==1){
																echo $nothresh_modified_school;
															}
														?>
													</td>
													<td class="text-center"><?php echo $Total_school;?></td>
												</tr>
												
												<tr>
													<td ><b><?php echo ObtenEtiqueta(2540);?>
															<?php echo $presenta_threshold_teacher;?></b>
													</td>
													<td class="text-center"><?php echo $contador_insert_teacher;
															if($proc_status==1){
															  echo $nothresh_added_teacher;
															}
														?>
													</td>
													<!--<td><?php echo $Total_teacher;?></td>-->
													<td class="text-center"><?php echo $deleted_count_teacher;
															if($proc_status==1){
															  echo $nothresh_deleted_teacher;
															}
														?>
													</td>
													<td class="text-center"><?php echo $unchanged_count_teacher;?></td>
                                                    <td class="text-center"><?php echo $modified_count_teacher;
															if($proc_status==1){
															  echo $nothresh_modified_teacher;	
															}
														?>
													
													</td>
													<td class="text-center"><?php echo $Total_teacher;?></td>
												</tr>
												
												<tr>
													<td ><b><?php echo ObtenEtiqueta(2541);?>
															<?php echo $presenta_threshold_student;?></b>
													</td>
													<td class="text-center"><?php echo $contador_insert_std;
															if($proc_status==1){
															  echo $nothresh_added_student;
															}
														?>
													</td>
													<!--<td><?php echo $Total_student;?></td>-->
													<td class="text-center"><?php echo $deleted_count_student;
															if($proc_status==1){
															  echo $nothresh_deleted_student;
															}
														?>
													</td>
													<td class="text-center"><?php echo $unchanged_count_student;?></td>
                                                    <td class="text-center"><?php echo $modified_count_student;
															if($proc_status==1){
															  echo $nothresh_modified_student;
															}
														?>
													</td>
													<td class="text-center"><?php echo $Total_student;?></td>
												</tr>
											<!---	
												<tr>
													<td><b><?php echo ObtenEtiqueta(2542);?></b></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
                                                    <td></td>
													<td></td>
												</tr>
												
												<tr>
													<td><b><?php echo ObtenEtiqueta(2543);?></b></td>
													<td></td>
													<td></td>
                                                    <td></td>
													<td></td>
													<td></td>
													<td></td>
												</tr>--->
												
												
												
									    </table>
									    </div>
									
									
									</div>
								</div>
  
							  
							  
                              </div>
						  </div>
		
		
		
		
		
		
		
		
		
		
		
		
		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        
      </div>
