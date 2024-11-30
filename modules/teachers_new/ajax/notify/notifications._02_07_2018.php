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
		$Query  = "SELECT fl_blog, ds_titulo, ds_resumen, ds_ruta_imagen, DATE_FORMAT(fe_blog, '%M %e, %Y') ";
		$Query .= "FROM c_blog ";
		$Query .= "WHERE fg_maestros='1' ";
		$Query .= "AND fe_blog <= $fe_actual ";
		$Query .= "AND DATE_ADD(fe_blog, INTERVAL ".ObtenConfiguracion(18)." DAY) >= $fe_actual ";
		$Query .= "ORDER BY fe_blog DESC";
		$rs = EjecutaQuery($Query);

		$result = array();
		
		for($i=0; $row=RecuperaRegistro($rs);){
			$fl_blog = $row[0];
			$ds_titulo = str_uso_normal($row[1]);
			$ds_resumen = str_uso_normal($row[2]);
			$ds_ruta_imagen = str_ascii($row[3]);
			$fe_blog = $row[4];

	  	if(!empty($ds_ruta_imagen)){
	  		$ds_ruta_imagen = SP_THUMBS."/news/".str_ascii($row[3]);
	  	} else {
	  		$ds_ruta_imagen = SP_IMAGES."/".S_NEWS_THUMB_DEF;
	  	}

	  	# Remove extra tags from TinyMCE
	  	$ds_resumen = str_replace("<p>", "", $ds_resumen);
	  	$ds_resumen = str_replace("</p>", "", $ds_resumen);

	  	$row2 = RecuperaValor("SELECT COUNT(1) FROM k_not_blog WHERE fl_blog=$fl_blog AND fl_usuario=$fl_usuario");
	  	if($row2[0] > 0){
	  		$result["blog".$i] = array(
	  			"url" => '#ajax/blog.php',
					"img" => $ds_ruta_imagen,
					"title" => 'School News',
					"time" => $fe_blog,
					"subject" => "<span class='text-danger' style='display:inline;'>(Unread) </span>".$ds_titulo,
					"abstract" => $ds_resumen,
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
		notices += NoticeBar(news.url, news.img, news.title, news.time, news.subject, news.abstract, news.abstract_style);	
	}
	$(".notification-body").prepend(notices);

	// VANAS Board
	var result, board, notices;
	result = <?php GetBoardNotification($fl_usuario); ?>;

	notices = "";
	for(var i=0; i<result.size.total; i++){
		board = result['stream'+i];
		notices += NoticeBar(board.url, board.img, board.title, board.time, board.subject, board.abstract, board.abstract_style);
	}
	$(".notification-body").prepend(notices);

	function NoticeBar(url, img, title, time, subject, abstract, abstractStyle){
		var notice =
			"<li>" +
				"<span>" + 
					"<a href='"+url+"' class='msg'>" +
						"<img src='"+img+"' class='air air-top-left margin-top-5' width='40' height='40'>" +
						"<span class='from'>"+title+"</span>" +
						"<time>"+time+"</time>" +
						"<span class='subject'>"+subject+"</span>" +		
						"<span class='msg-body' title='"+abstract+"' style='"+abstractStyle+"'>"+abstract+"</span>" +
					"</a>" +
				"</span>" +
			"</li>";
		return notice;
	}
</script>