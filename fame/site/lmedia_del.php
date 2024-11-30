<?php
  
 # Libreria de funciones	
 require("../lib/self_general.php");
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
	function delete_files($target) {
		if(is_dir($target)){
			$files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

			foreach( $files as $file ){
				delete_files( $file );      
			}

			rmdir( $target );
		} elseif(is_file($target)) {
			unlink( $target );  
		}
	}

  
 
  # Valida si existen alumnos en el curso de la leccion mandara error
  $row0 = RecuperaValor("SELECT COUNT(*)FROM k_usuario_programa a, c_leccion_sp b WHERE  a.fl_programa_sp = b.fl_programa_sp AND b.fl_leccion_sp=$clave");
  if(!empty($row0[0])){
    $result['fg_correcto'] = 0;
    $result['fg_curso_asignado']=1;
    echo json_encode((Object) $result);
	exit;
  }
  
  
  # Ruta donde se encuentras las imagenes
  $ruta = "uploads";
  # Elimina el registro    
  $rs = EjecutaQuery("SELECT fl_quiz_pregunta, fg_tipo FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave");
  for($x=0;$row=RecuperaRegistro($rs);$x++) {
    $fl_quiz_pregunta = $row["fl_quiz_pregunta"];
    $fg_tipo = $row["fg_tipo"];
    # Eliminamos los archivos
    if($fg_tipo=="I"){
      # Buscamos los registros
      $Query1 = "SELECT ds_respuesta, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta=".$fl_quiz_pregunta;
      $rs1 = EjecutaQuery($Query1);
      for($m=0;$row1=RecuperaRegistro($rs1);$m++){
        $ds_respuesta = $row1["ds_respuesta"];
        $ds_respuesta_esp = $row1["ds_respuesta_esp"];
        $ds_respuesta_fra = $row1["ds_respuesta_fra"];
        # Eliminamos el archivo
        unlink($ruta."/".$ds_respuesta);
        unlink($ruta."/".$ds_respuesta_esp);
        unlink($ruta."/".$ds_respuesta_fra);
      }
    }
    EjecutaQuery("DELETE FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta");
  }
  
  EjecutaQuery("DELETE FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave");
  EjecutaQuery("DELETE FROM k_criterio_programa_fame WHERE fl_programa_sp = $clave");
  EjecutaQuery("DELETE FROM c_leccion_sp WHERE fl_leccion_sp=$clave");
  
  
  #Elimnamos la carpeta videos.
  $folder = $_SERVER[DOCUMENT_ROOT]."/vanas_videos/fame/lessons/video_".$clave.""; 
  delete_files($folder);
  
  $result['fg_correcto'] = 1;
  
  echo json_encode((Object) $result);
 
?>