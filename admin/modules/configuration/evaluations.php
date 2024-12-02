<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_evaluacion, ds_evaluacion '".ObtenEtiqueta(430)."', ";
  $Query .= "CASE WHEN fg_promedio='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END ";
  $Query .= "'".ObtenEtiqueta(431)."|center' ";
  $Query .= "FROM c_evaluacion ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE ds_evaluacion LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE ds_evaluacion LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY no_orden";
  
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(430));
  PresentaPaginaListado(FUNC_CRITERIOS, $Query, TB_LN_IUD, True, False, $campos);
  
?>