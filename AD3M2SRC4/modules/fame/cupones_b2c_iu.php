<?php  
  
	# Libreria de funciones
	require '../../lib/general.inc.php';  

	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
	$clave = RecibeParametroNumerico('clave');
    
	# Determina si es alta o modificacion
	if(!empty($clave))
		$permiso = PERMISO_MODIFICACION;
	else
		$permiso = PERMISO_ALTA;
  
	# Verifica que el usuario tenga permiso de usar esta funcion
	if(!ValidaPermiso(FUNC_CUPON, $permiso)) {
		MuestraPaginaError(ERR_SIN_PERMISO);
		exit;
	}

	# Recibe parametros
	$fg_error = 0;
	$nb_cupon = RecibeParametroHTML('nb_cupon');
	$ds_code = RecibeParametroHTML('ds_code');
	$ds_descuento = RecibeParametroHTML('ds_descuento');
	$fe_start = RecibeParametroFecha('fe_start');
	$fe_end = RecibeParametroFecha('fe_end');
	$fg_activo = RecibeParametroBinario('fg_activo');
    $fg_plan_mensual =RecibeParametroBinario('fg_plan_mensual');
    $fg_plan_anual=RecibeParametroBinario('fg_plan_anual');
    $fg_pago_unico=RecibeParametroBinario('fg_pago_unico');
        
    
    $money=explode("$", $ds_descuento);
    $precio=$money[1]; 
    $porc=explode("%", $ds_descuento);
    $descuento=$porc[0];
    
    if(is_numeric($precio)){       
        $num=$money[1]; 
        $fg_tipo="C";  
    }
   
    if(is_numeric($descuento)){    
        $num=$porc[0];
        $fg_tipo="P";
    
    }

    if(empty($num))
        $num=0;
    if(empty($fg_tipo))
        $fg_tipo=NULL;
    
    
    # Registros 
    $tot_plan=3;    
	$selecionados = 0;
	for($i=1;$i<=$tot_plan;$i++){
		$fg_plan = RecibeParametroNumerico('cl_plan_'.$i);
        
        if($fg_plan==1)
            $fg_pago_unico=1;
        if($fg_plan==2)
            $fg_plan_mensual=1;
        if($fg_plan==3)
            $fg_plan_anual=1;
        
        
		if(!empty($fg_plan))
			$selecionados++;
	}
    
    
    
    $fe_start = "'".ValidaFecha($fe_start)."'";
    $fe_end = "'".ValidaFecha($fe_end)."'";

  
 if(!empty($clave)){
     
          #Verificamos que no exista registro con las fechas seleccionasa y en caso de que si exista pues arrojamos el error.
          if($fg_pago_unico){
            
             $Query="SELECT fl_cupon FROM c_cupones_b2c WHERE  fe_end between ".$fe_start." AND ".$fe_end."  AND fg_pago_unico='1' ";
             $row=RecuperaValor($Query);
             $fl_cupon=$row[0];
             if($row[0])
                 $error=1900;
          }
          if($fg_plan_mensual){

              $Query="SELECT fl_cupon FROM c_cupones_b2c WHERE  fe_end between ".$fe_start." AND ".$fe_end."  AND fg_plan_mensual='1' ";
              $row=RecuperaValor($Query);
              $fl_cupon=$row[0];
              if($row[0])
                  $error=1900;
          }
                  
          if($fg_plan_anual){
              $Query="SELECT fl_cupon FROM c_cupones_b2c WHERE  fe_end between ".$fe_start." AND ".$fe_end."  AND fg_plan_anual='1' ";
              $row=RecuperaValor($Query);
              $fl_cupon=$row[0];
              if($row[0])
                  $error=1900;
          }    
          
          #Regresamos la forma con error
          if($error==1){
          
              echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
              Forma_CampoOculto('clave', $clave);
              Forma_CampoOculto('fg_error', $error);    
              echo "\n</form>
		            <script>
		            document.datos.submit();
		            </script></body></html>";
              exit;
          
          }
          
 }   
    
    
    
    
    
    
  if(!empty($clave)){
      
  

      
          # Actualizamos los datos
          $Query  = "UPDATE c_cupones_b2c SET nb_cupon='".$nb_cupon."', ds_code='".$ds_code."' , mn_cantidad=$num ,fg_tipo='$fg_tipo', ";
          $Query .= "ds_descuento='".$ds_descuento."', fe_start=".$fe_start.", fe_end=".$fe_end.", fg_activo='".$fg_activo."' ,fg_plan_mensual='$fg_plan_mensual',fg_plan_anual='$fg_plan_anual', fg_pago_unico='$fg_pago_unico'  ";
          $Query .= "WHERE fl_cupon=$clave";
          EjecutaQuery($Query);	
      
      
      
  }else{
  
      
      
      #pago_unico
      if($fg_pago_unico){
          
          
          
          # Insertamos la nueva clase global
          $Query  = "INSERT INTO c_cupones_b2c (nb_cupon, ds_code, ds_descuento, fe_start, fe_end, fg_activo,fg_pago_unico,mn_cantidad,fg_tipo) ";
          $Query .= "VALUES('".$nb_cupon."', '".$ds_code."', '".$ds_descuento."', ".$fe_start.", ".$fe_end.", '".$fg_activo."','1',$num,'$fg_tipo')";
          $clave = EjecutaInsert($Query);
          
      }
      
      
      #Pagomes
      if($fg_plan_mensual){
          # Insertamos la nueva clase global
          $Query  = "INSERT INTO c_cupones_b2c (nb_cupon, ds_code, ds_descuento, fe_start, fe_end, fg_activo,fg_plan_mensual,mn_cantidad,fg_tipo) ";
          $Query .= "VALUES('".$nb_cupon."', '".$ds_code."', '".$ds_descuento."', ".$fe_start.", ".$fe_end.", '".$fg_activo."','1',$num,'$fg_tipo')";
          $clave = EjecutaInsert($Query);
      }
      #Pago anio
      if($fg_plan_anual){
          
          # Insertamos la nueva clase global
          $Query  = "INSERT INTO c_cupones_b2c (nb_cupon, ds_code, ds_descuento, fe_start, fe_end, fg_activo,fg_plan_anual,mn_cantidad,fg_tipo) ";
          $Query .= "VALUES('".$nb_cupon."', '".$ds_code."', '".$ds_descuento."', ".$fe_start.", ".$fe_end.", '".$fg_activo."','1',$num,'$fg_tipo')";
          $clave = EjecutaInsert($Query);
          
          
      }
      
      
      
  }
    
    
    
    
    
    
    
    /*
    
    
	# Inserta o actualiza el registro
	if(!empty($clave)){
        
		# Actualizamos los datos
		$Query  = "UPDATE c_cupones_b2c SET nb_cupon='".$nb_cupon."', ds_code='".$ds_code."', ";
		$Query .= "ds_descuento='".$ds_descuento."', fe_start=".$fe_start.", fe_end=".$fe_end.", fg_activo='".$fg_activo."' ,fg_plan_mensual='$fg_plan_mensual',fg_plan_anual='$fg_plan_anual', fg_pago_unico='$fg_pago_unico'  ";
		$Query .= "WHERE fl_cupon=$clave";
		EjecutaQuery($Query);		
	}
	else{
       
	}
    
    */
    
    
	# Redirige al listado
	header("Location: ".ObtenProgramaBase( ));  
?>