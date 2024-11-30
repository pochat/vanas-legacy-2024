<?php
	# Libreria de funciones
	require("../../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  function GetNewsNotification($fl_usuario){
  	# School News Notification
	    $fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
		
        $Query  = "( SELECT fl_blog, ds_titulo, ds_resumen, ds_ruta_imagen, DATE_FORMAT(fe_blog, '%M %e, %Y')fe_blog,''fg_solicitud_recibida,fe_blog fe_blogs ";
		$Query .= "FROM c_blog ";
		$Query .= "WHERE fg_maestros='1' ";
		$Query .= "AND fe_blog <= $fe_actual ";
		$Query .= "AND DATE_ADD(fe_blog, INTERVAL ".ObtenConfiguracion(18)." DAY) >= $fe_actual ";
		$Query .= "ORDER BY fe_blog DESC ) ";
        
       #Notificaciones recibidas SOLICITUDES RECIBIDAS
		$Query.="UNION (";
		$Query.="SELECT  fl_relacion fl_blog, ''ds_titulo,''ds_resumen,fl_usuario_origen ds_ruta_imagen, DATE_FORMAT(fe_creacion , '%M-%e-%Y ')fe_blog ,'1'fg_solicitud_recibida,fe_creacion fe_blogs     
                    FROM k_relacion_usuarios WHERE fl_usuario_destinatario=$fl_usuario AND fg_aceptado='0'     
		
	   ";
		$Query.=") ";
		#solicitudes aceptadas
		$Query.="UNION (";
		$Query.="SELECT  fl_relacion fl_blog, ''ds_titulo,''ds_resumen,fl_usuario_destinatario ds_ruta_imagen, DATE_FORMAT(fe_creacion , '%M-%e-%Y ')fe_blog ,'2'fg_solicitud_recibida, fe_creacion fe_blogs     
                  FROM k_relacion_usuarios WHERE fl_usuario_origen=$fl_usuario AND fg_aceptado='1' AND fg_revisado_alumno='0'     
		
	   ";
		$Query.=")ORDER BY fe_blogs DESC ";
        
        
		$rs = EjecutaQuery($Query);

		$result = array();
		
		for($i=0; $row=RecuperaRegistro($rs);){
			$fl_blog = $row[0];
			$ds_titulo = str_uso_normal($row[1]);
			$ds_resumen = str_uso_normal($row[2]);
			$ds_ruta_imagen = str_ascii($row[3]);
			$fe_blog = $row[4];
            $fg_solicitud=$row['fg_solicitud_recibida'];
            
			if($fg_solicitud){
              $fl_usuario_=$ds_ruta_imagen;
              $fl_blog=$ds_ruta_imagen;
            }
            
			
			
			
	  	if(!empty($ds_ruta_imagen)){
	  		$ds_ruta_imagen = SP_THUMBS."/news/".str_ascii($row[3]);
	  	} else {
	  		$ds_ruta_imagen = SP_IMAGES."/".S_NEWS_THUMB_DEF;
	  	}

	  	# Remove extra tags from TinyMCE
	  	$ds_resumen = str_replace("<p>", "", $ds_resumen);
	  	$ds_resumen = str_replace("</p>", "", $ds_resumen);

	  	$row2 = RecuperaValor("SELECT COUNT(1) FROM k_not_blog WHERE fl_blog=$fl_blog AND fl_usuario=$fl_usuario");
	  	if( ($row2[0] > 0) || (!empty($fg_solicitud)) ) {
			
			if($row2[0]>0){
                  $url="#ajax/blog.php";
                  $title="School News";
                  $subject="<span class='text-danger' style='display:inline;'>(Unread) </span>".$ds_titulo;
              }else{
                  
                  
                          #Identificamos quien envio la invitacion un teacher o estudents
                          $Que="SELECT fl_perfil,fl_perfil_sp,ds_nombres,ds_apaterno,fl_instituto  FROM c_usuario WHERE fl_usuario=$fl_usuario_ ";
                          $rt=RecuperaValor($Que);
                          $fl_perfil=$rt['fl_perfil'];
                          $fl_perfil_sp=$rt['fl_perfil_sp'];
						  $fl_instituto=$rt['fl_instituto'];
                          $ds_nombre_friends=$rt['ds_nombres']." ".$rt['ds_apaterno'];
                          
                          if(($fl_perfil==PFL_MAESTRO)||($fl_perfil_sp==PFL_MAESTRO_SELF)){
                              
                              $IMG="SELECT ds_ruta_avatar FROM c_maestro WHERE fl_maestro=$fl_usuario_ ";
                              $r=RecuperaValor($IMG);
                              $nb_imagen=str_texto($r['ds_ruta_avatar']);
                              
                              
                              if(!empty($nb_imagen)){
                                  $ds_ruta_imagen=PATH_MAE_IMAGES."/avatars/$nb_imagen";
                              }else{
                                  $ds_ruta_imagen=SP_IMAGES."/".IMG_S_AVATAR_DEF; 
                              }
                              
                              if(!empty($fl_perfil_sp)){
                                  
                                  #Recupermaos la imagen
                                  $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_maestro_sp WHERE fl_maestro_sp=$fl_usuario_ ");
                                  $ds_img=str_texto($row['ds_ruta_avatar']);
                                  if($ds_img){
                                     $ds_ruta_imagen= PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_usuario_."/".$ds_img;
                                  }else{
                                     $ds_ruta_imagen=SP_IMAGES."/".IMG_S_AVATAR_DEF;
                                  }
                                  
                                  
                              
                              }
                              
                              
                              
                                  
                          }
                          if(($fl_perfil==PFL_ESTUDIANTE)||($fl_perfil==PFL_ESTUDIANTE_SELF)){
                              
                              $IMG="SELECT ds_ruta_avatar FROM c_alumno WHERE fl_alumno=$fl_usuario_ ";
                              $r=RecuperaValor($IMG);
                              $nb_imagen=str_texto($r['ds_ruta_avatar']);
                              
                              
                              if(!empty($nb_imagen)){
                                  $ds_ruta_imagen=PATH_ALU_IMAGES."/avatars/$nb_imagen";
                              }else{
                                  $ds_ruta_imagen=SP_IMAGES."/".IMG_S_AVATAR_DEF;
                              }
                              
                              if(!empty($fl_perfil_sp)){
                                  
                                  #Recupermaos la imagen
                                  $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_alumno_sp WHERE fl_alumno_sp=$fl_usuario_ ");
                                  $ds_img=str_texto($row['ds_ruta_avatar']);
                                  if($ds_img){
                                      $ds_ruta_imagen= PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_usuario_."/".$ds_img;
                                  }else{
                                      $ds_ruta_imagen=SP_IMAGES."/".IMG_S_AVATAR_DEF;
                                  }
                                  
                                  
                                  
                              }
                              
                              
                              
                              
                              
                          }
                          

                          $title="Friend Request"; 
                          $subject="<span class='text-danger' style='display:inline;'>  
                                     
                                    </span>";
                          
                         // $subject="<p style='margin:0px;'><a href='javascript:void(0);' Onclick='ConfirmarSolicitud(2,$fl_usuario_comento,$fl_usuario);' style='float:right;margin-right: 5px;' class='btn btn-primary btn-xs'>".ObtenEtiqueta(2192)."</a></p> <p><a href='javascript:void(0);' Onclick='ConfirmarSolicitud(2,$fl_usuario_comento,$fl_usuario);'  style='float:right;margin-right: 5px;' class='btn btn-success btn-xs'>".ObtenEtiqueta(2191)."</a>  </p>";
                          $url="javascript:void(0);";
                          $ds_resumen=$ds_nombre_friends;
                          $btnes="  <a href='javascript:void(0);' Onclick='ConfirmarSolicitud(1,$fl_usuario_,$fl_usuario);' style='float:right;margin-right: 5px;' class='btn btn-primary btn-xs'>".ObtenEtiqueta(2192)."</a> <a href='javascript:void(0);' Onclick='ConfirmarSolicitud(2,$fl_usuario_,$fl_usuario);'  style='float:right;margin-right: 5px;' class='btn btn-success btn-xs'>".ObtenEtiqueta(2191)."</a>";
                          
                          if($fg_solicitud==2){
                              $ds_resumen=$ds_nombre_friends. "Has accepted your invitation to connect ";
                              $btnes="<span class='text-danger' Onclick='ConfirmarSolicitud(3,$fl_usuario_,$fl_usuario);' style='float:right;display:inline;'>(Mark read) </span>";
                          }
                        
                  
              }
			
			
			
			
			
	  		$result["blog".$i] = array(
	  			"url" => $url,
					"img" => $ds_ruta_imagen,
					"title" => $title,
					"time" => $fe_blog,
					"subject" => $subject,
					"abstract" => $ds_resumen,
					"botones"=>$btnes,
                    "fl_blog"=>$fl_blog,
					"abstract_style" => ''
	  		);
	  		$i++;
	  	}
		}
		$result["size"] = array("total" => $i);
		echo json_encode((Object) $result);
  }

  function GetBoardNotification($fl_usuario){
  	$Query  = "SELECT nb_tema, a.no_posts FROM k_f_usu_tema a LEFT JOIN c_f_tema b ON a.fl_tema=b.fl_tema WHERE fl_usuario = $fl_usuario";
  	$rs = EjecutaQuery($Query);

  	# Default activity board icon
  	$ds_ruta_imagen = SP_IMAGES."/activityboard_default.jpg";
  	$result = array();

  	for($i=0; $row=RecuperaRegistro($rs);){
  		$nb_tema = $row[0];
  		$no_posts = $row[1];

  		if(!empty($nb_tema) && $no_posts > 0){
  			$result["stream".$i] = array(
	  			"url" => '#ajax/gallery.php',
					"img" => $ds_ruta_imagen,
					"title" => 'VANAS Board',
					"time" => '',
					"subject" => $nb_tema,
					"abstract" => "You have $no_posts unread posts",
		            "botones"=>"",
					"abstract_style" => ''
	  		);
	  		$i++;
  		}
  	}
  	$result["size"] = array("total" => $i);
  	echo json_encode((Object) $result);
  }

?>

<ul class="notification-body"></ul>

<script type="text/javascript">

	// School News
	var result, news, notices;
	result = <?php GetNewsNotification($fl_usuario); ?>;
	
	notices = "";
	for(var i=0; i<result.size.total; i++){
		news = result['blog'+i];
		notices += NoticeBar(news.url, news.img, news.title, news.time, news.subject, news.abstract, news.abstract_style,news.botones,news.fl_blog);	
	}
	$(".notification-body").prepend(notices);

	// VANAS Board
	var result, board, notices;
	result = <?php GetBoardNotification($fl_usuario); ?>;

	notices = "";
	for(var i=0; i<result.size.total; i++){
		board = result['stream'+i];
		notices += NoticeBar(board.url, board.img, board.title, board.time, board.subject, board.abstract, board.abstract_style,board.botones);
	}
	$(".notification-body").prepend(notices);

	function NoticeBar(url, img, title, time, subject, abstract, abstractStyle,botones,fl_blog){
		
		if(botones){
	    
	    }else{
	        botones="";
	    }
	    if(fl_blog){
	    }else{
	       fl_blog="";
	    
	    }
		
		var notice =
			"<li id='li_"+fl_blog+"'>" +
				"<span>" + 
					"<a href='"+url+"' class='msg'>" +
						"<img src='"+img+"' class='air air-top-left margin-top-5' width='40' height='40'>" +
						"<span class='from'>"+title+"</span>" +
						"<time>"+time+"</time>" +
						"<span class='subject'>"+subject+"</span>" +		
						"<span class='msg-body' title='"+abstract+"' style='"+abstractStyle+"'>"+abstract+"</span>" +
						" "+botones+""+
					"</a>" +
				"</span>" +
			"</li>";
		return notice;
	}
</script>


<script>


    function ConfirmarSolicitud(fg_tipo_respuesta,fl_usuario_origen,fl_usuario_actual){

        var fl=1;


    
        $.ajax({
            type: 'POST',
            url: 'ajax/notify/marcar_solicitud_usuarios.php',
            data:'fg_tipo_respuesta='+fg_tipo_respuesta+
                  '&fl_usuario_origen='+fl_usuario_origen+
                  '&fl_usuario_actual='+fl_usuario_actual,

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
       
                var tot_notity=$('#tot_notity_'+fl_usuario_actual).text();//representa el primer circulo 
                $('#tot_notity_'+fl_usuario_actual).empty();//limpiamos el numerador
                var t = parseInt(tot_notity)-1;//obtenemos el nuevo total.
                $('#tot_notity_'+fl_usuario_actual).append(t);//Colocamos el nuevo total.

                var no_notices = $('#no_notices').text();
                $('#no_notices').empty();
                var n = parseInt(no_notices)-1;
                $('#no_notices').append(n);


            }
        //Enviamos notificction d aceptacion de amistad
            if(fg_tipo_respuesta==2){
		   
                socket.emit('solicitud-amistadaceptada',fl_usuario_confirma_solicitud,fl_usuario_destino,nb_usuario_confirma_solicitud);
		   
            }

        });

    }

</script>

