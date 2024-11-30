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
  $fg_plan=RecibeParametroNumerico('fg_option');
  $no_usuario_adicional=RecibeParametroNumerico('no_usuario_adicional');
  $no_total_licencias=RecibeParametroNumerico('no_total_licencias');
  
  
  $fg_plan_actual=ObtenPlanActualInstituto($fl_instituto);
  
  
  
  
  #Se suma las licencias actuales con los agregados pra asaber la nueva tarifa correspondiete.
  $mn_total_licencias_actuales= $no_total_licencias + $no_usuario_adicional ;
  
  

  if(empty($no_total_licencias)) {
      
            $no_total_licencias=ObtenNumeroUserInst($fl_instituto);
      
      $mn_total_licencias_actuales=$no_total_licencias+$no_usuario_adicional;
  

  }
 
  
  
  
                                            #Recuperamos la tabla de precios:
                                            $Query="SELECT no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto  ORDER BY fl_princing ASC ";
                                            $rs = EjecutaQuery($Query);
                                            $tot_registros = CuentaRegistros($rs);

                                            $contador_tr=0;
                                            for($i=1;$row=RecuperaRegistro($rs);$i++){
                                                 $mn_rango_ini= $row['no_ini'];
                                                 $mn_rango_fin= $row['no_fin'];
                                                 $mn_descuento_mensual= $row['ds_descuento_mensual'];
                                                 $mn_mensual= $row['mn_mensual'];
                                                 $mn_descuento_anual= $row['ds_descuento_mensual'];
                                                 $mn_anual= $row['mn_anual'];
                                                 $mn_descuento_licencia=$row['mn_descuento_licencia'];
                                                 $fg_activo=$row['fg_activo'];
                                                 
                                                 $contador_tr ++;
                                           
                                                 $id_tr="tr_".$contador_tr;
                                            
                                                 
                                                 
                                                 if(($mn_total_licencias_actuales>=100)&&($fg_activo<>1)){
                                                    $fg_plan=2;
                                                    $fg_puede_seleccionar_mes=false;
                                                 }else{
                                                     $fg_puede_seleccionar_mes=true;

                                                 }
                                                 
                                                 
                                                 
                                                 
                                                 
                                                  if(( $mn_total_licencias_actuales >=$mn_rango_ini)&&($mn_total_licencias_actuales<=$mn_rango_fin) ){

                                                      
                                                      
                                                         
                                                      
                                                      
                                                      
                                                      
                                                      
                                                                if($fg_plan==1){##mes
                                                                    
                                                                    
                                                                    if($contador_tr==1){
                                                                        
                                                                        if($mn_descuento_licencia==0)
                                                                            $base_price="<small style='color:#fff; font-size:12px !important;'><i>".ObtenEtiqueta(1752)."</i></small>";
                                                                        
                                                                    }else{
                                                                        $base_price="<small style='color:#fff; font-size:12px !important;'><i>(".number_format($mn_descuento_licencia)."% ".ObtenEtiqueta(1751).") </i></small>   ";
                                                                    }
                                                                    
                                                                    

                                                                    $ini_label_mes="<span class='label label-success parpadea' style='background-color:#0071BD;'>";
                                                                    if($fg_activo)
                                                                    $fin_label_mes="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$ ".$mn_mensual." &nbsp; $base_price";
                                                                    else
                                                                    $fin_label_mes="";    
                                                                    $fin_label_mes.="</span>";
                                                                    
                                                                    $ini_label_anual=" ";
                                                                    $fin_label_anual=" $".$mn_anual." <small style='color:#999; font-size:12px !important;'><i>(".number_format($mn_descuento_anual)."%) </i></small>";
                                                                    
                                                                    
                                                                }else{
                                                                    
                                                                    
                                                                    if($contador_tr==1){
                                                                        
                                                                        if($mn_descuento_licencia==0)
                                                                            $base_price="<small style='color:#999; font-size:12px !important;'><i>".ObtenEtiqueta(1752)."</i></small>";
                                                                        
                                                                    }else{
                                                                        $base_price="<small style='color:#999; font-size:12px !important;'><i>(".number_format($mn_descuento_licencia)."%)</i></small>   ";
                                                                    }

                                                                    
                                                                    $ini_label_mes=" ";
                                                                    if($fg_activo)
                                                                    $fin_label_mes=" $".$mn_mensual." $base_price";
                                                                    else
                                                                    $fin_label_mes="";    
                                                                    
                                                                    $ini_label_anual="<span class='label label-success parpadea'style='background-color:#0071BD;'>";
                                                                    $fin_label_anual="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $ ".$mn_anual." &nbsp;<small style='color:#fff; font-size:12px !important;'><i>(".number_format($mn_descuento_anual)."% ".ObtenEtiqueta(1751).") </i></small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>";
                                                                
                                                                    
                                                                }
                                                                
                                                                
                                                                 
                                                                
                                                                 
                                                  }else{
                                                  
                                                      
                                                      
                                                          if($contador_tr==1){
                                                          
                                                              if($mn_descuento_licencia==0)
                                                                  $base_price="<small style='color:#999; font-size:12px !important;'><i>".ObtenEtiqueta(1752)."</i></small>";
                                                          
                                                          }else{
                                                              $base_price="<small style='color:#999; font-size:12px !important;'><i>(".number_format($mn_descuento_licencia)."%)</i></small>   ";
                                                          }
                                                      
                                                      
                                                      
                                                   
                                                      
                                                           $ini_label_mes="";
                                                           if($fg_activo)
                                                           $fin_label_mes="$".$mn_mensual." $base_price";
                                                           else
                                                           $fin_label_mes="";    
                                                    
                                                      
                                                          $ini_label_anual=" ";
                                                          $fin_label_anual="$".$mn_anual." <small style='color:#999;font-size:12px !important;'><i>(".number_format($mn_descuento_anual)."%)</i></small>";
                                                     
                                                  }
                                                 
                                                 
                                                  
                                                  
                                                  
                                                  
                                                  
                                              echo"
                                              
                                              
                                              
                                                     <tr style='margin-top:1px;border:none;' id='presenta_lista_precios'>
                                                        <td class='text-center' style='border:none;'>".number_format($mn_rango_ini)." - ".number_format($mn_rango_fin)." </td>
                                                        <td class='text-center' style='border:none;'>$ini_label_mes $fin_label_mes  &nbsp;</td>
                                                        <td class='text-center' style='border:none;'>$ini_label_anual $fin_label_anual &nbsp;</td>
                                                     </tr>
                                              
                                              ";
                                                 
                                                 
                                                 
                                              
                                              
                                              #se formatea el label para que ya no aparesca.
                                              $ini_label=" ";
                                              $fin_label=" ";
                                                 
                                                 
                                            }              
                                                 
  if($mn_total_licencias_actuales>=100){
       
      
          if($fg_plan_actual){

             


                echo"<script>
                      $('#optionsRadios3').prop('checked', false); 
                      $('#optionsRadios4').prop('checked', true); 
                   </script>"; 
       
          }else{
                
                  if($fg_puede_seleccionar_mes){#Si en backen se puede elegir mes, entonces no hara nada
                  
                  }else{
                            ##se bloquea pocion de elegir mes.
                      echo"<script>
                              $('#optionsRadios3').prop('checked', false); 
                              $('#optionsRadios4').prop('checked', true);
                              $('#optionsRadios3').attr('disabled', true); 
                           </script>";
                  }
      
          } 
       
       
  }else{
      
      if(!empty($fg_plan_actual)){
      
          
                  if($fg_plan_actual=='M'){
              
                      echo"
                                        <script>
                                         $('#optionsRadios3').prop('checked',true );
                                          $('#optionsRadios4').prop('checked', false); 
                                        </script>             
                                     ";
              
              
                  }else{
              
              
              
                      echo"<script>
                                      $('#optionsRadios3').prop('checked', false); 
                                      $('#optionsRadios4').prop('checked', true); 
  
                                   </script>";  
              
              
                  }
          
          
      
      }else{
      
            ##PODEMOS ELEGIR EL PLAN MES.
            echo"
            <script>  
            $('#optionsRadios3').attr('disabled', false); 
            </script>
            ";
      
                   
      
        }
          
  
  
  }
                                       
                                            
                                           ?>




