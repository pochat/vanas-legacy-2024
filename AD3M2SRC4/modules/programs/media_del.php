<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MEDIA, PERMISO_BAJA)) {
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
  if(ExisteEnTabla('k_semana', 'fl_leccion', $clave)) {
    MuestraPaginaError(ERR_REFERENCIADO);
    exit;
	}
  
  # Elimina el registro
  EjecutaQuery("DELETE FROM c_leccion WHERE fl_leccion=$clave");
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>