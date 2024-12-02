<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_SECCIONES, PERMISO_BAJA)) {
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
  
  # Valida que no sea una funcion de sistema
  $row = RecuperaValor("SELECT fg_fijo FROM c_funcion WHERE fl_funcion=$clave");
  if($row[0] == 1) {
    MuestraPaginaError(ERR_FG_FIJO);
    exit;
  }
  
  # Valida que haya registros relacionados
  if(ExisteEnTabla('c_contenido', 'fl_funcion', $clave)) {
    MuestraPaginaError(ERR_REFERENCIADO);
    exit;
  }
  
  # Elimina registros relacionados
	EjecutaQuery("DELETE FROM k_per_funcion WHERE fl_funcion=$clave");
	
  # Elimina el registro
	EjecutaQuery("DELETE FROM c_funcion WHERE fl_funcion=$clave");
	
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>