<!DOCTYPE html>
<!--<html lang="en-us">-->
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
    <!--Lo secomente para el tamaño del aimagen en facebook-->
		<meta charset="utf-8" >    
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title> Vancouver Animation School Online Campus </title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel='shortcut icon' href='http://vanas.ca/templates/jm-me/favicon.ico'>
		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/bootstrap.min.css" >
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/font-awesome.min.css">
		<!-- SmartAdmin Style -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/smartadmin-production.css">
		<!-- Vanas Style -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/vanas.css">
		<!-- Flowplayer -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/flowplayer/playful.css">

		<?php

			if ($page_css) {
				foreach ($page_css as $css) {
					echo '<link rel="stylesheet" type="text/css" media="screen" href="'.PATH_N_COM_CSS.'/'.$css.'">';
				}
			}
		?>
		<!-- GOOGLE FONT -->
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
		<!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- FACEBOOK JS -->
		<!--<script src="<?php echo PATH_N_COM_JS; ?>/facebook.js.php"></script>-->
	</head>
	<body 
		<?php
			if ($page_body_prop) {
				foreach ($page_body_prop as $prop_name => $value) {
					echo $prop_name.'="'.$value.'" ';
				}
			}
		?>
	>
	<!-- POSSIBLE CLASSES: minified, fixed-ribbon, fixed-header, fixed-width-->
	<?php
		if (!$no_main_header) {
	?>

	<header id="vanas-header">
		<div id="logo-group" class="pull-left">

			<!-- LOGO -->
			<span id="logo"><img src="<?php echo PATH_N_COM_IMAGES; ?>/logo.jpg" alt="Vanas Logo"></span>
			<span id="activity" class="activity-dropdown pull-left"> <i class="fa fa-user"></i> <b class="badge">0</b> </span>

			<div class="ajax-dropdown">
				<div class="btn-group btn-group-justified" data-toggle="buttons">
					<label class="btn btn-default">
						<input type="radio" name="activity" id="<?php echo PATH_N_ALU_PAGES; ?>/notify/messages.php">
						Messages <span class='notice-count'><?php if(!empty($no_messages)){echo "(<span id='no_messages'>".$no_messages."</span>)";} ?></span>
					</label>
					<label class="btn btn-default active">
						<input type="radio" name="activity" id="<?php echo PATH_N_ALU_PAGES; ?>/notify/notifications.php">
						Notices <span class='notice-count'><?php if(!empty($no_notices)){echo "(<span id='no_notices'>".$no_notices."</span>)";} ?></span>
					</label>
					<label class="btn btn-default">
						<input type="radio" name="activity" id="<?php echo PATH_N_ALU_PAGES; ?>/notify/progress.php">
						Progress 
					</label>
				</div>
				<div class="ajax-notifications custom-scroll"></div>
			</div>

		</div>
    
		<div class="pull-right">
			<div class="btn-header pull-right">
				<span> <a href="#" title="Sign Out" data-logout-msg="Good Bye!"><i class="fa fa-sign-out"></i></a> </span>
			</div>

			<div id="clock" class="btn-header pull-right">
				<span> <a href="#clock-container" data-toggle="collapse"><i class="fa fa-clock-o"></i> </a> </span>
			</div>
		</div>
  </header>
	<!-- END HEADER -->

	<?php
		}
	?>