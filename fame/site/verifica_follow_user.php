<?php
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario = ValidaSesion(False,0, True);
	$fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
	# Obtenemos el instituto
	$fl_instituto = ObtenInstituto($fl_usuario);

	#RecibeParametros.
	$fl_usuario_del_post=RecibeParametroNumerico('fl_usuario_posteo');



	$Query="SELECT fl_followers FROM c_followers WHERE fl_usuario_destino=$fl_usuario_del_post AND fl_usuario_origen=$fl_usuario ";
	$rwo=RecuperaValor($Query);
	$fl_followers=!empty($rwo['fl_followers'])?$rwo['fl_followers']:NULL;


	if(!empty($fl_followers)){			
		$fg_seguidor=1;
	}else{	
		$fg_seguidor=0;	
	}

	$result['tipo_icono_follower'] = $fg_seguidor;


		
   echo json_encode((Object) $result);



?>

