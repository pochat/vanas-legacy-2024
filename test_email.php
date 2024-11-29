<?php

# Libreria de funciones
require("AD3M2SRC4/lib/general.inc.php");
require_once('AD3M2SRC4/lib/vendor/phpmailer/phpmailer/src/PHPMailer.php');

//Load Composer's autoloader
require 'AD3M2SRC4/lib/vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);


try{
    
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'noreply@vanas.ca';                     //SMTP username
    $mail->Password   = 'Duende1oo';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('noreply@vanas.ca', 'Mailer');
    $mail->addAddress('mikel_angel_jd@hotmail.com', 'Joe User');     //Add a recipient
    //$mail->addAddress('ellen@example.com');               //Name is optional
    //$mail->addReplyTo('terrylonz@gmail.com', 'Information');
    //$mail->addCC('terrylonz@gmail.com');//copy visible
    $mail->addBCC('terrylonz@gmail.com');//copia oculta

    //Attachments
    $mail->addAttachment('mike.php');         //Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body<h1>hola</h1>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    
	echo"fuaa2";
    
}
catch (Exception $e)
{
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"; 
}





























/*


$from=MAIL_FROM;
# Inicializa variables de ambiente para envio de correo adjunto
ini_set("SMTP", MAIL_SERVER);
ini_set("smtp_port", MAIL_PORT);
ini_set("sendmail_from", MAIL_FROM);
$repEmail = $from;

$eol = "\n";
$separator = md5(time());
$ds_emailto="mariopochat@icloud.com";

$headers  = 'MIME-Version: 1.0' .$eol;
// $headers .= 'From: "'.$ds_subject.'" <'.$repEmail.'>'.$eol;
$headers .= 'From: "'.$repEmail.'" <'.$repEmail.'>'.$eol;
$headers .= "Bcc: mike@vanas.ca \r\n";
$headers .= 'Content-Type: multipart/mixed; boundary="'.$separator.'"';

$message = "--".$separator.$eol;
$message .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
$message .="hola testing";
$message .= "--".$separator.$eol;
$message .= $fileatt;
$message .= "".$separator."--".$eol;

# insertamos el envio del email
if (mail($ds_emailto,'Subject', $message, $headers)){
echo"fuaa";
}


 */



?>


