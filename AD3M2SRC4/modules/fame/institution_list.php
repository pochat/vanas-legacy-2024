<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
  $fl_instituto= $_POST['extra_filters']['fl_instituto'];

 
  
  $Query  = "SELECT ds_instituto,fg_activo  FROM c_instituto WHERE fl_instituto_rector=$fl_instituto   "; 
   
			 
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
   
        $ds_instituto=$row['ds_instituto'];
        $fg_activo=$row['fg_activo'];
		
		
        
		if($fg_activo==0){
		    $color = "danger";
		    $status="Inactive";
		}else{
		    $color = "success";
		    $status="Active";
		}
		
	
    
            
      echo '
        {
           
            "name": "'.$ds_instituto.' ",        
            "estatus": "<td class=\"text-right\"><span class=\"label label-'.$color.'\">'.$status.'</span>  </td>",
            "espacio": "<td class=\"text-right\"> </td>"
            
            
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
