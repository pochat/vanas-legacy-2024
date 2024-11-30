<?php   
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
  # Query que obtiene los usuarios dependiedo de la intitucion
  # Adm Muestra teacher y students
  # Teacher Muestra los students
  $Query  = "SELECT fl_usuario, ds_ruta_avatar, ds_nombres, nb_perfil, status, fe_sesion, 'usage', fg_activo, fl_perfil_sp, ";
  $Query .= "nb_grupo, nb_programa".$sufix.", fl_programa_sp, confirmado, ds_progreso, no_promedio_t, fg_confirmado,  ";
  $Query .= "CASE fg_status_pro WHEN '1' THEN '<b class=\'text-warning\'><i class=\'fa fa-pause\'></i> ".ObtenEtiqueta(1999)."</b>' END fg_status_pro, ";
  $Query .= "fl_usu_pro, fg_assign_myself_course,fg_asignado_playlist,fl_playlist,ds_email FROM ( ";
  $Query .= "(SELECT usr.fl_usuario, al.ds_ruta_avatar, CONCAT(usr.ds_nombres, ' ', usr.ds_apaterno ) ds_nombres, cpr.nb_perfil, ";
  $Query .= "CASE usr.fg_activo WHEN 1 THEN 'Active' ELSE 'Inactive' END status, ";
  $Query .= "DATE_FORMAT((CASE WHEN usr.fe_ultacc is null THEN usr.fe_alta ELSE usr.fe_ultacc END), '%Y-%m-%d %H:%i:%s') fe_sesion, '0.01 GB' 'usage', usr.fg_activo, usr.fl_perfil_sp, ";
  $Query .= "al.nb_grupo, ";
  $Query .= "IFNULL(cpro.nb_programa".$sufix.", '".ObtenEtiqueta(1039)."') nb_programa".$sufix.", cpro.fl_programa_sp, '1' confirmado, usrp.ds_progreso, usrp.no_promedio_t, ";
  $Query .= "'1' fg_confirmado, fg_status_pro, fl_usu_pro, fg_assign_myself_course,usrp.fg_asignado_playlist,usrp.fl_playlist,usr.ds_email ";
  $Query .= "FROM c_usuario usr ";
  $Query .= "LEFT JOIN c_alumno_sp al ON(al.fl_alumno_sp=usr.fl_usuario) ";
  $Query .= "LEFT JOIN k_usuario_programa usrp ON(usrp.fl_usuario_sp=usr.fl_usuario) ";
  $Query .= "LEFT JOIN c_programa_sp cpro ON(cpro.fl_programa_sp=usrp.fl_programa_sp), c_perfil cpr ";
  $Query .= "WHERE usr.fl_perfil_sp=cpr.fl_perfil AND usr.fl_instituto=$fl_instituto) ";
  $Query .= "UNION ";
  $Query .= "(SELECT a.fl_envio_correo fl_usuario, '' ds_ruta_avatar, CONCAT(ds_first_name, ' ', ds_last_name ) ds_nombres, '".ObtenEtiqueta(1039)."'  nb_perfil, ";
  $Query .= "'".ObtenEtiqueta(1092)."' status, DATE_FORMAT(fe_alta, '%Y-%m-%d %H:%i:%s') fe_sesion, '0.0 GB' 'usage', '0' fg_activo, '0' fl_perfil_sp, ";
  $Query .= "nb_grupo, IFNULL(c.nb_programa".$sufix.", '".ObtenEtiqueta(1039)."') nb_programa".$sufix.", b.fl_programa_sp, '0' confirmado, '0' ds_progreso, ";
  $Query .= "'0' no_promedio_t, a.fg_confirmado, '0' fg_status_pro, '0' fl_usu_pro, '0' fg_assign_myself_course,b.fg_asignado_playlist,b.fl_playlist,a.ds_email ";
  $Query .= "FROM k_envio_email_reg_selfp a ";
  $Query .= "LEFT JOIN k_noconfirmados_pro b ON(a.fl_envio_correo=b.fl_envio_correo) ";
  $Query .= "LEFT JOIN c_programa_sp c ON(c.fl_programa_sp=b.fl_programa_sp) ";
  $Query .= "WHERE fl_invitado_por_instituto=$fl_instituto AND fg_enviado='1' AND fg_tipo_registro='S' AND fg_confirmado='0') ";
  $Query .= ") AS students WHERE 1=1 AND fl_perfil_sp = ".PFL_ESTUDIANTE_SELF." OR fl_perfil_sp='0' ORDER BY fe_sesion DESC LIMIT 10";

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [
  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
    $fl_usuario = $row[0];
    $ds_ruta_avatar = $row[1];    
    $ds_nombres = $row[2];
    $nb_perfil    = $row[3];
    $status = $row[4];
    $fe_sesion = $row[5];  
    $usage = $row[6];
    $fg_activo = $row[7];
    $fl_perfil = $row[8];
    $ds_email=$row['ds_email'];
    
    #Para saber tiene programa
    if(!empty($row[11]))
        $p=$row[11];
    else
        $p="";
    
    
    
    if(empty($fl_perfil))
      $fl_perfil = "Unassigned";
    # Este valor nos indica si ya confirmo o no sera utiizado para la asignacion y cambio de grupo
    $confirmado = $row[12];
    $nb_grupo = $row[9];
    # Asignar a un grupo
    if(empty($nb_grupo)){
      $nb_grupo = "<a href='javascript:actions(".ASG_GROUP.", 00, $fl_usuario, $confirmado);'> ".ObtenEtiqueta(1039)." </a>";
    }
    else{
        $nb_grupo = "<a href='javascript:actions(".CAM_GROUP.", 00, $fl_usuario, $confirmado);' title='Change Group'>$nb_grupo</a>";
    }
    // $nb_programa = $row[10];
    // if(empty($nb_programa))
    $nb_programa = "<a href='javascript:actions(".ASG_COURSE.", 00, $fl_usuario, $confirmado);'>".$row[10]."</a>";    
    $fl_programa = $row[11];
    if(empty($fl_programa)){
      $fl_programa = ObtenEtiqueta(1039);
    }
    $ds_progreso = $row[13];
    if(empty($ds_progreso))
      $ds_progreso = 0;
    
    $no_promedio_t = round($row[14]);
    
    if($fl_programa==33){
      $no_promedio_t=round($ds_progreso);
    }
    # Si esta confirmado mostrara la calificacion
    // if($no_promedio_t==""){
      // $no_promedio_t = ObtenPromedioPrograma($row[11], $fl_usuario);
    // }    
    // $cl_calificiacion = ObtenCalificacion($no_promedio_t);
    $Queryg = "SELECT cl_calificacion, fg_aprobado FROM c_calificacion_sp WHERE no_min <= ROUND($no_promedio_t) AND no_max >= ROUND($no_promedio_t)";
    $rowg = RecuperaValor($Queryg);
    $cl_calificacion = $rowg[0];
    $aprovado = $rowg[1];
	
	$Query5="SELECT ds_calificacion FROM c_calificacion_sp WHERE cl_calificacion='$cl_calificiacion' ";
	$row5=RecuperaValor($Query5);
	$nb_calificacion=str_texto($row5[0]);
	
    // $aprovado = ObtenCalificacionAprobada($no_promedio_t);
    if($aprovado)
      $cal_color = "success";
    else
      $cal_color = "danger";
    if(!empty($row[11]))     
      $gpa = '<span class=\'label label-'.$cal_color.'\'>'.$cl_calificiacion.' ('.$no_promedio_t.'%) '.$nb_calificacion.'</span>';
    else
      $gpa = ObtenEtiqueta(1039);
    $fg_confirmado = $row[15];
    $fg_status_pro = $row[16];
    $fl_usu_pro = $row[17]; 
    $fg_assign_myself_course = $row[18];
    $fg_asignado_play_list=$row[19];
    $fl_playlist=$row[20];
    
    if($fg_asignado_play_list==1){
        
        #Recuperamos el enombre del playlist.
        $Que="SELECT nb_playlist FROM c_playlist WHERE fl_playlist=$fl_playlist ";
        $rop=RecuperaValor($Que);
        $nb_playlist=str_texto($rop[0]);
        
        $fg_etq_play_list="Playlist: ".$nb_playlist;
        
    }else
        $fg_etq_play_list="";
    
    
    # Quiere decir que aun no a confrmado
    if(empty($fg_confirmado) || empty($fl_usu_pro))
      $fl_usu_pro = $fl_usuario;   
    
    if(empty($fg_activo)){
      $color = "danger";
      $active = "busy";
    }
    else{
      $color = "success";
      $active = "online";
    }
    if(!empty($ds_ruta_avatar))
      $ruta_avatar = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/".$ds_ruta_avatar;
    else
      $ruta_avatar = SP_IMAGES.'/avatar_default.jpg';
    
    $all = "ALL";
    
    # Enviamos que tiempo hay desde su ultima conexion
    if(!empty($fg_confirmado))
      $fe_sesion = time_elapsed_string($fe_sesion, false);
    else
      $fe_sesion = ObtenEtiqueta(1091);
    
    # Verificamos que los teacher no utilizan licencias
    if(!empty($fg_confirmado)){
      if($fl_perfil == PFL_MAESTRO_SELF)
        $use_licence = ObtenEtiqueta(1105);
      else{
        if(!empty($fg_activo))
          $use_licence = ObtenEtiqueta(1103);
        else
          $use_licence = ObtenEtiqueta(1104);
      }
    }else{
      $use_licence = ObtenEtiqueta(1104);
    }
    
    # Obtenemos los valores del Quizes o teacher grade
    if(ExisteEnTabla('k_details_usu_pro', 'fl_usu_pro', $fl_usu_pro)){
      $row1 = RecuperaValor("SELECT fg_quizes, fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=".$fl_usu_pro);
      $fg_quizes = $row1[0];
      if($fg_quizes){
        $ds_quizes = ObtenEtiqueta(1916);
         $icono_pul = '';
      }
      $fg_grade_tea = $row1[1];
      if($fg_grade_tea){
        $icono_pul = '<i class=\'fa fa-plus\'></i>';
        $ds_grade_tea = ObtenEtiqueta(1917);
      }
      # Por defaul estara quiz
      # SI tiene activado fg_grade_tea significa que el teacher lo calficara
      if(!empty($fg_quizes) && !empty($fg_grade_tea))
        $assessment = $ds_quizes.' '.$icono_pul.' '.$ds_grade_tea;
      else
        $assessment = $ds_quizes;
    }
    else{      
      $assessment = ObtenEtiqueta(1916);
    }
    
	
	#Recuperamos el gradod del estudiante 
		$Query="SELECT  nb_grado
				FROM c_alumno_sp A  
				JOIN k_grado_fame K ON K.fl_grado=A.fl_grado
				WHERE A.fl_alumno_sp =$fl_usuario";
		$row=RecuperaValor($Query);
		$nb_grado=str_texto($row[0]);
    
    # Verificamos si el usuario puede asignase solo
    if(!empty($fg_confirmado)){
      if(!empty($fg_assign_myself_course))
        $myself = "<a href='javascript:myself(".$fl_usuario.",1);'><span class='label label-success'>".ObtenEtiqueta(16)."</span></a>";
      else
        $myself = "<a href='javascript:myself(".$fl_usuario.",0);'><span class='label label-danger'>".ObtenEtiqueta(17)."</span></a>";
    }
    else{
      $myself = "";
      
      
      
      
    }

   
    #Recuperamos el mail
    if(empty($fg_activo)){
    
       #Verificamos que no falte, 
        $Query="SELECT a.fl_envio_correo,CASE WHEN r.fg_autorizado ='0' THEN 'FA' ELSE  r.fg_autorizado END fg_autorizado ,fe_reenvio,fg_confirmado 
                FROM k_envio_email_reg_selfp a
                LEFT JOIN k_responsable_alumno r ON r.fl_envio_correo=a.fl_envio_correo WHERE a.ds_email='$ds_email' ";
        $row=RecuperaValor($Query);
        $fl_envio_co=$row[0];
        $fg_autoriza=str_texto($row[1]);
		$fe_renvi=$row[2];
		$fe_reenvio=time_elapsed_string($row[2],false);
        
        if($fg_autoriza=='FA'){
        $color="danger";
        $status=ObtenEtiqueta(2126);
		$date_adicional='-FA';//falta autorizacion.
        }


        #La cuenta ni siquiera ha sido confirmada.
		if(empty($fg_confirmado)){
            $date_adicional='-NC';
        }
        
		if(!empty($fe_renvi)){
			$fe_renvio=ObtenEtiqueta(2309).": ".$fe_reenvio;
		}else{
		    $fe_renvio="";
		}
        
    
    }else{
		$date_adicional="";
		$fe_renvio="";
		
	}
    
    
    
    $Query="SELECT fl_maestro FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa ";
    $rowm=RecuperaValor($Query);
    $fl_mae=$rowm[0];

    $Query="SELECT CONCAT(ds_nombres,' ',ds_apaterno)AS teacher FROM c_usuario WHERE fl_usuario=$fl_mae ";
    $rop=RecuperaValor($Query);
    $teacher=$rop[0];

	
    /** ESTE PROCESO PARA AYUDAR A LA BUSQUEDA AVANZADA **/    
    echo '
    {
      "checkbox": "<label class=\'checkbox no-padding no-margin\'><input class=\'checkbox\' type=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$fl_usuario.''.$date_adicional.'\'><span></span></label><input type=\'hidden\' id=\'use_lic'.$i.'\' name=\'use_lic'.$i.'\' value=\'1\'>",
      "id": "<div class=\'project-members\'><input type=\'checkbox\' id=\'user_'.$i.'\' class=\'checkbox\'><a href=\'javascript:void(0)\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$ds_nombres.'\'><img src=\''.$ruta_avatar.'\' class=\''.$active.'\' alt=\''.$ds_ruta_avatar.'\' style=\'width:25px;\'></a> </div> ",
      "name": "<a href=\'index.php#site/users_details.php?clave='.$fl_usu_pro.'&c='.$fg_confirmado.'&p='.$p.'\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.ObtenEtiqueta(1897).'\'>'.$ds_nombres.'</a> <input type=\'hidden\' id=\'ds_nombres_'.$i.'\' value=\''.$ds_nombres.'\'>",
	  "course": "'.$ds_nombres.'",
	   "grade": "'.$nb_grado.'",
      "grupo": "'.$nb_grupo.' <label class=\'hidden\'>'.$all.'</label> <br><small class=\'text-muted\'>Current  teacher: '.$teacher.'</small><input type=\'hidden\' id=\'confirmado_'.$i.'\' value=\''.$confirmado.'\'>",
      "programa": "'.$nb_programa.'<br><small class=\'text-muted\'><i>'.$fg_etq_play_list.'</i></small> <input type=\'hidden\' id=\'fl_programa_std_'.$i.'\'  value=\''.$fl_programa.'\'> <input type=\'hidden\' id=\'fl_usu_pro_'.$i.'\' name=\'fl_usu_pro_'.$i.'\' value=\''.$fl_usu_pro.'\'>",
      "status": "<span class=\'label label-'.$color.'\'>'.$status.'</span><br><small class=\'text-muted\'> '.$fe_renvio.'</small>",
      "lastlogin": "<span>'.$fe_sesion.'</span>",
      "use_licence": "<span>'.$use_licence.'</span>", 
      "progress": "<div class=\'progress progress-xs\' data-progressbar-value=\''.$ds_progreso.'\'><div class=\'progress-bar\'></div></div><a href=\'javascript:Pause_Course('.$fl_usu_pro.',0,0);\'>'.$fg_status_pro.' <span class=\'hidden\'>'.$ds_progreso.'</span></a>",
      "gpa":  "'.$gpa.'",
      "ACR": "<span>ACR_'.$nb_programa.' '.$all.'</span>",
      "AGR": "<span>'.$all.'</span>",
      "ACT": "<span>'.$fg_activo.' '.$all.'</span>",
      "assessment":  "<a href=\'javascript:actions('.ASSESSMENT.', 00, '.$fl_usu_pro.', '.$confirmado.')\'><span class=\'label label-success\'>'.$assessment.'</span></a>",
      "myself": "'.$myself.'"
    }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>
  ]
}