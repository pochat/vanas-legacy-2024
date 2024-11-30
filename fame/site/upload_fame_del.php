<?php	
	# Libreria de funciones	
	require("../lib/self_general.php");
  
	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $fl_entregable = RecibeParametroNumerico('entregable');
  $no_semana = RecibeParametroNumerico('semana');
  $fl_programa = RecibeParametroNumerico('fl_programa');

  if(empty($fl_entregable)){
  	$message = array('error' => "Server Error. No file was specified.");
    echo json_encode((Object)$message);
    exit;
  }

  # Check if an assignment has been assigned a grade
  $Query = "SELECT b.fl_promedio_semana, a.ds_ruta_entregable, a.fl_entrega_semanal_sp, fl_leccion_sp, fg_tipo, b.fg_entregado, fg_increase_grade ";
  $Query .= "FROM k_entregable_sp a ";
  $Query .= "LEFT JOIN k_entrega_semanal_sp b ON b.fl_entrega_semanal_sp=a.fl_entrega_semanal_sp WHERE a.fl_entregable_sp=$fl_entregable ";
 	$row = RecuperaValor($Query);
 	$fl_promedio_semana = $row[0];
	$ds_ruta_entregable = $row[1];
	$fl_entrega_semanal_sp = $row[2];
  $fl_leccion_sp = $row[3];
  $fg_tipo = $row[4];
  $fg_entregado = $row[5];
  $fg_increase_grade = $row[6];
	# Find the type of file
	$ext = strtolower(ObtenExtensionArchivo($ds_ruta_entregable));
  
  # Verificamos si ya podemos activar el boton    
  $row_l = RecuperaValor("SELECT fg_complete FROM k_leccion_usu WHERE fl_usuario_sp=$fl_usuario AND fl_leccion_sp=$fl_leccion_sp");
  $fg_completa = $row_l[0];
  if($fg_completa==1 && empty($fg_increase_grade)){    
 		# Else the week's assignments has been graded.
 		$message = array('error' => ObtenEtiqueta(1899));
    echo json_encode((Object)$message);
    exit;
  }
  
	# If grade has not been assigned
 	if(empty($fl_promedio_semana) || (!empty($fl_promedio_semana) && !empty($fg_increase_grade))){	

	  # Delete image files
	  if($ext == 'jpg'){
	  	$path = PATH_SELF_UPLOADS_F."/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/sketches";
      // $path = $_SERVER['DOCUMENT_ROOT'].PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/sketches";
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
	  	$path = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/videos";

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
 		$Query = "SELECT fl_gallery_post_sp FROM k_gallery_post_sp WHERE fl_entregable_sp=$fl_entregable";
	  $row = RecuperaValor($Query);
	  $fl_gallery_post_sp = $row[0];

	  # Delete all the comments of the board post
	  EjecutaQuery("DELETE FROM k_gallery_comment_sp WHERE fl_gallery_post_sp=$fl_gallery_post_sp");
	  # Delete the post itself
	  EjecutaQuery("DELETE FROM k_gallery_post_sp WHERE fl_gallery_post_sp=$fl_gallery_post_sp");

		# Delete the uploaded file record
		EjecutaQuery("DELETE FROM k_entregable_sp WHERE fl_entregable_sp=$fl_entregable");

		# Check for assignment completion status
	  // $fl_programa = ObtenProgramaAlumno($fl_alumno);
	  // $no_grado = ObtenGradoAlumno($fl_alumno);
	  $Query = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
    $Query .= "FROM c_leccion_sp WHERE fl_programa_sp=$fl_programa AND fl_leccion_sp=$fl_leccion_sp ";
	  $row = RecuperaValor($Query);
	  $fg_animacion = $row[0];
	  $fg_ref_animacion = $row[1];
	  $no_sketch = $row[2];
	  $fg_ref_sketch = $row[3];
	  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='A' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
	  $tot_assignment = $row[0];
	  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='AR' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
	  $tot_assignment_ref = $row[0];
	  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='S' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
	  $tot_sketch = $row[0];
	  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='SR' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
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
	    $Query = "UPDATE k_entrega_semanal_sp SET fe_entregado=CURRENT_TIMESTAMP WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fe_entregado IS NULL";
	    EjecutaQuery($Query);
	    EjecutaQuery("UPDATE k_entrega_semanal_sp SET fg_entregado='1' WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
	  } else {
	  	EjecutaQuery("UPDATE k_entrega_semanal_sp SET fe_entregado=NULL, fg_entregado='0' WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
      # Actualizamos los assigment del teacher
      if($fl_perfil==PFL_ESTUDIANTE_SELF){
        $rowk = RecuperaValor("SELECT fl_usu_pro, fl_maestro FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa");
        $fl_usu_pro = $rowk[0];
        $fl_maestro = $rowk[1];
        $rowk1 = RecuperaValor("SELECT fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=$fl_usu_pro");
        $fg_grade_tea = $rowk1[0];
        if($fg_grade_tea==1 AND !empty($fg_entregado)){
          # Actualizamos las asigaciones del teacher
          $row3 = RecuperaValor("SELECT no_submitted_assi  FROM k_usu_notify WHERE fl_usuario=$fl_maestro");
          $no_assigments = $row3[0] - 1;
          EjecutaQuery("UPDATE k_usu_notify SET no_submitted_assi=".$no_assigments." WHERE fl_usuario=$fl_maestro");
        }    
      }
	  }
    
    # BUSCAMOS CUANTOS ENTREGABLES HAY DEL TIPO
    $roww = RecuperaValor("SELECT COUNT(*) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='$fg_tipo'");
    $tot_entregados = $roww[0];
    
    $message = array(
        'success' => 'File has been successfully deleted.',
        'tot_entregados' => $tot_entregados,
        'fl_maestro' => $fl_maestro,
        'fl_perfil' => $fl_perfil
    );
    
    # Una vz que subio el archivo vuelve a calcular elpeso de su carpeta y actualiza
    # Obtenemos el tamaño de la carpeta del usuario
    File_Size(PATH_SELF_UPLOADS_F."/".$fl_instituto."/".CARPETA_USER.$fl_usuario,2,$fl_usuario);
  
    echo json_encode((Object) $message);
    exit;
 	} else {
 		# Else the week's assignments has been graded.
 		$message = array('error' => "A grade has already been assigned. This file cannot be deleted.");
    echo json_encode((Object)$message);
    exit;
 	}
 	
?>