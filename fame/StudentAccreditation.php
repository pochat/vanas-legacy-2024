<?php

/**
 * Import other classes
 */
require('lib/self_general.php');

$type = RecibeParametroNumerico('type', True);
$fl_user = RecibeParametroNumerico('data', True);
$fl_pgrm = RecibeParametroNumerico('prgm', True);

/**
 * Set id_template
 */
$id_template = 203;
//  $id_template = 198;

// $fl_instituto = 4;

// Get alumno data
$Query = "SELECT ds_login, ds_nombres, ds_apaterno, ds_amaterno, cl_sesion, fl_instituto
            FROM c_usuario
            WHERE fl_usuario = $fl_user";

$row = RecuperaValor($Query);


$ds_login = str_texto($row[0]);
$ds_nombres = str_texto($row[1]);
$ds_apaterno = str_texto($row[2]);
$ds_amaterno = str_texto($row[3]);
$cl_sesion = $row[4];
$fl_instituto = $row[5];
// $cl_sesion = $row[5];
// $no_promedio_t = $row[6];
// $fe_alta = $row[7];

$fe_actual = ObtenFechaActualFAME(true);
$fe_alta = strtotime('+0 day', strtotime($fe_alta));
$fe_alta = date('m/Y', $fe_alta);
$full_name = $ds_nombres . ' ' . $ds_apaterno . ' ' . $ds_amaterno;

// Get dates
$Query = "SELECT nb_programa 
            FROM c_programa_sp
            WHERE fl_programa_sp = $fl_pgrm";

$row = RecuperaValor($Query);

$nb_programa = $row[0];

// Get dates
$Query = "SELECT fe_inicio_programa, fe_final_programa, fl_usu_pro, fg_terminado, fg_certificado
            FROM k_usuario_programa
            WHERE fl_usuario_sp = $fl_user
            AND fl_programa_sp = $fl_pgrm";

$row = RecuperaValor($Query);

$start_date = date('F j, Y', strtotime($row[0]));
$end_date = date('F j, Y', strtotime($row[1]));
$fl_usu_pro = $row[2];

$fg_terminado = $row[3];
$fg_certificado =  $row[4];

if(!$row[1]){
$Query = "SELECT fe_periodo_final 
            FROM k_current_plan 
            WHERE fl_instituto =$fl_instituto";
$row = RecuperaValor($Query);
$end_date = date('F j, Y', strtotime($row[0]));
}


# Recupera datos del aplicante: forma 1
$Query  = "SELECT ds_fname, ds_mname, ds_lname, ";
$Query .= ConsultaFechaBD('fe_birth', FMT_FECHA) . " fe_birth, ";
$Query .= "nb_programa, ";
$Query .= ConsultaFechaBD('fe_inicio', FMT_FECHA) . " fe_inicio, ";
$Query .= "b.fl_programa, c.fl_periodo,c.nb_periodo ";
$Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
$Query .= "WHERE a.fl_programa=b.fl_programa ";
$Query .= "AND a.fl_periodo=c.fl_periodo ";
$Query .= "AND a.ds_add_country=d.fl_pais ";
$Query .= "AND a.ds_eme_country=e.fl_pais ";
$Query .= "AND cl_sesion='$cl_sesion'";

$row = RecuperaValor($Query);

$ds_fname = str_texto($row[0]);
$ds_mname = str_texto($row[1]);
$ds_lname = str_texto($row[2]);
$fe_birth = $row[3];
// $nb_programa = $row[4];
$nb_periodo_temp = explode("-", $row[5]);
$nb_periodo = substr(ObtenNombreMes($nb_periodo_temp[1]), 0, 3) . ' ' . $nb_periodo_temp[0] . ', ' . $nb_periodo_temp[2];;
$fl_programa = $row[6];
$fl_periodo = $row[7];


# Obtenemos el periodo inicial cuando ya tenga un term definido
$Queryp = "SELECT nb_periodo FROM k_term a, c_periodo b ";
$Queryp .= "WHERE fl_term=(SELECT  MIN(fl_term)FROM k_alumno_term WHERE ";
$Queryp .= "fl_alumno=$fl_user) AND a.fl_periodo=b.fl_periodo ";
$rowp = RecuperaValor($Queryp);

$st_inicio =  $rowp[0];

$st_inicio = str_replace("th", '', $st_inicio);
$st_inicio = str_replace("st", '', $st_inicio);
$st_inicio = str_replace("nd", '', $st_inicio);

# Recupera datos de Official Transcript
$Query = "SELECT " . ConsultaFechaBD('fe_fin', FMT_FECHA) . " fe_fin,
    " . ConsultaFechaBD('fe_completado', FMT_FECHA) . " fe_completado,
    " . ConsultaFechaBD('fe_emision', FMT_FECHA) . " fe_emision,
    " . ConsultaFechaBD('fe_graduacion', FMT_FECHA) . " fe_graduacion
    FROM k_pctia
    WHERE fl_alumno = $fl_user
    AND fl_programa = $fl_programa ";

$row = RecuperaValor($Query);

$fe_fin_temp = explode("-", $row[0]);
$fe_fin = substr(ObtenNombreMes($fe_fin_temp[1]), 0, 3) . ' ' . $fe_fin_temp[0] . ', ' . $fe_fin_temp[2];
$fe_completado_temp = explode("-", $row[1]);
$fe_completado = substr(ObtenNombreMes($fe_completado_temp[1]), 0, 3) . ' ' . $fe_completado_temp[0] . ', ' . $fe_completado_temp[2];
$fe_emision_temp = explode("-", $row[2]);
$fe_emision = substr(ObtenNombreMes($fe_emision_temp[1]), 0, 3) . ' ' . $fe_emision_temp[0] . ', ' . $fe_emision_temp[2];
$fe_graduacion_temp = explode("-", $row[3]);
$fe_graduacion = substr(ObtenNombreMes($fe_graduacion_temp[1]), 0, 3) . ' ' . $fe_graduacion_temp[0] . ', ' . $fe_graduacion_temp[2];

#Obtenemos la fecha actual.
$fe_actual = ObtenFechaActualFAME();
$fe_actual = GeneraFormatoFecha($fe_actual);

$Query = "SELECT nb_archivo FROM c_imagen WHERE cl_imagen=303 ";
$row = RecuperaValor($Query);
$nb_logo_fame = $row['nb_archivo'];

$Query = "Select CURDATE() ";
$row = RecuperaValor($Query);
$date_actual = str_texto($row[0]);
$date_actual = strtotime('+0 day', strtotime($date_actual));
$date_actual = date('Y-m-d', $date_actual);

#Recupermos Datois generale de la escuela.
$Query = "SELECT * FROM c_instituto WHERE fl_instituto=$fl_instituto ";
$row = RecuperaValor($Query);
$nb_instituto = $row['ds_instituto'];
$ds_codigo_pais = $row['ds_codigo_pais'];
$ds_codigo_area = $row['ds_codigo_area'];
$no_telefono = $row['no_telefono'];
$ds_foto = $row['ds_foto'];
$fl_usuario = $row['fl_usuario_sp'];
$fe_creacion = $row['fe_creacion'];

$fe_creacion = strtotime('+0 day', strtotime($fe_creacion));
$fe_creacion = date('m/Y', $fe_creacion);

$Query = "SELECT * FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
$row = RecuperaValor($Query);
$fe_periodo_inicial = $row['fe_periodo_inicial'];
$fe_periodo_final = $row['fe_periodo_final'];

#Recuperamos el a�o actual de su periodo
$recent_ini_accreditation = strtotime('+0 day', strtotime($fe_periodo_inicial));
$recent_ini_accreditation = date('Y', $recent_ini_accreditation);
$recent_fin_accreditation = strtotime('+0 day', strtotime($fe_periodo_final));
$recent_fin_accreditation = date('Y', $recent_fin_accreditation);
$interval_recent_accreditation = $recent_ini_accreditation . "-" . $recent_fin_accreditation;

#Next acredited(se refiere al a�o siguiente de su periodo)
$next_ini_accreditation = strtotime('+0 day', strtotime($fe_periodo_final));
$next_ini_accreditation = date('Y', $next_ini_accreditation);

$next_fin_accreditation = strtotime('+1 year', strtotime($fe_periodo_final));
$next_fin_accreditation = date('Y', $next_fin_accreditation);

$fe_interval_next_accreditation = $next_ini_accreditation . "-" . $next_fin_accreditation;


// if ($date_actual < $fe_periodo_final) {
if ($fg_certificado == '1' && $fg_terminado == '1') {

    $status_accreditation = "<span class='text-success'><i class='fa fa-check-circle-o' aria-hidden='true'></i> Accredited</span>";
    $status_accredited = "<span class='text-success' style='font-size:28px;'><i style='font-size:35px;' class='fa fa-check-circle-o' aria-hidden='true'></i><br>Accredited</span>";
} else {
    $status_accreditation = "<span class='text-danger'><i class='fa fa-times-circle-o' aria-hidden='true'></i>Not Accredited</span>";
    $status_accredited = "<span class='text-danger'style='font-size:28px;'><i style='font-size:35px;' class='fa fa-times-circle-o' aria-hidden='true'></i><br>Not Accredited</span>";
}



$fe_periodo_final = GeneraFormatoFecha($row['fe_periodo_final']);


#Recupermos la direccion:
$Query = "SELECT * FROM k_usu_direccion_sp WHERE fl_usuario_sp=$fl_usuario ";
$row = RecuperaValor($Query);
$ds_state = $row['ds_state'];
$ds_city = $row['ds_city'];
$ds_number = $row['ds_number'];
$ds_street = $row['ds_street'];
$ds_zip = $row['ds_zip'];

#Recuperamos el website.
$Query = "SELECT * FROM c_administrador_sp WHERE fl_adm_sp=$fl_usuario ";
$row = RecuperaValor($Query);
$ds_website = $row['ds_website'];

if (!empty($ds_website)) {
    $ds_url_website = $ds_website;
    $ds_website = "<a href='$ds_website' target='_blank'>$ds_website</a> ";
} else {

    $ds_url_website = "javascript:void(0);";
}



$direccion1 = $ds_number . " " . $ds_street;
$direccion2 = $ds_state . " " . $ds_city . " " . $ds_zip;
$direccion3 = $ds_codigo_pais . " " . $ds_codigo_area . " " . $no_telefono;

if ((empty($ds_foto)) || ($ds_foto == 'null')) {
    $ruta_imagen_instituto = "img/Partner_School_Logo.jpg";
} else {
    $ruta_imagen_instituto = "site/uploads/$fl_instituto/" . $ds_foto;
}
#Recuperamos el template.
$template  = GeneraTemplate(1, $id_template);
$template .= GeneraTemplate(2, $id_template);
$template .= GeneraTemplate(3, $id_template);

#Reemplazamos valores.
$template = str_replace("#st_fullname#", $full_name, $template);
$template = str_replace("#ds_website#", $ds_login, $template);
$template = str_replace("#pg_name#", $nb_programa, $template);
$template = str_replace("#ds_url_website#", $ds_url_website, $template);
$template = str_replace("#institute_logo#", '../images/fame_logo.png', $template);
$template = str_replace("#link_certificate#", $link_pdf, $template);
$template = str_replace("#pg_stdate#", $start_date, $template);
$template = str_replace("#pg_edate#", $end_date, $template);
$template = str_replace("#iss_date#", $fe_actual, $template);
// $template = str_replace("#graduation_date#", $fe_graduacion, $template);
// is Diploma
if ($type == 1) {
    $template = str_replace("#download_document#", 'Certificate', $template);
}
// is transcript teacher
if ($type == 2) {
    $template = str_replace("#download_document#", 'Official Transcript', $template);
}
// is transcript quiz
if ($type == 3) {
    $template = str_replace("#download_document#", 'Official Transcript', $template);
}

$template = str_replace("#fe_terminacion_plan#", $fe_periodo_final, $template);
$template = str_replace("#fe_interval_recent_accreditation#", $interval_recent_accreditation, $template);
$template = str_replace("#fe_interval_next_accreditation#", $fe_interval_next_accreditation, $template);
$template = str_replace("#status_accreditation#", $status_accreditation, $template);


// is Diploma
if ($type == 1) {
    $icono_pdf = "<span style='font-size:30px;'><a href='site/certificado_pdf.php?u=".$fl_user."&p=".$fl_pgrm."&fg_tipo=2'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> </a></span>";
    $template = str_replace("#icono_pdf#", $icono_pdf, $template);
}
// is transcript teacher
if ($type == 2) {
    $icono_pdf = "<span style='font-size:30px;'><a href='../AD3M2SRC4/modules/reports/transcript_fame_quiz_teacher_rpt.php?c=".$fl_usu_pro."&u=".$fl_user."&i=".$fl_instituto."'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> </a></span>";
    $template = str_replace("#icono_pdf#", $icono_pdf, $template);
}
// is transcript quiz
if ($type == 3) {
    $icono_pdf = "<span style='font-size:30px;'><a href='../AD3M2SRC4/modules/reports/transcript_fame_quiz_rpt.php?c=".$fl_usu_pro."&u=".$fl_user."&i=".$fl_instituto."'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> </a></span>";
    $template = str_replace("#icono_pdf#", $icono_pdf, $template);
}
$template = str_replace("#status_accredited#", $status_accredited, $template);

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
    <!-------------------------<link rel="stylesheet" type="text/css" media="screen" href="fame/css/smartadmin-production.min.css">-->
    <!-------------------------<link rel="stylesheet" type="text/css" media="screen" href="fame/css/smartadmin-skins.min.css">---------->

    <!-- SmartAdmin RTL Support -->
    <!-------------------<link rel="stylesheet" type="text/css" media="screen" href="fame/css/smartadmin-rtl.min.css"> -------------->

    <!-- We recommend you use "your_style.css" to override SmartAdmin
                 specific styles this will also ensure you retrain your customization with each SmartAdmin update.
            <link rel="stylesheet" type="text/css" media="screen" href="fame/css/your_style.css"> -->

    <!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
    <!-------------------<link rel="stylesheet" type="text/css" media="screen" href="fame/css/demo.min.css">------>

    <!-- #FAVICONS -->
    <link rel="shortcut icon" href="https://campus.vanas.ca/fame/img/fame.ico" type="image/x-icon">
    <link rel="icon" href="https://campus.vanas.ca/fame/img/fame.ico" type="image/x-icon">

    <!-- #GOOGLE FONT -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script>
        $(function() {
            var div = $(".sticky");
            $(window).scroll(function() {
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
                <!-- <a href="<?php echo ObtenConfiguracion(116); ?>"><img src="<?php echo ObtenConfiguracion(116); ?>/images/<?php echo $nb_logo_fame; ?>" alt="FAME Logo"></a> -->

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
                        <?php echo 'Statement of FAME Accreditation Status'; ?><br>
                        <small class="text-primary" style="color:#fff;">as of <?php echo $fe_actual; ?></small>
                    </h1>
                </div>
            </div>

            <?php echo $template; ?>


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
    <script src="fame/js/app.min.js"></script>





    <!-- PAGE RELATED PLUGIN(S)
            <script src="..."></script>-->

    <script>
        $(document).ready(function() {

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
             * loadScript("fame/js/plugin/_PLUGIN_NAME_.js", pagefunction);
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

        (function() {
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
function GeneraTemplate($opc, $fl_template = 0)
{
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
