<?php

/**
 * Simply import all pages and different bounding boxes from different PDF documents.
 */

use setasign\Fpdi;

require_once '../../lib/fpdf/vendor/autoload.php';
require_once '../../lib/fpdf/vendor/setasign/fpdf/fpdf_protection.php';

$certificate_name = $_GET['certificate_name'];
$transcript_teacher_name = $_GET['transcript_teacher_name'];
$transcript_quiz_name = $_GET['transcript_quiz_name'];
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
    __DIR__ . $certificate_name,
    __DIR__ . $transcript_teacher_name,
    __DIR__ . $transcript_quiz_name
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

// $pdf->SetProtection(array('modify'), '000', '111');

$pdf->Output('D', $final_filename);

ob_end_flush();


foreach ($files as $file) {
    unlink($file);
}
