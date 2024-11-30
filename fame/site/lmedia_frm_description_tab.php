<div class="tab-pane fade in active" id="description">
  <!-- UMP  UMP START WIDGET BODY -->
  <div class="widget-body">
    <ul id="myTabDescription" class="nav nav-tabs bordered">
      <li class="active">
        <a id="mytabDesc1" href="#description_eng" data-toggle="tab">
          English
        </a>
      </li>
      <li class="">
        <a id="mytabDesc2" href="#description_esp" data-toggle="tab">
          Spanish
        </a>
      </li>
      <li class="">
        <a id="mytabDesc3" href="#description_fra" data-toggle="tab">
          French
        </a>
      </li>
    </ul>
    <div id="myTabDescCont" class="tab-content padding-10 no-border">
      <!--  UMP START English Content-->
      <div class="tab-pane fade in active" id="description_eng">
	  
		<div class="row">
			<div class="col-md-4">  
	  
				<div class="row">
					  <div class="col-sm-1"></div>
					  <div class="col-xs-10 col-sm-10">
						<br>
						<label class="control-label" style="margin: 7px;"><b>* <?php echo ObtenEtiqueta(380);?></b></label>
						<?php			
							if (empty($clave))
							  $script_2 = "onchange='val_tot_quiz(); val_tot_rubric(); HabilitaCampos();'";
							else
							  $script_2 = "";
				
							$Query  = "SELECT CONCAT(nb_programa,' (',contador,' ".ObtenEtiqueta(1242).")'), fl_programa_sp
									FROM (SELECT c.fl_programa_sp, c.nb_programa AS nb_programa, k.no_semanas AS no_semanas, (SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = c.fl_programa_sp AND fl_instituto=$fl_instituto ) contador 
										FROM k_programa_detalle_sp k, c_programa_sp c WHERE k.fl_programa_sp = c.fl_programa_sp 
										AND c.fl_instituto=$fl_instituto 
										ORDER BY c.no_orden 
										)AS principal 
									WHERE 1=1 ORDER BY nb_programa ASC ";
							FAMECampoSelectBD(ObtenEtiqueta(380),'fl_programa', $Query, $fl_programa, 'select2', True, $script_2, $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       
																	
						?>
						<div class="note hidden" id="fl_programa_texto_error" style="color:#A90329;">This is a required field.</div>
					  </div>
				</div>
				<br>
				<div class="row">
				  <div class="col-sm-1"></div>
				  <div class="col-xs-5 col-sm-5">
					<div class="smart-form">
					<?php FAMEInputText(ObtenEtiqueta(375),'no_grado',$no_grado,true,"onkeypress='return validaNumericos(event)'"); ?>
					</div>
				  </div>
				  <div class="col-xs-6 col-sm-6">
					<div id="div_no_semana" class="row form-group">
					  
					  <div class="col-sm-4">
						<div class="smart-form">
						  <?php FAMEInputText(ObtenEtiqueta(1250),'no_semana',$no_semana,true,"onkeypress='return validaNumericos(event)'"); ?>
						</div>
						<div class="note hidden" id="err_sesion" style="color:#A90329;"><?php echo ObtenEtiqueta(1288);?></div>
					  </div>
					  <div class='row' id="muestra_msj" style='display: none;'>
						<div class='col-sm-12 col-md-12'>
						  <div style='color: #A90329; font-zise:11px;'>
							<b> <?php echo ObtenEtiqueta(1288); ?> </b>
						  </div>
						</div>
					  </div>
					</div>
				  </div>
				  
				</div>
				<div class="row">
				  <div class="col-sm-1">
				  </div>
				  <div class="col-xs-10 col-sm-10">
						<div class="smart-form">
							<?php FAMEInputText(ObtenEtiqueta(385),'ds_titulo',$ds_titulo,true); ?>
						</div>
				  </div>
				</div>
				<div class="row">
				  <div class="col-sm-1">
				  </div>
				  <div class="col-xs-10 col-sm-10">
					<div class="smart-form">
						<?php FAMEInputText(ObtenEtiqueta(1297),'ds_learning',$ds_learning,false); ?>
					</div>
				  </div>
				</div>
			</div>
			<div class="col-md-8">
				<div class="row">
				  <div class="col-xs-12 col-sm-12">
					<br>
					<label><b><?php echo ObtenEtiqueta(391);?></b></label>
					<?php  
					FAMETinyMCE('ds_leccion',$ds_leccion);
					?>
					<br>
					<div id="err_ds_leccion"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>

				  </div>
				</div>
			</div>
		</div>
	  </div>
      <!-- UMP END English Content-->
      <!--  UMP START Spanish Content-->
      <div class="tab-pane fade in " id="description_esp">
	  
		<div class="row">
			<div class="col-md-4">		  
				<div class="row">
				  <div class="col-sm-1"></div>
				  <div class="col-xs-10 col-sm-10">	  
					<br>
					<div class="smart-form">
					<?php
					
					if (empty($clave))
					  $script_2 = "onchange='val_tot_quiz(); val_tot_rubric(); HabilitaCampos();'";
					else
					  $script_2 = "";

					$Query  = "SELECT CONCAT(nb_programa,' (',contador,' ".ObtenEtiqueta(1242).")'), fl_programa_sp
								FROM (SELECT c.fl_programa_sp, c.nb_programa_esp AS nb_programa, k.no_semanas AS no_semanas, (SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = c.fl_programa_sp AND fl_instituto=$fl_instituto ) contador 
									FROM k_programa_detalle_sp k, c_programa_sp c WHERE k.fl_programa_sp = c.fl_programa_sp 
									AND c.fl_instituto=$fl_instituto 
									ORDER BY c.no_orden 
									)AS principal 
								WHERE 1=1 ORDER BY nb_programa ASC ";
					FAMECampoSelectBD(ObtenEtiqueta(380),'fl_programa_esp', $Query, $fl_programa, 'select2', True, $p_script, $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       
					?>
					</div>
					<br>
				  </div>
				</div>
				<br>
				<div class="row">
				  <div class="col-sm-1"></div>
				  <div class="col-xs-5 col-sm-5">
					<div class="smart-form">
					<?php FAMEInputText(ObtenEtiqueta(375),'no_grado_esp',$no_grado,true); ?>
					</div>
				  </div>
				  <div class="col-xs-6 col-sm-6">
					<div id="div_no_semana_esp" class="row form-group">
					  
					  <div class="col-sm-4">
						<div class="smart-form">
						   <?php FAMEInputText(ObtenEtiqueta(1250),'no_semana_esp',$no_semana,true); ?>
						</div>
					  </div>
					  <div class='row' id="muestra_msj_esp" style='display: none;'>
						<div class='col-sm-12 col-md-12'>
						  <div style='color: #A90329; font-zise:11px;'>
							<b> <?php echo ObtenEtiqueta(1288); ?> </b>
						  </div>
						</div>
					  </div>
					</div>
				  </div>
				  
				</div>
				<div class="row">
				  <div class="col-sm-1">
				  </div>
				  <div class="col-xs-10 col-sm-10">
					<div class="smart-form">
					   <?php FAMEInputText(ObtenEtiqueta(385),'ds_titulo_esp',$ds_titulo_esp,true); ?>
					</div>  
					
				  </div>
				</div>
				<div class="row">
				  <div class="col-sm-1">
				  </div>
				  <div class="col-xs-10 col-sm-10">
					<div class="smart-form">
					   <?php FAMEInputText(ObtenEtiqueta(1297),'ds_learning_esp',$ds_learning_esp,true); ?>
					</div> 
				  </div>
				</div>
			</div>
			<div class="col-md-8">
				<div class="row">
				  <div class="col-xs-12 col-sm-12">
					<br>
					<label><b><?php echo ObtenEtiqueta(391);?></b></label>
					 <?php  
					FAMETinyMCE('ds_leccion_esp',$ds_leccion_esp);
					?>
					
				  </div>
				</div>
			</div>
		</div>
      </div>
      <!-- UMP END Spanish Content-->
      <!--  UMP START French Content-->
      <div class="tab-pane fade in " id="description_fra">
		
		<div class="row">
			<div class="col-md-4">
			
				<div class="row">
				  <div class="col-sm-1"></div>
				  <div class="col-xs-10 col-sm-10">
					<br>
					<?php
				   
					if (empty($clave))
					  $script_2 = "onchange='val_tot_quiz(); val_tot_rubric(); HabilitaCampos();'";
					else
					  $script_2 = "";

					$Query  = "SELECT CONCAT(nb_programa,' (',contador,' ".ObtenEtiqueta(1242).")'), fl_programa_sp
								FROM (SELECT c.fl_programa_sp, c.nb_programa_fra AS nb_programa, k.no_semanas AS no_semanas, (SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = c.fl_programa_sp AND fl_instituto=$fl_instituto ) contador 
									FROM k_programa_detalle_sp k, c_programa_sp c WHERE k.fl_programa_sp = c.fl_programa_sp 
									AND c.fl_instituto=$fl_instituto 
									ORDER BY c.no_orden 
									)AS principal 
								WHERE 1=1 ORDER BY nb_programa ASC ";
					//Forma_CampoSelectBD(ObtenEtiqueta(380), True, 'fl_programa_fra', $Query, $fl_programa, $fl_programa_err, True, "onclick='val_lesson();' {$script_2} ", 'right', 'col col-md-4', 'col col-md-8', 'unico');
					FAMECampoSelectBD(ObtenEtiqueta(380),'fl_programa_fra', $Query, $fl_programa, 'select2', True, $p_script, $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       
					
					?>
				  </div>
				</div>
				<br>
				<div class="row">
				  <div class="col-sm-1"></div>
				  <div class="col-xs-5 col-sm-5">
					<div class="smart-form">
					<?php FAMEInputText(ObtenEtiqueta(375),'no_grado_fra',$no_grado,true); ?>
					</div>
				  </div>
				  <div class="col-xs-6 col-sm-6">
					<div id="div_no_semana_fra" class="row form-group">
					  
					  <div class="col-sm-4">
						<div class="smart-form">
							<?php FAMEInputText(ObtenEtiqueta(1250),'no_semana_fra',$no_semana,true); ?>
						</div>
					  </div>
					  <div class='row' id="muestra_msj_fra" style='display: none;'>
						<div class='col-sm-12 col-md-12'>
						  <div style='color: #A90329; font-zise:11px;'>
							<b> <?php echo ObtenEtiqueta(1288); ?> </b>
						  </div>
						</div>
					  </div>
					</div>
				  </div>
				  
				</div>
				<div class="row">
				  <div class="col-sm-1">
				  </div>
				  <div class="col-xs-10 col-sm-10">
					<div class="smart-form">
						<?php FAMEInputText(ObtenEtiqueta(385),'ds_titulo_fra',$ds_titulo_fra,true); ?>
					</div>
				  </div>
				</div>
				<div class="row">
				  <div class="col-sm-1">
				  </div>
				  <div class="col-xs-10 col-sm-10">
					<div class="smart-form">
					   <?php FAMEInputText(ObtenEtiqueta(1297),'ds_learning_fra',$ds_learning_fra,true); ?>
					</div> 
				  </div>
				</div>
			</div>
			<div class="col-md-8">
			
				<div class="row">
				  <div class="col-xs-12 col-sm-12">
					<br>
					<label><b><?php echo ObtenEtiqueta(391);?></b></label>
					<?php  
					FAMETinyMCE('ds_leccion_fra',$ds_leccion_fra);
					?>
				  </div>
				</div>
			</div>
		</div>
      </div>
      <!-- UMP END French Content-->
      <!-- UMP END WIDGET BODY -->
    </div>
  </div>
</div>
<!-- UMP: Funcion que solo acepta numeros, se utiliza para que el campo de texto solo acepte numeros -->
<script>
  function SoloNumeros(evt) {
    if (window.event) { //asignamos el valor de la tecla a keynum
      keynum = evt.keyCode; //IE
    } else {
      keynum = evt.whUMP; //FF
    }
    //comprobamos si se encuentra en el rango numerico y que teclas no recibira.
    if ((keynum > 47 && keynum < 58) || keynum == 8 || keynum == 13 || keynum == 6) {
      return true;
    } else {
      return false;
    }
  }


 
</script>