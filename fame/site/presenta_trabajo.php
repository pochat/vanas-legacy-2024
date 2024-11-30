<?php

  # Libreria de funciones	
  require("../lib/self_general.php");

  #Recibimos parametros
  $fl_entregable_sp=RecibeParametroNumerico('fl_entregable_sp');
  $fl_alumno=RecibeParametroNumerico('fl_alumno');
  $fl_leccion_sp=RecibeParametroNumerico('fl_leccion_sp');
  $fl_maestro=RecibeParametroNumerico('fl_teacher');

  #Recupermaos el instituto del alumno seleccionado.
  $fl_instituto=ObtenInstituto($fl_alumno);
  
  # Muestra los trabajos extras work files
  if(empty($fl_leccion_sp)){
    #Recuperamos los trabajos.
    $Query="SELECT fg_tipo,ds_ruta_entregable,ds_comentario,fe_entregado FROM k_entregable_sp WHERE fl_entregable_sp=$fl_entregable_sp ";
    $row=RecuperaValor($Query);
    $fg_tipo=$row[0];
    $nb_archivo=$row[1];
    $ds_comentarios=str_texto($row[2]);
    $fe_entregado= ObtenEtiqueta(1677)." : ".ObtenFechaFormatoDiaMesAnioHora($row[3],true);

    $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
    
    if(($ext == "ogg")||($ext=='mp4')||($ext=='mov')||($ext=='m3u8')){
      $file = explode('.',$nb_archivo);
      $nb_archivo = $file[0].".".$ext;
    }

    if(($ext == "ogg")||($ext=='mp4')||($ext=='mov')||($ext=='m3u8')){
      #Rutas del archivo.
      $ruta_video = PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_alumno."/videos/$nb_archivo";
    }else{
      $ruta_thumbs = PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_alumno."/sketches/thumbs/$nb_archivo";
      $ruta_board_thumbs = PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_alumno."/sketches/board_thumbs/$nb_archivo";
      $ruta_original= PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_alumno."/sketches/original/$nb_archivo";
    }

    if(($ext == "ogg")||($ext=='mp4')||($ext=='mov')||($ext=='m3u8')){
      $ruta_thumbs="site/uploads/gallery/thumbs/vanas-board-video-default.jpg";
    }
    $contenido = "";
    # Muestra el trabajo videoo imagen
    if(($ext == "ogg")||($ext=='mp4')||($ext=='mov')){	
      $contenido .= "
      <video class='center' controls='controls' style='width:100%!important;'>
        <source src='$ruta_video' type='video/$ext'>
        <source src='$ruta_video' type='video/mov'>
        <source src='$ruta_video' type='video/mp4'>
      </video>";
    }else if($ext=='m3u8'){
     
        //flowplayer.
        $contenido.="

                 <div id='div_flowplayer' class='flowplayer fp-edgy'></div>
                
                <script>

// Neccesary to watermarker
    flowplayer.conf = {
      splash: true
    };
	
	flowplayer(function (api) {
	  $('#ui-dialog-titlebar-close').on('click', function () {
          api.toggleMute();     
		  api.stop();
          
	  });
	  

	});
	
	
// select the above element as player container
var container = document.getElementById('div_flowplayer'), watermarkTimer, timer;    
var sources_m3u8 = '".$ruta_video."';
var key_flowplayer = '".ObtenConfiguracion(110)."';

// opciones
      var optionss = {
        key: key_flowplayer,      
        ratio: 9/16,
        clip: {
          sources: [
            // { type: 'video/mp4',
              // src:  sources_mp4 },
            { type: 'application/x-mpegURL',
              src:  sources_m3u8 }
          ], 
   scaling: 'fit',
          // configure clip to use hddn as our provider, referring to the rtmp plugin
          provider: 'hddn'          
        },
		 //esto es para generar la vista previa en la linea de tiempo de los videos. 
        thumbnails: {
          width: 120,
          height: 100,
          columns: 5,
          rows: 8,
          template: '$ruta_img/img{time}.jpg'
		  //template: 'img1.jpg'
        },

        rtmp: 'rtmp://s3b78u0kbtx79q.cloudfront.net/cfx/st',
        // loop playlist
        loop: false,
        keyboard:true,
        embed:false,
        share:false,
        volume: 1.0
      };

      // install flowplayer into selected container
      flowplayer(container, optionss)
       // WaterMarke fullscreen and fullscreen-exit
       .on('fullscreen fullscreen-exit', function (e, api) {
          if (/exit/.test(e.type)) { // sale
             // do something after leaving fullscreen 
             // no working
          } else { // entra
            // do something after going fullscreen
            // Start the watermark interval
            watermarkTimer = setInterval(function() {
              var width, height, min, x, y, css;
            
              // Show or hide watermark
              // $('#div_watermark').toggle();

              // Screen size
              width = window.innerWidth;
              height = window.innerHeight;
              min = 20; // 20 padding

              // Generate random width and height
              x = Math.floor(Math.random() * (width - min)) + min;
              y = Math.floor(Math.random() * (height - min)) + min;
              // Move watermark to new positions
              css = {left: x, top: y};
              $('#div_watermark').animate(css, 0);              
            }, 10000);
          }
      });
</script>


        ";



        


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
      margin-top:25px !important;
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
              <h2>".ObtenEtiqueta(2215)."</h2>              
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
                      <th data-hide='phone'>".ObtenEtiqueta(2217)."</th>
                      <th data-class='expand'>".ObtenEtiqueta(2218)."</th>
                      <th data-hide='phone'>".ObtenEtiqueta(2219)."</th>
                      <th data-hide='phone, table'>".ObtenEtiqueta(2220)."</th>
                      <th data-hide='phone, table'>".ObtenEtiqueta(2227)."</th>
                      <th data-hide='phone, table'>".ObtenEtiqueta(2221)."</th>
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
         'url': 'Querys/works_files.php',
         'data': {
           'fl_leccion_sp': ".$fl_leccion_sp.",
           'fl_alumno': ".$fl_alumno.",
           'fl_teacher': ".$fl_maestro.",
          }
       },
      'bDestroy': true,
      'fnDrawCallback': function( oSettings ) {
        /** Se tuiliza para el nombre de las imagenes **/
        $('[rel=tooltip]').tooltip();
      },
      'columns': [
          { 'data': 'id', 'class':'text-align-center'},
          { 'data': 'name', 'class':'text-align-center'},
          { 'data': 'version'},
          { 'data': 'descr'} ,          	            
          { 'data': 'user'} ,          	            
          { 'data': 'date', 'class':'text-align-center'} ,          	            
          { 'data': 'btns', 'class':'text-align-center', 'width': '10%' } ,          	            
      ],
      'autoWidth' : true,
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
    $fe_entregado = ObtenEtiqueta(2215);
  }  
  $result['html'] = $contenido;
  $result['title'] = $fe_entregado;
  
  echo json_encode((Object) $result);
  
?>