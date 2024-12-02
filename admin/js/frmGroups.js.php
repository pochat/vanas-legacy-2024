<?php

  # Libreria de funciones
  require '../lib/general.inc.php';
  
  # Previene descargas si se ejecuta directamente en el URL
  if(!ValidaSesion( ))
    exit;
?>

function Inserta(semana, clave) 
{
  // alert('fl_semana '+document.datos['fl_semana_'+semana].value);
  $.ajax({
    type: 'POST',
    url : 'div_lecciones.php',
    data: 'accion=inserta'+
          '&clave='+clave+
          '&fl_sem='+document.datos['fl_semana_'+semana].value,
    async: false,
    success: function(html) {
      $('#div_lecciones').html(html);
    }
  });
}

function Borra(semana, clave) 
{
  // alert('fl_semana '+document.datos['fl_semana_'+semana].value);
  $.ajax({
    type: 'POST',
    url : 'div_lecciones.php',
    data: 'accion=borra'+
          '&clave='+clave+
          '&fl_clas='+document.datos['fl_clase_'+semana].value,
    async: false,
    success: function(html) {
      $('#div_lecciones').html(html);
    }
  });
}

function Actualiza(semana, clave) 
{
  // alert('fl_semana '+document.datos['fl_semana_'+semana].value);
  var obliga;
  if(document.datos['fg_obligatorio'+semana].checked == true)
    obliga = '1';
  else
    obliga = '0';
    
  $.ajax({
    type: 'POST',
    url : 'div_lecciones.php',
    data: 'accion=actualiza'+
          '&clave='+clave+
          '&fl_clas='+document.datos['fl_clase_'+semana].value+
          '&fe_clas='+document.datos['fe_clase_'+semana].value+
          '&hr_clas='+document.datos['hr_clase_'+semana].value+
          '&fg_obliga='+obliga,
    async: false,
    success: function(html) {
      $('#div_lecciones').html(html);
    }
  });
}

  