<?php
  # Libreria de funciones	
	require("../lib/self_general.php");
	
  # Libreria de funciones
	// require("../../modules/common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Obtenemos el perfil
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $delete = RecibeParametroNumerico('delete');
  
  # Si recibimos el parametro delete va a borrar el archivo
  # En caso contrario la va a guardar
  if(empty($delete)){
    # Rutas de las imagenes
    $storeFolder = PATH_SELF_UPLOADS_F."/".$fl_instituto;
    
    # Antes de que suba el archivo eliminara del servidor el actual
    if($fl_perfil == PFL_MAESTRO_SELF)
      $Query = "SELECT ds_oficial FROM c_maestro_sp WHERE fl_maestro_sp=$fl_usuario ";
    else
      $Query = "SELECT ds_oficial FROM c_alumno_sp WHERE fl_alumno_sp=$fl_usuario";
    $row = RecuperaValor($Query);
    $ds_oficial = $row[0];
    if(!empty($ds_oficial)){
      unlink($storeFolder."/".CARPETA_USER.$fl_usuario."/".$ds_oficial);
    }
    
    # Create a random int for original size and thumbnail picture
    $rand_int = rand(1, 32000);
    
    # Nombre de la official
    $tempFile = $_FILES['file']['tmp_name'];
    # Nombre original del archivo
    $name_original = $_FILES['file']['name'];
    
    # Obtenemos la extension del archivo
    $ext = ObtenExtensionArchivo($name_original);
    # Nuevo nombre del archivo
    $name_new = "official_".$fl_usuario.'_'.$rand_int.".".$ext;
    
    # Tamnio para ajustar la imagen oficial
    $foto_size = ObtenConfiguracion(80);
    
    # Carptea del usuario este nombre se compone con su fl_usuario
    # Ojo esta carpeta es d emucha utilidad para todo el sistema
    $carpeta_user = $storeFolder."/".CARPETA_USER.$fl_usuario;
    
    
    # Si no existe la carpeta la tiene que crear
    if (!file_exists($carpeta_user)) {
      mkdir($carpeta_user, 0777, true);
    }
    
    # Ruta de la foto donde se va a guardar que sera en su carpeta
    $targetFile =  $carpeta_user."/".$name_original;   
    
    # Sube el archivo
    if (!empty($_FILES)) {
      
        # Subimos automaticamente el archivo
        if(move_uploaded_file($tempFile,$targetFile)){
          # Ajustamos la imagen        
          CreaThumb($targetFile, $targetFile, 0, 0, $foto_size);
          $subio = True;
          # Cambiamos el nombre del archivo para identificarlo
          rename ($targetFile, $carpeta_user."/".$name_new);        
          
          # Actualizamos en la base de datos la informacion del nuevo
          if($fl_perfil == PFL_MAESTRO_SELF)
            $Query = "UPDATE c_maestro_sp SET ds_oficial='$name_new' WHERE fl_maestro_sp=$fl_usuario";
          else
            $Query = "UPDATE c_alumno_sp SET ds_oficial='$name_new' WHERE fl_alumno_sp=$fl_usuario";
          
          EjecutaQuery($Query);
        }
        else
          $subio = False;
        
        echo $subio;
         
    }
  }
  else{
    
    # Eliminamos el archivo del servidor
    $ds_ruta_oficial = ObtenFotoOficial($fl_usuario, True);
    $del = unlink($ds_ruta_oficial);
    // if($del)
      // echo "Borrado el archivo: ".$ds_ruta_oficial;
    // else
      // echo "NO!! se pudo borrare archivo: ".$ds_ruta_oficial;
    
    # Verificamos el perfil del usuario
    switch($fl_perfil){
      case PFL_MAESTRO_SELF:
      $QueryD = "UPDATE c_maestro_sp SET ds_oficial = '' WHERE fl_usuario=$fl_usuario";
      break;
      case PFL_ESTUDIANTE_SELF:
      $QueryD = "UPDATE c_alumno_sp SET ds_oficial = '' WHERE fl_usuario=%fl_usuario";
      break;
    }
    #Eliminamos el registro de la BD
    // echo $Query;
    EjecutaQuery($Query);
  }
?>