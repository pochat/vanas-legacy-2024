<?php 
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $usr_interaccion = RecibeParametroNumerico('usr', True);

  # Inicializa variables
  $ds_ruta_avatar = ObtenAvatarUsrFa_Va($fl_usuario);
  $ds_ruta_avatar_i = ObtenAvatarUsrFa_Va($usr_interaccion);
  $ds_nombre = ObtenNombreUsuario($fl_usuario);
  $ds_nombre_i = ObtenNombreUsuario($usr_interaccion);

  # Recupera el mensajes con el usuario
  $diferencia = RecuperaDiferenciaGMT( );
  $Query  = "SELECT fl_mensaje_directo, fl_usuario_ori, fl_usuario_dest, ds_mensaje, ";
  $Query .= "DATE_FORMAT((DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR)), '%M %e, %Y at %l:%i %p') 'fe_message', ";
  $Query .= "fg_leido ";
  $Query .= "FROM k_mensaje_directo ";
  $Query .= "WHERE (fl_usuario_ori=$fl_usuario AND fl_usuario_dest=$usr_interaccion) ";
  $Query .= "OR (fl_usuario_ori=$usr_interaccion AND fl_usuario_dest=$fl_usuario) ";
  $Query .= "ORDER BY fe_mensaje";
  $rs = EjecutaQuery($Query);

  # Initialize Original user and Destination user info
  $result = array();
  $result["user_ori"] = array(
    "name" => utf8_encode(str_ascii($ds_nombre)),
    "avatar" => $ds_ruta_avatar
  );
  $result["user_dest"] = array(
    "name" => utf8_encode(str_ascii($ds_nombre_i)),
    "avatar" => $ds_ruta_avatar_i
  );
 
  for($tot_messages = 0; $row = RecuperaRegistro($rs); $tot_messages++) {
    $fl_mensaje_directo = $row[0];
    $fl_usuario_ori = $row[1];
    $fl_usuario_dest = $row[2];
    $ds_mensaje = html_entity_decode(htmlspecialchars_decode($row[3]), ENT_QUOTES | ENT_HTML5);//str_uso_normal($row[3]);
    $fe_mensaje = $row[4];
    $fg_leido = $row[5];

    if($fl_usuario_ori <> $fl_usuario) {
      $type = "user_dest";
    } else {
      $type = "user_ori";
    }

    if($fg_leido == '0'){
      $fg_unread = "<i class='fa fa-asterisk'></i>";
    } else {
      $fg_unread = "";
    }

    $result["message".$tot_messages] = array(
      "type" => $type,
      "fl_message" => $fl_mensaje_directo,
      "text" => $ds_mensaje,
      "time" => $fe_mensaje,
      "unread" => $fg_unread
    );
  }
  $result["size"] = array("total_messages" => $tot_messages);

  # Actualiza estado de la notificacion para el usuario
  EjecutaQuery("UPDATE k_mensaje_directo SET fg_leido='1' WHERE fl_usuario_ori=$usr_interaccion AND fl_usuario_dest=$fl_usuario");

  echo json_encode((Object) $result);
?>