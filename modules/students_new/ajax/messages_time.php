<?php
	# Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_mensaje_directo = RecibeParametroNumerico('fl_mensaje', True);

  # Find the time for the new inserted message
  $diferencia = RecuperaDiferenciaGMT( );
  $Query  = "SELECT DATE_FORMAT((DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR)), '%M %e, %Y at %l:%i %p') 'fe_message' ";
  $Query .= "FROM k_mensaje_directo ";
  $Query .= "WHERE fl_mensaje_directo=$fl_mensaje_directo";
  $row = RecuperaValor($Query);
  $fe_message = $row[0];

  echo json_encode($fe_message);
?>