<?php


  define("MAIL_SERVER", "mail.vanas.ca");
  define("MAIL_FROM", "26");
  define("MAIL_PORT", "info@vanas.ca"); 

include "../lib/com_func.inc.php";

function EnviaMailHTML_Test($p_from_name, $p_from_mail, $p_to, $p_subject, $p_message, $p_bcc='') {

  # Inicializa variables de ambiente para envio de correo
  //7ini_set("SMTP", MAIL_SERVER);
  //ini_set("smtp_port", MAIL_PORT);
  //ini_set("sendmail_from", MAIL_FROM);

  ini_set("SMTP", "mail.vanas.ca");
  ini_set("smtp_port", "26");
  ini_set("sendmail_from", "info@vanas.ca");

  $to = str_ascii($p_to);
  $subject = str_ascii($p_subject);
  $headers = "From: $p_from_name<$p_from_mail>\r\nReply-To: $p_from_mail\r\n";
  if(!empty($p_bcc))
    $headers .= "Bcc: $p_bcc\r\n";
  $headers = str_ascii($headers);
  $message = ConvierteHTMLenMail($p_message, $headers);
  return mail($to, $subject, $message['multipart'], $message['headers']);
}


?>

<?php



$p_from_name = "Marco";
//$p_from_mail = "mdominguez@loomtek.com.mx"; // Para que lo rebote
$p_from_mail = "noreply@vanas.ca";
//$p_from_mail = "";
//$p_to = "gflores@loomtek.com.mx"; 
$p_to =  "mdominguez@loomtek.com.mx";
$p_subject = "Hola correo php";
$p_message = "Prueba de correo desde correo.php";
$p_bcc = "";

EnviaMailHTML($p_from_name, $p_from_mail, $p_to, $p_subject, $p_message, $p_bcc='');




?>
