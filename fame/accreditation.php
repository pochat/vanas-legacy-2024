<?php 

# Libreria de funciones
require_once("lib/self_general.php");

$fl_instituto=$_GET['i'];
#Substraemos el fl_instituto
$cadena=explode("_",$fl_instituto);
$fl_instituto=$cadena[0];
//$fl_instituto=373;

$icono_pdf="<span style='font-size:30px;'><a href='../AD3M2SRC4/modules/reports/acreditation.php?z=".$_GET['z']."&i=".$_GET['i']."'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> </a></span>";

#Obtenemos la fecha actual.
$fe_actual=ObtenFechaActualFAME();
$fe_actual=GeneraFormatoFecha($fe_actual);

$Query="SELECT nb_archivo FROM c_imagen WHERE cl_imagen=303 ";
$row=RecuperaValor($Query);
$nb_logo_fame=$row['nb_archivo'];

$Query = "Select CURDATE() ";
$row = RecuperaValor($Query);
$date_actual = str_texto($row[0]);
$date_actual=strtotime('+0 day',strtotime($date_actual));
$date_actual= date('Y-m-d',$date_actual);

#Recupermos Datois generale de la escuela.
$Query="SELECT * FROM c_instituto WHERE fl_instituto=$fl_instituto ";
$row=RecuperaValor($Query);
$nb_instituto=$row['ds_instituto'];
$ds_codigo_pais=$row['ds_codigo_pais'];
$ds_codigo_area=$row['ds_codigo_area'];
$no_telefono=$row['no_telefono'];
$ds_foto=$row['ds_foto'];
$fl_usuario=$row['fl_usuario_sp'];
$fe_creacion=$row['fe_creacion'];

$fe_creacion=strtotime('+0 day',strtotime($fe_creacion));
$fe_creacion= date('m/Y',$fe_creacion);

$Query="SELECT * FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
$row=RecuperaValor($Query);
$fe_periodo_inicial=$row['fe_periodo_inicial'];
$fe_periodo_final=$row['fe_periodo_final'];

#Recuperamos el a�o actual de su periodo
$recent_ini_accreditation=strtotime('+0 day',strtotime($fe_periodo_inicial));
$recent_ini_accreditation= date('Y',$recent_ini_accreditation);
$recent_fin_accreditation=strtotime('+0 day',strtotime($fe_periodo_final));
$recent_fin_accreditation= date('Y',$recent_fin_accreditation);
$interval_recent_accreditation=$recent_ini_accreditation."-".$recent_fin_accreditation;

#Next acredited(se refiere al a�o siguiente de su periodo)
$next_ini_accreditation=strtotime('+0 day',strtotime($fe_periodo_final));
$next_ini_accreditation= date('Y',$next_ini_accreditation);

$next_fin_accreditation=strtotime('+1 year',strtotime($fe_periodo_final));
$next_fin_accreditation= date('Y',$next_fin_accreditation);

$fe_interval_next_accreditation=$next_ini_accreditation."-".$next_fin_accreditation;


if($date_actual<$fe_periodo_final){

    $status_accreditation="<span class='text-success'><i class='fa fa-check-circle-o' aria-hidden='true'></i> Accredited</span>";
    $status_accredited="<span class='text-success' style='font-size:28px;'><i style='font-size:35px;' class='fa fa-check-circle-o' aria-hidden='true'></i><br>Accredited</span>";
}else{
    $status_accreditation="<span class='text-danger'><i class='fa fa-times-circle-o' aria-hidden='true'></i>No Accredited</span>";
    $status_accredited="<span class='text-danger'style='font-size:28px;'><i style='font-size:35px;' class='fa fa-times-circle-o' aria-hidden='true'></i><br>No Accredited</span>";
}



$fe_periodo_final=GeneraFormatoFecha($row['fe_periodo_final']);


#Recupermos la direccion:
$Query="SELECT * FROM k_usu_direccion_sp WHERE fl_usuario_sp=$fl_usuario ";
$row=RecuperaValor($Query);
$ds_state=$row['ds_state'];
$ds_city =$row['ds_city'];
$ds_number=$row['ds_number'];
$ds_street = $row['ds_street'];
$ds_zip = $row['ds_zip'];

#Recuperamos el website.
$Query="SELECT * FROM c_administrador_sp WHERE fl_adm_sp=$fl_usuario ";
$row=RecuperaValor($Query);
$ds_website=$row['ds_website'];

if(!empty($ds_website)){
    $ds_url_website=$ds_website;
    $ds_website="<a href='$ds_website' target='_blank'>$ds_website</a> ";
    
}else{
    
    $ds_url_website="javascript:void(0);";
}



$direccion1=$ds_number." ".$ds_street;
$direccion2=$ds_state." ".$ds_city." ".$ds_zip;
$direccion3=$ds_codigo_pais." ".$ds_codigo_area." ".$no_telefono;

if((empty($ds_foto))||($ds_foto=='null')){
    $ruta_imagen_instituto="site/../img/Partner_School_Logo.jpg";
}else{
     $ruta_imagen_instituto="site/uploads/$fl_instituto/".$ds_foto;
}
#Recuperamos el template.
$template  =GeneraTemplate(1,188);
$template .=GeneraTemplate(2,188);
$template .=GeneraTemplate(3,188);

#Reemplazamos valores.
$template =str_replace("#nb_instituto#",$nb_instituto,$template);
$template =str_replace("#ds_number#",$ds_number,$template);
$template =str_replace("#ds_street#",$ds_street,$template);
$template =str_replace("#ds_state#",$ds_state,$template);
$template =str_replace("#ds_city#",$ds_city,$template);
$template =str_replace("#ds_zip#",$ds_zip,$template);
$template =str_replace("#ds_codigo_pais#",$ds_codigo_pais,$template);
$template =str_replace("#ds_codigo_area#",$ds_codigo_area,$template);
$template =str_replace("#no_telefono#",$no_telefono,$template);
$template =str_replace("#ds_website#",$ds_website,$template);
$template =str_replace("#ds_url_website#",$ds_url_website,$template);
$template =str_replace("#institute_logo#",$ruta_imagen_instituto,$template);
$template=str_replace("#link_certificate#",$link_pdf,$template);
$template=str_replace("#fe_creacion#",$fe_creacion,$template);
$template=str_replace("#fe_terminacion_plan#",$fe_periodo_final,$template);
$template=str_replace("#fe_interval_recent_accreditation#",$interval_recent_accreditation,$template);
$template=str_replace("#fe_interval_next_accreditation#",$fe_interval_next_accreditation,$template);
$template=str_replace("#status_accreditation#",$status_accreditation,$template);
$template=str_replace("#icono_pdf#",$icono_pdf,$template);
$template=str_replace("#status_accredited#",$status_accredited,$template);

?>
<!DOCTYPE html>
<html lang="en-us">
	<head>
		<meta charset="utf-8">
		<title><?php echo ObtenEtiqueta(1934); ?></title>
		<meta name="description" content="">
		<meta name="author" content="">
			
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		
		<!-- #CSS Links -->
		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css">

		<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-production-plugins.min.css">
		<!-------------------------<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-production.min.css">-->
		<!-------------------------<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-skins.min.css">---------->

		<!-- SmartAdmin RTL Support -->
		<!-------------------<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-rtl.min.css"> -------------->

		<!-- We recommend you use "your_style.css" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
		<link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<!-------------------<link rel="stylesheet" type="text/css" media="screen" href="css/demo.min.css">------>

		<!-- #FAVICONS -->
		<link rel="shortcut icon" href="https://campus.vanas.ca/fame/img/fame.ico" type="image/x-icon">
		<link rel="icon" href="https://campus.vanas.ca/fame/img/fame.ico" type="image/x-icon">

		<!-- #GOOGLE FONT -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script>

    $(function () {
        var div = $(".sticky");
        $(window).scroll(function () {
            var scroll = $(window).scrollTop();

            if (scroll >= 100) {
                div.removeClass('hidden-lg').addClass("visible-lg");
            } else {
                div.removeClass("visible-lg").addClass('hidden-lg');
            }
        });
    });

</script>
	</head>

<body>
<!--sticky desktop menu-->
<div id="nav">
    <div class="navbar navbar-default navbar-fixed-top  navbar-inverse navbar-static-top" role="navigation" style="background-color: #fff;border-color: #fff;">
        <div class="navbar-header">
                    <a href="<?php echo ObtenConfiguracion(116);?>"><img src="<?php echo ObtenConfiguracion(116);?>/images/<?php echo $nb_logo_fame;?>" alt="FAME Logo"></a>

            <a class="navbar-brand" href="/"></a>
        </div>
        <div class="collapse navbar-collapse">
           

        </div>
        <!--/.nav-collapse -->
    </div>
</div>
<br /><br /><br /><br />
<div id='main' role='main'>
    <div class="container " id="content">

        <div class="row">
            <div class="col-md-12">

                
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-title txt-color-blueDark text-center well" style="border:0px;background: none repeat 0 0 #2fabff;color:#fff;">						
								            <?php echo ObtenEtiqueta(2667);?><br>
								            <small class="text-primary" style="color:#fff;">as of <?php echo $fe_actual;?></small>
                </h1>
            </div>
        </div>

          <?php echo $template;?>

     
    </div>
    <br />
</div>

	<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>

		<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script>
		    if (!window.jQuery) {
		        document.write('<script src="js/libs/jquery-3.2.1.min.js"><\/script>');
		    }
		</script>

		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<script>
		    if (!window.jQuery.ui) {
		        document.write('<script src="js/libs/jquery-ui.min.js"><\/script>');
		    }
		</script>

		<!-- IMPORTANT: APP CONFIG -->
		<script src="js/app.config.js"></script>

		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
		<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> 

		<!-- BOOTSTRAP JS -->
		<script src="js/bootstrap/bootstrap.min.js"></script>

		<!-- CUSTOM NOTIFICATION -->
		<script src="js/notification/SmartNotification.min.js"></script>

		<!-- JARVIS WIDGETS -->
		<script src="js/smartwidgets/jarvis.widget.min.js"></script>

		<!-- EASY PIE CHARTS -->
		<script src="js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

		<!-- SPARKLINES -->
		<script src="js/plugin/sparkline/jquery.sparkline.min.js"></script>

		<!-- JQUERY VALIDATE -->
		<script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>

		<!-- JQUERY MASKED INPUT -->
		<script src="js/plugin/masked-input/jquery.maskedinput.min.js"></script>

		<!-- JQUERY SELECT2 INPUT -->
		<script src="js/plugin/select2/select2.min.js"></script>

		<!-- JQUERY UI + Bootstrap Slider -->
		<script src="js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

		<!-- browser msie issue fix -->
		<script src="js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

	

		<!--[if IE 8]>

		<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

		<![endif]-->

	

		<!-- MAIN APP JS FILE -->
		<script src="js/app.min.js"></script>

		

		

		<!-- PAGE RELATED PLUGIN(S) 
		<script src="..."></script>-->

		<script>

		    $(document).ready(function () {

		        /* DO NOT REMOVE : GLOBAL FUNCTIONS!
				 *
				 * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
				 *
				 * // activate tooltips
				 * $("[rel=tooltip]").tooltip();
				 *
				 * // activate popovers
				 * $("[rel=popover]").popover();
				 *
				 * // activate popovers with hover states
				 * $("[rel=popover-hover]").popover({ trigger: "hover" });
				 *
				 * // activate inline charts
				 * runAllCharts();
				 *
				 * // setup widgets
				 * setup_widgets_desktop();
				 *
				 * // run form elements
				 * runAllForms();
				 *
				 ********************************
				 *
				 * pageSetUp() is needed whenever you load a page.
				 * It initializes and checks for all basic elements of the page
				 * and makes rendering easier.
				 *
				 */

		        pageSetUp();

		        /*
				 * ALL PAGE RELATED SCRIPTS CAN GO BELOW HERE
				 * eg alert("my home function");
				 * 
				 * var pagefunction = function() {
				 *   ...
				 * }
				 * loadScript("js/plugin/_PLUGIN_NAME_.js", pagefunction);
				 * 
				 * TO LOAD A SCRIPT:
				 * var pagefunction = function (){ 
				 *  loadScript(".../plugin.js", run_after_loaded);	
				 * }
				 * 
				 * OR
				 * 
				 * loadScript(".../plugin.js", run_after_loaded);
				 */

		    })

		</script>

		<!-- Your GOOGLE ANALYTICS CODE Below -->
		<script>
		    var _gaq = _gaq || [];
		    _gaq.push(['_setAccount', 'UA-XXXXXXXX-X']);
		    _gaq.push(['_trackPageview']);

		    (function () {
		        var ga = document.createElement('script');
		        ga.type = 'text/javascript';
		        ga.async = true;
		        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		        var s = document.getElementsByTagName('script')[0];
		        s.parentNode.insertBefore(ga, s);
		    })();

		</script>

	</body>

</html>

<?php 
#Genera Template 
function GeneraTemplate($opc, $fl_template = 0){
    # Recupera datos del template del documento
    switch ($opc) {
        case 1:
            $campo = "ds_encabezado";
            break;
        case 2:
            $campo = "ds_cuerpo";
            break;
        case 3:
            $campo = "ds_pie";
            break;
        case 4:
            $campo = "nb_template";
            break;
    }

    # Obtenemos la informacion del template header body or footer
    $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
    $row = RecuperaValor($Query1);

    $cadena = $row[0];
    # Sustituye caracteres especiales
    $cadena = $row[0];
    $cadena = str_replace("&lt;", "<", $cadena);
    $cadena = str_replace("&gt;", ">", $cadena);
    $cadena = str_replace("&quot;", "\"", $cadena);
    $cadena = str_replace("&#039;", "'", $cadena);
    $cadena = str_replace("&#061;", "=", $cadena);
    $cadena = str_replace("&nbsp;", " ", $cadena);
    $cadena = html_entity_decode($cadena);
    return $cadena;

}
?>