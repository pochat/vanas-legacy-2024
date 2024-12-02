<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_tema, nb_tema '".ObtenEtiqueta(18)."', CASE fg_tipo WHEN 'F' THEN 'Forum' WHEN 'P' THEN 'Program' WHEN 'S' THEN 'School News' END '".ObtenEtiqueta(44)."', ";
  $Query .= "no_orden '".ETQ_ORDEN."|right', no_posts '".ObtenEtiqueta(560)."|right' ";
  $Query .= "FROM c_f_tema ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE nb_tema LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE no_orden LIKE '%$criterio%' "; break;
      case 3: $Query .= "WHERE no_posts LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE nb_tema LIKE '%$criterio%' OR no_orden LIKE '%$criterio%' OR no_posts LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY no_orden ";
  
  # Muestra pagina de listado
  $opc = array(ObtenEtiqueta(18), ETQ_ORDEN, ObtenEtiqueta(560));
  PresentaPaginaListado(FUNC_FORO, $Query, TB_LN_IUD, True, False, $opc);
  
?>