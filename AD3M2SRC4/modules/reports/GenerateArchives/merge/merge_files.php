<?php

/**
 * Simply import all pages and different bounding boxes from different PDF documents.
 */

use setasign\Fpdi;

//  require_once 'vendor/autoload.php';
//  require_once 'vendor/setasign/fpdf/fpdf.php';

require_once '../../lib/fpdf/vendor/autoload.php';
require_once '../../lib/fpdf/vendor/setasign/fpdf/fpdf.php';

$transcript_name = $_GET['transcript_name'];
$contract_name = $_GET['contract_name'];
$diploma_name = $_GET['diploma_name'];
$final_filename = $_GET['final_filename'];

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(2);
date_default_timezone_set('UTC');
$start = microtime(true);


ob_start();
$pdf = new Fpdi\Fpdi('P', 'mm', 'Letter');

$pdf->SetMargins(6.35, 6.35, 6.35);
// quarter-inch margins
$pdf->SetAutoPageBreak(false);

$files = [
    __DIR__ . $contract_name,
    __DIR__ . $diploma_name,
    __DIR__ . $transcript_name
];
foreach ($files as $file) {
    $pageCount = $pdf->setSourceFile($file);

    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $pdf->AddPage();
        $pageId = $pdf->importPage($pageNo, '/MediaBox');
        //$pageId = $pdf->importPage($pageNo, Fpdi\PdfReader\PageBoundaries::ART_BOX);
        $s = $pdf->useTemplate($pageId, 0, 0, 200, 250, true);
    }
}

$file = uniqid() . '.pdf';

$pdf->Output('D', $final_filename);

ob_end_flush();


foreach ($files as $file) {
    unlink($file);
}
