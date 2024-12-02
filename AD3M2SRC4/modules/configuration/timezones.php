<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_zona_horaria, nb_zona_horaria '".ObtenEtiqueta(440)."', no_latitude '".ObtenEtiqueta(443)."|right', fg_latitude ' ', ";
  $Query .= "no_longitude '".ObtenEtiqueta(444)."|right', fg_longitude ' ', no_gmt '".ObtenEtiqueta(441)."|right', ";
  $Query .= "CASE WHEN fg_default='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(442)."|center' ";
  $Query .= "FROM c_zona_horaria ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE nb_zona_horaria LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE (no_latitude LIKE '%$criterio%' OR fg_latitude LIKE '%$criterio%') "; break;
      case 3: $Query .= "WHERE (no_longitude LIKE '%$criterio%' OR fg_longitude LIKE '%$criterio%') "; break;
      case 4: $Query .= "WHERE no_gmt LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE nb_zona_horaria LIKE '%$criterio%' ";
        $Query .= "OR no_latitude LIKE '%$criterio%' OR fg_latitude LIKE '%$criterio%' ";
        $Query .= "OR no_longitude LIKE '%$criterio%' OR fg_longitude LIKE '%$criterio%' ";
        $Query .= "OR no_gmt LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY fg_default DESC, nb_zona_horaria";
  
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(440), ObtenEtiqueta(443), ObtenEtiqueta(444), ObtenEtiqueta(441));
  PresentaPaginaListado(FUNC_ZONAS, $Query, TB_LN_IUD, True, False, $campos);
  
?>