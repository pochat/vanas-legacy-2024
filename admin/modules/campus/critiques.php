<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query = "SELECT fl_entrega_semanal, nb_programa '".ObtenEtiqueta(380)."', d.no_grado '".ObtenEtiqueta(375)."|right', 
            no_semana '".ObtenEtiqueta(390)."|right', nb_grupo '".ObtenEtiqueta(420)."|left',
            CONCAT(ds_nombres, ' ', ds_apaterno) '".ObtenEtiqueta(421)."|left', 
            CASE WHEN fe_calificacion < DATE(NOW()) AND fl_promedio_semana IS NULL THEN 'OVERDUE' ELSE fe_calificacion END '".ObtenEtiqueta(384)."|left', 
            ds_critica_animacion '".ObtenEtiqueta(457)."|left'
            FROM k_entrega_semanal a, c_usuario b, c_grupo c, k_term d, c_programa e, k_semana f, c_leccion g
            WHERE c.fl_maestro = b.fl_usuario 
            AND a.fl_grupo = c.fl_grupo
            AND c.fl_term = d.fl_term
            AND d.fl_programa = e.fl_programa
            AND a.fl_semana = f.fl_semana
            AND f.fl_leccion = g.fl_leccion ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND nb_programa LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND d.no_grado LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND no_semana LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND nb_grupo LIKE '%$criterio%' "; break;
      case 5: $Query .= "AND (ds_nombres LIKE '%$criterio%' OR ds_apaterno LIKE '%$criterio%') "; break;
      case 6: $Query .= "AND ds_critica_animacion LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND (nb_programa LIKE '%$criterio%' ";
        $Query .= "OR d.no_grado LIKE '%$criterio%' ";
        $Query .= "OR no_semana LIKE '%$criterio%' ";
        $Query .= "OR nb_grupo LIKE '%$criterio%' ";
        $Query .= "OR ds_nombres LIKE '%$criterio%' OR ds_apaterno LIKE '%$criterio%' ";
        $Query .= "OR ds_critica_animacion LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY nb_programa, d.no_grado, no_semana DESC, nb_grupo";
  
  # Muestra pagina de listado 
  $campos = array(ObtenEtiqueta(380), ObtenEtiqueta(375), ObtenEtiqueta(390), ObtenEtiqueta(420), ObtenEtiqueta(421), ObtenEtiqueta(457));
  PresentaPaginaListado(FUNC_CRITICAS, $Query, TB_LN_NUN, True, False, $campos);
  
?>