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
$Query = "SELECT fl_usu_pro,P.nb_programa,P.fl_programa_sp,K.fe_entregado,fe_inicio_programa,fe_final_programa,fe_creacion,fl_maestro, no_creditos  
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
$fl_maestro = $row['fl_maestro'];
$no_creditos = $row['no_creditos'];

#Recuperamos el nombre del istituto:
$Query = "SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
$row = RecuperaValor($Query);
$nb_instituto = $row[0];




#Recuperamos quien es el maestro.
$Query = "SELECT U.ds_nombres,U.ds_apaterno FROM c_maestro_sp M 
            JOIN c_usuario U ON U.fl_usuario=M.fl_maestro_sp
            WHERE M.fl_maestro_sp=$fl_maestro ";
$row = RecuperaValor($Query);
$ds_nombre_teacher = $row[0];
$ds_apaterno_teacher = $row[1];


if (!empty($fe_inicio_curso)) {
  #Damos formato alas fechas.
  $fe_inicio_curso = strtotime('+0 day', strtotime($fe_inicio_curso));
  $fe_inicio_curso = date('Y-m-d', $fe_inicio_curso);
  $fe_inicio_curso = GeneraFormatoFecha($fe_inicio_curso);
}


if (!empty($fe_fin_curso)) {
  $fe_fin_curso = strtotime('+0 day', strtotime($fe_fin_curso));
  $fe_fin_curso = date('Y-m-d', $fe_fin_curso);
  $fe_fin_curso = GeneraFormatoFecha($fe_fin_curso);
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

/*
     * QR Code
     */
// set link
// $link_qr = 'campus.vanas.ca/StudentAccreditation.php?clave=' . $fl_sesion . '&type=2&data=' . $clave;
$link_qr = ObtenConfiguracion(116).'/fame/StudentAccreditation.php?type=2&data='.$fl_usuario.'&prgm='.$fl_programa_sp;

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
    global $ds_nombre_teacher;
    global $ds_apaterno_teacher;
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
          ' . ObtenEtiqueta(1613) . '
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
         ' . $ds_nombre_teacher . ' ' . $ds_apaterno_teacher . '
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          ' . ObtenEtiqueta(51) . '
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
          ' . ObtenEtiqueta(518) . '
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


#Recupermaos datos claves  del programa.
$Query = "SELECT  P.fl_programa_sp,P.fl_usuario_sp,C.nb_programa
            FROM k_usuario_programa P 
            LEFT JOIN c_programa_sp C ON C.fl_programa_sp=P.fl_programa_sp  
            WHERE P.fl_programa_sp=$fl_programa_sp AND fl_usuario_sp=$fl_usuario ";
$rs = EjecutaQuery($Query);

# Empezamos a mostrar datos
$htmlcontent = '<table border="0" cellpadding="0" cellspacing="0" width="100%">';


$htmlcontent .= '<tr>';
$htmlcontent .= '<td colspan="7" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> ' . $nb_programa . '<br> </td>';

$htmlcontent .= '</tr>';





$total_porcentaje = 0;
$contador = 0;
for ($tot = 0; $row = RecuperaRegistro($rs); $tot++) {
  $contador++;
  $fl_programa_sp = $row['fl_programa_sp'];
  $fl_usuario = $row['fl_usuario_sp'];
  $nb_programa = $row['nb_programa'];




  #Recuperamos las lecciones del programa
  /*  $Query2="SELECT DISTINCT fl_leccion_sp,no_semana,ds_titulo,nb_quiz,no_valor_quiz  FROM 
                    c_leccion_sp WHERE fl_programa_sp=$fl_programa_sp ";
           $rs2 = EjecutaQuery($Query2);
           */
  #1.verificamos cuantas lecciones existen en esete programa(CUANDO EXISTE FL_PROMEDIO QUIERE DECIR QUE YA ESTA CALIFICADA)
  $Query2 = "SELECT A.fl_alumno,A.fl_leccion_sp,A.fl_promedio_semana,C.nb_programa,B.ds_titulo,B.no_valor_rubric,B.no_semana,D.cl_calificacion,D.no_equivalencia 
						    FROM k_entrega_semanal_sp A
						    JOIN c_leccion_sp B  ON B.fl_leccion_sp=A.fl_leccion_sp 
						    JOIN c_programa_sp C ON C.fl_programa_sp=B.fl_programa_sp
						    JOIN c_calificacion_sp D ON D.fl_calificacion=A.fl_promedio_semana
						    WHERE A.fl_alumno=$fl_usuario AND C.fl_programa_sp=$fl_programa_sp AND fl_promedio_semana IS NOT NULL ORDER BY B.no_semana ASC ";
  $rs2 = EjecutaQuery($Query2);

  $contador2 = 0;
  for ($tot2 = 0; $row2 = RecuperaRegistro($rs2); $tot2++) {
    $contador2++;
    $fl_leccion_sp = $row2['fl_leccion_sp'];
    $no_session = $row2['no_semana'];
    $nb_leccion = $row2['ds_titulo'];
    $grade = $row2['cl_calificacion'];

    #Recuperamos la calificacion asignada por el teacher (sin calculos ni equivalencias.)
    $Query2 = "SELECT no_calificacion FROM k_calificacion_teacher WHERE fl_alumno=$fl_usuario and fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp ";
    $row2 = RecuperaValor($Query2);
    $no_calificacion = $row2['no_calificacion'];

    #Recupermaos la fecha de utima modificacion/creacion
    $Query3 = "SELECT fe_modificacion 
														FROM c_com_criterio_teacher 
														WHERE fl_leccion_sp=$fl_leccion_sp AND fl_alumno=$fl_usuario AND fl_programa_sp=$fl_programa_sp  AND fg_com_final='1' ";

    $row3 = RecuperaValor($Query3);
    $fe_modificacion = GeneraFormatoFecha($row3[0]);

    $sum_porcentaje += $no_calificacion;


    #Recuperamos el nombre de la lecccion y titulos, solo se pinta una sola vez.
    if ($contador2 == 1) {

      $htmlcontent .= '<tr>';
      $htmlcontent .= '<td style="width:15%; height:15px;  color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1605) . '  </td>';
      $htmlcontent .= '<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1606) . '</td>';
      $htmlcontent .= '<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1612) . '  </td>';

      $htmlcontent .= '<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1610) . '  </td>';
      $htmlcontent .= '<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">' . ObtenEtiqueta(1611) . '  </td>';

      $htmlcontent .= '</tr>';
    }


    #para colorer celdas
    $valor_contador = $contador2 / 2;
    if (is_int($valor_contador))
      $bgcolor = "";
    else
      $bgcolor = "";


    if ($nb_leccion_actual <> $nb_leccion) {

      $htmlcontent .= '<tr>';
      $htmlcontent .= '<td style="width:15%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> ' . $fe_modificacion . ' </td>';
      $htmlcontent .= '<td style="width:15%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . $no_session . ' </td>';
      $htmlcontent .= '<td style="width:30%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> ' . $nb_leccion . '  </td>';
      $htmlcontent .= '<td style="width:20%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . $grade . '  </td>';
      $htmlcontent .= '<td style="width:20%; height:15px;background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . $no_calificacion . ' %</td>';
      $htmlcontent .= '</tr>';
    }



    $nb_leccion_actual = $nb_leccion;
  }
}



$total = $sum_porcentaje / $contador2;


#Buscamos en que rangose encuentra y se recuepra el grado final.
$Query = "SELECT cl_calificacion,no_min,no_max,no_equivalencia FROM c_calificacion_sp WHERE 1=1 ";
$rs4 = EjecutaQuery($Query);
$tot_registros = CuentaRegistros($rs4);
for ($i = 1; $row4 = RecuperaRegistro($rs4); $i++) {
  $no_min = $row4['no_min'];
  $no_max = $row4['no_max'];


  if (($total >= $no_min) && ($total <= $no_max)) {

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
$htmlcontent .= '<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;">  </td>';

$htmlcontent .= '<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">' . ObtenEtiqueta(524) . ':  </td>';
$htmlcontent .= '<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . $grade_final . '  </td>';
$htmlcontent .= '<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> ' . number_format($total) . ' %</td>';


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
