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
	$fl_gallery_post = RecibeParametroHTML('fl_gallery_post');
	$fl_comentari=RecibeParametroNumerico('fl_comentari');
	
	
	
	#Verificamos si ya esta marcado como correcta, y actualizamos el estatus.
	$Query="SELECT origen,fg_correcto,fl_usuario FROM v_gallery_feed_comments WHERE fl_gallery_comment_sp=$fl_comentari ";
	$rop=RecuperaValor($Query);
	$orugen=$rop['origen'];
	$fg_correcto=$rop['fg_correcto'];
	$fl_usuario_coment=$rop['fl_usuario'];

	#Datos del usuario original del post
	$Query="SELECT ds_email,ds_nombres,ds_apaterno,ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_coment ";
	$ro=RecuperaValor($Query);
	$ds_email_destin=$ro['ds_email'];
	$first_name=str_texto($ro[1]);
	$last_name=str_texto($ro[2]);
	$ds_email_destin=str_texto($row[3]);
	
	#Datos del usuario que esta marcando la respuesta
	$Query1="SELECT ds_email,ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario ";
	$rou=RecuperaValor($Query1);
	$ds_email_marcando=$rou['ds_email'];
	$fname_marcando_resp=str_texto($rou[1]);
	$lname_marcando_resp=str_texto($rou[2]);
		
	$name_calificador=$fname_marcando_resp." ".$lname_marcando_resp;	
	
	
	##Quitamos el marcado de respuesta correcta.
	if($fg_correcto==1){
		
		
		//viene del feed.
		if($orugen=='p'){
			//Actualizamos todos a 0 y despues a '1' el mero bueno.
			$Query="UPDATE k_feed_comment SET fg_correcto='0' WHERE fl_publicacion=$fl_gallery_post  ";
			EjecutaQuery($Query);
		}
		
		//viene del board gallery.
		if($orugen=='g'){
			
			//Actualizamos todos a 0 y despues a '1' el mero bueno.
			$Query="UPDATE k_gallery_comment_sp SET fg_correcto='0' WHERE fl_gallery_post_sp=$fl_gallery_post  ";
			EjecutaQuery($Query);
	
		}
		
		//nos indicara que la se elimnar esa respuesta y tendra que selccionar otra,
		$fg_elimnar_respuesta=1;	
		
		
	}else{
		$fg_elimnar_respuesta=0;
	
		
		

		//viene del feed.
		if($orugen=='p'){
			//Actualizamos todos a 0 y despues a '1' el mero bueno.
			$Query="UPDATE k_feed_comment SET fg_correcto='0' WHERE fl_publicacion=$fl_gallery_post  ";
			EjecutaQuery($Query);
			
			$Query="UPDATE k_feed_comment SET fg_correcto='1' WHERE fl_feed_comment=$fl_comentari  ";
			EjecutaQuery($Query);
			
			
			
		}
		
		//viene del board gallery.
		if($orugen=='g'){
			
			//Actualizamos todos a 0 y despues a '1' el mero bueno.
			$Query="UPDATE k_gallery_comment_sp SET fg_correcto='0' WHERE fl_gallery_post_sp=$fl_gallery_post  ";
			EjecutaQuery($Query);
					
			$Query="UPDATE k_gallery_comment_sp SET fg_correcto='1' WHERE fl_gallery_comment_sp=$fl_comentari  ";
			EjecutaQuery($Query);
			
			
			
		}
		
		
        if(VerificaPermisoEnvioEmail($fl_usuario_coment,'fg_ayuda_post')){

			# Obtenmos el template para enviar y decirleque su respuesta fue correcta
			$ds_header = genera_documento_sp('', 1, 166);
			$ds_body = genera_documento_sp('', 2, 166);
			$ds_footer = genera_documento_sp('', 3, 166);
			$dominio_campus = ObtenConfiguracion(116);
			$src_redireccion=$dominio_campus;#bueno
			
			$ruta_avatar_comment=ObtenAvatarUsuario($fl_usuario);

			
			$ds_mensaje=$ds_header.$ds_body.$ds_footer;
			$ds_mensaje = str_replace("#fame_fname#", $first_name, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_lname#", $last_name, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_fname_friends#", $fname_marcando_resp, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_lname_friends#", $lname_marcando_resp, $ds_mensaje);
		  
			$ds_mensaje = str_replace("#fame_ds_avatar_ori#", $dominio_campus.$ruta_avatar_comment, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_link#", $src_redireccion, $ds_mensaje);
		  
		 
			# Nombre del template
			$Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=166 AND fg_activo='1'";
			$template = RecuperaValor($Query0);
			$nb_template = str_uso_normal($template[0]);
			# Este email es necesario
			$from = ObtenConfiguracion(107);#de donde sale el email.
	  

			# Enviamos el correo al usuario dependiendo de la accion
			EnviaMailHTML($from, $from, $ds_email_destin, $nb_template, $ds_mensaje);
			
			
		}

		
			
			
	}


	# Check if the insert or update was successful
	if(empty($fl_comentari)){
		$error = array('error' => "Server Error. This post cannot be help.");
		echo json_encode((Object)$error);
		exit;
	}

	echo json_encode((Object)array('fl_gallery_post' => $fl_gallery_post,'fg_elimnar_respuesta'=>$fg_elimnar_respuesta,'fl_user_resp_correcta'=>$fl_usuario_coment,'fl_comentari'=>$fl_comentari,'etq_dialogo'=>ObtenEtiqueta(2515),'name_calificador'=>$name_calificador));


?>







