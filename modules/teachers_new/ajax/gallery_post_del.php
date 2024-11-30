<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $fl_gallery_post = RecibeParametroNumerico('fl_post');

  if(empty($fl_gallery_post)){
  	$error = array('error' => "Server Error. The post is not defined.");
    echo json_encode((Object)$error);
    exit;
  }

  # Initialize static variables
  $board_default = 'vanas-board-default';
  $board_video_default = 'vanas-board-video-default';

  # Check if the post is an assignment upload or board post
  $Query  = "SELECT fl_entregable, nb_archivo ";
  $Query .= "FROM k_gallery_post ";
  $Query .= "WHERE fl_gallery_post=$fl_gallery_post AND fl_usuario=$fl_usuario";
  $row = RecuperaValor($Query);
  $fl_entregable = $row[0];
  $nb_archivo = $row[1];

  # Find the type of file
  $file_name = ObtenNombreArchivo($nb_archivo);
	$ext = strtolower(ObtenExtensionArchivo($nb_archivo));

  # Only handles the delete if the post comes from the board
  if(empty($fl_entregable)) {

  	# Delete the image
  	if($ext == 'jpg') {

  		# Check for default file names
  		if($file_name != $board_default AND $file_name != $board_video_default) {
	  		$path = PATH_N_COM_F."/upload/gallery";
	  		$thumbs = $path."/thumbs";

	  		$path_error = unlink($path."/".$nb_archivo);
	  		$thumbs_error = unlink($thumbs."/".$nb_archivo);

	  		# Check if all files are safely deleted
	  		if(!$path_error || !$thumbs_error){
	  			$source = array(
						'path' => $path_error,
						'thumbs' => $thumbs_error
					);
					$error = array(
						'error' => "Server Error. Image file cannot be deleted.",
						'file_name' => $file_name,
	  				'ext' => $ext,
						'source' => $source
					);
			    echo json_encode((Object)$error);
			    exit;
	  		}
  		}	
  	}

  	# There are no video upload to the board yet.
  	if($ext == 'ogg') {
  		# TODO: handles video deletes after video uploading is implemented
  	}

  	# Delete all the comments of the board post
	  EjecutaQuery("DELETE FROM k_gallery_comment WHERE fl_gallery_post=$fl_gallery_post");
	  # Delete the post
	  EjecutaQuery("DELETE FROM k_gallery_post WHERE fl_gallery_post=$fl_gallery_post");

  } else {
  	# This is an uploaded assignment
  	$error = array('error' => "Server Error. Please delete this post in Assignment Upload page.");
    echo json_encode((Object)$error);
    exit;
  }
  
  $success = array('success' => 'The post has been deleted.');
  echo json_encode((Object)$success);
?>