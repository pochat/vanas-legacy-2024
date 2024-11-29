<?php
  
  # Libreria de funciones
  require("lib/sp_general.inc.php");
  require("lib/sp_forms.inc.php");
  
  # Recibe parametros
  $err = RecibeParametroNumerico('msg', True);
  
  # Limpia el cookie
  TerminaSesion(False);
  
  # Inicializa variables
  $titulo = "Password Recovery";
  $img_archivo = IMG_DEFAULT;
  
  # Header
  PresentaHeader(0, True);
  
  # Inicia pagina
  echo "
    <img src='".SP_IMAGES."/$img_archivo' width='720' height='150' border='0'>
    <table border='".D_BORDES."' width='720' valign='top' cellspacing='0' cellpadding='0'>
      <tr>
        <td width='20' height='20'>&nbsp;</td>
        <td>&nbsp;</td>
        <td width='20'>&nbsp;</td>
      </tr>
      <tr>
        <td height='30'>&nbsp;</td>
        <td><b>$titulo</b></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3' height='20'>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align='center' valign='top'>";
  
  # Forma para recuperacion de contrasena
  echo "
    <form name='datos' method='post' action='forgot_validate.php'>
      <table border='".D_BORDES."' width='50%' cellPadding='0' cellSpacing='0'>
        <tr><td align='left' class='login_err'>";
  
  # Presenta mensajes de error
  if(!empty($err)) {
    switch($err) {
      case 1: echo "Invalid username or email address.<br>"; break;
      case 3: echo "The password was not created because there is no email service available.<br>"; break;
      case 4: echo "Inactive user account.<br>"; break;
      case 5: echo "A new password has been generated and sent to your email.<br>"; break;
    }
    echo "<br><br>";
  }
  
  # Forma para Recuperar contrasenia
  echo "</td></tr>
        <tr><td align='left'>Your user name</td></tr>
        <tr><td align='left'><input type='text' name='ds_login' size='50' maxlength='20'></td></tr>
        <tr><td align='left'>&nbsp;</td></tr>
        <tr><td align='left'>Please enter your email address</td></tr>
        <tr><td align='left'><input type='text' name='ds_email' size='50' maxlength='200'></td></tr>
        <tr><td align='left'>&nbsp;</td></tr>
        <tr><td align='left'>
          <button type='button' id='buttons' OnClick='javascript:document.datos.submit();'>Send me a new password</button></td></tr>
        <tr><td align='left'>&nbsp;<br><br></td></tr>
      </table>
    </form>";
  
  # Cierra pagina
  echo "
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3' height='20'>&nbsp;</td>
      </tr>
    </table>";
  
  # Footer
  PresentaFooter( );
  
?>