<?php

# Definicion de librerias para Sitios de Alumnos y Maestros
// require($_SERVER['DOCUMENT_ROOT'].'/vanas/lib/com_func.inc.php');
// require($_SERVER['DOCUMENT_ROOT'].'/vanas/lib/sp_config.inc.php');
require($_SERVER['DOCUMENT_ROOT'].'/lib/com_func.inc.php');
require($_SERVER['DOCUMENT_ROOT'].'/lib/sp_config.inc.php');
require('cam_layout.inc.php');

# New campus libraries
// require($_SERVER['DOCUMENT_ROOT'].'/vanas/modules/common/new_campus/lib/cam_layout.inc.php');
// require($_SERVER['DOCUMENT_ROOT'].'/vanas/modules/common/new_campus/lib/cam_util_func.inc.php');
require($_SERVER['DOCUMENT_ROOT'].'/modules/common/new_campus/lib/cam_util_func.inc.php');
require($_SERVER['DOCUMENT_ROOT'].'/modules/common/new_campus/lib/cam_layout.inc.php');

#
# Funciones Generales
#

# Validacion de seguridad
function ValidaPermisoCampus($p_funcion) {
  
  # Lee la sesion del cookie
  $cl_sesion = isset($_COOKIE[SESION_RM])?$_COOKIE[SESION_RM]:NULL;
  if(empty($cl_sesion))
    $cl_sesion = $_COOKIE[SESION_CAMPUS];
  
  # Verifica que existe la sesion
  if(empty($cl_sesion))
    return False;
  
  # Recupera el usuario y su perfil
  $row = RecuperaValor("SELECT fl_usuario, fl_perfil FROM c_usuario WHERE cl_sesion='$cl_sesion'");
  $fl_usuario = $row[0];
  $fl_perfil = $row[1];
  
  # Verifica que existe el usuario
  if(empty($fl_usuario))
    return False;
  
  # Verifica si es el Administrador
  if($fl_usuario == ADMINISTRADOR)
    return True;
  
  # Revisa si es una funcion para Alumnos
  if($p_funcion == FUNC_ALUMNOS AND $fl_perfil == PFL_ESTUDIANTE)
    return True;
  
  # Revisa si es una funcion para Maestros
  if($p_funcion == FUNC_MAESTROS AND $fl_perfil == PFL_MAESTRO)
    return True;
  
  # Caso no esperado
  return False;
}

# Recupera la fecha actual de la base de datos
function ObtenFechaActual($p_display=False) {
  
  # Revisa si se debe usar una fecha para debug o la fecha actual
  $fg_degub = ObtenConfiguracion(21);
  $diferencia = RecuperaDiferenciaGMT( );
  if($fg_degub <> "1") {
    if($p_display) {
      $Query  = "SELECT DATE_FORMAT((DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
      $Query .= "DATE_FORMAT((DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $diferencia HOUR)), '%e, %Y') 'fe_dia_anio'";
      $row = RecuperaValor($Query);
      $fe_actual = ObtenNombreMes($row[0])." ".$row[1];
    }
    else {
      $row = RecuperaValor("SELECT CURRENT_TIMESTAMP");
      $fe_actual = $row[0];
    }
  }
  else {
    $fe_actual = ObtenConfiguracion(22);
    if($p_display) {
      $Query  = "SELECT DATE_FORMAT((DATE_ADD('$fe_actual', INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
      $Query .= "DATE_FORMAT((DATE_ADD('$fe_actual', INTERVAL $diferencia HOUR)), '%e, %Y') 'fe_dia_anio'";
      $row = RecuperaValor($Query);
      $fe_actual = ObtenNombreMes($row[0])." ".$row[1];
    }
  }
  return $fe_actual;
}

# Recupera la hora actual de la base de datos (Siempre es para despliegue)
function ObtenHoraActual( ) {
  
 # Revisa si se debe usar una fecha para debug o la fecha actual
  $fg_degub = ObtenConfiguracion(21);
  $diferencia = RecuperaDiferenciaGMT( );
  if($fg_degub <> "1") {
    $Query  = "SELECT DATE_FORMAT((DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $diferencia HOUR)), '%l:%i %p') 'fe_actual'";
    $row = RecuperaValor($Query);
    $fe_actual = $row[0];
  }
  else {
    $fe_actual = ObtenConfiguracion(22);
    $row = RecuperaValor("SELECT DATE_FORMAT((DATE_ADD('$fe_actual', INTERVAL $diferencia HOUR)), '%l:%i %p') 'fe_actual'");
    $fe_actual = $row[0];
  }
  return $fe_actual;
}

function EsSupervisor($p_usuario) {
  
  $usr_supervisor = ObtenConfiguracion(40);
  $row = RecuperaValor("SELECT fl_usuario FROM c_usuario WHERE ds_login='$usr_supervisor'");
  if(!empty($row[0]) AND $row[0] == $p_usuario)
    return True;
  
  return False;
}


#
# Funciones para Alumnos y Maestros
#

function ObtenPerfilUsuario($p_usuario) {
  
  # Recupera perfil de un usuario
  $row = RecuperaValor("SELECT fl_perfil FROM c_usuario WHERE fl_usuario='$p_usuario'");
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenNombreUsuario($p_usuario) {
  
  # Recupera matricula del usuario
  $concat = array('ds_nombres', "' '", 'ds_apaterno');
  $row = RecuperaValor("SELECT ".ConcatenaBD($concat)." FROM c_usuario WHERE fl_usuario=$p_usuario");
  return str_uso_normal(!empty($row[0])?$row[0]:NULL);
}

function ObtenAvatarUsuario($p_usuario) {
  
  # Recupera el perfil del usuario
  $fl_perfil = ObtenPerfilUsuario($p_usuario);
  
  # Verifica si el usuario tiene un avatar
  if($fl_perfil == PFL_MAESTRO) {
    $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_maestro WHERE fl_maestro=$p_usuario");
    if(!empty($row[0]))
      $ds_ruta_avatar = PATH_MAE_IMAGES."/avatars/".$row[0];
    else
      $ds_ruta_avatar = SP_IMAGES."/".IMG_T_AVATAR_DEF;
  }
  else {
    $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_alumno WHERE fl_alumno=$p_usuario");
    if(!empty($row[0]))
      $ds_ruta_avatar = PATH_ALU_IMAGES."/avatars/".$row[0];
    else
      $ds_ruta_avatar = SP_IMAGES."/".IMG_S_AVATAR_DEF;
  }
  return $ds_ruta_avatar;
}

function ObtenMatriculaAlumno($p_alumno) {
  
  # Recupera matricula del usuario
  $row = RecuperaValor("SELECT ds_login FROM c_usuario WHERE fl_usuario=$p_alumno");
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenGrupoAlumno($p_alumno) {
  
  # Recupera el grupo del alumno
  $row = RecuperaValor("SELECT fl_grupo FROM k_alumno_grupo WHERE fl_alumno=$p_alumno AND fg_grupo_global<>'1' ");
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenNombreGrupoAlumno($p_alumno) {
  
  # Recupera el grupo del alumno
  $fl_grupo = ObtenGrupoAlumno($p_alumno);
  
  # Recupera el nombre del grupo del alumno
  $row = RecuperaValor("SELECT nb_grupo FROM c_grupo WHERE fl_grupo=$fl_grupo");
  $result = !empty($row[0])?$row[0]:NULL;
  return str_uso_normal($result);
}

function ObtenMaestroAlumno($p_alumno) {
  
  # Recupera el grupo del alumno
  $fl_grupo = ObtenGrupoAlumno($p_alumno);
  
  # Recupera el maestro del grupo
  $row = RecuperaValor("SELECT fl_maestro FROM c_grupo WHERE fl_grupo=$fl_grupo");
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenNombreMaestroAlumno($p_alumno) {
  
  # Recupera el grupo del alumno
  $fl_maestro = ObtenMaestroAlumno($p_alumno);
  
  # Recupera el nombre del maestro del grupo
  return ObtenNombreUsuario($fl_maestro);
}

function ObtenTermAlumno($p_alumno) {
  
  # Recupera el grupo del alumno
  $fl_grupo = ObtenGrupoAlumno($p_alumno);
  
  # Recupera el term del grupo
  $row = RecuperaValor("SELECT fl_term FROM c_grupo WHERE fl_grupo=$fl_grupo");
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenPeriodoAlumno($p_alumno) {
  
  # Recupera el term del alumno
  $fl_term = ObtenTermAlumno($p_alumno);
  
  # Recupera el programa del term
  $row = RecuperaValor("SELECT fl_periodo FROM k_term WHERE fl_term=$fl_term");
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenProgramaAlumno($p_alumno) {
  
  # Recupera el term del alumno
  $fl_term = ObtenTermAlumno($p_alumno);
  
  # Recupera el programa del term
  $row = RecuperaValor("SELECT fl_programa FROM k_term WHERE fl_term=$fl_term");
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenNombreProgramaAlumno($p_alumno) {
  
  # Recupera el term del alumno
  $fl_term = ObtenTermAlumno($p_alumno);
  
  # Recupera el programa del term
  $row = RecuperaValor("SELECT nb_programa FROM c_programa a, k_term b WHERE a.fl_programa=b.fl_programa AND fl_term=$fl_term");
  $result = !empty($row[0])?$row[0]:NULL;
  return str_uso_normal($result);
}

function ObtenTituloLeccion($p_programa, $p_grado, $p_semana) {
  
  # Recupera el titulo de la leccion
  $row = RecuperaValor("SELECT ds_titulo FROM c_leccion WHERE fl_programa=$p_programa AND no_grado=$p_grado AND no_semana=$p_semana");
  $result = !empty($row[0])?$row[0]:NULL;
  return str_uso_normal($result);
}

function ObtenGradoAlumno($p_alumno) {
  
  # Recupera el term del alumno
  $fl_term = ObtenTermAlumno($p_alumno);
  
  # Recupera el grado del term
  $row = RecuperaValor("SELECT no_grado FROM k_term WHERE fl_term=$fl_term");
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenSemanaActualAlumno($p_alumno,$fg_grupo_global="",$fl_grupo="") {
  
  # Recupera datos del alumno
  $fl_term = ObtenTermAlumno($p_alumno);
  $fl_programa = ObtenProgramaAlumno($p_alumno);
  $no_grado = ObtenGradoAlumno($p_alumno);
  $fe_actual = ObtenFechaActual( );
  
  # Recupera la semana mas cercana con respecto a la fecha actual
  
  if($fg_grupo_global==1){
      $Query="
        SELECT MAX(a.no_semana) FROM k_semana_grupo a
        WHERE TO_DAYS(a.fe_publicacion) <= TO_DAYS('$fe_actual')
       AND  fl_grupo=$fl_grupo

        ";

  }else{
      
      $Query  = "SELECT MAX(b.no_semana) ";
      $Query .= "FROM k_semana a, c_leccion b ";
      $Query .= "WHERE a.fl_leccion=b.fl_leccion ";
      $Query .= "AND TO_DAYS(a.fe_publicacion) <= TO_DAYS('$fe_actual') ";
      $Query .= "AND a.fl_term=$fl_term ";
      $Query .= "AND b.fl_programa=$fl_programa ";
      $Query .= "AND b.no_grado=$no_grado";
     
  }
  $row = RecuperaValor($Query);
  $no_semana = $row[0];
  if(empty($no_semana))
    $no_semana = 0;
  
  return $no_semana;
}

function ObtenSemanaMaximaAlumno($p_alumno) {
  
  # Recupera datos del alumno
  $fl_programa = ObtenProgramaAlumno($p_alumno);
  $no_grado = ObtenGradoAlumno($p_alumno);
  
  # Recupera la semana actual
  $Query  = "SELECT MAX(no_semana) ";
  $Query .= "FROM c_leccion ";
  $Query .= "WHERE fl_programa=$fl_programa ";
  $Query .= "AND no_grado=$no_grado";
  $row = RecuperaValor($Query);
  $no_semana = $row[0];
  if(empty($no_semana))
    $no_semana = 0;
  
  return $no_semana;
}

function ObtenFolioSemanaAlumno($p_alumno, $p_no_semana,$fg_grupo_global="",$fl_grupo="") {
  
  # Recupera datos del alumno
  $fl_term = ObtenTermAlumno($p_alumno);
  $fl_programa = ObtenProgramaAlumno($p_alumno);
  $no_grado = ObtenGradoAlumno($p_alumno);
  
  # Recupera la leccion del programa 
  $row = RecuperaValor("SELECT fl_leccion FROM c_leccion WHERE fl_programa=$fl_programa AND no_grado=$no_grado AND no_semana=$p_no_semana");
  $fl_leccion = !empty($row[0])?$row[0]:NULL;
  
  # Recupera la semana de la leccion
  $row = RecuperaValor("SELECT fl_semana FROM k_semana WHERE fl_term=$fl_term AND fl_leccion=$fl_leccion");
  return !empty($row[0])?$row[0]:NULL;
}

function ObtenLimiteEntregaSemana($p_alumno, $p_semana) {
  
  # Recupera datos del alumno
  $fl_semana = ObtenFolioSemanaAlumno($p_alumno, $p_semana);
  $diferencia = RecuperaDiferenciaGMT( );
  
  # Recupera la fecha limite de entrega de la semana
  $Query  = "SELECT DATE_FORMAT(fe_entrega, '%c') 'fe_mes', DATE_FORMAT(fe_entrega, '%e, %Y') 'fe_dia_anio', ";
  // $Query .= "DATE_FORMAT(fe_entrega, '%H:%i:%s %p') 'fe_hora' ";
  $Query .= "DATE_FORMAT((DATE_ADD(fe_entrega, INTERVAL $diferencia HOUR)), '%l:%i %p') 'fe_hora'";
  $Query .= "FROM k_semana WHERE fl_semana=$fl_semana";
  $row = RecuperaValor($Query);
  return ObtenNombreMes($row[0])." ".$row[1]. " ".$row[2];
}

function ObtenStatusAlumno($p_alumno) {
  
  # Recupera datos del alumno
  $no_semana = ObtenSemanaActualAlumno($p_alumno);
  $fl_semana = ObtenFolioSemanaAlumno($p_alumno, $no_semana);
  $fl_grupo = ObtenGrupoAlumno($p_alumno);
  
  # Recupera la fecha limite de entrega de la semana
  $row = RecuperaValor("SELECT fg_entregado FROM k_entrega_semanal WHERE fl_alumno=$p_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana");
  if($row[0] == "1")
    return "Complete";
  else
    return "Not complete";
}

function ObtenCalificadoAlumno($p_alumno, $p_semana) {
  
  # Recupera datos del alumno
  $fl_semana = ObtenFolioSemanaAlumno($p_alumno, $p_semana);
  $fl_grupo = ObtenGrupoAlumno($p_alumno);
  
  # Recupera la fecha limite de entrega de la semana
  $Query  = "SELECT fl_promedio_semana FROM k_entrega_semanal WHERE fl_alumno=$p_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana";
  $row = RecuperaValor($Query);
  if(empty($row[0]))
    return False;
  else
    return True;
}


// MDB Livesession
function ObtenSemanaLiveSessionStudent($p_student) {
  $fl_grupo = ObtenGrupoAlumno($p_student);
  $tolerancia_link = ObtenConfiguracion(36);
  
  $Query  = "SELECT d.no_semana no_semana ";
  $Query .= "FROM k_clase a, c_grupo b, k_semana c, c_leccion d ";
  $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
  $Query .= "AND a.fl_semana=c.fl_semana ";
  $Query .= "AND c.fl_leccion=d.fl_leccion ";
  $Query .= "AND b.fl_grupo=$fl_grupo ";
  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
  $Query .= "ORDER BY fe_clase";
  $row = RecuperaValor($Query);
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenLiveSessionDisponible($p_clase) {
  $tolerancia_antes = ObtenConfiguracion(34);
  $tolerancia_link = ObtenConfiguracion(36);
  
  $Query  = "SELECT COUNT(1) ";
  $Query .= "FROM k_clase ";
  $Query .= "WHERE fl_clase=$p_clase ";
  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_SUB(fe_clase, INTERVAL $tolerancia_antes MINUTE)) <= 0 ";
  $row = RecuperaValor($Query);
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenLiveSessionActualStudent($p_grupo, $p_semana) {
  $tolerancia_link = ObtenConfiguracion(36);
  
  $diferencia = RecuperaDiferenciaGMT( );
  $Query  = "SELECT DATE_FORMAT((DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT((DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), '%e, %Y %H:%i') 'fe_dia_anio' ";
  $Query .= "FROM k_clase ";
  $Query .= "WHERE fl_grupo=$p_grupo ";
  $Query .= "AND fl_semana=$p_semana ";
  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
  $Query .= "ORDER BY fe_clase";
  $row = RecuperaValor($Query);
  return ObtenNombreMes($row[0])." ".$row[1];
}

function ObtenFolioLiveSessionStudent($p_grupo, $p_semana) {
  $tolerancia_link = ObtenConfiguracion(36);
  $fl_alumno = ValidaSesion(False);
  $Query  = "SELECT fl_clase ";
  $Query .= "FROM k_clase ";
  $Query .= "WHERE fl_grupo=$p_grupo ";
  if(($fl_alumno==7228)||($fl_alumno==7246)){
      $Query.=" ";
  }else{
      $Query .= "AND fl_semana=$p_semana ";
  }
  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
  $Query .= "ORDER BY fe_clase";
  $row = RecuperaValor($Query);
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}


function ObtenLiveSessionTeacher($p_teacher, $p_display=False) {
  
  $tolerancia_link = ObtenConfiguracion(36);
  if($p_display)
    $diferencia = RecuperaDiferenciaGMT( );
  else
    $diferencia = 0;
  $Query  = "SELECT a.fl_clase fl_clase, b.fl_grupo fl_grupo, a.fl_semana fl_semana, b.fl_term fl_term, ";
  $Query .= "DATE_FORMAT((DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT((DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), '%e, %Y') 'fe_dia_anio', ";
  $Query .= ConsultaFechaBD("(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR))", FMT_HORAMIN)." hr_clase ";
  $Query .= "FROM k_clase a, c_grupo b ";
  $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
  $Query .= "AND b.fl_maestro=$p_teacher ";
  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
  $Query .= "ORDER BY fe_clase";
  $row = RecuperaValor($Query);
  return $row;
}

function ObtenFechaLiveSessionTeacher($p_teacher) {
  $row = ObtenLiveSessionTeacher($p_teacher, True);  
  return ObtenNombreMes($row["fe_mes"]) . " " . $row["fe_dia_anio"] . " " . $row["hr_clase"];
}

function ObtenFolioLiveSessionTeacher($p_teacher) {  
  $row = ObtenLiveSessionTeacher($p_teacher);  
  return $row["fl_clase"];
}


function ObtenTituloLeccionTeacher($p_teacher) {
  
  $row = ObtenLiveSessionTeacher($p_teacher);
 
  $fl_grupo = $row["fl_grupo"];
  $fl_term = $row["fl_term"];
  $fl_semana = $row["fl_semana"];

  $Query  = "select fl_programa from k_term where fl_term= $fl_term";
  $row = RecuperaValor($Query);

  $fl_programa = $row[0];
        
        $Query  = "select ds_titulo ";
        $Query .= "from k_semana a, c_leccion b ";
        $Query .= "where a.fl_leccion = b.fl_leccion ";
        $Query .= "and fl_term = $fl_term ";
        $Query .= "and fl_semana = $fl_semana ";
        $Query .= "and fl_programa = $fl_programa ";
  $row = RecuperaValor($Query);
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
  
}

function ObtenTituloLeccionPorClase($p_clase) {  
  $Query  = "select ds_titulo ";
  $Query .= "from k_clase a, k_semana b, c_leccion c ";
  $Query .= "where a.fl_semana = b.fl_semana ";
  $Query .= "and b.fl_leccion = c.fl_leccion ";
  $Query .= "and a.fl_clase = $p_clase";
  $row = RecuperaValor($Query);
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;  
}

function ObtenGrupoTeacher($p_teacher) {  
  $row = ObtenLiveSessionTeacher($p_teacher); 
  $fl_grupo = $row["fl_grupo"];
  
  return ObtenNombreGrupo($fl_grupo);  
}

function ObtenNombreGrupo($p_grupo) {
  $Query  = "select nb_grupo from c_grupo where fl_grupo = $p_grupo";
  $row = RecuperaValor($Query);  
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;  
}

function ObtenRegistroLiveSession($p_clase) {
  $Query  = "select fl_live_session, fl_clase, cl_estatus, ds_meeting_id, ";
  $Query .= "ds_password_admin, ds_password_asistente, ds_mensaje_bienvenida, cl_licencia ";
  $Query .= "from k_live_session where fl_clase = $p_clase";
  
  $row = RecuperaValor($Query);  
  return $row;   
}

function ObtenNombreArchivoJNLP($folio) {
  return "applet_rc_" . $folio . ".jnlp";
}
# Funciones para las clases globales
function ObtenClaseGlobalStudent($p_alumno) {
  
  # Recupera el grupo del alumno  
  $row = RecuperaValor("SELECT fl_clase_global FROM k_alumno_cg WHERE fl_usuario=$p_alumno");
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

// JGFL Livesession
function ObtenSemanaLiveSessionStudentCG($p_student) {
  $fl_clase_global = ObtenClaseGlobalStudent($p_student);
  $tolerancia_link = ObtenConfiguracion(36);
  
  $Query  = "SELECT a.fl_clase_cg, a.fl_clase_global, no_orden, ds_clase ";
  $Query .= "FROM k_clase_cg a, c_clase_global cg ";
  $Query .= "WHERE a.fl_clase_global = cg.fl_clase_global ";
  $Query .= "AND a.fl_clase_global = $fl_clase_global ";
  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";  
  $Query .= "ORDER BY fe_clase ";
  $row = RecuperaValor($Query);
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenFolioLiveSessionStudentCG($p_clase_global, $p_clase) {
  $tolerancia_link = ObtenConfiguracion(36);
  
  $Query  = "SELECT fl_clase_cg";
  $Query .= "FROM k_clase_cg ";
  $Query .= "WHERE fl_clase_global=$p_clase_global ";
  $Query .= "AND fl_clase_cg=$p_p_clase ";
  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
  $Query .= "ORDER BY fe_clase";
  $row = RecuperaValor($Query);
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;
}

function ObtenRegistroLiveSessionCG($p_clase) {
  $Query = "SELECT fl_live_session_cg, fl_clase_cg, cl_estatus, ds_meeting_id, ";
  $Query .= "ds_password_admin, ds_password_asistente, ds_mensaje_bienvenida ";
  $Query .= "FROM k_live_sesion_cg WHERE fl_clase_cg = $p_clase ";
  
  $row = RecuperaValor($Query);  
  return $row;   
}

function ObtenTituloLeccionPorClaseGlobal($p_clase) {  
  $Query = "SELECT ds_clase FROM k_clase_cg a, c_clase_global b ";
  $Query .= "WHERE a.fl_clase_global = b.fl_clase_global AND a.fl_clase_cg= $p_clase ";
  $row = RecuperaValor($Query);
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;  
}

function ObtenLiveGlobalClassSessionTeacher($p_teacher, $p_display=False) {
  
  $tolerancia_link = ObtenConfiguracion(36);
  if($p_display)
    $diferencia = RecuperaDiferenciaGMT( );
  else
    $diferencia = 0;
  $Query  = "SELECT a.fl_clase_cg fl_clase, b.fl_clase_global fl_clase_global, ";
  $Query .= "DATE_FORMAT((DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT((DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), '%e, %Y') 'fe_dia_anio', ";
  $Query .= ConsultaFechaBD("(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR))", FMT_HORAMIN)." hr_clase ";
  $Query .= "FROM k_clase_cg a, c_clase_global b ";
  $Query .= "WHERE a.fl_clase_global=b.fl_clase_global ";
  $Query .= "AND b.fl_maestro=$p_teacher ";
  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
  $Query .= "ORDER BY fe_clase";
  $row = RecuperaValor($Query);
  return $row;
}

function ObtenNombreGlobalClass($p_clase_global) {
  $Query  = "SELECT ds_clase FROM c_clase_global WHERE fl_clase_global = $p_clase_global";
  $row = RecuperaValor($Query);  
  $result = !empty($row[0])?$row[0]:NULL;
  return $result;  
}

function ObtenGlobalClassTeacher($p_teacher) {  
  $row = ObtenLiveGlobalClassSessionTeacher($p_teacher); 
  $fl_clase_global = $row["fl_clase_global"];
  
  return ObtenNombreGlobalClass($fl_clase_global);  
}


/**
 * MJD ##funcion para presentar cuestionario FRONT teacher.
 * @param 
 * 
 */

function Btstrp_Forma_CampoInfoCampus($label, $text) {

    return '<div class="form-group">'
            . '<label class="col-xs-12" style="font-size:1.2em;"><strong>' . $label . '</strong></label>'
            . '<span class="col-xs-12" style="">' . $text . '</span>'
            . '</div>';
}




/**
 * MJD ##funcion para presentar/el historial de estudiante 
 * Sen envia como parametro el clave del alumno y el programa que esta cursado.
 * @param 
 * 
 */
function PresentaAcademiHistoryF($p_alumno, $p_programa, $p_admin = True){
    $clave = $p_alumno;
    $fl_programa = $p_programa;
    # Proceso que actualiza la calificacion de los term
    $QueryTR  = "SELECT SUM(i.no_equivalencia)/COUNT(a.fl_semana), a.fl_term, no_grado, c.fl_alumno ";
    $QueryTR .= "FROM k_semana a, k_term b, k_entrega_semanal c, c_calificacion i ";
    $QueryTR .= "WHERE a.fl_term=b.fl_term AND a.fl_semana=c.fl_semana AND c.fl_promedio_semana=i.fl_calificacion ";
    $QueryTR .= "AND a.fl_term IN(SELECT fl_term FROM k_alumno_term e WHERE e.fl_alumno=c.fl_alumno AND c.fl_alumno=$clave) ";
    $QueryTR .= "GROUP BY a.fl_term ";
    $rs = EjecutaQuery($QueryTR);
    $terms = 0;
    $terms_no = 0;
    for($i=0;$row=RecuperaRegistro($rs);$i++){    
        $fl_term = $row[1];
        EjecutaQuery("UPDATE k_alumno_term SET no_promedio='".$row[0]."' WHERE fl_alumno=$clave AND fl_term=$row[1] ");
    }
    # Term actual
    $no_grado_actual = $fl_term;
    
    # Actualizamos la calificacion del estudiante con la calificacion de los terms
    $Querysd  = "SELECT SUM(no_promedio)/COUNT(*) FROM k_alumno_term ";
    $Querysd .= "WHERE fl_term IN(SELECT MAX(a.fl_term) FROM k_alumno_term a, k_term b ";
    $Querysd .= "WHERE a.fl_term=b.fl_term AND fl_alumno=$clave GROUP BY b.no_grado) AND fl_alumno=$clave ";
    $rst = RecuperaValor($Querysd);
    EjecutaQuery("UPDATE c_alumno SET no_promedio_t='".round($rst[0])."' WHERE fl_alumno=$clave");
    
    
    # Promedio
    $Querystd  = "SELECT a.no_promedio_t, ";
    $Querystd .= "CASE no_promedio_t WHEN 0 THEN 0 ELSE (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t))  END cl_calificacion, ";
    $Querystd .= "CASE no_promedio_t WHEN 0 THEN 0 ELSE (SELECT fg_aprobado FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t))  END cal_aprobada ";
    $Querystd .= "FROM c_alumno a ";
    $Querystd .= "WHERE fl_alumno=$clave ";
    $rowstd = RecuperaValor($Querystd);
    $no_promedio_t = $rowstd[0];
    $cl_calificacion = $rowstd[1];
    $cal_aprobada = $rowstd[2];
    echo '
  <article class="col-sm-12 col-md-12 col-lg-12 padding-5 sortable-grid ui-sortable">
          
    <!-- Widget ID (each widget will need unique ID)-->
    <div class="jarviswidget jarviswidget-color-blueLight" id="wid-id-10" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-sortable="false" role="widget">
      <header role="heading" class="no-border txt-color-white" style="background-color:#0092cd;">
        <span class="widget-icon"> <i class="fa fa-list-alt"></i> </span>
        <h2>Terms</h2>
        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
        <div role="menu" class="widget-toolbar hidden-phone txt-color-black">        
          <span style="color:#000;">'.ObtenEtiqueta(524).'&nbsp<strong>'.$cl_calificacion.'</strong></span>
          <span class="label label-';if(!empty($cal_aprobada)) echo "success"; else echo "danger"; echo '">
            <i class="fa fa-thumbs-';if(!empty($cal_aprobada)) echo "up"; else echo "down"; echo ' fa-lg"></i>'.$no_promedio_t.'
          </span>                                                      
        </div>
      <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
      <!-- widget div-->
      <div role="content">
        <!-- widget content -->
        <div class="widget-body no-padding">
          <div class="panel-group smart-accordion-default" id="accordion-2">';
    # Buscamos todos los terms que haya cursado
    $QueryT = "SELECT a.fl_term, b.no_grado, a.no_promedio ";
    $QueryT .= "FROM k_alumno_term a, k_term b LEFT JOIN c_leccion lec ON(lec.fl_programa=b.fl_programa AND lec.no_grado=b.no_grado) ";
    $QueryT .= "LEFT JOIN c_programa pro ON(pro.fl_programa=b.fl_programa AND pro.fl_programa=lec.fl_programa), c_periodo c ";
    $QueryT .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$clave ";
    $QueryT .= "GROUP BY a.fl_term ORDER BY c.fe_inicio, b.no_grado";
    $rs = EjecutaQuery($QueryT);
    for($tot_grados=1;$row=RecuperaRegistro($rs);$tot_grados++){
        $fl_term = $row[0];
        $no_grado = $row[1];
        $no_promedio = $row[2];
        $rowg = RecuperaValor("SELECT fl_grupo FROM c_grupo WHERE fl_term=$fl_term");
        $fl_grupo = $rowg[0];
        # activa el term actual el collapsed
        if ($no_grado_actual == $fl_term)
            $aria_expanded = 'class="" aria-expanded="true"';
        else
            $aria_expanded = 'class="collapsed" aria-expanded="false"';
        echo '
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion-2" href="#term_'.$tot_grados.'" '.$aria_expanded.'> 
                      <i class="fa fa-fw fa-plus-circle txt-color-green"></i> 
                      <i class="fa fa-fw fa-minus-circle txt-color-red"></i>Term '.$no_grado."&nbsp;&nbsp;&nbsp;";
        $Query = "SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio) AND no_max >= ROUND($no_promedio)";
        $prom_g = RecuperaValor($Query);
        $term_cal = $prom_g[0] . "&nbsp;" . $no_promedio;
        $fg_aprobado = $prom_g[1];
        if(!empty($prom_g)){
            echo '<span class="label label-';
            if (!empty($fg_aprobado))
                echo "success";
            else
                echo "danger";
            echo '">
                        GPA: ' . $term_cal . ' %
                        </span>';
        }
        # Indicamos que terms son recursados
        if ($grado_repetido == $no_grado)
            echo $recurse = "&nbsp;&nbsp;&nbsp;<strong class='txt-color-red'>".ObtenEtiqueta(853)."</strong>";
        else
            echo $recurse = "";
        echo '
                      <div class="pull-right txt-color-red">';
        # Mostramos las semanas que hacen falta calificar
        $QueryT0  = "SELECT lec.fl_leccion, lec.no_semana, lec.ds_titulo, sem.fl_semana ";
        $QueryT0 .= "FROM c_leccion lec, k_semana sem ";
        $QueryT0 .= "WHERE lec.fl_leccion=sem.fl_leccion AND lec.fl_programa=".$fl_programa." AND lec.no_grado=".$no_grado." ";
        $QueryT0 .= "AND sem.fl_term=".$fl_term." ORDER BY lec.no_semana ";
        $rs0 = EjecutaQuery($QueryT0);
        $lo = "";
        for ($j = 0; $row2= RecuperaRegistro($rs0); $j++) {
            $fl_leccion = $row2[0];
            $no_semana = $row2[1];
            $ds_titulo = str_texto($row2[2]);
            $fl_semana = $row2[3];
            
            $fg_rubric=ExisteRubric($fl_leccion);

            if (!empty($no_semana)) {
                $Query = "SELECT fl_clase, " . ConsultaFechaBD('fe_clase', FMT_CAPTURA) . " fe_clase, ";
                $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN) . " hr_clase, fg_obligatorio, fg_adicional, b.fl_entrega_semanal ";
                $Query .= "FROM k_clase a, k_entrega_semanal b ";
                $Query .= "WHERE a.fl_semana=b.fl_semana ";
                $Query .= "AND a.fl_grupo=b.fl_grupo ";
                $Query .= "AND b.fl_alumno=$clave ";
                $Query .= "AND a.fl_semana=" . $fl_semana. " ";
                $Query .= "ORDER BY fl_clase ";
                $cons = EjecutaQuery($Query);
                while ($row2 = RecuperaRegistro($cons)) {
                    $fl_clase = $row2[0];
                    $fg_obligatorio = $row2[3];
                    $fg_adicional = $row2[4];
                    if ($fg_obligatorio == '1') {

                        if($fg_rubric==1){
                            # Revisa si hay calificacion para el alumno en esta leccion
                            $Query = "SELECT b.cl_calificacion, b.ds_calificacion, b.fg_aprobado, b.no_equivalencia ";
                            $Query .= "FROM k_entrega_semanal a, c_calificacion b ";
                            $Query .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
                            $Query .= "AND a.fl_alumno=$clave ";
                            $Query .= "AND a.fl_semana=" . $fl_semana;
                            $row = RecuperaValor($Query);
                        }else{
                            $row=null;
                        }
                    }
                    $fecha_clase = RecuperaValor("SELECT fe_clase FROM k_clase WHERE fl_semana = " . $fl_semana . " AND fl_grupo = $fl_grupo");
                    $diferencia_fechas = strtotime($fecha_clase[0]) + 1200 - time();
                    /* Solo hay una calificacion por semana en las extras no se le assigna */
                    if ($diferencia_fechas <= 0 AND $fg_obligatorio == '1' AND $fg_adicional == '0' AND empty($row[0]) AND $no_semana != 12) { // porque es la ultima semana por lo regular esa no se cuenta
                        $lo = $lo .$no_semana . ", ";
                    }
                }
            }
        }
        if(!empty($lo))
            echo "<label><i class='fa fa-exclamation-circle'></i> Missing Grades: </label>&nbsp;";
        echo "<span class='badge bg-color-red'>".substr($lo, 0, -2) . '</span></div>';
        $grado_repetido = $no_grado;
        echo '
                      </a>
                    </h4>
                  </div>';                  
        echo '
                  <div id="term_'.$tot_grados.'" ';
        if(!$p_admin)
            $style = "padding-top:50px;";
        # Activamos elterm actual para que se abara el collapsed
        if ($no_grado_actual == $fl_term) {
            echo "
                    style = '$style'
                    aria-expanded='true'
                    class='panel-collapse collapse in' ";
        }
        else {
            echo "
                    style = '$style height: 0px;'
                    aria-expanded='false'
                    class='panel-collapse collapse' ";
        }
        echo ' >
                    <div class="panel-body no-padding">
                      <div class="col-sm-12 col-md-12 col-lg-12 no-padding"  style="padding-top: 50px;">
                      <div class="table-responsive">
                        <table class="table table-bordered table-condensed" width="100%" id="tbl_term_'.$fl_term.'">
                          <thead>
                            <th>'.ObtenEtiqueta(550).'</th>
                            <th>'.ObtenEtiqueta(551).'</th>
                            <th>'.ObtenEtiqueta(557).'</th>
                            <th>'.ObtenEtiqueta(428).'</th>
                            <th>'.ObtenEtiqueta(552).'</th>
                            <th>'.ObtenEtiqueta(553).'</th>
                          </thead>
                          <tbody>';
        # Buscamos las lecciones del estudiante dependiendo del term y programa
        $QueryT1  = "SELECT lec.fl_leccion, lec.no_semana, lec.ds_titulo, sem.fl_semana ";
        $QueryT1 .= "FROM c_leccion lec, k_semana sem ";
        $QueryT1 .= "WHERE lec.fl_leccion=sem.fl_leccion AND lec.fl_programa=".$fl_programa." AND lec.no_grado=".$no_grado." ";
        $QueryT1 .= "AND sem.fl_term=".$fl_term." ORDER BY lec.no_semana ";
        $rs2 = EjecutaQuery($QueryT1);
        for($lecciones=0;$row2=RecuperaRegistro($rs2);$lecciones++){
            $fl_leccion = $row2[0];
            $no_semana = $row2[1];
            $ds_titulo = str_texto($row2[2]);
            $fl_semana = $row2[3];                            
            if(!empty($no_semana)){
                $Query = "SELECT fl_clase, " . ConsultaFechaBD('fe_clase', FMT_CAPTURA) . " fe_clase, ";
                $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN) . " hr_clase, ";
                $Query .= "fg_obligatorio, fg_adicional, b.fl_entrega_semanal, a.fl_grupo ";
                $Query .= "FROM k_clase a, k_entrega_semanal b ";
                $Query .= "WHERE a.fl_semana=b.fl_semana ";
                $Query .= "AND a.fl_grupo=b.fl_grupo ";
                $Query .= "AND b.fl_alumno = $clave ";
                $Query .= "AND a.fl_semana=" . $fl_semana. " ";
                $Query .= "ORDER BY fl_clase ";
                $cons = EjecutaQuery($Query);
                while ($row3 = RecuperaRegistro($cons)) {
                    $fl_clase = $row3[0];
                    if (!empty($row3[1])) { # Ya se habia puesto una fecha para la clase
                        $fe_clase = $row3[1];
                        $hr_clase = $row3[2];
                    }
                    $fg_obligatorio = $row3[3];
                    $fg_adicional = $row3[4];
                    $fl_grupo = $row3[5];

                    $fg_rubric=ExisteRubric($fl_leccion);

                    if ($fg_adicional == '1') {
                        $adicionales++;
                        $no_semana = '';
                        $ds_titulo = ObtenEtiqueta(538);
                        $row[0] = '';
                    } else {

                        if($fg_rubric==1){
                            # Revisa si hay calificacion para el alumno en esta leccion
                            $Query = "SELECT b.cl_calificacion, b.ds_calificacion, b.fg_aprobado, b.no_equivalencia ";
                            $Query .= "FROM k_entrega_semanal a, c_calificacion b ";
                            $Query .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
                            $Query .= "AND a.fl_alumno=$clave ";
                            $Query .= "AND a.fl_semana=" . $fl_semana;
                            $row = RecuperaValor($Query);
                        }else{
                            $row=null;
                        }
                    }
                    # Consulta el estatus de asistencia a live session
                    $Query = "SELECT a.fl_live_session, a.fl_usuario, b.nb_estatus, d.fl_semana
                                FROM k_live_session_asistencia a, c_estatus_asistencia b, k_live_session c, k_clase d
                                WHERE a.cl_estatus_asistencia = b.cl_estatus_asistencia
                                AND a.fl_live_session = c.fl_live_session
                                AND c.fl_clase = d.fl_clase
                                AND c.fl_clase = " . $fl_clase. " 
                                AND d.fl_semana = " . $fl_semana. "
                                AND a.fl_usuario = $clave";
                    $rasis = RecuperaValor($Query);
                    switch ($fg_obligatorio) {
                        case '0':
                            $obliga = ObtenEtiqueta(17);
                            break;
                        case '1':
                            $obliga = ObtenEtiqueta(16);
                            break;
                        default:
                            $obliga = '';
                    }

                    echo '
                                <tr>
                                  <td>'.$no_semana.'</td>
                                  <td>'.$ds_titulo.'</td>
                                  <td>'.$fe_clase.'</td>
                                  <td>'.$obliga.'</td>
                                  <td>';
                    if (!empty($rasis[0])) {
                        echo "$rasis[2]";
                    }
                    else {
                        $fecha_clase = RecuperaValor("SELECT fe_clase FROM k_clase WHERE fl_semana = " . $fl_semana . " AND fl_grupo = $fl_grupo");
                        $diferencia_fechas = strtotime($fecha_clase[0]) + 1200 - time();
                        if ($diferencia_fechas <= 0) {
                            $ds_rasis = RecuperaValor("SELECT nb_estatus FROM c_estatus_asistencia d WHERE cl_estatus_asistencia=1");
                            echo "$ds_rasis[0]";
                        }
                        else
                            echo "&nbsp;";
                    }
                    echo '</td>
                                  <td>';
                    if (!empty($row[0])) {
                        $suma_cal_g += $row[3];
                        $suma_cal_t += $row[3];
                        $factor_promedio_g++;
                        $factor_promedio_t++;
                        echo $row[0];
                    }
                    if(empty($fg_rubric)){
                        echo"<span class='text-success'>Evaluation not required</span>";
                    }
                    $fecha_clase = RecuperaValor("SELECT fe_clase FROM k_clase WHERE fl_semana = " . $fl_semana. " AND fl_grupo = $fl_grupo");
                    $diferencia_fechas = strtotime($fecha_clase[0]) + 1200 - time();
                    /* Solo hay una calificacion por semana en las extras no se le assigna */
                    if($p_admin){
                        if ($diferencia_fechas <= 0 AND $fg_obligatorio == '1' AND $fg_adicional== '0' AND empty($row[0])) {
                       //     echo "<a href='javascript:AssignGrade($row3[5],$clave);' class='txt-color-red'>Assign Grade</a> <i class='fa fa-warning'></i>";
                        } 
                        else
                            echo "&nbsp;";
                    }
                    echo '</td>
                                </tr>';
                }
            }
        }                  
        echo '    </tbody>
                        </table></div>
                      </div>
                    </div>
                  </div>
                </div>
                <script>
                $(document).ready(function(){
                  $("#tbl_term_'.$fl_term.'").dataTable({"bSort": false, "bLengthChange": false, "bPaginate": false,});
                });
                </script>';
    }
    echo '</div>
        </div>
        <!-- end widget content -->
      </div>
      <!-- end widget div -->
    </div>
    <!-- end widget -->
  </article>';
}




# Funcion para obtener tiempo desde su ultima sesion
function time_elapsed_string($datetime, $full = false){
    $date = date("Y-m-d G:i:s");
    $now = new DateTime($date);
    $then = new DateTime($datetime);
    $diff = (array) $now->diff( $then );

    // $diff['w']  = floor( $diff['d'] / 7 );
    // $diff['d'] -= $diff['w'] * 7;

    $string = array(
        'y' => ObtenEtiqueta(1107),
        'm' => ObtenEtiqueta(1108),
        'w' => ObtenEtiqueta(1108),
        'd' => ObtenEtiqueta(1110),
        'h' => ObtenEtiqueta(1111),
        'i' => ObtenEtiqueta(1112),
        's' => ObtenEtiqueta(1113),
    );

    foreach( $string as $k => & $v )
    {
        if ( $diff[$k] )
        {
            $v = $diff[$k] . ' ' . $v .( $diff[$k] > 1 ? 's' : '' );
        }
        else
        {
            unset( $string[$k] );
        }
    }

    if ( ! $full ) $string = array_slice( $string, 0, 1 );
    return $string ? implode( ', ', $string ) ." ".ObtenEtiqueta(1114) : 'just now';
}



/**
 * MJD #funcion que envia mensaje de notificacions a persona responsable si la tiene.
 * @param 
 * 
 */

function RecoveryEmailResponsableStudent($fl_alumno){
    
    #Verificamos si puede enviar el email al responsable del alumno
    $Query="SELECT fg_copy_email_responsable FROM c_alumno WHERE fl_alumno=$fl_alumno ";
    $row=RecuperaValor($Query);
    $fg_copy=$row[0];

    if($fg_copy){
        $Query="SELECT ds_email_r  FROM k_presponsable a
            LEFT JOIN c_usuario b ON(b.cl_sesion=a.cl_sesion) 
            WHERE b.fl_usuario=$fl_alumno ";
        $row=RecuperaValor($Query);
        $ds_email=str_texto($row['ds_email_r']);
    }


    return $ds_email;

}


/**
 * MJD #funcion que envia mensaje de notificacions al email alternativo si la tiene.
 * @param 
 * 
 */

function RecoveryEmailAlternative($fl_alumno){
    

    #Verificamos si puede enviar el email al responsable del alumno
    $Query="SELECT fg_copy_email_alternativo FROM c_alumno WHERE fl_alumno=$fl_alumno ";
    $row=RecuperaValor($Query);
    $fg_copy=$row[0];

    if($fg_copy){
        $Query="SELECT ds_a_email
			FROM k_app_contrato a
            LEFT JOIN c_usuario b ON(a.cl_sesion=b.cl_sesion) 
			WHERE b.fl_usuario=$fl_alumno  ";
        $row=RecuperaValor($Query);
        $ds_email=$row['ds_a_email']; 
    }
    return $ds_email;
}




?>