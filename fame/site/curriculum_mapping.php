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
<style>
div.dataTables_filter label {
    float: left !important;
}

</style>



<div style="width: 218px; right: 0px; display: block; padding:0px 0px 0px 145px;" outline="0" class="ui-widget ui-chatbox">
          <a href="index.php#site/curriculum_mapping_frm.php" class="btn btn-primary btn-circle btn-xl" title="Add record" style="color:#FFFFFF;height: 59px;
    width: 59px;padding: 4px 15px;"><i class="fa fa-plus"></i></a>
</div>
<!--
<form name='frm_insupd' method='POST' action='index.php#site/curriculum_mapping_frm.php'>
	<input type='hidden' name='clave'>
</form>
<form name='frm_del' method='POST' action='index.php#site/curriculum_mapping_del.php'>
	<input type='hidden' name='clave'>
</form>

-->



<!-- LISTADO PARA LOS USUARIOS DEL ADMINISTRADOR ES DECIR TEACHERS Y STUDENTS -->
  <div class="row" style="padding:5px;">

    <?php      
      SectionIni();
          ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "students2", "fa-table", ObtenEtiqueta(1656), true, true, false, false, false, ObtenEtiqueta(1074), "default", (isset($opt_btn)?$opt_btn:NULL), (isset($val_btn)?$val_btn:NULL), (isset($desc_btn)?$desc_btn:NULL));
          # Muestra Inicio de la tabla  Criterion name |
		     
          $titulos = array('',''.ObtenEtiqueta(2050).'',''.ObtenEtiqueta(2052).'', ''.ObtenEtiqueta(2053).'','',''.ObtenEtiqueta(2054).'',''.ObtenEtiqueta(2055).'','');
          MuestraTablaIni("tbl_users", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos,false);          
          # Muestra Fin de la tabla
          MuestraTablaFin(false);
          # Campos para el total de registros
          CampoOculto('tot_reg', (isset($tot_reg)?$tot_reg:NULL));
          # Muestra el modal para las acciones
          MuestraModal("Actions"); 
        ArticleFin();
      SectionFin();
    ?>
  </div>
  
  <script type="text/javascript">
	
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
  /* Debemos agregarlo para el fucnionamiento de diversos  plugins*/
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
	 * OR you can load chain scripts by doing
	 * 
	 * loadScript(".../plugin.js", function(){
	 * 	 loadScript("../plugin.js", function(){
	 * 	   ...
	 *   })
	 * });
	 */

	// pagefunction
  /** INICIO DE SCRIPT PARA DATATABLE **/
	var pagefunction = function() {
    // alert('ola');
		/* Formatting function for row details - modify as you need */
		        function format ( d ) {
            // `d` is the original data object for the row
            return '<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed" width="100%">'+
            d.programas
                // '<tr>'+
                    // '<td style="width:110px"><?php echo ObtenEtiqueta(114); ?>:</td>'+
                    // '<td>'+d.gender+'</td>'+
                // '</tr>'+
                // '<tr>'+
                    // '<td style="width:110px"><?php echo ObtenEtiqueta(693); ?>:</td>'+
                    // '<td>'+d.duplicados+'</td>'+
                // '</tr>'+
                // '<tr>'+
                    // '<td><?php echo ObtenEtiqueta(120); ?>:</td>'+
                    // '<td>'+d.edad+'</td>'+
                // '</tr>'+
                // '<tr>'+
                    // '<td>Time Preferences:</td>'+
                    // '<td>'+d.preferences+'</td>'+
                // '</tr>'+
                // '<tr>'+
                    // '<td><?php echo ObtenEtiqueta(61); ?>:</td>'+
                    // '<td>'+d.information+'</td>'+
                // '</tr>'+

            + '</table>';
        }

		// clears the variable if left blank
	    var table = $('#tbl_users').on( 'processing.dt', function ( e, settings, processing ) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        }).DataTable( {
	        "ajax": "Querys/curriculum_mapping_list.php",
	        "bDestroy": true,
	        "iDisplayLength": 10,
	        "columns": [
	           // { "data": "checkbox", "width":"15px", "orderable": false},
               {
                   "class": 'details-control',
                   "orderable": false,
                   "data": null,
                   "defaultContent": ''
               },

				  { "data": "cl_clave" },
				  { "data": "name" },
				  { "data": "level" },
				  { "data": "pais" },
				  { "data": "descrip" },
				  { "data": "prerequisito" },
				  { "data": "delete" }
	           
	                    	            
	          	           	            
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
	    $('#tbl_users tbody').on('click', 'td.details-control', function () {
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
      $("#fl_programa_sp").on('change', function () {
        var v =$(this).val();
        // busca en la columna del tupo        
        table.columns(13).search(v).draw();
      });
      // Usuarios activos o inactivos
      $("#fl_status").on('change', function () {
        var v =$(this).val();        
        // busca en la columna del tupo        
        table.columns(15).search(v).draw();       
      });
      // Programas
      $("#fl_grupo_sp").on('change', function () {
        var v =$(this).val();
        // busca en la columna del tupo        
        table.columns(3).search(v).draw(); 
      });
      
      /*** FIN DE BUSQUEDA AVANZADA ***/
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

	
	 $(document).ready(function () {	 
		 var button = '<a href="javascript:void(0);" id="btn_reset_filter" onclick="ResetFilter();" class="btn btn-default btn-xs" style="margin-left:5px;margin-bottom:5px;margin-top: 5px;"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp; Reset search</a>';
		 $('#tbl_users_filter').append(button);		 
	 });
	 
	  function ResetFilter(){	   
	    $("#tbl_users").DataTable().search("").draw();  //limpiar el restet	   
    }
	
	function Delete(fl_course_code) {
		var answer = confirm('<?php echo str_ascii(ObtenMensaje(13)); ?>');
		
		if(answer) {
			//pasamos por ajax los valores y presentamos modal.
			$.ajax({
				type: 'POST',
				url: 'site/curriculum_mapping_del.php',
				data: 'clave='+fl_course_code,
				async: true,
				success: function (html) {
					//$('#muestra_escalas').html(html);
				}
			});
			$('#tbl_users').DataTable().ajax.reload();

		}
		
		
		
	}
	
	
</script>