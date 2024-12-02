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
  
# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermiso(FUNC_BILLING, PERMISO_EJECUCION)) {
  MuestraPaginaError(ERR_SIN_PERMISO);
  exit;
}

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
PresentaEncabezado(FUNC_BILLING);//fl_funcion
?>
<div class="content">
  <section class="" id="widget-grid">
  <!--<div class="row" style="margin-left: 0px; margin-right: 0px;">
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
  </div>  -->
  <!-- Listado -->
  <div class="row" style="margin-left: 0px; margin-right: 0px;">
      <div class="col-xs-12 col-sm-12" style="padding: 3px">
          <!-- Widget ID (each widget will need unique ID)-->
          <div role="widget" class="jarviswidget" id="widget-grid-1" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-collapsed="false">
              <header role="heading">
                  <div role="menu" class="jarviswidget-ctrls">   
                      <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
                  </div>
                  <!--<span class="widget-icon"> <i class="fa fa-university"></i> </span>-->
                  <!--<h2>School Name </h2>-->
                  <div role="menu" class="widget-toolbar">
                    <!--<div class="btn-group">
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
                        </ul>                    
                    </div>-->
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
                                  
                                  <!--<th>
                                    <div class='checkbox'>
                                       <label><input class='checkbox' type='checkbox' id="ch_todo" onchange="javascript:SelTodoLista();" /><span></span> </label>
                                    </div>
                                  </th>-->
                                  <th><?php echo ObtenEtiqueta(1556); ?></th>
                                  <th><?php echo ObtenEtiqueta(1557); ?></th>
                                  <th><?php echo ObtenEtiqueta(1558); ?></th>
                                
                                  <th><?php echo ObtenEtiqueta(1559);?></th>
                                  <th><?php echo ObtenEtiqueta(1560); ?></th>
                                  <th>Total</th>
                                 
                                  <th><?php echo ObtenEtiqueta(1562); ?></th>
                                  
                                 
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
            url: 'div_dialogo_busqueda_bill.php',
            async: false,
            data: 'inicializa=' + inicializa,
            success: function (html) {
                $('#div_principal').html(html);
            }
        });
    }
    
   // var nuevo = '<?php echo $nuevo; ?>';
    
   // if(nuevo==6)
   //   muestraBusquedaAvanzada(1);
  //  else
 //       muestraBusquedaAvanzada(0);


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
                    '<td>'+
                    '</td>'+
                    '</tr>' +
                    '</table>';
        }
        /*Ordenar por fecha*/
       /* jQuery.extend( jQuery.fn.dataTableExt.oSort, {
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
        */
        // clears the variable if left blank
        var table = $('#example').DataTable({
            "ajax": {
                "url": "billing_list.php",
                "type": "POST",
                "dataType": "json",
                "data": function (d) {

                    d.extra_filters = {'advanced_search': $("#frm_avanzada").serialize()};
                }
            },
          
            "serverSide": false,
            "processing": true,
           
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "iDisplayLength": 25,
            "columns": [
                {
                    "data": null,
                    "class": 'details-control',
                    "orderable": false,
                    "defaultContent": ''
                },
             /*   {
                  "data": '',
                  "orderable": false,
                  //"defaultContent": '<div class="checkbox"><input role="checkbox"  class="cbox"  value="aaaa" type="checkbox" /></div>'
                  "render": function (_name, _type, _data) {
                        //return '<div class="checkbox"><input role="checkbox"  id="ch_'+_data.List.tot_registros+'" value="' + _data.List.fl_usuario + '" class="cbox" type="checkbox" /></div>'
                        return '<div class="checkbox"><label><input class="checkbox" type="checkbox" id="ch_'+_data.List.tot_registros+'" value="' + _data.List.fl_usuario + '" ><span></span> </label></div>'
                    }
                },
               */  {
                   "data": "List.name",
                   "render": function (_name, _type, _data) {
                       return '<span class="editRecord" data-id="' + _data.List.fl_instituto + '">' + _name + '</span>';
                   }
               },
                {"data": "List.country"},
                {"data": "List.admin"},
                 { "data": "List.user", "class": 'text-align-left' },
                  {"data": "List.fg_plan"},
                  {"data": "List.mn_total"},
                {"data": "List.status"},
               
               /* {"data": "List.progress"}
                */
                            
            ],         
            //"order": [[9, 'desc']],
            "fnDrawCallback": function (oSettings) {
                runAllCharts();
                $("span.editRecord").on('click', function () { //                    console.log('entró');
                    // Solo cambio el nombre del archivo
                    /*$('<form>', {"method": "post", "id": "editRecord", "action": "students_frm_NEW.php"}).appendTo('#redirect');*/
                    $('<form>', {"method": "post", "id": "editRecord", "action": "billing_frm.php"}).appendTo('#redirect');
                    $('#editRecord').append($('<input>', {"name": "clave", "type": "hidden", "value": $(this).data('id')}));
                    $('#editRecord').submit();
                }).css('cursor', 'pointer');
                var tot_registros_val = $("#example_info>span.text-primary").html();                
                $("#example_info>span.text-primary").html(tot_registros_val+"<input id='tot_registros' value='"+tot_registros_val+"' type='hidden' /> <input type='hidden' id='multiple' value='true'>");
                $("[rel=tooltip]").tooltip();
            },


            //"sDom": "<'dt-toolbar'<'col-xs-12 col-sm-3'f><'toolbar col-xs-12 col-sm-7'><'#std_exp.col-xs-12 col-sm-1'r><'col-xs-12 col-sm-1'l>>"+
				//		"t"+
				//		"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>"
            //"oTableTools": {
           // "aButtons": [{
               // "sExtends": "csv",
                //"sTitle": "<?php echo ObtenProgramaActual().date('Ymd')."_".rand(1000,9000); ?>",
            //},],
            //"sSwfPath": "<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/swf/copy_csv_xls_pdf.swf"
         // },*/
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
                    $('<form>', {"method": "post", "id": "editRecord", "action": "billing_frm.php"}).appendTo('#redirect');
                    $('#editRecord').append($('<input>', {"name": "clave", "type": "hidden", "value": $(this).data('id')}));
                    $('#editRecord').submit();
                });

                $("button.deleteRecord").on('click', function () {
                    if (confirm('Are you sure you want to DELETE this record?')) {
                        $('<form>', {"method": "post", "id": "deleteRecord", "action": "billing_del.php"}).appendTo('#redirect');
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
    });
   /* 
    // Funcion para el export
    function export_std(){
      var criterio = $('div.dataTables_filter input').val();
      var actual = $('#actual').val();
      var nuevo = $('#nuevo').val();
      var url = 'billing_exp.php';
      // Envia datos por forma
      document.export.criterio.value = criterio;
      document.export.actual.value = actual;
      document.export.nuevo.value = nuevo;
      document.export.action = url;
      document.export.submit();
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
    }*/
</script>

<form name=export method=post>
    <input type=hidden name=criterio>
    <input type=hidden name=actual>
    <input type=hidden name=nuevo>
</form>
<?php
EscribeJS( );
PresentaFooter();
