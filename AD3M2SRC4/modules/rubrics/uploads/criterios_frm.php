<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CURSOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
        $Query  = "SELECT nb_criterio,no_porcentaje ";
        $Query .= "FROM c_criterio WHERE fl_criterio=$clave  ";
        $row = RecuperaValor($Query);
	  
        $nb_criterio = str_texto($row[0]);
        $no_porcentaje = str_texto($row[1]);
    

    }
    else { // Alta, inicializa campos
      $nb_criterio = "";
      $no_porcentaje = "";
     
      #eLIMINAMOS EL CRITERIO que esten en null
      $Query="DELETE FROM k_criterio_fame WHERE fl_criterio IS NULL    ";
      EjecutaQuery($Query);
	  
	   #eLIMINAMOS archivos que esten en null
      $Query="DELETE FROM c_archivo_criterio WHERE fl_criterio_fame IS NULL    ";
      EjecutaQuery($Query);
      
      
      
    }

  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_criterio = RecibeParametroHTML('nb_criterio');
    $no_porcentaje = RecibeParametroNumerico('no_porcentaje');
  

  }
  
 
    
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(165);
  
  echo "<script type='text/javascript' src='".PATH_JS."/frmCourses.js.php'></script>";
 echo"<style>
 .input-group .form-control {
   
    z-index: 1 !important;
    
    }
    </style>
 "; 
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  
  
  
 
  
 ?>

 <!--
    <link rel="stylesheet" type="text/css" href="fancybox-3.0/fancybox-3.0/dist/jquery.fancybox.css">
    <script src="//code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="fancybox-3.0/fancybox-3.0/dist/jquery.fancybox.min.js"></script>

-->



<input type="hidden" value="<?php echo $clave; ?>" id="fl_registro" name="fl_registro" />

       <!-- widget content -->
            <div class="widget-body">

               <!-- <div class="pull-right" style="border-top-width: 0!important; margin-top: 5px!important; font-weight: 700;">
                    <a class="btn btn-success btn-xs" href="javascript:void(0);" id="add_tab"><i class="fa fa-address-book"></i>Transcript</a> 
                                
                    <a class="btn btn-danger btn-xs" href="javascript:void(0);" id="tabs2"><i class="fa fa-address-card-o"></i> Certificate</a>&nbsp;&nbsp;
                </div>-->


                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#criterio" data-toggle="tab"><i class="fa fa-fw fa-lg fa-info"></i>Criterion</a>
                    </li>

                </ul>

                
                  
<style>
.button input[type="text"]{
    height:1.5em;
    width:7em;
    -webkit-transform: rotate(-90deg); 
    -moz-transform: rotate(-90deg); 
    font-size:1.5em;
    border:0 none;
    background:none;
}



.dropzone .dz-default.dz-message {
background-image: url(../img/dropzone/spritemap.png) !important;
width: 1 px !important;
height: 1px !important;
}
    .font {
   
        
        color:#333 !important;
        font: 18x Arial !important;
        font-weight:100 !important;
         }

</style>


                <div id="myTabContent1" class="tab-content padding-10 no-border">
                     <div class="tab-pane fade in active" id="criterio">
            
                         <div class="row">

							<div class="col-md-2">
									
										<div class="panel panel-default">
									      <div class="panel-body text-center">
												<!--<div class="button">-->
														<div class="form-group" style="align:left;"><a href="javascript:void(0);" class="btn btn-xs btn-default pull-right" style="border:0px;" Onclick="EditarNombreCriterio();"><i class="fa fa-pencil" ></i></a>
														  <label for="usr" >Name:</label>
														  <input type="text" class="form-control" name="nb_criterio"  id="nb_criterio" style="width:90%;"  value="<?php echo $nb_criterio ?>"   />
														</div>
														
											<!--			
												</div>	-->


												<div class="form-group">
												  <label for="usr">Porcentaje %</label>
												  <input type="text" class="form-control" name="no_porcentaje" id="no_porcentaje" style="width:90%;" value="<?php echo $no_porcentaje ?>" />
												</div>												
												
												
										  </div>
										
											<!--<div class="form-group">
												<div class="col-md-6">
												<a class="btn btn-default btn-xs" href="javascript:void(0);" id="btncancelar" Onclick="CancelarNombreCriterio();">Cancel</a>
												</div>
											
												<div class="col-md-6"> 
													<a class="btn btn-primary btn-xs" href="javascript:void(0);"  id="btnsaves" Onclick="GuardarNombreCriterio();">Save</a>
												</div>
											
											</div>-->
										
										
										
										</div>
										
									
								
							
							</div>
						 
						 
						 <?php 
						 #Recupermos las calificaciones existentes
						 $contador=0;
						 
                         
                         
                         $Query="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max 
								FROM c_calificacion_criterio ";
								
						  $Query.="	WHERE 1=1 ORDER BY fl_calificacion_criterio DESC ";
						 $rs = EjecutaQuery($Query);
                         
                         
                      //echo"   
                      // <input type='hidden' name='nb_archivos' id='nb_archivos' value=''> ";
                         
                         
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
							$ds_equivalencia=$no_min."%-".$no_max." ($cl_calificacion)";
							
                            
                            
                            #Recupermaos la descripcion que tiene actualmente.
                            $Query="SELECT ds_descripcion,fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$clave AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
                            $row=RecuperaValor($Query);
                            $ds_desc=str_texto($row[0]);
                            $fl_criterio_fame=$row[1];
                            
							
                            
                            #Recuperamos las imagenes por calificacion
                            $Query="SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame ";
                            $row=RecuperaValor($Query);
                            $nb_archivo_criterio=$row[0];
                            
                            $src_img="../../images/rubrics/".$nb_archivo_criterio;
                            
                            
							$contador ++;
							
							
							
						 ?>
						 
							<div class="col-md-2">				
							  
							    <div class="well well-lg text-center" style='padding: 2px;'>
									<?php echo $ds_calificacion;  ?>
								</div>
							  
							    <br/>
							  
								<div class="panel panel-default">
									<div class="panel-body text-center">
									    <span href="" ><?php echo $ds_equivalencia;  ?> </span>   <p>&nbsp;</p>
									  

										<div class="knobs-demo">
											
											
											
											
											<div>
												<input class="knob<?php echo $contador; ?> font"     value="<?php echo $no_max; ?>"  disabled/>
											</div>
										</div>
									  
										<div class="form-group"><a href="javascript:void(0);" class="btn btn-xs btn-default pull-right" style="border:0px;" Onclick="EditarDescripcion<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);"><i class="fa fa-pencil" ></i></a>
											<!--<label for="comment">Comment:</label>-->
											<textarea class="form-control" rows="3"  id="desc<?php echo $contador; ?>" style="resize:none !important;" maxlength="130" > <?php echo $ds_desc  ?> </textarea>
										</div>
	
										<div class="form-group">
											<div class="col-md-6">
											<a class="btn btn-default btn-xs" href="javascript:void(0);" id="btncancel<?php echo $contador; ?>" Onclick="CancelarEdicion<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);">Cancel</a>
											</div>
										
											<div class="col-md-6"> 
												<a class="btn btn-primary btn-xs" href="javascript:void(0);"  id="btnsave<?php echo $contador; ?>" Onclick="GuardarDescripcion<?php echo $contador; ?>(<?php echo $fl_calificacion_criterio; ?>);">Save</a>
											</div>
										
										</div>
											
										
								<?php
                                
                                #Inicia dropzone
								$nombre= 'dropzone_'.$contador;
                                
								 echo "
				  
										      <input type='hidden' name='nb_archivo_$contador' id='nb_archivo_$contador' value=''>
										 
										      <div class='widget-body'>";
											echo "<div class='dropzone' id='{$nombre}' style='min-height: 100px;'>  </div>";
										  echo "</div>";
								
                                          
                                               
                                          
                                          
                                          #<!------finaliza dropzone-------->	
                                          
                           if(!empty($nb_archivo_criterio)){               
								#presenta preview imagen		  
                                
                               
                               //echo"
                               // <a href='$src_img' data-fancybox='group' data-caption='Caption #1_$contador'>
                               //     <img src='$src_img' alt=''  width='50px;' height='30px;' />
                               // </a>
                               //";
                               
                               
                               echo"<a class='zoomimg' href='#'> 
                                        <img src='$src_img' class='away no-border' width='40px' height='40px'>
                                            <span style='left:-300px;'>
                                                <div class='modal-dialog demo-modal' style='width:500px;height:500px;'>
                                                    <div class='modal-content' style='width:500px;height:500px;'>
                                                        <div class='modal-body padding-5'  style='width:500px;height:500px;'>
                                                            <img class='superbox-current-img' src='$src_img' style='width:494px;height:490px;'>
                                                             
                                                        </div>
                                                    </div>
                                                </div>
                                            </span>
                                      </a> ";
                               
    
                           }
										  
										  
										  
										  
										   echo "<script type='text/javascript'>
														// DO NOT REMOVE : GLOBAL FUNCTIONS!
														$(document).ready(function() {
														  pageSetUp();
														  Dropzone.autoDiscover = false;
														  
														  $('#{$nombre}').dropzone({
															url: 'upload.php', ";
                                         if(!empty($fl_criterio_fame)){  
								        echo"	params: {fl_criterio_fame:$fl_criterio_fame},  ";
                                        
                                         }
										echo"	
                                                            // data:  'id=1',
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
															complete: function(file){
															  if(file.status == 'success'){
                                                                 prev = $('#nb_archivos').val();
																// alert('El siguiente archivo ha subido correctamente: ' + file.name);
																document.getElementById('nb_archivo_$contador').value = file.name;
                                                               
																	if (prev != '')
																		$('#nb_archivos').val(prev + ',' + file.name);
																	else
																		$('#nb_archivos').val(file.name);
															  }
															},
															// error: function(file){
															  // alert('Error subiendo el archivo ' + file.name);
															// },
															removedfile: function(file, serverFileName){
															  var name = file.name;
															 
															  var element;
															  (element = file.previewElement) != null ? 
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
										 document.getElementById("desc<?php echo $contador;?>").disabled = true;//tofos al cargar el document estan desaibiltados
										  $("#btncancel<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
								          $("#btnsave<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
										//<!--propiedades del input knob -->
												$('.knob<?php echo $contador; ?>').knob({
												  'width':100,
												  'height':100,
												  'angleArc':360,
												  'thickness':0.16,
												  'cursor':false,
												  'readOnly':false,
												  'angleOffset':50,
												  'fgColor':'#92D099'
											   
												});
								
									    });

								
									//<!--MJD funcion para editar la descripcion de los criterios-->
									function EditarDescripcion<?php echo $contador; ?>(fl_calificacion_criterio){
										 
										 
										  document.getElementById("desc<?php echo $contador; ?>").disabled = false;
										  $("#btncancel<?php echo $contador; ?>").removeClass('hidden');
										  $("#btnsave<?php echo $contador; ?>").removeClass('hidden');
										  
									}
								   // <!---funcion para inabilitra la edicion de la descripcion del criterio-->
									function CancelarEdicion<?php echo $contador; ?>(fl_calificacion_criterio){
									
											 document.getElementById("desc<?php echo $contador; ?>").disabled = true;
											 $("#btncancel<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
								             $("#btnsave<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
											 
									}
								
								
									function GuardarDescripcion<?php echo $contador; ?>(fl_calificacion_criterio){
									
									        var ds_descripcion = document.getElementById("desc<?php echo $contador; ?>").value;
									        var fl_calificacion=fl_calificacion_criterio;
											var clave=document.getElementById("fl_registro").value;
											    $.ajax({
													type: 'POST',
													url: 'guardar_descripcion_criterio.php',
													data: 'ds_descripcion='+ ds_descripcion +
													      '&fl_registro='+clave +
														  '&fl_calificacion='+fl_calificacion ,
													async: true,
													success: function (html) {
														
														 $('#muestra_save').html(html);

													}

												});
											 document.getElementById("desc<?php echo $contador; ?>").disabled = true;
											 $("#btncancel<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
								             $("#btnsave<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
											
									}
								
								
								

								    
								</script>
							
							


							
							
							
							
							<?php
							
							}
							?>
 
 
                         </div>
                     



                     </div>



  
		  
        </div>
             
    
						
                  
               
                </div>
			</div>
  
  
  
  
  
   
  
  

  
  
  
  
<script>
 $(document).ready(function () {
  //document.getElementById("nb_criterio").disabled = true;
  //document.getElementById("no_porcentaje").disabled = true;
  $("#btncancelar").addClass('disabled');//botones desabilitados
  $("#btnsaves").addClass('disabled');//botones desabilitados
 });
 
function EditarNombreCriterio(){

    document.getElementById("nb_criterio").disabled = false;
	document.getElementById("no_porcentaje").disabled = false;
	$("#btncancelar").removeClass('disabled');//botones desabilitados
   $("#btnsaves").removeClass('disabled');//botones desabilitados
}

function CancelarNombreCriterio(){
    document.getElementById("nb_criterio").disabled = true;
	document.getElementById("no_porcentaje").disabled = true;
	$("#btncancelar").addClass('disabled');//botones desabilitados
    $("#btnsaves").addClass('disabled');//botones desabilitados
}

function GuardarNombreCriterio(){
    document.getElementById("nb_criterio").disabled = true;
	document.getElementById("no_porcentaje").disabled = true;
	$("#btncancelar").addClass('disabled');//botones desabilitados
    $("#btnsaves").addClass('disabled');//botones desabilitados
            
}

</script>



  <?php

  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
      $fg_guardar = ValidaPermiso(FUNC_ALUMNOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
  
?>

    <script src="<?php echo PATH_LIB; ?>/self_paced/dropzone.min.js"></script>	


<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/knob/jquery.knob.min.js"></script><!---plugin necesario para pintar el circulo -->


 