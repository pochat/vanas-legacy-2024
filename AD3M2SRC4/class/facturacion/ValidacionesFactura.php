<?php

function CorrigeRFC($rfcOrigen) {
  // Un RFC valido para timbrar la factura no debe contener los siguientes caractertes:
  // - Guion medio
  // - Espacio en blanco
  $rfcCorregido = $rfcOrigen;
  $rfcCorregido = str_replace("-", "", $rfcCorregido);
  $rfcCorregido = str_replace(" ", "", $rfcCorregido);
  
  return $rfcCorregido;
}

function CorrigeDescripcion($valorOrigen) {
  // Las descripciones no deben llevar espacios al final
  $valorCorregido = $valorOrigen;
  $valorCorregido = trim($valorCorregido);
  
  return $valorCorregido;
}

?>