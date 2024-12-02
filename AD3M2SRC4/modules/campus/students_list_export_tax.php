<?php

  # La libreria de funciones
  require '../../lib/general.inc.php';
  error_reporting(E_ALL);
  require_once '../../lib/PHPExcel1.8/PHPExcel/IOFactory.php';
  require_once '../../lib/PHPExcel1.8/PHPExcel.php';

  //usando phpSpeedsheet.
  require '../../lib/PHPspeed/vendor/autoload.php';
  use PhpOffice\PhpSpreadsheet\IOFactory;
  use PhpOffice\PhpSpreadsheet\Spreadsheet;

  $fl_param = $_GET['fl_param'];
  $fe_ini = $_GET['fe_uno'];
  $fe_dos = $_GET['fe_dos'];
  $criterio=$_GET['criterio'];
  $fg_opcion=$_GET['fg_opcion'];
  $fl_pais=$_GET['fl_pais'];


  if($fe_ini){
      #Damos formato de fecha alos parametros recibidos.
      $fe_ini =strtotime('0 days',strtotime($fe_ini));
      $fecha1= date('Y-m-d',$fe_ini);
  }
  if($fe_dos){
      $fe_dos=strtotime('0 days',strtotime($fe_dos));
      $fecha2= date('Y-m-d',$fe_dos);
  }

  function Mes($month){


      switch($month) {
          case '01': $month='January';  break;
          case '02': $month='February'; break;
          case '03': $month='March'; break;
          case '04': $month='April'; break;
          case '05': $month='May'; break;
          case '06': $month='June'; break;
          case '07': $month='July'; break;
          case '08': $month='August'; break;
          case '09': $month='September'; break;
          case '10': $month='October'; break;
          case '11': $month='November'; break;
          case '12': $month='December'; break;
      }

      return $month;
  }


  //$spreadsheet = IOFactory::load('ID-03642-Vancouver_Animation_School_2020-10-28-Current_correct.xlsx');
  $spreadsheet = new Spreadsheet();


  $spreadsheet->setActiveSheetIndex(0)
      ->setCellValue('A1','T2202')
      ->setCellValue('B1','Company.Name1')
      ->setCellValue('C1','Company.CompanyTag')
      ->setCellValue('D1','LastName')
      ->setCellValue('E1','FirstName')
      ->setCellValue('F1','Initial')
      ->setCellValue('G1','Address1')
      ->setCellValue('H1','Address2')
      ->setCellValue('I1','City')
      ->setCellValue('J1','Prov')
      ->setCellValue('K1','Postal')
      ->setCellValue('L1','Country')
      ->setCellValue('M1','TaxYear')
      ->setCellValue('N1','SIN')
      ->setCellValue('O1','SlipStatus')
      ->setCellValue('P1','ProgramName')
      ->setCellValue('Q1','StudentNumber')
      ->setCellValue('R1','FlyingSchoolCourseType')
      ->setCellValue('S1','FromYear1')
      ->setCellValue('T1','FromMonth1')
      ->setCellValue('U1','ToYear1')
      ->setCellValue('V1','ToMonth1')
      ->setCellValue('W1','TuitionFees1')
      ->setCellValue('X1','PartTimeMonths1')
      ->setCellValue('Y1','FullTimeMonths1')
      ->setCellValue('Z1','FromYear2')
      ->setCellValue('AA1','FromMonth2')
      ->setCellValue('AB1','ToYear2')
      ->setCellValue('AC1','ToMonth2')
      ->setCellValue('AD1','TuitionFees2')
      ->setCellValue('AE1','PartTimeMonths2')
      ->setCellValue('AF1','FullTimeMonths2')
      ->setCellValue('AG1','FromYear3')
      ->setCellValue('AH1','FromMonth3')
      ->setCellValue('AI1','ToYear3')
      ->setCellValue('AJ1','ToMonth3')
      ->setCellValue('AK1','TuitionFees3')
      ->setCellValue('AL1','PartTimeMonths3')
      ->setCellValue('AM1','FullTimeMonths3')
      ->setCellValue('AN1','FromYear4')
      ->setCellValue('AO1','FromMonth4')
      ->setCellValue('AP1','ToYear4')
      ->setCellValue('AQ1','ToMonth4')
      ->setCellValue('AR1','TuitionFees4')
      ->setCellValue('AS1','PartTimeMonths4')
      ->setCellValue('AT1','FullTimeMonths4')
      ->setCellValue('AU1','TextAtTop')
      ->setCellValue('AV1','EmailAddress')
      ->setCellValue('AW1','OkToEmailSlip')
      ->setCellValue('AX1','SlipTag')
      ->setCellValue('AY1','CustomField')
      ->setCellValue('AZ1','CustomPassword')
      ;



     #Muestra resultados de la busqueda.
  $Query ='
    SELECT Main.*,
           Main.nb_usuario "name",
           CONCAT_WS(" ", Main.ds_add_city, Main.nb_zona_horaria) "country",
           CONCAT_WS(" ", Main.nb_programa, "Term:", Main.no_grado) "program",
           Main.status_label "status",

           "teachers" "teachers"


    FROM (SELECT Usuario.fl_usuario fl_usuario,
                 Usuario.cl_sesion,
                 Usuario.ds_login ds_login,
                 CONCAT_WS(" ", IFNULL(Usuario.ds_nombres, ""),
                                IFNULL(Usuario.ds_apaterno, ""),
                                IFNULL(Usuario.ds_amaterno, "")) nb_usuario,
                 Usuario.ds_nombres ds_nombres,
                 Usuario.ds_apaterno ds_apaterno,
                 Usuario.ds_amaterno ds_amaterno,
                 Usuario.fg_genero fg_genero,
                 Usuario.fe_nacimiento,
                 Usuario.ds_email,
                 Usuario.fg_activo,
                 Usuario.fe_alta,
                 DATE_FORMAT(Usuario.fe_alta, "%d-%m-%Y") AS fe_alta_label,
                 Usuario.fe_ultacc,
                 USesion.fe_ultmod,
                 Alumno.no_promedio_t,
                 Alumno.ds_notas,
                 CONCAT(ZH.nb_zona_horaria, " ", "GMT", " (", ZH.no_gmt, ")") nb_zona_horaria,
                 (SELECT fg_international
                  FROM k_app_contrato app
                  WHERE app.cl_sesion = Usuario.cl_sesion
                  ORDER BY no_contrato LIMIT 1) fg_international,
                 Periodo.nb_periodo,
                 (SELECT fe_inicio
                  FROM k_term te, c_periodo i, k_alumno_term al
                  WHERE te.fl_periodo = i.fl_periodo
                  AND te.fl_term = al.fl_term
                  AND al.fl_alumno = Usuario.fl_usuario
                  AND no_grado = 1
                  LIMIT 1) fe_start_date,
                 Programa.nb_programa,
                 CONCAT(Profesor.ds_nombres, " ", Profesor.ds_apaterno) ds_profesor,
                 Grupo.nb_grupo,
                 PCTIA.fe_carta,
                 PCTIA.fe_contrato,
                 PCTIA.fe_fin,
                 PCTIA.fe_completado,
                 PCTIA.fe_emision,
                 PCTIA.fg_certificado,
                 PCTIA.fg_honores,
                 PCTIA.fe_graduacion,
                 PCTIA.fg_desercion,
                 PCTIA.fg_dismissed,
                 PCTIA.fg_job,
                 PCTIA.fg_graduacion,
                 Form1.ds_add_city,
                 Form1.ds_add_state,
                 USesion.fg_pago,
                 Pais.ds_pais,
                 YEAR(Usuario.fe_nacimiento) ye_fe_nacimiento,
                 YEAR(Usuario.fe_alta) ye_fe_alta,
                 YEAR(Usuario.fe_ultacc) ye_fe_ultacc,
                 YEAR(Form1.fe_ultmod) ye_fe_ultmod,
                 YEAR(PCTIA.fe_carta) ye_fe_carta,
                 YEAR(PCTIA.fe_contrato) ye_fe_contrato,
                 YEAR(PCTIA.fe_fin) ye_fe_fin,
                 YEAR(PCTIA.fe_completado) ye_fe_completado,
                 YEAR(PCTIA.fe_emision) ye_fe_emision,
                 YEAR(PCTIA.fe_graduacion) ye_fe_graduacion,
                 (SELECT YEAR(fe_inicio)
                  FROM k_term te, c_periodo i, k_alumno_term al
                  WHERE te.fl_periodo = i.fl_periodo
                  AND te.fl_term = al.fl_term
                  AND al.fl_alumno = Usuario.fl_usuario
                  AND no_grado = 1
                  LIMIT 1) ye_fe_start_date,
                  CASE
                  WHEN PCTIA.fg_job LIKE "1" THEN "Work placement"
                  WHEN PCTIA.fg_graduacion LIKE "1" THEN "Graduated"
                  WHEN PCTIA.fg_dismissed LIKE "1" THEN "Student dismissed"
	          WHEN PCTIA.fg_desercion LIKE "1" THEN "Student withdrawal"
	          WHEN Usuario.fg_activo LIKE "1" THEN "Active"
                  ELSE "Not Set"
                  END status_label,
                  CASE WHEN Grupo.fl_term >0 THEN Grupo.fl_term ELSE 0 END fl_term,
                  Form1.fl_programa,
                  CASE WHEN Term.no_grado >0 THEN Term.no_grado ELSE 0 END no_grado,
            (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= Alumno.no_promedio_t AND no_max >= Alumno.no_promedio_t LIMIT 1) cl_calificacion,
            Alumno.mn_progreso, CAST(Programa.ds_duracion AS UNSIGNED)ds_duracion,Alumno.fg_absence, Alumno.fg_change_status ,Form1.ds_add_country,Form1.ds_number,Form1.ds_add_street,Form1.fg_disability,Form1.ds_add_zip,Programa.no_ptib,Usuario.ds_graduate_status,Form1.fl_immigrations_status,ProgCosto.cl_delivery,ProgCosto.ds_credential,Form1.ds_sin,Form1.fl_periodo ,Form1.ds_add_number,ProgCosto.no_semanas
                  /*
                  OCULTO POR EFICIENCIA
                  ,IFNULL(GROUP_CONCAT(TRUNCATE(Grade.no_promedio, 0) ORDER BY Grade.no_grado ),0) grades_by_term
                  */
          FROM c_usuario Usuario
          JOIN c_sesion USesion ON(USesion.cl_sesion = Usuario.cl_sesion)
          JOIN c_alumno Alumno ON(Usuario.fl_usuario = Alumno.fl_alumno)
          JOIN c_zona_horaria ZH ON(ZH.fl_zona_horaria = Alumno.fl_zona_horaria)
          LEFT JOIN k_alumno_grupo AlumnoGrupo ON(AlumnoGrupo.fl_alumno = Usuario.fl_usuario) AND AlumnoGrupo.fg_grupo_global<>"1"
          LEFT JOIN c_grupo Grupo ON (Grupo.fl_grupo = AlumnoGrupo.fl_grupo)
          LEFT JOIN c_usuario Profesor ON(Grupo.fl_maestro = Profesor.fl_usuario)
          LEFT JOIN k_term Term ON(Term.fl_term = Grupo.fl_term)
          JOIN k_ses_app_frm_1 Form1 ON(Usuario.cl_sesion = Form1.cl_sesion)
          JOIN c_programa Programa ON(Programa.fl_programa = Form1.fl_programa)
          JOIN c_periodo Periodo ON (Periodo.fl_periodo = Form1.fl_periodo)
          LEFT JOIN k_programa_costos ProgCosto ON ProgCosto.fl_programa=Programa.fl_programa
          /*
          OCULTO POR EFICIENCIA
          LEFT JOIN (SELECT kat.fl_alumno, t.no_grado, kat.no_promedio, t.fl_programa
                     FROM k_alumno_term kat
                     JOIN k_term t ON(t.fl_term = kat.fl_term)
                     LEFT JOIN (SELECT kat.fl_alumno, t.fl_term, t.no_grado
                                FROM k_alumno_term kat
                                JOIN k_term t ON(t.fl_term = kat.fl_term)
		                ) t2 ON(t2.fl_alumno = kat.fl_alumno
                                        AND t2.no_grado = t.no_grado
                                        AND  t.fl_term < t2.fl_term )
		     WHERE t2.fl_term IS NULL
                     ORDER BY t.no_grado, t.fl_term) Grade ON(Grade.fl_alumno = Usuario.fl_usuario
                                                              AND Grade.fl_programa = Programa.fl_programa)
          */
          JOIN c_pais Pais ON(Pais.fl_pais = Form1.ds_add_country)
          LEFT JOIN k_pctia PCTIA ON (PCTIA.fl_alumno = Usuario.fl_usuario)
          WHERE Usuario.fl_perfil = 3 ';
  if(!empty($fl_pais)){
      $Query.='AND  Form1.ds_add_country='.$fl_pais.'  ';
  }
  $Query.='

          GROUP BY Usuario.fl_usuario) AS Main
    WHERE true = true   ';
  if($fl_param=='Active'){
      $Query.=' AND fg_activo = "1" ';
  }
  if($fl_param=='Inactive'){
      $Query.=' AND fg_activo = "0" ';
  }
  if(!empty($fe_ini)){
     // $Query.=' AND fe_start_date>="'.$fecha1.'" ';
      $Query.=' AND fe_start_date >= STR_TO_DATE("'.$fecha1.'", "%Y-%m-%d") ';
  }
  if(!empty($fe_dos)){
     // $Query.=' AND fe_fin<="'.$fecha2.'" ';
      $Query.=' AND fe_start_date <= STR_TO_DATE("'.$fecha2.'", "%Y-%m-%d") ';
  }
  if($fg_opcion=='Certificate'){
      $Query.=' AND ds_credential LIKE "%Certificate%" ';
      //$Query.=' AND fl_programa=31 ';
  }
  if($fg_opcion=='Diploma'){

      $Query.=' AND ds_credential LIKE "%Diploma%" ';

  }
  if($criterio){

      $Query.='AND  (ds_nombres LIKE \'%'.$criterio.'%\'
	                 OR nb_usuario LIKE \'%'.$criterio.'%\'
                     OR nb_programa LIKE \'%'.$criterio.'%\'
                     OR ds_pais LIKE \'%'.$criterio.'%\'
                     OR status_label LIKE \'%'.$criterio.'%\'
                     OR fe_graduacion LIKE \'%'.$criterio.'%\'
	                 OR fe_completado LIKE \'%'.$criterio.'%\' ) ';

  }


  $Query.='
    ORDER BY Main.fe_alta DESC
';

  //echo $Query;
  $rs = EjecutaQuery($Query);
  //$registros = CuentaRegistros($rs);
  for($i=2;$row=RecuperaRegistro($rs);$i++) {

      $fl_usuario=$row['fl_usuario'];
      $ds_login=$row['ds_login'];
      $nb_usuario=html_entity_decode($row['nb_usuario']);
      $ds_nombre=html_entity_decode($row['ds_nombres']);
      $ds_apaterno=html_entity_decode($row['ds_apaterno']);
      $ds_amaterno=html_entity_decode($row['ds_amaterno']);
      $ds_add_city=html_entity_decode($row['ds_add_city']);
      $ds_pais=html_entity_decode($row['ds_pais']);
      $nb_zona_horaria=$row['nb_zona_horaria'];
      $nb_programa=html_entity_decode($row['nb_programa']);
      $ds_duracion=$row['ds_duracion'];
      $fl_programa=$row['fl_programa'];
      $fe_birth=$row['fe_nacimiento'];
      $fe_gender=$row['fg_genero'];
      $ds_add_country=html_entity_decode($row['ds_add_country']);
      $ds_number=$row['ds_number'];
      $ds_email=$row['ds_email'];
      $ds_add_street=html_entity_decode($row['ds_add_street']);
      $ds_add_state=$row['ds_add_state'];
      $status=$row['status'];
      $fe_fin_=$row['fe_fin'];
      $fe_completado=$row['fe_completado'];
      $fe_graduacion=$row['fe_graduacion'];
      $fg_certificado=$row['fg_certificado'];
      $fe_start_date=$row['fe_start_date'];
      $status_label=$row['status_label'];
      $mn_progreso=$row['mn_progreso'];
      $fg_disability=!empty($row['fg_disability'])?"Y":"N";
      $ds_add_zip=$row['ds_add_zip'];
      $fl_immigrations_status=$row['fl_immigrations_status'];
      $SIN=!empty($row[70])?$row[70]:"000000000";
      $fl_periodo=$row['fl_periodo'];
      $ds_add_number=$row['ds_add_number'];
      $no_semanas=$row['no_semanas'];


      #Anio anterior
      $fecha_actual = date("d-m-Y");
      //resto 1 año
      $tax_year=date("Y",strtotime($fecha_actual."- 1 year"));

      #Recuperamos el mes y el anio de inicio.
      $data=explode('-',$fe_start_date);
      $start_year=$data[0];
      $start_month=$data[1];

      #Recuperamos el mes y anio de fin de curso.
      $data2=explode('-',$fe_fin_);
      $end_year=$data2[0];
      $end_month=$data2[1];

      $start_month=Mes($start_month);
      $end_month=Mes($end_month);
      if($ds_duracion==12)//Aunque haya sido fullpayment se divide en 4 trimestres.
      $fg_division_pagos=4;
      #seDetermina si son 1 3 12 mese etc. en trismestres.
      $no_meses=$ds_duracion;
      if($no_meses>3)$no_meses=3;

      $Query="SELECT code FROM immigrations_status WHERE fl_immigrations_status=$fl_immigrations_status ";
      $roc=RecuperaValor($Query);
      $code_immigration_status=$roc[0];

      $Queryo="SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$fl_usuario ";
      $rop=RecuperaValor($Queryo);
      $cl_sesion=$rop['cl_sesion'];

      $Query="SELECT fg_aboriginal,fg_opcion_pago FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
      $rok=RecuperaValor($Query);
      $fg_aboriginal=!empty($rok['fg_aboriginal'])?"Y":"N";
      $fg_opcion_pago=$rok['fg_opcion_pago'];

      # Recupera informacion de los pagos
      switch($fg_opcion_pago) {
          case 1: $mn_due='mn_a_due'; $no_x_payments = 'no_a_payments'; $ds_pagos = 'no_a_payments'; break;
          case 2: $mn_due='mn_b_due'; $no_x_payments = 'no_b_payments'; $ds_pagos = 'no_b_payments'; break;
          case 3: $mn_due='mn_c_due'; $no_x_payments = 'no_c_payments'; $ds_pagos = 'no_c_payments'; break;
          case 4: $mn_due='mn_d_due'; $no_x_payments = 'no_d_payments'; $ds_pagos = 'no_d_payments'; break;
      }

      $Querycon="SELECT $mn_due  FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1' ";
      $rowcon=RecuperaValor($Querycon);
      $mn_due_tax=$rowcon[0];


      $Querypago = "SELECT $ds_pagos FROM k_programa_costos WHERE fl_programa=$fl_programa ";
      $rowpago = RecuperaValor($Querypago);
      $no_x_payments=$rowpago[0];

      $mn_pago_1=NULL;
      $mn_pago_2=NULL;
      $mn_pago_3=NULL;
      $mn_pago_4=NULL;
      $from_month_1=NULL;
      $from_month_2=NULL;
      $from_month_3=NULL;
      $from_month_4=NULL;
      $to_month_1=NULL;
      $to_month_2=NULL;
      $to_month_3=NULL;
      $to_month_4=NULL;
      $from_year_1=NULL;
      $from_year_2=NULL;
      $from_year_3=NULL;
      $from_year_4=NULL;
      $to_year_1=NULL;
      $to_year_2=NULL;
      $to_year_3=NULL;
      $to_year_4=NULL;
      $no_pagos_opcion=NULL;
      $contador_refunds=NULL;
      $monto=NULL;
      $full_time=NULL;
      $part_time=NULL;
      $full_time_2=NULL;
      $full_time_3=NULL;
      $full_time_4=NULL;
      $partime_2=NULL;
      $partime_3=NULL;
      $partime_4=NULL;
      $initial=null;

      # Obtenemos el full o part time del programal
      $Query = "SELECT fg_fulltime FROM c_programa WHERE fl_programa=$fl_programa ";
      $row = RecuperaValor($Query);
      if($row[0]==1){
          $full_time=$no_meses;
      }else{
          $part_time=$no_meses;
      }


      # Le sumamos lo numero de meses a la fecha inicial para obtener el fecha final
      # Calculamos la cantidad que se paga por mes
      $fe_inicio1 = DATE_FORMAT(date_create($fe_start_date), 'Y-m-d');
      $mes_inicio1 = DATE_FORMAT(date_create($fe_start_date), 'm');
      $anio_inicio1 = DATE_FORMAT(date_create($fe_start_date), 'Y');
      $meses = ($no_semanas / 4);
      $fe_nueva = strtotime('+ ' . ($meses - 1) . ' month', strtotime($fe_inicio1));
      $fe_fin1 = date('Y-m-d', $fe_nueva);
      $mes_fin1 = date('m', $fe_nueva);
      $anio_fin1 = date('Y', $fe_nueva);
      $anios1 = $anio_fin1 - $anio_inicio1;


      for ($j = 0; $j <= $anios1; $j++) {
          $anios2 = $anio_inicio1 + $j;
          if ($anios2 < date('Y')) {
              # Obtiene los meses que conforman el anio para el que se pago
              if ($anio_inicio1 == $anio_fin1)
                  $num_meses_anio = $mes_fin1 - $mes_inicio1 + 1;
              else {
                  $num_meses_anio = 12;
                  if ($anios2 == $anio_fin1)
                      $num_meses_anio = $mes_fin1;
                  if ($anios2 == $anio_inicio1)
                      $num_meses_anio = 12 - $mes_inicio1 + 1;
              }

              # Monto pagado en el anio
              $no_pagos_opcion=$no_x_payments-$contador_refunds;
              $monto = ($mn_due_tax / ($meses/$no_pagos_opcion)) * $num_meses_anio;
              $monto = number_format($monto,2,'.',',');

              # Obtenemos los meses que cubren lo pagos
              # Obtenemos su nombre para mostrarlos en la tabla
              if ($anios2 == $anio_inicio1) {
                  if ($anio_inicio1 == $anio_fin1) {
                      $mes_ini = $mes_inicio1;
                      $mes_fin = $mes_fin1;
                  } else {
                      $mes_ini = $mes_inicio1;
                      $mes_fin = 12;
                  }
              }
              else {
                  $mes_ini = 1;
                  $mes_fin = $mes_fin1;
                  if ($anios2 != $anio_fin1)
                      $mes_fin = 12;
              }

              # Si el alumno se retiro antes de acabar el curso
              # Obtenemos el ultimo pago y hasta ahi sumamos las cantidades
              if (!empty($fg_desercion) AND ( $anios2 != $anio_fin1 AND $anios2 != $anio_inicio1)) {
                  $Query = "SELECT DATE_FORMAT(fe_pago,'%m') FROM k_alumno_pago WHERE fl_alumno=$fl_usuario AND DATE_FORMAT(fe_pago, '%Y')='$anios2' order by fe_pago DESC ";
                  $row = RecuperaValor($Query);
                  $num_meses_anio = $row[0];
                  $mes_fin = $row[0];
              }


              $mes_ini = ObtenNombreMes($mes_ini);
              $mes_fin = ObtenNombreMes($mes_fin);

          }

      }


      #Dividimos los pagos segun sea el caso.
      switch($ds_duracion){

          case "1":
              $from_year_1=$start_year;
              $from_month_1=$start_month;
              $to_year_1=$end_year;
              $to_month_1=$end_month;
              $mn_pago_1=$mn_due_tax;
              break;
          case "3":
              $from_year_1=$start_year;
              $from_month_1=$start_month;
              $to_year_1=$end_year;
              $to_month_1=$end_month;
              $mn_pago_1=$mn_due_tax;



              break;
          case "12":
              #se tiene que dividir el pago en 4 periodos.
              #Verificamos los pagos del alumno.
              $Query="SELECT a.fl_alumno,a.fl_term_pago, a.fe_pago,a.mn_pagado,b.fe_pago fe_pago_programada,
                        DATE_FORMAT(b.fe_ini_pago,'%M') fe_ini_pago,
                        DATE_FORMAT(b.fe_fin_pago,'%M') fe_fin_pago,
                        DATE_FORMAT(b.fe_ini_pago,'%Y') fe_ini_anio_pago,
                        DATE_FORMAT(b.fe_ini_pago,'%Y') fe_fin_anio_pago,
                        b.fe_ini_pago fecha_inicial,a.mn_late_fee

                            FROM k_alumno_pago a
                            JOIN k_term_pago b
                            ON a.fl_term_pago=b.fl_term_pago
                            WHERE a.fl_alumno=$fl_usuario AND DATE_FORMAT(b.fe_pago,'%Y')<=$start_year ";
			  $Query.="   AND DATE_FORMAT(b.fe_ini_pago,'%m')<12 ";//tiene ser menores de Diciembre

              $rspay=EjecutaQuery($Query);
              $no_pagos=CuentaRegistros($rspay);
              for($jp=1;$rowpay=RecuperaRegistro($rspay);$jp++) {

                  $mn_late_fee=!empty($rowpay['mn_late_fee'])?$rowpay['mn_late_fee']:0;
                  $mn_pago=$rowpay['mn_pagado']-$mn_late_fee;
                  $fe_pago=$rowpay['fe_pago'];
                  $fe_pago_programada=$rowpay['fe_pago_programada'];
                  $fe_mes_ini_pago=$rowpay['fe_ini_pago'];
                  $fe_mes_fin_pago=$rowpay['fe_fin_pago'];
                  $fe_anio_ini_pago=$rowpay['fe_ini_anio_pago'];
                  $fe_anio_fin_pago=$rowpay['fe_fin_anio_pago'];
                  $fecha_inicial=$rowpay['fecha_inicial'];
                  if($no_pagos==2)$mn_pago=$mn_pago/2;
                  if($no_pagos==3)$mn_pago=$mn_pago/3;

                      switch($jp){
                          case "1":

                              #Sumar 3 meses a la fecha inicial
                              $fecha = date($fecha_inicial);
                              $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) );
                              $nuevafecha = date ( 'm' , $nuevafecha );

                              $mn_pago_1=$mn_pago;
                              $from_month_1=Mes($fe_mes_ini_pago);
                              $to_month_1=Mes($nuevafecha);
                              $from_year_1=$fe_anio_ini_pago;
                              $to_year_1=$fe_anio_fin_pago;


                              $fecha=date($fecha_inicial);
                              $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                              $mes_siguiente=date('Y-m-d',$mes_siguiente);
                              #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                              $fe_ini_pagos=$mes_siguiente;

                              break;
                          case "2":
                              #Sumar 3 meses a la fecha inicial
                              $fecha = date($fe_ini_pagos);
                              $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                              $nuevafecha = date ( 'm' , $nuevafecha );

                              #Convertimos el mes inicial a numerico.
                              $NuevaFechaInicial=date($fe_ini_pagos);
                              $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                              $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                              $mn_pago_2=$mn_pago;
                              $from_month_2=Mes($NuevaFechaInicial);
                              $to_month_2=Mes($nuevafecha);
                              $from_year_2=$fe_anio_ini_pago;
                              $to_year_2=$fe_anio_fin_pago;

                              $fecha=date($fe_ini_pagos);
                              $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                              $mes_siguiente=date('Y-m-d',$mes_siguiente);
                              #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                              $fe_ini_pagos=$mes_siguiente;
                              $full_time_2=$full_time;
                              $partime_2=$part_time;

                              break;
                          case "3":
                              #Sumar 3 meses a la fecha inicial
                              $fecha = date($fe_ini_pagos);
                              $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                              $nuevafecha = date ( 'm' , $nuevafecha );

                              #Convertimos el mes inicial a numerico.
                              $NuevaFechaInicial=date($fe_ini_pagos);
                              $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                              $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                              $mn_pago_3=$mn_pago;
                              $from_month_3=Mes($NuevaFechaInicial);
                              $to_month_3=Mes($nuevafecha);
                              $from_year_3=$fe_anio_ini_pago;
                              $to_year_3=$fe_anio_fin_pago;

                              $fecha=date($fe_ini_pagos);
                              $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                              $mes_siguiente=date('Y-m-d',$mes_siguiente);
                              #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                              $fe_ini_pagos=$mes_siguiente;

                              $full_time_3=$full_time;
                              $partime_3=$part_time;

                              break;
                          case  "4":

                              #Sumar 3 meses a la fecha inicial
                              $fecha = date($fe_ini_pagos);
                              $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                              $nuevafecha = date ( 'm' , $nuevafecha );

                              #Convertimos el mes inicial a numerico.
                              $NuevaFechaInicial=date($fe_ini_pagos);
                              $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                              $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                              $mn_pago_4=$mn_pago;
                              $from_month_4=Mes($NuevaFechaInicial);
                              $to_month_4=Mes($nuevafecha);
                              $from_year_4=$fe_anio_ini_pago;
                              $to_year_4=$fe_anio_fin_pago;

                              $fecha=date($fe_ini_pagos);
                              $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                              $mes_siguiente=date('Y-m-d',$mes_siguiente);
                              #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                              $fe_ini_pagos=$mes_siguiente;

                              $full_time_4=$full_time;
                              $partime_4=$part_time;

                              break;
                      }





                  #}#end if


              }#end foreach

              #Forzozamente se divide en 4 cuando se encuentra 1 pago.
              if(($no_pagos==1)){

                  $Query="SELECT b.fl_term FROM k_alumno_pago a left JOIN k_term_pago b ON a.fl_term_pago=b.fl_term_pago WHERE a.fl_alumno=$fl_usuario AND fl_term IS NOT null ";
                  $rowps=RecuperaValor($Query);
                  $fl_term=$rowps['fl_term'];

                  $Query_ter=" SELECT fe_ini_pago fecha_inicial,fe_fin_pago FROM k_term_pago WHERE fl_term=$fl_term AND no_opcion=3 AND DATE_FORMAT(fe_ini_pago,'%Y')<=$start_year ";
                  $rs_ter=EjecutaQuery($Query_ter);

                  if(($fg_division_pagos==4)&&($fg_opcion_pago==1)){#forzamos a 12 pagos
                      $Querycon="SELECT $mn_due  FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1' ";
                      $rowcon=RecuperaValor($Querycon);
                      $mn_pago=$rowcon[0]/4;
                      //$mn_pago=$monto;
                  }
                  if(($fg_division_pagos==4)&&($fg_opcion_pago==2)){
                      #Dividimos el monto en 2
                      $mn_pago=$mn_pago/2;

                  }

                  for($jp1=1;$rowpag=RecuperaRegistro($rs_ter);$jp1++) {

                      $fecha_inicial=$rowpag['fecha_inicial'];
                      #Obtenemos el anio de los pagos.
                      $fecha_ini=explode("-",$fe_ini_pagos);
                      $anio_fecha=$fecha_ini[0];

                      if($anio_fecha==$tax_year){

                          switch($jp1) {
                              case 1:

                                  #Sumar 3 meses a la fecha inicial
                                  $fecha = date($fecha_inicial);
                                  $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) );
                                  $nuevafecha = date ( 'm' , $nuevafecha );

                                  $mn_pago_1=$mn_pago;
                                  $from_month_1=Mes($fe_mes_ini_pago);
                                  $to_month_1=Mes($nuevafecha);
                                  $from_year_1=$fe_anio_ini_pago;
                                  $to_year_1=$fe_anio_fin_pago;


                                  $fecha=date($fecha_inicial);
                                  $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                  $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                  #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                  $fe_ini_pagos=$mes_siguiente;
                                  break;
                              case 2:
                                  #Sumar 3 meses a la fecha inicial
                                  $fecha = date($fe_ini_pagos);
                                  $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                                  $nuevafecha = date ( 'm' , $nuevafecha );

                                  #Convertimos el mes inicial a numerico.
                                  $NuevaFechaInicial=date($fe_ini_pagos);
                                  $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                                  $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                                  $mn_pago_2=$mn_pago;
                                  $from_month_2=Mes($NuevaFechaInicial);
                                  $to_month_2=Mes($nuevafecha);
                                  $from_year_2=$fe_anio_ini_pago;
                                  $to_year_2=$fe_anio_fin_pago;

                                  $fecha=date($fe_ini_pagos);
                                  $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                  $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                  #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                  $fe_ini_pagos=$mes_siguiente;

                                  $full_time_2=$full_time;
                                  $partime_2=$part_time;

                                  break;
                              case 3:

                                  #Sumar 3 meses a la fecha inicial
                                  $fecha = date($fe_ini_pagos);
                                  $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                                  $nuevafecha = date ( 'm' , $nuevafecha );

                                  #Convertimos el mes inicial a numerico.
                                  $NuevaFechaInicial=date($fe_ini_pagos);
                                  $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                                  $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                                  $mn_pago_3=$mn_pago;
                                  $from_month_3=Mes($NuevaFechaInicial);
                                  $to_month_3=Mes($nuevafecha);
                                  $from_year_3=$fe_anio_ini_pago;
                                  $to_year_3=$fe_anio_fin_pago;


                                  $fecha=date($fe_ini_pagos);
                                  $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                  $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                  #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                  $fe_ini_pagos=$mes_siguiente;

                                  $full_time_3=$full_time;
                                  $partime_3=$part_time;

                                  break;
                              case 4:

                                  #Sumar 3 meses a la fecha inicial
                                  $fecha = date($fe_ini_pagos);
                                  $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                                  $nuevafecha = date ( 'm' , $nuevafecha );

                                  #Convertimos el mes inicial a numerico.
                                  $NuevaFechaInicial=date($fe_ini_pagos);
                                  $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                                  $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                                  $mn_pago_4=$mn_pago;
                                  $from_month_4=Mes($NuevaFechaInicial);
                                  $to_month_4=Mes($nuevafecha);
                                  $from_year_4=$fe_anio_ini_pago;
                                  $to_year_4=$fe_anio_fin_pago;

                                  $fecha=date($fe_ini_pagos);
                                  $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                  $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                  #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                  $fe_ini_pagos=$mes_siguiente;

                                  $full_time_4=$full_time;
                                  $partime_4=$part_time;
                                  break;


                          }
                      }#end if



                  }#end for






              }

              #No se encontro term su pago se divide en 4.
              if(empty($no_pagos)){
                $Query="SELECT b.fl_term FROM k_alumno_pago a left JOIN k_term_pago b ON a.fl_term_pago=b.fl_term_pago WHERE a.fl_alumno=$fl_usuario AND fl_term IS NOT null ";
                $rowps=RecuperaValor($Query);
                $fl_term=$rowps['fl_term'];

                if(!empty($fl_term)){

                    $Query_ter=" SELECT fe_ini_pago fecha_inicial FROM k_term_pago WHERE fl_term=$fl_term AND no_opcion=3 AND DATE_FORMAT(fe_pago,'%Y')<=$start_year ";
                    $rs_ter=EjecutaQuery($Query_ter);
                    for($jp1=1;$rowpag=RecuperaRegistro($rs_ter);$jp1++) {
                        $fecha_inicial=$rowpag['fecha_inicial'];

                        switch($jp1) {
                            case 1:

                                #Sumar 3 meses a la fecha inicial
                                $fecha = date($fecha_inicial);
                                $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) );
                                $nuevafecha = date ( 'm' , $nuevafecha );

                                $mn_pago_1=$mn_pago;
                                $from_month_1=Mes($fe_mes_ini_pago);
                                $to_month_1=Mes($nuevafecha);
                                $from_year_1=$fe_anio_ini_pago;
                                $to_year_1=$fe_anio_fin_pago;


                                $fecha=date($fecha_inicial);
                                $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                $fe_ini_pagos=$mes_siguiente;
                                break;
                            case 2:
                                #Sumar 3 meses a la fecha inicial
                                $fecha = date($fe_ini_pagos);
                                $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                                $nuevafecha = date ( 'm' , $nuevafecha );

                                #Convertimos el mes inicial a numerico.
                                $NuevaFechaInicial=date($fe_ini_pagos);
                                $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                                $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                                $mn_pago_2=$mn_pago;
                                $from_month_2=Mes($NuevaFechaInicial);
                                $to_month_2=Mes($nuevafecha);
                                $from_year_2=$fe_anio_ini_pago;
                                $to_year_2=$fe_anio_fin_pago;

                                $fecha=date($fe_ini_pagos);
                                $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                $fe_ini_pagos=$mes_siguiente;

                                $full_time_2=$full_time;
                                $partime_2=$part_time;

                                break;
                            case 3:

                                #Sumar 3 meses a la fecha inicial
                                $fecha = date($fe_ini_pagos);
                                $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                                $nuevafecha = date ( 'm' , $nuevafecha );

                                #Convertimos el mes inicial a numerico.
                                $NuevaFechaInicial=date($fe_ini_pagos);
                                $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                                $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                                $mn_pago_3=$mn_pago;
                                $from_month_3=Mes($NuevaFechaInicial);
                                $to_month_3=Mes($nuevafecha);
                                $from_year_3=$fe_anio_ini_pago;
                                $to_year_3=$fe_anio_fin_pago;


                                $fecha=date($fe_ini_pagos);
                                $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                $fe_ini_pagos=$mes_siguiente;

                                $full_time_3=$full_time;
                                $partime_3=$part_time;

                                break;
                            case 4:

                                #Sumar 3 meses a la fecha inicial
                                $fecha = date($fe_ini_pagos);
                                $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                                $nuevafecha = date ( 'm' , $nuevafecha );

                                #Convertimos el mes inicial a numerico.
                                $NuevaFechaInicial=date($fe_ini_pagos);
                                $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                                $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                                $mn_pago_4=$mn_pago;
                                $from_month_4=Mes($NuevaFechaInicial);
                                $to_month_4=Mes($nuevafecha);
                                $from_year_4=$fe_anio_ini_pago;
                                $to_year_4=$fe_anio_fin_pago;

                                $fecha=date($fe_ini_pagos);
                                $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                $fe_ini_pagos=$mes_siguiente;

                                $full_time_4=$full_time;
                                $partime_4=$part_time;

                                break;

                        }







                    }#end for.
                }else{


                    $mn_pago=0;
                    $mn_pago_1=0;
                    $no_meses=0;
                    $full_time=NULL;
                    $part_time=NULL;

                    #Dividimos su pagos en 3 solo presnetamos 1.
                    $Queryap="SELECT mn_pagado FROM k_alumno_pago WHERE fl_alumno=$fl_usuario ";
                    $rop=RecuperaValor($Queryap);
                    $mn_pago_1=$rop['mn_pagado']/$fg_opcion_pago;

                    #Sumar 3 meses a la fecha inicial default
                    $fecha = date($fe_start_date);
                    $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) );
                    $nuevafecha = date ( 'm' , $nuevafecha );
                    $end_month=Mes($nuevafecha);

                    if(!empty($mn_pago_1)){
                        $no_meses=3;

                        for ($y = 1; $y <= $fg_opcion_pago; $y++) {


                            switch($y) {
                                case 1:

                                    #Sumar 3 meses a la fecha inicial
                                    $fecha = date($fe_start_date);
                                    $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) );
                                    $nuevafecha = date ( 'm' , $nuevafecha );

                                    $from_month_1=$fe_mes_ini_pago;
                                    $to_month_1=Mes($nuevafecha);
                                    $from_year_1=$fe_anio_ini_pago;
                                    $to_year_1=$fe_anio_fin_pago;


                                    $fecha=date($fe_start_date);
                                    $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                    $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                    #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                    $fe_ini_pagos=$mes_siguiente;
                                    break;
                                case 2:
                                    #Sumar 3 meses a la fecha inicial
                                    $fecha = date($fe_ini_pagos);
                                    $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                                    $nuevafecha = date ( 'm' , $nuevafecha );

                                    #Convertimos el mes inicial a numerico.
                                    $NuevaFechaInicial=date($fe_ini_pagos);
                                    $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                                    $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                                    $mn_pago_2=$mn_pago_1;
                                    $from_month_2=Mes($NuevaFechaInicial);
                                    $to_month_2=Mes($nuevafecha);
                                    $from_year_2=$fe_anio_ini_pago;
                                    $to_year_2=$fe_anio_fin_pago;

                                    $fecha=date($fe_ini_pagos);
                                    $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                    $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                    #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                    $fe_ini_pagos=$mes_siguiente;

                                    $full_time_2=$no_meses;
                                    $partime_2=$part_time;

                                    break;
                                case 3:

                                    #Sumar 3 meses a la fecha inicial
                                    $fecha = date($fe_ini_pagos);
                                    $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
                                    $nuevafecha = date ( 'm' , $nuevafecha );

                                    #Convertimos el mes inicial a numerico.
                                    $NuevaFechaInicial=date($fe_ini_pagos);
                                    $NuevaFechaInicial=strtotime ( '+0 month' , strtotime ( $NuevaFechaInicial ) ) ;
                                    $NuevaFechaInicial = date ( 'm' , $NuevaFechaInicial );

                                    $mn_pago_3=$mn_pago_1;
                                    $from_month_3=Mes($NuevaFechaInicial);
                                    $to_month_3=Mes($nuevafecha);
                                    $from_year_3=$fe_anio_ini_pago;
                                    $to_year_3=$fe_anio_fin_pago;


                                    $fecha=date($fe_ini_pagos);
                                    $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                    $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                    #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                    $fe_ini_pagos=$mes_siguiente;

                                    $full_time_3=$no_meses;
                                    $partime_3=$part_time;

                                    break;
                            }

                        }#end for



                    }#end if





                }




              }


              break;



      }



      #Recupermaos el pais .
      $QueryP="SELECT cl_iso2,ds_pais FROM c_pais WHERE fl_pais=$ds_add_country ";
      $rowe=RecuperaValor($QueryP);
      $citizen_code=$rowe['cl_iso2'];
      $country=$rowe['ds_pais'];

      #Verifica y obtiene el code del estado.(canada)
      $Qury="SELECT ds_abreviada,ds_provincia FROM k_provincias WHERE fl_provincia=$ds_add_state ";
      $rol=RecuperaValor($Qury);
      $ds_code_state=$rol[0];
      $ds_add_state=$rol[1];

      $nb_compania="Vancouver Animation School";
      $companyTag="VANAS";









      $spreadsheet->setActiveSheetIndex(0)


        ->setCellValue('A'.$i.'','') //Student id
        ->setCellValue('B'.$i.'', ''.$nb_compania.'') //
        ->setCellValue('C'.$i.'',''.$companyTag.'') //company tag
        ->setCellValue('D'.$i.'',$ds_apaterno) //Last name
        ->setCellValue('E'.$i.'',''.$ds_nombre.'') //First name
        ->setCellValue('F'.$i.'',''.$initial.'') //initial
        ->setCellValue('G'.$i.'',''.$ds_add_number.'') //adress 1
        ->setCellValue('H'.$i.'',''.$ds_add_street.'') //adress 2
        ->setCellValue('I'.$i.'',''.$ds_add_city.'') //city
        ->setCellValue('J'.$i.'',''.$ds_add_state.'') //province
        ->setCellValue('K'.$i.'',''.$ds_add_zip.'') //postal
        ->setCellValue('L'.$i.'',''.$country.'') //country
        ->setCellValue('M'.$i.'',''.$tax_year.'') //tax_year
        ->setCellValue('N'.$i.'',''.$SIN.'') //SIN
        ->setCellValue('O'.$i.'','') //
        ->setCellValue('P'.$i.'',''.$nb_programa.'') //´rpgram name
        ->setCellValue('Q'.$i.'',''.$ds_login.'') //student number
        ->setCellValue('R'.$i.'','') //
        ->setCellValue('S'.$i.'',''.$from_year_1.'') //anio de inicio curso  FromYear
        ->setCellValue('T'.$i.'',''.$from_month_1.'') //mes de inicio curso  FromMonth
        ->setCellValue('U'.$i.'',''.$to_year_1.'') //anio fin curso          ToYear
        ->setCellValue('V'.$i.'',''.$to_month_1.'') //mes de fin curso       ToMonth  1
        ->setCellValue('W'.$i.'',''.number_format($mn_pago_1,2).'') //tuition fee  1pago     TuitonFee
        ->setCellValue('X'.$i.'','')   //PartTime Mont
        ->setCellValue('Y'.$i.'',''.$no_meses.'')//No. de meses. FullTimeMonth1
        ->setCellValue('Z'.$i.'',''.$from_year_2.'') //year_periodo 2
        ->setCellValue('AA'.$i.'',''.$from_month_2.'')
        ->setCellValue('AB'.$i.'',''.$to_year_2.'') //to year periodo 2
        ->setCellValue('AC'.$i.'',''.$to_month_2.'')
        ->setCellValue('AD'.$i.'',''.number_format($mn_pago_2,2).'')  //payment 2
        ->setCellValue('AE'.$i.'',''.$partime_2.'')  //no. de meses si lo tiene
        ->setCellValue('AF'.$i.'',''.$full_time_2.'')
        ->setCellValue('AG'.$i.'',''.$from_year_3.'')
        ->setCellValue('AH'.$i.'',''.$from_month_3.'')
        ->setCellValue('AI'.$i.'',''.$to_year_3.'')
        ->setCellValue('AJ'.$i.'',''.$to_month_3.'')
        ->setCellValue('AK'.$i.'',''.number_format($mn_pago_3,2).'')
        ->setCellValue('AL'.$i.'',''.$partime_3.'')//no meses 3
        ->setCellValue('AM'.$i.'',''.$full_time_3.'')
        ->setCellValue('AN'.$i.'',''.$from_year_4.'')
        ->setCellValue('AO'.$i.'',''.$from_month_4.'')
        ->setCellValue('AP'.$i.'',''.$to_year_4.'')
        ->setCellValue('AQ'.$i.'',''.$to_month_4.'')
        ->setCellValue('AR'.$i.'',''.number_format($mn_pago_4,2).'')
        ->setCellValue('AS'.$i.'',''.$partime_4.'')
        ->setCellValue('AT'.$i.'',''.$full_time_4.'')
        ->setCellValue('AU'.$i.'','')
        ->setCellValue('AV'.$i.'',''.$ds_email.'')
        ->setCellValue('AW'.$i.'','')
        ->setCellValue('AX'.$i.'','')
        ->setCellValue('AY'.$i.'','')
        ->setCellValue('AZ'.$i.'','');


  }

  // Rename sheet
  $spreadsheet->getActiveSheet(0)->setTitle('Student');


  //salida del excel.
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename="Student_data_report.xls"');
  $writer = IOFactory::createWriter($spreadsheet, 'Xls');
  $writer->save('php://output');


?>