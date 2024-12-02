<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT cl_mensaje, cl_mensaje '".ETQ_CLAVE."|right', ".EscogeIdioma('ds_titulo','tr_titulo')." '".ETQ_TITULO."', ";
  $Query .= EscogeIdioma('ds_mensaje','tr_mensaje')." '".ObtenEtiqueta(130)."', ";
  $Query .= "CASE fg_severidad WHEN 'I' THEN '".ETQ_TIT_INFO."' WHEN 'W' THEN '".ETQ_TIT_WARN."' ";
  $Query .= "WHEN 'E' THEN '".ETQ_TIT_ERROR."' WHEN 'P' THEN '".ETQ_TIT_CONFIRM."' ELSE fg_severidad END '".ObtenEtiqueta(131)."' ";
  $Query .= "FROM c_mensaje ";
  $Query .= "WHERE cl_mensaje > 0 ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND cl_mensaje LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND (ds_titulo LIKE '%$criterio%' OR tr_titulo LIKE '%$criterio%') "; break;
      case 3: $Query .= "AND (ds_mensaje LIKE '%$criterio%' OR tr_mensaje LIKE '%$criterio%') "; break;
      case 4: $Query .= "AND fg_severidad LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND (cl_mensaje LIKE '%$criterio%' OR fg_severidad LIKE '%$criterio%' ";
        $Query .= "OR ds_titulo LIKE '%$criterio%' OR tr_titulo LIKE '%$criterio%' ";
        $Query .= "OR ds_mensaje LIKE '%$criterio%'OR tr_mensaje LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY cl_mensaje";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_MENSAJES, $Query, TB_LN_NUN, True, False, array(ETQ_CLAVE, ETQ_TITULO, ObtenEtiqueta(130), ObtenEtiqueta(131)));
  
?>