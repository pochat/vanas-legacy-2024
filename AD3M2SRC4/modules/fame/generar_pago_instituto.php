<?php 
# Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );

  
  $fl_instituto=RecibeParametroNumerico('fl_instituto');
  $spiner=RecibeParametroNumerico('spiner');
  $fe_pago=RecibeParametroFecha('fe_pago');
  $fe_pago=ValidaFecha($fe_pago);
  $cl_metodo_pago=RecibeParametroNumerico('cl_metodo_pago');
  $ds_cheque=RecibeParametroHTML('ds_cheque');
  $ds_comentario=RecibeParametroHTML('ds_comentario');
  $fg_nuevo_plan=RecibeParametroHTML('fg_plan');
  $fg_tipo_pago=RecibeParametroNumerico('fg_tipo_pago');
  $fg_agregar_licencias=RecibeParametroHTML('fg_agregar_licencias');
  $fg_reducir_licencias=RecibeParametroHTML('fg_reducir_licencias');
  
  $_POST[''];
  

  #Recuperamos el rsponsable del instituto.
  $Query="SELECT fl_usuario_sp FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fl_usuario=$row['fl_usuario_sp'];
  
  #Verificamos si el usuario pagaria tax
  $Query  = "SELECT  b.fl_pais, b.ds_state ";
  $Query .= "FROM c_usuario a ";
  $Query .= "JOIN k_usu_direccion_sp b ON(a.fl_usuario=b.fl_usuario_sp) ";
  $Query .= "WHERE a.fl_usuario=$fl_usuario ";
  $row = RecuperaValor($Query);
  $fl_pais = $row[0];
  $fl_provincia = $row[1];
  
  //$fl_pais=38;
  ////$fl_provincia=2;
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
  
  #Recuperamos el email del cliente logeado/ para envio de email.
  $Query="Select ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Query);
  $email_cliente=$row[0];
  
  
  #Recuperamos el plan que tiene actualmente
  $Query="SELECT fg_plan,no_total_licencias,no_licencias_usadas,no_licencias_disponibles,fl_princing,fl_current_plan,
		  id_plan_stripe,id_cliente_stripe,id_suscripcion_stripe,ds_email_stripe
		  FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fg_plan=str_texto($row[0]);
  $no_total_licencias_act=$row[1];
  $no_licencias_usadas=$row[2];
  $no_licencias_disponibles=$row[3];
  $fl_princing=$row[4];
  $fl_current_plan=$row[5];
  $id_plan_creado_instituto=str_texto($row['id_plan_stripe']);
  $id_custom_creado_instituto=str_texto($row['id_cliente_stripe']);
  $id_suscripcion_creado_instituto=str_texto($row['id_suscripcion_stripe']);
  $ds_email_custom=str_texto($row['ds_email_stripe']);
  
  
  
  #Recuperamos el costo que tiene actualmente
  $Query="SELECT mn_mensual,mn_descuento_licencia, mn_anual,ds_descuento_mensual FROM c_princing WHERE fl_princing=$fl_princing ";
  $row=RecuperaValor($Query);
  $mn_mensual=$row[0];
  $mn_descuento_mensual=$row[1];
  $mn_anual=$row[2];
  $mn_descuento_anual=$row[2];
  
  
  #Recuperamos el Nombre del Istituto yel nombre del plan.
  $Query2="SELECT ds_instituto,B.ds_descripcion 
            FROM c_instituto A
            LEFT JOIN c_plan_fame B ON A.cl_plan_fame=B.cl_plan_fame 
            WHERE fl_instituto=$fl_instituto ";
  $row2=RecuperaValor($Query2);
  $nb_instituto=str_texto($row2[0]);
  $ds_plan=str_texto($row2[1]);
  
   if(empty($ds_plan)){
      
    $Query="SELECT ds_descripcion FROM c_plan_fame WHERE cl_plan_fame=1 ";
    $ro=RecuperaValor($Query);
    $ds_plan=str_texto($ro[0]);
    
  }
  
  $rand=rand(5,1000);
  
  
  
  
  if($fg_reducir_licencias){
	  
	  
	  
	  $no_licencias_eliminadas= $no_total_licencias_act - $spiner;
	  
	  
		$contador=0;
        for ($i=1;$i<=$no_licencias_eliminadas;$i++){#ciclo que comprende el no_licencias_eliminadas.Se realiza todo este ciclo ya que puede existrir usuarios registrados que sobrepasen el numero de licencias entonces e van desactivando los usuarios aleatoriamiente.
            $contador++;
	  
				$nuevo_total_licencias=ObtenNumLicenciasI($fl_instituto);
				$nuevo_total_lic_disponibles=ObtenNumLicenciasDisponiblesI($fl_instituto);
				$nuevo_total_lic_usadas=ObtenNumLicenciasUsadasI($fl_instituto);
	  
	  
			#1 aplica al total./se va reduciendo el total
			if($nuevo_total_licencias){
				$nuevo_total_licencias=$nuevo_total_licencias-1;
			
				$Query="UPDATE k_current_plan SET no_total_licencias=$nuevo_total_licencias ";
				$Query.="WHERE fl_current_plan=$fl_current_plan ";
				EjecutaQuery($Query);
				
				#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
				#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
				$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
				$rs5 = EjecutaQuery($Query);
				for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
			 
					$fl_instituto_campus=$rowe['fl_instituto'];
					
					$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
					$rop=RecuperaValor($Query);
					$fl_current_plan_campus=$rop['fl_current_plan'];
									
					$Query="UPDATE k_current_plan SET no_total_licencias=$nuevo_total_licencias ";
				    $Query.="WHERE fl_current_plan=$fl_current_plan_campus ";
				    EjecutaQuery($Query);
					
					
					
					
				}
  





				
			}
	  
	        #2aplica als licencias disponibles/se va reducuiendo el no. de disponibles si existe
            if($nuevo_total_lic_disponibles){
	  
	   
				 $nuevo_total_lic_disponibles=$nuevo_total_lic_disponibles-1;
				 
				 
				 $Query="UPDATE k_current_plan SET no_licencias_disponibles=$nuevo_total_lic_disponibles ";
				 $Query.="WHERE fl_current_plan=$fl_current_plan ";
				 EjecutaQuery($Query);
				 
				#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
				#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
				$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
				$rs5 = EjecutaQuery($Query);
				for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
			 
					$fl_instituto_campus=$rowe['fl_instituto'];
					
					$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
					$rop=RecuperaValor($Query);
					$fl_current_plan_campus=$rop['fl_current_plan'];
									
					$Query="UPDATE k_current_plan SET no_licencias_disponibles=$nuevo_total_lic_disponibles ";
				    $Query.="WHERE fl_current_plan=$fl_current_plan_campus ";
				    EjecutaQuery($Query);
					
					
					
					
				}
  
				 
				 
				 
				 
				 
		    }
	  
	        #3 aplica sobre las usadas/ se va reduciendo las licencias usadads.
			if($nuevo_total_lic_disponibles==0){
				 
				#3aplica sobre las licencias en uso si ya no existe licencias disponibles
				 $nuevo_total_lic_usadas=$nuevo_total_licencias;
				 $Query="UPDATE k_current_plan SET no_licencias_usadas=$nuevo_total_lic_usadas ";
				 $Query.="WHERE fl_current_plan=$fl_current_plan ";
				 EjecutaQuery($Query);
				 
				#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
				#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
				$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
				$rs5 = EjecutaQuery($Query);
				for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
			 
					$fl_instituto_campus=$rowe['fl_instituto'];
					
					$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
					$rop=RecuperaValor($Query);
					$fl_current_plan_campus=$rop['fl_current_plan'];
									
					$Query="UPDATE k_current_plan SET no_licencias_usadas=$nuevo_total_lic_usadas ";
				    $Query.="WHERE fl_current_plan=$fl_current_plan_campus ";
				    EjecutaQuery($Query);
					
					
					
					
				}
				 
				 
				 
			}
	  
		}
		
		#Recuperamos el total de licencias del Instituto.
        $nuevo_total_licencias=ObtenNumLicenciasI($fl_instituto);
		
		#Verificamos en que rango se encuentar el no. de licencias para aplicar nuevos costos.apartir del mes siguiente.
		$Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
		$rs = EjecutaQuery($Query);
		for($i=1;$row=RecuperaRegistro($rs);$i++) {
			 
			 $mn_rango_ini= $row['no_ini'];
			 $mn_rango_fin= $row['no_fin'];
			 
			 if(( $nuevo_total_licencias >=$mn_rango_ini)&&($nuevo_total_licencias<=$mn_rango_fin) ){
				 
				 $fl_plan=$row['fl_princing'];
				 
			 }

		}
		
		#Recuperamos costos segun el plan .
		 $Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_plan";
		 $row=RecuperaValor($Query);
		 $mn_costo_mensual=$row[0];
		 $mn_costo_anual=$row[1];
		 $mn_descuento_mensual=$row[2];
		 $mn_descuento_anual=$row[3];
		
		 $porc_tax=$mn_porcentaje_tax;
		
         
         
         
		#Plan Anual
		if($fg_nuevo_plan==2){ 
		
			#Se calcula el costo anual
		    $mn_costo_anual_total= ($nuevo_total_licencias * $mn_costo_anual) * 12 ;
		 
			#Se le suma el tax actual.
			$mn_tax_correspondiente=$mn_costo_anual_total *$porc_tax;
			$mn_costo_anual_total_con_tax=$mn_costo_anual_total+$mn_tax_correspondiente;
			#Se calcula el costo mensual sera reflejado hasta el proximo mes.
			$mn_costo_mensual_anual=$mn_costo_anual_total/12;
		  
		  
		    #Se actualiza registros del plan actual
		    $Query="UPDATE k_current_plan SET mn_total_plan=$mn_costo_anual_total_con_tax ,fl_princing=$fl_plan ";
		    $Query.="WHERE fl_current_plan=$fl_current_plan ";
		    EjecutaQuery($Query);
			
			
						 
				#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
				#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
				$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
				$rs5 = EjecutaQuery($Query);
				for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
			 
					$fl_instituto_campus=$rowe['fl_instituto'];
					
					$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
					$rop=RecuperaValor($Query);
					$fl_current_plan_campus=$rop['fl_current_plan'];
									
					#Se actualiza registros del plan actual
					$Query="UPDATE k_current_plan SET mn_total_plan=$mn_costo_anual_total_con_tax ,fl_princing=$fl_plan ";
					$Query.="WHERE fl_current_plan=$fl_current_plan_campus ";
					EjecutaQuery($Query);
					
					
					
					
				}
				 
			
			
			
			
                               
		
		
		}
		
		if($fg_nuevo_plan==1){
			
			#Se calcula el costo total    #no_licencias * el costo
			$mn_mensual_total=$nuevo_total_licencias * $mn_costo_mensual ;
			#Se le suma el tax actual.
			$mn_tax_correspondiente=$mn_mensual_total *$porc_tax;  
			$mn_mensual_total_con_tax=$mn_mensual_total+$mn_tax_correspondiente;
			#Se actualiza registros del plan actual
			$Query="UPDATE k_current_plan SET mn_total_plan=$mn_mensual_total_con_tax, fl_princing=$fl_plan ";
			$Query.="WHERE fl_current_plan=$fl_current_plan ";
			EjecutaQuery($Query);
			
					 
				#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
				#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
				$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
				$rs5 = EjecutaQuery($Query);
				for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
			 
					$fl_instituto_campus=$rowe['fl_instituto'];
					
					$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
					$rop=RecuperaValor($Query);
					$fl_current_plan_campus=$rop['fl_current_plan'];
									
					#Se actualiza registros del plan actual
					$Query="UPDATE k_current_plan SET mn_total_plan=$mn_mensual_total_con_tax, fl_princing=$fl_plan ";
					$Query.="WHERE fl_current_plan=$fl_current_plan_campus ";
					EjecutaQuery($Query);
					
					
					
					
				}
				 
			
			
			
			
			
			
			

			
		}
		

		#se genera el cuerpo del documento de email$fl_usuario(reducir licencias)
		$ds_encabezado = genera_documento_spb($fl_usuario, 1,110,$fl_instituto);
		$ds_cuerpo = genera_documento_spb($fl_usuario, 2,110,$fl_instituto);
		$ds_pie = genera_documento_spb($fl_usuario,3,110,$fl_instituto);
		$ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;

        $ds_titulo=ObtenEtiqueta(1642);#etiqueta de asunto del mensjae para el anunciante reduce my contrcat 
		$ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);  
		$ds_email_destinatario=$email_cliente;
		$nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje         
		$bcc=ObtenConfiguracion(107);
		$message  = $ds_contenido;
		$message = utf8_decode(str_ascii(str_uso_normal($message)));
		$mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
					

	
		echo"
			  <script>
			  
					$('#img_stripe').addClass('hidden');
			  
					$.smallBox({
								title : 'Completed renewal &nbsp;',
								content : \" <p class='text-align-right'><a href='".ObtenConfiguracion(121)."/AD3M2SRC4/modules/fame/members_frm.php?c=$fl_instituto' class='btn btn-default btn-sm'>Accept</a> </p>\",
								color : \"#659265\",
								//timeout: 8000,
								icon : \"fa fa-thumbs-up bounce animated\"
							});
					$('#img_stripe').addClass('hidden');
					$('#exampleModalLong').modal('hide');
					
			  </script>
	    ";
	
      
	  
	  
	  
	  
      
      
      
      
      
	  
	  
	  
	  
	  
  }else if($fg_agregar_licencias){
	  
	  
	     $fl_nuevo_princing=RecibeParametroHTML('fl_nuevo_princing');
		 $mn_porcentaje_tax=RecibeParametroHTML('mn_porcentaje_tax');
		 $mn_cantidad_tax=RecibeParametroFlotante('mn_cantidad_tax');
		 $mn_total_sin_tax=RecibeParametroHTML('mn_total_sin_tax');
		 $mn_total_con_tax=RecibeParametroHTML('mn_total_con_tax');
	  
	  
	  
		 
			
		
	
	  
	  
	  
	  
		#Reecupermaos datos generales 
		 $no_licencias_actuales=ObtenNumLicenciasI($fl_instituto);
		 $no_licencia_disponibles=ObtenNumLicenciasDisponiblesI($fl_instituto);
	  
	    #OBTENEMOS LAS licenias agregadads
		$no_licencias_agregadas= $spiner - $no_licencias_actuales;
	  
	  
	     #Obtenemos los nuevas licencias disponibles.
	     $no_licencia_disponibles=$no_licencia_disponibles+$no_licencias_agregadas;
	  
	  
	    #Recuperamos costos segun el plan obtenido del nuevo rango de licencias.
		$Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_nuevo_princing ";
		$row=RecuperaValor($Query);
		$mn_costo_mensual=$row[0];
		$mn_costo_anual=$row[1];
		
		$mn_descuento_anual=$row[2];
		$mn_descuento_licencia=$row[3];
									
		if(empty($mn_descuento_anual))
		 $mn_descuento_anual=0;
		if(empty($mn_descuento_licencia))
		$mn_descuento_licencia=0;
							
		if($fg_plan=='M'){
	 
			$nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-";
			$id_plan_creado_instituto=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-";
			$mn_costo_x_licencia_bd=$mn_costo_mensual;
			$mn_descuentoDB=$mn_descuento_licencia;
			$ds_descripcion=$nb_plan." ".$no_licencias_agregadas." added"; 
			$precio_plan=$spiner*$mn_costo_mensual;
			
		}
		if($fg_plan=='A'){
		     
			$nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-";
			$id_plan_creado_instituto=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-";
			$mn_costo_x_licencia_bd=$mn_costo_anual;
			$mn_descuentoDB=$mn_descuento_anual;
			$ds_descripcion=$nb_plan." ".$no_licencias_agregadas." licences added";
			$precio_plan=($spiner*$mn_costo_anual)*12;
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
		  $mn_cantidad_tax= ($precio_plan * $mn_porcentaje_tax_)/100 ;
		  
		  //$subtotal_pa=$precio_plan;
		  
		  $precio_plan=$precio_plan+$mn_cantidad_tax;
		
		
		
		
		 
		 #1.Solo actualizamos licencias disponibles de su plan actual y l nuevo fl_princing.
		 $Query="UPDATE k_current_plan SET no_total_licencias=$spiner,fl_princing=$fl_nuevo_princing, 
		 no_licencias_disponibles=$no_licencia_disponibles,mn_total_plan=$precio_plan , fg_plan='$fg_plan' ";
		 $Query.="WHERE fl_instituto=$fl_instituto ";
		 EjecutaQuery($Query);
			
		 $mints=date('h:i:s');
		 $fe_pago_bd=$fe_pago." ".$mints;	
		 $id_charge="ch_".$rand."I".$fl_instituto."U".$fl_usuario;	
		 $id_invoice="inv_1".$rand."-".$fl_instituto;	
		
		 #Guardado el registro de pago(bitacora de pagos).
		 $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
		 $Query.="VALUES('$id_custom_creado_instituto','$id_charge','$id_plan_creado_instituto','$id_suscripcion_creado_instituto','2','$ds_email_custom','$ds_descripcion',$mn_total_sin_tax,$mn_cantidad_tax,$mn_total_con_tax,'$fe_pago_bd', $fl_instituto)";
		 $fl_pago=EjecutaInsert($Query);
		
		 #Se guarda la bitacora de los pagos el Instituto  
		 $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,ds_comentario,ds_cheque,cl_metodo_pago,fl_pago_stripe)";
		 $Query.="VALUES($fl_current_plan,$mn_total_con_tax,'1','1','$fe_pago_bd','$ds_descripcion','2',$mn_costo_x_licencia_bd,'$id_invoice',$mn_descuentoDB,'$ds_comentario','$ds_cheque',$cl_metodo_pago,$fl_pago) ";
		 $fl_adm_pagos=EjecutaInsert($Query);

		 
		#se genera el cuerpo del documento de email$fl_usuario(reducir licencias)
		$ds_encabezado = genera_documento_spb($fl_usuario, 1,110,$fl_instituto);
		$ds_cuerpo = genera_documento_spb($fl_usuario, 2,110,$fl_instituto);
		$ds_pie = genera_documento_spb($fl_usuario,3,110,$fl_instituto);
		$ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;
	  
		$ds_titulo=ObtenEtiqueta(1644);#etiqueta de asunto del mensjae para el anunciante reduce my contrcat 
		$ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);  
		$ds_email_destinatario=$email_cliente;
		$nb_nombre_dos=ObtenEtiqueta(1646);#nombre de quien envia el mensaje         
		$bcc=ObtenConfiguracion(107);
		$message  = $ds_contenido;
		$message = utf8_decode(str_ascii(str_uso_normal($message)));
		$mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
		
		
        
        
        echo"
				  <script>
				  
						$('#img_stripe').addClass('hidden');
				  
						$.smallBox({
									title : 'Payment completed &nbsp;',
									content : \"".ObtenEtiqueta(2322)." <p class='text-align-right'><a href='".ObtenConfiguracion(121)."/AD3M2SRC4/modules/fame/billing_frm.php?c=$fl_instituto' class='btn btn-default btn-sm'>Yes</a> <a href='".ObtenConfiguracion(121)."/AD3M2SRC4/modules/fame/members_frm.php?c=$fl_instituto' class='btn btn-default btn-sm'>No</a></p>\",
									color : \"#659265\",
									//timeout: 8000,
									icon : \"fa fa-thumbs-up bounce animated\"
								});
						$('#img_stripe').addClass('hidden');
				        $('#exampleModalLong').modal('hide');
						
				  </script>
				  ";
        
        
        
					 
				#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
				#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
				$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
				$rs5 = EjecutaQuery($Query);
				for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
			 
					$fl_instituto_campus=$rowe['fl_instituto'];
					
					$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
					$rop=RecuperaValor($Query);
					$fl_current_plan_campus=$rop['fl_current_plan'];
									
					
					
					 #1.Solo actualizamos licencias disponibles de su plan actual y l nuevo fl_princing.
					 $Query="UPDATE k_current_plan SET no_total_licencias=$spiner,fl_princing=$fl_nuevo_princing, 
					 no_licencias_disponibles=$no_licencia_disponibles,mn_total_plan=$precio_plan , fg_plan='$fg_plan' ";
					 $Query.="WHERE fl_instituto=$fl_instituto_campus ";
					 EjecutaQuery($Query);
						
					 $mints=date('h:i:s');
					 $fe_pago_bd=$fe_pago." ".$mints;	
					 $id_charge="ch_".$rand."I".$fl_instituto."U".$fl_usuario;	
					 $id_invoice="inv_1".$rand."-".$fl_instituto;	
					
					 #Guardado el registro de pago(bitacora de pagos).
					 $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
					 $Query.="VALUES('$id_custom_creado_instituto','$id_charge','$id_plan_creado_instituto','$id_suscripcion_creado_instituto','2','$ds_email_custom','$ds_descripcion',$mn_total_sin_tax,$mn_cantidad_tax,$mn_total_con_tax,'$fe_pago_bd', $fl_instituto_campus)";
					 $fl_pago_campus=EjecutaInsert($Query);
					
					 #Se guarda la bitacora de los pagos el Instituto  
					 $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,ds_comentario,ds_cheque,cl_metodo_pago,fl_pago_stripe)";
					 $Query.="VALUES($fl_current_plan,$mn_total_con_tax,'1','1','$fe_pago_bd','$ds_descripcion','2',$mn_costo_x_licencia_bd,'$id_invoice',$mn_descuentoDB,'$ds_comentario','$ds_cheque',$cl_metodo_pago,$fl_pago_campus) ";
					 $fl_adm_pagos=EjecutaInsert($Query);
					
					
					
					
					
					
					
					
				}
				 
        
		
		
		
		
		
		
		
        
        
	  
	  
	  
	  
	  
	  
  }else{
  
  
  
  
  
  
  
  if($fg_tipo_pago==1){ #Renovar licenciass que tiene actualmente
	  
	   
	  #Verificamos en que rango se encuentar el no. de licencias para aplicar nuevos costos.apartir del mes siguiente.
	  $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
	  $rs = EjecutaQuery($Query);
	  for($i=1;$row=RecuperaRegistro($rs);$i++) {
		 
		 $mn_rango_ini= $row['no_ini'];
		 $mn_rango_fin= $row['no_fin'];
		 
		 if(( $spiner >=$mn_rango_ini)&&($spiner<=$mn_rango_fin) ){
			 
			 $fl_princing=$row['fl_princing'];
			 $Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princing ";
			 $row=RecuperaValor($Query);
			 $mn_costo_mensual=$row[0];
			 $mn_costo_anual=$row[1];
			 $mn_descuento_mensual=$row[3];
			 $mn_descuento_anual=$row[2];
			
			 $porc_tax=$mn_porcentaje_tax;
			 
			 
			 
			 
		 }

	  }
	   
	   #Se Calcula la fecha de su proxima factura
	   if($fg_nuevo_plan==1){#Mes
		   
		   
		        #Mes
                $fg_plan="M";
				$mn_total_nuevo_plan=$mn_costo_mensual * $spiner;
                $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                $ds_descripcion=$ds_plan."-".ObtenEtiqueta(1705)." ".$spiner." licences";
				$mn_costo_x_licencia=$mn_costo_mensual;
				$mn_descuento=$mn_descuento_mensual;
				
				$fe_final_periodo=strtotime('+1 month',strtotime($fe_pago));
				$fe_final_periodo= date('Y-m-d',$fe_final_periodo);
				
				
				
			
	   }
	   if($fg_nuevo_plan==2){#Anual
		   
				#Anio
                $fg_plan="A";
				$mn_total_nuevo_plan= ($mn_costo_anual*$spiner)*12;
                $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                $mn_costo_x_licencia=$mn_costo_anual;
				$mn_descuento=$mn_descuento_anual;
				
				$fe_final_periodo=strtotime('+1 year',strtotime($fe_pago));
				$fe_final_periodo= date('Y-m-d',$fe_final_periodo);
			    $ds_descripcion=$ds_plan."-".ObtenEtiqueta(1706)." ".$spiner." licences";
	   }
	            $mn_tax=($mn_total_nuevo_plan * $mn_porcentaje_tax_)/100 ;
	   
	            $mn_total_nuevo_plan_con_tax=$mn_total_nuevo_plan + $mn_tax;
	   

	            $id_invoice="inv_1".$rand."-".$fl_instituto;
	   
				$id_cliente_stripe="cus_".$email_cliente;
				$id_charge="ch_".$rand."I".$fl_instituto."U".$fl_usuario;
				$id_plan_creado="Essential Plan ".$nb_instituto."I".$fl_instituto;
				$id_suscripcion="sub_".$rand."I".$fl_instituto;
				$ds_email_custom=$email_cliente;
				$mn_monto_normal=$mn_total_nuevo_plan_con_tax-$mn_tax;
				
				
				
	   	        #Se realiza bitacora en: 
				$mn_monto_normal=$mn_total_nuevo_plan;
			    
			    $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
			    $Query.="VALUES('$id_cliente_stripe','$id_charge','$id_plan_creado','$id_suscripcion','1','$ds_email_custom','$ds_descripcion',$mn_monto_normal,$mn_tax,$mn_total_nuevo_plan_con_tax,CURRENT_TIMESTAMP, $fl_instituto)";
			    $fl_pago=EjecutaInsert($Query);
  

                $mints=date('h:i:s');
                $fe_pago_bd=$fe_pago." ".$mints;
  
	            #se inserta el registro y costo por mes en su bitacora de pagos      
	            $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,ds_comentario,ds_cheque,cl_metodo_pago,fl_pago_stripe)";
			    $Query.="VALUES($fl_current_plan,$mn_total_nuevo_plan_con_tax,'1','$fe_pago','$fe_final_periodo','1','$fe_pago_bd','$ds_descripcion','1',$mn_costo_x_licencia,'$id_invoice',$mn_descuento,'$ds_comentario','$ds_cheque',$cl_metodo_pago,$fl_pago) ";
			    $fl_adm_pagos=EjecutaInsert($Query);

				#Al final solo actualizamos su fecha de periodo de vigencia del neuvo plan creado
				$Query="UPDATE k_current_plan SET fe_periodo_inicial='$fe_pago',  fg_plan='$fg_plan', fe_periodo_final='$fe_final_periodo', fl_princing=$fl_princing WHERE fl_instituto=$fl_instituto ";
				EjecutaQuery($Query);
				 
				#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
				#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
				$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
				$rs5 = EjecutaQuery($Query);
				for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
			 
					$fl_instituto_campus=$rowe['fl_instituto'];
					
					$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
					$rop=RecuperaValor($Query);
					$fl_current_plan_campus=$rop['fl_current_plan'];
					
					$Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
			        $Query.="VALUES('$id_cliente_stripe','$id_charge','$id_plan_creado','$id_suscripcion','1','$ds_email_custom','$ds_descripcion',$mn_monto_normal,$mn_tax,$mn_total_nuevo_plan_con_tax,CURRENT_TIMESTAMP, $fl_instituto_campus)";
			        $fl_pago=EjecutaInsert($Query);
					
					#se inserta el registro y costo por mes en su bitacora de pagos      
					$Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,ds_comentario,ds_cheque,cl_metodo_pago,fl_pago_stripe)";
					$Query.="VALUES($fl_current_plan_campus,$mn_total_nuevo_plan_con_tax,'1','$fe_pago','$fe_final_periodo','1','$fe_pago_bd','$ds_descripcion','1',$mn_costo_x_licencia,'$id_invoice',$mn_descuento,'$ds_comentario','$ds_cheque',$cl_metodo_pago,$fl_pago) ";
					$fl_adm_pagos=EjecutaInsert($Query);
					
					
					
					
					#Al final solo actualizamos su fecha de periodo de vigencia del neuvo plan creado
					$Query="UPDATE k_current_plan SET fe_periodo_inicial='$fe_pago',  fg_plan='$fg_plan', fe_periodo_final='$fe_final_periodo', fl_princing=$fl_princing WHERE fl_instituto=$fl_instituto_campus ";
					EjecutaQuery($Query);
	            
					
					
									
									
				}
				
				
				
				
				
	   
	   
				#Realizamos los calculos para saber las nuevas licencias disponibles.
				
				#Reduccion de licencias.
				if($no_total_licencias_act>$spiner){ 
					
					$nuevo_no_licencias_totales=$spiner;
					
					
					$no_licencias_eliminadas= $no_total_licencias_act- $spiner;
					
					
					$contador=0;
					for ($i=1;$i<=$no_licencias_eliminadas;$i++){#ciclo que comprende el no_licencias_eliminadas.Se realiza todo este ciclo ya que puede existrir usuarios registrados que sobrepasen el numero de licencias entonces e van desactivando los usuarios aleatoriamiente.
					$contador++;
	  
							$nuevo_total_licencias=ObtenNumLicenciasI($fl_instituto);
							$nuevo_total_lic_disponibles=ObtenNumLicenciasDisponiblesI($fl_instituto);
							$nuevo_total_lic_usadas=ObtenNumLicenciasUsadasI($fl_instituto);
	  
							#1 aplica al total./se va reduciendo el total
							if($nuevo_total_licencias){
								$nuevo_total_licencias=$nuevo_total_licencias-1;
							
								$Query="UPDATE k_current_plan SET no_total_licencias=$nuevo_total_licencias ";
								$Query.="WHERE fl_current_plan=$fl_current_plan ";
								EjecutaQuery($Query);
								
								
								#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
								#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
								$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
								$rs5 = EjecutaQuery($Query);
								for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
							 
									$fl_instituto_campus=$rowe['fl_instituto'];
									
									$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
									$rop=RecuperaValor($Query);
									$fl_current_plan_campus=$rop['fl_current_plan'];
									
									$Query="UPDATE k_current_plan SET no_total_licencias=$nuevo_total_licencias ";
								    $Query.="WHERE fl_current_plan=$fl_current_plan_campus ";
								    EjecutaQuery($Query);
									
									
									
								}
					
								
								
								
							}
					  
							#2aplica als licencias disponibles/se va reducuiendo el no. de disponibles si existe
							if($nuevo_total_lic_disponibles){
					  
					   
								 $nuevo_total_lic_disponibles=$nuevo_total_lic_disponibles-1;
								 
								 
								 $Query="UPDATE k_current_plan SET no_licencias_disponibles=$nuevo_total_lic_disponibles ";
								 $Query.="WHERE fl_current_plan=$fl_current_plan ";
								 EjecutaQuery($Query);
								 
								 
								#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
								#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
								$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
								$rs5 = EjecutaQuery($Query);
								for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
							 
									$fl_instituto_campus=$rowe['fl_instituto'];
									
									$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
									$rop=RecuperaValor($Query);
									$fl_current_plan_campus=$rop['fl_current_plan'];
									
									 $Query="UPDATE k_current_plan SET no_licencias_disponibles=$nuevo_total_lic_disponibles ";
									 $Query.="WHERE fl_current_plan=$fl_current_plan_campus ";
									 EjecutaQuery($Query);
									
									
									
								}
								 
								 
								 
								 
								 
							}
					  
							#3 aplica sobre las usadas/ se va reduciendo las licencias usadads.
							if($nuevo_total_lic_disponibles==0){
								 
								#3aplica sobre las licencias en uso si ya no existe licencias disponibles
								 $nuevo_total_lic_usadas=$nuevo_total_licencias;
								 $Query="UPDATE k_current_plan SET no_licencias_usadas=$nuevo_total_lic_usadas ";
								 $Query.="WHERE fl_current_plan=$fl_current_plan ";
								 EjecutaQuery($Query);
								 
								 
								  
								#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
								#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
								$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
								$rs5 = EjecutaQuery($Query);
								for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
							 
									$fl_instituto_campus=$rowe['fl_instituto'];
									
									$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
									$rop=RecuperaValor($Query);
									$fl_current_plan_campus=$rop['fl_current_plan'];
									
									 
									 $Query="UPDATE k_current_plan SET no_licencias_usadas=$nuevo_total_lic_usadas ";
									 $Query.="WHERE fl_current_plan=$fl_current_plan_campus ";
									 EjecutaQuery($Query);
									 
									
									
									
								}
								 
								 
								 
								 
							}
	  
	  
	  
	  
					}
					
					
					#Recuperamos el total de licencias del Instituto.
				    $nuevo_no_licencias_totales=ObtenNumLicenciasI($fl_instituto);
					$nuevo_no_licencias_disponibles=ObtenNumLicenciasDisponiblesI($fl_instituto);
				    $nuevo_total_lic_usadas=ObtenNumLicenciasUsadasI($fl_instituto);
					
					
					$Query="UPDATE k_current_plan SET no_total_licencias=$nuevo_no_licencias_totales,  no_licencias_usadas=$nuevo_total_lic_usadas , no_licencias_disponibles=$nuevo_no_licencias_disponibles ";
					$Query.="WHERE fl_current_plan=$fl_current_plan ";
				    EjecutaQuery($Query);
					
					
								  
								#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
								#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
								$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
								$rs5 = EjecutaQuery($Query);
								for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
							 
									$fl_instituto_campus=$rowe['fl_instituto'];
									
									$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
									$rop=RecuperaValor($Query);
									$fl_current_plan_campus=$rop['fl_current_plan'];
									
									
									$Query="UPDATE k_current_plan SET no_total_licencias=$nuevo_no_licencias_totales,  no_licencias_usadas=$nuevo_total_lic_usadas , no_licencias_disponibles=$nuevo_no_licencias_disponibles ";
									$Query.="WHERE fl_current_plan=$fl_current_plan_campus ";
									EjecutaQuery($Query);
									 
									
									
									
								}
								 
					
					
					
					
					
					
					
				}
				
				#Aumento de licencias
				if($no_total_licencias_act<$spiner){
					
					$nuevo_no_licencias_totales=$spiner;
					$no_licencias_agregadas=$spiner - $no_total_licencias_act;
					$nuevo_no_licencias_disponibles=$no_licencias_disponibles + $no_licencias_agregadas;
					$nuevo_total_lic_usadas=ObtenNumLicenciasUsadasI($fl_instituto);
					
					
					 $Query="UPDATE k_current_plan SET no_total_licencias=$nuevo_no_licencias_totales,  no_licencias_usadas=$nuevo_total_lic_usadas , no_licencias_disponibles=$nuevo_no_licencias_disponibles ";
					 $Query.="WHERE fl_current_plan=$fl_current_plan ";
					 EjecutaQuery($Query);
					 
					 
					 
					  
					#Renovamos todas las licencias que tiene actualmente el Instituto Campus.
					#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
					$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
					$rs5 = EjecutaQuery($Query);
					for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
				 
						$fl_instituto_campus=$rowe['fl_instituto'];
						
						$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto_campus ";
						$rop=RecuperaValor($Query);
						$fl_current_plan_campus=$rop['fl_current_plan'];
						
						 $Query="UPDATE k_current_plan SET no_total_licencias=$nuevo_no_licencias_totales,  no_licencias_usadas=$nuevo_total_lic_usadas , no_licencias_disponibles=$nuevo_no_licencias_disponibles ";
						 $Query.="WHERE fl_current_plan=$fl_current_plan_campus ";
						 EjecutaQuery($Query);
						 
					 
						 
						
						
						
					}
								 
					 
					 
					 
					
					
					
				}
				
				
				
	   
	   
	   
	   
	   
			  echo"
				  <script>
				  
						$('#img_stripe').addClass('hidden');
				  
						$.smallBox({
									title : 'Payment completed &nbsp;',
									content : \"".ObtenEtiqueta(2322)." <p class='text-align-right'><a href='".ObtenConfiguracion(121)."/AD3M2SRC4/modules/fame/billing_frm.php?c=$fl_instituto' class='btn btn-default btn-sm'>Yes</a> <a href='".ObtenConfiguracion(121)."/AD3M2SRC4/modules/fame/members_frm.php?c=$fl_instituto' class='btn btn-default btn-sm'>No</a></p>\",
									color : \"#659265\",
									//timeout: 8000,
									icon : \"fa fa-thumbs-up bounce animated\"
								});
						$('#img_stripe').addClass('hidden');
				        $('#exampleModalLong').modal('hide');
						
				  </script>
				  ";
	   
	   
		  #Enviamos email de notificacion de que se ha suscrito a un plan.
		  #Se recupera el contenido del template/correo.
		  
		  $ds_encabezado = genera_documento_spb($fl_usuario, 1,125,$fl_instituto);
		  $ds_cuerpo = genera_documento_spb($fl_usuario, 2,125,$fl_instituto);
		  $ds_pie = genera_documento_spb($fl_usuario,3,125,$fl_instituto);
		  $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;
		  
		  
		  $ds_titulo=ObtenEtiqueta(1743);#etiqueta de asunto del mensjae FAME Alert Expiracion de plan 
		  $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);  
		  $ds_email_destinatario=$email_cliente;
		  $nb_nombre_dos=ObtenEtiqueta(1646);#nombre de quien envia el mensaje         
		  $bcc=ObtenConfiguracion(107);#envio de copia
		  $message  = $ds_contenido;
		  $message = utf8_decode(str_ascii(str_uso_normal($message)));
		  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
  
  
		#Renovamos todas las licencias que tiene actualmente el Instituto.
		#Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
		$Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
		$rs5 = EjecutaQuery($Query);
		for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
	 
			$fl_instituto=$rowe['fl_instituto'];
			
			
		}
  
  
  
	   
	     
	  
}



  }



  
  ##Funciones especificas para manejo de licencias del Instituto(Solo pagos manuales).
  
  function ObtenNumLicenciasI($fl_instituto){
	  
	  $row = RecuperaValor("SELECT  no_total_licencias FROM k_current_plan WHERE fl_instituto=$fl_instituto");
      return $row[0];
 
  }
  
  function ObtenNumLicenciasDisponiblesI($fl_instituto){
	  
	  $row = RecuperaValor("SELECT  no_licencias_disponibles FROM k_current_plan WHERE fl_instituto=$fl_instituto");
      return $row[0];
	  
  }
  
  function ObtenNumLicenciasUsadasI($fl_instituto){
	  
	  $row = RecuperaValor("SELECT  no_licencias_usadas FROM k_current_plan WHERE fl_instituto=$fl_instituto");
      return $row[0];
	  
  }
  
  
  
  
  function genera_documento_spb($clave, $opc, $fl_template=0, $fl_instituto='') {  
      
      # Recupera datos del template del documento
      switch($opc){
          case 1: $campo = "ds_encabezado"; break;
          case 2: $campo = "ds_cuerpo"; break;
          case 3: $campo = "ds_pie"; break;
          case 4: $campo = "nb_template"; break;
      }
      
      # Obtenemos la informacion del template header body or footer
      $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
      $row = RecuperaValor($Query1);
      
      $cadena = $row[0];
      # Sustituye caracteres especiales
      $cadena = $row[0];
      $cadena = str_replace("&lt;", "<", $cadena);
      $cadena = str_replace("&gt;", ">", $cadena);
      $cadena = str_replace("&quot;", "\"", $cadena);
      $cadena = str_replace("&#039;", "'", $cadena);
      $cadena = str_replace("&#061;", "=", $cadena);
      
      # Recupera datos usuario
      $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno,ds_login, fg_genero, ds_email, ".ConsultaFechaBD('fe_nacimiento', FMT_FECHA)." fe_nacimiento, fl_usu_invita ";
      $Query .= "FROM c_usuario WHERE fl_usuario=$clave ";
      $row = RecuperaValor($Query);
      $ds_fname = str_texto($row[0]);
      $ds_lname = str_texto($row[1]);
      $ds_mname = str_texto($row[2]);
      $ds_login = str_texto($row[3]);
      $fg_genero = str_texto($row[4]);
      switch($fg_genero){
          case "M": $ds_genero = ObtenEtiqueta(115); break;
          case "F": $ds_genero = ObtenEtiqueta(116); break;
          case "N": $ds_genero = ObtenEtiqueta(128); break;
      }
      $ds_email = $row[5];
      $fe_nacimiento = $row[6];
      $fl_usu_invita = $row[7];
      
      
      if(empty($clave)){#se coloca en dado caso de que la clave venga vacia.(se utiliza para envio de correo de registro de menor de edad.)
          
          #Recuperamos el nombre del estudinate que se registro
          $Query="SELECT ds_first_name,ds_last_name FROM k_envio_email_reg_selfp 
			 WHERE fl_envio_correo=$fl_envio_correo ";
          $row=RecuperaValor($Query);
          $ds_fname=str_texto($row[0]);
          $ds_lname=str_texto($row[1]);
          
          
          
          
          
          
          
          $Query3  = "SELECT b.ds_nombres, b.ds_apaterno, b.ds_amaterno ";
          $Query3 .= "FROM k_noconfirmados_pro a, c_usuario b WHERE a.fl_maestro=b.fl_usuario AND a.fl_envio_correo=$fl_envio_correo ";
          $row3 = RecuperaValor($Query3);
          $fame_te_fname = str_texto($row3[0]);
          $fame_te_lname = str_texto($row3[1]);  
          $cadena = str_replace("#fame_te_fname#",$fame_te_fname, $cadena);  # fname teacher
          $cadena = str_replace("#fame_te_lname#",$fame_te_lname, $cadena);  # lname teacher   
      }
      
      if($ds_fname)
          $cadena = str_replace("#fame_fname#", $ds_fname, $cadena);                        # Student first name 
      $cadena = str_replace("#fame_mname#", $ds_mname, $cadena);                        # Student middle name 
      if($ds_lname)
          $cadena = str_replace("#fame_lname#", $ds_lname, $cadena);                        # Student last name
      $cadena = str_replace("#fame_login#", $ds_login, $cadena);                        # Student login
      $cadena = str_replace("#fame_gender#", $ds_gender, $cadena);                      # Student gender female
      $cadena = str_replace("#fame_email#", $ds_email, $cadena);                        # Student email address
      $cadena = str_replace("#fame_byear#", substr($fe_nacimiento,6,4), $cadena);    #Student year of birth 
      $cadena = str_replace("#fame_bmonth#", substr($fe_nacimiento,3,2), $cadena);   #Student month of birth 
      $cadena = str_replace("#fame_bday#", substr($fe_nacimiento,0,2), $cadena);     #Student day of birth 
      
     
      
      # Obtenemos iinformacion de la direccion
      $row = RecuperaValor("SELECT a.fl_pais, nb_pais, ds_state, ds_city, ds_number, ds_street, ds_zip, ds_phone_number  
FROM k_usu_direccion_sp a, c_pais b WHERE a.fl_pais=b.fl_pais AND a.fl_usuario_sp=$clave");
      $fl_pais = $row[0];
      $nb_pais = str_texto($row[1]);
      if($fl_pais==38){
          $row1 = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$row[2]");
          $ds_state = $row1[0];
      }
      else
          $ds_state = str_texto($row[2]);
      $ds_city = str_texto($row[3]);
      $ds_number = str_texto($row[4]);
      $ds_street = str_texto($row[5]);
      $ds_zip = str_texto($row[6]);
      $ds_phone_number = str_texto($row[7]);
      
      $cadena = str_replace("#fame_street_no#", $ds_number, $cadena);                   # Student number street
      $cadena = str_replace("#fame_street_name#", $ds_street, $cadena);                 # Student name street
      $cadena = str_replace("#fame_city#", $ds_city, $cadena);                          # Student city
      $cadena = str_replace("#fame_state#", $ds_state, $cadena);                        # Student state
      $cadena = str_replace("#fame_country#", $nb_pais, $cadena);                       # Student country
      $cadena = str_replace("#fame_code_zip#", $ds_zip, $cadena);                       # Student zip
      $cadena = str_replace("#fame_phone#", $ds_phone_number, $cadena);                 # Student phone number
      
      
      
      
      
      /***********************************/ 
      $Query="SELECT fg_plan ,no_licencias_usadas,no_licencias_disponibles,no_total_licencias, fl_princing,fe_periodo_final FROM k_current_plan where fl_instituto=$fl_instituto ";
      $row=RecuperaValor($Query);
      $fg_plan=$row[0];
      $no_licencias_usadas=$row[1];
      $no_licencias_disponibles=$row[2];
      $total_licencias=$row[3];
      $fl_princi=$row[4];
      $fecha_termino_plan=$row[5];
      
      $Query="SELECT ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princi ";
      $row=RecuperaValor($Query);
      $mn_descuento_anual=number_format($row[0])."%";
      $mn_descuento_mensual=number_format($row[1])."%";
      
      
      
      if($fg_plan=='A'){
          $plan_actual=ObtenEtiqueta(1503);
          $mn_descuento_plan=$mn_descuento_anual;
      }else{
          $plan_actual=ObtenEtiqueta(1763);
          $mn_descuento_plan=$mn_descuento_mensual;
      }
      
      
      
      #Verificamos si el Instituto ha solicitado cambiado de plan, e identificmos su nuevo pan/nueva suscripcion. el fg_motivo=3 quiere decir que es cambio de plan.
      $QueryP="SELECT fg_cambio_plan FROM  k_cron_plan_fame WHERE fg_motivo_pago='3' AND fl_instituto=$fl_instituto ";
      $rowP=RecuperaValor($QueryP);
      $fg_nuevo_plan=$rowP[0];
      
      
      
      if($fg_nuevo_plan=='A')
          $nuevo_plan=ObtenEtiqueta(1503);
      if($fg_nuevo_plan=='M')
          $nuevo_plan=ObtenEtiqueta(1763);
      
      

     
      
      $dominio_campus = ObtenConfiguracion(116);
      $link_login_fame=$dominio_campus;#bueno#fame_link_login#;
      
      
      
      
      #damos formato ala fecha de finalizacion.
      #DAMOS FORMATO DIA,MES, ANÑO
      
      $fe_termino=strtotime('+0 day',strtotime($fecha_termino_plan));
      $fe_termino= date('Y-m-d',$fe_termino);
      
      $date = date_create($fe_termino);
      $fe_terminacion_plan=date_format($date,'F j , Y');
      
      
      
      
      #Varibales para sustituir para nitificaciones realizadas en billing.
      $cadena = str_replace("#fame_current_plan#", $plan_actual, $cadena);  #plan actual/mont/anual
      $cadena = str_replace("#fame_new_plan#", $nuevo_plan, $cadena);  #nuevo_plan
      $cadena = str_replace("#fame_available_licenses#", $no_licencias_disponibles, $cadena);  #licencisas disponibles
      $cadena = str_replace("#fame_licenses_used#", $no_licencias_usadas, $cadena); #lidcenias usadas
      $cadena = str_replace("#fame_total_licenses#", $total_licencias, $cadena);  #total de licencias 
      $cadena = str_replace("#fame_link_login#", $link_login_fame, $cadena);  #total de licencias 
      $cadena = str_replace("#fame_fe_expiration_plan#", $fe_terminacion_plan, $cadena);  #total de licencias 
      $cadena = str_replace("#fame_discount_plan#", $mn_descuento_plan, $cadena);  #total de licencias 
      
      # Obtenemos los datos del maestro
      $Query3  = "SELECT b.ds_nombres, b.ds_apaterno, b.ds_amaterno FROM k_usuario_programa a ";
      $Query3 .= "LEFT JOIN c_usuario b ON(a.fl_maestro=b.fl_usuario) ";
      $Query3 .= "WHERE fl_programa_sp=$programa AND fl_usuario_sp=$clave ";
      $row3 = RecuperaValor($Query3);
      $fame_te_fname = str_texto($row3[0]);
      $fame_te_lname = str_texto($row3[1]);      
      if(empty($fame_te_fname) || empty($fame_te_lname)){
          $row00 = RecuperaValor("SELECT ds_nombres, ds_apaterno, ds_amaterno FROM c_usuario WHERE fl_usuario=$fl_usu_invita");
          $fame_te_fname = str_texto($row00[0]);
          $fame_te_lname = str_texto($row00[1]);
          $cadena = str_replace("#fame_te_fname#",$fame_te_fname, $cadena);  # fname teacher
          $cadena = str_replace("#fame_te_lname#",$fame_te_lname, $cadena);  # lname teacher 
      }
      else{
          $cadena = str_replace("#fame_te_fname#",$fame_te_fname, $cadena);  # fname teacher
          $cadena = str_replace("#fame_te_lname#",$fame_te_lname, $cadena);  # lname teacher 
      }
      
      
      
      #Recuperamos datos del administrado del Instituto.
      $Query="SELECT A.fl_usuario_sp,U.ds_nombres,U.ds_apaterno FROM c_instituto A 
          JOIN c_usuario U ON U.fl_usuario=A.fl_usuario_sp
           WHERE A.fl_instituto =$fl_instituto ";
      $row=RecuperaValor($Query);
      $fame_fname_admin=str_texto($row[1]);
      $fame_lname_admin=str_texto($row[2]);
      
      $cadena = str_replace("#fame_adm_fname#",$fame_fname_admin, $cadena);  # fname teacher
      $cadena = str_replace("#fame_adm_lname#",$fame_lname_admin, $cadena);  # lname teacher 
      

      return (str_uso_normal($cadena));
  }
    
  
?>




