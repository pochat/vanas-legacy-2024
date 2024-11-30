



    <div class="row" style="padding-left:45px;">
		<div class="col-md-1">
			<div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'>
			<span>Name</span>
			</div>
			<br>	
			<div class="panel panel-default" style="height:565px;">

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
			$Query.="WHERE fl_instituto=$fl_instituto ORDER BY no_equivalencia ASC ";
			$rs = EjecutaQuery($Query);
			
			$tot_registros = CuentaRegistros($rs);
		?>
			<input type="hidden" name="tot_registros" id="tot_registros" value="<?php echo $tot_registros;?>">
			
		<?php	
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
			    $fl_criterio_fame=$row["fl_criterio_fame"];
			    $ds_desc=$row["ds_descripcion"];
			    #Recuperamos las imagenes por calificacion
			    $Query="SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame ";
			    $row=RecuperaValor($Query);
			    $nb_archivo_criterio=!empty($row[0])?$row[0]:NULL;
			    $src_img="../../AD3M2SRC4/images/rubrics/".$nb_archivo_criterio;
				$contador ++;

                #Sustitimos los saltos de linea.
                $ds_desc=str_replace("<br />", "\n", $ds_desc);


		?>
		<div class="col-md-2" >				
			<div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'>
				<?php echo $ds_calificacion;  ?>
			</div>
			<br>
			<div class="panel panel-default" style="height:565px;">
				<div class="panel-body text-center">
				
					<span  style="color:#8FCAE5;font-size:15px; "><?php echo $ds_equivalencia;  ?> </span>  <p>&nbsp;</p>
				
					<div class="chart" data-percent="<?php echo $no_max; ?>" id="easy-pie-chart_<?php echo $i;?>">
							<span class="percent" style="font:18px Arial;"><?php echo $no_max; ?></span>
					</div>
																					
						<script>
							$(document).ready(function () {
								$('#easy-pie-chart_<?php echo $i;?>').easyPieChart({
									animate: 2000,
									scaleColor: false,
									lineWidth: 7.5,
									lineCap: 'square',
									size: 100,
									trackColor: '#EEEEEE',
									barColor: '#92D099'
								});

								$('#easy-pie-chart_<?php echo $i;?>').css({
									width: 100 + 'px',
									height: 100 + 'px',
									margin: 'auto'
								});
								$('#easy-pie-chart_<?php echo $i;?>.percent').css({
									"line-height": 100 + 'px'
								})

							});
						</script>				    
					<!--<div class="knobs-demo">
						<div>
							<input class="knob<?php echo $contador; ?> font"     value="<?php echo $no_max; ?>"  disabled/>
						</div>
					</div>-->
					<hr style="margin-bottom:1px;"/>
					<div class="form-group text-left">
						<a href="javascript:void(0);" class="btn btn-xs btn-default pull-right" style="border:0px;" Onclick="EditarDescripcion<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);">
							<i class="fa fa-pencil" ></i>
						</a>
						<textarea class="form-control" rows="3" name="desc<?php echo $contador; ?>"  id="desc<?php echo $contador; ?>"  style="resize: none !important; overflow-y: scroll;width: 100%; height: 110px;" maxlength="130" onkeydown="CuentaCarteres_<?php echo $contador; ?>()" onKeyUp="CuentaCarteres_<?php echo $contador; ?>()" ><?php echo $ds_desc;?></textarea>
                    </div>
						<div class="form-group">
							<div class="col-md-5">
								<a class="btn btn-default btn-xs" href="javascript:void(0);" id="btncancel<?php echo $contador; ?>" Onclick="CancelarEdicion<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);">Cancel</a>
							</div>
							<div class="col-md-2 text-center" style="padding-left: 8px;">
								<span id="char<?php echo $contador;?>" name="char<?php echo $contador;?>" class="text-center">charrt </span>
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
						<br><br>
<?php

                        #Inicia dropzone
                        $nombre= 'dropzone_'.$contador;
                        echo "
                        <input type='hidden' name='fl_citerio_fame_$contador' id='fl_citerio_fame_$contador' value='$fl_criterio_fame'>
                        <input type='hidden' name='nb_archivo_$contador' id='nb_archivo_$contador' value='$nb_archivo_criterio'>
						<div class='widget-body'>";
                        echo "
                         <div class='dropzone' id='{$nombre}' style='min-height: 100px;  background-image: url(../../AD3M2SRC4/images/dropzone_small.png) !important; background:no-repeat;  background-size: 100% 100%; width: 100%;height: auto;'>
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
                                  <div class='modal-dialog demo-modal' style='width:400px;height:400px;margin-top: -530px;'>
                                   <div class='modal-content' style='width:400px;height:400px;'>
                                   <div class='modal-body padding-5'  style='width:400px;height:400px;'>
                                        <img class='superbox-current-img' src='$src_img' style='width:494px;height:389px;'>
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
								url: '../../AD3M2SRC4/modules/rubrics/upload.php', ";
                         if(!empty($fl_criterio_fame)){  
                             echo"params: {fl_criterio_fame:$fl_criterio_fame}, ";
                         }else{
                             echo"params: {'fl_criterio':'$clave','fl_calificacion_criterio':'$fl_calificacion_criterio','fg_creado_instituto':'1'}, ";
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
                                                $('#fl_citerio_fame_$contador').val(message.valores.fl_criterio_fame);
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
												url: 'site/guardar_descripcion_criterio_eng.php',
												data: 'ds_descripcion='+ ds_descripcion +
												      '&fl_registro='+clave +
													  '&fl_calificacion='+fl_calificacion
												}).done(function (result) {
									     var result = JSON.parse(result);

									          $("#fl_citerio_fame_<?php echo $contador;?>").val(result.fl_criterio_fame);

									     });
										 document.getElementById("desc<?php echo $contador;?>").disabled = true;
										 $("#btncancel<?php echo $contador;?>").addClass('hidden');
										 //botones desabilitados
							             $("#btnsave<?php echo $contador;?>").addClass('hidden');
							             //botones desabilitados
										 $("#char<?php echo $contador;?>").addClass('hidden');
								}
							</script>
						<?php
                           echo"<script>
                                	
                           
                                    function CuentaCarteres_$contador() {							            
										var texto= document.getElementById('desc$contador').value;												
										var carat=130-texto.length;																				                             
                                        $('#char$contador').html(carat);
							        }
									
									$(document).ready(function () {
                                       CuentaCarteres_$contador();
                                    });
                                </script>
                                ";
								
                        ?>

						<?php
						}
						?>
						<div class="col-md-1">&nbsp;</div>
				</div>	 
         