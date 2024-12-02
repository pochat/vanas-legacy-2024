<?php
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_sesion, ds_fname '".ObtenEtiqueta(117)."',ds_lname '".ObtenEtiqueta(118)."',ds_mname '".ObtenEtiqueta(119)."',ds_p_name '".ObtenEtiqueta(631)."',fe_ultmod '".ObtenEtiqueta(340)."', ds_pais '".ObtenEtiqueta(287)."', nb_programa '".ObtenEtiqueta(512)."', fe_inicio '".ObtenEtiqueta(382)."',fg_paypal '".ObtenEtiqueta(343)."|center',fg_pago '".ObtenEtiqueta(341)."', ds_cadena 'Contract Status',ds_education_number '".ObtenEtiqueta(632)."',fg_international 'International student', ds_number '".ObtenEtiqueta(280)."', ds_alt_number '".ObtenEtiqueta(281)."',
ds_email '".ObtenEtiqueta(121)."',fg_gender '".ObtenEtiqueta(114)."', fe_birth,ds_add_number '".ObtenEtiqueta(282)."', ds_add_street '".ObtenEtiqueta(283)."',ds_add_city '".ObtenEtiqueta(284)."', ds_add_state '".ObtenEtiqueta(285)."',
ds_add_zip '".ObtenEtiqueta(286)."',ds_m_add_number '".ObtenEtiqueta(282)."',  ds_m_add_street '".ObtenEtiqueta(283)."',  ds_m_add_city '".ObtenEtiqueta(284)."',  ds_m_add_state '".ObtenEtiqueta(285)."',
ds_m_add_zip '".ObtenEtiqueta(286)."', ds_pais2 '".ObtenEtiqueta(287)."', ds_eme_fname '".ObtenEtiqueta(117)."',  ds_eme_lname '".ObtenEtiqueta(118)."', ds_eme_number '".ObtenEtiqueta(280)."',
ds_eme_relation '".ObtenEtiqueta(288)."',ds_pais3 '".ObtenEtiqueta(287)."', fg_ori_via '".ObtenEtiqueta(289)."',fg_ori_ref '".ObtenEtiqueta(295)."', fe_pago '".ObtenEtiqueta(618)."' FROM( ";
  $concat = array(ConsultaFechaBD('a.fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_ultmod', FMT_HORA));
  $Query .= "SELECT fl_sesion,ds_fname ,ds_lname ,ds_mname ,ds_p_name ,(".ConcatenaBD($concat).") fe_ultmod ,d.ds_pais ds_pais ,e.nb_programa nb_programa , ";
  $Query .= "".ConsultaFechaBD('f.fe_inicio', FMT_FECHA)." fe_inicio , CASE WHEN fg_paypal='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_paypal , ";
  $Query .= "CASE WHEN fg_pago='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_pago, ";
  $Query .= "CASE WHEN (ds_firma_alumno='' OR ds_firma_alumno IS NULL) AND DATE(SUBSTRING(ds_cadena,1,8))+INTERVAL ".ObtenConfiguracion(57)." DAY < CURDATE() THEN 'Expired' ";
  $Query .= "WHEN ds_cadena<>'' AND (ds_firma_alumno='' OR ds_firma_alumno IS NULL) THEN 'Sent' WHEN ds_cadena<>'' AND ds_firma_alumno<>'' THEN 'Signed' ELSE '' END ds_cadena, ";
  $Query .= "ds_education_number , CASE fg_international WHEN 1 THEN '".ObtenEtiqueta(16)."' ELSE '".ObtenEtiqueta(17)."' END fg_international ";
  $Query .= ",ds_number , ds_alt_number , ds_email , CASE fg_gender WHEN 'M' THEN '".ObtenEtiqueta(115)."' ELSE '".ObtenEtiqueta(117)."' END fg_gender,  ";
  $Query .= "".ConsultaFechaBD('fe_birth', FMT_FECHA)." fe_birth,ds_add_number ,ds_add_street,ds_add_city ,ds_add_state ,ds_add_zip ,ds_m_add_number , ";
  $Query .= "ds_m_add_street ,ds_m_add_city ,ds_m_add_state ,ds_m_add_zip ,d.ds_pais ds_pais2 ,ds_eme_fname ,ds_eme_lname ,ds_eme_number ,ds_eme_relation , ";
  $Query .= "d.ds_pais ds_pais3 , ";
  $Query .= "CASE fg_ori_via WHEN 'A' THEN '".ObtenEtiqueta(290)."' WHEN  'B' THEN '".ObtenEtiqueta(291)."' WHEN 'C' THEN '".ObtenEtiqueta(292)."' ";
  $Query .= "WHEN 'D' THEN '".ObtenEtiqueta(293)."'  WHEN '0' THEN '".ObtenEtiqueta(294)."' END fg_ori_via , ";
  $Query .= "CASE fg_ori_ref WHEN '0' THEN '".ObtenEtiqueta(17)."' WHEN  'S' THEN '".ObtenEtiqueta(296)."' WHEN 'T' THEN '".ObtenEtiqueta(297)."' ";
  $concat2 = array(ConsultaFechaBD('g.fe_pago', FMT_FECHA), "' '", ConsultaFechaBD('g.fe_pago', FMT_HORA));
  $Query .= "WHEN 'G' THEN '".ObtenEtiqueta(298)."'  END fg_ori_ref , ";
  $Query .= "IFNULL((SELECT ".ConcatenaBD($concat2)." FROM k_ses_pago g, k_term_pago i WHERE g.cl_sesion=a.cl_sesion AND g.fl_term_pago=i.fl_term_pago AND i.no_pago='1' limit 1), '(To be paid)') as fe_pago ";
  $Query .= "FROM c_sesion a LEFT JOIN k_app_contrato c ON a.cl_sesion=c.cl_sesion, k_ses_app_frm_1 b, c_pais d, c_programa e, c_periodo f ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion 
  AND fg_app_1='1' AND fg_app_2='1' AND fg_app_3='1' AND fg_app_4='1' 
  AND fg_confirmado='1' AND fg_inscrito='0' ";
  $Query .= "AND (no_contrato IS NULL OR no_contrato=1) AND b.ds_add_country=d.fl_pais AND b.fl_programa=e.fl_programa AND b.fl_periodo=f.fl_periodo ";
  /**El listado mostrara a los aplicantes que se les paso la fecha de inicio **/
  /**Addemas que no hayan firmado contrato ni seleccionado una opcion de pago y que tenga activo el flag de archive**/
  $Query .= "AND a.fg_archive='1' ";
  /****/
  $Query .= "ORDER BY a.fe_ultmod DESC ) AS APPLICATIONS_EXP WHERE 1=1 ";  
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND ds_fname LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_lname LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND ds_pais LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND nb_programa LIKE '%$criterio%'"; break;
      case 5: $Query .= "AND DATE_FORMAT(fe_pago, '%d-%m-%Y') LIKE '%$criterio%' OR DATE_FORMAT(fe_pago, '%H:%i:%s') LIKE '%$criterio%' OR fe_pago LIKE '%$criterio%'  "; break;
      default:
        $Query .= "AND (ds_fname LIKE '%$criterio%' OR ds_lname LIKE '%$criterio%' OR ds_pais LIKE '%$criterio%' OR nb_programa LIKE '%$criterio%'  ";
        $Query .= "OR DATE_FORMAT(fe_pago, '%d-%m-%Y') LIKE '%$criterio%' OR DATE_FORMAT(fe_pago, '%H:%i:%s') LIKE '%$criterio%' OR fe_pago LIKE '%$criterio%' )";
    }
  }
  
  # Exporta el resultado a CSV
  $nom_arch = PATH_EXPORT."/applications_".date('Ymd')."_".rand(1000,9000).".csv";
  ExportaQuery($nom_arch, $Query);
  
  # Descarga el archivo
  header("Location: $nom_arch");
  

?>