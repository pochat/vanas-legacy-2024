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
 
  
  $fl_instituto=ObtenInstituto($fl_usuario);
  $no_usuarios_adicionales=RecibeParametroNumerico('no_usuarios');
  $no_usuario_actual=RecibeParametroNumerico('no_usuarios_actuales');
  $fg_opcion_pago=RecibeParametroNumerico('fg_plan');
  $fg_tiene_plan=RecibeParametroNumerico('fg_tiene_plan');
  
  
  if(empty($fg_tiene_plan)){#Para los que estan en modo free, y queiren adqueirir una licencias.
  
      $usuarios_free_actuales=ObtenNumeroUserInst($fl_instituto);
	  $no_total_licencias = $usuarios_free_actuales + $no_usuarios_adicionales ;
	  
  }else{
      $no_licencias_totales_actuales=ObtenNumLicencias($fl_instituto);
      $no_total_licencias = $no_licencias_totales_actuales + $no_usuarios_adicionales ;
  }
  
  

  
  #identificamos en que rango se encuentra,PARA SABER  NUEVO PLAN y nuevas tarifas.
  $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
  $rs = EjecutaQuery($Query);
  for($i=1;$row=RecuperaRegistro($rs);$i++){
      
      $mn_rango_ini= $row['no_ini'];
      $mn_rango_fin= $row['no_fin'];
      
      if(( $no_total_licencias >=$mn_rango_ini)&&($no_total_licencias<=$mn_rango_fin) ){
          
          $fl_nuevo_princing=$row['fl_princing'];
      }

  }

  #Recuperamos costos segun el plan obtenido del nuevo rango de licencias.
  $Query="SELECT mn_mensual,mn_anual FROM c_princing WHERE fl_princing=$fl_nuevo_princing ";
  $row=RecuperaValor($Query);
  $mn_costo_mensual=$row[0];
  $mn_costo_anual=$row[1];
  
  
  
  if($fg_opcion_pago==1)
      $mn_costo_total= $mn_costo_mensual /30 ;   //$mn_costo_total= ($no_total_licencias * $mn_costo_mensual) /30 ; 
		
  
  if($fg_opcion_pago==2)
        $mn_costo_total=$mn_costo_anual / 30 ; //$mn_costo_total= $no_total_licencias * $mn_costo_anual / 12;
  
  
  $mn_total_pagar="USD "." $".number_format($mn_costo_total,2);
  
  
  echo"
      $mn_total_pagar
  
  ";
  
  
  
  
  
  
                                     
?>

