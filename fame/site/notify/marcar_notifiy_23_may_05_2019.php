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

    }else{# Read all comments

    # Search  comments
    $Query3  = "SELECT b.fl_gallery_post_sp, a.fl_gallery_comment_sp, a.fg_read, a.fl_usuario, DATE_FORMAT(a.fe_comment , '%Y-%m-%d %H:%i:%s'), ";
    $Query3 .= "a.ds_comment, b.nb_archivo, c.nb_programa, b.fl_usuario ";
    $Query3 .= "FROM k_gallery_comment_sp a ";
    $Query3 .= "JOIN k_gallery_post_sp b ON(a.fl_gallery_post_sp=b.fl_gallery_post_sp) ";
    $Query3 .= "LEFT JOIN c_programa_sp c ON(b.fl_programa_sp=c.fl_programa_sp OR b.fl_programa_sp IS NULL) ";
    $Query3 .= "WHERE a.fl_gallery_post_sp IN(SELECT DISTINCT fl_gallery_post_sp fl_gallery_post_sp_comentado FROM k_gallery_comment_sp WHERE fl_usuario=$fl_usuario) ";
    $Query3 .= "AND a.fl_usuario<>$fl_usuario OR b.fl_usuario=$fl_usuario ";
    $Query3 .= "ORDER BY a.fe_comment DESC ";
    $rs = EjecutaQuery($Query3);
    $notice = "";
    $tot_cometarios = 0;
    for($i=0;$row=RecuperaRegistro($rs);$i++){
	    $fl_gallery_post_sp = $row[0];
	    $fl_gallery_comment_sp = $row[1];
	    $fg_read = $row[2];
	    $fl_usuario_comento = $row[3];
	    # Update comment to read
	    EjecutaQuery("UPDATE k_gallery_comment_sp SET fg_read='1' WHERE fl_gallery_comment_sp=".$fl_gallery_comment_sp." ");
					  
	    # Obtenemos el avatar del usuario que comento
	    $avatar = ObtenAvatarUsuario($fl_usuario_comento);
	    $ds_user_comento = ObtenNombreUsuario($fl_usuario_comento);
	    $fe_comment = $row[4];
	    # Obtenemos el tiempo
	    $time = time_elapsed_string($fe_comment);
	    $ds_comment = $row[5];
	    $nb_programa = $row[7];
	    $nb_archivo = img_usr($row[8], $row[6], $nb_programa);
					  
	    $notice = $notice.
	    "<li><span id='li_".$fl_gallery_comment_sp."'>
	    <a href='javascript:view_post(".$fl_gallery_post_sp.", ".$fl_gallery_comment_sp.")' style='padding-left:50px !important'>
		    <img src='".$avatar."' alt='' class='air air-top-left margin-top-5' height='40' width='40' />
		    <span class='from'>".$ds_user_comento."</span>
		    <time>".$time."</time>
		    <div class='col-sm-9 no-padding'>
		    <span class='msg-body'>".$ds_comment."</span></div>
		    <div class='col-sm-1 text-align-center'>".$nb_archivo."</div>
	    </a>
	    <div class='cursor-pointer text-align-right' id='noti_".$fl_gallery_comment_sp."'><small onclick='Mark(0,1,".$fl_gallery_comment_sp.");'><i class='fa fa-circle-o'></i> ".ObtenEtiqueta(1833)."</small></div>
	    </span></li>";
	    if($fg_read==0)
	    $tot_cometarios++;
    }
    EjecutaQuery("UPDATE k_usu_notify SET no_notice=0 WHERE fl_usuario=".$fl_usuario);
    $result['all_comments'] = $notice; 
    $result['all_comments_total'] = $tot_cometarios; 
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



  
  
  
  echo json_encode((Object) $result);
?>