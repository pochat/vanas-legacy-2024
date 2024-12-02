<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT a.cl_tipo_contenido, nb_tipo_contenido '".ETQ_TIPO."', ";
  $Query .= EscogeIdioma('ds_tipo_contenido','tr_tipo_contenido')." '".ETQ_DESCRIPCION."', ";
  $Query .= "(SELECT COUNT(1) FROM k_tipo_contenido_template b WHERE a.cl_tipo_contenido=b.cl_tipo_contenido) '".ObtenEtiqueta(150)."|right', ";
  $Query .= "(SELECT COUNT(1) FROM c_funcion b WHERE a.cl_tipo_contenido=b.cl_tipo_contenido) '".ObtenEtiqueta(151)."|right' ";
  $Query .= "FROM c_tipo_contenido a ";
  $Query .= "WHERE a.cl_tipo_contenido <> ".TC_PROGRAMA." ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND nb_tipo_contenido LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_tipo_contenido LIKE '%$criterio%' OR tr_tipo_contenido LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND nb_tipo_contenido LIKE '%$criterio%' OR ";
        $Query .= "ds_tipo_contenido LIKE '%$criterio%' OR tr_tipo_contenido LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY cl_tipo_contenido";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_CONTENIDOS, $Query, TB_LN_NUN, True, False, array(ETQ_TIPO, ETQ_DESCRIPCION));
  
?>