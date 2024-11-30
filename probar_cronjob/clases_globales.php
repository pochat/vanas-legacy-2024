<?php

	# Include campus libraries 
  # Produccion
	require '/var/www/html/vanas/lib/com_func.inc.php';	
	require '/var/www/html/vanas/lib/sp_config.inc.php';
  # Local
  // require '../lib/com_func.inc.php';	
	// require '../lib/sp_config.inc.php';

	# Include AWS SES libraries
  # Produccion
	require '/var/www/html/AWS_SES/PHP/com_email_func.inc.php';	
	require '/var/www/html/AWS_SES/aws/aws-autoloader.php';
  use Aws\Common\Aws;
  # Local
  // require 'com_email_func.inc.php';

	# Include html parser
  # Produccion
	require '/var/www/html/vanas/modules/common/new_campus/lib/simple_html_dom.php';
  # Local
	// require '../modules/common/new_campus/lib/simple_html_dom.php';

	# Load config file
	$aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');

	# Get the client from the builder by namespace
	$client = $aws->get('Ses');

	$from = 'noreply@vanas.ca';

	# Template para clases globales student 
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='46' AND fg_activo='1'";
	$row = RecuperaValor($Query);
	$std_template_cg = str_uso_normal($row[0].$row[1].$row[2]);
  
  # Teacher mandatory live session template teachers
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='47' AND fg_activo='1'";
	$row_te = RecuperaValor($Query);
	$te_template_cg = str_uso_normal($row_te[0].$row_te[1].$row_te[2]);
  
	# Create a DOM object
  $ds_template_html = new simple_html_dom();
  $fecha_actual = date("Y-m-d");
  $dia = ObtenConfiguracion(103);

	# Find all groups with upcoming mandatory live session
  $Query  = "SELECT ";
  $Query .= "kcg.fl_clase_cg, kcg.fl_clase_global, kcg.no_orden, kacg.fl_usuario, DATE_FORMAT(kcg.fe_clase, '%W') fe_day, ";
  $Query .= "DATE_FORMAT(kcg.fe_clase, '%M %e, %Y') fe_date, DATE_FORMAT(kcg.fe_clase, '%h:%i %p') fe_time, us.ds_nombres, us.ds_email, ";
  $Query .= "cg.ds_clase, kcg.ds_titulo,  kcg.fg_obligatorio,us.cl_sesion,us.fl_usuario ";
  $Query .= "FROM c_clase_global cg ";
  $Query .= "LEFT JOIN k_alumno_cg kacg ON(kacg.fl_clase_global=cg.fl_clase_global) ";
  $Query .= "LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=cg.fl_clase_global) ";
  $Query .= "LEFT JOIN c_usuario us ON(us.fl_usuario=kacg.fl_usuario) ";
  $Query .= "WHERE DATE_ADD('".$fecha_actual."', INTERVAL ".$dia." DAY) = DATE_FORMAT(kcg.fe_clase, '%Y-%m-%d') AND us.fg_activo='1' ";
	$rs = EjecutaQuery($Query);
  
  while($row=RecuperaRegistro($rs)){
		$fl_clase_cg = $row[0];
		$fl_clase_global = $row[1];
		$no_orden = $row[2];
    $fl_alumno = $row[3];
		$fe_day = $row[4];
		$fe_date = $row[5];
		$fe_time = $row[6];
    $ds_nombres = str_texto($row[7]);
    $ds_email_std = $row[8];
    $ds_clase = str_texto($row[9]);
    $ds_titulo = str_texto($row[10]);
    $fg_obligatorio = $row[11];
    
    if(!empty($fg_obligatorio))
      $mandatory = ObtenEtiqueta(16);
    else
      $mandatory = ObtenEtiqueta(17);
  
    $cl_sesion=$row['cl_sesion'];
	$fl_usuario=$row['fl_usuario'];
    
	#Recuperamos el email responsable alumno.
	$Query="SELECT  ds_email_r  FROM k_presponsable WHERE cl_sesion='$cl_sesion' ";
	$row=RecuperaValor($Query);
	$ds_email_responsable=$row['ds_email_r'];
	
	$Query="SELECT ds_a_email FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
	$row=RecuperaValor($Query);
	$ds_email_alternative=$row['ds_a_email'];
	
	$Query="SELECT fg_copy_email_responsable,fg_copy_email_alternativo FROM c_alumno WHERE fl_alumno=$fl_usuario ";
	$row=RecuperaValor($Query);
	$fg_copy_email_responsable=$row[0];
	$fg_copy_email_alternativo=$row[1];
	  
    # Enviamos el email al estudiante
    $variables_std = array(
      "st_fname" => $ds_nombres,      
      "no_week" => $no_orden,
      "ds_title" => 'Session de '.$ds_clase,
      "fe_day" => $fe_day,
      "fe_date" => $fe_date,
      "fe_time" => $fe_time,
      "nb_group" => ""
    );    
    # Generate the email template with the variables
    $ds_email_template = GenerateTemplate($std_template_cg, $variables_std);    
    $ds_email_template = str_replace("#global_fe_day#", $fe_day, $ds_email_template);
    $ds_email_template = str_replace("#global_fe_date#", $fe_date, $ds_email_template);
    $ds_email_template = str_replace("#global_fe_time#", $fe_time, $ds_email_template);
    $ds_email_template = str_replace("#global_ds_title#", $ds_clase, $ds_email_template);
    $ds_email_template = str_replace("#global_class_topic#", $ds_titulo, $ds_email_template);
    $ds_email_template = str_replace("#global_mandatory#", $mandatory, $ds_email_template);
    
    # Load the template into html
    $ds_template_html->load($ds_email_template);
    # Get base url (domain)
    $base_url = $ds_template_html->getElementById("login-redirect")->href;
    # Set url path and query string
    $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/students_new/index.php#ajax/home.php";

    if($fg_copy_email_alternativo)
      SendNoticeMail($client, $from, $ds_email_alternative, "", ObtenEtiqueta(860), $ds_template_html);

    if($fg_copy_email_responsable)
		  $ds_email_responsable=$ds_email_responsable;
	else
		$ds_email_responsable="";

  SendNoticeMail($client, $from, $ds_email_std, "".$ds_email_responsable."", ObtenEtiqueta(860), $ds_template_html);

  }
  
  # Buscamos a los profesroes
  $Query = "SELECT kcg.fl_clase_cg, cg.fl_clase_global, kcg.no_orden, kcg.fl_maestro, ";
  $Query .= "DATE_FORMAT(kcg.fe_clase, '%W') fe_day, DATE_FORMAT(kcg.fe_clase, '%M %e, %Y') fe_date,  ";
  $Query .= "DATE_FORMAT(kcg.fe_clase, '%h:%i %p') fe_time, CONCAT(usu.ds_nombres,' ', usu.ds_apaterno), usu.ds_email, cg.ds_clase, kcg.ds_titulo, kcg.fg_obligatorio ";
  $Query .= "FROM k_clase_cg kcg , c_clase_global cg, c_usuario usu ";
  $Query .= "WHERE kcg.fl_clase_global = cg.fl_clase_global ";
  $Query .= "AND kcg.fl_maestro = usu.fl_usuario ";
  $Query .= "AND DATE_ADD('".$fecha_actual."', INTERVAL $dia DAY) = DATE_FORMAT(kcg.fe_clase, '%Y-%m-%d') AND usu.fg_activo='1' ";
  $rs = EjecutaQuery($Query);
	while($row=RecuperaRegistro($rs)){
    $fl_clase_cg = $row[0];
		$fl_clase_global = $row[1];
		$no_orden = $row[2];
    $fl_maestro = $row[3];
		$fe_day = $row[4];
		$fe_date = $row[5];
		$fe_time = $row[6];
    $ds_nombres = str_texto($row[7]);
    $ds_email_te = $row[8];
    $ds_clase = str_texto($row[9]);
    $ds_titulo = str_texto($row[10]);
    $fg_obligatorio = $row[11];
    
    if(!empty($fg_obligatorio))
      $mandatory = ObtenEtiqueta(16);
    else
      $mandatory = ObtenEtiqueta(17);

    # Enviamos el email al estudiante
    $variables_te = array(
      "te_fname" => $ds_nombres,      
      "no_week" => $no_orden,
      "ds_title" => 'Session de '.$ds_clase,
      "fe_day" => $fe_day,
      "fe_date" => $fe_date,
      "fe_time" => $fe_time
    );

    # Generate the email template with the variables
    $ds_email_template = GenerateTemplate($te_template_cg, $variables_te);
    $ds_email_template = str_replace("#global_fe_day#", $fe_day, $ds_email_template);
    $ds_email_template = str_replace("#global_fe_date#", $fe_date, $ds_email_template);
    $ds_email_template = str_replace("#global_fe_time#", $fe_time, $ds_email_template);
    $ds_email_template = str_replace("#global_ds_title#", $ds_clase, $ds_email_template);
    $ds_email_template = str_replace("#global_class_topic#", $ds_titulo, $ds_email_template);
    $ds_email_template = str_replace("#global_mandatory#", $mandatory, $ds_email_template);

    # Load the template into html
    $ds_template_html->load($ds_email_template);
    # Get base url (domain)
    $base_url = $ds_template_html->getElementById("login-redirect")->href;
    # Set url path and query string
    $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/students_new/index.php#ajax/home.php";

    SendNoticeMail($client, $from, $ds_email_te, "", ObtenEtiqueta(860), $ds_template_html);

  }
?>