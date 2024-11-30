<?php 
	
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

	# Recibe parametros
	$fl_blog = RecibeParametroNumerico('blog', True);

	# Actualiza contador de visualizaciones de la noticia
	EjecutaQuery("UPDATE c_blog SET no_hits=no_hits+1 WHERE fl_blog=$fl_blog");

	# Actualiza estado de la notificacion para el usuario
	EjecutaQuery("DELETE FROM k_not_blog WHERE fl_blog=$fl_blog AND fl_usuario=$fl_alumno");

	# Retrieve content of the news post
	$fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
	$Query  = "SELECT ". ConsultaFechaBD('fe_blog', FMT_FECHA)." 'fe_blog', ds_titulo, ds_blog, ds_ruta_imagen, ds_ruta_video ";
	$Query .= "FROM c_blog ";
	$Query .= "WHERE fl_blog=$fl_blog ";
	$Query .= "AND fg_alumnos='1' ";
	$Query .= "AND fe_blog <= $fe_actual ";
	$Query .= "AND DATE_ADD(fe_blog, INTERVAL ".ObtenConfiguracion(18)." DAY) >= $fe_actual";
	$row = RecuperaValor($Query);
	$titulo = str_uso_normal($row[1]);
	$contenido = str_uso_normal($row[2]);
	$archivo_img = str_uso_normal($row[3]);
	$archivo_flv = str_uso_normal($row[4]);
    $ds_vl_ruta = $archivo_flv;
	# Initial variable
	$result = array();

	# Add static data
	$result["title"] = $titulo;
	$result["content"] = html_entity_decode($contenido);

	# News image
	if(!empty($archivo_img) AND empty($archivo_flv)) {
  	$result["archive_img"] = SP_IMAGES."/news/$archivo_img";
	}
  else
    $result["archive_img"] = "";
	
	# News video
	if(!empty($archivo_flv)) {
		# User id
		/*$ds_matricula = ObtenMatriculaAlumno($fl_alumno);

		$result["rtmp"] = "rtmp://".ObtenConfiguracion(60)."/oflaDemo";
		$result["rtmp_plugin"] = SP_FLASH."/flowplayer.rtmp-3.2.13.swf";
		$result["player"] = SP_FLASH."/flowplayer.commercial-3.2.18.swf";
		$result["player_img"] = SP_IMAGES."/PosterFrame_PlayIcon.jpg";
		$result["video_name"] = ObtenNombreArchivo($archivo_flv);
		$result["watermark"] = PresentWatermark($ds_matricula);
		$result["st_id"] = $ds_matricula;*/
	#Prod	
	$ruta_ini= "https://".ObtenConfiguracion(60)."/vanas_videos/campus/news/";	
    #Dev
	//$ruta_ini= ObtenConfiguracion(116)."/vanas_videos/campus/news/";
    $name_video = array_shift(explode('.',$ds_vl_ruta));
    $ruta_videos_mp4 = $ruta_ini."/video_".$fl_blog."/".$name_video.".mp4";
    $ruta_videos_m3u8 = $ruta_ini."/video_".$fl_blog."/video_".$fl_blog."_hd/".$name_video.".m3u8";
    $result["sources_fame_mp4"] = $ruta_videos_mp4;
    $result["sources_fame_m3u8"] = $ruta_videos_m3u8;
    $result["video_name"] = $ds_vl_ruta;
    $result["key_flowplayer"] = ObtenConfiguracion(110);
    $result["player_img"] = SP_IMAGES."/PosterFrame_PlayIcon.jpg";
	}
?>

<!-- Modal Content -->
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title"></h4>
</div>
<div class="modal-body">
<!-- Flowplayer library -->
<script src="<?php echo PATH_SELF_JS; ?>/flowplayer/flowplayer.min.js"></script>
<!-- Flowplayer hlsjs engine -->
<script src="//releases.flowplayer.org/hlsjs/flowplayer.hlsjs.min.js"></script>
<!-- Flowplayer quality selector plugin -->
<script src="//releases.flowplayer.org/vod-quality-selector/flowplayer.vod-quality-selector.js"></script>
</div>
<div class="modal-footer"></div>

<script type="text/javascript">
	var result, modalBody;

	// Retrieves the blog content
	result = <?php echo json_encode((Object)$result); ?>;

	// Modal body container
	modalBody = $(".modal-body");

	// Add news title
	$(".modal-title").append(result.title);

	// News image
	if(result.archive_img){
		// Add the image
		modalBody.append("<img class='img-responsive center-block' src='"+result.archive_img+"'>");

		// Add a border
		modalBody.append("<div style='border:1px solid #E3E3E3; margin:10px 0;'></div>");
	}
	
	// News video
	if(result.video_name){
		// Prepare video container
		modalBody.append("<div class='row'><div class='col col-sm-12 col-lg-1 col-md-12'></div><div class='col col-sm-12 col-lg-10 col-md-12'><div id='news-video'></div></div><div class='col col-sm-12 col-lg-1 col-md-12'></div></div>");

		/*// Default 16:9 ratio, carried over from the old campus
		// flashembed is a global flash object from flowplayer
		flashembed.conf.width = "720";
		flashembed.conf.height = "405";

		// Fullscreen watermark
		var watermarkTimer, clearWatermarkText;
		
		// Create flowplayer
		$f("news-video", result.player, {
			key: '#$79d60288437ade68168',

	    clip: {
        url: 'mp4:'+result.video_name,
        scaling: 'fit',
        // configure clip to use hddn as our provider, referring to the rtmp plugin
        provider: 'hddn'
	    },

	    // streaming plugins
	    plugins: {
	    	// controller bar
	    	controls:  {
          backgroundGradient: 'none',
          backgroundColor: 'transparent',
          timeColor: '#FFFFFF',
          timeSeparator: ' / ',
          durationColor: '#FFFFFF',
          timeBgColor: 'rgb(0,0,0, 0.2)',
          scrubber:true,
          height:45
        },
        // rtmp plugin configuration
        hddn: {
          url: result.rtmp_plugin,
         	
          // define where the streams are found
          netConnectionUrl: result.rtmp
        },
        // watermark plugin
        watermark: {
        	url: 'flowplayer.content-3.2.9.swf',
          bottom: 40,
        	left: 5,
        	width: 130,
        	height: 25,
          padding: 3,
          borderRadius: 10,
          border: '0px',
         	backgroundColor: 'rgba(0,0,0,0)',
         	opacity: 0.8,
        	html: '<p>'+result.st_id+'</p>',
        	style: {
        		p: {
        			fontSize: 16,
        			textAlign: 'center',
        			color: '#FFFFFF',
        			fontWeight: 'bold'
        		}
        	},
        	// don't display on video start
        	display: 'none'
        }
	    },

	    canvas: {
        backgroundGradient: 'none'
	    },

	    // Load settings before video
	    onBeforeLoad: function(){
	    	// Wrap in responsive css
	    	this.getParent().className = "embed-container";
	    },

	    // Load settings after video is ready
	    onLoad: function(){
	    	var parentNode = $("#"+this.getParent().id);

	    	// Add watermark
	    	if($("#div_watermark").length === 0){
	    		parentNode.prepend(result.watermark);
	    	}
	    },
	    
	    // When in full screen
	    onFullscreen: function(){
	    	// Fullscreen watermark
	    	var watermark = this.getPlugin("watermark");

	    	// Start the watermark interval
	    	watermarkTimer = setInterval(function() {
	    		var width, height, min, x, y, css;

	    		// Show or hide watermark
	    		watermark.toggle();

	    		// Screen size
	    		width = window.innerWidth;
	    		height = window.innerHeight;
	    		min = 20; // 20 padding

	    		// Generate random width and height
	    		x = Math.floor(Math.random() * (width - min)) + min;
	    		y = Math.floor(Math.random() * (height - min)) + min;

	    		// Move watermark to new positions
		  		css = {left: x, top: y};
		  		watermark.animate(css, 0);
	    	}, 5000);
	    },

	    // When exiting full screen
	    onFullscreenExit: function(){
	    	// Fullscreen watermark
	    	var watermark = this.getPlugin("watermark");

	   		// Always hide fullscreen watermark
	    	watermark.hide();
	    	
	    	// Clear watermark interval and timeout
	    	clearInterval(watermarkTimer);
	    },

	    // Don't show error messages on player, handle the errors ourselves
	    showErrors: false,

	    // When error occurs
	    onError: function(errorCode, errorMessage){
	    	// Stream not found
	    	if(errorCode === 200){
	    		// Switch back to flv
	    		this.setClip({
		        url: result.video_name,
		        scaling: 'fit',
		        provider: 'hddn'
			    });
			    this.play();
	    	}
	    }
		});*/
    var sources_mp4 = result.sources_fame_mp4;
    var sources_m3u8 = result.sources_fame_m3u8;
    var key_flow = result.key_flowplayer;
    var container = document.getElementById("news-video");
    // opciones
    var optionss = {
    key: key_flow,      
    ratio: 9/16,
    clip: {
      sources: [
        // { type: "video/mp4",
          // src:  sources_mp4 },
        { type: "application/x-mpegURL",
          src:  sources_m3u8 }
      ],          
      scaling: 'fit',
      // configure clip to use hddn as our provider, referring to the rtmp plugin
      provider: 'hddn'          
    },
    // streaming plugins
    plugins: {
      // controller bar
      controls:  {
        backgroundGradient: 'none',
        backgroundColor: 'transparent',
        timeColor: '#FFFFFF',
        timeSeparator: ' / ',
        durationColor: '#FFFFFF',
        timeBgColor: 'rgb(0,0,0, 0.2)',
        scrubber: true,
        height: 45
      },
      // rtmp plugin configuration
      hddn: {
        url: content.rtmp_plugin,
        
        // define where the streams are found
        netConnectionUrl: content.rtmp
      },
      // watermark plugin
      watermark: {
        url: 'flowplayer.content-3.2.9.swf',
        bottom: 40,
        left: 5,
        width: 130,
        height: 25,
        padding: 3,
        borderRadius: 10,
        border: '0px',
        backgroundColor: 'rgba(0,0,0,0)',
        opacity: 0.8,
        html: '<p>'+content.st_id+'</p>',
        style: {
          p: {
            fontSize: 16,
            textAlign: 'center',
            color: '#FFFFFF',
            fontWeight: 'bold'
          }
        },
        // don't display on video start
        display: 'none'
      }
    },
  
    rtmp: "rtmp://s3b78u0kbtx79q.cloudfront.net/cfx/st",
    // loop playlist
    loop: false,
    keyboard:true,
    embed:false,
    share:false,
    canvas: {
      backgroundGradient: 'none'
    },

    // Load settings before video
    onBeforeLoad: function(){
      // Wrap in responsive css
      this.getParent().className = "embed-container";
    },

    // Load settings after video is ready
    onLoad: function(){
      var parentNode = $("#"+this.getParent().id);

      // Add watermark
      if($("#div_watermark").length === 0){
        parentNode.prepend(content.watermark);
      }
    },
    // When in full screen
    onFullscreen: function(){
      // Fullscreen watermark
      var watermark = this.getPlugin("watermark");

      // Start the watermark interval
      watermarkTimer = setInterval(function() {
        var width, height, min, x, y, css;

        // Show or hide watermark
        watermark.toggle();

        // Screen size
        width = window.innerWidth;
        height = window.innerHeight;
        min = 20; // 20 padding

        // Generate random width and height
        x = Math.floor(Math.random() * (width - min)) + min;
        y = Math.floor(Math.random() * (height - min)) + min;

        // Move watermark to new positions
        css = {left: x, top: y};
        watermark.animate(css, 0);
      }, 5000);
    },

    // When exiting full screen
    onFullscreenExit: function(){
      // Fullscreen watermark
      var watermark = this.getPlugin("watermark");
      alert('exit');
      // Always hide fullscreen watermark
      watermark.hide();
      
      // Clear watermark interval and timeout
      clearInterval(watermarkTimer);
    }
  
    };
    
    // Neccesary to watermarker
    flowplayer.conf = {
      splash: true
    };
    // install flowplayer into selected container
    flowplayer(container, optionss)
     // WaterMarke fullscreen and fullscreen-exit
     .on("fullscreen fullscreen-exit", function (e, api) {
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
    // Add a border
    modalBody.append("<div style='border:1px solid #E3E3E3; margin:10px 0;'></div>");
	}

	// Add news content
	modalBody.append(result.content);
</script>
