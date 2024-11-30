<?php

# Aded by Ulises, select language and apply a sufix for the selection of the right lang-table on the DB
$langselect = $_COOKIE[IDIOMA_NOMBRE];

  switch ($langselect) {
    case '1': $sufix = '_esp';
      break;

    case '2': $sufix = '';
      break;

    case '3': $sufix = '_fra';
      break;
    
    default: $sufix = '';
      break;
  }

#Recuperamos el nombre 
$Query="SELECT  A.ds_titulo".$sufix;
$Query.=", B.nb_programa".$sufix." FROM  c_leccion_sp A
JOIN c_programa_sp B ON B.fl_programa_sp=A.fl_programa_sp
WHERE A.fl_leccion_sp=$fl_leccion_sp  ";
$row=RecuperaValor($Query);
$ds_leccion= htmlentities($row[0], ENT_QUOTES, "UTF-8"); //str_texto($row[0]);
$ds_programa=str_texto($row[1]);
$nb_rubric=$ds_leccion;

$fl_leccion_sp_raiz=$fl_leccion_sp;

#Verificamos si ya esta calificado por el teacher.(si tiene fl_promedio semna,quees llave que hace refrencia c_calificacion_sp)
$Query="SELECT fl_promedio_semana,fg_revisado_alumno FROM k_entrega_semanal_sp WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp ";
$row=RecuperaValor($Query);
$fg_calificado_teacher=$row['fl_promedio_semana'];
$fg_revisado_alumno=$row['fg_revisado_alumno'];



if(!empty($fg_calificado_teacher)){


		$Query="SELECT fe_modificacion FROM c_com_criterio_teacher WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp  AND fl_programa_sp=$fl_programa AND fg_com_final='1' ";
		$row=RecuperaValor($Query);
		$fe_modificacion=$row[0];

		$fe_modificacion=strtotime('+0 day',strtotime($fe_modificacion));
        $fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
        #DAMOS FORMATO DIA,MES, AÑO.
        $date = date_create($fe_modificacion);
        $fe_modificacion=date_format($date,'F j, Y, g:i a');

		#Recuperamos al teacher asosicado al programa
		$Query="SELECT U.ds_nombres,U.ds_apaterno,U.fl_usuario
				FROM k_usuario_programa P  
				JOIN c_usuario U ON U.fl_usuario=P.fl_maestro 
				WHERE fl_usuario_sp=$fl_alumno AND fl_programa_sp=$fl_programa  ";
		$row=RecuperaValor($Query);
		$nb_teacher=str_texto($row[0])." ".str_texto($row[1]);		
				
		
		
        if($fg_revisado_alumno){
            $checked1="hidden";
		    $checked2="";	
        }else{
            $checked1="";
			$checked2="hidden";
        }
        
        #Guardamos la confirmacion de visto, maracado como leido  , por el estudiante. 
        $Query="UPDATE k_entrega_semanal_sp SET  fg_revisado_alumno='1'  WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp ";
        EjecutaQuery($Query);
        
		$fe_calificado="<p class='text-left' style='font-weight:normal !important; letter-spacing: 0px;!important;'><b>".ObtenEtiqueta(1673).":</b> $fe_modificacion  </p>";
		$nb_teacher="<p class='text-left' style='font-weight:normal !important; letter-spacing: 0px;!important;'><b>".ObtenEtiqueta(1675).":</b> $nb_teacher </p>";
		$check="
        <!----
            <span class='button-checkbox'>
                <button type='button' class='btn' data-color='success'>".ObtenEtiqueta(1686)."</button>
                <input type='checkbox' class='hidden' />
            </span>
            
        
        
                <script>
            
                 $(function () {
                    $('.button-checkbox').each(function () {
                    // Settings
                        var widget = $(this),
                            button = widget.find('button'),
                            checkbox = widget.find('input:checkbox'),
                            color = button.data('color'),
                            settings = {
                                on: {
                                    icon: 'glyphicon glyphicon-check'
                                },
                                off: {
                                    icon: 'glyphicon glyphicon-unchecked'
                                }
                            };

                        // Event Handlers
                        button.on('click', function () {
                            checkbox.prop('checked', !checkbox.is(':checked'));
                            checkbox.triggerHandler('change');
                            updateDisplay();
                        });
                        checkbox.on('change', function () {
                            updateDisplay();
                        });

                        // Actions
                        function updateDisplay() {
                            var isChecked = checkbox.is(':checked');

                            // Set the button's state
                            button.data('state', (isChecked) ? 'on' :  'off');

                            // Set the button's icon
                            button.find('.state-icon')
                                .removeClass()
                                .addClass('state-icon ' + settings[button.data('state')].icon);

                            // Update the button's color
                            if (isChecked) {
                                button
                                    .removeClass('btn-default')
                                    .addClass('btn-' + color + ' active');
                            }
                            else {
                                button
                                    .removeClass('btn-' + color + ' active')
                                    .addClass('btn-default');
                            }
                        }

                        // Initialization
                        function init() {

                            updateDisplay();

                            // Inject the icon if applicable
                            if (button.find('.state-icon').length == 0) {
                                button.prepend('<i class=\"state-icon ' + settings[button.data('state')].icon + '\"></i> ');
                            }
                        }
                        init();
    
                                    });
                            });
                    </script>
            ------>
            
        <a href='javascript:void(0);' style='letter-spacing: 0px;!important' onclick='MarcarLeido();' class='btn btn-labeled btn-success txt-color-white $checked1' id='btn_check'> <span class='btn-label'><i class='fa fa-check-square-o' aria-hidden='true'></i></span>".ObtenEtiqueta(1686)." </a>
				<a href='javascript:void(0);' style='letter-spacing: 0px;!important' onclick='MarcarNoLeido();' class='btn btn-labeled btn-danger txt-color-white $checked2' id='btn_check2'> <span class='btn-label'><i class='fa fa-check-square-o' aria-hidden='true'></i></span>".ObtenEtiqueta(1688)." </a>		

						<script>
						function MarcarLeido(){
						  $('#btn_check2').removeClass('hidden');
						  $('#btn_check').addClass('hidden');
						
						  var fg_leido=1;
						  var fl_alumno=$fl_alumno;
						  var fl_leccion_sp=$fl_leccion_sp;

							$.ajax({
								type:'POST',
								url: 'site/marcar_leido.php',
								data:'fg_leido='+fg_leido+
									 '&fl_alumno='+fl_alumno+
									 '&fl_leccion_sp='+fl_leccion_sp,
							    async: false,
								success: function (html) {
									$('#mostrar_leido').html(html);
									}
									
									
									});
						
						
						
						
						}
						function MarcarNoLeido(){
						
						  $('#btn_check').removeClass('hidden');
						  $('#btn_check2').addClass('hidden');
						
						  var fg_leido=0;
						  var fl_alumno=$fl_alumno;
						  var fl_leccion_sp=$fl_leccion_sp;
						

							$.ajax({
								type:'POST',
								url: 'site/marcar_leido.php',
								data:'fg_leido='+fg_leido+
									 '&fl_alumno='+fl_alumno+
									 '&fl_leccion_sp='+fl_leccion_sp,
							    async: false,
								success: function (html) {
									$('#mostrar_leido').html(html);
									}
									
									
									});
						
						
						
						
						}
						
						</script>
						
						


		";

		
		$Query="";
		
		
}else{
$fe_calificado="";
}




$ds_style="




	<!----------librerias para presentar sliders Barra azul--------->
	<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.min.css' />
	<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.css' />
	<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.js' ></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.min.js' ></script>
						
		
	<style>

	    <!---estos stylos chocaron al pasarlos aqui-->
		
		.b, strong {
		   font-weight: none !important;
		}

		
		.h2{
		
		font-size: 13px !important;
		letter-spacing :!important;
		}
		<!----->
	
		/*Para aumentar tamaño de las imagenes solo con pasar el mouse*/
			.zoomimg {position: relative; z-index: 150; }
			.zoomimg:hover{ background-color: transparent; z-index: 150; }
			.zoomimg span{ /* Estilos para la imagen agrandada */
			position: absolute;
			/*background-color: black;*/
			padding: 5px;
			left: -50px;
			/*border: 5px double gray;*/
			visibility: hidden;
			color: #000;
			width:600px;
			/*text-decoration: none;*/
			}
			.zoomimg span img{ border-width: 0; padding: 2px; width:600%; height:600%; }
			.zoomimg:hover span{ visibility: visible; top: 0; /*left: -10px;*/ }
	
	
	
		/*stylos para slider*/ 
		.slider-selection {
			background-image: linear-gradient(to bottom, #3194DA 0%, #3194DA 100%)!important;
		}

		.ui-slider-horizontal {
			background: #D5D5D500 !important;
		}
		.ui-slider-horizontal .ui-slider-handle:hover {
		background-color: rgba(255, 255, 255, 0) !important;
		}
		.ui-slider-horizontal .ui-slider-handle:focus {
		background-color: rgba(255, 255, 255, 0) !important;
		}
		.ui-slider-horizontal {
		background: rgba(213, 213, 213, 0) !important;
		}
		.slider.slider-horizontal .slider-handle {
		margin-top: -1px !important;
		}
						
		/*estilos para el textatera desaibiltado*/
		input[type=text]:disabled {
			background: #fff !important;
		}
		/***iconoimagen*/
		.popover{
								
		max-width: 490px !important;
		}

		/**para los text desabilitados*/
		.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {

		background-color: #fff !important;
		}

		.border{
									border: 2px solid  #3194DA;
								}
			.col-md-2 {
			
		   padding-left:2px;
		   padding-right:2px;
			}
			
			
		.a {
		color: #323333;
		}
		h1, h2, h3, h4 {
			font-family: 'Open Sans',Arial,Helvetica,Sans-Serif ;
			font-size: 13px !important;
	
			color: #333;
			letter-spacing: none !important;
		}
	
		.well {
			font-family: 'Open Sans',Arial,Helvetica,Sans-Serif  !important;
			font-size: 13px !important;
			color: #333;
			letter-spacing: none !important;
			font-weight:normal !important;
		}
        
        
        .chart {
                    /* height: 220px; */
                    margin: auto !important;
               }

        .easyPieChart {
                position: relative;
                text-align: center !important;
             }
        
        .border{
				border: 2px solid  #3194DA;
				
		}
	</style>

	<div class='row'>	
	
		<div class='col-md-6'>
		    $fe_calificado 
			$nb_teacher
			
			
		</div>
		<div class='col-md-6 text-right'>
		  
		</div>
	</div>
	
	<div class='row'>
		<div class='col-md-12'>

			<div class='panel panel-default' style='border-radius:20px;'>
				<div class='panel-body text-center'>
				<p style='font-size:20px; letter-spacing:0px !important;'>$nb_rubric</p>
				</div>
			</div>
								
		</div>	
	</div>
		
		
		
						
						
";




if(!empty($fg_calificado_teacher)){#Presenta la rubric calificada.
    
            $ds_mensaje1="";
			
			#Verifica si la leccion es creada por el instituto.
			$Query="SELECT b.fl_instituto FROM c_leccion_sp a
					JOIN c_programa_sp b ON a.fl_programa_sp=b.fl_programa_sp where a.fl_leccion_sp=$fl_leccion_sp ";
			$rol=RecuperaValor($Query);
			$fl_leccion_de_instituto=$rol['fl_instituto'];


            #Se recuperan criterios.
            $Query="SELECT  K.fl_criterio,T.nb_criterio".$sufix.",K.no_valor,C.fl_leccion_sp,C.fl_programa_sp
							FROM  k_criterio_programa_alumno_fame K
							JOIN c_leccion_sp C ON C.fl_leccion_sp =K.fl_programa_sp
							JOIN c_criterio T ON T.fl_criterio=K.fl_criterio  
							AND EXISTS( SELECT 1  FROM c_com_criterio_teacher t where t.fl_leccion_sp=$fl_leccion_sp AND fl_alumno=$fl_alumno )
							WHERE K.fl_programa_sp=$fl_leccion_sp  AND K.fl_usuario_sp=$fl_alumno	";
            $rs = EjecutaQuery($Query);
			
			
            $registros = CuentaRegistros($rs);
			$contador_criterions=0;
            for($i=1;$row=RecuperaRegistro($rs);$i++) {
			
					$contador++;
					$contador_border1 ++;
					$contador_slider ++;
					$contador_comentario++;
				    $contador_img++;
				    $contador_criterions++;
				
					if($contador_img==1)
					$top_img="30px;";
					else
					$top_img="-530px;";
				
				
					$fl_criterio=$row['fl_criterio'];
					$nb_criterio= htmlentities($row['nb_criterio'.$sufix], ENT_QUOTES, "UTF-8");//str_texto($row['nb_criterio'.$sufix]);
					$no_porcentaje_criterio=$row['no_valor'];
					$fl_leccion_sp=$row['fl_leccion_sp'];
					$fl_programa_sp=$row['fl_programa_sp'];			
			
			
					$ds_mensaje1.="<div class='row'>";
			
												
							$ds_mensaje1.=" <div class='col-md-1' style='padding-right: 0px;'>
												
												
												<div class='col-md-12' style='padding-left: 1px;padding-right: 0px;' >
												
													<div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;letter-spacing: 0px;!important' >
														 Criterion 
													</div>
									
													<br/>
															  
													<div class='panel panel-default' style='height:380px;' id='divborder_cero_".$fl_criterio."_0'>
															<div class='panel-body text-center' id='divborder_cero".$fl_criterio."'>
																<span href='' style='color:#8FCAE5;font-size:15px;font-weight:normal!important;'>$no_porcentaje_criterio %</span>  <p>&nbsp;</p>

													
																
																<div class='form-group'><br/>
																	<label for='comment' style='writing-mode: vertical-lr;transform: rotate(180deg);font-size:16px; margin-top: 75px; font-weight:bold;'>$nb_criterio</label>
																	
																</div>

															</div>
													</div>
												
												
												
											
									  
												</div>
											 </div>	";
			
							$ds_mensaje1.=" <div class='col-md-11' style='padding-left: 1px;'>";
     
					#Recuperamos las escalas  que tiene el instituto
					$Queryc="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max 
					FROM c_calificacion_criterio ";
					$Queryc.="WHERE 1=1 ";
					if(!empty($fl_leccion_de_instituto)){
					$Queryc.=" AND fl_instituto=$fl_instituto ";
					}else{
					$Queryc.=" AND fl_instituto is null ";							
					}
					$Queryc.="ORDER BY no_equivalencia ASC ";
					$rsl = EjecutaQuery($Queryc);
					$contador_escalas=0;
					for($il=1;$rowl=RecuperaRegistro($rsl);$il++) {
						$fl_calificacion_criterio=$rowl['fl_calificacion_criterio'];
						$cl_calificacion=$rowl['cl_calificacion'];
						$ds_calificacion=$rowl['ds_calificacion'];
						$fg_aprobado=$rowl['fg_aprobado'];
						$no_equivalencia=$row1['no_equivalencia'];
						$no_min= number_format($rowl['no_min']);
						$no_max=number_format($rowl['no_max']);
						
						$contador_escalas++;
	 
	 
			
							#RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
							$Query1="SELECT C.fl_calificacion_criterio,C.ds_calificacion, K.ds_descripcion".$sufix;
							$Query1.=",C.no_min,C.no_max,C.cl_calificacion,K.fl_criterio_fame
														 FROM k_criterio_fame K
														 JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
														 WHERE fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=$fl_calificacion_criterio ";
							$rowcri=RecuperaValor($Query1);
							$ds_calificacion1=$rowcri[1];
							$ds_descripcion1= str_texto(!empty($rowcri[2])?$rowcri[2]:NULL);//$row[2];
							$no_min1=$rowcri[3];
							$no_max1=$rowcri[4];
							$cl_calificacion1=$rowcri[5];
							$fl_criterio_fame1=$rowcri[6];

                            #Sustitimos los saltos de linea.
                            $ds_descripcion1=str_replace("&lt;br &#47;&gt;", "\n", $ds_descripcion1);
			
			
							if($no_max1==0)
								$ds_equivalencia1="No Uploaded";
							else
								$ds_equivalencia1=number_format($no_min1)." % -".number_format($no_max1)." % ($cl_calificacion1)";	
							
							#Recuperamos las imagenes por calificacion
							$Query="SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame1 ";
							$row=RecuperaValor($Query);
							$nb_archivo_criterio1=$row[0];
						
							$src_img1="../AD3M2SRC4/images/rubrics/".$nb_archivo_criterio1;	
			
	

											if(!empty($nb_archivo_criterio1)){ 
									
												 $icono ="<a class='zoomimg' href='javascript:void(0);'> 
																<i class='fa fa-file-picture-o' style='color:#333;'></i>
																<span style='left:-300px;'>
																  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-top: $top_img'>
																	<div class='modal-content' style='width:500px;height:500px;'>
																	  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
																		<img class='superbox-current-img' src='$src_img1' style='width:494px;height:490px;'>
																	  </div>
																	</div>
																  </div>
																</span>
														   </a> ";
											
										
											 }else{											
											     $icono="";
											 }
												
							$ds_mensaje1.="  
												<div class='col-md-2' >
									
													<div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;letter-spacing: 0px;!important'>$ds_calificacion1 &nbsp; $icono			
										";																														
							$ds_mensaje1.="     	</div>";						
							$ds_mensaje1.="         <br/>
													<div class='panel panel-default' style='height:380px;' id='divborder_cero_'>
														<div class='panel-body text-center' id='divborder_cero".$fl_criterio."_".$fl_calificacion_criterio."'>
															<span  style='color:#8FCAE5;font-size:15px; letter-spacing:0px !important;font-weight:normal !important;' >$ds_equivalencia1</span>  <p>&nbsp;</p>

															<div class='chart' data-percent='$no_max1' id='easy-pie-chart".$fl_criterio."_".$fl_calificacion_criterio."'>
																<span class='percent' style='font:18px Arial;'>".number_format($no_max1)."</span>
															</div>															
															<hr />															
															<div class='form-group'>																
																<textarea class='form-control' rows='5'   style='resize:none !important;color:#999 !important;font-weight:normal !important;' maxlength='130' disabled>$ds_descripcion1</textarea>
															</div>

														</div>
													</div> ";
								
										
							$ds_mensaje1.="      </div>
												<script>
													$(document).ready(function () {

													
													
														$('#easy-pie-chart".$fl_criterio."_".$fl_calificacion_criterio."').easyPieChart({
															animate: 2000,
															scaleColor: false,
															lineWidth: 7.5,
															lineCap: 'square',
															size: 100,
															trackColor: '#EEEEEE',
															barColor: '#B7B7B7'
														});

														$('#easy-pie-chart".$fl_criterio."_".$fl_calificacion_criterio."').css({
															width: 100 + 'px',
															height: 100 + 'px'
														});
														$('#easy-pie-chart".$fl_criterio."_".$fl_calificacion_criterio." .percent').css({
															'line-height': 100 + 'px'
														})

													});
												</script>
									";
			
			
			
					}#end for escalas
			
					/*----------------PRESENTA cOMENATARIOS DEL TACHER----------*/

					$Query="SELECT ds_comentarios,no_porcentaje_equivalente,fe_modificacion  
							FROM c_com_criterio_teacher 
							WHERE fl_leccion_sp=$fl_leccion_sp_raiz AND fl_programa_sp=$fl_programa_sp AND fl_criterio=$fl_criterio and fl_alumno=$fl_alumno  ";
					$row=RecuperaValor($Query);
					$ds_comentario_teacher=$row[0];
					$no_porcentaje=$row[1];
							
					$fe_modificacion=ObtenFechaFormatoDiaMesAnioHora($row[2]);
					
					if(!empty($ds_comentario_teacher)){
						 $fe_modificacion=ObtenEtiqueta(1680).": ".$fe_modificacion;									
					}else{
						$fe_modificacion="";								
					}

			
			
				$ds_mensaje1.="
				
									<div class='col-md-2' >
											<div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;letter-spacing:0px !important;'>
												". ObtenEtiqueta(1664)."
											</div>
											<br/>
											<div class='panel panel-default' style='height:380px;'>
												<div class='panel-body text-center'>
													<span>&nbsp;</span><p>&nbsp;</p>
													
													 <div class='chart' data-percent='$no_porcentaje'  id='final_$contador_criterions'>
														<span class='percent'   style='font:18px Arial;font-weight:none !important;' id='span_final$contador_criterions'> ".$no_porcentaje." </span>
													 </div>


													<hr />
													
													<div class='form-group'>
													<span id='char$contador_criterions' class='pull-left text-left hidden' style=''></span>
													
														
														<textarea class='form-control' rows='5'  id='desc$contador_criterions' style='color:#999 !important;resize:none !important;font-weight:normal !important;font-style: italic;' maxlength='130' disabled>$ds_comentario_teacher</textarea>
													
														<h2 style='color:#999;margin: 2px 0;font-size:10px;' class='text-left'><i> $fe_modificacion </i></h2>
													</div>
							
												</div>
											</div>
									</div>
									
									
									<script>

										$(document).ready(function () {
												$('#final_$contador_criterions').easyPieChart({
													animate: 2000,
													scaleColor: false,
													lineWidth: 7.5,
													lineCap: 'square',
													value: '10',
													size: 100,
													trackColor: '#EEEEEE',
													barColor: '#92D099'
												});

												$('#final_$contador_criterions').css({
													width: 100 + 'px',
													height: 100 + 'px'
												});
												$('#final_$contador .percent').css({
													'line-height': 100 + 'px'
												})

										});
										
									</script>	
				
				
				";
				/*<!--------------Tremina comenatrios del teacher-------------->*/
					
			
			  $ds_mensaje1.="<div id='presenta_calculo_$contador_criterions'></div>
							<script>
										function PintaBorder_".$contador_criterions."(){
												 var rangeInput = $no_porcentaje;
												 var fl_criterio=$fl_criterio;//identificador del criterio
											     var fl_leccion_sp=$fl_leccion_sp_raiz;
												 $.ajax({
														type: 'POST',
														url: 'site/rubric_border.php',
														data: 'rangeInput='+rangeInput+
														      '&fl_leccion_sp='+fl_leccion_sp+
															  '&fl_criterio='+fl_criterio,

														async: true,
														success: function (html) {
															$('#presenta_calculo_$contador_criterions').html(html);
														}
													});

											
										}
							
							
														   PintaBorder_".$contador_criterions."();
							</script>";
			
			
			
			 $ds_mensaje1.="</div><!--end col--md-11--->
						</div><!--end col row--->";
			
			
			
			
			}#end for principal
    
    
    

	    #Comentarios generales del teacher
		$Query="SELECT ds_comentarios,no_calificacion 
				FROM k_calificacion_teacher
				WHERE fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp  AND fl_alumno=$fl_alumno ";
		$row=RecuperaValor($Query);
		$ds_comentario_final_teacher=$row[0];
		$no_porcentaje_final=$row[1];
		
		
		
		$Query="SELECT fe_modificacion,ds_comentarios
				FROM c_com_criterio_teacher 
				WHERE fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp AND fl_alumno=$fl_alumno  AND fg_com_final='1' ";
		$row=RecuperaValor($Query);
		$fe_modificacion=$row[0];
		$ds_comentario_final_teacher=$row[1];	
		$fe_modificacion=strtotime('+0 day',strtotime($fe_modificacion));
        $fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
        #DAMOS FORMATO DIA,MES, AÑO.
        $date = date_create($fe_modificacion);
        $fe_modificacion=date_format($date,'F j , Y');
		
		
		
		if(!empty($ds_comentario_final_teacher)){
		
		$ds_comentario_final_teacher=$ds_comentario_final_teacher;
		}else{
		
		$ds_comentario_final_teacher="";
		
		}
		
		
		$ds_mensaje2.="<div class='row'>
		    <div class='col-md-12'>
			    <div class='col-md-10'>	
			    
				    <textarea class='form-control' rows='4'  id='desc_teacher'  style='color:#999 !important;resize:none !important;font-weight:normal !important;' maxlength='130' disabled>$ds_comentario_final_teacher</textarea>	
					<h2 style='margin: 2px 0;font-size:10px;color:#999;' class='text-left'>".ObtenEtiqueta(1680).": <i>$fe_modificacion</i></p>
			    </div>
                
                
                <div class='col-md-2'>
								<div class='panel panel-default'>
									<div class='panel-body text-center'>
											
												<div class='chart' style='font-weight:normal!important;letter-spacing:0px !important;'  data-percent='$no_porcentaje_final' id='final_total'>
                                                    <span class='percent' style='font:18px Arial; font-weight:none !important;'>".number_format($no_porcentaje_final)."</span>
                                                </div>
									<hr />		
									<b style='letter-spacing:0px !important;'>".ObtenEtiqueta(1671)."</b>
									</div>
								
			    </div>
                
                
                
                
            </div>
            
            
            
			
							<script>
					$(document).ready(function () {
					$('#final_total').easyPieChart({
							animate: 2000,
							scaleColor: false,
							lineWidth: 8,
							lineCap: 'square',
							value:'10',
							size: 100,
							trackColor: '#EEEEEE',
							barColor: '#92D099'
						});

						$('#final_total').css({
							width: 100 + 'px',
							height: 100 + 'px'
						});
						$('#final_total .percent').css({
							'line-height': 100 + 'px'
						})

					});
				</script>
			
								
							
</div>
            
            
            
		
		</div>
		";
	
	

        
   }else{#Presneta como se va a clificar
   
       #Verifica si la leccion es creada por el instituto.
       $Query="SELECT b.fl_instituto FROM c_leccion_sp a
				JOIN c_programa_sp b ON a.fl_programa_sp=b.fl_programa_sp where a.fl_leccion_sp=$fl_leccion_sp ";
       $rol=RecuperaValor($Query);
	   $fl_leccion_de_instituto=$rol['fl_instituto'];
   
   
       $contador_img=0;
       #Seleccionamos los criterios que pertencen ala leccion
       $Query_prin = "SELECT fl_criterio, no_valor FROM k_criterio_programa_fame WHERE fl_programa_sp= $fl_leccion_sp ";
       $rs_prin = EjecutaQuery($Query_prin);

       $tot_reg=CuentaRegistros($rs_prin);
       
       for($i_prin=1;$row_prin=RecuperaRegistro($rs_prin);$i_prin++) {
         $fl_criterio = $row_prin[0];
         $no_valor_criterio = $row_prin[1];

        $contador_img++;
		
		
		  $rs_nb_crit = RecuperaValor("SELECT nb_criterio".$sufix." FROM c_criterio WHERE fl_criterio = $fl_criterio");
          $nb_crit = htmlentities($rs_nb_crit[0], ENT_QUOTES, "UTF-8");//str_texto($rs_nb_crit[0]);
		  
		  
		  
		  
		  	$ds_mensaje1.="<div class='row' style='padding-left:60px;'>";
			$ds_mensaje1.="
							

							
							";
			
			$ds_mensaje1.="  <div class='col-md-1' style='padding-right: 0px;'>
									
							
								<div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;letter-spacing: 0px;!important' >
									 Criterion 
								</div>
				
								<br/>
										  
								<div class='panel panel-default' style='height:370px;'>
										<div class='panel-body text-center' >
											<span href='' style='color:#8FCAE5;font-size:15px;font-weight:none!important;'>$no_valor_criterio %</span>  <p>&nbsp;</p>

								
											
											<div class='form-group'><br/>
												<label for='comment' style='writing-mode: vertical-lr;transform: rotate(180deg);font-size:16px; margin-top: 75px; font-weight:bold;'>$nb_crit</label>
												
											</div>

										</div>
								</div>
				  
					      
						      </div>
							 
							  ";
		  
		  
		
		$Query="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion".$sufix.",fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion_criterio ";
        $Query.="WHERE 1=1 ";
		if(!empty($fl_leccion_de_instituto)){
		$Query.=" AND fl_instituto=$fl_instituto ";
		}else{
        $Query.=" AND fl_instituto IS NULL ";   
        }
		$Query.=" ORDER BY fl_calificacion_criterio DESC ";
		$rs = EjecutaQuery($Query);
		  for($i=1;$row=RecuperaRegistro($rs);$i++) {
			$fl_calificacion_criterio=$row['fl_calificacion_criterio'];
			$cl_calificacion=$row['cl_calificacion'];
			$ds_calificacion= htmlentities($row['ds_calificacion'.$sufix], ENT_QUOTES, "UTF-8");//$row['ds_calificacion'.$sufix];
			$fg_aprobado=$row['fg_aprobado'];
			$no_equivalencia=$row['no_equivalencia'];
			$no_min= number_format($row['no_min']);
			$no_max=number_format($row['no_max']);
		
		
			
		
			if($contador_img==1)
			$top_img="30px;";
			else
			$top_img="-530px;";
		
		
			if($no_max==0)
			  $ds_equivalencia="No Uploaded";
			else
			  $ds_equivalencia=$no_min."% - ".$no_max."%"." ($cl_calificacion)";
	  
			#Recupermaos la descripcion que tiene actualmente.
			$Query_c="SELECT ds_descripcion".$sufix.", fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
			$row_c=RecuperaValor($Query_c);
			$ds_desc=  str_texto($row_c[0]);//str_texto($row_c[0]);
            $ds_desc=str_replace("'", "&#039;", $ds_desc);
			$fl_criterio_fame=$row_c[1];

            #Sustitimos los saltos de linea.
            $ds_desc=str_replace("&lt;br &#47;&gt;", "\n", $ds_desc);

			#Recuperamos las imagenes por calificacion
			$Query_img = "SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame ";
			$row_img = RecuperaValor($Query_img);
			$nb_archivo_criterio = $row_img[0];
			//$src_img="../../images/rubrics/".$nb_archivo_criterio;
			$src_img="../AD3M2SRC4/images/rubrics/".$nb_archivo_criterio;
			$contador ++;
		  
		  
		  if(!empty($nb_archivo_criterio)){
              $icono = "<a class='zoomimg' href='javascript:void(0);'> 
				<i class='fa fa-file-picture-o' style='color:#333;'></i>
				<span style='left:-300px;'>
				  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-top: $top_img '>
					<div class='modal-content' style='width:500px;height:500px;'>
					  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
						<img class='superbox-current-img' src='$src_img' style='width:494px;height:490px;'>
					  </div>
					</div>
				  </div>
				</span>
			 </a> ";
		  }else{
			   $icono="";
		  
		  }
		  
		   $ds_mensaje1.=" <div class='col-md-2' style='padding-right: 0px;'>
								<div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;  letter-spacing:0px !important;' >
									 $ds_calificacion &nbsp; $icono
								</div>
								
								<br/>
								    <div class='panel panel-default' style='height:370px;'>
										<div class='panel-body text-center'>
											<p href='' style='color:#8FCAE5;font-size:15px;font-weight:normal!important;'>$ds_equivalencia</p>  <p>&nbsp;</p>
											
											 <div class='chart' data-percent='$no_max'  id='final_".$fl_criterio."_".$fl_calificacion_criterio."'>
												<span class='percent'   style='font:18px Arial; font-weight:none !important;' id='span_final$contador'> ".$no_max." </span>
											 </div>


											<hr style=''>
											
											<div class='form-group'>
											<span id='char".$fl_criterio."_".$fl_calificacion_criterio."' class='pull-left text-left hidden' style=''></span>
											
												
												<textarea class='form-control' rows='5'  id='desc".$fl_criterio."_".$fl_calificacion_criterio."' style='color:#999 !important;resize:none !important;font-weight:normal !important;font-style: italic;' maxlength='130' disabled>$ds_desc</textarea>
											
												
											</div>
					
										</div>
									</div>
								
								
								
								
						    </div>";
		  
		  
		  
					    $ds_mensaje1.="
							<script>
								$(document).ready(function () {

								$('#final_".$fl_criterio."_".$fl_calificacion_criterio."').easyPieChart({
										animate: 2000,
										scaleColor: false,
										lineWidth: 8,
										lineCap: 'square',
										value:'10',
										size: 100,
										trackColor: '#EEEEEE',
										barColor: '#92D099'
									});

									$('#final_".$fl_criterio."_".$fl_calificacion_criterio."').css({
										width: 100 + 'px',
										height: 100 + 'px'
									});
									$('#final_".$fl_criterio."_".$fl_calificacion_criterio." .percent').css({
										'line-height': 100 + 'px'
									})

								});
							</script>
						
						
						
						";
		  
		  
		  
		  
		  
		  }
		  
		  $ds_mensaje1.="
							<div class='col-md-1'>
							&nbsp;
							</div>
							";
			
		 
		  
				
						
		 
		 
		 $ds_mensaje1.="</div>";
		 
		 
		}#end for rubric a c		 
	   
       
       
       
       
        //if($tot_reg==0){ cuando no existe la rubric
       
            //$ds_mensaje1="<div class='alert alert-danger' role='alert' style='letter-spacing:0px !important;'>".ObtenEtiqueta(1689)."</div>  ";     

        // }
       
       
       
       
        
        
       

}#end else. 
        
        
        
        
        
        
    





$ds_mensaje=$ds_style.$ds_mensaje.$ds_mensaje1.$ds_mensaje2;
	



$result["message"] .=  $ds_mensaje;
$result["etq_requiered"] .= $ds_mensaje;



echo json_encode((Object) $result);
  
?>