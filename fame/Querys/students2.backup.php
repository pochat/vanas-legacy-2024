<?php

# Libreria de funciones	
require("../lib/self_general.php");

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False, 0, True);

# Verifica que el usuario tenga permiso de usar esta funcion
if (!ValidaPermisoSelf(FUNC_SELF)) {
  MuestraPaginaError(ERR_SIN_PERMISO);
  exit;
}
# Obtenemo el instituto
$fl_instituto = ObtenInstituto($fl_usuario);
$fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
$fl_usuario_logueado=$fl_usuario;

#Verifica que tenga permiso de ver todos los estudiantes.
$Query="SELECT fg_ferpa FROM k_instituto_filtro WHERE fl_instituto=$fl_instituto ";
$ro=RecuperaValor($Query);
$fg_ferpa=$ro['fg_ferpa'];

$selected = (!empty($_GET['selected'])?$_GET['selected']:0);

# Entrega el parametro para el detalle del alumno
# $fl_alumno = isset($_REQUEST('fl_alumno')) ? $_REQUEST('fl_alumno') : '' ;

# Query que obtiene los usuarios dependiedo de la intitucion
# Adm Muestra teacher y students
# Teacher Muestra los students

$Query = "";
if ($selected != 0) {
  $Query .= "SELECT * FROM ( ";
}

$Query .= "SELECT fl_usuario, ds_ruta_avatar, ds_nombres, nb_perfil, `status`, fe_sesion, 'usage', fg_activo, fl_perfil_sp, ";
$Query .= "nb_grupo, nb_programa".$sufix.", fl_programa_sp, confirmado, ds_progreso, no_promedio_t, fg_confirmado,  ";

# Start CASE 1 Query
$Query .= "CASE fg_status_pro WHEN '1' THEN '<b class=\'text-warning\'><i class=\'fa fa-pause\'></i> ".ObtenEtiqueta(1999)."</b>' END fg_status_pro, fl_usu_pro, fg_assign_myself_course,fg_asignado_playlist,fl_playlist,ds_email,fg_pertenece_otro_instituto ";
# End CASE 1 Query

# Start FROM 1
$Query .= "FROM ((SELECT usr.fl_usuario, al.ds_ruta_avatar, CONCAT(usr.ds_nombres, ' ', usr.ds_apaterno ) ds_nombres, cpr.nb_perfil, ";

# Start CASE 2 Query Note: What is for the /*'0.01 GB'*/ ?
$Query .= "CASE usr.fg_activo WHEN 1 THEN 'Active' ELSE 'Inactive' END `status`, ";
$Query .= "DATE_FORMAT((CASE WHEN usr.fe_ultacc is null THEN usr.fe_alta ELSE usr.fe_ultacc END), '%Y-%m-%d %H:%i:%s') fe_sesion, /*'0.01 GB'*/ 'usage', usr.fg_activo, usr.fl_perfil_sp, ";
$Query .= "al.nb_grupo, ";
$Query .= "IFNULL(cpro.nb_programa".$sufix.", '".ObtenEtiqueta(1039)."') nb_programa".$sufix.", cpro.fl_programa_sp, '1' confirmado, usrp.ds_progreso, usrp.no_promedio_t, '1' fg_confirmado, fg_status_pro, fl_usu_pro, usr.fg_assign_myself_course,usrp.fg_asignado_playlist,usrp.fl_playlist,usr.ds_email,'0'fg_pertenece_otro_instituto ";
$Query .= "FROM c_usuario usr ";
# End CASE 2 Query

# Left Join 1
$Query .= "LEFT JOIN c_alumno_sp al ON(al.fl_alumno_sp=usr.fl_usuario) ";

# Left join 2
$Query .= "LEFT JOIN k_usuario_programa usrp ON(usrp.fl_usuario_sp=usr.fl_usuario) ";
if($fl_perfil_sp==PFL_MAESTRO_SELF){
    $Query .="LEFT JOIN c_usuario ma ON ma.fl_usuario= usrp.fl_maestro AND ma.fl_instituto=$fl_instituto ";
}

if($fg_ferpa<>'1'){
  $Query .=" AND usrp.fl_maestro=$fl_usuario ";
}

# Left Join 3
$Query .= "LEFT JOIN c_programa_sp cpro ON(cpro.fl_programa_sp=usrp.fl_programa_sp), c_perfil cpr ";
$Query .= "WHERE usr.fl_perfil_sp=cpr.fl_perfil ";
if($fl_perfil_sp==PFL_MAESTRO_SELF){
    $Query .="AND ma.fl_instituto=$fl_instituto ";
}
if($fl_perfil_sp==PFL_ADMINISTRADOR){
    $Query .=" AND usr.fl_instituto=$fl_instituto ";
}

if($fg_ferpa<>'1'){
  $Query .=" AND usrp.fl_maestro=$fl_usuario ";
}

$Query .=" ) ";

# Start UNION 1
$Query .= "UNION (
          SELECT a.fl_envio_correo fl_usuario, '' ds_ruta_avatar, CONCAT(ds_first_name, ' ', ds_last_name ) ds_nombres, '".ObtenEtiqueta(1039)."'  nb_perfil, ";
# Note: What is for this: /*'0.00 GB'*/ ?
$Query .= "'".ObtenEtiqueta(1092)."' `status`, DATE_FORMAT(fe_alta, '%Y-%m-%d %H:%i:%s') fe_sesion, /*'0.0 GB'*/ 'usage', '0' fg_activo, '0' fl_perfil_sp, ";
$Query .= "nb_grupo, IFNULL(c.nb_programa".$sufix.", '".ObtenEtiqueta(1039)."') nb_programa".$sufix.", b.fl_programa_sp, '0' confirmado, '0' ds_progreso, ";
$Query .= "'0' no_promedio_t, a.fg_confirmado, '0' fg_status_pro, '0' fl_usu_pro, '0' fg_assign_myself_course,b.fg_asignado_playlist,b.fl_playlist,a.ds_email,'0'fg_pertenece_otro_instituto ";
$Query .= "FROM k_envio_email_reg_selfp a ";
$Query .= "LEFT JOIN k_noconfirmados_pro b ON(a.fl_envio_correo=b.fl_envio_correo) ";
$Query .= "LEFT JOIN c_programa_sp c ON(c.fl_programa_sp=b.fl_programa_sp) ";
$Query .= "WHERE fl_invitado_por_instituto=$fl_instituto AND fg_enviado='1' AND fg_tipo_registro='S' AND fg_confirmado='0' AND fg_scf<>'1' ) ";

  #Nota, se agrega el query para obtener a los estudiantes que estan enviando invitacion que pertencen a otro instituto y no han sido confirmados.
  # Start UNION 2
  $Query .="UNION( 
            SELECT DISTINCT a.fl_usuario,c.ds_ruta_avatar,CONCAT( a.ds_nombres,' ',a.ds_apaterno)ds_nombres , d.nb_perfil,'nb_'`status` ,DATE_FORMAT(a.fe_sesion,'%Y-%m-%d %H:%i:%s')fe_sesion,'Unassigned' 'usage', a.fg_activo ,a.fl_perfil_sp,''nb_grupo,''nb_programa".$sufix."".$sufix.",NULL fl_programa_sp ,NULL confirmado,NULL ds_progreso,NULL no_promedio_t, b.fg_aceptado fg_confirmado,NULL fg_status_pro,NULL fl_usu_pro,NULL fg_assign_myself_course,NULL fg_asignado_playlist,NULL fl_playlist,a.ds_email,'1' fg_pertenece_otro_instituto
            FROM c_usuario a
            JOIN k_instituto_alumno b ON a.fl_usuario=b.fl_usuario_sp 
            JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) 
            JOIN c_perfil d ON d.fl_perfil=a.fl_perfil_sp AND b.fl_instituto=$fl_instituto AND fg_aceptado IN('0','2')
          )";

  #Cuando el amlumno ya acepto unirse y es de otro instituto.
  # Start UNION 3
  $Query .="UNION( 
            SELECT DISTINCT a.fl_usuario,c.ds_ruta_avatar,CONCAT( a.ds_nombres,' ',a.ds_apaterno)ds_nombres , d.nb_perfil,'".ObtenEtiqueta(113)."'`status` ,DATE_FORMAT(a.fe_sesion,'%Y-%m-%d %H:%i:%s')fe_sesion,'Unassigned' 'usage', a.fg_activo ,a.fl_perfil_sp,''nb_grupo,''nb_programa".$sufix.",NULL fl_programa_sp ,b.fg_aceptado confirmado,NULL ds_progreso,NULL no_promedio_t, b.fg_aceptado fg_confirmado,NULL fg_status_pro,NULL fl_usu_pro,NULL fg_assign_myself_course,NULL fg_asignado_playlist,NULL fl_playlist,a.ds_email,'1' fg_pertenece_otro_instituto
            FROM c_usuario a
            JOIN k_instituto_alumno b ON a.fl_usuario=b.fl_usuario_sp 
            JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) 
            JOIN c_perfil d ON d.fl_perfil=a.fl_perfil_sp AND b.fl_instituto=$fl_instituto AND fg_aceptado IN('1') 
            AND NOT EXISTS(SELECT fl_usuario_sp FROM k_usuario_programa WHERE fl_usuario_sp=a.fl_usuario AND b.fl_instituto<>$fl_instituto )
          )";

  #Query que muestra los estudiantes que pertnece a otro isntituto.
  # Start UNION 4
 /* $Query .="UNION(
	        SELECT usr.fl_usuario  , al.ds_ruta_avatar, CONCAT(usr.ds_nombres, ' ', usr.ds_apaterno ) ds_nombres 
	        , cpr.nb_perfil, CASE usr.fg_activo WHEN 1 THEN 'Active' ELSE 'Inactive' END status, DATE_FORMAT((CASE WHEN usr.fe_ultacc is null THEN usr.fe_alta ELSE usr.fe_ultacc END), '%Y-%m-%d %H:%i:%s') fe_sesion, '0.01 GB' 'usage', usr.fg_activo, usr.fl_perfil_sp, al.nb_grupo, IFNULL(cpro.nb_programa, 'Unassigned') nb_programa, cpro.fl_programa_sp, '1' confirmado, usrp.ds_progreso, usrp.no_promedio_t, '1' fg_confirmado, fg_status_pro, fl_usu_pro, usr.fg_assign_myself_course,usrp.fg_asignado_playlist,usrp.fl_playlist,usr.ds_email,''fg_pertenece_otro_instituto 
	        FROM k_instituto_alumno a
	        JOIN c_usuario usr ON usr.fl_usuario=a.fl_usuario_sp 
	        LEFT JOIN c_alumno_sp al ON(al.fl_alumno_sp=usr.fl_usuario) 
	        LEFT JOIN k_usuario_programa usrp ON(usrp.fl_usuario_sp=usr.fl_usuario) AND usrp.fl_maestro=$fl_usuario 
	        LEFT JOIN c_programa_sp cpro ON(cpro.fl_programa_sp=usrp.fl_programa_sp)
	        , c_perfil cpr 
	        WHERE 
	        usr.fl_perfil_sp=cpr.fl_perfil 
	        AND 
  	        a.fl_instituto=$fl_instituto AND a.fg_aceptado='1'
   )";*/
  # End FROM 1
  $Query .= ") AS STUDENTS WHERE fl_perfil_sp=".PFL_ESTUDIANTE_SELF." OR fl_perfil_sp=0 ";
  if ($selected == 0) {
    $Query .= "GROUP BY fl_usuario ";
  }

  $Query .= "ORDER BY fe_sesion DESC ";

  if ($selected != 0) {
    $Query .= " ) AS STUDENT WHERE fl_usuario=".$selected. " /*LIMIT 50 OFFSET 500*/ ";
  }

# The next echo and exit is for Debbuging
 // echo $Query;
 // exit();

$rs = EjecutaQuery($Query);
$numeroderegistros = CuentaRegistros($rs);

// echo '{ "data": [';

for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
  $fl_usuario = $row['fl_usuario'];
  $ds_ruta_avatar = $row['ds_ruta_avatar'];
  $ds_nombres = $row['ds_nombres'];
  $nb_perfil    = $row['nb_perfil'];
  $status = $row['status'];
  $fe_sesion = $row['fe_sesion'];
  $usage = $row['usage'];
  $fg_activo = $row['fg_activo'];
  $fl_perfil = $row['fl_perfil_sp'];
  $ds_email = $row['ds_email'];
  $fg_pertenece_otro_instituto=$row['fg_pertenece_otro_instituto'];
  $fl_usu_pro=$row['fl_usu_pro'];
  $nb_programa = $row[10];
  $fl_programa_sp=$row[11];

  if(!empty($fl_programa_sp)){
      $url_desktop='#site/desktop.php?student='.$fl_usuario.'&fl_programa='.$fl_programa_sp.'&t=1';
      $etq_desktop=ObtenEtiqueta(2573);
  }else{
      $url_desktop="javascript:void(0);";
      $etq_desktop="";
  }

  #Para saber tiene programa
  $p = (!empty($fl_programa_sp)?$fl_programa_sp:"");
  // if (!empty($fl_programa_sp)){
  //   $p = $fl_programa_sp;
  // } else {
  //   $p = "";
  // }
  
  $fl_perfil = (empty($fl_perfil)?"Unassigned":$fl_perfil);
  // if (empty($fl_perfil)){
  //   $fl_perfil = "Unassigned";
  // }

  # Este valor nos indica si ya confirmo o no sera utiizado para la asignacion y cambio de grupo
  $confirmado = $row['confirmado'];
  $nb_grupo = $row['nb_grupo'];
  
  #Para Asignar a un grupo
  $nb_grupo ="<a href='javascript:actions(".ASG_GROUP.", 00, $fl_usuario, $confirmado);'>".ObtenEtiqueta(1071)." </a>";

  #Recupera los grupos al que pertenece el alumno.
  $Query="SELECT nb_grupo,fl_grupo_fame FROM c_grupo_fame WHERE fl_alumno_sp=$fl_usuario ";
  $rsgroup=EjecutaQuery($Query);
  $ds_grupos="";

  for($g = 1; $row = RecuperaRegistro($rsgroup); $g++){
   
      $ds_grupos .='<small class=\'text-muted\'><a href=\'javascript:actions(' . CAM_GROUP . ', 00, '.$fl_usuario.', '.$confirmado.');\' title=\'Change Group\'>  '.$row['nb_grupo'].' </a>  <a href=\'javascript:DeleteGroup('.$row['fl_grupo_fame'].');\' class=\'\' style=\'margin-left:5px;font-size:11px;\' title=\''.ObtenEtiqueta(12).'\'><i class=\'fa  fa-trash-o\'></i></a>  </small><br> ';

  }
  
  // if(empty($nb_programa)) {
  //   $nb_programa = "<a href='javascript:actions(" . ASG_COURSE . ", 00, $fl_usuario, $confirmado);'>" . $row[10] . "</a>";
  // }

  $fl_programa = $row[11];

  if (empty($fl_programa)) {
    $fl_programa = ObtenEtiqueta(1039);
  }

  $ds_progreso = $row[13];
  if (empty($ds_progreso)){
    $ds_progreso = 0;
  }

  $no_promedio_t = round($row['no_promedio_t']);

  if ($fl_programa == 33) {
    $no_promedio_t = round($ds_progreso);
  }

  # Si esta confirmado mostrara la calificacion
  // if($no_promedio_t==""){
  // $no_promedio_t = ObtenPromedioPrograma($fl_programa_sp, $fl_usuario);
  // }    
  // $cl_calificiacion = ObtenCalificacion($no_promedio_t);

  $Queryg = "SELECT cl_calificacion, fg_aprobado FROM c_calificacion_sp WHERE no_min <= ROUND($no_promedio_t) AND no_max >= ROUND($no_promedio_t)";
  $rowg = RecuperaValor($Queryg);
  $cl_calificacion = $rowg['cl_calificacion'];
  $aprovado = $rowg['fg_aprobado'];

  $Query5 = "SELECT ds_calificacion FROM c_calificacion_sp WHERE cl_calificacion='$cl_calificiacion' ";
  $row5 = RecuperaValor($Query5);
  $nb_calificacion = str_texto($row5['ds_calificacion']);

  // $aprovado = ObtenCalificacionAprobada($no_promedio_t);
  if ($aprovado){
    $cal_color = "success";
  } else {
    $cal_color = "danger";
  }

  if (!empty($fl_programa_sp)){
    $gpa = '<span class=\'label label-' . $cal_color . '\'>' . $cl_calificiacion . ' (' . $no_promedio_t . '%) ' . $nb_calificacion . '</span>';
  } else {
    $gpa = ObtenEtiqueta(1039);
  }

  $fg_confirmado = $row['fg_confirmado'];
  $fg_status_pro = $row['fg_status_pro'];
  $fl_usu_pro = $row['fl_usu_pro'];
  $fg_assign_myself_course = $row['fg_assign_myself_course'];
  $fg_asignado_play_list = $row[19];
  $fl_playlist = $row[20];

  if ($fg_asignado_play_list == 1) {

    #Recuperamos el nombre del playlist.
    $QueryPL = "SELECT nb_playlist FROM c_playlist WHERE fl_playlist=$fl_playlist ";
    $rop = RecuperaValor($QueryPL);
    $nb_playlist = $rop[0];
    $fg_etq_play_list = "Playlist: ".$nb_playlist;

  } else {

    $fg_etq_play_list = "";

  }

  # Quiere decir que aun no a confrmado
  if (empty($fg_confirmado) || empty($fl_usu_pro)){
    $fl_usu_pro = $fl_usuario;
  }

  if (empty($fg_activo)) {

    $color = "danger";
    $active = "busy";

  } else {

    $color = "success";
    $active = "online";

  }
  if (!empty($ds_ruta_avatar)){

    $ruta_avatar = PATH_SELF_UPLOADS . "/" . $fl_instituto . "/" . CARPETA_USER . $fl_usuario . "/" . $ds_ruta_avatar;

  } else {

    $ruta_avatar = SP_IMAGES . '/avatar_default.jpg';

  }

  $all = "ALL";

  # Enviamos que tiempo hay desde su ultima conexion
  //$fe_sesion = (!empty($fg_confirmado)?time_elapsed_string($fe_sesion, false):ObtenEtiqueta(1091));

  # Verificamos que los teacher no utilizan licencias
  if (!empty($fg_confirmado)) {
    if ($fl_perfil == PFL_MAESTRO_SELF){
      $use_licence = ObtenEtiqueta(1105);
    } else {
      if (!empty($fg_activo)){
        $use_licence = ObtenEtiqueta(1103);
      } else {
        $use_licence = ObtenEtiqueta(1104);
      }
    }
  } else {
    $use_licence = ObtenEtiqueta(1104);
  }

  # Obtenemos los valores del Quizes o teacher grade
  if (ExisteEnTabla('k_details_usu_pro', 'fl_usu_pro', $fl_usu_pro)) {
    $row1 = RecuperaValor("SELECT fg_quizes, fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=" . $fl_usu_pro);
    $fg_quizes = $row1['fg_quizes'];
    
    if ($fg_quizes) {
      $ds_quizes = ObtenEtiqueta(1916);
      $icono_pul = '';
    }

    $fg_grade_tea = $row1['fg_grade_tea'];
    
    if ($fg_grade_tea) {
      $icono_pul = '<i class=\'fa fa-plus\'></i>';
      $ds_grade_tea = ObtenEtiqueta(1917);
    }

    # Por defaul estara quiz
    # SI tiene activado fg_grade_tea significa que el teacher lo calficara
    
    if (!empty($fg_quizes) && !empty($fg_grade_tea)){
      $assessment = $ds_quizes.' '.$icono_pul.' '.$ds_grade_tea;
    } else {
      $assessment = $ds_quizes;
    }
  } else {
    $assessment = ObtenEtiqueta(1916);
  }

  #Recuperamos el grado del estudiante 
  $Query = "SELECT  nb_grado FROM c_alumno_sp A JOIN k_grado_fame K ON K.fl_grado=A.fl_grado WHERE A.fl_alumno_sp =$fl_usuario";
  $row = RecuperaValor($Query);
  $nb_grado = str_texto($row[0]);

  # Verificamos si el usuario puede asignase solo
  if (!empty($fg_confirmado)) {
    if (!empty($fg_assign_myself_course)){
      $myself = "<a href='javascript:myself(".$fl_usuario.",1);'><span class='label label-success'>".ObtenEtiqueta(16)."</span></a>";
    } else {
      $myself = "<a href='javascript:myself(".$fl_usuario.",0);'><span class='label label-danger'>".ObtenEtiqueta(17)."</span></a>";
    }
  } else {
    $myself = "";
  }

  #Recuperamos el mail
  if (empty($fg_activo)) {

    #Verificamos que no falte, 
    $Query = "SELECT a.fl_envio_correo,CASE WHEN r.fg_autorizado ='0' THEN 'FA' ELSE  r.fg_autorizado END fg_autorizado ,fe_reenvio,fg_confirmado FROM k_envio_email_reg_selfp a LEFT JOIN k_responsable_alumno r ON r.fl_envio_correo=a.fl_envio_correo WHERE a.ds_email='$ds_email' ";

    $row = RecuperaValor($Query);
    $fl_envio_co = $row[0];
    $fg_autoriza = str_texto($row[1]);
    $fe_renvi = $row[2];
    $fe_reenvio = time_elapsed_string($row[2], false);

    if ($fg_autoriza == 'FA') {
      $color = "danger";
      $status = ObtenEtiqueta(2126);
      $date_adicional = '-FA'; //falta autorizacion.
    }

    #La cuenta ni siquiera ha sido confirmada.
    if (empty($fg_confirmado)) {
      $date_adicional = '-NC';
    }

    if (!empty($fe_renvi)) {
      $fe_renvio = ObtenEtiqueta(2309).": ".$fe_reenvio;
    } else {
      $fe_renvio = "";
    }
  } else {
    $date_adicional = "";
    $fe_renvio = "";
  }

  $Query = "SELECT fl_maestro FROM k_usuario_programa WHERE fl_usu_pro=$fl_usu_pro ";
  $rowm = RecuperaValor($Query);
  $fl_mae = $rowm[0];

  $Query = "SELECT CONCAT(ds_nombres,' ',ds_apaterno)AS teacher,fl_instituto FROM c_usuario WHERE fl_usuario=$fl_mae ";

  $rop = RecuperaValor($Query);
  $teacher = $rop[0];
  $fl_instituto_teacher=$rop['fl_instituto'];
  $ds_institutos="";

    #Identifa los estudiantes que han sido invitados y pertenecen a otro instituto.
    if($fg_pertenece_otro_instituto==1){

       $status=1;      
       $student_otro_instituto='&soi=1';
       $fg_confirmado=1;

       #Verficamos su estatus.
       $Query="SELECT fg_aceptado FROM  k_instituto_alumno WHERE fl_usuario_sp=$fl_usuario AND fl_instituto=$fl_instituto ";
       $row=RecuperaValor($Query);

       if($row['fg_aceptado']=='1'){
           $status=ObtenEtiqueta(113);
           $color="success";

           #Recuperamos los Institutos que tien ctualmente el usuario Nota aqui cuando ya se envio invitacion y solo hace falta aceptar.
           $Query="SELECT ds_instituto FROM c_usuario A JOIN c_instituto B ON B.fl_instituto=A.fl_instituto 
               WHERE fl_usuario=$fl_usuario  ";
           $rs3=EjecutaQuery($Query);
       }

       if($row['fg_aceptado']=='0'){
           $status=ObtenEtiqueta(2559);
           $color="primary";

           #Recuperamos los Institutos que tien ctualmente el usuario Nota aqui cuando ya se envio invitacion y solo hace falta aceptar.
           $Query="SELECT ds_instituto FROM c_usuario A JOIN c_instituto B ON B.fl_instituto=A.fl_instituto 
               WHERE fl_usuario=$fl_usuario  ";
           $rs3=EjecutaQuery($Query);
       }
 
       if($row['fg_aceptado']=='2'){
           $status=ObtenEtiqueta(2560);
           $color="warning";

           #Recuperamos los Institutos que tien ctualmente el usuario Cuando ya acepto la invitacion y pertence a mas de dos Institutos.
           $Query="SELECT ds_instituto FROM c_usuario A JOIN c_instituto B ON B.fl_instituto=A.fl_instituto 
               WHERE A.fl_usuario=$fl_usuario AND A.fl_instituto<> $fl_instituto  ";
           $rs3=EjecutaQuery($Query);
 
       }

       for($i3=1;$row3=RecuperaRegistro($rs3);$i3++){

           $ds_institutos.="<i class='fa fa-graduation-cap' aria-hidden='true'></i> ".$row3['ds_instituto']."<br>";

       }

       if(($row['fg_aceptado']=='2')||($row['fg_aceptado']=='0')){

           $bloqueded_privacidad="disabled";
           $liga_href="javascript:void(0);";
           $tooltip_title_name=ObtenEtiqueta(2568);
           $nb_grupo="";

       } else {
           
          #Links todo normal, cuando este registro(student) pertnece a este instituo y ya acepto invitacion.
          $bloqueded_privacidad="";
          if ($selected == 0) {
           $liga_href="index.php#site/students.php?selected=$fl_usuario";
          } else {
           $liga_href="index.php#site/users_details.php?clave=$fl_usu_pro&c=$fg_confirmado&p=".$p."".$student_otro_instituto."";
          }
          $tooltip_title_name=ObtenEtiqueta(1897);
          # Added for the selection of users

       }

    } else {

      #Links todo normal, cuando pertenece a un solo Instituto
      $bloqueded_privacidad="";
      if ($selected == 0) {
        $liga_href="index.php#site/students.php?selected=$fl_usuario";
      } else {
        $liga_href="index.php#site/users_details.php?clave=$fl_usu_pro&c=$fg_confirmado&p=".$p."".$student_otro_instituto.""; 
      }
      $tooltip_title_name=ObtenEtiqueta(1897);
      # Added for the selection of users
        
    }

  $url_perfil="#site/myprofile.php?profile_id=".$fl_usuario."&c=1&uo=".$fl_usuario_logueado."";

  switch ($ds_progreso) {
    case '0':
      $flag_progress = 'zero';
      break;

    case '100':
      $flag_progress = 'hundred';
      break;
    
    default:
      $flag_progress = 'in_the_middle';
      break;
  }

  #Identificamos el usuario CSF para mostrar el status.(Aqui se mostrara que los usuarios ya an sido cargados, mostrar si ya se le envio invitacion o aun no.)
  $Query="SELECT fg_scf FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Query);
  $fg_scf=$row['fg_scf'];
  if($fg_scf=='1'){
      
      $Query="SELECT fg_scfenvio_email,fg_confirmado FROM k_envio_email_reg_selfp WHERE fl_usuario=$fl_usuario ";
      $roy=RecuperaValor($Query);
      $fg_scfenvio_emailg=$roy['fg_scfenvio_email'];
      $fg_scf_confirmado=$roy['fg_confirmado'];

          if($fg_scfenvio_emailg=='1'){

              if($fg_scf_confirmado=='1'){
                  
              }else{
                  $color="warning";
                  $status=ObtenEtiqueta(2559);//Esperando confirmacion, ya se envio el email

              }
          }else{
              
              $color="danger";
              $status=ObtenEtiqueta(2589);//Esperando envio de invitacion.
          }

  }

  $registro = (object)[
    'checkbox'=>'<label class=\'checkbox no-padding no-margin\'><input class=\'checkbox\' type=\'checkbox\' id=\'ch_' . $i . '\' value=\'' . $fl_usuario . '' . $date_adicional . '\'  '.$bloqueded_privacidad.'  ><span></span></label><input type=\'hidden\' id=\'use_lic' . $i . '\' name=\'use_lic' . $i . '\' value=\'1\'>',
    'id'=>'<div class=\'project-members\'><input type=\'checkbox\' id=\'user_' . $i . '\' class=\'checkbox\' '.$bloqueded_privacidad.'><a href=\''.$url_perfil.'\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'' . $ds_nombres . '\'><img src=\'' . $ruta_avatar . '\' class=\'' . $active . '\' alt=\'' . $ds_ruta_avatar . '\' style=\'width:25px;\'></a> </div> ',
    'name'=>'<a href=\''.$liga_href.'\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'' .$tooltip_title_name. '\'>' . $ds_nombres . '</a> <input type=\'hidden\' id=\'ds_nombres_' . $i . '\' value=\'' . $ds_nombres . '\'>',
    'course'=>$ds_nombres,
    'grade'=>$nb_grado,
    'grupo'=>$nb_grupo.'<label class=\'hidden\'>' . $all . '</label> <br><small class=\'text-muted\'>Current  teacher: ' . $teacher . '</small><input type=\'hidden\' id=\'confirmado_' . $i . '\' value=\'' . $confirmado . '\'>',
    'programa'=>$nb_programa . '<br><small class=\'text-muted\'><i>' . $fg_etq_play_list . '</i></small> <br><small class=\'text-muted\'><i><a href=\''.$url_desktop.'\'>' . $etq_desktop . '</i></small><input type=\'hidden\' id=\'fl_programa_std_' . $i . '\'  value=\'' . $fl_programa . '\'> <input type=\'hidden\' id=\'fl_usu_pro_' . $i . '\' name=\'fl_usu_pro_' . $i . '\' value=\'' . $fl_usu_pro . '\'>',
    'status'=>'<span class=\'label label-' . $color . '\'>' . $status . '</span><br><small class=\'text-muted\'> ' . $fe_renvio . '</small>',
    'lastactivity'=>'<span>' . $fe_sesion . '</span>',
    'use_licence'=>'<span>' . $use_licence . '</span>',
    'progress'=>'<div class=\'progress progress-xs\' data-progressbar-value=\'' . $ds_progreso . '\'><div class=\'progress-bar\'></div></div><a href=\'javascript:Pause_Course(' . $fl_usu_pro . ',0,0);\'>' . $fg_status_pro . ' <span class=\'hidden\'>' . $flag_progress . '</span></a>',
    'gpa'=>$gpa,
    'ACR'=>'<span>ACR_' . $nb_programa . ' ' . $all . '</span>',
    'AGR'=>'<span>' . $all . '</span>',
    'ACT'=>'<span>' . $fg_activo . ' ' . $all . '</span>',
    'assessment'=>'<a href=\'javascript:actions(' . ASSESSMENT . ', 00, ' . $fl_usu_pro . ', ' . $confirmado . ')\'><span class=\'label label-success\'>' . $assessment . '</span></a>',
    'myself'=>$myself
  ];

  if ($i <= ($numeroderegistros - 1)){
    $registros .= json_encode($registro).', ';
  } else {
    $registros .= json_encode($registro);
  }

   /** ESTE PROCESO PARA AYUDAR A LA BUSQUEDA AVANZADA **/    
    // echo '
    // {
    //   "checkbox": "<label class=\'checkbox no-padding no-margin\'><input class=\'checkbox\' type=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$fl_usuario.'\'><span></span></label><input type=\'hidden\' id=\'use_lic'.$i.'\' name=\'use_lic'.$i.'\' value=\'1\'>",
    //   "id": "<div class=\'project-members\'><input type=\'checkbox\' id=\'user_'.$i.'\' class=\'checkbox\'><a href=\'javascript:void(0)\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$ds_nombres.'\'><img src=\''.$ruta_avatar.'\' class=\''.$active.'\' alt=\''.$ds_ruta_avatar.'\' style=\'width:25px;\'></a> </div> ",
    //   "name": "<a href=\''.$liga_href.'\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'' .$tooltip_title_name. '\'>' . $ds_nombres . '</a> <input type=\'hidden\' id=\'ds_nombres_' . $i . '\' value=\'' . $ds_nombres . '\'>",
    //   "course": "'.$ds_nombres.'",
    //   "grade": "'.$nb_grado.'",
    //   "grupo": "'.$nb_grupo.' <br>'.$ds_grupos.' <label class=\'hidden\'>'.$all.'</label> <br><small class=\'text-muted\'>Current  teacher: '.$teacher.'</small><input type=\'hidden\' id=\'confirmado_'.$i.'\' value=\''.$confirmado.'\'>",
    //   "programa": "'.$nb_programa.'<br><small class=\'text-muted\'><i>'.$fg_etq_play_list.'</i></small>'.$img_inst.'</div><input type=\'hidden\' id=\'fl_programa_std_'.$i.'\'  value=\''.$fl_programa.'\'> <input type=\'hidden\' id=\'fl_usu_pro_'.$i.'\' name=\'fl_usu_pro_'.$i.'\' value=\''.$fl_usu_pro.'\'><br>",
    //   "grupo": "'.$nb_grupo.' <label class=\'hidden\'>'.$all.'</label> <br><small class=\'text-muted\'>Current  teacher: '.$teacher.'</small><input type=\'hidden\' id=\'confirmado_'.$i.'\' value=\''.$confirmado.'\'>",
    //   "programa": "'.$nb_programa.'<br><small class=\'text-muted\'><i>'.$fg_etq_play_list.'</i></small></div><input type=\'hidden\' id=\'fl_programa_std_'.$i.'\'  value=\''.$fl_programa.'\'> <input type=\'hidden\' id=\'fl_usu_pro_'.$i.'\' name=\'fl_usu_pro_'.$i.'\' value=\''.$fl_usu_pro.'\'><br>",
    //   "status": "<span class=\'label label-'.$color.'\'>'.$status.'</span><br><small class=\'text-muted\'> '.$fe_renvio.'</small>",
    //   "lastactivity": "<span>'.$fe_sesion.'</span>",
    //   "use_licence": "<span>'.$use_licence.'</span>", 
    //   "progress": "<div class=\'progress progress-xs\' data-progressbar-value=\''.$ds_progreso.'\'><div class=\'progress-bar\'></div></div><a href=\'javascript:Pause_Course('.$fl_usu_pro.',0,0);\'>'.$fg_status_pro.' <span class=\'hidden\'>'.$ds_progreso.'</span></a>",
    //   "gpa":  "'.$gpa.'",
    //   "ACR": "<span>ACR_'.$nb_programa.' '.$all.'</span>",
    //   "AGR": "<span>'.$all.'</span>",
    //   "ACT": "<span>'.$fg_activo.' '.$all.'</span>",
    //   "assessment":  "<a href=\'javascript:actions('.ASSESSMENT.', 00, '.$fl_usu_pro.', '.$confirmado.')\'><span class=\'label label-success\'>'.$assessment.'</span></a>",
    //   "myself": "'.$myself.'"
    // }';
    // if($i<=($numeroderegistros-1))
    //     echo ",";
    //   else
    //     echo "";
}

# End of MAIN FOR LOOP
$tablaJson = '{"data": ['.$registros.']}';
# Entrega el resultado para mostrar en la tabla
echo $tablaJson;
// echo "]}";
?>
