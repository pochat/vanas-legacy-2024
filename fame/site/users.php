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

  # Retrieve the privacy information for the institute
  $institutePermits = RecuperaValor("SELECT fg_gender, fg_grade, fg_educational, fg_international, fg_blocking, fg_ferpa, fg_addStudents, fg_addTeachers, fg_deletions FROM k_instituto_filtro WHERE fl_instituto=$fl_instituto");
  $fg_addStudents=$institutePermits['fg_addStudents'];
  $fg_addTeachers=$institutePermits['fg_addTeachers'];
  $fg_deletions=$institutePermits['fg_deletions'];

  # Retrieve the rector institute
  $rector_institute=RecuperaValor("SELECT fl_instituto_rector FROM c_instituto WHERE fl_instituto=$fl_instituto");

  # Check if the rector exists
  $re_exist= !empty($rector_institute[0])?true:false;

  if ($re_exist==true) {
    # Get permits from Rector
    $rectorPermits = RecuperaValor("SELECT fg_addStudents, fg_addTeachers, fg_deletions FROM k_instituto_filtro WHERE fl_instituto=".$rector_institute[0]."");
    $re_addStudents=$rectorPermits['fg_addStudents'];
    $re_addTeachers=$rectorPermits['fg_addTeachers'];
    $re_deletions=$rectorPermits['fg_deletions'];
  } else {
    $re_addStudents=0;
    $re_addTeachers=0;
    $re_deletions=0;
  }

  # Obtenemos el perfil
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);

  # Check if a user is selected to see its detail information
	$selected = (isset($_GET['selected'])?$_GET['selected']:0);
	if ($selected != 0) {
	  $ajaxUrl = "Querys/users.php?selected=".$selected;
	  $Query = "SELECT fl_usuario, ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario = ".decriptClave($selected)." LIMIT 1";
	  $userData = RecuperaValor($Query);
	  $userName = "<a href='index.php#site/users.php'>User</a> > ".$userData['ds_nombres']." ".$userData['ds_apaterno'];
	  $visible = true;
	} else {
	  $ajaxUrl = "Querys/users.php";
	  $userName = 'Users';
	  $visible = false;
	}

	# Obtenemos la informacion de privacidad
	$row0 = RecuperaValor("SELECT fg_gender, fg_grade, fg_educational, fg_international, fg_blocking, fg_ferpa, fg_addStudents, fg_addTeachers, fg_deletions FROM k_instituto_filtro WHERE fl_instituto=$fl_instituto");
	$fg_genderr = $row0['fg_gender'];
	$fg_gradee = $row0['fg_grade'];
	$fg_educational = $row0['fg_educational'];
	$fg_international = $row0['fg_international'];
	$fg_blocking = $row0['fg_blocking'];
	$fg_ferpa=$row0['fg_ferpa'];
	$fg_addStudents=$row0['fg_addStudents'];
	$fg_addTeachers=$row0['fg_addTeachers'];
	$fg_deletions=$row0['fg_deletions'];

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
        <div class='form-group'>
        <?php
        $options = array(ObtenEtiqueta(1035), ObtenEtiqueta(1036), ObtenEtiqueta(1037), ObtenEtiqueta(1038), ObtenEtiqueta(1039));
        $valores = array("ALL",PFL_ESTUDIANTE_SELF, PFL_MAESTRO_SELF, "AD",'Unassigned');
        // $valores = array(0,3,2,12,0);
        CampoSelect('fl_users', $options, $valores, 'AU');
        ?>
        </div>      
      </div>
      <div class='col-xs-12 col-sm-7 col-md-7 col-lg-3'>
        <div class='form-group'>
        <?php
        # Por defaul ocultamos este filtro
        $opt_status = array(ObtenEtiqueta(1040), ObtenEtiqueta(1041), ObtenEtiqueta(1042));
        $val_status = array('ALL',1,0);
        CampoSelect('fl_status', $opt_status, $val_status, 2, 'select2');
        ?>
        </div>      
      </div>      
      <?php      
      echo PresentaContentTopAdm($fl_usuario, "col-xs-12 col-sm-6 col-md-6 col-lg-6"); 
      ?>
    </div>
    <?php      
      SectionIni();
          # Valores para el boton de actions
  		$opt_btn = array();
  		$desc_btn = array();
  		$val_btn = array();
  		# Show or Hide Add/Import Students
  		if ($fl_perfil==25 || $fg_addStudents==1) {
        # This commented because Admins don't need to add or import students
        # commented ON 2020-03-16 by Marios instructions
        // array_push($opt_btn, ObtenEtiqueta(1043), ObtenEtiqueta(1044));
        // array_push($desc_btn, ObtenEtiqueta(1116), ObtenEtiqueta(1117));
        // array_push($val_btn, ADD_STD, IMP_STD);
    	} elseif ($fl_perfil==13 && $re_addStudents==1 && $fg_addStudents==0) {
        array_push($opt_btn, ObtenEtiqueta(1043), ObtenEtiqueta(1044));
        array_push($desc_btn, ObtenEtiqueta(1116), ObtenEtiqueta(1117));
        array_push($val_btn, ADD_STD, IMP_STD);
      } elseif ($fl_perfil==13 && $re_exist==false) {
        array_push($opt_btn, ObtenEtiqueta(1043), ObtenEtiqueta(1044));
        array_push($desc_btn, ObtenEtiqueta(1116), ObtenEtiqueta(1117));
        array_push($val_btn, ADD_STD, IMP_STD);
      }
    	# Show or Hide Add/Import Teachers
    	if ($fl_perfil==25 || $fg_addTeachers==1) {
    		array_push($opt_btn, ObtenEtiqueta(1045), ObtenEtiqueta(1046));
    		array_push($desc_btn, ObtenEtiqueta(1118), ObtenEtiqueta(1119));
    		array_push($val_btn, ADD_MAE, IMP_MAE);
    	} elseif ($fl_perfil==13 && $re_addStudents==1 && $fg_addTeachers==0) {
        array_push($opt_btn, ObtenEtiqueta(1045), ObtenEtiqueta(1046));
        array_push($desc_btn, ObtenEtiqueta(1118), ObtenEtiqueta(1119));
        array_push($val_btn, ADD_MAE, IMP_MAE);
      } elseif ($fl_perfil==13 && $re_exist==false) {
        array_push($opt_btn, ObtenEtiqueta(1045), ObtenEtiqueta(1046));
        array_push($desc_btn, ObtenEtiqueta(1118), ObtenEtiqueta(1119));
        array_push($val_btn, ADD_MAE, IMP_MAE);
      }
    	# This are the acions for Activate and deactivate users
    	array_push($opt_btn, ObtenEtiqueta(1047), ObtenEtiqueta(1048));
    	array_push($desc_btn, ObtenEtiqueta(1120), ObtenEtiqueta(1121));
    	array_push($val_btn, ACTIVE, DESACTIVE);
    	# Show or Hide Delete users
    	if ($fl_perfil==25 || $fg_deletions==1) {
    		array_push($opt_btn, ObtenEtiqueta(1049));
    		array_push($desc_btn, ObtenEtiqueta(1122));
    		array_push($val_btn, DELETE);
    	} elseif ($fl_perfil==13 && $re_addStudents==1 && $fg_deletions==0) {
        array_push($opt_btn, ObtenEtiqueta(1049));
        array_push($desc_btn, ObtenEtiqueta(1122));
        array_push($val_btn, DELETE);
      } elseif ($fl_perfil==13 && $re_exist==false) {
        array_push($opt_btn, ObtenEtiqueta(1049));
        array_push($desc_btn, ObtenEtiqueta(1122));
        array_push($val_btn, DELETE);
      }
      
    	###############################################################
    	// Commented for the use of above method to use privacy settings to hide and show actions
    	// Bellow commented for the use of conditionals above to hide or show actions
      // $opt_btn = array(/*ObtenEtiqueta(1043), ObtenEtiqueta(1044),*/ ObtenEtiqueta(1045), ObtenEtiqueta(1046), ObtenEtiqueta(1047), ObtenEtiqueta(1048), ObtenEtiqueta(1049));
      // $desc_btn = array(/*ObtenEtiqueta(1116), ObtenEtiqueta(1117),*/ ObtenEtiqueta(1118), ObtenEtiqueta(1119), ObtenEtiqueta(1120), ObtenEtiqueta(1121), ObtenEtiqueta(1122));
      // $val_btn = array(/*ADD_STD,IMP_STD,*/ADD_MAE,IMP_MAE,ACTIVE,DESACTIVE,DELETE); 
      ###############################################################       
      ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "gabriel", "fa-table", $userName, true, true, false, false, false, ObtenEtiqueta(1074), "default", $opt_btn, $val_btn, $desc_btn);
      # Muestra Inicio de la tabla
      $titulos = array(ObtenEtiqueta(1054), ObtenEtiqueta(1055),ObtenEtiqueta(1640), ObtenEtiqueta(1076), ObtenEtiqueta(1056),  ObtenEtiqueta(1057), ObtenEtiqueta(1106), ObtenEtiqueta(1058), ObtenEtiqueta(1059));
      MuestraTablaIni("tbl_users", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos);
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
	    var table = $('#tbl_users').on( 'processing.dt', function ( e, settings, processing ) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        }).DataTable( {
	        "ajax": "<?php echo $ajaxUrl; ?>",
	        "bDestroy": true,
	        "iDisplayLength": 15,
	        "columns": [
	            { "data": "checkbox", "width":"10px", "orderable":false },
	            { "data": "id", "width":"15px", "orderable":false},
	            { "data": "name" },
				{ "data": "grade" },
            	{ "data": "programa", "visible":"<?php echo $visible; ?>"},
	            { "data": "perfil"},	            
	            { "data": "status" },
	            { "data": "use_licence" },
	            { "data": "lastlogin" },
	            { "data": "usage" },
	            { "data": "blank", "visible":false },
	            /*{ "data": "perfil_status_search_todos", "visible": false},*/
	            { "data": "perfil_search_st_te", "visible": false },
	            { "data": "status_search_st_te", "visible": false },
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
