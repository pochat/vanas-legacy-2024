<?php

	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");
	require("../../common/lib/cam_forum.inc.php");

	# Recibe parametros
	$fl_usuario = RecibeParametroNumerico('fl_usuario');
	$fl_post = RecibeParametroNumerico('fl_post');
	$ds_comentario = RecibeParametroHTML('ds_comentario');
	$nb_archivo = RecibeParametroHTML('archivo');

	# Revisa si el usuario es el autor del post
  $row = RecuperaValor("SELECT fl_usuario FROM k_f_post WHERE fl_post=$fl_post");
  $fl_usuario_post = $row[0];
  if($fl_usuario_post == $fl_usuario)
    $fg_leido = '1';
  else
    $fg_leido = '0';

	# Revisa si se envio un comentario nuevo
  if(!empty($ds_comentario)) {
    
    # Criterios para conversion del texto posteado
    $ds_comentario = PorcesaCadena($ds_comentario);
    
    # Revisa si se esta subiendo un archivo
    if(!empty($nb_archivo)) {
      $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
      $ruta = $_SERVER[DOCUMENT_ROOT].SP_HOME."/uploads";
      
      # Recibe el archivo seleccionado
      if(file_exists($ruta."/".$nb_archivo))
        unlink($ruta."/".$nb_archivo);
      rename(PATH_CAMPUS_F."/common/tmp/".$nb_archivo, $ruta."/".$nb_archivo);
      
      # Ajusta el maximo de dimensiones para imagenes
      if($ext == "jpg" OR $ext == "jpeg")
        CreaThumb($ruta."/".$nb_archivo, $ruta."/".$nb_archivo, 0, 0, 0, 480);
      
      # Convierte archivos .mov a .ogg
      if($ext == "mov") {
        $parametros = ObtenConfiguracion(41);
        $file_ogg = substr($nb_archivo, 0, (strlen($nb_archivo)-4)) . '.ogg';
        if(file_exists($ruta."/".$file_ogg))
          unlink($ruta."/".$file_ogg);
        $file_mov = $ruta."/".$nb_archivo;
        $comando = CMD_FFMPEG." -i \"$file_mov\" $parametros \"$ruta/$file_ogg\"";
        $nb_archivo = $file_ogg;
      }
    }
    
    # Inserta el comentario del post
    $Query  = "INSERT INTO k_f_comentario(fl_post, fl_usuario, fe_comentario, ds_comentario, nb_archivo, fg_leido) ";
    $Query .= "VALUES($fl_post, $fl_usuario, CURRENT_TIMESTAMP, '$ds_comentario', '$nb_archivo', '$fg_leido')";
    EjecutaQuery($Query);
    
    # Envia correo de notificacion al autor del post ( not sending email for now )
    /*if($fl_usuario_post <> $fl_usuario)
      EnviaNotificacion($ds_comentario, $ext, $fl_usuario, $fl_usuario_post);*/
  }

	# Retrieve comment posts
	$Query  = "SELECT fl_comentario, fl_usuario, ds_comentario, DATE_FORMAT(fe_comentario, '%c') 'fe_mes', ";
	$Query .= "DATE_FORMAT(fe_comentario, '%e, %Y at %l:%i %p') 'fe_dia_anio', nb_archivo ";
	$Query .= "FROM k_f_comentario ";
	$Query .= "WHERE fl_post=$fl_post ";
	$Query .= "ORDER BY fl_comentario";
	$rs = EjecutaQuery($Query);

	$result["size"] = array();
	$result["fl_post"] = array($fl_post);
	$result["data"] = array();
	$result["bar_data"] = array();

	for($tot_comentarios = 0; $row = RecuperaRegistro($rs); $tot_comentarios++){
		$fl_comentario = $row[0];
		$fl_usuario_com = $row[1];
		$ds_nombre = ObtenNombreUsuario($fl_usuario_com);
		$ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario_com);
		$ds_comentario = str_uso_normal($row[2]);
		$fe_comentario = ObtenNombreMes($row[3])." ".$row[4];
		$nb_archivo = str_uso_normal($row[5]);

		if(!empty($nb_archivo)) {
			$ext = strtolower(ObtenExtensionArchivo($nb_archivo));
			switch($ext) {
				case "ogg":
				$nb_archive = PresentVideoHTML5(SP_HOME."/uploads/", $nb_archivo, 480, 270, '');
				break;
				case "jpg":
				case "jpeg":
				$nb_archive = "<img class='post-image' src='".SP_HOME."/uploads/$nb_archivo' border='none' />";
				break;
			}
		} else {
			$nb_archive = "";
		}

		$result["data"] += array(
			"fl_comentario".$tot_comentarios => $fl_comentario,
			"fl_usuario_com".$tot_comentarios => $fl_usuario_com,
			"name".$tot_comentarios => $ds_nombre,
			"avatar".$tot_comentarios => $ds_ruta_avatar,
			"comment".$tot_comentarios => $ds_comentario,
			"fe_comentario".$tot_comentarios => $fe_comentario,
			"archive".$tot_comentarios => $nb_archive
		);
	}
	if($tot_comentarios > 1){
		# Retrieve comment post avatars
		$rs2 = EjecutaQuery("SELECT DISTINCT fl_usuario FROM k_f_comentario WHERE fl_post=$fl_post");

		for($tot_avatar = 0; $row = RecuperaRegistro($rs2); $tot_avatar++ ){
			$ds_ruta_avatar = ObtenAvatarUsuario($row[0]);
			$ds_nombre = ObtenNombreUsuario($row[0]);

			$result["bar_data"] += array(
				"avatar".$tot_avatar => $ds_ruta_avatar,
				"name".$tot_avatar => $ds_nombre
			);
		}
	} else {
		$tot_avatar = 0;
	}

	$result["size"] += array("total_bar" => $tot_avatar);
	$result["size"] += array("total_comments" => $tot_comentarios);

	echo json_encode((Object) $result);

?>