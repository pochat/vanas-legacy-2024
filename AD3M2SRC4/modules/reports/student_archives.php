<?php

// Include libraries and other configuration files
require('../../lib/general.inc.php');
require_once('../../lib/tcpdf/config/lang/eng.php');
require_once('../../lib/tcpdf/tcpdf.php');

// set data
$fl_usuario = RecibeParametroNumerico('clave', True);
$contract_num = RecibeParametroNumerico('con', True);


/** GET fl_sesion */
$Query = "SELECT s.`fl_sesion`,s.`cl_sesion` 
FROM c_usuario AS u
JOIN c_sesion AS s
ON u.`cl_sesion` = s.`cl_sesion`
WHERE u.`fl_usuario` = $fl_usuario;";
$row = RecuperaValor($Query);
// Set fl_sesion
$fl_sesion = $row[0];
$cl_sesio=$row[1];


/**GET_NAME_PROGRAM*/
$Query="SELECT b.nb_programa FROM vanas_prod.k_ses_app_frm_1 a
JOIN c_programa b ON a.fl_programa=b.fl_programa
AND a.cl_sesion='".$cl_sesio."'  ";
$row=RecuperaValor($Query);
$nb_programa=$row[0];
$nb_programa=strtr($nb_programa," ","-");
$nb_programa=strtr($nb_programa,"%","-");


/** GET student name */
$Query = "SELECT CONCAT(u.`ds_nombres`,'_',u.`ds_apaterno`) AS fullname 
FROM c_usuario AS u
WHERE u.`fl_usuario` = $fl_usuario;";
$row = RecuperaValor($Query);
// Set fl_sesion
$student_names = $row[0];


// Get fe_emision
$Query  = "SELECT fe_graduacion ";
$Query .= "FROM k_pctia ";
$Query .= "WHERE fl_alumno = $fl_usuario";
$row = RecuperaValor($Query);
if(!empty($row[0])){
  $fe_graduate = date('Ymd', strtotime($row[0]));
}

// Generate Transcript
$_GET['user'] = $fl_usuario;
include './GenerateArchives/pctia_rpt.php';

// Generate Diploma
$_GET['user'] = $fl_usuario;
include './GenerateArchives/newDiploma_rpt.php';

//Generate contract
$_GET['c'] = $fl_sesion;
$_GET['con'] = $contract_num;
$_GET['user'] = $fl_usuario;
include './GenerateArchives/documents_rpt.php';

// Build Names
$contract_name = '/tempPDFs/contract_'.$fl_sesion.'_'.$contract_num.'_'.$fl_usuario.'.pdf';
$diploma_name = '/tempPDFs/diploma_'.$fl_usuario.'.pdf';
$transcript_name = '/tempPDFs/transcript_'.$fl_usuario.'.pdf';
$final_filename = $student_names.'_'.$nb_programa.'_Archived_'.$fe_graduate.'.pdf';

// Download final file
$_GET['transcript_name'] = $transcript_name;
$_GET['final_filename'] = $final_filename;
$_GET['contract_name'] = $contract_name;
$_GET['diploma_name'] = $diploma_name;
include './GenerateArchives/merge/merge_files.php';

