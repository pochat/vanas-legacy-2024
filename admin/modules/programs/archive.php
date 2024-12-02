<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  #Recibe parametro
  $clave = RecibeParametroNumerico('clave');
  $fg_archive = RecibeParametroNumerico('fg_archive');
  
  if(!empty($clave)){
    $Query  = "UPDATE c_programa SET fg_archive = '$fg_archive' ";
    $Query .= "WHERE fl_programa = $clave";
    EjecutaQuery($Query);
    echo 1; //enviamos uno para validar que se actualizo
  }
  
?>