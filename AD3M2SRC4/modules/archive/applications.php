<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_sesion,ds_fname '".ObtenEtiqueta(117)."',ds_lname '".ObtenEtiqueta(118)."',fe_ultmod '".ObtenEtiqueta(340)."', ";
  $Query .= "ds_pais '".ObtenEtiqueta(287)."',nb_programa '".ObtenEtiqueta(512)."',fe_inicio '".ObtenEtiqueta(382)."', ";
  $Query .= "fg_paypal '".ObtenEtiqueta(343)."|center',fg_pago '".ObtenEtiqueta(341)."', ds_cadena 'Contract Status' ,fe_pago '".ObtenEtiqueta(618)."' FROM( ";
  $concat = array(ConsultaFechaBD('a.fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_ultmod', FMT_HORA));
  $Query .= "SELECT fl_sesion, ds_fname , ds_lname ,(".ConcatenaBD($concat).") fe_ultmod, d.ds_pais ds_pais, e.nb_programa nb_programa, ";
  $Query .= "".ConsultaFechaBD('f.fe_inicio',FMT_FECHA)." fe_inicio, ";
  $Query .= "CASE WHEN fg_paypal='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_paypal, ";
  $Query .= "CASE WHEN fg_pago='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_pago, ";
  //columna de primer pago
  $concat2 = array(ConsultaFechaBD('g.fe_pago', FMT_FECHA),"' '", ConsultaFechaBD('g.fe_pago', FMT_HORA));
  $Query .= "IFNULL((SELECT ".ConcatenaBD($concat2)." FROM k_ses_pago g, k_term_pago i WHERE g.cl_sesion=a.cl_sesion AND g.fl_term_pago=i.fl_term_pago AND i.no_pago='1' limit 1), 'Expired') as fe_pago, ";
  $Query .= "CASE WHEN (ds_firma_alumno='' OR ds_firma_alumno IS NULL) AND DATE(SUBSTRING(ds_cadena,1,8))+INTERVAL ".ObtenConfiguracion(57)." DAY < CURDATE() THEN 'Expired' WHEN ds_cadena<>'' AND (ds_firma_alumno='' OR ds_firma_alumno IS NULL) THEN 'Sent' ";
  $Query .= "WHEN ds_cadena<>'' AND ds_firma_alumno<>'' THEN 'Signed' ELSE '' END ds_cadena ";
  $Query .= "FROM c_sesion a LEFT JOIN k_app_contrato c ON a.cl_sesion=c.cl_sesion, k_ses_app_frm_1 b, c_pais d, c_programa e, c_periodo f ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion ";
  $Query .= "AND fg_app_1='1' AND fg_app_2='1' AND fg_app_3='1' AND fg_app_4='1' ";
  $Query .= "AND fg_confirmado='1' ";
  $Query .= "AND fg_inscrito='0' ";
  $Query .= "AND (no_contrato IS NULL OR no_contrato=1) AND b.ds_add_country=d.fl_pais AND b.fl_programa=e.fl_programa AND b.fl_periodo=f.fl_periodo ";
  /**El listado mostrara a los aplicantes que se les paso la fecha de inicio **/
  /**Addemas que no hayan firmado contrato ni seleccionado una opcion de pago y que tenga activo el flag de archive**/
  $Query .= "AND a.fg_archive='1' ";
  /****/
  $Query .= "ORDER BY a.fe_ultmod DESC ) AS APPLICATIONS WHERE 1=1 ";
  if(!empty($criterio)) {
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
  }
  
  # Muestra pagina de listado
  define('FUNC_APP_FRM_AR',123);
  PresentaPaginaListado(FUNC_APP_FRM_AR, $Query, TB_LN_NUD, True, True, array(ObtenEtiqueta(117), ObtenEtiqueta(118), ObtenEtiqueta(287),ObtenEtiqueta(512),  ObtenEtiqueta(618)));
  
?>