<?php
  
  #Archivo para mostrar los grados de cada programa que se eleje en fixed_frm.php
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $Query = "SELECT no_grados FROM c_programa WHERE fl_programa=$fl_programa";
  $row = RecuperaValor($Query);
  $no_grados= $row[0];
  #Muestra los grados dependiendo del programa que haya elejido
  for($i=0;$i<=$no_grados;$i++){
    echo "<option value='$i'>";if($i==0) echo ObtenEtiqueta(70); else echo $i; echo "</option>";
  }
  
?>