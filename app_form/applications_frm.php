<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $error = RecibeParametroNumerico('error');
  $confirmacion = RecibeParametroNumerico('confirmacion');
  
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
  $Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, nb_programa, nb_periodo, fl_template, a.fl_programa ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_periodo=c.fl_periodo ";
  $Query .= "AND a.ds_add_country=d.fl_pais ";
  $Query .= "AND a.ds_eme_country=e.fl_pais ";
  $Query .= "AND cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $nb_programa = $row[23];
  $nb_periodo = $row[24];
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  $ds_number = str_texto($row[3]);
  $ds_alt_number = str_texto($row[4]);
  $ds_email = str_texto($row[5]);
  $fg_gender = str_texto($row[6]);
  $fe_birth = $row[7];
  $ds_add_number = str_texto($row[8]);
  $ds_add_street = str_texto($row[9]);
  $ds_add_city = str_texto($row[10]);
  $ds_add_state = str_texto($row[11]);
  $ds_add_zip = str_texto($row[12]);
  $ds_add_country = str_texto($row[13]);
  $ds_eme_fname = str_texto($row[14]);
  $ds_eme_lname = str_texto($row[15]);
  $ds_eme_number = str_texto($row[16]);
  $ds_eme_relation = str_texto($row[17]);
  $ds_eme_country = str_texto($row[18]);
  $fg_ori_via = str_texto($row[19]);
  $ds_ori_other = str_texto($row[20]);
  $fg_ori_ref = str_texto($row[21]);
  $ds_ori_ref_name = str_texto($row[22]); 
  $fl_template = $row[25];
  $fl_programa = $row[26];
  
  #Recupera datos adicionales a la forma 1 y del contrato del aplicante
  $Query  = "SELECT no_contrato, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_discount, ds_discount, mn_tot_tuition, mn_tot_program, ";
  $Query .= "mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
  $Query .= "ds_cadena, ds_firma_alumno, fg_opcion_pago, fe_firma, ds_p_name, ds_education_number, fg_international, ";
  $Query .= "cl_preference_1, cl_preference_2, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_pais, ds_a_email ";
  $Query .= "FROM k_app_contrato  a LEFT JOIN c_pais b ON a.ds_m_add_country=b.fl_pais ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  $Query .= "ORDER BY no_contrato";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs))
  {
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
    $ds_m_add_number = $row[26];
    $ds_m_add_street = $row[27];
    $ds_m_add_city = $row[28];
    $ds_m_add_state = $row[29];
    $ds_m_add_zip = $row[30];
    $ds_m_add_country = $row[31];
    $ds_a_email = $row[32];
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
  PresentaEncabezado(FUNC_APP_FRM);
  Forma_Inicia($clave);
  
  echo "<script type='text/javascript' src='".PATH_JS."/frmApplications.js.php'></script>";
  
  # Contratos
  if($cl_type==4)
    $contratos = 3;
  else
    $contratos = 1;
  $enrol = False;
  for($i=1; $i<=$contratos; $i++)
  {
    if(!empty($fl_template))
    {
      $ds_descarga = "<a href='../reports/documents_rpt.php?c=$clave&con=$i'>".ObtenEtiqueta(346)."</a>";
      if(empty($ds_cadena[$i]) || (!empty($ds_cadena[$i]) && empty($ds_firma_alumno[$i])))
      {
        $ds_envia = "&nbsp;&nbsp;&nbsp;<a href='applications_snd.php?c=$clave&con=$i'>".ObtenEtiqueta(347)."</a>";
        $ds_firma = "";
        if($i==1)
          $enrol = False;
      }
      else
      {
        $ds_envia = "";
        $ds_firma = "&nbsp;&nbsp;&nbsp;<a href='view_contract.php?c=$clave&con=$i' target='_blank'>".ObtenEtiqueta(348)."</a>";
        if($i==1)
          $enrol = True;
      }
    }
    else
    {
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
  
  # Datos del envio
  Forma_CampoInfo(ObtenEtiqueta(340), $fe_ultmod);
  if($fg_confirmado == '1')
    $ds_fg_confirmado = ETQ_SI;
  else
    $ds_fg_confirmado = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(344), $ds_fg_confirmado);
  Forma_Espacio( );
  if($fg_paypal == '1')
    $ds_fg_paypal = ETQ_SI;
  else
    $ds_fg_paypal = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(343), $ds_fg_paypal);
  Forma_CampoCheckbox(ObtenEtiqueta(341), 'fg_pago', $fg_pago);
  if(!$enrol)
    Forma_CampoCheckbox(ObtenEtiqueta(345), 'fg_inscrito', $fg_inscrito, '', '', True, 'disabled="disabled"');
  else
    Forma_CampoCheckbox(ObtenEtiqueta(345), 'fg_inscrito', $fg_inscrito);
  Forma_Espacio( );
  
  # Datos del programa
  Forma_CampoInfo(ObtenEtiqueta(360), $nb_programa);
  Forma_CampoInfo(ObtenEtiqueta(342), $nb_periodo);
  Forma_Espacio( );
  
  # Datos del aplicante
  Forma_Seccion(ObtenEtiqueta(61));
  Forma_CampoInfo(ObtenEtiqueta(117), $ds_fname);
  Forma_CampoInfo(ObtenEtiqueta(119), $ds_mname);
  Forma_CampoInfo(ObtenEtiqueta(118), $ds_lname);
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
  switch($cl_preference_1)
  {
    case 1: $preferencia1 = ObtenEtiqueta(624); break;
    case 2: $preferencia1 = ObtenEtiqueta(625); break;
    case 3: $preferencia1 = ObtenEtiqueta(626); break;
    case 4: $preferencia1 = ObtenEtiqueta(627); break;
    case 5: $preferencia1 = ObtenEtiqueta(628); break;
    case 6: $preferencia1 = ObtenEtiqueta(629); break;
    case 7: $preferencia1 = ObtenEtiqueta(630); break;
  }
  switch($cl_preference_2)
  {
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
  
  # Datos de Costos y formas de pago
  Forma_Seccion(ObtenEtiqueta(580));
  Forma_Espacio( );
  Forma_Doble_Ini( );
  echo "
  <table border='".D_BORDES."' width='80%' cellpadding='3' cellspacing='0' class='css_default'>
    <tr class='css_tabla_encabezado'>
      <td colspan='3' align='center'>".ObtenEtiqueta(581)."</td>
    </tr>
    <tr class='css_tabla_detalle'>
      <td width='50%' align='left' style='font-weight:bold'>".ObtenEtiqueta(582)."</td>
      <td width='30%' align='left'>&nbsp;</td>
      <td width='20%' align='center' style='font-weight:bold'>".ObtenEtiqueta(583)."</td>
    </tr>
    <tr class='css_tabla_detalle_bg'>
      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(584)."</td>
      <td align='left'>&nbsp;</td>
      <td align='right'>$ ";
  CampoTexto('app_fee', $app_fee, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle'>
      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(585)."</td>
      <td align='left'>&nbsp;</td>
      <td align='right'>$ ";
  CampoTexto('tuition', $tuition, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle_bg'>
      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(586)."</td>
      <td align='left'>";
  CampoTexto('ds_costos_ad', $ds_costos_ad, 50, 25, 'css_input');
  echo "
      </td>
      <td align='right'>$ ";
  CampoTexto('no_costos_ad', $no_costos_ad, 10, 10, 'css_input', False, 'style="text-align:right" onchange="calcula()"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle'>
      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(587)."</td>
      <td align='left'>";
  CampoTexto('ds_descuento', $ds_descuento, 50, 25, 'css_input');
  echo "
      </td>
      <td align='right'>-$ ";
  CampoTexto('no_descuento', $no_descuento, 10, 10, 'css_input', False, 'style="text-align:right" onchange="calcula()"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle_bg'>
      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(588)."</td>
      <td align='left'>&nbsp;</td>
      <td align='right'>$ ";
  CampoTexto('total_tuition', $total_tuition, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle'>
      <td align='left' style='font-weight:bold'>".ObtenEtiqueta(589)."</td>
      <td align='left'>&nbsp;</td>
      <td align='right' style='font-weight:bold'>$ ";
  CampoTexto('total', $total, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
  </table>";
  Forma_Doble_Fin( );
  Forma_Espacio( );
  Forma_Doble_Ini( );
  echo "
  <table border='".D_BORDES."' width='80%' cellpadding='3' cellspacing='0' class='css_default'>
    <tr class='css_tabla_encabezado'>
      <td colspan='5' align='center'>".ObtenEtiqueta(590)."</td>
    </tr>
    <tr class='css_tabla_detalle' align='center' style='font-weight:bold;'>
      <td width='20%'>".ObtenEtiqueta(591)."</td>
      <td width='20%'>".ObtenEtiqueta(592)."</td>
      <td width='20%'>".ObtenEtiqueta(593)."</td>
      <td width='20%'>".ObtenEtiqueta(595)."</td>
      <td width='20%'>".ObtenEtiqueta(596)."</td>
    </tr>
    <tr class='css_tabla_detalle_bg'>
      <td width='20%' align='center'>";
  CampoRadio('opc_pago', 1, $opc_pago, 'A', True, 'disabled="disabled"');
  echo "
      </td>
      <td  align='center'>$no_a_payments</td>
      <td  align='left'>$ds_a_freq</td>
      <td  align='right'>$ ";
  CampoTexto('amount_due_a', $amount_due_a, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  Forma_CampoOculto('no_payments_a', $no_a_payments);
  Forma_CampoOculto('interes_a', $no_a_interes);
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_paid_a', $amount_paid_a, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle'>
      <td width='20%' align='center'>";
  CampoRadio('opc_pago', 2, $opc_pago, 'B', True, 'disabled="disabled"');
  echo "
      </td>
      <td  align='center'>$no_b_payments</td>
      <td  align='left'>$ds_b_freq</td>
      <td  align='right'>$ ";
  CampoTexto('amount_due_b', $amount_due_b, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  Forma_CampoOculto('no_payments_b', $no_b_payments);
  Forma_CampoOculto('interes_b', $no_b_interes);
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_paid_b', $amount_paid_b, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle_bg'>
      <td width='20%' align='center'>";
  CampoRadio('opc_pago', 3, $opc_pago, 'C', True, 'disabled="disabled"');
  echo "
      </td>
      <td  align='center'>$no_c_payments</td>
      <td  align='left'>$ds_c_freq</td>
      <td  align='right'>$ ";
  CampoTexto('amount_due_c', $amount_due_c, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  Forma_CampoOculto('no_payments_c', $no_c_payments);
  Forma_CampoOculto('interes_c', $no_c_interes);
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_paid_c', $amount_paid_c, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle'>
      <td width='20%' align='center'>";
  CampoRadio('opc_pago', 4, $opc_pago, 'D', True, 'disabled="disabled"');
  echo "
      </td>
      <td  align='center'>$no_d_payments</td>
      <td  align='left'>$ds_d_freq</td>
      <td  align='right'>$ ";
  CampoTexto('amount_due_d', $amount_due_d, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  Forma_CampoOculto('no_payments_d', $no_d_payments);
  Forma_CampoOculto('interes_d', $no_d_interes);
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_paid_d', $amount_paid_d, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
  </table>";
  Forma_Doble_Fin( );
  
  # Direccion
  Forma_Seccion(ObtenEtiqueta(62));
  Forma_CampoInfo(ObtenEtiqueta(282), $ds_add_number);
  Forma_CampoInfo(ObtenEtiqueta(283), $ds_add_street);
  Forma_CampoInfo(ObtenEtiqueta(284), $ds_add_city);
  Forma_CampoInfo(ObtenEtiqueta(285), $ds_add_state);
  Forma_CampoInfo(ObtenEtiqueta(286), $ds_add_zip);
  Forma_CampoInfo(ObtenEtiqueta(287), $ds_add_country);
  Forma_Espacio( );
  
  # Direccion de envio de correspondencia
  Forma_Seccion(ObtenEtiqueta(633));
  Forma_CampoInfo(ObtenEtiqueta(282), $ds_m_add_number);
  Forma_CampoInfo(ObtenEtiqueta(283), $ds_m_add_street);
  Forma_CampoInfo(ObtenEtiqueta(284), $ds_m_add_city);
  Forma_CampoInfo(ObtenEtiqueta(285), $ds_m_add_state);
  Forma_CampoInfo(ObtenEtiqueta(286), $ds_m_add_zip);
  Forma_CampoInfo(ObtenEtiqueta(287), $ds_m_add_country);
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
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_APP_FRM, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>