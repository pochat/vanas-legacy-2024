<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_class, nb_programa '".ObtenEtiqueta(380)."', no_grado '".ObtenEtiqueta(375)."|right', ";
  $Query .= "nb_class '".ObtenEtiqueta(640)."', a.no_orden '".ETQ_ORDEN."|right', (SELECT COUNT(1) FROM c_leccion c WHERE c.fl_class = a.fl_class) '".ObtenEtiqueta(641)."|right' ";
  $Query .= "FROM c_class a, c_programa b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND nb_programa LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND no_grado LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND nb_class LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND a.no_orden LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND (nb_programa LIKE '%$criterio%' ";
        $Query .= "OR no_grado LIKE '%$criterio%' ";
        $Query .= "OR nb_class LIKE '%$criterio%' ";
        $Query .= "OR a.no_orden LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY  b.no_orden, no_grado, a.no_orden";
  
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(380), ObtenEtiqueta(375), ObtenEtiqueta(640), ETQ_ORDEN);
  PresentaPaginaListado(FUNC_CLASSES, $Query, TB_LN_IUD, True, False, $campos);
  
?>