<?php
  
  #Programa que suma el valor de los criterios de un rubric
  # Libreria de funciones	
  require("../lib/self_general.php");
  
  $pk = RecibeParametroNumerico('pk');
  $name = RecibeParametroNumerico('name');
  $value = RecibeParametroNumerico('value');  
  
  EjecutaQuery("UPDATE k_criterio_programa_fame SET no_valor = $value WHERE fl_criterio = $pk AND fl_programa_sp = $name");
  
  $valida = RecibeParametroNumerico('valida');  
  $cle = RecibeParametroNumerico('cle');  
  
  if($valida == 1){
    $suma = RecuperaValor("SELECT SUM(no_valor) FROM k_criterio_programa_fame WHERE fl_programa_sp = $cle");
    echo $suma[0];
  }
  
?>