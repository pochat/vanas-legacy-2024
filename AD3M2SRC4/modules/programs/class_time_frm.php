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
  if(!ValidaPermiso(FUNC_CLASS_TIMES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT nb_periodo, ".ConsultaFechaBD('fe_inicio', FMT_CAPTURA)." fe_inicio, fg_activo,fe_inicio,ds_horarios ";
      $Query .= "FROM c_periodo ";
      $Query .= "WHERE fl_periodo=$clave";
      $row = RecuperaValor($Query);
      $nb_periodo = str_texto($row[0]);
      $fe_inicio = $row[1];
      $fg_activo = $row[2];
	  $fe_inicio_=$row[3];
	  $ds_horarios=$row[4];
    }
    else { // Alta, inicializa campos
      $nb_periodo = "";
      $fe_inicio = "";
      $fg_activo = "1";
    }
    $nb_periodo_err = "";
    $fe_inicio_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_periodo = RecibeParametroHTML('nb_periodo');
    $nb_periodo_err = RecibeParametroNumerico('nb_periodo_err');
    $fe_inicio = RecibeParametroFecha('fe_inicio');
    $fe_inicio_err = RecibeParametroNumerico('fe_inicio_err');
    $fg_activo = RecibeParametroNumerico('fg_activo');
	$ds_horarios=RecibeParametroHTML("ds_horarios");
  }

  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CLASS_TIMES);

  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );



  #Recupermos los pertencientes a este periodo.
  # Programas
  $Query  = "SELECT a.fl_programa, nb_programa '".ObtenEtiqueta(360)."', ds_duracion '".ObtenEtiqueta(361)."', ";
  $Query .= "ds_tipo '".ObtenEtiqueta(362)."', no_grado '".ObtenEtiqueta(375)."|right' ";
  $Query .= "FROM c_programa a, k_term b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND b.fl_periodo=$clave AND fg_archive='0' ";
  $Query .= "ORDER BY no_orden, no_grado ";
  $rs = EjecutaQuery($Query);
  $rs1 = EjecutaQuery($Query);

  for($m=1;$roew=RecuperaRegistro($rs1);$m++) {

		$fl_programa=$roew[0];
		$no_term=$roew[4];

		#Verificamos si existe y si no lo inserta.
		$Que="SELECT fl_class_time FROM k_class_time WHERE fl_programa=$fl_programa AND no_term=$no_term AND fl_periodo=$clave ";
		$rot=RecuperaValor($Que);

		if(empty($rot[0])){

			$Query="INSERT INTO k_class_time (fl_periodo,fl_programa,fe_inicio,no_term,ds_dia,no_hora,fe_creacion,fe_ulmod)
					VALUES($clave,$fl_programa,'$fe_inicio_',$no_term,'','',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ";
			$fl_class_time=EjecutaInsert($Query);


		}




  }





  #Generamos l formato de fecha.
  $fe_inicio_=strtotime('+0 day',strtotime($fe_inicio_));
  $fe_inicio_= date('Y-m-d',$fe_inicio_);

  $date = date_create($fe_inicio_);
  $fe_inicio_periodo=date_format($date,'F j , Y');


?>
<div class="row">
	<div class="col-md-12 text-center">
	
	   <h1 class="page-title txt-color-blueDark">CLASS TIMES - <?php echo $nb_periodo;?></h1>
		
	   <br>
		<h6>Combined Class Schedule</h6>
		<br />
        <table border="0" width="100%">
            <thead>
                <tr>
                    <th width="10%"></th>
                    <th width="20%" class="text-center">Day</th>
                    <th width="20%" class="text-center">Time</th>
                    <th width="5%" class="text-center">To</th>
                    <th width="20%" class="text-center">Time</th>
                    <th width="10%"></th>

				</tr>
			</thead>
            <?php
			$dias = array( 'Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
            $num = array(1, 2,3,4,5,6,7);
            $tot = count($dias);

			$Query="SELECT cl_dia,no_hora1,no_tiempo1,no_hora2,no_tiempo2 FROM c_periodo WHERE fl_periodo=$clave  ";
			$row=RecuperaValor($Query);
			$cl_dia_s=$row['cl_dia'];
			$no_hora_1=$row['no_hora1'];
			$no_tiempo_1=$row['no_tiempo1'];
			$no_hora_2=$row['no_hora2'];
			$no_tiempo_2=$row['no_tiempo2'];

			
			$select='<option value="0">&nbsp;&nbsp;&nbsp;Selected&nbsp;&nbsp;&nbsp;</option> ';
            for($m = 0; $m < $tot; $m++) {
                $values=$num[$m];

                $select.='<option value="'.$values.'" ';

                if($cl_dia_s==$num[$m])
                    $select.=" selected ";

                $select.="> $dias[$m]</option> ";
            }

            ?>
			<tbody>
				<tr><td width="10%"></td>
					<td width="20%">
                        <div class="form-group" style="width: 300px;margin: auto;">
                            <select style=" width: 128px;" name="cl_dia_combined" id="cl_dia_combined" class="select2">
                                <?php echo $select;?>
                            </select>
                        </div>
					</td>
                    <td width="20%">
                        <div class="input-group " style="width: 70%; margin: auto;">
                            <input class="form-control picker mike_select" name="timepicker_combined1" id="timepicker_combined1" type="text" value="<?php echo $no_hora_1." ".$no_tiempo_1; ?>" placeholder="Select time" />
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </span>
                        </div>
					</td>
                    <td width="5%"></td>
                    <td width="20%">
                        <div class="input-group " style="width: 70%;margin: auto;">
                            <input class="form-control picker mike_select" name="timepicker_combined2" id="timepicker_combined2" type="text" value="<?php echo $no_hora_2." ".$no_tiempo_2; ?>" placeholder="Select time" />
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </span>
                        </div>

                    </td>
                    <td width="10%"></td>
					
				</tr>
			</tbody>
        </table>
		<br /><br />
	</div>
</div>
<style>
.select2-container .select2-choice .select2-arrow {
width:31px!important;

}
</style>









						
								<div classs="row">
									
									<div class="col-md-12" style="padding-left: 0px;
								padding-right: 0px;">

										<!------------------------------->

											 <div class="content">
								  <section class="" id="widget-grid">
									<div class="row" style="margin-left: 0px; margin-right: 0px;">
									  <div class="col-xs-12 col-sm-12" style="padding: 3px">
										  <!-- Widget ID (each widget will need unique ID)-->
										  <div role="widget" class="jarviswidget" id="wid-id-list" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-collapsed="false">
											  <header role="heading">
												  <div role="menu" class="jarviswidget-ctrls">   
													  <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
												  </div>
												  <span class="widget-icon"> <i class="fa fa-book"></i> </span>
												  <h2>ClassTime </h2>
												  <div role="menu" class="widget-toolbar">
												   
												  </div>
											  </header>

											  <!-- widget div-->
											  <div role="content">
												  <!-- widget edit box -->
												  <div class="jarviswidget-editbox">
													  <!-- This area used as dropdown edit box -->
													  <input class="form-control" type="text">	
														
												  </div>
												  <!-- end widget edit box -->

												  <!-- widget content -->
												  <div class="widget-body no-padding">

													  <table id="example" class="display projects-table table table-striped table-bordered table-hover" cellspacing="0" width="100%">
														  <thead>
															  <tr>
															        <th></th>
																	<th class="text-center">Start Date</th>
																	<th class="text-center">End Date</th>
																	<th class="text-center">Term</th>
																	<th class="text-center">Program Name</th>
																	<th class="text-center">Day</th>
																	<th class="text-center">Time</th>
																	<th></th>
															  
																  
															  </tr>
														  </thead>                          
													  </table>
												  </div>
												  <!-- end widget content -->
											  </div>
											  <!-- end widget div -->
										  </div>
										  <!-- end widget -->
									  </div>
									</div>
								  </section>
								</div>
								
										<!------------------------------->





									</div>
								  

								</div>
					
					




















<!--============DATA TABLE MEMBERS===========------>

<script type="text/javascript">

   function AddSelect2(){
	   
	  
	   $(".mikel_jd").select2();
	   
	   
   }

    $(document).ready(function () {

       
    	$("#cl_dia_combined").select2();

        /* DO NOT REMOVE : GLOBAL FUNCTIONS!
         *
         * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
         *
         * // activate tooltips
         * $("[rel=tooltip]").tooltip();
         *
         * // activate popovers
         * $("[rel=popover]").popover();
         *
         * // activate popovers with hover states
         * $("[rel=popover-hover]").popover({ trigger: "hover" });
         *
         * // activate inline charts
         * runAllCharts();
         *
         * // setup widgets
         * setup_widgets_desktop();
         *
         * // run form elements
         * runAllForms();
         *
         ********************************
         *
         * pageSetUp() is needed whenever you load a page.
         * It initializes and checks for all basic elements of the page
         * and makes rendering easier.
         *
         */

        pageSetUp();

        /*
         * ALL PAGE RELATED SCRIPTS CAN GO BELOW HERE
         * eg alert("my home function");
         * 
         * var pagefunction = function() {
         *   ...
         * }
         * loadScript("js/plugin/_PLUGIN_NAME_.js", pagefunction);
         * 
         * TO LOAD A SCRIPT:
         * var pagefunction = function (){ 
         *  loadScript(".../plugin.js", run_after_loaded);	
         * }
         * 
         * OR
         * 
         * loadScript(".../plugin.js", run_after_loaded);
         */
        /* Formatting function for row details - modify as you need */
        /*MJD 30082016 here
        the content of each register*/
       


		


        // clears the variable if left blank
        var table = $('#example').on('processing.dt', function (e, settings, processing) {
            $('#datatable_fixed_column_processing').css('display', processing ? 'block' : 'none');
            $("#vanas_loader").show();
            if (processing == false)
                $("vanas_loader").hide();
            // alert(processing);
        }).DataTable({
           
		    "ajax": { 
                "url":"class_time_list2.php",
                "type": "POST",
                "dataType": "json",
                "data": function (d) {
                    d.extra_filters = {
                        'clave': <?php echo $clave; ?>

                    };
                }
            },
		 
            //"serverSide": true,
            "processing": true,
            "bDestroy": true,
            "lengthMenu": [[10, 15, 50, -1], [10, 15, 50, "All"]],
            "iDisplayLength": 50,
            "columns": [
               {
                    
                    "orderable": false,
                    "data": null,
                    "defaultContent": '',"bSortable": false
                },
               
                { "data": "start_date","bSortable": false},
                { "data": "end_date","bSortable": false },
                { "data": "term","bSortable": false },
				{ "data": "program_name","bSortable": false },
				{ "data": "day","bSortable": false },
				{ "data": "time" ,"className": "text-align-center","bSortable": false,"width": 200 },
				{ "data": "options","className": "text-align-center" ,"bSortable": false },
               
               
              

               

            ],
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'>>" +
                          "t" +
                          "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "fnDrawCallback": function (oSettings) {
                var tot_registros_val = $("#example_info>span.text-primary").html();
                $("#example_info>span.text-primary  ").html(tot_registros_val + "<input id='tot_registros' value='" + tot_registros_val + "' type='hidden' /> " +
                "<input type='hidden' id='multiple' value='true'>");
				 $("[rel=tooltip]").tooltip();
				//se agrega para que fucnione l timepiker
				 $('.picker').timepicker();
				
				//Se ejecuta esta fucnion solamente asi fucniona.
				 AddSelect2();
				
            }
			
			
			
        });

		
       
	  

        // Add event listener for opening and closing details
        $('#example tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });
		
		
		
    });
	

  function Borra(fl_class_time,fl_class_time_programa){
	        var fg_opc=2;  
	        $.ajax({
                type: 'POST',
                url: 'class_time_add.php',
                data:'fl_class_time='+fl_class_time+
				     '&fl_class_time_programa='+fl_class_time_programa+
				     '&fg_opc='+fg_opc
					 
					
					 
					 
				
            }).done(function (result) {
                var elems = JSON.parse(result);
			    var fg_correct=elems.fg_correct;				
				if(fg_correct==1){					
					 $('#example').DataTable().ajax.reload();
					  
					 //Dialogo save
					   $.smallBox({
						  title : '<h4 ><?php echo ObtenEtiqueta(2265);?>:</h4>',
						  content : ' <p class=\"text-align-right\"><i class=\"fa fa-trash-o\"></i></p>',
						  color : '#953b39',
						  icon : 'fa fa-trash-o',
						  timeout : 4000
						});
					 
					 
					 
					 
					 
				}
				
			});
	  
	  
  } 
  function Add(fl_class_time){
	   var fg_opc=1;
			$.ajax({
                type: 'POST',
                url: 'class_time_add.php',
                data:'fl_class_time='+fl_class_time+
				     '&fg_opc='+fg_opc
            }).done(function (result) {
                var elems = JSON.parse(result);
			    var fg_correct=elems.fg_correct;
				var fl_class_time_programa=elems.fl_class_time_programa;
				
				if(fg_correct==1){
					
					 $('#example').DataTable().ajax.reload(
					 
					 function ( json ) {
						 
							 $('#cl_dia_'+fl_class_time_programa).prop('disabled', false);	
		                     $('#timepicker_'+fl_class_time_programa).prop('disabled', false);	
						}
					 
					 );
					
					 
					 
					
				}
				
				
					
			});  
  }
  
 
  
  
function SaveUpdate(fl_class_time,fl_class_time_programa){
	
	
	
	 var timepicker = document.getElementById('timepicker_'+fl_class_time_programa).value;
	 var cl_dia=document.getElementById('cl_dia_'+fl_class_time_programa).value;
	 var fg_opc=3;
			$.ajax({
                type: 'POST',
                url: 'class_time_add.php',
                data:'fl_class_time='+fl_class_time+
				     '&fl_class_time_programa='+fl_class_time_programa+
					 '&timepicker='+timepicker+
					 '&cl_dia='+cl_dia+
				     '&fg_opc='+fg_opc
            }).done(function (result) {
                var elems = JSON.parse(result);
			    var fg_correct=elems.fg_correct;
				
				if(fg_correct==1){
					
					 $('#cl_dia_'+fl_class_time_programa).prop('disabled', true);	
		             $('#timepicker_'+fl_class_time_programa).prop('disabled', true);	
					 
					 //Dialogo save
					   $.smallBox({
						  title : '<h4 ><?php echo ObtenEtiqueta(1645);?>:</h4>',
						  content : ' <p class=\"text-align-right\"><i class=\"fa fa-save\"></i></p>',
						  color : '#659265',
						  icon : 'fa fa-save',
						  timeout : 4000
						});
					 
				}
				
			});
	 
	 
	 
	 
	 
	 
	 
	 
}

function Edit(fl_class_time,fl_class_time_programa){

          $('#cl_dia_'+fl_class_time_programa).prop('disabled', false);	
		  $('#timepicker_'+fl_class_time_programa).prop('disabled', false);	
	
	
	
	
}

</script>













<?php 
  # Campos de captura
  //Forma_CampoTexto(ObtenEtiqueta(370), True, 'nb_periodo', $nb_periodo, 50, 30, $nb_periodo_err);
  //Forma_Espacio( );
  
  //Forma_CampoTexto(ObtenEtiqueta(371).' '.ETQ_FMT_FECHA, True, 'fe_inicio', $fe_inicio, 10, 10, $fe_inicio_err);
  //Forma_Calendario('fe_inicio');
  //Forma_Espacio( );
  
  //Forma_CampoCheckbox(ObtenEtiqueta(372), 'fg_activo', $fg_activo);
  //Forma_Espacio( );
  
  
 // Forma_MuestraTabla($Query, TB_LN_NNN, 'programas', '', '100%');
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_CLASS_TIMES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>

    
	<script src="../../bootstrap/js/plugin/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>

