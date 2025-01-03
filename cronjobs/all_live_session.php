<?php
	# Include campus libraries

	if (PHP_OS == 'Linux') { # when is production
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
	$day_advance = 1;
	$dom = new DOMDocument();
	libxml_use_internal_errors(true); // Para suprimir errores relacionados con HTML mal formado


	# Prepare email templates for live session, note: (change nb_template='___' to fl_template=id once this is stable on production server)
	# Student mandatory live session template
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE nb_template='Live Session - Student' AND fg_activo='1'";
	$st_live_template = RecuperaValor($Query);
	$ds_st_live_template = str_uso_normal($st_live_template[0].$st_live_template[1].$st_live_template[2]);

	# Student extra live session template
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE nb_template='Live Session Extra - Student' AND fg_activo='1'";
	$st_extra_template = RecuperaValor($Query);
	$ds_st_extra_template = str_uso_normal($st_extra_template[0].$st_extra_template[1].$st_extra_template[2]);

	# Teacher mandatory live session template
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE nb_template='Live Session - Teacher' AND fg_activo='1'";
	$te_live_template = RecuperaValor($Query);
	$ds_te_live_template = str_uso_normal($te_live_template[0].$te_live_template[1].$te_live_template[2]);

	# Teacher extra live session template
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE nb_template='Live Session Extra - Teacher' AND fg_activo='1'";
	$te_extra_template = RecuperaValor($Query);
	$ds_te_extra_template = str_uso_normal($te_extra_template[0].$te_extra_template[1].$te_extra_template[2]);


	# Find all groups with upcoming mandatory live session in $day_advance day(s)
	$Query  ="(";
	$Query .= "SELECT a.fl_grupo, d.no_semana, d.ds_titulo, DATE_FORMAT(a.fe_clase, '%W') fe_day, DATE_FORMAT(a.fe_clase, '%M %e, %Y') fe_date, DATE_FORMAT(a.fe_clase, '%h:%i %p') fe_time ";
	$Query .= "FROM k_clase a ";
	$Query .= "LEFT JOIN c_grupo b ON a.fl_grupo=b.fl_grupo ";
	$Query .= "LEFT JOIN k_semana c ON a.fl_semana=c.fl_semana ";
	$Query .= "LEFT JOIN c_leccion d ON c.fl_leccion=d.fl_leccion ";
	$Query .= "WHERE DATE_ADD(CURDATE(), INTERVAL $day_advance DAY) = DATE_FORMAT(a.fe_clase, '%Y-%c-%d') ";
	$Query .= "AND fg_obligatorio='1' ";
	$Query .= ")UNION(";
    $Query .= "
                SELECT a.fl_grupo,c.no_semana,b.nb_grupo ds_titulo,
                DATE_FORMAT(a.fe_clase, '%W') fe_day, DATE_FORMAT(a.fe_clase, '%M %e, %Y') fe_date,
                DATE_FORMAT(a.fe_clase, '%h:%i %p') fe_time
                FROM k_clase_grupo a
                JOIN c_grupo b ON a.fl_grupo=b.fl_grupo
                LEFT JOIN k_semana_grupo c ON a.fl_semana_grupo=c.fl_semana_grupo
                WHERE DATE_ADD(CURDATE(), INTERVAL $day_advance DAY) = DATE_FORMAT(a.fe_clase, '%Y-%c-%d')
        ";
    $Query .= ") ";

	$rs = EjecutaQuery($Query);

	while($row=RecuperaRegistro($rs)){
		$fl_grupo = $row[0];
		$no_week = $row[1];
		$ds_title = $row[2];
		$fe_day = $row[3];
		$fe_date = $row[4];
		$fe_time = $row[5];

		# Find the students in this fl_grupo
		$Query  = "SELECT c.ds_nombres, c.ds_email,c.fl_usuario,c.cl_sesion  ";
		$Query .= "FROM c_grupo a ";
		$Query .= "LEFT JOIN k_alumno_grupo b ON a.fl_grupo=b.fl_grupo ";
		$Query .= "LEFT JOIN c_usuario c ON c.fl_usuario=b.fl_alumno ";
		$Query .= "WHERE a.fl_grupo=$fl_grupo ";
		$Query .= "AND c.fg_activo='1'";
		$rs2 = EjecutaQuery($Query);

		$tot_students = CuentaRegistros($rs2);
		if($tot_students > 0){
			# Send notice to the group of students
			while($row2=RecuperaRegistro($rs2)){
				$st_fname = $row2[0];
				$ds_email = $row2[1];
				$fl_usuario=$row2[2];
				$cl_sesion=$row2[3];


				#Verificamos si tiene marcado la copia,para envio de email

				#Recuperamos el email responsable alumno.
				$Query="SELECT  ds_email_r  FROM k_presponsable WHERE cl_sesion='$cl_sesion' ";
				$row=RecuperaValor($Query);
				$ds_email_responsable=!empty($row['ds_email_r'])?$row['ds_email_r']:NULL;

				$Query="SELECT ds_a_email FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
				$row=RecuperaValor($Query);
				$ds_email_alternative=$row['ds_a_email'];

				$Query="SELECT fg_copy_email_responsable,fg_copy_email_alternativo FROM c_alumno WHERE fl_alumno=$fl_usuario ";
				$row=RecuperaValor($Query);
				$fg_copy_email_responsable=$row[0];
				$fg_copy_email_alternativo=$row[1];



				$variables = array(
					"st_fname" => $st_fname,
					"st_lname" => "",
					"te_fname" => "",
					"te_lname" => "",
					"no_week" => $no_week,
					"ds_title" => $ds_title,
					"fe_day" => $fe_day,
					"fe_date" => $fe_date,
					"fe_time" => $fe_time,
					"nb_group" => ""
				);
				# Generate the email template with the variables
				$ds_email_template = GenerateTemplate($ds_st_live_template, $variables);

				$dom->loadHTML($ds_email_template);
				$link = $dom->getElementById('login-redirect');
				if ($link) {
					// Cambiar el atributo href
					$link->setAttribute('href', 'https://campus.vanas.ca/modules/students_new/index.php#ajax/home.php');
				}
				$ds_email_template = $dom->saveHTML();

					#Se envia copia de emmail si el studen asi lo elige.
            if ($fg_copy_email_alternativo) {
                //SendNoticeMail($client, $from, $ds_email_alterative, "", "Upcoming Live Session", $ds_email_template);
				EnviaMailHTML('', $from, $ds_email_alterative, "Upcoming Live Session", $ds_email_template);
            }
            if($fg_copy_email_responsable)
					$ds_email_responsable=$ds_email_responsable;
				else
					$ds_email_responsable="";

				//SendNoticeMail($client, $from, $ds_email, "".$ds_email_responsable."", "Upcoming Live Session", $ds_email_template);
				EnviaMailHTML('', $from, $ds_email, "Upcoming Live Session", $ds_email_template);
            }

			# Send notice to the instructor of the group
			$Query  = "SELECT b.ds_nombres, b.ds_email, b.ds_apaterno ";
			$Query .= "FROM c_grupo a ";
			$Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_maestro ";
			$Query .= "WHERE a.fl_grupo=$fl_grupo";
			$row3 = RecuperaValor($Query);
			$te_fname = $row3[0];
			$ds_email = $row3[1];
      $te_lname = $row3[2];

			$variables = array(
				"st_fname" => "",
				"st_lname" => "",
				"te_fname" => $te_fname,
				"te_lname" => $te_lname,
				"no_week" => $no_week,
				"ds_title" => $ds_title,
				"fe_day" => $fe_day,
				"fe_date" => $fe_date,
				"fe_time" => $fe_time,
				"nb_group" => ""
			);
			# Generate the email template with the variables
			$ds_email_template = GenerateTemplate($ds_te_live_template, $variables);

			$dom->loadHTML($ds_email_template);
			$link = $dom->getElementById('login-redirect');
			if ($link) {
				// Cambiar el atributo href
				$link->setAttribute('href', 'https://campus.vanas.ca/modules/teachers_new/index.php#ajax/home.php');
			}
			$ds_email_template = $dom->saveHTML();

			//SendNoticeMail($client, $from, $ds_email, "", "Upcoming Live Session", $ds_email_template);
			EnviaMailHTML('', $from, $ds_email, "Upcoming Live Session", $ds_email_template);
		}
	}

	# Find all groups with upcoming extra live session in $day_advance day(s)
	$Query  = "SELECT a.fl_grupo, d.no_semana, d.ds_titulo, DATE_FORMAT(a.fe_clase, '%W') fe_day, DATE_FORMAT(a.fe_clase, '%M %e, %Y') fe_date, DATE_FORMAT(a.fe_clase, '%h:%i %p') fe_time ";
	$Query .= "FROM k_clase a ";
	$Query .= "LEFT JOIN c_grupo b ON a.fl_grupo=b.fl_grupo ";
	$Query .= "LEFT JOIN k_semana c ON a.fl_semana=c.fl_semana ";
	$Query .= "LEFT JOIN c_leccion d ON c.fl_leccion=d.fl_leccion ";
	$Query .= "WHERE DATE_ADD(CURDATE(), INTERVAL $day_advance DAY) = DATE_FORMAT(a.fe_clase, '%Y-%c-%d') ";
	$Query .= "AND a.fg_adicional='1'";
	$rs = EjecutaQuery($Query);

	while($row=RecuperaRegistro($rs)){
		$fl_grupo = $row[0];
		$no_week = $row[1];
		$ds_title = $row[2];
		$fe_day = $row[3];
		$fe_date = $row[4];
		$fe_time = $row[5];

		# Find the students in this fl_grupo
		$Query  = "SELECT c.ds_nombres, c.ds_email ";
		$Query .= "FROM c_grupo a ";
		$Query .= "LEFT JOIN k_alumno_grupo b ON a.fl_grupo=b.fl_grupo ";
		$Query .= "LEFT JOIN c_usuario c ON c.fl_usuario=b.fl_alumno ";
		$Query .= "WHERE a.fl_grupo=$fl_grupo ";
		$Query .= "AND c.fg_activo='1'";
		$rs2 = EjecutaQuery($Query);

		$tot_students = CuentaRegistros($rs2);
		if($tot_students > 0){
			# Send notice to the group of students
			while($row2=RecuperaRegistro($rs2)){
				$st_fname = $row2[0];
				$ds_email = $row2[1];

				$variables = array(
					"st_fname" => $st_fname,
					"st_lname" => "",
					"te_fname" => "",
					"te_lname" => "",
					"no_week" => $no_week,
					"ds_title" => $ds_title,
					"fe_day" => $fe_day,
					"fe_date" => $fe_date,
					"fe_time" => $fe_time,
					"nb_group" => ""
				);
				# Generate the email template with the variables
				$ds_email_template = GenerateTemplate($ds_st_extra_template, $variables);

				$dom->loadHTML($ds_email_template);
				$link = $dom->getElementById('login-redirect');
				if ($link) {
					// Cambiar el atributo href
					$link->setAttribute('href', 'https://campus.vanas.ca/modules/students_new/index.php#ajax/home.php');
				}
				$ds_email_template = $dom->saveHTML();
            //SendNoticeMail($client, $from, $ds_email, "", "Upcoming Extra Live Session", $ds_email_template);
				EnviaMailHTML('', $from, $ds_email, "Upcoming Extra Live Session", $ds_email_template);
            }

			# Send notice to the instructor of the group
			$Query  = "SELECT b.ds_nombres, b.ds_email, b.ds_apaterno ";
			$Query .= "FROM c_grupo a ";
			$Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_maestro ";
			$Query .= "WHERE a.fl_grupo=$fl_grupo";
			$row3 = RecuperaValor($Query);
			$te_fname = $row3[0];
			$ds_email = $row3[1];
			$te_lname = $row3[2];

			$variables = array(
				"st_fname" => "",
				"st_lname" => "",
				"te_fname" => $te_fname,
				"te_lname" => $te_lname,
				"no_week" => $no_week,
				"ds_title" => $ds_title,
				"fe_day" => $fe_day,
				"fe_date" => $fe_date,
				"fe_time" => $fe_time,
				"nb_group" => ""
			);
			# Generate the email template with the variables
			$ds_email_template = GenerateTemplate($ds_te_extra_template, $variables);

			$dom->loadHTML($ds_email_template);
			$link = $dom->getElementById('login-redirect');
			if ($link) {
				// Cambiar el atributo href
				$link->setAttribute('href', 'https://campus.vanas.ca/modules/teachers_new/index.php#ajax/home.php');
			}
			$ds_email_template = $dom->saveHTML();
        //SendNoticeMail($client, $from, $ds_email, "", "Upcoming Extra Live Session", $ds_email_template);
			EnviaMailHTML('', $from, $ds_email, "Upcoming Extra Live Session", $ds_email_template);
    }
	}
?>
