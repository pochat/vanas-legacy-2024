<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Obtemos usuario logueado  
  $fl_usuario = ObtenUsuario();
  PresentaHeader();
  PresentaEncabezado(132);
  # Eliminamos registros que existan en la tabla temporal
  EjecutaQuery("DELETE FROM k_clase_cg_temporal WHERE fl_usuario=$fl_usuario");
  
?>
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
                  <h2>Global Class</h2>
              </header>

              <!-- widget div-->
              <div role="content">
                  <!-- widget edit box -->
                  <div class="jarviswidget-editbox">
                      <!-- This area used as dropdown edit box -->
                      <input class="form-control" type="text">	
                      <input class="form-control" type="hidden" id="fl_funcion" value="<?php echo 132; ?>">	
                  </div>
                  <!-- end widget edit box -->

                  <!-- widget content -->
                  <div class="widget-body no-padding">

                      <table id="example" class="display projects-table table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                          <thead>
                              <tr>
                                  <th class="details-control"></th>
                                  <th><div class="checkbox"><label><input class="checkbox" id="ch_todo" title="All" onchange="javascript:SelTodoLista();" type="checkbox"><span></span></label></div></th>
                                  <th><?php echo ObtenEtiqueta(1000); ?></th>
                                  <th><?php echo ObtenEtiqueta(1001); ?></th>
                                  <th><?php echo ObtenEtiqueta(1002); ?></th>
                                  <th><?php echo ObtenEtiqueta(1003); ?></th>
                                  <th><?php echo ObtenEtiqueta(1004); ?></th>
                                  <th><?php echo ObtenEtiqueta(1005); ?></th>
                                  <th><?php echo ObtenEtiqueta(1006); ?></th>
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

<div style="width: 228px; right: 0px; display: block; padding:0px 50px 10px 0px;" outline="0" class="ui-widget ui-chatbox">
  <a href="javascript:Envia('cglobales_frm.php', '');" class="btn btn-primary btn-circle btn-xl" title="Add record" style="color:#FFFFFF;"><i class="fa fa-plus"></i></a>
</div>

<script type="text/javascript">

  $(document).ready(function() {   
    // pageSetUp();
    function format ( d ) {
      // `d` is the original data object for the row
      return "<table class='display projects-table table table-striped table-hover' width='100%'>"+ d.sesiones+"</table>";
    }

    // clears the variable if left blank
      var table = $('#example').on( 'processing.dt', function ( e, settings, processing ) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        // alert(processing);
        } ).DataTable( {
          "ajax": "cglobales_list.php",
          //"serverSide": true,
          "processing": true,
          "bDestroy": true,
          "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]],
          "iDisplayLength": 15,
          "columns": [
              {
                  "class":          'details-control',
                  "orderable":      false,
                  "data":           null,
                  "defaultContent": '',
                  "width": "3%"
              },
              { "data": "checkbox", "orderable": false, "class": "text-align-center", "width": "3%"},
              { "data": "week", "orderable": false, "class": "text-align-center", "width": "3%"},
              { "data": "classname", "width": "20%" },
              { "data": "teachers", "orderable": false},
              { "data": "date", "orderable": false, "width": "10%"},
              { "data": "time", "orderable": false, "class": 'text-align-center',  "width": "5%"},
              { "data": "students",  "class": 'text-align-center', "width": "10%"},
              { "data": "mandatory", "class": 'text-align-center', "width": "10%"},
          ],
          "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'>>"+
						"t"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
          "fnDrawCallback": function (oSettings) {
            var tot_registros_val = $("#example_info>span.text-primary").html();
            $("#example_info>span.text-primary  ").html(tot_registros_val+"<input id='tot_registros' value='"+tot_registros_val+"' type='hidden' /> "+
            "<input type='hidden' id='multiple' value='true'>");
            $("[rel=tooltip]").tooltip();
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
  });

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
