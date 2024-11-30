<?php

	#librerias propias de FAME.
	require '/var/www/html/vanas/fame/lib/self_general.php';
	require '/var/www/html/vanas/fame/lib/Stripe/Stripe/init.php';

	#librerias propias de FAME.
    /*require '../fame/lib/self_general.php';
    require '../fame/lib/Stripe/Stripe/init.php';
	*/

    $from = 'noreply@vanas.ca';
    # Variables Stripe
    $secret_key = ObtenConfiguracion(112);
    $currency=ObtenConfiguracion(113);
	
	$logejecucion="log_cronjobs.txt";
	
	#Recuperamos la fecha actual. formato     
	$fe_actual=ObtenerFechaActual();

	/**
	 * para prubas
	 * 
	 */ 
	 #fecha en que finaliza su periodo 
	//$fe_actual="2018-02-21";
    

    
	
	try {

         # set your secret key: remember to change this to your live secret key in production
         # see your keys here https://manage.stripe.com/account
         \Stripe\Stripe::setApiKey($secret_key);

        

	$currency=ObtenConfiguracion(113); 
	GeneraLog($logejecucion,"__________________________inicia______________________________");
	#Verificamos los registros existentes de la DB de los planes de los alumnos existentes(Cuando compran un plan mes o anual.).
         $Query="SELECT A.fl_alumno,A.cl_plan_fame,A.fg_plan,A.fe_periodo_inicial,A.fe_periodo_final,A.mn_total_plan,U.ds_email,A.id_cupon_stripe,A.id_cliente_stripe,A.id_plan_stripe,A.id_suscripcion_stripe,A.fe_periodo_final_mes,A.fl_current_plan_alumno ";
	     $Query.="FROM k_current_plan_alumno A 
             JOIN c_usuario U ON A.fl_alumno=U.fl_usuario  ";
	$rs1 = EjecutaQuery($Query);
	for($i2=1;$rowa=RecuperaRegistro($rs1);$i2++){
	   
	        $fl_alumno=$rowa[0];
			$cl_plan_fame=$rowa[1];
            $fg_plan=$rowa[2];
            $fe_periodo_inicial=$rowa[3];
            $fe_periodo_final=$rowa[4];
            $mn_total_plan=$rowa[5];
			$ds_email_alumno=str_texto($rowa[6]);
            $cupon=$rowa['id_cupon_stripe'];
            $id_cliente_stripe=$rowa['id_cliente_stripe'];
            $id_plan_stripe=$rowa['id_plan_stripe'];
            $id_subscripcion_stripe=$rowa['id_suscripcion_stripe'];
			$fe_periodo_final_mes=$rowa['fe_periodo_final_mes'];
            $fl_current_plan_alumno=$rowa['fl_current_plan_alumno'];

            if($fe_periodo_final_mes){ $fe_periodo_final=$fe_periodo_final_mes;  }
            #Recuperamos datos genreales del alumno.
            $Query="SELECT fl_pais FROM k_usu_direccion_sp  WHERE fl_usuario_sp=$fl_alumno ";
			$row=RecuperaValor($Query);
			$fl_pais=$row['fl_pais'];

            GeneraLog($logejecucion,$Query);
            /**
             * 
             * #PRUEBAS
             */ 
            
            //$fe_periodo_inicial="2017-10-21";
            //$fe_periodo_final="2017-10-21";
            
            
  
            #Verificamos dias rstantes
			$no_dias_restantes_plan=ObtenDiasRestantesPlan($fe_periodo_final,$fe_actual);
            GeneraLog($logejecucion,$no_dias_restantes_plan);
  
			#Se genera un email de confirmacion al alumno que recibe el curso 
			$ds_encabezado = genera_documento_sp($fl_alumno,1,148,$fl_programa_sp);
			$ds_cuerpo = genera_documento_sp($fl_alumno,2,148,$fl_programa_sp);
			$ds_pie = genera_documento_sp($fl_alumno,3,148,$fl_programa_sp);
			$ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;                      
               
            
            
            $ds_contenido = str_replace("#fame_number_of_days#", $no_dias_restantes_plan, $ds_contenido);  #no_dias_cuso
            $ds_contenido = str_replace("#fame_pg_name#", "".ObtenEtiqueta(2118)."", $ds_contenido);  #no_dias_cuso
            
    
            
            
			#Recuperamos el titulo del documento email
			$Query="SELECT nb_template FROM k_template_doc WHERE fl_template=148 ";
			$row=RecuperaValor($Query);
			$ds_titulo=str_texto($row[0]);
			
			$ds_email_de_quien_envia_mensaje=$from;#ObtenConfiguracion(4);
			$nb_nombre_envia_email=ObtenEtiqueta(949);#nombre de quien envia el mensaje
			$bcc=ObtenConfiguracion(107);
			$message  = $ds_contenido;
			$message = utf8_decode(str_ascii(str_uso_normal($message)));
  
  
  
  
  
  
			 switch($no_dias_restantes_plan)
			{
				case 30: 
					#Envia email de notificcion al usuario
			        $mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_alumno, $ds_titulo, $message, $bcc);
			        $dias_anticipo=30;		
				break;
				case 15: 
					#Envia email de notificcion al usuario
			        $mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_alumno, $ds_titulo, $message, $bcc);
					$dias_anticipo=15;
				break;
				case 7: 
					#Envia email de notificcion al usuario
					$mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_alumno, $ds_titulo, $message, $bcc);
					$dias_anticipo=7;
				break;
				
			}
			 if($mail)
			   $fg_estatus=1;
			   else
			   $fg_estatus=0;
			   
			
             #Se genera un registro en la BD para saber si se ejecuto el cron, y que fue exctmene lo que hizo.
             $Query  ="INSERT INTO k_fame_cron_job_students_plan (ds_email_envio,ds_email_destinatario,fg_status,fl_alumno,fe_ejecucion,no_dias_anticipo,ds_tipo_cron)";
             $Query .="VALUES('$from','$ds_email_alumno','$fg_estatus',$fl_alumno,CURRENT_TIMESTAMP,$dias_anticipo,'Plan anual,mes')";
             $fl_registro=EjecutaInsert($Query); 
             
             
            #Enviara email de que el curso ha esxpirado. 
            if($fe_periodo_final < $fe_actual){
            
                
                #Verificamos si ya exuste e registro y si ya pues ya no manda nada.
                $Query="SELECT COUNT(*) FROM k_fame_cron_job_students_plan WHERE fl_alumno=$fl_alumno AND ds_tipo_cron='ExpirationDate' ";
                $row=RecuperaValor($Query);
                $existe=$row[0];
                
                if(empty($existe)){
                
                
                    #Se genera un email de confirmacion al alumno que recibe el curso 
                    $ds_encabezado = genera_documento_sp($fl_alumno,1,149,$fl_programa_sp);
                    $ds_cuerpo = genera_documento_sp($fl_alumno,2,149,$fl_programa_sp);
                    $ds_pie = genera_documento_sp($fl_alumno,3,149,$fl_programa_sp);
                    $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;                      
                
                    $fe_expiracion_curso=GeneraFormatoFecha($fe_periodo_final);

                    $ds_contenido = str_replace("#fame_expiration_course#", $fe_expiracion_curso, $ds_contenido);
               
                    #Envia email de notificcion al usuario
                    $mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_alumno, $ds_titulo, $message, $bcc);
                    $dias_anticipo=0;	

                    #Se genera su registro de que lo enviara solo una vez.
                    $Query  ="INSERT INTO k_fame_cron_job_students_plan (ds_email_envio,ds_email_destinatario,fg_status,fl_alumno,fe_ejecucion,no_dias_anticipo,ds_tipo_cron)";
                    $Query .="VALUES('$from','$ds_email_alumno','1',$fl_alumno,CURRENT_TIMESTAMP,0,'ExpirationDate')";
                    $fl_registro=EjecutaInsert($Query); 
                
                }
            
            
            }
            
            if($fe_periodo_final==$fe_actual){
				
                $Query3="SELECT fl_usuario,fl_current_plan,fe_cancelacion,fe_creacion FROM 
                         k_cancelacion_plan_alumno               
                         WHERE fl_current_plan=$fl_current_plan_alumno ";
                $roe = RecuperaValor($Query3);
				
				if(empty($roe[0])){
				
					#Recuperamos datos de la suscripcion actual.
					$subscription = \Stripe\Subscription::retrieve($id_subscripcion_stripe);
					$Id_subscricion=$subscription->id;
					$Idplan=$subscription['plan']->id;
					$Idproducto=$subscription['plan']->product;
					$IdCustomer=$subscription->customer;

					
					#1.Se cancela la suscripcion actual.
					$subscription = \Stripe\Subscription::retrieve($Id_subscricion);
					$subscription->cancel();
					
					#2.Se elimina el plan.
					$plan = \Stripe\Plan::retrieve($id_plan_stripe);
					$plan->delete();
					


					#Procedemos a crear un nuevo plan.
					#Obtenemos fecha actual :
					$Query = "Select CURDATE() ";
					$row = RecuperaValor($Query);
					$fe_actual = str_texto($row[0]);
					$fe_actual=strtotime('+0 day',strtotime($fe_actual));
					$fe_actual= date('Y-m-d',$fe_actual);

					$Query="SELECT mn_mensual,mn_anual FROM c_princing_course WHERE 1=1 ";
					$po=RecuperaValor($Query);
					$mn_costo_mensual=$po['mn_mensual'];
					$mn_costo_anual=$po['mn_anual'];

				   

					#Solo canada cobra tax.
					if($fl_pais==38){

						$mn_porcentaje_tax_=Tax_Can_User($fl_alumno);
						
						if($fg_plan=='A'){
							
							$mn_tax=$mn_costo_anual*$mn_porcentaje_tax_;
							$mn_costo=$mn_costo_anual+$mn_tax;
							$mn_monto_sin_tax=$mn_costo_anual;
							$mn_monto_sin_tax_DB=$mn_costo_anual;
							$interval="year";

						}

						if($fg_plan=='M'){
							$mn_tax=$mn_costo_mensual*$mn_porcentaje_tax_;
							$mn_costo=$mn_costo_mensual+$mn_tax;
							$mn_monto_sin_tax=$mn_costo_mensual;
							$mn_monto_sin_tax_DB=$mn_costo_mensual;
							$interval="month";

						}




					}else{

						$mn_porcentaje_tax_=0;
						$mn_tax=0;

						if($fg_plan=='A'){
							$mn_costo=$mn_costo_anual;
							$mn_monto_sin_tax=$mn_costo_anual;
							$mn_monto_sin_tax_DB=$mn_costo_anual;
							$interval="year";

						}
						if($fg_plan=='M'){
							$mn_costo=$mn_costo_mensual;
							$mn_monto_sin_tax=$mn_costo_mensual;
							$mn_monto_sin_tax_DB=$mn_costo_mensual;
							$interval="month";

						}


					}

					#Para enviar a stripe
					$mn_monto_sin_tax=Conv_Dollars_Stripe($mn_monto_sin_tax);
					
								 
					
				
				   #Se crea el nuevo plan y nuevos datos en stripe.

					#1.Creamos su plan de pago,propio por cliente con el tax incluido
					$plan = \Stripe\Plan::create(array(
					   "name" => $Idplan,
					   "id" => $Idplan,
					   "interval" => $interval,
					   "currency" => $currency,
					   "amount" => $mn_monto_sin_tax
					   )
					 );              
					$id_plan_stripe=$plan->id;
					$subscription= \Stripe\Subscription::create(array(
							 "customer" => $IdCustomer,
							 "plan" => $id_plan_stripe,
							 "quantity"=>1,
							 "tax_percent" => $mn_porcentaje_tax_*100,     
					 ));


					$id_suscripcion_stripe=$subscription->id;
					
					#Verificamos el eveno creado en Strippe, para despues poder recuperar elid del pago que se realizo. y actualizar su descripcion.
					$event = \Stripe\Event::all(array("limit" => 2));
					$id_charges=$event->data;    
					$id_evento=$id_charges[1]->data;
					$id_charge=$id_evento['object']->charge;
					$id_invoice=$id_evento['object']->id;  
					#Actualizamos la descripcion del pago que se realizao.
					$ch = \Stripe\Charge::retrieve($id_charge);
					$ch->description = $Idplan;
					$ch->save();
					  
					
					#Calculamos fecha de finalizacion
					if($fg_plan=='M'){
						
						$fe_periodo_final=strtotime('+30 day',strtotime($fe_actual));
						$fe_periodo_final=date('Y-m-d',$fe_periodo_final);

						$fe_periodo_final_todos_anual=strtotime('+365 day',strtotime($fe_actual));
						$fe_periodo_final_todos_anual=date('Y-m-d',$fe_periodo_final_todos_anual);

					}
					if($fg_plan=='A'){
						
						$fe_periodo_final=strtotime('+365 day',strtotime($fe_actual));
						$fe_periodo_final=date('Y-m-d',$fe_periodo_final);
						$fe_periodo_final_todos_anual=$fe_periodo_final;
					}


					

					#Actualizamos datos en FAME.
					$Query ="UPDATE k_current_plan_alumno SET mn_total_plan=$mn_costo,id_plan_stripe='$id_plan_stripe',id_suscripcion_stripe='$id_suscripcion_stripe',fe_periodo_inicial='$fe_actual',fe_periodo_final='$fe_periodo_final_todos_anual'  ";
					if($fe_periodo_final_mes){
						$Query.=", fe_periodo_final_mes='$fe_periodo_final' ";
					}else{
						$Query.=", fe_periodo_final_mes='' ";
					}
					$Query.="WHERE fl_current_plan_alumno=$fl_current_plan_alumno ";
					EjecutaQuery($Query);

					$mn_costo=number_format($mn_costo,2);

					#Se genera sus pagos correspondientes.  
					$Query="INSERT INTO k_admin_pagos_alumno (fl_current_plan_alumno,mn_total,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion,id_invoice_stripe,id_pago_stripe,mn_subtotal,mn_tax)";
					$Query.="VALUES($fl_current_plan_alumno,$mn_costo,'$fe_actual','$fe_periodo_final','1',CURRENT_TIMESTAMP,'$Idplan','$id_invoice','$id_charge',$mn_monto_sin_tax_DB,$mn_tax )";
					$fl_adm_pagos=EjecutaInsert($Query);
				
				
				}
			

            }

			
			
			
			
                                      
                                            
    } 



	//$fe_actual="2018-08-30";
  
    #Verificamos todos los alumno que ya tiene un plan por que adquiieron un curso solamente, atraves de un pago unico.
    $Query2="SELECT fl_alumno,fl_programa_sp,fe_periodo_inicial,fe_periodo_final,ds_email FROM 
             k_plan_curso_alumno A 
             JOIN c_usuario U ON U.fl_usuario=A.fl_alumno  
             WHERE 1=1 ";
    $rs2 = EjecutaQuery($Query2);
	for($i2=1;$row=RecuperaRegistro($rs2);$i2++){
        
              $fl_alumno=$row[0];
              $fl_programa_sp=$row[1];
              $fe_periodo_inicial=$row[2];
              $fe_periodo_final=$row[3];
              $ds_email_alumno=str_texto($row[4]);
              
              #Verificamos cuantos dias comprende su periodo trial y de ahi calculamos la mita y 
              $no_dias_que_tiene_plan=ObtenDiasRestantesPlan($fe_periodo_final,$fe_periodo_inicial);
              
              #Caluclamos la mitad d elos dia para enviar email a mitad de periodo.
              $no_dias_mitad=  ceil( $no_dias_que_tiene_plan / 2 );
              
			  //echo"<script>alert('fuaaa   $no_dias_mitad');</script>";
			  
              
              #Verificamos en que porcentaje se encuentra su periodo final de aviso
              $no_dias_restantes_plan=ObtenDiasRestantesPlan($fe_periodo_final,$fe_actual);
              
              

              
              
              
              
              #Se genera un email de confirmacion al alumno que recibe el curso 
              $ds_encabezado = genera_documento_sp($fl_alumno,1,148,$fl_programa_sp);
              $ds_cuerpo = genera_documento_sp($fl_alumno,2,148,$fl_programa_sp);
              $ds_pie = genera_documento_sp($fl_alumno,3,148,$fl_programa_sp);
              $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;                      

              $ds_contenido = str_replace("#fame_number_of_days#", $no_dias_restantes_plan, $ds_contenido);  #no_dias_cuso
              $ds_contenido = str_replace("#fame_pg_name#", "".ObtenEtiqueta(2118)."", $ds_contenido);  #no_dias_cuso

              #Recuperamos el titulo del documento email
              $Query="SELECT nb_template FROM k_template_doc WHERE fl_template=148 ";
              $row=RecuperaValor($Query);
              $ds_titulo=str_texto($row[0]);
              
              $ds_email_de_quien_envia_mensaje=$from;#ObtenConfiguracion(4);
              $nb_nombre_envia_email=ObtenEtiqueta(949);#nombre de quien envia el mensaje
              $bcc=ObtenConfiguracion(107);
              $message  = $ds_contenido;
              $message = utf8_decode(str_ascii(str_uso_normal($message)));
              
              #Anticipo antes de finalizar su periodo. son 30 dias.
              $no_dias_enviar=ObtenConfiguracion(130);
              
			  //echo"<script>alert(' $no_dias_restantes_plan  fuaaa   $no_dias_mitad');</script>";
			  
              
              #Envi email de notificacion cuando el peiodo ya es la mitad.
              if($no_dias_mitad==$no_dias_restantes_plan){
              
                  #Envia email de notificcion al usuario
                  $mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_alumno, $ds_titulo, $message, $bcc);
                  $dias_anticipo=$no_dias_mitad;	

              }
              if($no_dias_restantes_plan==$no_dias_enviar){
              
                  #Envia email de notificcion al usuario
                  $mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_alumno, $ds_titulo, $message, $bcc);
                  $dias_anticipo=$no_dias_enviar;

              }

              
              if($mail)
                  $fg_estatus=1;
              else
                  $fg_estatus=0;

              #Se genera un registro en la BD para saber si se ejecuto el cron, y que fue exctmene lo que hizo.
              $Query  ="INSERT INTO k_fame_cron_job_students_plan (ds_email_envio,ds_email_destinatario,fg_status,fl_alumno,fe_ejecucion,no_dias_anticipo,ds_tipo_cron)";
              $Query .="VALUES('$from','$ds_email_alumno','$fg_estatus',$fl_alumno,CURRENT_TIMESTAMP,$dias_anticipo,'Plan Por Curso')";
              $fl_registro=EjecutaInsert($Query);
              
              
              
              #Enviara email de que el curso ha expirado. 
              if($fe_periodo_final < $fe_actual){
                 
                  
                  
                
                    #Verificamos si ya exuste e registro y si ya pues ya no manda nada.
                    $Query="SELECT COUNT(*)FROM k_fame_cron_job_students_plan WHERE fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp ";
                    $row=RecuperaValor($Query);
                    $existe=$row[0];
                
                    if(empty($existe)){
                  
                  
                  
                          #Se genera un email de confirmacion al alumno que recibe el curso 
                          $ds_encabezado = genera_documento_sp($fl_alumno,1,149,$fl_programa_sp);
                          $ds_cuerpo = genera_documento_sp($fl_alumno,2,149,$fl_programa_sp);
                          $ds_pie = genera_documento_sp($fl_alumno,3,149,$fl_programa_sp);
                          $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;                      
                  
                          $fe_expiracion_curso=GeneraFormatoFecha($fe_periodo_final);

                          $ds_contenido = str_replace("#fame_expiration_course#", $fe_expiracion_curso, $ds_contenido);
        
                          #Envia email de notificcion al usuario
                          $mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_alumno, $ds_titulo, $message, $bcc);
                          $dias_anticipo=0;	

                          #Se genera su registro de que lo enviara solo una vez.
                          $Query  ="INSERT INTO k_fame_cron_job_students_plan (ds_email_envio,ds_email_destinatario,fg_status,fl_alumno,fe_ejecucion,no_dias_anticipo,ds_tipo_cron,fl_programa_sp)";
                          $Query .="VALUES('$from','$ds_email_alumno','1',$fl_alumno,CURRENT_TIMESTAMP,0,'ExpirationDate',$fl_programa_sp)";
                          $fl_registro=EjecutaInsert($Query); 
                  
                    }
                  
              } 
              
              
              
              
        
    }
    
    
	
	
	
	
	
	
	#Verificamos todos los alumno que ya tiene un plan free trial por que adquiieron uncurso atravez del envio de invitaciones.
    $Query2="SELECT fl_alumno,fl_programa_sp,fe_periodo_inicial,fe_periodo_final,ds_email FROM 
             k_periodo_trial_curso_alumno A 
             JOIN c_usuario U ON U.fl_usuario=A.fl_alumno  
             WHERE 1=1 ";
    $rs2 = EjecutaQuery($Query2);
	for($i2=1;$row=RecuperaRegistro($rs2);$i2++){
	
        
            $fl_alumno=$row[0];
            $fl_programa_sp=$row[1];
            $fe_periodo_inicial=$row[2];
            $fe_periodo_final=$row[3];
            $ds_email_alumno=str_texto($row[4]);
        
            #Verificamos cuantos dias comprende su periodo trial y de ahi calculamos la mita y 
            $no_dias_que_tiene_plan=ObtenDiasRestantesPlan($fe_periodo_final,$fe_periodo_inicial);
        
            #Caluclamos la mitad d elos dia para enviar email a mitad de periodo.
            $no_dias_mitad=  ceil( $no_dias_que_tiene_plan / 2 );
        
        
            #Verificamos en que porcentaje se encuentra su periodo final de aviso
            $no_dias_restantes_plan=ObtenDiasRestantesPlan($fe_periodo_final,$fe_actual);
        
        
            
            
            #Se genera un email de confirmacion al alumno que recibe el curso 
            $ds_encabezado = genera_documento_sp($fl_alumno,1,164,$fl_programa_sp);
            $ds_cuerpo = genera_documento_sp($fl_alumno,2,164,$fl_programa_sp);
            $ds_pie = genera_documento_sp($fl_alumno,3,164,$fl_programa_sp);
            $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;                      

            $ds_contenido = str_replace("#fame_number_of_days#", $no_dias_restantes_plan, $ds_contenido);  #no_dias_cuso
            $ds_contenido = str_replace("#fame_pg_name#", "".ObtenEtiqueta(2118)."", $ds_contenido);  #no_dias_cuso

            #Recuperamos el titulo del documento email
            $Query="SELECT nb_template FROM k_template_doc WHERE fl_template=164 ";
            $row=RecuperaValor($Query);
            $ds_titulo=str_texto($row[0]);
            
            $ds_email_de_quien_envia_mensaje=$from;#ObtenConfiguracion(4);
            $nb_nombre_envia_email=ObtenEtiqueta(949);#nombre de quien envia el mensaje
            $bcc=ObtenConfiguracion(107);
            $message  = $ds_contenido;
            $message = utf8_decode(str_ascii(str_uso_normal($message)));
            
            
            $no_dias_enviar=ObtenConfiguracion(130);#anticipo antes de finalizar su periodo
    
	
            
            #Envi email de notificacion cuando el peiodo ya es la mitad.
            if($no_dias_mitad==$no_dias_restantes_plan){
                
                #Envia email de notificcion al usuario
                $mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_alumno, $ds_titulo, $message, $bcc);
                $dias_anticipo=$no_dias_mitad;	

            }
            if($no_dias_restantes_plan==$no_dias_enviar){
                
                #Envia email de notificcion al usuario
                $mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_alumno, $ds_titulo, $message, $bcc);
                $dias_anticipo=$no_dias_enviar;

            }
            //$mail=true;
            
            if($mail)
                $fg_estatus=1;
            else
                $fg_estatus=0;

            #Se genera un registro en la BD para saber si se ejecuto el cron, y que fue exctmene lo que hizo.
            $Query  ="INSERT INTO k_fame_cron_job_students_plan (ds_email_envio,ds_email_destinatario,fg_status,fl_alumno,fe_ejecucion,no_dias_anticipo,ds_tipo_cron)";
            $Query .="VALUES('$from','$ds_email_alumno','$fg_estatus',$fl_alumno,CURRENT_TIMESTAMP,$dias_anticipo,'Plan Por Curso Trial Curso')";
            $fl_registro=EjecutaInsert($Query);
            
            
            
            #Enviara email de que el curso ha expirado. 
            if($fe_periodo_final < $fe_actual){
                
                
                
                
                #Verificamos si ya exuste e registro y si ya pues ya no manda nada.
                $Query="SELECT COUNT(*)FROM k_fame_cron_job_students_plan WHERE fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND ds_tipo_cron='ExpirationDate' ";
                $row=RecuperaValor($Query);
                $existe=$row[0];
                
                if(empty($existe)){
                    
                    
                    
                    #Se genera un email de confirmacion al alumno que recibe el curso 
                    $ds_encabezado = genera_documento_sp($fl_alumno,1,149,$fl_programa_sp);
                    $ds_cuerpo = genera_documento_sp($fl_alumno,2,149,$fl_programa_sp);
                    $ds_pie = genera_documento_sp($fl_alumno,3,149,$fl_programa_sp);
                    $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;                      
                    
                    $fe_expiracion_curso=GeneraFormatoFecha($fe_periodo_final);

                    $ds_contenido = str_replace("#fame_expiration_course#", $fe_expiracion_curso, $ds_contenido);
                    
                    #Envia email de notificcion al usuario
                    $mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_alumno, $ds_titulo, $message, $bcc);
                    $dias_anticipo=0;	

                    #Se genera su registro de que lo enviara solo una vez.
                    $Query  ="INSERT INTO k_fame_cron_job_students_plan (ds_email_envio,ds_email_destinatario,fg_status,fl_alumno,fe_ejecucion,no_dias_anticipo,ds_tipo_cron,fl_programa_sp)";
                    $Query .="VALUES('$from','$ds_email_alumno','1',$fl_alumno,CURRENT_TIMESTAMP,0,'ExpirationDate',$fl_programa_sp)";
                    $fl_registro=EjecutaInsert($Query); 
                    
                }
                
            } 
            
	
	
    
    
    
	}
	
	
	
	
	
	
    #{\__/}
	#( - > -)
	#    ~
	#/つ 
    #######################################
	#Para cancelar los planes de los curso que eleigieron un plan mensual.
	#Verificamos todos los alumno que ya tiene un plan free trial por que adquiieron uncurso atravez del envio de invitaciones.
    $Query3="SELECT fl_usuario,fl_current_plan,fe_cancelacion,fe_creacion FROM 
             k_cancelacion_plan_alumno               
             WHERE 1=1 ";
    $rs3 = EjecutaQuery($Query3);
	for($i2=1;$row3=RecuperaRegistro($rs3);$i3++){
	        $fl_usuario=$row3[0];
			$fl_current_plan=$row3[1];
			$fe_cancelacion=$row3[2];
			$fe_creacion=$row3[3];

			
			
	       if($fe_actual==$fe_cancelacion){
		   
				
		   
				#verificamos datos de stripe con el plan del alumno
		        $Query="SELECT fl_alumno,fl_current_plan_alumno,id_cliente_stripe,id_plan_stripe,id_suscripcion_stripe,ds_email_stripe       
						FROM k_current_plan_alumno WHERE fl_current_plan_alumno=$fl_current_plan  ";
				$row=RecuperaValor($Query);
				$id_cliente_stripe=str_texto($row['id_cliente_stripe']);
				$id_plan_stripe=str_texto($row['id_plan_stripe']);
				$id_suscripcion_stripe=str_texto($row['id_suscripcion_stripe']);
				$ds_email_stripe=str_texto($row['ds_email_stripe']);
				
		        
		        
				#cancelamos el plan actual.
				#1.Elimina Plan actual
                
                #1.Se cancela la suscripcion actual.
                $subscription = \Stripe\Subscription::retrieve($id_suscripcion_stripe);
                $subscription->cancel();
                
                #2.Se elimina el plan.
                $plan = \Stripe\Plan::retrieve($id_plan_stripe);
                $plan->delete();
				
				EjecutaQuery("UPDATE k_current_plan_alumno SET fg_cancelado='1' WHERE fl_current_plan_alumno=$fl_current_plan ");
				
			
		   
		   
		   }

	}
	
	
	
	}catch (\Stripe\Error\ApiConnection $e) {
		// Network problem, perhaps try again.
		$e_json = $e->getJsonBody();
		$err = $e_json['error'];
		$result['error'] = $err['message'];
	  } catch (\Stripe\Error\InvalidRequest $e) {
		// You screwed up in your programming. Shouldn't happen!
		$e_json = $e->getJsonBody();
		$err = $e_json['error'];
		$result['error'] = $err['message'];
	  } catch (\Stripe\Error\Api $e) {
		// Stripe's servers are down!
		$e_json = $e->getJsonBody();
		$err = $e_json['error'];
		$result['error'] = $err['message'];
	  } catch (\Stripe\Error\Base $e) {
		// Something else that's not the customer's fault.
		$e_json = $e->getJsonBody();
		$err = $e_json['error'];
		$result['error'] = $err['message'];
	  }
	  
	  
	  
	  
  echo json_encode((Object) $result);	  
	  
function GeneraLog($file_name_txt,$contenido_log=''){
	
	$fch= fopen($file_name_txt, "a+"); // Abres el archivo para escribir en él
	fwrite($fch, "\n".$contenido_log); // Grabas
	fclose($fch); // Cierras el archivo.

}


	
	
	
	
	
	
?>
