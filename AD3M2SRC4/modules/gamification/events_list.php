<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Consulta para el listado
  $Query  = "SELECT cl_evento,cl_clave,nb_evento, ds_evento  ";
  $Query .= "FROM c_evento WHERE 1=1 ORDER BY fe_creacion DESC ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);  
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++){
		$cl_evento = $row['cl_evento'];
        $cl_clave = $row['cl_clave'];
		$nb_evento = $row['nb_evento'];
		$ds_evento = $row['ds_evento'];
		

		
		

		echo '
        {       
            "clave": "<a href=\'javascript:Envia(\"events_frm.php\",'.$cl_evento.');\'><b>'.str_texto($cl_clave).'</b></a>",
			"name": "<a href=\'javascript:Envia(\"events_frm.php\",'.$cl_evento.');\'><b>'.str_texto($nb_evento).'</b></a>",
			"ds_decripcion": "<td class=\'sorting_1\'><a href=\'javascript:Envia(\"events_frm.php\",'.$cl_evento.');\'> <small class=\'text-muted\'><i>'.$ds_evento.'</i></small></a></td>",
			
			"btns": "<td><a class=\'btn btn-xs btn-default\' title=\'Edit Delete\' href=\'javascript:Eliminar('.$cl_evento.');\'><i class=\'fa fa-trash-o\'></i></a></td>"
        }';
        
     
        
		if($i<=($registros-1))
			echo ",";
		else
			echo "";
    }
    ?>
    ]
}