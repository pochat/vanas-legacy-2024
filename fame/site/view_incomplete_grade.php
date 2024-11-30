  

    
   <style>
	div.dataTables_filter label {
		float: left !important;
    }

   </style>
   
   
  <div class="row">
   <div class="col-md-12">
   <?php      
      SectionIni();
        # Valores para el boton de actions
        $opt_btn = array(ObtenEtiqueta(1043), ObtenEtiqueta(1044), ObtenEtiqueta(1045), ObtenEtiqueta(1046), ObtenEtiqueta(1071), ObtenEtiqueta(1072), ObtenEtiqueta(1073), 
        ObtenEtiqueta(1047), ObtenEtiqueta(1048), ObtenEtiqueta(1049), ObtenEtiqueta(1908), ObtenEtiqueta(1815));
       
        $desc_btn = array(ObtenEtiqueta(1116), ObtenEtiqueta(1117), ObtenEtiqueta(1118), ObtenEtiqueta(1119), ObtenEtiqueta(1905), ObtenEtiqueta(1906), ObtenEtiqueta(1907), 
        ObtenEtiqueta(1120), ObtenEtiqueta(1121), ObtenEtiqueta(1122), ObtenEtiqueta(1909), ObtenEtiqueta(1816));
        $val_btn ="";# array(ADD_STD, IMP_STD, ADD_MAE, IMP_MAE, ASG_GROUP, ASG_COURSE, DESASIGNAR_COURSE, ACTIVE, DESACTIVE, DELETE, ASSESSMENT, ASSIGN_MYSELF);
        ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "students", "fa-table", ObtenEtiqueta(1921), true, true, false, false, false, ObtenEtiqueta(1074), "default", $opt_btn, $val_btn, $desc_btn);
          # Muestra Inicio de la tabla
          $titulos = array( ObtenEtiqueta(1795),  ObtenEtiqueta(1796), ObtenEtiqueta(1797), ObtenEtiqueta(1798),ObtenEtiqueta(1799));
          MuestraTablaIni("tbl_incomplete", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos,false);          
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
 
 
  <script type="text/javascript">
	

  /* Debemos agregarlo para el fucnionamiento de diversos  plugins*/
	pageSetUp();
	
  /** INICIO DE SCRIPT PARA DATATABLE **/
	var pagefunction = function() {
   
		/* Formatting function for row details - modify as you need */
		function format ( d ) {
		    // `d` is the original data object for the row
        return d;
		}

		// clears the variable if left blank
		var table = $('#tbl_incomplete').on('processing.dt', function (e, settings, processing) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        }).DataTable( {
	        "ajax": "Querys/incomplete_grade.php",
	        "bDestroy": true,
			"stateSave": true,
	        "iDisplayLength": 10,
	        "scrollX": true,
	        "columns": [
	           
	            { "data": "id",  "width":"40px", "orderable": false },
	            { "data": "name", "orderable": false ,"className": "text-align-center"},
	            { "data": "myself", "orderable": false, "className": "text-align-left" },
                { "data": "trabajos" },
                { "data": "asigment_grade" },
				{ "data": "nada" },
         	            
	        ],
	        "order": [[5, 'DESC']],
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
		$('#tbl_incomplete tbody').on('click', 'td.details-control', function () {
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
  

	function AsignarCalificacion(fl_alumno, fl_leccion_sp, fl_programa_sp, no_semana, fl_entrega_semanal_sp, fl_teac) {

	    $('#presenta_calificacion').empty();
	    $('#tab_3').removeClass('hidden');
	    $('#tab_0').removeClass('active');
	    $('#tab_1').removeClass('active');
	    $('#tab_2').removeClass('active');
	    $('#tab_3').addClass('active');

	    $('#p_grade').removeClass('active');
	    $('#p_incomplete').removeClass('active');
	    $('#p_history').removeClass('active');
	    $('#p_assignment_grade').addClass('active');


	    var fl_alumno = fl_alumno;
	    var fl_leccion_sp = fl_leccion_sp;
	    var fl_programa_sp = fl_programa_sp;
	  
	    var no_semana = no_semana;
	    var fl_entrega_semanal_sp = fl_entrega_semanal_sp;


	        $.ajax({
	            type: 'POST',
	            url: 'site/presenta_rubric.php',
	            data: 'fl_alumno='+fl_alumno+
                       '&fl_leccion_sp='+fl_leccion_sp+
                       '&no_semana='+no_semana+
                       '&fl_entrega_semanal_sp='+fl_entrega_semanal_sp+
                       '&fl_programa_sp='+fl_programa_sp+
                       '&fl_teacher='+fl_teac,

	            async: false,
	            success: function (html) {
	                $('#presenta_calificacion').html(html);

	            }
	        });
	}


  
    function ViewComent(fl_entregable){
	
	 var fl_entregable=fl_entregable;
	  
			//alert(fl_entrega_semanal_sp);
               
			    $.ajax({
	            type: 'POST',
	            url: 'site/view_coments.php',
	            data: 'fl_entregable='+fl_entregable,
	            async: false,
	            success: function (html) {
						$('#view').html(html);

						}
				});
			   
				
	
	
	
	}
  
  function ResetFilter(){
	   
	    $("#tbl_incomplete").DataTable().search("").draw();  //limpiar el restet
	   
	   
   }
  
  

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


<script>

    var button = '<a href="javascript:void(0);" id="btn_reset_filter" onclick="ResetFilter();" class="btn btn-default btn-xs" style="margin-left:5px;margin-bottom:5px;margin-top: 5px;"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp; Reset search</a>';
    $('#tbl_incomplete_filter').append(button);

</script>
