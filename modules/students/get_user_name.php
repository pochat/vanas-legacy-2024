<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  
  # Recupera nombre del usuario
  $ds_nombre = ObtenNombreUsuario($fl_usuario);
  echo $ds_nombre;
  
?>