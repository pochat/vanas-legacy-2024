<?php 

	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  $fl_maestro_pago = RecibeParametroNumerico('fl_maestro_pago');
  $month = RecibeParametroHTML('month');
  if(strlen($month) == 1) $fe_month = '0'.$month; else $fe_month = $month;
  $fe_year = RecibeParametroHTML('year');
  $fe_periodo = $fe_month."-".$fe_year;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Funcion para quitar caracteres especiales y saltos de linea
  function getStrParaCSV($str) {
    $str_aux = $str;
    $str_aux = str_replace(",", " ", $str_aux);
    $str_aux = str_replace("\n", " ", $str_aux);
    $str_aux = str_replace("\r", " ", $str_aux);
    
    return $str_aux;  
  }
   
  # Exporta el resultado a CSV
  $nom_arch = PATH_ADM."/export/teacher_timesheet_".date('dmY')."_".rand(1000,9000).".csv";
  
  # Abre archivo de salida
  if(!$archivo = fopen($_SERVER['DOCUMENT_ROOT']."/".$nom_arch, "wb")) {
    MuestraPaginaError(ERR_EXPORTAR);
    exit;
  }
  
  # Encabezado
  $titulos = array(ObtenEtiqueta(718),ObtenEtiqueta(716),ObtenEtiqueta(717),ObtenEtiqueta(719),ObtenEtiqueta(720),ObtenEtiqueta(721),
  ObtenEtiqueta(722),ObtenEtiqueta(735),ObtenEtiqueta(723),ObtenEtiqueta(727),ObtenEtiqueta(724));
  $tot_tit_arreglo = count($titulos);
  for($i = 0; $i < $tot_tit_arreglo; $i++)
    fwrite($archivo,getStrParaCSV(str_ascii($titulos[$i])).",");
  # Fin del renglon
  fwrite($archivo, "\n");
  
  #Datos automaticos
  $Query  = "SELECT ".ConsultaFechaBD('d.fe_clase', FMT_FECHA).",no_semana, ds_titulo, CASE d.fg_adicional WHEN '0' THEN '".ObtenEtiqueta(714)."' ELSE '".ObtenEtiqueta(715)."' END ds_descripion, ";
  $Query .= "a.nb_grupo, e.nb_programa,(SELECT nb_periodo FROM c_periodo j WHERE j.fl_periodo=f.fl_periodo) nb_periodo, ";
  $Query .= "CASE a.no_alumnos WHEN 0 
    THEN (SELECT COUNT(1) FROM k_alumno_historia f, c_usuario e WHERE f.fl_grupo=a.fl_grupo AND f.fl_alumno=e.fl_usuario AND e.fg_activo='1')
    ELSE a.no_alumnos END no_alumnos, ";
  $Query .= "CASE d.fg_adicional WHEN '0' THEN IFNULL((SELECT t.mn_lecture_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_usuario),e.mn_lecture_fee) ";
  $Query .= "ELSE IFNULL((SELECT t.mn_extra_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_usuario),e.mn_extra_fee) END hourly_rate ";
  $Query .= ",a.fl_grupo,e.fl_programa,d.fl_clase,f.no_grado  ";
  $Query .= "FROM c_grupo a, k_clase d, c_programa e, k_term f ,k_semana b LEFT JOIN c_leccion c ON(c.fl_leccion=b.fl_leccion) ";
  $Query .= "WHERE a.fl_term = b.fl_term AND a.fl_grupo=d.fl_grupo AND b.fl_semana=d.fl_semana AND c.fl_programa = e.fl_programa ";
  $Query .= "AND c.fl_programa=e.fl_programa AND a.fl_term = f.fl_term AND b.fl_term = f.fl_term AND DATE_FORMAT(d.fe_clase,'%m-%Y')='".$fe_periodo."' ";
  $Query .= "AND a.fl_maestro=$fl_usuario ";
  # El grupo debe tener estudiantes
  //$Query .= "AND (SELECT COUNT(1) FROM k_alumno_grupo f, c_usuario e WHERE f.fl_grupo=a.fl_grupo AND f.fl_alumno=e.fl_usuario AND e.fg_activo='1')>0 ";
  $Query .= "ORDER BY d.fe_clase ";
  $rs = EjecutaQuery($Query);
  for($i=0;$row=RecuperaRegistro($rs);$i++){
      
      $no_alumnos=$row[7];
      $amount = $row[8]*1;
      
      
      # Si el periodo de ese grupo ya no esta activado muestra los registros  aunque no tengan alumnos
      $Query0  = "SELECT fg_activo  FROM c_grupo gr JOIN k_term ter ON(ter.fl_term=gr.fl_term) ";
      $Query0 .= "JOIN c_periodo per ON(per.fl_periodo=ter.fl_periodo) WHERE gr.fl_grupo=".$row[9]." ";
      $row0 = RecuperaValor($Query0);
      $periodo_activo = $row0[0];
      
      
      
      if((!empty($no_alumnos) AND !empty($periodo_activo) OR (empty($no_alumnos) AND empty($periodo_activo)) || (!empty($no_alumnos) AND empty($periodo_activo)))){
      
      if(!empty($fl_maestro_pago)){
        $row1 = RecuperaValor("SELECT p.mn_tarifa_hr, p.mn_subtotal FROM k_maestro_pago_det p WHERE p.fg_tipo='A' AND p.fl_maestro_pago=$fl_maestro_pago AND p.ds_concepto=".$row[11]."");
        $hourly_rate = number_format($row1[0],2,'.',',');
        $amount = $row1[1];
      }
      
      if($amount<=0)
      {
      
      }else{
      
      fwrite($archivo,getStrParaCSV(str_ascii($row[0])).",");
      fwrite($archivo,getStrParaCSV(str_ascii($row[1])).",");
      fwrite($archivo,getStrParaCSV(str_ascii($row[2])).",");
      fwrite($archivo,getStrParaCSV(str_ascii($row[3])).",");
      fwrite($archivo,getStrParaCSV(str_ascii($row[4])).",");
      fwrite($archivo,getStrParaCSV(str_ascii($row[5])).",");
      fwrite($archivo,getStrParaCSV(str_ascii($row[6])).",");
      fwrite($archivo,getStrParaCSV(str_ascii($row[7])).",");
      fwrite($archivo,getStrParaCSV($row[8]).",");
      fwrite($archivo,getStrParaCSV(1).",");
      fwrite($archivo,getStrParaCSV($amount).",");   
      # Fin del renglon
      fwrite($archivo, "\n");
      
      
      }
      
      
      
  } 
      
  }
  
  # Muestra las sessiones de las clases globales
  # monto por default
  $mn_cglobal_fee = ObtenConfiguracion(96);
  $Querycg  = "SELECT kcg.no_orden, kcg.ds_titulo, ".ConsultaFechaBD('kcg.fe_clase', FMT_FECHA).", 'Global Class' ds_descripion, cg.ds_clase ds_clase_global, ";
  $Querycg .= "IFNULL((SELECT kmt.mn_cglobal_fee FROM k_maestro_tarifa_cg kmt WHERE kmt.fl_clase_global=cg.fl_clase_global AND kmt.fl_maestro=kcg.fl_maestro), ";
  $Querycg .= "'".$mn_cglobal_fee."') mn_cglobal_fee, cg.fl_clase_global, cg.no_alumnos,  kcg.fl_clase_cg ";
  $Querycg .= "FROM c_clase_global cg ";
  $Querycg .= "LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=cg.fl_clase_global AND kcg.fl_maestro=$fl_usuario) ";
  $Querycg .= "WHERE DATE_FORMAT(kcg.fe_clase,'%m-%Y')='".$fe_periodo."'";
  $rcg = EjecutaQuery($Querycg);
  $tot_aut_cg = CuentaRegistros($rcg);
  $tot_aut = $tot_aut_nor + $tot_aut_cg;
  for($j=0;$row=RecuperaRegistro($rcg);$j++){
    $no_orden = $row[0];
    $ds_titulo = $row[1];
    $fe_clase = $row[2];
    $ds_descripion = $row[3];
    $ds_clase_global = $row[4];
    $nb_programa_cg = "";
    $nb_periodo_cg = "";
    $mn_cglobal_fee = $row[5];    
    $amount_sp = $mn_cglobal_fee*1;    
    $fl_clase_global = $row[6];
    $no_alumnos = $row[7];
    $fl_clase_cg = $row[8];
    
    # Si alguna clase ya esta registrada con la clase global
    # el monto de esa clase sera el de la BD 
    $Query2  = "SELECT mn_subtotal, fg_subtract_class, fl_maestro_pago_det FROM k_maestro_pago_det ";
    $Query2 .= "WHERE fg_tipo='ACG' AND fl_grupo=".$fl_clase_global." AND ds_concepto='".$fl_clase_cg."'";
    $row_sub2 = RecuperaValor($Query2);
    if(!empty($row_sub2[2])){
      $mn_cglobal_fee = $row_sub2[0];
      $amount_sp = $row_sub2[0];      
      if(!empty($row_sub2[1]))
        $checked = "checked";
      else
        $checked = " ";
      $fl_maestro_pago_det = $row_sub2[2];
    }
    
    $total_aut_sp += $amount_sp;
    fwrite($archivo,getStrParaCSV(str_ascii($fe_clase)).",");
    fwrite($archivo,getStrParaCSV(str_ascii($no_orden)).",");
    fwrite($archivo,getStrParaCSV(str_ascii($ds_titulo)).",");
    fwrite($archivo,getStrParaCSV(str_ascii($ds_descripion)).",");
    fwrite($archivo,getStrParaCSV(str_ascii($ds_clase_global)).",");
    fwrite($archivo,getStrParaCSV(str_ascii($nb_programa_cg)).",");
    fwrite($archivo,getStrParaCSV(str_ascii($no_term_sp)).",");
    fwrite($archivo,getStrParaCSV(str_ascii($no_alumnos)).",");
    fwrite($archivo,getStrParaCSV($mn_cglobal_fee).",");
    fwrite($archivo,getStrParaCSV(1).",");
    fwrite($archivo,getStrParaCSV($amount_sp).",");   
    # Fin del renglon
    fwrite($archivo, "\n");    
  }
  # Fin de las clases Globales

  #Recupera datos de las clases grupales globales.
  $Querycg  = "
						  SELECT  b.no_semana,a.nb_clase ds_titulo,".ConsultaFechaBD('a.fe_clase', FMT_FECHA).",
							'Group Class',''mn_clase,a.fl_clase_grupo,(SELECT COUNT(*) FROM k_alumno_grupo d WHERE d.fl_grupo=c.fl_grupo )no_alumnos,c.fl_grupo,c.nb_grupo
						 FROM k_clase_grupo a
						 JOIN k_semana_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo
						 JOIN c_grupo c ON c.fl_grupo=a.fl_grupo
						 WHERE a.fl_maestro=$fl_usuario ";
  $Querycg .="AND DATE_FORMAT(a.fe_clase,'%m-%Y')='".$fe_periodo."' ";
  $rgg = EjecutaQuery($Querycg);
  $tot_aut_gg = CuentaRegistros($rgg);	
  $rg = EjecutaQuery($Querycg);
  $total_gg=0;
  for($gg=0;$row=RecuperaRegistro($rgg);$j++){     
      $no_orden = $row[0];
      $ds_titulo = $row[1];
      $fe_clase = $row[2];
      $ds_descripion = $row[3];
      $ds_clase_grupal =$row['nb_grupo'];
      $fl_clase_grupal = $row[5];
      $no_alumnos = $row[6];
      $fl_grupo = $row[7];


      #Recupermaos todos los periodos que incluye el programa.
      $concat = array('nb_programa', "' ('", 'ds_duracion', "')'", "' - '", 'nb_periodo', "' - ".ObtenEtiqueta(375)." '", 'no_grado');
      $Querysv="SELECT a.fl_term,a.fl_grupo,b.fl_programa,b.fl_periodo,".ConcatenaBD($concat)." 'nb_term',nb_programa,no_grado   
							FROM k_grupo_term a
							JOIN k_term b ON a.fl_term=b.fl_term 
							JOIN c_programa c ON c.fl_programa=b.fl_programa
							JOIN c_periodo d ON d.fl_periodo=b.fl_periodo

							WHERE a.fl_grupo=$fl_grupo  ";
      $rsm=EjecutaQuery($Querysv);
      $total_terms=CuentaRegistros($rsm);
      $periodos="";$lessons="";$no_grados="";
      for($im=1;$im<$rowm=RecuperaRegistro($rsm);$im++){
          $fl_terms_i = $rowm[0];        
          $periodos.=$rowm[4]."<br>";
          $lessons.=$rowm['nb_programa']."-";
          $no_grados.=$rowm['no_grado']."-";


      }
      
      #Recupermaos la tarfa impuesta =
      $Query="SELECT mn_cgrupo FROM k_maestro_tarifa_gg WHERE fl_maestro=$fl_usuario AND fl_clase_grupo=$fl_clase_grupal ";
      $mn=RecuperaValor($Query);
      $mn_tarif=$mn[0];

      fwrite($archivo,getStrParaCSV(str_ascii($fe_clase)).",");
      fwrite($archivo,getStrParaCSV(str_ascii($no_orden)).",");
      fwrite($archivo,getStrParaCSV(str_ascii($lessons)).",");
      fwrite($archivo,getStrParaCSV(str_ascii($ds_descripion)).",");
      fwrite($archivo,getStrParaCSV(str_ascii($ds_clase_grupal)).",");
      fwrite($archivo,getStrParaCSV(str_ascii($ds_titulo)).",");
      fwrite($archivo,getStrParaCSV(str_ascii($periodos)).",");
      fwrite($archivo,getStrParaCSV(str_ascii($no_grados)).",");
      fwrite($archivo,getStrParaCSV(str_ascii($mn_tarif)).",");
      fwrite($archivo,getStrParaCSV(str_ascii('')).",");
      fwrite($archivo,getStrParaCSV(str_ascii($mn_tarif)).",");
      # Fin del renglon
      fwrite($archivo, "\n");






  }







  #Fin de las clases grupales globales
  # Datos Mauales
  $Query = "SELECT ds_concepto, CONCAT('$',' ',mn_tarifa_hr),no_horas ,CONCAT('$',' ',mn_subtotal) FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago AND fg_tipo='M'";
  $rs = EjecutaQuery($Query);
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    fwrite($archivo,getStrParaCSV(str_ascii('')).",");
    fwrite($archivo,getStrParaCSV(str_ascii('')).",");
    fwrite($archivo,getStrParaCSV(str_ascii('')).",");
    fwrite($archivo,getStrParaCSV(str_ascii($row[0])).",");
    fwrite($archivo,getStrParaCSV(str_ascii('')).",");
    fwrite($archivo,getStrParaCSV(str_ascii('')).",");
    fwrite($archivo,getStrParaCSV(str_ascii('')).",");
    fwrite($archivo,getStrParaCSV(str_ascii('')).",");
    fwrite($archivo,getStrParaCSV(str_ascii($row[1])).",");
    fwrite($archivo,getStrParaCSV(str_ascii($row[2])).",");
    fwrite($archivo,getStrParaCSV(str_ascii($row[3])).",");
    # Fin del renglon
    fwrite($archivo, "\n");
  }
  
  # Cierra el archivo
  fclose($archivo);

  # Descarga el archivo
  header("Location: $nom_arch");
  
?>