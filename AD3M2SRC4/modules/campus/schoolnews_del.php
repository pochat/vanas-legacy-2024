<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_BLOGS, PERMISO_BAJA)) {
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
  
  # Elimina registros relacionados
	EjecutaQuery("DELETE FROM k_com_blog WHERE fl_blog=$clave");
	EjecutaQuery("DELETE FROM k_not_blog WHERE fl_blog=$clave");
	
  # Elimina el registro
	EjecutaQuery("DELETE FROM c_blog WHERE fl_blog=$clave");
	
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>