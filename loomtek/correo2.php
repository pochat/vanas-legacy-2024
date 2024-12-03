<?php

function SendNoticeMail_x($client, $from, $to, $cc="", $subject, $message) {
	if(!empty($cc)){
			$CcAddresses = array('marco.bond@gmail.com', $cc);
	} else {
			$CcAddresses = array('marco.bond@gmail.com');
	}

	$result = $client->sendEmail(array(
'Source' => $from,
'Destination' => array(
	'ToAddresses' => array($to),
	'CcAddresses' => $CcAddresses//,
	//'BccAddresses' => array('string', ... ),
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
}


?>

<?php

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
$p_to = "mdominguez@loomtek.com.mx"; 
$p_subject = "Hola de parte de postfix";
$p_message = "Prueba de correo postfix";
$p_bcc = "";

//EnviaMailHTML($p_from_name, $p_from_mail, $p_to, $p_subject, $p_message, $p_bcc='');

SendNoticeMail_x($client, $p_from_mail, $p_to, '', $p_subject, $p_message);




?>
