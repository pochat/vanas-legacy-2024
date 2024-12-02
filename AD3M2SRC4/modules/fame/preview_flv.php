<?php
  
  # Libreria de funciones
  require("../../../lib/sp_general.inc.php");

  # Variable initialization
  $fl_vid_content=NULL;
  $fl_programa=NULL;
  
  # Recibe parametros
  $archivo = $_GET['archivo'];
  $clave = $_GET['clave'];
  $explosion=explode('.',$archivo);
  $name_video = array_shift($explosion);
  $fg_tipo = !empty($_GET['fg_tipo'])?$_GET['fg_tipo']:NULL;

  # Videos de Student library
  if(!empty($fg_tipo) && $fg_tipo=="SL"){
    $fl_programa = $_GET['p'];
    $fl_vid_content = $_GET['vid'];
    $ruta_video = ObtenConfiguracion(116)."/vanas_videos/fame/library/video_".$clave."_".$fl_programa."/video_".$fl_vid_content."/video_".$fl_vid_content."_sd/".$name_video.".m3u8";
	$ruta_img=ObtenConfiguracion(116)."/vanas_videos/fame/library/video_".$clave."_".$fl_programa."/video_".$fl_vid_content."/video_".$fl_vid_content."_sd/";
  }
  else{
    $ruta_video = ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$clave."/video_".$clave."_sd/".$name_video."_sd.m3u8";
    $ruta_subtitle = ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$clave;
	$ruta_img=ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$clave."/video_".$clave."_sd/";
  }
  
echo "
<!doctype html>

<head>
   <title> Administration</title>
   <!-- player skin -->
   <link rel='stylesheet' href='".SP_FLASH_FAME."/skin/skin.css'>
   <!-- Basic Styles -->
		<link rel='stylesheet' type='text/css' media='screen' href='".PATH_ADM."/bootstrap/css/bootstrap.min.css'>
		<link rel='stylesheet' type='text/css' media='screen' href='".PATH_ADM."/bootstrap/css/font-awesome.min.css'>

		<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
		<link rel='stylesheet' type='text/css' media='screen' href='".PATH_ADM."/bootstrap/css/smartadmin-production-plugins.min.css'>
		<link rel='stylesheet' type='text/css' media='screen' href='".PATH_ADM."/bootstrap/css/smartadmin-production.min.css'>
		<link rel='stylesheet' type='text/css' media='screen' href='".PATH_ADM."/bootstrap/css/smartadmin-skins.min.css'>
    <link rel='shortcut icon' href='http://vanas.ca/templates/jm-me/favicon.ico' type='image/x-icon'>
		<link rel='icon' href='http://vanas.ca/templates/jm-me/favicon.ico' type='image/x-icon'>

    <script src='https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js'></script>
	
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
    <script src='".PATH_SELF_JS."/flowplayer/flowplayer.min.js'></script>
    <!-- Flowplayer hlsjs engine -->
    <script src='//releases.flowplayer.org/hlsjs/flowplayer.hlsjs.min.js'></script>
    <!-- Flowplayer quality selector plugin -->
    <script src='//releases.flowplayer.org/vod-quality-selector/flowplayer.vod-quality-selector.js'></script>
	
	<script src='//releases.flowplayer.org/thumbnails/flowplayer.thumbnails.min.js'></script>
	
   <!-- site specific styling -->
   <style>
   body { font: 12px 'Myriad Pro', 'Lucida Grande', sans-serif; text-align: center; }
   .flowplayer { width: 70%; }
   [data-progressbar-value]::after{
     content: ''
   }
   [data-progressbar-value]::before{
     content: ''
   }
   </style>


</head>

<body>
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
  <div class='row padding-10'> <i id='keys_flow' class='fa fa-info'></i> ".ObtenEtiqueta(1887)."</div>
  ";
  
  /*
  echo"
  <div id='div_flowplayer' class='flowplayer'  data-key='".ObtenConfiguracion(110)."'>
 
    <video>         
      <source type='application/x-mpegurl' src='".$ruta_video."'>";
      # Los subtitles solo a los videos de las lecciones
      if(empty($fg_tipo)){
        # Obtenemos los idiomas del video solo muestra los videos activos
        $Query  = "SELECT a.fl_idioma, ds_language, nb_archivo, b.nb_idioma, ds_code FROM k_idioma_video a, c_idioma b ";     
        $Query .= "WHERE a.fl_idioma = b.fl_idioma AND  fl_leccion_sp=".$clave." AND a.fg_activo='1' ";
        $rs = EjecutaQuery($Query);
        for($i=0;$rowl=RecuperaRegistro($rs);$i++){
          $fl_idioma = $rowl[0];
          $ds_language = str_texto($rowl[1]);
          $nb_archivo = $rowl[2];
          $nb_idioma = str_texto($rowl[3]);
          $idioma = $rowl[4];
          echo "
        <track kind='subtitles' default srclang='".$idioma."' label='".$nb_idioma."' src='".$ruta_subtitle."/".$nb_archivo."?date=".date('Ymdhis')."'>";
      }
      }
 echo "
    </video>
  </div>";
*/  
  echo"
 <div id='div_flowplayer' class='flowplayer fp-edgy'></div>

<script>

// Neccesary to watermarker
    flowplayer.conf = {
      splash: true
    };
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
  
  
  
 /* echo"
     <div id='div_flowplayer' class='flowplayer fp-edgy'></div>
  ";
  
  
  echo"
  
  
  
  
  
  ";
  */
  
  
  echo Modal_Keys("key_modal")."
  <script>
  setInterval(function(){ 
    $.ajax({
				type: 'GET',
				url : 'progreso_comando.php',
				data: 'clave='+".$clave."+
							'&archivo='+'".$archivo."&fl_vid_cont=".$fl_vid_content."&fg_tipo=".$fg_tipo."&fl_programa=".$fl_programa."'
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
          $('#grl_progress1').empty().append('Error upload');
        }
      });
  }, 
  2000);
  </script>";

  echo "
  <!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<script data-pace-options='{ \'restartOnRequestAfter\': true }' src='".PATH_HOME."/bootstrap/js/plugin/pace/pace.min.js'></script>

		<!-- These scripts will be located in Header So we can add scripts inside body (used in class.datatables.php) -->    
    <script src='".PATH_HOME."/bootstrap/js/jquery.min.js'></script>
		<script>
			if (!window.jQuery) {
				document.write('<script src=\'".PATH_HOME."/bootstrap/js/libs/jquery-2.0.2.min.js\'><\/script>');
			}
		</script>

		<script src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js'></script>
		<script>
			if (!window.jQuery.ui) {
				document.write('<script src=\'".PATH_HOME."/bootstrap/js/libs/jquery-ui-1.10.3.min.js\'><\/script>');
			}
		</script>

		<!-- IMPORTANT: APP CONFIG -->
		<script src='". PATH_HOME."/bootstrap/js/app.config.js'></script>

		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
		<script src='". PATH_HOME."/bootstrap/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js'></script> 

		<!-- BOOTSTRAP JS -->
		<script src='". PATH_HOME."/bootstrap/js/bootstrap/bootstrap.min.js'></script>

		<!-- CUSTOM NOTIFICATION -->
		<script src='". PATH_HOME."/bootstrap/js/notification/SmartNotification.min.js'></script>

		<!-- JARVIS WIDGETS -->
		<script src='". PATH_HOME."/bootstrap/js/smartwidgets/jarvis.widget.min.js'></script>

		<!-- EASY PIE CHARTS -->
		<script src='". PATH_HOME."/bootstrap/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js'></script>

		<!-- SPARKLINES -->
		<script src='". PATH_HOME."/bootstrap/js/plugin/sparkline/jquery.sparkline.min.js'></script>

		<!-- JQUERY VALIDATE -->
		<script src='". PATH_HOME."/bootstrap/js/plugin/jquery-validate/jquery.validate.min.js'></script>

		<!-- JQUERY MASKED INPUT -->
		<script src='". PATH_HOME."/bootstrap/js/plugin/masked-input/jquery.maskedinput.min.js'></script>

		<!-- JQUERY SELECT2 INPUT -->
		<script src='". PATH_HOME."/bootstrap/js/plugin/select2/select2.min.js'></script>

		<!-- JQUERY UI + Bootstrap Slider -->
		<script src='". PATH_HOME."/bootstrap/js/plugin/bootstrap-slider/bootstrap-slider.min.js'></script>

		<!-- browser msie issue fix -->
		<script src='". PATH_HOME."/bootstrap/js/plugin/msie-fix/jquery.mb.browser.min.js'></script>

		<!-- FastClick: For mobile devices -->
		<script src='". PATH_HOME."/bootstrap/js/plugin/fastclick/fastclick.min.js'></script>

    <!-- Flowplayer library -->
  <!--  <script src='".SP_FLASH_FAME."/flowplayer.min.js'></script>-->
    <!-- Flowplayer hlsjs engine -->
  <!--  <script src='//releases.flowplayer.org/hlsjs/flowplayer.hlsjs.min.js'></script>-->
	
	

</body>";

function Modal_Keys($idmodal){

  $modal = 
  "<div id='".$idmodal."' class='modal fade' tabindex='-1' role='dialog' aria-labelledby='item-title' aria-hidden='true'>
    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'> <i class='fa fa-times fa-2x txt-colour-white'></i></button>
    <div class='row' style='position:relative; top:20%;color:#FFF;'>
      <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-2 padding-10'></div>
      <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-9 padding-10'>
      
        <div class='row padding-10'>
          <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-2 padding-10'>
            <a class='btn btn-default'><strong>space</strong></a> <strong>play / pause</strong>
          </div>
          <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-2 padding-10'>
            <a class='btn btn-default'><strong>q</strong></a> <strong>unload / stop</strong>
          </div>
          <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-2 padding-10'>
            <a class='btn btn-default'><strong>f</strong></a> <strong>fullscreen</strong>
          </div>
          <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-4 padding-10'>
            <a class='btn btn-default'><strong>shift</strong></a> <i class='fa fa-plus'></i> <a class='btn btn-default'><i class='fa fa-arrow-left'></i></a> <a class='btn btn-default'><i class='fa fa-arrow-right'></i></a> <strong>slower / faster</strong>
          </div>
        </div>
        
        <div class='row padding-10'>
          <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-3'>&nbsp;</div>
          <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-6'>
            <a class='btn btn-default'><i class='fa fa-arrow-up'></i></a> <a class='btn btn-default'><i class='fa  fa-arrow-down'></i></a> <strong>volumen</strong> <a class='btn btn-default'><strong>m</strong></a> <strong>mute</strong>
          </div>
          <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-3 padding-10'>&nbsp;</div>
        </div>
      
      
        <div class='row padding-10'>
         <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-3 padding-10'>
            <a class='btn btn-default'><i class='fa fa-arrow-left'></i></a> <a class='btn btn-default'><i class='fa fa-arrow-right'></i></a> <strong>seek</strong>
          </div>
          <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-3 padding-10'>
            <a class='btn btn-default'><strong>.</strong></a> <strong>seek to previous</strong>
          </div>
          <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-4 padding-10'>
            <a class='btn btn-default'><strong>1</strong></a> <a class='btn btn-default'><strong>2</strong></a> <strong>....</strong> <a class='btn btn-default'><strong>6</strong></a> <strong>seek to 10%, 20%... 60%</strong>
          </div>
        </div>
        
        
      </div>
      <div class='col col-sm-12 col-md-12 col-xs-12 col-lg-1'></div>
    </div>

      
  </div>
  <script>
    
    $(document).keypress(function (e) {     
      if (e.key == '?') {
        $('#".$idmodal."').modal('show');
      }
    });
  </script>";
  
  return $modal;
}


?>
