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


  if($fe_ini){
      #Damos formato de fecha alos parametros recibidos.
      $fe_ini =strtotime('0 days',strtotime($fe_ini));
      $fecha1= date('Y-m-d',$fe_ini);
  }
  if($fe_dos){
      $fe_dos=strtotime('0 days',strtotime($fe_dos));
      $fecha2= date('Y-m-d',$fe_dos);
  }


  //$spreadsheet = IOFactory::load('ID-03642-Vancouver_Animation_School_2020-10-28-Current_correct.xlsx');
  $spreadsheet = new Spreadsheet();


  $spreadsheet->setActiveSheetIndex(0)
      ->setCellValue('A1','Student ID Number')
      ->setCellValue('C1','Usual First name')
      ->setCellValue('D1','First Name')
      ->setCellValue('E1','Middle Name')
      ->setCellValue('F1','Last Name')
      ->setCellValue('G1','Birth Date (YYYY-MM-DD)')
      ->setCellValue('H1','Gender')
      ->setCellValue('I1','Immigration Status')
      ->setCellValue('J1','Citizenship Code')
      ->setCellValue('K1','Disability Y/N (opt)')
      ->setCellValue('L1','Aboriginal Y/N (opt)')
      ->setCellValue('M1','First Nations Y/N (opt)')
      ->setCellValue('N1','Metis Y/N (opt)')
      ->setCellValue('O1','"Inuit Y/N (opt)')
      ->setCellValue('P1','Permanent Address Line 1') //adress 1
      ->setCellValue('Q1','Permanent Address Line 2') //adress 2
      ->setCellValue('R1','City') //City
      ->setCellValue('S1','Province / State') //Province/state code
      ->setCellValue('T1','Country') //Country code
      ->setCellValue('U1','Postal Code') //Postal code
      ->setCellValue('V1','Current Phone Number') //Current Phone numer
      ->setCellValue('X1','Email address 1')//email
      ->setCellValue('Y1','Delete Flag');//Delete Flag



     #Muestra resultados de la busqueda.
  $Query ='
    SELECT Main.*,
           Main.nb_usuario "name",
           CONCAT_WS(" ", Main.ds_add_city, Main.nb_zona_horaria) "country",
           CONCAT_WS(" ", Main.nb_programa, "Term:", Main.no_grado) "program",
           Main.status_label "status",

           "teachers" "teachers"
           ,fl_pais_campus

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
                  WHEN PCTIA.fg_graduacion LIKE "1" THEN "Credential"
                  WHEN PCTIA.fg_dismissed LIKE "1" THEN "Dismissed"
	          WHEN PCTIA.fg_desercion LIKE "1" THEN "Withdrawal"
	          WHEN Usuario.fg_activo LIKE "1" THEN "Active"
                  ELSE "Not Set"
                  END status_label,
                  CASE WHEN Grupo.fl_term >0 THEN Grupo.fl_term ELSE 0 END fl_term,
                  Form1.fl_programa,
                  CASE WHEN Term.no_grado >0 THEN Term.no_grado ELSE 0 END no_grado,
            (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= Alumno.no_promedio_t AND no_max >= Alumno.no_promedio_t LIMIT 1) cl_calificacion,
            Alumno.mn_progreso, Programa.ds_duracion,Alumno.fg_absence, Alumno.fg_change_status ,Form1.ds_add_country,Form1.ds_number,Form1.ds_add_street,Form1.fg_disability,Form1.ds_add_zip,Programa.no_ptib,Usuario.ds_graduate_status,Form1.fl_immigrations_status,ProgCosto.cl_delivery,ProgCosto.ds_credential
           ,USesion.fl_pais_campus

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
      $Query.=' AND fe_start_date>= STR_TO_DATE("'.$fecha1.'", "%Y-%m-%d") ';
  }
  if(!empty($fe_dos)){
      $Query.=' AND fe_start_date<= STR_TO_DATE("'.$fecha2.'", "%Y-%m-%d") '; /*estaba como fe fin*/
  }
/*if (!empty($fe_ini)) {
    $Query .= ' AND fe_fin>= STR_TO_DATE("' . $fecha1 . '", "%Y-%m-%d") ';
}
if (!empty($fe_dos)) {
    $Query .= ' AND fe_fin<= STR_TO_DATE("' . $fecha2 . '", "%Y-%m-%d") '; estaba como fe fin
}*/
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


  $Query.=' AND (fl_pais_campus=38 OR fl_pais_campus IS NULL)
    ORDER BY Main.fe_alta DESC
';


  $rs = EjecutaQuery($Query);
  $rs2 =EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
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
      $fe_completado=$row['fe_completado'];
      $fe_graduacion=$row['fe_graduacion'];
      $fg_certificado=$row['fg_certificado'];
      $fe_start_date=$row['fe_start_date'];
      $status_label=$row['status_label'];
      $mn_progreso=$row['mn_progreso'];
      $fg_disability=!empty($row['fg_disability'])?"Y":"N";
      $ds_add_zip=$row['ds_add_zip'];
      $fl_immigrations_status=$row['fl_immigrations_status'];

      $Query="SELECT no_code FROM immigrations_status WHERE fl_immigrations_status=$fl_immigrations_status ";
      $roc=RecuperaValor($Query);
      $code_immigration_status=$roc[0];

      $Queryo="SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$fl_usuario ";
      $rop=RecuperaValor($Queryo);
      $cl_sesion=$rop['cl_sesion'];

      $Query="SELECT fg_aboriginal,DATE_FORMAT(fe_firma, '%Y-%m-%d') AS fe_firma FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
      $rok=RecuperaValor($Query);
      $fg_aboriginal=!empty($rok['fg_aboriginal'])?"Y":"N";
      $fe_firma = $rok['fe_firma'];

      #Recupermaos el pais .
      $QueryP="SELECT cl_iso2 FROM c_pais WHERE fl_pais=$ds_add_country ";
      $rowe=RecuperaValor($QueryP);
      $citizen_code=$rowe['cl_iso2'];

      if($ds_add_country==38 && $code_immigration_status=='UN'){
          $code_immigration_status="00";

      }
      if($ds_add_country==38 && (empty($code_immigration_status))){
          $code_immigration_status="00";
      }
      if($code_immigration_status=='UN'){
          $code_immigration_status="00";
      }
      if ($ds_add_country <> 38)
      {
        $code_immigration_status = 'UN';
      }
      #Verifica y obtiene el code del estado.(canada)
          $Qury="SELECT ds_abreviada FROM k_provincias WHERE fl_provincia=$ds_add_state ";
      $rol=RecuperaValor($Qury);
      $ds_code_state=$rol[0];

	  if($ds_add_country<>38)
	  {
		  $ds_code_state="ZY";
          $citizen_code="ZY";
	  }


      $spreadsheet->setActiveSheetIndex(0)


                            ->setCellValue('A'.$i.'',''.$ds_login.'') //Student id
                            ->setCellValue('B'.$i.'', '') //
                            ->setCellValue('C'.$i.'',''.$ds_nombre.'') //Usual Student first name
                            ->setCellValue('D'.$i.'',$ds_nombre) //First name
                            ->setCellValue('E'.$i.'',''.$ds_amaterno.'') //Middle name
                            ->setCellValue('F'.$i.'',''.$ds_apaterno.'') //Last name
                            ->setCellValue('G'.$i.'',''.$fe_birth.'') //Birth date
                            ->setCellValue('H'.$i.'',''.$fe_gender.'') //gender
                            ->setCellValue('I'.$i.'',''.$code_immigration_status.'') //$code_immigration_status
                            ->setCellValue('J'.$i.'',''.$citizen_code.'') //cityzenship code
                            ->setCellValue('K'.$i.'',''.$fg_disability.'') //disability
                            ->setCellValue('L'.$i.'',''.$fg_aboriginal.'') //aboriginal
                            ->setCellValue('M'.$i.'',''.$fg_aboriginal.'') //FIRST NATION
                            ->setCellValue('N'.$i.'',''.$fg_aboriginal.'') //mETIS
                            ->setCellValue('O'.$i.'',''.$fg_aboriginal.'') //iNUIT
                            ->setCellValue('P'.$i.'',''.$ds_add_street.'') //adress 1
                            ->setCellValue('Q'.$i.'','') //adress 2
                            ->setCellValue('R'.$i.'',''.$ds_add_city.'') //City
                            ->setCellValue('S'.$i.'',''.$ds_code_state.'') //Province/state code
                            ->setCellValue('T'.$i.'',''.$citizen_code.'') //Country code
                            ->setCellValue('U'.$i.'',''.$ds_add_zip.'') //Postal code
                            ->setCellValue('V'.$i.'',''.$ds_number.'') //Current Phone numer
                            ->setCellValue('X'.$i.'',''.$ds_email.'')//email
                            ->setCellValue('Y'.$i.'','');//delete code.

  }

  // Rename sheet
  $spreadsheet->getActiveSheet(0)->setTitle('Student');

  // Create a new worksheet, after the default sheet
  $spreadsheet->createSheet();

  $spreadsheet->setActiveSheetIndex(1)
      ->setCellValue('A1','Program Location & Title')
      ->setCellValue('B1', 'Student ID Number') //Student id
      ->setCellValue('C1','Student Name') //name complete
      ->setCellValue('D1','Full Time Flag (Y/N)') //Full time
      ->setCellValue('E1', 'Method Of Delivery') //start date
      ->setCellValue('F1', 'Date Student Erolled') //end date
      ->setCellValue('G1','Student Start Date (YYYY-MM-DD)') //start date
      ->setCellValue('H1','Student End Date (YYYY-MM-DD)') //end date
      ->setCellValue('I1','Program Achievement Status') //program archivenment status
      ->setCellValue('J1','Graduate Follow Up Date (YYYY-MM-DD)') //graduated follow date
      ->setCellValue('K1','Follow Up Type') //follow_type
      ->setCellValue('L1','Job Title')
      ->setCellValue('M1','Registered,Licensed,certified')
      ->setCellValue('N1','Delete Flag');


  //generamos la segunda hoja.
  for($m=2;$roz=RecuperaRegistro($rs2);$m++) {
      $fl_usuario=$roz['fl_usuario'];
      $ds_login=$roz['ds_login'];
      $nb_usuario=html_entity_decode($roz['nb_usuario']);
      $ds_nombre=html_entity_decode($roz['ds_nombres']);
      $ds_apaterno=html_entity_decode($roz['ds_apaterno']);
      $ds_amaterno=html_entity_decode($roz['ds_amaterno']);
      $ds_add_city=$roz['ds_add_city'];
      $ds_pais=html_entity_decode($roz['ds_pais']);
      $nb_zona_horaria=$roz['nb_zona_horaria'];
      $nb_programa=html_entity_decode($roz['nb_programa']);
      $ds_duracion=$roz['ds_duracion'];
      $fl_programa=$roz['fl_programa'];
      $fe_birth=$roz['fe_nacimiento'];
      $fe_gender=$roz['fg_genero'];
      $ds_add_country=$roz['ds_add_country'];
      $ds_number=$roz['ds_number'];
      $ds_email=$roz['ds_email'];
      $ds_add_street=html_entity_decode($roz['ds_add_street']);
      $ds_add_state=html_entity_decode($roz['ds_add_state']);
      $status=$roz['status'];
      $fe_completado=$roz['fe_completado'];
      $fe_graduacion=$roz['fe_graduacion'];
      $fg_certificado=$roz['fg_certificado'];
      $fe_start_date=$roz['fe_start_date'];
      $status_label=$roz['status_label'];
      $mn_progreso=$roz['mn_progreso'];
      $no_ptib=$roz['no_ptib'];
      $ds_graduate_status=$roz['ds_graduate_status'];
      #Recupermaos el pais .
      $QueryP="SELECT cl_iso2 FROM c_pais WHERE fl_pais=$ds_add_country ";
      $rowe=RecuperaValor($QueryP);
      $citizen_code=$rowe['cl_iso2'];

      $Querypr = "SELECT fg_payment FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
      $rowpr = RecuperaValor($Querypr);
      $ds_tipo = ($rowpr['fg_payment']=='C')?'Combined':'Distance';

      #Verifica y obtiene el code del estado.(canada)
      $Qury="SELECT ds_abreviada FROM k_provincias WHERE fl_provincia=$ds_add_state ";
      $rol=RecuperaValor($Qury);
      $ds_code_state=$rol[0];

      $Queryo="SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$fl_usuario ";
      $rop=RecuperaValor($Queryo);
      $cl_sesion=$rop['cl_sesion'];

      $Query = "SELECT fg_aboriginal,DATE_FORMAT(fe_firma, '%Y-%m-%d') AS fe_firma FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
      $rok = RecuperaValor($Query);
      $fg_aboriginal = !empty($rok['fg_aboriginal']) ? "Y" : "N";
      $fe_firma = $rok['fe_firma'];

      #
      $Querygra="SELECT fe_graduacion FROM k_pctia WHERE fl_alumno=$fl_usuario ";
      $rowgra=RecuperaValor($Querygra);
      $fe_graduacion=$rowgra['fe_graduacion'];

      $Querys="SELECT ds_graduate_status,job_title FROM k_ses_app_frm_1 where cl_sesion='$cl_sesion' ";
      $rows=RecuperaValor($Querys);
      $ds_graduate_status=$rows['ds_graduate_status'];
      $job_title=$rows['job_title'];

      $registered_Licensed_certified=null;
      $delete_Flag=null;

      #si la fecha de graduacion es posterior a la fecha del reporte entonces el no estan graduados aún en ese lapso.
      if($fe_graduacion > $fecha2)
      {
          $status="In Progress";
          $fe_completado=null;
          $fe_graduacion=null;
          $ds_graduate_status=null;
          $job_title=null;
      }
	  if($status=='Active')
	  {
		 $status="In Progress";
		 $fe_completado=null;
         $fe_graduacion=null;
         $ds_graduate_status=null;
         $job_title=null;
	  }



      switch ($ds_graduate_status)
        {
            case '1':
                $ds_follow_type="Employment in career";
            break;
            case '2':
                $ds_follow_type="Employment not in career";
            break;
            case '3':
                $ds_follow_type="Not Employed";
                break;
            case '4':
                $ds_follow_type="Enrolled in further training";
                break;
            case '5':
                $ds_follow_type="International Student returned  to country of citizenship";
                break;
            case '6':
                $ds_follow_type="International Student employed in non-origin country";
                break;
            case '7':
                $ds_follow_type="No possible contact after 3 attempts";
                break;
            default:
                $ds_follow_type="";
                break;
        }







      $name_school="Vancouver Animation School - Burnaby - 270 - 5489 Byrne Road[ID-03642L0002]::".$nb_programa."[ID-".$no_ptib."]";

      if($ds_amaterno){
          $ds_apellidos=$ds_apaterno." ".$ds_amaterno;
      }else
          $ds_apellidos=$ds_apaterno;



      //pasa editra hoja 2
      $spreadsheet->setActiveSheetIndex(1)
       ->setCellValue('A'.$m.'',''.$name_school.'') //Student id
       ->setCellValue('B'.$m.'', ''.$ds_login.'') //Student id
       ->setCellValue('C'.$m.'',''.$ds_apellidos.', '.$ds_nombre.'') //name complete
       ->setCellValue('D'.$m.'','Y') //Full time
       ->setCellValue('E' . $m . '', $ds_tipo) //Method Of Delivery
       ->setCellValue('F' . $m . '', $fe_firma) //Date Student Enrolled
       ->setCellValue('G'.$m.'',''.$fe_start_date.'') //start date
       ->setCellValue('H'.$m.'',''.$fe_completado.'') //end date
       ->setCellValue('I'.$m.'',''.$status.'') //program archivenment status
       ->setCellValue('J'.$m.'',''.$fe_graduacion.'') //graduated follow date
       ->setCellValue('K'.$m.'',''.$ds_follow_type.'') //follow_type
       ->setCellValue('L'.$m.'',''.$job_title.'')
       ->setCellValue('M'.$m.'',''.$registered_Licensed_certified.'')
       ->setCellValue('N'.$m.'',''.$delete_Flag.'');


  }
  // Rename sheet
  $spreadsheet->getActiveSheet(1)->setTitle('Enrollment');




  //salida del excel.
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename="Student_data_report.xls"');
  $writer = IOFactory::createWriter($spreadsheet, 'Xls');
  $writer->save('php://output');


?>