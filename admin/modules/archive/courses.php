<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_programa, nb_programa '".ObtenEtiqueta(360)."', ds_duracion '".ObtenEtiqueta(361)."', ";
  $Query .= "ds_tipo '".ObtenEtiqueta(362)."', no_grados '".ObtenEtiqueta(365)."|right', ";
  $Query .= "no_orden '".ETQ_ORDEN."|right' ";
  $Query .= "FROM c_programa a WHERE fg_archive='1' ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND nb_programa LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_duracion LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND ds_tipo LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND nb_programa LIKE '%$criterio%' OR ds_duracion LIKE '%$criterio%' ";
        $Query .= "OR ds_tipo LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY no_orden";
  
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(360), ObtenEtiqueta(361), ObtenEtiqueta(362));
  PresentaPaginaListado(FUNC_CURSOS, $Query, TB_LN_NUN, True, False, $campos);
  
?>