<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(132, PERMISO_BAJA)) {
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
  # Eliminamos los registros de la clase general  
  EjecutaQuery("DELETE FROM k_curso_cg WHERE fl_clase_global=$clave");
  EjecutaQuery("DELETE FROM k_clase_cg WHERE fl_clase_global=$clave");
  EjecutaQuery("DELETE FROM k_alumno_cg WHERE fl_clase_global=$clave");
  
  EjecutaQuery("DELETE FROM c_clase_global WHERE fl_clase_global=$clave");
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>