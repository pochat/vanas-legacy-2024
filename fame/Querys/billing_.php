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
  $fl_users = $_POST['fl_users'];
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
  # Query que obtiene los usuarios dependiedo de la intitucion
  # Adm Muestra teacher y students
  # Teacher Muestra los students
  $Query  = "SELECT  A.fe_periodo_inicial,A.mn_total,A.fg_pagado,A.fe_pago, 
                CASE WHEN B.fg_plan='A' THEN '".ObtenEtiqueta(1521)."' 
                ELSE '".ObtenEtiqueta(1520)."' END fg_plan ,";
  $Query .= "  DATE_FORMAT (A.fe_periodo_inicial, '%d') fe_periodo_d,  DATE_FORMAT(A.fe_periodo_inicial, '%m') fe_periodo_m,
					  DATE_FORMAT(A.fe_periodo_inicial,'%Y')fe_periodo_a ";
  //$Query .=" DATE_FORMAT(B.fe_fin, '%d') fe_fin_d,  DATE_FORMAT(B.fe_fin, '%m') fe_fin_m, DATE_FORMAT(B.fe_fin, '%Y') fe_fin_a ";
  
  $Query .=",A.fe_periodo_final,ds_descripcion ";
  $Query .="FROM ";

  $Query .= "k_admin_pagos A ";
  $Query .= "LEFT JOIN k_current_plan B ON B.fl_current_plan=A.fl_current_plan ";
  $Query .= "WHERE 1=1 AND B.fg_estatus='A' AND  B.fl_instituto=$fl_instituto AND A.fg_publicar='1'  ORDER BY A.fl_admin_pagos ASC ";

  // echo $Query;
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [
  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
     
      $fe_inicio_plan=$row['fe_periodo_inicial'];
      $fe_fin_plan=$row['fe_periodo_final'];
      $ds_descripcion_pago=$row['ds_descripcion'];
      
      
      #DAMOS FORMATO DIA,MES,AÑO
      $date=date_create($fe_inicio_plan);
      $fe_inicio_plan=date_format($date,'F j, Y');

      
      #DAMOS FORMATO DIA,MES,AÑO
      $date=date_create($fe_fin_plan);
      $fe_fin_plan=date_format($date,'F j, Y');
      
      
        # $nb_mes_inicial=ObtenNombreMes($row[6]);
        $fe_pago=$fe_inicio_plan."  -  ".$fe_fin_plan;
      
     
        
     
     // $fe_pago=$nb_mes_inicial.", ".$row[7] ;
      
      
      
      
      
      
      
    //$fe_pago = $row[0];
    $mn_total = "$ ".number_format($row[1],2);
    $fg_pagado = $row[2];
    $fe_pagado=$row[3];
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
      
      "fe_pago": "<div class=\'project-members text-left\' width=\'40%\' > '.$fe_pago.' </a> </div> ",
      "mn_total": " <div width=\'20%\' > '.$mn_total.' </div>  ",
         
      "status": "<span class=\'label label-'.$color.'\'>'.$status.'</span>",
      "information":"<div class=\'col col-sm-12 col-xs-12 col-md-12\'>'
      
        . '<div class=\'col col-sm-4 col-md-4 col-xs-4\'><strong>'.ObtenEtiqueta(1548).': </strong><br>'.$fe_pagado.'</div>'
        . '<div class=\'col col-sm-4 col-md-4 col-xs-4\'><strong>'.ObtenEtiqueta(987).':</strong><br>'.$plan.'</div>'
        . '<div class=\'col col-sm-4 col-md-4 col-xs-4\'><strong>No. Transfer:</strong><br>S-6374829292939</div>'
       
        . '</div>",
        "espacio":"&nbsp;"
      
    }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>
  ]
}








