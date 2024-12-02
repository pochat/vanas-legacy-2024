<?php
  # Librerias
  require '../../lib/general.inc.php';
 
  # Consulta para el listado
  $Query  = "SELECT fl_accion, cl_evento,ds_accion,no_puntos,ds_imagen 
             FROM k_accion  
             WHERE 1=1 ORDER BY fe_creacion DESC ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);  
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++){
        
        $fl_accion=$row['fl_accion'];
		$cl_evento = $row['cl_evento'];
		$ds_accion = str_texto($row['ds_accion']);
        $no_puntos=$row['no_puntos'];
        $ds_imagen=str_texto($row['ds_imagen']);
        
        
        
        #Recuperamos todas las acciones pertencientes ala accion.
        $Query2  = "SELECT nb_evento  ";
        $Query2 .= "FROM c_evento WHERE cl_evento=$cl_evento ";    
        $row2=RecuperaValor($Query2);
        
		$nb_evento = $row2['nb_evento'];
		
        $ds_imagen= PATH_IMAGES."/../../images/gamification/accion/".$ds_imagen;
		
		

		echo '
        {            
			"name": "<a href=\'javascript:Envia(\"actions_frm.php\",'.$fl_accion.');\'><b>'.str_texto($nb_evento).'</b></a>",
			"ds_decripcion": "<td class=\'sorting_1\'><a href=\'javascript:Envia(\"actions_frm.php\",'.$fl_accion.');\'> <small class=\'text-muted\'><i>'.$ds_accion.'</i></small></a></td>",
			"points":"<td><a href=\'javascript:Envia(\"actions_frm.php\",'.$fl_accion.');\'> '.$no_puntos.' </a></td>",
            "ds_image":"<td> <a class=\'zoomimg\' href=\'#\'><img src=\''.$ds_imagen.'\' class=\'away no-border\' width=\'30px\' height=\'30px\'><span style=\'left:-300px;\'><div class=\'modal-dialog demo-modal\'><div class=\'modal-content\'><div class=\'modal-body padding-5\'><img class=\'superbox-current-img\' src=\''.$ds_imagen.'\'></div></div></div></span></a> </td>",
			"btns": "<td><a class=\'btn btn-xs btn-default\' title=\'Edit Delete\' href=\'javascript:Eliminar('.$fl_accion.');\'><i class=\'fa fa-trash-o\'></i></a></td>"
        }';
        
     
        
		if($i<=($registros-1))
			echo ",";
		else
			echo "";
    }
    ?>
    ]




}