<?php
  
  # Libreria general de funciones
  require 'lib/general.inc.php';
  
  header("Location: modules/campus/dashboard.php");
  
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  # Presenta home del sistema
  PresentaHeader( );
  
  # Presenta Encabezado
  PresentaEncabezado();
  

  
  ?>
  

