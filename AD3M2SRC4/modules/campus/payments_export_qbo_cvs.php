<?php

require_once '../../lib/PHPExcel1.8/PHPExcel/IOFactory.php';
require_once '../../lib/PHPExcel1.8/PHPExcel.php';

//usando phpSpeedsheet.
require '../../lib/PHPspeed/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

# Funcion para quitar caracteres especiales y saltos de linea
function getStrParaCSV($str) {
	$str_aux = $str;
	$str_aux = str_replace(",", " ", $str_aux);
	$str_aux = str_replace("\n", " ", $str_aux);
	$str_aux = str_replace("\r", " ", $str_aux);

	return $str_aux;
}



//$spreadsheet = IOFactory::load('ID-03642-Vancouver_Animation_School_2020-10-28-Current_correct.xlsx');
$spreadsheet = new Spreadsheet();


$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1','InvoiceNo')
    ->setCellValue('B1','Customer')
    ->setCellValue('C1','InvoiceDate')
    ->setCellValue('D1','DueDate')
    ->setCellValue('E1','Terms')
    ->setCellValue('F1','Location')
    ->setCellValue('G1','Memo')
    ->setCellValue('H1','Product Service')
    ->setCellValue('I1','ItemDescription')
    ->setCellValue('J1','ItemQuantity')
    ->setCellValue('K1','ItemRate')
    ->setCellValue('L1','ItemAmount')
    ->setCellValue('M1','ItemTaxCode')
    ->setCellValue('N1','ItemTaxAmount')
    ->setCellValue('O1','Currency');

$count=1;
$name_ingresado="";
$invoice_id=1000;
//$Query3 .= " LIMIT 1 OFFSET 3";
$rsi = EjecutaQuery($Query3);
for($i=1;$row=RecuperaRegistro($rsi);$i++) {

    $fl_usuario=str_ireplace("'","",$row[0]);
    $ids=explode('-',$fl_usuario);
    $character=$ids[0];
    $fl_sesion=$ids[1];





        $course=$row[1];
        $name=$row[2];
        $name_payments = $row[2];
        $name_fame_resources = $row[2];
        $term=$row[3];
        $payment_number=$row[4];
        $frequency=$row[5];
        $payment_due=$row[6];
        $payment_amount=$row[7];
        $payment_date=$row[8];
        $payment_method=$row[9];
        $country=$row[10];
        $earned=$row[11];
        $unearded=$row[12];
        $eu=$row[13];
        $state=$row[14];

        #INITIALIZE VARIABLES

        $item_product_service="";
        $item_description="";
        $itemQuantity="";
        $terms="";
        $location="";
        $memo="";
        $itemRate="";
        $fe_pago = $payment_date;
        $ds_tax_provincia="";
        $mn_tax="";
        $currency="";
        $mn_pagado="";


        #get cl_Sesion contiene aplicaciones
        if($fl_sesion){
           $QuerySesion="SELECT cl_sesion FROM c_sesion where fl_sesion=$fl_sesion ";
        }
        if(empty($fl_sesion))
        {
            #solo son estudiante
            $QuerySesion="SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$fl_usuario ";
        }
        $rowsesion=RecuperaValor($QuerySesion);
        $cl_sesion=$rowsesion[0];

        #get  campus
        $QueryCampus="SELECT fl_pais_campus FROM c_sesion where cl_sesion='$cl_sesion' ";
        $rowc = RecuperaValor($QueryCampus);
        $fl_pais_campus = $rowc['fl_pais_campus'];


        $Queryin="SELECT ds_fname,ds_lname,ds_add_country,ds_add_state,fl_periodo FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion' ";
        $roin=RecuperaValor($Queryin);
        $ds_fname=$roin['ds_fname'];
        $ds_lname=$roin['ds_lname'];
        $ds_login = substr(strtolower($ds_lname), 0, 1) . substr(strtolower($ds_fname), 0, 1)."_";
        $fl_country = $roin['ds_add_country'];
        $fl_provincia = $roin['ds_add_state'];
        $fl_periodo = $roin['fl_periodo'];

        $ds_tax_provincia = "";

        #get tax
        if ($fl_country == 38 || $fl_country == 226) {

            $Qurry = "SELECT mn_tax,ds_type FROM k_provincias where fl_provincia=$fl_provincia ";
            $rowpro = RecuperaValor($Qurry);
            $percentage_tax = $rowpro['mn_tax'];
            $ds_tax_provincia = $rowpro['ds_type'];

            $mn_tax = ($payment_amount * $percentage_tax) / 100;

        }
        switch ($fl_pais_campus) {

            case '38':
                $currency = ObtenConfiguracion(82);
                break;
            case '226':
                $currency = "USD";

                break;
            case '199':
                $currency = "EUR";

                break;
            case '73':
                $currency = "EUR";

                break;
            case '80':
                $currency = "EUR";

                break;
            case '105':
                $currency = "EUR";

                break;
            case '225':
                $currency = "GBP";

                break;
            case '153':
                $currency = "EUR";

                break;
            default:
                $currency = "CAD";
                break;

        }

        if ($name <> $name_ingresado) {


        }else {

            $name = "";
        }




            #identificamos que tipo de pago corresponde si es APP FEE
            if ($term == "(App fee form)" && $payment_number = "(App fee form)" && $frequency = "(App fee form)") {


                $item_product_service = "App fee";
                $item_description = "App fee";
                $itemQuantity = "1";

                $invoice_id++;
                $count++;

                $no_invoice = $invoice_id;

                    $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $count . '', '' . $ds_login . $no_invoice . '') //invoice id
                    ->setCellValue('B' . $count . '', '' . $name . '') //customer
                    ->setCellValue('C' . $count . '', '' . $fe_pago . '') //InvoiceDate
                    ->setCellValue('D' . $count . '', '' . $fe_pago . '') //Due paymetn
                    ->setCellValue('E' . $count . '', '') //terms
                    ->setCellValue('F' . $count . '', '') //location
                    ->setCellValue('G' . $count . '', '') //memo
                    ->setCellValue('H' . $count . '', '' . $item_product_service . '') //item producto servicio
                    ->setCellValue('I' . $count . '', '' . $item_description . '') //item descripticon
                    ->setCellValue('J' . $count . '', '' . $itemQuantity . '') //ItemQuantity
                    ->setCellValue('K' . $count . '', '' . $itemRate . '') //ItemRate
                    ->setCellValue('L' . $count . '', '' . $payment_amount . '') //ItemAmount
                    ->setCellValue('M' . $count . '', '' . $ds_tax_provincia . '') //ItemTaxCode
                    ->setCellValue('N' . $count . '', '' . $mn_tax . '') //ItemTaxAmount
                    ->setCellValue('O' . $count . '', '' . $currency . ''); //Currency




            #son full payment son pagos de colegiaturas
            } else {


                #identificamos el tipo de pago.
                if ($frequency == 'Full Payment' || $frequency=='Semi Annual' || $frequency=='Trimesterly')
                {

                    //by default si existe learnig resourse FAME
                    #recuperamos si existe el aditional cost.
                    $Queryad = "SELECT mn_costs FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
                    $rowad = RecuperaValor($Queryad);
                    $mn_cost = $rowad['mn_costs'];

                    $fg_mismo_id = 0;

                    if ($mn_cost > 0) {

                        $invoice_id++;
                        $count++;

                        $no_invoice = $invoice_id;

                        $item_product_service = "Additional Costs";
                        $item_description = "VANAS+ Learning Resources";

                        $fg_mismo_id = 1;

                        if ($fl_country <> 38) {
                            $ds_tax_provincia = "Exempt";
                        }


                        $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('A' . $count . '', '' . $ds_login . $no_invoice . '') //invoice id
                            ->setCellValue('B' . $count . '', '' . $name_fame_resources . '') //customer
                            ->setCellValue('C' . $count . '', '' . $fe_pago . '') //InvoiceDate
                            ->setCellValue('D' . $count . '', '' . $fe_pago . '') //Due paymetn
                            ->setCellValue('E' . $count . '', '') //terms
                            ->setCellValue('F' . $count . '', '') //location
                            ->setCellValue('G' . $count . '', '') //memo
                            ->setCellValue('H' . $count . '', '' . $item_product_service . '') //item producto servicio
                            ->setCellValue('I' . $count . '', '' . $item_description . '') //item descripticon
                            ->setCellValue('J' . $count . '', '1') //ItemQuantity
                            ->setCellValue('K' . $count . '', '') //ItemRate
                            ->setCellValue('L' . $count . '', ''.$mn_cost.'') //ItemAmount
                            ->setCellValue('M' . $count . '', '' . $ds_tax_provincia . '') //ItemTaxCode
                            ->setCellValue('N' . $count . '', '') //ItemTaxAmount
                            ->setCellValue('O' . $count . '', '' . $currency . ''); //Currency

                    }








                    # Recupera el tipo de pago para el curso
                    $Queryp = "SELECT fg_opcion_pago, ds_firma_alumno, fe_firma FROM k_app_contrato WHERE cl_sesion='$cl_sesion'";
                    $rowp = RecuperaValor($Queryp);
                    $fg_opcion_pago = $rowp[0];

                    # Recupera informacion de los pagos
                    switch ($fg_opcion_pago) {
                        case 1:
                            $mn_due = 'mn_a_due';
                            $no_x_payments = 'no_a_payments';
                            $mn_paid = 'mn_a_paid';
                            $no_invoices = 1;
                            break;
                        case 2:
                            $mn_due = 'mn_b_due';
                            $no_x_payments = 'no_b_payments';
                            $mn_paid = 'mn_b_paid';
                            $no_invoices = 2;
                            break;
                        case 3:
                            $mn_due = 'mn_c_due';
                            $no_x_payments = 'no_c_payments';
                            $mn_paid = 'mn_c_paid';
                            $no_invoices = 4;
                            break;
                        case 4:
                            $mn_due = 'mn_d_due';
                            $no_x_payments = 'no_d_payments';
                            $mn_paid = 'mn_d_paid';
                            $no_invoices = 4;
                            break;
                    }


                    # Query para pagos realizados
                    if (empty($fl_sesion)) {
                        $Query_pagado = "SELECT  a.fl_term_pago, b.no_opcion, b.no_pago, DATE_FORMAT(b.fe_pago,'%d/%m/%Y'),(SELECT $mn_due FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1'), ";
                        $Query_pagado .= " a.mn_pagado, a.ds_comentario, a.fl_alumno_pago, a.cl_metodo_pago,";
                        $Query_pagado .= "mn_earned earned, mn_unearned unearned, ds_eu e_u,
                                    (SELECT nb_periodo FROM c_periodo r, k_term t WHERE r.fl_periodo=t.fl_periodo AND t.fl_term=b.fl_term) terms,a.fl_alumno_pago,b.fl_term_pago ";
                        $Query_pagado .= "FROM k_alumno_pago a, k_term_pago b ";
                        $Query_pagado .= "WHERE a.fl_term_pago = b.fl_term_pago AND a.fl_alumno=$fl_usuario ORDER BY b.fe_pago ";
                    } else {
                        $Query_pagado = "SELECT  a.fl_term_pago, b.no_opcion, b.no_pago, DATE_FORMAT(b.fe_pago,'%d/%m/%Y'),(SELECT $mn_due FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1'), ";
                        $Query_pagado .= " a.mn_pagado, a.ds_comentario, a.fl_ses_pago, a.cl_metodo_pago, ";
                        $Query_pagado .= "'' earned, '' unearned, '' e_u, (SELECT nb_periodo FROM c_periodo r, k_term t WHERE r.fl_periodo=t.fl_periodo AND t.fl_term=b.fl_term) terms,b.fl_term_pago ";
                        $Query_pagado .= "FROM k_ses_pago a, k_term_pago b ";
                        $Query_pagado .= "WHERE a.fl_term_pago = b.fl_term_pago AND  cl_sesion='$cl_sesion' ORDER BY b.fe_pago ";
                    }
                     $Query_pagado;

                    $rs2 = EjecutaQuery($Query_pagado);
                    for ($a = 0; $row2 = RecuperaRegistro($rs2); $a++) {

                        $no_pago = $row2[2];

                        $QuerySemanas = "SELECT b.no_semanas,c.nb_programa,a.ds_add_country,a.ds_add_state,a.fl_periodo  FROM k_ses_app_frm_1 a JOIN k_programa_costos b ON b.fl_programa=a.fl_programa JOIN c_programa c ON c.fl_programa=a.fl_programa WHERE cl_sesion='$cl_sesion' ";
                        $no_se = RecuperaValor($QuerySemanas);
                        $no_semanas = $no_se[0];
                        $nb_programa = $no_se[1];
                        $ds_add_country = $no_se[2];
                        $ds_add_state = $no_se[3];
                        $fl_term_pago = $no_se['fl_term_pago'];
                        $fl_periodo = $no_se['fl_periodo'];


                        $QueryPeri = "SELECT DATE_FORMAT(fe_inicio,'%Y/%m/%d') from c_periodo WHERE fl_periodo=$fl_periodo ";
                        $rowperi = RecuperaValor($QueryPeri);
                        $fe_inicio_pro = $rowperi[0];

                        #obtenemos la fecha del term_ini si el grado es mayor a 1
                        $row1 = RecuperaValor("SELECT a.fl_term, no_grado, fe_pago FROM k_term_pago a, k_term b WHERE a.fl_term=b.fl_term AND fl_term_pago=$fl_term_pago");
                        $fl_term_ini = $row1[0];
                        $fe_term_pago = $row1[2];

                        $meses_duracion = $no_semanas / 4; //meses de duracion del programa

                        #obtenemos los precios de cada uno de los contratos de los alumnos
                        $Query5 = "SELECT mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_discount, ds_discount, mn_tot_tuition, mn_tot_program,$mn_due, $mn_paid ";
                        $Query5 .= "FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato=1";
                        $row5 = RecuperaValor($Query5);
                        $mn_app_fee = $row5[0];
                        $mn_tuition = $row5[1];
                        $mn_costs = $row5[2];
                        $ds_costs = $row5[3];
                        $mn_discount = $row5[4];
                        $ds_discount = $row5[5];
                        $mn_tot_tuition = $row5[6];
                        $mn_tot_program = $row5[7];
                        $mn_x_due = $row5[8];
                        $mn_x_paid = $row5[9];

                        #numero de pagos, meses que cubre un pago
                        $numero_pagos = $mn_x_paid / $mn_x_due;

                        $no_meses_op = $meses_duracion / $numero_pagos; //numero de meses por opcion

                        $desfase = ($no_pago - 1) * $no_meses_op;
                        $nuevafecha = strtotime("+ " . $desfase . " month", strtotime($fe_inicio_pro));
                        $fe_mesini_pago = date('d-m-Y', $nuevafecha);

                        $pago_normal_x_mes = round($mn_x_due / $no_meses_op, 2);

                        if($fg_mismo_id == 1)
                        {
                            $pago_normal_x_mes= round(($mn_x_due - $mn_cost) / $no_meses_op,2) ;
                        }

                        //$pago_normal_x_mes = $mn_tuition / $no_invoices;

                        $contador2=0;


                        for ($b = 0; $b <= $no_meses_op - 1; $b++) {


                            if ($name_payments <> $customer_name) {

                                if ($fg_mismo_id == 1) {//quiere decir que si tiene aditional cost

                                } else {
                                    $invoice_id++;
                                }



                            } else {
                                $name_payments = "";


                            }
                            $mes_ini_pago = strtotime("+ " . $b . " month", strtotime($fe_mesini_pago)); //Esta la comentamos ahora tomamos la fecha de pago del term que se pago
                            $dia = date('d', $mes_ini_pago);
                            $mes = date('m', $mes_ini_pago);
                            $anio = date('Y', $mes_ini_pago);

                            switch ($mes) {
                                case 1:
                                    $mes_pago = "January";
                                    break;
                                case 2:
                                    $mes_pago = "February";
                                    break;
                                case 3:
                                    $mes_pago = "March";
                                    break;
                                case 4:
                                    $mes_pago = "April";
                                    break;
                                case 5:
                                    $mes_pago = "May";
                                    break;
                                case 6:
                                    $mes_pago = "June";
                                    break;
                                case 7:
                                    $mes_pago = "July";
                                    break;
                                case 8:
                                    $mes_pago = "August";
                                    break;
                                case 9:
                                    $mes_pago = "September";
                                    break;
                                case 10:
                                    $mes_pago = "October";
                                    break;
                                case 11:
                                    $mes_pago = "November";
                                    break;
                                case 12:
                                    $mes_pago = "December";
                                    break;
                            }
                            $item_description = $mes_pago . " " . $anio;
                            //$item_description="Registration / Payment(s) Tuition";
                            $terms = "";
                            $location = "";
                            $memo = "";
                            $item_product_service = "Un-Earned Tuition:" . $mes_pago . " " . $anio;
                            $itemQuantity = 1;
                            $itemRate = "";
                            $mn_pagado = $pago_normal_x_mes;
                            $mn_tax = "";
                            $ds_tax_provincia = "Out of Scope";

                            if ($fl_country <> 38) {
                                $ds_tax_provincia = "Exempt";
                            }

                            $count++;
                            $no_invoice = $invoice_id;


                            $spreadsheet->setActiveSheetIndex(0)
                                ->setCellValue('A' . $count . '', '' . $ds_login . $no_invoice . '') //invoice id
                                ->setCellValue('B' . $count . '', '' . $name_payments . '') //customer
                                ->setCellValue('C' . $count . '', '' . $fe_pago . '') //InvoiceDate
                                ->setCellValue('D' . $count . '', '' . $fe_pago . '') //Due paymetn
                                ->setCellValue('E' . $count . '', '' . $terms . '') //terms
                                ->setCellValue('F' . $count . '', '' . $location . '') //location
                                ->setCellValue('G' . $count . '', '' . $fl_sesion . '') //memo
                                ->setCellValue('H' . $count . '', '' . $item_product_service . '') //item producto servicio
                                ->setCellValue('I' . $count . '', '' . $item_description . '') //item descripticon
                                ->setCellValue('J' . $count . '', '' . $itemQuantity . '') //ItemQuantity
                                ->setCellValue('K' . $count . '', '' . $itemRate . '') //ItemRate
                                ->setCellValue('L' . $count . '', '' . $mn_pagado . '') //ItemAmount
                                ->setCellValue('M' . $count . '', '' . $ds_tax_provincia . '') //ItemTaxCode
                                ->setCellValue('N' . $count . '', '' . $mn_tax . '') //ItemTaxAmount
                                ->setCellValue('O' . $count . '', '' . $currency . ''); //Currency

                                $customer_name = $name_payments;



                        }

                    }









                }




            }




        //}
            $name_ingresado=$name;


}


// Rename sheet
$spreadsheet->getActiveSheet(0)->setTitle('Student');


//salida del excel.
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Tuition_management.xls"');
$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$writer->save('php://output');


?>