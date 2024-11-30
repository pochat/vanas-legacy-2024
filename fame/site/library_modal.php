<?php 
	# Libreria de funciones
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametro
  $fl_video_contenido = RecibeParametroNumerico('item');
  
  # Obtenemos la informacion del video
  $Query = "SELECT cl_pagina_sp, fl_programa_sp, ds_ruta_video FROM k_video_contenido_sp WHERE fl_video_contenido_sp=".$fl_video_contenido;
  $row = RecuperaValor($Query);
  $cl_pagina = $row[0];
  $fl_programa = $row[1];
  $ds_ruta_video = $row[2];
  $ruta = ObtenConfiguracion(116)."/vanas_videos/fame/library/video_".$cl_pagina."_".$fl_programa."/video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/".$ds_ruta_video;
  $mp4 = $ruta.".mp4";
  $m3u8 = $ruta.".m3u8";
  
  $result['video'] = 
  "
  <!-- Modal Content -->
  <div class='modal-header'>
    <button type='button' class='close' id='cerrar_modal_video' data-dismiss='modal' aria-hidden='true'>&times;</button>
    <h4 class='modal-title' id='item-title' style='font-weight:500;'></h4>
  </div>
  <div class='modal-body text-align-center' id='item-body'>
    <div class='row'>
      <div class='col col-sm-12 col-lg-1 col-md-12'></div>
      <div class='col col-sm-12 col-lg-10 col-md-12'>
        <div id='div_flowplayer' class='flowplayer fp-edgy'></div>
      </div>
      <div class='col col-sm-12 col-lg-1 col-md-12'></div>
    </div>
  </div>
  <div class='modal-footer' id='item-footer'></div>
  <script>
  var m3u8 = '".$m3u8."';
  var key = '".ObtenConfiguracion(110)."';
  // select the above element as player container
  var container = document.getElementById('div_flowplayer'), watermarkTimer, timer;    

  // Neccesary to watermarker
  flowplayer.conf = {
    splash: true
  };
  
  flowplayer(function (api) {
      $('#cerrar_modal_video').on('click', function () {
          api.stop();
      });
  });

 // opciones
  var optionss = {
    key: key,      
    ratio: 9/16,
    clip: {
      sources: [
        { type: 'application/x-mpegURL',
          src:  m3u8 }
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
    share:false 
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
  <!-- Flowplayer library -->
  <script src='".PATH_SELF_JS."/flowplayer/flowplayer.min.js'></script>
  <!-- Flowplayer hlsjs engine -->
  <script src='//releases.flowplayer.org/hlsjs/flowplayer.hlsjs.min.js'></script>
  <!-- Flowplayer quality selector plugin -->
  <script src='//releases.flowplayer.org/vod-quality-selector/flowplayer.vod-quality-selector.js'></script>
  <script src='".PATH_N_ALU_PAGES."/flowplayer.inc.js'></script>";
  
  echo json_encode((Object) $result);
    
?>
