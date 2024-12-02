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
<title>".ETQ_TITULO_PAGINA." - Login</title>
<link type='text/css' href='css/estilos.css' media='screen' rel='stylesheet'>
</head>
<body class='css_fondo' OnLoad='document.datos.ds_login.focus()'>
<center>
<br><br><br><br><br><br><br>
<table border='".D_BORDES."' width='500' cellPadding='0' cellSpacing='0' class='css_default'>
  <tr>
    <td align='center' width='40%'>
      <img src=".PATH_IMAGES.'/'.IMG_LOGIN." border=0>
      <br><br><br>
      <a href='".INICIO_W."'>".ObtenEtiqueta(77)."</a>
    </td>
    <td width='10%'>&nbsp;</td>
    <td width='50%'>
    <form name='datos' method='post' action='login_validate.php'>
      <table border='".D_BORDES."' cellPadding='0' cellSpacing='0' class='css_default'>
        <tr><td align='left' class='css_msg_error'>";
  
  # Presenta mensajes de error
  if(!empty($err)) {
    switch($err) {
      case 1: echo "Invalid username or password.<br>"; break;
      case 2: echo "Session expired.<br>"; break;
      case 3: echo "Session does not exist.<br>"; break;
      case 4: echo "Inactive user account.<br>"; break;
    }
    echo "Please try again.<br><br>";
  }
  else
    echo "<br><br><br>\n";
  
  # Forma para Login
  echo "</td></tr>
        <tr><td align='left'>".ETQ_USUARIO."</td></tr>
        <tr><td align='left'><input type='text' name='ds_login' length='30' maxlength='16'></td></tr>
        <tr><td align='left'>&nbsp;</td></tr>
        <tr><td align='left'>".ObtenEtiqueta(123)."</td></tr>
        <tr><td align='left'><input type='password' name='ds_password' length='30' maxlength='16' autocomplete='off'></td></tr>
        <tr><td align='left'>&nbsp;</td></tr>
        <tr><td align='left'><input type='submit' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Enter&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'></td></tr>
        <tr><td align='left'>&nbsp;<br><br></td></tr>
        <tr><td align='left'><a href='".PAGINA_OLVIDO."'>".ObtenEtiqueta(75)."</a></td></tr>
      </table>
      </form>
    </td>
  </tr>
</table>
</center>
</body>
</html>";

?>