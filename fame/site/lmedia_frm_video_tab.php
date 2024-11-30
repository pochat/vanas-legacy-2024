
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-1 col-md-1"></div>      
        <div class="col-xs-12 col-sm-12 col-lg-10 col-md-10 ">
			<?php
			   # si coparon algun video de otra leccions
			   if(!empty($ds_vl_ruta)){
				   $cla = $ds_vl_ruta."&clave=".$clave;
				   if(!empty($archivo_a) && !empty($ds_vl_ruta_copy)){
					   $cla = $ds_vl_ruta_copy."&clave=".$archivo_a;
				   }
				   echo "<br><strong>".ObtenEtiqueta(216).":</strong><br>";
			?>
				   <input type='hidden' name='ds_vl_ruta0' id='ds_vl_ruta0' value="<?php echo $ds_vl_ruta; ?>">
				   <input type='hidden' name='ds_vl_ruta' id='ds_vl_ruta' value="<?php echo $ds_vl_ruta; ?>">				  
			<?php     
				    //Forma_CampoPreview(ObtenEtiqueta(392), 'ds_vl_ruta', $cla, $ruta, True);
				   echo "
					<script>
					$(document).ready(function(){
					$('#a1_ds_vl_ruta').text('";
										   if(!empty($archivo_a) && !empty($ds_vl_ruta_copy)){
											   echo $ds_vl_ruta_copy;
										   }
										   else{
											   echo $ds_vl_ruta;
										   }
										   echo "');
					});
					</script>";
				 }
			   ?>
        </div>
    </div>
    <br>
    <?php
    if(!empty($ds_vl_ruta)){
    ?>
    <div class='row' id='grl_progress1'>
       <style>           
		[data-progressbar-value]::after{
			content: ''
		}
		[data-progressbar-value]::before{
			content: ''
		}
		</style>
        <div class='col col-sm-12 col-lg-1 col-md-12'>&nbsp;</div>
        <div class='col col-sm-12 col-lg-2 col-md-12'>
			 <a  href="preview_flv.php?archivo=<?php echo $cla; ?>" target="_blank">
				<img src="<?php echo ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$clave."/video_".$clave."_sd/img_1.png"; ?>"   class="superbox-img">
			</a>
			<div class="padding-top-10" style="border-bottom: 3px solid rgba(10,0,10,0.1); font-size:12.5px;">
				<a href="languages.php?fl_leccion=<?php echo $clave; ?>" data-toggle="modal" data-target="#LanguageModal" style="backcolor: #0092cd;color: #0092cd;"><i class="fa fa-cloud-upload"></i> <?php echo ObtenEtiqueta(2012); ?></a>
				<!-- MODAL PARA AGREGAR UN LENGUAGE -->
				<div class="modal fade" id="LanguageModal" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">  
					<div class="modal-dialog">  
						<div class="modal-content">
						</div>  
					</div>  
				</div>  
			</div>             
		</div>
		
		<div class='col col-sm-12 col-lg-6 col-md-12'>
			<div class='padding-10'>
				<p>
					<div>
						<strong>
						<?php echo ObtenEtiqueta(1864); ?>
						</strong>
					</div>                
				</p>
				<div class='progress' data-progressbar-value='0' id='grl_progress'>
					<div class='progress-bar' id='progress_hls'>
					0%
					</div>
				</div>
			</div>

			<div class="smart-form">
				<?php  FAMECheckBox('Re-start video encoding','fg_reset_video',$fg_reset_video,false);  ?>	    
			</div> 
		</div>
		<div class='col col-sm-12 col-lg-3 col-md-12'>&nbsp;<input type="hidden" id="camp_progreso_hls" name="camp_progreso_hls"/></div>
    </div>


	<script>
		// Guarda el contenido para convertilo
		function Languages(leccion, idioma, accion){
			var datos, fl_idioma, fl_leccion, confirmacion;
			// Valores
			if(accion==''){
				fl_idioma = $("#fl_idioma").val();
				fl_leccion = $("#fl_leccion").val();
				ds_language = $("#ds_language").val();
				datos = 'fl_idioma='+fl_idioma+'&fl_leccion='+fl_leccion+'&ds_language='+ds_language;
				confirmacion = true;
			}
			else{
				// Acciones de eliminar o editar
				if(accion=='D' || accion=='AD'){
					datos = 'fl_idioma='+idioma+'&fl_leccion='+leccion+'&accion='+accion;
					fl_idioma = idioma;
					fl_leccion = leccion;
					if(accion=='AD'){
						confirmacion = true;
					}
					else{
						confirmacion = confirm('<?php echo ObtenEtiqueta(2026); ?>');
					}
				}
			}

			if(confirmacion){
				$.ajax({
					type:	'POST',
					url :	'languages_iu.php',
					data:	datos,
				})
				.done(function(result){        
					var resultado = JSON.parse(result);
					var error = resultado.result.error;
					var mensaje = resultado.result.message;
					if(error==false){
						actualiza_lang(fl_leccion, fl_idioma);
						if(accion=='')
							$("#LanguageModal").modal("toggle");
						// Informamos
						$("#code_info").empty().removeClass("hidden").append(mensaje);              
					}      
				});
			}
		}

		function actualiza_lang(fl_leccion, fl_idioma){
			$("#div_languages").empty();
			$.ajax({
				type:	'POST',
				url :	'languages_iu.php',
				data:	'fl_idioma='+fl_idioma+
					'&fl_leccion='+fl_leccion+
					'&fg_idiomas=1'
			})
			.done(function(result){        
				var resultado = JSON.parse(result);
				var contenido = resultado.contenido;
				$("#div_languages").append(contenido);            
			});
		}
	</script>
    <input type='hidden' name='total_convertido' id='total_convertido' value="<?php echo $total_convertido;?>">
                                                          
    <?php     
    }
    ?>
	
    <div class="row">
		<style>      
				#Video_1.dropzone .dz-default.dz-message{
					background-image: url(../../AD3M2SRC4/bootstrap/img/dropzone/spritemap_videos.png);
				}
		</style>
       
	   
		<div class="col-sm-3">
			<code style="white-space: normal;" class="hidden" id="code_info">  </code>
			<div class="padding-top-10" style="border-bottom: 3px solid rgba(10,0,10,0.1); font-size:12.5px;">
				<span class="no-margin cursor-pointer" onclick="div_lan()">
					<i class="fa fa-language">
					</i>
				<?php echo ObtenEtiqueta(2013); ?> 
				</span>
			</div>            
			<div class="chat-body no-padding profile-message" id="div_languages">
				<!-- Obtenemos los lenguages --->
				<ul style="padding-left: 15px; padding-top:10px; color: darkgrey;">
					<?php              
					$rs = EjecutaQuery("SELECT a.fl_idioma, nb_idioma, a.fg_activo FROM k_idioma_video a, c_idioma b WHERE  a.fl_idioma = b.fl_idioma AND  fl_leccion_sp=".$clave);
					$tot_idiomas = CuentaRegistros($rs);
					for($i=1;$rowl=RecuperaRegistro($rs);$i++){
						$fl_idioma_bd = $rowl[0];
						$ds_language = str_texto($rowl[1]);
						$fg_activo = $rowl[2];
						$eye = "fa-eye";
						$lbl_eye = ObtenEtiqueta(2021);
						if(empty($fg_activo)){
							$eye = "fa-eye-slash";
							$lbl_eye = ObtenEtiqueta(2019);
						}
						$pla1 = "right";
						$pla2 = "bottom";
						$pla3 = "left";
						if($i==$tot_idiomas){                  
							$pla2 = "top";
						}
						echo "
						<li class='message no-margin' style='padding-top:2px;'>
						<i class='fa ".$eye." cursor-pointer' rel='tooltip' data-placement='".$pla1."' data-original-title='".$lbl_eye."' onclick='Languages(".$clave.", ".$fl_idioma_bd.", \"AD\");'></i> 
						<a href='languages.php?fl_leccion=".$clave."&fg_editar=1&fl_idioma=".$fl_idioma_bd."' data-toggle='modal' data-target='#LanguageModal' rel='tooltip' data-placement='".$pla2."' data-original-title='".ObtenEtiqueta(2022)."'>".$ds_language."</a> 
						<i class='fa fa-times pull-right cursor-pointer' rel='tooltip' data-placement='".$pla3."' data-original-title='".ObtenEtiqueta(2023)."' onclick='Languages(".$clave.", ".$fl_idioma_bd.", \"D\");'></i>
						</li>";
					}
					?>
				</ul>
			</div>
			<script>
				var clic = 1;
				function div_lan(){ 
					var ele = $("#div_languages");
					if(clic==1){
						// document.getElementById("example_vtt").style.height = "100px";
						ele.addClass("hidden");
						clic = clic + 1;
					} else{
						// document.getElementById("example_vtt").style.height = "0px";      
						ele.removeClass("hidden");
						clic = 1;
					}   
				}            
			</script>
		</div>

		<div class="col-xs-12 col-sm-6">
			<input type='hidden' name='nb_video' id='nb_video' value="<?php echo $ds_vl_ruta; ?>">
			<?php
			$p_id = 'Video_1';
			echo "<div class='row'>";
			echo "<label class='col col-sm-12 control-label text-align-left'>";
			echo "<strong>{$p_titulo}</strong>";
			echo "</label>";
			echo "<div class='col col-sm-12'>";
			echo "<div data-widget-editbutton='false'><!-- class='jarviswidget jarviswidget-color-blueLight' -->";
			echo "<div>";
			echo "<div class='widget-body'>";
			echo "<div class='dropzone' id='{$p_id}' style='min-height: 120px; padding:10px 0px 0px 20px'></div>";
			echo "</div>";
			echo "</div>";
			echo "</div>";
			echo "</div>";
			echo "</div>";
		   
			?>
		</div> 
    </div>     

	<br>
    <div class="row">
        <div class="col-xs-12 col-sm-3">
        </div>
        <div class="col-xs-12 col-sm-6">
			<div class="smart-form ">
				<?php
				$Query  = "SELECT CASE fl_leccion_sp WHEN ".$clave." THEN CONCAT(ds_vl_ruta,' ','".ObtenEtiqueta(1861)."') ELSE ds_vl_ruta END ds_vl_ruta, fl_leccion_sp ";
				$Query .= "FROM c_leccion_sp WHERE ds_vl_ruta <> ''  AND fl_instituto=".$fl_instituto." ORDER BY ds_vl_ruta"; 
				FAMECampoSelectBD(ObtenEtiqueta(389),'fl_programa', $Query, $fl_programa, 'select2', True, "", $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       
		
				?>
			</div>
        </div>
    </div>
	<br>
    <div class="row">
        <div class="col-xs-12 col-sm-3">
        </div>
        <div class="col-xs-6 col-sm-6">
			<div class="smart-form">
			   <?php
			   FAMEInputText(ObtenEtiqueta(396),'ds_vl_duracion',$ds_vl_duracion,true); 
			   ?>
			</div>
			<br /><br />
            <?php if(!empty($fe_vl_alta)){?>
			<span><?php echo ObtenEtiqueta(1759)." ".$fe_vl_alta;?></span>
            <?php } ?>
			<input type='hidden' name='fe_vl_alta' id='fe_vl_alta' value="<?php echo $fe_vl_alta; ?>">
			
        </div>
        <div class="col-xs-6 col-sm-6">
		    	
        </div>          
    </div>
	
	<div class="row">
		<div class="col-xs-12 col-sm-6">
        </div>
		<div class="col-xs-12 col-sm-6">
			<div class="smart-form">														
			<br><br>															
					<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 60px;padding-left: 295px;">
						
						<li>
							<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
						</li>
						
						<li>
							<a href="javascript:void(0);" onclick="GuardarTabVideo();"  class="btn btn-primary btn-circle btn-lg"><i class="fa-fa-check"></i></a>
						</li>
					</ul>														   
			</div> 
		</div>
	</div>
	
   
