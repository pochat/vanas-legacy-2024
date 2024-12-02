<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
  
  # Consulta para el listado
  $Query  = "SELECT fl_leccion, nb_programa '".ObtenEtiqueta(380)."', ds_duracion '".ObtenEtiqueta(380)." ".ObtenEtiqueta(396)."', no_grado '".ObtenEtiqueta(375)."|right', ";
  $Query .= "no_semana '".ObtenEtiqueta(390)."|right', ds_titulo '".ObtenEtiqueta(385)."', ";
  $Query .= "CASE WHEN ds_as_ruta IS NULL THEN 'No' WHEN ds_as_ruta='' THEN 'No' ELSE 'Yes' END 'Video Brief', ";
  $Query .= "ds_vl_ruta '".ObtenEtiqueta(395)."', ds_vl_duracion '".ObtenEtiqueta(396)."', ";
  $concat = array(ConsultaFechaBD('fe_vl_alta', FMT_FECHA), "' '", ConsultaFechaBD('fe_vl_alta', FMT_HORAMIN));
  $Query .= "(".ConcatenaBD($concat).") '".ObtenEtiqueta(397)."', no_valor_rubric, ";
  $Query .=" CASE 
				WHEN (SELECT COUNT(*) FROM k_criterio_programa WHERE fl_programa = a.fl_leccion) = 0 THEN 'No' 
				ELSE 'Yes' END 'rubric', ";
  $Query.="(SELECT SUM(kcp.no_valor_rubric) FROM c_leccion kcp WHERE kcp.fl_programa = a.fl_programa AND kcp.no_grado=a.no_grado) AS sum_tot_rubric ";
  
  $Query .= "FROM c_leccion a, c_programa b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa AND b.fg_archive='0'  ";
  $Query .= "ORDER BY no_orden, no_grado, no_semana ";
  
  
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
   
         $nb_programa=str_texto($row[1]);
		 $ds_duracion=str_texto($row[2]);
		 $no_grado=str_texto($row[3]);
		 $no_semana=str_texto($row[4]);
         $ds_titulo=str_texto($row[5]);
		 $ds_video=str_texto($row[6]);
		 $ds_ruta_video=str_texto($row[7]);
		 $ds_duracion_video=str_texto($row[8]);
		 $fe_alta=str_texto($row[9]);
		 $no_valor_rubric=($row[10]);
		 $rubric=str_texto($row[11]);
         $sum_rubric=$row[12];
	 
        if($rubric == ObtenEtiqueta(17)) {
			$color6 = "danger";
			$etq6=ObtenEtiqueta(17);
		  }else{
			$color6 = "success";
			$etq6 = ObtenEtiqueta(16);
		  }
	 
	 
		 # RUBRIC
      
      # Si la sumatoria de los valores del rubric es menor a 100%, entonces la etiqueta es amarilla
      if($sum_rubric < 100) {
        $color5 = "warning";
        $etq5 = $no_valor_rubric." %";
      }elseif($sum_rubric == 100){ # Si la sumatoria de los valores del rubric es igual a 100%, entonces la etiqueta es verde
        $color5 = "success";
        $etq5 = $no_valor_rubric." %";
      }
      
      # Si la sumatoria de los valores del rubric del curso excede el 100%, entonces la etiqueta es color rojo
      if($sum_rubric > 100){
        $color5 = "danger";
        $etq5 = $no_valor_rubric." %";
      }
      if(empty($no_valor_rubric)){
        $color5 = "";
        $etq5 = "";
      }
	 
	 
	 
      echo '
        {
          
          "course": "<a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'>'.$nb_programa.'</a>",
          "duration": "<td><a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'>'.$ds_duracion.'</a></td>",           
          "term": "<a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'><td>'.$no_grado.'</a></td>",          
          "semana": "<td><a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'>'.$no_semana.'</a></td>", 
          "titulo": "<td><a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'>'.$ds_titulo.'</a></td>",
          "video": "<td><a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'>'.$ds_video.'</a></td>",
          "ruta_video": "<td><a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'>'.$ds_ruta_video.'</a></td>",          
          "duracion": "<td><a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'>'.$ds_duracion_video.'</a></td>",          
          "fecha": "<td><a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'>'.$fe_alta.'</a></td>",          
          "rubric": "<td><a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'><span class=\'label label-'.$color6.'\'>'.$etq6.'</span></a></td>",     
          "valor_rubric": "<td><a href=\'javascript:Envia(\"media_frm.php\",'.$row[0].');\'><span class=\'label label-'.$color5.'\'>'.$etq5.'</span></a></td>",
          "delete":"<a href=\'javascript:Borra(\"media_del.php\",'.$row[0].');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a>"	  
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]
}