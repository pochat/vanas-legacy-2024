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
  
  $fg_opcion_pago=RecibeParametroNumerico('fg_option');
  $no_usuario_adicional=RecibeParametroHTML('no_usuario_adicional');#lo que tiene el spinner
  
  $fg_tiene_plan=RecibeParametroNumerico('fg_tiene_plan');
  

  if($fg_opcion_pago==1)
    $fg_opcion_pago_elegido="M";
  else
    $fg_opcion_pago_elegido="A";
  
  
  
  #verificamos si el usuario, ya tiene un plan asignado.
  $Query="SELECT fl_instituto,fl_princing,fl_current_plan,fg_plan,fe_periodo_final   ";
  $Query.="FROM k_current_plan
           WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $ya_tiene_plan=$row['fl_instituto'];
  $fl_princing_actual=$row['fl_princing']; 
  $fl_current_plan_actual=$row['fl_current_plan'];
  $fg_plan_actual=$row['fg_plan'];
  $fe_periodo_fin=$row['fe_periodo_final'];
  
  $no_total_licencias_atuales_istituto=ObtenNumLicencias($fl_instituto);
  
  $nuevo_numero_total_licncias=$no_total_licencias_atuales_istituto + $no_usuario_adicional;
  
  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual=strtotime('+0 day',strtotime($fe_actual));
  $fe_actual= date('Y-m-d',$fe_actual);
  
  #Obtenemos el año actual
  $anio_actual=date ("Y"); 
   
  
  #Si ya caduco su plan entonces quiere decir que regresa a Fame.
  if($fe_periodo_fin < $fe_actual){
    $ya_expiro_plan=1;   
      
  }
  
  
  
  #Si ya tiene asignado una licencia entonces se agregan sus licencias requeridas.
  if($ya_tiene_plan>0){
      
      
                    #Recuperamos la licencias actuales del usuario.
                    $Query="SELECT no_total_licencias,no_licencias_disponibles,fl_current_plan,fl_princing FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
                    $row=RecuperaValor($Query);
                    $no_licencia_actuales=$row[0];
                    $no_licencias_disponibles_actuales=$row[1];
                    $fl_current_plan_actual=$row[2];
                    $fl_princing_actual=$row['fl_princing'];
      
      
                    
      
      
                    #Obtenemos fecha inicio y fin de vigencia del plan actual existente.                                                 
                    $Query="SELECT fe_periodo_inicial, fe_periodo_final FROM k_current_plan WHERE fl_current_plan=$fl_current_plan_actual  ";
                    $row=RecuperaValor($Query);
                    $fe_inicio_vigencia=$row['fe_periodo_inicial'];
                    $fe_final_vigencia=$row['fe_periodo_final'];
                    
                    
      
                    if($fg_opcion_pago==1 ){#opcion de pago es  mes
      
                        
                                #Recuperamos costo que tine actualmente su plan 
                                $Query="SELECT K.no_total_licencias,K.mn_total_plan, P.mn_mensual,P.mn_anual
                                                FROM k_current_plan K 
                                                JOIN c_princing P ON P.fl_princing=K.fl_princing
                                                            
                                                WHERE fl_current_plan= $fl_current_plan_actual ";                                                            
                                $row=RecuperaValor($Query);
                                $no_total_licencias_actuales=$row['no_total_licencias'];
                                $mn_monto_tarifa_actual_mensual=$row['mn_total_plan'];
                                $mn_costo_mensual_actual=$row['mn_mensual'];
                                $mn_costo_anual_actual=$row['mn_anual'];
                        
                        
                       
                        
                                $fg_tipo_pago="M";
                                
                                
                                $mn_costo_total_sin_tax=$mn_costo_mensual_actual*$no_total_licencias_actuales;
                                
                                #summos el no. de  licencias que tiene actualmente  + el numero nuevo agregado para obtener nuevo  No. de licencias total.
                                $no_nuevo_licencias= $no_licencia_actuales + $no_usuario_adicional;
                                
                                #sumamos las licencias que se encuentran en  estado disponibles alas nuevas agregadas.
                                $no_licencia_disponibles=$no_licencias_disponibles_actuales+$no_usuario_adicional;
                                
                        
                                #identificamos en que rango se encuentra,PARA SABER  NUEVO PLAN y nuevas tarifas.
                                $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
                                $rs = EjecutaQuery($Query);
                                for($i=1;$row=RecuperaRegistro($rs);$i++){
                                    
                                    $mn_rango_ini= $row['no_ini'];
                                    $mn_rango_fin= $row['no_fin'];
                                    
                                    if(( $no_nuevo_licencias >=$mn_rango_ini)&&($no_nuevo_licencias<=$mn_rango_fin) ){
                                        
                                        $fl_princing=$row['fl_princing'];
                                        
                                        #Recuperamos costos segun el plan obtenido del nuevo rango de licencias.
                                        $Query="SELECT mn_mensual,mn_anual FROM c_princing WHERE fl_princing=$fl_princing ";
                                        $row=RecuperaValor($Query);
                                        $mn_costo_mensual=$row[0];
                                        
                                    }

                                }
                                
                                #se obtiene tarifa total es decir costo mensual  por las licencias adquiridas del (nuevo plan adquirido).
                                $mn_costo_total_mensual_nuevo_plan=$mn_costo_mensual*$no_nuevo_licencias;
                                
                                #se_obtiene costo por dia del nuevo plan adquirido.entre 30 dias del mes.
                                $mn_costo_por_dia_nueva_tarifa=$mn_costo_total_mensual_nuevo_plan/30;
                                
                                #Obtenemos el costo por dia de la tarifa que tiene actualmente el istituto
                                //$mn_costo_por_dia_tarifa_actual= $mn_monto_tarifa_actual_mensual/30;
                                $mn_costo_por_dia_tarifa_actual= $mn_costo_total_sin_tax/30;
                                #Obtengo dias que faltan para culminar mi plan actual.
                                $no_dias_faltan_terminar_plan=ObtenDiasRestantesPlan($fe_final_vigencia,$fe_actual);

                                #Calculamos mi credito que tengo restantes para finalizar el plan actual.
                                $mn_credito_actual=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_tarifa_actual;
                                
                                if($ya_expiro_plan==1)
                                    $no_dias_faltan_terminar_plan=30;
                                   

                                #Calculamos el credito del nuevo plan adquirido.
                                //$mn_credito_actual_nuevo_adquirido=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_nueva_tarifa;
                                $mn_credito_actual_nuevo_adquirido=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_nueva_tarifa;
                                
                                if($ya_expiro_plan==1)
                                    $mn_credito_actual=0;
                                
                                #realizamos la resta costo del nuevo plan  y le restamos nuestro credtio disponible para saber cuanto es la diferencia apagar.
                                $mn_monto_total_a_pagar=$mn_credito_actual_nuevo_adquirido-$mn_credito_actual;
                                
                                $no_licencias_compradas=$no_usuario_adicional;
                                
                 
                          
                                
                                
                                
                                
                                
                    }
      
      
      
      
                   if($fg_opcion_pago==2 ){#opcion de pago es  año
                       
                          $fg_tipo_pago="A";
                       
                       
                          
                          #Recuperamos costo que tine atualmente su plan 
                          $Query="SELECT K.no_total_licencias,K.mn_total_plan,P.mn_mensual,P.mn_anual
                                                                                                FROM k_current_plan K 
                                                                                                JOIN c_princing P ON P.fl_princing=K.fl_princing
                                                            
                                                                                                WHERE fl_current_plan= $fl_current_plan_actual ";                                                            
                          $row=RecuperaValor($Query);
                          $no_total_licencias_actuales=$row['no_total_licencias'];
                          $mn_monto_tarifa_actual_anual=$row['mn_total_plan'];
                          $mn_costo_anual_actual=$row['mn_anual']; 
                          
                          $fg_pan_atual_instituto=ObtenPlanActualInstituto($fl_instituto);
                          
                          #Pasa aqui cuando adquieren mas lienceias arriba de 100 y cambian de plan de mes a anual.
                          if($fg_pan_atual_instituto=='M'){
                              $mn_costo_mensual_actual=$row['mn_mensual'];
                              $mn_costo_total_sin_tax=$mn_costo_mensual_actual*$no_total_licencias_actuales;
                              
                              $fg_cambio_plan=1;
                              
                          }else{
                          
                            $mn_costo_total_sin_tax=($mn_costo_anual_actual*$no_total_licencias_actuales)*12;
                          }
                          
                          #summos el no. de  licencias que tiene actualmente  + el numero nuevo agregado para obtener nuevo  No. de licencias total.
                          $no_nuevo_licencias= $no_licencia_actuales + $no_usuario_adicional;
                       
                          #sumamos las licencias que se encuentran en  estado disponibles alas nuevas agregadas.
                          $no_licencia_disponibles=$no_licencias_disponibles_actuales+$no_usuario_adicional;
                       
                          
                          #identificamos en que rango se encuentra,PARA SABER  NUEVO PLAN y nuevas tarifas.
                          $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
                          $rs = EjecutaQuery($Query);
                          for($i=1;$row=RecuperaRegistro($rs);$i++){
                              
                              $mn_rango_ini= $row['no_ini'];
                              $mn_rango_fin= $row['no_fin'];
                              
                              if(( $no_nuevo_licencias >=$mn_rango_ini)&&($no_nuevo_licencias<=$mn_rango_fin) ){
                                  
                                  $fl_princing=$row['fl_princing'];
                                  
                                  #Recuperamos costos segun el plan obtenido del nuevo rango de licencias.
                                  $Query="SELECT mn_mensual,mn_anual FROM c_princing WHERE fl_princing=$fl_princing ";
                                  $row=RecuperaValor($Query);
                                  $mn_costo_anual=$row[1];
                                  
                                  
                              }

                          }
                          
                          
                          
                          #se obtiene tarifa total es decir  costo mensual apagar por las licencias adquiridas (nuevo plan adquieirdo).
                          $mn_costo_total_anual_nuevo_plan=($mn_costo_anual*$no_nuevo_licencias)*12;
                          
                          #se obtiene costo por mes de la nueva tarifa con nueva numero de licencis
                          $mn_costo_mensual_nueva_tarifa=$mn_costo_total_anual_nuevo_plan /12 ;  //$mn_costo_mensual_nueva_tarifa= $mn_costo_anual * $no_nuevo_licencias / 12;
                          
                          #se_obtiene costo por dia del nuevo plan adquirido.
                          $mn_costo_por_dia_nueva_tarifa= (   $mn_costo_total_anual_nuevo_plan   ) / 365;
                          
                          #Recuperamos los creditos que tenemos actualmente
                          #Obtenemos el costo por dia de la tarifa que tiene actualmente el istituto(antes de agregar las licenciaa)
                          $mn_costo_por_dia_tarifa_actual= $mn_costo_total_sin_tax/365;
                          
                          #Obtengo dias que faltan para culminar mi plan actual.
                          $no_dias_faltan_terminar_plan=ObtenDiasRestantesPlan($fe_final_vigencia,$fe_actual);

                          #Calculamos mi credito que tengo restantes para finalizar el plan actual.
                          $mn_credito_actual=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_tarifa_actual;
                          
                          if($ya_expiro_plan==1)
                              $no_dias_faltan_terminar_plan=30;
                          

                          #Calculamos el credito del nuevo plan adquirido.
                          $mn_credito_actual_nuevo_adquirido=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_nueva_tarifa;

                          if($ya_expiro_plan==1)
                              $mn_credito_actual=0;

                          #realizamos la resta costo del nuevo plan  y le restamos nuestro credtio disponible para saber cuanto es la diferencia apagar.
                          
                          $mn_monto_total_a_pagar=$mn_credito_actual_nuevo_adquirido-$mn_credito_actual;
                          
                          $no_licencias_compradas=$no_usuario_adicional;
                          
                          
                          
                          if($fg_cambio_plan){
                              
                             $mn_monto_total_a_pagar= $mn_costo_total_anual_nuevo_plan ;#-$mn_costo_total_sin_tax;
                              
                          }
                          
                          
                          
                   }
      
      
      
      
      
   
  #Es nuevo usuario de FAME.    
  }else{
  
  
              #Recuperamos los usuarios actuales 
              $no_user_actuales=ObtenNumeroUserInst($fl_instituto);
              $no_licencias_compradas=$no_user_actuales + $no_usuario_adicional;
              $no_user_agregados= $no_usuario_adicional; //- $no_user_actuales;     
  
              #identificamos en que rango se encuentra, e identificamos el tipo de plan.
              $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
              $rs = EjecutaQuery($Query);
              for($i=1;$row=RecuperaRegistro($rs);$i++) {
                  
                  $mn_rango_ini= $row['no_ini'];
                  $mn_rango_fin= $row['no_fin'];
                  
                          if(( $no_licencias_compradas >=$mn_rango_ini)&&($no_licencias_compradas<=$mn_rango_fin) ){
                              $fl_princing=$row['fl_princing'];
                              
                              #Recuperamos costos segun el plan .
                              $Query="SELECT mn_mensual,mn_anual FROM c_princing WHERE fl_princing=$fl_princing";
                              $row=RecuperaValor($Query);
                              $mn_costo_mensual=$row[0];
                              $mn_costo_anual=$row[1];
                              
                          }

               }
              
              
              /******************SI LA OPCION ELEGIDA ES MES************************/
              if($fg_opcion_pago==1){
               
                      $fg_tipo_pago="M";
                      #se calcula fecha de termino del plan por mes.
                     // $fe_final_periodo=strtotime('+1 month',strtotime($fe_actual));
                     // $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                      
                      
                      //$no_licencias_usadas=ObtenNumeroUserInst($fl_instituto);
                      //$no_licencias_disponibles=$no_usuario_adicional;
                      
                     /* 
                      #se genera el plan temporal. 
                      $Query="INSERT INTO k_current_plan  (fl_instituto,fl_princing,fg_plan,no_total_licencias,no_licencias_disponibles,no_licencias_usadas,no_total_storage,fg_estatus,fe_periodo_inicial,fe_periodo_final )  ";
                      $Query.="VALUES ($fl_instituto,$fl_plan,'$fg_tipo_pago',$no_licencias_compradas,$no_licencias_disponibles,$no_licencias_usadas,'','A','$fe_actual','$fe_final_periodo')";
                      $fl_current_plan_temp=EjecutaInsert($Query);
                      */
                      
                      
                      #Se calcula el costo total    #no_licencias * el costo
                      $mn_mensual_total=$no_licencias_compradas * $mn_costo_mensual ;
                      
                      
                      $ds_descripcion=ObtenEtiqueta(1554)."".$no_licencias_compradas." licences";
                      $fg_pagado="0";#este registro se actualizara cuando el ago se haya realizado y se vea reflejado..
                      
                      
                    /*  #se inserta el registro y costo por mes       
                      $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago)";
                      $Query.="VALUES($fl_current_plan,$mn_mensual_total,'1','$fe_actual','$fe_final_periodo','$fg_pagado','','$ds_descripcion','NP') ";
                      $fl_adm_pagos=EjecutaInsert($Query);
                    */
                      $mn_monto_total_a_pagar =$mn_mensual_total;

                    /*  $Query="UPDATE k_current_plan SET mn_total_plan=$mn_mensual_total WHERE fl_current_plan =$fl_current_plan ";
                      EjecutaQuery($Query);
                      */
                      $no_licencias_total=$no_licencias_compradas;
                      
                     
                      
                      
                      
                      
                      
              
              }
              /*******************SI LA OPCION DE PAGO ES ANUAL********************/
              if($fg_opcion_pago==2){
                  
                  
                                      $fg_tipo_pago="A";
                  
                                      #se calcula fecha de termino del plan por año.
                                      $fe_final_periodo=strtotime('+1 year',strtotime($fe_actual));
                                      $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                  
                  
                                      #Actualizaamsos el plan elegido. por default lo dejamos en Basico
                                     // $Query="UPDATE c_instituto SET cl_plan_fame='1' WHERE fl_instituto=$fl_instituto ";
                                     // EjecutaQuery($Query);
                  
                                      #Por defaul los usuarios actuleas pasan a adquieir esa licencias.
                                      $no_licencias_usadas=ObtenNumeroUserInst($fl_instituto);
                                      $no_licencias_disponibles=$no_usuario_adicional;

                  
                  
                  
                                      #se genera y se crea su plan elegido del instituto.
                                    //  $Query="INSERT INTO k_current_plan  (fl_instituto,fl_princing,fg_plan,no_total_licencias,no_licencias_disponibles,no_licencias_usadas,no_total_storage,fg_estatus,fe_periodo_inicial,fe_periodo_final )  ";
                                    //  $Query.="VALUES ($fl_instituto,$fl_plan,'$fg_tipo_pago',$no_licencias_compradas,$no_licencias_disponibles,$no_licencias_usadas,'', 'A', '$fe_actual','$fe_final_periodo')";
                                    //  $fl_current_plan=EjecutaInsert($Query);
                  
                  
                                      #se calcula el monto mensual a pagar .  mn_menual * No_licencias_contratado por 12 meses.   
                                      $mn_costo_total_anual= ( $mn_costo_anual * $no_licencias_compradas) * 12 ;
                  
                                      
                                      
                                      #Obtenemos el AÑO actual
                                      $anio_actual=date ("y"); 
                  
                                      $mn_monto_total_a_pagar =$mn_costo_total_anual;
                                      $no_licencias_total=$no_licencias_compradas;
                                     
                  
                                      $ds_descripcion=ObtenEtiqueta(1554)." ".$no_licencias_compradas." licences";
                  
                                      $fg_publicar="1";//anteriormente este servia para visualizar en el listado , se lodejo por default
                                      $fg_pagado="0";
                  
                                      #se inserta el registro y costo por mes       
                                    //  $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion)";
                                    //  $Query.="VALUES($fl_current_plan,$mn_monto_total_a_pagar,'$fg_publicar','$fe_actual','$fe_final_periodo','$fg_pagado','','$ds_descripcion') ";
                                    //  $fl_adm_pagos=EjecutaInsert($Query);
                  
                                      #Actualizamos datos de referencia.
                                   //   $Query="UPDATE k_current_plan SET mn_total_plan=$mn_costo_total_anual,no_licencias_usadas=$no_licencias_usadas,no_licencias_disponibles=$no_licencias_disponibles  WHERE fl_current_plan =$fl_current_plan ";
                                   //   EjecutaQuery($Query);
                  
                  
                  
                  
                  
                  
                                      /*******************************Asi estaba anteriormente.**************************************/
                                      //$fg_pagado="1";#este registro se actualizara , se lo dejo en 1 para hacer pruebas cuando el ago se haya realizado y se vea reflejado.y tambie se actualizara su fecha de pago.
                  
                  
                                      //#se inserta el registro y costo por mes       
                                      //$Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago)";
                                      //$Query.="VALUES($fl_current_plan,$mn_costo_total_anual,'1','$fe_actual','$fe_final_periodo','$fg_pagado','') ";
                                      //$fl_adm_pagos=EjecutaInsert($Query);
                  
                  
                  
                  
                  
                                      // $mn_costo_mensual=$no_licencias_total * $mn_costo_anual / 12;
                  
                                      //$contador=0;
                                      /* for ($i=1;$i<=12;$i++){#ciclo que comprende los 12 meses.
                  
                                      $contador++;
                  
                  
                                      #calculamos la fecha final del periodo por mes(es decir a la fecha actual  le vamos sumando hats l¿cubrir los 12 meses.)
                                      $fe_final_periodo=strtotime('+1 month',strtotime($fe_actual));
                                      $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                  
                  
                                      if($contador==1){
                                      $fg_pagado="1";
                                      $fg_publicar="1";
                                      }else{
                                      $fg_pagado="0";
                                      $fg_publicar="1";
                  
                                      }
                  
                  
                                      $ds_descripcion=ObtenEtiqueta(1554)." ".$no_usuario_adicional." licences";
                  
                                      #se inserta el registro y costo por mes       
                                      $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion)";
                                      $Query.="VALUES($fl_current_plan,$mn_costo_mensual,'$fg_publicar','$fe_actual','$fe_final_periodo','$fg_pagado','','$ds_descripcion') ";
                                      $fl_adm_pagos=EjecutaInsert($Query);
                  
                  
                  
                  
                                       */  
                  
                  
                                      /**
                                       * se lo quimtamos sino la cuen no va asi
                                       * ala fecha final le sumamos un dia por que apartir de ahi se cuenta el siguiente mes.
                                       */ 
                                      // $fe_final_periodo=strtotime('+1 day',strtotime($fe_final_periodo));
                                      // $fe_final_periodo=date('Y-m-d',$fe_final_periodo);
                  
                  
                  
                  
                  
                                      #se renombra la fecha actual, la fecha final del periodo se convierte en mes inicial y esto para ir agregando y sumando mes por mes.
                                      //$fe_actual=$fe_final_periodo;
                  
                  
                                      //}
                  
                  
                  
                  
                  
                  
                  
                  
                  
               }
              
              
              
              
              
              
  
  }    
  
  
  
  
  
  
  
  
  
  
  echo"<script>
  PresentaPlanActual();
  </script>";
  echo"<input type='hidden' value='$mn_monto_total_a_pagar' name='mn_total_pagar_' id='mn_total_pagar_' >
       <input type='hidden' value='$no_licencias_compradas' name='no_licencias_compradas' id='no_licencias_compradas' >
       <input type='hidden' value='$fg_tipo_pago' name='fg_tipo_plan' id='fg_tipo_plan' > 
       <input type='hidden' value='$fl_princing' name='fl_princing' id='fl_princing' >
      
        
  ";
  

                                     
?>


<script>

    function PresentaStriipe() {

        var mn_total_pagar = document.getElementById('mn_total_pagar_').value;
        var no_licencias_compradas = document.getElementById('no_licencias_compradas').value;//no_licencias actuales,compradas o agregadas.
        var fg_tipo_plan = document.getElementById('fg_tipo_plan').value;
        var fl_princing = document.getElementById('fl_princing').value;
     
       
        $.ajax({
            type: 'POST',
            url: 'site/presenta_metodo_pago.php',
            data: 'mn_total_pagar=' + mn_total_pagar +
                 '&fg_tipo_plan=' + fg_tipo_plan +

                 '&fl_princing=' + fl_princing +
                  '&no_licencias_compradas=' + no_licencias_compradas,

            async: true,
            success: function (html) {
                $('#presenta_strippe').html(html);
            }
        });

        $('#tab5').removeClass('hidden');



    }

 </script>

<script>
    $('#presenta_strippe').empty();
    PresentaStriipe();
</script>


