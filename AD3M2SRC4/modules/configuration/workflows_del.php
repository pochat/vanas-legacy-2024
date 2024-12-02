<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_FLUJOS, PERMISO_BAJA)) {
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
  
  # Valida que no haya registros relacionados
  if(ExisteEnTabla('c_funcion', 'fl_flujo', $clave)) {
    MuestraPaginaError(ERR_REFERENCIADO);
    exit;
  }
  
  # Elimina el registro
	EjecutaQuery("DELETE FROM k_nivel_usuario WHERE fl_flujo = $clave");
	EjecutaQuery("DELETE FROM k_nivel_perfil WHERE fl_flujo = $clave");
	EjecutaQuery("DELETE FROM k_flujo_nivel WHERE fl_flujo = $clave");
	EjecutaQuery("DELETE FROM c_flujo WHERE fl_flujo = $clave");
	
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>