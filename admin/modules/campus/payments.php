<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  $fl_usuario = ValidaSesion();
  $nuevo = RecibeParametroNumerico('nuevo');
  $cancelar=RecibeParametroNumerico('cancel');
  # Formato de la fecha se utiliza en latabla del detalle y en el export
  define('FORMAT_DATE','%b %d, %Y');
  if($cancelar)
    $inicializa=1;
  
  $Query3  = "SELECT clave, nb_programa '".ObtenEtiqueta(380)."', ";
  $Query3 .= "ds_nombre '".ETQ_NOMBRE."', no_grado '".ObtenEtiqueta(375)."|right', ";
  $Query3 .= "no_pago '".ObtenEtiqueta(481)."|right', frecuencia '".ObtenEtiqueta(482)."', 
  DATE_FORMAT(fe_pago,'".FORMAT_DATE."') '".ObtenEtiqueta(485)."', ";
  $Query3 .= "IF(mn_pago!='(To be paid)',CONCAT('$',' ',mn_pago), mn_pago) '".ObtenEtiqueta(486)."|right',
  fe_pagado '".ObtenEtiqueta(374)."', ds_metodo_pago '".ObtenEtiqueta(483)."', ";
  $Query3 .= "ds_pais '".ObtenEtiqueta(287)."', IFNULL(fg_earnedd,'0.00') '".ObtenEtiqueta(741)."', 
  IFNULL(unearned,'0.00') '".ObtenEtiqueta(742)."', IF(ganados='0/0','(To be paid)',ganados )  '".ObtenEtiqueta(743)."' ";
  # Consulta para el listado
  $Query     = "FROM (";
  # Stundets con refund
  $QueryTST  = "(SELECT b.fl_usuario clave, f.nb_programa nb_programa, ";
  $QueryTST .= "CONCAT(b.ds_nombres, ' ', b.ds_apaterno, ' ', IFNULL(b.ds_amaterno, '')) ds_nombre,e.no_grado no_grado, g.no_pago no_pago, ";
  $QueryTST .= "h.ds_frecuencia frecuencia, g.fe_pago fe_pago, IF(a.fg_refund='1',(a.mn_refund-a.mn_pagado),IFNULL(a.mn_pagado,'(To be paid)')) mn_pago, ";
  $QueryTST .= "IFNULL((CONCAT(DATE_FORMAT(!A.FE_PAGO!, '".FORMAT_DATE."'), ' ', DATE_FORMAT(!A.FE_PAGO!, '%H:%i:%s'))), '(To be paid)') fe_pagado, ";
  $QueryTST .= "CASE a.cl_metodo_pago WHEN '1' THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Paypal') ";
  $QueryTST .= "WHEN '2' THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Paypal Manual') WHEN 3 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Cheque') ";
  $QueryTST .= "WHEN 4 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Credit Card') WHEN 5 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Wire Transfer/Deposit') ";
  $QueryTST .= "WHEN 6 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Cash') END ds_metodo_pago, ";
  $QueryTST .= "(SELECT ds_pais FROM c_pais i, k_ses_app_frm_1 j WHERE i.fl_pais=j.ds_add_country AND j.cl_sesion=b.cl_sesion) ds_pais, ";
  $QueryTST .= "CASE g.no_opcion WHEN 1 THEN h.mn_a_due WHEN 2 THEN h.mn_b_due WHEN 3 THEN h.mn_c_due WHEN 4 THEN h.mn_d_due END tobepaid, ";
  $QueryTST .= "mn_earned fg_earnedd,mn_unearned unearned, ds_eu ganados, ";
  $QueryTST .= "CASE WHEN g.fe_fin_pago<'".date('Y-m-d')."' THEN 'Earned' END fe_fin_pago1, a.ds_transaccion ds_transaccion, a.ds_comentario ds_comentario, ";
  $QueryTST .= "IFNULL((CONCAT(DATE_FORMAT(a.fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(a.fe_pago, '%H:%i:%s'))), '(To be paid)') fe_graphs ";
  $QueryTST .= "FROM (c_usuario b, c_grupo d, k_term e, c_programa f, k_term_pago g, k_app_contrato h) ";
  $QueryTST .= "LEFT JOIN k_alumno_pago a  ON (a.fl_alumno=b.fl_usuario AND a.fl_term_pago=g.fl_term_pago) !K_ALUMNO_PAGO_DET! ";
  $QueryTST .= "WHERE  IF(fl_term_ini>0,
                  IF(fl_term_ini=(SELECT MIN(fl_term) FROM k_alumno_term y WHERE y.fl_alumno=b.fl_usuario),
                    e.fl_term=(SELECT MIN(fl_term) FROM k_alumno_term z WHERE z.fl_alumno=b.fl_usuario),
                    e.fl_term=(SELECT MIN(fl_term) FROM k_alumno_term s WHERE s.fl_alumno=b.fl_usuario) OR
                    e.fl_term=(SELECT MAX(fl_term) FROM k_alumno_term p WHERE p.fl_alumno=b.fl_usuario)),
                    e.fl_term=(SELECT MIN(fl_term) FROM k_alumno_term z WHERE z.fl_alumno=b.fl_usuario))";
  $QueryTST .= "AND e.fl_term=d.fl_term ";
  $QueryTST .= "AND e.fl_programa=f.fl_programa AND h.cl_sesion = b.cl_sesion AND h.no_contrato=1 ";
  $QueryTST .= "AND ((e.no_grado=1 AND g.fl_term=e.fl_term) OR (e.no_grado<>1 AND g.fl_term=e.fl_term_ini)) AND g.no_opcion=h.fg_opcion_pago ";
  $QueryTST .= " !ACTIVOS1! !EARNED! !METODO! !PAISST! !FE_LIMITST! !FE_LIMITST_DUE! !FE_PAGO_SEL! !FE_PAGO_DATE! !FE_PAGO_DET!)";
  # App fee students
  $QueryAPP  = "(SELECT CASE WHEN fg_inscrito='1' THEN (SELECT fl_usuario FROM c_usuario m WHERE m.cl_sesion=a.cl_sesion) END clave, ";
  $QueryAPP .= "nb_programa, CONCAT(ds_fname, ' ', ds_lname, ' ', IFNULL(ds_mname, '')) ds_nombre, CASE fg_inscrito ";
  $QueryAPP .= "WHEN '1' THEN IFNULL((SELECT no_grado FROM k_term e, c_grupo f, k_alumno_grupo g, c_usuario h WHERE e.fl_term=f.fl_term ";
  $QueryAPP .= "AND f.fl_grupo=g.fl_grupo AND g.fl_alumno=h.fl_usuario AND h.cl_sesion=a.cl_sesion), 'Inactive') END no_grado, "; 
  $QueryAPP .= "'0' no_pago,'App fee' frecuencia, a.fe_ultmod fe_pago, a.mn_pagado mn_pago, ";
  $QueryAPP .= "IFNULL((CONCAT(DATE_FORMAT(a.fe_pago, '".FORMAT_DATE."'), ' ', DATE_FORMAT(a.fe_pago, '%H:%i:%s'))), '(To be paid)') fe_pagado, ";
  $QueryAPP .= "CASE a.cl_metodo_pago WHEN '1' THEN 'Paypal' WHEN '2' THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' "; 
  $QueryAPP .= "WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' END ds_metodo_pago, ";
  $QueryAPP .= "(SELECT ds_pais FROM c_pais i WHERE i.fl_pais=b.ds_add_country) ds_pais,IFNULL('',a.mn_pagado) tobepaid, ";
  $QueryAPP .= "a.mn_pagado fg_earnedd,'0.00' unearned, CASE a.fe_pago < '".date('Y-m-d')."' WHEN '1' THEN '1/1' ELSE '0/1' END ganados, ";
  $QueryAPP .= "CASE WHEN a.fe_pago<'".date('Y-m-d')."' THEN 'Earned' END fe_fin_pago1, a.ds_transaccion ds_transaccion, a.ds_comentario ds_comentario, ";
  $QueryAPP .= "IFNULL((CONCAT(DATE_FORMAT(a.fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(a.fe_pago, '%H:%i:%s'))), '(To be paid)') fe_graphs ";
  $QueryAPP .= "FROM c_sesion a, k_ses_app_frm_1 b, c_programa c, k_app_contrato n ";
  $QueryAPP .= "WHERE a.cl_sesion=b.cl_sesion AND b.fl_programa = c.fl_programa AND a.fg_confirmado='1' AND a.cl_sesion=n.cl_sesion "; 
  $QueryAPP .= "AND n.no_contrato=1 AND fg_inscrito='1' AND EXISTS(SELECT 1 FROM c_usuario l WHERE l.cl_sesion=a.cl_sesion ";
  $QueryAPP .= " !ACTIVOS2!) !METODO! !PAIS! !FE_PAGO_SEL! !FE_PAGO_DATE!) ";
  $QueryST   = $QueryTST." UNION ".$QueryAPP;
  # No students con refund
  $QueryTNST = "(SELECT CONCAT('\'g-',b.fl_sesion,'\'') clave, d.nb_programa nb_programa,  ";
  $QueryTNST .= "CONCAT(g.ds_fname, ' ', g.ds_lname, ' ', IFNULL(g.ds_mname, '')) ds_nombre, '0' no_grado, e.no_pago, c.ds_frecuencia frecuencia, ";
  $QueryTNST .= "e.fe_pago fe_pago,IF(a.fg_refund='1',(a.mn_refund-a.mn_pagado),IFNULL(a.mn_pagado,'(To be paid)')) mn_pago, 
  IFNULL((CONCAT(DATE_FORMAT(a.fe_pago, '".FORMAT_DATE."'), ' ', DATE_FORMAT(a.fe_pago, '%H:%i:%s'))), '(To be paid)') fe_pagado, ";
  $QueryTNST .= "CASE a.cl_metodo_pago WHEN '1' THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Paypal')
  WHEN '2' THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Paypal Manual') WHEN 3 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Cheque') 
  WHEN 4 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Credit Card') WHEN 5 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Wire Transfer/Deposit') 
  WHEN 6 THEN CONCAT(IF(a.fg_refund='1','Refund',' '),' ','Cash') END ds_metodo_pago, ds_pais, ";
  $QueryTNST .= "CASE e.no_opcion AND ISNULL(a.mn_pagado) WHEN 1 THEN c.mn_a_due WHEN 2 THEN c.mn_b_due WHEN 3 THEN c.mn_c_due WHEN 4 THEN c.mn_d_due  END tobepaid, 
  '' fg_earnedd,'' unearned, '' ganados, CASE WHEN e.fe_fin_pago<'".date('Y-m-d')."' THEN 'Earned' END fe_fin_pago1, a.ds_transaccion ds_transaccion, a.ds_comentario ds_comentario, ";
  $QueryTNST .= "IFNULL((CONCAT(DATE_FORMAT(a.fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(a.fe_pago, '%H:%i:%s'))), '(To be paid)') fe_graphs ";
  $QueryTNST .= "FROM (k_ses_app_frm_1 g, c_sesion b, k_app_contrato c, c_programa d, k_term_pago e, k_term f, c_pais x)  ";
  $QueryTNST .= "LEFT JOIN k_ses_pago a ON(g.cl_sesion=a.cl_sesion AND a.cl_sesion = b.cl_sesion AND a.fl_term_pago = e.fl_term_pago ) ";
  $QueryTNST .= "WHERE g.cl_sesion = b.cl_sesion AND g.cl_sesion=c.cl_sesion AND g.fl_programa = d.fl_programa AND f.fl_programa = g.fl_programa AND g.ds_add_country=x.fl_pais
  AND no_contrato=1 AND fg_inscrito='0' AND fg_confirmado='1' AND e.fl_term=f.fl_term  AND e.no_opcion=c.fg_opcion_pago 
  AND f.fl_periodo = g.fl_periodo AND f.no_grado=1 !METODO! !PAIS! !FE_LIMITNST! !FE_LIMITNST_DUE! !FE_PAGO_SEL! !FE_PAGO_DATE!) ";    
  # App fee no students
  $QueryAPPN  = "(SELECT (CONCAT('\'a-',a.fl_sesion,'\'')) clave, nb_programa, ";
  $QueryAPPN .= "CONCAT(ds_fname, ' ', ds_lname, ' ', IFNULL(ds_mname, '')) ds_nombre, '(App fee form)' no_grado, ";
  $QueryAPPN .= "'(App fee form)' no_pago,'(App fee form)' frecuencia, a.fe_ultmod fe_pago, a.mn_pagado mn_pago, ";
  $QueryAPPN .= "IFNULL((CONCAT(DATE_FORMAT(a.fe_pago, '".FORMAT_DATE."'), ' ', DATE_FORMAT(a.fe_pago, '%H:%i:%s'))), '(To be paid)') fe_pagado, ";
  $QueryAPPN .= "CASE a.cl_metodo_pago WHEN '1' THEN 'Paypal' WHEN '2' THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' "; 
  $QueryAPPN .= "WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' END ds_metodo_pago, "; 
  $QueryAPPN .= "(SELECT ds_pais FROM c_pais i WHERE i.fl_pais=b.ds_add_country) ds_pais,IFNULL('',a.mn_pagado) tobepaid, ";
  $QueryAPPN .= "a.mn_pagado fg_earnedd, '0.00' unearned, CASE a.fe_pago < '".date('Y-m-d')."' WHEN '1' THEN '1/1' ELSE '0/1' END ganados, ";
  $QueryAPPN .= "CASE WHEN a.fe_pago<'".date('Y-m-d')."' THEN 'Earned' END fe_fin_pago1, a.ds_transaccion ds_transaccion, a.ds_comentario ds_comentario, ";
  $QueryAPPN .= "IFNULL((CONCAT(DATE_FORMAT(a.fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(a.fe_pago, '%H:%i:%s'))), '(To be paid)') fe_graphs ";
  $QueryAPPN .= "FROM c_sesion a, k_ses_app_frm_1 b,c_programa c, k_app_contrato d ";
  $QueryAPPN .= "WHERE a.cl_sesion=b.cl_sesion AND b.fl_programa=c.fl_programa AND a.cl_sesion=d.cl_sesion ";
  $QueryAPPN .= "AND fg_confirmado='1' AND no_contrato='1' AND fg_inscrito='0'  !METODO! !PAIS! !FE_PAGO_SEL! !FE_PAGO_DATE!) ";
  $QueryNST   = $QueryTNST." UNION ".$QueryAPPN;
  $Query .= $QueryST." UNION ".$QueryNST.") as pagos WHERE 1=1  ";
  
  # No dependen de los filtros Obtenemos el monto del anio actual y anterior incritos noincrtios y app fee
  $Query_totales  = "SELECT SUM(mn_pagado)FROM(SELECT SUM(mn_pagado) mn_pagado FROM k_alumno_pago  #YEAR# UNION ";
  $Query_totales .= "SELECT SUM(mn_pagado) mn_pagado FROM k_ses_pago #YEAR# UNION SELECT SUM(mn_pagado) mn_pagado FROM c_sesion #YEAR# ) totaltes ";
  $rowc = RecuperaValor(str_replace("#YEAR#","WHERE YEAR(fe_pago)='".date('Y')."'", $Query_totales));
  $current=number_format($rowc[0], 2, '.', ',');
  $rowp = RecuperaValor(str_replace('#YEAR#',"WHERE YEAR(fe_pago)='".date('Y',strtotime(date('Y').'- 1 YEAR'))."'",$Query_totales));
  $previous=number_format($rowp[0], 2, '.', ',');
 
  # Filtros de las busqueda normal y avanzada
  if ($actual==12){
    $Query = Query_Completo($nuevo, $fl_usuario, $QueryTST, $QueryAPP, $QueryTNST, $QueryAPPN);
  }
  else {
    # Remplaza del query las cadenas de !ACTIVOS1! y !ACTIVOS2! y algunas para la busqueda avanzada
    $Query = str_replace("!ACTIVOS1!", "AND b.fg_activo='1' ", $Query);
    $Query = str_replace("!ACTIVOS2!", "AND l.fg_activo='1' ", $Query);    
    $Query = str_replace("!EARNED!", " ", $Query);
    $Query = str_replace("!EARNED_APP!", " ", $Query);
    $Query = str_replace("!METODO!", " ", $Query);
    $Query = str_replace("!A.FE_PAGO!", "a.fe_pago", $Query);
    $Query = str_replace("!PAISST!", " ", $Query);
    $Query = str_replace("!PAIS!", " ", $Query);
    $Query = str_replace("!FE_LIMITST!", " ", $Query);
    $Query = str_replace("!FE_LIMITNST!", " ", $Query);
    $Query = str_replace("!FE_LIMITST_DUE!", " ", $Query);
    $Query = str_replace("!FE_LIMITNST_DUE!", " ", $Query);      
    $Query = str_replace("!FE_PAGO_SEL!", " ", $Query);
    # Por default mostrar solo los pagos del mes actual
    # Cuando realicen una busqueda ya se normal o avanzada ya realizara buscara en todos los registros
    if(empty($criterio))
      $Query = str_replace("!FE_PAGO_DATE!", "AND DATE_FORMAT(a.fe_pago,'%m-%Y')='".date('m-Y')."'  ", $Query);
    else
      $Query = str_replace("!FE_PAGO_DATE!", " ", $Query);
    $Query = str_replace("!FE_PAGO_DET!"," ",$Query);
    $Query = str_replace("!K_ALUMNO_PAGO_DET!", " ", $Query);
  }
  # Si trae algo el criterio
  if($criterio !='') {
    switch($actual) {
      case 1:  $Query .= "AND nb_programa LIKE '%$criterio%' "; break;
      case 2:  $Query .= "AND ds_nombre LIKE '%$criterio%' "; break;
      case 3:  $Query .= "AND no_grado LIKE '%$criterio%' "; break;
      case 4:  $Query .= "AND no_pago LIKE '%$criterio%' "; break;
      case 5:  $Query .= "AND frecuencia LIKE '%$criterio%' "; break;
      case 6:  $Query .= "AND DATE_FORMAT(fe_pago, '".FORMAT_DATE."') LIKE '%$criterio%' "; break;
      case 7:  $Query .= "AND mn_pago LIKE '%$criterio%' "; break;
      case 8:  $Query .= "AND (DATE_FORMAT(fe_pagado, '".FORMAT_DATE."') LIKE '%$criterio%' OR DATE_FORMAT(fe_pagado, '%H:%i:%s') LIKE '%$criterio%' OR fe_pagado LIKE '%$criterio%') "; break;
      case 9:  $Query .= "AND ds_metodo_pago LIKE '%$criterio%' "; break;
      case 10: $Query .= "AND (ds_transaccion LIKE '%$criterio%' OR ds_comentario LIKE '%$criterio%') "; break;
      default:
        $Query .= "AND (nb_programa LIKE '%$criterio%' ";
        $Query .= "OR ds_nombre LIKE '%$criterio%' ";
        $Query .= "OR no_grado LIKE '%$criterio%' ";
        $Query .= "OR no_pago LIKE '%$criterio%' ";
        $Query .= "OR frecuencia LIKE '%$criterio%' ";
        $Query .= "OR DATE_FORMAT(fe_pago, '".FORMAT_DATE."') LIKE '%$criterio%' ";
        $Query .= "OR mn_pago LIKE '%$criterio%' ";
        $Query .= "OR DATE_FORMAT(fe_pagado, '".FORMAT_DATE."') LIKE '%$criterio%' OR DATE_FORMAT(fe_pagado, '%H:%i:%s') LIKE '%$criterio%' OR fe_pagado LIKE '%$criterio%' ";
        $Query .= "OR ds_metodo_pago LIKE '%$criterio%' OR ds_transaccion LIKE '%$criterio%' OR ds_comentario LIKE '%$criterio%' ) ";
    }
  }
  $Query3 .= $Query."ORDER BY  fe_pagado DESC,ds_nombre,nb_programa, no_grado, CAST(no_pago AS UNSIGNED) ";
  
  //paid
  $row_paid = RecuperaValor("SELECT SUM(mn_pago) FROM (SELECT mn_pago ".$Query." ) paid");
  $paid = number_format(round($row_paid[0]), 2, '.', ',');
  //to be paid 
  $row_todepaid = RecuperaValor("SELECT SUM(tobepaid) FROM (SELECT tobepaid ".$Query." AND mn_pago='(To be paid)' ) tobepaid");
  $tobepaid = number_format(round($row_todepaid[0]), 2, '.', ',');
  //Earned
  $row_e = RecuperaValor("SELECT SUM(fg_earnedd) FROM (SELECT fg_earnedd ".$Query." ) earned");
  $earned = number_format(round($row_e[0]), 2, '.', ',');
  //Sumamos lo que no ha pagado y lo que es unearned
  $row_u = RecuperaValor("SELECT SUM(unearned) FROM (SELECT unearned ".$Query." ) unearned");
  $Unearned = number_format(round($row_u[0]), 2, '.', ','); 
  //total
  $Query_tot = "SELECT SUM(mn_pago) FROM (SELECT mn_pago ".$Query.") tot";
  $row = RecuperaValor($Query_tot);
  $total = number_format(round($row_paid[0]+$row_todepaid[0]), 2, '.', ',');
  
  $html_arriba = "";

  # Data graph
  require 'payments_graphs.php';

  # Advance search
  $html_arriba .= "
    <div id='montos' style='padding:5% 0% 0% 58%; position:absolute; 
    text-align:left; font-size:11px; border-style:2px solid; text-align:right;'>
      <b>".ObtenEtiqueta(653)."</b><br />
      <b>".ObtenEtiqueta(655).": $</b> $paid<br />
      <b>".ObtenEtiqueta(654).": $</b> $tobepaid<br />
      <b>Earned: $</b>$earned<br />
      <b>Unearned: $</b>$Unearned <br />
      <b>".ObtenEtiqueta(656).": $</b> $total<br /><br />
      <b>Annual Revenue</b><br />
      <b>".ObtenEtiqueta(651)." (".date('Y')."): $</b>$current <br />
      <b>".ObtenEtiqueta(652)." (".date('Y',strtotime(date('Y').'- 1 YEAR'))."): $</b>$previous <br />
    </div>
  <div id='div_principal' style='display: none; padding:10px; background-color: #E6E1DE; width: 75%; margin: 0 auto; position:relative; left:-90px;'></div>	
	<script type='text/javascript'>
		
		function muestraBusquedaAvanzada(inicializa) {      
      $('#div_principal').css('display', 'block');
      $.ajax({
        type: 'POST',
        url : 'div_payment_filter.php',
        async: false,
        data: 'inicializa='+inicializa,
        success: function(html) {
            $('#div_principal').html(html);
        }
      });      
		}
    muestraBusquedaAvanzada(0);
  </script>";
  
  if($actual==12) {
    $html_abajo = "
    <script type='text/javascript'>
    $('#montos').css('position', 'absolute');
      muestraBusquedaAvanzada(1);
    </script>";
  }

  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(380),ETQ_NOMBRE,ObtenEtiqueta(375), ObtenEtiqueta(481),ObtenEtiqueta(482), 
                   ObtenEtiqueta(485), ObtenEtiqueta(486),ObtenEtiqueta(374), ObtenEtiqueta(483), ObtenEtiqueta(759));
  PresentaPaginaListado(FUNC_PAGOS, $Query3, TB_LN_NUN, True, True, $campos, '', $html_arriba, $html_abajo);
  
  # QueryTST tuition students
  # QueryAPP app fee students
  # QueryTNST tuition form no students
  # QueryAPPN app fee form no students 
  # Recibe estos paramatros y como salida dependera de la busqueda que realize
  function Query_Completo($nuevo, $fl_usuario, $QueryTST, $QueryAPP, $QueryTNST, $QueryAPPN){
    if(!empty($nuevo)){
      $fe_limit = RecibeParametroHTML('opcion1');
      $opc_fechas = RecibeParametroHTML('opcion2');
      $fpagos1 = RecibeParametroHTML('fpagos1'); 
      $fpagos2 = RecibeParametroHTML('fpagos2'); 
      $fpagos3 = RecibeParametroHTML('fpagos3'); 
      $fpagos4 = RecibeParametroHTML('fpagos4'); 
      $fpagos5 = RecibeParametroHTML('fpagos5'); 
      $fpagos6 = RecibeParametroHTML('fpagos6'); 
      $fg_students = RecibeParametroHTML('fg_students');
      $fg_nstudents = RecibeParametroHTML('fg_nstudents');
      $fl_pais = RecibeParametroHTML('opcion3');
      $fg_earned_un = RecibeParametroHTML('fg_earned_un');
      $fg_activo_in = RecibeParametroHTML('fg_activo_in');
      $startdue = RecibeParametroHTML('startdue');
      $enddue = RecibeParametroHTML('enddue');
      $startdate = RecibeParametroHTML('startdate');
      $enddate = RecibeParametroHTML('enddate');
      $fg_detalle = RecibeParametroHTML('fg_detalle');
      $startdetalle = RecibeParametroHTML('startdetalle');
      $enddetalle = RecibeParametroHTML('enddetalle');
      
      # Validamos fechas 
      if(!empty($startdue))
        $startdue = "".ValidaFecha($startdue)."";
      if(!empty($enddue))
        $enddue = "".ValidaFecha($enddue)."";
      if(!empty($startdate))
        $startdate = "".ValidaFecha($startdate)."";
      if(!empty($enddate))
        $enddate = "".ValidaFecha($enddate)."";
      if(!empty($startdetalle))
        $startdetalle = "".ValidaFecha($startdetalle)."";
      if(!empty($enddetalle))
        $enddetalle = "".ValidaFecha($enddetalle)."";
        
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
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fg_students'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fg_nstudents'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fl_pais'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fg_earned_un'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fg_activo_in'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$startdue'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$enddue'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$startdate'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$enddate'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fg_detalle'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$startdetalle'), ";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$enddetalle') ";
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
      $fg_students = $row[0];
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $fg_nstudents = $row[0];
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $fl_pais = $row[0];
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $fg_earned_un = $row[0];
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $fg_activo_in = $row[0];
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $startdue = $row[0];
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $enddue = $row[0];
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $startdate = $row[0];
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $enddate = $row[0];  
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $fg_detalle = $row[0];
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $startdetalle = $row[0];  
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $enddetalle = $row[0];        
    }
    
    # Busqueda dependiendo de los radios
    # All-All Muestra todo
    # All-App fee Muestra los app fee tanto de students y aplicantes
    # All-Tuition Muestra los pagos de los students y aplicantes 
    # Aplicantes-All Muestra los app fee y pagos de los aplicantes
    # Aplicantes-App fee Muestra los app fee de los aplicantes
    # Aplicantes-Tution Muestra los pagos de los aplicantes
    # Studets-All Muestra los app fee y pagos de los students
    # Students-App fee Muestra los app fee de los students
    # Students-Tuition Muestra los pagos de los students
    $Query = "FROM(";
    if($fg_students==0){
      if($fg_nstudents==0)
        $Query .= $QueryTST." UNION ".$QueryAPP." UNION ".$QueryTNST. " UNION ".$QueryAPPN;
      if($fg_nstudents==1)
        $Query .= $QueryAPP." UNION ".$QueryAPPN;
      if($fg_nstudents==2)
        $Query .= $QueryTST." UNION ".$QueryTNST;
    }else{
      if($fg_students==1){
        if($fg_nstudents==0)
        $Query .= $QueryTNST. " UNION ".$QueryAPPN;
        if($fg_nstudents==1)
          $Query .= $QueryAPPN;
        if($fg_nstudents==2)
          $Query .= $QueryTNST;      
      }else{
        if($fg_students==2){
          if($fg_nstudents==0)
          $Query .= $QueryTST." UNION ".$QueryAPP;
          if($fg_nstudents==1)
            $Query .= $QueryAPP;
          if($fg_nstudents==2)
            $Query .= $QueryTST; 
        }
      }
      
    }    
    
    # Activo o Inactivos  o All radios
    # All muestra los activos y los inactivos
    # Activos Muestra los activo
    # Inactivos Muestra los inactivo
    if($fg_activo_in==0){
      $Query = str_replace("!ACTIVOS1!", " ", $Query);
      $Query = str_replace("!ACTIVOS2!", " ", $Query);
    }
    else{
      if($fg_activo_in==1){
        $Query = str_replace("!ACTIVOS1!", "AND b.fg_activo='1' ", $Query);
        $Query = str_replace("!ACTIVOS2!", "AND l.fg_activo='1' ", $Query);
      }
      else{
        if($fg_activo_in==2){
          $Query = str_replace("!ACTIVOS1!", "AND b.fg_activo='0' ", $Query);
          $Query = str_replace("!ACTIVOS2!", "AND l.fg_activo='0' ", $Query);
        }
      }      
    }
   
    # Metodo de pagos
    # Muestra los pagos con algun metodo que seleccionemos en los checkbox
    if(!empty($fpagos1) OR !empty($fpagos2) OR !empty($fpagos3) OR !empty($fpagos4) OR !empty($fpagos5) OR !empty($fpagos6)) {
      $metodo .= "AND a.cl_metodo_pago IN(";
      $vacio = True;
      if(!empty($fpagos1)) {
        if($vacio) {
          $metodo .= $fpagos1;
          $vacio = False;
        }
        else
          $metodo .= ",".$fpagos1;
      }
      if(!empty($fpagos2)) {
        if($vacio) {
          $metodo .= $fpagos2;
          $vacio = False;
        }
        else
          $metodo .= ",".$fpagos2;
      }
      if(!empty($fpagos3)) {
        if($vacio) {
          $metodo .= $fpagos3;
          $vacio = False;
        }
        else
          $metodo .= ",".$fpagos3;
      }
      if(!empty($fpagos4)) {
        if($vacio) {
          $metodo .= $fpagos4;
          $vacio = False;
        }
        else
          $metodo .= ",".$fpagos4;
      }
      if(!empty($fpagos5)) {
        if($vacio) {
          $metodo .= $fpagos5;
          $vacio = False;
        }
        else
          $metodo .= ",".$fpagos5;
      }
      if(!empty($fpagos6)) {
        if($vacio) {
          $metodo .= $fpagos6;
          $vacio = False;
        }
        else
          $metodo .= ",".$fpagos6;
      }
      $metodo .= ") ";
      $Query = str_replace('!METODO!',$metodo,$Query);
    }
    else
      $Query = str_replace('!METODO!'," ",$Query);
    
    # Pais del alumno 
    if(!empty($fl_pais)){
      $Query = str_replace("!PAISST!","AND (SELECT j.ds_add_country FROM c_pais i, k_ses_app_frm_1 j WHERE i.fl_pais=j.ds_add_country AND j.cl_sesion=b.cl_sesion)=$fl_pais ",$Query);
      $Query = str_replace("!PAIS!","AND ds_add_country = $fl_pais ",$Query);
    }
    else{
      $Query = str_replace("!PAISST!"," ",$Query);
      $Query = str_replace("!PAIS!"," ",$Query);
    }
    
    # Obtenemos los meses para los fechas limites y fechas de los pagos en los select
    $fecha_act = date('Y-m-d'); //fecha actual
    $weeks_2 = date('Y-m-d', strtotime('-2 week'));// the last 2 weeks
    $moths_1 = date('Y-m-d', strtotime('-30 day'));//the last 30 days
    $mes_anio_act = date('m-Y'); //mes actual
    $mes_pasado1 = date('m-Y', strtotime('-1 month')); //un mes antes
    $mes_pasado2 =  date('m-Y', strtotime('-2 month')); //dos meses antes
    $mes_sig = date('m-Y', strtotime('1 month'));
    $mes2_sig = date('m-Y', strtotime('2 months'));
    $anio_actual = date('Y');
    
    # Busqueda de registros mediante las fechas limites de pagos con el select 
    switch($fe_limit){
      case 1 : 
        $Query = str_replace("!FE_LIMITST!", "AND DATE_FORMAT(g.fe_pago,'%m-%Y')= '".$mes_pasado2."' ", $Query);
        $Query = str_replace("!FE_LIMITNST!", "AND DATE_FORMAT(e.fe_pago,'%m-%Y')= '".$mes_pasado2."' ", $Query);
      break;
      case 2 : 
        $Query = str_replace("!FE_LIMITST!", "AND DATE_FORMAT(g.fe_pago,'%m-%Y')= '".$mes_pasado1."' ", $Query); 
        $Query = str_replace("!FE_LIMITNST!", "AND DATE_FORMAT(e.fe_pago,'%m-%Y')= '".$mes_pasado1."' ", $Query); 
      break;
      case 3 : 
        $Query = str_replace("!FE_LIMITST!", "AND DATE_FORMAT(g.fe_pago, '%m-%Y')= '".$mes_anio_act."' ", $Query);
        $Query = str_replace("!FE_LIMITNST!", "AND DATE_FORMAT(e.fe_pago, '%m-%Y')= '".$mes_anio_act."' ", $Query);
      break;
      case 4 : 
        $Query = str_replace("!FE_LIMITST!", "AND DATE_FORMAT(g.fe_pago, '%m-%Y')= '".$mes_sig."' " , $Query);
        $Query = str_replace("!FE_LIMITNST!", "AND DATE_FORMAT(e.fe_pago, '%m-%Y')= '".$mes_sig."' " , $Query);
      break;
      case 5 : 
        $Query = str_replace("!FE_LIMITST!", "AND DATE_FORMAT(g.fe_pago, '%m-%Y')= '".$mes2_sig."' ", $Query); 
        $Query = str_replace("!FE_LIMITNST!", "AND DATE_FORMAT(e.fe_pago, '%m-%Y')= '".$mes2_sig."' ", $Query); 
      break;
      default: 
        $Query = str_replace("!FE_LIMITST!", "  ", $Query); 
        $Query = str_replace("!FE_LIMITNST!", " ", $Query); 
      break;      
    }
    
    # Verifica fechas en startdue y end due para  payment due
    if(!empty($startdue) AND !empty($enddue)){//agregar validacion  para que verificque los NULL que estamos insertando
      $Query = str_replace("!FE_LIMITST_DUE!", "AND DATE_FORMAT(g.fe_pago,'%Y-%m-%d')>='".$startdue."' AND DATE_FORMAT(g.fe_pago,'%Y-%m-%d')<='".$enddue."' ", $Query); 
      $Query = str_replace("!FE_LIMITNST_DUE!", "AND DATE_FORMAT(e.fe_pago,'%Y-%m-%d')>='".$startdue."' AND DATE_FORMAT(e.fe_pago,'%Y-%m-%d')<='".$enddue."' ", $Query); 
    }
    else{
      $Query = str_replace("!FE_LIMITST_DUE!", " ", $Query); 
      $Query = str_replace("!FE_LIMITNST_DUE!", " ", $Query); 
    }

    # Verifica Fechas mediante el select de payment date
    # Switch para las condiciones fechas en que se realizo el pago
    switch($opc_fechas ) {
      case 1: $Query = str_replace("!FE_PAGO_SEL!","AND a.fe_pago BETWEEN '".$weeks_2."' AND '".$fecha_act."' ",$Query); break;
      case 2: $Query = str_replace("!FE_PAGO_SEL!","AND a.fe_pago BETWEEN '".$moths_1."' AND '".$fecha_act."' ",$Query); break;
      case 3: $Query = str_replace("!FE_PAGO_SEL!","AND DATE_FORMAT(a.fe_pago, '%m-%Y') = '".$mes_anio_act."' ",$Query); break;
      case 4: $Query = str_replace("!FE_PAGO_SEL!","AND DATE_FORMAT(a.fe_pago, '%m-%Y') = '".$mes_pasado1."' ",$Query); break;
      case 5: $Query = str_replace("!FE_PAGO_SEL!","AND DATE_FORMAT(a.fe_pago, '%m-%Y')= '".$mes_pasado2."' ",$Query); break;
      default: $Query = str_replace("!FE_PAGO_SEL!"," ",$Query); break;
    }

    # Verifica fecha de pago mediante los campos de payment date
    if(!empty($startdate) AND !empty($enddate))
      $Query = str_replace("!FE_PAGO_DATE!", " AND DATE_FORMAT(a.fe_pago,'%Y-%m-%d')>= '".$startdate."' AND DATE_FORMAT(a.fe_pago,'%Y-%m-%d')<='".$enddate."' ",$Query);
    else
      $Query = str_replace("!FE_PAGO_DATE!", " ",$Query);
    
    # Con el select de detalle y los campos de fecha de detalle
    # Vamos a comparar estas fechas introducidas por las fechas de k_alumno_pago_det
    # Es decir agregamos la tabla k_alumno_pago_det
    if((!empty($fg_earned_un)) OR !empty($fg_detalle) OR  (!empty($startdetalle) AND !empty($enddetalle))){
      $Query = str_replace("!K_ALUMNO_PAGO_DET!","LEFT JOIN k_alumno_pago_det t ON (t.fl_alumno_pago=a.fl_alumno_pago) ", $Query);
      $Query = str_replace("!A.FE_PAGO!","t.fe_pago", $Query);
      $Query = str_replace("mn_earned","CASE t.fg_earned WHEN '1' THEN t.mn_pagado END ", $Query);
      $Query = str_replace("mn_unearned","CASE t.fg_earned WHEN '0' THEN t.mn_pagado END ", $Query);
      
      # Earned o Unearned o All radios
      # All muestra tanto los ganados y los no ganados
      # Earned Muestra todos los pagos que se han hido ganando
      # Unearned Muestra todos los pagos que no se han ganado
      if($fg_earned_un==0)
        $Query = str_replace("!EARNED!", " ", $Query);
      else{
        if($fg_earned_un==1)
          $Query = str_replace("!EARNED!", "AND fg_earned='1' ", $Query);
        else
          $Query = str_replace("!EARNED!", "AND fg_earned='0' ", $Query);
      }
      
      if(!empty($fg_detalle)){        
        switch($fg_detalle){
          case 1: $Query = str_replace("!FE_PAGO_DET!","AND DATE_FORMAT(t.fe_pago, '%m-%Y') = '".$mes_anio_act."' ",$Query); break;
          case 2: $Query = str_replace("!FE_PAGO_DET!","AND DATE_FORMAT(t.fe_pago, '%m-%Y') = '".$mes_pasado1."' ",$Query); break;
          case 3: $Query = str_replace("!FE_PAGO_DET!","AND DATE_FORMAT(t.fe_pago, '%m-%Y')= '".$mes_pasado2."' ",$Query); break;
          default: $Query = str_replace("!FE_PAGO_DET!"," ",$Query); break;
        }
      }
      if(!empty($startdetalle) AND !empty($enddetalle))
        $Query = str_replace("!FE_PAGO_DET!", " AND t.fe_pago BETWEEN '".$startdetalle."' AND '".$enddetalle."' ", $Query);
      else
         $Query = str_replace("!FE_PAGO_DET!", " ", $Query);
    }
    else{      
      $Query = str_replace("!K_ALUMNO_PAGO_DET!"," ", $Query);
      $Query = str_replace("!EARNED!"," ", $Query);
      $Query = str_replace("!A.FE_PAGO!","a.fe_pago", $Query);
      $Query = str_replace("!FE_PAGO_DET!", " ", $Query);
    }
    
    $Query .= ") as pagos WHERE 1=1 ";
    
    return $Query;
  }

?>