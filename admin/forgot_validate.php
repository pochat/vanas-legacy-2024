<?php
  
  # Libreria general de funciones
  require 'lib/general.inc.php';
  
  # Recibe parametros
  $ds_login = RecibeParametroHTML('ds_login');
  $ds_email = RecibeParametroHTML('ds_email');
  
  # Valida el usuario y correo
  $row = RecuperaValor("SELECT fl_usuario, fg_activo FROM c_usuario WHERE ds_login='$ds_login' AND ds_email='$ds_email'");
  $fl_usuario = $row[0];
  $fg_activo = $row[1];
  if($fl_usuario == "") {
    # -1: Usuario o contrase&ntilde;a inv&aacute;lida.
    header("Location: ".OLVIDO_INVALIDO);
    exit;
  }
  
  # Valida que el usuario este activo
  if($fg_activo <> 1) {
    # -4: El usuario no est&aacute; activo.
    header("Location: ".OLVIDO_INACTIVO);
    exit;
  }
  
  # Genera una nueva contrasenia al usuario
  $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
  $ds_password = "";
  for($i = 0; $i < 10; $i++)
    $ds_password .= substr($str, rand(0,62), 1);
  
  # Prepara variables de ambiente para envio de correo
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);
  
  # Envia el correo
  $subject = 'Password Recovery';
  $message  = "A new password has been generated for user $ds_login.\n";
  $message .= "To enter Administration System, \n";
  $message .= "your new password is $ds_password\n\n";
  $headers = "From: ".MAIL_FROM."\r\nReply-To: ".MAIL_FROM."\r\n";
  $mail_sent = mail(str_ascii($ds_email), $subject, $message, $headers);

  /*# Send notification email to the student that has a grade assigned
  # Email Library
  require('/var/www/html/AWS_SES/PHP/com_email_func.inc.php');

  # Load AWS class
  require('/var/www/html/AWS_SES/aws/aws-autoloader.php');
  use Aws\Common\Aws;

  # Initialize Amazon Web Service
  $aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');

  # Get the client
  $client = $aws->get('Ses');

  # Initialize the sender address
  $from = 'noreply@vanas.ca';

  $mail_sent = SendNoticeMail($client, $from, str_ascii($ds_email), '', $subject, $message);
*/

  
  # Actualiza el password del usuario
  if($mail_sent) {
    $ds_password_c = sha256($ds_password);
    $Query  = "UPDATE c_usuario SET ds_password='$ds_password_c' ";
    $Query .= "WHERE fl_usuario=$fl_usuario";
    EjecutaQuery($Query);
    header("Location: ".OLVIDO_EXITO);
  }
  else
    header("Location: ".OLVIDO_ERR_ENVIO);
  
?>
