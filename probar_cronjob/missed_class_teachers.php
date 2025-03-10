<?php

  # Este cron se ejecutara cada hora
  # Su Objetivo es revisar las clases que hubo la hora  anterior y los maestros que tuvieron que haber asistido
  # Si el maesrtro no asistio se enviara un email notification Missed Class

  require '/var/www/html/vanas/lib/com_func.inc.php';
  require '/var/www/html/vanas/lib/sp_config.inc.php';

  # Originate email address
  $from = 'noreply@vanas.ca';

  # New Object DateTime
  $fecha = new DateTime();

  # Minus 1 hour to the actual DateTime
  $fecha->modify('-120 minute');

  # Variable to use in the Query to search the especified date and time
  $fecha_busqueda = $fecha->format('Y-m-d');
  $hora_busqueda = $fecha->format('H');

  # Find all the Live Sessions Attendance for all the teachers
  $Query  = "SELECT * FROM ";
  $Query .= "(SELECT origin.fl_maestro, ds_nombres AS fname_teacher, ds_apaterno AS lname_teacher, ds_email AS teacher_emai, 'Lecture' AS tipo, (SELECT ds_titulo FROM c_leccion WHERE sem.fl_leccion=fl_leccion) AS clase, (SELECT nb_programa FROM c_programa WHERE term.fl_programa=fl_programa) AS programa, (SELECT no_semana FROM c_leccion WHERE sem.fl_leccion=fl_leccion) semana, origin.nb_grupo AS grupo, DATE_FORMAT(class.fe_clase, '%Y-%m-%d') AS fe_clase, DATE_FORMAT(class.fe_clase, '%H:%i') AS hr_clase, (SELECT fe_asistencia FROM k_live_session_asistencia WHERE live.fl_live_session=fl_live_session AND fl_maestro=fl_usuario) AS fe_asistencia, IFNULL((SELECT cl_estatus_asistencia FROM k_live_session_asistencia WHERE live.fl_live_session=fl_live_session AND fl_maestro=fl_usuario), 1) AS cl_estatus_asistencia FROM c_grupo AS origin JOIN c_usuario usr ON(usr.fl_usuario=origin.fl_maestro) JOIN k_term term ON(term.fl_term=origin.fl_term) JOIN k_clase class ON(class.fl_grupo=origin.fl_grupo) JOIN k_live_session live ON(live.fl_clase=class.fl_clase) JOIN k_semana AS sem ON( sem.fl_semana = class.fl_semana) ";
  $Query .= "UNION (SELECT origin.fl_maestro, fname_teacher, lname_teacher, ds_email AS teacher_emai, 'Group Review' AS tipo, ds_titulo AS clase, nb_programa AS programa, no_semana AS semana, grp.nb_grupo AS grupo, origin.fe_clase AS fe_clase, origin.hr_clase AS hr_clase, (SELECT fe_asistencia FROM k_live_session_asistencia AS attendance WHERE attendance.fl_live_session = live.fl_live_session AND origin.fl_maestro=fl_usuario) AS fe_asistencia, IFNULL((SELECT cl_estatus_asistencia FROM k_live_session_asistencia AS attendance WHERE attendance.fl_live_session = live.fl_live_session  AND origin.fl_maestro=fl_usuario), 1) AS cl_estatus_asistencia FROM groups_schedules origin JOIN c_usuario usr ON(usr.fl_usuario=origin.fl_maestro) JOIN c_grupo grp ON(grp.fl_grupo=origin.fl_grupo) JOIN k_live_session live ON(live.fl_clase=origin.fl_clase)) ";
  $Query .= "UNION (SELECT origin.fl_maestro, fname_teacher, lname_teacher, ds_email AS teacher_emai, 'Global Class' AS tipo, origin.ds_titulo AS clase, 'NA' AS programa, no_semana AS semana, 'NA' AS grupo, fe_formato_clase AS fe_clase, hr_formato_clase AS hr_clase, (SELECT fe_asistencia_cg FROM k_live_session_asistencia_cg AS attendance WHERE attendance.fl_live_session_cg=live.fl_live_session_cg AND origin.fl_maestro=fl_usuario) AS fe_asistencia, IFNULL((SELECT cl_estatus_asistencia_cg FROM k_live_session_asistencia_cg AS attendance WHERE attendance.fl_live_session_cg=live.fl_live_session_cg AND origin.fl_maestro=fl_usuario), 1) AS cl_estatus_asistencia FROM clases_globales origin JOIN c_usuario usr ON(usr.fl_usuario=origin.fl_maestro) JOIN k_live_sesion_cg live ON(live.fl_clase_cg=origin.fl_clase_cg))) AS resultado ";
  $Query .= "WHERE fe_clase LIKE '".$fecha_busqueda."%' AND hr_clase LIKE '".$hora_busqueda."%' AND cl_estatus_asistencia='1' ORDER  BY fe_clase DESC";

  $rs = EjecutaQuery($Query);
  $variables = array();

  while($row=RecuperaRegistro($rs)){
    $fl_usuario = $row[0];
    $ds_nombres = $row[1];
    $ds_apaterno = $row[2];
    $ds_email = $row[3];
    $type = $row[4];
    $ds_title = $row[5];
    $program_name = $row[6];
    $no_week = $row[7];
    $nb_group = $row[8];
    $fe_day = DATE("l d, Y");
    $fe_date = $row[9];
    $fe_time = $row[10];
    $fe_attendance = $row[11];
    $cl_estatus_asistencia = $row[12];  
    unset($variables);

    if($cl_estatus_asistencia!=2){
      $variables = array(
        "#te_fname#" => $ds_nombres,
        "#te_lname#" => $ds_apaterno,
	"#pg_name#" => $program_name,
        "#no_week#" => $no_week,
        "#ds_title#" => $ds_title,
        "#fe_day#" => $fe_day,
        "#fe_date#" => $fe_date,
        "#fe_time#" => $fe_time,
        "#nb_group#" => $nb_group
	);

       var_dump($variables);

      ## For testing prupose the email will be the following
      $ds_email = 'umeixueiro@gmail.com';

      # TEACHERS missed class template (180)
      $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template='180' AND fg_activo='1'";
      $missed_template = RecuperaValor($Query);
      $st_missed_template = str_uso_normal($missed_template[0].$missed_template[1].$missed_template[2]);
      
      # Variable substitution on template
      $st_missed_template = str_replace(array_keys($variables), $variables, $st_missed_template);

      # EMAIL NOTIFICATION MISSED CLASS
      EnviaMailHTML('VANAS', $from, $ds_email, "Teacher Missed Class Notice", $st_missed_template);
      exit();
      # Save a log of the email sended
      $QueryMC  = "INSERT INTO k_maestro_template(fl_maestro,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
      $QueryMC .= "VALUES ($fl_usuario, 180, CURRENT_TIMESTAMP, '".$st_missed_template."') ";
      # EjecutaQuery($QueryMC);
      
    }
  }

?>
