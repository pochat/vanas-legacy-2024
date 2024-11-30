<?php	
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $fl_entregable = RecibeParametroNumerico('entregable');
  $no_semana = RecibeParametroNumerico('semana');

  if(empty($fl_entregable)){
  	$message = array('error' => "Server Error. No file was specified.");
    echo json_encode((Object)$message);
    exit;
  }

  # Check if an assignment has been assigned a grade
  $Query  = "SELECT b.fl_promedio_semana, a.ds_ruta_entregable, a.fl_entrega_semanal ";
  $Query .= "FROM k_entregable a ";
  $Query .= "LEFT JOIN k_entrega_semanal b ON b.fl_entrega_semanal=a.fl_entrega_semanal ";
  $Query .= "WHERE a.fl_entregable=$fl_entregable ";
 	$row = RecuperaValor($Query);
 	$fl_promedio_semana = $row[0];
	$ds_ruta_entregable = $row[1];
	$fl_entrega_semanal = $row[2];

	# Find the type of file
	$ext = strtolower(ObtenExtensionArchivo($ds_ruta_entregable));

	# If grade has not been assigned
 	if(empty($fl_promedio_semana)){	

	  # Delete image files
	  if($ext == 'jpg'){
	  	$path = $_SERVER['DOCUMENT_ROOT'].PATH_ALU."/sketches";
	  	$board_thumbs = $path."/board_thumbs";
		  $original = $path."/original";
		  $regular = $path."/regular";
		  $thumbs = $path."/thumbs";

		  $path_error = unlink($path."/".$ds_ruta_entregable);
		  $board_thumbs_error = unlink($board_thumbs."/".$ds_ruta_entregable);
			$original_error = unlink($original."/".$ds_ruta_entregable);
			$regular_error = unlink($regular."/".$ds_ruta_entregable);
			$thumbs_error = unlink($thumbs."/".$ds_ruta_entregable);

			# Check if all files are safely deleted
			if(!$path_error || !$board_thumbs_error || !$original_error || !$regular_error || !$thumbs_error){
				$source = array(
					'path' => $path_error,
					'board_thumbs' => $board_thumbs_error,
					'original' => $original_error,
					'regular' => $regular_error,
					'thumbs' => $thumbs_error
				);
				$message = array(
					'error' => "Server Error. Image file cannot be deleted.",
					'source' => $source
				);
		    echo json_encode((Object)$message);
		    exit;
			}
	  }
	  # Delete video files
	  if($ext == 'ogg'){
	  	$path = $_SERVER['DOCUMENT_ROOT'].PATH_ALU."/videos";

	  	$path_error = unlink($path."/".$ds_ruta_entregable);

	  	# Check if the file has been safely deleted
	  	if(!$path_error){
				$source = array(
					'path' => $path_error
				);
				$message = array(
					'error' => "Server Error. Video file cannot be deleted.",
					'source' => $source
				);
		    echo json_encode((Object)$message);
		    exit;
			}
	  }

	  # Find the board image
 		$Query = "SELECT fl_gallery_post FROM k_gallery_post WHERE fl_entregable=$fl_entregable";
	  $row = RecuperaValor($Query);
	  $fl_gallery_post = $row[0];

	  # Delete all the comments of the board post
	  EjecutaQuery("DELETE FROM k_gallery_comment WHERE fl_gallery_post=$fl_gallery_post");
	  # Delete the post itself
	  EjecutaQuery("DELETE FROM k_gallery_post WHERE fl_gallery_post=$fl_gallery_post");

		# Delete the uploaded file record
		EjecutaQuery("DELETE FROM k_entregable WHERE fl_entregable=$fl_entregable");

		# Check for assignment completion status
	  $fl_programa = ObtenProgramaAlumno($fl_alumno);
	  $no_grado = ObtenGradoAlumno($fl_alumno);
	  $Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
	  $Query .= "FROM c_leccion WHERE fl_programa=$fl_programa AND no_grado=$no_grado AND no_semana=$no_semana";
	  $row = RecuperaValor($Query);
	  $fg_animacion = $row[0];
	  $fg_ref_animacion = $row[1];
	  $no_sketch = $row[2];
	  $fg_ref_sketch = $row[3];
	  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='A' AND fl_entrega_semanal=$fl_entrega_semanal");
	  $tot_assignment = $row[0];
	  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='AR' AND fl_entrega_semanal=$fl_entrega_semanal");
	  $tot_assignment_ref = $row[0];
	  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='S' AND fl_entrega_semanal=$fl_entrega_semanal");
	  $tot_sketch = $row[0];
	  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='SR' AND fl_entrega_semanal=$fl_entrega_semanal");
	  $tot_sketch_ref = $row[0];
	  $animacion_ok = False;
	  if($fg_animacion == "0" OR ($fg_animacion == "1" AND $tot_assignment > 0))
	    $animacion_ok = True;
	  $animacion_ref_ok = False;
	  if($fg_ref_animacion == "0" OR ($fg_ref_animacion == "1" AND $tot_assignment_ref > 0))
	    $animacion_ref_ok = True;
	  $sketch_ok = False;
	  if($tot_sketch >= $no_sketch)
	    $sketch_ok = True;
	  $sketch_ref_ok = False;
	  if($fg_ref_sketch == "0" OR ($fg_ref_sketch == "1" AND $tot_sketch_ref > 0))
	    $sketch_ref_ok = True;
	  
	  # Update comption status
	  if($animacion_ok AND $animacion_ref_ok AND $sketch_ok AND $sketch_ref_ok) {
	    $Query = "UPDATE k_entrega_semanal SET fe_entregado=CURRENT_TIMESTAMP WHERE fl_entrega_semanal=$fl_entrega_semanal AND fe_entregado IS NULL";
	    EjecutaQuery($Query);
	    EjecutaQuery("UPDATE k_entrega_semanal SET fg_entregado='1' WHERE fl_entrega_semanal=$fl_entrega_semanal");
	  } else {
	  	EjecutaQuery("UPDATE k_entrega_semanal SET fe_entregado=NULL, fg_entregado='0' WHERE fl_entrega_semanal=$fl_entrega_semanal");
	  }

 	} else {
 		# Else the week's assignments has been graded.
 		$message = array('error' => "A grade has already been assigned. This file cannot be deleted.");
    echo json_encode((Object)$message);
    exit;
 	}
 	echo json_encode((Object)array('success' => 'File has been successfully deleted.'));
?>