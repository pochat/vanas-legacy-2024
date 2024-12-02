<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_flujo, nb_flujo '".ObtenEtiqueta(140)."', ".EscogeIdioma('ds_flujo','tr_flujo')." '".ETQ_DESCRIPCION."', ";
  $Query .= "(SELECT COUNT(1) FROM k_flujo_nivel b WHERE a.fl_flujo=b.fl_flujo) '".ObtenEtiqueta(141)."|right', ";
  $Query .= "CASE fg_default WHEN 1 THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END '".ObtenEtiqueta(143)."|center' ";
  $Query .= "FROM c_flujo a ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE nb_flujo LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE ds_flujo LIKE '%$criterio%' OR tr_flujo LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE nb_flujo LIKE '%$criterio%' OR ";
        $Query .= "ds_flujo LIKE '%$criterio%' OR tr_flujo LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY nb_flujo";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_FLUJOS, $Query, TB_LN_IUD, True, False, array(ObtenEtiqueta(140), ETQ_DESCRIPCION));
  
?>