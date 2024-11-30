<?php
	# Librerias
	require("../../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  $sufix=langSufix();

  function GetBoardNotification($fl_usuario){
    # Para los comentarios en los diferentes post
  
    $Query  ="SELECT nb_programa, b.fl_usuario, b.ds_comment, DATE_FORMAT(b.fe_comment , '%Y-%m-%d %H:%i:%s'), a.fl_gallery_post_sp, ";
    $Query .= "b.fg_read, fl_gallery_comment_sp, a.nb_archivo, a.fl_usuario ,'0' fg_confirmacion_email ";
    $Query .= "FROM k_gallery_post_sp a ";
    $Query .= "JOIN k_gallery_comment_sp b ON(b.fl_gallery_post_sp=a.fl_gallery_post_sp) ";
    $Query .= "LEFT JOIN c_programa_sp c ON(c.fl_programa_sp=a.fl_programa_sp OR a.fl_programa_sp is null ) ";
    $Query .= "WHERE a.fl_usuario=".$fl_usuario." AND b.fl_usuario<>".$fl_usuario."  ";
    // $Query .= "AND b.fl_gallery_post_sp IN(SELECT DISTINCT r.fl_gallery_post_sp fl_gallery_post_sp_comentado FROM k_gallery_comment_sp r WHERE r.fl_usuario=$fl_usuario) ";
    $Query .= "ORDER BY b.fe_comment DESC ";

  	$rs = EjecutaQuery($Query);
    $no_posts = CuentaRegistros($rs);
  	# Default activity board icon
  	$ds_ruta_imagen = SP_IMAGES."/activityboard_default.jpg";
  	$result = array();        
  	for($i=0; $row=RecuperaRegistro($rs);){
  		$nb_programa = $row[0];
      $fl_usuario_comento = $row[1];
      $ds_comment = str_texto($row[2]);
      # Obtenemos el avatar del usuario que comento
      $avatar = ObtenAvatarUsuario($fl_usuario_comento);
      $ds_user_comento = ObtenNombreUsuario($fl_usuario_comento);
      $fe_comment = $row[3];
      # Obtenemos el tiempo
      $time = time_elapsed_string($fe_comment);
      $fl_gallery_post_sp = $row[4];
      $fg_read = $row[5];
      $fl_gallery_comment_sp = $row[6];
      $nb_archivo = img_usr($row[8], $row[7], $programa);
      $fg_confirmacion_email=$row[9];
      
      $url='#site/gallery.php?post='.$fl_gallery_comment_sp;
      
     
      
      
      if(empty($fg_read))
        $read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_gallery_comment_sp."'><small class='text-primary' onclick='Mark(0,0,".$fl_gallery_comment_sp.");'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1832)."</small></div>";
      else
        $read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_gallery_comment_sp."'><small onclick='Mark(0,1,".$fl_gallery_comment_sp.");'><i class='fa fa-circle-o'></i> ".ObtenEtiqueta(1833)."</small></div>";     
      if($no_posts > 0){
  			$result["stream".$i] = array(
	  			"url" => $url,
					"img" => $avatar,
					"title" => ObtenEtiqueta(1813),
					"time" => $time,
					"subject" => $ds_user_comento,
					"abstract" => "You have $no_posts unread posts",
					"abstract_style" => '',
          "comentario" => $ds_comment,
          "read" => $read,
          "fg_read" => $fg_read, 
          "fl_gallery_comment_sp" => $fl_gallery_comment_sp,
          "img_post" => $nb_archivo,
	  		);
	  		$i++;
  		}
  	}    
  	$result["size"] = array("total" => $i);
  	echo json_encode((Object) $result);
  }
  
  function GetBoardComments($fl_usuario){
    # Aqui mostrar todas los comentarios donde ha comentado
    # Buscamos todos los cometarios de los posts que ha comentado diferentes al usuario
      
      
    $Query3  ="( ";  
    $Query3 .= "SELECT nb_programa, a.fl_usuario, a.ds_comment, DATE_FORMAT(a.fe_comment , '%Y-%m-%d %H:%i:%s')fe_comment, a.fl_gallery_post_sp, ";
    $Query3 .= "a.fg_read, a.fl_gallery_comment_sp, b.nb_archivo, b.fl_usuario,'0' fg_confirmacion_email,''solicitud,''ds_post  ";
    $Query3 .= "FROM k_gallery_comment_sp a ";
    $Query3 .= "JOIN k_gallery_post_sp b ON(a.fl_gallery_post_sp=b.fl_gallery_post_sp) ";
    $Query3 .= "LEFT JOIN c_programa_sp c ON(b.fl_programa_sp=c.fl_programa_sp OR b.fl_programa_sp IS NULL) ";
    #$Query3 .= "WHERE a.fl_gallery_post_sp IN(SELECT DISTINCT fl_gallery_post_sp fl_gallery_post_sp_comentado FROM k_gallery_comment_sp WHERE fl_usuario=$fl_usuario) ";
    $Query3 .= "WHERE a.fl_usuario<>$fl_usuario AND b.fl_usuario=$fl_usuario AND a.fg_read='0' ";
    $Query3 .= "ORDER BY a.fe_comment DESC ";
    $Query3 .=") UNION (";
    $Query3 .="
             SELECT P.nb_programa, A.fl_alumno_beneficiado fl_usuario ,A.ds_email ds_comment,A.fe_creacion fe_comment, 
             fl_confirmacion_email_curso fl_gallery_post_sp, A.fg_revisado_alumno fg_read,fl_confirmacion_email_curso fl_gallery_comment_sp, P.nb_thumb nb_archivo,A.fl_alumno_beneficiado 
             ,'1' fg_confirmacion_email,''solicitud,''ds_post  
            FROM k_confirmacion_email_curso A
            JOIN c_programa_sp P ON P.fl_programa_sp=A.fl_programa_sp 
            WHERE A.fl_alumno_beneficiado=$fl_usuario AND A.fg_revisado_alumno='0' ORDER BY fl_confirmacion_email_curso DESC 
    ";
    $Query3.=") UNION ( ";
    $Query3.=" SELECT b.nb_programa,a.fl_usuario_sp fl_usuario,''ds_comment,DATE_FORMAT(a.fe_creacion , '%Y-%m-%d %H:%i:%s')fe_comment,''fl_gallery_post_sp,a.fg_revisado_alumno fg_read  
                ,a.fl_usu_pro fl_gallery_comment_sp, b.nb_thumb nb_archivo,a.fl_usuario_sp,'2'fg_confirmacion_email,''solicitud,''ds_post 
               FROM k_usuario_programa a 
			   JOIN c_programa_sp b ON b.fl_programa_sp=a.fl_programa_sp WHERE fl_usuario_sp=$fl_usuario AND a.fg_revisado_alumno='0' or a.fg_revisado_alumno='' 
                
    ";
    $Query3.=") ";
    #Notificaciones recibidas
    $Query3.="UNION (";
    $Query3.="SELECT ''nb_programa,fl_usuario_origen, ds_mensaje, DATE_FORMAT(fe_creacion , '%Y-%m-%d %H:%i:%s')fe_comment,''fl_gallery_post,fg_revisado_alumno fg_read 
              ,'' fl_gallery_comment_sp,''nb_archivo,''fl_usuario_sp ,''fg_confirmacion_email,'1'solicitud ,''ds_post    
              FROM k_relacion_usuarios WHERE fl_usuario_destinatario=$fl_usuario AND fg_aceptado='0'    
    
   ";
    $Query3.=") ";
	#solicitudes aceptadas
	$Query3.="UNION (";
    $Query3.="SELECT ''nb_programa,fl_usuario_destinatario, ds_mensaje, DATE_FORMAT(fe_creacion , '%Y-%m-%d %H:%i:%s')fe_comment,''fl_gallery_post,fg_revisado_alumno fg_read 
              ,'' fl_gallery_comment_sp,''nb_archivo,''fl_usuario_sp ,''fg_confirmacion_email,'2'solicitud,''ds_post     
              FROM k_relacion_usuarios WHERE fl_usuario_origen=$fl_usuario AND fg_aceptado='1' AND fg_revisado_alumno='0'   
    
   ";
   #Folowers
    $Query3.=") UNION (";
	$Query3.="SELECT  ''nb_programa,fl_usuario_origen,''ds_mensaje,DATE_FORMAT(fe_creacion , '%Y-%m-%d %H:%i:%s')fe_comment,''fl_gallery_post,fg_revisado fg_read
	  			,''fl_gallery_comment_sp,''nb_archivo,''fl_usuario_sp,''fg_confirmacion_email,'3'solicitud,''ds_post
			  FROM c_followers WHERE fl_usuario_destino=$fl_usuario  AND fg_revisado='0'  ";
	$Query3.=") UNION(";
    

	/*  se tarda mucho la consulta hacia la vista y se duvide en dos.
	#Likes en Post.
	$Query3.="
			 SELECT ''nb_programa,a.fl_usuario,a.fg_origen ds_mensaje,DATE_FORMAT(a.fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment,fl_gallery_post_sp,'0' fg_read
	         ,a.fl_gallery_post_feed,b.nb_archivo,b.fl_usuario  fl_usuario_sp ,''fg_confirmacion_email,'4'solicitud 
             FROM c_feed_likes a
			 JOIN v_gallery_feed b ON a.fl_gallery_post_feed=b.fl_gallery_post_sp
			 WHERE b.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND a.fl_usuario<>$fl_usuario  ";
	
	*/

    
    #Likes en Post. pertencientes a fedd.(1er nivel)
    $Query3.="
            SELECT ''nb_programa,a.fl_usuario,a.fg_origen  ds_mensaje,DATE_FORMAT(a.fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment, c.fl_publicacion,'0' fg_read,a.fl_gallery_post_feed ,c.nb_img_video,c.fl_usuario fl_usuario_sp ,''fg_confirmacion_email,'4'solicitud,c.ds_contenido ds_post  
            FROM c_feed_likes a
            JOIN c_feed_publicaciones c ON c.fl_publicacion = a.fl_gallery_post_feed
		    WHERE c.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND a.fl_usuario<>$fl_usuario
    ";
    $Query3.=")UNION(";
    #Likes en post (2do nivel).
    $Query3.="
            SELECT ''nb_programa,a.fl_usuario,'p'ds_mensaje,DATE_FORMAT(a.fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment,c.fl_publicacion fl_gallery_post_sp,a.fg_revisado fg_read,c.fl_publicacion fl_gallery_comment_sp,''nb_archivo,b.fl_usuario,''fg_confirmacion_email,'4'solicitud,b.ds_comment ds_post  
            FROM k_feed_likes a
            JOIN k_feed_comment b ON a.fl_gallery_comment_sp=b.fl_feed_comment
            JOIN c_feed_publicaciones c ON c.fl_publicacion=b.fl_publicacion
            WHERE c.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND a.fl_usuario<>$fl_usuario  
            ";

    #Likes 3er nivel
    $Query3.=")UNION( ";
    $Query3.="
            SELECT ''nb_programa,b.fl_usuario,'p'ds_comment,DATE_FORMAT(a.fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment,d.fl_publicacion fl_gallery_post_sp ,a.fg_revisado fg_read,d.fl_publicacion fl_gallery_comment_sp,''nb_archivo,b.fl_usuario,''fg_confirmacion_email,'4'solicitud,b.ds_comment ds_post 
            FROM k_feed_likes a
            JOIN k_feed_comment_comment b ON a.fl_gallery_comment_sp_comment=b.fl_feed_comment_comment 
            JOIN k_feed_comment c ON c.fl_feed_comment=b.fl_feed_comment
            JOIN c_feed_publicaciones d ON d.fl_publicacion=c.fl_publicacion
            WHERE c.fl_usuario=$fl_usuario AND a.fl_usuario<>$fl_usuario AND a.fg_revisado='0' 
        ";

    $Query3.=") UNION (";
    #Likes en post pertencientes al board.(1er nivel)
    $Query3.="
             SELECT ''nb_programa,a.fl_usuario,a.fg_origen ds_mensaje,DATE_FORMAT(a.fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment, b.fl_gallery_post_sp,'0' fg_read ,a.fl_gallery_post_feed ,b.nb_archivo,b.fl_usuario fl_usuario_sp ,''fg_confirmacion_email,'4'solicitud ,b.ds_post 
             FROM c_feed_likes a
             JOIN k_gallery_post_sp b ON b.fl_gallery_post_sp = a.fl_gallery_post_feed
			 WHERE b.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND a.fl_usuario<>$fl_usuario
    ";

    $Query3.=") UNION (";

	#Comentarios Post  1er nivel. feed(1er nivel)
	$Query3.=" 
			SELECT ''nb_programa,a.fl_usuario,a.ds_comment ds_mensaje, DATE_FORMAT(a.fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment,a.fl_feed_comment fl_gallery_post,'0' fg_read
	        , b.fl_publicacion,b.nb_img_video nb_archivo,'p'fl_usuario_sp,''fg_confirmacion_email,'5'solicitud,a.ds_comment ds_post 
			FROM k_feed_comment a
			JOIN c_feed_publicaciones b ON a.fl_publicacion=b.fl_publicacion
			WHERE b.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND fg_ayuda='0'  AND a.fl_usuario<>$fl_usuario 
	
	
	";
    #Comentarios de tercer nivel
    $Query3.=")UNION( ";
    $Query3.="
            SELECT ''nb_programa,b.fl_usuario,c.ds_comment ds_mensaje, DATE_FORMAT(b.fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment,c.fl_publicacion fl_gallery_post,'0' fg_read
	            ,d.fl_publicacion,''nb_archivo,'p'fl_usuario_sp,''fg_confirmacion_email,'5'solicitud,b.ds_comment ds_post
            FROM k_feed_comment_comment b
            JOIN k_feed_comment c ON c.fl_feed_comment=b.fl_feed_comment 
            JOIN c_feed_publicaciones d ON d.fl_publicacion=c.fl_publicacion 
            WHERE c.fl_usuario=$fl_usuario AND c.fg_revisado='0' AND b.fl_usuario<>$fl_usuario
        
        ";


    $Query3.=")UNION(";
    #Comentarios sobre post de ambulancia.
    $Query3.=" 
			SELECT ''nb_programa,a.fl_usuario,a.ds_comment ds_mensaje, DATE_FORMAT(a.fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment,a.fl_feed_comment fl_gallery_post,'0' fg_read
	        , b.fl_publicacion,b.nb_img_video nb_archivo,'p'fl_usuario_sp,''fg_confirmacion_email,'5'solicitud,a.ds_comment ds_post 
			FROM k_feed_comment a
			JOIN c_feed_publicaciones b ON a.fl_publicacion=b.fl_publicacion
			WHERE b.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND fg_ayuda='1'  AND a.fl_usuario<>$fl_usuario 
	
	
	";




	#Comenatrios del board.
	$Query3.=") UNION (";
    /*
	$Query3.=" SELECT ''nb_programa,a.fl_usuario,a.ds_comment ds_mensaje, DATE_FORMAT(a.fe_comment, '%Y-%m-%d %H:%i:%s')fe_comment,a.fl_gallery_comment_sp fl_gallery_post,'0' fg_read
				, b.fl_gallery_post_sp  , 'g'nb_archivo,''fl_usuario_sp,''fg_confirmacion_email,'5'solicitud
  
			   FROM k_gallery_comment_sp a
	           JOIN k_gallery_post_sp b ON a.fl_gallery_post_sp=b.fl_gallery_post_sp 
			   WHERE b.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND a.fl_usuario<>$fl_usuario AND fg_ayuda='0' 
	";
	$Query3.=")UNION(";
	*/
	##Respuesta correcta marcado que fue respuesta correcta.
	$Query3.="
		    SELECT ''nb_programa,a.fl_usuario,a.ds_comment ds_mensaje, DATE_FORMAT(a.fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment,a.fl_feed_comment fl_gallery_post,'0' fg_read
	        , b.fl_publicacion,'' nb_archivo,''fl_usuario_sp,''fg_confirmacion_email,'6'solicitud,a.ds_comment ds_post 
			FROM k_feed_comment a
			JOIN c_feed_publicaciones b ON a.fl_publicacion=b.fl_publicacion
			WHERE a.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND fg_ayuda='1' AND a.fg_correcto='1'  AND b.fl_usuario<>$fl_usuario
	
	";
	
	$Query3.=")UNION(";
    $Query3.=" SELECT ''nb_programa,fl_usuario_sp ,''ds_mensaje,DATE_FORMAT(fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment,fl_programa_sp fl_gallery_post,'0'fg_read
               ,fl_request_access,''nb_archivo,''fl_usuario_sp,''fg_confirmacion_email,'7'solicitud,''ds_post 
               FROM k_request_access_course WHERE fl_maestro_sp=$fl_usuario AND fg_revisado='0' ";
    $Query3.=")UNION(";

    #access reuquest denegados
    $Query3.=" SELECT ''nb_programa,fl_usuario_sp ,''ds_mensaje,DATE_FORMAT(fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment,fl_programa_sp fl_gallery_post,'0'fg_read
               ,fl_request_access,''nb_archivo,''fl_usuario_sp,''fg_confirmacion_email,'9'solicitud,''ds_post 
               FROM k_request_access_course WHERE fl_usuario_sp=$fl_usuario AND fg_denegado='1' "; 

    $Query3.=")UNION(";

    $Query3.="SELECT ''nb_programa,fl_usuario_sp,ds_comment,DATE_FORMAT(fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment,fl_feed_comentarios fl_gallery_post,'0' fg_read ";
    $Query3.=", fl_publicacion,''nb_archivo,fg_origen fl_usuario_sp,''fg_confirmacion_email,'8'solicitud,ds_comment ds_post FROM k_feed_comentarios WHERE fl_usuario_destino=$fl_usuario AND fg_revisado='0' ";
    $Query3.=") ";

	$Query3.="ORDER BY fe_comment DESC ";
    $rs3 = EjecutaQuery($Query3);
    $no_posts2 = CuentaRegistros($rs3);
    for($k2=0;$rowk3 = RecuperaRegistro($rs3);){
      $nb_programa = $rowk3[0];
      $fl_usuario_comento = $rowk3[1];
      $ds_comment = str_texto($rowk3[2]);
      # Obtenemos el avatar del usuario que comento
      $avatar = ObtenAvatarUsuario($fl_usuario_comento);
      $ds_user_comento = ObtenNombreUsuario($fl_usuario_comento);
      $fe_comment = $rowk3[3];
      # Obtenemos el tiempo
      $time = time_elapsed_string($fe_comment);
      $fl_gallery_post_sp = $rowk3[4];
      $fg_read = $rowk3[5];
      $fl_gallery_comment_sp = $rowk3[6];
      $nb_archivo = img_usr($rowk3[8], $rowk3[7], $nb_programa);
      $ds_description_post=$rowk3['ds_post'];
	  
	  $fg_confirmacion_email=$rowk3[9];
      
      $fg_confrimacion_em=0;
      $url2='view_post('.$fl_gallery_post_sp.', '.$fl_gallery_comment_sp.')';
      
      $fg_solicitud_amistad=$rowk3['solicitud'];
      
      if($fg_confirmacion_email==1){
          
          $ds_user_comento=ObtenEtiqueta(2119);
          $ds_comment="Course: ".$nb_programa."<br/> ".ObtenEtiqueta(2122).": ".$ds_comment;
          $avatar=PATH_HOME.'/modules/fame/uploads/'.$rowk3[7];
          $url="";
          $nb_archivo="";
          $fg_confrimacion_em=1;
          $url2="";
      }
    
      
      $confirmar_leido=0;
      
      
      if($fg_solicitud_amistad=='1'){
      
          #Recuperamos el avatar del usuario.
          $nb_archivo="";
          $ds_comment=ObtenEtiqueta(2190);
          $url2='ViewProfile("#site/profile_view.php?profile_id='.$fl_usuario_comento.'")';
          $fl_gallery_comment_sp=$fl_usuario_comento;
          
      }
      
      if($fg_solicitud_amistad=='2'){
          
          #Recuperamos el avatar del usuario.
          $nb_archivo="";
          $ds_comment=ObtenEtiqueta(2193);
          $url2='ViewProfile("#site/profile_view.php?profile_id='.$fl_usuario_comento.'")';
          $fl_gallery_comment_sp=$fl_usuario_comento;
          $confirmar_leido=1;
      }
	

 
      
      if($fg_solicitud_amistad==1){
      
        
          $read="<p style='margin:5px;'><a href='javascript:void(0);' Onclick='ConfirmarSolicitud(2,$fl_usuario_comento,$fl_usuario);' style='float:right;margin-right: 5px;' class='btn btn-primary btn-xs'>".ObtenEtiqueta(2192)."</a></p> <p><a href='javascript:void(0);' Onclick='ConfirmarSolicitud(2,$fl_usuario_comento,$fl_usuario);'  style='float:right;margin-right: 5px;' class='btn btn-success btn-xs'>".ObtenEtiqueta(2191)."</a>  </p>";
          
      }else{
          

              if(empty($fg_read)){
                  $read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_gallery_comment_sp."'><small class='text-primary' onclick='Mark(0,0,".$fl_gallery_comment_sp.",".$fg_confrimacion_em.",".$confirmar_leido.");'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1832)."</small></div>";
              }else{
                  $read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_gallery_comment_sp."'><small onclick='Mark(0,1,".$fl_gallery_comment_sp.");'><i class='fa fa-circle-o'></i> ".ObtenEtiqueta(1833)."</small></div>";     
              }
     
      }


      //courses asignados
      if($fg_confirmacion_email==2){
          $ds_user_comento=ObtenEtiqueta(2134);
          $ds_comment=$nb_programa;
          $avatar=PATH_HOME.'/modules/fame/uploads/'.$rowk3[7];
          $url="";
          $nb_archivo="";
          $fg_confrimacion_em=2;
          $url2="";
          $read="<div class='text-align-right' style='padding-top: 5px;' id='noti_".$fl_gallery_comment_sp."'><a href='index.php#site/node.php?node=159'  style='float:right;width:100px;' class='btn btn-success btn-xs'>".ObtenEtiqueta(2606)."</a></div>";
      }

	
	  #Para los followers.
      if($fg_solicitud_amistad=='3'){
		  
		 $nb_archivo="";
		 $ds_comment=ObtenEtiqueta(2518);
		 $url2='ViewProfile("#site/myprofile.php?profile_id='.$fl_usuario_comento.'&c=1&uo='.$fl_usuario.'&f=1")';
		 $confirmar_leido=0;
		 $read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_usuario_comento."_f_".$fl_usuario."'><small class='text-primary' onclick='Mark(0,0,0,0,0,0,$fl_usuario_comento,1);MarkFeed(1,".$fl_usuario_comento.",".$fl_usuario.",1);'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1832)."</small></div>"; 
		 $fl_gallery_comment_sp="f".$fl_usuario_comento."_".$fl_usuario;
	  }
         
     #Pra los likes
	 if(($fg_solicitud_amistad==4)){
		 

         $fl_instituto_us=ObtenInstituto($fl_usuario_comento);		 
		 $fl_post=$fl_gallery_comment_sp;
		 $fg_origen=$rowk3[2];  //Tarea si es un post galleryBoard o feed  g=gallery p=feed.
         $ext_archivo=ObtenExtensionArchivo($rowk3[7]);
         $nb_imh_thumb=ObtenNombreArchivo($rowk3[7]);
		 $fl_usuario_post=$rowk3[8];
		 
		 if(!empty($rowk3[7])){
		 
			 if($ext_archivo=='m3u8'){
				 
				 $nb_archivo="<img src='" . PATH_SELF_UPLOADS . "/" . $fl_instituto_us . "/USER_".$fl_usuario_comento."/videos/".$nb_imh_thumb.".png' "; 
				 
			 }else{

				 if($fg_origen=='g')
					 $nb_archivo="<img src=".PATH_SELF_UPLOADS."/gallery/feed_posts/".$rowk3[7]."  class='img-responsive' height='15px'>";
				 if($fg_origen=='p')
					 $nb_archivo="<img src=".PATH_SELF_UPLOADS."/posts/feed_posts/thumbs/".$rowk3[7]." class='img-responsive' height='15px' >";

			 }
		 }else
			 $nb_archivo="";

		 $ds_comment="<small class='text-muted'><i style='margin:3px;' class='fa likes fa-heart mikelike' aria-hidden='true'></i> ".ObtenEtiqueta(2519)." (".substr($ds_description_post,0,25)."...)</small>";
		 $url2="ViewPost($fl_gallery_post_sp,$fl_usuario_post,\"$fg_origen\");";
		 $confirmar_leido=0;		 
		 $read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_usuario_comento."_l_".$fl_usuario."'><small class='text-primary' onclick='Mark(0,0,".$fl_post.",0,0,0,$fl_usuario_comento,2,\"$fg_origen\");MarkFeed(2,".$fl_usuario_comento.",".$fl_usuario.",1);'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1832)."</small></div>"; 
		 $fl_gallery_comment_sp="like".$fl_usuario_comento."_".$fl_usuario;
		 
	 }
	 
	 #Para los comentarios de los post 1er nivel.
    if($fg_solicitud_amistad==5){
		$fg_read=0;
		$fg_origen=$rowk3[8];
		
		 $fl_instituto_us=ObtenInstituto($fl_usuario_comento);
		 $ext_archivo=ObtenExtensionArchivo($rowk3[7]);
         $nb_imh_thumb=ObtenNombreArchivo($rowk3[7]);
		 
		 if(!empty($rowk3[7])){
		 
			 if($ext_archivo=='m3u8'){
				 
				 $nb_archivo="<img src='" . PATH_SELF_UPLOADS . "/" . $fl_instituto_us . "/USER_".$fl_usuario_comento."/videos/".$nb_imh_thumb.".png' "; 
				 
			 }else{

				 if($fg_origen=='g')
					 $nb_archivo="<img src=".PATH_SELF_UPLOADS."/gallery/feed_posts/".$rowk3[7]."  class='img-responsive' height='15px'>";
				 if($fg_origen=='p')
					 $nb_archivo="<img src=".PATH_SELF_UPLOADS."/posts/feed_posts/thumbs/".$rowk3[7]." class='img-responsive' height='15px' >";

			 }
		 }else
			 $nb_archivo="";
		
		
		
		
		
		
		$fl_post=$fl_gallery_comment_sp;
		$fl_comentario=$fl_gallery_post_sp;
		$ds_comment=" <small class='text-muted'><i class='fa fa-comment-o' aria-hidden='true'></i> ".ObtenEtiqueta(2520)."<br> (".substr($ds_description_post,0,25)."...)</small>";//New comentario.
		
		$url2="ViewPost($fl_post,$fl_usuario_comento,\"$fg_origen\");";
		$confirmar_leido=0;	
		$read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_usuario_comento."_c_".$fl_usuario."'><small class='text-primary' onclick='Mark(0,0,".$fl_comentario.",0,0,0,$fl_usuario_comento,3,\"$fg_origen\");MarkFeed(3,".$fl_usuario_comento.",".$fl_usuario.",1);'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1832)."</small></div>"; 
		$fl_gallery_comment_sp="comments".$fl_usuario_comento."_".$fl_usuario;
		
	}
	
	 
	 #Para los comentarios ayuda.
    if($fg_solicitud_amistad==6){
		
		$fg_origen="p";
		$nb_archivo="";
		$fl_post=$fl_gallery_comment_sp;
		$fl_comentario=$fl_gallery_post_sp;
		$ds_comment=ObtenEtiqueta(2513);//Marcado como correcto.
		$url2="ViewPost($fl_post,$fl_usuario_comento,\"$fg_origen\");";
		$confirmar_leido=0;	
		$read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_usuario_comento."_a_".$fl_usuario."'><small class='text-primary' onclick='Mark(0,0,".$fl_comentario.",0,0,0,$fl_usuario_comento,3,\"$fg_origen\");MarkFeed(3,".$fl_usuario_comento.",".$fl_usuario.",1);'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1832)."</small></div>"; 
		$fl_gallery_comment_sp="ayuda".$fl_usuario_comento."_".$fl_usuario;
		
	}
	

    #Request access course 
    if($fg_solicitud_amistad==7){

        #Recuperamos la imagen del archivo,
        $Query="SELECT nb_thumb,nb_programa".$sufix." FROM c_programa_sp WHERE fl_programa_sp=$fl_gallery_post_sp   ";
        $row=RecuperaValor($Query);
        $nb_thumb=$row['nb_thumb'];
        $nb_programa=$row[1];
        $nb_archivo="<img src='/../../AD3M2SRC4/modules/fame/uploads/$nb_thumb'  class='img-responsive' height='15px'>";
        $ds_user_comento=ObtenEtiqueta(2598)."<small> (".$ds_user_comento.")</small>";
        $ds_comment=$nb_programa;
        $url2="";
        $read = "<div class='text-align-right' style='padding-top: 5px;' id='noti_".$fl_gallery_comment_sp."'><button class='btn btn-danger btn-xs' onclick=\"PermitirCourse(1,$fl_usuario_comento,$fl_gallery_post_sp);\"  >".ObtenEtiqueta(2605)."</button>&nbsp;<button class='btn btn-success btn-xs' onclick=\"PermitirCourse(2,$fl_usuario_comento,$fl_gallery_post_sp);\">".ObtenEtiqueta(2604)."</button>&nbsp;<small class=' cursor-pointer text-primary' onclick='Mark(0,0,".$fl_gallery_comment_sp.",".$fg_confrimacion_em.",".$confirmar_leido.",0,0,0,7);'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1832)."</small></div>";
        
        
    }
	
    #Para los comentarios de cualquier post donde el usuario participe.
    if($fg_solicitud_amistad==8){
		$fg_read=0;
		$fg_origen=$rowk3[8];
		
        $fl_instituto_us=ObtenInstituto($fl_usuario_comento);
        $ext_archivo=ObtenExtensionArchivo($rowk3[7]);
        $nb_imh_thumb=ObtenNombreArchivo($rowk3[7]);
        
      
        $nb_archivo="";
		
		
		
		
		
		
		$fl_post=$fl_gallery_comment_sp;
		$fl_comentario=$fl_gallery_post_sp;
		$ds_comment=" <small class='text-muted'><i class='fa fa-comment-o' aria-hidden='true'></i> ".ObtenEtiqueta(2520)."<br> (".substr($ds_description_post,0,25)."...)</small>";//New comentario.
		
		$url2="ViewPost($fl_post,$fl_usuario_comento,\"$fg_origen\");";
		$confirmar_leido=0;	
		$read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_gallery_comment_sp."'><small class='text-primary' onclick='Mark(0,0,".$fl_comentario.",0,0,0,$fl_usuario_comento,3,\"$fg_origen\");MarkFeed(3,".$fl_usuario_comento.",".$fl_usuario.",1);'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1832)."</small></div>"; 
		$fl_gallery_comment_sp="comments".$fl_usuario_comento."_".$fl_usuario;
		
	} 


    if($fg_solicitud_amistad==9){

        #Recuperamos la imagen del archivo,
        $Query="SELECT nb_thumb,nb_programa".$sufix." FROM c_programa_sp WHERE fl_programa_sp=$fl_gallery_post_sp   ";
        $row=RecuperaValor($Query);
        $nb_thumb=$row['nb_thumb'];
        $nb_programa=$row[1];
        $nb_archivo="<img src='/../../AD3M2SRC4/modules/fame/uploads/$nb_thumb'  class='img-responsive' height='15px'>";
        $ds_user_comento=ObtenEtiqueta(2603);
        $ds_comment=$nb_programa;
        $url2="";
        $read = "<div class='text-align-right' style='padding-top: 5px;' id='Div1'><small class=' cursor-pointer text-primary' onclick='Mark(0,0,".$fl_gallery_comment_sp.",".$fg_confrimacion_em.",".$confirmar_leido.",0,0,0,7);'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1832)."</small></div>";
        
        
    }






      
      if($no_posts2>0){
        $result["streamotros".$k2] = array(
          // "url2" => '#site/gallery.php?post='.$fl_gallery_comment_sp,
          "url2" => $url2,
          "img2" => $avatar,
          "title2" => ObtenEtiqueta(1813),
          "time2" => $time,
          "subject2" => $ds_user_comento,
          "abstract2" => "You have $no_posts2 unread posts",
          "abstract_style2" => '',
          "comentario2" => $ds_comment,
          "read2" => $read,
          "fg_read2" => $fg_read, 
          "fl_gallery_comment_sp2" => $fl_gallery_post_sp,
          "img_post2" => $nb_archivo,
          "fl_gallery_comment_sp_2" => $fl_gallery_comment_sp
        );
        $k2++;
      }      
    }



    $result["size"] = array("total" => $k2);
  	echo json_encode((Object) $result);
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
  
  # Get number comments read oor unread
  $Query  = "SELECT COUNT(*) FROM k_gallery_post_sp a ";
  $Query .= "LEFT JOIN k_gallery_comment_sp b ON(b.fl_gallery_post_sp=a.fl_gallery_post_sp) ";
  $Query .= "LEFT JOIN c_programa_sp c ON(c.fl_programa_sp=a.fl_programa_sp) ";
  $Query .= "WHERE a.fl_usuario=$fl_usuario AND (b.fg_read='0' OR b.fg_read='1') ";
  $row = RecuperaValor($Query);
  $tot_comments = $row[0];
  
  $QueryG="SELECT COUNT(*) FROM k_confirmacion_email_curso  a WHERE a.fl_alumno_beneficiado=$fl_usuario  and fg_revisado_alumno='0' ";  
  $rowg=RecuperaValor($QueryG);
  $no_courses_access = $rowg[0];
  
  $tot_comments=$tot_comments+$no_courses_access;
  
?>

<div class="message-text bg-color-white text-align-center padding-top-10">
<a href="javascript:void(0);" class="username" onclick="Mark(1,0,0);"><strong> <i class="fa fa-circle"></i> <?php echo ObtenEtiqueta(1834); ?></strong></a>
<hr class='no-margin margin-top-5'>
</div>


<ul class="notification-body" id="notify_comments"></ul>

<script type="text/javascript">
	// VANAS Board  
	var result, board, notices, result2, board2;
	result = <?php GetBoardNotification($fl_usuario); ?>;
	result2 = <?php GetBoardComments($fl_usuario); ?>;

	notices = "";
	// for(var i=0; i<result.size.total; i++){
		// board = result['stream'+i];
		// notices += NoticeBar(board.url, board.img, board.title, board.time, board.subject, board.abstract, board.abstract_style, board.comentario, board.read, 
    // board.fg_read, board.fl_gallery_comment_sp, board.img_post);
    // }
  var total=result2.size.total;
 
  for(var j=0; j<result2.size.total; j++){
    board2 = result2['streamotros'+j];
		notices += NoticeBar(board2.url2, board2.img2, board2.title2, board2.time2, board2.subject2, board2.abstract2, board2.abstract_style2, board2.comentario2, board2.read2, 
    board2.fg_read2, board2.fl_gallery_comment_sp2, board2.img_post2, board2.fl_gallery_comment_sp_2);
  }
  if(total==0){
      var mensaje="<?php echo ObtenEtiqueta(2397);?>";
      notices+="<li id='comments_fame'><span id='' class''> "+
               "  <a href='javascript:void(0);' style='padding-top:10px !important' ><div class='alert alert-info text-center' role='alert'>"+mensaje+"</div>"+
               "</a>"+
               "</li>";
  }

	$(".notification-body").prepend(notices);

	

	function NoticeBar(url, img, title, time, subject, abstract, abstractStyle, comentario, read, fg_read, fl_gallery_comment_sp, img_post, fl_gallery_comment_sp_2){
		var read_unread = "unread";
		if(fg_read == 1)
		  read_unread = "read";
			var notice =
		  "<li id='comments_fame'><span id='li_"+fl_gallery_comment_sp_2+"' class='"+read_unread+"'>"+
			"<a href='javascript:"+url+"' style='padding-left:50px !important; line-height:15px;'>"+
			  "<img src='"+img+"' alt='' class='air air-top-left ' height='40' width='40' />"+
			  "<span class='from'>"+subject+"</span>"+
			  "<time>"+time+"</time>"+
			  "<div class='col-sm-9 no-padding'>"+
			  "<span class='msg-body'>"+comentario+"</span></div>"+
			  "<div class='col-sm-1 text-align-center'>"+
			  img_post+
			  "</div>"+
			"</a>"+
			read+
		  "</span></li>";
			return notice;
	}

function Mark(all_comments=0, mark, post,fg_confirmacion_email=0,fg_confirmado_amigo=0,fg_mesaje_leido=0,fl_user_origen=0,fg_feed=0,fg_origen=0){
    

    // fg_feed 1= follow  2=likes 3=respuestas  4=comenn post.


    // Variables
	var data;
    if(all_comments==1){
	   //lo coloca en 0.
	   $("#no_news").empty();
	   $("#no_news").append(0);
	   $("#activity>b").empty();
	   $("#activity>b").append(0);
	   
	   $("#tot_assigment_"+<?php echo $fl_usuario;?>).empty();
	   $("#tot_assigment_"+<?php echo $fl_usuario;?>).append(0);
	   
	   $("#tot_messages_"+<?php echo $fl_usuario;?>).empty();
	   $("#tot_messages_"+<?php echo $fl_usuario;?>).append(0);
	  
      data = "all_comments="+all_comments;
    }
    else{
        data = "fg_read_r="+mark+"&fl_gallery_comment_sp="+post+"&fg_confirmacion_email="+fg_confirmacion_email+"&fg_confirmado_amigo="+fg_confirmado_amigo+"&all_comments="+all_comments+"&fg_mesaje_leido="+fg_mesaje_leido+"&fl_user_origen="+fl_user_origen+"&fg_feed="+fg_feed+"&fg_origen="+fg_origen
    }
    // ajax
    $.ajax({
      type: "POST",
      url: "<?php echo PATH_SELF_SITE; ?>/notify/marcar_notifiy.php",
      data: data,
    }).done(function(result){
				var content, elem = $('#noti_'+post), elem_il = $("#li_"+post);
				content = JSON.parse(result); 
        // notificacion
        var tot_notifications = $("#activity>b").text();
        var news = $("#no_news").text();
        var fg_chat=content.fg_chat;
        var no_mesajes = $("#no_messages").text();
        var no_mens_elimn=content.no_mensajes;


        

        // Limpaimos las notificaciones
        $("#activity>b").empty();
        
        if(fg_chat==1){

          
            //$("#no_news").empty();
            $("#no_messages").empty();
            
            var new_tot=parseInt(tot_notifications)-no_mens_elimn;
            var new_tot_msj=parseInt(no_mesajes)-no_mens_elimn;
            
            $("#activity>b").append(new_tot);
            $("#no_messages").append(new_tot_msj);
            $('#mostrar_noticias').hide();





        }else{
        
            // Mark read all comments        
            if(all_comments==1){
            
                var no_total_notifi= parseInt(content.no_total_notifi);
				



                $("#notify_comments").empty();
                $("#no_news").empty();
                $("#notify_comments").append(content.all_comments);
    
                var r = parseInt(tot_notifications) - content.all_comments_total;
                var n = parseInt(news) - content.all_comments_total;
                if(r>0){
                    $("#activity>b").append(r);
                }
                if(n>0){
                    //$("#no_news").append(n);
                }
				
                //lo coloca en 0.
				$("#no_news").empty();
				$("#no_news").append(0);
				
                $("#activity>b").empty();
				$("#activity>b").append(no_total_notifi);
				
				
				
				
				
            }
            else{
           

                elem.empty(); 
                $("#no_news").empty();
                elem.append(content.read_unread);          
                  
                // changer class li        
                if(mark==1){
                    elem_il.removeClass('read').addClass('unread');
                    // cambiamos el estado del la notificacio
                    var s = parseInt(tot_notifications)+1;
                    var n = parseInt(news)+1;
                    if(s>0){
                        $("#activity>b").append(s);           
                    }
                    if(n>0){
                        $("#no_news").append(n);
                    }
                }
                else{

           
                    // cambiamos el estado del la notificacio
                    elem_il.removeClass('unread');
                    var v = parseInt(tot_notifications) - 1;
                    var n = parseInt(news)-1;
                    if(v>0){
                        $("#activity>b").append(v);
                    }
                    if(n>0){
                        $("#no_news").append(n);
                    }
                }
				
				
				
				
				
            }

        }//end fg_chat

    });
	}


//Funcion para abri el profile del header monito de las notitifaciones.

function ViewProfile(url){

	window.open(url,'_self');

}
	
	    //Para las acciones de confirmar eleiminar solicitud.
function ConfirmarSolicitud(fg_tipo_respuesta,fl_usuario_origen,fl_usuario_actual){

    // ajax
    $.ajax({
        type: "POST",
        url: "<?php echo PATH_SELF_SITE; ?>/notify/marcar_solicitud_usuarios.php",
        data:"fg_tipo_respuesta="+fg_tipo_respuesta+
              "&fl_usuario_origen="+fl_usuario_origen+
              "&fl_usuario_actual="+fl_usuario_actual,
    }).done(function(result){

       var  respuesta = JSON.parse(result); 
       var  fg_tipo_contestacion= respuesta.fg_tipo_respuesta;
       var fl_usuario_origen=respuesta.fl_usuario_origen;
       var fl_usuario_actual=respuesta.fl_usuario_actual;
	   var nb_usuario_destinatario=respuesta.nb_usuario_destinatario;
	   
	   var fl_usuario_confirma_solicitud=fl_usuario_actual;
	   var fl_usuario_destino=fl_usuario_origen;
	   var nb_usuario_confirma_solicitud=respuesta.nb_usuario_confirma_solicitud;
	   
	   
       if(fg_tipo_respuesta){
        
           $('#li_'+fl_usuario_origen).addClass('hidden');
       
           var tot_notity=$("#tot_notity_"+fl_usuario_actual).text();//representa el primer circulo 
           $('#tot_notity_'+fl_usuario_actual).empty();//limpiamos el numerador
           var t = parseInt(tot_notity)-1;//obtenemos el nuevo total.
           $('#tot_notity_'+fl_usuario_actual).append(t);//Colocamos el nuevo total.

           var no_news = $('#no_news').text();
           $('#no_news').empty();
           var n = parseInt(no_news)-1;
           $('#no_news').append(n);


       }
	    //Enviamos, node notificcio bien chida para decirle que ya acpto su solicitud.
	   if(fg_tipo_respuesta==2){
		   
		   socket.emit('solicitud-amistadaceptada',fl_usuario_confirma_solicitud,fl_usuario_destino,nb_usuario_confirma_solicitud);
		   
	   }
	   
	   
	   
	   

    });
} 

function MarkFeed(fg_accion,fl_usuario_ori,fl_usuario_actual,fg_inicio){
	
	
	
	var div,noti,script;
	////monito de hsta arriba.
    var tot_notifications = $("#activity>b").text(); 
	var no_news = $("#no_news").text(); 
	
	
	
	//para notificaciones followers
	if(fg_accion==1){ 
	
		div = $("#li_f"+fl_usuario_ori+"_"+fl_usuario_actual+"");
		div.addClass('hidden');
		//escondemos el div y nos evitamos este chorizo abajo. de  quitar ponera,aumentar. etc.
		
		
		/*if(fg_inicio==1){//se marca como leido.
			script="<small  onclick='Mark(0,0,0,0,0,0,0,1);MarkFeed(1,"+fl_usuario_ori+","+fl_usuario_actual+",2);'><i class='fa fa-circle-o'></i> Mark unread</small>";
		    mark_follow.removeClass('unread').addClass('read');
		
		}
		else{//se marca como no leido.
			script="<small class='text-primary' onclick='Mark(0,0,0,0,0,0,0,1);MarkFeed(1,"+fl_usuario_ori+","+fl_usuario_actual+",1);'><i class='fa fa-circle'></i> Mark read</small>";	
			mark_follow.removeClass('read').addClass('unread');
		    
			//se obtinen los nuevos totales del monito y news.
			var new_to = parseInt(tot_notifications) + parseInt(1);
			$("#activity>b").empty();
			$("#activity>b").append(new_to);
			alert(new_to);
		    var no_news= parseInt(no_news) + parseInt(1);
			
			$("#no_news").empty();
			$("#no_news").append(no_news);
		}*/
		//$("#noti_"+fl_usuario_ori+"_f_"+fl_usuario_actual+"").empty();
		//$("#noti_"+fl_usuario_ori+"_f_"+fl_usuario_actual+"").append(script);
		
		
				
	}	
	//para likes
	if(fg_accion==2){		
		div = $("#li_like"+fl_usuario_ori+"_"+fl_usuario_actual+"");
		div.addClass('hidden');
	}
	//para comentarios
	if(fg_accion==3){
		
		div = $("#li_comments"+fl_usuario_ori+"_"+fl_usuario_actual+"");
		div.addClass('hidden');
			
	}
	if(fg_accion==4){
		
		div = $("#li_ayuda"+fl_usuario_ori+"_"+fl_usuario_actual+"");
		div.addClass('hidden');
	}
	
	
	
}


 function ViewPost(fl_gallery_post_sp,fl_usuario_post,fg_origen){
	 
	 $('#postId').modal('show');
	 var fame=1;
	 
	 $.ajax({
            type: "POST",
            url : "/fame/site/feed_post_modal.php",
            data: 'fl_gallery_post_sp='+fl_gallery_post_sp+'&fl_usuario_post='+fl_usuario_post+'&fame='+fame+'&fg_origen='+fg_origen,
            async: false,
			success: function (html) {
				
                $('#muestra_modal_post').html(html);
            }

        });
	 
 }




</script>
