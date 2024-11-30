<?php 
  
  # Include campus libraries 
	require '/var/www/html/vanas/lib/com_func.inc.php';
	require '/var/www/html/vanas/lib/sp_config.inc.php';
    //require 'lib/com_func.inc.php';
	// require 'lib/sp_config.inc.php';

	# Include AWS SES libraries
	require '/var/www/html/AWS_SES/PHP/com_email_func.inc.php';
	require '/var/www/html/AWS_SES/aws/aws-autoloader.php';  
  use Aws\Common\Aws;

	# Include html parser
	require '/var/www/html/vanas/modules/common/new_campus/lib/simple_html_dom.php';

echo"===================================";

	# Load config file
	$aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');

	# Get the client from the builder by namespace
	$client = $aws->get('Ses');
  
  $from = 'noreply@vanas.ca';
  
  #template para notificacion 
  $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie,nb_template FROM k_template_doc WHERE fl_template='28' AND fg_activo='1'";
	$st_live_template = RecuperaValor($Query);
	$ds_template = str_uso_normal($st_live_template[0].$st_live_template[1].$st_live_template[2]);
  $nb_template = str_texto($st_live_template[3]);
  
  # Create a DOM object
  $ds_template_html = new simple_html_dom();
  
  
  
  
      $variables = array(
        "te_fname" => "Miguel",
        "te_lname" => "Jimenez",
        "current_month" => "09" // mes actual
      );
      $ds_email="mjimenez@loomtek.com.mx"; 
  
  
       # template
      $ds_template_teacher = GenerateTemplate($ds_template, $variables);
      # Load the template into html
      $ds_template_html->load($ds_template_teacher);
      # Get base url (domain)
      $base_url = $ds_template_html->getElementById("login-redirect")->href;
      # Set url path and query string
      $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/teachers_new/index.php#ajax/home.php";
      

      # Enviamos el email
      $enviado=SendNoticeMail($client, $from, $ds_email, "", $nb_template, $ds_template_html);
      echo $enviado;
	  
	  
	  echo "===============FIN=====================";
	  
?>
