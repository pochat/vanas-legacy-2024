<?php   
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recibe Parametros
  $fl_users = !empty($_POST['fl_users'])?$_POST['fl_users']:NULL;
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
  # Query que obtiene los usuarios dependiedo de la intitucion
  # Adm Muestra teacher y students
  # Teacher Muestra los students
  $Query  = "SELECT  A.fe_periodo_inicial,A.mn_total,A.fg_pagado,A.fe_pago, 
                CASE WHEN B.fg_plan='A' THEN '".ObtenEtiqueta(1521)."' 
                ELSE '".ObtenEtiqueta(1520)."' END fg_plan ,";
  $Query .= "  DATE_FORMAT(A.fe_periodo_inicial, '%d') fe_periodo_d,  DATE_FORMAT(A.fe_periodo_inicial, '%m') fe_periodo_m,
					  DATE_FORMAT(A.fe_periodo_inicial,'%Y')fe_periodo_a ";
  //$Query .=" DATE_FORMAT(B.fe_fin, '%d') fe_fin_d,  DATE_FORMAT(B.fe_fin, '%m') fe_fin_m, DATE_FORMAT(B.fe_fin, '%Y') fe_fin_a ";
  
  $Query .=",A.fe_periodo_final,ds_descripcion,A.fl_admin_pagos,A.fg_motivo_pago,A.fl_pago_stripe,A.mn_costo_por_licencia,A.mn_descuento,B.fl_princing,B.fg_plan ";
  $Query .="FROM ";

  $Query .= "k_admin_pagos A ";
  $Query .= "LEFT JOIN k_current_plan B ON B.fl_current_plan=A.fl_current_plan ";
  $Query .= "WHERE 1=1 AND B.fg_estatus='A' AND  B.fl_instituto=$fl_instituto AND A.fg_publicar='1'  ORDER BY A.fl_admin_pagos DESC ";

  // echo $Query;
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [
  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
      $fl_admin_pagos=$row['fl_admin_pagos'];
      $fe_inicio_plan=$row['fe_periodo_inicial'];
      $fe_fin_plan=$row['fe_periodo_final'];
      $ds_descripcion_pago=$row['ds_descripcion'];
      $fg_motivo_pago=$row['fg_motivo_pago'];
      $fl_pago_stripe=$row['fl_pago_stripe'];
      $mn_costo_por_licencia=$row['mn_costo_por_licencia'];
      $mn_descuento=$row['mn_descuento'];
      $fl_princing=$row['fl_princing'];
	  $fg_plan=$row['fg_plan'];
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
      
      
     
      $Query2="SELECT mn_anual,mn_mensual FROM c_princing WHERE fl_princing=$fl_princing ";
	  $row2=RecuperaValor($Query2);
	  if($fg_plan=='M')
	  $mn_costo_por_licencia=$row2[1];
	  else
	  $mn_costo_por_licencia=$row2[0];
	 
      
      
      
      
      if($fg_motivo_pago==PAGO_ADD_LICENCES){
      
         $fe_pago=ObtenEtiqueta(1719);
      }else{
      
      
        # $nb_mes_inicial=ObtenNombreMes($row[6]);
        $fe_pago=$fe_inicio_plan."  -  ".$fe_fin_plan;
      }
     
        
     
     // $fe_pago=$nb_mes_inicial.", ".$row[7] ;
      
      
      
      
      
      
      
    //$fe_pago = $row[0];
    $mn_total = "$ ".number_format($row[1],2);
    $fg_pagado = $row[2];
    $fe_pagado=ObtenFechaFormatoDiaMesAnioHora($row[3],true);
    $plan=$row[4];
   
      
     
   
      if($fg_pagado==1){
          
          $color = "success";
          $status= ObtenEtiqueta(1545);
      
      }else{
          $status=ObtenEtiqueta(1546);
          $color = "danger";
      
      }
          
      
      
  
   
    
   
    
    
    
    /** ESTE PROCESO PARA AYUDAR A LA BUSQUEDA AVANZADA **/    
    echo '
    {
      
      "fe_pago": "<div class=\'project-members text-left\' width=\'40%\' > '.$fe_pagado.' </a> </div> ",
      "mn_total": " <div width=\'20%\' > '.$mn_total.' </div>  ",
         
      "status": "<div class=\'text-center\'><span class=\'label label-'.$color.'\'>'.$status.'</span></div>",
      "information":"<div class=\'col col-sm-12 col-xs-12 col-md-12\'>  '
        . '<div class=\'col col-sm-3 col-md-3 col-xs-3\'><strong>'.ObtenEtiqueta(1547).': </strong><br>'.$id_pago.'</div>'  
       
        . '<div class=\'col col-sm-3 col-md-3 col-xs-3\'><strong>'.ObtenEtiqueta(987).':</strong><br>'.$plan.'</div>'
        . '<div class=\'col col-sm-3 col-md-3 col-xs-3\'><strong>'.ObtenEtiqueta(1704).':</strong><br>'.$ds_descripcion_pago.'</div>'
		. '<div class=\'col col-sm-3 col-md-3 col-xs-3\'><strong>'.ObtenEtiqueta(1724).':</strong><br>$'.$mn_costo_por_licencia.'  ('.$mn_descuento.'% '.ObtenEtiqueta(1751).')</div>'
		
       
        . '</div>",
        "espacio":"<a href=\'site/../../AD3M2SRC4/modules/reports/invoice_rpt.php?c='.$fl_admin_pagos.'&u='.$fl_usuario.'&i='.$fl_instituto.'\' ><i class=\'fa fa-file-pdf-o\' aria-hidden=\'true\'></i></a>  "
      
    }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>
  ]
}








