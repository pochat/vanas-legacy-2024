<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  $date = DateTime::createFromFormat("Ymd", date("Ymd"));
  $interval = new DateInterval("P6M"); // 6 months
  $fourMonthsEarlier = $date->sub($interval);
  $fecha_limite = $fourMonthsEarlier->format("Y-m-d");
  
  # Consulta para el listado
  $Query = "SELECT a.fl_clase_global, a.ds_clase, (SELECT DATE_FORMAT(fe_clase, '%Y-%m-%d') FROM k_clase_cg b WHERE b.fl_clase_global=a.fl_clase_global AND b.no_orden='1' ) AS fecha, ";
  $Query .= "(SELECT DATE_FORMAT(fe_clase, '%H:%i') FROM k_clase_cg b WHERE b.fl_clase_global=a.fl_clase_global AND b.no_orden='1' ) AS hora, no_alumnos,  ";
  $Query .= "(SELECT fg_mandatory FROM k_clase_cg b WHERE b.fl_clase_global=a.fl_clase_global AND b.no_orden='1' ), ";
  $Query .= "(SELECT CASE a.fg_mandatory WHEN 1 THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE  '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_mandatory FROM k_clase_cg b WHERE b.fl_clase_global=a.fl_clase_global AND b.no_orden='1' ) ";
  $Query .= "FROM c_clase_global a WHERE (SELECT DATE_FORMAT(fe_clase, '%Y-%m-%d') FROM k_clase_cg b WHERE b.fl_clase_global=a.fl_clase_global AND b.no_orden='1') IS NOT NULL ";
  $Query .=  "AND (SELECT DATE_FORMAT(fe_clase, '%Y-%m-%d') FROM k_clase_cg b WHERE b.fl_clase_global=a.fl_clase_global AND b.no_orden='1') > '".$fecha_limite."' ORDER BY fecha DESC";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++){
      # Color del mandatory
      if(isset($row[5]))
        $mandatory = "success";
      else
        $mandatory = "danger";
      # Obtenemos el foto del techaer commented because row[9] won't exists
      //$ruta_foto = PATH_MAE_IMAGES."/avatars/".str_texto($row[9]);
      $cuerpo = '
        {
          "checkbox": "<div class=\'checkbox\'><label><input class=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$row[0].'\' type=\'checkbox\' /><span style=\'margin-top: -25px;\'></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_registros\' /> </label></div>",
          "week": "1",
          "classname": "<a href=\'javascript:Envia(\"cglobales_frm.php\",'.$row[0].');\'>'.str_texto($row[1]).'</a>",
          "date": "<td>'.date('l', strtotime($row[2])).'<br><small class=\'text-muted\'><i>'.date('M d, Y', strtotime($row[2])).'</i></small></td>",
          "time": "<td>'.$row[3].'<br><small class=\'text-muted\'><i></i></small></td>",
          "students": "<td class=\'text-align-center\'><span class=\'sparkline text-align-center\' data-sparkline-type=\'line\' data-sparkline-width=\'100%\' data-sparkline-height=\'25px\'>'.$row[4].'</span></td>",
          "mandatory": "<span class=\'label label-'.$mandatory.'\'>'.str_texto($row[6]).'</span>",
          "acceso":"<div class=\'col col-sm-12 col-xs-12 col-md-12\'></div>",
          "sesiones": "<div>';
          # Obtiene las sesiones de la clase
          $Query2  = "SELECT  no_orden, ds_titulo, DATE_FORMAT(fe_clase, '%b %d, %Y'), DATE_FORMAT(fe_clase,'%H:%i'), fg_obligatorio, ";
          $Query2 .= "CASE fg_obligatorio WHEN 1 THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_obligatorio, c.ds_ruta_avatar, ";
          $Query2 .= "CONCAT(ds_nombres,' ', ds_apaterno) ";
          $Query2 .= "FROM k_clase_cg a, c_usuario b left join c_maestro c ON c.fl_maestro=b.fl_usuario ";
          $Query2 .= "WHERE a.fl_maestro=b.fl_usuario AND a.fl_clase_global=".$row[0]." ORDER BY  no_orden  ";
          $rs2 = EjecutaQuery($Query2);
          for($k=0;$row2 = RecuperaRegistro($rs2); $k++){
            # color 
            if(isset($row2[4]))
              $color = "success";
            else
              $color = "danger";
          $cuerpo .= '<tr><td style=\'width: 3%;\'></td>';
          $cuerpo .= '<td style=\'width: 4.5%;\'></td>';
          $cuerpo .= '<td style=\'width: 5%;\' class=\'text-align-center\'>'.$row2[0].'</td>';
          $cuerpo .= '<td style=\'width:20%; padding-left:10px;\'>'.$row2[1].'</td>';
          $cuerpo .= '<td style=\'width:32%; padding-left:9px;\'><div class=\'project-members\'>';
          $cuerpo .= '<a href=\'javascript:void(0)\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$row2[7].'\'>';
          $cuerpo .= '<img src=\''.PATH_MAE_IMAGES.'/avatars/' .$row2[6]. '\' class=\'online\' alt=\'user\'>';
          $cuerpo .= '</a>';
          $cuerpo .= '</div><script>$(\'[rel=tooltip]\').tooltip();</script></td>';
          $cuerpo .= '<td style=\'width:10.5%;\'>'.date('l', strtotime($row2[2])).'<br><small class=\'text-muted\'><i>'.date('M d, Y', strtotime($row2[2])).'</small></td>';
          $cuerpo .= '<td class=\'text-align-center\' style=\'width:5%;\'>'.$row2[3].'</td>';
          $cuerpo .= '<td style=\'width:10.5%;\'></td>';
          $cuerpo .= '<td class=\'text-align-center\'><span class=\'label label-'.$color.'\'>'.$row2[5].'</span></td></tr>';          
          }
      $cuerpo .= '<tr><td colspan=\'9\' style=\'padding:10px\'>';
      $cuerpo .= '<a href=\'javascript:Envia(\"cglobales_del.php\",'.$row[0].');\' class=\'btn btn-xs btn-danger pull-right deleteRecord\' style=\'margin:5px\'><i class=\'fa fa-trash-o\'>&nbsp;</i>Delete Class</a>';
      $cuerpo .= '<a href=\'javascript:Envia(\"cglobales_frm.php\",'.$row[0].');\' class=\'btn btn-xs btn-success pull-right editRecord\' style=\'margin:5px\'><i class=\'fa fa-search-plus\'>&nbsp;</i>View Class</a>';
      $cuerpo .= '</td></tr>';
      $cuerpo .= '</div>",
        "teachers": "<div class=\'project-members\'>';
        # Obtemos los teachers de las clases
        $Queryp  = "SELECT DISTINCT a.fl_maestro, c.ds_ruta_avatar, CONCAT(ds_nombres,' ' , b.ds_apaterno ) ";
        $Queryp .= "FROM k_clase_cg a, c_usuario b LEFT JOIN c_maestro c ON c.fl_maestro=b.fl_usuario  ";
        $Queryp .= "WHERE a.fl_maestro=b.fl_usuario AND a.fl_clase_global=$row[0] ";
        $rsp = EjecutaQuery($Queryp);
        for($m=0;$rowp = RecuperaRegistro($rsp); $m++){
          $cuerpo .= '<a href=\'javascript:void(0)\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$rowp[2].'\'>';
            $cuerpo .= '<img src=\''.PATH_MAE_IMAGES.'/avatars/' .$rowp[1]. '\' class=\'online\' alt=\'user\'>';
          $cuerpo .= '</a>';
        }
      $cuerpo .= '</div>"}';
      if($i<=($registros-1))
        $cuerpo .=  ",";
      else
        $cuerpo .=  "";
      echo $cuerpo;
    }
    ?>
    ]
}
