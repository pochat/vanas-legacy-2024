<?php
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $rubric = RecibeParametroNumerico('rubric');
  $no_grado=RecibeParametroNumerico('no_grado');
  $no_semana=RecibeParametroNumerico('no_semana');

    $row_a = RecuperaValor("SELECT SUM(no_valor_rubric) FROM c_leccion WHERE fl_programa = $fl_programa AND no_grado=$no_grado ");
    echo $row_a[0];
 
?>