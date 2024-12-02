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
  #address
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
  
  $fg_provincia = RecibeParametroNumerico('fg_provincia');
  $fl_provincia = RecibeParametroNumerico('fl_provincia');
  $fg_archive = RecibeParametroBinario('fg_archive');
  
  #Validaciones  de address
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
  if(empty($fl_provincia) AND $fg_provincia==38)
    $fl_provincia_err = ERR_REQUERIDO;
    
  # Verifica que el tipo de archivo para avatar sea JPG
  $ext = strtolower(ObtenExtensionArchivo($_FILES['ds_ruta_foto']['name'][0]));
  if(!empty($ext) AND $ext!='jpg' AND $ext!='jpeg')
    $ds_ruta_foto_err = ERR_ARCHIVO_JPEG;

  $fg_error = $ds_add_number_err || $ds_add_street_err || $ds_add_city_err || $ds_add_state_err || $ds_add_zip_err || $ds_add_country_err 
              || $ds_fname_err || $ds_lname_err || $ds_number_err || $ds_email_err || $fe_birth_err || $ds_ruta_foto_err || $fl_provincia_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_add_number', $ds_add_number);
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
    Forma_CampoOculto('fg_provincia', $fg_provincia);
    Forma_CampoOculto('fl_provincia', $fl_provincia); 
    Forma_CampoOculto('fg_archive', $fg_archive);
    echo "\n</form>
    <script>
      document.datos.submit();
    </script></body></html>";
    exit;
  }
  

  $foto_size = ObtenConfiguracion(80);
  if(!empty($_FILES['ds_ruta_foto']['tmp_name'][0])) {
    $ruta = PATH_ALU_IMAGES_F."/id";
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

  # Prepara fechas en formato para insertar
  if(!empty($fe_birth))
    $fe_birth = "'".ValidaFecha($fe_birth)."'";
  else
    $fe_birth = "NULL";
  
  # el fl_provincia es igual que el ds_add_state
  if($fg_provincia==38)
    $ds_add_state=$fl_provincia;

  # Actualiza o inserta el registro
  $Query  = "UPDATE c_sesion ";
  $Query .= "SET fg_inscrito='$fg_inscrito', fg_archive='$fg_archive' ";
  $Query .= "WHERE fl_sesion=$clave";
  EjecutaQuery($Query);
  
  # Actualiza datos
  $Query = "UPDATE k_ses_app_frm_1 SET ds_add_number ='$ds_add_number', ";
  $Query .= "ds_add_street='$ds_add_street', ds_add_city='$ds_add_city', ds_add_state='$ds_add_state', ds_add_zip='$ds_add_zip', ds_add_country='$ds_add_country', ";
  $Query .= "ds_fname ='".$ds_fname."', ds_mname='".$ds_mname."', ds_lname='".$ds_lname."', ds_number='".$ds_number."', ds_link_to_portfolio='$ds_link_to_portfolio', ";
  $Query .= "fe_birth=$fe_birth, ds_email='$ds_email' WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave) ";
  EjecutaQuery($Query);
  if(!empty($ds_ruta_foto)) {
    $Query = "UPDATE k_ses_app_frm_1 SET ds_ruta_foto='$ds_ruta_foto' WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave) ";
    EjecutaQuery($Query);
  }
  
  # Inserta el registro del estudiante
  $Query  = "SELECT a.cl_sesion, ds_fname, ds_mname, ds_lname, ds_email, fg_gender, fe_birth ";
  $Query .= "FROM c_sesion a, k_ses_app_frm_1 b ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion ";
  $Query .= "AND fl_sesion='$clave'";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  $ds_email = str_texto($row[4]);
  $fg_gender = $row[5];
  $fe_birth = $row[6];

  if($fg_inscrito == '1') {
    $Query  = "INSERT INTO c_usuario(ds_login, ds_password, cl_sesion, fg_activo, fe_alta, no_accesos, ";
    $Query .= "ds_nombres, ds_apaterno, ds_amaterno, fg_genero, fe_nacimiento, ds_email, fl_perfil) ";
    $Query .= "VALUES('ds_login', '1234567890', '$cl_sesion', '1', CURRENT_TIMESTAMP, 0, ";
    $Query .= "'$ds_fname', '$ds_lname', '$ds_mname', '$fg_gender', '$fe_birth', '$ds_email', ".PFL_ESTUDIANTE.") ";
    $fl_usuario = EjecutaInsert($Query);
    $ds_login = substr(strtolower($ds_lname), 0, 1) . substr(strtolower($ds_fname), 0, 1);
    $ds_login = $ds_login . substr($fe_birth, 2, 2) . substr($fe_birth, 5, 2) . substr($fe_birth, 8, 2);
    $ds_login = $ds_login . str_pad($fl_usuario, 4, "0", STR_PAD_LEFT);
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

  }
  
  # Actualiza datos de costos para el contrato
  $Query  = "UPDATE k_app_contrato ";
  $Query .= "SET ds_costs='$ds_costos_ad', mn_costs=$no_costos_ad, ds_discount='$ds_descuento', mn_discount=$no_descuento, ";
  $Query .= "mn_tot_tuition=$total_tuition, mn_tot_program=$total, mn_a_due=$amount_due_a, mn_a_paid=$amount_paid_a, ";
  $Query .= "mn_b_due=$amount_due_b, mn_b_paid=$amount_paid_b, mn_c_due=$amount_due_c, mn_c_paid=$amount_paid_c, ";
  $Query .= "mn_d_due=$amount_due_d, mn_d_paid=$amount_paid_d, ds_m_add_number='$ds_m_add_number', ds_m_add_street='$ds_m_add_street', ds_m_add_city='$ds_m_add_city',   ";
  $Query .= "ds_m_add_state='$ds_m_add_state', ds_m_add_zip='$ds_m_add_zip', ds_m_add_country='$ds_m_add_country', fg_international='$fg_international',ds_a_email='$ds_a_email' ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  EjecutaQuery($Query);
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>