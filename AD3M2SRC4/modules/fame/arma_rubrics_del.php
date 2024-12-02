<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $fl_criterio = RecibeParametroNumerico('fl_criterio');
  $clave = RecibeParametroNumerico('clave');
  
  EjecutaQuery("DELETE FROM k_criterio_programa_fame WHERE fl_criterio = $fl_criterio AND fl_programa_sp = $clave");
  
?>  