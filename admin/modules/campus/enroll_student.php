<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../../lib/tcpdf/tcpdf.php');

  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $fl_sesion = RecibeParametroNumerico('fl_sesion');
  $fg_sendemail = RecibeParametroBinario('fg_sendemail');
  
  # Recuperamos informacion del aplicante
  $Query1  = "SELECT cl_sesion, ds_fname, ds_lname, ds_mname, fg_gender, fe_birth, ds_email, fl_programa, fl_periodo ";
  $Query1 .= "FROM k_ses_app_frm_1 WHERE cl_sesion=(SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$fl_sesion) ";
  $row1 = RecuperaValor($Query1);
  $cl_sesion = $row1[0];
  $ds_fname = $row1[1];
  $ds_lname = $row1[2];
  $ds_mname = $row1[3];
  $fg_gender = $row1[4];
  $fe_birth = $row1[5];
  $ds_email = str_texto($row1[6]);
  $fl_programa = $row1[7];
  $fl_periodo = $row1[8];
  
  # Obtenemos la fecha de inicio del programa
  $row2 = RecuperaValor("SELECT fe_inicio FROM c_periodo WHERE fl_periodo=$fl_periodo");
  $fe_inicio = $row2[0];
  
  # Obtenemos la fl_template del programa
  $row3 = RecuperaValor("SELECT fl_template FROM c_programa WHERE fl_programa=$fl_programa");
  $fl_template = $row3[0];
  
  #Recupera datos adicionales a la forma 1 y del contrato del aplicante
  $Query  = "SELECT no_contrato, ds_cadena, ds_firma_alumno, fe_firma ";
  $Query .= "FROM k_app_contrato  a ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ORDER BY no_contrato";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    $no_contrato = $row[0];
    $ds_cadena[$no_contrato] = $row[1];
    $ds_firma_alumno[$no_contrato] = $row[2];
    $fe_firma[$no_contrato] = $row[3];
  }

  # Recupera datos de pagos del curso
  $Query  = "SELECT cl_type, no_semanas ";
  $Query .= "FROM k_programa_costos ";
  $Query .= "WHERE fl_programa = $fl_programa";
  $row = RecuperaValor($Query);
  $cl_type = $row[0];
  $no_semanas = $row[1];
  # Si el contrato es mutil anios entonces se enviara un contrato por anio 
  #  totod esto es como lo marca PCTIA
  if($cl_type==4)
    $contratos = 3;
  else{
    # En caso de qe el curso sea mayor a 18 meses y menos a  104 (2 anios) entonces se enviaran dos contratos 
    if($no_semanas>78 AND $no_semanas<104)
      $contratos = 2;
    else# si es curso dure menos de 18 meses se enviara un solo contrato
      $contratos = 1;
  }
  
  # Verificamos que applicantes que si pueden convertirse en student
  $enrol = False;
  for($i=1; $i<=$contratos; $i++) {
    if(!empty($fl_template)) {
      if(empty($ds_cadena[$i]) || (!empty($ds_cadena[$i]) && empty($ds_firma_alumno[$i]))) {        
        if($i==1)
          $enrol = False;
      }
      else {
        if($i==1)
          $enrol = True;
      }
    }
  }
  
  // Se convierte a student
  if($enrol==True){
    
    # Registramos el nuevo student
    $Query  = "INSERT INTO c_usuario(ds_login, ds_password, cl_sesion, fg_activo, fe_alta, no_accesos, ";
    $Query .= "ds_nombres, ds_apaterno, ds_amaterno, fg_genero, fe_nacimiento, ds_email, fl_perfil) ";
    $Query .= "VALUES('ds_login', '1234567890', '$cl_sesion', '1', CURRENT_TIMESTAMP, 0, ";
    $Query .= "'$ds_fname', '$ds_lname', '$ds_mname', '$fg_gender', '$fe_birth', '$ds_email', ".PFL_ESTUDIANTE.") ";
    $fl_usuario = EjecutaInsert($Query);
    $ds_login = substr(strtolower($ds_lname), 0, 1) . substr(strtolower($ds_fname), 0, 1);
    $ds_login = $ds_login . substr($fe_birth, 2, 2) . substr($fe_birth, 5, 2) . substr($fe_birth, 8, 2);
    $ds_login = $ds_login . str_pad($fl_usuario, 4, "0", STR_PAD_LEFT);
    $ds_password = $ds_login;
    $Query  = "UPDATE c_usuario ";
    $Query .= "SET ds_login='$ds_login', ds_password='".sha256($ds_password)."' ";
    $Query .= "WHERE fl_usuario=$fl_usuario";
    EjecutaQuery($Query);
    $row = RecuperaValor("SELECT fl_zona_horaria FROM c_zona_horaria WHERE fg_default='1'");
    $fl_zona_horaria = $row[0];
    $Query  = "INSERT INTO c_alumno(fl_alumno, fl_zona_horaria) ";
    $Query .= "VALUES($fl_usuario, $fl_zona_horaria) ";
    EjecutaQuery($Query);    
    
    # Calculamos lo meses que dura el programa
    $meses = $no_semanas/4;

    # Calcula el end date de acuerdo a las semanas de curso y las coloca por default en el campo
    $fe_fin = date('d-m-Y', strtotime("$fe_inicio + $meses months"));
    $fe_fin = "'".ValidaFecha($fe_fin)."'";
    
    $Query  = "INSERT INTO k_pctia (fl_alumno, fl_programa, fe_fin, fe_completado) ";
    $Query .= "VALUES ($fl_usuario, $fl_programa, $fe_fin, $fe_fin)"; 
    EjecutaQuery($Query);
   
    # Inserta los datos del k_ses_pago a k_alumno_pago
    $Query  = "INSERT INTO k_alumno_pago(fl_alumno, fl_term_pago, cl_metodo_pago, fe_pago, mn_pagado, ds_comentario, ds_cheque, mn_late_fee, ds_transaccion, mn_tax_paypal, ds_tax_provincia)  ";
    $Query .= "SELECT $fl_usuario,fl_term_pago, cl_metodo_pago, fe_pago, mn_pagado, ds_comentario, ds_cheque, mn_late_fee, ds_transaccion, mn_tax_paypal, ds_tax_provincia FROM k_ses_pago ";
    $Query .= "WHERE cl_sesion='$cl_sesion' ";
    EjecutaQuery($Query);
    $Query = "DELETE FROM k_ses_pago WHERE cl_sesion = '$cl_sesion'";
    EjecutaQuery($Query);


    # Actualiza o inserta el registro
    $Query  = "UPDATE c_sesion ";
    $Query .= "SET fg_inscrito='1' ";
    $Query .= "WHERE fl_sesion=$fl_sesion";
    EjecutaQuery($Query);
    
    # Se envia el correo si antes fue confirmado
    if($fg_sendemail==1){
      #fecha en que se envio el template y nombre del template
      $Query  = "SELECT nb_template, DATE_FORMAT(CURRENT_DATE(),'%d-%m-%Y') FROM k_template_doc b WHERE fl_template=24 ";
      $row = RecuperaValor($Query);
      $nb_template = $row[0];
      $Date = $row[1];
      
      # Template
      $ds_header = genera_documento($fl_sesion, 1, 24);
      $ds_body = genera_documento($fl_sesion, 2, 24);
      $ds_footer = genera_documento($fl_sesion, 3, 24);
      
      #guardamos el pdf
      class ConPies extends TCPDF {
        //header 
        function Header(){
          $fl_sesion = RecibeParametroNumerico('fl_sesion');
          $this->writeHTML(genera_documento($fl_sesion, 1, 24), true, 0, true, 0); 
        }
        //footer
        function Footer(){
          $fl_sesion = RecibeParametroNumerico('fl_sesion');
          $this->SetY(-20);
          $this->writeHTML(genera_documento($fl_sesion, 3, 24), true, 0, true, 0); 
        }
      }

      // creamos un nuevo objeto usando la clase extendida ConPies 
      $pdf = new ConPies();
      $pdf->SetFont('times','',10);

      // add a page
      $pdf->AddPage("P"); 
      
      // output the HTML content
      $pdf->writeHTMLCell(180, 100, 10,30,$ds_body, 0, 0, false, true,'',true); 
      //nombre del archivo
      $fileName = $fl_sesion.'24'.$Date.'.pdf';
      // pasamos el archivo a base64
      //$pdf->Output($fileName, 'F');///guarda el archivo MRA: Se deja comen tado porque se cambio el metodo para que ahora vaya como attachment
      $fileatt = $pdf->Output($fileName, 'E'); //genera la codificacion para enviar adjuntado el archivo
      
      // //envia copia a admin@vanas.ca
      $admin=ObtenConfiguracion(83);
      $ds_emailfrom=ObtenConfiguracion(4);

      # Inicializa variables de ambiente para envio de correo adjunto
      ini_set("SMTP", MAIL_SERVER);
      ini_set("smtp_port", MAIL_PORT);
      ini_set("sendmail_from", MAIL_FROM);
      
      $repEmail =$ds_emailfrom;
      
      $eol = "\n";
      $separator = md5(time());

      $headers = 'From: '.$repEmail.' <'.$repEmail.'>'.$eol;
      $headers .= 'MIME-Version: 1.0' .$eol;
      $headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

      $message = "--".$separator.$eol;
      $message .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
      // $message .= "Content-Transfer-Encoding: quoted-printable ".$eol.$eol;
      $message .= utf8_decode($ds_header).utf8_decode($ds_body).utf8_decode($ds_footer).$eol;

      $message .= "--".$separator.$eol;
      $message .= $fileatt;
      $message .= "--".$separator."--".$eol;
      
      mail($ds_email, $nb_template, $message, $headers);
      mail($admin, $nb_template, $message, $headers);
      # Insertamos el template que ya se le envio al student
      $Queryt = "INSERT INTO k_alumno_template(fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
      $Queryt .= "VALUES ($fl_sesion, 24, CURRENT_TIMESTAMP, '".str_html_bd($ds_header)."', '".str_html_bd($ds_body)."','".str_html_bd($ds_footer)."') ";
      EjecutaQuery($Queryt);
    }
    echo 1;
  }
  else //No se convierte en student
    echo 0;
  
?>