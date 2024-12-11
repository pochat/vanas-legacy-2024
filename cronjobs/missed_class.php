<?php

  # Este cron se ejecutara en las madrugadas
  # Su Objetivo es revisar las clases que hubo un dia anterior y los alumnos que tuvieron que haber asistido
  # Si el alumno no asistio se enviara un email notification Missed Class
  # Si el alumno acumulo igual o mayor a tres faltas pero menor a ocho se enviara email notification Unsatisfactory Attendance Warning
  # Si el alumno acumulo igual o mayor a ocho faltas se enviara email notification Attendance Probation
    # Include campus libraries
    require '../lib/com_func.inc.php';
    require '../lib/sp_config.inc.php';

    # Include AWS SES libraries
    require '../AWS_SES/PHP/com_email_func.inc.php';

	$from = 'noreply@vanas.ca';
	$yesterdar = 1;

	# Student missed class template
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='32' AND fg_activo='1'";
	$missed_template = RecuperaValor($Query);
	$st_missed_template = str_uso_normal($missed_template[0].$missed_template[1].$missed_template[2]);

  # Student Attendance Warning template
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='33' AND fg_activo='1'";
	$attendance_template = RecuperaValor($Query);
	$st_attendance_template = str_uso_normal($attendance_template[0].$attendance_template[1].$attendance_template[2]);

  # Student Attendance Warning template
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='34' AND fg_activo='1'";
	$atten_proba_temp = RecuperaValor($Query);
	$st_atten_prob_template = str_uso_normal($atten_proba_temp[0].$atten_proba_temp[1].$atten_proba_temp[2]);

	# Create a DOM object
  #$ds_template_html = new simple_html_dom();

	# Find all groups with upcoming mandatory live session in $day_advance day(s)
	$Query  = "SELECT a.fl_grupo, d.no_semana, d.ds_titulo, DATE_FORMAT(a.fe_clase, '%W') fe_day, ";
  $Query .= "DATE_FORMAT(a.fe_clase, '%M %e, %Y') fe_date, DATE_FORMAT(a.fe_clase, '%h:%i %p') fe_time, fl_clase, nb_grupo,c.fl_semana, ";
  $Query .= "f.ds_nombres, f.ds_apaterno, f.ds_email, f.fl_usuario, a.fg_obligatorio, a.fg_adicional, ";
  $Query .= "(SELECT fl_sesion FROM c_sesion k WHERE k.cl_sesion=f.cl_sesion) fl_sesion ";
  $Query .= "FROM k_clase a  LEFT JOIN c_grupo b ON a.fl_grupo=b.fl_grupo ";
  $Query .= "LEFT JOIN k_semana c ON a.fl_semana=c.fl_semana LEFT JOIN c_leccion d ON c.fl_leccion=d.fl_leccion ";
  $Query .= "LEFT JOIN k_alumno_grupo e ON b.fl_grupo = e.fl_grupo LEFT JOIN c_usuario f ON e.fl_alumno=f.fl_usuario ";
  $Query .= "WHERE DATE_SUB(CURDATE(), INTERVAL $yesterdar DAY) = DATE_FORMAT(a.fe_clase, '%Y-%c-%d') ";
  $Query .= "AND ((a.fg_obligatorio='1' AND a.fg_adicional='0') OR (a.fg_obligatorio='1' AND a.fg_adicional='1')) AND f.fg_activo='1' ";
  $Query .= " ORDER BY f.ds_nombres ";
	$rs = EjecutaQuery($Query);
	while($row=RecuperaRegistro($rs)){
		$fl_grupo = $row[0];
		$no_week = $row[1];
		$ds_title = $row[2];
		$fe_day = $row[3];
		$fe_date = $row[4];
		$fe_time = $row[5];
    $fl_clase = $row[6];
    $nb_grupo = $row[7];
    $fl_semana = $row[8];
    $ds_nombres = $row[9];
    $ds_apaterno = $row[10];
    $ds_email = $row[11];
    $fl_usuario = $row[12];
    $fg_obligatorio = $row[13];
    $fg_adicional = $row[14];
    $fl_sesion = $row[15];
    # STUDENT ATTENDANCE
    $Query1  = "SELECT cl_estatus_asistencia FROM k_live_session a LEFT JOIN k_live_session_asistencia b ON a.fl_live_session = b.fl_live_session ";
    $Query1 .= "WHERE a.fl_clase=$fl_clase AND fl_usuario=$fl_usuario ";
    $row1 = RecuperaValor($Query1);
    $cl_estatus_asistencia = !empty($row1[0])?$row1[0]:NULL;
    if(empty($cl_estatus_asistencia) OR $cl_estatus_asistencia!=2){
      $variables = array(
        "st_fname" => $ds_nombres,
        "st_lname" => $ds_apaterno,
        "no_week" => $no_week,
        "ds_title" => $ds_title,
        "fe_day" => $fe_day,
        "fe_date" => $fe_date,
        "fe_time" => $fe_time,
        "nb_group" => $nb_grupo
      );


    #Recuperamos el email responsable alumno.
	$Query="SELECT  ds_email_r  FROM k_presponsable a JOIN c_usuario b ON b.cl_sesion=a.cl_sesion WHERE b.fl_usuario=$fl_usuario ";
	$row=RecuperaValor($Query);
	$ds_email_responsable=!empty($row['ds_email_r'])?$row['ds_email_r']:NULL;

	$Query="SELECT ds_a_email FROM k_app_contrato a JOIN c_usuario b ON b.cl_sesion=a.cl_sesion WHERE b.fl_usuario=$fl_usuario ";
	$row=RecuperaValor($Query);
	$ds_email_alternative=!empty($row['ds_a_email'])?$row['ds_a_email']:NULL;

	$Query="SELECT fg_copy_email_responsable,fg_copy_email_alternativo FROM c_alumno WHERE fl_alumno=$fl_usuario ";
	$row=RecuperaValor($Query);
	$fg_copy_email_responsable=$row[0];
	$fg_copy_email_alternativo=$row[1];



      # Generate the email template with the variables
#      $ds_email_template = GenerateTemplate($st_missed_template, $variables);

      # Load the template into html
 #     $ds_template_html->load($ds_email_template);
      # Get base url (domain)
 #     $base_url = $ds_template_html->getElementById("login-redirect")->href;
      # Set url path and query string
 #     $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/teachers_new/index.php#ajax/home.php";

        $dom->loadHTML($ds_email_template);
        $link = $dom->getElementById('login-redirect');
        if ($link) {
            // Cambiar el atributo href
            $link->setAttribute('href', 'https://campus.vanas.ca/modules/students_new/index.php#ajax/home.php');
        }
        $ds_email_template = $dom->saveHTML();


	  #Se envia copia de emmail si el studen asi lo elige.
#	  if($fg_copy_email_alternativo)
#	    SendNoticeMail($client, $from, $ds_email_alternative, "", "Missed Class", $ds_template_html);

	  if($fg_copy_email_responsable)
		 $ds_email_responsable=$ds_email_responsable;
	  else
		 $ds_email_responsable="";

      # EMAIL NOTIFICATION MISSED CLASS
#      SendNoticeMail($client, $from, $ds_email, "".$ds_email_responsable."", "Missed Class", $ds_template_html);
        EnviaMailHTML('', $from, $ds_email, "Missed Class", $ds_email_template, $ds_email_responsable);

        # Guardamos su email enviado
      $QueryMC  = "INSERT INTO k_alumno_template(fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
      $QueryMC .= "VALUES ($fl_sesion, 32, CURRENT_TIMESTAMP, '".GenerateTemplate(str_html_bd($missed_template[0]), $variables)."', '".GenerateTemplate(str_html_bd($missed_template[1]), $variables)."','".GenerateTemplate(str_html_bd($missed_template[2]), $variables)."') ";
      EjecutaQuery($QueryMC);

      ##### Historial de las faltas del alumno #####
      $Query5  = "SELECT no_semana no_semanaa, c.ds_titulo ds_tituloo, DATE_FORMAT(a.fe_clase, '%W') fe_dayy, DATE_FORMAT(a.fe_clase, '%M %e, %Y') fe_datee, ";
      $Query5 .= "DATE_FORMAT(a.fe_clase, '%h:%i %p') fe_timee, fl_clase fl_clasee FROM k_clase a ";
      $Query5 .= "LEFT JOIN k_semana b ON a.fl_semana = b.fl_semana LEFT JOIN c_leccion c ON b.fl_leccion = c.fl_leccion ";
      $Query5 .= "WHERE fl_grupo=$fl_grupo AND fg_obligatorio='1' ";
      $Query5 .= "AND DATE_FORMAT(a.fe_clase, '%Y-%c-%d') <=  dATE_SUB(CURDATE(), INTERVAL $yesterdar DAY) ";
      $Query5 .= "order by fe_clase ";
      $rs5 = EjecutaQuery($Query5);
      $absences = 0;
      $absences_class = "";
      while($row5=RecuperaRegistro($rs5)){
        $no_semanaa = $row5[0];
        $ds_tituloo = $row5[1];
        $fe_dayy = $row5[2];
        $fe_datee = $row5[3];
        $fe_timee = $row5[4];
        $fl_clasee = $row5[5];

        # buscamos las faltas que ha tenido el alumno
        $Query6  = "SELECT cl_estatus_asistencia FROM k_live_session a LEFT JOIN k_live_session_asistencia b ON a.fl_live_session = b.fl_live_session ";
        $Query6 .= "WHERE a.fl_clase=$fl_clasee AND fl_usuario=$fl_usuario";
        $row6 = RecuperaValor($Query6);
        $cl_estatus_asistenciaa = !empty($row6[0])?$row6[0]:NULL;
        if(empty($cl_estatus_asistenciaa) OR $cl_estatus_asistenciaa!=2){
          $absences ++;
          $absences_class = $absences_class."Week $no_semanaa: $ds_tituloo Date on: $fe_dayy, $fe_datee, $fe_timee <br>";
        }
      }
      $unsatisfactory = ObtenConfiguracion(87);
      $attendance_probation = ObtenConfiguracion(88);
      # Si el alumno a tenido faltas mayor o igual a $unsatisfactory pero menor que $attendance_probation
      # Se le enviara una email con el template unsatisfactory Attendance Warning
      if($absences>=$unsatisfactory AND $absences<$attendance_probation){
        $variables3 = array(
          "st_fname" => $ds_nombres,
          "st_lname" => $ds_apaterno,
          "missed_class_term_history" => $absences_class,
          "number_of_absences" => $unsatisfactory
        );
        # Generate the email template with the variables
  #      $ds_attendance_template = GenerateTemplate($st_attendance_template, $variables3);

        # Load the template into html
  #      $ds_attendance_html->load($ds_attendance_template);
        # Get base url (domain)
  #      $base_url = $ds_attendance_html->getElementById("login-redirect")->href;
        # Set url path and query string
  #      $ds_attendance_html->getElementById("login-redirect")->href = $base_url."/modules/teachers_new/index.php#ajax/home.php";


		#Se envia copia de emmail si el studen asi lo elige.
	#	if($fg_copy_email_alternativo)
#		SendNoticeMail($client, $from, $ds_email_alternative, "", "Unsatisfactory Attendance Warning", $ds_attendance_html);

		if($fg_copy_email_responsable)
			$ds_email_responsable=$ds_email_responsable;
		else
			$ds_email_responsable="";



  #      SendNoticeMail($client, $from, $ds_email, "", "Unsatisfactory Attendance Warning", $ds_attendance_html);

        # Guardamos su email enviado
        $Queryat = "INSERT INTO k_alumno_template(fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
        $Queryat .= "VALUES ($fl_sesion, 33, CURRENT_TIMESTAMP, '".GenerateTemplate(str_html_bd($attendance_template[0]), $variables3)."', '".GenerateTemplate(str_html_bd($attendance_template[1]), $variables3)."','".GenerateTemplate(str_html_bd($attendance_template[2]), $variables3)."') ";
        EjecutaQuery($Queryat);
      }

      # Si el alumno a acumulado mayor o igual a $attendance_probation
      # Se le enviara la notificacion Attendance Probation
      if($absences>=$attendance_probation){
        $variables4 = array(
          "st_fname" => $ds_nombres,
          "st_lname" => $ds_apaterno,
          "missed_class_term_history" => $absences_class,
          "number_of_absences" => $attendance_probation
        );
        # Generate the email template with the variables
#        $ds_att_prb_probation = GenerateTemplate($st_atten_prob_template, $variables4);

        # Load the template into html
#        $ds_probation_html->load($ds_att_prb_probation);
        # Get base url (domain)
#        $base_url = $ds_probation_html->getElementById("login-redirect")->href;
        # Set url path and query string
 #       $ds_probation_html->getElementById("login-redirect")->href = $base_url."/modules/teachers_new/index.php#ajax/home.php";


		#Se envia copia de emmail si el studen asi lo elige.
#		if($fg_copy_email_alternativo)
#		  SendNoticeMail($client, $from, $ds_email_alternative, "", "Attendance Probation", $ds_probation_html);

		if($fg_copy_email_responsable)
		   $ds_email_responsable=$ds_email_responsable;
		else
		   $ds_email_responsable="";


#        SendNoticeMail($client, $from, $ds_email, "".$ds_email_responsable."", "Attendance Probation", $ds_probation_html);
        # Guardamos su email enviado
        $Queryul = "INSERT INTO k_alumno_template(fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
        $Queryul .= "VALUES ($fl_sesion, 34, CURRENT_TIMESTAMP, '".GenerateTemplate(str_html_bd($atten_proba_temp[0]), $variables4)."', '".GenerateTemplate(str_html_bd($atten_proba_temp[1]), $variables4)."','".GenerateTemplate(str_html_bd($atten_proba_temp[2]), $variables4)."') ";
        EjecutaQuery($Queryul);
      }
    }
	}

?>
