<?php
# Libreria de funciones
require("../lib/sp_general.inc.php");
require("../lib/sp_session.inc.php");
// require("../lib/sp_forms.inc.php");
require("lib/app_forms.inc.php");
require("app_form.inc.php");
# Include the Stripe library
//require_once('../fame/lib/Stripe/Stripe/init.php'); //version vieja Stripe
require_once('Stripe2022/vendor/stripe/stripe-php/init.php'); //new version Stripe
$cl_sesion=$_POST['std_cl_sesion'];
$currency=RecibeParametroHTML('currency');
$calholder=RecibeParametroHTML('cardholder-name');
$email=RecibeParametroHTML('email');
$mn_app_fee=$_POST['mn_app_fee_stripe'];
$mn_app_fee_only=$_POST['mn_app_fee_stripe'];
$mn_tuition=$_POST['mn_tuition_stripe'];
$token = RecibeParametroHTML('stripeToken');
$stripepaymentMethodId=RecibeParametroHTML('stripepaymentMethodId');
$fl_periodo=$_POST['std_fl_periodo'];
$fl_programa=$_POST['std_fl_programa'];
$fl_pais_selected=$_POST['fl_pais_selected'];
$mn_cupon=$_POST['mn_cupon'];
$mn_tax=$_POST['percentage_tax'];
$mn_tax_amount=0;
$cupon=$_POST['cupon'];


$convenience_fee_app_fee=0;


$QueryP = "SELECT fg_total_programa,fg_tax_rate, fl_template,fg_tax_rate_internacional,fg_tax_rate_combined,fg_tax_rate_internacional_combined FROM c_programa WHERE fl_programa=".$fl_programa;
$RowP = RecuperaValor($QueryP);
$fg_total_programa = $RowP[0];
$fg_tax_rate = $RowP[1]; // si necesita que se cobre impuesto
$fl_template = $RowP[2];
$fg_tax_rate_internacional=$RowP['fg_tax_rate_internacional'];
$fg_tax_rate_combined=$RowP['fg_tax_rate_combined'];
$fg_tax_rate_internacional_combined=$RowP['fg_tax_rate_internacional_combined'];

$QyeryStudent="SELECT ds_fname,ds_lname FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion' ";
$rowstu=RecuperaValor($QyeryStudent);
$ds_fname = $rowstu['ds_fname'];
$ds_lname = $rowstu['ds_lname'];

$Queryapp = "SELECT tax_mn_cost,ds_costs,mn_costs FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
$rowapp = RecuperaValor($Queryapp);
$tax_mn_cost = !empty($rowapp['tax_mn_cost']) ? $rowapp['tax_mn_cost'] : 0;
$mn_costs = !empty($rowapp['mn_costs']) ? $rowapp['mn_costs'] : 0;

$ds_tax="Tax";
$mn_tax_tuituion=0;
$tuition_with_tax=0;
if(!empty($fg_total_programa)){

    if($mn_tax>0){
        $mn_tax_amount=($mn_app_fee * $mn_tax) /100;  //tax app_fee
        $mn_tax_tuituion=($mn_tuition * $mn_tax) /100; //tax tuition.

    }

    $mn_app_fee_with_tax=$mn_app_fee+$mn_tax_amount;
    $mn_app_fee=$mn_app_fee+$mn_tuition;






    if(($fg_total_programa==1)&&( ($fg_tax_rate==1)||($fg_tax_rate_international==1)||($fg_tax_rate_combined==1)||($fg_tax_rate_internacional_combined==1) )){
        if($mn_tax>0){
            $mn_tax_amount=($mn_app_fee*$mn_tax) / 100 ;
        }

    }




}else{
    if($mn_tax>0){
        $mn_tax_amount=($mn_app_fee * $mn_tax) /100;
        $mn_tax_tuituion=($mn_tuition * $mn_tax) /100;
    }
    $mn_app_fee_with_tax=$mn_app_fee+$mn_tax_amount;
    $tuition_with_tax=$mn_tuition+$mn_tax_tuituion;


}
if(empty($calholder)){
    $calholder=$email;
}


$mn_amount=$mn_app_fee + $mn_tax_amount;

$convenience_fee_percentage=ObtenConfiguracion(165);
$convenience_fee=($mn_amount*$convenience_fee_percentage)/100;
$convenience_fee_tuition=($tuition_with_tax*$convenience_fee_percentage)/100;
$convenience_fee_app_fee=($mn_app_fee_with_tax*$convenience_fee_percentage)/100;


if (!empty($fg_total_programa)) {

    $mn_app_fee_stripe = ($mn_app_fee + number_format($mn_tax_amount, 2) + number_format($convenience_fee, 2) + number_format($tax_mn_cost,2) ) * 100;


} else {

    $mn_app_fee_stripe = ($mn_app_fee + number_format($mn_tax_amount, 2) + number_format($convenience_fee, 2)) * 100;


}



#Recupermaos el nombre del programa.
$Query="SELECT nb_programa FROM c_programa WHERE fl_programa=$fl_programa ";
$row=RecuperaValor($Query);
$nb_programa=$row['nb_programa'];

$Query="SELECT nb_periodo FROM c_periodo WHERE fl_periodo=$fl_periodo ";
$row=RecuperaValor($Query);
$nb_periodo=$row['nb_periodo'];

#Recuperamos term para los pagos.
$Query="SELECT fl_term FROM k_term WHERE fl_programa=$fl_programa AND fl_periodo=$fl_periodo  ";
$ros=RecuperaValor($Query);
$fl_term=$ros['fl_term'];

$Query="SELECT fl_term_pago FROM k_term_pago WHERE fl_term=$fl_term AND no_pago='1'  ";
$ros=RecuperaValor($Query);
$fl_term_pago=$ros['fl_term_pago'];

$ds_descripcion="$ds_fname $ds_lname, App fee and Program:$nb_programa, Period:$nb_periodo $currency";


if($fl_pais_selected==226){
    # Variables Stripe
    $secret_key = ObtenConfiguracion(167);
}else{
    # Variables Stripe
    $secret_key = ObtenConfiguracion(112);
}
// create the charge on Stripe's servers - this will charge the user's card
try {
    # set your secret key: remember to change this to your live secret key in production
    # see your keys here https://manage.stripe.com/account
    \Stripe\Stripe::setApiKey($secret_key);

    /*
    # Crea cliente
    $customer = \Stripe\Customer::create(array(
      "email" => $email,
      "description" => $calholder,
      "source" => $token,
    ));
    */
    try {
        # Create the PaymentIntent
        $intent = \Stripe\PaymentIntent::create([
          'payment_method' => $stripepaymentMethodId,
          'amount' => $mn_app_fee_stripe,
          'currency' => $currency,
          'description'=> $ds_descripcion,
          'confirmation_method' => 'manual',
          'confirm' => true,
        ]);
        $paymentIntentID=$intent->id;
        $payment_intent_client_secret=$intent->client_secret;

        $action_payment=$intent->status;

    }
    catch (\Stripe\Exception\ApiErrorException $e) {

        $e_json = $e->getJsonBody();
        $err = $e_json['error'];

        $result['payment'] = false;
        $result['error'] = $err['message'];
        echo json_encode((Object) $result);
        exit;
    }


    if(($action_payment=="requires_source")||($action_payment=="requires_source_action")){



        $intent = \Stripe\PaymentIntent::retrieve($paymentIntentID);
        $intent->confirm();


        #save data en DB.
        EjecutaQuery("DELETE FROM stripe_payment_intent WHERE payment_intent_id='$paymentIntentID' ");
        $QueryIntent ="INSERT INTO stripe_payment_intent(cl_sesion,payment_intent_id,currency,calholder,mn_app_fee_only,charge_id,mn_tax_amount,ds_tax_provincia,convenience_fee,fl_term_pago,mn_tuition,ds_transaccion,convenience_fee_tuition,mn_tax_tuituion,fe_creacion )";
        $QueryIntent.="VALUES('$cl_sesion','$paymentIntentID','$currency','$calholder','$mn_app_fee_only','','$mn_tax_amount','$ds_tax','$convenience_fee_app_fee','$fl_term_pago','$mn_tuition','$ds_transaccion','$convenience_fee_tuition','$mn_tax_tuituion',CURRENT_TIMESTAMP) ";
        $id=EjecutaInsert($QueryIntent);




        #devuelve json indicando que requiere confirmar el pago se abrira una ventana en front end.
        $result['payment'] = "";
        $result['error'] = "";
        $result['requires_action']=True;
        $result['action']= $action_payment;
        $result['payment_intent_client_secret']= $payment_intent_client_secret;

        echo json_encode((Object) $result);
        exit;
    } else if ($intent->status == 'succeeded') {

        # The payment didn’t need any additional actions and completed!
        $charge=$intent->charges;
        $ds_transaccion=$charge->data[0]->id;
    }




    # Charge the order:
  /*  $charge = \Stripe\Charge::create(array(
      "amount" => $mn_app_fee_stripe,
      "currency" => $currency,
      "customer" => $customer->id,
      "description" => $ds_descripcion,
      "metadata" => array("tax" => ($mn_tax/100))
      )
    );
    */

    if ($charge->data[0]->paid == true) {

        EjecutaQuery("UPDATE k_app_contrato set fg_opcion_pago='1' WHERE cl_sesion='$cl_sesion' ");

        EjecutaQuery("UPDATE c_sesion set fg_paypal='1',fg_stripe='1',fg_pago='1' WHERE cl_sesion='$cl_sesion' ");

        $QueryAPP  = "UPDATE c_sesion SET fg_paypal='1',fg_stripe='1', fg_confirmado='1', fg_pago='1',  ";
        $QueryAPP .= "fe_ultmod=CURRENT_TIMESTAMP, cl_metodo_pago=1, fe_pago=CURRENT_TIMESTAMP, mn_pagado=$mn_app_fee_only, ds_transaccion='$ds_transaccion', ";
        $QueryAPP .= "mn_tax_paypal='$mn_tax_amount', ds_tax_provincia='$ds_tax',convenience_fee=$convenience_fee_app_fee
                  WHERE cl_sesion='$cl_sesion' ";
        EjecutaQuery($QueryAPP);

        EjecutaQuery("UPDATE c_sesion set fg_paypal='1',fg_stripe='1',fg_pago='1' WHERE cl_sesion='$cl_sesion' ");

        if(!empty($mn_cupon)){

            $roww = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion='".$cl_sesion."'");
            EjecutaQuery("DELETE FROM k_app_cupon WHERE fl_sesion=".$roww[0]);
            $Queri  = "INSERT INTO k_app_cupon(fl_sesion, ds_code, ds_descuento, fe_app) ";
            $Queri .= "VALUES($roww[0], '".$cupon."', '".$mn_cupon."', NOW()) ";
            EjecutaQuery($Queri);
        }

        if(!empty($fg_total_programa)){
            ##generate invoice.}
            EjecutaQuery("DELETE FROM k_ses_pago WHERE cl_sesion='".$cl_sesion."' AND fl_term_pago=$fl_term_pago ");

            $QueryPago="INSERT INTO k_ses_pago(cl_sesion,fl_term_pago,cl_metodo_pago,fe_pago,mn_pagado,ds_transaccion,ds_tax_provincia,mn_convenience_fee,mn_tax_paypal) ";
            $QueryPago.="VALUES('".$cl_sesion."',$fl_term_pago,1,CURRENT_TIMESTAMP,$mn_tuition,'$ds_transaccion','$ds_tax',$convenience_fee_tuition,$mn_tax_tuituion)";
            $dt=EjecutaInsert($QueryPago);

        }



        if($mn_tax_amount){
            EjecutaQuery("UPDATE c_sesion SET ds_tax_provincia='GST' WHERE cl_sesion='$cl_sesion'   ");
        }
        EjecutaQuery("UPDATE c_sesion SET fg_app_1='1',fg_app_2='1',fg_app_3='1',fg_app_4='1',fg_pago='1'  WHERE cl_sesion='".$cl_sesion."'");
        EnviarEmailForms2($cl_sesion,false);

        $Query="SELECT ds_fname,ds_lname  FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion'  ";
        $ro=RecuperaValor($Query);
        $ds_fname=$ro[0];
        $ds_lname=$ro[1];
        # Envia correo de confirmacion al aplicante
        $subject = ObtenEtiqueta(335);
        $message  = "Dear $ds_fname $ds_lname,<br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(331)."<br>";
        $message .= ObtenEtiqueta(332)."<br>";
        $message .= ObtenEtiqueta(333)."<br><br>";
        $message .= ObtenEtiqueta(337)."<br>";
        $message .= ObtenEtiqueta(338)."<br>";
        $message = utf8_encode(str_ascii($message));
        # Prepara variables de ambiente para envio de correo FORM1
        $smtp = ObtenConfiguracion(4);
        EnviaMailHTML($smtp, $smtp, $email, $subject, $message);

        #Envio copia pago Erika VANAS  Sonia VANAS.
        #Prepara variables de ambiente para envio de correo FORM1
        $smtp = ObtenConfiguracion(4);
        $messagePayment="Application Forms <br><br>";
        $messagePayment.="Student Name: $ds_fname $ds_lname <br>";
        $messagePayment.="Total: $ds_descripcion $".$mn_app_fee_stripe / 100;
        $messagePayment.="<br>Payment ID: $ds_transaccion <br>";

        EnviaMailHTML($smtp, $smtp, 'erika@vanas.ca', 'Payment Stripe', $messagePayment);
        EnviaMailHTML($smtp, $smtp, 'sonia@vanas.ca', 'Payment Stripe', $messagePayment);


        #envia notificacion de pago al cliente.
        $Query  = "SELECT nb_template,ds_encabezado,ds_cuerpo,ds_pie FROM k_template_doc WHERE fl_template=217 ";
        $row = RecuperaValor($Query);
        $nb_template=$row['nb_template'];
        $ds_encabezado=html_entity_decode($row['ds_encabezado']);
        $ds_cuerpo=html_entity_decode($row['ds_cuerpo']);
        $ds_pie=html_entity_decode($row['ds_pie']);

        $messagePayment=$ds_encabezado.$ds_cuerpo.$ds_pie;

        $messagePayment = str_replace("#st_fname#", $ds_fname, $messagePayment);
        $messagePayment = str_replace("#st_lname#", $ds_lname, $messagePayment);

       // EnviaMailHTML($smtp, $smtp, $email, $nb_template, $messagePayment);



        $result['payment'] = true;
        $result['error'] = false;
        $result['requires_action']=False;



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


function EnviarEmailForms2($clave,$email_f1=true,$fg_paso=''){
    # Prepara variables de ambiente para envio de correo FORM1
    $smtp = ObtenConfiguracion(4);
    $app_frm_email = ObtenConfiguracion(20);

    # sesion
    $QueryS = "SELECT ".ConsultaFechaBD('fe_ultmod', FMT_CAPTURA)." 'fe_ultmod' FROM c_sesion WHERE cl_sesion='".$clave."'";
    $rowS = RecuperaValor($QueryS);
    $fe_ultmod = $rowS[0];

    # Envia correo de confirmacion al Administrador
    $QueryE  = "SELECT a.fl_programa, a.fl_periodo, ds_fname, ds_mname, ds_lname, ds_number,ds_alt_number, ds_email, fg_gender, ";
    $QueryE .= ConsultaFechaBD('fe_birth', FMT_CAPTURA)." fe_birth, ds_add_number, ";
    $QueryE .= "ds_add_street, ds_add_city, ds_add_state, ds_add_zip, ds_add_country, fg_responsable, ds_eme_fname, ds_eme_lname, ds_eme_number, ";
    $QueryE .= "ds_eme_relation, ds_eme_relation_other, ds_eme_country, cl_recruiter, fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, ";
    $QueryE .= "nb_programa, nb_periodo ";
    //$QueryE .= ", e.ds_pais ";
    $QueryE .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c ";
    $QueryE .= "WHERE a.fl_programa=b.fl_programa ";
    $QueryE .= "AND a.fl_periodo=c.fl_periodo ";
    //$QueryE .= "AND a.ds_add_country=d.fl_pais ";
    //$QueryE .= "AND a.ds_eme_country=e.fl_pais ";
    $QueryE .= "AND cl_sesion='".$clave."'";
    $rowE = RecuperaValor($QueryE);
    $fl_programa = $rowE[0];
    $fl_periodo = $rowE[1];
    $ds_fname = $rowE[2];
    $ds_mname = $rowE[3];
    $ds_lname = $rowE[4];
    $ds_number = $rowE[5];
    $ds_alt_number = $rowE[6];
    $ds_email = $rowE[7];
    $fg_gender = $rowE[8];
    $fe_birth = $rowE[9];
    $ds_add_number = str_texto($rowE[10]);
    $ds_add_street = str_texto($rowE[11]);
    $ds_add_city = str_texto($rowE[12]);
    $ds_add_state = str_texto($rowE[13]);
    $ds_add_zip = str_texto($rowE[14]);
    $ds_add_country = $rowE[15];
    $fg_responsable = $rowE[16];
    $ds_eme_fname = str_texto($rowE[17]);
    $ds_eme_lname = str_texto($rowE[18]);
    $ds_eme_number = $rowE[19];
    $ds_eme_relation = str_texto($rowE[20]);
    $ds_eme_relation_other = str_texto($rowE[21]);
    $ds_eme_country = $rowE[22];
    $cl_recruiter = $rowE[23];
    $fg_ori_via = $rowE[24];
    $ds_ori_other = $rowE[25];
    $fg_ori_ref = $rowE[26];
    $ds_ori_ref_name = $rowE[27];
    $nb_programa = $rowE[28];
    $nb_periodo = $rowE[29];

    #Recupermaos pais
    $Query="SELECT b.ds_pais,a.ds_add_country FROM k_ses_app_frm_1 a JOIN c_pais b ON b.fl_pais=a.ds_eme_country WHERE cl_sesion='$clave' ";
    $rop=RecuperaValor($Query);
    $ds_pais = $rop[0];
    $ds_add_country=$rop['ds_add_country'];

    //if(empty($ds_add_country)){
    //  EjecutaQuery("UPDATE k_ses_app_frm_1 set ds_add_country=$ds_pais WHERE cl_sesion='$clave'   ");
    //}



    if(is_numeric($ds_add_country)) {

        $Query="SELECT ds_pais FROM c_pais where fl_pais= $ds_add_country ";
        $row=RecuperaValor($Query);
        $ds_add_country=str_texto($row[0]);

    }

    if(is_numeric($ds_add_state)) {

        $Query="SELECT ds_provincia FROM k_provincias where fl_provincia= $ds_add_state ";
        $row=RecuperaValor($Query);
        $ds_add_state=str_texto($row[0]);

    }



    # Consulta contrato
    $QueryC  = "SELECT ds_p_name, ds_education_number, fg_international, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_m_add_country,  ";
    $QueryC .= "ds_a_email, cl_preference_1, cl_preference_2, cl_preference_3,fl_class_time FROM k_app_contrato WHERE cl_sesion='".$clave."' AND no_contrato=1";
    $rowC = RecuperaValor($QueryC);
    $ds_p_name = str_texto($rowC[0]);
    $ds_education_number = str_texto($rowC[1]);
    $fg_international = $rowC[2];
    $ds_m_add_number = str_texto($rowC[3]);
    $ds_m_add_street = str_texto($rowC[4]);
    $ds_m_add_city = str_texto($rowC[5]);
    $ds_m_add_state = str_texto($rowC[6]);
    $ds_m_add_zip = str_texto($rowC[7]);
    $ds_m_add_country = str_texto($rowC[8]);
    $ds_a_email = str_texto($rowC[9]);
    $cl_preference_1 = $rowC[10];
    $cl_preference_2 = $rowC[11];
    $cl_preference_3 = $rowC[12];
    $fl_class_time=$rowC['fl_class_time'];



    # Recupera datos del aplicante: forma 2
    $Query  = "SELECT ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7 ";
    $Query .= "FROM k_ses_app_frm_2 ";
    $Query .= "WHERE cl_sesion='$clave'";
    $row = RecuperaValor($Query);
    $ds_resp2_1 = str_ascii($row[0]);
    $ds_resp2_2 = str_ascii($row[1]);
    $ds_resp2_3 = str_ascii($row[2]);
    $ds_resp2_4 = str_ascii($row[3]);
    $ds_resp2_5 = str_ascii($row[4]);
    $ds_resp2_6 = str_ascii($row[5]);
    $ds_resp2_7 = str_ascii($row[6]);

    # Recupera datos del aplicante: forma 3
    $Query  = "SELECT fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, ";
    $Query .= "fg_resp_2_1, fg_resp_2_2, fg_resp_2_3, fg_resp_2_4, fg_resp_2_5, fg_resp_2_6, fg_resp_2_7, ";
    $Query .= "fg_resp_3_1, fg_resp_3_2 ";
    $Query .= "FROM k_ses_app_frm_4 ";
    $Query .= "WHERE cl_sesion='$clave'";
    $row = RecuperaValor($Query);
    $fg_resp4_1_1 = str_ascii($row[0]);
    $fg_resp4_1_2 = str_ascii($row[1]);
    $fg_resp4_1_3 = str_ascii($row[2]);
    $fg_resp4_1_4 = str_ascii($row[3]);
    $fg_resp4_1_5 = str_ascii($row[4]);
    $fg_resp4_1_6 = str_ascii($row[5]);
    $fg_resp4_2_1 = str_ascii($row[6]);
    $fg_resp4_2_2 = str_ascii($row[7]);
    $fg_resp4_2_3 = str_ascii($row[8]);
    $fg_resp4_2_4 = str_ascii($row[9]);
    $fg_resp4_2_5 = str_ascii($row[10]);
    $fg_resp4_2_6 = str_ascii($row[11]);
    $fg_resp4_2_7 = str_ascii($row[12]);
    $fg_resp4_3_1 = str_ascii($row[13]);
    $fg_resp4_3_2 = str_ascii($row[14]);

    # Recupera datos del aplicante: forma 4
    $Query  = "SELECT ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, ds_resp_8 ";
    $Query .= "FROM k_ses_app_frm_3 ";
    $Query .= "WHERE cl_sesion='$clave'";
    $row = RecuperaValor($Query);
    $ds_resp3_1 = str_ascii($row[0]);
    $ds_resp3_2_1 = str_ascii($row[1]);
    $ds_resp3_2_2 = str_ascii($row[2]);
    $ds_resp3_2_3 = str_ascii($row[3]);
    $ds_resp3_3 = str_ascii($row[4]);
    $ds_resp3_4 = str_ascii($row[5]);
    $ds_resp3_5 = str_ascii($row[6]);
    $ds_resp3_6 = str_ascii($row[7]);
    $ds_resp3_7 = str_ascii($row[8]);
    $ds_resp3_8 = str_ascii($row[9]);
    # Priemr email cuando termina la forma 1
    if($email==true){
        $subject = ObtenEtiqueta(336);
        $message  = "Application form component 1 submitted <br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(55)."<br>";
        $rs = RecuperaValor("SELECT nb_programa FROM c_programa WHERE fl_programa = $fl_programa");
        $message .= ObtenEtiqueta(59).": $rs[0]<br>";
        $message .= "<br>";
        $rs = RecuperaValor("SELECT nb_periodo FROM c_periodo WHERE fl_periodo = $fl_periodo");
        $message .= ObtenEtiqueta(60).": $rs[0]<br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(61)."<br>";
        $message .= ObtenEtiqueta(117).": $ds_fname<br>";
        $message .= ObtenEtiqueta(119).": $ds_mname<br>";
        $message .= ObtenEtiqueta(118).": $ds_lname<br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(631).": $ds_p_name<br>";
        $message .= ObtenEtiqueta(632).": $ds_education_number<br>";
        if($fg_international == '0')
            $ds_international = ObtenEtiqueta(17);
        else
            $ds_international = ObtenEtiqueta(16);
        $message .= ObtenEtiqueta(620).": $ds_international<br>";
        $message .= "<br>";

        $message .= ObtenEtiqueta(280).": $ds_number<br>";
        $message .= ObtenEtiqueta(281).": $ds_alt_number<br>";
        $message .= ObtenEtiqueta(121).": $ds_email<br>";
        $message .= ObtenEtiqueta(127).": $ds_a_email<br>";
        $message .= "<br>";

        $message .= ObtenEtiqueta(114).": ";
        if($fg_gender == 'M')
            $message .= ObtenEtiqueta(115)."<br>";
        else
            $message .= ObtenEtiqueta(116)."<br>";
        $message .= ObtenEtiqueta(120).": $fe_birth<br>";
        $message .= "<br>";

        switch($cl_preference_1) {
            case 1: $ds_preference_1 = ObtenEtiqueta(624); break;
            case 2: $ds_preference_1 = ObtenEtiqueta(625); break;
            case 3: $ds_preference_1 = ObtenEtiqueta(626); break;
            case 4: $ds_preference_1 = ObtenEtiqueta(627); break;
            case 5: $ds_preference_1 = ObtenEtiqueta(628); break;
            case 6: $ds_preference_1 = ObtenEtiqueta(629); break;
            case 7: $ds_preference_1 = ObtenEtiqueta(630); break;
        }
        switch($cl_preference_2) {
            case 1: $ds_preference_2 = ObtenEtiqueta(624); break;
            case 2: $ds_preference_2 = ObtenEtiqueta(625); break;
            case 3: $ds_preference_2 = ObtenEtiqueta(626); break;
            case 4: $ds_preference_2 = ObtenEtiqueta(627); break;
            case 5: $ds_preference_2 = ObtenEtiqueta(628); break;
            case 6: $ds_preference_2 = ObtenEtiqueta(629); break;
            case 7: $ds_preference_2 = ObtenEtiqueta(630); break;
        }
        switch($cl_preference_3) {
            case 1: $ds_preference_3 = ObtenEtiqueta(624); break;
            case 2: $ds_preference_3 = ObtenEtiqueta(625); break;
            case 3: $ds_preference_3 = ObtenEtiqueta(626); break;
            case 4: $ds_preference_3 = ObtenEtiqueta(627); break;
            case 5: $ds_preference_3 = ObtenEtiqueta(628); break;
            case 6: $ds_preference_3 = ObtenEtiqueta(629); break;
            case 7: $ds_preference_3 = ObtenEtiqueta(630); break;
        }


        $message .= ObtenEtiqueta(621)."<br>";
        $message .= ObtenEtiqueta(622).": $ds_preference_1<br>";
        $message .= ObtenEtiqueta(623).": $ds_preference_2<br>";
        $message .= ObtenEtiqueta(616).": $ds_preference_3<br>";
        $message .= "<br>";
        # Address
        $message .= ObtenEtiqueta(62)."<br>";
        $message .= ObtenEtiqueta(282).": $ds_add_number<br>";
        $message .= ObtenEtiqueta(283).": $ds_add_street<br>";
        $message .= ObtenEtiqueta(284).": $ds_add_city<br>";
        # si el pais es canada mostra la provincia que selecciono
        if($ds_add_country==38){
            $row = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$fl_provincia");
            $ds_add_state = $row[0];
        }
        $message .= ObtenEtiqueta(285).": $ds_add_state<br>";
        $message .= ObtenEtiqueta(286).": $ds_add_zip<br>";
        $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_add_country");
        $message .= ObtenEtiqueta(287).": $rs[0]<br>";
        $message .= "<br>";
        $QueryR = "SELECT ds_fname_r, ds_lname_r, ds_email_r, ds_aemail_r, ds_pnumber_r, ds_relation_r, ds_relation_r_other ";
        $QueryR .= "FROM k_presponsable WHERE cl_sesion='".$clave."'";
        $rowR = RecuperaValor($QueryR);
        $ds_fname_r = str_texto($rowR[0]);
        $ds_lname_r = str_texto($rowR[1]);
        $ds_email_r = str_texto($rowR[2]);
        $ds_aemail_r = str_texto($rowR[3]);
        $ds_pnumber_r = str_texto($rowR[4]);
        $ds_relation_r = $rowR[5];
        $ds_relation_r_other = str_texto($rowR[6]);
        # Person Responsible
        $message .= ObtenEtiqueta(865).".<br>";
        if($fg_responsable==1)
            $message .= ObtenEtiqueta(866);
        else{
            $message .= ObtenEtiqueta(867);
            $message .= "<br> ".ObtenEtiqueta(868).": ".$ds_fname_r."<br>";
            $message .= " ".ObtenEtiqueta(869).": ".$ds_lname_r."<br>";
            $message .= " ".ObtenEtiqueta(870).": ".$ds_email_r."<br>";
            $message .= " ".ObtenEtiqueta(871).": ".$ds_aemail_r."<br>";
            $message .= " ".ObtenEtiqueta(872).": ".$ds_pnumber_r."<br>";
            if($ds_relation_r == ObtenEtiqueta(2254))
                $message .= " ".ObtenEtiqueta(873).": ".$ds_relation_r_other."<br>";
            else
                $message .= " ".ObtenEtiqueta(873).": ".$ds_relation_r."<br>";
        }
        $message .="<br><br>";
        # Mailing Address (If different from above)
        $message .= ObtenEtiqueta(633)."<br>";
        $message .= ObtenEtiqueta(282).": $ds_m_add_number<br>";
        $message .= ObtenEtiqueta(283).": $ds_m_add_street<br>";
        $message .= ObtenEtiqueta(284).": $ds_m_add_city<br>";
        $message .= ObtenEtiqueta(285).": $ds_m_add_state<br>";
        $message .= ObtenEtiqueta(286).": $ds_m_add_zip<br>";
        $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_m_add_country");
        $message .= ObtenEtiqueta(287).": $rs[0]<br>";
        $message .= "<br>";

        # Emergency Contact Information
        $message .= ObtenEtiqueta(63)."<br>";
        $message .= ObtenEtiqueta(117).": $ds_eme_fname<br>";
        $message .= ObtenEtiqueta(118).": $ds_eme_lname<br>";
        $message .= ObtenEtiqueta(280).": $ds_eme_number<br>";
        if($ds_eme_relation == ObtenEtiqueta(2254))
            $message .= ObtenEtiqueta(288).": $ds_eme_relation_other<br>";
        else
            $message .= ObtenEtiqueta(288).": $ds_eme_relation<br>";
        $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_eme_country");
        $message .= ObtenEtiqueta(287).": $rs[0]<br>";
        $message .= "<br>";
        # Representative
        $message .= ObtenEtiqueta(876)."<br>";
        $rowr = RecuperaValor("SELECT CONCAT(ds_nombres,' ', ds_apaterno) FROM c_usuario WHERE fl_usuario=$cl_recruiter");
        $message .= ObtenEtiqueta(877).": $rowr[0]<br>";
        $message .= ObtenEtiqueta(289)." ";
        switch($fg_ori_via) {
            case 'A': $message .= ObtenEtiqueta(290)."<br>"; break;
            case 'B': $message .= ObtenEtiqueta(291)."<br>"; break;
            case 'C': $message .= ObtenEtiqueta(292)."<br>"; break;
            case 'D': $message .= ObtenEtiqueta(293)."<br>"; break;
            case '0': $message .= ObtenEtiqueta(294)." - $ds_ori_other<br>"; break;
        }
        $message .= "<br>";
        $message .= ObtenEtiqueta(295)." ";
        switch($fg_ori_ref) {
            case '0': $message .= ObtenEtiqueta(17)."<br>"; break;
            case 'S': $message .= ObtenEtiqueta(296)." - $ds_ori_ref_name<br>"; break;
            case 'T': $message .= ObtenEtiqueta(297)." - $ds_ori_ref_name<br>"; break;
            case 'G': $message .= ObtenEtiqueta(298)." - $ds_ori_ref_name<br>"; break;
            case 'A': $message .= ObtenEtiqueta(811)." - $ds_ori_ref_name<br>"; break;
        }
        $message .= "<br><br>";
        $message = utf8_encode(str_ascii($message));
        $email = EnviaMailHTML($smtp, $smtp, $app_frm_email, $subject, $message);



    }
    else{

        # Envia correo de confirmacion al aplicante
        $subject = ObtenEtiqueta(335);
        $message  = "Dear $ds_fname $ds_lname,<br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(331)."<br>";
        $message .= ObtenEtiqueta(332)."<br>";
        $message .= ObtenEtiqueta(333)."<br><br>";
        $message .= ObtenEtiqueta(337)."<br>";
        $message .= ObtenEtiqueta(338)."<br>";
        $message = utf8_encode(str_ascii($message));
        if($fg_paso==19)
            EnviaMailHTML($smtp, $smtp, $ds_email, $subject, $message);


        if($fg_paso==10){
            $step=" Step 2/5";



        }else{
            $step="";
        }
        # Envia correo de confirmacion al Administrador
        $subject = ObtenEtiqueta(336).$step;
        $message  = "Application form submitted $step $fe_ultmod<br>";
        if($fg_paypal == '1')
            $message .= ObtenEtiqueta(343).".<br>";
        else
            $message .= "Payment not submitted.<br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(55)."<br>";
        $message .= ObtenEtiqueta(59).": $nb_programa<br>";
        $message .= ObtenEtiqueta(60).": $nb_periodo<br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(61)."<br>";
        $message .= ObtenEtiqueta(117).": $ds_fname<br>";
        $message .= ObtenEtiqueta(119).": $ds_mname<br>";
        $message .= ObtenEtiqueta(118).": $ds_lname<br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(280).": $ds_number<br>";
        $message .= ObtenEtiqueta(281).": $ds_alt_number<br>";
        $message .= ObtenEtiqueta(121).": $ds_email<br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(114).": ";
        if($fg_gender == 'M')
            $message .= ObtenEtiqueta(115)."<br>";
        else
            $message .= ObtenEtiqueta(116)."<br>";
        $message .= ObtenEtiqueta(120).": $fe_birth<br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(62)."<br>";
        $message .= ObtenEtiqueta(282).": $ds_add_number<br>";
        $message .= ObtenEtiqueta(283).": $ds_add_street<br>";
        $message .= ObtenEtiqueta(284).": $ds_add_city<br>";
        $message .= ObtenEtiqueta(285).": $ds_add_state<br>";
        $message .= ObtenEtiqueta(286).": $ds_add_zip<br>";
        $message .= ObtenEtiqueta(287).": $ds_add_country<br>";
        $message .= "<br>";
        $message .= ObtenEtiqueta(63)."<br>";
        $message .= ObtenEtiqueta(117).": $ds_eme_fname<br>";
        $message .= ObtenEtiqueta(118).": $ds_eme_lname<br>";
        $message .= ObtenEtiqueta(280).": $ds_eme_number<br>";
        $message .= ObtenEtiqueta(288).": $ds_eme_relation<br>";
        #Recuperamos la ciudad.
        $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_eme_country ");
        $message .= ObtenEtiqueta(287).":$rs[0]<br>";
        $message .= "<br>";
        if($fg_paso==10){
            #Recuperamos la preferencia de horario.
            $QueryClass="SELECT CONCAT(
                                CASE WHEN cl_dia='1'THEN '".ObtenEtiqueta(2390)."'
                                WHEN cl_dia='2'THEN '".ObtenEtiqueta(2391)."'
                                WHEN cl_dia='3'THEN '".ObtenEtiqueta(2392)."'
                                WHEN cl_dia='4'THEN '".ObtenEtiqueta(2393)."'
                                WHEN cl_dia='5'THEN '".ObtenEtiqueta(2394)."'
                                WHEN cl_dia='6'THEN '".ObtenEtiqueta(2395)."'
                                ELSE '".ObtenEtiqueta(2396)."'
                                END,' ',no_hora,' ',ds_tiempo,'') FROM k_class_time_programa WHERE fl_class_time=$fl_class_time ";
            $rowclass=RecuperaValor($QueryClass);
            $no_time=$rowclass[0];
            $message .= ObtenEtiqueta(2389).": $no_time<br>";
        }

        if($fg_paso<>10){
            $message .= ObtenEtiqueta(289)." ";
            switch($fg_ori_via) {
                case 'A': $message .= ObtenEtiqueta(290)."<br>"; break;
                case 'B': $message .= ObtenEtiqueta(291)."<br>"; break;
                case 'C': $message .= ObtenEtiqueta(292)."<br>"; break;
                case 'D': $message .= ObtenEtiqueta(293)."<br>"; break;
                case '0': $message .= ObtenEtiqueta(294)." - $ds_ori_other<br>"; break;
            }
            $message .= ObtenEtiqueta(295)." ";
            switch($fg_ori_ref) {
                case '0': $message .= ObtenEtiqueta(17)."<br>"; break;
                case 'S': $message .= ObtenEtiqueta(296)." - $ds_ori_ref_name<br>"; break;
                case 'T': $message .= ObtenEtiqueta(297)." - $ds_ori_ref_name<br>"; break;
                case 'G': $message .= ObtenEtiqueta(298)." - $ds_ori_ref_name<br>"; break;
                case 'A': $message .= ObtenEtiqueta(811)." - $ds_ori_ref_name<br>"; break;
            }
            $message .= "<br><br>";
            $message .= ObtenEtiqueta(56)."<br>";
            $message .= ObtenEtiqueta(301)."<br>$ds_resp2_1<br>";
            $message .= ObtenEtiqueta(302)."<br>$ds_resp2_2<br>";
            $message .= ObtenEtiqueta(303)."<br>$ds_resp2_3<br>";
            $message .= ObtenEtiqueta(304)."<br>$ds_resp2_4<br>";
            $message .= ObtenEtiqueta(305)."<br>$ds_resp2_5<br>";
            $message .= ObtenEtiqueta(306)."<br>$ds_resp2_6<br>";
            $message .= ObtenEtiqueta(307)."<br>$ds_resp2_7<br>";
            $message .= "<br>";

            $etq_si = ObtenEtiqueta(16);
            $etq_no = ObtenEtiqueta(17);
            $message .= ObtenEtiqueta(78)."<br>";

            $message .= ObtenEtiqueta(79)."<br>";
            $message .= ObtenEtiqueta(82)."<br>";
            switch($fg_resp4_1_1) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(83)."<br>";
            switch($fg_resp4_1_2) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(84)."<br>";
            switch($fg_resp4_1_3) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(85)."<br>";
            switch($fg_resp4_1_4) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(86)."<br>";
            switch($fg_resp4_1_5) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(87)."<br>";
            switch($fg_resp4_1_6) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }

            $message .= ObtenEtiqueta(80)."<br>";
            $message .= ObtenEtiqueta(88)."<br>";
            switch($fg_resp4_2_1) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(89)."<br>";
            switch($fg_resp4_2_2) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(90)."<br>";
            switch($fg_resp4_2_3) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(91)."<br>";
            switch($fg_resp4_2_4) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(92)."<br>";
            switch($fg_resp4_2_5) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(93)."<br>";
            switch($fg_resp4_2_6) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }
            $message .= ObtenEtiqueta(94)."<br>";
            switch($fg_resp4_2_7) {
                case '1': $message .= $etq_si."<br>"; break;
                case '0': $message .= $etq_no."<br>"; break;
            }

            $message .= ObtenEtiqueta(81)."<br>";
            $message .= ObtenEtiqueta(95)."<br>";
            switch($fg_resp4_3_1) {
                case '0': $message .= ObtenEtiqueta(97)."<br>"; break;
                case '1': $message .= ObtenEtiqueta(98)."<br>"; break;
                case '2': $message .= ObtenEtiqueta(99)."<br>"; break;
                case '3': $message .= ObtenEtiqueta(107)."<br>"; break;
            }
            $message .= ObtenEtiqueta(96)."<br>";
            switch($fg_resp4_3_2) {
                case '0': $message .= ObtenEtiqueta(97)."<br>"; break;
                case '1': $message .= ObtenEtiqueta(98)."<br>"; break;
                case '2': $message .= ObtenEtiqueta(99)."<br>"; break;
                case '3': $message .= ObtenEtiqueta(107)."<br>"; break;
            }

            $message .= "<br>";
            $message .= ObtenEtiqueta(57)."<br>";
            $message .= ObtenEtiqueta(308)."<br>$ds_resp3_1<br>";
            $message .= ObtenEtiqueta(309)."<br>";
            $message .= "1: $ds_resp3_2_1<br>";
            $message .= "2: $ds_resp3_2_2<br>";
            $message .= "3: $ds_resp3_2_3<br>";
            $message .= ObtenEtiqueta(310)."<br>$ds_resp3_3<br>";
            $message .= ObtenEtiqueta(311)."<br>$ds_resp3_4<br>";
            $message .= ObtenEtiqueta(312)."<br>$ds_resp3_5<br>";
            $message .= ObtenEtiqueta(313)."<br>";
            switch($ds_resp3_6) {
                case 'A': $message .= ObtenEtiqueta(314)."<br>"; break;
                case 'B': $message .= ObtenEtiqueta(315)."<br>"; break;
                case 'C': $message .= ObtenEtiqueta(316)."<br>"; break;
            }
            $message .= ObtenEtiqueta(317)."<br>";
            switch($ds_resp3_7) {
                case 'A': $message .= ObtenEtiqueta(318)."<br>"; break;
                case 'B': $message .= ObtenEtiqueta(319)."<br>"; break;
                case 'C': $message .= ObtenEtiqueta(320)."<br>"; break;
                case 'D': $message .= ObtenEtiqueta(321)."<br>"; break;
                case 'E': $message .= ObtenEtiqueta(322)."<br>"; break;
            }
            $message .= ObtenEtiqueta(323)."<br>$ds_resp3_8<br>";

        }

        $message .= "<br><br>";
        $message = utf8_encode(str_ascii($message));

        $email = EnviaMailHTML($smtp, $smtp, $app_frm_email, $subject, $message);

        # Actualiza estado del registro de aplicacion
        $Query  = "UPDATE c_sesion SET $Query_extra ";
        $Query .= "WHERE cl_sesion='$clave'";
        EjecutaQuery($Query);
    }
    return $email;
}


?>


