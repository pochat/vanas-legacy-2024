<?php

require_once(PATH_SELF_LIB.'/stripe/lib/Stripe.php');

$stripe = array(
  "secret_key"      => "sk_test_1v12TTLHyOAjEK3LvIb9HGHX",
  "publishable_key" => "pk_test_kJKdOaKm4B2HBpsz3t7kPQHT"
);

Stripe::setApiKey($stripe['secret_key']);
?>