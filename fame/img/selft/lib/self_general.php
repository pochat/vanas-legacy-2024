<?php

  # Definicion de librerias para Sitios de Alumnos y Maestros
  require($_SERVER['DOCUMENT_ROOT'].'/vanas/lib/com_func.inc.php');
  require($_SERVER['DOCUMENT_ROOT'].'/vanas/lib/sp_config.inc.php');

  # New campus libraries
  // require($_SERVER['DOCUMENT_ROOT'].'/vanas/modules/common/new_campus/lib/cam_layout.inc.php');
  // require($_SERVER['DOCUMENT_ROOT'].'/vanas/modules/common/new_campus/lib/cam_util_func.inc.php');
  require($_SERVER['DOCUMENT_ROOT'].'/vanas/self_pace/lib/com_layout_self.php');

#
# Funciones para Administradores Alumnos y Maestros
#

function ObtenPerfilUsuario($p_usuario) {
  
  # Recupera perfil de un usuario
  $row = RecuperaValor("SELECT fl_perfil FROM c_usuario WHERE fl_usuario='$p_usuario'");
  return $row[0];
}

function ObtenNombreUsuario($p_usuario) {
  
  # Recupera matricula del usuario
  $concat = array('ds_nombres', "' '", 'ds_apaterno');
  $row = RecuperaValor("SELECT ".ConcatenaBD($concat)." FROM c_usuario WHERE fl_usuario=$p_usuario");
  return str_uso_normal($row[0]);
}

function ObtenAvatarUsuario($p_usuario) {
  
  # Recupera el perfil del usuario
  $fl_perfil = ObtenPerfilUsuario($p_usuario);
  
  # Verifica si el usuario tiene un avatar
  if($fl_perfil == PFL_MAESTRO) {
    $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_maestro WHERE fl_maestro=$p_usuario");
    if(!empty($row[0]))
      $ds_ruta_avatar = PATH_MAE_IMAGES."/avatars/".$row[0];
    else
      $ds_ruta_avatar = SP_IMAGES."/".IMG_T_AVATAR_DEF;
  }
  else {
    $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_alumno WHERE fl_alumno=$p_usuario");
    if(!empty($row[0]))
      $ds_ruta_avatar = PATH_ALU_IMAGES."/avatars/".$row[0];
    else
      $ds_ruta_avatar = SP_IMAGES."/".IMG_S_AVATAR_DEF;
  }
  return $ds_ruta_avatar;
}

# Obten el intituto del administrador
function ObtenInstituto($p_admin){
  $row = RecuperaValor("SELECT fl_instituto FROM c_usuario_sp WHERE fl_usuario_sp=$p_admin");
  return $row[0];
}

# Funcion para obtener el numero de licencias dependiendo del administrador
function ObtenNumLicencias($p_admin){
  $row = RecuperaValor("SELECT no_licencias FROM k_current_plan WHERE fl_usuario_sp=$p_admin");
  return $row[0];
}

# Funcion para obtener el numero de usuarios por escuela
function ObtenNumeroUserInst($p_instituto){
  $row = RecuperaValor("SELECT COUNT(*) FROM c_usuario_sp WHERE fl_instituto=$p_instituto");
  return $row[0];
}

  
?>