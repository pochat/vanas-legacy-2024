<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_grupo,nb_programa '".ObtenEtiqueta(380)."', nb_periodo '".ObtenEtiqueta(381)."', ";
  $Query .= "no_grado '".ObtenEtiqueta(375)."|right', nb_grupo '".ObtenEtiqueta(420)."', ";
  $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
  $Query .= ConcatenaBD($concat)." '".ObtenEtiqueta(421)."', ";
  $Query .= "(SELECT COUNT(1) FROM k_alumno_grupo f WHERE a.fl_grupo=f.fl_grupo) '".ObtenEtiqueta(423)."|right' ";
  $Query .= "FROM c_grupo a, k_term b, c_programa c, c_periodo d, c_usuario e ";
  $Query .= "WHERE a.fl_term=b.fl_term ";
  $Query .= "AND b.fl_programa=c.fl_programa ";
  $Query .= "AND b.fl_periodo=d.fl_periodo ";
  $Query .= "AND a.fl_maestro=e.fl_usuario  AND c.fg_archive='1' ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND nb_programa LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND nb_periodo LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND no_grado LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND nb_grupo LIKE '%$criterio%' "; break;
      case 5: $Query .= "AND (ds_nombres LIKE '%$criterio%' OR ds_apaterno LIKE '%$criterio%' OR ds_amaterno LIKE '%$criterio%') "; break;
      default:
        $Query .= "AND (nb_programa LIKE '%$criterio%' ";
        $Query .= "OR nb_periodo LIKE '%$criterio%' ";
        $Query .= "OR no_grado LIKE '%$criterio%' ";
        $Query .= "OR nb_grupo LIKE '%$criterio%' ";
        $Query .= "OR ds_nombres LIKE '%$criterio%' OR ds_apaterno LIKE '%$criterio%' OR ds_amaterno LIKE '%$criterio%') "; 
    }
  }
  $Query .= "ORDER BY no_orden, fe_inicio, no_grado, nb_grupo ";
  
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(380), ObtenEtiqueta(381), ObtenEtiqueta(375), ObtenEtiqueta(420), ObtenEtiqueta(421));
  PresentaPaginaListado(121, $Query, TB_LN_NUN, True, False, $campos);
  
?>