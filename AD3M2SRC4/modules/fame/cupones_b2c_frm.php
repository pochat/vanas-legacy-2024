<?php
	# Libreria de funciones
	require '../../lib/general.inc.php';
    require '../../../modules/liveclass/bbb_api.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario = ValidaSesion( );

	# Recibe parametros
	$clave = RecibeParametroNumerico('clave');
	$fg_error = RecibeParametroNumerico('error');

	# Determina si es alta o modificacion
	if(!empty($clave))
	$permiso = PERMISO_DETALLE;
	else
	$permiso = PERMISO_ALTA;

	# Verifica que el usuario tenga permiso de usar esta funcion
	if(!ValidaPermiso(FUNC_COUPON_B2C, $permiso)) {
	MuestraPaginaError(ERR_SIN_PERMISO);
	exit;
	}
  
	# Inicializa variables
	if(!$fg_error) { // Sin error, viene del listado
		if(!empty($clave)) { // Actualizacion, recupera de la base de datos
		  $Query  = "SELECT  nb_cupon, ds_code, ds_descuento, ".ConsultaFechaBD('fe_start', FMT_FECHA) . " fe_start, ";
		  $Query .= ConsultaFechaBD('fe_end', FMT_FECHA) . " fe_end, fg_activo,fg_plan_mensual,fg_plan_anual,fg_pago_unico ";
		  $Query .= "FROM c_cupones_b2c a WHERE a.fl_cupon=$clave ";
		  $row = RecuperaValor($Query);      
		  $nb_cupon = str_texto($row[0]);
		  $ds_code = $row[1];
		  $ds_descuento = str_texto($row[2]);
		  $fe_start = $row[3];
		  $fe_end= $row[4];  
		  $fg_activo= $row[5];
          $fg_plan_mensual=$row[6];
          $fg_plan_anual=$row[7];
          $fg_pago_unico=$row[8];
          
		}
		else { // Alta, inicializa campos
		  $nb_cupon = "";
		  $ds_code = "";
		  $ds_descuento = "";
		  $fe_start = "";
		  $fe_end= "";     
		  $fg_activo= "";  
          
		}    
	}else{
        
        ########################    error   ############
       
        $Query  = "SELECT  nb_cupon, ds_code, ds_descuento, ".ConsultaFechaBD('fe_start', FMT_FECHA) . " fe_start, ";
        $Query .= ConsultaFechaBD('fe_end', FMT_FECHA) . " fe_end, fg_activo,fg_plan_mensual,fg_plan_anual,fg_pago_unico ";
        $Query .= "FROM c_cupones_b2c a WHERE a.fl_cupon=$clave ";
        $row = RecuperaValor($Query);      
        $nb_cupon = str_texto($row[0]);
        $ds_code = $row[1];
        $ds_descuento = $row[2];
        $fe_start = $row[3];
        $fe_end= $row[4];  
        $fg_activo= $row[5];
        $fg_plan_mensual=$row[6];
        $fg_plan_anual=$row[7];
        $fg_pago_unico=$row[8];
    
    }
    

	# Presenta forma de captura
	PresentaHeader( );
	PresentaEncabezado(FUNC_COUPON_B2C);
	# Inicia forma de captura
	Forma_Inicia($clave, false, true);
?>
	<div id='widget-grid' >
<?php
	if($fg_error)
		Forma_PresentaError( );  
?>
	<div id='widget-grid' >
		<div role="widget" style="" class="jarviswidget" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-custombutton="false" data-widget-sortable="false">
			<header role="heading">
				<div role="menu" class="jarviswidget-ctrls">   
					<a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
				</div>
				<span class="widget-icon"> <i class="fa fa-user"></i> </span>
				<h2>
				  <strong>Cupons B2C</strong>
				</h2>
			</header>
			<!-- widget div-->
			<div class="no-padding">            
			  <!-- widget content -->
			  <div class="widget-body">          
				<!-- Campos --->
				<div  class="row padding-10" >
					<div class="row padding-10">
						<div class="col-sm-3">
						<?php
						Forma_CampoTextoM(ObtenEtiqueta(2151), true, 'nb_cupon', $nb_cupon, 100, 0, !empty($nb_cupon_err)?$nb_cupon_err:NULL,False, 'nb_cupon_d', True, " onkeyup='javascript:ValidaCampos();'", '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6','Please enter your Name cupon');
						?>
                           
						</div>
						<div class="col-sm-3">
						<?php
						Forma_CampoTextoM(ObtenEtiqueta(2153), true, 'ds_code', $ds_code, 100, 0, !empty($ds_code_err)?$ds_code_err:NULL, False, 'ds_code_d', True, "onkeyup='javascript:ValidaCampos();'", '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6','Please enter your Cupon code');
						?>
						</div>
						<div class="col-sm-2 text-right no-padding">
							<div class="onoffswitch-container">
								<span class="onoffswitch-title"><strong><?php echo ObtenEtiqueta(2156); ?></strong></span> 
								<span class="onoffswitch">
									<input type="checkbox" class="onoffswitch-checkbox" name="fg_activo" id="fg_activo"
									<?php
                                    if(empty($clave))
                                        $fg_activo=1;
									if(!empty($fg_activo))
										echo "checked";
									?>
									>
									<label class="onoffswitch-label" for="fg_activo"> 
										<span class="onoffswitch-inner" data-swchon-text="ON" data-swchoff-text="OFF"></span> 
										<span class="onoffswitch-switch"></span>
									</label>
								</span>
							</div>
						</div>
					</div>

                   

					<div class="row padding-10">
						<div class="col-sm-3">
						<?php
						Forma_CampoTextoC(ObtenEtiqueta(2157),true,'fe_start',$fe_start,10,10, !empty($fe_start_err)?$fe_start_err:NULL, False, 'fe_start_d', True, 'onChange="ValidaCampos(true); $(\'#fe_end\').datepicker(\'option\', \'minDate\',$(this).val());" onkeyup="ValidaCampos(true); " onClick="ValidaCampos(true);$(\'#ui-datepicker-div\').css(\'z-index\', \'100\');"', '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6','Please enter your Date Start');
						Forma_Calendario('fe_start');
						?>
						</div>
						<div class="col-sm-3">
						<?php
						Forma_CampoTextoC(ObtenEtiqueta(2158),true,'fe_end',$fe_end,10,10, !empty($fe_end_err)?$fe_end_err:NULL, False, 'fe_end_d', True, 'onChange="ValidaCampos(true);" onkeyup="ValidaCampos(true);" onClick="ValidaCampos(true);$(\'#ui-datepicker-div\').css(\'z-index\', \'100\');"', '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6','Please enter your Date End');
						Forma_Calendario('fe_end');
						?>
						</div>
						<div class="col-sm-4 text-left">
						<?php
						Forma_CampoTextoM(ObtenEtiqueta(2159), true, 'ds_descuento', $ds_descuento, 100, 10, !empty($ds_descuento_err)?$ds_descuento_err:NULL,False, 'ds_descuento_d', True, "onkeyup='ValidaCampos(true);'", '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-2 no-padding','Please enter your Descount');
						?>
						</div>
					</div>

                    <br />
                    <script>
                        
                        // Funcion para ir a otro cupon
                        function cupon(fl_cupon){
                            document.cupon.clave.value = fl_cupon;
                            document.cupon.action = 'cupones_b2c_frm.php';
                            document.cupon.submit();
                        }


                    </script>
                    <div class="row padding-10">
                        <div class="col-md-12">
                            <div class="alert alert-danger fade in hidden" id="MensajeFecha"><i class="fa-fw fa fa-times"></i><strong>Error!</strong> Please select valid date range</div>
                  
                        </div>
                   </div>




                <!----
					<div class="row padding-10">
					<div class="col-sm-4">
                        <?php
                        
                        
    
                                    #Verificamos cuales faltan por seleccionar.
                                   if(empty($clave)){
                                    $QuerysM="SELECT fg_plan_anual,fg_plan_mensual,fg_pago_unico FROM c_cupones_b2c ";
                                   } else{
                                    $QuerysM="SELECT fg_plan_anual,fg_plan_mensual,fg_pago_unico FROM c_cupones_b2c WHERE fl_cupon<>$clave ";   
                                   } 
                                    $r9 = EjecutaQuery($QuerysM);
                                    for($m9=1;$rou9=RecuperaRegistro($r9);$m9++){
        
                                         $fg_plan_anual1=$rou9[0];
                                         $fg_plan_mensual1=$rou9[1];
                                         $fg_pago_unico1=$rou9[2];
                                         
                                         
                                         if($fg_plan_anual1)
                                             $fg_plan_anual_toltip=1;
                                         if($fg_plan_mensual1)
                                             $fg_plan_mensual_toltip=1;
                                         if($fg_pago_unico1)
                                             $fg_pago_unico_toltip=1;
                                         
                                         
                                         
                                         
                                         
                                    }
                        
                        
                        
                        if($fg_plan_mensual_toltip)
                            echo"<div rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(2303)."' > ";    
                        Forma_CampoCheckBox(ObtenEtiqueta(2160),'fg_plan_mensual', $fg_plan_mensual);
                        if($fg_plan_mensual_toltip)
                            echo"</div>";  
                        if($fg_plan_anual_toltip)
                            echo"<div rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(2303)."' > ";    
                        Forma_CampoCheckBox(ObtenEtiqueta(2161),'fg_plan_anual', $fg_plan_anual);
                        if($fg_plan_anual_toltip)    
                            echo"</div>";
                        if($fg_pago_unico_toltip)
                            echo"<div rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(2303)."' > ";
                        Forma_CampoCheckBox(ObtenEtiqueta(2162),'fg_pago_unico', $fg_pago_unico);
                        if($fg_pago_unico_toltip)
                            echo"</div>";
                        
                        ?>
					
					</div>
					<div class="col-sm-4">
					
					
					</div>
					<div class="col-sm-4"></div>
					
					
					</div>
					
                    ---->					

                    <div class="row">
                        <div class="col-md-12" id="muetra_tabla">

                           


                        </div>

                    </div>

                  




					
				</div>
			  </div>
			</div>
		</div>  
	</div>

<?php
  
	# Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
	if($permiso == PERMISO_DETALLE)
	$fg_guardar = ValidaPermiso(FUNC_COUPON_B2C, PERMISO_MODIFICACION);
	else
	$fg_guardar = True;
	Forma_Termina($fg_guardar, '', ETQ_SALVAR, ETQ_CANCELAR, '','');
	echo "
	<form name='cupon' method='post' target='_blank'>
		<input type=hidden name=clave>   
	</form>";

    

    
    
    
   
 
   
   
   
 
    
?>
      
          <script>
              function ValidaCampos() {

                  var nb_cupon = document.getElementById('nb_cupon').value;
                  var ds_code = document.getElementById('ds_code').value;
                  var fe_start = document.getElementById('fe_start').value;
                  var fe_end = document.getElementById('fe_end').value;
                  var ds_descuento = document.getElementById('ds_descuento').value;
                  

                  if (nb_cupon.length > 0) {
                      $('#nb_cupon_d').addClass('state-success');
                      $('#err_nb_cupon_d').addClass('hidden');
                  } else {
                      $('#nb_cupon_d').removeClass('state-success');
                      $('#nb_cupon_d').addClass('state-error');

                      $('#err_nb_cupon_d').removeClass('hidden');
                  }

                  if (ds_code.length > 0) {
                      $('#ds_code_d').addClass('state-success');
                      $('#err_ds_code_d').addClass('hidden');
                  } else {
                      $('#ds_code_d').removeClass('state-success');
                      $('#ds_code_d').addClass('state-error');
                      $('#err_ds_code_d').removeClass('hidden');
                  }


                 
                  if (fe_start.length > 0) {
                      $('#fe_start_d').addClass('state-success');
                      $('#fe_start_d').removeClass('state-error');
                      $('#err_fe_start_d').addClass('hidden');
                  } else {
                      $('#fe_start_d').removeClass('state-success');
                      $('#fe_start_d').addClass('state-error');
                      $('#err_fe_start_d').removeClass('hidden');
                   }

                  if (fe_end.length > 0) {
                      $('#fe_end_d').addClass('state-success');
                      $('#fe_end_d').removeClass('state-error');
                      $('#err_fe_end_d').addClass('hidden');
                  }else {
                      $('#fe_end_d').removeClass('state-success');
                      $('#fe_end_d').addClass('state-error');
                      $('#err_fe_end_d').removeClass('hidden');
                   }

                  if (ds_descuento.length > 0) {
                      $('#ds_descuento_d').addClass('state-success');
                      $('#err_ds_descuento_d').addClass('hidden');
                  } else {
                      $('#ds_descuento_d').removeClass('state-success');
                      $('#ds_descuento_d').addClass('state-error');
                      $('#err_ds_descuento_d').removeClass('hidden');
                  }


               





                  if ((nb_cupon.length > 0) && (ds_code.length) && (fe_start.length) && (fe_end.length > 0) && (ds_descuento.length > 0) ) {

                      $('#aceptar').removeClass('disabled');
                  } else {
                      $('#aceptar').addClass('disabled');

                  }





              }

               $('#fe_start').attr('readonly', true);
               $('#fe_end').attr('readonly', true);
              


          $(document).ready(function () {

              $('#fe_start').change(function () {
                  ShowTable();
              });
              $('#fe_end').change(function () {
                  ShowTable();
              });

              $('#aceptar').addClass('disabled');

          });


      
          
           function ShowTable() {

               var nb_cupon = document.getElementById('nb_cupon').value;
               var fe_start = document.getElementById('fe_start').value;
               var fe_end = document.getElementById('fe_end').value;
               var ds_code = document.getElementById('ds_code').value;
               var clave=<?php echo $clave?>;

               if ((fe_start.length) && (fe_end.length > 0)) {

                   //Ocultamos el mensaje de seleccionar fecha.
                   $('#MensajeFecha').addClass('hidden');


                   $.ajax({
                       type: 'POST',
                       url: 'cupones_lista.php',
                       data: 'fe_start=' + fe_start +
                             '&fe_end=' + fe_end +
                             '&nb_cupon=' + nb_cupon +
                             '&clave='+clave+
                             '&ds_code=' + ds_code, //indica si es trial  o tien plan 
                       processing: true,
                       bDestroy: true,
                       async: true,
                       success: function (html) {
                           $('#muetra_tabla').html(html);
                           ValidaCampos();
                       }
                   });
               } else {
                   //Muestra mensaje de que hace falta selccionar una rango de fecha valido.

                   $('#MensajeFecha').removeClass('hidden');


               }



                


           }
            <?php if(!empty($clave)){ ?> 
                     ShowTable();
                     

              <?php } ?>


   </script>
        
        
          
<?php            
    
	# Pie de Pagina
	PresentaFooter( );
    

    
    function Forma_CampoTextoC($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error='', $p_password=False, $p_id='', $fg_visible=True, $p_script='', $p_texto='', $class_div = "form-group", $prompt_aling='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4',$etq_err='') {
        
        if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
            if(!empty($p_error)) {
                $ds_error = ObtenMensaje($p_error);
                $ds_clase_err = 'has-error';
                $ds_clase = 'form-control';      
            }
            else {
                $ds_clase = 'form-control';
                $ds_error = "";
                $ds_clase_err = '';
            }
            if(!empty($p_id)) {
                if($fg_visible)
                    $ds_visible = "inline";
                else
                    $ds_visible = "none";
            }

            
            echo "
    <div id='div_".$p_nombre."' class='row ".$class_div." ".$ds_clase_err."'>
      <label class='$col_sm_promt control-label text-align-$prompt_aling'>
        <strong>";
            if(!empty($p_id)) echo "<div id='".$p_id."_ppt' style='display:$ds_visible;'>";
            if($p_requerido) echo "* ";
            if(!empty($p_prompt)) echo "$p_prompt:"; else echo "&nbsp;";
            if(!empty($p_id)) echo "</div>";
            echo "
        </strong>
      </label>
      <div class='$col_sm_cam'>
        <label class='input'>";
            if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;'>";
            CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script);
            if(!empty($p_texto)) echo "<span class='css_default'>$p_texto</span>";
            if(!empty($p_id)) echo "</div>";
            if(!empty($p_error)){          
                echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span><input type='hidden' id='err_".$p_nombre."' value='1'>";
            }
            echo "
        </label><em id='err_$p_id' class='hidden' style='font-size:11px;color:#D56161;font-style: normal;' class='invalid'>$etq_err</em>
      </div>      
    </div>";
            
        }
        else
            Forma_CampoOculto($p_nombre, $p_valor);
    }

  
    
?>