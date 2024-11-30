<?php
  # Libreria de funciones
  require("../lib/self_general.php");
  require_once('../../AD3M2SRC4/lib/tcpdf/config/lang/eng.php');
  require_once('../../AD3M2SRC4/lib/tcpdf/tcpdf.php');
  # Include the Stripe library
  require_once('../lib/Stripe/Stripe/init.php');
  
  # Obtiene usuario
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Recibe los parametros
  $token = RecibeParametroHTML('stripeToken');        
  $mn_amount = RecibeParametroNumerico('mn_amount'); // si existe tax ya lo agreamos al monto total
  $mn_tax = RecibeParametroNumerico('mn_tax');
  $name = RecibeParametroHTML('cardholder-name');
  $currency = RecibeParametroHTML('currency');
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp');
  $ds_descripcion=RecibeParametroHTML('ds_descripcion_pago');
  $nb_titular_tarjeta=RecibeParametroHTML('cardholder-name');
   
  
  
   
  # Variables Stripe
  $secret_key = ObtenConfiguracion(112);
  
  // create the charge on Stripe's servers - this will charge the user's card
	try {
  # set your secret key: remember to change this to your live secret key in production
  # see your keys here https://manage.stripe.com/account
  \Stripe\Stripe::setApiKey($secret_key);
  

  function Customer($customer)
  {
      try {
          return $id= \Stripe\Customer::retrieve($customer);
      }
      catch (Exception $e) {
          return 0;
      }
  }	 

  function getPlan($plan_id)
  {
      try {
          return $id= \Stripe\Plan::retrieve($plan_id);
      }
      catch (Exception $e) {
          return 0;
      }
  }


  function getSubscripcion($id_suscripcion)
  {
      try {
          return $id= \Stripe\Subscription::retrieve($id_suscripcion);
      }
      catch (Exception $e) {
          return 0;
      }
  }

  # Gabo
  if(!empty($fl_programa_sp)){
  
    $row = RecuperaValor("SELECT ds_nombres, ds_apaterno, ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario");
    $nb_cliente = str_texto($row[0])." ".str_texto($row[1]);
    $email_cliente =  $row[2];
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
      
    # Check that it was paid of stripe
    if ($charge->paid == true) {
      # Notify user of send payment
      $result["paid"] = true;
      $result["message"] = "
      <div class='text-center error-box'>
        <h3 class='error-text tada animated' style='font-size:400%;'><i class='fa fa-check-circle  text-success success-icon-shadow'></i> ".ObtenEtiqueta(1883)."</h3>
        <h5><strong>".ObtenEtiqueta(1884)."</strong></h5>
        <br>
        <a type='button' class='btn btn-primary btn-lg btn-block' data-dismiss='modal' id='btn_final'>
          <i class='fa fa-check-circle'></i> close
        </a>
      </div>";
      # Informamos al administrador que el usuario desea un certificado valido    
      # Insertamos el registro del pedido
      $Query  = "UPDATE k_usuario_doc SET fg_card='1', fg_pagado='1' WHERE fl_usuario=$fl_usuario AND fl_programa=$fl_programa_sp AND fg_tipo_doc='2'";
      EjecutaQuery($Query);
      
      # Enviamos al administrador una notificacion
      # Inicializa variables de ambiente para envio de correo adjunto
      ini_set("SMTP", MAIL_SERVER);
      ini_set("smtp_port", MAIL_PORT);
      ini_set("sendmail_from", MAIL_FROM);

      # Separadores
      $eol = "\n";
      $separator = md5(time());
      
      // Envia al administrador
      $repEmail = MAIL_FROM;
      $admin = ObtenConfiguracion(107);
      
      # Headers
      $headers  = 'MIME-Version: 1.0' .$eol;
      $headers .= 'From: "'.$repEmail.'" <'.$repEmail.'>'.$eol;
      // Send copy to test
      $headers .= "Bcc: $admin \r\n";
      $headers .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
      
      # Message
      $p_message  = genera_documento_sp($fl_usuario, 1, 109, $fl_programa_sp);
      $p_message .= genera_documento_sp($fl_usuario, 2, 109, $fl_programa_sp);
      $p_message .= genera_documento_sp($fl_usuario, 3, 109, $fl_programa_sp);

      # Envia email
      mail($repEmail, ObtenEtiqueta(1171), $p_message, $headers);
      #Actualizamos el registros de que ya fue pagado y pidio su certificado el usuario
      $Query  = "UPDATE k_usuario_programa SET fg_certificado = '1',fg_status = 'RD', fg_pagado = '1', fe_enviado = NOW(), fe_pagado = NOW(), mn_pagado = ".($mn_amount/100)." ";
      $Query .= "WHERE fl_usuario_sp = $fl_usuario AND fl_programa_sp = $fl_programa_sp ";
      EjecutaQuery($Query);
      
    } 
    else { // Charge was not paid!
      # Notify user of Error not payment
      $result["paid"] = false;
    }
    
    
  }
  # Mike
  else{
  
  
 
      
	  
	     
            #Recibimos parametros
            $fl_instituto=ObtenInstituto($fl_usuario); 
            $fg_motivo_pago=RecibeParametroHTML('fg_motivo_pago');
            $fg_tipo_plan=RecibeParametroHTML('fg_tipo_plan'); 
            $nb_cliente =ObtenNombreUsuario($fl_usuario);  
            $no_licencias_compradas=RecibeParametroNumerico('no_licencias_compradas');
          
            
            #Recuperamos el estado/provincia del usuario para determina el monto del tax.
            $mn_porcentaje_tax=Tax_Can_User($fl_usuario);
            $mn_porcentaje_tax_=$mn_porcentaje_tax*100;
            #Recuperamos el email del cliente logeado/ para envio de email.
            $Query="Select ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
            $row=RecuperaValor($Query);
            $email_cliente=$row[0];
         
            //$email_cliente="19@gmail.com";
            
            
            
            
            
            
            
            
    
            if($fg_motivo_pago==PAGO_NEW_PLAN){
                
                
                
                                #Obtenemos fecha actual :
                                $Query = "Select CURDATE() ";
                                $row = RecuperaValor($Query);
                                $fe_actual = str_texto($row[0]);
                                $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                                $fe_actual= date('Y-m-d',$fe_actual);
   
                                $mn_tax=$mn_tax/100;
                                
                                #Convertimosel monto para guardarlo en DB
                                $mn_monto_normal=(($mn_amount/100)-$mn_tax);
                                $mn_total=$mn_monto_normal+ $mn_tax;

                                #Para enviar a stripe
                                $mn_monto_sin_tax=Conv_Dollars_Stripe($mn_monto_normal);
                                

                                #Recuperamos el Nombre del Istituto yel nombre del plan.
                                $Query2="SELECT ds_instituto,B.ds_descripcion 
                                         FROM c_instituto A
                                         LEFT JOIN c_plan_fame B ON A.cl_plan_fame=B.cl_plan_fame 
                                         WHERE fl_instituto=$fl_instituto ";
                                $row2=RecuperaValor($Query2);
                                $nb_instituto=str_texto($row2[0]);
                                $ds_plan=str_texto($row2[1]);
                                
                                
                                #Adquiere el plan essencial (temporal, es por deafault)
                                if(empty($ds_plan)){
                                    $Query="SELECT ds_descripcion FROM c_plan_fame WHERE cl_plan_fame=1  ";
                                    $row=RecuperaValor($Query);
                                    $ds_plan=str_texto($row[0]);
                                
                                }
                                
                                
                                $rand=rand(5,1000);
                
                                #identificamos en que rango se encuentra, e identificamos el tipo de plan.
                                $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
                                $rs = EjecutaQuery($Query);
                                for($i=1;$row=RecuperaRegistro($rs);$i++) {
                    
                                    $mn_rango_ini= $row['no_ini'];
                                    $mn_rango_fin= $row['no_fin'];
                    
                                    if(( $no_licencias_compradas >=$mn_rango_ini)&&($no_licencias_compradas<=$mn_rango_fin) ){
                                        $fl_princing=$row['fl_princing'];
                        
                                        #Recuperamos costos segun el plan .
                                        $Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princing";
                                        $row=RecuperaValor($Query);
                                        $mn_costo_mensual=$row[0];
                                        $mn_descuento_anual=$row[2];
                                        $mn_descuento_licencia=$row[3];
                                        $mn_costo_anual=$row[1] * 12;

                                        if(empty($mn_descuento_anual))
                                            $mn_descuento_anual=0;
                                        if(empty($mn_descuento_licencia))
                                            $mn_descuento_licencia=0;
                                        
                                    }

                                }
                
                
                                #para crear el nombre del plan en strippe.
                                if($fg_tipo_plan=='A'){
                                    
                                    #Se asignan nombres para crear plan en strippe
                                    $interval="year";
                                    $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                                    $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                                    $mn_costo_x_licencia=Conv_Dollars_Stripe($mn_costo_anual);
                                    $mn_descuentoDB=$mn_descuento_anual;
                                    
                                }
                                
                                
                                
                                if($fg_tipo_plan=='M'){
                                    
                                    #Se asignan nombres para crear plan en strippe
                                    $interval="month";
                                    $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                                    $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                                    $mn_costo_x_licencia=Conv_Dollars_Stripe($mn_costo_mensual);
                                    $mn_descuentoDB=$mn_descuento_licencia;
                                    
                                }


                                #Creamos al cliente
                                $customer = \Stripe\Customer::create(array(
                                  "email" => $email_cliente,
                                  "description" => $nb_instituto,
                                  "source" => $token,
                                ));
                                
                                $id_custom=$customer->id;
                                $ds_email_custom=$customer->email;
                                
                                $id_charges=$customer->sources;
                                $fe_anio_expiracion_tarjeta=$id_charges['data']['0']->exp_year;
                                $fe_mes_expiracion_tarjeta=$id_charges['data']['0']->exp_month;
                                #Generamos el pago
                            /*    $charge = \Stripe\Charge::create(array(
                                  "amount" => $mn_amount,
                                  "currency" => $currency,
                                  "description" => $ds_descripcion, 
                                  "receipt_email" => $ds_email_custom,
                                  "customer" => $customer->id ,
                                  "metadata" => array("tax" => $mn_tax)
                                ));
                            */

                                
                                #Recuperamos datos enviados a strippe;
                               // $id_cliente_stripe= $charge->id;
                        
                                //$ds_descripcion_pago=$charge->description;
                               // $mn_monto=$charge->amount;
                        
                                
                               //$monto_individual=
                        
                                #1.Creamos su plan de pago,propio por cliente/Instituto, con el tax incluido
                                $plan = \Stripe\Plan::create(array(
                                   "name" => $nb_plan,
                                   "id" => $id_plan,
                                   "interval" => $interval,
                                   "currency" => $currency,
                                   "amount" => $mn_costo_x_licencia
                                   )
                                 );
                
                                 $id_plan=$plan->id;
                                 $id_plan_creado=$plan->id;
                        
                                 
                                $subscription= \Stripe\Subscription::create(array(
                                      "customer" => $customer->id,
                                      "plan" => $id_plan,
                                      "quantity"=>$no_licencias_compradas,
                                      "tax_percent" => $mn_porcentaje_tax_,
                                      
                                    ));
                                $id_suscripcion=$subscription->id;
                                
                                
                                #Verificamos el eveno creado en Strippe, para despues poder recuperar elid del pago que se realizo. y actualizar su descripcion.
                                $event = \Stripe\Event::all(array("limit" => 2));
                                $id_charges=$event->data;    
                                $id_evento=$id_charges[1]->data;
                                $id_charge=$id_evento['object']->charge;
                                $id_invoice=$id_evento['object']->id;  
								
								/*
								if($id_charge){
                                #Actualizamos la descripcion del pago que se realizao.
                                $ch = \Stripe\Charge::retrieve($id_charge);
                                $ch->description = $ds_descripcion;
                                $ch->save();
								}
								*/

                                #ObteNemos datos para la BD
                                $no_licencias_usadas=ObtenNumeroUserInst($fl_instituto);        
                                $no_licencias_disponibles=$no_licencias_compradas-$no_licencias_usadas;        
                        
                                
                                
                                if($fg_tipo_plan=='M'){
                                
                                        #se calcula fecha de termino del plan por mes.
                                        $fe_final_periodo=strtotime('+30 day',strtotime($fe_actual));
                                        $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                                        
                                }
                                if($fg_tipo_plan=='A'){
                                    
                                        #se calcula fecha de termino del plan por año.
                                        $fe_final_periodo=strtotime('+1 year',strtotime($fe_actual));
                                        $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                                        
                                }        
                        
                                #Se genera el plan del Instituto. 
                                $Query="INSERT INTO k_current_plan  (fl_instituto,fl_princing,  mn_total_plan,fg_plan,no_total_licencias,no_licencias_disponibles,no_licencias_usadas,fg_estatus,fe_periodo_inicial,fe_periodo_final,fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta )  ";
                                $Query.="VALUES ($fl_instituto,$fl_princing,$mn_total,'$fg_tipo_plan',$no_licencias_compradas,$no_licencias_disponibles,$no_licencias_usadas,'A','$fe_actual','$fe_final_periodo','$fe_mes_expiracion_tarjeta','$fe_anio_expiracion_tarjeta')";
                                $fl_current_plan=EjecutaInsert($Query);
                        
                                if($fg_tipo_plan=='M'){
                                            #Se calcula el costo total    #no_licencias * el costo
                                            $mn_mensual_total=$no_licencias_compradas * $mn_costo_mensual ;
                                            $ds_descripcion=$ds_plan."-".ObtenEtiqueta(1705)." ".$no_licencias_compradas." licences";
                                            
                                            #se inserta el registro y costo por mes en su bitacora de pagos      
                                            $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento)";
                                            $Query.="VALUES($fl_current_plan,$mn_total,'1','$fe_actual','$fe_final_periodo','1','','$ds_descripcion','1',$mn_costo_mensual,'$id_invoice',$mn_descuentoDB) ";
                                            $fl_adm_pagos=EjecutaInsert($Query);
                                            
                                           
                                            
                                }
                                if($fg_tipo_plan=='A'){
                                    
                                            #se calcula el monto mensual a pagar .  mn_menual * No_licencias_contratado / 12 meses.   
                                            $mn_costo_total_anual= ($mn_costo_anual * $no_licencias_compradas)*12 ;
                                            #Obtenemos el AÑO actual
                                            $anio_actual=date ("y"); 
                                            $ds_descripcion=$ds_plan."-".ObtenEtiqueta(1706)." ".$no_licencias_compradas." licences";
                                            
                                            #se inserta el registro y costo por mes       
                                            $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento)";
                                            $Query.="VALUES($fl_current_plan,$mn_total,'1','$fe_actual','$fe_final_periodo','1',CURRENT_TIMESTAMP,'$ds_descripcion','1',$mn_costo_anual,'$id_invoice',$mn_descuentoDB) ";
                                            $fl_adm_pagos=EjecutaInsert($Query);
                                            
                                            
                                }
                                
                                
                                #Actualizaamsos el plan elegido. por default lo dejamos en Basico
                                $Query="UPDATE c_instituto SET cl_plan_fame='1' WHERE fl_instituto=$fl_instituto ";
                                EjecutaQuery($Query);
                                
                               

                                #Actualizamos registro del instituto y le decimos que el instituto ya tiene un plan,entonces pasa de modo trial  a Member.
                                $Query="UPDATE c_instituto SET fg_tiene_plan='1'  WHERE fl_instituto=$fl_instituto ";
                                EjecutaQuery($Query);
                        
                        
                        
                                #Guardaso el registro de pago(bitacora de pagos).
                                $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
                                $Query.="VALUES('$id_custom','$id_charge','$id_plan_creado','$id_suscripcion','$fg_motivo_pago','$ds_email_custom','$ds_descripcion',$mn_monto_normal,$mn_tax,$mn_total,CURRENT_TIMESTAMP, $fl_instituto)";
                                $fl_pago=EjecutaInsert($Query);
                
                
                                $Query="SELECT fe_creacion FROM k_pago_stripe WHERE fl_pago=$fl_pago ";
                                $row=RecuperaValor($Query);
                                $fe_pago=$row[0];
                
                                #Conslutamos el registro previamente creado.
                                $Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
                                $row1=RecuperaValor($Query);
                                $fl_current_plan=$row1[0];
                
                                $Query="UPDATE k_admin_pagos SET fe_pago='$fe_pago',fg_pagado='1',fl_pago_stripe=$fl_pago  WHERE fl_current_plan=$fl_current_plan AND fg_motivo_pago='1'  ";
                                EjecutaQuery($Query);
                            
                                #gUARMADOS EL ID DEL CLIENTE EN
                                $Query="UPDATE k_current_plan SET id_cliente_stripe='$id_custom',ds_email_stripe='$ds_email_custom',id_suscripcion_stripe='$id_suscripcion' , id_plan_stripe='$id_plan_creado' WHERE fl_current_plan=$fl_current_plan AND fl_instituto=$fl_instituto ";
                                EjecutaQuery($Query);
                                
                                #Actualizamos todos los usuario a activos para poder accesar al sistema
                                $Query="UPDATE c_usuario SET fg_activo='1' WHERE fl_instituto=$fl_instituto ";
                                EjecutaQuery($Query);
								
                                #Enviamos email de notificacion de que se ha suscrito a un plan.
                                $email=EnviarEmailAdquisicionPlan($fl_instituto,$fl_usuario);
								
								#Se calcula la fecha posterios a terminacion del plan para ejecucion de cron
                                $fe_final_periodo=strtotime('+1 day',strtotime($fe_final_periodo));
                                $fe_ejecucion= date('Y-m-d',$fe_final_periodo);
                                
                                #NOTA:Se genera la renovacion autmatica para stripe.//ya despues el Instituto tiene la opcion de cancelar.
                                $Query="INSERT INTO k_cron_plan_fame (fe_ejecucion,fl_instituto,id_cliente_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias,fe_creacion) ";                           
                                $Query.="VALUES('$fe_ejecucion',$fl_instituto,'','','','1','','AUTORENOVACION' ,0,0,CURRENT_TIMESTAMP) ";
                                $fl_cron=EjecutaInsert($Query);
								
                                
                                
                                
                                
                                
                                
                                /*****************************Envia Invoice************************************************/

                                # Recupera datos usuario
                                $Query  = "SELECT ds_email,ds_nombres,ds_apaterno ";
                                $Query .= "FROM c_usuario WHERE fl_usuario=$fl_usuario ";
                                $row = RecuperaValor($Query);
                                $ds_email=str_texto($row[0]);
                                $ds_nombres=$row['ds_nombres'];
                                $ds_apaterno=$row['ds_apaterno'];
                                
                                
                                # generador de documento cuerpo del email
                                $ds_header = GeneraInvoice($fl_instituto,$fl_usuario,'',$id_charge,129,1);
                                $ds_cuerpo = GeneraInvoice($fl_instituto,$fl_usuario,'',$id_charge,129,2);
                                $ds_footer = GeneraInvoice($fl_instituto,$fl_usuario,'',$id_charge,129,3);
                                
                                
                                
                                #Recuperamos el datos del istituto:
                                $Query="SELECT ds_instituto,nb_plan,S.ds_pais,no_telefono 
                                                 FROM c_instituto I
                                                 JOIN c_plan_fame P ON P.cl_plan_fame=I.cl_plan_fame
                                                 JOIN c_pais S ON S.fl_pais=I.fl_pais
                                                 WHERE fl_instituto=$fl_instituto ";
                                $row=RecuperaValor($Query);
                                $nb_instituto=str_texto($row[0]);
                                $nb_plan_fame=str_texto($row[1]);
                                $ds_pais=str_texto($row[2]);
                                $no_telefono_instituto=$row[3];

                                
                                
                                #Recuperamos la direccion del Usuario.
                                $Query="SELECT ds_state,ds_city,ds_number,ds_street,ds_zip,ds_phone_number ";
                                $Query.="FROM k_usu_direccion_sp A
                                                   WHERE fl_usuario_sp=$fl_usuario ";
                                $row=RecuperaValor($Query);
                                $ds_estado=$row['ds_state'];
                                $ds_ciudad=$row['ds_city'];
                                $ds_numero_casa=$row['ds_number'];
                                $ds_calle=$row['ds_street'];
                                $ds_codigo_postal=$row['ds_zip'];
                                $ds_telefono=$row['ds_phone_number'];
                                
                                if(is_numeric($ds_estado)){
                                    
                                    #Recuperamos la provoincia de canada
                                    $Query4="SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$ds_estado ";
                                    $row4=RecuperaValor($Query4);
                                    $ds_estado=str_texto($row4[0]);
                                }
                                
                                #Obtenemos fecha actual :
                                $Query = "Select CURDATE() ";
                                $row = RecuperaValor($Query);
                                $fe_actual = str_texto($row[0]);
                                $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                                $fe_actual= date('Y-m-d',$fe_actual);
                                $fe_emision=GeneraFormatoFecha($fe_actual);
                                
                                
                                #Recuperamos la fecha de pago
                                $Query3="SELECT fe_pago,fl_pago_stripe,mn_descuento FROM k_admin_pagos WHERE fl_admin_pagos=$fl_adm_pagos ";
                                $row3=RecuperaValor($Query3);
                                $fe_pago=GeneraFormatoFecha(($row3[0]));
                                $fl_pago_stripe=$row3[1];
                                $mn_descuento=$row3[2];
                                
                                
                                #Recuperamos el id del pago, 
                                $Query2="SELECT id_pago_stripe FROM k_pago_stripe WHERE fl_pago=$fl_pago ";
                                $row2=RecuperaValor($Query2);
                                $id_pago=str_texto($row2[0]);
                                
                                
                                
                                
                                #guardamos el pdf
                                class ConPies extends TCPDF {
                                    //header 
                                    function Header(){
                                        
                                        
                                        $encabezado = '
                                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                <tr>
                                                                <td style="width:100%;">
                                                                    &nbsp;
                                                                </td>
                                                                </tr>
                                                ';
                                        $encabezado.='<tr>
       
        
                                                                <td rowspan="5" style="width:40%; color:#037EB7; font-family:Tahoma; font-size:32px; text-align:right;">
                                                                        <img src="../../AD3M2SRC4/images/Vanas_doc_logo.jpg" />
                                                                </td>
		
		                                                        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;"></td>
		
		                                                        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;"> </td>
                                                    </tr>
	                                                ';
                                        $encabezado.='
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
		
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
                                                        '.ObtenEtiqueta(516).'  
                                                    </td>
                                                    </tr>
	                                                ';
                                        $encabezado.='
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
   
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
                                                        '.ObtenEtiqueta(518).' 
                                                    </td>
                                                    </tr>
                                                    ';
                                        $encabezado.='
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
		
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
                                                    '.ObtenEtiqueta(519).'   
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
        
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
                                                        '.ObtenEtiqueta(1741).': '.ObtenEtiqueta(1740).'
                                                    </td>
                                                    </tr>
      
                                                    <tr>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:62px; font-weight:normal; text-align:left;">
		                                            <b>'.ObtenEtiqueta(1729).'</b>
                                                    </td>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
        
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
		
                                                    </td>
                                                    </tr>
      
      
		
                                                        ';
                                        $encabezado.='

                                                </table>';
                                        
                                        
                                        
                                        
                                        $this->writeHTML($encabezado, true, 0, true, 0); 
                                    }
                                    //footer
                                    function Footer(){

                                        $left_footer=ObtenEtiqueta(1746).ObtenEtiqueta(1737);
                                        $right_footer="";

                                        $this->SetY(-20);
                                        $this->SetFont('helvetica', '', 9);
                                        $this->writeHTML($left_footer, true, 0, true, 0); 
                                    }
                                }

                                // creamos un nuevo objeto usando la clase extendida ConPies 
                                $pdf = new ConPies();
                                $pdf->SetFont('helvetica', '', 10); 

                                // add a page
                                $pdf->AddPage("P"); 
                                
                                
                                /**** Inicia contenido del pdf*******/
                                #Recupermaos datos claves  del programa.
                                $Query="SELECT A.ds_descripcion,B.mn_monto,B.mn_tax,B.mn_total,A.fg_motivo_pago,B.id_pago_stripe,B.id_cliente_stripe,A.fe_periodo_inicial,A.fe_periodo_final,A.id_invoice_stripe,A.fl_current_plan  
                                                    FROM k_admin_pagos A
                                                    JOIN k_pago_stripe B ON B.fl_pago=A.fl_pago_stripe 
                                                    WHERE A.fl_admin_pagos=$fl_adm_pagos ";
                                $row = RecuperaValor($Query);
                                $ds_descripcion_pago=str_texto($row[0]);
                                $mn_monto_sin_tax=$row[1];
                                $mn_tax=$row[2];
                                $mn_total=number_format($row[3],2);
                                $fg_motivo_pago=$row[4];

                                $id_pago_stripe=$row[5];
                                $id_cliente_stripe=$row[6];
                                $fe_periodo_inicial=GeneraFormatoFecha($row[7]);
                                $fe_periodo_final=GeneraFormatoFecha($row[8]);
                                $id_invoice_stripe=$row[9];
                                $fl_current_plan=$row[10];
                                

                                #Obtenemos la cantidad para colocarlo en detalle del invoice
                                $cantidad = intval(preg_replace('/[^0-9]+/', '', $ds_descripcion_pago), 10);
                                $ds_descripcion_pago = preg_replace('/[0-9]+/', '', $ds_descripcion_pago);
                                
                                
                                #Recupermao el tipo de plan.
                                $Query="SELECT fl_princing,fg_plan FROM k_current_plan WHERE fl_current_plan=$fl_current_plan ";
                                $row=RecuperaValor($Query);
                                $fl_princing=$row[0];
                                $fg_plan=$row[1];
                                
                                $Query2="SELECT mn_anual,mn_mensual FROM c_princing WHERE fl_princing=$fl_princing ";
                                $row2=RecuperaValor($Query2);
                                if($fg_plan=='M')
                                    $mn_costo_por_licencia=$row2[1];
                                else
                                    $mn_costo_por_licencia=$row2[0];
                                
                                
                                #Agregamos el costo por licencia.
                                $ds_descripcion_pago=$ds_descripcion_pago."<br/>".ObtenEtiqueta(1765)." $".$mn_costo_por_licencia."<br/>".ObtenEtiqueta(1750)." ".$mn_descuento."%" ;
                                
                                #Eliminamos las licencias de la descripcion y se la pasamos ala cantidad del detalle del invoice.
                                //$ds_descripcion_pago = substr($ds_descripcion_pago, 0, -11); 
                                
                                
                                $interval=$fe_periodo_inicial." to ".$fe_periodo_final;
                                
                                if(empty($id_invoice_stripe)){
                                    #Recupermso el intervalo de la fecha del period actual del instituto
                                    $Query="SELECT fe_periodo_inicial,fe_periodo_final FROM k_current_plan WHERE fl_instituto=$fl_instituto";
                                    $row=RecuperaValor($Query);
                                    $fe_periodo_inicial=GeneraFormatoFecha($row[0]);
                                    $fe_periodo_final=GeneraFormatoFecha($row[1]);
                                    $interval=$fe_periodo_inicial." to ".$fe_periodo_final;
                                    
                                    $id_invoice_stripe=$id_pago_stripe;
                                }
                                
                                
                                
                                
                                /*****Termina contenido del pdf*****/
                                # Empezamos a mostrar datos
                                $htmlcontent = '<table border="0" cellpadding="1" cellspacing="0" width="100%">'; 
                                
                                
                                $htmlcontent .='<tr>';
                                $htmlcontent .='<td colspan="7" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> </td>';
                                $htmlcontent .='</tr>';
                                

                                
                                $htmlcontent .= '<tr >';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><strong>'.ObtenEtiqueta(1726).'</strong>  </td>';
                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><strong>'.ObtenEtiqueta(1727).'</strong>   </td>';
                                //$htmlcontent .='<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                //$htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .= '</tr>';
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$ds_nombres.' '.$ds_apaterno.'   </td>';
                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1728).':</td>';
                                $htmlcontent .='<td  colspan="2" style="height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$id_invoice_stripe.'</td>';
                                
                                $htmlcontent .='</tr>';
                                
                                
                                
                                
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_instituto.'   ';
                                $htmlcontent .='</td>';
                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1730).':</td>';
                                $htmlcontent .='<td colspan="2" style="height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$fe_emision.'</td>';
                                // $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='</tr>';
                                
                                
                                
                                
                                
                                
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">';
                                if($ds_numero_casa)
                                    $htmlcontent.=''.$ds_numero_casa.' ';
                                $htmlcontent.=''.$ds_calle.' '.$ds_codigo_postal.'';
                                
                                $htmlcontent .='</td>';
                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1731).':</td>';
                                $htmlcontent .='<td colspan="3" style=" height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$id_pago_stripe.' </td>';
                                
                                $htmlcontent .='</tr>';
                                
                                
                                
                                
                                
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">';
                                if($ds_estado)
                                    $htmlcontent .=''.$ds_estado.' ';
                                $htmlcontent .=''.$ds_pais.'  </td>';

                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1732).': </td>';
                                
                                $htmlcontent .='<td colspan="2" style=" height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_plan_fame.'  </td>';
                                $htmlcontent .='</tr>';
                                
                                
                                
                                
                                
                                /*
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_plan_fame.' </td>';
                                $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='</tr>';
                                 */
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">  </td>';
                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left; margin-lef:35px;">'.ObtenEtiqueta(1715).':</td>';
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$fe_pago.' </td>';
                                $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='</tr>';
                                
                                
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:50%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><b>'.ObtenEtiqueta(1733).':</b> '.$id_cliente_stripe.' </td>';
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"></td>';
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='</tr>';
                                

                                $htmlcontent .='<tr>';
                                $htmlcontent .='<td colspan="4" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> <br/> <br/></td>';
                                $htmlcontent .='</tr>';
                                
                                
                                
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:40%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b>'.ObtenEtiqueta(1716).' </b> </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b>'.ObtenEtiqueta(1734).' </b></td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b> '.ObtenEtiqueta(1717).' </b>  </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;"><b> '.ObtenEtiqueta(1718).' ('.ObtenConfiguracion(113).'$)</b>  </td>';
                                $htmlcontent .= '</tr>';

                                
                                
                                //$htmlcontent .= '<tr >';
                                //$htmlcontent .='<td colspan="4" style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><br/>  </td>';
                                //$htmlcontent .= '</tr>';
                                
                                
                                
                                
                                $htmlcontent .= '<tr >';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:40%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:left;">'.$ds_descripcion_pago.'  </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:30%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:left;">'.$interval.' </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:10%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:center;">'.$cantidad.'</td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:right;">'.number_format($mn_monto_sin_tax,2).' </td>';
                                $htmlcontent .= '</tr>';

                                
                                
                                #Obtenemos el porcentaje del tax.
                                $mn_porcentaje=($mn_tax/$mn_monto_sin_tax) *100;
                                
                                
                                //$htmlcontent .= '<tr >';
                                //$htmlcontent .='<td colspan="4" style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><br/>  </td>';
                                //$htmlcontent .= '</tr>';
                                
                                
                                
                                #Presentamos Descuento.
                                //$htmlcontent .= '<tr >';
                                //$htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                //$htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                //$htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1750).'  </td>';
                                //$htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.$mn_descuento.' %</td>';
                                //$htmlcontent .= '</tr>';
                                
                                
                                
                                #Presentamos totale y tax.
                                $htmlcontent .= '<tr >';
                                $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                $htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1736).' '.ObtenConfiguracion(113).' </td>';
                                $htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.number_format($mn_monto_sin_tax,2).' </td>';
                                $htmlcontent .= '</tr>';
                                
                                
                                $htmlcontent .= '<tr >';
                                $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">*'.ObtenEtiqueta(1735).' ('.number_format($mn_porcentaje).'%) </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.number_format($mn_tax,2).' </td>';
                                $htmlcontent .= '</tr>';
                                
                                
                                
                                
                                #Presentamos totale y tax.
                                $htmlcontent .= '<tr >';
                                $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1742).' '.ObtenConfiguracion(113).'</td>';
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.$mn_total.'  </td>';
                                $htmlcontent .= '</tr>';
                                
                                
                                
                                
                                $htmlcontent .= '</table>';
                                
                                
                                
                                
                                $ds_cuerpo2=$htmlcontent;
                                // output the HTML content contenido del pdf
                                $pdf->writeHTMLCell(180, 100, 10,60,$ds_cuerpo2, 0, 0, false, true,'',true); 
                                //nombre del archivo
                                $fileName ='Invoice_'.$nb_instituto.'_'.$id_pago_stripe.'.pdf';
                                // pasamos el archivo a base64
                                //$pdf->Output($fileName, 'F');///guarda el archivo MjD: Se deja comen tado porque se cambio el metodo para que ahora vaya como attachment
                                $fileatt = $pdf->Output($fileName, 'E'); //genera la codificacion para enviar adjuntado el archivo
                                

                                $from=MAIL_FROM;
                                # Inicializa variables de ambiente para envio de correo adjunto
                                ini_set("SMTP", MAIL_SERVER);
                                ini_set("smtp_port", MAIL_PORT);
                                ini_set("sendmail_from", MAIL_FROM);
                                $repEmail = $from;
                                $nb_nombre_dos=ObtenEtiqueta(1646);#nombre de quien envia el mensaje
                                $eol = "\n";
                                $separator = md5(time());
                                
                                $subject=ObtenEtiqueta(1768);#etiqueta de asunto del mensjae
                                $headers  = 'MIME-Version: 1.0' .$eol;
                                
                                $headers .= 'From: "'.$nb_nombre_dos.'" <'.$repEmail.'>'.$eol;
                                $admin=ObtenConfiguracion(107);
                                $headers .= "Bcc: $admin \r\n";
                                $headers .= 'Content-Type: multipart/mixed; boundary="'.$separator.'"';
                                
                                $message = "--".$separator.$eol;
                                $message .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
                               
                                #Presenta mensaje del email
                                $message .= utf8_decode($ds_header).utf8_decode($ds_cuerpo).utf8_decode($ds_footer).$eol;

                                $message .= "--".$separator.$eol;
                                $message .= $fileatt;
                                $message .= "--".$separator."--".$eol;
                                
                                # insertamos el envio del email
                                $mail=mail($ds_email, $subject, $message, $headers);
                                
                                
                                /************************************************************************************************/
                                
                                
                                
                                
                              
                                
                  
            }
                  
           
            
            if($fg_motivo_pago==PAGO_ADD_LICENCES){
                
                  
                     
                        
                
                        #Convertimosel monto para guardarlo en DB
                        $mn_tax_normal=$mn_tax/100;
                        $mn_monto_normal=(($mn_amount/100)-$mn_tax_normal);
                        $mn_total_nuevo_plan_con_tax=$mn_monto_normal+ $mn_tax_normal;
                        $mn_monto_total_a_pagar=number_format(($mn_amount/100),2);
                
                
                        #Para enviar a stripe.
                
                        $mn_monto_sin_tax=Conv_Dollars_Stripe($mn_monto_normal);
                
                        #Recupersmoe el fl_princinc_actual, con ello sabemos si se mantiene en el rango o cambia el precio por las licencias adquiridas.
                        $Query="SELECT fl_princing,fe_periodo_final FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
                        $row=RecuperaValor($Query);
                        $fl_princing_actual=$row[0];
                        $fe_terminacion_plan=$row[1];
                        
                        
                        
                        
                        
                        
                        #Recuperamos el Nombre del Istituto yel nombre del plan.
                        $Query2="SELECT ds_instituto,B.ds_descripcion 
                                         FROM c_instituto A
                                         LEFT JOIN c_plan_fame B ON A.cl_plan_fame=B.cl_plan_fame 
                                         WHERE fl_instituto=$fl_instituto ";
                        $row2=RecuperaValor($Query2);
                        $nb_instituto=str_texto($row2[0]);
                        $ds_plan=str_texto($row2[1]);
                        
                
                        #Recuperamos el id del plan creado en stripe, para actualizar el monto y tarifa.
                        $Query="SELECT id_plan_stripe,id_cliente_stripe,id_suscripcion_stripe,ds_email_stripe FROM k_current_plan WHERE fl_instituto=$fl_instituto  ";
                        $row=RecuperaValor($Query);
                        $id_plan_creado_instituto=str_texto($row['id_plan_stripe']);
                        $id_custom_creado_instituto=str_texto($row['id_cliente_stripe']);
                        $id_suscripcion_creado_instituto=str_texto($row['id_suscripcion_stripe']);
                        $ds_email_custom=str_texto($row['ds_email_stripe']);
                        
                        
                        
   
                        $no_total_licencias_actuales=ObtenNumLicencias($fl_instituto);                        
                        $no_nuevo_licencias=$no_total_licencias_actuales + $no_licencias_compradas;
                       
                        
                        $rand=rand(5,1000);
                        
                        #identificamos en que rango se encuentra,PARA SABER  NUEVO PLAN y nuevas tarifas.
                        $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
                        $rs = EjecutaQuery($Query);
                        for($i=1;$row=RecuperaRegistro($rs);$i++){
                            
                            
                            $mn_rango_ini= $row['no_ini'];
                            $mn_rango_fin= $row['no_fin'];
                            
                            if(( $no_nuevo_licencias >=$mn_rango_ini)&&($no_nuevo_licencias<=$mn_rango_fin) ){
                                
                                    $fl_princing=$row['fl_princing'];
                                
                                    #Recuperamos costos segun el plan obtenido del nuevo rango de licencias.
                                    $Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princing ";
                                    $row=RecuperaValor($Query);
                                    $mn_costo_mensual=$row[0];
                                    $mn_costo_anual=$row[1]*12;
                                    
                                    $mn_descuento_anual=$row[2];
                                    $mn_descuento_licencia=$row[3];
                                
                                    if(empty($mn_descuento_anual))
                                     $mn_descuento_anual=0;
                                    if(empty($mn_descuento_licencia))
                                    $mn_descuento_licencia=0;
                                    
                                    if($fg_tipo_plan=='M'){
                                 
                                        $mn_total_nuevo_plan=$mn_costo_mensual * $no_nuevo_licencias;
                                        $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                                        $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                                        $interval="month";#Valores default stripe
                                        $mn_costo_x_licencia=Conv_Dollars_Stripe($mn_costo_mensual);
                                        $mn_costo_x_licencia_bd=$mn_costo_mensual;
                                        $mn_descuentoDB=$mn_descuento_licencia;
                                        
                                        
                                    }
                                    if($fg_tipo_plan=='A'){
                                    
                                        $mn_total_nuevo_plan= ($mn_costo_anual*$no_nuevo_licencias);
                                        $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                                        $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                                        $interval="year";
                                        $mn_costo_x_licencia=Conv_Dollars_Stripe($mn_costo_anual);
                                        $mn_costo_x_licencia_bd=$mn_costo_anual;
                                        $mn_descuentoDB=$mn_descuento_anual;
                                    }
                                
                                 
                                    #Se le suma el tax.
                                    $mn_porcentaje_tax=Tax_Can_User($fl_usuario);  
                                    $mn_porcentaje_tax=$mn_porcentaje_tax*100;
                                    $mn_tax= ($mn_total_nuevo_plan * $mn_porcentaje_tax)/100 ;
                                    $mn_total_nuevo_plan_con_tax=$mn_total_nuevo_plan + $mn_tax;

                                
                                
                                
                            }

                        }
                        
                        
                        $customer=Customer($id_custom_creado_instituto);

                        if(empty($customer)){
                            

                            #Creamos al cliente
                            $customer = \Stripe\Customer::create(array(
                              "email" => $ds_email_custom,
                              "description" => $nb_instituto,
                              "source" => $token,
                            ));
                            
                            $id_custom_creado_instituto=$customer->id;
                            $ds_email_custom=$customer->email;


                        }


                        #Generamos el pago correspondientes, la diferencia, antes calculada.
                        $charge = \Stripe\Charge::create(array(
                            "amount" => $mn_amount,
                            "currency" => $currency,
                            "description" => $ds_descripcion, 
                            "customer" => $id_custom_creado_instituto,
                            "metadata" => array("tax" => $mn_tax)
                        ));
                        
                        $id_charge= $charge->id;
                        
                        

                        $suscription=getSubscripcion($id_suscripcion_creado_instituto);



                        
                        if($fl_princing_actual==$fl_princing){
                                    
                            if(!empty($suscription)){
                                #Aplica cambiso stripre misma tarifa
                                #Actualizmos tarifa en strippe
                                $update_plan = \Stripe\Subscription::retrieve($id_suscripcion_creado_instituto);
                                $update_plan->quantity = $no_nuevo_licencias;
                                $update_plan->save();
                            }
                        
                        }else{#Cuando ya pasa a otro nivel de plan
                            
                                  #Verificamos si existe un registro del isntituto
                                  $Query="SELECT COUNT(*) FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto ";
                                  $row=RecuperaValor($Query);
                                  $existe=$row[0];
                            
                                  
                                  #Se calcula la fecha posterios a terminacion del plan para ejecucion de cron
                                  $fe_final_periodo=strtotime('+1 day',strtotime($fe_terminacion_plan));
                                  $fe_ejecucion= date('Y-m-d',$fe_final_periodo);
                                  
                                  if($existe){
                                          #Actualiza datos    
                                      
                                          $Query="UPDATE k_cron_plan_fame SET fe_ejecucion='$fe_ejecucion',id_cliente_stripe='$id_custom_creado_instituto',id_plan_stripe='$id_plan_creado_instituto', ";
                                          $Query.="id_suscripcion_stripe='$id_suscripcion_creado_instituto',fg_motivo_pago='2',ds_email='$ds_email_custom',ds_descripcion_pago='$ds_descripcion',mn_monto_por_licencia=$mn_costo_x_licencia_bd,mn_cantidad_licencias=$no_nuevo_licencias ,  fe_creacion=CURRENT_TIMESTAMP  WHERE fl_instituto=$fl_instituto ";
                                          EjecutaQuery($Query);
                                      
                                      
                                      
                                  }else{
                                  
                                          #Se guarda el registro solo en BD Vanas, para despues recueprarlos al finalizar el plan actual,despues para cancelarlo  y despues crear un nuevo plan en Strippe.
                                          #NOTA:Se actualizaran sus datos correspondientes en la BD. y al final su plan , existe un cron que recuperara los datos actuales del Instituto y generara un nuevo plan en Stripe.
                                         $Query="INSERT INTO k_cron_plan_fame (fe_ejecucion,fl_instituto,id_cliente_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias,fe_creacion) ";                           
                                         $Query.="VALUES('$fe_ejecucion',$fl_instituto,'$id_custom_creado_instituto','$id_plan_creado_instituto','$id_suscripcion_creado_instituto','2','$ds_email_custom','$ds_descripcion', $mn_costo_x_licencia_bd,$no_nuevo_licencias,CURRENT_TIMESTAMP) ";
                                         $fl_cron=EjecutaInsert($Query);
                                  }

                        }
                        
                        
                        
                        
                        
                        

                     
                          
                        
                      

                        #sumamos las licencias que se encuentran en  estado disponibles alas nuevas agregadas.
                        $no_licencia_disponibles= ObtenNumLicenciasDisponibles($fl_instituto) + $no_licencias_compradas;
                        
                        
                        
                       // if($fg_tipo_plan=='M'){ 
                        
                        #Actualizamos Plan actual del Instituto.
                        #1.Solo actualizamos licencias disponibles de su plan actual.
                        $Query="UPDATE k_current_plan SET no_total_licencias=$no_nuevo_licencias,fl_princing=$fl_princing, no_licencias_disponibles=$no_licencia_disponibles,mn_total_plan=$mn_total_nuevo_plan_con_tax , fg_plan='$fg_tipo_plan' ";
                        $Query.="WHERE fl_instituto=$fl_instituto ";
                        EjecutaQuery($Query);
                        
                        
                        $Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto  ";
                        $row=RecuperaValor($Query);
                        $fl_current_plan_actual=$row[0];
                        
                        
                        
                        
                        #Guardaso el registro de pago(bitacora de pagos).
                        $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
                        $Query.="VALUES('$id_custom_creado_instituto','$id_charge','$id_plan_creado_instituto','$id_suscripcion_creado_instituto','$fg_motivo_pago','$ds_email_custom','$ds_descripcion',$mn_monto_normal,$mn_tax_normal,$mn_monto_total_a_pagar,CURRENT_TIMESTAMP, $fl_instituto)";
                        $fl_pago=EjecutaInsert($Query);
                        
                        #Se guarda la bitacora de los pagos el Instituto      
                        $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar, fg_pagado,fe_pago, ds_descripcion,fg_motivo_pago,fl_pago_stripe,mn_costo_por_licencia,mn_descuento)";
                        $Query.="VALUES($fl_current_plan_actual,$mn_monto_total_a_pagar,'1','1',CURRENT_TIMESTAMP,'$ds_descripcion','2',$fl_pago,$mn_costo_x_licencia_bd,$mn_descuentoDB) ";
                        $fl_adm_pagos=EjecutaInsert($Query);
                        
                       
                      
                        
                        
                      
                         #email de conitificacion.                        
                         #Enviamos email de notificacion de que se ha suscrito a un plan.
                         $email=EnviarEmailAdquisicionPlan($fl_instituto,$fl_usuario);
                  
                
                         
                         
                         
                         /*****************************Envia Invoice************************************************/
                                        
                                          # Recupera datos usuario
                                          $Query  = "SELECT ds_email,ds_nombres,ds_apaterno ";
                                          $Query .= "FROM c_usuario WHERE fl_usuario=$fl_usuario ";
                                          $row = RecuperaValor($Query);
                                          $ds_email=str_texto($row[0]);
                                          $ds_nombres=$row['ds_nombres'];
                                          $ds_apaterno=$row['ds_apaterno'];
                                          
                                          
                                          # generador de documento cuerpo del email
                                          $ds_header = GeneraInvoice($fl_instituto,$fl_usuario,'',$id_charge,129,1);
                                          $ds_cuerpo = GeneraInvoice($fl_instituto,$fl_usuario,'',$id_charge,129,2);
                                          $ds_footer = GeneraInvoice($fl_instituto,$fl_usuario,'',$id_charge,129,3);
  
                                         
                                          
                                          #Recuperamos el datos del istituto:
                                          $Query="SELECT ds_instituto,nb_plan,S.ds_pais,no_telefono 
                                                 FROM c_instituto I
                                                 JOIN c_plan_fame P ON P.cl_plan_fame=I.cl_plan_fame
                                                 JOIN c_pais S ON S.fl_pais=I.fl_pais
                                                 WHERE fl_instituto=$fl_instituto ";
                                          $row=RecuperaValor($Query);
                                          $nb_instituto=str_texto($row[0]);
                                          $nb_plan_fame=str_texto($row[1]);
                                          $ds_pais=str_texto($row[2]);
                                          $no_telefono_instituto=$row[3];

                                          
                                          
                                          #Recuperamos la direccion del Usuario.
                                          $Query="SELECT ds_state,ds_city,ds_number,ds_street,ds_zip,ds_phone_number ";
                                          $Query.="FROM k_usu_direccion_sp A
                                                   WHERE fl_usuario_sp=$fl_usuario ";
                                          $row=RecuperaValor($Query);
                                          $ds_estado=$row['ds_state'];
                                          $ds_ciudad=$row['ds_city'];
                                          $ds_numero_casa=$row['ds_number'];
                                          $ds_calle=$row['ds_street'];
                                          $ds_codigo_postal=$row['ds_zip'];
                                          $ds_telefono=$row['ds_phone_number'];
                                          
                                          if(is_numeric($ds_estado)){
                                              
                                              #Recuperamos la provoincia de canada
                                              $Query4="SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$ds_estado ";
                                              $row4=RecuperaValor($Query4);
                                              $ds_estado=str_texto($row4[0]);
                                          }
                                          
                                          #Obtenemos fecha actual :
                                          $Query = "Select CURDATE() ";
                                          $row = RecuperaValor($Query);
                                          $fe_actual = str_texto($row[0]);
                                          $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                                          $fe_actual= date('Y-m-d',$fe_actual);
                                          $fe_emision=GeneraFormatoFecha($fe_actual);
                                          
                                          
                                          #Recuperamos la fecha de pago
                                          $Query3="SELECT fe_pago,fl_pago_stripe,mn_descuento FROM k_admin_pagos WHERE fl_admin_pagos=$fl_adm_pagos ";
                                          $row3=RecuperaValor($Query3);
                                          $fe_pago=GeneraFormatoFecha(($row3[0]));
                                          $fl_pago_stripe=$row3[1];
                                          $mn_descuento=$row3[2];
                                          
                                          
                                          #Recuperamos el id del pago, 
                                          $Query2="SELECT id_pago_stripe FROM k_pago_stripe WHERE fl_pago=$fl_pago ";
                                          $row2=RecuperaValor($Query2);
                                          $id_pago=str_texto($row2[0]);
                                          
                                          
                                          
                                          
                                          #guardamos el pdf
                                          class ConPies extends TCPDF {
                                            //header 
                                            function Header(){
                                             
                                              
                                              $encabezado = '
                                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                <tr>
                                                                <td style="width:100%;">
                                                                    &nbsp;
                                                                </td>
                                                                </tr>
                                                ';
                                              $encabezado.='<tr>
       
        
                                                                <td rowspan="5" style="width:40%; color:#037EB7; font-family:Tahoma; font-size:32px; text-align:right;">
                                                                        <img src="../../AD3M2SRC4/images/Vanas_doc_logo.jpg" />
                                                                </td>
		
		                                                        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;"></td>
		
		                                                        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;"> </td>
                                                    </tr>
	                                                ';
                                                $encabezado.='
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
		
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:27px; font-weight:normal; text-align:right;">
                                                        '.ObtenEtiqueta(516).'  
                                                    </td>
                                                    </tr>
	                                                ';
                                                $encabezado.='
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:36px; font-weight:normal; text-align:left;">
   
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:27px; font-weight:normal; text-align:right;">
                                                        '.ObtenEtiqueta(518).' 
                                                    </td>
                                                    </tr>
                                                    ';
                                                 $encabezado.='
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
		
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:27px; font-weight:normal; text-align:right;">
                                                    '.ObtenEtiqueta(519).'   
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
        
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:27px; font-weight:normal; text-align:right;">
                                                        '.ObtenEtiqueta(1741).': '.ObtenEtiqueta(1740).'
                                                    </td>
                                                    </tr>
      
                                                    <tr>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:58px; font-weight:normal; text-align:left;">
		                                            <b>'.ObtenEtiqueta(1729).'</b>
                                                    </td>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
        
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
		
                                                    </td>
                                                    </tr>
      
      
		
                                                        ';
                                                $encabezado.='

                                                </table>';
                                              
                                              
                                              
                                              
                                                $this->writeHTML($encabezado, true, 0, true, 0); 
                                            }
                                            //footer
                                            function Footer(){

                                              $left_footer=ObtenEtiqueta(1746).ObtenEtiqueta(1737);
                                              $right_footer="";

                                              $this->SetY(-20);
                                              $this->SetFont('helvetica', '', 9);
                                              $this->writeHTML($left_footer, true, 0, true, 0); 
                                            }
                                          }

                                          // creamos un nuevo objeto usando la clase extendida ConPies 
                                          $pdf = new ConPies();
                                          $pdf->SetFont('helvetica', '', 10); 

                                          // add a page
                                          $pdf->AddPage("P"); 
                                          
                                          
                                          /**** Inicia contenido del pdf*******/
                                          #Recupermaos datos claves  del programa.
                                          $Query="SELECT A.ds_descripcion,B.mn_monto,B.mn_tax,B.mn_total,A.fg_motivo_pago,B.id_pago_stripe,B.id_cliente_stripe,A.fe_periodo_inicial,A.fe_periodo_final,A.id_invoice_stripe,A.fl_current_plan  
                                                    FROM k_admin_pagos A
                                                    JOIN k_pago_stripe B ON B.fl_pago=A.fl_pago_stripe 
                                                    WHERE A.fl_admin_pagos=$fl_adm_pagos ";
                                          $row = RecuperaValor($Query);
                                          $ds_descripcion_pago=str_texto($row[0]);
                                          $mn_monto_sin_tax=$row[1];
                                          $mn_tax=$row[2];
                                          $mn_total=number_format($row[3],2);
                                          $fg_motivo_pago=$row[4];

                                          $id_pago_stripe=$row[5];
                                          $id_cliente_stripe=$row[6];
                                          $fe_periodo_inicial=GeneraFormatoFecha($row[7]);
                                          $fe_periodo_final=GeneraFormatoFecha($row[8]);
                                          $id_invoice_stripe=$row[9];
                                          $fl_current_plan=$row[10];
                                          

                                          #Obtenemos la cantidad para colocarlo en detalle del invoice
                                          $cantidad = intval(preg_replace('/[^0-9]+/', '', $ds_descripcion_pago), 10);
                                          $ds_descripcion_pago = preg_replace('/[0-9]+/', '', $ds_descripcion_pago);
                                          
                                          
                                          #Recupermao el tipo de plan.
                                          $Query="SELECT fl_princing,fg_plan FROM k_current_plan WHERE fl_current_plan=$fl_current_plan ";
                                          $row=RecuperaValor($Query);
                                          $fl_princing=$row[0];
                                          $fg_plan=$row[1];
                                          
                                          $Query2="SELECT mn_anual,mn_mensual FROM c_princing WHERE fl_princing=$fl_princing ";
                                          $row2=RecuperaValor($Query2);
                                          if($fg_plan=='M')
                                              $mn_costo_por_licencia=$row2[1];
                                          else
                                              $mn_costo_por_licencia=$row2[0];
                                          
                                          
                                          #Agregamos el costo por licencia.
                                          $ds_descripcion_pago=$ds_descripcion_pago."<br/>".ObtenEtiqueta(1765)." $".$mn_costo_por_licencia."<br/>".ObtenEtiqueta(1750)." ".$mn_descuento."%" ;
                                          
                                          #Eliminamos las licencias de la descripcion y se la pasamos ala cantidad del detalle del invoice.
                                          //$ds_descripcion_pago = substr($ds_descripcion_pago, 0, -11); 
                                          
                                          
                                          $interval=$fe_periodo_inicial." to ".$fe_periodo_final;
                                          
                                          if(empty($id_invoice_stripe)){
                                              #Recupermso el intervalo de la fecha del period actual del instituto
                                              $Query="SELECT fe_periodo_inicial,fe_periodo_final FROM k_current_plan WHERE fl_instituto=$fl_instituto";
                                              $row=RecuperaValor($Query);
                                              $fe_periodo_inicial=GeneraFormatoFecha($row[0]);
                                              $fe_periodo_final=GeneraFormatoFecha($row[1]);
                                              $interval=$fe_periodo_inicial." to ".$fe_periodo_final;
                                              
                                              $id_invoice_stripe=$id_pago_stripe;
                                          }
                                          
                                          
                                          
                                          
                                          /*****Termina contenido del pdf*****/
                                          # Empezamos a mostrar datos
                                          $htmlcontent = '<table border="0" cellpadding="1" cellspacing="0" width="100%">'; 
                                          
                                          
                                          $htmlcontent .='<tr>';
                                          $htmlcontent .='<td colspan="7" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> </td>';
                                          $htmlcontent .='</tr>';
                                          

                                          
                                          $htmlcontent .= '<tr >';
                                          $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;"><strong>'.ObtenEtiqueta(1726).'</strong>  </td>';
                                          $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;"><strong>'.ObtenEtiqueta(1727).'</strong>   </td>';
                                          //$htmlcontent .='<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          //$htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          $htmlcontent .= '</tr>';
                                          
                                          $htmlcontent .= '<tr>';
                                          $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.$ds_nombres.' '.$ds_apaterno.'   </td>';
                                          $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.ObtenEtiqueta(1728).':</td>';
                                          $htmlcontent .='<td  colspan="2" style="height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.$id_invoice_stripe.'</td>';
                                          
                                          $htmlcontent .='</tr>';
                                          
                                          
                                          
                                          
                                          
                                          $htmlcontent .= '<tr>';
                                          $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.$nb_instituto.'   ';
                                          $htmlcontent .='</td>';
                                          $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.ObtenEtiqueta(1730).':</td>';
                                          $htmlcontent .='<td colspan="2" style="height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.$fe_emision.'</td>';
                                          // $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          $htmlcontent .='</tr>';
                                          
                                          
                                          
                                          
                                          
                                          
                                          
                                          $htmlcontent .= '<tr>';
                                          $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">';
                                          if($ds_numero_casa)
                                              $htmlcontent.=''.$ds_numero_casa.' ';
                                          $htmlcontent.=''.$ds_calle.' '.$ds_codigo_postal.'';
                                          
                                          $htmlcontent .='</td>';
                                          $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.ObtenEtiqueta(1731).':</td>';
                                          $htmlcontent .='<td colspan="3" style=" height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.$id_pago_stripe.' </td>';
                                          
                                          $htmlcontent .='</tr>';
                                          
                                          
                                          
                                          
                                          
                                          
                                          $htmlcontent .= '<tr>';
                                          $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">';
                                          if($ds_estado)
                                              $htmlcontent .=''.$ds_estado.' ';
                                          $htmlcontent .=''.$ds_pais.'  </td>';

                                          $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.ObtenEtiqueta(1732).': </td>';
                                          
                                          $htmlcontent .='<td colspan="2" style=" height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.$nb_plan_fame.'  </td>';
                                          $htmlcontent .='</tr>';
                                          
                                          
                                          
                                          
                                          
                                          /*
                                          $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_plan_fame.' </td>';
                                          $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          $htmlcontent .='</tr>';
                                           */
                                          $htmlcontent .= '<tr>';
                                          $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">  </td>';
                                          $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left; margin-lef:35px;">'.ObtenEtiqueta(1715).':</td>';
                                          $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.$fe_pago.' </td>';
                                          $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          $htmlcontent .='</tr>';
                                          
                                          
                                          
                                          $htmlcontent .= '<tr>';
                                          $htmlcontent .='<td style=" width:50%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;"><b>'.ObtenEtiqueta(1733).':</b> '.$id_cliente_stripe.' </td>';
                                          $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"></td>';
                                          $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          $htmlcontent .='<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          $htmlcontent .='</tr>';
                                          

                                          $htmlcontent .='<tr>';
                                          $htmlcontent .='<td colspan="4" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> <br/> <br/></td>';
                                          $htmlcontent .='</tr>';
                                          
                                          
                                          
                                          
                                          $htmlcontent .= '<tr>';
                                          $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:40%; height:15px; color:#000000; font-family:Arial; font-size:37px;  text-align:center;"><b>'.ObtenEtiqueta(1716).' </b> </td>';
                                          $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:30%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:center;"><b>'.ObtenEtiqueta(1734).' </b></td>';
                                          $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:10%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:center;"><b> '.ObtenEtiqueta(1717).' </b>  </td>';
                                          $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:20%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:right;"><b> '.ObtenEtiqueta(1718).' ('.ObtenConfiguracion(113).'$)</b>  </td>';
                                          $htmlcontent .= '</tr>';

                                          
                                          
                                          //$htmlcontent .= '<tr >';
                                          //$htmlcontent .='<td colspan="4" style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><br/>  </td>';
                                          //$htmlcontent .= '</tr>';
                                          
                                          
                                          
                                          
                                          $htmlcontent .= '<tr >';
                                          $htmlcontent .='<td style="border-bottom:2px solid #000; width:40%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.$ds_descripcion_pago.'  </td>';
                                          $htmlcontent .='<td style="border-bottom:2px solid #000; width:30%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:left;">'.$interval.' </td>';
                                          $htmlcontent .='<td style="border-bottom:2px solid #000; width:10%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:center;">'.$cantidad.'</td>';
                                          $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:right;">'.number_format($mn_monto_sin_tax,2).' </td>';
                                          $htmlcontent .= '</tr>';

                                          
                                          
                                          #Obtenemos el porcentaje del tax.
                                          $mn_porcentaje=($mn_tax/$mn_monto_sin_tax) *100;
                                          
                                          
                                          //$htmlcontent .= '<tr >';
                                          //$htmlcontent .='<td colspan="4" style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><br/>  </td>';
                                          //$htmlcontent .= '</tr>';
                                          
                                          
                                          
                                          #Presentamos Descuento.
                                          //$htmlcontent .= '<tr >';
                                          //$htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          //$htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                          //$htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1750).'  </td>';
                                          //$htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.$mn_descuento.' %</td>';
                                          //$htmlcontent .= '</tr>';
                                          
                                          
                                          
                                          #Presentamos totale y tax.
                                          $htmlcontent .= '<tr >';
                                          $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                          $htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:right;">'.ObtenEtiqueta(1736).' '.ObtenConfiguracion(113).' </td>';
                                          $htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:right;">'.number_format($mn_monto_sin_tax,2).' </td>';
                                          $htmlcontent .= '</tr>';
                                          
                                          
                                          $htmlcontent .= '<tr >';
                                          $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                          $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:right;">*'.ObtenEtiqueta(1735).' ('.number_format($mn_porcentaje).'%) </td>';
                                          $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:right;">'.number_format($mn_tax,2).' </td>';
                                          $htmlcontent .= '</tr>';
                                          
                                          
                                          
                                          
                                          #Presentamos totale y tax.
                                          $htmlcontent .= '<tr >';
                                          $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                          $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                          $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:right;">'.ObtenEtiqueta(1742).' '.ObtenConfiguracion(113).'</td>';
                                          $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:27px;  text-align:right;">'.$mn_total.'  </td>';
                                          $htmlcontent .= '</tr>';
                                          
                                          
                                          
                                          
                                          $htmlcontent .= '</table>';
                                          
                                          
                                          
                                          
                                          $ds_cuerpo2=$htmlcontent;
                                          // output the HTML content contenido del pdf
                                          $pdf->writeHTMLCell(180, 100, 10,60,$ds_cuerpo2, 0, 0, false, true,'',true); 
                                          //nombre del archivo
                                          $fileName ='Invoice_'.$nb_instituto.'_'.$id_pago_stripe.'.pdf';
                                          // pasamos el archivo a base64
                                          //$pdf->Output($fileName, 'F');///guarda el archivo MRA: Se deja comen tado porque se cambio el metodo para que ahora vaya como attachment
                                          $fileatt = $pdf->Output($fileName, 'E'); //genera la codificacion para enviar adjuntado el archivo
  
                                          
  
                                          
    
                                            //envia copia a admin@vanas.ca
                                            $admin = ObtenConfiguracion(20);
                                            // $apply=ObtenConfiguracion(83);
                                            $from=MAIL_FROM;

    
                                            # Inicializa variables de ambiente para envio de correo adjunto
                                            ini_set("SMTP", MAIL_SERVER);
                                            ini_set("smtp_port", MAIL_PORT);
                                            ini_set("sendmail_from", MAIL_FROM);
                                            $repEmail = $from;
                                            $nb_nombre_dos=ObtenEtiqueta(1646);#nombre de quien envia el mensaje
                                            $eol = "\n";
                                            $separator = md5(time());
    
                                            $subject=ObtenEtiqueta(1768);#etiqueta de asunto del mensjae
                                            $headers  = 'MIME-Version: 1.0' .$eol;
                                            // $headers .= 'From: "'.$ds_subject.'" <'.$repEmail.'>'.$eol;
                                            $headers .= 'From: "'.$nb_nombre_dos.'" <'.$repEmail.'>'.$eol;
                                            $admin=ObtenConfiguracion(107);
                                            $headers .= "Bcc: $admin \r\n";
                                            $headers .= 'Content-Type: multipart/mixed; boundary="'.$separator.'"';
    
                                            $message = "--".$separator.$eol;
                                            $message .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
                                            // $message .= "Content-Transfer-Encoding: quoted-printable ".$eol.$eol;
                                            #Presenta mensaje del email
                                            $message .= utf8_decode($ds_header).utf8_decode($ds_cuerpo).utf8_decode($ds_footer).$eol;

                                            $message .= "--".$separator.$eol;
                                            $message .= $fileatt;
                                            $message .= "--".$separator."--".$eol;
    
                                            # insertamos el envio del email
                                            $mail=mail($ds_email, $subject, $message, $headers);
                                
                                
                         /************************************************************************************************/
                         
                         
                
                        // $email_invoice=EnviaEmailInvoice($fl_instituto,$fl_usuario,$id_invoice,$id_charge,$fileatt);  
                
                
                
            }
            

    
             if($fg_motivo_pago==PAGO_NEW_PLAN){        
                  
                  
        
 
        
        
                                   # Notify user of send payment
                                   $result["paid"] = true;
                                   $result["message"] = "<script>
                                                              $(document).ready(function() {
                                                                 $('#presenta_gif').addClass('hidden');
                                                              });
                                                          </script> 
                                                          <a class='btn btn-success btn-sm hidden' href='index.php#site/node.php?node=155' id='redirigir_billing'><i class='fa fa-upload'></i> redirige</a>
                                                          
                                                          <div class='text-center error-box'>
                                                               <h5> <i class='fa fa-check-circle  text-success success-icon-shadow'></i> <strong>".ObtenEtiqueta(1739)."</strong></h5>
                                                          </div>
                                                        
                                                        
	                                                      <script>
                                                            //le asignamo un retardo para confirmar nuestro pago
                                                            setTimeout(function(){ 
                                                            location.reload();///document.getElementById('redirigir_billing').click();//clic au   
                                                            }, 4000);
                                                        </script>
	                                                ";
                     
               }else{
             
             
                 
                             #Se valida suscripcion.
                             if (  $charge->paid == true) {
                       
                                 $result["paid"] = true;
                                 $result["message"] = "  <script>
                                                          $(document).ready(function() {
                                                             $('#presenta_gif').addClass('hidden');
                                                          });
                                                         </script>
                                                            <a class='btn btn-success btn-sm hidden' href='index.php#site/node.php?node=155' id='redirigir_billing'><i class='fa fa-upload'></i> redirige</a>
                                                              <div class='text-center error-box'>
                                                                <h5> <i class='fa fa-check-circle  text-success success-icon-shadow'></i> <strong>".ObtenEtiqueta(1739)."</strong></h5>
                                                              </div>
	                                                          <script>
                                                                 //le asignamo un retardo para confirmar nuestro pago
                                                                 setTimeout(function(){ 
                                                                     document.getElementById('redirigir_billing').click();//clic au   
                                                                 }, 4000);
                                                              </script>
	                                                        ";
                                 }else{
                     
                                     $result["paid"] = false;
                     
                                 }
                 
                  
                 
                 
             
             
                }#END NEW PLAN
             
             
             
              
             
             
             
             
      
  }#END ELSE  
  
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