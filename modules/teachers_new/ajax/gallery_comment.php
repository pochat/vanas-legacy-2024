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
  
  # Receive parameters
  $fl_gallery_post = RecibeParametroNumerico('fl_post', True);

  $Query  = "SELECT fl_usuario, ds_comment, DATE_FORMAT(fe_comment, '%Y-%m-%d %H:%i:%s') ";
  $Query .= "FROM k_gallery_comment WHERE fl_gallery_post=$fl_gallery_post ";
  $Query .= "ORDER BY fe_comment DESC";
  $rs = EjecutaQuery($Query);

  $result = array();
  for($i=0; $row=RecuperaRegistro($rs); $i++){
    $fl_usuario_comment = $row[0];
    $ds_comment = str_uso_normal($row[1]);
    $fe_comment = time_elapsed_string($row[2], false);

    $nb_usuario_comment = ObtenNombreUsuario($fl_usuario_comment);
    $ds_usu_avatar_comment = ObtenAvatarUsuario($fl_usuario_comment);

    $result["comments".$i] = array(
      "nb_usuario" => $nb_usuario_comment,
      "fl_usuario" => $fl_usuario_comment,
      "ds_avatar" => $ds_usu_avatar_comment,
      "ds_comment" => $ds_comment,
      "fe_comment" => $fe_comment
    );
  }
  $result["size"] = array("total" => $i);
  
  echo json_encode((Object) $result);
?>