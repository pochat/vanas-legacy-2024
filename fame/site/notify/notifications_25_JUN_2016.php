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
  
  function GetBoardNotification($fl_usuario){
    # Para los comentarios en los diferentes post
  
    $Query  ="SELECT nb_programa, b.fl_usuario, b.ds_comment, DATE_FORMAT(b.fe_comment , '%Y-%m-%d %H:%i:%s'), a.fl_gallery_post_sp, ";
    $Query .= "b.fg_read, fl_gallery_comment_sp, a.nb_archivo, a.fl_usuario ,'0' fg_confirmacion_email ";
    $Query .= "FROM k_gallery_post_sp a ";
    $Query .= "JOIN k_gallery_comment_sp b ON(b.fl_gallery_post_sp=a.fl_gallery_post_sp) ";
    $Query .= "LEFT JOIN c_programa_sp c ON(c.fl_programa_sp=a.fl_programa_sp OR a.fl_programa_sp is null ) ";
    $Query .= "WHERE a.fl_usuario=".$fl_usuario." AND b.fl_usuario<>".$fl_usuario." ";
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
    $Query3 .= "SELECT nb_programa, a.fl_usuario, a.ds_comment, DATE_FORMAT(a.fe_comment , '%Y-%m-%d %H:%i:%s'), a.fl_gallery_post_sp, ";
    $Query3 .= "a.fg_read, a.fl_gallery_comment_sp, b.nb_archivo, b.fl_usuario,'0' fg_confirmacion_email ";
    $Query3 .= "FROM k_gallery_comment_sp a ";
    $Query3 .= "JOIN k_gallery_post_sp b ON(a.fl_gallery_post_sp=b.fl_gallery_post_sp) ";
    $Query3 .= "LEFT JOIN c_programa_sp c ON(b.fl_programa_sp=c.fl_programa_sp OR b.fl_programa_sp IS NULL) ";
    $Query3 .= "WHERE a.fl_gallery_post_sp IN(SELECT DISTINCT fl_gallery_post_sp fl_gallery_post_sp_comentado FROM k_gallery_comment_sp WHERE fl_usuario=$fl_usuario) ";
    $Query3 .= "AND a.fl_usuario<>$fl_usuario OR b.fl_usuario=$fl_usuario ";
    $Query3 .= "ORDER BY a.fe_comment DESC ";
    $Query3 .=") UNION (";
    $Query3 .="
             SELECT P.nb_programa, A.fl_alumno_beneficiado fl_usuario ,A.ds_email ds_comment,A.fe_creacion fe_fecha, 
             fl_confirmacion_email_curso fl_gallery_post_sp, A.fg_revisado_alumno fg_read,fl_confirmacion_email_curso fl_gallery_comment_sp, P.nb_thumb nb_archivo,A.fl_alumno_beneficiado 
             ,'1' fg_confirmacion_email 
            FROM k_confirmacion_email_curso A
            JOIN c_programa_sp P ON P.fl_programa_sp=A.fl_programa_sp 
            WHERE A.fl_alumno_beneficiado=$fl_usuario AND A.fg_revisado_alumno='0' ORDER BY fl_confirmacion_email_curso DESC 
    ";
    $Query3.=") UNION ( ";
    $Query3.=" SELECT b.nb_programa,a.fl_usuario_sp fl_usuario,''ds_comment,DATE_FORMAT(a.fe_creacion , '%Y-%m-%d %H:%i:%s'),''fl_gallery_post_sp,a.fg_revisado_alumno fg_read  
                ,a.fl_usu_pro fl_gallery_comment_sp, b.nb_thumb nb_archivo,a.fl_usuario_sp,'2'fg_confirmacion_email
               FROM k_usuario_programa a 
			   JOIN c_programa_sp b ON b.fl_programa_sp=a.fl_programa_sp WHERE fl_usuario_sp=$fl_usuario AND a.fg_revisado_alumno='0'
                
    ";
    $Query3.=") ";
    
    

    
    
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
      $fg_confirmacion_email=$rowk3[9];
      
      $fg_confrimacion_em=0;
      $url2='view_post('.$fl_gallery_post_sp.', '.$fl_gallery_comment_sp.')';
      
      
      
      if($fg_confirmacion_email==1){
          
          $ds_user_comento=ObtenEtiqueta(2119);
          $ds_comment="Course: ".$nb_programa."<br/> ".ObtenEtiqueta(2122).": ".$ds_comment;
          $avatar=PATH_HOME.'/modules/fame/uploads/'.$rowk3[7];
          $url="";
          $nb_archivo="";
          $fg_confrimacion_em=1;
          $url2="";
      }
      if($fg_confirmacion_email==2){
          $ds_user_comento=ObtenEtiqueta(2134);
          $ds_comment=$nb_programa;
          $avatar=PATH_HOME.'/modules/fame/uploads/'.$rowk3[7];
          $url="";
          $nb_archivo="";
          $fg_confrimacion_em=2;
          $url2="";
          $fg_read=null;
         
          
          
      }
      
      
      
      if(empty($fg_read))
          $read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_gallery_comment_sp."'><small class='text-primary' onclick='Mark(0,0,".$fl_gallery_comment_sp.",".$fg_confrimacion_em.");'><i class='fa fa-circle'></i> ".ObtenEtiqueta(1832)."</small></div>";
      else
        $read = "<div class='cursor-pointer text-align-right' id='noti_".$fl_gallery_comment_sp."'><small onclick='Mark(0,1,".$fl_gallery_comment_sp.");'><i class='fa fa-circle-o'></i> ".ObtenEtiqueta(1833)."</small></div>";     
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
  for(var j=0; j<result2.size.total; j++){
    board2 = result2['streamotros'+j];
		notices += NoticeBar(board2.url2, board2.img2, board2.title2, board2.time2, board2.subject2, board2.abstract2, board2.abstract_style2, board2.comentario2, board2.read2, 
    board2.fg_read2, board2.fl_gallery_comment_sp2, board2.img_post2, board2.fl_gallery_comment_sp_2);
  }
	$(".notification-body").prepend(notices);

	

	function NoticeBar(url, img, title, time, subject, abstract, abstractStyle, comentario, read, fg_read, fl_gallery_comment_sp, img_post, fl_gallery_comment_sp_2){
		var read_unread = "unread";
		if(fg_read == 1)
		  read_unread = "read";
			var notice =
		  "<li id='comments_fame'><span id='li_"+fl_gallery_comment_sp_2+"' class='"+read_unread+"'>"+
			"<a href='javascript:"+url+"' style='padding-left:50px !important; line-height:15px;'>"+
			  "<img src='"+img+"' alt='' class='air air-top-left margin-top-5' height='40' width='40' />"+
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

	function Mark(all_comments=0, mark, post,fg_confirmacion_email){
    
    
    // Variables
    var data;
    if(all_comments==1){
      data = "all_comments="+all_comments;
    }
    else{
        data = "fg_read_r="+mark+"&fl_gallery_comment_sp="+post+"&fg_confirmacion_email="+fg_confirmacion_email+"&all_comments="+all_comments
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
        // Limpaimos las notificaciones
        $("#activity>b").empty();
        
        
        // Mark read all comments        
        if(all_comments==1){
          $("#notify_comments").empty();
          $("#no_news").empty();
          $("#notify_comments").append(content.all_comments);
          var r = parseInt(tot_notifications) - content.all_comments_total;
          var n = parseInt(news) - content.all_comments_total;
          $("#activity>b").append(r);
          $("#no_news").append(n);
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
            $("#activity>b").append(s);           
            $("#no_news").append(n);
            
          }
          else{
            // cambiamos el estado del la notificacio
            elem_il.removeClass('unread');
            var v = parseInt(tot_notifications) - 1;
            var n = parseInt(news)-1;
            $("#activity>b").append(v);
            $("#no_news").append(n);
          }
        }
    });
  }
</script>