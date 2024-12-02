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

  $error = RecibeParametroNumerico('error');
  $confirmacion = RecibeParametroNumerico('confirmacion');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(130, $permiso) OR $permiso == PERMISO_ALTA) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  //programa actual
  $programa = ObtenProgramaActual();

  # Variable initialization to avoid errors
  $sum=NULL;
  $suma_cal_t=0;
  $factor_promedio_t=0;
  
  # Inicializa variables
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
  $ds_add_zip = str_texto($row[21]);
  $ds_add_country = str_texto($row[22]);
  $ds_m_add_number = $row[23];
  $ds_m_add_street = $row[24];
  $ds_m_add_city = $row[25];
  $ds_m_add_state = $row[25];
  $ds_m_add_zip = $row[27];
  $ds_m_add_country = $row[28];
  $ds_link_to_portfolio = str_texto($row[29]);
  $ds_foto_oficial = str_texto($row[30]);
  
  $row = RecuperaValor("SELECT fg_pago FROM c_sesion WHERE cl_sesion='$cl_sesion'");
  $fg_pago = $row[0];



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
  $fg_gender = str_texto($row[6]);
  $fe_birth = $row[7];
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
    $term_nivel = !empty($row_term[0])?$row_term[0]:NULL;
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
  
  PresentaEncabezado(130);
  
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
  if(!empty($fg_error))
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
        <option value='".$row[1]."'>
		";
		if($row[1]==52)
		echo"1.-";
	if($row[1]==53)
		echo"2.-";
	if($row[1]==54)
		echo"3.-";
	echo"
		".$row[0]." ";
	echo"
		</option>";
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
    $Query .= "WHERE a.fl_template=b.fl_template AND fl_alumno=$fl_sesion ";
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
  
  Forma_CampoInfo(ObtenEtiqueta(117),$ds_nombres);
  Forma_CampoInfo(ObtenEtiqueta(118), $ds_apaterno);
  Forma_CampoInfo(ObtenEtiqueta(119),$ds_amaterno);
  

  Forma_CampoInfo(ObtenEtiqueta(121), $ds_email);

  Forma_CampoInfo(ObtenEtiqueta(127), $ds_a_email);


  Forma_CampoInfo(ObtenEtiqueta(110), $nb_perfil);
  Forma_CampoOculto('fl_perfil', $fl_perfil);
  Forma_CampoOculto('nb_perfil', $nb_perfil);
  Forma_CampoInfo(ObtenEtiqueta(426), $ds_grupo);
  Forma_CampoInfo(ObtenEtiqueta(297), $nb_maestro);
  Forma_CampoInfo(ObtenEtiqueta(617), $no_grado_actual);
  Forma_CampoOculto('fl_grupo', $fl_grupo);
  
  # Calificaciones
  Forma_Seccion(ObtenEtiqueta(549));
  Forma_Doble_Ini();
  Div_Start_Responsive();
  # Presenta datos los cursos impartidos
  echo "
  <table border='0' cellpadding='0' cellspacing='0' class='table table-striped' width='80%'>
    <thead>      
    <tr  class='bg-color-blue txt-color-white'>
      <th align='center' class='css_caja'>
        ".ObtenEtiqueta(550)."
      </th>
      <th align='center' class='css_caja'>
        ".ObtenEtiqueta(551)."
      </th>
      <th align='center' class='css_caja'>
        ".ObtenEtiqueta(557)."
      </th>
      <th align='center' class='css_caja'>
        ".ObtenEtiqueta(428)."
      </th>
      <th align='center' class='css_caja'>
        ".ObtenEtiqueta(552)."
      </th>
      <th align='center' class='css_caja'>
        ".ObtenEtiqueta(553)."
      </th>
    </tr>
    </thead>";

  for($i = 0; $i < $tot_grados; $i++) {
    echo "
          <tr class='info'>
            <td colspan='6' align='center' class='css_prompt'>
              <strong>Term $no_grado[$i]</strong>
            </td>
          </tr>";
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
            $ds_titulo[$i][$j] = '';
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
                  echo " ";
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
              <strong>Term $no_grado[$i] GPA: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if($promedio_g > 0)
      echo "
                                    $prom_g[0]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".ROUND($promedio_g)."%";
    else
      echo "                        &nbsp;";
    ECHO "  
              </strong>
            </td>
          </tr>
    ";
  }
  echo "
        </table>";
  Div_close_Resposive();
  Forma_Doble_Fin();
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
  
  Forma_Doble_Ini();
    echo "<strong>".ObtenEtiqueta(524).":&nbsp;".$prom_t[0]."&nbsp;".$promedio_t."% ".$sum."</strong>";
  Forma_Doble_Fin();
  Forma_CampoOculto('no_promedio_t', $promedio_t);

  Forma_Termina(False);
  
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
  </script>
  <form name=parametros method=post>
    <input type=hidden name=fl_alumno_template>
    <input type=hidden name=fl_sesion>
    <input type=hidden name=origen>
  </form>\n
  <form name='super' method='post' target='_blank'>
    <input type=hidden name=ds_login>
    <input type=hidden name=ds_password>      
    <input type=hidden name=fg_campus>      
  </form>";
?>