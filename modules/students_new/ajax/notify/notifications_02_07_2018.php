<?php
	# Libreria de funciones
	require("../../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  function GetNewsNotification($fl_usuario){
  	# School News Notification
	  $fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
		$Query  = "SELECT fl_blog, ds_titulo, ds_resumen, ds_ruta_imagen, DATE_FORMAT(fe_blog, '%M %e, %Y') ";
		$Query .= "FROM c_blog ";
		$Query .= "WHERE fg_alumnos='1' ";
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

  function GetReminderNotification($fl_usuario){
		# Inicializa variables
		$result = array();
	  $fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";

		# Tabla de Reminders
	  $opt = 0;
	  $no_reg = 0;
	  $default_url = 'javascript:void(0);';
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
		  $reminder[$opt][3] = $default_url;
		  $reminder[$opt][4] = "";
		  $reminder[$opt][5] = "";
		  $opt++;
		}
		
		# Reminder para recordar las fechas de pago 
    
    # Recupera el programa y term que esta cursando el alumno
    $fl_programa = ObtenProgramaAlumno($fl_usuario);
    $fl_term = ObtenTermAlumno($fl_usuario);
    
    # Recupera la sesion
    $Query  = "SELECT cl_sesion ";
    $Query .= "FROM c_usuario ";
    $Query .= "WHERE fl_usuario=$fl_usuario";
    $row = RecuperaValor($Query);
    $cl_sesion = $row[0];
  
    # Recupera el term inicial
    $Query  = "SELECT fl_term_ini ";
    $Query .= "FROM k_term ";
    $Query .= "WHERE fl_programa=$fl_programa";
    $Query .= "AND fl_term=$fl_term";
    $row = RecuperaValor($Query);
    $fl_term_ini = $row[0];
    
    # Recupera el tipo de pago para el curso
    $Query  = "SELECT fg_opcion_pago ";
    $Query .= "FROM k_app_contrato ";
    $Query .= "WHERE cl_sesion='$cl_sesion'"; 
    $row = RecuperaValor($Query);
    $fg_opcion_pago = $row[0];
    
    if(empty($fl_term_ini))
      $fl_term_ini=$fl_term;
    
    # Recupera informacion de los pagos
    $Query  = "SELECT fl_term_pago, fe_pago ";
    $Query .= "FROM k_term_pago ";
    $Query .= "WHERE fl_term=$fl_term_ini ";
    $Query .= "AND no_opcion=$fg_opcion_pago";
    $rs = EjecutaQuery($Query);
    for($i=0; $row = RecuperaRegistro($rs); $i++) {
      $fl_term_pago = $row[0];
      $fe_limite_pago = $row[1];
      
      $Query  = "SELECT fl_term_pago ";
      $Query .= "FROM k_alumno_pago ";
      $Query .= "WHERE fl_term_pago=$fl_term_pago ";
      $Query .= "AND fl_alumno=$fl_usuario";
      $row = RecuperaValor($Query);
      $fl_t_pago = $row[0];
      
      if(empty($fl_t_pago)) {
        if(empty($proximo_pago)){
          $proximo_pago=$fl_term_pago;
          $etiqueta_reminder="Payment due date";
          $url = "#ajax/tuition_payment.php";
          $pay_now = "Click here to pay now!";
          $abstract_style = "color: #3276b1;";
        }
        else {
          $etiqueta_reminder="Payment due date";
          $pay_now = "";
          $abstract_style = "";
        }
      
        $fecha_actual = ObtenFechaActual(); 
        $fe_reminder = strtotime ('-2 weeks', strtotime($fe_limite_pago));
        $fe_reminder = date( 'Y-m-d', $fe_reminder); 

        if ($fecha_actual>=$fe_reminder) {
        
          # Arma arreglo con datos de query
          $reminder[$opt][0] = $fe_limite_pago;
          $reminder[$opt][1] = $etiqueta_reminder;
          $reminder[$opt][2] = '';
          $reminder[$opt][3] = $url;
          $reminder[$opt][4] = $pay_now;
          $reminder[$opt][5] = $abstract_style;
          $opt++;
        }
      }
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
		  $reminder[$opt][3] = $default_url;
		  $reminder[$opt][4] = "";
		  $reminder[$opt][5] = "";
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
		  $reminder[$opt][3] = $default_url;
		  $reminder[$opt][4] = "";
		  $reminder[$opt][5] = "";
		  $opt++;
		}

		# Assignment overdue reminder
		$Query6  = "SELECT a.fl_entrega_semanal, b.fe_entrega, c.no_semana, c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch ";
		$Query6 .= "FROM k_entrega_semanal a ";
		$Query6 .= "LEFT JOIN k_semana b ON b.fl_semana=a.fl_semana ";
		$Query6 .= "LEFT JOIN c_leccion c ON c.fl_leccion=b.fl_leccion ";
		$Query6 .= "WHERE a.fl_alumno=$fl_usuario ";
		$Query6 .= "AND a.fl_grupo=$fl_grupo ";
		$Query6 .= "AND a.fg_entregado='0' ";
		$Query6 .= "AND a.fl_promedio_semana IS NULL ";
		$Query6 .= "AND (c.fg_animacion='1' OR c.fg_ref_animacion='1' OR c.no_sketch>0 OR c.fg_ref_sketch='1') ";
		$rs6 = EjecutaQuery($Query6);

		$no_reg = $no_reg + CuentaRegistros($rs6);
		while($row1 = RecuperaRegistro($rs6)){
			$fl_entrega_semanal = $row1[0];
			$fe_entrega = $row1[1];
			$no_semana = $row1[2];
			$fg_animacion = $row1[3];
			$fg_ref_animacion = $row1[4];
			$no_sketch = $row1[5];
			$fg_ref_sketch = $row1[6];

			$abstract = "Missing ";
			if(!empty($fg_animacion)){
				$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='A' AND fl_entrega_semanal=$fl_entrega_semanal");
				$tot_assignment = $row[0];
				$abstract .= ($tot_assignment > 0) ? "" : "Assignment,";
			}
			if(!empty($fg_ref_animacion)){
				$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='AR' AND fl_entrega_semanal=$fl_entrega_semanal");
				$tot_assignment_ref = $row[0];
				$abstract .= ($tot_assignment_ref > 0) ? "" : " Assignment Reference,";
			}
			if(!empty($no_sketch)){
				$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='S' AND fl_entrega_semanal=$fl_entrega_semanal");
		  	$tot_sketch = $row[0];
		  	$abstract .= ($tot_sketch >= $no_sketch) ? "" : " ".$no_sketch-$tot_sketch." Sketch(es),";
			}
			if(!empty($fg_ref_sketch)){
				$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='SR' AND fl_entrega_semanal=$fl_entrega_semanal");
		  	$tot_sketch_ref = $row[0];
		  	$abstract .= ($tot_sketch_ref > 0) ? "" : " Sketch Reference";
			}

			$reminder[$opt][0] = $fe_entrega;
		  $reminder[$opt][1] = "Week $no_semana incomplete assignment(s)";
		  $reminder[$opt][2] = '';
		  $reminder[$opt][3] = "#ajax/desktop_upload.php?week=$no_semana";
		  $reminder[$opt][4] = rtrim($abstract,",");
		  $reminder[$opt][5] = "";
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

				$result["reminder".$i] = array(
					"url" => $reminder[$i][3],
					"img" => SP_IMAGES."/".ObtenNombreImagen(213),
					"title" => "Reminder",
					"time" => $fecha,
					"subject" => $reminder[$i][1].$reminder[$i][2],
					"abstract" => $reminder[$i][4],
					"abstract_style" => $reminder[$i][5]
				); 
				
	    }
	    $result["size"] = array("total" => $i);
	  }
  	echo json_encode((Object) $result);
  }
?>

<ul class="notification-body"></ul>

<script type="text/javascript">

	// School News
	var result, news, notices;
	result = <?php GetNewsNotification($fl_usuario); ?>;
	
	console.dir(result);

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

	// Reminders
	var result, reminder, notices;
	result = <?php GetReminderNotification($fl_usuario); ?>;

	notices = "";
	for(var i=0; i<result.size.total; i++){
		reminder = result['reminder'+i];
		notices += NoticeBar(reminder.url, reminder.img, reminder.title, reminder.time, reminder.subject, reminder.abstract, reminder.abstract_style);
	}
	$(".notification-body").append(notices);
	

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