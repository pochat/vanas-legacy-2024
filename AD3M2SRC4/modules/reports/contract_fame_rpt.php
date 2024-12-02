<?php
  require('../../lib/general.inc.php');
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../../lib/tcpdf/tcpdf.php');
  
  $clave = RecibeParametroNumerico('c', True);
  $p_usuario = RecibeParametroNumerico('u', True);
  
  # Recupera datos del instituto
  #Recuperamos datos generales del iNSTITUTO 
  $Query="SELECT I.ds_instituto,I.ds_codigo_pais,I.ds_codigo_area,I.no_telefono,fl_usuario_sp,I.fe_creacion  ";
  $Query.="FROM c_instituto I ";
  $Query.="JOIN c_pais P ON P.fl_pais=I.fl_pais ";
  $Query.="WHERE I.fl_instituto=$clave ";
  $row=RecuperaValor($Query);
  $nb_instituto=$row['ds_instituto'];
  $ds_codigo_pais=$row['ds_codigo_pais'];
  $ds_codigo_area=$row['ds_codigo_area'];
  $no_telefono=$row['no_telefono'];
  $fl_usuario=$row['fl_usuario_sp'];
  $fe_creacion=$row['fe_creacion'];
  
  $nb_template = str_uso_normal($nb_instituto);
  
  
  #Recuperamos el perfil en fame
  $Query="SELECT fl_perfil_sp,ds_nombres,ds_apaterno,fe_alta FROM c_usuario WHERE fl_usuario=$p_usuario ";
  $row=RecuperaValor($Query);
  $fl_perfil_fame=$row[0];
  $ds_nombs=str_uso_normal($row[1]);
  $ds_apaternos=str_uso_normal($row[2]);
  
   #Recuperamos datos del estufdiante
    if($fl_perfil_fame==PFL_ESTUDIANTE_SELF){
        $fl_usuario=$p_usuario;
        $nb_template = $ds_nombs."_".$ds_apaternos;
        $fe_creacion=$row[3];
    }
    
      #Recuperamos datos del teacher
    if($fl_perfil_fame==PFL_MAESTRO_SELF){
        $fl_usuario=$p_usuario;
        $nb_template = $ds_nombs."_".$ds_apaternos;
        $fe_creacion=$row[3];
    }
  
  #DAMOS FORMATO DE FECHA
  $fe_creacion=strtotime('+0 day',strtotime($fe_creacion));
  $fe_creacion= date('d_m_Y',$fe_creacion);

  
 
  
  
  
  $ds_encabezado=genera_ContratoFame($clave, 1, 102,$fl_usuario);
  $ds_cuerpo = genera_ContratoFame($clave, 2,102,$fl_usuario);
  $ds_pie = genera_ContratoFame($clave, 3,102,$fl_usuario);
  
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
  
  $nombre_archivo = $nb_template.'_'.$fe_creacion.'.pdf';
  #Close and output PDF document
  $pdf->Output($nombre_archivo, 'D');

?>