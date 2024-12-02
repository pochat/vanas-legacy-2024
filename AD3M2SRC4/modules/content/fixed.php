<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT CONCAT(\"'\", cl_pagina, '_', fl_programa, '_', no_grado, \"'\") 'clave', cl_pagina '".ETQ_CLAVE."|right', ";
  $Query .= "(SELECT nb_programa FROM c_programa b WHERE b.fl_programa = a.fl_programa) '".ObtenEtiqueta(512)."', ";
  $Query .= "CASE WHEN no_grado=0 THEN '' ELSE no_grado END '".ObtenEtiqueta(422)."', nb_pagina '".ObtenEtiqueta(270)."', ";
  $Query .= "ds_pagina '".ETQ_DESCRIPCION."', ".EscogeIdioma('ds_titulo', 'tr_titulo')." '".ETQ_TITULO."' ";
  $Query .= "FROM c_pagina a ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE cl_pagina LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE nb_pagina LIKE '%$criterio%' "; break;
      case 3: $Query .= "WHERE ds_pagina LIKE '%$criterio%' "; break;
      case 4: $Query .= "WHERE (ds_titulo LIKE '%$criterio%' OR tr_titulo LIKE '%$criterio%') "; break;
      default:
        $Query .= "WHERE (cl_pagina LIKE '%$criterio%' OR nb_pagina LIKE '%$criterio%' OR ds_pagina LIKE '%$criterio%' ";
        $Query .= "OR ds_titulo LIKE '%$criterio%' OR tr_titulo LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY cl_pagina";

  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_FIXED, $Query, TB_LN_IUD, True, False, array(ETQ_CLAVE, ObtenEtiqueta(270), ETQ_DESCRIPCION, ETQ_TITULO));
  
?>