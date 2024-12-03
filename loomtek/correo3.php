<?php

function SendNoticeMail_x($client, $from, $to, $cc="", $subject, $message, $copy_cc=True) {

$CcAddresses = array();

    if($copy_cc)
      $mario = 'gflores@loomtek.com.mx';
    else
      $mario = '';

		if(!empty($cc)){
			$CcAddresses = array($mario, $cc);
		} else {
                     if (!empty($mario))
			$CcAddresses = array($mario);
                     
		}

		$result = $client->sendEmail(array(
	    'Source' => $from,
	    'Destination' => array(
	        'ToAddresses' => array($to),
	        //'CcAddresses' => $CcAddresses//,
	        'BccAddresses' => $CcAddresses
	    ),
	    'Message' => array(
	        'Subject' => array(
	            'Data' => $subject,
	            'Charset' => 'UTF-8',
	        ),
	        'Body' => array(
	            'Text' => array(
	                'Data' => '',
	                'Charset' => 'UTF-8',
	            ),
	            'Html' => array(
	                'Data' => $message,
	                'Charset' => 'UTF-8',
	            ),
	        ),
	    ),
	    //'ReplyToAddresses' => array('string', ... ),
	    //'ReturnPath' => 'string',
		));



var_dump($result);


}


?>

<?php

 error_reporting(E_ALL);
     ini_set('display_errors', 1);

  # Send notification email to the student that has a grade assigned
  # Email Library
  require('/var/www/html/AWS_SES/PHP/com_email_func.inc.php');

  # Load AWS class
  require('/var/www/html/AWS_SES/aws/aws-autoloader.php');
  use Aws\Common\Aws;

  # Initialize Amazon Web Service
  $aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');

  
  # Get the client
  $client = $aws->get('Ses');




$p_from_name = "Marco";
//$p_from_mail = "mdominguez@loomtek.com.mx"; // Para que lo rebote
$p_from_mail = "noreply@vanas.ca";
//$p_from_mail = "";
//$p_to = "gflores@loomtek.com.mx"; 
$p_to =  "mdominguez@loomtek.com.mx";
$pA_subject = "Hola Correo 3 Prueba 1";
$p_message = "Prueba de correo funciones Gabriel";
$p_bcc = "";

//EnviaMailHTML($p_from_name, $p_from_mail, $p_to, $p_subject, $p_message, $p_bcc='');

SendNoticeMail_x($client, $p_from_mail, $p_to, '', $p_subject, $p_message, False);




?>
