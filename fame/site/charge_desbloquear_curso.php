<?php
  # Libreria de funciones
  require("../lib/self_general.php");
  # Include the Stripe library
  require_once('../lib/Stripe/Stripe/init.php');
  
  # Obtiene usuario
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto=ObtenInstituto($fl_usuario);
  
  # Recibe los parametros
  $token = RecibeParametroHTML('stripeToken');        
  $mn_amount = RecibeParametroNumerico('mn_amount'); // si existe tax ya lo agreamos al monto total
  $mn_tax = RecibeParametroNumerico('mn_tax');
  $name = RecibeParametroHTML('cardholder-name');
  $currency = RecibeParametroHTML('currency');
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp');
  $ds_descripcion=RecibeParametroHTML('ds_descripcion_pago');
  $nb_titular_tarjeta=RecibeParametroHTML('cardholder-name');
  $fg_plan_curso=RecibeParametroHTML('fg_plan_curso'); 
  $fg_cupon=RecibeParametroHTML('fg_cupon');
  $fl_cupon=RecibeParametroNumerico('fl_cupon');
  $fg_tipo_descuento=RecibeParametroHTML('fg_tipo_descuento');
  $fg_plan_seleccionado=RecibeParametroHTML('fg_plan_seleccionado');//pago_unico|plan mes| plan anual.
   
  # Variables Stripe
  $secret_key = ObtenConfiguracion(112);
  
  // create the charge on Stripe's servers - this will charge the user's card
	try {
  # set your secret key: remember to change this to your live secret key in production
  # see your keys here https://manage.stripe.com/account
  \Stripe\Stripe::setApiKey($secret_key);
  
   
  if($fg_cupon){

    #Recupermaos el ID del Cupon Code.
    $Query="SELECT nb_cupon,ds_code,ds_descuento,mn_cantidad,fe_end,fg_activo FROM c_cupones_b2c WHERE fl_cupon=$fl_cupon ";
    $row=RecuperaValor($Query);
    $mn_porcentaje_cupon=$row['mn_cantidad'];
    $id_cupon=str_texto($row['ds_code']);

   if(($fg_plan_seleccionado==1)||($fg_plan_seleccionado==2)){
   
        if($fg_tipo_descuento=='C'){
          $mn_tax_aplicar=$mn_tax/100;
         

          
        }
   
   }
    
    
  }
  
  

   
   
  
  if(!empty($fl_programa_sp)){
  
    $row = RecuperaValor("SELECT ds_nombres, ds_apaterno, ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario");
    $nb_cliente = str_texto($row[0])." ".str_texto($row[1]);
    $ds_fname_cliente=str_texto($row[0]);
    $ds_lname_cliente=str_texto($row[1]);
    $email_cliente =  $row[2];
    
    
    
            if($fg_plan_curso){ #Quiere decir que pago un plan de liberacion de cursos.
            
                    
                        #Obtenemos fecha actual :
                        $Query = "Select CURDATE() ";
                        $row = RecuperaValor($Query);
                        $fe_actual = str_texto($row[0]);
                        $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                        $fe_actual= date('Y-m-d',$fe_actual); 
                       
                        
                        $mn_tax=$mn_tax/100;
                
                        #Convertimosel monto para guardarlo en DB
                        $mn_monto_normal=(($mn_amount/100)-$mn_tax);
                        $mn_total_plan=$mn_monto_normal+ $mn_tax;
                        
                        #Para enviar a stripe
                        $mn_monto_sin_tax=Conv_Dollars_Stripe($mn_monto_normal);
                        
                       
                       
                        
                        
                        
                        $Query="SELECT ds_descripcion FROM c_plan_fame WHERE cl_plan_fame=1  ";
                        $row=RecuperaValor($Query);
                        $ds_plan=str_texto($row[0]);
                        
                        
                        $rand=rand(5,1000);
                        
                        #Recupermaos el plan mes y año para el curso 
                        $Query="SELECT ds_descuento_mensual, ds_descuento_anual,mn_mensual,mn_anual FROM c_princing_course WHERE 1=1 ";
                        $row=RecuperaValor($Query);
                        $porcentaje_mes=number_format($row['ds_descuento_mensual']); 
                        $porcentaje_anio=number_format($row['ds_descuento_anual']);
                        //$mn_mes=$row['mn_mensual'];
                        //$mn_anio=$row['mn_anual'];
                        
                        
                        
                       if($fg_plan_curso==1){ #Mes
                          $fg_plan="M";
                          
                          #Se asignan nombres para crear plan en strippe
                          $interval="month";
                          $nb_plan=$ds_plan."_".$ds_fname_cliente."_".$ds_lname_cliente."_".ObtenEtiqueta(1705)."_".$rand;
                          $id_plan=$ds_plan."_".$ds_fname_cliente."_".$ds_lname_cliente."_".ObtenEtiqueta(1705)."_".$rand;
                          
                          #se calcula fecha de termino del plan por mes.
                          $fe_periodo_final=strtotime('+1 year',strtotime($fe_actual));
                          $fe_periodo_final= date('Y-m-d',$fe_periodo_final);
                          
                          $mn_porcentaje_descuento=$porcentaje_mes;

                          $fe_periodo_final_mes=strtotime('+30 day',strtotime($fe_actual));
                          $fe_periodo_final_mes=date('Y-m-d',$fe_periodo_final_mes);
                          
                       }
                       
                       if($fg_plan_curso==2){#Anio
                          $fg_plan="A";
                       
                          #Se asignan nombres para crear plan en strippe
                          $interval="year";
                          $nb_plan=$ds_plan."_".$ds_fname_cliente."_".$ds_lname_cliente."_".ObtenEtiqueta(1706)."_".$rand;
                          $id_plan=$ds_plan."_".$ds_fname_cliente."_".$ds_lname_cliente."_".ObtenEtiqueta(1706)."_".$rand;
 
                          #se calcula fecha de termino del plan por mes.
                          $fe_periodo_final=strtotime('+1 year',strtotime($fe_actual));
                          $fe_periodo_final= date('Y-m-d',$fe_periodo_final);
                          
                          $mn_porcentaje_descuento=$porcentaje_anio;
                          $fe_periodo_final_mes="";

                       }
                       
                       
                       
					   
					   
                       #Creamos al cliente
                       $customer = \Stripe\Customer::create(array(
                         "email" => $email_cliente,
                         "description" => $ds_fname_cliente."_".$ds_lname_cliente,
                         "source" => $token,
                       ));
                       
                       $id_cliente_stripe=$customer->id;
                       $ds_email_stripe=$customer->email;
                       
                       $id_pago=$customer->sources;
                       $fe_anio_expiracion_tarjeta=$id_pago['data']['0']->exp_year;
                       $fe_mes_expiracion_tarjeta=$id_pago['data']['0']->exp_month;
                       
                       $no_tarjeta_pago=$id_pago['data']['0']->last4;
                       $tipo_tarjeta=$id_pago['data']['0']->brand;
                       
                       
                       #1.Creamos su plan de pago,propio por cliente/Instituto, con el tax incluido
                       $plan = \Stripe\Plan::create(array(
                          "name" => $nb_plan,
                          "id" => $id_plan,
                          "interval" => $interval,
                          "currency" => $currency,
                          "amount" => $mn_monto_sin_tax
                          )
                        );
                       
                       $id_plan_stripe=$plan->id;
                       
                       
                       #Recuperamos el estado/provincia del usuario para determina el monto del tax.
                       $mn_porcentaje_tax=Tax_Can_User($fl_usuario);
                       $mn_porcentaje_tax_=$mn_porcentaje_tax*100;
                       
                       $porcentaje_tax=$mn_tax/100;
                       
                       
                            $subscription= \Stripe\Subscription::create(array(
                                     "customer" => $customer->id,
                                     "plan" => $id_plan_stripe,
                                     "quantity"=>1,
                                     "tax_percent" => $mn_porcentaje_tax_,
                                     
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
                       $ch->description = $nb_plan;
                       $ch->save();
                       
                       
                       
                       
                       
                       #Realizamos el descuento
                       if($fl_cupon){
                       
                           
                            /* if($fg_tipo_descuento=='C'){//Cantidad
                             
                                 $mn_ta_x=$mn_tax_aplicar;
                                 
                                 $mn_monto_normal_=$mn_amount/100;
                                 $mn_monto_normal_sin_tax=$mn_monto_normal_-$mn_tax_aplicar;
                                 
                                 $mn_con_descuento= $mn_porcentaje_cupon;
                                 $mn_monto_con_cupon=number_format($mn_monto_normal_- $mn_con_descuento,2);
                                 $mn_descuento=$mn_porcentaje_cupon;
                                 $mn_total_plan=$mn_monto_con_cupon; 
                                 $mn_monto_normal=$mn_monto_normal_sin_tax;
                                 
                                 
                                 
                                 //$mn_con_descuento=$mn_porcentaje_cupon;
                                 //$mn_total_plan=number_format($mn_monto_normal_-$mn_con_descuento,2);
                                 //$mn_monto_normal=$mn_total_plan-$mn_tax;
                             }
                             if($fg_tipo_descuento=='P'){//Porcentaje
                                 
                                 
                                 $mn_monto_normal_ = $mn_amount/100;
                                 $mn_monto_normal_sin_tax=$mn_monto_normal_-$mn_tax;
                                 
                                 $mn_con_descuento=($mn_monto_normal_ * $mn_porcentaje_cupon)/100;
                                 $mn_monto_con_cupon=number_format($mn_monto_normal_- $mn_con_descuento,2);
                                 $mn_descuento=$mn_porcentaje_cupon;
                                 
                                 
                                 $mn_total_plan=$mn_monto_con_cupon; 
                                 $mn_monto_normal=$mn_monto_normal_sin_tax;
                                 
                                
                                  
                             }
                           */
                           
                          
                           
                        }else{
                       
                             $mn_porcentaje_cupon=0;
                        }
                       
                       
                       
                       
                       #Se genera su plan del alumno.
                       $Query="INSERT INTO k_current_plan_alumno(fl_alumno,cl_plan_fame,fg_plan,fe_periodo_inicial,fe_periodo_final,mn_total_plan,fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta,id_cliente_stripe,id_suscripcion_stripe,id_plan_stripe,ds_email_stripe,id_cupon_stripe,no_tarjeta,ds_tipo ";
                       if(!empty($fe_periodo_final_mes))
                       $Query.=" ,fe_periodo_final_mes ";
                       $Query.=" )";
                       $Query.="VALUES($fl_usuario,1,'$fg_plan','$fe_actual','$fe_periodo_final',$mn_total_plan, $fe_mes_expiracion_tarjeta,$fe_anio_expiracion_tarjeta,'$id_cliente_stripe','$id_suscripcion_stripe','$id_plan_stripe','$ds_email_stripe','$id_cupon',$no_tarjeta_pago,'$tipo_tarjeta' ";
                       if(!empty($fe_periodo_final_mes))
                       $Query.=" ,'$fe_periodo_final_mes'";
                       $Query.=" )";
                       $fl_current_plan=EjecutaInsert($Query);
            
                       #Se genera sus pagos correspondientes.  
                       $Query ="INSERT INTO k_admin_pagos_alumno (fl_current_plan_alumno,mn_total,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion,id_invoice_stripe,id_pago_stripe,mn_subtotal,mn_tax,mn_descuento,mn_porcentaje_cupon ";
                       if(!empty($fg_tipo_descuento))
                       $Query.=" ,fg_tipo_descuento "; 
                       $Query.=" ) ";
                       $Query.="VALUES($fl_current_plan,$mn_total_plan,'$fe_actual','$fe_periodo_final','1',CURRENT_TIMESTAMP,'$nb_plan','$id_invoice','$id_charge',$mn_monto_normal,$mn_tax,$mn_porcentaje_descuento,$mn_porcentaje_cupon ";
                       if(!empty($fg_tipo_descuento))
                       $Query.=", '$fg_tipo_descuento' ";
                       $Query.=" ) ";
                       $fl_adm_pagos=EjecutaInsert($Query);
                       
                       
                       if($fl_cupon){
                           
                           #Se genera la bitacora de cupones.     
                           $Query="INSERT INTO k_uso_cupones_alumno (fl_cupon,fl_usuario,fl_admin_pagos_alumno,fl_programa_sp,ds_codigo_cupon,fg_tipo_pago,mn_total,mn_tax,fe_creacion)";
                           $Query.="VALUES($fl_cupon,$fl_usuario,$fl_adm_pagos,$fl_programa_sp,'$id_cupon','$fg_plan',$mn_total_plan,$mn_tax,CURRENT_TIMESTAMP )";
                           $fl_uso_cupones=EjecutaInsert($Query);
                       
                       }
                       
                       
                       
                       #Recuperamos todos los cursos  y se los asignamos al estudiante.
                       AsignarTodosLosCursosAlAlumno($fl_usuario);
                       
                       
                       
                       
                       
                       
                       
                       
                       # Check that it was paid of stripe
                       if ($id_charge) {
                       
                           $result["paid"] = true; 
                           
                           $result["message"] = "<script>
                                                $(document).ready(function() {
                                                    $('#presenta_gif').addClass('hidden');
                                                });
                                                </script> 
                                                <a class='btn btn-success btn-sm hidden' href='index.php#site/node.php?node=158' id='redirigir_course'><i class='fa fa-upload'></i> redirige</a>
                                                          
                                                <div class='text-center error-box'>
                                                    <h5> <i class='fa fa-check-circle  text-success success-icon-shadow'></i> <strong>".ObtenEtiqueta(1739)."</strong></h5>
                                                </div>
                                                        
                                                        
	                                            <script>
                                                //le asignamo un retardo para confirmar nuestro pago
                                                setTimeout(function(){ 
                                                location.reload();///document.getElementById('redirigir_course').click();//clic au   
                                                }, 3000);
                                            </script>
                                              ";
                           
                           
                           
                       }else{
                       
                           $result["paid"] = false;
                       }
            
            
            
            }else{
    
                       #SE GENERA UN PAGO UNCIO POR ESE CURSO Y SE DESBLOQUEA SOLO ESE CURSO POR UN AÑO.
                   /*     if($fg_tipo_descuento=='C'){//Cantidad
                    
                           
                        
                                $mn_monto_normal=$mn_amount/100;
                                $mn_con_descuento=$mn_porcentaje_cupon;
                                $mn_monto_con_cupon=number_format($mn_monto_normal-$mn_con_descuento,2);
                                $mn_descuento=$mn_porcentaje_cupon;
                                $mn_amount=Conv_Dollars_Stripe($mn_monto_con_cupon);
                          
                        }
                        if($fg_tipo_descuento=='P'){//Cantidad
                            
                            
                                #Realizamos calculos del cupon. y lo converimos a monto aceptados por stripe.
                                $mn_monto_normal=$mn_amount/100; 
                                $mn_con_descuento= ($mn_monto_normal*$mn_porcentaje_cupon)/100;
                                $mn_monto_con_cupon=number_format($mn_monto_normal-$mn_con_descuento,2);
                                $mn_descuento=$mn_porcentaje_cupon;
                                $mn_amount=Conv_Dollars_Stripe($mn_monto_con_cupon);
                
                        }
                
                */
                
                
    
                        # Crea cliente
                        $customer = \Stripe\Customer::create(array(
                          "email" => $email_cliente,
                          "description" => $nb_cliente,
                          "source" => $token,
                        ));
  
                        # Charge the order:
                        $charge = \Stripe\Charge::create(array(
                          "amount" => $mn_amount,
                          "currency" => $currency,
                          "customer" => $customer->id,
                          "description" => $ds_descripcion,      
                          "metadata" => array("tax" => ($mn_tax/100))
                          )
                        );
      
                        $id_charge= $charge->id;
                        $id_customer=$customer->id;
    
                        
                        $id_pago=$customer->sources;
                        $fe_anio_expiracion_tarjeta=$id_pago['data']['0']->exp_year;
                        $fe_mes_expiracion_tarjeta=$id_pago['data']['0']->exp_month;
                        
                        $no_tarjeta_pago=$id_pago['data']['0']->last4;
                        $tipo_tarjeta=$id_pago['data']['0']->brand;
                        
                        
                        
                        # Check that it was paid of stripe
                        if ($charge->paid == true) {
        
        
                            #Verificamos el envio de email si existe y eliminamos registros basura que haya enviado este alumno relacionado con este curso.
                            $Query="SELECT B.fl_envio_correo 
                                    FROM c_desbloquear_curso_alumno A
			                        JOIN k_envio_email_reg_selfp B ON A.fl_envio_correo=B.fl_envio_correo
			                        WHERE A.fl_invitado_por_usuario=$fl_usuario AND A.fl_programa_sp=$fl_programa_sp ";
                             $rs = EjecutaQuery($Query);
	                         for($i = 1; $row = RecuperaRegistro($rs); $i++) {
             
                                   EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$row[0] ");
                                   EjecutaQuery("DELETE FROM c_desbloquear_curso_alumno WHERE fl_envio_correo=$row[0] ");
             
             
                             }

       
        
                            #Verificamos el precio del programa.
                            $Query="SELECT mn_precio,no_dias_pago FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
                            $row=RecuperaValor($Query);
                            $mn_precio=$row['mn_precio'];
                            $no_dias_pago=$row['no_dias_pago'];
        
                            if(empty($mn_precio))
                                $mn_precio=ObtenConfiguracion(123);
                            if(empty($no_dias_pago))
                                $no_dias_pago=ObtenConfiguracion(128);
        
                            #Liberamos el curso el curso del alumno.  
                            #buscamos un teacher activo y se lo asignamos esto es aleatoriamente.
                            $Query="SELECT fl_usuario FROM c_usuario WHERE fl_instituto=4 AND fl_perfil_sp=".PFL_MAESTRO_SELF." AND fg_activo='1' ORDER BY RAND() LIMIT 1 ";
                            $ru=RecuperaValor($Query);
                            $fl_maestro=$ru['fl_usuario'];
                            $fl_maestro="642";//Por default Mario Pochat.
                            
                            #Verifica que no exista el registro.
                            $Query="SELECT COUNT(*) FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa_sp ";
                            $rw=RecuperaValor($Query);
                            $no_reg=$rw[0];
                            
                           
                            
                            if(empty($no_reg)){
                            
                            $Query ="INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fg_certificado,fg_status,fg_pagado,mn_pagado,fl_maestro,fg_status_pro,fe_inicio_programa) ";
                            $Query.="VALUES($fl_usuario,$fl_programa_sp,0,'0','0','RD','0',0,$fl_maestro,'0',CURRENT_TIMESTAMP)";
                            $fl_usu_pro=EjecutaInsert($Query);
        
                            # Por defaul indicamos que tendran una calificacion de quiz
                            EjecutaQuery("INSERT INTO k_details_usu_pro (fl_usu_pro,fg_quizes,fg_grade_tea) VALUES ($fl_usu_pro,'1','0')");

                            
                            
                            #Se genera el orden cronologico de desbloqueo.
                            $Quert="SELECT no_orden FROM k_orden_desbloqueo_curso_alumno WHERE fl_alumno=$fl_usuario ORDER BY no_orden DESC ";
                            $fl=RecuperaValor($Quert);
                            $no_consecutiv=$fl[0] + 1 ;
                            
                            #Se genera su registro.
                            $fl_consecu=EjecutaInsert("INSERT INTO k_orden_desbloqueo_curso_alumno (fl_alumno,fl_programa_sp,no_orden, fe_creacion,fg_motivo )VALUES($fl_usuario , $fl_programa_sp,$no_consecutiv, CURRENT_TIMESTAMP,'PU') ");
                            
                            
                            
                            
                            
                            }
                           
                            
                           // if($fg_cupon){

                                    $mn_tax_db=$mn_tax/100;
                                    $mn_total=$mn_amount/100;
                                    $mn_precio=$mn_total-$mn_tax_db;

                           // }
                            
                            
                           
                            
                            
                            if($fg_cupon){
                               // $mn_total=$mn_monto_con_cupon; 
                                $mn_descuento=$mn_porcentaje_cupon;
                                
                                
                           }else
                                $mn_descuento=0;
                            
                            
                            #Caluclmaos elfinal de periodo de 1 año.
                            #Obtenemos fecha actual :
                            $Query = "Select CURDATE() ";
                            $row = RecuperaValor($Query);
                            $fe_actual = str_texto($row[0]);
                            $fe_actual=strtotime('+'.$no_dias_pago.' day',strtotime($fe_actual));
                            $fe_final_periodo= date('Y-m-d',$fe_actual);
                            
                            
                            #VERIFICAMOS SI NO EXITE UN PLAN RELACIONADO A ESTE CURSO.
                            $Uery="SELECT COUNT(*),fl_plan_curso_alumno FROM k_plan_curso_alumno WHERE fl_alumno=$fl_usuario AND fl_programa_sp=$fl_programa_sp  ";
                            $rows=RecuperaValor($Uery);
                            $existe_plan=$rows[0];
                            
                                
                            if($existe_plan){
                                   
                                EjecutaQuery("UPDATE k_plan_curso_alumno SET fe_periodo_inicial=CURRENT_TIMESTAMP, fe_periodo_final='$fe_final_periodo',fe_ulmod=CURRENT_TIMESTAMP WHERE fl_alumno=$fl_usuario AND fl_programa_sp=$fl_programa_sp ");
                                $fl_plan=$rows[1];    
                                
                                
                            }else{    
                                #Se genera sulan propio del curso-alumno
                                $Queryp="INSERT INTO k_plan_curso_alumno (fl_alumno,fl_programa_sp,fe_periodo_inicial,fe_periodo_final,fe_creacion,fe_ulmod )  ";
                                $Queryp.="VALUES ($fl_usuario,$fl_programa_sp,CURRENT_TIMESTAMP,'$fe_final_periodo',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP ) ";
                                $fl_plan=EjecutaInsert($Queryp);
                            }
                            
                            #Se inserta bitacora del pago.
                            $Query3="INSERT INTO k_pago_curso_alumno(fl_alumno_sp,fl_programa_sp,mn_total,ds_descripcion,id_pago,id_customer,mn_costo_curso,mn_tax,mn_descuento,fe_pago,fe_ulmod,fl_plan_curso_alumno,fg_tipo_descuento) ";
                            $Query3.="VALUES($fl_usuario,$fl_programa_sp,$mn_total,'$ds_descripcion','$id_charge','$id_customer',$mn_precio,$mn_tax_db,$mn_descuento,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_plan,'$fg_tipo_descuento' )";
                            $fl_pago=EjecutaInsert($Query3);
        
                            
                            #ELEIMINAMOS LA TARHJETA Y LA VOLVEMOS A INSERTAR
                            EjecutaQuery("DELETE FROM k_alumno_tarjeta WHERE fl_usuario=".$fl_usuario." ");
                            
                            #Generamos el registro de su tarjeta. 
                            $Query="INSERT INTO k_alumno_tarjeta(fl_usuario,no_tarjeta,ds_tipo,fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta,id_cliente_stripe,ds_email_stripe )";
                            $Query.="VALUES($fl_usuario,$no_tarjeta_pago,'$tipo_tarjeta','$fe_mes_expiracion_tarjeta','$fe_anio_expiracion_tarjeta','$id_customer','$email_cliente') ";
                            $fl_tareta=EjecutaInsert($Query);
                            
                            
                            if($fl_cupon){
                                
                                #Se genera la bitacora de uso cupones.     
                                $Query="INSERT INTO k_uso_cupones_alumno (fl_cupon,fl_usuario,fl_pago_curso_alumno,fl_programa_sp,ds_codigo_cupon,fg_tipo_pago,mn_total,mn_tax,fe_creacion)";
                                $Query.="VALUES($fl_cupon,$fl_usuario,$fl_pago,$fl_programa_sp,'$id_cupon','U',$mn_total,$mn_tax_db,CURRENT_TIMESTAMP )";
                                $fl_uso_cupones=EjecutaInsert($Query);
                                
                            }
                            
        
                            #Se genera un email de confirmacion al alumno que recibe el curso 
                            $ds_encabezado = genera_documento_sp($fl_usuario,1,148,$fl_programa_sp);
                            $ds_cuerpo = genera_documento_sp($fl_usuario,2,148,$fl_programa_sp);
                            $ds_pie = genera_documento_sp($fl_usuario,3,148,$fl_programa_sp);
                            $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;
                            
                            #Recupermaos el email del usuario 
                            $Quer="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
                            $row=RecuperaValor($Quer);
                            $ds_email_destinatario=str_texto($row[0]);
                            
                            $ds_contenido = str_replace("#fame_number_of_days#", $no_dias_pago, $ds_contenido);  #no_dias_cuso
                            
                            #Recuperamos el titulo del documento
                            $Query="SELECT nb_template FROM k_template_doc WHERE fl_template=148 ";
                            $row=RecuperaValor($Query);
                            $ds_titulo=str_texto($row[0]);
                            
                            $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);
                            $nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje
                            $bcc=ObtenConfiguracion(107);
                            $message  = $ds_contenido;
                            $message = utf8_decode(str_ascii(str_uso_normal($message)));
                            #Envia email de notificcion al usuario
                            $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
                            
                            
                            
                            
                            
                            
        
                           # Notify user of send payment
                           $result["paid"] = true;
                           $result["message"] = "<script>
                                                $(document).ready(function() {
                                                    $('#presenta_gif').addClass('hidden');
                                                });
                                                </script> 
                                                <a class='btn btn-success btn-sm hidden' href='index.php#site/node.php?node=158' id='redirigir_course'><i class='fa fa-upload'></i> redirige</a>
                                                          
                                                <div class='text-center error-box'>
                                                    <h5> <i class='fa fa-check-circle  text-success success-icon-shadow'></i> <strong>".ObtenEtiqueta(1739)."</strong></h5>
                                                </div>
                                                        
                                                        
	                                            <script>
                                                //le asignamo un retardo para confirmar nuestro pago
                                                setTimeout(function(){ 
                                                location.reload();///document.getElementById('redirigir_course').click();//clic au   
                                                }, 3000);
                                            </script>
                                              ";
      
      
      
                                            # Informamos al administrador que el usuario desea un certificado valido    
     
     
      
      
      
                        } 
                        else { // Charge was not paid!
                          # Notify user of Error not payment
                          $result["paid"] = false;
                        }
    
    
                        
            }#end else tien plan              
                        
                        
                        
                        
  }

  
  
  
  
  
  
    $result['error'] = 0;
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
  
?>