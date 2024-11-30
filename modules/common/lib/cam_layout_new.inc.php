<?php

	# Header para todas las paginas
	function CreateMenu() {
		$fl_usuario = ObtenUsuario(False);
		$fl_perfil = ObtenPerfil($fl_usuario);
		$nb_usuario = ObtenNombreUsuario($fl_usuario);
		$ruta_avatar = ObtenAvatarUsuario($fl_usuario);

		# Initializes the left menu list
		$page_nav = array();

	  # Presenta menu en Columna izquierda
		if($fl_perfil == PFL_MAESTRO) {
			$menu = MENU_MAESTROS;
			$path_nodo = PAGINA_NOD_MAE;
			$pag_fija = 6;
		}
		else {
			$menu = MENU_ALUMNOS;
			$path_nodo = PAGINA_NOD_ALU;
			$pag_fija = 4;
		}

	  # Retrieves the descriptions of the modules 
		$Query  = "SELECT fl_modulo, nb_modulo, tr_modulo ";
		$Query .= "FROM c_modulo ";
		$Query .= "WHERE fl_modulo_padre=$menu ";
		$Query .= "AND fg_menu='1' ";
		$Query .= "ORDER BY no_orden";
		$rs = EjecutaQuery($Query);
		for($i = 1; $row = RecuperaRegistro($rs); $i++) {
			$fl_modulo[$i] = $row[0];
			$nb_modulo[$i] = str_texto(EscogeIdioma($row[1], $row[2]));
			$Query  = "SELECT fl_funcion, nb_funcion, tr_funcion, nb_flash_default, tr_flash_default ";
			$Query .= "FROM c_funcion ";
			$Query .= "WHERE fl_modulo=$fl_modulo[$i] ";
			$Query .= "AND fg_menu='1' ";
			$Query .= "ORDER BY no_orden";
			$rs2 = EjecutaQuery($Query);
			for($j = 1; $row2 = RecuperaRegistro($rs2); $j++) {
				$fl_funcion[$i][$j] = $row2[0];
				$nb_funcion[$i][$j] = str_texto(EscogeIdioma($row2[1], $row2[2]));
				$nb_icono[$i][$j] = str_uso_normal(EscogeIdioma($row2[3], $row2[4]));
			}
			$tot_submodulos[$i] = $j-1;
		}
		$tot_modulos = $i-1;

	  # MDB Livesession
		$liveSessionDisplay = "";
		$clavesModulosTeacher = Array();
		$clavesModulosTeacher["liveSession"] = 76;
		$clavesModulosTeacher["desk"] = 13;
		$clavesModulosStudent = Array();
		$clavesModulosStudent["liveSession"] = 45;
		$clavesModulosStudent["desk"] = 16;

	  # Form the menu list array
		for($i = 1; $i <= $tot_modulos; $i++) {
			# Initialize sub array
			$sub_nav = array();

		  # Populate the sub module array first
			for($j = 1; $j <= $tot_submodulos[$i]; $j++) {
				if ( $fl_funcion[$i][$j] != $clavesModulosTeacher["liveSession"] && $fl_funcion[$i][$j] != $clavesModulosStudent["liveSession"]) 
				{
					if(!empty($nb_icono[$i][$j]))
						$nav_icon = "<img src='".SP_IMAGES."/".$nb_icono[$i][$j]."' width='16' height='16' border='0'>";
					else
						$nav_icon = "";

					$sub_nav += array(
						strtolower($nb_funcion[$i][$j]) => array(
							"title" => $nb_funcion[$i][$j],
		          "url" => "ajax/node.php?node=".$fl_funcion[$i][$j],
		          "nav_icon" => $nav_icon
		         )
					);
				}
		  }
		  $page_nav += array(
		  	strtolower($nb_modulo[$i]) => array(
		  		"title" => $nb_modulo[$i],
		  		"icon" => "",
		  		"sub" => $sub_nav
		  	)
		  );
		}
	  return $page_nav;
	}

	# Get user's id
	function GetUser($fl_usuario){
  	echo json_encode($fl_usuario);
  }

  # Get user's name
  function GetUserName($fl_usuario){
  	$ds_nombre = ObtenNombreUsuario($fl_usuario);
  	echo json_encode($ds_nombre);
  }

	# Get the user's avatar
	function GetUserAvatar($fl_usuario){
		$ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario);
		echo json_encode($ds_ruta_avatar);
	}

	# Get the program name of the student is in
	function GetProgramName($fl_alumno){
		$nb_programa = ObtenNombreProgramaAlumno($fl_alumno);
		echo json_encode($nb_programa);
	}

	# Get the current term the student is in
	function GetCurrentTerm($fl_alumno){
		$no_grado = ObtenGradoAlumno($fl_alumno);
		echo json_encode($no_grado);
	}
	function GetMaxTerm($fl_alumno){
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$Query  = "SELECT no_grados FROM c_programa WHERE fl_programa=$fl_programa";
		$row = RecuperaValor($Query);
		$no_grados = $row[0];
		echo json_encode($no_grados); 
	}

	# Get the week the student is on
	function GetActualWeek($fl_alumno){
		$no_semana = ObtenSemanaActualAlumno($fl_alumno);
		echo json_encode($no_semana);
	}
	function GetMaxWeek($fl_alumno){
		$max_semana = ObtenSemanaMaximaAlumno($fl_alumno);
		echo json_encode($max_semana);
	}

	# Get the teacher's full profile (includes live session)
	function GetTeacherInfo($fl_maestro){
		$ds_tituloAux = ObtenTituloLeccionTeacher($fl_maestro);
    $fechaLiveSession = ObtenFechaLiveSessionTeacher($fl_maestro);
    $folioClase = ObtenFolioLiveSessionTeacher($fl_maestro);
    $grupo = ObtenGrupoTeacher($fl_maestro);

    if($fechaLiveSession == " "){
    	$day = "unavailable";
    } else {
    	$day = date("l", strtotime($fechaLiveSession));
    	$countdown = date("Y/m/d H:i:s", strtotime($fechaLiveSession));
    }

    $fg_link_disponible = ObtenLiveSessionDisponible($folioClase);

    //if($fg_link_disponible)
      $liveSessionDisplay = "<a role='button' class='btn btn-sm btn-primary pull-right' href='../liveclass/LiveSession.php?folio=$folioClase' style='position: absolute; right: 10px; bottom: 10px;' target='_blank'>Join Class</a>";
    //else
    //  $liveSessionDisplay .= "<a role='button' class='btn btn-sm btn-primary pull-right disabled' href='#' style='position: absolute; right: 10px; bottom: 10px;'>Join Class</a>";

    $result["info"] = array(
    	"live_session_title" => $ds_tituloAux,
    	"live_session_time" => $fechaLiveSession,
    	"live_session_link" => $liveSessionDisplay,
    	"countdown" => $countdown,
    	"day" => $day
    );
		echo json_encode((Object) $result);
	}

	# Get the student's full profile (includes live session)
	function GetStudentInfo($fl_alumno){
		// Student Info
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
    $nb_programa = ObtenNombreProgramaAlumno($fl_alumno);
    $no_grado = ObtenGradoAlumno($fl_alumno);
    $no_semana = ObtenSemanaActualAlumno($fl_alumno);
    $ds_titulo = ObtenTituloLeccion($fl_programa, $no_grado, $no_semana);
    $ds_status = ObtenStatusAlumno($fl_alumno);

    // Live Session Info
    $fl_programaAux = ObtenProgramaAlumno($fl_alumno);
    $no_gradoAux = ObtenGradoAlumno($fl_alumno);
    $no_semanaAux = ObtenSemanaLiveSessionStudent($fl_alumno);
    $ds_tituloAux = ObtenTituloLeccion($fl_programaAux, $no_gradoAux, $no_semanaAux);
    
    $grupo = ObtenGrupoAlumno($fl_alumno);
    $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semanaAux);
    $fechaLiveSession = trim(ObtenLiveSessionActualStudent($grupo, $fl_semana));
    $folioClase = ObtenFolioLiveSessionStudent($grupo, $fl_semana);
    $grupo = ObtenNombreGrupo($grupo);

    if(empty($fechaLiveSession)){
    	$day = "unavailable";
    } else {
    	$day = date("l", strtotime($fechaLiveSession));
    	$countdown = date("Y/m/d H:i:s", strtotime($fechaLiveSession));
    }

    $fg_link_disponible = ObtenLiveSessionDisponible($folioClase);

    //if($fg_link_disponible)
      $liveSessionDisplay = "<a role='button' class='btn btn-sm btn-primary pull-right' href='../liveclass/LiveSession.php?folio=$folioClase' style='position: absolute; right: 10px; bottom: 10px;' target='_blank'>Join Class</a>";
    //else
    //  $liveSessionDisplay .= "<a role='button' class='btn btn-sm btn-primary pull-right disabled' href='#' style='position: absolute; right: 10px; bottom: 10px;'>Join Class</a>";

    $result["info"] = array(
    	"course" => $nb_programa,
    	"term" => $no_grado,
    	"week" => $no_semana,
    	"lesson" => $ds_titulo,
    	"status" => $ds_status,
    	"live_session_title" => $ds_tituloAux,
    	"live_session_time" => $fechaLiveSession,
    	"live_session_link" => $liveSessionDisplay,
    	"countdown" => $countdown,
    	"day" => $day
    );
		echo json_encode((Object) $result);
	}

	# Get the student's profile (excludes live session)
	function GetStudentProfile($fl_alumno){
		$ds_nombres = ObtenNombreUsuario($fl_alumno);
		$ds_ruta_avatar = ObtenAvatarUsuario($fl_alumno);
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
    $nb_programa = ObtenNombreProgramaAlumno($fl_alumno);
    $no_grado = ObtenGradoAlumno($fl_alumno);
    $no_semana = ObtenSemanaActualAlumno($fl_alumno);
    $ds_titulo = ObtenTituloLeccion($fl_programa, $no_grado, $no_semana);
    $ds_status = ObtenStatusAlumno($fl_alumno);

    $result["profile"] = array(
    	"name" => $ds_nombres,
    	"avatar" => $ds_ruta_avatar,
    	"course" => $nb_programa,
    	"term" => $no_grado,
    	"week" => $no_semana,
    	"lesson" => $ds_titulo
    );
		echo json_encode((Object) $result);
	}

	# Get local time in hr : min : sec AM/PM
	function GetLocalDate(){

		$diferencia = RecuperaDiferenciaGMT( );
		$ds_fecha = ObtenFechaActual(True);
  	$ds_hora = GetActualHour( );

		$result["date"] = array(
			"difference" => $diferencia,
			"fe_local" => $ds_fecha." ".$ds_hora
		);
		echo json_encode((Object) $result);
	}

	# Get server's time in hr : min : sec AM/PM
	function GetServerDate(){
		$Query  = "SELECT DATE_FORMAT(CURRENT_TIMESTAMP, '%c') 'fe_mes', ";
    $Query .= "DATE_FORMAT(CURRENT_TIMESTAMP, '%e, %Y') 'fe_dia_anio', ";
    $Query .= "DATE_FORMAT(CURRENT_TIMESTAMP, '%l:%i:%s %p') 'fe_time'";
    $row = RecuperaValor($Query);
    $fe_server = ObtenNombreMes($row[0])." ".$row[1]." ".$row[2];

    $result["date"] = array(
    	"fe_server" => $fe_server
    );

    echo json_encode((Object) $result);
	}

	# Get the time difference in hours for the student to the server
	function GetTimeDifference(){
		$diferencia = RecuperaDiferenciaGMT();
		echo json_encode((Object) $diferencia);
	}

	# Get the time in hr : min : sec AM/PM
	function GetActualHour( ) {
	  
	 # Revisa si se debe usar una fecha para debug o la fecha actual
	  $fg_degub = ObtenConfiguracion(21);
	  $diferencia = RecuperaDiferenciaGMT( );
	  if($fg_degub <> "1") {
	    $Query  = "SELECT DATE_FORMAT((DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $diferencia HOUR)), '%l:%i:%s %p') 'fe_actual'";
	    $row = RecuperaValor($Query);
	    $fe_actual = $row[0];
	  }
	  else {
	    $fe_actual = ObtenConfiguracion(22);
	    $row = RecuperaValor("SELECT DATE_FORMAT((DATE_ADD('$fe_actual', INTERVAL $diferencia HOUR)), '%l:%i:%s %p') 'fe_actual'");
	    $fe_actual = $row[0];
	  }
	  return $fe_actual;
	}

	# Query for teachers list used in community_div.php and contacts.php
	function TeacherQuery($letter="", $country="", $classmate="") {	
		$Query  = "SELECT a.fl_maestro, a.ds_ruta_avatar, ";
		$concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
		//$Query .= ConcatenaBD($concat)." 'ds_nombre', a.ds_empresa, ds_pais ";
		$Query .= ConcatenaBD($concat)." 'ds_nombre', a.ds_empresa, ds_pais, b.no_accesos ";
		$Query .= "FROM c_maestro a, c_usuario b, c_pais c ";
		$Query .= "WHERE a.fl_maestro=b.fl_usuario ";
		$Query .= "AND a.fl_pais=c.fl_pais ";
		$Query .= "AND b.fg_activo='1' ";
		if(!empty($letter))
			$Query .= "AND ASCII(UCASE(b.ds_nombres))=".ord($letter)." ";
		if(!empty($country))
			$Query .= "AND a.fl_pais=$country ";
		if(!empty($classmate))
			$Query .= "AND a.fl_maestro=$classmate ";
		//$Query .= "ORDER BY b.ds_nombres";
		$Query .= "ORDER BY b.no_accesos DESC";
		$rs = EjecutaQuery($Query);

		return $rs;
	}

	# Query for students list used in community_div.php and contacts.php
	function StudentQuery($letter="", $country="", $program="", $classmate="") {
		$Query  = "SELECT a.fl_alumno, a.ds_ruta_avatar, ";
		$concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
		//$Query .= ConcatenaBD($concat)." 'ds_nombre', e.nb_programa, d.ds_pais, a.fl_alumno ";
		$Query .= ConcatenaBD($concat)." 'ds_nombre', e.nb_programa, d.ds_pais, a.fl_alumno, b.no_accesos ";
		$Query .= "FROM c_alumno a, c_usuario b, k_ses_app_frm_1 c, c_pais d, c_programa e, k_alumno_grupo f ";
		$Query .= "WHERE a.fl_alumno=b.fl_usuario ";
		$Query .= "AND b.cl_sesion=c.cl_sesion ";
		$Query .= "AND c.ds_add_country=d.fl_pais ";
		$Query .= "AND c.fl_programa=e.fl_programa ";
		$Query .= "AND a.fl_alumno=f.fl_alumno ";
		$Query .= "AND b.fg_activo='1' ";
		if(!empty($letter))
			$Query .= "AND ASCII(UCASE(b.ds_nombres))=".ord($letter)." ";
		if(!empty($country))
			$Query .= "AND d.fl_pais=$country ";
		if(!empty($program))
			$Query .= "AND c.fl_programa=$program ";
		if(!empty($classmate))
			$Query .= "AND f.fl_grupo=$classmate ";
		//$Query .= "ORDER BY b.ds_nombres";
		$Query .= "ORDER BY b.no_accesos DESC";
		$rs = EjecutaQuery($Query);

		return $rs;
	}

	# Progress Bars for notification notify/progress.php and home.php
	function GetAssignProgress($fl_alumno){

		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);
		$no_semana = ObtenSemanaActualAlumno($fl_alumno);
		$fl_grupo = ObtenGrupoAlumno($fl_alumno);
  	$fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);

  	# Find the class the student is in (not needed because class model is abandoned)
		/*$Query  = "SELECT fl_class FROM c_class WHERE fl_programa=$fl_programa AND no_grado=$no_grado ";
		$row = RecuperaValor($Query);
		$fl_class = $row[0];*/

  	$requirement["size"] = array();
  	$requirement["total"] = array();
  	$requirement["uploaded"] = array();
  	$requirement["debug"] = array();
  	
  	// Find total assignment requirements
		$Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
		$Query .= "FROM c_leccion ";
		$Query .= "WHERE fl_programa=$fl_programa ";
		//$Query .= "AND fl_class=$fl_class ";
		$Query .= "AND no_grado=$no_grado ";
		$Query .= "AND no_semana=$no_semana ";
		$row = RecuperaValor($Query);

		$fg_animacion = $row[0];
		$fg_ref_animacion = $row[1];
		$no_sketch = $row[2];
		$fg_ref_sketch = $row[3];

		$total = $fg_animacion + $fg_ref_animacion + $no_sketch + $fg_ref_sketch;

		$requirement["total"] += array(
			"A" => $fg_animacion,
			"AR" => $fg_ref_animacion,
			"S" => $no_sketch,
			"SR" => $fg_ref_sketch
		);

		$requirement["size"] += array("total" => $total);

		// Find the number of uploads the student has done
		$Query  = "SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana";
  	$row = RecuperaValor($Query);
  	$fl_entrega_semanal = $row[0];
  	if(empty($fl_entrega_semanal)) {
  		// student has not done any uplaods before
  	}

  	$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='A' AND fl_entrega_semanal=$fl_entrega_semanal");
  	$tot_assignment = $row[0];
  	$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='AR' AND fl_entrega_semanal=$fl_entrega_semanal");
  	$tot_assignment_ref = $row[0];
  	$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='S' AND fl_entrega_semanal=$fl_entrega_semanal");
  	$tot_sketch = $row[0];
  	$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='SR' AND fl_entrega_semanal=$fl_entrega_semanal");
  	$tot_sketch_ref = $row[0];

  	$requirement["debug"] += array(
  		"no_semana" => $no_semana,
  		"tot_A" => $tot_assignment,
  		"tot_AR" => $tot_assignment_ref,
  		"tot_S" => $tot_sketch,
  		"tot_SR" => $tot_sketch_ref
  	);

  	$total_uploaded = 0;

  	if($fg_animacion == "0" OR ($fg_animacion == "1" AND $tot_assignment > 0)){
    	$animacion_ok = 1;
    	$total_uploaded += $fg_animacion;
  	} else {
  		$animacion_ok = 0;
  	}
	  
	  if($fg_ref_animacion == "0" OR ($fg_ref_animacion == "1" AND $tot_assignment_ref > 0)){
	    $animacion_ref_ok = 1;
	    $total_uploaded += $fg_ref_animacion;
	  } else {
	  	$animacion_ref_ok = 0;
	  }

	  if($tot_sketch >= $no_sketch){
	    $sketch_ok = $no_sketch;
	    $total_uploaded += $no_sketch;
	  } else {
	  	$sketch_ok = $tot_sketch;
	  	$total_uploaded += $tot_sketch;
	  }
	  
	  if($fg_ref_sketch == "0" OR ($fg_ref_sketch == "1" AND $tot_sketch_ref > 0)){
	    $sketch_ref_ok = 1;
	    $total_uploaded += $fg_ref_sketch;
	  } else {
	  	$sketch_ref_ok = 0;
	  }

	  $requirement["uploaded"] += array(
	  	"A" => $animacion_ok,
	  	"AR" => $animacion_ref_ok,
	  	"S" => $sketch_ok,
	  	"SR" => $sketch_ref_ok
	  );

	  $requirement["size"] += array("total_uploaded" => $total_uploaded);
		
		echo json_encode((Object) $requirement);
	}

	# End progress bars

	# Get the list of chat users for messages.php and notify/messages.php 
	function GetChatUsers($fl_usuario){
	  # Recupera usuarios que han enviado o se les ha enviado mensajes
	  $diferencia = RecuperaDiferenciaGMT( );

	  $Query  = "SELECT usr_interaccion, DATE_FORMAT(MAX(fe_mensaje), '%M %e, %Y at %l:%i %p') 'fe_message', MAX(fe_mensaje) cuando ";
	  $Query .= "FROM( SELECT CASE WHEN fl_usuario_ori<>$fl_usuario then fl_usuario_ori ELSE fl_usuario_dest END usr_interaccion, ";
	  $Query .= "DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR) fe_mensaje ";
	  $Query .= "FROM k_mensaje_directo ";
	  $Query .= "WHERE fl_usuario_ori=$fl_usuario ";
	  $Query .= "OR fl_usuario_dest=$fl_usuario) usuarios ";
	  $Query .= "GROUP BY usr_interaccion ";
	  $Query .= "ORDER BY cuando DESC";
	  $rs = EjecutaQuery($Query);

	  $result["users"] = array("diferencia" => $diferencia);
	  $result["size"] = array();

	  for($i=0; $row = RecuperaRegistro($rs); $i++) {
	    $usr_interaccion = $row[0];
	    $fe_mensaje = $row[1];
	    $ds_ruta_avatar = ObtenAvatarUsuario($usr_interaccion);
	    $ds_nombre = ObtenNombreUsuario($usr_interaccion);

      $result["users"] += array(
      	"usr_interaccion$i" => $usr_interaccion,
      	"fe_mensaje$i" => $fe_mensaje,
      	"ds_ruta_avatar$i" => $ds_ruta_avatar,
      	"ds_nombre$i" => $ds_nombre
      	//"ds_notificar$i" => $ds_notificar
      	//"no_cuantos$i" => $no_cuantos
      );
	  }

	  $result["size"] += array("total" => $i);
	  echo json_encode((Object) $result);
	}
	# end messages

	# Obtain user avatar for with new route
	function ObtainUserAvatar($fl_usuario){
		$fl_perfil = ObtenPerfilUsuario($fl_usuario);
		if($fl_perfil == PFL_MAESTRO) {
			$row = RecuperaValor("SELECT ds_ruta_avatar FROM c_maestro WHERE fl_maestro=$fl_usuario");
			if(!empty($row[0]))
	      $ds_ruta_avatar = PATH_N_MAE_IMAGES."/avatars/".$row[0];
	    else
	      $ds_ruta_avatar = SP_IMAGES."/".IMG_T_AVATAR_DEF;
		} else {
			$row = RecuperaValor("SELECT ds_ruta_avatar FROM c_alumno WHERE fl_alumno=$fl_usuario");
	    if(!empty($row[0]))
	      $ds_ruta_avatar = PATH_N_ALU_IMAGES."/avatars/".$row[0];
	    else
	      $ds_ruta_avatar = SP_IMAGES."/".IMG_S_AVATAR_DEF;
		}
		return $ds_ruta_avatar;
	}

	# Find Teacher's live session by group
	function ObtenGroupSessionTeacher($p_teacher, $p_grupo, $p_display=False) {
  
	  $tolerancia_link = ObtenConfiguracion(36);
	  if($p_display)
	    $diferencia = RecuperaDiferenciaGMT( );
	  else
	    $diferencia = 0;
	  $Query  = "SELECT a.fl_clase fl_clase, b.fl_grupo fl_grupo, a.fl_semana fl_semana, b.fl_term fl_term, ";
	  $Query .= "DATE_FORMAT((DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
	  $Query .= "DATE_FORMAT((DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), '%e, %Y') 'fe_dia_anio', ";
	  $Query .= ConsultaFechaBD("(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR))", FMT_HORAMIN)." hr_clase ";
	  $Query .= "FROM k_clase a, c_grupo b ";
	  $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
	  $Query .= "AND b.fl_maestro=$p_teacher ";
	  $Query .= "AND b.fl_grupo=$p_grupo ";
	  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
	  $Query .= "ORDER BY fe_clase";
	  $row = RecuperaValor($Query);
	  return $row;
	}
	function ObtenFechaGroupSessionTeacher($p_teacher, $p_grupo) {
	  $row = ObtenGroupSessionTeacher($p_teacher, $p_grupo, True);  
	  return ObtenNombreMes($row["fe_mes"]) . " " . $row["fe_dia_anio"] . " " . $row["hr_clase"];
	}

	function ObtenFolioGroupSessionTeacher($p_teacher, $p_grupo) {  
	  $row = ObtenGroupSessionTeacher($p_teacher, $p_grupo);  
	  return $row["fl_clase"];
	}


	# Present video functions

	# Wraps the video functions in a wrapper that allows videos to become responsive when the broswer size changes
	function PresentVideoJWP($ds_route){
		echo"<div class='embed-container'>";
			PresentaVideoJWP($ds_route);
		echo"</div>";
	}

	function PresentVideoJWP_2($p_file) {

		$file = ObtenNombreArchivo($p_file);
        $streamer = "rtmp://".ObtenConfiguracion(60)."/oflaDemo";
		$image = SP_IMAGES."/PosterFrame_White.jpg";
		$width = ObtenConfiguracion(13);
		$height = ObtenConfiguracion(14) + 25;
		$bufferTime = ObtenConfiguracion(56);

		$video = 
			"<div class='embed-container'>
				<object id='player' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' name='player' width='$width' height='$height' style='z-index: 1;'>
				<param name='movie' value='".SP_FLASH."/player.swf'/>
				<param name='allowfullscreen' value='true'/>
				<param name='allowscriptaccess' value='always'/>
				<param name='wmode' value='opaque'/>
				<param name='flashvars' value='file=$file&streamer=$streamer&image=$image&bufferlength=$bufferTime&smoothing=false'/>
				<embed
					type='application/x-shockwave-flash'
					id='player2'
					name='player2'
					src='".SP_FLASH."/player.swf'
					width='$width'
					height='$height'
					allowscriptaccess='always'
					allowfullscreen='true'
					wmode='opaque'
					flashvars='file=$file&streamer=$streamer&image=$image&bufferlength=$bufferTime&smoothing=false'/>
				</object>
			</div>";

		return $video;
	}

	# Capa con marca de agua para Video Lectures
	function PresentWatermark($p_watermark) {
	  
		$watermark = 
	  	"<div id='div_watermark' style='position: absolute; top: 230; left: 200; z-index: 2; font-size: 20; opacity:0.5; color: #FFF;' >
	  		$p_watermark
	  	</div>
	  	<script type='text/javascript'>
	  	timer = setInterval(\"CambiaEtiqueta()\", 10000);
	  	function CambiaEtiqueta() {
	  		var aleat = Math.random() * (405 - 20); // 405 alto del video - 20 alto de la etiqueta
	  		aleat = Math.round(aleat);
	  		$('#div_watermark').css('top', parseInt(220) + aleat);
	  		aleat = Math.random() * (720 - 100); // 720 ancho del video - 100 ancho de la etiqueta
	  		aleat = Math.round(aleat);
	  		$('#div_watermark').css('left', parseInt(200) + aleat);
	  		aleat = Math.random() * (20 - 12); // el font estara entre 12 y 20
	  		aleat = Math.round(aleat);
	  		$('#div_watermark').css('font-size', parseInt(12) + aleat);
	  		$('#div_watermark').html('$p_watermark');
	  		espera = setTimeout(\"$('#div_watermark').html('')\", 5000);
	  	}
	  	</script>";
		return $watermark;
	}

	# These function wraps the video functions in a wrapper that allows videos to become responsive when the broswer size changes
	function PresentVideoHTML5($p_path, $p_file, $p_width='720', $p_height='405', $p_id='recordCritique') { 

		$video = 
			"<video tabindex='0' id='$p_id' width='$p_width' height='$p_height' controls='controls'>
				<source src='".$p_path.$p_file."' type='video/mp4'>
				<source src='".$p_path.$p_file."' type='video/ogg'>
			</video>
			<div id='seekInfo' style='display:none;'></div>
			<script type='text/javascript' src='".PATH_LIB."/js_player/smpte_test_universal.js'></script>
			<script type='text/javascript' src='".PATH_LIB."/js_player/jquery.jkey-1.2.js'></script>";

		if($p_id == 'recordCritique') {
			$video .= 
				"<script type='text/javascript' src='".PATH_COM_JS."/recordCritique.js'></script>
				<script>
					var div_aux = $('<div />').appendTo('body'); 
					div_aux.attr('id', 'libCritiqueIncluidas');
					$('#libCritiqueIncluidas').html('True');
				</script>";    
		}
		return $video;
	}

	# end video functions

	# Record critique functions

	# Present canvas toolbar 
	function PresentToolBar( ) {
  
	  $toolbar = "
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/cp_depends.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/CanvasWidget_new.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/CanvasPainter.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/CPWidgets.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/CPAnimator.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/CPDrawing.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_jpicker/jpicker-1.1.6.min.js'></script>
		  <script type='text/javascript' src='".PATH_LIB."/js_paint/pizarron_new.js'></script>
		  <link rel='stylesheet' href='".PATH_LIB."/js_jpicker/jPicker-1.1.6.min.css' />
		  <link rel='stylesheet' href='".PATH_LIB."/js_paint/pizarron_new.css' />
		  
		  <div id='paint'>
		    <div id='controls'>
		      <div class='ctr_btn' id='btn_0' style='background: #CCCCCC;' onclick='setCPDrawAction(0)' onMouseDown=\"setControlLook(0, '#CCCCCC')\" onMouseOver=\"setControlLook(0, '#EEEEEE')\" onMouseOut=\"setControlLook(0, '#FFFFFF')\" title='Pencil'><img src='".PATH_COM_IMAGES."/brush1.png'/></div>
		      <div class='ctr_btn' id='btn_1' onclick='setCPDrawAction(1)' onMouseDown=\"setControlLook(1, '#CCCCCC')\" onMouseOver=\"setControlLook(1, '#EEEEEE')\" onMouseOut=\"setControlLook(1, '#FFFFFF')\" title='Brush'><img src='".PATH_COM_IMAGES."/brush2.png'/></div>
		      <div class='ctr_btn' id='btn_2' onclick='setCPDrawAction(2)' onMouseDown=\"setControlLook(2, '#CCCCCC')\" onMouseOver=\"setControlLook(2, '#EEEEEE')\" onMouseOut=\"setControlLook(2, '#FFFFFF')\" title='Line'><img src='".PATH_COM_IMAGES."/line.png'/></div>
		      <div class='ctr_btn' id='btn_3' onclick='setCPDrawAction(3)' onMouseDown=\"setControlLook(3, '#CCCCCC')\" onMouseOver=\"setControlLook(3, '#EEEEEE')\" onMouseOut=\"setControlLook(3, '#FFFFFF')\" title='Rectangle'><img src='".PATH_COM_IMAGES."/rectangle.png'/></div>
		      <div class='ctr_btn' id='btn_4' onclick='setCPDrawAction(4)' onMouseDown=\"setControlLook(4, '#CCCCCC')\" onMouseOver=\"setControlLook(4, '#EEEEEE')\" onMouseOut=\"setControlLook(4, '#FFFFFF')\" title='Circle'><img src='".PATH_COM_IMAGES."/circle.png'/></div>
		      <div class='ctr_btn' id='btn_5' onclick='setCPDrawAction(5)' onMouseDown=\"setControlLook(5, '#CCCCCC')\" onMouseOver=\"setControlLook(5, '#EEEEEE')\" onMouseOut=\"setControlLook(5, '#FFFFFF')\" title='Erase'><img src='".PATH_COM_IMAGES."/erase.gif'/></div>
		      <div class='ctr_btn' id='togglePizarron'><img src='".PATH_COM_IMAGES."/onoffon.png' title='Turn Off'/></div>
		      <div class='ctr_btn' id='selectLineWidth' title='Select line width'><img src='".PATH_COM_IMAGES."/selectLine.png'/></div>
		      <div class='ctr_btn' id='selectColor' title='Select color' style='background-color: #124cc7;'></div>
		    </div>
		    
		    <div id='chooserWidgets'>
		      <canvas id='lineWidthChooser' width='275' height='76' style='display:none;'></canvas>
		    </div>
		    <div id='dlgJPicker'><div id='Expandable'></div></div>
		  </div>
		  ";

	  return $toolbar;
	}

	function PresentCanvas(){
		$canvas = "
			<div id='canvas-container' title='Drawing Area'>
	    	<canvas id='canvas' width='720' height='375'></canvas>
	    	<canvas id='canvasInterface' width='720' height='375'></canvas>
	    </div>
	    ";
	  return $canvas;
	}

	function PresentWebcam($p_entrega_semanal) {
  
	  $webcam_top     = "5px";
	  $webcam_left    = "10px";
	  $webcam_width   = "250px";
	  $webcam_height  = "188px";
	  $nombreArchivoJNLP = ObtenNombreArchivoJNLP($p_entrega_semanal);
	  
	  $webcam = "
	  	<div id='broadcaster' style='position:absolute;width:$webcam_width;height:$webcam_height;top:$webcam_top;left:$webcam_left;'>No Flash</div>
	  	<div style='position:absolute;top:195px;background-color:#fed;'><a href='".SP_HOME.DIRECTORIO_JAR."/".$nombreArchivoJNLP."'><img src='".PATH_COM_IMAGES."/record.png' title='Start recording'/></a></div>";

	  return json_encode($webcam);

	  /*"<script type='text/javascript'>
	     <![CDATA[
	    var so = new SWFObject('".PATH_LIB."/js_webcam/broadcast.swf?folio=$p_entrega_semanal', 'broadcast', '250', '188', '8', '#FFFFFF');
	    so.addParam('allowScriptAccess', 'always');
	    so.addVariable('allowResize', canResizeFlash());
	    so.write('broadcaster');
	     ]]>
	  </script>*/
	}

	# end record critique functions

?>