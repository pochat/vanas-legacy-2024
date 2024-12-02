<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  # Applications archive
  define('FUNC_APP_FRM_AR',123);
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
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
  $fg_error = RecibeParametroNumerico('fg_error');
  # anios que falta agreagar el break
  $tot_anios = RecibeParametroNumerico('tot_anios');

  
  # En esta funcion solo se permite modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_APP_FRM, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recupera datos de la sesion
  $concat = array(ConsultaFechaBD('fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultmod', FMT_HORA));
  $Query  = "SELECT cl_sesion, fg_paypal, fg_confirmado, fg_pago, fg_inscrito, (".ConcatenaBD($concat).") 'fe_ultmod' ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE fl_sesion=$clave";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  $fg_paypal = $row[1];
  $fg_confirmado = $row[2];
  $fg_pago = $row[3];
  $fg_inscrito = $row[4];
  $fe_ultmod = $row[5];
  
  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
  $Query .= ConsultaFechaBD('fe_birth', FMT_FECHA)." fe_birth, ";
  $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, d.ds_pais, ";
  $Query .= "ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, e.ds_pais, ";
  $Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, nb_programa, nb_periodo, fl_template, b.fl_programa, c.fe_inicio ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_periodo=c.fl_periodo ";
  $Query .= "AND a.ds_add_country=d.fl_pais ";
  $Query .= "AND a.ds_eme_country=e.fl_pais ";
  $Query .= "AND cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_number = str_texto($row[3]);
  $ds_alt_number = str_texto($row[4]);
  $ds_email = str_texto($row[5]);
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
  $nb_programa = $row[23];
  $nb_periodo = $row[24];
  $fl_template = $row[25];
  $fl_programa = $row[26];
  $fe_inicio = $row[27];
  
  #Recupera datos adicionales a la forma 1 y del contrato del aplicante
  $Query  = "SELECT no_contrato, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_discount, ds_discount, mn_tot_tuition, mn_tot_program, ";
  $Query .= "mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
  $Query .= "ds_cadena, ds_firma_alumno, fg_opcion_pago, fe_firma, ds_p_name, ds_education_number, fg_international, ";
  $Query .= "cl_preference_1, cl_preference_2, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_pais, ds_a_email ";
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
    $fg_international = $row[23];
    $cl_preference_1 = $row[24];
    $cl_preference_2 = $row[25];
    $ds_a_email = $row[32];
  }
  
  #error
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($cl_sesion)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, ds_add_country, b.fg_pago,  c.ds_m_add_number, c.ds_m_add_street, ";
      $Query .= "ds_m_add_city, c.ds_m_add_state, c.ds_m_add_zip, c.ds_m_add_country, ds_fname, ds_mname, ds_lname, b.fg_archive FROM k_ses_app_frm_1 a, c_sesion b  LEFT JOIN k_app_contrato c ON(b.cl_sesion=c.cl_sesion AND c.no_contrato=1) ";
      $Query .= "WHERE a.cl_sesion=b.cl_sesion AND a.cl_sesion ='$cl_sesion' ";
      $row = RecuperaValor($Query);
      $ds_add_number = $row[0];
      $ds_add_street = $row[1];
      $ds_add_city = str_texto($row[2]);
      $ds_add_state = str_texto($row[3]);
      $ds_add_zip = $row[4];
      $ds_add_country = $row[5];
      $ds_m_add_number = $row[7];
      $ds_m_add_street = $row[8];
      $ds_m_add_city = str_texto($row[9]);
      $ds_m_add_state = str_texto($row[10]);
      $ds_m_add_zip = $row[11];
      $ds_m_add_country = $row[12];
      $ds_fname = $row[13];
      $ds_mname = $row[14];
      $ds_lname = $row[15];
      $fg_archive = $row[16];
    }
    else { // Alta, inicializa campos
      $ds_add_number = "";
      $ds_add_street = "";
      $ds_add_city = "";
      $ds_add_state = "";
      $ds_add_zip = "";
      $ds_add_country = "";
      $ds_m_add_number = "";
      $ds_m_add_street = "";
      $ds_m_add_city = "";
      $ds_m_add_state = "";
      $ds_m_add_zip = "";
      $ds_m_add_country = "";
      $ds_fname = "";
      $ds_mname = "";
      $ds_lname = "";
    }
    $ds_add_number_err = "";
    $ds_add_street_err = "";
    $ds_add_city_err = "";
    $ds_add_state_err = "";
    $ds_add_zip_err = "";
    $ds_add_country_err = "";
    $ds_m_add_number_err = "";
    $ds_m_add_street_err = "";
    $ds_m_add_city_err = "";
    $ds_m_add_state_err = "";
    $ds_m_add_zip_err = "";
    $ds_m_add_country_err = "";
    $ds_fname_err = "";
    $ds_mname_err = "";
    $ds_lname_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_add_number = RecibeParametroHTML('ds_add_number');
    $ds_add_street = RecibeParametroHTML('ds_add_street');
    $ds_add_city = RecibeParametroHTML('ds_add_city');
    $ds_add_state = RecibeParametroHTML('ds_add_state');
    $ds_add_zip = RecibeParametroHTML('ds_add_zip');
    $ds_add_country = RecibeParametroHTML('ds_add_country');
    $ds_add_number_err = RecibeParametroHTML('ds_add_number_err');
    $ds_add_street_err = RecibeParametroHTML('ds_add_street_err');
    $ds_add_city_err = RecibeParametroHTML('ds_add_city_err');
    $ds_add_state_err = RecibeParametroHTML('ds_add_state_err');
    $ds_add_zip_err = RecibeParametroHTML('ds_add_zip_err');
    $ds_add_country_err = RecibeParametroHTML('ds_add_country_err');
    //Mailing addrees
    $ds_m_add_number = RecibeParametroHTML('ds_m_add_number');
    $ds_m_add_street = RecibeParametroHTML('ds_m_add_street');
    $ds_m_add_city = RecibeParametroHTML('ds_m_add_city');
    $ds_m_add_state = RecibeParametroHTML('ds_m_add_state');
    $ds_m_add_zip = RecibeParametroHTML('ds_m_add_zip');
    $ds_m_add_country = RecibeParametroHTML('ds_m_add_country');
    #nombres
    $ds_fname = RecibeParametroHTML('ds_fname');
    $ds_fname_err = RecibeParametroHTML('ds_fname_err');
    $ds_mname = RecibeParametroHTML('ds_mname');
    $ds_lname = RecibeParametroHTML('ds_lname');
    $ds_lname_err = RecibeParametroHTML('ds_lname_err');
    $fg_archive = RecibeParametroBinario('fg_archive');
  }
  
  # Recupera datos de pagos del curso
  $Query  = "SELECT no_a_payments, ds_a_freq, no_b_payments, ds_b_freq, no_c_payments, ds_c_freq, no_d_payments, ds_d_freq, cl_type, ";
  $Query .= "no_a_interes, no_b_interes, no_c_interes, no_d_interes ";
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
  
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_APP_FRM_AR);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  echo "
  <script type='text/javascript' src='".PATH_JS."/frmApplications.js.php'></script>
  <script type='text/javascript' src='".PATH_JS."/sendtemplate.js.php'></script>";
  
  # Contratos
  if($cl_type==4)
    $contratos = 3;
  else
    $contratos = 1;
  $enrol = False;
  for($i=1; $i<=$contratos; $i++) {
    if($fe_inicio>date('Y-m-d')){
      if(!empty($fl_template)) {
        $ds_descarga = "<a href='../reports/documents_rpt.php?c=$clave&con=$i'>".ObtenEtiqueta(346)."</a>";
        if(empty($ds_cadena[$i]) || (!empty($ds_cadena[$i]) && empty($ds_firma_alumno[$i]))) {
          $ds_envia = "&nbsp;&nbsp;&nbsp;<a href='applications_snd.php?c=$clave&con=$i'>".ObtenEtiqueta(347)."</a>";
          $ds_firma = "";
          if($i==1)
            $enrol = False;
        }
        else {
          $ds_envia = "";
          $ds_firma = "&nbsp;&nbsp;&nbsp;<a href='view_contract.php?c=$clave&con=$i' target='_blank'>".ObtenEtiqueta(348)."</a>";
          if($i==1)
            $enrol = True;
        }
      }
      else {
        $ds_descarga = ObtenMensaje(213);
        $ds_envia = "";
        $ds_firma = "";
      }
    }
    else
      $ds_envia ='Expired';
    
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
  # Muestra error de que falta crear break en el anio
  if(!empty($tot_anios)){
    echo "<tr><td></td><td class='css_msg_error'>lack break in the year: ";
    for($i=0;$i<=$tot_anios;$i++){
      $existe = RecibeParametroNumerico('existe_'.$i);
      if($existe==0)
        echo $anios = RecibeParametroNumerico('anios_'.$i)."&nbsp;";
    }
    echo "</td></tr>";
  }
  
    
    
  Forma_Espacio( );
  
  # Datos del envio
  Forma_CampoInfo(ObtenEtiqueta(340), $fe_ultmod);
  if($fg_confirmado == '1')
    $ds_fg_confirmado = ETQ_SI;
  else
    $ds_fg_confirmado = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(344), $ds_fg_confirmado);
  Forma_Espacio( );
  /*Ya no aparece el checkbox para inscribirlo ni si pago de paypal ni confirmacion de applications fee payment*/  
  Forma_CampoCheckbox(ObtenEtiqueta(709), 'fg_archive', $fg_archive, ObtenEtiqueta(854));
  # Datos del programa
  Forma_CampoInfo(ObtenEtiqueta(360), $nb_programa);
  Forma_CampoInfo(ObtenEtiqueta(342), $nb_periodo);

  
  # Busca si existen alumnos con el mismo nombre y apellidos JGFL 26/01/2015
  $Query = "SELECT a.fl_usuario, CONCAT(a.ds_nombres,' ',a.ds_apaterno), c.nb_programa, nb_periodo FROM c_usuario a, k_ses_app_frm_1 b, c_programa c, c_periodo d ";
  $Query .= "WHERE a.ds_nombres='".$ds_fname."' AND a.ds_apaterno='".$ds_lname."' ";
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
  /*Ya no aparecera los enlaces para enviar los mensajes*/
  # Datos del aplicante
  Forma_Seccion(ObtenEtiqueta(61));
  Forma_CampoInfo(ObtenEtiqueta(117),$ds_fname);
  Forma_CampoInfo(ObtenEtiqueta(119),$ds_mname);
  Forma_CampoInfo(ObtenEtiqueta(118),$ds_lname);
  Forma_Espacio( );
  Forma_CampoInfo(ObtenEtiqueta(631), $ds_p_name);
  Forma_CampoInfo(ObtenEtiqueta(632), $ds_education_number);
  if($fg_international == '1')
    Forma_CampoInfo(ObtenEtiqueta(620), ETQ_SI);
  else
    Forma_CampoInfo(ObtenEtiqueta(620), ETQ_NO);
  Forma_Espacio( );
  Forma_CampoInfo(ObtenEtiqueta(280), $ds_number);
  Forma_CampoInfo(ObtenEtiqueta(281), $ds_alt_number);
  Forma_CampoInfo(ObtenEtiqueta(121), $ds_email);
  Forma_CampoInfo(ObtenEtiqueta(127), $ds_a_email);
  Forma_Espacio( );
  if($fg_gender == 'M')
    Forma_CampoInfo(ObtenEtiqueta(114), ObtenEtiqueta(115));
  else
    Forma_CampoInfo(ObtenEtiqueta(114), ObtenEtiqueta(116));
  Forma_CampoInfo(ObtenEtiqueta(120), $fe_birth);
  Forma_Espacio( );
  Forma_Seccion(ObtenEtiqueta(621));
  switch($cl_preference_1) {
    case 1: $preferencia1 = ObtenEtiqueta(624); break;
    case 2: $preferencia1 = ObtenEtiqueta(625); break;
    case 3: $preferencia1 = ObtenEtiqueta(626); break;
    case 4: $preferencia1 = ObtenEtiqueta(627); break;
    case 5: $preferencia1 = ObtenEtiqueta(628); break;
    case 6: $preferencia1 = ObtenEtiqueta(629); break;
    case 7: $preferencia1 = ObtenEtiqueta(630); break;
  }
  switch($cl_preference_2) {
    case 1: $preferencia2 = ObtenEtiqueta(624); break;
    case 2: $preferencia2 = ObtenEtiqueta(625); break;
    case 3: $preferencia2 = ObtenEtiqueta(626); break;
    case 4: $preferencia2 = ObtenEtiqueta(627); break;
    case 5: $preferencia2 = ObtenEtiqueta(628); break;
    case 6: $preferencia2 = ObtenEtiqueta(629); break;
    case 7: $preferencia2 = ObtenEtiqueta(630); break;
  }
  Forma_CampoInfo(ObtenEtiqueta(622), $preferencia1);
  Forma_CampoInfo(ObtenEtiqueta(623), $preferencia2);
  /*Ya no apareceran los datos del costo de los programas*/
  Forma_Doble_Fin( );
  
  # Direccion
  Forma_Seccion(ObtenEtiqueta(62));
  Forma_CampoInfo(ObtenEtiqueta(282),$ds_add_number);
  Forma_CampoInfo(ObtenEtiqueta(283),$ds_add_street);
  Forma_CampoInfo(ObtenEtiqueta(284),$ds_add_city);
  Forma_CampoInfo(ObtenEtiqueta(285),$ds_add_state);
  Forma_CampoInfo(ObtenEtiqueta(286),$ds_add_zip);
  Forma_CampoInfo(ObtenEtiqueta(287),$ds_add_country);
  Forma_Espacio( );
  
  # Direccion de envio de correspondencia
  Forma_Seccion(ObtenEtiqueta(633));
  Forma_CampoInfo(ObtenEtiqueta(282),$ds_m_add_number);
  Forma_CampoInfo(ObtenEtiqueta(283),$ds_m_add_street);
  Forma_CampoInfo(ObtenEtiqueta(284),$ds_m_add_city);
  Forma_CampoInfo(ObtenEtiqueta(285),$ds_m_add_state);
  Forma_CampoInfo(ObtenEtiqueta(286),$ds_m_add_zip);
  Forma_CampoInfo(ObtenEtiqueta(287),$ds_m_add_country);
  Forma_Espacio( );
  
  # Contacto de emergencia
  Forma_Seccion(ObtenEtiqueta(63));
  Forma_CampoInfo(ObtenEtiqueta(117), $ds_eme_fname);
  Forma_CampoInfo(ObtenEtiqueta(118), $ds_eme_lname);
  Forma_CampoInfo(ObtenEtiqueta(280), $ds_eme_number);
  Forma_CampoInfo(ObtenEtiqueta(288), $ds_eme_relation);
  Forma_CampoInfo(ObtenEtiqueta(287), $ds_eme_country);
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
  
  # AGRV 19/03/14
  # Envia campos ocultos para calcular end date
  Forma_CampoOculto('fl_programa', $fl_programa);
  Forma_CampoOculto('fe_inicio', $fe_inicio);
  Forma_CampoOculto('no_contrato', $no_contrato);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_APP_FRM, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );  
?>