<?php

  /*
    =======    ====     ====     ====   =======
    /==////    /==/==   /==/==  /==/==  /==////
    /==       /==  /==  /== /==/== /==  /==
    /=======  /=======  /== /===== /==  /======
    /==////   /==///==  /==  /===  /==  /==///=
    /==       /==  /==  /==  /===  /==  /==
    /==       /==  /==  /==   //   /==  /=======
    ///       ///  ///  ///        ///  ////////

     **** GLOBAL CLASS ATTENDANCE SCRIPT ****
     **** UMEIXUEIRO 28/Septiembre/2020  ****

     This script run as a cronjob every day “At 00:30.”
     following this cron schema: "30 0 * * *"

     The script review the students attendance on Global Clases.
     If the Student has >= to 3 abssents OR <= 8, an email is sent
     with the following notification:
     "Unsatisfactory Attendance Warning"

     If the Student has >= 8 abssents an email is sent
     with the following notification:
     "Attendance Probation"
  */

  /** DEBUGING SETING */
  /** Let the vlue to null, set this only for debuging purpose */
  $test_email = null;

  /** Include General libraries */
  require '../lib/com_func.inc.php';
  require '../lib/sp_config.inc.php';

  # Include AWS SES libraries
  require '../AWS_SES/PHP/com_email_func.inc.php';

  $dom = new DOMDocument();
  libxml_use_internal_errors(true); // Para suprimir errores relacionados con HTML mal formado

/** The from email setup */
  $from = 'noreply@vanas.ca';

  /** $yesterday Variable Initialization */
  $yesterday = 1;

	/** Student missed class template */
  $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='32' AND fg_activo='1'";

  $missed_template = RecuperaValor($Query);
  $st_missed_template = html_entity_decode($missed_template[0].$missed_template[1].$missed_template[2]);

  /** Student Attendance Warning template */
  $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='33' AND fg_activo='1'";

  $attendance_template = RecuperaValor($Query);
  $st_attendance_template = html_entity_decode($attendance_template[0].$attendance_template[1].$attendance_template[2]);

  /** Student Attendance Warning template */
  $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='34' AND fg_activo='1'";

  $atten_proba_temp = RecuperaValor($Query);

  /** This is the Attendance template */
  $st_atten_prob_template = html_entity_decode($atten_proba_temp[0].$atten_proba_temp[1].$atten_proba_temp[2]);



  /** This query is a new one to aplie on the missed class cronjob for live sessions, this must NOT BE USED HERE */
  // /** This query includes the cl_estatus_asistencia_cg and substitute the past one */
  // $Query = "SELECT
  //             a.fl_grupo,
  //             d.no_semana,
  //             d.ds_titulo,
  //             DATE_FORMAT(a.fe_clase, '%W') fe_day,
  //             DATE_FORMAT(a.fe_clase, '%M %e, %Y') fe_date,
  //             DATE_FORMAT(a.fe_clase, '%h:%i %p') fe_time,
  //             fl_clase,
  //             nb_grupo,
  //             c.fl_semana,
  //             f.ds_nombres,
  //             f.ds_apaterno,
  //             f.ds_email,
  //             f.fl_usuario,
  //             a.fg_obligatorio,
  //             a.fg_adicional,
  //             (SELECT fl_sesion FROM c_sesion k WHERE k.cl_sesion = f.cl_sesion) fl_sesion,
  //             (SELECT cl_estatus_asistencia FROM k_live_session_asistencia G JOIN k_live_sesion_cg H ON(G.fl_live_session = H.fl_live_session) WHERE A.fl_clase = H.fl_clase AND G.fl_usuario = f.fl_usuario) cl_estatus_asistencia_cg
  //           FROM k_clase a
  //             LEFT JOIN c_grupo b ON(a.fl_grupo = b.fl_grupo)
  //             LEFT JOIN k_semana c ON(a.fl_semana = c.fl_semana)
  //             LEFT JOIN c_leccion d ON(c.fl_leccion = d.fl_leccion)
  //             LEFT JOIN k_alumno_grupo e ON(b.fl_grupo = e.fl_grupo)
  //             LEFT JOIN c_usuario f ON(e.fl_alumno=f.fl_usuario)
  //           WHERE DATE_SUB(CURDATE(), INTERVAL $yesterdar DAY) = DATE_FORMAT(a.fe_clase, '%Y-%c-%d')
  //           AND ((a.fg_obligatorio='1' AND a.fg_adicional='0') OR (a.fg_obligatorio='1' AND a.fg_adicional='1')) AND f.fg_activo='1'
  //           ORDER BY f.ds_nombres;";

  /** Find all groups with upcoming mandatory Global Class Session in $day_advance day(s) */
  $Query = "SELECT
            A.fl_clase_global,
            no_orden no_semana,
            (SELECT ds_clase FROM c_clase_global B WHERE A.fl_clase_global = B.fl_clase_global ) ds_titulo,
            DATE_FORMAT(a.fe_clase, '%W') fe_day,
            DATE_FORMAT(a.fe_clase, '%M %e, %Y') fe_date,
            DATE_FORMAT(a.fe_clase, '%h:%i %p') fe_time,
            fl_clase_cg,
            A.ds_titulo nb_grupo,
            'No Aplica' fl_semana,
            f.ds_nombres,
            f.ds_apaterno,
            f.ds_email,
            f.fl_usuario,
            A.fg_obligatorio,
            'No Aplica' fg_adicional,
            (SELECT fl_sesion FROM c_sesion k WHERE k.cl_sesion = f.cl_sesion) fl_sesion,
            (SELECT cl_estatus_asistencia_cg FROM k_live_session_asistencia_cg G JOIN k_live_sesion_cg H ON(G.fl_live_session_cg=H.fl_live_session_cg) WHERE A.fl_clase_cg = H.fl_clase_cg AND G.fl_usuario = f.fl_usuario) cl_estatus_asistencia_cg
          FROM k_clase_cg A
            LEFT JOIN k_alumno_cg E ON(A.fl_clase_global = E.fl_clase_global)
            LEFT JOIN c_usuario F ON(E.fl_usuario = F.fl_usuario)
          WHERE DATE_SUB(CURDATE(), INTERVAL $yesterday DAY) = DATE_FORMAT(a.fe_clase, '%Y-%c-%d')  AND A.fg_obligatorio = 1 AND F.fg_activo='1' ORDER BY f.ds_nombres;";

  $rs = EjecutaQuery($Query);
  !empty($test_email)?print_r($rs."\n"):null;

  while($row=RecuperaRegistro($rs)){
    $fl_grupo = $row[0]; /** ID global class */
    $no_week = $row[1]; /** Week number */
    $ds_title = $row[2]; /** Class name */
    $fe_day = $row[3]; /** Day of week */
    $fe_date = $row[4]; /** Datae Month day, Year */
    $fe_time = $row[5]; /** 12hr Time */
    $nb_grupo = $row[7]; /** name of group */
    $fl_semana = $row[8]; /** This not aplies in Global class */
    $ds_nombres = $row[9]; /** Student First Name */
    $ds_apaterno = $row[10]; /** Student Last Name */
    $ds_email = !empty($test_email)?$test_email:$row[11]; /** Student email */
    $fl_usuario = $row[12]; /** Studen user ID */
    $fg_obligatorio = $row[13]; /** Class is mandatory codes([0, no], [1, yes]) */
    $fg_adicional = $row[14]; /** This not aplies in global class */
    $fl_sesion = $row[15]; /** Student user sesion */
    $cl_estatus_asistencia = $row[16]; /** Attendance status codes([1, absent], [2, present], [3, late]) */

    /** cl_estatus_asistencia codes([1, absent], [2, present], [3, late]) */
    if(empty($cl_estatus_asistencia) OR $cl_estatus_asistencia!=2){
      $variables = array(
        "st_fname" => $ds_nombres,
        "st_lname" => $ds_apaterno,
        "no_week" => $no_week,
        "ds_title" => $ds_title,
        "fe_day" => $fe_day,
        "fe_date" => $fe_date,
        "fe_time" => $fe_time,
        "nb_group" => $nb_grupo
      );

      /** Get the student principal email */
      $Query="SELECT
                ds_email_r
              FROM k_presponsable a
                JOIN c_usuario b ON(b.cl_sesion=a.cl_sesion)
              WHERE b.fl_usuario=$fl_usuario ";
      $row=RecuperaValor($Query);
      !empty($test_email)?print_r($row."\n"):null;

      /** Student principal email */
      $ds_email_responsable = !empty($test_email)?$test_email:$row['ds_email_r'];

      /** Get the student alternate email */
      $Query="SELECT
                ds_a_email
              FROM k_app_contrato a
                JOIN c_usuario b ON(b.cl_sesion=a.cl_sesion)
              WHERE b.fl_usuario=$fl_usuario ";
      $row=RecuperaValor($Query);
      !empty($test_email)?print_r($row."\n"):null;

      /** Student alternate email */
      $ds_email_alternative = !empty($test_email)?$test_email:$row['ds_a_email'];

      /** Student copy to email */
      $Query="SELECT
                fg_copy_email_responsable,
                fg_copy_email_alternativo
              FROM c_alumno
              WHERE fl_alumno=$fl_usuario ";

      $row=RecuperaValor($Query);
      !empty($test_email)?print_r($row."\n"):null;

      /** Student copy to principal email */
      $fg_copy_email_responsable=$row[0]??null;

      /** Student copy to alternate email */
      $fg_copy_email_alternativo=$row[1]??null;

      /** Generate the email template with the variables */
      $ds_email_template = GenerateTemplate($st_missed_template, $variables);


      $dom->loadHTML($ds_email_template);
      $link = $dom->getElementById('login-redirect');
      if ($link) {
        // Cambiar el atributo href
        $link->setAttribute('href', 'https://campus.vanas.ca/modules/teachers_new/index.php#ajax/home.php');
      }
      $ds_email_template = $dom->saveHTML();


      /** A copy email is sent if the student sets */
        if (!empty($fg_copy_email_alternativo)) {
            /** MISSED CLASS EMAIL NOTIFICATION */
            //SendNoticeMail($client, $from, $ds_email_alternative, "", "Missed Class", $ds_template_html);
            EnviaMailHTML('', $from, $ds_email_alternative, "Missed Class", $ds_email_template);
        }
        if (!empty($fg_copy_email_responsable)) {
            /** MISSED CLASS EMAIL NOTIFICATION */
            //SendNoticeMail($client, $from, $ds_email, "" . $ds_email_responsable . "", "Missed Class", $ds_template_html);
            EnviaMailHTML('', $from, $ds_email, "Missed Class", $ds_email_template, $ds_email_responsable);
        }
      /** Store log in DB for the sent email */
      $QueryMC  = "INSERT
                    INTO k_alumno_template (fl_alumno, fl_template, fe_envio, ds_header, ds_body, ds_footer)
                    VALUES ($fl_sesion, 32, CURRENT_TIMESTAMP, '".GenerateTemplate(str_html_bd($missed_template[0]), $variables)."', '".GenerateTemplate(str_html_bd($missed_template[1]), $variables)."','".GenerateTemplate(str_html_bd($missed_template[2]), $variables)."') ";

      EjecutaQuery($QueryMC);

      /** Retrieve the Global session data */
      $Query5 = "SELECT
                  no_orden no_semana,
                  ds_titulo ds_tituloo,
                  DATE_FORMAT(fe_clase, '%W') fe_dayy,
                  DATE_FORMAT(fe_clase, '%M %e, %Y') fe_datee,
                  DATE_FORMAT(fe_clase, '%h:%i %p') fe_timee,
                  fl_clase_global fl_clasee
                FROM k_clase_cg
                WHERE fl_clase_global=$fl_grupo AND fg_obligatorio='1'
                AND DATE_FORMAT(a.fe_clase, '%Y-%c-%d') <=  dATE_SUB(CURDATE(), INTERVAL $yesterday DAY)
                ORDER BY fe_clase;";

      $rs5 = EjecutaQuery($Query5);
      !empty($test_email)?print_r($rs5."\n"):null;

      /** attendance abssence count variable initialization */
      $absences = 0;
      $absences_class = "";

      while($row5=RecuperaRegistro($rs5)){
        $no_semanaa = $row5[0];
        $ds_tituloo = $row5[1];
        $fe_dayy = $row5[2];
        $fe_datee = $row5[3];
        $fe_timee = $row5[4];
        $fl_clasee = $row5[5];

        /** Retrieve the Global abssences that the student has */
        $Query6 = "SELECT
                    cl_estatus_asistencia_cg cl_estatus_asistencia
                  FROM k_live_sesion_cg a
                    LEFT JOIN k_live_session_asistencia_cg b ON a.fl_live_session_cg = b.fl_live_session_cg
                  WHERE a.fl_clase_cg=49 AND fl_usuario=$fl_usuario";

        $row6 = RecuperaValor($Query6);
        !empty($test_email)?print_r($row6."\n"):null;

        $cl_estatus_asistenciaa = $row6[0];

        if(empty($cl_estatus_asistenciaa) OR $cl_estatus_asistenciaa!=2){
          $absences ++;
          $absences_class = $absences_class."Week $no_semanaa: $ds_tituloo Date on: $fe_dayy, $fe_datee, $fe_timee <br>";
        }
      }

      /** message: "Unsatisfactory Attendance Warning" */
      $unsatisfactory = ObtenConfiguracion(87);

      /**  message: "Attendance Probation" */
      $attendance_probation = ObtenConfiguracion(88);

      /**
       * If student has >= $unsatisfactory AND < $attendance_probation
       *
       * Send an email with the message: "unsatisfactory Attendance Warning"
       *
       **/
      if($absences>=$unsatisfactory AND $absences<$attendance_probation){
        $variables3 = array(
          "st_fname" => $ds_nombres,
          "st_lname" => $ds_apaterno,
          "missed_class_term_history" => $absences_class,
          "number_of_absences" => $unsatisfactory
        );

        /** Generate the email template with the variables */
        $ds_attendance_template = GenerateTemplate($st_attendance_template, $variables3);


        $dom->loadHTML($ds_email_template);
        $link = $dom->getElementById('login-redirect');
        if ($link) {
            // Cambiar el atributo href
            $link->setAttribute('href', 'https://campus.vanas.ca/modules/teachers_new/index.php#ajax/home.php');
        }
        $ds_email_template = $dom->saveHTML();



        /** Send a copy email if the student sets */
            if ($fg_copy_email_alternativo) {
               // SendNoticeMail($client, $from, $ds_email_alternative, "", "Unsatisfactory Attendance Warning", $ds_attendance_html);
                EnviaMailHTML('', $from, $ds_email_alternative, "Unsatisfactory Attendance Warning", $ds_email_template);
            }
            if ($fg_copy_email_responsable) {
                //SendNoticeMail($client, $from, $ds_email, "", "Unsatisfactory Attendance Warning", $ds_attendance_html);
                EnviaMailHTML('', $from, $ds_email, "Unsatisfactory Attendance Warning", $ds_email_template);
            }
        /** Log the email sent on the DB */
        $Queryat = "INSERT INTO k_alumno_template (fl_alumno, fl_template, fe_envio, ds_header, ds_body, ds_footer)
                    VALUES ($fl_sesion, 33, CURRENT_TIMESTAMP, '".GenerateTemplate(str_html_bd($attendance_template[0]), $variables3)."', '".GenerateTemplate(str_html_bd($attendance_template[1]), $variables3)."','".GenerateTemplate(str_html_bd($attendance_template[2]), $variables3)."') ";

        EjecutaQuery($Queryat);
      }

      /**
       * If student has >= attendance_probation
       *
       * Send an email with the message: "Attendance Probation"
       *
       **/
      if($absences>=$attendance_probation){
        $variables4 = array(
          "st_fname" => $ds_nombres,
          "st_lname" => $ds_apaterno,
          "missed_class_term_history" => $absences_class,
          "number_of_absences" => $attendance_probation
        );

        /** Generate the email template with the variables */
        $ds_att_prb_probation = GenerateTemplate($st_atten_prob_template, $variables4);


        $dom->loadHTML($ds_email_template);
        $link = $dom->getElementById('login-redirect');
        if ($link) {
            // Cambiar el atributo href
            $link->setAttribute('href', 'https://campus.vanas.ca/modules/teachers_new/index.php#ajax/home.php');
        }
        $ds_email_template = $dom->saveHTML();

            /** Send a copy email if the student sets */
            if ($fg_copy_email_alternativo) {
              //  SendNoticeMail($client, $from, $ds_email_alternative, "", "Attendance Probation", $ds_probation_html);
                EnviaMailHTML('', $from, $ds_email_alternative, "Attendance Probation", $ds_email_template);
            }
            if ($fg_copy_email_responsable) {
              //  SendNoticeMail($client, $from, $ds_email, "" . $ds_email_responsable . "", "Attendance Probation", $ds_probation_html);
                EnviaMailHTML('', $from, $ds_email, "Attendance Probation", $ds_email_template);
            }
        /** Log the email sent on the DB */
        $Queryul = "INSERT INTO k_alumno_template (fl_alumno, fl_template, fe_envio, ds_header, ds_body, ds_footer)
                    VALUES ($fl_sesion, 34, CURRENT_TIMESTAMP, '".GenerateTemplate(str_html_bd($atten_proba_temp[0]), $variables4)."', '".GenerateTemplate(str_html_bd($atten_proba_temp[1]), $variables4)."','".GenerateTemplate(str_html_bd($atten_proba_temp[2]), $variables4)."') ";

        EjecutaQuery($Queryul);
      }
    }
  }

?>
