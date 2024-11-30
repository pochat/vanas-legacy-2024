<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_tema = RecibeParametroNumerico('theme', True);
  $row = RecuperaValor("SELECT nb_tema FROM c_f_tema WHERE fl_tema=$fl_tema");
  $titulo = str_uso_normal($row[0]);
  
  # Actualiza contador de visualizaciones del tema
  EjecutaQuery("UPDATE k_f_usu_tema SET no_posts=0 WHERE fl_usuario=$fl_usuario AND fl_tema=$fl_tema");
  
  # Presenta contenido de la pagina
  PresentaHeader($titulo);
  
  # Genera instancias de TinyMCE para nuevo post
  GeneraTinyMCE('TM_post', '535', '100');
  
  # Forma dinamica
  echo "
  <script type='text/javascript' src='".PATH_COM_JS."/frmForum.js.php'></script>
  <input type='hidden' name='fl_usuario' id='fl_usuario' value='$fl_usuario'>
  <input type='hidden' name='fl_tema' id='fl_tema' value='$fl_tema'>
  <input type='hidden' name='archivo' id='archivo'>
  <div id='dialog'></div>
  <div id='dlg_message'>
    Message to:<b><div id='msg_to'></div></b><br>
    <textarea name='ds_mensaje' id='ds_mensaje' cols=65 rows=4></textarea>
    <input type='hidden' name='fl_usuario_ori' id='fl_usuario_ori' value='$fl_usuario'>
    <input type='hidden' name='fl_usuario_dest' id='fl_usuario_dest'>
  </div>";
  
  # Presenta cuerpo de la pagina
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td colspan='3' valign='top' height='80' class='division_line'>
                        <br>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>";
  
  # Recupera notificaciones pendientes de comentarios
  $Query  = "SELECT fl_comentario, fl_post, fl_usuario, ds_comentario, DATE_FORMAT(fe_comentario, '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT(fe_comentario, '%e, %Y at %l:%i %p') 'fe_dia_anio' ";
  $Query .= "FROM k_f_comentario a ";
  $Query .= "WHERE EXISTS(SELECT 1 FROM k_f_post b WHERE b.fl_post=a.fl_post AND b.fl_tema=$fl_tema AND b.fl_usuario=$fl_usuario) ";
  $Query .= "AND fg_leido='0' ";
  $Query .= "ORDER BY fl_post DESC";
  $rs = EjecutaQuery($Query);
  $tot_notificaciones = CuentaRegistros($rs);
  if($tot_notificaciones > 0) {
    echo "
                          <tr id='tr_notificaciones'>
                            <td width='10'>&nbsp;</td>
                            <td valign='top'>
                              <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%' class='forum_not'>
                                <tr>
                                  <td width='90'>&nbsp;</td>
                                  <td align='left'>";
    while($row = RecuperaRegistro($rs)) {
      $fl_comentario = $row[0];
      $fl_post = $row[1];
      $ds_nombre = ObtenNombreUsuario($row[2]);
      $ds_comentario = str_uso_normal($row[3]);
      $fe_comentario = ObtenNombreMes($row[4])." ".$row[5];
      echo "
                                    <a href=\"javascript:Posiciona('$fl_post');\">$ds_nombre commented your post on $fe_comentario</a><br>";
    }
    echo "
                                  </td>
                                  <td width='10'>&nbsp;</td>
                                  <td width='30' align='center' valign='top'><a href='javascript:CierraNotificaciones( );'><img src='".SP_IMAGES."/collapse.png' border='none' title='Close' /></a></td>
                                </tr>
                              </table>
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                          <tr id='tr_notificaciones_esp'><td colspan='3'>&nbsp;</td></tr>";
  }
  
  # Forma para post nuevo
  echo "
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td valign='top'>
                              <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                                <tr>
                                  <td width='90'>&nbsp;</td>
                                  <td valign='top'>
                                    <div class='forum_add_post'>
                                      <img src='".SP_IMAGES."/new_post.png' border='none' />&nbsp;
                                      <a href='javascript:NuevoPost();'>New post</a>
                                    </div>
                                    <div id='div_forma_post' style='display: none;'>
                                      <textarea class='TM_post' name='ds_post' id='ds_post'></textarea>
                                      <div class='comment_text' style='float: left; margin-top: 5px;' title='Upload file'>
                                        <div id='fu_archivo'></div>
                                      </div>
                                      <div style='float: right; widh: 100%; text-align: right; margin: 10px 3px 0 0;'>
                                        <button onclick='javascript:InsertaPost();'>Publish</button>
                                        &nbsp;
                                        <button onclick='javascript:NuevoPost();'>Cancel</button>
                                      </div>
                                    </div>
                                  </td>
                                  <td width='145'>&nbsp;</td>
                                </tr>
                              </table>
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                          <tr><td colspan='3'>&nbsp;</td></tr>";
  
  # Cuerpo de posts
  echo "
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td valign='top'>
                              <div id='div_forum'></div>
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                        </table>
                        <br>
                      </td>
                    </tr>";
  
  # Carga area de posts
  echo "
  <script type='text/javascript'>
    MuestraPosts( );
  </script>";
  
  PresentaFooter( );
  
?>