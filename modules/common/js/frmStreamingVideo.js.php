<?php
  
  include("../lib/cam_general.inc.php");
  
  $watermark = "<div id='div_watermark' style='{position: absolute; top: 50; left: 50; color: white; font-size: 20; z-index:200; opacity:0.5;}'></div>";
  
  $streamer = "rtmp://".ObtenConfiguracion(60)."/oflaDemo";
  $image = SP_IMAGES."/PosterFrame_White.jpg";
  $width = ObtenConfiguracion(13);
  $height = ObtenConfiguracion(14) + 25;
  $bufferTime = ObtenConfiguracion(56);
  $inicio = "<object id='player' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' name='player' width='$width' height='$height'><param name='movie' value='".SP_FLASH."/player.swf' /><param name='allowfullscreen' value='true' /><param name='allowscriptaccess' value='always' /><param name='wmode' value='opaque' /><param name='flashvars' value='file=";
  $medio = "&streamer=$streamer&image=$image&bufferlength=$bufferTime&smoothing=false' /><embed type='application/x-shockwave-flash' id='player2' name='player2' src='".SP_FLASH."/player.swf' width='$width' height='$height' allowscriptaccess='always' allowfullscreen='true' wmode='opaque' flashvars='file=";
  $final = "&streamer=$streamer&image=$image&bufferlength=$bufferTime&smoothing=false' /></object>";
?>

// Muestra dialogo para mostrar video
function ShowVideo(video) {
  
  $('#dlg_video_content').html("<?php echo $watermark.$inicio; ?>" + video + "<?php echo $medio; ?>" + video + "<?php echo $final; ?>");
  $('#dlg_video').dialog('open');
  timer = setInterval("CambiaEtiqueta()", 10000);
}

$(function() {
  
  $('#dlg_video').dialog({
    autoOpen: false,
    resizable: false,
    modal: true,
    width: 745,
    height: 485,
    hide: 'highlight',
    buttons: {
      'Close': function() {
        $(this).dialog('close');
      }
    }
  });
  $('.ui-dialog-titlebar').hide();
});



function CambiaEtiqueta() {
  var aleat = Math.random() * (405 - 20); // 405 alto del video - 20 alto de la etiqueta
  aleat = Math.round(aleat);
  $('#div_watermark').css('top', parseInt(1) + aleat);
  aleat = Math.random() * (720 - 100); // 720 ancho del video - 100 ancho de la etiqueta
  aleat = Math.round(aleat);
  $('#div_watermark').css('left', parseInt(1) + aleat);
  aleat = Math.random() * (20 - 12); // el font estara entre 12 y 20
  aleat = Math.round(aleat);
  $('#div_watermark').css('font-size', parseInt(12) + aleat);
  $('#div_watermark').html($('#matricula').val());
  espera = setTimeout("$('#div_watermark').html('')", 5000);
}
