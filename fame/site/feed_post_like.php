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
	$fg_origen = RecibeParametroHTML('fg_origen');
	$fl_post_feed=RecibeParametroNumerico('item');
	$fg_like_comentario=RecibeParametroNumerico('fg_like_comentario');//idica si le dio like a los comnetarios de un post.
    $fl_usuario_post_original=RecibeParametroNumerico('fl_usuario_post_original');//es user al que pertenecese ese post � comentario.
	$fg_nivel_comentario=RecibeParametroNumerico('fg_nivel');

    ##Uusuario que esta dado ese like.
	$Query="SELECT ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario ";
    $ro=RecuperaValor($Query);
    $fname_like=str_texto($ro[0]);
    $lname_like=str_texto($ro[1]);

    

	//Cuando le da likes a los comentarios.
	if($fg_like_comentario==1){
	
	
	
	   
	   #Recuperamos el usuario orifginal del post.
	   $Query="SELECT  ds_nombres,ds_apaterno, fl_usuario,ds_email FROM c_usuario  
				WHERE fl_usuario=$fl_usuario_post_original ";
	   $rm=RecuperaValor($Query);
	   $first_name=str_texto($rm[0]);
       $last_name=str_texto($rm[1]);
	   $fl_usuario_comen=$rm[2];
	   $ds_email_destin=$rm[3];
	    

      if($fg_nivel_comentario==1){


		   #Con esto verificamos si este usuario ya le dio like a ese post.
		   $queryS="SELECT COUNT(1) as total, fg_like FROM k_feed_likes WHERE fl_gallery_comment_sp=$fl_post_feed and fg_origen='$fg_origen' and fl_usuario=$fl_usuario";
		   $rowS=RecuperaValor($queryS);
		   $totalLikeUser=$rowS['total'];
		   $fg_like=$rowS['fg_like'];
	




		   if(!empty($totalLikeUser)){
			   
				#Eliminamos su like por que ya existe.
				$Query="DELETE FROM k_feed_likes WHERE fl_usuario=$fl_usuario AND fl_gallery_comment_sp=$fl_post_feed AND fg_origen='$fg_origen' ";
				EjecutaQuery($Query);
				$fl_gallery_post=true;

				$fg_accion=2;	   
		   }else{
			   
				#Insertamos su like de este usuario.
				#Inserta el like correspondiente al usuario y relacionado al post.
				$Query = "INSERT INTO k_feed_likes ";
				$Query .= "(fl_gallery_comment_sp, fl_usuario,fg_origen, fe_alta,fg_like ) ";
				$Query .= "VALUES ($fl_post_feed,$fl_usuario,'$fg_origen',CURRENT_TIMESTAMP,'1')";
				$fl_gallery_post = EjecutaInsert($Query);
			   
				$fg_accion=1;
					
			   
		   }
		
	  }
	  #Para los comenatrios de 3er nivel.
	  if($fg_nivel_comentario==2){
		  
		  
          #Recuperamos la publicacion original el 1er nivel
          $Query="SELECT fl_publicacion FROM k_feed_comment WHERE fl_feed_comment=$fl_post_feed ";
          $row=RecuperaValor($Query);
          $fl_publicacion=$row['fl_publicacion'];




		   #Con esto verificamos si este usuario ya le dio like a ese post.
		   $queryS="SELECT COUNT(1) as total, fg_like FROM k_feed_likes WHERE fl_gallery_comment_sp_comment=$fl_post_feed and fg_origen='$fg_origen' and fl_usuario=$fl_usuario ";
		   $rowS=RecuperaValor($queryS);
		   $totalLikeUser=$rowS['total'];
		   $fg_like=$rowS['fg_like'];
		  
		   if(!empty($totalLikeUser)){
			   
				#Eliminamos su like por que ya existe.
				$Query="DELETE FROM k_feed_likes WHERE fl_usuario=$fl_usuario AND fl_gallery_comment_sp_comment=$fl_post_feed AND fg_origen='$fg_origen' ";
				EjecutaQuery($Query);
				$fl_gallery_post=true;

				$fg_accion=2;	   
		   }else{
			   
			    #Inserta el like correspondiente al usuario y relacionado al post.
				$Query = "INSERT INTO k_feed_likes ";
				$Query .= "(fl_gallery_comment_sp_comment, fl_usuario,fg_origen, fe_alta,fg_like ) ";
				$Query .= "VALUES ($fl_post_feed,$fl_usuario,'$fg_origen',CURRENT_TIMESTAMP,'1')";
				$fl_gallery_post = EjecutaInsert($Query);
			   
				$fg_accion=1;
			   
			   
			   
			   
			   
		   }
		  
		  
		   
		  
		  
	  }
	  
		
				
		#Enviamos el email
		if(VerificaPermisoEnvioEmail($fl_usuario_comen,'fg_like_post')){
			# Obtenmos el template para decirle que alguien le dio like su post.
			$ds_header = genera_documento_sp('', 1, 168);
			$ds_body = genera_documento_sp('', 2, 168);
			$ds_footer = genera_documento_sp('', 3, 168);
			$dominio_campus = ObtenConfiguracion(116);
			$src_redireccion=$dominio_campus;#bueno
			
			$ruta_avatar_comment=ObtenAvatarUsuario($fl_usuario);

			
			$ds_mensaje=$ds_header.$ds_body.$ds_footer;
			$ds_mensaje = str_replace("#fame_fname#", $first_name, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_lname#", $last_name, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_fname_friends#", $fname_like, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_lname_friends#", $lname_like, $ds_mensaje);
		  
			$ds_mensaje = str_replace("#fame_ds_avatar_ori#", $dominio_campus.$ruta_avatar_comment, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_link#", $src_redireccion, $ds_mensaje);
		  
		 
			# Nombre del template
			$Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=168 AND fg_activo='1'";
			$template = RecuperaValor($Query0);
			$nb_template = str_uso_normal($template[0]);
			# Este email es necesario
			$from = ObtenConfiguracion(107);#de donde sale el email.
	  
 
			# Enviamos el correo al usuario dependiendo de la accion
			EnviaMailHTML($from, $from, $ds_email_destin, $nb_template, $ds_mensaje);
		
		}	
	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	
	
	}


	if(empty($fg_like_comentario)){#Para lolike del post orifgnal(1er nivel)

		$queryS="select count(1) as total, fg_like from c_feed_likes where fl_gallery_post_feed=$fl_post_feed and fg_origen='$fg_origen' and fl_usuario=$fl_usuario";
		$rowS=RecuperaValor($queryS);
		$totalLikeUser=$rowS['total'];
		$fg_like=$rowS['fg_like'];

        #fl_publicacion origina.
        $fl_publicacion=$fl_post_feed;

	   #Recuperamos el usuario orifginal del post.
	    $Query="SELECT  ds_nombres,ds_apaterno, fl_usuario,ds_email FROM c_usuario  
				WHERE fl_usuario=$fl_usuario_post_original ";
	   $rm=RecuperaValor($Query);
	   $first_name=str_texto(!empty($rm[0])?$rm[0]:NULL);
       $last_name=str_texto(!empty($rm[1])?$rm[1]:NULL);
	   $fl_usuario_comen=!empty($rm[2])?$rm[2]:NULL;
	   $ds_email_destin=!empty($rm[3])?$rm[3]:NULL;





		if(!empty($totalLikeUser)){

		 
		    $Query="DELETE FROM c_feed_likes WHERE fl_usuario=$fl_usuario AND fl_gallery_post_feed=$fl_post_feed AND fg_origen='$fg_origen' ";
		    EjecutaQuery($Query);
		    $fl_gallery_post=true;

		    $fg_accion=2;

		}else{
			
		#Inserta el like correspondiente al usuario y relacionado al post.
		$Query = "INSERT INTO c_feed_likes ";
		$Query .= "(fl_gallery_post_feed, fl_usuario,fg_origen, fe_alta,fg_like ) ";
		$Query .= "VALUES ($fl_post_feed,$fl_usuario,'$fg_origen',CURRENT_TIMESTAMP,'1')";
		$fl_gallery_post = EjecutaInsert($Query);
	   
		$fg_accion=1;
		
		
		
		
		
				#Enviamos el email
				if(VerificaPermisoEnvioEmail($fl_usuario_comen,'fg_like_post')){
					# Obtenmos el template para decirle que alguien le dio like su post.
					$ds_header = genera_documento_sp('', 1, 168);
					$ds_body = genera_documento_sp('', 2, 168);
					$ds_footer = genera_documento_sp('', 3, 168);
					$dominio_campus = ObtenConfiguracion(118);
					$src_redireccion=$dominio_campus;#bueno
					
					$ruta_avatar_comment=ObtenAvatarUsuario($fl_usuario);

					
					$ds_mensaje=$ds_header.$ds_body.$ds_footer;
					$ds_mensaje = str_replace("#fame_fname#", $first_name, $ds_mensaje);
					$ds_mensaje = str_replace("#fame_lname#", $last_name, $ds_mensaje);
					$ds_mensaje = str_replace("#fame_fname_friends#", $fname_like, $ds_mensaje);
					$ds_mensaje = str_replace("#fame_lname_friends#", $lname_like, $ds_mensaje);
				  
					$ds_mensaje = str_replace("#fame_ds_avatar_ori#", $dominio_campus.$ruta_avatar_comment, $ds_mensaje);
					$ds_mensaje = str_replace("#fame_link#", $src_redireccion, $ds_mensaje);
				  
				 
					# Nombre del template
					$Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=168 AND fg_activo='1'";
					$template = RecuperaValor($Query0);
					$nb_template = str_uso_normal($template[0]);
					# Este email es necesario
					$from = ObtenConfiguracion(107);#de donde sale el email.
			  
		 
					# Enviamos el correo al usuario dependiendo de la accion
					EnviaMailHTML($from, $from, $ds_email_destin, $nb_template, $ds_mensaje);
				
				}
		
		
		
		
		
		
		
		
		
		
		
	   
		}


		

	}




	# Check if the insert or update was successful
	if(empty($fl_gallery_post)){
		$error = array('error' => "Server Error. This post cannot be uploaded.");
		echo json_encode((Object)$error);
		exit;
	}

	echo json_encode((Object)array(
	'post' => $fl_post_feed,
	'origen'=>$fg_origen, 
	'fl_usuario'=>$fl_usuario,
	'fg_accion'=>$fg_accion,
	'fl_usuario_post_inicial'=>$fl_usuario_comen,
    'fl_publicacion_original'=>$fl_publicacion,
	'fg_likes'=>1
	));


?>







