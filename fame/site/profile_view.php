<?php
  # Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
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
      // $ds_redirect = "profile_student.php?profile_id=$fl_profile";
      echo "location.hash = '#site/profile_user.php?profile_id=$fl_profile'";     
  }
  else {
    $ds_redirect = "profile.php";
    echo "location.hash = '#site/profile.php'";
    //header("Location: ".$ds_redirect);
  }
  echo 
    "</script>";
?>