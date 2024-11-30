<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  require("../../lib/sp_forms.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Verifica que la clave corresponda al usuario actual
  if(empty($clave) OR $clave <> $fl_alumno)
    MuestraPaginaError(ERR_SIN_PERMISO);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $cl_sesion = RecibeParametroHTML('cl_sesion');
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
  $ds_website = RecibeParametroHTML('ds_website');
  $ds_gustos = RecibeParametroHTML('ds_gustos');
  $ds_pasatiempos = RecibeParametroHTML('ds_pasatiempos');
  
  
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
    
  # Verifica que el tipo de archivo para foto sea JPG
  $ext = ObtenExtensionArchivo($_FILES['foto']['name'][0]);
  if(!empty($ext) AND $ext!='jpg' AND $ext!='jpeg')
    $ds_ruta_foto_err = ERR_ARCHIVO_JPEG;
  
  # Regresa a la forma con error
  $fg_error = $ds_nombres_err || $ds_apaterno_err || $ds_password_err || $fe_nacimiento_err || $ds_email_err || $ds_ruta_avatar_err || $ds_ruta_foto_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='profile.php'>\n";
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('cl_sesion', $cl_sesion);
    Forma_CampoOculto('ds_login', $ds_login);
    Forma_CampoOculto('ds_nombres', $ds_nombres);
    Forma_CampoOculto('ds_nombres_err', $ds_nombres_err);
    Forma_CampoOculto('ds_apaterno', $ds_apaterno);
    Forma_CampoOculto('ds_apaterno_err', $ds_apaterno_err);
    Forma_CampoOculto('ds_amaterno', $ds_amaterno);
    Forma_CampoOculto('ds_password_err' , $ds_password_err);
    Forma_CampoOculto('fg_genero', $fg_genero);
    Forma_CampoOculto('fe_nacimiento', $fe_nacimiento);
    Forma_CampoOculto('fe_nacimiento_err', $fe_nacimiento_err);
    Forma_CampoOculto('ds_email', $ds_email);
    Forma_CampoOculto('ds_email_err', $ds_email_err);
    Forma_CampoOculto('fl_pais', $fl_pais);
    Forma_CampoOculto('fl_zona_horaria', $fl_zona_horaria);
    Forma_CampoOculto('ds_ruta_avatar', $ds_ruta_avatar);
    Forma_CampoOculto('ds_ruta_avatar_err', $ds_ruta_avatar_err);
    Forma_CampoOculto('ds_ruta_foto', $ds_ruta_foto);
    Forma_CampoOculto('ds_ruta_foto_err', $ds_ruta_foto_err);
    Forma_CampoOculto('ds_website', $ds_website);
    Forma_CampoOculto('ds_gustos', $ds_gustos);
    Forma_CampoOculto('ds_pasatiempos' , $ds_pasatiempos);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Recibe el archivo seleccionado
  $avatar_size = ObtenConfiguracion(30);
  if(!empty($_FILES['avatar']['tmp_name'][0])) {
    $ruta = PATH_ALU_IMAGES_F."/avatars";
    $Query  = "SELECT ds_ruta_avatar ";
    $Query .= "FROM c_alumno ";
    $Query .= "WHERE fl_alumno=$clave";
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      unlink($ruta."/".$row[0]);
    $ext = strtolower(ObtenExtensionArchivo($_FILES['avatar']['name'][0]));
    $ds_ruta_avatar = $ds_login."_ava".rand(1, 32000).".$ext";
    move_uploaded_file($_FILES['avatar']['tmp_name'][0], $ruta."/".$ds_ruta_avatar);
    if($ext == "jpg")
      CreaThumb($ruta."/".$ds_ruta_avatar, $ruta."/".$ds_ruta_avatar, $avatar_size, $avatar_size);
  }
  if(!empty($_FILES['foto']['tmp_name'][0])) {
    $ruta = PATH_ALU_IMAGES_F."/pictures";
    $Query  = "SELECT ds_ruta_foto ";
    $Query .= "FROM c_alumno ";
    $Query .= "WHERE fl_alumno=$clave";
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      unlink($ruta."/".$row[0]);
    $ext = strtolower(ObtenExtensionArchivo($_FILES['foto']['name'][0]));
    $ds_ruta_foto = $ds_login."_pic".rand(1, 32000).".$ext";
    move_uploaded_file($_FILES['foto']['tmp_name'][0], $ruta."/".$ds_ruta_foto);
    
    # Ajusta el maximo de dimensiones para imagenes
    if($ext == "jpg")
      CreaThumb($ruta."/".$ds_ruta_foto, $ruta."/".$ds_ruta_foto, 0, 0, 0, 720);
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
  $Query  = "UPDATE c_alumno SET fl_zona_horaria=$fl_zona_horaria, ds_ruta_avatar='$ds_ruta_avatar', ds_ruta_foto='$ds_ruta_foto', ";
  $Query .= "ds_website='$ds_website', ds_gustos='$ds_gustos', ds_pasatiempos='$ds_pasatiempos' ";
  $Query .= "WHERE fl_alumno=$clave";
  EjecutaQuery($Query);
  $Query  = "UPDATE k_ses_app_frm_1 SET ds_add_country='$fl_pais' ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  EjecutaQuery($Query);
  
  # Actualiza el password del usuario
  if(!empty($ds_password)) {
    $ds_password = sha256($ds_password);
    $Query  = "UPDATE c_usuario SET ds_password='$ds_password' ";
    $Query .= "WHERE fl_usuario=$clave";
    EjecutaQuery($Query);
  }
  
  # Actualiza el cookie de la zona horaria seleccionada
  ActualizaDiferenciaGMT(PFL_ESTUDIANTE, $clave);
  
  # Regresa a la pagina de origen
  header("Location: desktop.php");
  
?>