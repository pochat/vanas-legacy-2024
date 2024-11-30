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
  $fl_mensaje_directo = RecibeParametroNumerico('fl_mensaje', True);

  # Find the time for the new inserted message
  $diferencia = RecuperaDiferenciaGMT( );
  $Query  = "SELECT DATE_FORMAT((DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR)), '%M %e, %Y at %l:%i %p') 'fe_message', b.ds_email ";
  $Query .= "FROM k_mensaje_directo a LEFT JOIN c_usuario b ON(b.fl_usuario=a.fl_usuario_dest) ";
  $Query .= "WHERE fl_mensaje_directo=$fl_mensaje_directo";
  $row = RecuperaValor($Query);
  $fe_message = $row[0];
  $ds_email = $row[1];

  echo json_encode($fe_message);
?>