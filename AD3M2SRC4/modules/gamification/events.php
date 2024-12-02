<?php
  # La libreria de funciones
  require '../../lib/general.inc.php';

  # Recibe parametros
  $actual = RecibeParametroNumerico('actual');  
  
   # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_EVENTS, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  PresentaHeader();
  PresentaEncabezado(FUNC_EVENTS);
?>
<div class="content">
  <section class="" id="widget-grid"> 
    <!-- Listado -->
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
        <div class="col-xs-12 col-sm-12" style="padding: 3px">
          <!-- Widget ID (each widget will need unique ID)-->
          <div role="widget" class="jarviswidget" id="widget-grid-1" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-collapsed="false">
              <header role="heading">
                  <div role="menu" class="jarviswidget-ctrls">   
                      <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
                  </div>
                  <span class="widget-icon"> <i class="fa fa-trophy"></i> </span>
                  <h2>Events </h2>
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
                            <th><i class=" text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(2179); ?></th>                                 
                            <th><i class=" text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(2163); ?></th>
							<th><i class=" text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(2164); ?></th> 
                            
							                                                             
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
<div style="width: 228px; right: 0px; display: block; padding:0px 50px 10px 0px;" outline="0" class="ui-widget ui-chatbox">
	<a href="javascript:Envia('events_frm.php', '');" class="btn btn-primary btn-circle btn-xl" title="Add record" style="color:#FFFFFF;"><i class="fa fa-plus"></i></a>
</div>

<script type='text/javascript'>
    $(document).ready(function () {
      /* Formatting function for row details - modify as you need */
    function format ( d ) {
        // `d` is the original data object for the row
        return '';
    }

    // clears the variable if left blank
      var table = $('#example').on( 'processing.dt', function ( e, settings, processing ) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        // alert(processing);
        } ).DataTable( {
          "ajax": "events_list.php",
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
                  "defaultContent": ''
              },
              { "data": "clave" },
              { "data": "name" },
			  { "data": "ds_decripcion" },
            
              { "data": "btns", "class": "text-align-center"}
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
      
    });
    function Eliminar(cupon){
		var con = confirm("<?php echo ObtenEtiqueta(2304); ?>");
		if(con==true){
			Envia('events_del.php', cupon)
		}
	}
</script>
<?php
EscribeJS( );
PresentaFooter();
?>