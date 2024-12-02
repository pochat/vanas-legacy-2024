<?php
  require('../../lib/general.inc.php');
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../../lib/tcpdf/tcpdf.php');

  $fl_instituto=$_GET['i'];
  #Substraemos el fl_instituto
  $cadena=explode("_",$fl_instituto);
  $fl_instituto=$cadena[0];

  $link_qr=ObtenConfiguracion(116)."/fame/accreditation.php?z=".$_GET['i']."&i=".$_GET['i']."";
      
  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual=strtotime('+0 day',strtotime($fe_actual));
  $fe_actual= date('Y-m-d',$fe_actual);
  $fe_emision=GeneraFormatoFecha($fe_actual);
    
  #Recupermos Datois generale de la escuela.
  $Query="SELECT * FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $nb_instituto=$row['ds_instituto'];
  $ds_codigo_pais=$row['ds_codigo_pais'];
  $ds_codigo_area=$row['ds_codigo_area'];
  $no_telefono=$row['no_telefono'];
  $ds_foto=$row['ds_foto'];
  $fl_usuario=$row['fl_usuario_sp'];
  $fe_creacion=$row['fe_creacion'];
    
  $fe_creacion=strtotime('+0 day',strtotime($fe_creacion));
  $fe_creacion= date('m/Y',$fe_creacion);
    
  $Query="SELECT * FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fe_final_periodo=GeneraFormatoFecha($row['fe_periodo_final']);
  



  #Recuperamos el template.
  $template  =GeneraTemplate(1,189);
  $template .=GeneraTemplate(2,189);
  $template .=GeneraTemplate(3,189);

  #Reemplazamos valores.
  $template =str_replace("#nb_instituto#",$nb_instituto,$template);
  $template =str_replace("#fe_creacion#",$fe_creacion,$template);
  $template =str_replace("#fe_final_plan#",$fe_final_periodo,$template);
 


    
    
  class MYPDF extends TCPDF {
      //Page header
      public function Header() {
          // get the current page break margin
          $bMargin = $this->getBreakMargin();
          // get current auto-page-break mode
          $auto_page_break = $this->AutoPageBreak;
          // disable auto-page-break
          $this->SetAutoPageBreak(false, 0);
          // set bacground image
          $img_file = '../../../images/06-FAME-Cert-BG-Squares.png';
          $this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

          $img_logo='../../../images/07-FAME-logo-accreditation.png';
          $this->Image($img_logo, 154.2, 15, 45, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);

          // restore auto-page-break status
          $this->SetAutoPageBreak($auto_page_break, $bMargin);
          // set the starting point for the page content
          $this->setPageMark();
      }


      // Page footer
      public function Footer() {
          global $link_qr;
          // set style for barcode
          $style = array(
              'border' => 0,
              'padding' => 0,
              'fgcolor' => array(0,0,0),
              'bgcolor' => false, //array(255,255,255)
              'module_width' => 1, // width of a single module in points
              'module_height' => 1 // height of a single module in points
          );

          // QRCODE,L : QR-CODE Low error correction
          $this->write2DBarcode(''.$link_qr.'', 'QRCODE,L', 10, 265, 20, 20, $style, 'N');

          // Page number
          $this->Cell(0, 10, '', 0, false, "C", 0, "", 0, false, "T", "M");
          
      }

  }

  // create new PDF document
  $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

  // set document information
  $pdf->SetCreator(PDF_CREATOR);
  $pdf->SetAuthor('Nicola Asuni');
  $pdf->SetTitle('TCPDF Example 051');
  $pdf->SetSubject('TCPDF Tutorial');
  $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

  // set header and footer fonts
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  // set margins
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(0);
  $pdf->SetFooterMargin(0);

  // remove default footer
 // $pdf->setPrintFooter(false);

  // set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

  // set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

  // add a page
  $pdf->AddPage();

  

  # Empezamos a mostrar datos
  $htmlcontent = '<table border="0" cellpadding="1" cellspacing="0" width="100%">'; 

  
  #Presentamos totale y tax.
  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:15px; color:#000000; font-family:Arial; font-size:135px;  text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:65px;  text-align:center;"> </td>';
  $htmlcontent .= '</tr>';

  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%; height:5px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:5px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:30%; height:5px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .= '</tr>';

  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:15px; color:#000000; font-family:Arial; font-size:135px;  text-align:center;"> Certificate</td>';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:65px;  text-align:center;"> </td>';
  $htmlcontent .= '</tr>';

  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:15px; color:#000000; font-family:Arial; font-size:45px;  text-align:center;">Proudly Presented to</td>';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:65px;  text-align:center;"> </td>';
  $htmlcontent .= '</tr>';

  $htmlcontent .= '<tr >';
  $htmlcontent .='<td colspan="3" style=" height:15px; color:#000000; font-family:Arial; font-size:145px;  text-align:center;">'.$nb_instituto.'</td>';
  $htmlcontent .= '</tr>';

  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:15px; color:#000000; font-family:Arial; font-size:65px;  text-align:center;">FAME Partner School</td>';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:65px;  text-align:center;"> </td>';
  $htmlcontent .= '</tr>';

  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:15px; color:#000000; font-family:Arial; font-size:135px;  text-align:center;"></td>';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:65px;  text-align:center;"> </td>';
  $htmlcontent .= '</tr>';
  
  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:15px; color:#000000; font-family:Arial; font-size:50px;  text-align:center;">MEMBER SINCE '.$fe_creacion.'</td>';
  $htmlcontent .='<td rowspan="3"  style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .= '</tr>';

   $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:15px; color:#000000; font-family:Arial; font-size:50px;  text-align:center;">Expires on: '.$fe_final_periodo.'</td>';
  $htmlcontent .='<td rowspan="3"  style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .= '</tr>';

  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:15px; color:#000000; font-family:Arial; font-size:50px;  text-align:center;"><img src="gratd.JPG" style="height:60px;" ></td>';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .= '</tr>';

 
  $htmlcontent .= '</table>';

  /*

  $htmlcontent .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%;  color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; color:#000000; font-family:Arial; font-size:55px;  text-align:center;"></td>';
  $htmlcontent .='<td style=" width:30%;  color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .= '</tr>';
  
  
  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%;color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; color:#000000; font-family:Arial; font-size:55px;  text-align:center;"></td>';
  $htmlcontent .='<td style=" width:30%; color:#000000; font-family:Arial;   text-align:right;"> </td>';
  $htmlcontent .= '</tr>';

  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%;color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; color:#000000; font-family:Arial; font-size:55px;  text-align:center;"></td>';
  $htmlcontent .='<td style=" width:30%; color:#000000; font-family:Arial;   text-align:right;"> </td>';
  $htmlcontent .= '</tr>';

  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%;color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:40px; color:#000000; font-family:Arial; font-size:55px; color:#2fabff; text-align:center;">____________________</td>';
  $htmlcontent .='<td style=" width:30%; color:#000000; font-family:Arial;   text-align:right;"> </td>';
  $htmlcontent .= '</tr>';

  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%;color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; color:#000000; font-family:Arial; font-size:45px;  text-align:center;"> Founder and CEO</td>';
  $htmlcontent .='<td style=" width:30%; color:#000000; font-family:Arial;   text-align:right;"> </td>';
  $htmlcontent .= '</tr>';
  

  $htmlcontent .= '</table>';
  */

  //$htmlcontent="";
  //$htmlcontent.=$template;


 // $pdf->Image('../../../images/03-FAME-Cert-square-left.png', 15, 10, 100, '', '', 'http://www.tcpdf.org', '', false, 300);

 // $pdf->Image('../../../images/02-FAME-Cert-square-right.png', 135, 188, 100, '', '', 'http://www.tcpdf.org', '', false, 300);

  $htmlcontent = '<table border="0" cellpadding="1" cellspacing="0" width="100%">'; 
  $htmlcontent .= '<tr >';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .='<td style=" width:40%; height:15px; color:#000000; font-family:Arial; font-size:50px;  text-align:center;"></td>';
  $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial;   text-align:center;"> </td>';
  $htmlcontent .= '</tr>';

  
  $htmlcontent .= '</table>';


  $htmlcontent .=$template;




  $pdf->SetTopMargin(69);
  $pdf->writeHTML($htmlcontent, true, false, true, false, '');


 
 
 
  //Close and output PDF document
  //$pdf->Output('example_051.pdf', 'I');
  $pdf->Output("Accreditation_".$nb_instituto.".pdf", 'D');








  #Genera Template 
  function GeneraTemplate($opc, $fl_template = 0){
      # Recupera datos del template del documento
      switch ($opc) {
          case 1:
              $campo = "ds_encabezado";
              break;
          case 2:
              $campo = "ds_cuerpo";
              break;
          case 3:
              $campo = "ds_pie";
              break;
          case 4:
              $campo = "nb_template";
              break;
      }

      # Obtenemos la informacion del template header body or footer
      $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
      $row = RecuperaValor($Query1);

      $cadena = $row[0];
      # Sustituye caracteres especiales
      $cadena = $row[0];
      $cadena = str_replace("&lt;", "<", $cadena);
      $cadena = str_replace("&gt;", ">", $cadena);
      $cadena = str_replace("&quot;", "\"", $cadena);
      $cadena = str_replace("&#039;", "'", $cadena);
      $cadena = str_replace("&#061;", "=", $cadena);
      $cadena = str_replace("&nbsp;", " ", $cadena);
      $cadena = html_entity_decode($cadena);
      return $cadena;

  }
?>