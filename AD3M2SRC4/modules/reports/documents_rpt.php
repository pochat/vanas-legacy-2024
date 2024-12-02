<?php
  require('../../lib/general.inc.php');
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../../lib/tcpdf/tcpdf.php');
  
  $clave = RecibeParametroNumerico('c', True);
  $no_contrato = RecibeParametroNumerico('con', True);
  
  # Recupera datos de la sesion
  $Query  = "SELECT cl_sesion,fl_pais_campus,fg_stripe,convenience_fee ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE fl_sesion=$clave";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  $fl_pais_campus=$row[1];
  $fg_stripe=$row['fg_stripe'];
  $mn_convenience_fee=$row['convenience_fee'];
  
  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname ";
  $Query .= "FROM k_ses_app_frm_1 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  
  #Recupera datos adicionales a la forma 1 y del contrato del aplicante
  $Query  = "SELECT ds_firma_alumno, ds_contrato, ds_header, ds_footer ";
  $Query .= "FROM k_app_contrato ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  $Query .= "AND no_contrato=$no_contrato ";
  $row = RecuperaValor($Query);
  $ds_firma_alumno = $row[0];
  $ds_contrato = html_entity_decode(str_texto($row[1]));
  $ds_header = html_entity_decode(str_texto($row[2]));
  $ds_footer = html_entity_decode(str_texto($row[3]));
  
  if(!empty($ds_firma_alumno))
  {    
    $ds_encabezado = $ds_header;
    $ds_cuerpo = $ds_contrato;
    $ds_pie = $ds_footer;
  }
  else
  {

      if($fl_pais_campus==226){
          $ds_encabezado = html_entity_decode(genera_documento($clave, 1, False, False, 201));
          $ds_cuerpo = html_entity_decode(genera_documento($clave, 2, False, False, 201));
          $ds_pie = html_entity_decode(genera_documento($clave, 3, False, False, 201));
      }else{
          
          $ds_encabezado = html_entity_decode(genera_documento($clave, 1, False, False, $no_contrato));
          $ds_cuerpo = html_entity_decode(genera_documento($clave, 2, False, False, $no_contrato));
          $ds_pie = html_entity_decode(genera_documento($clave, 3, False, False, $no_contrato));
      }
  }

  if($fl_pais_campus==226){
      $nb_template = html_entity_decode(genera_documento($clave, 4, False, False, 201));
  }else{
      
      $nb_template = html_entity_decode(genera_documento($clave, 4, False, False, $no_contrato));
  }
  class MYPDF extends TCPDF 
  {
    public function Header() 
    {      
      global $ds_encabezado;
      $encabezado = '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td style="height:300px; width:100%; color:#037EB7; font-family:Tahoma; font-size:25px; text-align:left;">
          '.$ds_encabezado.'
        </td>
      </tr>
    </table>';
      $this->SetFont('helvetica', '', 10);
      $this->Cell(0, 5, $this->writeHTML($encabezado, true, false, true, false, ''), 0, true, 'J', 0, '', 0, false, 'M', 'B');
    }
    public function Footer() 
    {      
      global $ds_pie;      
      if (empty($this->pagegroups)) {
        $pagenumtxt = $this->getAliasNumPage().' / '.$this->getAliasNbPages();
      } else {
        $pagenumtxt = $this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
      }
      $this->SetY(-15);
      $this->SetX($this->original_lMargin);
      $this->Cell(0, 0, $this->writeHTML($ds_pie, true, false, true, false, ''), 0, 0, 'C');
      $this->SetY(-15);
      $this->Cell(0, 0, $pagenumtxt, 'T', 0, 'C');
    }
  }
  
  $htmlcontent = $ds_cuerpo;
  
  // create new PDF document
  $pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);

  // set default header data
  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
  
  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  //set margins
  $pdf->SetHeaderMargin(5);
  $pdf->SetFooterMargin(15);
  $pdf->SetTopMargin(50);

  //set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, 20);

  //set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

  //set some language-dependent strings
  $pdf->setLanguageArray($l); 

  $pdf->SetFont('dejavusans', '', 10); 
  
  $pdf->AddPage("P"); 
  
  #output the HTML content
  $pdf->writeHTML($htmlcontent, true, 0, true, 0); 
  
  $nombre_archivo = $nb_template.'_'.$no_contrato.'_'.$ds_fname.'_'.$ds_mname.'_'.$ds_lname.'.pdf';
  #Close and output PDF document
  $pdf->Output($nombre_archivo, 'D');

?>