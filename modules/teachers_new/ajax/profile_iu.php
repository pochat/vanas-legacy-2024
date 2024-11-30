<?php
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require("../../../lib/sp_forms.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False);

  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Verifica que la clave corresponda al usuario actual
  if(empty($clave) OR $clave <> $fl_maestro)
    MuestraPaginaError(ERR_SIN_PERMISO);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $ds_login = RecibeParametroHTML('ds_login');
  $ds_nombres = RecibeParametroHTML('ds_nombres');
  $ds_apaterno = RecibeParametroHTML('ds_apaterno');
  $ds_amaterno = RecibeParametroHTML('ds_amaterno');
  $ds_password = RecibeParametroHTML('ds_password');
  $ds_password_conf = RecibeParametroHTML('ds_password_conf');
  $fg_genero = RecibeParametroHTML('fg_genero');
  $fe_nacimiento = RecibeParametroFecha('fe_nacimiento');
  $ds_email = RecibeParametroHTML('ds_email');
  $fl_pais = RecibeParametroNumerico('fl_pais');
  $fl_zona_horaria = RecibeParametroNumerico('fl_zona_horaria');
  $ds_ruta_avatar = RecibeParametroHTML('ds_ruta_avatar');
  $ds_ruta_foto = RecibeParametroHTML('ds_ruta_foto');
  $ds_empresa = RecibeParametroHTML('ds_empresa');
  $ds_website = RecibeParametroHTML('ds_website');
  $ds_gustos = RecibeParametroHTML('ds_gustos');
  $ds_pasatiempos = RecibeParametroHTML('ds_pasatiempos');
  $ds_biografia = RecibeParametroHTML('ds_biografia');
  
  # Valida campos obligatorios
  if(empty($ds_nombres))
    $ds_nombres_err = ERR_REQUERIDO;

  if(empty($ds_apaterno))
    $ds_apaterno_err = ERR_REQUERIDO;
  
  if(empty($ds_email))
    $ds_email_err = ERR_REQUERIDO;

  # Valida confirmacion de la contrasenia
  if((!empty($ds_password) OR !empty($ds_password_conf)) AND $ds_password <> $ds_password_conf)
    $ds_password_err = 101; // La contrase&ntilde; y su confirmaci&oacutE;n no coinciden.

	# Verifica que el formato de la fecha sea valido
  if(!empty($fe_nacimiento) AND !ValidaFecha($fe_nacimiento))
    $fe_nacimiento_err = ERR_FORMATO_FECHA;
  
  # Verifica que el formato del email sea valido
  if(!empty($ds_email) AND !ValidaEmail($ds_email))
    $ds_email_err = ERR_FORMATO_EMAIL;
  
  # Verifica que el tipo de archivo para avatar sea JPG
  $ext = strtolower(ObtenExtensionArchivo($_FILES['avatar']['name'][0]));
  if(!empty($ext) AND $ext!='jpg' AND $ext!='jpeg')
    $ds_ruta_avatar_err = ERR_ARCHIVO_JPEG;

  # Regresa a la forma con error
  $fg_error = $ds_nombres_err || $ds_apaterno_err || $ds_password_err || $fe_nacimiento_err || $ds_email_err || $ds_ruta_avatar_err;

  if($fg_error){
    $result["datos"] = array(
      "fg_error" => $fg_error,
      "ds_nombres_err" => ObtenMensaje($ds_nombres_err),
      "ds_apaterno_err" => ObtenMensaje($ds_apaterno_err),
      "ds_password_err" => ObtenMensaje($ds_password_err),
      "fe_nacimiento_err" => ObtenMensaje($fe_nacimiento_err),
      "ds_email_err" => ObtenMensaje($ds_email_err),
      "ds_ruta_avatar_err" => ObtenMensaje($ds_ruta_avatar_err)
    );

    echo json_encode((Object) $result);
    exit;
  }

  # Recibe el archivo seleccionado
  $avatar_size = ObtenConfiguracion(30);
  if(!empty($_FILES['avatar']['tmp_name'][0])) {
    $ruta = PATH_MAE_IMAGES_F."/avatars";
    $Query  = "SELECT ds_ruta_avatar ";
    $Query .= "FROM c_maestro ";
    $Query .= "WHERE fl_maestro=$clave";
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      unlink($ruta."/".$row[0]);
    $ext = strtolower(ObtenExtensionArchivo($_FILES['avatar']['name'][0]));
    $ds_ruta_avatar = $ds_login."_ava".rand(1, 32000).".$ext";
    move_uploaded_file($_FILES['avatar']['tmp_name'][0], $ruta."/".$ds_ruta_avatar);
    if($ext == "jpg" OR $ext == "jpeg")
      CreaThumb($ruta."/".$ds_ruta_avatar, $ruta."/".$ds_ruta_avatar, $avatar_size, $avatar_size);
  }
  if(!empty($_FILES['foto']['tmp_name'][0])) {
    $ruta = PATH_MAE_IMAGES_F."/pictures";
    $Query  = "SELECT ds_ruta_foto ";
    $Query .= "FROM c_maestro ";
    $Query .= "WHERE fl_maestro=$clave";
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      unlink($ruta."/".$row[0]);
    $ext = strtolower(ObtenExtensionArchivo($_FILES['foto']['name'][0]));
    $ds_ruta_foto = $ds_login."_pic".rand(1, 32000).".$ext";
    move_uploaded_file($_FILES['foto']['tmp_name'][0], $ruta."/".$ds_ruta_foto);

    # Adjust the uploaded image to scale to the header size (1315 x 150 px)
    if($ext == "jpg" OR $ext == "jpeg")
      CreaThumb($ruta."/".$ds_ruta_foto, $ruta."/".$ds_ruta_foto, 1315, 150, 0, 0);
  }
  
  # Prepara fechas en formato para insertar
  if(!empty($fe_nacimiento))
    $fe_nacimiento = "'".ValidaFecha($fe_nacimiento)."'";
  else
    $fe_nacimiento = "NULL";

  # Actualiza los datos del usuario
  $Query  = "UPDATE c_usuario SET ds_nombres='$ds_nombres', ds_apaterno='$ds_apaterno', ds_amaterno='$ds_amaterno', fg_genero='$fg_genero', ";
  $Query .= "fe_nacimiento=$fe_nacimiento, ds_email='$ds_email' ";
  $Query .= "WHERE fl_usuario=$clave";
  EjecutaQuery($Query);
  $Query  = "UPDATE c_maestro SET fl_pais=$fl_pais, fl_zona_horaria=$fl_zona_horaria, ds_ruta_avatar='$ds_ruta_avatar', ";
  $Query .= "ds_ruta_foto='$ds_ruta_foto', ds_empresa='$ds_empresa', ds_website='$ds_website', ds_gustos='$ds_gustos', ds_pasatiempos='$ds_pasatiempos', ";
  $Query .= "ds_biografia='$ds_biografia' ";
  $Query .= "WHERE fl_maestro=$clave";
  EjecutaQuery($Query);
  
  # Actualiza el password del usuario
  if(!empty($ds_password)) {
    $ds_password = sha256($ds_password);
    $Query  = "UPDATE c_usuario SET ds_password='$ds_password' ";
    $Query .= "WHERE fl_usuario=$clave";
    EjecutaQuery($Query);
  }
  
  # Actualiza el cookie de la zona horaria seleccionada
  ActualizaDiferenciaGMT(PFL_MAESTRO, $clave);

  $result["datos"] = array("fg_error" => $fg_error);
  echo json_encode((Object)$result);
?>