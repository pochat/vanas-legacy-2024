<?php

# Definicion de librerias para Sitios de Alumnos y Maestros
if (PHP_OS=='Linux') { # when is production
  require('/var/www/html/vanas/lib/com_func.inc.php');
  require('/var/www/html/vanas/lib/sp_config.inc.php');
  require('/var/www/html/vanas/modules/common/new_campus/lib/cam_layout.inc.php');
  require('/var/www/html/vanas/modules/common/lib/cam_forum.inc.php');
} else { # when is development
  require($_SERVER['DOCUMENT_ROOT'].'/lib/com_func.inc.php');
  require($_SERVER['DOCUMENT_ROOT'].'/lib/sp_config.inc.php');
  require($_SERVER['DOCUMENT_ROOT'] . '/modules/common/new_campus/lib/cam_layout.inc.php');
  require($_SERVER['DOCUMENT_ROOT'] . '/modules/common/lib/cam_forum.inc.php');
}

require('com_layout_selfv2.php');

$sufix=langSufix();

#
# Funciones para Administradores Alumnos y Maestros
#

function ValidaPermisoSelf($p_funcion){

  # Lee la sesion del cookie
  $cl_sesion = $_COOKIE[SESION_RM]??NULL;
  if (empty($cl_sesion))
    $cl_sesion = $_COOKIE[SESION_SELF]??NULL;

  # Verifica que existe la sesion
  if (empty($cl_sesion))
    return False;

  # Recupera el usuario y su perfil  
  $row = RecuperaValor("SELECT fl_usuario, fl_perfil_sp FROM c_usuario WHERE cl_sesion='$cl_sesion'");
  $fl_usuario = $row[0];
  $fl_perfil = $row[1];

  # Verifica que existe el usuario
  if (empty($fl_usuario))
    return False;

  # Verifica si es el Administrador
  if ($fl_usuario == ADMINISTRADOR)
    return True;

  # Revisa si es una funcion para Alumnos del SelfPace
  if ($p_funcion == FUNC_SELF and $fl_perfil == PFL_ESTUDIANTE_SELF)
    return True;

  # Revisa si es una funcion para Maestros del SelfPace
  if ($p_funcion == FUNC_SELF and $fl_perfil == PFL_MAESTRO_SELF)
    return True;

  # Revisa si es una funcion para administrador de la escuela SelfPace
  if ($p_funcion == FUNC_SELF and $fl_perfil == PFL_ADMINISTRADOR)
    return True;

  # Revisa si es una funcion para administrador de la escuela SelfPace
  if ($p_funcion == FUNC_SELF and $fl_perfil == PFL_ADM_CSF)
    return True;

  # Caso no esperado
  return False;
}

function ObtenPerfilUsuario($p_usuario)
{
  # Recupera perfil de un usuario
  $row = RecuperaValor("SELECT fl_perfil_sp FROM c_usuario WHERE fl_usuario='$p_usuario'");

  return !empty($row[0])?$row[0]:NULL;
}

function ObtenNombreUsuario($p_usuario, $p_user_logeado = 0)
{
  // echo "gabriel".p_usuario
  # Recupera matricula del usuario
  // $concat = array('ds_nombres', "' '", 'ds_apaterno');
  $row = RecuperaValor("SELECT ds_nombres, ds_apaterno, fl_instituto, fl_perfil_sp FROM c_usuario WHERE fl_usuario=$p_usuario");
  $ds_nombres = str_texto(!empty($row[0])?$row[0]:NULL);
  $ds_apaterno = str_texto(!empty($row[1])?$row[1]:NULL);
  $fl_instituto = !empty($row[2])?$row[2]:NULL;
  $fl_perfil_sp = !empty($row[3])?$row[3]:NULL;
  $fg_blocking = GetBlockingLName($fl_instituto);

  #remplazamos carateres especiales.
  $ds_nombres=str_replace("&Ntilde;", "N", $ds_nombres);
  $ds_nombres=str_replace("&ntilde;", "n", $ds_nombres);
  $ds_nombres=str_replace("&", " ", $ds_nombres);
  $ds_nombres=str_replace(";", " ", $ds_nombres);

  $ds_apaterno=str_replace("&Ntilde;", "N", $ds_apaterno);
  $ds_apaterno=str_replace("&ntilde;", "n", $ds_apaterno);
  $ds_apaterno=str_replace("&", " ", $ds_apaterno);
  $ds_apaterno=str_replace(";", " ", $ds_apaterno);


  # Blocking Last Name
  if ($fl_perfil_sp == PFL_ESTUDIANTE_SELF && $fg_blocking == 1 && !empty($p_user_logeado) && $p_usuario <> $p_user_logeado)
    $ds_nombres .= " " . CutText(html_entity_decode($ds_apaterno), 3);
  else
    $ds_nombres .= " " . html_entity_decode($ds_apaterno);

 

  return $ds_nombres;
}

function ObtenMatriculaAlumno($p_alumno)
{

  # Recupera matricula del usuario
  $row = RecuperaValor("SELECT ds_login FROM c_usuario WHERE fl_usuario=$p_alumno");
  return !empty($row[0])?$row[0]:NULL;
}

function ObtenFotoUsuario($p_usuario)
{

  # Recupera el perfil del usuario
  $fl_perfil = ObtenPerfilUsuario($p_usuario);
  $fl_instituto = ObtenInstituto($p_usuario);

  # ruta de la foto
  $ruta = PATH_SELF_UPLOADS . "/" . $fl_instituto . "/" . CARPETA_USER . $p_usuario . "/";
  # Verifica si el usuario tiene un avatar
  if ($fl_perfil == PFL_MAESTRO_SELF) {
    $row = RecuperaValor("SELECT ds_ruta_foto FROM c_maestro_sp WHERE fl_maestro_sp=$p_usuario");
    if (!empty($row[0]))
      $ds_ruta_avatar = $ruta . $row[0];
  } else {
    $row = RecuperaValor("SELECT ds_ruta_foto FROM c_alumno_sp WHERE fl_alumno_sp=$p_usuario");
    if (!empty($row[0]))
      $ds_ruta_avatar = $ruta . $row[0];
  }
  # Default
  if (empty($ds_ruta_avatar))
    $ds_ruta_avatar = PATH_N_COM_IMAGES . "/vanas-family-edutisse-header.jpg";

  return $ds_ruta_avatar;
}

function ObtenFotoOficial($p_usuario, $p_fisica = false)
{

  # Recupera el perfil del usuario
  $fl_perfil = ObtenPerfilUsuario($p_usuario);
  $fl_instituto = ObtenInstituto($p_usuario);
  # Verifica si el usuario tiene un avatar
  if ($fl_perfil == PFL_MAESTRO_SELF)
    $row = RecuperaValor("SELECT ds_oficial FROM c_maestro_sp WHERE fl_maestro_sp=$p_usuario");
  else
    $row = RecuperaValor("SELECT ds_oficial FROM c_alumno_sp WHERE fl_alumno_sp=$p_usuario");

  if ($p_fisica)
    $ruta = PATH_SELF_UPLOADS_F;
  else
    $ruta = PATH_SELF_UPLOADS;

  # Default
  if (!empty($row[0]))
    $ds_ruta_avatar = $ruta . "/" . $fl_instituto . "/" . CARPETA_USER . $p_usuario . "/" . $row[0];

  return $ds_ruta_avatar;
}

function ObtenNombreFotoOficial($p_usuario)
{
  # Recupera el perfil del usuario
  $fl_perfil = ObtenPerfilUsuario($p_usuario);

  # Verifica si el usuario tiene un avatar
  if ($fl_perfil == PFL_MAESTRO_SELF)
    $row = RecuperaValor("SELECT ds_oficial FROM c_maestro_sp WHERE fl_maestro_sp=$p_usuario");
  else
    $row = RecuperaValor("SELECT ds_oficial FROM c_alumno_sp WHERE fl_alumno_sp=$p_usuario");

  return $row[0];
}


# Obten el intituto del administrador
function ObtenInstituto($p_usuario)
{
  $row = RecuperaValor("SELECT fl_instituto FROM c_usuario WHERE fl_usuario=$p_usuario");
  return !empty($row[0])?$row[0]:NULL;
}

# Obtiene el nombre del intituto
function ObtenNameInstituto($p_instituto)
{
  $row = RecuperaValor("SELECT ds_instituto FROM c_instituto where fl_instituto=" . $p_instituto . "");
  $ds_instituto = str_texto($row[0]??NULL);
  return $ds_instituto;
}

# Obtiene el nombre del intituto
function ObtenFotoInstituto($p_instituto)
{
  $row = RecuperaValor("SELECT ds_foto FROM c_instituto where fl_instituto=" . $p_instituto . "");
  $ds_foto = str_texto($row[0]);
  return $ds_foto;
}

# Funcion para obtener el numero de licencias dependiendo del administrador
function ObtenNumLicencias($p_instituto)
{
  $row = RecuperaValor("SELECT  no_total_licencias FROM k_current_plan WHERE fl_instituto=$p_instituto");
   $resultado=!empty($row[0])?$row[0]:0;
  return $resultado;
}

# Funcion para obtener el numero de usuarios por escuela
function ObtenNumeroUserInst($p_instituto)
{
  $row = RecuperaValor("SELECT COUNT(*) FROM c_usuario WHERE fl_instituto=$p_instituto AND fl_perfil_sp<>" . PFL_ADMINISTRADOR . " AND fl_perfil_sp<>" . PFL_MAESTRO_SELF . " AND fg_activo='1' ");
  $resultado=!empty($row[0])?$row[0]:0;
  return $resultado;
}

# Obtenemos nombre del programa
function ObtenNombreCourse($p_course)
{
  # Aded by Ulises, select language and apply a sufix for the selection of the right lang-table on the DB
  $langselect = $_COOKIE[IDIOMA_NOMBRE];

  switch ($langselect) {
    case '1':
      $sufix = '_esp';
      break;

    case '2':
      $sufix = '';
      break;

    case '3':
      $sufix = '_fra';
      break;

    default:
      $sufix = '';
      break;
  }
  # Recupera matricula del usuario  
  $row = RecuperaValor("SELECT nb_programa" . $sufix . " FROM c_programa_sp WHERE fl_programa_sp=$p_course");
  # Commented for the use of htmlentities instead of str_uso_normal
  return str_texto(!empty($row[0])?$row[0]:NULL);
  //return $row[0];
}

# Obtenemos su ultima sesion del curso
function ObtenSessionActualCourse($p_usuario, $p_programa = 0)
{
  $Query = "SELECT MAX(no_semana) FROM c_leccion_sp cl ";
  $Query .= "LEFT JOIN k_leccion_usu klu ON(klu.fl_leccion_sp=cl.fl_leccion_sp) ";
  $Query .= "LEFT JOIN c_programa_sp cp ON(cp.fl_programa_sp=cl.fl_programa_sp) ";
  $Query .= "WHERE klu.fg_complete='1' AND klu.fl_usuario_sp=$p_usuario ";
  if (!empty($p_programa))
    $Query .= "AND cp.fl_programa_sp=$p_programa ";
  $row = RecuperaValor($Query);
  $max_week = $row[0];
  if (empty($max_week))
    $max_week = 1;
  return $max_week;
}

# Obtenemos Maximo de semanas del programa
function ObtenSemanaMaximaAlumno($p_programa)
{

  /*$Query  = "SELECT no_semanas FROM c_programa_sp a, k_programa_detalle_sp b ";
  $Query .= "WHERE a.fl_programa_sp = b.fl_programa_sp AND a.fl_programa_sp=$p_programa";
  $row = RecuperaValor($Query);
  $no_semanas = $row[0];*/
  $row = RecuperaValor("SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp=$p_programa");

  return $row[0];
}

# Funcion para obtener la semana que tiene un quiz
# Apartir de la semana actual
function GetNextWeekQuiz($p_usuario, $p_programa)
{

  $current_week = ObtenSessionActualCourse($p_usuario, $p_programa);
  if (empty($current_week))
    $current_week  = 1;
  $Query  = "SELECT no_semana FROM k_quiz_pregunta kq ";
  $Query .= "LEFT JOIN c_leccion_sp cl ON(kq.fl_leccion_sp=cl.fl_leccion_sp) ";
  $Query .= "WHERE cl.no_semana>$current_week AND fl_programa_sp=$p_programa ";
  $Query .= "AND NOT EXISTS(SELECT *FROM k_leccion_usu klu WHERE klu.fl_leccion_sp=cl.fl_leccion_sp AND klu.fl_usuario_sp=$p_usuario AND klu.fg_quiz_complete='1')";
  $Query .= "LIMIT 1 ";
  $row = RecuperaValor($Query);
  $next_week_quiz = !empty($row[0])?$row[0]:NULL;

  return $next_week_quiz;
}

# Matricula del alumno
function ObtenMatriculaAlumnoSP($p_alumno)
{

  # Recupera matricula del usuario
  $row = RecuperaValor("SELECT ds_login FROM c_usuario WHERE fl_usuario=$p_alumno");
  return $row[0];
}

# Obtenemos el grupo del alumno 
function ObtenGrupoAlumno($p_alumno, $p_programa)
{
  $Query = "SELECT kag.fl_grupo_sp FROM k_alumno_grupo_sp kag ";
  $Query .= "LEFT JOIN c_grupo_sp cg ON(cg.fl_grupo_sp=kag.fl_grupo_sp) ";
  $Query .= "WHERE kag.fl_alumno_sp=$p_alumno AND cg.fl_programa_sp=$p_programa ";
  $row = RecuperaValor($Query);
  $fl_grupo = $row[0];

  return $fl_grupo;
}

# Folio de la semana
function ObtenFolioSemanaAlumno($p_no_semana, $p_programa)
{


  # Recupera la leccion del programa 
  $row = RecuperaValor("SELECT fl_leccion_sp FROM c_leccion_sp WHERE fl_programa_sp=$p_programa AND no_semana=$p_no_semana");
  return $fl_leccion = $row[0];

  # Recupera la semana de la leccion
  // $row = RecuperaValor("SELECT fl_semana_sp FROM k_semana_sp WHERE fl_leccion_sp=$fl_leccion");
  // return $row[0];
}

# Limite de entrega
function ObtenLimiteEntregaSemana($p_alumno, $p_semana, $p_programa)
{

  # Recupera datos del alumno
  $fl_semana = ObtenFolioSemanaAlumno($p_alumno, $p_semana, $p_programa);

  # Recupera la fecha limite de entrega de la semana
  $Query  = "SELECT DATE_FORMAT(fe_entrega, '%c') 'fe_mes', DATE_FORMAT(fe_entrega, '%e, %Y') 'fe_dia_anio', DATE_FORMAT(fe_entrega, '%H:%i:%s %p') 'fe_hora' ";
  $Query .= "FROM k_semana_sp WHERE fl_semana_sp=$fl_semana";
  $row = RecuperaValor($Query);
  return ObtenNombreMes($row[0]) . " " . $row[1] . " " . $row[2];
}

# Funcion para obtener tiempo desde su ultima sesion
function time_elapsed_string($datetime, $full = false)
{
  $date = date("Y-m-d H:i:s");
  $now = new DateTime($date);
  $then = new DateTime($datetime);
  $diff = (array) $now->diff($then);

  // $diff['w']  = floor( $diff['d'] / 7 );
  // $diff['d'] -= $diff['w'] * 7;

  $string = array(
    'y' => ObtenEtiqueta(1107),
    'm' => ObtenEtiqueta(1108),
    'w' => ObtenEtiqueta(1109),
    'd' => ObtenEtiqueta(1110),
    'h' => ObtenEtiqueta(1111),
    'i' => ObtenEtiqueta(1112),
    's' => ObtenEtiqueta(1113),
  );

  foreach ($string as $k => &$v) {
    if (!empty($diff[$k])) {
      $v = $diff[$k] . ' ' . $v . ($diff[$k] > 1 ? 's' : '');
    } else {
      unset($string[$k]);
    }
  }

  if (!$full) $string = array_slice($string, 0, 1);
  return $string ? implode(', ', $string) . " " . ObtenEtiqueta(1114) : '' . ObtenEtiqueta(2367) . '';
}


# Funcion para generar EMAIL DE INVITACION
function genera_documentoSP($clave, $opc, $correo = False, $firma = False, $no_contrato = 1, $fl_template, $ds_cve, $ds_firts_name, $ds_last_name)
{

  $texto_boton = ObtenEtiqueta(920);
  $dominio_campus = ObtenConfiguracion(116);
  //$dominio_campus = "localhost:64573/vanas";#pruebas
  $src_redireccion = $dominio_campus . "/fame/confirmation.php?r=" . $ds_cve; #bueno
  /*
    $boton="<table width='100%'><tr><td align='center'><a href='".$src_redireccion."' style='background-color: #008CBA;
                border: none;
                color: white;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                cursor: pointer;'> ".$texto_boton."</a></td></tr></table> "; 
    */


  # Recupera datos del template del documento
  switch ($opc) {
    case 1:
      $campo = "ds_encabezado";
      break;
    case 2:
      $campo = "ds_cuerpo";
      break;
    case 3:
      $campo = "ds_pie";
      break;
    case 4:
      $campo = "nb_template";
      break;
  }
  $Query  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
  $row = RecuperaValor($Query);
  $cadena = str_uso_normal($row[0]);

  $nombre_ = $ds_firts_name . " " . $ds_last_name;

  # Sustituye variables con datos del alumno
  $cadena = str_replace("#sp_invitation_name#", "" . $nombre_, $cadena); # first name a quein se le envia el correo
  $cadena = str_replace("#sp_invitation_link#", "" . $src_redireccion, $cadena);  #bont link redireccion 
  $cadena = str_replace("&nbsp;", " ", $cadena);


  return ($cadena);
}


# Funcion para verificar y enviar la invitacion de los estudiantes y maestro
function Send_Invitacion($ds_email, $ds_first_name, $ds_last_name, $nb_grupo, $fl_action, $p_user_invitador, $p_user_ya_existente = "", $fl_instituto = "")
{

  if (!empty($p_user_ya_existente)) {

    $Query = "SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
    $row = RecuperaValor($Query);
    $ds_instituto = $row['ds_instituto'];

    $ds_email_registrado = 0;
    $fl_template = 175;
  } else {
    #Verificamos si la cuenta de correo ya esta activo entonces , ya no se le enviara el correo y mostrar mensaje de que la cuenta ya esta regitrada.
    $Query = "SELECT COUNT(1) FROM c_usuario WHERE  ds_email='$ds_email'  AND fl_perfil_sp IN (" . PFL_MAESTRO_SELF . "," . PFL_ESTUDIANTE_SELF . ") AND fg_activo='1' ";
    $row = RecuperaValor($Query);
    $ds_email_registrado = $row[0];

    $fl_template = 100;
  }

  # Si no existe el registro entonces envia la invitacion
  if ($ds_email_registrado == 0) {

    #Revuperamos el ultimo id del correo para saber y llevar su bitacora.
    $Query = "SELECT MAX(fl_envio_correo) AS fl_envio_correo FROM k_envio_email_reg_selfp ";
    $row = RecuperaValor($Query);
    $no_envio = $row[0];
    $no_envio = $no_envio + 1;

    # Genera una nueva clave para la liga de acceso al contrato
    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    for ($i = 0; $i < 40; $i++)
      $ds_cve .= substr($str, rand(0, 62), 1);
    $ds_cve .= date("Ymd") . $no_envio;

    #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
    $no_codigo_confirmacion = substr("$ds_cve", -30, 30);

    #se genera el cuerpo del documento de email
    $ds_encabezado = genera_documentoSP($clave, 1, True, '', '', $fl_template, $ds_cve, $ds_first_name, $ds_last_name);
    $ds_cuerpo = genera_documentoSP($clave, 2, True, '', '', $fl_template, $ds_cve, $ds_first_name, $ds_last_name);
    $ds_pie = genera_documentoSP($clave, 3, True, '', '', $fl_template, $ds_cve, $ds_first_name, $ds_last_name);

    $template_email = $ds_encabezado . $ds_cuerpo;
    $template_email .= $ds_pie;
    $ds_contenido = $template_email;

    #Recuperamo el istituto
    $Query = "SELECT fl_instituto,ds_nombres,ds_apaterno,ds_email,fl_perfil_sp FROM c_usuario WHERE fl_usuario=$p_user_invitador ";
    $row = RecuperaValor($Query);
    $fl_instituto = $row[0];
    $ds_fname_invitador = str_texto($row[1]);
    $ds_lname_invitador = str_texto($row[2]);
    $ds_email_invitador = str_texto($row[3]);
    $fl_perfil_sp=$row[4];

    if($fl_perfil_sp==PFL_MAESTRO_SELF){
        $role="Student";
    }
    if($fl_perfil_sp==PFL_ADMINISTRADOR){
        $role="Teacher";
    }
    if(empty($ds_instituto)){
        $Que="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
        $row=RecuperaValor($Que);
        $ds_instituto=$row['ds_instituto'];
    }
        

    $ds_contenido=str_replace("#perfil_user#",$role,$ds_contenido);

    #Sustitumos variables por el invitador
    $ds_contenido = str_replace("#fame_fname_invited#", $ds_fname_invitador, $ds_contenido); # first name a quien envia correo
    $ds_contenido = str_replace("#fame_lname_invited#", $ds_lname_invitador, $ds_contenido);  #last name a quien envia correo

    $ds_contenido = str_replace("#nb_instituto#", $ds_instituto, $ds_contenido);  #last name a quien envia correo
    if (!empty($p_user_ya_existente)) {

      $ds_contenido = str_replace("#fame_fname#", $ds_first_name, $ds_contenido); # first name a quien envia correo
      $ds_contenido = str_replace("#fame_lname#", $ds_last_name, $ds_contenido);  #last name a quien envia correo
      $ds_contenido = str_replace("#fame_link#", ObtenConfiguracion(116), $ds_contenido);  #last name a quien envia correo
    }

    $nombre_quien_escribe = $ds_first_name . " " . $ds_last_name;

    $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(107);
    $ds_email_destinatario = $ds_email;
    $nb_nombre_dos = ObtenEtiqueta(949); #nombre de quien envia el mensaje

    # Inicializa variables de ambiente para envio de correo
    ini_set("SMTP", MAIL_SERVER);
    ini_set("smtp_port", MAIL_PORT);
    ini_set("sendmail_from", MAIL_FROM);
    $message  = $ds_contenido;
    //$message .= "Content-type: text/html;charset=ISO-8859-1\r\n\r\n";
    $message = utf8_decode(str_ascii(str_uso_normal($message)));

    $ds_titulo = ObtenEtiqueta(950); #etiqueta de asunto del mensjae para el anunciante
    $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message);

    #Enviamos otro email desde otra configuracion.#MJD se agrega por si el primer email no llega (Peticion Mario.).
    //$mail = EnviaMailHTML($nb_nombre_dos, ObtenConfiguracion(107), $ds_email_destinatario, $ds_titulo, $message);

    #Se envia notificacion al usuario que esta invitando.
    $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_invitador, $ds_email_destinatario, $ds_titulo, $message);


    //$copy_send_email = ObtenConfiguracion(131);
    /*if ($copy_send_email) {

      $bcc = $copy_send_email;
      #Se cuelve enviar la invitacion desde otro correo
      $mail = EnviaMailHTML($nb_nombre_dos, $copy_send_email, $ds_email_destinatario, $ds_titulo, $message, $copy_send_email);
    }*/

    # Si envio el mensaje guarda el registro
    if ($mail=1) {
      # Enviamos email a fame
      EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, ObtenConfiguracion(107), $ds_titulo, $message);

      # Enviamos email AL TEACHER quien esta invitando.
      EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_invitador, $ds_titulo, $message);

      /*$copy_send_email = ObtenConfiguracion(131);
      if ($copy_send_email) {

        $bcc = $copy_send_email;
        #Se cuelve enviar la invitacion desde otro correo
        $mail = EnviaMailHTML($nb_nombre_dos, $copy_send_email, $ds_email_invitador, $ds_titulo, $message, $copy_send_email);
      }
	  */
      #Cuando la invitacion va dirigida a un usuario existente(Un alumno ya puede estar en varios Institutos).
      if (!empty($p_user_ya_existente)) {
      } else {
        #Verificamos si anteriormente se le habia mandado un correo, si ya esixte este correo entonces borramos la bitacora de envio de correo .
        $Query = "SELECT fl_envio_correo FROM k_envio_email_reg_selfp WHERE ds_email='$ds_email' AND fg_confirmado='0'  ";
        $row = RecuperaValor($Query);
        $fl_ya_se_envio_email = $row[0]??NULL;

        if ($fl_ya_se_envio_email) {

          #eliminamos el correo anteiror enviado 
          $Query = "DELETE FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_ya_se_envio_email ";
          EjecutaQuery($Query);
        }
      }

      # Dependiendo del perfil guadara el registro
      if ($fl_action == ADD_STD || $fl_action == IMP_STD) {
        $fg_tipo_registro = "S"; //Studiante,representa que es una invitacion para el admistrador
        $fl_perfil_sp=PFL_ESTUDIANTE_SELF;
      }
      if ($fl_action == ADD_MAE || $fl_action == IMP_MAE) {
        $fg_tipo_registro = "T"; //teacher
        $fl_perfil_sp=PFL_MAESTRO_SELF;
      }

      #Cuando la invitacion va dirigida a un usuario existente(Un alumno ya puede estar en varios Institutos).
      if (!empty($p_user_ya_existente)) {

        EjecutaQuery("DELETE FROM k_instituto_alumno WHERE fl_usuario_sp=$p_user_ya_existente  AND fl_instituto=$fl_instituto ");

        $qUERY = "INSERT INTO k_instituto_alumno(fl_usuario_sp,fl_instituto,fl_usuario_invitando,fe_creacion,fe_ultmod)VALUES($p_user_ya_existente,$fl_instituto,$p_user_invitador, CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ";
        EjecutaQuery($qUERY);
      } else {

        # UMP Busca si existe un fl_usuario con el email ingresado
        $fl_usuario = RecuperaValor("SELECT fl_usuario FROM c_usuario WHERE ds_email='$ds_email' WHERE fl_perfil_sp=".PFL_ESTUDIANTE_SELF." LIMIT 1");
        $fl_usuario = $fl_usuario[0]??NULL;

        # UMP Si no encuentra el fl_usuario inserta uno nuevo
        if ($fl_usuario == 0 or empty($fl_usuario)) {

          # Crea una entrada nueva en c_usuario y rescata el fl_usuario
          $fl_usuario = intval(EjecutaInsert("INSERT INTO c_usuario (ds_login, ds_password, cl_sesion, ds_nombres, ds_apaterno, ds_email, fg_activo, fe_alta, fl_perfil_sp, fl_perfil, fg_system, flag) VALUES ('', '" . rand(0, 100000) . "', '" . rand(0, 100000) . "', '$ds_first_name','$ds_last_name', '$ds_email', '0', current_timestamp, $fl_perfil_sp, 0, 'F', 0)"));
          #Generamos su token
          $token=sha256($fl_usuario);
          EjecutaQuery("UPDATE c_usuario SET token='$token' WHERE fl_usuario=$fl_usuario ");
        }

        # UMP Genera una nueva clave para id
        $str = "1234567890";
        $ds_id = NULL;
        for ($i = 0; $i < 6; $i++) {
          $ds_id .= substr($str, rand(0, 9), 1);
        }

        $ds_login3 = substr(strtolower($ds_last_name), 0, 1) . substr(strtolower($ds_first_name), 0, 1);
        $ds_login3 = $ds_login3 . $ds_id;
        $ds_login3 = $ds_login3 . str_pad($fl_usuario, 4, "0", STR_PAD_LEFT);

        //EjecutaQuery("INSERT INTO c_usuario (fl_usuario, ds_login, ds_nombres, ds_apaterno, ds_email) VALUES ('$fl_usuario','$ds_login3', '$ds_first_name','$ds_last_name','$ds_email')");

        # UMP Actualiza en c_usuario usando el fl_usuario
        EjecutaQuery("UPDATE c_usuario SET ds_login = '$ds_login3',fl_instituto=$fl_instituto WHERe fl_usuario = $fl_usuario");
        EjecutaQuery("INSERT INTO c_alumno (fl_alumno) VALUES ($fl_usuario)");

        # Si efectivamenete se envio el email entonces se guarda la bitacora de envio
        $Query  = "INSERT INTO k_envio_email_reg_selfp (ds_first_name,ds_last_name,ds_email,no_registro,fg_confirmado, ";
        $Query .= "fg_tipo_registro,fl_invitado_por_instituto,fe_alta,fe_ultmod, nb_grupo, fl_usu_invita, fl_usuario)";
        $Query .= "values('$ds_first_name','$ds_last_name','$ds_email','$no_codigo_confirmacion','0','$fg_tipo_registro', ";
        $Query .= "'$fl_instituto',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP, '$nb_grupo', $p_user_invitador, $fl_usuario) ";
        $fl_insertado = EjecutaInsert($Query);
        
        # Inserts FAME Getting Started for default
        $Query ="INSERT INTO k_usuario_programa(fl_usuario_sp, fl_programa_sp, ds_progreso, fg_terminado, fg_certificado, fg_status, fg_pagado, mn_pagado, fl_maestro, fg_status_pro, fe_inicio_programa, flag) ";
        $Query.="VALUES($fl_usuario, 33, 0,'0','0','RD', '0', 0, $p_user_invitador, '0', CURRENT_TIMESTAMP, 0)";
        $fl_usu_pro=EjecutaInsert($Query);

        #Generamos su token
        $token=sha256($fl_usu_pro);
        EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro ");

      }
    } else {

      #Cuando la invitacion va dirigida a un usuario existente(Un alumno ya puede estar en varios Institutos).
      if (!empty($p_user_ya_existente)) {

      } else {

          # Dependiendo del perfil guadara el registro
          if ($fl_action == ADD_STD || $fl_action == IMP_STD) {
              $fg_tipo_registro = "S"; //Studiante,representa que es una invitacion para el admistrador
          }
          if ($fl_action == ADD_MAE || $fl_action == IMP_MAE) {
              $fg_tipo_registro = "T"; //teacher
          }

        # Insertamos pero no enviamos el correo porque ya existe en la bd
        $Query = "INSERT INTO k_envio_email_reg_selfp (ds_first_name,ds_last_name,ds_email,no_registro,fg_confirmado, ";
        $Query .= "fg_tipo_registro,fl_invitado_por_instituto,fe_alta,fe_ultmod, fg_enviado, ds_causa, nb_grupo, fl_usu_invita) ";
        $Query .= "VALUES('$ds_first_name','$ds_last_name','$ds_email','NULL','0','$fg_tipo_registro','$fl_instituto', ";
        $Query .= "CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0', 'Email is wrong', '$nb_grupo', $p_user_invitador) ";
        EjecutaQuery($Query);
        $fl_insertado = 0;

      }
    }
  }
  return $fl_insertado;
}

#Obtenemos dias que llevo de mi plan actual. y para obtener los dias faltantes solo hay que invertir fechas.
function ObtenDiasRestantesPlan($fe_final, $fe_inicial)
{

  $Query = "SELECT DATEDIFF('$fe_final','$fe_inicial')";
  $row = RecuperaValor($Query);
  $no_dias = $row[0];

  return $no_dias;
}

# ICH: Presenta resultados de ultimo quiz contestado
function btn_consulta_resultados_quiz($fl_usuario_sp, $fl_leccion_sp)
{

  # ICH: Funcion para llamar contenido de modal por ajax
  $ds_mensaje = "
    <script>
      function muestra_res_quiz(){
        $.ajax({
          type: 'POST',
          url : 'site/consulta_res_quiz.php',
          async: false,
          data: 'fl_usuario_sp=$fl_usuario_sp'+
                '&fl_leccion_sp=$fl_leccion_sp',
          success: function(data) {
            $('#muestra_resultado_quiz').html(data);
          }
        });
      }
    </script>
    ";

  # Modal para presentar los resultados del ultimo quiz contestado    

  # Obtenemos el ultimo intento
  $row_int = RecuperaValor("SELECT no_intento FROM k_quiz_respuesta_usuario WHERE fl_leccion_sp = $fl_leccion_sp AND fl_usuario = $fl_usuario_sp GROUP BY no_intento ORDER BY no_intento DESC LIMIT 1 ");
  if (empty($row_int[0]))
    $style_btn = "display:none";
  else
    $style_btn = "display:inline-block";

  # ICH: Boton para abrir modal
  $ds_mensaje .= "<div id='valida_calif_quiz'></div>
      <span id='btn_cunsulta_res_quiz' style='$style_btn'>
      <button class='btn btn-labeled btn-warning' data-toggle='modal' style='margin-top:7px;' data-target='#myModal_result' onclick='muestra_res_quiz();'> 
      <span class='btn-label'><i class='fa fa fa-list-ul'></i></span>" . ObtenEtiqueta(1268) . "</button>
      </span>";

  # ICH: Abre el modal que contiene resultados de ultimo quiz
  $ds_mensaje .= "
    <div class='modal fade' id='myModal_result' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='overflow: auto;' data-keyboard='false' data-backdrop='static'>
      <div class='modal-dialog' style='width:65%;'>
           <div id='muestra_resultado_quiz'></div>
        </div>
      </div>
    ";
  return $ds_mensaje;
}

# ICH: Presenta quiz
function btn_presenta_quiz($fl_usuario_sp, $fl_leccion_sp)
{
  # Variable initializtion to avoid error
  $ds_mensaje=NULL;
  
  // $ds_mensaje = "
  // <link rel='stylesheet' type='text/css' media='screen' href='css/I_24102016_smartadmin-production.min.css'>
  // <script src='js/bootstrap-wizard/superbox.min.js'></script>
  // <!-- PAGE RELATED PLUGIN(S) -->
  // <script src='js/bootstrap-wizard/jquery.bootstrap.wizard.min.js'></script>
  // <script src='js/bootstrap-wizard/wizard.min.js'></script>
  // ";

  $row = RecuperaValor("SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp");
  if (!empty($row[0])) {

    # ICH: Boton presenta quiz
    $ds_mensaje .= btn_consulta_resultados_quiz($fl_usuario_sp, $fl_leccion_sp);

    # ICH: Funcion para llamar contenido de modal por ajax
    $ds_mensaje .= "
      <script>
      function test_quiz(fl_usuario_sp, fl_leccion_sp){
        $.ajax({
          type: 'POST',
          url : 'site/muestra_quiz.php',
          async: false,
          data: 'fl_usuario_sp='+fl_usuario_sp+
                '&fl_leccion_sp='+fl_leccion_sp, 
          success: function(data) {
            $('#regresa_cont').html(data);
          }
        });
      }
      </script>
    ";

    # Boton que abre el modal para contestar quiz
    $ds_mensaje .= "<a onclick='test_quiz($fl_usuario_sp, $fl_leccion_sp);' style='margin-top:7px;' class='btn btn-labeled btn-info' data-toggle='modal' data-target='#myModal_quiz'> 
    <span class='btn-label'><i class='fa fa-check-square-o'></i></span>" . ObtenEtiqueta(1264) . "</a>";

    # Abre el modal y contiene el div que regresa el contenido llamado por ajax
    $ds_mensaje .= "
    <div class='modal fade' id='myModal_quiz' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='overflow: auto;' data-keyboard='false' data-backdrop='static'>
      <div class='modal-dialog'>
        <div id='regresa_cont'></div>
      </div>
    </div>";
  }

  return $ds_mensaje;
}

# Funcion para activar el boton de mark  complete
function boton_active($fl_usuario, $fl_leccion_sp)
{
  # Verificamos si tiene quiz la leccion
  $quiz = false;
  $quiz_aprovado = 0;
  if (ExisteEnTabla('k_quiz_pregunta', 'fl_leccion_sp', $fl_leccion_sp)) {
    $quiz = true;
    # Verificamos que ya haya aprovado el Quiz
    $Query_0  = "SELECT fg_aprobado FROM c_calificacion_sp WHERE cl_calificacion=( SELECT cl_calificacion ";
    $Query_0 .= "FROM k_quiz_calif_final WHERE fl_usuario=" . $fl_usuario . " AND fl_leccion_sp=" . $fl_leccion_sp . " AND  ";
    $Query_0 .= "no_intento=(SELECT COUNT(*) FROM k_quiz_calif_final WHERE fl_usuario=" . $fl_usuario . " AND fl_leccion_sp=" . $fl_leccion_sp . ")) ";
    $row_0 = RecuperaValor($Query_0);
    $quiz_aprovado = !empty($row_0[0])?$row_0[0]:NULL;
  }
  # Verificamos si la leccion requiere trabajos
  $row_1 = RecuperaValor("SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch FROM c_leccion_sp WHERE fl_leccion_sp=" . $fl_leccion_sp);
  $fg_animacion = $row_1[0];
  $fg_ref_animacion = $row_1[1];
  $no_sketch = $row_1[2];
  $fg_ref_sketch = $row_1[3];
  if (!empty($fg_animacion) || !empty($fg_ref_animacion) || !empty($no_sketch) || !empty($fg_ref_sketch))
    $trab_require = true;
  else
    $trab_require = false;
  # Verificamos si ya entrego todos los trabajos  
  $ani_ent = 0;
  $rani_ent = 0;
  $ske_ent = 0;
  $rske_ent = 0;
  $entregado = true;
  $Query_2  = "SELECT b.fl_entregable_sp, b.fl_entrega_semanal_sp, b.fg_tipo FROM k_entrega_semanal_sp a ";
  $Query_2 .= "LEFT JOIN k_entregable_sp b ON(b.fl_entrega_semanal_sp=a.fl_entrega_semanal_sp) ";
  $Query_2 .= "WHERE fl_alumno=" . $fl_usuario . " AND fl_leccion_sp=" . $fl_leccion_sp;
  $rs_2 = EjecutaQuery($Query_2);
  for ($i = 0; $row_2 = RecuperaRegistro($rs_2); $i++) {
    $fg_tipo = $row_2[2];
    if ($fg_tipo == "A")
      $ani_ent = $ani_ent + 1;
    if ($fg_tipo == "AR")
      $rani_ent = $rani_ent + 1;
    if ($fg_tipo == "S")
      $ske_ent = $ske_ent + 1;
    if ($fg_tipo == "SR")
      $rske_ent = $rske_ent + 1;
  }

  # Verifica que ya haya entregado todos los trabajos
  if ((!empty($fg_animacion) && !empty($ani_ent)) || (!empty($fg_ref_animacion) && !empty($rani_ent))
    || (!empty($no_sketch) && $ske_ent == $no_sketch) || (!empty($fg_ref_sketch) && !empty($rske_ent))
  )
    $entregado = true;
  else
    $entregado = false;

  if ($quiz == true) {
    if ($quiz_aprovado == 1) {
      if ($trab_require == true) {
        if ($entregado)
          $btn_active = 1;
        else
          $btn_active = 0;
      } else
        $btn_active = 1;
    }
  } else {
    if ($trab_require == true) {
      if ($entregado)
        $btn_active = 1;
      else
        $btn_active = 0;
    } else
      $btn_active = 1;
  }

  return !empty($btn_active)?$btn_active:NULL;
}

# Muestra el boton para marcar como completado la session
function btn_complete_desktop($fl_usuario, $fl_leccion_sp)
{
  /*# Verifica si la leccion ya fue marcada como completa
  $row_l = RecuperaValor("SELECT fg_complete FROM k_leccion_usu WHERE fl_usuario_sp=$fl_usuario AND fl_leccion_sp=$fl_leccion_sp");
  $fg_completa = $row_l[0];
  
  # Activa el boton si no tiene un quiz
  $btn_activa = 1;
  # Buscamos si la leccion tiene quiz
  if(ExisteEnTabla('k_quiz_pregunta', 'fl_leccion_sp', $fl_leccion_sp)){
    # Buscamos el numero de intentos que realizo
    $rs = RecuperaValor("SELECT  MAX(no_intento) FROM k_quiz_calif_final WHERE fl_usuario=$fl_usuario AND fl_leccion_sp=$fl_leccion_sp");
    $tot_intentos = $rs[0];
    # Buscamos el ultimo intento del quiz si esta aprobado activa el boton completed
    $row = RecuperaValor("SELECT cl_calificacion FROM k_quiz_calif_final WHERE fl_usuario=$fl_usuario AND fl_leccion_sp=$fl_leccion_sp AND no_intento=$tot_intentos");
    $cl_calificacion = $row[0];
    $row1 = RecuperaValor("SELECT fg_aprobado FROM c_calificacion_sp WHERE cl_calificacion='".$cl_calificacion."'");
    $fg_aprobado = $row1[0];
    if($fg_aprobado==1){      
      if(!empty($fg_completa)){
        $btn_class = "btn-success";
        $etq = ObtenEtiqueta(1901);
      }
      else{
        $btn_class = "btn-danger";
        $etq = ObtenEtiqueta(1902);
      }
    }
    else{      
      $btn_class = "btn-danger disabled";
      $etq = ObtenEtiqueta(1902);
    }
  }
  else{
    # Boton para que pueda marcar como completado  
    # Buscamos si la leccion ya esta marcada como completo el boton estar activado
    $row = RecuperaValor("SELECT fg_complete FROM k_leccion_usu WHERE fl_usuario_sp=$fl_usuario AND fl_leccion_sp=$fl_leccion_sp");
    $fg_complete = $row[0];
    if($fg_complete){
      $btn_class = "btn-success";
      $etq = ObtenEtiqueta(1901);
    }
    else{
      $btn_class = "btn-danger";
      $etq = ObtenEtiqueta(1902);
    }
  }
  */
  $row_l = RecuperaValor("SELECT fg_complete FROM k_leccion_usu WHERE fl_usuario_sp=$fl_usuario AND fl_leccion_sp=$fl_leccion_sp");
  $fg_completa = !empty($row_l[0])?$row_l[0]:NULL;
  # Vamos activar el boton
  $activa_btn = boton_active($fl_usuario, $fl_leccion_sp);
  if ($activa_btn == 1) {
    $act_class = "";
    if ($fg_completa) {
      $btn_class = "btn-success";
      $etq = ObtenEtiqueta(1901);
      $completed='true';
    } else {
      $btn_class = "btn-danger";
      $etq = ObtenEtiqueta(1902);
      $completed='false';
    }
  } else {
    $act_class = "disabled";
    $btn_class = "btn-danger";
    $etq = ObtenEtiqueta(1902);
    $completed='false';
  }

  $ds_mensaje = "
  <div class='col-lg-12' style='top: -8px; left: 12px;'>
    <div class='pull-right'>";

  # ICH: Boton presenta quiz
  $ds_mensaje .= btn_presenta_quiz($fl_usuario, $fl_leccion_sp);

  $ds_mensaje .= "
      <a href='javascript:markascomplete($fl_leccion_sp, $fl_usuario, $completed);' class='btn btn-labeled " . $btn_class . " " . $act_class . " txt-color-white' id='btn_session_" . $fl_leccion_sp . "' style='margin-top:7px;'> <span class='btn-label'><i class='fa fa-check-square-o'></i></span>" . $etq . " </a>      
    </div>
  </div>
  <script>
  function markascomplete(p_lecccion, p_usuario, p_completed){
    $.ajax({
      type: 'POST',
      url: '" . PATH_SELF_SITE . "/complete_leccion.php',
      async: false,
      data: 'fl_leccion_sp='+p_lecccion+
            '&fl_usuario='+p_usuario+
            '&completed='+p_completed
    }).done(function(result){
        var content, progreso, data_programa, name_course, fl_programa, next_quiz, activado;
          content = JSON.parse(result);
          progreso = content.progreso;
          data_programa = content.programa;
          name_course = data_programa.name_program;
          fl_programa = data_programa.fl_programa;
          next_quiz = data_programa.quiznext;
          activado = content.activado;
          
          if (activado=='false'){
            // Activa el boton
            var msg = '<span class=\'btn-label\'><i class=\'fa fa-check-square-o\' aria-hidden=\'true\'></i></span>" . ObtenEtiqueta(1902) . "';
            $('#btn_session_".$fl_leccion_sp."').removeClass('btn-success').addClass('btn-danger').empty().append(msg);
            $('#btn_session_".$fl_leccion_sp."').attr('href','javascript:markascomplete($fl_leccion_sp, $fl_usuario, \'false\');');
          } else {
            // Desactiva el boton
            var msg = '<span class=\'btn-label\'><i class=\'fa fa-check-square-o\' aria-hidden=\'true\'></i></span>" . ObtenEtiqueta(1901) . "';
            $('#btn_session_".$fl_leccion_sp."').removeClass('btn-danger').addClass('btn-success').empty().append(msg);
            $('#btn_session_".$fl_leccion_sp."').attr('href','javascript:markascomplete($fl_leccion_sp, $fl_usuario, \'true\');');
          }
        
        // Barra para leccion
          $('#progress_sesssion_" . $fl_leccion_sp . "').removeClass('hidden');
        
        // Barra para el progreso del programa
          $('#span_info').html('');
          $('#span_info').append(progreso + '%');  
          $('#progreso').width(progreso + '%');
        // activamos las sessiones antes del proximo quiz
          var h;
          for(h=1;h<=next_quiz;h++){
            if($('#week_'+h).hasClass('disabled'))
              $('#week_'+h).removeClass('disabled');            
          }
        // Cuando termina el curso envara el mensaje para certificado
          if(progreso==100){
            var content  = 
                '<div class=\'row\'>'+
                  '<div class=\'col col-sm-12 col-lg-3 padding-top-10\' style=\'margin-top:20px;\'>'+
                    '<i class=\'glyphicon glyphicon-star-empty\' style=\'font-size:70px;\'></i> <i> </i>'+
                  '</div><div class=\'col col-sm-12 col-lg-9 text-aling-left\'>'+
                  '<strong><h3 class=\'no-margin\'>SUCCESS!!</h3></strong><br/>'+
                  '<div>you have completed the <strong>'+name_course+'</strong> course </div><br>'+
                  '<div>Would you like to request your Certificate?</div>'+
                  '<div class=\'row text-align-right\'><a class=\'btn bg-color-pink txt-color-white btn-xs\' id=\'btn_later_cer\'>Later</a>&nbsp;'+
                  '<a class=\'btn btn-success btn-xs\' id=\'btn_yes_cer\'>Yes</a></div>'+
                '</div></div>';
            $.smallBox({
              content: content,
              color: '#0071BD',              
            });
            
            // Botones
            $('#btn_yes_cer').click(function(){
              Modal_Certificado(fl_programa);
            });
          }
    });
    
  }
  </script>";

  return $ds_mensaje;
}
# Certificados etc
function genera_documento_sp($clave, $opc, $fl_template = 0, $programa = 0, $fl_envio_correo = ''){
  # Recupera datos del template del documento
  switch ($opc) {
    case 1:
      $campo = "ds_encabezado";
      break;
    case 2:
      $campo = "ds_cuerpo";
      break;
    case 3:
      $campo = "ds_pie";
      break;
    case 4:
      $campo = "nb_template";
      break;
  }

  # Obtenemos la informacion del template header body or footer
  $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
  $row = RecuperaValor($Query1);

  $cadena = $row[0];
  # Sustituye caracteres especiales
  $cadena = $row[0];
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);
  $cadena = str_replace("&nbsp;", " ", $cadena);

  # Recupera datos usuario
  $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno,ds_login, fg_genero, ds_email, " . ConsultaFechaBD('fe_nacimiento', FMT_FECHA) . " fe_nacimiento, fl_usu_invita ";
  $Query .= "FROM c_usuario WHERE fl_usuario=$clave ";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto(!empty($row[0])?$row[0]:null);
  $ds_lname = str_texto(!empty($row[1])?$row[1]:null);
  $ds_mname = str_texto(!empty($row[2])?$row[2]:null);
  $ds_login = str_texto(!empty($row[3])?$row[3]:null);
  $fg_genero = str_texto(!empty($row[4])?$row[4]:null);
  switch ($fg_genero) {
    case "M":
      $ds_genero = ObtenEtiqueta(115);
      break;
    case "F":
      $ds_genero = ObtenEtiqueta(116);
      break;
    case "N":
      $ds_genero = ObtenEtiqueta(128);
      break;
  }
  $ds_email = !empty($row[5])?$row[5]:null;
  $fe_nacimiento = !empty($row[6])?$row[6]:null;
  $fl_usu_invita = !empty($row[7])?$row[7]:null;

  if (empty($clave)) { #se coloca en dado caso de que la clave venga vacia.(se utiliza para envio de correo de registro de menor de edad.)

    #Recuperamos el nombre del estudinate que se registro
    $Query = "SELECT ds_first_name,ds_last_name FROM k_envio_email_reg_selfp 
			 WHERE fl_envio_correo=$fl_envio_correo ";
    $row = RecuperaValor($Query);
    $ds_fname = str_texto(!empty($row[0])?$row[0]:null);
    $ds_lname = str_texto(!empty($row[1])?$row[1]:null);
    $Query3  = "SELECT b.ds_nombres, b.ds_apaterno, b.ds_amaterno ";
    $Query3 .= "FROM k_noconfirmados_pro a, c_usuario b WHERE a.fl_maestro=b.fl_usuario AND a.fl_envio_correo=$fl_envio_correo ";
    $row3 = RecuperaValor($Query3);
    $fame_te_fname = str_texto(!empty($row3[0])?$row3[0]:null);
    $fame_te_lname = str_texto(!empty($row3[1])?$row3[1]:null);
    $cadena = str_replace("#fame_te_fname#", $fame_te_fname, $cadena);  # fname teacher
    $cadena = str_replace("#fame_te_lname#", $fame_te_lname, $cadena);  # lname teacher   
  }

  if ($ds_fname)
    $cadena = str_replace("#fame_fname#", $ds_fname, $cadena);                        # Student first name 
  $cadena = str_replace("#fame_mname#", $ds_mname, $cadena);                        # Student middle name 
  if ($ds_lname)
    $cadena = str_replace("#fame_lname#", $ds_lname, $cadena);                        # Student last name
  $cadena = str_replace("#fame_login#", $ds_login, $cadena);                        # Student login
  $cadena = str_replace("#fame_gender#",!empty($ds_gender)?$ds_gender:null , $cadena);                      # Student gender female
  $cadena = str_replace("#fame_email#", $ds_email, $cadena);                        # Student email address
  $cadena = str_replace("#fame_byear#", substr($fe_nacimiento, 6, 4), $cadena);    #Student year of birth 
  $cadena = str_replace("#fame_bmonth#", substr($fe_nacimiento, 3, 2), $cadena);   #Student month of birth 
  $cadena = str_replace("#fame_bday#", substr($fe_nacimiento, 0, 2), $cadena);     #Student day of birth 

  # Datos del programa
  if (!empty($programa)) {
    $nb_programa = ObtenNombreCourse($programa);
    $cadena = str_replace("#fame_pg_name#", $nb_programa, $cadena);                 # Program name
  }

  # Obtenemos iinformacion de la direccion
  $row = RecuperaValor("SELECT a.fl_pais, nb_pais, ds_state, ds_city, ds_number, ds_street, ds_zip, ds_phone_number  
FROM k_usu_direccion_sp a, c_pais b WHERE a.fl_pais=b.fl_pais AND a.fl_usuario_sp=$clave");
  $fl_pais = !empty($row[0])?$row[0]:null;
  $nb_pais = str_texto(!empty($row[1])?$row[1]:null);
  if ($fl_pais == 38) {
    $row1 = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$row[2]");
    $ds_state = $row1[0];
  } else
    $ds_state = str_texto(!empty($row[2])?$row[2]:null);
  $ds_city = str_texto(!empty($row[3])?$row[3]:null);
  $ds_number = str_texto(!empty($row[4])?$row[4]:null);
  $ds_street = str_texto(!empty($row[5])?$row[5]:null);
  $ds_zip = str_texto(!empty($row[6])?$row[6]:null);
  $ds_phone_number = str_texto(!empty($row[7])?$row[7]:null);

  $cadena = str_replace("#fame_street_no#", $ds_number, $cadena);                   # Student number street
  $cadena = str_replace("#fame_street_name#", $ds_street, $cadena);                 # Student name street
  $cadena = str_replace("#fame_city#", $ds_city, $cadena);                          # Student city
  $cadena = str_replace("#fame_state#", $ds_state, $cadena);                        # Student state
  $cadena = str_replace("#fame_country#", $nb_pais, $cadena);                       # Student country
  $cadena = str_replace("#fame_code_zip#", $ds_zip, $cadena);                       # Student zip
  $cadena = str_replace("#fame_phone#", $ds_phone_number, $cadena);                 # Student phone number




  $fl_instituto = ObtenInstituto($clave); #identificamos el id del instituto
  /***********************************/
  $Query = "SELECT fg_plan ,no_licencias_usadas,no_licencias_disponibles,fl_princing FROM k_current_plan where fl_instituto=$fl_instituto ";
  $row = RecuperaValor($Query);
  $fg_plan = !empty($row[0])?$row[0]:null;
  $no_licencias_usadas = !empty($row[1])?$row[1]:null;
  $no_licencias_disponibles = !empty($row[2])?$row[2]:null;
  $fl_princi = !empty($row[3])?$row[3]:null;

  $Query = "SELECT ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princi ";
  $row = RecuperaValor($Query);
  $mn_descuento_anual = number_format(!empty($row[0])?$row[0]:0) . "%";
  $mn_descuento_mensual = number_format(!empty($row[1])?$row[1]:0) . "%";



  if ($fg_plan == 'A') {
    $plan_actual = ObtenEtiqueta(1503);
    $mn_descuento_plan = $mn_descuento_anual;
  } else {
    $plan_actual = ObtenEtiqueta(1763);
    $mn_descuento_plan = $mn_descuento_mensual;
  }



  #Verificamos si el Instituto ha solicitado cambiado de plan, e identificmos su nuevo pan/nueva suscripcion. el fg_motivo=3 quiere decir que es cambio de plan.
  $QueryP = "SELECT fg_cambio_plan FROM  k_cron_plan_fame WHERE fg_motivo_pago='3' AND fl_instituto=$fl_instituto ";
  $rowP = RecuperaValor($QueryP);
  $fg_nuevo_plan = !empty($rowP[0])?$rowP[0]:null;



  if ($fg_nuevo_plan == 'A')
    $nuevo_plan = ObtenEtiqueta(1503);
  if ($fg_nuevo_plan == 'M')
    $nuevo_plan = ObtenEtiqueta(1763);



  #Recuperamos las licencias totales del instituto
  $total_licencias = ObtenNumLicencias($fl_instituto);  # total licencias actuales

  $dominio_campus = ObtenConfiguracion(116);
  $link_login_fame = $dominio_campus; #bueno#fame_link_login#;


  $fecha_termino_plan = ObtenFechaFinalizacionContratoPlan($fl_instituto);

  #damos formato ala fecha de finalizacion.
  #DAMOS FORMATO DIA,MES, ANÑO

  $fe_termino = strtotime('+0 day', strtotime($fecha_termino_plan));
  $fe_termino = date('Y-m-d', $fe_termino);

  $date = date_create($fe_termino);
  $fe_terminacion_plan = date_format($date, 'F j , Y');




  #Varibales para sustituir para nitificaciones realizadas en billing.
  $cadena = str_replace("#fame_current_plan#", $plan_actual, $cadena);  #plan actual/mont/anual
  $cadena = str_replace("#fame_new_plan#", (!empty($nuevo_plan)?$nuevo_plan:null), $cadena);  #nuevo_plan
  $cadena = str_replace("#fame_available_licenses#", $no_licencias_disponibles, $cadena);  #licencisas disponibles
  $cadena = str_replace("#fame_licenses_used#", $no_licencias_usadas, $cadena); #lidcenias usadas
  $cadena = str_replace("#fame_total_licenses#", $total_licencias, $cadena);  #total de licencias 
  $cadena = str_replace("#fame_link_login#", $link_login_fame, $cadena);  #total de licencias 
  $cadena = str_replace("#fame_fe_expiration_plan#", $fe_terminacion_plan, $cadena);  #total de licencias 
  $cadena = str_replace("#fame_discount_plan#", $mn_descuento_plan, $cadena);  #total de licencias 

  # Obtenemos los datos del maestro
  $Query3  = "SELECT b.ds_nombres, b.ds_apaterno, b.ds_amaterno FROM k_usuario_programa a ";
  $Query3 .= "LEFT JOIN c_usuario b ON(a.fl_maestro=b.fl_usuario) ";
  $Query3 .= "WHERE fl_programa_sp=$programa AND fl_usuario_sp=$clave ";
  $row3 = RecuperaValor($Query3);
  $fame_te_fname = str_texto(!empty($row3[0])?$row3[0]:null);
  $fame_te_lname = str_texto(!empty($row3[1])?$row3[1]:null);
  if (empty($fame_te_fname) || empty($fame_te_lname)) {
    $row00 = RecuperaValor("SELECT ds_nombres, ds_apaterno, ds_amaterno FROM c_usuario WHERE fl_usuario=$fl_usu_invita");
    $fame_te_fname = str_texto(!empty($row00[0])?$row00[0]:null);
    $fame_te_lname = str_texto(!empty($row00[1])?$row00[1]:null);
    $cadena = str_replace("#fame_te_fname#", $fame_te_fname, $cadena);  # fname teacher
    $cadena = str_replace("#fame_te_lname#", $fame_te_lname, $cadena);  # lname teacher 
  } else {
    $cadena = str_replace("#fame_te_fname#", $fame_te_fname, $cadena);  # fname teacher
    $cadena = str_replace("#fame_te_lname#", $fame_te_lname, $cadena);  # lname teacher 
  }



  #Recuperamos datos del administrado del Instituto.
  $Query = "SELECT A.fl_usuario_sp,U.ds_nombres,U.ds_apaterno FROM c_instituto A 
          JOIN c_usuario U ON U.fl_usuario=A.fl_usuario_sp
           WHERE A.fl_instituto =$fl_instituto ";
  $row = RecuperaValor($Query);
  $fame_fname_admin = str_texto(!empty($row[1])?$row[1]:null);
  $fame_lname_admin = str_texto(!empty($row[2])?$row[2]:null);

  $cadena = str_replace("#fame_adm_fname#", $fame_fname_admin, $cadena);  # fname teacher
  $cadena = str_replace("#fame_adm_lname#", $fame_lname_admin, $cadena);  # lname teacher 


  return (str_uso_normal($cadena));
}






# Funcion verifica si la intitucion esta en modo trial
function Obten_Status_Trial($p_instituto)
{
  $row = RecuperaValor("SELECT fg_tiene_plan FROM c_instituto WHERE fl_instituto=$p_instituto ");
  $fg_plan = $row[0];
  if (!empty($fg_plan))
    $status = True;
  else
    $status = False;

  return $status;
}

# Funcion para obtener las licencias disponibles en modo trial
function Licencias_disponibles_Trial($p_instituto)
{
  

  #2020 -sep  verificamos que el instituto no sea b2c.
    $Query="SELECT fg_b2c,no_tot_licencias_b2c FROM c_instituto WHERE fl_instituto=$p_instituto ";
  $row=RecuperaValor($Query);
  $fg_b2c=$row[0];
  
  if($fg_b2c==1){
      $no_max_user = $row['no_tot_licencias_b2c'];
  }else{
      # Maximo de usuarios
      $no_max_user = ObtenConfiguracion(102);
  }
  $no_users_inst = ObtenNumeroUserInst($p_instituto);
  # Realizamos el calculo de cuantas licencias tiene disponibles
  $licencias_disponibles = $no_max_user - $no_users_inst;

  return $licencias_disponibles;
}

# Funcion para validar la cantidad de usuarios en modo trial por institucion
# Esto solo aparaecer a los administradores o teachers
function ValidaUserTrial($p_usuario)
{
  # Variales de los trials
  $no_days_trial = ObtenConfiguracion(101);
  $no_max_user = ObtenConfiguracion(102);

  #2020 -sep  verificamos que el instituto no sea b2c.
  $Query="SELECT fg_b2c,no_tot_licencias_b2c FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fg_b2c=$row[0];
  if($fg_b2c==1){
      $no_max_user=$row['no_tot_licencias_b2c'];
  }

  # Obtenemos el perfil del usuario
  $fl_perfil = ObtenPerfilUsuario($p_usuario);
  if ($fl_perfil == PFL_ADMINISTRADOR  || $fl_perfil == PFL_MAESTRO_SELF || $fl_perfil==PFL_ADM_CSF) {

    # Obtenemos la institucion del usuario
    $fl_instituto = ObtenInstituto($p_usuario);

    # Verificamos si la escuela esta en modo trial o no    
    $fg_plan = Obten_Status_Trial($fl_instituto);
    if (!empty($fg_plan)) {
      # Obtenemos el numero del usuario de la institucion
      $licencias_disponibles = Licencias_disponibles_Trial($fl_instituto);
      if ($licencias_disponibles == 0)
        $status = 0;
      else
        $status = 1;
    } else
      $status = 1;
  }
  return $status;
}



/**
 * MJD Summary of ObtenEtiquetaPlanRenovacion funcion para formatear etiquetas (planes de renovacion en Billing FAME(billing.php)  )
 * @param mixed $fl_instituto  
 * @param mixed $fe_terminacion_plan 
 * @param mixed $etq 
 * @return mixed
 */



function ObtenEtiquetaPlanRenovacion($fl_instituto, $fe_terminacion_plan, $etq)
{

  #Identificamos el tipo de plan.
  $Query = "SELECT no_total_licencias,fg_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
  $row = RecuperaValor($Query);
  $no_licencias_actuales = $row['no_total_licencias'];
  $fg_plan_a = $row['fg_plan'];

  if ($fg_plan_a = 'A') {

    $fg_plan_a = "12 months";
  } else {

    $fg_plan_a = "1 month";
  }




  #Formateamos etiquetas, con la fecha de expiracion.
  $cadena = str_replace("#fe_expiration_plan#", $fe_terminacion_plan, $etq); # nb_isntituto 
  $cadena = str_replace("#tipo_plan#", $fg_plan_a, $cadena);
  $cadena = str_replace("#no_licencias_actuales#", $no_licencias_actuales, $cadena);


  return $cadena;
}

# Funcion para obtener los dias que le restan del trial
function ObtenDiasTrial($p_instituto)
{
  $row = RecuperaValor("SELECT DATEDIFF(fe_trial_expiracion, now())  FROM c_instituto WHERE fl_instituto =$p_instituto");
  $no_dias = $row[0];
  return $no_dias;
}



#funcion para saber que plan tiene actualmente el Instituto(Mess/Anual, una vez que ya tiene contrataod un plan de pago).
function ObtenPlanActualInstituto($fl_instituto)
{

  $Query = "SELECT fg_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
  $row = RecuperaValor($Query);
  $resultado = $row[0]??NULL;
  return $resultado;
}
#Funcion para saber cuantas licencias didsponibes tiene el Instituto.
function ObtenNumLicenciasDisponibles($p_instituto)
{
  $row = RecuperaValor("SELECT no_licencias_disponibles FROM k_current_plan WHERE fl_instituto=$p_instituto");
  $resultado=$row[0];
  return $resultado;
}

#Funcion para saber las licncias usadas por el Instituto.
function ObtenNumLicenciasUsadas($p_instituto)
{
  $row = RecuperaValor("SELECT 	no_licencias_usadas FROM k_current_plan WHERE fl_instituto=$p_instituto");
   $resultado=!empty($row[0])?$row[0]:null;
  return $resultado;
}

#funcion para conocer cuando finaliza el plan del Instituto,(una vez que ya tiene contratado un plan de pago).
function ObtenFechaFinalizacionContratoPlan($p_instituto)
{
  $row = RecuperaValor("SELECT 	fe_periodo_final FROM k_current_plan WHERE fl_instituto=$p_instituto");
  $resultado=!empty($row[0])?$row[0]:null;
  return $resultado;
}

#Funcion para renovacion del plan ||  Mismas licencias mismo plan Anual/Mensual  ||
function RenovarPlanActualInstituto($p_instituto, $p_plan_actual, $fl_usuario = '')
{



  #Verificamos que no haya cambiado de plan Montly/anaual
  $Query = "SELECT COUNT(*) FROM k_cron_plan_fame WHERE fl_instituto=$p_instituto AND fg_motivo_pago='3' ";
  $row = RecuperaValor($Query);
  $existe = $row[0];

  if ($existe > 0) {
  } else {

    #Obtenemos fecha actual :
    $Query = "Select CURDATE() ";
    $row = RecuperaValor($Query);
    $fe_actual = str_texto($row[0]);
    $fe_actual = strtotime('+0 day', strtotime($fe_actual));
    $fe_actual = date('Y-m-d', $fe_actual);

    #Obtenemos licencias actuales y licencias disponibles
    $no_licencias_totales_actuales = ObtenNumLicencias($p_instituto);
    $no_licencias_disponibles = ObtenNumLicenciasDisponibles($p_instituto);


    #Recuperamos variables generales del Instituto. 
    $Query = "SELECT fl_princing,fl_current_plan FROM k_current_plan WHERE fl_instituto=$p_instituto  ";
    $row = RecuperaValor($Query);
    $fl_princing_actual = $row[0];
    $fl_current_plan_actual = $row[1];





    # 1. Recuperamos la fecha del ultimo mes a vencer,del plan.
    $Query = "SELECT fe_periodo_final,mn_total,fl_current_plan FROM k_admin_pagos WHERE fl_current_plan=$fl_current_plan_actual  ORDER BY fl_admin_pagos DESC ";
    $row = RecuperaValor($Query);
    $fe_periodo_actual = $row['fe_periodo_final'];

    #2. Se calcula su fecha de inicio y fecha final del nuevo plan (es decir a la fecha le sumaos un mes.)  
    $fe_inicio_periodo = $fe_periodo_actual;





    $fe_final_periodo = ObtenFechaFinalizacionRenovacionContratoPlan($fe_inicio_periodo, $p_plan_actual);



    if ($p_plan_actual == "M") { #Mes 

      #3.Recuperamos la tarifa actual existente de la institucion  y que esta ligado al plan.
      $Query = "SELECT mn_mensual FROM c_princing WHERE fl_princing=$fl_princing_actual ";
      $row = RecuperaValor($Query);
      $mn_costo_mensual = $row['mn_mensual'];

      #seRaliza el calculo para saber el costo:
      $mn_mensual_total = $no_licencias_totales_actuales * $mn_costo_mensual;

      #4.Se gegera nuevo registro del plan.
      $fg_pagado = "0";

      /*     #se inserta el registro y costo por mes       
                        $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago)";
                        $Query.="VALUES($fl_current_plan_actual,$mn_mensual_total,'1','$fe_inicio_periodo','$fe_final_periodo','$fg_pagado', '') ";
                        $fl_adm_pagos=EjecutaInsert($Query);
                     */
      $mn_monto_total_a_pagar = $mn_mensual_total;
    }
    if ($p_plan_actual == "A") { #Anio

      #3.Recuperamos la tarifa actual existente de la institucion  y que esta ligado al plan.
      $Query = "SELECT mn_anual FROM c_princing WHERE fl_princing=$fl_princing_actual ";
      $row = RecuperaValor($Query);
      $mn_costo_anual = $row['mn_anual'];

      #seRaliza el calculo para saber el costo:
      $mn_anual_total = ($no_licencias_totales_actuales * $mn_costo_anual) * 12;

      $mn_costo_mensual = $mn_anual_total / 12;

      #Agregamos la bitacora para el cobro mensual 
      $contador = 0;
      /*  for ($i=1;$i<=12;$i++){#ciclo que comprende los 12 meses
                             $contador++;
                             
                             #calculamos la fecha final del periodo por mes(es decir a la fecha le sumaos un mes.)
                             $fe_final_periodo=strtotime('+1 month',strtotime($fe_periodo_actual));
                             $fe_final_periodo= date('Y-m-d',$fe_final_periodo);

                             $fg_pagado="0"; 

                             $fg_publicar="1";
                             $ds_descripcion=ObtenEtiqueta(1554)." ".$no_licencias_totales_actuales." licences";
                             
                             #se inserta el registro y costo por mes       
                             $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion)";
                             $Query.="VALUES($fl_current_plan_actual,$mn_costo_mensual,'$fg_publicar','$fe_periodo_actual','$fe_final_periodo','$fg_pagado','','$ds_descripcion') ";
                             $fl_adm_pagos=EjecutaInsert($Query);
                             
                             
                             $fe_periodo_actual=$fe_final_periodo;
 
                         }
                        */


      $mn_monto_total_a_pagar = $mn_anual_total;
    }

    /*  #actualizamo la fecha de inicio de vigencia y fevha final de vigencia del plan.
                $Query="UPDATE k_current_plan SET fe_periodo_final='$fe_final_periodo' ";
                $Query.="WHERE fl_current_plan =$fl_current_plan_actual ";
                EjecutaQuery($Query);
            */
  } #end else $existe      



  #se genera el cuerpo del documento de email$fl_usuario(reducir licencias)
  $ds_encabezado = genera_documento_sp($fl_usuario, 1, 113, '');
  $ds_cuerpo = genera_documento_sp($fl_usuario, 2, 113, '');
  $ds_pie = genera_documento_sp($fl_usuario, 3, 113, '');
  $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;

  $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row = RecuperaValor($Query);
  $ds_email = $row[0];

  $ds_titulo = ObtenEtiqueta(1597); #etiqueta de asunto del mensjae para el anunciante reduce my contrcat 
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4);
  $ds_email_destinatario = $ds_email;
  $nb_nombre_dos = ObtenEtiqueta(949); #nombre de quien envia el mensaje         
  $bcc = ObtenConfiguracion(107);
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
}


#Funcion que permite reducir la licencias actuales del Instituto.

function ReducirLicenciasPlanActualInstituto($p_instituto, $p_plan_actual, $p_no_licencias, $fl_usuario = '')
{


  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual = strtotime('+0 day', strtotime($fe_actual));
  $fe_actual = date('Y-m-d', $fe_actual);


  #Recuperamos variables generales del Instituto 
  $Query = "SELECT fl_princing,fl_current_plan FROM k_current_plan WHERE fl_instituto=$p_instituto  ";
  $row = RecuperaValor($Query);
  $fl_princing_actual = $row['fl_princing'];
  $fl_current_plan_actual = $row['fl_current_plan'];



  $contador = 0;
  for ($i = 1; $i <= $p_no_licencias; $i++) { #ciclo que comprende el no_licencias_eliminadas.Se realiza todo este ciclo ya que puede existrir usuarios registrados que sobrepasen el numero de licencias entonces e van desactivando los usuarios.
    $contador++;

    $nuevo_total_licencias = ObtenNumLicencias($p_instituto);
    $nuevo_total_lic_disponibles = ObtenNumLicenciasDisponibles($p_instituto);
    $nuevo_total_lic_usadas = ObtenNumLicenciasUsadas($p_instituto);


    #1 aplica al total./se va reduciendo el total
    if ($nuevo_total_licencias) {

      $nuevo_total_licencias = $nuevo_total_licencias - 1;



      $Query = "UPDATE k_current_plan SET no_total_licencias=$nuevo_total_licencias ";
      $Query .= "WHERE fl_current_plan=$fl_current_plan_actual ";
      EjecutaQuery($Query);
    }


    #2aplica als licencias disponibles/se va reducuiendo el no. de disponibles si existe
    if ($nuevo_total_lic_disponibles) {

      $nuevo_total_lic_disponibles = $nuevo_total_lic_disponibles - 1;


      $Query = "UPDATE k_current_plan SET no_licencias_disponibles=$nuevo_total_lic_disponibles ";
      $Query .= "WHERE fl_current_plan=$fl_current_plan_actual ";
      EjecutaQuery($Query);
    }

    #3 aplica sobre las usadas/ se va reduciendo las licencias usadads.
    if ($nuevo_total_lic_disponibles == 0) {

      #3aplica sobre las licencias en uso si ya no existe licencias disponibles
      $nuevo_total_lic_usadas = $nuevo_total_licencias;
      $Query = "UPDATE k_current_plan SET no_licencias_usadas=$nuevo_total_lic_usadas ";
      $Query .= "WHERE fl_current_plan=$fl_current_plan_actual ";
      EjecutaQuery($Query);
    }
  }

  #Recuperamos el total de licencias del Instituto.
  $nuevo_total_licencias = ObtenNumLicencias($p_instituto);

  #Verificamos en que rango se encuentar el no. de licencias para aplicar nuevos costos.apartir del mes siguiente.
  $Query = "SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$p_instituto ";
  $rs = EjecutaQuery($Query);
  for ($i = 1; $row = RecuperaRegistro($rs); $i++) {

    $mn_rango_ini = $row['no_ini'];
    $mn_rango_fin = $row['no_fin'];

    if (($nuevo_total_licencias >= $mn_rango_ini) && ($nuevo_total_licencias <= $mn_rango_fin)) {

      $fl_plan = $row['fl_princing'];
    }
  }
  #Recuperamos costos segun el plan .
  $Query = "SELECT mn_mensual,mn_anual FROM c_princing WHERE fl_princing=$fl_plan";
  $row = RecuperaValor($Query);
  $mn_costo_mensual = $row[0];
  $mn_costo_anual = $row[1];
  $porc_tax = Tax_Can_User($fl_usuario);
  if ($p_plan_actual == "A") { #Plan Anual

    #Se calcula el costo anual
    $mn_costo_anual_total = ($nuevo_total_licencias * $mn_costo_anual) * 12;

    #Se le suma el tax actual.
    $mn_tax_correspondiente = $mn_costo_anual_total * $porc_tax;
    $mn_costo_anual_total_con_tax = $mn_costo_anual_total + $mn_tax_correspondiente;
    #Se calcula el costo mensual sera reflejado hasta el proximo mes.
    $mn_costo_mensual_anual = $mn_costo_anual_total / 12;


    #Se actualiza registros del plan actual
    $Query = "UPDATE k_current_plan SET mn_total_plan=$mn_costo_anual_total_con_tax , fl_princing=$fl_plan ";
    $Query .= "WHERE fl_current_plan=$fl_current_plan_actual ";
    EjecutaQuery($Query);

    #se actualiza nuevas tarifas de las menusalidades que restan por pagar.
    // $Query="UPDATE k_admin_pagos SET mn_total=$mn_costo_mensual_anual ";
    //$Query.="WHERE fl_current_plan=$fl_current_plan_actual AND fg_pagado='0'  ";
    //  EjecutaQuery($Query);


  }
  if ($p_plan_actual == "M") { #Plan Mes

    #Se calcula el costo total    #no_licencias * el costo
    $mn_mensual_total = $nuevo_total_licencias * $mn_costo_mensual;
    #Se le suma el tax actual.
    $mn_tax_correspondiente = $mn_mensual_total * $porc_tax;
    $mn_mensual_total_con_tax = $mn_mensual_total + $mn_tax_correspondiente;
    #Se actualiza registros del plan actual
    $Query = "UPDATE k_current_plan SET mn_total_plan=$mn_mensual_total_con_tax, fl_princing=$fl_plan ";
    $Query .= "WHERE fl_current_plan=$fl_current_plan_actual ";
    EjecutaQuery($Query);
  }



  #Ontenemos valores claves.
  $nuevo_no_licencias = ObtenNumLicencias($p_instituto);
  $fe_terminacion_plan = ObtenFechaFinalizacionContratoPlan($p_instituto);
  $ds_plan = ObtenNombrePlanFame($p_instituto);
  $mn_tax_normal = $mn_tax_correspondiente;


  #Se calcula la fecha posterios a terminacion del plan para ejecucion de cron
  $fe_final_periodo = strtotime('+0 day', strtotime($fe_terminacion_plan));
  $fe_ejecucion = date('Y-m-d', $fe_final_periodo);

  if ($p_plan_actual == 'A') {
    $ds_descripcion = $ds_plan . "-" . ObtenEtiqueta(1706) . " " . $nuevo_no_licencias . " licences";
    $mn_monto_normal = $mn_costo_anual_total;
    $mn_costo_x_licencia_bd = $mn_costo_anual;
  } else {

    $ds_descripcion = $ds_plan . "-" . ObtenEtiqueta(1705) . " " . $nuevo_no_licencias . " licences";
    $mn_monto_normal = $mn_mensual_total;
    $mn_costo_x_licencia_bd = $mn_costo_mensual;
  }




  #Se realiza el proceso en Stripe para actualizar montos.
  #Recuperamos los ids de strippe creados del Instituto. 
  #Recuperamos el id del plan creado en stripe, para actualizar el monto y tarifa. y desues recuperalos en el cron a ejecutarse
  $Query = "SELECT id_plan_stripe,id_cliente_stripe,id_suscripcion_stripe,ds_email_stripe FROM k_current_plan WHERE fl_instituto=$p_instituto  ";
  $row = RecuperaValor($Query);
  $id_plan_creado_instituto = str_texto($row['id_plan_stripe']);
  $id_custom_creado_instituto = str_texto($row['id_cliente_stripe']);
  $id_suscripcion_creado_instituto = str_texto($row['id_suscripcion_stripe']);
  $ds_email_custom = str_texto($row['ds_email_stripe']);



  #Verificamos si existe un registro del instituto.
  $Query = "SELECT COUNT(*) FROM k_cron_plan_fame WHERE fl_instituto=$p_instituto ";
  $row = RecuperaValor($Query);
  $existe = $row[0];


  #Guardamos en la DB FAME para que al finalizar su plan , corra un cron y actualize datos en strippe con las nuevas cantidades y  uevos montos.

  if ($existe) {

    #Actualiza datos    
    $Query = "UPDATE k_cron_plan_fame SET fe_ejecucion='$fe_ejecucion',id_cliente_stripe='$id_custom_creado_instituto',id_plan_stripe='$id_plan_creado_instituto', ";
    $Query .= "id_suscripcion_stripe='$id_suscripcion_creado_instituto',fg_motivo_pago='1',ds_email='$ds_email_custom',ds_descripcion_pago='$ds_descripcion',mn_monto_por_licencia=$mn_costo_x_licencia_bd,mn_cantidad_licencias=$nuevo_total_licencias ,  fe_creacion=CURRENT_TIMESTAMP  WHERE fl_instituto=$p_instituto ";
    EjecutaQuery($Query);
  } else {

    #Se guarda el registro solo en BD Vanas, para despues recueprarlos al finalizar el plan actual,despues para cancelarlo  y despues crear un nuevo plan en Strippe.
    #NOTA:Se actualizaran sus datos correspondientes en la BD. y al final su plan , existe un cron que recuperara los datos actuales del Instituto y generara un nuevo plan en Stripe.
    $Query = "INSERT INTO k_cron_plan_fame (fe_ejecucion,fl_instituto,id_cliente_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias,fe_creacion) ";
    $Query .= "VALUES('$fe_ejecucion',$p_instituto,'$id_custom_creado_instituto','$id_plan_creado_instituto','$id_suscripcion_creado_instituto','1','$ds_email_custom','$ds_descripcion', $mn_costo_x_licencia_bd,$nuevo_total_licencias,CURRENT_TIMESTAMP) ";
    $fl_cron = EjecutaInsert($Query);
  }






  #se genera el cuerpo del documento de email$fl_usuario(reducir licencias)
  $ds_encabezado = genera_documento_sp($fl_usuario, 1, 110, '');
  $ds_cuerpo = genera_documento_sp($fl_usuario, 2, 110, '');
  $ds_pie = genera_documento_sp($fl_usuario, 3, 110, '');
  $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;

  $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row = RecuperaValor($Query);
  $ds_email = $row[0];

  $ds_titulo = ObtenEtiqueta(1642); #etiqueta de asunto del mensjae para el anunciante reduce my contrcat 
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4);
  $ds_email_destinatario = $ds_email;
  $nb_nombre_dos = ObtenEtiqueta(949); #nombre de quien envia el mensaje         
  $bcc = ObtenConfiguracion(107);
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
}




#Funcion que permite Cambio de plan de Mes/Anual solo aplica para qeuines contratado plan mensual
function CambiarPlanMensualAnual($p_instituto, $p_plan_actual, $fl_usuario = '')
{


  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual = strtotime('+0 day', strtotime($fe_actual));
  $fe_actual = date('Y-m-d', $fe_actual);

  #Obtenemos fecha que servira para inicializar la fcha de icinio de nuevo contrato anual.
  $fe_terminacion_plan = ObtenFechaFinalizacionContratoPlan($p_instituto);
  $ds_plan = ObtenNombrePlanFame($p_instituto);

  #Se calcula la fecha posterios a terminacion del plan para ejecucion de cron
  $fe_final_periodo = strtotime('+1 day', strtotime($fe_terminacion_plan));
  $fe_ejecucion = date('Y-m-d', $fe_final_periodo);


  #Recuperamos variables generales del Instituto 
  $Query = "SELECT fl_princing,fl_current_plan FROM k_current_plan WHERE fl_instituto=$p_instituto  ";
  $row = RecuperaValor($Query);
  $fl_princing_actual = $row['fl_princing'];
  $fl_current_plan_actual = $row['fl_current_plan'];

  #Recuperamos el costo que tiene el pan elegido.
  $Query = "SELECT mn_anual,mn_mensual FROM c_princing WHERE fl_princing=$fl_princing_actual ";
  $row = RecuperaValor($Query);
  $mn_anual = $row[0];
  $mn_mensual = $row[1];


  #Recuperamos el id del plan creado en stripe, para actualizar el monto y tarifa. y desues recuperalos en el cron a ejecutarse
  $Query = "SELECT id_plan_stripe,id_cliente_stripe,id_suscripcion_stripe,ds_email_stripe FROM k_current_plan WHERE fl_instituto=$p_instituto  ";
  $row = RecuperaValor($Query);
  $id_plan_creado_instituto = str_texto($row['id_plan_stripe']);
  $id_custom_creado_instituto = str_texto($row['id_cliente_stripe']);
  $id_suscripcion_creado_instituto = str_texto($row['id_suscripcion_stripe']);
  $ds_email_custom = str_texto($row['ds_email_stripe']);


  #Recuperamos licencias actuales:
  $total_licencias = ObtenNumLicencias($p_instituto);
  #sE INVIERTEN LOS PAPELES YA QUE SE CAMBIA DE PLAN   


  $Query = "SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$p_instituto ";
  $row = RecuperaValor($Query);
  $nb_instituto = str_texto($row[0]);

  $rand = rand(5, 1000);


  if ($p_plan_actual == 'M') {

    #Realizamos calculos de costos
    $mn_total_anual_sin_tax = ($total_licencias * $mn_anual) * 12;
    $ds_descripcion = $ds_plan . "-" . ObtenEtiqueta(1706) . " " . $total_licencias . " licences";
    $mn_costo_x_licencia_bd = $mn_anual;
    $fg_cambio_plan = 'A';
    $id_plan_creado_instituto = $ds_plan . "-" . $nb_instituto . "-" . ObtenEtiqueta(1706) . "-" . $rand;
  }

  if ($p_plan_actual == 'A') {

    #Realizamos calculos de costos
    $mn_total_mensual_sin_tax = $total_licencias * $mn_mensual;
    $ds_descripcion = $ds_plan . "-" . ObtenEtiqueta(1705) . " " . $total_licencias . " licences";
    $mn_costo_x_licencia_bd = $mn_mensual;
    $fg_cambio_plan = 'M';
    $id_plan_creado_instituto = $ds_plan . "-" . $nb_instituto . "-" . ObtenEtiqueta(1705) . "-" . $rand;
  }




  /********Para generar el cronjob************/
  #Verificamos si existe un registro del instituto.
  $Query = "SELECT COUNT(*) FROM k_cron_plan_fame WHERE fl_instituto=$p_instituto ";
  $row = RecuperaValor($Query);
  $existe = $row[0];


  #Guardamos en la DB FAME para que al finalizar su plan , corra un cron y actualize datos en strippe con las nuevas cantidades y  uevos montos, asi mismo en la bd..

  if ($existe) {

    #Actualiza datos  fg_motivo 3=ambi_plan   
    $Query = "UPDATE k_cron_plan_fame SET fe_ejecucion='$fe_ejecucion',id_cliente_stripe='$id_custom_creado_instituto',id_plan_stripe='$id_plan_creado_instituto', ";
    $Query .= "id_suscripcion_stripe='$id_suscripcion_creado_instituto',fg_cambio_plan='$fg_cambio_plan',  fg_motivo_pago='3',ds_email='$ds_email_custom',ds_descripcion_pago='$ds_descripcion',mn_monto_por_licencia=$mn_costo_x_licencia_bd,mn_cantidad_licencias=$total_licencias ,  fe_creacion=CURRENT_TIMESTAMP  WHERE fl_instituto=$p_instituto ";
    EjecutaQuery($Query);
  } else {

    #Se guarda el registro solo en BD Vanas, para despues recueprarlos al finalizar el plan actual,despues para cancelarlo  y despues crear un nuevo plan en Strippe.
    #NOTA:Se actualizaran sus datos correspondientes en la BD. y al final su plan , existe un cron que recuperara los datos actuales del Instituto y generara un nuevo plan en Stripe.
    $Query = "INSERT INTO k_cron_plan_fame (fe_ejecucion,fl_instituto,id_cliente_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias,fe_creacion,fg_cambio_plan) ";
    $Query .= "VALUES('$fe_ejecucion',$p_instituto,'$id_custom_creado_instituto','$id_plan_creado_instituto','$id_suscripcion_creado_instituto','3','$ds_email_custom','$ds_descripcion', $mn_costo_x_licencia_bd,$total_licencias,CURRENT_TIMESTAMP,'$fg_cambio_plan') ";
    $fl_cron = EjecutaInsert($Query);
  }












  #se genera el cuerpo del documento de email$fl_usuario(reducir licencias)
  $ds_encabezado = genera_documento_sp($fl_usuario, 1, 111, '');
  $ds_cuerpo = genera_documento_sp($fl_usuario, 2, 111, '');
  $ds_pie = genera_documento_sp($fl_usuario, 3, 111, '');
  $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;

  $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row = RecuperaValor($Query);
  $ds_email = $row[0];

  $ds_titulo = ObtenEtiqueta(1643); #etiqueta de asunto del mensjae para el anunciante reduce my contrcat 
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4);
  $ds_email_destinatario = $ds_email;
  $nb_nombre_dos = ObtenEtiqueta(949); #nombre de quien envia el mensaje         

  $bcc = ObtenConfiguracion(107);
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
}



#funcion que permite cancelar 
function CancelarPlanActualInstituto($p_instituto, $fl_usuario = '')
{

  #eL iNSTITUTO QUEDA DESACTIVADO UNA VEZ QUE HAYA FINALIZADO SU PERIODO, HASTA ESA FECHA SE DESACTIVAN SUS CUENTAS.

  #Desactivaos al Instituto.
  $Query = "UPDATE c_instituto SET fg_activo='0'  WHERE fl_instituto=$p_instituto ";
  EjecutaQuery($Query);

  /*  #Desactivamos todas las cuentas de los usuarios  del Instituto.
        $Query="UPDATE c_usuario SET fg_activo='0' WHERE fl_instituto=$p_instituto ";
        EjecutaQuery($Query);
        */
  #Desactivams el plan que tiene el Instituto.
  $Query = "UPDATE k_current_plan fg_estatus='C' WHERE fl_instituto=$p_instituto ";
  EjecutaQuery($Query);



  #Desactivamos todas las cuentas de los usuarios  del Instituto.
  $Query = "UPDATE c_usuario SET fg_activo='0' WHERE fl_instituto=$p_instituto AND fl_perfil_sp <> " . PFL_ADMINISTRADOR . " ";
  EjecutaQuery($Query);


  $fe_terminacion_plan = ObtenFechaFinalizacionContratoPlan($p_instituto);
  #Se calcula la fecha posterios a terminacion del plan para ejecucion de cron
  $fe_final_periodo = strtotime('+0 day', strtotime($fe_terminacion_plan));
  $fe_ejecucion = date('Y-m-d', $fe_final_periodo);

  #elimnamos los crones existentes y creamos uno donde se cancelara la cuenta del instituto.
  EjecutaQuery("DELETE FROM k_cron_plan_fame WHERE fl_instituto=$p_instituto");

  $Query = "INSERT INTO k_cron_plan_fame (fe_ejecucion,fl_instituto,id_cliente_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias,fe_creacion) ";
  $Query .= "VALUES('$fe_ejecucion',$p_instituto,'','','','4','','CANCELACION', 0,0,CURRENT_TIMESTAMP) ";
  $fl_cron = EjecutaInsert($Query);

  #se genera el cuerpo del documento de email$fl_usuario(reducir licencias)
  $ds_encabezado = genera_documento_sp($fl_usuario, 1, 112, '');
  $ds_cuerpo = genera_documento_sp($fl_usuario, 2, 112, '');
  $ds_pie = genera_documento_sp($fl_usuario, 3, 112, '');
  $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;

  $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row = RecuperaValor($Query);
  $ds_email = $row[0];

  $ds_titulo = ObtenEtiqueta(1644); #etiqueta de asunto del mensjae para el anunciante reduce my contrcat 
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4);
  $ds_email_destinatario = $ds_email;
  $nb_nombre_dos = ObtenEtiqueta(949); #nombre de quien envia el mensaje         
  $bcc = ObtenConfiguracion(107);
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
}

function ObtenFechaFinalizacionRenovacionContratoPlan($fe_inicio_periodo, $p_plan_actual)
{


  if ($p_plan_actual == "M") {
    $fe_final_periodo = strtotime('+1 month', strtotime($fe_inicio_periodo));
    $fe_final_periodo = date('Y-m-d', $fe_final_periodo);
  }
  if ($p_plan_actual == "A") {
    $fe_final_periodo = strtotime('+1 year', strtotime($fe_inicio_periodo));
    $fe_final_periodo = date('Y-m-d', $fe_final_periodo);
  }

  return $fe_final_periodo;
}



/**
 * MJD #funcion que envia mensaje de bienvenida al Institutcion que se registra.
 * @param 
 * 
 */

function EnviaMaildeBienvendida($p_usuario, $p_instituto)
{



  #se genera el cuerpo del documento de email
  $ds_encabezado = GeneraDocumentoEmailModoTrial(104, 1, $p_usuario, $p_instituto, '');
  $ds_cuerpo = GeneraDocumentoEmailModoTrial(104, 2, $p_usuario, $p_instituto, '');
  $ds_pie = GeneraDocumentoEmailModoTrial(104, 3, $p_usuario, $p_instituto, '');



  $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;


  #Recuperamos el email destinatario.
  $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$p_usuario";
  $row = RecuperaValor($Query);
  $ds_email_destinatario = $row[0];

  # Inicializa variables de ambiente para envio de correo
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $bcc = ObtenConfiguracion(107);
  $nb_quien_envia_email = ObtenEtiqueta(949); #Vamcouver School nombre de quien envia el mensaje
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4);
  $ds_titulo = ObtenEtiqueta(1616); #etiqueta de asunto del mensjae para el envio
  $mail = EnviaMailHTML($nb_quien_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
}



/**
 * MJD #funcion para calcular el tiempo que lleva de uso en periodo Trial.(se utiliza para envio correo mientas lleva su periodo trial.)
 * @param 
 * 
 */
function CalculaTiempoUsoModoTrial($p_usuario)
{

  #Recuperamos al usuario e identificamos al Instituto.
  $fl_instituto = ObtenInstituto($p_usuario);


  $fe_actual = ObtenerFechaActual();

  //$fe_actual='2017-01-29';
  #Recupermos fecha de terminacion del plan del Instituto.
  $Query = "SELECT fe_creacion, fe_trial_expiracion FROM c_instituto WHERE fl_instituto =$fl_instituto ";
  $row = RecuperaValor($Query);
  $fe_creacion = $row[0];
  $fe_final_vigencia = $row[1];


  #Obtengo el no. de dias que comprende su periodo Trial.
  $no_dias_permitidos_modo_trial = ObtenDiasRestantesPlan($fe_final_vigencia, $fe_creacion);

  #Obtengo dias que faltan para culminar mi plan actual.
  $no_dias_faltan_terminar_plan = ObtenDiasRestantesPlan($fe_final_vigencia, $fe_actual);


  #realizamos opercaion para saber cuantos dias llevo en modo_trial.
  $no_dias_llevo_en_modo_trial = $no_dias_permitidos_modo_trial - $no_dias_faltan_terminar_plan;


  #se calcula  porcentaje que corresponde a los dias utilizados.
  $no_porcentaje = $no_dias_llevo_en_modo_trial * 100;
  $no_porcentaje = $no_porcentaje / $no_dias_permitidos_modo_trial;



  return $no_porcentaje;
}





/**
 * MJD #Funcion Envia un email para brindar ayuda ala institutcion. cuando este en un 50% de su periodo de vigencia
 * @param 
 * 
 */
function EnviaEmailAyuda($p_usuario, $p_instituto)
{


  #se genera el cuerpo del documento de email
  $ds_encabezado = GeneraDocumentoEmailModoTrial(105, 1, $p_usuario, $p_instituto, '');
  $ds_cuerpo = GeneraDocumentoEmailModoTrial(105, 2, $p_usuario, $p_instituto, '');
  $ds_pie = GeneraDocumentoEmailModoTrial(105, 3, $p_usuario, $p_instituto, '');


  $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;


  #Recuperamos el email destinatario.
  $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$p_usuario";
  $row = RecuperaValor($Query);
  $ds_email_destinatario = $row[0];



  # Inicializa variables de ambiente para envio de correo
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $bcc = ObtenConfiguracion(107);
  $nb_quien_envia_email = ObtenEtiqueta(949); #Vamcouver School nombre de quien envia el mensaje
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4); #mariopochat
  $ds_titulo = ObtenEtiqueta(1617); #etiqueta de asunto del mensjae para el envio
  $mail = EnviaMailHTML($nb_quien_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);


  if ($mail) {
    $fg_motivo = "2";
    $ds_motivo = "Email support for mode Trial 50% usage.";

    #Generamos la bitacora de envio de email de ayuda.
    $Query = "INSERT INTO k_envio_email_ayuda (fl_instituto,fe_creacion,ds_motivo,fg_motivo)";
    $Query .= "VALUES ($p_instituto,CURRENT_TIMESTAMP,'$ds_motivo','$fg_motivo')";
    EjecutaQuery($Query);
  }
}
/**
 * MJD #Funcion que envia email para recordar que el periodo d Trial esta por vence, cuando falte 5% de terminacion de periodo o 95% de uso.
 * @param 
 * 
 */
function EnviaEmailCaducidadTrial($p_usuario, $p_instituto, $fe_expiracion_plan)
{

  #se genera el cuerpo del documento de email
  $ds_encabezado = GeneraDocumentoEmailModoTrial(106, 1, $p_usuario, $p_instituto, $fe_expiracion_plan);
  $ds_cuerpo = GeneraDocumentoEmailModoTrial(106, 2, $p_usuario, $p_instituto, $fe_expiracion_plan);
  $ds_pie = GeneraDocumentoEmailModoTrial(106, 3, $p_usuario, $p_instituto, $fe_expiracion_plan);

  $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;


  #Recuperamos el email destinatario.
  $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$p_usuario";
  $row = RecuperaValor($Query);
  $ds_email_destinatario = $row[0];



  # Inicializa variables de ambiente para envio de correo
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $bcc = ObtenConfiguracion(107);
  $nb_quien_envia_email = ObtenEtiqueta(949); #Vamcouver School nombre de quien envia el mensaje
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4); #mariopochat
  $ds_titulo = ObtenEtiqueta(1618); #etiqueta de asunto del mensjae para el envio
  $mail = EnviaMailHTML($nb_quien_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);


  if ($mail) {
    $fg_motivo = "3";
    $ds_motivo = "Email's reminder by the expiration of the plan.";

    #Generamos la bitacora de envio de email de ayuda.
    $Query = "INSERT INTO k_envio_email_fame (fl_instituto,fe_creacion,ds_motivo,fg_motivo)";
    $Query .= "VALUES ($p_instituto,CURRENT_TIMESTAMP,'$ds_motivo','$fg_motivo')";
    EjecutaQuery($Query);
  }
}


/**
 * MJD #funcion que envia mail una vez finalizado su periodo Trial para indicar la opciones de compra de un plan.
 * @param 
 * 
 */
function EnviaEmailComprarPlan($p_usuario, $p_instituto, $ds_clave_confirmacion)
{

  #se genera el cuerpo del documento de email
  $ds_encabezado = GeneraDocumentoEmailModoTrial(107, 1, $p_usuario, $p_instituto, '', $ds_clave_confirmacion);
  $ds_cuerpo = GeneraDocumentoEmailModoTrial(107, 2, $p_usuario, $p_instituto, '', $ds_clave_confirmacion);
  $ds_pie = GeneraDocumentoEmailModoTrial(107, 3, $p_usuario, $p_instituto, '', $ds_clave_confirmacion);

  $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;


  #Recuperamos el email destinatario.
  $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$p_usuario";
  $row = RecuperaValor($Query);
  $ds_email_destinatario = $row[0];



  # Inicializa variables de ambiente para envio de correo
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));

  $nb_quien_envia_email = ObtenEtiqueta(949); #Vamcouver School nombre de quien envia el mensaje
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4); #mariopochat
  $ds_titulo = ObtenEtiqueta(1619); #etiqueta de asunto del mensjae para el envio
  $mail = EnviaMailHTML($nb_quien_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);


  if ($mail) {
    $fg_motivo = "4";
    $ds_motivo = "Email to buy a plan.";

    #Generamos la bitacora de envio de email de ayuda.
    $Query = "INSERT INTO k_envio_email_fame (fl_instituto,fl_usuario,fe_creacion,ds_motivo,ds_clave_acceso,fg_motivo)";
    $Query .= "VALUES ($p_instituto,$p_usuario,CURRENT_TIMESTAMP,'$ds_motivo','$ds_clave_confirmacion','$fg_motivo')";
    EjecutaQuery($Query);
  }
}



/**
 * MJD #funcion que genera email para cron , enviar alos Institutos en modo Trial.
 * @param 
 * 
 */
function GeneraDocumentoEmailModoTrial($p_template, $opc, $p_usuario, $p_instituto, $fe_expiracion_plan = '', $clave_acceso = '')
{


  $nb_usuario = str_texto(ObtenNombreUsuario($p_usuario));
  $nb_instituto = str_texto(ObtenNameInstituto($p_instituto));


  # Recupera datos del template del documento
  switch ($opc) {
    case 1:
      $campo = "ds_encabezado";
      break;
    case 2:
      $campo = "ds_cuerpo";
      break;
    case 3:
      $campo = "ds_pie";
      break;
    case 4:
      $campo = "nb_template";
      break;
  }
  $Query  = "SELECT $campo FROM k_template_doc WHERE fl_template=$p_template ";
  $row = RecuperaValor($Query);
  $cadena = str_uso_normal($row[0]);


  #damos formato de fecha.
  if ($fe_expiracion_plan) {

    #DAMOS FORMATO DIA,MES, AÑO.
    $date = date_create($fe_expiracion_plan);
    $fe_expiracion_plan = date_format($date, 'F j , Y');
  }


  $dominio_campus = ObtenConfiguracion(116);
  // $dominio_campus = "localhost:64573/vanas";#pruebas
  $src_redireccion = $dominio_campus . "/fame/index.php?c=" . $clave_acceso; #bueno
  $link_login_fame = $dominio_campus; #bueno



  # Sustituye variables con datos del alumno/Instituto

  $cadena = str_replace("#fame_admin_user#", $nb_usuario, $cadena); # , se usa para template de mitad de uso de modo trial. fl_template 105
  $cadena = str_replace("#fame_fe_expiration_plan#", $fe_expiracion_plan, $cadena); # fecha de expiracion del plan (cuando es prosimo a vencer).
  $cadena = str_replace("#fame_link_buy_plan#", $src_redireccion, $cadena); #link para comprar un plan.
  $cadena = str_replace("#fame_link_login_fame#", $link_login_fame, $cadena); #es link para acceso a fame. 
  $cadena = str_replace("&nbsp;", " ", $cadena);
  
  return $cadena;
}

function ObtenerFechaActual()
{

  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual = strtotime('+0 day', strtotime($fe_actual));
  $fe_actual = date('Y-m-d', $fe_actual);

  return $fe_actual;
}

# Recupera la fecha actual de la base de datos
function ObtenFechaActualFAME($p_display = False)
{

  # Revisa si se debe usar una fecha para debug o la fecha actual
  $fg_degub = ObtenConfiguracion(21);
  $diferencia = RecuperaDiferenciaGMT();
  if ($fg_degub <> "1") {
    if ($p_display) {
      $Query  = "SELECT DATE_FORMAT((DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
      $Query .= "DATE_FORMAT((DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $diferencia HOUR)), '%e, %Y') 'fe_dia_anio'";
      $row = RecuperaValor($Query);
      $fe_actual = ObtenNombreMes($row[0]) . " " . $row[1];
    } else {
      $row = RecuperaValor("SELECT CURRENT_TIMESTAMP");
      $fe_actual = $row[0];
    }
  } else {
    $fe_actual = ObtenConfiguracion(22);
    if ($p_display) {
      $Query  = "SELECT DATE_FORMAT((DATE_ADD('$fe_actual', INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
      $Query .= "DATE_FORMAT((DATE_ADD('$fe_actual', INTERVAL $diferencia HOUR)), '%e, %Y') 'fe_dia_anio'";
      $row = RecuperaValor($Query);
      $fe_actual = ObtenNombreMes($row[0]) . " " . $row[1];
    }
  }
  return $fe_actual;
}


function ObtenProgresoCourse($p_programa, $p_alumno)
{

  $row = RecuperaValor("SELECT ds_progreso FROM k_usuario_programa WHERE fl_usuario_sp = $p_alumno  AND fl_programa_sp = $p_programa");

  return $row[0];
}

function ObtenPromedioPrograma($p_programa, $p_alumno){

  # Buscamos folio del programa y alumno
  $row0 = RecuperaValor("SELECT fl_usu_pro FROM k_usuario_programa WHERE fl_usuario_sp=$p_alumno and fl_programa_sp=$p_programa");
  $fl_usu_pro = $row0[0]??NULL;
  # Buscamos si el teacher lo califica
  $row00 = RecuperaValor("SELECT fg_quizes, fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=$fl_usu_pro");
  $fg_quizes = $row00[0]??NULL;
  $fg_grade_tea = $row00[1]??NULL;

  # Obtenemos la calificacion de los quiz
  # Lecciones del programa
  $query = "SELECT  a.fl_leccion_sp FROM c_leccion_sp a WHERE a.fl_programa_sp=$p_programa";
  $rs = EjecutaQuery($query);
  $cal_quiz = 0;
  $tot_quiz = 0;
  $tot_pro = 0;
  for ($i = 1; $rowq = RecuperaRegistro($rs); $i++) {
    $fl_leccion_sp = $rowq[0]??NULL;
    # Verificamos si la leccion tiene quiz
    $lec_quiz = ExisteEnTabla('k_quiz_pregunta', 'fl_leccion_sp', $fl_leccion_sp);
    if ($lec_quiz) {
      $tot_intentos = 0;
      # Si existe ya realizo quiz debe tener intentos busca el ultimo
      if (ExisteEnTabla('k_quiz_calif_final', 'fl_leccion_sp', $fl_leccion_sp, 'fl_usuario', $fl_usuario, true)) {
        $row3 = RecuperaValor("SELECT MAX(no_intento) FROM k_quiz_calif_final WHERE fl_usuario=$p_alumno AND fl_leccion_sp=$fl_leccion_sp");
        $tot_intentos = $row3[0]??NULL;
        # Buscamos el ultimo intento del quiz si esta aprobado activa el boton completed
        $rowq2 = RecuperaValor("SELECT no_calificacion FROM k_quiz_calif_final WHERE fl_usuario=$p_alumno AND fl_leccion_sp=$fl_leccion_sp AND no_intento=$tot_intentos");
        $no_calificacion = $rowq2[0]??NULL;
        # Sumamos las calificaciones
        $cal_quiz = $cal_quiz + $no_calificacion;

        if ($tot_intentos)
          $tot_quiz++;
      }
    }
  }
  if (!empty($cal_quiz)) {
    # Obtenemos la calificacion de los quizes
    # Sumando todas las quizes que ha realizado el alumno entre el total de quizez que tiene el programa
    $tot_cal_quiz =  $cal_quiz / $tot_quiz;
    $tot_pro = $tot_cal_quiz;
  }

  # SI el maestro lo califica obtiene datos
  if (!empty($fg_grade_tea)) {
    # Obtenemos el total de las lecciones que tiene el curso
    // $tot_weeks = ObtenSemanaMaximaAlumno($p_programa);
    // $grade_cal = $tot_weeks * 100;
    # Sumamos las calificaciones y lo dividimos entre el numero de las semanas    
    $row0 = RecuperaValor("SELECT SUM( no_calificacion), COUNT(*) FROM k_calificacion_teacher WHERE fl_programa_sp=$p_programa AND fl_alumno=$p_alumno");
    $sum_weeks = $row0[0] / $row0[1];
    $tot_tea = $sum_weeks;
    $tot_pro = ($tot_cal_quiz + $tot_tea) / 2;
  }
  # Actualizamos el promedio del alumno y programa
  EjecutaQuery("UPDATE k_usuario_programa SET no_promedio_t=$tot_pro WHERE fl_usu_pro=$fl_usu_pro");
  return round($tot_pro);
}

function ObtenCalificacion($p_promedio)
{
  $Query = "SELECT cl_calificacion FROM c_calificacion_sp WHERE no_min <= ROUND($p_promedio) AND no_max >= ROUND($p_promedio)";
  $row = RecuperaValor($Query);
  return $row[0];
}

function ObtenCalificacionAprobada($p_promedio)
{
  $Query = "SELECT fg_aprobado FROM c_calificacion_sp WHERE no_min <= ROUND($p_promedio) AND no_max >= ROUND($p_promedio)";
  $row = RecuperaValor($Query);
  return $row[0];
}


/**
 * MJD #funcion generar un agrupamiento de select (grados que tiene una categoriia y sus rspectivas valores) .
 * @param 
 * 
 */
function CampoSelectBDGRupoFAME($p_nombre, $p_query, $p_actual, $p_clase = 'css_input', $p_seleccionar = False, $p_script = '', $p_etiqueta = '', $p_requerido = false, $p_query2)
{
  if ($p_etiqueta) {
    echo "<label class='input'>";

    if ($p_requerido)
      echo "* ";

    echo " $p_etiqueta :</label><br/>";
  }
  echo "<select id='$p_nombre' name='$p_nombre' class='select2'";

  if (!empty($p_script)) echo " $p_script";
  echo ">\n";

  if ($p_seleccionar) {

    echo "<optgroup label=''>";
    echo "<option value=0>" . ObtenEtiqueta(70) . "</option>\n";
    echo "</optgroup>";
  }



  $rs = EjecutaQuery($p_query); #ejecua el primer query para saber la clasificacion padre.
  $contador=0;
  while ($row = RecuperaRegistro($rs)) {

    $nombre = str_texto($row[1]);
    echo "<optgroup label='$nombre'>";

    $contador++;
    $p_query_tem = $p_query2;



    #reemplazamos el identificador por primer resultado, del primer query,(es como buscar su papa jajaja)
    $p_query_tem = str_replace("#id_valor#", $row[0], $p_query_tem);

    /* $p_query2="SELECT fl_grado,nb_grado,cl_clasificacion_grado
                FROM k_grado_fame WHERE cl_clasificacion_grado=$row[0]
                ORDER BY  cl_clasificacion_grado asc  ";
        */

    $rs2 = EjecutaQuery($p_query_tem);
    while ($row2 = RecuperaRegistro($rs2)) {

      $nb_nombre2 = $row2[1];
      $contador++;

      echo "<option value=\"$row2[0]\"";
      if ($p_actual == $row2[0])
        echo " selected";
      $etq_campo = DecodificaEscogeIdiomaBD($row2[1]);
      echo ">$etq_campo</option>\n";
    }

    echo "</optgroup>";
  }
  echo "</select>";
}



/**
 * MJD #funcion que calcula los dias restantes que hacen falta para llegar a una fecha, y se le manda como parametro la fecha futura.
 * @param 
 * 
 */
function CalculaDiasRestantesFechaDeterminada($p_fecha)
{

  $fecha_actual = date('Y-m-d');

  $s = strtotime($p_fecha) - strtotime($fecha_actual);
  $d = intval($s / 86400);
  $diferencia = $d;

  return $diferencia;
}


function EnviarEmailNotificacionExpiracionPlan($p_instituto, $p_usuario)
{



  #Se recupera el contenido del template/correo.

  $ds_encabezado = genera_documento_sp($p_usuario, 1, 113, '');
  $ds_cuerpo = genera_documento_sp($p_usuario, 2, 113, '');
  $ds_pie = genera_documento_sp($p_usuario, 3, 113, '');
  $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;

  $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$p_usuario ";
  $row = RecuperaValor($Query);
  $ds_email = $row[0];

  $ds_titulo = ObtenEtiqueta(1644); #etiqueta de asunto del mensjae FAME Alert Expiracion de plan 
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4);
  $ds_email_destinatario = $ds_email;
  $nb_nombre_dos = ObtenEtiqueta(1646); #nombre de quien envia el mensaje         
  $bcc = ObtenConfiguracion(107); #envio de copia
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
}
# Query for list of teachers used in community_div.php
function AdminQuery($p_usuario)
{
  # Get intitute user
  $fl_instituto = ObtenInstituto($p_usuario);
  # Get privacity educational
  $privacity_educational = GetPrivacityEducationalInst($fl_instituto, $p_usuario);
  # Get privacity international
  $privacity_international = GetPrivacityInternationalInst($fl_instituto, $p_usuario);
  # Query
  $Query  = "SELECT fl_usuario, ds_ruta_avatar, ds_nombres, ds_empresa, ds_pais, no_accesos, fg_activo, fg_genero, fl_pais, fl_instituto,ds_instituto FROM( ";
  $Query .= "(SELECT a.fl_adm_sp fl_usuario, a.ds_ruta_avatar, ";
  $Query .= "CONCAT(b.ds_nombres,' ', b.ds_apaterno) ds_nombres, '' ds_empresa, ds_pais, b.no_accesos, b.fg_activo, b.fg_genero, ";
  $Query .= "d.fl_pais, b.fl_instituto,f.fg_educational,c.ds_instituto ";
  $Query .= "FROM c_usuario b ";
  $Query .= "JOIN c_administrador_sp a ON(b.fl_usuario=a.fl_adm_sp) ";
  $Query .= "JOIN c_instituto c ON(c.fl_instituto=b.fl_instituto) ";
  $Query .= "LEFT JOIN k_usu_direccion_sp d ON(d.fl_usuario_sp=b.fl_usuario) ";
  $Query .= "LEFT JOIN c_pais e ON(e.fl_pais=d.fl_pais) ";
  $Query .= " JOIN k_instituto_filtro f ON ( f.fl_instituto=b.fl_instituto )";
  $Query .= "WHERE b.fg_activo='1' AND c.fg_activo='1' AND b.fl_usuario<> $p_usuario ";
  $Query .= "ORDER BY b.no_accesos DESC) ";
  $Query .= ") as Main WHERE 1=1 ";
  # Privacity educational
  if (!empty($privacity_educational)) {
    $Query .= $privacity_educational;
  }
  # Privacity International
  if (!empty($privacity_international)) {
    $Query .= $privacity_international;
  }
  $Query .= "ORDER BY no_accesos DESC ";
  $rs = EjecutaQuery($Query);

  return $rs;
}

# Teachers de VANAS
function TeacherQueryVanas()
{
  # Query
  $Query = "SELECT a.fl_maestro fl_usuario, a.ds_ruta_avatar, ";
  $Query .= "b.ds_nombres ds_nombres, a.ds_empresa, ds_pais, b.no_accesos, b.fg_activo, b.fg_genero, ";
  $Query .= "c.fl_pais, '1' fl_instituto, '' fl_grado, '' cl_clasificacion_grado, TIMESTAMPDIFF(YEAR,b.fe_nacimiento, CURDATE()) edad,'0' fame, ";
  $Query .= "ds_apaterno,''fg_educational ";
  $Query .= "FROM c_maestro a, c_usuario b, c_pais c ";
  $Query .= "WHERE a.fl_maestro=b.fl_usuario ";
  $Query .= "AND a.fl_pais=c.fl_pais ";
  return $Query .= "AND b.fg_activo='1' ";
}

# Query for list of teachers used in community_div.php
function TeacherQuery($letter = "", $country = "", $p_usuario)
{
  # Get intitute user
  $fl_instituto = ObtenInstituto($p_usuario);
  # Get privacity educational
  $privacity_educational = GetPrivacityEducationalInst($fl_instituto, $p_usuario);
  # Get privacity international
  $privacity_international = GetPrivacityInternationalInst($fl_instituto, $p_usuario);
  # Query
  $Query  = "SELECT fl_usuario, ds_ruta_avatar, ds_nombres, ds_empresa, ds_pais, no_accesos, fg_activo, fg_genero, fl_pais, fl_instituto, edad, fame FROM( ";
  // $Query .= "(".TeacherQueryVanas().")";
  $Query .= "SELECT a.fl_maestro_sp fl_usuario, a.ds_ruta_avatar, ";
  $Query .= "CONCAT(b.ds_nombres,' ', b.ds_apaterno) ds_nombres, a.ds_empresa, ds_pais, b.no_accesos, b.fg_activo, b.fg_genero, ";
  $Query .= "c.fl_pais, b.fl_instituto,'' fl_grado, '' cl_clasificacion_grado, TIMESTAMPDIFF(YEAR,b.fe_nacimiento, CURDATE()) edad,'1' fame, b.ds_apaterno,f.fg_educational ";
  $Query .= "FROM c_usuario b ";
  $Query .= "JOIN c_maestro_sp a ON(b.fl_usuario=a.fl_maestro_sp) ";
  $Query .= "JOIN c_instituto e ON(e.fl_instituto=b.fl_instituto) ";
  $Query .= "LEFT JOIN k_usu_direccion_sp c ON(c.fl_usuario_sp=b.fl_usuario) ";
  $Query .= "LEFT JOIN c_pais d ON(d.fl_pais=c.fl_pais) ";
  $Query .= " JOIN k_instituto_filtro f ON ( f.fl_instituto=b.fl_instituto ) ";
  $Query .= "WHERE b.fg_activo='1' AND e.fg_activo='1' AND a.fl_maestro_sp<>$p_usuario ";
  $Query .= "ORDER BY b.no_accesos DESC ";
  # Teachers de VANAS
  // $Query .= " UNION ";
  // $Query .= "(".TeacherQueryVanas().") ";
  $Query .= ") as Main WHERE 1=1 ";
  # Privacity educational
  if (!empty($privacity_educational)) {
    $Query .= $privacity_educational;
  }
  # Privacity International
  if (!empty($privacity_international)) {
    $Query .= $privacity_international;
  }
  $Query .= "ORDER BY no_accesos DESC ";
  $rs = EjecutaQuery($Query);

  return $rs;
}

# Stundents Vanas
function StudentQueryVanas($p_usuario, $p_perfil)
{
  $fl_perfil = $p_perfil;
  # Obtenemos el grado la edad 
  if ($fl_perfil == PFL_ESTUDIANTE_SELF) {
    # Get grade user
    $fl_grade = GetGradeUser($p_usuario);
    # Get ages grades
    $gradefame = GetGradeAge($fl_grade);
    $calificacion = $gradefame['cl_clasificacion_grado'];
    $min = $gradefame['no_edad_min'];
    $max = $gradefame['no_edad_max'];
  } else {
    $fl_grade = '0';
    $calificacion = '0';
  }

  # Query
  $Query = "SELECT DISTINCT a.fl_alumno fl_usuario, a.ds_ruta_avatar, b.ds_nombres ds_nombres, '' ds_empresa, ";
  $Query .= "ds_pais, b.no_accesos, b.fg_activo, b.fg_genero, ";
  $Query .= "d.fl_pais, '1' fl_instituto, '" . $fl_grade . "' fl_grado, '" . $calificacion . "' cl_clasificacion_grado,  TIMESTAMPDIFF(YEAR,b.fe_nacimiento, CURDATE()) edad, '0' fame, b.ds_apaterno,''fg_educational  ";
  $Query .= "FROM c_alumno a, c_usuario b, k_ses_app_frm_1 c, c_pais d, c_programa e, k_alumno_grupo f ";
  $Query .= "WHERE a.fl_alumno=b.fl_usuario ";
  $Query .= "AND b.cl_sesion=c.cl_sesion ";
  $Query .= "AND c.ds_add_country=d.fl_pais ";
  $Query .= "AND c.fl_programa=e.fl_programa ";
  $Query .= "AND a.fl_alumno=f.fl_alumno ";
  $Query .= "AND b.fg_activo='1' ";
  # Students activos Vanas
  # Students depende de la edad
  # Teachers y admin muestra todos
  if ($fl_perfil == PFL_ESTUDIANTE_SELF) {
    $Query .= "AND TIMESTAMPDIFF(YEAR,b.fe_nacimiento, CURDATE()) BETWEEN " . $min . " AND " . $max . " AND fe_nacimiento IS NOT null ";
  }
  return $Query .= "ORDER BY b.no_accesos DESC ";
}

# Query for list of students used in community_div.php
function StudentQuery($letter = "", $country = "", $program = "", $p_usuario)
{
  # Get perfil
  $fl_perfil = ObtenPerfilUsuario($p_usuario);
  # Get intitute user
  $fl_instituto = ObtenInstituto($p_usuario);
  # Get privacity gender
  $privacity_gender = GetPrivacityGenderInst($fl_instituto, $p_usuario);
  # Get privacity grade
  $privacity_grade = GetPrivacityGradesInst($fl_instituto, $p_usuario);
  # Get privacity educational
  $privacity_educational = GetPrivacityEducationalInst($fl_instituto, $p_usuario);
  # Get privacity international
  $privacity_international = GetPrivacityInternationalInst($fl_instituto, $p_usuario);
  # Obtenemos el grado la edad 
  if ($fl_perfil == PFL_ESTUDIANTE_SELF) {
    # Get grade user
    $fl_grade = GetGradeUser($p_usuario);
    # Get ages grades
    $gradefame = GetGradeAge($fl_grade);
    $calificacion = $gradefame['cl_clasificacion_grado'];
    $min = $gradefame['no_edad_min'];
    $max = $gradefame['no_edad_max'];
  } else {
    $fl_grade = '0';
    $calificacion = '0';
  }

  #Query
  $Query  = "SELECT fl_usuario, ds_ruta_avatar, ds_nombres, ds_pais, no_accesos, fg_activo, fg_genero, ";
  $Query .= "fl_pais, fl_instituto, fl_grado, cl_clasificacion_grado, edad, fame, ds_apaterno,ds_instituto FROM ( ";
  if (empty($program)) {
    $Query .= "SELECT a.fl_alumno_sp fl_usuario, a.ds_ruta_avatar, b.ds_nombres ds_nombres, '' ds_empresa,  ";
    $Query .= "e.ds_pais, b.no_accesos, b.fg_activo, b.fg_genero, d.fl_pais,  b.fl_instituto, a.fl_grado, f.cl_clasificacion_grado, ";
    $Query .= "TIMESTAMPDIFF(YEAR,b.fe_nacimiento, CURDATE()) edad, '1' fame, b.ds_apaterno,fg_educational,c.ds_instituto ";
    $Query .= "FROM c_usuario b ";
    $Query .= "JOIN c_alumno_sp a ON(a.fl_alumno_sp=b.fl_usuario) ";
    $Query .= "JOIN c_instituto c ON(b.fl_instituto=c.fl_instituto) ";
    $Query .= "LEFT JOIN k_usu_direccion_sp d ON(d.fl_usuario_sp=b.fl_usuario) ";
    $Query .= "LEFT JOIN c_pais e ON(d.fl_pais=e.fl_pais) ";
    $Query .= "LEFT JOIN k_grado_fame f ON(a.fl_grado=f.fl_grado) ";
    $Query .= "JOIN k_instituto_filtro l ON (l.fl_instituto=b.fl_instituto) ";
    $Query .= "WHERE b.fg_activo='1' AND c.fg_activo='1' AND b.fl_usuario<>$p_usuario  ";
  } else {
    $Query .= "SELECT a.fl_alumno_sp fl_usuario, a.ds_ruta_avatar, b.ds_nombres ds_nombres, '' ds_empresa,   ";
    $Query .= "e.ds_pais, b.no_accesos, b.fg_activo, b.fg_genero, d.fl_pais, b.fl_instituto, a.fl_grado, f.cl_clasificacion_grado, ";
    $Query .= "TIMESTAMPDIFF(YEAR,b.fe_nacimiento, CURDATE()) edad, '1' fame, b.ds_apaterno,fg_educational,c.ds_instituto ";
    $Query .= "FROM c_usuario b ";
    $Query .= "JOIN c_alumno_sp a ON(a.fl_alumno_sp=b.fl_usuario) ";
    $Query .= "JOIN c_instituto c ON(b.fl_instituto=c.fl_instituto)  ";
    $Query .= "LEFT JOIN k_usu_direccion_sp d ON(d.fl_usuario_sp=b.fl_usuario) ";
    $Query .= "LEFT JOIN c_pais e ON(d.fl_pais=e.fl_pais) ";
    $Query .= "LEFT JOIN k_grado_fame f ON(a.fl_grado=f.fl_grado) ";
    $Query .= "LEFT JOIN k_usuario_programa g ON(g.fl_usuario_sp=a.fl_alumno_sp) ";
    $Query .= "JOIN k_instituto_filtro l ON (l.fl_instituto=b.fl_instituto) ";
    $Query .= "WHERE b.fg_activo='1' AND c.fg_activo='1' AND b.fl_usuario<>$p_usuario AND g.fl_programa_sp=$program ";
  }
  # Students de VANAS
  //$Query .= " UNION ";
  //$Query .= " (".StudentQueryVanas($p_usuario, $fl_perfil).") ";
  $Query .= ") as Main WHERE 1=1 ";
  # Privacity gender
  if (!empty($privacity_gender)) {
    $Query .= "AND fg_genero='" . $privacity_gender . "' ";
  }
  # Privacity gender
  if (!empty($privacity_grade) and $fl_perfil == PFL_ESTUDIANTE_SELF) {
    $Query .= $privacity_grade;
  }
  # Privacity educational
  if (!empty($privacity_educational)) {
    $Query .= $privacity_educational;
  }
  # Privacity International
  if (!empty($privacity_international)) {
    $Query .= $privacity_international;
  }
  $Query .= "ORDER BY no_accesos DESC ";
  $rs = EjecutaQuery($Query);

  return $rs;
}

# Get the list of chat users for messages.php and notify/messages.php 
function GetChatUsers($fl_usuario, $p_programa = '')
{
  # Recupera usuarios que han enviado o se les ha enviado mensajes
  $diferencia = RecuperaDiferenciaGMT();

  if (!empty($p_programa)) {


    $Queryy  = "SELECT usr_interaccion, DATE_FORMAT(MAX(fe_mensaje), '%M %e, %Y at %l:%i %p') 'fe_message', MAX(fe_mensaje) cuando ";
    $Queryy .= "FROM( SELECT CASE WHEN fl_usuario_ori<>$fl_usuario then fl_usuario_ori ELSE fl_usuario_dest END usr_interaccion, ";
    $Queryy .= "DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR) fe_mensaje ";
    $Queryy .= "FROM k_mensaje_directo ";
    $Queryy .= "WHERE fl_usuario_ori=$fl_usuario ";
    $Queryy .= "OR fl_usuario_dest=$fl_usuario) usuarios ";
    $Queryy .= "GROUP BY usr_interaccion ";
    $Queryy .= "ORDER BY cuando DESC";
  } else {


    $Queryy  = "SELECT fl_usuario_ori, DATE_FORMAT(MAX(fe_mensaje), '%M %e, %Y at %l:%i %p') 'fe_message', MAX(fe_mensaje) cuando, fl_mensaje_directo, fg_leido ";
    $Queryy .= "FROM(SELECT fl_usuario_ori, ";
    $Queryy .= "DATE_ADD( fe_mensaje, INTERVAL $diferencia HOUR) fe_mensaje,fl_mensaje_directo,fg_leido ";
    $Queryy .= "FROM k_mensaje_directo ";
    $Queryy .= "WHERE  ";
    $Queryy .= "fl_usuario_dest=$fl_usuario AND fg_leido='0' ) usuarios ";
    $Queryy .= " ";
    $Queryy .= "GROUP BY fl_usuario_ori ";
    $Queryy .= "ORDER BY cuando DESC";
  }
  $rs = EjecutaQuery($Queryy);

  $result = array();
  for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
    $usr_interaccion = $row[0];
    $fe_mensaje = $row[1];
    $no_mensaje = $row[2];
    $fl_mensaje_directo = !empty($row['fl_mensaje_directo'])?$row['fl_mensaje_directo']:NULL;
    $fg_leido = !empty($row['fg_leido'])?$row['fg_leido']:NULL;

    # OIdentificamos usuario si es de FAME o Vanas
    $fl_perfil_inter = ObtenPerfilUsuario($usr_interaccion);
    if (!empty($fl_perfil_inter))
      $ds_ruta_avatar = ObtenAvatarUsuario($usr_interaccion);
    else
      $ds_ruta_avatar = ObtenAvatarUsrVanas($usr_interaccion);
    $ds_nombre = ObtenNombreUsuario($usr_interaccion);



    if (!empty($p_programa)) {

      # Check if there's unread messages
      $Query = "SELECT COUNT(1) FROM k_mensaje_directo WHERE fl_usuario_ori=$usr_interaccion AND fl_usuario_dest=$fl_usuario AND fg_leido='0'";
      $row2 = RecuperaValor($Query);
      $no_unread = $row2[0];
      if ($no_unread > 0) {
        $ds_notificar = "(Unread Messages)";
      } else {
        $ds_notificar = "";
      }
    } else {


      # Check if there's unread messages
      // $Query = "SELECT COUNT(1) FROM k_mensaje_directo WHERE fl_usuario_ori=$usr_interaccion AND fl_usuario_dest=$fl_usuario AND fg_leido='0'";
      // $row2 = RecuperaValor($Query);
      //$no_unread = $row2[0];
      if ($fg_leido == 0) {
        $ds_notificar = "(Unread Messages)";
      } else {
        $ds_notificar = "";
      }
    }



    $result["user" . $i] = array(
      "fl_mensaje_directo" => $fl_mensaje_directo,
      "id" => $usr_interaccion,
      "fl_usuario_dest" => $fl_usuario,
      "time" => $fe_mensaje,
      "total" => $no_mensaje,
      "avatar" => $ds_ruta_avatar,
      "name" => $ds_nombre,
      "unread" => $ds_notificar
    );
  }
  $result["size"] = array("total" => $i);
  echo json_encode((object) $result);
}
# end messages
# Sent notificate message
function NotificateMessage($fl_mensaje_directo)
{

  $diferencia = RecuperaDiferenciaGMT();

  # Obtenemos el usuario que envia el mensaje
  $Query = "SELECT b.ds_nombres 'ds_nombre_ori', b.ds_apaterno 'ds_apaterno_ori', fl_usuario_ori, a.ds_mensaje, ";
  $Query .= "DATE_FORMAT((DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR)), '%W, %M %e, %Y '), ";
  $Query .= "c.ds_nombres 'ds_nombre_dest', c.ds_apaterno 'ds_apaterno_dest', c.ds_email ";
  $Query .= "FROM k_mensaje_directo a ";
  $Query .= "LEFT JOIN c_usuario b ON(b.fl_usuario=a.fl_usuario_ori) ";
  $Query .= "LEFT JOIN c_usuario c ON(c.fl_usuario=a.fl_usuario_dest) ";
  $Query .= "WHERE fl_mensaje_directo=$fl_mensaje_directo ";
  $row = RecuperaValor($Query);
  $ds_nombre_ori = str_texto($row[0]);
  $ds_apaterno_ori = str_texto($row[1]);
  $ds_ruta_avatar_ori = ObtenAvatarUsuario($row[2]);
  $ds_mensaje = str_texto($row[3]);
  $fe_mensaje = $row[4];
  $ds_nombre_dest = str_texto($row[5]);
  $ds_apaterno_dest = str_texto($row[6]);
  $ds_email = $row[7];

  # Obtenemosel template
  $Query  = "SELECT ds_encabezado, ds_cuerpo, ds_pie, nb_template FROM k_template_doc WHERE fl_template=115 ";
  $row = RecuperaValor($Query);
  $nb_template = $row[3];
  $cadena = str_uso_normal($row[0] . $row[1] . $row[2]);

  # Remplazamos las variables
  $cadena = str_replace("#fame_us_fname_ori#", $ds_nombre_ori, $cadena); # first name de quien envia el mensage
  $cadena = str_replace("#fame_us_lname_ori#", $ds_apaterno_ori, $cadena); # last name de quien envia el mensage
  $cadena = str_replace("#fame_ds_avatar_ori#", "<img src='" . $ds_ruta_avatar_ori . "'>", $cadena);  # Avatar  de quien envia el mensage
  $cadena = str_replace("#fame_ds_message#", $ds_mensaje, $cadena);  # Mensage enviado
  $cadena = str_replace("#fame_fe_mensaje#", $fe_mensaje, $cadena);  # Fecha de Mensage enviado
  $cadena = str_replace("#fame_fname#", $ds_nombre_dest, $cadena);  # Fisrt name del que recibe el mensage
  $cadena = str_replace("#fame_lname#", $ds_apaterno_dest, $cadena);  # Last name del que recibe el mensage


  # Envia el correo de contacto
  EnviaMailHTML($nb_template, ObtenConfiguracion(107), $ds_email, ObtenEtiqueta(1931), $cadena);
}

# Get privacity gender by institute
function GetPrivacityGenderInst($p_instituto, $p_usuario)
{
  # Obtenemos perfil usuario
  $fl_perfil = ObtenPerfilUsuario($p_usuario);
  # Datos
  $Query  = "SELECT fg_gender ";
  $Query .= "FROM k_instituto_filtro WHERE fl_instituto=$p_instituto ";
  $row = RecuperaValor($Query);
  $fg_gender = $row[0];
  $fg_genero_privacity = "";
  # Si el usuario es estudiante la privacidad se activa en caso contrario vera todos los generos
  if ($fg_gender == 2 and $fl_perfil == PFL_ESTUDIANTE_SELF) {
    # Get gender to user
    $row1 = RecuperaValor("SELECT fg_genero FROM c_usuario WHERE fl_usuario=$p_usuario");
    $fg_genero_privacity = $row1[0];
  }
  return $fg_genero_privacity;
}

# Get privacity grades and school levels  by intitute
function GetPrivacityGradesInst($p_instituto, $p_usuario)
{

  # Get grade by intitute
  $Query  = "SELECT fg_grade ";
  $Query .= "FROM k_instituto_filtro WHERE fl_instituto=$p_instituto ";
  $row = RecuperaValor($Query);
  $fg_grade = $row[0];

  # Get grade user depende perfil
  $fl_perfil = ObtenPerfilUsuario($p_usuario);
  # Solo esta privacidad sera para estudiantes
  # All levels
  $grado = "";
  if ($fl_perfil == PFL_ESTUDIANTE_SELF) {
    $rowAL = RecuperaValor("SELECT fl_grado FROM c_alumno_sp WHERE fl_alumno_sp=$p_usuario");
    $fl_grado = $rowAL[0];
    $above = $fl_grado + 1;
    $below = $fl_grado - 1;
    # Exact grade
    if ($fg_grade == 4)
      $grado = "AND fl_grado= " . $fl_grado . " ";
    # One grade above and below
    if ($fg_grade == 3)
      $grado = "AND fl_grado IN(" . $above . "," . $fl_grado . "," . $below . ") ";
    # My level
    if ($fg_grade == 2) {
      # Obtenemos la clasificacion del grado del usuario
      $row2 = RecuperaValor("SELECT cl_clasificacion_grado FROM k_grado_fame where fl_grado=$fl_grado");
      $grado  = "AND fl_grado IN(" . $above . "," . $fl_grado . "," . $below . ") ";
      $grado .= "AND cl_clasificacion_grado=" . $row2[0] . " ";
    }
  }

  return $grado;
}

# Get privacity Educational
function GetPrivacityEducationalInst($p_instituto, $p_usuario)
{
  # Get educational by intitute
  $Query  = "SELECT fg_educational ";
  $Query .= "FROM k_instituto_filtro WHERE fl_instituto=$p_instituto ";
  $row = RecuperaValor($Query);
  $fg_educational = $row[0];
  $educational = "";
  # search educational
  if ($fg_educational == 2) {
    $educational = "AND fl_instituto=" . $p_instituto . " ";
  } else {
    $educational .= "AND fg_educational<>'2'  "; //buscará a todos excepto los numero 2.  que estan limitados solo por su instituto. 


  }
  return $educational;
}
# Get privacity International
function GetPrivacityInternationalInst($p_instituto, $p_usuario)
{
  # Get educational by intitute
  $Query  = "SELECT fg_international ";
  $Query .= "FROM k_instituto_filtro WHERE fl_instituto=$p_instituto ";
  $row = RecuperaValor($Query);
  $fg_international = $row[0];
  $international = "";
  # search educational
  if ($fg_international == 2) {
    # Get country user
    $roww = RecuperaValor("SELECT fl_pais FROM k_usu_direccion_sp WHERE fl_usuario_sp=$p_usuario");
    $fl_pais = $roww[0];
    if ($fl_pais)
      $international = "AND fl_pais=" . $roww[0] . " ";
  }
  return $international;
}

# Get users active contacts.php
function GetUserOnline($p_usuario)
{
  # Get perfil
  $fl_perfil = ObtenPerfilUsuario($p_usuario);
  # Get institute
  $fl_instituto = ObtenInstituto($p_usuario);
  # Get Privacity gender user
  $privacity_gender = GetPrivacityGenderInst($fl_instituto, $p_usuario);
  # Get privacity grade
  $privacity_grade = GetPrivacityGradesInst($fl_instituto, $p_usuario);
  # Get privacity educational
  $privacity_educational = GetPrivacityEducationalInst($fl_instituto, $p_usuario);
  # Get privacity international
  $privacity_international = GetPrivacityInternationalInst($fl_instituto, $p_usuario);
  # Query to users
  $Query  = "SELECT fl_usuario, ds_ruta_avatar, ds_nombres, ds_empresa, ds_pais, no_accesos, fg_activo, fg_genero, ";
  $Query .= "fl_pais, fl_instituto, fl_grado, edad, fame, ds_apaterno FROM ( ";
  $Query .= "(SELECT a.fl_adm_sp fl_usuario, a.ds_ruta_avatar, ";
  $Query .= "b.ds_nombres ds_nombres, '' ds_empresa, ds_pais, b.no_accesos, b.fg_activo, b.fg_genero, ";
  $Query .= "d.fl_pais, b.fl_instituto, '' fl_grado, '' cl_clasificacion_grado, TIMESTAMPDIFF(YEAR,b.fe_nacimiento, CURDATE()) edad, '1' fame, ds_apaterno,fg_educational ";
  $Query .= "FROM c_administrador_sp a ";
  $Query .= "JOIN c_usuario b ON(b.fl_usuario=a.fl_adm_sp) ";
  $Query .= "JOIN c_instituto c ON(c.fl_instituto=b.fl_instituto) ";
  $Query .= "LEFT JOIN k_usu_direccion_sp d ON(d.fl_usuario_sp=b.fl_usuario) ";
  $Query .= "LEFT JOIN c_pais e ON(e.fl_pais=d.fl_pais) ";
  $Query .= "JOIN k_instituto_filtro l ON (l.fl_instituto=b.fl_instituto) ";
  $Query .= "WHERE b.fg_activo='1' AND c.fg_activo='1' AND b.no_accesos>0 ";
  $Query .= "ORDER BY b.no_accesos DESC) ";
  $Query .= "UNION ";
  $Query .= "(SELECT a.fl_maestro_sp fl_usuario, a.ds_ruta_avatar, ";
  $Query .= "b.ds_nombres ds_nombres, a.ds_empresa, ds_pais, b.no_accesos, b.fg_activo, b.fg_genero, ";
  $Query .= "c.fl_pais, b.fl_instituto, '' fl_grado, '' cl_clasificacion_grado, TIMESTAMPDIFF(YEAR,b.fe_nacimiento, CURDATE()) edad, '1' fame, ds_apaterno,fg_educational ";
  $Query .= "FROM c_maestro_sp a ";
  $Query .= "JOIN c_usuario b ON(b.fl_usuario=a.fl_maestro_sp) ";
  $Query .= "JOIN c_instituto e ON(e.fl_instituto=b.fl_instituto) ";
  $Query .= "LEFT JOIN k_usu_direccion_sp c ON(c.fl_usuario_sp=b.fl_usuario) ";
  $Query .= "LEFT JOIN c_pais d ON(d.fl_pais=c.fl_pais) ";
  $Query .= "JOIN k_instituto_filtro l ON (l.fl_instituto=b.fl_instituto) ";
  $Query .= "WHERE b.fg_activo='1' AND e.fg_activo='1' AND b.no_accesos>0 ";
  $Query .= "ORDER BY b.no_accesos DESC )";
  $Query .= "UNION ";
  $Query .= "(SELECT fl_usuario, ds_ruta_avatar, ds_nombres, ds_empresa, ds_pais, no_accesos, fg_activo, fg_genero, fl_pais, fl_instituto, fl_grado, cl_clasificacion_grado, edad, fame, ds_apaterno,fg_educational FROM ( ";
  $Query .= "(SELECT a.fl_alumno_sp fl_usuario, a.ds_ruta_avatar, b.ds_nombres ds_nombres, '' ds_empresa, ";
  $Query .= "e.ds_pais, b.no_accesos, b.fg_activo, b.fg_genero, d.fl_pais, b.fl_instituto, a.fl_grado, f.cl_clasificacion_grado, TIMESTAMPDIFF(YEAR,b.fe_nacimiento, CURDATE()) edad, '1' fame, ds_apaterno,fg_educational ";
  $Query .= "FROM c_alumno_sp a ";
  $Query .= "JOIN c_usuario b ON(a.fl_alumno_sp=b.fl_usuario) ";
  $Query .= "JOIN c_instituto c ON(b.fl_instituto=c.fl_instituto) ";
  $Query .= "LEFT JOIN k_usu_direccion_sp d ON(d.fl_usuario_sp=b.fl_usuario) ";
  $Query .= "LEFT JOIN c_pais e ON(d.fl_pais=e.fl_pais) ";
  $Query .= "LEFT JOIN k_grado_fame f ON(a.fl_grado=f.fl_grado) ";
  $Query .= "JOIN k_instituto_filtro l ON (l.fl_instituto=b.fl_instituto) ";
  $Query .= "WHERE b.fg_activo='1' AND c.fg_activo='1' AND b.no_accesos>0 ";
  $Query .= "ORDER BY b.no_accesos DESC)";
  # Stundents VANAS
  $Query .= " UNION ";
  $Query .= " (" . StudentQueryVanas($p_usuario, $fl_perfil) . ") ";
  $Query .= ") as students WHERE 1=1 ";
  # Privacity grade
  if (!empty($privacity_grade)) {
    $Query .= $privacity_grade;
  }
  # Privacity gender
  if (!empty($privacity_gender)) {
    $Query .= "AND fg_genero='" . $privacity_gender . "' ";
  }
  $Query .= ")";
  # Teachers Vanas
  $Query .= " UNION ";
  $Query .= " (" . TeacherQueryVanas() . ") ";
  $Query .= ") as UserMain WHERE fg_activo='1' ";
  # Privacity educational
  if (!empty($privacity_educational)) {
    $Query .= $privacity_educational;
  }
  # Privacity International
  if (!empty($privacity_international)) {
    $Query .= $privacity_international;
  }
  $Query .= "AND no_accesos>0 ORDER BY ds_nombres ";
  $rs = EjecutaQuery($Query);

  return $rs;
}


# Get grade user
function GetGradeUser($p_usuario)
{
  # perfil
  $fl_perfil_sp = ObtenPerfilUsuario($p_usuario);
  $row = RecuperaValor("SELECT  fl_grado FROM c_alumno_sp WHERE fl_alumno_sp=" . $p_usuario);
  $fl_grade = $row[0];

  return $fl_grade;
}

# Get Blocking Last Name
function GetBlockingLName($p_instituto)
{
  # Query
  $Query  = "SELECT fg_blocking ";
  $Query .= "FROM k_instituto_filtro WHERE fl_instituto=$p_instituto ";
  $row = RecuperaValor($Query);
  $fg_blocking = !empty($row[0])?$row[0]:NULL;
  if (empty($fg_blocking))
    $fg_blocking = 0;

  return $fg_blocking;
}

# Images png
function CreaThumbpng($p_origen, $p_destino, $p_ancho = 0, $p_alto = 0, $p_fija_lado = 0, $p_max_lado = 0)
{

  # Abre el archivo con la imagen original
  $original = imagecreatefrompng($p_origen);
  if (!$original)
    return False;
  $ancho_orig = imagesx($original);
  $alto_orig = imagesy($original);
  $ratio_orig = $ancho_orig / $alto_orig;
  if ($ancho_orig >= $alto_orig)
    $fg_horizontal = True;
  else
    $fg_horizontal = False;

  # Medidas por omision del thumb
  $ancho = 150;
  $alto = 150;

  # Calcula las dimensiones del thumb en base a una dimension maxima
  if ($p_max_lado > 0) {
    if ($ancho_orig > $p_max_lado or $alto_orig > $p_max_lado) {
      if ($fg_horizontal) {
        $p_ancho = $p_max_lado;
        $p_alto = 0;
      } else {
        $p_alto = $p_max_lado;
        $p_ancho = 0;
      }
      if ($p_fija_lado > $p_max_lado)
        $p_fija_lado = $p_max_lado;
    } else {
      $p_ancho = $ancho_orig;
      $p_alto = $alto_orig;
    }
  }

  # Fija las dimensiones del thumb
  if ($p_ancho > 0 and $p_alto > 0) {
    $ancho = $p_ancho;
    $alto = $p_alto;
  }

  # Calcula las dimensiones del thumb en base a un ancho fijo
  if ($p_ancho > 0 and $p_alto == 0) {
    $ancho = $p_ancho;
    $alto = $p_ancho / $ratio_orig; // Ajusta el alto
  }

  # Calcula las dimensiones del thumb en base a un alto fijo
  if ($p_ancho == 0 and $p_alto > 0) {
    $alto = $p_alto;
    $ancho = $p_alto * $ratio_orig; // Ajusta el ancho
  }

  # Calcula las dimensiones del thumb en base a una dimension dada por lado
  if ($p_fija_lado > 0) {
    $ancho = $p_fija_lado;
    $alto = $p_fija_lado;
    if ($fg_horizontal) // Calcula el alto
      $alto = $p_fija_lado / $ratio_orig;
    else // Calcula el ancho
      $ancho = $p_fija_lado * $ratio_orig;
  }

  # Genera la nueva imagen
  $thumb = imagecreatetruecolor($ancho, $alto);
  imagecopyresampled($thumb, $original, 0, 0, 0, 0, $ancho, $alto, $ancho_orig, $alto_orig);
  imagepng($thumb, $p_destino, 9);
  return True;
}


/**
 * MJD #funcion para notificar al alumno que algunas lecciones han sido calificadas.
 * @param 
 * 
 */
function NotificacionCalificacionTeacher($fl_alumno)
{



  #Obtenemos fecha actual.
  $fe_actual = ObtenerFechaActual();




  #Verificamos los cursos que han sido calificados correspondientes del alumno y que no han sido revisado por el alumno.
  $Query = "SELECT DISTINCT A.fl_leccion_sp 
                FROM k_entrega_semanal_sp A
                JOIN c_com_criterio_teacher B ON B.fl_leccion_sp=A.fl_leccion_sp AND A.fl_alumno=B.fl_alumno
                WHERE A.fl_alumno=$fl_alumno AND A.fl_promedio_semana IS NOT NULL  AND fg_revisado_alumno='0' ORDER BY fe_modificacion DESC ";
  $rs = EjecutaQuery($Query);
  $total = CuentaRegistros($rs);
  // $contador=0;
  // for($i=1;$row=RecuperaRegistro($rs);$i++){
  //   $contador++;
  $row = RecuperaValor($Query);


  $fl_leccion = $row[0];





  if ($total > 0) {

    #Obtenemos el programa
    $Query = "SELECT fl_programa_sp,no_semana FROM c_leccion_sp WHERE fl_leccion_sp=$fl_leccion ";
    $row = RecuperaValor($Query);
    $fl_programa_sp = $row[0];
    $no_semana = $row[1];


    #Verificamos el utlimo teahcer que asigno calificacion y que el estudiante no ha revisado. 
    $Query = "SELECT fl_maestro FROM k_usuario_programa where fl_programa_sp=$fl_programa_sp AND fl_usuario_sp=$fl_alumno ";
    $row = RecuperaValor($Query);
    $fl_maestro = $row[0];

    $nb_teacher = "<b>" . ObtenNombreUsuario($fl_maestro) . "</b>";

    $nb_tab = "assignments_grade";
  }



  //}








  $ds_titulo = ObtenEtiqueta(1682);




  # Remplazamos las variables
  $ds_titulo = str_replace("#nb_teacher#", $nb_teacher, $ds_titulo); # first name de quien envia el mensage


  if ($total > 0) {
    echo "
                    <script>
                      $(document).ready(function(){
          
                        $.smallBox({
                          title : '<h4 >" . ObtenEtiqueta(1681) . "</h4>',
                          content : '$ds_titulo <br/>" . ObtenEtiqueta(1683) . " <p class=\"text-align-right\"><a href=\"index.php#site/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab&fl_programa=$fl_programa_sp&t=1\" class=\"btn btn-primary btn-sm\" style=\"background:#7C9970;\" >" . ObtenEtiqueta(1684) . "</a> <a href=\"javascript:void(0);\" class=\"btn btn-danger btn-sm\" style=\"background:#DD8282;\">" . ObtenEtiqueta(1685) . "</a></p>',
                          color : '#2075BA',
                          icon : 'fa fa-edit swing animated'
                          //timeout : 4000
                        });
                      });
                      //pageSetUp();
                    </script>";
  }
}

/**
 * MJD #funcion el tamaño de una carpeta, mide archivo por archivo, y solamente mandamos la ruta de la carpeta.
 * @param 
 * 
 */
function size($path, $formated = true, $retstring = null)
{
  if (!is_dir($path) || !is_readable($path)) {
    if (is_file($path) || file_exists($path)) {
      $size = filesize($path);
    } else {
      return false;
    }
  } else {
    $path_stack[] = $path;
    $size = 0;

    do {
      $path  = array_shift($path_stack);
      $handle  = opendir($path);
      while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..' && is_readable($path . DIRECTORY_SEPARATOR . $file)) {
          if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
            $path_stack[] = $path . DIRECTORY_SEPARATOR . $file;
          }
          $size += filesize($path . DIRECTORY_SEPARATOR . $file);
        }
      }
      closedir($handle);
    } while (count($path_stack) > 0);
  }
  if ($formated) {
    $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    if ($retstring == null) {
      $retstring = '%01.2f %s';
    }
    $lastsizestring = end($sizes);
    foreach ($sizes as $sizestring) {
      if ($size < 1024) {
        break;
      }
      if ($sizestring != $lastsizestring) {
        $size /= 1024;
      }
    }
    if ($sizestring == $sizes[0]) {
      $retstring = '%01d %s';
    } // los Bytes normalmente no son fraccionales 
    $size = sprintf($retstring, $size, $sizestring);
  }

  return $size;
}
/**
 * MJD #funcion para envio de email,cuando un Instituto se ha suscrito por primera vez a un plan. de FAME
 * @param 
 * 
 */
function EnviarEmailAdquisicionPlan($p_instituto, $p_usuario)
{



  #Se recupera el contenido del template/correo.

  $ds_encabezado = genera_documento_sp($p_usuario, 1, 125, '');
  $ds_cuerpo = genera_documento_sp($p_usuario, 2, 125, '');
  $ds_pie = genera_documento_sp($p_usuario, 3, 125, '');
  $ds_contenido = $ds_encabezado . $ds_cuerpo . $ds_pie;

  $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$p_usuario ";
  $row = RecuperaValor($Query);
  $ds_email = $row[0];

  $ds_titulo = ObtenEtiqueta(1743); #etiqueta de asunto del mensjae FAME Alert Expiracion de plan 
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4);
  $ds_email_destinatario = $ds_email;
  $nb_nombre_dos = ObtenEtiqueta(1646); #nombre de quien envia el mensaje         
  $bcc = ObtenConfiguracion(107); #envio de copia
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
}

/**
 * MJD #funcion que se presneta para indicar la expiracion de credit card.
 * @param 
 * 
 */
function PresentaAlertExpirationCreditCar($fl_instituto, $fl_usuario)
{

  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual = strtotime('+0 day', strtotime($fe_actual));
  $fe_actual = date('Y-m-d', $fe_actual);


  #Verificamos que exista eun plan del instituto.
  $Query = "SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
  $row = RecuperaValor($Query);
  $fl_current_plan = $row[0];


  if ($fl_current_plan) {

    #Recuperamos el erfil del que esta loguedao.
    $row = RecuperaValor("SELECT fl_perfil_sp FROM c_usuario WHERE fl_usuario=$fl_usuario");
    $fl_perfil = $row[0];

    if (($fl_perfil == PFL_ADMINISTRADOR) || ($fl_perfil == PFL_MAESTRO_SELF)) {


      $fe_expiracion_tarjeta = ObtenFechaExpiracionTarjetaPagoPlan($fl_instituto);
      //$fe_expiracion_tarjeta="2017-06";
      //$fe_actual="2017-05-28";

      #Calculamos los dias restantes a expiracion de tarjeta.
      $datetime1 = date_create($fe_expiracion_tarjeta);
      $datetime2 = date_create($fe_actual);
      $interval = date_diff($datetime1, $datetime2);
      $no_dias_restantes_exp_tarjeta = $interval->days;


      $no_dias_anticipo_aviso = ObtenConfiguracion(114);



      if ($fe_expiracion_tarjeta <= $fe_actual)
        $ya_expiro = 1;

      if ($fe_expiracion_tarjeta > $fe_actual)
        $ya_expiro = 0;



      if (($no_dias_restantes_exp_tarjeta <= $no_dias_anticipo_aviso) && ($no_dias_restantes_exp_tarjeta <> 0)) {


        $etq = ObtenEtiqueta(1747);
        $etq_formato = str_replace("#fe_expiration_credit_card#", $fe_expiracion_tarjeta, $etq);

        echo "<div class='alert alert-danger alert-block fade in' style='border-left-width: 0px;background: #E67E22;margin-bottom: 0px;position:absolute; z-index:5000; top:50px; width: 100%'>
								                                        <button class='close' data-dismiss='alert' style='opacity: 1;'>
									                                        <font color='white'> x </font>
								                                        </button>
								                                        <font color='white'><i class='fa fa-credit-card-alt'></i>
								                                        <strong>!</strong> " . $etq_formato . "</font>
							                                        </div>";
      }


      #Ya expiro
      if ($ya_expiro == 1) {

        $etq = ObtenEtiqueta(1748);
        $etq_formato = str_replace("#fe_expiration_credit_card#", $fe_expiracion_tarjeta, $etq);


        echo "<div class='alert alert-danger alert-block fade in' style='border-left-width: 0px;background: #E74C3C;margin-bottom: 0px; position:absolute; z-index:5000; top:50px; width: 100%;'>
								                            <button class='close' data-dismiss='alert' style='opacity: 1;'>
									                            <font color='white'> x </font>
								                            </button>
								                            <font color='white'><i class='fa fa-credit-card-alt'></i>
								                            <strong>!</strong> " . $etq_formato . "</font>
							                            </div>";
      }
    }
  }
}

function ObtenFechaExpiracionTarjetaPagoPlan($fl_instituto)
{

  $Query = "SELECT fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta FROM k_current_plan  WHERE fl_instituto=$fl_instituto ";
  $row = RecuperaValor($Query);
  $fe_mes = $row[0];
  $fe_anio = $row[1];

  if ($fe_mes < 10)
    $fe_mes = "0" . $fe_mes;

  $fe_expiracion = $fe_anio . "-" . $fe_mes;


  return  $fe_expiracion;
}



/**
 * MJD #funcion que se presneta cuando el usuario ya cancelo su plan antes de terminarlo.
 * @param 
 * 
 */

function PresentaAlertaCancelacionPlan($fl_instituto, $fl_usuario)
{

  #Recuperamos elperfil

  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  if (($fl_perfil == PFL_ADMINISTRADOR) || ($fl_perfil == PFL_MAESTRO_SELF) || ($fl_perfil == PFL_ADM_CSF)) {


    #Verificamos si esta en TRIAL O Ya cuenta con plan.
    $Query = "SELECT fg_tiene_plan FROM c_instituto WHERE fl_instituto=$fl_instituto ";
    $row = RecuperaValor($Query);
    $fg_tiene_plan = $row[0];


    #Obtenemos fecha actual :
    $Query = "Select CURDATE() ";
    $row = RecuperaValor($Query);
    $fe_actual = str_texto($row[0]);
    $fe_actual = strtotime('+0 day', strtotime($fe_actual));
    $fe_actual = date('Y-m-d', $fe_actual);

    if (!empty($fg_tiene_plan)) {

      #Verificamos si el Instituto ya dio la orden de cancelar su cuenta de Fame.
      $Query = "SELECT fg_motivo_pago FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto ";
      $row = RecuperaValor($Query);
      $fg_motivo = $row[0];

      $fe_terminacion_plan = GeneraFormatoFecha(ObtenFechaFinalizacionContratoPlan($fl_instituto));



      if ($fg_motivo == 4) {

        $etq = ObtenEtiqueta(1753);
        $etq_formato = str_replace("#fe_expiration_plan#", $fe_terminacion_plan, $etq);

        echo "
			                    <style>
			                     .fua2{
			                        background-color: rgba(50, 118, 177, 0) !important;
			                        border-color: #fafcfe !important;
			                     }
		                         .bor{
		                            border-left-width: 0px !important;
		                            background: #E65723 !important;
		                            margin-bottom: 0px !important;
		                            padding: 10px !important;
                                    position:absolute; z-index:5000;width: 100%
		                         }
			                    </style>
			                    ";
        echo "<div class='alert alert-danger alert-block fade in bor' style=''>
									        <button class='close' data-dismiss='alert' style='opacity: 1;'>
										        <font color='white'> x </font>
									        </button>
									        <font color='white'><i class='fa fa-thumbs-down'></i>
									        <strong> </strong> " . $etq_formato . " <a href='index.php#site/node.php?node=155&t=1' style='' name='btn_renew2' id='btn_renew2' class='btn btn-primary btn-xs fua2'  >" . ObtenEtiqueta(1500) . "</a> </font>
								     </div>";
      } else {

        #verificmos si  la fecha de terminancion de Trial ya vencio, se preseta msg de que su periodo ya expiro.
        $Query = "SELECT fe_periodo_final FROM k_current_plan WHERE fl_instituto=$fl_instituto  ";
        $row = RecuperaValor($Query);
        $fe_terminacion_plan_ = $row[0];
        $fe_terminacion_plan = GeneraFormatoFecha($row[0]);


        if ($fe_terminacion_plan_ < $fe_actual) {


          $etq = ObtenEtiqueta(1767);
          $etq_formato = str_replace("#fe_expiration_plan#", $fe_terminacion_plan, $etq);


          echo "
			                            <style>
			                             .fua2{
			                                background-color:  rgba(50, 118, 177, 0) !important;
			                                border-color: #fafcfe !important;
			                             }
		                                 .bor{
		                                    border-left-width: 0px !important;
		                                    background: #E65723 !important;
		                                    margin-bottom: 0px !important;
		                                    padding: 10px !important;
                                            position:absolute; z-index:5000;width: 100%
		                                 }
			                            </style>
			                            ";
          echo "<div class='alert alert-danger alert-block fade in bor' style=''>
									                <button class='close' data-dismiss='alert' style='opacity: 1;'>
										                <font color='white'> x </font>
									                </button>
									                <font color='white'><i class='fa fa-thumbs-down'></i>
									                <strong> </strong> " . $etq_formato . " <a href='index.php#site/node.php?node=155&t=1' style='' name='btn_renew2' id='btn_renew2' class='btn btn-primary btn-xs fua2'  >" . ObtenEtiqueta(1500) . "</a> </font>
								             </div>";
        }
      }
    } else {


      #Para Trials.


      #verificmos si  la fecha de termiancion de Trial ya vencio, se preseta msg de que su periodo ya expiro.
      $Query = "SELECT fe_trial_expiracion FROM c_instituto WHERE fl_instituto=$fl_instituto  ";
      $row = RecuperaValor($Query);
      $fe_terminacion_trial = $row[0];
      $fe_terminacion_plan = GeneraFormatoFecha($row[0]);


      #Presenta mensaje de su cuenta ya expiro
      if ($fe_terminacion_trial < $fe_actual) {



        $etq = ObtenEtiqueta(1767);
        $etq_formato = str_replace("#fe_expiration_plan#", $fe_terminacion_plan, $etq);


        echo "
			                            <style>
			                             .fua2{
			                                background-color:  rgba(50, 118, 177, 0) !important;
			                                border-color: #fafcfe !important;
			                             }
		                                 .bor{
		                                    border-left-width: 0px !important;
		                                    background: #E65723 !important;
		                                    margin-bottom: 0px !important;
		                                    padding: 10px !important;
                                            position:absolute; z-index:5000;width: 100%
		                                 }
			                            </style>
			                            ";
        echo "<div class='alert alert-danger alert-block fade in bor' style=''>
									                <button class='close' data-dismiss='alert' style='opacity: 1;'>
										                <font color='white'> x </font>
									                </button>
									                <font color='white'><i class='fa fa-thumbs-down'></i>
									                <strong> </strong> " . $etq_formato . " <a href='index.php#site/node.php?node=155&t=2' style='' name='btn_renew2' id='btn_renew2' class='btn btn-primary btn-xs fua2'  >" . ObtenEtiqueta(1500) . "</a> </font>
								             </div>";
      }
    }
  }
}



/**
 * MJD ##funcion para conocer cuando finaliza el trial.
 * @param 
 * 
 */
function ObtenFechaFinalizacionTrial($p_instituto)
{
  $row = RecuperaValor("SELECT fe_trial_expiracion FROM c_instituto WHERE fl_instituto=$p_instituto AND fg_tiene_plan='0' ");

  return $row[0];
}


# User indited By
function User_Invited($fl_usuario)
{
  # Obtenemos el usuario que invito al usuario
  $Query = "SELECT fl_usuario, fl_usu_invita FROM c_usuario  WHERE fl_usuario=" . $fl_usuario;
  $row = RecuperaValor($Query);
  $fl_usu_invita = $row[1];
  # Nombre del usuario  que invita
  $user_invited = ObtenNombreUsuario($fl_usu_invita);
  # Obtenemosel perfil del usuario
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usu_invita);
  $row1 = RecuperaValor("SELECT nb_perfil FROM c_perfil WHERE fl_perfil=" . $fl_perfil_sp);
  $nb_perfil = $row1[0];
  return $user_invited . " (" . $nb_perfil . ")";
}


/**
 * MJD ##funcion para enviar email del invoice.
 * @param 
 * 
 */

function EnviaEmailInvoice($fl_instituto, $fl_usuario, $id_invoice = '', $id_pago = '')
{

  # Recupera datos usuario
  $Query  = "SELECT ds_nombres, ds_apaterno,ds_email ";
  $Query .= "FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto($row[0]);
  $ds_lname = str_texto($row[1]);
  $ds_email = str_texto($row[2]);

  $Query2 = "SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row = RecuperaValor($Query2);
  $nb_instituto = str_texto($row[0]);


  if (empty($id_invoice))
    $id_invoice = $id_pago;

  # Obtenemos la informacion del template header body or footer
  $Query1  = "SELECT ds_encabezado,ds_cuerpo,ds_pie FROM k_template_doc WHERE fl_template=127 ";
  $row = RecuperaValor($Query1);
  $cadena = $row[0] . "<br/>" . $row[1] . "<br/>" . $row[2];

  # Sustituye caracteres especiales
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);

  #Reemplazamos las variables.
  $cadena = str_replace("#fame_fname_admin#", $ds_fname, $cadena);
  $cadena = str_replace("#fame_lname_admin#", $ds_lname, $cadena);
  $cadena = str_replace("#fame_id_invoice#", $id_invoice, $cadena);
  $cadena = str_replace("#fame_id_charge#", $id_pago, $cadena);
  $cadena = str_replace("#fame_name_school#", $nb_instituto, $cadena);

  $ds_contenido = $cadena;

  $ds_titulo = ObtenEtiqueta(1768); #etiqueta de asunto del mensjae FAME Alert Expiracion de plan 
  $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(4);
  $ds_email_destinatario = $ds_email;
  $nb_nombre_dos = ObtenEtiqueta(1646); #nombre de quien envia el mensaje         
  $bcc = ObtenConfiguracion(107); #envio de copia
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
}

/**
 * MJD ##funcion para generar invoice del attach.
 * @param 
 * 
 */
function GeneraInvoice($fl_instituto, $fl_usuario, $id_invoice = '', $id_pago = '', $fl_template, $opc)
{

  # Recupera datos usuario
  $Query  = "SELECT ds_nombres, ds_apaterno,ds_email ";
  $Query .= "FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto($row[0]);
  $ds_lname = str_texto($row[1]);
  $ds_email = str_texto($row[2]);

  $Query2 = "SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row = RecuperaValor($Query2);
  $nb_instituto = str_texto($row[0]);


  if (empty($id_invoice))
    $id_invoice = $id_pago;


  # Recupera datos del template del documento
  switch ($opc) {
    case 1:
      $campo = "ds_encabezado";
      break;
    case 2:
      $campo = "ds_cuerpo";
      break;
    case 3:
      $campo = "ds_pie";
      break;
    case 4:
      $campo = "nb_template";
      break;
  }

  # Obtenemos la informacion del template header body or footer
  $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
  $row = RecuperaValor($Query1);
  $cadena = $row[0];

  # Sustituye caracteres especiales
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);

  #Reemplazamos las variables.
  $cadena = str_replace("#fame_fname_admin#", $ds_fname, $cadena);
  $cadena = str_replace("#fame_lname_admin#", $ds_lname, $cadena);
  $cadena = str_replace("#fame_id_invoice#", $id_invoice, $cadena);
  $cadena = str_replace("#fame_id_charge#", $id_pago, $cadena);
  $cadena = str_replace("#fame_name_school#", $nb_instituto, $cadena);

  return (str_uso_normal($cadena));
}
# Funcion para verificar si el programa no tiene prerequistos
function Mandatory_programas($fl_usuario, $fl_programa, $fl_programa_sp)
{
  $fl_programa_obl = $fl_programa;
  # Verfica si tiene prerequisitos el programa
  $programa_requerido = Requisito_programa($fl_programa_obl);
  # Si requiere programa
  if (!empty($programa_requerido)) {
    # Si ya curso el programa
    if (ExisteEnTabla('k_usuario_programa', 'fl_programa_sp', $programa_requerido, 'fl_usuario_sp', $fl_usuario, true)) {
      # Verifica si ya lo termino
      $row0 = RecuperaValor("SELECT fg_terminado FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$programa_requerido");
      $fg_terminado = $row0[0];
      if (!empty($fg_terminado)  || $fg_terminado == '1') {
        $prerequisito = $fl_programa_obl;
        $inicia = true;
      } else {
        $prerequisito = $programa_requerido;
        $inicia = false;
      }
    } else {

      $prerequisito = $programa_requerido;
      $inicia = false;
    }
  } else {
    // $prerequisito = $fl_programa_obl;
    // $inicia = true;

    # Si ya curso el programa
    if (ExisteEnTabla('k_usuario_programa', 'fl_programa_sp', $fl_programa_obl, 'fl_usuario_sp', $fl_usuario, true)) {
      # Verifica si ya lo termino
      $row0 = RecuperaValor("SELECT fg_terminado FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa_obl");
      $fg_terminado = $row0[0];
      if (!empty($fg_terminado)) {
        $prerequisito = $fl_programa_sp;
        $inicia = true;
      } else {
        $prerequisito = $fl_programa_obl;
        $inicia = false;
      }
    } else {

      $prerequisito = $fl_programa_obl;
      $inicia = false;
    }
  }
  # Valores si puede tomar el curso o requier prerequisito
  $valores = array(
    'inicia' => $inicia,
    'prerequisito' => $prerequisito,
    'test' => $fl_programa
  );

  return $valores;
}


# Obtenemos el prerequisito del programa
function Requisito_programa($fl_programa)
{
  $roww = RecuperaValor("SELECT fl_programa_sp_rel FROM k_relacion_programa_sp WHERE fl_programa_sp_act=" . $fl_programa . " AND fg_puesto='ANT'");
  $fl_programa_sp_rel = $roww[0];

  return $fl_programa_sp_rel;
}

# Funtion get min & max age user depende frade
function GetGradeAge($p_grade)
{
  $row = RecuperaValor("SELECT cl_clasificacion_grado, no_edad_min, no_edad_max FROM k_grado_fame WHERE fl_grado=$p_grade");
  $cl_clasificacion_grado = $row[0];
  $no_edad_min = $row[1];
  $no_edad_max = $row[2];
  $intervalos = array(
    "cl_clasificacion_grado" => $cl_clasificacion_grado,
    "no_edad_min" => $no_edad_min,
    "no_edad_max" => $no_edad_max
  );

  return $intervalos;
}


function VanasBoard($p_usuario, $p_instituto, $index, $index_end)
{
  # Get perfil
  $fl_perfil = ObtenPerfilUsuario($p_usuario);
  if ($fl_perfil == PFL_ESTUDIANTE_SELF) {
    # Get grade user
    $fl_grade = GetGradeUser($p_usuario);
    # Get ages grades
    $gradefame = GetGradeAge($fl_grade);
    $calificacion = $gradefame['cl_clasificacion_grado'];
    $min = $gradefame['no_edad_min'];
    $max = $gradefame['no_edad_max'];
    if (empty($calificacion))
      $calificacion = '0';
  } else {
    $fl_grade = '0';
    $calificacion = '0';
  }
  # Get post vanas
  $Query  = "SELECT fl_gallery_post, fl_entregable, fl_usuario, ds_title, ds_name, nb_archivo, fe_post1, ds_post, fg_genero, fl_grado, ";
  $Query .= "cl_clasificacion_grado, fl_instituto, fl_pais, nb_programa, fl_perfil_sp, fe_post, edad, fame, fl_programa_sp,fg_educational ";
  $Query .= "FROM( ";
  $Query .= "SELECT a.fl_gallery_post, a.fl_entregable, a.fl_usuario, a.ds_title, CONCAT(c.ds_nombres,' ',c.ds_apaterno) AS ds_name, ";
  $Query .= "a.nb_archivo, DATE_FORMAT(DATE_ADD(fe_post,INTERVAL 0 HOUR),'%M %e, %Y') fe_post1, ds_post, c.fg_genero," . $fl_grade . " fl_grado, " . $calificacion . " cl_clasificacion_grado, ";
  $Query .= "1 fl_instituto, CASE c.fl_perfil WHEN 2 THEN (SELECT fl_pais FROM  c_maestro r WHERE r.fl_maestro = a.fl_usuario) ";
  $Query .= "WHEN 3 THEN (SELECT t.ds_add_country FROM k_ses_app_frm_1 t WHERE t.cl_sesion = c.cl_sesion) END fl_pais, ";
  $Query .= "b.nb_tema nb_programa, c.fl_perfil fl_perfil_sp, a.fe_post, TIMESTAMPDIFF(YEAR,c.fe_nacimiento, CURDATE()) AS edad, '0' fame, '0' fl_programa_sp,''fg_educational ";
  $Query .= "FROM k_gallery_post a ";
  $Query .= "LEFT JOIN c_f_tema b ON a.fl_tema=b.fl_tema ";
  $Query .= "LEFT JOIN c_usuario c ON a.fl_usuario=c.fl_usuario ";
  $Query .= "LEFT JOIN k_alumno_grupo d ON a.fl_usuario=d.fl_alumno ";
  $Query .= "WHERE nb_tema IS NOT NULL ";

  if (($fl_perfil == PFL_ESTUDIANTE_SELF) && (!empty($min)) && (!empty($max)))
    $Query .= "AND TIMESTAMPDIFF(YEAR,c.fe_nacimiento, CURDATE()) BETWEEN " . $min . " AND " . $max . " AND fe_nacimiento IS NOT null ";
  $Query .= "ORDER BY a.fe_post DESC ";
  $Query .= "LIMIT $index_end OFFSET $index ";
  $Query .= ") AS vanas WHERE 1 = 1 ";
  # Get privacity institute GENDER
  # Same gender view users of vanas with same gender of the user current
  # All gender view all users of vanas of both of them gender
  $GetPrivacityGenderInst = GetPrivacityGenderInst($p_instituto, $p_usuario);
  if (!empty($GetPrivacityGenderInst))
    $Query .= "AND fg_genero='" . $GetPrivacityGenderInst . "' ";
  # Get privacity institute INTERNATIONAL
  # Worl view user vanas
  # My country  view users vanas with the country of the  user current
  $GetPrivacityInternationalInst = GetPrivacityInternationalInst($p_instituto, $p_usuario);
  if (!empty($GetPrivacityInternationalInst))
    $Query .= $GetPrivacityInternationalInst;
  # Get privacity institute EDUCATIONAL
  # All FAME Parterns view user vanas
  # My School view school of user current not view vanas why itÂ´s one
  $GetPrivacityEducationalInst = GetPrivacityEducationalInst($p_instituto, $p_usuario);
  if (!empty($GetPrivacityEducationalInst))
    $Query .= $GetPrivacityEducationalInst;

  return $Query;
}


/**
 * MJD ##funcion para generar email de aeptacion, str variables globales, admin, teachr student.
 * @param 
 * 
 */

function genera_documento_aceptacionFAME($fl_envio_correo, $opc, $fl_template,  $fl_perfil_invitado, $fl_usuario = '')
{




  # Recupera datos del template del documento
  switch ($opc) {
    case 1:
      $campo = "ds_encabezado";
      break;
    case 2:
      $campo = "ds_cuerpo";
      break;
    case 3:
      $campo = "ds_pie";
      break;
    case 4:
      $campo = "nb_template";
      break;
  }

  #Obtenemos la informacion del template header body or footer
  $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
  $row = RecuperaValor($Query1);
  $cadena = str_uso_normal($row[0]);

  # Sustituye caracteres especiales
  $cadena = $row[0];
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);


  #Recuperamos a quien se le ha enviado la invitacion:
  $Query = "SELECT ds_first_name,ds_last_name,ds_email FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio_correo ";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto($row[0]);
  $ds_lname = str_texto($row[1]);

  $nb_invitado = $ds_fname . " " . $ds_lname;

  #Recuperamos quien invito a este usuario segun perfil
  if ($fl_perfil_invitado == PFL_ADMINISTRADOR) {


    #Recuperamos el nombre de quien hizo la invitacion , por deaful es el instituto Vanas.ca solo en este caso que palica para la incvitacion por primera vez al instituto
    $nb_invitador = "Vancouver Animation School";
  }
  if (($fl_perfil_invitado == PFL_MAESTRO_SELF) || ($fl_perfil_invitado == PFL_ESTUDIANTE_SELF)) {

    #Recupermos el nombre de quien hizo la invitacion
    $Query = "SELECT ds_nombres,ds_apaterno FROM c_usuario  WHERE fl_usuario=$fl_usuario ";
    $row = RecuperaValor($Query);
    $ds_fname = str_texto($row[0]);
    $ds_lname = str_texto($row[1]);
    $nb_invitador = $ds_fname . " " . $ds_lname;
  }

  #Sustitumos y remplazamos varibles del template.
  $cadena = str_replace("#inviter_name#", $nb_invitador, $cadena);  # nombre de quien invito.
  $cadena = str_replace("#invited_name#", $nb_invitado, $cadena);   #nombre del invitado a fame.



  return $cadena;
}


/**
 * MJD ##funcion para verificar si una rubric que ya tiene signada el alumno ha sufrido modificaciones.
 * @param 
 * 
 */
function VerificaCambiosRubricActual($fl_leccion_sp, $fl_alumno)
{


  #Verificamos el criterio actual de la leccion
  $Query = "SELECT COUNT(*)
                                FROM  k_criterio_programa_fame K
                                JOIN c_leccion_sp C ON C.fl_leccion_sp =K.fl_programa_sp
                                JOIN c_criterio T ON T.fl_criterio=K.fl_criterio WHERE K.fl_programa_sp=$fl_leccion_sp ";
  $row = RecuperaValor($Query);
  $no_rubrics = $row[0];

  #Verificamos el criterio actual de leccion congeladad del alumno.

  $Query2 = "SELECT  COUNT(*)
                                 FROM  k_criterio_programa_alumno_fame K
                                 JOIN c_leccion_sp C ON C.fl_leccion_sp =K.fl_programa_sp
                                 JOIN c_criterio T ON T.fl_criterio=K.fl_criterio WHERE K.fl_programa_sp=$fl_leccion_sp AND K.fl_usuario_sp=$fl_alumno 	
                                 ";
  $row2 = RecuperaValor($Query2);
  $no_rubrics2 = $row2[0];


  if ($no_rubrics == $no_rubrics2)
    $fg_estatus = false;
  else
    $fg_estatus = true;

  return $fg_estatus;
}

function SavePromedio_Q_T($fl_programa_sp, $fl_usuario)
{

  # Obtenemos informacion
  # Buscamos folio del programa y alumno
  $row0 = RecuperaValor("SELECT fl_usu_pro FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario and fl_programa_sp=$fl_programa_sp");
  $fl_usu_pro = $row0[0];

  # Buscamos si el teacher lo califica, si tiene permisos.
  $row00 = RecuperaValor("SELECT fg_quizes, fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=$fl_usu_pro");
  $fg_quizes = $row00[0];
  $fg_grade_tea = $row00[1];

  # Si esta leccion tiene Quiz actualizamos el promedio final del mismo  
  #Recuperamos todas las lecciones del programa
  $Query2 = " SELECT fl_leccion_sp,no_semana, ds_titulo,nb_quiz,no_valor_quiz  "
    . " FROM c_leccion_sp WHERE fl_programa_sp=$fl_programa_sp ";
  $rs2 = EjecutaQuery($Query2);
    $contador_califi_finales=0;
  $suma_quizes_calific=NULL;
  for ($tot2 = 0; $row2 = RecuperaRegistro($rs2); $tot2++) {
    $fl_leccion_sp_bd = $row2[0];

    # Verifica si tiene quiz la leccion y ha realizado intentos
    if (
      ExisteEnTabla('k_quiz_pregunta', 'fl_leccion_sp', $fl_leccion_sp_bd)
      and ExisteEnTabla('k_quiz_calif_final', 'fl_leccion_sp', $fl_leccion_sp_bd, 'fl_usuario', $fl_usuario, true)
    ) {
      #Recuperamos los quizes por cada leccion del programa.
      $Query3 = "
          SELECT max(no_intento), no_calificacion,no_intento  
          FROM k_quiz_calif_final 
          WHERE fl_leccion_sp=$fl_leccion_sp_bd
          AND fl_usuario=$fl_usuario ORDER BY no_intento DESC ";
      $row3 = RecuperaValor($Query3);
      $no_intento = $row3['no_intento'];
      $no_total_calif = $row3['no_calificacion'];
      if (!empty($no_total_calif)) {
        $suma_quizes_calific += $no_total_calif;
        $contador_califi_finales++;
      }
    }
  }
  #Calculo de los promedios:           
  $promedio_quizes = $contador_califi_finales==0?0:($suma_quizes_calific/$contador_califi_finales);
  # actulizamos el registro    
  EjecutaQuery("UPDATE k_details_usu_pro SET no_prom_quiz=$promedio_quizes WHERE fl_usu_pro=$fl_usu_pro");

  # Verificamos si el teacher califica al estudiante
  if (!empty($fg_grade_tea)) {
    # Realizamos  verificamos ls calificaciones del estudiante
    $row0 = RecuperaValor("SELECT SUM( no_calificacion), COUNT(*) FROM k_calificacion_teacher WHERE fl_programa_sp=$fl_programa_sp AND fl_alumno=$fl_usuario");
    $sum_weeks = $row0[0] / $row0[1];
    $tot_tea = $sum_weeks;
    # Actualizamos     
    EjecutaQuery("UPDATE k_details_usu_pro SET no_prom_teacher=$tot_tea WHERE fl_usu_pro=$fl_usu_pro");
  }
}
# function to cut text
function CutText($txt = '', $lim = 1)
{
  $txt = trim($txt);
  $txt = strip_tags($txt);
  $size = strlen($txt);
  $resultado = '';
  if ($size <= $lim) {
    return $txt;
  } else {
    $txt = substr($txt, 0, $lim);
    $words = explode(' ', $txt);
    $result = implode(' ', $words);
    $result .= '.';
  }
  return $result;
}



/**
 * MJD ##funcion para verificar el alumno es de vanas y puede desbloquear curso atraves de envio de emeails o pago directo.
 * @param 
 * 
 */
function PuedeLiberarCurso($fl_instituto, $fl_usuario)
{

  #Verificamos si no es estudiante nuevo,registro
  $Query = "SELECT DATE(fe_alta),fg_b2c FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row = RecuperaValor($Query);
  $fg_b2c = $row['fg_b2c'];
    
  /*los b2c solo seran cuando el usuario tenga instituto 4 y tenga fg_b2c='1'*/
    if (($fg_b2c == 1)&&($fl_instituto==4)) {

      $fg_desbloquear = 1;
    } else {

      $fg_desbloquear = 0;
    }
  

  return $fg_desbloquear;
}

/**
 * MJD ##funcion para verificar si ya puede comensar el curso o sigue bloqueado.
 * @param 
 * 
 */
function VerificaCumplientoRequisitoParaAccederCurso($fl_alumno, $fl_programa_sp, $no_email_requeridos = '', $fg_plan = '', $fg_pago_curso = '',$fg_assign_myself_course='')
{



  if (empty($fg_plan)) {

    if ($fg_pago_curso) { #Quiere decir que pago curso y ya lo tiene desbloqueado               
      $fg_estatus = true;
    } else { #El desbloqueo depende del numero de emails confirmados


      #Verificamos si existe registro de envio de emails del alumno que ya esten confirmados.
      $Query = "SELECT COUNT(*)  
                        FROM c_desbloquear_curso_alumno A 
                        LEFT JOIN k_envio_email_reg_selfp B ON B.fl_envio_correo=A.fl_envio_correo 
                        WHERE  A.fl_invitado_por_usuario=$fl_alumno AND A.fl_programa_sp=$fl_programa_sp AND B.fg_confirmado='1' ";
      $row = RecuperaValor($Query);
      $no_invitaciones_confirmadas = $row[0];

      if ($no_email_requeridos == $no_invitaciones_confirmadas) {

        #Verifica que si ya habia enviado invitacion se habia segerado dsu plan de N/ dias para tomar el curso si ya revaso ya lo puede tomar solo hasta wue complre plan, . 
        $fg_activo = FAMEVerificaFechaExpiracionTrialCursoAlumno($fl_alumno, $fl_programa_sp);

        if ($fg_activo) {
          $fg_estatus = true;
        } else {
          $fg_estatus = false;
        }
      } else {
        $fg_estatus = false;
      }
    }
  } else {

    #Verificamos si el perido de vigencia igue activo y si no pues no tiene acceso.
    $vigente = VerificaVigenciaPlanAlumno($fl_alumno);


    if ($vigente) {
      $fg_estatus = true;
    } else {
      $fg_estatus = false;
    }
  }

  #Por defuault el gettin started FAME.
  if ($fl_programa_sp == 33)
    $fg_estatus = true;
  #Por default si esta activado el $fg_assign_myself_course
  if($fg_assign_myself_course==1){
      $fg_estatus=true;
  }


  return $fg_estatus;
}


/**
 * MJD ##funcion para verificar si su perido de vigencia esta activo.
 * @param 
 * 
 */
function VerificaVigenciaPlanAlumno($fl_alumno)
{

  $Query = "SELECT fe_periodo_final FROM k_current_plan_alumno WHERE fl_alumno=$fl_alumno ";
  $row = RecuperaValor($Query);
  $fe_periodo_final = !empty($row['fe_periodo_final'])?$row['fe_periodo_final']:NULL;

  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual = strtotime('+0 day', strtotime($fe_actual));
  $fe_actual = date('Y-m-d', $fe_actual);




  if ($fe_periodo_final >= $fe_actual) {
    $vigente = true;
  } else
    $vigente = false;


  return $vigente;
}



/**
 * MJD ##funcion para verificar si ya ha enviado invitaciones y cuantos son.
 * @param 
 * 
 */
function CuentaEmailEnviadosDesbloquearCurso($fl_alumno, $fl_programa_sp)
{

  $Query = "SELECT COUNT(*)  
                FROM  k_envio_email_reg_selfp  A  
                 JOIN c_desbloquear_curso_alumno B ON B.fl_envio_correo=A.fl_envio_correo
                WHERE  B.fl_invitado_por_usuario=$fl_alumno AND B.fl_programa_sp=$fl_programa_sp ";
  $row = RecuperaValor($Query);
  $no_mail_enviados = $row[0];




  return $no_mail_enviados;
}
/**
 * MJD ##Devuelve el numero email confirmados con formato.
 * @param 
 * 
 */
function EmailConfirmadosDesbloquearCurso($fl_alumno, $fl_programa_sp)
{

  $Query = "SELECT B.ds_email ,B.fg_confirmado  
                FROM c_desbloquear_curso_alumno A 
                LEFT JOIN k_envio_email_reg_selfp B ON B.fl_envio_correo=A.fl_envio_correo 
                WHERE  A.fl_invitado_por_usuario=$fl_alumno AND A.fl_programa_sp=$fl_programa_sp  ";
  //$Query.=" AND B.fg_confirmado='1' ";
  $rs = EjecutaQuery($Query);
  $tot = CuentaRegistros($rs);
  for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
    $ds_email = str_texto($row[0]);
    $fg_confirmado = $row[1];

    if ($fg_confirmado) {
      $color = "#0c7b05";
      $aweson = "fa-check-circle-o ";
    } else {
      $color = "#B93013";
      $aweson = "fa-times-circle-o";
    }
    echo "<small style='font-size:93%;'>$ds_email </small><i class='fa $aweson' aria-hidden='true' style='color:$color;'></i><br/>   ";
  }
}

/**
 * MJD ##funcion para verificar si ya puede comensar el curso o sigue bloqueado. ojo es por por medio del pago 2da opcion de un año
 * @param 
 * 
 */
function VerificaPagoCurso($fl_usuario, $fl_programa_sp)
{


  #Verificamos su plan actual del curso (vigenci del año).
  $Query = "SELECT fe_periodo_inicial,fe_periodo_final FROM k_plan_curso_alumno WHERE fl_alumno=$fl_usuario AND fl_programa_sp=$fl_programa_sp ";
  $row = RecuperaValor($Query);
  $fe_inicio_periodo = !empty($row['fe_periodo_inicial'])?$row['fe_periodo_inicial']:NULL;
  $fe_final_periodo = !empty($row['fe_periodo_final'])?$row['fe_periodo_final']:NULL;

  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);

  if (!empty($fe_final_periodo)) {

    if ($fe_final_periodo >= $fe_actual) {

      $fg_activo = 1;
    } else {

      $fg_activo = 0;
    }
  } else
    $fg_activo = 0;



  /*  $Query="SELECT fl_pago_curso_alumno FROM k_pago_curso_alumno WHERE fl_alumno_sp=$fl_usuario AND fl_programa_sp=$fl_programa_sp ";
         $rows=RecuperaValor($Query);
         $fl_pago=$rows['fl_pago_curso_alumno'];
       */




  return $fg_activo;
}



/**
 * MJD ##funcion para recuperar el plan actual del alumno si la tiene
 * @param 
 * 
 */
function RecuperaPlanActualAlumnoFame($fl_usuario){

  $Query = "SELECT fg_plan FROM k_current_plan_alumno WHERE fl_alumno=$fl_usuario ";
  $ro = RecuperaValor($Query);
  $fg_plan = str_texto($ro[0]??NULL);

  return $fg_plan;
}




/**
 * MJD ##funcion asignar todos los curos al alumno por que ya compro un plan.
 * @param 
 * 
 */
function  AsignarTodosLosCursosAlAlumno($fl_alumno){

  #Recuperamos el instituto del alumno
  $fl_instituto = ObtenInstituto($fl_alumno);

  # Query Principal
  $Query  = " SELECT fl_leccion_sp, ";
  $Query .= " a.fl_programa_sp,b.fl_instituto ";
  $Query .= " FROM c_leccion_sp a LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) LEFT JOIN k_programa_detalle_sp c ON(c.fl_programa_sp=b.fl_programa_sp) ";
  $Query .= "WHERE b.fg_publico='1' GROUP BY b.fl_programa_sp ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
    $fl_leccion_sp = $row[0];
    $fl_programa_sp = $row[1];
    $fl_instituto_programa=$row[2];

    #Cuando el fl_instituto programa viene con dato quiere decir que este curso fue creado por un instituto entonces validamos si puede ser compartido o no.
    if(!empty($fl_instituto_programa)){
        if($fl_instituto_programa==$fl_instituto){
            $fg_asignar=1;
        }else{
            #Verificamos que si ese es curso de de otro instituto y tiene permiso para compartir.
            $fg_asignar=Share_Course($fl_programa_sp,$fl_instituto_programa='',$fl_instituto='');
        }

        
    }else{
        $fg_asignar=1;   
    }

    if($fg_asignar==1){
        #Verifica que no exista el registro.
        $Query = "SELECT COUNT(*) FROM k_usuario_programa WHERE fl_usuario_sp=$fl_alumno AND fl_programa_sp=$fl_programa_sp ";
        $rw = RecuperaValor($Query);
        $no_reg = $rw[0];

        if (empty($no_reg)) {

            $fl_maestro = "642";
            $Query = "INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fg_certificado,fg_status,fg_pagado,mn_pagado,fl_maestro,fg_status_pro,fe_inicio_programa) ";
            $Query .= "VALUES($fl_alumno,$fl_programa_sp,0,'0','0','RD','0',0,$fl_maestro,'0',CURRENT_TIMESTAMP)";
            $fl_usu_pro = EjecutaInsert($Query);

            #Generamos su token
            $token=sha256($fl_usu_pro);
            EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro ");

            # Por defaul indicamos que tendran una calificacion de quiz
            EjecutaQuery("INSERT INTO k_details_usu_pro (fl_usu_pro,fg_quizes,fg_grade_tea) VALUES ($fl_usu_pro,'1','0')");
            #Se genera el orden cronologico de desbloqueo.
            $Quert = "SELECT no_orden FROM k_orden_desbloqueo_curso_alumno WHERE fl_alumno=$fl_alumno ORDER BY no_orden DESC ";
            $fl = RecuperaValor($Quert);
            $no_consecutiv = $fl['no_orden'] + 1;

            #Se genera su registro.
            $fl_consecu = EjecutaInsert("INSERT INTO k_orden_desbloqueo_curso_alumno (fl_alumno,fl_programa_sp,no_orden,fe_creacion,fg_motivo )VALUES($fl_alumno , $fl_programa_sp,$no_consecutiv,CURRENT_TIMESTAMP,'PL') ");
        }
    }


  }
}



/**
 * MJD ##funcion para saber cuantos cursos se han liberado por medio del envio de emails..
 * @param 
 * 
 */
function CuentaNoCursosPorMetodoEnvioEmails($fl_alumno, $fl_programa_sp)
{
  //B.fl_invitado_por_usuario, A.fl_programa_sp
  $Query = "
               SELECT DISTINCT COUNT(*)
                           FROM k_usuario_programa A
                           LEFT JOIN (

                            SELECT MIN(fl_programa_sp) fl_programa_sp, C.fl_invitado_por_usuario,B.fg_confirmado 
                            FROM c_desbloquear_curso_alumno C
                            left JOIN k_envio_email_reg_selfp B ON B.fl_envio_correo = C.fl_envio_correo
                            )B ON B.fl_programa_sp = A.fl_programa_sp
   								  AND B.fl_invitado_por_usuario = A.fl_usuario_sp
                           WHERE A.fl_usuario_sp =$fl_alumno AND B.fg_confirmado='1' 
                        
                ";
  //$Query.="  AND B.fl_invitado_por_usuario IS NOT NULL ";
  //$rs=EjecutaQuery($Query);
  //$no_envios=CuentaRegistros($rs);

  /* $Query="
                        SELECT COUNT(*) 
                        FROM k_envio_email_reg_selfp a, c_desbloquear_curso_alumno b 
                        WHERE a.fl_envio_correo=b.fl_envio_correo  AND fg_desbloquear_curso='1'
                        AND fl_invitado_por_usuario=$fl_alumno AND fg_confirmado='1'
               ";
              */
  $row = RecuperaValor($Query);
  $no_envios = $row[0];

  return $no_envios;
}



/**
 * MJD ##funcion para saber la fecha de expiracion de plan del alumno
 * @param 
 * 
 */
function ObtenFechaExpiracionPlanAlumnoFAME($fl_usuario)
{

  $Query = "SELECT fe_periodo_final FROM k_current_plan_alumno WHERE fl_alumno=$fl_usuario ";
  $ro = RecuperaValor($Query);
  $fe_periodo_final = str_texto($ro[0]);

  return $fe_periodo_final;
}


/**
 * MJD ##funcion para saber la fecha de expiracion del curso en modo trial del alumno-curso
 * @param 
 * 
 */
function FAMEVerificaFechaExpiracionTrialCursoAlumno($fl_alumno, $fl_programa_sp)
{

  $Query = "SELECT fe_periodo_inicial,fe_periodo_final FROM  k_periodo_trial_curso_alumno WHERE fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp  ";
  $row = RecuperaValor($Query);
  $fe_periodo_inicial = $row[0];
  $fe_periodo_final = $row[1];



  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);

  if (!empty($fe_periodo_final)) {
    if ($fe_periodo_final >= $fe_actual) {
      $fg_activo = true;
    } else {
      $fg_activo = false;
    }
  } else
    $fg_activo = false;


  return $fg_activo;
}


/**
 * MJD ##funcion para saber cuantos dias le restan a este curso , de periodo trial,(este solo aplica para aquelllos curso que se liberaron por el metodo de envio de emails).
 * @param 
 * 
 */
function MuestraTiempoRestanteTrialCurso($fl_usuario, $fl_programa_sp, $fg_pago = '')
{

  if (!empty($fg_pago))
    $Query = "SELECT fe_periodo_final FROM k_plan_curso_alumno WHERE fl_alumno=$fl_usuario AND fl_programa_sp=$fl_programa_sp ";
  else
    $Query = "SELECT fe_periodo_final FROM  k_periodo_trial_curso_alumno WHERE fl_alumno=$fl_usuario AND fl_programa_sp=$fl_programa_sp ";

  $row = RecuperaValor($Query);
  $p_fecha = $row['fe_periodo_final'];
  if (!empty($p_fecha))
    $no_dias_restantes = CalculaDiasRestantesFechaDeterminada($p_fecha);





  if ($no_dias_restantes < 0) {
    $no_dias_restantes = 0;
    $no_dias = "<br/><span><small class='text-muted' style='color:#a90329;'>$no_dias_restantes " . ObtenEtiqueta(2107) . "</small></span>";
  } else {

    $no_dias = "<br/><span><small class='text-muted'>$no_dias_restantes " . ObtenEtiqueta(2107) . " </small></span>";
  }


  return $no_dias;
}

/**
 * MJD ##funcion para saber si este curso fue liberao por metodo de envio de email).
 * @param 
 * 
 */
function DesbloqueadoPorPagoCurso($fl_usuario, $fl_programa_sp)
{

  $Query = "SELECT fl_plan_curso_alumno FROM k_plan_curso_alumno WHERE fl_alumno=$fl_usuario AND fl_programa_sp=$fl_programa_sp ";
  $row = RecuperaValor($Query);

  if ($row[0])
    $fg_tiene_plan = true;
  else
    $fg_tiene_plan = false;

  return $fg_tiene_plan;
}

/**
 * MJD ##funcion para saber si este curso es el primero de la serie.).
 * @param 
 * 
 */
function VerificaPoderDesbloquearCursoSerie($fl_usuario, $fl_programa_sp)
{


  $Query = "SELECT c.fl_programa_sp,c.nb_programa FROM k_relacion_programa_sp k, c_programa_sp c WHERE k.fl_programa_sp_act = $fl_programa_sp AND k.fl_programa_sp_rel = c.fl_programa_sp  AND fg_puesto = 'SIG' ";
  $rs = EjecutaQuery($Query);

  #Recupermaos el numero maximo para desbloquear u curso pertenciente ala serie.
  $no_permitido = ObtenConfiguracion(129);

  for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
    $fl_programa_sp_actual = $row[0];

    if (($i <= $no_permitido) && ($fl_programa_sp == $fl_programa_sp_actual)) {

      $fg_puede_desbloquear = true;
      break;
    }
  }


  return $fg_puede_desbloquear;
}

/**
 * MJD ##funcion para saber si se puede desbloquear curso por metodo de invitar a compradres.; verifica varios requisitos
 * @param 
 * 
 */


function VerificaBotonParaDesbloquearCursoPorMetodoEnvioEmail($fl_usuario, $fl_programa_sp)
{


  #1. Verificamos  si se encuentra dentro del rango permitido del  curso de la serie. 1er requisito para poder enviar invitaciones
  $fg_desbloquear_curso_serie = VerificaPoderDesbloquearCursoSerie($fl_usuario, $fl_programa_sp);

  if ($fg_desbloquear_curso_serie) {

    #2. Recuperamos el numero maximo permitido para poder desbloquear un curso por el metodo de envio de email.
    $no_maximo_desbloqueo_cursos_por_metodo_email = ObtenConfiguracion(126);

    #3. Ahora Verificamos cuantos cursos se han desbloqueado por el metodo de enviode  email.
    $no_cursos_desbloqueados_por_envio_email = CuentaNoCursosPorMetodoEnvioEmails($fl_usuario, $fl_programa_sp);

    #4. Verificamos si el periodo de prueba sigue activo para este curso.
    $fg_activo = FAMEVerificaFechaExpiracionTrialCursoAlumno($fl_usuario, $fl_programa_sp);
    if ($no_cursos_desbloqueados_por_envio_email < $no_maximo_desbloqueo_cursos_por_metodo_email)
      $puede_desbloquear = 1;

    if ($no_cursos_desbloqueados_por_envio_email < $no_maximo_desbloqueo_cursos_por_metodo_email) {

      if ($fg_activo) {
        $fg_desbloquear = true;
      } else {

        if ($puede_desbloquear)
          $fg_desbloquear = true;
        else
          $fg_desbloquear = false;
      }
    } else {
      $etq = ObtenEtiqueta(2110); #Ya supero el limite para envir por este metodo.
      $fg_desbloquear = false;
    }
  } else {

    $etq = ObtenEtiqueta(2109); #Solo puedes desbloquear el N. numero de la serie
    $etq = str_replace("#no_series#", ObtenConfiguracion(129), $etq);

    $fg_desbloquear = false;
  }





  #Verificamos la cantidad permitida de envio de email. 
  $Query = "SELECT no_email_desbloquear FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
  $row = RecuperaValor($Query);
  $no_email_desbloquear = $row['no_email_desbloquear'];

  if (empty($no_email_desbloquear)) {
  }





  return $fg_desbloquear . "#" . $etq;
}


/**
 * MJD ##funcion para traesns l informacion de tarjeta de pago en FAME Desbloqueo de cursos.
 * @param 
 * 
 */


function FAMEVerificaInformacionTarjetaCredito($fl_usuario)
{

  $Query = "SELECT no_tarjeta,ds_tipo,fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta,id_cliente_stripe,ds_email_stripe FROM k_alumno_tarjeta WHERE fl_usuario=$fl_usuario ";
  $row = RecuperaValor($Query);
  $no_tarjeta = $row[0];
  $ds_tipo = $row[1];
  $fe_mes_expiracion = $row[2];
  $fe_anio_expiracion = $row[3];

  if ($no_tarjeta) {
    $info_tarjeta = $no_tarjeta . "," . $ds_tipo . "," . $fe_mes_expiracion . "," . $fe_anio_expiracion;
  } else {
    $info_tarjeta = "";
  }

  return $info_tarjeta;
}



/**
 * MJD ##funcion para traer la informacion de tarjeta de pago en FAME Billing Instituto.
 * @param 
 * 
 */


function FAMEVerificaInformacionTarjetaCreditoBilling($fl_instituto)
{

  $Query = "SELECT no_tarjeta,ds_tipo,fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta,id_cliente_stripe,ds_email_stripe FROM k_current_plan WHERE fl_instituto=$fl_instituto   ";
  $row = RecuperaValor($Query);
  $no_tarjeta = $row[0];
  $ds_tipo = $row[1];
  $fe_mes_expiracion = $row[2];
  $fe_anio_expiracion = $row[3];

  if ($no_tarjeta) {
    $info_tarjeta = $no_tarjeta . "," . $ds_tipo . "," . $fe_mes_expiracion . "," . $fe_anio_expiracion;
  } else {
    $info_tarjeta = "";
  }

  return $info_tarjeta;
}


/**
 * MJD ##funcion para saber el asunto del email a mandar , solo recupra el nombre del template.
 * @param 
 * 
 */

function FAME_ObtenAsuntoEmail($fl_template)
{

  $Query = "SELECT nb_template FROM k_template_doc WHERE fl_template=$fl_template ";
  $row = RecuperaValor($Query);
  $nb_template = str_texto($row['nb_template']);

  return $nb_template;
}



/**
 * MJD ##funcion eleimnar los archivos para export moodle.
 * @param 
 * 
 */
function deleteDirectory($dir)
{
  if (!$dh = @opendir($dir)) return;
  while (false !== ($current = readdir($dh))) {
    if ($current != '.' && $current != '..') {
      // echo 'Se ha borrado el archivo '.$dir.'/'.$current.'<br/>';
      if (!@unlink($dir . '/' . $current))
        deleteDirectory($dir . '/' . $current);
    }
  }
  closedir($dh);
  //echo 'Se ha borrado el directorio '.$dir.'<br/>';
  @rmdir($dir);
}



/**
 * MJD ##funcion para saber si esta permitido que se vea el export moodle.
 * @param 
 * 
 */
function VerBotonExportMoodle($fl_instituto)
{

  $Query = "SELECT fg_export_moodle FROM c_instituto WHERE fl_instituto=$fl_instituto  ";
  $row = RecuperaValor($Query);

  if (!empty($row[0]))
    $fg_mostrar = true;
  else
    $fg_mostrar = false;



  return $fg_mostrar;
}

/**
 * MJD ##funcion para saber la profesion del usuario FAME.
 * @param 
 * 
 */
function FAMEObtenProfesionUsuario($fl_usuario, $fl_perfil = '')
{


  # Busca dependiendo el usuario.
  switch ($fl_perfil) {
    case "" . PFL_ESTUDIANTE_SELF . "":
      $tabla = "c_alumno_sp";
      $fl_user = "fl_alumno_sp";
      break;
    case "" . PFL_ADMINISTRADOR . "":
      $tabla = "c_administrador_sp";
      $fl_user = "fl_adm_sp";
      break;
    case "" . PFL_MAESTRO_SELF . "":
      $tabla = "c_maestro_sp";
      $fl_user = "fl_maestro_sp";
      break;
    default:
      $tabla = NULL;
      $fl_user = NULL;
      break;
  }



  $Query = "SELECT ds_profesion FROM $tabla WHERE $fl_user = $fl_usuario  ";
  $row = RecuperaValor($Query);
  $ds_profesion = str_texto(!empty($row[0])?$row[0]:NULL);
  if (empty($ds_profesion))
    $ds_profesion = "";

  return $ds_profesion;
}



/**
 * MJD ##funcion para saber la Compania del usuario FAME.
 * @param 
 * 
 */
function FAMEObtenCompaniaUsuario($fl_usuario, $fl_perfil = '')
{


  # Busca dependiendo el usuario.
  switch ($fl_perfil) {
    case "" . PFL_ESTUDIANTE_SELF . "":
        $tabla = "c_alumno_sp";
        $fl_user = "fl_alumno_sp";
        break;
    case "" . PFL_ADMINISTRADOR . "":
        $tabla = "c_administrador_sp";
        $fl_user = "fl_adm_sp";
        break;
    case "" . PFL_MAESTRO_SELF . "":
        $tabla = "c_maestro_sp";
        $fl_user = "fl_maestro_sp";
        break;
    default:
        $tabla = NULL;
        $fl_user = NULL;
        break;
  }

  $Query = "SELECT ds_compania FROM $tabla WHERE $fl_user = $fl_usuario  ";
  $row = RecuperaValor($Query);
  $ds_compania = str_texto(!empty($row[0])?$row[0]:NULL);
  if (empty($ds_compania)) {
    $p_instituto = ObtenInstituto($fl_usuario);
    $ds_compania = str_texto(ObtenNameInstituto($p_instituto));
  }




  return $ds_compania;
}





function FAMETinyMCE($p_nombre, $p_valor)
{


  $ds_clase = "MCE_" . $p_nombre;
  $lang = "en";

  echo "<textarea class='$ds_clase' id='$p_nombre' name='$p_nombre' cols='5' rows='5' ";
  echo " placeholder='Text area...' >$p_valor</textarea>";

  $IMG_FILE_MANAGER = PATH_SELF_JS . "/ckeditor/ckeditor";
  echo "<script>
      CKEDITOR.replace( '$p_nombre' ,{ 
      // Rutas para file manager
      filebrowserBrowseUrl : '" . $IMG_FILE_MANAGER . "/responsive_filemanager/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', 
      filebrowserUploadUrl : '" . $IMG_FILE_MANAGER . "/responsive_filemanager/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', 
      filebrowserImageBrowseUrl : '" . $IMG_FILE_MANAGER . "/responsive_filemanager/filemanager/dialog.php?type=1&editor=ckeditor&fldr=',
      language: '" . $lang . "'
      });
    </script>";
}


function FAMESelectMultiple($p_etiqueta, $id_name, $Query, $valor_actual = '', $p_etiqueta2 = '', $p_requerido, $p_clase2)
{

  if ($p_requerido)
    $asterisco = "*";
  else
    $asterisco = "";

  echo "

        <div class='form-group'>
            <label class='col-sm-2 control-label'><strong>$asterisco $p_etiqueta</strong></label>
			<div class='col-sm-8'>
			<select multiple style='width:100%' class='select2 $p_clase2' id='$id_name' name='$id_name' >
			";

  $rs = EjecutaQuery($Query);
  while ($row = RecuperaRegistro($rs)) {
    echo "<option value=\"$row[1]\" ";
    if ($p_actual == $row[1])
      echo " selected";

    # Determina si se debe elegir un valor por traduccion
    $etq_campo = DecodificaEscogeIdiomaBD($row[0]);
    echo ">$etq_campo</option>\n";
  }
  echo "													
			</select>
				
			<div class='note'>
				$p_etiqueta2
			</div>
            </div>
		</div>

    ";


  # Si el select es multiple recibimos diferentes valores
  if (!empty($valor_actual)) {
    echo "
                    <script>
                    $(document).ready(function(){
                        $(\".$p_clase2\").select2(\"val\", [";
    for ($k = 0; $k < count($valor_actual); $k++) {
      //if($valor_actual[$k])
      echo "\"" . $valor_actual[$k] . "\",";
    }
    echo "]);});
                    </script>";
  }
}

function FAMEFile($p_etiqueta = '', $id_name = '', $p_valor_actual = '', $ds_ruta = '')
{



  
    echo"<div id='img_mike_preview'>";
    if ($p_valor_actual) {

    /*echo "
		  <a class='zoomimg' href='javascript:void();'>
                <img id='img_1_$id_name' name='img_1_$id_name' src='$ds_ruta'  style='width: 14%' >
                <span style='left:-300px;'>
					<div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-top:-540px'>
					<div class='modal-content' style='width:500px;height:500px;'>
						<div class='modal-body padding-5'  style='width:500px;height:500px;'>
						<img class='superbox-current-img' id='img_2_$id_name' name='img_2_$id_name' src='$ds_ruta' style='width:494px;height:490px;'>
						</div>
					</div>
					</div>
				</span>
         </a>
		";*/
      echo"
           <a class='thumbnail' style='margin:auto;border: solid 0px; background: transparent;margin-left: -33px;' data-toggle='popover' href='javascript:void(0);' data-placement='top' id='img_2_1t' name='img_2_1t' data-full='$ds_ruta'>  <img id='img_1_1t' name='img_1_1t' src='$ds_ruta' style='width: 50%'>  </a> 
         ";


  }
    echo"
            <script>
                 // zoom thumbnails and add bootstrap popovers
	                // https://getbootstrap.com/javascript/#popovers
	                $('[data-toggle=\"popover\"]').popover({
	                container: 'body',
	                html: true,
	                placement: 'auto',
	                trigger: 'hover',
	                content: function() {
		                // get the url for the full size img
		                var url = $(this).data('full');
		                return '<img src=\"'+url+'\" style=\"max-width:250px;\">'
	                }
	                });



           </script>

    ";
    echo"   </div>";
  echo "<div class='smart-form'>
			<section>
				<label class='label'><b>" . $p_etiqueta . "</b></label>
				<div class='input input-file'>
					<span class='button'><input type='file' id='" . $id_name . "' name='" . $id_name . "' onchange='this.parentNode.nextSibling.value = this.value'>Browse</span><input type='text' placeholder='' readonly=''>
                    <input type='hidden' id='nb_thumb_load' name='nb_thumb_load' value='$p_valor_actual' />				
                </div>
			</section>
		</div>
    ";
}


function FAMECampoSelectBD(
  $p_etiqueta = '',
  $p_nombre,
  $p_query,
  $p_actual,
  $p_clase = 'select2',
  $p_seleccionar = False,
  $p_script = '',
  $p_valores = '',
  $p_seleccionar_txt = 'Select',
  $p_seleccionar_val = 0,
  $p_option_extra = ""
) {


  if (!empty($p_requerido))
    $asterisco = "*";
  else
    $asterisco = "";

  echo " <label class='label'><b>$asterisco $p_etiqueta</b></label>";
  echo "<select id='$p_nombre' name='$p_nombre' class='" . $p_clase . "'";
  if (!empty($p_script)) echo " $p_script";
  echo ">\n";
  if ($p_seleccionar)
    echo "<option value=" . $p_seleccionar_val . " data-id='" . $p_seleccionar_val . "'>" . $p_seleccionar_txt . "</option>\n";
  $rs = EjecutaQuery($p_query);
  while ($row = RecuperaRegistro($rs)) {
    echo "<option value=\"$row[1]\"";
    if ($p_actual == $row[1])
      echo " selected";

    # Determina si se debe elegir un valor por traduccion
    $etq_campo = DecodificaEscogeIdiomaBD($row[0]);
    echo " data-fulltext='" . (!empty($row[2])?$row[2]:NULL) . "'>$etq_campo</option>\n";
  }
  if (!empty($p_option_extra))
    echo $p_option_extra;
  echo "</select>";
  # Si el select es multiple recibimos diferentes valores
  if (!empty($p_valores)) {
    echo "    
                      <script>
                          $(document).ready(function(){
                            $(\".select2\").val([";
    for ($k = 0; $k < count($p_valores); $k++) {
      echo "\"$p_valores[$k]\",";
    }
    echo "
                          ]).select2();
                          });
                     </script>";
  }
}






function FAMESelectSimple($p_etiqueta = '', $id_name, $p_opc, $p_val, $p_actual, $p_requerido, $p_seleccionar = '')
{

  $tot = count($p_opc);

  echo " <label class='label'><b>$p_etiqueta</b></label>";

  echo "<select id='$id_name' name='$id_name' class='select2'";
  //if(!empty($p_script)) echo " $p_script";
  echo ">\n";
  if ($p_seleccionar)
    echo "<option value=0>" . ObtenEtiqueta(70) . "</option>\n";
  for ($i = 0; $i < $tot; $i++) {
    echo "<option value=\"$p_val[$i]\"";
    if ($p_actual == $p_val[$i])
      echo " selected";
    echo ">$p_opc[$i]</option>\n";
  }
  echo "</select>
    <div class='note hidden ' id='" . $id_name . "_texto_error' style='color:#A90329;'>This is a required field.</div>
    ";
}

function FAMECheckBox($p_etiqueta = '', $id_name, $p_valor_actual, $p_requerido, $p_descripcion = '')
{

  if ($p_valor_actual)
    $checked = "checked ";
  else
    $checked = "";


  if ($p_requerido)
    $asterisco = "*";
  else
    $asterisco = "";

  echo "<section>
            
			    <label class='checkbox state-success'>
                    <input type='checkbox' name='checkbox' id='$id_name' $checked ><i></i><b>$asterisco $p_etiqueta</b>
    ";
  if ($p_descripcion)
    echo "<br><small class='note'>" . $p_descripcion . "</small>";
  echo "
            </label>
            <div class='note hidden ' id='" . $id_name . "_texto_error' style='color:#A90329;'>" . ObtenEtiqueta(2350) . "</div>
         </section> 
       ";
}


function FAMEInputText($p_etiqueta = '', $id_name, $p_valor_actual = '', $p_requerido = '', $p_script = '', $p_holder = '', $disabled = '', $onkeyup = '')
{

  if ($p_requerido)
    $asterisco = "*";
  else
    $asterisco = "";


  echo "<section>
			<label class='label'><b>$asterisco $p_etiqueta</b></label>
			<label class='input' id='" . $id_name . "_input_error'>
               
				<input type='text' placeholder='$p_holder' onkeyup=\"myFunction_$id_name();$onkeyup\" $p_script name='".$id_name."'  id='".$id_name."'  value='" . $p_valor_actual . "' " . $disabled . " >
			</label>
			<div class='note  hidden' id='" . $id_name . "_texto_error' style='color:#A90329;'>This is a required field.</div>
		</section>";


  if ($p_requerido) {

    #Pintamos los inputs de verde     
    echo "
<script>


function myFunction_$id_name(){
  // alert('entro');
    var tiene_texto=document.getElementById('$id_name').value; 
    if(tiene_texto != ''){
        $('#" . $id_name . "_input_error').removeClass('state-error');
        $('#" . $id_name . "_input_error').addClass('state-success');
        $('#" . $id_name . "_texto_error').addClass('text-danger');
    }else{
 
       $('#" . $id_name . "_input_error').removeClass('state-success');
       $('#" . $id_name . "_input_error').addClass('state-error');
       $('#" . $id_name . "_texto_error').removeClass('text-danger');
    }
}
</script>
    
";
  } else {


    echo "
<script>


function myFunction_$id_name(){
  // alert('entro');
    var tiene_texto=document.getElementById('$id_name').value; 
    if(tiene_texto != ''){
        $('#" . $id_name . "_input_error').removeClass('state-error');
        $('#" . $id_name . "_input_error').addClass('state-success');
    }else{
       $('#" . $id_name . "_input_error').removeClass('state-error');
       $('#" . $id_name . "_input_error').addClass('state-success');
       
    }
}
</script>
    
";
  }
}




/**
 * MJD #función para mostrar datos generales del usuario FAME Feed.  el fg_btn_seguir=muestra opciones para seguir follower  el origen , es de donde proviene. del profile.php , fg_post_abrir_modal=viede del feed cuando abren modal para ver el post.
 * @param    
 * 
 */
function MuestraPerfilFeed($fl_usuario, $fl_perfil, $fl_usuario_logueado = '', $fe_post = '', $id_modal = '', $fg_btn_seguir = '', $fg_origen = '', $fg_feed = '', $fg_origen_post_modal = '',$fg_post_abrir_modal='')
{

  if (!empty($fl_usuario_logueado)) {
    $nb_usuario = ObtenNombreUsuario($fl_usuario, $fl_usuario_logueado);
  } else {
    $nb_usuario = ObtenNombreUsuario($fl_usuario);
  }

  $ds_profesion = FAMEObtenProfesionUsuario($fl_usuario, $fl_perfil);
  $ds_compania = FAMEObtenCompaniaUsuario($fl_usuario, $fl_perfil);
  $ruta_avatar = ObtenAvatarUsuario($fl_usuario);

  if ($fg_feed == 1)
    $style = "margin-top:-21px;";
  else
    $style = "";


  if ($fl_usuario <> $fl_usuario_logueado) {

    if ($fg_btn_seguir == 1) {

      $Query = "SELECT fl_followers FROM c_followers WHERE fl_usuario_destino=$fl_usuario AND fl_usuario_origen=$fl_usuario_logueado ";
      $rwo = RecuperaValor($Query);
      $fl_followers = !empty($rwo['fl_followers'])?$rwo['fl_followers']:NULL;

      if (!empty($fl_followers)) {
        $icono = "<span class='follow_" . $fl_usuario_logueado . "_" . $fl_usuario . "' ><i class='fa fa-check-square-o height_user' style='$style; cursor:pointer;float:right;color:rgba(0,0,0,.6);' onclick='Unfollow($fl_usuario_logueado,$fl_usuario)' aria-hidden='true'></i></span>";
      } else {
        $icono = "<span class='follow_" . $fl_usuario_logueado . "_" . $fl_usuario . "' ><i class='fa fa-user-plus height_user' style='$style; cursor:pointer;float:right;color:rgba(0,0,0,.6);' onclick='Follow($fl_usuario_logueado,$fl_usuario);' aria-hidden='true'></i></span>";
      }
    }
  }
    if (!empty($fl_followers)) {
        $icono = "<span class='follow_" . $fl_usuario_logueado . "_" . $fl_usuario . "' ><i class='fa fa-check-square-o height_user' style='$style; cursor:pointer;float:right;color:rgba(0,0,0,.6);' onclick='Unfollow($fl_usuario_logueado,$fl_usuario)' aria-hidden='true'></i></span>";
    } else {
        $icono = "<span class='follow_" . $fl_usuario_logueado . "_" . $fl_usuario . "' ><i class='fa fa-user-plus height_user' style='$style; cursor:pointer;float:right;color:rgba(0,0,0,.6);' onclick='Follow($fl_usuario_logueado,$fl_usuario);' aria-hidden='true'></i></span>";
    }

  #Cuando viene de un modal temos que cerrar ese modal.
  if (!empty($id_modal))
    $fg_cerrar_modal = "CerrarModal_$fl_usuario();";
  else
    $fg_cerrar_modal = "";


  if (!empty($id_modal)) {
    echo "
	<script>
  	function CerrarModal_$fl_usuario(){
  	   $('#$id_modal').modal('hide');
  	}
    </script>
    ";
  }
  echo "
  <script>
	 function View_$fl_usuario(){
        window.location.assign('".ObtenConfiguracion(116)."/fame/index.php#site/myprofile.php?profile_id=".$fl_usuario."&c=1&uo=".$fl_usuario_logueado . "');
        // Por x razon la galeria al visitar el perfil se va cargando conforme haces el scroll, pero una vez visitada siempre toma como index=20  , esto funciona perfectamente cuando visitas un perfil por primera vez , pero cuando vas sucesivamente el index se queda en 20 lo cual el query marca un error, para evitar esto se agrega la fucnion del reload.Saludos amigos. 
        window.location.reload(true);

    }
	</script>
	";
  //para poder ajustar los difernetes tamaños.
  if ($fg_origen == 1) {
    $col_md = "col-md-10 col-lg-10";
  } else {
    $col_md = "col-md-6 col-lg-6";
  }


  if ($fg_origen_post_modal == 1) {
    $col_avatar = "col-md-1 col-lg-1 text-center";
    $float = "";
  } else {
    $col_avatar = "col-md-2 col-lg-2";
    $float = "float: left;";
  }
  echo "
	<div class=\"margin\">
		<div class='col-sm-12 $col_avatar 'style='padding:3px;'>
			<img src='$ruta_avatar' alt='img' style='$float height: 50px;margin-right: 10px;width: 50px;' >
        </div>
		<div class='col-sm-12 $col_md'>
			<span class=\"name\" style='font-size: 15px !important;color:#57889c;'><b><a    href='javascript:void(0)'  onclick=\"View_$fl_usuario(),".$fg_cerrar_modal."\" > " . $nb_usuario . "</a></b> </span>
	";
  if(empty($fg_post_abrir_modal)){
      echo "$icono";
  }
  echo "	
			<p><span class=\"from\" style='font-size: 12px;opacity: .7;color:#333;'>" . $ds_profesion . " - " . $ds_compania . "</span></p>\n ";

  if (!empty($fe_post)) {
    echo "		
				 <span class=\"from\" style='font-size: 12px;opacity: .7;color:#333;'>" . $fe_post . "</span>\n";
  }

  echo "</div>
		</div>";
}


/**
 * MJD #función para saber si puede enviar la notificaciones del Fedd. 
 * @param    
 * 
 */
function VerificaPermisoEnvioEmail($fl_usuario, $fg_notificacion)
{



  $Query = "SELECT $fg_notificacion FROM k_notify_fame_feed WHERE fl_usuario=$fl_usuario ";
  $rof = RecuperaValor($Query);
  $fg_permiso = !empty($rof[0])?$rof[0]:NULL;


  return $fg_permiso;
}
/**
 * radio buttons. 
 * @param    
 * 
 */
function FAMECampoRadio($p_nombre, $p_valor, $p_actual, $p_texto='', $p_editar=True, $p_script=''){
	echo "<input type='radio' id='$p_nombre' name='$p_nombre' value='$p_valor'";
  if($p_valor == $p_actual)echo " checked";
  if($p_editar == False) echo " disabled=disabled";
  if(!empty($p_script)) echo " $p_script";
  echo "> <i></i>$p_texto";
}


/**
 * dropzone quiz. 
 * @param    
 * 
 */
Function FAMECargaImagenDropZone($p_titulo, $p_id, $no_tab, $editar=False, $clave = 0, $fg_error = 0, $ds_img_err = "", $fg_tipo_img = "", $p_script="", $p_tipo_resp="T"){

  $ord = substr("{$p_id}", 11, 1);
  $ds_resp_img = "ds_resp_img_".$ord."_".$no_tab;
  echo "<div class='row'>";

    echo "<label class='col col-sm-12 control-label text-align-left'>";
      echo "<strong>{$p_titulo}</strong>";
        $row = RecuperaValor("SELECT r.ds_respuesta, p.fg_posicion_img FROM k_quiz_respuesta r, k_quiz_pregunta p WHERE r.fl_quiz_pregunta = $clave AND r.no_orden = $ord AND r.no_tab = $no_tab AND r.fl_quiz_pregunta = p.fl_quiz_pregunta");
        $ds_respuesta = str_texto(!empty($row[0])?$row[0]:NULL);
        $fg_posicion_img = str_texto(!empty($row[1])?$row[1]:NULL);
        
        if((empty($ds_respuesta))&&($p_tipo_resp=="I")){
            $ds_respuesta=$ds_img_err;

        }



        if($fg_posicion_img == "L"){
          $tam_gd_w = "330px";
          $tam_gd_h = "180px";
          $tam_sm_w = "50px;";
          $tam_sm_h = "30px;";
          $padding  = "185px;";
         }
        else{
          $tam_gd_w = "180px";
          $tam_gd_h = "330px";
          $tam_sm_w = "30px;";
          $tam_sm_h = "50px;";
          $padding  = "335px";
        }
        #colocamos la imagen previa cuando la quiz ya lo tiene.
        if(!empty($ds_img_err)){
            echo"
          <a class='thumbnail' style='margin:auto;border: solid 0px; background: transparent;' data-toggle='popover' href='javascript:void(0);' data-placement='top' data-full='../../AD3M2SRC4/modules/fame/uploads/".$ds_img_err."'>  <img src='../../AD3M2SRC4/modules/fame/uploads/".$ds_img_err."'  style='width:$tam_sm_w;height:$tam_sm_h;'  >  </a>";
        }

       /* require ''.PATH_MODULOS.'/campus/preview.inc.php';
        $ruta = PATH_MODULOS."/fame/uploads";       
        $t = $ord."_".$no_tab;
        $padding = "style='padding-top:3px;'";
      echo '
      <a class=\'zoomimg\' href=\'#\'>
        <img src=\''.$ruta.'/'.$ds_respuesta.'\' id=\''.$t.'\' class=\'away no-border\' width=\''.$tam_sm_w.'\' height=\''.$tam_sm_h.'\'>
        <span id=\'div_1_'.$t.'\' style=\'left:-75px; width:'.$tam_gd_w.'; height:'.$tam_gd_h.';\'>
          <div id=\'div_2_'.$t.'\' class=\'modal-dialog demo-modal\' style=\'width:'.$tam_gd_w.'; height:'.$tam_gd_h.';\'>
            <div id=\'div_3_'.$t.'\'class=\'modal-content\' style=\'width:'.$tam_gd_w.'; height:'.$tam_gd_h.'; padding-bottom:'.$padding.';\'>
              <div class=\'modal-body padding-5\'>
                <img class=\'superbox-current-img\' src=\''.$ruta.'/'.$ds_respuesta.'\' id=\'2_'.$t.'\'>
                <br>
              </div><br>
            </div>
          </div>
        </span>
      </a>';
	  */
      if($p_tipo_resp=="T"){
        $ds_respuesta = "";
      }
      
      if(empty($clave)){
		  echo"<input type='hidden' id='nb_img_prev_{$p_id}' name='nb_img_prev_{$p_id}' value='$ds_img_err' > ";  
      }else{
		  echo"<input type='hidden' id='nb_img_prev_{$p_id}' name='nb_img_prev_{$p_id}' value='$ds_respuesta' > ";     
      }

      if($fg_error){        
       $t = $ord."_".$no_tab;
        $img = $ruta."/".$ds_img_err;
        echo "<script>
          var fg_tipo_img = '$fg_tipo_img';
          if(fg_tipo_img){
            document.getElementById('$t').src = '$img';
            document.getElementById('2_$t').src = '$img';
            document.getElementById('nb_img_prev_mydropzone_$t').value = '$ds_img_err';  
            logo = document.getElementById('$t');
            div_1 = document.getElementById('div_1_$t');
            div_2 = document.getElementById('div_2_$t');
            div_3 = document.getElementById('div_3_$t');
            if(fg_tipo_img == 'P'){
              div_1.style.width = '180px';
              div_1.style.height = '330px';
              div_2.style.width = '180px';
              div_2.style.height = '330px';
              div_3.style.width = '180px';
              div_3.style.height = '330px';
              logo.width = 30;
              logo.height = 50;  
            }else{
              div_1.style.width = '330px';
              div_1.style.height = '180px';
              div_2.style.width = '330px';
              div_2.style.height = '180px';
              div_3.style.width = '330px';
              div_3.style.height = '180px';
              logo.width = 50;
              logo.height = 30;  
            }
          }
        
        </script>";
		
      }
      
    echo "</label>";
    echo "<div class='col col-sm-12' {$padding}>";
      echo "<div data-widget-editbutton='false'><!-- class='jarviswidget jarviswidget-color-blueLight' -->";
        echo "<div>";
          echo "<div class='widget-body'>";
            echo "<div class='dropzone' id='{$p_id}' style='min-height: 120px; padding:10px 0px 0px 20px'></div>";
          echo "</div>";
        echo "</div>";
      echo "</div>";
    echo "</div>";
  echo "</div>";
  
 
  echo "<script type='text/javascript'>
    // DO NOT REMOVE : GLOBAL FUNCTIONS!
    $(document).ready(function() {
 //     pageSetUp();
      Dropzone.autoDiscover = false;
      var clave=document.getElementById('clave').value;
      $('#{$p_id}').dropzone({
        url: '../../AD3M2SRC4/modules/fame/upload.php?ord={$ord}&no_tab={$no_tab}&editar={$editar}&clave='+clave,
        // data:  'id=1',
        addRemoveLinks : true,
        maxFilesize: 1024,
        // Solo permite guardar un registro
        maxFiles: 1,        
        acceptedFiles: 'image/*,.jpeg,.jpg,.png,.JPEG,.JPG,.PNG',
        init: function() {
          this.on('error', function(file, message) { 
          alert('".ObtenEtiqueta(1239)."');
          this.removeFile(file); 
          });
        },        
        success: function(file, result){
          var message, status, name;
          message = JSON.parse(result);
          status = message.valores.status;
          name = message.valores.file_name;
          if(status==true){
            $('#nb_img_prev_{$p_id}').val(name);
            {$p_script}
          }
        },
        removedfile: function(file) {
          file.previewElement.remove();
          $('#nb_img_prev_{$p_id}').val('');
          {$p_script}
        }
      });
    })
  </script>";
  
}


/**
 * MJD #Saber si el curso creado por un instituto puede ser compartido a las demas scuelas. 
 * @param    
 * 
 */
 function Share_Course($fl_programa_sp,$fl_instituto_programa='',$fl_instituto_logueado=''){

     #Aplica para cursos default creados por vanas
     if((empty($fl_instituto_programa))||($fl_instituto_programa=='')){
             $fg_compartir_curso=1; 

     }else{

         if($fl_instituto_programa==$fl_instituto_logueado){       
             $fg_compartir_curso=1;        

         }else{

             #Verifica el permiso para poder ser compartido.
             $Query="SELECT fg_compartir_curso FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
             //$Query.="AND fl_instituto=$fl_instituto_programa ";
             $row=RecuperaValor($Query);
             $fg_compartir_curso=$row[0];

             if(empty($fg_compartir_curso)){
                 $fg_compartir_curso=0;
             }

         }


     }

	 return $fg_compartir_curso;
 }
