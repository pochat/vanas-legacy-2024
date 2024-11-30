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
$fl_post_feed=RecibeParametroNumerico('fl_gallery_post');
$fl_usuario_oculta=RecibeParametroNumerico('fl_usuario');
$fg_tipo_publicacion=RecibeParametroHTML('fg_tipo');
$fg_accion=RecibeParametroNumerico('fg_accion');


if($fg_accion==1){ #Es para oucltar un post en especifico

/*****se comenta por las cuestion : Un usuario que posteo no puede ocultar su mimso post./los demas si pueden a menos que loco de Mario cambie de opinion.

#Si,es el mismo usuario logueado entonces al dar ocultar post, lo olcultara para el resto de la comunidad.
if($fl_usuario==$fl_usuario_oculta){
	$fg_ocultar_general=1;
	
	#Marcamos el post propio que estara oculto.
	if($fg_tipo_publicacion=='g'){ //ocultaos el gallery post
		
		EjecutaQuery("UPDATE k_gallery_post_sp SET fg_oculto='1' WHERE fl_gallery_post_sp=$fl_post_feed ");
		
	}else{ //oucltamos el post feed
		EjecutaQuery("UPDATE c_feed_publicaciones SET fg_oculto='1' WHERE fl_publicacion=$fl_post_feed ");
		
	}
	
}else
	$fg_ocultar_general=0;
*/

$Query="DELETE FROM c_feed_hidden_post_usuario WHERE fl_gallery_post_sp=$fl_post_feed AND fl_usuario_origen=$fl_usuario_oculta ";
EjecutaQuery($Query);

$Query="INSERT INTO c_feed_hidden_post_usuario(fl_gallery_post_sp,fg_tipo,fl_usuario_origen,fg_ocultar,fg_ocultar_general,fe_creacion,fe_ulmod) 
        VALUES($fl_post_feed,'$fg_tipo_publicacion',$fl_usuario_oculta,'1','0',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ";
$fl_hidden_post=EjecutaInsert($Query);

	
	
}

if($fg_accion==2){ #Sirve para elimnar un post/solo aplica para los post que se generene desde del feed//los provenientes del fame board no se pueden elimnar.
	
	
	if($fg_tipo_publicacion=='p'){
		
		$Query="DELETE FROM c_feed_publicaciones WHERE fl_publicacion=$fl_post_feed ";
		EjecutaQuery($Query);
		
		$Query="DELETE FROM k_feed_comment WHERE fl_publicacion=$fl_post_feed ";
		EjecutaQuery($Query);
		
		$fl_hidden_post=$fl_post_feed;
	
		
	}
	
	
	
}


# Check if the insert or update was successful
if(empty($fl_hidden_post)){
    $error = array('error' => "Server Error. This post cannot be uploaded.");
    echo json_encode((Object)$error);
    exit;
}

echo json_encode((Object)array('post' => $fl_hidden_post, 'fl_usuario'=>$fl_usuario_oculta,'fg_accion'=>$fg_accion));

?>







