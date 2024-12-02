<?php
  
  # Libreria general de funciones
  require 'lib/general.inc.php';
  
  # Limpia el cookie
  TerminaSesion( );
  
  # Recibe parametros
  $err = RecibeParametroNumerico('err', True);
  
  # Presenta pagina de Login
  echo "
<html>
<head>
<title>".ETQ_TITULO_PAGINA." - Password recovery</title>
<link type='text/css' href='css/estilos.css' media='screen' rel='stylesheet'>
</head>
<body class='css_fondo' OnLoad='document.datos.ds_login.focus()'>
<center>
<br><br><br><br><br><br><br>
<table border='".D_BORDES."' width='500' cellPadding='0' cellSpacing='0' class='css_default'>
  <tr>
    <td align='center' width='40%'><img src=".PATH_IMAGES.'/'.IMG_LOGIN." border=0></td>
    <td width='10%'>&nbsp;</td>
    <td width='50%'>
    <form name='datos' method='post' action='forgot_validate.php'>
      <table border='".D_BORDES."' cellPadding='0' cellSpacing='0' class='css_default'>
        <tr><td align='left' class='css_msg_error'>";
  
  # Presenta mensajes de error
  if(!empty($err)) {
    switch($err) {
      case 1: echo "Invalid username or email address.<br>"; break;
      case 3: echo "The password was not created because there is no email service available.<br>"; break;
      case 4: echo "Inactive user account.<br>"; break;
      case 5: echo "A new password has been generated and sent to your email.<br>"; break;
    }
  }
  else
    echo "<br><br><br>\n";
  
  # Forma para Recuperar contrasenia
  echo "</td></tr>";
  if($err <> 3 AND $err <> 5)
    echo "
        <tr><td align='left'>User</td></tr>
        <tr><td align='left'><input type='text' name='ds_login' length='30' maxlength='16'></td></tr>
        <tr><td align='left'>&nbsp;</td></tr>
        <tr><td align='left'>Email address</td></tr>
        <tr><td align='left'><input type='text' name='ds_email' length='30' maxlength='100'></td></tr>
        <tr><td align='left'>&nbsp;</td></tr>
        <tr><td align='left'><input type='submit' value='&nbsp;&nbsp;Create new password&nbsp;&nbsp;'></td></tr>";
  echo "
        <tr><td align='left'>&nbsp;<br><br></td></tr>
        <tr><td align='left'><a href='".PATH_HOME."'>Back</a></td></tr>
      </table>
      </form>
    </td>
  </tr>
</table>
</center>
</body>
</html>";

?>