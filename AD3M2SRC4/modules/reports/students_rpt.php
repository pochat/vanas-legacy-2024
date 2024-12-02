<?php
  require('../../lib/general.inc.php');
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../../lib/tcpdf/tcpdf.php');
  
  
  $clave = RecibeParametroNumerico('clave', True);
  
  $Query  = "SELECT ds_login, fg_activo, ".ConsultaFechaBD('fe_alta', FMT_FECHA)." fe_alta, ";
  $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
  $Query .= "(".ConcatenaBD($concat).") 'fe_ultacc', ";
  $Query .= "no_accesos, ds_nombres, ds_apaterno, ds_amaterno, ds_email, a.fl_perfil, b.nb_perfil, fg_genero, ";
  $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, cl_sesion, ds_notas ";
  $Query .= "FROM c_usuario a, c_perfil b, c_alumno c ";
  $Query .= "WHERE a.fl_perfil=b.fl_perfil ";
  $Query .= "AND a.fl_usuario=c.fl_alumno ";
  $Query .= "AND fl_usuario=$clave";
  $row = RecuperaValor($Query);
  $ds_login = str_texto($row[0]);
  $fg_activo = $row[1];
  $fe_alta = $row[2];
  $fe_ultacc = $row[3];
  $no_accesos = $row[4];
  $ds_nombres = str_texto($row[5]);
  $ds_apaterno = str_texto($row[6]);
  $ds_amaterno = str_texto($row[7]);
  $ds_email = str_texto($row[8]);
  $fl_perfil = $row[9];
  $nb_perfil = $row[10];
  $fg_genero = $row[11];
  $fe_nacimiento = $row[12];
  $cl_sesion = $row[13];
  $ds_notas = $row[14];
  
  switch($fg_activo) {
    case '0': $ds_activo = ETQ_NO; break;
    case '1': $ds_activo = ETQ_SI; break;
  }
  switch($fg_genero) {
    case 'F': $ds_genero = ObtenEtiqueta(116); break;
    case 'M': $ds_genero = ObtenEtiqueta(115); break;
  }
  
  $row = RecuperaValor("SELECT fg_pago FROM c_sesion WHERE cl_sesion='$cl_sesion'");
  $fg_pago = $row[0];
  switch($fg_pago) {
    case '0': $ds_pago = ETQ_NO; break;
    case '1': $ds_pago = ETQ_SI; break;
  }
    
  # Recupera datos de la sesion y forma de aplicacion
  $concat = array(ConsultaFechaBD('fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultmod', FMT_HORA));
  $Query  = "SELECT fg_paypal, (".ConcatenaBD($concat).") 'fe_ultmod' ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $fg_paypal = $row[0];
  $fe_ultmod = $row[1];
  
  switch($fg_paypal) {
    case '0': $ds_paypal = ETQ_NO; break;
    case '1': $ds_paypal = ETQ_SI; break;
  }  
  
  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
  $Query .= ConsultaFechaBD('fe_birth', FMT_FECHA)." fe_birth, ";
  $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, d.ds_pais, ";
  $Query .= "ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, e.ds_pais, ";
  $Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, nb_programa, nb_periodo, b.fl_programa, c.fl_periodo ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_periodo=c.fl_periodo ";
  $Query .= "AND a.ds_add_country=d.fl_pais ";
  $Query .= "AND a.ds_eme_country=e.fl_pais ";
  $Query .= "AND cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $fl_programa = $row[25];
  $fl_periodo = $row[26];
  $nb_programa = $row[23];
  $nb_periodo = $row[24];
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  $ds_number = str_texto($row[3]);
  $ds_alt_number = str_texto($row[4]);
  $fg_gender = str_texto($row[6]);
  $fe_birth = $row[7];
  $ds_add_number = str_texto($row[8]);
  $ds_add_street = str_texto($row[9]);
  $ds_add_city = str_texto($row[10]);
  $ds_add_state = str_texto($row[11]);
  $ds_add_zip = str_texto($row[12]);
  $ds_add_country = str_texto($row[13]);
  $ds_eme_fname = str_texto($row[14]);
  $ds_eme_lname = str_texto($row[15]);
  $ds_eme_number = str_texto($row[16]);
  $ds_eme_relation = str_texto($row[17]);
  $ds_eme_country = str_texto($row[18]);
  $fg_ori_via = str_texto($row[19]);
  $ds_ori_other = str_texto($row[20]);
  $fg_ori_ref = str_texto($row[21]);
  $ds_ori_ref_name = str_texto($row[22]);
  
  # Informacion de referencia
  switch($fg_ori_via) {
    case 'A': $ds_ori_via = ObtenEtiqueta(290); break;
    case 'B': $ds_ori_via = ObtenEtiqueta(291); break;
    case 'C': $ds_ori_via = ObtenEtiqueta(292); break;
    case 'D': $ds_ori_via = ObtenEtiqueta(293); break;
    case '0': $ds_ori_via = ObtenEtiqueta(294)." - $ds_ori_other"; break;
  }
  switch($fg_ori_ref) {
    case '0': $ds_ori_ref = ObtenEtiqueta(17); break;
    case 'S': $ds_ori_ref = ObtenEtiqueta(296)." - $ds_ori_ref_name"; break;
    case 'T': $ds_ori_ref = ObtenEtiqueta(297)." - $ds_ori_ref_name"; break;
    case 'G': $ds_ori_ref = ObtenEtiqueta(298)." - $ds_ori_ref_name"; break;
  }
  
  # Recupera datos del aplicante: forma 2
  $Query  = "SELECT ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7 ";
  $Query .= "FROM k_ses_app_frm_2 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_resp2_1 = str_texto($row[0]);
  $ds_resp2_2 = str_texto($row[1]);
  $ds_resp2_3 = str_texto($row[2]);
  $ds_resp2_4 = str_texto($row[3]);
  $ds_resp2_5 = str_texto($row[4]);
  $ds_resp2_6 = str_texto($row[5]);
  $ds_resp2_7 = str_texto($row[6]);
  
  # Recupera datos del aplicante: forma 3
  $Query  = "SELECT fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, ";
  $Query .= "fg_resp_2_1, fg_resp_2_2, fg_resp_2_3, fg_resp_2_4, fg_resp_2_5, fg_resp_2_6, fg_resp_2_7, ";
  $Query .= "fg_resp_3_1, fg_resp_3_2 ";
  $Query .= "FROM k_ses_app_frm_4 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $fg_resp4_1_1 = str_ascii($row[0]);
  $fg_resp4_1_2 = str_ascii($row[1]);
  $fg_resp4_1_3 = str_ascii($row[2]);
  $fg_resp4_1_4 = str_ascii($row[3]);
  $fg_resp4_1_5 = str_ascii($row[4]);
  $fg_resp4_1_6 = str_ascii($row[5]);
  $fg_resp4_2_1 = str_ascii($row[6]);
  $fg_resp4_2_2 = str_ascii($row[7]);
  $fg_resp4_2_3 = str_ascii($row[8]);
  $fg_resp4_2_4 = str_ascii($row[9]);
  $fg_resp4_2_5 = str_ascii($row[10]);
  $fg_resp4_2_6 = str_ascii($row[11]);
  $fg_resp4_2_7 = str_ascii($row[12]);
  $fg_resp4_3_1 = str_ascii($row[13]);
  $fg_resp4_3_2 = str_ascii($row[14]);
  
  switch($fg_resp4_1_1) {
    case '1': $ds_resp4_1_1 = ETQ_SI; break;
    case '0': $ds_resp4_1_1 = ETQ_NO; break;
  }
  switch($fg_resp4_1_2) {
    case '1': $ds_resp4_1_2 = ETQ_SI; break;
    case '0': $ds_resp4_1_2 = ETQ_NO; break;
  }
  switch($fg_resp4_1_3) {
    case '1': $ds_resp4_1_3 = ETQ_SI; break;
    case '0': $ds_resp4_1_3 = ETQ_NO; break;
  }
  switch($fg_resp4_1_4) {
    case '1': $ds_resp4_1_4 = ETQ_SI; break;
    case '0': $ds_resp4_1_4 = ETQ_NO; break;
  }
  switch($fg_resp4_1_5) {
    case '1': $ds_resp4_1_5 = ETQ_SI; break;
    case '0': $ds_resp4_1_5 = ETQ_NO; break;
  }
  switch($fg_resp4_1_6) {
    case '1': $ds_resp4_1_6 = ETQ_SI; break;
    case '0': $ds_resp4_1_6 = ETQ_NO; break;
  }
  switch($fg_resp4_2_1) {
    case '1': $ds_resp4_2_1 = ETQ_SI; break;
    case '0': $ds_resp4_2_1 = ETQ_NO; break;
  }
  switch($fg_resp4_2_2) {
    case '1': $ds_resp4_2_2 = ETQ_SI; break;
    case '0': $ds_resp4_2_2 = ETQ_NO; break;
  }
  switch($fg_resp4_2_3) {
    case '1': $ds_resp4_2_3 = ETQ_SI; break;
    case '0': $ds_resp4_2_3 = ETQ_NO; break;
  }
  switch($fg_resp4_2_4) {
    case '1': $ds_resp4_2_4 = ETQ_SI; break;
    case '0': $ds_resp4_2_4 = ETQ_NO; break;
  }
  switch($fg_resp4_2_5) {
    case '1': $ds_resp4_2_5 = ETQ_SI; break;
    case '0': $ds_resp4_2_5 = ETQ_NO; break;
  }
  switch($fg_resp4_2_6) {
    case '1': $ds_resp4_2_6 = ETQ_SI; break;
    case '0': $ds_resp4_2_6 = ETQ_NO; break;
  }
  switch($fg_resp4_2_7) {
    case '1': $ds_resp4_2_7 = ETQ_SI; break;
    case '0': $ds_resp4_2_7 = ETQ_NO; break;
  }
  switch($fg_resp4_3_1) {
    case '0': $ds_resp4_3_1 = ObtenEtiqueta(97); break;
    case '1': $ds_resp4_3_1 = ObtenEtiqueta(98); break;
    case '2': $ds_resp4_3_1 = ObtenEtiqueta(99); break;
    case '3': $ds_resp4_3_1 = ObtenEtiqueta(107); break;
  }
  switch($fg_resp4_3_2) {
    case '0': $ds_resp4_3_2 = ObtenEtiqueta(97); break;
    case '1': $ds_resp4_3_2 = ObtenEtiqueta(98); break;
    case '2': $ds_resp4_3_2 = ObtenEtiqueta(99); break;
    case '3': $ds_resp4_3_2 = ObtenEtiqueta(107); break;
  }
  
  # Recupera datos del aplicante: forma 4
  $Query  = "SELECT ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, ds_resp_8 ";
  $Query .= "FROM k_ses_app_frm_3 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_resp3_1 = str_texto($row[0]);
  $ds_resp3_2_1 = str_texto($row[1]);
  $ds_resp3_2_2 = str_texto($row[2]);
  $ds_resp3_2_3 = str_texto($row[3]);
  $ds_resp3_3 = str_texto($row[4]);
  $ds_resp3_4 = str_texto($row[5]);
  $ds_resp3_5 = str_texto($row[6]);
  $ds_resp3_6 = str_texto($row[7]);
  $ds_resp3_7 = str_texto($row[8]);
  $ds_resp3_8 = str_texto($row[9]);
  
  switch($ds_resp3_6) {
    case 'A': $ds_resp3_6 = ObtenEtiqueta(314); break;
    case 'B': $ds_resp3_6 = ObtenEtiqueta(315); break;
    case 'C': $ds_resp3_6 = ObtenEtiqueta(316); break;
  }
  switch($ds_resp3_7) {
    case 'A': $ds_resp3_7 = ObtenEtiqueta(318); break;
    case 'B': $ds_resp3_7 = ObtenEtiqueta(319); break;
    case 'C': $ds_resp3_7 = ObtenEtiqueta(320); break;
    case 'D': $ds_resp3_7 = ObtenEtiqueta(321); break;
    case 'E': $ds_resp3_7 = ObtenEtiqueta(322); break;
  }
  
  # Obtiene el grupo, el term y el maestro
  $Query = "SELECT a.fl_grupo, b.fl_term, c.ds_nombres, c.ds_apaterno, b.nb_grupo ";
  $Query .= "FROM k_alumno_grupo a LEFT JOIN (c_grupo b LEFT JOIN c_usuario c ON b.fl_maestro = c.fl_usuario) ON a.fl_grupo = b.fl_grupo ";
  $Query .= "WHERE fl_alumno = $clave";
  $row1 = RecuperaValor($Query);
  $fl_grupo = $row1[0];
  $fl_term = $row1[1];
  $nb_maestro = $row1[2].'&nbsp;'.$row1[3];
  $ds_grupo = $row1[4];
  
  # Si no hay grupo asignado obtiene el term de la forma de aplicacion
  if(empty($fl_term)) {
    $Query ="SELECT fl_term FROM k_term WHERE fl_programa=$fl_programa AND fl_periodo=$fl_periodo";
    $row = RecuperaValor($Query);
    $fl_term = $row[0];
  }
  
  # Recupera datos de Official Transcript
  $Query = "SELECT ";
  $Query .= ConsultaFechaBD('fe_carta', FMT_FECHA)." fe_carta, ";
  $Query .= ConsultaFechaBD('fe_contrato', FMT_FECHA)." fe_contrato, ";
  $Query .= ConsultaFechaBD('fe_fin', FMT_FECHA)." fe_fin, ";
  $Query .= ConsultaFechaBD('fe_completado', FMT_FECHA)." fe_completado, ";
  $Query .= ConsultaFechaBD('fe_emision', FMT_FECHA)." fe_emision, ";
  $Query .= "fg_certificado, fg_honores ";
  $Query .= "FROM k_pctia ";
  $Query .= "WHERE fl_alumno = $clave ";
  $Query .= "AND fl_programa = $fl_programa ";
  $row = RecuperaValor($Query);
  $fe_carta = $row[0];
  $fe_contrato = $row[1];
  $fe_fin = $row[2];
  $fe_completado = $row[3];
  $fe_emision = $row[4];
  $fg_certificado = $row[5];
  $fg_honores = $row[6];
  switch($fg_certificado) {
    case '0': $ds_certificado = ETQ_NO; break;
    case '1': $ds_certificado = ETQ_SI; break;
  }
  switch($fg_honores) {
    case '0': $ds_honores = ETQ_NO; break;
    case '1': $ds_honores = ETQ_SI; break;
  }
  
  
  class MYPDF extends TCPDF 
  {
    public function Header() {
      global $ds_nombres;
      global $ds_apaterno;
      global $ds_amaterno;
      
      $encabezado = '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td style="height:30px; width:40%; color:#037EB7; font-family:Tahoma; font-size:25px; text-align:left; border-bottom:1px solid black;">
                <img src="../../images/vanas_logo_transcripts.jpg" />
        </td>
        <td style="width:60%; color:#000000; font-family:Arial; font-size:50px; font-weight:normal; text-align:center; border-bottom:1px solid black;">
          <br/><br/>'.ObtenEtiqueta(555).' - '.$ds_nombres.' '.$ds_apaterno.' '.$ds_amaterno.'
        </td>
      </tr>
    </table>';
      $this->SetFont('helvetica', '', 10);
      $this->Cell(0, 5, $this->writeHTML($encabezado, true, false, true, false, ''), 0, true, 'J', 0, '', 0, false, 'M', 'B');
    }
  }
  

  // create new PDF document
  $pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);

  // set default header data
  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
  
  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  //set margins
  $pdf->SetHeaderMargin(5);
  $pdf->SetFooterMargin(8);
  $pdf->SetTopMargin(20);

  //set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, 10);

  //set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

  //set some language-dependent strings
  $pdf->setLanguageArray($l); 

  // ---------------------------------------------------------

  // set font
  $pdf->SetFont('dejavusans', '', 10); 

  // add a page
  $pdf->AddPage("P"); 



  // create some HTML content
  $htmlcontent = '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(554).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ETQ_USUARIO.':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_login.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(117).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_nombres.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(118).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_apaterno.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(119).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_amaterno.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(114).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_genero.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(120).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$fe_nacimiento.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(121).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_email.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(540).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$fe_carta.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(541).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$fe_contrato.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(110).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$nb_perfil.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(426).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_grupo.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(297).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$nb_maestro.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(113).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_activo.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(196).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_notas.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(111).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$fe_alta.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(112).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$fe_ultacc.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(122).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$no_accesos.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:30px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(55).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(340).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$fe_ultmod.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(343).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_paypal.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(341).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_pago.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(360).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$nb_programa.'
        </td>
      </tr>
       <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(342).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$nb_periodo.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(289).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_ori_via.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(295).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_ori_ref.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:30px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(61).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(280).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_number.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(281).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_alt_number.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:30px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(62).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(282).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_add_number.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(283).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_add_street.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(284).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_add_city.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(285).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_add_state.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(286).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_add_zip.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(287).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_add_country.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:30px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(63).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(117).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_eme_fname.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(118).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_eme_lname.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(280).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_eme_number.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(288).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_eme_relation.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(287).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_eme_country.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:30px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(56).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(301).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp2_1.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(302).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp2_2.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(303).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp2_3.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(304).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp2_4.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(305).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp2_5.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(306).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp2_6.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(307).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp2_7.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:30px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(78).'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px; background-color:#E6E1DE; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:center;">
          '.ObtenEtiqueta(79).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(82).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_1_1.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(83).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_1_2.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(84).': 	
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_1_3.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(85).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_1_4.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(86).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_1_5.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(87).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_1_6.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px; background-color:#E6E1DE; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:center;">
          '.ObtenEtiqueta(80).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(88).': 	
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_2_1.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(89).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_2_2.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(90).': 	
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_2_3.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(91).': 	
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_2_4.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(92).': 	
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_2_5.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(93).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_2_6.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(94).': 	
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_2_7.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px; background-color:#E6E1DE; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:center;">
          '.ObtenEtiqueta(81).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(95).':	
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_3_1.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(96).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp4_3_2.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:30px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(57).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(308).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp3_1.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(309).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          1.&nbsp;'.$ds_resp3_2_1.'<br/>&nbsp;
          2.&nbsp;'.$ds_resp3_2_2.'<br/>&nbsp;
          3.&nbsp;'.$ds_resp3_2_3.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(310).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp3_3.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(311).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp3_4.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(312).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp3_5.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(313).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp3_6.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(317).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp3_7.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(323).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_resp3_8.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:30px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(542).'
        </td>
      </tr>';
      
  # Obtener el registro de APP fee
  $Query  = "SELECT fl_sesion,  CASE cl_metodo_pago WHEN 1 THEN 'Paypal' WHEN 2 THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' ";
  $Query .= "WHEN 6 THEN 'Cash' END cl_metodo_pago, (CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago, mn_pagado, ";
  $Query .= "". ConsultaFechaBD('b.fe_ultmod',FMT_FECHA) .", ds_comentario ds_comentario_app ";
  $Query .= "FROM c_sesion a, k_ses_app_frm_1 b  WHERE a.cl_sesion='$cl_sesion' and b.cl_sesion='$cl_sesion' ";
  $row = RecuperaValor($Query);
  $cl_metodo_app = $row[1];
  if(empty($cl_metodo_app))
    $cl_metodo_app= "(To be paid)";
  $fe_pago_app = $row[2];
  if(empty($fe_pago_app))
    $fe_pago_app = "(To be paid)";
  $mn_pagado_app = $row[3];
  if(empty($mn_pagado_app))
    $mn_pagado_app = "(To be paid)";
  $fe_ultmod1 = str_texto($row[4]);
  $ds_comentario_app = str_texto($row[5]);


  $htmlcontent .= '
      <br />
      <tr>
        <table align="center" width="100%" padding-left:50px;>
          <tr style="background-color:#E6E1DE;  font-family:Arial; font-weight:bold; font-size:25px;">
            <td width="8%">'.ObtenEtiqueta(481).'</td>
            <td width="12%">'.ObtenEtiqueta(482).'</td>
            <td width="12%">'.ObtenEtiqueta(485).'</td>
            <td width="12%">'.ObtenEtiqueta(486).'</td>
            <td width="12%">'.ObtenEtiqueta(374).'</td>
            <td width="12%">'.ObtenEtiqueta(596).'</td>
            <td width="12%">'.ObtenEtiqueta(483).'</td>
            <td width="20%">'.ObtenEtiqueta(72).'</td>
          </tr>
          <tr align="center" style=" font-family:Arial;  font-size:25px;">
            <td>Once</td>
            <td>Once</td>
            <td>'.$fe_ultmod1.'</td>
            <td>'.$mn_pagado_app.'</td>
            <td>'.$fe_pago_app.'</td>
            <td>'.$mn_pagado_app.'</td>
            <td>'.$cl_metodo_app.'</td>
            <td align="left">'.$ds_comentario_app.'</td>
          </tr>';

  # Recupera el term inicial
  $Query  = "SELECT fl_term_ini ";
  $Query .= "FROM k_term ";
  $Query .= "WHERE fl_programa=$fl_programa ";
  $Query .= "AND fl_term=$fl_term";
  $row = RecuperaValor($Query);
  $fl_term_ini = $row[0];
  
  # Recupera el tipo de pago para el curso
  $Query  = "SELECT fg_opcion_pago ";
  $Query .= "FROM k_app_contrato ";
  $Query .= "WHERE cl_sesion='$cl_sesion'"; 
  $row = RecuperaValor($Query);
  $fg_opcion_pago = $row[0];
  
  if(empty($fl_term_ini))
    $fl_term_ini=$fl_term;
  
  # Se obtiene la descripcion de la frecuencia del pago
  switch($fg_opcion_pago) {
    case 1:
      $mn_due='mn_a_due';
      $ds_frecuencia='ds_a_freq';
      break;
    case 2:
      $mn_due='mn_b_due';
      $ds_frecuencia='ds_b_freq';
      break;
    case 3:
      $mn_due='mn_c_due';
      $ds_frecuencia='ds_c_freq';
      break;
    case 4:
      $mn_due='mn_d_due';
      $ds_frecuencia='ds_d_freq';
      break;
  }
  $Query  = "SELECT $ds_frecuencia ";
  $Query .= "FROM k_programa_costos ";
  $Query .= "WHERE fl_programa=$fl_programa ";
  $row = RecuperaValor($Query);
  $ds_frecuencia = $row[0]; 

  # Recupera informacion de los pagos
  $Query  = "SELECT fl_term_pago, no_opcion, no_pago, ".ConsultaFechaBD("fe_pago", FMT_FECHA)."";
  $Query .= "FROM k_term_pago a, k_app_contrato b ";
  $Query .= "WHERE fl_term=$fl_term_ini ";
  $Query .= "AND no_opcion=$fg_opcion_pago AND b.no_contrato=1 AND cl_sesion='$cl_sesion'";
  $rs = EjecutaQuery($Query);
  for($i=0; $row = RecuperaRegistro($rs); $i++) {
    $fl_term_pago = $row[0];
    $no_opcion = $row[1];
    $no_pago = $row[2];
    $fe_limite_pago = $row[3];
    
    # Informacion de los pagos
    $Query  = "SELECT fl_term_pago, ";
    $Query .= "CASE cl_metodo_pago WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' ";
    $Query .= "WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash'  ";
    $Query .= "END cl_metodo_pago, ";
    $Query .= "(CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago, CONCAT('$','',FORMAT(mn_pagado,2)) mn_pagado, ds_comentario  "; 
    $Query .= "FROM k_alumno_pago ";
    $Query .= "WHERE fl_term_pago=$fl_term_pago ";
    $Query .= "AND fl_alumno=$clave";
    $row = RecuperaValor($Query);
    $fl_t_pago = $row[0];
    $cl_metodo_pago = $row[1];
    if(empty($cl_metodo_pago)) 
      $cl_metodo_pago = "(To be paid)";
    $fe_pago = $row[2];
    if(empty($fe_pago)) 
      $fe_pago = "(To be paid)";
    $mn_pagado =$row[3];
    if(empty($mn_pagado)) 
      $mn_pagado = "(To be paid)";
    $ds_comentario = $row[4];
    
    $Query  = "SELECT $mn_due ";
    $Query .= "FROM k_app_contrato ";
    $Query .= "WHERE cl_sesion='$cl_sesion'"; 
    $row = RecuperaValor($Query);
    $mn_due = $row[0];
    

  $htmlcontent .= '
          <tr  align="center" style="font-family:Arial;  font-size:25px;">
            <td>'.$no_pago.'</td>
            <td>'.$ds_frecuencia.'</td>
            <td>'.$fe_limite_pago.'</td>
            <td>$'.number_format($mn_due,2,'.',',').'</td>
            <td>'.$fe_pago.'</td>
            <td>'.$mn_pagado.'</td>
            <td>'.$cl_metodo_pago.'</td>
            <td align="left">'.$ds_comentario.'</td>
          </tr>';
  }
  $htmlcontent .= '        
        </table>
      </tr>
      <tr>
        <td style="width:100%; height:30px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(543).'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(544).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$fe_fin.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(545).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$fe_completado.'
        </td>
      </tr>
       <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(546).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$fe_emision.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(547).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_certificado.'
        </td>
      </tr>
      <tr>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(548).':
        </td>
        <td style="width:2%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          &nbsp;
        </td>
        <td style="width:49%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
          '.$ds_honores.'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:30px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:20px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center; vertical-align:bottom;">
          '.ObtenEtiqueta(549).'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
        <tr>
        <td style="width:10%; height:15px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center;">
          '.ObtenEtiqueta(550).'
        </td>
        <td style="width:40%; height:15px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center;">
          '.ObtenEtiqueta(551).'
        </td>
        <td style="width:15%; height:15px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center;">
          '.ObtenEtiqueta(557).'
        </td>
        <td style="width:10%; height:15px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center;">
          '.ObtenEtiqueta(428).'
        </td>
        <td style="width:19%; height:15px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center;">
          '.ObtenEtiqueta(552).'
        </td>
        <td style="width:6%; height:15px; background-color:#0071BF; color:#FFFFFF; font-family:Arial; font-size:25px; font-weight:bold; text-align:center;">
          '.ObtenEtiqueta(553).'
        </td>
      </tr>';
   
  # Recupera los nivles del programa
  $Query  = "SELECT count(a.fl_leccion), a.no_grado, b.nb_programa ";
  $Query .= "FROM c_leccion a, c_programa b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_programa=$fl_programa ";
  $Query .= "GROUP BY a.no_grado ";
  $Query .= "ORDER BY a.no_grado";
  $rs = EjecutaQuery($Query);
  
  # Recupera los distintos fl_term en los que ha estado un alumno
  $Query  = "SELECT a.fl_term ";
  $Query .= "FROM k_alumno_term a, k_term b, c_periodo c ";
  $Query .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$clave ";
  $Query .= "ORDER BY c.fe_inicio, b.no_grado";
  $consulta = EjecutaQuery($Query);
  
  for($tot_grados = 0; $row = RecuperaRegistro($rs); $tot_grados++) {
    $tot_lecciones[$tot_grados] = $row[0];
    $no_grado[$tot_grados] = $row[1];
    $nb_programa = str_uso_normal($row[2]);
    $row_term = RecuperaRegistro($consulta);
    
    # Recupera las lecciones del grado
    $Query  = "SELECT a.fl_leccion, a.no_semana, a.ds_titulo, b.fl_semana ";
    $Query .= "FROM c_leccion a LEFT JOIN k_semana b ";
    $Query .= "ON (a.fl_leccion=b.fl_leccion AND b.fl_term=$row_term[0]) ";
    $Query .= "WHERE a.fl_programa=$fl_programa ";
    $Query .= "AND a.no_grado=$no_grado[$tot_grados] ";
    $Query .= "ORDER BY a.no_semana";
    $rs2 = EjecutaQuery($Query);
    for($j = 0; $row2 = RecuperaRegistro($rs2); $j++) {
      $fl_leccion[$tot_grados][$j] = $row2[0];
      $no_semana[$tot_grados][$j] = $row2[1];
      $ds_titulo[$tot_grados][$j] = str_uso_normal($row2[2]);
      $fl_semana[$tot_grados][$j] = $row2[3];
    }
  }
  
  
  # Presenta datos los cursos impartidos
  for($i = 0; $i < $tot_grados; $i++) {
    $htmlcontent .= '
      <tr>
        <td style="width:100%; height:15px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:15px; color:#000000; font-family:Arial; font-size:25px; font-weight:bold; text-align:center;">
          Term '.$no_grado[$i].' 
        </td>
      </tr>
    ';
    $adicionales = 0;
    for($j = 0; $j < $tot_lecciones[$i]; $j++) 
    {
      if(!empty($no_semana[$i][$j]))
      {
        if($j % 2 != 0)
          $background = '';
        else
          $background = 'background-color:#E6E1DE;';
        
        $Query  = "SELECT fl_clase, ".ConsultaFechaBD('fe_clase', FMT_CAPTURA)." fe_clase, ";
        $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN)." hr_clase, fg_obligatorio, fg_adicional ";
        $Query .= "FROM k_clase a, k_entrega_semanal b ";
        $Query .= "WHERE a.fl_semana=b.fl_semana ";
        $Query .= "AND a.fl_grupo=b.fl_grupo ";
        $Query .= "AND b.fl_alumno=$clave ";
        $Query .= "AND a.fl_semana=".$fl_semana[$i][$j]." ";
        $Query .= "ORDER BY fl_clase";
        $cons = EjecutaQuery($Query);
        while($row2 = RecuperaRegistro($cons))
        {
          $fl_clase[$i][$j] = $row2[0];
          if(!empty($row2[1])) # Ya se habia puesto una fecha para la clase
          { 
            $fe_clase[$i][$j] = $row2[1];
            $hr_clase[$i][$j] = $row2[2];
          }
          $fg_obligatorio[$i][$j] = $row2[3];
          $fg_adicional[$i][$j] = $row2[4];
          
          if($fg_adicional[$i][$j] == '1')
          {
            $adicionales++;
            $no_semana[$i][$j] = '';
            $ds_titulo[$i][$j] = '';
            $row[0] = '';
          }
          else
          {
            # Revisa si hay calificacion para el alumno en esta leccion
            $Query  = "SELECT b.cl_calificacion, b.ds_calificacion, b.fg_aprobado ";
            $Query .= "FROM k_entrega_semanal a, c_calificacion b ";
            $Query .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
            $Query .= "AND a.fl_alumno=$clave ";
            $Query .= "AND a.fl_semana=".$fl_semana[$i][$j];
            $row = RecuperaValor($Query);
          }
          
          # Consulta el estatus de asistencia a live session
          $Query = "SELECT a.fl_live_session, a.fl_usuario, b.nb_estatus, d.fl_semana
                    FROM k_live_session_asistencia a, c_estatus_asistencia b, k_live_session c, k_clase d
                    WHERE a.cl_estatus_asistencia = b.cl_estatus_asistencia
                    AND a.fl_live_session = c.fl_live_session
                    AND c.fl_clase = d.fl_clase
                    AND c.fl_clase = ".$fl_clase[$i][$j]." 
                    AND d.fl_semana = ".$fl_semana[$i][$j]."
                    AND a.fl_usuario = $clave";
          $rasis = RecuperaValor($Query);
        
          switch($fg_obligatorio[$i][$j])
          {
          case '0':
            $obliga = ETQ_NO;
          break;
          case '1':
            $obliga = ETQ_SI;
          break;
          default:
            $obliga = '';
          }
        
          $htmlcontent .= '
        <tr>
          <td style="width:10%; height:15px; '.$background.' color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:center;">
            '.$no_semana[$i][$j].'
          </td>
          <td style="width:40%; height:15px; '.$background.' color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
            '.$ds_titulo[$i][$j].'
          </td>
          <td style="width:15%; height:15px; '.$background.' color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:center;">
            '.$fe_clase[$i][$j].'
          </td>
          <td style="width:10%; height:15px; '.$background.' color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:center;">
            '.$obliga.'
          </td>
          <td style="width:19%; height:15px; '.$background.' color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:center;">
          ';
        
          if(!empty($rasis[0]))
          {
            $htmlcontent .= ''.$rasis[2].'';
          }
          else
          {
            $Query = "SELECT fe_clase FROM k_clase WHERE fl_semana = ".$fl_semana[$i][$j]." AND fl_grupo = $fl_grupo";
            $fecha_clase = RecuperaValor($Query);
            $diferencia_fechas = strtotime($fecha_clase[0]) + 1200 - time(); 
            if($diferencia_fechas <= 0)
            {
              $ds_rasis = RecuperaValor("SELECT nb_estatus FROM c_estatus_asistencia d WHERE cl_estatus_asistencia=1");
              $htmlcontent .= ''.$ds_rasis[0].'';
            }
            else
              $htmlcontent .= '&nbsp;';
          }

          $htmlcontent .= '</td>';
        
          if(!empty($row[0]))
          {
            $htmlcontent .= '    
          <td style="width:6%; height:15px; '.$background.' color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:center;">
            ';
            $htmlcontent .= ''.$row[0].'';
          }
          else
            $htmlcontent .= '
          <td style="width:6%; height:15px; '.$background.' color:#000000; font-family:Arial; font-size:25px; font-weight:normal; text-align:left;">
            &nbsp;';
          $htmlcontent .= '
          </td>
        </tr>';
        }    
      }
    }
  }
  $htmlcontent .= '    
    </table>
    ';
  
  // output the HTML content
    $pdf->writeHTML($htmlcontent, true, 0, true, 0); 

  $nombre_archivo = 'Record '.$ds_nombres.' '.$ds_apaterno.'.pdf';
  //Close and output PDF document
  $pdf->Output($nombre_archivo, 'D');

?>