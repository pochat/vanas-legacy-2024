<?php
	# Email Library
  require('/var/www/html/AWS_SES/PHP/com_email_func.inc.php');

  # Load AWS class
  require('/var/www/html/AWS_SES/aws/aws-autoloader.php');
  use Aws\Common\Aws;

  # Html parser
  require('/var/www/html/vanas/modules/common/new_campus/lib/simple_html_dom.php');

  # Initialize Amazon Web Service
  $aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');

  # Get the client
  $client = $aws->get('Ses');

  # Initialize the sender address
  $from = 'noreply@vanas.ca';

  # Prepare email templates for assignment reminders
	$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE nb_template='School News' AND fg_activo='1'";
	$schoolnews_template = RecuperaValor($Query);
  $ds_template = str_uso_normal($schoolnews_template[0].$schoolnews_template[1].$schoolnews_template[2]);

  # Create a DOM object
  $ds_template_html = new simple_html_dom();

  # Prepare the right abstract text
  $ds_abstract = str_uso_normal($ds_resumen);
  $ds_abstract = str_replace("<p>", "", $ds_abstract);
  $ds_abstract = str_replace("</p>", "", $ds_abstract);

  # Email title
  $ds_email_title = str_ascii("School News - ".$ds_titulo);

  if($fg_maestros == "1") {
    $rs = EjecutaQuery("SELECT b.ds_nombres AS us_fname, b.ds_email FROM c_maestro a LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_maestro WHERE b.fg_activo='1'");
    while($row = RecuperaRegistro($rs)){
    	$us_fname = $row[0];
    	$ds_email = $row[1];

    	# Prepare email variables
  		$variables = array(
		  	"us_fname" => $us_fname,
		  	"ds_title" => $ds_titulo,
		  	"ds_abstract" => $ds_abstract
		  );
      # Generate the email template with the variables
      $ds_email_template = GenerateTemplate($ds_template, $variables);

      # Load the template into html
      $ds_template_html->load($ds_email_template);
      # Get base url (domain)
      $base_url = $ds_template_html->getElementById("login-redirect")->href;
      # Set url path and query string
      $component_blog = "blog=".$fl_blog;
      $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/teachers_new/index.php#ajax/blog.php?".$component_blog;

      SendNoticeMail($client, $from, $ds_email, "", $ds_email_title, $ds_template_html);
    }
  }
  if($fg_alumnos == "1") {
    $rs = EjecutaQuery("SELECT b.ds_nombres AS us_fname, b.ds_email FROM c_alumno a LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_alumno WHERE b.fg_activo='1'");
    while($row = RecuperaRegistro($rs)){
    	$us_fname = $row[0];
    	$ds_email = $row[1];

    	# Prepare email variables
    	$variables = array(
		  	"us_fname" => $us_fname,
		  	"ds_title" => $ds_titulo,
		  	"ds_abstract" => $ds_abstract
		  );
      # Generate the email template with the variables
      $ds_email_template = GenerateTemplate($ds_template, $variables);

      # Load the template into html
      $ds_template_html->load($ds_email_template);
      # Get base url (domain)
      $base_url = $ds_template_html->getElementById("login-redirect")->href;
      # Set url path and query string
      $component_blog = "blog=".$fl_blog;
      $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/students_new/index.php#ajax/blog.php?".$component_blog;

      SendNoticeMail($client, $from, $ds_email, "", $ds_email_title, $ds_template_html);
    }
  }
?>
