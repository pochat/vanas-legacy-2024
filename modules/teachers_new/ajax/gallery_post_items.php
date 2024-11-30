<?php
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# gallery_post_items queries for the list of post items for the board to display
	# only used when user is filtering the board

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
	
	# Receive Parameters
  $fl_tema = RecibeParametroNumerico('tema', True);
  $classmate = RecibeParametroNumerico('classmate', True);
  $my_posts = RecibeParametroNumerico('my_posts', True);
  $index = RecibeParametroNumerico('index', True);
	
  $index = intval($index);
  $index_end = 20;

  $diferencia = RecuperaDiferenciaGMT( );
    $Query  = "SELECT distinct fl_gallery_post, fl_entregable, a.fl_usuario, ds_title, nb_tema, CONCAT(c.ds_nombres, ' ', c.ds_apaterno) as ds_name, nb_archivo, DATE_FORMAT(DATE_ADD(fe_post, INTERVAL $diferencia HOUR), '%M %e, %Y') fe_post, ds_post, c.fl_perfil ";
	$Query .= "FROM k_gallery_post a ";
	$Query .= "LEFT JOIN c_f_tema b ON a.fl_tema=b.fl_tema ";
	$Query .= "LEFT JOIN c_usuario c ON a.fl_usuario=c.fl_usuario ";
	$Query .= "LEFT JOIN k_alumno_grupo d ON a.fl_usuario=d.fl_alumno ";
	$Query .= "WHERE nb_tema IS NOT NULL ";
	if(!empty($fl_tema))
		$Query .= "AND a.fl_tema=$fl_tema ";
	if(!empty($classmate)){
		# Find all groups that the teacher is with
		$Query2 = "SELECT fl_grupo FROM c_grupo WHERE fl_maestro=$fl_usuario";
		$rs2 = EjecutaQuery($Query2);
		$fl_grupos = "";
		while($row2=RecuperaRegistro($rs2)){
			$fl_grupos .= $row2[0].",";
		}
		$fl_grupos = rtrim($fl_grupos, ",");
		$Query .= "AND d.fl_grupo IN($fl_grupos) ";
		$Query .= "OR a.fl_usuario=$fl_usuario ";
		//$Query .= "AND c.fg_activo='1' ";
	}
	if(!empty($my_posts)){
		$Query .= "AND a.fl_usuario=$fl_usuario ";
	}
	$Query .= "ORDER BY a.fe_post DESC LIMIT $index_end OFFSET $index ";
	$rs = EjecutaQuery($Query);

	$result = array();
	for($i=0; $row=RecuperaRegistro($rs); $i++){
		$fl_gallery_post = $row[0];
		$fl_entregable = $row[1];
		$fl_post_usuario = $row[2];
		$ds_title = str_uso_normal($row[3]);
		$nb_tema = $row[4];
		$ds_nombres = $row[5];
		$nb_archivo = $row[6];
		$fe_post = $row[7];
		$ds_post = str_uso_normal($row[8]);
		$fl_post_perfil = $row[9];

		# Initialize default post settings
		$type = "";
		$fg_my_post = false;
		$fg_tipo = "";
		$no_semana = "";
		$no_grado = "";
		$ds_pais = "";

		# Find country of the author
		if($fl_post_perfil == PFL_ESTUDIANTE){
			$Query  = "SELECT ds_pais ";
			$Query .= "FROM c_usuario a ";
			$Query .= "LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
			$Query .= "LEFT JOIN c_pais c ON c.fl_pais=b.ds_add_country ";
			$Query .= "WHERE fl_usuario=$fl_post_usuario ";
		} else {
			$Query  = "SELECT ds_pais ";
			$Query .= "FROM c_maestro a ";
			$Query .= "LEFT JOIN c_pais b ON b.fl_pais=a.fl_pais ";
			$Query .= "WHERE fl_maestro=$fl_post_usuario ";
		}
		$row2 = RecuperaValor($Query);
		$ds_pais = $row2[0];

		# Find number of comments for this post
		$Query = "SELECT COUNT(1) FROM k_gallery_comment WHERE fl_gallery_post=$fl_gallery_post";
		$row2 = RecuperaValor($Query);
		$no_comments = $row2[0];

		# Check if this is an upload from desktop or straight from the board
		if(!empty($fl_entregable)){
			$type = "Desktop";

			# Retrieve term of the post
			$no_grado = ObtenGradoAlumno($fl_post_usuario);

			# Retrieve desktop post info
			$Query  = "SELECT a.fg_tipo, d.no_semana ";
			$Query .= "FROM k_entregable a ";
			$Query .= "LEFT JOIN k_entrega_semanal b ON b.fl_entrega_semanal=a.fl_entrega_semanal ";
			$Query .= "LEFT JOIN k_semana c ON c.fl_semana=b.fl_semana ";
			$Query .= "LEFT JOIN c_leccion d ON d.fl_leccion=c.fl_leccion ";
			$Query .= "WHERE a.fl_entregable=$fl_entregable ";
			$row2 = RecuperaValor($Query);
			$fg_tipo = $row2[0];
			$no_semana = $row2[1];

			switch($fg_tipo) {
				case "A":		$fg_tipo = "Assignment";  break;
		    case "AR":	$fg_tipo = "Assignment Reference"; break;
		    case "S":   $fg_tipo = "Sketch";  break;
		    case "SR":	$fg_tipo = "Sketch Reference"; break;
			}

			$ext = strtolower(ObtenExtensionArchivo($nb_archivo));
			if($ext == 'jpg'){
				# A student uploaded image
				$nb_file = "<img src='".PATH_ALU."/sketches/board_thumbs/$nb_archivo'>";
			} else {
				# A student uploaded video
				$nb_file = "<img src='".PATH_N_COM_UPLOAD."/gallery/thumbs/vanas-board-video-default.jpg'>";
			}
		} else {
			$type = "Board";
			$nb_file = "<img src='".PATH_N_COM_UPLOAD."/gallery/thumbs/$nb_archivo'>";

			# If this post belongs to the board and is posted by this user, allow delete
			if($fl_post_usuario == $fl_usuario){
				$fg_my_post = true;
			}
		}
		
		if(empty($ds_title)){
			$ds_title = "";
		}

		$result["item".$i] = array(
			"type" => $type,
			"fg_tipo" => $fg_tipo,
  		"no_semana" => $no_semana,
  		"no_grado" => $no_grado,
  		"ds_pais" => $ds_pais,
			"fg_my_post" => $fg_my_post,
  		"fl_gallery_post" => $fl_gallery_post,
  		"ds_title" => $ds_title,
  		"nb_tema" => $nb_tema,
  		"nb_archivo" => $nb_file,
  		"nb_usuario" => $ds_nombres,
  		"fe_post" => $fe_post,
  		"no_comments" => $no_comments
  	);
	}

	if($i == 0){
		$result["index"] = array("end" => 0);
		echo json_encode((Object)$result);
		exit;
	}

	$result["size"] = array("total" => $i);
	$result["index"] = array("end" => $index+$index_end);

	echo json_encode((Object) $result);
?>