<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_template, fl_template '".ETQ_CLAVE."|right', nb_template '".ObtenEtiqueta(19)."', nb_categoria '".ObtenEtiqueta(574)."', ";
  $Query .= "CASE fg_activo WHEN 1 THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END '".ObtenEtiqueta(113)."|center', ";
  $concat = array(ConsultaFechaBD('fe_creacion', FMT_FECHA), "' '", ConsultaFechaBD('fe_creacion', FMT_HORAMIN));
  $Query .= "(".ConcatenaBD($concat).") '".ObtenEtiqueta(575)."', ";
  $concat2 = array(ConsultaFechaBD('fe_modificacion', FMT_FECHA), "' '", ConsultaFechaBD('fe_modificacion', FMT_HORAMIN));
  $Query .= "(".ConcatenaBD($concat2).") '".ObtenEtiqueta(576)."' ";  
  $Query .= "FROM k_template_doc, c_categoria_doc ";
  $Query .= "WHERE k_template_doc.fl_categoria = c_categoria_doc.fl_categoria ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND fl_template LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND nb_template LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND nb_categoria LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND fe_creacion LIKE '%$criterio%' "; break;
      case 5: $Query .= "AND fe_modificacion LIKE '%$criterio%' "; break;
      default: $Query .= "AND (fl_template LIKE '%$criterio%' OR nb_template LIKE '%$criterio%' OR nb_categoria LIKE '%$criterio%' ";
               $Query .= "OR fe_creacion LIKE '%$criterio%' OR fe_modificacion LIKE '%$criterio%' )";
    }
  }
  $Query .= "ORDER BY fl_template";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_DOC_TEMPLATES, $Query, TB_LN_IUD, True, False, array(ETQ_CLAVE, ObtenEtiqueta(19), ObtenEtiqueta(570), ObtenEtiqueta(575), ObtenEtiqueta(576)));
  
?>