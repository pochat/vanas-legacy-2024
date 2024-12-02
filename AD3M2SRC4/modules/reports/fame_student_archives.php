<?php

// Include libraries and other configuration files
require('../../lib/general.inc.php');
require_once('../../lib/tcpdf/config/lang/eng.php');
require_once('../../lib/tcpdf/tcpdf.php');

// set data
$fl_usuario = RecibeParametroNumerico('u', True);
$clave = RecibeParametroNumerico('c', True);
$fl_instituto = RecibeParametroNumerico('i', True);
$fl_programa = RecibeParametroNumerico('p', True);
$fg_tipo = RecibeParametroNumerico('fg_tipo', True);

// /** GET fl_sesion */
// $Query = "SELECT s.`fl_sesion`
// FROM c_usuario AS u
// JOIN c_sesion AS s
// ON u.`cl_sesion` = s.`cl_sesion`
// WHERE u.`fl_usuario` = $fl_usuario;";
// $row = RecuperaValor($Query);
// // Set fl_sesion
// $fl_sesion = $row[0];


/** GET student name */
$Query = "SELECT CONCAT(u.`ds_nombres`,'_',u.`ds_apaterno`) AS fullname, fl_instituto
FROM c_usuario AS u
WHERE u.`fl_usuario` = $fl_usuario;";
$row = RecuperaValor($Query);
// Set fl_sesion
$student_names = $row[0];


// // Get fe_emision
// $Query  = "SELECT fe_graduacion ";
// $Query .= "FROM k_pctia ";
// $Query .= "WHERE fl_alumno = $fl_usuario";
// $row = RecuperaValor($Query);
// if(!empty($row[0])){
//   $fe_graduate = date('Ymd', strtotime($row[0]));
// }



// Generate Transcript quiz
$_GET['c'] = $clave;
$_GET['u'] = $fl_usuario;
$_GET['i'] = $fl_instituto;
include './GenerateArchives/transcript_fame_quiz_rpt.php';

// Generate certificate 
$_GET['u'] = $fl_usuario;
$_GET['p'] = $fl_programa;
$_GET['fg_tipo'] = $fg_tipo;
include './GenerateArchives/certificado_pdf.php';

// Generate transcript teacher
$_GET['c'] = $clave;
$_GET['u'] = $fl_usuario;
$_GET['i'] = $fl_instituto;
include './GenerateArchives/transcript_fame_quiz_teacher_rpt.php';

// Build Names
$certificate_name = '/tempPDFs/Certificate_'.$fl_usuario.'_'.$fl_programa.'.pdf';
$transcript_teacher_name = '/tempPDFs/Transcript_teacher_' . $clave. '_' . $fl_usuario . '.pdf';
$transcript_quiz_name = '/tempPDFs/Transcript_quiz_' . $clave. '_' . $fl_usuario . '.pdf';
$final_filename = $student_names.'_FAME_Archives.pdf';

// Download final file
$_GET['certificate_name'] = $certificate_name;
$_GET['transcript_teacher_name'] = $transcript_teacher_name;
$_GET['transcript_quiz_name'] = $transcript_quiz_name;
$_GET['final_filename'] = $final_filename;
include './GenerateArchives/mergeFame/merge_files.php';

