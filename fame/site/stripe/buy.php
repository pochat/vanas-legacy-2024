<?php # buy.php
// Created by Larry Ullman, www.larryullman.com, @LarryUllman
// Posted as part of the series "Processing Payments with Stripe"
// http://www.larryullman.com/series/processing-payments-with-stripe/
// Last updated April 14, 2015
// The class names are based upon Twitter Bootstrap (http://twitter.github.com/bootstrap/)

// This is the NEWER version of the script, that uses version 2.x of the Stripe library, installed via Composer (https://getcomposer.org/).
// If you're using version 1.x of the Stripe library, use buy-old.php instead.
// See https://stripe.com/docs/libraries

// This page is used to make a purchase.

// Every page needs the configuration file:
require('config.inc.php');

// Uses sessions to test for duplicate submissions:
session_start();

$caducidad = 60 * 60 * 24 * 30 + time();
$_SESSION['token'] = $caducidad;

?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Buy This Thing</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
  <!-- jQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <!-- Latest compiled JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body><?php

// Check for a form submission:
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Stores errors:
	$errors = array();  
	// Need a payment token:
	if (isset($_POST['stripeToken'])) {

		$token = $_POST['stripeToken'];
    
		// Check for a duplicate submission, just in case:
		// Uses sessions, you could use a cookie instead.
		if (isset($_SESSION['token']) && ($_SESSION['token'] == $token)) {
			$errors['token'] = 'You have apparently resubmitted the form. Please do not do that.';
		} else { // New submission.
			$_SESSION['token'] = $token;
		}

	} else {
		$errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
	}

	// Set the order amount somehow:
	$amount = 50; // $20, in cents
  $email = "gflores2702@gmail.com";  

	// Validate other form data!
	// If no errors, process the order:
	if (empty($errors)) {
    // Include the Stripe library:
			// Assumes you've installed the Stripe PHP library using Composer!
			// require_once('vendor/autoload.php');
      require_once('/Stripe/Stripe/init.php');

		// create the charge on Stripe's servers - this will charge the user's card
		try {

			

			// set your secret key: remember to change this to your live secret key in production
			// see your keys here https://manage.stripe.com/account
			\Stripe\Stripe::setApiKey(STRIPE_PRIVATE_KEY);

			// Charge the order:
			// $charge = \Stripe\Charge::create(array(
				// "amount" => $amount, // amount in cents, again
				// "currency" => "USD",
				// "source" => $token,
				// "description" => $email
				// )
			// );
      
      /*# Creamos plan
      $plan = \Stripe\Plan::create(array(
        "name" => "Basic Plan",
        "id" => "basic-monthly",
        "interval" => "month",
        "currency" => "usd",
        "amount" => 10,
      ));
      
      # Creamos customer
      $customer = \Stripe\Customer::create(array(
        "email" => "gflores@loomtek.com.mx",
        "description" => "Gabriel Flores",
        "source" => $token,
      ));
      
      \Stripe\Subscription::create(array(
        "customer" => $customer->id,
        "plan" => "basic-monthly",
      ));
      */
      $charge = \Stripe\Charge::create(array(
        "amount" => 500,
        "currency" => "usd",
        "description" => "Example charge",
        "receipt_email" => "gflores@loomtek.com.mx",
        "source" => $token,
      ));
      
      // Parametros que recibimos de stripe
      // Recibimos los datos de stripe para verificar que fue lo que mandamos
			// Check that it was paid:
			if ($charge->paid == true) {

				// Store the order in the database.
				// Send the email.
				// Celebrate!
        echo '<div class="alert alert-error"><h4>Payment System Send!</h4></div>';
			} else { // Charge was not paid!
				echo '<div class="alert alert-error"><h4>Payment System Error!</h4>Your payment could NOT be processed (i.e., you have not been charged) because the payment system rejected the transaction. You can try again or use another card.</div>';
			}

		} catch (\Stripe\Error\Card $e) {
		    // Card was declined.
			$e_json = $e->getJsonBody();
			$err = $e_json['error'];
			$errors['stripe'] = $err['message'];

		} catch (\Stripe\Error\ApiConnection $e) {
		    // Network problem, perhaps try again.
        $e_json = $e->getJsonBody();
        $err = $e_json['error'];
        $errors['stripe'] = $err['message'];
		} catch (\Stripe\Error\InvalidRequest $e) {
		    // You screwed up in your programming. Shouldn't happen!
        $e_json = $e->getJsonBody();
        $err = $e_json['error'];
        $errors['stripe'] = $err['message'];
		} catch (\Stripe\Error\Api $e) {      
		    // Stripe's servers are down!
        $e_json = $e->getJsonBody();
        $err = $e_json['error'];
        $errors['stripe'] = $err['message'];
		} catch (\Stripe\Error\Base $e) {
		    // Something else that's not the customer's fault.
        $e_json = $e->getJsonBody();
        $err = $e_json['error'];
        $errors['stripe'] = $err['message'];
		}

	} // A user form submission error occurred, handled below.

// } // Form submission.

// Set the Stripe key:
// Uses STRIPE_PUBLIC_KEY from the config file.
echo '<script type="text/javascript">Stripe.setPublishableKey("' . STRIPE_PUBLIC_KEY . '");</script>';
?>

	<h1>Buy This Thing</h1>

	<form action="buy.php" method="POST" id="payment-form">

		<?php // Show PHP errors, if they exist:
		if (isset($errors) && !empty($errors) && is_array($errors)) {
			echo '<div class="alert alert-error"><h4>Error!</h4>The following error(s) occurred:<ul>';
			foreach ($errors as $e) {
				echo "<li>$e</li>";
			}
			echo '</ul></div>';
		}?>

		<div id="payment-errors"></div>

		<span class="help-block">You can pay using: Mastercard, Visa, American Express, JCB, Discover, and Diners Club.</span>

		<div class="alert alert-info"><h4>JavaScript Required!</h4>For security purposes, JavaScript is required in order to complete an order.</div>

		<label>Card Number</label>
		<input type="text" size="20" autocomplete="off" class="card-number input-medium">
		<span class="help-block">Enter the number without spaces or hyphens.</span>
		<label>CVC</label>
		<input type="text" size="4" autocomplete="off" class="card-cvc input-mini">
		<label>Expiration (MM/YYYY)</label>
		<input type="text" size="2" class="card-expiry-month input-mini">
		<span> / </span>
		<input type="text" size="4" class="card-expiry-year input-mini">

		<button type="submit" class="btn" id="submitBtn">Submit Payment</button>

	</form>

	<script src="buy.js"></script>

</body>
</html>