<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_PAGOS, PERMISO_BAJA)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $clave_param = RecibeParametroHTML('clave');
  $clave_compuesta = explode('_', $clave_param);
  $clave = $clave_compuesta[0];
  $fl_term = $clave_compuesta[1];
  
  # Verifica que se haya recibido la clave
  if(empty($clave) OR empty($fl_term)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Elimina el registro
  EjecutaQuery("DELETE FROM k_alumno_term WHERE fl_alumno=$clave AND fl_term=$fl_term");
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>