<?php
 
    #Libreria de funciones 
    require("../../lib/self_general.php");

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
	
	#Recuoermaos nombre del origen.
	$Queryu  = "SELECT ds_nombres, ds_apaterno 
               FROM c_usuario WHERE fl_usuario=$fl_usuario ";
	$row=RecuperaValor($Queryu);
	$nb_usuario_confirma_solicitud=str_texto($row[0])." ".str_texto($row[1]);
	
   

	if($fg_tipo_respuesta==1){
		$Query ="DELETE FROM k_relacion_usuarios WHERE fl_usuario_origen=$fl_usuario_origen AND fl_usuario_destinatario=$fl_usuario ";
	}else{
		$Query="UPDATE k_relacion_usuarios SET fe_aceptado=CURRENT_TIMESTAMP ,fg_aceptado='1' WHERE fl_usuario_origen=$fl_usuario_origen AND fl_usuario_destinatario=$fl_usuario ";
	}
    EjecutaQuery($Query);

		
	
	$result=array(
	 "fg_tipo_respuesta"=>$fg_tipo_respuesta,
	 "fl_usuario_origen"=>$fl_usuario_origen,
	 "fl_usuario_actual"=>$fl_usuario,
	 "nb_usuario_confirma_solicitud"=>$nb_usuario_confirma_solicitud
	);
	
	
	
	
	
	echo json_encode((Object) $result);
	
  ?>

