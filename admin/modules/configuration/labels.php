<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT cl_etiqueta, cl_etiqueta '".ETQ_CLAVE."|right', nb_etiqueta '".ETQ_NOMBRE."', ";
  $Query .= EscogeIdioma('ds_etiqueta','tr_etiqueta')." '".ETQ_DESCRIPCION."' ";
  $Query .= "FROM c_etiqueta ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE cl_etiqueta LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE nb_etiqueta LIKE '%$criterio%' "; break;
      case 3: $Query .= "WHERE ds_etiqueta LIKE '%$criterio%' OR tr_etiqueta LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE cl_etiqueta LIKE '%$criterio%' OR nb_etiqueta LIKE '%$criterio%' OR ";
        $Query .= "ds_etiqueta LIKE '%$criterio%' OR tr_etiqueta LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY cl_etiqueta";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_ETIQUETAS, $Query, TB_LN_NUN, True, False, array(ETQ_CLAVE, ETQ_NOMBRE, ETQ_DESCRIPCION));
  
?>