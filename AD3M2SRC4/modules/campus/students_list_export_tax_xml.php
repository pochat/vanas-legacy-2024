<?php

  # La libreria de funciones
  require '../../lib/general.inc.php';
  error_reporting(E_ALL);
  header('Content-Type: application/xml');

  function MesNumerico($month){


      switch($month) {
          case 'January': $month='01';  break;
          case 'February': $month='2'; break;
          case 'March': $month='03'; break;
          case 'April': $month='04'; break;
          case 'May': $month='05'; break;
          case 'June': $month='06'; break;
          case 'July': $month='07'; break;
          case 'August': $month='08'; break;
          case 'September': $month='09'; break;
          case 'October': $month='10'; break;
          case 'November': $month='11'; break;
          case 'December': $month='12'; break;
      }

      return $month;

  }



  $fl_param = $_GET['fl_param'];
  $fe_ini = $_GET['fe_uno'];
  $fe_dos = $_GET['fe_dos'];
  $criterio=$_GET['criterio'];
  $fg_opcion=$_GET['fg_opcion'];
  $fl_pais=$_GET['fl_pais'];

  #Recibimos parametros y actualizamos la BD.
  $school_type=$_GET['school_type'];
  $file_account_number=$_GET['file_account_number'];
  $fg_report_type_code=$_GET['fg_report_type_code'];
  $filer_amendment_note=$_GET['filer_amendment_note'];
  $post_secondary_educational_institution_name=$_GET['post_secondary_educational_institution_name'];
  $post_secondary_educational_institution_mailing_address=$_GET['post_secondary_educational_institution_mailing_address'];
  $province_state_code=$_GET['province_state_code'];
  $country_code=$_GET['country_code'];
  $city_name=$_GET['city_name'];
  $postal_zip_code=$_GET['postal_zip_code'];
  $contact_name=$_GET['contact_name'];
  $contact_area_code=$_GET['contact_area_code'];
  $contact_phone_number=$_GET['contact_phone_number'];
  $contact_extension_number=$_GET['contact_extension_number'];





  if($fe_ini){
      #Damos formato de fecha alos parametros recibidos.
      $fe_ini =strtotime('0 days',strtotime($fe_ini));
      $fecha1= date('Y-m-d',$fe_ini);

      $StartYearMonth=date('y-m',$fe_ini);

      $TaxionYear=date('Y',$fe_ini);
  }
  if($fe_dos){
      $fe_dos=strtotime('0 days',strtotime($fe_dos));
      $fecha2= date('Y-m-d',$fe_dos);

      $EndYearMonth=date('y-m',$fe_dos);
  }

  $TaxionYear=ObtenConfiguracion(156);
  $sbmt_ref_id=ObtenConfiguracion(157);
  $trnmtr_nbr=ObtenConfiguracion(158);
  $l1_nm=ObtenConfiguracion(159);
  $cntc_email_area=ObtenConfiguracion(160);


  #Muestra resultados de la busqueda.
  $Query ='
    SELECT Main.*,
           Main.nb_usuario "name",
           CONCAT_WS(" ", Main.ds_add_city, Main.nb_zona_horaria) "country",
           CONCAT_WS(" ", Main.nb_programa, "Term:", Main.no_grado) "program",
           Main.status_label "status",
           -- Main.progress_std "progress",
           "teachers" "teachers"
           -- ,OCULTO POR EFICIENCIA "grades" "grades"
           -- ,Main.fg_activo "active"
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
            Alumno.mn_progreso,CAST(Programa.ds_duracion AS UNSIGNED)ds_duracion,Alumno.fg_absence, Alumno.fg_change_status ,Form1.ds_add_country,Form1.ds_number,Form1.ds_add_street,Form1.fg_disability,Form1.ds_add_zip,Programa.no_ptib,Usuario.ds_graduate_status,Form1.fl_immigrations_status,ProgCosto.cl_delivery,ProgCosto.ds_credential,Form1.ds_sin,Form1.fl_periodo ,Form1.ds_add_number,ProgCosto.no_semanas
            ,Periodo.fe_inicio
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
          JOIN k_programa_costos ProgCosto ON ProgCosto.fl_programa=Programa.fl_programa
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
          WHERE Usuario.fl_perfil = 3
          GROUP BY Usuario.fl_usuario) AS Main
    WHERE true = true ';
  if($fl_param=='Active'){
      $Query.=' AND fg_activo LIKE "1" ';
  }
  if($fl_param=='Inactive'){
      $Query.=' AND fg_activo LIKE "0" ';
  }
  if(!empty($fe_ini)){
      $Query.=' AND fe_start_date>=STR_TO_DATE("'.$fecha1.'", "%Y-%m-%d") ';
  }
  if(!empty($fe_dos)){
      $Query.=' AND fe_start_date<=STR_TO_DATE("'.$fecha2.'", "%Y-%m-%d") ';
  }
  if($fg_opcion=='Certificate'){
      $Query.=' AND ds_credential="Certificate" ';
      //$Query.=' AND fl_programa=31 ';
  }
  if($fg_opcion=='Diploma'){

      $Query.=' AND ds_credential="Diploma" ';

  }
  if(!empty($fl_pais)){
      $Query.=' AND ds_add_country='.$fl_pais.' ';
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

  #Recuperamos la data
  $rs = EjecutaQuery($Query);$mn_total_total=0;
  $no_alumnos_procesados=CuentaRegistros($rs);
  $monto_total=0;





$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);
$xml->setIndentString('');
$xml->startDocument('1.0', 'ISO-8859-1');
$xml->startElement("Submission");

$xml->startAttribute('xmlns:ccms');
$xml->text('http://www.cra-arc.gc.ca/xmlns/ccms/1-0-0');
$xml->endAttribute();

$xml->startAttribute('xmlns:sdt');
$xml->text('http://www.cra-arc.gc.ca/xmlns/sdt/2-2-0');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols/1-0-1');
$xml->endAttribute();


$xml->startAttribute('xmlns:ols1');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols1/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols10');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols10/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols100');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols100/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols12');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols12/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols125');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols125/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols140');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols140/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols141');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols141/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols2');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols2/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols5');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols5/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols50');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols50/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols52');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols52/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols6');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols6/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols8');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols8/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols8-1');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols8-1/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:ols9');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/ols9/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:olsbr');
$xml->text('http://www.cra-arc.gc.ca/enov/ol/interfaces/efile/partnership/olsbr/1-0-1');
$xml->endAttribute();

$xml->startAttribute('xmlns:xsi');
$xml->text('http://www.w3.org/2001/XMLSchema-instance');
$xml->endAttribute();


$xml->startAttribute('xsi:noNamespaceSchemaLocation');
$xml->text('layout-topologie.xsd');
$xml->endAttribute();

    $xml->startElement("T619");
        $xml->startElement("sbmt_ref_id");
        $xml->text(''.$sbmt_ref_id.'');//Required 8 alphanumeric
        $xml->endElement();

        $xml->startElement("rpt_tcd");
        $xml->text(''.$fg_report_type_code.'');//Required O original  A=Amended
        $xml->endElement();

        $xml->startElement("trnmtr_nbr");
        $xml->text(''.$trnmtr_nbr.'');//Transmitter NUMBER example: MM555555
        $xml->endElement();

		$xml->startElement("trnmtr_tcd");
        $xml->text('1');//Transmitter type indicator - 1 if you are submitting your returns | - 2 if you are submitting returns for others (service providers) | - 3 if you are submitting your returns using a purchased software package  |- 4 if you are a software vendor
        $xml->endElement();

        $xml->startElement("summ_cnt");
        $xml->text(''.$no_alumnos_procesados.'');//Total number of summary records
        $xml->endElement();

        $xml->startElement("lang_cd");
        $xml->text('E');//Lang code E = English
        $xml->endElement();

        $l1_nm_1=substr($l1_nm,0,30);
        $l1_nm_2=substr($l1_nm,30,60);

        $xml->startElement("TRNMTR_NM");
            $xml->startElement("l1_nm");
            $xml->text(''.$l1_nm_1.'');//Required 30 alphanumeric
            $xml->endElement();
            if(!empty($l1_nm_2)){
                $xml->startElement("l2_nm");
                $xml->text(''.$l1_nm_2.'');//Required 30 alphanumeric
                $xml->endElement();
            }
        $xml->endElement();


        $post_secondary_educational_institution_mailing_address_1=substr($post_secondary_educational_institution_mailing_address,0,30);

        $xml->startElement("TRNMTR_ADDR");
            $xml->startElement("addr_l1_txt");
            $xml->text(''.$post_secondary_educational_institution_mailing_address_1.'');//Transmitter city Required 28 alphanumeric
            $xml->endElement();

            $xml->startElement("cty_nm");
            $xml->text(''.$city_name.'');//Transmitter city Required 28 alphanumeric
            $xml->endElement();

            $xml->startElement("prov_cd");
            $xml->text(''.$province_state_code.'');//Transmitter province or territory code
            $xml->endElement();

            $xml->startElement("cntry_cd");
            $xml->text(''.$country_code.'');// CAN code
            $xml->endElement();


            //QUITAMOS ESPACIOS EN BLANCO
            $postal_zip_code=str_replace(" ","",$postal_zip_code);
            $xml->startElement("pstl_cd");
            $xml->text(''.$postal_zip_code.'');//Postal code
            $xml->endElement();
        $xml->endElement();

        $xml->startElement("CNTC");
            $xml->startElement("cntc_nm");
            $xml->text(''.$contact_name.'');//Contact name
            $xml->endElement();

            $xml->startElement("cntc_area_cd");
            $xml->text(''.$contact_area_code.'');//Contact area code
            $xml->endElement();

            $xml->startElement("cntc_phn_nbr");
            $xml->text(''.$contact_phone_number.'');//contac phone number
            $xml->endElement();

            $xml->startElement("cntc_email_area");
            $xml->text(''.$cntc_email_area.'');//contac email area
            $xml->endElement();
        $xml->endElement();

    $xml->endElement(); //fin T619




$xml->startElement("Return");
$xml->startElement("T2202"); //elemento T2202


    //Inician datos de cada estudiante.




        for($a=0;$row1=RecuperaRegistro($rs);$a++) {






            $fl_usuario=$row1['fl_usuario'];
            $fl_programa=$row1['fl_programa'];
            $nb_programa=$row1['nb_programa'];
            $ds_fname=$row1['ds_nombres'];
            $ds_lname=$row1['ds_apaterno'];
            $ds_number=$row1['ds_number'];
            $ds_login=$row1['ds_login'];
            $ds_add_street=$row1['ds_add_street'];//calle
            $ds_add_city=$row1['ds_add_city'];//ciudad
            $ds_add_state=$row1['ds_add_state'];//estado
            $ds_sin=!empty($row1['ds_sin'])?$row1['ds_sin']:"000000000";
            $ds_add_zip=$row1['ds_add_zip'];
            $fe_inicio_term=$row1['fe_start_date'];
            $no_semanas=$row1['no_semanas'];
            $cl_sesion=$row1['cl_sesion'];
            $ds_duracion=$row1['ds_duracion'];
            $fe_fin_=$row1['fe_fin'];

            #Recuperamos el mes y el anio de inicio.
            $data=explode('-',$fe_inicio_term);
            $start_year=$data[0];
            $start_month=$data[1];

            #Recuperamos el mes y anio de fin de curso.
            $data2=explode('-',$fe_fin_);
            $end_year=$data2[0];
            $end_month=$data2[1];


            $Queryk="SELECT ds_fname,ds_mname FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion' ";
            $rowk=RecuperaValor($Queryk);
            $ds_fname=$rowk['ds_fname'];
            $ds_mname=$rowk['ds_mname'];


            if($ds_duracion==12)//Aunque haya sido fullpayment se divide en 4 trimestres.
            $fg_division_pagos=4;
            #seDetermina si son 1 3 12 mese etc. en trismestres.
            $no_meses=$ds_duracion;
            if($no_meses>3)$no_meses=3;


            #Recuperamos el estado.
            $Querye="SELECT ds_provincia,ds_abreviada,fl_pais FROM k_provincias WHERE fl_provincia=$ds_add_state ";
            $rowe=RecuperaValor($Querye);
            $ds_estado=$rowe[0];
            $ds_code_state=$rowe[1];
            $fl_pais=$rowe[2];

            if($fl_pais==38){
                $ds_country_code="CAN";
            }else{
                $ds_country_code="USA";
            }

            $ds_direccion=$ds_add_street;


            $Querye="SELECT fg_aboriginal,fg_opcion_pago FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
            $rok=RecuperaValor($Querye);
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
            $from_moth_1=NULL;
            $from_moth_2=NULL;
            $from_moth_3=NULL;
            $from_moth_4=NULL;
            $to_moth_1=NULL;
            $to_moth_2=NULL;
            $to_moth_3=NULL;
            $to_moth_4=NULL;
            $from_year_period_1=NULL;
            $from_year_period_2=NULL;
            $from_year_period_3=NULL;
            $from_year_period_4=NULL;
            $to_year_period_1=NULL;
            $to_year_period_2=NULL;
            $to_year_period_3=NULL;
            $to_year_period_4=NULL;
            $no_pagos_opcion=NULL;
            $contador_refunds=NULL;
            $monto=NULL;
            $full_time_1=NULL;
            $part_time_1=NULL;
            $full_time_2=NULL;
            $full_time_3=NULL;
            $full_time_4=NULL;
            $partime_2=NULL;
            $partime_3=NULL;
            $partime_4=NULL;



            # Le sumamos lo numero de meses a la fecha inicial para obtener el fecha final
            # Calculamos la cantidad que se paga por mes
            $fe_inicio1 = DATE_FORMAT(date_create($fe_inicio_term), 'Y-m-d');
            $mes_inicio1 = DATE_FORMAT(date_create($fe_inicio_term), 'm');
            $anio_inicio1 = DATE_FORMAT(date_create($fe_inicio_term), 'Y');
            $meses = ($no_semanas / 4);
            $fe_nueva = strtotime('+ ' . ($meses - 1) . ' month', strtotime($fe_inicio1));
            $fe_fin1 = date('Y-m-d', $fe_nueva);
            $mes_fin1 = date('m', $fe_nueva);
            $anio_fin1 = date('Y', $fe_nueva);
            $anios1 = $anio_fin1 - $anio_inicio1;

            $start_month=DATE_FORMAT(date_create($fe_inicio_term), 'ym');;
            $end_month=substr($end_year,2,2)."".$end_month;


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
                    $monto = $monto;

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
            $full_time_1="";
            $part_time_1="";
            # Obtenemos el full o part time del programal
            $Query = "SELECT fg_fulltime FROM c_programa WHERE fl_programa=$fl_programa ";
            $row = RecuperaValor($Query);
            if($row[0]==1){
                $full_time_1=$num_meses_anio;

                //PARA SABER EL NUMERO DE MESES MAXIMO
                if($num_meses_anio>$max_numero_mes){

                    $max_meses_full_time=$num_meses_anio;
                }
                $max_numero_mes=$num_meses_anio;

            }else{
                $part_time_1=$num_meses_anio;

                //PARA SABER EL NUMERO DE MESES MAXIMO
                if($num_meses_anio>$max_numero_mes){

                    $max_meses_part_time=$num_meses_anio;
                }
                $max_numero_mes=$num_meses_anio;
            }


            #En total son 3 pagos verificamos que existan y que pertenescan en el anio fiscal.
            switch($ds_duracion){

                case "1":
                    $from_moth_1=$start_month;
                    $to_moth_1=$end_month;
                    $from_year_period_1=$anio_inicio1;
                    $to_year_period_1=$anio_inicio1;
                    $mn_pago_1=$mn_due_tax;

                    break;
                case "3":
                    $from_moth_1=$start_month;
                    $to_moth_1=$end_month;
                    $from_year_period_1=$anio_inicio1;
                    $to_year_period_1=$anio_inicio1;
                    $mn_pago_1=$mn_due_tax;
                    break;
                case "6":

                    break;
                case "12":

                    //SE DIVIDIE LOS PAGOS EN TRIMESTRES.

                    #Verificamos los pagos del alumno.
                    $Queryp="SELECT a.fl_alumno,a.fl_term_pago, a.fe_pago,a.mn_pagado,b.fe_pago fe_pago_programada,
                        DATE_FORMAT(b.fe_ini_pago,'%M') fe_ini_pago,
                        DATE_FORMAT(b.fe_fin_pago,'%M') fe_fin_pago,
                        DATE_FORMAT(b.fe_ini_pago,'%Y') fe_ini_anio_pago,
                        DATE_FORMAT(b.fe_ini_pago,'%Y') fe_fin_anio_pago,
                        b.fe_ini_pago fecha_inicial, a.mn_late_fee

                            FROM k_alumno_pago a
                            JOIN k_term_pago b
                            ON a.fl_term_pago=b.fl_term_pago
                            WHERE a.fl_alumno=$fl_usuario AND DATE_FORMAT(a.fe_pago,'%Y')<=$start_year ";
                    $Queryp.="   AND DATE_FORMAT(b.fe_ini_pago,'%m')<12 ";//tiene ser menores de Diciembre
                    $rspay=EjecutaQuery($Queryp);$no_pagos=CuentaRegistros($rspay);
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

                        switch($jp) {
                            case 1:

                                #Sumar 3 meses a la fecha inicial
                                $fecha = date($fecha_inicial);
                                $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) );
                                $nuevafecha = date ( 'm' , $nuevafecha );

                                $mn_pago_1=$mn_pago;
                                $from_moth_1=substr($fe_anio_ini_pago,2,2)."".MesNumerico($fe_mes_ini_pago);
                                $to_moth_1=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;

                                $fecha=date($fecha_inicial);
                                $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                $fe_ini_pagos=$mes_siguiente;

                                if(!empty($full_time_1))
                                $full_time_1=3;
                                if(!empty($part_time_1))
                                $part_time_1=3;

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
                                $from_moth_2=substr($fe_anio_ini_pago,2,2)."".$NuevaFechaInicial;
                                $to_moth_2=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;


                                $fecha=date($fe_ini_pagos);
                                $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                $fe_ini_pagos=$mes_siguiente;

                                if(!empty($full_time_1))
                                    $full_time_2=3;
                                if(!empty($part_time_1))
                                    $part_time_2=3;



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
                                $from_moth_3=substr($fe_anio_ini_pago,2,2)."".$NuevaFechaInicial;
                                $to_moth_3=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;


                                $fecha=date($fe_ini_pagos);
                                $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                $fe_ini_pagos=$mes_siguiente;

                                if(!empty($full_time_1))
                                    $full_time_3=3;
                                if(!empty($part_time_1))
                                    $part_time_3=3;

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
                                $from_moth_4=substr($fe_anio_ini_pago,2,2)."".$NuevaFechaInicial;
                                $to_moth_4=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;


                                $fecha=date($fe_ini_pagos);
                                $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                $fe_ini_pagos=$mes_siguiente;

                                if(!empty($full_time_1))
                                    $full_time_4=3;
                                if(!empty($part_time_1))
                                    $part_time_4=3;

                                break;

                        }


                    }



                    //forzozamente hay que dividirlo en 4
                    if($no_pagos==1){

                        $Query="SELECT b.fl_term FROM k_alumno_pago a left JOIN k_term_pago b ON a.fl_term_pago=b.fl_term_pago WHERE a.fl_alumno=$fl_usuario AND fl_term IS NOT null ";
                        $rowps=RecuperaValor($Query);
                        $fl_term=$rowps['fl_term'];

                        $Query_ter=" SELECT fe_ini_pago fecha_inicial,fl_term_pago FROM k_term_pago WHERE fl_term=$fl_term AND no_opcion=3 AND DATE_FORMAT(fe_ini_pago,'%Y')<=$start_year ";
                        $rs_ter=EjecutaQuery($Query_ter);

                        if(($fg_division_pagos==4)&&($fg_opcion_pago==1)){#forzamos a 12 pagos

                            $Querycon="SELECT $mn_due  FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1' ";
                            $rowcon=RecuperaValor($Querycon);
                            $mn_pago=$rowcon[0]/4;
                        }
                        if(($fg_division_pagos==4)&&($fg_opcion_pago==2)){
                            #Dividimos el monto en 2
                            $mn_pago=$mn_pago/2;

                        }

                        for($jp1=1;$rowpag=RecuperaRegistro($rs_ter);$jp1++) {

                            $fecha_inicial=$rowpag['fecha_inicial'];
                            $fl_term_pago=$rowpag['fl_term_pago'];
                            #Obtenemos el anio de los pagos.
                            $fecha_ini=explode("-",$fecha_inicial);
                            $anio_fecha=$fecha_ini[0];

                            if($anio_fecha==$TaxionYear){
                                switch($jp1) {
                                    case 1:

                                        #Sumar 3 meses a la fecha inicial
                                        $fecha = date($fecha_inicial);
                                        $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) );
                                        $nuevafecha = date ( 'm' , $nuevafecha );

                                        $mn_pago_1=$mn_pago;
                                        $from_moth_1=substr($fe_anio_ini_pago,2,2)."".MesNumerico($fe_mes_ini_pago);
                                        $to_moth_1=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;



                                        $fecha=date($fecha_inicial);
                                        $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                        $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                        #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                        $fe_ini_pagos=$mes_siguiente;

                                        if(!empty($full_time_1))
                                            $full_time_1=3;
                                        if(!empty($part_time_1))
                                            $part_time_1=3;

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
                                        $from_moth_2=substr($fe_anio_ini_pago,2,2)."".$NuevaFechaInicial;
                                        $to_moth_2=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;


                                        $fecha=date($fe_ini_pagos);
                                        $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                        $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                        #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                        $fe_ini_pagos=$mes_siguiente;

                                        if(!empty($full_time_1))
                                            $full_time_2=3;
                                        if(!empty($part_time_1))
                                            $part_time_2=3;
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
                                        $from_moth_3=substr($fe_anio_ini_pago,2,2)."".$NuevaFechaInicial;
                                        $to_moth_3=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;


                                        $fecha=date($fe_ini_pagos);
                                        $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                        $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                        #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                        $fe_ini_pagos=$mes_siguiente;

                                        if(!empty($full_time_1))
                                            $full_time_3=3;
                                        if(!empty($part_time_1))
                                            $part_time_3=3;

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
                                        $from_moth_4=substr($fe_anio_ini_pago,2,2)."".$NuevaFechaInicial;
                                        $to_moth_4=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;


                                        $fecha=date($fe_ini_pagos);
                                        $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                        $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                        #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                        $fe_ini_pagos=$mes_siguiente;

                                        if(!empty($full_time_1))
                                            $full_time_4=3;
                                        if(!empty($part_time_1))
                                            $part_time_4=3;

                                        break;

                                }
                            }#END IF
                        }
                    }


                    /****************************************/
                    if(empty($no_pagos)){

                        $mn_pago=0;
                        $mn_pago_1=0;
                        $no_meses=0;
                        $full_time_1=NULL;
                        $part_time_1=NULL;
                        $to_moth_1=NULL;

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
                                        $to_moth_1=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;

                                        $fecha=date($fecha_inicial);
                                        $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                        $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                        #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                        $fe_ini_pagos=$mes_siguiente;

                                        $full_time_1=3;

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
                                        $from_moth_2=substr($fe_anio_ini_pago,2,2)."".$NuevaFechaInicial;
                                        $to_moth_2=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;


                                        $fecha=date($fe_ini_pagos);
                                        $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                        $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                        #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                        $fe_ini_pagos=$mes_siguiente;

                                        $full_time_2=3;

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
                                        $from_moth_3=substr($fe_anio_ini_pago,2,2)."".$NuevaFechaInicial;
                                        $to_moth_3=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;


                                        $fecha=date($fe_ini_pagos);
                                        $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                        $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                        #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                        $fe_ini_pagos=$mes_siguiente;

                                        $full_time_2=3;



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

                                        $mn_pago_4=$mn_pago_1;
                                        $from_moth_4=substr($fe_anio_ini_pago,2,2)."".$NuevaFechaInicial;
                                        $to_moth_4=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;


                                        $fecha=date($fe_ini_pagos);
                                        $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                        $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                        #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                        $fe_ini_pagos=$mes_siguiente;

                                        $full_time_2=3;
                                        break;
                                }


                            }#end for




                        }else{


                            #Dividimos su pagos en 3 solo presnetamos 1.
                            $Queryap="SELECT mn_pagado FROM k_alumno_pago WHERE fl_alumno=$fl_usuario ";
                            $rop=RecuperaValor($Queryap);
                            $mn_pago_1=$rop['mn_pagado']/$fg_opcion_pago;
                            if(!empty($mn_pago_1)){
                                $full_time_1=3;
                            }
                            #Sumar 3 meses a la fecha inicial
                            $fecha = date($fe_inicio_term);
                            $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) );
                            $nuevafecha = date ( 'm' , $nuevafecha );
                            $to_moth_1=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;

                            if(!empty($mn_pago_1)){
                                $no_meses=3;

                                for ($y1 = 1; $y1 <= $fg_opcion_pago; $y1++) {


                                    switch($y1) {
                                        case 1:
                                            #Sumar 3 meses a la fecha inicial
                                            $fecha = date($fe_inicio_term);
                                            $nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) );
                                            $nuevafecha = date ( 'm' , $nuevafecha );

                                            $from_moth_1=substr($fe_anio_ini_pago,2,2)."".MesNumerico($fe_mes_ini_pago);
                                            $to_moth_1=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;


                                            $fecha=date($fe_inicio_term);
                                            $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                            $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                            #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                            $fe_ini_pagos=$mes_siguiente;

                                            $full_time_1=3;

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
                                            $from_moth_2=substr($fe_anio_ini_pago,2,2)."".$NuevaFechaInicial;
                                            $to_moth_2=substr($fe_anio_fin_pago,2,2)."".$nuevafecha;

                                            $fecha=date($fe_ini_pagos);
                                            $mes_siguiente=strtotime('+3 month',strtotime($fecha));
                                            $mes_siguiente=date('Y-m-d',$mes_siguiente);
                                            #Guardamos el mes inicial para seguir el lapso de cuatrimestres-
                                            $fe_ini_pagos=$mes_siguiente;

                                            $full_time_2=3;

                                            break;

                                    }

                                }


                            }

                        }#end else fl_term

                    }




                    break;
            }

        if(!empty($from_moth_1)){

          $xml->startElement("T2202Slip");
            $xml->startElement("SlipReportTypeCode");//- Required 1 alpha character  - original = O  - amendment = A - cancel = C
            $xml->text(''.$fg_report_type_code.'');//original //amended
            $xml->endElement();


            $xml->startElement('FilerAccountNumber');
            $xml->text(''.ObtenConfiguracion(141).'');
            $xml->endElement();

            $xml->startElement('PostSecondaryEducationalSchoolProgramName');
            $xml->text(''.substr($nb_programa,0,30).'');
            $xml->endElement();

            $xml->startElement('PostSecondaryEducationalSchoolTypeCode');
            $xml->text(''.ObtenConfiguracion(140).'');
            $xml->endElement();

           // $xml->startElement('FlyingSchoolClubCourseTypeCode');
           // $xml->text('O');
           // $xml->endElement();

            $xml->startElement('StudentName');
                $xml->startElement('FamilyName');
                $xml->text(''.substr($ds_lname,0,20).'');//only 20 characteres
                $xml->endElement();

                $xml->startElement('GivenName'); //12 alphanumeric characters
                $xml->text(''.substr($ds_fname,0,12).'');
                $xml->endElement();

                //$xml->startElement('NameInitialText'); //1 alphanumeric characters
                //$xml->text(''.substr($ds_mname,0,1).'');
                //$xml->endElement();
            $xml->endElement();


            $xml->startElement('SocialInsuranceNumber');
            $xml->text(''.$ds_sin.'');
            $xml->endElement();

            $xml->startElement('StudentNumber'); //20 alphanumeric characters
            $xml->text(''.substr($ds_login,0,20).'');
            $xml->endElement();

            $ds_direccion_1=substr($ds_direccion,0,30);
            $ds_direccion_2=substr($ds_direccion,30,60);

            $xml->startElement('StudentAddress');
            if(!empty($ds_direccion_1)){
                $xml->startElement('AddressLine1Text'); //30 alphanumeric characters
                $xml->text(''.$ds_direccion_1.'');
                $xml->endElement();
            }
            if(!empty($ds_direccion_2)){
                $xml->startElement('AddressLine2Text'); //30 alphanumeric characters continue direction
                $xml->text(''.$ds_direccion_2.'');
                $xml->endElement();
            }
                $xml->startElement('CityName'); //28 alphanumeric characters continue ciudad
                $xml->text(''.substr($ds_add_city,0,28).'');
                $xml->endElement();

                $xml->startElement('ProvinceStateCode'); //2 characters
                $xml->text($ds_code_state);
                $xml->endElement();

                $xml->startElement('CountryCode'); //3 alpha characters
                $xml->text($ds_country_code);
                $xml->endElement();

                //QUITAMOS ESPACIOS EN BLANCO
                $ds_add_zip=str_replace(" ","",$ds_add_zip);
                $xml->startElement('PostalZipCode');
                $xml->text($ds_add_zip);
                $xml->endElement();
            $xml->endElement();


            $xml->startElement('SchoolSession');
                //No de meses y totales por cada estudiante.
                $xml->startElement('StartYearMonth');  // 4 numeric YYMM format
                $xml->text(''.$from_moth_1.'');
                $xml->endElement();

                $xml->startElement('EndYearMonth');// 4 numeric YYMM format
                $xml->text(''.$to_moth_1.'');
                $xml->endElement();

                $xml->startElement('EligibleTuitionFeeAmount'); //11 nume   9 numeric +  2 cent
                $xml->text(''.number_format($mn_pago_1,2,'.','').'');
                $xml->endElement();

                if(empty($part_time_1)){
                    $part_time_1="00";
                }
                    $xml->startElement('PartTimeStudentMonthCount');
                    $xml->text(''.str_pad($part_time_1,2,"0",STR_PAD_LEFT).'');
                    $xml->endElement();

                if(!empty($full_time_1)){
                    $xml->startElement('FullTimeStudentMonthCount');
                    $xml->text(''.str_pad($full_time_1,2,"0",STR_PAD_LEFT).'');
                    $xml->endElement();
                }
            $xml->endElement();


                if(!empty($from_moth_2)){
                    $xml->startElement('SchoolSession');
                        //No de meses y totales por cada estudiante.
                        $xml->startElement('StartYearMonth');  // 4 numeric YYMM format
                        $xml->text(''.$from_moth_2.'');
                        $xml->endElement();


                    if(!empty($to_moth_2)){
                        $xml->startElement('EndYearMonth');// 4 numeric YYMM format
                        $xml->text(''.$to_moth_2.'');
                        $xml->endElement();
                    }

                    if(!empty($mn_pago_2)){ //si viene pago 2 quiere decir que si hay data
                        $xml->startElement('EligibleTuitionFeeAmount');
                        $xml->text(''.number_format($mn_pago_2,2,'.','').'');
                        $xml->endElement();

                        if(empty($part_time_2)){
                            $part_time_2=00;
                        }
                        $xml->startElement('PartTimeStudentMonthCount');
                        $xml->text(''.str_pad($part_time_2,2,"0",STR_PAD_LEFT).'');
                        $xml->endElement();
                    }
                    if(!empty($full_time_2)){
                        $xml->startElement('FullTimeStudentMonthCount');
                        $xml->text(''.str_pad($full_time_2,2,"0",STR_PAD_LEFT).'');
                        $xml->endElement();
                    }

                    $xml->endElement();//school sesion
                }

                if(!empty($from_moth_3)){
                    $xml->startElement('SchoolSession');

                        //No de meses y totales por cada estudiante.
                        $xml->startElement('StartYearMonth');  // 4 numeric YYMM format
                        $xml->text(''.$from_moth_3.'');
                        $xml->endElement();

                    if(!empty($to_moth_3)){
                        $xml->startElement('EndYearMonth');// 4 numeric YYMM format
                        $xml->text(''.$to_moth_3.'');
                        $xml->endElement();
                    }
                    if(!empty($mn_pago_3)){
                        $xml->startElement('EligibleTuitionFeeAmount');
                        $xml->text(''.number_format($mn_pago_3,2,'.','').'');
                        $xml->endElement();

                        if(empty($part_time_3)){
                            $part_time_3="00";
                        }
                        $xml->startElement('PartTimeStudentMonthCount');
                        $xml->text(''.str_pad($part_time_3,2,"0",STR_PAD_LEFT).'');
                        $xml->endElement();

                    }
                    if(!empty($full_time_3)){
                        $xml->startElement('FullTimeStudentMonthCount');
                        $xml->text(''.str_pad($full_time_3,2,"0",STR_PAD_LEFT).'');
                        $xml->endElement();
                    }
                    $xml->endElement();
                }

                if(!empty($from_moth_4)){
                    $xml->startElement('SchoolSession');
                    //No de meses y totales por cada estudiante.
                    $xml->startElement('StartYearMonth');  // 4 numeric YYMM format
                    $xml->text(''.$from_moth_4.'');
                    $xml->endElement();


                    if(!empty($to_moth_4)){
                        $xml->startElement('EndYearMonth');// 4 numeric YYMM format
                        $xml->text(''.$to_moth_4.'');
                        $xml->endElement();
                    }
                    if(!empty($mn_pago_4)){
                        $xml->startElement('EligibleTuitionFeeAmount');
                        $xml->text(''.number_format($mn_pago_4,2,'.','').'');
                        $xml->endElement();


                        if(empty($part_time_4)){
                            $part_time_4="00";
                        }
                        $xml->startElement('PartTimeStudentMonthCount');
                        $xml->text(''.str_pad($part_time_4,2,"0",STR_PAD_LEFT).'');
                        $xml->endElement();

                    }
                    if(!empty($full_time_4)){
                        $xml->startElement('FullTimeStudentMonthCount');
                        $xml->text(''.str_pad($full_time_4,2,"0",STR_PAD_LEFT).'');
                        $xml->endElement();
                    }
                    $xml->endElement();//end school sesion
                }




            $total_part_time=$part_time_1+$part_time_2+$part_time_3+$part_time_4;
            $total_full_time=$full_time_1+$full_time_2+$full_time_3+$full_time_4;
            $mn_total_student=$mn_pago_1+$mn_pago_2+$mn_pago_3+$mn_pago_4;


            //totales por cada estudiante.
            $xml->startElement('TotalEligibleTuitionFeeAmount');  // 13 numeric (11 dollars and 2 cents)
            $xml->text(''.number_format($mn_total_student,2,'.','').'');
            $xml->endElement();

            if(empty($total_part_time)){
                $total_part_time="00";
            }
                $xml->startElement('TotalPartTimeStudentMonthCount');  //
                $xml->text(''.str_pad($total_part_time,2,"0",STR_PAD_LEFT).'');
                $xml->endElement();

            if(!empty($total_full_time)){
                $xml->startElement('TotalFullTimeStudentMonthCount');  //
                $xml->text(''.str_pad($total_full_time,2,"0",STR_PAD_LEFT).'');
                $xml->endElement();
            }
           //Obtenemos el acumulado
            $mn_total_total += $mn_total_student;


         $xml->endElement(); //fin T2202Slip
        }//en if($from_moth_1)




        }



    $xml->startElement("T2202Summary");

            $xml->startElement('FilerAccountNumber');
            $xml->text(''.$file_account_number.'');
            $xml->endElement();

            $xml->startElement('SummaryReportTypeCode');
            $xml->text(''.$fg_report_type_code.'');
            $xml->endElement();

            if($fg_report_type_code=='A'){
                $xml->startElement('FilerAmendmentNote');
                $xml->text(''.$filer_amendment_note.'');
                $xml->endElement();
            }

            $xml->startElement('TaxationYear');
            $xml->text(''.$TaxionYear.'');
            $xml->endElement();

            $xml->startElement('TotalSlipCount');
            $xml->text(''.str_pad($no_alumnos_procesados, 7, "0", STR_PAD_LEFT).'');
            $xml->endElement();


            $post_secondary_educational_institution_name_1=substr($post_secondary_educational_institution_name,0,30);
            $post_secondary_educational_institution_name_2=substr($post_secondary_educational_institution_name,30,30);
            $post_secondary_educational_institution_name_3=substr($post_secondary_educational_institution_name,60,30);


            $xml->startElement('PostSecondaryEducationalInstitutionName');
                $xml->startElement('NameLine1Text');
                $xml->text(''.$post_secondary_educational_institution_name_1.'');
                $xml->endElement();

                if(!empty($post_secondary_educational_institution_name_2)){
                    $xml->startElement('NameLine2Text');
                    $xml->text(''.$post_secondary_educational_institution_name_2.'');
                    $xml->endElement();
                }
                if(!empty($post_secondary_educational_institution_name_3)){
                    $xml->startElement('NameLine3Text');
                    $xml->text(''.$post_secondary_educational_institution_name_3.'');
                    $xml->endElement();
                }
            $xml->endElement();


            $post_secondary_educational_institution_mailing_address_1=substr($post_secondary_educational_institution_mailing_address,0,30);
            $post_secondary_educational_institution_mailing_address_2=substr($post_secondary_educational_institution_mailing_address,30,30);
            $post_secondary_educational_institution_mailing_address_3=substr($post_secondary_educational_institution_mailing_address,60,30);


            $xml->startElement('PostSecondaryEducationalInstitutionMailingAddress');
                $xml->startElement('AddressLine1Text');
                $xml->text(''.$post_secondary_educational_institution_mailing_address_1.'');
                $xml->endElement();
                if(!empty($post_secondary_educational_institution_mailing_address_2)){
                    $xml->startElement('AddressLine2Text');
                    $xml->text(''.$post_secondary_educational_institution_mailing_address_2.'');
                    $xml->endElement();
                }
                if(!empty($post_secondary_educational_institution_mailing_address_3)){
                    $xml->startElement('AddressLine3Text');
                    $xml->text(''.$post_secondary_educational_institution_mailing_address_3.'');
                    $xml->endElement();
                }
                $xml->startElement('CityName');
                $xml->text(''.$city_name.'');
                $xml->endElement();

                $xml->startElement('ProvinceStateCode');
                $xml->text(''.$province_state_code.'');
                $xml->endElement();

                $xml->startElement('CountryCode');
                $xml->text(''.$country_code.'');
                $xml->endElement();

                //QUITAMOS ESPACIOS EN BLANCO.
                $postal_zip_code=str_replace(" ","",$postal_zip_code);

                $xml->startElement('PostalZipCode');
                $xml->text(''.$postal_zip_code.'');
                $xml->endElement();
            $xml->endElement();

            $xml->startElement('ContactInformation');

                $xml->startElement('ContactName');
                $xml->text(''.$contact_name.'');
                $xml->endElement();

                $xml->startElement('ContactAreaCode');
                $xml->text(''.$contact_area_code.'');
                $xml->endElement();

                $xml->startElement('ContactPhoneNumber');
                $xml->text(''.$contact_phone_number.'');
                $xml->endElement();
                if(!empty($contact_extension_number)){
                    $xml->startElement('ContactExtensionNumber');
                    $xml->text(''.$contact_extension_number.'');
                    $xml->endElement();
                }

            $xml->endElement();

            $xml->startElement('TotalEligibleTuitionFeeAmount');//15 num 2 decim
            $xml->text(''.number_format($mn_total_total,2,'.','').'');
            $xml->endElement();

    $xml->endElement();//fin T2202SummaryType
$xml->endElement(); //fin T2202
$xml->endElement(); //fin Return
$xml->endElement(); //fin submisiion

   $content = $xml->outputMemory();
   ob_end_clean();
   ob_start();
   header('Content-Type: application/xml; charset=UTF-8');

   header('Content-Encoding: UTF-8');
   header("Content-Disposition: attachment;filename=t2202_Vancouver_Animation_School_".$TaxionYear.".xml");
   header('Expires: 0');
   header('Pragma: cache');
   header('Cache-Control: private');
   echo $content;





?>