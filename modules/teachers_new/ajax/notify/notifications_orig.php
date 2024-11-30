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
		$Query  = "SELECT fl_blog, ds_titulo, ds_resumen, ds_ruta_imagen, fe_blog ";
		$Query .= "FROM c_blog ";
		$Query .= "WHERE fg_maestros='1' ";
		$Query .= "AND fe_blog <= $fe_actual ";
		$Query .= "AND DATE_ADD(fe_blog, INTERVAL ".ObtenConfiguracion(18)." DAY) >= $fe_actual ";
		$Query .= "ORDER BY fe_blog DESC";
		$rs = EjecutaQuery($Query);

		// default news icon
		$ds_ruta_imagen = SP_IMAGES."/".S_NEWS_THUMB_DEF;
		$result["icon"] = array("ds_ruta_imagen" => $ds_ruta_imagen);

		$result["blogs"] = array();
		
		$total = 0;
		for($i=0; $row=RecuperaRegistro($rs); $i++){
			$fl_blog = $row[0];
			$ds_titulo = str_uso_normal($row[1]);
			$ds_resumen = str_uso_normal($row[2]);
			$ds_ruta_imagen = str_ascii($row[3]);
			$ds_date = $row[4];
	  	$fe_blog = date("F j, Y", strtotime($ds_date));

	  	if(!empty($ds_ruta_imagen)){
	  		$ds_ruta_imagen = SP_THUMBS."/news/".str_ascii($row[3]);
	  	} else {
	  		$ds_ruta_imagen = SP_IMAGES."/".S_NEWS_THUMB_DEF;
	  	}

	  	$row2 = RecuperaValor("SELECT COUNT(1) FROM k_not_blog WHERE fl_blog=$fl_blog AND fl_usuario=$fl_usuario");
	  	if($row2[0] > 0){
	  		$result["blogs"] += array(
	  			"fl_blog$total" => $fl_blog,
	  			"ds_titulo$total" => $ds_titulo,
	  			"ds_resumen$total" => $ds_resumen,
	  			"ds_ruta_imagen$total" => $ds_ruta_imagen,
	  			"fe_blog$total" => $fe_blog
	  		);
	  		$total++;
	  	}
		}
		$result["size"] = array("total" => $total);
		echo json_encode((Object) $result);
  }

  function GetBoardNotification($fl_usuario){
  	$Query  = "SELECT nb_tema, a.no_posts FROM k_f_usu_tema a LEFT JOIN c_f_tema b ON a.fl_tema=b.fl_tema WHERE fl_usuario = $fl_usuario";
  	$rs = EjecutaQuery($Query);

  	// default activity board icon
  	$ds_ruta_imagen = SP_IMAGES."/activityboard_default.jpg";
  	$result["icon"] = array("ds_ruta_imagen" => $ds_ruta_imagen);

  	$result["streams"] = array();
  	$total = 0;
  	for($i=0; $row=RecuperaRegistro($rs); $i++){
  		$nb_tema = $row[0];
  		$no_posts = $row[1];

  		if(!empty($nb_tema)){
  			$result["streams"] += array(
	  			"nb_tema$total" => $nb_tema,
	  			"no_posts$total" => $no_posts
	  		);
	  		$total++;
  		}
  		
  	}
  	$result["size"] = array("total" => $total);
  	echo json_encode((Object) $result);
  }

  /*function GetReminderNotification($fl_usuario){
		# Inicializa variables
		$result["reminder"] = array();
	  $fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";

		# Tabla de Reminders
	  $opt = 0;
	  $no_reg = 0;
	  $reminder = array( );
	  $diferencia = RecuperaDiferenciaGMT( );

		// Reminders para alumnos
		#Query para traer fechas de Q&A Live Sessions
		$res = RecuperaValor("SELECT fl_grupo FROM k_alumno_grupo WHERE fl_alumno=$fl_usuario");
		$fl_grupo = $res[0];
		$Query1  = "SELECT (DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), 'Q&A Live Session ' ";
		$Query1 .= "FROM k_clase ";
		$Query1 .= "WHERE fe_clase >= $fe_actual ";
		$Query1 .= "AND fl_grupo = $fl_grupo ";
		$Query1 .= "ORDER BY fe_clase ";
		$rs1 = EjecutaQuery($Query1);
		
		#Arma arreglo con datos de query
		$no_reg = $no_reg + CuentaRegistros($rs1);
		while($row1 = RecuperaRegistro($rs1))
		{
		  $reminder[$opt][0] = $row1[0];
		  $reminder[$opt][1] = $row1[1];
		  $reminder[$opt][2] = '';
		  $opt++;
		}
		
		#Query para traer fechas lImite de pago de colegiatura para alumnos
		$Query3  = "SELECT (DATE_ADD(fe_pago, INTERVAL $diferencia HOUR)), 'Payment 1 due date ' ";
		$Query3 .= "FROM k_alumno_term a, k_term b, c_periodo c ";
		$Query3 .= "WHERE a.fl_term=b.fl_term ";
		$Query3 .= "AND b.fl_periodo=c.fl_periodo ";
		$Query3 .= "AND a.fl_alumno=$fl_usuario ";
		$Query3 .= "AND fe_pago >= $fe_actual ";
		$rs3 = EjecutaQuery($Query3);
		
		#Arma arreglo con datos de query
		$no_reg = $no_reg + CuentaRegistros($rs3);
		while($row1 = RecuperaRegistro($rs3))
		{
		  $reminder[$opt][0] = $row1[0];
		  $reminder[$opt][1] = $row1[1];
		  $reminder[$opt][2] = '';
		  $opt++;
		}
		
		#Query para traer fechas lImite de pago de colegiatura para alumnos
		$Query3  = "SELECT (DATE_ADD(fe_pago2, INTERVAL $diferencia HOUR)), 'Payment 2 due date ' ";
		$Query3 .= "FROM k_alumno_term a, k_term b, c_periodo c ";
		$Query3 .= "WHERE a.fl_term=b.fl_term ";
		$Query3 .= "AND b.fl_periodo=c.fl_periodo ";
		$Query3 .= "AND a.fl_alumno=$fl_usuario ";
		$Query3 .= "AND fe_pago2 >= $fe_actual ";
		$rs3 = EjecutaQuery($Query3);
		
		#Arma arreglo con datos de query
		$no_reg = $no_reg + CuentaRegistros($rs3);
		while($row1 = RecuperaRegistro($rs3))
		{
		  $reminder[$opt][0] = $row1[0];
		  $reminder[$opt][1] = $row1[1];
		  $reminder[$opt][2] = '';
		  $opt++;
		}
		
		#Query para traer fechas lImite de pago de colegiatura para alumnos
		$Query3  = "SELECT (DATE_ADD(fe_pago3, INTERVAL $diferencia HOUR)), 'Payment 3 due date ' ";
		$Query3 .= "FROM k_alumno_term a, k_term b, c_periodo c ";
		$Query3 .= "WHERE a.fl_term=b.fl_term ";
		$Query3 .= "AND b.fl_periodo=c.fl_periodo ";
		$Query3 .= "AND a.fl_alumno=$fl_usuario ";
		$Query3 .= "AND fe_pago3 >= $fe_actual ";
		$rs3 = EjecutaQuery($Query3);
		
		#Arma arreglo con datos de query
		$no_reg = $no_reg + CuentaRegistros($rs3);
		while($row1 = RecuperaRegistro($rs3))
		{
		  $reminder[$opt][0] = $row1[0];
		  $reminder[$opt][1] = $row1[1];
		  $reminder[$opt][2] = '';
		  $opt++;
		}
		
		#Query para traer fechas lImite de entrega de trabajos para alumnos
		$res = RecuperaValor("SELECT  MAX(fl_term) FROM k_alumno_term WHERE fl_alumno = $fl_usuario");
		$Query4  = "SELECT (DATE_ADD(fe_entrega, INTERVAL $diferencia HOUR)), 'Submission due date ' ";
		$Query4 .= "FROM k_semana ";
		$Query4 .= "WHERE fe_entrega >= $fe_actual ";
		$Query4 .= "AND fl_term = $res[0] ";
		$rs4 = EjecutaQuery($Query4);
		
		#Arma arreglo con datos de query
		$no_reg = $no_reg + CuentaRegistros($rs4);
		while($row1 = RecuperaRegistro($rs4))
		{
		  $reminder[$opt][0] = $row1[0];
		  $reminder[$opt][1] = $row1[1];
		  $reminder[$opt][2] = '';
		  $opt++;
		}
		
		#Query para traer fechas de cumpleanios de classmates
		$Query5  = "SELECT MAKEDATE(CASE WHEN dayofyear(fe_nacimiento) < dayofyear($fe_actual) THEN year($fe_actual)+1 ELSE year($fe_actual) END, ";
		$Query5 .= "        CASE WHEN (dayofyear(fe_nacimiento)>59 ";
		$Query5 .= "              AND ((year(fe_nacimiento)%4=0 AND year(fe_nacimiento)%100>0) OR year(fe_nacimiento)%400=0) ";
		$Query5 .= "                AND ((year($fe_actual)%4>0 OR year($fe_actual)%100=0) ";
		$Query5 .= "                  AND year($fe_actual)%400>0)) ";
		$Query5 .= "            THEN dayofyear(fe_nacimiento)-1 ";
		$Query5 .= "            WHEN (dayofyear(fe_nacimiento)>59 ";
		$Query5 .= "              AND ((year(fe_nacimiento)%4>0 OR year(fe_nacimiento)%100=0) AND year(fe_nacimiento)%400>0) ";
		$Query5 .= "                AND ((year($fe_actual)%4=0 AND year($fe_actual)%100>0) ";
		$Query5 .= "                  OR year($fe_actual)%400=0)) ";
		$Query5 .= "            THEN dayofyear(fe_nacimiento)+1 ";
		$Query5 .= "            ELSE dayofyear(fe_nacimiento) ";
		$Query5 .= "            END) fe_cumple, ";
		$Query5 .= "a.ds_nombres, a.ds_apaterno, ' birthday! ' ";
		$Query5 .= "FROM c_usuario a, k_alumno_grupo b ";
		$Query5 .= "WHERE a.fl_usuario = b.fl_alumno ";
		$Query5 .= "AND MAKEDATE(CASE WHEN dayofyear(fe_nacimiento) < dayofyear($fe_actual) THEN year($fe_actual)+1 ELSE year($fe_actual) END, ";
		$Query5 .= "        CASE WHEN (dayofyear(fe_nacimiento)>59 ";
		$Query5 .= "              AND ((year(fe_nacimiento)%4=0 AND year(fe_nacimiento)%100>0) OR year(fe_nacimiento)%400=0) ";
		$Query5 .= "                AND ((year($fe_actual)%4>0 OR year($fe_actual)%100=0) ";
		$Query5 .= "                  AND year($fe_actual)%400>0)) ";
		$Query5 .= "            THEN dayofyear(fe_nacimiento)-1 ";
		$Query5 .= "            WHEN (dayofyear(fe_nacimiento)>59 ";
		$Query5 .= "              AND ((year(fe_nacimiento)%4>0 OR year(fe_nacimiento)%100=0) AND year(fe_nacimiento)%400>0) ";
		$Query5 .= "                AND ((year($fe_actual)%4=0 AND year($fe_actual)%100>0) ";
		$Query5 .= "                  OR year($fe_actual)%400=0)) ";
		$Query5 .= "            THEN dayofyear(fe_nacimiento)+1 ";
		$Query5 .= "            ELSE dayofyear(fe_nacimiento) ";
		$Query5 .= "            END) >= $fe_actual ";
		$Query5 .= "AND b.fl_grupo = $fl_grupo";
		
		$rs5 = EjecutaQuery($Query5);
		
		#Arma arreglo con datos de query
		$no_reg = $no_reg + CuentaRegistros($rs5);
		while($row1 = RecuperaRegistro($rs5))
		{
		  $reminder[$opt][0] = $row1[0];
		  $reminder[$opt][1] = $row1[1].' '.$row1[2].' '.$row1[3];
		  $reminder[$opt][2] = '';
		  $opt++;
		}

		# Presenta reminders
	  if($no_reg < 5)
	    $n = $no_reg;
	  else
	    $n = 5;
	  if($n > 0) {
	    $rem = sort($reminder);
	    for($i=0; $i<$n; $i++) {
	      $var = $reminder[$i][0];
	      $mes = substr($var, 5, 2);
	      $dia = substr($var, 8, 2);
	      $anio = substr($var, 0, 4);
	      $hora = substr($var, 11, 5);
	      $fecha = ObtenNombreMes($mes)." ".$dia.", ".$anio." ".$hora;

				$result["reminder"] += array(
					"title$i" => $reminder[$i][1].$reminder[$i][2],
					"ds_ruta_imagen$i" => SP_IMAGES."/".ObtenNombreImagen(213),
					"fecha$i" => $fecha
				);
	    }
	    $result["size"] = array("total" => $i);
	  }
  	echo json_encode((Object) $result);
  }*/
  
?>

<ul class="notification-body"></ul>

<script type="text/javascript">
	// School News
	var result = <?php GetNewsNotification($fl_usuario); ?>;
	var news = result.blogs;

	if(result.size.total == 0){
		var notice = NoticeBar('#ajax/blog.php', result.icon.ds_ruta_imagen, 'School News', "", 'There are no new school news for you!', "");
		$(".notification-body").prepend(notice);
	} else {
		var notices = "";
		for(var i=0; i<result.size.total; i++){
			notices += NoticeBar('#ajax/blog.php', news["ds_ruta_imagen"+i], 'School News', news["fe_blog"+i], news["ds_titulo"+i], news["ds_resumen"+i]);	
		}
		$(".notification-body").prepend(notices);
	}

	// Activity Board
	var result = <?php GetBoardNotification($fl_usuario); ?>;
	var gallery = result.streams;

	var notices = "";
	for(var i=0; i<result.size.total; i++){
		if(gallery["no_posts"+i] != 0){
			notices += NoticeBar('#ajax/gallery.php', result.icon.ds_ruta_imagen, 'Activity Board', "", gallery["nb_tema"+i], "You have "+gallery["no_posts"+i]+" unread posts");
		}
	}
	if(notices == ""){
		notices += NoticeBar('#ajax/gallery.php', result.icon.ds_ruta_imagen, 'Activity Board', "", 'There are no new gallery posts for you!', "");
	}
	$(".notification-body").prepend(notices);

	// Reminders
	var result = <?php GetReminderNotification($fl_usuario); ?>;
	var reminders = result.reminder;

	var notices = "";
	for(var i=0; i<result.size.total; i++){
		notices += NoticeBar('javascript:void(0);', reminders["ds_ruta_imagen"+i], 'Reminder', reminders["fecha"+i], reminders["title"+i],"");
	}
	$(".notification-body").append(notices);
	

	function NoticeBar(url, img, title, time, subject, abstract){
		var notice =
			"<li>" +
				"<span>" + 
					"<a href='"+url+"' class='msg'>" +
						"<img src='"+img+"' class='air air-top-left margin-top-5' width='40' height='40'>" +
						"<span class='from'>"+title+"</span>" +
						"<time>"+time+"</time>" +
						"<span class='subject'>"+subject+"</span>" +		
						"<span class='msg-body'>"+abstract+"</span>" +
					"</a>" +
				"</span>" +
			"</li>";
		return notice;
	}
	
</script>