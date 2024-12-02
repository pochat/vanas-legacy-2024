<?php
  
	# Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );

	# Verifica que el usuario tenga permiso de usar esta funcion
	if(!ValidaPermiso(FUNC_CUPON, PERMISO_BAJA)) {
	MuestraPaginaError(ERR_SIN_PERMISO);
	exit;
	}

	# Recibe parametros
	$clave = RecibeParametroNumerico('clave');

	# Verifica que se haya recibido la clave
	if(empty($clave)) {
	MuestraPaginaError(ERR_SIN_PERMISO);
	exit;
	}
	
	# Eliminar los cupones con los programas
	
	EjecutaQuery("DELETE FROM c_cupones_b2c WHERE fl_cupon=".$clave);
	
	# No hubo errores
	header("Location: ".ObtenProgramaBase( ));

?>