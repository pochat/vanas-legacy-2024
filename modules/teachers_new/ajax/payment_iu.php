<?php 

	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  $fl_maestro_pago = RecibeParametroNumerico('fl_maestro_pago',True,False);
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Obtenemos los datos del mes que selecciono
  $row = RecuperaValor("SELECT DATE_FORMAT(fe_periodo,'%M, %Y'), mn_total, fg_pagado, fe_pagado, DATE_FORMAT(fe_periodo,'%m-%Y') periodo  FROM k_maestro_pago WHERE fl_maestro_pago=$fl_maestro_pago");
  $fe_periodo = $row[0];
  $mn_total  = $row[1];
  $fg_pagado = $row[2];
  $fe_pagado = $row[3];  
  $periodo = $row[4];

  #Recuperamos si tiene clases extras.
  $Query="SELECT SUM(mn_total) FROM k_maestro_pago a WHERE fl_maestro=$fl_usuario AND fg_publicar='1' AND DATE_FORMAT(fe_periodo,'%m-%Y')='".$periodo."'  ";
  $rop=RecuperaValor($Query);
  if($rop[0]){
      $mn_total=$rop[0];
  }
  /*por x razon desconocida en algunos casos no lo muetsra y en div coloca un opaciti 0 lo cual no se ve nada agrego este script para que si se viusalize la informacion.*/
  echo"<script>document.getElementById('content').style.opacity = '1';
  </script>";

  # Si no recibe fl_maestro_pago recibe periodo y se busca las clases del mes recibido
  if(empty($fl_maestro_pago))  {
    
    $periodo = RecibeParametroHTML('periodo',False,True);
    $Query  = "SELECT CASE w.fg_obligatorio WHEN '1' THEN IFNULL((SELECT u.mn_lecture_fee FROM k_maestro_tarifa u WHERE u.fl_grupo=w.fl_grupo AND u.fl_programa=y.fl_programa AND r.fl_maestro=u.fl_maestro),y.mn_lecture_fee)  END 'mn_lecture_fee', ";
    $Query .= "CASE w.fg_adicional WHEN '1' THEN IFNULL((SELECT u.mn_extra_fee FROM k_maestro_tarifa u WHERE u.fl_grupo=w.fl_grupo AND u.fl_programa=y.fl_programa AND r.fl_maestro=u.fl_maestro ),y.mn_extra_fee)  END 'mn_extra_fee' ";
    $Query .= "FROM k_clase  w, c_grupo r, k_term t, c_programa y WHERE w.fl_grupo=r.fl_grupo AND r.fl_maestro=$fl_usuario AND r.fl_term = t.fl_term AND t.fl_programa = y.fl_programa ";
    $Query .= "AND DATE_FORMAT(fe_clase,'%m-%Y')='".$periodo."' order by w.fe_clase ";
    $rs = EjecutaQuery($Query);
    for($i=0;$row1 = RecuperaRegistro($rs);$i++){
      $obli += $row1[0];
      $adic += $row1[1];
      $mn_total =$obli + $adic;
    }
  }
  
  # Buscamos que existan algunos clases que no impartio
  $row_substract = RecuperaValor("SELECT COUNT(*)FROM k_maestro_pago_det WHERE fg_subtract_class='0' AND fl_maestro_pago=$fl_maestro_pago");
  $classzero = $row_substract[0];
 
  # Obtenemos el mes y anio del periodo seleccionado
  $month = substr($periodo,0,2);
  $year = substr($periodo,3);
  
  # invoice para el maestro
  echo "
  <div class='row'>
    <div class='col-xs-12'>
    <div class='well well-light padding-10'>
      <div class='row'>
        <div class='col-xs-12'> 
          <div class='row'>
            <br />
            <div class='col-sm-8'>
              <address><strong>";
              # Datos del teacher
              $Query  = "SELECT CONCAT(ds_nombres, ' ', ds_apaterno, ' ', IFNULL(ds_amaterno, '') ) ds_nombre, ds_pais, a.ds_email ";
              $Query .= "FROM c_usuario a,c_maestro b, c_pais c WHERE  a.fl_usuario=b.fl_maestro  AND b.fl_pais = c.fl_pais AND a.fl_usuario=$fl_usuario "; 
              $row = RecuperaValor($Query);
              echo $ds_nombres = str_texto($row[0])."<br />".
              $ds_pais = str_texto($row[1])."<br />".
              $ds_email = str_texto($row[2])."<br />";
              echo
              "</strong>
              </address>
            </div>
            <div class='col-sm-4 margin-left-20'>";
              echo "
              <div>
                <div class='font-md'>
                  <strong>".ObtenEtiqueta(713).":</strong>
                  <span class='pull-right'> <i class='fa fa-calendar'></i>&nbsp;&nbsp;".$fe_periodo."</span>
                </div>
              </div>
              <br>
              <div class='well well-sm txt-color-white no-border' style='background-color:#0071BD;'>
                <div class='fa-lg'>
                  ".ObtenEtiqueta(736)."
                  <span class='pull-right' id='amount_total'>".number_format($mn_total,2,'.',',')." ".ObtenEtiqueta(737)."</span>
                </div>                
              </div>
              <div class='fa-lg text-align-right'>
                ".ObtenEtiqueta(26)."&nbsp;<a href='JavaScript:exportar($fl_maestro_pago,$month,$year);'><image src='".PATH_ADM_IMAGES."/icon_excel.png' title='Export'></a>
                <script>
                  function exportar(fl_maestro_pago,month,year){
                    document.export.fl_maestro_pago.value  = fl_maestro_pago;
                    document.export.month.value  = month;
                    document.export.year.value  = year;
                    document.export.action = '".PATH_N_MAE_PAGES."/payment_exp.php';
                    document.export.submit();
                  }                  
                </script>
                <form name=export method=post>
                  <input type=hidden name=fl_maestro_pago>
                  <input type=hidden name=month>
                  <input type=hidden name=year>
                </form>
              </div>
              <br>
            </div>          
          </div>        
          <div class='well well-light no-margin no-padding'>
            <div class='well well-light no-margin padding-10'>";
              # Encabezado de la tabla datos automaticos
              echo "
              <table class='table table-hover'>
                <thead>
                  <tr>
                  <th align='center'>".ObtenEtiqueta(718)."</th>
                  <th align='center'>".ObtenEtiqueta(716)."</th>
                  <th align='center'>".ObtenEtiqueta(717)."</th>
                  <th align='center'>".ObtenEtiqueta(719)."</th>
                  <th align='center'>".ObtenEtiqueta(720)."</th>
                  <th align='center'>".ObtenEtiqueta(721)."</th>
                  <th align='center'>".ObtenEtiqueta(722)."</th>
                  <th align='center'>".ObtenEtiqueta(807)."</th>
                  <th align='center'>".ObtenEtiqueta(735)."</th>
                  <th align='center'>Attendance</th>
                  <th align='center'>".ObtenEtiqueta(723)."</th>
                  <th align='center'>".ObtenEtiqueta(724)."</th>
                  </tr>
                </thead>
                <tbody>";

                # Obtenemos los grupos que imparte el maestro en el periodo que se selecciona
                $Query  = "SELECT no_semana, ds_titulo,".ConsultaFechaBD('d.fe_clase', FMT_FECHA).", CASE d.fg_adicional WHEN '0' THEN '".ObtenEtiqueta(714)."' ELSE '".ObtenEtiqueta(715)."' END ds_descripion, ";
                $Query .= "a.nb_grupo, e.nb_programa,(SELECT nb_periodo FROM c_periodo j WHERE j.fl_periodo=f.fl_periodo) nb_periodo, ";
                $Query .= "CASE d.fg_adicional WHEN '0' THEN IFNULL((SELECT t.mn_lecture_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_usuario),e.mn_lecture_fee) ";
                $Query .= "ELSE IFNULL((SELECT t.mn_extra_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_usuario),e.mn_extra_fee) END hourly_rate ";
                $Query .= ",a.fl_grupo,e.fl_programa,CASE a.no_alumnos WHEN 0 
                THEN (SELECT COUNT(1) FROM k_alumno_historia f, c_usuario e WHERE f.fl_grupo=a.fl_grupo AND f.fl_alumno=e.fl_usuario AND e.fg_activo='1')
                ELSE a.no_alumnos END no_alumnos,d.fl_clase,f.no_grado,d.fl_clase  ";
                $Query .= "FROM c_grupo a, k_clase d, c_programa e, k_term f ,k_semana b LEFT JOIN c_leccion c ON(c.fl_leccion=b.fl_leccion) ";
                $Query .= "WHERE a.fl_term = b.fl_term AND a.fl_grupo=d.fl_grupo AND b.fl_semana=d.fl_semana AND c.fl_programa = e.fl_programa ";
                $Query .= "AND c.fl_programa=e.fl_programa AND a.fl_term = f.fl_term AND b.fl_term = f.fl_term AND DATE_FORMAT(d.fe_clase,'%m-%Y')='".$periodo."' ";
                $Query .= "AND a.fl_maestro=$fl_usuario ";
                # El grupo debe tener estudiantes
                //$Query .= "AND (SELECT COUNT(1) FROM k_alumno_grupo f, c_usuario e WHERE f.fl_grupo=a.fl_grupo AND f.fl_alumno=e.fl_usuario AND e.fg_activo='1')>0 ";
                $Query .= "ORDER BY d.fe_clase ";
                $rs = EjecutaQuery($Query);
                $tot_aut = CuentaRegistros($rs);
                # Initialization of variables to prevent error (variable undefined)
                $total_aut=0;
                $tot_aut_nor=0;
                $total_aut_sp=0;
                $total_gg=0;
                $clase = '';
                $amount=0;
                $hourly_rate=0;
                for($i=0;$row=RecuperaRegistro($rs);$i++){
                  $no_semana = $row[0];
                  $ds_titulo = $row[1];
                  $fe_clase = $row[2];
                  $ds_descripion = $row[3];
                  $nb_grupo = $row[4];
                  $nb_programa = $row[5];
                  $nb_periodo = $row[6];
                  $hourly_rate = $row[7];
                  $fl_clase=$row['fl_clase'];

                  if(!empty($hourly_rate)){
                  $amount = $hourly_rate*1;
                  }else{
                     $amount=0; 
                  }
                  $no_alumnos = $row[10];
                  
                  $fl_clase = $row[11];
                  $no_grado = $row[12];
                  # Si el periodo de ese grupo ya no esta activado muestra los registros  aunque no tengan alumnos
                  $Query0  = "SELECT fg_activo  FROM c_grupo gr JOIN k_term ter ON(ter.fl_term=gr.fl_term) ";
                  $Query0 .= "JOIN c_periodo per ON(per.fl_periodo=ter.fl_periodo) WHERE gr.fl_grupo=".$row[8]." ";
                  $row0 = RecuperaValor($Query0);
                  $periodo_activo = $row0[0];                  
                  if((!empty($no_alumnos) AND !empty($periodo_activo) OR (empty($no_alumnos) AND empty($periodo_activo)) || (!empty($no_alumnos) AND empty($periodo_activo)))){                    
                  if(!empty($fl_maestro_pago)){
                    //$row1 = RecuperaValor("SELECT p.mn_tarifa_hr, p.mn_subtotal FROM k_maestro_pago_det p WHERE p.fg_tipo='A' AND p.fl_maestro_pago=$fl_maestro_pago AND p.ds_concepto=$fl_clase");
                    #En backend esta este query , lo pase al front.
                      $row1 = RecuperaValor("SELECT p.mn_tarifa_hr, p.mn_subtotal FROM k_maestro_pago_det p WHERE p.fg_tipo='A' AND fl_grupo=".$row['fl_grupo']." AND p.ds_concepto='$fl_clase' ");
            
                      $hourly_rate = number_format($row1[1],2,'.',',');
                      $amount = $row1[1];


                      #Recuperamos la tarifa de la clase:
                      $Query="SELECT mn_rate FROM k_clase WHERE fl_clase=$fl_clase ";
                      $rowt=RecuperaValor($Query);
                      $hourly_rate=number_format($rowt['mn_rate'],2,'.',',');
                      $amount=$rowt['mn_rate'];

                      if(empty($hourly_rate)){
                      #esta tarifa defibe el default de los precios que vienen desde detalle de los teacher  mjd*/
                      $Queryt="SELECT mn_hour_rate FROM c_maestro WHERE fl_maestro=$fl_maestro ";
                      $rot=RecuperaValor($Queryt);
                      $mn_tarifa_default=$rot['mn_hour_rate'];

                      $hourly_rate = number_format($mn_tarifa_default,2,'.',',');
                      $amount=$mn_tarifa_default;
                      }




                  }


					if(($fl_usuario==584)&&($row['fl_grupo']==1126)){
	
						$amount=80;
						$hourly_rate=80;

					
					}

				  
                  $total_aut += $amount;
                  # tachamos el registro que no haya partido el teacher
                  if($amount<=0)
                    echo "<style>#tr_$i{text-decoration: line-through;}</style>";

                  $Qclase = RecuperaValor("SELECT fl_live_session FROM k_live_session WHERE fl_clase=$fl_clase");

                  $fl_live_session = $Qclase['fl_live_session'];

                  $Qassist = RecuperaValor("SELECT cl_estatus_asistencia FROM k_live_session_asistencia WHERE fl_live_session=$fl_live_session AND fl_usuario=$fl_usuario");

                  $statusAsistencia = !empty($Qassist[0])?$Qassist[0]:NULL;
                  switch ($statusAsistencia) {
                    case '1':
                      $attendance="<small class='text-danger'><i>Absent</i></small>";
                      break;
                    case '2':
                      $attendance="<small class='text-success'><i>Present</i></small>";
                      break;
                    case '3':
                      $attendance="<small class='text-warning'><i>Late</i></small>";
                      break;
                    default:
                      $attendance="<small class='text-danger'><i>Absent</i></small>";
                      break;
                  }
                  
                  echo "
                  <tr class='$clase' id='tr_$i'>
                    <td>".$fe_clase."</td>
                    <td align='center'>".$no_semana."</td>
                    <td>".$ds_titulo."</td>   
                    <td>".$ds_descripion."</td>
                    <td>".$nb_grupo."</td>
                    <td>".$nb_programa."</td>
                    <td>".$nb_periodo."</td>
                    <td>".$no_grado."</td>
                    <td align='center'>".$no_alumnos."</td>
                    <td align='center'>".$attendance."</td>
                    <td align='center'>$ ".number_format($hourly_rate,2,'.',',')."</td>
                    <td align='center'>$ ".number_format($amount,2,'.',',')."</td>
                  </tr>";
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
                $Querycg .= "WHERE DATE_FORMAT(kcg.fe_clase,'%m-%Y')='".$periodo."'";
                $rcg = EjecutaQuery($Querycg);
                $tot_aut_cg = CuentaRegistros($rcg);
                $tot_aut = $tot_aut_nor + $tot_aut_cg;
                for($j=0;$row=RecuperaRegistro($rcg);$j++){
                  $no_orden = $row[0];
                  $ds_titulo = $row[1];
                  $fe_clase = $row[2];
                  $ds_descripion = $row[3];
                  $ds_clase_global = $row[4];
                  $nb_programa_sp = "";
                  $nb_periodo_sp = "";
                  $mn_cglobal_fee = $row[5];    
                  $amount_sp = $mn_cglobal_fee*1;    
                  $fl_clase_global = $row[6];
                  $no_alumnos = $row[7];
                  $fl_clase_cg = $row[8];
                  

                  #Recuperamos la tarifa de la clase:
                  $Query="SELECT mn_rate FROM k_clase_cg WHERE fl_clase_cg=$fl_clase_cg ";
                  $rowt=RecuperaValor($Query);
                  $mn_cglobal_fee=$rowt['mn_rate'];
                  $amount_sp=$rowt['mn_rate'];


                  # Si alguna clase ya esta registrada con la clase global
                  # el monto de esa clase sera el de la BD 
                  $Query2  = "SELECT mn_subtotal, fg_subtract_class, fl_maestro_pago_det FROM k_maestro_pago_det ";
                  $Query2 .= "WHERE fg_tipo='ACG' AND fl_grupo=".$fl_clase_global." AND ds_concepto='".$fl_clase_cg."'";
                  $row_sub2 = RecuperaValor($Query2);
                  if(!empty($row_sub2[2])){
                    if(empty($mn_cglobal_fee))
                    $mn_cglobal_fee = $row_sub2[0];
                    if(empty($amount_sp))
                    $amount_sp = $row_sub2[0];      
                    if(!empty($row_sub2[1]))
                      $checked = "checked";
                    else
                      $checked = " ";
                    $fl_maestro_pago_det = $row_sub2[2];
                  }
				  
			    if( ($fl_usuario==46) && ( ($fl_clase_cg >= 1497) && ($fl_clase_cg<=1500)) ){
					 $mn_cglobal_fee =65;
					 $amount_sp =65;

				}
				 
				if( ($fl_usuario==46) && ( ($fl_clase_cg >= 1509) && ($fl_clase_cg<=1516)) ){
					 $mn_cglobal_fee =30;
					 $amount_sp =30;

				}
				  
				  				 
				  
				  
                  
                  $total_aut_sp += $amount_sp;
                  if($i % 2 == 0)
                    $clase = "css_tabla_detalle";
                  else
                    $clase = "css_tabla_detalle_bg";

                  $Qclase_cg = RecuperaValor("SELECT `fl_live_session_cg` FROM `k_live_sesion_cg` WHERE `fl_clase_cg`=$fl_clase_cg ");

                  $fl_live_session_cg = $Qclase_cg['fl_live_session_cg'];

                  $Qassist_cg = RecuperaValor("SELECT cl_estatus_asistencia_cg FROM k_live_session_asistencia_cg WHERE fl_live_session_cg=$fl_live_session_cg AND fl_usuario=$fl_usuario");

                  $statusAsistencia_cg=!empty($Qassist_cg[0])?$Qassist_cg[0]:NULL;

                  switch ($statusAsistencia_cg) {
                    case '1':
                      $attendance_cg="<small class='text-danger'><i>Absent</i></small>";
                      break;
                    case '2':
                      $attendance_cg="<small class='text-success'><i>Present</i></small>";
                      break;
                    case '3':
                      $attendance_cg="<small class='text-warning'><i>Late</i></small>";
                      break;
                    default:
                      $attendance_cg="<small class='text-danger'><i>Absent</i></small>";
                      break;
                  }

                  echo "
                  <tr class='$clase'>
                    <td>".$fe_clase."</td>
                    <td align='center'>".$no_orden."</td>
                    <td>".$ds_titulo."</td>   
                    <td>".$ds_descripion."</td>
                    <td>".$ds_clase_global."</td>
                    <td>".$nb_programa_sp."</td>
                    <td>".$nb_periodo_sp."</td>
                    <td>".$no_term_sp."</td>
                    <td align='center'>".$no_alumnos."</td>
                    <td align='center'>".$attendance_cg."</td>
                    <td align='center'>$ ".number_format($mn_cglobal_fee,2,'.',',')."</td>
                    <td align='center'>$ ".number_format($amount_sp,2,'.',',')."</td>      
                  </tr>";                  
                }
                echo "<tr style='color:#0071BD;'><td colspan=10 class='text-right'><strong>".ObtenEtiqueta(726).": $ ".number_format($total_aut + $total_aut_sp,2,'.',',')."</strong></td></tr>
                </tbody>
              </table>";
                $mn_total_primer_tabla=$total_aut + $total_aut_sp;
			  
			#Inicia tabla para pagos de clases grupales.
			#Presntamos las fechas y montos para las clases grupales.
			$Querycg  = "
						  SELECT  b.no_semana,a.nb_clase ds_titulo,".ConsultaFechaBD('a.fe_clase', FMT_FECHA).",
							'Group Class',''mn_clase,a.fl_clase_grupo,(SELECT COUNT(*) FROM k_alumno_grupo d WHERE d.fl_grupo=c.fl_grupo )no_alumnos,c.fl_grupo,c.nb_grupo
						 FROM k_clase_grupo a
						 JOIN k_semana_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo
						 JOIN c_grupo c ON c.fl_grupo=a.fl_grupo
						 WHERE a.fl_maestro=$fl_usuario ";
			$Querycg .="AND DATE_FORMAT(a.fe_clase,'%m-%Y')='".$periodo."' ";
			$rgg = EjecutaQuery($Querycg);
			$tot_aut_gg = CuentaRegistros($rgg);	
			$rg = EjecutaQuery($Querycg);
			  
			if($tot_aut_gg>0){ 
			  
			  
			  echo "
                <br />
                <br />
                <table class='table table-hover'>
                  <thead>
                    <tr>
                      <th align='center'>".ObtenEtiqueta(718)."</th>
                      <th class='text-center'>".ObtenEtiqueta(716)."</th>
                      <th align='center'>".ObtenEtiqueta(717)."</th>                    

                      <th class='text-center'>".ObtenEtiqueta(719)."</th>
					  <th class='center'>".ObtenEtiqueta(720)."</th>
					  <th class='text-center'>".ObtenEtiqueta(721)." </th>
					  <th class='text-center'>".ObtenEtiqueta(722)."</th>

                      <th align='center'>".ObtenEtiqueta(807)."</th>
					  <th class='text-center'>".ObtenEtiqueta(735)."</th>
            <th align='center'>Attendance</th>
                      <th align='center'>".ObtenEtiqueta(723)."</th>
					  <th class='text-center'>".ObtenEtiqueta(724)."</th>


 
              


                    </tr>
                  </thead>
                  <tbody>";
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
					

                  #Recuperamos la tarifa de la clase:
                  $Query="SELECT mn_rate FROM k_clase_grupo WHERE fl_clase_grupo=$fl_clase_grupal ";
                  $rowt=RecuperaValor($Query);
                  $mn_tarif=$rowt['mn_rate'];
                 


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
                      $lessons.=$rowm['nb_programa']."<br>";
                      $no_grados.=$rowm['no_grado']."<br>";


				  }
				
				  #Recupermaos la tarfa impuesta =
				  $Query="SELECT mn_cgrupo FROM k_maestro_tarifa_gg WHERE fl_maestro=$fl_usuario AND fl_clase_grupo=$fl_clase_grupal ";
				  $mn=RecuperaValor($Query);
				  
                  if(empty($mn_tarif)){                     
                      $mn_tarif=!empty($mn[0])?$mn[0]:0;
                  }



				  $total_gg+=$mn_tarif;

        $Qclase_grupal = RecuperaValor("SELECT fl_live_session_grupal FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase_grupal ");

        $fl_live_session_grupal = $Qclase_grupal['fl_live_session_grupal'];

        $Qassist_grupal = RecuperaValor("SELECT cl_estatus_asistencia_gg FROM k_live_session_asistencia_gg WHERE fl_live_session_gg=$fl_live_session_grupal AND fl_usuario=$fl_usuario");

        $statusAsistencia_gg=!empty($Qassist_grupal[0])?$Qassist_grupal[0]:NULL;

        switch ($statusAsistencia_gg) {
          case '1':
            $attendance_gg="<small class='text-danger'><i>Absent</i></small>";
            break;
          case '2':
            $attendance_gg="<small class='text-success'><i>Present</i></small>";
            break;
          case '3':
            $attendance_gg="<small class='text-warning'><i>Late</i></small>";
            break;
          default:
            $attendance_gg="<small class='text-danger'><i>Absent</i></small>";
            break;
        }

				
				
				 echo "
                    <tr>
                      <td align='center'>".$fe_clase."</td>
                      <td align='center'>".$no_orden."</td>
                      <td align='center'>".$lessons."</td>
          					  <td align='center'>".$ds_descripion."</td>
          					  <td align='center'>".$ds_clase_grupal."</td>
          					  <td align='center'>".$ds_titulo."</td>
          					  <td align='center'>".$periodos."</td>
                      <td align='center'>".$no_grados."</td>
          					  <td align='center'>".$no_alumnos."</td>
                      <td align='center'>".$attendance_gg."</td>
                      <td align='center'>$ ".number_format($mn_tarif,2,'.',',')."</td>
                      <td align='right'>$ ".number_format($mn_tarif,2,'.',',')."</td>
                    </tr>";
				
				
			  }
			  
			    echo "<tr style='color:#0071BD;'><td colspan=10 class='text-right'><strong>".ObtenEtiqueta(726).": $ ".number_format($total_gg,2,'.',',')."</strong></td></tr>";
                
			  
			  echo"</tbody>
				</table>";
			  
			  
			  
			  
			  
			}
			  
			  
			  
			  
			  
			  
              # Fin de la tabla datos automaticos
              # Si no hay registros manuales no muestra la tabla
              $row_exi = RecuperaValor("SELECT *FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago AND fg_tipo='M'");
              if(!empty($row_exi[0])){
                echo "
                <br />
                <br />
                <table class='table table-hover'>
                  <thead>
                    <tr>
                      <th align='center'>".ObtenEtiqueta(719)."</th>
                      <th align='center'>".ObtenEtiqueta(723)."</th>
                      <th align='center'>".ObtenEtiqueta(727)."</th>
                      <th align='center'>".ObtenEtiqueta(724)."</th>
                    </tr>
                  </thead>
                  <tbody>";
                  # Query para las horas extras que se imparteron en el mes seleccionado
                  $Query = "SELECT ds_concepto, mn_tarifa_hr, no_horas FROM k_maestro_pago_det WHERE fg_tipo='M' AND fl_maestro_pago=$fl_maestro_pago";
                  $rs = EjecutaQuery($Query);
                  $tot_man =0;
                  for($i=0;$row=RecuperaRegistro($rs);$i++){
                    $subm_total = $row[1]*$row[2];
                    $tot_man = $tot_man + $subm_total;
                    echo "
                    <tr>
                      <td>".$row[0]."</td>
                      <td>$ ".number_format($row[1],2,'.',',')."</td>
                      <td>".$row[2]."</td>
                      <td>$ ".number_format($subm_total,2,'.',',')."</td>
                    </tr>";
                  }
                  echo "
                    <tr style='color:#0071BD;'><td colspan=5 class='text-right'><strong>".ObtenEtiqueta(734).": $ ".number_format($tot_man,2,'.',',')."</strong></td></tr>
                  </tbody>
                </table>";
              }            
              # Mostramos el mensaje si existen clases que no se impartieron
              if(!empty($classzero)){
                echo " 
                  <div class='alert alert-block alert-info'>                
                    <h4 class='alert-heading'><i class='fa fa-paperclip'></i>";
                    # Agregamos una nota para las clases con un monto cero
                    echo ObtenEtiqueta(108).":</h4>&nbsp;".ObtenEtiqueta(109);
                echo "
                  </div>";
              }
              echo "
             </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>";
  
  #colocamos el total hasta aariba
  $mn_total=$mn_total_primer_tabla+$total_gg+$tot_man;
  
  echo"
  <script>
     $('#amount_total').empty();
     
	 $('#amount_total').append('$'+".number_format($mn_total,2,'.',',')."+' ".ObtenEtiqueta(737)."');
    document.getElementById('content').style.opacity = '1';
  
  </script>
  
  ";
  
  
?>
