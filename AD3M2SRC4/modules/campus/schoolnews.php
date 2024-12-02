<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  /*# Consulta para el listado
  $Query  = "SELECT fl_blog, ".ConsultaFechaBD('fe_blog', FMT_FECHA)." '".ObtenEtiqueta(450)."', ds_titulo '".ETQ_TITULO."', ";
  $Query .= "CASE WHEN fg_maestros='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(451)."|center', ";
  $Query .= "CASE WHEN fg_alumnos='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(452)."|center', ";
  $Query .= "CASE WHEN fg_notificacion='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(453)."|center', ds_login '".ObtenEtiqueta(454)."', ";
  $Query .= "no_hits '".ObtenEtiqueta(455)."|right' ";
  $Query .= "FROM c_blog a, c_usuario b ";
  $Query .= "WHERE a.fl_usuario=b.fl_usuario ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND fe_blog LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_titulo LIKE '%criterio%' "; break;
      case 3: $Query .= "AND ds_login LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND (fe_blog LIKE '%$criterio%' OR ds_titulo LIKE '%$criterio%' OR ds_login LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY fe_blog DESC";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_BLOGS, $Query, TB_LN_IUD, True, False, array(ObtenEtiqueta(450), ETQ_TITULO, ObtenEtiqueta(454)));*/
  PresentaHeader();
  PresentaEncabezado(FUNC_BLOGS);
  # Si el usuario queria actualizar un video pero al final no lo decidio asi 
  # Buscara si hay registros
  if(ExisteEnTabla('k_vid_news_temp', 'fl_usuario', $fl_usuario)){
    $rs = EjecutaQuery("SELECT fl_clave FROM k_vid_news_temp WHERE fl_usuario=$fl_usuario");
    for($i=0;$row=RecuperaRegistro($rs); $i++){
      $fl_blog = $row[0];
      if(empty($fl_blog))
        $fl_blog = 0;
      # Elimina la carpeta que secreo cuando el usuario queria actualiza y alfinal dijo que no
      eliminarDirec(VID_CAM_NEWS."/video_".$fl_blog);
      # Regresamos la carpeta orignal
      rename(VID_CAM_NEWS."/video_re".$fl_blog, VID_CAM_NEWS."/video_".$fl_blog);      
    }
    # Eliminamos los registros
    EjecutaQuery("DELETE FROM k_vid_news_temp WHERE fl_usuario=$fl_usuario");
  }
?>
<!-- Listado -->
<div class="content">
  <section>
  <div class="row" style="margin-left: 0px; margin-right: 0px;">
      <div class="col-xs-12 col-sm-12" style="padding: 3px">
          <!-- Widget ID (each widget will need unique ID)-->
          <div role="widget" class="jarviswidget" id="widget-grid-1" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-collapsed="false">
              <header role="heading">
                  <div role="menu" class="jarviswidget-ctrls">   
                      <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
                  </div>
                  <span class="widget-icon"> <i class="fa fa-book"></i> </span>
                  <h2>School news </h2>
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

                      <table id="tbl_schoolnews" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                          <thead>
                              <tr>
                                <th data-hide="phone"><i class="fa fa-fw fa-calendar text-muted hidden-md hidden-sm hidden-xs"></i> <?php echo ObtenEtiqueta(450); ?></th>
                                <th data-hide="phone"><i class="fa fa-fw fa-calendar text-muted hidden-md hidden-sm hidden-xs"></i> <?php echo ObtenEtiqueta(45); ?> </th>
                     
								<th data-class="expand">&nbsp; </th>
                                <th data-hide="phone, tablet"><i class="fa fa-fw fa-users text-muted hidden-md hidden-sm hidden-xs"></i> <?php echo ObtenEtiqueta(451); ?></th>
                                <th data-hide="phone"><i class="fa fa-fw fa-users text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(452); ?></th>
                                <th data-hide="phone, tablet"><i class="fa fa-fw fa-paper-plane text-muted hidden-md hidden-sm hidden-xs"></i> <?php echo ObtenEtiqueta(453); ?></th>
                                <th data-hide="phone"><i class="fa fa-fw fa-vimeo-square text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(454); ?></th>
                                <th data-hide="phone"><i class="fa fa-fw fa-eye text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(455); ?></th>
                                <th data-hide="phone, tablet"></th>
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
<!-- btn add -->
 <div style="width: 228px; right: 0px; display: block; padding:0px 50px 10px 0px;" outline="0" class="ui-widget ui-chatbox">
  <a href="javascript:Envia('schoolnews_frm.php', '');" class="btn btn-primary btn-circle btn-xl" title="Add record" style="color:#FFFFFF;"><i class="fa fa-plus"></i></a>
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
        return '';
    }

    // clears the variable if left blank
      var table = $('#tbl_schoolnews').on( 'processing.dt', function ( e, settings, processing ) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        // alert(processing);
        } ).DataTable( {
          "ajax": "schoolnews_list.php",
          //"serverSide": true,
          "processing": true,
          "bDestroy": true,
          "autoWidth": true,
          "columns": [
              { "data": "datepublisher", "type": "date"},
              { "data": "title" },
		      { "data": "duration" },
              { "data": "teachers", "class": "text-align-center"},
              { "data": "students", "class": "text-align-center"},
              { "data": "send", "class": "text-align-center"},
              { "data": "publisher", "orderable": false},
              { "data": "view", "class": "text-align-center"},
              { "data": "delete" },
          ],
          // "order": [[ 0, "desc" ]],
          "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-11'f><'col-sm-1 col-xs-12 hidden-xs'l>r>"+
					"t"+
					"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
          "autoWidth" : true,
          "fnDrawCallback": function (oSettings) {
            var tot_registros_val = $("#tbl_schoolnew_info>span.text-primary").html();
            $("#tbl_schoolnew_info>span.text-primary  ").html(tot_registros_val+"<input id='tot_registros' value='"+tot_registros_val+"' type='hidden' /> "+
            "<input type='hidden' id='multiple' value='true'>");
          }
      });


       
      // Add event listener for opening and closing details
      $('#tbl_schoolnews tbody').on('click', 'td.details-control', function () {
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
<?php
EscribeJS( );
PresentaFooter();
?>