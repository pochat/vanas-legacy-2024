<?php

  # Libreria de funciones
	require '../../lib/general.inc.php';

	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  # Error de las preferencias
  define('ERR_PREFERENCEDIF',21);

	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');

  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_APP_FRM, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recibe parametros
  $fg_aplicar_international=RecibeParametroNumerico('fg_aplicar_international');
  $fg_enrollment=RecibeParametroBinario('fg_enrollment');
  $comments=RecibeParametroHTML('comments');
  $job_title=RecibeParametroHTML('job_title');
  $fg_inscrito = RecibeParametroBinario('fg_inscrito');
  $ds_costos_ad = RecibeParametroHTML('ds_costos_ad');
  $no_costos_ad = RecibeParametroHTML('no_costos_ad');
  $ds_descuento = RecibeParametroHTML('ds_descuento');
  $no_descuento = RecibeParametroHTML('no_descuento');
  $total_tuition = RecibeParametroHTML('total_tuition');
  $total = RecibeParametroHTML('total');
  $amount_due_a = RecibeParametroHTML('amount_due_a');
  $amount_paid_a = RecibeParametroHTML('amount_paid_a');
  $amount_due_b = RecibeParametroHTML('amount_due_b');
  $amount_paid_b = RecibeParametroHTML('amount_paid_b');
  $amount_due_c = RecibeParametroHTML('amount_due_c');
  $amount_paid_c = RecibeParametroHTML('amount_paid_c');
  $amount_due_d = RecibeParametroHTML('amount_due_d');
  $amount_paid_d = RecibeParametroHTML('amount_paid_d');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $fe_inicio = RecibeParametroFecha('fe_inicio');
  $no_contrato = RecibeParametroNumerico('no_contrato');
  $ds_sin=RecibeParametroHTML('ds_sin');
  $fl_immigrations_status=RecibeParametroNumerico('fl_immigrations_status');
  $ds_graduate_status=RecibeParametroNumerico('ds_graduate_status');

  $passport_number = RecibeParametroHTML('passport_number');
  $passport_exp_date = RecibeParametroFecha('passport_exp_date');
  $passport_exp_date = ValidaFecha($passport_exp_date);

  $fg_provider = RecibeParametroBinario('fg_provider');
  $provider = RecibeParametroHTML('provider'); 
  
  
  
$Query='SELECT cl_sesion FROM c_sesion WHERE fl_sesion='.$clave.'';
  $row=RecuperaValor($Query);
  $cl_sesion = $row[0];

  EjecutaQuery("UPDATE k_ses_app_frm_1 SET fl_immigrations_status=$fl_immigrations_status WHERE cl_sesion='$cl_sesion' ");

  $Query  = "UPDATE k_ses_app_frm_1 ";
  $Query .= "SET comments='$comments' ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  EjecutaQuery($Query);

EjecutaQuery("UPDATE k_ses_app_frm_1 SET passport_number='$passport_number' WHERE cl_sesion='$cl_sesion' ");
EjecutaQuery('UPDATE k_ses_app_frm_1 SET passport_exp_date="' . $passport_exp_date . '" WHERE cl_sesion="' . $cl_sesion . '" ');

EjecutaQuery('UPDATE k_ses_app_frm_1 SET fg_provider="' . $fg_provider . '",provider="' . $provider . '"  WHERE cl_sesion="' . $cl_sesion . '" ');

#address
  $fg_gender=RecibeParametroHTML('fg_gender');
  $ds_add_number = RecibeParametroHTML('ds_add_number', True);
  $ds_add_street = RecibeParametroHTML('ds_add_street', True);
  $ds_add_city = RecibeParametroHTML('ds_add_city', True);
  $ds_add_state = RecibeParametroHTML('ds_add_state', True);
  $ds_add_zip = RecibeParametroHTML('ds_add_zip', True);
  $ds_add_country = RecibeParametroHTML('ds_add_country');
  $ds_m_add_number = RecibeParametroHTML('ds_m_add_number', True);
  $ds_m_add_street = RecibeParametroHTML('ds_m_add_street', True);
  $ds_m_add_city = RecibeParametroHTML('ds_m_add_city', True);
  $ds_m_add_state = RecibeParametroHTML('ds_m_add_state', True);
  $ds_m_add_zip = RecibeParametroHTML('ds_m_add_zip', True);
  $ds_m_add_country = RecibeParametroHTML('ds_m_add_country');
  #Nombres
  $ds_fname = RecibeParametroHTML('ds_fname', True);
  $ds_mname = RecibeParametroHTML('ds_mname', True);
  $ds_lname = RecibeParametroHTML('ds_lname', True);
  $ds_number = RecibeParametroHTML('ds_number', True);
  $ds_email = RecibeParametroHTML('ds_email', True);
  $ds_a_email = RecibeParametroHTML('ds_a_email', True);
  $ds_link_to_portfolio = RecibeParametroHTML('ds_link_to_portfolio', True);
  $fg_international = RecibeParametroBinario('fg_international');
  $fe_birth = RecibeParametroFecha('fe_birth');
  $fg_archive = RecibeParametroBinario('fg_archive');
  #person Responsable
  $fg_responsable = RecibeParametroBinario('fg_responsable');
  $ds_fname_r = RecibeParametroHTML('ds_fname_r', True);
  $ds_lname_r = RecibeParametroHTML('ds_lname_r', True);
  $ds_email_r = RecibeParametroHTML('ds_email_r', True);
  $ds_aemail_r = RecibeParametroHTML('ds_aemail_r', True);
  $ds_pnumber_r= RecibeParametroHTML('ds_pnumber_r', True);
  $ds_relation_r = RecibeParametroHTML('ds_relation_r', True);
  $fg_email = RecibeParametroNumerico('fg_email');

  $cl_preference_1 = RecibeParametroNumerico('cl_preference_1');
  $cl_preference_2 = RecibeParametroNumerico('cl_preference_2');
  $cl_preference_3 = RecibeParametroNumerico('cl_preference_3');
  $fl_class_time= RecibeParametroNumerico('fl_class_time');

  $cl_recruiter = RecibeParametroNumerico('cl_recruiter');

  $ds_alt_number = RecibeParametroHTML("ds_alt_number");
  $ds_p_name = RecibeParametroHTML('ds_p_name');
  $ds_education_number = RecibeParametroHTML('ds_education_number');
  $ds_usual_name = RecibeParametroHTML('ds_usual_name');
  # new contratps
  $ds_citizenship = RecibeParametroHTML('ds_citizenship');
  if($fg_international==0){
    $ds_citizenship = "";
  }
  $fg_study_permit = RecibeParametroBinario('fg_study_permit');
  $fg_study_permit_other = RecibeParametroBinario('fg_study_permit_other');

  $fe_start_date=RecibeParametroFecha('fe_start_date');
  $fe_expirity_date=RecibeParametroFecha('fe_expirity_date');
  $nb_name_institutcion=RecibeParametroHTML('nb_name_institutcion');


  $fg_aboriginal = RecibeParametroBinario('fg_aboriginal');
  $ds_aboriginal = RecibeParametroHTML('ds_aboriginal');
  if($fg_aboriginal==0){
    $ds_aboriginal = "";
  }
  $fg_health_condition = RecibeParametroBinario('fg_health_condition');
  $ds_health_condition = RecibeParametroHTML('ds_health_condition');
  if($fg_health_condition==0){
    $ds_health_condition = "";
  }
  $fg_disabilityie = RecibeParametroBinario('fg_disabilityie');
  $ds_disability = RecibeParametroHTML('ds_disability');
  $fg_scholarship=RecibeParametroBinario('fg_scholarship');


  // echo "ds_citizenship:".$ds_citizenship."<br>fg_study_permit:".$fg_study_permit."<br>fg_study_permit_other:".$fg_study_permit_other.
        // "<br>fg_aboriginal:".$fg_aboriginal."<br>ds_aboriginal:".$ds_aboriginal."<br>fg_health_condition:".$fg_health_condition."<br>ds_health_condition:".$ds_health_condition;
  // exit;

  #Validaciones  de address
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
  if(empty($ds_fname))
    $ds_fname_err = ERR_REQUERIDO;
  if(empty($ds_lname))
    $ds_lname_err = ERR_REQUERIDO;
  if(empty($ds_number))
    $ds_number_err = ERR_REQUERIDO;
  if(empty($ds_email))
    $ds_email_err = ERR_REQUERIDO;
  if(empty($fe_birth))
    $fe_birth_err = ERR_REQUERIDO;
  if(!empty($fe_birth) AND !ValidaFecha($fe_birth))
    $fe_birth_err = ERR_FORMATO_FECHA;

  # Verifica que el tipo de archivo para avatar sea JPG
  $ext = strtolower(ObtenExtensionArchivo($_FILES['ds_ruta_foto']['name'][0]));
  if(!empty($ext) AND $ext!='jpg' AND $ext!='jpeg'){
    $ds_ruta_foto_err = ERR_ARCHIVO_JPEG;
  }
  # Validamos person responsible
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
  # Valida las prefencias
  if($fl_class_time){

		if(empty($fl_class_time))
        $fl_class_time_err = ERR_REQUERIDO;

  }else{
  /*if(empty($cl_preference_1))
    $cl_preference_1_err = ERR_REQUERIDO;
  if(empty($cl_preference_2))
    $cl_preference_2_err = ERR_REQUERIDO;
  if(empty($cl_preference_3))
    $cl_preference_2_err = ERR_REQUERIDO;
  */
  }



  if($fl_class_time){

  }else{

  # Si se repiten las preferencias
  if(!empty($cl_preference_1) AND ($cl_preference_1==$cl_preference_2 OR $cl_preference_1==$cl_preference_3))
    $cl_preference_1_err = ERR_PREFERENCEDIF;
  if(!empty($cl_preference_2) AND ($cl_preference_2==$cl_preference_1 OR $cl_preference_2==$cl_preference_3))
    $cl_preference_2_err = ERR_PREFERENCEDIF;
  if(!empty($cl_preference_3) AND ($cl_preference_3==$cl_preference_1 OR $cl_preference_3==$cl_preference_2))
    $cl_preference_3_err = ERR_PREFERENCEDIF;

  }


  $fg_error = $ds_add_number_err || $ds_add_street_err || $ds_add_city_err || $ds_add_state_err || $ds_add_zip_err || $ds_add_country_err
              || $ds_fname_err || $ds_lname_err || $ds_number_err || $ds_email_err || $fe_birth_err || $ds_ruta_foto_err;
  $fg_error = $fg_error || $ds_fname_r_err || $ds_lname_r_err || $ds_email_r_err || $ds_aemail_r_err || $ds_pnumber_r_err || $ds_relation_r_err;

  if($fl_class_time)
  $fg_error= $fg_error || $fl_class_time_err;
  else
  $fg_error = $fg_error || $cl_preference_1_err || $cl_preference_2_err || $cl_preference_3_err;


  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_add_number', $ds_add_number);
    Forma_CampoOculto('fg_enrollment', $fg_enrollment);
    Forma_CampoOculto('comments', $comments);
    Forma_CampoOculto('ds_email', $ds_email);
    Forma_CampoOculto('ds_email_err', $ds_email_err);
    Forma_CampoOculto('ds_a_email', $ds_a_email);
    Forma_CampoOculto('ds_add_street', $ds_add_street);
    Forma_CampoOculto('ds_add_city', $ds_add_city);
    Forma_CampoOculto('ds_add_state', $ds_add_state);
    Forma_CampoOculto('ds_add_zip', $ds_add_zip);
    Forma_CampoOculto('ds_add_country', $ds_add_country);
    Forma_CampoOculto('ds_add_number_err', $ds_add_number_err);
    Forma_CampoOculto('ds_add_street_err', $ds_add_street_err);
    Forma_CampoOculto('ds_add_city_err', $ds_add_city_err);
    Forma_CampoOculto('ds_add_state_err', $ds_add_state_err);
    Forma_CampoOculto('ds_add_zip_err', $ds_add_zip_err);
    Forma_CampoOculto('ds_add_country_err', $ds_add_country_err);
    //Mailing addrees
    Forma_CampoOculto('ds_m_add_number', $ds_m_add_number);
    Forma_CampoOculto('ds_m_add_street', $ds_m_add_street);
    Forma_CampoOculto('ds_m_add_city', $ds_m_add_city);
    Forma_CampoOculto('ds_m_add_state', $ds_m_add_state);
    Forma_CampoOculto('ds_m_add_zip', $ds_m_add_zip);
    Forma_CampoOculto('ds_m_add_country', $ds_m_add_country);
    #Nombres
    Forma_CampoOculto('ds_fname', $ds_fname);
    Forma_CampoOculto('ds_fname_err', $ds_fname_err);
    Forma_CampoOculto('ds_mname', $ds_mname);
    Forma_CampoOculto('ds_lname', $ds_lname);
    Forma_CampoOculto('ds_lname_err', $ds_lname_err);
    Forma_CampoOculto('ds_number', $ds_number);
    Forma_CampoOculto('ds_number_err', $ds_number_err);

    Forma_CampoOculto('ds_link_to_portfolio', $ds_link_to_portfolio);
    Forma_CampoOculto('fg_international', $fg_international);
    Forma_CampoOculto('fe_birth', $fe_birth);
    Forma_CampoOculto('fe_birth_err', $fe_birth_err);
    Forma_CampoOculto('ds_ruta_foto',$ds_ruta_foto);
    Forma_CampoOculto('ds_ruta_foto_err',$ds_ruta_foto_err);
    Forma_CampoOculto('fg_archive', $fg_archive);

    # Person Resposible
    Forma_CampoOculto('fg_responsable', $fg_responsable);
    Forma_CampoOculto('ds_fname_r', $ds_fname_r);
    Forma_CampoOculto('ds_fname_r_err', $ds_fname_r_err);
    Forma_CampoOculto('ds_lname_r', $ds_lname_r);
    Forma_CampoOculto('ds_lname_r_err', $ds_lname_r_err);
    Forma_CampoOculto('ds_email_r', $ds_email_r);
    Forma_CampoOculto('ds_email_r_err', $ds_email_r_err);
    Forma_CampoOculto('ds_aemail_r', $ds_aemail_r);
    Forma_CampoOculto('ds_pnumber_r', $ds_pnumber_r);
    Forma_CampoOculto('ds_pnumber_r_err', $ds_pnumber_r_err);
    Forma_CampoOculto('ds_relation_r', $ds_relation_r);
    Forma_CampoOculto('ds_relation_r_err', $ds_relation_r_err);
    Forma_CampoOculto('fg_email', $fg_email);
    # Preferencias
    Forma_CampoOculto('cl_preference_1', $cl_preference_1);
    Forma_CampoOculto('cl_preference_1_err', $cl_preference_1_err);
    Forma_CampoOculto('cl_preference_2', $cl_preference_2);
    Forma_CampoOculto('cl_preference_2_err', $cl_preference_2_err);
    Forma_CampoOculto('cl_preference_3', $cl_preference_3);
    Forma_CampoOculto('cl_preference_3_err', $cl_preference_3_err);
	Forma_CampoOculto('fl_class_time', $fl_class_time);
    Forma_CampoOculto('fl_class_time_err', $fl_class_time_err);
    Forma_CampoOculto('cl_recruiter', $cl_recruiter);
    Forma_CampoOculto('cl_recruiter_err', $cl_recruiter_err);

    Forma_CampoOculto('ds_alt_number', $ds_alt_number);
    Forma_CampoOculto('ds_alt_number_err', $ds_alt_number_err);
    Forma_CampoOculto('ds_p_name', $ds_p_name);
    Forma_CampoOculto('ds_p_name_err', $ds_p_name_err);
    Forma_CampoOculto('ds_education_number', $ds_education_number);
    Forma_CampoOculto('ds_education_number_err', $ds_education_number_err);
    Forma_CampoOculto('ds_usual_name', $ds_usual_name);
    Forma_CampoOculto('ds_usual_name_err', $ds_usual_name_err);

    Forma_CampoOculto('ds_citizenship', $ds_citizenship);
    Forma_CampoOculto('fg_study_permit', $fg_study_permit);
    Forma_CampoOculto('fg_study_permit_other', $fg_study_permit_other);
    Forma_CampoOculto('fg_aboriginal', $fg_aboriginal);
    Forma_CampoOculto('ds_aboriginal', $ds_aboriginal);
    Forma_CampoOculto('fg_health_condition', $fg_health_condition);
    Forma_CampoOculto('ds_health_condition', $ds_health_condition);

    Forma_CampoOculto('fe_start_date',$fe_start_date);
    Forma_CampoOculto('fe_expirity_date',$fe_expirity_date);
    Forma_CampoOculto('nb_name_institutcion',$nb_name_institutcion);
    Forma_CampoOculto('ds_aemail_r_err',$ds_aemail_r_err);

    Forma_CampoOculto('fg_disabilityie',$fg_disabilityie);
    Forma_CampoOculto('ds_disability',$ds_disability);
	
	Forma_CampoOculto('fg_provider',$fg_provider);
    Forma_CampoOculto('provider',$provider);

    echo "\n</form>
    <script>
      document.datos.submit();
    </script></body></html>";
    exit;
  }

  $foto_size = ObtenConfiguracion(80);
  if(!empty($_FILES['ds_ruta_foto']['tmp_name'][0])) {
    $ruta = SP_HOME."/modules/students/images/id";
    $Query  = "SELECT ds_ruta_foto ";
    $Query .= "FROM k_ses_app_frm_1 ";
    $Query .= "WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave)";
    $row = RecuperaValor($Query);
    if(!empty($row[0])){
      if(file_exists ($ruta."/".$row[0])){
        chmod ($ruta."/".$row[0], 0755);
        unlink($ruta."/".$row[0]);
      }
    }
    $ext = strtolower(ObtenExtensionArchivo($_FILES['ds_ruta_foto']['name'][0]));

    $ds_ruta_foto = $ds_fname."_";
    if(!empty($ds_mname))
      $ds_ruta_foto .= $ds_mname."_";
    $ds_ruta_foto .= $ds_lname."_ID_".$clave.".".$ext;
    //echo "ds_fname es $ds_fname ds_mname es $ds_mname ds_lname es $ds_lname fl_sesion es $fl_sesion ext es $ext ds_ruta_foto es $ds_ruta_foto";
    $ds_ruta_foto = NombreArchivoDecente($ds_ruta_foto);
    //echo "<br> ds_ruta_foto es $ds_ruta_foto"; exit;
    move_uploaded_file($_FILES['ds_ruta_foto']['tmp_name'][0], $ruta."/".$ds_ruta_foto);
    if($ext == "jpg" OR $ext == "jpeg")
      CreaThumb($ruta."/".$ds_ruta_foto, $ruta."/".$ds_ruta_foto, 0, 0, $foto_size);
    chmod ($ruta."/".$ds_ruta_foto, 0755);
  }

  if(!empty($_FILES['ds_ruta_foto_permiso']['tmp_name'][0])) {
      $ruta = SP_HOME."/modules/students/images/id";
      $Query  = "SELECT ds_ruta_foto_permiso ";
      $Query .= "FROM k_ses_app_frm_1 ";
      $Query .= "WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave)";
      $row = RecuperaValor($Query);
      if(!empty($row[0])){
          if(file_exists ($ruta."/".$row[0])){
              chmod ($ruta."/".$row[0], 0755);
              unlink($ruta."/".$row[0]);
          }
      }
      $ext = strtolower(ObtenExtensionArchivo($_FILES['ds_ruta_foto_permiso']['name'][0]));

      $ds_ruta_foto_permiso = $ds_fname."_";
      if(!empty($ds_mname))
          $ds_ruta_foto_permiso .= $ds_mname."_";
      $ds_ruta_foto_permiso .= $ds_lname."_ID_school_permiss".$clave.".".$ext;
      //echo "ds_fname es $ds_fname ds_mname es $ds_mname ds_lname es $ds_lname fl_sesion es $fl_sesion ext es $ext ds_ruta_foto es $ds_ruta_foto";
      $ds_ruta_foto_permiso = NombreArchivoDecente($ds_ruta_foto_permiso);
      //echo "<br> ds_ruta_foto es $ds_ruta_foto"; exit;
      move_uploaded_file($_FILES['ds_ruta_foto_permiso']['tmp_name'][0], $ruta."/".$ds_ruta_foto_permiso);
      if($ext == "jpg" OR $ext == "jpeg")
          CreaThumb($ruta."/".$ds_ruta_foto_permiso, $ruta."/".$ds_ruta_foto_permiso, 0, 0, $foto_size);
      chmod ($ruta."/".$ds_ruta_foto_permiso, 0755);
  }




  # Prepara fechas en formato para insertar
  if(!empty($fe_birth))
    $fe_birth = '"'.ValidaFecha($fe_birth).'"';
  else
    $fe_birth = 'NULL';


  if($fe_start_date<>'00-00-0000')
      $fe_start_date = '"'.ValidaFecha($fe_start_date).'"';
  else
      $fe_start_date = 'NULL';

  if($fe_expirity_date<>'00-00-0000')
      $fe_expirity_date = '"'.ValidaFecha($fe_expirity_date).'"';
  else
      $fe_expirity_date = 'NULL';

  $Query  = "UPDATE c_sesion ";
  $Query .= "SET fg_enrollment='$fg_enrollment' ";
  $Query .= "WHERE fl_sesion=$clave";
  EjecutaQuery($Query);




  # Actualiza o inserta el registro
  $Query  = "UPDATE c_sesion ";
  $Query .= "SET fg_inscrito='$fg_inscrito', fg_archive='$fg_archive',fg_scholarship='$fg_scholarship' ";
  $Query .= "WHERE fl_sesion=$clave";
  EjecutaQuery($Query);

  $Query="UPDATE k_ses_app_frm_1 SET job_title='$job_title' WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave) ";
  EjecutaQuery($Query);



  if(!empty($ds_graduate_status)){


      $Query="UPDATE k_ses_app_frm_1 SET ds_graduate_status=$ds_graduate_status WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave) ";
      EjecutaQuery($Query);


  }else{


      $Query="UPDATE k_ses_app_frm_1 SET ds_graduate_status=null WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave) ";
      EjecutaQuery($Query);


  }


  # Actualiza datos
  $Query = 'UPDATE k_ses_app_frm_1 SET ds_add_number ="'.$ds_add_number.'", ';
  $Query .=' nb_name_institutcion="'.$nb_name_institutcion.'", ';
  if((!empty($fe_expirity_date))&&($fe_expirity_date<>'""'))
  $Query .='fe_expirity_date='.$fe_expirity_date.', ';
  if((!empty($fe_start_date))&&($fe_start_date<>'""'))
  $Query .='fe_start_date='.$fe_start_date.' , ';
  $Query .= 'ds_add_street="'.$ds_add_street.'", ds_add_city="'.$ds_add_city.'", ds_add_state="'.$ds_add_state.'", ds_add_zip="'.$ds_add_zip.'", ds_add_country="'.$ds_add_country.'", ';
  $Query .= 'ds_fname ="'.$ds_fname.'", ds_mname="'.$ds_mname.'", ds_lname="'.$ds_lname.'", ds_number="'.$ds_number.'", ds_link_to_portfolio="'.$ds_link_to_portfolio.'", ';
  $Query .= 'fe_birth='.$fe_birth.', ds_email="'.$ds_email.'", fg_responsable="'.$fg_responsable.'", cl_recruiter="'.$cl_recruiter.'", ds_alt_number="'.$ds_alt_number.'" ';
  $Query .= ',fg_disability="'.$fg_disabilityie.'",ds_disability="'.$ds_disability.'" ';
  $Query .= 'WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion="'.$clave.'") ';
  EjecutaQuery($Query);

   if(!empty($ds_ruta_foto_permiso)){
      $Query = "UPDATE k_ses_app_frm_1 SET ds_ruta_foto_permiso='$ds_ruta_foto_permiso' WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave) ";
      EjecutaQuery($Query);
  }

  if(!empty($ds_ruta_foto)) {
    $Query = "UPDATE k_ses_app_frm_1 SET ds_ruta_foto='$ds_ruta_foto' WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave) ";
    EjecutaQuery($Query);
  }

  # Inserta el registro del estudiante
  $Query  = "SELECT a.cl_sesion, ds_fname, ds_mname, ds_lname, ds_email, fe_birth ";
  $Query .= "FROM c_sesion a, k_ses_app_frm_1 b ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion ";
  $Query .= "AND fl_sesion='$clave'";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  $ds_email = str_texto($row[4]);
  $fe_birth = $row[5];

  $Query='SELECT cl_sesion FROM c_sesion WHERE fl_sesion='.$clave.'';
  $row=RecuperaValor($Query);
  $cl_sesion = $row[0];

  EjecutaQuery("UPDATE k_ses_app_frm_1 SET ds_sin=$ds_sin,fg_gender='$fg_gender' WHERE cl_sesion='$cl_sesion' ");
  EjecutaQuery("UPDATE K_ses_app_frm_1 SET fl_immigrations_status=$fl_immigrations_status WHERE cl_sesion='$cl_sesion' ");

  $Query="SELECT fg_payment FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
  $rop=RecuperaValor($Query);
  $fg_payment=$rop['fg_payment'];



  # Insertamos o actualizamos person responsible
  $responsable = ExisteEnTabla('k_presponsable', 'cl_sesion', $cl_sesion);
  if(empty($responsable)){
    $Query_respon  = "INSERT INTO k_presponsable (cl_sesion,ds_fname_r,ds_lname_r,ds_email_r,ds_aemail_r,ds_pnumber_r, ds_relation_r, fg_email) ";
    $Query_respon .= "VALUES ('$cl_sesion', '$ds_fname_r', '$ds_lname_r', '$ds_email_r', '$ds_aemail_r', '$ds_pnumber_r', '$ds_relation_r', '0') ";
  }
  else{
    $Query_respon  = "UPDATE k_presponsable SET ds_fname_r = '$ds_fname_r' , ds_lname_r = '$ds_lname_r', ds_email_r = '$ds_email_r', ";
    $Query_respon .= "ds_aemail_r = '$ds_aemail', ds_pnumber_r = '$ds_pnumber_r' ,ds_relation_r = '$ds_relation_r' ";
    $Query_respon .= "WHERE cl_sesion = '$cl_sesion' ";
  }
  # Buscamos si fue enviado el correo a la persona responsable
  $rowr = RecuperaValor("SELECT fg_email, ds_email_r = '$ds_email_r' FROM k_presponsable WHERE cl_sesion='$cl_sesion'");
  $fg_email = $rowr[0];
  $com_emails = $rowr[1]; //se comparan los emails

  # Condiciones para enviar email
  # Si ya envio el correo no lo envia pero si camabia el correo lo envia
  # Si no lo ha enviado lo enviara
  #variables email
  $email_noreply = ObtenConfiguracion(4);
  $app_frm_email = ObtenConfiguracion(83);
  # Obtenemos el template que se le enviara a la person responsible
  //$message_resp = genera_documento($clave, 2, 38); 2025 no found
  $message_resp = "";
  if(!empty($fg_responsable)){
    if($fg_email==1){
      $snd_email = 0;
      if($com_emails==0)
        $snd_email = 1;
      else
        $snd_email = 0;
    }
    else{
      $snd_email = 1;
    }
    EjecutaQuery($Query_respon);
    if($snd_email==1){
      # Send email
      //$email_resp = EnviaMailHTML($email_noreply, $email_noreply, $ds_email_r, ObtenEtiqueta(865), $message_resp, $app_frm_email);
    }
    # Si se envio el email que actualice su registro
    if($email_resp=1)
      EjecutaQuery("UPDATE k_presponsable SET fg_email='1' WHERE cl_sesion='$cl_sesion'");
  }

  # Esta inscrito
  if($fg_inscrito == '1') {
    $Query  = "INSERT INTO c_usuario(ds_login, ds_password, cl_sesion, fg_activo, fe_alta, no_accesos, ";
    $Query .= "ds_nombres, ds_apaterno, ds_amaterno, fg_genero, fe_nacimiento, ds_email, fl_perfil,fg_nuevo) ";
    $Query .= "VALUES('ds_login', '1234567890', '$cl_sesion', '1', CURRENT_TIMESTAMP, 0, ";
    $Query .= "'$ds_fname', '$ds_lname', '$ds_mname', '$fg_gender', '$fe_birth', '$ds_email', ".PFL_ESTUDIANTE.",'1') ";
    $fl_usuario = EjecutaInsert($Query);
    $ds_login = substr(strtolower($ds_lname), 0, 1) . substr(strtolower($ds_fname), 0, 1);
    $ds_login = $ds_login . substr($fe_birth, 2, 2) . substr($fe_birth, 5, 2) . substr($fe_birth, 8, 2);
    $ds_login = $ds_login . str_pad($fl_usuario, 4, "0", STR_PAD_LEFT);
    $ds_password = $ds_login;

    ##Nueva implemntacion para que todos tengan un id desde la aplicacion.
    $Queryid="SELECT id_alumno FROM c_sesion WHERE cl_sesion='$cl_sesion' ";
    $rowid=RecuperaValor($Queryid);
    $ds_login=$rowid['id_alumno'];
    $ds_password = $ds_login;

    $Query  = "UPDATE c_usuario ";
    $Query .= "SET ds_login='$ds_login', ds_password='".sha256($ds_password)."' ";
    $Query .= "WHERE fl_usuario=$fl_usuario";
    EjecutaQuery($Query);
    $row = RecuperaValor("SELECT fl_zona_horaria FROM c_zona_horaria WHERE fg_default='1'");
    $fl_zona_horaria = $row[0];
    $Query  = "INSERT INTO c_alumno(fl_alumno, fl_zona_horaria) ";
    $Query .= "VALUES($fl_usuario, $fl_zona_horaria) ";
    EjecutaQuery($Query);

    # Recupera el numero de semanas
    $Query  = "SELECT no_semanas ";
    $Query .= "FROM k_programa_costos a ";
    $Query .= "WHERE a.fl_programa = $fl_programa";
    $row = RecuperaValor($Query);
    $no_semanas = $row[0];

    # Calculamos lo meses que dura el programa
    $meses = $no_semanas/4;

    # Calcula el end date de acuerdo a las semanas de curso y las coloca por default en el campo
    $fe_fin = date('d-m-Y', strtotime("$fe_inicio + $meses months"));
    $fe_fin = "'".ValidaFecha($fe_fin)."'";

    $Query  = "INSERT INTO k_pctia (fl_alumno, fl_programa, fe_fin, fe_completado) ";
    $Query .= "VALUES ($fl_usuario, $fl_programa, $fe_fin, $fe_fin)";
    EjecutaQuery($Query);

    # Inserta los datos del k_ses_pago a k_alumno_pago
    $Query  = "INSERT INTO k_alumno_pago(fl_alumno, fl_term_pago, cl_metodo_pago, fe_pago, mn_pagado, ds_comentario, ds_cheque, mn_late_fee, ds_transaccion, mn_tax_paypal, ds_tax_provincia)  ";
    $Query .= "SELECT $fl_usuario,fl_term_pago, cl_metodo_pago, fe_pago, mn_pagado, ds_comentario, ds_cheque, mn_late_fee, ds_transaccion, mn_tax_paypal, ds_tax_provincia FROM k_ses_pago ";
    $Query .= "WHERE cl_sesion='$cl_sesion' ";
    EjecutaQuery($Query);
    $Query = "DELETE FROM k_ses_pago WHERE cl_sesion = '$cl_sesion'";
    EjecutaQuery($Query);

    # Cuando se convierte a estudiante se le recuerda a la persona responsable de los pagos
    # Pero si se le envio el correo antes ya no se le manda nada
    if(!$email_resp){
      # Send email  2025  no found
      //$email_resp = EnviaMailHTML($email_noreply, $email_noreply, $ds_email_r, ObtenEtiqueta(865), $message_resp, $app_frm_email);
      EjecutaQuery("UPDATE k_presponsable SET fg_email='1' WHERE cl_sesion='$cl_sesion'");
    }
  }

  # Actualiza datos de costos para el contrato
  $Query  = 'UPDATE k_app_contrato ';
  $Query .= 'SET ds_costs="'.$ds_costos_ad.'", mn_costs='.$no_costos_ad.', ds_discount="'.$ds_descuento.'", mn_discount='.$no_descuento.', ';
  $Query .= 'mn_tot_tuition='.$total_tuition.', mn_tot_program='.$total.', mn_a_due='.$amount_due_a.', mn_a_paid='.$amount_paid_a.', ';
  $Query .= 'mn_b_due='.$amount_due_b.', mn_b_paid='.$amount_paid_b.', mn_c_due='.$amount_due_c.', mn_c_paid='.$amount_paid_c.', ';
  $Query .= 'mn_d_due='.$amount_due_d.', mn_d_paid='.$amount_paid_d.', ds_m_add_number="'.$ds_m_add_number.'", ds_m_add_street="'.$ds_m_add_street.'", ds_m_add_city="'.$ds_m_add_city.'",   ';
  $Query .= 'ds_m_add_state="'.$ds_m_add_state.'", ds_m_add_zip="'.$ds_m_add_zip.'", ds_m_add_country="'.$ds_m_add_country.'", fg_international="'.$fg_international.'",ds_a_email="'.$ds_a_email.'", ';
  $Query .= 'cl_preference_1 = '.$cl_preference_1.', cl_preference_2 = '.$cl_preference_2.', cl_preference_3 = '.$cl_preference_3.', ds_p_name="'.$ds_p_name.'", ';
  $Query .= 'ds_education_number="'.$ds_education_number.'", ds_usual_name="'.$ds_usual_name.'" ';
  $Query .= ',ds_citizenship="'.$ds_citizenship.'", fg_study_permit="'.$fg_study_permit.'", fg_study_permit_other="'.$fg_study_permit_other.'", fg_aboriginal="'.$fg_aboriginal.'", ';
  $Query .= 'ds_aboriginal="'.$ds_aboriginal.'", fg_health_condition="'.$fg_health_condition.'", ds_health_condition="'.$ds_health_condition.'" ';
  if($fl_class_time)
      $Query .=',fl_class_time='.$fl_class_time.'  ';
  $Query .='WHERE cl_sesion="'.$cl_sesion.'" ';
  EjecutaQuery($Query);

  $Query='UPDATE k_app_contrato SET ds_discount="'.$ds_descuento.'", mn_discount='.$no_descuento.' WHERE cl_sesion="'.$cl_sesion.'" ';
  EjecutaQuery($Query);

  #Actaulizamos costos segun sea canada/Extranjero
  if($fg_international==1){

      if( (empty($no_descuento)) || ($no_descuento<=0) )
          $no_descuento=0;


      #Recuperamos costos internacionales.
      if($fg_aplicar_international==1){

          if($fg_payment=='C'){

              $Qeu="SELECT mn_app_fee_internacional_combined,mn_tuition_internacional_combined,mn_costs_internacional_combined,ds_costs_internacional_combined ";
              $Qeu.=",mn_a_due_internacional_combined, mn_a_paid_internacional_combined, mn_b_due_internacional_combined, mn_b_paid_internacional_combined, mn_c_due_internacional_combined, mn_c_paid_internacional_combined, mn_d_due_internacional_combined, mn_d_paid_internacional_combined ";


          }else{

              $Qeu="SELECT mn_app_fee_internacional,mn_tuition_internacional,mn_costs_internacional,ds_costs_internacional ";
              $Qeu.=",mn_a_due_internacional, mn_a_paid_internacional, mn_b_due_internacional, mn_b_paid_internacional, mn_c_due_internacional, mn_c_paid_internacional, mn_d_due_internacional, mn_d_paid_internacional ";
          }

      }else{

          if($fg_payment=='C'){


              $Qeu.="SELECT mn_app_fee_combined,mn_tuition_combined,mn_costs_combined,ds_costs_combined ";
              $Qeu.=",mn_a_due_combined, mn_a_paid_combined, mn_b_due_combined, mn_b_paid_combined, mn_c_due_combined, mn_c_paid_combined, mn_d_due_combined, mn_d_paid_combined  ";

          }else{


              $Qeu.="SELECT mn_app_fee,mn_tuition,mn_costs,ds_costs ";
              $Qeu.=",mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid  ";
          }
      }

      $Qeu.="
             FROM k_programa_costos
             WHERE fl_programa=$fl_programa ";
      $row=RecuperaValor($Qeu);
      $mn_app_fee=$row[0];
      $mn_tuition=$row[1];
      $mn_costs=$row[2];
      $ds_costs=$row[3];

      if(empty($mn_app_fee))
          $mn_app_fee=0.0;
      if(empty($mn_tuition))
          $mn_tuition=0.0;
      if(empty($mn_costs))
          $mn_costs=0.0;


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

      if($mn_costs<=0)
        $mn_costs=$no_costos_ad;
      if(empty($ds_costs))
          $ds_costs=$ds_costos_ad;


      $mn_tot_tuition = $mn_tuition + $mn_costs - $no_descuento;



      $mn_tot_program = $mn_tot_tuition + $mn_app_fee;





      $Query ="UPDATE k_app_contrato SET ";
      $Query.="fg_aplicar_international='$fg_aplicar_international' , ";
      $Query.="mn_app_fee=$mn_app_fee  ,";
               // $Query.="ds_discount='$ds_descuento', mn_discount=$no_descuento, ";
               // $Query.="ds_costs='$ds_costos_ad', mn_costs=$no_costos_ad, ";
      $Query.="mn_tuition=$mn_tuition ";
                   //$Query.="mn_costs=$mn_costs ,";
                   //$Query.="ds_costs='$ds_costs', ";
                  //$Query.="mn_tot_tuition=$mn_tot_tuition, ";
                  //$Query.="mn_tot_program=$mn_tot_program ";
                  //$Query.=",mn_a_due=$mn_a_due , mn_a_paid=$mn_a_paid, mn_b_due=$mn_b_due, mn_b_paid=$mn_b_paid, mn_c_due=$mn_c_due, mn_c_paid=$mn_c_paid,
                 //mn_d_due=$mn_d_due, mn_d_paid=$mn_d_paid ";
      $Query.="WHERE cl_sesion='$cl_sesion' ";
      EjecutaQuery($Query);







  }


	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));

?>