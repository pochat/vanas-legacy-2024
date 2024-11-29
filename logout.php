<?php
  
  # Libreria general de funciones
  require 'lib/sp_general.inc.php';
  
  # Termina la sesion
  TerminaSesion(False);
  
  # Reditrige al home
  header("Location: ".PAGINA_INICIO);
  
?>