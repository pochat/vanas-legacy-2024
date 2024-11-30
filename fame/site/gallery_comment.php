<?php
	# Libreria de funciones
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $fl_gallery_post_sp = RecibeParametroNumerico('fl_post', True);
  $fl_gallery_comment_sp = RecibeParametroNumerico('fl_comentario', True);
  $fame = RecibeParametroNumerico('fame', True);
  
  # Obtenemos el usuario del post para poderlo marcar como leido
  if(!empty($fl_gallery_comment_sp)){
    if($fame==1){
      $row0 = RecuperaValor("SELECT fg_read FROM k_gallery_comment_sp WHERE fl_gallery_comment_sp=$fl_gallery_comment_sp");
      # Solo actualiza si el coemtario no ha sido leido
      if(empty($row0[0])){
        EjecutaQuery("UPDATE k_gallery_comment_sp SET fg_read='1' WHERE fl_gallery_comment_sp=$fl_gallery_comment_sp");
        # actualizamos el total de notificaciones
        EjecutaQuery("UPDATE k_usu_notify SET no_notice=no_notice-1 WHERE fl_usuario=".$fl_usuario);
      }
    }
    else{
      $row0 = RecuperaValor("SELECT fg_read FROM k_gallery_comment WHERE fl_gallery_comment=$fl_gallery_comment_sp");
      # Solo actualiza si el coemtario no ha sido leido
      if(empty($row0[0])){
        EjecutaQuery("UPDATE k_gallery_comment SET fg_read='1' WHERE fl_gallery_comment=$fl_gallery_comment_sp");        
      }
    }
  }  
  # Get info post
  if($fame==1){
    $Query  = "SELECT fl_usuario, ds_comment, DATE_FORMAT(fe_comment, '%Y-%m-%d %H:%i:%s') ";
    $Query .= "FROM k_gallery_comment_sp WHERE fl_gallery_post_sp=$fl_gallery_post_sp ";
    $Query .= "ORDER BY fe_comment DESC";
  }
  else{
    $Query  = "SELECT fl_usuario, ds_comment, DATE_FORMAT(fe_comment, '%Y-%m-%d %H:%i:%s') ";
    $Query .= "FROM k_gallery_comment WHERE fl_gallery_post=$fl_gallery_post_sp ";
    $Query .= "ORDER BY fe_comment DESC";
  }
  $rs = EjecutaQuery($Query);

  $result = array();
  for($i=0; $row=RecuperaRegistro($rs); $i++){
  	$fl_usuario_comment = $row[0];
	  $ds_comment = str_uso_normal($row[1]);
	  $fe_comment = $row[2];
    $fe_comment = time_elapsed_string($fe_comment, false);

	  $nb_usuario_comment = ObtenNombreUsuario($fl_usuario_comment, $fl_usuario);
    # Obtenemos perfil del usuario que comento
    $fl_perfil_us_coment = ObtenPerfilUsuario($fl_usuario_comment);
    if($fl_perfil_us_coment == PFL_ESTUDIANTE_SELF || $fl_perfil_us_coment == PFL_MAESTRO_SELF || $fl_perfil_us_coment == PFL_ADMINISTRADOR)
      $ds_usu_avatar_comment = ObtenAvatarUsuario($fl_usuario_comment);
    else
      $ds_usu_avatar_comment = ObtenAvatarUsrVanas ($fl_usuario_comment);
      

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