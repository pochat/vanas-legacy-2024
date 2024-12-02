<?php
# Libreria de funciones
require '../../lib/general.inc.php';

$cl_sesion=RecibeParametroHTML('cl_sesion');
$fl_programa=RecibeParametroNumerico('fl_programa');


//$cl_sesion="c5e7d8dec215c0bc1bd63c6076fdf13a749ccb71775fd9cb964475b1b658069e";
//$fl_programa=31;

$Query="SELECT nb_programa FROM c_programa WHERE fl_programa=$fl_programa ";
$row=RecuperaValor($Query);
$nb_programa=str_texto($row['nb_programa']);



?>
<script>
    pageSetUp();
</script>

<!----Librerias para pintar slider azul----->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.min.js" ></script>
   
<script>
    pageSetUp();
</script>
<style>
.chart {
    /* height: 220px; */
    margin: auto !important;
}
.easyPieChart {
    position: relative;
    text-align: center !important;
}
.easyPieChart canvas {
    position: absolute;
    top: 0;
    left: 0;
}

.ui-slider .ui-slider-handle {
z-index: -1 !important;
}
.ui-slider-horizontal {
background: transparent;
}
.slider.slider-horizontal .slider-handle {

	margin-top: -2px !important;
}
.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
background-color: #fff !important;

}

</style>


	
<!-----
 DEMO DE LA BARRA AZUL
	

<input id="ex99"  class="slider slider-primary hidden" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="14"/>

		<div id="amount"></div>										

<script>



$(document).ready(function () {

	
    var slideStart = false;
  var slider = new Slider('#ex99', {
  formatter: function(value) {
    return 'Current value: ' + value;
  }
});
    slider.on('slideStart', function () {
        // Set a flag to indicate slide in progress
        slideStart = true;
        // Clear the timeout
        // clearInterval(refreshId);
        // alert('inicia');
    });

    slider.on('slideStop', function (value) {
        // Set a flag to indicate slide not in progress
        slideStart = false;
        alert('stop'+value);
        // start the timeout
        // refreshId = setInterval(function () { // saving the timeout
            // sTimeout();
        // }, intSeconds * 3000);
    });
});







</script> 	
	
---->

<?php 

	#Verificamos si ya esta calificado por el admin 
	$Query="SELECT fg_calificado FROM c_sesion WHERE cl_sesion='$cl_sesion' ";
	$row=RecuperaValor($Query);
	$fg_esta_calificado_admin=$row['fg_calificado'];
	
	if(empty($fg_esta_calificado_admin)){#indica que se va calificar un estudiante que esta completo/immcompleto su trabajo.

		#Eliminamos registros basura que haya del los comentarios del teacher, y que esten asociado al alumno y sulecion y programa.
		EjecutaQuery("DELETE FROM c_com_criterio_admin WHERE  cl_sesion='$cl_sesion' AND fl_programa=$fl_programa  ");
		EjecutaInsert("DELETE FROM k_calificacion_admin WHERE cl_sesion='$cl_sesion' AND fl_programa=$fl_programa ");
		#Eliminamos regitros existentes
		EjecutaQuery("DELETE FROM c_calculo_admin WHERE  cl_sesion='$cl_sesion' AND fl_programa=$fl_programa ");


	}else{
		#Recupermaos la fecha de calificacion para presentarlo en transcript.
		$Query="SELECT fe_modificacion FROM c_com_criterio_admin WHERE fg_com_final='1' AND cl_sesion='$cl_sesion' AND fl_programa=$fl_programa ";
		$row=RecuperaValor($Query);
		$fe_calificado=ObtenFechaFormatoDiaMesAnioHora($row[0]);



	}
                                    
?>

		<div class="row padding-10" >
			<div class="col-md-12">
			   <div class="panel panel-default" style="border-radius:20px;">
					<div class="panel-body text-center">
					<p style="font-size:20px;"><?php echo $nb_programa;?></p>
						<?php
						if(!empty($fe_calificado)){
						  echo "<b>".ObtenEtiqueta(1678).":</b> ".$fe_calificado."<br >";
						}
						?>

					</div>
				</div>
			</div>
		</div>	


		
				<?php
					#Muestra Rubric para asignar calificacion del  teacher
				   

				   #Recuperamos todos los criterios
				   $Query="SELECT fl_criterio, no_valor FROM k_criterio_curso WHERE fl_programa = $fl_programa ORDER BY no_orden ASC	";
				   $rs_prin = EjecutaQuery($Query);
				   $registros = CuentaRegistros($rs_prin);
				  
				   $cont1=0;
				   $rubric ="";
				   $fl_identificador= rand(1, 300);

				   for($i_prin=1;$row_prin=RecuperaRegistro($rs_prin);$i_prin++) {
					   
					   $fl_criterio=$row_prin['fl_criterio'];
					   $no_valor_criterio = $row_prin['no_valor'];
					   
					   $rs_nb_crit = RecuperaValor("SELECT nb_criterio FROM c_criterio WHERE fl_criterio = $fl_criterio");
					   $nb_criterio = str_texto($rs_nb_crit[0]);
					   
					   $cont1 ++;
				?>

		
										
											<div class='row padding-10'  >
													<div class='col-md-1' style='padding-right: 0px;'>				
																		 <div class='col-md-12' style='padding-left: 1px;' >
																			  <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
																				Criterion
																			  </div>
																			  
																			  
																			  <br/>
																			  <div class='panel panel-default text-center' style='height:424px;'>
																			  <p style='margin: 0 0 0px;'>&nbsp;</p>
																			  <span  style='color:#8FCAE5;font-size:15px; '><?php echo $no_valor_criterio."%";?> </span>
																				<div  class='panel-body text-center' style='writing-mode: vertical-lr; transform: rotate(180deg); font-size:18px; font-weight:bold; padding: 15px 20px 90px 50px;'><?php echo $nb_criterio; ?></div>
																			  </div>
																		  </div>

													 </div>
													 
													 <div class='col-md-11' style='padding-left: 1px;'>
													 <?php
														   $Query="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion_criterio ";
														   $Query.="	WHERE fl_instituto IS NULL ORDER BY no_equivalencia ASC  ";
														   $rs = EjecutaQuery($Query);
														   $contador_border=0;
														   $contador = 0;
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
															   $Query_c="SELECT ds_descripcion,fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
															   $row_c=RecuperaValor($Query_c);
															   $ds_desc=!empty($row_c[0])?str_texto($row_c[0]):NULL;
															   $fl_criterio_fame=!empty($row_c[1])?$row_c[1]:NULL;
															   
															   
															   
															   
															   #Recuperamos las imagenes por calificacion
															   $Query_img = "SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame ";
															   $row_img = RecuperaValor($Query_img);
															   $nb_archivo_criterio = !empty($row_img[0])?$row_img[0]:NULL;
															   $src_img= PATH_HOME."/images/rubrics/".$nb_archivo_criterio;
															   
															   $contador ++;
															   $contador_border++;
															   if(!empty($nb_archivo_criterio)){
																   $icono = "<a class='zoomimg' href='#' style='color:#000;text-decoration: none;'> 
																				<i class='fa fa-file-picture-o'></i>
																				<span style='left:-300px;'>
																				  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-bottom: -530px;'>
																					<div class='modal-content' style='width:500px;height:500px;'>
																					  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
																						<img class='superbox-current-img' src='$src_img' style='width:494px;height:490px;'>
																					  </div>
																					</div>
																				  </div>
																				</span>
																			  </a> ";
															   }else{
																   $icono = "";
															   }
													 
													 
													 ?>
													 								   
															<div class='col-md-2' style='padding-left: 3px;padding-right: 3px;'>				
																    <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
																	  <?php echo $ds_calificacion."&nbsp;&nbsp;".$icono; ?>
																    </div>
																    <br/>
																    <div class='panel panel-default' style='height:424px;'>
																	   <div class='panel-body text-center'  id='divborder_cero_<?php echo $fl_criterio."_".$contador_border; ?> '>
																			<span  style='color:#8FCAE5;font-size:15px; '><?php echo $ds_equivalencia ?> </span>  <p>&nbsp;</p>
																			
																			 <div class='chart ' data-percent='<?php echo $no_max;?>' id='easy-pie-chart<?php echo $fl_criterio."_".$contador;?>'>
																				<span class='percent' style='font:18px Arial;font-weight:none !important;'><?php echo $no_max;?></span>
																			 </div>
																			
																			
																			 
																			<script> 
																				$(document).ready(function () {
																				
																						$('#easy-pie-chart<?php echo $fl_criterio."_".$contador; ?>').easyPieChart({
																							animate: 2000,
																							scaleColor: false,
																							lineWidth: 7.5,
																							lineCap: 'square',
																							size: 100,
																							trackColor: '#EEEEEE',
																							barColor: '#B7B7B7'
																						});

																						$('#easy-pie-chart<?php echo $fl_criterio."_".$contador; ?>').css({
																							width: 100 + 'px',
																							height: 100 + 'px'
																						});
																						$('#easy-pie-chart<?php echo $fl_criterio."_".$contador; ?> .percent').css({
																							'line-height': 100 + 'px'
																						})
																					
																				});
																			</script>
																			
																			
																			
																			
																			
																			
																			
																			
																			
																			
																			 <div class='form-group text-left' style='padding-left:5px; padding-right:5px;'>
																				<div id='desc<?php echo $contador; ?>'></div>
																				<hr>
												
																				<div class='bs-example' style='height:108px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
																				  <small class='text-muted'><i><?php echo $ds_desc; ?></i></small>              
																				</div>
											  
																			 </div>
																			 
																			 <div class='form-group'>
																						<div class='col-md-6'>
																						&nbsp;
																						</div>
																						<div class='col-md-6'> 
																					    &nbsp;
																						</div>

																			 </div>
																			 
																			 <p>&nbsp;</p>
													 
																			  
																	   </div>
																    </div>

															</div>
									
													
												
											<?php 

													}
													
													######################iNICIA COMENTARIOS DEL TEACHER##########################
												   #Recupermaos la calificacion asignada por el estudiante.
												   $porcentaje_equivalente=0;
												   $no_calificacion_final=0;
												   $ds_comentario_teacher="No comment";
												   $fe_calificado="No date";
												   $ds_comentario_final_teacher="No comment";
												   $no_promedio_final=110;
													 
												   if(!empty($fg_esta_calificado_admin)){
												   
													#Recuperamos si sxite una calificacion asignada.
													$Query="SELECT ds_comentarios,no_porcentaje_equivalente,fe_modificacion FROM c_com_criterio_admin WHERE fl_criterio=$fl_criterio AND cl_sesion='$cl_sesion' AND fl_programa=$fl_programa ";
													$row=RecuperaValor($Query);
													$ds_comentario_criterio=str_texto($row[0]);
													$no_porcentaje_equivalente=$row[1];
													//$fe_asignacion_califi=ObtenFechaFormatoDiaMesAnioHora($row[2]);
													$fe_asignacion_califi= $row[2];
													$fe_modificacion=strtotime('+0 day',strtotime($fe_asignacion_califi));
													$fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
													#DAMOS FORMATO DIA,MES, AÑO.
													$date = date_create($fe_modificacion);
													$fe_asignacion_califi=date_format($date,'F j, Y, g:i a');
												   
												   }else{
													$ds_comentario_criterio="";
													$fe_asignacion_califi="";
													$no_porcentaje_equivalente=0;
												   }

											?>
											        <div class='col-md-2' style='padding-left: 3px;padding-right: 3px;'>
															<div class='well well-lg text-center' style='padding:2px;background: #F2F2F2;'>
																<?php echo ObtenEtiqueta(1664); ?>	
															</div>
															<br/>
															
															<div class='panel panel-default' style='height:424px;'>
																<div class='panel-body text-center'>
																	<span  style='color:#8FCAE5;font-size:15px; '>&nbsp; </span>  <p>&nbsp;</p>
																	
																	 <div class='chart ' data-percent='<?php echo $no_porcentaje_equivalente; ?>' id='final_<?php echo $cont1;?>'>
																		<span class='percent' style='font:18px Arial;font-weight:none !important;' id='span_final<?php echo $cont1;?>' name='span_final<?php echo $cont1;?>'><?php echo $no_porcentaje_equivalente;?></span>
																	 </div>
																	 
																	<div class='form-group'>
																		<span id='char<?php echo $cont1;?>' name='char<?php echo $cont1;?>' class='pull-left text-left hidden'> </span>
																		<a href='javascript:void(0);' class='btn btn-xs btn-default pull-right' style='border:0px;' Onclick='EditarDescripcion<?php echo $cont1;?>(<?php echo $fl_criterio;?>);' style='top:-10px !important;'><i class='fa fa-pencil' ></i></a>
																	   
																		 <hr>
									
																		 
																		<textarea class='form-control' rows='4'  id='desc_<?php echo $cont1;?>' name='desc_<?php echo $cont1;?>' style='resize:none !important;' maxlength='130'  onkeydown='CuentaCarteres<?php echo $cont1;?>();' onKeyUp='CuentaCarteres<?php echo $cont1;?>();' disabled><?php echo $ds_comentario_criterio; ?></textarea>
																	</div>
																	
																			<script> 
																				$(document).ready(function () {
																						$('#btncancel<?php echo $cont1;?>').addClass('hidden');//botones desabilitados
																						$('#btnsave<?php echo $cont1;?>').addClass('hidden');//botones desabilitados
																						
																						$('#final_<?php echo $cont1;?>').easyPieChart({
																							animate: 2000,
																							scaleColor: false,
																							lineWidth: 7.5,
																							lineCap: 'square',
																							size: 100,
																							trackColor: '#EEEEEE',
																							barColor: '#92D099'
																						});

																						$('#final_<?php echo $cont1;?>').css({
																							width: 100 + 'px',
																							height: 100 + 'px'
																						});
																						$('#final_<?php echo $cont1;?> .percent').css({
																							'line-height': 100 + 'px'
																						})
																					
																				});
																			</script>
																	
																	
																	
																	
																	
																	<div class='form-group'>
																			<div class='col-md-6'>
																			<a class='btn btn-default btn-xs' style='font-size: 13px;' href='javascript:void(0);' id='btncancel<?php echo $cont1;?>' Onclick='CancelarEdicion<?php echo $cont1;?>(<?php echo $fl_criterio;?>);'>Cancel</a>
																			</div>
																			<div class='col-md-6'>
																				<a class='btn btn-primary btn-xs' style='font-size: 13px;' href='javascript:void(0);'  id='btnsave<?php echo $cont1;?>' Onclick='GuardarDescripcion<?php echo $cont1;?>(<?php echo $fl_criterio;?>);'>Save</a>
																			</div>

																	</div>
																	<div class='text-left' id='muestra_save<?php echo $cont1;?>' name='muestra_save<?php echo $cont1;?>' style='margin-left: -5px;color:#999;'><small class='text-muted'><i><?php echo ObtenEtiqueta(1680).": ".$fe_asignacion_califi;?></i></small></p> </div> 
											
											
																	
																   <script>
																	  function EditarDescripcion<?php echo $cont1;?>(fl_criterio) {
																	  
																			document.getElementById('desc_<?php echo $cont1;?>').disabled = false;
																			//$('#desc$cont1').removestyle('disabled')
																			
																			$('#btncancel<?php echo $cont1;?>').removeClass('hidden');
																			$('#btnsave<?php echo $cont1;?>').removeClass('hidden');
																			$('#char<?php echo $cont1?>').removeClass('hidden');//se hablita contador carateres.
																			
																	  }
																	 function CancelarEdicion<?php echo $cont1;?>(fl_calificacion_criterio) {

																			document.getElementById('desc_<?php echo $cont1;?>').disabled = true;
																			$('#btncancel<?php echo $cont1;?>').addClass('hidden');//botones desabilitados
																			$('#btnsave<?php echo $cont1;?>').addClass('hidden');//botones desabilitados
																			$('#char<?php echo $cont1;?>').addClass('hidden');//se hablita contador carateres.

																	 }
																	 
																	 
																	 function GuardarDescripcion<?php echo $cont1;?>(fl_criterio) {

																			var ds_descripcion = document.getElementById('desc_<?php echo $cont1;?>').value;
																			var fl_criterio=fl_criterio;											        
																			var fl_alumno='<?php echo $cl_sesion; ?>';
																			var fl_programa=<?php echo $fl_programa; ?>;
																			var fg_comentario_crietrio = 1;
																			var rangeInput = document.getElementById('ex<?php echo $cont1;?>').value;

																				$.ajax({
																					type: 'POST',
																					url: 'guardar_comentarios_criterio.php',
																					data: 'ds_descripcion='+ds_descripcion+
																						  '&fl_alumno='+fl_alumno+
																						  '&fl_programa='+fl_programa+
																						  '&rangeInput='+rangeInput+
																						  '&fg_comentario_crietrio='+fg_comentario_crietrio+
																						  '&fl_criterio='+fl_criterio,
																													   
																					async: true,
																					success: function (html) {

																						$('#muestra_save<?php echo $cont1;?>').html(html);

																					}


																				});

																				document.getElementById('desc_<?php echo $cont1;?>').disabled = true;
																				$('#btncancel<?php echo $cont1;?>').addClass('hidden');//botones desabilitados
																				$('#btnsave<?php echo $cont1;?>').addClass('hidden');//botones desabilitados
																				$('#char<?php echo $cont1;?>').addClass('hidden');//se hablita contador carateres.

																	 }
																	 function CuentaCarteres<?php echo $cont1;?>() {
																			
																			var comentario=document.getElementById('desc_<?php echo $cont1;?>').value.length;
																			var este = 130 - comentario;
																			//alert(comentario);
																			$('#char<?php echo $cont1;?>').html(este);
																			//alert(este);
																	 }
																   
																   </script>
											
											
											
											
											
											
																 </div>
															  </div>
											  
															
															
															
															
															
															
															
															
															
															
											
													</div>
													
													
													
											
													</div><!--end 11.-->
											</div><!---end row-->
											<style>
											   #ex<?php echo $cont1;?>Slider .slider-selection {
													background: #0092DC !important;
												}

											</style>
											
											<div class='row'>
													<div class='col-md-1' style='padding-right: 0px;'> &nbsp;</div>
													<div class='col-md-9 ' style='padding-left: 1px; padding-right:1px; '> 
														<input id='ex<?php echo $cont1;?>' class='slider slider-primary' data-slider-id='ex<?php echo $cont1;?>Slider' type='text' data-slider-min='0' data-slider-max='100'  data-slider-step='1' data-slider-value='<?php echo $no_porcentaje_equivalente;?>'/>
													
														<!--<input id="ex99"  class="slider slider-primary" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="14"/>-->


													</div>
													<div class='col-md-2'>&nbsp;</div>
											</div>
											
												
											<script>
												$(document).ready(function () {
														// Without JQuery
														var slideStart = false;
														var slider = new Slider('#ex<?php echo $cont1;?>', {
															formatter: function(value) {
															return 'Current value: ' + value;
															}
														});
														
														slider.on('slideStart', function () {
															// Set a flag to indicate slide in progress
															slideStart = true;
															// Clear the timeout
															// clearInterval(refreshId);
															// alert('inicia');
														});
												
														slider.on('slideStop', function (value) {
															// Set a flag to indicate slide not in progress
															slideStart = false;
															
															
															 var rangeInput = value;
															 var fl_criterio=<?php echo $fl_criterio; ?>;//identificador del criterio
															 var fl_alumno="<?php echo $cl_sesion;?>";
															 var fl_programa=<?php echo $fl_programa;?>;
															 var fg_calcula_promedio=1;
															 var peso_criterio=<?php echo $no_valor_criterio;?>;
															
															 $('#final_<?php echo $cont1;?>').data('easyPieChart').update(rangeInput);		
															 $('#span_final<?php echo $cont1;?>').html(rangeInput);
															
															
															
															// alert('stop'+value);
															 
															 $.ajax({
																type: 'POST',
																url: 'guardar_rango_calcular_calificacion.php',
																data: 'rangeInput='+rangeInput+
																	  '&fl_alumno='+fl_alumno+
																	  '&fl_programa='+fl_programa+
																	  '&peso_criterio='+peso_criterio+
																	  '&fl_criterio='+fl_criterio,

																async: true,
																success: function (html) {
																    //alert('entro3');	
																	$('#presenta_calculo').html(html);
																}
															});
															 
															 
															 
															 
															
														});
												
												

												
												 
												   
															 

															
													
													
													
																											
												});

											</script>
											
											
											<script>
  	
													function ObtenValor<?php echo $cont1;?>(){
	
													}
 
											</script>
											
											
											
									<?php
										}#end primer query
										
										#Presenta comentarios finales del teacher
									   if(!empty($fg_esta_calificado_admin)){
											$Query="SELECT ds_comentarios,fe_modificacion FROM c_com_criterio_admin 
											WHERE  cl_sesion='$cl_sesion' AND fl_programa=$fl_programa AND fg_com_final='1'  ";
											$row=RecuperaValor($Query);
											$ds_comentario_final=str_texto($row[0]);
											$fe_comentario_final=ObtenFechaFormatoDiaMesAnioHora($row[1]);
											
											#Recupermaos la calificCION FINAL:
											$Que="SELECT no_calificacion FROM k_calificacion_admin WHERE cl_sesion='$cl_sesion' AND fl_programa=$fl_programa  ";
											$r=RecuperaValor($Que);
											$no_clificacion_final=$r['no_calificacion'];
											
									   
									    }else{
										    $ds_comentario_final="";
										    $fe_comentario_final="";
										    $no_clificacion_final=0;
										
										} 
										
										
										
									?>
											<div class='row padding-10'>
													<div class='col-md-10'>
														<div class='col-md-12'>  
																 <a href='javascript:void(0);' class='btn btn-xs btn-default pull-right' style='border:0px;' Onclick='EditarDescripcionFinal(<?php echo $fl_identificador;?>);'><i class='fa fa-pencil' ></i></a>
																	<textarea class='form-control' rows='4' id='desc_teacher'  placeholder='<?php echo ObtenEtiqueta(1668);?>' style='resize:none !important;' maxlength='130' disabled><?php echo $ds_comentario_final;?></textarea>
																	
																	
																	<div class='col-md-4 text-left' id='muestra_save_final'><small class='text-muted'><i><?php echo ObtenEtiqueta(1680).": ".$fe_comentario_final; ?></i></small></div>

																				 <div class='col-md-4 text-center' ><br/>    
																							
																						  <a href='javascript:void(0);' class='btn btn-primary' style='border-radius:10px;' id='boton_final' onclick='GuardarTranscript();'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(1669);?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
									
																				 </div>
																				 
																				 <div class='col-md-4'>
																						<br/>
																						<div class='form-group' style='float:right;margin:6px;'>
																						<a class='btn btn-primary btn-xs' href='javascript:void(0);' style='float:right;font-size: 13px;' id='btnsavefinal' Onclick='GuardarDescripcionFinal(<?php echo $fl_identificador;?>);'>Save</a>
																						</div>
																						<div class='form-group' style='float:right;margin:6px;'>
																						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																						 <a class='btn btn-default btn-xs' href='javascript:void(0);' style='float:right;font-size: 13px;' id='btncancelfinal' Onclick='CancelarEdicionFinal(<?php echo $fl_identificador;?>);'>Cancel</a>
																						</div>
																				</div>
														</div>			 
																		 
													</div>	

													<div class='col-md-2'>
																		
															<div class='panel panel-default'>
																<div class='panel-body text-center'>
								  
																	<div id='presenta_calculo' name='presenta_calculo'>
																		<div class='chart' data-percent='<?php echo $no_clificacion_final;?>' data-barColor="#B7B7B7"  id='char_final'>
																			<span class='percent' style='font:18px Arial;font-weight:none !important;' id='span_char_final'><?php echo $no_clificacion_final;?> </span>
																		</div>
																	</div>
																	<hr />		
																	<b><?php echo ObtenEtiqueta(1671);?></b>
										
																</div>
															</div> 
																	
								   
													</div>



													
											</div>
									
			

												<script> 
													$(document).ready(function () {

															$('#char_final').easyPieChart({
																animate: 2000,
																scaleColor: false,
																lineWidth: 7.5,
																lineCap: 'square',
																size: 100,
																trackColor: '#EEEEEE',
																barColor: '#92D099'
															});

															$('#char_final').css({
																width: 100 + 'px',
																height: 100 + 'px'
															});
															$('#char_final .percent').css({
																'line-height': 100 + 'px'
															})
														
													});
												</script>
																	
												<script>
														$(document).ready(function () {

															$('#btncancelfinal').addClass('hidden');
															$('#btnsavefinal').addClass('hidden');
															$('#charfinal').addClass('hidden');//se hablita contador carateres.
															document.getElementById('desc_teacher').disabled = true;

														});
														function EditarDescripcionFinal(fl_identificador) {


															document.getElementById('desc_teacher').disabled = false;
															$('#btncancelfinal').removeClass('hidden');
															$('#btnsavefinal').removeClass('hidden');
															$('#charfinal').removeClass('hidden');//se hablita contador carateres.
															$('#boton_final').addClass('hidden');
														}

														function CancelarEdicionFinal(fl_identificador) {

															document.getElementById('desc_teacher').disabled = true;
															$('#btncancelfinal').addClass('hidden');//botones desabilitados
															$('#btnsavefinal').addClass('hidden');//botones desabilitados
															$('#charfinal').addClass('hidden');//se hablita contador carateres.
															 $('#boton_final').removeClass('hidden');
																
														}
														
														
														function GuardarDescripcionFinal(fl_identificador) {

															$('#boton_final').removeClass('hidden');
														
															var ds_comentarios = document.getElementById('desc_teacher').value;
															var fl_alumno ='<?php echo $cl_sesion;?>';
															var fl_programa =<?php echo $fl_programa;?>;

															var comen_final = 1;
															// var no_calificacion = document.getElementById('final_total').value;
															
															$.ajax({
																type: 'POST',
																url: 'guardar_comentarios_criterio.php',
																data: 'ds_comentarios='+ds_comentarios+
																		'&fl_alumno='+fl_alumno+
																		'&comen_final='+comen_final+
																		'&fl_programa='+fl_programa,

																async: true,
																success: function (html) {

																	$('#muestra_save_final').html(html);

																}

															});

															document.getElementById('desc_teacher').disabled = true;
															$('#btncancelfinal').addClass('hidden');//botones desabilitados
															$('#btnsavefinal').addClass('hidden');//botones desabilitados
															$('#charfinal').addClass('hidden');//se hablita contador carateres.

														}
														
														
														
														
														function GuardarTranscript() {

															var ds_comentarios = document.getElementById('desc_teacher').value;
															var fl_alumno ='<?php echo $cl_sesion;?>';
															var fl_programa=<?php echo $fl_programa;?>;
															var fg_guardar_todo = 1;

															
															$.ajax({
																type: 'POST',
																url: 'guardar_comentarios_criterio.php',
																data: 'ds_comentarios='+ds_comentarios+
																		'&fl_alumno='+fl_alumno+
																		'&fg_guardar_todo='+fg_guardar_todo+
																		'&fl_programa='+fl_programa,

																		async: true,
																		success: function (html) {
																			  
																		 
																				$.smallBox({
																					title : '<i class=\"fa fa-check\" aria-hidden=\"true\"></i><?php echo ObtenEtiqueta(1672);?>',
																					content : '&nbsp;',
																					color : '#739E73',
																					timeout: 4000,
																					iconSmall : 'fa fa-check ',
																					//number : '2'
																				});
																			$('#muestra_save_final').html(html);
																		}
																		
																		

															});
															
															
															


														}
														
													</script>
									
<script>
    pageSetUp();
</script>									
									


	
	
	
	
	
	
	
	
	