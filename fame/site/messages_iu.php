<?php
  # Libreria de funciones
  require("../lib/self_general.php");

  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_usuario_dest = RecibeParametroNumerico('usr');
  $ds_mensaje = RecibeParametroHTML('message');
  
  # Envia el mensaje del usuario destino
  if(!empty($fl_usuario_dest) AND !empty($ds_mensaje)) {
    // Transforms url links
    $ds_mensaje = PorcesaCadena($ds_mensaje);

    # Sanitize input (special cases)
    /* @url: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/HTML5/HTML5_Parser
     * Lack of Reparsing
     */
    $ds_mensaje = str_replace("&lt;!", "&#60;!", $ds_mensaje);   // html comment
    $ds_mensaje = str_replace("&lt;?", "&#60;?", $ds_mensaje);   // html comment

    $Query  = "INSERT INTO k_mensaje_directo (fl_usuario_ori, fl_usuario_dest, ds_mensaje) ";
    $Query .= "VALUES($fl_usuario, $fl_usuario_dest, '$ds_mensaje')";
    $fl_mensaje_directo = EjecutaInsert($Query);
    
    # Identificamos si el usuario  destino de FAME o VANAS
    $p = ObtenPerfilUsuario($fl_usuario_dest);
    # Si destino es uno indica que es de VANAS
    $destino =1;
    if(!empty($p))
      $destino=0;
    
	#Recupermso el nombre a quien esta enviando el mensaje.
	$Query="SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario ";
	$row=RecuperaValor($Query);
	$ds_nombre_destinatario=str_texto($row['ds_nombres'])." ".str_texto($row['ds_apaterno']);
	
	 # Find the time for the new inserted message
	  $diferencia = RecuperaDiferenciaGMT( );
	  $Query  = "SELECT DATE_FORMAT((DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR)), '%M %e, %Y at %l:%i %p') 'fe_message', b.ds_email ";
	  //$Query  = "SELECT DATE_FORMAT((DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR)), '%Y-%m-%d %H:%i:%s') 'fe_message', b.ds_email ";
	  $Query .= "FROM k_mensaje_directo a LEFT JOIN c_usuario b ON(b.fl_usuario=a.fl_usuario_dest) ";
	  $Query .= "WHERE fl_mensaje_directo=$fl_mensaje_directo";
	  $row = RecuperaValor($Query);
	  $fe_message = $row[0];
	  $ds_email = $row[1];
	
	#Obtenemos la fecha,
   // $fe_message=time_elapsed_stringChat($fe_message, true);
	
	
	
	$img_avatar_ori=ObtenAvatarUsuario($fl_usuario);
	$img_avatar_dest=ObtenAvatarUsuario($fl_usuario_dest);
	
    echo json_encode((Object)array(
      'success' => 'Message stored.', 
      'fl_message' => $fl_mensaje_directo,
	  'fl_usuario_ori'=>$fl_usuario,
	  'fl_usuario_dest'=>''.$fl_usuario_dest.'',
	  'fe_mensaje'=>''.$fe_message.'',
	  'img_avatar_ori'=>''.$img_avatar_ori.'',
	  'img_avatar_dest'=>''.$img_avatar_dest.'',
	  'ds_nombre_destinatario'=>''.$ds_nombre_destinatario.'',
      'destino' =>$destino
    ));
  } else {
    echo json_encode((Object)array('error' => 'Server Error. Cannot store message.'));
  }
  
  
  

  
?>