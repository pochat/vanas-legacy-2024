<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT cl_configuracion, cl_configuracion '".ETQ_CLAVE."|right', ds_configuracion '".ObtenEtiqueta(135)."', ";
  $Query .= "ds_valor '".ObtenEtiqueta(136)."|center' ";
  $Query .= "FROM c_configuracion ";
  $Query .= "WHERE fg_admin='0' ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND cl_configuracion LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_configuracion LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND ds_valor LIKE '%$criterio%' "; break;
      default: $Query .= "AND (cl_configuracion LIKE '%$criterio%' OR ds_configuracion LIKE '%$criterio%' OR ds_valor LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY cl_configuracion";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_VARIABLES, $Query, TB_LN_NUN, True, False, array(ETQ_CLAVE, ObtenEtiqueta(135), ObtenEtiqueta(136)));
  
?>