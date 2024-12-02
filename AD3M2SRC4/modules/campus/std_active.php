<?php

  # Librerias
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion();

  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_activo = RecibeParametroBinario('fg_activo');

  # Verifica que se haya recibido la clave
  if(empty($clave)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Actualizamos el usuario
  if(!empty($clave)){
    $Query = "UPDATE c_usuario SET fg_activo = '$fg_activo' WHERE fl_usuario = $clave ";
    EjecutaQuery($Query);
  }
  else
    echo ObtenEtiqueta(22);
  
?>