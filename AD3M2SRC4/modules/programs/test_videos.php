<?php
  
  # Libreria de funciones
  require("../../../lib/sp_general.inc.php");
  include("../../../vanas_videos/videos.php");
 /* # Recibe parametros
  $archivo = $_GET['archivo'];
  $clave = $_GET['clave'];
  $name_video = array_shift(explode('.',$archivo));
  $fg_tipo = $_GET['fg_tipo'];
  # Videos de Student library
  if(!empty($fg_tipo) && $fg_tipo=="SL"){
    $fl_programa = $_GET['p'];
    $fl_vid_content = $_GET['vid'];
    $ruta_video = ObtenConfiguracion(116)."/vanas_videos/fame/library/video_".$clave."_".$fl_programa."/video_".$fl_vid_content."/video_".$fl_vid_content."_sd/".$name_video.".m3u8";
  }
  else{
    $ruta_video = ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$clave."/video_".$clave."_sd/".$name_video."_sd.m3u8";
    $ruta_subtitle = ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$clave;
  }
  
  $ruta_video = "//mnt/data2/vanas/vanas_videos/campus/brief/video_1/video_1_vb_hd/CA_T1_week01_v_brief.m3u8";
  echo "PATH>>>".$PATH;
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
    <link rel='shortcut icon' href='https://vanas.ca/templates/jm-me/favicon.ico' type='image/x-icon'>
		<link rel='icon' href='https://vanas.ca/templates/jm-me/favicon.ico' type='image/x-icon'>
    
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js'></script>
    <!-- Flowplayer library -->
    <script src='".PATH_SELF_JS."/flowplayer/flowplayer.min.js'></script>
    <!-- Flowplayer hlsjs engine -->
    <script src='//releases.flowplayer.org/hlsjs/flowplayer.hlsjs.min.js'></script>
    <!-- Flowplayer quality selector plugin -->
    <script src='//releases.flowplayer.org/vod-quality-selector/flowplayer.vod-quality-selector.js'></script>
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
  <!-- flowplayer with RTMP configuration option -->
  <div class='flowplayer' data-rtmp='rtmp://s3b78u0kbtx79q.cloudfront.net/cfx/st' data-engine='false'>

  <video>    
    <source type='video/mp4'   src='".$ruta_video."'>
  </video>

  </div>
  <!--<div id='player' class='fixed-controls'  style='width:600px; height:338px;'></div>
  <script>
    var api = flowplayer('#player', {
      live: true,
      splash: true,
      clip: {
          sources: [
              {
                  type: 'application/x-mpegurl',
                  src: '".$ruta_video."'
              }
          ],
          title: 'LiveStream'
      },
      embed: {
          skin: 'http://releases.flowplayer.org/6.0.1/skin/bauhaus.css'
      }
  }); 
    </script>-->";

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

		<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js'></script>
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

*/
?>