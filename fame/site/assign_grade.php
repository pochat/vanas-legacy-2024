<?php
  
  # Libreria de funciones	
	require("../lib/self_general.php");
  
  # Recibe parametros
  $fl_entrega_semanal_sp = RecibeParametroNumerico('fl_entrega_semanal_sp');
  $fl_calificacion = RecibeParametroNumerico('fl_calificacion');
  if(empty($fl_calificacion)){
    $fl_calificacion = 'NULL';
  }  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recupera los datos de la entrega de la semana
  $Query  = "UPDATE k_entrega_semanal_sp SET fl_promedio_semana=$fl_calificacion ";
  $Query .= "WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp";
  EjecutaQuery($Query);
 
?>