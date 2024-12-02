<?php
  
  #Programa que suma el valor de los criterios de un rubric
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  $pk = RecibeParametroNumerico('pk');
  $name = RecibeParametroNumerico('name');
  $value = RecibeParametroNumerico('value');  
  
  EjecutaQuery("UPDATE k_criterio_curso SET no_valor = $value WHERE fl_criterio = $pk AND fl_programa = $name");
  
  $valida = RecibeParametroNumerico('valida');  
  $cle = RecibeParametroNumerico('cle');  
  
  if($valida == 1){
    $suma = RecuperaValor("SELECT SUM(no_valor) FROM k_criterio_curso WHERE fl_programa = $cle");
    echo $suma[0];
  }
  
?>