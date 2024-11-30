<?php 
	# Libreria de funciones
	require("../../modules/common/lib/cam_general.inc.php");
	require("../lib/layout_self.php");
	require("../lib/self_func.php");
  
  // $fl_insituto = ObtenInstituto($fl_usuario);
  $fl_insituto = 0;
?>
<!-- Encabezado de users administrador -->
<div class="row">
  <!--x<button id="prueba" class="btn btn-primary">test</button>-->
  <!-- col -->
  <div class="col-xs-12 col-sm-7 col-md-7 col-lg-2 padding-5">
    <div class="form-group">
      <?php
      $options = array('All Users', 'Students','Teachers', 'Admin(s)', 'Unassigned');
      $valores = array('AU','S','T','AD','UN');
      CampoSelect('fl_users', $options, $valores, 'AU');
      ?>
    </div>
  </div>
  
  <div class="col-xs-12 col-sm-7 col-md-7 col-lg-2 padding-5">
    <?php
    # Por defaul ocultamos este filtro
    $opt_status = array('Status', 'Active','Inactive');
    $val_status = array(2,1,0);
    CampoSelect('fl_status', $opt_status, $val_status, 2, 'select2 hidden');
    ?>
  </div>
  <!-- end col -->
  
  <!-- right side of the page with the sparkline graphs -->
  <!-- col -->
  <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
    <!-- sparks -->
    <ul id="sparks">
      <li class="sparks-info">
        <h5> Total de Licencias: <?php echo $tot_licencias = ObtenNumLicencias($fl_usuario); ?></h5>
      </li>
      <li class="sparks-info">
        <h5> Users: <?php echo $no_usuarios = ObtenNumeroUserInst($fl_instituto); ?></h5>
      </li>
      <li class="sparks-info">
        <h5> Available: 
        <?php
        echo $avaible = $tot_licencias - $no_usuarios;
        ?>
        </h5>
      </li>
    </ul>
    <!-- end sparks -->
    <div class="text-align-right">
    <div>15.45 GB(51%) de 30 GB used</div>
    <div><a href="#">Manage</a></div>
    </div>
  </div>
  <!-- end col -->

</div>

<!--- Listado de todos los usarios (Maestros y Estudiantes)-->
<div class="row" style="padding:5px;">
  <?php
    SectionIni();
      # Valores para el boton de actions
      $opt_btn = array('Add Student', 'Import Student', 'Add Teacher', 'Import Teacher', 'Activate', 'Desactive', 'Delete');
      $val_btn = array(1,2,3,4,5,6,7);     
      ArticleIni("gabriel", "fa-table", "Hide/Show Columns", true, true, false, false, false, "Actions", "default", $opt_btn, $val_btn);
        # Muestra Inicio de la tabla
        $titulos = array("ID", "Name", "Type", "Status", "Last login", "Usage");
        MuestraTablaIni("tbl_users", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos);
        # Muestra Fin de la tabla
        MuestraTablaFin(true, "tbl_users");
      ArticleFin();
    SectionFin();
  ?>
</div>

<!-- PAGE RELATED PLUGIN(S) DATETABLES -->
<script src="<?php echo PATH_N_COM_JS; ?>/plugin/datatables/jquery.dataTables-cust.min.js"></script>
<script src="<?php echo PATH_N_COM_JS; ?>/plugin/datatables/DT_bootstrap.js"></script>
  
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
        // return d.id;
    }

    // clears the variable if left blank
    var table = $('#tbl_users').DataTable({
        "ajax": "users_list.php",
        "serverSide": true,
        // "processing": true,
        "bDestroy": true,
        "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]],
        "iDisplayLength": 15,
        "columns": [
            {
              "class": 'details-control',
              "orderable": false,
              "data": null,
              "defaultContent": ''
            },
            { "data": "id", "orderable": false},
            { "data": "name" },
            { "data": "perfil" },
            { "data": "status" },
            { "data": "lastlogin" },
            { "data": "usange"},              
        ],
        "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'>>"+
          "t"+
          "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
        "fnDrawCallback": function (oSettings) {
          /*var tot_registros_val = $("#example_info>span.text-primary").html();
          $("#example_info>span.text-primary  ").html(tot_registros_val+"<input id='tot_registros' value='"+tot_registros_val+"' type='hidden' /> "+
          "<input type='hidden' id='multiple' value='true'>");*/
        }
    });
    
    // Add event listener for opening and closing details
    /*$('#tbl_users').on('click', 'td.details-control', function () {
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
    });    */
  });