<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_modulo, lb_modulo '".ObtenEtiqueta(160)."', ds_modulo '".ETQ_DESCRIPCION."', no_submenus '".ObtenEtiqueta(163)."|right', ";
  $Query .= "ds_fijo '".ObtenEtiqueta(161)."|center' ";
  $Query .= "FROM ( ";
  $Query .= "SELECT fl_modulo, nb_modulo, tr_modulo, ds_modulo, fg_admon, fg_fijo, ".EscogeIdioma('nb_modulo','tr_modulo')." lb_modulo, ";
  $Query .= "(SELECT count(1) FROM c_modulo b WHERE b.fl_modulo_padre=a.fl_modulo) no_submenus, ";
  $Query .= "CASE fg_fijo WHEN '1' THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END ds_fijo ";
  $Query .= "FROM c_modulo a ";
  $Query .= "WHERE fl_modulo_padre IS NULL ";
  $Query .= "AND fg_admon='0') AS c ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE nb_modulo LIKE '%$criterio%' OR tr_modulo LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE ds_modulo LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE nb_modulo LIKE '%$criterio%' OR tr_modulo LIKE '%$criterio%' OR ds_modulo LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY fl_modulo";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_MENUS, $Query, TB_LN_IUD, True, False, array(ObtenEtiqueta(160), ETQ_DESCRIPCION));
  
?>