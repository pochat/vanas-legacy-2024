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


$Query="SELECT ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario_origen ";
$ro=RecuperaValor($Query);
$name_usr_origen=str_texto($ro[0])." ".str_texto($ro[1]);
$fname_origen=str_texto($ro[0]);
$lname_origen=str_texto($ro[1]);

$Query2="SELECT ds_nombres,ds_apaterno,ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_destino ";
$ro2=RecuperaValor($Query2);
$name_usr_destino=str_texto($ro2[0])." ".str_texto($ro2[1]);
$first_name=str_texto($ro2[0]);
$last_name=str_texto($ro2[1]);
$ds_email_destin=str_texto($ro2[2]);

if($fg_accion==1){ #Es seguir a otro usuario.

   #Verificamos si no existe. 
   EjecutaQuery("DELETE FROM c_followers WHERE fl_usuario_origen=$fl_usuario_origen AND fl_usuario_destino=$fl_usuario_destino ");

	$Query="INSERT INTO c_followers(fl_usuario_origen,fl_usuario_destino,fe_creacion,fe_ultmod) 
			VALUES($fl_usuario_origen,$fl_usuario_destino,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
	$fl_followers=EjecutaInsert($Query);
	
	
	#Eviamos el email de notificacion.
	if(VerificaPermisoEnvioEmail($fl_usuario_destino,'fg_follow')){
	
	        # Obtenmos el template para enviar y decirleque que tiene nuevo seguidor.
			$ds_header = genera_documento_sp('', 1, 167);
			$ds_body = genera_documento_sp('', 2, 167);
			$ds_footer = genera_documento_sp('', 3, 167);
			$dominio_campus = ObtenConfiguracion(116);
			$src_redireccion=$dominio_campus;#bueno
			
			
			
			
			$ruta_avatar_comment=ObtenAvatarUsuario($fl_usuario_origen);

			
			$ds_mensaje=$ds_header.$ds_body.$ds_footer;
			$ds_mensaje = str_replace("#fame_fname#", $first_name, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_lname#", $last_name, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_fname_friends#", $fname_origen, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_lname_friends#", $lname_origen, $ds_mensaje);
		  
			$ds_mensaje = str_replace("#fame_ds_avatar_ori#", $dominio_campus.$ruta_avatar_comment, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_link#", $src_redireccion, $ds_mensaje);
		  
		 
			# Nombre del template
			$Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=167 AND fg_activo='1'";
			$template = RecuperaValor($Query0);
			$nb_template = str_uso_normal($template[0]);
			# Este email es necesario
			$from = ObtenConfiguracion(107);#de donde sale el email.
	  

			# Enviamos el correo al usuario dependiendo de la accion
			EnviaMailHTML($from, $from, $ds_email_destin, $nb_template, $ds_mensaje);
	
	
	}
	
	
	
	
	

    
	
	
	
	
}

if($fg_accion==2){ #Es para dejar de seguir al otro usuario.

	$Query="DELETE FROM c_followers WHERE fl_usuario_origen=$fl_usuario_origen AND fl_usuario_destino=$fl_usuario_destino ";
	EjecutaQuery($Query);
	$fl_followers=1;
}



	#Recuperamos los following. esta consulta es para profile.php personas que esta siguiendo
	$Query2="SELECT COUNT(*) FROM c_followers WHERE fl_usuario_origen=$fl_usuario ";
	$row2=RecuperaValor($Query2);
	$no_followed=$row2[0];
	
	
	#Recuperamos los followers persona que tiene como seguidores
    $Query="SELECT COUNT(*) FROM c_followers WHERE fl_usuario_destino=$fl_usuario ";
    $row=RecuperaValor($Query);
    $no_followers=$row[0];



# Check if the insert or update was successful
if(empty($fl_followers)){
    $error = array('error' => "Server Error. This follow cannot be uploaded.");
    echo json_encode((Object)$error);
    exit;
}

echo json_encode((Object)array('fg_correcto' => $fl_followers,"name_usr_origen"=> $name_usr_origen,"name_usr_destino"=>$name_usr_destino,"follower"=>$no_followers,"following"=>$no_followed ));
?>







