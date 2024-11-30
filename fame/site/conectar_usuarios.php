<?php
 
	# Libreria de funciones	
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario = ValidaSesion(False,0, True);

	# Verifica que el usuario tenga permiso de usar esta funcion
	if(!ValidaPermisoSelf(FUNC_SELF)) {  
	MuestraPaginaError(ERR_SIN_PERMISO);
	exit;
	}
 
	$fl_instituto=ObtenInstituto($fl_usuario);

	$fl_usuario_origen=RecibeParametroNumerico('fl_usuario_origen');
	$fl_usuario_destino=RecibeParametroHTML('fl_usuario_destino');
    $ds_mensaje=RecibeParametroHTML('ds_mensaje');
	
	#Recuperamos el nombre del usuario de origen
	$Query  = "SELECT a.ds_nombres, a.ds_apaterno, a.ds_email,a.fl_perfil_sp,b.nb_perfil,i.ds_instituto,p.ds_pais 
               FROM c_usuario a 
               LEFT JOIN c_perfil b ON b.fl_perfil=a.fl_perfil_sp
               LEFT JOIN c_instituto i ON i.fl_instituto=a.fl_instituto 
               LEFT JOIN k_usu_direccion_sp d ON d.fl_usuario_sp=a.fl_usuario 
               LEFT JOIN c_pais p ON p.fl_pais=d.fl_pais			   
               WHERE fl_usuario=$fl_usuario_origen ";
	$row = RecuperaValor($Query);
	$ds_fname_origen = str_texto($row[0]);
	$ds_lname_origen = str_texto($row[1]);
	$ds_email_origen=str_texto($row[2]);
    $fl_perfil_sp=$row[3];
    $nb_perfil=str_texto($row[4]);
    $nb_instituto=str_texto($row[5]);
	$ds_pais=str_texto($row[6]);
	$nb_usuario_origen=$ds_fname_origen." ".$ds_lname_origen;
	
	
	
    $img=ObtenConfiguracion(116).ObtenAvatarUsuario($fl_usuario_origen);
	#https://go.myfame.org/fame/site/uploads/4/USER_1085/avatar_1085_19706.jpg
    $info_friends="<strong>$ds_fname_origen $ds_lname_origen</strong>
                    <br/><span class='text-muted '><i class='fa fa-user-o' aria-hidden='true'></i> ".$nb_perfil."</span>
                    <br/><span class='text-muted '><i class='fa fa-institution' aria-hidden='true'></i> ".$nb_instituto." </span>

                    ";
	if($ds_pais)
    $info_friends.="<br/><span class='text-muted'><i class='fa fa-globe' aria-hidden='true'></i> ".$ds_pais."</span>";					
    
    
    
	
	#Recupermaos el usuario de destino.
	$Query  = "SELECT ds_nombres, ds_apaterno,ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_destino ";
	$row = RecuperaValor($Query);
	$ds_fname_destino = str_texto($row[0]);
	$ds_lname_destino = str_texto($row[1]);	
    $ds_email_destino=str_texto($row[2]);
	
	
	
	#se genera el cuerpo del documento de email
	$ds_encabezado = genera_documento_sp('', 1,154);
	$ds_cuerpo = genera_documento_sp('', 2,154);
	$ds_pie = genera_documento_sp('', 3,154);

	$ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;

	$ds_contenido = str_replace("#fame_fname#", $ds_fname_destino, $ds_contenido); 
	$ds_contenido = str_replace("#fame_lname#", $ds_lname_destino, $ds_contenido); 
	
	$ds_contenido = str_replace("#fame_fname_friends#", $ds_fname_origen, $ds_contenido);
	$ds_contenido = str_replace("#fame_lname_friends#", $ds_lname_origen, $ds_contenido); 
    $ds_contenido=str_replace("#message_friends#",$ds_mensaje,$ds_contenido);	
    $ds_contenido=str_replace("#info_friends#",$info_friends,$ds_contenido);
   
   
	$img_friends="<img alt='' class='img-circle' src='".$img."' style='height:61px; width:75px float:left;' /> ";
	
	$ds_contenido=str_replace("#img_friends#",$img_friends,$ds_contenido);
	
	
    $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(107);  

	#Insertamos el envio de la notificcion
	$message  = $ds_contenido;
	$message = utf8_decode(str_ascii(str_uso_normal($message)));
	
	$nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje

	
	
	$ds_titulo=FAME_ObtenAsuntoEmail(154);
	
	$bcc = ObtenConfiguracion(107);
	$mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destino, $ds_titulo, $message, $bcc);
   
    if($mail){


	$Query ="INSERT INTO k_relacion_usuarios (fl_usuario_origen,fl_usuario_destinatario,ds_mensaje,fe_creacion,fe_ulmod,fg_enviado,fg_aceptado,fg_rechazado )";
	$Query.="VALUES($fl_usuario_origen, $fl_usuario_destino,'$ds_mensaje', CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'1','0','0')";
	$fl_registro=EjecutaInsert($Query);

	
    #Eviamosel mensaje por el chat.
    $ds_mensaje = PorcesaCadena($ds_mensaje);
    $ds_mensaje = str_replace("&lt;!", "&#60;!", $ds_mensaje);   // html comment
    $ds_mensaje = str_replace("&lt;?", "&#60;?", $ds_mensaje);   // html comment

    $Query  = "INSERT INTO k_mensaje_directo (fl_usuario_ori, fl_usuario_dest, ds_mensaje) ";
    $Query .= "VALUES($fl_usuario_origen, $fl_usuario_destino, '$ds_mensaje')";
    $fl_mensaje_directo = EjecutaInsert($Query);
    
    
    
	
		$result = array(
		  "status" => 1,
		  "error"=>0,
		  "nb_usuario_origen"=>$nb_usuario_origen,
		  "fg_tipo" => 'Enviado'
		);

	}else{
	
		$result= array(
		  "status" => 404,
		  "error"=>1,
		  "fg_tipo" => 'Error'
		);
	
	
	}
	
	
  
  
   echo json_encode(($result));
  
  ?>


