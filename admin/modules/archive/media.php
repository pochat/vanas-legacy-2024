<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_leccion, nb_programa '".ObtenEtiqueta(380)."', ds_duracion '".ObtenEtiqueta(380)." ".ObtenEtiqueta(396)."', no_grado '".ObtenEtiqueta(375)."|right', ";
  $Query .= "no_semana '".ObtenEtiqueta(390)."|right', ds_titulo '".ObtenEtiqueta(385)."', ";
  $Query .= "CASE WHEN ds_as_ruta IS NULL THEN 'No' WHEN ds_as_ruta='' THEN 'No' ELSE 'Yes' END 'Video Brief', ";
  $Query .= "ds_vl_ruta '".ObtenEtiqueta(395)."', ds_vl_duracion '".ObtenEtiqueta(396)."', ";
  $concat = array(ConsultaFechaBD('fe_vl_alta', FMT_FECHA), "' '", ConsultaFechaBD('fe_vl_alta', FMT_HORAMIN));
  $Query .= "(".ConcatenaBD($concat).") '".ObtenEtiqueta(397)."' ";
  $Query .= "FROM c_leccion a, c_programa b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa AND b.fg_archive='1' ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND nb_programa LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_duracion LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND no_grado LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND no_semana LIKE '%$criterio%' "; break;
      case 5: $Query .= "AND ds_titulo LIKE '%$criterio%' "; break;
      case 6: $Query .= "AND ds_vl_ruta LIKE '%$criterio%' "; break;
      case 7: $Query .= "AND ds_vl_duracion LIKE '%$criterio%' "; break;
      case 8: $Query .= "AND fe_vl_alta LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND (nb_programa LIKE '%$criterio%' ";
        $Query .= "OR ds_duracion LIKE '%$criterio%' ";
        $Query .= "OR no_grado LIKE '%$criterio%' ";
        $Query .= "OR no_semana LIKE '%$criterio%' ";
        $Query .= "OR ds_titulo LIKE '%$criterio%' ";
        $Query .= "OR ds_vl_ruta LIKE '%$criterio%' ";
        $Query .= "OR ds_vl_duracion LIKE '%$criterio%' ";
        $Query .= "OR fe_vl_alta LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY no_orden, no_grado, no_semana";
  
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(380),ObtenEtiqueta(380)." ".ObtenEtiqueta(396), ObtenEtiqueta(375), ObtenEtiqueta(390), ObtenEtiqueta(385), ObtenEtiqueta(395), ObtenEtiqueta(396), ObtenEtiqueta(397));
  PresentaPaginaListado(FUNC_MEDIA, $Query, TB_LN_NUN, True, False, $campos);
  
?>