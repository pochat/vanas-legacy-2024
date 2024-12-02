<?php
/**
 * Import other classes
 */
include('../../../lib/general.inc.php');
include('../../../lib/tcpdf/config/lang/eng.php');
include('../../../lib/tcpdf/tcpdf.php');

  /**
   * Get clave by params
   */
  $clave = RecibeParametroNumerico('clave', True);

  // Get fe_emision
  $Query  = "SELECT fe_graduacion ";
  $Query .= "FROM k_pctia ";
  $Query .= "WHERE fl_alumno=$clave";
  $row = RecuperaValor($Query);
  if(!empty($row[0])){
    $fe_graduacion = date('F jS, Y', strtotime($row[0]));
    $fe_graduate = date('Ymd', strtotime($row[0]));
  }
  else{
    $fe_graduacion = "";
  }

  # Get cl_sesion
  $Query  = "SELECT cl_sesion ";        //Query
  $Query .= "FROM c_usuario ";          //Query
  $Query .= "WHERE fl_usuario=$clave";  //Query
  $row = RecuperaValor($Query);         //get Query from the RecuperaValor() function
  $cl_sesion = $row[0];                 //set the cl_sesion from the results


  /**
   * get fl_sesion
   */
  $Query  = "SELECT fl_sesion ";                    //Query
  $Query .= "FROM c_sesion ";                       //Query
  $Query .= "WHERE cl_sesion = '".$cl_sesion."';";  //Query
  $row = RecuperaValor($Query);                     //get Query from the RecuperaValor() function
  $fl_sesion = $row[0];                             //set the fl_sesion from the results


  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT b.fl_programa, c.fl_periodo ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_periodo=c.fl_periodo ";
  $Query .= "AND a.ds_add_country=d.fl_pais ";
  $Query .= "AND a.ds_eme_country=e.fl_pais ";
  $Query .= "AND cl_sesion='$cl_sesion'";

  $row = RecuperaValor($Query);

  $fl_programa = $row[0];

  # Recupera datos de Official Transcript
  $Query="SELECT ".ConsultaFechaBD('fe_fin', FMT_FECHA)." fe_fin,
  ". ConsultaFechaBD('fe_completado', FMT_FECHA)." fe_completado,
  ".ConsultaFechaBD('fe_emision', FMT_FECHA)." fe_emision,
  ".ConsultaFechaBD('fe_graduacion', FMT_FECHA)." fe_graduacion
  FROM k_pctia
  WHERE fl_alumno = $clave
  AND fl_programa = $fl_programa ";

  $row = RecuperaValor($Query);

  $fe_fin_temp = explode("-", $row[0]);
  $fe_fin = substr(ObtenNombreMes($fe_fin_temp[1]),0,3).' '.$fe_fin_temp[0].', '.$fe_fin_temp[2];


  /**
   * Set id_template
   * 194 -> Diploma template
   */
  $id_template = 194;

  /**
   * Set header, body and footer
   */
  $ds_header = genera_documento($fl_sesion, 1, $id_template);
  $ds_cuerpo = genera_documento($fl_sesion, 2, $id_template);
  $ds_footer = genera_documento($fl_sesion, 3, $id_template);

  # Get Student data
  $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno, nb_programa ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_usuario c ";
  $Query .= "WHERE a.fl_programa=b.fl_programa AND a.cl_sesion=c.cl_sesion ";
  $Query .= "AND a.cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_nombres = str_texto($row[0]);
  $ds_apaterno = str_texto($row[1]);
  $ds_amaterno = str_texto($row[2]);
  $nb_programa = $row[3];

  // Set full name (first name, last name)
  $fullname = $ds_nombres." ".$ds_amaterno." ".$ds_apaterno;

  //get diploma/certificate
  $Query="SELECT ds_credential FROM k_programa_costos where fl_programa=$fl_programa ";
  $row=RecuperaValor($Query);
  $ds_credential=$row['ds_credential'];

  $rowsa = RecuperaValor("SELECT notation_transcript FROM c_alumno WHERE fl_alumno=$clave ");
  $ds_notation = $rowsa['notation_transcript'];


// count the strings
  $nb_program_string_count = strlen($nb_programa." ".$ds_credential);
  $fullname_string_count = strlen($fullname);

  // Variables Replacement
  $ds_cuerpo = str_replace("#pg_emisiond#",$fe_graduacion,$ds_cuerpo);
  $ds_cuerpo = str_replace("#st_full_name#",$fullname,$ds_cuerpo);
  $ds_cuerpo = str_replace("#fe_diploma#", $fe_fin, $ds_cuerpo);

  if (empty($ds_notation)) {
    $ds_cuerpo = str_replace("Notation:", "", $ds_cuerpo);
  }

  // resize for a large full name
  if($fullname_string_count >= 23){
    $name_div = '<div class="names" style="font-size:85px; font-weight:bold; font-family:Arial">';
    $new_name_div = '<div class="names" style="font-size:58px; font-weight:bold; font-family:Arial">';
    $ds_cuerpo = str_replace($name_div,$new_name_div,$ds_cuerpo);
  }

  /**
   * QR Code
   */

  // set link
  $link_qr='campus.vanas.ca/StudentAccreditation.php?clave='.$fl_sesion.'&type=1&data='.$clave;

   // set style for barcode
   $style = array(
    'border' => 0,
    'padding' => 0,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
  );
  ob_clean();
  /**
   * PDF Code
 */
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


  //set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

  //set some language-dependent strings
  $pdf->setLanguageArray($l);

  // ---------------------------------------------------------

  // set font
  $pdf->SetFont('dejavusans', '', 10);

  // add a page
  $pdf->AddPage("P");

  //Background
  $bMargin = $pdf->getBreakMargin();
  //set auto page breaks
  $pdf->SetAutoPageBreak(false, 0);
  // image path
  $img_file = "../../images/VANAS_Diploma_2021_blank.png";
  // inserte image
  $pdf->Image($img_file, 0, 0, 216, 280, '', '', '', false, 300, '', false, false, 0);
  // image is a pageMark
  $pdf->setPageMark();

  // Set Margins
  $left = 25;     // left margin  39
  $top = 0;      // top margin   87
  $right = 20;    // right margin 20
  $pdf->SetMargins($left,$top,$right,true);

  //Margin of the body
  $body = '
  <div>
    '.$ds_cuerpo.'
  </div>';

  // output the HTML content
  $pdf->writeHTML($body, true, 0, true, 0);

  // QRCODE,L : QR-CODE Low error correction
  $pdf->write2DBarcode(''.$link_qr.'', 'QRCODE,L', 165, 224, 23, 23, $style, 'N');

  // print date and Sign
  if($nb_program_string_count <= 31){

    // Validate large for full name  define la posicion de altura del date en documento
    //if($fullname_string_count >= 23){
    //$pdf->SetFont('dejavusans', '', 15);
   // $pdf->SetXY(29, 195);
    //}else{
    //  $pdf->SetFont('dejavusans', '', 15);
    //  $pdf->SetXY(29, 195);
    //}

   // $qr_description = $fe_fin;
   // $pdf->writeHTML($qr_description, true, false, false, false, '');

    // set bacground image
    $vanasSignature = "../../images/diploma-vanas-signature.png";

    // Validation large for full name
    if($fullname_string_count >= 23){
      $pdf->Image($vanasSignature, 35, 205, 35, 35, '', '', '', false, 300, '', false, false, 0);
    }else{
      $pdf->Image($vanasSignature, 35, 208, 35, 35, '', '', '', false, 300, '', false, false, 0);
    }

    // set the starting point for the page content
    $pdf->setPageMark();

  }

  if($nb_program_string_count > 31){

    // Validate large for full name
   // if($fullname_string_count >= 23){
   //   $pdf->SetFont('dejavusans', '', 15);
   //   $pdf->SetXY(29, 205);
   // }else{
   //   $pdf->SetFont('dejavusans', '', 15);


        //if($fullname_string_count <= 23) //19
        //{
      //  $pdf->SetXY(29, 205); //define la altura date fecha. no existe algo defina exactamente la logica
        //}
        //if($fullname_string_count > 23) //19
        //{
        //	$pdf->SetXY(29, 205); //define la altura date fecha.
        //}



   // }

   // $qr_description = $fe_fin;
   // $pdf->writeHTML($qr_description, true, false, false, false, '');

    // set background image
    $vanasSignature = "../../images/diploma-vanas-signature.png";

    // Validation large for full name
    if($fullname_string_count >= 23){
      $pdf->Image($vanasSignature, 35, 216, 35, 35, '', '', '', false, 300, '', false, false, 0);
    }else{
      $pdf->Image($vanasSignature, 35, 218, 35, 35, '', '', '', false, 300, '', false, false, 0);
    }

    // set the starting point for the page content
    $pdf->setPageMark();
  }

  $nombre_archivo = 'diploma_'.$clave.'.pdf';

  //   set path
  #$path = './GenerateArchives/merge/tempPDFs/'.$nombre_archivo;
  $path = $_SERVER['DOCUMENT_ROOT'].'AD3M2SRC4/modules/reports/GenerateArchives/merge/tempPDFs/'.$nombre_archivo;
  #$path = __DIR__.'/GenerateArchives/merge/tempPDFs/'.$nombre_archivo;

  ob_clean();
  #Close and output PDF document
  $pdf->Output($path, 'F');

