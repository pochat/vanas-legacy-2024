<?php

	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");
	require("../../common/lib/cam_forum.inc.php");

	# Recibe parametros
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  $fl_tema = RecibeParametroNumerico('fl_tema');
  $ds_post = RecibeParametroHTML('ds_post');
  $ds_ruta_avatar_usu = ObtenAvatarUsuario($fl_usuario);
  $nb_archivo = RecibeParametroHTML('archivo');

  $no_from = RecibeParametroNumerico('no_from');
  $no_to = RecibeParametroNumerico('no_to');

	# Revisa si se envio un post nuevo
  if(!empty($ds_post)) {
    
    # Criterios para conversion del texto posteado
    $ds_post = PorcesaCadena($ds_post);
    
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
    
    # Inserta el post
    $Query  = "INSERT INTO k_f_post(fl_tema, fl_usuario, fe_post, ds_post, nb_archivo) ";
    $Query .= "VALUES($fl_tema, $fl_usuario, CURRENT_TIMESTAMP, '$ds_post', '$nb_archivo')";
    EjecutaQuery($Query);
    
    # Actualiza contador de posts
    EjecutaQuery("UPDATE c_f_tema SET no_posts=no_posts+1 WHERE fl_tema=$fl_tema");
    
    # Actualiza notificaciones para usuarios por tema
    $rs = EjecutaQuery("SELECT fl_usuario FROM c_usuario WHERE fl_perfil IN(".PFL_ESTUDIANTE.", ".PFL_MAESTRO.") AND fl_usuario<>$fl_usuario");
    while($row = RecuperaRegistro($rs)) {
      $row2 = RecuperaValor("SELECT COUNT(1) FROM k_f_usu_tema WHERE fl_usuario=$row[0] AND fl_tema=$fl_tema");
      if($row2[0] == 0)
        EjecutaQuery("INSERT INTO k_f_usu_tema(fl_usuario, fl_tema, no_posts) VALUES($row[0], $fl_tema, 1)");
      else
        EjecutaQuery("UPDATE k_f_usu_tema SET no_posts=no_posts+1 WHERE fl_usuario=$row[0] AND fl_tema=$fl_tema");
    }
  }

	$Query  = "SELECT fl_post, fl_usuario, ds_post, DATE_FORMAT(fe_post, '%c') 'fe_mes', ";
	$Query .= "DATE_FORMAT(fe_post, '%e, %Y at %l:%i %p') 'fe_dia_anio', nb_archivo ";
	$Query .= "FROM k_f_post ";
	$Query .= "WHERE fl_tema=$fl_tema ";
	$Query .= "ORDER BY fl_post DESC ";
	//$Query .= "LIMIT $no_from, $no_to";
	$rs = EjecutaQuery($Query);

	$result["size"] = array();
	$result["data"] = array();

	for($tot_posts = 0; $row = RecuperaRegistro($rs); $tot_posts++){
		$fl_post = $row[0];
		$fl_usuario_post = $row[1];
		$ds_nombre = ObtenNombreUsuario($fl_usuario_post);
		$ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario_post);
		$ds_post = str_uso_normal($row[2]);
		$fe_post = ObtenNombreMes($row[3])." ".$row[4];
		$nb_archivo = str_uso_normal($row[5]);

		if(!empty($nb_archivo)) {
			$ext = strtolower(ObtenExtensionArchivo($nb_archivo));
			switch($ext) {
      case "ogg":
        $nb_archive = PresentVideoHTML5(SP_HOME."/uploads/", $nb_archivo, 480, 270, '');
        break;
      case "jpg":
      case "jpeg":
        $nb_archive = "<img class='post-image' src='".SP_HOME."/uploads/$nb_archivo'>";
        break;
      }
		} else {
			$nb_archive = "";
		}

		$result["data"] += array(
			"fl_post".$tot_posts => $fl_post,
			"fl_usuario_post".$tot_posts => $fl_usuario_post,
			"name".$tot_posts => $ds_nombre,
			"avatar".$tot_posts => $ds_ruta_avatar,
			"post".$tot_posts => $ds_post,
			"fe_post".$tot_posts => $fe_post,
			"archive".$tot_posts => $nb_archive
		);
	}

	$result["size"] = array(
		"total" => $tot_posts
		//"no_from" => $no_from+$no_to
	);

	echo json_encode((Object) $result);

?>