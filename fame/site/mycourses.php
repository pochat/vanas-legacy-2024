<?php 

	# Libreria de funciones	
	require("../lib/self_general.php");

	# Libreria de funciones
	// require("../../modules/common/lib/cam_general.inc.php");

    # Verifica que exista una sesion valida en el cookie y la resetea
    $fl_usuario = ValidaSesion(False,0, True);

    # Verifica que el usuario tenga permiso de usar esta funcion
    if(!ValidaPermisoSelf(FUNC_SELF)) {
        MuestraPaginaError(ERR_SIN_PERMISO);
        exit;
    }

?>

<div class="row" style="padding:5px;">
  <div class="row">
    <div class='col-xs-12 col-sm-7 col-md-7 col-lg-2'>
    </div>
    <div class='col-xs-12 col-sm-7 col-md-7 col-lg-3'>
      <div class='form-group'>
      <?php
      # Obtiene los programas del usuario
      $Query  = "SELECT cp.nb_programa".$sufix.", kup.fl_programa_sp ";
      $Query .= "FROM k_usuario_programa kup, c_programa_sp cp ";
      $Query .= "WHERE kup.fl_programa_sp = cp.fl_programa_sp AND fl_usuario_sp=$fl_usuario ";
      // CampoSelectBD('fl_programa_spp', $Query, 'ALL', 'select2', True, '', '', 'Show all courses', 'ALL');
      ?>
      </div>      
    </div>  
  </div>
  <?php      
    SectionIni();
      ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "mycourses", "fa-table", ObtenEtiqueta(1099), true, true, false, false, false);
        # Muestra Inicio de la tabla
        $titulos = array(ObtenEtiqueta(1054), ObtenEtiqueta(1094), 'Order', ObtenEtiqueta(1101), ObtenEtiqueta(1057), ObtenEtiqueta(1077), ObtenEtiqueta(1078), ObtenEtiqueta(1098));
        MuestraTablaIni("tbl_mycourses", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos);          
        # Muestra Fin de la tabla
        MuestraTablaFin(false);
        # Muestra el modal para certificado
        MuestraModal("certificado"); 
        MuestraModal("pause_course", true);
        # Campos para el total de registros
        CampoOculto('tot_reg', !empty($tot_reg)?$tot_reg:NULL);        
      ArticleFin();
    SectionFin();    
  ?>
</div>

<script type="text/javascript">
  /* Debemos agregarlo para el fucnionamiento de diversos  plugins*/
	pageSetUp();

	// pagefunction
  /** INICIO DE SCRIPT PARA DATATABLE **/
	var pagefunction = function() {
    // alert('ola');
		/* Formatting function for row details - modify as you need */
		function format ( d ) {
		    // `d` is the original data object for the row
        return d;
		}

		// clears the variable if left blank
    var table = $('#tbl_mycourses').on( 'processing.dt', function ( e, settings, processing ) {
    $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
    $("#vanas_loader").show();
    if(processing == false)
      $("vanas_loader").hide();
    }).DataTable({
      "ajax": "Querys/mycourses.php",
      "bDestroy": true,
      "iDisplayLength": 15,
      "columns": [
          { "data": "checkbox", "orderable": false},
          { "data": "id", "width":"10%", "orderable": false },
          { "data": "nb_programa", "orderable": true },
          { "data": "order", "orderable": true },
          { "data": "ds_ruta_foto_tec", "width":"5%", "orderable": true},
          { "data": "status", "class": "text-align-center" },
          // { "data": "assigment", "class": "text-align-center" },
          { "data": "ds_progreso", "orderable": true },
          { "data": "no_promedio_t" },
          { "data": "fg_certificado" },
          { "data": "fl_programa_sp", "visible": false },
      ],
      "order": [[0, 'asc']],
      "fnDrawCallback": function( oSettings ) {
        runAllCharts();
        /** Se tuiliza para el nombre de las imagenes **/
        $("[rel=tooltip]").tooltip();
        /** Total de registros **/
        var oSettings = this.fnSettings();
        var iTotalRecords = oSettings.fnRecordsTotal(); 
        /** Es necesario si vamos a selelecionar muchos registros en la tabla **/
        $("#tot_reg").val(iTotalRecords);
      }
    });

    // Add event listener for opening and closing details
    $('#tbl_mycourses tbody').on('click', 'td.details-control', function () {
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
    /** INICIO DE SELECIONAR TODOS ***/   
    $('#sel_todo').on('change', function(){
      var v_sel_todo = $(this).is(':checked'), i;
      var iTotalRecords = $('#tot_reg').val();
      for(i=1;i<=iTotalRecords;i++){
        $("#ch_"+i).prop('checked', v_sel_todo);
      }
    })
    /** FIN DE SELECIONAR TODOS ***/
    
    /*** INICIO DE BUSQUEDA AVANZADA ***/      
      /** OBTENEMOS EL VALOR DEL  TIPO DE STATUS A BUSCAR **/ 
      // Programas
      $("#fl_programa_spp").on('change', function () {
        var v =$(this).val();
        // busca en la columna del tupo        
        table.columns(7).search(v).draw();
      });
    /** FIN DE BUSQUEDA AVANZADA **/
	};
  
	/** FIN DE SCRIPT PARA DATATABLE **/
	// end pagefunction
  
	// load related plugins & run pagefunction
  /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/      
  /** IMPORTANTES Y NECESARIAS EN DONDE SE UTILIZEN **/
	loadScript("../fame/js/plugin/datatables/jquery.dataTables.min.js", function(){
		loadScript("../fame/js/plugin/datatables/dataTables.colVis.min.js", function(){
			loadScript("../fame/js/plugin/datatables/dataTables.tableTools.min.js", function(){
				loadScript("../fame/js/plugin/datatables/dataTables.bootstrap.min.js", function(){
					loadScript("../fame/js/plugin/datatable-responsive/datatables.responsive.min.js", pagefunction)
				});
			});
		});
	});
  /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/
  function required(order, playlist, programa) {
    $.ajax({
      type: "POST",
      url: "site/getPrevPlaylist.php",
      async: false,
      data: { 'order': order,
              'playlist': playlist,
              'fl_programa': programa,
            },
      success: function(html){
        $('#modal_actions').html(html);
        $('#requiredModal').modal('toggle');
      }
    });
    
  }   
  
</script>
  <!-- Modal -->
  <style>  
   @media (min-width: 768px){
  .modal-dialog {
      width: 600px !important;
      margin: 30px auto !important;
  }
  .mike_jd{
    width:40%;
    margin:10% 10% 15% 30%;
    
  }
 }
</style>
<div class="modal fade" id="requiredModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div id="modal_actions" class="modal-dialog mike_jd" role="document">
    </div>
  </div>
</div>
