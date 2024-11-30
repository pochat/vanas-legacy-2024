<?php

$content = 
"
<link rel='stylesheet' type='text/css' media='screen' href='".PATH_N_COM_CSS."/lib_css_original/smartadmin-production-plugins.min.css'>
 <style>
    div.dataTables_wrapper  {
        top: 44px !important;
    }
      .zoomimg span img {
	border-width: 0;
	padding: 2px;
	width: auto;
	height: auto;
																					
	}
   .dt-toolbar-footer {
    border: solid 1px #d4d3d3 !important;
    padding: 4px !important;
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
					<!--<h2>".ObtenEtiqueta(2226)."</h2>-->
          <div class='widget-toolbar'>						
            <!--<button class='btn dropdown-toggle btn-xs btn-primary' onclick='frm_file(".$fl_alumno.", ".$fl_leccion_sp.", ".$fl_usuario.")'>
              <i class='fa fa-check-circle'></i> ".ObtenEtiqueta(2216)."
            </button>-->
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
                  <th data-hide='phone'>".ObtenEtiqueta(2363)."</th>
                  <th data-class='expand'>".ObtenEtiqueta(2360)."</th>
                  <th data-hide='phone'>".ObtenEtiqueta(2361)."</th>
                  <th data-hide='phone, table'> </th>
                  <th data-hide='phone, table'>".ObtenEtiqueta(2362)."</th>
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
  
  var workings = function(){
    var responsiveHelper_dt_basic = undefined;
    var responsiveHelper_datatable_fixed_column = undefined;
    var responsiveHelper_datatable_col_reorder = undefined;
    var responsiveHelper_datatable_tabletools = undefined;
    
    var breakpointDefinition = {
      tablet : 1024,
      phone : 480
    };

    var tbworks = $('#dt_works').dataTable({
      'ajax': {
         'url': 'Querys/student_library.php',
         'data': {
		   'fl_programa_sp':".$fl_programa.",
           'fl_leccion_sp': ".$fl_leccion_sp.",
           'fl_alumno': ".$fl_alumno.",
          }
       },
      'bDestroy': true,
      'columns': [
          { 'data': 'name','class':'text-align-center'},
          { 'data': 'version','class':'text-align-center'},
          { 'data': 'descr','class':'text-align-center'} , 
          { 'data': 'tipo_archivo','class':'text-align-center'} ,
          { 'data': 'date','class':'text-align-center'},  
          { 'data': 'btns', 'class':'text-align-center', 'width': '10%' } ,          	            
      ],
      'autoWidth' : true,
      'order': [[4, 'desc']],
      'fnDrawCallback': function( oSettings ) {
        /** Se tuiliza para el nombre de las imagenes **/
        $('[rel=tooltip]').tooltip();
           // zoom thumbnails and add bootstrap popovers
	        // https://getbootstrap.com/javascript/#popovers
	        $('[data-toggle=\"popover\"]').popover({
	        container: 'body',
	        html: true,
	        placement: 'auto',
	        trigger: 'hover',
	        content: function() {
		        // get the url for the full size img
		        var url = $(this).data('full');
		        return '<img src=\"'+url+'\" style=\"max-width:250px;\">'
	        }
	        }); 


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
  
  }
  
  	// load related plugins & run pagefunction
  /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/      
  /** IMPORTANTES Y NECESARIAS EN DONDE SE UTILIZEN **/
	loadScript('".PATH_SELF_JS."/plugin/datatables/jquery.dataTables.min.js', function(){
		loadScript('".PATH_SELF_JS."/plugin/datatables/dataTables.colVis.min.js', function(){
			loadScript('".PATH_SELF_JS."/plugin/datatables/dataTables.tableTools.min.js', function(){
				loadScript('".PATH_SELF_JS."/plugin/datatables/dataTables.bootstrap.min.js', function(){
					loadScript('".PATH_SELF_JS."/plugin/datatable-responsive/datatables.responsive.min.js', workings)
				});
			});
		});
	});
  /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/   
  
  function frm_file(alu,lecc, usu_uplo){
    // $('.modal-content').empty();
    var ele = $('#modal-frm-files').modal('toggle');   
    $.ajax({
      type: 'POST',
      url: 'site/upload_files.php',
      data: 'fl_alumno='+alu+'&fl_leccion_sp='+lecc+'&fg_fame=1&fg_accion=1&fl_usu_upload='+usu_uplo,
      success: function(html){
          $('#frm-files').append(html);
      }
    });
    
  }
  
  function del_file(alu,lecc, work){
    var conf = confirm('".ObtenEtiqueta(2230)."');
    if(conf==true){
     $.ajax({
        type: 'POST',
        url: 'site/upload_files.php',
        data: 'fl_alumno='+alu+'&fl_leccion_sp='+lecc+'&fg_fame=1&fg_accion=3&fl_worksfiles='+work,
      }).done(function(result){
        var result = JSON.parse(result);
        var success = result.success;
        if(success==false){
          $('#dt_works').DataTable().ajax.reload();
        }
        
      });
    }
  }





</script><br><br><br>";

$content .="  

            <div class='modal fade' id='myModalVideosStudentLibrary' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='static' data-keyboard='false'   >
	            <div class='modal-dialog'>
		            <div class='modal-content' id='modal_videos_library'>
                    </div>
	            </div>
            </div>
        <div class='row'>
           <div class='col-md-12'>
                <div class='panel panel-default'>  
                    <div class='panel-body'>
                    
                    
         ";

#Recuperamos datos de videos del estudent library.
$Queryx ="SELECT a.fl_video_contenido_sp,b.fl_vid_contet_temp,a.cl_pagina_sp, a.ds_progreso,nb_archivo,a.fl_programa_sp,a.ds_title_vid,no_orden,a.fe_creacion, ds_duracion,b.fl_usuario 
                FROM k_video_contenido_sp a
                JOIN k_vid_content_temp b ON a.fl_vid_contet_temp =b.fl_vid_contet_temp
                WHERE cl_pagina_sp=$fl_programa AND fl_programa_sp=$fl_programa 
                ORDER BY b.fl_vid_contet_temp DESC "; 
$rsx = EjecutaQuery($Queryx);
$tot_reg = CuentaRegistros($rsx);

for($i=0;$rowx=RecuperaRegistro($rsx);$i++){
    $fl_video_contenido_sp=$rowx['fl_video_contenido_sp'];
    $fl_vid_contet_temp=$rowx['fl_vid_contet_temp'];
    $no_orden=$rowx['no_orden'];  
    $fl_usuario_progra=$rowx['fl_usuario'];
    $fl_instituto = ObtenInstituto($fl_usuario_progra);
    $fe_creacion=$rowx['fe_creacion'];
    $ds_duracion=$rowx['ds_duracion'];
    $ds_titulo=$rowx['ds_title_vid'];

    #Damos formato de fecha
    $p_fecha=strtotime('+0 day',strtotime($fe_creacion));
    $p_fecha= date('Y-m-d H:i:s',$p_fecha);
    $date = date_create($p_fecha);
    $fecha=date_format($date,'l F j, Y g:i a');

   
    $content .="
          
                <div class='col-md-3 padding-top-15'>
                        <p class='text-left'><strong>".ObtenEtiqueta(1759).":</strong><br><small>$fecha</small></p>
			            <p class='text-left'><strong>".ObtenEtiqueta(2360).":</strong><br><small>$ds_titulo</small></p>

                        <div>
			                <a onclick='MuestraVideos_$fl_video_contenido_sp($fl_video_contenido_sp,$fl_vid_contet_temp);' data-toggle='modal' data-target='#myModalVideosStudentLibrary'>
					                <img src='".ObtenConfiguracion(116)."/fame/site/uploads/".$fl_instituto."/attachments/student_library/video_".$fl_video_contenido_sp."/video_".$no_orden."/img_1.png' style='height: 141px;' class='superbox-img'>
			                </a>
                            <span class='label label-info bg-color-darken pull-right' style='position:relative;bottom:30px;right:5px;font-size:12px;display:inline' >$ds_duracion</span>
                
                        </div>
                        <script>
                            function MuestraVideos_$fl_video_contenido_sp(fl_video_contenido_sp,fl_vid_contet_temp){

		                             $.ajax({
                                         type: 'POST',
                                         url: 'site/show_video_student_library.php',
                                         data: 'fl_vid_contet_temp='+fl_vid_contet_temp+
                                               '&fl_usuario=$fl_usuario_progra'+
			                                   '&fl_video_contenido_sp='+fl_video_contenido_sp,
                                         async: true,
                                         success: function (html) {
                                             $('#modal_videos_library').html(html);
                                         }
                                     });	
	                            }

                        </script>
                </div>    

    ";
} 
$content.="

                </div>
             </div>
          </div>


   </div>

    


";

$result['rs'] = array(
"content" => $content
);
echo json_encode((Object) $result);
?>