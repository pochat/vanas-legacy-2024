<?php
if (PHP_OS == 'Linux') {
    # Include campus libraries
    require '/var/www/html/vanas/lib/com_func.inc.php';
    require '/var/www/html/vanas/lib/sp_config.inc.php';

    # Include AWS SES libraries
    require '/var/www/html/vanas/AWS_SES/PHP/com_email_func.inc.php';
} else {

    require '../lib/com_func.inc.php';
    require '../lib/sp_config.inc.php';
    require '../AWS_SES/PHP/com_email_func.inc.php';

}
	$from = 'noreply@vanas.ca';
	$day_advance = 7;//7 siempre debe ser 7 dias.

	# Prepare email templates for assignment reminders, note: (change nb_template='___' to fl_template=id once this is stable on production server)
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='17' AND fg_activo='1'";
	$tu_upcoming_template = RecuperaValor($Query);
	$ds_template = str_uso_normal($tu_upcoming_template[0].$tu_upcoming_template[1].$tu_upcoming_template[2]);

	# Create a DOM object
#	$ds_template_html = new simple_html_dom();

	# Upcoming tuition payment, find terms that have tuition due in $day_advance day(s)
	$Query  = "SELECT a.fl_term_pago, a.fl_term, a.no_opcion, a.no_pago, DATE_FORMAT(a.fe_pago, '%M %e, %Y'), c.nb_programa,b.fl_term_ini  ";
	$Query .= "FROM k_term_pago a ";
	$Query .= "LEFT JOIN k_term b ON b.fl_term=a.fl_term ";
	$Query .= "LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
	$Query .= "WHERE DATE_ADD(CURDATE(), INTERVAL $day_advance DAY) = DATE(a.fe_pago) ";
    #$Query .= "AND fl_term_pago=601142 ";
	$rs = EjecutaQuery($Query);

	while($row=RecuperaRegistro($rs)){
		$fl_term_pago = $row[0];
		$fl_term = $row[1];
		$no_opcion = $row[2];
		$no_pago = $row[3];
		$py_date = $row[4];
		$pg_name = $row[5];
		$fl_term_ini=$row[6];

		#Cuando tiene term inicial se toma el term inicial.(para envitar envio de email erroneos antes de las fechas correspondientes)
		if(!empty($fl_term_ini)){
			$fl_term=$fl_term_ini;

            $Query="SELECT fl_term_pago,DATE_FORMAT(fe_pago, '%M %e, %Y') FROM k_term_pago WHERE fl_term=$fl_term and no_opcion=$no_opcion and DATE(CURDATE()) <=DATE(fe_pago) ";
            $row=RecuperaValor($Query);
            $fl_term_pago=$row['fl_term_pago'];
            $py_date=$row[1];


        }


		# For each term, find all the active students, match student's chosen payment option with the payment option from k_term_pago
		$Query  = "SELECT DISTINCT(a.fl_alumno), b.ds_nombres, CASE c.fg_opcion_pago WHEN 1 THEN c.mn_a_due WHEN 2 THEN c.mn_b_due WHEN 3 THEN c.mn_c_due WHEN 4 THEN c.mn_d_due END py_amount, b.ds_email, b.ds_apaterno ";
		$Query .= "FROM k_alumno_term a ";
		$Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_alumno ";
		$Query .= "LEFT JOIN k_app_contrato c ON c.cl_sesion=b.cl_sesion ";
		$Query .= "WHERE a.fl_term=$fl_term ";
		$Query .= "AND c.fg_opcion_pago=$no_opcion ";
		$Query .= "AND b.fg_activo='1' ";
		//$Query .= "AND b.fl_usuario<>3833 AND b.fl_usuario<>7228 AND b.fl_usuario<>7247 AND b.fl_usuario<>6809 ";//Dilan lawor Shelby| Nadine Higgins |Nadine Higgins
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
			if(empty($row3[0]))
            {
				$variables = array(
					"st_fname" => $st_fname,
					"pg_name" => $pg_name,
					"py_date" => $py_date,
					"py_amount" => $py_amount,
          "st_lname" => $st_lname
				);

            # Generate the email template with the variables
				$ds_email_template = GenerateTemplate($ds_template, $variables);
                $ds_email_template = str_replace("#link_payment#", "https://campus.vanas.ca/modules/students_new/index.php#ajax/payment_history.php", $ds_email_template);

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
          $ds_email_template_rep = $h_respo.$b_respo.$f_respo;
          $row4 = RecuperaValor("SELECT ds_email_r FROM k_presponsable WHERE cl_sesion='$cl_sesion'");
          $ds_email_r = $row4[0];
          if(!empty($ds_email_r)){
           //   SendNoticeMail($client, $from, $ds_email_r, "", "Tuition Payment Due Responsible", $ds_email_template_rep);
             EnviaMailHTML('', $from, $ds_email_r, "Tuition Payment Due Responsible", $ds_email_template_rep);
          }
        }
        # Si existe una persona responsablele enviamos copia de la notificacion
        #$email = SendNoticeMail($client, $from, $ds_email, $ds_email_r, "Upcoming Tuition Payment", $ds_template_html);

        $email = EnviaMailHTML('', $from, $ds_email, "Upcoming Tuition Payment", $ds_email_template);


            # Guardamos si el correo se envio
        if(!empty($email))
          $emails = "Y";
        else
          $emails = "N";
        EjecutaQuery("INSERT INTO k_envio_cronjob(fl_usuario, ds_mensaje, fe_cron, fg_enviado, fl_template) VALUES(".$fl_alumno.", '".str_html_bd($ds_template)."', NOW(), '".$emails."', 17)");
			}
		}
	}


	/*

	#Notificacviones extras, personalizadas, solo aplica a notificaciones personalizadas.
    $Query="SELECT fl_term_pago, no_opcion, no_pago, DATE_FORMAT(fe_pago,'%b %d, %Y'), mn_c_due, DATEDIFF(a.fe_pago, '2020-01-21') no_dias ,(SELECT nb_periodo FROM c_periodo l, k_term s WHERE l.fl_periodo=s.fl_periodo AND s.fl_term=a.fl_term) terms FROM k_term_pago a, k_app_contrato b WHERE fl_term=686 AND no_opcion=3 AND no_contrato=1 AND cl_sesion='3ba9a12ce3431e853bb85bc04c2e4de68a98d09bc2861fa9220528afd89aaf90' AND no_pago>3  ORDER BY no_pago ";
    $Query="SELECT * FROM c_usuario WHERE fl_usuario=2828 ";#Hanna Thomson
    $row=RecuperaValor($Query);
    $st_fname=$row['ds_nombres'];
    $pg_name="Concept Art Diploma ";
    $py_date="Dec 19, 2019";
    $py_amount="4089.00";
    $st_lname=$row['ds_apaterno'];


    $variables = array(
					"st_fname" => $st_fname,
					"pg_name" => $pg_name,
					"py_date" => $py_date,
					"py_amount" => $py_amount,
                    "st_lname" => $st_lname);
    $qUERY="SELECT COUNT(*)
             FROM k_envio_cronjob
		     WHERE fe_alta>= DATE_SUB(NOW(), INTERVAL DAYOFWEEK(NOW())-1 DAY)
	         AND fe_alta<=DATE_ADD(NOW(), INTERVAL 7-DAYOFWEEK(NOW()) DAY) ";
    $date=RecuperaValor($qUERY);
    $no_reg=$date[0];

    if(empty($no_reg)){

        $ds_email_template = GenerateTemplate($ds_template, $variables);
        # Load the template into html
        $ds_template_html->load($ds_email_template);
        # Get base url (domain)
        $base_url = $ds_template_html->getElementById("login-redirect")->href;
        # Set url path and query string
        $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/students_new/index.php#ajax/payment_history.php";

        # Obtenemos la informacion del students
        $row5 = RecuperaValor("SELECT m.cl_sesion, (SELECT fl_sesion FROM c_sesion r WHERE r.cl_sesion=m.cl_sesion) FROM c_usuario m WHERE m.fl_usuario=2828 ");
        $cl_sesion =$row5[0];
        $fl_sesion =$row5[1];
        $ds_email_r = "";


        # Si tiene una persona responsable enviara la notificacion
        if(ExisteEnTabla('k_presponsable','cl_sesion', $cl_sesion)){
          $h_respo = genera_documento($fl_sesion, 1, 38);
          $b_respo = genera_documento($fl_sesion, 2, 38);
          $f_respo = genera_documento($fl_sesion, 3, 38);
          $ds_email_template_rep = $h_respo.$b_respo.$f_respo;
          $row4 = RecuperaValor("SELECT ds_email_r FROM k_presponsable WHERE cl_sesion='$cl_sesion'");
          $ds_email_r = $row4[0];
          if(!empty($ds_email_r)){}
            SendNoticeMail($client, $from, $ds_email_r, "", "Tuition Payment Due Responsible", $ds_email_template_rep);
        }
        # Si existe una persona responsablele enviamos copia de la notificacion
        $email = SendNoticeMail($client, $from, $ds_email, $ds_email_r, "Upcoming Tuition Payment", $ds_template_html);

        # Guardamos si el correo se envio
        if(!empty($email))
          $emails = "Y";
        else
          $emails = "N";

        EjecutaQuery("INSERT INTO k_envio_cronjob(fl_usuario, ds_mensaje, fe_cron, fg_enviado, fl_template)
                                       VALUES(2828,'AVISO Upcoming Tuition Payment', NOW(), '$emails', 17)    ");



    }
*/







?>
