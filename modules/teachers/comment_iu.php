<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $ds_redirect = RecibeParametroHTML('redirect');
  $fl_entrega_semanal = RecibeParametroNumerico('entrega');
  $fg_tipo = RecibeParametroHTML('tipo');
  $fg_otro_alumno = RecibeParametroBinario('otro_alumno');
  $ds_comentario = RecibeParametroHTML('comment');
  
  # Inserta el comentari del usuario
  if(!empty($fl_entrega_semanal) AND !empty($ds_comentario)) {
    if($fg_otro_alumno)
      $fg_leido = '0';
    else
      $fg_leido = '1';
    $Query  = "INSERT INTO k_com_entregable(fl_entrega_semanal, fg_tipo, fl_usuario, fe_comentario, ds_comentario, fg_leido) ";
    $Query .= "VALUES($fl_entrega_semanal, '$fg_tipo', $fl_usuario, CURRENT_TIMESTAMP, '$ds_comentario', '$fg_leido')";
    EjecutaQuery($Query);
  }
  
  # Redirige al listado
  header("Location: ".str_ascii($ds_redirect));
  
?>