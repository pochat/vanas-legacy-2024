<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  # Obtenemos el perfil
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
?>
<!-- LISTADO PARA LOS USUARIOS ESTUDIANTES DE VANAS SOLAMENTE -->
  <div class="row" style="padding:5px;">
    <div class="row">
     
    </div>
    <?php      
      SectionIni();        
        ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "referrals", "fa-table", ObtenEtiqueta(2202), true, true, false, false, false, ObtenEtiqueta(1074), "default", $opt_btn, $val_btn, $desc_btn);
          # Muestra Inicio de la tabla          
          $titulos = array( ObtenEtiqueta(2203), ObtenEtiqueta(2204), ObtenEtiqueta(2205),ObtenEtiqueta(2206));
          MuestraTablaIni("tbl_referrals", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos,false);          
          # Muestra Fin de la tabla
          MuestraTablaFin(false);
          # Campos para el total de registros
          CampoOculto('tot_reg', $tot_reg);
          # Muestra el modal para las acciones
          MuestraModal("Actions"); 
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
	    var table = $('#tbl_referrals').on( 'processing.dt', function ( e, settings, processing ) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        }).DataTable( {
	        "ajax": "Querys/referrals.php",
	        "bDestroy": true,
	        "iDisplayLength": 15,
	        "columns": [
	          
	            { "data": "email"},
	            { "data": "programa" },
	            { "data": "status", "className": "text-align-center"},
              { "data": "fecha", "className": "text-align-center" },
			  { "data": "action", "className": "text-align-center" }	
			  
	        ],
	        "order": [[1, 'asc']],
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
	    } );

	    // Add event listener for opening and closing details
	    $('#tbl_referrals tbody').on('click', 'td.details-control', function () {
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
      /** FIN DE SELECIONAR TODOS ****/
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
	
</script>