<?php
require('../../lib/general.inc.php');
require_once('../../lib/tcpdf/config/lang/eng.php');
require_once('../../lib/tcpdf/tcpdf.php');

$clave = RecibeParametroNumerico('c', True); #id del tabla k_usuario_programa
$fl_usuario = RecibeParametroNumerico('u', True);
$fl_instituto = RecibeParametroNumerico('i', True);


#Recuperamos datos del studiante
$Query = "SELECT ds_nombres,ds_apaterno,ds_email, ";
$Query .= "fe_nacimiento, ";
$Query .= "ds_login FROM c_usuario WHERE fl_usuario=$fl_usuario ";
$row = RecuperaValor($Query);
$ds_nombres = $row['ds_nombres'];
$ds_apaterno = $row['ds_apaterno'];
$ds_email = $row['ds_email'];
$fe_nacimiento = $row['fe_nacimiento'];
$ds_login = $row['ds_login'];


if ($fe_nacimiento) {


  $fe_nacimiento = strtotime('+0 day', strtotime($fe_nacimiento));
  $fe_nacimiento = date('Y-m-d', $fe_nacimiento);
  #DAMOS FORMATO DIA,MES, A�O.
  $date = date_create($fe_nacimiento);
  $fe_nacimiento = date_format($date, 'F j, Y');
}

#Recuperamos datos del curso/Programa.
$Query = "SELECT fl_usu_pro,P.nb_programa,P.fl_programa_sp,K.fe_entregado,fe_inicio_programa,fe_final_programa,fe_creacion,no_creditos  
            FROM k_usuario_programa K
            JOIN c_programa_sp P ON P.fl_programa_sp=K.fl_programa_sp ";
$Query .= "WHERE fl_usu_pro=$clave ";
$row = RecuperaValor($Query);
$fl_usuario_pro = $row['fl_usu_pro'];
$nb_programa = str_texto($row['nb_programa']);
$fl_programa_sp = $row['fl_programa_sp'];
$fe_entregado = $row['fe_entregado'];
$fe_inicio_curso = $row['fe_inicio_programa'];
$fe_fin_curso = $row['fe_final_programa'];
$no_creditos = $row['no_creditos'];


if(!$fe_fin_curso){
  $Query = "SELECT fe_periodo_final 
              FROM k_current_plan 
              WHERE fl_instituto =$fl_instituto";
  $row = RecuperaValor($Query);
  $fe_fin_curso = date('F j, Y', strtotime($row[0]));
  }

if ($fe_inicio_curso) {
  #Damos formato alas fechas.
  $fe_inicio_curso = strtotime('+0 day', strtotime($fe_inicio_curso));
  $fe_inicio_curso = date('Y-m-d', $fe_inicio_curso);
  $fe_inicio_curso = GeneraFormatoFecha($fe_inicio_curso);
}

if ($fe_fin_curso) {
  $fe_fin_curso = strtotime('+0 day', strtotime($fe_fin_curso));
  $fe_fin_curso = date('Y-m-d', $fe_fin_curso);
  $fe_fin_curso = GeneraFormatoFecha($fe_fin_curso);
}else{
  
}


$fe_entregado = strtotime('+0 day', strtotime($fe_entregado));
$fe_entregado = date('Y-m-d', $fe_entregado);


#Obtenemos fecha actual :
$Query = "Select CURDATE() ";
$row = RecuperaValor($Query);
$fe_actual = str_texto($row[0]);
$fe_actual = strtotime('+0 day', strtotime($fe_actual));
$fe_actual = date('Y-m-d', $fe_actual);
$fe_emision = GeneraFormatoFecha($fe_actual);

$left_footer = ObtenEtiqueta(1614);
$right_footer = ObtenEtiqueta(1615);

#Recuperamos el nombre del istituto:
$Query = "SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
$row = RecuperaValor($Query);
$nb_instituto = $row[0];

/*
     * QR Code
     */
// set link
$link_qr = ObtenConfiguracion(116) . '/fame/StudentAccreditation.php?type=3&data=' . $fl_usuario . '&prgm=' . $fl_programa_sp;

// set style for barcode
$style = array(
  'border' => 0,
  'padding' => 0,
  'fgcolor' => array(0, 0, 0),
  'bgcolor' => false, //array(255,255,255)
  'module_width' => 1, // width of a single module in points
  'module_height' => 1 // height of a single module in points
);

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{
  //Page header
  public function Header()
  {

    global $clave;
    global $ds_login;
    global $ds_nombres;
    global $ds_apaterno;
    global $ds_email;
    global $fe_nacimiento;
    global $nb_programa;
    global $fe_inicio_curso;
    global $fe_fin_curso;
    global $fe_emision;
    global $left_footer;
    global $right_footer;
    global $no_creditos;
    global $nb_instituto;
    global $link_qr;
    global $style;


    // get the current page break margin
    $bMargin = $this->getBreakMargin();
    // get current auto-page-break mode
    $auto_page_break = $this->AutoPageBreak;
    // disable auto-page-break
    $this->SetAutoPageBreak(false, 0);
    // set bacground image
    $img_file = "../../images/Protected_paper_vanas_official_transcript.jpg";
    $this->Image($img_file, 0, 0, 216, 280, '', '', '', false, 300, '', false, false, 0);
    // restore auto-page-break status
    $this->SetAutoPageBreak($auto_page_break, $bMargin);
    // set the starting point for the page content
    $this->setPageMark();

    $encabezado = '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td style="width:100%;">
         &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . ObtenEtiqueta(510) . ':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . $ds_nombres . ' ' . $ds_apaterno . '
        </td>
        <td rowspan="5" style="width:40%; color:#037EB7; font-family:Tahoma; font-size:32px; text-align:right;">
        <img src="../../images/VANAS_transcripts_logo_2021.png" />
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . ObtenEtiqueta(511) . ':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . $ds_login . '
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . ObtenEtiqueta(120) . ':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . $fe_nacimiento . '
        </td>
      </tr>
      
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . ObtenEtiqueta(512) . ':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . $nb_programa . '
        </td>
      </tr>
      
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . ObtenEtiqueta(60) . ':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . $fe_inicio_curso . '
        </td>
      </tr>
      
       <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . ObtenEtiqueta(513) . ':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . $fe_fin_curso . '
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          ' . ObtenEtiqueta(520) . '
        </td>
      </tr>
      
       <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . ObtenEtiqueta(515) . ':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
           ' . $fe_emision . '
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          ' . ObtenEtiqueta(516) . '
        </td>
      </tr>
      
      
       <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . ObtenEtiqueta(1639) . ':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
         ' . $no_creditos . '
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          ' . ObtenEtiqueta(517) . '
        </td>
      </tr>
      
       <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          ' . ObtenEtiqueta(1693) . '
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
         ' . $nb_instituto . '
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          ' . ObtenEtiqueta(518) . '
        </td>
      </tr>
      
       <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
         
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          ' . ObtenEtiqueta(519) . '
        </td>
      </tr>
     
    </table><br/><br/>';






    $this->SetFont('helvetica', '', 10);
    $this->Cell(0, 5, $this->writeHTML($encabezado, true, false, true, false, ''), 0, true, 'J', 0, '', 0, false, 'M', 'B');

    $this->SetFont('helvetica', 'B', 10);
    $this->Cell(0, 5, 'OFFICIAL TRANSCRIPT                                 PAGE ' .
      $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 'B', true, 'J', 0, '', 0, false, 'M', 'B');
    $this->Cell(0, 8, '', '', true, 'center', 0, '', 0, false, 'M', 'B');

    // QRCODE,L : QR-CODE Low error correction
    $this->write2DBarcode('' . $link_qr . '', 'QRCODE,L', 124, 8, 20, 20, $style, 'N');

    $this->SetFont('dejavusans', '', 6);
    $this->SetXY(120, 26);

    $qr_description = 'Verify Academic Credential';
    $this->writeHTML($qr_description, true, false, false, false, '');
  }

  // Page footer
  public function Footer()
  {
    global $left_footer;
    global $right_footer;

    // get the current page break margin
    $bMargin = $this->getBreakMargin();
    // get current auto-page-break mode
    $auto_page_break = $this->AutoPageBreak;
    // disable auto-page-break
    $this->SetAutoPageBreak(false, 0);
    // set bacground image
    $vanasSealPath = "../../images/vanas_seal.png";
    $this->Image($vanasSealPath, 167, 232, 35, 35, '', '', '', false, 300, '', false, false, 0);
    // restore auto-page-break status
    $this->SetAutoPageBreak($auto_page_break, $bMargin);
    // set the starting point for the page content
    $this->setPageMark();

    // signature
    $vanasSignature = "../../images/diploma-vanas-signature.png";
    $this->Image($vanasSignature, 167, 236, 35, 35, '', '', '', false, 300, '', false, false, 0);



    $left_column = '' . $left_footer . '';
    $right_column = '' . $right_footer . '';
    // Position at 15 mm from bottom
    $this->SetY(-20);
    // Set font
    $this->SetFont('helvetica', '', 9);
    $this->writeHTMLCell(110, '', '', '', $left_column, 0, 0, 0, true, 'J', true);
    $this->writeHTMLCell(40, '', '', '', '', 0, 0, 0, true, 'J', true);
    $this->SetFont('helvetica', '', 10);
    $this->writeHTMLCell(0, '', '', '', $right_column, 'T', 0, 0, true, 'C', true);
  }
}



# Empezamos a mostrar datos
$htmlcontent = '<table border="0" cellpadding="0" cellspacing="0" width="100%">';


$htmlcontent .= '<tr>';
$htmlcontent .= '<td colspan="7" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> ' . $nb_programa . '<br> </td>';

$htmlcontent .= '</tr>';

$htmlcontent .= '<tr>';
$htmlcontent .= '<td style="width:15%; height:15px;  color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1605) . ' </td>';
$htmlcontent .= '<td style="width:8%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1606) . ' </td>';
$htmlcontent .= '<td style="width:27%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1607) . ' </td>';
$htmlcontent .= '<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1608) . '  </td>';
$htmlcontent .= '<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1609) . '  </td>';
$htmlcontent .= '<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1610) . ' </td>';
$htmlcontent .= '<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1611) . '  </td>';

$htmlcontent .= '</tr>';







$total_porcentaje = 0;
$no_total_calif = 0;
$division = 0;
#Recuperamos todas las lecciones del programa
$Query2 = "SELECT fl_leccion_sp,no_semana,ds_titulo,nb_quiz,no_valor_quiz  FROM 
                    c_leccion_sp WHERE fl_programa_sp=$fl_programa_sp AND nb_quiz IS NOT NULL ";
$rs2 = EjecutaQuery($Query2);
$contador2 = 0;
for ($tot2 = 0; $row2 = RecuperaRegistro($rs2); $tot2++) {
  $contador2++;
  $fl_leccion_sp = $row2['fl_leccion_sp'];
  $no_session = $row2['no_semana'];
  //$nb_leccion=$row2['ds_titulo'];
  $nb_quiz = $row2['nb_quiz'];



  #Recuperamos los quizes por cada leccion del programa.
  $Query3 = "SELECT no_intento,no_calificacion,cl_calificacion,fe_final 
                             FROM k_quiz_calif_final 
                             WHERE fl_leccion_sp=$fl_leccion_sp AND fl_usuario=$fl_usuario ORDER BY no_intento ASC ";
  $rs3 = EjecutaQuery($Query3);
  $tot_reg = CuentaRegistros($rs3);
  $contador3 = 0;

  for ($tot3 = 0; $row3 = RecuperaRegistro($rs3); $tot3++) {
    $fe_termino_quiz = GeneraFormatoFecha($row3['fe_final']);
    $attemp = $row3['no_intento'];
    $grade = $row3['cl_calificacion'];

    $division++;

    $contador3++;

    if ($tot_reg == $contador3) {
      $no_weight = $row2['no_valor_quiz'] . "%";
      $porcentaje = $row3['no_calificacion'] . "%";

      $total_porcentaje += $row3['no_calificacion'];
      $total_registros_a_dividir++;
    } else {

      $no_weight = null;
      $porcentaje = null;
      $no_porcentaje_sumar = null;
    }





    #para colorer celdas
    $valor_contador = $division / 2;
    if (is_int($valor_contador))
      $bgcolor = "";
    else
      $bgcolor = "";



    $htmlcontent .= '<tr>';
    $htmlcontent .= '<td style="width:15%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> ' . $fe_termino_quiz . ' </td>';
    $htmlcontent .= '<td style="width:8%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . $no_session . ' </td>';
    $htmlcontent .= '<td style="width:27%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> ' . $nb_quiz . '  </td>';
    $htmlcontent .= '<td style="width:10%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . $attemp . ' </td>';
    $htmlcontent .= '<td style="width:10%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . $no_weight . ' </td>';
    $htmlcontent .= '<td style="width:15%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . $grade . '  </td>';
    $htmlcontent .= '<td style="width:15%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . $porcentaje . ' </td>';


    $htmlcontent .= '</tr>';
  }
}



#Se realiza calculo de final promedio. y su equivalencia 
$porcentaje_final = $total_porcentaje / $total_registros_a_dividir;


#Obtenemos el equivalente.
$Query = "SELECT cl_calificacion,no_min,no_max,no_equivalencia FROM c_calificacion_sp WHERE 1=1 ";
$rs4 = EjecutaQuery($Query);
$tot_registros = CuentaRegistros($rs4);
for ($i = 1; $row4 = RecuperaRegistro($rs4); $i++) {
  $no_min = $row4['no_min'];
  $no_max = $row4['no_max'];
  if (($porcentaje_final >= $no_min) && ($porcentaje_final <= $no_max)) {
    $grade_final = $row4['cl_calificacion'];
  }
}



$htmlcontent .= '
                                  <tr>
                                    <td style="width:100%; height:10px;">
                                      &nbsp;
                                    </td>
                                  </tr>';
$htmlcontent .= '<tr>';
$htmlcontent .= '<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">  </td>';
$htmlcontent .= '<td style="width:8%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;">  </td>';
$htmlcontent .= '<td style="width:27%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;">  </td>';
$htmlcontent .= '<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;">  </td>';
$htmlcontent .= '<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;">' . ObtenEtiqueta(524) . ':  </td>';
$htmlcontent .= '<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . $grade_final . '  </td>';
$htmlcontent .= '<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . number_format($porcentaje_final) . ' %</td>';


$htmlcontent .= '</tr>';










$htmlcontent .= '

      <tr>
        <td style="width:100%; height:5px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:10px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center; border-top:1px solid black;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:40px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">
          ' . ObtenEtiqueta(525) . '
        </td>
      </tr>
      <tr>
        <td style="width:35%; height:120px; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          ' . ObtenEtiqueta(536) . '<br/>' . $fe_fin_curso . '
        </td>
        <td style="width:30%; height:120px; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          &nbsp;
        </td>
        <td style="width:35%; height:120px; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          ' . ObtenEtiqueta(537) . '<br/>' . $fe_emision . '
        </td>
      </tr>
    </table>
  ';














// create new PDF document
$pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

$ownerPassword = ObtenConfiguracion(164);
$pdf->SetProtection(array('modify'), '', $ownerPassword, 0, null);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(5);
$pdf->SetTopMargin(60);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage("P");

// output the HTML content
$pdf->writeHTML($htmlcontent, true, 0, true, 0);





$nombre_archivo = 'Transcript_' . $nb_programa . '_' . $ds_nombres . '_' . $ds_apaterno . '.pdf';
//Close and output PDF document
$pdf->Output($nombre_archivo, 'D');
