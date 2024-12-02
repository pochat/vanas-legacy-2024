<?php
  
  # Libreria de funciones
  include("../../../../fame/lib/self_general.php");
  include('../../../lib/tcpdf/config/lang/eng.php');
  include('../../../lib/tcpdf/tcpdf.php');
  
  # Recibimos los parametros
  $fl_usuario = RecibeParametroNumerico('u');
  if(empty($fl_usuario))
  $fl_usuario = RecibeParametroNumerico('u', true, false);
  $fl_programa = RecibeParametroNumerico('p');
  if(empty($fl_programa))
  $fl_programa = RecibeParametroNumerico('p', true, false);
  $fg_tipo = RecibeParametroNumerico('fg_tipo');
  if(empty($fg_tipo)){
    $fg_tipo = RecibeParametroNumerico('fg_tipo', true);
  }
   
  
  // echo $fl_sesion;
  // exit;
  # cleaning the buffer before Output()
  ob_clean();
  # create new PDF document
  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, "LETTER", true, 'UTF-8', true);

  // Set owner password
  $ownerPassword = ObtenConfiguracion(164);
  // $pdf->SetProtection(array('modify'), '', $ownerPassword, 0, null);
  
  
  # set header and footer fonts
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

  # set margins
  # Acomodar la imagen
  $pdf->SetMargins(0, 0, 0, 0);
  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(0);

  # remove default header/footer
  $pdf->setPrintHeader(false);
  $pdf->setPrintFooter(false);

  # set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->SetAutoPageBreak(true);

  # set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  
  # set some language-dependent strings
  $pdf->setLanguageArray($l);

  # add a page
  $pdf->AddPage("P");

  // set link
  // $link_qr='campus.vanas.ca/StudentAccreditation.php?clave='.$fl_sesion.'&type=1&data='.$clave;
  $link_qr = ObtenConfiguracion(116).'/fame/StudentAccreditation.php?type=1&data='.$fl_usuario.'&prgm='.$fl_programa;

   // set style for barcode
   $style = array(
    'border' => 0,
    'padding' => 0,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
  );

  
  
  
  # Obtenemos la informacion del estudiante
  $user = RecuperaValor("SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario");
  $ds_nombres = str_uso_normal($user[0]);
  $ds_apaterno = str_uso_normal($user[1]);
  
  # Obtenemos el nombre del programa
  // $nb_programa = ObtenNombreCourse($fl_programa); //// TODO
  // $getNbPrograma = "SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp = $fl_programa";
  $row = RecuperaValor("SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp = $fl_programa");
  $nb_programa = str_uso_normal($row[0]);
  
  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = date_create($row[0]);
  $fe_actual = date_format($fe_actual,'F j, Y');

  # Altura para las certificaciones blancas
  $height = 250;
  
  
  # Verificamos si quiere imagen o no  
  # Imagen background
  if($fg_tipo==2){
    // $img_file = PATH_SELF_IMG_F.'/FAME_course_certificate_2021.png';
    $img_file = "../../images/FAME_course_certificate_2021.png";
    // $img_file = './resources/img/FAME_course_certificate_2021.png';

    // $img_file = '';
    $pdf->Image($img_file, 0, 0, 0, 0, '', '', '', false, 300, '', false, false, $border, false, false, false);
    $height = 350;
    
    
    // QRCODE,L : QR-CODE Low error correction
    $pdf->write2DBarcode(''.$link_qr.'', 'QRCODE,L', 165, 224, 23, 23, $style, 'N');
  }
 
  // Long name validations
  $fullname = $ds_nombres." ".$ds_amaterno." ".$ds_apaterno;
  $fullname_string_count = strlen($fullname);
  $nb_program_string_count = strlen($nb_programa);
  
  
  # output the HTML content
  $htmlcontent = '
  <table border="0" style="font-size:45px;">
  <tbody>
  <tr>
  <td width="18%" height="'.$height.'px"></td>
  <td width="82%" ></td>
  </tr>
  <tr>
  <td></td>
  <td height="40px">'.ObtenEtiqueta(1800).'</td>
  </tr>
  <tr>
  <td></td>
  <td height="60px"><strong style="font-size:80px;">'.$fullname.'</strong></td>
  </tr>    
  <tr>
  <td></td>
  <td>'.ObtenEtiqueta(1801).'</td>
  </tr>
  <tr>
  <td></td>
  <td>'.ObtenEtiqueta(1802).'</td>
  </tr>
  <tr>
  <td></td>
  <td>'.ObtenEtiqueta(1803).'</td>
  </tr>
  <tr>
  <td></td>
  <td height="60px">'.ObtenEtiqueta(1804).'</td>
  </tr>
  <tr>
  <td></td>
  <td height="80px"><strong id="program_name" style="font-size:80px;">'.$nb_programa.'</strong></td>
  </tr>
  <tr>
  <td></td>
  <td height="60px">'.ObtenEtiqueta(1805).'</td>
  </tr>
  <tr>
  <td></td>
  <td height="100px">'.$fe_actual.'<div style="line-height:3px;">______________________</div>'.ObtenEtiqueta(1806).'</td>
  </tr>
  <tr>
  <td></td>
  <td><div>______________________</div>'.ObtenEtiqueta(1807).'<div>'.ObtenEtiqueta(1808).'</div></td>
      </tr>
      </tbody>
      </table>';
      
      // resize for a large full name 
      if($fullname_string_count >= 23){
    $name_div = '<td height="60px"><strong style="font-size:80px;">';
    $new_name_div = '<td height="60px"><strong style="font-size:55px;">';
    $htmlcontent = str_replace($name_div,$new_name_div,$htmlcontent);
  }
  
  // resize for a large full name 
  if($nb_program_string_count >= 36 ){
    $name_div = '<strong id="program_name" style="font-size:80px;">';
    $new_name_div = '<strong id="program_name" style="font-size:65px;">';
    $htmlcontent = str_replace($name_div,$new_name_div,$htmlcontent);
  }
  
  // signature
  $vanasSignature = "../../images/diploma-vanas-signature.png";
  
  // Validation large for full name signature
  if($fullname_string_count >= 23){
    $pdf->Image($vanasSignature, 35, 207, 35, 35, '', '', '', false, 300, '', false, false, 0);
  }else{
    $pdf->Image($vanasSignature, 45, 210, 35, 35, '', '', '', false, 300, '', false, false, 0);
  }
  
  
  $pdf->writeHTMLCell(0, 0, 0, 0, $htmlcontent, 0, 0, false, false,'',false);
  
  
  # Buscamos el registro dependiendo del tipo si existe solo actualiza la fecha
  $row = RecuperaValor("SELECT COUNT(*) FROM k_usuario_doc WHERE fl_usuario=$fl_usuario AND fl_programa=$fl_programa AND fg_tipo_doc='$fg_tipo'");
  # Insertamos en el registro
  if($fg_tipo==2){
    # Si no existe lo insterta en caso contrario solo actualiza la fecha en que descarga
    if(!$row[0]){
      # Insertamos el registro del pdf que descargo
      # Indicamos con el fg_tipo_doc=1 que el usuario descargo este certifcado pero no tinene valides
      $Query  = "INSERT INTO k_usuario_doc (fl_usuario,fl_programa,fl_template,fe_enviado,ds_body, fg_tipo_doc) ";
    $Query .= "VALUES ($fl_usuario, $fl_programa, 0, now(), '$htmlcontent', '$fg_tipo') ";
  }  
}
else{
  # Si no existe lo insterta en caso contrario solo actualiza la fecha en que descarga
  if(!$row[0]){
    # Insertamos el registro del pdf que descargo
    # Indicamos con el fg_tipo_doc=1 que el usuario descargo este certifcado pero no tinene valides
    $Query  = "INSERT INTO k_usuario_doc (fl_usuario,fl_programa,fl_template,fe_enviado,ds_body, fg_tipo_doc) ";
    $Query .= "VALUES ($fl_usuario, $fl_programa, 0, now(), '$htmlcontent', '1') ";
  }
  else{
    $Query = "UPDATE k_usuario_doc SET fe_enviado=now(),ds_body='$htmlcontent' WHERE fl_usuario=$fl_usuario AND fl_programa=$fl_programa AND fg_tipo_doc='$fg_tipo'";
  }
}  
EjecutaQuery($Query);

# Nombre del archivo
$nombre_archivo = "Certificate_".$fl_usuario."_".$fl_programa.".pdf";

$path = './GenerateArchives/mergeFame/tempPDFs/'.$nombre_archivo;

# Close and output PDF document
// $pdf->Output($nombre_archivo, 'D');
$pdf->Output($path, 'F');