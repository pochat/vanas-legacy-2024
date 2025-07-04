<?php

	# Header para todas las paginas
	function CreateMenu() {
		$fl_usuario = ObtenUsuario(False);
		$fl_perfil = ObtenPerfil($fl_usuario);
		$nb_usuario = ObtenNombreUsuario($fl_usuario);
		$ruta_avatar = ObtenAvatarUsuario($fl_usuario);

		# Initializes the left menu list
		$page_nav = array();

		# Presenta menu en Columna izquierda
		if($fl_perfil == PFL_MAESTRO) {
			$menu = MENU_MAESTROS;
			$path_nodo = PAGINA_NOD_MAE;
			$pag_fija = 6;
			$pending_assignments = "";
            $fl_moduloo = "";
            $fl_funcionn = "";
		} else {
			$menu = MENU_ALUMNOS;
			$path_nodo = PAGINA_NOD_ALU;
			$pag_fija = 4;
			# Obtenemos si el alumno ya se graduo o no jgfl
	        # Si se graduo muestra solo el payment history
	        # En caso contrario muestra todo el menu
	        $Query  = "SELECT fg_graduacion, fg_activo FROM k_pctia a, c_usuario b ";
	        $Query .= "WHERE a.fl_alumno=b.fl_usuario AND b.fl_perfil= ".PFL_ESTUDIANTE." AND fl_alumno = $fl_usuario ";
	        $row = RecuperaValor($Query);
	        $fg_activo = $row[1];
            if(!empty($fg_activo)){
              $fl_moduloo = "";
              $fl_funcionn = "";
            }
            else{
              $fl_moduloo = "AND fl_modulo IN(27,15,16) ";
              $fl_funcionn = "AND fl_funcion IN(98, 102,104) ";//42->grade hidden 04-07-2025 by Erika
            }
		}

	  # Recupera las descripciones de los modulos
	  $Query  = "SELECT fl_modulo, nb_modulo, tr_modulo ";
	  $Query .= "FROM c_modulo ";
	  $Query .= "WHERE fl_modulo_padre=$menu ";
	  $Query .= "AND fg_menu='1' ".$fl_moduloo; //jgfl
	  $Query .= "ORDER BY no_orden";
	  $rs = EjecutaQuery($Query);
	  for($i = 1; $row = RecuperaRegistro($rs); $i++) {
	    $fl_modulo[$i] = $row[0];
	    $nb_modulo[$i] = str_texto(EscogeIdioma($row[1], $row[2]));
	    $Query  = "SELECT fl_funcion, nb_funcion, tr_funcion, nb_flash_default, tr_flash_default ";
	    $Query .= "FROM c_funcion ";
	    $Query .= "WHERE fl_modulo=$fl_modulo[$i] ";
	    $Query .= "AND fg_menu='1' ".$fl_funcionn." "; //jgfl
	    $Query .= "ORDER BY no_orden";
	    $rs2 = EjecutaQuery($Query);
	    for($j = 1; $row2 = RecuperaRegistro($rs2); $j++) {
	      $fl_funcion[$i][$j] = $row2[0];
	      $nb_funcion[$i][$j] = str_texto(EscogeIdioma($row2[1], $row2[2]));
	      $nb_icono[$i][$j] = str_uso_normal(EscogeIdioma($row2[3], $row2[4]));
	    }
	    $tot_submodulos[$i] = $j-1;
	  }
	  $tot_modulos = $i-1;

	  # MDB Livesession
		$liveSessionDisplay = "";
		$clavesModulosTeacher = Array();
		$clavesModulosTeacher["liveSession"] = 76;
		$clavesModulosTeacher["desk"] = 13;
		$clavesModulosStudent = Array();
		$clavesModulosStudent["liveSession"] = 45;
		$clavesModulosStudent["desk"] = 16;

		# Find the number of pending submitted assignments
		if($fl_perfil == PFL_MAESTRO) {
      $Query  = "SELECT COUNT(1) ";
      $Query .= "FROM k_entrega_semanal a, c_grupo b ";
      $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
      $Query .= "AND a.fg_entregado='1' ";
      $Query .= "AND (a.fl_promedio_semana IS NULL OR a.fl_promedio_semana='') ";
	  $Query .= "AND  EXISTS (SELECT 1 FROM k_alumno_grupo g 
                 LEFT JOIN c_usuario h ON(h.fl_usuario=fl_alumno   ) WHERE h.fg_activo = '1' AND g.fl_grupo =  a.fl_grupo) ";
      $Query .= "AND b.fl_maestro=$fl_usuario";
      $row = RecuperaValor($Query);

      $pending_assignments = $row[0];
    }

	  # Form the menu list array
		for($i = 1; $i <= $tot_modulos; $i++) {
			# Initialize sub array
			$sub_nav = array();

		  # Populate the sub module array first
			for($j = 1; $j <= $tot_submodulos[$i]; $j++) {
				if ( $fl_funcion[$i][$j] != $clavesModulosTeacher["liveSession"] && $fl_funcion[$i][$j] != $clavesModulosStudent["liveSession"]) 
				{
					if(!empty($nb_icono[$i][$j])){
						$nav_icon = "<img src='".SP_IMAGES."/".$nb_icono[$i][$j]."' width='16' height='16'>";
					} else {
						$nav_icon = "";
					}

					if($nb_funcion[$i][$j] == "Submitted Assignments" && !empty($pending_assignments)){
						$sub_nav += array(
							strtolower($nb_funcion[$i][$j]) => array(
								"title" => $nb_funcion[$i][$j],
			          "url" => "ajax/node.php?node=".$fl_funcion[$i][$j],
			          "nav_icon" => $nav_icon,
			          "label_htm" => "<span class='badge pull-right bg-colour-red inbox-badge'>$pending_assignments</span>"
			        )
						);
					} else {
						$sub_nav += array(
							strtolower($nb_funcion[$i][$j]) => array(
								"title" => $nb_funcion[$i][$j],
			          "url" => "ajax/node.php?node=".$fl_funcion[$i][$j],
			          "nav_icon" => $nav_icon
			        )
						);
					}
				}
		  }
		  $page_nav += array(
		  	strtolower($nb_modulo[$i]) => array(
		  		"title" => $nb_modulo[$i],
		  		"icon" => "",
		  		"sub" => $sub_nav
		  	)
		  );
		}
	  return $page_nav;
	}
	
	# Present video functions

	# Capa con marca de agua para Video Lectures
	function PresentWatermark($p_watermark) {
		$watermark = 
	  	"<div id='div_watermark' style='position: absolute; top: 230; left: 200; z-index: 2000; font-size: 20; opacity:0.5; color: #FFF;' >
	  		$p_watermark
	  	</div>
	  	<script type='text/javascript'>
	  	timer = setInterval(function() {
	  		var aleat = Math.random() * (405 - 20); // 405 alto del video - 20 alto de la etiqueta
	  		aleat = Math.round(aleat);
	  		$('#div_watermark').css('top', parseInt(220) + aleat);
	  		aleat = Math.random() * (720 - 100); // 720 ancho del video - 100 ancho de la etiqueta
	  		aleat = Math.round(aleat);
	  		$('#div_watermark').css('left', parseInt(200) + aleat);
	  		aleat = Math.random() * (20 - 12); // el font estara entre 12 y 20
	  		aleat = Math.round(aleat);
	  		$('#div_watermark').css('font-size', parseInt(12) + aleat);
	  		$('#div_watermark').html('$p_watermark');
	  		espera = setTimeout(\"$('#div_watermark').html('')\", 5000);
	  	}, 10000);
	  	
	  	</script>";
		return $watermark;
	}

	function PresentVideoHTML5($p_path, $p_file, $p_width='720', $p_height='405', $p_id='recordCritique') { 
		$video = 
			"<video tabindex='0' id='$p_id' width='$p_width' height='$p_height' controls='controls'>
				<source src='".$p_path.$p_file."' type='video/mp4'>
				<source src='".$p_path.$p_file."' type='video/ogg'>
			</video>
			<div id='seekInfo' style='display:none;'></div>
			<script type='text/javascript' src='".PATH_LIB."/js_player/smpte_test_universal.js'></script>
			<script type='text/javascript' src='".PATH_LIB."/js_player/jquery.jkey-1.2.js'></script>";

		if($p_id == 'recordCritique') {
			$video .= 
				"<script type='text/javascript' src='".PATH_COM_JS."/recordCritique.js'></script>
				<script>
					var div_aux = $('<div />').appendTo('body'); 
					div_aux.attr('id', 'libCritiqueIncluidas');
					div_aux.css('display', 'none');
					$('#libCritiqueIncluidas').html('True');
				</script>";    
		}
		return $video;
	}

	# TO BE DELETED
	function PresentVideoHTML5Critique($p_path, $p_file) {
		$video = 
			"<video id='one' width='720' height='405' controls='controls'>
				<source src='".$p_path."".$p_file."' type='video/mp4'>
				<source src='".$p_path."".$p_file."' type='video/ogg'>
			</video>
			<script type='text/javascript' src='".PATH_COM_JS."/critiquevideos.js'></script>";

		return $video;
	}

	# TO BE DELETED
	function PresentVideoHTML5Webcam($p_path, $p_file) {
		/*$webcam_top     = "10px";
		$webcam_left    = "10px";
		$webcam_width   = "250px";
		$webcam_height  = "188px";*/

		/*$video =
			"<div style='position:absolute;width:$webcam_width;height:$webcam_height;top:$webcam_top;left:$webcam_left;'>
				<video id='two' width='250' height='188'>
					<source src='".$p_path."".$p_file."' type='video/ogg'>
				</video>
			</div>";*/

		$video = "
			<video id='two' width='250' height='188'>
				<source src='".$p_path."".$p_file."' type='video/mp4'>
				<source src='".$p_path."".$p_file."' type='video/ogg'>
			</video>";

		return $video;
	}

	# TO BE DELETED
	function PresentVideo($path, $file, $p_width=720, $p_height=405) {
  	$video =
	  	"<script type='text/javascript'>
		  	var flashvars = {};
		       // video width
		  	var videoWidth = $p_width;
		       // video height
		  	var videoHeight = $p_height;
		       // Main Video Path
		  	flashvars.videoFilePath = '".$path."".$file."';
		       // Video buffer time (seconds)
		  	flashvars.videoBufferTime = '5'
		       // automatically start video playing when first start video player. (yes/no)
		  	flashvars.autoStartVideoPlay = 'no';
		       // Auto Repeat at end of the video. (yes/no)
		  	flashvars.autoRepeat = 'no';
		       // Video starting volume (Max: 100 Min: 0)
		  	flashvars.videoStartVolume= '75';
		       // Show Advertisement video (yes/no)
		  	flashvars.showAdvertisementVideo = 'no';
		       // Advertisement Video Path
		  	flashvars.advertisementVideoPath = '".SP_VIDEOS."';
		       // Video Title Text
		  	flashvars.titleTxt = \"<font color='#9999FF' size='15'>Vancouver Animation School</font>\";
		       // Video Description Text
		  	flashvars.descriptionTxt = '';
		       // Show Logo
		  	flashvars.logoDisplay = 'no';
		       // Logo Image Path
		  	flashvars.logoImagePath = '".SP_IMAGES."/logo.jpg';
		       // Logo Position
		  	flashvars.logoPlacePosition = 'top-left';
		       // Logo Margin Space
		  	flashvars.logoMargin = '20';
		       // Logo Width
		  	flashvars.logoWidth = '86';
		       // Logo Height
		  	flashvars.logoHeight = '38';
		       // Logo Transparency Value (100 : Solid  50 : Semi Transparency)
		  	flashvars.logoTransparency = '60';
		       // Define video bar color (blue, green, orange, purple, white, red)
		       // random : it will select color randomly
		  	flashvars.videoBarColorName = 'white';
		       // Define volume bar color (blue, green, orange, purple, white, red)
		       // random : it will select color randomly
		  	flashvars.volumeBarColorName = 'white';
		       // Show Cover Image
		  	flashvars.coverImageDisplay = 'no';
		       // cover image path. You can use SWF, PNG, JPG or GIF
		  	flashvars.coverImagePath = '".SP_IMAGES."/';
		       // Auto Hide Control Panel and Mouse
		       // flashvars.hideControlPanelAndMouse='yes'
		  	flashvars.hideControlPanel='yes'
		  	flashvars.hideMouse='no'
		       // Auto Hide time (second)
		  	flashvars.hideTime='1'
		  	var params = {};
		  	params.scale = 'exactfit';
		  	params.allowfullscreen = 'false';
		  	params.salign = 't';
		  	params.bgcolor = '000000';
		  	params.wmode = 'opaque';

		  	var attributes = {};
		  	swfobject.embedSWF('".SP_FLASH."/video_player8_flashvars.swf', 'myContent$file', videoWidth, videoHeight, '9.0.0', false, flashvars, params, attributes);
	  	</script>
	  	<div id='myContent$file'>
	  		<h1>Alternative content</h1>
	  		<p><a href='http://www.adobe.com/go/getflashplayer'><img src='http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a></p>
	  	</div>";

  	return $video;
	}

	# end video functions

	# Record critique functions

	# Present canvas toolbar 
	function PresentToolBar( ) {
  
	  $toolbar = "
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/cp_depends.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/CanvasWidget_new.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/CanvasPainter.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/CPWidgets.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/CPAnimator.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/CPDrawing.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_jpicker/jpicker-1.1.6.min.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/pizarron_new.js'></script>
		  <link rel='stylesheet' href='".PATH_LIB."/js_jpicker/jPicker-1.1.6.min.css' />
		  <link rel='stylesheet' href='".PATH_LIB."/js_paint/pizarron_new.css' />
		  
		  <div id='paint'>
		    <div id='controls'>
		      <div class='ctr_btn' id='btn_0' style='background: #CCCCCC;' onclick='setCPDrawAction(0)' onMouseDown=\"setControlLook(0, '#CCCCCC')\" onMouseOver=\"setControlLook(0, '#EEEEEE')\" onMouseOut=\"setControlLook(0, '#FFFFFF')\" title='Pencil'><img src='".PATH_COM_IMAGES."/brush1.png'/></div>
		      <div class='ctr_btn' id='btn_1' onclick='setCPDrawAction(1)' onMouseDown=\"setControlLook(1, '#CCCCCC')\" onMouseOver=\"setControlLook(1, '#EEEEEE')\" onMouseOut=\"setControlLook(1, '#FFFFFF')\" title='Brush'><img src='".PATH_COM_IMAGES."/brush2.png'/></div>
		      <div class='ctr_btn' id='btn_2' onclick='setCPDrawAction(2)' onMouseDown=\"setControlLook(2, '#CCCCCC')\" onMouseOver=\"setControlLook(2, '#EEEEEE')\" onMouseOut=\"setControlLook(2, '#FFFFFF')\" title='Line'><img src='".PATH_COM_IMAGES."/line.png'/></div>
		      <div class='ctr_btn' id='btn_3' onclick='setCPDrawAction(3)' onMouseDown=\"setControlLook(3, '#CCCCCC')\" onMouseOver=\"setControlLook(3, '#EEEEEE')\" onMouseOut=\"setControlLook(3, '#FFFFFF')\" title='Rectangle'><img src='".PATH_COM_IMAGES."/rectangle.png'/></div>
		      <div class='ctr_btn' id='btn_4' onclick='setCPDrawAction(4)' onMouseDown=\"setControlLook(4, '#CCCCCC')\" onMouseOver=\"setControlLook(4, '#EEEEEE')\" onMouseOut=\"setControlLook(4, '#FFFFFF')\" title='Circle'><img src='".PATH_COM_IMAGES."/circle.png'/></div>
		      <div class='ctr_btn' id='btn_5' onclick='setCPDrawAction(5)' onMouseDown=\"setControlLook(5, '#CCCCCC')\" onMouseOver=\"setControlLook(5, '#EEEEEE')\" onMouseOut=\"setControlLook(5, '#FFFFFF')\" title='Erase'><img src='".PATH_COM_IMAGES."/erase.gif'/></div>
		      <div class='ctr_btn' id='togglePizarron'><img src='".PATH_COM_IMAGES."/onoffon.png' title='Turn Off'/></div>
		      <div class='ctr_btn' id='selectLineWidth' title='Select line width'><img src='".PATH_COM_IMAGES."/selectLine.png'/></div>
		      <div class='ctr_btn' id='selectColor' title='Select color' style='background-color: #124cc7;'></div>
		    </div>
		    
		    <div id='chooserWidgets'>
		      <canvas id='lineWidthChooser' width='275' height='76' style='display:none;'></canvas>
		    </div>
		    <div id='dlgJPicker'><div id='Expandable'></div></div>
		  </div>
		  ";

	  return $toolbar;
	}

	function PresentCanvas(){
		$canvas = "
			<div id='canvas-container' title='Drawing Area'>
	    	<canvas id='canvas' width='720' height='375'></canvas>
	    	<canvas id='canvasInterface' width='720' height='375'></canvas>
	    </div>
	    ";
	  return $canvas;
	}

	function PresentWebcam($p_entrega_semanal) {
	  $webcam_top     = "5px";
	  $webcam_left    = "10px";
	  $webcam_width   = "250px";
	  $webcam_height  = "188px";
	  $nombreArchivoJNLP = ObtenNombreArchivoJNLP($p_entrega_semanal);
	  
	  $webcam = "
	  	<div id='broadcaster' style='position:absolute;width:$webcam_width;height:$webcam_height;top:$webcam_top;left:$webcam_left;'>No Flash</div>
	  	<div style='position:absolute;top:195px;background-color:#fed;'><a href='".SP_HOME.DIRECTORIO_JAR."/".$nombreArchivoJNLP."'><img src='".PATH_COM_IMAGES."/record.png' title='Start recording'/></a></div>";

	  return json_encode($webcam);
	}

	# end record critique functions

?>