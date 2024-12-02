<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
  # Consulta para el listado
  $Query  = "SELECT fl_instituto,ds_instituto,P.ds_pais,I.fg_tiene_plan, I.fg_princing_default,I.cl_tipo_instituto,I.fl_instituto_rector ";
  $Query .= "FROM c_instituto I ";
  $Query .= "JOIN c_pais P ON (P.fl_pais=I.fl_pais) WHERE 1=1 ";

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
   
          
			$fg_tiene_plan=$row['fg_tiene_plan'];
			$fl_instituto=$row['fl_instituto'];
            $fg_princing_default=$row['fg_princing_default'];
            $cl_tipo_instituto=$row['cl_tipo_instituto'];
            $fl_instituto_rector=$row['fl_instituto_rector'];

            if($cl_tipo_instituto==2){
                $nb_rector='<small class=\'text-muted\'><i>'.ObtenEtiqueta(2524).'</i></small>';
            }else{
                $nb_rector="";
            }


			   switch($fg_tiene_plan){
			   
					case "0": 
						 $color_label = "danger";
						 $status="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Trial&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
						 break;
					 case "1": 
						 $color_label="success";
						 $status="Partner School";
						 break;
			   }
			   
			   
			   if($fg_princing_default=="1"){
					   $color_label2="success";
					   $status2="Yes";
				   }else{
					   $color_label2 = "danger";
					   $status2="No"; 
				   
				   }
       
			   
               #Recuperamos el princing del Instituto.
               
               $Query2="SELECT fl_princing, no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia  
                        FROM c_princing 
                        WHERE fl_instituto=$fl_instituto 
                        ORDER BY fl_princing ASC ";
               $rs2 = EjecutaQuery($Query2);
               $tot_registros = CuentaRegistros($rs2);
               
               $arma ="<tr>";
               $arma.="<td width='5%'></td>";
               $arma.="<td width='15%' class='text-center'>".ObtenEtiqueta(1501)."<p><em style='color:#888686;'>".ObtenEtiqueta(1504)."</em> </p></td>";
               $arma.="<td width='15%' class='text-center'>".ObtenEtiqueta(1749)."</td>";
               $arma.="<td width='15%' class='text-center'>".ObtenEtiqueta(1551)."</td>";
               $arma.="<td width='20%' class='text-center'>Monthly - Flexible payments<p><em style='color:#888686;'>".ObtenEtiqueta(1505)."</td>";
               $arma.="<td width='20%' class='text-center'>".ObtenEtiqueta(1503)."<p><em style='color:#888686;'>".ObtenEtiqueta(1506)."</em></p></td>";
               $arma.="<td></td>";
               $arma.="</tr>";
               
               for($m=1;$row2=RecuperaRegistro($rs2);$m++){
                   
                   $mn_rango_ini= $row2['no_ini'];
                   $mn_rango_fin= $row2['no_fin'];
                   $mn_descuento_licencia= number_format($row2['mn_descuento_licencia']);
                   $mn_mensual= $row2['mn_mensual'];
                   $mn_descuento_mensual= number_format($row2['ds_descuento_mensual']);
                   $mn_anual= $row2['mn_anual'];
                   
                   $arma.="<tr>";
                   $arma.="<td></td>";
                   $arma.="<td class='text-center'>$mn_rango_ini - $mn_rango_fin </td>";
                   $arma.="<td class='text-center'>$mn_descuento_licencia% </td>";
                   $arma.="<td class='text-center'>$mn_descuento_mensual%</td>";
                   $arma.="<td class='text-center'>$ $mn_mensual</td>";
                   $arma.="<td class='text-center'>$ $mn_anual</td>";
                   $arma.="<td></td>";
                   $arma.="</tr>";
                   
                   
                   
               }	
               
               
               
               #Recuperamos datos del isntituto Rector.
               if($fl_instituto_rector){
                   $Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto_rector ";
                   $ro=RecuperaValor($Query);
                   $nb_instituto_rector='<small class=\'text-muted\' ><i>'.ObtenEtiqueta(2524).': '.$ro['ds_instituto'].'</i></small>';
               }else{
                   $nb_instituto_rector=""; 
               } 
               


		  
      echo '
        {
           "name": "<a href=\'javascript:Envia(\"pricing_frm.php\",'.$row[0].');\'>'.str_texto($row[1]).'</a> <br>'.$nb_rector.' '.$nb_instituto_rector.'  ",
           "pais": "<a href=\'javascript:Envia(\"pricing_frm.php\",'.$row[0].');\'>'.str_texto($row[2]).'</a>",
		    "status": "<span class=\"label label-'.$color_label.'\">'.$status.'</span>",
			"princing_default": "<span class=\"label label-'.$color_label2.'\">'.$status2.'</span>",
            "princing": "'.$arma.'", 
            "action": "<a href=\'javascript:Borra(\"pricing_del.php\",'.$row[0].');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a>"
 
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
