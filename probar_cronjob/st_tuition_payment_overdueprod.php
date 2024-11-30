<?php
	# Include campus libraries 
	require '/var/www/html/vanas/lib/com_func.inc.php';
	require '/var/www/html/vanas/lib/sp_config.inc.php';

	# Include AWS SES libraries
	require '/var/www/html/AWS_SES/PHP/com_email_func.inc.php';
	require '/var/www/html/AWS_SES/aws/aws-autoloader.php';
	use Aws\Common\Aws;

	# Include html parser
	require '/var/www/html/vanas/modules/common/new_campus/lib/simple_html_dom.php';

	# Load config file
	$aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');

	# Get the client from the builder by namespace
	$client = $aws->get('Ses');

	$from = 'noreply@vanas.ca';
	# Define the interval of time where overdue payments should be sent (daily)
	$day_late = 1;
	//$day_late_app = 3;
	$day_late_period = 15;

	# Prepare email templates for assignment reminders, note: (change nb_template='___' to fl_template=id once this is stable on production server)
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='19' AND fg_activo='1'";
	$tu_overdue_template = RecuperaValor($Query);
	$ds_template = str_uso_normal($tu_overdue_template[0].$tu_overdue_template[1].$tu_overdue_template[2]);

	# Create a DOM object
	$ds_template_html = new simple_html_dom();

	# Tuition payment overdue, find terms that have tuition overdue in between $day_late and $day_late_period day(s)
	$Query  = "SELECT a.fl_term_pago, a.fl_term, a.no_opcion, a.no_pago, DATE_FORMAT(a.fe_pago, '%M %e, %Y'), c.nb_programa ";
	$Query .= "FROM k_term_pago a ";
	$Query .= "LEFT JOIN k_term b ON b.fl_term=a.fl_term ";
	$Query .= "LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
	$Query .= "WHERE DATE_SUB(CURDATE(), INTERVAL $day_late DAY) >= DATE(a.fe_pago) ";
	$Query .= "AND DATE_SUB(CURDATE(), INTERVAL $day_late_period DAY) <= DATE(a.fe_pago) ";
	$rs = EjecutaQuery($Query);
	while($row=RecuperaRegistro($rs)){
		$fl_term_pago = $row[0];
		$fl_term = $row[1];
		$no_opcion = $row[2];
		$no_pago = $row[3];
		$py_date = $row[4];
		$pg_name = $row[5];

		# For each term, find all the active students, match student's chosen payment option with the payment option from k_term_pago	
    $Query  = "SELECT DISTINCT(a.fl_alumno), d.ds_nombres, CASE e.fg_opcion_pago WHEN 1 THEN e.mn_a_due WHEN 2 THEN e.mn_b_due ";
    $Query .= "WHEN 3 THEN e.mn_c_due WHEN 4 THEN e.mn_d_due END py_amount,d.ds_email, d.ds_apaterno FROM (k_alumno_grupo a, c_grupo b, k_term c) ";
    $Query .= "LEFT JOIN c_usuario d ON d.fl_usuario=a.fl_alumno LEFT JOIN k_app_contrato e ON e.cl_sesion = d.cl_sesion ";
    $Query .= "WHERE a.fl_grupo = b.fl_grupo AND b.fl_term = c.fl_term AND 
    ((c.no_grado='1' AND c.fl_term=$fl_term) OR (c.no_grado<>'1' AND fl_term_ini=$fl_term))
    AND e.fg_opcion_pago=$no_opcion AND d.fg_activo='1' ";

		$rs2 = EjecutaQuery($Query);

		while($row2=RecuperaRegistro($rs2)){
			$fl_alumno = $row2[0];
			$st_fname = $row2[1];
			$py_amount = $row2[2];
			$ds_email = $row2[3];
			$st_lname = $row2[4];

			# Check if the student has paid or not
			$Query  = "SELECT COUNT(1) FROM k_alumno_pago WHERE fl_alumno=$fl_alumno AND fl_term_pago=$fl_term_pago ";
			$row3 = RecuperaValor($Query);

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

				# Load the template into html
				$ds_template_html->load($ds_email_template);
				# Get base url (domain)
				$base_url = $ds_template_html->getElementById("login-redirect")->href;
				# Set url path and query string
				$ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/students_new/index.php#ajax/payment_history.php";				

        # Obtenemos la informacion del students
        $row5 = RecuperaValor("SELECT m.cl_sesion, (SELECT fl_sesion FROM c_sesion r WHERE r.cl_sesion=m.cl_sesion) FROM c_usuario m WHERE m.fl_usuario=$fl_alumno ");
        $cl_sesion =$row5[0];
        $fl_sesion =$row5[1];
        $ds_email_r = "";
        # Si tiene una persona responsable enviara la notificacion        
        if(ExisteEnTabla('k_presponsable','cl_sesion', $cl_sesion)){
          $h_respo = genera_documento($fl_sesion, 1, 38);
          $b_respo = genera_documento($fl_sesion, 2, 38);
          $f_respo = genera_documento($fl_sesion, 3, 38);
          echo $ds_email_template_rep = $h_respo.$b_respo.$f_respo;          
          $row4 = RecuperaValor("SELECT ds_email_r FROM k_presponsable WHERE cl_sesion='$cl_sesion'");
          $ds_email_r = $row4[0];
          // SendNoticeMail($client, $from, $ds_email_r, "", "Tuition Payment Due Responsable", $ds_email_template_rep);
        }        
        # Si existe una persona responsablele enviamos copia de la notificacion
        // $email = SendNoticeMail($client, $from, $ds_email, $ds_email_r, "Tuition Payment Overdue", $ds_template_html);
        
        // # Guardamos si el correo se envio
        // if(!empty($email))
          // $emails = "Y";
        // else
          // $emails = "N";
        // EjecutaQuery("INSERT INTO k_envio_cronjob(fl_usuario, ds_mensaje, fe_cron, fg_enviado, fl_template) VALUES(".$fl_alumno.", '".str_html_bd($ds_template_html)."', NOW(), '".$emails."', 19)");
			}
		}
	}
?>
