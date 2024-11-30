<?php
  # Libreria de funciones	
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Email Library
  if(FG_PRODUCCION==true)
    require('/var/www/html/AWS_SES/PHP/com_email_func.inc.php');
  else
    require('../../AWS_SES/PHP/com_email_func.inc.php');
  
  # Initialize the sender address
  $from = 'noreply@vanas.ca';
  
  # Enviamos la copia al fame@vanas.ca
  $email_fame = ObtenConfiguracion(107);
  
  # Receive parameters
  $fl_gallery_post_sp = RecibeParametroNumerico('fl_post');
  $ds_comment = RecibeParametroHTML('ds_comment');
  $fame = RecibeParametroNumerico('fame');
  
  # File uploads on a comment post is not implemented yet
  $nb_archivo = "";

  if(empty($fl_gallery_post_sp)){
    $error = array('error' => "Server Error. Unknown post.");
    echo json_encode((Object)$error);
    exit;
  }
   
  if(!empty($ds_comment)) {
    
    # Get last comment  
    $fl_gallery_comment_sp_ultimo = 0;
    if($fame==1){
      $Query3 = "SELECT MAX(fl_gallery_comment_sp) fl_gallery_comment_sp_ultimo FROM k_gallery_comment_sp WHERE fl_gallery_post_sp=$fl_gallery_post_sp ORDER BY fl_gallery_comment_sp DESC";
      $row3 = RecuperaValor($Query3);
      $fl_gallery_comment_sp_ultimo = $row3[0];
      if(empty($fl_gallery_comment_sp_ultimo))
        $fl_gallery_comment_sp_ultimo = 0;
    }
    else{
      $Query3 = "SELECT MAX(fl_gallery_comment) fl_gallery_comment_sp_ultimo FROM k_gallery_comment WHERE fl_gallery_post=$fl_gallery_post_sp ORDER BY fl_gallery_comment DESC";
      $row3 = RecuperaValor($Query3);
      $fl_gallery_comment_sp_ultimo = $row3[0];
      if(empty($fl_gallery_comment_sp_ultimo))
        $fl_gallery_comment_sp_ultimo = 0;
    }
    
    # Comentarios
    $ds_comment = rawurldecode($ds_comment);
    $ds_orig_comment = $ds_comment;
    $ds_comment = PorcesaCadena($ds_comment);

    # Sanitize input (special cases)
    // @url: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/HTML5/HTML5_Parser
    // Lack of Reparsing

    $ds_post = str_replace("&lt;!", "&#60;!", $ds_post);   // html comment
    $ds_post = str_replace("&lt;?", "&#60;?", $ds_post);   // html comment

    # Store comment to this post
    if($fame==1){
      $Query  = "INSERT INTO k_gallery_comment_sp ";
      $Query .= "(fl_gallery_post_sp, fl_usuario, ds_comment, fe_comment, nb_archivo, fg_read) ";
      $Query .= "VALUES ($fl_gallery_post_sp, $fl_usuario, '$ds_comment', CURRENT_TIMESTAMP, '$nb_archivo', '0')";
    }
    else{
      $Query  = "INSERT INTO k_gallery_comment ";
      $Query .= "(fl_gallery_post,fl_usuario,ds_comment,fe_comment,nb_archivo,fg_read) ";
      $Query .= "VALUES ($fl_gallery_post_sp, $fl_usuario, '$ds_comment', CURRENT_TIMESTAMP, '$nb_archivo', '0')"; 
    }
    $fl_gallery_comment_sp = EjecutaInsert($Query);

    # Check if the insert or update was successful
    if(empty($fl_gallery_comment_sp)){
      $error = array('error' => "Server Error. This comment cannot be stored.");
      echo json_encode((Object)$error);
      exit;
    }
    
    # Prepare Email Template
    $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie, nb_template FROM k_template_doc WHERE fl_template=117 AND fg_activo='1'";
    $board_template = RecuperaValor($Query);
    $ds_template = str_uso_normal($board_template[0].$board_template[1].$board_template[2]);
    $nb_template = str_uso_normal($board_template[3]);
    
    # Post commentor
    $Query  = "SELECT ds_nombres, ds_apaterno ";
    $Query .= "FROM c_usuario ";
    $Query .= "WHERE fl_usuario=$fl_usuario ";
    $row = RecuperaValor($Query);
    $ds_nombres = $row[0];
    $ds_apaterno = $row[1];

    # Post creator 
    # Comments of vanas or fame
    if($fame==1){
      $Query  = "SELECT a.fl_usuario, b.ds_nombres, b.ds_apaterno, b.fl_perfil, b.ds_email ";
      $Query .= "FROM k_gallery_post_sp a ";
      $Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_usuario ";
      $Query .= "WHERE fl_gallery_post_sp=$fl_gallery_post_sp ";
    }
    else{
      $Query  = "SELECT a.fl_usuario, b.ds_nombres, b.ds_apaterno, b.fl_perfil, b.ds_email ";
      $Query .= "FROM k_gallery_post a ";
      $Query .= "LEFT JOIN c_usuario b ON(b.fl_usuario=a.fl_usuario) ";
      $Query .= "WHERE a.fl_gallery_post= $fl_gallery_post_sp ";
    }
    $row = RecuperaValor($Query);
    $fl_post_usuario = $row[0];
    $ds_post_nombres = $row[1];
    $ds_post_apaterno = $row[2];
    $fl_post_perfil = $row[3];
    $ds_post_email = $row[4];
    
    # Actuliza las notificaciones para fame
    if($fame==1){
      # Obtenemos el numero de notificaciones que tiene el usuario
      $rowk = RecuperaValor("SELECT no_notice FROM k_usu_notify WHERE fl_usuario=$fl_post_usuario");
      $no_notify = $rowk[0] + 1;
      # Actualizamos el numero de comentario del uusuario
      EjecutaQuery("UPDATE k_usu_notify SET no_notice=$no_notify WHERE fl_usuario=$fl_post_usuario");
    }
    
    # Direct the user to the correct path based on profile
    if($fl_post_perfil == PFL_ESTUDIANTE_SELF){
      $path = PATH_SELF."/index.php#site/gallery.php?";
    } else {
      $path = PATH_SELF."/index.php#site/gallery.php?";
    }

    $variables = array(
      'us_fname' => $ds_post_nombres,
      'us_fname_from' => $ds_nombres,
      'us_lname_from' => $ds_apaterno,
      'ds_comment' => $ds_orig_comment
    );
    
    # Generate the email template with the variables
    $ds_email_template = GenerateTemplate($ds_template, $variables);

    $envio_fame = false;
    # Send to author, but don't send to myself (if post author is not commentor)
    if($fl_usuario != $fl_post_usuario){
      EnviaMailHTML($from, $from, $ds_post_email, $nb_template, $ds_email_template);
      EnviaMailHTML($from, $from, $email_fame, $nb_template, $ds_email_template);
      $envio_fame = true;
    }
    
    # Everyone else that's involved except for creator and commentor
    if($fame==1){
      $Query  = "SELECT DISTINCT a.fl_usuario, b.ds_nombres, b.ds_apaterno, b.fl_perfil, b.ds_email ";
      $Query .= "FROM k_gallery_comment_sp a ";
      $Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_usuario ";
      $Query .= "WHERE fl_gallery_post_sp=$fl_gallery_post_sp ";
      $Query .= "AND a.fl_usuario!=$fl_post_usuario ";
      $Query .= "AND a.fl_usuario!=$fl_usuario ";
    }
    else{
      $Query  = "SELECT DISTINCT a.fl_usuario, b.ds_nombres, b.ds_apaterno, b.fl_perfil, b.ds_email ";
      $Query .= "FROM k_gallery_comment a ";
      $Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_usuario ";
      $Query .= "WHERE fl_gallery_post=$fl_gallery_post_sp ";
      $Query .= "AND a.fl_usuario!=$fl_post_usuario ";
      $Query .= "AND a.fl_usuario!=$fl_usuario ";
    }
    $rs = EjecutaQuery($Query);
    $tot_comments = CuentaRegistros($rs);
    $usu_comenta = "[";
    if($tot_comments > 0){
      // while($row=RecuperaRegistro($rs)){
      for($i=0;$row=RecuperaRegistro($rs);$i++){
        $fl_comm_usuario = $row[0];
        $ds_comm_nombres = $row[1];
        $ds_comm_apaterno = $row[2];
        $fl_comm_perfil = $row[3];
        $ds_comm_email = $row[4];

        # Direct the user to the correct path based on profile
        if($fl_comm_perfil == PFL_ESTUDIANTE_SELF){
          $path = PATH_SELF."/index.php#site/gallery.php?";
        } else {
          $path = PATH_SELF."/index.php#site/gallery.php?";
        }

        $variables = array(
          'us_fname' => $ds_comm_nombres,
          'us_fname_from' => $ds_nombres,
          'us_lname_from' => $ds_apaterno,
          'ds_comment' => $ds_orig_comment
        );
        # Generate the email template with the variables
        $ds_email_template = GenerateTemplate($ds_template, $variables);

        $bcc_fame = $email_fame;
        if($envio_fame)
          $bcc_fame = "";
       EnviaMailHTML($from, $from, $ds_comm_email, $nb_template, $ds_email_template);
       EnviaMailHTML($from, $from, $bcc_fame, $nb_template, $ds_email_template);
        $usurios_com["$i"] = $fl_comm_usuario;
        # Actualizamos la notificaciones del usuario que comenta
        if($fame==1){
          $rowe = RecuperaValor("SELECT no_notice FROM k_usu_notify WHERE fl_usuario=$fl_comm_usuario");
          $no_notice = $rowe[0] + 1;
          if(ExisteEnTabla('k_usu_notify', 'fl_usuario', $fl_comm_usuario))
            EjecutaQuery("UPDATE k_usu_notify SET no_notice=$no_notice WHERE fl_usuario=$fl_comm_usuario");
          else
            EjecutaQuery('INSERT INTO k_usu_notify (fl_usuario,no_notice) VALUES ('.$fl_comm_usuario.', '.$no_notice.')');
        }
      }
    }    
  }   
    # acatualizamos la fecha
    if($fame==1)
      EjecutaQuery("UPDATE k_gallery_post_sp SET fe_post=CURRENT_TIMESTAMP WHERE fl_gallery_post_sp=$fl_gallery_post_sp");
    else
      EjecutaQuery("UPDATE k_gallery_post SET fe_post=CURRENT_TIMESTAMP WHERE fl_gallery_post=$fl_gallery_post_sp");
  
   $success['success'] = true;
   $success['fame'] = $fame;
   $success['tot_comments'] = $tot_comments;
   $success['usu_comentan'] = $usurios_com;
   $success['fl_gallery_comment_sp_ultimo'] = $fl_gallery_comment_sp_ultimo;   
  echo json_encode((Object) $success);
?>
