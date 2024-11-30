<?php

try {
  require_once('Stripe/lib/Stripe.php');
  Stripe::setApiKey("sk_test_o5HtIVufoNKfoQKipEgAoPbh"); //Replace with your Secret Key

  $charge = Stripe_Charge::create(array(
    "amount" => 1500,
    "currency" => "usd",
    "card" => $_POST['stripeToken'],
    "description" => "Charge for Facebook Login code."
  ));
} catch (Exception $e) {
  //echo 'Caught exception: ',  $e->getMessage(), "\n";
}
