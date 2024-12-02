<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');

  # Consulta para el listado
  $Query  = "SELECT fl_sesion,ds_fname '".ObtenEtiqueta(117)."',ds_lname '".ObtenEtiqueta(118)."', ds_mname ,fe_ultmods '".ObtenEtiqueta(340)."', ";
  $Query .= "ds_pais '".ObtenEtiqueta(287)."', ds_add_state, nb_programa '".ObtenEtiqueta(512)."',fe_inicio '".ObtenEtiqueta(382)."', ";
  $Query .= "fg_paypal '".ObtenEtiqueta(343)."|center',fg_pago '".ObtenEtiqueta(341)."', ds_cadena 'Contract Status' ,fe_pago '".ObtenEtiqueta(618)."', ";
  $Query .= "ds_ruta_foto, ds_link_to_portfolio, ds_metodo_pago, fg_gender, fe_birth, edad, cl_preference_1, cl_preference_2, cl_preference_3, cl_recruiter ";
  $Query .= ", ds_email, ds_number, ds_alt_number,fe_ultmod,fg_payment,fl_programa FROM( ";
  //$concat = array(ConsultaFechaBD('a.fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_ultmod', FMT_HORA));
  $Query .= "SELECT fl_sesion, ds_fname , ds_lname ,ds_mname, DATE_FORMAT(a.fe_ultmod, '%M %d, %Y') fe_ultmods, d.ds_pais ds_pais, e.nb_programa nb_programa, ";
  $Query .= "DATE_FORMAT(f.fe_inicio, '%M %D, %Y') fe_inicio,  ";
  $Query .= "CASE WHEN fg_paypal='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_paypal, ";
  $Query .= "CASE WHEN fg_pago='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_pago, ";
  //columna de primer pago
  //$concat2 = array(ConsultaFechaBD('g.fe_pago', FMT_FECHA),"' '", ConsultaFechaBD('g.fe_pago', FMT_HORA));
  $Query .= "IFNULL((SELECT DATE_FORMAT(g.fe_pago, '%M %D, %Y') FROM k_ses_pago g, k_term_pago i WHERE g.cl_sesion=a.cl_sesion AND g.fl_term_pago=i.fl_term_pago AND i.no_pago='1' limit 1), '(To be paid)') as fe_pago, ";
  $Query .= "CASE WHEN (ds_firma_alumno='' OR ds_firma_alumno IS NULL) AND DATE(SUBSTRING(ds_cadena,1,8))+INTERVAL ".ObtenConfiguracion(57)." DAY < CURDATE() THEN 'Expired' WHEN ds_cadena<>'' AND (ds_firma_alumno='' OR ds_firma_alumno IS NULL) THEN 'Sent' ";
  $Query .= "WHEN ds_cadena<>'' AND ds_firma_alumno<>'' THEN 'Signed' ELSE 'Not sent' END ds_cadena, ";
  $Query .= "CASE WHEN  ds_add_state>0 THEN (SELECT ds_provincia FROM k_provincias pr WHERE ds_add_state=fl_provincia) ELSE ds_add_state END ds_add_state, ds_ruta_foto, b.ds_link_to_portfolio, ";
  $Query .= "CASE a.cl_metodo_pago WHEN '1' THEN 'Paypal' WHEN '2' THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' "; 
  $Query .= "WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Transfer/Deposit' WHEN 6 THEN 'Cash' ELSE '(To be paid)' END ds_metodo_pago, ";
  $Query .= "CASE b.fg_gender WHEN 'M' THEN '".ObtenEtiqueta(115)."' ELSE '".ObtenEtiqueta(116)."' END fg_gender, DATE_FORMAT(b.fe_birth, '%d-%m-%Y') fe_birth, ";
  $Query .= "TIMESTAMPDIFF(YEAR, b.fe_birth, CURDATE()) AS edad, c.cl_preference_1, c.cl_preference_2, c.cl_preference_3, ";
  $Query .= "(SELECT CONCAT(ds_nombres,' ', ds_apaterno) FROM c_usuario usr, c_perfil per WHERE usr.fl_perfil=per.fl_perfil AND usr.fl_usuario=b.cl_recruiter) cl_recruiter ";
  $Query .= ",b.ds_email, b.ds_number, b.ds_alt_number,a.fe_ultmod,c.fg_payment,e.fl_programa ";
  $Query .= "FROM c_sesion a LEFT JOIN k_app_contrato c ON a.cl_sesion=c.cl_sesion, k_ses_app_frm_1 b, c_pais d, c_programa e, c_periodo f ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion ";
  $Query .= "AND fg_app_1='1' AND fg_app_2='1' AND fg_app_3='1' AND fg_app_4='1' ";
  $Query .= "AND fg_confirmado='1' ";
  // El listado Mostrara a los aplicantes que no tengan activado el flag de archive
  $Query .= "AND fg_inscrito='0' AND a.fg_archive='0' ";
  $Query .= "AND (no_contrato IS NULL OR no_contrato=1) AND b.ds_add_country=d.fl_pais AND b.fl_programa=e.fl_programa AND b.fl_periodo=f.fl_periodo ";
  $Query .= "ORDER BY a.fe_ultmod DESC ) AS APPLICATIONS WHERE 1=1 ORDER BY fe_ultmod DESC ";
  /*if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND ds_fname LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_lname LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND ds_pais LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND nb_programa LIKE '%$criterio%'"; break;
      case 5: $Query .= "AND fe_pago LIKE '%$criterio%' OR DATE_FORMAT(fe_pago, '%d-%m-%Y') LIKE '%$criterio%' OR DATE_FORMAT(fe_pago, '%H:%i:%s') lIKE '%$criterio%'"; break;
      default:
        $Query .= "AND (ds_fname LIKE '%$criterio%' OR ds_lname LIKE '%$criterio%' OR ds_pais LIKE '%$criterio%' OR nb_programa LIKE '%$criterio%'  ";
        $Query .= "OR  fe_pago LIKE '%$criterio%' OR DATE_FORMAT(fe_pago, '%d-%m-%Y') LIKE '%$criterio%' OR DATE_FORMAT(fe_pago, '%H:%i:%s') lIKE '%$criterio%') ";
    }
  }*/
  // echo $Query;
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++){
      // link or url
      /*if(!empty($row[14])){
        $ds_link_to_portfolio = str_texto($row[14]);
      }*/
      // Colores de metodo de pago


     $Query="SELECT cl_delivery FROM k_programa_costos WHERE fl_programa=".$row['fl_programa']." ";
     $rop=RecuperaValor($Query);
     $cl_delivery=$rop['cl_delivery'];

     $fg_payment=$row['fg_payment'];

     switch ($cl_delivery)
     {
         case 'O': $modalida = "Online"; break;
         case 'S': $modalida = "On-Site"; break;
         case 'C': $modalida = "Combined"; break;
     }


     if($fg_payment=='C'){
          
          $modalida=ObtenEtiqueta(2387);
     }else
      if($fg_payment=='O'){
          $modalida=ObtenEtiqueta(2386);

     }




      switch($row[15]) {
        case "Paypal": $color = "success"; break;
        case "Paypal Manual": $color = "warning"; break;
        case "Cheque": $color = "info"; break;
        case "Credit Card": $color = "primary"; break;
        case "Transfer/Deposit": $color = "success"; break;
        case "Cash": $color = "danger"; break;
        case "(To be paid)": $color = "danger";break;
      }
      // Colores del contrato
      switch($row[11]) {
        case "Signed": $color1 = "success"; break;
        case "Sent": $color1 = "warning"; break;
        case "Not sent": $color1 = "danger"; break;
        case "Expired": $color1 = "danger"; break;
      }
      // Colores del primer pago
      if($row[12] != "(To be paid)")
        $firt_color = "success";
      else
        $firt_color = "danger";
      // Duplicados
      $Queryd  = "SELECT  usr.fl_usuario, CONCAT(usr.ds_nombres,' ',usr.ds_apaterno), prm.nb_programa, CONCAT('Start date: ', DATE_FORMAT(per.fe_inicio, '%M %D')),alm.no_promedio_t ";
      $Queryd .= "FROM c_usuario usr LEFT JOIN k_ses_app_frm_1 frm ON(frm.cl_sesion=usr.cl_sesion) LEFT JOIN c_programa prm ON(prm.fl_programa=frm.fl_programa) ";
      $Queryd .= "LEFT JOIN c_periodo per ON(per.fl_periodo=frm.fl_periodo) LEFT JOIN c_alumno alm ON(alm.fl_alumno=usr.fl_usuario) ";
      $Queryd .= "WHERE usr.ds_nombres='".$row[1]."' AND usr.ds_apaterno='".$row[2]."' ";
      $rsd = EjecutaQuery($Queryd);
      $regis = CuentaRegistros($rsd);
      $duplicados = "";
      if(!empty($regis)){
        for($j=0;$rowd=RecuperaRegistro($rsd);$j++) {
          $Query2 = "SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($rowd[4]) AND no_max >= ROUND($rowd[4]) limit 1 ";
          $row2 = RecuperaValor($Query2);
          $duplicados .= "<strong>".$rowd[1]."</strong><br/><small class='text-muted'><i>".$rowd[2].", ".$rowd[3].". Grade: ".$row2[0]."</i></small><br/>";
        }
      }
      // Preferencias
      $cl_preferences = array($row[19], $row[20], $row[21]);
      $preferencias = "";
      for($k=0;$k<3;$k++){
        switch($cl_preferences[$k]) {    
          case 0: $preferencia2 = ' '; break;
          case 1: $preferencia2 = ObtenEtiqueta(624); break;
          case 2: $preferencia2 = ObtenEtiqueta(625); break;
          case 3: $preferencia2 = ObtenEtiqueta(626); break;
          case 4: $preferencia2 = ObtenEtiqueta(627); break;
          case 5: $preferencia2 = ObtenEtiqueta(628); break;
          case 6: $preferencia2 = ObtenEtiqueta(629); break;
          case 7: $preferencia2 = ObtenEtiqueta(630); break;
        }
        $preferencias .= "<div class='col-xs-12 col-sm-3'>".$preferencia2."</div>";
      }    
      
     
      
      
      
      echo '
        {
            "checkbox": "<div class=\'checkbox\'><label><input class=\'checkbox\' onclick=\'Select('.$row[0].','.$i.');\' id=\'ch_'.$i.'\' value=\''.$row[0].'\' type=\'checkbox\' /><span></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_registros\' /> </label></div>",
            "name": "<a href=\'javascript:Envia(\"applications_frm.php\",'.$row[0].');\'><b>'.str_texto($row[1]).'</b><br><small class=\'text-muted\'><i>'.str_texto($row[2]).'&nbsp;'.str_texto($row[3]).'</i></small></a>",
            "country": "<td>'.str_texto($row[5]).'<br><small class=\'text-muted\'><i>'.$row[6].'</i></small></td>",
            "start_date": "<td>'.ObtenEtiqueta(382).':&nbsp; <i>'.$row[8].'</i>  <br><small class=\'text-muted\'>'.ObtenEtiqueta(2027).' '.str_texto($row[4]).'</i></small></td>",
            "program": "<td>'.str_texto($row[7]).'<br/><small class=\'text-muted\'><i>'.$modalida.'</i></small></td>",            
            "photo-id": "<a class=\'zoomimg\' href=\'#\'><img src=\''.PATH_ALU_IMAGES.'/id/'.str_texto($row[13]).'\' class=\'away no-border\' width=\'30px\' height=\'30px\'><span style=\'left:-300px;\'><div class=\'modal-dialog demo-modal\'><div class=\'modal-content\'><div class=\'modal-body padding-5\'><img class=\'superbox-current-img\' src=\''.PATH_ALU_IMAGES.'/id/'.str_texto($row[13]).'\'><br><strong>'.str_texto($row[1]).'</strong></div></div></div></span></a>",
            "actual": "<span class=\'sparkline text-align-center\' data-sparkline-type=\'line\' data-sparkline-width=\'100%\' data-sparkline-height=\'25px\'>gabriel</span>",
            "contract": " <span class=\'label label-'.$color1.'\'>'.$row[11].'</span>",
            "portafolio": "<a href=\''.$row[14].'\' target=\'_blank\'>'.$row[14].'</a>",
            "firt_payment": "<span class=\'label label-'.$firt_color.'\'>'.$row[12].'</span>",            
            "metodo": "<span class=\'label label-'.$color.'\'>'.$row[15].'</span>",
            "gender": "<i class=\'fa fa-'.strtolower($row[16]).' \'></i>&nbsp;'.$row[16].'",
            "duplicados": "'.$duplicados.'",
            "edad": "'.$row[17].' ('.$row[18].' years of age)",
            "preferences": "<div class=\'row col-xs-12 col-sm-12\' style=\'padding-left: 0px;\'>'.$preferencias.'</div>",
            "action": "<strong>'.$row[22].'</strong>'
              . '<a href=\'javascript:Borra(\"applications_del.php\",'.$row[0].');\' class=\'btn btn-xs btn-danger pull-right\' style=\'margin-left:5px\'>Delete Student</a> '
              . '<a href=\'javascript:Envia(\"applications_frm.php\",'.$row[0].');\' class=\'btn btn-xs btn-success pull-right\' style=\'margin-left:5px\'>View Student File</a>'
              . '<a href=\''.PATH_MODULOS.'/campus/payments_frm.php?clave='.$row[0].'&a=1\' target=\'blank\' class=\'btn btn-xs btn-warning pull-right\'><i class=\'fa fa-money\'></i> '.ObtenEtiqueta(886).'</a> ",
            "information":"<div class=\'col col-sm-12 col-xs-12 col-md-12\'>'
              . '<div class=\'col col-sm-4 col-md-4 col-xs-4\'><strong>'.ObtenEtiqueta(121).': </strong><br>'.$row[23].'</div>'
              . '<div class=\'col col-sm-4 col-md-4 col-xs-4\'><strong>Contact Number: </strong><br>'.$row[24].'</div>'
              . '<div class=\'col col-sm-4 col-md-4 col-xs-4\'><strong>'.ObtenEtiqueta(281).': </strong><br>'.$row[25].'</div>'
              . '</div>"
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
    ]
}