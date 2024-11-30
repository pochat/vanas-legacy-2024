<?php
require("../lib/self_general.php");


# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);

# Obtenemos el instituto
$fl_instituto = ObtenInstituto($fl_usuario);

# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
}

# Receive parameters
$fl_usuario_origen=RecibeParametroNumerico('fl_usuario_origen');
$fl_usuario_destino=RecibeParametroNumerico('fl_usuario_destino');
$fg_accion=RecibeParametroNumerico('fg_accion');



if($fg_accion==1){ #Es seguir a otro usuario.

	$Query="INSERT INTO c_followers(fl_usuario_origen,fl_usuario_destino,fe_creacion,fe_ulmod) 
			VALUES($fl_usuario_origen,$fl_usuario_destino,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ";
	$fl_followers=EjecutaInsert($Query);

	
	
}

if($fg_accion==2){ #Es para dejar de seguir al otro usuario.

	$Query="DELETE FROM c_followers WHERE fl_usuario_origen=$fl_usuario_origen AND fl_usuario_destino=$fl_usuario_destino ";
	EjecutaQuery($Query);
	$fl_followers=1;
}


# Check if the insert or update was successful
if(empty($fl_hidden_post)){
    $error = array('error' => "Server Error. This post cannot be uploaded.");
    echo json_encode((Object)$error);
    exit;
}

echo json_encode((Object)array('fg_corecto' => $fl_followers));
?>







