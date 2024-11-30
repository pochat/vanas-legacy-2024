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
  	
	function ContentQuery($fl_alumno){
		
		# Recibe parametros
		$fixed_page = RecibeParametroNumerico('page', True);
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);
		if(empty($fixed_page)) {
	    MuestraPaginaError(ERR_SIN_PERMISO);
	    exit;
		}
		
		# Recupera contenido de la pagina fija
		$Query  = "SELECT ds_titulo, tr_titulo, ds_contenido, tr_contenido, fg_fijo ";
		$Query .= "FROM c_pagina ";
		$Query .= "WHERE cl_pagina=$fixed_page ";
		$Query .= "AND (fl_programa=$fl_programa OR fl_programa=0) ";
		$Query .= "AND no_grado<=$no_grado ";
		$Query .= "AND (no_grado=$no_grado OR no_grado=0) ";
		$Query .= "ORDER BY fl_programa DESC, no_grado DESC";
		$rs = EjecutaQuery($Query);

		return $rs;
	}

	$rs = ContentQuery($fl_alumno);
	while($row = RecuperaRegistro($rs)) {
		$titulo = str_uso_normal(EscogeIdioma($row[0], $row[1]));
		$contenido .= str_uso_normal(EscogeIdioma($row[2], $row[3]));
	}
  
  # Obtenemos los videos
  function ContentVideos($fl_alumno){
    # Recibe parametros
		$fixed_page = RecibeParametroNumerico('page', True);
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);
		if(empty($fixed_page)) {
	    MuestraPaginaError(ERR_SIN_PERMISO);
	    exit;
		}
    
    # Recupera videos
		$Query  = "SELECT ds_ruta_video, fl_video_contenido, ds_title_vid, ds_duration ";
		$Query .= "FROM k_video_contenido ";
		$Query .= "WHERE cl_pagina=$fixed_page ";
		$Query .= "AND (fl_programa=$fl_programa OR fl_programa=0) ";
		$Query .= "AND no_grado<=$no_grado ";
		// $Query .= "AND (no_grado=$no_grado) ";
		$Query .= "ORDER BY fl_programa DESC, no_grado DESC";
		$rs = EjecutaQuery($Query);
    
    return $rs;
  }
  
  $rsv = ContentVideos($fl_alumno);
  $tot_videos = CuentaRegistros($rsv);
  $videos = "<div clas='text-align-center'><hr><p><h1><strong>Videos</strong></h1></p><hr></div>";
  $fixed_page = RecibeParametroNumerico('page', True);
	$fl_programa = ObtenProgramaAlumno($fl_alumno);
  $no_grado = ObtenGradoAlumno($fl_alumno);
  // $ruta = ObtenConfiguracion(121)."/vanas_videos/campus/library/video_".$fixed_page."_".$fl_programa."_".$no_grado;
  $ruta = ObtenConfiguracion(121)."/vanas_videos/campus/student_library";
  $j=1;
  for($i=1;$row=RecuperaRegistro($rsv);$i++){
    $ds_ruta_video = $row[0];  
    $fl_video_contenido = $row[1];
    $ds_title_vid = str_texto($row[2]);
    $ds_duration = str_texto($row[3]);
    $ruta_img = $ruta."/video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/img_1.png";        
    $handle = @fopen($ruta_img,'r');
    if($handle !== false)
      $ruta_img = $ruta."/video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/img_1.png"; 
    else
       $ruta_img = ObtenConfiguracion(121)."/images/PosterFrame_White.jpg"; 
    $j++;
    # Videos
    $videos .= "<div class='col-sm-12 col-md-12 col-lg-4 cursor-pointer' onclick='ShowVideo(".$fl_video_contenido.")'><div id='myCarousel-2' class='carousel slide'><div class='carousel-inner'>";
    $videos .= "<div class='item active' style='width:auto;'>";
    $videos .= "<img class='cursor-pointer' src='".$ruta_img."' alt='' style='border-style:solid; border-width:2px;border-color:#BDBDBD;' >";
    $videos .= "<div class='carousel-caption no-margin' style='padding-bottom:40px;'>";
    $videos .= "<span class='glyphicon glyphicon-play-circle' style='font-size:80px;'>";
    $videos .= "</div>";
    $videos .= "<span class='label label-info bg-color-darken pull-right' style='position:relative;bottom:30px;right:5px;font-size:12px;display:inline;'>".$ds_duration."</span>";
    $videos .= "</div>";
    $videos .= "</div></div><div class='text-align-left padding-left-5'><h5 style='font-size:15px; height:60px;' class='no-margin'><strong>".$ds_title_vid."</strong></h5></div></div>";
  }
  
  
  

	# Settings for flowplayer
	$ds_matricula = ObtenMatriculaAlumno($fl_alumno);
	$playerSetting["rtmp"] = "rtmp://".ObtenConfiguracion(60)."/oflaDemo";
	$playerSetting["rtmp_plugin"] = SP_FLASH."/flowplayer.rtmp-3.2.13.swf";
	$playerSetting["player"] = SP_FLASH."/flowplayer.commercial-3.2.18.swf";
	$playerSetting["player_img"] = SP_IMAGES."/PosterFrame_PlayIcon.jpg";
	$playerSetting["watermark"] = PresentWatermark($ds_matricula);
	$playerSetting["st_id"] = $ds_matricula;
?>

<!-- Content container -->
<div class="row">
	<div class="col-xs-12">
		<div class="well well-light no-margin padding-10">
      <div class="well well-light no-margin"><div class="row" id="content-container_videos"></div></div>
			<div class="well well-light no-margin" id="content-container"></div>			
		</div>
	</div>
</div>

<!--<div id='dlg_video'><div id='content-video' style='min-height:405px;'></div></div>-->
<!-- Base Modal Muestra videos -->

<div class="modal fade" id="modal-item-container" tabindex="-1" role="dialog" aria-labelledby="item-title" aria-hidden="true">
  <div class="modal-dialog">
  	<!-- contents -->
  	<div class="modal-content"></div>
  </div>
</div>
	
<script type="text/javascript">
	var content, contentContainer;
	content = <?php echo json_encode($contenido); ?>;
	contentContainer = $("#content-container");
	contentContainer.append(content);
	contentContainer.children().attr("align", "center");
  // videos
  var tot_videos = '<?php echo json_encode($tot_videos); ?>';
  var videos = "<?php echo $videos; ?>";
  var content_video = $("#content-container_videos"); 
  if(tot_videos>0)
    content_video.append(videos);
	content_video.children().attr("align", "center");
  
  

  function ShowVideo(video){
    var modalvideos;    
    modalvideos = $("#modal-item-container");
    modalvideos.on("show.bs.modal", function(){
      $(this).find(".modal-content").load("ajax/library_modal.php", "item="+video);
      // Disable window scroll
      // modalvideos.css('overflow-y', 'hidden');
    });
    $('.modal-header').remove();
    $('#item-body').remove();
    $('#item-footer').remove();
    // $('html').css('overflow-y', 'hidden');
    modalvideos.modal("show");
    modalvideos.off("show.bs.modal");
  }
  
/*	$(document).ready(function(){
		$('#dlg_video').dialog({
	  	appendTo: "#content",
	    autoOpen: false,
	    resizable: false,
	    modal: true,
	    width: 748,
	    height: 'auto',
	    hide: 'highlight',
	    buttons: {
	      'Close': function() {
	        $(this).dialog('close');
	      }
	    }
	  });
	  $('.ui-dialog-titlebar').hide();
	});

	// Muestra dialogo para mostrar video
	function ShowVideo(video) {
		var playerSetting = <?php echo json_encode($playerSetting); ?>;
	  
	  // Default 16:9 ratio, carried over from the old campus
		// flashembed is a global flash object from flowplayer
		flashembed.conf.width = "720";
		flashembed.conf.height = "405";

		// Fullscreen watermark
		var watermarkTimer, clearWatermarkText;
		
		// Create flowplayer
		$f("content-video", playerSetting.player, {
			key: '#$79d60288437ade68168',

	    clip: {
        url: 'mp4:'+video,
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
          url: playerSetting.rtmp_plugin,
         	
          // define where the streams are found
          netConnectionUrl: playerSetting.rtmp
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
        	html: '<p>'+playerSetting.st_id+'</p>',
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
	    		parentNode.prepend(playerSetting.watermark);
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
		        url: video,
		        scaling: 'fit',
		        provider: 'hddn'
			    });
			    this.play();
	    	}
	    }
		});

	  $('#dlg_video').dialog('open');
	}*/
</script>
