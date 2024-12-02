<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_STUDENTS_FAME, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  PresentaHeader();
  PresentaEncabezado(FUNC_STUDENTS_FAME);
  
?>

<script src="https://use.fontawesome.com/f3d2d94b47.js"></script>

<div class="content">
  <section class="" id="widget-grid">
    <div class="row" style="margin-left: 0px; margin-right: 0px;">    
      <div class="row" style="margin-left: 0px; margin-right: 0px;">
      <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
          <div role="widget" class="jarviswidget" id="wid-id-advanced-search" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
              <header role="heading">
                  <div role="menu" class="jarviswidget-ctrls">   
                      <a data-original-title="Collapse" href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-minus"></i></a>
                  </div>
                  <span class="widget-icon"> <i class="fa fa-search"></i></span>
                  <h2>
                      <strong><?php echo ObtenEtiqueta(2060); ?></strong>
                  </h2>				
                  <span style="display: none;" class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
              </header>
              <div style="display: block;" role="content">

                  <div class="jarviswidget-editbox">
                  </div>

                  <div class="widget-bodyr row padding-10">
                    <div id="frm_div_fame_search"></div>
                    <div class="col-sm-12 col-md-12 col-lg-12 padding-top-10 text-align-center">
                      <a class="btn btn-primary" id="btn_Search"><i class="fa fa-search"></i> <?php echo ObtenEtiqueta(2063); ?> </a>
                      <a class="btn btn-danger" id="btn_clear"><i class="fa fa-times"></i> <?php echo ObtenEtiqueta(2064); ?> </a>                      
                    </div>
                  </div>                  
              </div>
          </div>
      </div>
  </div> 
      <div class="col-xs-12 col-sm-12" style="padding: 3px">
          <!-- Widget ID (each widget will need unique ID)-->
          <div role="widget" class="jarviswidget" id="wid-id-list" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-collapsed="false">
              <header role="heading">
                  <div role="menu" class="jarviswidget-ctrls">   
                      <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
                  </div>
                  <span class="widget-icon"> <i class="fa fa-book"></i> </span>
                  <h2>&nbsp;Students </h2>
                  <div role="menu" class="widget-toolbar">
                    <div class="btn-group">
                        <button aria-expanded="false" class="btn dropdown-toggle btn-xs btn-info" data-toggle="dropdown">
                            <?php echo ObtenEtiqueta(878); ?> <i class="fa fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right">
                          
                         
                          <li>

                             

                            <a href="javascript:export_apply();"><i class="fa  fa-file-excel-o">&nbsp;</i><?php echo ObtenEtiqueta(26); ?></a>
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
                      <input class="form-control" type="hidden" id="fl_funcion" value="<?php echo FUNC_APP_FRM; ?>">	                      
                  </div>
                  <!-- end widget edit box -->                  
                  <!-- widget content -->
                  <div class="widget-body no-padding">
                      <table id="example" class="display projects-table table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                          <thead>
                              <tr>
                                 <th></th>
                                  <th></th>
                                  <th style="text-align:center;"><!--<div class="checkbox"><label><input class="checkbox" id="ch_todo" title="All" onchange="javascript:SelTodoLista();" type="checkbox"><span></span></label></div>--></th>
                                  <th><?php echo ObtenEtiqueta(1563); ?></th>
                                  <th><?php echo ObtenEtiqueta(1564); ?></th>
                                  <th><?php echo ObtenEtiqueta(1565); ?></th>
                                   <th><?php echo ObtenEtiqueta(1562); ?></th>
                                 
                                  <th>Last Login</th>
								
                                  <th>Progrress</th>
								  <th><?php echo ObtenEtiqueta(1908); ?></th>
                                  <th>GPA</th>
                                 
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
        <!--  <a href="javascript:Envia('certificates_frm.php', '');" class="btn btn-primary btn-circle btn-xl" title="Add record" style="color:#FFFFFF;"><i class="fa fa-plus"></i></a>
        --></div>


<script type="text/javascript">

    $(document).ready(function () {
      
      function busqueda(instituto,programa){
        $('#frm_div_fame_search').css('display', 'block');
        $.ajax({
            type: 'POST',
            url: 'div_dialogo_busqueda_std.php',
            async: false,
            data: 'instituto=' + instituto + '&programa='+ programa,
            success: function (html) {
                $('#frm_div_fame_search').html(html);
            }
        });
        
      }
      busqueda(6,0);

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
            


        return '<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed" width="100%">'+
            '<tr>'+
                '<td style="width:110px"> </td>'+
                '<td> </td>'+
            '</tr>'+
        '</table>';



        }
   

      // clears the variable if left blank
      var table = $('#example').on('processing.dt', function (e, settings, processing) {
          $('#datatable_fixed_column_processing').css('display', processing ? 'block' : 'none');
          $("#vanas_loader").show();
          if (processing == false)
              $("vanas_loader").hide();
          // alert(processing);
      })
      .DataTable({                    
          "ajax": {
                "url": "students_list.php",
                "type": "POST",
                "data": 
                function (d) {                    
                    d.extra_filters = {'advanced_search': $("#frm_search_fame").serialize()};
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
              { "data": "checkbox", "orderable": false },
              { "data": "avatar" ,"orderable": false },
              { "data": "name" },
              { "data": "name_school" },
              { "data": "course" },
              { "data": "age" },

              { "data": "ide" },
              { "data": "progress", "sClass": "center" },
              { "data": "assesment", "sClass": "center" },
              { "data": "estatus" },
             
             
       
          ],
          "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'>>" +
						"t" +
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
          "fnDrawCallback": function (oSettings) {
              var tot_registros_val = $("#example_info>span.text-primary").html();
              $("#example_info>span.text-primary  ").html(tot_registros_val + "<input id='tot_registros' value='" + tot_registros_val + "' type='hidden' /> " +
              "<input type='hidden' id='multiple' value='true'>");

              /** Se tuiliza para el nombre de las imagenes **/
              $("[rel=tooltip]").tooltip();
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
      
      $("#btn_Search").on('click', function () {
            // Redraw data table, causes data to be reloaded
            $('#example').DataTable().ajax.reload().data(function (d) {
                d.extra_filters = {'advanced_search': $("#frm_search_fame").serialize()};
            });
            return false;
        });
        $("#btn_clear").on('click', function () {
            busqueda(0,0);
            pageSetUp();
            // Redraw data table, causes data to be reloaded
            $('#example').DataTable().ajax.reload().data(function (d) {
              d.extra_filters = {'advanced_search': $("#frm_search_fame").serialize()};
            });
            return false;
        });
    });


// Funcion para el export
function export_apply() {
    var criterio = $('div.dataTables_filter input').val();
    var fl_instituto = document.getElementById("fl_instituto_params").value;//$('fl_instituto_params').val();
    var fl_programa = document.getElementById("fl_programa_params").value;
    var url = 'student_exp.php';
    // Envia datos por forma
    document.export.criterio.value = criterio;
    document.export.fl_instituto.value = fl_instituto;
    document.export.fl_programa.value = fl_programa;
    document.export.action = url;
    document.export.submit();
}

</script>
  <form name=export method=post>
    <input type=hidden name=criterio>
      <input type=hidden name=fl_instituto>
      <input type=hidden name=fl_programa>
    <input type=hidden name=actual>
    <input type=hidden name=nuevo>
  </form>




<script>
    function certificado(fg_tipo,user,program) {
        var user = user;
        var program = program;
        var url = '<?php echo SP_HOME_W; ?>/fame/site/certificado_pdf.php';
        // Envia datos por forma
        document.certificado.u.value = user;
        document.certificado.p.value = program;
        document.certificado.fg_tipo.value = fg_tipo;
        document.certificado.action = url;
        document.certificado.submit();
    }




  </script>
  <!-- Envia el programa usuario y un flag para identificar que es del back -->
  <form name=certificado method=post>
    <input type=hidden name=u>    
    <input type=hidden name=p>
    <input type=hidden name=fg_tipo>
  </form>




<?php
  EscribeJS( );
  PresentaFooter();
 
?>