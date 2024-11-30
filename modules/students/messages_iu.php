<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario_ori = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_usuario_dest = RecibeParametroNumerico('usr');
  $ds_mensaje = RecibeParametroHTML('message');
  
  # Envia el mensaje del usuario destino
  if(!empty($fl_usuario_dest) AND !empty($ds_mensaje)) {
    $Query  = "INSERT INTO k_mensaje_directo (fl_usuario_ori, fl_usuario_dest, ds_mensaje) ";
    $Query .= "VALUES($fl_usuario_ori, $fl_usuario_dest, '$ds_mensaje')";
    EjecutaQuery($Query);
    
    # Prepara variables para envio
    $ds_nombre_ori = ObtenNombreUsuario($fl_usuario_ori);
    $ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario_ori); 
    $row = RecuperaValor("SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_dest");
    $ds_email = str_ascii($row[0]);
    $subject = "$ds_nombre_ori sent you a message";
    
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
  }
  
  # Redirige al listado
  header("Location: messages_detail.php?usr=".$fl_usuario_dest);
  
?>