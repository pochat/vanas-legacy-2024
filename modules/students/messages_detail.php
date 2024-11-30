<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $usr_interaccion = RecibeParametroNumerico('usr', True);
  
  # Inicializa variables
  $ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario);
  $ds_ruta_avatar_i = ObtenAvatarUsuario($usr_interaccion);
  $ds_nombre = ObtenNombreUsuario($fl_usuario);
  $ds_nombre_i = ObtenNombreUsuario($usr_interaccion);
  
  # Actualiza estado de la notificacion para el usuario
  EjecutaQuery("UPDATE k_mensaje_directo SET fg_leido='1' WHERE fl_usuario_ori=$usr_interaccion AND fl_usuario_dest=$fl_usuario");
  
  # Inicia pagina
  $titulo = $ds_nombre_i . " messages";
  PresentaHeader($titulo);
  
  # Inicia lista
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' valign='top' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td colspan='3' height='20'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td width='10'></td>
                      <td valign='top'>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                        <form name='comments' method='post' action='messages_iu.php'>
                          <input type='hidden' name='usr' value='$usr_interaccion'>
                          <tr>
                            <td width='15' height='15' class='new_comment_corner_TL'>&nbsp;</td>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td width='55' class='new_comment_blank_cells'>&nbsp;</td>
                            <td width='15' class='new_comment_corner_TR'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td class='new_comment_title'>Send a message to $ds_nombre_i:</td>
                            <td rowspan='2' class='new_comment_blank_cells'>
                              <input type='submit' name='' value='Send'>
                            </td>
                            <td rowspan='2' class='new_comment_blank_cells'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td height='50' class='new_comment_blank_cells'>&nbsp;</td>
                            <td class='new_comment_content'>
                                <textarea name='message' rows=2 cols=73></textarea>
                            </td>
                          </tr>
                          <tr>
                            <td height='15' class='new_comment_corner_BL'>&nbsp;</td>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td class='new_comment_blank_cells'>&nbsp;</td>
                            <td class='new_comment_corner_BR'>&nbsp;</td>
                          </tr>
                        </form>
                        </table>
                      </td>
                      <td width='10'></td>
                    </tr>
                    <tr>
                      <td colspan='3' valign='top' height='80'>&nbsp;&nbsp;&nbsp;
                        ";
  
  # Recupera el mensajes con el usuario
  $diferencia = RecuperaDiferenciaGMT( );
  $Query  = "SELECT fl_mensaje_directo, fl_usuario_ori, fl_usuario_dest, ds_mensaje, fg_leido, ";
  $Query .= "DATE_FORMAT((DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT((DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR)), '%e, %Y at %l:%i %p') 'fe_dia_anio' ";
  $Query .= "FROM k_mensaje_directo ";
  $Query .= "WHERE (fl_usuario_ori=$fl_usuario AND fl_usuario_dest=$usr_interaccion) ";
  $Query .= "OR (fl_usuario_ori=$usr_interaccion AND fl_usuario_dest=$fl_usuario) ";
  $Query .= "ORDER BY fe_mensaje DESC";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    $fl_mensaje_directo = $row[0];
    $fl_usuario_ori = $row[1];
    $fl_usuario_dest = $row[2];
    $ds_mensaje = str_uso_normal($row[3]);
    $fg_leido = $row[4];
    $fe_mensaje = ObtenNombreMes($row[5])." ".$row[6];
    if($fg_leido == '0' AND $fl_usuario_ori <> $fl_usuario)
      $ds_notificar = "(Unread message)";
    else
      $ds_notificar = "";
    if($fl_usuario_ori <> $fl_usuario) {
      $img_envio = ObtenNombreImagen(218);
      $avatar = $ds_ruta_avatar_i;
      $nombre = $ds_nombre_i;
    }
    else {
      $img_envio = ObtenNombreImagen(219);
      $avatar = $ds_ruta_avatar;
      $nombre = $ds_nombre;
    }
    echo "
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td colspan='3' class='division_line'>&nbsp;</td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td width='80' valign='top' align='center'><img src='$avatar' border='none'/></td>
                            <td width='20'>&nbsp;</td>
                            <td valign='top'>
                              <table border='".D_BORDES."' width='100%' cellspacing='0' cellpadding='0'>
                                <tr>
                                  <td class='comment_text'><b>$nombre</b> <span class='text_unread'>$ds_notificar</span></td>
                                  <td width='200' align='right'>$fe_mensaje</td>
                                </tr>
                              </table>
                              <p class='comment_text'>$ds_mensaje</p>
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                          <tr><td colspan='5' height='10'>&nbsp;</td></tr>
                        </table>";
  }
  
  # Termina lista
  echo "
                      </td>
                    </tr>
                    <tr>
                      <td colspan='3' height='20'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='3' align='center'><a href='messages.php'>Back to Messages</a></td>
                    </tr>
                    <tr>
                      <td colspan='3' height='20'>&nbsp;</td>
                    </tr>";
  
  PresentaFooter( );
  
?>