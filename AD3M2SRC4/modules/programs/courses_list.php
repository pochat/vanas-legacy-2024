<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
  # Consulta para el listado
  $Query  = "SELECT fl_programa, nb_programa '".ObtenEtiqueta(360)."', ds_duracion '".ObtenEtiqueta(361)."', ";
  $Query .= "ds_tipo '".ObtenEtiqueta(362)."', no_grados '".ObtenEtiqueta(365)."|right', ";
  $Query .= "no_orden '".ETQ_ORDEN."|right',a.fg_total_programa,a.fg_taxes    ,a.fg_tax_rate,
             case when a.fg_fulltime=1 then 'Full Time'
                  else 'Part Time' end sschedule, ";
  $Query .=" CASE 
				WHEN (SELECT COUNT(*) FROM k_criterio_curso WHERE fl_programa = a.fl_programa) = 0 THEN 'No' 
				ELSE 'Yes' END 'rubric' ";
  $Query .= "FROM c_programa a WHERE 1=1 and  fg_archive='0' ";
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
   
        $rubric=$row['rubric'];
        
        
        $Query  = "SELECT no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, ";
        $Query .= "mn_app_fee, mn_tuition, mn_costs, ds_costs, no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes, ";
        $Query .= "no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes, ";
        $Query .= "no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes, no_horas_week ";
        $Query .= "FROM k_programa_costos ";
        $Query .= "WHERE fl_programa = $row[0] ";
        $row1 = RecuperaValor($Query);
      
        
      switch($row[6]) {
        case "0": 
            $color1 = "danger";
            $etq=ObtenEtiqueta(17);
        break;
        case "1": 
            $color1 ="success";
            $etq=ObtenEtiqueta(16);
        break;
      } 
      
      
      switch($row[7]) {
          case "0": 
              $color2 = "danger";
              $etq2=ObtenEtiqueta(17);
              break;
          case "1": 
              $color2 ="success";
              $etq2=ObtenEtiqueta(16);
              break;
      } 
      
      
      switch($row[8]) {
          case "0": 
              $color3 = "danger";
              $etq3=ObtenEtiqueta(17);
              break;
          case "1": 
              $color3 ="success";
              $etq3=ObtenEtiqueta(16);
              break;
      }  
           
      
      if($rubric == ObtenEtiqueta(17)) {
          $color6 = "danger";
          $etq6=ObtenEtiqueta(17);
      }else{
          $color6 = "success";
          $etq6 = ObtenEtiqueta(16);
      }
      
      
      
      
      
      
      
      echo '
        {
           "checkbox": "<!--<div class=\'checkbox \'><label><input class=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$row[0].'\' type=\'checkbox\' /><span></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_registros\' /> </label></div>-->",
            "name": "<a href=\'javascript:Envia(\"courses_frm.php\",'.$row[0].');\'>'.str_texto($row[1]).'<br><small class=\'text-muted\'><i>'.str_texto($row[3]).'&nbsp;'.str_texto($row1[2]).'</i></small></a>",
            "duration": "<td><a href=\'javascript:Envia(\"courses_frm.php\",'.$row[0].');\'>'.str_texto($row[2]).'<br><small class=\'text-muted\'><i>'.str_texto($row1[1]).' '.ObtenEtiqueta(390).'s &nbsp;'.str_texto($row1[0]).' '.ObtenEtiqueta(727).'</i></small></a></td>",           
            "level": "<td><a href=\'javascript:Envia(\"courses_frm.php\",'.$row[0].');\'>'.str_texto($row[4]).'</a></td>", 
           "display": "<a href=\'javascript:Envia(\"courses_frm.php\",'.$row[0].');\'><td>'.str_texto($row[5]).'</a></td>",
           "schedule": "<a href=\'javascript:Envia(\"courses_frm.php\",'.$row[0].');\'><td>'.str_texto($row[9]).'</a><br><small class=\'text-muted\'><i>'.$row1[30].' hours per week</i></small></td>",
            "request": "<td><a href=\'javascript:Envia(\"courses_frm.php\",'.$row[0].');\'><span class=\'label label-'.$color1.'\'>'.$etq.'</span>   </a></td>", 
            "taxable": "<td><a href=\'javascript:Envia(\"courses_frm.php\",'.$row[0].');\'><span class=\'label label-'.$color2.'\'>'.$etq2.'</span>   </a></td>",  
            "taxable_canada": "<td><a href=\'javascript:Envia(\"courses_frm.php\",'.$row[0].');\'><span class=\'label label-'.$color3.'\'>'.$etq3.'</span>   </a></td>",            
            "rubric": "<td><a href=\'javascript:Envia(\"courses_frm.php\",'.$row[0].');\'><span class=\'label label-'.$color6.'\'>'.$etq6.'</span></a></td>",   
            "action": "<a href=\'javascript:Borra(\"courses_del.php\",'.$row[0].');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a>"
 
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
