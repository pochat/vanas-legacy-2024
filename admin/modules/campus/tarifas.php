<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  # El listado mostrara los dos ultimos meses  
  # Consulta para el listado 
  $month_current = date('m');
  $last_month = strtotime('-1 month', strtotime(date('d-m-Y')));
  $last_month = date('m',$last_month);
  $year_current = date('Y');
  
  #Query para el listado 
  $Query  = "SELECT fl_usuario,nombres '".ETQ_NOMBRE."', fe_clase'".ObtenEtiqueta(7)."', ";
  $Query .= "mn_total '".ObtenEtiqueta(731)."', fe_pagado '".ObtenEtiqueta(729)."' FROM  (";
  #query para el mes anterior 
  $Query .= "(SELECT CONCAT('\'',fl_usuario,'a','$last_month\'') fl_usuario, CONCAT(a.ds_nombres, ' ', a.ds_apaterno, ' ', IFNULL(a.ds_amaterno, '')) nombres, DATE_FORMAT(fe_clase,'%M, %Y') fe_clase, ";
  $Query .= "CASE WHEN ISNULL(d.mn_total) THEN '".ObtenEtiqueta(712)."' ELSE CONCAT('$',' ',FORMAT(d.mn_total,2)) END mn_total, CASE WHEN ISNULL(d.fg_pagado) THEN '".ObtenEtiqueta(712)."' ";
  $Query .= "WHEN d.fg_pagado='0' THEN '".ObtenEtiqueta(654)."' WHEN d.fg_pagado='1' THEN '".ObtenEtiqueta(655)."'  END fe_pagado FROM c_usuario a ";
  $Query .= "LEFT JOIN k_maestro_pago d ON(d.fl_maestro=a.fl_usuario AND MONTH(fe_periodo)='$last_month' AND YEAR(fe_periodo)='$year_current' ), c_grupo b, k_clase c ";
  $Query .= "WHERE a.fl_usuario=b.fl_maestro AND b.fl_grupo=c.fl_grupo AND fl_perfil='2' AND a.fg_activo='1' ";
  $Query .= "AND MONTH(fe_clase)='$last_month' AND YEAR(fe_clase)='$year_current' ";
  $Query .= "AND (SELECT COUNT(*) FROM k_alumno_grupo l WHERE l.fl_grupo=b.fl_grupo AND a.fg_activo='1')>0 )";
  #Query para el mes actual
  $Query .= "UNION (SELECT CONCAT('\'',fl_usuario,'a','$month_current\'') fl_usuario, CONCAT(a.ds_nombres, ' ', a.ds_apaterno, ' ', IFNULL(a.ds_amaterno, '')) nombres, DATE_FORMAT(fe_clase,'%M, %Y') fe_clase, ";
  $Query .= "CASE WHEN ISNULL(d.mn_total) THEN '".ObtenEtiqueta(712)."' ELSE CONCAT('$',' ',FORMAT(d.mn_total,2)) END mn_total, CASE WHEN ISNULL(d.fg_pagado) THEN '".ObtenEtiqueta(712)."' ";
  $Query .= "WHEN d.fg_pagado='0' THEN '".ObtenEtiqueta(654)."' WHEN d.fg_pagado='1' THEN '".ObtenEtiqueta(655)."'  END fe_pagado FROM c_usuario a ";
  $Query .= "LEFT JOIN k_maestro_pago d ON(d.fl_maestro=a.fl_usuario AND MONTH(fe_periodo)='$month_current' AND YEAR(fe_periodo)='$year_current' ), c_grupo b, k_clase c ";
  $Query .= "WHERE a.fl_usuario=b.fl_maestro AND b.fl_grupo=c.fl_grupo AND fl_perfil='2' AND a.fg_activo='1' ";
  $Query .= "AND MONTH(fe_clase)='$month_current' AND YEAR(fe_clase)='$year_current' ";
  $Query .= "AND (SELECT COUNT(*) FROM k_alumno_grupo l WHERE l.fl_grupo=b.fl_grupo AND a.fg_activo='1')>0 )";
  $Query .= ") as teachers  WHERE 1=1 "; 
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND nombres LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND mn_total LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND fe_pagado LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND ( nombres LIKE '%$criterio%'  OR fe_clase LIKE '%$criterio%' ";
        $Query .= "OR mn_total LIKE '%$criterio%'  ";
        $Query .= "OR fe_pagado LIKE '%$criterio%' ) "; 
    }
  }
  $Query .="ORDER BY nombres, fe_clase ";
  
  # Muestra pagina de listado
  $campos = array(ETQ_NOMBRE,ObtenEtiqueta(731),ObtenEtiqueta(729) );
  PresentaPaginaListado(FUNC_TEACHER_RATE, $Query, TB_LN_NUN, True, False, $campos);
  
?>