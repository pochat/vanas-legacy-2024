<?php
  
  # Libreria de funciones
  require('../../lib/general.inc.php');

  $frecuencias = array('Full Payment','Monhtly','Per Term','Annually','Trimesterly','Semesterly','BiWeekly','Two payments','Monthly');
  $Query .= "SELECT ds_contrato,concat(ds_nombres,' ', ds_apaterno),ds_login,b.cl_sesion,no_contrato FROM c_usuario a, k_app_contrato b
  WHERE a.cl_sesion = b.cl_sesion AND a.fg_activo='1' ";
  $rs = EjecutaQuery($Query);
  for($j=0;$row= RecuperaRegistro($rs);$j++){
    $ds_cuerpo = $row[0];
    $INI = strpos($ds_cuerpo,'X&lt;/span&gt;');
    $ds_cuerpo = substr ( $ds_cuerpo,$INI,500);    
    for($i=0;$i<sizeof($frecuencias);$i++){
      $pos = strpos($ds_cuerpo,$frecuencias[$i]);
      if($pos){
        $fre_obt = substr($ds_cuerpo,$pos,strlen($frecuencias[$i]));
        $Query = "UPDATE k_app_contrato SET ds_frecuencia='$fre_obt' WHERE cl_sesion='".$row[3]."' AND no_contrato=".$row[4]." ";
        EjecutaQuery($Query);
        break;
      }
    }
  }
  
?>