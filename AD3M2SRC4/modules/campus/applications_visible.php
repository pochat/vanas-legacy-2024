<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  
  #Eliminamos los filtros check, se resetea todo
  EjecutaQuery("DELETE FROM c_export_cvs WHERE nb_programa='applications.php' ");
  
  
  PresentaHeader();
  PresentaEncabezado(FUNC_APP_FRM);
  
?>
<style>
div.dataTables_filter label {
	float: left !important;
}

</style>


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
                  <span class="widget-icon"> <i class="fa fa-users"></i> </span>
                  <h2>Applications </h2>
                  <div role="menu" class="widget-toolbar">
                    <div class="btn-group">
                        <button aria-expanded="false" class="btn dropdown-toggle btn-xs btn-info" data-toggle="dropdown">
                            <?php echo ObtenEtiqueta(878); ?> <i class="fa fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right">
                          
                        </ul>                    
                    </div>
                  </div>
              </header>

              <!-- widget div-->
              <div role="content">
                  <!-- widget edit box -->
                  <div class="jarviswidget-editbox">
                      <!-- This area used as dropdown edit box -->
                      <input class="form-control" type="text">	
                      <input class="form-control" type="hidden" id="fl_funcion" value="<?php echo FUNC_APP_FRM; ?>">	
                  </div>
                  <!-- end widget edit box -->

                  <!-- widget content -->
                  <div class="widget-body no-padding">
					
                      <table id="example" class="display projects-table table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                          <thead>
                              <tr>
                                  <th></th>
                                  <th><div class="checkbox"><label><input class="checkbox" id="ch_todo" title="All" onchange="javascript:SelTodoLista(),BorraExport();" type="checkbox"><span></span></label></div></th>
                                  <th><?php echo ObtenEtiqueta(18); ?></th>
                                  <th><?php echo ObtenEtiqueta(287); ?></th>
                                  <th><?php echo ObtenEtiqueta(340); ?></th>
                                  <th><?php echo ObtenEtiqueta(360); ?></th>
                                  <th><?php echo ObtenEtiqueta(879); ?></th>
                                  <th><?php echo ObtenEtiqueta(880); ?></th>
                                  <th><?php echo ObtenEtiqueta(343); ?></th>
                                  <th><?php echo ObtenEtiqueta(881); ?></th>
                                  <th><?php echo ObtenEtiqueta(618); ?></th>
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
<div id="redirect">

</div>

<script type="text/javascript">

  $(document).ready(function() {
    
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
    function format ( d ) {
        // `d` is the original data object for the row
        return '<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed" width="100%">'+
		    '<tr>'+
                '<td colspan="2">'+d.payments+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td style="width:110px"><?php echo ObtenEtiqueta(114); ?>:</td>'+
                '<td>'+d.gender+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td style="width:110px"><?php echo ObtenEtiqueta(693); ?>:</td>'+
                '<td>'+d.duplicados+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td><?php echo ObtenEtiqueta(120); ?>:</td>'+
                '<td>'+d.edad+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td>Time Preferences:</td>'+
                '<td>'+d.preferences+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td><?php echo ObtenEtiqueta(61); ?>:</td>'+
                '<td>'+d.information+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td>Recruiter:</td>'+
                '<td>'+d.action+'</td>'+
            '</tr>'+
        '</table>';
    }

    // clears the variable if left blank
      var table = $('#example').on( 'processing.dt', function ( e, settings, processing ) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        // alert(processing);
        } ).DataTable( {
          "ajax": "applications_visible_list.php",
          //"serverSide": true,
		  "colReorder": false,
          "processing": true,
          "bDestroy": true,
		  "stateSave": false,
		  "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]],
		  "order": [[ 1, 'desc' ]],
          "iDisplayLength": 15,
		 // "responsive": true,
          "columns": [
              {
                  "class":          'details-control',
                  "orderable":      false,
                  "data":           null,
                  "defaultContent": ''
              },
              { "data": "checkbox", "orderable": false},
              { "data": "name" },
              { "data": "country" },
              { "data": "start_date" },
              { "data": "program" },
              { "data": "photo-id", "orderable": false},
              { "data": "portafolio" },
              { "data": "metodo" },
              { "data": "contract" },
              { "data": "firt_payment" },
          ],
          "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'>>"+
						"t"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
          "fnDrawCallback": function (oSettings) {
            var tot_registros_val = $("#example_info>span.text-primary").html();
            $("#example_info>span.text-primary  ").html(tot_registros_val+"<input id='tot_registros' value='"+tot_registros_val+"' type='hidden' /> "+
            "<input type='hidden' id='multiple' value='true'>");
          }
      });


       
      // Add event listener for opening and closing details
      $('#example tbody').on('click', 'td.details-control', function () {
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
      
	  
	//Agregamos boton de reset.

    var button = '<a href="javascript:void(0);" id="btn_reset_filter" onclick="ResetFilter();" class="btn btn-default btn-xs" style="margin-left:5px;margin-bottom:5px;margin-top: 5px;"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp; Reset search</a>';
    $('#example_filter').append(button);	
	  
	  
  });
  
   function ResetFilter(){
	   
	    $("#example").DataTable().search("").draw();  //limpiar el restet
		
	    $("#example").DataTable().colReorder("").draw(); 
	   
   }

  
  
  
    function BorraExport() {

       
        var chek = $("#ch_todo").is(":checked");
        if (!chek) {
            var fg_insert = 0;
        } else {
            var fg_insert = 1;
        }
        var nb_programa = "applications.php";
        $.ajax({
            type: 'POST',
            url: 'select.php',
            data: 'fg_insert='+fg_insert+
                  '&nb_programa=' + nb_programa +
                  '&fg_borra=1',
            async: true,
            success: function (html) {

            }
        });

    }

    function Select(fl_alumno,id_check) {

        var chek = $("#ch_" + id_check).is(":checked");
        var nb_programa = "applications.php";
        if (!chek) {
           var  fg_insert = 0;
        } else {
            var fg_insert = 1;
        }

        $.ajax({
            type: 'POST',
            url: 'select.php',
            data: 'fg_insert='+fg_insert+
                  '&nb_programa='+nb_programa+
                  '&fl_registro='+fl_alumno,
            async: true,
            success: function (html) {
               
            }
        });





    }


   

    
  
function export_apply() {
    var criterio = $('div.dataTables_filter input').val();

    var actual = $('#actual').val();
    var nuevo = $('#nuevo').val();
    var url = 'applications_exp.php';

    // Envia datos por forma
    document.export.criterio.value = criterio;
    document.export.actual.value = actual;
    document.export.nuevo.value = nuevo;
    document.export.action = url;
    document.export.submit();
}



</script>




  <form name=export method=post>
    <input type=hidden name=criterio>
    <input type=hidden name=actual>
    <input type=hidden name=nuevo>
  </form>
<?php
  EscribeJS( );
  PresentaFooter();
  /*
  # Consulta para el listado
  $Query  = "SELECT fl_sesion,ds_fname '".ObtenEtiqueta(117)."',ds_lname '".ObtenEtiqueta(118)."',fe_ultmod '".ObtenEtiqueta(340)."', ";
  $Query .= "ds_pais '".ObtenEtiqueta(287)."',nb_programa '".ObtenEtiqueta(512)."',fe_inicio '".ObtenEtiqueta(382)."', ";
  $Query .= "fg_paypal '".ObtenEtiqueta(343)."|center',fg_pago '".ObtenEtiqueta(341)."', ds_cadena 'Contract Status' ,fe_pago '".ObtenEtiqueta(618)."' FROM( ";
  $concat = array(ConsultaFechaBD('a.fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_ultmod', FMT_HORA));
  $Query .= "SELECT fl_sesion, ds_fname , ds_lname ,(".ConcatenaBD($concat).") fe_ultmod, d.ds_pais ds_pais, e.nb_programa nb_programa, ";
  $Query .= "".ConsultaFechaBD('f.fe_inicio',FMT_FECHA)." fe_inicio, ";
  $Query .= "CASE WHEN fg_paypal='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_paypal, ";
  $Query .= "CASE WHEN fg_pago='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_pago, ";
  //columna de primer pago
  $concat2 = array(ConsultaFechaBD('g.fe_pago', FMT_FECHA),"' '", ConsultaFechaBD('g.fe_pago', FMT_HORA));
  $Query .= "IFNULL((SELECT ".ConcatenaBD($concat2)." FROM k_ses_pago g, k_term_pago i WHERE g.cl_sesion=a.cl_sesion AND g.fl_term_pago=i.fl_term_pago AND i.no_pago='1' limit 1), '(To be paid)') as fe_pago, ";
  $Query .= "CASE WHEN (ds_firma_alumno='' OR ds_firma_alumno IS NULL) AND DATE(SUBSTRING(ds_cadena,1,8))+INTERVAL ".ObtenConfiguracion(57)." DAY < CURDATE() THEN 'Expired' WHEN ds_cadena<>'' AND (ds_firma_alumno='' OR ds_firma_alumno IS NULL) THEN 'Sent' ";
  $Query .= "WHEN ds_cadena<>'' AND ds_firma_alumno<>'' THEN 'Signed' ELSE '' END ds_cadena ";
  $Query .= "FROM c_sesion a LEFT JOIN k_app_contrato c ON a.cl_sesion=c.cl_sesion, k_ses_app_frm_1 b, c_pais d, c_programa e, c_periodo f ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion ";
  $Query .= "AND fg_app_1='1' AND fg_app_2='1' AND fg_app_3='1' AND fg_app_4='1' ";
  $Query .= "AND fg_confirmado='1' ";
  // El listado Mostrara a los aplicantes que no tengan activado el flag de archive
  $Query .= "AND fg_inscrito='0' AND a.fg_archive='0' ";
  $Query .= "AND (no_contrato IS NULL OR no_contrato=1) AND b.ds_add_country=d.fl_pais AND b.fl_programa=e.fl_programa AND b.fl_periodo=f.fl_periodo ";
  $Query .= "ORDER BY a.fe_ultmod DESC ) AS APPLICATIONS WHERE 1=1 ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND ds_fname LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_lname LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND ds_pais LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND nb_programa LIKE '%$criterio%'"; break;
      case 5: $Query .= "AND fe_pago LIKE '%$criterio%' OR DATE_FORMAT(fe_pago, '%d-%m-%Y') LIKE '%$criterio%' OR DATE_FORMAT(fe_pago, '%H:%i:%s') lIKE '%$criterio%'"; break;
      default:
        $Query .= "AND (ds_fname LIKE '%$criterio%' OR ds_lname LIKE '%$criterio%' OR ds_pais LIKE '%$criterio%' OR nb_programa LIKE '%$criterio%'  ";
        $Query .= "OR  fe_pago LIKE '%$criterio%' OR DATE_FORMAT(fe_pago, '%d-%m-%Y') LIKE '%$criterio%' OR DATE_FORMAT(fe_pago, '%H:%i:%s') lIKE '%$criterio%') ";
    }
  }
  
  # Muestra pagina de listado
  $array = array(ObtenEtiqueta(117), ObtenEtiqueta(118), ObtenEtiqueta(287),ObtenEtiqueta(512),  ObtenEtiqueta(618));
  PresentaPaginaListado(FUNC_APP_FRM, $Query, TB_LN_IUD, True, True,$array,'','','','payments_frm.php','','',True, True, True );
  */
?>