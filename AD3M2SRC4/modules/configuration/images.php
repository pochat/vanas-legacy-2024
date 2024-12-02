<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT cl_imagen, cl_imagen '".ETQ_CLAVE."|right', ds_imagen '".ETQ_DESCRIPCION."', nb_archivo '".ObtenEtiqueta(208)."' ";
  $Query .= "FROM c_imagen ";
  $Query .= "WHERE fg_admon='0' ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND cl_imagen LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_imagen LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND (nb_archivo LIKE '%$criterio%' OR tr_archivo LIKE '%$criterio%') "; break;
      default:
        $Query .= "AND (cl_imagen LIKE '%$criterio%' OR ds_imagen LIKE '%$criterio%' ";
        $Query .= "OR nb_archivo LIKE '%$criterio%' OR tr_archivo LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY cl_imagen";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_IMAGENES, $Query, TB_LN_NUN, True, False, array(ETQ_CLAVE, ETQ_DESCRIPCION, ObtenEtiqueta(208)));
  
?>