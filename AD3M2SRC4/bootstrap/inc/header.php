<!DOCTYPE html>
<html lang="en-us" >
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

		<title> <?php echo $page_title != "" ? $page_title : ""; ?></title>
		<meta name="description" content="">
		<meta name="author" content="">

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- ICH 05/10/16: Librerias para usar CKEDITOR -->
    <link rel="stylesheet" type="text/css" href="<?php echo PATH_JS; ?>/ckeditor/ckeditor/samples/css/samples.css" />
    <script src="<?php echo PATH_JS; ?>/ckeditor/ckeditor/ckeditor.js"></script>
    
		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/font-awesome.min.css">

		<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/smartadmin-production-plugins.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/smartadmin-production.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/smartadmin-skins.min.css">

		<!-- SmartAdmin RTL Support is under construction-->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/smartadmin-rtl.min.css">

		<!-- We recommend you use "your_style.css" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.-->
		<!--<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/your_style.css">-->
    
		<!-- ESTILOS ANTERIORES -->
		<!--<link type='text/css' href='<?php echo PATH_CSS; ?>/theme/jquery-ui-1.8rc3.custom.css' rel='stylesheet' />-->
    <!-- Quitamos los estios del template anterior para agregar los nuevos -->
		<!--<link type='text/css' href='<?php echo PATH_CSS; ?>/estilos.css' media='screen' rel='stylesheet' />-->
		<link type='text/css' href='<?php echo PATH_CSS; ?>/fileuploader.css' rel='stylesheet' />
		<link type='text/css' href='<?php echo PATH_CSS; ?>/jquery.lovs.css' rel='stylesheet' />
		<link type='text/css' href='<?php echo PATH_CSS; ?>/separadores.css' media='screen' rel='stylesheet' />
		<link type='text/css' href='<?php echo PATH_CSS; ?>/colorbox.css' media='screen' rel='stylesheet' />
		<link type='text/css' href='<?php echo PATH_CSS; ?>/procesos.css' rel='stylesheet' />
		<!-- END CSS ANTERIORES -->
    
		<?php

			if (isset($page_css)) {
				foreach ($page_css as $css) {
					echo '<link rel="stylesheet" type="text/css" media="screen" href="'.PATH_HOME.'/bootstrap/css/'.$css.'">';
				}
			}
		?>


		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/demo.min.css">

		<!-- #FAVICONS -->
		<link rel="shortcut icon" href="https://vanas.ca/templates/jm-me/favicon.ico" type="image/x-icon">
		<link rel="icon" href="https://vanas.ca/templates/jm-me/favicon.ico" type="image/x-icon">

		<!-- GOOGLE FONT -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

		<!-- Specifying a Webpage Icon for Web Clip
			 Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
		<link rel="apple-touch-icon" href="<?php echo PATH_HOME; ?>/bootstrap/img/splash/sptouch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo PATH_HOME; ?>/bootstrap/img/splash/touch-icon-ipad.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php echo PATH_HOME; ?>/bootstrap/img/splash/touch-icon-iphone-retina.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php echo PATH_HOME; ?>/bootstrap/img/splash/touch-icon-ipad-retina.png">

		<!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">

		<!-- Startup image for web apps -->
		<link rel="apple-touch-startup-image" href="<?php echo PATH_HOME; ?>/bootstrap/img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
		<link rel="apple-touch-startup-image" href="<?php echo PATH_HOME; ?>/bootstrap/img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
		<link rel="apple-touch-startup-image" href="<?php echo PATH_HOME; ?>/bootstrap/img/splash/iphone.png" media="screen and (max-device-width: 320px)">

		<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script>
			if (!window.jQuery) {
				document.write('<script src="<?php echo PATH_HOME; ?>/bootstrap/js/libs/jquery-2.1.1.min.js"><\/script>');
			}
		</script>

		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script>
			if (!window.jQuery.ui) {
				document.write('<script src="<?php echo PATH_HOME; ?>/bootstrap/js/libs/jquery-ui-1.10.3.min.js"><\/script>');
			}      
		</script>
		<script type="text/javascript">
      $("#menu").addClass("active");
		</script>       
		<!-- SCRIPTS DEL VANAS ANTERIOR -->
		<script type='text/javascript' src='<?php echo PATH_JS; ?>/tiny_mce/tiny_mce.js'></script>
		<script type='text/javascript' src='<?php echo PATH_JS; ?>/fileuploader.js'></script>
		<script type='text/javascript' src='<?php echo PATH_JS; ?>/jquery.MultiFile.js'></script>
		<script type='text/javascript' src='<?php echo PATH_JS; ?>/jquery-1.4.2.min.js'></script>
		<script type='text/javascript' src='<?php echo PATH_JS; ?>/jquery-ui-1.8rc3.custom.min.js'></script>
		<script type='text/javascript' src='<?php echo PATH_JS; ?>/jquery.lovs.js.php'></script>
		<!--<script type='text/javascript' src='<?php echo PATH_JS; ?>/colorbox.js'></script>-->
		<script type='text/javascript' src='<?php echo PATH_JS; ?>/jquery.colorbox.js'></script>
		<script type='text/javascript' src='<?php echo PATH_JS; ?>/d3-3.5.5.min.js'></script>
    <script type='text/javascript' src='<?php echo PATH_JS; ?>/sendtemplate.js.php'></script>
		<!--END VANAS ANTERIOR -->
    <!-- ELIMINAMOS EL HEADER DEL DIALOG -->
    <style>
      .ui-dialog-titlebar-close{
        display: none;
      }
      .ui-dialog-titlebar{
        display: none;
      }
    </style>
	</head>
	<body class=" desktop-detected smart-style-0 pace-done fixed-header fixed-navigation fixed-ribbon">
		<!-- POSSIBLE CLASSES: minified, fixed-ribbon, fixed-header, fixed-width
		You can also add different skin classes such as "smart-skin-1", "smart-skin-2" etc...-->
		<?php      
			if (!isset($no_main_header)) {     
		?>
				<!-- HEADER -->
        <header id="header">
          <div id="logo-group"  class="superbox">

            <!-- PLACE YOUR LOGO HERE -->
            <span> <img class="superbox-current-img padding-10" src="<?php echo SP_IMAGES_W."/".ObtenNombreImagen(20); ?>" alt="<?php echo ETQ_TITULO_ADMON; ?>"> </span>
            <!-- END LOGO PLACEHOLDER -->
            
          </div>
          
          
          <!-- pulled right: nav area -->
          <div class="pull-right">          
            <!-- collapse menu button -->
            <div id="hide-menu" class="btn-header pull-right">
              <span> <a href="javascript:void(0);" title="Collapse Menu" data-action="toggleMenu"><i class="fa fa-reorder"></i></a> </span>
            </div>
            <!-- end collapse menu -->

            <!-- logout button -->
            <div id="logout" class="btn-header transparent pull-right">
              <!--<span> <a href="<?php echo PATH_HOME; ?>/login.php" title="Sign Out" data-action="userLogout" data-logout-msg="You can improve your security further after logging out by closing this opened browser"><i class="fa fa-sign-out"></i></a> </span>-->
              <span> <a href="<?php echo SP_HOME_W;?>/logout.php" title="<?php echo ETQ_SALIR; ?>" data-action="userLogout" data-logout-msg="You can improve your security further after logging out by closing this opened browser"><i class="fa fa-sign-out"></i></a> </span>
            </div>
            <!-- end logout button -->						

            <!-- fullscreen button -->
            <div id="fullscreen" class="btn-header transparent pull-right">
              <span> <a href="javascript:void(0);" title="Full Screen" data-action="launchFullscreen"><i class="fa fa-arrows-alt"></i></a> </span>
            </div>
            <!-- end fullscreen button -->
            
            <!-- Visitar Vanas version v1 button -->
            <div id="version_v1" class="btn-header transparent pull-right">
              <span><a href="javascript:void(0);" id="vanas_v1"><i class="fa fa-vimeo-square"></i></a></span>
            </div>
            <!-- Visitar Vanas version v1 button -->

          </div>
          <!-- end pulled right: nav area -->

        </header>
        <!-- END HEADER -->

		<?php
			}
		?>