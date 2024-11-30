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
<!-- LISTADO PARA LOS USUARIOS DEL ADMINISTRADOR ES DECIR TEACHERS Y STUDENTS -->
  <div class="row" style="padding:5px;">
    <div class="row">
      <div class='col-xs-12 col-sm-7 col-md-7 col-lg-3'>
             
      </div>
      <div class='col-xs-12 col-sm-7 col-md-7 col-lg-3'>
            
      </div>      
      
    </div>
    <?php      
      SectionIni();
        # Valores para el boton de actions
        #$opt_btn = array(ObtenEtiqueta(2593));
        //$desc_btn = array(ObtenEtiqueta(2594));
        //$val_btn = array(LOCKED_INSTITUTE);        
        ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "gabriel2", "fa-table", ObtenEtiqueta(2061), true, true, false, false, false, ObtenEtiqueta(1074), "default");
          # Muestra Inicio de la tabla
        $titulos = array(ObtenEtiqueta(2593),'', ObtenEtiqueta(1055),'');
          MuestraTablaIni("tbl_institutos", "display projects-table table table-striped table-bordered table-hover", "100%",$titulos,false);
          # Muestra Fin de la tabla
          MuestraTablaFin(false);
          # Campos para el total de registros
          CampoOculto('tot_reg', !empty($tot_reg)?$tot_reg:NULL);
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
        return '';
		}

		// clears the variable if left blank
	    var table = $('#tbl_institutos').on( 'processing.dt', function ( e, settings, processing ) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        }).DataTable( {
	        "ajax": "Querys/institutions.php",
	        "bDestroy": true,
	        "iDisplayLength": 15,
	        "columns": [
	            //{ "data": "checkbox", "width": "10px", "orderable": false },
                { "data": "checked","class":"text-center", "width": "60px" },
	            { "data": "img", "width":"15px", "orderable":false},
	            { "data": "name" },
				{ "data": "activity" },
                { "data": "extra" }
            	
	        ],
	        "order": [[2, 'asc']],
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
      /** OBTENEMOS EL VALOR DEL  TIPO DE USUARIO A BUSCAR **/      
      // Typo de usuarios
      $("#fl_users").on('change', function () {
        var v =$(this).val();
        // if(v == 'ALL')
          // $('#fl_status').addClass('hidden');
        // else
          // $('#fl_status').removeClass('hidden');
        // busca en la columna del tupo         
        table.columns(11).search(v).draw();
        // alert(v);
      });
      /** OBTENEMOS EL VALOR DEL  TIPO DE STATUS A BUSCAR **/      
      // Usuarios activos o inactivos
       $("#fl_status").on('change', function () {
        var v =$(this).val();
        // busca en la columna del tupo  
        table.columns(12).search(v).draw();
        // alert(v);        
      });
      /*** FIN DE BUSQUEDA AVANZADA ***/
            
	};
	/** FIN DE SCRIPT PARA DATATABLE **/
	// end pagefunction
  function cambiar_perfil(p_user, p_perfil){
    var option = '<?php echo CHANGE_PERFIL; ?>';
    $.ajax({
      type: "POST",
      url: "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
      async: false,
      data: "fl_action="+option+"&fl_usuario="+p_user+"&fl_perfil_user="+p_perfil
    });
    $('#tbl_users').DataTable().ajax.reload();
  }
  
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
		 
		 
		 var button = '<a href="javascript:void(0);" id="btn_reset_filter" onclick="ResetFilter();" class="btn btn-default btn-xs" style="margin-left:5px;margin-bottom:5px;margin-top: 5px;"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;'+'<?php echo ObtenEtiqueta(2306); ?>'+'</a>';
		 $('#tbl_users_filter').append(button);	
	 
		 
	 });
	 
	  function ResetFilter(){
	   
	    $("#tbl_users").DataTable().search("").draw();  //limpiar el restet

	  }
	  function url(fl_instituto) {

	      document.super.ins.value = fl_instituto;
	      document.super.action = '../../../login_validate.php';
	      document.super.submit();

	      //window.location.assign("../../login_validate.php?i=" + fl_instituto + "");

	  }



	  function HabilitarInstituto(fl_instituto) {
	      

	      if ($("#ch_" + fl_instituto + "").is(':checked')) {
	          var check = 1;
	          var etq = "<?php echo ObtenEtiqueta(2595);?>";
	      } else {
	          var check = 0;
	          var etq = "<?php echo ObtenEtiqueta(2594);?>";
	      }

	      //Enviamos por post para hablitar/desabilitar la privacidad.
	      $.ajax({
	          type: 'POST',
	          url: 'site/setting_institutos.php',
	          data: 'valor=' + check +
                    '&fl_instituto=' + fl_instituto,
	          async: true,
	          success: function (html) {

	          }
	      });


	      $.smallBox({
	          title: etq,
	          content: "&nbsp;&nbsp;",
	          color: "#5384AF",
	          //timeout: 8000,
	          icon: "fa fa-check-square-o"
	      });
	     



	  }
  

</script>

<form name='super' method='post' target='_blank'>
    <input type=hidden name=ins>    
</form>