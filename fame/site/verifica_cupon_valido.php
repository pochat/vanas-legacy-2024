<?php
  # Libreria de funciones
  require("../lib/self_general.php");

  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  
  
  # Recibe parametros
  $ds_codigo_cupon = RecibeParametroHTML('ds_codigo_cupon');  
  $mn_subtotal=RecibeParametroFlotante('mn_subtotal');
  $mn_tax=RecibeParametroFlotante('mn_tax');
  $fg_plan_seleccionado=RecibeParametroHTML('fg_plan_seleccionado');
  $mn_total=$mn_subtotal + $mn_tax;
  
  #Tax usuario
  $mn_porcentaje_tax=Tax_Can_User($fl_usuario);
  $mn_porcentaje_tax_=$mn_porcentaje_tax*100;
  
  $mn_tax_anterior=Conv_Dollars_Stripe($mn_tax);
  $mn_subtotal_anterior=Conv_Dollars_Stripe($mn_subtotal);
  
  $mn_total_anterior=Conv_Dollars_Stripe($mn_total);
  
  
  if($ds_codigo_cupon){
  
  
      #Verificamos si efectivamente existe ese codigo de cupon en BD. Y su fecha de vigencia y que pertenescza a ese plan
      $Query="SELECT fl_cupon, nb_cupon,ds_code,fe_end,fg_activo,mn_cantidad,fg_tipo FROM c_cupones_b2c WHERE ds_code='$ds_codigo_cupon'  ";
      if($fg_plan_seleccionado==1)#pAGO UNICO
      $Query.="AND fg_pago_unico='1' ";    
      if($fg_plan_seleccionado==2)#PLAN MES
      $Query.="AND fg_plan_mensual='1' ";    
      if($fg_plan_seleccionado==3)#PLAN ANUAL
      $Query.="AND fg_plan_anual='1' "; 
      
      $row=RecuperaValor($Query);
      $mn_cantidad_descuento_cupon=$row['mn_cantidad'];
      $fe_end_cupon= GeneraFormatoFecha($row['fe_end']);
      $fe_expiracion_cupon=$row['fe_end'];
      $ds_codigo_cupon=str_texto($row['ds_code']);
      $fg_tipo_descuento=$row['fg_tipo'];
      $fl_cupon=$row['fl_cupon'];
      
      
      if($fl_cupon){
      
          
          
          #Revisamos su fecha de vigencia
          $fe_actual=ObtenerFechaActual();
          
          #Validamos que el cupon sea vigente para canjear.
          if($fe_expiracion_cupon<$fe_actual){
              $vigente=0;
              $mn_cantidad_con_descuento=$mn_total;
              
              #Error Codifo no existente.
              echo json_encode((Object)array(
                 'success' => 'Codigo ya expiro',
                 'mn_costo_anterior'=>$mn_subtotal,
                 'mn_tax_anterior'=>$mn_tax,
                 'mn_cantidad_con_descuento'=>$mn_cantidad_con_descuento,
                 'mn_tax_anterior_stripe'=>$mn_tax_anterior,
                 'mn_subtotal_total_anterior'=>$mn_total_anterior,
                 'fg_error' => '1',//codigo ya expiro
                
             
              ));
              
              
              
              
          }else{
              $vigente=1;

                              if($fg_plan_seleccionado==1){
              
                                    #Se raliza el descuento %
                                    if($fg_tipo_descuento=='P'){
                                        
                                        $fg_signo_descuento=$mn_cantidad_descuento_cupon."%";
                                        
                                        #Obtenemos el porcentaje con respecto als 12 meses.
                                        $mn_equivalente_porcentaje=($mn_cantidad_descuento_cupon * $mn_subtotal) /100;
                                        
                                        $mn_costo_menos_porcentaje=$mn_subtotal-$mn_equivalente_porcentaje;
                                        $mn_cantidad_con_descuento=$mn_costo_menos_porcentaje;
                                        
                                        $mn_cantidad_por_mes_sin_tax=number_format($mn_cantidad_con_descuento,2, '.','.');
                                        #Obtenemos el tax ala nueva cantidad.
                                        $mn_tax_nueva_cantidad=number_format( ($mn_cantidad_por_mes_sin_tax*$mn_porcentaje_tax_)/100,2);
                                        
                                        #Le sumamos el tax a la nueva cantidad
                                        $mn_cantidad_con_nuevo_tax=$mn_cantidad_por_mes_sin_tax + $mn_tax_nueva_cantidad;
                                        $mn_cantidad_con_descuento=number_format($mn_cantidad_con_nuevo_tax,2,'.','.');
                                        
                                        $mn_cantidad_pagar_stripe=Conv_Dollars_Stripe($mn_cantidad_con_descuento);
                                        $mn_cantidad_pagar_tax_stripe=Conv_Dollars_Stripe($mn_tax_nueva_cantidad);
                                        
                                        $mn_cantidad_descuento_vista=$mn_equivalente_porcentaje;
                                        
                                        $mn_subtotal_actual_vista=number_format($mn_costo_menos_porcentaje,2);
                                    }
                                  
                                   #se realiza el descuento por $
                                   if($fg_tipo_descuento=='C'){
                                       
                                       $fg_signo_descuento="$".number_format(($mn_cantidad_descuento_cupon),2);
                                       
                                       #Obtenemos el porcentaje con respecto als 12 meses.
                                       $mn_equivalente_porcentaje=$mn_cantidad_descuento_cupon ;
                                       
                                       $mn_costo_menos_porcentaje=$mn_subtotal-$mn_equivalente_porcentaje;
                                       
                                       $mn_cantidad_por_mes_sin_tax=number_format($mn_costo_menos_porcentaje,2, '.','.');
                                       
                                       #Obtenemos el tax ala nueva cantidad.
                                       $mn_tax_nueva_cantidad=number_format( ($mn_cantidad_por_mes_sin_tax*$mn_porcentaje_tax_)/100,2 );
                                       
                                       #Le sumamos el tax a la nueva cantidad
                                       $mn_cantidad_con_nuevo_tax=$mn_cantidad_por_mes_sin_tax + $mn_tax_nueva_cantidad;
                                       
                                       $mn_cantidad_con_descuento=number_format($mn_cantidad_con_nuevo_tax,2,'.','.');
                                       
                                       $mn_cantidad_pagar_stripe=Conv_Dollars_Stripe($mn_cantidad_con_descuento);
                                       $mn_cantidad_pagar_tax_stripe=Conv_Dollars_Stripe($mn_tax_nueva_cantidad);
                                       
                                       $mn_cantidad_descuento_vista=$mn_cantidad_descuento_cupon;
                                       
                                       $mn_subtotal_actual_vista=number_format($mn_costo_menos_porcentaje,2);
                                   }
                                  
                                  
                                  
              
              
                              }else{
              

              
                              #Se raliza el descuento %
                              if($fg_tipo_descuento=='P'){
                  
                                  $fg_signo_descuento=$mn_cantidad_descuento_cupon."%";
                  
                                  #Realizamos cuanto costaria por 12 meses(obligados a 1 año.)
                                  $mn_costo_doce_meses_sin_tax=$mn_subtotal * 12;
                                  #Obtenemos el porcentaje con respecto als 12 meses.
                                  $mn_equivalente_porcentaje=($mn_costo_doce_meses_sin_tax * $mn_cantidad_descuento_cupon)/100;
                  
                                  $mn_costo_menos_porcentaje=$mn_costo_doce_meses_sin_tax - $mn_equivalente_porcentaje;  //275
                  
                  
                                  $mn_costo_por_mes=$mn_costo_menos_porcentaje/12;
                                  $mn_cantidad_por_mes_sin_tax=number_format($mn_costo_por_mes,2, '.','.');
                                  $fg_signo_descuento=number_format(($mn_cantidad_descuento_cupon),2) ."%";
                  
                  
                                  #Obtenemos el tax ala nueva cantidad.
                                  $mn_tax_nueva_cantidad= ($mn_cantidad_por_mes_sin_tax*$mn_porcentaje_tax_)/100;
                  
                                  #Le sumamos el tax a la nueva cantidad
                                  $mn_cantidad_con_nuevo_tax=$mn_cantidad_por_mes_sin_tax + $mn_tax_nueva_cantidad;
                  
                  
                                  $mn_cantidad_con_descuento=number_format($mn_cantidad_con_nuevo_tax,2,'.','.');
                  
                  
                                  $mn_cantidad_descuento_cupon=$mn_equivalente_porcentaje;
                                  $mn_tax_nueva_cantidad=number_format($mn_tax_nueva_cantidad,2,'.','.');
                  
                                  $mn_cantidad_pagar_stripe=Conv_Dollars_Stripe($mn_cantidad_con_descuento);
                                  $mn_cantidad_pagar_tax_stripe=Conv_Dollars_Stripe($mn_tax_nueva_cantidad);
                  
                                  $mn_cantidad_descuento_vista=number_format(($mn_cantidad_descuento_cupon/12),2,'.','.');
                  
                                  $mn_subtotal_actual_vista=number_format($mn_cantidad_por_mes_sin_tax,2);
                              }
                              
                              
                              
                              
                              
                              #se realiza el descuento por $
                              if($fg_tipo_descuento=='C'){  
                  
                  
                    
                                    if($fg_plan_seleccionado==2){
                                          #Realizamos cuanto costaria por 12 meses(obligados a 1 año.)
                                          $mn_costo_doce_meses_sin_tax=$mn_subtotal * 12;
                                          $mn_costo_menos_porcentaje=$mn_costo_doce_meses_sin_tax - $mn_cantidad_descuento_cupon; 
                                          $mn_costo_por_mes=$mn_costo_menos_porcentaje/12;
                          
                                          #Obtenemos el tax ala nueva cantidad.
                                          $mn_tax_nueva_cantidad= number_format(  ($mn_costo_por_mes*$mn_porcentaje_tax_)/100,2,'.','.');
                                          
                                          #Le sumamos el tax a la nueva cantidad
                                          $mn_cantidad_con_nuevo_tax=$mn_costo_por_mes + $mn_tax_nueva_cantidad;
                                          $mn_cantidad_por_mes_sin_tax=number_format($mn_costo_por_mes,2, '.','.');
                                          $mn_cantidad_con_descuento=number_format($mn_cantidad_con_nuevo_tax,2, '.','.');
                                          
                                          $fg_signo_descuento="$".number_format(($mn_cantidad_descuento_cupon),2);

                                          $mn_cantidad_pagar_stripe=Conv_Dollars_Stripe($mn_cantidad_con_descuento);
                                          $mn_cantidad_pagar_tax_stripe=Conv_Dollars_Stripe($mn_tax_nueva_cantidad);
                                          
                                          
                                          $mn_cantidad_descuento_vista=number_format($mn_cantidad_descuento_cupon,2,'.','.');
                                          
                                          
                                          $mn_subtotal_actual_vista=number_format($mn_costo_por_mes,2);
                                          
                                    }else{
                                    
                                          $mn_costo_doce_meses_sin_tax=$mn_subtotal;
                                          $mn_costo_menos_porcentaje=$mn_costo_doce_meses_sin_tax-$mn_cantidad_descuento_cupon;
                                          $mn_costo_por_mes=$mn_costo_menos_porcentaje;
                                          
                                          #Obtenemos el tax ala nueva cantidad.
                                          $mn_tax_nueva_cantidad= number_format(  ($mn_costo_por_mes*$mn_porcentaje_tax_)/100,2,'.','.');
                                          #Le sumamos el tax a la nueva cantidad
                                          $mn_cantidad_con_nuevo_tax=$mn_costo_por_mes + $mn_tax_nueva_cantidad;
                                          
                                          $mn_cantidad_por_mes_sin_tax=number_format($mn_costo_por_mes,2, '.','.');
                                          $mn_cantidad_con_descuento=number_format($mn_cantidad_con_nuevo_tax,2, '.','.');
                                          $fg_signo_descuento="$".number_format(($mn_cantidad_descuento_cupon),2);
                                          
                                          $mn_cantidad_pagar_stripe=Conv_Dollars_Stripe($mn_cantidad_con_descuento);
                                          $mn_cantidad_pagar_tax_stripe=Conv_Dollars_Stripe($mn_tax_nueva_cantidad);
                                          
                                          
                                          $mn_cantidad_descuento_vista=number_format($mn_cantidad_descuento_cupon,2,'.','.');
                                    
                                          $mn_subtotal_actual_vista=number_format($mn_costo_por_mes,2);
                                          
                                    }      
                                          
                                
                                        
                      
                      
                              }
              
                              }
              
              
                              #Error Codifo no existente.
                              echo json_encode((Object)array(
                                 'success' => 'Congratulations', 
                                 'fg_error' => '2',//Confgratulations
                                 'mn_cantidad_descuento'=>$mn_cantidad_descuento_cupon,         //Reprsenta la cantidad o porcentaje DE DESCUENTO
                                 'mn_cantidad_descuento_vista'=>$mn_cantidad_descuento_vista,
                                 'mn_cantidad_con_descuento'=>$mn_cantidad_con_descuento,       //reprsenta la cantidad ya con el descuento aplicado sin tax
                                 'mn_cantidad_por_mes'=>$mn_cantidad_por_mes_sin_tax,           //reprsensta la cantidad a pagar sin tax.
                                 'fg_signo_descuento'=>$fg_signo_descuento,                     //especifica el SIGNO de descuento $|%
                                 'fg_tipo_descuento'=>$fg_tipo_descuento,
                                 'mn_costo_anterior'=>$mn_subtotal,
                                 'mn_tax_anterior'=>$mn_tax,
                                 'mn_nuevo_tax'=>$mn_tax_nueva_cantidad,
                                 'mn_cantidad_stripe'=>$mn_cantidad_pagar_stripe,
                                 'mn_subtotal_vista'=>$mn_subtotal_actual_vista,
                                 'mn_tax_sripe'=>$mn_cantidad_pagar_tax_stripe,
                                 'fl_cupon'=>$fl_cupon,
                                 'vigente'=> '',
                                 'expirado'=>''
             
                              ));
                              
                              
                              
                              
                              
                              
              
              
          } 
          

          
      
      }else{
          
          $mn_cantidad_con_descuento=$mn_total;
          
          #Error Codifo no existente.
          echo json_encode((Object)array(
             'mensaje' => 'Codigo No existente en BD',
             'mn_cantidad_con_descuento'=>$mn_cantidad_con_descuento,
             'mn_costo_anterior'=>$mn_subtotal,
             'mn_tax_anterior'=>$mn_tax,
             'mn_tax_anterior_stripe'=>$mn_tax_anterior,
             'mn_subtotal_total_anterior'=>$mn_total_anterior,
             'fg_error' => '1'//codigo no existente en BD
          ));
          
          
      
      }
      
      
      
      
      
      
      
     
  } else {
      
    $mn_cantidad_con_descuento=$mn_total;
      
    echo json_encode((Object)array(
        'mensaje' => 'Codigo No existente en BD',
        'mn_cantidad_con_descuento'=>$mn_cantidad_con_descuento,
        'mn_costo_anterior'=>$mn_subtotal,
        'mn_tax_anterior'=>$mn_tax,
        'mn_tax_anterior_stripe'=>$mn_tax_anterior,
        'mn_subtotal_total_anterior'=>$mn_total_anterior,
        'fg_error' => '1' //Codigo no existente en BD
       
        ));
  }
  
  
  

  
?>