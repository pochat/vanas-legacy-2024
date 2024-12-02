<?php

# Libreria de funciones
require '../../lib/general.inc.php';

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion();

# Recibe parametros

$clave = RecibeParametroNumerico('clave');
$fg_error = RecibeParametroNumerico('fg_error');
$error = RecibeParametroNumerico('error');

if(isset($_GET['c'])){
    $clave=$_GET['c'];   
}

# Determina si es alta o modificacion
if(!empty($clave))
    $permiso = PERMISO_DETALLE;
else
    $permiso = PERMISO_ALTA;

# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermiso(FUNC_BILLING, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
}

//programa actual
$programa = ObtenProgramaActual();

# Inicializa variables
if (empty($fg_error)) { // Sin error, viene del listado
    
    #Recuperamos datos generales del plan einstituto.
    
    $Query = "SELECT  K.fl_instituto, I.ds_instituto ,P.ds_pais, CASE WHEN K.fg_plan='A' THEN '".ObtenEtiqueta(1521)."' ELSE '".ObtenEtiqueta(1520)."' END ds_plan, K.mn_total_plan,K.no_licencias_usadas,K.no_licencias_disponibles,K.no_total_licencias, K.fe_periodo_inicial,K.fe_periodo_final,CONCAT(U.ds_nombres,' ',U.ds_apaterno)admin,cl_tipo_instituto,I.fl_instituto_rector FROM k_current_plan K JOIN c_instituto I ON I.fl_instituto=K.fl_instituto JOIN c_pais P ON P.fl_pais=I.fl_pais JOIN c_usuario U ON U.fl_usuario=I.fl_usuario_sp WHERE K.fl_instituto=$clave ";
    $row = RecuperaValor($Query);
    $fl_instituto = str_texto($row['fl_instituto']);
    $ds_instituto = $row['ds_instituto'];
    $ds_pais = $row['ds_pais'];
    $tipo_plan = $row['ds_plan'];
    $mn_total_plan = $row['mn_total_plan'];
    $no_total_licencias=$row['no_total_licencias'];
    $no_licencias_usadas = str_texto($row['no_licencias_usadas']);
    $no_licencias_disponibles = str_texto($row['no_licencias_disponibles']);
    $fe_periodo_final_vigencia = str_texto($row['fe_periodo_final']);
    $fe_periodo_inicial_vigencia = str_texto($row['fe_periodo_inicial']);
    $nb_admin=$row['admin'];
	$cl_tipo_instituto=$row['cl_tipo_instituto'];
    $fl_instituto_rector=$row['fl_instituto_rector'];
    
	$Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto_rector ";
    $ro=RecuperaValor($Query);
	$nb_instituto_rector=!empty($ro['ds_instituto'])?$ro['ds_instituto']:NULL;
    
    #Recupermaos el Plan del Instituto
    $Query="SELECT nb_plan FROM c_instituto I JOIN c_plan_fame F ON F.cl_plan_fame =I.cl_plan_fame WHERE I.fl_instituto=$fl_instituto ";
    $row=RecuperaValor($Query);
    $tipo_plan=$row[0]." ".$tipo_plan;
    
} else { // Con error, recibe parametros (viene de la pagina de actualizacion)
   
}

# Presenta forma de captura
PresentaHeader();
echo "
  <script type='text/javascript' src='" . PATH_JS . "/sendtemplate.js.php'></script>";

PresentaEncabezado(FUNC_BILLING);
Forma_CampoOculto('fl_instituto',$clave);
# Funciones para preview de imagenes

# Forma para captura de datos
Forma_Inicia($clave, True);
?>
<br/>
<div class="row">
    <div class="col-md-2">
        &nbsp;
    </div>
    <div class="col-md-8">
        <table class="table" width="100%">
            <tbody>
			   <?php if(($cl_tipo_instituto==2)||(!empty($fl_instituto_rector))){ ?>
				<tr>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"><strong><?php echo ObtenEtiqueta(2524) ?> :</strong></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"><?php  if($fl_instituto_rector){ echo $nb_instituto_rector; } ?></td>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"></td>
                </tr>
			   <?php } ?>
                <tr>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"><strong><?php echo ObtenEtiqueta(933) ?> :</strong></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"><?php echo $ds_instituto ?></td>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"><strong><?php echo ObtenEtiqueta(934)?> :</strong></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"><?php echo $ds_pais ?></td>
                </tr>

                <tr>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"><strong><?php echo ObtenEtiqueta(1744)?> :</strong></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"><?php echo $nb_admin ?></td>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"><strong><?php echo ObtenEtiqueta(1745)?> :</strong></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"><?php echo $tipo_plan ?></td>
                </tr>

                 <tr>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"><strong><?php echo ObtenEtiqueta(988) ?> :</strong></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"><?php echo $no_total_licencias ?></td>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"></td>
                </tr>

                <tr>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"><strong><?php echo ObtenEtiqueta(989) ?> :</strong></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"><?php echo $no_licencias_usadas ?></td>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"></td>
                </tr>

                <tr>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"><strong><?php echo ObtenEtiqueta(990) ?> :</strong></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"><?php echo $no_licencias_disponibles ?></td>
                    <td width="25%" class="text-right" style="border:solid 1px #fff;"></td>
                    <td width="25%" class="text-left" style="border:solid 1px #fff;"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-2">
        &nbsp;
    </div>
</div>
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
                  <h2>Payment History </h2>
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
                                 
                                  <th><?php echo ObtenEtiqueta(1548); ?></th>
                                  <th><?php echo ObtenEtiqueta(1543); ?></th>
                                  <th><?php echo ObtenEtiqueta(1544); ?></th>
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
<input type="hidden" value="<?php echo $clave ?>" id="fl_instituto" />
    <!------------------------------->
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

       


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
        function format(d) {
            // `d` is the original data object for the row 
            return '<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed" width="100%">' +
                '<tr>' + 
                    '<td style="width:110px">'+ d.idp + '</td>' +
                    '<td>' + d.payment + '</td>' +
                    '<td>' + d.descripcion + '</td>' +
                    '<td>' + d.costo + '</td>' +
                '</tr>' +
            '</table>';
        }

        // clears the variable if left blank
        var table = $('#example').on('processing.dt', function (e, settings, processing) {
            $('#datatable_fixed_column_processing').css('display', processing ? 'block' : 'none');
            $("#vanas_loader").show();
            if (processing == false)
                $("vanas_loader").hide();
            // alert(processing);
        }).DataTable({
            "ajax": { 
                "url":"billing_history_list.php",
                "type": "POST",
                "dataType": "json",
                "data": function (d) {
                    //d.extra_filters = {
                    //      'inicia_fe_pago': $("#FuaStartDate").val(),
                    //      'finaliza_fe_pago': $("#FuaEndDate").val()
                    //};
                    d.extra_filters = {
                        'fl_instituto': $("#fl_instituto").val()
                    };
                }
            },
           
            //"serverSide": true,
            "processing": true,
            "bDestroy": true,
            "lengthMenu": [[10, 15, 50, -1], [10, 15, 50, "All"]],
            "iDisplayLength": 15,
            "columns": [
                {
                    "class": 'details-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": ''
                },
               
                { "data": "name" },
                { "data": "duration" },
                { "data": "estatus" },
                { "data": "espacio" },

            ],
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'>>" +
                          "t" +
                          "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "fnDrawCallback": function (oSettings) {
                var tot_registros_val = $("#example_info>span.text-primary").html();
                $("#example_info>span.text-primary  ").html(tot_registros_val + "<input id='tot_registros' value='" + tot_registros_val + "' type='hidden' /> " +
                "<input type='hidden' id='multiple' value='true'>");
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

</script>
 
<?php
  EscribeJS();
?>

<?php

# Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
if ($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_BILLING, PERMISO_MODIFICACION);
else
    $fg_guardar = True;
?>

<?php
Forma_Termina($fg_guardar);

# Pie de Pagina
PresentaFooter();
?>

