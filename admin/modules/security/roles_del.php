<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_PERFILES, PERMISO_BAJA)) {
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
  if(ExisteEnTabla('c_usuario', 'fl_perfil', $clave) OR ExisteEnTabla('k_nivel_perfil', 'fl_perfil', $clave)) {
    MuestraPaginaError(ERR_REFERENCIADO);
    exit;
	}
  
  # Elimina los registro asociados
  EjecutaQuery("DELETE FROM k_per_funcion WHERE fl_perfil = $clave");
  
  # Elimina el registro
  EjecutaQuery("DELETE FROM c_perfil WHERE fl_perfil = $clave");
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>