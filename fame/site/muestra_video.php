<?php 
# Libreria de funciones	
  require("../lib/self_general.php");
  
  $clave = $_POST['clave'];
  
  $langselect = $_COOKIE[IDIOMA_NOMBRE];
  
   switch ($langselect) {
      case '1': $sufix = '_esp';
        break;

      case '2': $sufix = '';
        break;

      case '3': $sufix = '_fra';
        break;
      
      default: $sufix = '';
        break;
    }
  
  
  
  #Recuperamos la vista previa del video.
  $Query="SELECT ds_vl_ruta,ds_titulo".$sufix." FROM c_leccion_sp WHERE fl_leccion_sp=$clave ";
  $row=RecuperaValor($Query);
  $name_video=$row[0];
  $nb_leccion_sp=$row[1];
  $explosion=explode('.',$name_video);
  $name_video = array_shift($explosion);
  $fg_tipo = !empty($_GET['fg_tipo'])?$_GET['fg_tipo']:NULL;

 $ruta_video = ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$clave."/video_".$clave."_sd/".$name_video."_sd.m3u8";
 $ruta_subtitle = ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$clave;
 $ruta_img=ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$clave."/video_".$clave."_sd/";


?>


<div class="modal-header" >

	<h5 class="modal-title" ><i class="fa fa-book" aria-hidden="true"></i> <?php echo $nb_leccion_sp;?></h5>
	<button type="button" class="close" id="cerrar_modal_video" onclick="CerrarVideo();" data-dismiss="modal" aria-label="Close" style="margin-top: -20px;">
	  <span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
				
			 
 <div id='div_flowplayer' class='flowplayer fp-edgy'></div>
 
 <?php 
 
 
 echo"
 <script>

// Neccesary to watermarker
    flowplayer.conf = {
      splash: true
    };
	
	flowplayer(function (api) {
	  $('#cerrar_modal_video').on('click', function () {
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
";

    # Los subtitles solo a los videos de las lecciones
      if(empty($fg_tipo)){

        # Obtenemos los idiomas del video solo muestra los videos activos
        $Query  = "SELECT a.fl_idioma, ds_language, nb_archivo, b.nb_idioma, ds_code FROM k_idioma_video a, c_idioma b ";     
        $Query .= "WHERE a.fl_idioma = b.fl_idioma AND  fl_leccion_sp=".$clave." AND a.fg_activo='1' ";
        $rs = EjecutaQuery($Query);
		
		 echo" subtitles: [  ";
		$registros = CuentaRegistros($rs);
        for($i=0;$rowl=RecuperaRegistro($rs);$i++){
          $fl_idioma = $rowl[0];
          $ds_language = str_texto($rowl[1]);
          $nb_archivo = $rowl[2];
          $nb_idioma = str_texto($rowl[3]);
          $idioma = $rowl[4];
	
	
	   
		   echo"{
		     kind: 'subtitles', srclang: '$idioma', label: '$nb_idioma',
             src:  '$ruta_subtitle/$nb_archivo?date=".date('Ymdhis')."' }
		   
		   ";
		   if($i<=($registros-1))
              echo ",";
           else
              echo "";
		
	
	
	
	  }
        echo" ], "; 

	  }




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
  
  
 ?>
 
 </div>
<div class="modal-footer" style="display:none">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	<button type="button" class="btn btn-primary">Save changes</button>
</div>
 
 