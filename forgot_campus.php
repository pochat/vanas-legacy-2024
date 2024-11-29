<?php
  
  # Libreria de funciones
  require("lib/sp_general.inc.php");
  require("lib/sp_forms.inc.php");
  
  # Limpia el cookie
  TerminaSesion(False);
  
  # Recibe parametros
  $err = RecibeParametroNumerico('msg', True);
  $url_system=$_GET['s'];
  
  
  
  
  # Presenta mensajes de error
  switch($err) {
    case 1: $err_msg = "Invalid username or email address."; break;
    case 3: $err_msg = "The password was not created because there is no email service available."; break;
    case 4: $err_msg = "Inactive user account."; break;
    case 5: $err_msg = "A new password has been generated and sent to your email."; break;
    default: $err_msg = "&nbsp;";
  }

?>
<!DOCTYPE html>
<html lang="en-us" >
	<head>
		<meta charset="utf-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

		<title> <?php echo $page_title??NULL; ?></title>
		<meta name="description" content="">
		<meta name="author" content="">

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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
		<link rel="shortcut icon" href="http://vanas.ca/templates/jm-me/favicon.ico" type="image/x-icon">
		<link rel="icon" href="http://vanas.ca/templates/jm-me/favicon.ico" type="image/x-icon">

		<!-- GOOGLE FONT -->
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

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
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script>
			if (!window.jQuery) {
				document.write('<script src="<?php echo PATH_HOME; ?>/bootstrap/js/libs/jquery-2.1.1.min.js"><\/script>');
			}
		</script>

		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
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
    <?php
    echo "
    <script type='text/javascript'>

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-20837828-1']);
      _gaq.push(['_setDomainName', '.vanas.ca']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>

    <script type='text/javascript'>

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-27662999-1']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>

    <script type='text/javascript'>
      
      function submitenter(myfield, e) {
        var keycode;
        if(window.event)
          keycode = window.event.keyCode;
        else if(e)
          keycode = e.which;
        else
          return true;
      
        if(keycode == 13) {
          document.datos.submit();
          return false;
        }
        else
          return true;
      }

    </script>
    <script type='text/javascript'>
    var sc_project=6551997; 
    var sc_invisible=1; 
    var sc_security='1194400a'; 
    </script>
    <script type='text/javascript' src='http://www.statcounter.com/counter/counter.js'></script>
    <noscript><div class='statcounter'><a title='vBulletin stat' href='http://statcounter.com/vbulletin/' target='_blank'><img class='statcounter'
    src='http://c.statcounter.com/6551997/0/1194400a/1/' alt='vBulletin stat' ></a></div></noscript>";
    ?>
		<!--END VANAS ANTERIOR -->

	</head>
	<body class="animated fadeInDown fixed-ribbon" style="background-image: url('/images/<?php echo ObtenNombreImagen(18); ?>');background-repeat:no-repeat; background-size: 100%">

  <!-- possible classes: minified, no-right-panel, fixed-ribbon, fixed-header, fixed-width
  <header id="header">
    <div id="logo-group ">
      <span id="logo" style="margin-top:0;"> <img src="<?php echo PATH_HOME; ?>/images/header_vanas_adm.png" alt="Vanas"> </span>
    </div>
  </header>-->
  
  <div id="main" role="main" style="margin-left:0px; margin-top:60px">
    <!-- MAIN CONTENT -->
    <div id="content" class="container">
      <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-7 col-lg-8 text-align-center">
        <!--<h1 class="login-header-big"><strong>Vancouver Animation School</strong></h1>
        <div class="hero">    
          <img src="<?php echo SP_HOME; ?>/images/login.jpg" class="display-image no-margin superbox-current-img" alt="">
        </div>-->
      </div>
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
        <div class="well no-padding margin-right-5">
          <form name="datos" id="datos" method="post" action="forgot_validate.php" class="smart-form client-form">
            <header>
               <?php echo ObtenEtiqueta(1850); ?>  
            </header>
            <form name='datos' id="datos" method='post' action='forgot_validate.php'>
            <fieldset> 
                
              <?php if($url_system=="c"){ #solo si es campus mustra el campo de user name ?>  
                             
              <section>                  
                <label class="label"><?php echo ObtenEtiqueta(1851); ?></label>
                <label class="input"> <i class="icon-append fa fa-user"></i>
                  <input type="text" id="ds_login" name="ds_login" />
                  <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> <?php echo ObtenEtiqueta(1857); ?></b>
                </label>
              </section>

             <?php  } ?>
              <section>                    
                <label class="label"><?php echo ObtenEtiqueta(1852); ?></label>
                <label class="input"> <i class="icon-append fa fa-lock"></i>
                  <input type='text' name='ds_email' size='30' maxlength='200' onKeyPress='return submitenter(this,event)' />
                  <b class="tooltip tooltip-top-right"><i class="fa fa-envelope txt-color-teal"></i> <?php echo ObtenEtiqueta(1858); ?></b>
                </label>
              </section>          
              <section>

              <input type="hidden" name="s" id="s" value="<?php echo $url_system; ?>" />



              <?php
                # Presenta mensajes de error
                if(!empty($err)) {
                  if($err== 5)
                    $class = "alert-success";
                  else
                    $class = "alert-danger";
                  echo '
                  <div class="alert '.$class.' fade in">
                  <i class="fa fa-times fa-x"></i>
                  <strong>';
                  echo $err_msg;
                  echo "
                  </strong> Please try again.
                  </div>";
                }
              ?>   
              </section>          
            </fieldset>
            <footer>
              <!--<input class="btn btn-primary" type='button' value='Send me a new password' onclick='document.datos.submit();' />-->
              
              <button type="submit" class="btn btn-primary" id="acceder">
              <?php echo ObtenEtiqueta(1853); ?>
              </button>
              <!--<input type='button' class="btn btn-primary" value='Log in' id="no_acceder" onclick='validaForma(this.form);' />-->
            </footer> 
            </form>
          </form>
        </div>
        <!-- Sitio publico -->
        <h5 class="text-center"> - <?php echo ObtenEtiqueta(77); ?> -</h5>
        <ul class="list-inline text-center">
          <li>
            <a href="<?php echo ObtenConfiguracion(77); ?>" class='btn btn-info' style="background-color:#0092cd;">Return to Vanas Site</a>
          </li>
          <li>
            <a href='<?php echo INICIO_W; ?>' class='btn btn-info' style="background-color:#0092cd;">Return to Login</a>
          </li>
        </ul>
      </div>
      </div>
    </div>
  </div>
  <?php 
  //include required scripts
  include("AD3M2SRC4/bootstrap/inc/scripts.php"); 
?>

  <script type="text/javascript">
    runAllForms();
    $(function() {

      // Validation
      $("#datos").validate({
        // Rules for form validation
          rules: {
        <?php if($url_system=="c"){ ?>
          ds_login : {
            required : true,
            // minlength : 3,
            maxlength : 50
          },
        <?php } ?>
          ds_email : {
            required : true,
            // minlength : 3,
            maxlength : 50,
            email: true            
          },
          quality: {
            required:true
          }
        },

        // Messages for form validation
          messages: {
        <?php if($url_system=="c"){ ?>
          ds_login : {
            required : '<?php echo ObtenEtiqueta(1854); ?>',
            ds_login : '<?php echo ObtenEtiqueta(1855); ?>'
          },
        <?php } ?>

          ds_email : {
            required : '<?php echo ObtenEtiqueta(1856); ?>'
          },
          quality: {
            required : 'error'
          }
        },

        // Do not change code below
        errorPlacement : function(error, element) {
          error.insertAfter(element.parent());
        }
      });
    });
    
  </script>

<?php
  include("AD3M2SRC4/bootstrap/inc/footer.php");
?>