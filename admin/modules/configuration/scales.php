<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_calificacion, cl_calificacion '".ObtenEtiqueta(400)."', ds_calificacion '".ETQ_DESCRIPCION."', ";
  $Query .= "CASE WHEN fg_aprobado='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END ";
  $Query .= "'".ObtenEtiqueta(401)."|center', no_equivalencia '".ObtenEtiqueta(402)."|right' ";
  $Query .= "FROM c_calificacion ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE cl_calificacion LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE ds_calificacion LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE cl_calificacion LIKE '%$criterio%' ";
        $Query .= "OR ds_calificacion LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY no_equivalencia desc";
  
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(400), ETQ_DESCRIPCION);
  PresentaPaginaListado(FUNC_ESCALAS, $Query, TB_LN_NUN, True, False, $campos);
  
?>