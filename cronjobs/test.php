<?php


require '/var/www/html/vanas/lib/com_func.inc.php';
require '/var/www/html/vanas/lib/sp_config.inc.php';

# Include AWS SES libraries
require '/var/www/html/vanas/AWS_SES/PHP/com_email_func.inc.php';

$file_name_txt="/var/www/html/vanas/cronjobs/log.txt";
$from = 'noreply@vanas.ca';

# Prepare email templates for assignment reminders, note: (change nb_template='___' to fl_template=id once this is stable on production server)
$Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='17' AND fg_activo='1'";
$tu_upcoming_template = RecuperaValor($Query);
$ds_template = str_uso_normal($tu_upcoming_template[0] . $tu_upcoming_template[1] . $tu_upcoming_template[2]);

$variables = array(
    "st_fname" => "Mike",
    "pg_name" => "Program name",
    "py_date" => "Dec 10",
    "py_amount" => "10",
    "st_lname" => "Test"
);

# Generate the email template with the variables
$ds_email_template = GenerateTemplate($ds_template, $variables);
$ds_email_template = str_replace("#link_payment#", "https://campus.vanas.ca/modules/students_new/index.php#ajax/payment_history.php", $ds_email_template);



#Generamos el log.
GeneraLog($file_name_txt,"====================================Inicia proceso ".date("F j, Y, g:i a")."=================================================");


GeneraLog($file_name_txt,"====================================Finaliza proceso ".date("F j, Y, g:i a")."=================================================");

$email = EnviaMailHTML('', $from, "mike@vanas.ca", "Test CronJob", $ds_email_template);

$fua=1;

function GeneraLog($file_name_txt,$contenido_log=''){

    $fch= fopen($file_name_txt, "a+"); // Abres el archivo para escribir en él
    fwrite($fch, "\n".$contenido_log); // Grabas
    fclose($fch); // Cierras el archivo.
}

?>
