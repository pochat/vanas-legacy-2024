<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_categoria, fl_categoria '".ETQ_CLAVE."|right', nb_categoria '".ObtenEtiqueta(574)."', ds_categoria '".ObtenEtiqueta(19)."' ";
  $Query .= "FROM c_categoria_doc ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND fl_categoria LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND nb_categoria LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND ds_categoria LIKE '%$criterio%' "; break;
      default: $Query .= "AND (fl_categoria LIKE '%$criterio%' OR nb_categoria LIKE '%$criterio%' OR ds_categoria LIKE '%$criterio%' ) ";
    }
  }
  $Query .= "ORDER BY fl_categoria";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_CATEGORIAS, $Query, TB_LN_IUD, True, False, array(ETQ_CLAVE, ObtenEtiqueta(574), ObtenEtiqueta(19)));
  
?>