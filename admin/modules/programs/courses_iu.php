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
  if(!ValidaPermiso(FUNC_CURSOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$nb_programa = RecibeParametroHTML('nb_programa');
  $no_orden = RecibeParametroNumerico('no_orden');
  $no_grados = RecibeParametroNumerico('no_grados');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $fl_template = RecibeParametroNumerico('fl_template');
	$ds_duracion = RecibeParametroHTML('ds_duracion');
  
  $no_horas = RecibeParametroNumerico('no_horas');
  $no_horas_week = RecibeParametroNumerico('no_horas_week');
  $no_semanas = RecibeParametroNumerico('no_semanas');
  $ds_credential = RecibeParametroHTML('ds_credential');
  $cl_delivery = RecibeParametroHTML('cl_delivery');
  $ds_language = RecibeParametroHTML('ds_language');
  $cl_type = RecibeParametroNumerico('cl_type');
  $app_fee = RecibeParametroFlotante('app_fee');
  $tuition = RecibeParametroFlotante('tuition');
  $no_costos_ad = RecibeParametroFlotante('no_costos_ad');
  $ds_costos_ad = RecibeParametroHTML('ds_costos_ad');
  $no_payments_a = RecibeParametroNumerico('no_payments_a');
  $frequency_a = RecibeParametroHTML('frequency_a');
  $amount_due_a = RecibeParametroFlotante('amount_due_a');
  $amount_paid_a = RecibeParametroFlotante('amount_paid_a');
  $interes_a = RecibeParametroFlotante('interes_a');
  $no_payments_b =RecibeParametroNumerico('no_payments_b');
  $frequency_b = RecibeParametroHTML('frequency_b');
  $amount_due_b = RecibeParametroFlotante('amount_due_b');
  $amount_paid_b = RecibeParametroFlotante('amount_paid_b');
  $interes_b = RecibeParametroFlotante('interes_b');
  $no_payments_c = RecibeParametroNumerico('no_payments_c');
  $frequency_c = RecibeParametroHTML('frequency_c');
  $amount_due_c = RecibeParametroFlotante('amount_due_c');
  $amount_paid_c = RecibeParametroFlotante('amount_paid_c');
  $interes_c = RecibeParametroFlotante('interes_c');
  $no_payments_d = RecibeParametroNumerico('no_payments_d');
  $frequency_d = RecibeParametroHTML('frequency_d');
  $amount_due_d = RecibeParametroFlotante('amount_due_d');
  $amount_paid_d = RecibeParametroFlotante('amount_paid_d');
  $interes_d = RecibeParametroFlotante('interes_d');
  $fg_fulltime = RecibeParametroBinario('fg_fulltime');
  $fg_taxes = RecibeParametroBinario('fg_taxes');
  $fg_total_programa = RecibeParametroBinario('fg_total_programa');
  $fg_archive = RecibeParametroBinario('fg_archive');
  $mn_lecture_fee = RecibeParametroFlotante('mn_lecture_fee');
  $mn_extra_fee = RecibeParametroFlotante('mn_extra_fee');
  $fg_tax_rate = RecibeParametroBinario('fg_tax_rate');
  
  // $d_meses = round($no_semanas / 4.3, 0);
  // $ds_duracion = $d_meses." months";
  switch ($cl_delivery)
  {
    case 'O': $ds_tipo = "Online"; break;
    case 'S': $ds_tipo = "On-Site"; break;
    case 'C': $ds_tipo = "Combined"; break;
  }
  
  # Valida campos obligatorios
  if(empty($nb_programa))
    $nb_programa_err = ERR_REQUERIDO;
  if(empty($ds_duracion))
    $ds_duracion_err = ERR_REQUERIDO;
  if(empty($ds_tipo))
    $ds_tipo_err = ERR_REQUERIDO;
  if($fl_template==0)
    $fl_template_err = ERR_REQUERIDO;
  if(empty($no_horas))
    $no_horas_err = ERR_REQUERIDO;
  if(empty($no_horas_week))
    $no_horas_week_err = ERR_REQUERIDO;
  if(empty($no_semanas))
    $no_semanas_err = ERR_REQUERIDO;
  if(empty($ds_credential))
    $ds_credential_err = ERR_REQUERIDO;
  if(empty($mn_lecture_fee))
    $mn_lecture_fee_err = ERR_REQUERIDO;
  if(empty($mn_extra_fee))
    $mn_extra_fee_err = ERR_REQUERIDO;
    
  if(empty($app_fee)) $app_fee = 0;
  if(empty($tuition)) $tuition = 0;
  if(empty($no_costos_ad)) $no_costos_ad = 0;
  if(empty($no_payments_a)) $no_payments_a = 0;
  if(empty($amount_due_a)) $amount_due_a = 0;
  if(empty($amount_paid_a)) $amount_paid_a = 0;
  if(empty($interes_a)) $interes_a = 0;
  if(empty($no_payments_b)) $no_payments_b = 0;
  if(empty($amount_due_b)) $amount_due_b = 0;
  if(empty($amount_paid_b)) $amount_paid_b = 0;
  if(empty($interes_b)) $interes_b = 0;
  if(empty($no_payments_c)) $no_payments_c = 0;
  if(empty($amount_due_c)) $amount_due_c = 0;
  if(empty($amount_paid_c)) $amount_paid_c = 0;
  if(empty($interes_c)) $interes_c = 0;
  if(empty($no_payments_d)) $no_payments_d = 0;
  if(empty($amount_due_d)) $amount_due_d = 0;
  if(empty($amount_paid_d)) $amount_paid_d = 0;
  if(empty($interes_d)) $interes_d = 0;
  if(empty($mn_lecture_fee)) $mn_lecture_fee = 0;
  if(empty($mn_extra_fee)) $mn_extra_fee = 0;
  
  # Valida enteros
  if($no_orden > MAX_TINYINT)
    $no_orden_err = ERR_TINYINT;
  if($no_grados > MAX_TINYINT)
    $no_grados_err = ERR_TINYINT;
  if($no_horas > MAX_SMALLINT)
    $no_horas_err = ERR_SMALLINT;
  if($no_semanas > MAX_TINYINT)
    $no_semanas_err = ERR_TINYINT;
  
	# Regresa a la forma con error
  $fg_error = $nb_programa_err || $ds_duracion_err || $ds_tipo_err || $no_orden_err || $no_grados_err || $fl_template_err || 
              $no_horas_err || $no_horas_week_err || $no_semanas_err || $ds_credential_err || $mn_lecture_fee_err || $mn_extra_fee_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('nb_programa' , $nb_programa);
    Forma_CampoOculto('nb_programa_err' , $nb_programa_err);
    Forma_CampoOculto('ds_duracion' , $ds_duracion);
    Forma_CampoOculto('ds_duracion_err' , $ds_duracion_err);
    Forma_CampoOculto('ds_tipo' , $ds_tipo);
    Forma_CampoOculto('ds_tipo_err' , $ds_tipo_err);
    Forma_CampoOculto('no_orden' , $no_orden);
    Forma_CampoOculto('no_orden_err' , $no_orden_err);
    Forma_CampoOculto('no_grados' , $no_grados);
    Forma_CampoOculto('no_grados_err' , $no_grados_err);
    Forma_CampoOculto('no_horas', $no_horas);
    Forma_CampoOculto('no_horas_err' , $no_horas_err);
    Forma_CampoOculto('no_horas_week', $no_horas_week);
    Forma_CampoOculto('no_horas_week_err' , $no_horas_week_err);
    Forma_CampoOculto('no_semanas', $no_semanas);
    Forma_CampoOculto('no_semanas_err' , $no_semanas_err);
    Forma_CampoOculto('ds_credential', $ds_credential);
    Forma_CampoOculto('ds_credential_err' , $ds_credential_err);
    Forma_CampoOculto('cl_delivery', $cl_delivery);
    Forma_CampoOculto('ds_language', $ds_language);
    Forma_CampoOculto('cl_type', $cl_type);
    Forma_CampoOculto('fl_template' , $fl_template);
    Forma_CampoOculto('fl_template_err' , $fl_template_err);
    Forma_CampoOculto('app_fee', $app_fee);
    Forma_CampoOculto('tuition', $tuition);
    Forma_CampoOculto('no_costos_ad', $no_costos_ad);
    Forma_CampoOculto('ds_costos_ad', $ds_costos_ad);
    Forma_CampoOculto('no_payments_a', $no_payments_a);
    Forma_CampoOculto('frequency_a', $frequency_a);
    Forma_CampoOculto('amount_due_a', $amount_due_a);
    Forma_CampoOculto('amount_paid_a', $amount_paid_a);
    Forma_CampoOculto('interes_a', $interes_a);
    Forma_CampoOculto('no_payments_b', $no_payments_b);
    Forma_CampoOculto('frequency_b', $frequency_b);
    Forma_CampoOculto('amount_due_b', $amount_due_b);
    Forma_CampoOculto('amount_paid_b', $amount_paid_b);
    Forma_CampoOculto('interes_b', $interes_b);
    Forma_CampoOculto('no_payments_c', $no_payments_c);
    Forma_CampoOculto('frequency_c', $frequency_c);
    Forma_CampoOculto('amount_due_c', $amount_due_c);
    Forma_CampoOculto('amount_paid_c', $amount_paid_c);
    Forma_CampoOculto('interes_c', $interes_c);
    Forma_CampoOculto('no_payments_d', $no_payments_d);
    Forma_CampoOculto('frequency_d', $frequency_d);
    Forma_CampoOculto('amount_due_d', $amount_due_d);
    Forma_CampoOculto('amount_paid_d', $amount_paid_d);
    Forma_CampoOculto('interes_d', $interes_d);
    Forma_CampoOculto('fg_fulltime', $fg_fulltime);
    Forma_CampoOculto('fg_taxes', $fg_taxes);
    Forma_CampoOculto('fg_total_programa', $fg_total_programa);
    Forma_CampoOculto('fg_archive', $fg_archive);
    Forma_CampoOculto('mn_lecture_fee', $mn_lecture_fee);
    Forma_CampoOculto('mn_lecture_fee_err', $mn_lecture_fee_err);
    Forma_CampoOculto('mn_extra_fee', $mn_extra_fee);
    Forma_CampoOculto('mn_extra_fee_err', $mn_extra_fee_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_programa (nb_programa, ds_duracion, ds_tipo, no_orden, no_grados, fl_template, fg_fulltime, fg_taxes, fg_total_programa, fg_archive, ";
    $Query .= "mn_lecture_fee, mn_extra_fee, fg_tax_rate) ";
    $Query .= "VALUES('$nb_programa', '$ds_duracion', '$ds_tipo', $no_orden, $no_grados, $fl_template, '$fg_fulltime', '$fg_taxes', '$fg_total_programa', '$fg_archive', ";
    $Query .= "$mn_lecture_fee, $mn_extra_fee,'$fg_tax_rate') ";
    EjecutaQuery($Query);
    $row = RecuperaValor("SELECT MAX(fl_programa) FROM c_programa");
    $clave = $row[0];
    
    $Query  = "INSERT INTO k_programa_costos (fl_programa, mn_app_fee, mn_tuition, mn_costs, ds_costs, ";
    $Query .= "no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes, no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, ";
    $Query .= "no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes, no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes, ";
    $Query .= "no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_horas_week) ";
    $Query .= "VALUES($clave, $app_fee, $tuition, $no_costos_ad, '$ds_costos_ad', ";
    $Query .= "$no_payments_a, '$frequency_a', $amount_due_a, $amount_paid_a, $interes_a, ";
    $Query .= "$no_payments_b, '$frequency_b', $amount_due_b, $amount_paid_b, $interes_b, ";
    $Query .= "$no_payments_c, '$frequency_c', $amount_due_c, $amount_paid_c, $interes_c, ";
    $Query .= "$no_payments_d, '$frequency_d', $amount_due_d, $amount_paid_d, $interes_d, ";
    $Query .= "$no_horas, $no_semanas, '$ds_credential', '$cl_delivery', '$ds_language', $cl_type, $no_horas_week) ";
    EjecutaQuery($Query);
  }
  else {
    $Query  = "UPDATE c_programa SET nb_programa='$nb_programa', ds_duracion='$ds_duracion', ds_tipo='$ds_tipo', ";
    $Query .= "no_orden=$no_orden, no_grados=$no_grados, fl_template=$fl_template, fg_fulltime='$fg_fulltime', fg_taxes='$fg_taxes', fg_total_programa='$fg_total_programa', fg_archive='$fg_archive',";
    $Query .= "mn_lecture_fee = $mn_lecture_fee,mn_extra_fee = $mn_extra_fee, fg_tax_rate='$fg_tax_rate' ";
    $Query .= "WHERE fl_programa=$clave";
    EjecutaQuery($Query);
    
    if(ExisteEnTabla('k_programa_costos', 'fl_programa', $clave))
    {
      $Query  = "UPDATE k_programa_costos SET mn_app_fee=$app_fee, mn_tuition=$tuition, mn_costs=$no_costos_ad, ds_costs='$ds_costos_ad', ";
      $Query .= "no_a_payments=$no_payments_a, ds_a_freq='$frequency_a', mn_a_due=$amount_due_a, mn_a_paid=$amount_paid_a, no_a_interes=$interes_a, ";
      $Query .= "no_b_payments=$no_payments_b, ds_b_freq='$frequency_b', mn_b_due=$amount_due_b, mn_b_paid=$amount_paid_b, no_b_interes=$interes_b, ";
      $Query .= "no_c_payments=$no_payments_c, ds_c_freq='$frequency_c', mn_c_due=$amount_due_c, mn_c_paid=$amount_paid_c, no_c_interes=$interes_c, ";
      $Query .= "no_d_payments=$no_payments_d, ds_d_freq='$frequency_d', mn_d_due=$amount_due_d, mn_d_paid=$amount_paid_d, no_d_interes=$interes_d, ";
      $Query .= "no_horas=$no_horas, no_semanas=$no_semanas, ds_credential='$ds_credential', cl_delivery='$cl_delivery', ds_language='$ds_language', cl_type=$cl_type, ";
      $Query .= "no_horas_week=$no_horas_week WHERE fl_programa=$clave";
    }
    else
    {
      $Query  = "INSERT INTO k_programa_costos (fl_programa, mn_app_fee, mn_tuition, mn_costs, ds_costs, ";
      $Query .= "no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes, no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, ";
      $Query .= "no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes, no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes, ";
      $Query .= "no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_horas_week) ";
      $Query .= "VALUES($clave, $app_fee, $tuition, $no_costos_ad, '$ds_costos_ad', ";
      $Query .= "$no_payments_a, '$frequency_a', $amount_due_a, $amount_paid_a, $interes_a, ";
      $Query .= "$no_payments_b, '$frequency_b', $amount_due_b, $amount_paid_b, $interes_b, ";
      $Query .= "$no_payments_c, '$frequency_c', $amount_due_c, $amount_paid_c, $interes_c, ";
      $Query .= "$no_payments_d, '$frequency_d', $amount_due_d, $amount_paid_d, $interes_d, ";
      $Query .= "$no_horas, $no_semanas, '$ds_credential', '$cl_delivery', '$ds_language', $cl_type, $no_horas_week) ";
    }
    EjecutaQuery($Query);
  }
  
  if(!empty($fl_programa))
  {
    $Query2  = "SELECT no_grado, no_semana, ds_titulo, ds_leccion, ds_vl_ruta, ds_vl_duracion, fe_vl_alta, ds_as_ruta, ";
    $Query2 .= "ds_as_duracion, fe_as_alta, fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
    $Query2 .= "FROM c_leccion WHERE fl_programa = $fl_programa";
    $cons = EjecutaQuery($Query2);
    while($row = RecuperaRegistro($cons))
    {
      $Query3  = "INSERT INTO c_leccion (fl_programa, no_grado, no_semana, ds_titulo, ds_leccion, ds_vl_ruta, ds_vl_duracion, fe_vl_alta, ";
      $Query3 .= "ds_as_ruta, ds_as_duracion, fe_as_alta, fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch)";
      $Query3 .= "VALUES($clave, $row[0], $row[1], '$row[2]', '$row[3]', '$row[4]', '$row[5]', '$row[6]', ";
      $Query3 .= "'$row[7]', '$row[8]', '$row[9]', '$row[10]', '$row[11]', $row[12], '$row[13]') ";
      EjecutaQuery($Query3);
    }
  }
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>