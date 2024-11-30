<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_entrega_semanal = RecibeParametroNumerico('fl_entrega_semanal');
  $fl_calificacion = RecibeParametroNumerico('fl_calificacion');
  if(empty($fl_calificacion))
    $fl_calificacion = 'NULL';
  
  # Recupera los datos de la entrega de la semana
  $Query  = "UPDATE k_entrega_semanal SET fl_promedio_semana=$fl_calificacion ";
  $Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal";
  EjecutaQuery($Query);
  
  # Redirige al listado
  header("Location: submitted_assignments.php");
  
?>