<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_FREE_TRIAL, PERMISO_BAJA)) {
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

  
  
 
  
  #eliminamso el cnstituto
  EjecutaQuery("DELETE FROM c_instituto WHERE fl_instituto=$clave");
  #Elimimnaso el criterio
  EjecutaQuery("DELETE FROM c_usuario WHERE fl_instituto=$clave");
  
  
  header("Location: ".ObtenProgramaBase( ));
  
  
?>