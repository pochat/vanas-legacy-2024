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
	if(!ValidaPermiso(FUNC_CUPON, $permiso)) {
	MuestraPaginaError(ERR_SIN_PERMISO);
	exit;
	}
  
	# Inicializa variables
	if(!$fg_error) { // Sin error, viene del listado
		if(!empty($clave)) { // Actualizacion, recupera de la base de datos
		  $Query  = "SELECT  nb_cupon, ds_code, ds_descuento, ".ConsultaFechaBD('fe_start', FMT_FECHA) . " fe_start, ";
		  $Query .= ConsultaFechaBD('fe_end', FMT_FECHA) . " fe_end, fg_activo ";
		  $Query .= "FROM c_cupones a WHERE a.fl_cupon=$clave";
		  $row = RecuperaValor($Query);      
		  $nb_cupon = str_texto($row[0]);
		  $ds_code = $row[1];
		  $ds_descuento = $row[2];
		  $fe_start = $row[3];
		  $fe_end= $row[4];  
		  $fg_activo= $row[5];  
		}
		else { // Alta, inicializa campos
		  $nb_cupon = "";
		  $ds_code = "";
		  $ds_descuento = "";
		  $fe_start = "";
		  $fe_end= "";     
		  $fg_activo= "";     
		}    
	}

	# Presenta forma de captura
	PresentaHeader( );
	PresentaEncabezado(FUNC_CUPON);
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
				  <strong><?php echo ObtenEtiqueta(2274); ?></strong>
				</h2>
			</header>
			<!-- widget div-->
			<div class="no-padding">            
			  <!-- widget content -->
			  <div class="widget-body">          
				<!-- Campos --->
				<div  class="row padding-10" >
					<div class="row padding-10">
						<div class="col-sm-4">
						<?php
						Forma_CampoTexto(ObtenEtiqueta(2275), true, 'nb_cupon', $nb_cupon, 100, 0, !empty($nb_cupon_err)?$nb_cupon_err:NULL,False, '', True, " onkeyup='javascript:Form_Valida(true);'", '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6');
						?>
						</div>
						<div class="col-sm-4">
						<?php
						Forma_CampoTexto(ObtenEtiqueta(2277), true, 'ds_code', $ds_code, 100, 0, !empty($ds_code_err)?$ds_code_err:NULL, False, '', True, 'style=\'padding:6px 2px;\' onkeyup=\'javascript:Form_Valida(true);\'', '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6');
						?>
						</div>
						<div class="col-sm-2 text-right no-padding">
							<div class="onoffswitch-container">
								<span class="onoffswitch-title"><strong><?php echo ObtenEtiqueta(2280); ?></strong></span> 
								<span class="onoffswitch">
									<input type="checkbox" class="onoffswitch-checkbox" name="fg_activo" id="fg_activo"
									<?php
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
						<div class="col-sm-4">
						<?php
						Forma_CampoTexto(ObtenEtiqueta(2281),true,'fe_start', !empty($fe_start)?$fe_start:NULL,10,10, !empty($fe_start_err)?$fe_start_err:NULL, False, '', True, 'onChange="Form_Valida(true); $(\'#fe_end\').datepicker(\'option\', \'minDate\',$(this).val());" onkeyup="Form_Valida(true); " onClick="Form_Valida(true);$(\'#ui-datepicker-div\').css(\'z-index\', \'100\');"', '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6');
						Forma_Calendario('fe_start');
						?>
						</div>
						<div class="col-sm-4">
						<?php
						Forma_CampoTexto(ObtenEtiqueta(2282),true,'fe_end',$fe_end,10,10, !empty($fe_end_err)?$fe_end_err:NULL, False, '', True, 'onChange="Form_Valida(true);" onkeyup="Form_Valida(true);" onClick="Form_Valida(true);$(\'#ui-datepicker-div\').css(\'z-index\', \'100\');"', '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6');
						Forma_Calendario('fe_end');
						?>
						</div>
						<div class="col-sm-2">
						<?php
						Forma_CampoTexto(ObtenEtiqueta(2283), true, 'ds_descuento', $ds_descuento, 100, 0, !empty($ds_descuento_err)?$ds_descuento_err:NULL,False, '', True, 'style=\'padding:6px 2px;\' onkeyup=\'Form_Valida(true);\'', '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-10', 'col-xs-12 col-sm-12 col-md-12 col-lg-2 no-padding');
						?>
						</div>
					</div>
					<div class="row padding-10">
						<!--<div class="col-sm-1">&nbsp;</div>-->
						<div class="col-sm-12">
							<div class="row padding-10" id="div_programs">
								<strong><?php echo ObtenEtiqueta(2284); ?>: </strong>
							</div>
							<div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0" data-widget-editbutton="false" role="widget">
								<table id="tbl_programs" class="display table table-striped" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>&nbsp;</th>
											<th><i class="fa fa-fw fa-files-o text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(2285); ?></th>
											<th><i class="fa fa-fw fa-calendar text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(2276); ?></th>
											<th><i class="fa fa-fw fa-trophy text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(2305); ?></th>
										</tr>
									</thead>
								</table>
							</div>
							<?php
							$Query  = "SELECT fl_programa, CONCAT(nb_programa, ' - ', ds_duracion) FROM c_programa a ";
							$Query .= "WHERE fg_archive='0' ";							
							$rs = EjecutaQuery($Query);
							$registros = CuentaRegistros($rs); 
							?>
							<script type='text/javascript'>
								$(document).ready(function () {								
								var start = $("#fe_start").val();
								var end = $("#fe_end").val();
								if(start.length>0)
									$("#fe_end").datepicker("option", "minDate",start);
								
								// clears the variable if left blank
								function DataTble(start, end){
								var table = 
								$('#tbl_programs').DataTable( {
									"ajax": {
									"url":"cupo_list.php",
									"type": "POST",
									"data": {
										"clave": <?php echo $clave; ?>,
										"fe_start": start,
										"fe_end": end
									}
									},							
									"processing": true,
									"bDestroy": true,
									"lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]],
									"iDisplayLength": 30,
									"columns": [
										{ "data": "checkbox", "orderable": false, "class": "text-align-center" },
										{ "data": "nb_program" },
										{ "data": "date" },
										{ "data": "nb_cupon" }							  
									],
									"sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'>>"+
											"t"+
											"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
									"fnDrawCallback": function (oSettings) {
										$("[rel=tooltip]").tooltip();										
									},
									"initComplete": function(settings, json) {
										var tot = '<?php echo $registros; ?>';
										var checkeds = 0;
										for(var i=1;i<=tot;i++){
											var c = $("#ch_"+i).is(":checked");
											if(c==true)
												checkeds++;
										}
										if(checkeds>0){
											$("#error-programs").remove();
										}
									}
								});
								// Add event listener for opening and closing details
								$('#tbl_programs tbody').on('click', 'td.details-control', function () {
									var tr = $(this).closest('tr');
									var row = table.row( tr );							
									if ( row.child.isShown() ) {
										// This row is already open - close it
										row.child.hide();
										tr.removeClass('shown');
									}
									else {
										// Open this row
										row.child( format(row.data()) ).show();
										tr.addClass('shown');
									}
								});								
								}
								DataTble(start, end);
								$("#fe_start").change(function(){
									var fe1 = $(this).val();
									var fe2 = $("#fe_end").val();
									$("#tbl_programs").dataTable().fnDestroy();
									DataTble(fe1,fe2);
								});
								$("#fe_end").change(function(){
									var fe1 = $("#fe_start").val();
									var fe2 = $(this).val();
									$("#tbl_programs").dataTable().fnDestroy();
									DataTble(fe1,fe2);
								});
								$("#tbl_programs_wrapper").addClass("no-padding");
								});
								// Funcion para ir a otro cupon
								function cupon(fl_cupon){
									document.cupon.clave.value = fl_cupon;
									document.cupon.action = 'cupones_frm.php';
									document.cupon.submit();
								}
								
							</script>
						</div>
						<!--<div class="col-sm-1">&nbsp;</div>-->
					</div>
				</div>
			  </div>
			</div>
		</div>  
	</div>
	<script>
	function Form_Valida(campos=false){
		var tot = '<?php echo $registros; ?>';
		var ignore_grl = ".select2-input, .select2-container";
		/** Valida fecha **/
		$.validator.addMethod(
			"mydate",
			function(value, element) {
				// put your own logic here, this is just a (crappy) example
				return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
			},
			"Please enter a date in the format dd-mm-yyyy."
		);
		var $frm1 = $('#datos').validate({
			ignore: ignore_grl,
			// Rules for form validation
			rules : {
				nb_cupon : {required : true},
				ds_code : {required : true},
				fe_start : {required : true, mydate: true},
				fe_end : {required : true, mydate: true},
				ds_descuento: {required: true},
				// fg_activo: {required:true}
			},
			// Messages for form validation
			messages : {
				nb_cupon : {required : "Please enter your Name cupon"},
				ds_code : {required : "Please enter your Cupon code"},
				fe_start : {required : "Please enter your Date Start"},
				fe_end : {required : "Please enter your Date End"},
				ds_descuento : {required : "Please enter your Descount"},
				// fg_activo : {required : "Please enter your status"}
			},
			onsubmit: false,
			highlight: function (element) {
				$(element).closest(".input").removeClass("state-success").addClass("state-error");
				$(element).closest(".checkbox").removeClass("state-success").addClass("state-error");
			},
			//When removing make the same adjustments as when adding
			unhighlight: function (element, errorClass, validClass) {
				$(element).closest('.checkbox').removeClass('state-error').addClass('state-success');
				$(element).closest(".input").removeClass("state-error").addClass("state-success");
			},
			// Do not change code below
			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});
		var form = $("#datos").valid();
		var checkeds = 0;
		for(var i=1;i<=tot;i++){
			var c = $("#ch_"+i).is(":checked");
			if(c==true)
				checkeds++;
		}
		var error = '<div class="alert alert-danger fade in" id="error-programs">'+								
						'<i class="fa-fw fa fa-times"></i>'+
							'<strong>Error!</strong> Please select some a programs'+
					  '</div>';
		$("#error-programs").remove();
		if(checkeds==0){
			$("#div_programs").removeClass("txt-color-green").addClass("txt-color-red").after(error);
		}
		else{
			$("#div_programs").removeClass("txt-color-red").addClass("txt-color-green");
		}		
		if(form==true && checkeds>0 && campos==false){
			document.datos.submit();
		}
		
	}
	
	
	
	</script>
<?php
  
	# Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
	if($permiso == PERMISO_DETALLE)
	$fg_guardar = ValidaPermiso(FUNC_CUPON, PERMISO_MODIFICACION);
	else
	$fg_guardar = True;
	Forma_Termina($fg_guardar, '', ETQ_SALVAR, ETQ_CANCELAR, '','Form_Valida()');
	echo "
	<form name='cupon' method='post' target='_blank'>
		<input type=hidden name=clave>   
	</form>";

	# Pie de Pagina
	PresentaFooter( );
?>