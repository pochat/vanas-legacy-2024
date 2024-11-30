<?php

$content = 
"
<link rel='stylesheet' type='text/css' media='screen' href='".PATH_N_COM_CSS."/lib_css_original/smartadmin-production-plugins.min.css'>
 <style>
    div.dataTables_wrapper  {
        top: 44px !important;
    }
</style>
<!-- widget grid -->
<section id='widget-grid' class=''>
	<!-- row -->
	<div class='row'>
    <!-- NEW WIDGET START -->
		<article class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
      <!-- Widget ID (each widget will need unique ID)-->
			<div class='jarviswidget' id='wid-id-list' role='widget' 
      data-widget-colorbutton='false'
      data-widget-editbutton='false'
      data-widget-togglebutton='false'
      data-widget-deletebutton='false'
      data-widget-fullscreenbutton='false'
      data-widget-fullscreenbutton='false'
      data-widget-custombutton='false'
      data-widget-collapsed='false'
      >
				<header>
					<span class='widget-icon'> <i class='fa fa-files-o'></i> </span>
					<h2>".ObtenEtiqueta(2222)."</h2>
          <div class='widget-toolbar'>						
            <button class='btn dropdown-toggle btn-xs btn-primary' onclick='frm_file(".$fl_alumno.", ".$fl_leccion.", ".$fl_usuario.")'>
              <i class='fa fa-check-circle'></i> ".ObtenEtiqueta(2209)."
            </button>
					</div>
				</header>
				<!-- widget div-->
				<div>

					<!-- widget edit box -->
					<div class='jarviswidget-editbox'>
						<!-- This area used as dropdown edit box -->

					</div>
					<!-- end widget edit box -->

					<!-- widget content -->
					<div class='widget-body no-padding'>
            <table id='dt_works' class='table table-striped table-bordered table-hover' width='100%'>
              <thead>
                <tr>
                  <th data-hide='phone'>".ObtenEtiqueta(2210)."</th>
                  <th data-class='expand'>".ObtenEtiqueta(2211)."</th>
                  <th data-hide='phone'>".ObtenEtiqueta(2212)."</th>
                  <th data-hide='phone, table'>".ObtenEtiqueta(2213)."</th>
                  <th data-hide='phone, table'>".ObtenEtiqueta(2223)."</th>
                  <th data-hide='phone, table'>".ObtenEtiqueta(2214)."</th>
                  <th data-hide='phone'>&nbsp;</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </article>
  </div>
</section>

<!-- Modal for old campus -->
<div class='modal fade' id='modal-frm-files' tabindex='-1' role='dialog' aria-labelledby='item-title' aria-hidden='true'>
  <div class='modal-dialog'>
  	<div class='modal-content' id='frm-files'></div>
  </div>
</div>

<script>
  pageSetUp();  
  var responsiveHelper_dt_basic = undefined;
  var responsiveHelper_datatable_fixed_column = undefined;
  var responsiveHelper_datatable_col_reorder = undefined;
  var responsiveHelper_datatable_tabletools = undefined;
  
  var breakpointDefinition = {
    tablet : 1024,
    phone : 480
  };

  $('#dt_works').dataTable({
    'ajax': {
       'url': 'ajax/works_files.php',
       'data': {
         'fl_leccion': ".$fl_leccion.",
         'fl_alumno': ".$fl_alumno.",
         'fl_teacher': ".$teacher_std."
        }
     },
    'bDestroy': true,
    'columns': [
        { 'data': 'id', 'class':'text-align-center'},
        { 'data': 'name'},
        { 'data': 'version'},
        { 'data': 'descr'} ,          	            
        { 'data': 'user'} ,          	            
        { 'data': 'date', 'class':'text-align-center'} ,          	            
        { 'data': 'btns', 'class':'text-align-center', 'width': '10%' } ,          	            
    ],
    'autoWidth' : true,
    'fnDrawCallback': function( oSettings ) {
      /** Se tuiliza para el nombre de las imagenes **/
      $('[rel=tooltip]').tooltip();
    },
    'preDrawCallback' : function() {
				// Initialize the responsive datatables helper once.
				if (!responsiveHelper_datatable_tabletools) {
					responsiveHelper_datatable_tabletools = new ResponsiveDatatablesHelper($('#dt_works'), breakpointDefinition);
				}
			},
    'rowCallback' : function(nRow) {
      responsiveHelper_datatable_tabletools.createExpandIcon(nRow);
    },
    'drawCallback' : function(oSettings) {
      responsiveHelper_datatable_tabletools.respond();
    }
  });
  
  function frm_file(alu,lecc, usu_upload){
    // $('.modal-content').empty();
    var ele = $('#modal-frm-files').modal('toggle');   
    $.ajax({
      type: 'POST',
      url: '".PATH_N_ALU_PAGES."/upload_files.php',
      data: 'fl_alumno='+alu+'&fl_leccion='+lecc+'&fg_fame=0&fg_accion=1&fl_usu_upload='+usu_upload,
      success: function(html){
          $('#frm-files').append(html);
      }
    });
    
  }
  
  function del_file(alu,lecc, work, tea){
    var conf = confirm('".ObtenEtiqueta(2230)."');
    if(conf==true){
     $.ajax({
        type: 'POST',
        url: '".PATH_N_ALU_PAGES."/upload_files.php',
        data: 'fl_alumno='+alu+'&fl_leccion='+lecc+'&fg_fame=0&fg_accion=3&fl_worksfiles='+work+'&fl_teacher='+tea,
      }).done(function(result){
        var result = JSON.parse(result);
        var success = result.success;
        if(success==true){
          $('#dt_works').DataTable().ajax.reload();
        }
        
      });
    }
  }

</script>";
 

$result['rs'] = array(
"content" => $content
);
echo json_encode((Object) $result);
?>