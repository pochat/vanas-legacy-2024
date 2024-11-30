<?php
  # Functions libraries
  require("../../lib/self_general.php");
  
  # Session validation
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Get parameters
  $all_comments = RecibeParametroNumerico('all_comments');
  $fg_read_r = RecibeParametroNumerico('fg_read_r');
  $fl_gallery_comment_sp = RecibeParametroNumerico('fl_gallery_comment_sp');
  $fg_marcar_mensaje_chat=RecibeParametroNumerico('fg_mesaje_leido');
  
  #Parameters of assigment to grade
  $fl_entrega_semanal_sp=RecibeParametroNumerico('fl_entrega_semanal_sp');
  $fg_confirmado_amigo=RecibeParametroHTML('fg_confirmado_amigo'); 
  $fl_user_origen=RecibeParametroNumerico('fl_user_origen');
  
  
  #Parameters feed.
  $fg_feed=RecibeParametroNumerico('fg_feed');
  $fg_origen=RecibeParametroHTML('fg_origen');


  if($fg_marcar_mensaje_chat==1){
      $fl_mensaje_directo=$fl_gallery_comment_sp;
      $fl_confirmacion_email_curso=null;
      $fl_entrega_semanal_sp=null;
      
  }

 
  # Funcion para obtener la imagen del post
  function img_usr($fl_usuario_ori, $nb_archivo, $programa){
	$ext = ObtenExtensionArchivo($nb_archivo);
	$fl_instituto = ObtenInstituto($fl_usuario_ori);
	if(empty($programa))
	  if($ext=="jpg" || $ext=="jpeg" || $ext=="png" || $ext=="PNG")
		$ruta = "<img src='".PATH_SELF_UPLOADS."/gallery/thumbs/".$nb_archivo."' alt='' class='air margin-top-5' height='40' width='40' />";
	  else
		$ruta = "<i class='fa fa-video-camera fa-2x' style='padding-left:25px;'></i>";
	else{
	  if($ext=="jpg" || $ext=="jpeg" || $ext=="png" || $ext=="PNG")
		$ruta = "<img src='".PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_usuario_ori."/sketches/thumbs/".$nb_archivo."' alt='' class='air margin-top-5' height='40' width='40' />";
	  else
		$ruta = "<i class='fa fa-video-camera fa-2x' style='padding-left:25px;'></i>";
	}
	return $ruta;
  }



  #Parameyros de confirmaciones de email.
  $fg_confirmacion_email=RecibeParametroHTML('fg_confirmacion_email'); 
  if($fg_confirmacion_email==1){
  
    $fl_confirmacion_email_curso=$fl_gallery_comment_sp;
    $fl_entrega_semanal_sp=null;
    $fl_gallery_comment_sp=null;
  }
  if($fg_confirmacion_email==2){
     $fl_usu_pro=$fl_gallery_comment_sp;
	 $fl_entrega_semanal_sp=null;
     $fl_gallery_comment_sp=null;
  
  }
 
  
  
  if($fg_confirmado_amigo){
  
    $fl_usuario_acepto_invitacion=$fl_gallery_comment_sp;
  
    #USER_
	$Querym="UPDATE k_relacion_usuarios SET fg_revisado_alumno='1' 
			 WHERE fl_usuario_origen=$fl_usuario AND fl_usuario_destinatario=$fl_usuario_acepto_invitacion ";
		     EjecutaQuery($Querym);
	
  }
  
  
  
  
  if($fl_entrega_semanal_sp){
  
			       # Update comment on read or unread
					if(empty($fg_read_r)){
					  $fg_read = 0;
					  #Update read k_entrega_semanal_sp
					  $Query="UPDATE k_entrega_semanal_sp SET fg_revisado_alumno='$fg_read' WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp ";
					  EjecutaQuery($Query);
					  $result['read_unread'] = "<small  onclick='MarkGrade(1,".$fl_entrega_semanal_sp.");'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1833)."</small>";      
					}
					else{
					  $fg_read = 1;    
					    #Update read k_entrega_semanal_sp
						$Query="UPDATE k_entrega_semanal_sp SET fg_revisado_alumno='$fg_read' WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp ";
						EjecutaQuery($Query);
					    $result['read_unread'] = "<small class='text-primary' onclick='MarkGrade(0,".$fl_entrega_semanal_sp.");'><i class='fa fa-circle-o'></i>  ".ObtenEtiqueta(1832)."</small>";      
					}
  
					
  
  
  }


  
  
if($fg_confirmacion_email){
	
	if($fg_confirmacion_email==1){
	
			#Marca como leido em mensaje de confirmacion de email.
			$Query="UPDATE k_confirmacion_email_curso SET fg_revisado_alumno='1' WHERE fl_confirmacion_email_curso=$fl_confirmacion_email_curso ";
			EjecutaQuery($Query);
		 
	}
	if($fg_confirmacion_email==2){
		    #Marca como leido la signacion del programa.
        $Query="UPDATE k_usuario_programa SET fg_revisado_alumno='1' WHERE fl_usu_pro=$fl_usu_pro ";
			EjecutaQuery($Query);
		
	}
	
	
	
	
}

  
if(empty($fg_marcar_mensaje_chat)){  
  
    # All comments or a comment
    if(empty($all_comments)){
            # Update comment on read or unread
            if(empty($fg_read_r)){
	            $fg_read = 1;
	            EjecutaQuery("UPDATE k_usu_notify SET no_notice=no_notice-1 WHERE fl_usuario=".$fl_usuario);
	            $result['read_unread'] = "<small  onclick='Mark(0,1,".$fl_gallery_comment_sp.");'><i class='fa fa-circle-o'></i> ".ObtenEtiqueta(1833)."</small>";      
            }
            else{
	            $fg_read = 0;    
	            EjecutaQuery("UPDATE k_usu_notify SET no_notice=no_notice+1 WHERE fl_usuario=".$fl_usuario);
	            $result['read_unread'] = "<small class='text-primary' onclick='Mark(0,0,".$fl_gallery_comment_sp.");'><i class='fa fa-circle'></i>  ".ObtenEtiqueta(1832)."</small>";      
            }
					
            $Query = "UPDATE k_gallery_comment_sp SET fg_read='".$fg_read."' WHERE fl_gallery_comment_sp=".$fl_gallery_comment_sp." ";
            EjecutaQuery($Query);


            #Recuperamos los totales de mensajy 
            $row = RecuperaValor("SELECT COUNT(1) FROM k_mensaje_directo WHERE fl_usuario_dest=$fl_usuario  AND fg_leido='0'");
            $total_mensajes=$row[0];
            if(empty($total_mensajes))
                $total_mensajes=0;


            #Recuperamos el total de grading.
            $QueryG="SELECT COUNT(*) FROM k_entrega_semanal_sp  a WHERE a.fl_alumno=$fl_usuario  and fg_revisado_alumno='0' and fl_promedio_semana is not null ; ";  
            $rowg=RecuperaValor($QueryG);
            $no_assigment_grade = $rowg[0];
            if(empty($total_mensajes))
                $no_assigment_grade=0;

			#Aceptacion de solicitudes.
			$Que="UPDATE  k_confirmacion_email_curso SET fg_revisado_alumno='1' WHERE fl_alumno_beneficiado=$fl_usuario AND fg_revisado_alumno='0'";
			EjecutaQuery($Que);
			
			#Cursos calificados
			$Qur="UPDATE k_usuario_programa SET fg_revisado_alumno='1'  WHERE fl_usuario_sp=$fl_usuario AND fg_revisado_alumno='0' ";
			EjecutaQuery($Qur);

			$Qo="UPDATE k_relacion_usuarios SET fg_aceptado='1'  WHERE fl_usuario_destinatario=".$fl_usuario." AND fg_aceptado='0'  ";
			EjecutaQuery($Qo);
			
			$qu="UPDATE k_relacion_usuarios SET fg_revisado_alumno='1' WHERE fl_usuario_origen=$fl_usuario AND fg_aceptado='1' AND fg_revisado_alumno='0' ";
			EjecutaQuery($qu);

            $no_total_notifi= $total_mensajes + $no_assigment_grade;
            $result['no_total_notifi']=$no_total_notifi;





    }else{# Read all comments

    # Search  comments
    $Query3="UPDATE k_gallery_comment_sp a 
            JOIN k_gallery_post_sp b ON b.fl_gallery_post_sp=a.fl_gallery_post_sp   
            SET a.fg_read='1'  WHERE a.fl_usuario<>$fl_usuario AND b.fl_usuario=$fl_usuario AND a.fg_read='0'
    
    ";
    EjecutaQuery($Query3);
    EjecutaQuery("UPDATE k_usu_notify SET no_notice=0 WHERE fl_usuario=".$fl_usuario);
    $result['all_comments'] = $notice; 
    $result['all_comments_total'] = $tot_cometarios; 
    
    #Para los mensajes directos messages.chat. 
    EjecutaQuery("UPDATE k_mensaje_directo SET fg_leido='1'  WHERE  fl_usuario_dest=$fl_usuario AND fg_leido='0'  ");

	#Aceptacion de solicitudes.
	$Que="UPDATE  k_confirmacion_email_curso SET fg_revisado_alumno='1' WHERE fl_alumno_beneficiado=$fl_usuario AND fg_revisado_alumno='0'";
	EjecutaQuery($Que);
	
	#Cursos calificados
	$Qur="UPDATE k_usuario_programa SET fg_revisado_alumno='1'  WHERE fl_usuario_sp=$fl_usuario ";
	EjecutaQuery($Qur);

	$Qo="UPDATE k_relacion_usuarios SET fg_aceptado='1'  WHERE fl_usuario_destinatario=".$fl_usuario." AND fg_aceptado='0'  ";
	EjecutaQuery($Qo);
	
	$qu="UPDATE k_relacion_usuarios SET fg_revisado_alumno='1' WHERE fl_usuario_origen=$fl_usuario AND fg_aceptado='1' AND fg_revisado_alumno='0' ";
	EjecutaQuery($qu);
	
    #grading.
    EjecutaQuery("UPDATE k_entrega_semanal_sp  SET fg_revisado_alumno='1'  WHERE fl_alumno=$fl_usuario ");

	
	#Followers.
	EjecutaQuery("UPDATE c_followers SET fg_revisado='1' WHERE   fl_usuario_destino=$fl_usuario ");
	
    EjecutaQuery("UPDATE k_gallery_comment_sp a
	                    JOIN k_gallery_post_sp b ON a.fl_gallery_post_sp=b.fl_gallery_post_sp  SET a.fg_read='1' WHERE  b.fl_usuario=$fl_usuario  ");

	#Likes. 
	EjecutaQuery("UPDATE c_feed_likes a
		            JOIN c_feed_publicaciones b ON a.fl_gallery_post_feed=b.fl_publicacion
		            JOIN k_gallery_post_sp c ON c.fl_gallery_post_sp=a.fl_gallery_post_feed 	
		            SET fg_revisado='1'
		            WHERE ( b.fl_usuario=$fl_usuario OR c.fl_usuario=$fl_usuario )
		            AND a.fl_usuario<>$fl_usuario AND fg_revisado='0' ");
    #Comentarios feed.
	EjecutaQuery("UPDATE  
                k_feed_comment a
                JOIN c_feed_publicaciones b ON a.fl_publicacion=b.fl_publicacion
                SET fg_revisado='1'
                WHERE b.fl_usuario=$fl_usuario
                AND a.fl_usuario<>$fl_usuario AND a.fg_revisado='0' ");
	EjecutaQuery("UPDATE k_gallery_comment_sp a 
					JOIN k_gallery_post_sp b ON b.fl_gallery_post_sp=a.fl_gallery_post_sp
                    SET fg_read='1',fg_revisado='1'   
					WHERE b.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND a.fg_read='0' AND b.fg_ayuda='0' ");


    #Comentarios 3r nivel 7
    EjecutaQuery("
             UPDATE 
             k_feed_comment_comment b
             JOIN k_feed_comment c ON c.fl_feed_comment=b.fl_feed_comment 
             JOIN c_feed_publicaciones d ON d.fl_publicacion=c.fl_publicacion 
             SET c.fg_revisado='1',b.fg_read='1'      
             WHERE c.fl_usuario=$fl_usuario AND c.fg_revisado='0' AND b.fl_usuario<>$fl_usuario ");
    #Likes 2 nivel.
    EjecutaQuery("
            UPDATE   
            k_feed_likes a
            JOIN k_feed_comment b ON a.fl_gallery_comment_sp=b.fl_feed_comment
            JOIN c_feed_publicaciones c ON c.fl_publicacion=b.fl_publicacion
            SET a.fg_revisado='1' 
            WHERE c.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND a.fl_usuario<>$fl_usuario  
        
        ");
    #Like 3 nivel
    EjecutaQuery("
            UPDATE  
            k_feed_likes a
            JOIN k_feed_comment_comment b ON a.fl_gallery_comment_sp_comment=b.fl_feed_comment_comment 
            JOIN k_feed_comment c ON c.fl_feed_comment=b.fl_feed_comment
            JOIN c_feed_publicaciones d ON d.fl_publicacion=c.fl_publicacion
            SET a.fg_revisado='1'
            WHERE c.fl_usuario=$fl_usuario AND a.fl_usuario<>$fl_usuario AND a.fg_revisado='0' 
        ");

	#Respuestas Correctas
	EjecutaQuery("UPDATE k_feed_comment  SET fg_revisado='1' WHERE fl_usuario=$fl_usuario AND  fg_correcto='1' ");

    #Request accses course
    EjecutaQuery("UPDATE k_request_access_course SET fg_revisado='1' WHERE fl_maestro_sp=$fl_usuario ");

    EjecutaQuery("UPDATE k_feed_comentarios SET fg_revisado='1' WHERE fl_usuario_destino=$fl_usuario ");

    #requets_access denegados.
    EjecutaQuery("delete FROM k_request_access_course WHERE fl_usuario_sp=$fl_usuario AND fg_denegado='1' ");


    $no_total_notifi= 0;
    $result['no_total_notifi']=$no_total_notifi;


    }
}

        #Marcamos como leido los menjase del chat.
        if($fg_marcar_mensaje_chat){

            $Queryy  = "SELECT  fl_usuario_ori,fl_mensaje_directo ";
            $Queryy .= "FROM k_mensaje_directo ";
            $Queryy .= "WHERE fl_usuario_ori=$fl_user_origen AND fl_usuario_dest=$fl_usuario AND fg_leido='0' ";
            $rs = EjecutaQuery($Queryy);
            $no_registro=CuentaRegistros($rs);
            for($i=0; $row = RecuperaRegistro($rs); $i++) {

                $fl_mensaje_directo=$row['fl_mensaje_directo'];

                $Query="UPDATE k_mensaje_directo SET fg_leido='1' WHERE fl_mensaje_directo=$fl_mensaje_directo ";
                EjecutaQuery($Query);

            }


            $result['fg_chat']=1;
            $result['no_mensajes']=$no_registro;


        }

##############para la notificaciones del feed#####################

if($fg_feed==1){
	EjecutaQuery("UPDATE c_followers SET fg_revisado='1' WHERE   fl_usuario_destino=$fl_usuario AND fl_usuario_origen=".$fl_user_origen);
}
if($fg_feed==2){
	$fl_post=$fl_gallery_comment_sp;		
    EjecutaQuery("UPDATE c_feed_likes SET fg_revisado='1' WHERE   fl_gallery_post_feed=$fl_post AND fg_origen='$fg_origen' AND fl_usuario=".$fl_user_origen );
}

//Comentarios

if($fg_feed==3){
	
	$fl_post=$fl_gallery_comment_sp;
	
	//del feed.
	if($fg_origen=='p'){		
    	 EjecutaQuery("UPDATE k_feed_comment SET fg_revisado='1' WHERE   fl_feed_comment=$fl_post ");	
	}
	//de las tareas
	if($fg_origen=='g'){		
		 EjecutaQuery("UPDATE k_gallery_comment_sp SET fg_revisado='1' WHERE   fl_gallery_comment_sp=$fl_post ");	
	}

}


#Marca como leido el request access
if($fg_origen==7){
    
    EjecutaQuery("UPDATE k_request_access_course SET fg_revisado='1' WHERE fl_request_access=$fl_gallery_comment_sp  ");
    $result['read_unread'] = "";      




}



  echo json_encode((Object) $result);
?>