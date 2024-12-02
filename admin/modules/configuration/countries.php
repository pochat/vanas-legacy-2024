<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_pais, ds_pais '".ObtenEtiqueta(352)."', nb_pais '".ObtenEtiqueta(351)."', ";
  $Query .= "cl_iso2 '".ObtenEtiqueta(350)."', cl_iso3 '".ObtenEtiqueta(353)."' ";
  $Query .= "FROM c_pais ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE ds_pais LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE nb_pais LIKE '%$criterio%' "; break;
      case 3: $Query .= "WHERE cl_iso2 LIKE '%$criterio%' "; break;
      case 4: $Query .= "WHERE cl_iso3 LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE ds_pais LIKE '%$criterio%' OR nb_pais LIKE '%$criterio%' ";
        $Query .= "OR cl_iso2 LIKE '%$criterio%' OR cl_iso3 LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY ds_pais";
  
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(352), ObtenEtiqueta(351), ObtenEtiqueta(350), ObtenEtiqueta(353));
  PresentaPaginaListado(FUNC_PAISES, $Query, TB_LN_IUD, True, False, $campos);
  
?>