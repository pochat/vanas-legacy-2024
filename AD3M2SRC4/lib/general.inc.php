<?php
  
  # Definicion de librerias para Sitio de Administracion
  require($_SERVER['DOCUMENT_ROOT'].'/lib/com_func.inc.php');
  require($_SERVER['DOCUMENT_ROOT'].'/AD3M2SRC4/lib/adm_config.inc.php');
  require($_SERVER['DOCUMENT_ROOT'].'/AD3M2SRC4/lib/adm_layout.inc.php');
  require($_SERVER['DOCUMENT_ROOT'].'/AD3M2SRC4/lib/adm_layout_bootstrap.inc.php');
  require($_SERVER['DOCUMENT_ROOT'].'/AD3M2SRC4/lib/adm_paginacion.inc.php');
  /*require('adm_config.inc.php');
  require('adm_layout.inc.php');
  require('adm_layout_bootstrap.inc.php');
  require('adm_paginacion.inc.php');*/
  $replacements = array(
    "'"=>"\'",
    '"'=>'\"'
  );
?>
