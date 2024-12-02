<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
  $fl_instituto= !empty($_POST['extra_filters']['fl_instituto'])?$_POST['extra_filters']['fl_instituto']:NULL;

 /* # Consulta para el listado
  $Query  = "SELECT A.fl_current_plan,A.fe_periodo_inicial,A.fe_periodo_final,A.mn_total,A.fg_pagado,A.ds_descripcion ";
  $Query .= "FROM k_current_plan K ";
  $Query .="JOIN k_admin_pagos A  ON K.fl_current_plan=A.fl_current_plan ";
  $Query .= "WHERE K.fl_instituto=$fl_instituto AND  A.fl_current_plan=1 AND fg_pagado='1' ORDER BY A.fl_admin_pagos ASC  
   ";
 */
  $Query="SELECT fl_usuario_sp FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fl_usuario=!empty($row[0])?$row[0]:NULL; 

  $Query  = "SELECT  A.fe_periodo_inicial,A.mn_total,A.fg_pagado,A.fe_pago, CASE WHEN B.fg_plan='A' THEN '".ObtenEtiqueta(1521)."' ELSE '".ObtenEtiqueta(1520)."' END fg_plan , DATE_FORMAT(A.fe_periodo_inicial, '%d') fe_periodo_d,  DATE_FORMAT(A.fe_periodo_inicial, '%m') fe_periodo_m, DATE_FORMAT(A.fe_periodo_inicial,'%Y')fe_periodo_a";
  //$Query .=" DATE_FORMAT(B.fe_fin, '%d') fe_fin_d,  DATE_FORMAT(B.fe_fin, '%m') fe_fin_m, DATE_FORMAT(B.fe_fin, '%Y') fe_fin_a ";
  $Query .=", A.fe_periodo_final,ds_descripcion,A.fl_admin_pagos,A.fg_motivo_pago,A.fl_pago_stripe,A.mn_costo_por_licencia FROM ";
  $Query .= "k_admin_pagos A ";
  $Query .= "LEFT JOIN k_current_plan B ON B.fl_current_plan=A.fl_current_plan ";
  $Query .= "WHERE 1=1 AND B.fg_estatus='A' AND  B.fl_instituto=$fl_instituto AND A.fg_publicar='1'  ORDER BY A.fl_admin_pagos DESC ";
 
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
   
        $fl_admin_pagos=$row['fl_admin_pagos'];
        $fe_inicio_plan=$row['fe_periodo_inicial'];
        $fe_fin_plan=$row['fe_periodo_final'];
        $ds_descripcion_pago=$row['ds_descripcion'];
        $fg_motivo_pago=$row['fg_motivo_pago'];
        $fl_pago_stripe=$row['fl_pago_stripe'];
        $mn_costo_por_licencia=$row['mn_costo_por_licencia'];

        #Recuperamos el id de pago de strippe
        $Query1="SELECT id_pago_stripe FROM k_pago_stripe 
			  WHERE fl_pago=$fl_pago_stripe ";
        $row1=RecuperaValor($Query1);
        $id_pago=str_texto($row1[0]);
        
        #DAMOS FORMATO DIA,MES,AÑO
        $date=date_create($fe_inicio_plan);
        $fe_inicio_plan=date_format($date,'F j, Y');

        #DAMOS FORMATO DIA,MES,AÑO
        $date=date_create($fe_fin_plan);
        $fe_fin_plan=date_format($date,'F j, Y');
        
        if($fg_motivo_pago==2){
            $fe_pago=ObtenEtiqueta(1719);
        }else{ 
            # $nb_mes_inicial=ObtenNombreMes($row[6]);
            $fe_pago=$fe_inicio_plan."  -  ".$fe_fin_plan;
        }
        
        //$fe_pago = $row[0];
        $mn_total = "$ ".number_format($row[1],2);
        $fg_pagado = $row[2];
        //$fe_pagado=ObtenFechaFormatoDiaMesAnioHora($row[3],true);
        
        #DAMOS FORMATO DIA,MES,AÑO
        $date=date_create($row[3]);
        $fe_pagado=date_format($date,'F j, Y');
        $plan=$row[4];
           
        if($fg_pagado==1){
          $color = "success";
          $status= ObtenEtiqueta(1545);
        }else{
          $status=ObtenEtiqueta(1546);
          $color = "danger";
        }
                    
      echo '
        {
           
            "name": "'.$fe_pagado.' ",
            "duration": "<td class=\"text-right\">'.$mn_total.'</td>",
            "estatus": "<td class=\"text-right\"><span class=\"label label-'.$color.'\">'.$status.'</span>  </td>",
            "espacio": "<td class=\"text-right\"> <a href=\"../reports/invoice_rpt.php?c='.$fl_admin_pagos.'&u='.$fl_usuario.'&i='.$fl_instituto.'\" ><i class=\"fa fa-file-pdf-o\" aria-hidden=\"true\"></i></a> </td>",
            "idp":"<strong>'.ObtenEtiqueta(1547).':</strong><br>'.$id_pago.'",
            "payment":"<strong>'.ObtenEtiqueta(987).':</strong><br>'.$plan.'",
             "descripcion":"<strong>'.ObtenEtiqueta(1704).':</strong><br>'.$ds_descripcion_pago.'",
             "costo":"<strong>'.ObtenEtiqueta(1724).':</strong><br>$'.number_format($mn_costo_por_licencia,2).'"
            
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]
}
