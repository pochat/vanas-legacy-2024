<?php
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
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
    # Depende del perfil si es FAME o CAMPUS
    $row1 = RecuperaValor("SELECT IFNULL(fl_perfil_sp, fl_perfil), fl_instituto FROM c_usuario WHERE fl_usuario=".$fl_usuario_comment);
    $fl_perfil_usr_comment = $row1[0];
    $fl_instituto = $row1[1];	  
    if($fl_perfil_usr_comment==PFL_ADMINISTRADOR || $fl_perfil_usr_comment == PFL_MAESTRO_SELF || $fl_perfil_usr_comment == PFL_ESTUDIANTE_SELF){
      # Obtenemos el avatar de usuario de FAME
      $Query2 = "SELECT ds_ruta_avatar FROM ";
      if($fl_perfil_usr_comment == PFL_ADMINISTRADOR)
        $Query2 .= "c_administrador_sp WHERE fl_adm_sp=".$fl_usuario_comment;
      if($fl_perfil_usr_comment == PFL_MAESTRO_SELF)
        $Query2 .= "c_maestro_sp WHERE fl_maestro_sp=".$fl_usuario_comment;
      if($fl_perfil_usr_comment == PFL_ESTUDIANTE_SELF)
        $Query2 .= "c_alumno_sp WHERE fl_alumno_sp=".$fl_usuario_comment;
      $row2 = RecuperaValor($Query2);
      $ds_ruta_avatar = $row2[0];
      if(!empty($ds_ruta_avatar))
        $ds_usu_avatar_comment = PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_usuario_comment."/".$ds_ruta_avatar;
      else
        $ds_usu_avatar_comment = SP_IMAGES."/".IMG_S_AVATAR_DEF;
      $nb_usuario_comment .= "<span>(FAME)</span>";
    }
    else
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