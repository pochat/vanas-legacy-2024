<?php

# Libreria de funciones
require '../../lib/general.inc.php';

# Verifica que exista una sesion valida en el cookie y la resetea
ValidaSesion();

$clave = RecibeParametroNumerico('clave');

$action = RecibeParametroHTML('action');

if ($action == 'add')
{

    $fl_newCountry = RecibeParametroNumerico('fl_pais');

    if (!empty($fl_newCountry)) {

        $Query = "INSERT INTO k_programa_costos_pais(fl_pais,fl_programa,no_horas_week)value($fl_newCountry,$clave,0) ";
        EjecutaQuery($Query);

    }


}

if ($action == 'delete')
{
    $fl_newCountry = RecibeParametroNumerico('fl_pais');

    $Query = "DELETE FROM k_programa_costos_pais WHERE fl_programa=$clave AND fl_pais=$fl_newCountry ";
    EjecutaQuery($Query);


}


$Query = "SELECT fg_total_programa, fg_tax_rate, ";
$Query .= "fg_total_programa_internacional,fg_tax_rate_internacional, ";
$Query .= "fg_total_programa_combined,fg_tax_rate_combined, ";
$Query .= "fg_total_programa_internacional_combined,fg_tax_rate_internacional_combined ";
$Query .= "FROM c_programa ";
$Query .= "WHERE fl_programa=$clave ";
$row = RecuperaValor($Query);
$fg_total_programa = $row['fg_total_programa'];
$fg_tax_rate = $row['fg_tax_rate'];
$fg_total_programa_internacional = $row['fg_total_programa_internacional'];
$fg_tax_rate_internacional = $row['fg_tax_rate_internacional'];
$fg_total_programa_combined = $row['fg_total_programa_combined'];
$fg_total_programa_internacional_combined = $row['fg_total_programa_internacional_combined'];
$fg_tax_rate_combined = $row['fg_tax_rate_combined'];
$fg_tax_rate_internacional_combined = $row['fg_tax_rate_internacional_combined'];




$Query = "SELECT no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, ";
$Query .= "mn_app_fee, mn_tuition, mn_costs, ds_costs, no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes, ";
$Query .= "no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes, ";
$Query .= "no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes, no_horas_week ";
$Query .= ",mn_app_fee_internacional, mn_tuition_internacional, mn_costs_internacional,ds_costs_internacional,no_a_payments_internacional,ds_a_freq_internacional,mn_a_due_internacional,mn_a_paid_internacional,    no_a_interes_internacional, ";
$Query .= "no_b_payments_internacional,ds_b_freq_internacional,mn_b_due_internacional,mn_b_paid_internacional,no_b_interes_internacional, ";
$Query .= "no_c_payments_internacional,ds_c_freq_internacional,mn_c_due_internacional,mn_c_paid_internacional,no_c_interes_internacional, ";
$Query .= "no_d_payments_internacional,ds_d_freq_internacional,mn_d_due_internacional,mn_d_paid_internacional,no_d_interes_internacional, ";
$Query .= "mn_app_fee_combined,mn_tuition_combined,mn_costs_combined,ds_costs_combined, no_a_payments_combined, ds_a_freq_combined, mn_a_due_combined, mn_a_paid_combined, no_a_interes_combined, ";
$Query .= "no_b_payments_combined, ds_b_freq_combined, mn_b_due_combined, mn_b_paid_combined, no_b_interes_combined, no_c_payments_combined, ds_c_freq_combined, mn_c_due_combined, mn_c_paid_combined, no_c_interes_combined,";
$Query .= "no_d_payments_combined, ds_d_freq_combined, mn_d_due_combined, mn_d_paid_combined, no_d_interes_combined ";
$Query .= ",mn_app_fee_internacional_combined, mn_tuition_internacional_combined, mn_costs_internacional_combined,ds_costs_internacional_combined,no_a_payments_internacional_combined,ds_a_freq_internacional_combined,mn_a_due_internacional_combined,mn_a_paid_internacional_combined,no_a_interes_internacional_combined, ";
$Query .= "no_b_payments_internacional_combined,ds_b_freq_internacional_combined,mn_b_due_internacional_combined,mn_b_paid_internacional_combined,no_b_interes_internacional_combined, ";
$Query .= "no_c_payments_internacional_combined,ds_c_freq_internacional_combined,mn_c_due_internacional_combined,mn_c_paid_internacional_combined,no_c_interes_internacional_combined, ";
$Query .= "no_d_payments_internacional_combined,ds_d_freq_internacional_combined,mn_d_due_internacional_combined,mn_d_paid_internacional_combined,no_d_interes_internacional_combined ";

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
#Online

if (!empty($row[6]))
    $app_fee = $row[6];
else
    $app_fee = 0;

if (!empty($row[7]))
    $tuition = $row[7];
else
    $tuition = 0;

$no_costos_ad = $row[8];
$ds_costos_ad = $row[9];
#Combined
if (!empty($row['mn_app_fee_combined']))
    $app_fee_combined = $row['mn_app_fee_combined'];
else
    $app_fee_combined = 0;
if (!empty($row['mn_tuition_combined']))
    $tuition_combined = $row['mn_tuition_combined'];
else
    $tuition_combined = 0;

$no_costos_ad_combined = $row['mn_costs_combined'];
$ds_costos_ad_combined = $row['ds_costs_combined'];

#Online
$app_fee_internacional = $row['mn_app_fee_internacional'];
if (empty($app_fee_internacional))
    $app_fee_internacional = 0;

$tuition_internacional = $row['mn_tuition_internacional'];

if (empty($tuition_internacional))
    $tuition_internacional = 0;

$no_costos_ad_internacional = $row['mn_costs_internacional'];
$ds_costos_ad_internacional = $row['ds_costs_internacional'];

#Combined
$app_fee_internacional_combined = $row['mn_app_fee_internacional_combined'];

if (empty($app_fee_internacional_combined))
    $app_fee_internacional_combined = 0;

$tuition_internacional_combined = $row['mn_tuition_internacional_combined'];

if (empty($tuition_internacional_combined))
    $tuition_internacional_combined = 0;

$no_costos_ad_internacional_combined = $row['mn_costs_internacional_combined'];
$ds_costos_ad_internacional_combined = $row['ds_costs_internacional_combined'];

#Online
if (!empty($row[10]))
    $no_payments_a = $row[10];
else
    $no_payments_a = 1;

if (!empty($row[11]))
    $frequency_a = $row[11];
else
    $frequency_a = "Full Payment";

$amount_due_a = $row[12];
$amount_paid_a = $row[13];

if (!empty($row[14]))
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
$no_horas_week = $row[30];

#Internacional Online
if (!empty($row['no_a_payments_internacional']))
    $no_payments_a_internacional = $row['no_a_payments_internacional'];
else
    $no_payments_a_internacional = 1;

if (!empty($row['ds_a_freq_internacional']))
    $frequency_a_internacional = $row['ds_a_freq_internacional'];
else
    $frequency_a_internacional = "Full Payment";

$amount_due_a_internacional = $row['mn_a_due_internacional'];
$amount_paid_a_internacional = $row['mn_a_paid_internacional'];

if (!empty($row['no_a_interes_internacional']))
    $interes_a_internacional = $row['no_a_interes_internacional'];
else
    $interes_a_internacional = 0;

$no_payments_b_internacional = $row['no_b_payments_internacional'];
$frequency_b_internacional = $row['ds_b_freq_internacional'];
$amount_due_b_internacional = $row['mn_b_due_internacional'];
$amount_paid_b_internacional = $row['mn_b_paid_internacional'];
$interes_b_internacional = $row['no_b_interes_internacional'];
$no_payments_c_internacional = $row['no_c_payments_internacional'];
$frequency_c_internacional = $row['ds_c_freq_internacional'];
$amount_due_c_internacional = $row['mn_c_due_internacional'];
$amount_paid_c_internacional = $row['mn_c_paid_internacional'];
$interes_c_internacional = $row['no_c_interes_internacional'];
$no_payments_d_internacional = $row['no_d_payments_internacional'];
$frequency_d_internacional = $row['ds_d_freq_internacional'];
$amount_due_d_internacional = $row['mn_d_due_internacional'];
$amount_paid_d_internacional = $row['mn_d_paid_internacional'];
$interes_d_internacional = $row['no_d_interes_internacional'];

#Combined
if (!empty($row['no_payments_a_combined']))
    $no_payments_a_combined = $row['no_payments_a_combined'];
else
    $no_payments_a_combined = 1;

if (!empty($row['ds_a_freq_combined']))
    $frequency_a_combined = $row['ds_a_freq_combined'];
else
    $frequency_a_combined = "Full Payment";

$amount_due_a_combined = $row['mn_a_due_combined'];
$amount_paid_a_combined = $row['mn_a_paid_combined'];

if (!empty($row['no_a_interes_combined']))
    $interes_a_combined = $row['no_a_interes_combined'];
else
    $interes_a_combined = 0;

$no_payments_b_combined = $row['no_b_payments_combined'];
$frequency_b_combined = $row['ds_b_freq_combined'];
$amount_due_b_combined = $row['mn_b_due_combined'];
$amount_paid_b_combined = $row['mn_b_paid_combined'];
$interes_b_combined = $row['no_b_interes_combined'];
$no_payments_c_combined = $row['no_c_payments_combined'];
$frequency_c_combined = $row['ds_c_freq_combined'];
$amount_due_c_combined = $row['mn_c_due_combined'];
$amount_paid_c_combined = $row['mn_c_paid_combined'];
$interes_c_combined = $row['no_c_interes_combined'];
$no_payments_d_combined = $row['no_d_payments_combined'];
$frequency_d_combined = $row['ds_d_freq_combined'];
$amount_due_d_combined = $row['mn_d_due_combined'];
$amount_paid_d_combined = $row['mn_d_paid_combined'];
$interes_d_combined = $row['no_d_interes_combined'];

#Internacional Combined
if (!empty($row['no_a_payments_internacional_combined']))
    $no_payments_a_internacional_combined = $row['no_a_payments_internacional_combined'];
else
    $no_payments_a_internacional_combined = 1;

if (!empty($row['ds_a_freq_internacional_combined']))
    $frequency_a_internacional_combined = $row['ds_a_freq_internacional_combined'];
else
    $frequency_a_internacional_combined = "Full Payment";

$amount_due_a_internacional_combined = $row['mn_a_due_internacional_combined'];
$amount_paid_a_internacional_combined = $row['mn_a_paid_internacional_combined'];

if (!empty($row['no_a_interes_internacional_combined']))
    $interes_a_internacional_combined = $row['no_a_interes_internacional_combined'];
else
    $interes_a_internacional_combined = 0;

$no_payments_b_internacional_combined = $row['no_b_payments_internacional_combined'];
$frequency_b_internacional_combined = $row['ds_b_freq_internacional_combined'];
$amount_due_b_internacional_combined = $row['mn_b_due_internacional_combined'];
$amount_paid_b_internacional_combined = $row['mn_b_paid_internacional_combined'];
$interes_b_internacional_combined = $row['no_b_interes_internacional_combined'];
$no_payments_c_internacional_combined = $row['no_c_payments_internacional_combined'];
$frequency_c_internacional_combined = $row['ds_c_freq_internacional_combined'];
$amount_due_c_internacional_combined = $row['mn_c_due_internacional_combined'];
$amount_paid_c_internacional_combined = $row['mn_c_paid_internacional_combined'];
$interes_c_internacional_combined = $row['no_c_interes_internacional_combined'];
$no_payments_d_internacional_combined = $row['no_d_payments_internacional_combined'];
$frequency_d_internacional_combined = $row['ds_d_freq_internacional_combined'];
$amount_due_d_internacional_combined = $row['mn_d_due_internacional_combined'];
$amount_paid_d_internacional_combined = $row['mn_d_paid_internacional_combined'];
$interes_d_internacional_combined = $row['no_d_interes_internacional_combined'];


#for USA.
$Queryc = "SELECT no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, ";
$Queryc .= "mn_app_fee, mn_tuition, mn_costs, ds_costs, no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes, ";
$Queryc .= "no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes, ";
$Queryc .= "no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes, no_horas_week ";
$Queryc .= ",mn_app_fee_internacional, mn_tuition_internacional, mn_costs_internacional,ds_costs_internacional,no_a_payments_internacional,ds_a_freq_internacional,mn_a_due_internacional,mn_a_paid_internacional,    no_a_interes_internacional, ";
$Queryc .= "no_b_payments_internacional,ds_b_freq_internacional,mn_b_due_internacional,mn_b_paid_internacional,no_b_interes_internacional, ";
$Queryc .= "no_c_payments_internacional,ds_c_freq_internacional,mn_c_due_internacional,mn_c_paid_internacional,no_c_interes_internacional, ";
$Queryc .= "no_d_payments_internacional,ds_d_freq_internacional,mn_d_due_internacional,mn_d_paid_internacional,no_d_interes_internacional, ";
$Queryc .= "mn_app_fee_combined,mn_tuition_combined,mn_costs_combined,ds_costs_combined, no_a_payments_combined, ds_a_freq_combined, mn_a_due_combined, mn_a_paid_combined, no_a_interes_combined, ";
$Queryc .= "no_b_payments_combined, ds_b_freq_combined, mn_b_due_combined, mn_b_paid_combined, no_b_interes_combined, no_c_payments_combined, ds_c_freq_combined, mn_c_due_combined, mn_c_paid_combined, no_c_interes_combined,";
$Queryc .= "no_d_payments_combined, ds_d_freq_combined, mn_d_due_combined, mn_d_paid_combined, no_d_interes_combined ";
$Queryc .= ",mn_app_fee_internacional_combined, mn_tuition_internacional_combined, mn_costs_internacional_combined,ds_costs_internacional_combined,no_a_payments_internacional_combined,ds_a_freq_internacional_combined,mn_a_due_internacional_combined,mn_a_paid_internacional_combined,no_a_interes_internacional_combined, ";
$Queryc .= "no_b_payments_internacional_combined,ds_b_freq_internacional_combined,mn_b_due_internacional_combined,mn_b_paid_internacional_combined,no_b_interes_internacional_combined, ";
$Queryc .= "no_c_payments_internacional_combined,ds_c_freq_internacional_combined,mn_c_due_internacional_combined,mn_c_paid_internacional_combined,no_c_interes_internacional_combined, ";
$Queryc .= "no_d_payments_internacional_combined,ds_d_freq_internacional_combined,mn_d_due_internacional_combined,mn_d_paid_internacional_combined,no_d_interes_internacional_combined ";

$Queryc .= "FROM k_programa_costos_pais ";
$Queryc .= "WHERE fl_programa = $clave AND fl_pais =226 ";
$rowu = RecuperaValor($Queryc);

if (!empty($rowu[6]))
    $app_fee_usa = $rowu[6];
else
    $app_fee_usa = 0;

if (!empty($rowu[7]))
    $tuition_usa = $rowu[7];
else
    $tuition_usa = 0;

$no_costos_ad_usa = $rowu[8];
$ds_costos_ad_usa = $rowu[9];

#Combined
if (!empty($rowu['mn_app_fee_combined']))
    $app_fee_combined_usa = $rowu['mn_app_fee_combined'];
else
    $app_fee_combined_usa = 0;
if (!empty($rowu['mn_tuition_combined']))
    $tuition_combined_usa = $rowu['mn_tuition_combined'];
else
    $tuition_combined_usa = 0;

$no_costos_ad_combined_usa = $rowu['mn_costs_combined'];
$ds_costos_ad_combined_usa = $rowu['ds_costs_combined'];

#Online
$app_fee_internacional_usa = $rowu['mn_app_fee_internacional'];
if (empty($app_fee_internacional_usa))
    $app_fee_internacional_usa = 0;

$tuition_internacional_usa = $rowu['mn_tuition_internacional'];

if (empty($tuition_internacional_usa))
    $tuition_internacional_usa = 0;

$no_costos_ad_internacional_usa = $rowu['mn_costs_internacional'];
$ds_costos_ad_internacional_usa = $rowu['ds_costs_internacional'];

#Combined
$app_fee_internacional_combined_usa = $rowu['mn_app_fee_internacional_combined'];

if (empty($app_fee_internacional_combined_usa))
    $app_fee_internacional_combined_usa = 0;

$tuition_internacional_combined_usa = $rowu['mn_tuition_internacional_combined'];

if (empty($tuition_internacional_combined_usa))
    $tuition_internacional_combined_usa = 0;

$no_costos_ad_internacional_combined_usa = $rowu['mn_costs_internacional_combined'];
$ds_costos_ad_internacional_combined_usa = $rowu['ds_costs_internacional_combined'];

#Online
if (!empty($rowu[10]))
    $no_payments_a_usa = $rowu[10];
else
    $no_payments_a_usa = 1;

if (!empty($rowu[11]))
    $frequency_a_usa = $rowu[11];
else
    $frequency_a_usa = "Full Payment";

$amount_due_a_usa = $rowu[12];
$amount_paid_a_usa = $rowu[13];

if (!empty($row[14]))
    $interes_a_usa = $rowu[14];
else
    $interes_a_usa = 0;

$no_payments_b_usa = $rowu[15];
$frequency_b_usa = $rowu[16];
$amount_due_b_usa = $rowu[17];
$amount_paid_b_usa = $rowu[18];
$interes_b_usa = $rowu[19];
$no_payments_c_usa = $rowu[20];
$frequency_c_usa = $rowu[21];
$amount_due_c_usa = $rowu[22];
$amount_paid_c_usa = $rowu[23];
$interes_c_usa = $rowu[24];
$no_payments_d_usa = $rowu[25];
$frequency_d_usa = $rowu[26];
$amount_due_d_usa = $rowu[27];
$amount_paid_d_usa = $rowu[28];
$interes_d_usa = $rowu[29];
$no_horas_week_usa = $rowu[30];

#Internacional Online
if (!empty($rowu['no_a_payments_internacional']))
    $no_payments_a_internacional_usa = $rowu['no_a_payments_internacional'];
else
    $no_payments_a_internacional_usa = 1;

if (!empty($rowu['ds_a_freq_internacional']))
    $frequency_a_internacional_usa = $rowu['ds_a_freq_internacional'];
else
    $frequency_a_internacional_usa = "Full Payment";

$amount_due_a_internacional_usa = $rowu['mn_a_due_internacional'];
$amount_paid_a_internacional_usa = $rowu['mn_a_paid_internacional'];

if (!empty($rowu['no_a_interes_internacional']))
    $interes_a_internacional_usa = $rowu['no_a_interes_internacional'];
else
    $interes_a_internacional_usa = 0;

$no_payments_b_internacional_usa = $rowu['no_b_payments_internacional'];
$frequency_b_internacional_usa = $rowu['ds_b_freq_internacional'];
$amount_due_b_internacional_usa = $rowu['mn_b_due_internacional'];
$amount_paid_b_internacional_usa = $rowu['mn_b_paid_internacional'];
$interes_b_internacional_usa = $rowu['no_b_interes_internacional'];
$no_payments_c_internacional_usa = $rowu['no_c_payments_internacional'];
$frequency_c_internacional_usa = $rowu['ds_c_freq_internacional'];
$amount_due_c_internacional_usa = $rowu['mn_c_due_internacional'];
$amount_paid_c_internacional_usa = $rowu['mn_c_paid_internacional'];
$interes_c_internacional_usa = $rowu['no_c_interes_internacional'];
$no_payments_d_internacional_usa = $rowu['no_d_payments_internacional'];
$frequency_d_internacional_usa = $rowu['ds_d_freq_internacional'];
$amount_due_d_internacional_usa = $rowu['mn_d_due_internacional'];
$amount_paid_d_internacional_usa = $rowu['mn_d_paid_internacional'];
$interes_d_internacional_usa = $rowu['no_d_interes_internacional'];

#Combined
if (!empty($rowu['no_payments_a_combined']))
    $no_payments_a_combined_usa = $rowu['no_payments_a_combined'];
else
    $no_payments_a_combined_usa = 1;

if (!empty($rowu['ds_a_freq_combined']))
    $frequency_a_combined_usa = $rowu['ds_a_freq_combined'];
else
    $frequency_a_combined_usa = "Full Payment";

$amount_due_a_combined_usa = $rowu['mn_a_due_combined'];
$amount_paid_a_combined_usa = $rowu['mn_a_paid_combined'];

if (!empty($rowu['no_a_interes_combined']))
    $interes_a_combined_usa = $rowu['no_a_interes_combined'];
else
    $interes_a_combined_usa = 0;

$no_payments_b_combined_usa = $rowu['no_b_payments_combined'];
$frequency_b_combined_usa = $rowu['ds_b_freq_combined'];
$amount_due_b_combined_usa = $rowu['mn_b_due_combined'];
$amount_paid_b_combined_usa = $rowu['mn_b_paid_combined'];
$interes_b_combined_usa = $rowu['no_b_interes_combined'];
$no_payments_c_combined_usa = $rowu['no_c_payments_combined'];
$frequency_c_combined_usa = $rowu['ds_c_freq_combined'];
$amount_due_c_combined_usa = $rowu['mn_c_due_combined'];
$amount_paid_c_combined_usa = $rowu['mn_c_paid_combined'];
$interes_c_combined_usa = $rowu['no_c_interes_combined'];
$no_payments_d_combined_usa = $rowu['no_d_payments_combined'];
$frequency_d_combined_usa = $rowu['ds_d_freq_combined'];
$amount_due_d_combined_usa = $rowu['mn_d_due_combined'];
$amount_paid_d_combined_usa = $rowu['mn_d_paid_combined'];
$interes_d_combined_usa = $rowu['no_d_interes_combined'];

#Internacional Combined
if (!empty($rowu['no_a_payments_internacional_combined']))
    $no_payments_a_internacional_combined_usa = $rowu['no_a_payments_internacional_combined'];
else
    $no_payments_a_internacional_combined_usa = 1;

if (!empty($rowu['ds_a_freq_internacional_combined']))
    $frequency_a_internacional_combined_usa = $rowu['ds_a_freq_internacional_combined'];
else
    $frequency_a_internacional_combined_usa = "Full Payment";

$amount_due_a_internacional_combined_usa = $rowu['mn_a_due_internacional_combined'];
$amount_paid_a_internacional_combined_usa = $rowu['mn_a_paid_internacional_combined'];

if (!empty($rowu['no_a_interes_internacional_combined']))
    $interes_a_internacional_combined_usa = $rowu['no_a_interes_internacional_combined'];
else
    $interes_a_internacional_combined_usa = 0;

$no_payments_b_internacional_combined_usa = $rowu['no_b_payments_internacional_combined'];
$frequency_b_internacional_combined_usa = $rowu['ds_b_freq_internacional_combined'];
$amount_due_b_internacional_combined_usa = $rowu['mn_b_due_internacional_combined'];
$amount_paid_b_internacional_combined_usa = $rowu['mn_b_paid_internacional_combined'];
$interes_b_internacional_combined_usa = $rowu['no_b_interes_internacional_combined'];
$no_payments_c_internacional_combined_usa = $rowu['no_c_payments_internacional_combined'];
$frequency_c_internacional_combined_usa = $rowu['ds_c_freq_internacional_combined'];
$amount_due_c_internacional_combined_usa = $rowu['mn_c_due_internacional_combined'];
$amount_paid_c_internacional_combined_usa = $rowu['mn_c_paid_internacional_combined'];
$interes_c_internacional_combined_usa = $rowu['no_c_interes_internacional_combined'];
$no_payments_d_internacional_combined_usa = $rowu['no_d_payments_internacional_combined'];
$frequency_d_internacional_combined_usa = $rowu['ds_d_freq_internacional_combined'];
$amount_due_d_internacional_combined_usa = $rowu['mn_d_due_internacional_combined'];
$amount_paid_d_internacional_combined_usa = $rowu['mn_d_paid_internacional_combined'];
$interes_d_internacional_combined_usa = $rowu['no_d_interes_internacional_combined'];





?>




<ul id="tab_pais" class="nav nav-tabs bordered">
    <li class="active">
        <a href="#canada" data-toggle="tab">
            <i class="fa fa-fw fa-lg fa-globe"></i>Canada ($ CAD)
        </a>
    </li>
    <li>
        <a href="#usa" data-toggle="tab">
            <i class="fa fa-fw fa-lg fa-globe"></i>USA ($ USD)
        </a>
    </li>

    <?php
    #Recuperamos los pais agregados
    $Queryp = "SELECT * FROM k_programa_costos_pais WHERE fl_pais<>226 AND fl_pais<>38 AND fl_programa=$clave ORDER BY fl_programa_costos asc ";
    $rsp = EjecutaQuery($Queryp);

    for($a=0;$rowp=RecuperaRegistro($rsp);$a++){
        $fl_pais = $rowp['fl_pais'];

        $Querypa="SELECT cl_iso2,ds_pais,currency_code FROM c_pais WHERE fl_pais=$fl_pais ";
        $rowpa=RecuperaValor($Querypa);
        $cl_iso2=$rowpa['cl_iso2'];
        $ds_pais=$rowpa['ds_pais'];
        $currency_code = $rowpa['currency_code'];




        switch ($fl_pais) {

            case '38':

                $symbol = "$";
                $symbo_tab = "$";
                break;
            case '226':

                $symbol = "$";
                $symbo_tab = "$";
                break;
            case '199':

                $symbol = "€";
                $symbo_tab = "&euro;";

                break;
            case '73':

                $symbol = "€";
                $symbo_tab = "&euro;";

                break;
            case '80':

                $symbol = "€";
                $symbo_tab = "&euro;";

                break;
            case '105':

                $symbol = "€";
                $symbo_tab = "&euro;";
			case '153':

                $symbol = "€";
                $symbo_tab = "&euro;";

                break;
            case '225':

                $symbol = "£";
                $symbo_tab = "&pound;";

                break;
            default:

                $symbol = "$";
                $symbo_tab = "$";

                break;

        }





        echo"
            <li>
                <a href='#$cl_iso2' data-toggle='tab'>
                    <i class='fa fa-fw fa-lg fa-globe'></i>$ds_pais ($symbo_tab $currency_code)
                </a>

            </li>
            ";


    }
    ?>
</ul>
<div id="tab_pais" class="tab-content padding-10 no-border">

    <div class="tab-pane fade in active" id="canada">


        <ul id="tab_app_0" class="nav nav-tabs bordered">
            <li class="active">
                <a href="#onlines" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-navicon"></i><?php echo ObtenEtiqueta(2386) ?>
                </a>
            </li>
            <li>
                <a href="#combineds" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-compress"></i><?php echo ObtenEtiqueta(2387) ?>
                </a>
            </li>
        </ul>

        <div id="tab_app_0" class="tab-content padding-10 no-border">

            <div class="tab-pane fade in active" id="onlines">


                <ul id="tab_app" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#canada" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2315) ?>
                        </a>
                    </li>
                    <li>
                        <a href="#internacional" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>

                <div id="tab_app" class="tab-content padding-10 no-border">

                    <div class="tab-pane fade in active" id="canada">

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding padding-bottom-10">


                                <table class="table table-striped" align="center" width="100%">
                                    <!--<thead>-->
                                    <tr>
                                        <th colspan="3" class="text-align-center">
                                            <?php echo ObtenEtiqueta(581) ?>
                                        </th>
                                    </tr>

                                    
                                    <tbody>
                                        <tr>
                                            <td colspan="2" width="50%" style="padding-top: 2%;">
                                                <?php echo ObtenEtiqueta(584) ?>
                                            </td>

                                            <td width="30%" align="right">

                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('app_fee', $app_fee, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"'); ?>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(585) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('tuition', $tuition, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"'); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <?php echo ObtenEtiqueta(586) ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo CampoTexto('ds_costos_ad', $ds_costos_ad, 50, 25, 'form-control');?>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('no_costos_ad', $no_costos_ad, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"'); ?>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>

                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(588) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('total_tuition', $total_tuition, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(589) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('total', $total, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>

                                    </tbody>
                                </table>


                                <?php  //Div_close_Resposive(); ?>



                                <?php
														 // Div_Start_Responsive();
														 Forma_Espacio();
														echo "
														  <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
															<tr>
															  <td align='right'>
																	<div class='form-group'>
																				<div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa' name='fg_total_programa' ";
														if(!empty($fg_total_programa))
															echo "checked";
														echo "><span> &nbsp; </span></label></div></div></td>
																</tr>
																<tr>
																   <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate' name='fg_tax_rate' ";
														if(!empty($fg_tax_rate))
															echo "checked";
														echo "><span>&nbsp;</span></label></div></div></td>
																</tr>
															  </table>";
														// Div_close_Resposive();

                                ?>


                            </div>
                        </div>


                    </div><!---END TAB-->


                    <div class="tab-pane fade" id="internacional">




                        <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding padding-bottom-10">

                                <?php
														# Payments - Program Costs
														//Forma_Espacio( );
														//Div_Start_Responsive();
                                ?>

                                <table class="table table-striped" align="center" width="100%">
                                    <!--<thead>-->
                                    <tr>
                                        <th colspan="3" class="text-align-center">
                                            <?php echo ObtenEtiqueta(581) ?>
                                        </th>
                                    </tr>

                                    <!--<tr>
																			<th colspan="2" align="left" style="font-weight:bold"><?php echo ObtenEtiqueta(582) ?> </th>
																			<th align="center" style="font-weight:bold"><?php echo ObtenEtiqueta(583) ?></th>
																		  </tr>-->

                                    <!--</thead>-->
                                    <tbody>
                                        <tr>
                                            <td colspan="2" width="50%" style="padding-top: 2%;">
                                                <?php echo ObtenEtiqueta(584) ?>
                                            </td>

                                            <td width="30%" align="right">

                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('app_fee_internacional', $app_fee_internacional, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"'); ?>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(585) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('tuition_internacional', $tuition_internacional, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"'); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <?php echo ObtenEtiqueta(586) ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo CampoTexto('ds_costos_ad_internacional', $ds_costos_ad_internacional, 50, 25, 'form-control');?>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('no_costos_ad_internacional', $no_costos_ad_internacional, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"'); ?>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>

                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(588) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('total_tuition_internacional', $total_tuition_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(589) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('total_internacional', $total_internacional, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>

                                    </tbody>
                                </table>


                                <?php  //Div_close_Resposive(); ?>



                                <?php
																 // Div_Start_Responsive();
																 Forma_Espacio();
																echo "
																  <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
																	<tr>
																	  <td align='right'>
																			<div class='form-group'>
																						<div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_internacional' name='fg_total_programa_internacional' ";
																if(!empty($fg_total_programa_internacional))
																	echo "checked";
																echo "><span> &nbsp; </span></label></div></div></td>
																		</tr>
																		<tr>
																		   <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_internacional' name='fg_tax_rate_internacional' ";
																if(!empty($fg_tax_rate_internacional))
																	echo "checked";
																echo "><span>&nbsp;</span></label></div></div></td>
																		</tr>
																	  </table>";
																// Div_close_Resposive();

                                ?>


                            </div>
                        </div>
                    </div><!---END TAB INTERNACIONAL-->
                </div>
                <!----END TABS GENERALES--->
            </div>
            <!---end tab onlines--->



            <div class="tab-pane fade " id="combineds">




                <ul id="tab_app_01" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#canada_combined" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2315) ?>
                        </a>
                    </li>
                    <li>
                        <a href="#internacional_combined" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>

                <div id="tab_app_01" class="tab-content padding-10 no-border">

                    <div class="tab-pane fade in active" id="canada_combined">

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding padding-bottom-10">

                                <?php
												# Payments - Program Costs
												//Forma_Espacio( );
												//Div_Start_Responsive();
                                ?>

                                <table class="table table-striped" align="center" width="100%">
                                    <!--<thead>-->
                                    <tr>
                                        <th colspan="3" class="text-align-center">
                                            <?php echo ObtenEtiqueta(581) ?>
                                        </th>
                                    </tr>

                                    <!--<tr>
																	<th colspan="2" align="left" style="font-weight:bold"><?php echo ObtenEtiqueta(582) ?> </th>
																	<th align="center" style="font-weight:bold"><?php echo ObtenEtiqueta(583) ?></th>
																  </tr>-->

                                    <!--</thead>-->
                                    <tbody>
                                        <tr>
                                            <td colspan="2" width="50%" style="padding-top: 2%;">
                                                <?php echo ObtenEtiqueta(584) ?>
                                            </td>

                                            <td width="30%" align="right">

                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('app_fee_combined', $app_fee_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"'); ?>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(585) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('tuition_combined', $tuition_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"'); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <?php echo ObtenEtiqueta(586) ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo CampoTexto('ds_costos_ad_combined', $ds_costos_ad_combined, 50, 25, 'form-control');?>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('no_costos_ad_combined', $no_costos_ad_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"'); ?>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>

                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(588) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('total_tuition_combined', $total_tuition_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(589) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('total_combined', $total_combined, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>

                                    </tbody>
                                </table>


                                <?php  //Div_close_Resposive(); ?>



                                <?php
														 // Div_Start_Responsive();
														 Forma_Espacio();
														echo "
														  <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
															<tr>
															  <td align='right'>
																	<div class='form-group'>
																				<div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_combined' name='fg_total_programa_combined' ";
														if(!empty($fg_total_programa_combined))
															echo "checked";
														echo "><span> &nbsp; </span></label></div></div></td>
																</tr>
																<tr>
																   <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_combined' name='fg_tax_rate_combined' ";
														if(!empty($fg_tax_rate_combined))
															echo "checked";
														echo "><span>&nbsp;</span></label></div></div></td>
																</tr>
															  </table>";
														// Div_close_Resposive();

                                ?>


                            </div>
                        </div>


                    </div><!---END TAB canada-->




                    <div class="tab-pane fade in active" id="internacional_combined">


                        <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding padding-bottom-10">

                                <?php
														# Payments - Program Costs
														//Forma_Espacio( );
														//Div_Start_Responsive();
                                ?>

                                <table class="table table-striped" align="center" width="100%">
                                    <!--<thead>-->
                                    <tr>
                                        <th colspan="3" class="text-align-center">
                                            <?php echo ObtenEtiqueta(581) ?>
                                        </th>
                                    </tr>

                                    <!--<tr>
																			<th colspan="2" align="left" style="font-weight:bold"><?php echo ObtenEtiqueta(582) ?> </th>
																			<th align="center" style="font-weight:bold"><?php echo ObtenEtiqueta(583) ?></th>
																		  </tr>-->

                                    <!--</thead>-->
                                    <tbody>
                                        <tr>
                                            <td colspan="2" width="50%" style="padding-top: 2%;">
                                                <?php echo ObtenEtiqueta(584) ?>
                                            </td>

                                            <td width="30%" align="right">

                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('app_fee_internacional_combined', $app_fee_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"'); ?>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(585) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('tuition_internacional_combined', $tuition_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"'); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <?php echo ObtenEtiqueta(586) ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo CampoTexto('ds_costos_ad_internacional_combined', $ds_costos_ad_internacional_combined, 50, 25, 'form-control');?>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('no_costos_ad_internacional_combined', $no_costos_ad_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"'); ?>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>

                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(588) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('total_tuition_internacional_combined', $total_tuition_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding-top: 2%;">
                                                <?php   echo ObtenEtiqueta(589) ?>
                                            </td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                        CAD $
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <?php echo CampoTexto('total_internacional_combined', $total_internacional_combined, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>

                                    </tbody>
                                </table>


                                <?php  //Div_close_Resposive(); ?>



                                <?php
																 // Div_Start_Responsive();
																 Forma_Espacio();
																echo "
																  <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
																	<tr>
																	  <td align='right'>
																			<div class='form-group'>
																						<div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_internacional_combined' name='fg_total_programa_internacional_combined' ";
																if(!empty($fg_total_programa_internacional_combined))
																	echo "checked";
																echo "><span> &nbsp; </span></label></div></div></td>
																		</tr>
																		<tr>
																		   <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_internacional_combined' name='fg_tax_rate_internacional_combined' ";
																if(!empty($fg_tax_rate_internacional_combined))
																	echo "checked";
																echo "><span>&nbsp;</span></label></div></div></td>
																		</tr>
																	  </table>";
																// Div_close_Resposive();

                                ?>


                            </div>
                        </div>
                    </div><!---END TAB INTERNACIONAL-->








                </div>













            </div>
            <!--end tabs combined--->


        </div><!---end tab_app_0 general--->

    </div>
    <div class="tab-pane fade " id="usa">

        <ul id="app_usa" class="nav nav-tabs bordered">
            <li class="active" id="tab_usa">
                <a href="#onlines_usa" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-navicon"></i><?php echo ObtenEtiqueta(2386) ?>
                </a>
            </li>
            <li>
                <a href="#combineds_usa" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-compress"></i><?php echo ObtenEtiqueta(2674) ?>
                </a>
            </li>
        </ul>

        <div id="app_usa" class="tab-content padding-10 no-border">

            <div class="tab-pane fade in active" id="onlines_usa">
                <ul id="conf_usa" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#usa_local" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2675) ?>
                        </a>
                    </li>
                    <li>
                        <a href="#international_usa" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>

                <div id="conf_usa" class="tab-content padding-10 no-border">
                    <!---ini usa local--->
                    <div class="tab-pane fade in active" id="usa_local">
                        <table class="table table-striped" align="center" width="100%">
                            <tr>
                                <th colspan="3" class="text-align-center">
                                    <?php echo ObtenEtiqueta(581) ?>
                                </th>
                            </tr>
                            <tbody>
                                <tr>
                                    <td colspan="2" width="50%" style="padding-top: 2%;">
                                        <?php echo ObtenEtiqueta(584) ?>
                                    </td>

                                    <td width="30%" align="right">

                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('app_fee_usa', $app_fee_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"'); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(585) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('tuition_usa', $tuition_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <?php echo ObtenEtiqueta(586) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo CampoTexto('ds_costos_ad_usa', $ds_costos_ad_usa, 50, 25, 'form-control');?>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('no_costos_ad_usa', $no_costos_ad_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"'); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>


                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(588) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_tuition_usa', $total_tuition_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(589) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_usa', $total_usa, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>


                            </tbody>
                        </table>

                        <?php
                                            Forma_Espacio();
                                            echo "
														  <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
															<tr>
															  <td align='right'>
																	<div class='form-group'>
																				<div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_usa' name='fg_total_programa_usa' ";
                                            if(!empty($fg_total_programa_usa))
                                                echo "checked";
                                            echo "><span> &nbsp; </span></label></div></div></td>
																</tr>
																<tr>
																   <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_usa' name='fg_tax_rate_usa' ";
                                            if(!empty($fg_tax_rate_usa))
                                                echo "checked";
                                            echo "><span>&nbsp;</span></label></div></div></td>
																</tr>
															  </table>";

                        ?>



                    </div>
                    <!---fin usa local--->

                    <!---ini international usa--->
                    <div class="tab-pane" id="international_usa">

                        <table class="table table-striped" align="center" width="100%">

                            <tr>
                                <th colspan="3" class="text-align-center">
                                    <?php echo ObtenEtiqueta(581) ?>
                                </th>
                            </tr>


                            <tbody>
                                <tr>
                                    <td colspan="2" width="50%" style="padding-top: 2%;">
                                        <?php echo ObtenEtiqueta(584) ?>
                                    </td>

                                    <td width="30%" align="right">

                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('app_fee_internacional_usa', $app_fee_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"'); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(585) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('tuition_internacional_usa', $tuition_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <?php echo ObtenEtiqueta(586) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo CampoTexto('ds_costos_ad_internacional_usa', $ds_costos_ad_internacional_usa, 50, 25, 'form-control');?>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('no_costos_ad_internacional_usa', $no_costos_ad_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"'); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(588) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_tuition_internacional_usa', $total_tuition_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(589) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_internacional_usa', $total_internacional_usa, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                            </tbody>
                        </table>

                        <?php
                                                    Forma_Espacio();
                                                    echo "
																          <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
																	        <tr>
																	          <td align='right'>
																			        <div class='form-group'>
																						        <div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_internacional_usa' name='fg_total_programa_internacional_usa' ";
                                                    if(!empty($fg_total_programa_internacional_usa))
                                                        echo "checked";
                                                    echo "><span> &nbsp; </span></label></div></div></td>
																		        </tr>
																		        <tr>
																		           <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_internacional_usa' name='fg_tax_rate_internacional_usa' ";
                                                    if(!empty($fg_tax_rate_internacional_usa))
                                                        echo "checked";
                                                    echo "><span>&nbsp;</span></label></div></div></td>
																		        </tr>
																	          </table>";

                        ?>


                    </div>
                    <!---fin international usa--->
                </div>


            </div>

            <div class="tab-pane" id="combineds_usa">
                <ul id="conf_combined_usa" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#usa_combined_local" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2675) ?>
                        </a>
                    </li>
                    <li>
                        <a href="#combined_international_usa" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>

                <div id="conf_combined_usa" class="tab-content padding-10 no-border">
                    <!---ini usa combinaded local--->
                    <div class="tab-pane fade in active" id="usa_combined_local">

                        <table class="table table-striped" align="center" width="100%">
                            <tr>
                                <th colspan="3" class="text-align-center">
                                    <?php echo ObtenEtiqueta(581) ?>
                                </th>
                            </tr>

                            <tbody>
                                <tr>
                                    <td colspan="2" width="50%" style="padding-top: 2%;">
                                        <?php echo ObtenEtiqueta(584) ?>
                                    </td>

                                    <td width="30%" align="right">

                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('app_fee_combined_usa', $app_fee_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"'); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(585) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('tuition_combined_usa', $tuition_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <?php echo ObtenEtiqueta(586) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo CampoTexto('ds_costos_ad_combined_usa', $ds_costos_ad_combined_usa, 50, 25, 'form-control');?>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('no_costos_ad_combined_usa', $no_costos_ad_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"'); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(588) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_tuition_combined_usa', $total_tuition_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(589) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_combined_usa', $total_combined_usa, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>

                            </tbody>
                        </table>


                        <?php

                                                    Forma_Espacio();
                                                    echo "
														          <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
															        <tr>
															          <td align='right'>
																	        <div class='form-group'>
																				        <div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_combined_usa' name='fg_total_programa_combined_usa' ";
                                                    if(!empty($fg_total_programa_combined_usa))
                                                        echo "checked";
                                                    echo "><span> &nbsp; </span></label></div></div></td>
																        </tr>
																        <tr>
																           <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_combined_usa' name='fg_tax_rate_combined_usa' ";
                                                    if(!empty($fg_tax_rate_combined_usa))
                                                        echo "checked";
                                                    echo "><span>&nbsp;</span></label></div></div></td>
																        </tr>
															          </table>";

                        ?>



                    </div>
                    <!---fin usa combinaded local--->

                    <!---ini usa combinaded international--->
                    <div class="tab-pane" id="combined_international_usa">
                        <table class="table table-striped" align="center" width="100%">

                            <tr>
                                <th colspan="3" class="text-align-center">
                                    <?php echo ObtenEtiqueta(581) ?>
                                </th>
                            </tr>


                            <tbody>
                                <tr>
                                    <td colspan="2" width="50%" style="padding-top: 2%;">
                                        <?php echo ObtenEtiqueta(584) ?>
                                    </td>

                                    <td width="30%" align="right">

                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('app_fee_internacional_combined_usa', $app_fee_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"'); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(585) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('tuition_internacional_combined_usa', $tuition_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <?php echo ObtenEtiqueta(586) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo CampoTexto('ds_costos_ad_internacional_combined_usa', $ds_costos_ad_internacional_combined_usa, 50, 25, 'form-control');?>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('no_costos_ad_internacional_combined_usa', $no_costos_ad_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"'); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(588) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_tuition_internacional_combined_usa', $total_tuition_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(589) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                USD $
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_internacional_combined_usa', $total_internacional_combined_usa, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>

                            </tbody>
                        </table>

                        <?php
                                            // Div_Start_Responsive();
                                            Forma_Espacio();
                                            echo "
																  <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
																	<tr>
																	  <td align='right'>
																			<div class='form-group'>
																						<div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_internacional_combined_usa' name='fg_total_programa_internacional_combined_usa' ";
                                            if(!empty($fg_total_programa_internacional_combined_usa))
                                                echo "checked";
                                            echo "><span> &nbsp; </span></label></div></div></td>
																		</tr>
																		<tr>
																		   <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_internacional_combined_usa' name='fg_tax_rate_internacional_combined_usa' ";
                                            if(!empty($fg_tax_rate_internacional_combined_usa))
                                                echo "checked";
                                            echo "><span>&nbsp;</span></label></div></div></td>
																		</tr>
																	  </table>";
                                            // Div_close_Resposive();

                        ?>

                    </div>
                    <!---fin usa combinaded international--->
                </div>
            </div>
        </div>


    </div>

    <?php

    #get all countries add fees
    $rspb = EjecutaQuery($Queryp);
    for ($b = 0; $rowpb = RecuperaRegistro($rspb); $b++) {
        $fl_pais = $rowpb['fl_pais'];


        $app_fee = $rowpb['mn_app_fee'];
        $tuition = $rowpb['mn_tuition'];
        $ds_costos_ad = $rowpb['ds_costs'];
        $no_costos_ad = $rowpb['mn_costs'];

        $total_tuition = 0;
        $total =0;

        $app_fee_internacional = $rowpb['mn_app_fee_internacional'];
        $tuition_internacional = $rowpb['mn_tuition_internacional'];
        $ds_costos_ad_internacional = $rowpb['ds_costs_internacional'];
        $no_costos_ad_internacional = $rowpb['mn_costs_internacional'];

        $total_tuition_internacional = 0;
        $total_internacional = 0;


        $app_fee_combined = $rowpb['mn_app_fee_combined'];
        $tuition_combined = $rowpb['mn_tuition_combined'];
        $no_costos_ad_combined = $rowpb['mn_costs_combined'];
        $ds_costos_ad_combined = $rowpb['ds_costs_combined'];

        $total_tuition_combined = 0;
        $total_combined = 0;

        $app_fee_internacional_combined = $rowpb['mn_app_fee_internacional_combined'];
        $tuition_internacional_combined = $rowpb['mn_tuition_internacional_combined'];
        $no_costos_ad_internacional_combined = $rowpb['mn_costs_internacional_combined'];
        $ds_costos_ad_internacional_combined = $rowpb['ds_costs_internacional_combined'];

        $total_tuition_internacional_combined = 0;
        $total_internacional_combined = 0;

        $fg_total_programa = $rowpb['fg_total_programa'];
        $fg_tax_rate = $rowpb['fg_taxes'];

        $fg_total_programa_internacional= $rowpb['fg_total_programa_internacional'];
        $fg_tax_rate_internacional = $rowpb['fg_taxes_internacional'];


        $fg_total_programa_combined = $rowpb['fg_total_programa_combined'];
        $fg_tax_rate_combined = $rowpb['fg_taxes_combined'];

        $fg_total_programa_internacional_combined = $rowpb['fg_total_programa_internacional_combined'];
        $fg_tax_rate_internacional_combined = $rowpb['fg_taxes_internacional_combined'];



        $Querypa = "SELECT cl_iso2,ds_pais,currency_code,symbol FROM c_pais WHERE fl_pais=$fl_pais ";
        $rowpa = RecuperaValor($Querypa);
        $cl_iso2=$rowpa['cl_iso2'];
        $ds_pais=$rowpa['ds_pais'];
        $currency_code = $rowpa['currency_code'];
        $symbol = $rowpa['symbol'];
    ?>

   





    <div class="tab-pane fade " id="<?php echo $cl_iso2;?>">
        <ul id="app_<?php echo $cl_iso2;?>" class="nav nav-tabs bordered">
            <li class="active" id="tab_<?php echo $cl_iso2; ?>">
                <a href="#onlines_<?php echo $cl_iso2; ?>" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-navicon"></i><?php echo ObtenEtiqueta(2386) ?>
                </a>
            </li>
            <li>
                <a href="#combineds_<?php echo $cl_iso2; ?>" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-compress"></i>Combined (Online+Onsite <?php echo $ds_pais;?>)
                </a>
            </li>
        </ul>

        <div id="app_<?php echo $cl_iso2;?>" class="tab-content padding-10 no-border">
            <div class="tab-pane fade in active" id="onlines_<?php echo $cl_iso2; ?>">
                <ul id="conf_<?php echo $cl_iso2; ?>" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#<?php echo $cl_iso2; ?>_local" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo $ds_pais;?> Students
                        </a>
                    </li>
                    <li>
                        <a href="#international_<?php echo $cl_iso2; ?>" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>
                <div id="conf_<?php echo $cl_iso2; ?>" class="tab-content padding-10 no-border">
                    <!---ini local--->
                    <div class="tab-pane fade in active" id="<?php echo $cl_iso2; ?>_local">
                        <table class="table table-striped" align="center" width="100%">
                            <tr>
                                <th colspan="3" class="text-align-center">
                                    <?php echo ObtenEtiqueta(581) ?>
                                </th>
                            </tr>
                            <tbody>
                                <tr>
                                    <td colspan="2" width="50%" style="padding-top: 2%;">
                                        <?php echo ObtenEtiqueta(584) ?>
                                    </td>

                                    <td width="30%" align="right">

                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol ; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('app_fee_'.$cl_iso2, $app_fee, 10, 10, 'form-control', False, 'style=\'text-align:right\' onchange=\'calcula_costo_country("'.$cl_iso2.'") \' '); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(585) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol;?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('tuition_'. $cl_iso2, $tuition, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_country("' . $cl_iso2 . '")\'  '); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <?php echo ObtenEtiqueta(586) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo CampoTexto('ds_costos_ad_'. $cl_iso2, $ds_costos_ad, 50, 25, 'form-control');?>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('no_costos_ad_'. $cl_iso2, $no_costos_ad, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_country("' . $cl_iso2 . '")\''); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>


                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(588) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_tuition_'. $cl_iso2, $total_tuition, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(589) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_'. $cl_iso2, $total, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>


                            </tbody>
                        </table>
                        <?php
                            Forma_Espacio();
                            echo "
								    <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
									    <tr>
											<td align='right'>
												<div class='form-group'>
																<div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_$cl_iso2' name='fg_total_programa_$cl_iso2' ";
                            if(!empty($fg_total_programa))
                                echo "checked";
                            echo "><span> &nbsp; </span></label></div></div></td>
												</tr>
												<tr>
													<td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_$cl_iso2' name='fg_tax_rate_$cl_iso2' ";
                            if(!empty($fg_tax_rate))
                                echo "checked";
                            echo "><span>&nbsp;</span></label></div></div></td>
												</tr>
												</table>";

                        ?>

                    </div>
                    <!---fin local -->
                    <!---ini online international--->
                    <div class="tab-pane" id="international_<?php echo $cl_iso2; ?>">

                        <table class="table table-striped" align="center" width="100%">

                            <tr>
                                <th colspan="3" class="text-align-center">
                                    <?php echo ObtenEtiqueta(581) ?>
                                </th>
                            </tr>


                            <tbody>
                                <tr>
                                    <td colspan="2" width="50%" style="padding-top: 2%;">
                                        <?php echo ObtenEtiqueta(584) ?>
                                    </td>

                                    <td width="30%" align="right">

                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('app_fee_internacional_'.$cl_iso2, $app_fee_internacional, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_internacional_country("' . $cl_iso2 . '")\' '); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(585) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('tuition_internacional_'. $cl_iso2, $tuition_internacional, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_internacional_country("' . $cl_iso2 . '")\''); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <?php echo ObtenEtiqueta(586) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo CampoTexto('ds_costos_ad_internacional_'. $cl_iso2, $ds_costos_ad_internacional, 50, 25, 'form-control');?>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('no_costos_ad_internacional_'. $cl_iso2, $no_costos_ad_internacional, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_internacional_country("' . $cl_iso2 . '")\''); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(588) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_tuition_internacional_'. $cl_iso2, $total_tuition_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(589) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_internacional_'. $cl_iso2, $total_internacional, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                            </tbody>
                        </table>

                        <?php
                            Forma_Espacio();
                            echo "
													<table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
													<tr>
														<td align='right'>
															<div class='form-group'>
																		<div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_internacional_$cl_iso2' name='fg_total_programa_internacional_$cl_iso2' ";
                            if(!empty($fg_total_programa_internacional))
                                echo "checked";
                            echo "><span> &nbsp; </span></label></div></div></td>
														</tr>
														<tr>
															<td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_internacional_$cl_iso2' name='fg_tax_rate_internacional_$cl_iso2' ";
                            if(!empty($fg_tax_rate_internacional))
                                echo "checked";
                            echo "><span>&nbsp;</span></label></div></div></td>
														</tr>
														</table>";

                        ?>

                    </div>
                    <!-- fin online international-->
                </div>

            </div>

            <div class="tab-pane" id="combineds_<?php echo $cl_iso2; ?>">
                <ul id="conf_combined_<?php echo $cl_iso2; ?>" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#<?php echo $cl_iso2; ?>_combined_local" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo $ds_pais;?> Students
                        </a>
                    </li>
                    <li>
                        <a href="#combined_international_<?php echo $cl_iso2; ?>" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>
                <div id="conf_combined_<?php echo $cl_iso2; ?>" class="tab-content padding-10 no-border">
                    <!---ini usa combinaded local--->
                    <div class="tab-pane fade in active" id="<?php echo $cl_iso2; ?>_combined_local">
                        <table class="table table-striped" align="center" width="100%">
                            <tr>
                                <th colspan="3" class="text-align-center">
                                    <?php echo ObtenEtiqueta(581) ?>
                                </th>
                            </tr>

                            <tbody>
                                <tr>
                                    <td colspan="2" width="50%" style="padding-top: 2%;">
                                        <?php echo ObtenEtiqueta(584) ?>
                                    </td>

                                    <td width="30%" align="right">

                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('app_fee_combined_'. $cl_iso2, $app_fee_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_combined_country("'. $cl_iso2.'")\' '); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(585) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol; ?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('tuition_combined_'. $cl_iso2, $tuition_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_combined_country("'. $cl_iso2.'")\''); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <?php echo ObtenEtiqueta(586) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo CampoTexto('ds_costos_ad_combined_'. $cl_iso2, $ds_costos_ad_combined, 50, 25, 'form-control');?>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol;?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('no_costos_ad_combined_'. $cl_iso2, $no_costos_ad_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_combined_country("'. $cl_iso2.'")\''); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(588) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol;?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_tuition_combined_'. $cl_iso2, $total_tuition_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(589) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol;?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_combined_'. $cl_iso2, $total_combined, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>

                            </tbody>
                        </table>

                        <?php

                            Forma_Espacio();
                            echo "
											<table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
											<tr>
												<td align='right'>
													<div class='form-group'>
																<div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_combined_$cl_iso2' name='fg_total_programa_combined_$cl_iso2' ";
                            if(!empty($fg_total_programa_combined))
                                echo "checked";
                            echo "><span> &nbsp; </span></label></div></div></td>
												</tr>
												<tr>
													<td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_combined_$cl_iso2' name='fg_tax_rate_combined_$cl_iso2' ";
                            if(!empty($fg_tax_rate_combined))
                                echo "checked";
                            echo "><span>&nbsp;</span></label></div></div></td>
												</tr>
												</table>";

                        ?>

                    </div>
                    <!---ini usa combinaded international--->
                    <div class="tab-pane" id="combined_international_<?php echo $cl_iso2; ?>">
                        <table class="table table-striped" align="center" width="100%">

                            <tr>
                                <th colspan="3" class="text-align-center">
                                    <?php echo ObtenEtiqueta(581) ?>
                                </th>
                            </tr>


                            <tbody>
                                <tr>
                                    <td colspan="2" width="50%" style="padding-top: 2%;">
                                        <?php echo ObtenEtiqueta(584) ?>
                                    </td>

                                    <td width="30%" align="right">

                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol;?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('app_fee_internacional_combined_'. $cl_iso2, $app_fee_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_internacional_combined_country("'.$cl_iso2.'")\' '); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(585) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol;?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('tuition_internacional_combined_'. $cl_iso2, $tuition_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_internacional_combined_country("'. $cl_iso2.'")\''); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <?php echo ObtenEtiqueta(586) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo CampoTexto('ds_costos_ad_internacional_combined_'. $cl_iso2, $ds_costos_ad_internacional_combined, 50, 25, 'form-control');?>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol;?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('no_costos_ad_internacional_combined_'. $cl_iso2, $no_costos_ad_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_internacional_combined_country("'. $cl_iso2.'")\''); ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(588) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol;?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_tuition_internacional_combined_'. $cl_iso2, $total_tuition_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 2%;">
                                        <?php   echo ObtenEtiqueta(589) ?>
                                    </td>

                                    <td>
                                        <div class="row">
                                            <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                <?php echo $symbol;?> 
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?php echo CampoTexto('total_internacional_combined_'. $cl_iso2, $total_internacional_combined, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                            </div>
                                        </div>

                                    </td>

                                </tr>

                            </tbody>
                        </table>

                        <?php
                                            // Div_Start_Responsive();
                                            Forma_Espacio();
                                            echo "
																  <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>
																	<tr>
																	  <td align='right'>
																			<div class='form-group'>
																						<div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_internacional_combined_$cl_iso2' name='fg_total_programa_internacional_combined_$cl_iso2' ";
                                            if(!empty($fg_total_programa_internacional_combined))
                                                echo "checked";
                                            echo "><span> &nbsp; </span></label></div></div></td>
																		</tr>
																		<tr>
																		   <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_internacional_combined_$cl_iso2' name='fg_tax_rate_internacional_combined_$cl_iso2' ";
                                            if(!empty($fg_tax_rate_internacional_combined))
                                                echo "checked";
                                            echo "><span>&nbsp;</span></label></div></div></td>
																		</tr>
																	  </table>";
                                            // Div_close_Resposive();

                        ?>
                    </div>
                </div>

            </div>

        </div>



    </div>

    <?php
    }
    ?>


</div>


