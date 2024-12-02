    
    <!--Div para envio de los correos-->
    <div style="display: none;" class="modal fade in" id="dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <a href="javascript:cerrar();" class="close">
            <i class="fa fa-close"></i>
            </a>
            <h4 class="modal-title" id="myModalLabel">Send Email</h4>
          </div>
          <div class="modal-body" style="overflow: scroll; height:450px;">
            <div class="row" id="select_modal">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="category"><?php echo ObtenEtiqueta(153); ?>:</label>
                  <?php
                  $Query = "SELECT nb_template, fl_template FROM k_template_doc a ";
                  $Query .= "WHERE fl_categoria=2 ORDER BY fl_template ASC";
                  $rs = EjecutaQuery($Query);
                  echo "
                  <select class='select2' name='fl_template' id='fl_template'>
                    <option value='0'>".ObtenEtiqueta(70)."</option>";
                  for($i=0;$row=RecuperaRegistro($rs);$i++){
                    echo "
                    <option value='".$row[1]."'>";
					if($row[1]==52)
						echo"1.-";
					if($row[1]==53)
						echo"3.-";
					if($row[1]==54)
						echo"2.-";					
					echo"".$row[0]."";
					echo"</option>";
                  }
                  ?>
                  </select>
                </div>
              </div>              
            </div>
            <div id='ds_mensaje'></div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div>
    </div>
    <!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<script data-pace-options='{ "restartOnRequestAfter": true }' src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/pace/pace.min.js"></script>

		<!-- These scripts will be located in Header So we can add scripts inside body (used in class.datatables.php) -->
		<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
    <!-- Originalmente se utlizaba la version 2.0.2 pero en la seleccion multiple no realizaba el checked -->		
    <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>-->
    <script src="<?php echo PATH_HOME; ?>/bootstrap/js/jquery.min.js"></script>
		<script>
			if (!window.jQuery) {
				document.write('<script src="<?php  echo PATH_HOME; ?>/bootstrap/js/libs/jquery-2.0.2.min.js"><\/script>');
			}
		</script>

		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script>
			if (!window.jQuery.ui) {
				document.write('<script src="<?php echo PATH_HOME; ?>/bootstrap/js/libs/jquery-ui-1.10.3.min.js"><\/script>');
			}
		</script>

		<!-- IMPORTANT: APP CONFIG -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/app.config.js"></script>

		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> 

		<!-- BOOTSTRAP JS -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/bootstrap/bootstrap.min.js"></script>

		<!-- CUSTOM NOTIFICATION -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/notification/SmartNotification.min.js"></script>

		<!-- JARVIS WIDGETS -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/smartwidgets/jarvis.widget.min.js"></script>

		<!-- EASY PIE CHARTS -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

		<!-- SPARKLINES -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/sparkline/jquery.sparkline.min.js"></script>

		<!-- JQUERY VALIDATE -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/jquery-validate/jquery.validate.min.js"></script>

		<!-- JQUERY MASKED INPUT -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/masked-input/jquery.maskedinput.min.js"></script>

		<!-- JQUERY SELECT2 INPUT -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/select2/select2.min.js"></script>

		<!-- JQUERY UI + Bootstrap Slider -->
	<!--	<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>--->

		<!-- browser msie issue fix -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

		<!-- FastClick: For mobile devices -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/fastclick/fastclick.min.js"></script>

		<!--[if IE 8]>
			<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
		<![endif]-->

		<!-- Demo purpose only -->
		<!--<script src="<?php echo PATH_HOME; ?>/bootstrap/js/demo.min.js"></script>-->
    <!-- Boton para beta vanas-->
    <script>
    $(document).ready(function(){
      // Agregamos la url y el nombre la funcion
      var ruta = '<?php echo '/admin/modules'; ?>';
      var programa_act =  $('#programa_act').val();
      if(programa_act == 0)
        programa_act = "/admin/home.php";
      else
        programa_act = ruta + $('#programa_act').val();
      
      $('#vanas_v1').attr('href',programa_act);
      $('#vanas_v1').html('<?php echo ObtenEtiqueta(874); ?>');
    });
    </script>
		<!-- MAIN APP JS FILE -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/app.min.js"></script>		

		<!-- ENHANCEMENT PLUGINS : NOT A REQUIREMENT -->
		<!-- Voice command : plugin -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/speech/voicecommand.min.js"></script>	

		<!-- SmartChat UI : plugin -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/smart-chat-ui/smart.chat.ui.min.js"></script>
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/smart-chat-ui/smart.chat.manager.min.js"></script>
    
    <!-- PAGE RELATED PLUGIN(S) -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/jquery.dataTables.min.js"></script>
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/dataTables.colVis.min.js"></script>
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/dataTables.tableTools.min.js"></script>
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/dataTables.bootstrap.min.js"></script>
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatable-responsive/datatables.responsive.min.js"></script>
    
    <!-- ICH Nov16: Librerias para tags Self Paced - Course Library -->
    <link  href="<?php echo PATH_LIB; ?>/fame/tags/master_tag.css" rel="stylesheet" type="text/css">
    <script src="<?php echo PATH_LIB; ?>/fame/tags/tag-it.js" type="text/javascript" charset="utf-8"></script>
    <link href="<?php echo PATH_LIB; ?>/fame/tags/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">
    <!-- Full Calendar -->
	<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/fullcalendar/jquery.fullcalendar.min.js"></script>
    
		<script type="text/javascript">
			// DO NOT REMOVE : GLOBAL FUNCTIONS!
			$(document).ready(function() {
      pageSetUp();
        
				var responsiveHelper_dt_basic = undefined;
				var responsiveHelper_datatable_fixed_column = undefined;
				var responsiveHelper_datatable_col_reorder = undefined;
				var responsiveHelper_datatable_tabletools = undefined;
				
				var breakpointDefinition = {
					tablet : 1024,
					phone : 480
				};
        setup_widgets_desktop();
        
        
        /* TABLE BASIC */
        $('#dt_basic').dataTable({
					/*"sDom": "<'dt-toolbar hidden-xs'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>"+
						"t"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",*/
					"autoWidth" : true,
          "bSort":false,
          "searching": false,
          "lengthChange":false,
          "lengthMenu": [[5, 10, 20, "All"] ],
          "pageLength": 50,
					"preDrawCallback" : function() {
						// Initialize the responsive datatables helper once.
						if (!responsiveHelper_dt_basic) {
							responsiveHelper_dt_basic = new ResponsiveDatatablesHelper($('#dt_basic'), breakpointDefinition);
						}
					},
					"rowCallback" : function(nRow) {
						responsiveHelper_dt_basic.createExpandIcon(nRow);
					},
					"drawCallback" : function(oSettings) {
						responsiveHelper_dt_basic.respond();
					}
				});
        /* END TABLE BASIC */
        
        /* COLUMN FILTER  */
        //var url = '@Url.Action("GetJsonData", "Home")';
		    var otable = $('#datatable_fixed_column').on( 'processing.dt', function ( e, settings, processing ) {
        $('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
        $("#vanas_loader").show();
        if(processing == false)
          $("vanas_loader").hide();
        // alert(processing);
        } ).DataTable({
          //"serverSide": true,
          "processing": true,
          /*"ajax": {
            "url": url,
            "type": "POST"
          },*/
		    	//"autoWidth" : true, 
          //"fixedColumns": true,
          //"bSort":false,
          //"searching": true,
          "lengthChange":true,
          "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
          "pageLength": 20,
          "paging": true,
          "info":true,
          //"order":false,
          /*"sDom": "<'dt-toolbar'<'col-xs-12 col-sm-3'f><'toolbar col-xs-12 col-sm-8'r><'col-xs-12 col-sm-1'T>>"+
						"t"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",*/
          "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'T>>"+
						"t"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
          "oTableTools": {
            "aButtons": [{
                "sExtends": "csv",
                "sTitle": "<?php echo ObtenProgramaActual().date('Ymd')."_".rand(1000,9000); ?>",
            },],
            "sSwfPath": "<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/swf/copy_csv_xls_pdf.swf"
          },		       
				"preDrawCallback" : function() {
					// Initialize the responsive datatables helper once.
					if (!responsiveHelper_datatable_fixed_column) {
						responsiveHelper_datatable_fixed_column = new ResponsiveDatatablesHelper($('#datatable_fixed_column'), breakpointDefinition);
					}
				},
				"rowCallback" : function(nRow) {
					responsiveHelper_datatable_fixed_column.createExpandIcon(nRow);
				},
				"drawCallback" : function(oSettings) {
					responsiveHelper_datatable_fixed_column.respond();
				}					
		    });
		    
		    // Custom toolbar con este colocamos los botones de letter y enroll
        // En el ecabezado de la tabla applcations y students
        var btn_multiple = $("#btn_multi").html();
		    $("div.toolbar").html( btn_multiple);		    

		    // Apply the filter
		    /*$("#datatable_fixed_column thead th input[type=text]").on( 'keyup change', function () {		    	
          otable
          .column( $(this).parent().index()+':visible' )
          .search( this.value )
          .draw();		            
		    } );*/
		    /* END COLUMN FILTER */
        
        
		})
    $( '#checkbox' ).on( 'click', function() {
        if( $(this).is(':checked') ){
            // Hacer algo si el checkbox ha sido seleccionado
            alert("El checkbox con valor " + $(this).val() + " ha sido seleccionado");
            $('#checkbox1').attr('checked', true);
        } else {
            // Hacer algo si el checkbox ha sido deseleccionado
            alert("El checkbox con valor " + $(this).val() + " ha sido deseleccionado");
            $('#checkbox1').attr('checked', false);
        }
    });
    // enlances
    $("a").mouseover(function(){
        $("i.fa fa-file-pdf-o").css("background-color", "yellow");
    });
    
    // Validaciones del alias
    function ChangeAlias(user) {
        var x = document.getElementById("ds_alias");
        var val = x.value;
        var user = user;

        if(val.length>0){
          $.ajax({
            type: "POST",
            dataType: 'json',
            url: "valida_alias.php",
            async: false,
            data: "ds_alias="+val+
                  "&fl_usuario="+user,        
            success: function(result){
              var error = result.resultado.fg_error;
              if(error==true && val.length>0){
                document.getElementById("ds_alias").style.borderColor = "red";
                document.getElementById("ds_alias").style.background = "#fff0f0";  
                $("#ds_alias_err").remove();                
                $("#ds_alias").after("<p id='ds_alias_err' class='form-control-static text-danger'><?php echo ObtenEtiqueta(2011); ?></p>");
              }
              else{
                document.getElementById("ds_alias").style.borderColor = "#739e73";
                document.getElementById("ds_alias").style.background = "#f0fff0";
                
                $("#ds_alias_err").remove();
              }
              // ValidaInfo();
            }
          });
        }
        else{
          document.getElementById("ds_alias").style.borderColor = "red";
          document.getElementById("ds_alias").style.background = "#fff0f0";  
          $("#ds_alias_err").remove();                
          $("#ds_alias").after("<p id='ds_alias_err' class='form-control-static text-danger'><?php echo  ObtenMensaje(ERR_REQUERIDO); ?></p>");          
        }
        
    }
    // Campos no ingresa espacios
    function validarnspace(e) { // 1
       tecla = (document.all) ? e.keyCode : e.which; // 2
         if (tecla == 8) return true; // 3
         if (tecla == 32) return false;
         if (tecla == 9) return true; // 3
         if (tecla == 11) return true; // 3
         patron = /[- 0-9 A-Za-zÃ±Ã‘'Ã¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÃ Ã¨Ã¬Ã²Ã¹Ã€ÃˆÃŒÃ’Ã™Ã¢ÃªÃ®Ã´Ã»Ã‚ÃŠÃŽÃ”Ã›Ã‘Ã±Ã¤Ã«Ã¯Ã¶Ã¼Ã„Ã‹ÃÃ–Ãœ\s\t]/; // 4

         te = String.fromCharCode(tecla); // 5
         return patron.test(te); // 6
    }
    </script>    
