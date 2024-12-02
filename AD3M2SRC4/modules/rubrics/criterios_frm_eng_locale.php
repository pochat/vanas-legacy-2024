<!-- English Content Starts Here -->
    
<div class="tab-pane fade in active" id="criterio">
    <div class="row" style="padding-left:45px;">
		<div class="col-md-1">
			<div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'>
			<span>Name</span>
			</div>
			<br>	
			<div class="panel panel-default" style="height:565px;">
				<!--
				<div class="panel-body text-center" style='writing-mode: vertical-lr; transform: rotate(180deg); font-size:16px; font-weight:bold;'>
						<p style="margin: 0 0 179px;"><a href="" ><?php echo $nb_criterio; ?> </a></p>
					</div>-->
				<div class="panel-body text-center" >
					<!--<a href="javascript:void(0);" class="btn btn-xs btn-default pull-right" style="border:0px;" Onclick="EditarTitulo( );"><i class="fa fa-pencil" ></i></a>-->
					<section class="form-group" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);margin-top: 290px;">
						
						<label class="input" style="font-weight:bold;">
							<input  class="form-control input-lg"  style=" border: 0px solid #ccc;" name="nb_criterio" id="nb_criterio" type="text" value="<?php echo $nb_criterio; ?>" />
						</label>
					</section>
					<div class='col-md-6' style='padding-left:1px !important;'>
						<a class="btn btn-default btn-xs hidden" style='padding-left:-10px !important;' href="javascript:void(0);" id="btncancelarr" Onclick="CancelarEdicion( );">Cancel</a>
					</div>
					<script>
					function EditarTitulo( ){
					document.getElementById('nb_criterio').style.border = '1px solid #ccc';
					$("#btncancelarr").removeClass('hidden');//botones desabilitados
					}
					function CancelarEdicion( ){
					 document.getElementById('nb_criterio').style.border = '0px solid #ccc';
					 $("#btncancelarr").addClass('hidden');//botones desabilitados
					
					}
					</script>		
				</div>
			</div>
		</div>
		<?php 
			#Recupermos las calificaciones existentes
			$contador=0;
			$Query="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max 
					FROM c_calificacion_criterio ";
			$Query.="	WHERE fl_instituto is null ORDER BY no_equivalencia ASC ";
			$rs = EjecutaQuery($Query);
			//echo"   
			// <input type='hidden' name='nb_archivos' id='nb_archivos' value=''> ";

			$tot_registros = CuentaRegistros($rs);
			Forma_CampoOculto('tot_registros', $tot_registros);
			for($i=1;$row=RecuperaRegistro($rs);$i++) {
				$fl_calificacion_criterio=$row['fl_calificacion_criterio'];
				$cl_calificacion=$row['cl_calificacion'];
				$ds_calificacion=$row['ds_calificacion'];
				$fg_aprobado=$row['fg_aprobado'];
				$no_equivalencia=$row['no_equivalencia'];
				$no_min= number_format($row['no_min']);
				$no_max=number_format($row['no_max']);
				if($no_max==0)
					$ds_equivalencia="No Uploaded";
				else
					$ds_equivalencia=$no_min."% - ".$no_max."%"." ($cl_calificacion)";
							
			    #Recupermaos la descripcion que tiene actualmente.
			    $Query="SELECT fl_criterio_fame, ds_descripcion  FROM k_criterio_fame WHERE fl_criterio=$clave AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
			    $row=RecuperaValor($Query);
			    $fl_criterio_fame=!empty($row["fl_criterio_fame"])?$row["fl_criterio_fame"]:NULL;
			    $ds_desc=str_texto(!empty($row["ds_descripcion"])?$row["ds_descripcion"]:NULL);
			    #Recuperamos las imagenes por calificacion
			    $Query="SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame ";
			    $row=RecuperaValor($Query);
			    $nb_archivo_criterio=!empty($row[0])?$row[0]:NULL;
			    $src_img="../../images/rubrics/".$nb_archivo_criterio;
				$contador ++;

		?>
		<div class="col-md-2" >				
			<div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'>
				<?php echo $ds_calificacion;  ?>
			</div>
			<br>
			<div class="panel panel-default" style="height:565px;">
				<div class="panel-body text-center">
				    <span  style="color:#8FCAE5;font-size:15px; "><?php echo $ds_equivalencia;  ?> </span>  <p>&nbsp;</p>
					<div class="knobs-demo">
						<div>
							<input class="knob<?php echo $contador; ?> font"     value="<?php echo $no_max; ?>"  disabled/>
						</div>
					</div>
					<hr style="margin-bottom:1px;"/>
					<div class="form-group text-left">
						<a href="javascript:void(0);" class="btn btn-xs btn-default pull-right" style="border:0px;" Onclick="EditarDescripcion<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);">
							<i class="fa fa-pencil" ></i>
						</a>
						<textarea class="form-control" rows="3" name="desc<?php echo $contador; ?>"  id="desc<?php echo $contador; ?>"  style="resize: none !important; overflow-y: scroll; height: 110px;" maxlength="130" onkeydown="CuentaCarteres<?php echo $contador; ?>()" onKeyUp="CuentaCarteres<?php echo $contador; ?>()" ><?php echo $ds_desc;?></textarea>
                    </div>
						<div class="form-group">
							<div class="col-md-5">
								<a class="btn btn-default btn-xs" href="javascript:void(0);" id="btncancel<?php echo $contador; ?>" Onclick="CancelarEdicion<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);">Cancel</a>
							</div>
							<div class="col-md-2 text-center" style="padding-left: 8px;">
								<span id="char<?php echo $contador; ?>" class="text-center"> </span>
							</div>
							<div class="col-md-5"> 
								<a class="btn btn-primary btn-xs" href="javascript:void(0);"  id="btnsave<?php echo $contador; ?>" Onclick="GuardarDescripcion<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);">Save</a>
							</div>
						</div>
						<style>
						.dropzone .dz-preview.dz-processing .dz-progress, .dropzone-previews .dz-preview.dz-processing .dz-progress {
							display: none;
						}
						</style>									
<?php

                        #Inicia dropzone
                        $nombre= 'dropzone_'.$contador;
                        echo "
                        <input type='hidden' name='nb_archivo_$contador' id='nb_archivo_$contador' value=''>
						<div class='widget-body'>";
                        echo "
                         <div class='dropzone' id='{$nombre}' style='min-height: 100px;  background-image: url(../../images/dropzone_small.png) !important; background:no-repeat;  background-size: 100% 100%; width: 100%;height: auto;'>
                         </div>";
                        echo"<div class='text-left' style='font-size:10px;'> ";
                        echo html_entity_decode(ObtenEtiqueta(1662));
                        echo "</div></div>";
                        #<!------finaliza dropzone-------->	
                        if(!empty($nb_archivo_criterio)){               
                            #presenta preview imagen		  
                            echo"<a class='zoomimg' href='#'> 
                                 <img src='$src_img' class='away no-border' width='40px' height='40px'>
                                  <span style='left:-300px;'>
                                  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-top: -530px;'>
                                   <div class='modal-content' style='width:500px;height:500px;'>
                                   <div class='modal-body padding-5'  style='width:500px;height:500px;'>
                                        <img class='superbox-current-img' src='$src_img' style='width:494px;height:490px;'>
                                    </div>
                                    </div>
                                    </div>
                                    </span>
                                	</a>&nbsp;&nbsp;";
                         }                             
                         echo "<script type='text/javascript'>
							// DO NOT REMOVE : GLOBAL FUNCTIONS!
							$(document).ready(function() {
								pageSetUp();
								Dropzone.autoDiscover = false;
								$('#{$nombre}').dropzone({
								url: 'upload.php', ";
                         if(!empty($fl_criterio_fame)){  
                             echo"params: {fl_criterio_fame:$fl_criterio_fame}, ";
                         }
                         echo "// data:  'id=1',
								addRemoveLinks : true,
								maxFilesize: 1024,
								acceptedFiles: '.png,.jpeg,.jpg,.flv, .mov, .mp4',
								// Solo permite guardar un registro
								maxFiles: 1,           
								init: function() {
										this.on('error', function(file, message) { 
											alert('".ObtenEtiqueta(1239)."');
											this.removeFile(file); 
										});
									   }, 
										success: function(file,result){
										var message, status;
										message = JSON.parse(result);		
										status = message.valores.status;
										nb_archivo=message.valores.nb_archivo;
                                            if(status==true){
												document.getElementById('nb_archivo_$contador').value = nb_archivo;
											}else{
												alert('File already exists !');
												this.removeFile(file);
											}
										},
										// complete: function(file,result){
											// if(file.status == 'success'){
                                                // prev = $('#nb_archivos').val();
												// document.getElementById('nb_archivo_$contador').value = file.name;
												// if (prev != '')
													// $('#nb_archivos').val(prev + ',' + file.name);
												// else
													// $('#nb_archivos').val(file.name);
										// }
										// },
										// error: function(file){
											// alert('Error subiendo el archivo ' + file.name);
										// },
										removedfile: function(file, serverFileName){
											var name = file.name;
											var element;
											(element = file.previewElement)!=null ? 
											element.parentNode.removeChild(file.previewElement) : 
												false;
												// alert('El elemento fué eliminado: ' + name); 
											}
										});
									})
									</script>";
                            ?>
								</div>
							</div>
						</div>
						<script>
							$(document).ready(function () {
								document.getElementById("desc<?php echo $contador;?>").disabled = true;
								//tofos al cargar el document estan desaibiltados
								$("#btncancel<?php echo $contador; ?>").addClass('hidden');
								//botones desabilitados
				        		$("#btnsave<?php echo $contador; ?>").addClass('hidden');
				        		//botones desabilitados
								$("#char<?php echo $contador; ?>").addClass('hidden');
								//botones desabilitados
								//<!--propiedades del input knob -->
								$('.knob<?php echo $contador; ?>').knob({
								  'width':100,
								  'height':100,
								  'angleArc':360,
								  'thickness':0.16,
								  'cursor':false,
								  'readOnly':true,
								  'angleOffset':50,
								  'fgColor':'#B7B7B7'
							   
								});
					    	});
							//<!--MJD funcion para editar la descripcion de los criterios-->
							function EditarDescripcion<?php echo $contador; ?>(fl_calificacion_criterio){
									document.getElementById("desc<?php echo $contador; ?>").disabled = false;
									  $("#btncancel<?php echo $contador; ?>").removeClass('hidden');
									  $("#btnsave<?php echo $contador; ?>").removeClass('hidden');
									  $("#char<?php echo $contador; ?>").removeClass('hidden');
								}
							   // <!---funcion para inabilitra la edicion de la descripcion del criterio-->
								function CancelarEdicion<?php echo $contador; ?>(fl_calificacion_criterio){
										document.getElementById("desc<?php echo $contador; ?>").disabled = true;
										$("#btncancel<?php echo $contador; ?>").addClass('hidden');
										//botones desabilitados
							            $("#btnsave<?php echo $contador; ?>").addClass('hidden');
							            //botones desabilitados
										$("#char<?php echo $contador; ?>").addClass('hidden');
								}
								function GuardarDescripcion<?php echo $contador; ?>(fl_calificacion_criterio){
								        var ds_descripcion = document.getElementById("desc<?php echo $contador; ?>").value;
								        var fl_calificacion=fl_calificacion_criterio;
										var clave=document.getElementById("fl_registro").value;
										    $.ajax({
												type: 'POST',
												url: 'guardar_descripcion_criterio_eng.php',
												data: 'ds_descripcion='+ ds_descripcion +
												      '&fl_registro='+clave +
													  '&fl_calificacion='+fl_calificacion ,
												async: true,
												success: function (html) {
													
													 $('#muestra_save').html(html);
												}
											});
										 document.getElementById("desc<?php echo $contador; ?>").disabled = true;
										 $("#btncancel<?php echo $contador; ?>").addClass('hidden');
										 //botones desabilitados
							             $("#btnsave<?php echo $contador; ?>").addClass('hidden');
							             //botones desabilitados
										 $("#char<?php echo $contador; ?>").addClass('hidden');
								}
							</script>
						<?php
                           echo"<script>
                                	$(document).ready(function () {
                                        CuentaCarteres$contador();
                                    });
                           
                                    function CuentaCarteres$contador() {
							            //document.datos.char$contador.value=130 -(document.datos.desc$contador.value.length);
                                        var este=130 -(document.datos.desc$contador.value.length);
                                        $('#char$contador').html(este);
							        }
                                </script>
                                ";
                        ?>

						<?php
						}
						?>
						<div class="col-md-1">&nbsp;</div>
				</div>	 
            </div>
<!-- END of English Content -->
