<?php
# Librerias
require '../../lib/general.inc.php';


# Consulta para el listado 
$month_current = date('m');
$last_month = strtotime('-0 month', strtotime(date('d-m-Y')));
$last_month = date('m',$last_month);
$year_current = date('Y');
$fe_periodo=date('m-Y');

# Query for the listing of teachers timesheets
$Query = "SELECT fl_usuario, nombres, fe_clase '".ObtenEtiqueta(7)."', mn_total '".ObtenEtiqueta(731)."', fe_pagado '".ObtenEtiqueta(729)."' FROM (";
# Query for anterior month
$Query .= "(SELECT fl_usuario, concat(a.ds_nombres, ' ', a.ds_apaterno, ' ', ifnull(a.ds_amaterno, '')) nombres, date_format(fe_clase,'%M, %Y') fe_clase, CASE WHEN isnull(d.mn_total) THEN '".ObtenEtiqueta(712)."' ELSE concat('$',' ',format(d.mn_total,2)) END mn_total, CASE WHEN isnull(d.fg_pagado) THEN '".ObtenEtiqueta(712)."' WHEN d.fg_pagado='0' THEN '".ObtenEtiqueta(654)."' WHEN d.fg_pagado='1' THEN '".ObtenEtiqueta(655)."' END fe_pagado FROM c_usuario a LEFT JOIN k_maestro_pago d ON(  d.fl_maestro=a.fl_usuario AND MONTH(fe_periodo)='$last_month' AND YEAR(fe_periodo)='$year_current_ant' ), c_grupo b, k_clase c WHERE a.fl_usuario=b.fl_maestro AND b.fl_grupo=c.fl_grupo AND fl_perfil='2' AND a.fg_activo='1' AND MONTH(fe_clase)='$last_month' AND year(fe_clase)='$year_current_ant')";
# Query to add the actual month
$Query .= "UNION(SELECT fl_usuario, concat(a.ds_nombres, ' ', a.ds_apaterno, ' ', ifnull(a.ds_amaterno, '')) nombres, date_format(fe_clase,'%M, %Y') fe_clase, CASE WHEN isnull(d.mn_total) THEN '".ObtenEtiqueta(712)."' ELSE concat('$',' ',format(d.mn_total,2)) END mn_total, CASE WHEN isnull(d.fg_pagado) THEN '".ObtenEtiqueta(712)."' WHEN d.fg_pagado='0' THEN '".ObtenEtiqueta(654)."' WHEN d.fg_pagado='1' THEN '".ObtenEtiqueta(655)."' END fe_pagado FROM c_usuario a LEFT JOIN k_maestro_pago d ON( d.fl_maestro=a.fl_usuario AND MONTH(fe_periodo)='$month_current' AND YEAR(fe_periodo)='$year_current' ), c_grupo b, k_clase_cg c WHERE b.fl_maestro=c.fl_maestro AND fl_perfil='2' AND a.fg_activo='1' AND MONTH(fe_clase)='$month_current' AND year(fe_clase)='$year_current')";
# Query to add the global clases
$Query .= "UNION(SELECT fl_usuario, concat(a.ds_nombres, ' ', a.ds_apaterno, ' ', ifnull(a.ds_amaterno, '')) nombres, date_format(fe_clase,'%M, %Y') fe_clase, CASE WHEN isnull(d.mn_total) THEN '".ObtenEtiqueta(712)."' ELSE concat('$',' ',format(d.mn_total,2)) END mn_total, CASE WHEN isnull(d.fg_pagado) THEN '".ObtenEtiqueta(712)."' WHEN d.fg_pagado='0' THEN '".ObtenEtiqueta(654)."' WHEN d.fg_pagado='1' THEN '".ObtenEtiqueta(655)."' END fe_pagado FROM c_usuario a LEFT JOIN k_maestro_pago d ON(  d.fl_maestro=a.fl_usuario AND MONTH(fe_periodo)='$month_current' AND YEAR(fe_periodo)='$year_current' ), c_grupo b, k_clase_cg c WHERE b.fl_maestro=c.fl_maestro AND fl_perfil='2' AND a.fg_activo='1' AND MONTH(fe_clase)='$month_current' AND year(fe_clase)='$year_current')";
# Query to add the group clases
$Query .= "UNION (SELECT fl_usuario, concat(a.ds_nombres, ' ', a.ds_apaterno, ' ', ifnull(a.ds_amaterno, '')) nombres, date_format(fe_clase,'%M, %Y') fe_clase, CASE WHEN isnull(d.mn_total) THEN '".ObtenEtiqueta(712)."' ELSE concat('$',' ',format(d.mn_total,2)) END mn_total, CASE WHEN isnull(d.fg_pagado) THEN '".ObtenEtiqueta(712)."' WHEN d.fg_pagado='0' THEN '".ObtenEtiqueta(654)."' WHEN d.fg_pagado='1' THEN '".ObtenEtiqueta(655)."' END fe_pagado FROM c_usuario a LEFT JOIN k_maestro_pago d ON( d.fl_maestro=a.fl_usuario AND MONTH(fe_periodo)='$month_current' AND YEAR(fe_periodo)='$year_current' ), c_grupo b, k_clase_grupo c WHERE b.fl_grupo=c.fl_grupo AND fl_perfil='2' AND a.fg_activo='1' AND MONTH(fe_clase)='$month_current' AND year(fe_clase)='$year_current' )";
# Finish the Query
$Query .= ") AS teachers GROUP BY nombres ORDER BY nombres";

$rs = EjecutaQuery($Query);
$registros = CuentaRegistros($rs);
for($i=1;$row=RecuperaRegistro($rs);$i++) {
    
    $fl_maestro=str_texto($row['fl_usuario']);



  $Query2  = "SELECT no_semana, ds_titulo,".ConsultaFechaBD('d.fe_clase', FMT_FECHA)." fe_clase, CASE d.fg_adicional WHEN '0' THEN '".ObtenEtiqueta(714)."' ELSE '".ObtenEtiqueta(715)."' END ds_descripion, ";
  $Query2 .= "a.nb_grupo, e.nb_programa,(SELECT nb_periodo FROM c_periodo j WHERE j.fl_periodo=f.fl_periodo) nb_periodo, ";
  $Query2 .= "CASE d.fg_adicional WHEN '0' THEN IFNULL((SELECT t.mn_lecture_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_maestro),e.mn_lecture_fee) ";
  $Query2 .= "ELSE IFNULL((SELECT t.mn_extra_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_maestro),e.mn_extra_fee) END hourly_rate ";
  $Query2 .= ",a.fl_grupo,e.fl_programa, CASE a.no_alumnos WHEN 0 
    THEN (SELECT COUNT(1) FROM k_alumno_historia f, c_usuario e WHERE f.fl_grupo=a.fl_grupo AND f.fl_alumno=e.fl_usuario AND e.fg_activo='1')
    ELSE a.no_alumnos END no_alumnos, ";
 
  $Query2 .= "d.fl_clase  ";
  $Query2 .= "FROM c_grupo a, k_clase d, c_programa e, k_term f ,k_semana b LEFT JOIN c_leccion c ON(c.fl_leccion=b.fl_leccion) ";
  $Query2 .= "WHERE a.fl_term = b.fl_term AND a.fl_grupo=d.fl_grupo AND b.fl_semana=d.fl_semana AND c.fl_programa = e.fl_programa ";
  $Query2 .= "AND c.fl_programa=e.fl_programa AND a.fl_term = f.fl_term AND b.fl_term = f.fl_term AND DATE_FORMAT(d.fe_clase,'%m-%Y')='".$fe_periodo."' ";
  $Query2 .= "AND a.fl_maestro=$fl_maestro ";

  # El grupo debe tener estudiantes 
  
  $Query2 .= "ORDER BY d.fe_clase ";
  $rs2 = EjecutaQuery($Query2);

  $tot_aut_nor = CuentaRegistros($rs2);

  for($m=0;$row2=RecuperaRegistro($rs2);$m++){


      $hourly_rate = $row2['hourly_rate'];
      $fl_grupo = $row2['fl_grupo']; // $row[8]
      $fl_programa = $row2['fl_programa'];
      $fl_clase = $row2['fl_clase'];

      $Queryt="SELECT mn_hour_rate FROM c_maestro WHERE fl_maestro=$fl_maestro ";
      $rot=RecuperaValor($Queryt);
      $mn_tarifa_default=$rot['mn_hour_rate'];

      if(!empty($mn_tarifa_default)){
          $amount=$mn_tarifa_default;
          $hourly_rate=$amount;
          EjecutaQuery("UPDATE k_maestro_tarifa SET mn_lecture_fee=$amount WHERE fl_grupo=$fl_grupo AND fl_maestro=$fl_maestro  ");

          $Queryu="UPDATE k_maestro_pago_det SET mn_tarifa_hr=$amount, mn_subtotal=$amount  WHERE fg_tipo='A' AND fl_grupo=".$fl_grupo." AND ds_concepto='".$fl_clase."'  ";
          EjecutaQuery($Queryu);
      }




  }










}

?>