<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_TABLAS, PERMISO_BAJA)) {
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
  
  # Verifica si existen registros asociados
  if(ExisteEnTabla('k_tabla', 'fl_tabla', $clave)) {
    MuestraPaginaError(ERR_REFERENCIADO);
    exit;
	}
  
  # Elimina registros relacionados
	EjecutaQuery("DELETE FROM k_celda_tabla WHERE fl_columna IN(SELECT fl_columna FROM k_columna_tabla WHERE fl_tabla=$clave)");
	EjecutaQuery("DELETE FROM k_columna_tabla WHERE fl_tabla=$clave");
	
  # Elimina el registro
	EjecutaQuery("DELETE FROM c_tabla WHERE fl_tabla=$clave");
	
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>