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
  $fl_profile = RecibeParametroNumerico('profile_id');
  if(empty($fl_profile))
    $fl_profile = RecibeParametroNumerico('profile_id', True);
	if(empty($fl_profile))
    $fl_profile = $fl_usuario;

  echo
    "<script type='text/javascript'>";
  
  # Recupera perfil del usuarios solicitado
  if($fl_profile <> $fl_usuario) {
    $fl_perfil = ObtenPerfilUsuario($fl_profile);
    if($fl_perfil == PFL_MAESTRO){
      
      $ds_redirect = "profile_teacher.php?profile_id=$fl_profile";
      echo "location.hash = '#ajax/profile_teacher.php?profile_id=$fl_profile'";
    }
    else {
      $ds_redirect = "profile_student.php?profile_id=$fl_profile";
      echo "location.hash = '#ajax/profile_student.php?profile_id=$fl_profile'"; 
    }
  }
  else {
    $ds_redirect = "profile.php";
    echo "location.hash = '#ajax/profile.php'";
    //header("Location: ".$ds_redirect);
  }
  echo 
    "</script>";
  
?>

