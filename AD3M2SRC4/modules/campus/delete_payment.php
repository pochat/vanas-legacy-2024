<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
 
  
  $fl_alumno_pago = RecibeParametroHTML('fl_alumno_pago');
  EjecutaQuery("DELETE FROM k_alumno_pago where fl_alumno_pago=$fl_alumno_pago");
  
?>