<?php
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
 $fl_programa = RecibeParametroNumerico('fl_programa');
  $rubric = RecibeParametroNumerico('rubric');
 

    $row_a = RecuperaValor("SELECT SUM(no_valor_rubrics) FROM c_programa WHERE fl_programa = $fl_programa ");
    echo $row_a[0];
 
?>