<?php
	
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario_actual = ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_PWD_OTROS, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
	# Recibe parametros
  $ds_password = RecibeParametroHTML('ds_password');
  $ds_password_conf = RecibeParametroHTML('ds_password_conf');
	
	
  # Actualiza el password del usuario
  $ds_password = sha256($ds_password);
  $Query  = "UPDATE c_usuario SET ds_password='$ds_password' ";
  $Query .= "WHERE fl_usuario=$clave";
  EjecutaQuery($Query);
  
?>