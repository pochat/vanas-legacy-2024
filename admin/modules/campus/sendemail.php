<?php
    
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../../lib/tcpdf/tcpdf.php');

  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $fl_template = $_REQUEST['fl_template'];
  $ds_emailfrom = $_REQUEST['ds_emailfrom'];
  $ds_emailto = $_REQUEST['ds_emailto'];
  $ds_subject = $_REQUEST['ds_subject'];
  $fl_sesion = $_REQUEST['fl_sesion'];
  $fl_alumno = $_REQUEST['fl_alumno'];
  
  # generador de documento
  $ds_header = genera_documento($fl_sesion, 1, $fl_template);
  $ds_cuerpo = genera_documento($fl_sesion, 2, $fl_template);
  $ds_footer = genera_documento($fl_sesion, 3, $fl_template);
  
  #Obtenemos los datos de la persona
  $Query  = "SELECT ds_fname, ds_mname, ds_lname ";
  $Query .= "FROM k_ses_app_frm_1 a, c_sesion b ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion AND fl_sesion='$fl_sesion'";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  $ds_nombre = $ds_fname." ".$ds_lname;
  #fecha en que se envio el template y nombre del template
  $Query  = "SELECT nb_template, DATE_FORMAT(CURRENT_DATE(),'%d-%m-%Y') FROM k_template_doc b WHERE fl_template=$fl_template ";
  $row = RecuperaValor($Query);
  $nb_template = $row[0];
  $Date = $row[1];
   
  #guardamos el pdf
  class ConPies extends TCPDF {
    //header 
    function Header(){
      $fl_template = $_REQUEST['fl_template'];
      $fl_sesion = $_REQUEST['fl_sesion'];
      $this->writeHTML(genera_documento($fl_sesion, 1, $fl_template), true, 0, true, 0); 
    }
    //footer
    function Footer(){
      $fl_template = $_REQUEST['fl_template'];
      $fl_sesion = $_REQUEST['fl_sesion'];
      $this->SetY(-20);
      $this->writeHTML(genera_documento($fl_sesion, 3, $fl_template), true, 0, true, 0); 
    }
  }

  // creamos un nuevo objeto usando la clase extendida ConPies 
  $pdf = new ConPies();
  $pdf->SetFont('times','',10);

  // add a page
  $pdf->AddPage("P"); 
  
  // output the HTML content
  $pdf->writeHTMLCell(180, 100, 10,30,$ds_cuerpo, 0, 0, false, true,'',true); 
  //nombre del archivo
  $fileName = $fl_sesion.$fl_template.$Date.'.pdf';
  // pasamos el archivo a base64
  //$pdf->Output($fileName, 'F');///guarda el archivo MRA: Se deja comen tado porque se cambio el metodo para que ahora vaya como attachment
  $fileatt = $pdf->Output($fileName, 'E'); //genera la codificacion para enviar adjuntado el archivo
  
  # Valida los campos que no esten en blanco y los correos que sean validos
  if(empty($fl_template))
    $fl_template_err = ERR_REQUERIDO;
  if(empty($ds_emailfrom))
    $ds_emailfrom_err = ERR_REQUERIDO;
  if(empty($ds_emailto))
    $ds_emailto_err = ERR_REQUERIDO;
  
  $fg_error = $fl_template_err || $ds_emailfrom_err || $ds_emailto_err;
  
  if(empty($fg_error)){
    
    //envia copia a admin@vanas.ca
    $apply=ObtenConfiguracion(83);

    
    # Inicializa variables de ambiente para envio de correo adjunto
    ini_set("SMTP", MAIL_SERVER);
    ini_set("smtp_port", MAIL_PORT);
    ini_set("sendmail_from", MAIL_FROM);
    $repEmail =$apply;
    
    $eol = "\n";
    $separator = md5(time());
    
    
    $headers  = 'MIME-Version: 1.0' .$eol;
    $headers .= 'From: "'.$ds_subject.'" <'.$repEmail.'>'.$eol;
    $headers .= 'Content-Type: multipart/mixed; boundary="'.$separator.'"';
    
    $message = "--".$separator.$eol;
    $message .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
    // $message .= "Content-Transfer-Encoding: quoted-printable ".$eol.$eol;
    $message .= utf8_decode($ds_header).utf8_decode($ds_cuerpo).utf8_decode($ds_footer).$eol;

    $message .= "--".$separator.$eol;
    $message .= $fileatt;
    $message .= "--".$separator."--".$eol;
    
    # insertamos el envio del email
    if (mail($ds_emailto, $nb_template, $message, $headers) AND mail($apply, $nb_template, $message, $headers)){
      # cambio el fl_alumno por fl_sesion para funcionamiento tanto en students como en applications
      $Query = "INSERT INTO k_alumno_template(fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
      $Query .= "VALUES ($fl_sesion, $fl_template, CURRENT_TIMESTAMP, '".str_html_bd($ds_header)."', '".str_html_bd($ds_cuerpo)."','".str_html_bd($ds_footer)."') ";
      EjecutaQuery($Query);
      //unlink($fileName);//eliminamos el archivo MRA: Se deja comen tado porque se cambio el metodo para que ahora vaya como attachment
      echo 1;
    }
    else {
      echo ErrorInfo;
    }
  }

?>