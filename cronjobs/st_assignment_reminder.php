<?php
	# Include campus libraries
	require '../lib/com_func.inc.php';
	require '../lib/sp_config.inc.php';

	# Include AWS SES libraries
	require '../AWS_SES/PHP/com_email_func.inc.php';

	$from = 'noreply@vanas.ca';
	$day_advance = 1;
	$dom = new DOMDocument();
	libxml_use_internal_errors(true); // Para suprimir errores relacionados con HTML mal formado
	$day_advance = 1;

	# Prepare email templates for assignment reminders, note: (change nb_template='___' to fl_template=id once this is stable on production server)
	# Upcoming assignment deadline
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template=6 AND fg_activo='1'";
	$st_deadline_template = RecuperaValor($Query);
	$ds_deadline_template = str_uso_normal($st_deadline_template[0].$st_deadline_template[1].$st_deadline_template[2]);

	# Assignment overdue
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template=5 AND fg_activo='1'";
	$st_overdue_template = RecuperaValor($Query);
	$ds_overdue_template = str_uso_normal($st_overdue_template[0].$st_overdue_template[1].$st_overdue_template[2]);

	# Find all students with upcoming assignment deadline in $day_advance day(s)
	$Query  = "SELECT b.fl_entrega_semanal, d.ds_nombres, c.no_semana, c.ds_titulo, DATE_FORMAT(a.fe_entrega, '%W') fe_day, DATE_FORMAT(a.fe_entrega, '%M %e, %Y') fe_date, d.ds_email, ";
	$Query .= "c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch, ";
  $Query .= "DATE_FORMAT(a.fe_entrega, '%h:%i %p') fe_time,d.fl_usuario ";
	$Query .= "FROM k_semana a ";
	$Query .= "LEFT JOIN k_entrega_semanal b ON a.fl_semana=b.fl_semana ";
	$Query .= "LEFT JOIN c_leccion c ON a.fl_leccion=c.fl_leccion ";
	$Query .= "LEFT JOIN c_usuario d ON b.fl_alumno=d.fl_usuario ";
	$Query .= "WHERE DATE_ADD(DATE_FORMAT(CURDATE(),'%Y-%m-%d'), INTERVAL $day_advance DAY) = DATE_FORMAT(a.fe_entrega,'%Y-%m-%d') ";
	$Query .= "AND b.fg_entregado='0' ";
	$Query .= "AND (c.fg_animacion!='0' OR c.fg_ref_animacion!='0' OR c.no_sketch>0 OR c.fg_ref_sketch!='0') ";
	$Query .= "AND d.fg_activo='1' ";
	$rs = EjecutaQuery($Query);

	$tot_students = CuentaRegistros($rs);
	if($tot_students > 0){
		while($row=RecuperaRegistro($rs)){
			$fl_entrega_semanal = $row[0];
			$st_fname = $row[1];
			$no_week = $row[2];
			$ds_title = $row[3];
			$fe_day = $row[4];
			$fe_date = $row[5];
			$ds_email = $row[6];
      $fe_time = $row[11];

			# Assignment tabs
			$fg_animacion = $row[7];
		  $fg_ref_animacion = $row[8];
		  $no_sketch = $row[9];
		  $fg_ref_sketch = $row[10];

		  $fl_usuario=$row['fl_usuario'];

		  # Count student's uploaded assignment
			$row2 = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='A' AND fl_entrega_semanal=$fl_entrega_semanal");
		  $tot_assignment = $row2[0];
		  $row2 = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='AR' AND fl_entrega_semanal=$fl_entrega_semanal");
		  $tot_assignment_ref = $row2[0];
		  $row2 = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='S' AND fl_entrega_semanal=$fl_entrega_semanal");
		  $tot_sketch = $row2[0];
		  $row2 = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='SR' AND fl_entrega_semanal=$fl_entrega_semanal");
		  $tot_sketch_ref = $row2[0];

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




		  # Find missing tabs, if the tab is missing, add the name
		  $tabs = array();
		  if($fg_animacion == "1" AND $tot_assignment == 0){
		    $tabs[] = "Assignment";
		  }
		  if($fg_ref_animacion == "1" AND $tot_assignment_ref == 0){
		    $tabs[] = "Assignment Reference";
		  }
		  if($tot_sketch < $no_sketch){
		    $tabs[] = "Sketch";
		  }
		  if($fg_ref_sketch == "1" AND $tot_sketch_ref == 0){
		    $tabs[] = "Sketch Reference";
		  }
			$nb_tabs = implode(", ", $tabs);


			# Send notice to the group of students
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
				"nb_group" => "",
				"nb_tabs" => $nb_tabs
			);
			# Generate the email template with the variables
			$ds_email_template = GenerateTemplate($ds_deadline_template, $variables);

			$dom->loadHTML($ds_email_template);
			$link = $dom->getElementById('login-redirect');


			# Set url query string
			$component_week = "week=".$no_week;
			$component_tab = "";
			if(!empty($tabs)){
				$nb_tab = str_replace(" Reference", "_ref", $tabs[0]);
				$component_tab = "&tab=".strtolower($nb_tab);
			}
			//$ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/students_new/index.php#ajax/desktop.php?".$component_week.$component_tab;

			if ($link) {
				// Cambiar el atributo href
				$link->setAttribute('href', 'https://campus.vanas.ca/modules/students_new/index.php#ajax/desktop.php?'.$component_week.$component_tab);
			}
			$ds_email_template = $dom->saveHTML();


			#Se envia copia de emmail si el studen asi lo elige.
		    if($fg_copy_email_alternativo){
				EnviaMailHTML('', $from, $ds_email_alternative, "Upcoming Assignment Deadline", $ds_email_template);

			}
			  //SendNoticeMail($client, $from, $ds_email_alternative, "", "Upcoming Assignment Deadline", $ds_template_html);

		    if($fg_copy_email_responsable)
			  $ds_email_responsable=$ds_email_responsable;
		    else
			  $ds_email_responsable="";

			EnviaMailHTML('', $from, $ds_email, "Upcoming Assignment Deadline", $ds_email_template, $ds_email_responsable);

			//SendNoticeMail($client, $from, $ds_email, "".$ds_email_responsable."", "Upcoming Assignment Deadline", $ds_template_html);
		}
	}

	# Find all students with assignment overdue in $day_advance day(s)
	$Query  = "SELECT b.fl_entrega_semanal, d.ds_nombres, c.no_semana, c.ds_titulo, DATE_FORMAT(a.fe_entrega, '%W') fe_day, DATE_FORMAT(a.fe_entrega, '%M %e, %Y') fe_date, d.ds_email, ";
	$Query .= "c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch, ";
  $Query .= "DATE_FORMAT(a.fe_entrega, '%h:%i %p') fe_time,d.fl_usuario ";
	$Query .= "FROM k_semana a ";
	$Query .= "LEFT JOIN k_entrega_semanal b ON a.fl_semana=b.fl_semana ";
	$Query .= "LEFT JOIN c_leccion c ON a.fl_leccion=c.fl_leccion ";
	$Query .= "LEFT JOIN c_usuario d ON b.fl_alumno=d.fl_usuario ";
	$Query .= "WHERE DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-%d'), INTERVAL $day_advance DAY) = DATE_FORMAT(a.fe_entrega, '%Y-%m-%d')) ";
	$Query .= "AND b.fg_entregado='0' ";
	$Query .= "AND (c.fg_animacion!='0' OR c.fg_ref_animacion!='0' OR c.no_sketch>0 OR c.fg_ref_sketch!='0') ";
	$Query .= "AND d.fg_activo='1' ";
	$rs = EjecutaQuery($Query);

	$tot_students = CuentaRegistros($rs);
	if($tot_students > 0){
		while($row=RecuperaRegistro($rs)){
			$fl_entrega_semanal = $row[0];
			$st_fname = $row[1];
			$no_week = $row[2];
			$ds_title = $row[3];
			$fe_day = $row[4];
			$fe_date = $row[5];
			$ds_email = $row[6];
		    $fe_time = $row[11];
			$fl_usuario=$row['fl_usuario'];

			# Assignment tabs
			$fg_animacion = $row[7];
		  $fg_ref_animacion = $row[8];
		  $no_sketch = $row[9];
		  $fg_ref_sketch = $row[10];

		  # Count student's uploaded assignment
			$row2 = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='A' AND fl_entrega_semanal=$fl_entrega_semanal");
		  $tot_assignment = $row2[0];
		  $row2 = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='AR' AND fl_entrega_semanal=$fl_entrega_semanal");
		  $tot_assignment_ref = $row2[0];
		  $row2 = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='S' AND fl_entrega_semanal=$fl_entrega_semanal");
		  $tot_sketch = $row2[0];
		  $row2 = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='SR' AND fl_entrega_semanal=$fl_entrega_semanal");
		  $tot_sketch_ref = $row2[0];

		  # Find missing tabs, if the tab is missing, add the name
		  $tabs = array();
		  if($fg_animacion == "1" AND $tot_assignment == 0){
		    $tabs[] = "Assignment";
		  }
		  if($fg_ref_animacion == "1" AND $tot_assignment_ref == 0){
		    $tabs[] = "Assignment Reference";
		  }
		  if($tot_sketch < $no_sketch){
		    $tabs[] = "Sketch";
		  }
		  if($fg_ref_sketch == "1" AND $tot_sketch_ref == 0){
		    $tabs[] = "Sketch Reference";
		  }
			$nb_tabs = implode(", ", $tabs);

			# Send notice to the group of students
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
				"nb_group" => "",
				"nb_tabs" => $nb_tabs
			);


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



			# Generate the email template with the variables
			$ds_email_template = GenerateTemplate($ds_overdue_template, $variables);

			$dom->loadHTML($ds_email_template);
			$link = $dom->getElementById('login-redirect');
			# Set url path and query string
			$component_week = "week=".$no_week;
			$component_tab = "";
			if(!empty($tabs)){
				$nb_tab = str_replace(" Reference", "_ref", $tabs[0]);
				$component_tab = "&tab=".strtolower($nb_tab);
			}
			//$ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/students_new/index.php#ajax/desktop.php?".$component_week.$component_tab;
			if ($link) {
				// Cambiar el atributo href
				$link->setAttribute('href', 'https://campus.vanas.ca/modules/students_new/index.php#ajax/desktop.php?' . $component_week . $component_tab);
			}
			$ds_email_template = $dom->saveHTML();


		    #Se envia copia de emmail si el studen asi lo elige.
			if ($fg_copy_email_alternativo) {
				EnviaMailHTML('', $from, $ds_email_alternative, "Assignment Overdue", $ds_email_template);

			}
			   //SendNoticeMail($client, $from, $ds_email_alternative, "", "Assignment Overdue", $ds_template_html);

		    if($fg_copy_email_responsable)
			  $ds_email_responsable=$ds_email_responsable;
		    else
			  $ds_email_responsable="";

			EnviaMailHTML('', $from, $ds_email, "Assignment Overdue", $ds_email_template, $ds_email_responsable);

			//SendNoticeMail($client, $from, $ds_email, "".$ds_email_responsable."", "Assignment Overdue", $ds_template_html);
		}
	}
?>
