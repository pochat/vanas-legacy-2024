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
 
	$fl_instituto=ObtenInstituto($fl_usuario);
	
    $fg_tipo_respuesta=RecibeParametroHTML('fg_tipo_respuesta');
	$fl_usuario_origen=RecibeParametroNumerico('fl_usuario_origen');
	$fl_usuario_destino=RecibeParametroHTML('fl_usuario_destino');
   

	if($fg_tipo_respuesta==2){
		$Query ="DELETE FROM k_relacion_usuarios WHERE fl_usuario_origen=$fl_usuario_origen AND fl_usuario_destinatario=$fl_usuario_destinatario ";
	}else{
		$Query="UPDATE k_relacion_usuarios SET fe_aceptado=CURRENT_TIMESTAMP ,fg_aceptado='1' WHERE fl_usuario_origen=$fl_usuario_origen AND fl_usuario_destinatario=$fl_usuario_destinatario ";
	}
    EjecutaQuery($Query);

	
	
	
	$result=array(
	 "fg_tipo_respuesta"=>$fg_tipo_respuesta,
	 "fl_usuario_origen"=>$fl_usuario_origen,
	 "fl_usuario_actual"=>$fl_usuario
	);
	
	
	
	
	
	echo json_encode((Object) $result);
	
  ?>


