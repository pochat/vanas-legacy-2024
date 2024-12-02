<?php

	# Funcion para quitar caracteres especiales y saltos de linea
  function getStrParaCSV($str) {
	$str_aux = $str;
	$str_aux = str_replace(",", " ", $str_aux);
	$str_aux = str_replace("\n", " ", $str_aux);
	$str_aux = str_replace("\r", " ", $str_aux);
  
	return $str_aux;  
  }
?>
<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ALUMNOS, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual'); 
  $nuevo = RecibeParametroNumerico('nuevo');
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  if ($actual==6)   # Si se realiza busqueda avanzada
    $Query = Query_Adv_Search($nuevo);
  else
    $Query = Query_Completo($criterio, $actual);
    
  function Query_Adv_Search($p_criterio) {
    
    $Query  = "SELECT fl_usuario, ds_login '".ETQ_USUARIO."', ";
    $Query .= "ds_nombres '".ETQ_NOMBRE."', ";
    $Query .= "ds_amaterno '".ObtenEtiqueta(119)."', ";
    $Query .= "ds_apaterno '".ObtenEtiqueta(118)."', ";
    $Query .= "fg_genero  '".ObtenEtiqueta(114)."', ";
    $Query .= "".ConsultaFechaBD('fe_nacimiento',FMT_FECHA)." '".ObtenEtiqueta(120)."', ";
    $Query .= "ds_email '".ObtenEtiqueta(121)."',nb_perfil ".ObtenEtiqueta(110).", ";
    $Query .= "CASE WHEN fg_activo='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(113)."',ds_notas '".ObtenEtiqueta(196)."', ";
    $Query .= "fe_alta '".ObtenEtiqueta(111)."', ";
    $Query .= "fe_ultacc '".ObtenEtiqueta(112)."', ";
    $Query .= "no_accesos '".ObtenEtiqueta(122)."', ";
    $Query .= "CASE WHEN fg_pago='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(341)."',";
    $Query .= "CASE WHEN fg_paypal='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(343)."',";
    $Query .= "fe_ultmod '".ObtenEtiqueta(340)."',";
    $Query .= "nb_programa '".ObtenEtiqueta(360)."', ";
    $Query .= "ds_profesor '".ObtenEtiqueta(297)."', ";
    $Query .= "nb_grupo '".ObtenEtiqueta(426)."', ";
    $Query .= "no_grado '".ObtenEtiqueta(375)."', ds_number '".ObtenEtiqueta(280)."', ds_alt_number '".ObtenEtiqueta(281)."', ";
    $Query .= "ds_add_number '".ObtenEtiqueta(282)."', ds_add_street '".ObtenEtiqueta(283)."', ds_add_zip '".ObtenEtiqueta(286)."', ";    
    $Query .= "ds_add_city '".ObtenEtiqueta(284)."', ";
    $Query .= "ds_add_state '".ObtenEtiqueta(285)."', ";
    $Query .= "ds_pais '".ObtenEtiqueta(287)."',ds_eme_fname '".ObtenEtiqueta(117)."',ds_eme_lname '".ObtenEtiqueta(118)."', ";
    $Query .= "ds_eme_number '".ObtenEtiqueta(280)."', ds_eme_relation '".ObtenEtiqueta(288)."', ";
    $Query .= "nb_periodo '".ObtenEtiqueta(342)."', ";
    //$Query .= "fe_ultacc '".ObtenEtiqueta(112)."', ";
    $Query .= "CASE WHEN fg_international='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(620)."', ";
    $Query .= "nb_zona_horaria '".ObtenEtiqueta(411)."', ";
    $Query .= "fe_start_date '".ObtenEtiqueta(60)."', ";
    //$Query .= "ye_fe_start_date '".ObtenEtiqueta(674)."', ";
    $Query .= "ds_website '".ObtenEtiqueta(414)."', ";
    $Query .= "fe_carta '".ObtenEtiqueta(540)."', ";
    $Query .= "fe_contrato '".ObtenEtiqueta(541)."', ";
    $Query .= "fe_fin '".ObtenEtiqueta(544)."', ";
    $Query .= "fe_completado '".ObtenEtiqueta(545)."', ";
    $Query .= "fe_emision '".ObtenEtiqueta(546)."', ";
    $Query .= "CASE WHEN fg_certificado='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(547)."', ";
    $Query .= "CASE WHEN fg_honores='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(547)."', ";
    $Query .= "fe_graduacion '".ObtenEtiqueta(556)."', ";
    $Query .= "CASE WHEN fg_desercion='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(558)."', ";
    $Query .= "CASE WHEN fg_dismissed='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(559)."', ";
    $Query .= "CASE WHEN fg_job='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(644)."', ";
    $Query .= "CASE WHEN fg_graduacion='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(645)."', ";
    $Query .= "ye_fe_nacimiento '".ObtenEtiqueta(645)."', ";
    $Query .= "ye_fe_alta '".ObtenEtiqueta(671)."', ";
    $Query .= "ye_fe_ultacc '".ObtenEtiqueta(672)."', ";
    $Query .= "ye_fe_ultmod '".ObtenEtiqueta(673)."', ";
    $Query .= "ye_fe_carta '".ObtenEtiqueta(675)."', ";
    $Query .= "ye_fe_contrato '".ObtenEtiqueta(676)."', ";
    $Query .= "ye_fe_fin '".ObtenEtiqueta(677)."', ";
    $Query .= "ye_fe_completado '".ObtenEtiqueta(678)."', ";
    $Query .= "ye_fe_emision '".ObtenEtiqueta(679)."', ";
    $Query .= "ye_fe_graduacion '".ObtenEtiqueta(680)."' ";
    $Query .= "FROM (";
    $Query .= "
    SELECT a.fl_usuario fl_usuario, a.cl_sesion cl_sesion, a.ds_login ds_login, a.ds_nombres ds_nombres, a.ds_apaterno ds_apaterno, 
    a.ds_amaterno ds_amaterno, a.fg_genero fg_genero, 
    fe_nacimiento,
    a.ds_email, b.nb_perfil,
    fg_activo, ds_notas, 
    fe_alta, 
    fe_ultacc, no_accesos,
    (SELECT fe_ultmod FROM c_sesion se WHERE se.cl_sesion=a.cl_sesion) fe_ultmod,
    (SELECT CONCAT(nb_zona_horaria, ' ', 'GMT', ' (', no_gmt, ')') FROM c_zona_horaria zo WHERE zo.fl_zona_horaria=c.fl_zona_horaria) nb_zona_horaria,
    (SELECT CASE WHEN fg_international='1' THEN 'Yes' ELSE 'No' END FROM k_app_contrato app WHERE app.cl_sesion=a.cl_sesion ORDER BY no_contrato LIMIT 1) fg_international,
    (SELECT w.nb_periodo FROM c_periodo w, k_term s WHERE w.fl_periodo=s.fl_periodo AND s.fl_term=(SELECT MIN(r.fl_term) FROM k_alumno_term r WHERE r.fl_alumno=a.fl_usuario)) nb_periodo,
    (SELECT fe_inicio FROM k_term te, c_periodo i, k_alumno_term al WHERE te.fl_periodo=i.fl_periodo AND te.fl_term=al.fl_term AND al.fl_alumno=a.fl_usuario AND no_grado=1 LIMIT 1) fe_start_date,
    ds_website,(SELECT nb_programa FROM c_programa e WHERE e.fl_programa=f.fl_programa) nb_programa,
    (SELECT CONCAT(h.ds_nombres, ' ', h.ds_apaterno) FROM k_alumno_grupo k, c_grupo gpo, c_usuario h WHERE k.fl_grupo=gpo.fl_grupo AND gpo.fl_maestro=h.fl_usuario AND k.fl_alumno=a.fl_usuario) ds_profesor,
    (SELECT nb_grupo FROM c_grupo n, k_alumno_grupo o WHERE n.fl_grupo=o.fl_grupo AND o.fl_alumno=a.fl_usuario) nb_grupo,
    (SELECT no_grado FROM k_term b, k_alumno_grupo d, c_grupo m WHERE b.fl_term=m.fl_term AND d.fl_grupo=m.fl_grupo AND d.fl_alumno=a.fl_usuario) no_grado, 
    ds_number,ds_alt_number,ds_add_number,ds_add_street,ds_add_zip, ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation,
    fe_carta, fe_contrato, fe_fin, 
	  fe_completado, fe_emision, 
		fg_certificado, 
		fg_honores, 
		fe_graduacion, 
		fg_desercion,
    fg_dismissed,
    fg_job,
    fg_graduacion, 
    ds_add_city, CASE ds_add_country WHEN 38 THEN (SELECT ds_provincia FROM k_provincias pr WHERE pr.fl_provincia=ds_add_state) ELSE ds_add_state END ds_add_state,
    (SELECT fg_pago FROM c_sesion ses WHERE a.cl_sesion=ses.cl_sesion) fg_pago, 
    (SELECT fg_paypal FROM c_sesion ses WHERE a.cl_sesion=ses.cl_sesion) fg_paypal,
    (SELECT p.ds_pais FROM c_pais p WHERE p.fl_pais=f.ds_add_country) ds_pais,
    YEAR(fe_nacimiento) ye_fe_nacimiento, YEAR(fe_alta) ye_fe_alta, YEAR(fe_ultacc) ye_fe_ultacc, YEAR(fe_ultmod) ye_fe_ultmod, 
    YEAR(fe_carta) ye_fe_carta, YEAR(fe_contrato) ye_fe_contrato, YEAR(fe_fin) ye_fe_fin, YEAR(fe_completado) ye_fe_completado, 
    YEAR(fe_emision) ye_fe_emision, YEAR(fe_graduacion) ye_fe_graduacion,
    YEAR((SELECT fe_inicio FROM k_term te, c_periodo i, k_alumno_term al WHERE te.fl_periodo=i.fl_periodo AND te.fl_term=al.fl_term AND al.fl_alumno=a.fl_usuario AND no_grado=1 LIMIT 1)) ye_fe_start_date    
    FROM (c_usuario a, c_alumno c, k_ses_app_frm_1 f, c_perfil b) LEFT JOIN k_pctia j ON (a.fl_usuario=j.fl_alumno)
    WHERE a.fl_usuario=c.fl_alumno AND a.fl_perfil=b.fl_perfil AND a.cl_sesion=f.cl_sesion AND a.fl_perfil=".PFL_ESTUDIANTE." "; 
    $Query .= ") AS principal ";
    $Query .= "WHERE fl_usuario NOT LIKE '0' ";
    $nuevo=$p_criterio;
    require 'filtros.inc.php';
    $Query .= "ORDER BY ds_login";
    
     # Exporta el resultado a CSV
    $nom_arch = PATH_EXPORT."/students_".date('Ymd')."_".rand(1000,9000).".csv";
   
     # Abre archivo de salida
    if(!$archivo = fopen($_SERVER[DOCUMENT_ROOT].$nom_arch, "wb")) {
      MuestraPaginaError(ERR_EXPORTAR);
      exit;
    }
    
    # Exporta los datos
    $rs = EjecutaQuery($Query);
    $tot_campos = CuentaCampos($rs);
    for($i = 1; $i < $tot_campos; $i++)
      fwrite($archivo, str_replace(",", " ", str_ascii(NombreCampo($rs, $i, True))).",");
    fwrite($archivo, "\n");
    while($row = RecuperaRegistro($rs)) {
      for($i = 1; $i < $tot_campos; $i++)
        fwrite($archivo, str_replace(",", " ", getStrParaCSV(str_ascii(DecodificaEscogeIdiomaBD($row[$i])))).",");
      fwrite($archivo, "\n");
    }
  
  # Cierra el archivo
  fclose($archivo);
  
  # Descarga el archivo
  header("Location: $nom_arch");
    
  }
  
  function Query_Completo($p_criterio, $p_actual) {
  
    # Consulta principal
    $Query  = "SELECT fl_usuario, a.cl_sesion,  ds_login '".ETQ_USUARIO."', ds_nombres '".ETQ_NOMBRE."', ds_apaterno'".ObtenEtiqueta(118)."', ds_amaterno '".ObtenEtiqueta(119)."', fg_genero '".ObtenEtiqueta(114)."', ".ConsultaFechaBD('fe_nacimiento', FMT_FECHA)." '".ObtenEtiqueta(120)."', ";
    $Query .= "a.ds_email '".ObtenEtiqueta(121)."', b.nb_perfil '".ObtenEtiqueta(110)."', CASE WHEN fg_activo='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(113)."', ";
    $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
    $Query .= "ds_notas '".ObtenEtiqueta(196)."', ".ConsultaFechaBD('fe_alta', FMT_FECHA)." '".ObtenEtiqueta(111)."', "."(".ConcatenaBD($concat).") '".ObtenEtiqueta(112)."', no_accesos '".ObtenEtiqueta(122)."', ";
    $concat = array(ConsultaFechaBD('d.fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('d.fe_ultmod', FMT_HORA));
    $Query .= "CASE WHEN fg_pago='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(341)."', ";
    $Query .= "CASE WHEN fg_paypal='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(343)."', ".ConcatenaBD($concat)." '".ObtenEtiqueta(340)."', nb_programa '".ObtenEtiqueta(360)."' ";
    $Query .= "FROM c_usuario a, c_perfil b, c_alumno c, c_sesion d, c_programa e, k_ses_app_frm_1 f ";
    $Query .= "WHERE a.fl_perfil=b.fl_perfil ";
    $Query .= "AND a.fl_usuario=c.fl_alumno ";
    $Query .= "AND a.cl_sesion=d.cl_sesion ";
    $Query .= "AND f.cl_sesion=d.cl_sesion ";
    $Query .= "AND f.fl_programa=e.fl_programa ";
    
    if(!empty($p_criterio)) {
      switch($p_actual) {
        case 1: $Query .= "AND ds_login LIKE '%$p_criterio%' "; break;
        case 2: $Query .= "AND (ds_nombres LIKE '%$p_criterio%' OR ds_apaterno LIKE '%$p_criterio%' OR ds_amaterno LIKE '%$p_criterio%') "; break;
        case 3: $Query .= "AND nb_programa LIKE '%$p_criterio%' "; break;
        case 4: $Query .= "AND ".ConsultaFechaBD('fe_alta', FMT_FECHA)." LIKE '%$p_criterio%' "; break;
        case 5: 
          $Query .= "AND (".ConsultaFechaBD('fe_ultacc', FMT_FECHA)." LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_HORA)." LIKE '%$p_criterio%') ";
          break;
        default:
          $Query .= "AND ( ";
          $Query .= "ds_login LIKE '%$p_criterio%' ";
          $Query .= "OR ds_nombres LIKE '%$p_criterio%' OR ds_apaterno LIKE '%$p_criterio%' OR ds_amaterno LIKE '%$p_criterio%' ";
          $Query .= "OR nb_programa LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_alta', FMT_FECHA)." LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_FECHA)." LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_HORA)." LIKE '%$p_criterio%') ";
      }
    }
    $Query .= "ORDER BY ds_login"; 
    
    # Exporta el resultado a CSV
    $nom_arch = PATH_EXPORT."/students_".date('Ymd')."_".rand(1000,9000).".csv";
    
    # Abre archivo de salida
    if(!$archivo = fopen($_SERVER[DOCUMENT_ROOT].$nom_arch, "wb")) {
      MuestraPaginaError(ERR_EXPORTAR);
      exit;
    }
    
    # Titulos de columnas del query principal
    $rs = EjecutaQuery($Query);
    $tot_campos = CuentaCampos($rs);
    for($i = 2; $i < $tot_campos; $i++)
      fwrite($archivo, getStrParaCSV(str_ascii(NombreCampo($rs, $i, True))).",");
    
    # Titulos de columnas del segundo query
    $titulos = array(
    ObtenEtiqueta(297), ObtenEtiqueta(426), ObtenEtiqueta(342), # Obtiene el grupo, el term y el maestro
    ObtenEtiqueta(280), ObtenEtiqueta(281), ObtenEtiqueta(282), ObtenEtiqueta(283), ObtenEtiqueta(284), ObtenEtiqueta(285), ObtenEtiqueta(286), # Recupera datos del aplicante: forma 1
    ObtenEtiqueta(287), ObtenEtiqueta(117),  ObtenEtiqueta(118), ObtenEtiqueta(280), ObtenEtiqueta(288), ObtenEtiqueta(287), # Recupera datos del aplicante: forma 1
    ObtenEtiqueta(342), # Datos del programa
    ObtenEtiqueta(282), ObtenEtiqueta(283), ObtenEtiqueta(284), ObtenEtiqueta(285), ObtenEtiqueta(286), ObtenEtiqueta(287),	ObtenEtiqueta(631), ObtenEtiqueta(632), ObtenEtiqueta(620), # Recupera datos adicionales a la forma 1 y del contrato del aplicante
    ObtenEtiqueta(411),	ObtenEtiqueta(414),	# Recupera datos de configuracion del alumno 
    ObtenEtiqueta(60), #Etiqueta del program start date
    ObtenEtiqueta(540), ObtenEtiqueta(541), ObtenEtiqueta(544), ObtenEtiqueta(545), ObtenEtiqueta(546), ObtenEtiqueta(547), ObtenEtiqueta(548), ObtenEtiqueta(556), ObtenEtiqueta(558)# Etiquetas de Official Transcript
    ); 
    
    $tot_tit_arreglo = count($titulos);
    for($i = 0; $i < $tot_tit_arreglo; $i++)
      fwrite($archivo,getStrParaCSV(str_ascii($titulos[$i])).",");
    
    # Fin del renglon
    fwrite($archivo, "\n");
    
    # Campos de la consulta principal
    while($row = RecuperaRegistro($rs)) {
      $fl_alumno = $row[0];
      $cl_sesion = $row[1];
      for($i = 2; $i < $tot_campos; $i++)
        fwrite($archivo, getStrParaCSV(str_ascii(DecodificaEscogeIdiomaBD($row[$i]))).","); 
      
      # Obtiene el grupo, el term y el maestro ***
      $concat = array('c.ds_nombres', "' '", 'c.ds_apaterno');
      $Query = "SELECT  ".ConcatenaBD($concat).",  nb_grupo, (SELECT no_grado FROM k_term WHERE fl_term=b.fl_term) ";
      $Query .= "FROM k_alumno_grupo a LEFT JOIN (c_grupo b LEFT JOIN c_usuario c ON b.fl_maestro = c.fl_usuario) ON a.fl_grupo = b.fl_grupo ";
      $Query .= "WHERE fl_alumno = $fl_alumno";	
      $row2 = RecuperaValor($Query);
    
      for($i = 0; $i < 3; $i++)
        fwrite($archivo, getStrParaCSV(str_ascii(DecodificaEscogeIdiomaBD($row2[$i]))).",");
        
      # Recupera datos del aplicante: forma 1
      $Query  = "SELECT a.fl_programa, ds_number, ds_alt_number, ds_add_number, ds_add_street, ds_add_city, ";
      $Query .= "CASE ds_add_country WHEN 38 THEN (SELECT ds_provincia FROM k_provincias pr WHERE pr.fl_provincia=ds_add_state) ELSE ds_add_state END ds_add_state, ds_add_zip, ";
      $Query .= "d.ds_pais, ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, e.ds_pais   ";
      $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
      $Query .= "WHERE a.fl_programa=b.fl_programa ";
      $Query .= "AND a.fl_periodo=c.fl_periodo ";
      $Query .= "AND a.ds_add_country=d.fl_pais ";
      $Query .= "AND a.ds_eme_country=e.fl_pais ";
      $Query .= "AND cl_sesion='$cl_sesion'";
      
      $row2 = RecuperaValor($Query);
      $fl_programa = $row2[0];
      for($i = 1; $i < 14; $i++)
        fwrite($archivo, getStrParaCSV(str_ascii(DecodificaEscogeIdiomaBD($row2[$i]))).",");
        
      # Datos del programa term inicial
      $Query  = "SELECT nb_periodo FROM k_term a, c_periodo b WHERE a.fl_periodo=b.fl_periodo ";
      $Query .= "AND fl_term=(SELECT MIN(fl_term) FROM k_alumno_term WHERE fl_alumno=";
      $Query .= "(SELECT fl_usuario FROM c_usuario WHERE cl_sesion='$cl_sesion'))";
       
      $row2 = RecuperaValor($Query);
      for($i = 0; $i < 1; $i++)
        fwrite($archivo, getStrParaCSV(str_ascii(DecodificaEscogeIdiomaBD($row2[$i]))).",");
      
      # Recupera datos adicionales a la forma 1 y del contrato del aplicante
      $Query  = "SELECT ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_pais, ds_p_name, ds_education_number, ";
      $Query .= "CASE WHEN fg_international='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END  ";
      $Query .= "FROM k_app_contrato a LEFT JOIN c_pais b ON a.ds_m_add_country=b.fl_pais ";
      $Query .= "WHERE cl_sesion='$cl_sesion' ";
      $Query .= "ORDER BY no_contrato";
      
      $row2 = RecuperaValor($Query);
      for($i = 0; $i < 9; $i++)
        fwrite($archivo, getStrParaCSV(str_ascii(DecodificaEscogeIdiomaBD($row2[$i]))).",");
   
      # Recupera datos de configuracion del alumno
      $concat = array('nb_zona_horaria', "' '", "'GMT'", "' ('", 'no_gmt',"')'");
      $Query  = "SELECT a.fl_zona_horaria, ".ConcatenaBD($concat).", ds_website ";
      $Query .= "FROM c_alumno a, c_zona_horaria b ";
      $Query .= "WHERE a.fl_zona_horaria=b.fl_zona_horaria ";
      $Query .= "AND fl_alumno=$fl_alumno";
      
      $row2 = RecuperaValor($Query);
      for($i = 1; $i < 3; $i++)
        fwrite($archivo, getStrParaCSV(str_ascii(DecodificaEscogeIdiomaBD($row2[$i]))).",");
      
      # Recupera el program start date 
      $Query  = "SELECT nb_periodo ";
      $Query .= "FROM k_term b, c_periodo c, k_alumno_term d ";
      $Query .= "WHERE b.fl_periodo=c.fl_periodo ";
      $Query .= "AND b.fl_term=d.fl_term AND d.fl_alumno='$fl_alumno' ";
      $Query .= "AND no_grado=1 ";
      $row2 = RecuperaValor($Query);
      $fe_inicio = $row2[0]; 
      
      $row2 = RecuperaValor($Query);
      for($i = 0; $i < 1; $i++)
        fwrite($archivo, getStrParaCSV(str_ascii(DecodificaEscogeIdiomaBD($row2[$i]))).",");
      
      # Recupera datos de Official Transcript
      $Query 	= "SELECT ".ConsultaFechaBD('fe_carta', FMT_FECHA).", ".ConsultaFechaBD('fe_contrato', FMT_FECHA).", ".ConsultaFechaBD('fe_fin', FMT_FECHA).", ";
      $Query .= ConsultaFechaBD('fe_completado', FMT_FECHA).", ".ConsultaFechaBD('fe_emision', FMT_FECHA).", ";
      $Query .= "CASE WHEN fg_certificado='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END , ";
      $Query .= "CASE WHEN fg_honores='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END , ";
      $Query .= ConsultaFechaBD('fe_graduacion', FMT_FECHA).", ";
      $Query .= "CASE WHEN fg_desercion='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END  ";
      $Query .= "FROM k_pctia ";
      $Query .= "WHERE fl_alumno=$fl_alumno ";
      $Query .= "AND fl_programa=$fl_programa ";
       
      $row2 = RecuperaValor($Query);
      for($i = 0; $i < 9; $i++)
        fwrite($archivo, getStrParaCSV(str_ascii(DecodificaEscogeIdiomaBD($row2[$i]))).",");

      # Fin del renglon
      fwrite($archivo, "\n");
    }
    
    # Cierra el archivo
    fclose($archivo);
    
    # Descarga el archivo
    header("Location: $nom_arch");
  
  }
	
	
?>