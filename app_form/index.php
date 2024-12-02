<?php
  
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  // require("../lib/sp_forms.inc.php");
  require("lib/app_forms.inc.php");
  require("app_form.inc.php");

  $c = RecibeParametroHTML('c', false, true);
  $ds_code = RecibeParametroHTML('cd', false, true);

  # Variable initialization to avoid errors
  $clave = NULL;

  # Verifica si tiene alguna clave
  # Si la clave ya fue confirmada enviara mensaje
  # En caso de que no tenga clave iniciara una 
  if(!empty($c)){
    # Obtenemos la clave oculta
    $c = substr($c, 3,-4);    #$c="123  3342   8910";
    # Verificamos si la clave aun  no a sido confirmada
    $row = RecuperaValor("SELECT cl_sesion, fg_confirmado, ".ConsultaFechaBD('fe_ultmod', FMT_CAPTURA)." FROM c_sesion WHERE fl_sesion=".$c);    
    $cl_sesi=$row[0];
    $fg_confirmado = $row[1];
    $fe_ultmod = $row[2];

    if(($row[0])){
      if(empty($fg_confirmado)){
        $clave = $row[0];
        $message = ObtenEtiqueta(2258)."<br>".$fe_ultmod;
      }
      else{
        $message = ObtenEtiqueta(2259);
      }
    }
    else{
      $message = ObtenEtiqueta(2260);
    }
  }

  #Recuperamos el ultimo paso donde se quedo el alumno.
  $Query="SELECT MAX(step_complete * 1) AS fg_paso FROM steps_completed WHERE cl_sesion='$cl_sesi' AND  step_complete<>'1989' ";
  $rop=RecuperaValor($Query);
  $fg_paso=$rop[0];

  #$fg_paso = RecibeParametroHTML('p', false, true);
  $fl_pais_selected = RecibeParametroHTML('co', false, true);

  if(empty($fg_paso)){
      $fg_paso=$_POST['fg_paso'];
      $clave=$_POST['clave'];
      $fl_pais_selected=$_POST['fl_pais_selected'];
  }
  
?>

<!DOCTYPE html>
<html lang="en-us">	
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo ObtenEtiqueta(2264); ?></title>
		
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/form-elements.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Basic CSS -->
    
	<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
	<link rel="stylesheet" type="text/css" media="screen" href="assets/bootstrap/version/css/smartadmin-production-plugins.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="assets/bootstrap/version/css/smartadmin-production.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="assets/bootstrap/version/css/smartadmin-skins.min.css">

	<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
	<link rel="stylesheet" type="text/css" media="screen" href="assets/bootstrap/version/css/demo.min.css">
    <link href="https://cdn.rawgit.com/michalsnik/aos/2.1.1/dist/aos.css" rel="stylesheet">
	
	<!-- #FAVICONS -->
	<link rel="shortcut icon" href="<?php echo SP_IMAGES."/favicon.ico"; ?>" type="image/x-icon">
	<link rel="icon" href="<?php echo SP_IMAGES."/favicon.ico"; ?>" type="image/x-icon">

    <script src='https://js.stripe.com/v3/'></script>

	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-MLCQWKN');</script>
	<!-- End Google Tag Manager -->


	</head>
<?php  

$Query="select nb_archivo from c_imagen WHERE cl_imagen=311 ";
$row=RecuperaValor($Query);
$nb_arcimagen=$row[0];

?>
  <style>
  .containerr {
    z-index: 1;
    position: relative;
    overflow: hidden;
    
    align-items: center;
    justify-content: center;  
    /* min-height: 100vh;*/
    min-height: 35rem;
    /*background-image: linear-gradient(to bottom,  rgba(255,168,76,0.6) 0%,rgba(255,123,13,0.6) 100%), url('http://dev.vanas.ca/images/background-login.jpg');*/
    background-blend-mode: soft-light;
    background-size: cover;
    background-position: center center;
    
  }
	.error-text-2 {
		text-align: center;
		font-size: 200%;
		font-weight: bold;
		font-weight: 100;
		color:#000;
		line-height: 2;
		letter-spacing: -.03em;
		
		-webkit-background-clip: text;	
	}	
  :focus{outline: none;}
  .effect-7,{border: 1px solid #ccc; padding: 7px 14px 9px; transition: 0.4s;}

  .effect-7 ~ .focus-border:before,
  .effect-7 ~ .focus-border:after{content: ""; position: absolute; top: 0; left: 50%; width: 0; height: 2px; background-color: #3399FF; transition: 0.4s;}
  .effect-7 ~ .focus-border:after{top: auto; bottom: 0;}
  .effect-7 ~ .focus-border i:before,
  .effect-7 ~ .focus-border i:after{content: ""; position: absolute; top: 50%; left: 0; width: 2px; height: 0; background-color: #3399FF; transition: 0.6s;}
  .effect-7 ~ .focus-border i:after{left: auto; right: 0;}
  .effect-7:focus ~ .focus-border:before,
  .effect-7:focus ~ .focus-border:after{left: 0; width: 100%; transition: 0.4s;}
  .effect-7:focus ~ .focus-border i:before,
  .effect-7:focus ~ .focus-border i:after{top: 0; height: 100%; transition: 0.6s;}
  .bgimage {
    background-image: url(https://campus.vanas.ca/images/<?php echo $nb_arcimagen;?>);
    background-size: 100%;
    background-repeat: no-repeat;
  }
  .bgimage-inside {
      padding-top: 10.36%;
    .smart-form .label{
      font-size:16px;
    }
  }
  
  .dot-elastic {
  position: relative;
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: #bbb;
  color: #bbb;
  animation: dotElastic 1s infinite linear;
}

.dot-elastic::before, .dot-elastic::after {
  content: '';
  display: inline-block;
  position: absolute;
  top: 0;
}

.dot-elastic::before {
  left: -15px;
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: #bbb;
  color: #bbb;
  animation: dotElasticBefore 1s infinite linear;
}

.dot-elastic::after {
  left: 15px;
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: #bbb;
  color: #bbb;
  animation: dotElasticAfter 1s infinite linear;
}

@keyframes dotElasticBefore {
  0% {
    transform: scale(1, 1);
  }
  25% {
    transform: scale(1, 1.5);
  }
  50% {
    transform: scale(1, 0.67);
  }
  75% {
    transform: scale(1, 1);
  }
  100% {
    transform: scale(1, 1);
  }
}

@keyframes dotElastic {
  0% {
    transform: scale(1, 1);
  }
  25% {
    transform: scale(1, 1);
  }
  50% {
    transform: scale(1, 1.5);
  }
  75% {
    transform: scale(1, 1);
  }
  100% {
    transform: scale(1, 1);
  }
}

@keyframes dotElasticAfter {
  0% {
    transform: scale(1, 1);
  }
  25% {
    transform: scale(1, 1);
  }
  50% {
    transform: scale(1, 0.67);
  }
  75% {
    transform: scale(1, 1.5);
  }
  100% {
    transform: scale(1, 1);
  }
}
  </style>
	<body class="">
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MLCQWKN" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	
		<!-- Top content -->
    <div class="">
      <div class="containerr">
        <div class="row">
          <div class="col-md-12 bgimage">
              <div class="bgimage-inside"></div>
          </div>
        </div>        
        <div class="col col-sm-12 col-md-12 col-lg-2 padding-top-10">&nbsp;</div>
        <!-- Forms -->
        <div class="col col-sm-12 col-md-12 col-lg-8 padding-top-10">          
          <!-- Titulo -->
          <div class="row text-align-center">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <h6 class="padding-10 tada animated" style="color:#FF0000;"> <?php if(!empty($message)) echo "<i class='fa fa-warning'></i> $message"; ?></h6>
                <h1 class="error-text-2 bounceInDown animated" style="line-height: 35px;letter-spacing: 0px;"> 
                  Hi <strong 
                  style="
                  color:#0092cd !important; 
                  background-image: -webkit-linear-gradient(92deg,#0092cd,#0092cd);
                  -webkit-background-clip: text;
                  -webkit-text-fill-color: transparent;"
                  id="str_name"></strong>, 
                  <br/>Welcome to <strong>Vancouver Animation School</strong>
                </h1>
            </div>
          </div>          
          <div class="well" id="frm-app-form" style="background-color:#fff">              
          </div>
        </div>
        
      </div>       
    </div>
    
		<!-- #PLUGINS -->
    <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local-->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script>
			if (!window.jQuery) {
				document.write('<script src="assets/bootstrap/version/js/libs/jquery-2.1.1.min.js"><\/script>');
			}
		</script>

		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script>
			if (!window.jQuery.ui) {
				document.write('<script src="assets/bootstrap/version/js/libs/jquery-ui-1.10.3.min.js"><\/script>');
			}
		</script>

		<!-- IMPORTANT: APP CONFIG -->
		<script src="assets/bootstrap/version/js/libs/jquery-3.2.1.min.js"></script>
		<script src="assets/bootstrap/version/js/libs/jquery-ui.min.js"></script>
		<script src="assets/bootstrap/version/js/app.config.js"></script>

		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
		<script src="assets/bootstrap/version/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> 

		<!-- BOOTSTRAP JS -->
		<script src="assets/bootstrap/version/js/bootstrap/bootstrap.min.js"></script>

		<!-- CUSTOM NOTIFICATION -->
		<script src="assets/bootstrap/version/js/notification/SmartNotification.min.js"></script>

		<!-- JARVIS WIDGETS -->
		<script src="assets/bootstrap/version/js/smartwidgets/jarvis.widget.min.js"></script>

		<!-- EASY PIE CHARTS -->
		<script src="assets/bootstrap/version/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

		<!-- SPARKLINES -->
		<script src="assets/bootstrap/version/js/plugin/sparkline/jquery.sparkline.min.js"></script>

		<!-- JQUERY VALIDATE -->
		<script src="assets/bootstrap/version/js/plugin/jquery-validate/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

		<!-- JQUERY MASKED INPUT -->
		<script src="assets/bootstrap/version/js/plugin/masked-input/jquery.maskedinput.min.js"></script>

		<!-- JQUERY SELECT2 INPUT -->
		<script src="assets/bootstrap/version/js/plugin/select2/select2.min.js"></script>

		<!-- JQUERY UI + Bootstrap Slider -->
		<script src="assets/bootstrap/version/js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

		<!-- browser msie issue fix -->
		<script src="assets/bootstrap/version/js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

		<!-- FastClick: For mobile devices: you can disable this in app.js -->
		<script src="assets/bootstrap/version/js/plugin/fastclick/fastclick.min.js"></script>

		<!-- Demo purpose only -->
		<script src="assets/bootstrap/version/js/demo.min.js"></script>

		<!-- MAIN APP JS FILE -->
		<script src="assets/bootstrap/version/js/app.min.js"></script>
    
    
    <!-- Animaciones -->
    <script src="https://cdn.rawgit.com/michalsnik/aos/2.1.1/dist/aos.js"></script>
    
    <!--- Basic -->
    <script src="assets/js/jquery.backstretch.min.js"></script>
    <script src="assets/js/retina-1.1.0.min.js"></script>
    <script src="assets/js/scripts.js"></script>  
    <script src='assets/js/validar.js' id='delete_script'></script>
    <div id="scripts"></div>
    
    
    <script type="text/javascript">
     
      // Indicamos al usuario que si recarga puede perder los datos
      // var v = window.onbeforeunload = confirmExit;
      // function confirmExit(){
        // return "You have attempted to leave this page.  If you have made any changes to the fields without clicking the Save button, your changes will be lost.  Are you sure you want to exit this page?";
      // }
      
      /* Inicia 1er paso*/
      var clave = '<?php echo $clave; ?>';
      var paso = '<?php echo $fg_paso; ?>';
        var cd = '<?php echo $ds_code; ?>';
        var fl_pais_selected = '<?php echo $fl_pais_selected;?>';
      if(clave!="" && paso!="")
        paso = paso;
      else
        paso = 1989;
	  // Form
      app_form(paso, clave, false, cd, fl_pais_selected);
    </script>
	</body>

</html>