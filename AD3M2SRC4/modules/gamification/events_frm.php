<?php
	# Libreria de funciones
	require '../../lib/general.inc.php';
    require '../../../modules/liveclass/bbb_api.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario = ValidaSesion( );

	# Recibe parametros
	$clave = RecibeParametroNumerico('clave');
	$fg_error = RecibeParametroNumerico('fg_error');

	# Determina si es alta o modificacion
	if(!empty($clave))
	$permiso = PERMISO_DETALLE;
	else
	$permiso = PERMISO_ALTA;

	# Verifica que el usuario tenga permiso de usar esta funcion
	if(!ValidaPermiso(FUNC_EVENTS, $permiso)) {
	MuestraPaginaError(ERR_SIN_PERMISO);
	exit;
	}
  
	# Inicializa variables
	if(!$fg_error) { // Sin error, viene del listado
		if(!empty($clave)) { // Actualizacion, recupera de la base de datos
            $Query  = "SELECT  nb_evento, ds_evento,cl_clave ";
		    $Query .= "FROM c_evento  WHERE cl_evento=$clave ";
		  $row = RecuperaValor($Query);      
		  $nb_evento = str_texto($row[0]);
		  $ds_descripcion = str_texto($row[1]);
          $cl_clave=str_texto($row[2]);
		 
		 
          
		}
		else { // Alta, inicializa campos
            $nb_evento = "";
            $ds_descripcion = "";
           $cl_clave="";
   
		}    
	}

	# Presenta forma de captura
	PresentaHeader( );
	PresentaEncabezado(FUNC_EVENTS);
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
				  <strong>Actions</strong>
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
							Forma_CampoTextoM(ObtenEtiqueta(2179), true, 'cl_clave', $cl_clave, 100, 0, !empty($cl_clave_err)?$cl_clave_err:NULL,False, 'cl_clave_d', True, "onkeyup='javascript:ValidaCampos();'", '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6','Please enter your Id');
						?>

						
						</div>
						<div class="col-sm-4">
                        <?php
							Forma_CampoTextoM(ObtenEtiqueta(2163), true, 'nb_evento', $nb_evento, 100, 0, !empty($nb_evento_err)?$nb_evento_err:NULL,False, 'nb_evento_d', True, "onkeyup='javascript:ValidaCampos();'", '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6','Please enter your name event');
						?>

						</div>
						
						<div class="col-md-4">
						<?php
							Forma_CampoTextoM(ObtenEtiqueta(2164), False, 'ds_descripcion', $ds_descripcion, 100, 10, !empty($ds_descripcion_err)?$ds_descripcion_err:NULL,False, 'ds_descripcion_d', True, "onkeyup='ValidaCampos(true);'", '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6 no-padding','Please enter your points');
						?>
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
        $fg_guardar = ValidaPermiso(FUNC_EVENTS, PERMISO_MODIFICACION);
	else
	$fg_guardar = True;
	Forma_Termina($fg_guardar, '', ETQ_SALVAR, ETQ_CANCELAR, '','');
	echo "
	<form name='cupon' method='post' target='_blank'>
		<input type=hidden name=clave>   
	</form>";

    
    echo"<script> $('#aceptar').addClass('disabled');</script>";
  
?>
      
          <script>
              function ValidaCampos() {

                  var cl_clave = document.getElementById('cl_clave').value;
                  var nb_evento = document.getElementById('nb_evento').value;
                  var ds_descripcion = document.getElementById('ds_descripcion').value;
                
                 
                  if (cl_clave.length > 0) {
                      $('#cl_clave_d').addClass('state-success');
                      $('#err_cl_clave_d').addClass('hidden');
                  } else {
                      $('#cl_clave_d').removeClass('state-success');
                      $('#cl_clave_d').addClass('state-error');

                      $('#err_cl_clave_d').removeClass('hidden');
                  }


                  if (nb_evento.length > 0) {
                      $('#nb_evento_d').addClass('state-success');
                      $('#err_nb_evento_d').addClass('hidden');
                  } else {
                      $('#nb_evento_d').removeClass('state-success');
                      $('#nb_evento_d').addClass('state-error');

                      $('#err_nb_evento_d').removeClass('hidden');
                  }
				
                  if (ds_descripcion.length > 0) {
                      $('#ds_descripcion_d').addClass('state-success');
                      $('#err_ds_descripcion_d').addClass('hidden');
                  } //else {
                     // $('#ds_descripcion_d').removeClass('state-success');
                     // $('#ds_descripcion_d').addClass('state-error');
                     // $('#err_ds_descripcion_d').removeClass('hidden');
                 // }

                 
				   
				   
				   
/*
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


                  if ($('#fg_plan_mensual').is(':checked')) {
                      
                      var check = 1;
                  } else if ($('#fg_plan_anual').is(':checked')) {
                     
                      var check = 1;
                  } else if ($('#fg_pago_unico').is(':checked')) {
                     
                      var check = 1;
                  } else {
                      var check = 0;

                  }

				*/



                  if ((nb_evento.length > 0) && (cl_clave.length > 0)) {

                      $('#aceptar').removeClass('disabled');
                  } else {
                      $('#aceptar').addClass('disabled');

                  }





              }


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