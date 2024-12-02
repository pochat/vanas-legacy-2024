<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
  # Consulta para el listado
  $Query  = "SELECT a.fl_programa_sp, a.nb_programa '".ObtenEtiqueta(360)."', a.ds_duracion '".ObtenEtiqueta(361)."', ";
  $Query .= "a.ds_tipo '".ObtenEtiqueta(362)."', a.no_grados '".ObtenEtiqueta(365)."|right', ";
  $Query .= "a.no_orden '".ObtenEtiqueta(48)."|right', a.no_creditos, ";
  $Query .= "CASE WHEN a.fg_fulltime='1' THEN 'Full Time' ELSE 'Part Time' END schedule, ";
  $Query .= "(SELECT SUM(b.no_valor_quiz) FROM c_leccion_sp b WHERE b.fl_programa_sp = a.fl_programa_sp ) as valor_tot_quiz, ";
  $Query .= "(SELECT c.no_workload FROM k_programa_detalle_sp c WHERE c.fl_programa_sp = a.fl_programa_sp) AS workload, ";
  $Query .= "(SELECT COUNT(1) FROM c_leccion_sp z WHERE z.fl_programa_sp = a.fl_programa_sp) AS cont_lecciones, ";
  $Query .= "CASE WHEN (SELECT COUNT(1) FROM c_leccion_sp z WHERE z.fl_programa_sp = a.fl_programa_sp) = 0 
	THEN '".ObtenEtiqueta(1354)."' 
	ELSE CONCAT((SELECT COUNT(1) FROM c_leccion_sp z WHERE z.fl_programa_sp = a.fl_programa_sp), ' ".ObtenEtiqueta(1355)."')
	END 'cont_lecciones_2',fg_publico,fl_instituto ";
  $Query .= "FROM c_programa_sp a ";
  $Query .= "ORDER BY a.no_orden";
  // echo $Query;
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
        $fg_publico=$row['fg_publico'];
        $fl_instituto_curso=$row['fl_instituto'];
        $Query  = "SELECT no_horas, no_semanas ";
        $Query .= "FROM k_programa_detalle_sp ";
        $Query .= "WHERE fl_programa_sp = $row[0] ";
        $row1 = RecuperaValor($Query);
              
        $row1[0]==1 ?   $hora = ObtenEtiqueta(1232) : $hora = ObtenEtiqueta(1233);
        $row1[1]==1 ? $sesion = ObtenEtiqueta(1230) : $sesion = ObtenEtiqueta(1231);
        
        #Recupermaos avatar del instituto.
        if(!empty($fl_instituto_curso)){

            $Query="SELECT ds_foto,ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto_curso ";
            $rof=RecuperaValor($Query);
            $nb_logo_instituto=$rof['ds_foto'];
            $ds_instituto=$rof['ds_instituto'];

            if((empty($nb_logo_instituto))||($nb_logo_instituto=='null')){               
                $logo_instituto=PATH_SELF_UPLOADS."/../../img/Partner_School_Logo.jpg";
            }else{
                $logo_instituto=PATH_SELF_UPLOADS."/".$fl_instituto_curso."/".$nb_logo_instituto;
            }
            $img_instituto="<a href='javascript:void(0)' rel='tooltip' data-placement='top' data-html='true' data-original-title='$ds_instituto'><img src='$logo_instituto' height='25px' ></a>";
            
        }else{
            $logo_instituto="";
            $img_instituto="";
        }



        if($row[8] < 100) {
          $color = "warning";
          $etq = $row[8]." %";
        }else{
          $color = "success";
          $etq = $row[8]." %";
        }
        
        if(empty($row[8])){
          $color = "";
          $etq = "";
        }        
        
        $Query_t = "SELECT ds_titulo, ds_vl_duracion, ds_tiempo_tarea, no_valor_quiz FROM c_leccion_sp WHERE fl_programa_sp = $row[0]";
        $rs_t = EjecutaQuery($Query_t);
        $arma = "";
        for($i_t=1;$row_t=RecuperaRegistro($rs_t);$i_t++) {
          $arma .= "<tr><td width='5%'></td><td width='5%'>$i_t</td><td width='50%'>$row_t[0]</td><td width='10%'>$row_t[1]</td><td width='15%'>$row_t[2]</td><td width='15%'>$row_t[3] %</td width='5%'><td></td></tr>";
        }
        
        if(empty($row[10])){
          $color_10 = "danger";
          $etq_10 = $row[11];
        }else{
          $color_10 = "success";
          $etq_10 = $row[11];
        }
        
		if($fg_publico==1){
		    $publish="<span class='label label-success'>Yes</span> ";
		}else{
		    $publish="<span class='label label-danger'>No</span>";
		}
    
      echo '
        {
          "checkbox": "<!--<div class=\'checkbox \'><label><input class=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$row[0].'\' type=\'checkbox\' /><span></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_registros\' /> </label></div>-->",
          
          "name": "<a href=\'javascript:Envia(\"clibrary_frm.php\",'.(!empty($row[0])?$row[0]:NULL).');\'>'.str_texto(!empty($row[1])?$row[1]:NULL).'<br><small class=\'text-muted\'><i>'.str_texto(!empty($row[3])?$row[3]:NULL).'&nbsp;'.str_texto(!empty($row1[2])?$row1[2]:NULL).'</i></small></a><br> '.$img_instituto.'",
          "public":" '.$publish.'",
          "duration": "<td><a href=\'javascript:Envia(\"clibrary_frm.php\",'.$row[0].');\'>'.$row1[0].' '.$hora.'<br><small class=\'text-muted\'><i>'.str_texto($row1[1]).' '.$sesion.'</i></small></a></td>",           

          "cont_lecciones": "<a href=\'javascript:Envia(\"clibrary_frm.php\",'.$row[0].');\'><td><span class=\'label label-'.$color_10.'\'>'.$etq_10.'</span</a></td>",          
          
          "schedule": "<a href=\'javascript:Envia(\"clibrary_frm.php\",'.$row[0].');\'><td><span class=\'label label-'.$color.'\'>'.$etq.'</span</a></td>",          
          
          "level": "<td><a href=\'javascript:Envia(\"clibrary_frm.php\",'.$row[0].');\'>'.str_texto($row[6]).'</a></td>", 
          
          "workload": "<td><a href=\'javascript:Envia(\"clibrary_frm.php\",'.$row[0].');\'>'.($row[9]).'</a></td>", 
          
          "gender": "'.$arma.'", 
          
          "action": "<a href=\'javascript:Borra(\"clibrary_del.php\",'.$row[0].');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a>"
 
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]
}
