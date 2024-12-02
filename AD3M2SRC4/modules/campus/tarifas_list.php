<?php
  # Librerias
  require '../../lib/general.inc.php';

  # Recibe Parametros
  parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
  $_POST += isset($advanced_search) ? $advanced_search : "";
  $fg_label = isset($_POST['fg_label']) ? $_POST['fg_label'] : "";

 #Recupermaos todos los labels.

  # Consulta para el listado
  $month_current = date('m');
  $last_month = strtotime('-1 month', strtotime(date('d-m-Y')));
  $last_month = date('m',$last_month);
  $year_current = date('Y');
  # Aprincipio de anio
  if($last_month==12 AND $month_current==1){
      $year_current_ant = $year_current -1;
  }
  else{
      $year_current_ant = date('Y');
  }

  #Recibe parametros de los select
  $fl_param = isset($_POST['fl_param'])?$_POST['fl_param']:NULL;

  $fe_ini = isset($_POST['fl_instituto_params'])?$_POST['fl_instituto_params']:NULL;

  if(isset($fe_ini)){

  #Desconponemos la fecha.
    $fe=explode("-",$fe_ini);
    $anio=$fe[0];
    $mes=$fe[1];
    $last_month=$mes;
    $year_current_ant=$anio;
    $month_current=$mes;

  }else{

	   $year_current_actu = date('Y-m');
     // $year_current_actu=
     $actual = strtotime($year_current_actu);
     $mesmenos = date("Y-m", strtotime("-0 month", $actual));

	  #Desconponemos la fecha.
	  $fe=explode("-",$mesmenos);
	  $anio=$fe[0];
	  $mes=$fe[1];

      $last_month=$mes;
      $year_current_ant=$anio;
      $month_current=$mes;
  }

  $Queryd="SELECT DISTINCT DATE_FORMAT(fe_periodo,'%M, %Y'), DATE_FORMAT(fe_periodo,'%Y-%m')  FROM k_maestro_pago
                            WHERE  DATE_FORMAT(fe_periodo,'%Y-%m') ='".$year_current."-".$month_current."' ";
  $rowd=RecuperaValor($Queryd);
  if(empty($rowd[0])){

      $mesmenos = date("Y-m", strtotime("-1 month", $actual));
      #Desconponemos la fecha.
	  $fe=explode("-",$mesmenos);
	  $anio=$fe[0];
	  $mes=$fe[1];
      $last_month=$mes;
      $year_current=$anio;
      $year_current_ant=$anio;
      $month_current=$mes;
  }


  # Query for the listing of teachers timesheets
  $Query = "SELECT fl_usuario, nombres, fe_clase '".ObtenEtiqueta(7)."', mn_total '".ObtenEtiqueta(731)."', fe_pagado '".ObtenEtiqueta(729)."',fl_maestro FROM (";
  # Query for anterior month
  $Query .= "(SELECT concat('\'',fl_usuario,'a','$last_month','a',date_format(fe_clase,'%Y'),'\'') fl_usuario, concat(a.ds_nombres, ' ', a.ds_apaterno, ' ', ifnull(a.ds_amaterno, '')) nombres, date_format(fe_clase,'%M, %Y') fe_clase, CASE WHEN isnull(d.mn_total) THEN '".ObtenEtiqueta(712)."' ELSE concat('$',' ',format(d.mn_total,2)) END mn_total, CASE WHEN isnull(d.fg_pagado) THEN '".ObtenEtiqueta(712)."' WHEN d.fg_pagado='0' THEN '".ObtenEtiqueta(654)."' WHEN d.fg_pagado='1' THEN '".ObtenEtiqueta(655)."' END fe_pagado,fl_usuario fl_maestro FROM c_usuario a LEFT JOIN k_maestro_pago d ON(  d.fl_maestro=a.fl_usuario AND MONTH(fe_periodo)='$last_month' AND YEAR(fe_periodo)='$year_current_ant' ), c_grupo b, k_clase c WHERE a.fl_usuario=b.fl_maestro AND b.fl_grupo=c.fl_grupo AND fl_perfil='2' AND a.fg_activo='1' AND MONTH(fe_clase)='$last_month' AND year(fe_clase)='$year_current_ant')";
  # Query to add the actual month
  $Query .= "UNION(SELECT concat('\'',fl_usuario,'a','$month_current','a',date_format(fe_clase,'%Y'),'\'') fl_usuario, concat(a.ds_nombres, ' ', a.ds_apaterno, ' ', ifnull(a.ds_amaterno, '')) nombres, date_format(fe_clase,'%M, %Y') fe_clase, CASE WHEN isnull(d.mn_total) THEN '".ObtenEtiqueta(712)."' ELSE concat('$',' ',format(d.mn_total,2)) END mn_total, CASE WHEN isnull(d.fg_pagado) THEN '".ObtenEtiqueta(712)."' WHEN d.fg_pagado='0' THEN '".ObtenEtiqueta(654)."' WHEN d.fg_pagado='1' THEN '".ObtenEtiqueta(655)."' END fe_pagado,fl_usuario fl_maestro FROM c_usuario a LEFT JOIN k_maestro_pago d ON( d.fl_maestro=a.fl_usuario AND MONTH(fe_periodo)='$month_current' AND YEAR(fe_periodo)='$year_current' ), c_grupo b, k_clase_cg c WHERE b.fl_maestro=c.fl_maestro AND fl_perfil='2' AND a.fg_activo='1' AND MONTH(fe_clase)='$month_current' AND year(fe_clase)='$year_current')";
  # Query to add the global clases
  $Query .= "UNION(SELECT concat('\'',fl_usuario,'a','$month_current','a',date_format(fe_clase,'%Y'),'\'') fl_usuario, concat(a.ds_nombres, ' ', a.ds_apaterno, ' ', ifnull(a.ds_amaterno, '')) nombres, date_format(fe_clase,'%M, %Y') fe_clase, CASE WHEN isnull(d.mn_total) THEN '".ObtenEtiqueta(712)."' ELSE concat('$',' ',format(d.mn_total,2)) END mn_total, CASE WHEN isnull(d.fg_pagado) THEN '".ObtenEtiqueta(712)."' WHEN d.fg_pagado='0' THEN '".ObtenEtiqueta(654)."' WHEN d.fg_pagado='1' THEN '".ObtenEtiqueta(655)."' END fe_pagado,fl_usuario fl_maestro FROM c_usuario a LEFT JOIN k_maestro_pago d ON(  d.fl_maestro=a.fl_usuario AND MONTH(fe_periodo)='$month_current' AND YEAR(fe_periodo)='$year_current' ), c_grupo b, k_clase_cg c WHERE b.fl_maestro=c.fl_maestro AND fl_perfil='2' AND a.fg_activo='1' AND MONTH(fe_clase)='$month_current' AND year(fe_clase)='$year_current')";
  # Query to add the group clases
  $Query .= "UNION (SELECT concat('\'',fl_usuario,'a','$month_current','a',date_format(fe_clase,'%Y'),'\'') fl_usuario, concat(a.ds_nombres, ' ', a.ds_apaterno, ' ', ifnull(a.ds_amaterno, '')) nombres, date_format(fe_clase,'%M, %Y') fe_clase, CASE WHEN isnull(d.mn_total) THEN '".ObtenEtiqueta(712)."' ELSE concat('$',' ',format(d.mn_total,2)) END mn_total, CASE WHEN isnull(d.fg_pagado) THEN '".ObtenEtiqueta(712)."' WHEN d.fg_pagado='0' THEN '".ObtenEtiqueta(654)."' WHEN d.fg_pagado='1' THEN '".ObtenEtiqueta(655)."' END fe_pagado,fl_usuario fl_maestro FROM c_usuario a LEFT JOIN k_maestro_pago d ON( d.fl_maestro=a.fl_usuario AND MONTH(fe_periodo)='$month_current' AND YEAR(fe_periodo)='$year_current' ), c_grupo b, k_clase_grupo c WHERE b.fl_grupo=c.fl_grupo AND fl_perfil='2' AND a.fg_activo='1' AND MONTH(fe_clase)='$month_current' AND year(fe_clase)='$year_current' )";
  # Finish the Query
  $Query .= ") AS teachers GROUP BY nombres ORDER BY nombres";

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
		
      $fl_usuario=str_texto($row['fl_usuario']);
      $nb_nombre = str_texto($row[1]);
      $fe_clase = str_texto($row[2]);          
      $mn_total = $row[3];
      $fg_pagado = $row[4];
      $fl_maestro=$row['fl_maestro'];

      # Commented because (fl_maestro_pago) does not exixst as a row in the Query
      //$fl_maestro_pago=$row['fl_maestro_pago'];

    /*  $Query="SELECT SUM(a.mn_tarifa_hr),b.fl_maestro_pago  FROM k_maestro_pago_det a
            JOIN k_maestro_pago b ON b.fl_maestro_pago=a.fl_maestro_pago
             WHERE fl_maestro=".$row['fl_usuario']." AND a.fg_tipo='ACG' AND DATE_FORMAT(fe_periodo,'%M, %Y')='$fe_clase' ";
      $ro=RecuperaValor($Query);
      if((!empty($ro[0]))&&($fl_maestro_pago<>$ro['fl_maestro_pago'])){
          
          $mn_total=$mn_total+$ro[0];
          $mn_total_consulta=$mn_total+$ro[0];
      }
      */

      #Total Lecture
      $Queryt="
        SELECT COUNT(*)  
		FROM c_grupo aa, k_clase dd, c_programa ee, k_term ff ,k_semana bb 
		LEFT JOIN c_leccion cc ON(cc.fl_leccion=bb.fl_leccion) 
		WHERE aa.fl_term = bb.fl_term 
		AND aa.fl_grupo=dd.fl_grupo 
		AND bb.fl_semana=dd.fl_semana 
		AND cc.fl_programa = ee.fl_programa 
		AND cc.fl_programa=ee.fl_programa 
		AND aa.fl_term = ff.fl_term 
		AND bb.fl_term = ff.fl_term 
		AND DATE_FORMAT(dd.fe_clase,'%m-%Y')='$month_current-$year_current' 
		AND aa.fl_maestro=$fl_maestro 
		ORDER BY dd.fe_clase 
        ";
      $row2=RecuperaValor($Queryt);
      $tot_lecture=$row2['0'];




      #Total Review
      $Querygg  = "SELECT COUNT(*) FROM k_clase_grupo a JOIN k_semana_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo JOIN c_grupo c ON c.fl_grupo=a.fl_grupo WHERE a.fl_maestro=$fl_maestro AND DATE_FORMAT(a.fe_clase,'%m-%Y')='".$month_current."-".$year_current."' ";
      $row3=RecuperaValor($Querygg);
      $tot_review=$row3['0'];



      #Total Global Class
      $Querycg  = "SELECT COUNT(*) ";
      $Querycg .= "FROM c_clase_global cg ";
      $Querycg .= "LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=cg.fl_clase_global AND kcg.fl_maestro=$fl_maestro) ";
      $Querycg .= "WHERE DATE_FORMAT(kcg.fe_clase,'%m-%Y')='".$month_current."-".$year_current."' ";
      $row4=RecuperaValor($Querycg);
      $tot_global=$row4['0'];


    echo '
    {      
      "nombre": "<div><a href=\'javascript:EnviaFame(\"tarifas_frm.php\",'.$fl_usuario.');\'>'.$nb_nombre.' </a></div>",      
      "fe_clase": "<td><a href=\'javascript:EnviaFame(\"tarifas_frm.php\",'.$fl_usuario.');\'>'.$fe_clase.'</a></td>",
      "total_lecture": "<td><a href=\'javascript:EnviaFame(\"tarifas_frm.php\",'.$fl_usuario.');\'> '.$tot_lecture.'</a></td>",
      "total_review": "<td><a href=\'javascript:EnviaFame(\"tarifas_frm.php\",'.$fl_usuario.');\'> '.$tot_review.'</a></td>",
      "total_global": "<td><a href=\'javascript:EnviaFame(\"tarifas_frm.php\",'.$fl_usuario.');\'> '.$tot_global.'</a></td>",
      "mn_total": "<td><a href=\'javascript:EnviaFame(\"tarifas_frm.php\",'.$fl_usuario.');\'> '.$mn_total.'</a></td>",
      "fg_pagado": "<td><a href=\'javascript:EnviaFame(\"tarifas_frm.php\",'.$fl_usuario.');\'>'.$fg_pagado.'</a></td>"
     
    }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
