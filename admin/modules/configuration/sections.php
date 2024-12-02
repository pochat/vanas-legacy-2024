<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_funcion, lb_modulo '".ObtenEtiqueta(164)."', lb_funcion '".ObtenEtiqueta(154)."', ds_funcion '".ETQ_DESCRIPCION."', ";
  $Query .= "nb_tipo_contenido '".ObtenEtiqueta(232)."', no_contenidos '".ObtenEtiqueta(231)."|right', ";
  $Query .= "ds_menu '".ObtenEtiqueta(165)."|center', no_orden '".ETQ_ORDEN."|right', ds_fijo '".ObtenEtiqueta(161)."|center' ";
  $Query .= "FROM ( ";
  $Query .= "SELECT fl_funcion, nb_funcion, tr_funcion, ds_funcion, nb_modulo, tr_modulo, nb_tipo_contenido, a.fg_fijo fg_fijo, a.no_orden, ";
  $Query .= EscogeIdioma('nb_funcion','tr_funcion')." lb_funcion, ";
  $Query .= EscogeIdioma('nb_modulo','tr_modulo')." lb_modulo, ";
  $Query .= "(SELECT count(1) FROM c_contenido c WHERE c.fl_funcion=a.fl_funcion) no_contenidos, ";
  $Query .= "CASE a.fg_menu WHEN 1 THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END ds_menu, ";
  $Query .= "CASE a.fg_fijo WHEN 1 THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END ds_fijo ";
  $Query .= "FROM c_funcion a LEFT JOIN c_modulo b ON a.fl_modulo=b.fl_modulo, c_tipo_contenido c ";
  $Query .= "WHERE a.cl_tipo_contenido=c.cl_tipo_contenido ";
  $Query .= "AND fg_tipo_seguridad <> 'A' ";
  $Query .= "AND a.cl_tipo_contenido <> '".TC_PROGRAMA."' ";
  $Query .= "AND (b.fg_admon IS NULL OR b.fg_admon='0')) AS principal ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE nb_modulo LIKE '%$criterio%' OR tr_modulo LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE nb_funcion LIKE '%$criterio%' OR tr_funcion LIKE '%$criterio%' "; break;
      case 3: $Query .= "WHERE ds_funcion LIKE '%$criterio%' "; break;
      case 4: $Query .= "WHERE nb_tipo_contenido LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE nb_funcion LIKE '%$criterio%' OR tr_funcion LIKE '%$criterio%' OR ds_funcion LIKE '%$criterio%' ";
        $Query .= "OR nb_modulo LIKE '%$criterio%' OR tr_modulo LIKE '%$criterio%' OR nb_tipo_contenido LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY fg_fijo DESC, nb_modulo, no_orden";
  
  # Muestra pagina de listado
  $opc = array(ObtenEtiqueta(164), ObtenEtiqueta(154), ETQ_DESCRIPCION, ObtenEtiqueta(232));
  PresentaPaginaListado(FUNC_SECCIONES, $Query, TB_LN_IUD, True, False, $opc);
  
?>