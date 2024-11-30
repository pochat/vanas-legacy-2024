<?php
  # Librerias
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Verifica que la clave corresponda al usuario actual
  if(empty($fl_usuario))
    MuestraPaginaError(ERR_SIN_PERMISO);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Obtenemos el intituto del alumno que esta logeado
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Obtenemos el perfil del usuario
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  
  # Recibe parametros
  $ds_foto1 = RecibeParametroHTML('ds_foto1');
  $type_img = RecibeParametroHTML('type_img');
  
  # Checamos que tabla va revisar
  if($fl_perfil == PFL_MAESTRO_SELF){
    $tbl = "c_maestro_sp ";
    $campo = "fl_maestro_sp ";
  }
  else{
    if($fl_perfil == PFL_ESTUDIANTE_SELF){
      $tbl = "c_alumno_sp";
      $campo = "fl_alumno_sp";
    }
    else{
      $tbl = "c_administrador_sp";
      $campo = "fl_adm_sp";
    }
  }
  # Si no esta la carpeta delusuario la crea
  $ruta_int = PATH_SELF_UPLOADS_F."/".$fl_instituto;
  $ruta = $ruta_int."/".CARPETA_USER.$fl_usuario;
  # SI no esta la carpeta del intituto la crea
  if(!file_exists ( $ruta_int ))
    mkdir($ruta_int, 0777);
  if(!file_exists ( $ruta ))
    mkdir($ruta, 0777);
  
  if($type_img=="A") {
      
    # Recibe el archivo seleccionado
    $avatar_size = ObtenConfiguracion(30);
    if(!empty($_FILES['ds_foto1']['tmp_name'])) {
      $Query  = "SELECT ds_ruta_avatar ";
      $Query .= "FROM ".$tbl." ";
      $Query .= "WHERE ".$campo."=$fl_usuario";
      $row = RecuperaValor($Query);
      if(!empty($row[0])){
        if(file_exists ( $ruta."/".$row[0] ))
          unlink($ruta."/".$row[0]);
      }
      $ext = strtolower(ObtenExtensionArchivo($_FILES['ds_foto1']['name']));
      $ds_ruta_avatar = "avatar_".$fl_usuario."_".rand(1, 32000).".$ext";
      move_uploaded_file($_FILES['ds_foto1']['tmp_name'], $ruta."/".$ds_ruta_avatar);
      if($ext == "jpg" OR $ext == "jpeg")
        CreaThumb($ruta."/".$ds_ruta_avatar, $ruta."/".$ds_ruta_avatar, $avatar_size, $avatar_size);
      
      EjecutaQuery("UPDATE ".$tbl." SET ds_ruta_avatar='".$ds_ruta_avatar."' WHERE ".$campo."=$fl_usuario");
      
      $result["datos"] = array("fg_error" => 0);
      echo json_encode((Object)$result);    
    }
    
  }
  else{
    if(!empty($_FILES['ds_foto1']['tmp_name'])) {    
      $Query  = "SELECT ds_ruta_foto ";
      $Query .= "FROM ".$tbl." ";
      $Query .= "WHERE ".$campo."=$fl_usuario";
      $row = RecuperaValor($Query);
      if(!empty($row[0])){
        if(file_exists ( $ruta."/".$row[0] ))
          unlink($ruta."/".$row[0]);
      }
      $ext = strtolower(ObtenExtensionArchivo($_FILES['ds_foto1']['name']));
      $ds_ruta_foto = "pic_".$fl_usuario."_".rand(1, 32000).".$ext";
      move_uploaded_file($_FILES['ds_foto1']['tmp_name'], $ruta."/".$ds_ruta_foto);

      # Adjust the uploaded image to scale to the header size (1315 x 150 px)
      if($ext == "jpg" OR $ext == "jpeg")
        CreaThumb($ruta."/".$ds_ruta_foto, $ruta."/".$ds_ruta_foto, 1315, 150, 0, 0);
      
      EjecutaQuery("UPDATE ".$tbl." SET ds_ruta_foto='".$ds_ruta_foto."' WHERE ".$campo."=$fl_usuario");
      
      $result["datos"] = array("fg_error" => 0);
      echo json_encode((Object)$result);   
    }
  }
  
?>