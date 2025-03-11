<?php
if (PHP_OS == 'Linux') {
    require '/var/www/html/vanas/lib/com_func_cronjobs.inc.php';
    require '/var/www/html/vanas/lib/sp_config.inc.php';

    # Include AWS SES libraries
    require '/var/www/html/vanas/AWS_SES/PHP/com_email_func.inc.php';
} else {

    require '../lib/com_func_cronjobs.inc.php';
    require '../lib/sp_config.inc.php';
    require '../AWS_SES/PHP/com_email_func.inc.php';
}
	$dom = new DOMDocument();
	libxml_use_internal_errors(true); // Para suprimir errores relacionados con HTML mal formado

	$from = 'noreply@vanas.ca';
	$day_late_start = 1;
	$day_late_end = 15;

	# Prepare email templates for assignment reminders, note: (change nb_template='___' to fl_template=id once this is stable on production server)
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE nb_template='Missing Grades' AND fg_activo='1'";
	$te_grade_template = RecuperaValor($Query);
	$ds_template = str_uso_normal($te_grade_template[0].$te_grade_template[1].$te_grade_template[2]);


	# Find all groups within the given time period
	$Query  = "SELECT a.fl_semana, b.fl_grupo, (d.ds_nombres) te_fname, c.no_semana, c.ds_titulo, DATE_FORMAT(a.fe_calificacion, '%M %e, %Y') fe_date, b.nb_grupo, d.ds_email, (d.ds_apaterno) te_lname  ";
	$Query .= "FROM k_semana a ";
	$Query .= "LEFT JOIN c_grupo b ON b.fl_term=a.fl_term ";
	$Query .= "LEFT JOIN c_leccion c ON c.fl_leccion=a.fl_leccion ";
	$Query .= "LEFT JOIN c_usuario d ON d.fl_usuario=b.fl_maestro ";
	$Query .= "WHERE DATE_SUB(CURDATE(), INTERVAL $day_late_start DAY) >= a.fe_calificacion ";
	$Query .= "AND DATE_SUB(CURDATE(), INTERVAL $day_late_end DAY) <= a.fe_calificacion ";
	$Query .= "AND b.fl_term IS NOT NULL ";
	$Query .= "AND (c.fg_animacion!='0' OR c.fg_ref_animacion!='0' OR c.no_sketch>0 OR c.fg_ref_sketch!='0') AND d.fg_activo='1' ";
  // verifa que el grupo tenga alumnos
  $Query .= "AND (SELECT COUNT(1) FROM k_alumno_grupo r WHERE r.fl_grupo=b.fl_grupo)>0  ";
  $Query .="AND no_semana<>12 ";
	$rs = EjecutaQuery($Query);

	while($row=RecuperaRegistro($rs)){
		$fl_semana = $row[0];
		$fl_grupo = $row[1];
		$te_fname = $row[2];
		$no_week = $row[3];
		$ds_title = $row[4];
		$fe_date = $row[5];
		$nb_group = $row[6];
		$ds_email = $row[7];
		$te_lname = $row[8];

		# Check if the week's grade has been assigned
		$Query  = "SELECT (b.ds_nombres) st_fname, (b.ds_apaterno) st_lname,b.fl_usuario ";
		$Query .= "FROM k_entrega_semanal a ";
		$Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_alumno ";
		$Query .= "WHERE fl_semana=$fl_semana AND fl_grupo=$fl_grupo ";
		$Query .= "AND (fl_promedio_semana IS NULL OR fl_promedio_semana='0') ";
		$Query .= "AND b.fg_activo='1' AND b.fl_usuario<>475 ";
		$rs2 = EjecutaQuery($Query);

		while($row2=RecuperaRegistro($rs2)){
			$st_fname = $row2[0];
			$st_lname = $row2[1];
			$fl_usuario=$row2[2];



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






			$variables = array(
				"st_fname" => $st_fname,
				"st_lname" => $st_lname,
				"te_fname" => $te_fname,
				"te_lname" => $te_lname,
				"no_week" => $no_week,
				"ds_title" => $ds_title,
				"fe_date" => $fe_date,
				"fe_time" => "",
				"nb_group" => $nb_group
			);
			# Generate the email template with the variables
			$ds_email_template = GenerateTemplate($ds_template, $variables);


			$dom->loadHTML($ds_email_template);
			$link = $dom->getElementById('login-redirect');
			if ($link) {
				// Cambiar el atributo href
				$link->setAttribute('href', 'https://campus.vanas.ca/modules/teachers_new/index.php#ajax/submitted_assignments.php');
			}
			$ds_email_template = $dom->saveHTML();


        #Se envia copia de emmail si el studen asi lo elige.
		    //if($fg_copy_email_alternativo)
			  // SendNoticeMail($client, $from, $fg_copy_email_alternativo, "", "Missing Assignment Grade", $ds_template_html);

		    if($fg_copy_email_responsable)
			   $ds_email_responsable=$ds_email_responsable;
		    else
			   $ds_email_responsable="";

			//mike
			//SendNoticeMail($client, $from, "mike@vanas.ca", "", "Missing Assignment Grade 4", $ds_template_html);
			//SendNoticeMail($client, $from, $ds_email, "".$ds_email_responsable."", "Missing Assignment Grade", $ds_template_html);
            EnviaMailHTML('', $from, $ds_email, "Missing Assignment Grade", $ds_template_html);

    }
	}
?>
