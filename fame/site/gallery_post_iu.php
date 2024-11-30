<?php

	# Libreria de funciones
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Obtenemos el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $ds_title = RecibeParametroHTML('ds_title');
  $ds_post = RecibeParametroHTML('ds_post');
  $ds_login = ObtenMatriculaAlumno($fl_usuario);
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp');
  
  if(empty($_FILES['qqfile']['tmp_name'])) {
    echo json_encode((Object)array('error' => 'Required field', 'input_error' => 0));
    exit;
  }

  # The default thumbnail width
  $thumbnail_width = 300;
  if(!empty($_FILES['qqfile']['tmp_name'])) {
    # Validate the uploaded file then move it to new_campus' common tmp
    require("../../modules/common/new_campus/lib/fileuploader.php");
    // list of valid extensions
    $allowedExtensions = array('jpeg', 'jpg', 'png', 'PNG');
    // max file size in bytes
    $sizeLimit = 500 * 1024 * 1024;

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $error = $uploader->handleUpload(PATH_SELF_F."/tmp/", True);
    if(isset($error['error'])){
      echo json_encode((Object)$error);
      exit;
    }

    $nb_archivo = $uploader->getName();

    # Malicious file names, exiting IU
    if(strpos($nb_archivo, '<!') !== false OR strpos($nb_archivo, '<?') !== false OR strpos($nb_archivo, '<script') !== false OR strpos($nb_archivo, '</script') !== false){
      echo json_encode((Object)array('error' => 'Input Error. Filename not accepted.'));
      exit;
    }

    $ruta = PATH_SELF_UPLOADS_F."/gallery";
    $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
      
    # Keep the original name;
    $nb_archivo_ant = $nb_archivo;
    $nb_archivo = $ds_login."_stream_".$fl_tema."_".rand(1, 32000).".$ext";

    rename(PATH_SELF_F."/tmp/".$nb_archivo_ant, $ruta."/".$nb_archivo);
    
    if($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "PNG"){
      if($ext == "jpg" || $ext == "jpeg")
        CreaThumb($ruta."/".$nb_archivo, $ruta."/thumbs/".$nb_archivo, $thumbnail_width);
      if($ext == "png" || $ext == "PNG")
        CreaThumbpng($ruta."/".$nb_archivo, $ruta."/thumbs/".$nb_archivo, $thumbnail_width);
    }

    # Upload videos (in the future)

  }
  else {
    $nb_archivo = 'vanas-board-default.jpg';
  }

  # Handles post title
  if(!empty($ds_title)){
    $ds_title = rawurldecode($ds_title);

    # Sanitize input (special cases)
    // @url: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/HTML5/HTML5_Parser
    // Lack of Reparsing
     
    $ds_title = str_replace("&lt;!", "&#60;!", $ds_title);   // html comment
    $ds_title = str_replace("&lt;?", "&#60;?", $ds_title);   // html comment
  }

  # Handles post description
  if(!empty($ds_post)) {
    $ds_post = rawurldecode($ds_post);
    $ds_post = PorcesaCadena($ds_post);

    # Sanitize input (special cases)
    // @url: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/HTML5/HTML5_Parser
    // Lack of Reparsing
     
    $ds_post = str_replace("&lt;!", "&#60;!", $ds_post);   // html comment
    $ds_post = str_replace("&lt;?", "&#60;?", $ds_post);   // html comment

    # Check if the post is an embedded video, e.g. Youtube, Vimeo
    # Refer to common/lib/cam_forum.inc.php, SeparaFrames() for the iframe check
    if(strpos($ds_post, 'iframe') !== false){
      $nb_archivo = 'vanas-board-video-default.jpg';
    }
  }
  
  if(empty($fl_programa_sp))
    $fl_programa_sp = 0;
  # Insert post
  $Query  = "INSERT INTO k_gallery_post_sp ";
  $Query .= "(fl_programa_sp, fl_usuario, ds_title, ds_post, fe_post, nb_archivo) ";
  $Query .= "VALUES ($fl_programa_sp, $fl_usuario, '$ds_title', '$ds_post', CURRENT_TIMESTAMP, '$nb_archivo')";
  $fl_gallery_post = EjecutaInsert($Query);

  # Check if the insert or update was successful
  if(empty($fl_gallery_post)){
    $error = array('error' => "Server Error. This post cannot be uploaded.");
    echo json_encode((Object)$error);
    exit;
  }

  echo json_encode((Object)array('post' => $fl_gallery_post));
?>