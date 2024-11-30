<?php
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require("../../common/lib/cam_forum.inc.php");

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

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $fl_gallery_post = RecibeParametroNumerico('fl_post');
  $ds_comment = RecibeParametroHTML('ds_comment');

  # File uploads on a comment post is not implemented yet
  $nb_archivo = "";

  if(empty($fl_gallery_post)){
    $error = array('error' => "Server Error. Unknown post.");
    echo json_encode((Object)$error);
    exit;
  }

  if(!empty($ds_comment)) {
    $ds_comment = rawurldecode($ds_comment);
    $ds_orig_comment = $ds_comment;
    $ds_comment = PorcesaCadena($ds_comment);

    # Sanitize input (special cases)
    /* @url: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/HTML5/HTML5_Parser
     * Lack of Reparsing
     */
    $ds_post = str_replace("&lt;!", "&#60;!", $ds_post);   // html comment
    $ds_post = str_replace("&lt;?", "&#60;?", $ds_post);   // html comment

    # Store comment to this post
    $Query  = "INSERT INTO k_gallery_comment ";
    $Query .= "(fl_gallery_post, fl_usuario, ds_comment, fe_comment, nb_archivo, fg_read) ";
    $Query .= "VALUES ($fl_gallery_post, $fl_usuario, '$ds_comment', CURRENT_TIMESTAMP, '$nb_archivo', '0')";
    $fl_gallery_comment = EjecutaInsert($Query);

    # Check if the insert or update was successful
    if(empty($fl_gallery_comment)){
      $error = array('error' => "Server Error. This comment cannot be stored.");
      echo json_encode((Object)$error);
      exit;
    }

    # Prepare Email Template
    $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE nb_template='Activity board comments' AND fg_activo='1'";
    $board_template = RecuperaValor($Query);
    $ds_template = str_uso_normal($board_template[0].$board_template[1].$board_template[2]);

    # Create a DOM object
    $ds_template_html = new simple_html_dom();

    # Post commentor
    $Query  = "SELECT ds_nombres, ds_apaterno ";
    $Query .= "FROM c_usuario ";
    $Query .= "WHERE fl_usuario=$fl_usuario ";
    $row = RecuperaValor($Query);
    $ds_nombres = $row[0];
    $ds_apaterno = $row[1];

    # Post creator
    $Query  = "SELECT a.fl_usuario, b.ds_nombres, b.ds_apaterno, b.fl_perfil, b.ds_email ";
    $Query .= "FROM k_gallery_post a ";
    $Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_usuario ";
    $Query .= "WHERE fl_gallery_post=$fl_gallery_post ";
    $row = RecuperaValor($Query);

    $fl_post_usuario = $row[0];
    $ds_post_nombres = $row[1];
    $ds_post_apaterno = $row[2];
    $fl_post_perfil = $row[3];
    $ds_post_email = $row[4];

    # Direct the user to the correct path based on profile
    if($fl_post_perfil == PFL_ESTUDIANTE){
      $path = "/modules/students_new/index.php#ajax/gallery.php?";
    } else {
      $path = "/modules/teachers_new/index.php#ajax/gallery.php?";
    }

    $variables = array(
      'us_fname' => $ds_post_nombres,
      'us_fname_from' => $ds_nombres,
      'us_lname_from' => $ds_apaterno,
      'ds_comment' => $ds_orig_comment
    );
    # Generate the email template with the variables
    $ds_email_template = GenerateTemplate($ds_template, $variables);

    # Load the template into html
    $ds_template_html->load($ds_email_template);
    # Get base url (domain)
    $base_url = $ds_template_html->getElementById("login-redirect")->href;
    # Set url path and query string
    $component_post = "post=".$fl_gallery_post;
    $ds_template_html->getElementById("login-redirect")->href = $base_url.$path.$component_post;

    # Send to author, but don't send to myself (if post author is not commentor)
    if($fl_usuario != $fl_post_usuario){
      SendNoticeMail($client, $from, $ds_post_email, '', 'New Comment on VANAS Board', $ds_template_html);
    }

    # Everyone else that's involved except for creator and commentor
    $Query  = "SELECT DISTINCT a.fl_usuario, b.ds_nombres, b.ds_apaterno, b.fl_perfil, b.ds_email ";
    $Query .= "FROM k_gallery_comment a ";
    $Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_usuario ";
    $Query .= "WHERE fl_gallery_post=$fl_gallery_post ";
    $Query .= "AND a.fl_usuario!=$fl_post_usuario ";
    $Query .= "AND a.fl_usuario!=$fl_usuario ";
    $rs = EjecutaQuery($Query);

    $tot_comments = CuentaRegistros($rs);
    if($tot_comments > 0){
      while($row=RecuperaRegistro($rs)){
        $fl_comm_usuario = $row[0];
        $ds_comm_nombres = $row[1];
        $ds_comm_apaterno = $row[2];
        $fl_comm_perfil = $row[3];
        $ds_comm_email = $row[4];

        # Direct the user to the correct path based on profile
        if($fl_comm_perfil == PFL_ESTUDIANTE){
          $path = "/modules/students_new/index.php#ajax/gallery.php?";
        } else {
          $path = "/modules/teachers_new/index.php#ajax/gallery.php?";
        }

        $variables = array(
          'us_fname' => $ds_comm_nombres,
          'us_fname_from' => $ds_nombres,
          'us_lname_from' => $ds_apaterno,
          'ds_comment' => $ds_orig_comment
        );
        # Generate the email template with the variables
        $ds_email_template = GenerateTemplate($ds_template, $variables);

        # Load the template into html
        $ds_template_html->load($ds_email_template);
        # Get base url (domain)
        $base_url = $ds_template_html->getElementById("login-redirect")->href;
        # Set url path and query string
        $component_post = "post=".$fl_gallery_post;
        $ds_template_html->getElementById("login-redirect")->href = $base_url.$path.$component_post;

        SendNoticeMail($client, $from, $ds_comm_email, '', 'New Comment on VANAS Board', $ds_template_html);
      }
    }
  }
  echo json_encode((Object)array('success' => 'Mail Sent.'));
?>
