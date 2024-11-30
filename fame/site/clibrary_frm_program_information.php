
  <div class="row">
    <ul id="myTabpi" class="nav nav-tabs bordered">
        <li class="active">
          <a href="#pi_eng" data-toggle="tab">English</a>
        </li>
        <li class="">
          <a href="#pi_esp" data-toggle="tab">Spanish</a>
        </li>
        <li class="">
          <a href="#pi_fra" data-toggle="tab">French</a>
        </li>
    </ul>
  </div>
  <div id="myTabContentpi" class="tab-content padding-10 no-border">
    <!-- ENGLISH START -->
    <div class="tab-pane fade in active" id="pi_eng">
      <div class="row">
		   <div class="col-md-1"></div>
		   <div class="col-md-10">
       
				<div class="row">
				  
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
						<div class="smart-form">
						<?php FAMEInputText(ObtenEtiqueta(360),'nb_programa',$nb_programa,True); ?>
						</div>
				  </div>
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					   <div class="smart-form">
						<?php FAMEInputText(ObtenEtiqueta(1223),'ds_credential',$ds_credential,true); ?>
					   </div>
				  </div>
				</div>
				<div class="row">
				  
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
				   <label class="control-label" style="margin: 7px;"><b>* <?php echo ObtenEtiqueta(1224);?></b></label>
					<?php
					$p_opc = array('Online', 'On-Site', 'Combined', 'Online / Blended');
					$p_val = array('O', 'S', 'C', 'OB');
					FAMESelectSimple(ObtenEtiqueta(1224),'cl_delivery',$p_opc, $p_val,$cl_delivery,true);
					?>
				  </div>
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
						<div class="smart-form">
							<?php FAMEInputText(ObtenEtiqueta(1225),'ds_language',$ds_language,false); ?>
						</div>
				  </div>
				</div>
				<div class="row">
          
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					<label class="control-label" style="margin: 7px;"><b>* <?php echo ObtenEtiqueta(1226);?></b></label>
					<?php 
					 $p_opc1 = array('Long Term Duration', 'Short Term Duration', 'Corporate');
					 $p_val1 = array(1, 2, 3);
					 FAMESelectSimple(ObtenEtiqueta(1226),'cl_type',$p_opc1, $p_val1,$cl_type,true);
					  ?>
				  </div>
				</div>        
			</div>
		<div class="col-md-1"></div>
	  </div>
    </div>
    <!-- ENGLISH FINISH -->
    <!-- SPANISH START -->
    <div class="tab-pane fade in " id="pi_esp">
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<div class="row">        
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					<div class="smart-form">
					<?php FAMEInputText(ObtenEtiqueta(360),'nb_programa_esp',$nb_programa_esp,True); ?>
					</div>
				  </div>
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					<div class="smart-form">
					<?php #FAMEInputText(ObtenEtiqueta(1223), True, 'ds_credential_esp', replaceLangWords($ds_credential, 'esp'), 50, 30, $ds_credential_err, '', '', True, "$val_camp_obl_6"); ?>
					<?php FAMEInputText(ObtenEtiqueta(1223),'ds_credential_esp',$ds_credential_esp,true); ?>
					</div>
				  </div>
				</div>
				<div class="row">         
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					<label class="control-label" style="margin: 7px;"><b>* <?php echo ObtenEtiqueta(1224);?></b></label>
					<?php
					$p_opc = array('En-línea', 'En-Sitio', 'Combinado', 'En-línea / Mezclado');
					$p_val = array('O', 'S', 'C', 'OB');
					#Forma_CampoSelect(ObtenEtiqueta(1224), True, 'cl_delivery_esp', $p_opc, $p_val, $cl_delivery, $cl_delivery_err, False, "$val_camp_obl_7", 'right', 'col col-sm-4', 'col col-sm-7'); 
					#$p_opc = array('Online', 'On-Site', 'Combined', 'Online / Blended');
					$p_val = array('O', 'S', 'C', 'OB');
					FAMESelectSimple(ObtenEtiqueta(1224),'cl_delivery_esp',$p_opc, $p_val,$cl_delivery,true);
					 ?>
				  </div>
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
						<div class="smart-form">
						<?php
						 #Forma_CampoTexto(ObtenEtiqueta(1225), False, 'ds_language_esp', replaceLangWords($ds_language, 'esp'), 50, 30);
						FAMEInputText(ObtenEtiqueta(1225),'ds_language_esp',$ds_language_esp,false); 
						 ?>
						</div>
				  </div>
				</div>
				<div class="row">        
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					<label class="control-label" style="margin: 7px;"><b>*<?php echo ObtenEtiqueta(1226);?></b></label>
					<?php
					  $p_opc1 = array('Duración a largo plazo','Duración a corto plazo','Corporativo','Duración a largo plazo (3 contratos, 1 por año)');
					  $p_val1 = array(1, 2, 3, 4);
					  #Forma_CampoSelect(ObtenEtiqueta(1226), True, 'cl_type', $p_opc1, $p_val1, $cl_type, $cl_type_err, False, "$val_camp_obl_8", 'right', 'col col-sm-4', 'col col-sm-7'); 
					  FAMESelectSimple(ObtenEtiqueta(1226),'cl_type',$p_opc1, $p_val1,$cl_type,true);
					  ?>
				  </div>
				</div> 
			</div>
			<div class="col-md-1"></div>
        </div>
    </div>
    <!-- SPANISH FINISH -->
    <!-- FRENCH START -->
    <div class="tab-pane fade in " id="pi_fra">
      <div class="row">
			<div class="col-md-1"></div>
            <div class="col-md-10">
				<div class="row">
				  
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					<div class="smart-form">
					<?php FAMEInputText(ObtenEtiqueta(360),'nb_programa_fra',$nb_programa_fra,True);?>
					</div>
				  </div>
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					<div class="smart-form">
					<?php #Forma_CampoTexto(ObtenEtiqueta(1223), True, 'ds_credential_fra', replaceLangWords($ds_credential, 'fra'), 50, 30, $ds_credential_err, '', '', True, "$val_camp_obl_6"); 
						  FAMEInputText(ObtenEtiqueta(1223),'ds_credential_fra',$ds_credential_fra,True);
					?>
					</div>
				  </div>
				</div>
				<div class="row">
				  
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					<label class="control-label" style="margin: 7px;"><b>* <?php echo ObtenEtiqueta(1224);?></b></label>
					<?php
					$p_opc = array('En-ligne', 'Sur-Site', 'Combiné', 'En-ligne / Mixte');
					$p_val = array('O', 'S', 'C', 'OB');
					#Forma_CampoSelect(ObtenEtiqueta(1224), True, 'cl_delivery_fra', $p_opc, $p_val, $cl_delivery, $cl_delivery_err, False, "$val_camp_obl_7", 'right', 'col col-sm-4', 'col col-sm-7');
					FAMESelectSimple(ObtenEtiqueta(1224),'cl_delivery_fra',$p_opc, $p_val,$cl_delivery,true);
					?>
				  </div>
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					<div class="smart-form">
					<?php
					//Forma_CampoTexto(ObtenEtiqueta(1225), False, 'ds_language_fra', replaceLangWords($ds_language, 'fra'), 50, 30);
					 FAMEInputText(ObtenEtiqueta(1225),'ds_language_fra',$ds_language_fra,false);
					?>
					</div>
				  </div>
				</div>
				
				<div class="row">
				  
				  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
					<label class="control-label" style="margin: 7px;"><b>*<?php echo ObtenEtiqueta(1226);?></b></label>
					<?php
					  $p_opc1 = array('Durée à long terme','Durée à court terme','Corporate','Durée à long terme (3 contrats, 1 par an)');
					  $p_val1 = array(1, 2, 3, 4);
					  //Forma_CampoSelect(ObtenEtiqueta(1226), True, 'cl_type', $p_opc1, $p_val1, $cl_type, $cl_type_err, False, "$val_camp_obl_8", 'right', 'col col-sm-4', 'col col-sm-7');
					  FAMESelectSimple(ObtenEtiqueta(1226),'cl_type',$p_opc1, $p_val1,$cl_type,true);
					  ?>
				  </div>
				</div>
			</div>
			<div class="col-md-1"></div>
      </div>
    </div>
  </div>
  <!-- FRENCH FINISH -->
<!-- END OF LANGUAGE MENUS START OF GENERAL CONTENT -->

<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-10">

		<div class="row">
		  
		  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
				<div class="smart-form">
					<?php
					FAMEInputText(ObtenEtiqueta(1249),'workload',$workload,false); ?>
				</div>
		  </div>
		  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
				<div class="smart-form">
					<?php
					   FAMEInputText(ObtenEtiqueta(1216),'no_creditos',$no_creditos,true,"onkeypress='return NumDecimal(event, this)'");
					?>
				</div>
		  </div>
		</div>
		<div class="row">
		  
		  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
			<div class="smart-form">
			<?php
			  // Forma_CampoTexto(ObtenEtiqueta(1219), True, 'ds_duracion', $ds_duracion, 50, 30, $ds_duracion_err);
			 // Forma_CampoTexto(ObtenEtiqueta(1220), True, 'no_horas', $no_horas, 50, 30, $no_horas_err, '', '', True, "$val_camp_obl_4"); 
			 FAMEInputText(ObtenEtiqueta(1220),'no_horas',$no_horas,true,"onkeypress='return validaNumericos(event)'"); 
			  ?>
			</div>
		  </div>
		  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
			<div class="smart-form">
			<?php
			  // Forma_CampoTexto(ObtenEtiqueta(1221), True, 'no_horas_week', $no_horas_week, 50, 30, $no_horas_week_err);
			  //Forma_CampoTexto(ObtenEtiqueta(1222), True, 'no_semanas', $no_semanas, 50, 30, $no_semanas_err, '', '', True, "$val_camp_obl_5"); 
			  FAMEInputText(ObtenEtiqueta(1222),'no_semanas',$no_semanas,true,"onkeypress='return validaNumericos(event)'");
			  ?>
			</div>
		  </div>
		</div>
		<div class="row hidden">
		  
		  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6"> 
			 <div class="smart-form">  
			  <?php
			  //Forma_CampoTexto(ObtenEtiqueta(1218), True, 'no_orden', $no_orden, 3, 30, $no_orden_err, '', '', True, "$val_camp_obl_3"); 
			  FAMEInputText(ObtenEtiqueta(1218),'no_orden',$no_orden,true);
			  ?>
			  </div>
		  </div>
		  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
		    <div class="hidden">
			<label class="control-label" style="margin: 7px;"><b>*<?php echo ObtenEtiqueta(1227);?></b></label>
			<?php
				
			  $Query  = "SELECT DISTINCT CONCAT(nb_programa,' (', ds_duracion,')'), fl_programa_sp FROM c_programa_sp ";
			  $Query .= "WHERE fl_programa_sp <> $clave ";
			  $Query .= "ORDER BY no_orden";
			  $row = RecuperaValor("SELECT COUNT(*) FROM c_leccion_sp WHERE fl_programa_sp = $clave");
			  if($row[0] > 0)
				  $p_script = "disabled='disabled'";
			  else
				  $p_script = '';

			  FAMECampoSelectBD(ObtenEtiqueta(1227),'fl_programa', $Query, (isset($fl_programa)?$fl_programa:NULL), 'select2', True, $p_script, $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);
												
											
			?> 
			</div>
		  </div>
		</div>
		

		<div class="row">
		  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">
			<?php
			   $ds_rut_img=SP_HOME."/AD3M2SRC4/modules/fame/uploads/".$nb_thumb;										
			   FAMEFile(ObtenEtiqueta(1241),'thumb',$nb_thumb,$ds_rut_img);
			
			?>
		  </div>
		  
		</div>
	</div>
	<div class="col-md-1"></div>
</div>