<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  require("../common/lib/cam_forum.inc.php");
  
  # Recibe parametros
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  $fl_tema = RecibeParametroNumerico('fl_tema');
  $ds_post = RecibeParametroHTML('ds_post');
  $ds_ruta_avatar_usu = ObtenAvatarUsuario($fl_usuario);
  $nb_archivo = RecibeParametroHTML('archivo');
  
  # Revisa si se envio un post nuevo
  if(!empty($ds_post)) {
    
    # Criterios para conversion del texto posteado
    $ds_post = PorcesaCadena($ds_post);
    
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
    
    # Inserta el post
    $Query  = "INSERT INTO k_f_post(fl_tema, fl_usuario, fe_post, ds_post, nb_archivo) ";
    $Query .= "VALUES($fl_tema, $fl_usuario, CURRENT_TIMESTAMP, '$ds_post', '$nb_archivo')";
    EjecutaQuery($Query);
    
    # Actualiza contador de posts
    EjecutaQuery("UPDATE c_f_tema SET no_posts=no_posts+1 WHERE fl_tema=$fl_tema");
    
    # Actualiza notificaciones para usuarios por tema
    $rs = EjecutaQuery("SELECT fl_usuario FROM c_usuario WHERE fl_perfil IN(".PFL_ESTUDIANTE.", ".PFL_MAESTRO.") AND fl_usuario<>$fl_usuario");
    while($row = RecuperaRegistro($rs)) {
      $row2 = RecuperaValor("SELECT COUNT(1) FROM k_f_usu_tema WHERE fl_usuario=$row[0] AND fl_tema=$fl_tema");
      if($row2[0] == 0)
        EjecutaQuery("INSERT INTO k_f_usu_tema(fl_usuario, fl_tema, no_posts) VALUES($row[0], $fl_tema, 1)");
      else
        EjecutaQuery("UPDATE k_f_usu_tema SET no_posts=no_posts+1 WHERE fl_usuario=$row[0] AND fl_tema=$fl_tema");
    }
  }
  
  # Genera instancias de TinyMCE para nuevo comentarios
  GeneraTinyMCE('TM_comment', '480', '100');
  
  # Dialogo para tooltip
  echo "
  <script type='text/javascript'>
    $(function() {
      $('#dialog').dialog({
        autoOpen: false,
        resizable: false,
        minHeight: 20
      });
      $('.name_tooltip').mouseenter(function() {
        var user = this.id;
        $('#dialog').dialog('option', 'position', 
          [$(this).position().left - $(document).scrollLeft(), $(this).position().top - $(document).scrollTop() + $(this).outerHeight() + 3]
        );
        $('#dialog').html('<img src=\"../common/images/loading.gif\"> Loading...');
        $('#dialog').dialog('open');
        $('#dialog').load('div_user_tooltip.php', {fl_usuario: user});
      }).mouseleave(function() {
        $('#dialog').html('');
        $('#dialog').dialog('close');
      });
      $('.ui-dialog-titlebar').hide();
    });
  </script>";
  
  # Maximo de posts a mostrar para boton "Mostrar mas"
  $max_posts = ObtenConfiguracion(48);
  $fg_ver_mas = False;
  
  # Recupera posts del tema seleccionado
  $Query  = "SELECT fl_post, fl_usuario, ds_post, DATE_FORMAT(fe_post, '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT(fe_post, '%e, %Y at %l:%i %p') 'fe_dia_anio', nb_archivo ";
  $Query .= "FROM k_f_post ";
  $Query .= "WHERE fl_tema=$fl_tema ";
  $Query .= "ORDER BY fl_post DESC";
  $rs = EjecutaQuery($Query);
  for($tot_posts = 0; $row = RecuperaRegistro($rs); $tot_posts++) {
    $fl_post = $row[0];
    $fl_usuario_post = $row[1];
    $ds_nombre = ObtenNombreUsuario($fl_usuario_post);
    $ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario_post);
    $ds_post = str_uso_normal($row[2]);
    $fe_post = ObtenNombreMes($row[3])." ".$row[4];
    $nb_archivo = str_uso_normal($row[5]);
    if($tot_posts == $max_posts) {
      echo "
    <div id='div_ver_mas_liga' class='forum_add_post' style='text-align: center;'>
      <a href='javascript:VerMas();'>View older posts</a>
    </div>
    <div id='div_ver_mas' style='display: none;'>";
      $fg_ver_mas = True;
    }
    echo "
      <div id='div_post_$fl_post'>
        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
          <tr><td colspan='4' class='division_line'>&nbsp;</td></tr>
          <tr>
            <td width='80' valign='top' align='center'>
              <a href='profile_view.php?profile_id=$fl_usuario_post'><img src='$ds_ruta_avatar' border='0' title='View $ds_nombre profile'></a>
            </td>
            <td width='10'>&nbsp;</td>
            <td valign='top' class='comment_text'>
              <table border='".D_BORDES."' width='100%' cellspacing='0' cellpadding='0'>
                <tr>
                  <td>
                    <div style='float: left; margin-right: 10px; margin-top: 1px;' width='50%'>
                      <b>$ds_nombre</b>
                    </div>";
    if($fl_usuario_post <> $fl_usuario)
      echo "
                    <a href='javascript:SendMessageDialog($fl_usuario_post);'><img src='".SP_IMAGES."/".ObtenNombreImagen(217)."' width='16' height='16' border='none' title='Send a message to $ds_nombre'></a>";
    echo "
                  </td>
                  <td width='50%' align='right'>$fe_post";
    if($fl_usuario_post == $fl_usuario)
      echo "&nbsp;&nbsp;<a href='javascript:BorraPost($fl_post);'><img src='".PATH_COM_IMAGES."/delete.png' width='15' height='15' border='0' title='Delete post'></a>";
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
              $ds_post
            </td>
            <td width='90'>&nbsp;</td>
          </tr>
          <tr><td colspan='4' height='10'>&nbsp;</td></tr>
          <tr>
            <td colspan='2' height='10'>&nbsp;</td>
            <td>
              <div class='forum_add_comment'>
                <img src='".SP_IMAGES."/new_comment.png' border='none' />&nbsp;
                <a href='javascript:NuevoComentario($fl_post);'>Add a comment</a>
              </div>
            </td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan='2'>&nbsp;</td>
            <td><div id='div_comentarios_$fl_post'></div></td>
            <td>&nbsp;</td>
          </tr>
          <script type='text/javascript'>
            MuestraComentarios($fl_post);
          </script>";
    
    # Forma para nuevos comentarios
    echo "
          <tr>
            <td colspan='2'>&nbsp;</td>
            <td>
              <div id='div_forma_comment_$fl_post' style='display: none;'>
                <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0'>
                  <tr><td colspan='3' class='division_line'>&nbsp;</td></tr>
                  <tr>
                    <td width='45' valign='top'><img src='$ds_ruta_avatar_usu' border='none' width='45' /></td>
                    <td width='10'></td>
                    <td><textarea class='TM_comment' name='ds_comment_$fl_post' id='ds_comentario_$fl_post'></textarea></td>
                  </tr>
                  <tr><td colspan='3' height='10'></td></tr>
                  <tr>
                    <td colspan='2'>&nbsp;</td>
                    <td>
                      <div class='comment_text' style='float: left;' title='Upload file'>
                        <div id='fu_archivo_$fl_post'></div>
                      </div>
                      <div style='float: right; widh: 100%; text-align: right;'>
                        <button onclick='javascript:InsertaComentario($fl_post);'>Publish</button>
                        &nbsp;
                        <button onclick='javascript:NuevoComentario($fl_post);'>Cancel</button>
                      </div>
                    </td>
                  </tr>
                </table>
              </div>
            </td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </div>";
  }
  
  # Cierra div de Ver mas
  if($fg_ver_mas)
    echo "
    </div>";
  
  # Mensaje si no hay ningun post
  if($tot_posts == 0)
    echo "
      <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
        <tr><td colspan='2' class='division_line'>&nbsp;</td></tr>
        <tr><td width='90'>&nbsp;</td><td>There are no posts for this stream yet.</td></tr>
      </table>";
  
  # Convierte archivo mov y lo elimina
  if(!empty($comando))
    exec($comando);
  if(!empty($file_mov))
    unlink($file_mov);
  
?>