<?php
  
  # Libreria general de funciones
  require 'lib/sp_general.inc.php';
  
  # Recibe parametros
  $ds_login = RecibeParametroHTML('ds_login');
  $ds_email = RecibeParametroHTML('ds_email');
  
  $url_system=RecibeParametroHTML('s');
  
   
  # Valida el usuario y correo
  if($url_system=="c")
  $row =RecuperaValor("SELECT fl_usuario, fg_activo FROM c_usuario WHERE ds_login='$ds_login' AND ds_email='$ds_email'  ");
  else
  $row =RecuperaValor("SELECT fl_usuario, fg_activo FROM c_usuario WHERE ds_email='$ds_email' AND fl_perfil_sp IS NOT NULL ");   #En FAME verificamos que  efectivamete tenga fl_perfil_sp 
  
  $fl_usuario = $row[0];
  $fg_activo = $row[1];
  
  
  
  if($fl_usuario == "") {
    # -1: Usuario o contrase&ntilde;a inv&aacute;lida.
      header("Location: ".OLVIDO_INVALIDO."&s=$url_system");
    exit;
  }
  
  # Valida que el usuario este activo
  if($fg_activo <> 1) {
    # -4: El usuario no est&aacute; activo.
      header("Location: ".OLVIDO_INACTIVO."&s=$url_system");
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
  
  if($url_system=="c"){
  
      $subject = 'Password Recovery';
      $message  = "A new password has been generated for user $ds_login.\n";
      $message .= "To enter Administration System, \n";
      $message .= "your new password is $ds_password\n\n";
  
      $headers = "From: ".MAIL_FROM."\r\nReply-To: ".MAIL_FROM."\r\n";
      $mail_sent = mail(str_ascii($ds_email), $subject, $message, $headers);
  
  
  }else{
  
     #Recuperamos el nombre el template de FAME.
     $row=RecuperaValor("SELECT nb_template FROM k_template_doc WHERE fl_template=142 ");
     $subject=str_texto($row['nb_template']);
  
     #Generamos el contenido del email
     
     #Enviamos email de notificacion al teacher.
     $ds_encabezado=genera_template($fl_usuario, 1, 142);
     $ds_cuerpo=genera_template($fl_usuario, 2, 142);
     $ds_pie=genera_template($fl_usuario, 3, 142);
     
     $message=$ds_encabezado.$ds_cuerpo.$ds_pie;
     
     $message= str_replace("#fame_new_password#", $ds_password, $message); 
     
     
     # Inicializa variables de ambiente para envio de correo
     ini_set("SMTP", MAIL_SERVER);
     ini_set("smtp_port", MAIL_PORT);
     ini_set("sendmail_from", MAIL_FROM); 
    
     $message = utf8_decode(str_ascii(str_uso_normal($message)));
     $bcc=ObtenConfiguracion(107);
     $nb_quien_envia_email=ObtenEtiqueta(949);#Vamcouver School nombre de quien envia el mensaje
     $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);
    
     $mail_sent = EnviaMailHTML($nb_quien_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email, $subject, $message, $bcc);
     
  
  }
  
  
 
  
  
  
  # Actualiza el password del usuario
  if($mail_sent) {
    $ds_password_c = sha256($ds_password);
    $Query  = "UPDATE c_usuario SET ds_password='$ds_password_c' ";
    $Query .= "WHERE fl_usuario=$fl_usuario";
    EjecutaQuery($Query);
    header("Location: ".OLVIDO_EXITO."&s=$url_system");
  }
  else
    header("Location: ".OLVIDO_ERR_ENVIO."&s=$url_system");
  
  
  
 /**
  * Generael el template.
  */ 
  
  function genera_template($clave, $opc, $fl_template=0){
  
  
      # Recupera datos del template del documento
      switch($opc){
          case 1: $campo = "ds_encabezado"; break;
          case 2: $campo = "ds_cuerpo"; break;
          case 3: $campo = "ds_pie"; break;
          case 4: $campo = "nb_template"; break;
      }
      
      # Obtenemos la informacion del template header body or footer
      $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
      $row = RecuperaValor($Query1);
      
      $cadena = $row[0];
      # Sustituye caracteres especiales
      $cadena = $row[0];
      $cadena = str_replace("&lt;", "<", $cadena);
      $cadena = str_replace("&gt;", ">", $cadena);
      $cadena = str_replace("&quot;", "\"", $cadena);
      $cadena = str_replace("&#039;", "'", $cadena);
      $cadena = str_replace("&#061;", "=", $cadena);
      
      # Recupera datos usuario
      $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno,ds_login, fg_genero, ds_email, ".ConsultaFechaBD('fe_nacimiento', FMT_FECHA)." fe_nacimiento, fl_usu_invita,ds_alias ";
      $Query .= "FROM c_usuario WHERE fl_usuario=$clave ";
      $row = RecuperaValor($Query);
      $ds_fname = str_texto($row[0]);
      $ds_lname = str_texto($row[1]);
      $ds_mname = str_texto($row[2]);
      $ds_login = str_texto($row[3]);
      $fg_genero = str_texto($row[4]);
      if($fg_gender == 'M')
          $ds_gender = ObtenEtiqueta(115);
      else
          $ds_gender = ObtenEtiqueta(116);
      $ds_email = $row[5];
      $fe_nacimiento = $row[6];
      $fl_usu_invita = $row[7];
      $ds_alias=str_texto($row['ds_alias']);
  
      $cadena = str_replace("#fame_fname#", $ds_fname, $cadena);                        # Student first name 
      $cadena = str_replace("#fame_mname#", $ds_mname, $cadena);                        # Student middle name 
      $cadena = str_replace("#fame_lname#", $ds_lname, $cadena);                        # Student last name
      $cadena = str_replace("#fame_login#", $ds_login, $cadena);                        # Student login
      $cadena = str_replace("#fame_alias#", $ds_alias, $cadena);                        # Student login
      $cadena = str_replace("#fame_gender#", $ds_gender, $cadena);                      # Student gender female
      $cadena = str_replace("#fame_email#", $ds_email, $cadena);                        # Student email address
  
      return ($cadena);
  }
  
  
  
  
  
?>