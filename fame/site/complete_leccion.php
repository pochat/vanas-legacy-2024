<?php 
	# Libreria de funciones
    require("../lib/self_general.php");

    # Obtenemos el usuario y el instituto
    // $fl_usuario = ValidaSesion(False,0, True);
    // $fl_instituto = ObtenInstituto($fl_usuario);

    $fl_leccion_sp = RecibeParametroNumerico('fl_leccion_sp');
    $fl_usuario = RecibeParametroNumerico('fl_usuario');
    $activado = $_POST['completed'];

    $fl_perfil_sp=ObtenPerfilUsuario($fl_usuario);

    #Recupermo la semana y ds_leccion
    $Queryl="SELECT C.no_semana,P.nb_programa 
            FROM c_leccion_sp C 
            JOIN c_programa_sp P on P.fl_programa_sp=C.fl_programa_sp
            WHERE C.fl_leccion_sp=$fl_leccion_sp ";
    $rowl=RecuperaValor($Queryl);
    $no_semana=str_texto($rowl[0]);
    $ds_leccion=str_texto($rowl[1]);

    # Buscamos si el usuario ya esta registrado con esta leccion y esta marcado completo ya no modificara nada
    $row = RecuperaValor("SELECT COUNT(1) FROM k_leccion_usu WHERE fl_leccion_sp=$fl_leccion_sp AND fl_usuario_sp=$fl_usuario");
    $existe = $row[0];

    if(empty($existe)){
        $Query  = "INSERT INTO k_leccion_usu (fl_leccion_sp,fl_usuario_sp,fg_complete) ";
        $Query .= "VALUES ($fl_leccion_sp, $fl_usuario, '1')";
        $fl_lec_usu = EjecutaInsert($Query);
    } else {
        $Query = "UPDATE k_leccion_usu SET fg_complete='1' WHERE fl_leccion_sp = $fl_leccion_sp AND fl_usuario_sp = $fl_usuario";
        EjecutaQuery($Query);
    }

    # Actualizamos el progreso del usuario al programa que esta cursando

    # Obtenemos el programa de la leccion
    $row = RecuperaValor("SELECT fl_programa_sp FROM c_leccion_sp WHERE fl_leccion_sp=$fl_leccion_sp");
    $fl_programa_sp = $row[0];
    $tot_lecciones = 0;
    $tot_completos = 0;

    $Query="SELECT COUNT(1) FROM k_calificacion_teacher WHERE fl_leccion_sp=$fl_leccion_sp AND fl_alumno=$fl_usuario";
    $row = RecuperaValor($Query);
    $cal_exist = $row[0];

    if ($activado=='true'){
        if ($cal_exist==0){
            $activado='false';
            $next_quiz = GetNextWeekQuiz($fl_usuario, $fl_programa_sp);
            $Query="DELETE FROM k_leccion_usu  WHERE (fl_leccion_sp=$fl_leccion_sp AND fl_usuario_sp=$fl_usuario) ";
            EjecutaQuery($Query);
        }
    } else {
        $activado='true';

        # Recorremos todas las lecciones que tiene este programa
        $rs = EjecutaQuery("SELECT fl_leccion_sp FROM c_leccion_sp WHERE fl_programa_sp=$fl_programa_sp");

        for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
            $tot_lecciones++;
            $fl_leccion_sp = $row[0];
            $row1 = RecuperaValor("SELECT fg_complete, fg_quiz_complete FROM k_leccion_usu WHERE fl_leccion_sp=$fl_leccion_sp AND fl_usuario_sp=$fl_usuario");
            $fg_complete = $row1[0];
            $fg_quiz_complete = $row1[0];
            if (!empty($fg_complete))
                $tot_completos++;
        }

        # Actualizamos el proceso del programa
        $ds_progreso = ((100 / $tot_lecciones) * $tot_completos);

        if(($fl_perfil_sp==PFL_ADMINISTRADOR)||($fl_perfil_sp==PFL_ADM_CSF)){

            #vERIFICA si existe la relacion de k_usuario_programa y si no lo inserta esto para mantener la integridad del los datos un usuario un curso.
            $Query="SELECT COUNT(*)FROM k_usuario_programa WHERE fl_usuario=$fl_usuario AND fl_programa_sp=$fl_programa_sp ";
            $row=RecuperaValor($Query);
            if(empty($row[0])){
                $fl_new_pro=EjecutaInsert("INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fl_maestro,fe_inicio_programa,fe_creacion)VALUES($fl_usuario,$fl_programa_sp,'0','1',$fl_usuario,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ");
            }

            
        }

        EjecutaQuery("UPDATE k_usuario_programa SET ds_progreso = '$ds_progreso' WHERE fl_usuario_sp = $fl_usuario  AND fl_programa_sp = $fl_programa_sp");

        if ($tot_lecciones == $tot_completos) {
            EjecutaQuery("UPDATE k_usuario_programa SET fg_terminado='1' WHERE fl_programa_sp=$fl_programa_sp AND fl_usuario_sp=$fl_usuario");
        }

        # Obtenemos donde se encuntra el siguiente quiz del programa
        $next_quiz = GetNextWeekQuiz($fl_usuario, $fl_programa_sp);
        if (empty($next_quiz)) {
            $next_quiz = ObtenSemanaMaximaAlumno($fl_programa_sp);
        }

        #Verificamos cuantas lecciones tiene.
        $no_semanas = ObtenSemanaMaximaAlumno($fl_programa_sp);

        #Cuando es la ultima semana correspondiente a esa leccion
        if ($no_semanas == $no_semana) {

            #Verifica si ya esta marcado como conpleto.
            $Query = "SELECT fg_terminado FROM k_usuario_programa WHERE fl_programa_sp=$fl_programa_sp AND fl_usuario_sp=$fl_usuario ";
            $ro = RecuperaValor($Query);

            if (empty($ro[0])) {
                #Se guarda la fecha de cuominacion de la leccion.
                $Updta = "UPDATE k_usuario_programa SET fe_final_programa=NOW() WHERE fl_programa_sp=$fl_programa_sp AND fl_usuario_sp=$fl_usuario ";
                EjecutaQuery($Updta);
            }
        }

        # Actualizamos los promedios de quizes y teachers
        SavePromedio_Q_T($fl_programa_sp, $fl_usuario);


        #Recupermso al teacher del alumno.
        $Querys = "SELECT fl_maestro FROM k_usuario_programa WHERE fl_programa_sp=$fl_programa_sp AND fl_usuario_sp=$fl_usuario ";
        $rows = RecuperaValor($Querys);
        $fl_maestro = str_texto($rows[0]);

        #Se verifica el permiso de enviar email al teacher.
        $fg_session_completed = VerificaPermisoEnvioEmail($fl_maestro, 'fg_session_completed');

        if (!empty($fg_session_completed)) {
            #Enviamos email de notificacion al teacher.
            $ds_encabezado = genera_documento_sp($fl_usuario, 1, 140, $fl_programa_sp, '');
            $ds_cuerpo = genera_documento_sp($fl_usuario, 2, 140, $fl_programa_sp, '');
            $ds_pie = genera_documento_sp($fl_usuario, 3, 140, $fl_programa_sp, '');
            $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;


            $QueryT = "SELECT U.ds_nombres,U.ds_apaterno, U.ds_email 
                    FROM k_usuario_programa K
                    JOIN c_usuario U ON U.fl_usuario=K.fl_maestro
                    WHERE fl_maestro=$fl_maestro ";
            $rot = RecuperaValor($QueryT);
            $teacher_fname = str_texto($rot[0]);
            $teacher_lname = str_texto($rot[1]);
            $teacher_email = str_texto($rot[2]);

            $ds_contenido = str_replace("#fame_te_fname#", $teacher_fname, $ds_contenido);
            $ds_contenido = str_replace("#fame_te_lname#", $teacher_lname, $ds_contenido);

            $ds_contenido = str_replace("#no_week#", $no_semana, $ds_contenido);
            $ds_contenido = str_replace("#ds_leccion#", $ds_leccion, $ds_contenido);

            $row = RecuperaValor("SELECT nb_template FROM k_template_doc WHERE fl_template=140 ");
            $nb_template = str_texto($row[0]);

            # Inicializa variables de ambiente para envio de correo
            ini_set("SMTP", MAIL_SERVER);
            ini_set("smtp_port", MAIL_PORT);
            ini_set("sendmail_from", MAIL_FROM);
            $message = $ds_contenido;
            $message = utf8_decode(str_ascii(str_uso_normal($message)));
            $bcc = ObtenConfiguracion(107);
            $nb_quien_envia_email = ObtenEtiqueta(949);#Vamcouver School nombre de quien envia el mensaje
            $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4);
            $ds_titulo = $nb_template;#etiqueta de asunto del mensjae para el envio
            $mail = EnviaMailHTML($nb_quien_envia_email, $ds_email_de_quien_envia_mensaje, $teacher_email, $ds_titulo, $message, $bcc);
        }
    }

    $result['activado'] = $activado;
    $result["progreso"] = ObtenProgresoCourse($fl_programa_sp, $fl_usuario);
    $result["programa"] = array(
        "name_program" => ObtenNombreCourse($fl_programa_sp),
        "fl_programa" => $fl_programa_sp,
        "quiznext" => $next_quiz
    );

    echo json_encode((Object) $result);

?>