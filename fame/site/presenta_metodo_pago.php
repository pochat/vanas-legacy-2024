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

  #RecibeParametros. 
  $no_licencias_compradas=RecibeParametroNumerico('no_licencias_compradas');
  $mn_monto_pagar=RecibeParametroFlotante('mn_total_pagar');
  $fl_princing=RecibeParametroNumerico('fl_princing');   
  $fg_tipo_plan=RecibeParametroHTML('fg_tipo_plan');
  
  #Recuperamos datos del usuario actual
  $fl_instituto=ObtenInstituto($fl_usuario);

  #Recuperamos el estado/provincia del usuario para determina el monto del tax.
  $mn_tax=Tax_Can_User($fl_usuario); 
  if(empty($mn_tax))
  $mn_tax=0;
  
  
  
  #Verificamos si tiene plan el instituto
  $Query="SELECT ds_pais,ds_instituto,fg_tiene_plan,ds_descripcion  
          FROM c_instituto I 
          JOIN c_pais P ON P.fl_pais=I.fl_pais 
          LEFT JOIN c_plan_fame C ON C.cl_plan_fame=I.cl_plan_fame  
          WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $ds_pais =$row['ds_pais'];
  $nb_instituto=$row['ds_instituto'];
  $fg_tiene_plan=$row['fg_tiene_plan'];
  $nb_plan_fame=$row['ds_descripcion'];
  
  #Aun no tiene plan el Instituto enonces se le asigna el esssencial plan.
  if(empty($nb_plan_fame)){
      $Query="SELECT ds_descripcion FROM c_plan_fame WHERE cl_plan_fame=1  ";
      $row=RecuperaValor($Query);
      $nb_plan_fame=str_texto($row[0]);
  
  }
  
  
  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual=strtotime('+0 day',strtotime($fe_actual));
  $fe_actual= date('d-m-Y',$fe_actual);
  

  #Recuperamos ecosto del plan actual
  $Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princing ";
  $row=RecuperaValor($Query);
  $mn_mensual=$row['mn_mensual'];
  $mn_anual=$row['mn_anual'];
  $mn_descuento_anual=$row[2];
  $mn_descuento_mensual=$row[3];
      
  if($fg_tipo_plan=='M'){
            if(empty($fg_tiene_plan)){#el monto a pagar considera la suma de los uausrios actuales.

                    #Obtenemos las licencias adquiridas para determinar el concepto del pago.
                $ds_decripcion_pago= $nb_plan_fame."-".ObtenEtiqueta(1705)." ".$no_licencias_compradas." licences";
                $fg_motivo_pago=PAGO_NEW_PLAN; 
              
            }else{
            
                
                $ds_decripcion_pago=$nb_plan_fame."-".ObtenEtiqueta(1705)." ".$no_licencias_compradas." licences added";
                $fg_motivo_pago=PAGO_ADD_LICENCES;
               
            
            }
            
 
            
              $mn_descuento=number_format($mn_descuento_mensual);     
  }
 
  if($fg_tipo_plan=='A'){#Plan anual
              #Si adquiere licencias por primera vez
              if(empty($fg_tiene_plan)){

                  #Obtenemos las licencias adquiridas para determinar el concepto del pago.
                  $ds_decripcion_pago=$nb_plan_fame."-".ObtenEtiqueta(1706)." ".$no_licencias_compradas." licences";
                  $fg_motivo_pago=PAGO_NEW_PLAN; 
              }else{
              
                  #Obtenemos las licencias adquiridas para determinar el concepto del pago.
                  $ds_decripcion_pago=$nb_plan_fame."-".ObtenEtiqueta(1706)." ".$no_licencias_compradas." licences added";
                  $fg_motivo_pago=PAGO_ADD_LICENCES;
              }
          
          
              $mn_descuento=number_format($mn_descuento_anual);
          
    
  }
  
 
  $url_charge="site/charge.php";
 
 #Stripe no permite mas de dos decimales y se da formato a dos decimales nadamas.
  if(is_int($mn_monto_pagar))	
      $mn_monto_pagar=$mn_monto_pagar;
  else
      $mn_monto_pagar=number_format((float)$mn_monto_pagar,2,'.','');      
 
  
?>

<div class=""

<div class="row">
	<div class="col-md-3">

	</div>
	<div class="col-md-6">
	
		<?php FormaStripe('frm_stripe',$mn_monto_pagar,$mn_tax,$url_charge ,'',$ds_decripcion_pago,$fg_tipo_plan,$fg_motivo_pago,$no_licencias_compradas,$mn_descuento); ?>
	
	</div>
	<div class="col-md-3">
	
	</div>
	
</div>


