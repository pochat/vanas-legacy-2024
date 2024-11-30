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
  
  # Inicia pagina
  $titulo = "Messages";
  PresentaHeader($titulo);
  
  # Inicializa variables de la forma
  echo "
  <div id='dialog'></div>
  <script type='text/javascript'>
    
    $(function() {
      
      // Dialogo para tooltip
      $('#dialog').dialog({
        autoOpen: false,
        resizable: false,
        minHeight: 20
      });
      $('.name_tooltip').mouseenter(function() {
        var user = this.id;
        $('#dialog').dialog('option', 'position', 
          [$(this).position().left - $(document).scrollLeft(), $(this).position().top - $(document).scrollTop() + $(this).outerHeight() + 2]
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
  
  # Inicia lista
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' valign='top' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td colspan='3' valign='top' height='80' class='division_line'>";
  
  # Recupera usuarios que han enviado o se les ha enviado mensajes
  $diferencia = RecuperaDiferenciaGMT( );
  $Query  = "SELECT usr_interaccion, DATE_FORMAT(MAX(fe_mensaje), '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT(MAX(fe_mensaje), '%e, %Y at %l:%i %p') 'fe_dia_anio', MAX(fe_mensaje) cuando FROM( ";
  $Query .= "SELECT CASE WHEN fl_usuario_ori<>$fl_usuario then fl_usuario_ori ELSE fl_usuario_dest END usr_interaccion, ";
  $Query .= "DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR) fe_mensaje ";
  $Query .= "FROM k_mensaje_directo ";
  $Query .= "WHERE fl_usuario_ori=$fl_usuario ";
  $Query .= "OR fl_usuario_dest=$fl_usuario) usuarios ";
  $Query .= "GROUP BY usr_interaccion ";
  $Query .= "ORDER BY cuando DESC";
  $rs = EjecutaQuery($Query);
  $tot_usuarios = CuentaRegistros($rs);
  while($row = RecuperaRegistro($rs)) {
    $usr_interaccion = $row[0];
    $fe_mensaje = ObtenNombreMes($row[1])." ".$row[2];
    $ds_ruta_avatar = ObtenAvatarUsuario($usr_interaccion);
    $ds_nombre = ObtenNombreUsuario($usr_interaccion);
    
    # Revisa si hay mensajes pendientes por leer
    $Query  = "SELECT COUNT(1) FROM k_mensaje_directo WHERE fl_usuario_ori=$usr_interaccion AND fl_usuario_dest=$fl_usuario AND fg_leido='0'";
    $row2 = RecuperaValor($Query);
    if($row2[0] > 0)
      $ds_notificar = "(You have unread messages)";
    else
      $ds_notificar = "";
    
    # Recupera datos de los mensajes con el usuario
    $Query  = "SELECT fl_usuario_ori ";
    $Query .= "FROM k_mensaje_directo ";
    $Query .= "WHERE (fl_usuario_ori=$fl_usuario AND fl_usuario_dest=$usr_interaccion) ";
    $Query .= "OR (fl_usuario_ori=$usr_interaccion AND fl_usuario_dest=$fl_usuario) ";
    $Query .= "ORDER BY fl_mensaje_directo DESC";
    $rs2 = EjecutaQuery($Query);
    $no_cuantos = CuentaRegistros($rs2);
    $row2 = RecuperaRegistro($rs2);
    if($row2[0] <> $fl_usuario)
      $img_envio = ObtenNombreImagen(218);
    else
      $img_envio = ObtenNombreImagen(219);
    echo "
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr><td colspan='5' height='20'>&nbsp;</td></tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td width='80' valign='middle' align='center'>
                              <a href='profile_view.php?profile_id=$usr_interaccion'><img src='$ds_ruta_avatar' border='none' title='View $ds_nombre profile'/></a>
                            </td>
                            <td width='20'>&nbsp;</td>
                            <td>
                              <div class='comment_text'>
                                <a href='messages_detail.php?usr=$usr_interaccion' class='name_tooltip' id='$usr_interaccion'>$ds_nombre</a>
                                ($no_cuantos)
                                <a href='messages_detail.php?usr=$usr_interaccion' class='text_unread' title='View $ds_nombre messages'>$ds_notificar</a>
                              </div>
                              Last message on $fe_mensaje<br>
                              <img src='".SP_IMAGES."/$img_envio' border='none'/>
                              &nbsp;&nbsp;<a href='messages_detail.php?usr=$usr_interaccion'>View all $ds_nombre messages</a>
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                        </table>";
  }
  
  # Mensaje cuando no hay usuarios
  if($tot_usuarios == 0) {
    echo "
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr><td colspan='5' height='10'>&nbsp;</td></tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td colspan='3'>
                              <div class='comment_text'>You have no private messages.</div>
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                        </table>";
  }
  
  # Termina lista
  echo "
                      </td>
                    </tr>";
  
  PresentaFooter( );
  
?>