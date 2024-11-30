<?php
	function GenerateTemplate($cadena="", $variables=array()){

		# Replace special characters
		$cadena = str_replace("&lt;", "<", $cadena);
	  $cadena = str_replace("&gt;", ">", $cadena);
	  $cadena = str_replace("&quot;", "\"", $cadena);
	  $cadena = str_replace("&#039;", "'", $cadena);
	  $cadena = str_replace("&#061;", "=", $cadena);

	  # Replace student/teacher variables
	  $cadena = str_replace("#st_fname#", $variables["st_fname"], $cadena);                      # Student first name 
	  $cadena = str_replace("#st_lname#", $variables["st_lname"], $cadena);                      # Student last name

	  $cadena = str_replace("#te_fname#", $variables["te_fname"], $cadena);                      # Teacher first name 
	  $cadena = str_replace("#te_lname#", $variables["te_lname"], $cadena);                      # Teacher last name

	  # Replace general variables
	  $cadena = str_replace("#no_week#", $variables["no_week"], $cadena);                      	# Week number
	  $cadena = str_replace("#ds_title#", $variables["ds_title"], $cadena);               	    # Lesson title
	  $cadena = str_replace("#fe_day#", $variables["fe_day"], $cadena);													# Lesson day
	  $cadena = str_replace("#fe_date#", $variables["fe_date"], $cadena);                      	# Lesson date
	  $cadena = str_replace("#fe_time#", $variables["fe_time"], $cadena);                      	# Lesson time
	  $cadena = str_replace("#nb_group#", $variables["nb_group"], $cadena);                     # Name of the group

	  return $cadena;
	}

	function SendNoticeMail($client, $from, $to, $subject, $message){
		$result = $client->sendEmail(array(
	    'Source' => $from,
	    'Destination' => array(
	        'ToAddresses' => array($to)
	        ,'CcAddresses' => array('mario@vanas.ca', 'eric@vanas.ca')//,
	        //'BccAddresses' => array('string', ... ),
	    ),
	    'Message' => array(
	        'Subject' => array(
	            'Data' => $subject,
	            'Charset' => 'UTF-8',
	        ),
	        'Body' => array(
	            'Text' => array(
	                'Data' => 'Please View This Email on A Desktop Computer',
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
	}
?>