<?php

# IMPORTANT!!! Change all the sufix in the form (_xxx, where the (xxx) are the leters used to identify the language used on this instance

function localeEtiqueta_esp($p_etiqueta){
# Change the locale file to match the language used on this instance of criterion
	$langfile = 'spanish.csv';
	$file = fopen($_SERVER['DOCUMENT_ROOT']."/locale/".$langfile, "r") or exit("Unable to open file!");
	$separator = "|";
	$id = strval($p_etiqueta)." |";

	// Output a line of the file until the end is reached
	while(!feof($file)) {
	    $line = fgets($file);
	    $len_line = strlen($line);
	    $find_id = strripos($line, $id);
	    $find_gap = strripos($line, $separator);

	    if ($find_id === false) {
	    } else {
	      $id = substr($line, $find_id, $find_gap);
	      $tag = substr($line, $find_gap+1);
	      break;
	    }
	}
	fclose($file);
	return htmlspecialchars(rtrim($tag), ENT_QUOTES, "UTF-8");
}

?>

<div class="row" style="padding-left:45px;">
			<div class="col-md-1">
				<div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'>
				<span>Nombre</span>
				</div>
				<br>	
				<div class="panel panel-default" style="height:565px;">
					
					<div class="panel-body text-center" >
						<!--<a href="javascript:void(0);" class="btn btn-xs btn-default pull-right" style="border:0px;" Onclick="EditarTitulo( );"><i class="fa fa-pencil" ></i></a>-->
						<section class="form-group" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);margin-top: 290px;">
						<label class="input" style="font-weight:bold;">
								<input  class="form-control input-lg"  style=" border: 0px solid #ccc;" name="nb_criterio_esp" id="nb_criterio_esp" type="text" value="<?php echo $nb_criterio_esp; ?>" />
						</label>
						</section>
						<div class='col-md-6' style='padding-left:1px !important;'>
							<a class="btn btn-default btn-xs hidden" style='padding-left:-10px !important;' href="javascript:void(0);" id="btncancelar_esp" Onclick="CancelarEdicion_esp();">Cancel</a>
						</div>
						<script>
						function EditarTitulo_esp(){
						document.getElementById('nb_criterio_esp').style.border = '1px solid #ccc';
						$("#btncancelar_esp").removeClass('hidden');//botones desabilitados
						}
						function CancelarEdicion_esp(){
						 document.getElementById('nb_criterio_esp').style.border = '0px solid #ccc';
						 $("#btncancelar_esp").addClass('hidden');//botones desabilitados
						}
						</script>		
					</div>
				</div>
			</div>
	<?php

		#Recupermos las calificaciones existentes
		$contador=0;
		$Query="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion_esp,fg_aprobado,no_equivalencia,no_min,no_max 
				FROM c_calificacion_criterio ";
		$Query.="WHERE fl_instituto=$fl_instituto ORDER BY no_equivalencia ASC ";
		$rs_esp = EjecutaQuery($Query);
		$tot_registros = CuentaRegistros($rs_esp);
		?>
		<input type="hidden" name="tot_registros_esp" id="tot_registros_esp" value="<?php echo $tot_registros;?>">
		<?php
		for($i=1;$row=RecuperaRegistro($rs_esp);$i++) {
			$fl_calificacion_criterio=$row['fl_calificacion_criterio'];
			$cl_calificacion=$row['cl_calificacion'];
			$ds_calificacion_esp=$row['ds_calificacion_esp'];
			$fg_aprobado=$row['fg_aprobado'];
			$no_equivalencia=$row['no_equivalencia'];
			$no_min= number_format($row['no_min']);
			$no_max=number_format($row['no_max']);
			if($no_max==0)
				$ds_equivalencia="No subido";
			else
				$ds_equivalencia=$no_min."% - ".$no_max."%"." ($cl_calificacion)";

		    #Recupermaos la descripcion que tiene actualmente.
		    $Query="SELECT fl_criterio_fame, ds_descripcion_esp  FROM k_criterio_fame WHERE fl_criterio=$clave AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
		    $row=RecuperaValor($Query);
		    $fl_criterio_fame=$row["fl_criterio_fame"];
		    $ds_desc_esp=$row["ds_descripcion_esp"];


            #Sustitimos los saltos de linea.
            $ds_desc_esp=str_replace("<br />", "\n", $ds_desc_esp);


		    #Recuperamos las imagenes por calificacion
		    $Query="SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame ";
		    $row=RecuperaValor($Query);
		    $nb_archivo_criterio=!empty($row[0])?$row[0]:NULL;
		    $src_img="../../AD3M2SRC4/images/rubrics/".$nb_archivo_criterio;
			$contador ++;
	?>
			<div class="col-md-2" >				
				<div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'>
					<?php echo $ds_calificacion_esp;  ?>
				</div>
				<br>
				<div class="panel panel-default" style="height:565px;">
					<div class="panel-body text-center">
					    <span  style="color:#8FCAE5;font-size:15px; "><?php echo $ds_equivalencia;  ?> </span>  <p>&nbsp;</p>
							
						<div class="chart" data-percent="<?php echo $no_max; ?>" id="easy-pie-chart_esp_<?php echo $i;?>">
							<span class="percent" style="font:18px Arial;"><?php echo $no_max; ?></span>
						</div>
							
						<script>
							$(document).ready(function () {
								$('#easy-pie-chart_esp_<?php echo $i;?>').easyPieChart({
									animate: 2000,
									scaleColor: false,
									lineWidth: 7.5,
									lineCap: 'square',
									size: 100,
									trackColor: '#EEEEEE',
									barColor: '#92D099'
								});

								$('#easy-pie-chart_esp_<?php echo $i;?>').css({
									width: 100 + 'px',
									height: 100 + 'px',
									margin: 'auto'
								});
								$('#easy-pie-chart_esp_<?php echo $i;?>.percent').css({
									"line-height": 100 + 'px'
								})

							});
						</script>	
						<hr style="margin-bottom:1px;"/>
						<div class="form-group text-left">
							<a href="javascript:void(0);" class="btn btn-xs btn-default pull-right" style="border:0px;" Onclick="EditarDescripcion_esp<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);">
								<i class="fa fa-pencil" ></i>
							</a>
							<textarea class="form-control" rows="3" name="desc_esp<?php echo $contador; ?>"  id="desc_esp<?php echo $contador; ?>"  style="resize: none !important; overflow-y: scroll;width: 100%; height: 110px;" maxlength="130" onkeydown="CuentaCarteres_esp<?php echo $contador; ?>()" onKeyUp="CuentaCarteres_esp<?php echo $contador; ?>()" ><?php echo $ds_desc_esp;?></textarea>
                        </div>
							<div class="form-group">
								<div class="col-md-5">
									<a class="btn btn-default btn-xs" href="javascript:void(0);" id="btncancel_esp<?php echo $contador; ?>" Onclick="CancelarEdicion_esp<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);">Cancel</a>
								</div>
								<div class="col-md-2 text-center" style="padding-left: 8px;">
									<span id="char_esp<?php echo $contador; ?>" class="text-center"> </span>
								</div>
								<div class="col-md-5"> 
									<a class="btn btn-primary btn-xs" href="javascript:void(0);"  id="btnsave_esp<?php echo $contador; ?>" Onclick="GuardarDescripcion_esp<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);">Save</a>
								</div>
							</div>
							<style>
							.dropzone .dz-preview.dz-processing .dz-progress, .dropzone-previews .dz-preview.dz-processing .dz-progress {
								display: none;
							}
							</style>
							<br><br>
							<div class='widget-body'>
							<div class='row' style='min-height: 100px;' width='130px' height='100px'>
                            </div>
                            <div class='text-left' style='font-size:10px;'>
                            	<?php echo html_entity_decode(localeEtiqueta_esp(1662)); ?>
    						</div>
    					</div>								
<?php
            echo"<input type='hidden' name='fl_citerio_fame_esp_$contador' id='fl_citerio_fame_esp_$contador' value='$fl_criterio_fame'>";
       //                     #Inicia dropzone
       //                     #$nombre= 'dropzone_'.$contador;
       //                      echo "
       //                      <input type='hidden' name='nb_archivo_$contador' id='nb_archivo_$contador' value=''>
							// <div class='widget-body'>";
       //                      echo "
       //                       <div class='dropzone' id='{$nombre}' style='min-height: 100px;  background-image: url(../../images/dropzone_small.png) !important; background:no-repeat;  background-size: 100% 100%; width: 100%;height: auto;'>
       //                       </div>";
       //                      echo"<div class='text-left' style='font-size:10px;'> ";
       //                      echo html_entity_decode(ObtenEtiqueta(1662))."</div>";
       //                      echo "</div>";
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
                                ?>
									</div>
								</div>
							</div>
							<script>
								$(document).ready(function () {
									document.getElementById("desc_esp<?php echo $contador;?>").disabled = true;
									//All buttons are disabled on firt load
									$("#btncancel_esp<?php echo $contador; ?>").addClass('hidden');
									//botones desabilitados
					        		$("#btnsave_esp<?php echo $contador; ?>").addClass('hidden');
					        		//botones desabilitados
									$("#char_esp<?php echo $contador; ?>").addClass('hidden');
									
						    	});
								//<!--MJD funcion para editar la descripcion de los criterios-->
								function EditarDescripcion_esp<?php echo $contador; ?>(fl_calificacion_criterio){
										document.getElementById("desc_esp<?php echo $contador; ?>").disabled = false;
										  $("#btncancel_esp<?php echo $contador; ?>").removeClass('hidden');
										  $("#btnsave_esp<?php echo $contador; ?>").removeClass('hidden');
										  $("#char_esp<?php echo $contador; ?>").removeClass('hidden');
									}
								   // <!---funcion para inabilitra la edicion de la descripcion del criterio-->
									function CancelarEdicion_esp<?php echo $contador; ?>(fl_calificacion_criterio){
											document.getElementById("desc_esp<?php echo $contador; ?>").disabled = true;
											$("#btncancel_esp<?php echo $contador; ?>").addClass('hidden');
											//botones desabilitados
								            $("#btnsave_esp<?php echo $contador; ?>").addClass('hidden');
								            //botones desabilitados
											$("#char_esp<?php echo $contador; ?>").addClass('hidden');
									}
									function GuardarDescripcion_esp<?php echo $contador; ?>(fl_calificacion_criterio){
									        var ds_descripcion_esp = document.getElementById("desc_esp<?php echo $contador; ?>").value;
									        var fl_calificacion=fl_calificacion_criterio;
											var clave=document.getElementById("fl_registro").value;
											    $.ajax({
													type: 'POST',
													url: 'site/guardar_descripcion_criterio_esp.php',
													data: 'ds_descripcion_esp='+ ds_descripcion_esp +
													      '&fl_registro='+clave +
														  '&fl_calificacion='+fl_calificacion ,
													async: true,
													success: function (html) {
														
														 $('#muestra_save').html(html);
													}
												});
											 document.getElementById("desc_esp<?php echo $contador; ?>").disabled = true;
											 $("#btncancel_esp<?php echo $contador; ?>").addClass('hidden');
											 //botones desabilitados
								             $("#btnsave_esp<?php echo $contador; ?>").addClass('hidden');
								             //botones desabilitados
											 $("#char_esp<?php echo $contador; ?>").addClass('hidden');
									}
								</script>
							<?php
                               echo"<script>
                                    	$(document).ready(function () {
                                            CuentaCarteres_esp$contador();
                                        });
                                
                                        function CuentaCarteres_esp$contador() {
								            
											var texto= document.getElementById('desc_esp$contador').value;												
											var carat=130-texto.length;																				                             
                                            $('#char_esp$contador').html(carat);
											
											
											
								        }
                                    </script>
                                    ";
								}
							?>
 						<div class="col-md-1">&nbsp;</div>
					</div>	 
