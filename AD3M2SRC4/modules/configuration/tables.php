<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT a.fl_tabla, ".EscogeIdioma('a.nb_tabla', 'a.tr_tabla')." '".ObtenEtiqueta(221)."', ";
  $Query .= "a.no_columnas '".ObtenEtiqueta(250)."|right', ";
  $Query .= "(SELECT count(distinct (no_renglon)) FROM k_celda_tabla c WHERE c.fl_columna IN(";
  $Query .= "SELECT fl_columna FROM k_columna_tabla WHERE fl_tabla=a.fl_tabla)) '".ObtenEtiqueta(258)."|right', ";
  $Query .= "(SELECT count(1) FROM k_tabla b WHERE b.fl_tabla=a.fl_tabla) '".ObtenEtiqueta(231)."|right' ";
  $Query .= "FROM c_tabla a ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE a.nb_tabla LIKE '%$criterio%' OR a.tr_tabla LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE a.no_columnas LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE a.nb_tabla LIKE '%$criterio%' OR a.tr_tabla LIKE '%$criterio%' OR a.no_columnas LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY a.nb_tabla";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_TABLAS, $Query, TB_LN_IUD, True, False, array(ETQ_NOMBRE, ObtenEtiqueta(250)));
  
?>