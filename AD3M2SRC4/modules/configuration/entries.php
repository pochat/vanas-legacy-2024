<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT a.fl_celda, ".EscogeIdioma('b.nb_tabla', 'b.tr_tabla')." '".ObtenEtiqueta(221)."', ";
  $Query .= "a.no_renglon '".ObtenEtiqueta(260)."|right', ";
  $Query .= EscogeIdioma('a.ds_celda', 'a.tr_celda')." '".ObtenEtiqueta(136)."' ";
  $Query .= "FROM k_celda_tabla a, c_tabla b, k_columna_tabla c ";
  $Query .= "WHERE a.fl_columna=c.fl_columna ";
  $Query .= "AND c.fl_tabla=b.fl_tabla ";
  $Query .= "AND c.no_orden=1 ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND (b.nb_tabla LIKE '%$criterio%' OR b.tr_tabla LIKE '%$criterio%') "; break;
      case 2: $Query .= "AND a.no_renglon LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND (a.ds_celda LIKE '%$criterio%' OR a.tr_celda LIKE '%$criterio%') "; break;
      default:
        $Query .= "AND (b.nb_tabla LIKE '%$criterio%' OR b.tr_tabla LIKE '%$criterio%' ";
        $Query .= "OR a.no_renglon LIKE '%$criterio%' ";
        $Query .= "OR a.ds_celda LIKE '%$criterio%' OR a.tr_celda LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY b.nb_tabla, a.no_renglon, c.no_orden";
  
  # Muestra pagina de listado
  $opc = array(ObtenEtiqueta(221), ObtenEtiqueta(260), ObtenEtiqueta(136));
  PresentaPaginaListado(FUNC_REGISTROS, $Query, TB_LN_IUD, True, False, $opc);
  
?>