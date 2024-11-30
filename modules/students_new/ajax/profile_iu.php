<?php

  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require("../../../lib/sp_forms.inc.php");
  
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
  $ds_add_number = RecibeParametroHTML('ds_add_number');
  $ds_add_street = RecibeParametroHTML('ds_add_street');
  $ds_add_city = RecibeParametroHTML('ds_add_city');
  $ds_add_state = RecibeParametroHTML('ds_add_state');
  $ds_add_zip = RecibeParametroHTML('ds_add_zip');
  $ds_alias = RecibeParametroHTML('ds_alias');
  $ds_sin=RecibeParametroNumerico('ds_sin');

  # Responsable
  $ds_fname_r = RecibeParametroHTML('ds_fname_r');
  $ds_lname_r = RecibeParametroHTML('ds_lname_r');
  $ds_email_r = RecibeParametroHTML('ds_email_r');
  $ds_email_r_bd = RecibeParametroHTML('ds_email_r_bd');
  $fg_email = RecibeParametroBinario('fg_email');
  $fl_sesion = RecibeParametroNumerico('fl_sesion');
  $ds_aemail_r = RecibeParametroHTML('ds_aemail_r');
  $ds_pnumber_r = RecibeParametroHTML('ds_pnumber_r');
  $ds_relation_r = RecibeParametroHTML('ds_relation_r');
  $fg_respo = RecibeParametroBinario('fg_respo');
  
 # Valida campos obligatorios
/*  if(empty($ds_nombres))
    $ds_nombres_err = ERR_REQUERIDO;

  if(empty($ds_apaterno))
    $ds_apaterno_err = ERR_REQUERIDO;
  */
  if(empty($ds_email))
    $ds_email_err = ERR_REQUERIDO;

  # Valida confirmacion de la contrasenia
  if((!empty($ds_password) OR !empty($ds_password_conf)) AND $ds_password <> $ds_password_conf)
    $ds_password_err = 101; // La contrase&ntilde; y su confirmaci&oacutE;n no coinciden.

	# Verifica que el formato de la fecha sea valido
  //if(!empty($fe_nacimiento) AND !ValidaFecha($fe_nacimiento))
  //  $fe_nacimiento_err = ERR_FORMATO_FECHA;
  
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
  
  # Valida campos que no este en blanco
 /* if(empty($ds_add_number))
    $ds_add_number_err = ERR_REQUERIDO;
  if(empty($ds_add_street))
    $ds_add_street_err = ERR_REQUERIDO;
  if(empty($ds_add_city))
    $ds_add_city_err = ERR_REQUERIDO;
  if(empty($ds_add_state))
    $ds_add_state_err = ERR_REQUERIDO;
  if(empty($ds_add_zip))
    $ds_add_zip_err = ERR_REQUERIDO;
    */
  if(empty($ds_alias))
    $ds_alias_err = ERR_REQUERIDO;
  
  // # Validamos info de la persona responsable
  // if(!empty($fg_resp)){
    // if(empty($ds_fname_r))
      // $ds_fname_r_err = ERR_REQUERIDO;
    // if(empty($ds_lname_r))
      // $ds_lname_r_err = ERR_REQUERIDO;
    // if(empty($ds_email_r))
      // $ds_email_r_err = ERR_REQUERIDO;
    // if(empty($ds_pnumber_r))
      // $ds_pnumber_r_err = ERR_REQUERIDO;
    // if(empty($ds_relation_r))
      // $ds_relation_r_err = ERR_REQUERIDO;
  // }
  // else{
    // if(!empty($ds_fname_r))
      // $ds_fname_r_err = ERR_REQUERIDO;
    // if(!empty($ds_lname_r))
      // $ds_lname_r_err = ERR_REQUERIDO;
    // if(!empty($ds_email_r))
      // $ds_email_r_err = ERR_REQUERIDO;
    // if(!empty($ds_pnumber_r))
      // $ds_pnumber_r_err = ERR_REQUERIDO;
    // if(!empty($ds_relation_r))
      // $ds_relation_r_err = ERR_REQUERIDO;
  // }
    
  
  # Regresa a la forma con error
  $fg_error =  $ds_password_err || $ds_email_err || $ds_ruta_avatar_err || $ds_ruta_foto_err
  || $ds_alias_err 
  || $ds_email_r_err || $ds_pnumber_r_err || $ds_relation_r_err;
  
  if($fg_error){
    $result["datos"] = array(
      "fg_error" => $fg_error,
      
      "ds_password_err" => ObtenMensaje($ds_password_err),
      
      "ds_email_err" => ObtenMensaje($ds_email_err),
      "ds_ruta_avatar_err" => ObtenMensaje($ds_ruta_avatar_err),
      "ds_alias_err" => ObtenMensaje($ds_alias_err),
      
      "ds_email_r_err" => ObtenMensaje($ds_email_r_err),   
      "ds_pnumber_r_err" => ObtenMensaje($ds_pnumber_r_err), 
      "ds_relation_r_err" => ObtenMensaje($ds_relation_r_err)
    );

    echo json_encode((Object) $result);
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
    if($ext == "jpg" OR $ext == "jpeg")
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
  #$Query  = "UPDATE c_usuario SET ds_nombres='$ds_nombres', ds_apaterno='$ds_apaterno', ds_amaterno='$ds_amaterno', ";
  $Query  = "UPDATE c_usuario SET ";
  $Query .= "ds_email='$ds_email', ds_alias='$ds_alias' ";
  $Query .= "WHERE fl_usuario=$clave";
  EjecutaQuery($Query);
  $Query  = "UPDATE c_alumno SET fl_zona_horaria=$fl_zona_horaria, ds_ruta_avatar='$ds_ruta_avatar', ds_ruta_foto='$ds_ruta_foto', ";
  $Query .= "ds_website='$ds_website', ds_gustos='$ds_gustos', ds_pasatiempos='$ds_pasatiempos' ";
  $Query .= "WHERE fl_alumno=$clave";
  EjecutaQuery($Query);
  # Si la informacion de la persona responsable
  $fg_responsable = 0;
  if(!empty($ds_fname_r) && !empty($ds_lname_r) && !empty($ds_email_r) && !empty($ds_pnumber_r) && !empty($ds_relation_r)){    
    # Actualizamos o insertamos datos de la persona resposable
    if(ExisteEnTabla('k_presponsable', 'cl_sesion', $cl_sesion)){
     $Queryr  = "UPDATE k_presponsable SET ds_fname_r = '$ds_fname_r',ds_lname_r = '$ds_lname_r',ds_email_r = '$ds_email_r', ";
     $Queryr .= "ds_aemail_r = '$ds_aemail_r',ds_pnumber_r = '$ds_pnumber_r', ds_relation_r = '$ds_relation_r' ";
     $Queryr .= "WHERE cl_sesion = '$cl_sesion'";
     # Verifica si ya se el envio el correo de notificacion  
     if(!empty($fg_email)){
       # Si el correo es diferente enviara
       if(strcasecmp ($ds_email_r, $ds_email_r_bd)==0)
         $fg_email = 0;
       else
         $fg_email = 1;
     }
     # Enviara correo
     else
       $fg_email = 1;
    }
    else{
      $Queryr = "INSERT INTO k_presponsable (cl_sesion,ds_fname_r,ds_lname_r,ds_email_r,ds_aemail_r,ds_pnumber_r,ds_relation_r) ";
      $Queryr .= "VALUES ('$cl_sesion', '$ds_fname_r', '$ds_lname_r', '$ds_email_r', '$ds_aemail_r', '$ds_pnumber_r', '$ds_relation_r') ";          
      $fg_email = 1;
    }    
    $fg_responsable = 1;
    EjecutaQuery($Queryr);
    # Enviamos notificacion    
    if(!empty($fg_email)){
      # Enviamos email a la persona responsable
      $email_noreply = ObtenConfiguracion(4);
      $app_frm_email = ObtenConfiguracion(83);
      # Obtenemos el template que se le enviara a la person responsible
      $Queryy = "SELECT b.fl_sesion FROM c_usuario a, c_sesion b ";
      $Queryy .= "WHERE a.cl_sesion = b.cl_sesion AND fl_usuario=".$fl_alumno;
      $rowy = RecuperaValor($Queryy);
      $fl_sesion = $rowy[0];
      $message_resp = genera_documento($fl_sesion, 2, 38);
      # Mensage
     // if(EnviaMailHTML($email_noreply, $email_noreply, $ds_email_r, ObtenEtiqueta(865), $message_resp, $app_frm_email))
     //   EjecutaQuery("UPDATE k_presponsable SET fg_email='1' WHERE cl_sesion='".$cl_sesion."'");
    }
  }else{
	  
	  $Queryf1 = "UPDATE k_ses_app_frm_1 SET fg_responsable='0' WHERE cl_sesion='".$cl_sesion."'";
      EjecutaQuery($Queryf1);
      $Queryr  = "UPDATE k_presponsable SET ds_fname_r = '$ds_fname_r',ds_lname_r = '$ds_lname_r',ds_email_r = '$ds_email_r', ";
      $Queryr .= "ds_aemail_r = '$ds_aemail_r',ds_pnumber_r = '$ds_pnumber_r', ds_relation_r = '$ds_relation_r' ";
      $Queryr .= "WHERE cl_sesion = '$cl_sesion'";
      EjecutaQuery($Queryr);
	  
	  
  }
  /*
  $Query  = "UPDATE k_ses_app_frm_1 SET ds_sin=$ds_sin, ds_add_number='$ds_add_number', ds_add_street='$ds_add_street',ds_add_city='$ds_add_city', ds_add_state='$ds_add_state', ";
  $Query .= "ds_add_zip='$ds_add_zip', ds_add_country='$fl_pais', fg_responsable='$fg_responsable' ";  
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  EjecutaQuery($Query);*/
  
  $Query  = "UPDATE k_ses_app_frm_1 SET ds_sin=$ds_sin, ";
  $Query .= "fg_responsable='$fg_responsable' ";  
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

  $result["datos"] = array("fg_error" => $fg_error);
  echo json_encode((Object)$result);
?>