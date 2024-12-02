<?php
  
  # Libreria de funciones
  require('../../lib/general.inc.php');
  
  # Recibe parametros
  $ds_critica_animacion = $_GET['video'];
  
  # Inicia cuerpo de la pagina
  echo "
<!--DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'-->
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
  <meta http-equiv='cache-control' content='max-age=0' />
  <meta http-equiv='cache-control' content='no-cache' />
  <meta http-equiv='expires' content='0'>
  <meta http-equiv='pragma' content='no-cache'>
  <title>Vancouver Animation Online Campus</title>\n
  <link type='text/css' href='/modules/common/css/theme/jquery-ui-1.8rc3.custom.css' rel='stylesheet' />
  <link type='text/css' href='/modules/common/css/campus.css' rel='stylesheet' />
  <link type='text/css' href='/modules/common/css/demos.css' rel='stylesheet' />
  <link type='text/css' href='/modules/common/css/fileuploader.css' rel='stylesheet' />
  <link type='text/css' href='/lib/js_mediaelement/mediaelementplayer.css' rel='stylesheet' />
  <link type='text/css' href='/modules/common/css/jquery.jqzoom.css' rel='stylesheet' />
  <script type='text/javascript' src='/js/AC_RunActiveContent.js'></script>
  <script type='text/javascript' src='/js/swfobject.js'></script>
  <script type='text/javascript' src='/modules/common/js/2leveltab.js'></script>
  <script type='text/javascript' src='/modules/common/js/fileuploader.js'></script>
  <script type='text/javascript' src='/modules/common/js/jquery.MultiFile.js'></script>
  <script type='text/javascript' src='/modules/common/js/jquery-1.4.2.js'></script>
  <script type='text/javascript' src='/modules/common/js/jquery-ui-1.8rc3.custom.min.js'></script>
  <script type='text/javascript' src='/modules/common/js/jquery.ui.widget.js'></script>
  <script type='text/javascript' src='/modules/common/js/jquery.ui.mouse.js'></script>
  <script type='text/javascript' src='/modules/common/js/jquery.ui.slider.js'></script>
  <script type='text/javascript' src='/modules/common/js/jquery.jqzoom-core.js'></script>
  <script type='text/javascript' src='/modules/common/js/frmStreamingVideo.js.php'></script>
</head>
<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0' class='site_background'>
  <div id='dlg_video'><div id='dlg_video_content'></div></div>
  <table border='".D_BORDES."' width='100%' cellspacing='0' cellpadding='0'>
    <div id='dlg_camara'>";
  $pathVideo = "/modules/students/critiques/";
  $videoFile = ObtenNombreArchivo($ds_critica_animacion)."_cam.ogg";
  
  #PresentaVideoHTML5Webcam
  $webcam_top     = "10px";
  $webcam_left    = "10px";
  $webcam_width   = "250px";
  $webcam_height  = "188px";
  echo "
      <div style='position:absolute;width:$webcam_width;height:$webcam_height;top:$webcam_top;left:$webcam_left;'>
        <video id='two' width='250' height='188'>
          <source src='".$pathVideo."".$videoFile."' type='video/ogg'>
        </video>
      </div>
    </div>
    <script type='text/javascript'>
      $(function() {  
        $('#dlg_camara').dialog({
          width: 270,
          height: 225,
          position: [932, 218],
          closeOnEscape: false,
          title: 'Teacher',
          resizable: false,
          beforeClose: function(event, ui) { return false; }
        });
      });
    </script>";
    
  #PresentaVideoHTML5Critique
  echo "
    <tr>
      <td width='10'>&nbsp;</td>
      <td valign='top' width='720' align='center' class='video_sketch'>
        <video id='one' width='720' height='405' controls='controls'>
          <source src='".$pathVideo."".$ds_critica_animacion."' type='video/ogg'>
        </video>
        <script type='text/javascript' src='/modules/common/js/critiquevideos.js'></script>
      </td>
      <td width='10'>&nbsp;</td>
    </tr>
    <tr><td colspan='3' height='5'>&nbsp;</td></tr>";
  
  #Presenta  Boton para cerrar preview
  echo "
    <tr>
      <td colspan='3' align='center' class='default'>
        <br>
        <input type='button' id='buttons' value='".ObtenEtiqueta(74)."' onClick='javascript:window.close();'>
      </td>
    </tr>
   </table>
</body>
</html>";
  
?>