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

  # Variable initialization to avoid errors
  $nb_programa_err=NULL;
  $ds_duracion_err=NULL;
  $ds_tipo_err=NULL;
  $no_orden_err=NULL;
  $no_grados_err=NULL;
  $fl_template_err=NULL;
  $no_horas_err=NULL;
  $no_horas_week_err=NULL;
  $no_semanas_err=NULL;
  $ds_credential_err=NULL;
  $mn_lecture_fee_err=NULL;
  $mn_extra_fee_err=NULL;
  $no_val_rub_err=NULL;
  $no_max_grade_err=NULL;
  $tab_rubric_err=NULL;
  $ds_ruta_imagen=NULL;


  # Recibe parametros
  $fg_error = 0;
	$nb_programa = RecibeParametroHTML('nb_programa');
  $no_orden = RecibeParametroNumerico('no_orden');
  $no_grados = RecibeParametroNumerico('no_grados');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $fl_template = RecibeParametroNumerico('fl_template');
	$ds_duracion = RecibeParametroHTML('ds_duracion');
    $no_ptib=RecibeParametroHTML('no_ptib');

  $ptib_approval = RecibeParametroBinario('ptib_approval');

  $no_horas = RecibeParametroNumerico('no_horas');
  $no_horas_week = RecibeParametroNumerico('no_horas_week');
  $no_semanas = RecibeParametroNumerico('no_semanas');
  $ds_credential = RecibeParametroHTML('ds_credential');
  $cl_delivery = RecibeParametroHTML('cl_delivery');
  $ds_language = RecibeParametroHTML('ds_language');
  $cl_type = RecibeParametroNumerico('cl_type');

  $app_fee = RecibeParametroFlotante('app_fee');
  $app_fee_internacional = RecibeParametroFlotante('app_fee_internacional');
  $tuition = RecibeParametroFlotante('tuition');
  $tuition_internacional = RecibeParametroFlotante('tuition_internacional');
  $no_costos_ad = RecibeParametroFlotante('no_costos_ad');
  $no_costos_ad_internacional = RecibeParametroFlotante('no_costos_ad_internacional');
  $ds_costos_ad = RecibeParametroHTML('ds_costos_ad');
  $ds_costos_ad_internacional = RecibeParametroHTML('ds_costos_ad_internacional');


  $app_fee_combined = RecibeParametroFlotante('app_fee_combined');
  $app_fee_internacional_combined = RecibeParametroFlotante('app_fee_internacional_combined');
  $tuition_combined = RecibeParametroFlotante('tuition_combined');
  $tuition_internacional_combined = RecibeParametroFlotante('tuition_internacional_combined');
  $no_costos_ad_combined = RecibeParametroFlotante('no_costos_ad_combined');
  $no_costos_ad_internacional_combined = RecibeParametroFlotante('no_costos_ad_internacional_combined');
  $ds_costos_ad_combined = RecibeParametroHTML('ds_costos_ad_combined');
  $ds_costos_ad_internacional_combined = RecibeParametroHTML('ds_costos_ad_internacional_combined');

  #USA
  $app_fee_usa = RecibeParametroFlotante('app_fee_usa');
  $app_fee_internacional_usa = RecibeParametroFlotante('app_fee_internacional_usa');
  $tuition_usa = RecibeParametroFlotante('tuition_usa');
  $tuition_internacional_usa = RecibeParametroFlotante('tuition_internacional_usa');
  $no_costos_ad_usa = RecibeParametroFlotante('no_costos_ad_usa');
  $no_costos_ad_internacional_usa = RecibeParametroFlotante('no_costos_ad_internacional_usa');
  $ds_costos_ad_usa = RecibeParametroHTML('ds_costos_ad_usa');
  $ds_costos_ad_internacional_usa = RecibeParametroHTML('ds_costos_ad_internacional_usa');

  $app_fee_combined_usa = RecibeParametroFlotante('app_fee_combined_usa');
  $app_fee_internacional_combined_usa = RecibeParametroFlotante('app_fee_internacional_combined_usa');
  $tuition_combined_usa = RecibeParametroFlotante('tuition_combined_usa');
  $tuition_internacional_combined_usa = RecibeParametroFlotante('tuition_internacional_combined_usa');
  $no_costos_ad_combined_usa = RecibeParametroFlotante('no_costos_ad_combined_usa');
  $no_costos_ad_internacional_combined_usa = RecibeParametroFlotante('no_costos_ad_internacional_combined_usa');
  $ds_costos_ad_combined_usa = RecibeParametroHTML('ds_costos_ad_combined_usa');
  $ds_costos_ad_internacional_combined_usa = RecibeParametroHTML('ds_costos_ad_internacional_combined_usa');


  #Online
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

  #USA
  $no_payments_a_usa = RecibeParametroNumerico('no_payments_a_usa');
  $frequency_a_usa = RecibeParametroHTML('frequency_a_usa');
  $amount_due_a_usa = RecibeParametroFlotante('amount_due_a_usa');
  $amount_paid_a_usa = RecibeParametroFlotante('amount_paid_a_usa');
  $interes_a_usa = RecibeParametroFlotante('interes_a_usa');
  $no_payments_b_usa =RecibeParametroNumerico('no_payments_b_usa');
  $frequency_b_usa = RecibeParametroHTML('frequency_b_usa');
  $amount_due_b_usa = RecibeParametroFlotante('amount_due_b_usa');
  $amount_paid_b_usa = RecibeParametroFlotante('amount_paid_b_usa');
  $interes_b_usa = RecibeParametroFlotante('interes_b_usa');
  $no_payments_c_usa = RecibeParametroNumerico('no_payments_c_usa');
  $frequency_c_usa = RecibeParametroHTML('frequency_c_usa');
  $amount_due_c_usa = RecibeParametroFlotante('amount_due_c_usa');
  $amount_paid_c_usa = RecibeParametroFlotante('amount_paid_c_usa');
  $interes_c_usa = RecibeParametroFlotante('interes_c_usa');
  $no_payments_d_usa = RecibeParametroNumerico('no_payments_d_usa');
  $frequency_d_usa = RecibeParametroHTML('frequency_d_usa');
  $amount_due_d_usa = RecibeParametroFlotante('amount_due_d_usa');
  $amount_paid_d_usa = RecibeParametroFlotante('amount_paid_d_usa');
  $interes_d_usa = RecibeParametroFlotante('interes_d_usa');



  #Internacional
  $no_payments_a_internacional = RecibeParametroNumerico('no_payments_a_internacional');
  $frequency_a_internacional = RecibeParametroHTML('frequency_a_internacional');
  $amount_due_a_internacional = RecibeParametroFlotante('amount_due_a_internacional');
  $amount_paid_a_internacional = RecibeParametroFlotante('amount_paid_a_internacional');
  $interes_a_internacional = RecibeParametroFlotante('interes_a_internacional');
  $no_payments_b_internacional =RecibeParametroNumerico('no_payments_b_internacional');
  $frequency_b_internacional = RecibeParametroHTML('frequency_b_internacional');
  $amount_due_b_internacional = RecibeParametroFlotante('amount_due_b_internacional');
  $amount_paid_b_internacional = RecibeParametroFlotante('amount_paid_b_internacional');
  $interes_b_internacional = RecibeParametroFlotante('interes_b_internacional');
  $no_payments_c_internacional = RecibeParametroNumerico('no_payments_c_internacional');
  $frequency_c_internacional = RecibeParametroHTML('frequency_c_internacional');
  $amount_due_c_internacional = RecibeParametroFlotante('amount_due_c_internacional');
  $amount_paid_c_internacional = RecibeParametroFlotante('amount_paid_c_internacional');
  $interes_c_internacional = RecibeParametroFlotante('interes_c_internacional');
  $no_payments_d_internacional = RecibeParametroNumerico('no_payments_d_internacional');
  $frequency_d_internacional = RecibeParametroHTML('frequency_d_internacional');
  $amount_due_d_internacional = RecibeParametroFlotante('amount_due_d_internacional');
  $amount_paid_d_internacional = RecibeParametroFlotante('amount_paid_d_internacional');
  $interes_d_internacional = RecibeParametroFlotante('interes_d_internacional');

  $no_payments_a_internacional_usa = RecibeParametroNumerico('no_payments_a_internacional_usa');
  $frequency_a_internacional_usa = RecibeParametroHTML('frequency_a_internacional_usa');
  $amount_due_a_internacional_usa = RecibeParametroFlotante('amount_due_a_internacional_usa');
  $amount_paid_a_internacional_usa = RecibeParametroFlotante('amount_paid_a_internacional_usa');
  $interes_a_internacional_usa = RecibeParametroFlotante('interes_a_internacional_usa');
  $no_payments_b_internacional_usa =RecibeParametroNumerico('no_payments_b_internacional_usa');
  $frequency_b_internacional_usa = RecibeParametroHTML('frequency_b_internacional_usa');
  $amount_due_b_internacional_usa = RecibeParametroFlotante('amount_due_b_internacional_usa');
  $amount_paid_b_internacional_usa = RecibeParametroFlotante('amount_paid_b_internacional_usa');
  $interes_b_internacional_usa = RecibeParametroFlotante('interes_b_internacional_usa');
  $no_payments_c_internacional_usa = RecibeParametroNumerico('no_payments_c_internacional_usa');
  $frequency_c_internacional_usa = RecibeParametroHTML('frequency_c_internacional_usa');
  $amount_due_c_internacional_usa = RecibeParametroFlotante('amount_due_c_internacional_usa');
  $amount_paid_c_internacional_usa = RecibeParametroFlotante('amount_paid_c_internacional_usa');
  $interes_c_internacional_usa = RecibeParametroFlotante('interes_c_internacional_usa');
  $no_payments_d_internacional_usa = RecibeParametroNumerico('no_payments_d_internacional_usa');
  $frequency_d_internacional_usa = RecibeParametroHTML('frequency_d_internacional_usa');
  $amount_due_d_internacional_usa = RecibeParametroFlotante('amount_due_d_internacional_usa');
  $amount_paid_d_internacional_usa = RecibeParametroFlotante('amount_paid_d_internacional_usa');
  $interes_d_internacional_usa = RecibeParametroFlotante('interes_d_internacional_usa');


  ###Combined

  #Canada
  $no_payments_a_combined = RecibeParametroNumerico('no_payments_a_combined');
  $frequency_a_combined = RecibeParametroHTML('frequency_a_combined');
  $amount_due_a_combined = RecibeParametroFlotante('amount_due_a_combined');
  $amount_paid_a_combined = RecibeParametroFlotante('amount_paid_a_combined');
  $interes_a_combined = RecibeParametroFlotante('interes_a_combined');
  $no_payments_b_combined =RecibeParametroNumerico('no_payments_b_combined');
  $frequency_b_combined = RecibeParametroHTML('frequency_b_combined');
  $amount_due_b_combined = RecibeParametroFlotante('amount_due_b_combined');
  $amount_paid_b_combined = RecibeParametroFlotante('amount_paid_b_combined');
  $interes_b_combined = RecibeParametroFlotante('interes_b_combined');
  $no_payments_c_combined = RecibeParametroNumerico('no_payments_c_combined');
  $frequency_c_combined = RecibeParametroHTML('frequency_c_combined');
  $amount_due_c_combined = RecibeParametroFlotante('amount_due_c_combined');
  $amount_paid_c_combined = RecibeParametroFlotante('amount_paid_c_combined');
  $interes_c_combined = RecibeParametroFlotante('interes_c_combined');
  $no_payments_d_combined = RecibeParametroNumerico('no_payments_d_combined');
  $frequency_d_combined = RecibeParametroHTML('frequency_d_combined');
  $amount_due_d_combined = RecibeParametroFlotante('amount_due_d_combined');
  $amount_paid_d_combined = RecibeParametroFlotante('amount_paid_d_combined');
  $interes_d_combined = RecibeParametroFlotante('interes_d_combined');

  #Combined Internacional
  $no_payments_a_internacional_combined = RecibeParametroNumerico('no_payments_a_internacional_combined');
  $frequency_a_internacional_combined = RecibeParametroHTML('frequency_a_internacional_combined');
  $amount_due_a_internacional_combined = RecibeParametroFlotante('amount_due_a_internacional_combined');
  $amount_paid_a_internacional_combined = RecibeParametroFlotante('amount_paid_a_internacional_combined');
  $interes_a_internacional_combined = RecibeParametroFlotante('interes_a_internacional_combined');
  $no_payments_b_internacional_combined =RecibeParametroNumerico('no_payments_b_internacional_combined');
  $frequency_b_internacional_combined = RecibeParametroHTML('frequency_b_internacional_combined');
  $amount_due_b_internacional_combined = RecibeParametroFlotante('amount_due_b_internacional_combined');
  $amount_paid_b_internacional_combined = RecibeParametroFlotante('amount_paid_b_internacional_combined');
  $interes_b_internacional_combined = RecibeParametroFlotante('interes_b_internacional_combined');
  $no_payments_c_internacional_combined = RecibeParametroNumerico('no_payments_c_internacional_combined');
  $frequency_c_internacional_combined = RecibeParametroHTML('frequency_c_internacional_combined');
  $amount_due_c_internacional_combined = RecibeParametroFlotante('amount_due_c_internacional_combined');
  $amount_paid_c_internacional_combined = RecibeParametroFlotante('amount_paid_c_internacional_combined');
  $interes_c_internacional_combined = RecibeParametroFlotante('interes_c_internacional_combined');
  $no_payments_d_internacional_combined = RecibeParametroNumerico('no_payments_d_internacional_combined');
  $frequency_d_internacional_combined = RecibeParametroHTML('frequency_d_internacional_combined');
  $amount_due_d_internacional_combined = RecibeParametroFlotante('amount_due_d_internacional_combined');
  $amount_paid_d_internacional_combined = RecibeParametroFlotante('amount_paid_d_internacional_combined');
  $interes_d_internacional_combined = RecibeParametroFlotante('interes_d_internacional_combined');

  #USA

  $no_payments_a_combined_usa = RecibeParametroNumerico('no_payments_a_combined_usa');
  $frequency_a_combined_usa = RecibeParametroHTML('frequency_a_combined_usa');
  $amount_due_a_combined_usa = RecibeParametroFlotante('amount_due_a_combined_usa');
  $amount_paid_a_combined_usa = RecibeParametroFlotante('amount_paid_a_combined_usa');
  $interes_a_combined_usa = RecibeParametroFlotante('interes_a_combined_usa');
  $no_payments_b_combined_usa =RecibeParametroNumerico('no_payments_b_combined_usa');
  $frequency_b_combined_usa = RecibeParametroHTML('frequency_b_combined_usa');
  $amount_due_b_combined_usa = RecibeParametroFlotante('amount_due_b_combined_usa');
  $amount_paid_b_combined_usa = RecibeParametroFlotante('amount_paid_b_combined_usa');
  $interes_b_combined_usa = RecibeParametroFlotante('interes_b_combined_usa');
  $no_payments_c_combined_usa = RecibeParametroNumerico('no_payments_c_combined_usa');
  $frequency_c_combined_usa = RecibeParametroHTML('frequency_c_combined_usa');
  $amount_due_c_combined_usa = RecibeParametroFlotante('amount_due_c_combined_usa');
  $amount_paid_c_combined_usa = RecibeParametroFlotante('amount_paid_c_combined_usa');
  $interes_c_combined_usa = RecibeParametroFlotante('interes_c_combined_usa');
  $no_payments_d_combined_usa = RecibeParametroNumerico('no_payments_d_combined_usa');
  $frequency_d_combined_usa = RecibeParametroHTML('frequency_d_combined_usa');
  $amount_due_d_combined_usa = RecibeParametroFlotante('amount_due_d_combined_usa');
  $amount_paid_d_combined_usa = RecibeParametroFlotante('amount_paid_d_combined_usa');
  $interes_d_combined_usa = RecibeParametroFlotante('interes_d_combined_usa');

  #combined USA
  $no_payments_a_internacional_combined_usa = RecibeParametroNumerico('no_payments_a_internacional_combined_usa');
  $frequency_a_internacional_combined_usa = RecibeParametroHTML('frequency_a_internacional_combined_usa');
  $amount_due_a_internacional_combined_usa = RecibeParametroFlotante('amount_due_a_internacional_combined_usa');
  $amount_paid_a_internacional_combined_usa = RecibeParametroFlotante('amount_paid_a_internacional_combined_usa');
  $interes_a_internacional_combined_usa = RecibeParametroFlotante('interes_a_internacional_combined_usa');
  $no_payments_b_internacional_combined_usa =RecibeParametroNumerico('no_payments_b_internacional_combined_usa');
  $frequency_b_internacional_combined_usa = RecibeParametroHTML('frequency_b_internacional_combined_usa');
  $amount_due_b_internacional_combined_usa = RecibeParametroFlotante('amount_due_b_internacional_combined_usa');
  $amount_paid_b_internacional_combined_usa = RecibeParametroFlotante('amount_paid_b_internacional_combined_usa');
  $interes_b_internacional_combined_usa = RecibeParametroFlotante('interes_b_internacional_combined_usa');
  $no_payments_c_internacional_combined_usa = RecibeParametroNumerico('no_payments_c_internacional_combined_usa');
  $frequency_c_internacional_combined_usa = RecibeParametroHTML('frequency_c_internacional_combined_usa');
  $amount_due_c_internacional_combined_usa = RecibeParametroFlotante('amount_due_c_internacional_combined_usa');
  $amount_paid_c_internacional_combined_usa = RecibeParametroFlotante('amount_paid_c_internacional_combined_usa');
  $interes_c_internacional_combined_usa = RecibeParametroFlotante('interes_c_internacional_combined_usa');
  $no_payments_d_internacional_combined_usa = RecibeParametroNumerico('no_payments_d_internacional_combined_usa');
  $frequency_d_internacional_combined_usa = RecibeParametroHTML('frequency_d_internacional_combined_usa');
  $amount_due_d_internacional_combined_usa = RecibeParametroFlotante('amount_due_d_internacional_combined_usa');
  $amount_paid_d_internacional_combined_usa = RecibeParametroFlotante('amount_paid_d_internacional_combined_usa');
  $interes_d_internacional_combined_usa = RecibeParametroFlotante('interes_d_internacional_combined_usa');


  $fg_fulltime = RecibeParametroBinario('fg_fulltime');
  $fg_taxes = RecibeParametroBinario('fg_taxes');
  $fg_total_programa = RecibeParametroBinario('fg_total_programa');
  $fg_total_programa_internacional = RecibeParametroBinario('fg_total_programa_internacional');

  $fg_total_programa_combined = RecibeParametroBinario('fg_total_programa_combined');
  $fg_total_programa_internacional_combined = RecibeParametroBinario('fg_total_programa_internacional_combined');

  $fg_archive = RecibeParametroBinario('fg_archive');
  $mn_lecture_fee = RecibeParametroFlotante('mn_lecture_fee');
  $mn_extra_fee = RecibeParametroFlotante('mn_extra_fee');
  $fg_tax_rate = RecibeParametroBinario('fg_tax_rate');
  $fg_tax_rate_internacional = RecibeParametroBinario('fg_tax_rate_internacional');

  $fg_tax_rate_combined = RecibeParametroBinario('fg_tax_rate_combined');
  $fg_tax_rate_internacional_combined = RecibeParametroBinario('fg_tax_rate_internacional_combined');

  $fg_streams = RecibeParametroBinario('fg_streams');

  # Valor de rubric
  $no_val_rub = RecibeParametroNumerico('no_val_rub');
  $no_ter_co = RecibeParametroNumerico('no_ter_co');
  $sum_val_grade = RecibeParametroNumerico('sum_val_grade');


  $ds_career=RecibeParametroHTML('ds_career');
  $ds_objetives=RecibeParametroHTML('ds_objetives');
  $ds_teaching=RecibeParametroHTML('ds_teaching');
  $ds_evaluation=RecibeParametroHTML('ds_evaluation');
  $ds_requeriments=RecibeParametroHTML('ds_requeriments');
  $ds_program_org=RecibeParametroHTML('ds_program_org');
  $ds_combinend=RecibeParametroHTML('ds_combinend');


 $fl_paises = $_REQUEST['fl_paises'];

 $fl_paises = explode(",", $fl_paises);

 ##programas countries
 #se realiza delete  y se vuelve a insertar
 $Query = "DELETE FROM k_programa_costos_pais WHERE fl_programa=$clave AND fl_pais<>226 AND fl_pais<>38  ";
 EjecutaQuery($Query);

foreach ($fl_paises as $fl_pais) {

    if (!empty($fl_pais)) {

        $Queryp = "SELECT cl_iso2 FROM c_pais WHERE fl_pais=$fl_pais";
        $row = RecuperaValor($Queryp);
        $cl_iso = $row['cl_iso2'];

        $app_fee_ = !empty($_REQUEST['app_fee_' . $cl_iso]) ? $_REQUEST['app_fee_' . $cl_iso] : 0;
        $mn_tuition_ = !empty($_REQUEST['tuition_' . $cl_iso]) ? $_REQUEST['tuition_' . $cl_iso] : 0;
        $mn_costs_ = !empty($_REQUEST['no_costos_ad_' . $cl_iso]) ? $_REQUEST['no_costos_ad_' . $cl_iso] : 0;
        $ds_costs_ = !empty($_REQUEST['ds_costos_ad_' . $cl_iso]) ? $_REQUEST['ds_costos_ad_' . $cl_iso] : NULL;



        $no_a_payments_ = !empty($_REQUEST['no_payments_a_' . $cl_iso]) ? $_REQUEST['no_payments_a_' . $cl_iso] : 0;
        $no_b_payments_ = !empty($_REQUEST['no_payments_b_' . $cl_iso]) ? $_REQUEST['no_payments_b_' . $cl_iso] : 0;
        $no_c_payments_ = !empty($_REQUEST['no_payments_c_' . $cl_iso]) ? $_REQUEST['no_payments_c_' . $cl_iso] : 0;
        $no_d_payments_ = !empty($_REQUEST['no_payments_d_' . $cl_iso]) ? $_REQUEST['no_payments_d_' . $cl_iso] : 0;

        $ds_a_freq_ = !empty($_REQUEST['frequency_a_' . $cl_iso]) ? $_REQUEST['frequency_a_' . $cl_iso] : NULL;
        $ds_b_freq_ = !empty($_REQUEST['frequency_b_' . $cl_iso]) ? $_REQUEST['frequency_b_' . $cl_iso] : NULL;
        $ds_c_freq_ = !empty($_REQUEST['frequency_c_' . $cl_iso]) ? $_REQUEST['frequency_c_' . $cl_iso] : NULL;
        $ds_d_freq_ = !empty($_REQUEST['frequency_d_' . $cl_iso]) ? $_REQUEST['frequency_d_' . $cl_iso] : NULL;

        $mn_a_due_ = !empty($_REQUEST['amount_due_a_' . $cl_iso]) ? $_REQUEST['amount_due_a_' . $cl_iso] : 0;
        $mn_b_due_ = !empty($_REQUEST['amount_due_b_' . $cl_iso]) ? $_REQUEST['amount_due_b_' . $cl_iso] : 0;
        $mn_c_due_ = !empty($_REQUEST['amount_due_c_' . $cl_iso]) ? $_REQUEST['amount_due_c_' . $cl_iso] : 0;
        $mn_d_due_ = !empty($_REQUEST['amount_due_d_' . $cl_iso]) ? $_REQUEST['amount_due_d_' . $cl_iso] : 0;

        $mn_a_paid_ = !empty($_REQUEST['amount_paid_a_' . $cl_iso]) ? $_REQUEST['amount_paid_a_' . $cl_iso] : 0;
        $mn_b_paid_ = !empty($_REQUEST['amount_paid_b_' . $cl_iso]) ? $_REQUEST['amount_paid_b_' . $cl_iso] : 0;
        $mn_c_paid_ = !empty($_REQUEST['amount_paid_c_' . $cl_iso]) ? $_REQUEST['amount_paid_c_' . $cl_iso] : 0;
        $mn_d_paid_ = !empty($_REQUEST['amount_paid_d_' . $cl_iso]) ? $_REQUEST['amount_paid_d_' . $cl_iso] : 0;

        $no_a_interes_ = !empty($_REQUEST['interes_a_' . $cl_iso]) ? $_REQUEST['interes_a_' . $cl_iso] : 0;
        $no_b_interes_ = !empty($_REQUEST['interes_b_' . $cl_iso]) ? $_REQUEST['interes_b_' . $cl_iso] : 0;
        $no_c_interes_ = !empty($_REQUEST['interes_c_' . $cl_iso]) ? $_REQUEST['interes_c_' . $cl_iso] : 0;
        $no_d_interes_ = !empty($_REQUEST['interes_d_' . $cl_iso]) ? $_REQUEST['interes_d_' . $cl_iso] : 0;

        $mn_app_fee_internacional_ = !empty($_REQUEST['app_fee_internacional_' . $cl_iso]) ? $_REQUEST['app_fee_internacional_' . $cl_iso] : 0;
        $mn_tuition_internacional_ = !empty($_REQUEST['tuition_internacional_' . $cl_iso]) ? $_REQUEST['tuition_internacional_' . $cl_iso] : 0;
        $mn_costs_internacional_ = !empty($_REQUEST['no_costos_ad_internacional_' . $cl_iso]) ? $_REQUEST['no_costos_ad_internacional_' . $cl_iso] : 0;
        $ds_costs_internacional_ = !empty($_REQUEST['ds_costos_ad_internacional_' . $cl_iso]) ? $_REQUEST['ds_costos_ad_internacional_' . $cl_iso] : NULL;

        $no_a_payments_internacional_ = !empty($_REQUEST['no_payments_a_internacional_' . $cl_iso]) ? $_REQUEST['no_payments_a_internacional_' . $cl_iso] : 0;
        $no_b_payments_internacional_ = !empty($_REQUEST['no_payments_b_internacional_' . $cl_iso]) ? $_REQUEST['no_payments_b_internacional_' . $cl_iso] : 0;
        $no_c_payments_internacional_ = !empty($_REQUEST['no_payments_c_internacional_' . $cl_iso]) ? $_REQUEST['no_payments_c_internacional_' . $cl_iso] : 0;
        $no_d_payments_internacional_ = !empty($_REQUEST['no_payments_d_internacional_' . $cl_iso]) ? $_REQUEST['no_payments_d_internacional_' . $cl_iso] : 0;

        $ds_a_freq_internacional_ = !empty($_REQUEST['frequency_a_internacional_' . $cl_iso]) ? $_REQUEST['frequency_a_internacional_' . $cl_iso] : NULL;
        $ds_b_freq_internacional_ = !empty($_REQUEST['frequency_b_internacional_' . $cl_iso]) ? $_REQUEST['frequency_b_internacional_' . $cl_iso] : NULL;
        $ds_c_freq_internacional_ = !empty($_REQUEST['frequency_c_internacional_' . $cl_iso]) ? $_REQUEST['frequency_c_internacional_' . $cl_iso] : NULL;
        $ds_d_freq_internacional_ = !empty($_REQUEST['frequency_d_internacional_' . $cl_iso]) ? $_REQUEST['frequency_d_internacional_' . $cl_iso] : NULL;

        $mn_a_due_internacional_ = !empty($_REQUEST['amount_due_a_internacional_' . $cl_iso]) ? $_REQUEST['amount_due_a_internacional_' . $cl_iso] : 0;
        $mn_b_due_internacional_ = !empty($_REQUEST['amount_due_b_internacional_' . $cl_iso]) ? $_REQUEST['amount_due_b_internacional_' . $cl_iso] : 0;
        $mn_c_due_internacional_ = !empty($_REQUEST['amount_due_c_internacional_' . $cl_iso]) ? $_REQUEST['amount_due_c_internacional_' . $cl_iso] : 0;
        $mn_d_due_internacional_ = !empty($_REQUEST['amount_due_d_internacional_' . $cl_iso]) ? $_REQUEST['amount_due_d_internacional_' . $cl_iso] : 0;

        $mn_a_paid_internacional_ = !empty($_REQUEST['amount_paid_a_internacional_' . $cl_iso]) ? $_REQUEST['amount_paid_a_internacional_' . $cl_iso] : 0;
        $mn_b_paid_internacional_ = !empty($_REQUEST['amount_paid_b_internacional_' . $cl_iso]) ? $_REQUEST['amount_paid_b_internacional_' . $cl_iso] : 0;
        $mn_c_paid_internacional_ = !empty($_REQUEST['amount_paid_c_internacional_' . $cl_iso]) ? $_REQUEST['amount_paid_c_internacional_' . $cl_iso] : 0;
        $mn_d_paid_internacional_ = !empty($_REQUEST['amount_paid_d_internacional_' . $cl_iso]) ? $_REQUEST['amount_paid_d_internacional_' . $cl_iso] : 0;

        $no_a_interes_internacional_ = !empty($_REQUEST['interes_a_internacional_' . $cl_iso]) ? $_REQUEST['interes_a_internacional_' . $cl_iso] : 0;
        $no_b_interes_internacional_ = !empty($_REQUEST['interes_b_internacional_' . $cl_iso]) ? $_REQUEST['interes_b_internacional_' . $cl_iso] : 0;
        $no_c_interes_internacional_ = !empty($_REQUEST['interes_c_internacional_' . $cl_iso]) ? $_REQUEST['interes_c_internacional_' . $cl_iso] : 0;
        $no_d_interes_internacional_ = !empty($_REQUEST['interes_d_internacional_' . $cl_iso]) ? $_REQUEST['interes_d_internacional_' . $cl_iso] : 0;

        $mn_app_fee_combined_ = !empty($_REQUEST['app_fee_combined_' . $cl_iso]) ? $_REQUEST['app_fee_combined_' . $cl_iso] : 0;
        $mn_tuition_combined_ = !empty($_REQUEST['tuition_combined_' . $cl_iso]) ? $_REQUEST['tuition_combined_' . $cl_iso] : 0;
        $mn_costs_combined_ = !empty($_REQUEST['no_costos_ad_combined_' . $cl_iso]) ? $_REQUEST['no_costos_ad_combined_' . $cl_iso] : 0;
        $ds_costs_combined_ = !empty($_REQUEST['ds_costos_ad_combined_' . $cl_iso]) ? $_REQUEST['ds_costos_ad_combined_' . $cl_iso] : NULL;

        $no_a_payments_combined_ = !empty($_REQUEST['no_payments_a_combined_' . $cl_iso]) ? $_REQUEST['no_payments_a_combined_' . $cl_iso] : 0;
        $no_b_payments_combined_ = !empty($_REQUEST['no_payments_b_combined_' . $cl_iso]) ? $_REQUEST['no_payments_b_combined_' . $cl_iso] : 0;
        $no_c_payments_combined_ = !empty($_REQUEST['no_payments_c_combined_' . $cl_iso]) ? $_REQUEST['no_payments_c_combined_' . $cl_iso] : 0;
        $no_d_payments_combined_ = !empty($_REQUEST['no_payments_d_combined_' . $cl_iso]) ? $_REQUEST['no_payments_d_combined_' . $cl_iso] : 0;

        $ds_a_freq_combined_ = !empty($_REQUEST['frequency_a_combined_' . $cl_iso]) ? $_REQUEST['frequency_a_combined_' . $cl_iso] : NULL;
        $ds_b_freq_combined_ = !empty($_REQUEST['frequency_b_combined_' . $cl_iso]) ? $_REQUEST['frequency_b_combined_' . $cl_iso] : NULL;
        $ds_c_freq_combined_ = !empty($_REQUEST['frequency_c_combined_' . $cl_iso]) ? $_REQUEST['frequency_c_combined_' . $cl_iso] : NULL;
        $ds_d_freq_combined_ = !empty($_REQUEST['frequency_d_combined_' . $cl_iso]) ? $_REQUEST['frequency_d_combined_' . $cl_iso] : NULL;

        $mn_a_due_combined_ = !empty($_REQUEST['amount_due_a_combined_' . $cl_iso]) ? $_REQUEST['amount_due_a_combined_' . $cl_iso] : 0;
        $mn_b_due_combined_ = !empty($_REQUEST['amount_due_b_combined_' . $cl_iso]) ? $_REQUEST['amount_due_b_combined_' . $cl_iso] : 0;
        $mn_c_due_combined_ = !empty($_REQUEST['amount_due_c_combined_' . $cl_iso]) ? $_REQUEST['amount_due_c_combined_' . $cl_iso] : 0;
        $mn_d_due_combined_ = !empty($_REQUEST['amount_due_d_combined_' . $cl_iso]) ? $_REQUEST['amount_due_d_combined_' . $cl_iso] : 0;

        $mn_a_paid_combined_ = !empty($_REQUEST['amount_paid_a_combined_' . $cl_iso]) ? $_REQUEST['amount_paid_a_combined_' . $cl_iso] : 0;
        $mn_b_paid_combined_ = !empty($_REQUEST['amount_paid_b_combined_' . $cl_iso]) ? $_REQUEST['amount_paid_b_combined_' . $cl_iso] : 0;
        $mn_c_paid_combined_ = !empty($_REQUEST['amount_paid_c_combined_' . $cl_iso]) ? $_REQUEST['amount_paid_c_combined_' . $cl_iso] : 0;
        $mn_d_paid_combined_ = !empty($_REQUEST['amount_paid_d_combined_' . $cl_iso]) ? $_REQUEST['amount_paid_d_combined_' . $cl_iso] : 0;

        $no_a_interes_combined_ = !empty($_REQUEST['interes_a_combined_' . $cl_iso]) ? $_REQUEST['interes_a_combined_' . $cl_iso] : 0;
        $no_b_interes_combined_ = !empty($_REQUEST['interes_b_combined_' . $cl_iso]) ? $_REQUEST['interes_b_combined_' . $cl_iso] : 0;
        $no_c_interes_combined_ = !empty($_REQUEST['interes_c_combined_' . $cl_iso]) ? $_REQUEST['interes_c_combined_' . $cl_iso] : 0;
        $no_d_interes_combined_ = !empty($_REQUEST['interes_d_combined_' . $cl_iso]) ? $_REQUEST['interes_d_combined_' . $cl_iso] : 0;

        $mn_app_fee_internacional_combined_ = !empty($_REQUEST['app_fee_internacional_combined_' . $cl_iso]) ? $_REQUEST['app_fee_internacional_combined_' . $cl_iso] : 0;
        $mn_tuition_internacional_combined_ = !empty($_REQUEST['tuition_internacional_combined_' . $cl_iso]) ? $_REQUEST['tuition_internacional_combined_' . $cl_iso] : 0;
        $mn_costs_internacional_combined_ = !empty($_REQUEST['no_costos_ad_internacional_combined_' . $cl_iso]) ? $_REQUEST['no_costos_ad_internacional_combined_' . $cl_iso] : 0;
        $ds_costs_internacional_combined_ = !empty($_REQUEST['ds_costos_ad_internacional_combined_' . $cl_iso]) ? $_REQUEST['ds_costos_ad_internacional_combined_' . $cl_iso] : NULL;

        $no_a_payments_internacional_combined_ = !empty($_REQUEST['no_payments_a_internacional_combined_' . $cl_iso]) ? $_REQUEST['no_payments_a_internacional_combined_' . $cl_iso] : 0;
        $no_b_payments_internacional_combined_ = !empty($_REQUEST['no_payments_b_internacional_combined_' . $cl_iso]) ? $_REQUEST['no_payments_b_internacional_combined_' . $cl_iso] : 0;
        $no_c_payments_internacional_combined_ = !empty($_REQUEST['no_payments_c_internacional_combined_' . $cl_iso]) ? $_REQUEST['no_payments_c_internacional_combined_' . $cl_iso] : 0;
        $no_d_payments_internacional_combined_ = !empty($_REQUEST['no_payments_d_internacional_combined_' . $cl_iso]) ? $_REQUEST['no_payments_d_internacional_combined_' . $cl_iso] : 0;

        $ds_a_freq_internacional_combined_ = !empty($_REQUEST['frequency_a_internacional_combined_' . $cl_iso]) ? $_REQUEST['frequency_a_internacional_combined_' . $cl_iso] : NULL;
        $ds_b_freq_internacional_combined_ = !empty($_REQUEST['frequency_b_internacional_combined_' . $cl_iso]) ? $_REQUEST['frequency_b_internacional_combined_' . $cl_iso] : NULL;
        $ds_c_freq_internacional_combined_ = !empty($_REQUEST['frequency_c_internacional_combined_' . $cl_iso]) ? $_REQUEST['frequency_c_internacional_combined_' . $cl_iso] : NULL;
        $ds_d_freq_internacional_combined_ = !empty($_REQUEST['frequency_d_internacional_combined_' . $cl_iso]) ? $_REQUEST['frequency_d_internacional_combined_' . $cl_iso] : NULL;

        $mn_a_due_internacional_combined_ = !empty($_REQUEST['amount_due_a_internacional_combined_' . $cl_iso]) ? $_REQUEST['amount_due_a_internacional_combined_' . $cl_iso] : 0;
        $mn_b_due_internacional_combined_ = !empty($_REQUEST['amount_due_b_internacional_combined_' . $cl_iso]) ? $_REQUEST['amount_due_b_internacional_combined_' . $cl_iso] : 0;
        $mn_c_due_internacional_combined_ = !empty($_REQUEST['amount_due_c_internacional_combined_' . $cl_iso]) ? $_REQUEST['amount_due_c_internacional_combined_' . $cl_iso] : 0;
        $mn_d_due_internacional_combined_ = !empty($_REQUEST['amount_due_d_internacional_combined_' . $cl_iso]) ? $_REQUEST['amount_due_d_internacional_combined_' . $cl_iso] : 0;

        $mn_a_paid_internacional_combined_ = !empty($_REQUEST['amount_paid_a_internacional_combined_' . $cl_iso]) ? $_REQUEST['amount_paid_a_internacional_combined_' . $cl_iso] : 0;
        $mn_b_paid_internacional_combined_ = !empty($_REQUEST['amount_paid_b_internacional_combined_' . $cl_iso]) ? $_REQUEST['amount_paid_b_internacional_combined_' . $cl_iso] : 0;
        $mn_c_paid_internacional_combined_ = !empty($_REQUEST['amount_paid_c_internacional_combined_' . $cl_iso]) ? $_REQUEST['amount_paid_c_internacional_combined_' . $cl_iso] : 0;
        $mn_d_paid_internacional_combined_ = !empty($_REQUEST['amount_paid_d_internacional_combined_' . $cl_iso]) ? $_REQUEST['amount_paid_d_internacional_combined_' . $cl_iso] : 0;

        $no_a_interes_internacional_combined_ = !empty($_REQUEST['interes_a_internacional_combined_' . $cl_iso]) ? $_REQUEST['interes_a_internacional_combined_' . $cl_iso] : 0;
        $no_b_interes_internacional_combined_ = !empty($_REQUEST['interes_b_internacional_combined_' . $cl_iso]) ? $_REQUEST['interes_b_internacional_combined_' . $cl_iso] : 0;
        $no_c_interes_internacional_combined_ = !empty($_REQUEST['interes_c_internacional_combined_' . $cl_iso]) ? $_REQUEST['interes_c_internacional_combined_' . $cl_iso] : 0;
        $no_d_interes_internacional_combined_ = !empty($_REQUEST['interes_d_internacional_combined_' . $cl_iso]) ? $_REQUEST['interes_d_internacional_combined_' . $cl_iso] : 0;

        $fg_total_programa_ = ($_REQUEST['fg_total_programa_' . $cl_iso] == 'on') ? '1' : '0';
        $fg_taxes_ = ($_REQUEST['fg_tax_rate_' . $cl_iso] == 'on') ? '1' : '0';

        $fg_total_programa_internacional_ = ($_REQUEST['fg_total_programa_internacional_' . $cl_iso] == 'on') ? '1' : '0';
        $fg_taxes_internacional_ = ($_REQUEST['fg_tax_rate_internacional_' . $cl_iso] == 'on') ? '1' : '0';

        $fg_total_programa_combined_ = ($_REQUEST['fg_total_programa_combined_' . $cl_iso] == 'on') ? '1' : '0';
        $fg_taxes_combined_ = ($_REQUEST['fg_tax_rate_combined_' . $cl_iso] == 'on') ? '1' : '0';

        $fg_total_programa_internacional_combined_ = ($_REQUEST['fg_total_programa_internacional_combined_' . $cl_iso] == 'on') ? '1' : '0';
        $fg_taxes_internacional_combined_ = ($_REQUEST['fg_tax_rate_internacional_combined_' . $cl_iso] == 'on') ? '1' : '0';





        #Insert by countries
        $Query = "INSERT INTO k_programa_costos_pais (fl_programa,fl_pais,
                    mn_app_fee,mn_tuition,mn_costs,ds_costs,
                    no_a_payments,no_b_payments,no_c_payments,no_d_payments,
                    ds_a_freq,ds_b_freq,ds_c_freq,ds_d_freq,
                    mn_a_due,mn_b_due,mn_c_due,mn_d_due,
                    mn_a_paid,mn_b_paid,mn_c_paid,mn_d_paid,
                    no_a_interes,no_b_interes,no_c_interes,no_d_interes,
                    mn_app_fee_internacional,mn_tuition_internacional,mn_costs_internacional,ds_costs_internacional,
                    no_a_payments_internacional,no_b_payments_internacional,no_c_payments_internacional,no_d_payments_internacional,
                    ds_a_freq_internacional,ds_b_freq_internacional,ds_c_freq_internacional,ds_d_freq_internacional,
                    mn_a_due_internacional,mn_b_due_internacional,mn_c_due_internacional,mn_d_due_internacional,
                    mn_a_paid_internacional,mn_b_paid_internacional,mn_c_paid_internacional,mn_d_paid_internacional,
                    no_a_interes_internacional,no_b_interes_internacional,no_c_interes_internacional,no_d_interes_internacional,
                    mn_app_fee_combined,mn_tuition_combined,mn_costs_combined,ds_costs_combined,
                    no_a_payments_combined,no_b_payments_combined,no_c_payments_combined,no_d_payments_combined,
                    ds_a_freq_combined,ds_b_freq_combined,ds_c_freq_combined,ds_d_freq_combined,
                    mn_a_due_combined,mn_b_due_combined,mn_c_due_combined,mn_d_due_combined,
                    mn_a_paid_combined,mn_b_paid_combined,mn_c_paid_combined,mn_d_paid_combined,
                    no_a_interes_combined,no_b_interes_combined,no_c_interes_combined,no_d_interes_combined,
                    mn_app_fee_internacional_combined,mn_tuition_internacional_combined,mn_costs_internacional_combined,ds_costs_internacional_combined,
                    no_a_payments_internacional_combined,no_b_payments_internacional_combined,no_c_payments_internacional_combined,no_d_payments_internacional_combined,
                    ds_a_freq_internacional_combined,ds_b_freq_internacional_combined,ds_c_freq_internacional_combined,ds_d_freq_internacional_combined,
                    mn_a_due_internacional_combined,mn_b_due_internacional_combined,mn_c_due_internacional_combined,mn_d_due_internacional_combined,
                    mn_a_paid_internacional_combined,mn_b_paid_internacional_combined,mn_c_paid_internacional_combined,mn_d_paid_internacional_combined,
                    no_a_interes_internacional_combined,no_b_interes_internacional_combined,no_c_interes_internacional_combined,no_d_interes_internacional_combined,
                    fg_total_programa,fg_taxes,fg_total_programa_internacional,fg_taxes_internacional,fg_total_programa_combined,fg_taxes_combined,fg_total_programa_internacional_combined,fg_taxes_internacional_combined)

                    ";
        $Query .= "VALUES($clave,$fl_pais,$app_fee_,$mn_tuition_,$mn_costs_,'$ds_costs_',
	    $no_a_payments_,$no_b_payments_,$no_c_payments_,$no_d_payments_,
	    '$ds_a_freq_','$ds_b_freq_','$ds_c_freq_','$ds_d_freq_',
	    $mn_a_due_,$mn_b_due_,$mn_c_due_,$mn_d_due_,
	    $mn_a_paid_,$mn_b_paid_,$mn_c_paid_,$mn_d_paid_,
	    $no_a_interes_,$no_b_interes_,$no_c_interes_,$no_d_interes_,
	    $mn_app_fee_internacional_,$mn_tuition_internacional_,$mn_costs_internacional_,'$ds_costs_internacional_',
	    $no_a_payments_internacional_,$no_b_payments_internacional_,$no_c_payments_internacional_,$no_d_payments_internacional_,
	    '$ds_a_freq_internacional_','$ds_b_freq_internacional_','$ds_c_freq_internacional_','$ds_d_freq_internacional_',
	    $mn_a_due_internacional_,$mn_b_due_internacional_,$mn_c_due_internacional_,$mn_d_due_internacional_,
	    $mn_a_paid_internacional_,$mn_b_paid_internacional_,$mn_c_paid_internacional_,$mn_d_paid_internacional_,
	    $no_a_interes_internacional_,$no_b_interes_internacional_,$no_c_interes_internacional_,$no_d_interes_internacional_,
	    $mn_app_fee_combined_,$mn_tuition_combined_,$mn_costs_combined_,'$ds_costs_combined_',
	    $no_a_payments_combined_,$no_b_payments_combined_,$no_c_payments_combined_,$no_d_payments_combined_,
	    '$ds_a_freq_combined_','$ds_b_freq_combined_','$ds_c_freq_combined_','$ds_d_freq_combined_',
	    $mn_a_due_combined_,$mn_b_due_combined_,$mn_c_due_combined_,$mn_d_due_combined_,
	    $mn_a_paid_combined_,$mn_b_paid_combined_,$mn_c_paid_combined_,$mn_d_paid_combined_,
	    $no_a_interes_combined_,$no_b_interes_combined_,$no_c_interes_combined_,$no_d_interes_combined_,
	    $mn_app_fee_internacional_combined_,$mn_tuition_internacional_combined_,$mn_costs_internacional_combined_,'$ds_costs_internacional_combined_',
	    $no_a_payments_internacional_combined_,$no_b_payments_internacional_combined_,$no_c_payments_internacional_combined_,$no_d_payments_internacional_combined_,
	    '$ds_a_freq_internacional_combined_','$ds_b_freq_internacional_combined_','$ds_c_freq_internacional_combined_','$ds_d_freq_internacional_combined_',
	    $mn_a_due_internacional_combined_,$mn_b_due_internacional_combined_,$mn_c_due_internacional_combined_,$mn_d_due_internacional_combined_,
	    $mn_a_paid_internacional_combined_,$mn_b_paid_internacional_combined_,$mn_c_paid_internacional_combined_,$mn_d_paid_internacional_combined_,
	    $no_a_interes_internacional_combined_,$no_b_interes_internacional_combined_,$no_c_interes_internacional_combined_,$no_d_interes_internacional_combined_,
	    '$fg_total_programa_','$fg_taxes_',
	    '$fg_total_programa_internacional_','$fg_taxes_internacional_',
	    '$fg_total_programa_combined_','$fg_taxes_combined_',
	    '$fg_total_programa_internacional_combined_','$fg_taxes_internacional_combined_'
	    )";
        $fl_insert = EjecutaInsert($Query);

    }

}






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
  if(empty($app_fee_internacional)) $app_fee_internacional=0;
  if(empty($tuition)) $tuition = 0;
  if(empty($tuition_internacional)) $tuition_internacional = 0;
  if(empty($no_costos_ad)) $no_costos_ad = 0;
  if(empty($no_costos_ad_internacional)) $no_costos_ad_internacional=0;
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

  #usa
  if(empty($app_fee_usa)) $app_fee_usa = 0;
  if(empty($app_fee_internacional_usa)) $app_fee_internacional_usa=0;
  if(empty($tuition_usa)) $tuition_usa = 0;
  if(empty($tuition_internacional_usa)) $tuition_internacional_usa = 0;
  if(empty($no_costos_ad_usa)) $no_costos_ad_usa = 0;
  if(empty($no_costos_ad_internacional_usa)) $no_costos_ad_internacional_usa=0;
  if(empty($no_payments_a_usa)) $no_payments_a_usa = 0;
  if(empty($amount_due_a_usa)) $amount_due_a_usa = 0;
  if(empty($amount_paid_a_usa)) $amount_paid_a_usa = 0;
  if(empty($interes_a_usa)) $interes_a_usa = 0;
  if(empty($no_payments_b_usa)) $no_payments_b_usa = 0;
  if(empty($amount_due_b_usa)) $amount_due_b_usa = 0;
  if(empty($amount_paid_b_usa)) $amount_paid_b_usa = 0;
  if(empty($interes_b_usa)) $interes_b_usa = 0;
  if(empty($no_payments_c_usa)) $no_payments_c_usa = 0;
  if(empty($amount_due_c_usa)) $amount_due_c_usa = 0;
  if(empty($amount_paid_c_usa)) $amount_paid_c_usa = 0;
  if(empty($interes_c_usa)) $interes_c_usa = 0;
  if(empty($no_payments_d_usa)) $no_payments_d_usa = 0;
  if(empty($amount_due_d_usa)) $amount_due_d_usa = 0;
  if(empty($amount_paid_d_usa)) $amount_paid_d_usa = 0;
  if(empty($interes_d_usa)) $interes_d_usa = 0;
  if(empty($mn_lecture_fee_usa)) $mn_lecture_fee_usa = 0;
  if(empty($mn_extra_fee_usa)) $mn_extra_fee_usa = 0;

  #Online  Internacional
  if(empty($no_payments_a_internacional)) $no_payments_a_internacional = 0;
  if(empty($amount_due_a_internacional)) $amount_due_a_internacional = 0;
  if(empty($amount_paid_a_internacional)) $amount_paid_a_internacional = 0;
  if(empty($interes_a_internacional)) $interes_a_internacional = 0;
  if(empty($no_payments_b_internacional)) $no_payments_b_internacional = 0;
  if(empty($amount_due_b_internacional)) $amount_due_b_internacional = 0;
  if(empty($amount_paid_b_internacional)) $amount_paid_b_internacional = 0;
  if(empty($interes_b_internacional)) $interes_b_internacional = 0;
  if(empty($no_payments_c_internacional)) $no_payments_c_internacional = 0;
  if(empty($amount_due_c_internacional)) $amount_due_c_internacional = 0;
  if(empty($amount_paid_c_internacional)) $amount_paid_c_internacional = 0;
  if(empty($interes_c_internacional)) $interes_c_internacional = 0;
  if(empty($no_payments_d_internacional)) $no_payments_d_internacional = 0;
  if(empty($amount_due_d_internacional)) $amount_due_d_internacional = 0;
  if(empty($amount_paid_d_internacional)) $amount_paid_d_internacional = 0;
  if(empty($interes_d_internacional)) $interes_d_internacional = 0;

  #USA
  if(empty($no_payments_a_internacional_usa)) $no_payments_a_internacional_usa = 0;
  if(empty($amount_due_a_internacional_usa)) $amount_due_a_internacional_usa = 0;
  if(empty($amount_paid_a_internacional_usa)) $amount_paid_a_internacional_usa = 0;
  if(empty($interes_a_internacional_usa)) $interes_a_internacional_usa = 0;
  if(empty($no_payments_b_internacional_usa)) $no_payments_b_internacional_usa = 0;
  if(empty($amount_due_b_internacional_usa)) $amount_due_b_internacional_usa = 0;
  if(empty($amount_paid_b_internacional_usa)) $amount_paid_b_internacional_usa = 0;
  if(empty($interes_b_internacional_usa)) $interes_b_internacional_usa = 0;
  if(empty($no_payments_c_internacional_usa)) $no_payments_c_internacional_usa = 0;
  if(empty($amount_due_c_internacional_usa)) $amount_due_c_internacional_usa = 0;
  if(empty($amount_paid_c_internacional_usa)) $amount_paid_c_internacional_usa = 0;
  if(empty($interes_c_internacional_usa)) $interes_c_internacional_usa = 0;
  if(empty($no_payments_d_internacional_usa)) $no_payments_d_internacional_usa = 0;
  if(empty($amount_due_d_internacional_usa)) $amount_due_d_internacional_usa = 0;
  if(empty($amount_paid_d_internacional_usa)) $amount_paid_d_internacional_usa = 0;
  if(empty($interes_d_internacional_usa)) $interes_d_internacional_usa = 0;


  #Combined Canada
  if(empty($app_fee_combined)) $app_fee_combined = 0;
  if(empty($app_fee_internacional_combined)) $app_fee_internacional_combined=0;
  if(empty($tuition_combined)) $tuition_combined = 0;
  if(empty($tuition_internacional_combined)) $tuition_internacional_combined = 0;
  if(empty($no_costos_ad_combined)) $no_costos_ad_combined = 0;
  if(empty($no_costos_ad_internacional_combined)) $no_costos_ad_internacional_combined=0;
  if(empty($no_payments_a_combined)) $no_payments_a_combined = 0;
  if(empty($amount_due_a_combined)) $amount_due_a_combined = 0;
  if(empty($amount_paid_a_combined)) $amount_paid_a_combined = 0;
  if(empty($interes_a_combined)) $interes_a_combined = 0;
  if(empty($no_payments_b_combined)) $no_payments_b_combined = 0;
  if(empty($amount_due_b_combined)) $amount_due_b_combined = 0;
  if(empty($amount_paid_b_combined)) $amount_paid_b_combined = 0;
  if(empty($interes_b_combined)) $interes_b_combined = 0;
  if(empty($no_payments_c_combined)) $no_payments_c_combined = 0;
  if(empty($amount_due_c_combined)) $amount_due_c_combined = 0;
  if(empty($amount_paid_c_combined)) $amount_paid_c_combined = 0;
  if(empty($interes_c_combined)) $interes_c_combined = 0;
  if(empty($no_payments_d_combined)) $no_payments_d_combined = 0;
  if(empty($amount_due_d_combined)) $amount_due_d_combined = 0;
  if(empty($amount_paid_d_combined)) $amount_paid_d_combined = 0;
  if(empty($interes_d_combined)) $interes_d_combined = 0;

  #Combined Internacional
  if(empty($no_payments_a_internacional_combined)) $no_payments_a_internacional_combined = 0;
  if(empty($amount_due_a_internacional_combined)) $amount_due_a_internacional_combined = 0;
  if(empty($amount_paid_a_internacional_combined)) $amount_paid_a_internacional_combined = 0;
  if(empty($interes_a_internacional_combined)) $interes_a_internacional_combined = 0;
  if(empty($no_payments_b_internacional_combined)) $no_payments_b_internacional_combined = 0;
  if(empty($amount_due_b_internacional_combined)) $amount_due_b_internacional_combined = 0;
  if(empty($amount_paid_b_internacional_combined)) $amount_paid_b_internacional_combined = 0;
  if(empty($interes_b_internacional_combined)) $interes_b_internacional_combined = 0;
  if(empty($no_payments_c_internacional_combined)) $no_payments_c_internacional_combined = 0;
  if(empty($amount_due_c_internacional_combined)) $amount_due_c_internacional_combined = 0;
  if(empty($amount_paid_c_internacional_combined)) $amount_paid_c_internacional_combined = 0;
  if(empty($interes_c_internacional_combined)) $interes_c_internacional_combined = 0;
  if(empty($no_payments_d_internacional_combined)) $no_payments_d_internacional_combined = 0;
  if(empty($amount_due_d_internacional_combined)) $amount_due_d_internacional_combined = 0;
  if(empty($amount_paid_d_internacional_combined)) $amount_paid_d_internacional_combined = 0;
  if(empty($interes_d_internacional_combined)) $interes_d_internacional_combined = 0;


  #USA
  if(empty($app_fee_combined_usa)) $app_fee_combined_usa = 0;
  if(empty($app_fee_internacional_combined_usa)) $app_fee_internacional_combined_usa=0;
  if(empty($tuition_combined_usa)) $tuition_combined_usa = 0;
  if(empty($tuition_internacional_combined_usa)) $tuition_internacional_combined_usa = 0;
  if(empty($no_costos_ad_combined_usa)) $no_costos_ad_combined_usa = 0;
  if(empty($no_costos_ad_internacional_combined_usa)) $no_costos_ad_internacional_combined_usa=0;
  if(empty($no_payments_a_combined_usa)) $no_payments_a_combined_usa = 0;
  if(empty($amount_due_a_combined_usa)) $amount_due_a_combined_usa = 0;
  if(empty($amount_paid_a_combined_usa)) $amount_paid_a_combined_usa = 0;
  if(empty($interes_a_combined_usa)) $interes_a_combined_usa = 0;
  if(empty($no_payments_b_combined_usa)) $no_payments_b_combined_usa = 0;
  if(empty($amount_due_b_combined_usa)) $amount_due_b_combined_usa = 0;
  if(empty($amount_paid_b_combined_usa)) $amount_paid_b_combined_usa = 0;
  if(empty($interes_b_combined_usa)) $interes_b_combined_usa = 0;
  if(empty($no_payments_c_combined_usa)) $no_payments_c_combined_usa = 0;
  if(empty($amount_due_c_combined_usa)) $amount_due_c_combined_usa = 0;
  if(empty($amount_paid_c_combined_usa)) $amount_paid_c_combined_usa = 0;
  if(empty($interes_c_combined_usa)) $interes_c_combined_usa = 0;
  if(empty($no_payments_d_combined_usa)) $no_payments_d_combined_usa = 0;
  if(empty($amount_due_d_combined_usa)) $amount_due_d_combined_usa = 0;
  if(empty($amount_paid_d_combined_usa)) $amount_paid_d_combined_usa = 0;
  if(empty($interes_d_combined_usa)) $interes_d_combined_usa = 0;

  #Combined Internacional USA
  if(empty($no_payments_a_internacional_combined_usa)) $no_payments_a_internacional_combined_usa = 0;
  if(empty($amount_due_a_internacional_combined_usa)) $amount_due_a_internacional_combined_usa = 0;
  if(empty($amount_paid_a_internacional_combined_usa)) $amount_paid_a_internacional_combined_usa = 0;
  if(empty($interes_a_internacional_combined_usa)) $interes_a_internacional_combined_usa = 0;
  if(empty($no_payments_b_internacional_combined_usa)) $no_payments_b_internacional_combined_usa = 0;
  if(empty($amount_due_b_internacional_combined_usa)) $amount_due_b_internacional_combined_usa = 0;
  if(empty($amount_paid_b_internacional_combined_usa)) $amount_paid_b_internacional_combined_usa = 0;
  if(empty($interes_b_internacional_combined_usa)) $interes_b_internacional_combined_usa = 0;
  if(empty($no_payments_c_internacional_combined_usa)) $no_payments_c_internacional_combined_usa = 0;
  if(empty($amount_due_c_internacional_combined_usa)) $amount_due_c_internacional_combined_usa = 0;
  if(empty($amount_paid_c_internacional_combined_usa)) $amount_paid_c_internacional_combined_usa = 0;
  if(empty($interes_c_internacional_combined_usa)) $interes_c_internacional_combined_usa = 0;
  if(empty($no_payments_d_internacional_combined_usa)) $no_payments_d_internacional_combined_usa = 0;
  if(empty($amount_due_d_internacional_combined_usa)) $amount_due_d_internacional_combined_usa = 0;
  if(empty($amount_paid_d_internacional_combined_usa)) $amount_paid_d_internacional_combined_usa = 0;
  if(empty($interes_d_internacional_combined_usa)) $interes_d_internacional_combined_usa = 0;



  # Valida enteros
  if($no_orden > MAX_TINYINT)
    $no_orden_err = ERR_TINYINT;
  if($no_grados > MAX_TINYINT)
    $no_grados_err = ERR_TINYINT;
  if($no_horas > MAX_SMALLINT)
    $no_horas_err = ERR_SMALLINT;
  if($no_semanas > MAX_TINYINT)
    $no_semanas_err = ERR_TINYINT;



  /* Validacion de rubric */
  # 1- Validamos si NO existenten criterios y el rubric tiene valor
  if($no_val_rub > 0){
      $cont_criterios = RecuperaValor("SELECT COUNT(1) FROM k_criterio_curso WHERE fl_programa = $clave");
      if(empty($cont_criterios[0]))
          $no_val_rub_err = 1; // No hay registros en tabla
  }

  # 2- Validamos SI existenten criterios y el rubric NO tiene valor
  $cont_criterios = RecuperaValor("SELECT COUNT(1) FROM k_criterio_curso WHERE fl_programa = $clave");
  if(($cont_criterios[0]) AND empty($no_val_rub))
      $no_val_rub_err = 2; // Hay registros en tabla, pero el rubric no tiene valor

  # 3- Validamos que todos los criterios tengan un valor
  $cont_criterios = RecuperaValor("SELECT COUNT(1) FROM k_criterio_curso WHERE fl_programa = $clave AND no_valor IS NULL");
  if(($cont_criterios[0]))
      $no_val_rub_err = 3; // Existen criterios sin valor asignado

  # 4- Validamos el max grade
  // Es mayor a 100
  if($sum_val_grade > 100)
      $no_max_grade_err = 1;

  if( ($no_val_rub > 0)&& ($no_val_rub < 100 )){
      $no_val_rub_err = 2;

	 // $no_ter_co=100 - $no_val_rub;
  }
  // Valida el valor de los criterios
  if($sum_val_grade != 100 and $sum_val_grade > 0)
      $no_max_grade_err = 2;

  // Comprobamos error para marcar tab
  if(!empty($no_val_rub_err) OR !empty($no_max_grade_err))
      $tab_rubric_err = 1;





	# Regresa a la forma con error
  $fg_error = $nb_programa_err || $ds_duracion_err || $ds_tipo_err || $no_orden_err || $no_grados_err || $fl_template_err || $no_horas_err || $no_horas_week_err || $no_semanas_err || $ds_credential_err || $mn_lecture_fee_err || $mn_extra_fee_err || $no_val_rub_err  || $no_max_grade_err  || $tab_rubric_err;

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
    Forma_CampoOculto('app_fee_internacional',$app_fee_internacional);
    Forma_CampoOculto('tuition', $tuition);
    Forma_CampoOculto('tuition_internacional', $tuition_internacional);
    Forma_CampoOculto('no_costos_ad', $no_costos_ad);
    Forma_CampoOculto('no_costos_ad_internacional',$no_costos_ad_internacional);
    Forma_CampoOculto('ds_costos_ad', $ds_costos_ad);
    Forma_CampoOculto('ds_costos_ad_internacional',$ds_costos_ad_internacional);
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

	Forma_CampoOculto('no_payments_a_internacional', $no_payments_a_internacional);
    Forma_CampoOculto('frequency_a_internacional', $frequency_a_internacional);
    Forma_CampoOculto('amount_due_a_internacional', $amount_due_a_internacional);
    Forma_CampoOculto('amount_paid_a_internacional', $amount_paid_a_internacional);
    Forma_CampoOculto('interes_a_internacional', $interes_a_internacional);
    Forma_CampoOculto('no_payments_b_internacional', $no_payments_b_internacional);
    Forma_CampoOculto('frequency_b_internacional', $frequency_b_internacional);
    Forma_CampoOculto('amount_due_b_internacional', $amount_due_b_internacional);
    Forma_CampoOculto('amount_paid_b_internacional', $amount_paid_b_internacional);
    Forma_CampoOculto('interes_b_internacional', $interes_b_internacional);
    Forma_CampoOculto('no_payments_c_internacional', $no_payments_c_internacional);
    Forma_CampoOculto('frequency_c_internacional', $frequency_c_internacional);
    Forma_CampoOculto('amount_due_c_internacional', $amount_due_c_internacional);
    Forma_CampoOculto('amount_paid_c_internacional', $amount_paid_c_internacional);
    Forma_CampoOculto('interes_c_internacional', $interes_c_internacional);
    Forma_CampoOculto('no_payments_d_internacional', $no_payments_d_internacional);
    Forma_CampoOculto('frequency_d_internacional', $frequency_d_internacional);
    Forma_CampoOculto('amount_due_d_internacional', $amount_due_d_internacional);
    Forma_CampoOculto('amount_paid_d_internacional', $amount_paid_d_internacional);
    Forma_CampoOculto('interes_d_internacional', $interes_d_internacional);


    Forma_CampoOculto('fg_fulltime', $fg_fulltime);
    Forma_CampoOculto('fg_taxes', $fg_taxes);

    Forma_CampoOculto('fg_total_programa', $fg_total_programa);
    Forma_CampoOculto('fg_total_programa_internacional',$fg_total_programa_internacional);
    Forma_CampoOculto('fg_tax_rate_internacional',$fg_tax_rate_internacional);


	####Combined
    Forma_CampoOculto('app_fee_combined', $app_fee_combined);
    Forma_CampoOculto('app_fee_internacional_combined',$app_fee_internacional_combined);
    Forma_CampoOculto('tuition_combined', $tuition_combined);
    Forma_CampoOculto('tuition_internacional_combined', $tuition_internacional_combined);
    Forma_CampoOculto('no_costos_ad_combined', $no_costos_ad_combined);
    Forma_CampoOculto('no_costos_ad_internacional_combined',$no_costos_ad_internacional_combined);
    Forma_CampoOculto('ds_costos_ad_combined', $ds_costos_ad_combined);
    Forma_CampoOculto('ds_costos_ad_internacional_combined',$ds_costos_ad_internacional_combined);
    Forma_CampoOculto('no_payments_a_combined', $no_payments_a_combined);
    Forma_CampoOculto('frequency_a_combined', $frequency_a_combined);
    Forma_CampoOculto('amount_due_a_combined', $amount_due_a_combined);
    Forma_CampoOculto('amount_paid_a_combined', $amount_paid_a_combined);
    Forma_CampoOculto('interes_a_combined', $interes_a_combined);
    Forma_CampoOculto('no_payments_b_combined', $no_payments_b_combined);
    Forma_CampoOculto('frequency_b_combined', $frequency_b_combined);
    Forma_CampoOculto('amount_due_b_combined', $amount_due_b_combined);
    Forma_CampoOculto('amount_paid_b_combined', $amount_paid_b_combined);
    Forma_CampoOculto('interes_b_combined', $interes_b_combined);
    Forma_CampoOculto('no_payments_c_combined', $no_payments_c_combined);
    Forma_CampoOculto('frequency_c_combined', $frequency_c_combined);
    Forma_CampoOculto('amount_due_c_combined', $amount_due_c_combined);
    Forma_CampoOculto('amount_paid_c_combined', $amount_paid_c_combined);
    Forma_CampoOculto('interes_c_combined', $interes_c_combined);
    Forma_CampoOculto('no_payments_d_combined', $no_payments_d_combined);
    Forma_CampoOculto('frequency_d_combined', $frequency_d_combined);
    Forma_CampoOculto('amount_due_d_combined', $amount_due_d_combined);
    Forma_CampoOculto('amount_paid_d_combined', $amount_paid_d_combined);
    Forma_CampoOculto('interes_d_combined', $interes_d_combined);

	Forma_CampoOculto('no_payments_a_internacional_combined', $no_payments_a_internacional_combined);
    Forma_CampoOculto('frequency_a_internacional_combined', $frequency_a_internacional_combined);
    Forma_CampoOculto('amount_due_a_internacional_combined', $amount_due_a_internacional_combined);
    Forma_CampoOculto('amount_paid_a_internacional_combined', $amount_paid_a_internacional_combined);
    Forma_CampoOculto('interes_a_internacional_combined', $interes_a_internacional_combined);
    Forma_CampoOculto('no_payments_b_internacional_combined', $no_payments_b_internacional_combined);
    Forma_CampoOculto('frequency_b_internacional_combined', $frequency_b_internacional_combined);
    Forma_CampoOculto('amount_due_b_internacional_combined', $amount_due_b_internacional_combined);
    Forma_CampoOculto('amount_paid_b_internacional_combined', $amount_paid_b_internacional_combined);
    Forma_CampoOculto('interes_b_internacional_combined', $interes_b_internacional_combined);
    Forma_CampoOculto('no_payments_c_internacional_combined', $no_payments_c_internacional_combined);
    Forma_CampoOculto('frequency_c_internacional_combined', $frequency_c_internacional_combined);
    Forma_CampoOculto('amount_due_c_internacional_combined', $amount_due_c_internacional_combined);
    Forma_CampoOculto('amount_paid_c_internacional_combined', $amount_paid_c_internacional_combined);
    Forma_CampoOculto('interes_c_internacional_combined', $interes_c_internacional_combined);
    Forma_CampoOculto('no_payments_d_internacional_combined', $no_payments_d_internacional_combined);
    Forma_CampoOculto('frequency_d_internacional_combined', $frequency_d_internacional_combined);
    Forma_CampoOculto('amount_due_d_internacional_combined', $amount_due_d_internacional_combined);
    Forma_CampoOculto('amount_paid_d_internacional_combined', $amount_paid_d_internacional_combined);
    Forma_CampoOculto('interes_d_internacional_combined', $interes_d_internacional_combined);

	Forma_CampoOculto('fg_total_programa', $fg_total_programa);
    Forma_CampoOculto('fg_total_programa_internacional',$fg_total_programa_internacional);
    Forma_CampoOculto('fg_tax_rate_internacional',$fg_tax_rate_internacional);

	Forma_CampoOculto('fg_total_programa_combined', $fg_total_programa_combined);
    Forma_CampoOculto('fg_total_programa_internacional_combined',$fg_total_programa_internacional_combined);
    Forma_CampoOculto('fg_tax_rate_internacional_combined',$fg_tax_rate_internacional_combined);



    Forma_CampoOculto('ds_career', $ds_career);
    Forma_CampoOculto('ds_objetives', $ds_objetives);
    Forma_CampoOculto('ds_teaching', $ds_teaching);
    Forma_CampoOculto('ds_evaluation', $ds_evaluation);
    Forma_CampoOculto('ds_requeriments', $ds_requeriments);
    Forma_CampoOculto('ds_program_org', $ds_program_org);
    Forma_CampoOculto('ds_combinend', $ds_combinend);

    Forma_CampoOculto('ptib_approval', $ptib_approval);

	Forma_CampoOculto('fg_archive', $fg_archive);
    Forma_CampoOculto('mn_lecture_fee', $mn_lecture_fee);
    Forma_CampoOculto('mn_lecture_fee_err', $mn_lecture_fee_err);
    Forma_CampoOculto('mn_extra_fee', $mn_extra_fee);
    Forma_CampoOculto('mn_extra_fee_err', $mn_extra_fee_err);
    Forma_CampoOculto('fg_streams', $fg_streams);

	# Rubric
    Forma_CampoOculto('no_ter_co' , $no_ter_co);
    Forma_CampoOculto('no_val_rub' , $no_val_rub);
    Forma_CampoOculto('no_val_rub_err' , $no_val_rub_err);
    Forma_CampoOculto('no_max_grade_err' , $no_max_grade_err);
    Forma_CampoOculto('sum_val_grade' , $sum_val_grade);
    Forma_CampoOculto('tab_rubric_err' , $tab_rubric_err);
    Forma_CampoOculto('tab_description_err' , $tab_description_err);
	Forma_CampoOculto('no_ptib',$no_ptib);

    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }


  # Inserta o actualiza el registro
  if(empty($clave)) {
      $Query  = "INSERT INTO c_programa (nb_programa,no_ptib, ds_duracion, ds_tipo, no_orden, no_grados, fl_template, fg_fulltime, fg_taxes, fg_total_programa,fg_total_programa_internacional,fg_tax_rate_internacional, fg_archive, ";
    $Query .= "mn_lecture_fee, mn_extra_fee, fg_tax_rate,no_valor_rubrics,ptib_approval,  ";
    $Query .=" ds_career,ds_objetives,ds_teaching,ds_evaluation,ds_requeriments,ds_program_org,ds_combinend, ";
	$Query.= "fg_total_programa_combined,fg_total_programa_internacional_combined,fg_tax_rate_combined,fg_tax_rate_internacional_combined) ";

    $Query .= "VALUES('$nb_programa', '$no_ptib','$ds_duracion', '$ds_tipo', $no_orden, $no_grados, $fl_template, '$fg_fulltime', '$fg_taxes', '$fg_total_programa','$fg_total_programa_internacional','$fg_tax_rate_internacional', '$fg_archive', ";
    $Query .= "$mn_lecture_fee, $mn_extra_fee,'$fg_tax_rate',$no_val_rub , '$ptib_approval', ";
	$Query .=" '$ds_career','$ds_objetives','$ds_teaching','$ds_evaluation','$ds_requeriments','$ds_program_org','$ds_combinend', ";
    $Query .="'$fg_total_programa_combined','$fg_total_programa_internacional_combined','$fg_tax_rate_combined', '$fg_tax_rate_internacional_combined') ";

    EjecutaQuery($Query);
    $row = RecuperaValor("SELECT MAX(fl_programa) FROM c_programa");
    $clave = $row[0];


    $Query  = "INSERT INTO k_programa_costos (fl_programa, mn_app_fee,mn_app_fee_internacional,mn_tuition,mn_tuition_internacional, mn_costs,mn_costs_internacional, ds_costs,ds_costs_internacional, ";
    $Query .= "no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes,
			   no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, ";
    $Query .= "no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes,
			   no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes, ";

	$Query .= "no_a_payments_internacional, ds_a_freq_internacional, mn_a_due_internacional, mn_a_paid_internacional, no_a_interes_internacional,
	           no_b_payments_internacional, ds_b_freq_internacional, mn_b_due_internacional, mn_b_paid_internacional, no_b_interes_internacional, ";
    $Query .= "no_c_payments_internacional, ds_c_freq_internacional, mn_c_due_internacional, mn_c_paid_internacional, no_c_interes_internacional,
	           no_d_payments_internacional, ds_d_freq_internacional, mn_d_due_internacional, mn_d_paid_internacional, no_d_interes_internacional, ";

    $Query .= "no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_horas_week ";
	$Query .= ",mn_app_fee_combined,mn_app_fee_internacional_combined,mn_tuition_combined,mn_tuition_internacional_combined,mn_costs_combined,mn_costs_internacional_combined,ds_costs_combined,ds_costs_internacional_combined, ";

	$Query .= "no_a_payments_combined, ds_a_freq_combined, mn_a_due_combined, mn_a_paid_combined, no_a_interes_combined, no_b_payments_combined, ds_b_freq_combined, mn_b_due_combined, mn_b_paid_combined, no_b_interes_combined, ";
    $Query .= "no_c_payments_combined, ds_c_freq_combined, mn_c_due_combined, mn_c_paid_combined, no_c_interes_combined, no_d_payments_combined, ds_d_freq_combined, mn_d_due_combined, mn_d_paid_combined, no_d_interes_combined, ";

	$Query .= "no_a_payments_internacional_combined, ds_a_freq_internacional_combined, mn_a_due_internacional_combined, mn_a_paid_internacional_combined, no_a_interes_internacional_combined,
			   no_b_payments_internacional_combined, ds_b_freq_internacional_combined, mn_b_due_internacional_combined, mn_b_paid_internacional_combined, no_b_interes_internacional_combined, ";
    $Query .= "no_c_payments_internacional_combined, ds_c_freq_internacional_combined, mn_c_due_internacional_combined, mn_c_paid_internacional_combined, no_c_interes_internacional_combined,
	           no_d_payments_internacional_combined, ds_d_freq_internacional_combined, mn_d_due_internacional_combined, mn_d_paid_internacional_combined, no_d_interes_internacional_combined ";
	$Query .= ") ";

	$Query .= "VALUES($clave, $app_fee,$app_fee_internacional, $tuition,$tuition_internacional, $no_costos_ad,$no_costos_ad_internacional, '$ds_costos_ad','$ds_costos_ad_internacional', ";
    $Query .= "$no_payments_a, '$frequency_a', $amount_due_a, $amount_paid_a, $interes_a, ";
    $Query .= "$no_payments_b, '$frequency_b', $amount_due_b, $amount_paid_b, $interes_b, ";
    $Query .= "$no_payments_c, '$frequency_c', $amount_due_c, $amount_paid_c, $interes_c, ";
    $Query .= "$no_payments_d, '$frequency_d', $amount_due_d, $amount_paid_d, $interes_d, ";

	$Query .= "$no_payments_a_internacional, '$frequency_a_internacional', $amount_due_a_internacional, $amount_paid_a_internacional, $interes_a_internacional, ";
    $Query .= "$no_payments_b_internacional, '$frequency_b_internacional', $amount_due_b_internacional, $amount_paid_b_internacional, $interes_b_internacional, ";
    $Query .= "$no_payments_c_internacional, '$frequency_c_internacional', $amount_due_c_internacional, $amount_paid_c_internacional, $interes_c_internacional, ";
    $Query .= "$no_payments_d_internacional, '$frequency_d_internacional', $amount_due_d_internacional, $amount_paid_d_internacional, $interes_d_internacional, ";

    $Query .= "$no_horas, $no_semanas, '$ds_credential', '$cl_delivery', '$ds_language', $cl_type, $no_horas_week, ";
	$Query .= "$app_fee_combined,$app_fee_internacional_combined,$tuition_combined,$tuition_internacional_combined, $no_costos_ad_combined,$no_costos_ad_internacional_combined, '$ds_costos_ad_combined','$ds_costos_ad_internacional_combined', ";

	$Query .= "$no_payments_a_combined, '$frequency_a_combined', $amount_due_a_combined, $amount_paid_a_combined, $interes_a_combined, ";
    $Query .= "$no_payments_b_combined, '$frequency_b_combined', $amount_due_b_combined, $amount_paid_b_combined, $interes_b_combined, ";
    $Query .= "$no_payments_c_combined, '$frequency_c_combined', $amount_due_c_combined, $amount_paid_c_combined, $interes_c_combined, ";
    $Query .= "$no_payments_d_combined, '$frequency_d_combined', $amount_due_d_combined, $amount_paid_d_combined, $interes_d_combined, ";


	$Query .= "$no_payments_a_internacional_combined, '$frequency_a_internacional_combined', $amount_due_a_internacional_combined, $amount_paid_a_internacional_combined, $interes_a_internacional_combined, ";
    $Query .= "$no_payments_b_internacional_combined, '$frequency_b_internacional_combined', $amount_due_b_internacional_combined, $amount_paid_b_internacional_combined, $interes_b_internacional_combined, ";
    $Query .= "$no_payments_c_internacional_combined, '$frequency_c_internacional_combined', $amount_due_c_internacional_combined, $amount_paid_c_internacional_combined, $interes_c_internacional_combined, ";
    $Query .= "$no_payments_d_internacional_combined, '$frequency_d_internacional_combined', $amount_due_d_internacional_combined, $amount_paid_d_internacional_combined, $interes_d_internacional_combined ";

	$Query .=") ";
    EjecutaQuery($Query);


    #cost by country
    #USA

    $Query  = "INSERT INTO k_programa_costos_pais (fl_programa,fl_pais, mn_app_fee,mn_app_fee_internacional,mn_tuition,mn_tuition_internacional, mn_costs,mn_costs_internacional, ds_costs,ds_costs_internacional, ";
    $Query .= "no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes,
			   no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, ";
    $Query .= "no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes,
			   no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes, ";

	$Query .= "no_a_payments_internacional, ds_a_freq_internacional, mn_a_due_internacional, mn_a_paid_internacional, no_a_interes_internacional,
	           no_b_payments_internacional, ds_b_freq_internacional, mn_b_due_internacional, mn_b_paid_internacional, no_b_interes_internacional, ";
    $Query .= "no_c_payments_internacional, ds_c_freq_internacional, mn_c_due_internacional, mn_c_paid_internacional, no_c_interes_internacional,
	           no_d_payments_internacional, ds_d_freq_internacional, mn_d_due_internacional, mn_d_paid_internacional, no_d_interes_internacional, ";

    $Query .= "no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_horas_week ";
	$Query .= ",mn_app_fee_combined,mn_app_fee_internacional_combined,mn_tuition_combined,mn_tuition_internacional_combined,mn_costs_combined,mn_costs_internacional_combined,ds_costs_combined,ds_costs_internacional_combined, ";

	$Query .= "no_a_payments_combined, ds_a_freq_combined, mn_a_due_combined, mn_a_paid_combined, no_a_interes_combined, no_b_payments_combined, ds_b_freq_combined, mn_b_due_combined, mn_b_paid_combined, no_b_interes_combined, ";
    $Query .= "no_c_payments_combined, ds_c_freq_combined, mn_c_due_combined, mn_c_paid_combined, no_c_interes_combined, no_d_payments_combined, ds_d_freq_combined, mn_d_due_combined, mn_d_paid_combined, no_d_interes_combined, ";

	$Query .= "no_a_payments_internacional_combined, ds_a_freq_internacional_combined, mn_a_due_internacional_combined, mn_a_paid_internacional_combined, no_a_interes_internacional_combined,
			   no_b_payments_internacional_combined, ds_b_freq_internacional_combined, mn_b_due_internacional_combined, mn_b_paid_internacional_combined, no_b_interes_internacional_combined, ";
    $Query .= "no_c_payments_internacional_combined, ds_c_freq_internacional_combined, mn_c_due_internacional_combined, mn_c_paid_internacional_combined, no_c_interes_internacional_combined,
	           no_d_payments_internacional_combined, ds_d_freq_internacional_combined, mn_d_due_internacional_combined, mn_d_paid_internacional_combined, no_d_interes_internacional_combined ";
	$Query .= ") ";

	$Query .= "VALUES($clave,226, $app_fee_usa,$app_fee_internacional_usa, $tuition_usa,$tuition_internacional_usa, $no_costos_ad_usa,$no_costos_ad_internacional_usa, '$ds_costos_ad_usa','$ds_costos_ad_internacional_usa', ";
    $Query .= "$no_payments_a_usa, '$frequency_a_usa', $amount_due_a_usa, $amount_paid_a_usa, $interes_a_usa, ";
    $Query .= "$no_payments_b_usa, '$frequency_b_usa', $amount_due_b_usa, $amount_paid_b_usa, $interes_b_usa, ";
    $Query .= "$no_payments_c_usa, '$frequency_c_usa', $amount_due_c_usa, $amount_paid_c_usa, $interes_c_usa, ";
    $Query .= "$no_payments_d_usa, '$frequency_d_usa', $amount_due_d_usa, $amount_paid_d_usa, $interes_d_usa, ";

	$Query .= "$no_payments_a_internacional_usa, '$frequency_a_internacional_usa', $amount_due_a_internacional_usa, $amount_paid_a_internacional_usa, $interes_a_internacional_usa, ";
    $Query .= "$no_payments_b_internacional_usa, '$frequency_b_internacional_usa', $amount_due_b_internacional_usa, $amount_paid_b_internacional_usa, $interes_b_internacional_usa, ";
    $Query .= "$no_payments_c_internacional_usa, '$frequency_c_internacional_usa', $amount_due_c_internacional_usa, $amount_paid_c_internacional_usa, $interes_c_internacional_usa, ";
    $Query .= "$no_payments_d_internacional_usa, '$frequency_d_internacional_usa', $amount_due_d_internacional_usa, $amount_paid_d_internacional_usa, $interes_d_internacional_usa, ";

    $Query .= "$no_horas, $no_semanas, '$ds_credential', '$cl_delivery', '$ds_language', $cl_type, $no_horas_week, ";
	$Query .= "$app_fee_combined_usa,$app_fee_internacional_combined_usa,$tuition_combined_usa,$tuition_internacional_combined_usa, $no_costos_ad_combined_usa,$no_costos_ad_internacional_combined_usa, '$ds_costos_ad_combined_usa','$ds_costos_ad_internacional_combined_usa', ";

	$Query .= "$no_payments_a_combined_usa, '$frequency_a_combined_usa', $amount_due_a_combined_usa, $amount_paid_a_combined_usa, $interes_a_combined_usa, ";
    $Query .= "$no_payments_b_combined_usa, '$frequency_b_combined_usa', $amount_due_b_combined_usa, $amount_paid_b_combined_usa, $interes_b_combined_usa, ";
    $Query .= "$no_payments_c_combined_usa, '$frequency_c_combined_usa', $amount_due_c_combined_usa, $amount_paid_c_combined_usa, $interes_c_combined_usa, ";
    $Query .= "$no_payments_d_combined_usa, '$frequency_d_combined_usa', $amount_due_d_combined_usa, $amount_paid_d_combined_usa, $interes_d_combined_usa, ";


	$Query .= "$no_payments_a_internacional_combined_usa, '$frequency_a_internacional_combined_usa', $amount_due_a_internacional_combined_usa, $amount_paid_a_internacional_combined_usa, $interes_a_internacional_combined_usa, ";
    $Query .= "$no_payments_b_internacional_combined_usa, '$frequency_b_internacional_combined_usa', $amount_due_b_internacional_combined_usa, $amount_paid_b_internacional_combined_usa, $interes_b_internacional_combined_usa, ";
    $Query .= "$no_payments_c_internacional_combined_usa, '$frequency_c_internacional_combined_usa', $amount_due_c_internacional_combined_usa, $amount_paid_c_internacional_combined_usa, $interes_c_internacional_combined_usa, ";
    $Query .= "$no_payments_d_internacional_combined_usa, '$frequency_d_internacional_combined_usa', $amount_due_d_internacional_combined_usa, $amount_paid_d_internacional_combined_usa, $interes_d_internacional_combined_usa ";

	$Query .=") ";
    EjecutaQuery($Query);




  }
  else {
      $Query  = "UPDATE c_programa SET nb_programa='$nb_programa',no_ptib='$no_ptib', ds_duracion='$ds_duracion', ds_tipo='$ds_tipo', ";
    $Query .= "no_orden=$no_orden, no_grados=$no_grados, fl_template=$fl_template, fg_fulltime='$fg_fulltime', fg_taxes='$fg_taxes', fg_total_programa='$fg_total_programa', fg_archive='$fg_archive',";
    $Query .= "mn_lecture_fee = $mn_lecture_fee,mn_extra_fee = $mn_extra_fee, fg_tax_rate='$fg_tax_rate',no_valor_rubrics=$no_val_rub , ";
    $Query .="fg_total_programa_internacional='$fg_total_programa_internacional', fg_tax_rate_internacional='$fg_tax_rate_internacional' ,";
	$Query .="fg_total_programa_combined='$fg_total_programa_combined', fg_total_programa_internacional_combined='$fg_total_programa_internacional_combined', ";
	$Query .="fg_tax_rate_combined='$fg_tax_rate_combined',fg_tax_rate_internacional_combined='$fg_tax_rate_internacional_combined' , ";
    $Query .=" ptib_approval='$ptib_approval' ";
    $Query .= "WHERE fl_programa=$clave";
    EjecutaQuery($Query);

    $QueryU ="UPDATE c_programa SET ds_career='$ds_career',ds_objetives='$ds_objetives',ds_teaching='$ds_teaching',ds_evaluation='$ds_evaluation',ds_requeriments='$ds_requeriments',ds_program_org='$ds_program_org',ds_combinend='$ds_combinend' ";
    $QueryU .= "WHERE fl_programa=$clave ";
    EjecutaQuery($QueryU);


    if(ExisteEnTabla('k_programa_costos', 'fl_programa', $clave))
    {
      $Query  = "UPDATE k_programa_costos SET
				 mn_app_fee=$app_fee, mn_tuition=$tuition, mn_costs=$no_costos_ad, ds_costs='$ds_costos_ad', ";
      $Query .= "no_a_payments=$no_payments_a, ds_a_freq='$frequency_a', mn_a_due=$amount_due_a, mn_a_paid=$amount_paid_a, no_a_interes=$interes_a, ";
      $Query .= "no_b_payments=$no_payments_b, ds_b_freq='$frequency_b', mn_b_due=$amount_due_b, mn_b_paid=$amount_paid_b, no_b_interes=$interes_b, ";
      $Query .= "no_c_payments=$no_payments_c, ds_c_freq='$frequency_c', mn_c_due=$amount_due_c, mn_c_paid=$amount_paid_c, no_c_interes=$interes_c, ";
      $Query .= "no_d_payments=$no_payments_d, ds_d_freq='$frequency_d', mn_d_due=$amount_due_d, mn_d_paid=$amount_paid_d, no_d_interes=$interes_d, ";

	  $Query .= "no_a_payments_internacional=$no_payments_a_internacional, ds_a_freq_internacional='$frequency_a_internacional', mn_a_due_internacional=$amount_due_a_internacional, mn_a_paid_internacional=$amount_paid_a_internacional, no_a_interes_internacional=$interes_a_internacional, ";
      $Query .= "no_b_payments_internacional=$no_payments_b_internacional, ds_b_freq_internacional='$frequency_b_internacional', mn_b_due_internacional=$amount_due_b_internacional, mn_b_paid_internacional=$amount_paid_b_internacional, no_b_interes_internacional=$interes_b_internacional, ";
      $Query .= "no_c_payments_internacional=$no_payments_c_internacional, ds_c_freq_internacional='$frequency_c_internacional', mn_c_due_internacional=$amount_due_c_internacional, mn_c_paid_internacional=$amount_paid_c_internacional, no_c_interes_internacional=$interes_c_internacional, ";
      $Query .= "no_d_payments_internacional=$no_payments_d_internacional, ds_d_freq_internacional='$frequency_d_internacional', mn_d_due_internacional=$amount_due_d_internacional, mn_d_paid_internacional=$amount_paid_d_internacional, no_d_interes_internacional=$interes_d_internacional, ";

	  $Query .= "no_horas=$no_horas, no_semanas=$no_semanas, ds_credential='$ds_credential', cl_delivery='$cl_delivery', ds_language='$ds_language', cl_type=$cl_type, ";
      $Query .=" mn_tuition_internacional=$tuition_internacional , mn_app_fee_internacional=$app_fee_internacional ,mn_costs_internacional=$no_costos_ad_internacional, ds_costs_internacional='$ds_costos_ad_internacional', ";

	  $Query .=" mn_app_fee_combined=$app_fee_combined, mn_tuition_combined=$tuition_combined, mn_costs_combined=$no_costos_ad_combined, ds_costs_combined='$ds_costos_ad_combined', ";

	  $Query .= "no_a_payments_combined=$no_payments_a_combined, ds_a_freq_combined='$frequency_a_combined', mn_a_due_combined=$amount_due_a_combined, mn_a_paid_combined=$amount_paid_a_combined, no_a_interes_combined=$interes_a_combined, ";
      $Query .= "no_b_payments_combined=$no_payments_b_combined, ds_b_freq_combined='$frequency_b_combined', mn_b_due_combined=$amount_due_b_combined, mn_b_paid_combined=$amount_paid_b_combined, no_b_interes_combined=$interes_b_combined, ";
      $Query .= "no_c_payments_combined=$no_payments_c_combined, ds_c_freq_combined='$frequency_c_combined', mn_c_due_combined=$amount_due_c_combined, mn_c_paid_combined=$amount_paid_c_combined, no_c_interes_combined=$interes_c_combined, ";
      $Query .= "no_d_payments_combined=$no_payments_d_combined, ds_d_freq_combined='$frequency_d_combined', mn_d_due_combined=$amount_due_d_combined, mn_d_paid_combined=$amount_paid_d_combined, no_d_interes_combined=$interes_d_combined, ";

	  $Query .=" mn_app_fee_internacional_combined=$app_fee_internacional_combined, mn_tuition_internacional_combined=$tuition_internacional_combined , mn_costs_internacional_combined=$no_costos_ad_internacional_combined, ds_costs_internacional_combined='$ds_costos_ad_internacional_combined', ";
	  $Query .= "no_a_payments_internacional_combined=$no_payments_a_internacional_combined, ds_a_freq_internacional_combined='$frequency_a_internacional_combined', mn_a_due_internacional_combined=$amount_due_a_internacional_combined, mn_a_paid_internacional_combined=$amount_paid_a_internacional_combined, no_a_interes_internacional_combined=$interes_a_internacional_combined, ";
      $Query .= "no_b_payments_internacional_combined=$no_payments_b_internacional_combined, ds_b_freq_internacional_combined='$frequency_b_internacional_combined', mn_b_due_internacional_combined=$amount_due_b_internacional_combined, mn_b_paid_internacional_combined=$amount_paid_b_internacional_combined, no_b_interes_internacional_combined=$interes_b_internacional_combined, ";
      $Query .= "no_c_payments_internacional_combined=$no_payments_c_internacional_combined, ds_c_freq_internacional_combined='$frequency_c_internacional_combined', mn_c_due_internacional_combined=$amount_due_c_internacional_combined, mn_c_paid_internacional_combined=$amount_paid_c_internacional_combined, no_c_interes_internacional_combined=$interes_c_internacional_combined, ";
      $Query .= "no_d_payments_internacional_combined=$no_payments_d_internacional_combined, ds_d_freq_internacional_combined='$frequency_d_internacional_combined', mn_d_due_internacional_combined=$amount_due_d_internacional_combined, mn_d_paid_internacional_combined=$amount_paid_d_internacional_combined, no_d_interes_internacional_combined=$interes_d_internacional_combined, ";


	  $Query .= "no_horas_week=$no_horas_week WHERE fl_programa=$clave";
    }
    else
    {
      $Query  = "INSERT INTO k_programa_costos (fl_programa, mn_app_fee, mn_tuition, mn_costs, ds_costs,mn_app_fee_internacional,mn_tuition_internacional,mn_costs_internacional,ds_costs_internacional, ";
      $Query .= "no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes, no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, ";
      $Query .= "no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes, no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes, ";

	  $Query .= "no_a_payments_internacional, ds_a_freq_internacional, mn_a_due_internacional, mn_a_paid_internacional, no_a_interes_internacional, no_b_payments_internacional, ds_b_freq_internacional, mn_b_due_internacional, mn_b_paid_internacional, no_b_interes_internacional, ";
      $Query .= "no_c_payments_internacional, ds_c_freq_internacional, mn_c_due_internacional, mn_c_paid_internacional, no_c_interes_internacional, no_d_payments_internacional, ds_d_freq_internacional, mn_d_due_internacional, mn_d_paid_internacional, no_d_interes_internacional, ";


      $Query .= "no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_horas_week, ";
      $Query .= ",mn_app_fee_combined,mn_app_fee_internacional_combined,mn_tuition_combined,mn_tuition_internacional_combined,mn_costs_combined,mn_costs_internacional_combined,ds_costs_combined,ds_costs_internacional_combined ) ";

      $Query .= "VALUES($clave, $app_fee, $tuition, $no_costos_ad, '$ds_costos_ad',$app_fee_internacional, $tuition_internacional, $no_costos_ad_internacional,'$ds_costos_ad_internacional',  ";
      $Query .= "$no_payments_a, '$frequency_a', $amount_due_a, $amount_paid_a, $interes_a, ";
      $Query .= "$no_payments_b, '$frequency_b', $amount_due_b, $amount_paid_b, $interes_b, ";
      $Query .= "$no_payments_c, '$frequency_c', $amount_due_c, $amount_paid_c, $interes_c, ";
      $Query .= "$no_payments_d, '$frequency_d', $amount_due_d, $amount_paid_d, $interes_d, ";

      $Query .= "$no_payments_a_internacional, '$frequency_a_internacional', $amount_due_a_internacional, $amount_paid_a_internacional, $interes_a_internacional, ";
      $Query .= "$no_payments_b_internacional, '$frequency_b_internacional', $amount_due_b_internacional, $amount_paid_b_internacional, $interes_b_internacional, ";
      $Query .= "$no_payments_c_internacional, '$frequency_c_internacional', $amount_due_c_internacional, $amount_paid_c_internacional, $interes_c_internacional, ";
      $Query .= "$no_payments_d_internacional, '$frequency_d_internacional', $amount_due_d_internacional, $amount_paid_d_internacional, $interes_d_internacional, ";

      $Query .= "$no_horas, $no_semanas, '$ds_credential', '$cl_delivery', '$ds_language', $cl_type, $no_horas_week, ";
      $Query .= "$app_fee_combined,$app_fee_internacional_combined,$tuition_combined,$tuition_internacional_combined, $no_costos_ad_combined,$no_costos_ad_internacional_combined, '$ds_costos_ad_combined','$ds_costos_ad_internacional_combined') ";
    }

    EjecutaQuery($Query);



    ##USA
    $Query="SELECT COUNT(*) FROM k_programa_costos_pais WHERE fl_programa=$clave AND fl_pais=226 ";
    $row=RecuperaValor($Query);
    if(!empty($row[0]))
    {
        $Query  = "UPDATE k_programa_costos_pais SET
				 mn_app_fee=$app_fee_usa, mn_tuition=$tuition_usa, mn_costs=$no_costos_ad_usa, ds_costs='$ds_costos_ad_usa', ";
        $Query .= "no_a_payments=$no_payments_a_usa, ds_a_freq='$frequency_a_usa', mn_a_due=$amount_due_a_usa, mn_a_paid=$amount_paid_a_usa, no_a_interes=$interes_a_usa, ";
        $Query .= "no_b_payments=$no_payments_b_usa, ds_b_freq='$frequency_b_usa', mn_b_due=$amount_due_b_usa, mn_b_paid=$amount_paid_b_usa, no_b_interes=$interes_b_usa, ";
        $Query .= "no_c_payments=$no_payments_c_usa, ds_c_freq='$frequency_c_usa', mn_c_due=$amount_due_c_usa, mn_c_paid=$amount_paid_c_usa, no_c_interes=$interes_c_usa, ";
        $Query .= "no_d_payments=$no_payments_d_usa, ds_d_freq='$frequency_d_usa', mn_d_due=$amount_due_d_usa, mn_d_paid=$amount_paid_d_usa, no_d_interes=$interes_d_usa, ";

        $Query .= "no_a_payments_internacional=$no_payments_a_internacional_usa, ds_a_freq_internacional='$frequency_a_internacional_usa', mn_a_due_internacional=$amount_due_a_internacional_usa, mn_a_paid_internacional=$amount_paid_a_internacional_usa, no_a_interes_internacional=$interes_a_internacional_usa, ";
        $Query .= "no_b_payments_internacional=$no_payments_b_internacional_usa, ds_b_freq_internacional='$frequency_b_internacional_usa', mn_b_due_internacional=$amount_due_b_internacional_usa, mn_b_paid_internacional=$amount_paid_b_internacional_usa, no_b_interes_internacional=$interes_b_internacional_usa, ";
        $Query .= "no_c_payments_internacional=$no_payments_c_internacional_usa, ds_c_freq_internacional='$frequency_c_internacional_usa', mn_c_due_internacional=$amount_due_c_internacional_usa, mn_c_paid_internacional=$amount_paid_c_internacional_usa, no_c_interes_internacional=$interes_c_internacional_usa, ";
        $Query .= "no_d_payments_internacional=$no_payments_d_internacional_usa, ds_d_freq_internacional='$frequency_d_internacional_usa', mn_d_due_internacional=$amount_due_d_internacional_usa, mn_d_paid_internacional=$amount_paid_d_internacional_usa, no_d_interes_internacional=$interes_d_internacional_usa, ";

        $Query .= "no_horas=$no_horas, no_semanas=$no_semanas, ds_credential='$ds_credential', cl_delivery='$cl_delivery', ds_language='$ds_language', cl_type=$cl_type, ";
        $Query .=" mn_tuition_internacional=$tuition_internacional_usa , mn_app_fee_internacional=$app_fee_internacional_usa ,mn_costs_internacional=$no_costos_ad_internacional_usa, ds_costs_internacional='$ds_costos_ad_internacional_usa', ";

        $Query .=" mn_app_fee_combined=$app_fee_combined_usa, mn_tuition_combined=$tuition_combined_usa, mn_costs_combined=$no_costos_ad_combined_usa, ds_costs_combined='$ds_costos_ad_combined_usa', ";

        $Query .= "no_a_payments_combined=$no_payments_a_combined_usa, ds_a_freq_combined='$frequency_a_combined_usa', mn_a_due_combined=$amount_due_a_combined_usa, mn_a_paid_combined=$amount_paid_a_combined_usa, no_a_interes_combined=$interes_a_combined_usa, ";
        $Query .= "no_b_payments_combined=$no_payments_b_combined_usa, ds_b_freq_combined='$frequency_b_combined_usa', mn_b_due_combined=$amount_due_b_combined_usa, mn_b_paid_combined=$amount_paid_b_combined_usa, no_b_interes_combined=$interes_b_combined_usa, ";
        $Query .= "no_c_payments_combined=$no_payments_c_combined_usa, ds_c_freq_combined='$frequency_c_combined_usa', mn_c_due_combined=$amount_due_c_combined_usa, mn_c_paid_combined=$amount_paid_c_combined_usa, no_c_interes_combined=$interes_c_combined_usa, ";
        $Query .= "no_d_payments_combined=$no_payments_d_combined_usa, ds_d_freq_combined='$frequency_d_combined_usa', mn_d_due_combined=$amount_due_d_combined_usa, mn_d_paid_combined=$amount_paid_d_combined_usa, no_d_interes_combined=$interes_d_combined_usa, ";

        $Query .=" mn_app_fee_internacional_combined=$app_fee_internacional_combined_usa, mn_tuition_internacional_combined=$tuition_internacional_combined_usa , mn_costs_internacional_combined=$no_costos_ad_internacional_combined_usa, ds_costs_internacional_combined='$ds_costos_ad_internacional_combined_usa', ";
        $Query .= "no_a_payments_internacional_combined=$no_payments_a_internacional_combined_usa, ds_a_freq_internacional_combined='$frequency_a_internacional_combined_usa', mn_a_due_internacional_combined=$amount_due_a_internacional_combined_usa, mn_a_paid_internacional_combined=$amount_paid_a_internacional_combined_usa, no_a_interes_internacional_combined=$interes_a_internacional_combined_usa, ";
        $Query .= "no_b_payments_internacional_combined=$no_payments_b_internacional_combined_usa, ds_b_freq_internacional_combined='$frequency_b_internacional_combined_usa', mn_b_due_internacional_combined=$amount_due_b_internacional_combined_usa, mn_b_paid_internacional_combined=$amount_paid_b_internacional_combined_usa, no_b_interes_internacional_combined=$interes_b_internacional_combined_usa, ";
        $Query .= "no_c_payments_internacional_combined=$no_payments_c_internacional_combined_usa, ds_c_freq_internacional_combined='$frequency_c_internacional_combined_usa', mn_c_due_internacional_combined=$amount_due_c_internacional_combined_usa, mn_c_paid_internacional_combined=$amount_paid_c_internacional_combined_usa, no_c_interes_internacional_combined=$interes_c_internacional_combined_usa, ";
        $Query .= "no_d_payments_internacional_combined=$no_payments_d_internacional_combined_usa, ds_d_freq_internacional_combined='$frequency_d_internacional_combined_usa', mn_d_due_internacional_combined=$amount_due_d_internacional_combined_usa, mn_d_paid_internacional_combined=$amount_paid_d_internacional_combined_usa, no_d_interes_internacional_combined=$interes_d_internacional_combined_usa, ";


        $Query .= "no_horas_week=$no_horas_week WHERE fl_programa=$clave AND fl_pais=226 ";
    }
    else
    {
        $Query  = "INSERT INTO k_programa_costos_pais (fl_programa,fl_pais, mn_app_fee,mn_app_fee_internacional,mn_tuition,mn_tuition_internacional, mn_costs,mn_costs_internacional, ds_costs,ds_costs_internacional, ";
        $Query .= "no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes,
			   no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, ";
        $Query .= "no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes,
			   no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes, ";

        $Query .= "no_a_payments_internacional, ds_a_freq_internacional, mn_a_due_internacional, mn_a_paid_internacional, no_a_interes_internacional,
	           no_b_payments_internacional, ds_b_freq_internacional, mn_b_due_internacional, mn_b_paid_internacional, no_b_interes_internacional, ";
        $Query .= "no_c_payments_internacional, ds_c_freq_internacional, mn_c_due_internacional, mn_c_paid_internacional, no_c_interes_internacional,
	           no_d_payments_internacional, ds_d_freq_internacional, mn_d_due_internacional, mn_d_paid_internacional, no_d_interes_internacional, ";

        $Query .= "no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_horas_week ";
        $Query .= ",mn_app_fee_combined,mn_app_fee_internacional_combined,mn_tuition_combined,mn_tuition_internacional_combined,mn_costs_combined,mn_costs_internacional_combined,ds_costs_combined,ds_costs_internacional_combined, ";

        $Query .= "no_a_payments_combined, ds_a_freq_combined, mn_a_due_combined, mn_a_paid_combined, no_a_interes_combined, no_b_payments_combined, ds_b_freq_combined, mn_b_due_combined, mn_b_paid_combined, no_b_interes_combined, ";
        $Query .= "no_c_payments_combined, ds_c_freq_combined, mn_c_due_combined, mn_c_paid_combined, no_c_interes_combined, no_d_payments_combined, ds_d_freq_combined, mn_d_due_combined, mn_d_paid_combined, no_d_interes_combined, ";

        $Query .= "no_a_payments_internacional_combined, ds_a_freq_internacional_combined, mn_a_due_internacional_combined, mn_a_paid_internacional_combined, no_a_interes_internacional_combined,
			   no_b_payments_internacional_combined, ds_b_freq_internacional_combined, mn_b_due_internacional_combined, mn_b_paid_internacional_combined, no_b_interes_internacional_combined, ";
        $Query .= "no_c_payments_internacional_combined, ds_c_freq_internacional_combined, mn_c_due_internacional_combined, mn_c_paid_internacional_combined, no_c_interes_internacional_combined,
	           no_d_payments_internacional_combined, ds_d_freq_internacional_combined, mn_d_due_internacional_combined, mn_d_paid_internacional_combined, no_d_interes_internacional_combined ";
        $Query .= ") ";

        $Query .= "VALUES($clave,226, $app_fee_usa,$app_fee_internacional_usa, $tuition_usa,$tuition_internacional_usa, $no_costos_ad_usa,$no_costos_ad_internacional_usa, '$ds_costos_ad_usa','$ds_costos_ad_internacional_usa', ";
        $Query .= "$no_payments_a_usa, '$frequency_a_usa', $amount_due_a_usa, $amount_paid_a_usa, $interes_a_usa, ";
        $Query .= "$no_payments_b_usa, '$frequency_b_usa', $amount_due_b_usa, $amount_paid_b_usa, $interes_b_usa, ";
        $Query .= "$no_payments_c_usa, '$frequency_c_usa', $amount_due_c_usa, $amount_paid_c_usa, $interes_c_usa, ";
        $Query .= "$no_payments_d_usa, '$frequency_d_usa', $amount_due_d_usa, $amount_paid_d_usa, $interes_d_usa, ";

        $Query .= "$no_payments_a_internacional_usa, '$frequency_a_internacional_usa', $amount_due_a_internacional_usa, $amount_paid_a_internacional_usa, $interes_a_internacional_usa, ";
        $Query .= "$no_payments_b_internacional_usa, '$frequency_b_internacional_usa', $amount_due_b_internacional_usa, $amount_paid_b_internacional_usa, $interes_b_internacional_usa, ";
        $Query .= "$no_payments_c_internacional_usa, '$frequency_c_internacional_usa', $amount_due_c_internacional_usa, $amount_paid_c_internacional_usa, $interes_c_internacional_usa, ";
        $Query .= "$no_payments_d_internacional_usa, '$frequency_d_internacional_usa', $amount_due_d_internacional_usa, $amount_paid_d_internacional_usa, $interes_d_internacional_usa, ";

        $Query .= "$no_horas, $no_semanas, '$ds_credential', '$cl_delivery', '$ds_language', $cl_type, $no_horas_week, ";
        $Query .= "$app_fee_combined_usa,$app_fee_internacional_combined_usa,$tuition_combined_usa,$tuition_internacional_combined_usa, $no_costos_ad_combined_usa,$no_costos_ad_internacional_combined_usa, '$ds_costos_ad_combined_usa','$ds_costos_ad_internacional_combined_usa', ";

        $Query .= "$no_payments_a_combined_usa, '$frequency_a_combined_usa', $amount_due_a_combined_usa, $amount_paid_a_combined_usa, $interes_a_combined_usa, ";
        $Query .= "$no_payments_b_combined_usa, '$frequency_b_combined_usa', $amount_due_b_combined_usa, $amount_paid_b_combined_usa, $interes_b_combined_usa, ";
        $Query .= "$no_payments_c_combined_usa, '$frequency_c_combined_usa', $amount_due_c_combined_usa, $amount_paid_c_combined_usa, $interes_c_combined_usa, ";
        $Query .= "$no_payments_d_combined_usa, '$frequency_d_combined_usa', $amount_due_d_combined_usa, $amount_paid_d_combined_usa, $interes_d_combined_usa, ";


        $Query .= "$no_payments_a_internacional_combined_usa, '$frequency_a_internacional_combined_usa', $amount_due_a_internacional_combined_usa, $amount_paid_a_internacional_combined_usa, $interes_a_internacional_combined_usa, ";
        $Query .= "$no_payments_b_internacional_combined_usa, '$frequency_b_internacional_combined_usa', $amount_due_b_internacional_combined_usa, $amount_paid_b_internacional_combined_usa, $interes_b_internacional_combined_usa, ";
        $Query .= "$no_payments_c_internacional_combined_usa, '$frequency_c_internacional_combined_usa', $amount_due_c_internacional_combined_usa, $amount_paid_c_internacional_combined_usa, $interes_c_internacional_combined_usa, ";
        $Query .= "$no_payments_d_internacional_combined_usa, '$frequency_d_internacional_combined_usa', $amount_due_d_internacional_combined_usa, $amount_paid_d_internacional_combined_usa, $interes_d_internacional_combined_usa ";

        $Query .=") ";
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



# Streams
  $rowt = RecuperaValor("SELECT fl_tema FROM c_f_tema WHERE nb_tema='$nb_programa'");
  if(!empty($fg_streams) && empty($rowt[0])){
    $Querys  = "INSERT INTO c_f_tema (nb_tema, no_orden, ds_ruta_imagen, fg_tipo) ";
    $Querys .= "VALUES('$nb_programa', 2, '$ds_ruta_imagen', 'P')";
    EjecutaQuery($Querys);
  }

  # Redirige al listado
  header("Location: ".ObtenProgramaBase());

?>