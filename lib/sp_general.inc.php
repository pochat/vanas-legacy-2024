<?php
  
  # Definicion de librerias para Sitio de Administracion
  require('com_func.inc.php');
  require('sp_config.inc.php');
  require('sp_layout.inc.php');
  
  $langselect = $_COOKIE[IDIOMA_NOMBRE];

  switch ($langselect) {
    case '1': $sufix = '_esp';
      break;
    case '2': $sufix = '';
      break;
    case '3': $sufix = '_fra';
      break;
    default: $sufix = '';
      break;
    }
?>