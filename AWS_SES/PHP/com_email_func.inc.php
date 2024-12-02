<?php
	function GenerateTemplate($cadena="", $variables=array()){

		# Replace special characters
		$cadena = str_replace("&lt;", "<", $cadena);
	  $cadena = str_replace("&gt;", ">", $cadena);
	  $cadena = str_replace("&quot;", "\"", $cadena);
	  $cadena = str_replace("&#039;", "'", $cadena);
	  $cadena = str_replace("&#061;", "=", $cadena);

	  # Replace student/teacher variables
	  if(!empty($variables["st_fname"]))
	  $cadena = str_replace("#st_fname#", $variables["st_fname"], $cadena);                      	# Student first name 
	  if(!empty($variables["st_lname"]))
	  $cadena = str_replace("#st_lname#", $variables["st_lname"], $cadena);                      	# Student last name
	  if(!empty($variables["te_fname"]))
	  $cadena = str_replace("#te_fname#", $variables["te_fname"], $cadena);                      	# Teacher first name 
	  if(!empty($variables["te_lname"]))
	  $cadena = str_replace("#te_lname#", $variables["te_lname"], $cadena);                      	# Teacher last name

	  # Replace tuition payment variables
	  if(!empty($variables["py_date"]))
	  $cadena = str_replace("#py_date#", $variables["py_date"], $cadena);  												# Payment date
	  if(!empty($variables["py_amount"]))
	  $cadena = str_replace("#py_amount#", $variables["py_amount"], $cadena);  										# Payment amount

	  # Replace general variables
	  if(!empty($variables["us_fname"]))
	  $cadena = str_replace("#us_fname#", $variables["us_fname"], $cadena);  											# User first name
	  if(!empty($variables["us_lname"]))
	  $cadena = str_replace("#us_lname#", $variables["us_lname"], $cadena);  										  # User last name
	  if(!empty($variables["us_fname_from"]))
	  $cadena = str_replace("#us_fname_from#", $variables["us_fname_from"], $cadena);  						# User first name send from this user
	  if(!empty($variables["us_lname_from"]))
	  $cadena = str_replace("#us_lname_from#", $variables["us_lname_from"], $cadena);  						# User last name send from this user
      
	  if(!empty($variables["ds_avatar"]))	  
	  $cadena = str_replace("#ds_avatar#", $variables["ds_avatar"], $cadena);  										# ds_avatar
	  if(!empty($variables["ds_avatar_from"]))
	  $cadena = str_replace("#ds_avatar_from#", $variables["ds_avatar_from"], $cadena);  					# ds_avatar_from
    
	  if(!empty($variables["ds_comment"]))
	  $cadena = str_replace("#ds_comment#", $variables["ds_comment"], $cadena);  						      # ds_comment
	  if(!empty($variables["ds_message"]))
	  $cadena = str_replace("#ds_message#", $variables["ds_message"], $cadena);  						      # ds_message
    
	  if(!empty($variables["ds_abstract"]))
	  $cadena = str_replace("#ds_abstract#", $variables["ds_abstract"], $cadena);  						    # ds_abstract   
    
	  if(!empty($variables["pg_name"]))
	  $cadena = str_replace("#pg_name#", $variables["pg_name"], $cadena);  												# Program name

      if(!empty($variables["no_week"]))
	  $cadena = str_replace("#no_week#", $variables["no_week"], $cadena);                      		# Week number
	  if(!empty($variables["fe_day"]))
	  $cadena = str_replace("#fe_day#", $variables["fe_day"], $cadena);														# Day
	  if(!empty($variables["fe_date"]))
	  $cadena = str_replace("#fe_date#", $variables["fe_date"], $cadena);                      		# Date
	  if(!empty($variables["fe_time"]))
	  $cadena = str_replace("#fe_time#", $variables["fe_time"], $cadena);                      		# Time
	  if(!empty($variables["ds_title"]))
	  $cadena = str_replace("#ds_title#", $variables["ds_title"], $cadena);               	    	# Title
	 
	  if(!empty($variables["nb_group"]))
	  $cadena = str_replace("#nb_group#", $variables["nb_group"], $cadena);                     	# Name of the group
    
	  if(!empty($variables["nb_tabs"]))
	  $cadena = str_replace("#nb_tabs#", $variables["nb_tabs"], $cadena);                     	  #nb_tabs

    if(!empty($variables["current_month"]))
    $cadena = str_replace("#current_month#", $variables["current_month"], $cadena);             # current month
    if(!empty($variables["current_week_grade"]))
	$cadena = str_replace("#current_week_grade#", $variables["current_week_grade"], $cadena);             # current month
    if(!empty($variables["no_grado"]))
	$cadena = str_replace("#no_grado#", $variables["no_grado"], $cadena);             # grado
    
	if(!empty($variables["st_lmadd"]))
	$cadena = str_replace("#st_lmadd#", $variables["st_lmadd"], $cadena);             # st_lmadd
    if(!empty($variables["st_country"]))
	$cadena = str_replace("#st_country#", $variables["st_country"], $cadena);             # ccountry
    if(!empty($variables["st_lmaddpc"]))
	$cadena = str_replace("#st_lmaddpc#", $variables["st_lmaddpc"], $cadena);             # st_lmaddpc
    if(!empty($variables["program_gpa"]))
	$cadena = str_replace("#program_gpa#", $variables["program_gpa"], $cadena);             # Program gpa
    if(!empty($variables["minimum_gpa"]))
	$cadena = str_replace("#minimum_gpa#", $variables["minimum_gpa"], $cadena);             # minimo de gpa
    if(!empty($variables["number_of_absences"]))
	$cadena = str_replace("#number_of_absences#", $variables["number_of_absences"], $cadena);             # varible for list missed class
    if(!empty($variables["missed_class_term_history"]))
	$cadena = str_replace("#missed_class_term_history#", $variables["missed_class_term_history"], $cadena);             # History missed class
    if(!empty($variables["sts_take_action"]))
	$cadena = str_replace("#sts_take_action#", $variables["sts_take_action"], $cadena);             # students take action
    
	# Fechas en que se envia y expira
    $roww = RecuperaValor("SELECT DATE_FORMAT(NOW(),'%M-%d-%Y'), DATE_FORMAT(DATE_ADD(NOW(), INTERVAL ".ObtenConfiguracion(89)." DAY),'%Y/%m/%d') fe_expiration");
    $fe_envio_template = $roww[0];
    $fe_expiration = $roww[1];
    $cadena = str_replace("#sent_date#", $fe_envio_template, $cadena);               #Fecha de envio del template o correo
    $cadena = str_replace("#fe_expiration#", $fe_expiration, $cadena);               #Days expiration of letter of acceptance
    # En caso de que obtenga sesion
    $fl_sesion = !empty($variables["fl_sesion"])?$variables["fl_sesion"]:NULL;
    if(!empty($fl_sesion)){
      # Obtenemos la sesion del alumno
      $row0 = RecuperaValor("SELECT cl_sesion FROM c_sesion where fl_sesion=".$fl_sesion);
      $cl_sesion = $row0[0];
      # Obtenemos al usuario mendiante la sesion
      $row1 = RecuperaValor("SELECT *FROM c_usuario WHERE cl_sesion='".$cl_sesion."'");
      $fl_alumno = $row1[0];
      # Obtenemos el promedio general del curso
      $QueryGPA  = "SELECT (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t)), ";
      $QueryGPA .= "no_promedio_t FROM c_alumno WHERE fl_alumno=$fl_alumno ";
      $row2 = RecuperaValor($QueryGPA);
      $gpa_grl = $row2[0]." ".round($row2[1])."%";
      if(empty($gpa_grl))
        $gpa_grl = "(No assigment)";
      # Remplazamos el caracter del grado actual y el promedio general
      $rowterm = RecuperaValor("SELECT MAX(fl_term) FROM k_alumno_term WHERE fl_alumno=$fl_alumno");
      $fl_term_actual = $rowterm[0];      
      $cadena = str_replace("#program_gpa#", $gpa_grl, $cadena);                       # Promedio general del alumno
      
      # Obtenemos la calificacion del term
      $QueryTerm  = "SELECT (SELECT cl_calificacion FROM c_calificacion WHERE no_min <=ROUND(no_promedio) AND no_max >=ROUND(no_promedio)), ROUND(no_promedio) ";
      $QueryTerm .= "FROM k_alumno_term WHERE fl_term=$fl_term_actual AND fl_alumno=$fl_alumno";
      $rowc = RecuperaValor($QueryTerm);
      $cl_cal_term = $rowc[0];
      $current_term_promedio = $rowc[1];
      if(empty($current_term_promedio))
        $current_term_promedio = "0";
      $current_term_gpa = $cl_cal_term." ".round($current_term_promedio)."%";
      # Remplazamos el caracter de la calificacion del term actual
      $cadena = str_replace("#current_term_gpa#", $current_term_gpa,$cadena );
    }
    
      
	  return $cadena;
	}

	function SendNoticeMail($client, $from, $to, $cc="", $subject='', $message, $bcc=True){

    $CcAddresses = array();
    $BCcAddresses = array();
    
    # Se envia la copia al correo espeficado
		if(!empty($cc))
			$CcAddresses = array($cc);
    
    # Sen envia la copia oculta a admin actualmente debe llegar de todos este correo
    if($bcc)
			$BCcAddresses = array(ObtenConfiguracion(83));

		$result = $client->sendEmail(array(
	    'Source' => $from,
	    'Destination' => array(
	        'ToAddresses' => array($to),
	        'CcAddresses' => $CcAddresses,
	        'BccAddresses' => $BCcAddresses
	    ),
	    'Message' => array(
	        'Subject' => array(
	            'Data' => $subject,
	            'Charset' => 'UTF-8',
	        ),
	        'Body' => array(
	            'Text' => array(
	                'Data' => '',
	                'Charset' => 'UTF-8',
	            ),
	            'Html' => array(
	                'Data' => $message,
	                'Charset' => 'UTF-8',
	            ),
	        ),
	    ),
	    //'ReplyToAddresses' => array('string', ... ),
	    //'ReturnPath' => 'string',
		));
    return $result['MessageId'];
	}
?>