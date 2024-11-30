<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require("../../common/lib/cam_forum.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario_ori = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_usuario_dest = RecibeParametroNumerico('usr');
  $ds_mensaje = htmlspecialchars_decode(RecibeParametroHTML('message'));
  
  # Envia el mensaje del usuario destino
  if(!empty($fl_usuario_dest) AND !empty($ds_mensaje)) {
    // Transforms url links
    //$ds_mensaje = PorcesaCadena($ds_mensaje);

    # Sanitize input (special cases)
    /* @url: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/HTML5/HTML5_Parser
     * Lack of Reparsing
     */
    $ds_mensaje = str_replace("&lt;!", "&#60;!", $ds_mensaje);   // html comment
    //$ds_mensaje = str_replace("&lt;?", "&#60;?", $ds_mensaje);   // html comment
	
	#Recupermso el nombre a quien esta enviando el mensaje.
	$Query="SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario_ori ";
	$row=RecuperaValor($Query);
	$ds_nombre_destinatario=str_texto($row['ds_nombres'])." ".str_texto($row['ds_apaterno']);

    $Query  = "INSERT INTO k_mensaje_directo (fl_usuario_ori, fl_usuario_dest, ds_mensaje) ";
    $Query .= "VALUES($fl_usuario_ori, $fl_usuario_dest, '$ds_mensaje')";
    $fl_mensaje_directo = EjecutaInsert($Query);
    
   # Identificamos si el usuario  destino de FAME o VANAS
    $p = ObtenPerfil($fl_usuario_dest);
    # Si destino es uno indica que es de VANAS
    $destino =0;
    if(!empty($p))
      $destino=1;
 

	  # Find the time for the new inserted message
	  $diferencia = RecuperaDiferenciaGMT( ); 
	  
	  #Recuperamos fe_mensaje 
	  $Query  = "SELECT DATE_FORMAT((DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR)), '%M %e, %Y at %l:%i %p') 'fe_message', b.ds_email ";
	  //$Query  = "SELECT DATE_FORMAT((DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR)), '%Y-%m-%d %H:%i:%s') 'fe_message', b.ds_email ";
	  $Query .= "FROM k_mensaje_directo a LEFT JOIN c_usuario b ON(b.fl_usuario=a.fl_usuario_dest) ";
	  $Query .= "WHERE fl_mensaje_directo=$fl_mensaje_directo";
	  $row = RecuperaValor($Query);
	  $fe_message = $row[0];
	  $ds_email = $row[1];

     #Obtenemos la fecha,
     //$fe_message=time_elapsed_stringChat($fe_message, true);
      #Identificamos el ds_nombre de la imagen del teacher..
     $Query="SELECT ds_ruta_avatar FROM c_alumno WHERE fl_alumno=$fl_usuario_ori ";
     $row=RecuperaValor($Query);
     $ds_imagen=str_uso_normal($row[0]); 
	 
	 #Identificamos al usuario destino
	 
	 
	 if($ds_imagen)
     $img_avatar_ori="".PATH_ALU_IMAGES."/avatars/".$ds_imagen;
	 else
	 $img_avatar_ori=ObtenAvatarUsuario($fl_usuario);
	 
	 
	  #IDENTIFICAMOS EL USUARIO DESTINO.
	 $Query="SELECT fl_perfil FROM c_usuario WHERE fl_usuario=$fl_usuario_dest ";
	 $row=RecuperaValor($Query);
	 $fl_perfil=$row[0];
	 
	 if($fl_perfil==PFL_MAESTRO){
	  
		 #Identificamos el ds_nombre de la imagen del teacher..
		 $Query="SELECT ds_ruta_avatar FROM c_maestro WHERE fl_maestro=$fl_usuario_dest ";
		 $row=RecuperaValor($Query);
		 $ds_imagen_des=str_uso_normal($row[0]);
		 
		 if($ds_imagen_des)
		 $img_avatar_dest="".PATH_MAE_IMAGES."/avatars/".$ds_imagen_des;
		 else
		 $img_avatar_dest=ObtenAvatarUsuario($fl_usuario_dest);
		 
	 }
	 if($fl_perfil==PFL_ESTUDIANTE){
	 
	     #Identificamos el ds_nombre de la imagen del teacher..
		 $Query="SELECT ds_ruta_avatar FROM c_alumno WHERE fl_alumno=$fl_usuario_dest ";
		 $row=RecuperaValor($Query);
		 $ds_imagen_des=str_uso_normal($row[0]);
	 
	     if($ds_imagen_des)
		 $img_avatar_dest="".PATH_ALU_IMAGES."/avatars/".$ds_imagen_des;
		 else
		 $img_avatar_dest=ObtenAvatarUsuario($fl_usuario_dest);
		 
	 
	 
	 
	    // $img_avatar_dest=ObtenAvatarUsuario($fl_usuario_dest);
	 
	 }
	 
	 
	 
	 
	 //$img_avatar_dest=ObtenAvatarUsuario($fl_usuario_dest);

 
    echo json_encode((Object)array(
      'success' => 'Message stored.', 
      'fl_message' => $fl_mensaje_directo,
	  'fl_usuario_ori'=>$fl_usuario_ori,
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