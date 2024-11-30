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

  #Recuperamos el nombre del usuario:
  
  $Query="SELECT ds_nombres,ds_apaterno,fl_perfil_sp FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Query);
  $ds_nombre=$row[0];
  $ds_apaterno=$row[0];
  $fl_perfil_fame=$row['fl_perfil_sp'];
  $nb_user_actual=$ds_nombre." ".$ds_apaterno;
  
  $Query="SELECT fg_plan,fe_periodo_final,fg_pago_fallido,fg_pago_manual,fe_periodo_inicial FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fg_plan_actual_instituto=$row['fg_plan'];
  $fe_periodo_expiracion_plan=$row['fe_periodo_final'];
  $fg_pago_fallido=$row['fg_pago_fallido'];
  $cl_metodo_pago=$row['fg_pago_manual'];
  $fe_periodo_inicial=$row['fe_periodo_inicial'];
?>
<style>
	div.dataTables_filter label {
	    float: left !important;
	}
</style>


	<div class="widget-body">
                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active" id="tab1">
                        <a href="#current_plan" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-bars"></i>&nbsp;<?php echo ObtenEtiqueta(2525) ?></a>
                    </li>
                </ul>


		 <div id="myTabContent1" class="tab-content padding-10 ">
		   <div class="tab-pane fade in active" id="current_plan">


				<div class="row">
					<div class="col-md-12">
						<?php $info= ObtenEtiqueta(2551);
							  echo str_uso_normal($info);
						?>
						
						<br><br>
						<b><?php echo str_uso_normal(ObtenEtiqueta(2552));?></b><br><br>
						<ul class="list-inline">
						  <li class="text-center"><a href="templates/nothresh_school.csv"><i class="fa fa-file-excel-o" aria-hidden="true"></i><br><small class="text-muted"><?php echo ObtenEtiqueta(2061);?></small></a></li>
						  <li class="text-center"><a href="templates/nothresh_teachers.csv"><i class="fa fa-file-excel-o" aria-hidden="true"></i><br><small class="text-muted"><?php echo ObtenEtiqueta(2540);?></small></a></li>
						  <li class="text-center"><a href="templates/nothresh_student.csv"><i class="fa fa-file-excel-o" aria-hidden="true"></i><br><small class="text-muted"><?php echo ObtenEtiqueta(2541);?></small></a></li>
						</ul>
						
					</div>
					<div class="col-md-12">
						<?php      
						  SectionIni();			 
						  ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "gabriel2", "fa-table", "", false);
						  # Muestra Inicio de la tabla
						  $titulos = array(ObtenEtiqueta(2528), ObtenEtiqueta(2531),ObtenEtiqueta(2532), ObtenEtiqueta(2529), ObtenEtiqueta(2530),  ObtenEtiqueta(2548));
						  MuestraTablaIni("tbl_users", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos,true);
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
			   </div>
		  </div>
		</div>
  </div>
  
  
  
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width: 81%;margin: auto;">
    <div class="modal-content" id="presenta_info">
      
	  
	  
    </div>
  </div>
</div>


  
  
  <script type="text/javascript">


	function MuestraResultados(fl_upload){
		
		$("#exampleModal").modal("show");
		
		   //pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'site/muestra_resultados.php',
             data: 'fl_upload=' + fl_upload,
             async: true,
             success: function (html) {
                 $('#presenta_info').html(html);
             }
         });

		
		
		
		
		
	}







	
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
	    var table = $('#tbl_users').on( 'processing.dt', function ( e, settings, processing ) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        }).DataTable( {
	        "ajax": "Querys/csf_list.php",
	        "bDestroy": true,
	        "iDisplayLength": 15,
	        "columns": [
	            { "data": "checkbox", "width":"10px", "orderable":false },
	           { "data": "start_time" },
                  { "data": "start_time2" },
				  { "data": "end_time" },
				  { "data": "runtime" },
                 
                  { "data": "status","class":"text-center" },
                  { "data": "espacio","class":"text-center" },
                  { "data": "extradata" }
	        ],
	        "order": [[0, 'desc']],
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

</script>
