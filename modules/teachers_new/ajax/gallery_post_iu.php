<?php

  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require("../../common/lib/cam_forum.inc.php");

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $ds_title = RecibeParametroHTML('ds_title');
  $ds_post = RecibeParametroHTML('ds_post');
  $fl_tema = RecibeParametroNumerico('theme');
  $ds_login = ObtenMatriculaAlumno($fl_usuario);

  if(empty($fl_tema)){
    echo json_encode((Object)array('error' => 'Server Error. Missing theme.'));
    exit;
  }
  
  # The default thumbnail width
  $thumbnail_width = 300;

  if(!empty($_FILES['qqfile']['tmp_name'])) {
    # Validate the uploaded file then move it to new_campus' common tmp
    require("../../common/new_campus/lib/fileuploader.php");
    // list of valid extensions
    //$allowedExtensions = array('mov', 'mp4', 'jpeg', 'jpg');
    $allowedExtensions = array('jpeg', 'jpg');
    // max file size in bytes
    $sizeLimit = 500 * 1024 * 1024;

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $error = $uploader->handleUpload(PATH_CAMPUS_F."/common/tmp/", True);
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

    $ruta = PATH_N_COM_F."/upload/gallery";
    $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
      
    # Keep the original name;
    $nb_archivo_ant = $nb_archivo;
    $nb_archivo = $ds_login."_stream_".$fl_tema."_".rand(1, 32000).".$ext";

    rename(PATH_CAMPUS_F."/common/tmp/".$nb_archivo_ant, $ruta."/".$nb_archivo);
    
    if($ext == "jpg" OR $ext == "jpeg"){
      CreaThumb($ruta."/".$nb_archivo, $ruta."/thumbs/".$nb_archivo, $thumbnail_width);
    }

    # Upload videos (in the future)

  } else {
    $nb_archivo = 'vanas-board-default.jpg';
  }

  # Handles post title
  if(!empty($ds_title)){
    $ds_title = rawurldecode($ds_title);

    # Sanitize input (special cases)
    /* @url: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/HTML5/HTML5_Parser
     * Lack of Reparsing
     */ 
    $ds_title = str_replace("&lt;!", "&#60;!", $ds_title);   // html comment
    $ds_title = str_replace("&lt;?", "&#60;?", $ds_title);   // html comment
  }

  # Handles post description
  if(!empty($ds_post)) {
    $ds_post = rawurldecode($ds_post);
    $ds_post = PorcesaCadena($ds_post);

    # Sanitize input (special cases)
    /* @url: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/HTML5/HTML5_Parser
     * Lack of Reparsing
     */
    $ds_post = str_replace("&lt;!", "&#60;!", $ds_post);   // html comment
    $ds_post = str_replace("&lt;?", "&#60;?", $ds_post);   // html comment

    # Check if the post is an embedded video, e.g. Youtube, Vimeo
    # Refer to common/lib/cam_forum.inc.php, SeparaFrames() for the iframe check
    if(strpos($ds_post, 'iframe') !== false){
      $nb_archivo = 'vanas-board-video-default.jpg';
    }
  }
    
  # Insert post
  $Query  = "INSERT INTO k_gallery_post ";
  $Query .= "(fl_tema, fl_usuario, ds_title, ds_post, fe_post, nb_archivo) ";
  $Query .= "VALUES ($fl_tema, $fl_usuario, '$ds_title', '$ds_post', CURRENT_TIMESTAMP, '$nb_archivo')";
  $fl_gallery_post = EjecutaInsert($Query);

  # Check if the insert or update was successful
  if(empty($fl_gallery_post)){
    $error = array('error' => "Server Error. This post cannot be uploaded.");
    echo json_encode((Object)$error);
    exit;
  }

  # Actualiza contador de posts
  EjecutaQuery("UPDATE c_f_tema SET no_posts=no_posts+1 WHERE fl_tema=$fl_tema");
  
  # Actualiza notificaciones para usuarios por tema
  $rs = EjecutaQuery("SELECT fl_usuario FROM c_usuario WHERE fl_perfil IN(".PFL_ESTUDIANTE.", ".PFL_MAESTRO.") AND fl_usuario<>$fl_usuario");
  while($row = RecuperaRegistro($rs)) {
    $row2 = RecuperaValor("SELECT COUNT(1) FROM k_f_usu_tema WHERE fl_usuario=$row[0] AND fl_tema=$fl_tema");
    if($row2[0] == 0){
      EjecutaQuery("INSERT INTO k_f_usu_tema(fl_usuario, fl_tema, no_posts) VALUES($row[0], $fl_tema, 1)");
    } else {
      EjecutaQuery("UPDATE k_f_usu_tema SET no_posts=no_posts+1 WHERE fl_usuario=$row[0] AND fl_tema=$fl_tema");
    }
  }
    
  echo json_encode((Object)array('post' => $fl_gallery_post));
?>