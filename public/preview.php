<?php
	# Libreria de funciones
	require("../modules/common/lib/cam_general.inc.php");
  
  # Recibbe parametros
  $clave = RecibeParametroHTML('k', False, True);
  if(empty($clave)){
    $clave = RecibeParametroHTML('clave', False, True);
    # Buscamos  el fl_entregable
    $row = RecuperaValor("SELECT fl_entregable FROM k_share WHERE fl_share_face LIKE '%$clave%'");
    $clave = $row[0];
  }
  
  # Verifica que traiga un parametro
  if(!$clave) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Buscamos la imagen y la informacion
  $Query  = "SELECT CONCAT(ds_nombres,' ',ds_apaterno), ";
  $Query .= "(SELECT ds_pais FROM c_pais s, k_ses_app_frm_1 r WHERE r.cl_sesion=c.cl_sesion AND s.fl_pais=r.ds_add_country) pais, ";
  $Query .= "e.ds_titulo,(SELECT nb_tema FROM c_f_tema z WHERE z.fl_tema=f.fl_tema) tema,no_semana, DATE_FORMAT(a.fe_entregado,'%M %d, %Y'), ";
  $Query .= "a.ds_ruta_entregable, c.fl_usuario ";
  $Query .= "FROM k_entregable a, k_entrega_semanal b, c_usuario c, k_semana d, c_leccion e, k_gallery_post f ";
  $Query .= "WHERE a.fl_entrega_semanal = b.fl_entrega_semanal  AND b.fl_alumno=c.fl_usuario  ";
  $Query .= "AND b.fl_semana = d.fl_semana AND d.fl_leccion = e.fl_leccion AND a.fl_entregable = f.fl_entregable AND a. fl_entregable=$clave ";
  $row = RecuperaValor($Query);
  $ds_nombres = $row[0];
  $ds_pais = $row[1];
  $ds_titulo = $row[2];
  $ds_tema = $row[3];
  $no_semana = $row[4];
  $fe_entregado = $row[5];
  $nb_archivo = $row[6];
  $fl_usuario = $row[7];
  $no_term = ObtenGradoAlumno($fl_usuario);
  
  # Obtenemos la extencion del archivo
  $ext = ObtenExtensionArchivo($nb_archivo);
  
  # Ruta del archivo
  switch($ext) {
    case "jpg": 
    case "jpeg": $ruta = PATH_ALU."/sketches/board_thumbs"; break;
    default: $ruta = PATH_ALU."/videos/";
  }
  
  # Header
  echo '<!DOCTYPE html>
  <!--<html lang="en-us">-->
  <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <meta charset="utf-8" >
      <!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->
      <title> Vancouver Animation School Online Campus </title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

      <!-- Basic Styles -->
      <link rel="stylesheet" type="text/css" media="screen" href="'.PATH_N_COM_CSS.'/bootstrap.min.css" >
      <link rel="stylesheet" type="text/css" media="screen" href="'.PATH_N_COM_CSS.'/font-awesome.min.css">
      <!-- SmartAdmin Style -->
      <link rel="stylesheet" type="text/css" media="screen" href="'.PATH_N_COM_CSS.'/smartadmin-production.css">
      <!-- Vanas Style -->
      <link rel="stylesheet" type="text/css" media="screen" href="'.PATH_N_COM_CSS.'/vanas.css">
      <!-- Flowplayer -->
      <link rel="stylesheet" type="text/css" media="screen" href="'.PATH_N_COM_CSS.'/flowplayer/playful.css">';
        if ($page_css) {
          foreach ($page_css as $css) {
            echo '<link rel="stylesheet" type="text/css" media="screen" href="'.PATH_N_COM_CSS.'/'.$css.'">';
          }
        }
    echo '
      <!-- GOOGLE FONT -->
      <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
      <!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
      <meta name="apple-mobile-web-app-capable" content="yes">
      <meta name="apple-mobile-web-app-status-bar-style" content="black">
      <!-- FACEBOOK JS -->
      <script src="'.PATH_N_COM_JS.'/facebook.js.php"></script>
    </head>
  <body class="desktop-detected pace-done" style="min-height: 1000px;">';
  
  # Colocamos un contador para insertar las vistas que se ha realizado del archivo
  EjecutaQuery("UPDATE k_share SET no_visto=no_visto+1 WHERE fl_entregable=$clave");
    
  # validacion para que es lo que semuestra si video o imagen
  if($ext!='ogg'){    
    $image_video = '<img class="img-responsive img-center superbox" src="'.$ruta.'/'.$nb_archivo.'">';
  }
  else{
    $image_video = 
    "<video class='img-responsive img-center  superbox' tabindex='0' controls='controls'>
      <source src='".$ruta.$nb_archivo."' type='video/ogg'>
    </video>
    <div id='seekInfo' style='display:none;'></div>
    <script type='text/javascript' src='../lib/js_player/smpte_test_universal.js'></script>
    <script type='text/javascript' src='../lib/js_player/jquery.jkey-1.2.js'></script>";
    $image_video .= 
    "<script type='text/javascript' src='../modules/common/js/recordCritique.js'></script>
    <script>
      var div_aux = $('<div />').appendTo('body'); 
      div_aux.attr('id', 'libCritiqueIncluidas');
      div_aux.css('display', 'none');
      $('#libCritiqueIncluidas').html('True');
    </script>"; 
  }
  
  echo '
  <div id="row"><div class="col-xs-12 col-sm-12 col-md-12"><br /><br/></div></div>
  <div class="col-xs-12 col-sm-12 col-md-12 padding-10">
  <div id="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
  </div>
  <div id="row">
    <div class="col-lg-3 col-xs-3 col-sm-3 col-md-3"></div>
    <div class="col-lg-6 col-xs-6 col-sm-6 col-md-6">
      <div class="listing">      
        <div class="well well-light">
          <div class="modal-header no-padding">
            <div class="no-margin no-padding">
              <a href="'.ObtenConfiguracion(77).'" target="_blank"><img class="img-responsive img-center" src="'.SP_IMAGES.'/Vanas_doc_logo.jpg" width="315"></a>
            </div>
          </div>
          <div id="item-body" class="modal-body">
            <div class="row">
              <div class="col-xs-12">'.$image_video.'</div>
            </div>
          </div>
          <div id="item-footer" class="modal-footer">
            <span class="h5 text-primary"><strong>'.$ds_nombres.'</strong></span><br /> 
            <span class="text-primary">'.$ds_pais.'</span><br />
            <span class="h4">'.$ds_titulo.'</span><br /> 
            <span class="text-primary">Desktop<br> Week '.$no_semana.' - Assignment - Term '.$no_term.'</span><br>
            <span class="text-muted">'.$fe_entregado.'</span>
          </div>
          <div class="text-align-center"><a class="btn btn-primary" href="'.ObtenConfiguracion(77).'" target="_blank">Visit Vancouver Animation School</a>
          </div>
        </div>
      </div>
    <div class="col-lg-3 col-xs-3 col-sm-3 col-md-3"></div>
  </div>
  <div id="row">
    <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12"></div>
  </div>
  <div id="row">
    <div class="col-lg-3 col-xs-3 col-sm-3 col-md-3"></div>
    <div class="col-lg-6 col-xs-6 col-sm-6 col-md-6">
      <div class="listing">      
        <div class="well well-light no-margin padding-10">
          <div class="text-align-center" style="background-color:#F9F9F9;">
            <span style="color: #c0c0c0;"><strong>Vancouver Animation School</strong><br />Your Education, Your Destination.&nbsp;</span><br /><br />
            <span style="color: #c0c0c0;">1526 Duranleau Street, Vancouver British Columbia</span><br /><br />
            <span style="color: #888888;"><span style="color: #c0c0c0;">www.vanas.ca</span><br /></span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-xs-3 col-sm-3 col-md-3"></div>
  </div>
  </div>';
  
  include '../inc/scripts.php';
  echo '
  </body>
  </html>';

?>
