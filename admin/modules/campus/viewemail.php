<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../..//lib/tcpdf/tcpdf.php');
  
  # Recibimos parametros
  $fl_alumno_template = RecibeParametroNumerico('fl_alumno_template', True);
  $fl_sesion = RecibeParametroNumerico('fl_sesion', True);
  
  #Obtenemos los datos de la persona
  $Query  = "SELECT ds_fname, ds_mname, ds_lname ";
  $Query .= "FROM k_ses_app_frm_1 a, c_sesion b ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion AND fl_sesion='$fl_sesion'";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  $ds_nombre = $ds_fname." ".$ds_lname;
  
  
  $Query  = "SELECT DATE_FORMAT(s.fe_envio,'%d-%m-%Y'), DATE_FORMAT(s.fe_envio,'%M-%d-%Y'),s.ds_header, s.ds_body, s.ds_footer, ";
  $Query .= "(SELECT nb_template FROM k_template_doc i WHERE i.fl_template=s.fl_template), fl_template ";
  $Query .= "FROM k_alumno_template s WHERE s.fl_alumno_template=$fl_alumno_template";
  $row = RecuperaValor($Query);
  $Date = $row[0];
  $Date2 = $row[1];
  $ds_cuerpo = str_ascii(str_texto($row[3]));
  $nb_template = $row[5];
  $fl_template = $row[6];
  
  # Obtenemos los datos del template seleccinado
  if(empty($ds_cuerpo))
    $ds_cuerpo = genera_documento($fl_sesion, 2, $fl_template);
  
  // create new PDF document
  $pdf = new TCPDF('P', 'mm', 'LETTER', true);

  //do not show header or footer 
  $pdf->SetPrintHeader(false); 
  $pdf->SetPrintFooter(false);

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  //set margins
  $pdf->SetMargins(1.0, PDF_MARGIN_TOP, 1.0);
  $pdf->SetMargins(1.0, 1000, 1.0);
  $pdf->SetHeaderMargin(5);
  $pdf->SetFooterMargin(50);

  //set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, 5);

  //set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

  //set some language-dependent strings
  $pdf->setLanguageArray($l); 

  // ---------------------------------------------------------

  // set font
  $pdf->SetFont('dejavusans', '', 10); 
  
  # Datos del mensaje
  $htmlcontent = '
  <table>
    <tbody>
      <tr>
        <td>
          '.$ds_header.'
        </td>
      </tr>
      <tr>
        <td>
          '.$ds_cuerpo.'
        </td>
      </tr>
    </tbody>
  </table>';  
  
  class ConPies extends TCPDF {
    //header 
    function Header(){
      $fl_alumno_template = RecibeParametroNumerico('fl_alumno_template', True);
      $fl_sesion = RecibeParametroNumerico('fl_sesion', True);
      $row = RecuperaValor("SELECT ds_header, fl_template FROM k_alumno_template WHERE fl_alumno_template=$fl_alumno_template");
      $header = str_ascii(str_texto($row[0]));
      $fl_template = $row[1];
      if(empty($header))
        $header = genera_documento($fl_sesion, 1, $fl_template);
      $this->writeHTML($header, true, 0, true, 0); 
    }
    //footer
    function Footer(){
      $fl_alumno_template = RecibeParametroNumerico('fl_alumno_template', True);
      $fl_sesion = RecibeParametroNumerico('fl_sesion', True);
      $this->SetY(-20);
      $row = RecuperaValor("SELECT ds_footer, fl_template FROM k_alumno_template WHERE fl_alumno_template=$fl_alumno_template");
      $footer = str_ascii(str_texto($row[0]));
      $fl_template = $row[1];
      if(empty($footer))
        $footer = genera_documento($fl_sesion, 3, $fl_template);
      $this->writeHTML($footer, true, 0, true, 0); 
    }
  }
  
  // creamos un nuevo objeto usando la clase extendida ConPies 
  $pdf = new ConPies();
  $pdf->SetFont('times','',10);
  
  // add a page
  $pdf->AddPage("P"); 
  
  // output the HTML content
  $pdf->writeHTMLCell(180, 100, 10,30,$ds_cuerpo, 0, 0, false, true,'',true); 
  
  # Nombre del archivo
  $nombre_archivo = $ds_nombre.' '.$nb_template.' '.$Date.'.pdf';
  //Close and output PDF document
  $pdf->Output($nombre_archivo, 'D');
  
?>