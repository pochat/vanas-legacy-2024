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
	$fl_post_feed=RecibeParametroNumerico('fl_comentario');
	
    ##Buscamos la respuesta correcta.
	$Query="SELECT fl_gallery_comment_sp FROM v_gallery_feed_comments WHERE fl_gallery_post_sp=$fl_post_feed AND fg_correcto='1' ";
    $ro=RecuperaValor($Query);
    $fl_gallery_comment_sp=$ro[0];

	echo json_encode((Object)array(
	'fl_comentario_respuesta_correcta' => $fl_gallery_comment_sp
	));


?>







