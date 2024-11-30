<?php
  # Libreria de funciones	
	require("../lib/self_general.php");

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe Parametros
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp_1');
  $cl_pagina = RecibeParametroNumerico('cl_pagina_1');  
  $archivo=RecibeParametroHTML('archivo');
  $fg_accion=RecibeParametroNumerico('fg_accion_1');
  $_POST[''];
 

  # Obtenemos el intituto del alumno
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Tipos de archivos
  $file_tiposDro = "image/*, application/*, rar, zip";
  $file_tipos = array('jpg', 'JPG', 'png', 'PNG', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'txt', 'rar', 'zip', 'psd', 'gif');
  
  
  # Upload file
  # Ruta de los archivos extras de los students
  $ruta_std = PATH_SELF_UPLOADS_F."/".$fl_instituto."/attachments/student_library";
  
  if($fg_accion==1){
    # Recibe parametros
    $archivo = RecibeParametroHTML('archivo');
    $ext = strtolower(ObtenExtensionArchivo($archivo));
    $ds_titulo = RecibeParametroHTML('ds_titulo_1');
    $ds_descripcion = RecibeParametroHTML('ds_descr_1');
    
    # Agregamos caracteres al nombre del archivo para identificarlo
    $archivo1 = explode(".", $archivo);
    $archivo_new = $archivo1[0].".".$ext;

    $err_titulo="";
    $err_imagen_repetida="";
    #Si existe el archivo se renombra.
    if ((file_exists($ruta_std."/".$archivo_new)) || (empty($ds_titulo)) ) {
        $res=true;
       if(file_exists($ruta_std."/".$archivo_new)){
           $err_imagen_repetida=1;
       }
       if(empty($ds_titulo)){
            $err_titulo=1;
       }
        //$archivo_new = $archivo1[0].rand(10,100).".".$ext; 
    }else{
			# Nombre original del archivo
			$file_name_ori = $_FILES['qqfile']['name'];
			# Nombre de la official
			$tempFile = $_FILES['qqfile']['tmp_name']; 
			
			# Creamos la carpeta si no existe
			if (!file_exists($ruta_std)) {
			  mkdir($ruta_std, 0777, true);
			}
			
			# uplad file
			$upload = move_uploaded_file($tempFile, $ruta_std."/".$archivo_new);
			if($upload){

			  $Query  = "INSERT INTO k_arch_student_library(fl_programa_sp, nb_archivo, ds_titulo, ds_descripcion, fe_file, fl_usu_upload) ";
			  $Query .= "VALUES($fl_programa_sp,'".$archivo_new."', '".$ds_titulo."', '".$ds_descripcion."', NOW(), ".$fl_usuario.") ";
			  $fl_arch_student_libra = EjecutaInsert($Query);
			  
              #Actualizamos si trae el dato de la pagiina.
              if(!empty($cl_pagina)){
                  $Query="UPDATE k_arch_student_library SET cl_pagina=$cl_pagina WHERE fl_arch_student_library=$fl_arch_student_libra ";
                  EjecutaQuery($Query);
              }


			  $res = false;
			}
			else{
			  $res = true;
			}
	
	}
	
	
    $result["err_titulo"]=$err_titulo;
    $result["err_imagen_repetida"]=$err_imagen_repetida;
    $result["error"] = $res;
    $result["Query"] = $Query;
    echo json_encode((Object) $result);
  }
  
  
  
  
  
  
  
  
  
  
  # Elimina  el archivo
  if($fg_accion==2){
	  
	  
    # Recibe Parametros
    $fl_archivo_delete =RecibeParametroNumerico('fl_archivo_delete');

    $rw = RecuperaValor("SELECT nb_archivo FROM k_arch_student_library WHERE fl_arch_student_library=".$fl_archivo_delete);
    $nb_archivo = $rw[0];
    
    # Si existe el archivo lo eliminamos
    $rt = $ruta_std."/".$nb_archivo;
    if(file_exists($rt)){
      $del = unlink($rt);
      if($del==true){
        EjecutaQuery("DELETE FROM k_arch_student_library WHERE fl_arch_student_library=".$fl_archivo_delete);
        $eliminado = true;
      }
      else
        $eliminado = false;
    }
    else{
      EjecutaQuery("DELETE FROM k_arch_student_library WHERE fl_arch_student_library=".$fl_archivo_delete);
      $eliminado = true;
    }
    
    $result["success"] = $eliminado;
    echo json_encode((Object) $result);
  }
?>