<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $id_usumod = ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MAESTROS, PERMISO_BAJA)) {
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
  
  # Valida que no se este borrando al usuario conectado o el Administrador
	if($clave == $id_usumod OR $clave == ADMINISTRADOR) {
		# El usuario est&aacute; en uso, no es posible eliminarlo.
		MuestraPaginaError(102);
		exit;
	}
  
  # Verifica si existen registros asociados
  if(ExisteEnTabla('c_grupo', 'fl_maestro', $clave)) {
    MuestraPaginaError(ERR_REFERENCIADO);
    exit;
	}
  
  # Elimina el registro
  EjecutaQuery("DELETE FROM c_maestro WHERE fl_maestro=$clave");
  EjecutaQuery("DELETE FROM c_usuario WHERE fl_usuario=$clave");
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>