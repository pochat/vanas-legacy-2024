<?php

  # Libreria de funciones
  require '../lib/general.inc.php';
  
  # Previene descargas si se ejecuta directamente en el URL
  if(!ValidaSesion( ))
    exit;
?>

function ActualizaDias( ) {
  
  var fe1 = $('#fe_ini').val().split("-");
  var d1 = new Date(fe1[2], parseFloat(fe1[1])-1, parseFloat(fe1[0]));
  var fe2 = $('#fe_fin').val().split("-");
  var d2 = new Date(fe2[2], parseFloat(fe2[1])-1, parseFloat(fe2[0]));
  
  var res = d2.getTime() - d1.getTime();
  var dias = (Math.floor(res / (1000 * 60 * 60 * 24))) + 1;
  
  if(!dias)
    var dias = 0;
  else
    var dias = dias;
  
  $('#dias').html(dias);
}
