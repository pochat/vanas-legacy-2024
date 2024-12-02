<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Query para los datos
  $Query = Query_Principal($criterio, $actual);

  # Muestra pagina de listado
  $campos = array(ETQ_USUARIO, ETQ_NOMBRE, ObtenEtiqueta(360), ObtenEtiqueta(112), ObtenEtiqueta(820), ObtenEtiqueta(821));
  

	
  PresentaPaginaListado(130, $Query, TB_LN_NUN, True, False, $campos);

  function Query_Principal($p_criterio, $p_actual) {
    
    # Calificacion Minima aprovada
    $reprovada  = "SELECT no_min FROM c_calificacion ";
    $reprovada .= "WHERE no_equivalencia=(SELECT MIN(no_equivalencia) FROM c_calificacion WHERE fg_aprobado='1') ";
    $row = RecuperaValor($reprovada);
    $calificacion_min = round($row[0]);
    
    # Consulta para el listado
    $Query  = "SELECT fl_usuario, ds_login '".ETQ_USUARIO."', ";
    $concat = array('ds_nombres', "' '", NulosBD('ds_amaterno'), "' '",'ds_apaterno');
    $Query .= ConcatenaBD($concat)." '".ETQ_NOMBRE."', ";
    $Query .= "CONCAT(nb_programa,' (',ds_duracion,')') '".ObtenEtiqueta(360)."', ";
    $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
    $Query .= "(".ConcatenaBD($concat).") '".ObtenEtiqueta(112)."', ";
    $Query .= "(SELECT CONCAT(ROUND(no_promedio),'%') FROM k_alumno_term s WHERE s.fl_alumno=a.fl_usuario ";
    $Query .= "AND fl_term=(SELECT MAX(fl_term) FROM k_alumno_term t WHERE t.fl_alumno=a.fl_usuario)) '".ObtenEtiqueta(820)."', ";
    $Query .= "(SELECT CONCAT(ROUND(no_promedio_t),'%') FROM c_alumno g WHERE g.fl_alumno=a.fl_usuario) '".ObtenEtiqueta(821)."' ";
    $Query .= "FROM c_usuario a, c_perfil b, c_sesion c, k_ses_app_frm_1 d, c_programa e ";
    $Query .= "WHERE a.fl_perfil=b.fl_perfil ";
    $Query .= "AND a.cl_sesion=c.cl_sesion ";
    $Query .= "AND c.cl_sesion=d.cl_sesion ";
    $Query .= "AND d.fl_programa=e.fl_programa ";
    $Query .= "AND a.fl_perfil=".PFL_ESTUDIANTE." AND fg_activo='1' ";
    $Query .= "AND (SELECT no_promedio_t FROM c_alumno g WHERE g.fl_alumno=a.fl_usuario)<".$calificacion_min." ";
    $Query .= "AND (SELECT no_promedio FROM k_alumno_term s WHERE s.fl_alumno=a.fl_usuario ";
    $Query .= "AND fl_term=(SELECT MAX(fl_term) FROM k_alumno_term t WHERE t.fl_alumno=a.fl_usuario))<".$calificacion_min." ";
    if(!empty($p_criterio)) {
      switch($p_actual) {
        case 1: $Query .= "AND ds_login LIKE '%$p_criterio%' "; break;
        case 2: $Query .= "AND (ds_nombres LIKE '%$p_criterio%' OR ds_apaterno LIKE '%$p_criterio%' OR ds_amaterno LIKE '%$p_criterio%') "; break;
        case 3: $Query .= "AND nb_programa LIKE '%$p_criterio%' "; break;
        case 4: 
          $Query .= "AND (".ConsultaFechaBD('fe_ultacc', FMT_FECHA)." LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_HORA)." LIKE '%$p_criterio%') ";
          break;
        case 5:
          $Query .= "AND (SELECT no_promedio FROM k_alumno_term s WHERE s.fl_alumno=a.fl_usuario ";
          $Query .= "AND fl_term=(SELECT MAX(fl_term) FROM k_alumno_term t WHERE t.fl_alumno=a.fl_usuario)) LIKE '$p_criterio%' ";
          break;
        case 6:
          $Query .= " AND (SELECT CONCAT(no_promedio_t,'','%') FROM c_alumno g WHERE g.fl_alumno=a.fl_usuario) LIKE '%$p_criterio%' ";
          break;
        default:
          $Query .= "AND ( ";
          $Query .= "ds_login LIKE '%$p_criterio%' ";
          $Query .= "OR ds_nombres LIKE '%$p_criterio%' OR ds_apaterno LIKE '%$p_criterio%' OR ds_amaterno LIKE '%$p_criterio%' ";
          $Query .= "OR nb_programa LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_FECHA)." LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_HORA)." LIKE '%$p_criterio%' ";
          $Query .= "OR (SELECT no_promedio FROM k_alumno_term s WHERE s.fl_alumno=a.fl_usuario
          AND fl_term=(SELECT MAX(fl_term) FROM k_alumno_term t WHERE t.fl_alumno=a.fl_usuario)) LIKE '%$p_criterio%' ";
          $Query .= "OR (SELECT CONCAT(no_promedio_t,'','%') FROM c_alumno g WHERE g.fl_alumno=a.fl_usuario) LIKE '%$p_criterio%')";
      }
    }
    $Query .= "ORDER BY ds_nombres, ds_login ";
    return $Query;
  }
  
?>