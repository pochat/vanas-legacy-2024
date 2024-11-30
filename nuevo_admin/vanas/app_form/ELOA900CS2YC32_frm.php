<?php
  
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  require("../lib/sp_forms.inc.php");
  require("app_form.inc.php");

  # Recupera sesion del cookie
  $clave = SP_RecuperaSesion();
  
  $origen = RecibeParametroHTML("origen");
  if($origen <> 'DMMB7SDFVC645BV_frm.php') // Si viene de paypal o de otro programa
    $Query_extra  = "fg_paypal='1', fg_confirmado='1', fg_pago='1', fe_ultmod=CURRENT_TIMESTAMP ";
  else
    $Query_extra  = "fg_paypal='0', fg_confirmado='1', fg_pago='0', fe_ultmod=CURRENT_TIMESTAMP ";

  # Si no es una sesion valida redirige a la forma inicial 
  if(empty($clave)) {
    header("Location: ABSP4MDSFSDF8V_frm.php");
    exit;
  }
  
  # Recupera datos de la sesion
  $concat = array(ConsultaFechaBD('fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultmod', FMT_HORA));
  $Query  = "SELECT fg_app_1, fg_app_2, fg_app_3, fg_app_4, (".ConcatenaBD($concat).") 'fe_ultmod' ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE cl_sesion='$clave'";
  $row = RecuperaValor($Query);
  $fg_app_1 = $row[0];
  $fg_app_2 = $row[1];
  $fg_app_3 = $row[2];
  $fg_app_4 = $row[3];
  $fe_ultmod = $row[4];
  
  # Si no se completaron todos los pasos redirige a la forma inicial
  if($fg_app_1 <> '1' OR $fg_app_2 <> '1' OR $fg_app_3 <> '1' OR $fg_app_4 <> '1') {
    header("Location: ABSP4MDSFSDF8V_frm.php");
    exit;
  }
  
  
  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
  $Query .= ConsultaFechaBD('fe_birth', FMT_FECHA)." fe_birth, ";
  $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, d.ds_pais, ";
  $Query .= "ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, e.ds_pais, ";
  $Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, nb_programa, nb_periodo ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_periodo=c.fl_periodo ";
  $Query .= "AND a.ds_add_country=d.fl_pais ";
  $Query .= "AND a.ds_eme_country=e.fl_pais ";
  $Query .= "AND cl_sesion='$clave'";
  $row = RecuperaValor($Query);
  $nb_programa = $row[23];
  $nb_periodo = $row[24];
  $ds_fname = str_ascii($row[0]);
  $ds_mname = str_ascii($row[1]);
  $ds_lname = str_ascii($row[2]);
  $ds_number = str_ascii($row[3]);
  $ds_alt_number = str_ascii($row[4]);
  $ds_email = str_ascii($row[5]);
  $fg_gender = str_ascii($row[6]);
  $fe_birth = $row[7];
  $ds_add_number = str_ascii($row[8]);
  $ds_add_street = str_ascii($row[9]);
  $ds_add_city = str_ascii($row[10]);
  $ds_add_state = str_ascii($row[11]);
  $ds_add_zip = str_ascii($row[12]);
  $ds_add_country = str_ascii($row[13]);
  $ds_eme_fname = str_ascii($row[14]);
  $ds_eme_lname = str_ascii($row[15]);
  $ds_eme_number = str_ascii($row[16]);
  $ds_eme_relation = str_ascii($row[17]);
  $ds_eme_country = str_ascii($row[18]);
  $fg_ori_via = str_ascii($row[19]);
  $ds_ori_other = str_ascii($row[20]);
  $fg_ori_ref = str_ascii($row[21]);
  $ds_ori_ref_name = str_ascii($row[22]);
  
  # Recupera datos del aplicante: forma 2
  $Query  = "SELECT ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7 ";
  $Query .= "FROM k_ses_app_frm_2 ";
  $Query .= "WHERE cl_sesion='$clave'";
  $row = RecuperaValor($Query);
  $ds_resp2_1 = str_ascii($row[0]);
  $ds_resp2_2 = str_ascii($row[1]);
  $ds_resp2_3 = str_ascii($row[2]);
  $ds_resp2_4 = str_ascii($row[3]);
  $ds_resp2_5 = str_ascii($row[4]);
  $ds_resp2_6 = str_ascii($row[5]);
  $ds_resp2_7 = str_ascii($row[6]);
  
  # Recupera datos del aplicante: forma 3
  $Query  = "SELECT fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, ";
  $Query .= "fg_resp_2_1, fg_resp_2_2, fg_resp_2_3, fg_resp_2_4, fg_resp_2_5, fg_resp_2_6, fg_resp_2_7, ";
  $Query .= "fg_resp_3_1, fg_resp_3_2 ";
  $Query .= "FROM k_ses_app_frm_4 ";
  $Query .= "WHERE cl_sesion='$clave'";
  $row = RecuperaValor($Query);
  $fg_resp4_1_1 = str_ascii($row[0]);
  $fg_resp4_1_2 = str_ascii($row[1]);
  $fg_resp4_1_3 = str_ascii($row[2]);
  $fg_resp4_1_4 = str_ascii($row[3]);
  $fg_resp4_1_5 = str_ascii($row[4]);
  $fg_resp4_1_6 = str_ascii($row[5]);
  $fg_resp4_2_1 = str_ascii($row[6]);
  $fg_resp4_2_2 = str_ascii($row[7]);
  $fg_resp4_2_3 = str_ascii($row[8]);
  $fg_resp4_2_4 = str_ascii($row[9]);
  $fg_resp4_2_5 = str_ascii($row[10]);
  $fg_resp4_2_6 = str_ascii($row[11]);
  $fg_resp4_2_7 = str_ascii($row[12]);
  $fg_resp4_3_1 = str_ascii($row[13]);
  $fg_resp4_3_2 = str_ascii($row[14]);
  
  # Recupera datos del aplicante: forma 4
  $Query  = "SELECT ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, ds_resp_8 ";
  $Query .= "FROM k_ses_app_frm_3 ";
  $Query .= "WHERE cl_sesion='$clave'";
  $row = RecuperaValor($Query);
  $ds_resp3_1 = str_ascii($row[0]);
  $ds_resp3_2_1 = str_ascii($row[1]);
  $ds_resp3_2_2 = str_ascii($row[2]);
  $ds_resp3_2_3 = str_ascii($row[3]);
  $ds_resp3_3 = str_ascii($row[4]);
  $ds_resp3_4 = str_ascii($row[5]);
  $ds_resp3_5 = str_ascii($row[6]);
  $ds_resp3_6 = str_ascii($row[7]);
  $ds_resp3_7 = str_ascii($row[8]);
  $ds_resp3_8 = str_ascii($row[9]);
  
  
  # Prepara variables de ambiente para envio de correo
  $app_frm_email = ObtenConfiguracion(20);
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", $app_frm_email);
  
  # Envia correo de confirmacion al aplicante
  $subject = ObtenEtiqueta(335);
  $message  = "Dear $ds_fname $ds_lname,\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(331)."\n";
  $message .= ObtenEtiqueta(332)."\n";
  $message .= ObtenEtiqueta(333)."\n\n";
  $message .= ObtenEtiqueta(337)."\n";
  $message .= ObtenEtiqueta(338)."\n";
  $message = utf8_encode(str_ascii($message));
  $headers = "From: $app_frm_email\r\nReply-To: $ds_email\r\n";
  $mail_sent = mail($ds_email, $subject, $message, $headers);
  
  # Envia correo de confirmacion al Administrador
  $subject = ObtenEtiqueta(336);
  $message  = "Application form submitted $fe_ultmod\n";
  if($fg_paypal == '1')
    $message .= ObtenEtiqueta(343).".\n";
  else
    $message .= "Payment not submitted.\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(55)."\n";
  $message .= ObtenEtiqueta(59).": $nb_programa\n";
  $message .= ObtenEtiqueta(60).": $nb_periodo\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(61)."\n";
  $message .= ObtenEtiqueta(117).": $ds_fname\n";
  $message .= ObtenEtiqueta(119).": $ds_mname\n";
  $message .= ObtenEtiqueta(118).": $ds_lname\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(280).": $ds_number\n";
  $message .= ObtenEtiqueta(281).": $ds_alt_number\n";
  $message .= ObtenEtiqueta(121).": $ds_email\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(114).": ";
  if($fg_gender == 'M')
    $message .= ObtenEtiqueta(115)."\n";
  else
    $message .= ObtenEtiqueta(116)."\n";
  $message .= ObtenEtiqueta(120).": $fe_birth\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(62)."\n";
  $message .= ObtenEtiqueta(282).": $ds_add_number\n";
  $message .= ObtenEtiqueta(283).": $ds_add_street\n";
  $message .= ObtenEtiqueta(284).": $ds_add_city\n";
  $message .= ObtenEtiqueta(285).": $ds_add_state\n";
  $message .= ObtenEtiqueta(286).": $ds_add_zip\n";
  $message .= ObtenEtiqueta(287).": $ds_add_country\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(63)."\n";
  $message .= ObtenEtiqueta(117).": $ds_eme_fname\n";
  $message .= ObtenEtiqueta(118).": $ds_eme_lname\n";
  $message .= ObtenEtiqueta(280).": $ds_eme_number\n";
  $message .= ObtenEtiqueta(288).": $ds_eme_relation\n";
  $message .= ObtenEtiqueta(287).": $ds_eme_country\n";
  $message .= "\n";
  $message .= ObtenEtiqueta(289)." ";
  switch($fg_ori_via) {
    case 'A': $message .= ObtenEtiqueta(290)."\n"; break;
    case 'B': $message .= ObtenEtiqueta(291)."\n"; break;
    case 'C': $message .= ObtenEtiqueta(292)."\n"; break;
    case 'D': $message .= ObtenEtiqueta(293)."\n"; break;
    case '0': $message .= ObtenEtiqueta(294)." - $ds_ori_other\n"; break;
  }
  $message .= ObtenEtiqueta(295)." ";
  switch($fg_ori_ref) {
    case '0': $message .= ObtenEtiqueta(17)."\n"; break;
    case 'S': $message .= ObtenEtiqueta(296)." - $ds_ori_ref_name\n"; break;
    case 'T': $message .= ObtenEtiqueta(297)." - $ds_ori_ref_name\n"; break;
    case 'G': $message .= ObtenEtiqueta(298)." - $ds_ori_ref_name\n"; break;
    case 'A': $message .= ObtenEtiqueta(811)." - $ds_ori_ref_name\n"; break;
  }
  $message .= "\n\n";
  $message .= ObtenEtiqueta(56)."\n";
  $message .= ObtenEtiqueta(301)."\n$ds_resp2_1\n";
  $message .= ObtenEtiqueta(302)."\n$ds_resp2_2\n";
  $message .= ObtenEtiqueta(303)."\n$ds_resp2_3\n";
  $message .= ObtenEtiqueta(304)."\n$ds_resp2_4\n";
  $message .= ObtenEtiqueta(305)."\n$ds_resp2_5\n";
  $message .= ObtenEtiqueta(306)."\n$ds_resp2_6\n";
  $message .= ObtenEtiqueta(307)."\n$ds_resp2_7\n";
  $message .= "\n";
  
  $etq_si = ObtenEtiqueta(16);
  $etq_no = ObtenEtiqueta(17);
  $message .= ObtenEtiqueta(78)."\n";
  
  $message .= ObtenEtiqueta(79)."\n";
  $message .= ObtenEtiqueta(82)."\n";
  switch($fg_resp4_1_1) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(83)."\n";
  switch($fg_resp4_1_2) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(84)."\n";
  switch($fg_resp4_1_3) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(85)."\n";
  switch($fg_resp4_1_4) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(86)."\n";
  switch($fg_resp4_1_5) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(87)."\n";
  switch($fg_resp4_1_6) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  
  $message .= ObtenEtiqueta(80)."\n";
  $message .= ObtenEtiqueta(88)."\n";
  switch($fg_resp4_2_1) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(89)."\n";
  switch($fg_resp4_2_2) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(90)."\n";
  switch($fg_resp4_2_3) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(91)."\n";
  switch($fg_resp4_2_4) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(92)."\n";
  switch($fg_resp4_2_5) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(93)."\n";
  switch($fg_resp4_2_6) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  $message .= ObtenEtiqueta(94)."\n";
  switch($fg_resp4_2_7) {
    case '1': $message .= $etq_si."\n"; break;
    case '0': $message .= $etq_no."\n"; break;
  }
  
  $message .= ObtenEtiqueta(81)."\n";
  $message .= ObtenEtiqueta(95)."\n";
  switch($fg_resp4_3_1) {
    case '0': $message .= ObtenEtiqueta(97)."\n"; break;
    case '1': $message .= ObtenEtiqueta(98)."\n"; break;
    case '2': $message .= ObtenEtiqueta(99)."\n"; break;
    case '3': $message .= ObtenEtiqueta(107)."\n"; break;
  }
  $message .= ObtenEtiqueta(96)."\n";
  switch($fg_resp4_3_2) {
    case '0': $message .= ObtenEtiqueta(97)."\n"; break;
    case '1': $message .= ObtenEtiqueta(98)."\n"; break;
    case '2': $message .= ObtenEtiqueta(99)."\n"; break;
    case '3': $message .= ObtenEtiqueta(107)."\n"; break;
  }
  
  $message .= "\n";
  $message .= ObtenEtiqueta(57)."\n";
  $message .= ObtenEtiqueta(308)."\n$ds_resp3_1\n";
  $message .= ObtenEtiqueta(309)."\n";
  $message .= "1: $ds_resp3_2_1\n";
  $message .= "2: $ds_resp3_2_2\n";
  $message .= "3: $ds_resp3_2_3\n";
  $message .= ObtenEtiqueta(310)."\n$ds_resp3_3\n";
  $message .= ObtenEtiqueta(311)."\n$ds_resp3_4\n";
  $message .= ObtenEtiqueta(312)."\n$ds_resp3_5\n";
  $message .= ObtenEtiqueta(313)."\n";
  switch($ds_resp3_6) {
    case 'A': $message .= ObtenEtiqueta(314)."\n"; break;
    case 'B': $message .= ObtenEtiqueta(315)."\n"; break;
    case 'C': $message .= ObtenEtiqueta(316)."\n"; break;
  }
  $message .= ObtenEtiqueta(317)."\n";
  switch($ds_resp3_7) {
    case 'A': $message .= ObtenEtiqueta(318)."\n"; break;
    case 'B': $message .= ObtenEtiqueta(319)."\n"; break;
    case 'C': $message .= ObtenEtiqueta(320)."\n"; break;
    case 'D': $message .= ObtenEtiqueta(321)."\n"; break;
    case 'E': $message .= ObtenEtiqueta(322)."\n"; break;
  }
  $message .= ObtenEtiqueta(323)."\n$ds_resp3_8\n";
  $message .= "\n\n";
  $message = utf8_encode(str_ascii($message));
  $headers = "From: $app_frm_email\r\nReply-To: $app_frm_email\r\n";
  $mail_sent = mail($app_frm_email, $subject, $message, $headers);
  
  # Actualiza estado del registro de aplicacion
  $Query  = "UPDATE c_sesion SET $Query_extra ";
  $Query .= "WHERE cl_sesion='$clave'";
  EjecutaQuery($Query);

  # Termina la sesion
  SP_ActualizaSesion("");
  
  
  # Header
  PresentaHeaderAF( );
  
  # MRA 15 junio 2012
  # Codigo perfectpixel
  echo "
  <!-- Conversion Pixel - default conversion pixel - DO NOT MODIFY -->
    <script src='https://secure.adnxs.com/px?id=39442&t=1' type='text/javascript'></script>
  <!-- End of Conversion Pixel -->";
  
  #Codigo para Google Adwords
  echo '
  <!-- Google Code for Registration Conversion Page -->
  <script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 972779902;
    var google_conversion_language = "en";
    var google_conversion_format = "2";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "bgltCPL9llcQ_uLtzwM";
    var google_remarketing_only = false;
    /* ]]> */
  </script>
  <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
  <noscript>
    <div style="display:inline;">
      <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/972779902/?label=bgltCPL9llcQ_uLtzwM&amp;guid=ON&amp;script=0"/>
    </div>
  </noscript>';
  
  # Cuerpo del Home
  echo "
    <table border='".D_BORDES."' width='100%' height='584' valign='top' cellspacing='0' cellpadding='0' class='app_form'>
      <tr>
        <td width='20' height='20'>&nbsp;</td>
        <td>&nbsp;</td>
        <td width='20'>&nbsp;</td>
      </tr>
      <tr>
        <td height='30'>&nbsp;</td>
        <td><b>".ObtenEtiqueta(328)."</b></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td valign='top'>
          <p>".ObtenEtiqueta(331)."</p>
          <p>".ObtenEtiqueta(332)."</p>
          <p>".ObtenEtiqueta(333)."</p>
          <p>".ObtenEtiqueta(334)."</p>
          <br>
          <br>
		  <form method='link' action='ABSP4MDSFSDF8V_frm.php'>
			<input type='submit' id='buttons' value='".ObtenEtiqueta(329)."'>
		  </form>
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3' height='20'>&nbsp;</td>
      </tr>
    </table>";
   
  # Footer
  PresentaFooterAF( );
  
?>