<?php
# Libreria de funciones
require '../../lib/general.inc.php';

require_once '../../lib/PHPExcel1.8/PHPExcel/IOFactory.php';
require_once '../../lib/PHPExcel1.8/PHPExcel.php';

//usando phpSpeedsheet.
require '../../lib/PHPspeed/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

# Funcion para quitar caracteres especiales y saltos de linea
function getStrParaCSV($str)
{
    $str_aux = $str;
    $str_aux = str_replace(",", " ", $str_aux);
    $str_aux = str_replace("\n", " ", $str_aux);
    $str_aux = str_replace("\r", " ", $str_aux);

    return $str_aux;
}



//$spreadsheet = IOFactory::load('ID-03642-Vancouver_Animation_School_2020-10-28-Current_correct.xlsx');
$spreadsheet = new Spreadsheet();


$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Did you research other schools Which ones Why did you choose to enroll at VANAS')
    ->setCellValue('B1', 'What inspires you about being an artist')
    ->setCellValue('C1', 'On a scale from 1 to 10, 10 being the highest. How do you rate yourself as an artist')
    ->setCellValue('D1', 'Do you have a clear picture of where you would like your career to be in 5 years')
    ->setCellValue('E1', 'What is your most important career goal for this year')
    ->setCellValue('F1', 'When did you know you wanted to be an artist')
    ->setCellValue('G1', 'What do you like about the Animation/Film/Games Industries')
    ->setCellValue('H1', 'What do you expect from VANAS')
    ->setCellValue('I1', 'What are your three big goals in your career')
    ->setCellValue('J1', 'What do you expect your chosen program to do for your career')
    ->setCellValue('K1', 'What kind of effect do you expect Vanas to have on your life apart from your career')
    ->setCellValue('L1', 'What do you think will be necessary for you to reach the objectives you listed in questions 3 and 4')
    ->setCellValue('M1', 'In completing your chosen program of study, who do you think will be doing most of the actual academic work')
    ->setCellValue('N1', 'How many hours per day do you expect to invest in your chosen program')
    ->setCellValue('O1', 'What other expectations do you have about your chosen program')
    ->setCellValue('P1', 'Age');



$Query  = "SELECT a.ds_resp_1, a.ds_resp_2, a.ds_resp_3, a.ds_resp_4, a.ds_resp_5, a.ds_resp_6, a.ds_resp_7,a.cl_sesion, ";
$Query .= "b.ds_resp_1, b.ds_resp_2_1, b.ds_resp_2_2, b.ds_resp_2_3, b.ds_resp_3, b.ds_resp_4, b.ds_resp_5, b.ds_resp_6, b.ds_resp_7, b.ds_resp_8 ";
$Query .= "FROM k_ses_app_frm_2 a JOIN k_ses_app_frm_3 b on b.cl_sesion=a.cl_sesion  ";
$rs = EjecutaQuery($Query);
for ($i = 2; $row = RecuperaRegistro($rs); $i++) {

    $ds_resp_1 = $row[0];
    $ds_resp_2 = $row[1];
    $ds_resp_3 = $row[2];
    $ds_resp_4 = $row[3];
    $ds_resp_5 = $row[4];
    $ds_resp_6 = $row[5];
    $ds_resp_7 = $row[6];
    $cl_sesion = $row[7];

    $ds_resp3_1 = str_texto($row[8]);
    $ds_resp3_2_1 = str_texto($row[9]);
    $ds_resp3_2_2 = str_texto($row[10]);
    $ds_resp3_2_3 = str_texto($row[11]);
    $ds_resp3_3 = str_texto($row[12]);
    $ds_resp3_4 = str_texto($row[13]);
    $ds_resp3_5 = str_texto($row[14]);
    $ds_resp3_6 = str_texto($row[15]);
    $ds_resp3_7 = str_texto($row[16]);
    $ds_resp3_8 = str_texto($row[17]);

    $Queryage = "SELECT  TIMESTAMPDIFF(YEAR,fe_birth,CURDATE()) AS edad  from k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion' ";
    $rowa = RecuperaValor($Queryage);
    $age = $rowa[0];




    switch ($ds_resp3_6) {
        case 'A':
            $resp=ObtenEtiqueta(314);
            break;
        case 'B':
            $resp=ObtenEtiqueta(315);
            break;
        case 'C':
            $resp=ObtenEtiqueta(316);
            break;
    }

    switch ($ds_resp3_7) {
        case 'A':
            $resp2=ObtenEtiqueta(318);
            break;
        case 'B':
            $resp2=ObtenEtiqueta(319);
            break;
        case 'C':
            $resp2=ObtenEtiqueta(320);
            break;
        case 'D':
            $resp2=ObtenEtiqueta(321);
            break;
        case 'E':
            $resp2=ObtenEtiqueta(322);
            break;
    }




   $spreadsheet->setActiveSheetIndex(0)


       ->setCellValue('A' . $i . '', $ds_resp_1)
       ->setCellValue('B' . $i . '', '' . $ds_resp_2 . '')
       ->setCellValue('C' . $i . '', '' . $ds_resp_3 . '')
       ->setCellValue('D' . $i . '', $ds_resp_4)
       ->setCellValue('E' . $i . '', '' . $ds_resp_5 . '')
       ->setCellValue('F' . $i . '', '' . $ds_resp_6 . '')
       ->setCellValue('G' . $i . '', '' . $ds_resp_7 . '')
       ->setCellValue('H' . $i . '', '' .$ds_resp3_1 . '')
       ->setCellValue('I' . $i . '', '1.' . $ds_resp3_2_1 . " 2." . $ds_resp3_2_2 . " 3." . $ds_resp3_2_3)
       ->setCellValue('J' . $i . '', '' . $ds_resp3_3 . '')
       ->setCellValue('K' . $i . '', '' . $ds_resp3_4 . '')
       ->setCellValue('L' . $i . '', '' . $ds_resp3_5 . '')
       ->setCellValue('M' . $i . '', '' . $resp . '')
       ->setCellValue('N' . $i . '', '' . $resp2 . '')
       ->setCellValue('O' . $i . '', '' . $ds_resp3_8 . '')
       ->setCellValue('P' . $i . '', '' . $age . '');




}

// Rename sheet
$spreadsheet->getActiveSheet(0)->setTitle('Student');


//salida del excel.
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="App.xls"');
$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$writer->save('php://output');


?>