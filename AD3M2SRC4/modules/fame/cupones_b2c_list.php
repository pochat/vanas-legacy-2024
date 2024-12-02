<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Consulta para el listado
  $Query  = "SELECT fl_cupon,nb_cupon, ds_code, ds_descuento, DATE_FORMAT(fe_start, '%M %D, %Y'), DATE_FORMAT(fe_end, '%M %D, %Y'), fg_activo,fg_plan_mensual,fg_plan_anual,fg_pago_unico  ";
  $Query .= "FROM c_cupones_b2c WHERE 1=1 ORDER BY fe_start";
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
        $fg_plan_mensual=$row['fg_plan_mensual'];
        $fg_plan_anual=$row['fg_plan_anual'];
        $fg_pago_unico=$row['fg_pago_unico'];
        
        
		if(!empty($fg_activo))
			$activo = "<span class='label label-success'>".ObtenEtiqueta(2270)."</span>";
		else
			$activo = "<span class='label label-danger'>".ObtenEtiqueta(2271)."</span>";
		
        
        if($fg_plan_mensual)
            $etq1=ObtenEtiqueta(2160)."<br>";
        else
            $etq1='';

        if($fg_plan_anual)
            $etq2=ObtenEtiqueta(2161)."<br>";
        else
            $etq2='';

        if($fg_pago_unico)
            $etq3=ObtenEtiqueta(2162);
        else
            $etq3='';
        
        
        #Recuperamos quines han utilizado este cupon y para que curso.
        $Query2="SELECT A.fl_usuario,A.fl_programa_sp,U.ds_nombres,U.ds_apaterno,P.nb_programa 
                FROM  k_uso_cupones_alumno A 
                JOIN c_usuario U  ON U.fl_usuario=A.fl_usuario
                JOIN  c_programa_sp P ON P.fl_programa_sp=A.fl_programa_sp
                WHERE A.fl_cupon=$fl_cupon ";
        $rs2=EjecutaQuery($Query2);
        $data_code="";
        for($m=1;$row2=RecuperaRegistro($rs2);$m++){
            $nb_alumno=str_texto($row2['ds_nombres'])." ".str_texto($row2['ds_apaterno']);
            $nb_programa=str_texto($row2['nb_programa']);      
        
        
            $data_code.="$nb_alumno :<i> $nb_programa</i><br/>";
            
            
        }
        

		echo '
        {            
			"name": "<a href=\'javascript:Envia(\"cupones_b2c_frm.php\",'.$fl_cupon.');\'><b>'.str_texto($nb_cupon).'</b></a>",
			"Dstart": "<td class=\'sorting_1\'><a href=\'javascript:Envia(\"cupones_b2c_frm.php\",'.$fl_cupon.');\'><strong>'.ObtenEtiqueta(2272).':</strong> '.$fe_start.' <br/><small class=\'text-muted\'><i>'.ObtenEtiqueta(2273).': '.$fe_end.'<i></i></i></small></a></td>",
			"cupon": "<td><a href=\'javascript:Envia(\"cupones_b2c_frm.php\",'.$fl_cupon.');\'>'.str_texto($ds_code).'<br></a></td>",
			"programs": "<td><a href=\'javascript:Envia(\"cupones_b2c_frm.php\",'.$fl_cupon.');\'>'.$etq1.' '.$etq2.' '.$etq3.'</a></td>",
			"descuento": "<td><a href=\'javascript:Envia(\"cupones_b2c_frm.php\",'.$fl_cupon.');\'>'.$ds_descuento.'</a></td>",
			"status": "<td><a href=\'javascript:Envia(\"cupones_b2c_frm.php\",'.$fl_cupon.');\'>'.$activo.'</a></td>",
            "data_code": "<td> '.$data_code.' </td>",
			"btns": "<td><a class=\'btn btn-xs btn-default\' title=\'Edit Delete\' href=\'javascript:Eliminar('.$fl_cupon.');\'><i class=\'fa fa-trash-o\'></i></a></td>"
        }';
        
        $etq1="";
        $etq2="";
        $etq3="";
        $data_code="";
        
        
		if($i<=($registros-1))
			echo ",";
		else
			echo "";
    }
    ?>
    ]
}