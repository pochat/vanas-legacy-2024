<?php
if (PHP_OS == 'Linux') { # when is production
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
	$dom = new DOMDocument();
	libxml_use_internal_errors(true); // Para suprimir errores relacionados con HTML mal formado


	$from = 'noreply@vanas.ca';
	$day_advance = 1;

	# Prepare email templates for assignment reminders, note: (change nb_template='___' to fl_template=id once this is stable on production server)
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE nb_template='New Week Topic' AND fg_activo='1'";
	$st_week_template = RecuperaValor($Query);
	$ds_template = str_uso_normal($st_week_template[0].$st_week_template[1].$st_week_template[2]);

	# Find all student groups with upcoming new week topic in $day_advance day(s)
	$Query  = "SELECT b.fl_grupo, c.no_semana, c.ds_titulo, DATE_FORMAT(a.fe_publicacion, '%M %e, %Y') fe_date ";
	$Query .= "FROM k_semana a ";
	$Query .= "LEFT JOIN c_grupo b ON a.fl_term=b.fl_term ";
	$Query .= "LEFT JOIN c_leccion c ON a.fl_leccion=c.fl_leccion ";
	$Query .= "WHERE DATE_ADD(CURDATE(), INTERVAL $day_advance DAY) = a.fe_publicacion ";
	$rs = EjecutaQuery($Query);

	while($row=RecuperaRegistro($rs)){
		$fl_grupo = $row[0];
		$no_week = $row[1];
		$ds_title = $row[2];
		$fe_date = $row[3];

		if(!empty($fl_grupo)){
			# Find the students in this fl_grupo
			$Query  = "SELECT c.ds_nombres, c.ds_email,c.fl_usuario ";
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

					$variables = array(
						"st_fname" => $st_fname,
						"st_lname" => "",
						"te_fname" => "",
						"te_lname" => "",
						"no_week" => $no_week,
						"ds_title" => $ds_title,
						"fe_date" => $fe_date,
						"fe_time" => "",
						"nb_group" => ""
					);
					# Generate the email template with the variables
					$ds_email_template = GenerateTemplate($ds_template, $variables);

					$dom->loadHTML($ds_email_template);
					$link = $dom->getElementById('login-redirect');
					if ($link) {
						// Cambiar el atributo href
						$link->setAttribute('href', 'https://campus.vanas.ca'."/modules/students_new/index.php#ajax/desktop.php?"."week=".$no_week);
					}
					$ds_email_template = $dom->saveHTML();



					#Recuperamos el email responsable alumno.
					$Query="SELECT  ds_email_r  FROM k_presponsable a JOIN c_usuario b ON b.cl_sesion=a.cl_sesion WHERE b.fl_usuario=$fl_usuario ";
					$row=RecuperaValor($Query);
					$ds_email_responsable=$row['ds_email_r'];

					$Query="SELECT ds_a_email FROM k_app_contrato a JOIN c_usuario b ON b.cl_sesion=a.cl_sesion WHERE b.fl_usuario=$fl_usuario ";
					$row=RecuperaValor($Query);
					$ds_email_alternative=$row['ds_a_email'];

					$Query="SELECT fg_copy_email_responsable,fg_copy_email_alternativo FROM c_alumno WHERE fl_alumno=$fl_usuario ";
					$row=RecuperaValor($Query);
					$fg_copy_email_responsable=$row[0];
					$fg_copy_email_alternativo=$row[1];



					#Se envia copia de emmail si el studen asi lo elige.
					if ($fg_copy_email_alternativo) {
						//SendNoticeMail($client, $from, $ds_email_alternative, "", "New Week Topic", $ds_template_html);
						EnviaMailHTML('', $from, $ds_email_alternative, "New Week Topic", $ds_email_template);
                    }
					if($fg_copy_email_responsable)
					  $ds_email_responsable=$ds_email_responsable;
					else
					  $ds_email_responsable="";



					//SendNoticeMail($client, $from, $ds_email, "".$ds_email_responsable."", "New Week Topic", $ds_template_html);
					EnviaMailHTML('', $from, $ds_email, "New Week Topic", $ds_email_template, $ds_email_responsable);
                }
			}
		}
	}

?>
