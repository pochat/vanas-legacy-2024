<?php
  
  # Libreria de funciones
  require("modules/common/lib/cam_general.inc.php");
  
  # Recibe parametros
  $err = RecibeParametroNumerico('err', True);
  # Recibe una url porque queria ingresar a una seccion y no testab logueado 
  # una vez que se logue ira automaticamente a a url que recibe
  $ori = RecibeParametroHTML('ori',False,True);
  
  # Presenta mensajes de error
  switch($err) {
    case 1: $err_msg = "Invalid username or password..."; break;
    case 2: $err_msg = "Session expired, please sign in again."; break;
    case 3: $err_msg = "Session expired, please sign in again."; break;
    case 4: $err_msg = "Your user account is inactive."; break;
    case 5: $err_msg = "Access denied, please contact Vanas Administrator."; break;
    case 6: $err_msg = "You already have an active session."; break;
    case 7: $err_msg = "Online Campus closed for maintenance. Please try again later."; break;
    default: $err_msg = "&nbsp;";
  }
  
  #Verifica que el checkbox se haya activado anteriormente
  $fg_check_rm = $_COOKIE[SESION_CHECK_RM];
  
  # Verifica si esta activado el remember me
  $cl_sesion = $_COOKIE[SESION_RM];
  if(!empty($cl_sesion)) {
  
    # Recupera identificador de sesion y estado del usuario
    $Query  = "SELECT fl_usuario, cl_sesion, fg_activo, fl_perfil, TIMESTAMPDIFF(SECOND, fe_sesion, CURRENT_TIMESTAMP) no_segundos ";
    $Query .= "FROM c_usuario WHERE cl_sesion='$cl_sesion'";
    $row = RecuperaValor($Query);
    $fl_usuario = $row[0];
    $cl_sesion = $row[1];
    $fg_activo = $row[2];
    $fl_perfil = $row[3];
    $no_segundos = $row[4];

    # Valida que el usuario exista y este activo
    if(!empty($fl_usuario) and $fg_activo == '1') {

      # Revisa si el perfil es de administracion
      $row = RecuperaValor("SELECT fg_admon FROM c_perfil WHERE fl_perfil=$fl_perfil");
      if($row[0] <> '1') {

        # Validaciones de acceso para estudiantes
        if($fl_perfil == PFL_ESTUDIANTE) {

          # Revisa que el Online Campus este disponible
          if(ObtenConfiguracion(47) <> '0') {

            # Revisa que el alumno este inscrito en un gurpo
            $fl_grupo = ObtenGrupoAlumno($fl_usuario);
            if(!empty($fl_grupo)) {

              # Revisa que haya realizado su pago
              $fl_term = ObtenTermAlumno($fl_usuario);
              $fl_periodo = ObtenPeriodoAlumno($fl_usuario);
              $fe_actual = ObtenFechaActual( );
              $Query  = "SELECT 1 ";
              $Query .= "FROM k_alumno_term a, k_term b, c_periodo c ";
              $Query .= "WHERE a.fl_term=b.fl_term ";
              $Query .= "AND b.fl_periodo=c.fl_periodo ";
              $Query .= "AND a.fl_alumno=$fl_usuario ";
              $Query .= "AND a.fl_term=$fl_term ";
              $row = RecuperaValor($Query);
              if($row[0] == '1') {
                
                # Actualiza estadisticas de acceso del usuario
                EjecutaQuery("UPDATE c_usuario SET fe_ultacc=CURRENT_TIMESTAMP, no_accesos=no_accesos+1 WHERE cl_sesion='$cl_sesion'");
                EjecutaQuery("INSERT INTO k_usu_login (fl_usuario, fe_login) VALUES($fl_usuario, CURRENT_TIMESTAMP)");

                # Redirige a la pagina inicial de acuerdo al perfil del usuario
                //$pag = PAGINA_INI_ALU;
                //$pag = PATH_N_ALU."/index.php#ajax/home.php";
                $pag = PATH_N_ALU."/index.php#ajax/desktop.php";
                ActualizaDiferenciaGMT($fl_perfil, $fl_usuario);
                
                # Crea cookie con identificador de sesion y redirige al home del sistema
                ActualizaSesion($cl_sesion, false);
                header("Location: ".$pag);
                exit;
              }
            }
          }
        }
        else {
          if($fl_perfil == PFL_MAESTRO){
            if ($ds_login != ObtenConfiguracion(40)){
              
              # Revisa que el Online Campus este disponible
              if(ObtenConfiguracion(47) <> '0') {
                # Actualiza estadisticas de acceso del usuario
                EjecutaQuery("UPDATE c_usuario SET fe_ultacc=CURRENT_TIMESTAMP, no_accesos=no_accesos+1 WHERE cl_sesion='$cl_sesion'");
                EjecutaQuery("INSERT INTO k_usu_login (fl_usuario, fe_login) VALUES($fl_usuario, CURRENT_TIMESTAMP)");

                # Redirige a la pagina inicial de acuerdo al perfil del usuario
                //$pag = PAGINA_INI_MAE;
                $pag = PATH_N_MAE."/index.php#ajax/home.php";
                ActualizaDiferenciaGMT($fl_perfil, $fl_usuario);
                
                # Crea cookie con identificador de sesion y redirige al home del sistema
                ActualizaSesion($cl_sesion, false);
                header("Location: ".$pag);
                exit;
              }
            }
            else {
              
              # Actualiza estadisticas de acceso del usuario
              EjecutaQuery("UPDATE c_usuario SET fe_ultacc=CURRENT_TIMESTAMP, no_accesos=no_accesos+1 WHERE cl_sesion='$cl_sesion'");
              EjecutaQuery("INSERT INTO k_usu_login (fl_usuario, fe_login) VALUES($fl_usuario, CURRENT_TIMESTAMP)");

              # Redirige a la pagina inicial de acuerdo al perfil del usuario
              //$pag = PAGINA_INI_MAE;
              $pag = PATH_N_MAE."/index.php#ajax/home.php";
              ActualizaDiferenciaGMT($fl_perfil, $fl_usuario);
              
              # Crea cookie con identificador de sesion y redirige al home del sistema
              ActualizaSesion($cl_sesion, false);
              header("Location: ".$pag);
              exit;
            }
          }
        }
      }
    }
  }

  # Presenta pagina de Login
$page_title = "VanAS Online Campus - Login";

//include header
//you can add your custom css in $page_css array.
//Note: all css files are inside css/ folder
$page_css[] = "your_style.css";
$no_main_header = true;
$page_html_prop = array("id"=>"extr-page", "class"=>"animated fadeInDown");
/*
# Incluimos el header
include("AD3M2SRC4/bootstrap/inc/header.php");

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
          validaForma(myfield.form);
          return false;
        }
        else
          return true;
      }

      var BrowserDetect = {
        init: function () {
          this.browser = this.searchString(this.dataBrowser) || \"An unknown browser\";
          this.version = this.searchVersion(navigator.userAgent)
            || this.searchVersion(navigator.appVersion)
            || \"an unknown version\";
          this.OS = this.searchString(this.dataOS) || \"an unknown OS\";
        },
        searchString: function (data) {
          for (var i=0;i<data.length;i++) {
            var dataString = data[i].string;
            var dataProp = data[i].prop;
            this.versionSearchString = data[i].versionSearch || data[i].identity;
            if (dataString) {
              if (dataString.indexOf(data[i].subString) != -1)
                return data[i].identity;
            }
            else if (dataProp)
              return data[i].identity;
          }
        },
        searchVersion: function (dataString) {
          var index = dataString.indexOf(this.versionSearchString);
          if (index == -1) return;
          return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
        },
        dataBrowser: [
          {
            string: navigator.userAgent,
            subString: \"Chrome\",
            identity: \"Chrome\"
          },
          {   string: navigator.userAgent,
            subString: \"OmniWeb\",
            versionSearch: \"OmniWeb/\",
            identity: \"OmniWeb\"
          },
          {
            string: navigator.vendor,
            subString: \"Apple\",
            identity: \"Safari\",
            versionSearch: \"Version\"
          },
          {
            prop: window.opera,
            identity: \"Opera\"
          },
          {
            string: navigator.vendor,
            subString: \"iCab\",
            identity: \"iCab\"
          },
          {
            string: navigator.vendor,
            subString: \"KDE\",
            identity: \"Konqueror\"
          },
          {
            string: navigator.userAgent,
            subString: \"Firefox\",
            identity: \"Firefox\"
          },
          {
            string: navigator.vendor,
            subString: \"Camino\",
            identity: \"Camino\"
          },
          {   // for newer Netscapes (6+)
            string: navigator.userAgent,
            subString: \"Netscape\",
            identity: \"Netscape\"
          },
          {
            string: navigator.userAgent,
            subString: \"MSIE\",
            identity: \"Explorer\",
            versionSearch: \"MSIE\"
          },
          {
            string: navigator.userAgent,
            subString: \"Gecko\",
            identity: \"Mozilla\",
            versionSearch: \"rv\"
          },
          {     // for older Netscapes (4-)
            string: navigator.userAgent,
            subString: \"Mozilla\",
            identity: \"Netscape\",
            versionSearch: \"Mozilla\"
          }
        ],
        dataOS : [
          {
            string: navigator.platform,
            subString: \"Win\",
            identity: \"Windows\"
          },
          {
            string: navigator.platform,
            subString: \"Mac\",
            identity: \"Mac\"
          },
          {
               string: navigator.userAgent,
               subString: \"iPhone\",
               identity: \"iPhone/iPod\"
            },
          {
            string: navigator.platform,
            subString: \"Linux\",
            identity: \"Linux\"
          }
        ]

      };
      BrowserDetect.init();

      function validaForma(forma)
      {
        if(BrowserDetect.browser==\"Firefox\" || BrowserDetect.browser==\"Chrome\" || BrowserDetect.browser==\"Safari\")
        {
          document.datos.submit();
        }
        else 
        {
          alert('Access is allowed only from Chrome, Firefox, or Safari');
          return;
        }   
      }

    </script>";*/

?>
<!DOCTYPE html>
<html lang="en-us" >
	<head>
		<meta charset="utf-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

		<title> <?php echo $page_title != "" ? $page_title : ""; ?></title>
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

			if ($page_css) {
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
          validaForma(myfield.form);
          return false;
        }
        else
          return true;
      }

      var BrowserDetect = {
        init: function () {
          this.browser = this.searchString(this.dataBrowser) || \"An unknown browser\";
          this.version = this.searchVersion(navigator.userAgent)
            || this.searchVersion(navigator.appVersion)
            || \"an unknown version\";
          this.OS = this.searchString(this.dataOS) || \"an unknown OS\";
        },
        searchString: function (data) {
          for (var i=0;i<data.length;i++) {
            var dataString = data[i].string;
            var dataProp = data[i].prop;
            this.versionSearchString = data[i].versionSearch || data[i].identity;
            if (dataString) {
              if (dataString.indexOf(data[i].subString) != -1)
                return data[i].identity;
            }
            else if (dataProp)
              return data[i].identity;
          }
        },
        searchVersion: function (dataString) {
          var index = dataString.indexOf(this.versionSearchString);
          if (index == -1) return;
          return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
        },
        dataBrowser: [
          {
            string: navigator.userAgent,
            subString: \"Chrome\",
            identity: \"Chrome\"
          },
          {   string: navigator.userAgent,
            subString: \"OmniWeb\",
            versionSearch: \"OmniWeb/\",
            identity: \"OmniWeb\"
          },
          {
            string: navigator.vendor,
            subString: \"Apple\",
            identity: \"Safari\",
            versionSearch: \"Version\"
          },
          {
            prop: window.opera,
            identity: \"Opera\"
          },
          {
            string: navigator.vendor,
            subString: \"iCab\",
            identity: \"iCab\"
          },
          {
            string: navigator.vendor,
            subString: \"KDE\",
            identity: \"Konqueror\"
          },
          {
            string: navigator.userAgent,
            subString: \"Firefox\",
            identity: \"Firefox\"
          },
          {
            string: navigator.vendor,
            subString: \"Camino\",
            identity: \"Camino\"
          },
          {   // for newer Netscapes (6+)
            string: navigator.userAgent,
            subString: \"Netscape\",
            identity: \"Netscape\"
          },
          {
            string: navigator.userAgent,
            subString: \"MSIE\",
            identity: \"Explorer\",
            versionSearch: \"MSIE\"
          },
          {
            string: navigator.userAgent,
            subString: \"Gecko\",
            identity: \"Mozilla\",
            versionSearch: \"rv\"
          },
          {     // for older Netscapes (4-)
            string: navigator.userAgent,
            subString: \"Mozilla\",
            identity: \"Netscape\",
            versionSearch: \"Mozilla\"
          }
        ],
        dataOS : [
          {
            string: navigator.platform,
            subString: \"Win\",
            identity: \"Windows\"
          },
          {
            string: navigator.platform,
            subString: \"Mac\",
            identity: \"Mac\"
          },
          {
               string: navigator.userAgent,
               subString: \"iPhone\",
               identity: \"iPhone/iPod\"
            },
          {
            string: navigator.platform,
            subString: \"Linux\",
            identity: \"Linux\"
          }
        ]

      };
      BrowserDetect.init();

      function validaForma(forma)
      {
        if(BrowserDetect.browser==\"Firefox\" || BrowserDetect.browser==\"Chrome\" || BrowserDetect.browser==\"Safari\")
        {
          document.datos.submit();
        }
        else 
        {
          alert('Access is allowed only from Chrome, Firefox, or Safari');
          return;
        }   
      }

    </script>";
    ?>
		<!--END VANAS ANTERIOR -->

	</head>
	<body class="animated fadeInDown fixed-ribbon" style="background-image: url('/images/<?php echo ObtenNombreImagen(18); ?>');background-repeat:no-repeat; background-size: 100% 100%">
  <!-- ==========================CONTENT STARTS HERE ========================== -->
  <!--<header id="header" style="background:#0092cd;">

    <div id="logo-group">
      <span id="logo"> <img src="img/logo.png" alt="SmartAdmin"> </span>
    </div>

    <span id="extr-page-header-space"> <span class="hidden-mobile hiddex-xs">Need an account?</span> <a href="register.html" class="btn btn-danger">Create account</a> </span>

  </header>-->
  <!-- MAIN -->
  <div id="main" role="main" style="margin-left:0px; margin-top:60px">
    <!-- MAIN CONTENT -->
    <div id="content" class="container">
      <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-7 col-lg-8 text-align-center">
        <!--<h1 class="login-header-big"><strong>Vancouver Animation School</strong></h1>
        <div class="hero">
          <div class="pull-left login-desc-box-l">
            <h4 class="paragraph-header">It's Okay to be Smart. Experience the simplicity of SmartAdmin, everywhere you go!</h4>
            <div class="login-app-icons">
              <a href="javascript:void(0);" class="btn btn-danger btn-sm">Frontend Template</a>
              <a href="javascript:void(0);" class="btn btn-danger btn-sm">Find out more</a>
            </div>
          </div>       
          <img src="<?php echo SP_HOME; ?>/images/login.jpg" class="display-image no-margin superbox-current-img" alt="">
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h5 class="about-heading">About SmartAdmin - Are you up to date?</h5>
            <p>
              Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa.
            </p>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h5 class="about-heading">Not just your average template!</h5>
            <p>
              Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi voluptatem accusantium!
            </p>
          </div>
        </div>-->
      </div>
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
        <div class="well no-padding margin-right-5">
          <form name="datos" id="datos" method="post" action="login_validate.php" class="smart-form client-form">
            <header>
              Sign In
            </header>
          	<input type='hidden' name='fg_campus' value='1' checked='checked'>
            <fieldset>

              <section>
                <label class="label"><?php echo ObtenEtiqueta(805); ?></label>
                <label class="input"> <i class="icon-append fa fa-user"></i>
                <input type="text" id="ds_login" name="ds_login" />
                <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter username</b></label>
              </section>

              <section>
                <label class="label"><?php echo ObtenEtiqueta(806); ?></label>
                <label class="input"> <i class="icon-append fa fa-lock"></i>
                <input type='password' name='ds_password' id='ds_password' size='30' maxlength='16' autocomplete='off' onKeyPress='return submitenter(this,event)' />
                <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Enter your password</b> </label>
                <section class="note">
                  <label>
                    <a href="<?php echo PAGINA_OLVIDO; ?>"><?php echo ObtenEtiqueta(75); ?></a>                    
                  </label>
                </section>
                <section class="note">
                  <label class="checkbox">
                    <?php
                    if($fg_check_rm)
                      echo "<input type='checkbox' name='fg_rm' value='1' checked='True'>";
                    else
                      echo "<input type='checkbox' name='fg_rm' value='1'>";
                    ?>
                    <i></i><?php echo ObtenEtiqueta(634); ?>                    
                  </label>
                  <span><?php echo ObtenEtiqueta(636); ?></span>
                </section>
              </section>

              <section>								
              <?php
                # Presenta mensajes de error
                if(!empty($err)) {
                  echo '
                  <div class="alert alert-danger fade in">
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
              <button type="submit" class="btn btn-primary" id="acceder">
              Log in
              </button>
              <input type='button' class="btn btn-primary" value='Log in' id="no_acceder" onclick='validaForma(this.form);' />
              <input type='hidden' name='ori' id='ori' value='$ori' />
            </footer>            
          </form>
        </div>       
        <h5 class="text-center"> - <?php echo ObtenEtiqueta(77); ?> -</h5>
        <ul class="list-inline text-center">
          <li>
            <a href="<?php echo INICIO_W; ?>" class='btn btn-info btn-circle'><i class="fa fa-font"></i></a>
          </li>
        </ul>
      </div>
      </div>
    </div>
  </div>
  <!-- END MAIN PANEL -->
  <!-- ==========================CONTENT ENDS HERE ========================== -->
    
<?php 
  //include required scripts
  include("AD3M2SRC4/bootstrap/inc/scripts.php"); 
?>

  <script type="text/javascript">
    runAllForms();
    $(function() {
      
      if(BrowserDetect.browser=="Firefox" || BrowserDetect.browser=="Chrome" || BrowserDetect.browser=="Safari"){
        $("#acceder").show();
        $("#no_acceder").hide();
      }
      else{
        $("#acceder").hide();
        $("#no_acceder").show();
      }
        
      
      // Validation
      $("#datos").validate({
        // Rules for form validation
        rules : {
          ds_login : {
            required : true,
            // minlength : 3,
            maxlength : 30
          },
          ds_password : {
            required : true,
            // minlength : 3,
            maxlength : 30
          },
          quality: {
            required:true
          }
        },

        // Messages for form validation
        messages : {
          ds_login : {
            required : 'Please enter your user address',
            ds_login : 'Please enter a VALID user address'
          },
          ds_password : {
            required : 'Please enter your password'
          },
          quality: {
            required : 'erorrrr'
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
  //include footer
  include("AD3M2RC4/bootstrap/inc/footer.php"); 
?>