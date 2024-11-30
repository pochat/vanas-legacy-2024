 
<div class="row">
	<div class="col-xs-12 col-sm-12 col-lg-4 col-md-4">
 
 
		 <div class="row">
				<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1">&nbsp;</div>
				<div class="col-xs-12 col-sm-12 col-lg-10 col-md-10">			
								<div class="smart-form">
									<?php FAMEInputText(ObtenEtiqueta(1252),'ds_tiempo_tarea',$ds_tiempo_tarea,false);  ?>
								</div>			
				</div>		
				<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1">&nbsp;</div>
		 </div>
			<?php 						 
			if($no_sketch>=1){
				$style_n=" style='display:block;' ";
				$descripcion3=1;		
			}else{
				$no_sketch=0;
				$style_n=" style='display:none;' ";
				$descripcion3=0;		
			}
			?>						 
		<div class="row">
				<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1">&nbsp;</div>
				<div class="col-xs-12 col-sm-12 col-lg-10 col-md-10">			
								<div class="smart-form">
								   <?php FAMEInputText(ObtenEtiqueta(394),'no_sketch',$no_sketch,false,"onkeypress='return validaNumericos(event);'",'','','MuestraDescSketchNum('.$descripcion3.')');  ?>	    
								</div> 				   				
				</div>
				<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1">&nbsp;</div>
		</div>

		<div class="row">
				<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1">&nbsp;</div>
				<div class="col-xs-12 col-sm-12 col-lg-10 col-md-10">				
								<div class="smart-form">
								   <?php  FAMECheckBox(ObtenEtiqueta(393),'fg_animacion',$fg_animacion,false,ObtenEtiqueta(2381));  ?>	    
								</div> 					   				
				</div>
				<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1">&nbsp;</div>
		 </div>
		<?php 						 
			if($fg_animacion==1){
				$style_div_fg_animacion=" style='display:block;' ";
				$descripcion1 = 1;		
			}else{
				$style_div_fg_animacion=" style='display:none;' ";	
				$descripcion1 = 0;
			}

			if(!empty($fg_ref_animacion)){
				$style_div_fg_ref_animacion=" style='display:block;' ";	
				$descripcion2=1;
			}else{
				$style_div_fg_ref_animacion=" style='display:none;' ";	
				$descripcion2=0;
			}
		?>
		  
		<div class="row">
				<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1">&nbsp;</div>
				<div class="col-xs-12 col-sm-12 col-lg-10 col-md-10">			
								<div class="smart-form">
								   <?php  FAMECheckBox(ObtenEtiqueta(398),'fg_ref_animacion',$fg_ref_animacion,false,ObtenEtiqueta(2382));  ?>	    
								</div> 	   
				</div>
				<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1">&nbsp;</div>
		</div>


														  
		<div class="row">
				<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1">&nbsp;</div>
				<div class="col-xs-12 col-sm-12 col-lg-10 col-md-10">				
								<div class="smart-form">
								   <?php  FAMECheckBox(ObtenEtiqueta(399),'fg_ref_sketch',$fg_ref_sketch,false,ObtenEtiqueta(2383));  ?>	    
								</div> 				   				
				</div>
				<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1">&nbsp;</div>
		</div>
    
	
	</div>
	
	<div class="col-xs-12 col-sm-12 col-lg-8 col-md-8">

		<?php 
		if(!empty($fg_ref_sketch)){
			$style_div_fg_ref_sketch=" style='display:block;' ";	
		}else{
			$style_div_fg_ref_sketch=" style='display:none;' ";	
		}
		
		#Para esconder las tabs de idiomas
		if(($no_sketch==0)&&($fg_animacion==0)&&($fg_ref_animacion==0)&&($fg_ref_sketch==0)){	
			$style_tabs_idiomas="style='display:none;'";
		}else{
			$style_tabs_idiomas="";
		}
		
		
		
		?>					 
		<div class="widget-body" id="tabs_idiomas" style="<?php echo $style_tabs_idiomas;?>" >
			<ul id="myTabAssignment" class="nav nav-tabs bordered" >
				<li class="active">
				  <a id="mytabAssign1" href="#assignment_eng" data-toggle="tab">
					English
				  </a>
				</li>
				<li class="">
				  <a id="mytabAssign2" href="#assignment_esp" data-toggle="tab">
					Spanish
				  </a>
				</li>
				<li class="">
				  <a id="mytabAssign3" href="#assignment_fra" data-toggle="tab">
					French
				  </a>
				</li>
			</ul>
		    <div id="myTabAssignCont" class="tab-content padding-10 no-border">
		  
			    <!-- START English Content-->
			    <div class="tab-pane fade in active" id="assignment_eng">
			   
				   <div id="content_p" <?php echo $style_div_fg_animacion; ?> >
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
							<label><b><?php echo ObtenEtiqueta(1289);?></b></label>						
							<br />
						  <?php
							//Forma_CampoTinyMCE(ObtenEtiqueta(393), False, 'ds_animacion', $ds_animacion, 50, 20, $ds_animacion_err);
							FAMETinyMCE('ds_animacion',$ds_animacion);
						  ?>
						  <br>
						  <div id="err_ds_animacion"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>									
						</div>
					  </div>
					</div>
	   
					<div id='content_2' <?php echo $style_div_fg_ref_animacion;?>  >
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
							<label><b><?php echo ObtenEtiqueta(1290);?></b></label>
							<br />
						  <?php
						   // Forma_CampoTinyMCE(ObtenEtiqueta(1290), False, 'ds_ref_animacion', $ds_ref_animacion, 50, 20, $ds_ref_animacion_err);
							 FAMETinyMCE('ds_ref_animacion',$ds_ref_animacion);
						   ?>
						   <br>
						   <div id="err_ds_ref_animacion"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
						</div>
					  </div>
					</div>
					<div id='content_3' <?php echo $style_div_fg_ref_sketch; ?> >
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
							<label><b><?php echo ObtenEtiqueta(1292);?></b></label>
							<br />
						  <?php
							//  Forma_CampoTinyMCE(ObtenEtiqueta(1292), False, 'ds_ref_sketch', $ds_ref_sketch, 50, 20, $ds_ref_sketch_err);
							FAMETinyMCE('ds_ref_sketch',$ds_ref_sketch);			  ?>
							<br>
							<div id="err_ds_ref_sketch"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
												
						</div>
					  </div>
					</div>
					<div id='content_4' <?php echo $style_n; ?> >
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
							<label><b><?php echo ObtenEtiqueta(394);?></b></label>
							<br />
						  <?php
							//Forma_CampoTinyMCE(ObtenEtiqueta(1291), False, 'ds_no_sketch', $ds_no_sketch, 50, 20, $ds_no_sketch_err);
							FAMETinyMCE('ds_no_sketch',$ds_no_sketch);
							?>
							<br>
							<div id="err_ds_no_sketch"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
											
						</div>
					  </div>
					</div>
		  
				</div>
	   
				<!-- START Spanish Content-->
				<div class="tab-pane fade in " id="assignment_esp">
		  		  
					<div <?php echo $style_div_fg_animacion;?> id='content_p_esp'  >
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
							<label><b><?php echo ObtenEtiqueta(1289);?></b></label>
							<br />
						  <?php
						   // Forma_CampoTinyMCE(ObtenEtiqueta(393), False, 'ds_animacion_esp', $ds_animacion_esp, 50, 20, $ds_animacion_err);
						   FAMETinyMCE('ds_animacion_esp',$ds_animacion_esp);
						  ?>
						  <br>
						  <div id="err_ds_animacion_esp"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>									
						
						  
						</div>
					  </div>
					 
					</div>
					<div <?php echo $style_div_fg_ref_animacion; ?> id='content_2_esp' >
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
							<label><b><?php echo ObtenEtiqueta(1290);?></b></label>
							<br />
						  <?php
						   // Forma_CampoTinyMCE(ObtenEtiqueta(1290), False, 'ds_ref_animacion_esp', $ds_ref_animacion_esp, 50, 20, $ds_ref_animacion_err); 
						   FAMETinyMCE('ds_ref_animacion_esp',$ds_ref_animacion_esp);
						   ?>
						  <br>
						  <div id="err_ds_ref_animacion_esp"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>									
						
						</div>
					  </div>
					</div>
					<div id='content_3_esp' <?php echo $style_div_fg_ref_sketch; ?>  >
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
							<label><b><?php echo ObtenEtiqueta(1292);?></b></label>
							<br />
						  <?php
						   // Forma_CampoTinyMCE(ObtenEtiqueta(1292), False, 'ds_ref_sketch_esp', $ds_ref_sketch_esp, 50, 20, $ds_ref_sketch_err); 
							FAMETinyMCE('ds_ref_sketch_esp',$ds_ref_sketch_esp);
						   ?>
						   <br>
							<div id="err_ds_ref_sketch_esp"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
							
						</div>
					  </div>
					</div>
					<div id='content_4_esp' <?php echo $style_n; ?>  >
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
							<label><b><?php echo ObtenEtiqueta(394);?></b></label>
							<br />
						  <?php
						   // Forma_CampoTinyMCE(ObtenEtiqueta(1291), False, 'ds_no_sketch_esp', $ds_no_sketch_esp, 50, 20, $ds_no_sketch_err);
						   FAMETinyMCE('ds_no_sketch_esp',$ds_no_sketch_esp);
						   ?>
						   <br>
						   <div id="err_ds_no_sketch_esp"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
							
						</div>
					  </div>
					</div>

	  
				</div>
	  
	    
				<!-- START French Content-->
				<div class="tab-pane fade in " id="assignment_fra">
		  
					<div id='content_p_fra' <?php echo $style_div_fg_animacion; ?>>
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
							<label><b><?php echo ObtenEtiqueta(1289);?></b></label>
							<br />
						  <?php
						   // Forma_CampoTinyMCE(ObtenEtiqueta(393), False, 'ds_animacion_fra', $ds_animacion_fra, 50, 20, $ds_animacion_err);
							FAMETinyMCE('ds_animacion_fra',$ds_animacion_fra);
						  ?>
						   <br>
						  <div id="err_ds_animacion_fra"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>									
						
						</div>
					  </div>
					</div>
					<div id='content_2_fra' <?php echo $style_div_fg_ref_animacion; ?>>
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
						 <label><b><?php echo ObtenEtiqueta(1290);?></b></label>
							<br />
						  <?php
						   // Forma_CampoTinyMCE(ObtenEtiqueta(1290), False, 'ds_ref_animacion_fra', $ds_ref_animacion_fra, 50, 20, $ds_ref_animacion_err);
							FAMETinyMCE('ds_ref_animacion_fra',$ds_ref_animacion_fra);
						   ?>
						   <br>
						  <div id="err_ds_ref_animacion_fra"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>									
						
						</div>
					  </div>
					</div>
					<div id='content_3_fra' <?php echo $style_div_fg_ref_sketch; ?>>
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
						 <label><b><?php echo ObtenEtiqueta(1292);?></b></label>
						  <br />
						  <?php
						  //  Forma_CampoTinyMCE(ObtenEtiqueta(1292), False, 'ds_ref_sketch_fra', $ds_ref_sketch_fra, 50, 20, $ds_ref_sketch_err); 
						  FAMETinyMCE('ds_ref_sketch_fra',$ds_ref_sketch_fra);
						  ?>
						  <br>
						  <div id="err_ds_ref_sketch_fra"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
							
						</div>
					  </div>
					</div>
					<div id='content_4_fra' <?php echo $style_n; ?>>
					  <div class="row">
						<div class="col-xs-12 col-sm-12">
						  <label><b><?php echo ObtenEtiqueta(394);?></b></label>
							<br />
						  <?php
						   // Forma_CampoTinyMCE(ObtenEtiqueta(1291), False, 'ds_no_sketch_fra', $ds_no_sketch_fra, 50, 20, $ds_no_sketch_err); 
							FAMETinyMCE('ds_no_sketch_fra',$ds_no_sketch_fra);
						   ?>
							<br>
						   <div id="err_ds_no_sketch_fra"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
							
						</div>
					  </div>
					</div>
	  
	  
				</div>
	  
		    </div>
		    <!-- END WIDGET BODY -->
		</div>
	</div>
</div> 
  <script type="text/javascript">
	//valida solo el rango de numeros de 0 - 100
	function Suma_val_resp_quiz(valor, name) {
		if (valor > 100)
		  document.getElementById(name).value = 100;
		if (valor < 0)
		  document.getElementById(name).value = 0;
	}
  
  
	function MuestraDescAssig(desc1) {
              element = document.getElementById("content_p");
              element_esp = document.getElementById("content_p_esp");
              element_fra = document.getElementById("content_p_fra");
              check = document.getElementById("fg_animacion");
			  
		    //Verifica los valores de los demas checkbox.
			var no_sketch=document.getElementById("no_sketch").value;
			var fg_animacion=document.getElementById("fg_animacion");
			var fg_ref_animacion=document.getElementById("fg_ref_animacion");
			var fg_ref_sketch=document.getElementById("fg_ref_sketch");
			var idiomas= document.getElementById("tabs_idiomas");
			
			if(fg_animacion.checked){
				fg_animacion=1;
			}else{
				fg_animacion=0;
			}
			if(fg_ref_animacion.checked){
				fg_ref_animacion=1;
			}else{
				fg_ref_animacion=0;
			}
			if(fg_ref_sketch.checked){
				fg_ref_sketch=1;
			}else{
				fg_ref_sketch=0;
			}				
			
			if(((no_sketch==0)||(no_sketch==''))&&(fg_animacion==0)&&(fg_ref_animacion==0)&&(fg_ref_sketch==0)){			
				idiomas.style.display="none";
			}else{
				idiomas.style.display="block";
			}
			  
              if (check.checked){ 
                  element.style.display='block';
                  element_esp.style.display='block';
                  element_fra.style.display='block';
                  if(desc1==0){
                    element.style.borderColor = "red";
                    element.style.color = "red";
                    element.style.background = "#fff0f0";
                    //element_esp.style.borderColor = "red";
                    //element_esp.style.color = "red";
                    //element_esp.style.background = "#fff0f0";
                    //element_fra.style.borderColor = "red";
                    //element_fra.style.color = "red";
                    //element_fra.style.background = "#fff0f0";
                  }
              }
              else {
                element.style.display='none';
                element_esp.style.display='none';
                element_fra.style.display='none';
              }     
          }
		  
    function MuestraDescAssigRef(desc2) {
        element = document.getElementById("content_2");
        element_esp = document.getElementById("content_2_esp");
        element_fra = document.getElementById("content_2_fra");
        check = document.getElementById("fg_ref_animacion");
		
		
		//Verifica los valores de los demas checkbox.
		var no_sketch=document.getElementById("no_sketch").value;
		var fg_animacion=document.getElementById("fg_animacion");
		var fg_ref_animacion=document.getElementById("fg_ref_animacion");
		var fg_ref_sketch=document.getElementById("fg_ref_sketch");
		var idiomas= document.getElementById("tabs_idiomas");
		
		if(fg_animacion.checked){
			fg_animacion=1;
		}else{
			fg_animacion=0;
		}
		if(fg_ref_animacion.checked){
			fg_ref_animacion=1;
		}else{
			fg_ref_animacion=0;
		}
		if(fg_ref_sketch.checked){
			fg_ref_sketch=1;
		}else{
			fg_ref_sketch=0;
		}				
		
		if(((no_sketch==0)||(no_sketch==''))&&(fg_animacion==0)&&(fg_ref_animacion==0)&&(fg_ref_sketch==0)){			
			idiomas.style.display="none";
		}else{
			idiomas.style.display="block";
		}
		
		
        if (check.checked){ 
            element.style.display='block';
            element_esp.style.display='block';
            element_fra.style.display='block';
            if(desc2==0){
              element.style.borderColor = "red";
              element.style.color = "red";
              element.style.background = "#fff0f0";
              //element_esp.style.borderColor = "red";
              //element_esp.style.color = "red";
              //element_esp.style.background = "#fff0f0";
              //element_fra.style.borderColor = "red";
              //element_fra.style.color = "red";
              //element_fra.style.background = "#fff0f0";
            }
        }
        else {
          element.style.display='none';
          element_esp.style.display='none';
          element_fra.style.display='none';
        }
    }
	function MuestraDescSketch( ){
		
		var desc3="";
        element = document.getElementById("content_3");
        element_esp = document.getElementById("content_3_esp");
        element_fra = document.getElementById("content_3_fra");
        check = document.getElementById("fg_ref_sketch");
		
		//Verifica los valores de los demas checkbox.
		var no_sketch=document.getElementById("no_sketch").value;
		var fg_animacion=document.getElementById("fg_animacion");
		var fg_ref_animacion=document.getElementById("fg_ref_animacion");
		var fg_ref_sketch=document.getElementById("fg_ref_sketch");
		var idiomas= document.getElementById("tabs_idiomas");
		
		if(fg_animacion.checked){
			fg_animacion=1;
		}else{
			fg_animacion=0;
		}
		if(fg_ref_animacion.checked){
			fg_ref_animacion=1;
		}else{
			fg_ref_animacion=0;
		}
		if(fg_ref_sketch.checked){
			fg_ref_sketch=1;
		}else{
			fg_ref_sketch=0;
		}				
		
		if(((no_sketch==0)||(no_sketch==''))&&(fg_animacion==0)&&(fg_ref_animacion==0)&&(fg_ref_sketch==0)){			
			idiomas.style.display="none";
		}else{
			idiomas.style.display="block";
		}
		
		
		
        if (check.checked){ 
            element.style.display='block';
            element_esp.style.display='block';
            element_fra.style.display='block';
			
            if(desc3==0){
              element.style.borderColor = "red";
              element.style.color = "red";
              element.style.background = "#fff0f0";
              //element_esp.style.borderColor = "red";
              //element_esp.style.color = "red";
              //element_esp.style.background = "#fff0f0";
              //element_fra.style.borderColor = "red";
              //element_fra.style.color = "red";
              //element_fra.style.background = "#fff0f0";
            }
        }
        else {
          element.style.display='none';
          element_esp.style.display='none';
          element_fra.style.display='none';
        }
    }

    function MuestraDescSketchNum(desc4) {
        
		element = document.getElementById("content_4");
		element_esp = document.getElementById("content_4_esp");
		element_fra = document.getElementById("content_4_fra");
        check = document.getElementById("no_sketch").value;		
		
		
		//Verifica los valores de los demas checkbox.
		var no_sketch=document.getElementById("no_sketch").value;
		var fg_animacion=document.getElementById("fg_animacion");
		var fg_ref_animacion=document.getElementById("fg_ref_animacion");
		var fg_ref_sketch=document.getElementById("fg_ref_sketch");
		var idiomas= document.getElementById("tabs_idiomas");
		
		if(fg_animacion.checked){
			fg_animacion=1;
		}else{
			fg_animacion=0;
		}
		if(fg_ref_animacion.checked){
			fg_ref_animacion=1;
		}else{
			fg_ref_animacion=0;
		}
		if(fg_ref_sketch.checked){
			fg_ref_sketch=1;
		}else{
			fg_ref_sketch=0;
		}				
		
		if(((no_sketch==0)||(no_sketch==''))&&(fg_animacion==0)&&(fg_ref_animacion==0)&&(fg_ref_sketch==0)){			
			idiomas.style.display="none";
		}else{
			idiomas.style.display="block";
		}
		
		
		
        if (check >= 1) {
            element.style.display='block';
			element_esp.style.display='block';
            element_fra.style.display='block';
            if(desc4==0){
              element.style.borderColor = "red";
              element.style.color = "red";
              element.style.background = "#fff0f0";
			  //element_esp.style.borderColor = "red";
              //element_esp.style.color = "red";
              //element_esp.style.background = "#fff0f0";
			 // element_fra.style.borderColor = "red";
              //element_fra.style.color = "red";
              //element_fra.style.background = "#fff0f0";
            }
        }
        else {
          element.style.display='none';
          element_esp.style.display='none';
          element_fra.style.display='none';
        }
    }

	$('#fg_animacion').change(function (){
		MuestraDescAssig(<?php echo $descripcion1;?>);
			
	});
	$('#fg_ref_animacion').change(function (){
		MuestraDescAssigRef(<?php echo $descripcion2;?>);
	});
	$('#fg_ref_sketch').change(function (){
		MuestraDescSketch();
	});
    
  </script>
