<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Consulta para el listado
  $Query  = "SELECT fl_cupon,nb_cupon, ds_code, ds_descuento, DATE_FORMAT(fe_start, '%M %D, %Y'), DATE_FORMAT(fe_end, '%M %D, %Y'), fg_activo  ";
  $Query .= "FROM c_cupones WHERE 1=1 ORDER BY fe_start";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);  
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++){
		$fl_cupon = $row[0];
		$nb_cupon = $row[1];
		$ds_code = $row[2];
		$ds_descuento = $row[3];
		$fe_start = $row[4];
		$fe_end = $row[5];
		$fg_activo = $row[6];
		if(!empty($fg_activo))
			$activo = "<span class='label label-success'>".ObtenEtiqueta(2270)."</span>";
		else
			$activo = "<span class='label label-danger'>".ObtenEtiqueta(2271)."</span>";
		# Obtenemos los programas
		$Queryj = "SELECT nb_programa FROM k_cupones_course a, c_programa b ";
		$Queryj .= "WHERE a.fl_programa=b.fl_programa AND fl_cupon=$fl_cupon ORDER BY nb_programa ";
		$rsj = EjecutaQuery($Queryj);
		$programas = "";
		for($j=0;$rowj = RecuperaRegistro($rsj);$j++){
			$nb_programa = $rowj[0];
			if($j==0)
				$programas .= "<strong>".$nb_programa."</strong><br>";
			else
				$programas .= "<small class='text-muted'>".$nb_programa."</small></br>";
		}

		echo '
        {            
			"name": "<a href=\'javascript:Envia(\"cupones_frm.php\",'.$fl_cupon.');\'><b>'.str_texto($nb_cupon).'</b></a>",
			"Dstart": "<td class=\'sorting_1\'><a href=\'javascript:Envia(\"cupones_frm.php\",'.$fl_cupon.');\'><strong>'.ObtenEtiqueta(2272).':</strong> '.$fe_start.' <br/><small class=\'text-muted\'><i>'.ObtenEtiqueta(2273).': '.$fe_end.'<i></i></i></small></a></td>",
			"cupon": "<td><a href=\'javascript:Envia(\"cupones_frm.php\",'.$fl_cupon.');\'>'.str_texto($ds_code).'<br></a></td>",
			"programs": "<td><a href=\'javascript:Envia(\"cupones_frm.php\",'.$fl_cupon.');\'>'.$programas.'</a></td>",
			"descuento": "<td><a href=\'javascript:Envia(\"cupones_frm.php\",'.$fl_cupon.');\'>'.$ds_descuento.'</a></td>",
			"status": "<td><a href=\'javascript:Envia(\"cupones_frm.php\",'.$fl_cupon.');\'>'.$activo.'</a></td>",
			"btns": "<td><a class=\'btn btn-xs btn-default\' title=\'Edit record\' href=\'javascript:Envia(\"cupones_frm.php\", '.$fl_cupon.');\' style=\'margin:3px;\'><i class=\'fa fa-pencil\'></i></a><a class=\'btn btn-xs btn-default\' title=\'Edit Delete\' href=\'javascript:Eliminar('.$fl_cupon.');\'><i class=\'fa fa-trash-o\'></i></a></td>"
        }';
		if($i<=($registros-1))
			echo ",";
		else
			echo "";
    }
    ?>
    ]
}