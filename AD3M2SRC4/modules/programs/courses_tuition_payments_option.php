<?php
# Libreria de funciones
require '../../lib/general.inc.php';

# Verifica que exista una sesion valida en el cookie y la resetea
ValidaSesion();

$clave = RecibeParametroNumerico('clave');

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

<ul id="countrys" class="nav nav-tabs bordered">
    <li class="active">
        <a href="#canada1" data-toggle="tab">
            <i class="fa fa-fw fa-lg fa-globe"></i>Canada
        </a>
    </li>
    <li>
        <a href="#usa1" data-toggle="tab">
            <i class="fa fa-fw fa-lg fa-globe"></i>USA
        </a>
    </li>

    <?php

	#Recuperamos los pais agregados
    $Queryp = "SELECT * FROM k_programa_costos_pais WHERE fl_pais<>226 AND fl_programa=$clave ORDER BY fl_programa_costos asc ";
    $rsp = EjecutaQuery($Queryp);

    for($a=0;$rowp=RecuperaRegistro($rsp);$a++){
        $fl_pais = $rowp['fl_pais'];

	


        $Querypa="SELECT cl_iso2,ds_pais FROM c_pais WHERE fl_pais=$fl_pais ";
        $rowpa=RecuperaValor($Querypa);
        $cl_iso2=$rowpa['cl_iso2'];
        $ds_pais=$rowpa['ds_pais'];

    ?>
		<li>
			<a href="#<?php echo $cl_iso2;?>1" data-toggle="tab">
				<i class="fa fa-fw fa-lg fa-globe"></i><?php echo $ds_pais;?>
			</a>
		</li>
	<?php 
	
    }
	?>

</ul>

<div id="countrys" class="tab-content padding-10 no-border">

    <!---ini canada--->
    <div class="tab-pane fade in active" id="canada1">

        <ul id="tab_app3" class="nav nav-tabs bordered">
            <li class="active">
                <a href="#online" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-navicon"></i><?php echo ObtenEtiqueta(2386);?>
                </a>
            </li>
            <li>
                <a href="#combined" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-compress"></i><?php echo ObtenEtiqueta(2387);?>
                </a>
            </li>
        </ul>


        <div id="tab_app3" class="tab-content padding-10 no-border">

            <div class="tab-pane fade in active" id="online">

                <ul id="tab_app2" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#canadan" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2315) ?>
                        </a>
                    </li>
                    <li>
                        <a href="#internacionaln" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>
                <!----ini local---->
                <div id="tab_app2" class="tab-content padding-10 no-border">

                    <div class="tab-pane fade in active" id="canadan">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding">
                                <?php
															       # Payments - Payment Options
														    // Forma_Doble_Ini( );
														    // Div_Start_Responsive();
														    echo "
															      <table class='table table-striped' width='100%'>
																    <tr class='css_tabla_encabezado'>
																      <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
														    CampoTexto('no_payments_a', $no_payments_a, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
														    echo "
															      </td>
															      <td  align='center'>";
														    CampoTexto('frequency_a', $frequency_a, 15, 10, 'form-control');
														    echo "
															      </td>
															      <td  align='right'>";
														    CampoTexto('interes_a', $interes_a, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
														    echo "
														      </td>
														      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_due_a', $amount_due_a, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														    echo"</td>
																	    </tr></table>";
														    echo "
															      </td>
															      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td>";
													       CampoTexto('amount_paid_a', $amount_paid_a, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

															    echo"</td>
													    </tr></table>";
														    echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle'>
															      <td align='center'>B</td>
															      <td  align='center'>";
														    CampoTexto('no_payments_b', $no_payments_b, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
														    echo "
															      </td>
															      <td  align='center'>";
														    CampoTexto('frequency_b', $frequency_b, 15, 10, 'form-control');
														    echo "
															      </td>
															      <td  align='right'>";
														    CampoTexto('interes_b', $interes_b, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
														    echo "
															      </td>
															      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_due_b', $amount_due_b, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														    echo"</td>
																	    </tr></table>";

														    echo "
															      </td>
															      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_paid_b', $amount_paid_b, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														    echo"</td>
																	    </tr></table>";
														    echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle_bg'>
															      <td align='center'>C</td>
															      <td  align='center'>";
														    CampoTexto('no_payments_c', $no_payments_c, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
														    echo "
															      </td>
															      <td  align='center'>";
														    CampoTexto('frequency_c', $frequency_c, 15, 10, 'form-control');
														    echo "
															      </td>
															      <td  align='right'>";
														    CampoTexto('interes_c', $interes_c, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
														    echo "
															      </td>
															      <td  align='right'>
																	    <table><tr><td>CAD $ &nbsp;</td>
																			       <td>
															       ";
														    CampoTexto('amount_due_c', $amount_due_c, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														    echo"</td>
																	    </tr></table>";

														    echo "
															      </td>
															      <td  align='right'> <table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_paid_c', $amount_paid_c, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														    echo"</td>
																	    </tr></table>";
														    echo "
																      </td>
																    </tr>
																    <tr class='css_tabla_detalle'>
																      <td align='center'>D</td>
																      <td  align='center'>";
														    CampoTexto('no_payments_d', $no_payments_d, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
														    echo "
																      </td>
																      <td  align='center'>";
														    CampoTexto('frequency_d', $frequency_d, 15, 10, 'form-control');
														    echo "
															      </td>
															      <td  align='right'>";
														    CampoTexto('interes_d', $interes_d, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
														    echo "
															      </td>
															      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_due_d', $amount_due_d, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														    echo"</td>
																	    </tr></table>";
														    echo "
														      </td>
														      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_paid_d', $amount_paid_d, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														    echo"</td>
																	    </tr></table>";
														    echo "
															      </td>
															    </tr>
														      </table>";
														    // Div_close_Resposive();
														    // Forma_Doble_Fin( );

                                ?>

                            </div>
                        </div>

                    </div><!----end canadian tab-->



                    <div class="tab-pane fade" id="internacionaln">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding">
                                <?php
															       # Payments - Payment Options
														    // Forma_Doble_Ini( );
														    // Div_Start_Responsive();
														    echo "
															      <table class='table table-striped' width='100%'>
																    <tr class='css_tabla_encabezado'>
																      <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
														    CampoTexto('no_payments_a_internacional', $no_payments_a_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
														    echo "
															      </td>
															      <td  align='center'>";
														    CampoTexto('frequency_a_internacional', $frequency_a_internacional, 15, 10, 'form-control');
														    echo "
															      </td>
															      <td  align='right'>";
														    CampoTexto('interes_a_internacional', $interes_a_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
														    echo "
														      </td>
														      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_due_a_internacional', $amount_due_a_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														    echo"</td>
																	    </tr></table>";
														    echo "
															      </td>
															      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td>";
													       CampoTexto('amount_paid_a_internacional', $amount_paid_a_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

															    echo"</td>
													    </tr></table>";
														    echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle'>
															      <td align='center'>B</td>
															      <td  align='center'>";
														    CampoTexto('no_payments_b_internacional', $no_payments_b_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
														    echo "
															      </td>
															      <td  align='center'>";
														    CampoTexto('frequency_b_internacional', $frequency_b_internacional, 15, 10, 'form-control');
														    echo "
															      </td>
															      <td  align='right'>";
														    CampoTexto('interes_b_internacional', $interes_b_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
														    echo "
															      </td>
															      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_due_b_internacional', $amount_due_b_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														    echo"</td>
																	    </tr></table>";

														    echo "
															      </td>
															      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_paid_b_internacional', $amount_paid_b_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														    echo"</td>
																	    </tr></table>";
														    echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle_bg'>
															      <td align='center'>C</td>
															      <td  align='center'>";
														    CampoTexto('no_payments_c_internacional', $no_payments_c_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
														    echo "
															      </td>
															      <td  align='center'>";
														    CampoTexto('frequency_c_internacional', $frequency_c_internacional, 15, 10, 'form-control');
														    echo "
															      </td>
															      <td  align='right'>";
														    CampoTexto('interes_c_internacional', $interes_c_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
														    echo "
															      </td>
															      <td  align='right'>
																	    <table><tr><td>CAD $ &nbsp;</td>
																			       <td>
															       ";
														    CampoTexto('amount_due_c_internacional', $amount_due_c_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														    echo"</td>
																	    </tr></table>";

														    echo "
															      </td>
															      <td  align='right'> <table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_paid_c_internacional', $amount_paid_c_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														    echo"</td>
																	    </tr></table>";
														    echo "
																      </td>
																    </tr>
																    <tr class='css_tabla_detalle'>
																      <td align='center'>D</td>
																      <td  align='center'>";
														    CampoTexto('no_payments_d_internacional', $no_payments_d_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
														    echo "
																      </td>
																      <td  align='center'>";
														    CampoTexto('frequency_d_internacional', $frequency_d_internacional, 15, 10, 'form-control');
														    echo "
															      </td>
															      <td  align='right'>";
														    CampoTexto('interes_d_internacional', $interes_d_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
														    echo "
															      </td>
															      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_due_d_internacional', $amount_due_d_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														    echo"</td>
																	    </tr></table>";
														    echo "
														      </td>
														      <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			       <td> ";
														    CampoTexto('amount_paid_d_internacional', $amount_paid_d_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														    echo"</td>
																	    </tr></table>";
														    echo "
															      </td>
															    </tr>
														      </table>";
														    // Div_close_Resposive();
														    // Forma_Doble_Fin( );

                                ?>

                            </div>
                        </div>
                    </div><!----end tab internacionaln---->
                </div>

                <!---fin-- local--->

            </div>
            <!----end tab online-->

            <!---ini combined---->
            <div class="tab-pane fade " id="combined">

                <ul id="tab_app2" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#canadian" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2315) ?>
                        </a>
                    </li>
                    <li>
                        <a href="#internacionalin" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>



                <div id="tab_app2" class="tab-content padding-10 no-border">

                    <div class="tab-pane fade in active" id="canadian">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding">


                                <?php
													    # Payments - Payment Options

														echo "
															  <table class='table table-striped' width='100%'>
																<tr class='css_tabla_encabezado'>
																  <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
														CampoTexto('no_payments_a_combined', $no_payments_a_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_a_combined', $frequency_a_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_a_combined', $interes_a_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"');
														echo "
														  </td>
														  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_a_combined', $amount_due_a_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td>";
													   CampoTexto('amount_paid_a_combined', $amount_paid_a_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

															echo"</td>
													</tr></table>";
														echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle'>
															  <td align='center'>B</td>
															  <td  align='center'>";
														CampoTexto('no_payments_b_combined', $no_payments_b_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_b_combined', $frequency_b_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_b_combined', $interes_b_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"');
														echo "
															  </td>
															  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_b_combined', $amount_due_b_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														echo"</td>
																	</tr></table>";

														echo "
															  </td>
															  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_b_combined', $amount_paid_b_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle_bg'>
															  <td align='center'>C</td>
															  <td  align='center'>";
														CampoTexto('no_payments_c_combined', $no_payments_c_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_c_combined', $frequency_c_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_c_combined', $interes_c_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"');
														echo "
															  </td>
															  <td  align='right'>
																	<table><tr><td>CAD $ &nbsp;</td>
																			   <td>
															   ";
														CampoTexto('amount_due_c_combined', $amount_due_c_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														echo"</td>
																	</tr></table>";

														echo "
															  </td>
															  <td  align='right'> <table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_c_combined', $amount_paid_c_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
																  </td>
																</tr>
																<tr class='css_tabla_detalle'>
																  <td align='center'>D</td>
																  <td  align='center'>";
														CampoTexto('no_payments_d_combined', $no_payments_d_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"');
														echo "
																  </td>
																  <td  align='center'>";
														CampoTexto('frequency_d_combined', $frequency_d_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_d_combined', $interes_d_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined()"');
														echo "
															  </td>
															  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_d_combined', $amount_due_d_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
														  </td>
														  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_d_combined', $amount_paid_d_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															</tr>
														  </table>";


                                ?>

                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="internacionalin">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding">

                                <?php
															   # Payments - Payment Options

														echo "
															  <table class='table table-striped' width='100%'>
																<tr class='css_tabla_encabezado'>
																  <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
														CampoTexto('no_payments_a_internacional_combined', $no_payments_a_internacional_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_a_internacional_combined', $frequency_a_internacional_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_a_internacional_combined', $interes_a_internacional_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"');
														echo "
														  </td>
														  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_a_internacional_combined', $amount_due_a_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td>";
													   CampoTexto('amount_paid_a_internacional_combined', $amount_paid_a_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

															echo"</td>
													</tr></table>";
														echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle'>
															  <td align='center'>B</td>
															  <td  align='center'>";
														CampoTexto('no_payments_b_internacional_combined', $no_payments_b_internacional_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_b_internacional_combined', $frequency_b_internacional_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_b_internacional_combined', $interes_b_internacional_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"');
														echo "
															  </td>
															  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_b_internacional_combined', $amount_due_b_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														echo"</td>
																	</tr></table>";

														echo "
															  </td>
															  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_b_internacional_combined', $amount_paid_b_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle_bg'>
															  <td align='center'>C</td>
															  <td  align='center'>";
														CampoTexto('no_payments_c_internacional_combined', $no_payments_c_internacional_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_c_internacional_combined', $frequency_c_internacional_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_c_internacional_combined', $interes_c_internacional_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"');
														echo "
															  </td>
															  <td  align='right'>
																	<table><tr><td>CAD $ &nbsp;</td>
																			   <td>
															   ";
														CampoTexto('amount_due_c_internacional_combined', $amount_due_c_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														echo"</td>
																	</tr></table>";

														echo "
															  </td>
															  <td  align='right'> <table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_c_internacional_combined', $amount_paid_c_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
																  </td>
																</tr>
																<tr class='css_tabla_detalle'>
																  <td align='center'>D</td>
																  <td  align='center'>";
														CampoTexto('no_payments_d_internacional_combined', $no_payments_d_internacional_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"');
														echo "
																  </td>
																  <td  align='center'>";
														CampoTexto('frequency_d_internacional_combined', $frequency_d_internacional_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_d_internacional_combined', $interes_d_internacional_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined()"');
														echo "
															  </td>
															  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_d_internacional_combined', $amount_due_d_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
														  </td>
														  <td  align='right'><table><tr><td>CAD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_d_internacional_combined', $amount_paid_d_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															</tr>
														  </table>";


                                ?>




                            </div>
                        </div>

                    </div>



                </div>



            </div>
            <!----end combined-->

        </div>
    </div>
    <!----fin canada--->

    <!---ini usa--->
    <div class="tab-pane " id="usa1">

        <ul id="tab_usa" class="nav nav-tabs bordered">
            <li class="active">
                <a href="#onlines_usa1" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-navicon"></i><?php echo ObtenEtiqueta(2386) ?>
                </a>
            </li>
            <li>
                <a href="#combineds_usa1" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-compress"></i><?php echo ObtenEtiqueta(2674) ?>
                </a>
            </li>
        </ul>

        <div id="tab_usa" class="tab-content padding-10 no-border">

            <!---ini onlines--->
            <div class="tab-pane fade in active" id="onlines_usa1">
                <ul id="tabs_usa_online1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#local_usa_online" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2675) ?>
                        </a>
                    </li>
                    <li>
                        <a href="#internacional_usa_online" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>

                <div id="tabs_usa_online1" class="tab-content padding-10 no-border">
                    <!--ini usa online--->
                    <div class="tab-pane in active" id="local_usa_online">

                        <?php
                                             # Payments - Payment Options

                                             echo "
															      <table class='table table-striped' width='100%'>
																    <tr class='css_tabla_encabezado'>
																      <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
                                             CampoTexto('no_payments_a_usa', $no_payments_a_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"');
                                             echo "
															      </td>
															      <td  align='center'>";
                                             CampoTexto('frequency_a_usa', $frequency_a_usa, 15, 10, 'form-control');
                                             echo "
															      </td>
															      <td  align='right'>";
                                             CampoTexto('interes_a_usa', $interes_a_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"');
                                             echo "
														      </td>
														      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_due_a_usa', $amount_due_a_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
																	    </tr></table>";
                                             echo "
															      </td>
															      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td>";
                                             CampoTexto('amount_paid_a_usa', $amount_paid_a_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
													    </tr></table>";
                                             echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle'>
															      <td align='center'>B</td>
															      <td  align='center'>";
                                             CampoTexto('no_payments_b_usa', $no_payments_b_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"');
                                             echo "
															      </td>
															      <td  align='center'>";
                                             CampoTexto('frequency_b_usa', $frequency_b_usa, 15, 10, 'form-control');
                                             echo "
															      </td>
															      <td  align='right'>";
                                             CampoTexto('interes_b_usa', $interes_b_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"');
                                             echo "
															      </td>
															      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_due_b_usa', $amount_due_b_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                             echo"</td>
																	    </tr></table>";

                                             echo "
															      </td>
															      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_paid_b_usa', $amount_paid_b_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
																	    </tr></table>";
                                             echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle_bg'>
															      <td align='center'>C</td>
															      <td  align='center'>";
                                             CampoTexto('no_payments_c_usa', $no_payments_c_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"');
                                             echo "
															      </td>
															      <td  align='center'>";
                                             CampoTexto('frequency_c_usa', $frequency_c_usa, 15, 10, 'form-control');
                                             echo "
															      </td>
															      <td  align='right'>";
                                             CampoTexto('interes_c_usa', $interes_c_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"');
                                             echo "
															      </td>
															      <td  align='right'>
																	    <table><tr><td>USD $ &nbsp;</td>
																			       <td>
															       ";
                                             CampoTexto('amount_due_c_usa', $amount_due_c_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                             echo"</td>
																	    </tr></table>";

                                             echo "
															      </td>
															      <td  align='right'> <table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_paid_c_usa', $amount_paid_c_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
																	    </tr></table>";
                                             echo "
																      </td>
																    </tr>
																    <tr class='css_tabla_detalle'>
																      <td align='center'>D</td>
																      <td  align='center'>";
                                             CampoTexto('no_payments_d_usa', $no_payments_d_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"');
                                             echo "
																      </td>
																      <td  align='center'>";
                                             CampoTexto('frequency_d_usa', $frequency_d_usa, 15, 10, 'form-control');
                                             echo "
															      </td>
															      <td  align='right'>";
                                             CampoTexto('interes_d_usa', $interes_d_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_usa()"');
                                             echo "
															      </td>
															      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_due_d_usa', $amount_due_d_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
																	    </tr></table>";
                                             echo "
														      </td>
														      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_paid_d_usa', $amount_paid_d_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
																	    </tr></table>";
                                             echo "
															      </td>
															    </tr>
														      </table>";


                        ?>

                    </div>
                    <!--fin usa online--->

                    <!--ini usa internatio--->
                    <div class="tab-pane " id="internacional_usa_online">
                        <?php
                                            # Payments - Payment Options

                                            echo "
															      <table class='table table-striped' width='100%'>
																    <tr class='css_tabla_encabezado'>
																      <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
                                            CampoTexto('no_payments_a_internacional_usa', $no_payments_a_internacional_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"');
                                            echo "
															      </td>
															      <td  align='center'>";
                                            CampoTexto('frequency_a_internacional_usa', $frequency_a_internacional_usa, 15, 10, 'form-control');
                                            echo "
															      </td>
															      <td  align='right'>";
                                            CampoTexto('interes_a_internacional_usa', $interes_a_internacional_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"');
                                            echo "
														      </td>
														      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_due_a_internacional_usa', $amount_due_a_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
																	    </tr></table>";
                                            echo "
															      </td>
															      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td>";
                                            CampoTexto('amount_paid_a_internacional_usa', $amount_paid_a_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
													    </tr></table>";
                                            echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle'>
															      <td align='center'>B</td>
															      <td  align='center'>";
                                            CampoTexto('no_payments_b_internacional_usa', $no_payments_b_internacional_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"');
                                            echo "
															      </td>
															      <td  align='center'>";
                                            CampoTexto('frequency_b_internacional_usa', $frequency_b_internacional_usa, 15, 10, 'form-control');
                                            echo "
															      </td>
															      <td  align='right'>";
                                            CampoTexto('interes_b_internacional_usa', $interes_b_internacional_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"');
                                            echo "
															      </td>
															      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_due_b_internacional_usa', $amount_due_b_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                            echo"</td>
																	    </tr></table>";

                                            echo "
															      </td>
															      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_paid_b_internacional_usa', $amount_paid_b_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
																	    </tr></table>";
                                            echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle_bg'>
															      <td align='center'>C</td>
															      <td  align='center'>";
                                            CampoTexto('no_payments_c_internacional_usa', $no_payments_c_internacional_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"');
                                            echo "
															      </td>
															      <td  align='center'>";
                                            CampoTexto('frequency_c_internacional_usa', $frequency_c_internacional_usa, 15, 10, 'form-control');
                                            echo "
															      </td>
															      <td  align='right'>";
                                            CampoTexto('interes_c_internacional_usa', $interes_c_internacional_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"');
                                            echo "
															      </td>
															      <td  align='right'>
																	    <table><tr><td>USD $ &nbsp;</td>
																			       <td>
															       ";
                                            CampoTexto('amount_due_c_internacional_usa', $amount_due_c_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                            echo"</td>
																	    </tr></table>";

                                            echo "
															      </td>
															      <td  align='right'> <table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_paid_c_internacional_usa', $amount_paid_c_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
																	    </tr></table>";
                                            echo "
																      </td>
																    </tr>
																    <tr class='css_tabla_detalle'>
																      <td align='center'>D</td>
																      <td  align='center'>";
                                            CampoTexto('no_payments_d_internacional_usa', $no_payments_d_internacional_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"');
                                            echo "
																      </td>
																      <td  align='center'>";
                                            CampoTexto('frequency_d_internacional_usa', $frequency_d_internacional_usa, 15, 10, 'form-control');
                                            echo "
															      </td>
															      <td  align='right'>";
                                            CampoTexto('interes_d_internacional_usa', $interes_d_internacional_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_usa()"');
                                            echo "
															      </td>
															      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_due_d_internacional_usa', $amount_due_d_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
																	    </tr></table>";
                                            echo "
														      </td>
														      <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_paid_d_internacional_usa', $amount_paid_d_internacional_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
																	    </tr></table>";
                                            echo "
															      </td>
															    </tr>
														      </table>";


                        ?>

                    </div>
                    <!--fin usa internatio--->
                </div>



            </div>
            <!--fin onlines--->

            <!---ini combineds-->
            <div class="tab-pane fade" id="combineds_usa1">

                <ul id="combineds_usa_1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#usa_local_combineds" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2675) ?>
                        </a>
                    </li>
                    <li>
                        <a href="#usa_internacional_combined" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>

                <div id="combineds_usa_1" class="tab-content padding-10 no-border">

                    <div class="tab-pane fade in active" id="usa_local_combineds">

                        <?php
                                                            # Payments - Payment Options

                                                            echo "
															  <table class='table table-striped' width='100%'>
																<tr class='css_tabla_encabezado'>
																  <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
                                                            CampoTexto('no_payments_a_combined_usa', $no_payments_a_combined_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"');
                                                            echo "
															  </td>
															  <td  align='center'>";
                                                            CampoTexto('frequency_a_combined_usa', $frequency_a_combined_usa, 15, 10, 'form-control');
                                                            echo "
															  </td>
															  <td  align='right'>";
                                                            CampoTexto('interes_a_combined_usa', $interes_a_combined_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"');
                                                            echo "
														  </td>
														  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_due_a_combined_usa', $amount_due_a_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                            echo"</td>
																	</tr></table>";
                                                            echo "
															  </td>
															  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td>";
                                                            CampoTexto('amount_paid_a_combined_usa', $amount_paid_a_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

															echo"</td>
													</tr></table>";
                                                            echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle'>
															  <td align='center'>B</td>
															  <td  align='center'>";
                                                            CampoTexto('no_payments_b_combined_usa', $no_payments_b_combined_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"');
                                                            echo "
															  </td>
															  <td  align='center'>";
                                                            CampoTexto('frequency_b_combined_usa', $frequency_b_combined_usa, 15, 10, 'form-control');
                                                            echo "
															  </td>
															  <td  align='right'>";
                                                            CampoTexto('interes_b_combined_usa', $interes_b_combined_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"');
                                                            echo "
															  </td>
															  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_due_b_combined_usa', $amount_due_b_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                                            echo"</td>
																	</tr></table>";

                                                            echo "
															  </td>
															  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_paid_b_combined_usa', $amount_paid_b_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                            echo"</td>
																	</tr></table>";
                                                            echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle_bg'>
															  <td align='center'>C</td>
															  <td  align='center'>";
                                                            CampoTexto('no_payments_c_combined_usa', $no_payments_c_combined_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"');
                                                            echo "
															  </td>
															  <td  align='center'>";
                                                            CampoTexto('frequency_c_combined_usa', $frequency_c_combined_usa, 15, 10, 'form-control');
                                                            echo "
															  </td>
															  <td  align='right'>";
                                                            CampoTexto('interes_c_combined_usa', $interes_c_combined_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"');
                                                            echo "
															  </td>
															  <td  align='right'>
																	<table><tr><td>USD $ &nbsp;</td>
																			   <td>
															   ";
                                                            CampoTexto('amount_due_c_combined_usa', $amount_due_c_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                                            echo"</td>
																	</tr></table>";

                                                            echo "
															  </td>
															  <td  align='right'> <table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_paid_c_combined_usa', $amount_paid_c_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                            echo"</td>
																	</tr></table>";
                                                            echo "
																  </td>
																</tr>
																<tr class='css_tabla_detalle'>
																  <td align='center'>D</td>
																  <td  align='center'>";
                                                            CampoTexto('no_payments_d_combined_usa', $no_payments_d_combined_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"');
                                                            echo "
																  </td>
																  <td  align='center'>";
                                                            CampoTexto('frequency_d_combined_usa', $frequency_d_combined_usa, 15, 10, 'form-control');
                                                            echo "
															  </td>
															  <td  align='right'>";
                                                            CampoTexto('interes_d_combined_usa', $interes_d_combined_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_usa()"');
                                                            echo "
															  </td>
															  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_due_d_combined_usa', $amount_due_d_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                            echo"</td>
																	</tr></table>";
                                                            echo "
														  </td>
														  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_paid_d_combined_usa', $amount_paid_d_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                            echo"</td>
																	</tr></table>";
                                                            echo "
															  </td>
															</tr>
														  </table>";


                        ?>
                    </div>
                    <div class="tab-pane " id="usa_internacional_combined">

                        <?php
                                                        # Payments - Payment Options

														echo "
															  <table class='table table-striped' width='100%'>
																<tr class='css_tabla_encabezado'>
																  <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
														CampoTexto('no_payments_a_internacional_combined_usa', $no_payments_a_internacional_combined_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_a_internacional_combined_usa', $frequency_a_internacional_combined_usa, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_a_internacional_combined_usa', $interes_a_internacional_combined_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"');
														echo "
														  </td>
														  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_a_internacional_combined_usa', $amount_due_a_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td>";
                                                        CampoTexto('amount_paid_a_internacional_combined_usa', $amount_paid_a_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                        echo"</td>
													</tr></table>";
														echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle'>
															  <td align='center'>B</td>
															  <td  align='center'>";
														CampoTexto('no_payments_b_internacional_combined_usa', $no_payments_b_internacional_combined_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_b_internacional_combined_usa', $frequency_b_internacional_combined_usa, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_b_internacional_combined_usa', $interes_b_internacional_combined_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"');
														echo "
															  </td>
															  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_b_internacional_combined_usa', $amount_due_b_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														echo"</td>
																	</tr></table>";

														echo "
															  </td>
															  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_b_internacional_combined_usa', $amount_paid_b_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle_bg'>
															  <td align='center'>C</td>
															  <td  align='center'>";
														CampoTexto('no_payments_c_internacional_combined_usa', $no_payments_c_internacional_combined_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_c_internacional_combined_usa', $frequency_c_internacional_combined_usa, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_c_internacional_combined_usa', $interes_c_internacional_combined_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"');
														echo "
															  </td>
															  <td  align='right'>
																	<table><tr><td>USD $ &nbsp;</td>
																			   <td>
															   ";
														CampoTexto('amount_due_c_internacional_combined_usa', $amount_due_c_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														echo"</td>
																	</tr></table>";

														echo "
															  </td>
															  <td  align='right'> <table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_c_internacional_combined_usa', $amount_paid_c_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
																  </td>
																</tr>
																<tr class='css_tabla_detalle'>
																  <td align='center'>D</td>
																  <td  align='center'>";
														CampoTexto('no_payments_d_internacional_combined_usa', $no_payments_d_internacional_combined_usa, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"');
														echo "
																  </td>
																  <td  align='center'>";
														CampoTexto('frequency_d_internacional_combined_usa', $frequency_d_internacional_combined_usa, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_d_internacional_combined_usa', $interes_d_internacional_combined_usa, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_usa()"');
														echo "
															  </td>
															  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_d_internacional_combined_usa', $amount_due_d_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
														  </td>
														  <td  align='right'><table><tr><td>USD $ &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_d_internacional_combined_usa', $amount_paid_d_internacional_combined_usa, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															</tr>
														  </table>";


                        ?>



                    </div>

                </div>


            </div>
            <!---fin combineds--->
        </div>


    </div>
    <!-- fin usa--->

    <?php

	#get all countries add fees
    $rspb = EjecutaQuery($Queryp);
    for ($b = 0; $rowpb = RecuperaRegistro($rspb); $b++) {
        $fl_pais = $rowpb['fl_pais'];




        $no_payments_a = $rowpb['no_a_payments'];
        $no_payments_b = $rowpb['no_b_payments'];
        $no_payments_c = $rowpb['no_c_payments'];
        $no_payments_d = $rowpb['no_d_payments'];

        $frequency_a = $rowpb['ds_a_freq'];
        $frequency_b = $rowpb['ds_b_freq'];
        $frequency_c = $rowpb['ds_c_freq'];
        $frequency_d = $rowpb['ds_d_freq'];

        $amount_due_a = $rowpb['mn_a_due'];
        $amount_due_b = $rowpb['mn_b_due'];
        $amount_due_c = $rowpb['mn_c_due'];
        $amount_due_d = $rowpb['mn_d_due'];

        $amount_paid_a = $rowpb['mn_a_paid'];
        $amount_paid_b = $rowpb['mn_b_paid'];
        $amount_paid_c = $rowpb['mn_c_paid'];
        $amount_paid_d = $rowpb['mn_d_paid'];

        $interes_a = $rowpb['no_a_interes'];
        $interes_b = $rowpb['no_b_interes'];
        $interes_c = $rowpb['no_c_interes'];
        $interes_d = $rowpb['no_d_interes'];

        //	$mn_tuition_internacional = $rowpb['mn_tuition_internacional'];
        //    $mn_app_fee_internacional = $rowpb['mn_app_fee_internacional'];
        //    $mn_costs_internacional = $rowpb['mn_costs_internacional'];
        //    $ds_costs_internacional = $rowpb['ds_costs_internacional'];

        $no_payments_a_internacional = $rowpb['no_a_payments_internacional'];
        $no_payments_b_internacional = $rowpb['no_b_payments_internacional'];
        $no_payments_c_internacional = $rowpb['no_c_payments_internacional'];
        $no_payments_d_internacional = $rowpb['no_d_payments_internacional'];

        $frequency_a_internacional = $rowpb['ds_a_freq_internacional'];
        $frequency_b_internacional = $rowpb['ds_b_freq_internacional'];
        $frequency_c_internacional = $rowpb['ds_c_freq_internacional'];
        $frequency_d_internacional = $rowpb['ds_d_freq_internacional'];

        $amount_due_a_internacional = $rowpb['mn_a_due_internacional'];
        $amount_due_b_internacional = $rowpb['mn_b_due_internacional'];
        $amount_due_c_internacional = $rowpb['mn_c_due_internacional'];
        $amount_due_d_internacional = $rowpb['mn_d_due_internacional'];

        $amount_paid_a_internacional = $rowpb['mn_a_paid_internacional'];
        $amount_paid_b_internacional = $rowpb['mn_b_paid_internacional'];
        $amount_paid_c_internacional = $rowpb['mn_c_paid_internacional'];
        $amount_paid_d_internacional = $rowpb['mn_d_paid_internacional'];

        $interes_a_internacional = $rowpb['no_a_interes_internacional'];
        $interes_b_internacional = $rowpb['no_b_interes_internacional'];
        $interes_c_internacional = $rowpb['no_c_interes_internacional'];
        $interes_d_internacional = $rowpb['no_d_interes_internacional'];





        $no_payments_a_combined = $rowpb['no_a_payments_combined'];
        $no_payments_b_combined = $rowpb['no_b_payments_combined'];
        $no_payments_c_combined = $rowpb['no_c_payments_combined'];
        $no_payments_d_combined = $rowpb['no_d_payments_combined'];

        $frequency_a_combined = $rowpb['ds_a_freq_combined'];
        $frequency_b_combined = $rowpb['ds_b_freq_combined'];
        $frequency_c_combined = $rowpb['ds_c_freq_combined'];
        $frequency_d_combined = $rowpb['ds_d_freq_combined'];

        $amount_due_a_combined = $rowpb['mn_a_due_combined'];
        $amount_due_b_combined = $rowpb['mn_b_due_combined'];
        $amount_due_c_combined = $rowpb['mn_c_due_combined'];
        $amount_due_d_combined = $rowpb['mn_d_due_combined'];

        $amount_paid_a_combined = $rowpb['mn_a_paid_combined'];
        $amount_paid_b_combined = $rowpb['mn_b_paid_combined'];
        $amount_paid_c_combined = $rowpb['mn_c_paid_combined'];
        $amount_paid_d_combined = $rowpb['mn_d_paid_combined'];

        $interes_a_combined = $rowpb['no_a_interes_combined'];
        $interes_b_combined = $rowpb['no_b_interes_combined'];
        $interes_c_combined = $rowpb['no_c_interes_combined'];
        $interes_d_combined = $rowpb['no_d_interes_combined'];





        $no_payments_a_internacional_combined = $rowpb['no_a_payments_internacional_combined'];
        $no_payments_b_internacional_combined = $rowpb['no_b_payments_internacional_combined'];
        $no_payments_c_internacional_combined = $rowpb['no_c_payments_internacional_combined'];
        $no_payments_d_internacional_combined = $rowpb['no_d_payments_internacional_combined'];

        $frequency_a_internacional_combined = $rowpb['ds_a_freq_internacional_combined'];
        $frequency_b_internacional_combined = $rowpb['ds_b_freq_internacional_combined'];
        $frequency_c_internacional_combined = $rowpb['ds_c_freq_internacional_combined'];
        $frequency_d_internacional_combined = $rowpb['ds_d_freq_internacional_combined'];

        $amount_due_a_internacional_combined = $rowpb['mn_a_due_internacional_combined'];
        $amount_due_b_internacional_combined = $rowpb['mn_b_due_internacional_combined'];
        $amount_due_c_internacional_combined = $rowpb['mn_c_due_internacional_combined'];
        $amount_due_d_internacional_combined = $rowpb['mn_d_due_internacional_combined'];

        $amount_paid_a_internacional_combined = $rowpb['mn_a_paid_internacional_combined'];
        $amount_paid_b_internacional_combined = $rowpb['mn_b_paid_internacional_combined'];
        $amount_paid_c_internacional_combined = $rowpb['mn_c_paid_internacional_combined'];
        $amount_paid_d_internacional_combined = $rowpb['mn_d_paid_internacional_combined'];

        $interes_a_internacional_combined = $rowpb['no_a_interes_internacional_combined'];
        $interes_b_internacional_combined = $rowpb['no_b_interes_internacional_combined'];
        $interes_c_internacional_combined = $rowpb['no_c_interes_internacional_combined'];
        $interes_d_internacional_combined = $rowpb['no_d_interes_internacional_combined'];




        $Querypa = "SELECT cl_iso2,ds_pais,currency_code,symbol FROM c_pais WHERE fl_pais=$fl_pais ";
        $rowpa = RecuperaValor($Querypa);
        $cl_iso2=$rowpa['cl_iso2'];
        $ds_pais=$rowpa['ds_pais'];
        $currency_code = $rowpa['currency_code'];
        $symbol = $rowpa['symbol'];
    ?>

    <div id="<?php echo $cl_iso2;?>1" class="tab-pane">

        <ul id="tab_<?php echo $cl_iso2;?>" class="nav nav-tabs bordered">
            <li class="active">
                <a href="#onlines_<?php echo $cl_iso2;?>1" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-navicon"></i>Online Live Clases
                </a>
            </li>
            <li>
                <a href="#combineds_<?php echo $cl_iso2; ?>1" data-toggle="tab">
                    <i class="fa fa-fw fa-lg fa-compress"></i>Combined (Online + Onsite <?php echo $ds_pais;?>)
                </a>
            </li>
        </ul>
        
        <div id="tab_<?php echo $cl_iso2; ?>" class="tab-content padding-10 no-border">
            
			<!--onlines-->
			<div class="tab-pane fade in active" id="onlines_<?php echo $cl_iso2;?>1">
                <ul id="tabs_<?php echo $cl_iso2; ?>_online1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#local_<?php echo $cl_iso2; ?>_online" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo $ds_pais;?> Students
                        </a>
                    </li>
                    <li>
                        <a href="#internacional_<?php echo $cl_iso2; ?>_online" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>

                <div id="tabs_<?php echo $cl_iso2; ?>_online1" class="tab-content padding-10 no-border">
                    
					<div class="tab-pane in active" id="local_<?php echo $cl_iso2; ?>_online">

                        <?php
                                             # Payments - Payment Options

                                             echo "
															      <table class='table table-striped' width='100%'>
																    <tr class='css_tabla_encabezado'>
																      <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
                                             CampoTexto('no_payments_a_'. $cl_iso2, $no_payments_a, 3, 3, 'form-control', False, 'style="text-align:right"    onchange=\'calcula_costo_country("' . $cl_iso2 . '") \' ');
                                             echo "
															      </td>
															      <td  align='center'>";
                                             CampoTexto('frequency_a_'. $cl_iso2, $frequency_a, 15, 10, 'form-control');
                                             echo "
															      </td>
															      <td  align='right'>";
                                             CampoTexto('interes_a_'. $cl_iso2, $interes_a, 5, 3, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_country("' . $cl_iso2 . '") \'');
                                             echo "
														      </td>
														      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_due_a_'. $cl_iso2, $amount_due_a, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
																	    </tr></table>";
                                             echo "
															      </td>
															      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td>";
                                             CampoTexto('amount_paid_a_'. $cl_iso2, $amount_paid_a, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
													    </tr></table>";
                                             echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle'>
															      <td align='center'>B</td>
															      <td  align='center'>";
                                             CampoTexto('no_payments_b_'. $cl_iso2, $no_payments_b, 3, 3, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_country("' . $cl_iso2 . '") \'');
                                             echo "
															      </td>
															      <td  align='center'>";
                                             CampoTexto('frequency_b_'. $cl_iso2, $frequency_b, 15, 10, 'form-control');
                                             echo "
															      </td>
															      <td  align='right'>";
                                             CampoTexto('interes_b_'. $cl_iso2, $interes_b, 5, 3, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_country("' . $cl_iso2 . '") \'');
                                             echo "
															      </td>
															      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_due_b_'. $cl_iso2, $amount_due_b, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                             echo"</td>
																	    </tr></table>";

                                             echo "
															      </td>
															      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_paid_b_'. $cl_iso2, $amount_paid_b, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
																	    </tr></table>";
                                             echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle_bg'>
															      <td align='center'>C</td>
															      <td  align='center'>";
                                             CampoTexto('no_payments_c_'. $cl_iso2, $no_payments_c, 3, 3, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_country("' . $cl_iso2 . '") \'');
                                             echo "
															      </td>
															      <td  align='center'>";
                                             CampoTexto('frequency_c_'. $cl_iso2, $frequency_c, 15, 10, 'form-control');
                                             echo "
															      </td>
															      <td  align='right'>";
                                             CampoTexto('interes_c_'. $cl_iso2, $interes_c, 5, 3, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_country("' . $cl_iso2 . '") \'');
                                             echo "
															      </td>
															      <td  align='right'>
																	    <table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td>
															       ";
                                             CampoTexto('amount_due_c_'. $cl_iso2, $amount_due_c, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                             echo"</td>
																	    </tr></table>";

                                             echo "
															      </td>
															      <td  align='right'> <table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_paid_c_'. $cl_iso2, $amount_paid_c, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
																	    </tr></table>";
                                             echo "
																      </td>
																    </tr>
																    <tr class='css_tabla_detalle'>
																      <td align='center'>D</td>
																      <td  align='center'>";
                                             CampoTexto('no_payments_d_'. $cl_iso2, $no_payments_d, 3, 3, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_country("' . $cl_iso2 . '") \'');
                                             echo "
																      </td>
																      <td  align='center'>";
                                             CampoTexto('frequency_d_'. $cl_iso2, $frequency_d, 15, 10, 'form-control');
                                             echo "
															      </td>
															      <td  align='right'>";
                                             CampoTexto('interes_d_'. $cl_iso2, $interes_d, 5, 3, 'form-control', False, 'style="text-align:right" onchange=\'calcula_costo_country("' . $cl_iso2 . '") \'');
                                             echo "
															      </td>
															      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_due_d_'. $cl_iso2, $amount_due_d, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
																	    </tr></table>";
                                             echo "
														      </td>
														      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                             CampoTexto('amount_paid_d_'. $cl_iso2, $amount_paid_d, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                             echo"</td>
																	    </tr></table>";
                                             echo "
															      </td>
															    </tr>
														      </table>";


                        ?>
					</div>
					
					
                    <div class="tab-pane " id="internacional_<?php echo $cl_iso2; ?>_online">
                        <?php
                                            # Payments - Payment Options

                                            echo "
															      <table class='table table-striped' width='100%'>
																    <tr class='css_tabla_encabezado'>
																      <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
                                            CampoTexto('no_payments_a_internacional_'. $cl_iso2, $no_payments_a_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_country(\"' . $cl_iso2 . '\")"');
                                            echo "
															      </td>
															      <td  align='center'>";
                                            CampoTexto('frequency_a_internacional_'. $cl_iso2, $frequency_a_internacional, 15, 10, 'form-control');
                                            echo "
															      </td>
															      <td  align='right'>";
                                            CampoTexto('interes_a_internacional_'. $cl_iso2, $interes_a_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_country(\"' . $cl_iso2 . '\")"');
                                            echo "
														      </td>
														      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_due_a_internacional_'. $cl_iso2, $amount_due_a_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
																	    </tr></table>";
                                            echo "
															      </td>
															      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td>";
                                            CampoTexto('amount_paid_a_internacional_'. $cl_iso2, $amount_paid_a_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
													    </tr></table>";
                                            echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle'>
															      <td align='center'>B</td>
															      <td  align='center'>";
                                            CampoTexto('no_payments_b_internacional_'. $cl_iso2, $no_payments_b_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_country(\"' . $cl_iso2 . '\")"');
                                            echo "
															      </td>
															      <td  align='center'>";
                                            CampoTexto('frequency_b_internacional_'. $cl_iso2, $frequency_b_internacional, 15, 10, 'form-control');
                                            echo "
															      </td>
															      <td  align='right'>";
                                            CampoTexto('interes_b_internacional_'. $cl_iso2, $interes_b_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_country(\"' . $cl_iso2 . '\")"');
                                            echo "
															      </td>
															      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_due_b_internacional_'. $cl_iso2, $amount_due_b_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                            echo"</td>
																	    </tr></table>";

                                            echo "
															      </td>
															      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_paid_b_internacional_'. $cl_iso2, $amount_paid_b_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
																	    </tr></table>";
                                            echo "
															      </td>
															    </tr>
															    <tr class='css_tabla_detalle_bg'>
															      <td align='center'>C</td>
															      <td  align='center'>";
                                            CampoTexto('no_payments_c_internacional_'. $cl_iso2, $no_payments_c_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_country(\"' . $cl_iso2 . '\")"');
                                            echo "
															      </td>
															      <td  align='center'>";
                                            CampoTexto('frequency_c_internacional_'. $cl_iso2, $frequency_c_internacional, 15, 10, 'form-control');
                                            echo "
															      </td>
															      <td  align='right'>";
                                            CampoTexto('interes_c_internacional_'. $cl_iso2, $interes_c_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_country(\"' . $cl_iso2 . '\")"');
                                            echo "
															      </td>
															      <td  align='right'>
																	    <table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td>
															       ";
                                            CampoTexto('amount_due_c_internacional_'. $cl_iso2, $amount_due_c_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                            echo"</td>
																	    </tr></table>";

                                            echo "
															      </td>
															      <td  align='right'> <table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_paid_c_internacional_'. $cl_iso2, $amount_paid_c_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
																	    </tr></table>";
                                            echo "
																      </td>
																    </tr>
																    <tr class='css_tabla_detalle'>
																      <td align='center'>D</td>
																      <td  align='center'>";
                                            CampoTexto('no_payments_d_internacional_'. $cl_iso2, $no_payments_d_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_country(\"' . $cl_iso2 . '\")"');
                                            echo "
																      </td>
																      <td  align='center'>";
                                            CampoTexto('frequency_d_internacional_'. $cl_iso2, $frequency_d_internacional, 15, 10, 'form-control');
                                            echo "
															      </td>
															      <td  align='right'>";
                                            CampoTexto('interes_d_internacional_'. $cl_iso2, $interes_d_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_country(\"' . $cl_iso2 . '\")"');
                                            echo "
															      </td>
															      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_due_d_internacional_'. $cl_iso2, $amount_due_d_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
																	    </tr></table>";
                                            echo "
														      </td>
														      <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			       <td> ";
                                            CampoTexto('amount_paid_d_internacional_'. $cl_iso2, $amount_paid_d_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                            echo"</td>
																	    </tr></table>";
                                            echo "
															      </td>
															    </tr>
														      </table>";


                        ?>
					</div>

				</div>
			</div>

			<!---ini combineds-->
            <div class="tab-pane fade" id="combineds_<?php echo $cl_iso2;?>1">
                <ul id="combineds_<?php echo $cl_iso2; ?>_1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#<?php echo $cl_iso2; ?>_local_combineds" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo $ds_pais; ?> Students
                        </a>
                    </li>
                    <li>
                        <a href="#<?php echo $cl_iso2; ?>_internacional_combined" data-toggle="tab">
                            <i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?>
                        </a>
                    </li>
                </ul>

                <div id="combineds_<?php echo $cl_iso2;?>_1" class="tab-content padding-10 no-border">

                    <div class="tab-pane fade in active" id="<?php echo $cl_iso2;?>_local_combineds">

                        <?php
                                                            # Payments - Payment Options

                                                            echo "
															  <table class='table table-striped' width='100%'>
																<tr class='css_tabla_encabezado'>
																  <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
                                                            CampoTexto('no_payments_a_combined_'. $cl_iso2, $no_payments_a_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_country(\"' . $cl_iso2 . '\")"');
                                                            echo "
															  </td>
															  <td  align='center'>";
                                                            CampoTexto('frequency_a_combined_'. $cl_iso2, $frequency_a_combined, 15, 10, 'form-control');
                                                            echo "
															  </td>
															  <td  align='right'>";
                                                            CampoTexto('interes_a_combined_'. $cl_iso2, $interes_a_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_country(\"' . $cl_iso2 . '\")"');
                                                            echo "
														  </td>
														  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_due_a_combined_'. $cl_iso2, $amount_due_a_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                            echo"</td>
																	</tr></table>";
                                                            echo "
															  </td>
															  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td>";
                                                            CampoTexto('amount_paid_a_combined_'. $cl_iso2, $amount_paid_a_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

															echo"</td>
													</tr></table>";
                                                            echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle'>
															  <td align='center'>B</td>
															  <td  align='center'>";
                                                            CampoTexto('no_payments_b_combined_'. $cl_iso2, $no_payments_b_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_country(\"' . $cl_iso2 . '\")"');
                                                            echo "
															  </td>
															  <td  align='center'>";
                                                            CampoTexto('frequency_b_combined_'. $cl_iso2, $frequency_b_combined, 15, 10, 'form-control');
                                                            echo "
															  </td>
															  <td  align='right'>";
                                                            CampoTexto('interes_b_combined_'. $cl_iso2, $interes_b_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_country(\"' . $cl_iso2 . '\")"');
                                                            echo "
															  </td>
															  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_due_b_combined_'. $cl_iso2, $amount_due_b_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                                            echo"</td>
																	</tr></table>";

                                                            echo "
															  </td>
															  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_paid_b_combined_'. $cl_iso2, $amount_paid_b_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                            echo"</td>
																	</tr></table>";
                                                            echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle_bg'>
															  <td align='center'>C</td>
															  <td  align='center'>";
                                                            CampoTexto('no_payments_c_combined_'. $cl_iso2, $no_payments_c_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_country(\"' . $cl_iso2 . '\")"');
                                                            echo "
															  </td>
															  <td  align='center'>";
                                                            CampoTexto('frequency_c_combined_'. $cl_iso2, $frequency_c_combined, 15, 10, 'form-control');
                                                            echo "
															  </td>
															  <td  align='right'>";
                                                            CampoTexto('interes_c_combined_'. $cl_iso2, $interes_c_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_country(\"' . $cl_iso2 . '\")"');
                                                            echo "
															  </td>
															  <td  align='right'>
																	<table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td>
															   ";
                                                            CampoTexto('amount_due_c_combined_'. $cl_iso2, $amount_due_c_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                                            echo"</td>
																	</tr></table>";

                                                            echo "
															  </td>
															  <td  align='right'> <table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_paid_c_combined_'. $cl_iso2, $amount_paid_c_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                            echo"</td>
																	</tr></table>";
                                                            echo "
																  </td>
																</tr>
																<tr class='css_tabla_detalle'>
																  <td align='center'>D</td>
																  <td  align='center'>";
                                                            CampoTexto('no_payments_d_combined_'. $cl_iso2, $no_payments_d_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_country(\"' . $cl_iso2 . '\")"');
                                                            echo "
																  </td>
																  <td  align='center'>";
                                                            CampoTexto('frequency_d_combined_'. $cl_iso2, $frequency_d_combined, 15, 10, 'form-control');
                                                            echo "
															  </td>
															  <td  align='right'>";
                                                            CampoTexto('interes_d_combined_'. $cl_iso2, $interes_d_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_combined_country(\"' . $cl_iso2 . '\")"');
                                                            echo "
															  </td>
															  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_due_d_combined_'. $cl_iso2, $amount_due_d_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                            echo"</td>
																	</tr></table>";
                                                            echo "
														  </td>
														  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
                                                            CampoTexto('amount_paid_d_combined_'. $cl_iso2, $amount_paid_d_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                            echo"</td>
																	</tr></table>";
                                                            echo "
															  </td>
															</tr>
														  </table>";


                        ?>
					</div>

                    <div class="tab-pane " id="<?php echo $cl_iso2;?>_internacional_combined">
                        <?php
                                                        # Payments - Payment Options

														echo "
															  <table class='table table-striped' width='100%'>
																<tr class='css_tabla_encabezado'>
																  <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
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
														CampoTexto('no_payments_a_internacional_combined_'. $cl_iso2, $no_payments_a_internacional_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_country(\"' . $cl_iso2 . '\")"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_a_internacional_combined_'. $cl_iso2, $frequency_a_internacional_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_a_internacional_combined_'. $cl_iso2, $interes_a_internacional_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_country(\"' . $cl_iso2 . '\")"');
														echo "
														  </td>
														  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_a_internacional_combined_'. $cl_iso2, $amount_due_a_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td>";
                                                        CampoTexto('amount_paid_a_internacional_combined_'. $cl_iso2, $amount_paid_a_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

                                                        echo"</td>
													</tr></table>";
														echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle'>
															  <td align='center'>B</td>
															  <td  align='center'>";
														CampoTexto('no_payments_b_internacional_combined_'. $cl_iso2, $no_payments_b_internacional_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_country(\"' . $cl_iso2 . '\")"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_b_internacional_combined_'. $cl_iso2, $frequency_b_internacional_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_b_internacional_combined_'. $cl_iso2, $interes_b_internacional_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_country(\"' . $cl_iso2 . '\")"');
														echo "
															  </td>
															  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_b_internacional_combined_'. $cl_iso2, $amount_due_b_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														echo"</td>
																	</tr></table>";

														echo "
															  </td>
															  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_b_internacional_combined_'. $cl_iso2, $amount_paid_b_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															</tr>
															<tr class='css_tabla_detalle_bg'>
															  <td align='center'>C</td>
															  <td  align='center'>";
														CampoTexto('no_payments_c_internacional_combined_'. $cl_iso2, $no_payments_c_internacional_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_country(\"' . $cl_iso2 . '\")"');
														echo "
															  </td>
															  <td  align='center'>";
														CampoTexto('frequency_c_internacional_combined_'. $cl_iso2, $frequency_c_internacional_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_c_internacional_combined_'. $cl_iso2, $interes_c_internacional_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_country(\"' . $cl_iso2 . '\")"');
														echo "
															  </td>
															  <td  align='right'>
																	<table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td>
															   ";
														CampoTexto('amount_due_c_internacional_combined_'. $cl_iso2, $amount_due_c_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
														echo"</td>
																	</tr></table>";

														echo "
															  </td>
															  <td  align='right'> <table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_c_internacional_combined_'. $cl_iso2, $amount_paid_c_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
																  </td>
																</tr>
																<tr class='css_tabla_detalle'>
																  <td align='center'>D</td>
																  <td  align='center'>";
														CampoTexto('no_payments_d_internacional_combined_'. $cl_iso2, $no_payments_d_internacional_combined, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_country(\"' . $cl_iso2 . '\")"');
														echo "
																  </td>
																  <td  align='center'>";
														CampoTexto('frequency_d_internacional_combined_'. $cl_iso2, $frequency_d_internacional_combined, 15, 10, 'form-control');
														echo "
															  </td>
															  <td  align='right'>";
														CampoTexto('interes_d_internacional_combined_'. $cl_iso2, $interes_d_internacional_combined, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional_combined_country(\"'.$cl_iso2.'\")"');
														echo "
															  </td>
															  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_due_d_internacional_combined_'. $cl_iso2, $amount_due_d_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
														  </td>
														  <td  align='right'><table><tr><td>$currency_code $symbol &nbsp;</td>
																			   <td> ";
														CampoTexto('amount_paid_d_internacional_combined_'. $cl_iso2, $amount_paid_d_internacional_combined, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');

														echo"</td>
																	</tr></table>";
														echo "
															  </td>
															</tr>
														  </table>";


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
<script>
    	 setTimeout(function () {
                        console.log("Hola Mundo tuition");
    calcula_costo();
    calcula_costo_internacional();

    calcula_costo_usa();
    calcula_costo_internacional_usa();

    calcula_costo_combined();
    calcula_costo_internacional_combined();

    calcula_costo_combined_usa();
    calcula_costo_internacional_combined_usa();
   }, 4000);

	///different countryes
	//calcula_costo_country();
	//calcula_costo_internacional_country();
	//calcula_costo_combined_country();
	//calcula_costo_internacional_combined_country();

</script>


<?php


#get db searh countryes.
  $Queryc = "SELECT fl_pais FROM k_programa_costos_pais ";
  $Queryc .= "WHERE fl_programa = $clave AND fl_pais <> 226 ";
  $rsp = EjecutaQuery($Queryc);
  for ($z = 0; $rowp = RecuperaRegistro($rsp); $z++) {
      $fl_pais = $rowp['fl_pais'];




      $Querypa = "SELECT cl_iso2,ds_pais FROM c_pais WHERE fl_pais=$fl_pais ";
      $rowpa = RecuperaValor($Querypa);
      $cl_iso2 = $rowpa['cl_iso2'];
      $ds_pais = $rowpa['ds_pais'];

      echo "<script>

			console.log('Hola Mundo tuition $cl_iso2');
            calcula_costo_country('$cl_iso2');
            calcula_costo_internacional_country('$cl_iso2');
            calcula_costo_combined_country('$cl_iso2');
            calcula_costo_internacional_combined_country('$cl_iso2');

</script>";


  }

?>

