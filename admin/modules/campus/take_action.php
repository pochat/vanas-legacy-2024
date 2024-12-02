<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Update a los students que no tienen calificacion y ya terminaron curso
  $Query_up  = "SELECT d.fl_alumno,b.ds_nombres, b.ds_apaterno, b.ds_amaterno FROM k_pctia a, c_usuario b ";
  $Query_up .= "LEFT JOIN c_alumno d ON (d.fl_alumno=b.fl_usuario AND ISNULL(d.no_promedio_t) ) WHERE b.fl_perfil=3 AND a.fl_alumno=b.fl_usuario AND a.fl_alumno=d.fl_alumno ";
  $Query_up .= "AND fe_completado < Curdate() AND (b.fg_activo='1' OR (b.fg_activo='0' AND a.fg_desercion='0' AND a.fg_dismissed='0' ";
  $Query_up .= "AND a.fg_job='0' AND a.fg_graduacion='0' AND a.fg_certificado='0' AND a.fg_honores ='0') ) ";
  $rs = EjecutaQuery($Query_up);
  for($j=0;$row= RecuperaRegistro($rs);$j++){
    $fl_alumno = $row[0];
    $row1 = RecuperaValor("SELECT SUM(no_equivalencia)/COUNT(*) FROM k_entrega_semanal a, c_calificacion b WHERE a.fl_promedio_semana=b.fl_calificacion 
    AND a.fl_alumno=$fl_alumno");
    $no_promedio_t = round($row1[0]);
    EjecutaQuery("UPDATE c_alumno SET no_promedio_t='$no_promedio_t' WHERE fl_alumno=$fl_alumno ");
  }
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
  $Query  = "SELECT fl_usuario, ds_login '".ETQ_USUARIO."', (".ConcatenaBD($concat).") '".ETQ_NOMBRE."', ";
  $Query .= "nb_programa '".ObtenEtiqueta(360)."', DATE_FORMAT(fe_alta, '%d-%m-%Y') '".ObtenEtiqueta(111)."', ";
  $Query .= "".ConsultaFechaBD('fe_completado', FMT_FECHA)." '".ObtenEtiqueta(545)."', ";
  $Query .= "CASE WHEN fg_activo='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(113)."', ";
  $Query .= "CASE WHEN fg_pago='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(341)."' ";
  $Query .= ",CASE WHEN g.no_promedio_t IS NULL THEN ' ' WHEN g.no_promedio_t='0' THEN 'NU' WHEN g.no_promedio_t>'0' THEN h.cl_calificacion END '".ObtenEtiqueta(431)."'  ";
  $Query .= "FROM c_usuario a, c_perfil b, c_sesion c, k_ses_app_frm_1 d, c_programa e, k_pctia f, c_alumno g ";
  $Query .= "LEFT JOIN c_calificacion h ON(g.no_promedio_t>0 AND g.no_promedio_t BETWEEN no_min AND  no_max ) ";
  $Query .= "WHERE a.fl_perfil=b.fl_perfil AND a.cl_sesion=c.cl_sesion AND c.cl_sesion=d.cl_sesion AND d.fl_programa=e.fl_programa ";
  $Query .= "AND a.fl_usuario = f.fl_alumno AND a.fl_perfil=3 AND a.fl_usuario=g.fl_alumno AND f.fe_completado < CURDATE() ";
  $Query .= "AND (a.fg_activo='1' OR (a.fg_activo='0' AND f.fg_desercion='0' AND f.fg_dismissed='0' AND f.fg_job='0' AND f.fg_graduacion='0' ";
  $Query .= "AND f.fg_certificado='0' AND f.fg_honores ='0'))  ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND ds_login LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND (ds_nombres LIKE '%$criterio%' OR ds_apaterno LIKE '%$criterio%' OR ds_amaterno LIKE '%$criterio%') "; break;
      case 3: $Query .= "AND nb_programa LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND ".ConsultaFechaBD('fe_completado', FMT_FECHA)." LIKE '%$criterio%' "; break;
      case 5: $Query .= "AND cl_calificacion LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND ( ";
        $Query .= "ds_login LIKE '%$criterio%' ";
        $Query .= "OR ds_nombres LIKE '%$criterio%' OR ds_apaterno LIKE '%$criterio%' OR ds_amaterno LIKE '%$criterio%' ";
        $Query .= "OR nb_programa LIKE '%$criterio%' ";
        $Query .= "OR ".ConsultaFechaBD('fe_completado', FMT_FECHA)." LIKE '%$criterio%' ";
        $Query .= "OR cl_calificacion LIKE '%$criterio%') ";
    }
  }
  
  $Query .= "ORDER BY fe_completado, ds_login ";
  
  # Muestra pagina de listado
  $campos = array(ETQ_USUARIO,ETQ_NOMBRE,ObtenEtiqueta(360),  ObtenEtiqueta(545), ObtenEtiqueta(431));
  PresentaPaginaListado(FUNC_TAKE, $Query, TB_LN_NUN, True, True, $campos);
  
 
?>