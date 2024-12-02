<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_contacto, ".EscogeIdioma('ds_area', 'tr_area')." '".ObtenEtiqueta(240)."', ";
  $Query .= "ds_email '".ObtenEtiqueta(121)."', no_orden '".ETQ_ORDEN."|right' ";
  $Query .= "FROM c_contacto ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE ds_area LIKE '%$criterio%' OR tr_area LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE ds_email LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE ds_area LIKE '%$criterio%' OR tr_area LIKE '%$criterio%' OR ds_email LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY no_orden";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_CORREOS, $Query, TB_LN_IUD, True, False, array(ObtenEtiqueta(240), ObtenEtiqueta(121)));
  
?>