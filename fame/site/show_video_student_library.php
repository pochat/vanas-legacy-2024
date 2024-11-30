<?php 
# Libreria de funciones
require("../lib/self_general.php");

  
  $fl_vid_contet_temp = RecibeParametroHTML('fl_vid_contet_temp');
  $fl_video_contenido_sp=RecibeParametroNumerico('fl_video_contenido_sp');
  $fl_usuario=RecibeParametroNumerico('fl_usuario');
  $fl_instituto = ObtenInstituto($fl_usuario); 
    
  #we retrieve data from the video to show it.
  $Query="SELECT a.fl_video_contenido_sp,b.fl_vid_contet_temp,a.ds_progreso,nb_archivo,a.fl_programa_sp,a.ds_title_vid,b.no_orden
		  FROM k_video_contenido_sp a
		  JOIN k_vid_content_temp b ON a.fl_vid_contet_temp =b.fl_vid_contet_temp
          WHERE a.fl_video_contenido_sp=$fl_video_contenido_sp AND b.fl_vid_contet_temp=$fl_vid_contet_temp 		  
  
  ";
  $row=RecuperaValor($Query);
  $nb_archivo=str_texto($row['nb_archivo']);
  $ds_title_vid=str_texto($row['ds_title_vid']);
  $ds_progreso=$row['ds_progreso'];
  $fl_programa_sp=$row['fl_programa_sp'];
  $no_orden=$row['no_orden'];
  $fl_video_contenido_sp=$row['fl_video_contenido_sp'];


  # Obtenemos la extension del archivo
  $ext = ObtenExtensionArchivo($nb_archivo);      

  # Nombre para todos los archivos
  $solo_nombre_archivo = explode(".", $nb_archivo);
  $solo_nombre_archivo = $solo_nombre_archivo[0];

  #document content video
  $ruta = ObtenConfiguracion(116)."/fame/site/uploads/".$fl_instituto."/attachments/student_library/video_".$fl_video_contenido_sp."/video_".$no_orden."/";                  
  $ruta_video=$ruta."".$solo_nombre_archivo."_sd.m3u8";
 
  
?>
<style>
.flowplayer {
   
   
}
cotent{
	
	width: 60%;
}
.flowplayer {
    background-image: url(../fame/img/PosterFrame_White.jpg)!important;
}
</style>

	<div class="modal-header">
		<button type="button" class="close" id="cerrar_modal_video" onclick="CerrarVideo();" data-dismiss="modal" aria-hidden="true">
			&times;
		</button>
		<h4 class="modal-title" id="myModalLabel"><i class="fa fa-video-camera" aria-hidden="true"></i> <?php echo $ds_title_vid;?></h4>
	</div>
	<div class="modal-body">
	
		<div class="row">
			<div class="col-md-1">&nbsp;</div>
			<div class="col-md-10">
			     <div class="content">
					<div id='div_flowplayer' class='flowplayer fp-edgy'></div>
				  </div>
			</div>
			<div class="col-md-1">&nbsp;</div>
		</div>

	</div>
	
	<div class="modal-footer">
		<button type="button" class="btn btn-default" id="cerrar_modal_video2" onclick="CerrarVideo();" data-dismiss="modal">
			<i class="fa fa-times-circle-o" aria-hidden="true"></i> Close
		</button>
		<button type="button" class="btn btn-primary hidden">
			Post Article
		</button>
	</div>
	
	
	
	
<?php
echo"
<script>

 function CerrarVideo(){
            $('#div_flowplayer').flowplayer().stop();
		    $('#div_flowplayer').html('');
 }
// Neccesary to watermarker
    flowplayer.conf = {
      splash: true
    };
	flowplayer(function (api) {
      $('#cerrar_modal_video').on('click', function () {
          api.stop();
      });
	  $('#cerrar_modal_video2').on('click', function () {
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
";		
	/*	subtitles: [
            { 'default': true,       // note the quotes around 'default'!
              kind: 'subtitles', srclang: 'en', label: 'English',
              src:  '//edge.flowplayer.org/subtitles/subtitles-en.vtt' },
            { kind: 'subtitles', srclang: 'de', label: 'Deutsch',
              src:  '//edge.flowplayer.org/subtitles/subtitles-de.vtt' }
        ],*/
echo"
		  
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
          template: '$ruta/img{time}.jpg'
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



?>	
	
	
	
	
