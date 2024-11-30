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
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
  
  # Valida si existen alumnos en este curso no puede eliminarlo
  $row0 = RecuperaValor("SELECT COUNT(*)FROM k_usuario_programa WHERE fl_programa_sp=$clave");
  if(!empty($row0[0])){
    $result['fg_correcto'] = 0;
    $result['fg_curso_asignado']=1;
    echo json_encode((Object) $result);
	exit;
  }
  
 
  # 1.- Obtener lecciones de un programa para poder borrar las preguntas y respuestas del quiz perteneciente a esas lecciones
  $rs = EjecutaQuery("SELECT fl_leccion_sp FROM c_leccion_sp WHERE fl_programa_sp = $clave");
  for($i = 0; $row = RecuperaRegistro($rs); $i++) {
    $fl_leccion_sp = $row[0];
    
    # 2.- Obtenemos preguntas de cada leccion para borrar sus respuestas
    $rs2 = EjecutaQuery("SELECT fl_quiz_pregunta  FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp");
    for($i2 = 0; $row2 = RecuperaRegistro($rs2); $i2++) {
      $fl_quiz_pregunta = $row2[0];
      
      # 3.- Borramos respuestas
      EjecutaQuery("DELETE FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta"); 
      # 4.- Borramos preguntas
      EjecutaQuery("DELETE FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp"); 
      # 5.- Borramos lecciones
      EjecutaQuery("DELETE FROM c_leccion_sp WHERE fl_programa_sp = $clave");    
    }
  }
  
  # 6.- Elimina los registro asociados
  EjecutaQuery("DELETE FROM k_programa_detalle_sp WHERE fl_programa_sp=$clave");
  
  # 7.- Elimina el registro
  EjecutaQuery("DELETE FROM c_programa_sp WHERE fl_programa_sp=$clave");
  
  # Eliminamos los videos    
  $rsv = EjecutaQuery("SELECT fl_video_contenido_sp FROM k_video_contenido_sp WHERE fl_programa_sp=".$clave);
  $tot_vid = CuentaRegistros($rsv);
  if(!empty($tot_vid)){
    $row = RecuperaValor("SELECT cl_pagina_sp FROM c_pagina_sp WHERE fl_programa_sp=".$clave);
    $cl_pagina_sp = $row[0];
     # Eliminamos los archivos fisicos
     //$ruta =  VID_FAME_STU_LIB."/video_".$cl_pagina_sp."_".$clave;
    //eliminarDirec($ruta);
	
	#Elimnamos la carpeta videos.
    $folder = $_SERVER[DOCUMENT_ROOT]."/vanas_videos/fame/library/video_".$cl_pagina_sp."_".$clave; 
	
    for($i=0;$row=RecuperaRegistro($rsv);$i++){
      $fl_video_contenido_sp = $row[0];      
      #Eliminamos registros de la BD
      EjecutaQuery("DELETE FROM k_video_contenido_sp WHERE fl_video_contenido_sp=".$fl_video_contenido_sp);
    }
    EjecutaQuery("DELETE FROM c_pagina_sp WHERE fl_programa_sp=".$clave);
  }
  
  $result['fg_correcto'] = 1;
  
  echo json_encode((Object) $result);
  
  
  
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

  
  
?>