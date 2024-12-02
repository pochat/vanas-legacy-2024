<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_FORO, PERMISO_BAJA)) {
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
  $rs = EjecutaQuery("SELECT DISTINCT fl_post FROM k_f_post WHERE fl_tema = $clave");
  while($row = RecuperaRegistro($rs))
  {
    EjecutaQuery("DELETE FROM k_f_comentario WHERE fl_post = $row[0]");  
  }
  EjecutaQuery("DELETE FROM k_f_post WHERE fl_tema = $clave"); 
  EjecutaQuery("DELETE FROM k_f_usu_tema WHERE fl_tema=$clave");
	
  # Elimina el registro
	EjecutaQuery("DELETE FROM c_f_tema WHERE fl_tema=$clave");
	
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>