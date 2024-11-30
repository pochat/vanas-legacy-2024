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
  $nb_titular_tarjeta=RecibeParametroHTML('cardholder-name');
  //$nb_titular_tarjeta="Migyel Jimenez";
  
  # Variables Stripe
  $secret_key = ObtenConfiguracion(112);
  
  #Recuperamos datos de la cuenta de Billing
  $Query="SELECT fl_current_plan,id_cliente_stripe FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fl_current_plan=$row['fl_current_plan'];
  $id_cliente_stripe=str_texto($row['id_cliente_stripe']);
  
  // create the charge on Stripe's servers - this will charge the user's card
   try {
  # set your secret key: remember to change this to your live secret key in production
  # see your keys here https://manage.stripe.com/account
  \Stripe\Stripe::setApiKey($secret_key);
  

   
  ##Actualizamos la tarjeta con la pagara.      
  $cu = \Stripe\Customer::retrieve($id_cliente_stripe); // stored in your application
  $cu->source = $token; // obtained with Checkout
  $cu->save(); 
      
      
  #Recuperamos la fecha de expiracion de la tarjeta.
  $dta=$cu->sources;
  $fe_anio_expiracion_tarjeta=$dta['data']['0']->exp_year;
  $fe_mes_expiracion_tarjeta=$dta['data']['0']->exp_month;
  $id_tarjeta=$dta['data']['0']->id;
  $numero_tarjeta=$dta['data']['0']->last4;
  $card_id=$dta['data']['0']->id;    
  
  
     
  #Actualizamos el nombre del titular.
  $customer = \Stripe\Customer::retrieve($id_cliente_stripe);
  $card = $customer->sources->retrieve($card_id);
  $card->name = $nb_titular_tarjeta;
  $card->save();        
     


  #Actualizamos fecha de vencimeinto de la tarjeta en su plan de pago.
  $Query="UPDATE k_current_plan SET fe_mes_expiracion_tarjeta='$fe_mes_expiracion_tarjeta',no_tarjeta=$numero_tarjeta , fe_anio_expiracion_tarjeta='$fe_anio_expiracion_tarjeta'  
		  WHERE fl_current_plan=$fl_current_plan AND fl_instituto=$fl_instituto ";
  EjecutaQuery($Query);

   
    
      
  $result["correct"] = true;             
		 
  #Generamos el template para enviar un email de notificacion.

  $ds_encabezado = genera_documento_sp($fl_usuario,1,151);
  $ds_cuerpo = genera_documento_sp($fl_usuario,2,151);
  $ds_pie = genera_documento_sp($fl_usuario,3,151);
  $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;
				   
  #Recupermaos el email del usuario 
  $Quer="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Quer);
  $ds_email_destinatario=str_texto($row[0]);
  
  
  $ds_contenido = str_replace("#fame_responsible_credit_card#", $nb_titular_tarjeta, $ds_contenido);  #no_dias_cuso
                       
  #Recuperamos el titulo del documento
  $Query="SELECT nb_template FROM k_template_doc WHERE fl_template=151 ";
  $row=RecuperaValor($Query);
  $ds_titulo=str_texto($row[0]);

  $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);
  $nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje
  $bcc=ObtenConfiguracion(107);
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  #Envia email de notificcion al usuario
  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
                           	 
             
      
    
  
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