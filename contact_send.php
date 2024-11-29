<?php
  
  # Libreria de funciones
	require("lib/sp_general.inc.php");
  
	# Recibe parametros
  $fg_error = 0;
	$area_contacto = RecibeParametroNumerico('area_contacto');
  $nombre = RecibeParametroHTML('nombre');
  $telefono = RecibeParametroHTML('telefono');
  $email = RecibeParametroHTML('email');
  $comentarios = RecibeParametroHTML('comentarios');
  
  # Recupera la direccion a donde debe enviarse el correo
  if(!empty($area_contacto)) {
    $row = RecuperaValor("SELECT ds_area, ds_email, fg_anexo FROM c_contacto WHERE fl_contacto=$area_contacto");
    $ds_area = $row[0];
    $ds_email = str_ascii($row[1]);
    $fg_anexo = $row[2];
  }
  
  # Valida campos obligatorios
  if(empty($area_contacto) OR empty($ds_email))
    $area_contacto_err = 201; // Por favor seleccione un area de contacto
  if(empty($nombre))
    $nombre_err = 202; // Por favor escriba su nombre
  if(empty($telefono))
    $telefono_err = 203; // Por favor escriba su telefono
  if(empty($email))
    $email_err = 204; // Por favor escriba su correo electronico
  if(empty($comentarios))
    $comentarios_err = 206; // Por favor escriba su comentario
  
  #Verifica que el formato del email sea valido
  if(!empty($email) AND !ValidaEmail($email))
    $email_err = 205; // Por favor escriba un correo electronico valido
  
  # Verifica la extension del archivo anexo
  $fg_enviar_anexo = False;
  if($fg_anexo == 1 AND !empty($_FILES['archivo_'.$area_contacto]['tmp_name'])) {
    $ruta = $_SERVER[DOCUMENT_ROOT].PATH_TMP;
    $nb_archivo = $_FILES['archivo_'.$area_contacto]['name'];
    $ext = strtoupper(ObtenExtensionArchivo($nb_archivo));
    $ext_validas = strtoupper(ObtenConfiguracion(19));
    if(strpos($nb_archivo, ".") === False OR strpos($ext_validas, $ext) === False) {
      $comentarios_err = 207; // Por favor seleccione un archivo de tipo...
    }
    switch($ext) {
      case "DOCX": case "DOC": $subtipo_mime = "MSWORD"; break;
      case "ZIP": case "PDF": $subtipo_mime = $ext; break;
      default: $subtipo_mime = "octet-stream";
    }
    $fg_enviar_anexo = True;
  }
  
  # Regresa a la forma con error
  $fg_error = $area_contacto_err || $nombre_err || $telefono_err || $email_err || $comentarios_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".FRM_CONTACTO."'>
    <input type='hidden' name='fg_error' value='$fg_error'>
    <input type='hidden' name='area_contacto' value='$area_contacto'>
    <input type='hidden' name='area_contacto_err' value='$area_contacto_err'>
    <input type='hidden' name='nombre' value='$nombre'>
    <input type='hidden' name='nombre_err' value='$nombre_err'>
    <input type='hidden' name='telefono' value='$telefono'>
    <input type='hidden' name='telefono_err' value='$telefono_err'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='email_err' value='$email_err'>
    <input type='hidden' name='comentarios' value='$comentarios'>
    <input type='hidden' name='comentarios_err' value='$comentarios_err'>
    </form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Inicializa variables de ambiente para envio de correo
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);
  
  # Prepara variables para envio
  $to = $ds_email;
  $subject = "Contact Form, sent by $nombre";
  $boundary = '-----='.md5(uniqid(rand( )));
  $headers  = "From: ".$nombre."<".$email.">\r\nReply-To: ".$email."\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  if($fg_enviar_anexo)
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
  
  # Mensaje del correo
  if($fg_enviar_anexo) {
    $message  = "--$boundary\r\n";
    $message .= "Content-Type: text/plain; charset=ISO-8859-1\r\n\r\n";
  }
  $message .= "Contact area: $ds_area\r\n";
  $message .= "Name: $nombre\r\n";
  $message .= "Contact number: $telefono\r\n";
  $message .= "Email address: $email\r\n";
  if($fg_enviar_anexo)
    $message .= "Attachments: $nb_archivo\r\n";
  else
    $message .= "Attachments: No files attached.\r\n";
  $message .= "\r\nComments:\r\n\r\n".str_ascii($comentarios)."\r\n\r\n";
  
  # Archivo anexo
  if($fg_enviar_anexo) {
    move_uploaded_file($_FILES['archivo_'.$area_contacto]['tmp_name'], $ruta."/".$nb_archivo);
    $attachment = chunk_split(base64_encode(file_get_contents($ruta."/".$nb_archivo)));
    unlink($ruta."/".$nb_archivo);
    $message .= "--$boundary\r\n";
    $message .= "Content-Type: application/$subtipo_mime; name=\"$nb_archivo\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"$theFile\"\r\n\r\n";
    $message .= "$attachment\r\n";
  }
  
  # Envia el correo de contacto
  $mail_sent = mail($to, $subject, $message, $headers);
  
	# Redirige al listado
  echo "<html><body><form name='datos' method='post' action='".FRM_CONTACTO."'>
    <input type='hidden' name='fg_error' value='32000'>
    </form>
<script>
  document.datos.submit();
</script></body></html>";
  
?>