<?php
	# Librerias
	require '../../lib/general.inc.php';
	# Recibe parametros
	$clave = RecibeParametroNumerico("clave");
	$fe_startt = RecibeParametroFecha("fe_start");
	$fe_endd = RecibeParametroFecha("fe_end");
  
	# Consulta para el listado
	$Query  = "SELECT fl_programa, CONCAT(nb_programa, ' - ', ds_duracion) FROM c_programa a ";
	$Query .= "WHERE fg_archive='0' ";	
	$rs = EjecutaQuery($Query);
	$registros = CuentaRegistros($rs);  
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++){
		$fl_programa = $row[0];
		$nb_programa = $row[1];
		$check = "";
		
		# Verifiamos cuales son de este cupon
		$row0 = RecuperaValor("SELECT COUNT(*) FROM k_cupones_course WHERE fl_cupon=".$clave." AND fl_programa=".$fl_programa);
		if(!empty($row0[0]))
			$check = "checked";
		$g  = "SELECT a.fl_cupon, DATE_FORMAT(fe_start, '%M %D, %Y'), DATE_FORMAT(fe_end, '%M %D, %Y'), nb_cupon ";
		$g .= "FROM c_cupones a, k_cupones_course  b ";
		$g .= "WHERE a.fl_cupon=b.fl_cupon AND DATE_FORMAT(fe_end, '%d-%m-%Y') ";
		if(!empty($fe_startt) && !empty($fe_endd))
			$g .= "BETWEEN '".$fe_startt."' AND '".$fe_endd."' ";
		$g .= "AND a.fl_cupon<>".$clave." AND a.fl_cupon<>0 AND b.fl_programa=".$fl_programa;
		$row2 = RecuperaValor($g);
		if(!empty($row2[0])){
			$disabled = "disabled";
			$txt_color = "txt-color-blue";
			$tool = "rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(2303)."' data-html='true'";
			$ai = "<a href='javascript:cupon(".$row2[0].");'>";
			$ae = "</a>";
			$nb_cupo = str_texto($row2[3]);
			$fe_start = $row2[1];
			$fe_end = $row2[2];			
		}
		else{
			$disabled = "";
			$txt_color = "";
			$tool = "";
			$ai = "";
			$ae = "";
			$row3 = RecuperaValor("SELECT nb_cupon, DATE_FORMAT(fe_start, '%M %D, %Y'), DATE_FORMAT(fe_end, '%M %D, %Y') FROM k_cupones_course a, c_cupones b WHERE a.fl_cupon=b.fl_cupon AND a.fl_programa=".$fl_programa);
			$nb_cupo = str_texto(!empty($row3[0])?$row3[0]:NULL);
			$fe_start = !empty($row3[1])?$row3[1]:NULL;
			$fe_end = !empty($row3[2])?$row3[2]:NULL;
		}
		$date = '';
		if(!empty($fe_start) && !empty($fe_end))
			$date = $ai.'<strong>'.ObtenEtiqueta(2272).':</strong> '.$fe_start.' <br/><small class=\'text-muted\'><i>'.ObtenEtiqueta(2273).': '.$fe_end.'<i></i></i></small>'.$ae;		

		echo '
        {            
			"checkbox": "<div class=\'checkbox no-padding '.$txt_color.'\' '.$tool.'>'.$ai.'<label><input class=\'checkbox\' name=\'ch_'.$i.'\' id=\'ch_'.$i.'\' value=\''.$fl_programa.'\' type=\'checkbox\' '.$check.' '.$disabled.'/><span></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_programas\' name=\'tot_programas\' /></label>'.$ae.'</div>",
			"nb_program": "<td class=\'sorting_1\'>'.$ai.'<p class=\''.$txt_color.'\' '.$tool.'>'.$nb_programa.'</p>'.$ae.'</td>",
			"date": "<td class=\'sorting_1\'>'.$date.'</td>",
			"nb_cupon": "<td class=\''.$txt_color.'\'>'.$ai.'<p class=\''.$txt_color.'\' '.$tool.'>'.$nb_cupo.'</p>'.$ae.'</td>"
        }';
		if($i<=($registros-1))
			echo ",";
		else
			echo "";
    }
    ?>
    ]
}