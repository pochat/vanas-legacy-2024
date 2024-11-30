<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Recibe parametros
  $fl_usuario_ori = RecibeParametroNumerico('fl_usuario_ori');
  $fl_usuario_dest = RecibeParametroNumerico('fl_usuario_dest');
  $ds_mensaje = RecibeParametroHTML('ds_mensaje', True);

  # Inseta el mensaje
  $Query  = "INSERT INTO k_mensaje_directo (fl_usuario_ori, fl_usuario_dest, ds_mensaje) ";
  $Query .= "VALUES($fl_usuario_ori, $fl_usuario_dest, '$ds_mensaje')";
  EjecutaQuery($Query);
  
  # Prepara variables para envio
  $ds_nombre_ori = ObtenNombreUsuario($fl_usuario_ori);
  $ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario_ori); 
  $row = RecuperaValor("SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_dest");
  $ds_email = str_ascii($row[0]);
  $subject = "$ds_nombre_ori sent you a message";
  $ds_mensaje = html_entity_decode(htmlspecialchars_decode($ds_mensaje), ENT_QUOTES | ENT_HTML5);
  
  # Mensaje del correo
  $message  = "
    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
      <tr>
        <td width='10'>&nbsp;</td>
        <td width='80' valign='top' align='center'><img src='$ds_ruta_avatar' border='none' /></td>
        <td width='20'>&nbsp;</td>
        <td valign='top' style='font-family: Tahoma; font-size: 12px; font-weight: normal;'>
          <b>$ds_nombre_ori</b>
          <p>$ds_mensaje</p>
        </td>
        <td width='10'>&nbsp;</td>
      </tr>
    </table>
    <br>
    <p style='font-family: Tahoma; font-size: 11px; font-weight: normal;'>Please go to <a href='http://vanas.ca'>Vancouver Animation School Online Campus</a> to reply $ds_nombre_ori.</p>";
  
  # Envia el correo de contacto
  EnviaMailHTML("Vancouver Animation School", MAIL_FROM, $ds_email, $subject, $message);
  
?>
