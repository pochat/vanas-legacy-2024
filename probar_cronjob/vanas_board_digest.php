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

	# The day to send Vanas digest set by school admin
	$day = ObtenConfiguracion(70);

	# Get today's day
	$Query = "SELECT DAYNAME(CURDATE())";
	$row = RecuperaValor($Query);
	$today = $row[0];

	# Limit the amount of posts
	$limit = 6;

	# Do not send this notification, if days don't match
	if(strcasecmp($day, $today) != 0){ exit; }

	# Prepare email templates for assignment reminders, note: (change nb_template='___' to fl_template=id once this is stable on production server)
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE nb_template='Weekly Vanas board digest' AND fg_activo='1'";
	$board_digest_template = RecuperaValor($Query);
	$ds_template = str_uso_normal($board_digest_template[0].$board_digest_template[1].$board_digest_template[2]);

	# Create a DOM object
	$ds_template_html = new simple_html_dom();
	# Load the template as HTML
	$ds_template_html->load($ds_template);

	# The board container from template
	$table = $ds_template_html->getElementById("board-container");

	# Search for latest posts within the last 2 weeks
	$Query  = "SELECT b.ds_nombres, b.ds_apaterno, fl_gallery_post, nb_tema, board_table.fl_usuario, fl_entregable, DATE_FORMAT(fe_post, '%M %e, %Y'), nb_archivo ";
	$Query .= "FROM ( ";
	$Query .= "		SELECT fl_gallery_post, fl_tema, fl_usuario, fl_entregable, fe_post, nb_archivo ";
	$Query .= "		FROM k_gallery_post ";
	$Query .= "		WHERE fl_entregable IS NOT NULL ";
	$Query .= "		AND fe_post >= DATE_SUB(CURDATE(), INTERVAL 2 WEEK) ";
	$Query .= "		AND nb_archivo LIKE '%.jpg' ";
	$Query .= "		ORDER BY fe_post DESC ";
	$Query .= ") AS board_table ";
	$Query .= "LEFT JOIN c_usuario b ON b.fl_usuario = board_table.fl_usuario ";
	$Query .= "LEFT JOIN c_f_tema c ON c.fl_tema=board_table.fl_tema ";
	$Query .= "WHERE b.fg_activo='1' ";
	$Query .= "GROUP BY board_table.fl_usuario ORDER BY fe_post DESC LIMIT $limit ";
	$rs = EjecutaQuery($Query);

	# Create the template
	for($i=0; $row=RecuperaRegistro($rs); $i++){
		$ds_nombres = $row[0];
		$ds_apaterno = $row[1];
		$fl_gallery_post = $row[2];
		$nb_tema = $row[3];
		$fl_usuario = $row[4];
		$fl_entregable = $row[5];
		$fe_post = $row[6];
		$nb_archivo = $row[7];

		# Country
		$Query  = "SELECT ds_pais ";
		$Query .= "FROM c_usuario a ";
		$Query .= "LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
		$Query .= "LEFT JOIN c_pais c ON c.fl_pais=b.ds_add_country ";
		$Query .= "WHERE fl_usuario=$fl_usuario ";
		$row2 = RecuperaValor($Query);
		$ds_pais = $row2[0];

		# Term
		$Query  = "SELECT c.no_grado ";
		$Query .= "FROM k_alumno_grupo a ";
		$Query .= "LEFT JOIN c_grupo b ON b.fl_grupo=a.fl_grupo ";
		$Query .= "LEFT JOIN k_term c ON c.fl_term=b.fl_term ";
		$Query .= "WHERE fl_alumno=$fl_usuario ";
		$row2 = RecuperaValor($Query);
		$no_grado = $row2[0];

		# Type of assignment and Week
		$Query  = "SELECT a.fg_tipo, d.no_semana ";
		$Query .= "FROM k_entregable a ";
		$Query .= "LEFT JOIN k_entrega_semanal b ON b.fl_entrega_semanal=a.fl_entrega_semanal ";
		$Query .= "LEFT JOIN k_semana c ON c.fl_semana=b.fl_semana ";
		$Query .= "LEFT JOIN c_leccion d ON d.fl_leccion=c.fl_leccion ";
		$Query .= "WHERE a.fl_entregable=$fl_entregable ";
		$row2 = RecuperaValor($Query);
		$fg_tipo = $row2[0];
		$no_semana = $row2[1];

		switch($fg_tipo) {
			case "A":		$fg_tipo = "Assignment";  break;
	    case "AR":	$fg_tipo = "Assignment Reference"; break;
	    case "S":   $fg_tipo = "Sketch";  break;
	    case "SR":	$fg_tipo = "Sketch Reference"; break;
		}

		# Find a td block
		$td = $table->find("td", $i);

		# Create a post, add it to a table cell
		$td->innertext = "
			<div style='border:1px solid #E3E3E3; background-color: #FFF; padding: 10px;'>
				<a href='http://".ObtenConfiguracion(60).PATH_N_ALU."/index.php#ajax/gallery.php?post=$fl_gallery_post' target='_blank'>
					<img src='http://".ObtenConfiguracion(60).PATH_ALU."/sketches/board_thumbs/$nb_archivo'>
				</a>
				<div style='border-top:1px solid #E3E3E3; margin-top:10px; padding: 10px; text-align: right;'>
					<span style='color: #3276b1; font-size: 17px; font-weight: bold; line-height: 1.1;'>$ds_nombres $ds_apaterno</span><br>
					<span style='color: #3276b1; font-size: 13px; line-height: 1.428571429;'>$ds_pais</span><br>
					<span style='font-size: 18px; font-weight: 500; line-height: 1.1; word-wrap: break-word;'>$nb_tema</span><br>
					<span style='color: #3276b1; font-size: 13px; line-height: 1.428571429;'>Desktop<br> Week $no_semana - $fg_tipo - Term $no_grado</span><br>
					<span style='color: #999; font-size: 13px; line-height: 1.428571429;'>$fe_post</span>
				</div>
			</div>
			";
	}
	# Save the html template back to string
	$ds_template = $ds_template_html->save();

	# Prepare list of target user
	$Query  = "SELECT b.ds_nombres, b.ds_apaterno, b.ds_email ";
	$Query .= "FROM c_alumno a ";
	$Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_alumno ";
	$Query .= "WHERE b.fg_activo='1' AND fl_perfil IS NOT NULL AND fl_perfil<>0 ";
	$students = EjecutaQuery($Query);

	# Send the digest to every student
	for($i=0; $student=RecuperaRegistro($students); $i++){
		$st_fname = $student[0];
		$st_lname = $student[1];
		$ds_email = $student[2];
		
		# Template variables
		$variables = array(
			"st_fname" => $st_fname,
			"st_lname" => "",
			"te_fname" => "",
			"te_lname" => "",
			"no_week" => "",
			"ds_title" => "",
			"fe_date" => "",
			"fe_time" => "",
			"nb_group" => ""
		);
		# Generate the email template with the variables
		$ds_template_email = GenerateTemplate($ds_template, $variables);

		SendNoticeMail($client, $from, $ds_email, "", "Weekly Vanas Board Digest", $ds_template_email);
	}
?>
