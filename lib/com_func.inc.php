<?php

# Definicion de constantes y funciones para interfase grafica
require_once('com_config.inc.php');
require_once('adodb/adodb.inc.php');
require_once('sha256/sha256.inc.php');
#MJD PHP MAILER 6.5
if (PHP_OS=='Linux') { # when is production
    require_once('/var/www/html/vanas/AD3M2SRC4/lib/vendor/phpmailer/phpmailer/src/PHPMailer.php');
    require '/var/www/html/vanas/AD3M2SRC4/lib/vendor/autoload.php';
}else{

    require_once($_SERVER['DOCUMENT_ROOT'].'/AD3M2SRC4/lib/vendor/phpmailer/phpmailer/src/PHPMailer.php');
    require ''.$_SERVER['DOCUMENT_ROOT'].'/AD3M2SRC4/lib/vendor/autoload.php';

}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


// For debug purpose, it needs to be commented
 error_reporting(0);

# Added the function to provide the languaje sufix
function langSufix(){
  $langselect = isset($_COOKIE[IDIOMA_NOMBRE])?$_COOKIE[IDIOMA_NOMBRE]:NULL;
  switch ($langselect) {
    case '1': $sufix = '_esp'; break;
    case '2': $sufix = ''; break;
    case '3': $sufix = '_fra'; break;
    default: $sufix = ''; break;
  }
  return $sufix;
}

#
# MRA: Funciones para el manejo de base de datos
#

# Inicia conexion a la base de datos
function ConectaBD() {

  $db = NewADOConnection(DATABASE_TYPE);
  $db->debug = D_DEBUG_ADO;
  if (!DATABASE_FG_DSN)
    $db->Connect(DATABASE_SERVER, DATABASE_USER, DATABASE_PWD, DATABASE_NAME);
  else
    $db->Connect(DATABASE_DSN, DATABASE_USER, DATABASE_PWD);
  $err_no = $db->ErrorNo();
  if (!empty($err_no)) {
    echo "Data base connection error $err_no - " . $db->ErrorMsg();
    exit;
  }
  $db->setCharset('utf8');
  $SQL = "SET
    character_set_results    = 'utf8mb4',
    character_set_client     = 'utf8mb4',
    character_set_connection = 'utf8mb4',
    character_set_database   = 'utf8mb4',
    character_set_server     = 'utf8mb4'";
  $db->execute($SQL);

  return $db;
}
# Funcion para generar textos de contratos y cartas
function genera_documento($clave, $opc, $correo = False, $firma = False, $no_contrato = 1, $fg_unofficial = 0)
{

    #variable initialization to prevent error
    $mn_payment_duee = 0;

    # Recupera datos de la sesion
    $Query = "SELECT cl_sesion, fg_inscrito, mn_tax_paypal,id_alumno,fl_pais_campus ";
    $Query .= "FROM c_sesion ";
    $Query .= "WHERE fl_sesion=$clave";
    $row = RecuperaValor($Query);
    $cl_sesion = $row[0];
    $fg_inscrito = $row[1];
    $app_fee_tax = $row[2];
    $student_id = $row[3];
    $fl_pais_campus = $row[4];

    if (!empty($student_id)) {
        $ds_login = $student_id;
    }

    $Query = "SELECT fg_payment FROM k_app_contrato WHERE cl_sesion='$clave' ";
    $ro_ = RecuperaValor($Query);
    $fg_payment = $ro_['fg_payment'];

    if (empty($fg_payment)) {
        $Query = "SELECT fg_payment FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
        $ro_ = RecuperaValor($Query);
        $fg_payment = $ro_['fg_payment'];
    }



    $fg_tipo_curso = ObtenEtiqueta(2386);

    $ds_campus = "CANADA";

    if ($fl_pais_campus == 226) { #USA
        if ($fg_payment == 'C') {
            $fg_tipo_curso = "Combined (Asynchronous)";
        }
        $ds_campus = "USA";
        $ds_direccion1 = "8105 Birch Bay Square St";
        $ds_direccion2 = "#103 Blaine, WA 98230";
        $ds_direccion3 = "United States";
        $ds_direccion_school = ObtenConfiguracion(168);
        $ds_direccion_acreditation_pdf = ObtenEtiqueta(2685);

    } else {
        if ($fg_payment == 'C') {
            $fg_tipo_curso = "Combined (Asynchronous)";
        }
        $ds_direccion1 = "270-5489 Byrne Rd, V5J 3J1";
        $ds_direccion2 = "Burnaby, British Columbia";
        $ds_direccion3 = "CANADA";
        $ds_direccion_school = ObtenConfiguracion(169);
        $ds_direccion_acreditation_pdf = ObtenEtiqueta(2686);

    }



    #Obtiene el login siempre y cuando ya este inscrito
    if ($fg_inscrito == '1') {
        $row = RecuperaValor("SELECT ds_login, fl_usuario FROM c_usuario WHERE cl_sesion='$cl_sesion'");
        $ds_login = $row[0];
        $fl_alumno = $row[1];
        $student_id = $ds_login;
    }
    # Recupera datos del aplicante: forma 1
    $Query = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
    $Query .= ConsultaFechaBD('fe_birth', FMT_FECHA) . " fe_birth, ";
    $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, d.ds_pais, ";
    $Query .= "nb_programa, fl_template, ds_duracion, nb_periodo, a.fl_programa, b.fg_tax_rate, a.ds_add_country, b.fg_fulltime, fg_total_programa,a.fl_periodo,b.ptib_approval ";
    $Query .= ",passport_number, ";
    $Query .= ConsultaFechaBD('passport_exp_date', FMT_FECHA) . " passport_exp_date,a.fg_provider,a.provider ";
    $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
    $Query .= "WHERE a.fl_programa=b.fl_programa ";
    $Query .= "AND a.fl_periodo=c.fl_periodo ";
    $Query .= "AND a.ds_add_country=d.fl_pais ";
    $Query .= "AND a.ds_eme_country=e.fl_pais ";
    $Query .= "AND cl_sesion='$cl_sesion'";
    $row = RecuperaValor($Query);
    $nb_programa = $row[14];
    $ds_fname = str_texto(html_entity_decode($row[0]));
    $ds_mname = str_texto(html_entity_decode($row[1]));
    $ds_lname = str_texto(html_entity_decode($row[2]));
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
    $fl_periodo = $row['fl_periodo'];
    $fl_programa_search = $row['fl_programa'];
    $ptib_approval = $row['ptib_approval'];
    $passport_number = $row['passport_number'];
    $passport_exp_date = $row['passport_exp_date'];
	$fg_provider= $row['fg_provider'];
	$provider = $row['provider'];

    $label_ptib_approval = ($row['ptib_approval']) ? ObtenEtiqueta(2687) : ObtenEtiqueta(2688);
    $yes_no_approval = ($row['ptib_approval']) ? 'Yes' : 'No';

    # Si es de canada obtendremos su provicia
    if ($row[20] == 38 and is_numeric($ds_add_state)) {
        $row_1 = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$ds_add_state");
        $st_state = $row_1[0];
    } else
        $st_state = $ds_add_state;
    $mailing_add = $ds_add_number . " " . $ds_add_street . ", " . $ds_add_city . " " . $st_state . ", " . $ds_add_country;
    $ds_duracion = $row[16];
    $nb_periodo = $row[17];
    $fl_programa = $row[18];
    $fg_tax_rate = $row[19];
    $fl_pais = $row[20];
    $fg_fulltime = $row[21];
    if (!empty($fg_fulltime))
        $fg_fulltime = ObtenEtiqueta(278);
    else
        $fg_fulltime = ObtenEtiqueta(279);
    $fg_total_programa = $row[22];
    # Obtenemos el periodo inicial cuando ya tenga un term definido
    $Queryp = "SELECT nb_periodo,b.fl_periodo FROM k_term a, c_periodo b ";
    $Queryp .= "WHERE fl_term=(SELECT  MIN(fl_term)FROM k_alumno_term WHERE ";
    $Queryp .= "fl_alumno=$fl_alumno) AND a.fl_periodo=b.fl_periodo ";
    $rowp = RecuperaValor($Queryp);
    if (ExisteEnTabla('k_alumno_term', 'fl_alumno', $fl_alumno)) {
        $nb_periodo = $rowp[0];
        $fl_periodo = $rowp['fl_periodo'];
    }



    #recuperamos datos de los calsstimes
    $Querype = "SELECT case
            when cl_dia=1 then 'Monday'
            when cl_dia=2 then 'Tuesday'
            when cl_dia=3 then 'Wednesday'
            when cl_dia=4 then 'Thursday'
            when cl_dia=5 then 'Friday'
            when cl_dia=6 then 'Saturday' end cl_dia, no_hora1,no_tiempo1,no_hora2,no_tiempo2 from c_periodo where fl_periodo=$fl_periodo ";
    $rowpe = RecuperaValor($Querype);
    if ($rowpe[0]) {



        $classtime_combined = $rowpe[0] . " " . $rowpe[1] . " " . $rowpe[2] . " to " . $rowpe[3] . " " . $rowpe[4];
        $class_time_combined_label = "Combined (Asynchronous) :" . $classtime_combined;

    } else {

        $classtime_combined = "";
        $class_time_combined_label = "";

    }






    # Template 3 es "Contract Email Template", en c_programa puede traer 1 o 2, que son "Short Term Duration Contract" y "Long Term Student Enrolment Contract"
    # Si trae otro numero, es otro fl_template
    if ($correo === True)
        $fl_template = 3;
    elseif ($correo === False)
        $fl_template = $row[15];
    else
        $fl_template = $correo;
    if ($fg_gender == 'M')
        $ds_gender = ObtenEtiqueta(115);
    if ($fg_gender == 'F')
        $ds_gender = ObtenEtiqueta(116);
    if ($fg_gender == 'N')
        $ds_gender = "Non-Binary";





    #Recuperamos los classtimes dependiendo del periodo y pograma elegido.
    $Queryc = "SELECT fl_class_time,fl_programa FROM k_class_time WHERE fl_programa=$fl_programa
             AND fl_periodo=$fl_periodo ";
    $rsm = EjecutaQuery($Queryc);
    $horarios = "";
    for ($iii = 1; $rowww = RecuperaRegistro($rsm); $iii++) {
        $fl_class_time = $rowww['fl_class_time'];
        $fl_programa_class = $rowww[1];



        $Wqe = "SELECT CASE WHEN cl_dia='1' THEN '" . ObtenEtiqueta(2390) . "'
								  WHEN cl_dia='2' THEN '" . ObtenEtiqueta(2391) . "'
								  WHEN cl_dia='3' THEN '" . ObtenEtiqueta(2392) . "'
								  WHEN cl_dia='4' THEN '" . ObtenEtiqueta(2393) . "'
								  WHEN cl_dia='5' THEN '" . ObtenEtiqueta(2394) . "'
								  WHEN cl_dia='6' THEN '" . ObtenEtiqueta(2395) . "'
								  ELSE '" . ObtenEtiqueta(2396) . "' END dia ,no_hora,ds_tiempo
					  FROM k_class_time_programa WHERE fl_class_time=$fl_class_time
					";
        $rs3 = EjecutaQuery($Wqe);
        $totclass = CuentaRegistros($rs3);

        $tiene_pro = 0;
        for ($mi = 1; $romi = RecuperaRegistro($rs3); $mi++) {

            $nb_di = $romi[0];
            $nd_hora = $romi[1];
            $ampm = $romi[2];

            $horarios .= $nb_di . " " . $nd_hora . " " . $ampm;
            if ($mi <= ($totclass - 1))
                $horarios .= ", ";
            else
                $horarios .= "";

            $tiene_pro = 1;
        }
    }


    $horarios_label_online = "Online: " . $horarios;

    #init variables.
    $third_party_payment = null;
    $confirmation_enrollment_deposit = null;
    $opt_white_selected = "   ";
    $opt_black_selected = "   ";
    $opt_america_selected = "   ";
    $opt_hawaiian_selected = "   ";
    $opt_asian_selected = "   ";
    $opt_multiracial_selected = "   ";
    $opt_other = "   ";
    $hispanic = null;
    $disabled = null;
    $military_veteran = null;
    $opt_male = "   ";
    $opt_female = "   ";
    $opt_hight_graduation = "   ";
    $opt_hight_graduate = "   ";
    $opt_ged = "   ";
    $opt_certificate = "   ";
    $opt_certificate_less = "   ";
    $opt_associate = "   ";
    $opt_bahelor = "   ";
    $opt_master = "   ";


    #case student other countrys
    if ($no_contrato == 201) {
        $no_contrato = 1;
        $fl_template = 201;
        $fl_pais_campus = 226;

        $Queryusa = "SELECT race,grade,hispanic,military,fg_disability,ds_sin,fg_gender FROM k_ses_app_frm_1  WHERE cl_sesion='$cl_sesion'  ";
        $rowusa = RecuperaValor($Queryusa);
        $race = $rowusa['race'];
        $grade = $rowusa['grade'];
        $hispanic = !empty($rowusa['hispanic']) ? "Yes" : "No";
        $military = !empty($rowusa['military']) ? "Yes" : "No";
        $fg_disability = !empty($rowusa['fg_disability']) ? "Yes" : "No";
        $ds_sin = $rowusa['ds_sin'];
        $fg_gender = $rowusa['fg_gender'];
        $military_veteran = !empty($rowusa['military']) ? "Yes" : "No";

        switch ($fg_gender) {
            case 'F':
                $opt_female = " X ";
                break;
            case 'M':
                $opt_male = " X ";
                break;
            case 'N':
                $opt_nobinary = " X ";
                break;

        }


        switch ($race) {

            case 'W':
                $opt_white_selected = " X ";
                break;
            case 'B':
                $opt_black_selected = " X ";
                break;
            case 'A':
                $opt_america_selected = " X ";
                break;
            case 'H':
                $opt_hawaiian_selected = " X ";
                break;
            case 'AS':
                $opt_asian_selected = " X ";
                break;
            case 'M':
                $opt_multiracial_selected = " X ";
                break;
            case 'O':
                $opt_other = " X ";
                break;
        }

        switch ($grade) {
            case 'L':
                $opt_hight_graduation = " X ";
                break;
            case 'H':
                $opt_hight_graduate = " X ";
                break;
            case 'G':
                $opt_ged = " X ";
                break;
            case 'S':
                $opt_certificate = " X ";
                break;
            case 'C':
                $opt_certificate_less = " X ";
                break;
            case 'A':
                $opt_associate = " X ";
                break;
            case 'B':
                $opt_bahelor = " X ";
                break;
            case 'M':
                $opt_master = " X ";
                break;

        }



    }

    #Recupera datos adicionales a la forma 1 y del contrato del aplicante
    $Query = "SELECT no_contrato, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_discount, ds_discount, mn_tot_tuition, mn_tot_program, ";
    $Query .= "mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
    $Query .= "ds_cadena, ds_firma_alumno, fg_opcion_pago, fe_firma, ds_p_name, ds_education_number, fg_international, ";
    $Query .= "cl_preference_1, cl_preference_2, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_pais, ";
    $Query .= "ds_firma_padre, ds_a_email, a.cl_metodo_pago, (SELECT r.ds_metodo_pago FROM k_methods_payments r WHERE a.cl_metodo_pago=r.cl_metodo_pago) ds_metodo_pago, ";
    $Query .= "a.ds_metodo_otro, a.no_weeks, a.mn_payment_due, ds_usual_name ";
    $Query .= ", a.ds_citizenship, a.fg_study_permit, a.fg_study_permit_other, a.fg_aboriginal, a.ds_aboriginal, a.fg_health_condition, a.ds_health_condition,fg_payment,tax_mn_cost ";
    $Query .= "FROM k_app_contrato a LEFT JOIN c_pais b ON a.ds_m_add_country=b.fl_pais ";
    $Query .= "WHERE cl_sesion='$cl_sesion' ";
    $Query .= "AND no_contrato=$no_contrato ";
    $row = RecuperaValor($Query);
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
    $ds_cadena = $row[17];
    $ds_firma_alumno = $row[18];
    $opc_pago = $row[19];
    $fg_payment = $row['fg_payment'];
    $tax_mn_cost = !empty($row['tax_mn_cost']) ? $row['tax_mn_cost'] : 0;

    switch ($opc_pago) {
        /*case 1: $mn_x_paid = ',mn_a_paid'; break;
        case 2: $mn_x_paid = ',mn_b_paid'; break;
        case 3: $mn_x_paid = ',mn_c_paid'; break;
        case 4: $mn_x_paid = ',mn_d_paid'; break;
        # En caso de que ya haya tenido una  opcion de pago elejira la primera
        default:
          $Queryc  = "SELECT CASE fg_opcion_pago WHEN 1 THEN 'mn_a_paid' WHEN 2 THEN 'mn_b_paid' WHEN 3 THEN 'mn_c_paid' WHEN 4 THEN 'mn_d_paid' ELSE 'mn_a_paid' END opc_pago ";
          $Queryc .= "FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1'";
          $rowc = RecuperaValor($Queryc);
          $mn_x_paid = ",".$rowc[0];
        break;*/
        case 1:
            $mn_x_paid = "," . $amount_paid_a;
            break;
        case 2:
            $mn_x_paid = "," . $amount_paid_b;
            break;
        case 3:
            $mn_x_paid = "," . $amount_paid_c;
            break;
        case 4:
            $mn_x_paid = "," . $amount_paid_d;
            break;
    }
    $fe_firma = $row[20];
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
    $ds_firma_padre = $row[32];
    $ds_a_email = $row[33];
    $p_mailing_add = $ds_m_add_number . " ";
    if (!empty($ds_m_add_street))
        $p_mailing_add .= $ds_m_add_street . ", ";
    $p_mailing_add .= $ds_m_add_city . " ";
    if (!empty($ds_m_add_state))
        $p_mailing_add .= $ds_m_add_state . ", ";
    $p_mailing_add .= $ds_m_add_country;
    if ($fg_international == '1')
        $ds_intl_st = "Yes";
    else
        $ds_intl_st = "No";
    # Metodo de pago desde el contrato
    $cl_metodo_pago = $row[34];
    $ds_metodo_pago = $row[35];
    $ds_metodo_otro = $row[36];
    $no_weeks_contrato = $row[37];
    $mn_payment_due = $row[38];
    $ds_usual_name = $row[39];
    $ds_citizenship = str_texto($row[40]);
    $fg_study_permit = $row[41];
    if (!empty($fg_study_permit))
        $fg_study_permit = ObtenEtiqueta(16);
    else
        $fg_study_permit = ObtenEtiqueta(17);
    $fg_study_permit_other = $row[42];
    if (!empty($fg_study_permit_other))
        $fg_study_permit_other = ObtenEtiqueta(16);
    else
        $fg_study_permit_other = ObtenEtiqueta(17);
    $fg_aboriginal = $row[43];
    if (!empty($fg_aboriginal))
        $fg_aboriginal = ObtenEtiqueta(16);
    else
        $fg_aboriginal = ObtenEtiqueta(17);
    $ds_aboriginal = str_texto($row[44]);
    $fg_health_condition = $row[45];
    if (!empty($fg_health_condition))
        $fg_health_condition = ObtenEtiqueta(16);
    else
        $fg_health_condition = ObtenEtiqueta(17);
    $ds_health_condition = $row[46];

    # Recupera datos de pagos del curso
    $Query = "SELECT no_a_payments, ds_a_freq, no_b_payments, ds_b_freq, no_c_payments, ds_c_freq, no_d_payments, ds_d_freq, cl_type, ";
    $Query .= "no_a_interes, no_b_interes, no_c_interes, no_d_interes, no_horas, no_semanas, ds_credential, cl_delivery, ds_language, fe_modificacion, no_horas_week $mn_x_paid ";
    if ($fl_pais_campus <> 38) {

        $Query .= "FROM k_programa_costos_pais a, c_programa b, k_template_doc c ";
    } else {

        $Query .= "FROM k_programa_costos a, c_programa b, k_template_doc c ";
    }

    $Query .= "WHERE a.fl_programa = b.fl_programa ";
    $Query .= "AND b.fl_template = c.fl_template ";
    $Query .= "AND a.fl_programa = $fl_programa";
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
    $no_horas = $row[13];
    $no_semanas = $row[14];
    $ds_credential = $row[15];
    $cl_delivery = $row[16];
    $ds_language = $row[17];
    $fe_modificacion = $row[18];
    $no_horas_week = $row[19];
    $mn_x_paid = $row[20];

    # Calculos pagos
    $total_tuition = number_format($tuition + $no_costos_ad - $no_descuento, 2, '.', '');
    $total = number_format($app_fee + $total_tuition, 2, '.', '');


    #Recovery additional information
    $QueryP = "SELECT ds_career,ds_objetives,ds_teaching,ds_evaluation,ds_requeriments,ds_program_org,ds_combinend FROM c_programa WHERE fl_programa=$fl_programa_search ";
    $rowp = RecuperaValor($QueryP);
    $ds_career = html_entity_decode($rowp['ds_career']);
    $ds_objetives = html_entity_decode($rowp['ds_objetives']);
    $ds_teaching = html_entity_decode($rowp['ds_teaching']);
    $ds_evaluation = html_entity_decode($rowp['ds_evaluation']);
    $ds_requeriments = html_entity_decode($rowp['ds_requeriments']);
    $ds_program_org = html_entity_decode($rowp['ds_program_org']);
    $ds_combinend = html_entity_decode($rowp['ds_combinend']);

    $grading_scale = '';

    switch ($fl_pais_campus) {

        case '38':

            $symbol = "$";
            break;
        case '226':
            $symbol = "$";
            break;
        case '199':
            $symbol = "€";
            break;
        case '73':
            $symbol = "€";
            break;
        case '80':
            $symbol = "€";
            break;
        case '105':
            $symbol = "€";
            break;
        case '225':
            $symbol = "£";
            break;
        case '153':
            $symbol = "€";
            break;
        default:
            $symbol = "$";
            break;

    }
    /*
      $Queryc="SELECT cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion ORDER BY fl_calificacion asc ";
      $rsc=EjecutaQuery($Queryc);
      $tablegrading='<table border="1" cellpadding="0" cellspacing="0" class="PlainTable11" style="width:100%">
                    <tbody>
                        <tr>
                            <td><strong>Letter</strong></td>
                            <td>
                            <p><strong>Percent</strong></p>
                            </td>
                            <td><strong>Description</strong></td>
                        </tr>';
      for ($xxx = 1; $roxxx = RecuperaRegistro($rsc); $xxx++) {


          $tablegrading.='<tr>
                            <td>'.$roxxx['cl_calificacion'].'</td>
                            <td>
                            <p>'.$roxxx['no_min'].'-'.$roxxx['no_max'].'</p>
                            </td>
                            <td>'.$roxxx['ds_calificacion'].'</td>
                        </tr>';

      }
      $tablegrading.='</tbody>
                    </table>';
    */

    # Recupera datos del template del documento
    switch ($opc) {
        case 1:
            $campo = "ds_encabezado";
            break;
        case 2:
            $campo = "ds_cuerpo";
            break;
        case 3:
            $campo = "ds_pie";
            break;
        case 4:
            $campo = "nb_template";
            break;
    }
    $Query = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
    $row = RecuperaValor($Query);

    # Sustituye caracteres especiales
    $cadena = html_entity_decode($row[0]);
    //  $cadena = str_replace("&lt;", "<", $cadena);
//  $cadena = str_replace("&gt;", ">", $cadena);
//  $cadena = str_replace("&quot;", "\"", $cadena);
//  $cadena = str_replace("&#47;", "/", $cadena);
//  $cadena = str_replace("&#039;", "'", $cadena);
//  $cadena = str_replace("&#061;", "=", $cadena);

    # Remplazamos el metodo de pago que realizo
    if ($cl_metodo_pago == 5)
        $cadena = str_replace("#paymethod_contract#", $ds_metodo_otro, $cadena);
    else {
        if (empty($ds_firma_alumno) and empty($cl_metodo_pago))
            $cadena = str_replace("#paymethod_contract#", ObtenEtiqueta(905), $cadena);
        else
            $cadena = str_replace("#paymethod_contract#", $ds_metodo_pago, $cadena);
    }


    #replace
	if($fg_provider=='1'){		
		$cadena = str_replace("#provider#", $provider, $cadena);
	}else{		
		$cadena = str_replace("#provider#", " ", $cadena);
	}
	
	$cadena = str_replace("#ds_careers#", $ds_career, $cadena);
    $cadena = str_replace("#ds_careers#", $ds_career, $cadena);
    $cadena = str_replace("#ds_objectives#", $ds_objetives, $cadena);
    $cadena = str_replace("#ds_teaching#", $ds_teaching, $cadena);
    $cadena = str_replace("#ds_evaluation#", $ds_evaluation, $cadena);
    $cadena = str_replace("#ds_requirements#", $ds_requeriments, $cadena);
    $cadena = str_replace("#ds_program_org#", $ds_program_org, $cadena);
    $cadena = str_replace("#ds_combined#", $ds_combinend, $cadena);
    $cadena = str_replace("#grading_scale#", $grading_scale, $cadena);

    $cadena = str_replace("#passport_number#", $passport_number, $cadena);
    $cadena = str_replace("#passport_exp_date#", $passport_exp_date, $cadena);

    if ($fl_template == 24) {

        $cadena = str_replace("#ds_combined_time#", $classtime_combined, $cadena);
    } else {

        if ($fg_tipo_curso == 'Combined') {

            $classtime_combined = $horarios_label_online . " " . $class_time_combined_label;
        } else {
            $classtime_combined = ""; // $horarios_label_online;//solo muestra online

        }




        $cadena = str_replace("#ds_combined_time#", $classtime_combined, $cadena);
    }










    # Si ya se graduo toma nombre del usuario actual.
    $Queryg = "SELECT fg_graduacion FROM k_pctia WHERE fl_alumno=$fl_alumno";
    $rowg = RecuperaValor($Queryg);
    $fg_graduado = $rowg['fg_graduacion'];
    if (!empty($fg_graduado)) {

        $Quers = "SELECT ds_nombres,ds_apaterno,ds_amaterno FROM c_usuario WHERE fl_usuario=$fl_alumno ";
        $rows = RecuperaValor($Quers);
        $ds_fname = $rows['ds_nombres'];
        $ds_mname = $rows['ds_amaterno'];
        $ds_lname = $rows['ds_apaterno'];

    }

    //if (empty($horarios))
    //  $horarios = "&nbsp;";
    #Sustituye variables del classtime.
    $cadena = str_replace("#hr_class_time#", "" . $horarios, $cadena); #Horarios classtimes


    if ($fl_template == 194) {
        $nb_programa = $nb_programa . " " . $ds_credential;

    }

    # Sustituye variables con datos del alumno
    $cadena = str_replace("#st_id#", "" . $student_id, $cadena); #Student first name
    $cadena = str_replace("#st_fname#", "" . $ds_fname, $cadena); #Student first name
    $cadena = str_replace("#pg_delivery#", "" . $fg_tipo_curso, $cadena); #Student pg_delivery
    $cadena = str_replace("#st_mname#", "" . $ds_mname, $cadena); #Student middle name
    $cadena = str_replace("#st_lname#", $ds_lname, $cadena); #Student last name
    $cadena = str_replace("#st_pname#", "" . $ds_p_name, $cadena); #Student previous name
    $cadena = str_replace("#st_usualname#", "" . $ds_usual_name, $cadena); #Student usual name
    $cadena = str_replace("#st_ednum#", "" . $ds_education_number, $cadena); #Student personal education number
    $cadena = str_replace("#st_lmadd#", "" . $mailing_add, $cadena); #Student local mailing address
    $cadena = str_replace("#st_lmaddpc#", "" . $ds_add_zip, $cadena); #Student local mailing address postal code
    $cadena = str_replace("#st_pmadd#", "" . $p_mailing_add, $cadena); #Student permanent mailing address
    $cadena = str_replace("#st_pmaddpc#", "" . $ds_m_add_zip, $cadena); #Student permanent mailing address postal code
    $cadena = str_replace("#st_street_no#", $ds_add_number, $cadena); #Student street number
    $cadena = str_replace("#st_street_name#", $ds_add_street, $cadena); #Student street
    $cadena = str_replace("#st_city#", $ds_add_city, $cadena); #Student city
    $cadena = str_replace("#st_country#", $ds_add_country, $cadena); #Student country
    $cadena = str_replace("#st_state#", $st_state, $cadena); #Student state
    $cadena = str_replace("#st_code_zip#", $ds_add_zip, $cadena); #Student codigo postal
    $cadena = str_replace("#st_pnone#", "" . $ds_number, $cadena); #Student telephone number
    $cadena = str_replace("#st_aphone#", "" . $ds_alt_number, $cadena); #Student alternative telephone number
    $cadena = str_replace("#st_email#", "" . $ds_email, $cadena); #Student email address
    $cadena = str_replace("#st_aemail#", "" . $ds_a_email, $cadena); #Student alternative email address
    $cadena = str_replace("#st_ist#", "" . $ds_intl_st, $cadena); #International student yes
    $cadena = str_replace("#st_byear#", "" . substr($fe_birth, 6, 4), $cadena); #Student year of birth
    $cadena = str_replace("#st_bmonth#", "" . substr($fe_birth, 3, 2), $cadena); #Student month of birth
    $cadena = str_replace("#st_bday#", "" . substr($fe_birth, 0, 2), $cadena); #Student day of birth
    $cadena = str_replace("#st_gender#", "" . $ds_gender, $cadena); #Student gender female
    $cadena = str_replace("#st_login#", "" . $ds_login, $cadena); #Student login
    $cadena = str_replace("#pg_name#", "" . $nb_programa, $cadena); #Program name
    $cadena = str_replace("#academic_status#", $fg_fulltime, $cadena); #Full o Part Time Program
    $cadena = str_replace("#hours_week#", $no_horas_week, $cadena); #Hour per week course
    $cadena = str_replace("#pg_durationh#", "" . $no_horas, $cadena); #Program duration in hours
    $cadena = str_replace("#pg_durationw#", "" . $no_semanas, $cadena); #Program duration in weeks
    // $no_duracion = round($no_semanas / 4.3, 0);
    $cadena = str_replace("#pg_durationm#", "" . $ds_duracion, $cadena); #Program duration in months

    $cadena = str_replace("#st_citizenship#", "" . $ds_citizenship, $cadena); # International Student citizenship
    $cadena = str_replace("#st_study_permit#", "" . $fg_study_permit, $cadena); # Have study permit
    $cadena = str_replace("#st_study_other_permit#", "" . $fg_study_permit_other, $cadena); # Have study other permit
    $cadena = str_replace("#fg_st_aboriginal#", "" . $fg_aboriginal, $cadena); # Identify yourselft as an Aboriginal person
    $cadena = str_replace("#ds_st_aboriginal#", "" . $ds_aboriginal, $cadena); # Aboriginal person
    $cadena = str_replace("#fg_st_disabilities#", "" . $fg_health_condition, $cadena); # Have physical or mental health condition
    $cadena = str_replace("#ds_st_disabilities#", "" . $ds_health_condition, $cadena); # What phhysical or mental health condition



    #app usa
    $cadena = str_replace("#third_party_payment#", "" . $third_party_payment, $cadena); ##third_party_payment#
    $cadena = str_replace("#confirmation_enrollment_deposit#", "" . $confirmation_enrollment_deposit, $cadena); ##confirmation_enrollment_deposit#
    $cadena = str_replace("#social_insurance_number_USA#", "" . $ds_sin, $cadena); ##social_insurance_number_USA#
    $cadena = str_replace("#opt_white_selected#", "" . $opt_white_selected, $cadena); ##opt_white_selected#
    $cadena = str_replace("#opt_black_selected#", "" . $opt_black_selected, $cadena); ##opt_black_selected#
    $cadena = str_replace("#opt_america_selected#", "" . $opt_america_selected, $cadena); ##opt_america_selected#
    $cadena = str_replace("#opt_hawaiian_selected#", "" . $opt_hawaiian_selected, $cadena); ##opt_hawaiian_selected#
    $cadena = str_replace("#opt_asian_selected#", "" . $opt_asian_selected, $cadena); ##opt_asian_selected#
    $cadena = str_replace("#opt_multiracial_selected#", "" . $opt_multiracial_selected, $cadena); ##opt_multiracial_selected#
    $cadena = str_replace("#opt_other#", "" . $opt_other, $cadena); ##opt_other#
    $cadena = str_replace("#hispanic#", "" . $hispanic, $cadena); ##hispanic#
    $cadena = str_replace("#disabled#", "" . $fg_disability, $cadena); ##disabled#
    $cadena = str_replace("#military_veteran#", "" . $military_veteran, $cadena); ##military_veteran#
    $cadena = str_replace("#opt_male#", "" . $opt_male, $cadena); ##sex#
    $cadena = str_replace("#opt_female#", "" . $opt_female, $cadena); ##sex#
    $cadena = str_replace("#opt_nonbinary#", "" . $opt_nobinary, $cadena);
    $cadena = str_replace("#opt_hight_graduation#", "" . $opt_hight_graduation, $cadena); ##opt_hight_graduation#
    $cadena = str_replace("#opt_hight_graduate#", "" . $opt_hight_graduate, $cadena); ##opt_hight_graduate#
    $cadena = str_replace("#opt_ged#", "" . $opt_ged, $cadena); ##opt_ged#
    $cadena = str_replace("#opt_certificate#", "" . $opt_certificate, $cadena); ##opt_certificate#
    $cadena = str_replace("#opt_certificate_less#", "" . $opt_certificate_less, $cadena); ##opt_certificate_less#
    $cadena = str_replace("#opt_associate#", "" . $opt_associate, $cadena); ##opt_associate#
    $cadena = str_replace("#opt_bahelor#", "" . $opt_bahelor, $cadena); ##opt_bahelor#
    $cadena = str_replace("#opt_master#", "" . $opt_master, $cadena); ##opt_master#
    $cadena = str_replace("#ds_campus#", $ds_campus, $cadena); # campus
    $cadena = str_replace("#ds_direccion1#", $ds_direccion1, $cadena);
    $cadena = str_replace("#ds_direccion2#", $ds_direccion2, $cadena);
    $cadena = str_replace("#ds_direccion3#", $ds_direccion3, $cadena);
    $cadena = str_replace("#st_address_campus#", $ds_direccion_school, $cadena);
    $cadena = str_replace("#st_address_campus_accreditation#", $ds_direccion_acreditation_pdf, $cadena);

    $cadena = str_replace("#ptib_approval#", $label_ptib_approval, $cadena);
    $cadena = str_replace("#yes_no_approval#", $yes_no_approval, $cadena);

    /*# Si el curso es typo 4 deberan ser 3 contratos  uno por cada anio
    # todo esto como lo dice PCTIA que es mutl anios
    if($cl_type==4) //modifica
    {
      switch($no_contrato)
      {
        case 1:
          $no_semanas_i = 0;
          $no_semanas_f = 52;
        break;
        case 2:
          $no_semanas_i = 52;
          $no_semanas_f = 104;
        break;
        case 3:
          $no_semanas_i = 104;
          $no_semanas_f = $no_semanas;
        break;
      }
    }
    else
    {
      # En caso de que el curso dure mas de 18 meses y menos que 24 meses
      # Entonces se enviaran 2 contratos uno por anio
      if($no_semanas>78 AND $no_semenas<104){
        switch($no_contrato){
        case 1:
          $no_semanas_i = 0;
          $no_semanas_f = 52;
        break;
        case 2:
          $no_semanas_i = 52;
          $no_semanas_f = $no_semanas;
        break;
      }

      }
      else{ # Si es curso dura menos de 18 meses entonces solo se enviara contrato
        $no_semanas_i = 0;
        $no_semanas_f = $no_semanas;
      }
    }*/
    $no_semanas_f = $no_semanas;
    $cadena = str_replace("#pg_stdate#", "" . $nb_periodo, $cadena); #Program start date
    # Si existe ya un fecha final la obtendremos de la BD
    $Queryf = "SELECT fe_completado FROM k_pctia WHERE fl_alumno=$fl_alumno";
    $rowf = RecuperaValor($Queryf);
    if (!empty($rowf[0]))
        $fecha_final = date('M j, Y', strtotime($rowf[0]));
    else {
        # Buscamos los breaks que existen entre la fecha_inicio y fecha_final y obtenemos el total de dias
        # para despues sumarlos en la fecha final
        $Query = "SELECT SUM(no_days) no_dias FROM c_break WHERE fe_ini  BETWEEN '" . date("Y-m-d", strtotime("$nb_periodo")) . "'  ";
        $Query .= "AND '" . date("Y-m-d", strtotime("$nb_periodo + $no_semanas_f weeks")) . "' ";
        $row = RecuperaValor($Query);
        $no_dias = $row[0];
        # Si el no_dias es mayor a cero los sumara a la fecha final
        if (!empty($no_dias))
            $fecha_final = date("M j, Y", strtotime("$nb_periodo + $no_semanas_f weeks " . ($no_dias + 1) . " days"));
        else
            $fecha_final = date("M j, Y", strtotime("$nb_periodo + $no_semanas_f weeks 1 day"));
    }
    $cadena = str_replace("#pg_edate#", "" . $fecha_final, $cadena); #Program end date
    $cadena = str_replace("#pg_credential#", "" . $ds_credential, $cadena); #Program credential diploma
    # Fechas inciales contratos
    # Sumamos los meses de un contrato anterior
    if ($no_semanas <= ObtenConfiguracion(92)) {
        $fe_start_contrato = $nb_periodo; # Fecha inicial del contrato
        $fe_end_contrato1 = date("M j, Y", strtotime("$nb_periodo + " . $no_weeks_contrato . " weeks "));
        $Query_c = "SELECT SUM(no_days) no_dias FROM c_break WHERE fe_ini  BETWEEN '" . date("Y-m-d", strtotime("$fe_start_contrato")) . "'  ";
        $Query_c .= "AND '" . date("Y-m-d", strtotime("$fe_end_contrato1")) . "' ";
        $rowc = RecuperaValor($Query_c);
        $no_dias_contrato = $rowc[0];

        $fe_end_contrato2 = date("M j, Y", strtotime("" . date("Y-m-d", strtotime("$nb_periodo + " . $no_weeks_contrato . " weeks ")) . " + " . ($no_dias_contrato + 1) . " days "));
        if (empty($no_dias_contrato))
            $fe_end_contrato = $fe_end_contrato1;
        else
            $fe_end_contrato = $fe_end_contrato2;
        $Query2 = "SELECT SUM(no_days) no_dias FROM c_break WHERE fe_ini  BETWEEN '" . date("Y-m-d", strtotime("$fe_start_contrato")) . "'  ";
        $Query2 .= "AND '" . date("Y-m-d", strtotime("$fe_end_contrato")) . "' ";
        $row2 = RecuperaValor($Query2);
        $no_dias2 = $row2[0];
        $fe_end_contrato3 = date("M j, Y", strtotime("$fe_start_contrato + $no_weeks_contrato weeks + $no_dias2 days"));
        if ($no_dias2 == $no_dias_contrato)
            $fe_end_contrato = $fe_end_contrato2;
        else
            $fe_end_contrato = $fe_end_contrato3;
    } else {
        if ($no_contrato == 1) {
            $fe_start_contrato = $nb_periodo;
            $fe_end_contrato = date("Y-m-d", strtotime("$nb_periodo + $no_weeks_contrato  weeks"));
            $fe_end_contrato1 = date("M j, Y", strtotime("$nb_periodo + $no_weeks_contrato  weeks"));
            $Query_c = "SELECT SUM(no_days) no_dias FROM c_break WHERE fe_ini  BETWEEN '" . date("Y-m-d", strtotime("$fe_start_contrato")) . "'  ";
            $Query_c .= "AND '" . $fe_end_contrato . "' ";
            $rowc = RecuperaValor($Query_c);
            $no_dias_contrato = $rowc[0];
            $fe_end_contrato2 = date("M j, Y", strtotime("" . date("Y-m-d", strtotime("$nb_periodo + " . $no_weeks_contrato . " weeks ")) . " + " . $no_dias_contrato . " days"));
            if (empty($no_dias_contrato))
                $fe_end_contrato = $fe_end_contrato1;
            else
                $fe_end_contrato = $fe_end_contrato2;
            $Query2 = "SELECT SUM(no_days) no_dias FROM c_break WHERE fe_ini  BETWEEN '" . date("Y-m-d", strtotime("$fe_start_contrato")) . "'  ";
            $Query2 .= "AND '" . date("Y-m-d", strtotime("$fe_end_contrato")) . "' ";
            $row2 = RecuperaValor($Query2);
            $no_dias2 = $row2[0];
            $fe_end_contrato3 = date("Y-m-d", strtotime("$fe_start_contrato + $no_weeks_contrato weeks + $no_dias2 days"));
            if ($no_dias2 == $no_dias_contrato)
                $fe_end_contrato = $fe_end_contrato2;
            else
                $fe_end_contrato = $fe_end_contrato3;

            $fe_end_contrato = date("M j, Y", strtotime("$fe_end_contrato"));
        } else {
            # Obtenemos las semanas de las BD y los breaks
            $rw = RecuperaValor("SELECT SUM(no_weeks), SUM(mn_payment_due) FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato<$no_contrato");
            $weeks_bd = $rw[0];
            $mn_payment_duee = $rw[1];
            $fe_start_contrato1 = $nb_periodo;
            $fe_end_contrato1 = date("Y-m-d", strtotime("$nb_periodo + $weeks_bd weeks"));
            # Apartir de la fecha inicial le sumamos las semanas de los contratos anteriores
            $Query = "SELECT SUM(no_days) no_dias FROM c_break WHERE fe_ini  BETWEEN '" . date("Y-m-d", strtotime("$fe_start_contrato1")) . "'  ";
            $Query .= "AND '" . $fe_end_contrato1 . "' ";
            $row = RecuperaValor($Query);
            $no_dias1 = $row[0];
            # primera vuelta para obtener la fecha inicial donde inciara el siguiente contrato   semanas mas breaks
            $fe_start_contrato2 = date("Y-m-d", strtotime("$fe_start_contrato1 + $weeks_bd weeks + $no_dias1 days"));
            if ($no_dias1 > 0)
                $fe_start_contrato = $fe_end_contrato1;
            else
                $fe_start_contrato = $fe_start_contrato2;
            # Damos otra vuelta por si no cayo en un break
            $Query2 = "SELECT SUM(no_days) no_dias FROM c_break WHERE fe_ini  BETWEEN '" . date("Y-m-d", strtotime("$fe_start_contrato1")) . "'  ";
            $Query2 .= "AND '" . $fe_start_contrato2 . "' ";
            $row2 = RecuperaValor($Query2);
            $no_dias2 = $row2[0];
            $fe_start_contrato3 = date("Y-m-d", strtotime("$fe_start_contrato1 + $weeks_bd weeks + $no_dias2 days"));
            if ($no_dias2 == $no_dias1)
                $fe_start_contrato = $fe_start_contrato2;
            else
                $fe_start_contrato = $fe_start_contrato3;

            $fe_start_contrato = date("M j, Y", strtotime("$fe_start_contrato + 1 days"));

            # Apartir de aqui buscamos la final final del contrato
            # Primer recorrido
            $fe_end_contrato1 = date("Y-m-d", strtotime("$fe_start_contrato + $no_weeks_contrato weeks"));
            $Query = "SELECT SUM(no_days) no_dias FROM c_break WHERE fe_ini  BETWEEN '" . date("Y-m-d", strtotime("$fe_start_contrato")) . "'  ";
            $Query .= "AND '" . $fe_end_contrato1 . "' ";
            $row = RecuperaValor($Query);
            $no_dias1 = $row[0];

            $fe_end_contrato2 = date("Y-m-d", strtotime("$fe_start_contrato + $no_weeks_contrato weeks + $no_dias1 days"));
            if ($no_dias1 == 0)
                $fe_end_contrato = $fe_end_contrato1;
            else
                $fe_end_contrato = $fe_end_contrato2;
            # Iniciamos el segundo recorrido
            $Query2 = "SELECT SUM(no_days) no_dias FROM c_break WHERE fe_ini  BETWEEN '" . date("Y-m-d", strtotime("$fe_start_contrato")) . "'  ";
            $Query2 .= "AND '" . $fe_end_contrato . "' ";
            $row2 = RecuperaValor($Query2);
            $no_dias2 = $row2[0];
            $fe_end_contrato3 = date("Y-m-d", strtotime("$fe_start_contrato + $no_weeks_contrato weeks + $no_dias2 days"));
            if ($no_dias2 == $no_dias1)
                $fe_end_contrato = $fe_end_contrato2;
            else
                $fe_end_contrato = $fe_end_contrato3;
            $fe_end_contrato = date("M j, Y", strtotime("$fe_end_contrato"));
        }
    }
    # Obtenemos lo que ya pago y el total
    $tut_paid_contract = $mn_payment_duee;
    if (empty($tut_paid_contract))
        $tut_paid_contract = 0;
    $tot_balance_contract = $mn_x_paid - $tut_paid_contract;

    # Montos por contrato
    $mn_payment_due = number_format($mn_payment_due, 2, '.', ',');
    $tut_paid_contract = number_format($tut_paid_contract, 2, '.', ',');
    $tot_balance_contract = number_format($tot_balance_contract, 2, '.', ',');

    # Si no ha firmado no se mostrara estos datos
    if ($no_contrato == 1 and empty($ds_firma_alumno)) {
        $mn_payment_due = ObtenEtiqueta(905);
        $tut_paid_contract = ObtenEtiqueta(905);
        $tot_balance_contract = ObtenEtiqueta(905);
    }

    switch ($cl_delivery) {
        case 'O':
            $ds_tipo = "Online";
            break;
        case 'S':
            $ds_tipo = "On-Site";
            break;
        case 'C':
            $ds_tipo = "Combined";
            break;
    }


    if ($fg_payment == 'C')
        $ds_tipo = "Combined";
    if ($fg_payment == 'O')
        $ds_tipo = "Online";


    $cadena = str_replace("#pg_delivery#", "" . $ds_tipo, $cadena); #Program delivery on-site
    $cadena = str_replace("#pg_language#", "" . $ds_language, $cadena); #Program language
    $cadena = str_replace("#pg_appfee#", $symbol . "" . number_format($app_fee, 2), $cadena); #Program application fee
    $cadena = str_replace("#pg_tuition#", $symbol . "" . number_format($tuition, 2), $cadena); #Program tuition
    $cadena = str_replace("#pg_ds_other_cost#", "" . $ds_costos_ad, $cadena); #Program other cost description
    $cadena = str_replace("#pg_other_cost#", $symbol . "" . number_format($no_costos_ad, 2), $cadena); #Program other cost
    $cadena = str_replace("#pg_ds_cost_discount#", "" . $ds_descuento, $cadena); #Program discount description.
    $cadena = str_replace("#pg_cost_discount#", $symbol . "" . number_format($no_descuento, 2), $cadena); #Program discount.
    $cadena = str_replace("#pg_total_tuition#", $symbol . "" . number_format($mn_tot_tuition, 2, '.', ''), $cadena); #Program total tuition cost
    $cadena = str_replace("#pg_total_cost#", $symbol . "" . number_format($mn_tot_program, 2), $cadena); #Program total cost
    # Obtendremos el app fee tax y el tuition app fee tax
    # si el aplicante es de canada y el  programa requiere tax rate
    $app_fee_tax = 0;
    $tuition_fee_tax = 0;



    # Si el usuario es canadiense se cobra TAX
    if ($fl_pais == 38 and (!empty($fg_tax_rate) || empty($fg_tax_rate))) {
        // if($fl_pais == 38 AND !empty($fg_tax_rate)){
        if (!empty($ds_add_state)) {
            $row_tax = RecuperaValor("SELECT ds_abreviada,mn_tax FROM k_provincias WHERE fl_provincia='$ds_add_state'");
            $ds_abreviada = $row_tax[0];
            $mn_tax_rate = $row_tax[1];
            $app_fee_tax = $app_fee * ($mn_tax_rate / 100);
            # Si el pago es App + Curso se obtiene el tax del curso
            # Si el curso es largo solo se cobra el tax del App y el tax del Curso es 0
            if (!empty($fg_total_programa)) {
			
				#validate learners obligatorios con descuento fl_sesion
				if($clave==3331000)
				{	
					$tuition_fee_tax = 0;
					$app_fee_tax=0;
					
				}else{
					$tuition_fee_tax = $tuition*($mn_tax_rate/100);
				}
				
			
			} else {
				
				if($clave==3331000)
				{	
					$app_fee_tax = 0;
				}
				
                $tuition_fee_tax = 0;
            }

            if ((empty($tax_mn_cost)) || ($tax_mn_cost == 0)) {
                #tax FAME.
                if (($ds_costos_ad == "VANAS+ Learning Resources") || ($ds_costos_ad == "VANAS Plus Learning Resources") || ($ds_costos_ad == "VANAS+ Learning Resources")) {
                    $tax_mn_cost = $no_costos_ad * ($mn_tax_rate / 100);
                }
				
				#validate learners obligatorios con descuento fl_sesion
				if($clave==3331000)
				{
					  $tax_mn_cost = 0;
				
				}

            }



        }
    }

    #verifica si tiene tax la BD
    if ((!empty($opc_pago))&&($mn_payment_due<>"No payment option has been selected")) {

        $mn_payment_due = str_replace(",", "", $mn_payment_due);
        $tot_balance_contract = str_replace(",", "", $tot_balance_contract);

        if ($mn_payment_due >= 0) {
            $mn_payment_due = $mn_payment_due + $tax_mn_cost;
            $tut_paid_contract = $mn_payment_due ;
        }
        if ($tot_balance_contract >= 0) {
            $tot_balance_contract = $tot_balance_contract + $tax_mn_cost;
        }
    }

    # fechas inicial y final de cada contrato
    $cadena = str_replace("#start_date_contract#", $fe_start_contrato, $cadena); # Fecha Inicial del contrato
    $cadena = str_replace("#end_date_contract#", $fe_end_contrato, $cadena); # Fecha Final del contrato
    $cadena = str_replace("#payment_due_contract#", $mn_payment_due, $cadena); # Payment Due del contrato
    $cadena = str_replace("#tut_paid_contract#", $tut_paid_contract, $cadena); # Tuition paid to date del contrato
    $cadena = str_replace("#tot_balance_contract#", $tot_balance_contract, $cadena); # Total balance to date del contrato






    # Realizamos la suma total que pagara app fee tax mas tuition fee tax y el costo del programa
    $total_costs = $mn_tot_program + $app_fee_tax + $tuition_fee_tax + $tax_mn_cost;
    # Remplazamos los valores del app fee tax y el tuition fee tax
    $cadena = str_replace("#app_fee_tax#", $symbol . "" . number_format($app_fee_tax, 2), $cadena); #App fee tax
    $cadena = str_replace("#tuition_fee_tax#", $symbol . "" . number_format($tuition_fee_tax, 2), $cadena); #Tuition fee tax
    $cadena = str_replace("#total_costs#", $symbol . "" . number_format($total_costs, 2), $cadena); # Total costs
    $cadena = str_replace("#fame_tax#", $symbol . "" . number_format($tax_mn_cost, 2), $cadena); #fame taxes

    $tax_mn_cost_x_invoice_a = 0;
    $tax_mn_cost_x_invoice_b = 0;
    $tax_mn_cost_x_invoice_c = 0;

    $tax_mn_cost_x_invoice_b_paid = 0;
    $tax_mn_cost_x_invoice_c_paid = 0;


    if (($ds_costos_ad == "VANAS+ Learning Resources") || ($ds_costos_ad == "VANAS Plus Learning Resources") || ($ds_costos_ad == "VANAS+ Learning Resources")) {
        $tax_mn_cost_x_invoice_a = $tax_mn_cost;
        $tax_mn_cost_x_invoice_b = $tax_mn_cost / 2;
        $tax_mn_cost_x_invoice_c = $tax_mn_cost / 4;

        $tax_mn_cost_x_invoice_b_paid = $tax_mn_cost;
        $tax_mn_cost_x_invoice_c_paid = $tax_mn_cost;

    }




    switch ($opc_pago) {
        case 1:
            $opc_a = "X";
            $opc_b = "";
            $opc_c = "";
            $opc_d = "";

            break;
        case 2:
            $opc_a = "";
            $opc_b = "X";
            $opc_c = "";
            $opc_d = "";

            break;
        case 3:
            $opc_a = "";
            $opc_b = "";
            $opc_c = "X";
            $opc_d = "";

            break;
        case 4:
            $opc_a = "";
            $opc_b = "";
            $opc_c = "";
            $opc_d = "X";
            break;
    }
    $cadena = str_replace("#py_optionA#", "" . $opc_a, $cadena); #Payment option A.
    $cadena = str_replace("#py_optionB#", "" . $opc_b, $cadena); #Payment option B.
    $cadena = str_replace("#py_optionC#", "" . $opc_c, $cadena); #Payment option C.
    $cadena = str_replace("#py_optionD#", "" . $opc_d, $cadena); #Payment option D.
    $cadena = str_replace("#py_paymentsA#", "" . $no_a_payments, $cadena); #Number of payments option A.
    $cadena = str_replace("#py_paymentsB#", "" . $no_b_payments, $cadena); #Number of payments option B.
    $cadena = str_replace("#py_paymentsC#", "" . $no_c_payments, $cadena); #Number of payments option C.
    $cadena = str_replace("#py_paymentsD#", "" . $no_d_payments, $cadena); #Number of payments option D.
    $cadena = str_replace("#py_freqA#", "" . $ds_a_freq, $cadena); #Frequency Payment option A.
    $cadena = str_replace("#py_freqB#", "" . $ds_b_freq, $cadena); #Frequency Payment option B.
    $cadena = str_replace("#py_freqC#", "" . $ds_c_freq, $cadena); #Frequency Payment option C.
    $cadena = str_replace("#py_freqD#", "" . $ds_d_freq, $cadena); #Frequency Payment option D.
    $cadena = str_replace("#py_dueoptionA#", $symbol . "" . number_format(($amount_due_a + $tax_mn_cost_x_invoice_a), 2), $cadena); #Payment Amount Due option A
    $cadena = str_replace("#py_dueoptionB#", $symbol . "" . number_format(($amount_due_b + $tax_mn_cost_x_invoice_b), 2), $cadena); #Payment Amount Due option B
    $cadena = str_replace("#py_dueoptionC#", $symbol . "" . number_format(($amount_due_c + $tax_mn_cost_x_invoice_c), 2), $cadena); #Payment Amount Due option C
    $cadena = str_replace("#py_dueoptionD#", $symbol . "" . number_format($amount_due_d, 2), $cadena); #Payment Amount Due option D
    $cadena = str_replace("#py_paidoptionA#", $symbol . "" . number_format(($amount_paid_a + $tax_mn_cost_x_invoice_a), 2), $cadena); #Payment Amount Paid option A
    $cadena = str_replace("#py_paidoptionB#", $symbol . "" . number_format(($amount_paid_b + $tax_mn_cost_x_invoice_b_paid), 2), $cadena); #Payment Amount Paid option B
    $cadena = str_replace("#py_paidoptionC#", $symbol . "" . number_format(($amount_paid_c + $tax_mn_cost_x_invoice_c_paid), 2), $cadena); #Payment Amount Paid option C
    $cadena = str_replace("#py_paidoptionD#", $symbol . "" . number_format($amount_paid_d, 2), $cadena); #Payment Amount Paid option D
    if ($firma)
        $fecha = date("M j, Y");
    else {
        if (!empty($fe_firma))
            $fecha = date("M j, Y", strtotime("$fe_firma"));
        else
            $fecha = "";
    }

    if (empty($ds_firma_padre)) {

        $Query_res = "SELECT ds_fname_r, ds_lname_r, ds_email_r, ds_aemail_r, ds_pnumber_r, ds_relation_r, fg_email ";
        $Query_res .= "FROM k_presponsable WHERE cl_sesion='$cl_sesion'";
        $row_res = RecuperaValor($Query_res);
        $ds_fname_r = str_ascii($row_res[0]);
        $ds_lname_r = str_ascii($row_res[1]);
        $ds_firma_padre = $ds_fname_r . " " . $ds_lname_r;

    }


    $cadena = str_replace("#st_signaturedt#", "" . $fecha, $cadena); #Electronic student signature date
    $cadena = str_replace("#st_signature#", "" . $ds_firma_alumno, $cadena); #Electronic student signature date
    $cadena = str_replace("#st_lg_signature#", "" . $ds_firma_padre, $cadena); #Electronic legal guardian signature
    if (!empty($ds_firma_padre))
        $fecha_papa = $fecha;
    else
        $fecha_papa = '';
    $cadena = str_replace("#st_lg_signaturedt#", "" . $fecha_papa, $cadena); #Electronic legal guardian signature date
    $fe_mod = date("M j, Y", strtotime("$fe_modificacion"));
    $cadena = str_replace("#con_mod_date#", "" . $fe_mod, $cadena); #Contract template modification date

    /* JGFL 170601
    # Obtenemos la fecha que se envio el correo. Si no se ha enviado, se muestra fecha actual
    if(ExisteEnTabla('k_alumno_template','fl_template',$fl_template, 'fl_alumno', $clave,True))
      $row = RecuperaValor("SELECT DATE_FORMAT(fe_envio,'%M-%d-%Y') fe_envio_template, DATE_FORMAT(DATE_ADD(fe_envio, INTERVAL ".ObtenConfiguracion(89)." DAY),'%Y/%m/%d') fe_expiration FROM k_alumno_template WHERE fl_alumno=$clave AND fl_template=$fl_template");
    else*/
    $roww = RecuperaValor("SELECT DATE_FORMAT(NOW(),'%M-%d-%Y'), DATE_FORMAT(DATE_ADD(NOW(), INTERVAL " . ObtenConfiguracion(89) . " DAY),'%Y/%m/%d') fe_expiration");
    $fe_envio_template = $roww[0];
    $fe_expiration = $roww[1];
    $cadena = str_replace("#sent_date#", $fe_envio_template, $cadena); #Fecha de envio del template o correo
    $cadena = str_replace("#fe_expiration#", $fe_expiration, $cadena); #Days expiration of letter of acceptance

    # Obtenemos el fl_alumno mediante el cl_sesion
    $rowst = RecuperaValor("SELECT fl_usuario FROM c_usuario WHERE cl_sesion='$cl_sesion'");
    $fl_alumno = $rowst[0];

    $rowsa = RecuperaValor("SELECT notation_transcript FROM c_alumno WHERE fl_alumno=$fl_alumno ");
    $ds_notation = ($fl_template == 194) ? null : $rowsa['notation_transcript'];


    $cadena = str_replace("#ds_notation#", $ds_notation, $cadena); ##notation diplomas and transcripts

    if ((empty($ds_notation)) && $fl_template == 194) { //diploma
        $cadena = str_replace("Notation:", "", $cadena);
    }



    # Obtenemos el promedio general del curso
    $QueryGPA = "SELECT (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t)), ";
    $QueryGPA .= "no_promedio_t FROM c_alumno WHERE fl_alumno=$fl_alumno ";
    $row2 = RecuperaValor($QueryGPA);
    $gpa_grl = $row2[0] . " " . round($row2[1]) . "%";
    //$row3 = RecuperaValor("");
    if (empty($gpa_grl))
        $gpa_grl = "(No assigment)";
    # Remplazamos el caracter del grado actual y el promedio general
    $rowterm = RecuperaValor("SELECT MAX(fl_term) FROM k_alumno_term WHERE fl_alumno=$fl_alumno");
    $fl_term_actual = $rowterm[0];
    /*$rowgrado = RecuperaValor("SELECT no_grado FROM k_term WHERE fl_term=$fl_term_actual");*/
    $rowgrado = RecuperaValor("SELECT c.no_grado FROM k_alumno_grupo a, c_grupo b, k_term c
                           WHERE  a.fl_grupo=b.fl_grupo AND b.fl_term=c.fl_term AND a.fl_alumno =$fl_alumno ");
    $no_grado = $rowgrado[0];
    $cadena = str_replace("#no_grado#", $no_grado, $cadena); # No de grado actual del alumno
    $cadena = str_replace("#program_gpa#", $gpa_grl, $cadena); # Promedio general del alumno

    # Obtenemos la calificacion del term
    $QueryTerm = "SELECT (SELECT cl_calificacion FROM c_calificacion WHERE no_min <=ROUND(no_promedio) AND no_max >=ROUND(no_promedio)), ROUND(no_promedio) ";
    $QueryTerm .= "FROM k_alumno_term WHERE fl_term=$fl_term_actual AND fl_alumno=$fl_alumno";
    $rowc = RecuperaValor($QueryTerm);
    $cl_cal_term = $rowc[0];
    $current_term_promedio = $rowc[1];
    if (empty($current_term_promedio))
        $current_term_promedio = "0";
    $current_term_gpa = $cl_cal_term . " " . round($current_term_promedio) . "%";

    if ((empty($current_term_promedio)) && (!empty($gpa_grl)))
        $current_term_gpa = $gpa_grl;


    # Remplazamos el caracter de la calificacion del term actual
    $cadena = str_replace("#current_term_gpa#", $current_term_gpa, $cadena);

    # Obtenemos la calificacion de la ultima semana
    $rowgrupo = RecuperaValor("SELECT fl_grupo FROM k_alumno_grupo WHERE fl_alumno=$fl_alumno");
    $fl_grupo_actual = $rowgrupo[0];
    if (empty($fl_grupo_actual)) {
        $rowgrupo1 = RecuperaValor("SELECT fl_grupo FROM c_grupo WHERE fl_term=$fl_term_actual");
        $fl_grupo_actual = $rowgrupo1[0];
    }
    #  Calificacion de la semana actual lo dejo por cualquier otra cosa
    /*$Querys  = "SELECT cl_calificacion, no_equivalencia, a.fl_semana ";
    $Querys .= "FROM k_entrega_semanal a, c_calificacion b ";
    $Querys .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
    $Querys .= "AND fl_semana=(SELECT MAX(fl_semana) FROM k_entrega_semanal ";
    $Querys .= "WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo_actual AND fl_promedio_semana IS NOT NULL) ";
    $Querys .= "AND fl_alumno=$fl_alumno ";
    $Querys .= "AND fl_grupo=$fl_grupo_actual ";
    $Querys .= "AND fl_promedio_semana IS NOT NULL";
    $rows = RecuperaValor($Querys);
    $semana_act = $rows[0]." ".$rows[1]." %";
    $cadena = str_replace("#current_week_grade#", $semana_act, $cadena);*/
    $Querysem = "SELECT i.fl_semana, i.fl_promedio_semana FROM k_entrega_semanal i WHERE i.fl_semana = ( ";
    $Querysem .= "SELECT MAX(a.fl_semana) FROM k_semana a, k_entrega_semanal b ";
    $Querysem .= "WHERE a.fl_semana=b.fl_semana AND fl_term=$fl_term_actual AND b.fl_grupo=$fl_grupo_actual AND b.fl_alumno=$fl_alumno ";
    $Querysem .= "AND  b.fl_promedio_semana>=1 ORDER BY a.fl_semana) AND i.fl_grupo=$fl_grupo_actual AND i.fl_alumno=$fl_alumno ";
    $Querysem .= "AND i.fl_promedio_semana>=1 ";
    $rowsem = RecuperaValor($Querysem);
    $fl_semana_actual = $rowsem[0];
    $fl_promedio_semana = $rowsem[1];

    # semana actual
    $rowsem = RecuperaValor("SELECT no_semana FROM k_semana a, c_leccion b WHERE a.fl_semana=$fl_semana_actual AND a.fl_leccion=b.fl_leccion");
    $no_semana = $rowsem[0];
    $cadena = str_replace("#no_week#", $no_semana, $cadena);

    # Current grade week
    $rowweek = RecuperaValor("SELECT cl_calificacion, no_equivalencia FROM c_calificacion WHERE fl_calificacion=$fl_promedio_semana");
    $current_grade_week = $rowweek[0] . " " . round($rowweek[1]) . "%";
    $cadena = str_replace("#current_week_grade#", $current_grade_week, $cadena);

    # Calificacion Minima aprovada
    $reprovada = "SELECT cl_calificacion, no_min FROM c_calificacion ";
    $reprovada .= "WHERE no_equivalencia=(SELECT MIN(no_equivalencia) FROM c_calificacion WHERE fg_aprobado='1') ";
    $rowr = RecuperaValor($reprovada);
    $cl_calificacion = $rowr[0];
    $no_equivalencia = round($rowr[1]);
    $calificacion_min = $cl_calificacion . " " . $no_equivalencia . "%";
    $cadena = str_replace("#minimum_gpa#", $calificacion_min, $cadena);

    # Add data person responsible
    $Query_res = "SELECT ds_fname_r, ds_lname_r, ds_email_r, ds_aemail_r, ds_pnumber_r, ds_relation_r, fg_email ";
    $Query_res .= "FROM k_presponsable WHERE cl_sesion='$cl_sesion'";
    $row_res = RecuperaValor($Query_res);
    $ds_fname_r = str_ascii($row_res[0]);
    $ds_lname_r = str_ascii($row_res[1]);
    $ds_email_r = str_ascii($row_res[2]);
    $ds_aemail_r = str_ascii($row_res[3]);
    $ds_pnumber_r = str_ascii($row_res[4]);
    $ds_relation_r = str_ascii($row_res[5]);
    $fg_email = $row_res[6];
    # Remplazamos las variables por los datos
    $cadena = str_replace("#ds_fname_r#", $ds_fname_r, $cadena);
    $cadena = str_replace("#ds_lname_r#", $ds_lname_r, $cadena);
    $cadena = str_replace("#ds_email_r#", $ds_email_r, $cadena);
    $cadena = str_replace("#ds_aemail_r#", $ds_aemail_r, $cadena);
    $cadena = str_replace("#ds_pnumber_r#", $ds_pnumber_r, $cadena);
    $cadena = str_replace("#ds_relation_r#", $ds_relation_r, $cadena);

    ##### Historial de las faltas del alumno #####
    $Query5 = "SELECT no_semana no_semanaa, c.ds_titulo ds_tituloo, DATE_FORMAT(a.fe_clase, '%W') fe_dayy, DATE_FORMAT(a.fe_clase, '%M %e, %Y') fe_datee, ";
    $Query5 .= "DATE_FORMAT(a.fe_clase, '%h:%i %p') fe_timee, fl_clase fl_clasee FROM k_clase a ";
    $Query5 .= "LEFT JOIN k_semana b ON a.fl_semana = b.fl_semana LEFT JOIN c_leccion c ON b.fl_leccion = c.fl_leccion ";
    $Query5 .= "WHERE fl_grupo=$fl_grupo_actual AND fg_obligatorio='1'";
    $rs5 = EjecutaQuery($Query5);
    $absences = 0;
    $absences_class = "";
    while ($row5 = RecuperaRegistro($rs5)) {
        $no_semanaa = $row5[0];
        $ds_tituloo = $row5[1];
        $fe_dayy = $row5[2];
        $fe_datee = $row5[3];
        $fe_timee = $row5[4];
        $fl_clasee = $row5[5];

        # buscamos las faltas que ha tenido el alumno
        $Query6 = "SELECT cl_estatus_asistencia FROM k_live_session a LEFT JOIN k_live_session_asistencia b ON a.fl_live_session = b.fl_live_session ";
        $Query6 .= "WHERE a.fl_clase=$fl_clasee AND fl_usuario=$fl_alumno";
        $row6 = RecuperaValor($Query6);
        $cl_estatus_asistenciaa = !empty($row6[0]) ? $row6[0] : NULL;
        if (empty($cl_estatus_asistenciaa) or $cl_estatus_asistenciaa != 2) {
            $absences++;
            $absences_class = $absences_class . "Week $no_semanaa: $ds_tituloo Date on: $fe_dayy, $fe_datee, $fe_timee <br>";
        }
    }
    $cadena = str_replace("#missed_class_term_history#", $absences_class, $cadena);

    return ($cadena);
}

# Ejecuta una consulta, para usar cuando se espera mas de 1 resultado
function EjecutaQuery($p_query)
{
    if(!empty($p_query)){

        $db = ConectaBD();
        $rs = $db->Execute($p_query);
        return $rs;

    }
}

# Ejecuta una consulta regresando el ultimo valor de la clave insertada
function EjecutaInsert($p_query)
{

  $db = ConectaBD();
  $rs = $db->Execute($p_query);
  if (DATABASE_TYPE == DATABASE_SLQSERVER)
    $rs = $db->Execute("SELECT @@IDENTITY");
  if (DATABASE_TYPE == DATABASE_MYSQL)
    $rs = $db->Execute("SELECT LAST_INSERT_ID()");
  $row = $rs->FetchRow();
  return $row[0];
}

# Ejecuta una consulta, para usar cuando se espera mas de 1 resultado, trayendo unicamente una pagina
function EjecutaQueryLimit($p_query, $p_total, $p_inicio)
{

  $db = ConectaBD();
  $rs = $db->SelectLimit($p_query, $p_total, $p_inicio);
  return $rs;
}

# Regresa un arreglo con los campos del siguiente registro, para usar en consultas con mas de 1 resultado
function RecuperaRegistro($p_rs)
{

  if (!empty($p_rs))
    $row = $p_rs->FetchRow();
  return !empty($row)?$row:NULL;
}

# Regresa un arreglo con los campos del registro, para usar en consultas que recuperan un solo resultado
function RecuperaValor($p_query)
{

  $rs = EjecutaQuery($p_query);
  $row = RecuperaRegistro($rs);
  return $row;
}

# Cuenta registros de un cursor
function CuentaRegistros($p_rs)
{

  if ($p_rs)
    $no_regs = $p_rs->RecordCount();
  if (empty($no_regs))
    $no_regs = 0;
  return $no_regs;
}

# Cuenta campos de un cursor
function CuentaCampos($p_rs)
{

  if ($p_rs)
    $no_campos = $p_rs->FieldCount();
  if (empty($no_campos))
    $no_campos = 0;
  return $no_campos;
}

# Cuenta campos de un cursor
function NombreCampo($p_rs, $p_cual, $p_sin_centrado = False)
{

  if ($p_rs)
    $campos = $p_rs->FetchField($p_cual);
  if ($campos)
    $nombre = $campos->name;
  else
    $nombre = "";

  # Elimina alineacion del encabezado
  if ($p_sin_centrado) {
    $nombre = str_replace('|hidden', '', $nombre);
    $nombre = str_replace('|left', '', $nombre);
    $nombre = str_replace('|center', '', $nombre);
    $nombre = str_replace('|right', '', $nombre);
  }

  return $nombre;
}

# Funcion para verificar si existen registros en la tabla para un campo llave y valor dados
function ExisteEnTabla($p_tabla, $p_campo, $p_valor, $p_clave = '', $p_valor_clave = '', $p_igual = False)
{

  if (empty($p_tabla) or empty($p_campo) or empty($p_valor))
    return False;

  $Query  = "SELECT count(1) FROM $p_tabla WHERE $p_campo='$p_valor' ";
  if (!empty($p_clave) and !empty($p_valor_clave)) {
    $Query .= "AND $p_clave";
    if ($p_igual)
      $Query .= " = ";
    else
      $Query .= " <> ";
    $Query .= "$p_valor_clave";
  }
  $row = RecuperaValor($Query);
  if ($row[0] > 0)
    return True;
  else
    return False;
}

# Exporta una consulta a CVS
function ExportaQuery($p_nom_arch, $p_query)
{

  # Abre archivo de salida
  if (!$archivo = fopen($_SERVER['DOCUMENT_ROOT'] . $p_nom_arch, "wb")) {
    MuestraPaginaError(ERR_EXPORTAR);
    exit;
  }

  # Exporta los datos
  $rs = EjecutaQuery($p_query);
  $tot_campos = CuentaCampos($rs);
  for ($i = 1; $i < $tot_campos; $i++)
    fwrite($archivo, str_replace(",", " ", str_ascii(NombreCampo($rs, $i, True))) . ",");
  fwrite($archivo, "\n");
  while ($row = RecuperaRegistro($rs)) {
    for ($i = 1; $i < $tot_campos; $i++)
      fwrite($archivo, str_replace(",", " ", str_ascii(DecodificaEscogeIdiomaBD($row[$i]))) . ",");
    fwrite($archivo, "\n");
  }

  # Cierra el archivo
  fclose($archivo);
}

# Regresa dos campos separados por || para escoger idioma
function EscogeIdiomaBD($p_base, $p_trad)
{

  $concat = array($p_base, "'||'", NulosBD($p_trad));
  $campo = ConcatenaBD($concat);
  return $campo;
}

# Decodifica el resultado de EscogeIdiomaBD
function DecodificaEscogeIdiomaBD($p_base)
{

  $campo = $p_base;
  if ($lpos = strpos($campo, '||')) {
    $val1 = substr($campo, 0, $lpos);
    $val2 = substr($campo, $lpos + 2);
    $campo = EscogeIdioma($val1, $val2);
  }
  return $campo;
}


#
# Funciones de compatibilidad de Bases de Datos
#

function LengthBD($p_campo)
{

  $campo = $p_campo;
  if (DATABASE_TYPE == DATABASE_SLQSERVER)
    $campo = "LEN($p_campo)";
  if (DATABASE_TYPE == DATABASE_MYSQL)
    $campo = "LENGTH($p_campo)";
  return $campo;
}

function NulosBD($p_campo, $p_valor = '')
{

  $campo = $p_campo;
  if (DATABASE_TYPE == DATABASE_SLQSERVER)
    $campo = "ISNULL($p_campo, '$p_valor')";
  if (DATABASE_TYPE == DATABASE_MYSQL)
    $campo = "IFNULL($p_campo, '$p_valor')";
  return $campo;
}

function ConcatenaBD($p_concat = array())
{

  $campo = "";
  $tot = count($p_concat);
  if (DATABASE_TYPE == DATABASE_SLQSERVER) {
    $campo = "(" . $p_concat[0];
    for ($i = 1; $i < $tot; $i++)
      $campo .= " + " . $p_concat[$i];
    $campo .= ")";
  }
  if (DATABASE_TYPE == DATABASE_MYSQL) {
    $campo = "CONCAT(" . $p_concat[0];
    for ($i = 1; $i < $tot; $i++)
      $campo .= ", " . $p_concat[$i];
    $campo .= ")";
  }
  return $campo;
}

function ConsultaFechaBD($p_campo, $p_formato){

  $campo = $p_campo;
  if (DATABASE_TYPE == DATABASE_SLQSERVER) {
    switch ($p_formato) {
      case FMT_CAPTURA:
        $campo = "CONVERT(varchar, $p_campo, " . EscogeIdioma('105', '110') . ")";
        break;
      case FMT_FECHA:
        $campo = "CONVERT(varchar, $p_campo, " . EscogeIdioma('105', '110') . ")";
        break;
      case FMT_HORA:
        $campo = "CONVERT(varchar(8), $p_campo, 114)";
        break;
    }
  }
  if (DATABASE_TYPE == DATABASE_MYSQL) {
    switch ($p_formato) {
      case FMT_CAPTURA:
        $campo = "DATE_FORMAT($p_campo, '" . EscogeIdioma('%d-%m', '%m-%d') . "-%Y')";
        break;
      case FMT_FECHA:
        $campo = "DATE_FORMAT($p_campo, '" . EscogeIdioma('%d-%m', '%m-%d') . "-%Y')";
        break;
      case FMT_HORA:
        $campo = "DATE_FORMAT($p_campo, '%H:%i:%s')";
        break;
      case FMT_HORAMIN:
        $campo = "DATE_FORMAT($p_campo, '%H:%i')";
        break;
      case FMT_DATETIME:
        $campo = "DATE_FORMAT($p_campo, '" . EscogeIdioma('%d-%m', '%m-%d') . "-%Y %H:%i')";
        break;
    }
  }
  return $campo;
}

#
# MRA: Funciones para manejo de sesiones
#

# Crea o actualiza cookie con numero de sesion, expira en p_tiempo tiempo (en segundos), scope todo el sitio
# Se agrego un parametro cuando es self pace madara un True
function ActualizaSesion($p_sesion, $p_admin = True, $p_tiempo = 0, $p_self = False){

  # Tiempo de la sesion
  if (empty($p_tiempo)) {
    if (!empty($p_admin))
      $p_tiempo = SESION_VIGENCIA;
    else
      $p_tiempo = ObtenConfiguracion(42) * 60;
  }

  # Cookie de sesion
  if (!empty($p_admin))
    setcookie(SESION_ADMIN, $p_sesion, time() + $p_tiempo, "/");
  else {
    # Cookie entre campus y self pace
    if (empty($p_self))
      setcookie(SESION_CAMPUS, $p_sesion, time() + $p_tiempo, "/");
    else
      setcookie(SESION_SELF, $p_sesion, time() + $p_tiempo, "/");
  }

  EjecutaQuery("UPDATE c_usuario SET fe_sesion=CURRENT_TIMESTAMP WHERE cl_sesion='$p_sesion'");

  # Reinicializa cookie de idioma o establece el idioma por omision
  # Se comenta por uso de nuevo metodo de idiomas archivos locale
  // $cl_idioma = $_COOKIE[IDIOMA_NOMBRE];
  // if(!empty($cl_idioma))
  //   setcookie(IDIOMA_NOMBRE, $cl_idioma, time( )+IDIOMA_VIGENCIA, "/");
  // else
  //   setcookie(IDIOMA_NOMBRE, IDIOMA_DEFAULT, time( )+IDIOMA_VIGENCIA, "/");
  # Se comenta por uso de nuevo metodo de idiomas archivos locale
}

# Limpia cookie de sesion
# Se agrego un parametro cuando es self pace madara un True
function TerminaSesion($p_admin = True){

  $fl_usuario = ObtenUsuario($p_admin);
  # Obtenemos si es FAME o campus
  $row = RecuperaValor("SELECT fl_perfil_sp FROM c_usuario WHERE fl_usuario=$fl_usuario");
  $p_self = false;
  if (!empty($row[0]))
    $p_self = true;
  if (!$p_admin) {
    if (!$p_self) {
      $row = RecuperaValor("SELECT MAX(fl_usu_login) FROM k_usu_login WHERE fl_usuario=$fl_usuario");
      $fl_usu_login = $row[0]??NULL;
      if (!empty($fl_usu_login))
        EjecutaQuery("UPDATE k_usu_login SET fe_logout=CURRENT_TIMESTAMP WHERE fl_usuario=$fl_usuario AND fe_logout IS NULL");
      setcookie(SESION_CAMPUS, '', time() + SESION_VIGENCIA, "/");
      setcookie(SESION_RM, '', time() + SESION_VIGENCIA, "/");
    } else {
      $row = RecuperaValor("SELECT MAX(fl_usu_login_sp) FROM k_usu_login_sp WHERE fl_usuario_sp=$fl_usuario");
      $fl_usu_login = !empty($row[0])?$row[0]:NULL;
      if (!empty($fl_usu_login))
        EjecutaQuery("UPDATE k_usu_login_sp SET fe_logout=CURRENT_TIMESTAMP WHERE fl_usuario_sp=$fl_usuario AND fe_logout IS NULL");
      setcookie(SESION_SELF, '', time() + SESION_VIGENCIA, "/");
      setcookie(SESION_RM, '', time() + SESION_VIGENCIA, "/");
    }
  } else
    setcookie(SESION_ADMIN, '', time() + SESION_VIGENCIA, "/");
  EjecutaQuery("UPDATE c_usuario SET fe_sesion=NULL, fg_remember_me='0' WHERE fl_usuario='$fl_usuario'");
}

# Cambia el idioma
function CambiaIdioma()
{

  # Revisa el idioma seleccionado y lo invierte
  $cl_idioma = $_COOKIE[IDIOMA_NOMBRE];
  if ($cl_idioma == IDIOMA_ALTERNO || empty($cl_idioma))
    setcookie(IDIOMA_NOMBRE, IDIOMA_DEFAULT, time() + IDIOMA_VIGENCIA, "/");
  else
    setcookie(IDIOMA_NOMBRE, IDIOMA_ALTERNO, time() + IDIOMA_VIGENCIA, "/");
}

# Check if the request is made through ajax
function isAjax()
{
  $ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
  return $ajax;
}

# Verifica que una sesion es valida
# Se agrego un parametro cuando es self pace madara un True
function ValidaSesion($p_admin = True, $p_tiempo = 0, $p_self = False)
{

  # Lee la sesion del cookie
  if(!$p_admin) {
    $id_sesion = isset($_COOKIE[SESION_RM]) ? $_COOKIE[SESION_RM] : '';
    # Lee la sesion del cookie para self
    if ($p_self) {
      if (empty($id_sesion))
        $id_sesion = isset($_COOKIE[SESION_SELF]) ? $_COOKIE[SESION_SELF] : '';
    } else {
      if (empty($id_sesion))
        $id_sesion = isset($_COOKIE[SESION_CAMPUS]) ? $_COOKIE[SESION_CAMPUS] : '';
    }
  } else
    $id_sesion = isset($_COOKIE[SESION_ADMIN]) ? $_COOKIE[SESION_ADMIN] : '';

  # Valida si existe un identificador de sesion en el cookie
  if (empty($id_sesion)) {
    # -2: La sesi&oacute;n ha expirado.
    if(isAjax()){
      echo json_encode((Object) array('location' => SESION_EXPIRADA));
    } else {
      # Si viene de algu correo o algo externo al sistema y no tiene sesion recuperadas
      # envia como parametro el url donde deseaba ingresar para posteriormente hacerlo
      echo "
      <script>
      var hash = location.href.split('#');
      window.location.href='".SESION_EXPIRADA."&ori='+hash[1];
      </script>";
    }
    exit;
  }

  # Recupera el usuario de la sesion
  $row = RecuperaValor("SELECT fl_usuario FROM c_usuario WHERE cl_sesion='$id_sesion'");
  if (empty($row[0])) {
    # -3: La sesi&oacute;n no existe.
    if(isAjax()){
      echo json_encode((Object) array('location' => SESION_NO_EXISTE));
    } else {
      header("Location: ".SESION_NO_EXISTE);
    }
    exit;
  }
  $fl_usuario = $row[0];
  # Actualiza la sesion
  ActualizaSesion($id_sesion, $p_admin, $p_tiempo, $p_self);

  # Agrega time stamp en last activity y regresa el usuario de la sesion
  EjecutaQuery("UPDATE c_usuario SET last_activity=current_timestamp WHERE fl_usuario=$fl_usuario");
  return $fl_usuario;
}

# Funcion para verificar si el usuario tiene permiso de entrar a la funcion
function ValidaPermiso($p_funcion, $p_tipo)
{

  # Lee la sesion del cookie
  $cl_sesion = $_COOKIE[SESION_ADMIN];

  # Verifica que existe la sesion
  if (empty($cl_sesion))
    return False;

  # Recupera el usuario y su perfil
  $row = RecuperaValor("SELECT fl_usuario, fl_perfil FROM c_usuario WHERE cl_sesion='$cl_sesion'");
  $fl_usuario = $row[0];
  $fl_perfil = $row[1];

  # Verifica que existe el usuario
  if (empty($fl_usuario))
    return False;

  # Verifica si es el Administrador
  if ($fl_usuario == ADMINISTRADOR)
    return True;

  # Recupera el tipo de seguridad de la funcion
  $row = RecuperaValor("SELECT fg_tipo_seguridad FROM c_funcion WHERE fl_funcion=$p_funcion");
  $fg_tipo_seguridad = $row[0];

  # Revisa si la funcion es solo para el Administrador
  if ($fg_tipo_seguridad == 'A')
    return False;

  # Revisa si la funcion es Gratis
  if ($fg_tipo_seguridad == 'X')
    return True;

  # El tipo de seguridad es Restringido ('R'), verifica si el perfil tiene permiso para la funcion
  $Query  = "SELECT COUNT(1) FROM k_per_funcion ";
  $Query .= "WHERE fl_perfil=$fl_perfil ";
  $Query .= "AND fl_funcion=$p_funcion ";
  switch ($p_tipo) {
    case PERMISO_EJECUCION:
      $Query .= "AND fg_ejecucion = '1' ";
      break;
    case PERMISO_DETALLE:
      $Query .= "AND fg_detalle = '1' ";
      break;
    case PERMISO_MODIFICACION:
      $Query .= "AND fg_modificacion = '1' ";
      break;
    case PERMISO_ALTA:
      $Query .= "AND fg_alta = '1' ";
      break;
    case PERMISO_BAJA:
      $Query .= "AND fg_baja = '1' ";
      break;
    default:
      return False;
  }
  $row = RecuperaValor($Query);
  if ($row[0] > 0)
    return True;
  else
    return False;
}

# Recupera el usuario logueado
# Se agrego un parametro cuando es self pace madara un True
function ObtenUsuario($p_admin=True) {

  # Lee la sesion del cookie
  if (!$p_admin) {
    $id_sesion = !empty($_COOKIE[SESION_RM])?$_COOKIE[SESION_RM]:NULL;
    if (empty($id_sesion)) {
      $id_sesion = !empty($_COOKIE[SESION_CAMPUS])?$_COOKIE[SESION_CAMPUS]:NULL;
      if (!$id_sesion)
        $id_sesion = !empty($_COOKIE[SESION_SELF])?$_COOKIE[SESION_SELF]:NULL;
    }
  }
  else
    $id_sesion = $_COOKIE[SESION_ADMIN];

  # Valida si existe un identificador de sesion en el cookie
  if (empty($id_sesion))
    return False;

  # Recupera el usuario de la sesion
  $row = RecuperaValor("SELECT fl_usuario FROM c_usuario WHERE cl_sesion='$id_sesion'");
  if (empty($row[0]))
    return False;

  return $row[0];
}

# Recupera el usuario logueado
function ObtenNombre()
{

  # Lee la sesion del cookie
  $id_sesion = $_COOKIE[SESION_ADMIN];

  # Valida si existe un identificador de sesion en el cookie
  if (empty($id_sesion))
    return False;

  # Recupera el usuario de la sesion
  $row = RecuperaValor("SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE cl_sesion='$id_sesion'");
  if (empty($row[0]))
    return False;

  return str_uso_normal($row[0] . " " . $row[1]);
}

# Recupera el perfil del usuario logueado
function ObtenPerfil($p_usuario)
{

  # Revisa que se haya recibido el usuario
  if (empty($p_usuario))
    return False;

  # Recupera el perfil del usuario solicitado
  $row = RecuperaValor("SELECT fl_perfil FROM c_usuario WHERE fl_usuario=$p_usuario");
  if (empty($row[0]))
    return False;

  return $row[0];
}


#
# MRA: Funciones para manejo de parametros
#

function RecibeParametroNumerico($p_nombre, $p_get = False, $p_signo = False)
{

  if ($p_get)
    $var = isset($_GET[$p_nombre]) ? $_GET[$p_nombre] : '';
  else
    $var = isset($_POST[$p_nombre]) ? $_POST[$p_nombre] : '';

  if ($var == 'on')
    $var = "1";
  if (!ValidaEntero($var, $p_signo))
    $var = "0";

  return $var;
}

function RecibeParametroBinario($p_nombre)
{

  $var = !empty($_POST[$p_nombre])?$_POST[$p_nombre]:NULL;
  if (!empty($var))
    $var = "1";
  else
    $var = "0";

  return $var;
}

function RecibeParametroFlotante($p_nombre, $p_get = False)
{

  if ($p_get)
    $var = isset($_GET[$p_nombre]) ? $_GET[$p_nombre] : '';
  else
    $var = isset($_POST[$p_nombre]) ? $_POST[$p_nombre] : '';

  $var = str_float($var);
  if (!ValidaFlotante($var))
    $var = "0.0";

  return $var;
}

function RecibeParametroHTML($p_nombre, $p_utf8=True, $p_get=False) {

  if($p_get)
    $var = isset($_GET[$p_nombre]) ? $_GET[$p_nombre] : '';
  else
    $var = isset($_POST[$p_nombre]) ? $_POST[$p_nombre] : '';

  if($p_utf8)
    //$var = str_html_bd($var); // This is not working properly
    $var = htmlentities($var, ENT_QUOTES);
  else
    //$var = str_html_bd($var); // This is not working properly
    $var = htmlentities($var, ENT_QUOTES);

  return $var;
}

function RecibeParametroFecha($p_nombre)
{

  $var = isset($_POST[$p_nombre])?$_POST[$p_nombre]:NULL;
  $len = strlen($var);
  if ($len > 0) {
    for ($i = 0; $i < $len; $i++) {
      $c = $var[$i];
      if ($c >= '0' && $c <= '9') // Puede contener numeros 0-9
        continue;
      if ($c == '/' || $c == '-') // Puede contener / -
        continue;
      $var[$i] = ' ';
    }
  }

  return $var;
}

function RecibeParametroHoraMin($p_nombre)
{

  $var = !empty($_POST[$p_nombre])?$_POST[$p_nombre]:NULL;
  if (!ValidaHoraMin($var))
    $var = substr($var, 0, 5);

  return $var;
}


#
# MRA: Funciones para manejo de cadenas de texto
#

# Funcion para convertir cadenas recuperadas de la base de datos que se van a editar como HTML
function str_html($p_cadena)
{

  $cadena = $p_cadena;
  // $cadena = str_replace("&", "&amp;", $cadena);
  $cadena = str_replace("<", "&lt;", $cadena);
  $cadena = str_replace(">", "&gt;", $cadena);
  $cadena = str_replace("\"", "&quot;", $cadena);
  $cadena = str_replace("'", "&#039;", $cadena);
  $cadena = str_replace("/", "&#47;", $cadena);
  return ($cadena);
}

# Funcion para convertir cadenas recuperadas de la base de datos que se van a editar como texto
function str_texto($p_cadena)
{
  #*************NOTA IMPORTANTE********************
  # en la nuevas version cambiar esto por "htmlentities()", es lo mismo que hace todos los replace

  $cadena = $p_cadena;
  $cadena = str_replace("<", "&lt;", $cadena);
  $cadena = str_replace(">", "&gt;", $cadena);
  $cadena = str_replace("\"", "&quot;", $cadena);
  $cadena = str_replace("'", "&#039;", $cadena);
  $cadena = str_replace("’", "&#039;", $cadena);
  $cadena = str_replace("“", "&#039;", $cadena);
  $cadena = str_replace("”", "&#039;", $cadena);
  $cadena = str_replace("à", "&#224;", $cadena);
  $cadena = str_replace("â", "&#226;", $cadena);
  $cadena = str_replace("ã", "&#227;", $cadena);
  $cadena = str_replace("ä", "&#228;", $cadena);
  $cadena = str_replace("å", "&#229;", $cadena);
  $cadena = str_replace("æ", "&#230;", $cadena);
  $cadena = str_replace("ç", "&#231;", $cadena);
  $cadena = str_replace("è", "&#232;", $cadena);
  $cadena = str_replace("ê", "&#234;", $cadena);
  $cadena = str_replace("ë", "&#235;", $cadena);
  $cadena = str_replace("ì", "&#236;", $cadena);
  $cadena = str_replace("î", "&#238;", $cadena);
  $cadena = str_replace("ï", "&#239;", $cadena);
  $cadena = str_replace("ò", "&#242;", $cadena);
  $cadena = str_replace("ô", "&#244;", $cadena);
  $cadena = str_replace("õ", "&#245;", $cadena);
  $cadena = str_replace("ö", "&#246;", $cadena);
  $cadena = str_replace("ù", "&#249;", $cadena);
  $cadena = str_replace("û", "&#251;", $cadena);
  $cadena = str_replace("ü", "&#252;", $cadena);
  $cadena = str_replace("ª", "&#170;", $cadena);
  $cadena = str_replace("º", "&#186;", $cadena);
  $cadena = str_replace("/", "&#47;", $cadena);

  return ($cadena);
}

# Funcion para recuperar cadenas de la base de datos (HTML) para usarse en el sitio
function str_uso_normal($p_cadena)
{
  #*************NOTA IMPORTANTE********************
  # en la nuevas version cambiar esto por " html_entity_decode()", es lo mismo que hace todos los replace
  # Sustituye caracteres especiales
  $cadena = $p_cadena;
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);
  $cadena = str_replace("&#47;", "/", $cadena);
  return ($cadena);
}

# Funcion para convertir cadenas de la base de datos (HTML) con salida ASCII Ej. para exportacion a Excel
function str_ascii($p_cadena)
{
  #*************NOTA IMPORTANTE********************
  # en la nuevas version cambiar esto por " html_entity_decode()", es lo mismo que hace todos los replace
  # Sustituye caracteres especiales
  $cadena = $p_cadena;
  //$cadena = str_replace("&#65;ND", "AND", $cadena);
  //$cadena = str_replace("&#97;nd", "and", $cadena);
  //$cadena = str_replace("&#65;nd", "And", $cadena);
  $cadena = str_replace("&aacute;", chr(225), $cadena);
  $cadena = str_replace("&Aacute;", chr(193), $cadena);
  $cadena = str_replace("&eacute;", chr(233), $cadena);
  $cadena = str_replace("&Eacute;", chr(201), $cadena);
  $cadena = str_replace("&iacute;", chr(237), $cadena);
  $cadena = str_replace("&Iacute;", chr(205), $cadena);
  $cadena = str_replace("&oacute;", chr(243), $cadena);
  $cadena = str_replace("&Oacute;", chr(211), $cadena);
  $cadena = str_replace("&uacute;", chr(250), $cadena);
  $cadena = str_replace("&Uacute;", chr(218), $cadena);
  $cadena = str_replace("&uuml;", chr(252), $cadena);
  $cadena = str_replace("&Uuml;", chr(220), $cadena);
  $cadena = str_replace("&ntilde;", chr(241), $cadena);
  $cadena = str_replace("&Ntilde;", chr(209), $cadena);
  $cadena = str_replace("&iquest;", chr(191), $cadena);
  $cadena = str_replace("&copy;", chr(169), $cadena);
  $cadena = str_replace("&reg;", chr(174), $cadena);
  $cadena = str_replace("&#8482;", '™', $cadena);
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);
  $cadena = str_replace("&#47;", "/", $cadena);
  return ($cadena);
}

# Funcion para convertir cadenas que se van a guardar en la base de datos
function str_html_bd($p_cadena)
{

  # Sustituye caracteres especiales
  $cadena = $p_cadena;
  //$cadena = str_replace("AND", "&#65;ND", $cadena);
  //$cadena = str_replace("and", "&#97;nd", $cadena);
  //$cadena = str_replace("And", "&#65;nd", $cadena);
  //$cadena = str_ireplace("and", "", $cadena);
  $cadena = str_replace("SCRIPT", "&#83;CRIPT", $cadena);
  $cadena = str_replace("script", "&#115;cript", $cadena);
  $cadena = str_replace("Script", "&#83;cript", $cadena);
  $cadena = str_ireplace("script", "", $cadena);
  $cadena = str_replace(chr(225), "&aacute;", $cadena);
  $cadena = str_replace(chr(193), "&Aacute;", $cadena);
  $cadena = str_replace(chr(233), "&eacute;", $cadena);
  $cadena = str_replace(chr(201), "&Eacute;", $cadena);
  $cadena = str_replace(chr(237), "&iacute;", $cadena);
  $cadena = str_replace(chr(205), "&Iacute;", $cadena);
  $cadena = str_replace(chr(243), "&oacute;", $cadena);
  $cadena = str_replace(chr(246), "&ouml;", $cadena);
  $cadena = str_replace(chr(211), "&Oacute;", $cadena);
  $cadena = str_replace(chr(250), "&uacute;", $cadena);
  $cadena = str_replace(chr(218), "&Uacute;", $cadena);
  $cadena = str_replace(chr(252), "&uuml;", $cadena);
  $cadena = str_replace(chr(220), "&Uuml;", $cadena);
  $cadena = str_replace(chr(241), "&ntilde;", $cadena);
  $cadena = str_replace(chr(209), "&Ntilde;", $cadena);
  $cadena = str_replace(chr(191), "&iquest;", $cadena);
  $cadena = str_replace(chr(169), "&copy;", $cadena);
  $cadena = str_replace(chr(174), "&reg;", $cadena);
  $cadena = str_replace(chr(180), "&ordf;", $cadena);
  $cadena = str_replace(chr(186), "&ordm;", $cadena);
  $cadena = str_replace('™', "&#8482;", $cadena);
  $cadena = str_replace("<", "&lt;", $cadena);
  $cadena = str_replace(">", "&gt;", $cadena);
  $cadena = str_replace("\"", "&quot;", $cadena);
  $cadena = str_replace("'", "&#039;", $cadena);
  $cadena = str_replace("=", "&#061;", $cadena);
  $cadena = str_replace("/", "&#47;", $cadena);
  $cadena = str_replace('ã', "&atilde;", $cadena);
  $cadena = str_replace('Ã', "&Atilde;", $cadena);
  if(DATABASE_TYPE == DATABASE_MYSQL)
    $cadena = str_replace("\\", "\\\\", $cadena);
  return ($cadena);
}

# Funcion para quitar el formato numerico a una cadena
function str_float($p_cadena)
{

  $cadena = $p_cadena;
  $cadena = str_replace('$', '', $cadena);
  $cadena = str_replace(',', '', $cadena);
  $cadena = str_replace('%', '', $cadena);
  return ($cadena);
}

# Fincion para dar formato de solo texto eliminando codigo HTML
function str_sin_html($p_cadena)
{

  # Elimina tags de formato del texto
  $cadena = $p_cadena;
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("<a ", "", $cadena);
  $cadena = str_replace("</a>", "", $cadena);
  $cadena = str_replace("<strong>", "", $cadena);
  $cadena = str_replace("</strong>", "", $cadena);
  $cadena = str_replace("<em>", "", $cadena);
  $cadena = str_replace("</em>", "", $cadena);
  $cadena = str_replace("<sub>", "", $cadena);
  $cadena = str_replace("</sub>", "", $cadena);
  $cadena = str_replace("<sup>", "", $cadena);
  $cadena = str_replace("</sup>", "", $cadena);
  $cadena = str_replace("<b>", "", $cadena);
  $cadena = str_replace("</b>", "", $cadena);
  $cadena = str_replace("<p>", "", $cadena);
  $cadena = str_replace("</p>", "", $cadena);
  $cadena = str_replace("<li>", " ", $cadena);
  $cadena = str_replace("</li>", " ", $cadena);
  $cadena = str_replace("<ul>", " ", $cadena);
  $cadena = str_replace("</ul>", " ", $cadena);
  $cadena = str_replace("<hr>", "", $cadena);
  $cadena = str_replace("<hr />", "", $cadena);
  $cadena = str_replace("<br>", " ", $cadena);
  $cadena = str_replace("<br />", " ", $cadena);

  # Elimina la ultima palabara para no presentar palabras truncas
  $cadena = substr($cadena, 0, strrpos($cadena, ' '));
  return ($cadena);
}


#
# MRA: Funciones para manejo de fechas
#

# Verifica que la fecha sea valida
# Formatos permitidos
# espanol: ddmmaa ddmmaaaa dd-mm-aa dd-mm-aaaa
# ingles : mmddaa mmddaaaa mm-dd-aa mm-dd-aaaa
# separadores: '-', '/', '.', ' '
function ValidaFecha($p_date)
{

  # Valida la longitud de la cadena
  if(strlen($p_date) != 6 AND strlen($p_date) != 8 AND strlen($p_date) != 10)
    return NULL;

  # Obtiene el idioma de la sesion
  $cl_idioma = ObtenIdioma();

  # Descompone la fecha
  if (strlen($p_date) == 6) {
    //if($cl_idioma == ESPANOL) { // ddmmaa
    $day = substr($p_date, 0, 2);
    $month = substr($p_date, 2, 2);
    //}
    //else { // mmddaa
    //  $month = substr($p_date, 0, 2);
    //  $day = substr($p_date, 2, 2);
    //}
    $year = substr($p_date, 4, 2);
  }
  if(strlen($p_date) == 8) {
    if(strpos($p_date, '.') OR strpos($p_date, '-') OR strpos($p_date, '/') OR strpos($p_date, ' ')) {
      //if($cl_idioma == ESPANOL) { // dd-mm-aa
      $day = substr($p_date, 0, 2);
      $month = substr($p_date, 3, 2);
      //}
      //else { // mm-dd-aa
      //  $month = substr($p_date, 0, 2);
      //  $day = substr($p_date, 3, 2);
      //}
      $year = substr($p_date, 6, 2);
    } else {
      //if($cl_idioma == ESPANOL) { // ddmmaaaa
      $day = substr($p_date, 0, 2);
      $month = substr($p_date, 2, 2);
      //}
      //else { // mmddaaaa
      //  $month = substr($p_date, 0, 2);
      //  $day = substr($p_date, 2, 2);
      //}
      $year = substr($p_date, 4, 4);
    }
  }
  if (strlen($p_date) == 10) {
    //if($cl_idioma == ESPANOL) { // dd-mm-aaaa
    $day = substr($p_date, 0, 2);
    $month = substr($p_date, 3, 2);
    //}
    //else { // mm-dd-aaaa
    //  $month = substr($p_date, 0, 2);
    //  $day = substr($p_date, 3, 2);
    //}
    $year = substr($p_date, 6, 4);
  }

  # Valida que los componentes sean numericos
  if(!is_numeric($day) OR !is_numeric($month) OR !is_numeric($year))
    return NULL;

  # Convierte la fecha en timestamp
  $stamp = strtotime($year . "-" . $month . "-" . $day);
  if (!is_numeric($stamp))
    return NULL;

  # Verifica que sea una fecha valida
  $day   = date('d', $stamp);
  $month = date('m', $stamp);
  $year  = date('Y', $stamp);
  if (!checkdate($month, $day, $year))
    return NULL;

  # Regresa una cadena con la fecha en formato universal
  $date = $year."-".$month."-".$day;
  return $date;
}

# Verifica que la hora sea valida (horas-minutos)
# Formatos permitidos: H:MM, HH:MM
function ValidaHoraMin($p_hora) {

  # Valida la longitud de la cadena
  $len = strlen($p_hora);
  if($len == 4)
    $p_hora = "0".$p_hora;
  $len = strlen($p_hora);
  if($len != 5)
    return NULL;

  # Verifica el formato H:MM o HH:MM
  $c = $p_hora[2];
  if ($c <> ':')
    return NULL;
  for ($i = 0; $i < $len; $i++) {
    if ($i == 2)
      continue;
    $c = $p_hora[$i];
    if ($c >= '0' && $c <= '9') // Puede contener numeros 0-9
      continue;
    return NULL;
  }

  # Descompone la hora
  $horas = substr($p_hora, 0, 2);
  $minutos = substr($p_hora, 3, 2);

  # Valida que la hora sea valida
  if($horas < 0 OR $horas > 23)
    return NULL;

  # Valida que los minutos sean validos
  if($minutos < 0 OR $minutos > 59)
    return NULL;

  # Regresa la hora original
  return $p_hora;
}

# Obtiene el nombre de un mes
function ObtenNombreMes($p_mes) {

  $etq_mes = "";
  switch($p_mes) {
    case  1: $etq_mes = ObtenEtiqueta(460); break;
    case  2: $etq_mes = ObtenEtiqueta(461); break;
    case  3: $etq_mes = ObtenEtiqueta(462); break;
    case  4: $etq_mes = ObtenEtiqueta(463); break;
    case  5: $etq_mes = ObtenEtiqueta(464); break;
    case  6: $etq_mes = ObtenEtiqueta(465); break;
    case  7: $etq_mes = ObtenEtiqueta(466); break;
    case  8: $etq_mes = ObtenEtiqueta(467); break;
    case  9: $etq_mes = ObtenEtiqueta(468); break;
    case 10: $etq_mes = ObtenEtiqueta(469); break;
    case 11: $etq_mes = ObtenEtiqueta(470); break;
    case 12: $etq_mes = ObtenEtiqueta(471); break;
  }
  return $etq_mes;
}


#
# MRA: Funciones de uso general
#

# Obtiene el nombre de un archivo sin la extension
function ObtenNombreArchivo($p_archivo) {

  $archivo = $p_archivo;
  if (substr_count($archivo, '/') > 0)
    $archivo = substr($archivo, strrpos($archivo, '/') + 1);
  if (substr_count($archivo, '.') > 0)
    $archivo = substr($archivo, 0, strpos($archivo, '.'));
  return $archivo;
}

# Obtiene la extension de un nombre de archivo
function ObtenExtensionArchivo($p_archivo) {

  $tokens = array( );
  $tokens = explode(".", $p_archivo);
  $extension = $tokens[count($tokens) - 1];
  return $extension;
}

# Regresa el idioma seleccionado de la sesion
function ObtenIdioma( ) {

  # Revisa el idioma de la sesion, toma el valor por omision si no esta definido
  $cl_idioma = isset($_COOKIE[IDIOMA_NOMBRE]) ? $_COOKIE[IDIOMA_NOMBRE] : "";
  if (empty($cl_idioma))
    $cl_idioma = IDIOMA_DEFAULT;

  # Regresa el idioma
  return $cl_idioma;
}

# Regresa el parametro elegido dependiendo del idioma de la sesion
function EscogeIdioma($p_base, $p_trad) {

  # Revisa el idioma de la sesion
  $cl_idioma = ObtenIdioma();

  # Regresa el parametro elegido, toma el valor por omision si no esta definido
  if ($cl_idioma <> IDIOMA_DEFAULT && !empty($p_trad))
    return $p_trad;
  else
    return $p_base;
}

function SelectLang($lang) {

  if (empty($lang)) {
    $lang = 2; // If $lang is empty set to english(2) as default using locale files
  }

  setcookie(IDIOMA_NOMBRE, $lang);
}

# Funcion para recuperar una variable de configuracion
function ObtenConfiguracion($p_configuracion) {
  # Recupera la variable de la tabla de configuracion
  $row = RecuperaValor("SELECT ds_valor FROM c_configuracion WHERE cl_configuracion=$p_configuracion");
  $respuesta=str_uso_normal($row[0]);
  return $respuesta;
}

# Funcion para recuperar etiquetas
function ObtenEtiquetaLang($p_etiqueta, $sufix = 2)
{

  # Tag recovery (From file - New)
  $langselect = $sufix;
  switch ($langselect) {
    case '1':
      $langfile = 'spanish.csv';
      break;

    case '2':
      $langfile = 'english.csv';
      break;

    case '3':
      $langfile = 'french.csv';
      break;

    default:
      $langfile = 'english.csv';
      break;
  }

  $file = fopen($_SERVER['DOCUMENT_ROOT']."/locale/".$langfile, "r") or exit("Unable to open file!");
  $separator = "|";
  $id = strval($p_etiqueta);

  // Output a line of the file until the end is reached
  while (!feof($file)) {
    $line = fgets($file);
    $len_line = strlen($line);
    $find_id = strripos($line, $id);
    $find_gap = strripos($line, $separator);

    if ($find_id === false) {
    } else {
      $id = substr($line, $find_id, $find_gap);
      $tag = substr($line, $find_gap+1);
      break;
    }
  }
  fclose($file);

  //return "Tag#:".$p_etiqueta; //returns the Tag value for debuging purpose
  return htmlentities(rtrim($tag), ENT_QUOTES, "UTF-8");
  //str_uso_normal(rtrim($tag)); Not used animore
  # -----Tag recovery (From file - New)-----
}

# Funcion para recuperar etiquetas -- Recovery function for the tags
function ObtenEtiqueta($p_etiqueta, $revela_tag=False) {

  # In case you need to retrieve tags from DB uncoment this:
   $langselect = isset($_COOKIE[IDIOMA_NOMBRE])?$_COOKIE[IDIOMA_NOMBRE]:2;

   switch ($langselect) {
     case '1': $tag = RecuperaValor("SELECT ds_etiqueta_esp  FROM c_etiqueta WHERE cl_etiqueta=$p_etiqueta");
       break;

     case '2': $tag = RecuperaValor("SELECT ds_etiqueta FROM c_etiqueta WHERE cl_etiqueta=$p_etiqueta");
       break;

     case '3': $tag = RecuperaValor("SELECT ds_etiqueta_fra FROM c_etiqueta WHERE cl_etiqueta=$p_etiqueta");
       break;

     default: $tag = RecuperaValor("SELECT ds_etiqueta FROM c_etiqueta WHERE cl_etiqueta=$p_etiqueta");
       break;
   }
   return $revela_tag ? $p_etiqueta." ".htmlspecialchars(rtrim($tag[0]), ENT_QUOTES, "UTF-8") : htmlspecialchars(rtrim($tag[0]), ENT_QUOTES, "UTF-8");
  # -----Recupera la etiqueta fromDB - END -----
  #--------------------------------------------#
  # Tag recovery From file - New Implemented (LOCALE)
//  $langselect = isset($_COOKIE[IDIOMA_NOMBRE]) ? $_COOKIE[IDIOMA_NOMBRE] : "";
//  switch ($langselect) {
//    case '1':
//      $langfile = 'spanish.csv';
//      break;
//
//    case '2':
//      $langfile = 'english.csv';
//      break;
//
//    case '3':
//      $langfile = 'french.csv';
//      break;
//
//    default:
//      $langfile = 'english.csv';
//      break;
//  }
//  $file = fopen(__DIR__ . "/../locale/" . $langfile, "r") or exit("Unable to open file!");
//  $separator = " |";
//  $id = strval($p_etiqueta . $separator);
  # Output a line of the file until the end is reached or find the tag
//  while (!feof($file)) {
//    $line = fgets($file);
//    $len_line = strlen($line);
//    $find_id = strripos($line, $id);
//    $find_gap = strripos($line, $separator);
//    if ($find_id === false) {
//      $tag=NULL;
//    } else {
//      $id = substr($line, $find_id, $find_gap);
//      $tag = substr($line, $find_gap+2);
//      break;
//    }
//  }
//  fclose($file);
  # If $revela_tag= True shows the tag Number for Debuging
  # -----Tag recovery (From file - New)-----
  //return $revela_tag ? $p_etiqueta." ".htmlspecialchars(rtrim($tag), ENT_QUOTES, "UTF-8") : htmlspecialchars(rtrim($tag), ENT_QUOTES, "UTF-8");

}

# Funcion para recuperar nombre de archivos de imagen
function ObtenNombreImagen($p_imagen) {

  # Recupera el nombre de la imagen
  $row = RecuperaValor("SELECT nb_archivo, tr_archivo FROM c_imagen WHERE cl_imagen=$p_imagen");
  return str_ascii(EscogeIdioma($row[0], $row[1]));
}

# Funcion para recuperar mensajes
function ObtenMensaje($p_mensaje) {

  # Recupera el texto del mensaje
  if ($p_mensaje <> "") {
    $row = RecuperaValor("SELECT ds_mensaje, tr_mensaje FROM c_mensaje WHERE cl_mensaje=$p_mensaje");
    return EscogeIdioma($row[0], $row[1]);
  } else
    return "";
}

# Recupera el nombre del programa actual
function ObtenProgramaActual( ) {

  # Determina el nombre del programa en ejecucion
  $nb_programa = $_SERVER['PHP_SELF'];
  $nb_programa = substr($nb_programa, strrpos($nb_programa, '/')+1);
  return $nb_programa;
}

# Recupera el nombre del programa base
function ObtenProgramaBase()
{

  # Determina el nombre del programa en ejecucion
  $nb_programa = ObtenProgramaActual();
  $nb_programa = str_replace(PGM_FORM, '', $nb_programa);
  $nb_programa = str_replace(PGM_INSUPD, '', $nb_programa);
  $nb_programa = str_replace("_i", '', $nb_programa);
  $nb_programa = str_replace("_u", '', $nb_programa);
  $nb_programa = str_replace("_del", '', $nb_programa);
  $nb_programa = str_replace("_exp", '', $nb_programa);
  $nb_programa = str_replace("_rpt", '', $nb_programa);
  $nb_programa = str_replace("_snd", '', $nb_programa);
  return $nb_programa;
}

# Recupera el nombre del programa alterno
function ObtenProgramaNombre($p_nombre) {

  # Determina el nombre del programa para la forma de captura
  $nb_programa = ObtenProgramaBase();
  $lon = strpos($nb_programa, '.');
  $nb_programa = substr($nb_programa, 0, $lon);
  $nb_programa .= $p_nombre.".php";
  return $nb_programa;
}

# Verifica que el formato del email sea valido
function ValidaEmail($p_mail) {

  $p_mail = str_ascii($p_mail);
  $len = strlen($p_mail);
  if ($len == 0) // Que no este vacio
    return False;
  if (substr_count($p_mail, '@') != 1) // Que tenga un @
    return False;
  if (substr_count($p_mail, '.') < 1) // Que tenga al menos un .
    return False;
  if (strpos($p_mail, '..')) // Que no tenga ..
    return False;
  if (($p_mail[0] == '@') || ($p_mail[0] == '.')) // Que no empiece con @ ni con .
    return False;
  $ult = $len - 1;
  if ($p_mail[$ult] == '@') // Que no termine con @
    return False;
  for ($i = 0; $i < $len; $i++) {
    $c = $p_mail[$i];
    if ($c >= 'A' && $c <= 'Z') // Puede contener letras A-Z
      continue;
    if ($c >= 'a' && $c <= 'z') // Puede contener letras a-z
      continue;
    if ($c >= '0' && $c <= '9') // Puede contener numeros 0-9
      continue;
    if ($c == '@' || $c == '.' || $c == '_' || $c == '-') // Puede contener @ . _ -
      continue;
    return False;
  }
  return True;
}

# Verifica que sea entero
function ValidaEntero($p_valor, $p_signo=False) {

  if(!is_numeric($p_valor)) // Que sea numerico
    return False;
  $len = strlen($p_valor);
  if ($len == 0) // Que no este vacio
    return False;
  for ($i = 0; $i < $len; $i++) {
    $c = $p_valor[$i];
    if ($c >= '0' && $c <= '9') // Puede contener numeros 0-9
      continue;
    if($i == 0 AND $c == '-' AND $p_signo) // Puede ser negativo si se indica en el parametro
      continue;
    return False;
  }
  return True;
}

# Verifica que sea entero
function ValidaFlotante($p_valor) {

  if(!is_numeric($p_valor)) // Que sea numerico
    return False;
  $len = strlen($p_valor);
  if ($len == 0) // Que no este vacio
    return False;
  if (substr_count($p_valor, '.') > 1) // Que tenga solo un .
    return False;
  for ($i = 0; $i < $len; $i++) {
    $c = $p_valor[$i];
    if ($c >= '0' && $c <= '9') // Puede contener numeros 0-9
      continue;
    if ($c == '.') // Puede contener .
      continue;
    return False;
  }
  return True;
}

# Presenta pagina de error
function MuestraPaginaError($p_error=ERR_DEFAULT) {

  # Recupera el usuario de la sesion
  $fl_usuario = ObtenUsuario();
  if (empty($fl_usuario))
    $fl_usuario = ObtenUsuario(False);
  if (empty($fl_usuario))
    $fl_usuario = 0;

  # Prepara el codigo de error a mostrar al usuario
  EjecutaQuery("UPDATE c_usuario SET cl_mensaje=$p_error WHERE fl_usuario=$fl_usuario");
  header("Location: ".PAGINA_ERROR);
  exit;
}

# Genera un thumbnail en destino para una imagen dada en origen
# Las medidas por omision del thumb son 150x150
# Si se reciben ambas dimensiones se ajusta la imagen sin mantener la proporcion original
# Si recibe solo una dimension, se calculara la otra manteniendo la proporcion de la imagen original
# Si se especifica una dimension de lado fija se inicializa la mayor y se ajusta la menor para no perder la proporcion
# Si se especifica una dimension maxima se reducen ambas dimensiones para no excederla y mantener la proporcion
function CreaThumb($p_origen, $p_destino, $p_ancho=0, $p_alto=0, $p_fija_lado=0, $p_max_lado=0) {

  # Abre el archivo con la imagen original
  $original = imagecreatefromjpeg($p_origen);
  if (!$original)
    return False;
  $ancho_orig = imagesx($original);
  $alto_orig = imagesy($original);
  $ratio_orig = $ancho_orig / $alto_orig;
  if ($ancho_orig >= $alto_orig)
    $fg_horizontal = True;
  else
    $fg_horizontal = False;

  # Medidas por omision del thumb
  $ancho = 150;
  $alto = 150;

  # Calcula las dimensiones del thumb en base a una dimension maxima
  if($p_max_lado > 0) {
    if($ancho_orig > $p_max_lado OR $alto_orig > $p_max_lado) {
      if($fg_horizontal) {
        $p_ancho = $p_max_lado;
        $p_alto = 0;
      } else {
        $p_alto = $p_max_lado;
        $p_ancho = 0;
      }
      if ($p_fija_lado > $p_max_lado)
        $p_fija_lado = $p_max_lado;
    } else {
      $p_ancho = $ancho_orig;
      $p_alto = $alto_orig;
    }
  }

  # Fija las dimensiones del thumb
  if($p_ancho > 0 AND $p_alto > 0) {
    $ancho = $p_ancho;
    $alto = $p_alto;
  }

  # Calcula las dimensiones del thumb en base a un ancho fijo
  if($p_ancho > 0 AND $p_alto == 0) {
    $ancho = $p_ancho;
    $alto = $p_ancho / $ratio_orig; // Ajusta el alto
  }

  # Calcula las dimensiones del thumb en base a un alto fijo
  if($p_ancho == 0 AND $p_alto > 0) {
    $alto = $p_alto;
    $ancho = $p_alto * $ratio_orig; // Ajusta el ancho
  }

  # Calcula las dimensiones del thumb en base a una dimension dada por lado
  if ($p_fija_lado > 0) {
    $ancho = $p_fija_lado;
    $alto = $p_fija_lado;
    if ($fg_horizontal) // Calcula el alto
      $alto = $p_fija_lado / $ratio_orig;
    else // Calcula el ancho
      $ancho = $p_fija_lado * $ratio_orig;
  }

  # Genera la nueva imagen
  $thumb = imagecreatetruecolor($ancho, $alto);
  imagecopyresampled($thumb, $original, 0, 0, 0, 0, $ancho, $alto, $ancho_orig, $alto_orig);
  imagejpeg($thumb, $p_destino, 90);
  return True;
}

# Prepara codigo HTML embeviendo imagenes para enviar por correo
function ConvierteHTMLenMail($p_html, $p_headers, $p_kod='iso-8859-1') {

  /*preg_match_all('~<img.*?src=.([\/.a-z0-9:_-]+).*?>~si', $p_html, $matches);
  $i = 0;
  $paths = array( );

  foreach ($matches[1] as $img) {
    $img_old = $img;
    if(strpos($img, "http://") == false) {
      $uri = parse_url($img);
      $paths[$i]['path'] = $_SERVER['DOCUMENT_ROOT'].$uri['path'];
      $content_id = md5($img);
      $p_html = str_replace($img_old, 'cid:'.$content_id, $p_html);
      $paths[$i++]['cid'] = $content_id;
    }
  }*/

  $boundary = "--" . md5(uniqid(time()));
  $headers  = $p_headers;
  $headers .= "MIME-Version: 1.0\n";
  $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
  $multipart  = "--$boundary\n";
  $multipart .= "Content-Type: text/html; charset=$p_kod\n";
  $multipart .= "Content-Transfer-Encoding: 8bit\n\n";
  $multipart .= "$p_html\n\n";

  /*foreach($paths as $path) {
    if(file_exists($path['path']))
      $fp = fopen($path['path'], "r");
    if(!$fp)
      return false;
    $imagetype = substr(strrchr($path['path'], '.' ), 1);
    $file = fread($fp, filesize($path['path']));
    fclose($fp);
    $message_part = "";
    switch ($imagetype) {
      case 'png':
      case 'PNG':
        $message_part .= "Content-Type: image/png";
        break;
      case 'jpg':
      case 'jpeg':
      case 'JPG':
      case 'JPEG':
        $message_part .= "Content-Type: image/jpeg";
        break;
      case 'gif':
      case 'GIF':
        $message_part .= "Content-Type: image/gif";
        break;
    }
    $message_part .= "; file_name=\"$path\"\n";
    $message_part .= 'Content-ID: <'.$path['cid'].">\n";
    $message_part .= "Content-Transfer-Encoding: base64\n";
    $message_part .= "Content-Disposition: inline; filename=\"".basename($path['path'])."\"\n\n";
    $message_part .= chunk_split(base64_encode($file))."\n";
    $multipart .= "--$boundary\n".$message_part."\n";
  }*/

  $multipart .= "--$boundary--\n";
  return array('multipart' => $multipart, 'headers' => $headers);
}

# Envia correo con HTML
function EnviaMailHTML($p_from_name, $p_from_mail, $p_to, $p_subject, $p_message, $p_bcc='') {

   $p_message = str_replace("&nbsp;", " ", $p_message);
   $p_message = str_replace("&nbsp", " ", $p_message);

    /*
  # Inicializa variables de ambiente para envio de correo
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);

  $to = str_ascii($p_to);
  $subject = str_ascii($p_subject);
  $headers = "From: $p_from_name<$p_from_mail>\r\nReply-To: $p_from_mail\r\n";
  if(!empty($p_bcc))
    $headers .= "Bcc: $p_bcc\r\n";
  $headers = str_ascii($headers);
  $message = ConvierteHTMLenMail($p_message, $headers);
  return mail($to, $subject, $message['multipart'], $message['headers']);
  */
    #Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    $smtphost=ObtenConfiguracion(161);
    $mailfrom=ObtenConfiguracion(162);
    $mailpass=ObtenConfiguracion(163);

    if(empty($p_from_name))
        $p_from_name=$p_to;


    //envia copia a admin@vanas.ca
    $admin = ObtenConfiguracion(83);
    try{

        //Server settings
        $mail->SMTPDebug = false;//SMTP::DEBUG_SERVER midkdk;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $smtphost;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $mailfrom;                     //SMTP username
        $mail->Password   = $mailpass;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($mailfrom, $p_subject);
        $mail->addAddress($p_to, $p_from_name);     //Add a recipient
        $mail->addBCC($admin);//copia oculta forever
        if($p_bcc)
            $mail->addBCC($p_bcc);//copia oculta

        //Attachments
        if($attachment)
            $mail->AddStringAttachment($attachment, $nameAttachment, 'base64', 'application/pdf');// attachment
        //$mail->addAttachment($attachment);         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $p_subject;
        $mail->Body    = $p_message;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->send();

        $status=true;

    }
    catch (Exception $e)
    {
        $status=false;
    }


    return $status;

}


#
# MRA: Funciones para manejo de zonas horarias (Utiliza AskGeo, Web API http://www.askgeo.com)
#

# Recupera diferencia de horas entre la zona horaria default y la solicitada (p_zona_horaria)
function RecueperaDiferenciaGMT($p_zona_horaria) {

  # Recupera latitud y longitud de la zona horaria default
  $Query  = "SELECT no_gmt, no_latitude, fg_latitude, no_longitude, fg_longitude ";
  $Query .= "FROM c_zona_horaria ";
  $Query .= "WHERE fg_default='1'";
  $row = RecuperaValor($Query);
  $no_gmt_d = $row[0];
  if ($no_gmt_d == '')
    return 0;

  # Recupera latitud y longitud de la zona horaria solicitada
  $Query  = "SELECT no_gmt, no_latitude, fg_latitude, no_longitude, fg_longitude ";
  $Query .= "FROM c_zona_horaria ";
  $Query .= "WHERE fl_zona_horaria=$p_zona_horaria";
  $row = RecuperaValor($Query);
  $no_gmt = $row[0];
  if ($no_gmt == '')
    return 0;


  // Finalmente se obtiene la diferencia en horas entre las dos zonas horarias
  $diferencia = $no_gmt - $no_gmt_d;

  return $diferencia;
}

# Se agrego un parametro cuando es self pace madara un True
function ActualizaDiferenciaGMT($p_perfil, $p_usuario, $p_self=False) {

  # Recupera la diferencia de horario de la zona horaria del usuario
  if (!$p_self) {
    if ($p_perfil == PFL_ESTUDIANTE)
      $row = RecuperaValor("SELECT fl_zona_horaria FROM c_alumno WHERE fl_alumno=$p_usuario");
    else
      $row = RecuperaValor("SELECT fl_zona_horaria FROM c_maestro WHERE fl_maestro=$p_usuario");
  } else {
    if ($p_perfil == PFL_ESTUDIANTE)
      $row = RecuperaValor("SELECT fl_zona_horaria FROM c_alumno_sp WHERE fl_alumno_sp=$p_usuario");
    if ($p_perfil == PFL_MAESTRO)
      $row = RecuperaValor("SELECT fl_zona_horaria FROM c_maestro_sp WHERE fl_maestro_sp=$p_usuario");
    else
      $row = RecuperaValor("SELECT fl_zona_horaria FROM c_administrador_sp WHERE fl_adm_sp=$p_usuario");
  }
  if (!empty($row[0]))
    $diferencia = RecueperaDiferenciaGMT($row[0]);
  else
    $diferencia = 0;

  # Escribe cookie con la diferencia de horario
  setcookie("DIF_GMT", $diferencia, time() + IDIOMA_VIGENCIA, "/");
  return $diferencia;
}

function RecuperaDiferenciaGMT( ) {

  $diferencia = isset($_COOKIE["DIF_GMT"]) ? $_COOKIE["DIF_GMT"]: "";
  if (empty($diferencia))
    $diferencia = 0;
  return $diferencia;
}


# Funcion para obtener los meses que conforma el pago si no existe en k_alumno_pago_det lo inserta en caso contrario solo actualizara
function Meses_X_Pago (){
  $Query  = "SELECT fl_alumno, a.fe_pago, CASE fg_refund WHEN '1' THEN mn_refund-a.mn_pagado ELSE a.mn_pagado END mn_pagado, no_pago, fl_alumno_pago FROM k_alumno_pago a,k_term_pago  b ";
  $Query .= "WHERE a.fl_term_pago=b.fl_term_pago AND NOT EXISTS(SELECT * FROM k_alumno_pago_det t WHERE t.fl_alumno_pago=a.fl_alumno_pago)";
  $rs = EjecutaQuery($Query);
  for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
    $mn_pagado = $row[2];
    $no_pago = $row[3];
    $fl_alumno_pago = $row[4];
    $Query2  = "SELECT e.no_grado,".ConsultaFechaBD('h.fe_inicio', FMT_FECHA).", no_semanas, g.fg_opcion_pago, ";
    $Query2 .= "CASE g.fg_opcion_pago WHEN 1 THEN g.mn_a_due WHEN 2 THEN g.mn_b_due WHEN 3 THEN g.mn_c_due WHEN 4 THEN g.mn_d_due END mn_x_due, ";
    $Query2 .= "CASE g.fg_opcion_pago WHEN 1 THEN g.mn_a_paid WHEN 2 THEN g.mn_b_paid WHEN 3 THEN g.mn_c_paid WHEN 4 THEN g.mn_d_paid END mn_x_paid, e.fl_programa, e.fl_term, ";
    $Query2 .= "CONCAT(b.ds_nombres,' ',b.ds_apaterno, ' ' , b.ds_amaterno) ";
    $Query2 .= "FROM c_usuario b,  c_grupo d, k_term e, c_programa f, k_app_contrato g, c_periodo h, k_programa_costos i ";
    $Query2 .= "WHERE  e.fl_term=(SELECT MIN(fl_term) FROM k_alumno_term s WHERE s.fl_alumno=b.fl_usuario) AND d.fl_term=e.fl_term ";
    $Query2 .= "AND e.fl_programa=f.fl_programa AND b.cl_sesion=g.cl_sesion AND e.fl_periodo=h.fl_periodo ";
    $Query2 .= "AND b.fl_usuario=$row[0] AND g.no_contrato=1  AND f.fl_programa = i.fl_programa  ";
    $row2 = RecuperaValor($Query2);
    $no_grado = $row2[0];
    $fe_inicio_pro = $row2[1];
    $no_semanas = $row2[2];
    $meses_duracion = $no_semanas / 4;
    $fg_opcion_pago = $row2[3];
    $mn_x_due = $row2[4];
    $mn_x_paid = $row2[5];
    $fl_programa = $row2[6];
    $fl_term = $row2[7];
    $ds_nombres = $row2[8];
    #obtenemos la fecha del term_ini si el grado es mayor a 1
    if ($no_grado <> 1) {
      $row3 = RecuperaValor("SELECT fl_term_ini FROM k_term a WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND no_grado=$no_grado ");
      $fl_term_ini = $row3[0];
      $row4 = RecuperaValor("SELECT fe_inicio FROM k_term a, c_periodo b WHERE fl_term=$fl_term_ini AND a.fl_periodo = b.fl_periodo");
      $fe_inicio_pro = $row4[0];
    }

    #numero de pagos, meses que cubre un pago
    if(!empty($fg_opcion_pago) AND $mn_x_due<>0 AND $mn_x_paid<>0){
      $numero_pagos =  $mn_x_paid/$mn_x_due;
      $no_meses_op = $meses_duracion/$numero_pagos; //numero de meses por opcion
      $desfase = ($no_pago-1)*$no_meses_op;
      $nuevafecha = strtotime ( "+ ".$desfase." month", strtotime($fe_inicio_pro));
      $fe_mesini_pago = date ( 'd-m-Y' , $nuevafecha );

      $pago_normal_x_mes = $mn_pagado/$no_meses_op;
      $suma=0;
      for($j=0;$j<=$no_meses_op-1;$j++){
        $mes_ini_pago = RecuperaValor("SELECT ADDDATE('".date('Y-m-d',strtotime($fe_mesini_pago))."', INTERVAL $j MONTH)");
        $mes_ini_pago = $mes_ini_pago[0];
        # Estas son las fechas que esta realmente cubriendo el pago
        $dia = substr($mes_ini_pago,8,10);
        $mes = substr($mes_ini_pago,5,2);
        $anio = substr($mes_ini_pago,0,4);
        $marzo = RecuperaValor("SELECT '$anio-$mes'='2015-03'");
        # Inserta los meses que cubre el pago con el fl_alumno_pago y el mes
        $Insert = "INSERT INTO k_alumno_pago_det(fl_alumno_pago, fe_pago,mn_pagado) VALUES($fl_alumno_pago, '$anio-$mes-$dia', $pago_normal_x_mes)";
        EjecutaQuery($Insert);
      }
    }
  }

  # solo va actualizar los registros que ya se haya ganado
  $rs = EjecutaQuery("SELECT fl_alumno_pago_det, fl_alumno_pago, fe_pago FROM k_alumno_pago_det WHERE fg_earned='0' AND fe_pago<'".date('Y-m-01')."'");
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $fl_alumno_pago_det = $row[0];
    $fl_alumno_pago = $row[1];
    $fe_pago = $row[2]; // este es el mes que esta cubriendo
    EjecutaQuery("UPDATE k_alumno_pago_det  SET fg_earned = '1' WHERE fl_alumno_pago_det = $fl_alumno_pago_det");
  }

  # Actualizar los ganados y no ganados
  $Query_eu = "SELECT fl_alumno_pago FROM k_alumno_pago a WHERE EXISTS(SELECT *FROM k_alumno_pago_det b WHERE a.fl_alumno_pago=b.fl_alumno_pago)";
  $rs_eu = EjecutaQuery($Query_eu);
  for($k=0;$row_eu=RecuperaRegistro($rs_eu);$k++){
    $fl_alumno_pago = $row_eu[0];
    # Actualizzamos lo ganado
    $Earned  = "UPDATE k_alumno_pago a ";
    $Earned .= "SET mn_earned=(SELECT SUM(mn_pagado) FROM k_alumno_pago_det r WHERE r.fl_alumno_pago=a.fl_alumno_pago AND fg_earned='1') ";
    $Earned .= "WHERE fl_alumno_pago=$fl_alumno_pago";
    EjecutaQuery($Earned);
    # Actualizzamos lo no ganado
    $Unearned  = "UPDATE k_alumno_pago a ";
    $Unearned .= "SET mn_unearned=(SELECT SUM(mn_pagado) FROM k_alumno_pago_det r WHERE r.fl_alumno_pago=a.fl_alumno_pago AND fg_earned='0') ";
    $Unearned .= "WHERE fl_alumno_pago=$fl_alumno_pago";
    EjecutaQuery($Unearned);
    # Actualizzamos la cantidad de ganados y no ganados
    $row_e = RecuperaValor("SELECT COUNT(*) FROM k_alumno_pago_det WHERE fl_alumno_pago=$fl_alumno_pago AND fg_earned='1' ");
    $no_earned = $row_e[0];
    $row_t = RecuperaValor("SELECT COUNT(*) FROM k_alumno_pago_det WHERE fl_alumno_pago=$fl_alumno_pago ");
    $total = $row_t[0];
    EjecutaQuery("UPDATE k_alumno_pago SET ds_eu='$no_earned/$total' WHERE fl_alumno_pago=$fl_alumno_pago");
  }
}

function NombreArchivoDecente($p_nombre) {

  # Sustituye caracteres especiales
  $cadena = str_ascii($p_nombre);
  $cadena = str_replace(chr(225), "a", $cadena);
  $cadena = str_replace(chr(193), "A", $cadena);
  $cadena = str_replace(chr(233), "e", $cadena);
  $cadena = str_replace(chr(201), "E", $cadena);
  $cadena = str_replace(chr(237), "i", $cadena);
  $cadena = str_replace(chr(205), "I", $cadena);
  $cadena = str_replace(chr(243), "o", $cadena);
  $cadena = str_replace(chr(211), "O", $cadena);
  $cadena = str_replace(chr(250), "u", $cadena);
  $cadena = str_replace(chr(218), "U", $cadena);
  $cadena = str_replace(chr(202), "", $cadena);
  $cadena = str_replace(chr(234), "", $cadena);
  $cadena = str_replace(chr(252), "u", $cadena);
  $cadena = str_replace(chr(220), "U", $cadena);
  $cadena = str_replace(chr(241), "n", $cadena);
  $cadena = str_replace(chr(209), "N", $cadena);
  $cadena = str_replace("\"", "", $cadena);
  $cadena = str_replace("'", "", $cadena);
  $cadena = str_replace("=", "", $cadena);
  $cadena = str_replace(" ", "_", $cadena);
  return ($cadena);
}

# Funcion para mostrar los terms que ha cursado el alumno
# Sen envia como parametro el clave del alumno y el programa que esta cursado
function PresentaAcademiHistory($p_alumno, $p_programa, $p_admin = True){

  $suma_cal_g=0;
  $suma_cal_t=0;
  $factor_promedio_g=0;
  $factor_promedio_t=0;
  $adicionales=0;
  $grado_repetido=NULL;

  #Verificamos si es alumno nuevo y presenta rubric.:
  $Queryn="SELECT fg_nuevo FROM c_usuario WHERE fl_usuario=$p_alumno ";
  $rown=RecuperaValor($Queryn);
  $fg_es_alumno_nuevo=$rown['fg_nuevo'];

  # Proceso que actualiza la calificacion de los term
  $QueryTR  = "SELECT SUM(i.no_equivalencia)/COUNT(a.fl_semana), a.fl_term, no_grado, c.fl_alumno ";
  $QueryTR .= "FROM k_semana a, k_term b, k_entrega_semanal c, c_calificacion i ";
  $QueryTR .= "WHERE a.fl_term=b.fl_term AND a.fl_semana=c.fl_semana AND c.fl_promedio_semana=i.fl_calificacion ";
  $QueryTR .= "AND a.fl_term IN(SELECT fl_term FROM k_alumno_term e WHERE e.fl_alumno=c.fl_alumno AND c.fl_alumno=$p_alumno) ";
  $QueryTR .= "GROUP BY a.fl_term ";
  $rs = EjecutaQuery($QueryTR);
  $terms = 0;
  $terms_no = 0;
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $fl_term = $row[1];
    EjecutaQuery("UPDATE k_alumno_term SET no_promedio='".$row[0]."' WHERE fl_alumno=$p_alumno AND fl_term=$row[1] ");
  }
  # Term actual
  $no_grado_actual = $fl_term;

  # Actualizamos la calificacion del estudiante con la calificacion de los terms
  $Querysd  = "SELECT SUM(no_promedio)/COUNT(*) FROM k_alumno_term ";
  $Querysd .= "WHERE fl_term IN(SELECT MAX(a.fl_term) FROM k_alumno_term a, k_term b ";
  $Querysd .= "WHERE a.fl_term=b.fl_term AND fl_alumno=$p_alumno AND a.no_promedio>0 GROUP BY b.no_grado) AND fl_alumno=$p_alumno ";
  $rst = RecuperaValor($Querysd);
  EjecutaQuery("UPDATE c_alumno SET no_promedio_t='".round($rst[0])."' WHERE fl_alumno=$p_alumno");


  # Promedio
  $Querystd  = "SELECT a.no_promedio_t, ";
  $Querystd .= "CASE no_promedio_t WHEN 0 THEN 0 ELSE (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t))  END cl_calificacion, ";
  $Querystd .= "CASE no_promedio_t WHEN 0 THEN 0 ELSE (SELECT fg_aprobado FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t))  END cal_aprobada ";
  $Querystd .= "FROM c_alumno a ";
  $Querystd .= "WHERE fl_alumno=$p_alumno ";
  $rowstd = RecuperaValor($Querystd);
  $no_promedio_t = $rowstd[0];
  $cl_calificacion = $rowstd[1];
  $cal_aprobada = $rowstd[2];
  echo '
  <article class="col-sm-12 col-md-12 col-lg-12 padding-5 sortable-grid ui-sortable">

    <!-- Widget ID (each widget will need unique ID)-->
    <div class="jarviswidget jarviswidget-color-blueLight" id="wid-id-10" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-sortable="false" role="widget">
      <header role="heading" class="no-border txt-color-white" style="background-color:#0092cd;">
        <span class="widget-icon"> <i class="fa fa-list-alt"></i> </span>
        <h2>Terms</h2>
        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
        <div role="menu" class="widget-toolbar hidden-phone txt-color-black">
          <span style="color:#000;">'.ObtenEtiqueta(524).'&nbsp<strong>'.$cl_calificacion.'</strong></span>
          <span class="label label-';if(!empty($cal_aprobada)) echo "success"; else echo "danger"; echo '">
            <i class="fa fa-thumbs-';if(!empty($cal_aprobada)) echo "up"; else echo "down"; echo ' fa-lg"></i>'.$no_promedio_t.'
          </span>
        </div>
      <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

      <!-- widget div-->
      <div role="content">
        <!-- widget content -->
        <div class="widget-body no-padding">
          <div class="panel-group smart-accordion-default" id="accordion-2">';
            # Buscamos todos los terms que haya cursado
            $QueryT = "SELECT
                        a.fl_term,
                        b.no_grado,
                        a.no_promedio,
                        (SELECT fl_grupo FROM c_grupo d WHERE d.fl_term = a.fl_term LIMIT 1) fl_grupo,
                        (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= ROUND(a.no_promedio) AND no_max >= ROUND(a.no_promedio)) cl_calificacion,
                        (SELECT fg_aprobado FROM c_calificacion WHERE no_min <= ROUND(a.no_promedio) AND no_max >= ROUND(a.no_promedio)) fg_aprobado ";
            $QueryT .= "FROM k_alumno_term a, k_term b LEFT JOIN c_leccion lec ON(lec.fl_programa=b.fl_programa AND lec.no_grado=b.no_grado) ";
            $QueryT .= "LEFT JOIN c_programa pro ON(pro.fl_programa=b.fl_programa AND pro.fl_programa=lec.fl_programa), c_periodo c ";
            $QueryT .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$p_alumno ";
            $QueryT .= "GROUP BY a.fl_term ORDER BY c.fe_inicio, b.no_grado";
            $rs = EjecutaQuery($QueryT);
            for($tot_grados=1;$row=RecuperaRegistro($rs);$tot_grados++){
              $fl_term = $row[0];
              $no_grado = $row[1];
              $no_promedio = $row[2];
              $fl_grupo = $row[3];
              $term_cal = $row[4]??null." ".$no_promedio;
              $fg_aprobado = $row[5]??null;
              # activa el term actual el collapsed
              if ($no_grado_actual == $fl_term)
                $aria_expanded = 'class="" aria-expanded="true"';
              else
                $aria_expanded = 'class="collapsed" aria-expanded="false"';
                echo '
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion-2" href="#term_'.$tot_grados.'" '.$aria_expanded.'>
                      <i class="fa fa-fw fa-plus-circle txt-color-green"></i>
                      <i class="fa fa-fw fa-minus-circle txt-color-red"></i>Term '.$no_grado."&nbsp;&nbsp;&nbsp;";
                    if(!empty($no_promedio)){
                      echo '<span class="label label-';
                      if (!empty($fg_aprobado))
                          echo "success";
                      else
                          echo "danger";
                      echo '">
                        GPA: ' . $term_cal . ' '.number_format($no_promedio).' %
                        </span>';
                      }
                      # Indicamos que terms son recursados
                      if ($grado_repetido == $no_grado)
                        echo $recurse = "&nbsp;&nbsp;&nbsp;<strong class='txt-color-red'>".ObtenEtiqueta(853)."</strong>";
                      else
                        echo $recurse = "";
                      echo '
                      <div class="pull-right txt-color-red">';
                      # Mostramos las semanas que hacen falta calificar
                      $QueryT0  = "SELECT lec.fl_leccion, lec.no_semana, lec.ds_titulo, sem.fl_semana ";
                      $QueryT0 .= "FROM c_leccion lec, k_semana sem ";
                      $QueryT0 .= "WHERE lec.fl_leccion=sem.fl_leccion AND lec.fl_programa=".$p_programa." AND lec.no_grado=".$no_grado." ";
                      $QueryT0 .= "AND sem.fl_term=".$fl_term." ORDER BY lec.no_semana ";
                      $rs0 = EjecutaQuery($QueryT0);
                      $lo = "";
                      for ($j = 0; $row2= RecuperaRegistro($rs0); $j++) {
                        $fl_leccion = $row2[0];
                        $no_semana = $row2[1];
                        $ds_titulo = str_texto($row2[2]);
                        $fl_semana = $row2[3];

                        //Revisamos si existe rubric.
                        $fg_rubric=ExisteRubric($fl_leccion);
                        if($no_semana==12)//la semana 12 nunca se califica
                            $fg_rubric="";
                        if (!empty($no_semana)) {
                          $Query = "SELECT fl_clase, " . ConsultaFechaBD('fe_clase', FMT_CAPTURA) . " fe_clase, ";
                          $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN) . " hr_clase, fg_obligatorio, fg_adicional, b.fl_entrega_semanal ";
                          $Query .= "FROM k_clase a, k_entrega_semanal b ";
                          $Query .= "WHERE a.fl_semana=b.fl_semana ";
                          $Query .= "AND a.fl_grupo=b.fl_grupo ";
                          $Query .= "AND b.fl_alumno=$p_alumno ";
                          $Query .= "AND a.fl_semana=" . $fl_semana. " ";
                          $Query .= "ORDER BY fl_clase ";
                          $cons = EjecutaQuery($Query);
                          while ($row2 = RecuperaRegistro($cons)) {
                            $fl_clase = $row2[0];

                            $fg_obligatorio = $row2[3];
                            $fg_adicional = $row2[4];
                            if ($fg_obligatorio == '1') {

                                //if($fg_rubric==1){
                                    # Revisa si hay calificacion para el alumno en esta leccion
                                    $Query = "SELECT b.cl_calificacion, b.ds_calificacion, b.fg_aprobado, b.no_equivalencia ";
                                    $Query .= "FROM k_entrega_semanal a, c_calificacion b ";
                                    $Query .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
                                    $Query .= "AND a.fl_alumno=$p_alumno ";
                                    $Query .= "AND a.fl_semana=" . $fl_semana;
                                    $row = RecuperaValor($Query);
                                //}else{
                                //    $row=null;
                                //}
                            }
                            $fecha_clase = RecuperaValor("SELECT fe_clase FROM k_clase WHERE fl_semana = " . $fl_semana . " AND fl_grupo = $fl_grupo");
                            $diferencia_fechas = strtotime($fecha_clase[0]) + 1200 - time();
                            /* Solo hay una calificacion por semana en las extras no se le assigna */
                            if ($diferencia_fechas <= 0 AND $fg_obligatorio == '1' AND $fg_adicional == '0' AND empty($row[0]) AND $no_semana != 12) { // porque es la ultima semana por lo regular esa no se cuenta
                              $lo = $lo .$no_semana . ", ";
                            }
                          }
                        }
                      }
                      if(!empty($lo))
                        echo "<label><i class='fa fa-exclamation-circle'></i> Missing Grades: </label>&nbsp;";
                      echo "<span class='badge bg-color-red'>".substr($lo, 0, -2) . '</span></div>';
                      $grado_repetido = $no_grado;
                  echo '
                      </a>
                    </h4>
                  </div>';
                  echo '
                  <div id="term_'.$tot_grados.'" ';
                  if(!$p_admin)
                    $style = "padding-top:50px;";
                  else
                    $style = "";
                  # Activamos elterm actual para que se abara el collapsed
                  if ($no_grado_actual == $fl_term) {
                    echo "
                    style = '$style'
                    aria-expanded='true'
                    class='panel-collapse collapse in' ";
                  }
                  else {
                    echo "
                    style = '$style height: 0px;'
                    aria-expanded='false'
                    class='panel-collapse collapse' ";
                  }
                  echo ' >
                    <div class="panel-body no-padding">
                      <div class="col-sm-12 col-md-12 col-lg-12 no-padding"  style="padding-top: 50px;">
                        <table class="table table-bordered table-condensed" width="100%" id="tbl_term_'.$fl_term.'">
                          <thead>
                            <th>'.ObtenEtiqueta(550).'</th>
                            <th>'.ObtenEtiqueta(551).'</th>
                            <th>'.ObtenEtiqueta(557).'</th>
                            <th>'.ObtenEtiqueta(428).'</th>
                            <th>'.ObtenEtiqueta(552).'</th>
                            <th>'.ObtenEtiqueta(553).'</th>';
                if( $fg_es_alumno_nuevo==1)
                            echo'<th> </th>';
            echo'
                          </thead>
                          <tbody>';
                          # Buscamos las lecciones del estudiante dependiendo del term y programa
                          $QueryT1  = "SELECT lec.fl_leccion, lec.no_semana, lec.ds_titulo, sem.fl_semana ";
                          $QueryT1 .= "FROM c_leccion lec, k_semana sem ";
                          $QueryT1 .= "WHERE lec.fl_leccion=sem.fl_leccion AND lec.fl_programa=".$p_programa." AND lec.no_grado=".$no_grado." ";
                          $QueryT1 .= "AND sem.fl_term=".$fl_term." ORDER BY lec.no_semana ";
                          $rs2 = EjecutaQuery($QueryT1);
                          for($lecciones=0;$row2=RecuperaRegistro($rs2);$lecciones++){
                            $fl_leccion = $row2[0];
                            $no_semana = $row2[1];
                            $ds_titulo = str_texto($row2[2]);
                            $fl_semana = $row2[3];
                            //Revisamos si existe rubric.
                            $fg_rubric=ExisteRubric($fl_leccion);
                            if($no_semana==12)//la semana 12 nunca se califica
                                $fg_rubric="";
                            if(!empty($no_semana)){
                              $Query = "SELECT fl_clase, " . ConsultaFechaBD('fe_clase', FMT_CAPTURA) . " fe_clase, ";
                              $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN) . " hr_clase, ";
                              $Query .= "fg_obligatorio, fg_adicional, b.fl_entrega_semanal, a.fl_grupo ";
                              $Query .= "FROM k_clase a, k_entrega_semanal b ";
                              $Query .= "WHERE a.fl_semana=b.fl_semana ";
                              $Query .= "AND a.fl_grupo=b.fl_grupo ";
                              $Query .= "AND b.fl_alumno = $p_alumno ";
                              $Query .= "AND a.fl_semana=" . $fl_semana. " ";
                              $Query .= "ORDER BY fl_clase ";

                              $cons = EjecutaQuery($Query);
                              $cons1 = CuentaRegistros($cons);
                              if(!empty($cons1)){
                                  while ($row3 = RecuperaRegistro($cons)) {
                                    $fl_clase = $row3[0];


                                    if (!empty($row3[1])) { # Ya se habia puesto una fecha para la clase
                                        $fe_clase = $row3[1];
                                        $hr_clase = $row3[2];
                                    }
                                    $fg_obligatorio = $row3[3];
                                    $fg_adicional = $row3[4];
                                    $fl_grupo = $row3[5];

                                    //Revisamos si existe rubric.
                                    $fg_rubric=ExisteRubric($fl_leccion);
                                    if($no_semana==12)//la semana 12 nunca se califica
                                        $fg_rubric="";
                                    if ($fg_adicional == '1') {
                                        $adicionales++;
                                        $no_semana = '';
                                        $ds_titulo = ObtenEtiqueta(538);
                                        $row[0] = '';
                                    } else {
                                        if($fg_rubric==1){
                                            # Revisa si hay calificacion para el alumno en esta leccion
                                            $Query = "SELECT b.cl_calificacion, b.ds_calificacion, b.fg_aprobado, b.no_equivalencia ";
                                            $Query .= "FROM k_entrega_semanal a, c_calificacion b ";
                                            $Query .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
                                            $Query .= "AND a.fl_alumno=$p_alumno ";
                                            $Query .= "AND a.fl_semana=" . $fl_semana;
                                            $row = RecuperaValor($Query);
                                        }else{
                                            $row=null;
                                        }
                                    }

                                    # Consulta el estatus de asistencia a live session
                                    $Query = "SELECT a.fl_live_session, a.fl_usuario, b.nb_estatus, d.fl_semana
                                    FROM k_live_session_asistencia a, c_estatus_asistencia b, k_live_session c, k_clase d
                                    WHERE a.cl_estatus_asistencia = b.cl_estatus_asistencia
                                    AND a.fl_live_session = c.fl_live_session
                                    AND c.fl_clase = d.fl_clase
                                    AND c.fl_clase = " . $fl_clase. "
                                    AND d.fl_semana = " . $fl_semana. "
                                    AND a.fl_usuario = $p_alumno";
                                    $rasis = RecuperaValor($Query);
                                    switch ($fg_obligatorio) {
                                        case '0':
                                            $obliga = ObtenEtiqueta(17);
                                            break;
                                        case '1':
                                            $obliga = ObtenEtiqueta(16);
                                            break;
                                        default:
                                            $obliga = '';
                                    }

                                    echo '
                                    <tr>
                                      <td>'.$no_semana.' </td>
                                      <td>'.$ds_titulo.'  </td>
                                      <td>'.$fe_clase.'</td>
                                      <td>'.$obliga.'</td>
                                      <td> ';
                                      # Verificamos si la fecha aun no ha pasado
                                      $rowe = RecuperaValor("SELECT DATEDIFF('".ValidaFecha($fe_clase)."', CURDATE())");
                                      if($rowe[0]<0){
                                        if (!empty($rasis[0])) {

                                            echo" $rasis[2]";
                                                        /*
                                                          echo "<a href=\"form-x-editable.html#\" id=\"sex_".$fl_clase."\" data-type=\"select\" data-pk=\"1\" data-value=\"\" data-original-title=\"Select sex\"> $ds_rasis[2] </a>";

                                                          echo"<script>

                                                             // DO NOT REMOVE : GLOBAL FUNCTIONS!
                                                            $(document).ready(function() {
                                                              pageSetUp();
                                                              $(\"#sex_".$fl_clase."\").editable({
                                                                  prepend: \"".$ds_rasis[2]."\",
                                                                  source: [{
                                                                    value: 2,
                                                                    text: 'Present'
                                                                  },{
                                                                    value: 3,
                                                                    text: 'Late'
                                                                  }

                                                                  ],
                                                                  display: function (value, sourceData) {
                                                                    var colors = {
                                                                      \"\": \"gray\",
                                                                      1: \"green\",
                                                                      2: \"blue\"
                                                                    }, elem = $.grep(sourceData, function (o) {
                                                                        return o.value == value;
                                                                      });

                                                                    if (elem.length) {
                                                                      //alert(value);
                                                                      $(this).text(elem[0].text).css(\"color\", colors[value]);

                                                                                                        if(value){
                                                                                                            var clave=".$p_alumno.";
                                                                                                            var valor=value;
                                                                                                            var fl_clase=".$fl_clase.";
                                                                                                             $.ajax({
                                                                                                                type: 'POST',
                                                                                                                url : 'asignar_asistencia.php',
                                                                                                                async: false,
                                                                                                                data: 'valor='+valor+
                                                                                                                        '&fl_clase='+fl_clase+
                                                                                                                        '&fl_usuario='+clave,
                                                                                                                success: function(data) {

                                                                                                                }
                                                                                                                });

                                                                                                        }

                                                                    } else {
                                                                      $(this).empty();
                                                                    }
                                                                  }
                                                                });
                                                               });
                                                              </script>
                                                              ";
                                                                */



                                        }
                                        else {
                                          $fecha_clase = RecuperaValor("SELECT fe_clase FROM k_clase WHERE fl_semana = " . $fl_semana . " AND fl_grupo = $fl_grupo");
                                          $diferencia_fechas = strtotime(!empty($fecha_clase[0])?$fecha_clase[0]:NULL) + 1200 - time();
                                          if ($diferencia_fechas <= 0) {
                                                $ds_rasis = RecuperaValor("SELECT nb_estatus FROM c_estatus_asistencia d WHERE cl_estatus_asistencia=1");

                                                if($ds_rasis[0]){
                                                        echo "<a href=\"form-x-editable.html#\" id=\"sex_".$fl_clase."\" data-type=\"select\" data-pk=\"1\" data-value=\"\" data-original-title=\"Select\"> $ds_rasis[0] </a> ";

                                                          echo"<script>
                                                               $(document).ready(function() {
                                                              pageSetUp();
                                                                $(\"#sex_".$fl_clase."\").editable({
                                                                  prepend: \"".$ds_rasis[0]."\",
                                                                  source: [
                                                                  {
                                                                    value: 2,
                                                                    text: 'Present'
                                                                  },{
                                                                    value: 3,
                                                                    text: 'Late'
                                                                  }

                                                                  ],
                                                                  display: function (value, sourceData) {
                                                                    var colors = {
                                                                      \"\": \"gray\",
                                                                      1: \"green\",
                                                                      2: \"blue\"
                                                                    }, elem = $.grep(sourceData, function (o) {
                                                                        return o.value == value;
                                                                      });

                                                                    if (elem.length) {
                                                                      //alert(value);
                                                                      $(this).text(elem[0].text).css(\"color\", colors[value]);
                                                                                                            if(value){
                                                                                                                var clave=".$p_alumno.";
                                                                                                                var valor=value;
                                                                                                                var fl_clase=".$fl_clase.";


                                                                                                                 $.ajax({
                                                                                                                    type: 'POST',
                                                                                                                    url : 'asignar_asistencia.php',
                                                                                                                    async: false,
                                                                                                                    data: 'valor='+valor+
                                                                                                                            '&fl_clase='+fl_clase+
                                                                                                                            '&fl_usuario='+clave,
                                                                                                                    success: function(data) {

                                                                                                                    }
                                                                                                                    });
                                                                                                            }


                                                                    } else {
                                                                      $(this).empty();
                                                                    }
                                                                  }
                                                                });
                                                                });
                                                              </script>
                                                              ";
                                                }


                                           }
                                          else
                                            echo "&nbsp;";
                                        }#end else
                                      }
                                      else{
                                        echo "";
                                      }
                                      echo '</td>
                                      <td>';
                                      if (!empty($row[0])) {
                                          $suma_cal_g += $row[3];
                                          $suma_cal_t += $row[3];
                                          $factor_promedio_g++;
                                          $factor_promedio_t++;
                                          echo $row[0];

                                          echo"&nbsp;&nbsp;<a class='text-small text-muted' style='float:right;font-size:80%' href='javascript:ResetGrading($fl_semana,$p_alumno)'><i class='fa fa-times-circle' aria-hidden='true'></i> Reset</a>";
                                      }
                                      $fecha_clase = RecuperaValor("SELECT fe_clase FROM k_clase WHERE fl_semana = " . $fl_semana. " AND fl_grupo = $fl_grupo");
                                      $diferencia_fechas = strtotime(!empty($fecha_clase[0])?$fecha_clase[0]:NULL) + 1200 - time();




                                      /* Solo hay una calificacion por semana en las extras no se le assigna */
                                      if(!empty($p_admin)){
                                        if($diferencia_fechas <= 0 AND $fg_obligatorio == '1' AND $fg_adicional== '0' AND empty($row[0])) {

                                            if(!empty($fg_rubric)){
                                                echo "<a href='javascript:AssignGrade($row3[5],$p_alumno);' class='txt-color-red'>Assign Grade</a> <i class='fa fa-warning'></i>";
                                            }
                                        }
                                        else
                                          echo "&nbsp;";
                                      }
                                      if(empty($fg_rubric)){
                                          echo"<span class='text-success'><i>Evaluation not required.</i></span>";
                                      }
                                      echo '</td>';

                                      if((!empty($fg_es_alumno_nuevo))&&(!empty($fg_rubric))){ //muestra btn de la rubric
                                          echo "<td class='text-center'> <a href='javascript:void(0);'  class='btn btn-default' onclick='PresentaRubric($fl_leccion,$p_programa,$p_alumno,$fl_grupo,$fl_semana);' ><i class='fa fa-calendar'></i>&nbsp;Rubric </a></td>";
                                      }else{
                                          echo"<td></td>";
                                      }

                                        echo'
                                    </tr>';
                                  }

                              }
                              else{
                                $Query = "SELECT DISTINCT fl_clase, " . ConsultaFechaBD('fe_clase', FMT_CAPTURA) . " fe_clase, ";
                                $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN) . " hr_clase, ";
                                $Query .= "fg_obligatorio, fg_adicional,fl_grupo ";
                                $Query .= "FROM k_clase a WHERE a.fl_semana=" . $fl_semana. " ";
                                $Query .= "ORDER BY fl_clase ";

                                $rss = EjecutaQuery($Query);
                                for($r=0;$roww = RecuperaRegistro($rss);$r++){
                                    $fl_grupoquery = $roww['fl_grupo'];
                                    #search grupo si existe.
                                    $QueryGroup = "SELECT fl_grupo FROM c_grupo where fl_grupo=$fl_grupoquery and no_alumnos>0 ";
                                    $rowgrou = RecuperaValor($QueryGroup);
                                    if($rowgrou['fl_grupo'])
                                    {

                                                  switch ($roww[3]) {
                                                      case '0':
                                                          $obliga = ObtenEtiqueta(17);
                                                          break;
                                                      case '1':
                                                          $obliga = ObtenEtiqueta(16);
                                                          break;
                                                      default:
                                                          $obliga = '';
                                                  }
                                                  if ($roww[4] == '1') {
                                                      $no_semana = '';
                                                      $ds_titulo = ObtenEtiqueta(538);
                                                  }

                                                  echo '
                                                  <tr>
                                                    <td>'.$no_semana.'</td>
                                                    <td>'.$ds_titulo.'  </td>
                                                    <td>'.$roww[1].'</td>
                                                    <td>'.$obliga.'</td>
                                                    <td>';

                                                  # Consulta el estatus de asistencia a live session
                                                  $Query = "SELECT a.fl_live_session, a.fl_usuario, b.nb_estatus, d.fl_semana
                                                            FROM k_live_session_asistencia a, c_estatus_asistencia b, k_live_session c, k_clase d
                                                            WHERE a.cl_estatus_asistencia = b.cl_estatus_asistencia
                                                            AND a.fl_live_session = c.fl_live_session
                                                            AND c.fl_clase = d.fl_clase
                                                            AND d.fl_semana = " . $fl_semana. "
                                                            AND a.fl_usuario = $p_alumno";
                                                  $rasis = RecuperaValor($Query);
                                        if (!empty($rasis[0])) {

                                            echo " $rasis[2]"; //present
                                        }



                                        echo '</td>
                                                    <td>';
                                        if (empty($fg_rubric)) {
                                            echo "<span class='text-success'><i>Evaluation not required.</i></span>";
                                        }
                                        echo '
                                                    </td>
                                                    <td></td>
                                                   </tr>';

                                    }#end exits group;


                                }
                              }

                #Recupermos las clases de los grupos y las pintamos.
                $Query="SELECT DISTINCT c.fl_clase_grupo," . ConsultaFechaBD('c.fe_clase', FMT_CAPTURA) . " fe_clase, c.fg_obligatorio,fg_adicional, ''fl_entrega_semanal, a.fl_grupo, '1' fg_grupo_global, e.fl_term ,c.nb_clase,a.nb_grupo
                    ,c.fe_clase feclass
                    FROM c_grupo a
                    JOIN k_alumno_grupo b ON b.fl_grupo=a.fl_grupo
                    JOIN k_clase_grupo c ON c.fl_grupo=a.fl_grupo
                    JOIN k_semana_grupo d ON d.fl_semana_grupo=c.fl_semana_grupo
                    JOIN k_grupo_term e ON e.fl_grupo= a.fl_grupo
                    AND b.fl_alumno = $p_alumno
                    AND e.fl_term=$fl_term
                    AND d.no_semana=$no_semana
                    ORDER BY c.fl_clase_grupo ";

                $rp=RecuperaValor($Query);

                $fl_clase_grupo=!empty($rp[0])?$rp[0]:NULL;
                if($fl_clase_grupo){
                  $nb_clase=$rp['nb_clase'];
                  $nb_grupo=$rp['nb_grupo'];
                  $fe_clase=$rp['fe_clase'];
                  $obliga=$rp['fg_obligatorio'];
                  $feclass = $rp['feclass'];

                  if($obliga=='1')
                    $obliga= ObtenEtiqueta(16);
                  else
                    $obliga= ObtenEtiqueta(17);

                  #Recupermaos la asistencia. del alumno.
                  $Query="SELECT c.nb_estatus,(SELECT nb_estatus FROM c_estatus_asistencia WHERE cl_estatus_asistencia=1)
                                          FROM k_live_session_grupal a
                      JOIN  k_live_session_asistencia_gg b ON b.fl_live_session_gg=a.fl_live_session_grupal
                      JOIN c_estatus_asistencia c ON c.cl_estatus_asistencia=b.cl_estatus_asistencia_gg
                      WHERE fl_usuario=$p_alumno
                      AND a.fl_clase_grupo=$fl_clase_grupo ";

                  $to=RecuperaValor($Query);

                  $nb_asustencia=!empty($to['nb_estatus'])?$to['nb_estatus']:NULL;
                  $nb_ausent=!empty($to[1])?$to[1]:NULL;

                  if(empty($nb_asustencia))
                    $nb_asustencia=$nb_ausent;

                    $Queryfecha = "Select CURDATE() ";
                    $rowfe = RecuperaValor($Queryfecha);
                    $fe_actual = str_texto($rowfe[0]);
                    $fe_actual = strtotime('+0 day', strtotime($fe_actual));
                    $fe_actual = date('Y-m-d', $fe_actual);

                    if ($feclass < $fe_actual) {
                        if (empty($nb_asustencia)) {
                            $nb_asustencia = "Absent";
                            $nb_ausent="Absent";
                        }


                    }

                  echo '
                                  <tr>
                                    <td> '.$no_semana.'  </td>
                                    <td>'.ObtenEtiqueta(2522).' <br><small class="text-muted/">'.$nb_grupo.'</small> <br><small class="text-muted/">'.$nb_clase.'</small> </td>
                                    <td>'.$fe_clase.'</td>
                                    <td>'.$obliga.'</td>
                  <td>';

                  if(!empty($nb_asustencia)&&($nb_asustencia=='Absent')){

                      echo'
                      <a href="form-x-editable.html#" id="sex_g'.$fl_clase_grupo.'" data-type="select" data-pk="1" data-value="" data-original-title="Select">'.$nb_asustencia.'</a>

                      <script>
                       $(document).ready(function() {
                        pageSetUp();
                        $("#sex_g'.$fl_clase_grupo.'").editable({
                          prepend: "'.$nb_asustencia.'",
                          source: [{
                                value: 2,
                                text: "Present"
                              },{
                                value: 3,
                                text: "Late"
                              }
                              ],
                          display: function (value, sourceData) {
                          var colors = {
                            "": "gray",
                            1: "green",
                            2: "blue"
                          }, elem = $.grep(sourceData, function (o) {
                              return o.value == value;
                            });

                          if (elem.length) {
                            //alert(value);
                            $(this).text(elem[0].text).css("color", colors[value]);
                                                           if(value){
                                                                var clave='.$p_alumno.';
                                                                var valor=value;
                                                                var fl_clase='.$fl_clase_grupo.';
                                                                 $.ajax({
                                                                    type: "POST",
                                                                    url : "asignar_asistencia.php",
                                                                    async: false,
                                                                    data: "valor="+valor+
                                                                            "&fl_clase="+fl_clase+
                                                                            "&fl_usuario="+clave+
                                                                            "&fg_gg=1",
                                                                    success: function(data) {
                                                                        $("#muestra_rubrics").html(data);
                                                                    }
                                                                    });
                                                            }

                          } else {
                            $(this).empty();
                          }
                        }
                        });
                      });
                    </script>';
                  }else{
                                        echo $nb_asustencia;

                                    }
                 echo'
                  </td>
                                    <td></td>
                                    <td></td>
                                   </tr>';

                }

                              /** Global classes attendance and groups */
                             $Query="SELECT
                                          B.ds_clase nb_clase,
                                          C.ds_titulo nb_grupo,
                                          ".ConsultaFechaBD('C.fe_clase', FMT_CAPTURA)." fe_clase,
                                          C.fg_obligatorio fg_obligatorio,
                                          (SELECT nb_estatus FROM c_estatus_asistencia WHERE cl_estatus_asistencia = D.cl_estatus_asistencia_cg) nb_ausencia,
                                          (SELECT nb_estatus FROM c_estatus_asistencia WHERE cl_estatus_asistencia = D.cl_estatus_asistencia_cg) nb_ausent
                                      FROM k_alumno_cg A
                                      JOIN c_clase_global B ON(A.fl_clase_global = B.fl_clase_global)
                                      JOIN k_clase_cg C ON(A.fl_clase_global = C.fl_clase_global)
                                      JOIN k_live_session_asistencia_cg D ON(A.fl_usuario=D.fl_usuario)
                                      JOIN k_alumno_term E ON(A.fl_usuario = E.fl_alumno)
                                      WHERE A.fl_usuario = $p_alumno AND E.fl_term = $fl_term AND C.no_orden = $no_semana ORDER BY fl_clase_cg";

                              $rp=RecuperaValor($Query);

                              $fl_clase_grupo=!empty($rp[0])?$rp[0]:NULL;

                              if($fl_clase_grupo){
                                $nb_clase=$rp['nb_clase'];
                                $nb_grupo=$rp['nb_grupo'];
                                $fe_clase=$rp['fe_clase'];
                                $obliga=$rp['fg_obligatorio'];
                                $nb_asustencia=$rp['nb_ausencia']??NULL;
                                $nb_ausent=$rp['nb_ausent']??NULL;

                                if($obliga==1)
                                  $obliga= ObtenEtiqueta(16);
                                else
                                  $obliga= ObtenEtiqueta(17);

                                if(empty($nb_asustencia))
                                  $nb_asustencia=$nb_ausent;

                                echo '<tr>
            <td> '.$no_semana.'</td>
            <td> Global Class <br><small class="text-muted/">Group: '.$nb_grupo.'</small> <br><small class="text-muted/">Class: '.$nb_clase.'</small> </td>
            <td>'.$fe_clase.'</td>
            <td>'.$obliga.'</td>
            <td>'; /** This "Clase Global" needs to be changed to ObtenEtiqueta(#) */

                                if(!empty($nb_asustencia)&&($nb_asustencia=='Absent')){

                                  echo'<a href="form-x-editable.html#" id="sex_g'.$fl_clase_grupo.'" data-type="select" data-pk="1" data-value="" data-original-title="Select">'.$nb_asustencia.'</a>
        <script>
        $(document).ready(function() {
            pageSetUp();
            $("#sex_g'.$fl_clase_grupo.'").editable({
            prepend: "'.$nb_asustencia.'",
            source: [{
                        value: 2,
                        text: "Present"
                    },{
                        value: 3,
                        text: "Late"
                    }
            ],
            display: function (value, sourceData) {
            var colors = {
                "": "gray",
                1: "green",
                2: "blue"
            }, elem = $.grep(sourceData, function (o) {
                return o.value == value;
                });

            if (elem.length) {
                //alert(value);
                $(this).text(elem[0].text).css("color", colors[value]);
                if(value){
                var clave='.$p_alumno.';
                var valor=value;
                var fl_clase='.$fl_clase_grupo.';
                $.ajax({
                    type: "POST",
                    url : "asignar_asistencia.php",
                    async: false,
                    data: "valor="+valor+
                            "&fl_clase="+fl_clase+
                            "&fl_usuario="+clave+
                            "&fg_gg=1",
                    success: function(data) {
                        $("#muestra_rubrics").html(data);
                    }
                });
                }
            } else {
                $(this).empty();
            }
            }
        });
        });
        </script>';
                                } else {
                                  echo $nb_asustencia;
                                }
                                echo'</td>
        <td></td>
        <td></td>
        </tr>';
                              }

                            }



                          }
                echo '    </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <script>
                $(document).ready(function(){
                  $("#tbl_term_'.$fl_term.'").dataTable({"bSort": false, "bLengthChange": false, "bPaginate": false,});
                });
                </script>';


         echo"<div id='presenta_rubric'></div>
                 <script>
                            function PresentaRubric(fl_leccion,fl_programa,fl_alumno,fl_grupo,fl_semana){

                               var fl_leccion=fl_leccion;
                               var fl_programa=fl_programa;
                               var fl_alumno=fl_alumno;
                 var fl_grupo=fl_grupo;
                               var fl_semana=fl_semana;
                               //alert(fl_leccion);

                                $.ajax({
                                     type: 'POST',
                                     url: 'presenta_rubric.php',
                                     data: 'fl_leccion='+fl_leccion+
                                           '&fl_programa='+fl_programa+
                       '&fl_grupo='+fl_grupo+
                       '&fl_semana='+fl_semana+
                                           '&fl_alumno='+fl_alumno,
                                     async: false,
                                     success: function (html) {
                                         $('#presenta_rubric').html(html);
                                     }
                                 });




                            }

                  </script>";



            }
          echo '</div>
        </div>
        <!-- end widget content -->
      </div>
      <!-- end widget div -->
    </div>
    <!-- end widget -->
  </article>';
}




function ContratosDetalles($cl_sesion, $fg_opcion_pago, $fl_programa, $no_contrato=0){

  # Definimos la opcion de pago
  switch($fg_opcion_pago){
    case 1: $no_payments = 'no_a_payments'; $mn_due = 'mn_a_due'; $mn_paid = 'mn_a_paid'; break;
    case 2: $no_payments = 'no_b_payments'; $mn_due = 'mn_b_due'; $mn_paid = 'mn_b_paid'; break;
    case 3: $no_payments = 'no_c_payments'; $mn_due = 'mn_c_due'; $mn_paid = 'mn_c_paid'; break;
    case 4: $no_payments = 'no_d_payments'; $mn_due = 'mn_d_due'; $mn_paid = 'mn_d_paid'; break;
  }

  # Obtenemos el numero de pagos
  $row = RecuperaValor("SELECT no_semanas, $no_payments, b.$mn_paid FROM c_programa a, k_programa_costos b WHERE a.fl_programa=b.fl_programa AND a.fl_programa=$fl_programa");
  $no_semanas = $row[0];
  $no_x_payments = $row[1];
  $row4 = RecuperaValor("SELECT mn_costs, mn_discount, b.$mn_due  FROM k_app_contrato b WHERE cl_sesion='$cl_sesion'");
  $mn_x_due = $row4[2];
  $mn_paid = ($row[2] + $row4[0]) - $row4[1];

  # Usamos la formula
  $meses_x_curso = $no_semanas / 4;
  if ($meses_x_curso >= 12)
    $no_pagos_year = 12 / ($meses_x_curso / $no_x_payments);
  else
    $no_pagos_year = 1;
  $payment_x_year = $mn_x_due * $no_pagos_year;

  # Obtenemos todos el numero de contratos
  $row1 = RecuperaValor("SELECT COUNT(*) FROM k_app_contrato WHERE cl_sesion='$cl_sesion'");
  $no_contratos = $row1[0];
  if ($no_contratos == 1) {
    # Acualizamos la informacion
    EjecutaQuery("UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=1");
    // echo "UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=1";
  } else {
    # mas de un contrato
    for ($i = 1; $i <= $no_contratos; $i++) {
      if ($i == $no_contratos) {
        $no_contratos = $no_contratos - 1;
        if ($no_contratos == 0)
          $no_contratos = 1;
        $payment_x_year = /*$mn_paid-*/ ($payment_x_year * $no_contratos);
      } else
        $payment_x_year = $payment_x_year;
      # Actualiamos el monto del contrato
      EjecutaQuery("UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=$i");
      // echo "UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=$i <br>";
    }
  }
}

/**
 * EGMC 20160517
 * Obtiene el arreglo con las rutas fisicas donde hay clases
 */
global $pathsProviders;
$pathsProviders = require_once ROOT . DS . 'lib' . DS . 'com_path_providers.inc.php';

/**
 * EGMC 20160517
 * Funcion de autocarga que es automaticamente invocada
 * en caso de que se este intentando utilizar una clase/interfaz que todavía no haya sido definida.
 * NOTA: el nombre del archivo sera el mismo que el de la clase
 * @param type class nombre de la clase
 * @return boolean
 */
spl_autoload_register(function ($class) {

  // print_r("gabriel".$class);

  /**
   * EGMC 20160517
   * Obtiene el arreglo con las rutas fisicas donde hay clases
   */
  global $pathsProviders;
  /**
   * EGMC 20160517
   * Recorre el arreglo con las rutas fisicas,
   * Verifica que le archivo exista
   * si existe hace un carga el archivo y retorna true
   */
  foreach ($pathsProviders as $path) {
    if (file_exists($path . $class . '.php')) {
      require_once $path . $class . '.php';
      return true;
    }
  }
  /**
   * EGMC 20160517
   * Agregar un error log para ver por que no entra
   */
  //throw new Exception('Clase no encontrada!!!');
});




/**
 *
 * MJD  02-12_2016
 *
 * # Funcion para generar contarto FAME por Instituto
 *
 */

function genera_ContratoFame($p_instituto, $opc,$fl_template,$p_usuario,$ds_fname='',$ds_lname='') {

    # Variable Initialization to avoid errors
    $fname_teacher=NULL;
    $lname_teacher=NULL;
    $fe_creacion_instituto=NULL;

    # Recupera datos del template del documento
    switch($opc)
    {
        case 1: $campo = "ds_encabezado"; break;
        case 2: $campo = "ds_cuerpo"; break;
        case 3: $campo = "ds_pie"; break;
        case 4: $campo = "nb_template"; break;
    }
    $Query  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
    $row = RecuperaValor($Query);
    $cadena = str_uso_normal($row[0]);



    #Recuperamos el perfil en fame
    $Query="SELECT fl_perfil_sp FROM c_usuario WHERE fl_usuario=$p_usuario ";
    $row=RecuperaValor($Query);
    $fl_perfil_fame=$row[0]??NULL;





    #Recuperamos datos generales del iNSTITUTO
    $Query="SELECT I.ds_instituto,I.ds_codigo_pais,I.ds_codigo_area,I.no_telefono,fl_usuario_sp,fe_creacion,fg_tiene_plan  ";
    $Query.="FROM c_instituto I ";
    $Query.="JOIN c_pais P ON P.fl_pais=I.fl_pais ";
    $Query.="WHERE I.fl_instituto=$p_instituto ";
    $row=RecuperaValor($Query);
    $nb_instituto=$row['ds_instituto']??NULL;
    $ds_codigo_pais=$row['ds_codigo_pais']??NULL;
    $ds_codigo_area=$row['ds_codigo_area']??NULL;
    $no_telefono=$row['no_telefono']??NULL;
    $fl_usuario_admin=$row['fl_usuario_sp']??NULL;
    $fg_tiene_plan=$row['fg_tiene_plan']??NULL;
    #para trials.
	if(!empty($row['fe_creacion']))
      $fe_creacion_instituto=GeneraFormatoFecha($row['fe_creacion']);

   /* if($fg_tiene_plan==1){
      #Recupermaos la fecha que inicio su contrato.
      $Query="SELECT fe_periodo_inicial FROM k_current_plan WHERE fl_instituto=$p_instituto ";
      $row=RecuperaValor($Query);
      $fe_creacion_instituto=GeneraFormatoFecha($row['fe_periodo_inicial']);

    }
   */




    #Recuperamos datos del admin
    $Query="SELECT ds_nombres,ds_apaterno,ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_admin ";
    $row=RecuperaValor($Query);
    $fname_admin=$row['ds_nombres']??NULL;
    $lname_admin=$row['ds_apaterno']??NULL;
    $ds_email=$row['ds_email']??NULL;


    #Recuperamos datos del estufdiante
    if($fl_perfil_fame==PFL_ESTUDIANTE_SELF){

      $fname_admin=null;
      $lname_admin=null;

      #Recuperamos datos del admin
      $Query="SELECT ds_nombres,ds_apaterno,fe_alta FROM c_usuario WHERE fl_usuario=$p_usuario ";
      $row=RecuperaValor($Query);
      $fname_student=$row['ds_nombres'];
      $lname_student=$row['ds_apaterno'];
      $fe_creacion_instituto=GeneraFormatoFecha($row['fe_alta']);


      #vERIFICAMOS SI TIENE LEGAL GUARDIAN
      $Query="SELECT ds_fname,ds_lname FROM k_responsable_alumno WHERE fl_usuario=$p_usuario ";
      $row=RecuperaValor($Query);
      $fname_legal=!empty($row['ds_fname'])?$row['ds_fname']:NULL;
      $lname_legal=!empty($row['ds_lname'])?$row['ds_lname']:NULL;

      $nb_legal_guardian=$fname_legal." ".$lname_legal;

    } else {
      $nb_legal_guardian=NULL;
    }

    #Recuperamos datos del teacher
    if($fl_perfil_fame==PFL_MAESTRO_SELF){

         $fname_admin=null;
         $lname_admin=null;

        #Recuperamos datos del admin
        $Query="SELECT ds_nombres,ds_apaterno,fe_alta FROM c_usuario WHERE fl_usuario=$p_usuario ";
        $row=RecuperaValor($Query);
        $fname_teacher=!empty($row['ds_nombres'])?$row['ds_nombres']:NULL;
        $lname_teacher=!empty($row['ds_apaterno'])?$row['ds_apaterno']:NULL;
		$fe_creacion_instituto=GeneraFormatoFecha($row['fe_alta']);


    }

    #Quiere decir que
	if($p_usuario=='T'){

      $fname_teacher=$ds_fname;
      $lname_teacher=$ds_lname;

      $lname_student=null;
      $fname_student=null;
      $fname_admin=null;
      $lname_admin=null;

      #Obtenemos fecha actual :
      $Query = "Select CURDATE() ";
      $row = RecuperaValor($Query);
      $fe_actual = str_texto($row[0]);
      $fe_actual=strtotime('+0 day',strtotime($fe_actual));
      $fe_creacion_instituto= GeneraFormatoFecha(date('Y-m-d',$fe_actual));
	}
	if($p_usuario=='S'){

    $fname_student=$ds_fname;
    $lname_student=$ds_lname;

		$lname_teacher=null;
		$fname_teacher=null;
		$fname_admin=null;
    $lname_admin=null;

		#Obtenemos fecha actual :
		$Query = "Select CURDATE() ";
		$row = RecuperaValor($Query);
		$fe_actual = str_texto($row[0]);
		$fe_actual=strtotime('+0 day',strtotime($fe_actual));
		$fe_creacion_instituto= GeneraFormatoFecha(date('Y-m-d',$fe_actual));

	} else {
    $fname_student=NULL;
    $lname_student=NULL;
  }

    /**
     * Varibles del template
     */


    # Sustituye variables con datos del instituto y su admin.
    $cadena = str_replace("#fame_partner_school#", $nb_instituto,$cadena);
    $cadena = str_replace("#fame_date_signed#",  $fe_creacion_instituto,$cadena);

    $cadena = str_replace("#fame_fname_admin#", $fname_admin,$cadena);
    $cadena = str_replace("#fame_lname_admin#", $lname_admin,$cadena);

    $cadena = str_replace("#fame_fname_student#", $fname_student,$cadena);
    $cadena = str_replace("#fame_lname_student#", $lname_student,$cadena);
    $cadena = str_replace("#fame_legal_guardian#",$nb_legal_guardian,$cadena);

    $cadena = str_replace("#fame_fname_teacher#", $fname_teacher,$cadena);
    $cadena = str_replace("#fame_lname_teacher#", $lname_teacher,$cadena);



  return $cadena;
}


/**
 *
 * MJD  02-12_2016
 *
 * # Funcion para generar contarto Quiz por certificado
 *
 */
function genera_TranscripQuiz($clave, $opc, $correo=False, $firma=False, $no_contrato=1,$fl_template='',$ds_cve='') {


    # Recupera datos del template del documento
    switch($opc)
    {
        case 1: $campo = "ds_encabezado"; break;
        case 2: $campo = "ds_cuerpo"; break;
        case 3: $campo = "ds_pie"; break;
        case 4: $campo = "nb_template"; break;
    }
    $Query  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
    $row = RecuperaValor($Query);
    $cadena = str_uso_normal($row[0]);

    #Recuperamos datos generales del iNSTITUTO
    $Query="SELECT I.ds_instituto,I.ds_codigo_pais,I.ds_codigo_area,I.no_telefono,fl_usuario_sp  ";
    $Query.="FROM c_instituto I ";
    $Query.="JOIN c_pais P ON P.fl_pais=I.fl_pais ";
    $Query.="WHERE I.fl_instituto=$clave ";
    $row=RecuperaValor($Query);
    $nb_instituto=$row['ds_instituto'];
    $ds_codigo_pais=$row['ds_codigo_pais'];
    $ds_codigo_area=$row['ds_codigo_area'];
    $no_telefono=$row['no_telefono'];
    $fl_usuario=$row['fl_usuario_sp'];


    #Recuperamos datos del admin
    $Query="SELECT ds_nombres,ds_apaterno,ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
    $row=RecuperaValor($Query);
    $ds_nombres=$row['ds_nombres'];
    $ds_apaterno=$row['ds_apaterno'];
    $ds_email=$row['ds_email'];


    /**
     * Varibles del template
     * #firstname_admin# #lastname_admin#
     * #school_name#
     * #email_address#
     * #phone_number#
     */


    # Sustituye variables con datos del instituto y su admin.
    $cadena = str_replace("#firstname_admin#", $ds_nombres,$cadena);
    $cadena = str_replace("#lastname_admin#", $ds_apaterno,$cadena);
    $cadena = str_replace("#school_name#", $nb_instituto,$cadena);
    $cadena = str_replace("#email_address#", $ds_email,$cadena);
    $cadena = str_replace("#phone_number#", $no_telefono,$cadena);




  return ($cadena);
}


function GeneraFormatoFecha($p_fecha)
{

  #DAMOS FORMATO MES,dia A?O.(sep 29, 2016)
  $date = date_create($p_fecha);
  $p_fecha = date_format($date, 'F j, Y');


  return $p_fecha;
}

# Obtiene el Avatars y Foto Header
function ObtenAvatarUsrFa_Va($usuario, $p_avatars=true){
  $fl_perfil = ObtenPerfil($usuario);
  # Vanas
  if ($fl_perfil == PFL_ESTUDIANTE || $fl_perfil == PFL_MAESTRO) {
    if ($fl_perfil == PFL_ESTUDIANTE) {
      $row = RecuperaValor("SELECT ds_ruta_avatar, ds_ruta_foto FROM c_alumno WHERE fl_alumno=" . $usuario);
      $ds_ruta_avatar = $row[0];
      $ds_ruta_avatar = PATH_ALU_IMAGES."/avatars/".$ds_ruta_avatar;
      $ds_ruta_foto = $row[1];
      $ds_ruta_foto = PATH_ALU_IMAGES . "/pictures/" . $ds_ruta_foto;
    } else {
      $row = RecuperaValor("SELECT ds_ruta_avatar, ds_ruta_foto FROM c_maestro WHERE fl_maestro=" . $usuario);
      $ds_ruta_avatar = $row[0];
      $ds_ruta_avatar = PATH_MAE_IMAGES."/avatars/".$ds_ruta_avatar;
      $ds_ruta_foto = $row[1];
      $ds_ruta_foto = PATH_MAE_IMAGES."/pictures/".$ds_ruta_foto;
    }
  }
  # FAME
  else {
    # Recupera el perfil del usuario
    $row0 = RecuperaValor("SELECT fl_perfil_sp, fl_instituto FROM c_usuario WHERE fl_usuario=$usuario");
    $fl_perfil = !empty($row0[0])?$row0[0]:NULL;
    $fl_instituto = !empty($row0[1])?$row0[1]:NULL;

    # Ruta del avatar
    $ruta = PATH_SELF_UPLOADS . "/" . $fl_instituto . "/USER_" . $usuario . "/";

    # Verifica si el usuario tiene un avatar
    if ($fl_perfil == PFL_MAESTRO_SELF) {
      $row = RecuperaValor("SELECT ds_ruta_avatar, ds_ruta_foto FROM c_maestro_sp WHERE fl_maestro_sp=$usuario");
    } else {
      if ($fl_perfil == PFL_ESTUDIANTE_SELF) {
        $row = RecuperaValor("SELECT ds_ruta_avatar, ds_ruta_foto FROM c_alumno_sp WHERE fl_alumno_sp=$usuario");
      } else {
        $row = RecuperaValor("SELECT ds_ruta_avatar, ds_ruta_foto FROM c_administrador_sp  WHERE fl_adm_sp=$usuario");
      }
    }
    $ds_ruta_avatar = $ruta.(!empty($row[0])?$row[0]:NULL);
    $ds_ruta_foto = $ruta.(!empty($row[1])?$row[1]:NULL);
  }

  # Si no hay avatar pone el default
  if (empty($row[0]))
    $ds_ruta_avatar = SP_IMAGES . "/" . IMG_T_AVATAR_DEF;
  # Si no hay foto header pone la default
  if (empty($row[1]))
    $ds_ruta_foto = PATH_N_COM_IMAGES . "/vanas-family-edutisse-header.jpg";
  # Depende del la foto que desea
  if ($p_avatars == true)
    return $ds_ruta_avatar;
  else
    return $ds_ruta_foto;
}





/**
 * MJD #funcion presenta rubic de como se va acalificar y cuando ya esta califiaco por un teacher.
 * @param
 *
 */
function PresentaRubric($fl_leccion,$fl_alumno,$fl_grupo='',$fl_semana='',$no_grado='',$fg_app_form=false,$back=false){




	if(empty($fg_app_form)){
		#Verificamos si ya esta calificado por el teacher.(si tiene fl_promedio semna,quees llave que hace refrencia c_calificacion_sp)
		$Query = "SELECT a.fl_promedio_semana FROM k_entrega_semanal a, k_semana b ";
		$Query .= "WHERE a.fl_semana = b.fl_semana AND a.fl_alumno=$fl_alumno AND b.fl_leccion=$fl_leccion  AND a.fl_semana=$fl_semana  ";
		$row=RecuperaValor($Query);
		$fg_calificado=!empty($row['fl_promedio_semana'])?$row['fl_promedio_semana']:NULL;

	}else{
		$Query="SELECT fl_promedio FROM c_sesion WHERE cl_sesion='$fl_alumno' ";
		$row=RecuperaValor($Query);
        $fg_calificado=$row['fl_promedio'];
	}

    $rubric = "";
    $ruta = PATH_HOME."/images/rubrics/";



    if(empty($fg_calificado)){

        $rubric.="
		<style>

		small, .small {
		font-size: 100% !important;
		}
		 .chart {
										/* height: 220px; */
										margin: auto !important;
									}
		</style>
		";

    if (empty($fg_app_form)) {
      $Query = "SELECT fl_criterio, no_valor FROM k_criterio_programa WHERE fl_programa = $fl_leccion ORDER BY no_orden ASC	";
    } else {
      $Query = "SELECT fl_criterio, no_valor FROM k_criterio_curso WHERE fl_programa = $fl_leccion ORDER BY no_orden ASC	";
    }
    #Recuperamos todos los criterios

    $rs_prin = EjecutaQuery($Query);
    $registros = CuentaRegistros($rs_prin);
    for ($i_prin = 1; $row_prin = RecuperaRegistro($rs_prin); $i_prin++) {

      $fl_criterio = $row_prin['fl_criterio'];
      $no_valor_criterio = $row_prin['no_valor'];

      $rs_nb_crit = RecuperaValor("SELECT nb_criterio FROM c_criterio WHERE fl_criterio = $fl_criterio");
      $nb_criterio = str_texto($rs_nb_crit[0]);



      $rubric .= "<div class='row' style='height:auto; padding-left:75px;'>";
      $rubric .= "<div class='col-md-1' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'>

									  <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>



										Criterion
									  </div>
									  <br/>
									  <div class='panel panel-default text-center' style='height:358px;'> <p style='margin: 0 0 0px;'>&nbsp;</p>
								        <span  style='color:#8FCAE5;font-size:15px;' class='text-center'>$no_valor_criterio% </span>
														<!--<section class='form-group' style='-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);margin-top: 188px;'>

															<label class='input' style='font-weight:bold;'>
																<input  class='form-control input-lg'  style=' border: 0px solid #ccc;' name='nb_criterio' id='nb_criterio' type='text' value='$nb_criterio' />
															</label>
														</section>-->

														<div style='font-size:18px; font-weight:bold; -webkit-transform: rotate(-90deg);margin-top: 111px;width: 215px;margin-left: -63px;'>$nb_criterio</div>
															<!--<div  class='panel-body text-center' style='writing-mode: vertical-lr; transform: rotate(180deg); font-size:18px; font-weight:bold; padding: 15px 29px 88px 50px;'>$nb_criterio</div>--->
									  </div>

							  </div>";


      $Query = "SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion_criterio ";
      $Query .= "	WHERE fl_instituto IS NULL ORDER BY no_equivalencia ASC ";
      $rs = EjecutaQuery($Query);
      $contador=0;
      for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
        $fl_calificacion_criterio = $row['fl_calificacion_criterio'];
        $cl_calificacion = $row['cl_calificacion'];
        $ds_calificacion = $row['ds_calificacion'];
        $fg_aprobado = $row['fg_aprobado'];
        $no_equivalencia = $row['no_equivalencia'];
        $no_min = number_format($row['no_min']);
        $no_max = number_format($row['no_max']);

        if ($no_max == 0)
          $ds_equivalencia = "No Uploaded";
        else
          $ds_equivalencia = $no_min . "% - " . $no_max . "%" . " ($cl_calificacion)";

        #Recupermaos la descripcion que tiene actualmente.
        $Query_c = "SELECT ds_descripcion,fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
        $row_c = RecuperaValor($Query_c);
        $ds_desc = str_texto($row_c[0]);
        $fl_criterio_fame = $row_c[1];

        #Recuperamos las imagenes por calificacion
        $Query_img = "SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame ";
        $row_img = RecuperaValor($Query_img);
        $nb_archivo_criterio = $row_img[0];
        $src_img = $ruta . $nb_archivo_criterio;

        $contador++;

        if (!empty($nb_archivo_criterio)) {
          $icono = "<a class='zoomimg' href='#'>
								<i class='fa fa-file-picture-o'></i>
								<span style='left:-300px;'>
								  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-bottom: -530px;'>
									<div class='modal-content' style='width:500px;height:500px;'>
									  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
										<img class='superbox-current-img' src='$src_img' style='width:494px;height:490px;'>
									  </div>
									</div>
								  </div>
								</span>
							  </a> ";
        } else {
          $icono = "";
        }




        $rubric .= "

							 <div class='col-md-2' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'>

							  <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
								 $ds_calificacion &nbsp;&nbsp;$icono
							  </div>
							  <br/>
							  <div class='panel panel-default' style='height:358px;'>
								<div class='panel-body text-center'>
								  <span  style='color:#8FCAE5;font-size:15px; '>$ds_equivalencia </span>  <p>&nbsp;</p>

								    <div class='chart' data-percent='$no_max' id='easy-pie-chart$contador".$fl_criterio_fame."'>
                                        <span class='percent' style='font:18px Arial;'>$no_max</span>
                                    </div>





											<div class='form-group text-left' style='padding-left:5px; padding-right:5px;'>
											   <div id='desc$contador".$fl_criterio_fame."'></div>
											   <hr>

												<div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
												  <small class='text-muted'><i>$ds_desc</i></small>
												</div>

											</div>
								</div>
							  </div>

							</div>



							";
        $rubric .= "

								 <script>
									$(document).ready(function () {
										$('#easy-pie-chart$contador".$fl_criterio_fame."').easyPieChart({
											animate: 2000,
											scaleColor: false,
											lineWidth: 7.5,
											lineCap: 'square',
											size: 100,
											trackColor: '#EEEEEE',
											barColor: '#92D099'
										});

										$('#easy-pie-chart$contador".$fl_criterio_fame."').css({
											width: 100 + 'px',
											height: 100 + 'px'
										});
										$('#easy-pie-chart$contador".$fl_criterio_fame." .percent').css({
											'line-height': 100 + 'px'
										})

									});
								</script>

							  ";


            }#end 2do query
            $rubric .= "   <div class='col-md-1' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'></div>";
            $rubric .= "</div>";
            $rubric .= "<br/>";


        }#end primer query.






    }
    else{




        if($back)
        $url="rubric_border.php";
        else
        $url="ajax/rubric_border.php";

        ###############Muestr rubric calificada
        #Recuperamos todos los criterios
        if(empty($fg_app_form)){
            $Query="SELECT fl_criterio, no_valor FROM k_criterio_programa_alumno WHERE fl_programa = $fl_leccion AND fl_alumno=$fl_alumno ORDER BY no_orden ASC	";
            $rs_prin = EjecutaQuery($Query);
        }else{

            $Query="SELECT fl_criterio, no_valor FROM k_criterio_curso WHERE fl_programa = $fl_leccion ORDER BY no_orden ASC	";
            $rs_prin = EjecutaQuery($Query);

        }


        $registros = CuentaRegistros($rs_prin);
        $cont1=0;
        $rubric=" ";
        $rubric.="
	<style>

		small, .small {
		font-size: 100% !important;
		}
		 .chart {
										/* height: 220px; */
										margin: auto !important;
									}

		.border{
				border: 2px solid  #3194DA;

		}

	</style>
	";
    for ($i_prin = 1; $row_prin = RecuperaRegistro($rs_prin); $i_prin++) {
      $fl_criterio = $row_prin['fl_criterio'];
      $no_valor_criterio = $row_prin['no_valor'];
      $rs_nb_crit = RecuperaValor("SELECT nb_criterio FROM c_criterio WHERE fl_criterio = $fl_criterio");
      $nb_criterio = str_texto($rs_nb_crit[0]);
      $cont1++;
      $rubric .= "<div class='row' >";
      $rubric .= "
       <div class='col-md-1' style='padding-right: 0px;'>
           <div class='col-md-12' style='padding-left: 1px;' >
                <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
                  Criterion
                </div>
                <br/>
                <div class='panel panel-default' style='height:412px;' >
				<p style='margin: 0 0 0px;'>&nbsp;</p>
								        <span  style='color:#8FCAE5;font-size:15px;' class='text-center'>$no_valor_criterio% </span>
                  <!--<div  class='panel-body text-center' style='writing-mode: vertical-lr; transform: rotate(180deg); font-size:18px; font-weight:bold; padding: 15px 19px 173px 50px;'>$nb_criterio</div>--->
                  <div style='font-size:18px; font-weight:bold; -webkit-transform: rotate(-90deg);margin-top: 165px;width: 215px;margin-left: -71px;'>$nb_criterio</div>
				</div>
            </div>
       </div>
       <div class='col-md-11' style='padding-left: 1px;'>";
      $Query = "SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion_criterio ";
      $Query .= "WHERE 1=1 and fl_instituto IS NULL ORDER BY fl_calificacion_criterio DESC ";
      $rs = EjecutaQuery($Query);
      $cont_crite = 0;
      for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
        $fl_calificacion_criterio = $row['fl_calificacion_criterio'];
        $cl_calificacion = $row['cl_calificacion'];
        $ds_calificacion = $row['ds_calificacion'];
        $fg_aprobado = $row['fg_aprobado'];
        $no_equivalencia = $row['no_equivalencia'];
        $no_min = number_format($row['no_min']);
        $no_max = number_format($row['no_max']);

        $cont_crite++;

        if ($no_max == 0)
          $ds_equivalencia = "No Uploaded";
        else
          $ds_equivalencia = $no_min . "% - " . $no_max . "%" . " ($cl_calificacion)";

        #Recupermaos la descripcion que tiene actualmente.
        $Query_c = "SELECT ds_descripcion,fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
        $row_c = RecuperaValor($Query_c);
        $ds_desc = str_texto($row_c[0]);
        $fl_criterio_fame = $row_c[1];

        #Recuperamos las imagenes por calificacion
        $Query_img = "SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame ";
        $row_img = RecuperaValor($Query_img);
        $nb_archivo_criterio = $row_img[0];
        $src_img = $ruta . $nb_archivo_criterio;
        $contador++;
        if (!empty($nb_archivo_criterio)) {
          $icono = "
          <a class='zoomimg' href='#' style='color:#000;text-decoration: none;' >
          <i class='fa fa-file-picture-o'></i>
          <span style='left:-300px;'>
            <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-bottom: -530px;'>
              <div class='modal-content' style='width:500px;height:500px;'>
                <div class='modal-body padding-5'  style='width:500px;height:500px;'>
                  <img class='superbox-current-img' src='$src_img' style='width:494px;height:490px;'>
                </div>
              </div>
            </div>
          </span>
          </a>";
        } else {
          $icono = "";
        }

        $rubric .= "
        <div class='col-md-2' style='padding-left: 3px;padding-right: 3px;'>
          <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
             $ds_calificacion &nbsp;&nbsp;$icono
          </div>
          <br/>
          <div class='panel panel-default' style='height: 412px;' id='divborder_cero_" . $fl_criterio . "_" . $cont_crite . "'>
           <div class='panel-body text-center'>
               <span  style='color:#8FCAE5;font-size:15px; '>$ds_equivalencia </span>  <p>&nbsp;</p>


				<div class='chart' data-percent='$no_max' id='easy-pie-charts$contador'>
					<span class='percent' style='font:18px Arial;'>$no_max</span>
				</div>

				 <script>
									$(document).ready(function () {
										$('#easy-pie-charts$contador').easyPieChart({
											animate: 2000,
											scaleColor: false,
											lineWidth: 7.5,
											lineCap: 'square',
											size: 100,
											trackColor: '#EEEEEE',
											barColor: '#B7B7B7'
										});

										$('#easy-pie-charts$contador').css({
											width: 100 + 'px',
											height: 100 + 'px'
										});
										$('#easy-pie-charts$contador .percent').css({
											'line-height': 100 + 'px'
										})

									});
								</script>





               <div class='form-group text-left' style='padding-left:5px; padding-right:5px;'>
                  <div id='desc$contador'></div>
                  <hr>
                  <div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
                    <small class='text-muted'><i>$ds_desc</i></small>
                  </div>
               </div>
                  <p style='margin-left: -5px;color:#999;'>&nbsp;</p>
				  <p style='margin-left: -5px;color:#999;margin-top: -8px;'>&nbsp;</p>
           </div>
           </div>
          </div>
           ";





            }#end 2do query

            ######################iNICIA COMENTARIOS DEL TEACHER##########################
            #Recupermaos la calificacion asignada por el estudiante.
            $porcentaje_equivalente=0;
            $no_calificacion_final=0;
            $ds_comentario_teacher="No comment";
            $fe_calificado="No date";
            $ds_comentario_final_teacher="No comment";
            $no_promedio_final=110;


            if(empty($fg_app_form)){
                #Recuperamos si sxite una calificacion asignada.
                $Query="SELECT ds_comentarios,no_porcentaje_equivalente,fe_modificacion FROM c_com_criterio_teacher_campus WHERE fl_criterio=$fl_criterio AND fl_alumno=$fl_alumno
			      AND fl_leccion=$fl_leccion  AND fl_semana=$fl_semana    ";
        $row = RecuperaValor($Query);
      } else {
        #Recuperamos si sxite una calificacion asignada del admin.
        $Query = "SELECT ds_comentarios,no_porcentaje_equivalente,fe_modificacion FROM c_com_criterio_admin WHERE fl_criterio=$fl_criterio AND cl_sesion='$fl_alumno'
			     AND fl_programa=$fl_leccion ";
                $row=RecuperaValor($Query);


            }


            $ds_comentario_criterio=str_texto($row[0]);
            $no_porcentaje_equivalente=$row[1];
            //$fe_asignacion_califi=ObtenFechaFormatoDiaMesAnioHora($row[2]);
            $fe_asignacion_califi= $row[2];
			if(!empty($fe_asignacion_califi)){
				$fe_modificacion=strtotime('+0 day',strtotime($fe_asignacion_califi));
				$fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
				#DAMOS FORMATO DIA,MES, AÑO.
				$date = date_create($fe_modificacion);
				$fe_asignacion_califi=ObtenEtiqueta(1680)." ".date_format($date,'F j, Y, g:i a');
			}else{
			    $fe_asignacion_califi="";

			}


      $rubric .= "
      <div class='col-md-2' style='padding-left: 3px;padding-right: 3px;'>
        <div class='well well-lg text-center' style='padding:2px;background: #F2F2F2;'>".ObtenEtiqueta(1664)."</div>
        <br/>
        <div class='panel panel-default' style='height: 412px;'>
            <div class='panel-body text-center'>
              <span  style='color:#8FCAE5;font-size:15px; '>&nbsp;</span>  <p>&nbsp;</p>



				<div class='chart' data-percent='$no_porcentaje_equivalente' id='easy-pie-chartm$cont1'>
					<span class='percent' style='font:18px Arial;'>$no_porcentaje_equivalente</span>
				</div>

				 <script>
									$(document).ready(function () {
										$('#easy-pie-chartm$cont1').easyPieChart({
											animate: 2000,
											scaleColor: false,
											lineWidth: 7.5,
											lineCap: 'square',
											size: 100,
											trackColor: '#EEEEEE',
											barColor: '#92D099'
										});

										$('#easy-pie-chartm$cont1').css({
											width: 100 + 'px',
											height: 100 + 'px'
										});
										$('#easy-pie-chartm$cont1 .percent').css({
											'line-height': 100 + 'px'
										})

									});
								</script>





                  <div class='form-group text-left' style='padding-left:5px; padding-right:5px;'>
                     <div id='desc$cont1'></div>
                       <hr>

                      <div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:-5px;'>
                        <small class='text-muted'><i>$ds_comentario_criterio</i></small>
                      </div>
                  </div>
			     <div class='text-left'>
                 <p style='margin-left: 4px;color:#999;'><i>$fe_asignacion_califi</i></p>
				</div>
           </div>
          </div>
        </div>
         ";


      $rubric .= "<div id='presenta_calculo_$cont1'></div>
			<script>
						function PintaBorder_".$cont1."(){
								 var rangeInput = $no_porcentaje_equivalente;
								 var fl_criterio=$fl_criterio;//identificador del criterio

								 $.ajax({
										type: 'POST',
										url: '$url',
										data: 'rangeInput='+rangeInput+
											  '&fl_criterio='+fl_criterio,

										async: true,
										success: function (html) {
											$('#presenta_calculo_$cont1').html(html);
										}
									});


                        }


                                           PintaBorder_".$cont1."();
			</script>



			";










      ###################finaliza comentarios del teacher################
      $rubric .= "</div>";
      $rubric .= "</div>";
      $rubric .= "<br/>";
    } #end primer query.


    #Presenta comentarios finales del teacher
    $rubric .= "
       <style>
        .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
            background-color: #fff !important;

            }
      </style>";


    if (empty($fg_app_form)) {
      #Recuperamos comentarios finales
      $Query = "SELECT ds_comentarios,fe_modificacion FROM c_com_criterio_teacher_campus
		    WHERE  fl_alumno=$fl_alumno AND  fl_leccion =$fl_leccion    AND fl_semana=$fl_semana  AND fg_com_final='1'  ";
      $row = RecuperaValor($Query);
    } else {

      #Recuperamos comentarios finales
      $Query = "SELECT ds_comentarios,fe_modificacion FROM c_com_criterio_admin
		    WHERE  cl_sesion='$fl_alumno' AND  fl_programa =$fl_leccion AND fg_com_final='1'  ";
      $row = RecuperaValor($Query);
    }
    $ds_comentario_final = str_texto($row[0]);
    //$fe_comentario_final=ObtenFechaFormatoDiaMesAnioHora($row[1]);
    $fe_comentario_final = $row[1];

    if (!empty($fe_comentario_final)) {
      $fe_modificacion = strtotime('+0 day', strtotime($fe_comentario_final));
      $fe_modificacion = date('Y-m-d H:i:s', $fe_modificacion);
      #DAMOS FORMATO DIA,MES, A?O.
      $date = date_create($fe_modificacion);
      $fe_comentario_final = date_format($date, 'F j, Y, g:i a');
    } else {
      $fe_comentario_final = "";
    }

    if (empty($fg_app_form)) {
      #Recupermaos la calificCION FINAL:
      $Que = "SELECT no_calificacion FROM k_calificacion_teacher_campus WHERE fl_alumno=$fl_alumno AND  fl_leccion =$fl_leccion  AND fl_semana=$fl_semana ";
      $r = RecuperaValor($Que);
      $no_clificacion_final = $r['no_calificacion'];
    } else {
      #Recupermaos la calificCION FINAL:
      $Que = "SELECT no_calificacion FROM k_calificacion_admin WHERE cl_sesion='$fl_alumno' AND  fl_programa =$fl_leccion ";
      $r = RecuperaValor($Que);
      $no_clificacion_final = $r['no_calificacion'];
    }


    $rubric .= "<div class='row'>
                <div class='col-md-10' style='padding-right: 0px;'>
                    <div class='col-md-12 no-padding'>";
    $rubric .= "         <textarea class='form-control' rows='4' id='desc_teacher'  style='resize:none !important;color:#999;font-style: italic; ' maxlength='130' disabled>" . $ds_comentario_final . "</textarea>";

    $rubric .= "</div>
				   <div class='col-md-4 text-left no-padding'>
					<small class='text-muted'><i>$fe_comentario_final</i></small>
				   </div>
                </div>";

    $rubric .= "<div class='col-md-2'>

                        <div class='panel panel-default' style='margin-right: 4px;'>
                            <div class='panel-body text-center' >


								<div class='chart' data-percent='$no_clificacion_final' id='easy-pie-chartfin'>
									<span class='percent' style='font:18px Arial;'>$no_clificacion_final</span>
								</div>

								<script>
									$(document).ready(function () {
										$('#easy-pie-chartfin').easyPieChart({
											animate: 2000,
											scaleColor: false,
											lineWidth: 7.5,
											lineCap: 'square',
											size: 100,
											trackColor: '#EEEEEE',
											barColor: '#92D099'
										});

										$('#easy-pie-chartfin').css({
											width: 100 + 'px',
											height: 100 + 'px'
										});
										$('#easy-pie-chartfin .percent').css({
											'line-height': 100 + 'px'
										})

									});
								</script>









                                <hr />
									     <b>".ObtenEtiqueta(1671)."</b>

                            </div>
                        </div>


                </div>";

    $rubric .= "
            </div>";
  }



  return $rubric;
}



# Identificamos si estudiante es nuevo
# Para calificar con rubrics
function StudentNew($p_std)
{

  # Flag
  $row = RecuperaValor("SELECT fg_nuevo FROM c_usuario WHERE fl_usuario=".$p_std);
  $fg_nuevo = $row[0];

  return $fg_nuevo;
}


/**
 * MJD #funcion que devuelve la fecha con formato April 10 , 2017 , por default,si queremos agregar los segundos  hay que manderle un true,como sgundo parametro 11:15 am/pm.
 * @param
 *
 */
function ObtenFechaFormatoDiaMesAnioHora($p_fecha,$p_hora=''){


    $p_fecha=strtotime('+0 day',strtotime($p_fecha));
    $p_fecha= date('Y-m-d H:i:s',$p_fecha);
    #DAMOS FORMATO DIA,MES, AÑO.
    $date = date_create($p_fecha);
    if(!empty($p_hora))
         $fecha=date_format($date,'F j, Y, g:i a');
    else
        $fecha=date_format($date,'F j, Y');

  return $fecha;
}

# Certificados etc FAME
function genera_documento_FAME($clave, $opc, $fl_template=0, $programa=0,$fl_envio_correo='') {

  # Recupera datos del template del documento
  switch($opc){
    case 1: $campo = "ds_encabezado"; break;
    case 2: $campo = "ds_cuerpo"; break;
    case 3: $campo = "ds_pie"; break;
    case 4: $campo = "nb_template"; break;
  }

  # Obtenemos la informacion del template header body or footer
  $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
  $row = RecuperaValor($Query1);

  $cadena = $row[0];
  # Sustituye caracteres especiales
  $cadena = str_uso_normal($row[0]);
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);

  # Recupera datos usuario
  $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno,ds_login, fg_genero, ds_email, ".ConsultaFechaBD('fe_nacimiento', FMT_FECHA)." fe_nacimiento, fl_usu_invita, ds_alias ";
  $Query .= "FROM c_usuario WHERE fl_usuario=$clave ";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto($row[0]);
  $ds_lname = str_texto($row[1]);
  $ds_mname = str_texto($row[2]);
  $ds_login = str_texto($row[3]);
  $fg_genero = str_texto($row[4]);
  if ($fg_gender == 'M')
    $ds_gender = ObtenEtiqueta(115);
  else
    $ds_gender = ObtenEtiqueta(116);
  $ds_email = $row[5];
  $fe_nacimiento = $row[6];
  $fl_usu_invita = $row[7];
  $ds_alias = $row[8];


  if (empty($clave)) { #se coloca en dado caso de que la clave venga vacia.(se utiliza para envio de correo de registro de menor de edad.)

    #Recuperamos el nombre del estudinate que se registro
    $Query = "SELECT ds_first_name,ds_last_name FROM k_envio_email_reg_selfp
			 WHERE fl_envio_correo=$fl_envio_correo ";
    $row = RecuperaValor($Query);
    $ds_fname = str_texto($row[0]);
    $ds_lname = str_texto($row[1]);
    $Query3  = "SELECT b.ds_nombres, b.ds_apaterno, b.ds_amaterno ";
    $Query3 .= "FROM k_noconfirmados_pro a, c_usuario b WHERE a.fl_maestro=b.fl_usuario AND a.fl_envio_correo=$fl_envio_correo ";
    $row3 = RecuperaValor($Query3);
    $fame_te_fname = str_texto($row3[0]);
    $fame_te_lname = str_texto($row3[1]);
    $cadena = str_replace("#fame_te_fname#", $fame_te_fname, $cadena);  # fname teacher
    $cadena = str_replace("#fame_te_lname#", $fame_te_lname, $cadena);  # lname teacher
  }


  $cadena = str_replace("#fame_fname#", $ds_fname, $cadena);                        # Student first name
  $cadena = str_replace("#fame_mname#", $ds_mname, $cadena);                        # Student middle name
  $cadena = str_replace("#fame_lname#", $ds_lname, $cadena);                        # Student last name
  $cadena = str_replace("#fame_login#", $ds_login, $cadena);                        # Student login
  $cadena = str_replace("#fame_gender#", $ds_gender, $cadena);                      # Student gender female
  $cadena = str_replace("#fame_email#", $ds_email, $cadena);                        # Student email address
  $cadena = str_replace("#fame_byear#", substr($fe_nacimiento, 6, 4), $cadena);    #Student year of birth
  $cadena = str_replace("#fame_bmonth#", substr($fe_nacimiento, 3, 2), $cadena);   #Student month of birth
  $cadena = str_replace("#fame_bday#", substr($fe_nacimiento, 0, 2), $cadena);     #Student day of birth
  $cadena = str_replace("#fame_alias#", $ds_alias, $cadena);     #Student day of birth


  # Obtenemos iinformacion de la direccion
  $row = RecuperaValor("SELECT a.fl_pais, nb_pais, ds_state, ds_city, ds_number, ds_street, ds_zip, ds_phone_number
FROM k_usu_direccion_sp a, c_pais b WHERE a.fl_pais=b.fl_pais AND a.fl_usuario_sp=$clave");
  $fl_pais = $row[0];
  $nb_pais = str_texto($row[1]);
  if ($fl_pais == 38) {
    $row1 = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$row[2]");
    $ds_state = $row1[0];
  } else
    $ds_state = str_texto($row[2]);
  $ds_city = str_texto($row[3]);
  $ds_number = str_texto($row[4]);
  $ds_street = str_texto($row[5]);
  $ds_zip = str_texto($row[6]);
  $ds_phone_number = str_texto($row[7]);

  $cadena = str_replace("#fame_street_no#", $ds_number, $cadena);                   # Student number street
  $cadena = str_replace("#fame_street_name#", $ds_street, $cadena);                 # Student name street
  $cadena = str_replace("#fame_city#", $ds_city, $cadena);                          # Student city
  $cadena = str_replace("#fame_state#", $ds_state, $cadena);                        # Student state
  $cadena = str_replace("#fame_country#", $nb_pais, $cadena);                       # Student country
  $cadena = str_replace("#fame_code_zip#", $ds_zip, $cadena);                       # Student zip
  $cadena = str_replace("#fame_phone#", $ds_phone_number, $cadena);                 # Student phone number

  #identificamos el id del instituto
  $rowi = RecuperaValor("SELECT fl_instituto FROM c_usuario WHERE fl_usuario=".$clave);
  $fl_instituto = $rowi[0];
 /***********************************/
  $Query="SELECT fg_plan ,no_licencias_usadas,no_licencias_disponibles,fl_princing FROM k_current_plan where fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fg_plan=$row[0];
  $no_licencias_usadas=$row[1];
  $no_licencias_disponibles=$row[2];
  $fl_princi=$row[3];

 $Query="SELECT ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princi ";
  $row=RecuperaValor($Query);
  $mn_descuento_anual=number_format($row[0])."%";
  $mn_descuento_mensual=number_format($row[1])."%";



  if($fg_plan=='A'){
    $plan_actual=ObtenEtiqueta(1503);
    $mn_descuento_plan=$mn_descuento_anual;
  }else{
     $plan_actual=ObtenEtiqueta(1763);
     $mn_descuento_plan=$mn_descuento_mensual;
  }



  #Verificamos si el Instituto ha solicitado cambiado de plan, e identificmos su nuevo pan/nueva suscripcion. el fg_motivo=3 quiere decir que es cambio de plan.
  $QueryP="SELECT fg_cambio_plan FROM  k_cron_plan_fame WHERE fg_motivo_pago='3' AND fl_instituto=$fl_instituto ";
  $rowP=RecuperaValor($QueryP);
  $fg_nuevo_plan=$rowP[0];



  if($fg_nuevo_plan=='A')
      $nuevo_plan=ObtenEtiqueta(1503);
  if($fg_nuevo_plan=='M')
      $nuevo_plan=ObtenEtiqueta(1763);



  #Recuperamos las licencias totales del instituto
  // $total_licencias=ObtenNumLicencias($fl_instituto);  # total licencias actuales

  $dominio_campus = ObtenConfiguracion(116);
  $link_login_fame = $dominio_campus; #bueno#fame_link_login#;


  // $fecha_termino_plan=ObtenFechaFinalizacionContratoPlan($fl_instituto);

  #damos formato ala fecha de finalizacion.
  #DAMOS FORMATO DIA,MES, AÑO

  $fe_termino=strtotime('+0 day',strtotime($fecha_termino_plan));
  $fe_termino= date('Y-m-d',$fe_termino);

  $date = date_create($fe_termino);
  $fe_terminacion_plan = date_format($date, 'F j , Y');




  #Varibales para sustituir para nitificaciones realizadas en billing.
  $cadena = str_replace("#fame_current_plan#", $plan_actual, $cadena);  #plan actual/mont/anual
  $cadena = str_replace("#fame_new_plan#", $nuevo_plan, $cadena);  #nuevo_plan
  $cadena = str_replace("#fame_available_licenses#", $no_licencias_disponibles, $cadena);  #licencisas disponibles
  $cadena = str_replace("#fame_licenses_used#", $no_licencias_usadas, $cadena); #lidcenias usadas
  // $cadena = str_replace("#fame_total_licenses#", $total_licencias, $cadena);  #total de licencias
  $cadena = str_replace("#fame_link_login#", $link_login_fame, $cadena);  #total de licencias
  // $cadena = str_replace("#fame_fe_expiration_plan#", $fe_terminacion_plan, $cadena);  #total de licencias
  $cadena = str_replace("#fame_discount_plan#", $mn_descuento_plan, $cadena);  #total de licencias

  # Obtenemos los datos del maestro
  $Query3  = "SELECT b.ds_nombres, b.ds_apaterno, b.ds_amaterno FROM k_usuario_programa a ";
  $Query3 .= "LEFT JOIN c_usuario b ON(a.fl_maestro=b.fl_usuario) ";
  $Query3 .= "WHERE fl_programa_sp=$programa AND fl_usuario_sp=$clave ";
  $row3 = RecuperaValor($Query3);
  $fame_te_fname = str_texto($row3[0]);
  $fame_te_lname = str_texto($row3[1]);
  if (empty($fame_te_fname) || empty($fame_te_lname)) {
    $row00 = RecuperaValor("SELECT ds_nombres, ds_apaterno, ds_amaterno FROM c_usuario WHERE fl_usuario=$fl_usu_invita");
    $fame_te_fname = str_texto($row00[0]);
    $fame_te_lname = str_texto($row00[1]);
    $cadena = str_replace("#fame_te_fname#", $fame_te_fname, $cadena);  # fname teacher
    $cadena = str_replace("#fame_te_lname#", $fame_te_lname, $cadena);  # lname teacher
  } else {
    $cadena = str_replace("#fame_te_fname#", $fame_te_fname, $cadena);  # fname teacher
    $cadena = str_replace("#fame_te_lname#", $fame_te_lname, $cadena);  # lname teacher
  }



  #Recuperamos datos del administrado del Instituto.
  $Query="SELECT A.fl_usuario_sp,U.ds_nombres,U.ds_apaterno FROM c_instituto A
          JOIN c_usuario U ON U.fl_usuario=A.fl_usuario_sp
           WHERE A.fl_instituto =$fl_instituto ";
  $row=RecuperaValor($Query);
  $fame_fname_admin=str_texto($row[1]);
  $fame_lname_admin=str_texto($row[2]);

  $cadena = str_replace("#fame_adm_fname#",$fame_fname_admin, $cadena);  # fname teacher
  $cadena = str_replace("#fame_adm_lname#",$fame_lname_admin, $cadena);  # lname teacher


  return ($cadena);
}

# Envia notificacion de cambio de usuario
function UserChangeAlias($p_usuario){
  # Variables
  $email_noreply = ObtenConfiguracion(107);
  $app_frm_email = ObtenConfiguracion(83);
  # Obtenemos el email del usuario
  $rowu = RecuperaValor("SELECT ds_email FROM c_usuario WHERE fl_usuario=$p_usuario");
  $ds_email = $rowu[0];
  $message_resp = genera_documento_FAME($p_usuario, 2, 137);
  $rowt = RecuperaValor("SELECT  nb_template FROM k_template_doc WHERE fl_template=137");
  $nb_template = str_texto($rowt[0]);
  # Mensage
  EnviaMailHTML($email_noreply, $email_noreply, $ds_email, $nb_template, $message_resp, $app_frm_email);
}

/**
 * MJD #funcion ddetrmina si una leccion en campus tiene rubric.
 * @param
 *
 */
function TieneRubricLeccionCampus($fl_leccion,$no_semana){

  #Verificamos si la leccion tien rubric
  $Query = "SELECT no_valor_rubric FROM c_leccion WHERE fl_leccion=$fl_leccion AND no_semana=$no_semana ";
  $row = RecuperaValor($Query);
  $no_valor_rubric = $row[0];

  return $no_valor_rubric;
}



/**
 * MJD #funcion determina si los criterios de la leccion son iguales a los congelados que ya tiene el alumno.
 * @param
 *
 */
 function VerificaCambiosRubricActualCampus($fl_leccion,$fl_alumno){


     #Verificamos el criterio actual de la leccion
     $Query="SELECT COUNT(*)
                                FROM  k_criterio_programa K
                                JOIN c_leccion C ON C.fl_leccion =K.fl_programa
                                JOIN c_criterio T ON T.fl_criterio=K.fl_criterio WHERE K.fl_programa=$fl_leccion ";
  $row = RecuperaValor($Query);
  $no_rubrics = $row[0];

  #Verificamos el criterio actual de leccion congeladad del alumno.

  $Query2 = "SELECT  COUNT(*)
                                 FROM  k_criterio_programa_alumno K
                                 JOIN c_leccion C ON C.fl_leccion =K.fl_programa
                                 JOIN c_criterio T ON T.fl_criterio=K.fl_criterio WHERE K.fl_programa=$fl_leccion AND K.fl_alumno=$fl_alumno
                                 ";
  $row2 = RecuperaValor($Query2);
  $no_rubrics2 = $row2[0];


  if ($no_rubrics == $no_rubrics2)
    $fg_estatus = false;
  else
    $fg_estatus = true;

  return $fg_estatus;
}



# Funcion con parametro para obtener el tiempo
# De duracion de los videos
function VideoDuration($ffmpeg, $file, $segundos=false, $tipo_v='', $fl_video=''){

  //$time = 00:00:00.000 format
  $time =  exec($ffmpeg." -i ".$file." 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");

  $duration = explode(".",$time);
  if($segundos==true)
    $duration = $duration[0]*3600 + $duration[1]*60+ round($duration[2]);
  else
    $duration = $duration[0];

  # Student Library Campus
  if($tipo_v=="CSL"){
    $Query = "UPDATE k_video_contenido SET ds_duration='".$duration."' WHERE fl_video_contenido=".$fl_video;
  }

  # Actualiza el tiempo
  EjecutaQuery($Query);


  return $duration;
}

function document_wrokfiles($fl_workfile, $fl_user_get, $fl_template, $fg_campus = 1)
{

  # Template
  $rw0 = RecuperaValor("SELECT ds_encabezado, ds_cuerpo, ds_pie, nb_template FROM k_template_doc WHERE fl_template=".$fl_template);
  $ds_encabezado = str_texto($rw0[0]);
  $ds_cuerpo = str_texto($rw0[1]);
  $ds_pie = str_texto($rw0[2]);
  $nb_template = str_texto($rw0[3]);
  $cadena = $ds_encabezado . $ds_cuerpo . $ds_pie;

  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#47;", "/", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);

  # Working Files
  # Datos de upload
  $Query  = "SELECT ds_nombres, ds_apaterno, ds_files, ds_version, ds_descripcion, nb_programa, no_semana ";
  $Query .= "FROM k_worksfiles a, c_usuario b, c_leccion c, c_programa d ";
  $Query .= "WHERE a.fl_usu_upload=b.fl_usuario AND a.fl_leccion=c.fl_leccion ";
  $Query .= "AND c.fl_programa=d.fl_programa AND a.fl_worksfiles=".$fl_workfile;
  $rw1 = RecuperaValor($Query);
  $ds_nombres = str_texto($rw1[0]);
  $ds_apaterno = str_texto($rw1[1]);
  $ds_files = str_texto($rw1[2]);
  $ds_version = str_texto($rw1[3]);
  $ds_descripcion = str_texto($rw1[4]);
  $nb_programa = str_texto($rw1[5]);
  $no_semana = str_texto($rw1[6]);

  # Obtenemos los datos del usuario que lo recibe
  $rw2 = RecuperaValor("SELECT ds_nombres name_get, ds_apaterno apa_get, ds_email FROM c_usuario WHERE fl_usuario=".$fl_user_get);
  $name_get = str_texto($rw2[0]);
  $apa_get = str_texto($rw2[1]);
  $ds_email = str_texto($rw2[2]);

  # Variables
  if (!empty($fg_campus)) {
    $usr_fname_wf = $name_get;
    $usr_lname_wf =  $apa_get;
    $pg_name = $nb_programa;
    $no_week = $no_semana;
    $usrupload_fname = $ds_nombres;
    $usrupload_lname = $ds_apaterno;
    $vanas_workfilename = $ds_files;
    $vanas_workfilename_version = $ds_version;
    $vanas_workfilename_descrip = $ds_descripcion;
    $cadena = str_replace("#usr_fname_wf#", $usr_fname_wf, $cadena);
    $cadena = str_replace("#usr_lname_wf#", $usr_lname_wf, $cadena);
    $cadena = str_replace("#pg_name#", $pg_name, $cadena);
    $cadena = str_replace("#no_week#", $no_week, $cadena);
    $cadena = str_replace("#usrupload_fname#", $usrupload_fname, $cadena);
    $cadena = str_replace("#usrupload_lname#", $usrupload_lname, $cadena);
    $cadena = str_replace("#vanas_workfilename#", $vanas_workfilename, $cadena);
    $cadena = str_replace("#vanas_workfilename_version#", $vanas_workfilename_version, $cadena);
    $cadena = str_replace("#vanas_workfilename_descrip#", $vanas_workfilename_descrip, $cadena);
    # Vanas
    $from = ObtenConfiguracion(4);
    $bcc = ObtenConfiguracion(83);
  } else {
    $fame_usr_fname = $name_get;
    $fame_usr_lname =  $apa_get;
    $fame_pg_name = $nb_programa;
    $fame_no_week = $no_semana;
    $fame_usrupload_fname = $ds_nombres;
    $fame_usrupload_lname = $ds_apaterno;
    $fame_workfilename = $ds_files;
    $fame_workfile_version = $ds_version;
    $fame_workfile_descrip = $ds_descripcion;
    $cadena = str_replace("#fame_usr_fname#", $fame_usr_fname, $cadena);
    $cadena = str_replace("#fame_usr_lname#", $fame_usr_lname, $cadena);
    $cadena = str_replace("#fame_pg_name#", $fame_pg_name, $cadena);
    $cadena = str_replace("#fame_no_week#", $fame_no_week, $cadena);
    $cadena = str_replace("#fame_usrupload_fname#", $fame_usrupload_fname, $cadena);
    $cadena = str_replace("#fame_usrupload_lname#", $fame_usrupload_lname, $cadena);
    $cadena = str_replace("#fame_workfilename#", $fame_workfilename, $cadena);
    $cadena = str_replace("#fame_workfile_version#", $fame_workfile_version, $cadena);
    $cadena = str_replace("#fame_workfile_descrip#", $fame_workfile_descrip, $cadena);

    #FAME
    $from = ObtenConfiguracion(4);
    $bcc = ObtenConfiguracion(107);
  }

  # Set email user get with bcc admin
  EnviaMailHTML($from, $from, $ds_email, $nb_template, $cadena, $bcc);
}

function replaceLangWords($string, $langselect) {

  switch ($langselect) {
    case 'esp':
      $replacements = array(
        "hours" => "horas",
        "minutes" => "minutos",
        "Certificate" => "Certificado",
        "English" => "Ingles"
      );
      $replaced = str_replace(array_keys($replacements), $replacements, $string);
      return $replaced;
      break;

    case 'eng':
      return $string;
      break;

    case 'fra':
    $replacements = array(
        "hours" => "heures",
        "minutes" => "minutes",
        "Certificate" => "Certificat",
        "English" => "Anglais"
      );
      $replaced = str_replace(array_keys($replacements), $replacements, $string);
      return $replaced;
      break;

    default:
      return $string;
      break;
  }
}

function createLocaleCSVs(){

  //open files for writing ('w')
  $handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/locale/english.csv", "w") or die('Cannot open file:  ' . $my_file);
  $handle_esp = fopen($_SERVER['DOCUMENT_ROOT'] . "/locale/spanish.csv", "w") or die('Cannot open file:  ' . $my_file);
  $handle_fra = fopen($_SERVER['DOCUMENT_ROOT'] . "/locale/french.csv", "w") or die('Cannot open file:  ' . $my_file);

  // Set the header for the files
  $data = "#Eglish_Tag_ID, ds_etiqueta\r\n";
  $data_esp = "#Spanish_Tag_ID, ds_etiqueta_esp\r\n";
  $data_fra = "#French_Tag_ID, ds_etiqueta_fra\r\n";

  // Creates the UTF-8 BOM
  fwrite($handle, pack("CCC", 0xef, 0xbb, 0xbf));
  fwrite($handle_esp, pack("CCC", 0xef, 0xbb, 0xbf));
  fwrite($handle_fra, pack("CCC", 0xef, 0xbb, 0xbf));

  // Write the header for the files
  fwrite($handle, $data);
  fwrite($handle_esp, $data_esp);
  fwrite($handle_fra, $data_fra);

  // Set the Query
  $sql = "SELECT cl_etiqueta, ds_etiqueta, ds_etiqueta_esp, ds_etiqueta_fra FROM c_etiqueta ORDER BY cl_etiqueta;";

  // Execue the query and write the data to files
  $res = EjecutaQuery($sql);
  for ($i = 1; $row = RecuperaRegistro($res); $i++) {
    $data = $row['cl_etiqueta'] . " |" . $row["ds_etiqueta"] . PHP_EOL;
    $data_esp = $row['cl_etiqueta'] . " |" . $row["ds_etiqueta_esp"] . PHP_EOL;
    $data_fra = $row['cl_etiqueta'] . " |" . $row["ds_etiqueta_fra"] . PHP_EOL;
    fwrite($handle, $data);
    fwrite($handle_esp, $data_esp);
    fwrite($handle_fra, $data_fra);
  }
  // Close the files
  fclose($handle);
  fclose($handle_esp);
  fclose($handle_fra);
}

function lastActivityUser($p_usuario){
  # Set the user last activity to current time stamp
  $Query = EjecutaQuery("UPDATE c_usuario SET last_activity=current_timestamp WHERE fl_usuario=$p_usuario");
}

function lastActivityCourse($p_usuario, $fl_programa){
  $Query = EjecutaQuery("UPDATE k_usuario_programa SET last_activity=current_timestamp WHERE fl_usuario_sp = $p_usuario AND fl_programa_sp=$fl_programa ");
}

# UMP 02/14/2020 Function to generate a unique random token using checksum signature
function encriptClave($clave){
  $str = '';
  $aleatorio=strval(random_int(1, 128));
  $rndHex=dechex($aleatorio);
  if (strlen($rndHex) < 2){$rndHex = "0".$rndHex;}
  $length=strlen(dechex($clave*$aleatorio));
  $clavehex=dechex($clave*$aleatorio);
  $keyspace = '01234567890abcdef';
  $max = mb_strlen($keyspace, '8bit') - 1;
  $strLenght=128;
  for ($i = 0; $i < $strLenght; ++$i) {$str .= $keyspace[random_int(0, $max)];}
  $encriptedVal=str_split($clavehex);
  $construct="";
  $startval=0;
  foreach($encriptedVal as $endval){
      $construct.=$endval.substr($str, $startval, hexdec("$endval"));
      $startval=hexdec("$endval");
  }
  $encriptedStr=$rndHex.$length.$construct;
  $encriptedStr=$encriptedStr."-".crc32($encriptedStr);
  return $encriptedStr;
}

// UMP 02/14/2020 Function to decrypt the unique random token, use checksum signature
function decriptClave($clave){
  $checksum=substr($clave, strpos($clave, "-")+1);
  $aleatorio=(substr($clave, 0, 2));
  $lenght=hexdec(substr($clave, 2, 1));
  $decripval='';
  $startval=dechex('3');
  for ($i=1;$i<=$lenght;$i++){
      $valor=substr($clave, hexdec($startval), 1);
      $decripval.=$valor;
      $startval=dechex(hexdec($valor)+hexdec($startval)+1);
  }
  $decripval=hexdec($decripval)/hexdec($aleatorio);
  if ($checksum!=crc32(substr($clave, 0, strpos($clave, "-")))){
      $decripval=NULL;
  }
  return $decripval;
}

// UMP 05/05/2020 Function to delete a directory recursively
function delete_folder($folder) {
  $glob = glob($folder);
  foreach ($glob as $g) {
    if (!is_dir($g)) {
      unlink($g);
    } else {
      delete_folder("$g/*");
      rmdir($g);
    }
  }
}

//Function to know if it has a rubric lesson
function ExisteRubric($fl_leccion){

    $Query="SELECT COUNT(*) FROM k_criterio_programa WHERE fl_programa =$fl_leccion ";
    $row=RecuperaValor($Query);
    $fg_rubric=!empty($row[0])?1:0;

    return $fg_rubric;
}


function Mailer($mailto,$subject,$message,$copy='',$attachment='',$nameAttachment='',$fg_copy_all='',$cronjobs=false){


    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    $smtphost=ObtenConfiguracion(161);
    $mailfrom=ObtenConfiguracion(162);
    $mailpass=ObtenConfiguracion(163);

    //envia copia a admin@vanas.ca
    $admin = ObtenConfiguracion(83);
	$aply_vanas = ObtenConfiguracion(20);



    try{

        //Server settings
        $mail->SMTPDebug = false;//SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $smtphost;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $mailfrom;                     //SMTP username
        $mail->Password   = $mailpass;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($mailfrom, $subject);
        $mail->addAddress($mailto, $mailto);     //Add a recipient
        $mail->addBCC($admin);//copia oculta forever
        if($cronjobs==false){
            $mail->addBCC($aply_vanas);//copia oculta forever
        }
		if($fg_copy_all){

			$mail->addBCC('sonia@vanas.ca');//copy forever
			$mail->addBCC('erika@vanas.ca');//copia oculta forever
			$mail->addBCC('ask@vanas.ca');//copia oculta forever
		}
        if($copy)
            $mail->addBCC($copy);//copia oculta

        //Attachments
        if($attachment)
            $mail->AddStringAttachment($attachment, $nameAttachment, 'base64', 'application/pdf');// attachment
            //$mail->addAttachment($attachment);         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->send();

        $status=true;

    }
    catch (Exception $e)
    {

        #create log error email.
        $error = $e->getMessage()."to:$mailto ";

        #MJD PHP MAILER 6.5
        if (PHP_OS=='Linux') { # when is production
            $file_name_txt="/var/www/html/vanas/log_phpmailer.txt";
        }else{
            $file_name_txt=$_SERVER['DOCUMENT_ROOT']."/log_phpmailer.txt";
        }

        $fch= fopen($file_name_txt, "a+"); // Abres el archivo para escribir en él
        fwrite($fch, "\n".$error); // Grabas
        fclose($fch); // Cierras el archivo.

        $fch = fopen($file_name_txt, "a+"); // Abres el archivo para escribir en él
        fwrite($fch, "\n" ."Subject:.". $subject." ". $mailto); // Grabas
        fclose($fch); // Cierras el archivo.

        $status=false;
    }


    return $status;


}

?>
