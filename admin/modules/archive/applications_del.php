<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_APP_FRM, PERMISO_BAJA)) {
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
  
  # Recupera valor de la sesion
  $row = RecuperaValor("SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave");
  $cl_sesion = $row[0];
  
  # Elimina registros referenciados
  EjecutaQuery("DELETE FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion'");
  EjecutaQuery("DELETE FROM k_ses_app_frm_2 WHERE cl_sesion='$cl_sesion'");
  EjecutaQuery("DELETE FROM k_ses_app_frm_3 WHERE cl_sesion='$cl_sesion'");
  EjecutaQuery("DELETE FROM k_ses_app_frm_4 WHERE cl_sesion='$cl_sesion'");
  
  # Elimina el registro
  EjecutaQuery("DELETE FROM c_sesion WHERE fl_sesion=$clave");
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>