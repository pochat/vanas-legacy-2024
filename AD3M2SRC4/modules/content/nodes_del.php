<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_NODOS, PERMISO_BAJA)) {
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
  $row = RecuperaValor("SELECT fg_fijo FROM c_contenido WHERE fl_contenido=$clave");
  if($row[0] == 1) {
    MuestraPaginaError(ERR_FG_FIJO);
    exit;
  }
  
  # Elimina registros relacionados
	EjecutaQuery("DELETE FROM k_estado_hist WHERE fl_contenido=$clave");
	EjecutaQuery("DELETE FROM k_texto WHERE fl_contenido=$clave");
	EjecutaQuery("DELETE FROM k_imagen_dinamica WHERE fl_contenido=$clave");
	EjecutaQuery("DELETE FROM k_flash WHERE fl_contenido=$clave");
	EjecutaQuery("DELETE FROM k_anexo WHERE fl_contenido=$clave");
	EjecutaQuery("DELETE FROM k_tabla WHERE fl_contenido=$clave");
	EjecutaQuery("DELETE FROM k_liga WHERE fl_contenido=$clave");
	
  # Elimina el registro
	EjecutaQuery("DELETE FROM c_contenido WHERE fl_contenido=$clave");
	
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>