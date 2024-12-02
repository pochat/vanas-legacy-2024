<?php
  
  # Libreria de funciones
  require("../../../lib/sp_general.inc.php");
  
  # Recibe parametros
  $archivo = $_GET['archivo'];
  $clave = $_GET['clave'];
  $name_video = array_shift(explode('.',$archivo));
  $type = $_GET['type'];
  $fg_tipo = $_GET['fg_tipo'];  
  $campus_url = ObtenConfiguracion(121);
  $fame_url = ObtenConfiguracion(116);
  if(empty($fg_tipo)){
    if($type=="VL")
      $ruta = $campus_url."/vanas_videos/campus/lessons/video_".$clave."/video_".$clave."_vl_hd/".$name_video;
    else
      $ruta = $campus_url."/vanas_videos/campus/brief/video_".$clave."/video_".$clave."_vb_hd/".$name_video;
    $ruta_progreso = "campus_procesos.php";
    $para_progreso = "clave=".$clave."&archivo=".$name_video."&type=".$type;
  }
  else{
    # School news
    if($fg_tipo=='news'){      
      $ruta = $campus_url."/vanas_videos/campus/news/video_".$clave."/video_".$clave."_hd/".$name_video;
      $ruta_progreso = "../campus/new_progreso.php";
      $para_progreso = "clave=".$clave."&archivo=".$name_video;
    }
    # Student library
    if($fg_tipo=='SL'){
      $fl_programa = $_GET['p'];
      $no_grado = $_GET['g'];
      $vid = $_GET['vid'];
      $fg_fame = $_GET['fg_fame'];      
      if(empty($fg_fame)){
        // $ruta = ObtenConfiguracion(116)."/vanas_videos/campus/library/video_".$clave."_".$fl_programa."_".$no_grado."/video_".$vid."/video_".$vid."_hd/".$name_video;
        $ruta = $campus_url."/vanas_videos/campus/student_library/video_".$vid."/video_".$vid."_hd/".$name_video;
        $ruta_progreso = PATH_HOME."/modules/content/library_progress.php";
        $para_progreso = "clave=".$clave."&fl_programa=".$fl_programa."&no_grado=".$no_grado."&fl_vid_cont=".$vid;
      }
      else{
        $ruta = $fame_url."/vanas_videos/fame/library/video_".$clave."_".$fl_programa."/video_".$vid."/video_".$vid."_hd/".$name_video;
        $ruta_progreso = PATH_HOME."/modules/content/library_progress.php";
        $para_progreso = "clave=".$clave."&fl_programa=".$fl_programa."&no_grado=".$no_grado."&fl_vid_cont=".$vid."&fg_fame=".$fg_fame;
      }
    }
  }
  
  # Inicia cuerpo de la pagina
  PresentaInicioPagina(False, False, False);
  if(VIDEOS_FLASH==true && $fg_fame==0){    
    echo "  
    <table border='".D_BORDES."' width='100%' cellspacing='0' cellpadding='0'>
      <tr>
        <td align='center'>
          <br>
          <br>\n";        
          PresentaVideoJWP($archivo);        
    echo "
        </td>
      </tr>
      <tr>
        <td align='center' class='default'>
          <br>
          <input type='button' id='buttons' value='".ObtenEtiqueta(74)."' onClick='javascript:window.close();'>
        </td>
      </tr>
     </table>";
  }
  else{
    # Rutas de los videos
    $ruta_ini = ObtenConfiguracion(38)."/vanas_videos/fame/lessons/video_100";
     echo '
    <!-- player skin -->
    <link rel="stylesheet" href="'.SP_FLASH_FAME.'/skin/skin.css">
    <!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="'.PATH_ADM.'/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="'.PATH_ADM.'/bootstrap/css/font-awesome.min.css">

		<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
		<link rel="stylesheet" type="text/css" media="screen" href="'.PATH_ADM.'/bootstrap/css/smartadmin-production-plugins.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="'.PATH_ADM.'/bootstrap/css/smartadmin-production.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="'.PATH_ADM.'/bootstrap/css/smartadmin-skins.min.css">
    <link rel="shortcut icon" href="http://vanas.ca/templates/jm-me/favicon.ico" type="image/x-icon">
		<link rel="icon" href="http://vanas.ca/templates/jm-me/favicon.ico" type="image/x-icon">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <!-- site specific styling -->
    <style>
    .flowplayer {
       width: 720;
       height: 480;
    }
    .flowplayer {      
      background-image: url('.SP_HOME.'/images/PosterFrame_White.jpg);
    }
    </style>
    <!-- Flowplayer library -->
    <script src="'.PATH_SELF_JS.'/flowplayer/flowplayer.min.js"></script>
    <!-- Flowplayer hlsjs engine -->
    <script src="//releases.flowplayer.org/hlsjs/flowplayer.hlsjs.min.js"></script>
    <!-- Flowplayer quality selector plugin -->
    <script src="//releases.flowplayer.org/vod-quality-selector/flowplayer.vod-quality-selector.js"></script>';
    if(empty($fg_tipo)){
      if($type=="VL"){
        echo "
        <div class='row text-align-center'>
          <h3>Video Lecture</h3>
        </div>";
      }
      else{
        echo "
        <div class='row text-align-center'>
          <h3>Video Brief</h3>
        </div>";
      }
    }
    else{
      if($fg_tipo=="news"){
        # Name School news
        $row = RecuperaValor("SELECT ds_titulo FROM c_blog WHERE fl_blog=".$clave);
        $ds_titulo = $row[0];
        echo "
        <div class='row text-align-center'>
          <h3>".$ds_titulo."</h3>
        </div>";
      }
    }
      
    
    echo "
    <style>           
      [data-progressbar-value]::after{
        content: ''
      }
      [data-progressbar-value]::before{
        content: ''
      }
    </style>    
    <div class='row'>
      <div class='col col-sm-12 col-lg-3 col-md-12'>&nbsp</div>
      <div class='col col-sm-12 col-lg-6 col-md-12'>
          <div class='padding-10' id='grl_progress1'>
            <p>
              <div><strong>".ObtenEtiqueta(1864)."</strong></div>
              <!--<div>".ObtenEtiqueta(1863).":
              <code id='duration'></code></div>-->
            </p>
            <div class='progress' data-progressbar-value='0' id='grl_progress'><div class='progress-bar' id='progress_hls'>0%</div></div>
          </div>
        </div>
        <div class='col col-sm-12 col-lg-3 col-md-12'>&nbsp</div>
      </div>
      <div class='row padding-10'> 
        <div class='col col-sm-12 col-lg-3 col-md-12'>&nbsp</div>
        <div class='col col-sm-12 col-lg-6 col-md-12'>
          <div id='div_flowplayer' class='flowplayer fp-edgy'></div>
        </div>
        <div class='col col-sm-12 col-lg-3 col-md-12'>&nbsp</div>
      </div>
    </div>
    <script>
    // Neccesary to watermarker
    flowplayer.conf = {
      splash: true
    };
    // select the above element as player container
    var container = document.getElementById('div_flowplayer'), watermarkTimer, timer;    
    var sources_mp4 = '".$ruta.".mp4';
    var sources_m3u8 = '".$ruta.".m3u8';
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
      // programa para verificar el proceso del video
      setInterval(function(){ 
        $.ajax({
            type: 'GET',
            url : '".$ruta_progreso."',
            data: '".$para_progreso."'
          }).done(function(result){
            var content, tabContainer;
            content = JSON.parse(result);
            progress = content.progress;
            if(!content.error){
              if(progress<=100){
                $('#duration').empty().append(content.duration + '&nbsp;Mins');
                $('#grl_progress').attr('data-progressbar-value', progress);
                $('#progress_hls').empty().append(progress + '%');            
              }
              else{
                $('#grl_progress1').remove('');
              }
            }
            else{
              // $('#grl_progress1').empty().append('Error upload');
              $('#grl_progress').attr('data-progressbar-value', progress);
              $('#progress_hls').empty().append(progress + '%');
            }
          });
      }, 
      2000);
    
    </script>";
    
    include("../../bootstrap/inc/scripts.php");

  }  
echo "
</body>
</html>";
  
?>