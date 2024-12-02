<?php
# Libreria de funciones
require("../lib/sp_general.inc.php");
require("../lib/sp_session.inc.php");
// require("../lib/sp_forms.inc.php");
require("lib/app_forms.inc.php");
require("app_form.inc.php");

$Query = "select nb_archivo from c_imagen WHERE cl_imagen=311 ";
$row = RecuperaValor($Query);
$nb_arcimagen = $row[0];

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
	
	<!-- #FAVICONS -->
	<link rel="shortcut icon" href="<?php echo SP_IMAGES."/favicon.ico"; ?>" type="image/x-icon">
	<link rel="icon" href="<?php echo SP_IMAGES."/favicon.ico"; ?>" type="image/x-icon">

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-MLCQWKN');</script>
        <!-- End Google Tag Manager -->

    <style>
          .bgimage {
            background-image: url(https://campus.vanas.ca/images/<?php echo $nb_arcimagen;?>);
            background-size: 100%;
            background-repeat: no-repeat;
          }
          .bgimage-inside {
              padding-top: 10.36%;
            .smart-form.label{
            font-size: 16px;
            }
          }
          
        </style>
  
	</head>

<body class="">
    <!-- Top content -->
    <div class="">
        <div class="container">
            <div class="row">
                <div class="col-md-12 bgimage">
                    <div class="bgimage-inside"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">

                </div>
                <div class="col-md-6">
                    <br />
                    <h1 class='error-text tada animated text-center' style='font-size:50px; color:#0092DB;'>
                        <i class='fa fa-check text-success'></i><?php echo ObtenEtiqueta(328); ?>
                    </h1>
                    <br />
                    <br />
                    <p>
                        <?php echo ObtenEtiqueta(331); ?>
                    </p>
                    <p>
                        <?php echo ObtenEtiqueta(332); ?>
                    </p>
                    <p>
                        <?php echo ObtenEtiqueta(333); ?>
                    </p>
                    <p>
                        <?php echo ObtenEtiqueta(334); ?>
                    </p>
                </div>

            </div>




            
        </div>
    </div>
</body>
</html>