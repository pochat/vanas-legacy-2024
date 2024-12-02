<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $fl_usuario = ValidaSesion( );
 
  # Consulta para el listado
  $Query  = "SELECT fl_term, nb_programa '".ObtenEtiqueta(380)."',ds_duracion '".ObtenEtiqueta(361)."', nb_periodo '".ObtenEtiqueta(381)."', ";
  $Query .= "no_grado '".ObtenEtiqueta(375)."|right' ";
  $Query .= "FROM k_term a, c_programa b, c_periodo c ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_periodo=c.fl_periodo AND b.fg_archive='1' ";

  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND nb_programa LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_duracion LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND nb_periodo LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND no_grado LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND (nb_programa LIKE '%$criterio%' ";
        $Query .= "OR ds_duracion LIKE '%$criterio%' ";
        $Query .= "OR nb_periodo LIKE '%$criterio%' ";
        $Query .= "OR no_grado LIKE '%$criterio%') ";
    }
  }
    
  $Query .= "ORDER BY nb_programa, fe_inicio, no_grado";
 
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(380), ObtenEtiqueta(361), ObtenEtiqueta(381), ObtenEtiqueta(375));
  PresentaPaginaListado(FUNC_PERIODOS, $Query, TB_LN_NUN, True, False, $campos);
  
?>