<?php 
	# Libreria de funciones
	// require("../../modules/common/lib/cam_general.inc.php");
	// require("../lib/layout_self.php");
	// require("../lib/self_func.php");
  require("../lib/self_general.php");
  // $fl_insituto = ObtenInstituto($fl_usuario);
  
  $fl_perfil_sp = RecibeParametroNumerico('fl_perfil_sp');
  $ds_email = RecibeParametroHTML('ds_email');
  $ds_fname = RecibeParametroHTML('ds_fname');
  $ds_lname = RecibeParametroHTML('ds_lname');
  
  echo "ok";
  
  
?>