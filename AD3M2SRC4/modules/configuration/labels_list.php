<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  # Recibe Parametros  
  parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
  $_POST += $advanced_search;
  $fg_label = $_POST['fg_label'];
 

  
 #Recupermaos todos los labels.
  
  # Consulta para el listado
  $Query  = "SELECT cl_etiqueta, nb_etiqueta, ";
  $Query .= "ds_etiqueta, ds_etiqueta_esp, ds_etiqueta_fra ";
  $Query .= "FROM c_etiqueta ";
  if($fg_label==1)#Campus
      $Query .= "WHERE fg_sistema='0'  ";    
  if($fg_label==2)#FAME
  $Query .= "WHERE fg_sistema='1'  ";
  $Query .= "ORDER BY cl_etiqueta ;";

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
      $cl_etiqueta = $row["cl_etiqueta"];
      $nb_etiqueta = str_texto($row["nb_etiqueta"]);
      $ds_etiqueta = htmlspecialchars($row["ds_etiqueta"], ENT_QUOTES, "UTF-8");
      $ds_etiqueta_esp = htmlspecialchars($row["ds_etiqueta_esp"], ENT_QUOTES, "UTF-8");
      $ds_etiqueta_fra = htmlspecialchars($row["ds_etiqueta_fra"], ENT_QUOTES, "UTF-8");
    echo '
    {      
      "cl_etiqueta": "<div><a href=\'javascript:EnviaFame(\"labels_frm.php\",'.$cl_etiqueta.');\'>'.$cl_etiqueta.' </a></div>",      
      "name": "<td><a href=\'javascript:EnviaFame(\"labels_frm.php\",'.$cl_etiqueta.');\'>'.$nb_etiqueta.'</a></td>",                   
      "ds_etiqueta": "<td><a href=\'javascript:EnviaFame(\"labels_frm.php\",'.$cl_etiqueta.');\'>'.$ds_etiqueta.'</a></td>",                   
      "ds_etiqueta_esp": "<td><a href=\'javascript:EnviaFame(\"labels_frm.php\",'.$cl_etiqueta.');\'>'.$ds_etiqueta_esp.'</a></td>",                   
      "ds_etiqueta_fra": "<td><a href=\'javascript:EnviaFame(\"labels_frm.php\",'.$cl_etiqueta.');\'>'.$ds_etiqueta_fra.'</a></td>"
    }';

      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]
}
