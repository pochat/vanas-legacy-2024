<?php

# Libreria de funciones
require '../../lib/general.inc.php';



# Estudiantes
$Query = "SELECT fl_usuario FROM c_usuario a, k_alumno_grupo b WHERE a.fl_usuario=b.fl_alumno AND fg_activo='1'";
$rs = EjecutaQuery($Query);
for($i=0;$row = RecuperaRegistro($rs); $i++){
  echo "<lu>".$row[0];
  $Query2 = "SELECT no_grado FROM k_alumno_term a, k_term b WHERE a.fl_term=b.fl_term and fl_alumno=$row[0]  ";
  $rs2 = EjecutaQuery($Query2);
  $grado_repetido = 0;
  for($j=0;$row2 = RecuperaRegistro($rs2);$j++){   
    echo "<li>".$row2[0];
    if($grado_repetido == $row2[0])
      echo ObtenEtiqueta(853);
    echo "</li>";
    $no_grado = $row2[0];
    $grado_repetido = $no_grado;
  }
  echo "</lu>";
}

# Cambia y Repite Term
# Cambia Pero no Rerpite Term
# No Cambia y Repite Term
# No Cambia pero no Repite Term
$status = "";
if($cambia){
  $status .= "Cambia";
  if($repite)
    $status .= "Y Repite";
  else
    $status .= "No Repite";
}
else{
  $status .= "No Cambia";
  if($repite)
    $status .= "Y Repite";
  else
    $status .= "No Repite";
}

echo $status;

?>