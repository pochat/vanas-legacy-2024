<?php
# La libreria de funciones
require '../../lib/general.inc.php';

# Recibe parametros
//$criterio = RecibeParametroHTML('criterio');
$actual = RecibeParametroNumerico('actual');
$nuevo = RecibeParametroNumerico('nuevo');
$cancelar = RecibeParametroNumerico('cancel');
if ($cancelar)
    $inicializa = 1; //echo "entra";



// start random string
$randString = rand(1000000, 9000000);
$randString = base64_encode($randString); 

//
//if ($actual == 6)   # Si se realiza busqueda avanzada
//    $Query = Query_Completo($nuevo);
//else
//    $Query = Query_Principal($criterio, $actual);
# Muestra pagina de listado
//$campos = array(ETQ_USUARIO, ETQ_NOMBRE, ObtenEtiqueta(360), ObtenEtiqueta(111), ObtenEtiqueta(112));
//Dbg::pd($campos);
PresentaHeader();
?>
<style>
    /**
    * EGMC
    * aling select leng to right
    */
    #example_length:first-child {
        float:right;
    }
</style>
<?php
PresentaEncabezado(FUNC_ALUMNOS);
?>

<style>
	div.dataTables_filter label {
		float: left !important;
    }

   </style>

<div id="data_st_tuition_payment_due"></div>
<div class="content">
  <section class="" id="widget-grid">
  <div class="row" style="margin-left: 0px; margin-right: 0px;">
      <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
          <div role="widget" class="jarviswidget" id="wid-id-advanced-search" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
              <header role="heading">
                  <div role="menu" class="jarviswidget-ctrls">   
                      <a data-original-title="Collapse" href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-minus"></i></a>
                  </div>
                  <span class="widget-icon"> <i class="fa fa-search"></i></span>
                  <h2>
                      <strong>Advanced Search filters</strong>
                  </h2>				
                  <span style="display: none;" class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
              </header>
              <div style="display: block;" role="content">

                  <div class="jarviswidget-editbox">
                  </div>

                  <div class="widget-body">
                      <div id='div_principal' style='padding: 10px;'></div>
                  </div>
              </div>
          </div>
      </div>
  </div>  
  <!-- Listado -->
  <div class="row" style="margin-left: 0px; margin-right: 0px;">
      <div class="col-xs-12 col-sm-12" style="padding: 3px">
          <!-- Widget ID (each widget will need unique ID)-->
          <div role="widget" class="jarviswidget" id="widget-grid-1" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-collapsed="false">
              <header role="heading">
                  <div role="menu" class="jarviswidget-ctrls">   
                      <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
                  </div>
                  <span class="widget-icon"> <i class="fa fa-users"></i> </span>
                  <h2>Students </h2>
                  <div role="menu" class="widget-toolbar">
                    <div class="btn-group">
                        <button aria-expanded="false" class="btn dropdown-toggle btn-xs btn-info" data-toggle="dropdown">
                            <?php echo ObtenEtiqueta(878); ?> <i class="fa fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right">
                          <li>
                              <a href="javascript:selected();"><i class="fa fa-envelope">&nbsp;</i><?php echo ObtenEtiqueta(844); ?></a>                              
                          </li>
                          <li>
                            <a href="javascript:export_std();"><i class="fa  fa-file-excel-o">&nbsp;</i><?php echo ObtenEtiqueta(26); ?></a>
                          </li>
                          <li id="charts_action">
                            <a href="javascript:toggleCharts();"><i class="fa  fa-bar-chart-o">&nbsp;</i>Charts a&uacute; no funciona</a>
                          </li>

                         <li>
                             <a href="javascript:VerPago();"><i class="fa fa-credit-card" aria-hidden="true"></i>&nbsp; Tuition Payment</a>

                         </li>

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
                  </div>
                  <!-- end widget edit box -->

                  <!-- widget content -->
                  <div class="widget-body no-padding">

                      <table id="example" class="display projects-table table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                          <thead>
                              <tr>
                                  <th></th>
                                  <!--<th><input class="cbox" id="ch_todo" title="" onchange="javascript:SelTodoLista();" type="checkbox"></th>-->
                                  <th>
                                    <div class='checkbox'>
                                      <label><input class='checkbox' type='checkbox' id="ch_todo" onchange="javascript:SelTodoLista();" /><span></span> </label>
                                    </div>
                                  </th>
                                  <th><i class="fa fa-fw fa-user text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(510); ?></th>
                                  <th><i class="fa fa-fw fa-globe text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(287); ?></th>
                                  <th><i class="fa fa-fw fa-code text-muted hidden-md hidden-sm hidden-xs"></i> Program / Term</th>
                                  <!--<th>Term</th>-->
                                  <th>Status</th>
                                  <th><i class="fa fa-fw fa-users text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(297); ?>(s)</th>
                                  <th><i class="fa fa-fw fa-percent text-muted hidden-md hidden-sm hidden-xs">%</i> Progress</th>
                                  <!-- oculto por eficiencia -->
                                  <!--<th><i class="fa fa-fw fa-bar-chart text-muted hidden-md hidden-sm hidden-xs"></i> Grades</th>-->
                                  <th><i class="fa fa-fw fa-check-circle-o text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(821); ?></th>
                                  <th><i class="fa fa-fw fa-calendar text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(60); ?></th>
                                  <!--<th><i class="fa fa-check txt-color-green font-xs"></i> Active/ <i class="fa fa-times text-danger font-xs"></i> Inactive</th>-->
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

<script type='text/javascript'>
    function muestraBusquedaAvanzada(inicializa) {

        $('#div_principal').css('display', 'block');
        $.ajax({
            type: 'POST',
            url: 'div_dialogo_busqueda_std.php',
            async: false,
            data: 'inicializa=' + inicializa,
            success: function (html) {
                $('#div_principal').html(html);
            }
        });
    }
    
    var nuevo = '<?php echo $nuevo; ?>';
    
    if(nuevo==6)
      muestraBusquedaAvanzada(1);
    else
      muestraBusquedaAvanzada(0);
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

        setup_widgets_desktop();

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
        function format(_data) {
            _data = _data.List;
            console.log(_data);
            // `d` is the original data object for the row
            return '<table style="width:100%;" cellpadding="9" cellspacing="0" border="0" class="table table-hover table-condensed">' +
                    '<tr>' +
                    '<td style="width:100px">Student name:</td>' +
                    '<td>' + _data.name + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Gender:</td>' +
                    '<td>' + (_data.fg_genero == 'M' ? '<i class="fa fa-male">&nbsp;</i>Male' : '<i class="fa fa-female">&nbsp;</i>Female') + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Group:</td>' +
                    '<td>' + _data.nb_grupo + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Comments:</td>' +
                    '<td>' + _data.ds_notas + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Active:</td>' +
                    '<td>' +
                    "<span class='onoffswitch'><input type='checkbox' name='start_interval' class='onoffswitch-checkbox' onchange='std_active("+_data.fl_usuario+");'  id='st"+_data.fl_usuario+"' " + (_data.fg_activo == '1' ? "checked='checked'" : '') + "><label class='onoffswitch-label' for='st"+_data.fl_usuario+"'><span class='onoffswitch-inner' data-swchon-text='ON' data-swchoff-text='OFF'></span><span class='onoffswitch-switch'></span></label></span>" +
                    '<button class="btn btn-xs btn-danger pull-right deleteRecord" data-id="' + _data.fl_usuario + '" style="margin-left:5px"><i class="fa fa-trash-o">&nbsp;</i>Delete Student</button>' +
                    '<button class="btn btn-xs btn-success pull-right editRecord" data-id="' + _data.fl_usuario + '" style="margin-left:5px"><i class="fa fa-search-plus">&nbsp;</i>View Student File</button>' +
                    '<a class="btn btn-xs btn-warning pull-right editRecord" href="<?php echo PATH_MODULOS; ?>/campus/payments_frm.php?clave='+_data.fl_usuario+'" target="blank"><i class="fa fa-money">&nbsp;</i> <?php echo ObtenEtiqueta(886); ?></a>' +
                    '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Transcript Send:  </td>' +
                    '<td>' +
                    '<a href="https://campus.vanas.ca/transcript_frm.php?c='+_data.fl_usuario+'<?php echo $randString; ?>" target="_blank">https://campus.vanas.ca/transcript_frm.php?c='+_data.fl_usuario+'<?php echo $randString; ?></a>' +
                    '<span class="pull-right" style="margin-right: 5%;"> Password: '+_data.ds_login+' </span>' +
                    '</td>' +
                    '</tr>' +
                    '</table>';
        }
        /*Ordenar por fecha*/
        jQuery.extend( jQuery.fn.dataTableExt.oSort, {
            "date-uk-pre": function ( a ) {
                if (a == null || a == "") {
                    return 0;
                }
                var ukDatea = a.split('-');
                return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
            },
         
            "date-uk-asc": function ( a, b ) {
                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
            },
         
            "date-uk-desc": function ( a, b ) {
                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
            }
        } );
        
        // clears the variable if left blank
        var table = $('#example').DataTable({
            "ajax": {
                "url": "students_list.php",
                "type": "POST",
                "dataType": "json",
                "data": function (d) {
//                                    d.extra_filters = {
//                                        'inicia_fe_pago': $("#FuaStartDate").val(),
//                                        'finaliza_fe_pago': $("#FuaEndDate").val()
//                                    };
                    d.extra_filters = {'advanced_search': $("#frm_avanzada").serialize()};
                }
            },
           // "ajax": "students_list.php",
            "serverSide": false,
            "processing": true,
			 "stateSave": true,
            //            "bDestroy": true,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "iDisplayLength": 25,
            "columns": [
                {
                    "data": null,
                    "class": 'details-control',
                    "orderable": false,
                    "defaultContent": ''
                },
                {
                  "data": '',
                  "orderable": false,
                  //"defaultContent": '<div class="checkbox"><input role="checkbox"  class="cbox"  value="aaaa" type="checkbox" /></div>'
                  "render": function (_name, _type, _data) {
                        //return '<div class="checkbox"><input role="checkbox"  id="ch_'+_data.List.tot_registros+'" value="' + _data.List.fl_usuario + '" class="cbox" type="checkbox" /></div>'
                        return '<div class="checkbox"><label><input class="checkbox" type="checkbox" id="ch_'+_data.List.tot_registros+'" value="' + _data.List.fl_usuario + '" ><span></span> </label></div>'
                    }
                },
                {
                    "data": "List.name",
                    "render": function (_name, _type, _data) {
                        return '<span class="editRecord" data-id="' + _data.List.fl_usuario + '">' + _name + '</span><br><small class="text-muted">' + _data.List.ds_login+'<br>'+_data.List.campus;
                    }
                },
                {"data": "List.country"},
                {"data": "List.program"},
                {"data": "List.status"},
                {
                    "orderable": false,
                    "data": "List.teachers"
                },
                {"data": "List.progress"},
                /**
                 * EGMC 20160609
                 * Oculto por eficiencia
                 {"data": "List.grades"},
                 */
                {
                    "data": "List.no_promedio_t",
                    "render": function (_no_promedio_t, _type, _data) {
                        return _data.List.cl_calificacion + " (" + _no_promedio_t + '%)';

                    }                  
                },
                {
                  "data": "List.fe_alta_label",
                  "type": "date-uk", "targets": 0
                  /*"render": function (_fe_alta, _type, _data) {                      
                     // _fe_alta = new Date(_fe_alta);
                     // return _fe_alta.getDate() + '-' + ('0' + (parseInt(_fe_alta.getMonth())+1)).slice(-2) + '-' + _fe_alta.getFullYear();
                      // return _data.List.fe_alta_label
                  }*/
                }
            ],         
            //"order": [[9, 'desc']],
            "fnDrawCallback": function (oSettings) {
                runAllCharts();
                $("span.editRecord").on('click', function () { //                    console.log('entró');
                    // Solo cambio el nombre del archivo
                    /*$('<form>', {"method": "post", "id": "editRecord", "action": "students_frm_NEW.php"}).appendTo('#redirect');*/
                    $('<form>', {"method": "post", "id": "editRecord", "action": "students_frm.php"}).appendTo('#redirect');
                    $('#editRecord').append($('<input>', {"name": "clave", "type": "hidden", "value": $(this).data('id')}));
                    $('#editRecord').submit();
                }).css('cursor', 'pointer');
                var tot_registros_val = $("#example_info>span.text-primary").html();                
                $("#example_info>span.text-primary").html(tot_registros_val+"<input id='tot_registros' value='"+tot_registros_val+"' type='hidden' /> <input type='hidden' id='multiple' value='true'>");
                $("[rel=tooltip]").tooltip();
            }
            //"sDom": "<'dt-toolbar'<'col-xs-12 col-sm-3'f><'toolbar col-xs-12 col-sm-7'><'#std_exp.col-xs-12 col-sm-1'r><'col-xs-12 col-sm-1'l>>"+
				//		"t"+
					//	"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>"
            /*"oTableTools": {
            "aButtons": [{
                "sExtends": "csv",
                "sTitle": "<?php echo ObtenProgramaActual().date('Ymd')."_".rand(1000,9000); ?>",
            },],
            "sSwfPath": "<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/swf/copy_csv_xls_pdf.swf"
          },*/
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

                $("button.editRecord").on('click', function () {
//                    console.log('entró');
                    //$('<form>', {"method": "post", "id": "editRecord", "action": "students_frm_NEW.php"}).appendTo('#redirect');
                    $('<form>', {"method": "post", "id": "editRecord", "action": "students_frm.php"}).appendTo('#redirect');
                    $('#editRecord').append($('<input>', {"name": "clave", "type": "hidden", "value": $(this).data('id')}));
                    $('#editRecord').submit();
                });

                $("button.deleteRecord").on('click', function () {
                    if (confirm('Are you sure you want to DELETE this record?')) {
                        $('<form>', {"method": "post", "id": "deleteRecord", "action": "students_del.php"}).appendTo('#redirect');
                        $('#deleteRecord').append($('<input>', {"name": "clave", "type": "hidden", "value": $(this).data('id')}));
                        $('#deleteRecord').submit();
                    }
                });

                tr.addClass('shown');
            }
        });

        $("#advancedSearchGo").on('click', function () {
            table.ajax.reload();
            //table.draw();
            return false;
        });
        /*$('#ninguno_filtro').on('click', function () {
            table.ajax.reload();
            //table.draw();
            return true;
        });*/        
        
// Grab the datatables input box and alter how it is bound to events
//if(false)
        /*$(".dataTables_filter input")
                .unbind() // Unbind previous default bindings
                .bind("input", function (e) { // Bind for field changes
                    // Search if enough characters, or search cleared with backspace
                    if (/*this.value.length >= 3 || this.value == "") {
                        // Call the API search function
                        table.search(this.value).draw();
                    }
                })
                .bind("keydown", function (e) { // Bind for enter key press
                    // Search when user presses Enter
                    if (e.keyCode == 13)
                        table.search(this.value).draw();
                });*/
				
				
				
					//Agregamos boton de reset.

    var button = '<a href="javascript:void(0);" id="btn_reset_filter" onclick="ResetFilter();" class="btn btn-default btn-xs" style="margin-left:5px;margin-bottom:5px;margin-top: 5px;"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp; Reset search</a>';
    $('#example_filter').append(button);	
	 
    });
    
	
	
	  
    function ResetFilter(){
	   
	    $("#example").DataTable().search("").draw();  //limpiar el restet
	   
	   
    }
	
	
	
    // Funcion para el export
    function export_std(){
      var criterio = $('div.dataTables_filter input').val();
      /*var actual = $('#actual').val();
      var nuevo = $('#nuevo').val();
      var url = 'students_exp.php';
      // Envia datos por forma
      document.export.criterio.value = criterio;
      document.export.actual.value = actual;
      document.export.nuevo.value = nuevo;
      document.export.action = url;
      document.export.submit();*/
      $.ajax({
        "url": "students_exp.php",
        "type": "POST",
        "data": 'advanced_search' + $("#frm_avanzada").serialize()+'&criterio='+criterio,
        "success":function(response){
          window.open(response, "_blank");
        }
      });
    }
   
    // Activar o desactivar al alumno desde listado
    function std_active(std_clave){
      var check_clave =  $("#st"+std_clave).prop("checked");      
      if(check_clave)
        check_clave = 1;
      else
        check_clave = 0;
      $.ajax({
        type: 'POST',
        url : 'std_active.php',
        data: "clave="+std_clave+"&fg_activo="+check_clave, 
        async: false,
        success: function(data) {
          //alert(data);
        }
      });
    }
	

    function VerPago(){
    
        
        $.ajax({        
            type: 'POST',
            url : 'st_tuition_payment_due.php',
            data: "", 
            async: true,
            success: function (html) {
                $('#data_st_tuition_payment_due').html(html);

               // $(document).ready(function () {
                    $('#tuition').DataTable({
                        'order': [[4, 'asc']]
                    });
              //  });
            } 
            
        });
        

    
    
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
//PresentaPaginaListado(FUNC_ALUMNOS, $Query, TB_LN_NUD, True, True, $campos, '../reports/students_rpt.php', $html_arriba, $html_abajo, 'payments_frm.php','','',True, True);

function Query_Principal($p_criterio, $p_actual) {
    # Consulta para el listado
    $Query = "SELECT fl_usuario, ds_login '" . ETQ_USUARIO . "', ";
    //$concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
    //jgfl 18/11/14 cambio del ds_amaterno primero que el ds_apaterno
    $concat = array('ds_nombres', "' '", NulosBD('ds_amaterno'), "' '", 'ds_apaterno');
    $Query .= ConcatenaBD($concat) . " '" . ETQ_NOMBRE . "', ";
    $Query .= "CONCAT(nb_programa,' (',ds_duracion,')') '" . ObtenEtiqueta(360) . "', ";
    $Query .= ConsultaFechaBD('fe_alta', FMT_FECHA) . " '" . ObtenEtiqueta(111) . "', ";
    $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
    $Query .= "(" . ConcatenaBD($concat) . ") '" . ObtenEtiqueta(112) . "', ";
    $Query .= "CASE WHEN fg_activo='1' THEN '" . ObtenEtiqueta(NO_ETQ_SI) . "' ";
    $Query .= "ELSE '" . ObtenEtiqueta(NO_ETQ_NO) . "' END '" . ObtenEtiqueta(113) . "|center', ";
    $Query .= "CASE WHEN fg_pago='1' THEN '" . ObtenEtiqueta(NO_ETQ_SI) . "' ";
    $Query .= "ELSE '" . ObtenEtiqueta(NO_ETQ_NO) . "' END '" . ObtenEtiqueta(341) . "|center' ";
    $Query .= "FROM c_usuario a, c_perfil b, c_sesion c, k_ses_app_frm_1 d, c_programa e ";
    $Query .= "WHERE a.fl_perfil=b.fl_perfil ";
    $Query .= "AND a.cl_sesion=c.cl_sesion ";
    $Query .= "AND c.cl_sesion=d.cl_sesion ";
    $Query .= "AND d.fl_programa=e.fl_programa ";
    $Query .= "AND a.fl_perfil=" . PFL_ESTUDIANTE . " ";
    if (!empty($p_criterio)) {
        switch ($p_actual) {
            case 1: $Query .= "AND ds_login LIKE '%$p_criterio%' ";
                break;
            case 2: $Query .= "AND (ds_nombres LIKE '%$p_criterio%' OR ds_apaterno LIKE '%$p_criterio%' OR ds_amaterno LIKE '%$p_criterio%') ";
                break;
            case 3: $Query .= "AND nb_programa LIKE '%$p_criterio%' ";
                break;
            case 4: $Query .= "AND " . ConsultaFechaBD('fe_alta', FMT_FECHA) . " LIKE '%$p_criterio%' ";
                break;
            case 5:
                $Query .= "AND (" . ConsultaFechaBD('fe_ultacc', FMT_FECHA) . " LIKE '%$p_criterio%' ";
                $Query .= "OR " . ConsultaFechaBD('fe_ultacc', FMT_HORA) . " LIKE '%$p_criterio%') ";
                break;
            default:
                $Query .= "AND ( ";
                $Query .= "ds_login LIKE '%$p_criterio%' ";
                $Query .= "OR ds_nombres LIKE '%$p_criterio%' OR ds_apaterno LIKE '%$p_criterio%' OR ds_amaterno LIKE '%$p_criterio%' ";
                $Query .= "OR nb_programa LIKE '%$p_criterio%' ";
                $Query .= "OR " . ConsultaFechaBD('fe_alta', FMT_FECHA) . " LIKE '%$p_criterio%' ";
                $Query .= "OR " . ConsultaFechaBD('fe_ultacc', FMT_FECHA) . " LIKE '%$p_criterio%' ";
                $Query .= "OR " . ConsultaFechaBD('fe_ultacc', FMT_HORA) . " LIKE '%$p_criterio%') ";
        }
    }
    $Query .= "ORDER BY fe_alta DESC";
    return $Query;
}

function Query_Completo($p_criterio = '') {
    $Query = "SELECT fl_usuario, ds_login '" . ETQ_USUARIO . "', ";
    //$concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
    //jgfl 18/11/14 cambio del ds_amaterno primero que el ds_apaterno
    $concat = array('ds_nombres', "' '", NulosBD('ds_amaterno'), "' '", 'ds_apaterno');
    $Query .= ConcatenaBD($concat) . " '" . ETQ_NOMBRE . "', ";
    $Query .= "nb_programa '" . ObtenEtiqueta(360) . "', ";
    $Query .= "fe_alta '" . ObtenEtiqueta(111) . "', ";
    $Query .= "fe_ultacc '" . ObtenEtiqueta(112) . "', ";
    $Query .= "CASE WHEN fg_activo='1' THEN '" . ObtenEtiqueta(NO_ETQ_SI) . "' ";
    $Query .= "ELSE '" . ObtenEtiqueta(NO_ETQ_NO) . "' END '" . ObtenEtiqueta(113) . "|center', ";
    $Query .= "CASE WHEN fg_pago='1' THEN '" . ObtenEtiqueta(NO_ETQ_SI) . "' ";
    $Query .= "ELSE '" . ObtenEtiqueta(NO_ETQ_NO) . "' END '" . ObtenEtiqueta(341) . "|center' ";
    $Query .= "FROM (";
    $Query .= "
    SELECT a.fl_usuario fl_usuario, a.cl_sesion, a.ds_login ds_login, a.ds_nombres ds_nombres, a.ds_apaterno ds_apaterno, 
    a.ds_amaterno ds_amaterno, a.fg_genero fg_genero, 
    fe_nacimiento,
    a.ds_email, 
    fg_activo, 
    fe_alta, 
    fe_ultacc, 
    (SELECT fe_ultmod FROM c_sesion se WHERE se.cl_sesion=a.cl_sesion) fe_ultmod,
    (SELECT CONCAT(nb_zona_horaria, ' ', 'GMT', ' (', no_gmt, ')') FROM c_zona_horaria zo WHERE zo.fl_zona_horaria=c.fl_zona_horaria) nb_zona_horaria,
    (SELECT fg_international FROM k_app_contrato app WHERE app.cl_sesion=a.cl_sesion ORDER BY no_contrato LIMIT 1) fg_international,
    (SELECT nb_periodo FROM c_periodo w WHERE w.fl_periodo=f.fl_periodo) nb_periodo,
    (SELECT fe_inicio FROM k_term te, c_periodo i, k_alumno_term al WHERE te.fl_periodo=i.fl_periodo AND te.fl_term=al.fl_term AND al.fl_alumno=a.fl_usuario AND no_grado=1 LIMIT 1) fe_start_date,
    (SELECT nb_programa FROM c_programa e WHERE e.fl_programa=f.fl_programa) nb_programa,
    (SELECT CONCAT(h.ds_nombres, ' ', h.ds_apaterno) FROM k_alumno_grupo k, c_grupo gpo, c_usuario h WHERE k.fl_grupo=gpo.fl_grupo AND gpo.fl_maestro=h.fl_usuario AND k.fl_alumno=a.fl_usuario) ds_profesor,
    (SELECT nb_grupo FROM c_grupo n, k_alumno_grupo o WHERE n.fl_grupo=o.fl_grupo AND o.fl_alumno=a.fl_usuario) nb_grupo,
    (SELECT no_grado FROM k_term b, k_alumno_grupo d, c_grupo m WHERE b.fl_term=m.fl_term AND d.fl_grupo=m.fl_grupo AND d.fl_alumno=a.fl_usuario) no_grado, 
    fe_carta, fe_contrato, fe_fin, 
	  fe_completado, fe_emision, 
		fg_certificado, 
		fg_honores, 
		fe_graduacion, 
		fg_desercion,
    fg_dismissed,
    fg_job,
    fg_graduacion, 
    ds_add_city, ds_add_state, 
    (SELECT fg_pago FROM c_sesion ses WHERE a.cl_sesion=ses.cl_sesion) fg_pago, 
    (SELECT p.ds_pais FROM c_pais p WHERE p.fl_pais=f.ds_add_country) ds_pais,
    YEAR(fe_nacimiento) ye_fe_nacimiento, YEAR(fe_alta) ye_fe_alta, YEAR(fe_ultacc) ye_fe_ultacc, YEAR(fe_ultmod) ye_fe_ultmod, 
    YEAR(fe_carta) ye_fe_carta, YEAR(fe_contrato) ye_fe_contrato, YEAR(fe_fin) ye_fe_fin, YEAR(fe_completado) ye_fe_completado, 
    YEAR(fe_emision) ye_fe_emision, YEAR(fe_graduacion) ye_fe_graduacion, 
    YEAR((SELECT fe_inicio FROM k_term te, c_periodo i, k_alumno_term al WHERE te.fl_periodo=i.fl_periodo AND te.fl_term=al.fl_term AND al.fl_alumno=a.fl_usuario AND no_grado=1 LIMIT 1)) ye_fe_start_date
    FROM (c_usuario a, c_alumno c, k_ses_app_frm_1 f) LEFT JOIN k_pctia j ON (a.fl_usuario=j.fl_alumno)
    WHERE a.fl_usuario=c.fl_alumno AND a.cl_sesion=f.cl_sesion AND fl_perfil=" . PFL_ESTUDIANTE . " ";
    $Query .= ") AS principal ";
    $Query .= "WHERE fl_usuario NOT LIKE '0' ";
    $nuevo = $p_criterio;
    require 'filtros.inc.php';
    $Query .= "ORDER BY fe_alta DESC";

//    print_r($Query);    

    return $Query;
}
*/