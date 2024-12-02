<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  $no_semana = RecibeParametroNumerico('no_semana');
  $no_grado = RecibeParametroNumerico('no_grado');
  $fl_programa = RecibeParametroNumerico('fl_programa');

  $row = RecuperaValor("SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa AND no_grado = $no_grado AND no_semana = $no_semana");
  
  if(!empty($row[0]))
    echo "1";
  else
    echo "0";
  
?>