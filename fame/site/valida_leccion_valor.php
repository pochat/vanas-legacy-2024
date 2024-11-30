<?php
 # Libreria de funciones	
 require("../lib/self_general.php");
  
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $rubric = RecibeParametroNumerico('rubric');

  if($rubric == 0){
    $row_a = RecuperaValor("SELECT SUM(no_valor_quiz) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa");
    echo $row_a[0];
  }else{
    $row_a = RecuperaValor("SELECT SUM(no_valor_rubric) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa");
    echo $row_a[0];
  }
?>