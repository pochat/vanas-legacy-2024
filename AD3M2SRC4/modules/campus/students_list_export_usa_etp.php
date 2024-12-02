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
      ->setCellValue('A1','Location')
      ->setCellValue('B1','ProgramTitle')
      ->setCellValue('C1','ProgramAwardType')
      ->setCellValue('D1','StudentLastName')
      ->setCellValue('E1','StudentFirstName')
      ->setCellValue('F1','MI')
      ->setCellValue('G1','Address')
      ->setCellValue('H1','City')
      ->setCellValue('I1','State')
      ->setCellValue('J1','ZipCode')
      ->setCellValue('K1','Phone')
      ->setCellValue('L1','SSN')
      ->setCellValue('M1','BirthDate')
      ->setCellValue('N1','Hispanic')
      ->setCellValue('O1','Race')
      ->setCellValue('P1','Gender')
      ->setCellValue('Q1','Disability')
      ->setCellValue('R1','Veteran')
      ->setCellValue('S1','PriorEducation')
      ->setCellValue('T1','StartDate')
      ->setCellValue('U1','ExitDate')
      ->setCellValue('V1','ProgramEnrollmentStatus')
      ->setCellValue('W1','EarnedAwardType')
      ->setCellValue('X1','GPA')
      ->setCellValue('Y1','PassFail');



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
            Alumno.mn_progreso, Programa.ds_duracion,Alumno.fg_absence, Alumno.fg_change_status ,Form1.ds_add_country,Form1.ds_number,Form1.ds_add_street,Form1.fg_disability,Form1.ds_add_zip,Programa.no_ptib,Usuario.ds_graduate_status,Form1.fl_immigrations_status,ProgCosto.cl_delivery,ProgCosto.ds_credential
          ,USesion.fl_pais_campus
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

          JOIN c_pais Pais ON(Pais.fl_pais = Form1.ds_add_country)
          LEFT JOIN k_pctia PCTIA ON (PCTIA.fl_alumno = Usuario.fl_usuario)
          WHERE Usuario.fl_perfil = 3 and USesion.fg_archive="0"
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


  $Query.=' AND fl_pais_campus=226
    ORDER BY Main.fe_alta DESC
';


  $rs = EjecutaQuery($Query);
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

      $fe_birth=strtotime('0 days',strtotime($fe_birth));
      $fe_birth= date('m/d/Y',$fe_birth);

      $fe_gender=$row['fg_genero'];
      $ds_add_country=html_entity_decode($row['ds_add_country']);
      $ds_number=$row['ds_number'];
      $ds_email=$row['ds_email'];
      $ds_add_street=html_entity_decode($row['ds_add_street']);
      $ds_add_state=$row['ds_add_state'];
      $status=$row['status'];
      $fe_completado=$row['fe_completado'];

      $fe_completado=strtotime('0 days',strtotime($fe_completado));
      $fe_completado= date('m/d/Y',$fe_completado);


      $fe_graduacion=$row['fe_graduacion'];
      $fg_certificado=$row['fg_certificado'];
      $fe_start_date=$row['fe_start_date'];

      $fe_start_date=strtotime('0 days',strtotime($fe_start_date));
      $fe_start_date= date('m/d/Y',$fe_start_date);

      $status_label=$row['status_label'];
      $mn_progreso=$row['mn_progreso'];
      $fg_disability=!empty($row['fg_disability'])?"1":"2";//1=Yes 2=No
      $ds_add_zip=$row['ds_add_zip'];
      $fl_immigrations_status=$row['fl_immigrations_status'];
      $ds_graduate_status=$row['ds_graduate_status'];


      if($row['fg_change_status']==1)
          $label_fg_change_status="<p><span class='label label-warning'>".ObtenEtiqueta(2059)."</span></p>";
      else
          $label_fg_change_status="";
      if($row['fg_absence']==1)
          $label_fg_absence="<p><span class='label label-warning'>".ObtenEtiqueta(2058)."</span></p>";
      else
          $label_fg_absence="";

      $Query="SELECT code FROM immigrations_status WHERE fl_immigrations_status=$fl_immigrations_status ";
      $roc=RecuperaValor($Query);
      $code_immigration_status=$roc[0];

      $Queryo="SELECT cl_sesion,fg_activo FROM c_usuario WHERE fl_usuario=$fl_usuario ";
      $rop=RecuperaValor($Queryo);
      $cl_sesion=$rop['cl_sesion'];
      $fg_activo=$rop['fg_activo'];

    $Query="SELECT ds_credential from k_programa_costos WHERE fl_programa=$fl_programa ";
    $rowc=RecuperaValor($Query);
    $program_award_type = $rowc['ds_credential'];


    $Query = "SELECT ds_sin,hispanic,race,military,grade FROM k_ses_app_frm_1 where cl_sesion='$cl_sesion' ";
    $rows = RecuperaValor($Query);
    $ds_sin=!empty($rows['ds_sin'])?$rows['ds_sin']:null;
      $hispanic=!empty($rows['hispanic'])?'1':'0';
      $race=$rows['race'];
      $fg_veteran=!empty($rows['military'])?'1':null;
      $grade=$rows['grade'];


      $Query00  = "SELECT MAX(a.fl_term), a.no_promedio FROM k_alumno_term a, k_term b ";
      $Query00 .= "WHERE a.fl_term = b.fl_term AND a.fl_alumno=".$fl_usuario." ORDER BY b.no_grado DESC";
      $row00 = RecuperaValor($Query00);
      $fl_term_max = !empty($row00[0])?$row00[0]:NULL;
	  $gpa=$row00[1];
	  if(empty($row00[1]))
	  {
		  # Obtener el promedio
		  $Query01 = "SELECT no_promedio FROM k_alumno_term WHERE fl_alumno=".$fl_usuario." AND fl_term=".$fl_term_max;
		  $row01 = RecuperaValor($Query01);
		  $gpa = $row01[0];
		  if(empty($gpa)){
			  $gpa=null;
		  }

	  }




      $row01 = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($gpa) AND no_max >= ROUND($gpa)");
      $fg_aprobado = $row01[1];

    if ($gpa > 0) {
        $gpa = ($gpa / 100) * 4;
        $gpa = number_format($gpa, 2);

    }

    //W=WHITE B=BLACK, A=America, H=Hawaian,  AS=asiatico, M=Multiracial, O=ther,
      switch($race){

          case'W':
              $race="1";
              break;
          case'B':
              $race="2";
              break;
          case'A':
              $race="4";
              break;
          case'H':
              $race="6";
              break;
          case'AS':
              $race="5";
              break;
          case'M':
              $race="7";
              break;
          case'O':
              $race="8";
              break;
          default:
              $race="9";//UKNOW
              break;


      }


    //`grade` ENUM('L','H','G','S','C','A','B','M') NULL DEFAULT NULL COMMENT 'L= Less than high school graduation,H=High school graduate, GED, S=post high school, no degree/certificate,C=Certificate (less than 2 years),A=Associate degree,B=Bachelor’s degree,M=____Master’s  ' COLLATE 'utf8mb4_unicode_ci',

      switch($grade){

          case'L':
              $prior_education="11";
              break;
          case'H':
              $prior_education="13";
              break;
          case'G':
              $prior_education="12";
              break;
          case'S':
              $prior_education="14";
              break;
          case'C':
              $prior_education="15";
              break;
          case'A':
              $prior_education="16";
              break;
          case'B':
              $prior_education="17";
              break;
          case'M':
              $prior_education="18";
              break;
          default:
              $prior_education="99";//UKNOW
              break;


      }




      $Query="SELECT fg_aboriginal FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
      $rok=RecuperaValor($Query);
      $fg_aboriginal=!empty($rok['fg_aboriginal'])?"Y":"N";

      #Recupermaos el pais .
      $QueryP="SELECT cl_iso2 FROM c_pais WHERE fl_pais=$ds_add_country ";
      $rowe=RecuperaValor($QueryP);
      $citizen_code=$rowe['cl_iso2'];

      #Verifica y obtiene el code del estado.(canada)
      $Qury="SELECT ds_abreviada FROM k_provincias WHERE fl_provincia=$ds_add_state ";
      $rol=RecuperaValor($Qury);
      $ds_code_state=$rol[0];

      $ds_location="Online";

      $hispanic=null;
      $fg_veteran=null;

      $earned_award_type=null;
     // $gpa=null;
      $pass_fail=($fg_aprobado=='1')?'P':'F'; //Passes  Fail

      if($fg_activo=='1'){

          $program_enrollment_status="3";//aun esta activo

          $pass_fail=null; #!empty($fg_aprobado)?'Passed':'Failed';


      }else{

          if($status_label=='Graduated'){
              $program_enrollment_status="1";

              $Query="SELECT ds_credential from k_programa_costos WHERE fl_programa=$fl_programa ";
              $rowc=RecuperaValor($Query);
              $earned_award_type=$rowc['ds_credential'];


          }

          if($status_label=='Student withdrawal'){
              $program_enrollment_status="2";
          }

      }

	  $Querycountry="SELECT ds_add_country FROM k_ses_app_frm_1 where cl_sesion='$cl_sesion' ";
	  $rowcountry=RecuperaValor($Querycountry);
	  $fl_pais=$rowcountry['ds_add_country'];
	  if($fl_pais<>226)
	  {
		 $ds_add_state=null;
	  }



      $spreadsheet->setActiveSheetIndex(0)

                            ->setCellValue('A'.$i.'',''.$ds_location.'') //location
                            ->setCellValue('B'.$i.'',''.$nb_programa.'') //program title
                            ->setCellValue('C'.$i.'',''.$program_award_type.'') //program award type
                            ->setCellValue('D'.$i.'',''.$ds_apaterno.'') //Last name
                            ->setCellValue('E'.$i.'',$ds_nombre) //First name
                            ->setCellValue('F'.$i.'',''.substr($ds_amaterno,0,1).'') //Middle name
                            ->setCellValue('G'.$i.'',''.$ds_add_street.'') //adress 1
                            ->setCellValue('H'.$i.'',''.$ds_add_city.'') //City
                            ->setCellValue('I'.$i.'',''.substr($ds_add_state,0,2).'') //Province/state code in usa only First Letter
                            ->setCellValue('J'.$i.'',''.$ds_add_zip.'') //Postal code
                            ->setCellValue('K'.$i.'',''.$ds_number.'') //phone
                            ->setCellValue('L'.$i.'', ''.$ds_sin.'') //ssn
                            ->setCellValue('M'.$i.'',''.$fe_birth.'') //Birth date
                            ->setCellValue('N'.$i.'',''.$hispanic.'') //Hispanic
                            ->setCellValue('O'.$i.'',''.$race.'') //race
                            ->setCellValue('P'.$i.'',''.$fe_gender.'') //gender
                            ->setCellValue('Q'.$i.'',''.$fg_disability.'') //disability
                            ->setCellValue('R'.$i.'',''.$fg_veteran.'') //veteran
                            ->setCellValue('S'.$i.'',''.$prior_education.'') //veteran
                            ->setCellValue('T'.$i.'',''.$fe_start_date.'') //start date
                            ->setCellValue('U'.$i.'',''.$fe_completado.'') //EXIT date
                            ->setCellValue('v'.$i.'',''.$program_enrollment_status.'') //program_enroollment_status
                            ->setCellValue('w'.$i.'',''.$earned_award_type.'') //earned award type
                            ->setCellValue('x'.$i.'',''.$gpa.'') //earned award type
                            ->setCellValue('Y'.$i.'',''.$pass_fail.''); //earned award type

                         /*   ->setCellValue('C'.$i.'',''.$ds_nombre.'') //Usual Student first name




                            ->setCellValue('I'.$i.'',''.$code_immigration_status.'') //$code_immigration_status
                            ->setCellValue('J'.$i.'',''.$citizen_code.'') //cityzenship code

                            ->setCellValue('L'.$i.'',''.$fg_aboriginal.'') //aboriginal
                            ->setCellValue('M'.$i.'',''.$fg_aboriginal.'') //FIRST NATION
                            ->setCellValue('N'.$i.'',''.$fg_aboriginal.'') //mETIS
                            ->setCellValue('O'.$i.'',''.$fg_aboriginal.'') //iNUIT

                            ->setCellValue('Q'.$i.'','') //adress 2


                            ->setCellValue('T'.$i.'',''.$citizen_code.'') //Country code

                            ->setCellValue('V'.$i.'','') //Current Phone numer
                            ->setCellValue('X'.$i.'',''.$ds_email.''); //email
                            */
  }

  // Rename sheet
  $spreadsheet->getActiveSheet(0)->setTitle('Student');
  /*
  // Create a new worksheet, after the default sheet
  $spreadsheet->createSheet();

  $spreadsheet->setActiveSheetIndex(1)
      ->setCellValue('A1','Program Location & Title')
      ->setCellValue('B1', 'Student ID Number') //Student id
      ->setCellValue('C1','Student Name') //name complete
      ->setCellValue('D1','Full Time Flag (Y/N)') //Full time
      ->setCellValue('E1','Student Start Date (YYYY-MM-DD)') //start date
      ->setCellValue('F1','Student End Date (YYYY-MM-DD)') //end date
      ->setCellValue('G1','Program Achievement Status') //program archivenment status
      ->setCellValue('H1','Graduate Follow Up Date (YYYY-MM-DD)') //graduated follow date
      ->setCellValue('I1','Follow Up Type'); //follow_type


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

      #Verifica y obtiene el code del estado.(canada)
      $Qury="SELECT ds_abreviada FROM k_provincias WHERE fl_provincia=$ds_add_state ";
      $rol=RecuperaValor($Qury);
      $ds_code_state=$rol[0];






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
       ->setCellValue('E'.$m.'',''.$fe_start_date.'') //start date
       ->setCellValue('F'.$m.'',''.$fe_completado.'') //end date
       ->setCellValue('G'.$m.'',''.$status.'') //program archivenment status
       ->setCellValue('H'.$m.'',''.$fe_graduacion.'') //graduated follow date
       ->setCellValue('I'.$m.'',''.$ds_follow_type.''); //follow_type


  }

  // Rename sheet
  $spreadsheet->getActiveSheet(1)->setTitle('Enrollment');
  */



  //salida del excel.
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename="Student_data_report.xls"');
  $writer = IOFactory::createWriter($spreadsheet, 'Xls');
  $writer->save('php://output');


?>