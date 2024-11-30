<?php
  
	# Libreria de funciones	
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario = ValidaSesion(False,0, True);
	$fl_perfil = ObtenPerfilUsuario($fl_usuario);
	$fl_instituto = ObtenInstituto($fl_usuario);

	# Verifica que el usuario tenga permiso de usar esta funcion
	if(!ValidaPermisoSelf(FUNC_SELF)) {  
	MuestraPaginaError(ERR_SIN_PERMISO);
	exit;
	}


	$fl_instituto_elegido=RecibeParametroNumerico('fl_instituto');

    if(!empty($fl_instituto_elegido)){
		$Query="UPDATE c_usuario SET  fl_instituto=$fl_instituto_elegido ,fg_select_instituto='1' WHERE fl_usuario=$fl_usuario ";
		EjecutaQuery($Query);
	}else{
		
		//$Query="UPDATE c_usuario SET  fl_instituto=NULL WHERE fl_usuario=$fl_usuario ";
		//EjecutaQuery($Query);
		
	}
?>