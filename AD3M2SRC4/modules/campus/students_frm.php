<?php
# Libreria de funciones
require '../../lib/general.inc.php';


# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion();

# Recibe parametros
$origen = RecibeParametroHTML('origen', False, True);
$destino = RecibeParametroHTML('destino', False, True);
if (!empty($origen) || !empty($destino)) {
    $clave = RecibeParametroHTML('clave', False, True);
} else {
    $clave = RecibeParametroNumerico('clave');
}


$fg_error = RecibeParametroNumerico('fg_error');
$error = RecibeParametroNumerico('error');
$confirmacion = RecibeParametroNumerico('confirmacion');

//$clave = 190; //231; //248; // 
# Determina si es alta o modificacion
if (!empty($clave))
    $permiso = PERMISO_DETALLE;
else
    $permiso = PERMISO_ALTA;

# Verifica que el usuario tenga permiso de usar esta funcion
if (!ValidaPermiso(FUNC_ALUMNOS, $permiso) OR $permiso == PERMISO_ALTA) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
}
//programa actual
$programa = ObtenProgramaActual();

$SimpleNotify = new SimpleNotify();
$mn_total_pagado=0;
$mn_total_falta_pagar=0;
$grado_repetido=NULL;

# Inicializa variables
if (!$fg_error) { // Sin error, viene del listado
    $Query = "SELECT ds_login, fg_activo, " . ConsultaFechaBD('fe_alta', FMT_FECHA) . " fe_alta, ";
    $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
    $Query .= "(" . ConcatenaBD($concat) . ") 'fe_ultacc', ";
    $Query .= "no_accesos, ds_nombres, ds_apaterno, ds_amaterno, a.ds_email, a.fl_perfil, b.nb_perfil, fg_genero, ";
    $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA) . " fe_nacimiento, a.cl_sesion, ds_notas, d.ds_number, d.ds_alt_number, ";
    $Query .= "d.ds_add_number, d.ds_add_street, d.ds_add_city, d.ds_add_state, d.ds_add_zip, d.ds_add_country, ";
    $Query .= "e.ds_m_add_number, e.ds_m_add_street, e.ds_m_add_city, e.ds_m_add_state, e.ds_m_add_zip, e.ds_m_add_country, d.ds_link_to_portfolio, d.ds_ruta_foto ds_foto_oficial, fg_responsable, ";
    $Query .= "e.cl_preference_1, e.cl_preference_2, e.cl_preference_3, ds_p_name, ds_education_number, ds_usual_name ";
    $Query .= ", e.ds_citizenship, e.fg_study_permit, e.fg_study_permit_other, e.fg_aboriginal, e.ds_aboriginal, e.fg_health_condition, e.ds_health_condition,d.fl_immigrations_status ";
	$Query .= ",c.fg_absence,c.fg_change_status,a.ds_alias,a.ds_graduate_status,c.notation_transcript  ";
    $Query .= ",d.passport_number, ";
    $Query .= ConsultaFechaBD('d.passport_exp_date', FMT_FECHA) . " passport_exp_date ";
    $Query .= "FROM c_usuario a, c_perfil b, c_alumno c, k_ses_app_frm_1 d, k_app_contrato e ";
    $Query .= "WHERE a.fl_perfil=b.fl_perfil AND a.cl_sesion=d.cl_sesion ";
    $Query .= "AND a.fl_usuario=c.fl_alumno AND a.cl_sesion=e.cl_sesion ";
    $Query .= "AND fl_usuario=$clave";
	
    $rs = EjecutaQuery($Query);
    $registro = CuentaRegistros($rs);
    # no tiene contrato
    if (empty($registro)) {
        $Query = "SELECT ds_login, fg_activo, " . ConsultaFechaBD('fe_alta', FMT_FECHA) . " fe_alta, ";
        $Query .= "(" . ConcatenaBD($concat) . ") 'fe_ultacc', ";
        $Query .= "no_accesos, ds_nombres, ds_apaterno, ds_amaterno, a.ds_email, a.fl_perfil, b.nb_perfil, fg_genero, ";
        $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA) . " fe_nacimiento, a.cl_sesion, ds_notas, d.ds_number, d.ds_alt_number, ";
        $Query .= "d.ds_add_number, d.ds_add_street, d.ds_add_city, d.ds_add_state, d.ds_add_zip, d.ds_add_country,d.fl_immigrations_status ";
		$Query .=",c.fg_absence,c.fg_change_status,a.ds_alias,c.notation_transcript ";
        $Query .= ",d.passport_number, ";
        $Query .= ConsultaFechaBD('d.passport_exp_date', FMT_FECHA) . " passport_exp_date ";
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
    //$fl_provincia = str_texto($row[20]);
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
    # Person Responsible
    $fg_responsable = $row[31];
	$fg_absence=$row['fg_absence'];
	$fg_change_status=$row['fg_change_status'];
	$ds_alias=$row['ds_alias'];
    $ds_graduate_status=$row['ds_graduate_status'];
    $fl_immigrations_status=$row['fl_immigrations_status'];
    $notation_transcript = $row['notation_transcript'];
    $passport_number = $row['passport_number'];
    $passport_exp_date = $row['passport_exp_date'];

    if(!empty($fg_responsable)){
      $Query_res  = "SELECT ds_fname_r, ds_lname_r, ds_email_r, ds_aemail_r, ds_pnumber_r, ds_relation_r, fg_email ";
      $Query_res .= "FROM k_presponsable WHERE cl_sesion='$cl_sesion'";
      $row_res = RecuperaValor($Query_res);
      $ds_fname_r = str_ascii($row_res[0]);
      $ds_lname_r = str_ascii($row_res[1]);
      $ds_email_r = str_ascii($row_res[2]);
      $ds_aemail_r = str_ascii($row_res[3]);
      $ds_pnumber_r= str_ascii($row_res[4]);
      $ds_relation_r = str_ascii($row_res[5]);
      $fg_email = $row_res[6];
    }
    $cl_preference_1 = $row[32];
    $cl_preference_2 = $row[33];
    $cl_preference_3 = $row[34];
    $ds_p_name = str_texto($row[35]);
    $ds_education_number = $row[36];
    $ds_usual_name = str_texto($row[37]);
    $ds_citizenship = str_texto($row[38]);
    $fg_study_permit = $row[39];
    $fg_study_permit_other = $row[40];
    $fg_aboriginal = $row[41];
    $ds_aboriginal = str_texto($row[42]);
    $fg_health_condition = $row[43];
    $ds_health_condition = str_texto($row[44]);

    $Query = "SELECT ds_header,ds_contrato,ds_footer,mn_discount,ds_discount FROM k_app_contrato where cl_sesion='$cl_sesion' ";
    $row = RecuperaValor($Query);
    $ds_header = $row['ds_header'];
    $ds_contrato = $row['ds_contrato'];
    $ds_footer = $row['ds_footer'];
    $mn_discount = $row['mn_discount'];
    $ds_discount = $row['ds_discount'];

    if ($ds_discount > 0) {
        
        EjecutaQuery("UPDATE c_sesion SET fg_scholarship='1' WHERE cl_sesion='$cl_sesion' ");

    } else {
        EjecutaQuery("UPDATE c_sesion SET fg_scholarship='0' WHERE cl_sesion='$cl_sesion' ");
    }



    $row = RecuperaValor("SELECT fg_pago,fg_scholarship FROM c_sesion WHERE cl_sesion='$cl_sesion'");
    $fg_pago = $row[0];
    $fg_scholarship=$row['fg_scholarship'];
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
    $notation_transcript_err = "";
    $passport_exp_date_err = "";
} else { // Con error, recibe parametros (viene de la pagina de actualizacion)
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
    $ds_number_err = RecibeParametroHTML('ds_number_err');
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
    // $fg_provincia = RecibeParametroNumerico('fg_provincia');
    // $fl_provincia = RecibeParametroNumerico('fl_provincia');
    # Person Responsable
    $fg_responsable = RecibeParametroBinario('fg_responsable');    
    $ds_fname_r = RecibeParametroHTML('ds_fname_r');
    $ds_fname_r_err = RecibeParametroHTML('ds_fname_r_err');    
    $ds_lname_r = RecibeParametroHTML('ds_lname_r');
    $ds_lname_r_err = RecibeParametroHTML('ds_lname_r_err');
    $ds_email_r = RecibeParametroHTML('ds_email_r');
    $ds_email_r_err = RecibeParametroHTML('ds_email_r_err');
    $ds_aemail_r = RecibeParametroHTML('ds_aemail_r');
    $ds_pnumber_r= RecibeParametroHTML('ds_pnumber_r');
    $ds_pnumber_r_err= RecibeParametroHTML('ds_pnumber_r_err');
    $ds_relation_r = RecibeParametroHTML('ds_relation_r');
    $ds_relation_r_err = RecibeParametroHTML('ds_relation_r_err');
    $fg_email = RecibeParametroNumerico('fg_email');
    # Preferencias
    $cl_preference_1 = RecibeParametroNumerico('cl_preference_1');
    $cl_preference_1_err = RecibeParametroNumerico('cl_preference_1_err');
    $cl_preference_2 = RecibeParametroNumerico('cl_preference_2');
    $cl_preference_2_err = RecibeParametroNumerico('cl_preference_2_err');
    $cl_preference_3 = RecibeParametroNumerico('cl_preference_3');
    $cl_preference_3_err = RecibeParametroNumerico('cl_preference_3_err');
    
    $ds_p_name = RecibeParametroHTML('ds_p_name');
    $ds_education_number = RecibeParametroHTML('ds_education_number');
    $ds_usual_name = RecibeParametroHTML('ds_usual_name');
    
    $ds_citizenship = RecibeParametroHTML('ds_citizenship');
    $ds_sin = RecibeParametroNumerico('ds_sin');
    $fg_study_permit = RecibeParametroNumerico('fg_study_permit');
    $fg_study_permit_other = RecibeParametroNumerico('fg_study_permit_other');
    $fg_aboriginal = RecibeParametroBinario('fg_aboriginal');
    $ds_aboriginal = RecibeParametroHTML('ds_aboriginal');
    $fg_health_condition = RecibeParametroHTML('fg_health_condition');
    $ds_health_condition = RecibeParametroHTML('ds_health_condition');
    $fg_disabilityie = RecibeParametroBinario('fg_disabilityie');
    $fg_disabilityie_err = RecibeParametroBinario('fg_disabilityie_err');
    $ds_disability = RecibeParametroHTML('ds_disability');
    $ds_disability_err = RecibeParametroHTML('ds_disability_err');
	$fg_absence=RecibeParametroBinario('fg_absence');
	$fg_change_status=RecibeParametroBinario('fg_change_status');
	$ds_alias=RecibeParametroHTML('ds_alias');
    $ds_alias_err=RecibeParametroHTML('ds_alias_err');

    $ds_header = RecibeParametroHTML('ds_header');
    $ds_contrato = RecibeParametroHTML('ds_contrato');
    $ds_footer = RecibeParametroHTML('ds_footer');
    $ds_discount = RecibeParametroHTML('ds_discount');
    $mn_discount = RecibeParametroNumerico('mn_discount');

    $notation_transcript = RecibeParametroHTML('notation_transcript');

    $passport_number = RecibeParametroHTML('passport_number');
    $passport_number_err = RecibeParametroHTML('passport_number_err');
    $passport_exp_date = RecibeParametroHTML('passport_exp_date');
    $passport_exp_date_err = RecibeParametroHTML('passport_exp_date_err');





}

if(empty($ds_login)){
    
    ##Nueva implemntacion para que todos tengan un id desde la aplicacion.
    $Queryid="SELECT id_alumno FROM c_sesion WHERE cl_sesion='$cl_sesion' ";
    $rowid=RecuperaValor($Queryid);
    $ds_login=$rowid['id_alumno'];
    
}

# Recupera datos de la sesion y forma de aplicacion
$concat = array(ConsultaFechaBD('fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultmod', FMT_HORA));
$Query = "SELECT fg_paypal, (" . ConcatenaBD($concat) . ") 'fe_ultmod', fl_sesion, fg_inscrito ";
$Query .= "FROM c_sesion ";
$Query .= "WHERE cl_sesion='$cl_sesion'";
$row = RecuperaValor($Query);
$fg_paypal = $row[0];
$fe_ultmod = $row[1];
$fl_sesion = $row[2];
$fg_inscrito = $row[3];

# Recupera datos del aplicante: forma 1
$Query = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
$Query .= ConsultaFechaBD('fe_birth', FMT_FECHA) . " fe_birth, ";
$Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, d.ds_pais, ";
$Query .= "ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, e.ds_pais, ";
$Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, nb_programa, a.fl_periodo, a.fl_programa, nb_periodo, fl_template,fg_taxes, ds_sin ";
$Query .=",a.fg_disability,a.ds_disability,a.cl_recruiter,a.comments  ";
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
if (empty($fl_periodo))
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
$fg_disabilityie=$row['fg_disability'];
$ds_disability=$row['ds_disability'];
$cl_recruiter=$row['cl_recruiter'];
$ds_sin=$row['ds_sin'];
$comments=$row['comments'];


#Recuperamos el advisor name.
$Query  = "SELECT CONCAT( ds_nombres, ' ', ds_apaterno ) , fl_usuario FROM c_usuario usr, c_perfil per ";
$Query .= "WHERE  usr.fl_usuario=$cl_recruiter AND usr.fl_perfil = per.fl_perfil AND usr.fl_perfil=".PERFIL_RECRUITER." AND usr.fg_activo='1' ORDER BY fg_default ASC , ds_nombres ASC ";
$row=RecuperaValor($Query);                     
$nb_nombre_advisor=!empty($row[0])?str_texto($row[0]):NULL;


#Recupera datos adicionales a la forma 1 y del contrato del aplicante
$Query = "SELECT no_contrato, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_discount, ds_discount, mn_tot_tuition, mn_tot_program, ";
$Query .= "mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
$Query .= "ds_cadena, ds_firma_alumno, fg_opcion_pago, DATE_FORMAT(fe_firma, '%M %d, %Y'), ds_p_name, ds_education_number, fg_international, ";
$Query .= "cl_preference_1, cl_preference_2, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_pais, ds_a_email, cl_preference_3 ";
$Query .= "FROM k_app_contrato  a LEFT JOIN c_pais b ON a.ds_m_add_country=b.fl_pais ";
$Query .= "WHERE cl_sesion='$cl_sesion' ";
$Query .= "ORDER BY no_contrato";
$rs = EjecutaQuery($Query);
while ($row = RecuperaRegistro($rs)) {
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
    # Si no hay error 
    if (empty($fg_error))
        $fg_international = $row[23];
    //$cl_preference_1 = $row[24];
    //$cl_preference_2 = $row[25];
    if (empty($fg_error))
        $ds_a_email = $row[32];
    //$cl_preference_3 = $row[33];
}

# Recupera datos de pagos del curso
$Query = "SELECT no_a_payments, ds_a_freq, no_b_payments, ds_b_freq, no_c_payments, ds_c_freq, no_d_payments, ds_d_freq, cl_type, ";
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
$Query = "SELECT ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7 ";
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
$Query = "SELECT fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, ";
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
$Query = "SELECT ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, ds_resp_8 ";
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
$Querystd  = "SELECT a.fl_zona_horaria, nb_zona_horaria, ds_ruta_avatar, ds_ruta_foto, ds_website, ds_gustos, ds_pasatiempos, no_gmt,a.no_promedio_t, ";
$Querystd .= "CASE no_promedio_t WHEN 0 THEN 0 ELSE (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t))  END cl_calificacion, ";
$Querystd .= "CASE no_promedio_t WHEN 0 THEN 0 ELSE (SELECT fg_aprobado FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t))  END cal_aprobada ";
$Querystd .= "FROM c_alumno a, c_zona_horaria b ";
$Querystd .= "WHERE a.fl_zona_horaria=b.fl_zona_horaria ";
$Querystd .= "AND fl_alumno=$clave ";
$rowstd = RecuperaValor($Querystd);
$fl_zona_horaria = $rowstd[0];
$ds_zona_horaria = str_texto($rowstd[1]) . " (GMT " . $rowstd[7] . ")";
$ds_ruta_avatar = str_texto($rowstd[2]);
$ds_ruta_foto = str_texto($rowstd[3]);
$ds_website = str_texto($rowstd[4]);
$ds_gustos = str_texto($rowstd[5]);
$ds_pasatiempos = str_texto($rowstd[6]);
$no_promedio_t = $rowstd[8];
$cl_calificacion = $rowstd[9];
$cal_aprobada = $rowstd[10];

# Obtiene el grupo, el term y el maestro
$Query = "SELECT a.fl_grupo, b.fl_term, CONCAT(c.ds_nombres, ' ', c.ds_apaterno), b.nb_grupo, d.no_grado  ";
$Query .= "FROM k_alumno_grupo a LEFT JOIN (c_grupo b LEFT JOIN c_usuario c ON b.fl_maestro = c.fl_usuario) ON a.fl_grupo = b.fl_grupo ";
$Query .= "LEFT JOIN k_term d ON d.fl_term=b.fl_term ";
$Query .= "WHERE fl_alumno = $clave  AND a.fg_grupo_global <>'1'  ";
$row1 = RecuperaValor($Query);
$fl_grupo = !empty($row1[0])?$row1[0]:NULL;
$fl_term = !empty($row1[1])?$row1[1]:NULL;
$nb_maestro = !empty($row1[2])?$row1[2]:NULL;
$ds_grupo = !empty($row1[3])?$row1[3]:NULL;
$no_grado_actual = !empty($row1[4])?$row1[4]:NULL;

# MRA: 16 sept 2014: Si aun no tiene un grupo asignado, obtiene el term de la forma de aplicacion (fl_term se usa para obterner la informacion de pagos)
if (empty($fl_term)) {
    //$Query ="SELECT fl_term FROM k_term WHERE fl_programa=$fl_programa AND fl_periodo=$fl_periodo AND ";
    $Query = "SELECT MIN(fl_term) FROM k_alumno_term WHERE fl_alumno=$clave ";
    $row = RecuperaValor($Query);
    $fl_term = $row[0];
}

# Recupera el program start date 
$Query = "SELECT nb_periodo, " . ConsultaFechaBD('c.fe_inicio', FMT_FECHA) . " ";
$Query .= "FROM k_term b, c_periodo c, k_alumno_term d ";
$Query .= "WHERE b.fl_periodo=c.fl_periodo ";
$Query .= "AND b.fl_term=d.fl_term AND d.fl_alumno='$clave' ";
$Query .= "AND no_grado=1 ";
$row2 = RecuperaValor($Query);
$fe_inicio = $row2[0];
$fe_inicio_term = $row2[1];

# Recupera el term inicial
# Si no tiene fecha inicio buscara por medio del term inicial
if (empty($fe_inicio) AND empty($fe_inicio_term)) {
    $Query = "SELECT fl_term_ini FROM k_term WHERE fl_programa=$fl_programa AND fl_term=$fl_term";
    $row = RecuperaValor($Query);
    $fl_term_ini = $row[0];
    $row = RecuperaValor("SELECT nb_periodo, DATE_FORMAT(b.fe_inicio, '%d-%m-%Y') FROM k_term a, c_periodo b WHERE a.fl_periodo = b.fl_periodo AND fl_term=$fl_term_ini");
    $fe_inicio = $row[0];
    $fe_inicio_term = $row[1];
}

# Recupera datos de Official Transcript
$Query = "SELECT ";
$Query .= ConsultaFechaBD('fe_carta', FMT_FECHA) . " fe_carta, ";
$Query .= ConsultaFechaBD('fe_contrato', FMT_FECHA) . " fe_contrato, ";
$Query .= ConsultaFechaBD('fe_fin', FMT_FECHA) . " fe_fin, ";
$Query .= ConsultaFechaBD('fe_completado', FMT_FECHA) . " fe_completado, ";
$Query .= ConsultaFechaBD('fe_emision', FMT_FECHA) . " fe_emision, ";
$Query .= "fg_certificado, fg_honores, ";
$Query .= ConsultaFechaBD('fe_graduacion', FMT_FECHA) . " fe_graduacion, ";
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
if($clave==10072){

}else{
if (empty($fe_fin) OR empty($fe_completado)) {

    #Obtenemos los meses que dura el curso y las fechas fin y completado
    $meses = $no_semanas / 4;
    $fe_fin = date("d-m-Y", strtotime("$fe_inicio_term + $meses months"));
    $fe_completado = date("d-m-Y", strtotime("$fe_inicio_term + $meses months"));
}
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
$Query = "SELECT count(a.fl_leccion), a.no_grado, b.nb_programa ";
$Query .= "FROM c_leccion a, c_programa b ";
$Query .= "WHERE a.fl_programa=b.fl_programa ";
$Query .= "AND a.fl_programa=$fl_programa ";
$Query .= "GROUP BY a.no_grado ";
$Query .= "ORDER BY a.no_grado ";
$rs = EjecutaQuery($Query);

# Recupera los distintos fl_term en los que ha estado un alumno
# Si hay 2 term con el mismo grado obtendremos el ultimo que se inserto
$Query = "SELECT MAX(a.fl_term), no_promedio ";
$Query .= "FROM k_alumno_term a, k_term b, c_periodo c ";
$Query .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$clave ";
$Query .= "GROUP BY b.no_grado ORDER BY c.fe_inicio, b.no_grado";
$consulta = EjecutaQuery($Query);

for ($tot_grados = 0; $row = RecuperaRegistro($rs); $tot_grados++) {
    $tot_lecciones[$tot_grados] = $row[0];
    $no_grado[$tot_grados] = $row[1];
    $nb_programa = str_uso_normal($row[2]);
    $row_term = RecuperaRegistro($consulta);
    $term_nivel = !empty($row_term[0])?$row_term[0]:NULL;
    if (empty($term_nivel))
        $term_nivel = $fl_term;
    $no_tpromedio[$tot_grados] = !empty($row_term[1])?$row_term[1]:NULL;
    # Recupera las lecciones del grado
    $Query = "SELECT a.fl_leccion, a.no_semana, a.ds_titulo, b.fl_semana ";
    $Query .= "FROM c_leccion a LEFT JOIN k_semana b ";
    $Query .= "ON (a.fl_leccion=b.fl_leccion AND b.fl_term=$term_nivel) ";
    $Query .= "WHERE a.fl_programa=$fl_programa ";
    $Query .= "AND a.no_grado=$no_grado[$tot_grados] ";
    $Query .= "ORDER BY a.no_semana ";
    $rs2 = EjecutaQuery($Query);
    for ($j = 0; $row2 = RecuperaRegistro($rs2); $j++) {
        $fl_leccion[$tot_grados][$j] = $row2[0];
        $no_semana[$tot_grados][$j] = $row2[1];
        $ds_titulo[$tot_grados][$j] = str_uso_normal($row2[2]);
        $fl_semana[$tot_grados][$j] = $row2[3];
    }
}

# Presenta forma de captura
PresentaHeader();
echo "
  <script type='text/javascript' src='" . PATH_JS . "/sendtemplate.js.php'></script>";

PresentaEncabezado(FUNC_ALUMNOS);

# Funciones para preview de imagenes
require 'preview.inc.php';

# Forma para cambiar contrasena a otros usuarios
if (ValidaPermiso(FUNC_PWD_OTROS, PERMISO_EJECUCION)) {
    $ds_cambiar_pwd = "<a class='btn btn-primary' href='javascript:cambio_pwd_otros.submit();'><i class='fa fa-key'>&nbsp;</i>" . ObtenEtiqueta(126) . "</a>";
    echo "
  <form name='cambio_pwd_otros' method='post' action='pwd_frm.php'>
    <input type='hidden' name='clave' value='$clave'>
  </form>\n";
} else {
    $ds_cambiar_pwd = " ";
}

#Liga para generar reporte de historia de login
$ds_login_rpt = "<a class='btn btn-primary' href='historia_login_frm.php?clave=$clave'><i class='fa fa-sign-in'>&nbsp;</i>Login record</a>";

#Liga para generar reporte oficial para PCTIA
$ds_pctia_rpt = "<a class='btn btn-primary' href='../reports/pctia_rpt.php?clave=$clave' target='blank'><i class='fa fa-file-pdf-o'>&nbsp;</i>" . ObtenEtiqueta(534) . "</a>";
if (!empty($fe_completado))
    $ds_diploma = "<a class='btn btn-primary' href='../reports/newDiploma_rpt.php?clave=$clave' target='blank'><i class='fa fa-file-pdf-o'>&nbsp;</i>" . ObtenEtiqueta(535) . "</a>";
else
    $ds_diploma = "&nbsp;";
# super user
$super_user = "<a class='btn btn-primary' href=\"javascript:super_user('$ds_login');\"'><i class='fa fa-file-user'>&nbsp;</i>" . ObtenEtiqueta(808) . "</a>";

# Forma para captura de datos
Forma_Inicia($clave, True);

?>
<div id='widget-grid' >
    <?php
    if ($fg_error)
        Forma_PresentaError();
    ?>
    <div role="widget" style="" class="jarviswidget" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-custombutton="false" data-widget-sortable="false">
        <header role="heading">
            <div role="menu" class="jarviswidget-ctrls">   
                <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
            </div>
            <span class="widget-icon"> <i class="fa fa-user"></i> </span>
            <h2>
              <strong>
                <?php
                echo $ds_nombres . ' ' . ($ds_amaterno != '' ? $ds_amaterno . ' ' : '') . $ds_apaterno
                ?>                
              </strong>                
              <small class="font-sm"><?php echo $ds_login ?></small>
            </h2>
            <!--                
            <div role="menu" class="widget-toolbar">
                <div class="btn-group">
                    <button aria-expanded="false" class="btn dropdown-toggle btn-xs btn-warning" data-toggle="dropdown">
                        Contracts <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="javascript:void(0);">Option 1</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">Option 2</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">Option 3</a>
                        </li>
                    </ul>
                </div>
            </div>
            -->
            <div role="menu" class="widget-toolbar">
                <div class="btn-group">
                    <button aria-expanded="false" class="btn dropdown-toggle btn-xs btn-info" data-toggle="dropdown">
                        Actions <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="javascript:super_user('<?php echo $ds_login ?>');"><i class='fa fa-sign-in'>&nbsp;</i><?php echo ObtenEtiqueta(808) ?> </a>
                        </li>
                        <?php
                        if (ValidaPermiso(FUNC_PWD_OTROS, PERMISO_EJECUCION)) {
                            ?>
                            <li>
                                <!--<a href='javascript:cambio_pwd_otros.submit();'>
                                    <i class='fa fa-key'>&nbsp;</i><?php echo ObtenEtiqueta(126) ?></a>                                
                                <form name='cambio_pwd_otros' method='post' action='pwd_frm.php'>
                                    <input type='hidden' name='clave' value='<?php echo $clave ?>'>
                                </form>-->
                                <a href='javascript:change_pwd();'>
                                    <i class='fa fa-key'>&nbsp;</i><?php echo ObtenEtiqueta(126) ?></a>
                            </li>
                        <?php }
                        ?>
                        <!--<li>
                            <!--<a href='historia_login_frm.php?clave=<?php echo $clave ?>'><i class='fa fa-navicon'>&nbsp;</i>Login record</a>
                            <a href="javascript:login_record();"><i class='fa fa-navicon'>&nbsp;</i>Login record</a>
                        </li>-->
                        <hr>
                        <li>
                            <a href="javascript:showDialog();"><i class="fa fa-envelope">&nbsp;</i>Send Letter</a>
                        </li>
                    </ul>                    
                </div>
            </div>
<!--<span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>-->
        </header>
        <!-- widget div-->
        <!--<div role="content" class="no-padding">-->
        <div class="no-padding">
            <!-- widget edit box -->
            <div class="jarviswidget-editbox">
                <!-- This area used as dropdown edit box -->
            </div>
            <!-- end widget edit box -->
            <!-- widget content -->
            <div class="widget-body">
                <ul id="myTab1" class="nav nav-tabs bordered">
                    <!--                    
                    <li class="active">
                        <a href="#s1" data-toggle="tab">Left Tab <span class="badge bg-color-blue txt-color-white">12</span></a>
                    </li>
                    -->
                    <li class="active">
                        <a href="#student" data-toggle="tab"><i class="fa fa-fw fa-lg fa-user"></i> General</a>
                    </li>
                    <li>
                        <a href="#communicationsHistory" data-toggle="tab"><i class="fa fa-fw fa-lg fa-envelope"></i> Communications</a>
                    </li>
                    <li>
                        <a href="#payments" data-toggle="tab"><i class="fa fa-fw fa-lg fa-usd"></i> Payment History</a>
                    </li>
                    <li>
                        <a href="#applicationForm" data-toggle="tab"><i class="fa fa-fw fa-lg fa-file-text"></i> Application Form</a>
                    </li>
                    <li>
                        <a href="#grades" data-toggle="tab"><i class="fa fa-fw fa-lg fa-folder-open"></i> Academic History</a>
                    </li>
                    <li>
                        <a href="#status" data-toggle="tab"><i class="fa fa-fw fa-lg fa-asterisk"></i> Status</a>
                    </li>
                    <li>
                        <a href="#record" data-toggle="tab"><i class="fa fa-fw fa-lg fa-navicon"></i> Login Record</a>
                    </li>
                    <li style="display:none;">
                        <a href="#contract" data-toggle="tab"><i class="fa fa-fw fa-lg fa-table"></i> Contract</a>
                    </li>
                    <?php
                    /*
                      ?>
                      <li class="dropdown">
                      <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Utilities <b class="caret"></b></a>
                      <ul class="dropdown-menu">
                      <li>
                      <a href="javascript:super_user('<?php echo $ds_login ?>');"><i class='fa fa-sign-in'>&nbsp;</i><?php echo ObtenEtiqueta(808) ?> </a>
                      </li>
                      <?php
                      if (ValidaPermiso(FUNC_PWD_OTROS, PERMISO_EJECUCION)) {
                      ?>
                      <li>
                      <a href='javascript:cambio_pwd_otros.submit();'>
                      <i class='fa fa-key'>&nbsp;</i><?php echo ObtenEtiqueta(126) ?>
                      </a>
                      <form name='cambio_pwd_otros' method='post' action='pwd_frm.php'>
                      <input type='hidden' name='clave' value='<?php echo $clave ?>'>
                      </form>
                      </li>
                      <?php }
                      ?>
                      <li>
                      <a href='historia_login_frm.php?clave=<?php echo $clave ?>'><i class='fa fa-navicon'>&nbsp;</i>Login record</a>
                      </li>
                      <li>
                      <a href='../reports/pctia_rpt.php?clave=<?php echo $clave ?>'><i class='fa fa-file-pdf-o'>&nbsp;</i><?php echo ObtenEtiqueta(534) ?></a>
                      </li>
                      <?php
                      if (!empty($fe_completado)) {
                      ?>
                      <li>
                      <a href='../reports/newDiploma_rpt.php?clave=<?php echo $clave ?>'><i class='fa fa-file-pdf-o'>&nbsp;</i><?php echo ObtenEtiqueta(535) ?></a>
                      </li>
                      <?php
                      }
                      ?>
                      </ul>
                      </li>
                      <?php
                     */
                    ?>
                    <!--                    
                    <li class="pull-right">
                        <a href="javascript:void(0);">
                            <div class="sparkline txt-color-pinkDark text-align-right" data-sparkline-height="18px" data-sparkline-width="90px" data-sparkline-barwidth="7"><canvas height="18" width="52" style="display: inline-block; width: 52px; height: 18px; vertical-align: top;"></canvas>
                            </div> 
                        </a>
                    </li>
                    -->
                </ul>

                <div id="myTabContent1" class="tab-content padding-10 no-border">
                    <div class="tab-pane fade in active" id="student">
                        <?php
                        # Revisa si es un registro nuevo
                        if (empty($clave)) {
                            Forma_CampoTexto(ETQ_USUARIO, True, 'ds_login', $ds_login, 16, 16, $ds_login_err);
                            Forma_CampoTexto(ObtenEtiqueta(123), True, 'ds_password', '', 16, 16, $ds_password_err, True);
                            Forma_CampoTexto(ObtenEtiqueta(124), True, 'ds_password_conf', '', 16, 16, $ds_password_conf_err, True);
                        } else {
//                            Forma_CampoInfo(ETQ_USUARIO, $ds_login . $ds_cambiar_pwd . $ds_login_rpt . $ds_pctia_rpt . $ds_diploma . $super_user);
                            Forma_CampoOculto('ds_login', $ds_login);
                            Forma_CampoOculto('cl_sesion', $cl_sesion);
                        }
                        ?>
                        <!--                        
                        <div class="row">
                            <div class="col-xs-12 col-sm-9 col-sm-offset-3">
                                <ul id="sparks" class="">
                                    <li class="sparks-info">
                                        <h5>
                                            <a rel="tooltip" data-placement="top" data-original-title="<?php echo ObtenEtiqueta(348) ?>" >Contract 1</a>  
                                            <span class="txt-color-blue" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(597) ?>" >May, 13 2016</span>
                                        </h5>
                                        <div class="sparkline txt-color-blue hidden-mobile hidden-md hidden-sm" style="padding:0 0 0 5px">
                                            <i class="fa fa-file-pdf-o fa-2x" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(346) ?>" >&nbsp;</i>
                                        </div>
                                    </li>
                                    <li class="sparks-info">
                                        <h5>
                                            <a class="btn btn-info" rel="tooltip" data-placement="top" data-original-title="<?php echo ObtenEtiqueta(348) ?>" ><i class="fa fa-envelope">&nbsp;</i>Send Contract 2</a>  
                                            <span class="txt-color-blue" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(597) ?>" ></span>
                                        </h5>
                                        <div class="sparkline txt-color-blue hidden-mobile hidden-md hidden-sm" style="padding:0 0 0 5px">
                                            <i class="fa fa-file-pdf-o fa-2x" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(346) ?>" >&nbsp;</i>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12">
                                <ul id="sparks" class="">
                                    <?php
                                    # Contratos
                                    /*if ($cl_type == 4)
                                        $contratos = 3;
                                    else
                                        $contratos = 1;*/                                    
                                    # Obtenemos el numero de contratos por programa
                                    $meses_maximo = ObtenConfiguracion(92); // Agregar en configuracion
                                    $meses_x_contrato = 48; // Agregar en configuracion
                                    
                                    # Obtenemos los numeros de contratos que deben tener
                                    $contratos = ceil($no_semanas/$meses_x_contrato);
                                    # Obtenemos los contratos que son de 12 meses
                                    $no_contratos_floor = floor($no_semanas/$meses_x_contrato); 

                                    $enrol = False;                                    
                                    for ($i = 1; $i <= $contratos; $i++) {                                        
                                        # Si no existe el numero de mes en los contratos actualiza
                                        $row3 = RecuperaValor("SELECT SUM( no_weeks ) FROM k_app_contrato WHERE cl_sesion = '$cl_sesion' AND no_contrato <$i");                            
                                        # Obtenemos el numero de meses que cubre el contrato
                                        if($no_semanas <= $meses_maximo){
                                          $weeks_contrato = round($no_semanas);
                                        }
                                        else{
                                          if($i<=$no_contratos_floor)
                                            $weeks_contrato = $meses_x_contrato;
                                          else
                                            $weeks_contrato = $no_semanas-$row3[0];
                                        }
                                        # Buscamos los contratos si existen si no existe lo insertara con la informacion del contrato numero uno                                       
                                        $rowp = RecuperaValor("SELECT COUNT(*) FROM k_app_contrato WHERE no_contrato=".$i." AND cl_sesion='".$cl_sesion."'");
                                        $contrato_existe = $rowp[0];
                                        if(!empty($contrato_existe)){
                                          # Actualizamos contrato                                        
                                          EjecutaQuery("UPDATE k_app_contrato SET no_weeks=".$weeks_contrato." WHERE cl_sesion='".$cl_sesion."' AND no_contrato='".$i."'");
                                          // echo "UPDATE k_app_contrato SET no_weeks=".$weeks_contrato." WHERE cl_sesion='".$cl_sesion."' AND no_contrato='".$i."'";
                                        }
                                        else{
                                          $Query = "INSERT INTO k_app_contrato 
                                          (cl_sesion,no_contrato,mn_app_fee,mn_tuition,mn_costs,ds_costs,mn_discount,ds_discount,mn_tot_tuition,mn_tot_program
                                          ,mn_a_due,mn_a_paid,mn_b_due,mn_b_paid,mn_c_due,mn_c_paid,mn_d_due,mn_d_paid,fg_opcion_pago
                                          ,ds_p_name,ds_education_number,ds_usual_name,fg_international,cl_preference_1,cl_preference_2,cl_preference_3,ds_m_add_number,ds_m_add_street
                                          ,ds_m_add_city,ds_m_add_state,ds_m_add_zip,ds_m_add_country,ds_a_email,ds_frecuencia,cl_metodo_pago,ds_metodo_otro,no_weeks)
                                          SELECT cl_sesion,".$i.",mn_app_fee,mn_tuition,mn_costs,ds_costs,mn_discount,ds_discount,mn_tot_tuition,mn_tot_program,
                                          mn_a_due,mn_a_paid,mn_b_due,mn_b_paid,mn_c_due,mn_c_paid,mn_d_due, mn_d_paid,fg_opcion_pago,
                                          ds_p_name,ds_education_number,ds_usual_name,fg_international,cl_preference_1,cl_preference_2,cl_preference_3,ds_m_add_number,ds_m_add_street,
                                          ds_m_add_city,ds_m_add_state,ds_m_add_zip,ds_m_add_country,ds_a_email,ds_frecuencia, cl_metodo_pago,ds_metodo_otro,".$weeks_contrato."
                                          FROM k_app_contrato WHERE no_contrato='1' AND cl_sesion='".$cl_sesion."' ";
                                          EjecutaQuery($Query);
                                        }
                                        if (!empty($fl_template)) {
                                            if (empty($ds_cadena[$i]) || (!empty($ds_cadena[$i]) && empty($ds_firma_alumno[$i]))) {
                                              if(empty($ds_cadena[$i])){
                                                $btn_class = "btn-danger";
                                                $txt_color = "text-danger";
                                                $text = ObtenEtiqueta(238);
                                              }
                                              else{
                                                $btn_class = "btn-warning";
                                                $txt_color = "text-warning";
                                                $text =  ObtenEtiqueta(239);
                                              }
//                                                $ds_envia = "&nbsp;&nbsp;&nbsp;<a href='applications_snd.php?c=$fl_sesion&con=$i'>" . ObtenEtiqueta(347) . "</a>";
                                                $ds_envia = '<a  href="applications_snd.php?c=' . $fl_sesion . '&con=' . $i . '" class="btn btn-info '.$btn_class.'" rel="tooltip" data-placement="top" data-original-title="' . ObtenEtiqueta(347) . '" >'
                                                        . '<i class="fa fa-envelope">&nbsp;</i>'.$text.' &nbsp; (' . $i.')'
                                                        . '</a>';
                                                $ds_firma = "";
                                                if ($i == 1)
                                                    $enrol = False;
                                            }
                                            else {
                                                $ds_envia = "";
//                                                $ds_firma = "&nbsp;&nbsp;&nbsp;<a href='view_contract.php?c=$fl_sesion&con=$i' target='_blank'>" . ObtenEtiqueta(348) . "</a>";
                                                $ds_firma = '<a href="view_contract.php?c=' . $fl_sesion . '&con=' . $i . '" target="_blank" rel="tooltip" data-placement="top" data-original-title="' . ObtenEtiqueta(348) . '" ><i class="fa fa-search">&nbsp;</i>' .ObtenEtiqueta(243).'&nbsp;('. $i . ')</a> ';
                                                $txt_color = "text-success";
                                                if ($i == 1)
                                                    $enrol = True;
                                            }
                                            $ds_descarga = '<div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
                                                                <a href="../reports/documents_rpt.php?c=' . $fl_sesion . '&con=' . $i . '"><i class="fa fa-file-pdf-o '.$txt_color.'" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="' . ObtenEtiqueta(346) . '" >&nbsp;</i></a>
                                                             </div>';
                                        }
                                        else {
                                            $ds_descarga = ObtenMensaje(213);
                                            $ds_envia = "";
                                            $ds_firma = "";
                                        }
//                                        echo ObtenEtiqueta(346);
//                                        echo '<br>';
//                                        echo ObtenEtiqueta(347);
//                                        echo '<br>';
//                                        echo ObtenEtiqueta(348);
//                                        echo '<br>';
//                                        echo ObtenMensaje(213);
//                                        Forma_CampoInfo('Contract ' . $i, $ds_descarga . $ds_envia . $ds_firma);                                        
                                        $fe_temp = substr($ds_cadena[$i], 0, 8);
                                        if (!empty($fe_temp))
                                            $fe_envio = date("M j, Y", strtotime("$fe_temp"));
                                        else
                                            $fe_envio = "";
//                                        Forma_CampoInfo(ObtenEtiqueta(597), $fe_envio);
                                        ?>
                                        <li class="sparks-info">
                                            <h5>
                                                <?php echo $ds_envia . $ds_firma ?>
                                                <br/><small class="<?php echo $txt_color; ?>" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(597) ?>" >
                                                    <strong><?php echo ObtenEtiqueta(883).": ".$fe_firma[$i]; ?></strong>
                                                </small>
                                                <br/><small class="<?php echo $txt_color; ?>" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(597) ?>" >
                                                    <strong><?php echo ObtenEtiqueta(884).": ".$fe_envio; ?></strong>
                                                </small>
                                            </h5>
                                            <?php echo $ds_descarga ?>
                                        </li>
                                        <?php
                                    }                                    
                                    ?>
                                      <li>
                                        <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
                                          <a href='../reports/student_archives.php?clave=<?php echo $clave ?>&con=1'><i class="fa fa-file-pdf-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(346); ?>" >&nbsp;</i></a>
                                        </div>
                                        <a href='../reports/student_archives.php?clave=<?php echo $clave ?>&con=1' rel="tooltip" data-placement="top" data-original-title="<?php echo ObtenEtiqueta(2671); ?>">Archived</a>
                                      </li>
                                    <li>
                                      <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
                                        <a href='../reports/pctia_rpt.php?clave=<?php echo $clave ?>'><i class="fa fa-file-pdf-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(346); ?>" >&nbsp;</i></a>
                                      </div>
                                      <a href='../reports/pctia_rpt.php?clave=<?php echo $clave ?>' rel="tooltip" data-placement="top" data-original-title="<?php echo ObtenEtiqueta(534); ?>"><i class="fa fa-search">&nbsp;</i><?php echo ObtenEtiqueta(534) ?></a>
                                      <br>
                                      <a href='../reports/pctia_rpt.php?clave=<?php echo $clave ?>&unofficial=1'><small style="font-size: 12px;">Unofficial Transcript</small></a>
                                    </li>

                                    <?php
                                    if (!empty($fe_completado)) {
                                        ?>
                                        <li>
                                          <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
                                            <a href='../reports/newDiploma_rpt.php?clave=<?php echo $clave ?>'><i class="fa fa-file-pdf-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(346); ?>" >&nbsp;</i></a>
                                          </div>
                                          <a href='../reports/newDiploma_rpt.php?clave=<?php echo $clave ?>' rel="tooltip" data-placement="top" data-original-title="<?php echo ObtenEtiqueta(535); ?>"><i class='fa fa-file-search'>&nbsp;</i><?php echo ObtenEtiqueta(535) ?></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                                <?php
                                if (!empty($confirmacion))
                                    Forma_CampoInfo('', ObtenMensaje(212));
                                if ($error > 0)
                                    Forma_Error(211);
                                ?>
                            </div>
                        </div>
                        <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(693) ?> </strong></legend></h2>
                        <?php
# Busca si existen alumnos con el mismo nombre y apellidos JGFL 26/01/2015
                        $Query = "SELECT a.fl_usuario, CONCAT(a.ds_nombres,' ',a.ds_apaterno), c.nb_programa, nb_periodo FROM c_usuario a, k_ses_app_frm_1 b, c_programa c, c_periodo d ";
                        $Query .= "WHERE a.ds_nombres='" . $ds_fname . "' AND a.ds_apaterno='" . $ds_lname . "' AND ds_login!='" . $ds_login . "'";
                        $Query .= "AND a.cl_sesion=b.cl_sesion AND b.fl_programa = c.fl_programa AND b.fl_periodo=d.fl_periodo ";
                        $rs = EjecutaQuery($Query);
                        $registros = CuentaRegistros($rs);
# Si existe regitros mostrar tanto la seccion como la tabla de los studens homonimos
                        if (!empty($registros)) {
                            //Forma_Seccion(ObtenEtiqueta(693));
                            $titulos = array('' . ObtenEtiqueta(360) . '|center', '' . ETQ_NOMBRE . '|center', '' . ObtenEtiqueta(382) . '|center');
                            $ancho_col = array('20%', '20%', '10%', '3%');
                            Forma_Tabla_Ini('50%', $titulos, $ancho_col);
                            for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
                                echo "
                                <tr>
                                  <td><a href=\"javascript:otro('students_frm.php',$row[0]);\"'>" . $row[2] . "</a></td>
                                  <td><a href=\"javascript:otro('students_frm.php',$row[0]);\"'>" . $row[1] . "</a></td>
                                  <td><a href=\"javascript:otro('students_frm.php',$row[0]);\"'>" . $row[3] . "</a></td>
                                </tr>";
                            }
                            Forma_Tabla_Fin();
                        }
                        ?>
                        <h2 class="no-margin"><legend><strong> Student Data </strong></legend></h2>
                        <!---<hr>-->
                        <?php
                        /*Forma_CampoTexto(ObtenEtiqueta(117), True, 'ds_nombres', $ds_nombres, 100, 32, $ds_nombres_err);
                        Forma_CampoTexto(ObtenEtiqueta(118), True, 'ds_apaterno', $ds_apaterno, 50, 32, $ds_apaterno_err);
                        Forma_CampoTexto(ObtenEtiqueta(119), False, 'ds_amaterno', $ds_amaterno, 50, 32, '');
                        $ruta = PATH_ALU_IMAGES . "/id";
                        Forma_CampoUpload(ObtenEtiqueta(810), '', 'ds_foto_oficial', $ds_foto_oficial, $ruta, True, 'ds_foto_oficial', 60, $ds_foto_oficial_err, 'jpg|jpeg');
                        Forma_Espacio();
                        Forma_CampoInfo(ObtenEtiqueta(631), $ds_p_name);
                        Forma_CampoInfo(ObtenEtiqueta(632), $ds_education_number);
                        Forma_CampoCheckbox(ObtenEtiqueta(620), 'fg_international', $fg_international);
                        Forma_Espacio();
                        $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116)); // Masculino, Femenino
                        $val = array('M', 'F');
                        Forma_CampoSelect(ObtenEtiqueta(114), False, 'fg_genero', $opc, $val, $fg_genero);
                        Forma_CampoTexto(ObtenEtiqueta(120) . ' ' . ETQ_FMT_FECHA, False, 'fe_nacimiento', $fe_nacimiento, 10, 10, $fe_nacimiento_err);
                        Forma_Calendario('fe_nacimiento');
                        Forma_CampoTexto(ObtenEtiqueta(121), True, 'ds_email', $ds_email, 64, 32, $ds_email_err);
                        Forma_CampoTexto(ObtenEtiqueta(127), True, 'ds_a_email', $ds_a_email, 64, 32, $ds_a_email_err);
                        Forma_CampoTexto(ObtenEtiqueta(339), False, 'ds_link_to_portfolio', $ds_link_to_portfolio, 255, 32);
                        Forma_Espacio();
                        Forma_CampoTexto(ObtenEtiqueta(540) . ' ' . ETQ_FMT_FECHA, False, 'fe_carta', $fe_carta, 10, 10, $fe_carta_err);
                        Forma_Calendario('fe_carta');
                        Forma_CampoTexto(ObtenEtiqueta(541) . ' ' . ETQ_FMT_FECHA, False, 'fe_contrato', $fe_contrato, 10, 10, $fe_contrato_err);
                        Forma_Calendario('fe_contrato');*/
                        ?>
                        <!------========= la funcion validarnspace valida los espacio en blanco ========--->
                        
                        <div class="row no-margin">
                            <div class="col-sm-4">
                                    <?php echo Forma_CampoTexto('Username or Student ID',True, 'ds_alias', $ds_alias, 100, 0, $ds_alias_err, False, '', True, "onkeypress='return validarnspace(event)' onkeyup='ValidaAlias(".$clave.")' ", '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>                        
                                    <small class="text-muted text-danger hidden" id="user_exists">*This user already exists</small>                            
                            </div>
                            <script>
                                function ValidaAlias(clave){
                                
                                    var val = document.getElementById("ds_alias").value;
                                    var user = clave;

                                    if(val.length>0){
                                                      $.ajax({
                                                        type: "POST",
                                                        dataType: 'json',
                                                        url: "valida_alias.php",
                                                        async: false,
                                                        data: "ds_alias="+val+
                                                              "&fl_usuario="+user,        
                                                        success: function(result){
                                                          var error = result.resultado.fg_error;
                                                         
                                                          if(error==true){
                                                              $("#user_exists").removeClass('hidden');
                                                          }else{
                                                              $("#user_exists").addClass('hidden');
                                                          }
              
                                                        }
                                                      });
                                              }



                                }
                                ValidaAlias(<?php echo $clave;?>);
                            </script>
                        </div>

                          <div class="row no-margin">
                            <div class="col-sm-4">
                              <?php echo Forma_CampoTexto(ObtenEtiqueta(117), True, 'ds_nombres', $ds_nombres, 100, 0, $ds_nombres_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                              <?php echo Forma_CampoTexto(ObtenEtiqueta(118), True, 'ds_apaterno', $ds_apaterno, 50, 0, $ds_apaterno_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                            <?php echo Forma_CampoTexto(ObtenEtiqueta(119), False, 'ds_amaterno', $ds_amaterno, 50, 0, '', False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>                          
                          </div>
                          <div class="row no-margin">
                            <div class="col-sm-4">
                              <?php echo Forma_CampoTexto(ObtenEtiqueta(121), True, 'ds_email', $ds_email, 64, 0, $ds_email_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                              <?php echo Forma_CampoTexto(ObtenEtiqueta(127), True, 'ds_a_email', $ds_a_email, 64, 0, !empty($ds_a_email_err)?$ds_a_email_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                            <!--339-->
                            <?php
                            
                            echo Btstrp_Forma_CampoTextArea("<strong>Link to art portfolio</strong>", False, 'ds_link_to_portfolio', $ds_link_to_portfolio, 0, 3);
                            
                            //echo Forma_CampoTexto('Link to art portfolio', False, 'ds_link_to_portfolio', $ds_link_to_portfolio, 255, 0, '', False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                          </div>

                           <div class="row no-margin">
                                <div class="col-sm-4" style="padding-left: 28px;">
                                  <?php
                                  $comments = str_replace("\r\n","<br>",$comments);
                                  echo Btstrp_Forma_CampoTextArea("<strong>Comments</strong>", False, 'comments', $comments, 0, 3); ?>
                                </div>
                           </div>

                          <div class="row no-margin padding-bottom-10">
                            <div class="col-sm-4">
                              <?php 
                              echo Forma_CampoTexto(ObtenEtiqueta(120) . ' ' . ETQ_FMT_FECHA, False, 'fe_nacimiento', $fe_nacimiento, 10, 0, $fe_nacimiento_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12');  
                              Forma_Calendario('fe_nacimiento');
                              ?>
                            </div>
                            <div class="col-sm-4">
                              <?php 
                              echo Forma_CampoTexto(ObtenEtiqueta(540) . ' ' . ETQ_FMT_FECHA, False, 'fe_carta', $fe_carta, 10, 0, $fe_carta_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); 
                              Forma_Calendario('fe_carta');
                              ?>
                            </div>
                            <div class="col-sm-4">
                              <?php 
                              echo Forma_CampoTexto(ObtenEtiqueta(541) . ' ' . ETQ_FMT_FECHA, False, 'fe_contrato', $fe_contrato, 10, 0, $fe_contrato_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12');
                              Forma_Calendario('fe_contrato');
                              ?>
                            </div>
                          </div>
                          <div class="row no-margin padding-top-10">
                            <div class="col-sm-4">
                              <?php 
                              $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116),'Non-Binary'); // Masculino, Femenino
                              $val = array('M', 'F','N');
                              echo Forma_CampoSelect(ObtenEtiqueta(114), False, 'fg_genero', $opc, $val, $fg_genero, '', False, '', 'left', 'col col-sm-12', 'col col-sm-12');
                              ?>
                            </div>
                            <div class="col-sm-4">
                              <?php 
                              $ruta = PATH_ALU_IMAGES . "/id";
                              echo Forma_CampoUpload(ObtenEtiqueta(810), '', 'ds_foto_oficial', $ds_foto_oficial, $ruta, True, 'ds_foto_oficial', 60, !empty($ds_foto_oficial_err)?$ds_foto_oficial_err:NULL, 'jpg|jpeg', '', 'left', 'col-sm-12 col-lg-12', 'col-sm-12 col-lg-12');
                              ?>
                            </div>                            
                          </div>
                          <div class="row no-margin">
                            <div class="col-sm-4">
                              <?php echo Forma_CampoTexto(ObtenEtiqueta(631), False, 'ds_p_name', $ds_p_name, 64, 0, '', False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>                            
                            <div class="col-sm-4">
                              <?php echo Forma_CampoTexto(ObtenEtiqueta(887), False, 'ds_usual_name', $ds_usual_name, 200, 0, '', False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>                            
                            <div class="col-sm-4">
                              <?php echo Forma_CampoTexto(ObtenEtiqueta(632), False, 'ds_education_number', $ds_education_number, 20, 0, '', False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>                            
                          </div>
                          <div class="row no-margin">
                            <div class="col-sm-3">
                              <?php
                              Forma_CampoInfo(ObtenEtiqueta(110), $nb_perfil, 'left', 'col-sm-12', 'col-sm-12');                              
                              ?>
                            </div>
                            <div class="col-sm-3">
                              <?php
                              echo Forma_CampoInfo(ObtenEtiqueta(426), $ds_grupo, 'left', 'col-sm-12', 'col-sm-12');
                              Forma_CampoOculto('fl_perfil', $fl_perfil);
                              Forma_CampoOculto('nb_perfil', $nb_perfil);
                              ?>
                            </div>
                            <div class="col-sm-3">
                              <?php
                              Forma_CampoInfo(ObtenEtiqueta(297), $nb_maestro, 'left', 'col-sm-12', 'col-sm-12');
                              ?>
                            </div>                          
                            <div class="col-sm-3">
                              <?php
                              Forma_CampoInfo(ObtenEtiqueta(617), $no_grado_actual, 'left', 'col-sm-12', 'col-sm-12');
                              Forma_CampoOculto('fl_grupo', $fl_grupo);
                              ?>
                            </div>
                          </div>
                          <script>
                          function mostrar_ocultar(tipo){    
                            if(tipo==1){
                              var international = $("input[name='fg_international']:checked").val();
                              if(international == 'on'){
                                $('#international').attr('style', 'display:inline;');
                                $('#international_ppt').attr('style', 'display:inline;');
                                $('#sin').attr('style', 'display:none;');
                                $('#sin_ppt').attr('style', 'display:none;');
                                $('#passport').attr('style', 'display:inline;');
							    $('#passport_date').attr('style', 'display:inline;');
                              }
                              else{
                                $('#international').attr('style', 'display:none;');
                                $('#international_ppt').attr('style', 'display:none;');
                                $('#sin').attr('style', 'display:inline;');
                                $('#sin_ppt').attr('style', 'display:inline;');
                                $('#passport').attr('style', 'display:none;');
                                $('#passport_date').attr('style', 'display:none;');

                              }
                            }
                            if(tipo==2){
                              var international = $("input[name='fg_study_permit']:checked").val();
                              if(international != 'on'){
                                $('#other_permit').attr('style', 'display:inline;');
                              }
                              else{
                                $('#other_permit').attr('style', 'display:none;');   
                              }
                            }
                            if(tipo==3){
                              var aboriginal = $("input[name='fg_aboriginal']:checked").val();
                              if(aboriginal == 'on'){
                                $('#ds_aboriginal_ppt').attr('style', 'display:inline;');
                              }
                              else{
                                $('#ds_aboriginal_ppt').attr('style', 'display:none;');   
                              }
                            }
                            if(tipo==4){
                              var health = $("input[name='fg_health_condition']:checked").val();
                              if(health == 'on'){      
                                $('#fg_health_ppt').attr('style', 'display:inline;');
                                $('#fg_health').attr('style', 'display:inline;');
                              }
                              else{
                                $('#fg_health_ppt').attr('style', 'display:none;');
                                $('#fg_health').attr('style', 'display:none;');
                              }
                            }



                            if(tipo==5){
                                var disabilty = $("input[name='fg_disabilityie']:checked").val();
                                if(disabilty == 'on'){
      
                                    $('#fg_disability_ppt').attr('style', 'display:inline;');
                                    $('#fg_disability').attr('style', 'display:inline;');
                                }else{
     
                                    $('#fg_disability_ppt').attr('style', 'display:none;');
                                    $('#fg_disability').attr('style', 'display:none;');
                                }
                            }

                          }
                          </script>
                          <div class="row no-margin">
                            <div class="col-xs-12 col-sm-5 no-padding">
                              <?php
                                if($fg_international == 1) {
                                    $sin=False;
                                }else{
                                    $sin=True;
                                }
                                Forma_CampoTexto('Social Insurance Number',True,'ds_sin', $ds_sin, 50,0, $ds_sin_err, False, 'sin', $sin, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                                echo Forma_CampoCheckbox('', 'fg_international', $fg_international, str_uso_normal(ObtenEtiqueta(620)), '', True, " onClick='javascript:mostrar_ocultar(1);'", 'left', 'col-sm-1 no-padding', 'col-sm-12 no-padding');
                                ?>
                            </div>
                            <div class="col-xs-12 col-sm-5">
                              <?php
                              if($fg_international == 1) {
                                $international = True;
                              }else{
                                $international = False;
                              }
                              Forma_CampoTexto(ObtenEtiqueta(1024),True,'ds_citizenship',$ds_citizenship, 50,0, $ds_citizenship_err, False, 'international', $international, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                              ?>
                            </div>
                          </div>

                          <div class="row no-margin" >
                              <div class="col-xs-12 col-sm-4 no-padding">
                                  <?php
                                 
                                 echo Forma_CampoTexto('Passport Number', False, 'passport_number', $passport_number, 50, 0, $passport_number_err, False,'passport', $international, '' ,  '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                                  ?>
                              </div>
                              <div class="col-xs-12 col-sm-4">
                                  <?php
                                  echo Forma_CampoTexto('Passport Expiration Date ' . ETQ_FMT_FECHA, False, 'passport_exp_date', $passport_exp_date, 10, 0, $passport_exp_date_err, False, 'passport_date', $international, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12');
                                  Forma_Calendario('passport_exp_date');
                                
                                  ?>
                              </div>

                          </div>

                          <div class="row no-margin">
                            <div class="col-xs-12 col-sm-5 no-padding">
                              <?php
                              Forma_CampoCheckbox('','fg_study_permit',$fg_study_permit, ObtenEtiqueta(1025), '', True, " onClick='javascript:mostrar_ocultar(2);'", 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');                         
                              if($fg_study_permit=='0')
                                $permit = 'inline';
                              else
                                $permit = 'none';
                              ?>
                            </div>                      
                            <div class="col-xs-12 col-sm-5" id="other_permit" style="display:<?php echo $permit; ?>;">
                              <?php                        
                              Forma_CampoCheckbox('','fg_study_permit_other',$fg_study_permit_other, ObtenEtiqueta(1026), '', True, '', 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');
                              ?>
                            </div>
                          </div>
                        <!-- BASIC INFORMATION -->
                        <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(55) ?> </strong></legend></h2>
                        <?php                        
                        /**
                         * EGMC 20160520
                         * Cambio de Forma_Seccion
                         
                        Forma_CampoInfo(ObtenEtiqueta(340), $fe_ultmod);
                        if ($fg_paypal == '1')
                            $ds_fg_paypal = ETQ_SI;
                        else
                            $ds_fg_paypal = ETQ_NO;
                        Forma_CampoInfo(ObtenEtiqueta(343), $ds_fg_paypal);
                        if ($fg_pago == '1')
                            $fg_pago = ETQ_SI;
                        else
                            $fg_pago = ETQ_NO;
                        Forma_Campoinfo(ObtenEtiqueta(341), $fg_pago);
                        Forma_Espacio();
                        # Datos del programa
                        Forma_CampoInfo(ObtenEtiqueta(360), $nb_programa);
                        Forma_CampoOculto('fl_programa', $fl_programa);
                        $Query = "SELECT DISTINCT CONCAT(nb_periodo,' (',c.ds_duracion,')'), a.fl_periodo ";
                        $Query .= "FROM k_term a, c_periodo b, c_programa c ";
                        $Query .= "WHERE a.fl_periodo=b.fl_periodo AND a.fl_programa=c.fl_programa ";
                        $Query .= "AND a.fl_programa=$fl_programa ";
                        $Query .= "ORDER BY fe_inicio";
                        Forma_CampoSelectBD(ObtenEtiqueta(342), True, 'fl_periodo', $Query, $fl_periodo);
                        Forma_Espacio();

                        # Informacion de referencia
                        switch ($fg_ori_via) {
                            case 'A': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(290));
                                break;
                            case 'B': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(291));
                                break;
                            case 'C': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(292));
                                break;
                            case 'D': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(293));
                                break;
                            case '0': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(294) . " - $ds_ori_other");
                                break;
                        }
                        switch ($fg_ori_ref) {
                            case '0': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(17));
                                break;
                            case 'S': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(296) . " - $ds_ori_ref_name");
                                break;
                            case 'T': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(297) . " - $ds_ori_ref_name");
                                break;
                            case 'G': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(298) . " - $ds_ori_ref_name");
                                break;
                        }*/
                        ?>
                        <div class="col col-xs-12 col-sm-10 col-lg-12">
                          <div class="row">
                          <div class="col col-xs-12 col-sm-4">
                            <?php echo Forma_CampoInfo(ObtenEtiqueta(340), $fe_ultmod, 'left', 'col-lg-12', 'col-lg-12'); ?>
                          </div>
                          <div class="col col-xs-12 col-sm-4">
                            <?php 
                            if ($fg_paypal == '1')
                              $ds_fg_paypal = ETQ_SI;
                            else
                              $ds_fg_paypal = ETQ_NO;
                            echo Forma_CampoInfo(ObtenEtiqueta(343), $ds_fg_paypal, 'left', 'col-lg-12', 'col-lg-12'); 
                            ?>
                          </div>
                          <div class="col col-xs-12 col-sm-4">
                            <?php 
                            if ($fg_pago == '1')
                                $fg_pago = ETQ_SI;
                            else
                                $fg_pago = ETQ_NO;
                            echo Forma_CampoInfo(ObtenEtiqueta(341), $fg_pago, 'left', 'col-lg-12', 'col-lg-12'); 
                            ?>
                          </div>
                          </div>
                          <div class="row">
                          <div class="col col-xs-12 col-sm-4">
                            <?php echo Forma_CampoInfo(ObtenEtiqueta(360), $nb_programa, 'left', 'col-lg-12', 'col-lg-12'); ?>
                          </div>
                          <div class="col col-xs-12 col-sm-4">                            
                            <?php 
                            Forma_CampoOculto('fl_programa', $fl_programa);
                            $Query = "SELECT DISTINCT CONCAT(nb_periodo,' (',c.ds_duracion,')'), a.fl_periodo ";
                            $Query .= "FROM k_term a, c_periodo b, c_programa c ";
                            $Query .= "WHERE a.fl_periodo=b.fl_periodo AND a.fl_programa=c.fl_programa ";
                            $Query .= "AND a.fl_programa=$fl_programa ";
                            $Query .= "ORDER BY fe_inicio";
                            Forma_CampoSelectBD(ObtenEtiqueta(342), True, 'fl_periodo', $Query, $fl_periodo,'',False,'','left' ,'col col-sm-12', 'col col-sm-12'); 
                            ?>
                          </div>
                          <div class="col col-xs-12 col-sm-4">
                            <label><strong>&nbsp;</strong></label>
                            <div class="input-group">
                            <?php
                            switch ($fg_ori_via) {
                            case 'A': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(290), 'left', 'col-lg-12', 'col-lg-12');
                                break;
                            case 'B': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(291), 'left', 'col-lg-12', 'col-lg-12');
                                break;
                            case 'C': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(292), 'left', 'col-lg-12', 'col-lg-12');
                                break;
                            case 'D': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(293), 'left', 'col-lg-12', 'col-lg-12');
                                break;
                            case '0': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(294) . " - $ds_ori_other", 'left', 'col-lg-12', 'col-lg-12');
                                break;
                            }                            
                            ?>
                            </div>
                          </div>                          
                          </div>
                          <div class="row">
                            <div class="col col-lg-12 col-sm-4">
                            <?php
                            switch ($fg_ori_ref) {
                              case '0': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(17), 'left', 'col-lg-12', 'col-lg-12');
                                  break;
                              case 'S': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(296) . " - $ds_ori_ref_name", 'left', 'col-lg-12', 'col-lg-12');
                                  break;
                              case 'T': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(297) . " - $ds_ori_ref_name", 'left', 'col-lg-12', 'col-lg-12');
                                  break;
                              case 'G': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(298) . " - $ds_ori_ref_name", 'left', 'col-lg-12', 'col-lg-12');
                                  break;
                            }
                            ?>
                            </div>
                          </div>
						  
						   <div class="row">

                               <div class="col col-xs-12 col-sm-4">
                                <?php
                                
                                Forma_CampoInfo( ObtenEtiqueta(877)  ,$nb_nombre_advisor,'left', 'col-sm-12', 'col-sm-12');
                                
                                ?>
								</div>


                          </div>
						  
						  
						  
						  
                        </div>
                        <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(61) ?> </strong></legend></h2>
                        <?php
                        /**
                         * EGMC 20160520
                         * Cambio de Forma_Seccion
                         */
                        # Datos del aplicante
                        //jgfl 03-11-2014
                        ?>
                        <div class="col-sm-12">
                          <div class="col-sm-6">
                          <?php Forma_CampoTexto(ObtenEtiqueta(280), True, 'ds_number', $ds_number, 20, 16, !empty($ds_number_err)?$ds_number_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-6'); ?>
                          </div>
                          <div class="col-sm-6">
                          <?php Forma_CampoTexto(ObtenEtiqueta(281), True, 'ds_alt_number', $ds_alt_number, 20, 16, !empty($ds_alt_number_err)?$ds_alt_number_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-6'); ?>
                          </div>
                        </div>
                        <div class="row">&nbsp;</div>
                        <div class="row">&nbsp;</div>
                        <!--<h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(62) ?> </strong></legend></h2>-->
                        <?php
                        /**
                         * EGMC 20160520
                         * Cambio de Forma_Seccion
                         
                        # Direccion
                        //jgfl 03-11-2014
                        Forma_CampoTexto(ObtenEtiqueta(282), True, 'ds_add_number', $ds_add_number, 20, 10, $ds_add_number_err);
                        Forma_CampoTexto(ObtenEtiqueta(283), True, 'ds_add_street', $ds_add_street, 20, 50, $ds_add_street_err);
                        Forma_CampoTexto(ObtenEtiqueta(284), True, 'ds_add_city', $ds_add_city, 20, 50, $ds_add_city_err);
                        Forma_CampoTexto(ObtenEtiqueta(286), True, 'ds_add_zip', $ds_add_zip, 20, 10, $ds_add_zip_err);
                        Forma_CampoSelectCombinado("ds_add_state", True, $ds_add_state, 50, 16, $ds_add_country, "ds_add_country", $ds_add_state_err);
                        $Query = "SELECT nb_pais, fl_pais FROM c_pais ";
                        Forma_CampoSelectBD(ObtenEtiqueta(287), True, 'ds_add_country', $Query, $ds_add_country, $ds_add_country_err, True);*/
                        ?>                 
                        <!--<h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(865) ?> </strong></legend></h2>-->
                        <?php
                        # Person Resposible
                        //Forma_Seccion(ObtenEtiqueta(865));
                        //Forma_CampoCheckbox('', 'fg_responsable', $fg_responsable, ObtenEtiqueta(866)." (Off) / (On) ".ObtenEtiqueta(867));
                        ?>
                        <!--<div id="person_responsable" style="display: <?php if(empty($fg_responsable)) echo "none"; else echo "inline"; ?>;">
                        <?php  
                        /*Forma_CampoTexto(ObtenEtiqueta(868), True, 'ds_fname_r', $ds_fname_r, 50, 32, $ds_fname_r_err);
                        Forma_CampoTexto(ObtenEtiqueta(869), True, 'ds_lname_r', $ds_lname_r, 50, 32, $ds_lname_r_err);
                        Forma_CampoTexto(ObtenEtiqueta(870), True, 'ds_email_r', $ds_email_r, 50, 32, $ds_email_r_err);
                        Forma_CampoTexto(ObtenEtiqueta(871), False, 'ds_aemail_r', $ds_aemail_r, 50, 32, $ds_aemail_r_err);
                        Forma_CampoTexto(ObtenEtiqueta(872), True, 'ds_pnumber_r', $ds_pnumber_r, 50, 32, $ds_pnumber_r_err);
                        Forma_CampoTexto(ObtenEtiqueta(873), True, 'ds_relation_r', $ds_relation_r, 50, 32, $ds_relation_r_err);*/
                        ?>
                        </div>-->
                        <!--<div class="row no-margin">
                          <div class="col-xs-12 col-sm-6">-->
                            <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(62) ?> </strong></legend></h2>
                            <div class="col-sm-12">
                              <div class="col-sm-3">
                              <?php Forma_CampoTexto(ObtenEtiqueta(282), True, 'ds_add_number', $ds_add_number, 20, 0, !empty($ds_add_number_err)?$ds_add_number_err:NULL, False, '', True, '', '','smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-5'); ?>
                              </div>
                              <div class="col-sm-4">
                              <?php Forma_CampoTexto(ObtenEtiqueta(283), True, 'ds_add_street', $ds_add_street, 20, 0, !empty($ds_add_street_err)?$ds_add_street_err:NULL, False, '', True, '', '','smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                              </div>
                              <div class="col-sm-4">
                              <?php Forma_CampoTexto(ObtenEtiqueta(284), True, 'ds_add_city', $ds_add_city, 20, 0, !empty($ds_add_city_err)?$ds_add_city_err:NULL, False, '', True, '', '','smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                              </div>
                              <div class="col-sm-3">
                              <?php Forma_CampoTexto(ObtenEtiqueta(286), True, 'ds_add_zip', $ds_add_zip, 20, 0, !empty($ds_add_zip_err)?$ds_add_zip_err:NULL, False, '', True, '', '','smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-6'); ?>
                              </div>
                              <div class="col-sm-4">
                              <?php Forma_CampoSelectCombinado("ds_add_state", True, $ds_add_state, 50, 0, $ds_add_country, "ds_add_country", !empty($ds_add_state_err)?$ds_add_state_err:NULL, 'smart-form form-group\' style="padding-top:7px;"', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                              </div>
                              <div class="col-sm-4">
                              <?php 
                              $Query = "SELECT ds_pais, fl_pais FROM c_pais ";
                              Forma_CampoSelectBD(ObtenEtiqueta(287), True, 'ds_add_country', $Query, $ds_add_country, !empty($ds_add_country_err)?$ds_add_country_err:NULL, True,'', 'left', 'col col-sm-12', 'col col-sm-12');
                              ?>
                              </div>                              
                            </div>
                          <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(633) ?> </strong></legend></h2>
                        <?php
                        /**
                         * EGMC 20160520
                         * Cambio de Forma_Seccion
                        
                        Forma_CampoTexto(ObtenEtiqueta(282), False, 'ds_m_add_number', $ds_m_add_number, 20, 10);
                        Forma_CampoTexto(ObtenEtiqueta(283), False, 'ds_m_add_street', $ds_m_add_street, 50, 50);
                        Forma_CampoTexto(ObtenEtiqueta(284), False, 'ds_m_add_city', $ds_m_add_city, 50, 50);
                        Forma_CampoTexto(ObtenEtiqueta(285), False, 'ds_m_add_state', $ds_m_add_state, 50, 50);
                        Forma_CampoTexto(ObtenEtiqueta(286), False, 'ds_m_add_zip', $ds_m_add_zip, 20, 10);
                        $Query = "SELECT nb_pais, fl_pais FROM c_pais ";
                        Forma_CampoSelectBD(ObtenEtiqueta(287), False, 'ds_m_add_country', $Query, $ds_m_add_country, '', True);*/
                        ?>
                        <!--<div class="col col-xs-12 col-md-12">
                          <div class="row">-->
                          <div class="col-sm-12">
                            <div class="col-sm-3">
                            <?php echo Forma_CampoTexto(ObtenEtiqueta(282), False, 'ds_m_add_number', $ds_m_add_number, 20, 0,'',False,'',True,'','', 'smart-form form-group', 'left','col col-sm-12', 'col col-sm-5'); ?>
                            </div>
                            <div class="col-sm-4">
                            <?php echo Forma_CampoTexto(ObtenEtiqueta(283), False, 'ds_m_add_street', $ds_m_add_street, 50, 0,'',False,'',True,'','',  'smart-form form-group','left','col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                            <?php echo Forma_CampoTexto(ObtenEtiqueta(284), False, 'ds_m_add_city', $ds_m_add_city, 50, 0,'',False,'',True,'','',  'smart-form form-group','left','col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                          <!--</div>
                          <div class="row">-->
                            <div class="col-sm-3">
                            <?php echo Forma_CampoTexto(ObtenEtiqueta(285), False, 'ds_m_add_state', $ds_m_add_state, 50, 0,'',False,'',True,'','',  'smart-form form-group','left','col col-sm-12', 'col col-sm-6'); ?>
                            </div>
                            <div class="col-sm-4">
                            <?php echo Forma_CampoTexto(ObtenEtiqueta(286), False, 'ds_m_add_zip', $ds_m_add_zip, 20, 0,'',False,'',True,'','',  'smart-form form-group','left','col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                            <?php
                            $Query = "SELECT ds_pais, fl_pais FROM c_pais ";
                            Forma_CampoSelectBD(ObtenEtiqueta(287), False, 'ds_m_add_country', $Query, $ds_m_add_country, '', True,'','left','col col-sm-12','col col-sm-12');
                            ?>
                            </div>
                          </div>
                          <!--</div>
                        </div>-->
                        <div class="col col-xs-12 col-sm-10 col-lg-12">
                          <?php
                          # Voluntary Disclosure
                          Forma_Seccion(ObtenEtiqueta(1034));
                          ?>
                          <div class="row">
                            <div class="col col-xs-12 col-sm-5">
                            <?php
                            Forma_CampoCheckbox('','fg_aboriginal',$fg_aboriginal, ObtenEtiqueta(1027), '', True, " onClick='javascript:mostrar_ocultar(3);'", 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');
                            if($fg_aboriginal ==1)
                              $aboriginal = "inline";
                            else
                              $aboriginal = "none";
                            ?>
                            </div>
                            <div class="col col-xs-12 col-sm-5" id="ds_aboriginal_ppt" style="display:<?php echo $aboriginal; ?>;">
                              <div class="smart-form">
                                <section>
                                <div class="row">
                                  <label class="label"><strong><?php echo ObtenEtiqueta(1028); ?></strong></label>
                                  <?php 
                                  echo "<div class='col col-4'><label class='radio' style='padding-top:0px;'>";
                                  CampoRadio('ds_aboriginal', ObtenEtiqueta(1029), $ds_aboriginal, ObtenEtiqueta(1029));
                                  echo "</label></div>
                                  <div class='col col-4'><label class='radio' style='padding-top:0px;'>";
                                  CampoRadio('ds_aboriginal', ObtenEtiqueta(1030), $ds_aboriginal, ObtenEtiqueta(1030));
                                  echo "</label></div>
                                  <div class='col col-4'><label class='radio' style='padding-top:0px;'>";
                                  CampoRadio('ds_aboriginal', ObtenEtiqueta(1031), $ds_aboriginal, ObtenEtiqueta(1031));
                                  echo "</label></div>";
                                  ?> 
                                </div>
                                </section>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col col-xs-12 col-sm-5">
                            <?php
                            Forma_CampoCheckbox('','fg_health_condition',$fg_health_condition, ObtenEtiqueta(1032), '', True, " onClick='javascript:mostrar_ocultar(4);'", 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');
                            ?>
                            </div>
                            <div class="col-xs-12 col-sm-5">
                            <?php
                            if($fg_health_condition ==1)
                              $health = True;
                            else
                              $health = False;                        
                            Forma_CampoTexto(ObtenEtiqueta(1033),True,'ds_health_condition',$ds_health_condition, 50,0, !empty($ds_health_condition_err)?$ds_health_condition_err:NULL, False, 'fg_health', $health, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                            ?>
                            </div>
                          </div>


                          <div class="row">
                            <div class="col col-xs-12 col-sm-5">
                            <?php
                            Forma_CampoCheckbox('','fg_disabilityie',$fg_disabilityie, ObtenEtiqueta(1778), '', True, " onClick='javascript:mostrar_ocultar(5);'", 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');
                            ?>
                            </div>
                            <div class="col-xs-12 col-sm-5">
                            <?php
                            if($fg_disabilityie ==1)
                                $disability = True;
                            else
                                $disability  = False;                        
                            Forma_CampoTexto(ObtenEtiqueta(1779),True,'ds_disability',$ds_disability, 50,0, !empty($ds_disability_err)?$ds_disability_err:NULL, False, 'fg_disability', $disability, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                            ?>
                            </div>
                          </div>


                        </div>
                          <!--</div>
                          <div class="col-xs-12 col-sm-6">-->
                          <div class="row">&nbsp;</div>
                          <div class="row">&nbsp;</div>
                          <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(865) ?> </strong></legend></h2>
                          <?php 
                          // echo Forma_CampoCheckbox('', 'fg_responsable', $fg_responsable, ObtenEtiqueta(866)." (Off) / (On) ".ObtenEtiqueta(867),'',True,'', 'left', 'col-sm-12', 'col-sm-12'); 
                          if(ExisteEnTabla('k_presponsable', 'cl_sesion', $cl_sesion)){
                            if(!empty($fg_email)){
                              $info = ObtenEtiqueta(831);
                            }
                            else{
                              $info = ObtenEtiqueta(832);
                            }
                          }
                          else{
                            $info = ObtenEtiqueta(833);
                          }
                          Forma_CampoCheckbox('', 'fg_responsable', $fg_responsable, ObtenEtiqueta(866)." (Off) / (On) ".ObtenEtiqueta(867)." <small class='text-danger'>(".$info.")</small>", '', True, '', 'left', 'col-sm-12', 'col-sm-12');                        
                          Forma_CampoOculto('fg_email', $fg_email);
                          ?>
                          <div id="person_responsable" style="display: <?php if(empty($fg_responsable)) echo "none"; else echo "inline"; ?>;" class="row co-sm-12">
                            <div class="col-sm-4">
                            <?php Forma_CampoTexto(ObtenEtiqueta(868), True, 'ds_fname_r', $ds_fname_r, 50, 32, !empty($ds_fname_r_err)?$ds_fname_r_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                            <?php Forma_CampoTexto(ObtenEtiqueta(869), True, 'ds_lname_r', $ds_lname_r, 50, 32, !empty($ds_lname_r_err)?$ds_lname_r_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                            <?php Forma_CampoTexto(ObtenEtiqueta(870), True, 'ds_email_r', $ds_email_r, 50, 32, !empty($ds_email_r_err)?$ds_email_r_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                            <?php Forma_CampoTexto(ObtenEtiqueta(871), False, 'ds_aemail_r', $ds_aemail_r, 50, 32, !empty($ds_aemail_r_err)?$ds_aemail_r_err:NULL, False, '', True, '', '',  'smart-form form-group','left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                            <?php Forma_CampoTexto(ObtenEtiqueta(872), True, 'ds_pnumber_r', $ds_pnumber_r, 50, 32, !empty($ds_pnumber_r_err)?$ds_pnumber_r_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                            <div class="col-sm-4">
                            <?php Forma_CampoTexto(ObtenEtiqueta(873), True, 'ds_relation_r', $ds_relation_r, 50, 32, !empty($ds_relation_r_err)?$ds_relation_r_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                            </div>
                          </div>  
                          <!--</div>
                        </div>-->
                        
                        <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(63) ?> </strong></legend></h2>
                        <?php
                        /**
                         * EGMC 20160520
                         * Cambio de Forma_Seccion
                         
                        Forma_CampoInfo(ObtenEtiqueta(117), $ds_eme_fname);
                        Forma_CampoInfo(ObtenEtiqueta(118), $ds_eme_lname);
                        Forma_CampoInfo(ObtenEtiqueta(280), $ds_eme_number);
                        Forma_CampoInfo(ObtenEtiqueta(288), $ds_eme_relation);
                        Forma_CampoInfo(ObtenEtiqueta(287), $ds_eme_country);*/
                        ?>
                        <div class="col col-xs-12 col-md-12">
                          <div class="row">
                            <div class="col-xs-12 col-sm-4">
                            <?php echo Forma_CampoInfo(ObtenEtiqueta(117), $ds_eme_fname, 'left', 'col-lg-12', 'col-lg-12'); ?>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                            <?php echo Forma_CampoInfo(ObtenEtiqueta(118), $ds_eme_lname, 'left', 'col-lg-12', 'col-lg-12'); ?>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                            <?php echo Forma_CampoInfo(ObtenEtiqueta(280), $ds_eme_number, 'left', 'col-lg-12', 'col-lg-12'); ?>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-xs-12 col-sm-4">
                            <?php echo Forma_CampoInfo(ObtenEtiqueta(288), $ds_eme_relation, 'left', 'col-lg-12', 'col-lg-12'); ?>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                            <?php echo Forma_CampoInfo(ObtenEtiqueta(287), $ds_eme_country, 'left', 'col-lg-12', 'col-lg-12'); ?>
                            </div>                            
                          </div>
                        </div>
                        <!-- Personal settings -->
                        <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(410) ?> </strong></legend></h2>
                        <div class="row">
                            <?php
                            /**
                             * EGMC 20160520
                             * Cambio de Forma_Seccion
                             
                            # Configuracion personal
                            // Forma_Seccion(ObtenEtiqueta(410));
                            Forma_CampoOculto('fl_zona_horaria', $fl_zona_horaria);
                            Forma_CampoInfo(ObtenEtiqueta(411), $ds_zona_horaria);
                            Forma_CampoPreview(ObtenEtiqueta(412), 'ds_ruta_avatar', $ds_ruta_avatar, PATH_ALU_IMAGES . "/avatars", False, False);
                            Forma_CampoPreview(ObtenEtiqueta(413), 'ds_ruta_foto', $ds_ruta_foto, PATH_ALU_IMAGES . "/pictures", False, False);
                            Forma_CampoInfo(ObtenEtiqueta(414), $ds_website);
                            Forma_CampoInfo(ObtenEtiqueta(415), $ds_gustos);
                            Forma_CampoInfo(ObtenEtiqueta(416), $ds_pasatiempos);*/
                            ?>
                        </div>
                        <div class="col col-xs-12 col-md-12">
                          <div class="row">
                            <div class="col-xs-12 col-sm-4">
                            <?php 
                            Forma_CampoOculto('fl_zona_horaria', $fl_zona_horaria);
                            echo Forma_CampoInfo(ObtenEtiqueta(411), $ds_zona_horaria, 'left', 'col-lg-12', 'col-lg-12'); 
                            ?>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                            <?php echo Forma_CampoPreview(ObtenEtiqueta(412), 'ds_ruta_avatar', $ds_ruta_avatar, PATH_ALU_IMAGES . "/avatars", False, False, 'left', 'col-lg-12', 'col-lg-12'); ?>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                            <?php echo Forma_CampoPreview(ObtenEtiqueta(413), 'ds_ruta_foto', $ds_ruta_foto, PATH_ALU_IMAGES . "/pictures", False, False, 'left', 'col-lg-12', 'col-lg-12'); ?>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-xs-12 col-sm-4">
                            <?php echo Forma_CampoInfo(ObtenEtiqueta(414), $ds_website, 'left', 'col-lg-12', 'col-lg-12'); ?>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                            <?php echo Forma_CampoInfo(ObtenEtiqueta(415), $ds_gustos, 'left', 'col-lg-12', 'col-lg-12'); ?>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                            <?php echo Forma_CampoInfo(ObtenEtiqueta(416), $ds_pasatiempos, 'left', 'col-lg-12', 'col-lg-12'); ?>
                            </div>
                          </div>
                        </div>
                        
                    </div>
                    <div class="tab-pane fade" id="communicationsHistory">
                        <?php
                        # Obtener los correos de vanas y alumno
                        $Query = "SELECT ds_email FROM c_usuario a, c_sesion b WHERE a.cl_sesion=b.cl_sesion AND b.fl_sesion=$fl_sesion ";
                        $row = RecuperaValor($Query);
                        $ds_emailto = $row[0];
                        $ds_subject = ObtenEtiqueta(336);

                        # Variable para el obtener el dialogo de envio de mensaje   
                        //Forma_Seccion('Communications History');
                        // Forma_CampoInfo('', "<a href='javascript:showDialog();'>Send Letter</a>");
                        Forma_Espacio();
# dialogo para el envio del template
                        echo
                        "<input type='hidden' id='fl_sesion' name='fl_sesion' value='$fl_sesion'>
  <input type='hidden' id='programa' name='programa' value='" . $programa . "'>
  <input type='hidden' id='fl_alumno' name='fl_alumno' value='" . $clave . "'>";

#Tabla de los templates enviados al alumno
                        if (ExisteEnTabla('k_alumno_template', 'fl_alumno', $fl_sesion)) {
                            $titulos = array('Subject|center', 'Date|center', '', '');
                            $ancho_col = array('15%', '12%', '3%', '3%');
                            Forma_Tabla_Ini('100%', $titulos, array('', '', '', ''));
                            $Query = "SELECT nb_template, fe_envio, a.fl_template, fl_alumno_template FROM k_template_doc a, k_alumno_template b ";
                            $Query .= "WHERE a.fl_template=b.fl_template AND fl_alumno=$fl_sesion ORDER BY fe_envio DESC";
                            $rs = EjecutaQuery($Query);
                            for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
                                if ($i % 2 == 0)
                                    $clase = "css_tabla_detalle";
                                else
                                    $clase = "css_tabla_detalle_bg";
                                echo "
                            <tr class='$clase'>
                              <td align='center'>" . $row[0] . "</td>
                              <td align='center'>" . $row[1] . "</td>       
                              <td><a href='viewemail.php?fl_alumno_template=" . $row[3] . "&fl_sesion=" . $fl_sesion . "' title='" . ObtenEtiqueta(487) . "'><i class='fa fa-file-pdf-o fa-2x'></i></a></td>
                              <td><a href=\"javascript:borrar_template('template_delete.php',$row[3],$fl_sesion,'$programa');\" title='" . ObtenEtiqueta(487) . "'><i class='fa fa-trash-o fa-2x'></i></a></td>                              
                            </tr>";
                            }
                            Forma_Tabla_Fin();
                            Forma_Espacio();
                        }
                        ?>                        
                    </div>
                    <div class="tab-pane fade" id="payments">
                        <?php
                        # Recupera el tipo de pago para el curso
                        $Query = "SELECT fg_opcion_pago, no_weeks, mn_payment_due, ds_frecuencia,DATE_FORMAT(fe_firma, '%b %d, %Y') fe_firma,mn_costs,ds_costs,mn_discount,ds_discount,tax_mn_cost ";
                        $Query .= "FROM k_app_contrato ";
                        $Query .= "WHERE cl_sesion='$cl_sesion' AND no_contrato='1'";
                        $row = RecuperaValor($Query);
                        $fg_opcion_pago = $row[0];
                        $no_weeks = $row[1];
                        $mn_payment_due = $row[2];
                        $frecuencia = $row[3];
                        $fe_firma = $row['fe_firma'];
                        $mn_costs=!empty($row['mn_costs'])?$row['mn_costs']:0;
                        $ds_costs=$row['ds_costs'];
                        $mn_discount = !empty($row['mn_discount'])?$row['mn_discount']:0;
                        $ds_discount = $row['ds_discount'];
                        $tax_mn_cost = !empty($row['tax_mn_cost']) ? $row['tax_mn_cost'] : 0;

                        $titulos = array(ObtenEtiqueta(375) . '|center', ObtenEtiqueta(481) . '|center', ObtenEtiqueta(485) . '|center', ObtenEtiqueta(486) . '|center',
                            ObtenEtiqueta(374) . '|center', ObtenEtiqueta(596) . '|center', ObtenEtiqueta(741), ObtenEtiqueta(742), ObtenEtiqueta(743),
                            ObtenEtiqueta(483) . '|center', ObtenEtiqueta(72), '&nbsp;','&nbsp;', '&nbsp;');
                        $ancho_col = array('10%', '', '', '', '', '', '', '', '', '');

                        switch ($fg_opcion_pago) {
                            case 1: $mn_due = 'ds_a_freq';
                                $ds_pagos = 'no_a_payments';
                                break;
                            case 2: $mn_due = 'ds_b_freq';
                                $ds_pagos = 'no_b_payments';

                                if ($tax_mn_cost > 0) {
                                    $tax_mn_cost = $tax_mn_cost / 2;
                                }

                                break;
                            case 3: $mn_due = 'ds_c_freq';
                                $ds_pagos = 'no_c_payments';

                                if ($tax_mn_cost > 0) {
                                    $tax_mn_cost = $tax_mn_cost / 4;
                                }
                                break;
                            case 4: $mn_due = 'ds_d_freq';
                                $ds_pagos = 'no_d_payments';
                                break;
                        }
                        $Query = "SELECT $mn_due, $ds_pagos FROM k_programa_costos WHERE fl_programa=$fl_programa ";
                        $row = RecuperaValor($Query);
                        
                        $no_pagos_opcion = $row[1];
                        ?>
                        <!--<legend> <?php echo ObtenEtiqueta(690) ?> </st></legend>-->
                        <div class="row">
                          <div class=" col col-sm-12 col-lg-6">
                            <h2 class="no-margin">
                            <strong style="color:#0092cd;"><?php echo ObtenEtiqueta(690); ?></strong><br/>
                            <small><i class="fa fa-money"></i>&nbsp;<?php echo ObtenEtiqueta(593).":&nbsp;".$frecuencia; ?></small>
                            </h2>
						   </div>
                           <div class=" col col-sm-12 col-lg-6 text-right"> 
						      <h2 class="no-margin " style="font-size:18px;color: #000;" id=""><?php echo ObtenEtiqueta(2194); ?> $<span id="payment_moment"></span> </h2>
                              <h2 class="no-margin " style="font-size:18px;color: #000;" id=""><?php echo ObtenEtiqueta(2195); ?> $<span id="payment_pending"></span> </h2>                               
                          
						   
                           </div>
                        </div>
                        <?php
                        /**
                         * EGMC 20160520
                         * Cambio de Forma_Seccion
                         */
                        # Datos de los pagos del alumno
//                        Forma_Seccion(ObtenEtiqueta(690));
                        //Forma_Espacio();
                        //Forma_CampoInfo(ObtenEtiqueta(482), $frecuencia);
                        $Querypago = "SELECT MAX(fl_alumno_pago) FROM k_alumno_pago WHERE fl_alumno=$clave";
                        $rowp = RecuperaValor($Querypago);
                        $pago_final = $rowp[0];
                        Forma_Espacio();
                        Forma_Tabla_Ini('100%', $titulos, array(), 'tbl_payments');
                        switch ($fg_opcion_pago) {
                            case 1: $mn_due = 'mn_a_due';
                                break;
                            case 2: $mn_due = 'mn_b_due';
                                break;
                            case 3: $mn_due = 'mn_c_due';
                                break;
                            case 4: $mn_due = 'mn_d_due';
                                break;
                        }

                        //para obtener informacion del pago de app_fee
                        $Query = "SELECT fl_sesion,  CASE cl_metodo_pago WHEN 1 THEN 'Paypal' WHEN 2 THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' ";
                        $Query .= "WHEN 6 THEN 'Cash' END cl_metodo_pago, (CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago, mn_pagado, ";
                        $Query .= "" . ConsultaFechaBD('b.fe_ultmod', FMT_FECHA) . ", ds_comentario ds_comentario_app ";
                        $Query .= "FROM c_sesion a, k_ses_app_frm_1 b ";
                        $Query .= "WHERE a.cl_sesion=b.cl_sesion AND a.cl_sesion='$cl_sesion'";
                        $row = RecuperaValor($Query);
                        $cl_metodo_app = $row[1];
                        $fe_pago_app = $row[2];
                        $mn_pagado_app = $row[3];
                        $fe_ultmod1 = str_texto($row[4]);
                        $ds_comentario_app = str_texto($row[5]);
                        if(empty($ds_comentario_app))
                          $ds_comentario_app = "<div style='width: 100%;height: 35px;'>&nbsp;</div>";
                        # Podemos modificar la fecha de pago del app fee
                        $fe_pago_app = "<a href='javascript:dialogo_refund($clave,$fl_sesion,$fg_inscrito,0,\"FAPP\");' title='Change payment date'>$fe_pago_app</a>";
                        # Podemos modificar el metodo de pago
                        $cl_metodo_app = "<a href='javascript:dialogo_refund($clave,$fl_sesion,$fg_inscrito,0,\"MAPP\");' title='Change payment method'>$cl_metodo_app</a>";
                        # Podemos modificar el comentario
                        $ds_comentario_app = "<a href='javascript:dialogo_refund($clave,$fl_sesion,$fg_inscrito,0,\"CAPP\");' title='Change payment method'>$ds_comentario_app</a>";

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
                          <!--<td><a href='" . PATH_CAMPUS . "/students/invoice.php?fl_sesion=$fl_sesion&destino=payments_frm.php' target='_blank'><img src='" . PATH_IMAGES . "/icon_pdf.gif' width=12 height=12 border=0 title='" . ObtenEtiqueta(487) . "'></a></td>-->
                          <td><a href='" . PATH_CAMPUS . "/students/invoice.php?fl_sesion=$fl_sesion&destino=payments_frm.php' target='_blank'><i class='fa fa-file-pdf-o fa-2x'></i></a></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>";
                        echo "
  </tr>";


                       
    #Additional Cost.
    #recuperamos los aditional cost.
    if($mn_costs>0){

        
        echo "
                        <tr style='font-weight:bold;' align='center'>
                          <td>Additional costs</td>
                          <td></td>
                          <td>$fe_firma</td>
                          <td>$mn_costs</td>
                          <td>$fe_firma</td>
                          <td>$mn_costs</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td></td>
                          <td align='left'><small style='font-size: 11px;'>Additional costs: $ds_costs</small></td>
                          
                          <td><a href='" . PATH_CAMPUS . "/students/invoice_additional.php?fl_sesion=$fl_sesion&destino=payments_frm.php' target='_blank'><i class='fa fa-file-pdf-o fa-2x'></i></a></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>";
        echo "          </tr>";

    }
                       




# Recupera informacion de los pagos realizados
                        $fe_actual = Date('Y-m-d'); //fecha actual con formato Y-m-d
                        $concat = array(ConsultaFechaBD('a.fe_pago', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_pago', FMT_HORA)); // formato de la fecha en que pago
                        $Query_pagado = "SELECT  a.fl_term_pago, b.no_opcion, b.no_pago, " . ConsultaFechaBD('b.fe_pago', FMT_FECHA) . ",(SELECT $mn_due FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1'), ";
                        $Query_pagado .= "CASE a.cl_metodo_pago ";
                        $Query_pagado .= "WHEN 1 THEN '" . ObtenEtiqueta(488) . "' WHEN 2 THEN '" . ObtenEtiqueta(488) . " Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' ";
                        $Query_pagado .= "END ds_metodo_pago, ";
                        $Query_pagado .= " " . ConcatenaBD($concat) . ", a.mn_pagado, a.ds_comentario, a.fl_alumno_pago, a.cl_metodo_pago, a.fg_refund,DATEDIFF(a.fe_pago, '$fe_actual') no_dias, ";
                        $Query_pagado .= "(SELECT SUM(o.mn_pagado) FROM k_alumno_pago_det o WHERE o.fl_alumno_pago=a.fl_alumno_pago AND o.fg_earned='1') earned, ";
                        $Query_pagado .= "(SELECT SUM(o.mn_pagado) FROM k_alumno_pago_det o WHERE o.fl_alumno_pago=a.fl_alumno_pago AND o.fg_earned='0') unearned, ";
                        $Query_pagado .= "CONCAT((SELECT COUNT(*) FROM k_alumno_pago_det o WHERE o.fl_alumno_pago=a.fl_alumno_pago AND o.fg_earned='1'),'/',
  (SELECT COUNT(*) FROM k_alumno_pago_det o WHERE o.fl_alumno_pago=a.fl_alumno_pago)) e_u,
  (SELECT nb_periodo FROM c_periodo r, k_term t WHERE r.fl_periodo=t.fl_periodo AND t.fl_term=b.fl_term) terms ";
                        $Query_pagado .= "FROM k_alumno_pago a, k_term_pago b ";
                        $Query_pagado .= "WHERE a.fl_term_pago = b.fl_term_pago AND a.fl_alumno=$clave ORDER BY b.fe_pago ";
                        $rs = EjecutaQuery($Query_pagado);
						$mn_pago_p=0;
						$contador_refunds=0;
                        $pagos_realizados=0;
                        for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
                            $fl_term_pago_p = $row[0];
                            $no_opcion_p = $row[1];
                            $no_pago_p = $row[2];
                            $fe_limite_pago_p = $row[3];
                            $mn_pago_p = $row[4];
                            $ds_metodo_pago_p = $row[5];
                            $fe_pago_p = $row[6];
                            $mn_pagado_p = $row[7];
                            $ds_comentario_det_p = $row[8];
                            if(empty($ds_comentario_det_p))
                              $ds_comentario_det_p = "<div style='width: 100%;height: 35px;'>&nbsp;</div>"; 
                            $fl_alumno_pago_p = $row[9];
                            $cl_metodo_pago_det_p = $row[10];
                            $fg_refund_p = $row[11];
                            $no_dias = $row[12];
                            $earned = Number_format(round($row[13]), 2, '.', ',');
                            $unearned = Number_format(round($row[14]), 2, '.', ',');
                            $e_u = $row[15];
                            $terms = $row[16];

                            # Validamos si el fg_refund ya se realizo no se podra volver a realizar y estara en colo rojo
                            if (empty($fg_refund_p))
                                $onclick = "<span class='label label-success txt-color-white'><a href='javascript:realizar_refund($clave,$pago_final,$fg_inscrito,$no_pago_p);' style='color:white;'>Refund</a></span>";
                            #fg_refund pagos que se regresan
                            if ($cl_metodo_pago_det_p > 0 AND empty($fg_refund_p) AND $pago_final == $fl_alumno_pago_p)
                                $refund = "<td>$onclick </td>";
                            else {
                                $refund = "<td> </td>";
                                if (!empty($fg_refund_p))
                                    $refund = "<td style='color:red; font-weight:bold;'><span class='label label-danger'><i class='fa fa-thumbs-o-down'></i> Refund</span></td>";
                            }

                            #ultimo pago
                            if ($pago_final == $fl_alumno_pago_p AND $cl_metodo_pago_det_p > 0)
                                $borrar = "<td>
                              <a href=javascript:pago('borrar_payment.php',$clave,$pago_final,0); title='Delete last payment'>
                                <!--<img src=" . PATH_HOME . "/images/icon_delete.gif >-->
                                <i class='fa fa-trash-o fa-2x'></i>
                              </a>
                              </td>";
                            else
                                $borrar = "<td></td>";

                            if ($i % 2 == 0)
                                $clase = "css_tabla_detalle";
                            else
                                $clase = "css_tabla_detalle_bg";
                            $numero_pago = $i + 1;
                            
                            # podremos cambiar el motodo de pago
                            $ds_metodo_pago_p = "<a href='javascript:dialogo_refund($clave,$fl_alumno_pago_p,$fg_inscrito,$numero_pago,\"M\");' title='Change payment method'>$ds_metodo_pago_p</a>";
                            # Podremos Cambiar la fecha de pago
                            $fe_pago_p = "<a href='javascript:dialogo_refund($clave,$fl_alumno_pago_p,$fg_inscrito,$numero_pago,\"F\");' title='Change payment date'>$fe_pago_p</a>";
                            # Podemos modificar el comentario
                            $ds_comentario_det_p = "<a href='javascript:dialogo_refund($clave,$fl_alumno_pago_p,$fg_inscrito,$numero_pago,\"C\");' title='Change payment method'>$ds_comentario_det_p</a>";
                            

                            /*************
                             * Caso particular ariel rabesca
                             * 
                             * 
                             **/

                            if($clave==2829){
                                
                                if(($numero_pago>=1)&&($numero_pago<=3)){
                                    $terms="Apr 8";
                                }
                                if(($numero_pago>=4)&&($numero_pago<=6)){
                                    $terms="Jul 8";
                                }


                            }


							/***Otro caso particular para QiJunMa
							*/
							if($clave==1535){		
								$mn_pago_p=14900.00;
								if($numero_pago==2){
									$mn_pago_p=3874.00;
								}
								
							}
							/**caso Dylan laword*/
                            if(($clave==3833)&&($fl_term_pago_p==5726)){
                                $mn_pago_p="4928.00";
                            }
                            /*caso Ekam*/
                            if(($clave==3550)&&($fl_term_pago_p==6005)){
                                $mn_pago_p="3093.75";
                            }

                            /**
                             * MJD 2023 JUL,24
                             *
                             */
                            /*if ($fg_opcion_pago == 1) {
                                if ($mn_costs > 0) {
                                    $mn_pago_p = $mn_pago_p - $mn_costs;
                                    $mn_pagado_p = $mn_pagado_p - $mn_costs;
                                }
                            }*/

                            $mn_pago_p = $mn_pago_p + $tax_mn_cost;

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
                              <td>
                                <a href='" . PATH_CAMPUS . "/students/invoice.php?f=$fl_term_pago_p&pago=$no_pago_p&destino=payments_frm.php&fl_sesion=$fl_sesion&n_pago=$numero_pago'>
                                  <!--<img src='" . PATH_IMAGES . "/icon_pdf.gif' width=12 height=12 border=0 title='" . ObtenEtiqueta(487) . "'>-->
                                  <i class='fa fa-file-pdf-o fa-2x'></i>
                                </a>
                              </td>";
                            echo " 
                                  " . $borrar . "
                                  " . $refund . "
                                </tr>";
                            $pagos_realizados++;
							
							if($fg_refund_p==0)
							$mn_total_pagado += $mn_pago_p;
							
							#Contamos cuantos refunds son.
                            if($fg_refund_p==1){
                                
                                $contador_refunds++;

                            }
							
                        }
						if(empty($mn_total_pagado))
						$mn_total_pagado=0;
						
						
                        # Verificamos si repitio el grado
                        $Query3 = "SELECT no_grado FROM k_alumno_term a, k_term b WHERE a.fl_term=b.fl_term and fl_alumno=$clave  ";
                        $rs3 = EjecutaQuery($Query3);
                        $r = '';
                        $repetido = 0;
                        for ($i = 0; $row3 = RecuperaRegistro($rs3); $i++) {
                            if ($r == $row3[0])
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
                        
                        
                        if($clave==2829){ #Caso en particular para Ariel Rabesca que reptie term.
                            $fl_term_ini=944;
                            $term_ini=944;
                            $term_ini_act=944;
                        }   


                        if ($term_ini_act == $term_ini){
                            $fl_term_ini = $term_ini;
                            if(!empty($pagos_realizados))
                              $pagos_extras = "AND no_pago>$pagos_realizados ";
                        }
                        else {
                            $fl_term_ini = $term_ini_act;
                            # Obtenemos el total de pagos y numeros de term para identificar los meses que cubre un term
                            $row1 = RecuperaValor("SELECT no_grados, $ds_pagos FROM c_programa a, k_programa_costos k WHERE a.fl_programa=k.fl_programa AND a.fl_programa =$fl_programa");
                            $no_grados = $row1[0];
                            $no_x_payments = $row1[1];
                            $meses_sumados = round(($no_semanas / 4) / $no_grados);
                            if ($repetido > 0) {
                                $meses_x_term = ($no_x_payments / $no_grados) * $repetido;
                                $meses_sumados = round(($no_semanas / 4) / $no_grados) * $repetido;
                            } else
                                $meses_x_term = round(($no_semanas / 4) / $no_grados);
                            if ($fg_opcion_pago == 1 AND $no_x_payments == 1) {
                                $meses_x_term = round(($no_semanas / 4) / $no_grados);
                            }
                            if($repetido>0){
                              # Obtenemos el total se pagos realizados y si recursa un term tendran que haber pagos extras de los 
                              $pagos_extras = "AND no_pago>$pagos_realizados-$meses_x_term ";
                            }
                            else{
                              $pagos_extras="";
                              $fl_term_ini = $term_ini;
                            }
                        }

                        #Con esto eliminamos errores de query cuando el term es repetido.
                        if(($clave<>963)&&($clave<>965)){
                            if(!empty($pagos_extras)){
                                $data = explode("-", $pagos_extras);
                                $pagos_extras=$data[0]; 
                            }
                        }
                        
                        # Datos de pagos que no se han realizado 
                        $Query = "SELECT fl_term_pago, no_opcion, no_pago, " . ConsultaFechaBD('fe_pago', FMT_FECHA) . " , $mn_due, DATEDIFF(a.fe_pago, '$fe_actual') no_dias ";
                        $Query .= ", (SELECT nb_periodo FROM c_periodo l, k_term s WHERE l.fl_periodo=s.fl_periodo AND s.fl_term=a.fl_term) terms ";
                        $Query .= "FROM k_term_pago a, k_app_contrato b WHERE fl_term=$fl_term_ini ";
                        $Query .= "AND no_opcion=$fg_opcion_pago AND no_contrato=1 AND cl_sesion='$cl_sesion' $pagos_extras ";
                        if($clave==3833){
                            $Query.=" AND fl_term_pago<>4576 ";  
                        }
                        $Query .= "ORDER BY no_pago ";
                        $rs = EjecutaQuery($Query);
						$mn_pago=0;
                        for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
                            $fl_term_pago = $row[0];
                            $no_opcion = $row[1];
                            $no_pago = $row[2];
                            if ($term_ini_act != $term_ini) {
                                if ($repetido > 0)
                                    $no_pago_lista = ($pagos_realizados + 1) + $i;
                                else
                                    $no_pago_lista = $pagos_realizados + $i;
                            } else
                                $no_pago_lista = $no_pago;
                            $fe_limite_pago = $row[3];
                            $mn_pago = $row[4];
                            $no_dias = $row[5];
                            $terms = $row[6];

                            if(($clave==3833)&&($fl_term_pago==6006)){
                                $mn_pago="9856.00";
                            }
                            if(($clave==3833)&&(($fl_term_pago==4577)||($fl_term_pago==6761))){
                                $mn_pago="4928.00";
                            }
							if(($clave==3833)&&($fl_term==5718)){
								$fe_pago="April 5, 2021";
								$fe_limite_pago="April 5, 2021";
								$no_pago_lista=4;
							}

                            /*caso Ekam*/
                            if(($clave==3550)&&($fl_term_pago==6005)){
                                $mn_pago="3093.75";
                            }

                            /*case Maedlyn*/
                            if ($fl_term_pago == 6350 && $clave == 11512 & $no_pago_lista == 3) {
                                $mn_pago = 3638;

                            }


                            //para obtener los pagos
                            $concat = array(ConsultaFechaBD('fe_pago', FMT_FECHA), "' '", ConsultaFechaBD('fe_pago', FMT_HORA));
                            $Query = "SELECT fl_term_pago, ";
                            $Query .= "CASE cl_metodo_pago ";
                            $Query .= "WHEN 1 THEN '" . ObtenEtiqueta(488) . "' WHEN 2 THEN '" . ObtenEtiqueta(488) . " Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' ";
                            $Query .= "END ds_metodo_pago, ";
                            $Query .= "(" . ConcatenaBD($concat) . ") fe_pago, mn_pagado, ds_comentario, fl_alumno_pago, cl_metodo_pago,fg_refund ";
                            $Query .= "FROM k_alumno_pago a ";
                            $Query .= "WHERE fl_term_pago=$fl_term_pago ";
                            $Query .= "AND fl_alumno=$clave";

                            $row = RecuperaValor($Query);
                            $fl_t_pago = !empty($row[0])?$row[0]:NULL;
                            $ds_metodo_pago = !empty($row[1])?$row[1]:NULL;
                            if (empty($ds_metodo_pago))
                                $ds_metodo_pago = "(To be paid)";
                            $fe_pago = !empty($row[2])?$row[2]:NULL;
                            if (empty($fe_pago))
                                $fe_pago = "(To be paid)";
                            $mn_pagado = !empty($row[3])?$row[3]:NULL;
                            if (empty($mn_pagado))
                                $mn_pagado = "(To be paid)";
                            $ds_comentario_det = !empty($row[4])?str_uso_normal($row[4]):NULL;
                            $fl_alumno_pago = !empty($row[5])?$row[5]:NULL;
                            $cl_metodo_pago_det = !empty($row[6])?$row[6]:NULL;
                            $fg_refund = !empty($row[7])?$row[7]:NULL;


                            if (empty($fl_t_pago)) {
                                if (empty($proximo_pago)) {
                                    $pinta_pdf = false;
                                    $proximo_pago = $fl_term_pago;
                                    $no_opcion_pagar = $no_opcion;
                                    $no_pago_pagar = $no_pago;
                                    $fe_limite_pago_pagar = $fe_limite_pago;
                                    # Validamos si los dias son menores 0(paso fecha) paga late fee, si son mayores o igual a  0 pago normal
                                    if ($no_dias < 0)
                                        $late_fee = ObtenConfiguracion(66);
                                    $mn_due_pagar = $mn_pago;
                                }
                            } else
                                $pinta_pdf = true;

                            if ($i % 2 == 0)
                                $clase = "css_tabla_detalle";
                            else
                                $clase = "css_tabla_detalle_bg";

                            /*if ($fg_opcion_pago == 1) {

                                if ($mn_costs > 0) {
                                    $mn_pago = $mn_pago - $mn_costs;
                                    $mn_pagado = $mn_pagado - $mn_costs;
                                }
                            
                            }*/
                            


                            # Para los alumnos incritos solo obtendra los pagos que hacen faltan por pagar
                            # Para los alumnos que no se han inscrito obtendra tanto pagos realizados como lo que hacen falta
                            if (empty($fl_t_pago)) {

                                $mn_pago = $mn_pago + $tax_mn_cost;

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
	  if($fg_refund==0)
	   $mn_total_falta_pagar += $mn_pago;
                            }
                        }
						
		if(empty($mn_total_falta_pagar))
        $mn_total_falta_pagar=0;

	
                        Forma_Tabla_Fin();
                        Forma_Espacio();

                        # Vaidamos que se puedan generan taxes
                        ?>
                        <!-- Inicia tabla de los T220a -->
                        <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(692) ?> </strong></legend></h2>
                        <?php
                        if (!empty($fg_taxes)) {
                            ?>                            
                            <div class="col-sm-12 col-lg-12 col-xs-12">
                              <?php echo str_uso_normal(ObtenMensaje(228)); ?>
                            </div>
                            <?php 
                            # Obtenemos el pago total que realizo el estudiante
                            $rowt = RecuperaValor("SELECT $mn_due FROM k_app_contrato WHERE cl_sesion='$cl_sesion'");
                            $mn_due_tax = $rowt[0];

                            if($clave==1535){
                                $mn_due_tax=14900;

                            }


                            Forma_Espacio();
                            # Le sumamos lo numero de meses a la fecha inicial para obtener el fecha final
                            # Calculamos la cantidad que se paga por mes
                            $fe_inicio1 = DATE_FORMAT(date_create($fe_inicio_term), 'Y-m-d');
                            $mes_inicio1 = DATE_FORMAT(date_create($fe_inicio_term), 'm');
                            $anio_inicio1 = DATE_FORMAT(date_create($fe_inicio_term), 'Y');
                            $meses = ($no_semanas / 4);
                            $fe_nueva = strtotime('+ ' . ($meses - 1) . ' month', strtotime($fe_inicio1));
                            $fe_fin1 = date('Y-m-d', $fe_nueva);
                            $mes_fin1 = date('m', $fe_nueva);
                            $anio_fin1 = date('Y', $fe_nueva);
                            $anios1 = $anio_fin1 - $anio_inicio1;
                            $msg = ObtenEtiqueta(2239);
                            $msg = str_replace("#current_year#", "<strong>".date('Y')."</strong>", $msg);
                            $msg = str_replace("#program_start_date#", "<strong>".$anio_inicio1."</strong>", $msg);
                            echo "                          
                            <div class='alert alert-danger fade in'>                            
                              ".$msg."
                            </div>";

                          $titulos = array(ObtenEtiqueta(360), 'Year', 'Initial month', 'Final month', ObtenEtiqueta(583) . '|center', '');
                          $ancho_col = array('20%', '20%', '20%', '20%', '5%');
                          Forma_Tabla_Ini('100%', $titulos, !empty($ancho)?$ancho:0);
                          for ($i = 0; $i <= $anios1; $i++) {
                              $anios2 = $anio_inicio1 + $i;
                              if ($anios2 < date('Y')) {
                                  # Obtiene los meses que conforman el anio para el que se pago 
                                  if ($anio_inicio1 == $anio_fin1)
                                      $num_meses_anio = $mes_fin1 - $mes_inicio1 + 1;
                                  else {
                                      $num_meses_anio = 12;
                                      if ($anios2 == $anio_fin1)
                                          $num_meses_anio = $mes_fin1;
                                      if ($anios2 == $anio_inicio1)
                                          $num_meses_anio = 12 - $mes_inicio1 + 1;
                                  }
                                  
                                  # Monto pagado en el anio
								  $no_pagos_opcion=$no_pagos_opcion-$contador_refunds;
                                  $monto=0;
                                  if($no_pagos_opcion>0)
                                  {
                                      $monto = ($mn_due_tax / ($meses/$no_pagos_opcion)) * $num_meses_anio;
                                  }
                                 

                                  $monto = number_format($monto,2,'.',',');
                                  
                                  # Obtenemos los meses que cubren lo pagos
                                  # Obtenemos su nombre para mostrarlos en la tabla
                                  if ($anios2 == $anio_inicio1) {
                                      if ($anio_inicio1 == $anio_fin1) {
                                          $mes_ini = $mes_inicio1;
                                          $mes_fin = $mes_fin1;
                                      } else {
                                          $mes_ini = $mes_inicio1;
                                          $mes_fin = 12;
                                      }
                                  } 
                                  else {
                                      $mes_ini = 1;
                                      $mes_fin = $mes_fin1;
                                      if ($anios2 != $anio_fin1)
                                          $mes_fin = 12;
                                  }

                                  # Si el alumno se retiro antes de acabar el curso
                                  # Obtenemos el ultimo pago y hasta ahi sumamos las cantidades
                                  if (!empty($fg_desercion) AND ( $anios2 != $anio_fin1 AND $anios2 != $anio_inicio1)) {
                                      $Query = "SELECT DATE_FORMAT(fe_pago,'%m') FROM k_alumno_pago WHERE fl_alumno=$clave AND DATE_FORMAT(fe_pago, '%Y')='$anios2' order by fe_pago DESC ";
                                      $row = RecuperaValor($Query);
                                      $num_meses_anio = $row[0];
                                      $mes_fin = $row[0];
                                  }

                                  // # Monto pagado en el anio
                                  // $monto = ($mn_pago / ($meses / $no_pagos_opcion)) * $num_meses_anio;
                                  // $monto = number_format($monto, 2, '.', ',');

                                  $mes_ini = ObtenNombreMes($mes_ini);
                                  $mes_fin = ObtenNombreMes($mes_fin);
                                  # Datos de los taxes
                                  echo "
                                  <tr>
                                    <td>" . $nb_programa . "</td>
                                    <td>" . $anios2 . "</td>
                                    <td>" . $mes_ini . "</td>
                                    <td>" . $mes_fin . "</td>
                                    <td align='center'>" . $monto . "</td>
                                    <!--<td><a href='" . PATH_CAMPUS . "/students/taxes.php?anio=$anios2&fl_alumno=$clave&fl_term=$fl_term&num_meses_anio=$num_meses_anio&monto=$monto' target='_blank'><img src='" . PATH_IMAGES . "/icon_pdf.gif' width=12 height=12 border=0 title='" . ObtenEtiqueta(487) . "'></a></td>-->
                                    <td><a href='" . PATH_CAMPUS . "/students/taxes.php?anio=$anios2&fl_alumno=$clave&fl_term=$fl_term&num_meses_anio=$num_meses_anio&monto=$monto' target='_blank'  title='".ObtenEtiqueta(487)."'><i class='fa fa-file-pdf-o fa-2x'></i></a></td>
                                  </tr>";
                              }
                          }
                          Forma_Tabla_Fin();                            
                        }
                        else{
                          Forma_Espacio();
                          echo "                          
                          <div class='alert alert-danger fade in'>                            
                            ".str_replace("#course_name#", "<strong>".$nb_programa."</strong>", ObtenEtiqueta(2238))."
                          </div>";
                          
                        }
                        ?>
                    </div>
                    <div class="tab-pane fade" id="applicationForm">

                            	<!----------librerias para presentar sliders Barra azul--------->
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.min.css" />
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.css" />
			<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.js" ></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.min.js" ></script>               
                    <!--=============Presenta Rubric================-->
                    
                        <div class="row">
                            <div class="col-md-12">

                                <a  class="btn btn-default" style="float:right;" data-toggle="modal" data-target="#AbrePreviewRubric" Onclick="MuestraRubric();"><i class='fa fa-table' aria-hidden='true'></i>&nbsp;Rubric</a>

                                
                                              <!-- Preview Rubric -->
                                              <div class="modal fade" id="AbrePreviewRubric" tabindex="-1" role="dialog" aria-labelledby="myModalLabelaa" aria-hidden="true">
                                                <div class="modal-dialog" style="width:90%;">
                                                  <div class="modal-content" id="muestra_rubric">
                                                 


                                                  </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                              </div><!-- /.modal -->


                                <script>
                                    function MuestraRubric(){
                                        
                                        var cl_sesion='<?php echo $cl_sesion;  ?>';
                                        var fl_programa=<?php echo $fl_programa; ?>;

                                            $.ajax({
                                                type: 'POST',
                                                url: 'muestra_rubric.php',
                                                data: 'cl_sesion='+cl_sesion+
                                                      '&fl_programa='+fl_programa,									   
                                                async: true,
                                                success: function (html) {

                                                    $('#muestra_rubric').html(html);

                                                }
                                            });
                                    }

                                </script>






                            </div>
                        </div>



                    
                    
                    <!---============================-->
                    





                        <div class="row">
                            <div class="col-xs-12" style="padding-lef:10px;paddin-right:10px;padding-top:10px;">

                                <!-- Availability for Online Live Classes in your local time -->
                                <div class="jarviswidget" id="wid-id-2" role="widget2" style="" 
                                     data-widget-colorbutton="false"	
                                     data-widget-editbutton="false"
                                     data-widget-togglebutton="false"
                                     data-widget-deletebutton="false">
                                    <!-- widget options:
                                            usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
                                            
                                            data-widget-colorbutton="false"	
                                            data-widget-editbutton="false"
                                            data-widget-togglebutton="false"
                                            data-widget-deletebutton="false"
                                            data-widget-fullscreenbutton="false"
                                            data-widget-custombutton="false"
                                            data-widget-collapsed="true" 
                                            data-widget-sortable="false"
                                            
                                    -->
                                    <header role="heading">
                                        <div class="jarviswidget-ctrls" role="menu">  
                                            <!--                                        
                                            <a href="javascript:void(0);" class="button-icon jarviswidget-edit-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Edit"><i class="fa fa-cog "></i></a> 
                                            -->
                                            <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a> 
                                            <!--<a href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Fullscreen"><i class="fa fa-expand "></i></a> -->
                                            <!--<a href="javascript:void(0);" class="button-icon jarviswidget-delete-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Delete"><i class="fa fa-times"></i></a>-->
                                        </div>
                                        <!--                                        <div class="widget-toolbar" role="menu">
                                                                                    <a data-toggle="dropdown" class="dropdown-toggle color-box selector" href="javascript:void(0);"></a>
                                                                                    <ul class="dropdown-menu arrow-box-up-right color-select pull-right"><li><span class="bg-color-green" data-widget-setstyle="jarviswidget-color-green" rel="tooltip" data-placement="left" data-original-title="Green Grass"></span></li>
                                                                                        <li><span class="bg-color-greenDark" data-widget-setstyle="jarviswidget-color-greenDark" rel="tooltip" data-placement="top" data-original-title="Dark Green"></span></li>
                                                                                        <li><span class="bg-color-greenLight" data-widget-setstyle="jarviswidget-color-greenLight" rel="tooltip" data-placement="top" data-original-title="Light Green"></span></li>
                                                                                        <li><span class="bg-color-purple" data-widget-setstyle="jarviswidget-color-purple" rel="tooltip" data-placement="top" data-original-title="Purple"></span></li>
                                                                                        <li><span class="bg-color-magenta" data-widget-setstyle="jarviswidget-color-magenta" rel="tooltip" data-placement="top" data-original-title="Magenta"></span></li>
                                                                                        <li><span class="bg-color-pink" data-widget-setstyle="jarviswidget-color-pink" rel="tooltip" data-placement="right" data-original-title="Pink"></span></li><li><span class="bg-color-pinkDark" data-widget-setstyle="jarviswidget-color-pinkDark" rel="tooltip" data-placement="left" data-original-title="Fade Pink"></span></li><li><span class="bg-color-blueLight" data-widget-setstyle="jarviswidget-color-blueLight" rel="tooltip" data-placement="top" data-original-title="Light Blue"></span></li><li><span class="bg-color-teal" data-widget-setstyle="jarviswidget-color-teal" rel="tooltip" data-placement="top" data-original-title="Teal"></span></li><li><span class="bg-color-blue" data-widget-setstyle="jarviswidget-color-blue" rel="tooltip" data-placement="top" data-original-title="Ocean Blue"></span></li><li><span class="bg-color-blueDark" data-widget-setstyle="jarviswidget-color-blueDark" rel="tooltip" data-placement="top" data-original-title="Night Sky"></span></li><li><span class="bg-color-darken" data-widget-setstyle="jarviswidget-color-darken" rel="tooltip" data-placement="right" data-original-title="Night"></span></li><li><span class="bg-color-yellow" data-widget-setstyle="jarviswidget-color-yellow" rel="tooltip" data-placement="left" data-original-title="Day Light"></span></li><li><span class="bg-color-orange" data-widget-setstyle="jarviswidget-color-orange" rel="tooltip" data-placement="bottom" data-original-title="Orange"></span></li><li><span class="bg-color-orangeDark" data-widget-setstyle="jarviswidget-color-orangeDark" rel="tooltip" data-placement="bottom" data-original-title="Dark Orange"></span></li><li><span class="bg-color-red" data-widget-setstyle="jarviswidget-color-red" rel="tooltip" data-placement="bottom" data-original-title="Red Rose"></span></li><li><span class="bg-color-redLight" data-widget-setstyle="jarviswidget-color-redLight" rel="tooltip" data-placement="bottom" data-original-title="Light Red"></span></li><li><span class="bg-color-white" data-widget-setstyle="jarviswidget-color-white" rel="tooltip" data-placement="right" data-original-title="Purity"></span></li><li><a href="javascript:void(0);" class="jarviswidget-remove-colors" data-widget-setstyle="" rel="tooltip" data-placement="bottom" data-original-title="Reset widget color to default">Remove</a></li></ul>
                                                                                </div>-->
                                        <h2>
                                            <strong>
                                                <?php echo ObtenEtiqueta(621) ?>
                                            </strong>
                                        </h2>				

                                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                                    <!-- widget div-->
                                    <div role="content">

                                        <!-- widget edit box
                                        <div class="jarviswidget-editbox">
                                            <input class="form-control" type="text">
                                            <span class="note"><i class="fa fa-check text-success"></i> -------Change title to update and save instantly!</span>

                                        </div>
                                        end widget edit box -->

                                        <!-- widget content -->
                                        <div class="widget-body" style="min-height:0; padding-bottom: 0px;">
                                            <div class="row">
                                                <?php
                                                /**
                                                 * EGMC 20160520
                                                 * Cambio de Forma_Seccion
                                                 
                                                ////Forma_Seccion(ObtenEtiqueta(621));
                                                // preferencias
                                                $campos = array(ObtenEtiqueta(622), ObtenEtiqueta(623), ObtenEtiqueta(616));
                                                $cl_preferences = array($cl_preference_1, $cl_preference_2, $cl_preference_3);
                                                for ($i = 0; $i < 3; $i++) {
                                                    switch ($cl_preferences[$i]) {
                                                        case 0: $preferencia2 = ' ';
                                                            break;
                                                        case 1: $preferencia2 = ObtenEtiqueta(624);
                                                            break;
                                                        case 2: $preferencia2 = ObtenEtiqueta(625);
                                                            break;
                                                        case 3: $preferencia2 = ObtenEtiqueta(626);
                                                            break;
                                                        case 4: $preferencia2 = ObtenEtiqueta(627);
                                                            break;
                                                        case 5: $preferencia2 = ObtenEtiqueta(628);
                                                            break;
                                                        case 6: $preferencia2 = ObtenEtiqueta(629);
                                                            break;
                                                        case 7: $preferencia2 = ObtenEtiqueta(630);
                                                            break;
                                                    }
                                                    ?>
                                                    <!--<div class="col-xs-12 col-sm-4">-->
                                                        <?php 
                                                        echo Btstrp_Forma_CampoInfo($campos[$i], $preferencia2);
                                                        //$opc = array(ObtenEtiqueta(624), ObtenEtiqueta(625), ObtenEtiqueta(626), ObtenEtiqueta(627), ObtenEtiqueta(628), ObtenEtiqueta(629), ObtenEtiqueta(630));
                                                        //$val = array('1', '2', '3', '4', '5', '6', '7');
                                                        //Forma_CampoSelect(ObtenEtiqueta(622), True, 'cl_preference_1', $opc, $val, $cl_preference_1, $cl_preference_1_err, True);
                                                        ?>
                                                    <!--</div>-->
                                                    <?php
                                                }*/
                                                $opc = array(ObtenEtiqueta(624), ObtenEtiqueta(625), ObtenEtiqueta(626), ObtenEtiqueta(627), ObtenEtiqueta(628), ObtenEtiqueta(629), ObtenEtiqueta(630));
                                                $val = array('1', '2', '3', '4', '5', '6', '7');
                                                ?>
                                                <div class="row padding-bottom-10">
                                                  <div class="col-sm-4">
                                                    <?php echo Forma_CampoSelect(ObtenEtiqueta(622), True, 'cl_preference_1', $opc, $val, $cl_preference_1, !empty($cl_preference_1_err)?$cl_preference_1_err:NULL, True, '', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                                                  </div>
                                                  <div class="col-sm-4">
                                                    <?php echo Forma_CampoSelect(ObtenEtiqueta(623), True, 'cl_preference_2', $opc, $val, $cl_preference_2, !empty($cl_preference_2_err)?$cl_preference_2_err:NULL, True, '', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                                                  </div>
                                                  <div class="col-sm-4">
                                                <?php echo Forma_CampoSelect(ObtenEtiqueta(616), True, 'cl_preference_3', $opc, $val, $cl_preference_3, !empty($cl_preference_3_err)?$cl_preference_3_err:NULL, True, '', 'left', 'col col-sm-12', 'col col-sm-12');   ?>
                                                  </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end widget content -->

                                    </div>
                                    <!-- end widget div -->
                                </div>
                                <?php
                                /*
                                  <!-- Availability for Online Live Classes in your local time -->
                                  <legend> <?php echo ObtenEtiqueta(621) ?> </legend>
                                  <div class="row">
                                  <div class="row">
                                  <?php
                                  ////Forma_Seccion(ObtenEtiqueta(621));
                                  // preferencias
                                  $campos = array(ObtenEtiqueta(622), ObtenEtiqueta(623), ObtenEtiqueta(616));
                                  $cl_preferences = array($cl_preference_1, $cl_preference_2, $cl_preference_3);
                                  for ($i = 0; $i < 3; $i++) {
                                  switch ($cl_preferences[$i]) {
                                  case 0: $preferencia2 = ' ';
                                  break;
                                  case 1: $preferencia2 = ObtenEtiqueta(624);
                                  break;
                                  case 2: $preferencia2 = ObtenEtiqueta(625);
                                  break;
                                  case 3: $preferencia2 = ObtenEtiqueta(626);
                                  break;
                                  case 4: $preferencia2 = ObtenEtiqueta(627);
                                  break;
                                  case 5: $preferencia2 = ObtenEtiqueta(628);
                                  break;
                                  case 6: $preferencia2 = ObtenEtiqueta(629);
                                  break;
                                  case 7: $preferencia2 = ObtenEtiqueta(630);
                                  break;
                                  }
                                  //                            Forma_CampoInfo($campos[$i], $preferencia2);
                                  ?>
                                  <div class="col-xs-12 col-sm-4">
                                  <?php echo Btstrp_Forma_CampoInfo($campos[$i], $preferencia2); ?>
                                  </div>
                                  <?php
                                  }
                                  ?>
                                  </div>
                                  </div>
                                 */
                                ?>
							</div>
						</div>	
                                <!-- 2. Career Assessment -->
						 <div class="row">
                            <div class="col-xs-12" style="padding-lef:10px;padding-right:10px;">		
								
                                <div class="jarviswidget" id="wid-id-3" role="widget3" style="" 
                                     data-widget-colorbutton="false"	
                                     data-widget-editbutton="false"
                                     data-widget-togglebutton="false"
                                     data-widget-deletebutton="false">
                                    <!-- widget options:
                                            usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
                                            
                                            data-widget-colorbutton="false"	
                                            data-widget-editbutton="false"
                                            data-widget-togglebutton="false"
                                            data-widget-deletebutton="false"
                                            data-widget-fullscreenbutton="false"
                                            data-widget-custombutton="false"
                                            data-widget-collapsed="true" 
                                            data-widget-sortable="false"
                                            
                                    -->
                                    <header role="heading">
                                        <div class="jarviswidget-ctrls" role="menu">  
<!--                                            <a href="javascript:void(0);" class="button-icon jarviswidget-edit-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Edit"><i class="fa fa-cog "></i></a>  -->
                                            <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a>
                                            <!--<a href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Fullscreen"><i class="fa fa-expand "></i></a> -->
                                            <!--<a href="javascript:void(0);" class="button-icon jarviswidget-delete-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Delete"><i class="fa fa-times"></i></a>-->
                                        </div>
                                        <!--                                        <div class="widget-toolbar" role="menu">
                                                                                    <a data-toggle="dropdown" class="dropdown-toggle color-box selector" href="javascript:void(0);"></a>
                                                                                    <ul class="dropdown-menu arrow-box-up-right color-select pull-right"><li><span class="bg-color-green" data-widget-setstyle="jarviswidget-color-green" rel="tooltip" data-placement="left" data-original-title="Green Grass"></span></li>
                                                                                        <li><span class="bg-color-greenDark" data-widget-setstyle="jarviswidget-color-greenDark" rel="tooltip" data-placement="top" data-original-title="Dark Green"></span></li>
                                                                                        <li><span class="bg-color-greenLight" data-widget-setstyle="jarviswidget-color-greenLight" rel="tooltip" data-placement="top" data-original-title="Light Green"></span></li>
                                                                                        <li><span class="bg-color-purple" data-widget-setstyle="jarviswidget-color-purple" rel="tooltip" data-placement="top" data-original-title="Purple"></span></li>
                                                                                        <li><span class="bg-color-magenta" data-widget-setstyle="jarviswidget-color-magenta" rel="tooltip" data-placement="top" data-original-title="Magenta"></span></li>
                                                                                        <li><span class="bg-color-pink" data-widget-setstyle="jarviswidget-color-pink" rel="tooltip" data-placement="right" data-original-title="Pink"></span></li><li><span class="bg-color-pinkDark" data-widget-setstyle="jarviswidget-color-pinkDark" rel="tooltip" data-placement="left" data-original-title="Fade Pink"></span></li><li><span class="bg-color-blueLight" data-widget-setstyle="jarviswidget-color-blueLight" rel="tooltip" data-placement="top" data-original-title="Light Blue"></span></li><li><span class="bg-color-teal" data-widget-setstyle="jarviswidget-color-teal" rel="tooltip" data-placement="top" data-original-title="Teal"></span></li><li><span class="bg-color-blue" data-widget-setstyle="jarviswidget-color-blue" rel="tooltip" data-placement="top" data-original-title="Ocean Blue"></span></li><li><span class="bg-color-blueDark" data-widget-setstyle="jarviswidget-color-blueDark" rel="tooltip" data-placement="top" data-original-title="Night Sky"></span></li><li><span class="bg-color-darken" data-widget-setstyle="jarviswidget-color-darken" rel="tooltip" data-placement="right" data-original-title="Night"></span></li><li><span class="bg-color-yellow" data-widget-setstyle="jarviswidget-color-yellow" rel="tooltip" data-placement="left" data-original-title="Day Light"></span></li><li><span class="bg-color-orange" data-widget-setstyle="jarviswidget-color-orange" rel="tooltip" data-placement="bottom" data-original-title="Orange"></span></li><li><span class="bg-color-orangeDark" data-widget-setstyle="jarviswidget-color-orangeDark" rel="tooltip" data-placement="bottom" data-original-title="Dark Orange"></span></li><li><span class="bg-color-red" data-widget-setstyle="jarviswidget-color-red" rel="tooltip" data-placement="bottom" data-original-title="Red Rose"></span></li><li><span class="bg-color-redLight" data-widget-setstyle="jarviswidget-color-redLight" rel="tooltip" data-placement="bottom" data-original-title="Light Red"></span></li><li><span class="bg-color-white" data-widget-setstyle="jarviswidget-color-white" rel="tooltip" data-placement="right" data-original-title="Purity"></span></li><li><a href="javascript:void(0);" class="jarviswidget-remove-colors" data-widget-setstyle="" rel="tooltip" data-placement="bottom" data-original-title="Reset widget color to default">Remove</a></li></ul>
                                                                                </div>-->
                                        <h2>
                                            <strong>
                                                <?php echo ObtenEtiqueta(56) ?>
                                            </strong>
                                        </h2>				

                                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                                    </header>

                                    <!-- widget div-->
                                    <div role="content">

                                        <!-- widget edit box -
                                        <div class="jarviswidget-editbox">
                                            <input class="form-control" type="text
                                            <span class="note"><i class="fa fa-check text-success"></i> Change title to update and save instantly!</span>

                                        </div>
                                        <!-- end widget edit box -->

                                        <!-- widget content -->
                                        <div class="row">
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(301), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_1."</div></div>"); ?>
                                          </div>
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(302), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_2."</div></div>"); ?>
                                          </div>
                                        </div>
                                        <div class="row">
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(303), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_3."</div></div>"); ?>
                                          </div>
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(304), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_4."</div></div>"); ?>
                                          </div>
                                        </div>
                                        <div class="row">
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(305), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_5."</div></div>"); ?>
                                          </div>
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(306), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_6."</div></div>"); ?>
                                          </div>
                                        </div>
                                        <div class="row">
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(307), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_7."</div></div>"); ?>
                                          </div>
                                          <div class="col-xs-12 col-sm-6">
                                          &nbsp;
                                          </div>
                                        </div>                                      
                                    </div>
                                    <!-- end widget div -->
                                </div>
							</div>
						</div>	
                                <!--3. Computer Skills Assessment-->
								
						 <div class="row">
                            <div class="col-xs-12" style="padding-lef:10px;padding-right:10px;">
		
                                <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(78) ?> </strong></legend></h2>
                                <!-- Computer Skills Assessment -->
                                <div class="jarviswidget" id="wid-id4" role="widget4" style="" 
                                     data-widget-colorbutton="false"	
                                     data-widget-editbutton="false"
                                     data-widget-togglebutton="false"
                                     data-widget-deletebutton="false">
                                    <!-- widget options:
                                            usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
                                            
                                            data-widget-colorbutton="false"	
                                            data-widget-editbutton="false"
                                            data-widget-togglebutton="false"
                                            data-widget-deletebutton="false"
                                            data-widget-fullscreenbutton="false"
                                            data-widget-custombutton="false"
                                            data-widget-collapsed="true" 
                                            data-widget-sortable="false"
                                            
                                    -->
                                    <header role="heading">
                                        <div class="jarviswidget-ctrls" role="menu">  
<!--                                            <a href="javascript:void(0);" class="button-icon jarviswidget-edit-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Edit"><i class="fa fa-cog "></i></a>  -->
                                            <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a>
                                            <!--<a href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Fullscreen"><i class="fa fa-expand "></i></a> -->
                                            <!--<a href="javascript:void(0);" class="button-icon jarviswidget-delete-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Delete"><i class="fa fa-times"></i></a>-->
                                        </div>
                                        <!--                                        <div class="widget-toolbar" role="menu">
                                                                                    <a data-toggle="dropdown" class="dropdown-toggle color-box selector" href="javascript:void(0);"></a>
                                                                                    <ul class="dropdown-menu arrow-box-up-right color-select pull-right"><li><span class="bg-color-green" data-widget-setstyle="jarviswidget-color-green" rel="tooltip" data-placement="left" data-original-title="Green Grass"></span></li>
                                                                                        <li><span class="bg-color-greenDark" data-widget-setstyle="jarviswidget-color-greenDark" rel="tooltip" data-placement="top" data-original-title="Dark Green"></span></li>
                                                                                        <li><span class="bg-color-greenLight" data-widget-setstyle="jarviswidget-color-greenLight" rel="tooltip" data-placement="top" data-original-title="Light Green"></span></li>
                                                                                        <li><span class="bg-color-purple" data-widget-setstyle="jarviswidget-color-purple" rel="tooltip" data-placement="top" data-original-title="Purple"></span></li>
                                                                                        <li><span class="bg-color-magenta" data-widget-setstyle="jarviswidget-color-magenta" rel="tooltip" data-placement="top" data-original-title="Magenta"></span></li>
                                                                                        <li><span class="bg-color-pink" data-widget-setstyle="jarviswidget-color-pink" rel="tooltip" data-placement="right" data-original-title="Pink"></span></li><li><span class="bg-color-pinkDark" data-widget-setstyle="jarviswidget-color-pinkDark" rel="tooltip" data-placement="left" data-original-title="Fade Pink"></span></li><li><span class="bg-color-blueLight" data-widget-setstyle="jarviswidget-color-blueLight" rel="tooltip" data-placement="top" data-original-title="Light Blue"></span></li><li><span class="bg-color-teal" data-widget-setstyle="jarviswidget-color-teal" rel="tooltip" data-placement="top" data-original-title="Teal"></span></li><li><span class="bg-color-blue" data-widget-setstyle="jarviswidget-color-blue" rel="tooltip" data-placement="top" data-original-title="Ocean Blue"></span></li><li><span class="bg-color-blueDark" data-widget-setstyle="jarviswidget-color-blueDark" rel="tooltip" data-placement="top" data-original-title="Night Sky"></span></li><li><span class="bg-color-darken" data-widget-setstyle="jarviswidget-color-darken" rel="tooltip" data-placement="right" data-original-title="Night"></span></li><li><span class="bg-color-yellow" data-widget-setstyle="jarviswidget-color-yellow" rel="tooltip" data-placement="left" data-original-title="Day Light"></span></li><li><span class="bg-color-orange" data-widget-setstyle="jarviswidget-color-orange" rel="tooltip" data-placement="bottom" data-original-title="Orange"></span></li><li><span class="bg-color-orangeDark" data-widget-setstyle="jarviswidget-color-orangeDark" rel="tooltip" data-placement="bottom" data-original-title="Dark Orange"></span></li><li><span class="bg-color-red" data-widget-setstyle="jarviswidget-color-red" rel="tooltip" data-placement="bottom" data-original-title="Red Rose"></span></li><li><span class="bg-color-redLight" data-widget-setstyle="jarviswidget-color-redLight" rel="tooltip" data-placement="bottom" data-original-title="Light Red"></span></li><li><span class="bg-color-white" data-widget-setstyle="jarviswidget-color-white" rel="tooltip" data-placement="right" data-original-title="Purity"></span></li><li><a href="javascript:void(0);" class="jarviswidget-remove-colors" data-widget-setstyle="" rel="tooltip" data-placement="bottom" data-original-title="Reset widget color to default">Remove</a></li></ul>
                                                                                </div>-->
                                        <h2>
                                            <strong>
                                                <?php echo ObtenEtiqueta(79) ?>
                                            </strong>
                                        </h2>				

                                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                                    </header>

                                    <!-- widget div-->
                                    <div role="content">

                                        <!-- widget edit box 
                                        <div class="jarviswidget-editbox">
                                            <!-- This area used as dropdown edit box 
                                            <input class="form-control" type="text">
                                            <span class="note"><i class="fa fa-check text-success"></i> Change title to update and save instantly!</span>

                                        </div>
                                        <!-- end widget edit box -->
                                        
                                        <div class="row">
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(82), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_1_1) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                          </div>
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(83), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_1_2) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                          </div>
                                        </div>
                                        <div class="row">
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(84), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_1_3) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                          </div>
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(85), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_1_4) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                          </div>
                                        </div>
                                        <div class="row">
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(86), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_1_5) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                          </div>
                                          <div class="col-xs-12 col-sm-6">
                                            <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(87), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_1_6) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                          </div>
                                        </div>                                        
                                    </div>
                                </div>
							</div>
						</div>	
                            
                                <!-- Basic Internet Skills -->
						 <div class="row">
                            <div class="col-xs-12" style="padding-lef:10px;padding-right:10px;">
		
                                <div class="jarviswidget" id="wid-id5" role="widget5" style="" 
                                     data-widget-colorbutton="false"	
                                     data-widget-editbutton="false"
                                     data-widget-togglebutton="false"
                                     data-widget-deletebutton="false">
                                    <header role="heading">
                                        <div class="jarviswidget-ctrls" role="menu">  
                                            <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a>
                                            <!--<a href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Fullscreen"><i class="fa fa-expand "></i></a> -->
                                        </div>
                                        <h2>
                                            <strong>
                                                <?php echo ObtenEtiqueta(80) ?>
                                            </strong>
                                        </h2>				

                                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                                    </header>
                                    <div role="content">
                                      <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(88), 
                                          "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_2_1) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(89), 
                                          "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_2_2) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(90), 
                                          "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_2_3) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(91), 
                                          "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_2_4) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(92), 
                                          "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_2_5) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(93), 
                                          "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_2_6) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(94), 
                                          "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".(($fg_resp4_2_7) ? ETQ_SI : ETQ_NO)."</div></div>"); ?>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                          &nbsp;
                                        </div>
                                      </div>
                                    </div>
                                </div>
							</div>	
						</div>
                                <!-- Computer Graphics Software Skills -->
						 <div class="row">
                            <div class="col-xs-12" style="padding-lef:10px;padding-right:10px;">
		
                                <div class="jarviswidget" id="wid-id6" role="widget6" style="" 
                                     data-widget-colorbutton="false"	
                                     data-widget-editbutton="false"
                                     data-widget-togglebutton="false"
                                     data-widget-deletebutton="false">
                                    <header role="heading">
                                        <div class="jarviswidget-ctrls" role="menu">  
                                            <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a>
                                            <!--<a href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Fullscreen"><i class="fa fa-expand "></i></a> -->
                                        </div>
                                        <h2>
                                            <strong>
                                                <?php echo ObtenEtiqueta(81) ?>
                                            </strong>
                                        </h2>				

                                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                                    </header>
                                    <div role="content">
                                      <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <?php
                                            switch ($fg_resp4_3_1) {
                                                case '0': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(97));
                                                    break;
                                                case '1': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(98));
                                                    break;
                                                case '2': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(99));
                                                    break;
                                                case '3': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(107));
                                                    break;
                                            }
                                            ?>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <?php
                                            switch ($fg_resp4_3_2) {
                                                case '0': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(97));
                                                    break;
                                                case '1': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(98));
                                                    break;
                                                case '2': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(99));
                                                    break;
                                                case '3': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(107));
                                                    break;
                                            }
                                            ?>
                                        </div>
                                      </div>
                                    </div>
                                </div>
							</div>	
                        </div>        

                                <!-- 4. Expectations Questionnaire -->
						 <div class="row">
                            <div class="col-xs-12" style="padding-lef:10px;padding-right:10px;">
		
                                <div class="jarviswidget" id="wid-id7" role="widget7" style="" 
                                     data-widget-colorbutton="false"	
                                     data-widget-editbutton="false"
                                     data-widget-togglebutton="false"
                                     data-widget-deletebutton="false">
                                    <header role="heading">
                                        <div class="jarviswidget-ctrls" role="menu">  
                                            <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a>
                                            <!--<a href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Fullscreen"><i class="fa fa-expand "></i></a> -->
                                        </div>
                                        <h2>
                                            <strong>
                                                <?php echo ObtenEtiqueta(57) ?>
                                            </strong>
                                        </h2>				

                                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                                    </header>
                                    <div role="content">
                                      <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(308), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp3_1."</div></div>"); ?>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                          <?php
                                            echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(309), 
                                            '<div class="row"><div class="col-xs-12 col-sm-10 col-sm-offset-1">' . "<strong>1.-</strong> " . $ds_resp3_2_1 . '</div></div>' .
                                            '<div class="row"><div class="col-xs-12 col-sm-10 col-sm-offset-1">' . "<strong>2.-</strong> " . $ds_resp3_2_2 . '</div></div>' .
                                            '<div class="row"><div class="col-xs-12 col-sm-10 col-sm-offset-1">' . "<strong>3.-</strong> " . $ds_resp3_2_3 . '</div></div>');
                                            ?>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(310), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp3_3."</div></div>"); ?>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(311), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp3_4."</div></div>"); ?>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                          <?php echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(312), 
                                          "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp3_5."</div></div>"); ?>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                          <?php
                                          switch ($ds_resp3_6) {
                                            case 'A': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(313), 
                                                "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(314)."</div></div>");
                                                break;
                                            case 'B': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(313), 
                                                "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(315)."</div></div>");
                                                break;
                                            case 'C': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(313), 
                                                "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(316)."</div></div>");
                                                break;
                                          }
                                          ?>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                          <?php
                                          switch ($ds_resp3_7) {
                                            case 'A': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(317), 
                                                "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(318)."</div></div>");
                                                break;
                                            case 'B': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(317), 
                                                "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(319)."</div></div>");
                                                break;
                                            case 'C': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(317), 
                                                "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(320)."</div></div>");
                                                break;
                                            case 'D': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(317), 
                                                "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(321)."</div></div>");
                                                break;
                                            case 'E': echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(317), 
                                                "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(322)."</div></div>");
                                                break;
                                          }
                                          ?>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                          <?php
                                            echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(323), 
                                            "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp3_8."</div></div>");
                                            ?>
                                        </div>
                                      </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    
 
                    </div>
                    <div class="tab-pane fade" id="grades">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 padding-5">
                                <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(549) ?> </strong></legend></h2>
                                <div class="row">
                                    <?php
                                    
                                    PresentaAcademiHistory($clave,$fl_programa);
                                    
                                    ?>
                                    <div id='dlg_grade'  class="no-border"><div id='dlg_grade_content'></div></div>
                                    <?php
# Promedio General del curso
# si aun no tiene calificacion la calculara para posterior save 
# si ya hay pone la de la tabla c_alumno 
                                    if (!empty($suma_cal_t) AND ! empty($factor_promedio_t))
                                        $promedio_t = round(($suma_cal_t / $factor_promedio_t) * 100) / 100;
                                    else
                                        $promedio_t = 0;
                                    // Forma_Espacio();
                                    if (!empty($suma_cal_t) AND ! empty($factor_promedio_t))
                                        $promedio_t = round(($suma_cal_t / $factor_promedio_t));
                                    else
                                        $promedio_t = 0;
# Actualiza el promedio del student
                                    if(empty($no_promedio_t) OR $no_promedio_t<=0)
                                      $promedio_t = $promedio_t;
                                    else
                                      $promedio_t = round($no_promedio_t);
                                    $Query = "SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($promedio_t) AND no_max >= ROUND($promedio_t)";
                                    $prom_t = RecuperaValor($Query);
                                    $fg_aprbado_grl = $prom_t[1];

# Actualizamos el promedio total del student
                                    EjecutaQuery("UPDATE c_alumno SET no_promedio_t=$promedio_t WHERE fl_alumno=$clave");
                                    ?>
                                    <!-- Grafica para el promedio 
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 pull-right">
                                        <div class="easy-pie-chart txt-color-<?php
                                        echo  "<strong>". $prom_t[0] . "</strong>";
                                        if (!empty($fg_aprbado_grl))
                                            echo "greenLight";
                                        else
                                            echo "orangeDark";
                                        ?>" data-percent="<?php echo $promedio_t; ?>" data-pie-size="50">
                                            <span class="percent percent-sign"><?php echo $promedio_t; ?></span>
                                            <canvas width="5" height="50"></canvas>                                        
                                        </div>                                    
                                        <span class="txt-color-<?php
                                        if (!empty($fg_aprbado_grl))
                                            echo "greenLight";
                                        else
                                            echo "orangeDark";
                                        ?>"><?php echo ObtenEtiqueta(524) . "&nbsp"; ?></span>
                                    </div>-->
                                    <?php
                                    // Forma_Doble_Fin();
                                    Forma_CampoOculto('no_promedio_t', $promedio_t);

                                    # Seccion del historial de los alumnos en los grupos y cursos
                                    // Forma_Espacio();
                                    ?>
                                </div>
                                <h2 class="no-margin"><legend><strong> Student history  </strong></legend></h2>
                                <!--<div class="row">
                                    <div class="row">-->
                                <?php
                                // * EGMC 20160520
                                // * Cambio de Forma_Seccion
//                        Forma_Seccion('Student history ');
                                $titulos = array(ObtenEtiqueta(360), ObtenEtiqueta(381), ObtenEtiqueta(365),
                                    ObtenEtiqueta(420), ObtenEtiqueta(421), '');
                                $ancho_col = array('25%', '10%', '10%', '10%', '10%', '5%');
                                Forma_Tabla_Ini('100%', $titulos, $ancho_col, 'studentHistory');

                                /*$Query = "SELECT nb_programa, nb_periodo, no_grado, nb_grupo, CONCAT(ds_nombres, ds_apaterno, ds_amaterno) ds_teacher  ";
                                $Query .= "FROM k_alumno_historia a, c_programa b, c_periodo c, c_grupo d, c_usuario e ";
                                $Query .= "WHERE a.fl_programa=b.fl_programa AND a.fl_periodo=c.fl_periodo AND a.fl_grupo=d.fl_grupo ";
                                $Query .= "AND a.fl_maestro=e.fl_usuario AND fl_alumno=$clave  GROUP  BY d.fl_term ORDER BY a.fe_inicio";*/
                                $Query  = "SELECT pr.nb_programa, pe.nb_periodo, kah.no_grado, gr.nb_grupo, ";
                                $Query .= "CONCAT(us.ds_nombres, ' ',us.ds_apaterno, ' ',us.ds_amaterno) nb_maestro, ma.ds_ruta_avatar, kah.no_grado ";
                                $Query .= "FROM k_alumno_historia kah ";
                                $Query .= "LEFT JOIN c_maestro ma ON(ma.fl_maestro=kah.fl_maestro) ";
                                $Query .= "LEFT JOIN c_usuario us ON(us.fl_usuario=kah.fl_maestro) ";
                                $Query .= "LEFT JOIN c_periodo pe ON(pe.fl_periodo=kah.fl_periodo) ";
                                $Query .= "LEFT JOIN c_programa pr ON(pr.fl_programa=kah.fl_programa) ";
                                $Query .= "JOIN c_grupo gr ON(gr.fl_grupo=kah.fl_grupo) ";
                                $Query .= "WHERE kah.fl_alumno = $clave GROUP BY  kah.fl_grupo ORDER BY kah.no_grado ";
                                $rs = EjecutaQuery($Query);
                                for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
                                    $nb_programa = str_texto($row[0]);
                                    $nb_periodo = str_texto($row[1]);
                                    if ($grado_repetido == $row[2])
                                        $recurse = ObtenEtiqueta(853);
                                    else
                                        $recurse = "";
                                    $no_grado = $row[2];
                                    $nb_grupo = str_texto($row[3]);
                                    $ds_teacher = str_texto($row[4]);
                                    $grado_repetido = $no_grado;
                                    if ($i % 2 == 0)
                                        $clase = "css_tabla_detalle";
                                    else
                                        $clase = "css_tabla_detalle_bg";
                                    echo "
                                  <tr class='$clase'>
                                    <td>" . $nb_programa . "</td>
                                    <td>" . $nb_periodo . "</td>
                                    <td align='center'>" . $no_grado . "</td>
                                    <td>" . $nb_grupo . "</td>
                                    <td>" . $ds_teacher . "</td>
                                    <td><div style='color:red; font-weight:bold;'>" . $recurse . "</div></td>
                                  </tr>";
                                }

                                Forma_Tabla_Fin();
                                ?>
                                <!--</div>
                            </div>-->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="status">
                        <!--<div class="row">-->
                        <div class="col-xs-12 col-sm-12">
                          <!-- Student Status -->
                          <h2 class="no-margin"><legend><strong> Student Status </strong></legend></h2>
                          <!--<div class="row">
                            <div class=" col col-sm-12 col-lg-12" style="border-color:#103cff;">                                    
                              <small style="color:#aaa;"><i class="fa fa-money"><?php echo str_uso_normal(ObtenMensaje(227)); ?><i></i></i></small>
                            </div>
                          </div>-->
                          
                          <!--Titulo-->
                          <div class="col-sm-3 no-padding">
                            <div class="text text-align-center col-sm-12" style="color:#33;">
                              <blockquote class="twitter-tweet padding-10">
                              <?php echo str_uso_normal(ObtenMensaje(227)); ?>
                              </blockquote>
                            </div>
                          </div>
                          
                          <!--Active-->
                          <div class="col-sm-3">
                              <?php
                              Forma_CampoCheckbox('', 'fg_activo', $fg_activo,"<strong>".ObtenEtiqueta(113)."</strong>", '', True, '', 'left', 'col-sm-1', 'col-sm-11');
                              //Btstrp_V_Forma_CampoCheckbox(ObtenEtiqueta(113), 'fg_activo', $fg_activo);
                              ?>
                          </div>
                          <!--Student withdrawal-->
                          <div class="col-sm-3">
                              <?php
                              Forma_CampoCheckbox('', 'fg_desercion', $fg_desercion, "<strong>".ObtenEtiqueta(558)."</strong>", '', True, '', 'left', 'col-sm-1', 'col-sm-11');
                              ?>
                          </div>
                          <!--Student dismissed-->
                          <div class="col-sm-3">
                              <?php
                              Forma_CampoCheckbox('', 'fg_dismissed', $fg_dismissed, "<strong>".ObtenEtiqueta(559)."<br/></strong>", '', True, '', 'left', 'col-sm-11', 'col-sm-6');
                              ?>
                          </div>
                          <!--Graduated-->
                          <div class="col-sm-3">
                              <?php
                              Forma_CampoCheckbox('', 'fg_graduacion', $fg_graduacion, "<strong>".ObtenEtiqueta(645)."</strong>", '', True, '', 'left', 'col-sm-1', 'col-sm-11');
                              ?>
                          </div>
                          <!--work placement-->
                          <div class="col-sm-3">
                              <?php
                              Forma_CampoCheckbox('', 'fg_job', $fg_job, "<strong>".ObtenEtiqueta(644)."</strong>", '', True, '', 'left', 'col-sm-1', 'col-sm-11');
                              ?>
                          </div>                            
                          <!--Academic Credential-->
                          <div class="col-sm-3">
                              <?php
                              Forma_CampoCheckbox('', 'fg_certificado', $fg_certificado, "<strong>".ObtenEtiqueta(547)."</strong>", '', True, '', 'left', 'col-sm-1', 'col-sm-11');
                              ?>
                          </div>
                          <!--Awards-->
                          <div class="col-sm-3">
                              <?php
                              Forma_CampoCheckbox('', 'fg_honores', $fg_honores, "<strong>".ObtenEtiqueta(548)."</strong>", '', True, '', 'left', 'col-sm-1', 'col-sm-11');
                              ?>
                          </div>
                          <!--Notas-->
						  <div class="col-sm-3">
                              <?php
                              Forma_CampoCheckbox('', 'fg_absence', $fg_absence, "<strong>".ObtenEtiqueta(2058)."</strong>", '', True, '', 'left', 'col-sm-1', 'col-sm-11');
                              ?>
                          </div>
						  
						  <div class="col-sm-3">
                              <?php
                              Forma_CampoCheckbox('', 'fg_change_status', $fg_change_status, "<strong>".ObtenEtiqueta(2059)."</strong>", '', True, '', 'left', 'col-sm-1', 'col-sm-11');
                              ?>
                          </div>
                          <div class="col-sm-3">
                             <?php
                             Forma_CampoCheckbox('', 'fg_scholarship', $fg_scholarship, "<strong>Scholarship</strong>", '', True, '', 'left', 'col-sm-1', 'col-sm-11');
                             ?>
                          </div>
                          <div class="col-sm-3">
                              &nbsp;
                          </div>
                          <div class="col-sm-6">
                              &nbsp;
                          </div>
                            <div class="col-sm-3">
                              &nbsp;
                          </div>
                          
						  
						   <div class="col-sm-6"><div style="margin-left:40px"><br />
						       <?php
                               Btstrp_Forma_CampoTextArea("<strong>".ObtenEtiqueta(196)."</strong>", False, 'ds_notas', $ds_notas, 0, 3);
                               ?></div>
						   </div>
						  
						  
                          <div class="col-sm-3">
                              <p>&nbsp;</p>
                          </div>
                           <!--Date applicant enrolled as a student-->
                          <div class="col-sm-4">
                              <?php
                              echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(111), $fe_alta);
                              Forma_CampoOculto('fe_alta', $fe_alta);
                              ?>
                          </div>
                          <!--Last Login-->
                          <div class="col-sm-3">
                              <?php
                              echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(112), $fe_ultacc);
                              Forma_CampoOculto('fe_ultacc', $fe_ultacc);
                              ?>
                          </div>
                          <!--Time Accessed-->
                          <div class="col-sm-3">
                              <?php
                              echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(122), $no_accesos);
                              Forma_CampoOculto('no_accesos', $no_accesos);
                              ?>
                          </div>
                          <div class="col-sm-3">
                              <?php
                              
                              $Querys="SELECT ds_graduate_status,job_title FROM k_ses_app_frm_1 where cl_sesion='$cl_sesion' ";
                              $rows=RecuperaValor($Querys);
                              $ds_graduate_status=$rows['ds_graduate_status'];
                              $job_title=$rows['job_title'];

                              $opc_quiz = array(ObtenEtiqueta(2654), ObtenEtiqueta(2655), ObtenEtiqueta(2656), ObtenEtiqueta(2657), ObtenEtiqueta(2658), ObtenEtiqueta(2659),'No possible contact after 3 attempts');
                              $val_quiz = array('1', '2', '3', '4', '5', '6','7');
                              ?>
                               <?php echo Forma_CampoSelect('Follow Up Type', False, 'ds_graduate_status', $opc_quiz, $val_quiz, $ds_graduate_status, !empty($ds_graduate_status_err)?$ds_graduate_status_err:NULL, True, '', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                                                 
                              <br />
                          </div>
                           <div class="col-sm-3">
                            <?php  $Query  = "SELECT CONCAT(no_code,'-',description)immigration, fl_immigrations_status FROM immigrations_status  ORDER BY fl_immigrations_status ASC ";                                  
                                   Forma_CampoSelectBD(ObtenEtiqueta(2663), False, 'fl_immigrations_status', $Query, $fl_immigrations_status, '', True,'','left','col col-sm-12','col col-sm-12');
                            ?>
                          </div>
                          <div class="col-sm-3">
                              <?php 
                              
                              Forma_CampoTexto('Job Title',False,'job_title',$job_title,50,40,$job_title_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                              ?>
                          </div>
                         
                          

                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12">
                                <div class="col-sm-3">
                                    <?php
                                        Forma_CampoTexto('Notation for Transcripts and Diplomas',False,'notation_transcript',$notation_transcript,250,100,$notation_transcript_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <!-- Official Transcript information -->
                        <h2 class="no-margin"><legend><strong> <?php echo ObtenEtiqueta(543) ?></strong> </legend></h2>
                        <?php
                        /**
                         * EGMC 20160520
                         * Cambio de Forma_Seccion
                         */
#Datos del Official Transcipt
//Forma_Seccion(ObtenEtiqueta(543));
                        ?>
                        <div class="row">
                            <?php
							/*
							if(($clave==10072)||($clave==11477)||($clave="")){
								
								
							}else{
							
								# Vamos a asumar las fecha fin del curso si es que repitio term y cambio de term 
								if ($repetido >= 1 || $term_ini_act != $term_ini) {
									$fe_inicio_term = date_format(date_create($fe_inicio_term), 'Y-m-d');
									$rowm = RecuperaValor("SELECT DATE_ADD('$fe_inicio_term', INTERVAL ($meses + $meses_sumados) MONTH)");
									$fe_fin = date_format(date_create($rowm[0]), 'd-m-Y');
									$fe_completado = $fe_fin;
								}
							}*/
                            ?>
                            <div class="col-sm-4">
                            <?php Forma_CampoInfo(ObtenEtiqueta(382), $fe_inicio, 'left', 'col col-sm-12', 'col col-sm-4' );  ?>
                            </div>
                            <div class="col-sm-8">
                              <div class="col-sm-6">
                              <?php
                              Forma_CampoTexto(ObtenEtiqueta(544) . ' ' . ETQ_FMT_FECHA, False, 'fe_fin', $fe_fin, 10, 10, $fe_fin_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12');                            
                              Forma_Calendario('fe_fin');
                              ?>
                              </div>
                              <div class="col-sm-6">
                              <?php
                              Forma_CampoTexto(ObtenEtiqueta(545) . ' ' . ETQ_FMT_FECHA, False, 'fe_completado', $fe_completado, 10, 10, $fe_completado_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12');
                              Forma_Calendario('fe_completado');
                              ?>
                              </div>
                              <div class="col-sm-6">
                              <?php
                              Forma_CampoTexto(ObtenEtiqueta(556) . ' ' . ETQ_FMT_FECHA, False, 'fe_graduacion', $fe_graduacion, 10, 10, $fe_graduacion_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12');
                              Forma_Calendario('fe_graduacion');
                              ?>
                              </div>
                              <div class="col-sm-6">
                              <?php
                              Forma_CampoTexto(ObtenEtiqueta(546) . ' ' . ETQ_FMT_FECHA, False, 'fe_emision', $fe_emision, 10, 10, $fe_emision_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12');
                              Forma_Calendario('fe_emision');
                              ?>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="record">                    
                      <table id="login_record" width='100%' class='table table-striped table-hover dataTable no-footer has-columns-hidden'>
                      <thead>
                        <tr style="background-color:#0092cd; color:#fff;">
                          <th style="background-color:#0092cd;"><?php echo ObtenEtiqueta(510); ?></th>
                          <th style="background-color:#0092cd;"><?php echo "Login date";?></th>
                          <th style="background-color:#0092cd;"><?php echo "Logout date";?></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      # Consulta para el listado
                      $Query = "SELECT a.fl_usu_login, CONCAT(b.ds_nombres, ' ', b.ds_apaterno) '".ObtenEtiqueta(510)."|left', 
                                a.fe_login 'Login date', a.fe_logout 'Logout date'
                                FROM k_usu_login a, c_usuario b
                                WHERE a.fl_usuario = b.fl_usuario
                                AND a.fl_usuario = $clave
                                ORDER BY fe_login DESC";
                      $rs = EjecutaQuery($Query);
                      for($i=0;$row=RecuperaRegistro($rs);$i++){
                        echo "
                        <tr>
                          <td>".str_texto($row[1])."</td>
                          <td>".$row[2]."</td>
                          <td>".$row[3]."</td>
                        </tr>";
                      }                      
                      ?>
                      </tbody>
                      </table>                     
                    </div>  

                   



                    <div class="tab-pane fade" id="contract"> 

                        
                        <div class="row no-margin padding-top-10">
                             <div class="col-sm-4">
                                    <?php Forma_CampoTexto(ObtenEtiqueta(587), True, 'ds_discount', $ds_discount, 50, 32, !empty($ds_discount_err)?$ds_discount_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                             </div>
                            <div class="col-sm-4">
                                    <?php Forma_CampoTexto('Total discount', True, 'mn_discount', $mn_discount, 50, 32, !empty($mn_discount_err)?$mn_discount_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                             </div>
                            <div class="col-sm-4">
                                
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-8">
                                 <h4>Header:</h4>
                                 <?php Forma_CampoTinyMCE("", False, 'ds_header', $ds_header, 50, 20, $ds_header_err);?>
                            </div>

                            <div class="col-md-2"> 
                             </div>
                      
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-8">
                                 <h4>Body:</h4>
                                 <?php Forma_CampoTinyMCE("", False, 'ds_contrato', $ds_contrato, 50, 20, $ds_contrato_err);?>
                            </div>

                            <div class="col-md-2"> 
                             </div>
                      
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-8">
                                <h4>Footer:</h4>

                                 <?php Forma_CampoTinyMCE("", False, 'ds_footer', $ds_footer, 50, 20, $ds_footer_err);?>
                            </div>

                            <div class="col-md-2"> 
                             </div>
                      
                        </div>

                        


                </div>
            </div>
        </div>
    </div>
    <!-- end widget content -->

</div>
<!-- end widget div -->

</div>



<?php
# Actualizamos los datos de los contratos si no tiene el numero de semanas y el monto a pagar por contrato
if(empty($no_weeks) || empty($mn_payment_due))
  ContratosDetalles($cl_sesion, $fg_opcion_pago, $fl_programa);
# Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
if ($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_ALUMNOS, PERMISO_MODIFICACION);
else
    $fg_guardar = True;
?>
</div>
<?php
Forma_Termina($fg_guardar);




# Pie de Pagina
PresentaFooter();

#scripts para que funcione circulos verdes rubric.
echo"<script src='../../../modules/common/new_campus/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js'></script>";


/*
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
 */
?>
<script>
// eliminamos todos los datos
//window.localStorage.clear(); 
  function super_user(ds_log) {
      document.super.ds_login.value = ds_log;
      document.super.ds_password.value = '<?php echo ObtenConfiguracion(40) ?>';
      document.super.fg_campus.value = '1';
      document.super.action = '../../../login_validate.php';
      document.super.submit();
  }
  function borrar_template(url, fl_alumno_template, fl_sesion, origen) {
      var answer = confirm('<?php echo str_ascii(ObtenMensaje(MSG_ELIMINAR)) ?>');
      if (answer) {
          document.parametros.fl_alumno_template.value = fl_alumno_template;
          document.parametros.fl_sesion.value = fl_sesion;
          document.parametros.origen.value = origen;
          document.parametros.action = url;
          document.parametros.submit();
      }
  }
  function otro(url, clave, origen) {
      document.otro.clave.value = clave;
      document.otro.action = url;
      document.otro.submit();
  }

  // Muestra dialogo para asignar calificacion
  function AssignGrade(entrega, clave) {
      $.ajax({
          type: 'POST',
          url: '<?php echo PATH_CAMPUS ?>/teachers_new/ajax/get_assign_grades.php', async: false,
          data: 'fl_entrega_semanal=' + entrega + '&clave=' + clave + '&fl_usuario=' + <?php echo $fl_usuario; ?>,
          success: function (msg) {
              $('#dlg_grade_content').html(msg);
              $('#dlg_grade').dialog('open');
          }
      });
  }

 //Para resetar calificacion.
 function ResetGrading(fl_semana,clave){
    
     var answer = confirm("Are you sure to reset grading?");
  
     if(answer) {
        $.ajax({
              type: 'POST',
              url: 'reset_grade.php', 
              async: false,
              data: 'fl_semana=' + fl_semana + 
                    '&fl_usuario=' + clave,
            success: function(msg){
                
            }
        });
        window.location.reload();
    }
 }


  $('#dlg_grade').dialog({
      appendTo: '#content',
      autoOpen: false,
      resizable: false, width: 320,
      height: 330,
      hide: 'highlight',
      title: 'Assign grade',
      modal: true,
      buttons: {
          'Cancel': function () {
              $(this).dialog('close');
          },
          'Submit': function () {
              $(this).dialog('close');
              document.datos1.submit();
          }
      }
  });
  
  $(document).ready(function () {

    //pageSetUp();

    setup_widgets_desktop();

    runAllCharts();


    $("#studentHistory").DataTable();    
    $('#tbl_payments').dataTable( {
      "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'#go_tuiton.col-xs-12 col-sm-7 text-align-right'><'col-sm-1 col-xs-12 hidden-xs'l>>"+
      "t"+
      "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
      "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 11,12,13] } ],
    } );
    // Agregamos boton para ir a tuition
    $("#go_tuiton").append("<a class='btn btn-primary' href='payments_frm.php?clave=<?php echo $clave; ?>' target='blank'><i class='fa fa-money'></i> <?php echo ObtenEtiqueta(886); ?></a>");
    $("#login_record").DataTable();
    $("a").mouseover(function(){
        $("i.fa fa-file-pdf-o").css("background-color", "yellow");
    });
    /* Person Responsible*/
    $('#fg_responsable').change(function(){           
      if($(this).is(':checked')){
        $('#person_responsable').show();        
      }
      else{
        $('#person_responsable').hide();        
      }      
    });

    });

  //Muestra dialogo
  function change_pwd(){
    $('#vanas_preloader').show();
    $('#vanas_preloader').css('z-index','1000');
    $("#div_pwd").show();
    $("#t_div_pwd").html('<?php echo ObtenEtiqueta(126); ?>');
    $("#modal-body_pwd").css('heading','250px');    
    var clave = <?php echo $clave ?>;
    $.ajax({
      type: 'POST',
      url : 'pwd_frm.php',
      data: "clave="+clave,      
      async: false,
      success: function(html) {
        $('#div_pwd_msj').html(html);
      }
    });
  }
  function closed_pwd(){
    $('#div_pwd').hide();
    $('#vanas_preloader').hide();
  }
  function login_record(){
    $("#div_pwd").show();
    $("#t_div_pwd").html('Login record');
    $("#modal-body_pwd").css('height','480px');
    var clave = <?php echo $clave ?>;
    $.ajax({
      type: 'POST',
      url : 'historia_login_frm.php',
      data: "clave="+clave,      
      async: false,
      success: function(html) {
        $('#div_pwd_msj').html(html);
      }
    });
  }
  

  /* Acciones de los pagos */
  function realizar_refund(clave,pago_final,fg_inscrito,no_pago){
    dialogo_refund(clave,pago_final,fg_inscrito,no_pago);
  }
  function dialogo_refund(clave,pago_borrar,fg_inscrito,no_pago,type="R"){
      if(type=="C")
        weight=400;
      else
        weight=500;
      /*$("#div_pwd").dialog({
        width: weight,
        height: "auto"
      });*/
      $("#div_payments").show();
      $("#vanas_preloader").show();
      div_refund(clave,pago_borrar,fg_inscrito,no_pago,type);
  }
  /* Borra pago*/
  function pago(url,clave,borrar,fg_app_frm) {
    var answer = confirm('<?php echo str_ascii(ObtenMensaje(MSG_ELIMINAR)); ?>');
    if(answer) {
      document.borrarpago.clave.value  = clave;
      document.borrarpago.borrar.value  = borrar;
      document.borrarpago.fg_app_frm.value  = fg_app_frm;
      document.borrarpago.origen.value  = 'students_frm.php';
      document.borrarpago.action = url;
      document.borrarpago.submit();
    }
  }
</script>
<form name=borrarpago method=post>
  <input type=hidden name=clave>
  <input type=hidden name=borrar>
  <input type=hidden name=fg_app_frm>
  <input type=hidden name=origen>
</form>



<form name=parametros method=post>
    <input type=hidden name=fl_alumno_template>
    <input type=hidden name=fl_sesion>
    <input type=hidden name=origen>
</form>
<form name=otro method=post>
    <input type=hidden name=clave>
</form>
<form name='super' method='post' target='_blank'>
    <input type=hidden name=ds_login>
    <input type=hidden name=ds_password>      
    <input type=hidden name=fg_campus>      
</form>
<div style="display: none;" class="modal fade in" id="div_pwd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:350px;">
    <div class="modal-content">
      <div class="modal-header padding-5">
        <a href="javascript:closed_pwd();" class="close">
          <i class="fa fa-close"></i>
        </a>
        <h4 class="modal-title" id="t_div_pwd"></h4>
      </div>
      <div id="modal-body_pwd" class="modal-body no-padding">                            
        <div id='div_pwd_msj'></div>
      </div>
    </div>
  </div>
</div>

<div style="display: none;" id="div_payments" class="modal fade in">
  <div class="modal-dialog modal-content" id='ds_mensaje_refund' style="width:350px;">
  </div>
</div>


<!---colocamos totales y faltantes en payment history --->
<script>
    $(document).ready(function () {

        var monto_pagado="<?php echo number_format($mn_total_pagado,2)?>";
        var monto_falta_pagar="<?php echo number_format($mn_total_falta_pagar,2)?>";


        $('#payment_moment').empty();
        $('#payment_pending').empty();
       
        var monto_pagado=monto_pagado;
        var monto_falta_pagar=monto_falta_pagar;

        $('#payment_moment').append(monto_pagado);
        $('#payment_pending').append(monto_falta_pagar);




    });



</script>




<?php


echo"
  <script src='" . PATH_SELF_JS . "/plugin/x-editable/moment.min.js'></script>
    <script src='" . PATH_SELF_JS . "/plugin/x-editable/jquery.mockjax.min.js'></script>
    <script src='" . PATH_SELF_JS . "/plugin/x-editable/x-editable.min.js'></script>
";
echo"
<!---plugin necesario para pintar el circulo -->
  <script src='".PATH_HOME."/bootstrap/js/plugin/knob/jquery.knob.min.js'></script>

  ";

?>
