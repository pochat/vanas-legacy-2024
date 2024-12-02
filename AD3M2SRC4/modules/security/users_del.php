<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $id_usumod = ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_USUARIOS, PERMISO_BAJA)) {
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
  if(ExisteEnTabla('k_nivel_usuario', 'fl_usuario', $clave) OR 
     ExisteEnTabla('k_estado_hist', 'fl_usuario', $clave) OR 
     ExisteEnTabla('c_contenido', 'fl_usuario_alta', $clave) OR 
     ExisteEnTabla('c_contenido', 'fl_usuario_mod', $clave) OR
     ExisteEnTabla('c_blog', 'fl_usuario', $clave)) {
    MuestraPaginaError(ERR_REFERENCIADO);
    exit;
	}
  
  # Elimina el registro
  EjecutaQuery("DELETE FROM c_usuario WHERE fl_usuario=$clave");
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>