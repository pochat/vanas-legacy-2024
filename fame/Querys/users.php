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
  
  # Recibe Parametros
  $fl_users = isset($_POST['fl_users'])?$_POST['fl_users']:NULL;
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);

  $fl_usuario_logueado=$fl_usuario;

  # Recover the value of the user selected to retrieve in the query
  $selected = (!empty($_GET['selected'])?$_GET['selected']:0);

  $Query = "";
  if ($selected != 0) {
    $Query .= "SELECT * FROM ( ";
  }

  # Query que obtiene los usuarios dependiedo de la intitucionx 
  # Adm Muestra teacher y students
  # Teacher Muestra los students
  $Query  .= "SELECT fl_usuario, ds_ruta_avatar, ds_nombres, nb_perfil, status, fe_sesion, no_usage, fg_activo, fl_perfil_sp, fg_confirmado, fl_instituto, fl_usu_pro, fl_programa_sp FROM ( ";
  $Query .= "(SELECT a.fl_usuario, CASE a.fl_perfil WHEN ".PFL_MAESTRO_SELF."  THEN ma.ds_ruta_avatar ELSE st.ds_ruta_avatar END ds_ruta_avatar, ";
  $Query .= "CONCAT( a.ds_nombres,' ', a.ds_apaterno ) ds_nombres, ";
  $Query .= "c.nb_perfil, CASE a.fg_activo WHEN '1' THEN 'Active' ELSE 'Inactive' END status, ";
  $Query .= "DATE_FORMAT(IFNULL(fe_sesion, fe_ultacc), '%Y-%m-%d %H:%i:%s') fe_sesion, a.no_usage, a.fg_activo, a.fl_perfil_sp, '1' fg_confirmado, a.fl_instituto, kup.fl_usu_pro, kup.fl_programa_sp ";
  $Query .= "FROM c_usuario a LEFT JOIN c_maestro_sp ma ON(a.fl_usuario = ma.fl_maestro_sp) ";
  $Query .= "LEFT JOIN k_usuario_programa kup ON(kup.fl_usuario_sp=a.fl_usuario) LEFT JOIN c_alumno_sp st ON(a.fl_usuario = st.fl_alumno_sp), c_perfil c ";
  $Query .= "WHERE a.fl_perfil_sp = c.fl_perfil AND ";
  if($fl_perfil_sp==PFL_ADM_CSF){
      $Query .= " ( a.fl_instituto=$fl_instituto OR a.fl_instituto IN(SELECT z.fl_instituto FROM c_instituto z WHERE fl_instituto_rector=$fl_instituto ) )  ";
  }else{
      $Query .= "  a.fl_instituto=$fl_instituto ";
  }
  $Query .=" AND a.fl_usuario<>".$fl_usuario.") ";
  $Query .= "UNION ";
  $Query .= "(SELECT a.fl_envio_correo fl_usuario, '' ds_ruta_avatar, CONCAT(a.ds_first_name, ' ', ds_last_name ) ds_nombres, ";
  $Query .= "fg_tipo_registro  nb_perfil, '".ObtenEtiqueta(1092)."' status, ";
  $Query .= "DATE_FORMAT(a.fe_alta, '%Y-%m-%d %H:%i:%s') fe_sesion, '0' no_usage, '0' fg_activo, '0' fl_perfil_sp, a.fg_confirmado, '0' fl_instituto, '0' fl_usu_pro, '0' fl_programa_sp ";
  $Query .= "FROM k_envio_email_reg_selfp a ";
  $Query .= "WHERE a.fl_invitado_por_instituto=$fl_instituto AND a.fg_enviado='1' AND a.fg_confirmado='0'  ";
  $Query .= "  and not EXISTS(select z.fl_usuario FROM c_usuario z where z.fl_usuario=a.fl_usuario ) ";
  $Query .= " ) ";
  $Query .= " )AS USERS WHERE 1=1 ";

   if ($selected == 0) {
    $Query .= "GROUP BY fl_usuario ";
  }

  # Si es Maestro solo muestra los students
  if($fl_perfil_sp == PFL_MAESTRO_SELF)
    $Query .= " AND fl_perfil_sp = ".PFL_ESTUDIANTE_SELF." OR fl_perfil_sp='0' ";
  $Query .= "ORDER BY fe_sesion DESC";

  if ($selected != 0) {
    $Query .= " ) AS USER WHERE fl_usuario=".decriptClave($selected). " /*LIMIT 50 OFFSET 500*/ ";
  }
  
  // echo $Query;
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
    if($nb_perfil == 'S')
      $nb_perfil = ObtenEtiqueta(1100);
    if($nb_perfil == "T")
      $nb_perfil = ObtenEtiqueta(1101);
    if($nb_perfil == "A")
      $nb_perfil = ObtenEtiqueta(1102);
    $status = $row[4];   
    $fe_sesion = $row[5];    
    $usage = $row[6];
    if(empty($usage))
      $usage = "0 %";
    else
      $usage = $usage." %";
    $fg_activo = $row[7];
    $fl_perfil = $row[8];
    if($fl_perfil == PFL_ADMINISTRADOR)
      $fl_perfil = "AD";
    if(empty($fl_perfil))
      $fl_perfil = "Unassigned";
    if(empty($fg_activo)){
      $color = "danger";
      $img_color = "busy";
    }
    else{
      $color = "success";
      $img_color = "online";
    }
    
    #Para saber tiene programa
    if (!empty($row['fl_programa_sp']))
        $p = $row['fl_programa_sp'];
    else
        $p = "";

    $perfil_searh = "ALL";
    $fg_confirmado = $row[9];
    $fl_instituto = $row[10];
    $fl_usu_pro = $row[11];
    # Quiere decir que aun no a confrmado
    if(empty($fg_confirmado) || empty($fl_usu_pro))
      $fl_usu_pro = $fl_usuario;
    $fl_programa_sp = $row[12];
    $nb_programa = ObtenNombreCourse($fl_programa_sp);
    if(empty($fl_programa_sp)){
        $nb_programa = "<label class='text-danger'>".ObtenEtiqueta(1920)."</label>";
        $url_desktop="javascript:void(0);";
        $etq_desktop="";
    }else{
        
        $url_desktop='#site/desktop.php?student='.$fl_usuario.'&fl_programa='.$fl_programa_sp.'&t=1';
        $etq_desktop=ObtenEtiqueta(2573);

    }
    # Ruta de las imagenes
    $ruta_avatar = ObtenAvatarUsuario($fl_usuario);
    
    # Enviamos que tiempo hay desde su ultima conexion
    if(!empty($fg_confirmado))
      $fe_sesion = time_elapsed_string($fe_sesion, false);
    else
      $fe_sesion = ObtenEtiqueta(1091);
    
    # Verificamos que los teacher no utilizan licencias
    if(!empty($fg_confirmado)){
      if($fl_perfil == PFL_MAESTRO_SELF){
        $use_licence = ObtenEtiqueta(1105);
        // $nb_perfil = "<a href='javascript:cambiar_perfil($fl_usuario, ".PFL_ADMINISTRADOR.");' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1123)."'>".$nb_perfil."</a>";
        $nb_perfil = $nb_perfil;
        $use_lic = 0;
      }
      else{
        $use_licence = ObtenEtiqueta(1103);
        $use_lic = 1;
        if($fl_perfil == "AD"){
          // $nb_perfil = "<a href='javascript:cambiar_perfil($fl_usuario, ".PFL_MAESTRO_SELF.");' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1123)."'>".$nb_perfil."</a>";
          $nb_perfil = $nb_perfil;
          $use_licence = ObtenEtiqueta(1105); 
          $use_lic = 0;
        }
      }
    }else{
      $use_licence = ObtenEtiqueta(1104);
      $use_lic = 0;
    }    
    
		#Recuperamos el gradod del estudiante 
		$Query="SELECT  nb_grado FROM c_alumno_sp A 
        JOIN k_grado_fame K ON K.fl_grado=A.fl_grado
				WHERE A.fl_alumno_sp =$fl_usuario";
		$row=RecuperaValor($Query);
		$nb_grado=str_texto(!empty($row[0])?$row[0]:NULL);

    $url_perfil="#site/myprofile.php?profile_id=".$fl_usuario."&c=1&uo=".$fl_usuario_logueado."";

    if ($selected ==0) {
      $liga_href = 'index.php#site/users.php?selected='.encriptClave($fl_usuario);
    } else {
      $liga_href = "index.php#site/users_details.php?clave=".encriptClave($fl_usu_pro)."&c=".$fg_confirmado."&p=".$p;
    }


    #Identificamos el usuario CSF para mostrar el status.(Aqui se mostrara que los usuarios ya an sido cargados, mostrar si ya se le envio invitacion o aun no.)
    $Query="SELECT fg_scf FROM c_usuario WHERE fl_usuario=$fl_usuario ";
    $row=RecuperaValor($Query);
    $fg_scf=!empty($row['fg_scf'])?$row['fg_scf']:NULL;
    if($fg_scf=='1'){
        
        $Query="SELECT fg_scfenvio_email,fg_confirmado FROM k_envio_email_reg_selfp WHERE fl_usuario=$fl_usuario ";
        $roy=RecuperaValor($Query);
        $fg_scfenvio_emailg=!empty($roy['fg_scfenvio_email'])?$roy['fg_scfenvio_email']:NULL;
        $fg_scf_confirmado=!empty($roy['fg_confirmado'])?$roy['fg_confirmado']:NULL;

        if($fg_scfenvio_emailg=='1'){

            if($fg_scf_confirmado=='1'){
                
            }else{
                $color="warning";
                $status=ObtenEtiqueta(2591);//Esperando confirmacion, ya se envio el email

            }
        }else{
            
            $color="danger";
            $status=ObtenEtiqueta(2589);//Esperando envio de invitacion.
        }
    }

    /** ESTE PROCESO PARA AYUDAR A LA BUSQUEDA AVANZADA **/    
    echo '
    {
      "checkbox": "<label class=\'checkbox no-padding no-margin\'><input class=\'checkbox\' type=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$fl_usuario.'\'><span></span></label>",
      "id": "<div class=\'project-members\'><a href=\''.$url_perfil.'\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$ds_nombres.'\'><img src=\''.$ruta_avatar.'\' class=\''.$img_color.'\' alt=\''.$ds_ruta_avatar.'\' style=\'width:25px;\'></a> </div> ",
      "name": "<a href=\''.$liga_href.'\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'Details\'>'.$ds_nombres.'</a> <input type=\'hidden\' id=\'confirmado_'.$i.'\' name=\'confirmado_'.$i.'\' value=\''.$fg_confirmado.'\'>",
       "grade": "'.$nb_grado.'",
	  "programa": "'.$nb_programa.' <br><small class=\'text-muted\'><i><a href=\''.$url_desktop.'\'>'.$etq_desktop.'</a></i></small>",      
      "perfil": "'.$nb_perfil.'",      
      "status": "<span class=\'label label-'.$color.'\'>'.$status.'</span>",
      "lastlogin": "<span>'.$fe_sesion.'</span>",
      "usage": "<span>'.$usage.'</span>",     
      "use_licence": "<span>'.$use_licence.'</span> <input type=\'hidden\' id=\'use_lic'.$i.'\' name=\'use_lic'.$i.'\' value=\''.$use_lic.'\'>",     
      "blank": "<span></span>",
      "perfil_search_st_te": "'.$fl_perfil.''.$perfil_searh.'", 
      "status_search_st_te": "'.$fg_activo.''.$perfil_searh.'"
    }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  echo "]}";
  ?>
