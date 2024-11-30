<?php

  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");

  #Recibimos parametros
  $fl_entregable=RecibeParametroNumerico('fl_entregable');
  $fl_alumno=RecibeParametroNumerico('fl_alumno');
  $fl_leccion = RecibeParametroNumerico('fl_leccion');

  

  if(empty($fl_leccion)){
    #Recuperamos los trabajos.
    $Query="SELECT fg_tipo,ds_ruta_entregable,ds_comentario,fe_entregado FROM k_entregable WHERE fl_entregable=$fl_entregable ";
    $row=RecuperaValor($Query);
    $fg_tipo=$row[0];
    $nb_archivo=$row[1];
    $ds_comentarios=str_texto($row[2]);
    $fe_entregado= ObtenEtiqueta(1677)." : ".ObtenFechaFormatoDiaMesAnioHora($row[3]);

    $ext = strtolower(ObtenExtensionArchivo($nb_archivo));


    if(($ext == "ogg")||($ext=='mp4')||($ext=='mov')||($ext=='m3u8')){
      #Rutas del archivo.
        $ruta_video = PATH_CAMPUS."/students/videos/$nb_archivo";
    }else{
        $ruta_thumbs = PATH_CAMPUS."/students/sketches/board_thumbs/$nb_archivo";
     // $ruta_board_thumbs = PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_alumno."/sketches/board_thumbs/$nb_archivo";
      $ruta_original= PATH_CAMPUS."/students/sketches/original/$nb_archivo";
    }


    $contenido = "";
    # Muestra el trabajo videoo imagen
    if(($ext == "ogg")||($ext=='mp4')||($ext=='mov')||($ext=='m3u8')){	
      $contenido .= "
      <video class='center' controls='controls' style='width:100%!important;'>
        <source src='$ruta_video' type='video/mp4'>
		<source src='$ruta_video' type='video/ogg'>
      </video>";
    }
    else{
      $contenido .= "<img  src='$ruta_original'   style='width:100%;height:auto;position: relative;background-size: cover;'/> ";
    }
    # Si hay comentarios los mostrara
    if(!empty($ds_comentarios)){
      $contenido .= "  <b>".ObtenEtiqueta(1679).": </b><br/> $ds_comentarios  ";
    }
    
  }
  else{
    # html datatables
    $contenido = 
    "
    <style>
    .dt-toolbar{
      padding:20px !important;
    }
    .dataTables_filter{
      margin-top:30px !important;
      left:-16px !important;
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
              <h2>".ObtenEtiqueta(2208)."</h2>              
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
      'sDom': '<\'dt-toolbar\'<\'col-xs-12 col-sm-12 hidden-xs\'f><\'col-sm-6 col-xs-12 hidden-xs\'<\'toolbar\'>>r>'+
					't'+
					'<\'dt-toolbar-footer\'<\'col-sm-6 col-xs-12 hidden-xs\'i><\'col-xs-12 col-sm-6\'p>>',
			'autoWidth': true,
      'ajax': {
         'url': 'ajax/works_files.php',
         'data': {
           'fl_leccion': ".$fl_leccion.",
           'fl_alumno': ".$fl_alumno."
          }
       },
      'bDestroy': true,
      'columns': [
          { 'data': 'id', 'class':'text-align-center'},
          { 'data': 'name'},
          { 'data': 'version'},
          { 'data': 'descr'} ,          	            
          { 'data': 'user', 'class':'text-align-center'} ,          	            
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
    </script>";
    $fe_entregado = "".ObtenEtiqueta(2208);
  }
  $result['html'] = $contenido;
  $result['title'] = $fe_entregado;
  echo json_encode((Object) $result);
  
?>