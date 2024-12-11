<?php
  # Include campus libraries
    require '/var/www/html/vanas/lib/com_func.inc.php';
    require '/var/www/html/vanas/lib/sp_config.inc.php';

    # Include AWS SES libraries
    require '/var/www/html/vanas/AWS_SES/PHP/com_email_func.inc.php';

    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // Para suprimir errores relacionados con HTML mal formado

    $from = 'noreply@vanas.ca';

  #template para notificacion
  $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie,nb_template FROM k_template_doc WHERE fl_template='36' AND fg_activo='1'";
	$row_take = RecuperaValor($Query);
	$ds_template = str_uso_normal($row_take[0].$row_take[1].$row_take[2]);
  $nb_template = str_texto($row_take[3]);


  # Buscamos a los alumnos que esta en el listado de take action
  $Query  = "SELECT fl_usuario, ds_login, ds_nombres, ds_apaterno,ds_amaterno, nb_programa, DATE_FORMAT(fe_alta, '%d-%m-%Y'), ".ConsultaFechaBD('fe_completado', FMT_FECHA).", ";
  $Query .= "fg_activo,fg_pago, g.no_promedio_t ";
  $Query .= "FROM c_usuario a, c_perfil b, c_sesion c, k_ses_app_frm_1 d, c_programa e, k_pctia f, c_alumno g ";
  $Query .= "LEFT JOIN c_calificacion h ON(g.no_promedio_t>0 AND g.no_promedio_t BETWEEN no_min AND  no_max ) ";
  $Query .= "WHERE a.fl_perfil=b.fl_perfil AND a.cl_sesion=c.cl_sesion AND c.cl_sesion=d.cl_sesion AND d.fl_programa=e.fl_programa ";
  $Query .= "AND a.fl_usuario = f.fl_alumno AND a.fl_perfil=3 AND a.fl_usuario=g.fl_alumno AND f.fe_completado < CURDATE() ";
  $Query .= "AND (a.fg_activo='1' OR (a.fg_activo='0' AND f.fg_desercion='0' AND f.fg_dismissed='0' AND f.fg_job='0' AND f.fg_graduacion='0' ";
  $Query .= "AND f.fg_certificado='0' AND f.fg_honores ='0'))  ";
  $rs = EjecutaQuery($Query);
  $sts_take_action = "";
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $fl_alumno = $row[0];
    $ds_nombres = str_texto($row[2]);
    $ds_apaterno = str_texto($row[3]);
    $ds_amaterno = str_texto($row[4]);
    $sts_take_action = $sts_take_action.$ds_nombres."&nbsp;".$ds_apaterno."<br>";
  }

  # Variables
  $variables = array(
    "sts_take_action" => $sts_take_action
  );

  # template
  $ds_take_action = GenerateTemplate($ds_template, $variables);


    $dom->loadHTML($ds_take_action);
    $link = $dom->getElementById('login-redirect');
    if ($link) {
        // Cambiar el atributo href
        $link->setAttribute('href', 'https://campus.vanas.ca/modules/teachers_new/index.php#ajax/home.php');
    }
    $ds_take_action = $dom->saveHTML();

# Enviamos el email
  # Recibe adminvanasca
  //SendNoticeMail($client, $from, ObtenConfiguracion(20), "", $nb_template, $ds_template_html);
  EnviaMailHTML('', $from, ObtenConfiguracion(20), $nb_template, $ds_take_action);

?>
