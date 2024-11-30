<?php
# Libreria de funciones
require("../../common/lib/cam_general.inc.php");

# Include the Stripe library
require_once('../../../fame/lib/Stripe/Stripe/init.php');


#Recibe Parametros.

$fl_alumno=RecibeParametroNumerico('fl_alumno');
$fl_term_pago=RecibeParametroNumerico('fl_term_pago');
$fl_term=RecibeParametroNumerico('fl_term');
$fl_programa=RecibeParametroNumerico('fl_programa');
$mn_total=$_POST['mn_total'];
$mn_total_total=$_POST['mn_total_total'];
$mn_convenie_fee=$_POST['mn_convenie_fee'];
$fl_pais_campus=RecibeParametroNumerico('fl_pais_campus');
$email=RecibeParametroNumerico('stripeEmail');
$token = RecibeParametroHTML('stripeToken');
$fg_late_fee=RecibeParametroBinario('fg_late_fee');
$tax_mn_cost= $_POST['tax_mn_cost'];



#RECUPERAMOS DATOS DEL ALUMNO
$qUERY="SELECT CONCAT(ds_nombres,' ',ds_apaterno),email FROM c_usuario WHERE fl_usuario=$fl_alumno ";
$row=RecuperaValor($qUERY);
$ds_nombres=$row[0];
$email= $row[1];

$qUERY="SELECT nb_programa FROM c_programa WHERE fl_programa=$fl_programa ";
$row=RecuperaValor($qUERY);
$nb_programa=$row[0];
$ds_descripcion="$ds_nombres, $nb_programa ";


switch ($fl_pais_campus) {

    case '38':

        $currency = "CAD";
        $secret_key = ObtenConfiguracion(112);
        break;
    case '226':
        $currency = "USD";
        $secret_key = ObtenConfiguracion(167);
        break;
    case '199':
        $currency = "EUR";
        $secret_key = ObtenConfiguracion(112);
        break;
    case '73':
        $currency = "EUR";
        $secret_key = ObtenConfiguracion(112);
        break;
    case '80':
        $currency = "EUR";
        $secret_key = ObtenConfiguracion(112);
        break;
    case '105':
        $currency = "EUR";
        $secret_key = ObtenConfiguracion(112);
        break;
    case '225':
        $currency = "GBP";
        $secret_key = ObtenConfiguracion(112);
        break;
    case '153':
        $currency = "EUR";
        $secret_key = ObtenConfiguracion(112);
        break;
    default:
        $currency = "CAD";
        $secret_key = ObtenConfiguracion(112);
        break;

}



// create the charge on Stripe's servers - this will charge the user's card
try {
    # set your secret key: remember to change this to your live secret key in production
    # see your keys here https://manage.stripe.com/account
    \Stripe\Stripe::setApiKey($secret_key);



    # Crea cliente
    $customer = \Stripe\Customer::create(array(
      "email" => $email,
      "description" => $ds_nombres,
      "source" => $token,
    ));



    # Charge the order:
    $charge = \Stripe\Charge::create(array(
      "amount" => $mn_total_total*100,
      "customer" => $customer->id,
      "currency" => $currency,
      "description" => $ds_descripcion
      )
    );
    $ds_transaccion=$charge->id;



    $mn_late_fee=0;
    if($fg_late_fee){
        $mn_late_fee=ObtenConfiguracion(66);
    }

    $Query  = "INSERT INTO k_alumno_pago (fl_alumno, fl_term_pago, cl_metodo_pago, fe_pago,mn_convenience_fee, mn_pagado, mn_late_fee, ds_transaccion,tax_mn_cost)  ";
    $Query .= "VALUES ($fl_alumno,$fl_term_pago, '1', CURRENT_TIMESTAMP,$mn_convenie_fee,'$mn_total', $mn_late_fee, '$ds_transaccion',$tax_mn_cost)";
    $fl_alumno_pago = EjecutaInsert($Query);


?>    

<a class='btn btn-success btn-sm hidden' href='<?php echo ObtenConfiguracion(121)."/modules/students_new/";?>index.php#ajax/payment_history.php' id='redirect_payment_history'><i class='fa fa-upload'></i> Processing...</a>                                                 
<script>
    document.getElementById('redirect_payment_history').click();
</script>


<?php


}
catch (\Stripe\Error\ApiConnection $e) {
    // Network problem, perhaps try again.
    $e_json = $e->getJsonBody();
    $err = $e_json['error'];
    //$result['error'] = $err['message'];



?>


    <a class='btn btn-success btn-sm hidden' href='<?php echo ObtenConfiguracion(121)."/modules/students_new/";?>index.php#ajax/payment_history.php?msg=<?php echo $err['message'];?>' id='A3'><i class='fa fa-upload'></i> Processing...</a>                                                 
        <script>
            document.getElementById('redirect_payment_history').click();
        </script>


<?php

}
catch (\Stripe\Error\InvalidRequest $e) {
    // You screwed up in your programming. Shouldn't happen!
    $e_json = $e->getJsonBody();
    $err = $e_json['error'];
    //$result['error'] = $err['message'];


    ?>


        <a class='btn btn-success btn-sm hidden' href='<?php echo ObtenConfiguracion(121)."/modules/students_new/";?>index.php#ajax/payment_history.php?msg=<?php echo $err['message'];?>' id='A2'><i class='fa fa-upload'></i> Processing...</a>                                                 
        <script>
            document.getElementById('redirect_payment_history').click();
        </script>


<?php
}
catch (\Stripe\Error\Api $e) {
    // Stripe's servers are down!
    $e_json = $e->getJsonBody();
    $err = $e_json['error'];
    //$result['error'] = $err['message'];

    ?>

        <a class='btn btn-success btn-sm hidden' href='<?php echo ObtenConfiguracion(121)."/modules/students_new/";?>index.php#ajax/payment_history.php?msg=<?php echo $err['message'];?>' id='A1'><i class='fa fa-upload'></i> Processing...</a>                                                 
        <script>
            document.getElementById('redirect_payment_history').click();
        </script>


<?php
}
catch (\Stripe\Error\Base $e) {
    // Something else that's not the customer's fault.
    $e_json = $e->getJsonBody();
    $err = $e_json['error'];
    //$result['error'] = $err['message'];

    ?>    
     <a class='btn btn-success btn-sm hidden' href='<?php echo ObtenConfiguracion(121)."/modules/students_new/";?>index.php#ajax/payment_history.php?msg=<?php echo $err['message'];?>' id='redirect_payment_history'><i class='fa fa-upload'></i> Processing...</a>                                                 
      <script>
            document.getElementById('redirect_payment_history').click();
      </script>

<?php
}



?>

