<?php
 
	# community.php shows the list of users (teachers / students)
	require("../../common/lib/cam_general.inc.php");
    require("../../common/lib/cam_forum.inc.php");

    
	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario = ValidaSesion(False);

	# Verifica que el usuario tenga permiso de usar esta funcion
    if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
       MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
    }
 
 
	

	$fl_usuario_origen=RecibeParametroNumerico('fl_usuario_origen');
	$fl_usuario_destino=RecibeParametroHTML('fl_usuario_destino');
    $ds_mensaje=RecibeParametroHTML('ds_mensaje');
	
    
    
    #Recupermaos el prerfil del usuario de origen.
    $Query="SELECT fl_perfil FROM c_usuario WHERE fl_usuario=".$fl_usuario_origen ;
    $wp=RecuperaValor($Query);
    $fl_perfil=$wp['fl_perfil'];
    
    
    if($fl_perfil==PFL_ESTUDIANTE){
    
        #Recuperamos el nombre del usuario de origen
        $Query  = "SELECT a.ds_nombres, a.ds_apaterno, a.ds_email,a.fl_perfil,b.nb_perfil,p.ds_pais
               FROM c_usuario a 
               LEFT JOIN c_perfil b ON b.fl_perfil=a.fl_perfil
               JOIN c_alumno c ON c.fl_alumno=a.fl_usuario
               LEFT JOIN k_ses_app_frm_1 m ON m.cl_sesion=a.cl_sesion
               LEFT JOIN c_pais p ON p.fl_pais=m.ds_add_country				   
               WHERE fl_usuario=$fl_usuario_origen ";
        $row = RecuperaValor($Query);
     
    
    }
    
    if($fl_perfil==PFL_MAESTRO){
        
        #Recuperamos el nombre del usuario de origen
        $Query  = "SELECT a.ds_nombres, a.ds_apaterno, a.ds_email,a.fl_perfil,b.nb_perfil,p.ds_pais
                    FROM c_usuario a 
                    LEFT JOIN c_perfil b ON b.fl_perfil=a.fl_perfil
                    left JOIN c_maestro c ON c.fl_maestro=a.fl_usuario
                     LEFT JOIN c_pais p ON p.fl_pais=c.fl_pais				   
                    WHERE fl_usuario=$fl_usuario_origen ";
        $row = RecuperaValor($Query);
       
        
        
        
        
    }
    
    $ds_fname_origen = str_texto($row[0]);
    $ds_lname_origen = str_texto($row[1]);
    $ds_email_origen=str_texto($row['ds_email']);
    $fl_perfil=$row['fl_perfil'];
    $nb_perfil=str_texto($row['nb_perfil']);
	
    
    
	$ds_pais=str_texto($row['ds_pais']);
	$nb_usuario_origen=$ds_fname_origen." ".$ds_lname_origen;
	
    #Recupermos el avatar del usuario que envia invitacion.
    $Query="SELECT ds_ruta_avatar FROM c_alumno WHERE fl_alumno=$fl_usuario_origen ";
    $rim=RecuperaValor($Query);
    $img=str_texto($rim['ds_ruta_avatar']);
    
    if($img){
        
        $ds_ruta_avatar = "/modules/avatars/$img";
        
    }else{
        $ds_ruta_avatar="/images/".IMG_S_AVATAR_DEF;
    
    }
    
    $img="https://".ObtenConfiguracion(60)."/".$ds_ruta_avatar;
    $nb_instituto="Vancouver Animation School";
    
    
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
	
	
    # Prepare Email Template
    $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie,nb_template FROM k_template_doc WHERE fl_template=155 ";
    $board_template = RecuperaValor($Query);
    $ds_contenido = str_uso_normal($board_template[0].$board_template[1].$board_template[2]);
    $nb_template=str_texto($board_template[3]);

	$ds_contenido = str_replace("#fame_fname#", $ds_fname_destino, $ds_contenido); 
	$ds_contenido = str_replace("#fame_lname#", $ds_lname_destino, $ds_contenido); 
	
	$ds_contenido = str_replace("#fame_fname_friends#", $ds_fname_origen, $ds_contenido);
	$ds_contenido = str_replace("#fame_lname_friends#", $ds_lname_origen, $ds_contenido); 
    $ds_contenido=str_replace("#message_friends#",$ds_mensaje,$ds_contenido);	
    $ds_contenido=str_replace("#info_friends#",$info_friends,$ds_contenido);
   
   
	$img_friends="<img alt='' class='img-circle' src='".$img."' style='height:61px; width:75px float:left;' /> ";
	
	$ds_contenido=str_replace("#img_friends#",$img_friends,$ds_contenido);
	
	
    $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);  

	#Insertamos el envio de la notificcion
	$message  = $ds_contenido;
	$message = utf8_decode(str_ascii(str_uso_normal($message)));
	
    
    
   // $mail=SendNoticeMail($client, $from, $ds_comm_email, '', 'New Comment on VANAS Board', $ds_template_html);
    
    
    
    
	$nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje

	
	
	$ds_titulo=$nb_template;
	
	$bcc = ObtenConfiguracion(4);
	$mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destino, $ds_titulo, $message, $bcc);
   
    if($mail=1){


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


