<?php
  
  # Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $perfil_usuario = ObtenPerfilUsuario($fl_usuario);
  
  # Recibe Parametros
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp');
  $fl_alumno=RecibeParametroNumerico('fl_alumno');
  $price_desbloquear_stripe=RecibeParametroFlotante('mn_precio');
  $fg_plan=RecibeParametroBinario('fg_plan');
  $fg_tipo_plan=RecibeParametroNumerico('fg_tipo_plan');
  $fg_pago_individual_curso=RecibeParametroBinario('fg_pago_individual_curso');
  
  
  if($fg_pago_individual_curso){
  
     $fg_cupon=1;
     $fg_plan_seleccionado=1;
  
  }else{ 
  
      
          #Recupermaos el plan mes y año para el curso 
          $Query="SELECT ds_descuento_mensual, ds_descuento_anual,mn_mensual,mn_anual FROM c_princing_course WHERE 1=1 ";
          $row=RecuperaValor($Query);
          $porcentaje_mes=number_format($row['ds_descuento_mensual']); 
          $porcentaje_anio=number_format($row['ds_descuento_anual']);
          $mn_mes=$row['mn_mensual'];
          $mn_anio=$row['mn_anual'];
          $ds_codigo_cupon=str_texto($row['ds_code']);
          
          
          if($fg_tipo_plan==1){#Mes
              $price_desbloquear_stripe=$mn_mes;  #ObtenConfiguracion(124);
              $fg_plan_curso_alumno=1;
              $fg_plan_seleccionado=2;
          }else{#Anio
              $price_desbloquear_stripe=$mn_anio;#ObtenConfiguracion(125);
              $fg_plan_curso_alumno=2;
              $fg_plan_seleccionado=3;
          }
          
          $fg_cupon=1;
          
  
  }
  
  
  #Stripe no permite mas de dos decimales y se da formato a dos decimales nadamas.
  if(is_int($price_desbloquear_stripe))	
      $mn_monto_pagar=$price_desbloquear_stripe;
  else
      $mn_monto_pagar=number_format((float)$price_desbloquear_stripe,2,'.',''); 
 
  
  
  
  
   
	
	#Recuperamos el estado/provincia del usuario para determina el monto del tax.
	$mn_tax=Tax_Can_User($fl_usuario); 
	if(empty($mn_tax))
		$mn_tax=0;
	
    
    #Recuperamos el pais.
    $Query  = "SELECT  b.fl_pais, b.ds_state ";
    $Query .= "FROM c_usuario a ";
    $Query .= "JOIN k_usu_direccion_sp b ON(a.fl_usuario=b.fl_usuario_sp) ";
    $Query .= "WHERE a.fl_usuario=$fl_usuario ";
    $row = RecuperaValor($Query);
    $fl_pais = $row[0];
    $fl_provincia = $row[1];
    if(($fl_pais==38)&&(empty($fl_provincia))){
        $mn_tax=5/100;
    }




	$url_charge="site/charge_desbloquear_curso.php";
	$ds_decripcion_pago=ObtenEtiqueta(2093);
                            
                            
    FormaStripe('frm_stripe',$mn_monto_pagar,$mn_tax,$url_charge,$fl_programa_sp,$ds_decripcion_pago,'','','','',1,$fg_plan_curso_alumno,$fg_cupon,$fg_plan_seleccionado); 
						  

	

    
?>
	                 