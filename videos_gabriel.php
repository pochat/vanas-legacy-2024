<?php
  # Libreria de funciones
  require("lib/sp_general.inc.php");
  
  
  $ruta_videos = "/var/www/html/vanas_videos/";
  $Query = "SELECT fl_leccion, ds_vl_ruta, ds_as_ruta, fe_vl_alta, fe_as_alta FROM c_leccion /*WHERE ds_vl_ruta<>'' AND ds_as_ruta <>''*/ ORDER BY ds_vl_ruta  ";
  $rs = EjecutaQuery($Query);
  echo 
  "<table>
    <thead>
      <tr>
        <td>No</td>
        <td>Clave</td>
        <td>Video lecture</td>
        <td>Existe S/N</td>
        <td>Fecha Lecture</td>
        <td>Fecha Lecture</td>
        <td>Video Brief</td>
        <td>Fecha Brief</td>
      </tr>
    </thead>
    <tbody>";
  for($i=1;$row=RecuperaRegistro($rs);$i++){
    $fl_leccion = $row[0];
    $ds_vl_ruta = $row[1];
    $ds_as_ruta = $row[2];
    $fe_vl_alta = $row[3];
    $fe_as_alta = $row[3];
    if(!empty($ds_vl_ruta)){
      if(file_exists($ruta_videos.$ds_vl_ruta))
        $exite_vl = "<p>Si</p>";
      else
        $exite_vl = "<p style='color:red;'>No</p>";
    }
    else{
      $exite_vl = "<p style='color:blue;'>No tiene video Lecture</p>";
    }
    if(!empty($ds_as_ruta)){
      if(file_exists($ruta_videos.$ds_as_ruta))
        $exite_as = "<p>Si</p>";
      else
        $exite_as = "<p style='color:red;'>No</p>";
    }
    else{
      $exite_as = "<p style='color:blue;'>No tiene video brief</p>";
    }
    echo 
    "<tr>
      <td>".$i."</td>
      <td>".$fl_leccion."</td>
      <td>".$ds_vl_ruta."</td>
      <td>".$exite_vl."</td>
      <td>".$fe_vl_alta."</td>
      <td>".$ds_as_ruta."</td>
      <td>".$exite_as."</td>
      <td>".$fe_as_alta."</td>
    </tr>";   
  }
  echo 
  " </tbody>
  </table>";
?>