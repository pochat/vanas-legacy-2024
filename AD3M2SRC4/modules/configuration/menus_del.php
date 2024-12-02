<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MENUS, PERMISO_BAJA)) {
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
  
  # Valida que no sea un modulo de sistema
  $row = RecuperaValor("SELECT fg_fijo FROM c_modulo WHERE fl_modulo=$clave");
  if($row[0] == 1) {
    MuestraPaginaError(ERR_FG_FIJO);
    exit;
  }
  
  # Valida que no haya registros relacionados
  if(ExisteEnTabla('c_modulo', 'fl_modulo_padre', $clave) OR ExisteEnTabla('c_funcion', 'fl_modulo', $clave)) {
    MuestraPaginaError(ERR_REFERENCIADO);
    exit;
  }
  
  # Actualiza registros relacionados
	EjecutaQuery("UPDATE c_funcion SET fl_modulo=NULL WHERE fl_modulo=$clave");
	
  # Elimina el registro
	EjecutaQuery("DELETE FROM c_modulo WHERE fl_modulo=$clave");
	
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>