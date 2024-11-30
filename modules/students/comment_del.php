<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $ds_redirect = RecibeParametroHTML('redirect');
  $fl_com_entregable = RecibeParametroNumerico('id');
  
  # Elimina el comentario solo si es del mismo usuario
  if(!empty($fl_com_entregable)) {
    $Query  = "DELETE FROM k_com_entregable ";
    $Query .= "WHERE fl_com_entregable=$fl_com_entregable ";
    $Query .= "AND fl_usuario=$fl_usuario";
    EjecutaQuery($Query);
  }
  
  # Redirige al listado
  header("Location: ".str_uso_normal($ds_redirect));
  
?>