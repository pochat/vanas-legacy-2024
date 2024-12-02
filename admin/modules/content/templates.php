<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT a.cl_template, nb_template '".ETQ_NOMBRE."', ";
  $Query .= EscogeIdioma('ds_template','tr_template')." '".ETQ_DESCRIPCION."', ";
  $Query .= "(SELECT COUNT(1) FROM k_tipo_contenido_template b WHERE a.cl_template=b.cl_template) '".ObtenEtiqueta(230)."|right', ";
  $Query .= "(SELECT COUNT(1) FROM c_contenido b WHERE a.cl_template=b.cl_template) '".ObtenEtiqueta(231)."|right' ";
  $Query .= "FROM c_template a ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE nb_template LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE ds_template LIKE '%$criterio%' OR tr_template LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE nb_template LIKE '%$criterio%' OR ";
        $Query .= "ds_template LIKE '%$criterio%' OR tr_template LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY cl_template";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_TEMPLATES, $Query, TB_LN_NUN, True, False, array(ETQ_NOMBRE, ETQ_DESCRIPCION));
  
?>