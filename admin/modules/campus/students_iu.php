<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ALUMNOS, $permiso) OR $permiso == PERMISO_ALTA) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_login = RecibeParametroHTML('ds_login');
  $ds_password = RecibeParametroHTML('ds_password');
  $ds_password_conf = RecibeParametroHTML('ds_password_conf');
  $cl_sesion = RecibeParametroHTML('cl_sesion');
  $ds_nombres = RecibeParametroHTML('ds_nombres', True);
  $ds_apaterno = RecibeParametroHTML('ds_apaterno', True);
  $ds_amaterno = RecibeParametroHTML('ds_amaterno', True);
  $fg_international = RecibeParametroBinario('fg_international');
  $fg_genero = RecibeParametroHTML('fg_genero');
  $fe_nacimiento = RecibeParametroFecha('fe_nacimiento');
  $ds_email = RecibeParametroHTML('ds_email');
  $ds_a_email = RecibeParametroHTML('ds_a_email');
  $fl_perfil = RecibeParametroNumerico('fl_perfil');
  $nb_perfil = RecibeParametroHTML('nb_perfil');
  $fg_activo = RecibeParametroBinario('fg_activo');
  $fe_alta = RecibeParametroFecha('fe_alta');
  $fe_ultacc = RecibeParametroFecha('fe_ultacc');
  $no_accesos = RecibeParametroNumerico('no_accesos');
  $fg_pago = RecibeParametroBinario('fg_pago');
  $ds_notas= RecibeParametroHTML('ds_notas', True);
  $fe_carta = RecibeParametroFecha('fe_carta');
  $fe_contrato = RecibeParametroFecha('fe_contrato');
  $fe_fin = RecibeParametroFecha('fe_fin');
  $fe_completado = RecibeParametroFecha('fe_completado');
  $fe_emision = RecibeParametroFecha('fe_emision');
  $fe_graduacion = RecibeParametroFecha('fe_graduacion');
  $fg_certificado = RecibeParametroBinario('fg_certificado');
  $fg_desercion = RecibeParametroBinario('fg_desercion');
  $fg_dismissed = RecibeParametroBinario('fg_dismissed');
  $fg_job = RecibeParametroBinario('fg_job');
  $fg_graduacion = RecibeParametroBinario('fg_graduacion');
  $fg_honores = RecibeParametroBinario('fg_honores');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $fl_periodo = RecibeParametroNumerico('fl_periodo');
  $fl_grupo = RecibeParametroNumerico('fl_grupo');
  //jgfl 03-11-2014
  $ds_number = RecibeParametroHTML('ds_number', True);
  $ds_alt_number = RecibeParametroHTML('ds_alt_number', True);
  $ds_add_number = RecibeParametroHTML('ds_add_number', True);
  $ds_add_street = RecibeParametroHTML('ds_add_street', True);
  $ds_add_city = RecibeParametroHTML('ds_add_city', True);
  $ds_add_state = RecibeParametroHTML('ds_add_state', True);
  $ds_add_zip = RecibeParametroHTML('ds_add_zip', True);
  $ds_add_country = RecibeParametroHTML('ds_add_country', True);
  #mailing address
  $ds_m_add_number = RecibeParametroHTML('ds_m_add_number', True);
  $ds_m_add_street = RecibeParametroHTML('ds_m_add_street', True);
  $ds_m_add_city = RecibeParametroHTML('ds_m_add_city', True);
  $ds_m_add_state = RecibeParametroHTML('ds_m_add_state', True);
  $ds_m_add_zip = RecibeParametroHTML('ds_m_add_zip', True);
  $ds_m_add_country = RecibeParametroHTML('ds_m_add_country', True);
  $ds_link_to_portfolio = RecibeParametroHTML('ds_link_to_portfolio', True);
  #promedio total
  $no_promedio_t = RecibeParametroHTML('no_promedio_t');

  # Obtenemos la fecha de la ultima clase
  # Deactivate students 2 weeks after finishing their last class
  $fe_ult_class = RecibeParametroHTML('fe_ult_class');
  # obtenemos el fg_activo de la BD y si esta activado lo desactiva y si no toma el valor que se le recibe
  $rowbd = RecuperaValor("SELECT fg_activo FROM c_usuario WHERE fl_usuario=$clave");
  $fg_activo_bd = $rowbd[0];
  if(!empty($fe_ult_class) AND $fe_ult_class==date('Y-m-d') AND !empty($fg_activo_bd))
    $fg_activo = 0;
  else  
    $fg_activo = $fg_activo;


  # Valida campos obligatorios
  if(empty($ds_login))
    $ds_login_err = ERR_REQUERIDO;
  if(empty($clave) AND empty($ds_password))
    $ds_password_err = ERR_REQUERIDO;
  if(empty($clave) AND empty($ds_password_conf))
    $ds_password_conf_err = ERR_REQUERIDO;
  if(empty($ds_nombres))
    $ds_nombres_err = ERR_REQUERIDO;
  if(empty($ds_apaterno))
    $ds_apaterno_err = ERR_REQUERIDO;
  if(empty($ds_email))
    $ds_email_err = ERR_REQUERIDO;
  if(empty($fl_perfil))
    $fl_perfil_err = ERR_REQUERIDO;
  //jgfl 03-11-2014
  if(empty($ds_number))
    $ds_number_err = ERR_REQUERIDO;
  if(empty($ds_alt_number))
    $ds_alt_number_err = ERR_REQUERIDO;
  if(empty($ds_add_number))
    $ds_add_number_err = ERR_REQUERIDO;
  if(empty($ds_add_street))
    $ds_add_street_err = ERR_REQUERIDO;
  if(empty($ds_add_city))
    $ds_add_city_err = ERR_REQUERIDO;
  if(empty($ds_add_state))
    $ds_add_state_err = ERR_REQUERIDO;
  if(empty($ds_add_zip))
    $ds_add_zip_err = ERR_REQUERIDO;
  if(empty($ds_add_country))
    $ds_add_country_err = ERR_REQUERIDO;
    
  # Valida que no exista el registro
  if(empty($clave) AND !empty($ds_login) AND ExisteEnTabla('c_usuario', 'ds_login', $ds_login))
    $ds_login_err = ERR_DUPVAL;
    
  # Valida si existe el registro en la tabla k_pctia
  if(ExisteEnTabla('k_pctia', 'fl_alumno', $clave) AND ExisteEnTabla('k_pctia', 'fl_programa', $fl_programa))
    $inserta = 0;
  else
    $inserta = 1;
  
  # Valida confirmacion de la contrasenia
  if((empty($clave)) AND ((!empty($ds_password) OR !empty($ds_password_conf)) AND $ds_password <> $ds_password_conf))
    $ds_password_conf_err = 101; // La contrase&ntilde; y su confirmaci&oacutE;n no coinciden.
  
  # Verifica que el formato de la fecha sea valido
  if(!empty($fe_nacimiento) AND !ValidaFecha($fe_nacimiento))
    $fe_nacimiento_err = ERR_FORMATO_FECHA;
  if(!empty($fe_carta) AND !ValidaFecha($fe_carta))
    $fe_carta_err = ERR_FORMATO_FECHA;
  if(!empty($fe_contrato) AND !ValidaFecha($fe_contrato))
    $fe_contrato_err = ERR_FORMATO_FECHA;
  if(!empty($fe_fin) AND !ValidaFecha($fe_fin))
    $fe_fin_err = ERR_FORMATO_FECHA;
  if(!empty($fe_completado) AND !ValidaFecha($fe_completado))
    $fe_completado_err = ERR_FORMATO_FECHA;
  if(!empty($fe_emision) AND !ValidaFecha($fe_emision))
    $fe_emision_err = ERR_FORMATO_FECHA;
  if(!empty($fe_graduacion) AND !ValidaFecha($fe_graduacion))
    $fe_graduacion_err = ERR_FORMATO_FECHA;
  
  # Verifica que el formato del email sea valido
  if(!empty($ds_email) AND !ValidaEmail($ds_email))
    $ds_email_err = ERR_FORMATO_EMAIL;
  if(!empty($ds_a_email) AND !ValidaEmail($ds_a_email))
    $ds_a_email_err = ERR_FORMATO_EMAIL;
  
  # Verifica que el tipo de archivo para avatar sea JPG
  $ext = strtolower(ObtenExtensionArchivo($_FILES['ds_foto_oficial']['name'][0]));
  if(!empty($ext) AND $ext!='jpg' AND $ext!='jpeg')
    $ds_foto_oficial_err = ERR_ARCHIVO_JPEG;
  
  # Regresa a la forma con error
  $fg_error = $ds_login_err || $ds_password_err || $ds_password_conf_err || $ds_nombres_err || $ds_apaterno_err || $fe_nacimiento_err || $ds_email_err || $fl_perfil_err  || $fe_carta_err || $fe_contrato_err || $fe_fin_err || $fe_completado_err || $fe_emision_err || $fe_graduacion_err
            || $ds_number_err || $ds_alt_number_err || $ds_add_number_err || $ds_add_street_err || $ds_add_city_err || $ds_add_state_err || $ds_add_zip_err || $ds_add_country_err
            || $ds_a_email_err || $ds_foto_oficial_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_login' , $ds_login);
    Forma_CampoOculto('ds_login_err' , $ds_login_err);
    Forma_CampoOculto('ds_password_err' , $ds_password_err);
    Forma_CampoOculto('ds_password_conf_err' , $ds_password_conf_err);
    Forma_CampoOculto('cl_sesion' , $cl_sesion);
    Forma_CampoOculto('ds_nombres' , $ds_nombres);
    Forma_CampoOculto('ds_nombres_err' , $ds_nombres_err);
    Forma_CampoOculto('ds_apaterno' , $ds_apaterno);
    Forma_CampoOculto('ds_apaterno_err' , $ds_apaterno_err);
    Forma_CampoOculto('ds_amaterno' , $ds_amaterno);
    Forma_CampoOculto('fg_international' , $fg_international);
    Forma_CampoOculto('fg_genero' , $fg_genero);
    Forma_CampoOculto('fe_nacimiento' , $fe_nacimiento);
    Forma_CampoOculto('fe_nacimiento_err' , $fe_nacimiento_err);
    Forma_CampoOculto('ds_email' , $ds_email);
    Forma_CampoOculto('ds_email_err' , $ds_email_err);
    Forma_CampoOculto('ds_a_email' , $ds_a_email);
    Forma_CampoOculto('ds_a_email_err' , $ds_a_email_err);
    Forma_CampoOculto('fl_perfil' , $fl_perfil);
    Forma_CampoOculto('fl_perfil_err' , $fl_perfil_err);
    Forma_CampoOculto('nb_perfil' , $nb_perfil);
    Forma_CampoOculto('fg_activo' , $fg_activo);
    Forma_CampoOculto('fe_alta' , $fe_alta);
    Forma_CampoOculto('fe_ultacc' , $fe_ultacc);
    Forma_CampoOculto('no_accesos' , $no_accesos);
    Forma_CampoOculto('fg_pago' , $fg_pago);
    Forma_CampoOculto('ds_notas' , $ds_notas);
    Forma_CampoOculto('fe_carta' , $fe_carta);
    Forma_CampoOculto('fe_carta_err' , $fe_carta_err);
    Forma_CampoOculto('fe_contrato' , $fe_contrato);
    Forma_CampoOculto('fe_contrato_err' , $fe_contrato_err);
    Forma_CampoOculto('fe_fin' , $fe_fin);
    Forma_CampoOculto('fe_fin_err' , $fe_fin_err);
    Forma_CampoOculto('fe_completado' , $fe_completado);
    Forma_CampoOculto('fe_completado_err' , $fe_completado_err);
    Forma_CampoOculto('fe_emision' , $fe_emision);
    Forma_CampoOculto('fe_emision_err' , $fe_emision_err);
    Forma_CampoOculto('fe_graduacion' , $fe_graduacion);
    Forma_CampoOculto('fe_graduacion_err' , $fe_graduacion_err);
    Forma_CampoOculto('fl_periodo', $fl_periodo);
    Forma_CampoOculto('fg_dismissed' , $fg_dismissed);
    Forma_CampoOculto('fg_desercion' , $fg_desercion);
    Forma_CampoOculto('fg_job' , $fg_job);
    Forma_CampoOculto('fg_graduacion' , $fg_graduacion);
     //jgfl 03-11-2014
    Forma_CampoOculto('ds_number' , $ds_number);
    Forma_CampoOculto('ds_number_err' , $ds_number_err);
    Forma_CampoOculto('ds_alt_number' , $ds_alt_number);
    Forma_CampoOculto('ds_alt_number_err' , $ds_alt_number_err);
    Forma_CampoOculto('ds_add_number', $ds_add_number);
    Forma_CampoOculto('ds_add_number_err', $ds_add_number_err);
    Forma_CampoOculto('ds_add_street', $ds_add_street);
    Forma_CampoOculto('ds_add_street_err', $ds_add_street_err);
    Forma_CampoOculto('ds_add_city', $ds_add_city);
    Forma_CampoOculto('ds_add_city_err', $ds_add_city_err);
    Forma_CampoOculto('ds_add_state', $ds_add_state);
    Forma_CampoOculto('ds_add_state_err', $ds_add_state_err);
    Forma_CampoOculto('ds_add_zip', $ds_add_zip);
    Forma_CampoOculto('ds_add_zip_err', $ds_add_zip_err);
    Forma_CampoOculto('ds_add_country', $ds_add_country);
    Forma_CampoOculto('ds_add_country_err', $ds_add_country_err);
    # Malign address
    Forma_CampoOculto('ds_m_add_number', $ds_m_add_number);
    Forma_CampoOculto('ds_m_add_street', $ds_m_add_street);
    Forma_CampoOculto('ds_m_add_city', $ds_m_add_city);
    Forma_CampoOculto('ds_m_add_state', $ds_m_add_state);
    Forma_CampoOculto('ds_m_add_zip', $ds_m_add_zip);
    Forma_CampoOculto('ds_m_add_country', $ds_m_add_country);
    Forma_CampoOculto('ds_link_to_portfolio', $ds_link_to_portfolio);
    Forma_CampoOculto('ds_foto_oficial',$ds_foto_oficial);
    Forma_CampoOculto('ds_foto_oficial_err',$ds_foto_oficial_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Prepara fechas en formato para insertar
  if(!empty($fe_nacimiento))
    $fe_nacimiento = "'".ValidaFecha($fe_nacimiento)."'";
  else
    $fe_nacimiento = "NULL";
  if(!empty($fe_carta))
    $fe_carta = "'".ValidaFecha($fe_carta)."'";
  else
    $fe_carta = "NULL";
  if(!empty($fe_contrato))
    $fe_contrato = "'".ValidaFecha($fe_contrato)."'";
  else
    $fe_contrato = "NULL";
  if(!empty($fe_fin))
    $fe_fin = "'".ValidaFecha($fe_fin)."'";
  else
    $fe_fin = "NULL";
  if(!empty($fe_completado))
    $fe_completado = "'".ValidaFecha($fe_completado)."'";
  else
    $fe_completado = "NULL";
  if(!empty($fe_emision))
    $fe_emision = "'".ValidaFecha($fe_emision)."'";
  else
    $fe_emision = "NULL";
  if(!empty($fe_graduacion))
    $fe_graduacion = "'".ValidaFecha($fe_graduacion)."'";
  else
    $fe_graduacion = "NULL";

  $foto_size = ObtenConfiguracion(80);
  if(!empty($_FILES['ds_foto_oficial']['tmp_name'][0])) {
    $ruta = SP_HOME."/modules/students/images/id";
    $Query  = "SELECT ds_ruta_foto, cl_sesion ";
    $Query .= "FROM k_ses_app_frm_1 ";
    $Query .= "WHERE cl_sesion=(SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$clave)";
    $row = RecuperaValor($Query);
    if(!empty($row[0])){
      if(file_exists ($ruta."/".$row[0])){
        chmod ($ruta."/".$row[0], 0755);
        unlink($ruta."/".$row[0]);
      }
    }
    $cl_sesion = $row[1];
    $ext = strtolower(ObtenExtensionArchivo($_FILES['ds_foto_oficial']['name'][0]));
    
    $row1 = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion='$cl_sesion'");
    $fl_sesion = $row1[0];
    $ds_foto_oficial = $ds_nombres."_";
    if(!empty($ds_amaterno))
      $ds_foto_oficial .= $ds_amaterno."_";
    $ds_foto_oficial .= $ds_apaterno."_ID_".$fl_sesion.".".$ext;
    $ds_foto_oficial = NombreArchivoDecente($ds_foto_oficial);
    move_uploaded_file($_FILES['ds_foto_oficial']['tmp_name'][0], $ruta."/".$ds_foto_oficial);    
    if($ext == "jpg" OR $ext == "jpeg")
      CreaThumb($ruta."/".$ds_foto_oficial, $ruta."/".$ds_foto_oficial, 0, 0, $foto_size);
    chmod ($ruta."/".$ds_foto_oficial, 0755);
  }
  
  # Verifica si se esta insertando
  if(!empty($clave)) {
    
    # Actualiza los datos del usuario
    $Query  = "UPDATE c_usuario SET fl_perfil=$fl_perfil, fg_activo='$fg_activo', ds_nombres='$ds_nombres', ds_apaterno='$ds_apaterno', ";
    $Query .= "ds_amaterno='$ds_amaterno', fg_genero='$fg_genero', fe_nacimiento=$fe_nacimiento, ds_email='$ds_email' ";
    $Query .= "WHERE fl_usuario=$clave";
    EjecutaQuery($Query);
    
    
    # Actualiza el registro del alumno
    $Query  = "UPDATE c_alumno ";
    $Query .= "SET ds_notas='$ds_notas' ";
    if(!empty($fg_activo) OR empty($fg_activo))
      $Query .= ", no_promedio_t = ".$no_promedio_t." ";
    $Query .= "WHERE fl_alumno='$clave'";
    EjecutaQuery($Query);
    
    # Actualiza el periodo del alumno
    $Query  = "UPDATE k_ses_app_frm_1 ";
    $Query .= "SET fl_periodo=$fl_periodo, ds_number='$ds_number', ds_alt_number='$ds_alt_number', ds_add_number='$ds_add_number', ds_add_street='$ds_add_street', ";
    $Query .= "ds_add_city='$ds_add_city', ds_add_state='$ds_add_state', ds_add_zip='$ds_add_zip', ds_add_country='$ds_add_country', ds_link_to_portfolio='$ds_link_to_portfolio', ds_ruta_foto='$ds_foto_oficial' ";
    $Query .= "WHERE cl_sesion='$cl_sesion'";
    EjecutaQuery($Query);
    
    #Actualizamos el fg_international
    $Query  = "UPDATE k_app_contrato SET fg_international='$fg_international', ds_m_add_number='".$ds_m_add_number."', ds_m_add_street='".$ds_m_add_street."', ";
    $Query .= " ds_m_add_city='".$ds_m_add_city."', ds_m_add_state='".$ds_m_add_state."', ds_m_add_zip='".$ds_m_add_zip."', ds_m_add_country='".$ds_m_add_country."', ";
    $Query .= "ds_a_email='".$ds_a_email."' ";
    $Query .= "WHERE cl_sesion='$cl_sesion'";
    EjecutaQuery($Query);
    
    # Inserta o actualiza datos de Official Transcript
    if($inserta == 1) {
      $Query  = "INSERT INTO k_pctia (fl_alumno, fl_programa, fe_carta, fe_contrato, fe_fin, fe_completado, fe_emision, fe_graduacion, fg_certificado, fg_honores, fg_desercion, fg_dismissed, fg_job, fg_graduacion) ";
      $Query .= "VALUES ($clave, $fl_programa, $fe_carta, $fe_contrato, $fe_fin, $fe_completado, $fe_emision, $fe_graduacion, '$fg_certificado', '$fg_honores', '$fg_desercion', '$fg_dismissed', '$fg_job', '$fg_graduacion')";
    }
    else {
      $Query  = "UPDATE k_pctia ";
      $Query .= "SET fe_carta=$fe_carta, fe_contrato=$fe_contrato, fe_fin=$fe_fin, fe_completado=$fe_completado, ";
      $Query .= "fe_emision=$fe_emision, fe_graduacion=$fe_graduacion, fg_certificado='$fg_certificado', fg_honores='$fg_honores', fg_desercion='$fg_desercion', fg_dismissed='$fg_dismissed', fg_job='$fg_job', fg_graduacion='$fg_graduacion' ";
      $Query .= "WHERE fl_alumno=$clave ";
      $Query .= "AND fl_programa=$fl_programa";
    }
    EjecutaQuery($Query);
  }

  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>