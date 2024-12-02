<?php

function Query_Completo($nuevo, $fl_usuario){
  // pagos por alumonos incritos
  $QueryT = "(SELECT  b.fl_usuario clave, f.nb_programa nb_programa,CONCAT(b.ds_nombres, ' ', b.ds_apaterno, ' ', IFNULL(b.ds_amaterno, '')) ds_nombre,
  e.no_grado no_grado, g.no_pago no_pago,h.ds_frecuencia frecuencia, g.fe_pago fe_pago,
  IF(a.fg_refund='1',(a.mn_refund-a.mn_pagado),IFNULL(t.mn_pagado,'(To be paid)')) mn_pago,
  IFNULL((CONCAT(DATE_FORMAT(t.fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(t.fe_pago, '%H:%i:%s'))), '(To be paid)') fe_pagado,
  CASE a.cl_metodo_pago WHEN '1' THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Paypal') 
  WHEN '2' THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Paypal Manual') 
  WHEN 3 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Cheque') 
  WHEN 4 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Credit Card') 
  WHEN 5 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Wire Transfer/Deposit')
  WHEN 6 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Cash') END ds_metodo_pago, 
  (SELECT ds_pais FROM c_pais i, k_ses_app_frm_1 j WHERE i.fl_pais=j.ds_add_country AND j.cl_sesion=b.cl_sesion) ds_pais, 
  CASE g.no_opcion WHEN 1 THEN h.mn_a_due WHEN 2 THEN h.mn_b_due WHEN 3 THEN h.mn_c_due WHEN 4 THEN h.mn_d_due END tobepaid, 
  '' fg_earnedd, '' unearned, '' ganados, a.cl_metodo_pago,a.fe_pago pagado ,
  CASE g.fe_fin_pago<'".date('Y-m-d')."' WHEN 1 THEN 'Earned' END fe_fin_pago1 
  FROM (c_usuario b, k_alumno_grupo c,c_grupo d, k_term e, c_programa f, k_term_pago g, k_app_contrato h) 
  LEFT JOIN k_alumno_pago a ON (a.fl_alumno=b.fl_usuario AND a.fl_term_pago=g.fl_term_pago)
  LEFT JOIN k_alumno_pago_det t ON (t.fl_alumno_pago=a.fl_alumno_pago)
  WHERE b.fl_usuario=c.fl_alumno AND c.fl_grupo=d.fl_grupo AND e.fl_term=d.fl_term AND e.fl_programa=f.fl_programa 
  AND h.cl_sesion = b.cl_sesion AND
  h.no_contrato=1 AND ((e.no_grado=1 AND g.fl_term=e.fl_term) 
  OR (e.no_grado<>1 AND g.fl_term=e.fl_term_ini)) AND g.no_opcion=h.fg_opcion_pago AND fg_activo='1' GROUP BY a.fl_alumno_pago !ACTIVOS1!) ";
  // aplicaciones
  /*$QueryAPP  = "(SELECT CASE WHEN fg_inscrito='1' THEN (SELECT fl_usuario FROM c_usuario m WHERE m.cl_sesion=a.cl_sesion) 
  ELSE (CONCAT('\'a-',a.fl_sesion,'\'')) END clave, nb_programa, ";    
  $QueryAPP .= "CONCAT(ds_fname, ' ', ds_lname, ' ', IFNULL(ds_mname, '')) ds_nombre, ";
  $QueryAPP .= "CASE fg_inscrito WHEN '1' THEN IFNULL(( SELECT no_grado FROM k_term e, c_grupo f, k_alumno_grupo g, c_usuario h WHERE e.fl_term=f.fl_term AND f.fl_grupo=g.fl_grupo AND ";
  $QueryAPP .= "g.fl_alumno=h.fl_usuario AND h.cl_sesion=a.cl_sesion ), '0') ELSE '0' END no_grado, ";
  $concat2 = array(ConsultaFechaBD('a.fe_pago', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_pago', FMT_HORA));
  $QueryAPP .= " '0' no_pago,'(App form)' frecuencia,a.fe_ultmod  fe_pago, a.mn_pagado mn_pago,IFNULL((".ConcatenaBD($concat2)."), '(To be paid)') fe_pagado,   ";    
  $QueryAPP .= " CASE a.cl_metodo_pago WHEN '1' THEN 'Paypal' WHEN '2' THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' "; 
  $QueryAPP .= "WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit'  WHEN 6 THEN 'Cash' END ds_metodo_pago,  ";
  $QueryAPP .= "ds_pais, IFNULL('',a.mn_pagado) tobepaid,a.mn_pagado fg_earnedd, '0.00' unearned, 
  CASE a.fe_pago < CURDATE() WHEN '1' THEN '1/1' ELSE '0/1' END ganados,
  a.cl_metodo_pago,a.fe_pago pagado, CASE WHEN a.fe_pago<'".date('Y-m-d')."' THEN 'Earned' END fe_fin_pago1 ";
  $QueryAPP .= "FROM c_sesion a, k_ses_app_frm_1 b, c_programa c, k_app_contrato n, c_pais i WHERE a.cl_sesion=b.cl_sesion AND b.fl_programa = c.fl_programa AND a.fg_confirmado='1'  AND a.cl_sesion=n.cl_sesion AND n.no_contrato=1 ";
  $QueryAPP .= "AND b.ds_add_country=i.fl_pais AND (fg_inscrito='0' OR (fg_inscrito='1' AND EXISTS(SELECT 1 FROM c_usuario l WHERE l.cl_sesion=a.cl_sesion !ACTIVOS2!)))) ";*/
  
  if(!empty($nuevo)){
    $fe_limit = RecibeParametroHTML('opcion1');
    $opc_fechas = RecibeParametroHTML('opcion2');
    $fpagos1 = RecibeParametroHTML('fpagos1'); 
    $fpagos2 = RecibeParametroHTML('fpagos2'); 
    $fpagos3 = RecibeParametroHTML('fpagos3'); 
    $fpagos4 = RecibeParametroHTML('fpagos4'); 
    $fpagos5 = RecibeParametroHTML('fpagos5'); 
    $fpagos6 = RecibeParametroHTML('fpagos6'); 
    $tuition = RecibeParametroHTML('tuition');
    $app_fee = RecibeParametroHTML('app_fee');
    $fl_pais = RecibeParametroHTML('opcion3');
    $fg_unearned = RecibeParametroHTML('fg_unearned');
    $fg_activo = RecibeParametroHTML('fg_activo');
    $startdue = RecibeParametroHTML('startdue');
    $enddue = RecibeParametroHTML('enddue');
    $startdate = RecibeParametroHTML('startdate');
    $enddate = RecibeParametroHTML('enddate');
    
    # Validamos fechas 
    if(!empty($startdue))
      $startdue = "'".ValidaFecha($startdue)."'";
    else
      $startdue = "NULL";
    if(!empty($enddue))
      $enddue = "'".ValidaFecha($enddue)."'";
    else
      $enddue = "NULL";
    if(!empty($startdate))
      $startdate = "'".ValidaFecha($startdate)."'";
    else
      $startdate = "NULL";
    if(!empty($enddate))
      $enddate = "'".ValidaFecha($enddate)."'";
    else
      $enddate = "NULL";
      
    $Query_del= "DELETE FROM k_usu_parametro WHERE fl_usuario=$fl_usuario ";
    $Query_del .= "AND EXISTS(SELECT 1 FROM k_parametro_funcion b WHERE fl_funcion=34 AND b.fl_parametro_funcion=k_usu_parametro.fl_parametro_funcion) ";
    EjecutaQuery($Query_del);
    $Query2 = "INSERT INTO k_usu_parametro (fl_parametro_funcion, fl_usuario, ds_valor) VALUES";
    $parametro = 43;
    $Query2 .= "(".$parametro++.", $fl_usuario, '$opc_fechas'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos1'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos2'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos3'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos4'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos5'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos6'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$fe_limit'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$tuition'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$app_fee'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$fl_pais'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$fg_unearned'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, '$fg_activo'), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, $startdue), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, $enddue), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, $startdate), ";
    $Query2 .= "(".$parametro++.", $fl_usuario, $enddate) ";
    EjecutaQuery($Query2);
  }
  else{
    $parametro=43;
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $opc_fechas = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos1 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos2 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos3 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos4 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos5 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos6 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fe_limit = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $tuition = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $app_fee = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fl_pais = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fg_unearned = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fg_activo = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $startdue = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $enddue = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $startdate = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $enddate = $row[0];  
  }
    
  $fecha_act = date('Y-m-d'); //fecha actual
  $weeks_2 = date('Y-m-d', strtotime('-2 week'));// the last 2 weeks
  $moths_1 = date('Y-m-d', strtotime('-30 day'));//the last 30 days
  $mes_anio_act = date('m-Y'); //mes actual
  $mes_pasado1 = date('m-Y', strtotime('-1 month')); //un mes antes
  $mes_pasado2 =  date('m-Y', strtotime('-2 month')); //dos meses antes
  $mes_sig = date('m-Y', strtotime('1 month'));
  $mes2_sig = date('m-Y', strtotime('2 months'));
  $anio_actual = date('Y');
     
    
  # Filtros para tipo de registro (App form y tuition notmal)
  if((!empty($tuition) AND !empty($app_fee)) OR (empty($tuition) AND empty($app_fee)))
    $Query  = "FROM($QueryT UNION $QueryAPP) as pagos WHERE 1=1 ";
  
  # si tuition es 7 y app_fee 0
  if(!empty($tuition) AND empty($app_fee))
    $Query = "FROM($QueryT) as pagos WHERE 1=1 ";
  
  # si tuition es 0 y app_fee 8
  if(empty($tuition) AND !empty($app_fee))
    $Query = "FROM($QueryAPP) as pagos WHERE 1=1 ";
  
    
  # Query de las opciones de pagos
  if(!empty($fpagos1) OR !empty($fpagos2) OR !empty($fpagos3) OR !empty($fpagos4) OR !empty($fpagos5) OR !empty($fpagos6)) {
    $Query .= "AND cl_metodo_pago IN(";
    $vacio = True;
    if(!empty($fpagos1)) {
      if($vacio) {
        $Query .= $fpagos1;
        $vacio = False;
      }
      else
        $Query .= ",".$fpagos1;
    }
    if(!empty($fpagos2)) {
      if($vacio) {
        $Query .= $fpagos2;
        $vacio = False;
      }
      else
        $Query .= ",".$fpagos2;
    }
    if(!empty($fpagos3)) {
      if($vacio) {
        $Query .= $fpagos3;
        $vacio = False;
      }
      else
        $Query .= ",".$fpagos3;
    }
    if(!empty($fpagos4)) {
      if($vacio) {
        $Query .= $fpagos4;
        $vacio = False;
      }
      else
        $Query .= ",".$fpagos4;
    }
    if(!empty($fpagos5)) {
      if($vacio) {
        $Query .= $fpagos5;
        $vacio = False;
      }
      else
        $Query .= ",".$fpagos5;
    }
    if(!empty($fpagos6)) {
      if($vacio) {
        $Query .= $fpagos6;
        $vacio = False;
      }
      else
        $Query .= ",".$fpagos6;
    }
    $Query .= ") ";
  }
  
  #Fechas limites a pagar
  switch($fe_limit){
    case 1 : $Query .= "AND DATE_FORMAT(fe_pago,'%m-%Y')= '".$mes_pasado2."' "; break;
    case 2 : $Query .= "AND DATE_FORMAT(fe_pago,'%m-%Y')= '".$mes_pasado1."' ";break;
    case 3 : $Query .= "AND DATE_FORMAT(fe_pago, '%m-%Y')= '".$mes_anio_act."' "; break;
    case 4 : $Query .= "AND DATE_FORMAT(fe_pago, '%m-%Y')= '".$mes_sig."' ";break;
    case 5 : $Query .= "AND DATE_FORMAT(fe_pago, '%m-%Y')= '".$mes2_sig."' ";break;
    case 6 : $Query = "AND fe_pago NULL "; break;
  }

  #switch para las condiciones fechas en que se realizo el pago
  switch($opc_fechas ) {
    case 1: $opc_fechas1 = "AND pagado BETWEEN '".$weeks_2."' AND '".$fecha_act."' "; break;
    case 2: $opc_fechas1 = "AND pagado BETWEEN '".$moths_1."' AND '".$fecha_act."' "; break;
    case 3: $opc_fechas1 = "AND DATE_FORMAT(pagado, '%m-%Y') = '".$mes_anio_act."' "; break;
    case 4: $opc_fechas1 = "AND DATE_FORMAT(pagado, '%m-%Y') = '".$mes_pasado1."' "; break;
    case 5: $opc_fechas1 = "AND DATE_FORMAT(pagado, '%m-%Y')= '".$mes_pasado2."' "; break;
    case 6: $opc_fechas1 = "AND pagado IS NULL "; break;
  }
  
  # Verifica el pais
  if(!empty($fl_pais))
    $Query .= "AND ds_pais = (SELECT ds_pais FROM c_pais WHERE fl_pais=$fl_pais) ";
  
  # Verifica el unearned
  if(!empty($fg_unearned)){
    $Query = str_replace("!FG_UNEARNED!","  AND t.fg_earned='0'  ",$Query);
    $Query = str_replace("!FG_UNEARNEDAPP!","AND a.fe_pago > CURDATE() ",$Query);
  }
  ELSE{
    $Query = str_replace("!FG_UNEARNED!"," ",$Query);
    $Query = str_replace("!FG_UNEARNEDAPP!"," ",$Query);
  }
    

  # si fg_activo es 0 muestra solo los activos, si es 1 muestra los activos y Inactivos
  if(empty($fg_activo)){
        $Query = str_replace("!ACTIVOS1!", "AND b.fg_activo='1' ", $Query);
        $Query = str_replace("!ACTIVOS2!", "AND l.fg_activo='1' ", $Query);
  }
  else{
      $Query = str_replace("!ACTIVOS1!", " ", $Query);
      $Query = str_replace("!ACTIVOS2!", " ", $Query);
  }
    
  # Verifica fechas en startdue y end due para  payment due
  if(!empty($startdue) AND !empty($enddue))//agregar validacion  para que verificque los NULL que estamos insertando
    $Query .= "AND fe_pago BETWEEN '".$startdue."' AND '".$enddue."'";
  else
    $Query .= " ";

  # Verifica fechas en startdate y end enddate para  payment date
  if(!empty($startdate) AND !empty($enddate)) {
    $Query = str_replace("!DATET!", " AND t.fe_pago>='".$startdate."' AND t.fe_pago<='".$enddate."' ",$Query); 
    $Query = str_replace("!DATEAPPFEE!", " AND a.fe_pago>='".$startdate."' AND a.fe_pago<='".$enddate."' ",$Query); 
  }
  else{
    $Query = str_replace("!DATET!", " ",$Query); 
    $Query = str_replace("!DATEAPPFEE!", " ",$Query);             
  }
    
    
  return $Query;
}
  
?>