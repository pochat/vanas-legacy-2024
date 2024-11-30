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
  $no_usuario_adicional=RecibeParametroHTML('no_usuario_adicional');
  
  $fg_tiene_plan=RecibeParametroNumerico('fg_tiene_plan');
  

  if($fg_opcion_pago==1)
    $fg_opcion_pago_elegido="M";
  else
    $fg_opcion_pago_elegido="A";
  
  
  
  #verificamos si el usuario, ya tiene un plan asignado.
  $Query="SELECT fl_instituto,fl_princing,fl_current_plan,fg_plan  FROM k_current_plan 
  WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $ya_tiene_plan=$row['fl_instituto'];
  $fl_princing_actual=$row['fl_princing'];
  
  $fl_current_plan_actual=$row['fl_current_plan'];
  $fg_plan_actual=$row['fg_plan'];

  
  
  
  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual=strtotime('+0 day',strtotime($fe_actual));
  $fe_actual= date('Y-m-d',$fe_actual);
 
  #Obtenemos el año actual
  $anio_actual=date ("Y"); 

  

  
  
  
  
  if($ya_tiene_plan>0){#si ya tiene asignado una licencia.


                        
                        if($fg_plan_actual==$fg_opcion_pago_elegido){#quiere decir que elifigio mismo plan que tenia antes.
                          
                                            /**
                                            * CASO PLAN MES.
                                            */ 
                                          if($fg_opcion_pago==1 ){#opcion de pago es  mes
                          
                                                               /**
                                                                * MJD
                                                                * 1ER CASO PLAN MES NO SE AGREGA LICENCIAS
                                                                * 
                                                                */ 
                          
                                                              if($no_usuario_adicional==0) {#Quiere decir que se renovara su plan que tiene.no se agregan licencias.

                                                                                /**
                                                                                 *MJD Talvez aqui faltaria saber cuantos dias hacen falta para determinar si es renovacion o no(se me paso por la mente pero por logica si no agrega licencias queire decir que es nuevo pago adelatado.)
                                                                                 * 
                                                                                 */ 
                                          
                                                                                  #Recuperamos la licencias actuales del usuario.
                                                                                  $Query="SELECT no_total_licencias,no_licencias_disponibles,fl_current_plan,fl_princing FROM k_current_plan WHERE fl_current_plan=$fl_current_plan_actual ";
                                                                                  $row=RecuperaValor($Query);
                                                                                  $no_licencia_actual=$row[0];
                                                                                  $no_licencias_disponibles_actual=$row[1];
                                                                                  $fl_current_plan_actual=$row[2];
                                                                                  $fl_princing_actual=$row['fl_princing'];

                                                                                  # 1. Recuperamos la fecha del ultimo mes a vencer,del plan, para sumarle 1 mes, que sera su nuevo plan creado.y que ya este pagado.
                                                                                  $Query="SELECT fe_periodo_final,mn_total,fl_current_plan FROM k_admin_pagos WHERE fl_current_plan=$fl_current_plan_actual  ORDER BY fl_admin_pagos DESC ";
                                                                                  $row=RecuperaValor($Query);
                                                                                  $fe_periodo_actual=$row['fe_periodo_final'];

                                          
                                                                                  #2. Se calcula su fecha de inicio y fecha final del nuevo plan (es decir a la fecha le sumaos un mes.)                                                            
                                                                                  $fe_inicio_periodo=$fe_periodo_actual;
                                                                                  $fe_final_periodo=strtotime('+1 month',strtotime($fe_inicio_periodo));
                                                                                  $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                                          
                                          
                                                                                  #3.Recuperamos la tarifa actual existente de la institucion  y que esta ligado al plan.
                                                                                  $Query="SELECT mn_mensual FROM c_princing WHERE fl_princing=$fl_princing_actual ";
                                                                                  $row=RecuperaValor($Query);
                                                                                  $mn_costo_mensual=$row['mn_mensual'];
                              
                                                          
                                                                                  #seRaliza el calculo para saber el costo:
                                                                                  $mn_mensual_total=$no_licencia_actual * $mn_costo_mensual ;
                                                          
                                                          
                                                                                  #4.Se gegera nuevo registro del plan.
                                                                                  $fg_pagado="0";
                                          
                                                                                  #se inserta el registro y costo por mes       
                                                                                  $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago)";
                                                                                  $Query.="VALUES($fl_current_plan_actual,$mn_mensual_total,'1','$fe_inicio_periodo','$fe_final_periodo','$fg_pagado', '') ";
                                                                                  $fl_adm_pagos=EjecutaInsert($Query);
                                                          
                                                          
                                                                                  #actualizamo la fecha de inicio de vigencia y fevha final de vigencia del plan.
                                                          
                                                                                  $Query="UPDATE k_current_plan SET fe_fin='$fe_final_periodo' ";
                                                                                  $Query.="WHERE fl_current_plan =$fl_current_plan_actual ";
                                                                                  EjecutaQuery($Query);
                                          
                              
                                                                                  $mn_monto_total_a_pagar=$mn_mensual_total;
                                                                                  
                                                                                  $fl_princing=$fl_princing_actual;
                                                          
                                                              }
                          
                                                            /**
                                                             * MJD 
                                                             * 2DO. CASO SE AGREGAN LICENCIAS MISMO PLAN MENSUAL.
                                                             */ 
                                                              #Otro caso Cuando se mantiene el mismo plan MES pero se gregan licencias.
                                                              if($no_usuario_adicional > 0){
                                          
                                          
                                          
                                          
                                                                          #Recuperamos la licencias actuales del usuario.
                                                                          $Query="SELECT no_total_licencias,no_licencias_disponibles,fl_current_plan,fl_princing FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
                                                                          $row=RecuperaValor($Query);
                                                                          $no_licencia_actual=$row[0];
                                                                          $no_licencias_disponibles_actual=$row[1];
                                                                          $fl_current_plan_actual=$row[2];
                                                                          $fl_princing_actual=$row['fl_princing'];
                                          
                                                                          #summos el no. de  licencias que tiene actualmente  + el numero nuevo agregado para obtener nuevo  No. de licencias total.
                                                                          $no_nuevo_licencias= $no_licencia_actual + $no_usuario_adicional;
                                                  
                                                                          #sumamos las licencias que se encuentran en  estado disponibles alas nuevas agregadas.
                                                                          $no_licencia_disponibles=$no_licencias_disponibles_actual+$no_usuario_adicional;
                                                  
                                                                          #Recuperamos costo que tine actualmente su plan 
                                                                          $Query="SELECT K.no_total_licencias,K.mn_total_plan,P.mn_mensual,P.mn_anual
                                                                                        FROM k_current_plan K 
                                                                                        JOIN c_princing P ON P.fl_princing=K.fl_princing
                                                            
                                                                                        WHERE fl_current_plan= $fl_current_plan_actual ";                                                            
                                                                          $row=RecuperaValor($Query);
                                                                          $no_total_licencias_actuales=$row['no_total_licencias'];
                                                                          $mn_monto_tarifa_actual_mensual=$row['mn_total_plan'];
                                                                          $mn_costo_mensual_actual=$row['mn_mensual'];                           
                                                

                                                
                                                                        #identificamos en que rango se encuentra,PARA SABER  NUEVO PLAN y nuevas tarifas.
                                                                        $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
                                                                        $rs = EjecutaQuery($Query);
                                                                        for($i=1;$row=RecuperaRegistro($rs);$i++){
            
                                                                            $mn_rango_ini= $row['no_ini'];
                                                                            $mn_rango_fin= $row['no_fin'];
            
                                                                            if(( $no_nuevo_licencias >=$mn_rango_ini)&&($no_nuevo_licencias<=$mn_rango_fin) ){
            
                                                                                $fl_nuevo_princing=$row['fl_princing'];
                                                                            }

                                                                        }

                                                                        #Recuperamos costos segun el plan obtenido del nuevo rango de licencias.
                                                                        $Query="SELECT mn_mensual,mn_anual FROM c_princing WHERE fl_princing=$fl_nuevo_princing ";
                                                                        $row=RecuperaValor($Query);
                                                                        $mn_costo_mensual=$row[0];
                                                
                                                
                                                                        #se obtiene tarifa total es decir costo mensual  por las licencias adquiridas (nuevo plan adquieirdo).
                                                                        $mn_costo_total_mensual_nuevo_plan=$mn_costo_mensual*$no_nuevo_licencias;
                                                
                                                                        #se_obtiene costo por dia del nuevo plan adquirido.entre 30 dias del mes.
                                                                        $mn_costo_por_dia_nueva_tarifa=$mn_costo_total_mensual_nuevo_plan/30;
                                                
                                                
                                                
                                                
                                                                        /**
                                                                         *MJD
                                                                         *CASO LAS LICENCIAS AGREGADAS ESTAN DENTRO DEL MISMO PLAN ACTUAL
                                                                         * 
                                                                         */ 

                                                                        //if($fl_nuevo_princing == $fl_princing_actual){#quiere decir que esta dentro del mismo plan que su plan anterior.

                                                    
                                                                                #1.Solo actualizamos licencias disponibles de su plan actual.
                                                                                $Query="UPDATE k_current_plan SET no_total_licencias=$no_nuevo_licencias,no_licencias_disponibles=$no_licencia_disponibles,mn_total_plan=$mn_costo_total_mensual_nuevo_plan ";
                                                                                $Query.="WHERE fl_current_plan=$fl_current_plan_actual ";
                                                                                EjecutaQuery($Query); 

                                                                                
                                                                                #2. Obtenemos fecha inicio y fin de vigencia del plan actual existente.                                                 
                                                                                $Query="SELECT fe_periodo_inicial, fe_periodo_final FROM k_current_plan WHERE fl_current_plan=$fl_current_plan_actual  ";
                                                                                $row=RecuperaValor($Query);
                                                                                $fe_inicio_vigencia=$row['fe_periodo_inicial'];
                                                                                $fe_final_vigencia=$row['fe_periodo_final'];
                                                                                
                                                                                
                                                                                $fe_actual="2016-11-25";
                                                                                
                                                                                
                                                                                #Obtenemos el costo por dia de la tarifa que tiene actualmente el istituto
                                                                                $mn_costo_por_dia_tarifa_actual= $mn_monto_tarifa_actual_mensual/30;
                                                                                
                                                                                #Obtengo dias que faltan para culminar mi plan actual.
                                                                                $no_dias_faltan_terminar_plan=ObtenDiasRestantesPlan($fe_final_vigencia,$fe_actual);
                                                                                
                                                                                
                                                                                #Calculamos mi credito que tengo restantes para finalizar el plan actual.
                                                                                $mn_credito_actual=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_tarifa_actual;
                                                                                
                                                                                #Calculamos el credito del nuevo plan adquirido.
                                                                                $mn_credito_actual_nuevo_adquirido=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_nueva_tarifa;

                                                                                
                                                                                
                                                                                #realizamos la resta costo del nuevo plan  y le restamos nuestro credtio disponible para saber cuanto es la diferencia apagar.
                                                                                
                                                                                $mn_monto_total_a_pagar=$mn_credito_actual_nuevo_adquirido-$mn_credito_actual;
                                                                                
                                                                               
                                                                                /**
                                                                                 *Insertamos bitacora de todos los pagos. y u descripcion de quse se agregaron 8 licencias mas.
                                                                                 * 
                                                                                 */ 
                                                                                 
                                                                                 $ds_descripcion=$no_usuario_adicional." ".ObtenEtiqueta(1553) ;
                                                                                 
                                                                                
                                                                                 #1.Recuperamos la fecha del ultimo mes a vencer,del plan, para sumarle 1 mes, que sera su nuevo plan creado.y que ya este pagado.    
                                                                                 $Query="SELECT fe_periodo_final,mn_total,fl_current_plan FROM k_admin_pagos WHERE fl_current_plan=$fl_current_plan_actual  ORDER BY fl_admin_pagos DESC ";
                                                                                 $row=RecuperaValor($Query);
                                                                                 $fe_periodo_final_vencer=$row['fe_periodo_final'];
                                                                                 
                                                                                 
                                                                                 #2. Se calcula su fecha de inicio del nuevo plan (es decir a la fecha le sumaos un dia que es apartir del cual empezara su plan.)                                                            
                                                                                 $fe_inicio_periodo =$fe_periodo_final_vencer;
                                                                                 $fe_inicio_periodo =strtotime('+1 day',strtotime($fe_inicio_periodo));
                                                                                 $fe_inicio_vigencia = date('Y-m-d',$fe_inicio_periodo);
                                                                                 
                                                                                 
                                                                                 #2. Se calcula su fecha  final del nuevo plan (es decir a la fecha le sumaos un mes.)                                                            
                                                                                
                                                                                 $fe_final_periodo=strtotime('+1 month',strtotime($fe_inicio_vigencia));
                                                                                 $fe_final_vigencia= date('Y-m-d',$fe_final_periodo);
                                                                                 
                                                                                 
                                                                                 
                                                                                
                                                                                 
                                                                                $fg_pagado='0';
                                                                                #se inserta el registro y costo por mes       
                                                                                $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago, ds_descripcion)";
                                                                                $Query.="VALUES($fl_current_plan_actual,$mn_monto_total_a_pagar,'1','$fe_inicio_vigencia','$fe_final_vigencia','$fg_pagado','','$ds_descripcion') ";
                                                                                $fl_adm_pagos=EjecutaInsert($Query);
                                                                                
                                                                                
                                                                                
                                                                                //#Actualizamos plan actual 
                                                                                $Query="UPDATE k_current_plan SET mn_total_plan=$mn_costo_total_mensual_nuevo_plan,fl_princing=$fl_nuevo_princing ";
                                                                                $Query.="WHERE fl_current_plan=$fl_current_plan_actual ";
                                                                                EjecutaQuery($Query);
                                                                                
                                                                                
                                                                        //}
                                                                        /**
                                                                         * MJD
                                                                         * 4TO CASO LAS LICENCIAS AGREGADAS PROVOCA QUE CAMBIE  DE PLAN 
                                                                         * 
                                                                         */ 
                                                                        //if($fl_nuevo_princing <> $fl_princing_actual){ #quiere decir que cambio de plan causa del No. de licencias agregadas, entonces cambia el costo.
                                                                                
                                                                                        ////# 1. actualizamos licencias disponibles y no. total de licencias.
                                                                                        //$Query="UPDATE k_current_plan SET no_total_licencias=$no_nuevo_licencias,no_licencias_disponibles=$no_licencia_disponibles ";
                                                                                        //$Query.="WHERE fl_current_plan=$fl_current_plan_actual ";
                                                                                        //EjecutaQuery($Query);
                                                    
                                                                                        //#2. Obtenemos fecha inicio y fin de vigencia del plan actual existente.                                                 
                                                                                        //$Query="SELECT fe_periodo_inicial, fe_periodo_final FROM k_current_plan WHERE fl_current_plan=$fl_current_plan_actual  ";
                                                                                        //$row=RecuperaValor($Query);
                                                                                        //$fe_inicio_vigencia=$row['fe_periodo_inicial'];
                                                                                        //$fe_final_vigencia=$row['fe_periodo_final'];
                                                    
                                                                
                                                                                        ////$fe_inicio_vigencia="2016-11-02";
                                                                                        ////$fe_final_vigencia="2016-12-02";
                                                                
                                                                
                                                                                        //#Obtenemos el costo diario del plan actual.
                                                                                        //#Obtenemos el costo por dia de la tarifa que tiene actualmente el istituto
                                                                                        //$mn_costo_por_dia_simple= $mn_monto_tarifa_actual_mensual/30;

                                                                                        //#Obtengo dias que faltan para culminar mi plan actual.
                                                                                        //$no_dias_faltan_terminar_plan=ObtenDiasRestantesPlan($fe_final_vigencia,$fe_actual);
                                                                
                                                                
                                                                                        //#Calculamos cuanto es el costo por esos dias restantes para finalizar el plan actual.
                                                                                        //$mn_credito_actual=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_simple;
                                                                
                                                                                        //#Calculamos el costo del nuevo plan adquirido.
                                                                                        //$mn_credito_actual_nuevo_adquirido=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_nueva_tarifa;

                                                                                        //#realizamos la resta costo del nuevo plan  y le restamos nuestro credtio disponible para saber cuanto es la diferencia apagar.
                                                                
                                                                                        //$mn_monto_total_a_pagar=$mn_credito_actual_nuevo_adquirido - $mn_credito_actual ;
                                                                
                                                    
                                                                                        /**
                                                                                         *Falta definir en que momento se gregarar nuevo registro a pagar. 
                                                                                         * 
                                                                                         */ 

                                                                                        /*
                                                                                        #se genera la bitacora de pagos
                                                                                       // $Query ="INSERT INTO k_plan_pagos (fl_usuario,mn_total_pago,fg_plan,no_total_licencias,fe_inicio_plan,fe_final_plan,fl_)";

                                                                                        if($no_dias_faltan_terminar_plan==0) { #se agrega nuevo registro con estatus de pago pendiente
                                                                
                                                                            
                                                                    
                                                                                                    #2. Se calcula su fecha de inicio y fecha final del nuevo plan (es decir a la fecha le sumamos un mes.)                                                            
                                                                                                    $fe_inicio_periodo=$fe_final_vigencia;
                                                                                                    $fe_final_periodo=strtotime('+1 month',strtotime($fe_inicio_periodo));
                                                                                                    $fe_final_periodo= date('Y-m-d',$fe_final_periodo);

                                                                                                    #Se gegera nuevo registro del plan.
                                                                                                    $fg_pagado="0";
                                                                    
                                                                                                    #se inserta el registro y costo por mes.       
                                                                                                    $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago)";
                                                                                                    $Query.="VALUES($fl_current_plan_actual,$mn_costo_total_mensual_nuevo_plan,'1','$fe_inicio_periodo','$fe_final_periodo','$fg_pagado', '') ";
                                                                                                    $fl_adm_pagos=EjecutaInsert($Query);
                                                                    
                                                                                                    #Actualizamos plan actual 
                                                                                                    $Query="UPDATE k_current_plan SET mn_total_plan=$mn_costo_total_mensual_nuevo_plan,fe_inicio='$fe_inicio_periodo',fe_fin='$fe_final_vigencia', fl_princing=$fl_nuevo_princing ";
                                                                                                    $Query.="WHERE fl_current_plan=$fl_current_plan_actual ";
                                                                                                    EjecutaQuery($Query);
                                                                            
      
                                                                
                                                                                        }
                                                                
                                                                                         if($no_dias_faltan_terminar_plan>0){# se actualiza plan actual nada mas
                                                               
                                                                     
                                                                                                 #Actualizamos plan actual 
                                                                                                 $Query="UPDATE k_current_plan SET mn_total_plan=$mn_costo_total_mensual_nuevo_plan,fl_princing=$fl_nuevo_princing ";
                                                                                                 $Query.="WHERE fl_current_plan=$fl_current_plan_actual ";
                                                                                                 EjecutaQuery($Query);

                                                               
                                                                                         }
                                                                
                                                                                        */
                                                                                        $fl_princing=$fl_nuevo_princing;
                                                                 
                                                    
                                                                        //}#end $fl_nuevo_princing <> $fl_princing_actual
                                                
                                         
                                                              }#end $no_usuario_adicional > 0
                                      
                                   }#end pago mes =eligio mismo pago_mes
                                   
                                          
                                      
                                 /**
                                  *MJD OPCION PAGO ES AÑO
                                  * 
                                  */ 
                                          
                                 if($fg_opcion_pago==2){
                                     
                                            $fg_tipo_pago="A";
                                            
                                           if($no_usuario_adicional==0) {#Quiere decir que se renovara su plan que tiene.no se agregan licencias.
                                     
                                               #Recuperamos la licencias actuales del usuario.
                                               $Query="SELECT no_total_licencias,no_licencias_disponibles,fl_current_plan,fl_princing FROM k_current_plan WHERE fl_current_plan=$fl_current_plan_actual ";
                                               $row=RecuperaValor($Query);
                                               $no_licencia_actual=$row[0];
                                               $no_licencias_disponibles_actual=$row[1];
                                               $fl_current_plan_actual=$row[2];
                                               $fl_princing_actual=$row['fl_princing'];
                                               
                                               # 1. Recuperamos la fecha del ultimo mes a vencer,del plan, para sumarle 1 mes, que sera su nuevo plan creado.y que ya este pagado.
                                               $Query="SELECT fe_periodo_final,mn_total,fl_current_plan FROM k_admin_pagos WHERE fl_current_plan=$fl_current_plan_actual  ORDER BY fl_admin_pagos DESC ";
                                               $row=RecuperaValor($Query);
                                               $fe_periodo_actual=$row['fe_periodo_final'];
                                               
                                               #2. Se calcula su fecha de inicio y fecha final del nuevo plan (es decir a la fecha le sumaos un mes.)                                                            
                                               $fe_inicio_periodo=$fe_periodo_actual;
                                               $fe_final_periodo=strtotime('+1 year',strtotime($fe_inicio_periodo));
                                               $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                                               
                                               #3.Recuperamos la tarifa actual existente de la institucion  y que esta ligado al plan.
                                               $Query="SELECT mn_anual FROM c_princing WHERE fl_princing=$fl_princing_actual ";
                                               $row=RecuperaValor($Query);
                                               $mn_costo_anual=$row['mn_anual'];
                                               
                                               #seRaliza el calculo para saber el costo:
                                               $mn_anual_total=$no_licencia_actual * $mn_costo_anual ;
                                               
                                               #4.Se gegera nuevo registro del plan.
                                               $fg_pagado="0";
                                               
                                               #se inserta el registro y costo por anio.      
                                               $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago)";
                                               $Query.="VALUES($fl_current_plan_actual,$mn_anual_total,'1','$fe_inicio_periodo','$fe_final_periodo','$fg_pagado', '') ";
                                               $fl_adm_pagos=EjecutaInsert($Query);
                                               
                                               #actualizamo la fecha de inicio de vigencia y fevha final de vigencia del plan.  
                                               $Query="UPDATE k_current_plan SET fe_fin='$fe_final_periodo' ";
                                               $Query.="WHERE fl_current_plan =$fl_current_plan_actual ";
                                               EjecutaQuery($Query);
                                               
                                               $mn_monto_total_a_pagar=$mn_anual_total;
                                               
                                           }
                                     
                                          if($no_usuario_adicional > 0){#quiere decir que se agregan licencias
                                              
                                                      #Recuperamos la licencias actuales del usuario.
                                                      $Query="SELECT no_total_licencias,no_licencias_disponibles,fe_periodo_final,fl_current_plan,fl_princing 
                                                      FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
                                                      $row=RecuperaValor($Query);
                                                      $no_licencia_actual=$row[0];#no. de licencias que tiene actualmente
                                                      $no_licencias_disponibles_actual=$row[1];
                                                      $fl_current_plan_actual=$row['fl_current_plan'];
                                                      $fl_princing_actual=$row['fl_princing'];
                                                      $fe_periodo_final_vigencia=$row['fe_periodo_final'];
                                              
                                                      
                                                      #summos el no. de  licencias que tiene actualmente  + el numero nuevo agregado para obtener nuevo  No. de licencias total.
                                                      $no_nuevo_licencias= $no_licencia_actual + $no_usuario_adicional;
                                              
                                                      #sumamos las licencias que se encuentran en  estado disponibles alas nuevas agregadas.
                                                      $no_licencia_disponibles=$no_licencias_disponibles_actual+$no_usuario_adicional;
                                              
                                                      #Recuperamos costo que tine atualmente su plan 
                                                      $Query="SELECT K.no_total_licencias,K.mn_total_plan,P.mn_mensual,P.mn_anual
                                                                                                FROM k_current_plan K 
                                                                                                JOIN c_princing P ON P.fl_princing=K.fl_princing
                                                            
                                                                                                WHERE fl_current_plan= $fl_current_plan_actual ";                                                            
                                                      $row=RecuperaValor($Query);
                                                      $no_total_licencias_actuales=$row['no_total_licencias'];
                                                      $mn_monto_tarifa_actual_anual=$row['mn_total_plan'];
                                                      $mn_costo_anual_actual=$row['mn_anual'];                           
                                              

                                                      
                                                      
                                              
                                                      #identificamos en que rango se encuentra,PARA SABER  NUEVO PLAN y nuevas tarifas.
                                                      $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
                                                      $rs = EjecutaQuery($Query);
                                                      for($i=1;$row=RecuperaRegistro($rs);$i++){
                                                  
                                                          $mn_rango_ini= $row['no_ini'];
                                                          $mn_rango_fin= $row['no_fin'];
                                                  
                                                          if(( $no_nuevo_licencias >=$mn_rango_ini)&&($no_nuevo_licencias<=$mn_rango_fin) ){
                                                      
                                                              $fl_nuevo_princing=$row['fl_princing'];
                                                          }

                                                      }

                                                      #Recuperamos costos segun el plan obtenido del nuevo rango de licencias.
                                                      $Query="SELECT mn_mensual,mn_anual FROM c_princing WHERE fl_princing=$fl_nuevo_princing ";
                                                      $row=RecuperaValor($Query);
                                                      $mn_costo_mensual=$row[0];
                                                      $mn_costo_anual=$row['mn_anual'];
                                              
                                                      
                                                      #actualizamos las licencias del instituto actual:
                                                      $fe_actual="2017-11-15";
                                                      
                                                      
                                                      
                                                      #se obtiene tarifa total es decir  costo mensual apagar por las licencias adquiridas (nuevo plan adquieirdo).
                                                      $mn_costo_total_anual_nuevo_plan=$mn_costo_anual*$no_nuevo_licencias;
                                                      
                                                      #se obtiene costo por mes de la nueva tarifa con nueva numero de licencis
                                                      $mn_costo_mensual_nueva_tarifa=$mn_costo_anual * $no_nuevo_licencias / 12;

                                                      #se_obtiene costo por dia del nuevo plan adquirido.
                                                      //$mn_costo_por_dia_nueva_tarifa=$mn_costo_mensual_nueva_tarifa/30;
                                                      $mn_costo_por_dia_nueva_tarifa=$mn_costo_anual* $no_nuevo_licencias / 365;
                                                      
                                                      
                                                      #Recuperamos los creditos que tenemos actualmente
                                                      
                                                      #Obtenemos el costo por dia de la tarifa que tiene actualmente el istituto
                                                      $mn_costo_por_dia_tarifa_actual= $mn_monto_tarifa_actual_anual/365;
                                                      
                                                      #Obtengo dias que faltan para culminar mi plan actual.
                                                      $no_dias_faltan_terminar_plan=ObtenDiasRestantesPlan($fe_periodo_final_vigencia,$fe_actual);
                                                      
                                                      
                                                      #Calculamos mi credito que tengo restantes para finalizar el plan actual.
                                                      $mn_credito_actual=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_tarifa_actual;
                                                      
                                                      #Calculamos el credito del nuevo plan adquirido.
                                                      $mn_credito_actual_nuevo_adquirido=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_nueva_tarifa;

                                                      
                                                      
                                                      #realizamos la resta costo del nuevo plan  y le restamos nuestro credtio disponible para saber cuanto es la diferencia apagar.
                                                      
                                                      $mn_monto_total_a_pagar=$mn_credito_actual_nuevo_adquirido-$mn_credito_actual;
                                                      
                                                      
                                                      #Actualizamos nuevo plan adquirido
                                                      $Query="UPDATE k_current_plan SET mn_total_plan=$mn_costo_total_anual_nuevo_plan,fl_princing=$fl_nuevo_princing,no_total_licencias=$no_nuevo_licencias,no_licencias_disponibles=$no_licencia_disponibles 
                                                      WHERE fl_current_plan=$fl_current_plan_actual";                           
                                                      EjecutaQuery($Query);
                                                      
                                                      $Query="UPDATE k_admin_pagos SET mn_total=$mn_costo_mensual_nueva_tarifa WHERE fl_current_plan=$fl_current_plan_actual AND fg_pagado='0' ";
                                                      EjecutaQuery($Query);
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                              
                                          }
                                            
                                          
                                          if( $no_usuario_adicional < 0 ){#Quiere decir que le vamos aquitar las licencias.
                                              
                                                  //#Recuperamos la licencias actuales del usuario.
                                                  //$Query="SELECT no_total_licencias,no_licencias_disponibles,fe_periodo_final,fl_current_plan,fl_princing 
                                                  //        FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
                                                  //$row=RecuperaValor($Query);
                                                  //$no_licencia_actual=$row[0];
                                                  //$no_licencias_disponibles_actual=$row[1];
                                                  //$fl_current_plan_actual=$row['fl_current_plan'];
                                                  //$fl_princing_actual=$row['fl_princing'];
                                                  //$fe_periodo_final_vigencia=$row['fe_periodo_final'];
                                                  
                                                  
                                                  
                                              
                                              
                                          
                                          
                                          }
                                 
                                             

                                            

                                     
                                            
                                     
                                     
                                             
                                     
                                     
                                             
                                     
                                     
                                             
                                     
                                             
                                     
                                     
                                             
                                     
                                     
                                            
                                 
                                 
                                 
                                 }         
                                          
                                          
                                          
                                          
                                          
                                          
                                          
                                          
          
          
                      }#end mismo plan
      

      
                    if($fg_opcion_pago==2){#año
          
                        $fg_tipo_pago="A";
          
          
                        for ($i=1;$i<=12;$i++){#ciclo que comprende los 12 meses
              
                            #calculamos la fecha final del periodo por mes(es decir a la fecha le sumaos un mes.)
                            $fe_final_periodo=strtotime('+1 month',strtotime($fe_periodo_actual));
                            $fe_final_periodo= date('Y-m-d',$fe_final_periodo);

                            $fg_pagado="1";
              
              
                            #se inserta el registro y costo por mesen un plan anual       
                            $Query="INSERT INTO k_admin_pagos (fl_current_plan,fe_periodo,mn_total,fg_publicar,fg_pagado,fe_pagado)";
                            $Query.="VALUES($fl_current_plan_actual,'$fe_final_periodo',$mn_costo_mensual,'1','$fg_pagado', CURRENT_TIMESTAMP) ";
                            $fl_adm_pagos=EjecutaInsert($Query);
              
                            
                            #se inserta el registro y costo por mes.      
                          //  $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago)";
                          //  $Query.="VALUES($fl_current_plan_actual,$mn_anual_total,'1','$fe_inicio_periodo','$fe_final_periodo','$fg_pagado', '') ";
                          //  $fl_adm_pagos=EjecutaInsert($Query);
                            
                            
                            
                            
                            #se renombra la fecha actual
                            $fe_periodo_actual=$fe_final_periodo;
                        }
          
    
                    }

      #si efectivamente existe un susario y un plan entonces actualizamos registro, plan , total disponivbles.
     // $Query="UPDATE k_current_plan SET fg_plan='$fg_tipo_pago',no_total_licencias=$no_nuevo_licencias,no_licencias_disponibles=$no_licencia_disponibles,fl_princing=$fl_plan WHERE fl_usuario=$fl_usuario ";
     // EjecutaQuery($Query);
    
   
      
      
      
  
  }else{ #si es nuevo registro de licencias
      
      
              
            #Recuperamos los usuarios actuales y le sumamos lo del spinner.
            $no_user_actuales=ObtenNumeroUserInst($fl_instituto);
            $no_usuario_adicional=$no_user_actuales + $no_usuario_adicional;
      
      
      
      
              #identificamos en que rango se encuentra, e identificamos el tipo de plan.
              $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
              $rs = EjecutaQuery($Query);
              for($i=1;$row=RecuperaRegistro($rs);$i++) {
          
                  $mn_rango_ini= $row['no_ini'];
                  $mn_rango_fin= $row['no_fin'];
          
                  if(( $no_usuario_adicional >=$mn_rango_ini)&&($no_usuario_adicional<=$mn_rango_fin) ){
              
                      $fl_plan=$row['fl_princing'];
                     
                  }

              }
      
      
              #Recuperamos costos segun el plan .
              $Query="SELECT mn_mensual,mn_anual FROM c_princing WHERE fl_princing=$fl_plan";
              $row=RecuperaValor($Query);
              $mn_costo_mensual=$row[0];
              $mn_costo_anual=$row[1];
              
      
            
          if($fg_opcion_pago==1)#mes
          {
                  $fg_tipo_pago="M";
                  #se calcula fecha de termino del plan por mes.
                  $fe_final_periodo=strtotime('+1 month',strtotime($fe_actual));
                  $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
              
                  
                  $no_licencias_usadas=ObtenNumeroUserInst($fl_instituto);
                  $no_licencias_disponibles=$no_usuario_adicional-$no_licencias_usadas;
                  
                  
                  #se genera el plan  
                  $Query="INSERT INTO k_current_plan  (fl_instituto,fl_princing,fg_plan,no_total_licencias,no_licencias_disponibles,no_licencias_usadas,no_total_storage,fg_estatus,fe_periodo_inicial,fe_periodo_final )  ";
                  $Query.="VALUES ($fl_instituto,$fl_plan,'$fg_tipo_pago',$no_usuario_adicional,$no_licencias_disponibles,$no_licencias_usadas,'','A','$fe_actual','$fe_final_periodo')";
                  $fl_current_plan=EjecutaInsert($Query);
                  
                  
                  
                  #Se calcula el costo total    #no_licencias * el costo
                  $mn_mensual_total=$no_usuario_adicional * $mn_costo_mensual ;
                  
                  
                  $ds_descripcion=ObtenEtiqueta(1554)." ".$no_usuario_adicional." licences";
                  $fg_pagado="1";#este registro se actualizara cuando el ago se haya realizado y se vea reflejado.y tambie se actualizara su fecha de pago.
                  #se inserta el registro y costo por mes       
                  $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion)";
                  $Query.="VALUES($fl_current_plan,$mn_mensual_total,'1','$fe_actual','$fe_final_periodo','$fg_pagado','','$ds_descripcion') ";
                  $fl_adm_pagos=EjecutaInsert($Query);

                  $mn_monto_total_a_pagar =$mn_mensual_total;

                  $Query="UPDATE k_current_plan SET mn_total_plan=$mn_mensual_total WHERE fl_current_plan =$fl_current_plan ";
                  EjecutaQuery($Query);
                  
                  $no_licencias_total=$no_usuario_adicional;
                  
                  $fl_princing=$fl_plan;
                  
                  
                  
                  
          }
          if($fg_opcion_pago==2)#año
          {
              
                  $fg_tipo_pago="A";
          
                  #se calcula fecha de termino del plan por año.
                  $fe_final_periodo=strtotime('+1 year',strtotime($fe_actual));
                  $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
 

                  #se genera el plan  
                  $Query="INSERT INTO k_current_plan  (fl_instituto,fl_princing,fg_plan,no_total_licencias,no_licencias_disponibles,no_licencias_usadas,no_total_storage,fg_estatus,fe_periodo_inicial,fe_periodo_final )  ";
                  $Query.="VALUES ($fl_instituto,$fl_plan,'$fg_tipo_pago',$no_usuario_adicional,$no_usuario_adicional,'','', 'A', '$fe_actual','$fe_final_periodo')";
                  $fl_current_plan=EjecutaInsert($Query);
              
              
                  #se calcula el monto mensual a pagar .  mn_menual * No_licencias_contratado / 12 meses.   
                  $mn_costo_total_anual= $mn_costo_anual * $no_usuario_adicional;
                  
                  #Obtenemos el AÑO actual
                  $anio_actual=date ("y"); 
                  
                  
                  $fg_pagado="1";#este registro se actualizara , se lo dejo en 1 para hacer pruebas cuando el ago se haya realizado y se vea reflejado.y tambie se actualizara su fecha de pago.
                  
                  
                  //#se inserta el registro y costo por mes       
                  //$Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago)";
                  //$Query.="VALUES($fl_current_plan,$mn_costo_total_anual,'1','$fe_actual','$fe_final_periodo','$fg_pagado','') ";
                  //$fl_adm_pagos=EjecutaInsert($Query);
                  
                  $mn_monto_total_a_pagar =$mn_costo_total_anual;
                  $no_licencias_total=$no_usuario_adicional;
                  $fl_princing=$fl_plan;
                  
                  
                  $etq_mes = "";
                  
                  $mn_costo_mensual=$no_licencias_total * $mn_costo_anual / 12;
                  
                  $contador=0;
                  for ($i=1;$i<=12;$i++){#ciclo que comprende los 12 meses.
                      
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
                      
                      
                    
                     // if($contador==12){#actulizamos la fecha de terminacion del plan si actualizamos esto se recorreuna semana. ya no cuadra
                       //   $Query="UPDATE k_current_plan SET fe_periodo_final='$fe_final_periodo' WHERE fl_current_plan =$fl_current_plan ";
                       //   EjecutaQuery($Query);
                          
                    //  }
                       
                      
                      
            
                        /**
                         * se lo quimtamos sino la cuen no va asi
                         * ala fecha final le sumamos un dia por que apartir de ahi se cuenta el siguiente mes.
                         */ 
                     // $fe_final_periodo=strtotime('+1 day',strtotime($fe_final_periodo));
                     // $fe_final_periodo=date('Y-m-d',$fe_final_periodo);
                      /**
                       * 
                       */ 
                      
                      
                      
                      
                      #se renombra la fecha actual, la fecha final del periodo se convierte en mes inicial y esto para ir agregando y sumando mes por mes.
                      $fe_actual=$fe_final_periodo;
                      
                     
                  }
                  
                  
                  $no_licencias_usadas=ObtenNumeroUserInst($fl_instituto);
                  $no_licencias_disponibles=$no_usuario_adicional-$no_licencias_usadas;
                      
                  
                  
                  $Query="UPDATE k_current_plan SET mn_total_plan=$mn_costo_total_anual,no_licencias_usadas=$no_licencias_usadas,no_licencias_disponibles=$no_licencias_disponibles  WHERE fl_current_plan =$fl_current_plan ";
                  EjecutaQuery($Query);
                  
                  
                  
                  
                  
                  
          
          }
      
             
      
          
          
          
        
      
        
      
      
      
      
  }
  
  
   #Actualizamos registro del instituto y le decimos que el instituto ya tiene un plan,entonces pasa de modo trial  a Member.
   $Query="UPDATE c_instituto SET fg_tiene_plan='1'  WHERE fl_instituto=$fl_instituto ";
   EjecutaQuery($Query);
  
  
  
  
  
  echo"<script>
  PresentaPlanActual();
  </script>";
  echo"<input type='hidden' value='$mn_monto_total_a_pagar' name='mn_total_pagar' id='mn_total_pagar' >
       <input type='hidden' value='$no_licencias_total' name='no_usuario_adicionales' id='no_usuario_adicionales' >
       <input type='hidden' value='$fg_tipo_pago' name='fg_tipo_plan' id='fg_tipo_plan' >
       <input type='hidden' value='$fl_current_plan' name='fl_current_plan' id='fl_current_plan' >
        <input type='hidden' value='$fl_princing' name='fl_princing' id='fl_princing' >
  ";
  

                                     
?>


<script>

    function PresentaStriipe() {

        var mn_total_pagar = document.getElementById('mn_total_pagar').value;
        var no_usuario_adicionales = document.getElementById('no_usuario_adicionales').value;//no_licencias actuales.
        var fg_tipo_plan = document.getElementById('fg_tipo_plan').value;
        var fl_princing = document.getElementById('fl_princing').value;

        $.ajax({
            type: 'POST',
            url: 'site/presenta_metodo_pago.php',
            data: 'mn_total_pagar=' + mn_total_pagar +
                 '&fg_tipo_plan=' + fg_tipo_plan +

                 '&fl_princing=' + fl_princing +
                  '&no_usuario_adicionales=' + no_usuario_adicionales,

            async: true,
            success: function (html) {
                $('#presenta_strippe').html(html);
            }
        });


    }

 </script>

<script>

    PresentaStriipe();
</script>


