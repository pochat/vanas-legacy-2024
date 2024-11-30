<style>
    .dropzone .dz-default.dz-message {
    background-image: url(../fame/img/spritemap_fame.png)!important;
    margin-left: -179px !important;
    margin-top: -84.5px !important;
    }
	.bg-color-red {
    background-color: #c7123cab!important;
	}
</style> 
  


<?php
	if(empty($tot_preguntas)){
		$tot_preguntas=1;
	}



    if(!empty($clave)){
      echo "<p></p><center><div id='muestra_loading' style='display:block;'>
            <span id='ump' class='ui-widget  txt-color-black'>
            <i class='fa fa-cog fa-4x  fa-spin txt-color-black'></i><h2><strong> Loading....</strong></h2>
            </span>
            </div></center>
            <br>";
    }
  ?>
  <div id="tabs2" style="display:none;">
    <?php
      echo "<script>
              function suma_quiz_tot(actual){
                val_org = document.getElementById('ds_course_1').value;
                if(actual == '')
                  actual = 0;
                // muestra_wng = document.getElementById('muestra_wng');";
      if(empty($clave)){
        echo "
		//alert('entyro1');
            val_sum_org = document.getElementById('val_sum_org').value;
			
            if(val_sum_org=='')
              val_sum_org = 100;
            val_princi  = 100 - parseInt(val_sum_org);
            res_suma = parseInt(val_princi) + parseInt(actual);
            res_tot = 100 - parseInt(res_suma);";
      }else{
        echo "
			
            no_lecciones = document.getElementById('no_lecciones').value;
            res_suma = 0;
            for(x=0; x<no_lecciones; x++){
              quiz_rem_x = document.getElementById('quiz_rem_' + x).value;
              res_suma = parseInt(res_suma) + parseInt(quiz_rem_x);
            }
            if(res_suma=='NaN'){
              res_suma=0;
            }
            res_suma = parseInt(res_suma) + parseInt(actual);
            res_tot = 100 - parseInt(res_suma);
            
            
            if(Number.isInteger(res_tot)){
              
              console.log('Yes')
            }else{
                   var res_tot=0
            }

            
            ";
        
      }
        echo "
		
		    document.getElementById('ds_course_1').value = res_tot;
            document.getElementById('c_remaining').value = res_tot;
            style_u = document.getElementById('error_msj2').className;
      
            if(res_tot >= 1){
              // muestra_wng.style.display='block';                    
              $('#error_msj2').removeClass('input').addClass('input state-error');
              document.getElementById('no_valor_quiz').style.backgroundColor = '#dfb56c';
              document.getElementById('no_valor_quiz').style.borderColor = '#dfb56c';
            }else{
              // muestra_wng.style.display='none'; 
              $('#error_msj2').removeClass('input state-error').addClass('input');
              document.getElementById('no_valor_quiz').style.backgroundColor = '#fff';
              document.getElementById('no_valor_quiz').style.borderColor = '#ccc';
            }
            if(res_suma > 100){
              document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
              document.getElementById('no_valor_quiz').style.borderColor = '#953b39';
              muestra_err.style.display='block';
              if(style_u == 'input')
                $('#error_msj2').removeClass('input').addClass('input state-error');
              else
                $('#error_msj2').removeClass('input state-error').addClass('input state-error');
            }else{
              muestra_err.style.display='none';
            }
          }         
            </script>";
			
      $Query = "SELECT no_valor_quiz FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa AND fl_leccion_sp <> $clave ";
      $rs = EjecutaQuery($Query);
      $tot_reg_quiz=CuentaRegistros($rs);

      for($i=0;$row = RecuperaRegistro($rs);$i++){
        $quiz_rem = !empty($row[0])?$row[0]:0;
        //Forma_CampoOculto("quiz_rem_$i", $quiz_rem);
        echo"<input type='hidden' name='quiz_rem_$i' id='quiz_rem_$i' value='".$quiz_rem."'>";
                                                          
      }
      //Forma_CampoOculto('no_lecciones', $i);
      echo"<input type='hidden' name='no_lecciones' id='no_lecciones' value='".$i."'>";
      if(!empty($clave)){
        $row = RecuperaValor("SELECT (100 - SUM(no_valor_quiz)) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa");
        $valor_inicial = !empty($row[0])?$row[0]:0;
      }
      # Estos campos se utilizan cuando el registro es nuevo y controla el peso del quiz
      //Forma_CampoOculto('val_sum_org');
      echo"<input type='hidden' name='val_sum_org' id='val_sum_org' value=''>";
      //Forma_CampoOculto('val_inc_tabs', 1);
      echo"<input type='hidden' name='val_inc_tabs' id='val_inc_tabs' value='1'>";
     echo "<script>
        function Suma_val_preg_quiz(actual, val){ 
          band  = 0;
          band2 = 0;
          res_suma = 0;                        
          // Numero de preguntas
          no_lec_2 = document.getElementById('ContTabCounterLimit').value;
          no_lec   = parseInt(no_lec_2) - parseInt(1);
          for(x=1; x<=no_lec; x++){
            document.getElementById('muestra_valor_' + x).innerHTML  = '".ObtenEtiqueta(1200)." ' + x + ' <b>(' + document.getElementById('valor_' + x).value + ' %)</b>';
            quiz_rem_x = document.getElementById('valor_' + x).value;                
            if(quiz_rem_x == '')
              quiz_rem_x = 0;
            res_suma = parseInt(res_suma) + parseInt(quiz_rem_x);
          }     
          res_fin = 100 - parseInt(res_suma);
          for(x=1; x<=no_lec; x++){
            quiz_remainning = document.getElementById('ds_quiz_' + x).value = res_fin;
            document.getElementById('q_remaining_' + x).value = res_fin;
          }
          for(x=1; x<=no_lec; x++){
            if(res_suma > 100){
              // document.getElementById('muestra_err_preg_'+x).style.display='block';
              band = 1;
            }
            else{
              // document.getElementById('muestra_err_preg_'+x).style.display='none';
              band = 0;
            }
            if(res_fin >= 1){
              // document.getElementById('muestra_wng_preg_'+x).style.display='block';
              band2 = 1;
            }
            else{
              // document.getElementById('muestra_wng_preg_'+x).style.display='none';
              band2 = 0;
            }      
            if(band == 1 || band2 == 1){
              if(res_suma > 100){
              document.getElementById('valor_' + x).style.backgroundColor = '#FFF0F0';
              document.getElementById('valor_' + x).style.borderColor = '#953b39';                        
              }else{
              document.getElementById('valor_' + x).style.backgroundColor = '#efe1b3';
              document.getElementById('valor_' + x).style.borderColor = '#dfb56c';
              }
            }
            else{
              document.getElementById('valor_' + x).style.backgroundColor = '#fff';
              document.getElementById('valor_' + x).style.borderColor = '#ccc';
            }
          }
        }     
      </script>";

      $row_a = RecuperaValor("SELECT SUM(ds_valor_pregunta) FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp");
      $suma_ini_preg = $row_a[0];
      //Forma_CampoOculto('suma_ini_preg', $suma_ini_preg);
      echo"<input type='hidden' name='suma_ini_preg' id='suma_ini_preg' value='".$suma_ini_preg."'>";
      if(!empty($clave)){
        if(empty($fg_error)){
          $row_b = RecuperaValor("SELECT (100 - SUM(ds_valor_pregunta)) FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp");
          $valor_ini_preg = $row_b[0];
        }else{
          $valor_ini_preg = $valor_ini_preg;
        }
      }else{
        if($valor_ini_preg == NULL){
          $valor_ini_preg = 100;
        }
      }
    ?>
    <div class="row">
		<div class="col-xs-12 col-sm-3">      
			<div class="smart-form">
			<input type="hidden" name="tab" id="tab" value="4">
			<?php FAMEInputText(ObtenEtiqueta(1253),'nb_quiz',$nb_quiz,false); 
			?>
			</div>
		</div>     
		  <?php 
			$var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1281)."' tabindex='100000'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
		  ?>
		<div class="col-xs-12 col-sm-3">
			<div id="div_no_semana2" class="" style="padding-top:5px;" <?php echo popover('','',$warning, ObtenEtiqueta(1284));  ?> >
							<label class="control-label">
								<strong><?php echo $var.ObtenEtiqueta(1254); ?>:</strong>
							</label> 
				<div class="smart-form">
					<section>
							    	
						  <label class="input" id="error_msj2">
							<input class="form-control" id="no_valor_quiz" name="no_valor_quiz" value="<?php echo $no_valor_quiz; ?>" maxlength="3" size="12" type="text" onkeyup="suma_quiz_tot(this.value); Suma_val_resp_quiz(this.value, 'no_valor_quiz'); Valida_Quiz();" onKeyPress="return SoloNumeros(event);" >
							<?php
							  if(!empty($err_val_quiz_pregs))
								echo "<span class='help-block txt-color-red'><i class='fa fa-warning'></i>".ObtenMensaje(3)."</span>";
							?>                          
						  </label>                              
					</section>
				</div>  
			</div>               
       </div>             
       <div class="col-xs-12 col-sm-3">
            <?php 
            //Forma_CampoOculto('c_remaining', $c_remaining);
            echo"<input type='hidden' name='c_remaining' id='c_remaining' value='".$c_remaining."'>";
            //Forma_CampoTexto(ObtenEtiqueta(1214),False,'ds_course_1',$valor_inicial,3,3,$ds_course_1_err, False, '', True, 'disabled', '', "form-group", 'right', 'col col-sm-8', 'col col-sm-3');
            ?> 
            <div class="smart-form">
			  <?php FAMEInputText(ObtenEtiqueta(1214),'ds_course_1',$valor_inicial,false,'','',"class='form-control' disabled");?>
		    </div>
          
             <?php 
              if($c_remaining != 0){
                echo "<script>
                  document.getElementById('ds_course_1').value = document.getElementById('c_remaining').value;
                </script>";
              }
            ?>
      </div>
      <div class="col-xs-12 col-sm-2">
        <div class="pull-right" style="border-top-width: 0!important; margin-top: 0px!important; font-weight: 700;">
          <!-- <a class="btn btn-danger btn-md" href="javascript:void(0);" id="tabs2"><i class="fa fa-minus-square"></i> <?php # echo ObtenEtiqueta(1210); ?></a> -->
        </div>                            
      </div>
    </div>
	
    <?php   
      if(!empty($err_val_quiz_pregs)){
        $style_v = "style='display:block;'";
        echo "
        <script>
          $('#div_no_semana2').addClass('smart-form');
          $('#error_msj2').addClass('state-error');
        </script>";

      }
      else{
        $style_v = "style='display:none;'";                  
      }
    ?>
    <!-- Error del valor del quiz sobre pasa el remaining -->
    <div id="muestra_err" style="display:none;">
      <div class="row">
        <div class="col-xs-1 col-sm-1"></div>
        <div class="col-xs-10 col-sm-10">
          <div class="alert alert-danger fade in">
            <i class="fa-fw fa fa-times"></i>
            <strong><?php echo ObtenEtiqueta(1285); ?> </strong>
          </div>
        </div>
        <div class="col-xs-1 col-sm-1"></div>
      </div>
    </div> 
    <!-- -->
    <div id="muestra_err_val" <?php echo $style_v; ?>>
      <div class="row">
        <div class="col-xs-1 col-sm-1"></div>
        <div class="col-xs-10 col-sm-10">
          <div class="alert alert-danger fade in">
            <i class="fa-fw fa fa-times"></i>
            <strong><?php echo ObtenEtiqueta(1361); ?> </strong>
          </div>
        </div>
        <div class="col-xs-1 col-sm-1"></div>
      </div>
    </div>             
    <hr style="margin-top: 0px; margin-bottom: 15px;">
    <div id="msg_err_tabs" class="hidden">
      <div class="row padding-10">
        <i class="fa-fw fa fa-danger"></i>
        <code><?php echo ObtenEtiqueta(1893); ?></code>
      </div>
    </div>
    <input type="hidden" id="CuantosLi" name="CuantosLi" value="<?php echo $tot_preguntas; ?>">
    <ul id="tabss" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
		<li> 
			<a id="add_tab" style="cursor:pointer;"><i class="fa fa-lg fa-plus-circle"></i></a>
		</li>
		<li class="active" id="cont_tab_quiz_1" <?php //echo popover('','',$warning,ObtenEtiqueta(1894)); ?>>             
			<small id="delete_quiz" class="air air-top-left delete-tab txt-color-red" style="top:7px; left:7px;" rel="tooltip" data-placement="top" 
			data-original-title="<?php echo ObtenEtiqueta(1891)?>">
			  <div class="btn btn-xs font-xs btn-default hover-transparent txt-color-red"><i class="fa fa-times"></i></div>
			</small>
			<a href="#tabs-1"><div id="muestra_valor_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(1200); ?> 1 <b>(<?php echo $ds_quiz_1; ?> %)</b></div></a>
		</li>
		<?php 
		if(!empty($clave)){
			if(empty($fg_error)){
				$row = RecuperaValor("SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp");
				$no_preguntas = $row[0];
			}else{
			  $no_preguntas = $no_max_tabs;
			}
        }else{
			$no_preguntas = $no_max_tabs;
		}
		
			for($i=2;$i<=$no_preguntas;$i++){
				echo "<li style='position:relative;' class='ui-state-default ui-corner-top' role='tab' tabindex='-1' aria-controls='tabs-2' aria-labelledby='ui-id-4' aria-selected='false'>
						<span class='air air-top-left delete-tab' style='top:7px; left:7px;'><div class='btn btn-xs font-xs btn-default hover-transparent'><i class='fa fa-times'></i></div></span>
						<a href='#tabs-$i' class='ui-tabs-anchor' role='presentation' tabindex='-1' id='ui-id-4'>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				if(!empty($clave)){
				  if(empty($fg_error)){
					$rs_vp = EjecutaQuery("SELECT ds_valor_pregunta FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp AND no_orden = $i ORDER BY no_orden");
					$row_vp = RecuperaRegistro($rs_vp);
					$ds_valor_pregunta = $row_vp[0];
					echo "<span id='muestra_valor_$i'>".ObtenEtiqueta(1200)." $i <b>($ds_valor_pregunta %)</b></span>";
				  }else{
					echo "<span id='muestra_valor_$i'>".ObtenEtiqueta(1200)." $i <b>($valor_preg_[$i] %)</b></span>";
				  }
				}
				else{
					  if(empty($fg_error)){
						$rs_vp = EjecutaQuery("SELECT ds_valor_pregunta FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp AND no_orden = $i ORDER BY no_orden");
						$row_vp = RecuperaRegistro($rs_vp);
						$ds_valor_pregunta = $row_vp[0];
						echo "<span id='muestra_valor_$i'>".ObtenEtiqueta(1200)." $i <b>($ds_valor_pregunta %)</b></span>";
					  }else{
						echo "<span id='muestra_valor_$i'>".ObtenEtiqueta(1200)." $i <b>($valor_preg_[$i] %)</b></span>";
					  }
				}                        
				echo "</a></li>";
			}
    ?>
    </ul>
	
	
	
    <!-- START QUIZ -->
    <div id="tabs-1">
      <div class="row">
      <script type="text/javascript">
          function showContent(val) {
            img_b_1 = document.getElementById("img_based_1");
            img_b_3 = document.getElementById("img_based_3");
            img_b_2 = document.getElementById("img_based_2");
            img_b_2_esp = document.getElementById("img_based_2_esp");
            img_b_2_fra = document.getElementById("img_based_2_fra");
            document.getElementById("fg_tipo_preg_1").value = val;
            check = document.getElementById("fg_tipo_resp_1");
            if (check.checked) {
                img_b_1.style.display='none';
                img_b_3.style.display='none';
                img_b_2.style.display='block';
                img_b_2_esp.style.display='block';
                img_b_2_fra.style.display='block';
            }
            else {
                img_b_1.style.display='block';
                img_b_3.style.display='block';
                img_b_2.style.display='none';
                img_b_2_esp.style.display='none';
                img_b_2_fra.style.display='none';
            }
          }
          // Pregunta con imagen, vemos el tipo de imagen
          function TipoImagen(val){
            document.getElementById("fg_tipo_img_prev_1").value = val;
          }
        </script>



        <div class="col col-xs-6 col-sm-4">
          <?php                   
            echo "<div class='form-group'>";
            echo "<label class='col-md-3 control-label text-align-right'>";
            echo "<strong>";
            echo ObtenEtiqueta(1201);
            echo "</strong>";
            echo "</label>";
            echo "<div class='col-md-9'><div class='smart-form'>";
            echo "<label>";		
            FAMECampoRadio('fg_tipo_resp_1', 'T', $fg_tipo_resp_1, ObtenEtiqueta(1202), True, 'onchange="showContent(this.value); Valida_Quiz();"');
            echo "</label>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "<label>";
            FAMECampoRadio('fg_tipo_resp_1', 'I', $fg_tipo_resp_1, ObtenEtiqueta(1203), True, 'onchange="showContent(this.value); Valida_Quiz();"');
            echo "</label>";
            echo "</div>";
            echo "</div>";
            //Forma_CampoOculto("fg_tipo_preg_1", $fg_tipo_resp_1);
            echo"<input type='hidden' name='fg_tipo_preg_1' id='fg_tipo_preg_1' value='".$fg_tipo_resp_1."'>";
            echo "</div>";
            
          ?>
        </div>
        <?php
          if($fg_tipo_resp_1 == 'T')
            $style_1 = "style='display: none;'";
          else
            $style_1 = "style='display: block;'";
        ?>
        <div id="img_based_1" <?php echo $style_1; ?>>
          <div class="col col-xs-6 col-sm-6">
            <?php                           
              echo " <div class='form-group'>";
              echo "<label class='col-md-2 control-label text-align-right'>";
              echo "<strong></strong>";
              echo "</label>";
              echo "<div class='col-md-8'>";
              echo "<div class='smart-form'>";
              echo "<label>";                             
              FAMECampoRadio('fg_tipo_img_1', 'L', $fg_tipo_img_1, ObtenEtiqueta(1211), True, "onchange='TipoImagen(this.value);'");
              echo "</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>";
              FAMECampoRadio('fg_tipo_img_1', 'P', $fg_tipo_img_1, ObtenEtiqueta(1212), True, "onchange='TipoImagen(this.value);'");
              echo "</label>";
              echo "</div>";
              echo "</div>";
              ///Forma_CampoOculto("fg_tipo_img_prev_1", $fg_tipo_img_1);
              echo"<input type='hidden' name='fg_tipo_img_prev_1' id='fg_tipo_img_prev_1' value='".$fg_tipo_img_1."'>";
              echo "</div>";                        
            ?>
          </div>
        </div>   
      </div>
      <div class="row">
        <br>
      </div>
      <!-- START QUIZ Lang Tabs -->
      <div class="tab-pane fade in active" id="quiz_lang">
        <!-- START WIDGET BODY -->
        <div class="widget-body">
          <ul id="myTabQLang" class="nav nav-tabs bordered">
            <li class="active">
              <a id="mytabQL_eng" href="#tab-quiz_eng" data-toggle="tab">
                English
              </a>
            </li>
            <li class="">
              <a id="mytabQL_esp" href="#tab-quiz_esp" data-toggle="tab">
                Spanish
              </a>
            </li>
            <li class="">
              <a id="mytabQL_fra" href="#tab-quiz_fra" data-toggle="tab">
                French
              </a>
            </li>
          </ul>
          <div id="myTabQC" class="tab-content padding-10 no-border">
            <div class="tab-pane fade in active" id="tab-quiz_eng">
              <!-- START English Content-->
              <div class="row">
                <div class="col col-xs-12 col-sm-5">
                  <?php
                    //Forma_CampoTexto(ObtenEtiqueta(1200).' 1',False,'ds_pregunta_1',$ds_pregunta_1,255,60,$ds_pregunta_1_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                  ?>

                      <div class="smart-form">
			            <?php FAMEInputText(ObtenEtiqueta(1200).' 1','ds_pregunta_1',$ds_pregunta_1,false); ?>
		              </div>

                </div>  
                <div class="col col-xs-12 col-sm-1">
                </div> 
                <?php
                  $var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1282)."' tabindex='100000'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
                ?>
                <div class="col col-xs-12 col-sm-2">
                  <div id="div_no_semana3" class="row form-group" <?php echo popover('popover', 'top', $warning, ObtenEtiqueta(1286)); ?>>
                    <label class="col col-sm-12 control-label text-align-left">
                      <strong><?php echo $var.ObtenEtiqueta(1255); ?></strong>
                    </label>
                    <div class="col-sm-12"> 
                      <div class="smart-form">  
                        <label class="input" id="error_msj3_1">
                          <input class="form-control" id="valor_1" name="valor_1" value="<?php echo $ds_quiz_1; ?>" maxlength="3" size="10" type="text" onkeyup="Suma_val_preg_quiz(this.value); Suma_val_resp_quiz(this.value, 'valor_1'); Valida_Quiz(); $('#muestra_valor_1').removeAttr('padding-left').css('padding-left', '15%');$('#muestra_valor_1').removeAttr('padding-right').css('padding-right', '15%');" onKeyPress="return SoloNumeros(event);">
                        </label>
                        <?php
                        if(!empty($err_val_quiz_preg) || !empty($err_sum_val_preg_min) || !empty($err_sum_val_preg_max))
                          echo "<span class='help-block txt-color-red'><i class='fa fa-warning'></i>".ObtenMensaje(3)."</span>";
                        ?>
                      </div>
                    </div>  
                  </div>
                </div>                  
                <div class="col col-xs-12 col-sm-2">
                  <?php                    
                    //Forma_CampoOculto('q_remaining_1', $q_remaining_1);
                    //Forma_CampoTexto(ObtenEtiqueta(1213),False,'ds_quiz_1',$valor_ini_preg,3,10,$ds_quiz_1_err, False, '', True, 'disabled', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                  ?>  
                  <div class="smart-form">
			        <?php 
                        echo"<input type='hidden' name='q_remaining_1' id='q_remaining_1' value='".$q_remaining_1."'>";
                        FAMEInputText(ObtenEtiqueta(1213),'ds_quiz_1',$valor_ini_preg,false,'','',true); 
                     ?>
		          </div>
                  
                  <?php
                  
                     // if(empty($fg_error)){
                      if($q_remaining_1 != 0){
                        echo "<script>
                          document.getElementById('ds_quiz_1').value = document.getElementById('q_remaining_1').value;
                        </script>";
                      }
                    // }
                  ?>
                </div> 
                <div class="col col-xs-12 col-sm-2">
                  <?php
                    echo "<div id='div_ds_quiz_1' class='row form-group '>
                      <label class='col col-sm-12 control-label text-align-left'><strong>&nbsp;&nbsp;  </strong></label>
                      <div class='col col-sm-12'><label class='input'><a href='javascript:MuestraPreview_1(1);' class='btn btn-primary'  >".ObtenEtiqueta(1208)."</a></label></div>      
                    </div>";
                  ?>
                </div>
              </div>
              <!------------START OF ANSWERS-------------->
              <div class="row"> 
                <?php
                  if($valor_ini_preg == 0)
                    $style = "style='display:none;'";
                  elseif($valor_ini_preg > 0)
                    $style = "style='display:block;'";
                  else
                    $style = "style='display:none;'";
                ?>
              </div>
              <?php
              if(!empty($err_val_quiz_preg) || !empty($err_sum_val_preg_min) || !empty($err_sum_val_preg_max)){
              ?>
                <script>
                $("#error_msj3_1").addClass("state-error");
                </script>
              <?php
              }
              else{
              ?>
                <script>
                $("#error_msj3_1").removeClass("state-error");
                </script>
              <?php
              }
              if(!empty($err_valor_repuestas_err)){
              ?>
              <div>
                <div class="row">
                  <div class="col-xs-1 col-sm-1"></div>
                    <div class="col-xs-10 col-sm-10">
                      <div class="alert alert-danger fade in">
                        <i class="fa-fw fa fa-times"></i>
                        <strong><?php echo ObtenEtiqueta(1358); ?></strong>
                      </div>
                    </div>
                  <div class="col-xs-1 col-sm-1"></div>
                </div>
              </div>
              <?php
              }
              if($fg_tipo_resp_1 == 'T'){
                  $style_2 = "style='display: block;'";
              }else{
                  $style_2 = "style='display: none;'";
              }
              ?>
              <div class="row hidden" id="error_preguntas_valores">
                <div class="col col-sm-12 col-md-12 col-lg-12">
                  <i class="fa fa-warning txt-color-red"></i> <code><?php echo ObtenEtiqueta(1895); ?></code>
                </div>
              </div>
              <div id="img_based_2" <?php echo $style_2; ?>> 
                <div class="row">
                  <div class="col col-xs-12 col-sm-3">
                      <div class="smart-form">
						<?php
						 // Forma_CampoTexto(ObtenEtiqueta(1204).' 1',False,'ds_resp_1',$ds_resp_1,255,41,$ds_resp_1_err, False, '', True, '', '', "from-group", 'left', 'col col-sm-12', 'col col-sm-12');
						FAMEInputText(ObtenEtiqueta(1204).' 1','ds_resp_1',$ds_resp_1,false);

                        ?>
                      </div>
                  </div>
                  <div class="col col-xs-12 col-sm-1">
                  </div>
                  <div class="col col-xs-12 col-sm-2">
                      <?php  $var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1283)."' tabindex='100000'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
                      ?>
                      <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
                      <div class="smart-form">
						<?php
						  //Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_1',$ds_grade_1,3,10,$ds_grade_1_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_1\'); Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');                     
						  FAMEInputText('','ds_grade_1',$ds_grade_1,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_1'); Valida_Quiz();");
						  
						  
						  ?>
                      </div>
                  </div>
                  <div class="col col-xs-12 col-sm-3">
						<div class="smart-form">
							<?php
							  //Forma_CampoTexto(ObtenEtiqueta(1204).' 2',False,'ds_resp_2',$ds_resp_2,255,41,$ds_resp_2_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
							  FAMEInputText(ObtenEtiqueta(1204).' 2','ds_resp_2',$ds_resp_2,false);
                            ?>
						</div>
                  </div>
                  <div class="col col-xs-12 col-sm-1">
                  </div>
                  <div class="col col-xs-12 col-sm-2">
                        <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
					    <div class="smart-form">
						<?php
						  //Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_2',$ds_grade_2,3,10,$ds_grade_2_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_2\'); Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
						FAMEInputText('','ds_grade_2',$ds_grade_2,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_2'); Valida_Quiz();");
						
						?>
						</div>
                  </div>
                </div>
                <div class="row">           
                  <div class="col col-xs-12 col-sm-3">
                      <div class="smart-form">
						<?php
						 // Forma_CampoTexto(ObtenEtiqueta(1204).' 3',False,'ds_resp_3',$ds_resp_3,255,41,$ds_resp_3_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');                
						 FAMEInputText(ObtenEtiqueta(1204).' 3','ds_resp_3',$ds_resp_3,false);
                        ?>            
		              </div>
		  
		  
                  </div>
                  <div class="col col-xs-12 col-sm-1">
                  </div>
                  <div class="col col-xs-12 col-sm-2">
                      <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
                      <div class="smart-form">
						<?php
						  //Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_3',$ds_grade_3,3,10,$ds_grade_3_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_3\'); Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
						 FAMEInputText('','ds_grade_3',$ds_grade_3,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_3'); Valida_Quiz();");
						
						?>
					   </div>
                  </div>
                  <div class="col col-xs-12 col-sm-3">
					  <div class="smart-form">
                      <?php
                      //Forma_CampoTexto(ObtenEtiqueta(1204).' 4',False,'ds_resp_4',$ds_resp_4,255,41,$ds_resp_4_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                      FAMEInputText(ObtenEtiqueta(1204).' 4','ds_resp_4',$ds_resp_4,false);
                      ?>
					  </div>
                  </div>
                  <div class="col col-xs-12 col-sm-1">
                  </div>
                  <div class="col col-xs-12 col-sm-2">
                    <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
					<div class="smart-form">
                    <?php
                      //Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_4',$ds_grade_4,3,10,$ds_grade_4_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_4\');Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                      FAMEInputText('','ds_grade_4',$ds_grade_4,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_4'); Valida_Quiz();");                 
                      ?>
					</div>
                  </div>
                </div>
              </div>
              <!------------END OF ANSWERS-------------->
              
            </div>



            <!-- END English Content-->
              <div class="tab-pane fade in " id="tab-quiz_esp">
                <!-- START Spanish Content-->
                <div class="row">
                  <div class="col col-xs-12 col-sm-5">
					<div class="smart-form">
						<?php
						  //Forma_CampoTexto(ObtenEtiqueta(1200).' 1',False,'ds_pregunta_esp_1',$ds_pregunta_esp_1,255,60,$ds_pregunta_1_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
						  FAMEInputText(ObtenEtiqueta(1200).' 1','ds_pregunta_esp_1',$ds_pregunta_esp_1,false);
						
						?>
					</div>
                  </div>  
                  <div class="col col-xs-12 col-sm-1">
                  </div> 
                  <?php
                    $var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1282)."' tabindex='100000'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
                  ?>
                  <div class="col col-xs-12 col-sm-2">
                    <div id="div_no_semana3" class="row form-group" <?php echo popover('popover', 'top', $warning, ObtenEtiqueta(1286)); ?>>
                      <label class="col col-sm-12 control-label text-align-left">
                      <strong>
                        <?php echo $var.ObtenEtiqueta(1255); ?>
                      </strong>
                      </label>
                      <div class="col-sm-12"> 
                        <div class="smart-form">  
                          <label class="input" id="error_msj3_1">
                            <input class="form-control" id="valor_1esp" name="valor_1esp" value="<?php echo $ds_quiz_1; ?>" maxlength="3" size="10" type="text" 
                            onkeyup="Suma_val_preg_quiz(this.value); Suma_val_resp_quiz(this.value, 'valor_1'); Valida_Quiz(); $('#muestra_valor_1').removeAttr('padding-left').css('padding-left', '20%');" onKeyPress="return SoloNumeros(event);">
                          </label>
                          <?php
                          if(!empty($err_val_quiz_preg) || !empty($err_sum_val_preg_min) || !empty($err_sum_val_preg_max))
                            echo "<span class='help-block txt-color-red'><i class='fa fa-warning'></i>".ObtenMensaje(3)."</span>";
                          ?>
                        </div>
                      </div>  
                    </div>
                  </div>              
                  <div class="col col-xs-12 col-sm-2">
				    <div class="smart-form">
                    <?php                    
                     // Forma_CampoOculto('q_remaining_1', $q_remaining_1);
                     // Forma_CampoTexto(ObtenEtiqueta(1213),False,'ds_quiz_1_esp',$valor_ini_preg,3,10,$ds_quiz_1_err, False, '', True, 'disabled', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        echo"<input type='hidden' name='q_remaining_1' id='q_remaining_1' value='".$q_remaining_1."'>";
					     FAMEInputText(ObtenEtiqueta(1213).' 1','ds_quiz_1_esp',$valor_ini_preg,false);
                    ?>
					</div>
					<?php   
					  // if(empty($fg_error)){
                        if($q_remaining_1 != 0){
                          echo "<script>
                            document.getElementById('ds_quiz_1').value = document.getElementById('q_remaining_1').value;
                          </script>";
                        }
                      // }
                    ?>
                  </div> 
                  <div class="col col-xs-12 col-sm-2">
                    <?php
                      echo "<div id='div_ds_quiz_1' class='row form-group '>
                        <label class='col col-sm-12 control-label text-align-left'><strong>&nbsp;&nbsp;  </strong></label>
                        <div class='col col-sm-12'><label class='input'><a href='javascript:MuestraPreview_1(1);' class='btn btn-primary'  >".ObtenEtiqueta(1208)."</a></label></div>      
                      </div>";
                    ?>
                  </div>
                </div>
                <!------------START OF ANSWERS-------------->
                <div class="row"> 
                  <?php
                    if($valor_ini_preg == 0)
                      $style = "style='display:none;'";
                    elseif($valor_ini_preg > 0)
                      $style = "style='display:block;'";
                    else
                      $style = "style='display:none;'";
                  ?>
                </div>
                <?php
                  if(!empty($err_val_quiz_preg) || !empty($err_sum_val_preg_min) || !empty($err_sum_val_preg_max)){
                ?>
                <script>
                  $("#error_msj3_1").addClass("state-error");
                </script>
                <?php
                  }
                  else{
                ?>
                <script>
                  $("#error_msj3_1").removeClass("state-error");
                </script>
                <?php
                  }
                  if(!empty($err_valor_repuestas_err)){
                ?>
                <div>
                  <div class="row">
                    <div class="col-xs-1 col-sm-1">
                    </div>
                    <div class="col-xs-10 col-sm-10">
                      <div class="alert alert-danger fade in">
                        <i class="fa-fw fa fa-times">
                        </i>
                        <strong>
                          <?php echo ObtenEtiqueta(1358); ?>
                        </strong>
                      </div>
                    </div>
                    <div class="col-xs-1 col-sm-1">
                    </div>
                  </div>
                </div>
                <?php
                  }
                    if($fg_tipo_resp_1 == 'T')
                      $style_2 = "style='display: block;'";
                    else
                      $style_2 = "style='display: none;'";
                ?>
                <div class="row hidden" id="error_preguntas_valores">
                  <div class="col col-sm-12 col-md-12 col-lg-12">
                    <i class="fa fa-warning txt-color-red">
                    </i>
                    <code>
                      <?php echo ObtenEtiqueta(1895); ?>
                    </code>
                  </div>
                </div>
                <div id="img_based_2_esp" <?php echo $style_2; ?>> 
                  <div class="row">
                    <div class="col col-xs-12 col-sm-3">
						<div class="smart-form">
							<?php 
							  //Forma_CampoTexto(ObtenEtiqueta(1204).' 1',False,'ds_resp_esp_1',$ds_resp_esp_1,255,41,$ds_resp_1_err, False, '', True, '', '', "from-group", 'left', 'col col-sm-12', 'col col-sm-12');
                            FAMEInputText(ObtenEtiqueta(1204).' 1','ds_resp_esp_1',$ds_resp_esp_1,false); ?>
						  </div>
		  
                    </div>
                    <div class="col col-xs-12 col-sm-1">
                    </div>
                    <div class="col col-xs-12 col-sm-2">
                      <?php 
                        $var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1283)."' tabindex='100000'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";              
                      ?>
                      <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
					  <div class="smart-form">
                      <?php
                        //Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_1esp',$ds_grade_1,3,10,$ds_grade_1_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_1\'); Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        FAMEInputText('','ds_grade_1esp',$ds_grade_1,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_1'); Valida_Quiz();");
						  
					  
					  ?>
					  </div>
                    </div>
                    <div class="col col-xs-12 col-sm-3">
						<div class="smart-form">
						  <?php
							//Forma_CampoTexto(ObtenEtiqueta(1204).' 2',False,'ds_resp_esp_2',$ds_resp_esp_2,255,41,$ds_resp_2_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
							FAMEInputText(ObtenEtiqueta(1204).' 2','ds_resp_esp_2',$ds_resp_esp_2,false); 
                          ?>
						</div>
                    </div>
                    <div class="col col-xs-12 col-sm-1">
                    </div>
                    <div class="col col-xs-12 col-sm-2">
                        <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
						<div class="smart-form">
						  <?php
							//Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_2esp',$ds_grade_2,3,10,$ds_grade_2_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_2\'); Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
							FAMEInputText('','ds_grade_2esp',$ds_grade_2,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_2'); Valida_Quiz();");							 
						  ?>
						</div>
                    </div>
                  </div>
                  <div class="row">           
                    <div class="col col-xs-12 col-sm-3">
						<div class="smart-form">
						  <?php
							//Forma_CampoTexto(ObtenEtiqueta(1204).' 3',False,'ds_resp_esp_3',$ds_resp_esp_3,255,41,$ds_resp_3_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
							FAMEInputText(ObtenEtiqueta(1204).' 3','ds_resp_esp_3',$ds_resp_esp_3,false); 
						  ?>
						</div>
                    </div>
                    <div class="col col-xs-12 col-sm-1">
                    </div>
                    <div class="col col-xs-12 col-sm-2">
                          <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
						  <div class="smart-form">
						  <?php
							//Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_3esp',$ds_grade_3,3,10,$ds_grade_3_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_3\'); Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
						    FAMEInputText('','ds_grade_3esp',$ds_grade_3,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_3'); Valida_Quiz();");							 
						  ?>
						  </div>
                    </div>
                    <div class="col col-xs-12 col-sm-3">
						<div class="smart-form">
						  <?php
							//Forma_CampoTexto(ObtenEtiqueta(1204).' 4',False,'ds_resp_esp_4',$ds_resp_esp_4,255,41,$ds_resp_4_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
							FAMEInputText(ObtenEtiqueta(1204).' 4','ds_resp_esp_4',$ds_resp_esp_4,false);  
                          ?>
						</div>
                    </div>
                    <div class="col col-xs-12 col-sm-1">
                    </div>
                    <div class="col col-xs-12 col-sm-2">
                       <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
					   <div class="smart-form">
						  <?php
							//Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_4esp',$ds_grade_4,3,10,$ds_grade_4_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_4\');Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
							FAMEInputText('','ds_grade_4esp',$ds_grade_4,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_4'); Valida_Quiz();");							 
						  
						  ?>
					   </div>
                    </div>
                  </div>
                </div>
                <!------------END OF ANSWERS-------------->
                <!-- END Spanish Content-->
              </div>
              <div class="tab-pane fade in " id="tab-quiz_fra">
                <!-- START French Content-->
                <div class="row">
                  <div class="col col-xs-12 col-sm-5">
				  
					<div class="smart-form">
                    <?php
                      //Forma_CampoTexto(ObtenEtiqueta(1200).' 1',False,'ds_pregunta_fra_1',$ds_pregunta_fra_1,255,60,$ds_pregunta_1_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
					  FAMEInputText(ObtenEtiqueta(1200).' 1','ds_pregunta_fra_1',$ds_pregunta_fra_1,false); 					
					?>
					</div>
                  </div>  
                  <div class="col col-xs-12 col-sm-1">
                  </div> 
                  <?php
                    $var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1282)."' tabindex='100000'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
                  ?>
                  <div class="col col-xs-12 col-sm-2">
                    <div id="div_no_semana3" class="row form-group" <?php echo popover('popover', 'top', $warning, ObtenEtiqueta(1286)); ?>>
                      <label class="col col-sm-12 control-label text-align-left">
                        <strong>
                          <?php echo $var.ObtenEtiqueta(1255); ?>
                        </strong>
                      </label>
                      <div class="col-sm-12"> 
                        <div class="smart-form">  
                          <label class="input" id="error_msj3_1">
                            <input class="form-control" id="valor_1fra" name="valor_1fra" value="<?php echo $ds_quiz_1; ?>" maxlength="3" size="10" type="text" onkeyup="Suma_val_preg_quiz(this.value); Suma_val_resp_quiz(this.value, 'valor_1'); Valida_Quiz(); $('#muestra_valor_1').removeAttr('padding-left').css('padding-left', '20%');" onKeyPress="return SoloNumeros(event);">
                          </label>
                          <?php
                            if(!empty($err_val_quiz_preg) || !empty($err_sum_val_preg_min) || !empty($err_sum_val_preg_max))
                              echo "<span class='help-block txt-color-red'><i class='fa fa-warning'></i>".ObtenMensaje(3)."</span>";
                          ?>
                        </div>
                      </div>  
                    </div>
                  </div>                  
                  <div class="col col-xs-12 col-sm-2">
				    <div class="smart-form">
                    <?php                    
                      //Forma_CampoOculto('q_remaining_1', $q_remaining_1);
                      //Forma_CampoTexto(ObtenEtiqueta(1213),False,'ds_quiz_1',$valor_ini_preg,3,10,$ds_quiz_1_err, False, '', True, 'disabled', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                      echo"<input type='hidden' name='q_remaining_1' id='q_remaining_1' value='".$q_remaining_1."'>";
					  FAMEInputText(ObtenEtiqueta(1213).' 1','ds_quiz_1',$valor_ini_preg,false); 
					?>
				    </div>
					
					<?php
					
					  // if(empty($fg_error)){
                        if($q_remaining_1 != 0){
                          echo "<script>
                            document.getElementById('ds_quiz_1').value = document.getElementById('q_remaining_1').value;
                          </script>";
                        }
                      // }
                    ?>
                  </div> 
                  <div class="col col-xs-12 col-sm-2">
                    <?php
                      echo "<div id='div_ds_quiz_1' class='row form-group '>
                        <label class='col col-sm-12 control-label text-align-left'><strong>&nbsp;&nbsp;  </strong></label>
                        <div class='col col-sm-12'><label class='input'><a href='javascript:MuestraPreview_1(1);' class='btn btn-primary'  >".ObtenEtiqueta(1208)."</a></label></div>      
                      </div>";
                    ?>
                  </div>
                </div>
                <!------------START OF ANSWERS-------------->
                <div class="row"> 
                <?php
                  if($valor_ini_preg == 0)
                    $style = "style='display:none;'";
                  elseif($valor_ini_preg > 0)
                    $style = "style='display:block;'";
                  else
                    $style = "style='display:none;'";
                ?>
                </div>
                <?php
                  if(!empty($err_val_quiz_preg) || !empty($err_sum_val_preg_min) || !empty($err_sum_val_preg_max)){
                ?>
                  <script>
                  $("#error_msj3_1").addClass("state-error");
                  </script>
                <?php
                  }
                  else{
                ?>
                <script>
                  $("#error_msj3_1").removeClass("state-error");
                </script>
                <?php
                  }
                  if(!empty($err_valor_repuestas_err)){
                ?>
                <div>
                  <div class="row">
                    <div class="col-xs-1 col-sm-1">
                    </div>
                    <div class="col-xs-10 col-sm-10">
                      <div class="alert alert-danger fade in">
                        <i class="fa-fw fa fa-times"></i>
                        <strong>
                          <?php echo ObtenEtiqueta(1358); ?>
                        </strong>
                      </div>
                    </div>
                    <div class="col-xs-1 col-sm-1"></div>
                  </div>
                </div>
                <?php
                  }
                  if($fg_tipo_resp_1 == 'T')
                    $style_2 = "style='display: block;'";
                  else
                    $style_2 = "style='display: none;'";
                ?>
                <div class="row hidden" id="error_preguntas_valores">
                  <div class="col col-sm-12 col-md-12 col-lg-12">
                    <i class="fa fa-warning txt-color-red">
                    </i>
                    <code><?php echo ObtenEtiqueta(1895); ?>
                    </code>
                  </div>
                </div>
                <div id="img_based_2_fra" <?php echo $style_2; ?>> 
                  <div class="row">
                    <div class="col col-xs-12 col-sm-3">
					  <div class="smart-form">
                      <?php 
					  //Forma_CampoTexto(ObtenEtiqueta(1204).' 1',False,'ds_resp_fra_1',$ds_resp_fra_1,255,41,$ds_resp_1_err, False, '', True, '', '', "from-group", 'left', 'col col-sm-12', 'col col-sm-12');
						FAMEInputText(ObtenEtiqueta(1204).' 1','ds_resp_fra_1',$ds_resp_fra_1,false); 
                      ?>
                      </div>
					</div>
                    <div class="col col-xs-12 col-sm-1">
                    </div>
                    <div class="col col-xs-12 col-sm-2">

                        <?php 
                        $var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1283)."' tabindex='100000'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
                        
                        ?>
                        <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
						<div class="smart-form">
                      <?php
                        //Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_1fra',$ds_grade_1,3,10,$ds_grade_1_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_1\'); Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');						
                        FAMEInputText('','ds_grade_1fra',$ds_grade_1,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_1'); Valida_Quiz();");							 
						  
						
                      ?>
						</div>
                    </div>
                    <div class="col col-xs-12 col-sm-3">
					    <div class="smart-form">
                      <?php 
					    
						//Forma_CampoTexto(ObtenEtiqueta(1204).' 2',False,'ds_resp_fra_2',$ds_resp_fra_2,255,41,$ds_resp_2_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); 
						FAMEInputText(ObtenEtiqueta(1204).' 2','ds_resp_fra_2',$ds_resp_fra_2,false); 
                      ?>
						</div>
                    </div>
                    <div class="col col-xs-12 col-sm-1">
                    </div>
                    <div class="col col-xs-12 col-sm-2">
                        <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
						<div class="smart-form">
						  <?php 					  
							//Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_2fra',$ds_grade_2,3,10,$ds_grade_2_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_2\'); Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); 
						    FAMEInputText('','ds_grade_2fra',$ds_grade_2,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_2'); Valida_Quiz();");							 						
						  ?>
					    </div>
                    </div>
                  </div>
                  <div class="row">           
                    <div class="col col-xs-12 col-sm-3">
						<div class="smart-form">
                      <?php 
						    //Forma_CampoTexto(ObtenEtiqueta(1204).' 3',False,'ds_resp_fra_3',$ds_resp_fra_3,255,41,$ds_resp_3_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); 
							FAMEInputText(ObtenEtiqueta(1204).' 3','ds_resp_fra_3',$ds_resp_fra_3,false); 
                      ?>
						</div>
                    </div>
                    <div class="col col-xs-12 col-sm-1">
                    </div>
                    <div class="col col-xs-12 col-sm-2">
                        <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
						<div class="smart-form">
						  <?php 
							//Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_3fra',$ds_grade_3,3,10,$ds_grade_3_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_3\'); Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); 
							FAMEInputText('','ds_grade_3fra',$ds_grade_3,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_3'); Valida_Quiz();");							 						
						  ?>
						</div>
                    </div>
                    <div class="col col-xs-12 col-sm-3">
						<div class="smart-form">
                        <?php 
							//Forma_CampoTexto(ObtenEtiqueta(1204).' 4',False,'ds_resp_fra_4',$ds_resp_fra_4,255,41,$ds_resp_4_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); 
							FAMEInputText(ObtenEtiqueta(1204).' 4','ds_resp_fra_4',$ds_resp_fra_4,false);
                        ?>
						</div>
                    </div>
                    <div class="col col-xs-12 col-sm-1">
                    </div>
                    <div class="col col-xs-12 col-sm-2">
                        <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
						<div class="smart-form">
							<?php 
							//Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_4fra',$ds_grade_4,3,10,$ds_grade_4_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_4\');Valida_Quiz();" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); 
						    FAMEInputText('','ds_grade_4fra',$ds_grade_4,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_4'); Valida_Quiz();");							 						
						  
							?>
						</div>
                    </div>
                  </div>
                </div>
                <!------------END OF ANSWERS-------------->
                <!-- END French Content-->
              </div>
            </div>
            <!-- Se paso el </div> al fial, es el penmultimo para cerrar el tab de pregunta general -->
            <!-- END QUIZ Lang Tabs -->
            <!---------Start of IMAGE Answers------------>
            <?php
              if($fg_tipo_resp_1 == 'T')
                $style_3 = "style='display: none;'";
              else
                $style_3 = "style='display: block;'";
            ?>
            <div id="img_based_3" <?php echo $style_3; ?>> 
              <!---------------------------------------------->
              <div class="row">
                <div class="col col-xs-12 col-sm-4">
                  <?php                      
                  FAMECargaImagenDropZone(ObtenEtiqueta(1204).' 1', 'mydropzone_1', '1', $editar, $fl_quiz_pregunta, $fg_error, $ds_resp_1, "L", "Valida_Quiz();", $fg_tipo_resp_1);           
                  ?>
                        

                </div>
                <div class="col col-xs-12 col-sm-2">
                    <?php $var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1283)."'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
                    ?>
                    <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
					<div class="smart-form">
					  <?php
						//Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_img_1',$ds_grade_img_1,3,10,$ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_img_1\'); Valida_Quiz();"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
						FAMEInputText('','ds_grade_img_1',$ds_grade_img_1,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_img_1'); Valida_Quiz();");							 						
					  ?>
				    </div>
                </div>
                <div class="col col-xs-12 col-sm-4">
					
						<?php 
						FAMECargaImagenDropZone(ObtenEtiqueta(1204).' 2', 'mydropzone_2', '1', $editar, $fl_quiz_pregunta, $fg_error, $ds_resp_2, "L", "Valida_Quiz();", $fg_tipo_resp_1); ?>
					
                </div>
                <div class="col col-xs-12 col-sm-2">
                    <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
					<div class="smart-form">
					  <?php
						//Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_img_2',$ds_grade_img_2,3,10,$ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_img_2\'); Valida_Quiz();"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
						FAMEInputText('','ds_grade_img_2',$ds_grade_img_2,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_img_2'); Valida_Quiz();");							 						
					  
					  ?>
				    </div>
                </div>
              </div>
              <div class="row">             
                <div class="col col-xs-12 col-sm-4">
                  <?php
                  FAMECargaImagenDropZone(ObtenEtiqueta(1204).' 3', 'mydropzone_3', '1', $editar, $fl_quiz_pregunta, $fg_error, $ds_resp_3, "L", "Valida_Quiz();", $fg_tipo_resp_1);
                  ?>   
                </div>
                <div class="col col-xs-12 col-sm-2">
                    <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
					<div class="smart-form">
					  <?php
						//Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_img_3',$ds_grade_img_3,3,10,$ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_img_3\'); Valida_Quiz();"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
					    FAMEInputText('','ds_grade_img_3',$ds_grade_img_3,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_img_3'); Valida_Quiz();");							 						
					  
					  ?>
					</div>
                </div>
                <div class="col col-xs-12 col-sm-4">
                  <?php
                  FAMECargaImagenDropZone(ObtenEtiqueta(1204).' 4', 'mydropzone_4', '1', $editar, $fl_quiz_pregunta, $fg_error, $ds_resp_4, "L", "Valida_Quiz();", $fg_tipo_resp_1);
                  ?>   
                </div>
                <div class="col col-xs-12 col-sm-2">
                    <label class="control-label"><strong><?php echo $var.ObtenEtiqueta(1205);?></strong></label>
					<div class="smart-form">
					  <?php
						//Forma_CampoTexto($var.ObtenEtiqueta(1205),False,'ds_grade_img_4',$ds_grade_img_4,3,10,$ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_img_4\'); Valida_Quiz();"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
						FAMEInputText('','ds_grade_img_4',$ds_grade_img_4,false,'onKeyPress=\"return SoloNumeros(event);\"','','',"Suma_val_resp_quiz(this.value, 'ds_grade_img_4'); Valida_Quiz();");							 						
					  
					  ?>
					</div>
                </div>
              </div>
            </div>
            <?php
              if(!empty($err_valor_repuestas_err)){
                if($fg_tipo_resp_1=="I"){
            ?>
            <script>
              $("#div_ds_grade_img_1 > div > label").addClass("state-error");
              $("#div_ds_grade_img_2 > div > label").addClass("state-error");
              $("#div_ds_grade_img_3 > div > label").addClass("state-error");
              $("#div_ds_grade_img_4 > div > label").addClass("state-error");
            </script>		
            <?php
                }
                else{
            ?>
            <script>
                $("#div_ds_grade_1 > div > label").addClass("state-error");
                $("#div_ds_grade_2 > div > label").addClass("state-error");
                $("#div_ds_grade_3 > div > label").addClass("state-error");
                $("#div_ds_grade_4 > div > label").addClass("state-error");
            </script>

            <?php
                }
              }else{
            ?>
            <script>
                $("#div_ds_grade_img_1 > div > label").removeClass("state-error");
                $("#div_ds_grade_img_2 > div > label").removeClass("state-error");
                $("#div_ds_grade_img_3 > div > label").removeClass("state-error");
                $("#div_ds_grade_img_4 > div > label").removeClass("state-error");
                $("#div_ds_grade_1 > div > label").removeClass("state-error");
                $("#div_ds_grade_2 > div > label").removeClass("state-error");
                $("#div_ds_grade_3 > div > label").removeClass("state-error");
                $("#div_ds_grade_4 > div > label").removeClass("state-error");
              </script>		  
			  
			  
            <?php
              }
            ?>
			
            <script>
              function MuestraPreview_<?php echo 1; ?>(tabCounter){
                // Verificamos tipo de pregunta
                var tipo = document.getElementById('fg_tipo_preg_' + tabCounter).value;
                // Verificamos el tipo de imagen
                var fg_tipo_img_prev = document.getElementById('fg_tipo_img_prev_' + tabCounter).value;
                // Control de numeracion
                var tabCounter = tabCounter;
                // Mostramos descripcion de pregunta y valor
                document.getElementById('ds_pregunta_prev_' + tabCounter).innerHTML = document.getElementById('ds_pregunta_' + tabCounter).value;
                document.getElementById('valor_preg_' + tabCounter).innerHTML = document.getElementById('valor_' + tabCounter).value;
                document.getElementById('nb_curso_prev_a').innerHTML = document.getElementById('ds_titulo').value;
                // Validamos el tipo de imagen
                var img_land = document.getElementById('img_land_' + tabCounter);
                var img_port = document.getElementById('img_port_' + tabCounter);
                // Mostramos contenido de acuerdo al tipo de imagen
                if(fg_tipo_img_prev == 'L'){
                  img_land.style.display='block';
                  img_port.style.display='none';
                }
                else{
                  img_land.style.display='none';
                  img_port.style.display='block';
                }
                // Tipo respuesta texto
                if(tipo == 'T'){
                  var resp_txt = document.getElementById('resp_txt_' + tabCounter);
                  resp_txt.style.display='block';
                  for(inc=1; inc<=4; inc++){
                    document.getElementById('Txt_' + inc + '_' + tabCounter).value = document.getElementById('ds_resp_' + inc).value;
                  }
                  document.getElementById('img_port_1').style.display = 'none';
                  document.getElementById('img_land_1').style.display = 'none';
                }
                // Tipo respuesta imagen
                if(tipo == 'I'){
                  for(inc=1; inc<=4; inc++){
                    document.getElementById('Img_' + inc + '_1').src = '../../AD3M2SRC4/modules/fame/uploads/' + document.getElementById('nb_img_prev_mydropzone_' + inc).value;
                    document.getElementById('Img2_' + inc + '_1').src = '../../AD3M2SRC4/modules/fame/uploads/' + document.getElementById('nb_img_prev_mydropzone_' + inc).value;
                  }
                }
                // Abrimos modal
                $("#PreviewQuiz_<?php echo 1; ?>").modal();
              }
            </script>
			
		
            <?php
              echo "
              <div class='modal fade' id='PreviewQuiz_1' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'  data-keyboard='false' data-backdrop='static'>
              <div class='modal-dialog' style='width:80%; align:center;'>
              <div class='modal-content'>
              <div class='modal-header'>
              <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
              <h4 class='modal-title' id='myModalLabel'><i class='fa fa-warning'></i> Lesson: <strong><span id='nb_curso_prev_a'></span></strong> </h4>
              </div>
              <div class='modal-body' style='padding-bottom:0px;'>
              <div class='row'>
              <article class='col-sm-12 col-md-12 col-lg-12'>
              <div class='jarviswidget' id='wid-id-2' data-widget-editbutton='false' data-widget-deletebutton='false' style='margin: 0 0 15px;'>
              <div>
              <div class='jarviswidget-editbox'><!-- This area used as dropdown edit box --></div>
              <div class='widget-body fuelux'>
              <div class='wizard'>
              <ul class='steps'>";
              echo "<li data-target='#preg{$no_orden}' class='active' id='preg_{$no_orden}'>
              <span class='badge badge-info'>{$no_orden}</span>".ObtenEtiqueta(1200)." {$no_orden} (<span id='valor_preg_1'></span> %)<span class='chevron'></span>
              </li>";
              echo "</ul>"; 
              echo "<div class='actions'>";
              echo "<style>
              .hvr-shadow:hover{
              opacity: 0.7;
              filter: alpha(opacity=70); 
              }
              </style>";
              echo "<div style='display:block;' >
              <button type='button' class='btn btn-sm btn-primary' data-dismiss='modal'><i class='fa fa-times-circle'></i>&nbsp;".ObtenEtiqueta(74)."</button>
              </div>";
              echo "</div>
              </div>";  
              echo "<div class='step-content'>";
              echo "<div class='step-pane active' align='center'>";   
              echo " <br/>
              <div id='resultado_$i'>"; 
              echo "<center><h3><strong><span id='ds_pregunta_prev_1'></span></strong></h3></center>
              <div class='form-group' align='center'>";
              # Pregunta tipo Texto
              echo "<div id='resp_txt_1' style='display:none;'>";
              for($inc=1; $inc<=4; $inc++){
                echo "<div class='row'>";
                echo "<div class='col-lg-4'></div>";
                echo "<div class='col-lg-4'>";
                echo "<input type='button' class='btn btn-primary btn-sm btn-block' id='Txt_$inc".'_'."1' />";
                echo "</div>";
                echo "<div class='col-lg-4'></div>";
                echo "</div>";
                echo "<p></p>";
              }
              echo "</div>";
              # Pregunta tipo Imagen
              // Tipo Landscape
              echo "<div id='img_land_1' style='display:none;'>";
                for($inc=1; $inc<=4; $inc++){
                  if($inc==1){
                    echo "<div class='row'>";
                    echo "<div class='col-lg-1'></div><div class='col-lg-10' style='letter-spacing: -5px;'>";
                  }
              echo "<img src='' id='Img_$inc".'_'."1' width='330' height='180' 
                    style=' width: 100%; max-width: 330px; height: 100%; max-height: 180px;' class='hvr-shadow'>  ";
                  if($inc == 4){
                    echo "</div>
                          </div>";
				  }
                  }
              echo "</div>";
              // Tipo Portrait    
              echo "<div id='img_port_1' style='display:none;'>";
                for($inc=1; $inc<=4; $inc++){
                  if($inc==1){
                    echo "<div class='row'>";
                      echo "<div class='col-lg-12' style='letter-spacing: -5px;'>";
                  }
                        echo "<img src='' id='Img2_$inc".'_'."1' 
                        style=' width: 100%; max-width: 180px; height: 100%; max-height: 330px;' class='hvr-shadow'>  ";
                        if($inc == 4)
                          echo "</div></div>";
                }
              echo "</div>";
              echo "</div>";
              echo "</div>";                                
              echo "</div>";
              echo "</div>";
              echo "</div>
                    </div>
                    </div>
                    </article>
                    </div>
                    </div>
                    <div class='modal-footer' ></div>
                    </div>
                    </div>
                    </div>";
            ?>
          </div>
        </div>
      </div>
      <?php
        if(!empty($clave)){
          if(empty($fg_error)){
            $row = RecuperaValor("SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp");
            $no_preguntas = $row[0];
          }else
            $no_preguntas = $no_max_tabs;
        }else{
          $no_preguntas = $no_max_tabs;
        }
		for($i=2;$i<=$no_preguntas;$i++) {
          echo "<div id='tabs-$i'>";
            include('lmedia_ajax.php');
          echo "</div>";
        }
		
      ?>
  <!-- Add NEW QUIZ Tab START Here -->
  <input type='hidden' name='tabs_add' id='tabs_add' value='<?php echo $tot_preguntas;?>' />
  <script type="text/javascript">
    // DO NOT REMOVE : GLOBAL FUNCTIONS!
    $(document).ready(function() {
      /* * Just Tabs  */
      $('#tabs').tabs();
      /* *  Simple tabs adding and removing */
      $('#tabs2').tabs();
      $('#muestra_loading').hide();
      $('#tabs2').show();
	 
      // Dynamic tabs
      var tabTitle = $("#tab_title"), tabContent = $("#tab_content"), 
      tabTemplate = "<li id='cont_tab_quiz_"+ tabContent +"' style='position:relative;'><span class='air air-top-left delete-tab' style='top:7px; left:7px;'><div class='btn btn-xs font-xs btn-default hover-transparent'><i class='fa fa-times'></i></div></span></span><a href='#{href}'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; #{label}</a></li>", tabCounter = 2;
      var tabs = $("#tabs2").tabs();
      // actual addTab function:
      //adds new tab using the input from the form above
      function addTab() {
        <?php if(!empty($clave)){ ?>
          var tabCounter = document.getElementById('ContTabCounter').value;
          document.getElementById('ContTabCounter').value = parseInt(tabCounter) + parseInt(1);
          var tabCounterLimit = document.getElementById('ContTabCounterLimit').value;
          document.getElementById('ContTabCounterLimit').value = parseInt(tabCounterLimit) + parseInt(1);
        <?php }
        else{ ?>            
          var tabCounter = document.getElementById('ContTabCounter').value;
          document.getElementById('ContTabCounter').value = parseInt(tabCounter) + parseInt(1);
          
          var tabCounterLimit = document.getElementById('ContTabCounterLimit').value;
          document.getElementById('ContTabCounterLimit').value = parseInt(tabCounterLimit) + parseInt(1);
        <?php } ?>            
        if(tabCounterLimit > 10){
          document.getElementById('ContTabCounterLimit').value = 11;
          // alert(tabCounter);
          return;
        }
        //alert(tabCounter);#no de tab a agregar
        $('#muestra_loading_quiz').modal('toggle');
        var label = tabTitle.val() || "<span id='muestra_valor_" + tabCounter + "'><?php echo ObtenEtiqueta(1200); ?> " + "<span id='tit_valor_" + tabCounter + "'>" + tabCounterLimit + "</span>" + " <b>(0 %)</b></span>", id = "tabs-" + tabCounter, 
        li = $(tabTemplate.replace(/#\{href\}/g, "#" + id).replace(/#\{label\}/g, label)), 
        tabContentHtml = tabContent.val() || "Tab " + tabCounter + " content.";
        tabs.find(".ui-tabs-nav").append(li);
        var parametros = {
          "tabCounter" : tabCounter
        };
        $.ajax({
          data:  parametros,
          url:   'site/lmedia_ajax.php?nuevo=True',
          type:  'post',
          success:  function (response) {                   
            AjaxContent = response;
            tabs.append("<div id='" + id + "' style='display:none;'><p>" + AjaxContent + "</p></div>");
            tabs.tabs("refresh");
            // Si agrega una pregunta sumara 1 mas a campo de preguntas temorales
            var cam = $("#NoPreguntas_temporal");
            var tot_preguntas = cam.val();
            // cambiamos el valor
            var tot_preguntas1 = parseFloat(tot_preguntas)+1;
            cam.val(tot_preguntas1);
          }
        });
        // Se utiliza cuando es nuevo registro
        document.getElementById('val_inc_tabs').value = tabCounter;
      }
      // addTab button: just opens the dialog
      $("#add_tab").button().click(function() {
        addTab();   
        //Actualizamos el input para saber cuantos tabss fueron agregadoS.y esto nos sirve para el for de delete tabs, y renombrar cada tab.
        var no_tabs= parseInt(document.getElementById('tabs_add').value)+parseInt(1); 
        document.getElementById('tabs_add').value = no_tabs;
      });
      // close icon: removing the tab on click
      $("#tabs2").on("click", 'span.delete-tab', function() {            
      var answer = confirm(<?php echo "'".str_ascii(ObtenMensaje(MSG_ELIMINAR))."'"; ?>);
      if(answer) {
        //Actualizamo la tab oculta con la nueva numeracion para otra vez ir sumando.
        var no_tab_actual= document.getElementById('ContTabCounter').value;
        document.getElementById('ContTabCounter').value =  parseInt(no_tab_actual)- parseInt(1);
    
            //alert('n_tab_actual'+no_tab_actual);
        var cont_tot= parseInt(document.getElementById('ContTabCounter').value);
        var act = document.getElementById('ContTabCounterLimit').value;
        document.getElementById('ContTabCounterLimit').value =  parseInt(act)- parseInt(1);
        var panelId = $(this).closest("li").remove().attr("aria-controls");
        $("#" + panelId).remove();
        tabs.tabs("refresh");
        //var no_tab_elimnar= panelId.substr(5);
        //obtenemos el total de las tabs agrgedas y esto se guarda en input hidde llamando tabs_add
        var tot_tabs_agregados= parseInt(document.getElementById('tabs_add').value);
        var contador=1;
        for(x=1; x <=(tot_tabs_agregados); x++){
          //mator a 1 ya que la tab inicial no cuenta
        if(x>1){ 		 				
            if(!document.getElementById('tit_valor_'+x)){ 
            }else{
                    contador ++;
                var tab_sig=parseInt(contador); 
                    $('#tit_valor_'+x).html(tab_sig);
                $('#NoPreg_'+x).html(tab_sig);
                    var tab_sig=parseInt(contador);
            }
        }  
        }
        $.ajax({
          type: 'POST',
          url : 'site/borra_preg_quiz.php',
          data: 'fl_leccion_sp=<?php echo $clave; ?>'+
            '&tab='+panelId,
          async: false,
          success: function(html) {
          // Si elimina una pregunta restara 1 menos a campo de preguntas temorales
          var cam = $("#NoPreguntas_temporal");
          var tot_preguntas = cam.val();
          // cambiamos el valor
          var tot_preguntas1 = parseFloat(tot_preguntas)-1;                
          cam.val(tot_preguntas1);
          Valida_Quiz();
          }
        });
      }
    });
      // Delete quiz with questions
      $("#delete_quiz").on("click", function(){
        // Confirmacion
        var confirmm = confirm('<?php echo ObtenEtiqueta(1892); ?>');
        var no_preguntas = '<?php echo $no_preguntas; ?>';
        if(confirmm){             
          $.ajax({
              type: 'POST',
              url : 'site/borra_preg_quiz.php',
              data: 'fl_leccion_sp=<?php echo $clave; ?>'+
                    '&quiz_delete=true',
              async: false,
              success: function(html) {
              }
          }).done(function(result){        
            var resultado = JSON.parse(result);
            var error = resultado.valores.error;
            // si no hay error
            if(error==false){
              // Eliminamos todos los tabs
              var tot_preguntas = $("#NoPreguntas_temporal").val();            
              for(var j=1;j<=tot_preguntas;j++){
                if(j>1){
                  $("[aria-controls='tabs-"+j+"']").remove();
                  // $("#tabs-"+j+"").remove();
                }
              }
              // Limpiamos las preguntas
              $("#nb_quiz").val("");
              $("#no_valor_quiz").val("");
              $("#muestra_valor_1").empty().append("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Question 1 <b>(0 %)");
              $("#ds_pregunta_1").val("");
              $("#ds_pregunta_esp_1").val("");
              $("#ds_pregunta_fra_1").val("");
              $("#valor_1").val(0);
              $("#ds_quiz_1").val(100);
              // tipo I
              $("#1_1").remove();
              $("#2_1").remove();
              $("#3_1").remove();
              $("#4_1").remove();
              $("#ds_grade_img_1").val(0);
              $("#ds_grade_img_2").val(0);
              $("#ds_grade_img_3").val(0);
              $("#ds_grade_img_4").val(0);   
              // tipo T
              $("#ds_resp_1").val("");
              $("#ds_resp_2").val("");
              $("#ds_resp_3").val("");
              $("#ds_resp_4").val("");
              $("#ds_resp_esp_1").val("");
              $("#ds_resp_esp_2").val("");
              $("#ds_resp_esp_3").val("");
              $("#ds_resp_esp_4").val("");
              $("#ds_resp_fra_1").val("");
              $("#ds_resp_fra_2").val("");
              $("#ds_resp_fra_3").val("");
              $("#ds_resp_fra_4").val("");
              $("#ds_grade_1").val("");
              $("#ds_grade_2").val("");
              $("#ds_grade_3").val("");
              $("#ds_grade_4").val("");
              $("#NoPreguntas").val(0);                  
              $("#no_max_tabs").val(0);                  
              $("#cont_tab_quiz_1").addClass("active  ui-tabs-active ui-state-active");   
              // habilitamos el boton
              $('footer > div > div > a:first').removeClass('disabled');
              // Quitamos color
              $("#tab_4").removeClass("txt-color-red");
              $("#error_preguntas_valores").addClass('hidden');
              // Reseteamos para iniciar un quiz
              $("#ContTabCounter").val(1);
            }
            else{
              $("#msg_err_tabs").removeClass('hidden');
            }
          });
        }
      });
	  
    });
	
  </script>

 <script>
     //para quie funcione el popover img quiz
     $('[data-toggle="popover"]').popover({
         container: 'body',
         html: true,
         placement: 'auto',
         trigger: 'hover',
         content: function () {
             // get the url for the full size img
             var url = $(this).data('full');
             return '<img src="' + url + '" style="max-width:250px;">'
         }
     });
     
     
 </script>

</div>
