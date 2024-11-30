<?php

  # Librerias
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion(False,0, True);

  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  # Obtenemos el intituto del alumno que esta logeado
  $fl_instituto = ObtenInstituto($clave);
  # Obtenemos el perfil del usuario
  $fl_perfil = ObtenPerfilUsuario($clave);
  // echo $message_resp = genera_documento_sp($clave, 2, 137);exit;
  
  # Verifica que la clave corresponda al usuario actual
  if(empty($clave))
    MuestraPaginaError(ERR_SIN_PERMISO);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_language = RecibeParametroNumerico('fl_language');
  $cl_sesion = RecibeParametroHTML('cl_sesion');
  $ds_login = RecibeParametroHTML('ds_login');
  $ds_instituto = RecibeParametroHTML('ds_instituto');
  $ds_alias = RecibeParametroHTML('ds_alias');
  $ds_alias_bd = RecibeParametroHTML('ds_alias_bd');
  $ds_nombres = $_POST['ds_nombres'];
  $ds_apaterno = $_POST['ds_apaterno'];
  $ds_amaterno = $_POST['ds_amaterno'];
  $ds_email = RecibeParametroHTML('ds_email');
  $fg_genero = RecibeParametroHTML('fg_genero');
  $fe_nacimiento = RecibeParametroFecha('fe_nacimiento');
  $ds_profesion=RecibeParametroHTML('ds_profesion');
  $ds_compania=RecibeParametroHTML('ds_compania');
  $school_id=RecibeParametroHTML('school_id');
  $fl_grado=RecibeParametroNumerico('fl_grado');
  
  $ds_website = RecibeParametroHTML('ds_website');
  $ds_gustos = RecibeParametroHTML('ds_gustos');
  $ds_pasatiempos = RecibeParametroHTML('ds_pasatiempos');
  $ds_power = RecibeParametroHTML('ds_power');
  $ds_favorite_movie = RecibeParametroHTML('ds_favorite_movie');
  
  
  $ds_number = RecibeParametroHTML('ds_number');
  $ds_street = RecibeParametroHTML('ds_street');
  $ds_city = RecibeParametroHTML('ds_city');
  $ds_state = RecibeParametroHTML('ds_state');
  $ds_zip = RecibeParametroHTML('ds_zip');
  $fl_pais = RecibeParametroNumerico('fl_pais');
  
  $ds_password = RecibeParametroHTML('ds_password');
  $ds_password_conf = RecibeParametroHTML('ds_password_conf');

  $ds_ruta_foto = RecibeParametroHTML('ds_ruta_foto');
  $ds_ruta_avatar = RecibeParametroHTML('ds_ruta_avatar');
  $ds_foto_inst = RecibeParametroHTML('ds_foto_inst');
  
  $ds_rfc=RecibeParametroHTML('ds_rfc');
  
  #Notifiaciones
  $fg_nuevo_post=RecibeParametroBinario('fg_nuevo_post');
  $fg_coment_post=RecibeParametroBinario('fg_coment_post');
  $fg_like_post=RecibeParametroBinario('fg_like_post');
  $fg_ayuda_post=RecibeParametroBinario('fg_ayuda_post');
  $fg_follow=RecibeParametroBinario('fg_follow');
  $fg_ayuda_post_all_comunity=RecibeParametroBinario('fg_ayuda_post_all_comunity');
  $fg_session_completed=RecibeParametroBinario('fg_session_completed');
  
  # Valida campos obligatorios
  if($fl_perfil == PFL_ADMINISTRADOR AND empty($ds_instituto)){
    $ds_instituto_err = ERR_REQUERIDO;
    $no_tab = 1;
  }
  if(empty($ds_alias)){
    $ds_alias_err = ERR_REQUERIDO;
    $no_tab = 1;
  }  
  if(empty($ds_nombres)){
    $ds_nombres_err = ERR_REQUERIDO;
    $no_tab = 1;
  } 
  if(empty($ds_apaterno)){
    $ds_apaterno_err = ERR_REQUERIDO;
    $no_tab = 1;
  }
  if(empty($ds_email)){
    $ds_email_err = ERR_REQUERIDO;
    $no_tab = 1;
  }
  # Verifica que el formato del email sea valido
  if(!empty($ds_email) AND !ValidaEmail($ds_email)){
    $ds_email_err = ERR_FORMATO_EMAIL;
    $no_tab = 1;
  }
  
  # Verifica que el formato de la fecha sea valido
  if(!empty($fe_nacimiento) AND !ValidaFecha($fe_nacimiento)){
    $fe_nacimiento_err = ERR_FORMATO_FECHA;
    $no_tab = 1;
  }
  
  # Valida campos que no este en blanco
  if(empty($ds_number)){
    $ds_number_err = ERR_REQUERIDO;
    $no_tab = 4;
  }
  if(empty($ds_street)){
    $ds_street_err = ERR_REQUERIDO;
    $no_tab = 4;
  }
  if(empty($ds_city)){
    $ds_city_err = ERR_REQUERIDO;
    $no_tab = 4;
  }
  if(empty($ds_state)){
    $ds_state_err = ERR_REQUERIDO;
    $no_tab = 4;
  }
  if(empty($ds_zip)){
    $ds_zip_err = ERR_REQUERIDO;
    $no_tab = 4;
  }
  
  # Valida confirmacion de la contrasenia
  if((!empty($ds_password) OR !empty($ds_password_conf)) AND $ds_password <> $ds_password_conf){
    $ds_password_err = 101; // La contrase&ntilde; y su confirmaci&oacutE;n no coinciden.
    $no_tab = 5;
  }

  # Verifica que el tipo de archivo para avatar sea JPG
  $ext = strtolower(ObtenExtensionArchivo($_FILES['avatar']['name'][0]));
  if(!empty($ext) AND $ext!='jpg' AND $ext!='jpeg'){
    $ds_ruta_avatar_err = ERR_ARCHIVO_JPEG;
    $no_tab = 6;
  }

  # Verifica que el tipo de archivo para foto sea JPG
  $ext = ObtenExtensionArchivo($_FILES['foto']['name'][0]);
  if(!empty($ext) AND $ext!='jpg' AND $ext!='jpeg'){
    $ds_ruta_foto_err = ERR_ARCHIVO_JPEG;
    $no_tab = 6;
  }
  
  # Verifica que el tipo de archivo para foto del insitituto sea JPG
  $ext = ObtenExtensionArchivo($_FILES['foto_inst']['name'][0]);
  if(!empty($ext) AND $ext!='jpg' AND $ext!='jpeg' AND $fl_perfil == PFL_ADMINISTRADOR){
    $ds_foto_inst_err = ERR_ARCHIVO_JPEG;
    $no_tab = 6;
  }
  
  # Regresa a la forma con error
  $fg_error = $ds_instituto_err || $ds_alias_err || $ds_nombres_err || $ds_apaterno_err || $ds_email_err || $fe_nacimiento_err 
   || $ds_number_err || $ds_street_err || $ds_city_err || $ds_state_err || $ds_zip_err ||  $ds_password_err  || $ds_ruta_avatar_err || $ds_ruta_foto_err  || $ds_foto_inst_err;  
  
  if($fg_error){
    $result["datos"] = array(
      "fg_error" => $fg_error,
      "no_tab" => $no_tab,
      "ds_instituto_err" => ObtenMensaje($ds_instituto_err),
      "ds_alias_err" => ObtenMensaje($ds_alias_err),
      "ds_nombres_err" => ObtenMensaje($ds_nombres_err),
      "ds_apaterno_err" => ObtenMensaje($ds_apaterno_err),
      "ds_email_err" => ObtenMensaje($ds_email_err),
      "fe_nacimiento_err" => ObtenMensaje($fe_nacimiento_err),
      
      "ds_number_err" => ObtenMensaje($ds_number_err),
      "ds_street_err" => ObtenMensaje($ds_street_err),
      "ds_city_err" => ObtenMensaje($ds_city_err),
      "ds_state_err" => ObtenMensaje($ds_state_err),
      "ds_zip_err" => ObtenMensaje($ds_zip_err),
      
      "ds_password_err" => ObtenMensaje($ds_password_err),
      
      "ds_ruta_avatar_err" => ObtenMensaje($ds_ruta_avatar_err),
      "ds_ruta_foto_err" => ObtenMensaje($ds_ruta_foto_err),
      "ds_foto_inst_err" => ObtenMensaje($ds_foto_inst_err),
    );
    echo json_encode((Object) $result);
    exit;
  }
  
  # Prepara fechas en formato para insertar
  if(!empty($fe_nacimiento))
    $fe_nacimiento = "'".ValidaFecha($fe_nacimiento)."'";
  else
    $fe_nacimiento = "NULL";
  
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
  $ruta = $ruta_int."/".CARPETA_USER.$clave;
  # SI no esta la carpeta del intituto la crea
  if(!file_exists ( $ruta_int ))
    mkdir($ruta_int, 0777);
  if(!file_exists ( $ruta ))
    mkdir($ruta, 0777);
    

  # Recibe el archivo seleccionado
  $avatar_size = ObtenConfiguracion(30);
  if(!empty($_FILES['avatar']['tmp_name'][0])) {
    $Query  = "SELECT ds_ruta_avatar ";
    $Query .= "FROM ".$tbl." ";
    $Query .= "WHERE ".$campo."=$clave";
    $row = RecuperaValor($Query);
    if(!empty($row[0])){
      if(file_exists ( $ruta."/".$row[0] ))
        unlink($ruta."/".$row[0]);
    }
    $ext = strtolower(ObtenExtensionArchivo($_FILES['avatar']['name'][0]));
    $ds_ruta_avatar = "avatar_".$clave."_".rand(1, 32000).".$ext";
    move_uploaded_file($_FILES['avatar']['tmp_name'][0], $ruta."/".$ds_ruta_avatar);
    if($ext == "jpg" OR $ext == "jpeg")
      CreaThumb($ruta."/".$ds_ruta_avatar, $ruta."/".$ds_ruta_avatar, $avatar_size, $avatar_size);
  }
  if(!empty($_FILES['foto']['tmp_name'][0])) {    
    $Query  = "SELECT ds_ruta_foto ";
    $Query .= "FROM ".$tbl." ";
    $Query .= "WHERE ".$campo."=$clave";
    $row = RecuperaValor($Query);
    if(!empty($row[0])){
      if(file_exists ( $ruta."/".$row[0] ))
        unlink($ruta."/".$row[0]);
    }
    $ext = strtolower(ObtenExtensionArchivo($_FILES['foto']['name'][0]));
    $ds_ruta_foto = "pic_".$clave."_".rand(1, 32000).".$ext";
    move_uploaded_file($_FILES['foto']['tmp_name'][0], $ruta."/".$ds_ruta_foto);

    # Adjust the uploaded image to scale to the header size (1315 x 150 px)
    if($ext == "jpg" OR $ext == "jpeg")
      CreaThumb($ruta."/".$ds_ruta_foto, $ruta."/".$ds_ruta_foto, 1315, 150, 0, 0);
  }
  # imagen del la institucion
  if(!empty($_FILES['foto_inst']['tmp_name'][0])) {    
    $Query  = "SELECT ds_foto ";
    $Query .= "FROM c_instituto ";
    $Query .= "WHERE fl_instituto=$fl_instituto";
    $row = RecuperaValor($Query);
    if(!empty($row[0])){
      if(file_exists ( $ruta_int."/".$row[0] ))
        unlink($ruta_int."/".$row[0]);
    }
    $ext = strtolower(ObtenExtensionArchivo($_FILES['foto_inst']['name'][0]));
    $ds_foto_inst = "inst_".$fl_instituto."_".rand(1, 32000).".$ext";
    move_uploaded_file($_FILES['foto_inst']['tmp_name'][0], $ruta_int."/".$ds_foto_inst);

    # Adjust the uploaded image to scale to the header size (1315 x 150 px)
    if($ext == "jpg" OR $ext == "jpeg")
      CreaThumb($ruta_int."/".$ds_foto_inst, $ruta_int."/".$ds_foto_inst, 150, 150, 0, 0);
  }
    
  #Por extraña razon  el utlimo input que es de compania es el que esta actualmente,siempre viene con dato aunque la BD este vaciaentonces hacemos la comparacion para que no suceda.
  $Query="SELECT ds_alias FROM c_usuario WHERE fl_usuario=$clave ";
  $row=RecuperaValor($Query);
  $ds_alias2=$row['ds_alias'];
  if($ds_alias2==$ds_compania)
      $ds_compania="";



  $Query  = "UPDATE ".$tbl." SET ";
  if($fl_perfil == PFL_ESTUDIANTE_SELF){
      $Query .="fl_grado=$fl_grado, ";
  }
  $Query .=" ds_ruta_avatar='$ds_ruta_avatar',ds_profesion='$ds_profesion', ds_ruta_foto='$ds_ruta_foto',ds_compania='$ds_compania' , ";
  $Query .= "ds_website='$ds_website', ds_gustos='$ds_gustos', ds_pasatiempos='$ds_pasatiempos', ds_power='$ds_power', ds_favorite_movie='$ds_favorite_movie' ";
  $Query .= "WHERE ".$campo."=$clave";
  EjecutaQuery($Query);
  
  # Actualiza los datos del usuario

  # Set cookie to the selected lang for locale file schema(NEW)
  setcookie(IDIOMA_NOMBRE, $fl_language, time() + IDIOMA_VIGENCIA, '/');
  setcookie(IDIOMA_NOMBRE, $fl_language, time() + IDIOMA_VIGENCIA, '/fame/site');

  EjecutaQuery("UPDATE c_usuario SET fe_nacimiento=$fe_nacimiento WHERE fl_usuario=$clave ");


  $Query  = 'UPDATE c_usuario SET ds_nombres="'.$ds_nombres.'", ds_apaterno="'.$ds_apaterno.'", ds_amaterno="'.$ds_amaterno.'", fg_genero="'.$fg_genero.'", ';
  $Query .= ' ds_email="'.$ds_email.'", fl_language='.$fl_language.' ';
  # Buscamos si hay un alias
  $rowu = RecuperaValor("SELECT 1 FROM c_usuario WHERE fl_usuario!=$clave AND ds_alias='".$ds_alias."'");
  $alias_existe = $rowu[0];
  $update_alias = false;
  if(empty($alias_existe)){
    $Query .= ', ds_alias="'.$ds_alias.'" ';
    $update_alias = true;
  }
  $Query .= 'WHERE fl_usuario='.$clave.'';
  EjecutaQuery($Query);
  
  $Query=" SELECT COUNT(*) FROM k_usu_direccion_sp WHERE fl_usuario_sp = $clave ";
  $rop=RecuperaValor($Query);
  if(empty($rop[0])){
	  
	  $Query  = "INSERT INTO k_usu_direccion_sp (fl_pais,ds_state,ds_city,ds_number,  ";
	  $Query .= "ds_street, ds_zip, fl_usuario_sp ) VALUES ";
	  $Query  = "( $fl_pais,'$ds_state','$ds_city', '$ds_number',  ";
	  $Query .= " '$ds_street', '$ds_zip', $clave ) ";
	  EjecutaQuery($Query);
	  
	  
  }else{
  
  $Query  = "UPDATE k_usu_direccion_sp SET fl_pais = $fl_pais, ds_state = '$ds_state',ds_city = '$ds_city', ds_number = '$ds_number',  ";
  $Query .= "ds_street = '$ds_street', ds_zip = '$ds_zip' WHERE fl_usuario_sp = $clave ";
  EjecutaQuery($Query);
  }
  
  
  # Actualizamos el los datos del instituto
  if(($fl_perfil == PFL_ADMINISTRADOR)||($fl_perfil==PFL_ADM_CSF)){
      $QueryI = "UPDATE c_instituto SET school_id='$school_id', ds_instituto = '$ds_instituto', ds_foto = '$ds_foto_inst', ds_rfc='$ds_rfc' WHERE fl_instituto = $fl_instituto";
    EjecutaQuery($QueryI);
  }
  
  #Actualiza/Insetrt datos de las notificaciones.
  EjecutaQuery("DELETE FROM k_notify_fame_feed WHERE fl_usuario=$clave ");
  $Query="INSERT INTO k_notify_fame_feed (fl_usuario,fg_nuevo_post,fg_coment_post,fg_like_post,fg_ayuda_post,fg_follow,fg_ayuda_post_all_comunity,fg_session_completed)";
  $Query.="VALUES($clave,'$fg_nuevo_post','$fg_coment_post','$fg_like_post','$fg_ayuda_post','$fg_follow','$fg_ayuda_post_all_comunity','$fg_session_completed')";
  EjecutaQuery($Query);
  
  
  
  # Actualiza el password del usuario
  if(!empty($ds_password)) {
    $ds_password = sha256($ds_password);
    $Query  = "UPDATE c_usuario SET ds_password='$ds_password' ";
    $Query .= "WHERE fl_usuario=$clave";
    EjecutaQuery($Query);
  }
  
  # Enviamos notificacion si el usuario cambio su usuario
  if($ds_alias!=$ds_alias_bd && $update_alias == true){
    UserChangeAlias($clave);
  }

  $result["datos"] = array("fg_error" => $fg_error);
  echo json_encode((Object)$result);
?>