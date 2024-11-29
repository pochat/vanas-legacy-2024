<?php
  
  # Libreria de funciones
  require("modules/common/lib/cam_general.inc.php");
  

$page_css[] = "your_style.css";
$no_main_header = true;
$page_html_prop = array("id"=>"extr-page", "class"=>"animated fadeInDown");


?>
<!DOCTYPE html>
<html lang="en-us" >
	<head>
		<meta charset="utf-8">
		

		<title> 
    <?php
    
    ?></title>
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
		<!--<link type='text/css' href='<?php echo PATH_CSS; ?>/fileuploader.css' rel='stylesheet' />-->
		<!--<link type='text/css' href='<?php echo PATH_CSS; ?>/jquery.lovs.css' rel='stylesheet' />-->
		<!--<link type='text/css' href='<?php echo PATH_CSS; ?>/separadores.css' media='screen' rel='stylesheet' />
		<link type='text/css' href='<?php echo PATH_CSS; ?>/colorbox.css' media='screen' rel='stylesheet' />
		<link type='text/css' href='<?php echo PATH_CSS; ?>/procesos.css' rel='stylesheet' />-->
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

	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-MLCQWKN');</script>
	<!-- End Google Tag Manager -->
	
		
		
		
	</head>
  <?php  
  $background = ObtenNombreImagen(18);
  
	
	
  ?>
  <body class="animated fadeInDown fixed-ribbon" style="background-image: url('<?php echo SP_IMAGES."/".$background ?>');background-repeat:no-repeat; background-size: 100%">
  
    <!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MLCQWKN" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	
  
  <?php
  
  
  
  ?>
  
  
  
  <div id="main" role="main" class="no-margin">
    <!-- MAIN CONTENT -->
    <div id="content" class="container">
      <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-7 col-lg-8 text-align-center">
      
      </div>
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
        <br /><br /><br /><br />
             <p class="text-center"><b></b></p>
             <h5 class="text-center"><b><i class="fa fa-cogs" aria-hidden="true"></i> Closed for maintenance.<br /> We'll be back soon. </b></h5>
              
        
		
      
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

      function ReenviarEmail(fl_envio_correo,fl_usuario) {

          var fg_resend = 1;///alert(fl_usuario);


          $.ajax({
              type: 'POST',
              url: 'fame/func_guardar_registro_parentesco.php',
              async: false,
              data: 'fl_envio_correo='+fl_envio_correo+
                    '&fg_resend='+fg_resend+
                    '&fl_usuario='+fl_usuario,
              success: function (data) {
                    $('#envia_email').html(data);
              }
          });


      }




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
            // maxlength : 30
          },
          ds_password : {
            required : true,
            // minlength : 3,
            // maxlength : 30
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

#MJD Validacion para FAME, Si el plan del Instituto ya vencio, entonces ocultamos el boton de login y se aparece el boton de envio de correo para administracion
if($err==8){

echo"<script>
	$(document).ready(function(){
	  $('#acceder').addClass('hidden');
      $('#request').removeClass('hidden');


    });
	
	function send_email_adm(){
	
	        var fl_usuario=$fl_usuario;
            var fl_instituto=$fl_instituto;
	      
		    $.ajax({
				type: 'POST',
				url: 'fame/envia_email_adm.php',
				data: 'fl_usuario='+fl_usuario+
					  '&fl_instituto='+fl_instituto,

				async: true,
				success: function (html) {
					$('#send_envio').html(html);
				}
			});
	
	
	}
	
	
	</script>
	
	
	
";


}
#Ocultamos el boton de login.
if($fa==1){
 
    echo"<script>
	     $('#acceder').addClass('hidden');
	
		</script>";
 
}





  //include footer
  include("AD3M2SRC4/bootstrap/inc/footer.php"); 
?>
