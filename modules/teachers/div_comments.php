<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  require("../common/lib/cam_forum.inc.php");
  
  # Presenta el detalle de un comentario de un post
  function PresentaComentarioPost($p_rs, $p_usuario, $p_post, $p_linea) {
    
    $row = RecuperaRegistro($p_rs);
    $fl_comentario = $row[0];
    $fl_usuario_com = $row[1];
    $ds_nombre = ObtenNombreUsuario($fl_usuario_com);
    $ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario_com);
    $ds_comentario = str_uso_normal($row[2]);
    $fe_comentario = ObtenNombreMes($row[3])." ".$row[4];
    $nb_archivo = str_uso_normal($row[5]);
    echo "
                    <tr>
                      <td width='45' valign='top'><img src='$ds_ruta_avatar' border='none' width='45' /></td>
                      <td width='10'></td>
                      <td valign='top' class='comment_text'>
                        <table border='".D_BORDES."' width='100%' cellspacing='0' cellpadding='0'>
                          <tr>
                            <td>
                              <div class='name_tooltip' id='$fl_usuario_com' style='float: left; margin-right: 10px; margin-top: 1px;' width='50%'>
                                <b>$ds_nombre</b>
                              </div>";
    if($fl_usuario_com <> $p_usuario)
      echo "
                              <a href='javascript:SendMessageDialog($fl_usuario_com);'><img src='".SP_IMAGES."/".ObtenNombreImagen(217)."' width='16' height='16' border='none' title='Send a message to $ds_nombre'></a>";
    echo "
                            </td>
                            <td width='50%' align='right'>$fe_comentario";
    if($fl_usuario_com == $p_usuario)
      echo "&nbsp;&nbsp;<a href='javascript:BorraComentario($p_post, $fl_comentario);'><img src='".PATH_COM_IMAGES."/delete.png' width='15' height='15' border='0' title='Delete comment'></a>";
    echo "</td>
                          </tr>
                        </table>";
    
    # Revisa si el post tiene un archivo anexo
    if(!empty($nb_archivo)) {
      echo "<p>";
      $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
      switch($ext) {
        case "ogg":
          PresentaVideoHTML5(SP_HOME."/uploads/", $nb_archivo, 480, 270, '');
          break;
        case "jpg":
        case "jpeg":
          echo "<img src='".SP_HOME."/uploads/$nb_archivo' border='none' />";
          break;
      }
      echo "</p>";
    }
    echo "
                        $ds_comentario
                      </td>
                    </tr>
                    <tr><td colspan='3' height='10'></td></tr>";
    if($p_linea)
      echo "
                    <tr><td colspan='3' class='division_line'>&nbsp;</td></tr>";
  }
  
  # Envia correo de notificacion
  function EnviaNotificacion($ds_comentario, $ext, $fl_usuario, $fl_usuario_post) {
    
    $ds_comentario = str_uso_normal($ds_comentario);
    switch ($ext)
    {
      case 'jpg':
      case 'jpeg': 
        $ds_comentario .= "<p>An image is attached to comment.</p>";
        break;
      case 'ogg':
        $ds_comentario .= "<p>A video is attached to comment.</p>";
        break;
    }
    if((stripos($ds_comentario, "<iframe ") !== false) AND ($ext <> 'ogg'))
      $ds_comentario .= "<p>A video is attached to comment.</p>";
    
    # Prepara variables para envio
    $ds_nombre_ori = ObtenNombreUsuario($fl_usuario);
    $ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario); 
    $row = RecuperaValor("SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_post");
    $ds_email = str_ascii($row[0]);
    $subject = "$ds_nombre_ori commented your post";
    
    # Mensaje del correo
    $message  = "
      <table border='0' cellpadding='0' cellspacing='0' width='100%'>
        <tr>
          <td width='10'>&nbsp;</td>
          <td width='80' valign='top' align='center'><img src='$ds_ruta_avatar' border='none' /></td>
          <td width='20'>&nbsp;</td>
          <td valign='top' style='font-family: Tahoma; font-size: 12px; font-weight: normal;'>
            <b>$ds_nombre_ori</b>
            <p>$ds_comentario</p>
          </td>
          <td width='10'>&nbsp;</td>
        </tr>
      </table>
      <br>
      <p style='font-family: Tahoma; font-size: 11px; font-weight: normal;'>Please go to <a href='http://vanas.ca'>Vancouver Animation School Online Campus</a> to check all the comments.</p>";
    
    # Envia el correo de contacto
    EnviaMailHTML("Vancouver Animation School", MAIL_FROM, $ds_email, $subject, $message);
  }
  
  
  # Recibe parametros
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  $fl_post = RecibeParametroNumerico('fl_post');
  $ds_comentario = RecibeParametroHTML('ds_comentario');
  $nb_archivo = RecibeParametroHTML('archivo');
  
  # Revisa si el usuario es el autor del post
  $row = RecuperaValor("SELECT fl_usuario FROM k_f_post WHERE fl_post=$fl_post");
  $fl_usuario_post = $row[0];
  if($fl_usuario_post == $fl_usuario)
    $fg_leido = '1';
  else
    $fg_leido = '0';
  
  # Revisa si se envio un comentario nuevo
  if(!empty($ds_comentario)) {
    
    # Criterios para conversion del texto posteado
    $ds_comentario = PorcesaCadena($ds_comentario);
    
    # Revisa si se esta subiendo un archivo
    if(!empty($nb_archivo)) {
      $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
      $ruta = $_SERVER[DOCUMENT_ROOT].SP_HOME."/uploads";
      
      # Recibe el archivo seleccionado
      if(file_exists($ruta."/".$nb_archivo))
        unlink($ruta."/".$nb_archivo);
      rename(PATH_CAMPUS_F."/common/tmp/".$nb_archivo, $ruta."/".$nb_archivo);
      
      # Ajusta el maximo de dimensiones para imagenes
      if($ext == "jpg" OR $ext == "jpeg")
        CreaThumb($ruta."/".$nb_archivo, $ruta."/".$nb_archivo, 0, 0, 0, 480);
      
      # Convierte archivos .mov a .ogg
      if($ext == "mov") {
        $parametros = ObtenConfiguracion(41);
        $file_ogg = substr($nb_archivo, 0, (strlen($nb_archivo)-4)) . '.ogg';
        if(file_exists($ruta."/".$file_ogg))
          unlink($ruta."/".$file_ogg);
        $file_mov = $ruta."/".$nb_archivo;
        $comando = CMD_FFMPEG." -i \"$file_mov\" $parametros \"$ruta/$file_ogg\"";
        $nb_archivo = $file_ogg;
      }
    }
    
    # Inserta el comentario del post
    $Query  = "INSERT INTO k_f_comentario(fl_post, fl_usuario, fe_comentario, ds_comentario, nb_archivo, fg_leido) ";
    $Query .= "VALUES($fl_post, $fl_usuario, CURRENT_TIMESTAMP, '$ds_comentario', '$nb_archivo', '$fg_leido')";
    EjecutaQuery($Query);
    
    # Envia correo de notificacion al autor del post
    if($fl_usuario_post <> $fl_usuario)
      EnviaNotificacion($ds_comentario, $ext, $fl_usuario, $fl_usuario_post);
  }
  
  # Recupera comentarios del post
  $Query  = "SELECT fl_comentario, fl_usuario, ds_comentario, DATE_FORMAT(fe_comentario, '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT(fe_comentario, '%e, %Y at %l:%i %p') 'fe_dia_anio', nb_archivo ";
  $Query .= "FROM k_f_comentario ";
  $Query .= "WHERE fl_post=$fl_post ";
  $Query .= "ORDER BY fl_comentario";
  $rs = EjecutaQuery($Query);
  $tot_comentarios = CuentaRegistros($rs);
  if($tot_comentarios > 0) {
    echo "<br>";
    if($tot_comentarios == 1)
      echo "
                <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0'>
                  <tr><td colspan='3' class='forum_x_comments'>1 comment</td></tr>
                  <tr><td colspan='3' height='10'></td></tr>
                </table>";
    if($tot_comentarios > 1) {
      echo "
                <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0'>
                  <tr>
                    <td width='90' class='forum_x_comments'>
                      <a href='javascript:ExpandeComentarios($fl_post);'>$tot_comentarios comments</a>
                    </td>
                    <td class='forum_x_comments'>
                      <table border='".D_BORDES."' cellpadding='0' cellspacing='0'>
                        <tr>";
      
      # Recupera avatares de los que han comentado
      $rs2 = EjecutaQuery("SELECT DISTINCT fl_usuario FROM k_f_comentario WHERE fl_post=$fl_post");
      for($i = 0; $row = RecuperaRegistro($rs2) AND $i < 14; $i++) {
        $ds_ruta_avatar = ObtenAvatarUsuario($row[0]);
        $ds_nombre = ObtenNombreUsuario($row[0]);
        echo "
                          <td><img src='$ds_ruta_avatar' width='25' border='none' title='$ds_nombre' />&nbsp;</td>";
      }
      echo "
                        </tr>
                      </table>
                    </td>
                    <td width='10' class='forum_x_comments'>
                      <a href='javascript:ExpandeComentarios($fl_post);'><div id='expand-collapse_$fl_post'><img src='".SP_IMAGES."/expand.png' border='none' title='Expand' /></div></a>
                    </td>
                  </tr>
                  <tr><td colspan='3' height='10'></td></tr>
                </table>
                <div id='div_comentarios_det_$fl_post' style='display: none;'>
                  <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0'>";
    }
    
    # Lista de comentarios ocultos (solo cuando hay mas de dos comentarios)
    for($i = 0; ($i + 1) < $tot_comentarios; $i++)
      PresentaComentarioPost($rs, $fl_usuario, $fl_post, True);
    if($tot_comentarios > 1)
      echo "
                  </table>
                </div>";
    echo "
                <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0'>";
    PresentaComentarioPost($rs, $fl_usuario, $fl_post, False);
    echo "
                </table>";
    
    if($tot_comentarios > 1)
      echo "
                <div id='div_comentarios_exp_$fl_post' style='display: none;'>
                  <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0'>
                    <tr>
                      <td class='forum_x_comments'>&nbsp;</td>
                      <td width='10' class='forum_x_comments'><a href='javascript:ExpandeComentarios($fl_post);'><img src='".SP_IMAGES."/collapse.png' border='none' title='Collapse' /></a></td>
                    </tr>
                    <tr>
                      <td colspan='2'>
                        <div class='forum_add_comment'>
                          <img src='".SP_IMAGES."/new_comment.png' border='none' />&nbsp;
                          <a href='javascript:NuevoComentario($fl_post);'>Add a comment</a>
                        </div>
                      </td>
                    </tr>
                  </table>
                </div>";
  }
  
  # Actualiza contador de notificaciones para el usuario
  if($fl_usuario_post == $fl_usuario)
    EjecutaQuery("UPDATE k_f_comentario a SET fg_leido='1' WHERE fl_post=$fl_post");
  
  # Convierte archivo mov y lo elimina
  if(!empty($comando))
    exec($comando);
  if(!empty($file_mov))
    unlink($file_mov);
  
?>