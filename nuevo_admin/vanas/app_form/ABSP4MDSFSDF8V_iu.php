<?php
  
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  require("../lib/sp_forms.inc.php");
  
  # Recupera sesion del cookie
  $cl_sesion = SP_RecuperaSesion( );
  
  # Recibe parametro con la clave de sesion
  $clave = RecibeParametroHTML('clave');
  
  # Si no es una sesion valida redirige a la forma inicial
  if(empty($cl_sesion) OR $cl_sesion <> $clave) {
    header("Location: ".ObtenProgramaNombre(PGM_FORM));
    exit;
  }
  
  
  define('ERR_PREFERENCEDIF',21);
  # Recibe parametros
  $fg_error = 0;
	$fl_programa = RecibeParametroNumerico('fl_programa');
  $fl_periodo = RecibeParametroNumerico('fl_periodo');
  $ds_fname = RecibeParametroHTML('ds_fname', True);
  $ds_mname = RecibeParametroHTML('ds_mname', True);
  $ds_lname = RecibeParametroHTML('ds_lname', True);
  $ds_number = RecibeParametroHTML('ds_number');
  $ds_alt_number = RecibeParametroHTML('ds_alt_number');
  $ds_email = RecibeParametroHTML('ds_email');
  $ds_email_conf = RecibeParametroHTML('ds_email_conf');
  $fg_gender = RecibeParametroHTML('fg_gender');
  $fe_birth = RecibeParametroFecha('fe_birth');
  $ds_add_number = RecibeParametroHTML('ds_add_number');
  $ds_add_street = RecibeParametroHTML('ds_add_street');
  $ds_add_city = RecibeParametroHTML('ds_add_city');
  $ds_add_state = RecibeParametroHTML('ds_add_state');
  $ds_add_zip = RecibeParametroHTML('ds_add_zip');
  $ds_add_country = RecibeParametroHTML('ds_add_country');
  $ds_eme_fname = RecibeParametroHTML('ds_eme_fname');
  $ds_eme_lname = RecibeParametroHTML('ds_eme_lname');
  $ds_eme_number = RecibeParametroHTML('ds_eme_number');
  $ds_eme_relation = RecibeParametroHTML('ds_eme_relation');
  $ds_eme_country = RecibeParametroHTML('ds_eme_country');
  $fg_ori_via = RecibeParametroHTML('fg_ori_via');
  $ds_ori_other = RecibeParametroHTML('ds_ori_other');
  $fg_ori_ref = RecibeParametroHTML('fg_ori_ref');
  $ds_ori_ref_name = RecibeParametroHTML('ds_ori_ref_name');
  $ds_p_name = RecibeParametroHTML('ds_p_name');
  $ds_education_number = RecibeParametroHTML('ds_education_number');
  $fg_international = RecibeParametroHTML('fg_international');
  $cl_preference_1 = RecibeParametroNumerico('cl_preference_1');
  $cl_preference_2 = RecibeParametroNumerico('cl_preference_2');
  $cl_preference_3 = RecibeParametroNumerico('cl_preference_3');
  $ds_m_add_number = RecibeParametroHTML('ds_m_add_number');
  $ds_m_add_street = RecibeParametroHTML('ds_m_add_street');
  $ds_m_add_city = RecibeParametroHTML('ds_m_add_city');
  $ds_m_add_state = RecibeParametroHTML('ds_m_add_state');
  $ds_m_add_zip = RecibeParametroHTML('ds_m_add_zip');
  $ds_m_add_country = RecibeParametroHTML('ds_m_add_country');
  $ds_a_email = RecibeParametroHTML('ds_a_email');
  $ds_link_to_portfolio = RecibeParametroHTML('ds_link_to_portfolio');
  $fg_provincia = RecibeParametroNumerico('fg_provincia');
  $fl_provincia = RecibeParametroNumerico('fl_provincia');
  $ds_ruta_foto = RecibeParametroHTML('ds_ruta_foto');
  $fg_responsable = RecibeParametroBinario('fg_responsable');
  $ds_fname_r = RecibeParametroHTML('ds_fname_r');
  $ds_lname_r = RecibeParametroHTML('ds_lname_r');
  $ds_email_r = RecibeParametroHTML('ds_email_r');
  $ds_aemail_r = RecibeParametroHTML('ds_aemail_r');
  $ds_pnumber_r = RecibeParametroHTML('ds_pnumber_r');
  $ds_relation_r = RecibeParametroHTML('ds_relation_r');
  $cl_recruiter = RecibeParametroNumerico('cl_recruiter');
  
  
  # Valida campos obligatorios
  if(empty($fl_programa))
    $fl_programa_err = ERR_REQUERIDO;
  if(empty($fl_periodo))
    $fl_periodo_err = ERR_REQUERIDO;
  if(empty($ds_fname))
    $ds_fname_err = ERR_REQUERIDO;
  if(empty($ds_lname))
    $ds_lname_err = ERR_REQUERIDO;
  if(empty($ds_number))
    $ds_number_err = ERR_REQUERIDO;
  if(empty($ds_alt_number))
    $ds_alt_number_err = ERR_REQUERIDO;
  if(empty($ds_email))
    $ds_email_err = ERR_REQUERIDO;
  if(empty($ds_email_conf))
    $ds_email_conf_err = ERR_REQUERIDO;
  if(empty($fg_gender))
    $fg_gender_err = ERR_REQUERIDO;
  if(empty($fe_birth))
    $fe_birth_err = ERR_REQUERIDO;
  if(empty($ds_add_number))
    $ds_add_number_err = ERR_REQUERIDO;
  if(empty($ds_add_street))
    $ds_add_street_err = ERR_REQUERIDO;
  if(empty($ds_add_city))
    $ds_add_city_err = ERR_REQUERIDO;
  if(empty($ds_add_state) AND $fg_provincia!=38)
    $ds_add_state_err = ERR_REQUERIDO;
  if(empty($ds_add_zip))
    $ds_add_zip_err = ERR_REQUERIDO;
  if(empty($ds_add_country))
    $ds_add_country_err = ERR_REQUERIDO;
  if(empty($ds_eme_fname))
    $ds_eme_fname_err = ERR_REQUERIDO;
  if(empty($ds_eme_lname))
    $ds_eme_lname_err = ERR_REQUERIDO;
  if(empty($ds_eme_number))
    $ds_eme_number_err = ERR_REQUERIDO;
  if(empty($ds_eme_relation))
    $ds_eme_relation_err = ERR_REQUERIDO;
  if(empty($ds_eme_country))
    $ds_eme_country_err = ERR_REQUERIDO;
  if($fg_ori_via == "")
    $fg_ori_via_err = ERR_REQUERIDO;
  if($fg_ori_via == "0" AND empty($ds_ori_other))
    $ds_ori_other_err = ERR_REQUERIDO;
  if($fg_ori_ref == "")
    $fg_ori_ref_err = ERR_REQUERIDO;
  if($fg_ori_ref <> "0" AND empty($ds_ori_ref_name))
    $ds_ori_ref_name_err = ERR_REQUERIDO;
  if($fg_international=='')
    $fg_international_err = ERR_REQUERIDO;
  if(empty($cl_preference_1))
    $cl_preference_1_err = ERR_REQUERIDO;
  if(empty($cl_preference_2))
    $cl_preference_2_err = ERR_REQUERIDO;
  if(empty($cl_preference_3))
    $cl_preference_3_err = ERR_REQUERIDO;
  if(empty($fl_provincia) AND $fg_provincia==38)
    $fl_provincia_err = ERR_REQUERIDO;
  
  # Si se repiten las preferencias
  if(!empty($cl_preference_1) AND ($cl_preference_1==$cl_preference_2 OR $cl_preference_1==$cl_preference_3))
    $cl_preference_1_err = ERR_PREFERENCEDIF;
  if(!empty($cl_preference_2) AND ($cl_preference_2==$cl_preference_1 OR $cl_preference_2==$cl_preference_3))
    $cl_preference_2_err = ERR_PREFERENCEDIF;
  if(!empty($cl_preference_3) AND ($cl_preference_3==$cl_preference_1 OR $cl_preference_3==$cl_preference_2))
    $cl_preference_3_err = ERR_PREFERENCEDIF;

  # Verifica que el formato de la fecha sea valido
  if(empty($fe_birth_err) AND !ValidaFecha($fe_birth))
    $fe_birth_err = ERR_FORMATO_FECHA;
  
  # Verifica que el formato del email sea valido
  if(empty($ds_email_err) AND !ValidaEmail($ds_email))
    $ds_email_err = ERR_FORMATO_EMAIL;
  
  # Valida confirmacion del email
  if((empty($ds_email_err) AND empty($ds_email_conf_err)) AND ($ds_email <> $ds_email_conf))
    $ds_email_conf_err = 210; // The email confirmation is different from the email provided.
  
  # Verifica que el tipo de archivo para avatar sea JPG
  $ext = strtolower(ObtenExtensionArchivo($_FILES['foto']['name'][0]));
  if(!empty($ext) AND $ext!='jpg')
    $ds_ruta_foto_err = ERR_ARCHIVO_JPEG;
  
  # Requerido el campo para subir imagen
  if(empty($_FILES['foto']['name'][0]))
    $ds_ruta_foto_err = ERR_REQUERIDO;
  
  # Valida Campos de la persona responsable
  if(!empty($fg_responsable)){
    if(empty($ds_fname_r))
      $ds_fname_r_err = ERR_REQUERIDO;    
    if(empty($ds_lname_r))
      $ds_lname_r_err = ERR_REQUERIDO;    
    if(empty($ds_email_r))
      $ds_email_r_err = ERR_REQUERIDO;    
    if(empty($ds_pnumber_r))
      $ds_pnumber_r_err = ERR_REQUERIDO;
    if(empty($ds_relation_r))
      $ds_relation_r_err = ERR_REQUERIDO;    
    # Verifica que el formato del email sea valido
    if(empty($ds_email_r_err) AND !ValidaEmail($ds_email_r))
      $ds_email_r_err = ERR_FORMATO_EMAIL;
    # Verifica que el formato del email sea valido
    if(!empty($ds_aemail_r) AND !ValidaEmail($ds_aemail_r))
      $ds_aemail_r_err = ERR_FORMATO_EMAIL;
  }
  if(empty($cl_recruiter))
      $cl_recruiter_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $fl_programa_err || $fl_periodo_err || $ds_fname_err || $ds_lname_err;
  $fg_error = $fg_error || $ds_number_err || $ds_alt_number_err || $ds_email_err || $ds_email_conf_err || $fg_gender_err || $fe_birth_err;
  $fg_error = $fg_error || $ds_add_number_err || $ds_add_street_err || $ds_add_city_err || $ds_add_state_err;
  $fg_error = $fg_error || $ds_add_zip_err || $ds_add_country_err;
  $fg_error = $fg_error || $ds_eme_fname_err || $ds_eme_lname_err || $ds_eme_number_err || $ds_eme_relation_err || $ds_eme_country_err;
  $fg_error = $fg_error || $fg_ori_via_err || $ds_ori_other_err || $fg_ori_ref_err || $ds_ori_ref_name_err;
  $fg_error = $fg_error || $fg_international_err || $cl_preference_1_err || $cl_preference_2_err || $cl_preference_3_err;
  $fg_error = $fg_error || $fl_provincia_err || $ds_ruta_foto_err;
  $fg_error = $fg_error || $ds_fname_r_err || $ds_lname_r_err || $ds_email_r_err || $ds_aemail_r_err || $ds_pnumber_r_err || $ds_relation_r_err || $cl_recruiter_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('fl_programa' , $fl_programa);
    Forma_CampoOculto('fl_programa_err' , $fl_programa_err);
    Forma_CampoOculto('fl_periodo' , $fl_periodo);
    Forma_CampoOculto('fl_periodo_err' , $fl_periodo_err);
    Forma_CampoOculto('ds_fname' , $ds_fname);
    Forma_CampoOculto('ds_fname_err' , $ds_fname_err);
    Forma_CampoOculto('ds_mname' , $ds_mname);
    Forma_CampoOculto('ds_lname' , $ds_lname);
    Forma_CampoOculto('ds_lname_err' , $ds_lname_err);
    Forma_CampoOculto('ds_number' , $ds_number);
    Forma_CampoOculto('ds_number_err' , $ds_number_err);
    Forma_CampoOculto('ds_alt_number' , $ds_alt_number);
    Forma_CampoOculto('ds_alt_number_err' , $ds_alt_number_err);
    Forma_CampoOculto('ds_email' , $ds_email);
    Forma_CampoOculto('ds_email_err' , $ds_email_err);
    Forma_CampoOculto('ds_email_conf' , $ds_email_conf);
    Forma_CampoOculto('ds_email_conf_err' , $ds_email_conf_err);
    Forma_CampoOculto('fg_gender' , $fg_gender);
    Forma_CampoOculto('fg_gender_err' , $fg_gender_err);
    Forma_CampoOculto('fe_birth' , $fe_birth);
    Forma_CampoOculto('fe_birth_err' , $fe_birth_err);
    Forma_CampoOculto('ds_add_number' , $ds_add_number);
    Forma_CampoOculto('ds_add_number_err' , $ds_add_number_err);
    Forma_CampoOculto('ds_add_street' , $ds_add_street);
    Forma_CampoOculto('ds_add_street_err' , $ds_add_street_err);
    Forma_CampoOculto('ds_add_city' , $ds_add_city);
    Forma_CampoOculto('ds_add_city_err' , $ds_add_city_err);
    Forma_CampoOculto('ds_add_state' , $ds_add_state);
    Forma_CampoOculto('ds_add_state_err' , $ds_add_state_err);
    Forma_CampoOculto('ds_add_zip' , $ds_add_zip);
    Forma_CampoOculto('ds_add_zip_err' , $ds_add_zip_err);
    Forma_CampoOculto('ds_add_country' , $ds_add_country);
    Forma_CampoOculto('ds_add_country_err' , $ds_add_country_err);
    Forma_CampoOculto('ds_eme_fname' , $ds_eme_fname);
    Forma_CampoOculto('ds_eme_fname_err' , $ds_eme_fname_err);
    Forma_CampoOculto('ds_eme_lname' , $ds_eme_lname);
    Forma_CampoOculto('ds_eme_lname_err' , $ds_eme_lname_err);
    Forma_CampoOculto('ds_eme_number' , $ds_eme_number);
    Forma_CampoOculto('ds_eme_number_err' , $ds_eme_number_err);
    Forma_CampoOculto('ds_eme_relation' , $ds_eme_relation);
    Forma_CampoOculto('ds_eme_relation_err' , $ds_eme_relation_err);
    Forma_CampoOculto('ds_eme_country' , $ds_eme_country);
    Forma_CampoOculto('ds_eme_country_err' , $ds_eme_country_err);
    Forma_CampoOculto('fg_ori_via' , $fg_ori_via);
    Forma_CampoOculto('fg_ori_via_err' , $fg_ori_via_err);
    Forma_CampoOculto('ds_ori_other' , $ds_ori_other);
    Forma_CampoOculto('ds_ori_other_err' , $ds_ori_other_err);
    Forma_CampoOculto('fg_ori_ref' , $fg_ori_ref);
    Forma_CampoOculto('fg_ori_ref_err' , $fg_ori_ref_err);
    Forma_CampoOculto('ds_ori_ref_name' , $ds_ori_ref_name);
    Forma_CampoOculto('ds_ori_ref_name_err' , $ds_ori_ref_name_err);
    Forma_CampoOculto('ds_p_name', $ds_p_name);
    Forma_CampoOculto('ds_education_number', $ds_education_number);
    Forma_CampoOculto('fg_international', $fg_international);
    Forma_CampoOculto('fg_international_err', $fg_international_err);
    Forma_CampoOculto('cl_preference_1', $cl_preference_1);
    Forma_CampoOculto('cl_preference_1_err', $cl_preference_1_err);
    Forma_CampoOculto('cl_preference_2', $cl_preference_2);
    Forma_CampoOculto('cl_preference_2_err', $cl_preference_2_err);
    Forma_CampoOculto('cl_preference_3', $cl_preference_3);
    Forma_CampoOculto('cl_preference_3_err', $cl_preference_3_err);
    Forma_CampoOculto('ds_m_add_number', $ds_m_add_number);
    Forma_CampoOculto('ds_m_add_street', $ds_m_add_street);
    Forma_CampoOculto('ds_m_add_city', $ds_m_add_city);
    Forma_CampoOculto('ds_m_add_state', $ds_m_add_state);
    Forma_CampoOculto('ds_m_add_zip', $ds_m_add_zip);
    Forma_CampoOculto('ds_m_add_country', $ds_m_add_country);
    Forma_CampoOculto('ds_a_email', $ds_a_email);
    Forma_CampoOculto('ds_link_to_portfolio', $ds_link_to_portfolio);
    Forma_CampoOculto('fg_provincia', $fg_provincia);
    Forma_CampoOculto('fl_provincia', $fl_provincia); 
    Forma_CampoOculto('ds_ruta_foto', $ds_ruta_foto); 
    Forma_CampoOculto('ds_ruta_foto_err', $ds_ruta_foto_err); 
    Forma_CampoOculto('fg_responsable',$fg_responsable);
    Forma_CampoOculto('ds_fname_r', $ds_fname_r);
    Forma_CampoOculto('ds_fname_r_err', $ds_fname_r_err);    
    Forma_CampoOculto('ds_lname_r', $ds_lname_r);
    Forma_CampoOculto('ds_lname_r_err', $ds_lname_r_err);
    Forma_CampoOculto('ds_email_r', $ds_email_r);
    Forma_CampoOculto('ds_email_r_err', $ds_email_r_err);
    Forma_CampoOculto('ds_aemail_r', $ds_aemail_r);
    Forma_CampoOculto('ds_aemail_r_err', $ds_aemail_r_err);
    Forma_CampoOculto('ds_pnumber_r', $ds_pnumber_r);
    Forma_CampoOculto('ds_pnumber_r_err', $ds_pnumber_r_err);    
    Forma_CampoOculto('ds_relation_r', $ds_relation_r);
    Forma_CampoOculto('ds_relation_r_err', $ds_relation_r_err);    
    Forma_CampoOculto('cl_recruiter', $cl_recruiter);    
    Forma_CampoOculto('cl_recruiter_err', $cl_recruiter_err);    
    
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  
  # Prepara fechas en formato para insertar
  if(!empty($fe_birth))
    $fe_birth = "'".ValidaFecha($fe_birth)."'";
  else
    $fe_birth = "NULL";
  
  # Verifica si es una sesion nueva
  $row = RecuperaValor("SELECT COUNT(1) FROM c_sesion WHERE cl_sesion='$clave'");
  if(empty($row[0]))
    $fg_nueva = True;
  else
    $fg_nueva = False;
  
  # el fl_provincia es igual que el ds_add_state
  if($fg_provincia==38)
    $ds_add_state=$fl_provincia; 
  
  # Inserta o actualiza los datos de la forma para la sesion
  if($fg_nueva) {
    $Query  = "INSERT INTO c_sesion ";
    $Query .= "(cl_sesion, fg_app_1, fe_ultmod) ";
    $Query .= "VALUES ('$cl_sesion', '1', CURRENT_TIMESTAMP)";
    $fl_sesion = EjecutaInsert($Query);
  }
  else {
    $Query  = "UPDATE c_sesion SET fg_app_1='1', fe_ultmod=CURRENT_TIMESTAMP ";
    $Query .= "WHERE cl_sesion='$clave'";
    EjecutaQuery($Query);
  }
  
  # Verifica si se esta insertando
  $row = RecuperaValor("SELECT COUNT(1) FROM k_ses_app_frm_1 WHERE cl_sesion='$clave'");
  if(empty($row[0]))
    $fg_nueva = True;
  else
    $fg_nueva = False;
  
  # Recibe el archivo seleccionado
  $foto_size = ObtenConfiguracion(80);
  if(!empty($_FILES['foto']['tmp_name'][0])) {
    $ruta = PATH_ALU_IMAGES_F."/id";
    $Query  = "SELECT ds_ruta_foto ";
    $Query .= "FROM k_ses_app_frm_1 ";
    $Query .= "WHERE cl_sesion='$clave'";
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      unlink($ruta."/".$row[0]);
    $ext = strtolower(ObtenExtensionArchivo($_FILES['foto']['name'][0]));
    if(!$fg_nueva){
     $row = RecuperaValor("SELECT fl_sesion FROM c_sesion  WHERE cl_sesion='$clave'");
     $fl_sesion = $row[0];
    }      
    $ds_ruta_foto = $ds_fname."_";
    if(!empty($ds_mname))
      $ds_ruta_foto .= $ds_mname."_";
    $ds_ruta_foto .= $ds_lname."_ID_".$fl_sesion.".".$ext;
    //echo "ds_fname es $ds_fname ds_mname es $ds_mname ds_lname es $ds_lname fl_sesion es $fl_sesion ext es $ext ds_ruta_foto es $ds_ruta_foto";
    $ds_ruta_foto = NombreArchivoDecente($ds_ruta_foto);
    //echo "<br> ds_ruta_foto es $ds_ruta_foto"; exit;
    move_uploaded_file($_FILES['foto']['tmp_name'][0], $ruta."/".$ds_ruta_foto);
    if($ext == "jpg")
      CreaThumb($ruta."/".$ds_ruta_foto, $ruta."/".$ds_ruta_foto, 0, 0, $foto_size);
  }
  
  # Inserta o actualiza los datos de la forma para la sesion
  if($fg_nueva) {
    $Query  = "INSERT INTO k_ses_app_frm_1 ";
    $Query .= "(cl_sesion, fl_programa, fl_periodo, ";
    $Query .= "ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email,ds_link_to_portfolio, fg_gender, fe_birth, ";
    $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, ds_add_country, ";
    $Query .= "ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, ds_eme_country, ";
    $Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, ds_ruta_foto, fe_ultmod, fg_responsable, cl_recruiter) ";
    $Query .= "VALUES ('$cl_sesion', $fl_programa, $fl_periodo, ";
    $Query .= "'$ds_fname', '$ds_mname', '$ds_lname', '$ds_number', '$ds_alt_number', '$ds_email','$ds_link_to_portfolio', '$fg_gender', $fe_birth, ";
    $Query .= "'$ds_add_number', '$ds_add_street', '$ds_add_city', '$ds_add_state', '$ds_add_zip', '$ds_add_country', ";
    $Query .= "'$ds_eme_fname', '$ds_eme_lname', '$ds_eme_number', '$ds_eme_relation', '$ds_eme_country', ";
    $Query .= "'$fg_ori_via', '$ds_ori_other', '$fg_ori_ref', '$ds_ori_ref_name','$ds_ruta_foto', CURRENT_TIMESTAMP, '$fg_responsable', $cl_recruiter)";
    // Insertamos en una nueva tabla los datos de la persona responsable de los pagos 
    $Query_respon  = "INSERT INTO k_presponsable (cl_sesion,ds_fname_r,ds_lname_r,ds_email_r,ds_aemail_r,ds_pnumber_r, ds_relation_r) ";
    $Query_respon .= "VALUES ('$clave', '$ds_fname_r', '$ds_lname_r', '$ds_email_r', '$ds_aemail_r', '$ds_pnumber_r', '$ds_relation_r') ";
  }
  else {
    $Query  = "UPDATE k_ses_app_frm_1 SET fl_programa=$fl_programa, fl_periodo=$fl_periodo, ";
    $Query .= "ds_fname='$ds_fname', ds_mname='$ds_mname', ds_lname='$ds_lname', ds_number='$ds_number', ds_alt_number='$ds_alt_number', ";
    $Query .= "ds_email='$ds_email', fg_gender='$fg_gender', fe_birth=$fe_birth, ";
    $Query .= "ds_add_number='$ds_add_number', ds_add_street='$ds_add_street', ds_add_city='$ds_add_city', ";
    $Query .= "ds_add_state='$ds_add_state', ds_add_zip='$ds_add_zip', ds_add_country='$ds_add_country', ";
    $Query .= "ds_eme_fname='$ds_eme_fname', ds_eme_lname='$ds_eme_lname', ds_eme_number='$ds_eme_number', ";
    $Query .= "ds_eme_relation='$ds_eme_relation', ds_eme_country='$ds_eme_country', ";
    $Query .= "fg_ori_via='$fg_ori_via', ds_ori_other='$ds_ori_other', fg_ori_ref='$fg_ori_ref', ds_ori_ref_name='$ds_ori_ref_name', ";
    $Query .= "fe_ultmod=CURRENT_TIMESTAMP, ds_ruta_foto='$ds_ruta_foto', fg_responsable='$fg_responsable', cl_recruiter='$cl_recruiter' ";
    $Query .= "WHERE cl_sesion='$clave'";
    // Actualizamos los datos de la persona responsable de los pagos
    $Query_respon  = "UPDATE k_presponsable SET ds_fname_r = '$ds_fname_r' , ds_lname_r = '$ds_lname_r', ds_email_r = '$ds_email_r', ";
    $Query_respon .= "ds_aemail_r = '$ds_aemail', ds_pnumber_r = '$ds_pnumber_r' ,ds_relation_r = '$ds_relation_r' ";
    $Query_respon .= "WHERE cl_sesion = '$clave' ";
  }
  EjecutaQuery($Query);
  # Si el applicante selecciono otra persona guardara ese dato
  if(!empty($fg_responsable))
    EjecutaQuery($Query_respon);

  
  # Recupera infromacion del programa seleccionado
  $Query  = "SELECT mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, cl_type, no_semanas ";
  $Query .= "FROM k_programa_costos ";
  $Query .= "WHERE fl_programa=$fl_programa";
  $row = RecuperaValor($Query);
  $mn_app_fee = $row[0];
  if(empty($mn_app_fee))
    $mn_app_fee = 0.0;
  $mn_tuition = $row[1];
  if(empty($mn_tuition))
    $mn_tuition = 0.0;
  $mn_costs = $row[2];
  if(empty($mn_costs))
    $mn_costs = 0.0;
  $ds_costs = $row[3];
  $mn_a_due = $row[4];
  if(empty($mn_a_due))
    $mn_a_due = 0.0;
  $mn_a_paid = $row[5];
  if(empty($mn_a_paid))
    $mn_a_paid = 0.0;
  $mn_b_due = $row[6];
  if(empty($mn_b_due))
    $mn_b_due = 0.0;
  $mn_b_paid = $row[7];
  if(empty($mn_b_paid))
    $mn_b_paid = 0.0;
  $mn_c_due = $row[8];
  if(empty($mn_c_due))
    $mn_c_due = 0.0;
  $mn_c_paid = $row[9];
  if(empty($mn_c_paid))
    $mn_c_paid = 0.0;
  $mn_d_due = $row[10];
  if(empty($mn_d_due))
    $mn_d_due = 0.0;
  $mn_d_paid = $row[11];
  if(empty($mn_d_paid))
    $mn_d_paid = 0.0;
  $cl_type = $row[12];
  $no_semanas = $row[13];
  # Si el curso dura menos de 18 meses entonces se envia un solo contrato
  $no_contratos = 1;
  # contrato por años porque es mult anio como lo dice PCTIA
  if($cl_type == '4')
    $no_contratos = 3;
  # Si el curso dura mas de 18 meses y menos que 2 años ya va hacer dos contratos
  # comolo marca PCTIA
  if($no_semanas>78 AND $no_semanas<104)
    $no_contratos = 2;
  
  $mn_tot_tuition = $mn_tuition + $mn_costs;
  $mn_tot_program = $mn_tot_tuition + $mn_app_fee;
  
  # Inicializa los datos de la forma para la sesion
  EjecutaQuery("DELETE FROM k_app_contrato WHERE cl_sesion='$clave'");
  for($i = 1; $i <= $no_contratos; $i++) {
    $Query  = "INSERT INTO k_app_contrato ";
    $Query .= "(cl_sesion, no_contrato, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_tot_tuition, mn_tot_program, mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
    $Query .= "ds_p_name, ds_education_number, fg_international, cl_preference_1, cl_preference_2, cl_preference_3, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_m_add_country, ds_a_email) ";
    $Query .= "VALUES('$clave', $i, $mn_app_fee, $mn_tuition, $mn_costs, '$ds_costs', $mn_tot_tuition, $mn_tot_program, $mn_a_due, $mn_a_paid, $mn_b_due, $mn_b_paid, $mn_c_due, $mn_c_paid, $mn_d_due, $mn_d_paid, ";
    $Query .= "'$ds_p_name', '$ds_education_number', '$fg_international', $cl_preference_1, $cl_preference_2, $cl_preference_3, ";
    $Query .= "'$ds_m_add_number', '$ds_m_add_street', '$ds_m_add_city', '$ds_m_add_state', '$ds_m_add_zip', '$ds_m_add_country', '$ds_a_email')";
    EjecutaQuery($Query);
  }
  
  
  # Prepara variables de ambiente para envio de correo
  $app_frm_email = ObtenConfiguracion(20);
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", $app_frm_email);
  
  # Envia correo de confirmacion al Administrador
  $subject = ObtenEtiqueta(336);
  $message  = "Application form component 1 submitted\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(55)."\n";
  $rs = RecuperaValor("SELECT nb_programa FROM c_programa WHERE fl_programa = $fl_programa");
  $message .= ObtenEtiqueta(59).": $rs[0]\n";
  $message .= "\n";
  $rs = RecuperaValor("SELECT nb_periodo FROM c_periodo WHERE fl_periodo = $fl_periodo");
  $message .= ObtenEtiqueta(60).": $rs[0]\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(61)."\n";
  $message .= ObtenEtiqueta(117).": $ds_fname\n";
  $message .= ObtenEtiqueta(119).": $ds_mname\n";
  $message .= ObtenEtiqueta(118).": $ds_lname\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(631).": $ds_p_name\n";
  $message .= ObtenEtiqueta(632).": $ds_education_number\n";
  if($fg_international == '1')
    $ds_international = ObtenEtiqueta(17);
  else
    $ds_international = ObtenEtiqueta(16);
  $message .= ObtenEtiqueta(620).": $ds_international\n";
  $message .= "\n";
  
  $message .= ObtenEtiqueta(280).": $ds_number\n";
  $message .= ObtenEtiqueta(281).": $ds_alt_number\n";
  $message .= ObtenEtiqueta(121).": $ds_email\n";
  $message .= ObtenEtiqueta(127).": $ds_a_email\n";
  $message .= "\n";
  
  $message .= ObtenEtiqueta(114).": ";
  if($fg_gender == 'M')
    $message .= ObtenEtiqueta(115)."\n";
  else
    $message .= ObtenEtiqueta(116)."\n";
  $message .= ObtenEtiqueta(120).": $fe_birth\n";
  $message .= "\n";
  
  switch($cl_preference_1) {
    case 1: $ds_preference_1 = ObtenEtiqueta(624); break;
    case 2: $ds_preference_1 = ObtenEtiqueta(625); break;
    case 3: $ds_preference_1 = ObtenEtiqueta(626); break;
    case 4: $ds_preference_1 = ObtenEtiqueta(627); break;
    case 5: $ds_preference_1 = ObtenEtiqueta(628); break;
    case 6: $ds_preference_1 = ObtenEtiqueta(629); break;
    case 7: $ds_preference_1 = ObtenEtiqueta(630); break;
  }
  switch($cl_preference_2) {
    case 1: $ds_preference_2 = ObtenEtiqueta(624); break;
    case 2: $ds_preference_2 = ObtenEtiqueta(625); break;
    case 3: $ds_preference_2 = ObtenEtiqueta(626); break;
    case 4: $ds_preference_2 = ObtenEtiqueta(627); break;
    case 5: $ds_preference_2 = ObtenEtiqueta(628); break;
    case 6: $ds_preference_2 = ObtenEtiqueta(629); break;
    case 7: $ds_preference_2 = ObtenEtiqueta(630); break;
  }
  switch($cl_preference_3) {
    case 1: $ds_preference_3 = ObtenEtiqueta(624); break;
    case 2: $ds_preference_3 = ObtenEtiqueta(625); break;
    case 3: $ds_preference_3 = ObtenEtiqueta(626); break;
    case 4: $ds_preference_3 = ObtenEtiqueta(627); break;
    case 5: $ds_preference_3 = ObtenEtiqueta(628); break;
    case 6: $ds_preference_3 = ObtenEtiqueta(629); break;
    case 7: $ds_preference_3 = ObtenEtiqueta(630); break;
  }
  $message .= ObtenEtiqueta(621)."\n";
  $message .= ObtenEtiqueta(622).": $ds_preference_1\n";
  $message .= ObtenEtiqueta(623).": $ds_preference_2\n";
  $message .= ObtenEtiqueta(616).": $ds_preference_3\n";
  $message .= "\n";
  
  # Address
  $message .= ObtenEtiqueta(62)."\n";
  $message .= ObtenEtiqueta(282).": $ds_add_number\n";
  $message .= ObtenEtiqueta(283).": $ds_add_street\n";
  $message .= ObtenEtiqueta(284).": $ds_add_city\n";
  # si el pais es canada mostra la provincia que selecciono
  if($ds_add_country==38){
    $row = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$fl_provincia");
    $ds_add_state = $row[0];
  }
  $message .= ObtenEtiqueta(285).": $ds_add_state\n";
  $message .= ObtenEtiqueta(286).": $ds_add_zip\n";
  $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_add_country");
  $message .= ObtenEtiqueta(287).": $rs[0]\n";
  $message .= "\n";
  
  # Person Responsible
  $message .= ObtenEtiqueta(865).".\n";
  if(empty($fg_responsable))
    $message .= ObtenEtiqueta(866);
  else{
    $message .= ObtenEtiqueta(867);
    $message .= "\n ".ObtenEtiqueta(868).": ".$ds_fname_r."\n";
    $message .= " ".ObtenEtiqueta(869).": ".$ds_lname_r."\n";
    $message .= " ".ObtenEtiqueta(870).": ".$ds_email_r."\n";
    $message .= " ".ObtenEtiqueta(871).": ".$ds_aemail_r."\n";
    $message .= " ".ObtenEtiqueta(872).": ".$ds_pnumber_r."\n";
    $message .= " ".ObtenEtiqueta(873).": ".$ds_relation_r."\n";
  }
  $message .="\n";
  # Mailing Address (If different from above)
  $message .= ObtenEtiqueta(633)."\n";
  $message .= ObtenEtiqueta(282).": $ds_m_add_number\n";
  $message .= ObtenEtiqueta(283).": $ds_m_add_street\n";
  $message .= ObtenEtiqueta(284).": $ds_m_add_city\n";
  $message .= ObtenEtiqueta(285).": $ds_m_add_state\n";
  $message .= ObtenEtiqueta(286).": $ds_m_add_zip\n";
  $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_m_add_country");
  $message .= ObtenEtiqueta(287).": $rs[0]\n";
  $message .= "\n";
  
  # Emergency Contact Information
  $message .= ObtenEtiqueta(63)."\n";
  $message .= ObtenEtiqueta(117).": $ds_eme_fname\n";
  $message .= ObtenEtiqueta(118).": $ds_eme_lname\n";
  $message .= ObtenEtiqueta(280).": $ds_eme_number\n";
  $message .= ObtenEtiqueta(288).": $ds_eme_relation\n";
  $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_eme_country");
  $message .= ObtenEtiqueta(287).": $rs[0]\n";
  $message .= "\n";
  # Representative
  $message .= ObtenEtiqueta(876)."\n";
  $rowr = RecuperaValor("SELECT CONCAT(ds_nombres,' ', ds_apaterno) FROM c_usuario WHERE fl_usuario=$cl_recruiter");
  $message .= ObtenEtiqueta(877).": $rowr[0]\n";
  $message .= ObtenEtiqueta(289)." ";
  switch($fg_ori_via) {
    case 'A': $message .= ObtenEtiqueta(290)."\n"; break;
    case 'B': $message .= ObtenEtiqueta(291)."\n"; break;
    case 'C': $message .= ObtenEtiqueta(292)."\n"; break;
    case 'D': $message .= ObtenEtiqueta(293)."\n"; break;
    case '0': $message .= ObtenEtiqueta(294)." - $ds_ori_other\n"; break;
  }
  $message .= "\n";
  $message .= ObtenEtiqueta(295)." ";
  switch($fg_ori_ref) {
    case '0': $message .= ObtenEtiqueta(17)."\n"; break;
    case 'S': $message .= ObtenEtiqueta(296)." - $ds_ori_ref_name\n"; break;
    case 'T': $message .= ObtenEtiqueta(297)." - $ds_ori_ref_name\n"; break;
    case 'G': $message .= ObtenEtiqueta(298)." - $ds_ori_ref_name\n"; break;
    case 'A': $message .= ObtenEtiqueta(811)." - $ds_ori_ref_name\n"; break;
  }
  $message .= "\n\n";
  $message = utf8_encode(str_ascii($message));
  $headers = "From: $app_frm_email\r\nReply-To: $app_frm_email\r\n";
  $mail_sent = mail($app_frm_email, $subject, $message, $headers);
  
  # Redirige al listado
  header("Location: BDARC876XCS2FU9_frm.php");
  
?>