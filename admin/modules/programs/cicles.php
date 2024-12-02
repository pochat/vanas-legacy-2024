<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_periodo, nb_periodo '".ObtenEtiqueta(370)."', ";
  $Query .= ConsultaFechaBD('fe_inicio', FMT_FECHA)." '".ObtenEtiqueta(371)."', ";
  $Query .= "(SELECT COUNT(1) FROM k_term b WHERE b.fl_periodo=a.fl_periodo) '".ObtenEtiqueta(373)."|right', ";
  $Query .= "CASE WHEN fg_activo='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(372)."|center' ";
  $Query .= "FROM c_periodo a ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE nb_periodo LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE nb_periodo LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY fe_inicio";
  
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(370));
  PresentaPaginaListado(FUNC_CICLOS, $Query, TB_LN_IUD, True, False, $campos);
  
?>