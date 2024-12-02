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
    ->setCellValue('A1','Course')
    ->setCellValue('B1','Name') 
    ->setCellValue('C1','Term')
    ->setCellValue('D1','Payment Number')
    ->setCellValue('E1','Payment Frequency') 
    ->setCellValue('F1','Payment Due') 
    ->setCellValue('G1','Payment Amount')  
    ->setCellValue('H1','Payment Date') 
    ->setCellValue('I1','Payment Method') 
    ->setCellValue('J1','Country') 
    ->setCellValue('K1','Earned')
    ->setCellValue('L1','Unearned')
    ->setCellValue('M1','E/U')
    ->setCellValue('N1','State/Province');


$rs = EjecutaQuery($Query3);
for($i=1;$row=RecuperaRegistro($rs);$i++) {

    $course=$row[1];
    $name=$row[2];
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



    $spreadsheet->setActiveSheetIndex(0)


       ->setCellValue('A'.$i.'',$course) //Student id
       ->setCellValue('B'.$i.'', ''.$name.'') //
       ->setCellValue('C'.$i.'',''.$term.'') //company tag
       ->setCellValue('D'.$i.'',$payment_number) //Last name 
       ->setCellValue('E'.$i.'',''.$frequency.'') //First name
       ->setCellValue('F'.$i.'',''.$payment_due.'') //initial
       ->setCellValue('G'.$i.'',''.$payment_amount.'') //adress 1
       ->setCellValue('H'.$i.'',''.$payment_date.'') //adress 2
       ->setCellValue('I'.$i.'',''.$payment_method.'') //city                                            
       ->setCellValue('J'.$i.'',''.$country.'') //province
       ->setCellValue('K'.$i.'',''.$earned.'') //postal
       ->setCellValue('L'.$i.'',''.$unearded.'') //country
       ->setCellValue('M'.$i.'',''.$eu.'')
       ->setCellValue('N'.$i.'',''.$state.'');




}


// Rename sheet 
$spreadsheet->getActiveSheet(0)->setTitle('Student'); 


//salida del excel.
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Tuition_management.xls"');
$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$writer->save('php://output');


?>