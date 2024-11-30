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
$fl_comentari=RecibeParametroNumerico('fl_comentari');
$ds_comment = RecibeParametroHTML('comentario');
$fame = RecibeParametroNumerico('fame');
$fl_usuario_post_que_se_esta_comentando=RecibeParametroNumerico('fl_usuario_post_que_se_esta_comentando');


$nb_nombre_user_comentando=ObtenNombreUsuario($fl_usuario);


if(empty($fl_comentari)){
    $error = array('error' => "Server Error. Unknown post.");
    echo json_encode((Object)$error);
    exit;
}
if(!empty($ds_comment)) {

    # Comentarios
    $ds_comment = rawurldecode($ds_comment);
    $ds_comment = PorcesaCadena($ds_comment);

    if ($fg_origen == 'g') {

        $Query = "INSERT INTO k_gallery_comment_sp_comment ";
        $Query .= "(fl_gallery_comment, fl_usuario, ds_comment,fe_comment, fg_read ) ";
        $Query .= "VALUES ($fl_comentari,$fl_usuario,'$ds_comment', now(),'0')";
        $fl_gallery_comment = EjecutaInsert($Query);
		
		
    }
    if ($fg_origen == 'p') {

        $Query = "INSERT INTO k_feed_comment_comment ";
        $Query .= "(fl_feed_comment, fl_usuario, ds_comment, fg_read, fe_alta ) ";
        $Query .= "VALUES ($fl_comentari, $fl_usuario, '$ds_comment', '0', now())";
        $fl_gallery_comment = EjecutaInsert($Query);
    }

    $avatar =ObtenAvatarUsuario($fl_usuario);
    $nb_nombre_user_comentando=ObtenNombreUsuario($fl_usuario);
    # Check if the insert or update was successful
    if (empty($fl_gallery_comment)) {
        $error = array('error' => "Server Error. This post cannot be uploaded.");
        echo json_encode((Object)$error);
        exit;
    }
	 
	//mandamos el email solo cuando sea otro usuario el que repsonde. 	
	if($fl_usuario_post_que_se_esta_comentando<>$fl_usuario){
		

			if(VerificaPermisoEnvioEmail($fl_usuario_post_que_se_esta_comentando,'fg_coment_post')){


				#Datos del usuario original del post
				$Query="SELECT ds_email,ds_nombres,ds_apaterno,ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_post_que_se_esta_comentando ";
				$ro=RecuperaValor($Query);
				$ds_email_destin=$ro['ds_email'];
				$first_name=str_texto($ro[1]);
				$last_name=str_texto($ro[2]);
				$ds_email_destin=str_texto($ro[3]);

				#Datos del usuario que esta comentando
				$Query1="SELECT ds_email,ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario ";
				$rou=RecuperaValor($Query1);
				$ds_email_comment=$rou['ds_email'];
				$fname_comment=str_texto($rou[1]);
				$lname_comment=str_texto($rou[2]);



				# Obtenmos el template para decirle que alguien comento su post.
				$ds_header = genera_documento_sp('', 1, 172);
				$ds_body = genera_documento_sp('', 2, 172);
				$ds_footer = genera_documento_sp('', 3, 172);
				$dominio_campus = ObtenConfiguracion(116);
				$src_redireccion=$dominio_campus;#bueno
				
				$ruta_avatar_comment=ObtenAvatarUsuario($fl_usuario);

				
				$ds_mensaje=$ds_header.$ds_body.$ds_footer;
				$ds_mensaje = str_replace("#fame_fname#", $first_name, $ds_mensaje);
				$ds_mensaje = str_replace("#fame_lname#", $last_name, $ds_mensaje);
				$ds_mensaje = str_replace("#fame_fname_friends#", $fname_comment, $ds_mensaje);
				$ds_mensaje = str_replace("#fame_lname_friends#", $lname_comment, $ds_mensaje);
			  
				$ds_mensaje = str_replace("#fame_ds_avatar_ori#", $dominio_campus.$ruta_avatar_comment, $ds_mensaje);
				$ds_mensaje = str_replace("#fame_link#", $src_redireccion, $ds_mensaje);
			  
			 
				# Nombre del template
				$Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=172 AND fg_activo='1'";
				$template = RecuperaValor($Query0);
				$nb_template = str_uso_normal($template[0]);
				# Este email es necesario
				$from = ObtenConfiguracion(107);#de donde sale el email.
		  
	 
				# Enviamos el correo al usuario dependiendo de la accion
				EnviaMailHTML($from, $from, $ds_email_destin, $nb_template, $ds_mensaje);
				
			}

			



	}
	
	
	
	
	
	
	


}
echo json_encode((Object)array(
    'fl_comentario' => $fl_comentari, 
    'fl_comentario_new'=>$fl_gallery_comment, 
    'origen'=>$fg_origen,
    'fl_usuario_comentando'=>$fl_usuario,
    'nb_nombre_user_comentando'=>$nb_nombre_user_comentando,
    'avatar'=>$avatar));

?>







