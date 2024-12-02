<?php
  require('../../lib/general.inc.php');
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../../lib/tcpdf/tcpdf.php');
  
  $clave = RecibeParametroNumerico('clave', True);
  
  $Query  = "SELECT cl_sesion ";
  $Query .= "FROM c_usuario ";
  $Query .= "WHERE fl_usuario=$clave";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  
  # Recupera datos del alumno: forma 1
  $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno, nb_programa ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_usuario c ";
  $Query .= "WHERE a.fl_programa=b.fl_programa AND a.cl_sesion=c.cl_sesion ";
  $Query .= "AND a.cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_nombres = str_texto($row[0]);
  $ds_apaterno = str_texto($row[1]);
  $ds_amaterno = str_texto($row[2]);
  $nb_programa = $row[3];
  
  $Query  = "SELECT fe_emision ";
  $Query .= "FROM k_pctia ";
  $Query .= "WHERE fl_alumno=$clave";
  $row = RecuperaValor($Query);
  if(!empty($row[0]))
    $fe_emision = date('F jS, Y', strtotime($row[0]));
  else
    $fe_emision = "";
  
  
  // create new PDF document
  $pdf = new TCPDF('P', 'mm', 'LETTER', true);

  //do not show header or footer
  $pdf->SetPrintHeader(false); 
  $pdf->SetPrintFooter(false);

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  //set margins
  //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(5);
  $pdf->SetFooterMargin(5);

  //set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, 5);

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
        <td style="width:100%; height:280px; text-align:center;">
         &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:15%;">
         &nbsp;
        </td>
        <td style="width:80%; height:60px; color:#000000; font-family:Arial; font-size:55px; font-weight:normal; text-align:left;">
         <br/>'.str_uso_normal(ObtenEtiqueta(526)).'
        </td>
      </tr>
      <tr>
        <td style="width:15%;">
         &nbsp;
        </td>
        <td style="width:80%; height:80px; color:#000000; font-family:Arial; font-size:85px; font-weight:normal; text-align:left;">
         <br/>'.$ds_nombres.' '.$ds_amaterno.' '.$ds_apaterno.'
        </td>
      </tr>
      <tr>
        <td style="width:15%;">
         &nbsp;
        </td>
        <td style="width:80%; height:120px; color:#000000; font-family:Arial; font-size:55px; font-weight:normal; text-align:left;">
         <br/>'.str_uso_normal(ObtenEtiqueta(527)).'
        </td>
      </tr>
      <tr>
        <td style="width:15%;">
         &nbsp;
        </td>
        <td style="width:80%; height:70px; color:#000000; font-family:Arial; font-size:85px; font-weight:normal; text-align:left;">
         <br/>'.$nb_programa.'
        </td>
      </tr>
      <tr>
        <td style="width:15%;">
         &nbsp;
        </td>
        <td style="width:80%; height:50px; color:#000000; font-family:Arial; font-size:55px; font-weight:normal; text-align:left;">
         <br/>'.str_uso_normal(ObtenEtiqueta(528)).'
        </td>
      </tr>
      <tr>
        <td style="width:15%;">
         &nbsp;
        </td>
        <td style="width:80%; height:30px; color:#000000; font-family:Arial; font-size:55px; font-weight:normal; text-align:left;">
         <br/>'.$fe_emision.'
        </td>
      </tr>
      <tr>
        <td style="width:15%;">
         &nbsp;
        </td>
        <td style="width:30%; height:80px; color:#000000; font-family:Arial; font-size:50px; font-weight:normal; text-align:left; border-top:1px solid black;">
         <br/>'.str_uso_normal(ObtenEtiqueta(529)).'
        </td>
      </tr>
      <tr>
        <td style="width:15%;">
         &nbsp;
        </td>
        <td style="width:45%; height:100px; color:#000000; font-family:Arial; font-size:50px; font-weight:normal; text-align:left; border-top:1px solid black;">
         <br/>'.str_uso_normal(ObtenEtiqueta(530)).'
        </td>
      </tr>
    </table>
    ';
    
  // output the HTML content
    $pdf->writeHTML($htmlcontent, true, 0, true, 0); 
    
  $nombre_archivo = 'Diploma '.$ds_nombres.' '.$ds_apaterno.'.pdf';
  //Close and output PDF document
  $pdf->Output($nombre_archivo, 'D');

?>