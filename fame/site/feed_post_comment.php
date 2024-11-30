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
$fl_post_feed=RecibeParametroNumerico('item');//fl post_padre, primer post
$ds_comment = RecibeParametroHTML('comentario');
$fame = RecibeParametroNumerico('fame');


$nb_nombre_user_comentando=ObtenNombreUsuario($fl_usuario);


if(empty($fl_post_feed)){
    $error = array('error' => "Server Error. Unknown post.");
    echo json_encode((Object)$error);
    exit;
}
if(!empty($ds_comment)) {

    # Comentarios
    $ds_comment = rawurldecode($ds_comment);
    $ds_comment = PorcesaCadena($ds_comment);

    if ($fg_origen == 'g') {


        $Query="SELECT fg_ayuda,fl_usuario FROM  k_gallery_post_sp WHERE  fl_gallery_post_sp=$fl_post_feed  ";
        $rop=RecuperaValor($Query);
        $fg_post_ayuda=$rop['fg_ayuda'];
        $fl_usuario_post_origen=$rop['fl_usuario'];

        $Query = "INSERT INTO k_gallery_comment_sp ";
        $Query .= "(fl_gallery_post_sp, fl_usuario, ds_comment,fe_comment, fg_read ) ";
        $Query .= "VALUES ($fl_post_feed,$fl_usuario,'$ds_comment', now(),'0')";
        $fl_gallery_post = EjecutaInsert($Query);

        #Recuperamos todos usuarios involucarados para enviar email de comentarios.
        $Quercom="SELECT DISTINCT fl_usuario FROM k_gallery_comment_sp WHERE fl_gallery_post_sp=$fl_post_feed AND fl_usuario<>$fl_usuario ";


    }
    if ($fg_origen == 'p') {


        $Query="SELECT fg_ayuda,fl_usuario FROM c_feed_publicaciones WHERE fl_publicacion=$fl_post_feed  ";
        $rop=RecuperaValor($Query);
        $fg_post_ayuda=$rop['fg_ayuda'];
        $fl_usuario_post_origen=$rop['fl_usuario'];

        $Query = "INSERT INTO k_feed_comment ";
        $Query .= "(fl_publicacion, fl_usuario, ds_comment, fg_read, fe_alta ) ";
        $Query .= "VALUES ($fl_post_feed, $fl_usuario, '$ds_comment', '0', now())";
        $fl_gallery_post = EjecutaInsert($Query);

        #Recuperamos todos usuarios involucarados para enviar email de comentarios.
        $Quercom="SELECT DISTINCT fl_usuario FROM k_feed_comment WHERE fl_publicacion=$fl_post_feed AND fl_usuario<>$fl_usuario ";


    }

    $avatar =ObtenAvatarUsuario($fl_usuario);
    # Check if the insert or update was successful
    if (empty($fl_gallery_post)) {
        $error = array('error' => "Server Error. This post cannot be uploaded.");
        echo json_encode((Object)$error);
        exit;
    }else{
			
		
        #Enviamos la notificacion de email al usuario original del post.
        #Datos del usuario original del post
        $Query="SELECT ds_email,ds_nombres,ds_apaterno,ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_post_origen ";
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
        
        #Buscanos los usuario involucardos sobre este post enviamos el email.
        
        
        //mandamos el email solo cuando sea otro usuario el que repsonde. 	
        if($fl_usuario_post_origen<>$fl_usuario){
            
            if(VerificaPermisoEnvioEmail($fl_usuario_post_origen,'fg_coment_post')){
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
                $ds_mensaje = str_replace("#fame_comment_feed#", substr($ds_comment,0,10), $ds_mensaje);

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


        $rs_data=EjecutaQuery($Quercom);
        $emails_enviados=array();
        for($i=0;$rowdata=RecuperaRegistro($rs_data);$i++) {
            $fl_usuario_post_involucrados = $rowdata[0];


		    #Enviamos la notificacion de email al usuario original del post.
		    #Datos del usuario original del post
		    $Query="SELECT ds_email,ds_nombres,ds_apaterno,ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_post_involucrados ";
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
            
            #Buscanos los usuario involucardos sobre este post enviamos el email.
            
            
		    //mandamos el email solo cuando sea otro usuario el que repsonde. 	
		    if($fl_usuario_post_involucrados<>$fl_usuario){
                
			    if(VerificaPermisoEnvioEmail($fl_usuario_post_involucrados,'fg_coment_post')){
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
                    $ds_mensaje = str_replace("#fame_comment_feed#", substr($ds_comment,0,10), $ds_mensaje);

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


                    $emails_enviados["emails".$i] = array(
				        "fl_usuario_destino" => $fl_usuario_post_involucrados    
                        ); 

                    $Query ="INSERT INTO k_feed_comentarios (fl_publicacion,fl_usuario_sp,fl_usuario_destino,ds_comment,fe_alta,fg_origen)";
                    $Query.="VALUES( $fl_post_feed,$fl_usuario,$fl_usuario_post_involucrados,'$ds_comment', CURRENT_TIMESTAMP,'$fg_origen')";
                    EjecutaQuery($Query);




                    
			    }

            }
            
        }#end for de todos los usuarios involucrados.
		
		
	}


}

echo json_encode((Object)
array(
'fl_gallery_post'=>$fl_post_feed,
'fl_usuario_post_origen'=>$fl_usuario_post_origen,
'fg_post_ayuda'=>$fg_post_ayuda,
'post' => $fl_gallery_post,
'fl_usuario_esta_comentanto'=>$fl_usuario,
'origen'=>$fg_origen,
'emails_enviados'=>$emails_enviados,
'avatar'=>$avatar,
'nombre_user_comentando'=>$nb_nombre_user_comentando));

?>







