<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
 
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
 
  #RecibeParametros.
  $valor= $_POST['value']; 

  $Query="UPDATE c_configuracion SET ds_valor='$valor' WHERE cl_configuracion=26 ";
  EjecutaQuery($Query); 
 
 
 

?>