<?php
  
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  // require("../lib/sp_forms.inc.php");
  require("lib/app_forms.inc.php");
  require("app_form.inc.php");

  $clave = RecibeParametroHTML('c', false, true);
 
  #Recuperamos datos del estudiante.
  $Query="select * FROM c_usuario WHERE fl_usuario=$clave ";
  $row=RecuperaValor($Query);
  $ds_nombres=str_texto($row['ds_nombre']);
  $ds_apaterno=str_texto($row['ds_apaterno']);
  $ds_resp=$row['ds_graduate_status'];


 

?>

<!DOCTYPE html>
<html lang="en-us">	
	<head>
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
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<!-- #FAVICONS -->
	<link rel="shortcut icon" href="<?php echo SP_IMAGES."/favicon.ico"; ?>" type="image/x-icon">
	<link rel="icon" href="<?php echo SP_IMAGES."/favicon.ico"; ?>" type="image/x-icon">

	</head>

  <style>
  .containerr {
    z-index: 1;
    position: relative;
    overflow: hidden;
    
    align-items: center;
    justify-content: center;  
    // min-height: 100vh;
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
    background-image: url(https://campus.vanas.ca/fame/img/login.jpg);
    background-size: 100%;
    background-repeat: no-repeat;
  }
  .bgimage-inside {
      padding-top: 10.36%;
    .smart-form .label{
      font-size:16px;
    }
  }
  </style>
	<body class="">
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
           <?php  
           if(empty($ds_resp)){
            
            ?>
                    
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
                  id="str_name"><?php echo $ds_nombres." ".$ds_apaterno;?></strong>, 
                  <br/>Welcome to <strong>Vancouver Animation School</strong>
                </h1>
            </div>
          </div>          
          <div class="" id="exito" style="background-color:#fff">
              <br />
            <?php  

                      $names = array("ds_resp", "ds_resp","ds_resp","ds_resp","ds_resp","ds_resp");
                      $labels = array(ObtenEtiqueta(2654), ObtenEtiqueta(2655),ObtenEtiqueta(2656),ObtenEtiqueta(2657),ObtenEtiqueta(2658),ObtenEtiqueta(2659));
                      $vals = array("1", "2","3", "4","5","6");

                      echo Forma_CampoRadioBootstrap(str_uso_normal(ObtenEtiqueta(2653)), $names, $labels, $vals,'', '', true, "12 padding-10", ""); ?>
                
              <br /><br />
              <div class="text-center">
                    <a href="javascript:Save(<?php echo $clave;?>);" class="btn btn-lg txt-color-white hidden" id="btn_1" style="background-color:#0092cd;border-radius: 10px;"> Send </a>  
              </div>  
          </div>


            <?php }else{ ?>
              
               <div class="text-center" ><br /><br />
                   <h1 class="text-danger"><i class="fa fa-times-circle-o" aria-hidden="true" style="font-size:35px;"></i></h1>
                   <p style="font-size:15px;"><a href="<?php echo ObtenConfiguracion(77);?>">Go to https://vanas.ca</a></p>
                </div>
              <?php } ?>



        </div>
        
      </div>       
    </div>
    <script>
        $(document).ready(function () {

            $('.radio').change(function () {
                $("#btn_1").removeClass('hidden');
            });
        });

        function Save(fl_usuario) {

           
            var radios = document.getElementsByName('ds_resp');

            for (var i = 0, length = radios.length; i < length; i++) {
                if (radios[i].checked) {
                    var opc = radios[i].value;
                    break;
                }
            }


            $.ajax({
                type: 'POST',
                url: 'save_graduated.php',
                data: 'fl_usuario=' + fl_usuario +
                      '&opc=' + opc,
                async: false,
                success: function (html) {
                    $('#btn_1').addClass('hidden');
                    $('#exito').html(html);
                }
            });



        }

    </script>



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
		<!--<script src="assets/bootstrap/version/js/plugin/jquery-validate/jquery.validate.min.js"></script>-->
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
    
    
  
	</body>

</html>
