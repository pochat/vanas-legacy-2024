<?php
  # Este cron funcionara para los email de  Missed Class and Attendance Warning para los estudiantes
	# Email Library
  require ('/var/www/html/AWS_SES/PHP/com_email_func.inc.php');
  
  # Student Take Action template
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie, nb_template FROM k_template_doc WHERE fl_template='36' AND fg_activo='1'";
	$takeaction_template = RecuperaValor($Query);
	$st_takeaction_template = str_uso_normal($takeaction_template[0].$takeaction_template[1].$takeaction_template[2]);
  $nb_template = $takeaction_template[3];

  
  # Buscamos a los alumnos que esta en el listado de take action
  $Query  = "SELECT fl_usuario, ds_login, ds_nombres, ds_apaterno,ds_amaterno, nb_programa, DATE_FORMAT(fe_alta, '%d-%m-%Y'), ".ConsultaFechaBD('fe_completado', FMT_FECHA).", ";
  $Query .= "fg_activo,fg_pago, g.no_promedio_t ";
  $Query .= "FROM c_usuario a, c_perfil b, c_sesion c, k_ses_app_frm_1 d, c_programa e, k_pctia f, c_alumno g ";
  $Query .= "LEFT JOIN c_calificacion h ON(g.no_promedio_t>0 AND g.no_promedio_t BETWEEN no_min AND  no_max ) ";
  $Query .= "WHERE a.fl_perfil=b.fl_perfil AND a.cl_sesion=c.cl_sesion AND c.cl_sesion=d.cl_sesion AND d.fl_programa=e.fl_programa ";
  $Query .= "AND a.fl_usuario = f.fl_alumno AND a.fl_perfil=3 AND a.fl_usuario=g.fl_alumno AND f.fe_completado < CURDATE() ";
  $Query .= "AND (a.fg_activo='1' OR (a.fg_activo='0' AND f.fg_desercion='0' AND f.fg_dismissed='0' AND f.fg_job='0' AND f.fg_graduacion='0' ";
  $Query .= "AND f.fg_certificado='0' AND f.fg_honores ='0'))  ";
  $rs = EjecutaQuery($Query);
  $sts_take_action = "";
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $fl_alumno = $row[0];
    $ds_nombres = str_texto($row[2]);
    $ds_apaterno = str_texto($row[3]);
    $ds_amaterno = str_texto($row[4]);    
    $sts_take_action = $sts_take_action.$ds_nombres."&nbsp;".$ds_apaterno."<br>";
    
  }
  
  # Variables
  $variables = array(
    "sts_take_action" => $sts_take_action
  );
  
  # Inicializa variables de ambiente para envio de correo adjunto
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);
  
  $repEmail = ObtenConfiguracion(83);
  
  $eol = "\n";
  $separator = md5(time());

  $headers = 'From: '.$repEmail.' <'.$repEmail.'>'.$eol;
  $headers .= 'MIME-Version: 1.0' .$eol;
  $headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";
  
  # Generate the email template with the variables message
  $ds_email_template = GenerateTemplate($st_takeaction_template, $variables);
  $message = "--".$separator.$eol;
  $message .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
  // $message .= "Content-Transfer-Encoding: quoted-printable ".$eol.$eol;
  $message .= $ds_email_template.$eol;
  
  mail($repEmail, $nb_template, $message, $headers);

 ?>
