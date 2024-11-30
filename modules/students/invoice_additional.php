<?php
# Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  require_once('../../AD3M2SRC4/lib/tcpdf/config/lang/eng.php');
  require_once('../../AD3M2SRC4/lib/tcpdf/tcpdf.php');


  $fl_sesion = RecibeParametroNumerico('fl_sesion', True);

  $row = RecuperaValor("SELECT cl_sesion, fg_inscrito,id_alumno FROM c_sesion WHERE fl_sesion=$fl_sesion");
  $cl_sesion = str_texto($row[0]);
  $fg_inscrito = $row[1];

  $row = RecuperaValor("SELECT fl_usuario, nb_grupo FROM c_usuario a, k_alumno_grupo b, c_grupo c WHERE cl_sesion='$cl_sesion' AND a.fl_usuario=b.fl_alumno AND b.fl_grupo=c.fl_grupo");
  $fl_alumno = $row[0];

  if(empty($fl_alumno)){
      $rowalu= RecuperaValor("SELECT fl_usuario FROM c_usuario WHERE cl_sesion='$cl_sesion'");
      $fl_alumno = $rowalu[0];
  }

  #Verifica si existe el id de alumno
  $Query="SELECT ds_login FROM c_usuario where fl_usuario=$fl_alumno ";
  $row=RecuperaValor($Query);
  $ds_id=$row['ds_login'];

  if(empty($ds_id)){
      #en caso de venir vacio se asigna el id e sesion.
      $Query="SELECT id_alumno FROM c_sesion where cl_sesion='$cl_sesion' ";
      $row=RecuperaValor($Query);
      $ds_id=$row['id_alumno'];
  }

  # Recupera datos del alumno: forma 1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname, ".ConsultaFechaBD('fe_ultmod',FMT_FECHA).",fl_programa ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa AND cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  $fl_programa = $row['fl_programa'];

  #damos a conocer si el estudiantes es de canada
  $Query  ="SELECT ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, nb_pais, ds_add_country ";
  $Query .="FROM k_ses_app_frm_1 a, c_pais b WHERE cl_sesion='$cl_sesion' AND a.ds_add_country=b.fl_pais ";
  $row = RecuperaValor($Query);
  $ds_add_number = $row[0];
  $ds_add_street = $row[1];
  $ds_add_city = $row[2];
  $ds_add_state = $row[3];
  $ds_add_zip = $row[4];
  $ds_add_country = $row[5];
  $cl_pais = $row[6];
if ($cl_pais == 38) {
    $rowstate = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$ds_add_state");
    $ds_add_state = $rowstate[0];
    if (empty($ds_add_state))
        $ds_add_state = $row[3];
}

  $Query="SELECT CASE b.cl_metodo_pago WHEN 1 THEN ' ' WHEN 2 THEN 'Payment Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' END cl_metodo_pago
          FROM vanas_prod.c_sesion a
          JOIN vanas_prod.k_methods_payments b ON b.cl_metodo_pago=a.cl_metodo_pago
          WHERE cl_sesion='$cl_sesion' ";
  $row=RecuperaValor($Query);
  $cl_metodo_pago=$row[0];



  # Recupera el tipo de pago para el curso
    $Query  = "SELECT fg_opcion_pago, ds_firma_alumno, DATE_FORMAT(fe_firma, '%b %d, %Y') fe_firma,mn_costs,ds_costs,mn_discount,ds_discount,fg_international,fg_payment FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
    $row = RecuperaValor($Query);
    $fg_opcion_pago = $row[0];
    $ds_firma_alumno = $row[1];
    $Date = $row[2];
    $mn_costs=!empty($row[3])?$row[3]:0;
    $ds_costs=$row[4];
    $mn_discount = !empty($row[5])?$row[5]:0;
    $ds_discount = $row[6];
    $fg_international = !empty($row['fg_international']) ? $row['fg_international'] : 0;
    $fg_payment = $row['fg_payment'];

    $ds_cheque=null;


if ($cl_pais == 38) {

    //check flag taxes
    $Queryt = "SELECT fg_taxes,fg_taxes_internacional,fg_taxes_combined,fg_taxes_internacional_combined FROM k_programa_costos WHERE fl_programa=$fl_programa ";
    $rowt = RecuperaValor($Queryt);
    $fg_taxes = $rowt['fg_taxes'];
    $fg_taxes_internacional = $rowt['fg_taxes_internacional'];
    $fg_taxes_combined = $rowt['fg_taxes_combined'];
    $fg_taxes_internacional_combined = $rowt['fg_taxes_internacional_combined'];


    if (!empty($fg_international)) {

        if ($fg_payment == "O") {

            $applyTax = $fg_taxes_internacional;

        }
        if ($fg_payment == "C") {

            $applyTax = $fg_taxes_internacional_combined;
        }
    } else {
        if ($fg_payment == "O") {
            $applyTax = $fg_taxes;

        }
        if ($fg_payment == "C") {

            $applyTax = $fg_taxes_combined;
        }




    }



}




  // create new PDF document
  $pdf = new TCPDF('P', 'mm', 'LETTER', true);

  //do not show header or footer
  $pdf->SetPrintHeader(false);
  $pdf->SetPrintFooter(false);

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  //set margins
  $pdf->SetMargins(1.5, PDF_MARGIN_TOP, 1.5);
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

  #encabezado de los recibos
  $htmlcontent = '
    <table style="width:350px;" border="0">
      <tr>
        <td width="100%">
          <img src="../../images/Vanas_doc_logo.jpg" border="0" />
        </td>
      </tr>
    </table>
    <br />
    <table style="width:540px;">
      <tr>
        <td width="52%" rowspan="3">
          <div style="font-size:12px;">'.ObtenEtiqueta(516).'<br />
            '.ObtenEtiqueta(517).','.ObtenEtiqueta(518).' <br />
           '.ObtenEtiqueta(519).' <br />
            '.MAIL_FROM.'
          </div>
        </td>
        <td width="18%">&nbsp;</td>
        <td colspan="2" width="30%" style="font-size:16px; font-weight:bold; text-align: center">Sales Receipt</td>
      </tr>
      <tr style="text-align:center;">
        <td width="18%">&nbsp;</td>
        <td width="15%" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  font-size:12px;">Date</td>
        <td width="15%" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  font-size:12px;">Sale No.</td>
      </tr>
      <tr style="text-align:center;">
        <td width="18%">&nbsp;</td>
        <td width="15%" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid; font-size:12px; ">'.$Date.'</td>
        <td width="15%" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  font-size:12px;">0</td>
      </tr>
    </table>
    <br /><br />
    <table border="0" style="width:540px;">
      <tr >
        <td width="52%"; style="font-size:12px;   border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid; " ><br />Sold to</td>
        <td width="3%"></td>
        <td width="15%" style="text-align:center; font-size:12px;  border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  ">Cheque No.</td>
        <td width="15%" style="text-align:center; font-size:12px;   border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  ">Payment Method</td>
        <td width="15%" style="text-align:center; font-size:12px;   border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  ">Payment Number</td>

       </tr>
      <tr>
        <td rowspan="3" style="font-size:12px;  border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;">'.$ds_fname.' '.$ds_lname.' '.$ds_mname.'<br />' .$ds_add_number.'  '.$ds_add_street.'<br />'.$ds_add_city.', '.$ds_add_state.'<br />'.$ds_add_country.', '.$ds_add_zip.'<br /> Student ID:<br>'.$ds_id.'</td>
        <td></td>
        <td style="text-align:center; font-size:12px;  border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;">'.$ds_cheque.'</td>
        <td style="text-align:center; font-size:12px;  border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;">'.$cl_metodo_pago.'</td>
        <td style="text-align:center; font-size:12px;  border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;"> 1</td>
      </tr>
    </table><br /><br /><br /><br><br><br>';
  $htmlcontent .='
    <br /> <br /> <br /><br />
    <table border="1" style="width:540px;">';
  $htmlcontent .='
    <tr style="font-weight:bold; background-color:#FFFFFF; font-size:12px;" >
    <td width="50%"; style="text-align:center; border:1px #cccccc solid;">'.ObtenEtiqueta(19).'</td>
    <td width="10%"; style="text-align:center; border:1px #cccccc solid; ">Qty</td>
    <td width="20%"; style="text-align:center; border:1px #cccccc solid; ">Fee</td>
    <td width="20%"; style="text-align:center; border:1px #cccccc solid;">'.ObtenEtiqueta(583).'</td>
    </tr>';



      #si el descuento es mayor a cero muestra
      if($mn_costs>0 ){
          $htmlcontent .='
          <tr style="font-size:12px;">
          <td height="15"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid;"> Additional costs: '.$ds_costs.'</td>
          <td style="text-align:center; border-left:1px #cccccc solid; border-right:1px #cccccc solid;  #cccccc solid;">1</td>
          <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid;  #cccccc solid;">'.number_format($mn_costs, 2, '.',',').'</td>
          <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid;  #cccccc solid;">'.number_format($mn_costs, 2, '.',',').'</td>
          </tr>';

      }

      $subtotal =$mn_costs ;


      $htmlcontent .='
        <tr style="font-size:12px;">
          <td height="15"; COLSPAN="2" style="border-left:1px #cccccc solid;  border-top:1px #cccccc solid; border-right:1px #cccccc solid;"></td>
          <td style="border-left:1px #cccccc solid;  border-top:1px #cccccc solid; border-right:1px #cccccc solid;">Subtotal: </td>
          <td align="right" style="border-left:1px #cccccc solid; border-top:1px #cccccc solid; border-right:1px #cccccc solid;">'.number_format($subtotal,2,'.',',').'</td>
        </tr>';
      $mn_tax_additional=0;

      if((($cl_pais==38)||$cl_pais==226)){

        if(!empty($applyTax)) {

        }


              $Query="SELECT ds_add_state ,b.mn_tax,b.ds_type ";
              $Query.="FROM k_ses_app_frm_1 a ";
              $Query.="JOIN k_provincias b ON a.ds_add_state=b.fl_provincia ";
              $Query.="WHERE cl_sesion='$cl_sesion'  ";
              $ro=RecuperaValor($Query);
              $mn_tax_=$ro[1]/100;
              $mn_tax_pdf=number_format($ro[1],0);
              $mn_tax_additional=$subtotal * $mn_tax_;
              $ds_type_tax=$ro['ds_type'];


                  $htmlcontent .='
                <tr style="font-size:12px;">
                  <td height="15"; COLSPAN="2"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; ">&nbsp;</td>
                  <td style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; ">Tax  '.number_format($mn_tax_pdf).'%: ';

                  $htmlcontent .='';
                  $htmlcontent.='
                </td>
                  <td align="right" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; " >'.number_format($mn_tax_additional,2,'.',',').' ';

                  $htmlcontent .='';

                  $htmlcontent.='
                </td>
                </tr>';

       }


      $htmlcontent .='
        <tr style="text-align:right; font-size:12px;">
          <td Colspan="2" align="left"></td>
          <td align="left">Total: </td>
          <td align="right">'.number_format($subtotal + $mn_tax_additional,2,'.',',').'</td>
        </tr>';


  $htmlcontent .='
    </table>';

  class ConPies extends TCPDF {
      //Pie de pagina
      function Footer()
      {
          $bussuness= ObtenConfiguracion(64);
          $this->SetY(-10);
          $this->Cell(0,10,$bussuness,0,0,'L');
      }
      function Header()
      {
      }
  }

  // creamos un nuevo objeto usando la clase extendida ConPies
  $pdf = new ConPies();

  $pdf->SetFont('Times','',8);
  // add a page
  $pdf->AddPage("P");

  //imagen del refund
  if(!empty($fg_refund)){
      $img_file = '../../images/refund.jpg';
      $pdf->Image($img_file, 90, 42, 40, 30, '', '', '', true,300, '', false, false, 0, false, false, false);
  }

  //Proteje el archivo contra copia
  //$pdf->SetProtection($permissions = array('copy'));

  // output the HTML content
  $pdf->writeHTML($htmlcontent, true, 0, true, 0);

  $nombre_archivo = 'Invoice '.$ds_fname.' '.$ds_lname.' '.$Date.'Fame_learning_resources.pdf';
  //Close and output PDF document
  $pdf->Output($nombre_archivo, 'D');




?>