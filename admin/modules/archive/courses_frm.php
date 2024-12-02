<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CURSOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!empty($clave)) { // Actualizacion, recupera de la base de datos
    $Query  = "SELECT nb_programa, ds_duracion, ds_tipo, no_orden, no_grados, fl_template, fg_fulltime, fg_taxes, fg_total_programa, fg_archive ";
    $Query .= "FROM c_programa ";
    $Query .= "WHERE fl_programa=$clave";
    $row = RecuperaValor($Query);
    $nb_programa = str_texto($row[0]);
    $ds_duracion = str_texto($row[1]);
    $ds_tipo = str_texto($row[2]);
    $no_orden = $row[3];
    $no_grados = $row[4];
    $fl_template = $row[5];
    $fg_fulltime = $row[6];
    $fg_taxes = $row[7];
    $fg_total_programa = $row[8];
    $fg_archive = $row[9];
    
    $Query  = "SELECT no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, ";
    $Query .= "mn_app_fee, mn_tuition, mn_costs, ds_costs, no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes, ";
    $Query .= "no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes, ";
    $Query .= "no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes ";
    $Query .= "FROM k_programa_costos ";
    $Query .= "WHERE fl_programa = $clave ";
    $row = RecuperaValor($Query);
    
    # Datos pagos y para contrato
    $no_horas = $row[0];
    $no_semanas = $row[1];
    $ds_credential = $row[2];
    $cl_delivery = $row[3];
    $ds_language = $row[4];
    $cl_type = $row[5];
    if(!empty($row[6]))
      $app_fee = $row[6];
    else
      $app_fee = 0;
    if(!empty($row[7]))
      $tuition = $row[7];
    else
      $tuition = 0;
    $no_costos_ad = $row[8];
    $ds_costos_ad = $row[9];
    if(!empty($row[10]))
      $no_payments_a = $row[10];
    else
      $no_payments_a = 1;
    if(!empty($row[11]))
      $frequency_a = $row[11];
    else
      $frequency_a = "Full Payment";
    $amount_due_a = $row[12];
    $amount_paid_a = $row[13];
    if(!empty($row[14]))
      $interes_a = $row[14];
    else
      $interes_a = 0;
    $no_payments_b = $row[15];
    $frequency_b = $row[16];
    $amount_due_b = $row[17];
    $amount_paid_b = $row[18];
    $interes_b = $row[19];
    $no_payments_c = $row[20];
    $frequency_c = $row[21];
    $amount_due_c = $row[22];
    $amount_paid_c = $row[23];
    $interes_c = $row[24];
    $no_payments_d = $row[25];
    $frequency_d = $row[26];
    $amount_due_d = $row[27];
    $amount_paid_d = $row[28];
    $interes_d = $row[29];
    
  }

  
  $total_tuition = number_format($tuition + $no_costos_ad, 2, '.', '');
  $total = number_format($app_fee + $total_tuition, 2, '.', '');
      
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CURSOS);
  
  echo "<script type='text/javascript' src='".PATH_JS."/frmCourses.js.php'></script>";
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Campos de captura
  Forma_CampoTexto(ObtenEtiqueta(360), True, 'nb_programa', $nb_programa, 50, 50, $nb_programa_err);
  Forma_Espacio( );
  // Forma_CampoTexto(ObtenEtiqueta(362), True, 'ds_tipo', $ds_tipo, 50, 20, $ds_tipo_err);
  Forma_CampoTexto(ETQ_ORDEN, True, 'no_orden', $no_orden, 3, 5, $no_orden_err);
  Forma_CampoTexto(ObtenEtiqueta(365), True, 'no_grados', $no_grados, 3, 5, $no_grados_err);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(361), True, 'ds_duracion', $ds_duracion, 50, 20, $ds_duracion_err);
  Forma_CampoTexto(ObtenEtiqueta(368), True, 'no_horas', $no_horas, 50, 20, $no_horas_err);
  Forma_CampoTexto(ObtenEtiqueta(369), True, 'no_semanas', $no_semanas, 50, 20, $no_semanas_err);
  Forma_CampoTexto(ObtenEtiqueta(610), True, 'ds_credential', $ds_credential, 50, 20, $ds_credential_err);
  $p_opc = array('Online', 'On-Site', 'Combined');
  $p_val = array('O', 'S', 'C');
  Forma_CampoSelect(ObtenEtiqueta(611), True, 'cl_delivery', $p_opc, $p_val, $cl_delivery, $cl_delivery_err);
  Forma_CampoTexto(ObtenEtiqueta(612), False, 'ds_language', $ds_language, 50, 20);
  $p_opc1 = array('Long Term Duration', 'Short Term Duration', 'Corporate', 'Long Term Duration(3 contracts, 1 per year)');
  $p_val1 = array(1, 2, 3, 4);
  Forma_CampoSelect(ObtenEtiqueta(613), True, 'cl_type', $p_opc1, $p_val1, $cl_type, $cl_type_err);
  Forma_Espacio( );
  
  $Query = "SELECT nb_template, fl_template FROM k_template_doc WHERE fl_categoria=1 ORDER BY nb_template";
  Forma_CampoSelectBD(ObtenEtiqueta(367), True, 'fl_template', $Query, $fl_template, $fl_template_err, True);
  Forma_Espacio( );
  
  $Query  = "SELECT DISTINCT CONCAT(nb_programa,' (', ds_duracion,')'), fl_programa FROM c_programa ";
  $Query .= "WHERE fl_programa <> $clave ";
  $Query .= "ORDER BY no_orden";
  $row = RecuperaValor("SELECT COUNT(*) FROM c_leccion WHERE fl_programa = $clave");
  if($row[0] > 0)
    $p_script = "disabled='disabled'";
  else
    $p_script = '';
  Forma_CampoSelectBD(ObtenEtiqueta(366), False, 'fl_programa', $Query, 0, '', True, $p_script);
  Forma_CampoCheckBox('Full time','fg_fulltime', $fg_fulltime, '(Uncheck for Part Time)');
  Forma_CampoCheckBox(ObtenEtiqueta(695),'fg_taxes', $fg_taxes);
  Forma_Espacio( );
  
  
  # Payments - Program Costs
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
  CampoTexto('app_fee', $app_fee, 10, 10, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle'>
      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(585)."</td>
      <td align='left'>&nbsp;</td>
      <td align='right'>$ ";
  CampoTexto('tuition', $tuition, 10, 10, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
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
  CampoTexto('no_costos_ad', $no_costos_ad, 10, 10, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle'>
      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(588)."</td>
      <td align='left'>&nbsp;</td>
      <td align='right'>$ ";
  CampoTexto('total_tuition', $total_tuition, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle_bg'>
      <td align='left' style='font-weight:bold'>".ObtenEtiqueta(589)."</td>
      <td align='left'>&nbsp;</td>
      <td align='right' style='font-weight:bold'>$ ";
  CampoTexto('total', $total, 10, 10, 'css_input', False, "style='text-align:right' readonly='readonly'");
  echo "
      </td>
    </tr>
  </table>
  <table border='".D_BORDES."' width='80%' cellpadding='3' cellspacing='0' class='css_default'>
    <tr>
      <td align='right'>".ObtenEtiqueta(698)." <input type='checkbox' id='fg_total_programa' name='fg_total_programa' ";
  if(!empty($fg_total_programa))
    echo "checked";
  echo "></td>
    </tr>
  </table>";
  Forma_Doble_Fin( );
  Forma_Espacio( );
  
  # Payments - Payment Options
  Forma_Doble_Ini( );
  echo "
  <table border='".D_BORDES."' width='80%' cellpadding='3' cellspacing='0' class='css_default'>
    <tr class='css_tabla_encabezado'>
      <td colspan='6' align='center'>".ObtenEtiqueta(590)."</td>
    </tr>
    <tr class='css_tabla_detalle' align='center' style='font-weight:bold;'>
      <td width='8%'>".ObtenEtiqueta(591)."</td>
      <td width='20%'>".ObtenEtiqueta(592)."</td>
      <td width='20%'>".ObtenEtiqueta(593)."</td>
      <td width='12%'>".ObtenEtiqueta(594)."</td>
      <td width='20%'>".ObtenEtiqueta(595)."</td>
      <td width='20%'>".ObtenEtiqueta(596)."</td>
    </tr>
    <tr class='css_tabla_detalle_bg'>
      <td align='center'>A</td>
      <td  align='center'>";
  CampoTexto('no_payments_a', $no_payments_a, 3, 3, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
  echo "
      </td>
      <td  align='center'>";
  CampoTexto('frequency_a', $frequency_a, 15, 10, 'css_input');
  echo "
      </td>
      <td  align='right'>";
  CampoTexto('interes_a', $interes_a, 5, 3, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_due_a', $amount_due_a, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_paid_a', $amount_paid_a, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle'>
      <td align='center'>B</td>
      <td  align='center'>";
  CampoTexto('no_payments_b', $no_payments_b, 3, 3, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
  echo "
      </td>
      <td  align='center'>";
  CampoTexto('frequency_b', $frequency_b, 15, 10, 'css_input');
  echo "
      </td>
      <td  align='right'>";
  CampoTexto('interes_b', $interes_b, 5, 3, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_due_b', $amount_due_b, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_paid_b', $amount_paid_b, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle_bg'>
      <td align='center'>C</td>
      <td  align='center'>";
  CampoTexto('no_payments_c', $no_payments_c, 3, 3, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
  echo "
      </td>
      <td  align='center'>";
  CampoTexto('frequency_c', $frequency_c, 15, 10, 'css_input');
  echo "
      </td>
      <td  align='right'>";
  CampoTexto('interes_c', $interes_c, 5, 3, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_due_c', $amount_due_c, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_paid_c', $amount_paid_c, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
    <tr class='css_tabla_detalle'>
      <td align='center'>D</td>
      <td  align='center'>";
  CampoTexto('no_payments_d', $no_payments_d, 3, 3, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
  echo "
      </td>
      <td  align='center'>";
  CampoTexto('frequency_d', $frequency_d, 15, 10, 'css_input');
  echo "
      </td>
      <td  align='right'>";
  CampoTexto('interes_d', $interes_d, 5, 3, 'css_input', False, 'style="text-align:right" onchange="calcula_costo()"');
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_due_d', $amount_due_d, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
      <td  align='right'>$ ";
  CampoTexto('amount_paid_d', $amount_paid_d, 10, 10, 'css_input', False, 'style="text-align:right" readonly="readonly"');
  echo "
      </td>
    </tr>
  </table>";
  Forma_Doble_Fin( );
  
  Forma_Termina(False);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>