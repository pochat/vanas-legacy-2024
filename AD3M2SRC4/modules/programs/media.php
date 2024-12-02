<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MEDIA, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  PresentaHeader();
  PresentaEncabezado(FUNC_MEDIA);

  


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
            <span class="widget-icon"> <i class="fa fa-book"></i> </span>
              <h2>Lessons & Media</h2>
              <div role="menu" class="widget-toolbar">
                <!--<div class="btn-group">
                  <button aria-expanded="false" class="btn dropdown-toggle btn-xs btn-info" data-toggle="dropdown">
                      <?php echo ObtenEtiqueta(878); ?> <i class="fa fa-caret-down"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li>
                      <a href="javascript:export_apply();"><i class="fa  fa-file-excel-o">&nbsp;</i><?php echo ObtenEtiqueta(26); ?></a>
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
                <input class="form-control" type="hidden" id="fl_funcion" value="<?php echo FUNC_MEDIA; ?>">	
            </div>
            <!-- end widget edit box -->

            <!-- widget content -->
            <div class="widget-body no-padding">

                <table id="example" class="display projects-table table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                         
                            <!--<th style="text-align:center;"><div class="checkbox"><label><input class="checkbox" id="ch_todo" title="All" onchange="javascript:SelTodoLista();" type="checkbox"><span></span></label></div></th>-->
                           
                            <th width="7%"><?php echo ObtenEtiqueta(380) ?></th>
							<th width="7%"><?php echo ObtenEtiqueta(380)." ".ObtenEtiqueta(396) ?></th>
                            <th width="22%"><?php echo ObtenEtiqueta(375); ?></th>
                            <th width="7%"><?php echo ObtenEtiqueta(390); ?></th>
                            <th width="7%"><?php echo ObtenEtiqueta(385); ?></th>
							<th width="7%">Video Brief</th>
							<th width="7%"><?php echo ObtenEtiqueta(395); ?></th>
                            <th width="7%"><?php echo ObtenEtiqueta(396); ?></th>
                            <th width="7%"><?php echo ObtenEtiqueta(397); ?></th>
							
                            <th width="7%"><?php echo ObtenEtiqueta(1328); ?></th>
                            <th width="7%"><?php echo ObtenEtiqueta(1329); ?></th>
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
<div id="redirect">

</div>

<div style="width: 228px; right: 0px; display: block; padding:0px 50px 10px 0px;" outline="0" class="ui-widget ui-chatbox">
          <a href="javascript:Envia('media_frm.php', '');" class="btn btn-primary btn-circle btn-xl" title="Add record" style="color:#FFFFFF;"><i class="fa fa-plus"></i></a>
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
            


        return '<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed" width="100%">'+'</table>';



        }
   

      // clears the variable if left blank
      var table = $('#example').on('processing.dt', function (e, settings, processing) {
          $('#datatable_fixed_column_processing').css('display', processing ? 'block' : 'none');
          $("#vanas_loader").show();
          if (processing == false)
              $("vanas_loader").hide();
          // alert(processing);
      }).DataTable({
          "ajax": "media_list.php",
          //"serverSide": true,
          "processing": true,
          "bDestroy": true,
		  "stateSave": true,
          "lengthMenu": [[10, 15, 50, -1], [10, 15, 50, "All"]],
          "iDisplayLength": 15,
          "columns": [
            
              
              { "data": "course" },
              { "data": "duration" },
              { "data": "term" },
              { "data": "semana" },    
              { "data": "titulo" },
              { "data": "video" },
              { "data": "ruta_video" },
              { "data": "duracion" },
              { "data": "fecha" },
			  { "data": "rubric" },
			  { "data": "valor_rubric" },
			  { "data": "delete" },
             
       
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
	  
	  //Agregamos boton de reset.

    var button = '<a href="javascript:void(0);" id="btn_reset_filter" onclick="ResetFilter();" class="btn btn-default btn-xs" style="margin-left:5px;margin-bottom:5px;margin-top: 5px;"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp; Reset search</a>';
    $('#example_filter').append(button);	
	  
	  

    });
	
 function ResetFilter(){
	   
	    $("#example").DataTable().search("").draw();  //limpiar el restet
	   
	   
   }

// Funcion para el export
function export_apply() {
    var criterio = $('div.dataTables_filter input').val();
    var actual = $('#actual').val();
    var nuevo = $('#nuevo').val();
    var url = 'media_exp.php';
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
 
?>