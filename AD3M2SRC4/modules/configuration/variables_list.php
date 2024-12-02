<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  # Recibe Parametros  
  parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
  $_POST += $advanced_search;
  $fg_label = isset($_POST['fg_label'])?$_POST['fg_label']:NULL;
 

  
 #Recupermaos todos los labels.
  $Query  = "SELECT cl_configuracion, ds_configuracion, ";
  $Query .= "ds_valor ";
  $Query .= "FROM c_configuracion ";
  $Query .= "WHERE fg_admin='0' ";
  if($fg_label==1)#Campus
      $Query .= "AND fg_sistema='0'  ";    
  if($fg_label==2)#FAME
  $Query .= "AND fg_sistema='1' ";
  
  $Query .= "ORDER BY cl_configuracion ";

  
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
      $cl_configuracion = $row[0];
      $ds_configuracion = str_texto($row[1]);
      $ds_valor = str_texto($row[2]);          
      

    echo '
    {      
      "cl_configuracion": "<div><a href=\'javascript:EnviaFame(\"varaiables_frm.php\",'.$cl_configuracion.');\'>'.$cl_configuracion.' </a></div>",      
      "ds_configuracion": "<td><a href=\'javascript:EnviaFame(\"variables_frm.php\",'.$cl_configuracion.');\'>'.$ds_configuracion.'</a></td>",                   
      "ds_valor": "<td><a href=\'javascript:EnviaFame(\"variables_frm.php\",'.$cl_configuracion.');\'>'.$ds_valor.'</a></td>",    
      "eliminar": "<td><a href=\'javascript:Borra(\"variables_del.php\",'.$cl_configuracion.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a></td>"   
    }';
	
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
		
    }
    ?>
   ]

}
