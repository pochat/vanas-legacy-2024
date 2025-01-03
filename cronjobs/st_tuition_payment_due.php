<?php
if (PHP_OS == 'Linux') {
    # Include campus libraries
    require '/var/www/html/vanas/lib/com_func.inc.php';
    require '/var/www/html/vanas/lib/sp_config.inc.php';

    # Include AWS SES libraries
    require '/var/www/html/vanas/AWS_SES/PHP/com_email_func.inc.php';
} else {

    require '../vanas/lib/com_func.inc.php';
    require '../lib/sp_config.inc.php';
    require '../AWS_SES/PHP/com_email_func.inc.php';

}
	$from = 'noreply@vanas.ca';

	# Prepare email templates for assignment reminders, note: (change nb_template='___' to fl_template=id once this is stable on production server)
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template=18 AND fg_activo='1'";
	$tu_due_template = RecuperaValor($Query);
	$ds_template = str_uso_normal($tu_due_template[0].$tu_due_template[1].$tu_due_template[2]);

	# Create a DOM object
#	$ds_template_html = new simple_html_dom();
    #Nota:  se le agrega -1 para que se sejute un dia antes de su fecha de pago. 15-06-2020 $Query .= "WHERE CURDATE() = DATE(a.fe_pago - 1) ";
	# Tuition payment due, find terms that have tuition due today
	$Query  = "SELECT a.fl_term_pago, a.fl_term, a.no_opcion, a.no_pago, DATE_FORMAT(a.fe_pago, '%M %e, %Y'), c.nb_programa ";
	$Query .= "FROM k_term_pago a ";
	$Query .= "LEFT JOIN k_term b ON b.fl_term=a.fl_term ";
	$Query .= "LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
	$Query .= "WHERE CURDATE() = DATE(a.fe_pago ) ";
	$rs = EjecutaQuery($Query);

	while($row=RecuperaRegistro($rs)){
		$fl_term_pago = $row[0];
		$fl_term = $row[1];
		$no_opcion = $row[2];
		$no_pago = $row[3];
		$py_date = $row[4];
		$pg_name = $row[5];

		# For each term, find all the active students
		$Query  = "SELECT DISTINCT(a.fl_alumno), b.ds_nombres, CASE c.fg_opcion_pago WHEN 1 THEN c.mn_a_due WHEN 2 THEN c.mn_b_due WHEN 3 THEN c.mn_c_due WHEN 4 THEN c.mn_d_due END py_amount, b.ds_email, b.ds_apaterno ";
    $Query .= ", (SELECT fl_sesion FROM c_sesion r WHERE r.cl_sesion=b.cl_sesion), b.cl_sesion ";
		$Query .= "FROM k_alumno_term a ";
		$Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_alumno ";
		$Query .= "LEFT JOIN k_app_contrato c ON c.cl_sesion=b.cl_sesion ";
		$Query .= "WHERE a.fl_term=$fl_term ";
		$Query .= "AND c.fg_opcion_pago=$no_opcion ";
		$Query .= "AND b.fg_activo='1' ";
		//$Query .= "AND b.fl_usuario <>3833  ";
		$rs2 = EjecutaQuery($Query);

		while($row2=RecuperaRegistro($rs2)){
			$fl_alumno = $row2[0];
			$st_fname = $row2[1];
			$py_amount = $row2[2];
			$ds_email = $row2[3];
      $st_lname = $row2[4];
      $fl_sesion = $row2[5];
      $cl_sesion = $row2[6];
			# Check if the student has paid or not
            # Check if the student has paid or not
            $Query  = "SELECT COUNT(*) FROM k_alumno_pago a
                        JOIN k_term_pago b ON b.fl_term_pago= a.fl_term_pago
                        WHERE a.fl_alumno=$fl_alumno AND b.fl_term=$fl_term ";
			$row3 = RecuperaValor($Query);
			//copia a mike
			//$ds_email_mike="mike@vanas.ca";
			//$email_mike = SendNoticeMail($client, $from, $ds_email_mike, "", "Tuition Payment Due", "Query:$Query-->".$row3[0]);
			# If have not paid, send out reminder
			if(empty($row3[0])){
				$variables = array(
					"st_fname" => $st_fname,
					"st_lname" => $st_lname,
					"pg_name" => $pg_name,
					"py_date" => $py_date,
					"py_amount" => $py_amount
				);
				# Generate the email template with the variables
				$ds_email_template = GenerateTemplate($ds_template, $variables);
				$ds_email_template = str_replace("#link_payment#", "https://campus.vanas.ca/modules/students_new/index.php#ajax/payment_history.php", $ds_email_template);


            # Si tiene una persona responsable enviara la notificacion
        $ds_email_r = "";
        if(ExisteEnTabla('k_presponsable','cl_sesion', $cl_sesion)){
          $h_respo = genera_documento($fl_sesion, 1, 38);
          $b_respo = genera_documento($fl_sesion, 2, 38);
          $f_respo = genera_documento($fl_sesion, 3, 38);
          $ds_email_template_rep = $h_respo.$b_respo.$f_respo;
          $row4 = RecuperaValor("SELECT ds_email_r FROM k_presponsable WHERE cl_sesion='$cl_sesion'");
          $ds_email_r = $row4[0];
          if(!empty($ds_email_r)){
              SendNoticeMail($client, $from, $ds_email_r, "", "Tuition Payment Due Responsible", $ds_email_template_rep);
          }
        }
        # Si hay persona responsable se le enviara copia
        $email = SendNoticeMail($client, $from, $ds_email, "", "Tuition Payment Due", $ds_email_template);

		//copia a mike
		//$ds_email_mike="mike@vanas.ca";
		//$email_mike = SendNoticeMail($client, $from, $ds_email_mike, "", "Tuition Payment Due", $ds_template_html);

        # Guardamos si el correo se envio
        if(!empty($email))
          $emails = "Y";
        else
          $emails = "N";
        EjecutaQuery("INSERT INTO k_envio_cronjob(fl_usuario, ds_mensaje, fe_cron, fg_enviado, fl_template) VALUES(".$fl_alumno.", 'template 18', NOW(), '".$emails."', 18)");
			}
		}
	}
?>
