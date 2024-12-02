<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibe parametros
  $origen = RecibeParametroHTML('origen',False,True);
  if(!empty($origen)){
    $clave = RecibeParametroHTML('clave',False,True);
  }
  else{
    $clave = RecibeParametroNumerico('clave');
  }

  $fg_error = RecibeParametroNumerico('fg_error');
  $error = RecibeParametroNumerico('error');
  $confirmacion = RecibeParametroNumerico('confirmacion');

  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ALUMNOS, $permiso) OR $permiso == PERMISO_ALTA) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  //programa actual
  $programa = ObtenProgramaActual();
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    $Query  = "SELECT ds_login, fg_activo, ".ConsultaFechaBD('fe_alta', FMT_FECHA)." fe_alta, ";
    $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
    $Query .= "(".ConcatenaBD($concat).") 'fe_ultacc', ";
    $Query .= "no_accesos, ds_nombres, ds_apaterno, ds_amaterno, a.ds_email, a.fl_perfil, b.nb_perfil, fg_genero, ";
    $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, a.cl_sesion, ds_notas, d.ds_number, d.ds_alt_number, ";
    $Query .= "d.ds_add_number, d.ds_add_street, d.ds_add_city, d.ds_add_state, d.ds_add_zip, d.ds_add_country, ";
    $Query .= "e.ds_m_add_number, e.ds_m_add_street, e.ds_m_add_city, e.ds_m_add_state, e.ds_m_add_zip, e.ds_m_add_country, d.ds_link_to_portfolio, d.ds_ruta_foto ds_foto_oficial ";
    $Query .= "FROM c_usuario a, c_perfil b, c_alumno c, k_ses_app_frm_1 d, k_app_contrato e ";
    $Query .= "WHERE a.fl_perfil=b.fl_perfil AND a.cl_sesion=d.cl_sesion ";
    $Query .= "AND a.fl_usuario=c.fl_alumno AND a.cl_sesion=e.cl_sesion ";
    $Query .= "AND fl_usuario=$clave";
    $rs = EjecutaQuery($Query);
    $registro=CuentaRegistros($rs);
    # no tiene contrato
    if(empty($registro)){
      $Query  = "SELECT ds_login, fg_activo, ".ConsultaFechaBD('fe_alta', FMT_FECHA)." fe_alta, ";  
      $Query .= "(".ConcatenaBD($concat).") 'fe_ultacc', ";
      $Query .= "no_accesos, ds_nombres, ds_apaterno, ds_amaterno, a.ds_email, a.fl_perfil, b.nb_perfil, fg_genero, ";
      $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, a.cl_sesion, ds_notas, d.ds_number, d.ds_alt_number, ";
      $Query .= "d.ds_add_number, d.ds_add_street, d.ds_add_city, d.ds_add_state, d.ds_add_zip, d.ds_add_country ";
      $Query .= "FROM c_usuario a, c_perfil b, c_alumno c, k_ses_app_frm_1 d ";
      $Query .= "WHERE a.fl_perfil=b.fl_perfil AND a.cl_sesion=d.cl_sesion ";
      $Query .= "AND a.fl_usuario=c.fl_alumno ";
      $Query .= "AND fl_usuario=$clave";
    }
      
    $row = RecuperaValor($Query);
    $ds_login = str_texto($row[0]);
    $fg_activo = $row[1];
    $fe_alta = $row[2];
    $fe_ultacc = $row[3];
    $no_accesos = $row[4];
    $ds_nombres = str_texto($row[5]);
    $ds_apaterno = str_texto($row[6]);
    $ds_amaterno = str_texto($row[7]);
    $ds_email = str_texto($row[8]);
    $fl_perfil = $row[9];
    $nb_perfil = $row[10];
    $fg_genero = $row[11];
    $fe_nacimiento = $row[12];
    $cl_sesion = $row[13];
    $ds_notas = $row[14];
    $ds_number = $row[15];
    $ds_alt_number = $row[16];
    $ds_add_number = str_texto($row[17]);
    $ds_add_street = str_texto($row[18]);
    $ds_add_city = str_texto($row[19]);
    $ds_add_state = str_texto($row[20]);
    $fl_provincia = str_texto($row[20]);
    $ds_add_zip = str_texto($row[21]);
    $ds_add_country = str_texto($row[22]);
    $ds_m_add_number = $row[23];
    $ds_m_add_street = $row[24];
    $ds_m_add_city = $row[25];
    $ds_m_add_state = $row[26];
    $ds_m_add_zip = $row[27];
    $ds_m_add_country = $row[28];
    $ds_link_to_portfolio = str_texto($row[29]);
    $ds_foto_oficial = str_texto($row[30]);
    
    $row = RecuperaValor("SELECT fg_pago FROM c_sesion WHERE cl_sesion='$cl_sesion'");
    $fg_pago = $row[0];
    $ds_login_err = "";
    $ds_password_err = "";
    $ds_password_conf_err = "";
    $ds_nombres_err = "";
    $ds_apaterno_err = "";
    $ds_email_err = "";
    $fl_perfil_err = "";
    $fe_nacimiento_err = "";
    $fe_carta_err = "";
    $fe_contrato_err = "";
    $fe_fin_err = "";
    $fe_completado_err = "";
    $fe_emision_err = "";
    $fe_graduacion_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_login = RecibeParametroHTML('ds_login');
    $ds_login_err = RecibeParametroNumerico('ds_login_err');
    $ds_password_err = RecibeParametroNumerico('ds_password_err');
    $ds_password_conf_err = RecibeParametroNumerico('ds_password_conf_err');
    $fg_activo = RecibeParametroBinario('fg_activo');
    $fe_alta = RecibeParametroFecha('fe_alta');
    $fe_ultacc = RecibeParametroFecha('fe_ultacc');
    $no_accesos = RecibeParametroNumerico('no_accesos');
    $ds_nombres = RecibeParametroHTML('ds_nombres');
    $ds_nombres_err = RecibeParametroNumerico('ds_nombres_err');
    $ds_apaterno = RecibeParametroHTML('ds_apaterno');
    $ds_apaterno_err = RecibeParametroNumerico('ds_apaterno_err');
    $ds_amaterno = RecibeParametroHTML('ds_amaterno');
    $fg_international = RecibeParametroBinario('fg_international');
    $ds_email = RecibeParametroHTML('ds_email');
    $ds_email_err = RecibeParametroNumerico('ds_email_err');
    $ds_a_email = RecibeParametroHTML('ds_a_email');
    $ds_a_email_err = RecibeParametroNumerico('ds_a_email_err');
    $fl_perfil = RecibeParametroNumerico('fl_perfil');
    $fl_perfil_err = RecibeParametroNumerico('fl_perfil_err');
    $nb_perfil = RecibeParametroHTML('nb_perfil');
    $fg_genero = RecibeParametroHTML('fg_genero');
    $fe_nacimiento = RecibeParametroFecha('fe_nacimiento');
    $fe_nacimiento_err = RecibeParametroNumerico('fe_nacimiento_err');
    $cl_sesion = RecibeParametroHTML('cl_sesion');
    $fg_pago = RecibeParametroBinario('fg_pago');
    $ds_notas = RecibeParametroHTML('ds_notas');
    $fe_carta = RecibeParametroFecha('fe_carta');
    $fe_carta_err = RecibeParametroNumerico('fe_carta_err');
    $fe_contrato = RecibeParametroFecha('fe_contrato');
    $fe_contrato_err = RecibeParametroNumerico('fe_contrato_err');
    $fe_fin = RecibeParametroFecha('fe_fin');
    $fe_fin_err = RecibeParametroNumerico('fe_fin_err');
    $fe_completado = RecibeParametroFecha('fe_completado');
    $fe_completado_err = RecibeParametroNumerico('fe_completado_err');
    $fe_emision = RecibeParametroFecha('fe_emision');
    $fe_emision_err = RecibeParametroNumerico('fe_emision_err');
    $fe_graduacion = RecibeParametroFecha('fe_graduacion');
    $fe_graduacion_err = RecibeParametroNumerico('fe_graduacion_err');
    $fl_periodo = RecibeParametroNumerico('fl_periodo');
    $fg_desercion = RecibeParametroBinario('fg_desercion');
    $fg_dismissed = RecibeParametroBinario('fg_dismissed');
    $fg_job = RecibeParametroBinario('fg_job');
    $fg_graduacion = RecibeParametroBinario('fg_graduacion');
    //jgfl 03-11-2014
    $ds_number = RecibeParametroHTML('ds_number');
    $ds_number_err= RecibeParametroHTML('ds_number_err');
    $ds_alt_number = RecibeParametroHTML('ds_alt_number');
    $ds_alt_number_err = RecibeParametroHTML('ds_alt_number_err');
    $ds_add_number = RecibeParametroHTML('ds_add_number');
    $ds_add_number_err = RecibeParametroHTML('ds_add_number_err');
    $ds_add_street = RecibeParametroHTML('ds_add_street');
    $ds_add_street_err = RecibeParametroHTML('ds_add_street_err');
    $ds_add_city = RecibeParametroHTML('ds_add_city');
    $ds_add_city_err = RecibeParametroHTML('ds_add_city_err');
    $ds_add_state = RecibeParametroHTML('ds_add_state');
    $ds_add_state_err = RecibeParametroHTML('ds_add_state_err');
    $ds_add_zip = RecibeParametroHTML('ds_add_zip');
    $ds_add_zip_err = RecibeParametroHTML('ds_add_zip_err');
    $ds_add_country = RecibeParametroHTML('ds_add_country');
    $ds_add_country_err = RecibeParametroHTML('ds_add_country_err');
    #mailing address
    $ds_m_add_number = RecibeParametroHTML('ds_m_add_number');
    $ds_m_add_street = RecibeParametroHTML('ds_m_add_street');
    $ds_m_add_city = RecibeParametroHTML('ds_m_add_city');
    $ds_m_add_state = RecibeParametroHTML('ds_m_add_state');
    $ds_m_add_zip = RecibeParametroHTML('ds_m_add_zip');
    $ds_m_add_country = RecibeParametroHTML('ds_m_add_country');
    $ds_link_to_portfolio = RecibeParametroHTML('ds_link_to_portfolio');
    $ds_foto_oficial = RecibeParametroHTML('ds_foto_oficial');
    $ds_foto_oficial_err = RecibeParametroHTML('ds_foto_oficial_err');
    $fg_provincia = RecibeParametroNumerico('fg_provincia');
    $fl_provincia = RecibeParametroNumerico('fl_provincia');
  }
  
  # Recupera datos de la sesion y forma de aplicacion
  $concat = array(ConsultaFechaBD('fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultmod', FMT_HORA));
  $Query  = "SELECT fg_paypal, (".ConcatenaBD($concat).") 'fe_ultmod', fl_sesion ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $fg_paypal = $row[0];
  $fe_ultmod = $row[1];
  $fl_sesion = $row[2];
  
  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
  $Query .= ConsultaFechaBD('fe_birth', FMT_FECHA)." fe_birth, ";
  $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, d.ds_pais, ";
  $Query .= "ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, e.ds_pais, ";
  $Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, nb_programa, a.fl_periodo, a.fl_programa, nb_periodo, fl_template,fg_taxes ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_periodo=c.fl_periodo ";
  $Query .= "AND a.ds_add_country=d.fl_pais ";
  $Query .= "AND a.ds_eme_country=e.fl_pais ";
  $Query .= "AND cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $fl_programa = $row[25];
  $nb_programa = $row[23];	
  $nb_periodo = $row[26];
  if(empty($fl_periodo))
    $fl_periodo = $row[24];
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  //$ds_number = str_texto($row[3]);
  //$ds_alt_number = str_texto($row[4]);
  $fg_gender = str_texto($row[6]);
  $fe_birth = $row[7];
  //$ds_add_number = str_texto($row[8]);
  //$ds_add_street = str_texto($row[9]);
  //$ds_add_city = str_texto($row[10]);
  //$ds_add_state = str_texto($row[11]);
  //$ds_add_zip = str_texto($row[12]);
  //$ds_add_country = str_texto($row[13]);
  $ds_eme_fname = str_texto($row[14]);
  $ds_eme_lname = str_texto($row[15]);
  $ds_eme_number = str_texto($row[16]);
  $ds_eme_relation = str_texto($row[17]);
  $ds_eme_country = str_texto($row[18]);
  $fg_ori_via = str_texto($row[19]);
  $ds_ori_other = str_texto($row[20]);
  $fg_ori_ref = str_texto($row[21]);
  $ds_ori_ref_name = str_texto($row[22]);
  $fl_template = $row[27];
  $fg_taxes = $row[28];
  
  #Recupera datos adicionales a la forma 1 y del contrato del aplicante
  $Query  = "SELECT no_contrato, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_discount, ds_discount, mn_tot_tuition, mn_tot_program, ";
  $Query .= "mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
  $Query .= "ds_cadena, ds_firma_alumno, fg_opcion_pago, fe_firma, ds_p_name, ds_education_number, fg_international, ";
  $Query .= "cl_preference_1, cl_preference_2, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_pais, ds_a_email, cl_preference_3 ";
  $Query .= "FROM k_app_contrato  a LEFT JOIN c_pais b ON a.ds_m_add_country=b.fl_pais ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  $Query .= "ORDER BY no_contrato";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    $no_contrato = $row[0];
    $app_fee = $row[1];
    $tuition = $row[2];
    $no_costos_ad = $row[3];
    $ds_costos_ad = $row[4];
    $no_descuento = $row[5];
    $ds_descuento = $row[6];
    $mn_tot_tuition = $row[7];
    $mn_tot_program = $row[8];
    $amount_due_a = $row[9];
    $amount_paid_a = $row[10];
    $amount_due_b = $row[11];
    $amount_paid_b = $row[12];
    $amount_due_c = $row[13];
    $amount_paid_c = $row[14];
    $amount_due_d = $row[15];
    $amount_paid_d = $row[16];
    $ds_cadena[$no_contrato] = $row[17];
    $ds_firma_alumno[$no_contrato] = $row[18];
    $opc_pago = $row[19];
    $fe_firma[$no_contrato] = $row[20];
    $ds_p_name = $row[21];
    $ds_education_number = $row[22];
    # Si no hay error 
    if(empty($fg_error))
      $fg_international = $row[23];
    $cl_preference_1 = $row[24];
    $cl_preference_2 = $row[25];
    if(empty($fg_error))
      $ds_a_email = $row[32];
    $cl_preference_3 = $row[33];
  }
  
  # Recupera datos de pagos del curso
  $Query  = "SELECT no_a_payments, ds_a_freq, no_b_payments, ds_b_freq, no_c_payments, ds_c_freq, no_d_payments, ds_d_freq, cl_type, ";
  $Query .= "no_a_interes, no_b_interes, no_c_interes, no_d_interes, no_semanas ";
  $Query .= "FROM k_programa_costos ";
  $Query .= "WHERE fl_programa = $fl_programa";
  $row = RecuperaValor($Query);
  $no_a_payments = $row[0];
  $ds_a_freq = $row[1];
  $no_b_payments = $row[2];
  $ds_b_freq = $row[3];
  $no_c_payments = $row[4];
  $ds_c_freq = $row[5];
  $no_d_payments = $row[6];
  $ds_d_freq = $row[7];
  $cl_type = $row[8];
  $no_a_interes = $row[9];
  $no_b_interes = $row[10];
  $no_c_interes = $row[11];
  $no_d_interes = $row[12];
  $no_semanas = $row[13];
    
  # Calculos pagos
  $total_tuition = number_format($tuition + $no_costos_ad - $no_descuento, 2, '.', '');
  $total = number_format($app_fee + $total_tuition, 2, '.', '');
    
  # Recupera datos del aplicante: forma 2
  $Query  = "SELECT ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7 ";
  $Query .= "FROM k_ses_app_frm_2 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_resp2_1 = str_texto($row[0]);
  $ds_resp2_2 = str_texto($row[1]);
  $ds_resp2_3 = str_texto($row[2]);
  $ds_resp2_4 = str_texto($row[3]);
  $ds_resp2_5 = str_texto($row[4]);
  $ds_resp2_6 = str_texto($row[5]);
  $ds_resp2_7 = str_texto($row[6]);
  
  # Recupera datos del aplicante: forma 3
  $Query  = "SELECT fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, ";
  $Query .= "fg_resp_2_1, fg_resp_2_2, fg_resp_2_3, fg_resp_2_4, fg_resp_2_5, fg_resp_2_6, fg_resp_2_7, ";
  $Query .= "fg_resp_3_1, fg_resp_3_2 ";
  $Query .= "FROM k_ses_app_frm_4 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $fg_resp4_1_1 = str_ascii($row[0]);
  $fg_resp4_1_2 = str_ascii($row[1]);
  $fg_resp4_1_3 = str_ascii($row[2]);
  $fg_resp4_1_4 = str_ascii($row[3]);
  $fg_resp4_1_5 = str_ascii($row[4]);
  $fg_resp4_1_6 = str_ascii($row[5]);
  $fg_resp4_2_1 = str_ascii($row[6]);
  $fg_resp4_2_2 = str_ascii($row[7]);
  $fg_resp4_2_3 = str_ascii($row[8]);
  $fg_resp4_2_4 = str_ascii($row[9]);
  $fg_resp4_2_5 = str_ascii($row[10]);
  $fg_resp4_2_6 = str_ascii($row[11]);
  $fg_resp4_2_7 = str_ascii($row[12]);
  $fg_resp4_3_1 = str_ascii($row[13]);
  $fg_resp4_3_2 = str_ascii($row[14]);
  
  # Recupera datos del aplicante: forma 4
  $Query  = "SELECT ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, ds_resp_8 ";
  $Query .= "FROM k_ses_app_frm_3 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_resp3_1 = str_texto($row[0]);
  $ds_resp3_2_1 = str_texto($row[1]);
  $ds_resp3_2_2 = str_texto($row[2]);
  $ds_resp3_2_3 = str_texto($row[3]);
  $ds_resp3_3 = str_texto($row[4]);
  $ds_resp3_4 = str_texto($row[5]);
  $ds_resp3_5 = str_texto($row[6]);
  $ds_resp3_6 = str_texto($row[7]);
  $ds_resp3_7 = str_texto($row[8]);
  $ds_resp3_8 = str_texto($row[9]);
  
  # Recupera datos de configuracion del alumno
  $Query  = "SELECT a.fl_zona_horaria, nb_zona_horaria, ds_ruta_avatar, ds_ruta_foto, ds_website, ds_gustos, ds_pasatiempos, no_gmt,a.no_promedio_t ";
  $Query .= "FROM c_alumno a, c_zona_horaria b ";
  $Query .= "WHERE a.fl_zona_horaria=b.fl_zona_horaria ";
  $Query .= "AND fl_alumno=$clave";
  $row = RecuperaValor($Query);
  $fl_zona_horaria = $row[0];
  $ds_zona_horaria = str_texto($row[1])." (GMT ".$row[7].")";
  $ds_ruta_avatar = str_texto($row[2]);
  $ds_ruta_foto = str_texto($row[3]);
  $ds_website = str_texto($row[4]);
  $ds_gustos = str_texto($row[5]);
  $ds_pasatiempos = str_texto($row[6]);
  $no_promedio_t = $row[8];
  
  # Obtiene el grupo, el term y el maestro
  $Query = "SELECT a.fl_grupo, b.fl_term, c.ds_nombres, c.ds_apaterno, b.nb_grupo, d.no_grado  ";
  $Query .= "FROM k_alumno_grupo a LEFT JOIN (c_grupo b LEFT JOIN c_usuario c ON b.fl_maestro = c.fl_usuario) ON a.fl_grupo = b.fl_grupo ";
  $Query .= "LEFT JOIN k_term d ON d.fl_term=b.fl_term ";
  $Query .= "WHERE fl_alumno = $clave";
  $row1 = RecuperaValor($Query);
  $fl_grupo = $row1[0];
  $fl_term = $row1[1];
  $nb_maestro = $row1[2].'&nbsp;'.$row1[3];
  $ds_grupo = $row1[4];
  $no_grado_actual = $row1[5];

  # MRA: 16 sept 2014: Si aun no tiene un grupo asignado, obtiene el term de la forma de aplicacion (fl_term se usa para obterner la informacion de pagos)
  if(empty($fl_term)) {
    //$Query ="SELECT fl_term FROM k_term WHERE fl_programa=$fl_programa AND fl_periodo=$fl_periodo AND ";
    $Query ="SELECT MIN(fl_term) FROM k_alumno_term WHERE fl_alumno=$clave ";    
    $row = RecuperaValor($Query);
    $fl_term = $row[0];
  }

  # Recupera el program start date 
  $Query  = "SELECT nb_periodo, ".ConsultaFechaBD('c.fe_inicio',FMT_FECHA)." ";
  $Query .= "FROM k_term b, c_periodo c, k_alumno_term d ";
  $Query .= "WHERE b.fl_periodo=c.fl_periodo ";
  $Query .= "AND b.fl_term=d.fl_term AND d.fl_alumno='$clave' ";
  $Query .= "AND no_grado=1 ";
  $row2 = RecuperaValor($Query);
  $fe_inicio = $row2[0];
  $fe_inicio_term = $row2[1];

  # Recupera el term inicial
  # Si no tiene fecha inicio buscara por medio del term inicial
  if(empty($fe_inicio) AND empty($fe_inicio_term)){
    $Query  = "SELECT fl_term_ini FROM k_term WHERE fl_programa=$fl_programa AND fl_term=$fl_term";
    $row = RecuperaValor($Query);
    $fl_term_ini = $row[0];
    $row = RecuperaValor("SELECT nb_periodo, DATE_FORMAT(b.fe_inicio, '%d-%m-%Y') FROM k_term a, c_periodo b WHERE a.fl_periodo = b.fl_periodo AND fl_term=$fl_term_ini");
    $fe_inicio = $row[0];
    $fe_inicio_term = $row[1];
  }
  
  # Recupera datos de Official Transcript
  $Query = "SELECT ";
  $Query .= ConsultaFechaBD('fe_carta', FMT_FECHA)." fe_carta, ";
  $Query .= ConsultaFechaBD('fe_contrato', FMT_FECHA)." fe_contrato, ";
  $Query .= ConsultaFechaBD('fe_fin', FMT_FECHA)." fe_fin, ";
  $Query .= ConsultaFechaBD('fe_completado', FMT_FECHA)." fe_completado, ";
  $Query .= ConsultaFechaBD('fe_emision', FMT_FECHA)." fe_emision, ";
  $Query .= "fg_certificado, fg_honores, ";
  $Query .= ConsultaFechaBD('fe_graduacion', FMT_FECHA)." fe_graduacion, ";
	$Query .= "fg_desercion, fg_dismissed, fg_job, fg_graduacion ";
  $Query .= "FROM k_pctia ";
  $Query .= "WHERE fl_alumno = $clave ";
  $Query .= "AND fl_programa = $fl_programa ";
  $row = RecuperaValor($Query);
  $fe_carta = $row[0];
  $fe_contrato = $row[1];
  $fe_fin = $row[2];
  $fe_completado = $row[3];
  # Si las fechas fin y completado estan en blanco sumamos los meses que dura el curso
  # a la fe_inicio del periodo elejido en app form
  if(empty($fe_fin) OR empty($fe_completado)){
    
    #Obtenemos los meses que dura el curso y las fechas fin y completado
    $meses = $no_semanas/4;
    $fe_fin = date("d-m-Y", strtotime("$fe_inicio_term + $meses months"));
    $fe_completado = date("d-m-Y", strtotime("$fe_inicio_term + $meses months"));
    
  }
  $fe_emision = $row[4];
  $fg_certificado = $row[5];
  $fg_honores = $row[6];
  $fe_graduacion = $row[7];
	$fg_desercion = $row[8];
  $fg_dismissed = $row[9];
  $fg_job = $row[10];
  $fg_graduacion = $row[11];
  
  # Recupera los nivles del programa para presentar calificaciones
  $Query  = "SELECT count(a.fl_leccion), a.no_grado, b.nb_programa ";
  $Query .= "FROM c_leccion a, c_programa b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_programa=$fl_programa ";
  $Query .= "GROUP BY a.no_grado ";
  $Query .= "ORDER BY a.no_grado ";
  $rs = EjecutaQuery($Query);
  
  # Recupera los distintos fl_term en los que ha estado un alumno
  # Si hay 2 term con el mismo grado obtendremos el ultimo que se inserto
  $Query  = "SELECT MAX(a.fl_term) ";
  $Query .= "FROM k_alumno_term a, k_term b, c_periodo c ";
  $Query .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$clave ";
  $Query .= "GROUP BY b.no_grado ORDER BY c.fe_inicio, b.no_grado";
  $consulta = EjecutaQuery($Query);
  
  for($tot_grados = 0; $row = RecuperaRegistro($rs); $tot_grados++) {
    $tot_lecciones[$tot_grados] = $row[0];
    $no_grado[$tot_grados] = $row[1];
    $nb_programa = str_uso_normal($row[2]);
    $row_term = RecuperaRegistro($consulta);
    $term_nivel = $row_term[0];
    if(empty($term_nivel))
      $term_nivel = $fl_term;
    
    # Recupera las lecciones del grado
    $Query  = "SELECT a.fl_leccion, a.no_semana, a.ds_titulo, b.fl_semana ";
    $Query .= "FROM c_leccion a LEFT JOIN k_semana b ";
    $Query .= "ON (a.fl_leccion=b.fl_leccion AND b.fl_term=$term_nivel) ";
    $Query .= "WHERE a.fl_programa=$fl_programa ";
    $Query .= "AND a.no_grado=$no_grado[$tot_grados] ";
    $Query .= "ORDER BY a.no_semana ";
    $rs2 = EjecutaQuery($Query);
    for($j = 0; $row2 = RecuperaRegistro($rs2); $j++) {
      $fl_leccion[$tot_grados][$j] = $row2[0];
      $no_semana[$tot_grados][$j] = $row2[1];
      $ds_titulo[$tot_grados][$j] = str_uso_normal($row2[2]);
      $fl_semana[$tot_grados][$j] = $row2[3];
    }
  }
 
  # Presenta forma de captura
  PresentaHeader( );
  echo "
  <script type='text/javascript' src='".PATH_JS."/sendtemplate.js.php'></script>";
  
  PresentaEncabezado(FUNC_ALUMNOS);
  
  # Funciones para preview de imagenes
  require 'preview.inc.php';
  
  # Forma para cambiar contrasena a otros usuarios
  if(ValidaPermiso(FUNC_PWD_OTROS, PERMISO_EJECUCION)) {
    $ds_cambiar_pwd = "&nbsp;&nbsp;&nbsp;<a href='javascript:cambio_pwd_otros.submit();'>".ObtenEtiqueta(126)."</a>";
    echo "
  <form name='cambio_pwd_otros' method='post' action='pwd_frm.php'>
    <input type='hidden' name='clave' value='$clave'>
  </form>\n";
  }
  else
    $ds_cambiar_pwd = " ";
  
  #Liga para generar reporte de historia de login
  $ds_login_rpt = "&nbsp;&nbsp;&nbsp;<a href='historia_login_frm.php?clave=$clave'>Login record</a>";
  
  #Liga para generar reporte oficial para PCTIA
  $ds_pctia_rpt = "&nbsp;&nbsp;&nbsp;<a href='../reports/pctia_rpt.php?clave=$clave' target='blank'>".ObtenEtiqueta(534)."</a>";
  if(!empty($fe_completado))
    $ds_diploma = "&nbsp;&nbsp;&nbsp;<a href='../reports/diploma_rpt.php?clave=$clave' target='blank'>".ObtenEtiqueta(535)."</a>";
  else
    $ds_diploma = "&nbsp;";
  # super user
  $super_user = "&nbsp;&nbsp;&nbsp;<a href=\"javascript:super_user('$ds_login');\"'>".ObtenEtiqueta(808)."</a>";
  # Forma para captura de datos
  Forma_Inicia($clave, True);
  if($fg_error)
    Forma_PresentaError( );
  
  # Revisa si es un registro nuevo
  if(empty($clave)) {
    Forma_CampoTexto(ETQ_USUARIO, True, 'ds_login', $ds_login, 16, 16, $ds_login_err);
    Forma_CampoTexto(ObtenEtiqueta(123), True, 'ds_password', '', 16, 16, $ds_password_err, True);
    Forma_CampoTexto(ObtenEtiqueta(124), True, 'ds_password_conf', '', 16, 16, $ds_password_conf_err, True);
  }
  else {
    Forma_CampoInfo(ETQ_USUARIO, $ds_login.$ds_cambiar_pwd.$ds_login_rpt.$ds_pctia_rpt.$ds_diploma.$super_user);
    Forma_CampoOculto('ds_login' , $ds_login);
    Forma_CampoOculto('cl_sesion' , $cl_sesion);
  }
  Forma_Espacio( );
  
  # Contratos
  if($cl_type==4)
    $contratos = 3;
  else
    $contratos = 1;
  $enrol = False;
  for($i=1; $i<=$contratos; $i++) {
    if(!empty($fl_template)) {
      $ds_descarga = "<a href='../reports/documents_rpt.php?c=$fl_sesion&con=$i'>".ObtenEtiqueta(346)."</a>";
      if(empty($ds_cadena[$i]) || (!empty($ds_cadena[$i]) && empty($ds_firma_alumno[$i]))) {
        $ds_envia = "&nbsp;&nbsp;&nbsp;<a href='applications_snd.php?c=$fl_sesion&con=$i'>".ObtenEtiqueta(347)."</a>";
        $ds_firma = "";
        if($i==1)
          $enrol = False;
      }
      else {
        $ds_envia = "";
        $ds_firma = "&nbsp;&nbsp;&nbsp;<a href='view_contract.php?c=$fl_sesion&con=$i' target='_blank'>".ObtenEtiqueta(348)."</a>";
        if($i==1)
          $enrol = True;
      }
    }
    else {
      $ds_descarga = ObtenMensaje(213);
      $ds_envia = "";
      $ds_firma = "";
    }
       
    Forma_CampoInfo('Contract '.$i, $ds_descarga.$ds_envia.$ds_firma);
      
    $fe_temp = substr($ds_cadena[$i],0,8);
    if(!empty($fe_temp))
      $fe_envio = date("M j, Y",strtotime("$fe_temp"));
    else
      $fe_envio = "";
    Forma_CampoInfo(ObtenEtiqueta(597), $fe_envio); 
  }
  if(!empty($confirmacion))
    Forma_CampoInfo('', ObtenMensaje(212));
  if($error>0)
    Forma_Error(211);
  Forma_Espacio( );
  
  # Busca si existen alumnos con el mismo nombre y apellidos JGFL 26/01/2015
  $Query = "SELECT a.fl_usuario, CONCAT(a.ds_nombres,' ',a.ds_apaterno), c.nb_programa, nb_periodo FROM c_usuario a, k_ses_app_frm_1 b, c_programa c, c_periodo d ";
  $Query .= "WHERE a.ds_nombres='".$ds_fname."' AND a.ds_apaterno='".$ds_lname."' AND ds_login!='".$ds_login."'";
  $Query .= "AND a.cl_sesion=b.cl_sesion AND b.fl_programa = c.fl_programa AND b.fl_periodo=d.fl_periodo ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  # Si existe regitros mostrar tanto la seccion como la tabla de los studens homonimos
  if(!empty($registros)){
    Forma_Seccion(ObtenEtiqueta(693));
    $titulos = array(''.ObtenEtiqueta(360).'|center', ''.ETQ_NOMBRE.'|center',''.ObtenEtiqueta(382).'|center');
    $ancho_col = array('20%', '20%','10%','3%');
    Forma_Tabla_Ini('50%', $titulos, $ancho_col);
    for($i=0;$row=RecuperaRegistro($rs);$i++) {
      echo "
      <tr>
        <td><a href=\"javascript:otro('students_frm.php',$row[0]);\"'>".$row[2]."</a></td>
        <td><a href=\"javascript:otro('students_frm.php',$row[0]);\"'>".$row[1]."</a></td>
        <td><a href=\"javascript:otro('students_frm.php',$row[0]);\"'>".$row[3]."</a></td>
      </tr>";
    }
    Forma_Tabla_Fin();
  }
  
  
  # Obtener los correos de vanas y alumno
  $Query = "SELECT ds_email FROM c_usuario a, c_sesion b WHERE a.cl_sesion=b.cl_sesion AND b.fl_sesion=$fl_sesion ";
  $row = RecuperaValor($Query);
  $ds_emailto = $row[0];
  $ds_subject = ObtenEtiqueta(336);
  # Variable para el obtener el dialogo de envio de mensaje   
  Forma_Seccion('Communications History');
  Forma_CampoInfo('', "<a href='javascript:showDialog();'>Send Letter</a>");
  Forma_Espacio();
  # dialogo para el envio del template
  echo "
  <div  class='dialog' id='dialog' title='Email sent' style='display: none; font-size:14px;'>";
    $Query = "SELECT nb_template, fl_template FROM k_template_doc a ";
    $Query .= "WHERE fl_categoria=2 ORDER BY fl_template ASC";
    $rs = EjecutaQuery($Query);
    echo "
    <b>".ObtenEtiqueta(153).":</b> &nbsp;
    <select name='fl_template' id='fl_template' onChange='javascript:template();'>
      <option value='0'>--Select Option---</option>";
      for($i=0;$row=RecuperaRegistro($rs);$i++){
      echo "
        <option value='".$row[1]."'>".$row[0]."</option>";
      }
    echo "
    </select><br/>
    <input type='hidden' id='fl_sesion' name='fl_sesion' value='$fl_sesion'>
    <input type='hidden' id='programa' name='programa' value='".$programa."'>
    <input type='hidden' id='fl_alumno' name='fl_alumno' value='".$clave."'>
    <div id='ds_mensaje'></div>";
  echo "
    </div>";
    
  #Tabla de los templates enviados al alumno
  if(ExisteEnTabla('k_alumno_template', 'fl_alumno', $fl_sesion)){
    $titulos = array('Subject|center', 'Date|center','','');
    $ancho_col = array('15%', '12%','3%','3%');
    Forma_Tabla_Ini('33%', $titulos, $ancho_col);
    $Query  = "SELECT nb_template, fe_envio, a.fl_template, fl_alumno_template FROM k_template_doc a, k_alumno_template b ";
    $Query .= "WHERE a.fl_template=b.fl_template AND fl_alumno=$fl_sesion ORDER BY fe_envio DESC";
    $rs = EjecutaQuery($Query);
    for($i=0;$row=RecuperaRegistro($rs);$i++){
      if($i % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";
      echo "
      <tr class='$clase'>
        <td align='center'>".$row[0]."</td>
        <td align='center'>".$row[1]."</td>       
        <td><a href='viewemail.php?fl_alumno_template=".$row[3]."&fl_sesion=".$fl_sesion."'><img  src='".PATH_IMAGES."/icon_pdf.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>
        <td><a href=\"javascript:borrar_template('template_delete.php',$row[3],$fl_sesion,'$programa');\"><img  src='".PATH_IMAGES."/icon_delete.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>
      </tr>";
    }
    Forma_Tabla_Fin();
    Forma_Espacio(); 
  }
  
  Forma_CampoTexto(ObtenEtiqueta(117), True, 'ds_nombres', $ds_nombres, 100, 32, $ds_nombres_err);
  Forma_CampoTexto(ObtenEtiqueta(118), True, 'ds_apaterno', $ds_apaterno, 50, 32, $ds_apaterno_err);
  Forma_CampoTexto(ObtenEtiqueta(119), False, 'ds_amaterno', $ds_amaterno, 50, 32, '');
  $ruta = PATH_ALU_IMAGES."/id";
  Forma_CampoUpload(ObtenEtiqueta(810), '', 'ds_foto_oficial', $ds_foto_oficial,$ruta,True,'ds_foto_oficial',60, $ds_foto_oficial_err, 'jpg|jpeg');
  Forma_Espacio( );
  Forma_CampoInfo(ObtenEtiqueta(631), $ds_p_name);
  Forma_CampoInfo(ObtenEtiqueta(632), $ds_education_number);
  Forma_CampoCheckbox(ObtenEtiqueta(620),'fg_international',$fg_international);
  Forma_Espacio( );
  $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116)); // Masculino, Femenino
  $val = array('M', 'F');
  Forma_CampoSelect(ObtenEtiqueta(114), False, 'fg_genero', $opc, $val, $fg_genero);
  Forma_CampoTexto(ObtenEtiqueta(120).' '.ETQ_FMT_FECHA, False, 'fe_nacimiento', $fe_nacimiento, 10, 10, $fe_nacimiento_err);
  Forma_Calendario('fe_nacimiento');
  Forma_CampoTexto(ObtenEtiqueta(121), True, 'ds_email', $ds_email, 64, 32, $ds_email_err);
  Forma_CampoTexto(ObtenEtiqueta(127), True, 'ds_a_email', $ds_a_email, 64, 32, $ds_a_email_err);
  Forma_CampoTexto(ObtenEtiqueta(339), False, 'ds_link_to_portfolio', $ds_link_to_portfolio, 255, 32);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(540).' '.ETQ_FMT_FECHA, False, 'fe_carta', $fe_carta, 10, 10, $fe_carta_err);
  Forma_Calendario('fe_carta');
  Forma_CampoTexto(ObtenEtiqueta(541).' '.ETQ_FMT_FECHA, False, 'fe_contrato', $fe_contrato, 10, 10, $fe_contrato_err);
  Forma_Calendario('fe_contrato');
  Forma_Espacio( );
  Forma_CampoInfo(ObtenEtiqueta(110), $nb_perfil);
  Forma_CampoOculto('fl_perfil', $fl_perfil);
  Forma_CampoOculto('nb_perfil', $nb_perfil);
  Forma_CampoInfo(ObtenEtiqueta(426), $ds_grupo);
  Forma_CampoInfo(ObtenEtiqueta(297), $nb_maestro);
  Forma_CampoInfo(ObtenEtiqueta(617), $no_grado_actual);
  Forma_CampoOculto('fl_grupo', $fl_grupo);

  # Student Status 
  Forma_Seccion('Student Status');
  echo "
  <tr>
    <td colspan='2' class='css_etq_texto' aling='center' style='text-align:center;'>".
    str_uso_normal(ObtenMensaje(227))
    ."</td>
  </tr>";
  Forma_CampoCheckbox(ObtenEtiqueta(113), 'fg_activo', $fg_activo);
  Forma_CampoCheckbox(ObtenEtiqueta(558), 'fg_desercion', $fg_desercion);
  Forma_CampoCheckbox(ObtenEtiqueta(559), 'fg_dismissed', $fg_dismissed);
  Forma_CampoCheckbox(ObtenEtiqueta(644), 'fg_job', $fg_job);
  Forma_CampoCheckbox(ObtenEtiqueta(645), 'fg_graduacion', $fg_graduacion);
  Forma_CampoCheckbox(ObtenEtiqueta(547), 'fg_certificado', $fg_certificado);
  Forma_CampoCheckbox(ObtenEtiqueta(548), 'fg_honores', $fg_honores);
  Forma_Espacio( );

  Forma_CampoTextArea(ObtenEtiqueta(196), False, 'ds_notas', $ds_notas, 80, 3);
  Forma_Espacio( );
  
  # Estadisticas del usuario
  Forma_CampoInfo(ObtenEtiqueta(111), $fe_alta);
  Forma_CampoOculto('fe_alta', $fe_alta);
  Forma_CampoInfo(ObtenEtiqueta(112), $fe_ultacc);
  Forma_CampoOculto('fe_ultacc', $fe_ultacc);
  Forma_CampoInfo(ObtenEtiqueta(122), $no_accesos);
  Forma_CampoOculto('no_accesos', $no_accesos);
  Forma_Espacio( );

  Forma_Seccion(ObtenEtiqueta(621));
  // preferencias
  $campos = array(ObtenEtiqueta(622), ObtenEtiqueta(623), ObtenEtiqueta(616));
  $cl_preferences = array($cl_preference_1, $cl_preference_2, $cl_preference_3);
  for($i=0;$i<3;$i++){
    switch($cl_preferences[$i]) {    
      case 0: $preferencia2 = ' '; break;
      case 1: $preferencia2 = ObtenEtiqueta(624); break;
      case 2: $preferencia2 = ObtenEtiqueta(625); break;
      case 3: $preferencia2 = ObtenEtiqueta(626); break;
      case 4: $preferencia2 = ObtenEtiqueta(627); break;
      case 5: $preferencia2 = ObtenEtiqueta(628); break;
      case 6: $preferencia2 = ObtenEtiqueta(629); break;
      case 7: $preferencia2 = ObtenEtiqueta(630); break;
    }
    Forma_CampoInfo($campos[$i], $preferencia2);
  }
  
  # Datos de la forma de aplicacion
  Forma_Seccion(ObtenEtiqueta(55));
  Forma_CampoInfo(ObtenEtiqueta(340), $fe_ultmod);
  if($fg_paypal == '1')
    $ds_fg_paypal = ETQ_SI;
  else
    $ds_fg_paypal = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(343), $ds_fg_paypal);
  if($fg_pago == '1')
    $fg_pago = ETQ_SI;
  else
    $fg_pago = ETQ_NO;
  Forma_Campoinfo(ObtenEtiqueta(341), $fg_pago);
  Forma_Espacio( );
  
  # Datos del programa
  Forma_CampoInfo(ObtenEtiqueta(360), $nb_programa);
  Forma_CampoOculto('fl_programa' , $fl_programa);
  $Query  = "SELECT DISTINCT CONCAT(nb_periodo,' (',c.ds_duracion,')'), a.fl_periodo ";
  $Query .= "FROM k_term a, c_periodo b, c_programa c ";
  $Query .= "WHERE a.fl_periodo=b.fl_periodo AND a.fl_programa=c.fl_programa ";
  $Query .= "AND a.fl_programa=$fl_programa ";
  $Query .= "ORDER BY fe_inicio";
  Forma_CampoSelectBD(ObtenEtiqueta(342), True, 'fl_periodo', $Query, $fl_periodo);
  Forma_Espacio( );
  
  # Informacion de referencia
  switch($fg_ori_via) {
    case 'A': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(290)); break;
    case 'B': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(291)); break;
    case 'C': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(292)); break;
    case 'D': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(293)); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(294)." - $ds_ori_other"); break;
  }
  switch($fg_ori_ref) {
    case '0': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(17)); break;
    case 'S': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(296)." - $ds_ori_ref_name"); break;
    case 'T': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(297)." - $ds_ori_ref_name"); break;
    case 'G': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(298)." - $ds_ori_ref_name"); break;
  }
  Forma_Espacio( );
  
  # Datos del aplicante
  //jgfl 03-11-2014
  Forma_Seccion(ObtenEtiqueta(61));
  Forma_CampoTexto(ObtenEtiqueta(280),True,'ds_number', $ds_number,20,16, $ds_number_err );
  Forma_CampoTexto(ObtenEtiqueta(281), True,'ds_alt_number', $ds_alt_number, 20,16, $ds_alt_number_err);
  Forma_Espacio( );
  
  # Direccion
  //jgfl 03-11-2014
  Forma_Seccion(ObtenEtiqueta(62));
  Forma_CampoTexto(ObtenEtiqueta(282),True,'ds_add_number', $ds_add_number,20,10,$ds_add_number_err);
  Forma_CampoTexto(ObtenEtiqueta(283),True,'ds_add_street', $ds_add_street,20,50,$ds_add_street_err);
  Forma_CampoTexto(ObtenEtiqueta(284),True,'ds_add_city', $ds_add_city,20,50,$ds_add_city_err);
  // Forma_CampoTexto(ObtenEtiqueta(285),True,'ds_add_state', $ds_add_state,20,50,$ds_add_state_err);
  Forma_CampoTexto(ObtenEtiqueta(286),True,'ds_add_zip', $ds_add_zip,20,10,$ds_add_zip_err);
  $Query = "SELECT nb_pais, fl_pais FROM c_pais ";
  Forma_CampoSelectBD(ObtenEtiqueta(287), True, 'ds_add_country', $Query, $ds_add_country, $ds_add_country_err,True);
  # si hay error
  if(!empty($fg_error) AND ((empty($ds_add_state) AND $fg_provincia!=38) OR ($fg_provincia==38 AND $fl_provincia==0))){
    $ds_error = "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>".ObtenMensaje(ERR_REQUERIDO)."</td></tr>";
    $ds_clase = 'css_input_error';
  }
  else {
    $ds_clase = 'css_input';
    $ds_error = "";
  }
  # Muestra el campo  o las provincias de canada
  echo "
  <tr>
    <td class='css_prompt' align='right' valign='middle'>* ".ObtenEtiqueta(285)."</td>
    <td align='left' valign='middle'>
      <input type='hidden' id='fg_provincia' name='fg_provincia'>
      <!--Campo de texto para las provincias-->
      <input class='$ds_clase' id='ds_add_state' name='ds_add_state' value='$ds_add_state' maxlength='50' size='32' type='text'>
      <!--Select para las provincias de canada -->
      <select class='$ds_clase' id='fl_provincia' name='fl_provincia'>
        <option value=0>".ObtenEtiqueta(70)."</option>";
        $Query  = "SELECT ds_provincia, fl_provincia FROM k_provincias WHERE fl_pais=38 ORDER BY ds_provincia";
        $rs = EjecutaQuery($Query);
        for($i=0;$row = RecuperaRegistro($rs);$i++){        
          $ds_provincia = $row[0];
          $fl_provincia_o = $row[1];
          if($fl_provincia_o==$fl_provincia)
            $selected = "selected";
          else
            $selected = "";
          echo "<option ".$selected." value='".$fl_provincia_o."' >".$ds_provincia."</option>";
        }
  echo "
        </select>        
    </td>
  </tr>$ds_error";
  Forma_Espacio( );
  
  echo "
  <script>
  $(document).ready(
    function(){
      var country = ".$ds_add_country.";
      if(country==38){
        $('#fl_provincia').css('display','inline');
        $('#ds_add_state').css('display','none');
        $('#fg_provincia').val(38);
      }
      else{
        $('#ds_add_state').css('display','inline');        
        $('#fl_provincia').css('display','none');
        $('#fg_provincia').val(0);
      }
      $('#ds_add_country').change(
        function(){
          if($(this).val()==38){
          $('#fl_provincia').css('display','inline');
          $('#ds_add_state').css('display','none');
          $('#fg_provincia').val(38);
          }
          else{
            $('#ds_add_state').css('display','inline');        
            $('#fl_provincia').css('display','none');
            $('#fg_provincia').val(0);            
          }
        }
      );
    }
  );
  </script>";
  
  # Direccion de envio de correspondencia
  Forma_Seccion(ObtenEtiqueta(633));
  Forma_CampoTexto(ObtenEtiqueta(282),False,'ds_m_add_number', $ds_m_add_number,20,10);
  Forma_CampoTexto(ObtenEtiqueta(283),False,'ds_m_add_street', $ds_m_add_street,50,50);
  Forma_CampoTexto(ObtenEtiqueta(284),False,'ds_m_add_city', $ds_m_add_city,50,50);
  Forma_CampoTexto(ObtenEtiqueta(285),False,'ds_m_add_state', $ds_m_add_state,50,50);
  Forma_CampoTexto(ObtenEtiqueta(286),False,'ds_m_add_zip', $ds_m_add_zip,20,10);
  $Query = "SELECT nb_pais, fl_pais FROM c_pais ";
  Forma_CampoSelectBD(ObtenEtiqueta(287), False, 'ds_m_add_country', $Query, $ds_m_add_country, '',True);
  Forma_Espacio( );
  
  # Contacto de emergencia
  Forma_Seccion(ObtenEtiqueta(63));
  Forma_CampoInfo(ObtenEtiqueta(117), $ds_eme_fname);
  Forma_CampoInfo(ObtenEtiqueta(118), $ds_eme_lname);
  Forma_CampoInfo(ObtenEtiqueta(280), $ds_eme_number);
  Forma_CampoInfo(ObtenEtiqueta(288), $ds_eme_relation);
  Forma_CampoInfo(ObtenEtiqueta(287), $ds_eme_country);
  Forma_Espacio( );
  
  # Career Assessment
  Forma_Seccion(ObtenEtiqueta(56));
  Forma_CampoInfo(ObtenEtiqueta(301), $ds_resp2_1);
  Forma_CampoInfo(ObtenEtiqueta(302), $ds_resp2_2);
  Forma_CampoInfo(ObtenEtiqueta(303), $ds_resp2_3);
  Forma_CampoInfo(ObtenEtiqueta(304), $ds_resp2_4);
  Forma_CampoInfo(ObtenEtiqueta(305), $ds_resp2_5);
  Forma_CampoInfo(ObtenEtiqueta(306), $ds_resp2_6);
  Forma_CampoInfo(ObtenEtiqueta(307), $ds_resp2_7);
  Forma_Espacio( );
  
  # Computer Skills Assessment
  Forma_Seccion(ObtenEtiqueta(78));
  Forma_Seccion(ObtenEtiqueta(79), False);
  switch($fg_resp4_1_1) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(82), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(82), ETQ_NO); break;
  }
  switch($fg_resp4_1_2) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(83), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(83), ETQ_NO); break;
  }
  switch($fg_resp4_1_3) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(84), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(84), ETQ_NO); break;
  }
  switch($fg_resp4_1_4) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(85), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(85), ETQ_NO); break;
  }
  switch($fg_resp4_1_5) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(86), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(86), ETQ_NO); break;
  }
  switch($fg_resp4_1_6) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(87), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(87), ETQ_NO); break;
  }
  Forma_Seccion(ObtenEtiqueta(80), False);
  switch($fg_resp4_2_1) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(88), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(88), ETQ_NO); break;
  }
  switch($fg_resp4_2_2) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(89), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(89), ETQ_NO); break;
  }
  switch($fg_resp4_2_3) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(90), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(90), ETQ_NO); break;
  }
  switch($fg_resp4_2_4) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(91), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(91), ETQ_NO); break;
  }
  switch($fg_resp4_2_5) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(92), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(92), ETQ_NO); break;
  }
  switch($fg_resp4_2_6) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(93), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(93), ETQ_NO); break;
  }
  switch($fg_resp4_2_7) {
    case '1': Forma_CampoInfo(ObtenEtiqueta(94), ETQ_SI); break;
    case '0': Forma_CampoInfo(ObtenEtiqueta(94), ETQ_NO); break;
  }
  Forma_Seccion(ObtenEtiqueta(81), False);
  switch($fg_resp4_3_1) {
    case '0': Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(97)); break;
    case '1': Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(98)); break;
    case '2': Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(99)); break;
    case '3': Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(107)); break;
  }
  switch($fg_resp4_3_2) {
    case '0': Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(97)); break;
    case '1': Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(98)); break;
    case '2': Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(99)); break;
    case '3': Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(107)); break;
  }
  Forma_Espacio( );
  
  # Expectations Questionnaire
  Forma_Seccion(ObtenEtiqueta(57));
  Forma_CampoInfo(ObtenEtiqueta(308), $ds_resp3_1);
  Forma_Prompt(ObtenEtiqueta(309));
  Forma_CampoInfo("1", $ds_resp3_2_1);
  Forma_CampoInfo("2", $ds_resp3_2_2);
  Forma_CampoInfo("3", $ds_resp3_2_3);
  Forma_CampoInfo(ObtenEtiqueta(310), $ds_resp3_3);
  Forma_CampoInfo(ObtenEtiqueta(311), $ds_resp3_4);
  Forma_CampoInfo(ObtenEtiqueta(312), $ds_resp3_5);
  switch($ds_resp3_6) {
    case 'A': Forma_CampoInfo(ObtenEtiqueta(313), ObtenEtiqueta(314)); break;
    case 'B': Forma_CampoInfo(ObtenEtiqueta(313), ObtenEtiqueta(315)); break;
    case 'C': Forma_CampoInfo(ObtenEtiqueta(313), ObtenEtiqueta(316)); break;
  }
  switch($ds_resp3_7) {
    case 'A': Forma_CampoInfo(ObtenEtiqueta(317), ObtenEtiqueta(318)); break;
    case 'B': Forma_CampoInfo(ObtenEtiqueta(317), ObtenEtiqueta(319)); break;
    case 'C': Forma_CampoInfo(ObtenEtiqueta(317), ObtenEtiqueta(320)); break;
    case 'D': Forma_CampoInfo(ObtenEtiqueta(317), ObtenEtiqueta(321)); break;
    case 'E': Forma_CampoInfo(ObtenEtiqueta(317), ObtenEtiqueta(322)); break;
  }
  Forma_CampoInfo(ObtenEtiqueta(323), $ds_resp3_8);
  Forma_Espacio( );
  
  # Configuracion personal
  Forma_Seccion(ObtenEtiqueta(410));
  Forma_CampoOculto('fl_zona_horaria', $fl_zona_horaria);
  Forma_CampoInfo(ObtenEtiqueta(411), $ds_zona_horaria);
  Forma_CampoPreview(ObtenEtiqueta(412), 'ds_ruta_avatar', $ds_ruta_avatar, PATH_ALU_IMAGES."/avatars", False, False);
  Forma_CampoPreview(ObtenEtiqueta(413), 'ds_ruta_foto', $ds_ruta_foto, PATH_ALU_IMAGES."/pictures", False, False);
  Forma_CampoInfo(ObtenEtiqueta(414), $ds_website);
  Forma_CampoInfo(ObtenEtiqueta(415), $ds_gustos);
  Forma_CampoInfo(ObtenEtiqueta(416), $ds_pasatiempos);
  Forma_Espacio( );
  
  # Recupera el tipo de pago para el curso
  $Query  = "SELECT fg_opcion_pago ";
  $Query .= "FROM k_app_contrato ";
  $Query .= "WHERE cl_sesion='$cl_sesion'"; 
  $row = RecuperaValor($Query);
  $fg_opcion_pago = $row[0];
  $titulos = array(ObtenEtiqueta(375).'|center',ObtenEtiqueta(481).'|center', ObtenEtiqueta(485).'|center', ObtenEtiqueta(486).'|center',
                   ObtenEtiqueta(374).'|center', ObtenEtiqueta(596).'|center',ObtenEtiqueta(741),ObtenEtiqueta(742),ObtenEtiqueta(743), 
                   ObtenEtiqueta(483).'|center', ObtenEtiqueta(72),'','','');
  $ancho_col = array('10%','5%', '10%', '10%', '15%', '15%', '15%', '20%','5%', '5%');
  
  switch($fg_opcion_pago) {
    case 1: $mn_due='ds_a_freq'; $ds_pagos='no_a_payments'; break;
    case 2: $mn_due='ds_b_freq'; $ds_pagos='no_b_payments'; break;
    case 3: $mn_due='ds_c_freq'; $ds_pagos='no_c_payments'; break;
    case 4: $mn_due='ds_d_freq'; $ds_pagos='no_d_payments'; break;
  }
  $Query = "SELECT $mn_due, $ds_pagos FROM k_programa_costos WHERE fl_programa=$fl_programa ";
  $row = RecuperaValor($Query);
  $frecuencia = $row[0];
  $no_pagos_opcion = $row[1];
  
  # Datos de los pagos del alumno
  Forma_Seccion(ObtenEtiqueta(690));
  Forma_Espacio( );
  Forma_CampoInfo(ObtenEtiqueta(482), $frecuencia);
  Forma_Espacio( );
  Forma_Tabla_Ini('95%', $titulos, $ancho_col);
  switch($fg_opcion_pago) {
    case 1: $mn_due='mn_a_due'; break;
    case 2: $mn_due='mn_b_due'; break;
    case 3: $mn_due='mn_c_due'; break;
    case 4: $mn_due='mn_d_due'; break;
  }
  
  //para obtener informacion del pago de app_fee
  $Query  = "SELECT fl_sesion,  CASE cl_metodo_pago WHEN 1 THEN 'Paypal' WHEN 2 THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' ";
  $Query .= "WHEN 6 THEN 'Cash' END cl_metodo_pago, (CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago, mn_pagado, ";
  $Query .= "". ConsultaFechaBD('b.fe_ultmod',FMT_FECHA) .", ds_comentario ds_comentario_app ";
  $Query .= "FROM c_sesion a, k_ses_app_frm_1 b ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion AND a.cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $cl_metodo_app = $row[1];
  $fe_pago_app = $row[2];
  $mn_pagado_app = $row[3];
  $fe_ultmod1 = str_texto($row[4]);
  $ds_comentario_app = str_texto($row[5]);

  echo "
  <tr style='font-weight:bold;' align='center'>
    <td>App fee</td>
    <td>Once</td>
    <td>$fe_ultmod1</td>
    <td>$mn_pagado_app</td>
    <td>$fe_pago_app</td>
    <td>$mn_pagado_app</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>$cl_metodo_app</td>
    <td align='left'>$ds_comentario_app</td>
    <td><a href='".PATH_CAMPUS."/students/invoice.php?fl_sesion=$fl_sesion&destino=payments_frm.php' target='_blank'><img src='".PATH_IMAGES."/icon_pdf.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>
    <td></td>";
  echo "
  </tr>";
  
  # Recupera informacion de los pagos realizados
  $fe_actual = Date('Y-m-d');//fecha actual con formato Y-m-d
  $concat = array(ConsultaFechaBD('a.fe_pago', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_pago', FMT_HORA)); // formato de la fecha en que pago
  $Query_pagado  = "SELECT  a.fl_term_pago, b.no_opcion, b.no_pago, ".ConsultaFechaBD('b.fe_pago', FMT_FECHA).",(SELECT $mn_due FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1'), ";
  $Query_pagado .= "CASE a.cl_metodo_pago ";
  $Query_pagado .= "WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' ";
  $Query_pagado .= "END ds_metodo_pago, ";
  $Query_pagado .= " ".ConcatenaBD($concat).", a.mn_pagado, a.ds_comentario, a.fl_alumno_pago, a.cl_metodo_pago, a.fg_refund,DATEDIFF(a.fe_pago, '$fe_actual') no_dias, ";
  $Query_pagado .= "(SELECT SUM(o.mn_pagado) FROM k_alumno_pago_det o WHERE o.fl_alumno_pago=a.fl_alumno_pago AND o.fg_earned='1') earned, ";
  $Query_pagado .= "(SELECT SUM(o.mn_pagado) FROM k_alumno_pago_det o WHERE o.fl_alumno_pago=a.fl_alumno_pago AND o.fg_earned='0') unearned, ";
  $Query_pagado .= "CONCAT((SELECT COUNT(*) FROM k_alumno_pago_det o WHERE o.fl_alumno_pago=a.fl_alumno_pago AND o.fg_earned='1'),'/',
  (SELECT COUNT(*) FROM k_alumno_pago_det o WHERE o.fl_alumno_pago=a.fl_alumno_pago)) e_u,
  (SELECT nb_periodo FROM c_periodo r, k_term t WHERE r.fl_periodo=t.fl_periodo AND t.fl_term=b.fl_term) terms ";      
  $Query_pagado .= "FROM k_alumno_pago a, k_term_pago b ";
  $Query_pagado .= "WHERE a.fl_term_pago = b.fl_term_pago AND a.fl_alumno=$clave ORDER BY b.fe_pago ";
  $rs = EjecutaQuery($Query_pagado);
  for($i=0; $row = RecuperaRegistro($rs); $i++) {
    $fl_term_pago_p = $row[0];
    $no_opcion_p = $row[1];
    $no_pago_p = $row[2];
    $fe_limite_pago_p = $row[3];
    $mn_pago_p = $row[4];
    $ds_metodo_pago_p = $row[5];
    $fe_pago_p = $row[6];
    $mn_pagado_p = $row[7];
    $ds_comentario_det_p = $row[8];
    $fl_alumno_pago_p = $row[9];
    $cl_metodo_pago_det_p = $row[10];
    $fg_refund_p = $row[11];
    $no_dias = $row[12];
    $earned = Number_format(round($row[13]),2,'.',',');
    $unearned = Number_format(round($row[14]),2,'.',',');
    $e_u = $row[15];
    $terms = $row[16];
    
    # Validamos si el fg_refund ya se realizo no se podra volver a realizar y estara en colo rojo
    if(empty($fg_refund_p))
      $onclick = "<a href='javascript:realizar_refund($clave,$pago_final,$fg_inscrito,$no_pago_p);'>Refund</a>";
     #fg_refund pagos que se regresan
    if($cl_metodo_pago_det_p >0 AND empty($fg_refund_p) AND $pago_final == $fl_alumno_pago_p)
      $refund = "<td>$onclick </td>";
    else{
      $refund = "<td> </td>";
      if(!empty($fg_refund_p))
        $refund = "<td style='color:red; font-weight:bold;'>Refund</td>";
    }
    
    #ultimo pago
    if($pago_final == $fl_alumno_pago_p AND $cl_metodo_pago_det_p > 0)       
      $borrar = "<td><a href=javascript:pago('borrar_payment.php',$clave,$pago_final,$fg_app_frm);><img src=".PATH_HOME."/images/icon_delete.gif title='Delete last payment'></a></td>";
    else
      $borrar = "<td></td>";
    
    if($i % 2 == 0)
      $clase = "css_tabla_detalle";
    else
      $clase = "css_tabla_detalle_bg";
    $numero_pago = $i + 1;
    echo "
    <tr class='$clase' align='center'>
      <td>$terms</td>
      <td>$numero_pago</td>
      <td>$fe_limite_pago_p</td>
      <td>$mn_pago_p</td>
      <td>$fe_pago_p</td>
      <td>$mn_pagado_p</td>
      <td>$earned</td>
      <td>$unearned</td>
      <td>$e_u</td>
      <td>$ds_metodo_pago_p</td>
      <td align='left'>$ds_comentario_det_p</td>
      <td><a href='".PATH_CAMPUS."/students/invoice.php?f=$fl_term_pago_p&pago=$no_pago_p&destino=payments_frm.php&fl_sesion=$fl_sesion&n_pago=$numero_pago'><img src='".PATH_IMAGES."/icon_pdf.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>";
      echo " 
      ".$borrar."
      ".$refund."
    </tr>";
    $pagos_realizados++;
  }
  
  # Verificamos si repitio el grado
  $Query3 = "SELECT no_grado FROM k_alumno_term a, k_term b WHERE a.fl_term=b.fl_term and fl_alumno=$clave  ";
  $rs3 = EjecutaQuery($Query3);
  $r = '';
  $repetido = 0 ;
  for($i=0; $row3=RecuperaRegistro($rs3); $i++){
    if($r == $row3[0])
      $repetido++;
    $no_grado_re = $row3[0];
    $r = $no_grado_re;
  }
  
  # Datos para el Payment history
  # Recupera el term inicial cuando se incribio y term inicial actual
  $row_ini = RecuperaValor("SELECT MIN(fl_term) FROM k_alumno_term WHERE fl_alumno=$clave");
  $term_ini = $row_ini[0];
  $row_ic = RecuperaValor("SELECT CASE fl_term_ini WHEN 0 THEN fl_term ELSE fl_term_ini END term_ini_act FROM k_term WHERE fl_term=(SELECT MAX(fl_term) FROM k_alumno_term WHERE fl_alumno=$clave)");
  $term_ini_act = $row_ic[0];
  if($term_ini_act==$term_ini)
    $fl_term_ini = $term_ini;
  else{               
    $fl_term_ini = $term_ini_act;
    # Obtenemos el total de pagos y numeros de term para identificar los meses que cubre un term
    $row1 = RecuperaValor("SELECT no_grados, $ds_pagos FROM c_programa a, k_programa_costos k WHERE a.fl_programa=k.fl_programa AND a.fl_programa =$fl_programa");
    $no_grados = $row1[0];
    $no_x_payments = $row1[1];
    $meses_sumados = round(($no_semanas/4)/$no_grados);
    if($repetido>0){
      $meses_x_term = ($no_x_payments/$no_grados)*$repetido;
      $meses_sumados = round(($no_semanas/4)/$no_grados)*$repetido;
    }
    else
      $meses_x_term = round(($no_semanas/4)/$no_grados);    
    if($fg_opcion_pago==1 AND $no_x_payments==1){      
      $meses_x_term = round(($no_semanas/4)/$no_grados);
    }
    # Obtenemos el total se pagos realizados y si recursa un term tendran que haber pagos extras de los 
    $pagos_extras = "AND no_pago>$pagos_realizados-$meses_x_term ";
  }
  
  # Datos de pagos que no se han realizado 
  $Query  = "SELECT fl_term_pago, no_opcion, no_pago, ".ConsultaFechaBD('fe_pago', FMT_FECHA)." , $mn_due, DATEDIFF(a.fe_pago, '$fe_actual') no_dias ";
  $Query .= ", (SELECT nb_periodo FROM c_periodo l, k_term s WHERE l.fl_periodo=s.fl_periodo AND s.fl_term=a.fl_term) terms ";
  $Query .= "FROM k_term_pago a, k_app_contrato b WHERE fl_term=$fl_term_ini ";
  $Query .= "AND no_opcion=$fg_opcion_pago AND no_contrato=1 AND cl_sesion='$cl_sesion' $pagos_extras ";
  $Query .= "ORDER BY no_pago ";
  $rs = EjecutaQuery($Query);
  for($i=0; $row = RecuperaRegistro($rs); $i++) {
    $fl_term_pago = $row[0];
    $no_opcion = $row[1];
    $no_pago = $row[2];
    if($term_ini_act!=$term_ini){
      if($repetido>0)
        $no_pago_lista = ($pagos_realizados + 1) + $i;
      else
        $no_pago_lista = $pagos_realizados + $i;
    }
    else
      $no_pago_lista = $no_pago;
    $fe_limite_pago = $row[3];
    $mn_pago = $row[4];
    $no_dias = $row[5];
    $terms = $row[6];

    //para obtener los pagos
    $concat = array(ConsultaFechaBD('fe_pago', FMT_FECHA), "' '", ConsultaFechaBD('fe_pago', FMT_HORA));
    $Query  = "SELECT fl_term_pago, ";
    $Query .= "CASE cl_metodo_pago ";
    $Query .= "WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' ";
    $Query .= "END ds_metodo_pago, ";
    $Query .= "(".ConcatenaBD($concat).") fe_pago, mn_pagado, ds_comentario, fl_alumno_pago, cl_metodo_pago,fg_refund ";
    $Query .= "FROM k_alumno_pago a ";
    $Query .= "WHERE fl_term_pago=$fl_term_pago ";
    $Query .= "AND fl_alumno=$clave";
 
    $row = RecuperaValor($Query);
    $fl_t_pago = $row[0];
    $ds_metodo_pago = $row[1];
    if(empty($ds_metodo_pago)) 
      $ds_metodo_pago = "(To be paid)";
    $fe_pago = $row[2];
    if(empty($fe_pago)) 
      $fe_pago = "(To be paid)";
    $mn_pagado = $row[3];
    if(empty($mn_pagado)) 
      $mn_pagado = "(To be paid)";
    $ds_comentario_det = str_uso_normal($row[4]);
    $fl_alumno_pago = $row[5];
    $cl_metodo_pago_det = $row[6];
    $fg_refund = $row[7];
    
      
    if(empty($fl_t_pago)) {
      if(empty($proximo_pago)) {
        $pinta_pdf=false;
        $proximo_pago=$fl_term_pago;
        $no_opcion_pagar=$no_opcion;
        $no_pago_pagar=$no_pago;
        $fe_limite_pago_pagar=$fe_limite_pago;
        # Validamos si los dias son menores 0(paso fecha) paga late fee, si son mayores o igual a  0 pago normal
        if($no_dias < 0)
          $late_fee = ObtenConfiguracion(66);
        $mn_due_pagar=$mn_pago;
      }
    }
    else
      $pinta_pdf = true;
      
    if($i % 2 == 0)
      $clase = "css_tabla_detalle";
    else
      $clase = "css_tabla_detalle_bg";  

    # Para los alumnos incritos solo obtendra los pagos que hacen faltan por pagar
    # Para los alumnos que no se han inscrito obtendra tanto pagos realizados como lo que hacen falta
    if(empty($fl_t_pago)){
      echo "
      <tr class='$clase' align='center'>
        <td>$terms</td>
        <td>$no_pago_lista</td>
        <td>$fe_limite_pago</td>
        <td>$mn_pago</td>
        <td>$fe_pago</td>
        <td>$mn_pagado</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>$ds_metodo_pago</td>
        <td align='left'>$ds_comentario_det</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>";
    }
  }  
  Forma_Tabla_Fin( );
  Forma_Espacio( );
  
  # Vaidamos que se puedan generan taxes
  if(!empty($fg_taxes)){
    # Inicia tabla de los T220a
    Forma_Seccion('<div align="center">'.ObtenEtiqueta(692).'</div>');
    echo "
    <tr>
      <td colspan='2' class='css_etq_texto' aling='center' style='text-align:center;'>".
      str_uso_normal(ObtenMensaje(228))
      ."</td>
    <tr>";
    Forma_Espacio();
    $titulos = array(ObtenEtiqueta(360),'Year','Initial month', 'Final month',ObtenEtiqueta(583).'|center','');
    $ancho_col = array('20%','20%','20%','20%','5%');
    Forma_Tabla_Ini('85%', $titulos,$ancho);
    
    # Le sumamos lo numero de meses a la fecha inicial para obtener el fecha final
    # Calculamos la cantidad que se paga por mes
    $fe_inicio1 = DATE_FORMAT(date_create($fe_inicio_term),'Y-m-d');
    $mes_inicio1 = DATE_FORMAT(date_create($fe_inicio_term),'m');
    $anio_inicio1 = DATE_FORMAT(date_create($fe_inicio_term),'Y');
    $meses = ($no_semanas/4);
    $fe_nueva = strtotime ( '+ '.($meses-1).' month' , strtotime ( $fe_inicio1 ) ) ;
    $fe_fin1 = date ( 'Y-m-d' , $fe_nueva );
    $mes_fin1 = date ( 'm' , $fe_nueva );
    $anio_fin1 = date ( 'Y' , $fe_nueva );
    $anios1 = $anio_fin1 - $anio_inicio1;
    for($i=0;$i<=$anios1;$i++){
      $anios2=$anio_inicio1+$i;
      if($anios2<date('Y')){
        # Obtiene los meses que conforman el anio para el que se pago 
        if($anio_inicio1==$anio_fin1)
          $num_meses_anio=$mes_fin1-$mes_inicio1+1;
        else{
          $num_meses_anio = 12;
          if($anios2==$anio_fin1)
            $num_meses_anio = $mes_fin1;
          if($anios2==$anio_inicio1)
            $num_meses_anio = 12-$mes_inicio1+1;
        }
        
        # Obtenemos los meses que cubren lo pagos
        # Obtenemos su nombre para mostrarlos en la tabla
        if($anios2==$anio_inicio1){
          if($anio_inicio1==$anio_fin1){
            $mes_ini= $mes_inicio1;
            $mes_fin= $mes_fin1;
          }
          else{
            $mes_ini =$mes_inicio1;
            $mes_fin=12;
          }
        }
        else {
          $mes_ini =1;
          $mes_fin=$mes_fin1;
          if($anios2 != $anio_fin1)
            $mes_fin=12;
        }
        
        # Si el alumno se retiro antes de acabar el curso
        # Obtenemos el ultimo pago y hasta ahi sumamos las cantidades
        if(!empty($fg_desercion) AND ($anios2!=$anio_fin1 AND $anios2!=$anio_inicio1)){
          $Query = "SELECT DATE_FORMAT(fe_pago,'%m') FROM k_alumno_pago WHERE fl_alumno=$clave AND DATE_FORMAT(fe_pago, '%Y')='$anios2' order by fe_pago DESC ";
          $row = RecuperaValor($Query);
          $num_meses_anio = $row[0];
          $mes_fin = $row[0];
        }
        
        # Monto pagado en el anio
        $monto = ($mn_pago / ($meses/$no_pagos_opcion)) * $num_meses_anio;
        $monto = number_format($monto,2,'.',',');
        
        $mes_ini = ObtenNombreMes($mes_ini);
        $mes_fin = ObtenNombreMes($mes_fin);
        # Datos de los taxes
        echo "
        <tr>
          <td>".$nb_programa."</td>
          <td>".$anios2."</td>
          <td>".$mes_ini."</td>
          <td>".$mes_fin."</td>
          <td align='center'>".$monto."</td>
          <td><a href='".PATH_CAMPUS."/students/taxes.php?anio=$anios2&fl_alumno=$clave&fl_term=$fl_term&num_meses_anio=$num_meses_anio&monto=$monto' target='_blank'><img src='".PATH_IMAGES."/icon_pdf.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>
        </tr>";
      }
    }
    
    Forma_Tabla_Fin();
    Forma_Espacio();
  }

  
  # Vamos a asumar las fecha fin del curso si es que repitio term y cambio de term 
  if($repetido >= 1 || $term_ini_act != $term_ini){
    $fe_inicio_term = date_format(date_create($fe_inicio_term),'Y-m-d');
    $rowm = RecuperaValor("SELECT DATE_ADD('$fe_inicio_term', INTERVAL ($meses + $meses_sumados) MONTH)");
    $fe_fin = date_format(date_create($rowm[0]),'d-m-Y');
    $fe_completado = $fe_fin;
  }

  #Datos del Official Transcipt
  Forma_Seccion(ObtenEtiqueta(543));
  Forma_CampoInfo(ObtenEtiqueta(382), $fe_inicio);
  Forma_CampoTexto(ObtenEtiqueta(544).' '.ETQ_FMT_FECHA, False, 'fe_fin', $fe_fin, 10, 10, $fe_fin_err);
  Forma_Calendario('fe_fin');
  Forma_CampoTexto(ObtenEtiqueta(545).' '.ETQ_FMT_FECHA, False, 'fe_completado', $fe_completado, 10, 10, $fe_completado_err);
  Forma_Calendario('fe_completado');
  Forma_CampoTexto(ObtenEtiqueta(556).' '.ETQ_FMT_FECHA, False, 'fe_graduacion', $fe_graduacion, 10, 10, $fe_graduacion_err);
  Forma_Calendario('fe_graduacion');
  Forma_CampoTexto(ObtenEtiqueta(546).' '.ETQ_FMT_FECHA, False, 'fe_emision', $fe_emision, 10, 10, $fe_emision_err);
  Forma_Calendario('fe_emision');
  Forma_Espacio( );
  
  # Calificaciones
  Forma_Seccion(ObtenEtiqueta(549));
  # Presenta datos los cursos impartidos
  echo "
    <tr>
      <td colspan='2'>
        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
          <tr>
            <td colspan='6' >
              &nbsp;
            </td>
          </tr>
          <tr>
            <td width='10%' align='center' class='css_caja'>
              ".ObtenEtiqueta(550)."
            </td>
            <td width='40%' align='center' class='css_caja'>
              ".ObtenEtiqueta(551)."
            </td>
            <td width='15%' align='center' class='css_caja'>
              ".ObtenEtiqueta(557)."
            </td>
            <td width='10%' align='center' class='css_caja'>
              ".ObtenEtiqueta(428)."
            </td>
            <td width='19%' align='center' class='css_caja'>
              ".ObtenEtiqueta(552)."
            </td>
            <td width='6%' align='center' class='css_caja'>
              ".ObtenEtiqueta(553)."
            </td>
          </tr>
  ";

  for($i = 0; $i < $tot_grados; $i++) {
    echo "
          <tr>
            <td colspan='6' >
              &nbsp;
            </td>
          </tr>
          <tr>
            <td colspan='6' align='center' class='css_prompt'>
              Term $no_grado[$i] 
            </td>
          </tr>
    ";
    $adicionales = 0;
    $factor_promedio_g = 0;
    $suma_cal_g = 0;  
    for($j = 0; $j < $tot_lecciones[$i]; $j++) {
      if(!empty($no_semana[$i][$j])) {
        if($j % 2 != 0)
          $clase = 'css_tabla_detalle';
        else
          $clase = 'css_tabla_detalle_bg';
        
        $Query  = "SELECT fl_clase, ".ConsultaFechaBD('fe_clase', FMT_CAPTURA)." fe_clase, ";
        $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN)." hr_clase, fg_obligatorio, fg_adicional, b.fl_entrega_semanal ";
        $Query .= "FROM k_clase a, k_entrega_semanal b ";
        $Query .= "WHERE a.fl_semana=b.fl_semana ";
        $Query .= "AND a.fl_grupo=b.fl_grupo ";
        $Query .= "AND b.fl_alumno=$clave ";
        $Query .= "AND a.fl_semana=".$fl_semana[$i][$j]." ";
        $Query .= "ORDER BY fl_clase ";
        $cons = EjecutaQuery($Query);
        while($row2 = RecuperaRegistro($cons)) {
          $fl_clase[$i][$j] = $row2[0];
          if(!empty($row2[1])) { # Ya se habia puesto una fecha para la clase
            $fe_clase[$i][$j] = $row2[1];
            $hr_clase[$i][$j] = $row2[2];
          }
          $fg_obligatorio[$i][$j] = $row2[3];
          $fg_adicional[$i][$j] = $row2[4];
          
          if($fg_adicional[$i][$j] == '1') {
            $adicionales++;
            $no_semana[$i][$j] = '';
            $ds_titulo[$i][$j] = ObtenEtiqueta(538);
            $row[0]= '';
          }
          else {
            # Revisa si hay calificacion para el alumno en esta leccion
            $Query  = "SELECT b.cl_calificacion, b.ds_calificacion, b.fg_aprobado, b.no_equivalencia ";
            $Query .= "FROM k_entrega_semanal a, c_calificacion b ";
            $Query .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
            $Query .= "AND a.fl_alumno=$clave ";
            $Query .= "AND a.fl_semana=".$fl_semana[$i][$j];
            $row = RecuperaValor($Query);            
          }
          
          # Consulta el estatus de asistencia a live session
          $Query = "SELECT a.fl_live_session, a.fl_usuario, b.nb_estatus, d.fl_semana
                    FROM k_live_session_asistencia a, c_estatus_asistencia b, k_live_session c, k_clase d
                    WHERE a.cl_estatus_asistencia = b.cl_estatus_asistencia
                    AND a.fl_live_session = c.fl_live_session
                    AND c.fl_clase = d.fl_clase
                    AND c.fl_clase = ".$fl_clase[$i][$j]." 
                    AND d.fl_semana = ".$fl_semana[$i][$j]."
                    AND a.fl_usuario = $clave";
          $rasis = RecuperaValor($Query);
        
          switch($fg_obligatorio[$i][$j]) {
          case '0':
            $obliga = ETQ_NO;
          break;
          case '1':
            $obliga = ETQ_SI;
          break;
          default:
            $obliga = '';
          }
          
          echo "
              <tr>
                <td width='10%' align='center' class=$clase>
                  ".$no_semana[$i][$j]."
                </td>
                <td width='40%' class=$clase>
                  ".$ds_titulo[$i][$j]."
                </td>
                <td width='15%' align='center' class=$clase>
                  ".$fe_clase[$i][$j]." ";
                  # Tomamos la fecha de la ultima clase para poder sumar das
                  if($j==$tot_lecciones[$i]-1 AND $fg_obligatorio[$i][$j]==1){
                    # Sumamos  los dias a la fecha de la ultima clase
                    $fe_ult_class = date('Y-m-d',strtotime('+ '.ObtenConfiguracion(74).' day '.$fe_clase[$i][$j].''));
                    echo "<input type='hidden' name='fe_ult_class' id='fe_ult_class' value='".$fe_ult_class."'>";
                  }
          echo "
                </td>
                <td width='10%' align='center' class=$clase>
                  ".$obliga."
                </td>
                <td width='19%' align='center' class=$clase>";
                
          if(!empty($rasis[0])) {
            echo "
                $rasis[2]";
          }
          else {
            $fecha_clase = RecuperaValor("SELECT fe_clase FROM k_clase WHERE fl_semana = ".$fl_semana[$i][$j]." AND fl_grupo = $fl_grupo");
            $diferencia_fechas = strtotime($fecha_clase[0]) + 1200 - time();
            if($diferencia_fechas <= 0) {
              $ds_rasis = RecuperaValor("SELECT nb_estatus FROM c_estatus_asistencia d WHERE cl_estatus_asistencia=1");
              echo "
                  $ds_rasis[0]";
            }
            else
              echo "
                  &nbsp;";
          }
          echo "    
                </td>";
          if(!empty($row[0])) {
            $suma_cal_g += $row[3];
            $suma_cal_t += $row[3];
            $factor_promedio_g++;
            $factor_promedio_t++;
            echo "    
                <td width='6%' align='center' class=$clase>
                  $row[0]";

          }
          else
            echo "
                <td width='5%' class=$clase>";
                $fecha_clase = RecuperaValor("SELECT fe_clase FROM k_clase WHERE fl_semana = ".$fl_semana[$i][$j]." AND fl_grupo = $fl_grupo");
                $diferencia_fechas = strtotime($fecha_clase[0]) + 1200 - time();
                if($diferencia_fechas <= 0 AND $fg_obligatorio[$i][$j]=='1' AND empty($row[0])) {
                  echo "
                      <a href='javascript:AssignGrade($row2[5],$clave);'>Assign Grade</a>";
                }
                else
                  echo "
                      &nbsp;";
          echo "
                </td>
              </tr>";  
        }
      }
    }
    # Promedio por term
    if(!empty($suma_cal_g) AND !empty($factor_promedio_g)){
    $suma_cal_g;
    $promedio_g = round(($suma_cal_g / $factor_promedio_g)*100)/100;
    $promedio_g1 = round($suma_cal_g / $factor_promedio_g);
    }
    else{
    $promedio_g = 0;
    $promedio_g1 = 0;
    }
    $Query = "SELECT cl_calificacion FROM c_calificacion WHERE no_min <= $promedio_g1 AND no_max >= $promedio_g1";
    $prom_g = RecuperaValor($Query);
    
    echo "
      <tr>
            <td colspan='6' align='right' class='css_prompt'>
              Term $no_grado[$i] GPA: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if($promedio_g > 0)
      echo "
                                    $prom_g[0]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$promedio_g%";
    else
      echo "                        &nbsp;";
    ECHO "
            </td>
          </tr>
    ";
  }
  echo "
        </table>
      </td>
    </tr>";
  echo "<div id='dlg_grade'><div id='dlg_grade_content'></div></div>"; 

  # Promedio General del curso
  # si aun no tiene calificacion la calculara para posterior save 
  # si ya hay pone la de la tabla c_alumno 
  if(!empty($suma_cal_t) AND !empty($factor_promedio_t))
    $promedio_t = round(($suma_cal_t / $factor_promedio_t)*100)/100;
  else
    $promedio_t = 0;
  Forma_Espacio();
  if(!empty($suma_cal_t) AND !empty($factor_promedio_t))
    $promedio_t = round(($suma_cal_t / $factor_promedio_t));
  else
    $promedio_t = 0;
  # Actualiza el promedio del student
  $promedio_t = round($no_promedio_t);
  $Query = "SELECT cl_calificacion FROM c_calificacion WHERE no_min <= $promedio_t AND no_max >= $promedio_t";
  $prom_t = RecuperaValor($Query);
  
  # Actualizamos el promedio total del student
  EjecutaQuery("UPDATE c_alumno SET no_promedio_t=$promedio_t WHERE fl_alumno=$clave");
  
  echo "
    <tr>
      <td colspan='2' align='center'class='css_prompt'>".ObtenEtiqueta(524).":&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$prom_t[0]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$promedio_t% $sum</td>
    </tr>";
  Forma_CampoOculto('no_promedio_t', $promedio_t);

  # Seccion del historial de los alumnos en los grupos y cursos
  Forma_Espacio();
  Forma_Seccion('Student history ');
  Forma_Espacio();
  $titulos = array(ObtenEtiqueta(360), ObtenEtiqueta(381), ObtenEtiqueta(365),
                 ObtenEtiqueta(420), ObtenEtiqueta(421),'');
  $ancho_col = array('25%', '10%', '10%', '10%', '10%','5%');
  Forma_Tabla_Ini('90%', $titulos, $ancho_col);
 
  $Query  = "SELECT nb_programa, nb_periodo, no_grado, nb_grupo, CONCAT(ds_nombres, ds_apaterno, ds_amaterno) ds_teacher  ";
  $Query .= "FROM k_alumno_historia a, c_programa b, c_periodo c, c_grupo d, c_usuario e ";
  $Query .= "WHERE a.fl_programa=b.fl_programa AND a.fl_periodo=c.fl_periodo AND a.fl_grupo=d.fl_grupo ";
  $Query .= "AND a.fl_maestro=e.fl_usuario AND fl_alumno=$clave  GROUP  BY d.fl_term ORDER BY a.fe_inicio";
  $rs = EjecutaQuery($Query);
  for($i=0;$row= RecuperaRegistro($rs);$i++){
    $nb_programa = str_texto($row[0]);
    $nb_periodo = str_texto($row[1]);
    if($grado_repetido == $row[2])
      $recurse = ObtenEtiqueta(853);
    else
      $recurse = "";
    $no_grado = $row[2];
    $nb_grupo = str_texto($row[3]);
    $ds_teacher = str_texto($row[4]);
    $grado_repetido = $no_grado;
    if($i % 2 == 0)
      $clase = "css_tabla_detalle";
    else
      $clase = "css_tabla_detalle_bg";
    echo "
    <tr class='$clase'>
      <td>".$nb_programa."</td>
      <td>".$nb_periodo."</td>
      <td align='center'>".$no_grado."</td>
      <td>".$nb_grupo."</td>
      <td>".$ds_teacher."</td>
      <td><div style='color:red; font-weight:bold;'>".$recurse."</div></td>
    </tr>";
  }

  Forma_Tabla_Fin();
  
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_ALUMNOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
  #Script  para la cofirmacion de borrar un pago 
     echo "
     <script>
     function super_user(ds_log){
        document.super.ds_login.value  = ds_log;
        document.super.ds_password.value  = '".ObtenConfiguracion(40)."';
        document.super.fg_campus.value  = '1';
        document.super.action = '../../../login_validate.php';
        document.super.submit();
     }
      function borrar_template(url,fl_alumno_template,fl_sesion,origen) {
      var answer = confirm('".str_ascii(ObtenMensaje(MSG_ELIMINAR))."');
      if(answer) {
        document.parametros.fl_alumno_template.value  = fl_alumno_template;
        document.parametros.fl_sesion.value  = fl_sesion;
        document.parametros.origen.value  = origen;
        document.parametros.action = url;
        document.parametros.submit();
      }
		}
    function otro(url,clave, origen) {
      document.otro.clave.value  = clave;
      document.otro.action = url;
      document.otro.submit();
    }
    
    // Muestra dialogo para asignar calificacion
    function AssignGrade(entrega,clave) {
      $.ajax({
        type: 'POST',
        url: '".PATH_CAMPUS."/teachers_new/ajax/get_assign_grades.php',
        async: false,
        data: 'fl_entrega_semanal='+entrega+'&clave='+clave+'&fl_usuario='+$fl_usuario,
        success: function(msg){
          $('#dlg_grade_content').html(msg);
          $('#dlg_grade').dialog('open');
        }
      });
    }
    $(function() {
      $('#dlg_grade').dialog({
	  	appendTo: '#content',
	    autoOpen: false,
	    resizable: false,
	    width: 320,
	    height: 330,
	    hide: 'highlight',
	    title: 'Assign grade',
	    modal: true,
	    buttons: {
	      'Cancel': function() {
	        $(this).dialog('close');
	      },
	      'Submit': function() {
	        $(this).dialog('close');
	        document.datos1.submit();
	      }
	    }
      });
	  });
    </script>
    <form name=parametros method=post>
      <input type=hidden name=fl_alumno_template>
      <input type=hidden name=fl_sesion>
      <input type=hidden name=origen>
    </form>\n
    <form name=otro method=post>
      <input type=hidden name=clave>
    </form>
     <form name='super' method='post' target='_blank'>
      <input type=hidden name=ds_login>
      <input type=hidden name=ds_password>      
      <input type=hidden name=fg_campus>      
    </form>";
  
?>