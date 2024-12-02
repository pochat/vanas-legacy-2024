<?php 
# Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  //ValidaSesion( );
  
  
  
               
  $no_usuario_adicional=RecibeParametroNumerico('no_usuario_adicional');
  $no_total_licencias=RecibeParametroNumerico('no_total_usuarios_actuales'); 
  $fl_instituto=RecibeParametroNumerico('fl_instituto');
  $fg_tiene_plan=RecibeParametroNumerico('fg_tiene_plan');
  $no_spiner=RecibeParametroNumerico('no_usuario_adicional');
  $fg_accion=RecibeParametroNumerico('fg_accion');
  
  $no_total_licencias_free=$no_total_licencias;
  

  #Recuperamos el usuario del instituto.
  $Queru="SELECT fl_usuario_sp FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $rowr=RecuperaValor($Queru);
  $fl_usuario=$rowr[0];  
  
  
  if($fg_accion==1)
  $no_total_licencias=$no_total_licencias+$no_spiner;
  else
  $no_total_licencias=$no_spiner;
  
  
  #Verificamos si no tiene plan.
  $Quer2="SELECT fg_plan FROM k_current_plan WHERE  fl_instituto=$fl_instituto ";
  $ro=RecuperaValor($Quer2);
  $fg_ya_tiene_plan=!empty($ro['fg_plan'])?$ro['fg_plan']:NULL;
  
  if(empty($fg_ya_tiene_plan))#es freee va adquirir un plan.
      $no_total_licencias=$no_total_licencias_free+$no_usuario_adicional;
  
  
  if((empty($no_total_licencias))&& (empty($no_spiner)) )
      $no_total_licencias=$no_total_licencias_free;
  
  if($fg_tiene_plan==1){#cuenta con plan 
      
      $fg_plan="M";
      
      #Recuperamos datos del plan del Instituto.
      //$Query="SELECT no_total_licencias,fg_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
      //$row=RecuperaValor($Query);
      //$fg_plan=$row['fg_plan'];
      //$no_total_licencias=$row['no_total_licencias'];

  }else{#freee
  
      $fg_plan="A";
  }
  
  
 
  if($no_total_licencias>=100)
      $fg_plan="A";
  
  
  
	
	
                                              $Query="SELECT fl_princing, no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia ,fg_activo 
                                                                                         FROM c_princing 
                                                                                         WHERE fl_instituto=$fl_instituto 
                                                                                         ORDER BY fl_princing ASC ";
                                              $rs = EjecutaQuery($Query);
                                              $tot_registros = CuentaRegistros($rs);
  
                                                $contador_tr=0;
                                            for($i=1;$row=RecuperaRegistro($rs);$i++){
                                                
                                                 $mn_rango_ini= $row['no_ini'];
                                                 $mn_rango_fin= $row['no_fin'];
                                                 $mn_descuento_licencia= $row['mn_descuento_licencia'];
                                                 $mn_mensual= $row['mn_mensual'];
                                                 $mn_descuento_anual= $row['ds_descuento_mensual'];
                                                 $mn_anual= $row['mn_anual'];
												 $fg_activo=$row['fg_activo'];
                                                 
                                            
                                                 $contador_tr ++;
                                                 
                                                
                                                 if(( $no_total_licencias >=$mn_rango_ini)&&($no_total_licencias<=$mn_rango_fin) ){
                                                      $mn_mensual_=$mn_mensual;
                                                      $mn_anual_=$mn_anual;
                                                     
                                                     
                                                    if($fg_plan=="M"){##mes
                                                        $ini_label_mes="<span class='label label-success parpadea' style='background-color:#0071BD;'>";
														if($fg_activo)
                                                        $fin_label_mes="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $ ".$mn_mensual." <small style='color:#fff; font-size:12px !important;'><i>(".number_format($mn_descuento_licencia)."%) ".ObtenEtiqueta(1751)."</i></small>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
                                                        else
														$fin_label_mes="";	
                                                        $ini_label_anual=" ";
                                                        $fin_label_anual=" $ ".$mn_anual." ";
                                                    }else{
                                                    
                                                        $ini_label_mes=" ";
														if($fg_activo)
                                                        $fin_label_mes=" $ ".$mn_mensual." ";
                                                        else
														$fin_label_mes="";	
                                                        
                                                        $ini_label_anual="<span class='label label-success parpadea'style='background-color:#0071BD;'>";
                                                        $fin_label_anual="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $ ".$mn_anual." <small style='color:#fff; font-size:12px !important;'><i>(".number_format($mn_descuento_anual)."%) ".ObtenEtiqueta(1751)."</i></small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>";
                                                        
                                                    
                                                    }
                                                     
                                                      
                                                 }else{
                                                     
                                                     
                                                     
                                                     $ini_label_mes=" ";
													 if($fg_activo)
                                                     $fin_label_mes=" $ ".$mn_mensual." ";
                                                     else
												     $fin_label_mes="";		 
                                                     
                                                     
                                                     $ini_label_anual=" ";
                                                     $fin_label_anual=" $ ".$mn_anual." ";
                                                     
													 	 
                                                 }
                                                 
                                                 
                                                 
                                                
                                                 
                                            
                
                                            echo"
                                              
                                              
                                              
                                                     <tr style='margin-top:1px;border:none;' id='presenta_lista_precios'>
                                                        <td class='text-center' style='border:none;'> $mn_rango_ini - $mn_rango_fin</td>
                                                        <td class='text-center' style='border:none;'>$ini_label_mes $fin_label_mes </td>
                                                        <td class='text-center' style='border:none;'>$ini_label_anual $fin_label_anual </td>
                                                     </tr>
                                              
                                              ";
	
                                            
                                            }
                                            
                                            
                                            if($fg_plan=='M'){
                                                
                                                $total_pagar=$no_total_licencias*(!empty($mn_mensual_)?$mn_mensual_:NULL);
												$subtotal_pa=$total_pagar;
												$fg_agregar_licecias=0;
												$fg_reducir_licencias=0;
									
												#Verificamos si el usuario pagaria tax
												$Query  = "SELECT  b.fl_pais, b.ds_state ";
												$Query .= "FROM c_usuario a ";
												$Query .= "JOIN k_usu_direccion_sp b ON(a.fl_usuario=b.fl_usuario_sp) ";
												$Query .= "WHERE a.fl_usuario=$fl_usuario ";
												$row = RecuperaValor($Query);
												$fl_pais = $row[0];
												$fl_provincia = $row[1];
												  
												$pais_tax=38;
												# Si el pais de canada paga tax
												if($fl_pais==$pais_tax){
													  # Obtenemos la provincia
													  $row0 = RecuperaValor("SELECT mn_tax FROM k_provincias WHERE fl_provincia=$fl_provincia");
													  $mn_porcentaje_tax = $row0[0]/100;
												}
												else{
													  $mn_porcentaje_tax = 0.0;
												}
												$mn_porcentaje_tax_=$mn_porcentaje_tax*100;
												$mn_cantidad_tax= ($total_pagar * $mn_porcentaje_tax_)/100 ;

												
												$total_pagar=$total_pagar+$mn_cantidad_tax;
													
		
												
												
												
												
												
												
												
												
                                                
                                            }else{
                                                
                                                $total_pagar=($no_total_licencias*(!empty($mn_anual_)?$mn_anual_:NULL))*12;
												$subtotal_pa=$total_pagar;
												$fg_agregar_licecias=0;
												$fg_reducir_licencias=0;
                                            
												#Verificamos si el usuario pagaria tax
												$Query  = "SELECT  b.fl_pais, b.ds_state ";
												$Query .= "FROM c_usuario a ";
												$Query .= "JOIN k_usu_direccion_sp b ON(a.fl_usuario=b.fl_usuario_sp) ";
												$Query .= "WHERE a.fl_usuario=$fl_usuario ";
												$row = RecuperaValor($Query);
												$fl_pais = $row[0];
												$fl_provincia = $row[1];
												  
												$pais_tax=38;
												# Si el pais de canada paga tax
												if($fl_pais==$pais_tax){
													  # Obtenemos la provincia
													  $row0 = RecuperaValor("SELECT mn_tax FROM k_provincias WHERE fl_provincia=$fl_provincia");
													  $mn_porcentaje_tax = $row0[0]/100;
												}
												else{
													  $mn_porcentaje_tax = 0.0;
												}
												$mn_porcentaje_tax_=$mn_porcentaje_tax*100;
												$mn_cantidad_tax= ($total_pagar * $mn_porcentaje_tax_)/100 ;

												
												$total_pagar=$total_pagar+$mn_cantidad_tax;
													
		
												
											
											
											
											
											
											
											}
                                    

	#Verificamos su periodo de vigencia.
	#1.Si actualmente esta dentro de el entonces realizamos nuevos calculos y si es mayor a total de licencias actuales 
	#Obtenemos fecha actual :
	$Query = "Select CURDATE() ";
	$row = RecuperaValor($Query);
	$fe_actual = str_texto($row[0]);
	$fe_actual=strtotime('+0 day',strtotime($fe_actual));
	$fe_actual= date('Y-m-d',$fe_actual);
	
	  #Recuperamos el plan que tiene actualmente
	  $Query="SELECT fg_plan,no_total_licencias,no_licencias_usadas,no_licencias_disponibles,fl_princing,fl_current_plan,mn_total_plan,fe_periodo_inicial,fe_periodo_final FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
	  $row=RecuperaValor($Query);
	  $fg_plan=str_texto(!empty($row[0])?$row[0]:NULL);
	  $no_total_licencias_act=!empty($row[1])?$row[1]:NULL;
	  $no_licencias_usadas=!empty($row[2])?$row[2]:NULL;
	  $no_licencias_disponibles=!empty($row[3])?$row[3]:NULL;
	  $fl_princing=!empty($row[4])?$row[4]:NULL;
	  $fl_current_plan=!empty($row[5])?$row[5]:NULL;
	  $mn_total_plan=!empty($row[6])?$row[6]:NULL;
	  $fe_periodo_inicial=!empty($row['fe_periodo_inicial'])?$row['fe_periodo_inicial']:NULL;
	  $fe_expiracion_plan=!empty($row['fe_periodo_final'])?$row['fe_periodo_final']:NULL;
	    
	  $Query="SELECT mn_mensual,mn_anual FROM c_princing WHERE fl_princing=$fl_princing ";
	  $row=RecuperaValor($Query);
	  $mn_costo_anual_actual=!empty($row['mn_anual'])?$row['mn_anual']:NULL;
	  $mn_costo_mensual_actual=!empty($row['mn_mensual'])?$row['mn_mensual']:NULL;
		
	
	#Se calculan sabiendo el credito.
	if( ($fe_actual <= $fe_expiracion_plan) && ($no_total_licencias_act < $no_total_licencias) ){
	
		if($fg_plan=='A'){
		    $mn_costo_actual_total_sin_tax=($mn_costo_anual_actual*$no_total_licencias_act)*12;
		}
		if($fg_plan=='M'){
		    $mn_costo_actual_total_sin_tax=$mn_costo_mensual_actual*$no_total_licencias_act;	  
			  
		}
	
	   $nuevo_total_licencias=$no_total_licencias;
	
	
	
	    #Verificamos en que rango se encuentar el no. de licencias para aplicar nuevos costos.apartir del mes siguiente.
        $Query="SELECT fl_princing,no_ini, no_fin,mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
        $rs = EjecutaQuery($Query);
        for($i=1;$row=RecuperaRegistro($rs);$i++) {
		 
			 $mn_rango_ini= $row['no_ini'];
			 $mn_rango_fin= $row['no_fin'];
			 
			if(( $nuevo_total_licencias >=$mn_rango_ini)&&($nuevo_total_licencias<=$mn_rango_fin) ){
				 
				  $fl_plan=$row['fl_princing'];
				  $mn_costo_mensual=$row['mn_mensual'];
				  $mn_costo_anual=$row['mn_anual'];
				  
				  $mn_descuento_anual=$row['mn_descuento_licencia'];
				  $mn_descuento_mes=$row['ds_descuento_mensual'];
				  
				  if(empty($mn_descuento_anual))
					$mn_descuento_anual=0;
				  if(empty($mn_descuento_mes))
					$mn_descuento_mes=0;
				  
				  if($fg_plan=='M'){
					  $mn_total_nuevo_plan=$mn_costo_mensual * $nuevo_total_licencias;
					  $mn_descuento=$mn_descuento_mes;
				  }
				  if($fg_plan=='A'){
					  $mn_total_nuevo_plan= ($mn_costo_anual*$nuevo_total_licencias)*12;
					  $mn_descuento=$mn_descuento_anual;

				  }
			}

		}
	
	
	
		if($fg_plan=='M'){
	  
			#se_obtiene costo por dia del nuevo plan adquirido.entre 30 dias del mes.
			$mn_costo_por_dia_nueva_tarifa=$mn_total_nuevo_plan/30;
			$mn_costo_por_dia_tarifa_actual= $mn_costo_actual_total_sin_tax/30;
		}
		if($fg_plan=='A'){
				#se_obtiene costo por dia del nuevo plan adquirido.entre 30 dias del mes.
				$mn_costo_por_dia_nueva_tarifa=$mn_total_nuevo_plan/365;
				$mn_costo_por_dia_tarifa_actual=$mn_costo_actual_total_sin_tax/365;
			  
		}
	
	
			
		  #Se calcula los numeros de dias que hacen para terminar plan.
		  $Query="SELECT DATEDIFF('$fe_expiracion_plan','$fe_actual')";
		  $row=RecuperaValor($Query);
		  $no_dias_faltan_terminar_plan=$row[0];
		  
		  #Calculamos mi credito que tengo restantes para finalizar el plan actual.
		  $mn_credito_actual=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_tarifa_actual;
		  
		  #Calculamos el credito del nuevo plan adquirido.
		  $mn_credito_actual_nuevo_adquirido=$no_dias_faltan_terminar_plan*$mn_costo_por_dia_nueva_tarifa;
		  
		  #realizamos la resta costo del nuevo plan  y le restamos nuestro credtio disponible para saber cuanto es la diferencia apagar.
		  $mn_monto_total_a_pagar=$mn_credito_actual_nuevo_adquirido-$mn_credito_actual;
									
		  if($mn_monto_total_a_pagar<0)
		  $mn_monto_total_a_pagar=0;
		  
		  #Verificamos si el usuario pagaria tax
		  $Query  = "SELECT  b.fl_pais, b.ds_state ";
		  $Query .= "FROM c_usuario a ";
		  $Query .= "JOIN k_usu_direccion_sp b ON(a.fl_usuario=b.fl_usuario_sp) ";
		  $Query .= "WHERE a.fl_usuario=$fl_usuario ";
		  $row = RecuperaValor($Query);
		  $fl_pais = $row[0];
		  $fl_provincia = $row[1];
		  
		  $pais_tax=38;
		  # Si el pais de canada paga tax
		  if($fl_pais==$pais_tax){
			  # Obtenemos la provincia
			  $row0 = RecuperaValor("SELECT mn_tax FROM k_provincias WHERE fl_provincia=$fl_provincia");
			  $mn_porcentaje_tax = $row0[0]/100;
		  }
		  else{
			  $mn_porcentaje_tax = 0.0;
		  }
		  $mn_porcentaje_tax_=$mn_porcentaje_tax*100;
		  
		  $subtotal_pa=$mn_monto_total_a_pagar;
		  $mn_cantidad_tax= ($mn_monto_total_a_pagar * $mn_porcentaje_tax_)/100 ;
		  
		  $total_pagar=$mn_monto_total_a_pagar+$mn_cantidad_tax;
			
		
          #Para saber que accion se va ralizar.		
		  $fg_agregar_licecias=1;
          $fg_reducir_licencias=0;		  

	
	}
	
	#Quiere decir que disminuira las licencias.
	if($no_total_licencias_act > $no_total_licencias){
		
		$fg_agregar_licecias=0;
		$fg_reducir_licencias=1;
		  
		  
		  
		 
	    #Verificamos en que rango se encuentar el no. de licencias para aplicar nuevos costos.apartir del mes siguiente.
        $Query="SELECT fl_princing,no_ini, no_fin,mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
        $rs = EjecutaQuery($Query);
        for($i=1;$row=RecuperaRegistro($rs);$i++) {
		 
			 $mn_rango_ini= $row['no_ini'];
			 $mn_rango_fin= $row['no_fin'];
			 
			if(( $no_total_licencias >=$mn_rango_ini)&&($no_total_licencias<=$mn_rango_fin) ){
				 
				  $fl_plan=$row['fl_princing'];
				  $mn_costo_mensual=$row['mn_mensual'];
				  $mn_costo_anual=$row['mn_anual'];
				  
				  $mn_descuento_anual=$row['mn_descuento_licencia'];
				  $mn_descuento_mes=$row['ds_descuento_mensual'];
				  
				  if(empty($mn_descuento_anual))
					$mn_descuento_anual=0;
				  if(empty($mn_descuento_mes))
					$mn_descuento_mes=0;
				  
				  if($fg_plan=='M'){
					  $mn_total_nuevo_plan=$mn_costo_mensual * $no_total_licencias;
					  $mn_descuento=$mn_descuento_mes;
				  }
				  if($fg_plan=='A'){
					  $mn_total_nuevo_plan= ($mn_costo_anual*$no_total_licencias)*12;
					  $mn_descuento=$mn_descuento_anual;

				  }
			}

		}
	
	  
		  #Verificamos si el usuario pagaria tax
		  $Query  = "SELECT  b.fl_pais, b.ds_state ";
		  $Query .= "FROM c_usuario a ";
		  $Query .= "JOIN k_usu_direccion_sp b ON(a.fl_usuario=b.fl_usuario_sp) ";
		  $Query .= "WHERE a.fl_usuario=$fl_usuario ";
		  $row = RecuperaValor($Query);
		  $fl_pais = $row[0];
		  $fl_provincia = $row[1];
		  
		  $pais_tax=38;
		  # Si el pais de canada paga tax
		  if($fl_pais==$pais_tax){
			  # Obtenemos la provincia
			  $row0 = RecuperaValor("SELECT mn_tax FROM k_provincias WHERE fl_provincia=$fl_provincia");
			  $mn_porcentaje_tax = $row0[0]/100;
		  }
		  else{
			  $mn_porcentaje_tax = 0.0;
		  }
		  $mn_porcentaje_tax_=$mn_porcentaje_tax*100;
		  $mn_cantidad_tax= ($mn_total_nuevo_plan * $mn_porcentaje_tax_)/100 ;
		  
		  $subtotal_pa=$mn_total_nuevo_plan;
		  
		  $total_pagar=$mn_total_nuevo_plan+$mn_cantidad_tax;
			
		
	
	
	
	
	
	
		
	 
		  
		  
		
		
	}

if(empty($fl_plan))
    $fl_plan="";
									
?>											

																				
		
<?php 											
											
											
                                            
                                            echo"<script> 

												var subtotal=parseFloat($subtotal_pa);
												var subt=subtotal.toFixed(2);
												
												var tax_pa=parseFloat($mn_cantidad_tax);
												var mn_tax=tax_pa.toFixed(2);
												
												var porc_tax=parseFloat($mn_porcentaje_tax_);
												var porc_tx=porc_tax.toFixed(2);
												
                                                var mn_total_pagar=$total_pagar; 
                                                var t = parseFloat(mn_total_pagar);
												var tt=t.toFixed(2);
                                                var total_lice=$no_total_licencias;
												
												$('#subtotal_pa').empty();
												$('#subtotal_pa').append(subt);
												
												$('#tax_pa').empty();
												$('#tax_pa').append('('+porc_tx+'%)'+mn_tax);
												
												
                                                $('#tot_licencias').empty();
                                                $('#total_pa').empty();   
				                                $('#total_pa').append(tt);
                                                $('#tot_licencias').append(total_lice);
                                                
                                                $('#fg_agregar_licencias').val($fg_agregar_licecias);
                                                $('#fg_reducir_licencias').val($fg_reducir_licencias);
												
                                                //asignamos valores a inputs
                                                $('#mn_total_sin_tax').val(subt);
                                                $('#mn_cantidad_tax').val(mn_tax);
                                                $('#mn_porcentaje_tax').val(porc_tx);
                                                $('#mn_total_con_tax').val(tt);
                                                $('#fl_nuevo_princing').val($fl_plan);
                                                
                                                
                                                
												
                                            </script>";
											

?>




