<?php

  # Estas funciones pueden ser utitilizandas en el back como el front
  # Por el momento solo son layout
  
  function ObtenAvatarUsuario($p_usuario, $fg_front=true) {
  
    # Recupera el perfil del usuario    
    $row0 = RecuperaValor("SELECT fl_perfil_sp, fl_instituto FROM c_usuario WHERE fl_usuario=$p_usuario");
    $fl_perfil = !empty($row0[0])?$row0[0]:NULL;
    $fl_instituto = !empty($row0[1])?$row0[1]:NULL;
    
    if($fg_front==true)
      $image = SP_IMAGES;
    else
      $image = SP_IMAGES_W;
    
    # Ruta del avatar
    $ruta = PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$p_usuario."/";
    
    # Verifica si el usuario tiene un avatar
    if($fl_perfil == PFL_MAESTRO_SELF) {
      $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_maestro_sp WHERE fl_maestro_sp=$p_usuario");
      if(!empty($row[0]))
        $ds_ruta_avatar = $ruta.$row[0];
      else
        $ds_ruta_avatar = $image."/".'avatar_default.jpg';
    }
    else {
      if($fl_perfil==PFL_ESTUDIANTE_SELF){
      $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_alumno_sp WHERE fl_alumno_sp=$p_usuario");
        if(!empty($row[0]))
          $ds_ruta_avatar = $ruta.$row[0];
        else
        $ds_ruta_avatar = $image."/".ObtenNombreImagen(203);
      }
      else{
        $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_administrador_sp  WHERE fl_adm_sp=$p_usuario");
        if(!empty($row[0]))
          $ds_ruta_avatar = $ruta.$row[0];
        else
        $ds_ruta_avatar = $image."/".ObtenNombreImagen(203);
      }
    }
    return $ds_ruta_avatar;
  }

  function ObtenAvatarUsrVanas($p_usuario) {
  
    # Recupera el perfil del usuario    
    $row0 = RecuperaValor("SELECT fl_perfil FROM c_usuario WHERE fl_usuario=$p_usuario");
    $fl_perfil = !empty($row0[0])?$row0[0]:NULL;
    
    # Ruta default
    $image = SP_IMAGES;
    
    # Verifica si el usuario tiene un avatar
    if($fl_perfil == PFL_MAESTRO) {
      $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_maestro WHERE fl_maestro=$p_usuario");
      if(!empty($row[0]))        
        $ds_ruta_avatar = PATH_MAE_IMAGES."/avatars/".$row[0];
      else
        $ds_ruta_avatar = $image."/".'avatar_default.jpg';
    }
    else {
      
      $row = RecuperaValor("SELECT ds_ruta_avatar FROM c_alumno WHERE fl_alumno=$p_usuario");
      if(!empty($row[0]))
        $ds_ruta_avatar = PATH_ALU_IMAGES."/avatars/".$row[0];
      else
      $ds_ruta_avatar = $image."/".ObtenNombreImagen(203);
      
    }
    return $ds_ruta_avatar;
  }

  function ObtenFotoUsrVanas($p_usuario) {
  
    # Recupera el perfil del usuario    
    $row0 = RecuperaValor("SELECT fl_perfil FROM c_usuario WHERE fl_usuario=$p_usuario");
    $fl_perfil = $row0[0];
    
    define('IMG_T_FOTO_DEF', 'vanas-family-edutisse-header.jpg');
    
    # Ruta default
    $image = PATH_N_COM_IMAGES;
    
    # Verifica si el usuario tiene un avatar
    if($fl_perfil == PFL_MAESTRO) {
      $row = RecuperaValor("SELECT ds_ruta_foto FROM c_maestro WHERE fl_maestro=$p_usuario");
      if(!empty($row[0]))        
        $ds_ruta_foto = PATH_MAE_IMAGES."/pictures/".$row[0];
      else
        $ds_ruta_foto = $image."/".IMG_T_FOTO_DEF;
    }
    else {
      
      $row = RecuperaValor("SELECT ds_ruta_foto FROM c_alumno WHERE fl_alumno=$p_usuario");
      if(!empty($row[0]))
        $ds_ruta_foto = PATH_ALU_IMAGES."/pictures/".$row[0];
      else
      $ds_ruta_foto = $image."/".IMG_T_FOTO_DEF;
      
    }
    return $ds_ruta_foto;
  }

  
  # Funcion para mostrar la informacion general del usuario
  function Profile_pic_FAME($fl_usuario, $fl_programa_sp, $no_session=0, $fl_maestro, $fg_front=true){

    # Aded by Ulises, select language and apply a sufix for the selection of the right lang-table on the DB
    $sufix=langSufix();
    
    # Obtenemos el nombre del usuario
    $row0 = RecuperaValor("SELECT  CONCAT( ds_nombres,' ', ds_apaterno ) FROM c_usuario WHERE fl_usuario=$fl_usuario");
    $ds_nombres = str_texto($row0[0]);
    # Obtenemos nombre del curso
    $row1 = RecuperaValor("SELECT nb_programa".$sufix." FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp");
    //$ds_programa = str_texto($row1[0]);
    $ds_programa = htmlentities((!empty($row1[0])?$row1[0]:NULL), ENT_QUOTES, "UTF-8");
    
    # Obtenemos avatar
    $avatar = ObtenAvatarUsuario($fl_usuario, $fg_front);
    
    # Obtenemos nombre del maestro
    $row3 = RecuperaValor("SELECT  CONCAT( ds_nombres,' ', ds_apaterno ) FROM c_usuario WHERE fl_usuario=$fl_maestro");
    $ds_maestro = str_texto(!empty($row3[0])?$row3[0]:NULL);
    
    # Obtenemos grupo
    $nb_grupo = ObtenGrupoUser($fl_usuario);
    
    # Obtenemos informacion de quien loinvito    
    $Query0  = "SELECT a.fl_usuario, a.fl_usu_invita, ";
    $Query0 .= "(SELECT CONCAT(r.ds_nombres, ' ', r.ds_apaterno) FROM c_usuario r WHERE r.fl_usuario=a.fl_usu_invita) ";
    $Query0 .= "FROM c_usuario a  WHERE a.fl_usuario= $fl_usuario ";
    $row0 = RecuperaValor($Query0);
    $fl_usu_invita = $row0[1];
    $user_invited = str_texto($row0[2]);
    # Perfil del usuario
    $row1 = RecuperaValor("SELECT  b.nb_perfil FROM c_usuario a, c_perfil b WHERE a.fl_perfil_sp=b.fl_perfil AND a.fl_usuario=".$fl_usu_invita);
    $nb_perfil = !empty($row1[0])?$row1[0]:NULL;
    
    $invited = $user_invited." (".$nb_perfil.")";
    
    # Informacion
    # front
    if($fg_front==true){
      echo "
      <div class='carousel-inner profile-pic' style='background-color:rgba(255, 255, 255, 0.82);'>
        <div id='user-profile-container' class='profile-container no-margin'>
        <img class='avatar' src='".$avatar."' height='70' width='70'>
        <div class='info'>
          <div class='username no-margin'>&nbsp;".$ds_nombres."</div>
          <div class='text no-margin'><strong>".ObtenEtiqueta(1878)."</strong>&nbsp;".$ds_programa."</div>
          <div class='text no-margin'><strong>".ObtenEtiqueta(1965)."</strong>&nbsp;".$nb_grupo."</div>";
          if($no_session>0){
            echo "
            <div class='text no-margin'><strong>".ObtenEtiqueta(1879)."</strong>&nbsp;".$no_session."</div>";
          }
      echo "
          <div class='text no-margin'><strong>".ObtenEtiqueta(1880)."</strong>&nbsp;".$ds_maestro."</div>               
          <div class='text no-margin'><strong>".ObtenEtiqueta(2000)."</strong>&nbsp;".$invited."</div>
        </div>
        </div>                  
      </div>";
    }
    else{
      echo "
       <div class='col-sm-3'>
         <img src='".$avatar."' style='height:70px;'  alt='' class='img-rounded' >
       </div>
       <div class='col-sm-9 text-align-right'>
          <div class='info'>
            <div class='username no-margin'><h3 class='no-margin'>".$ds_nombres."</h3></div>
            <div class='text no-margin'><strong>".ObtenEtiqueta(1878)."</strong>&nbsp;".$ds_programa."</div>
            <div class='text no-margin'><strong>".ObtenEtiqueta(1880)."</strong>&nbsp;".$ds_maestro."</div>
          <div class='text no-margin'><strong>".ObtenEtiqueta(2000)."</strong>&nbsp;".$invited."</div>            
          </div>
       </div>";
    }
  }

  # Funcion para otener el grupo
  function ObtenGrupoUser($fl_usuario){
    
    # Obtenemos el perfil del usuario
    $row0 = RecuperaValor("SELECT fl_perfil_sp FROM c_usuario WHERE fl_usuario=$fl_usuario");
    $fl_perfil = $row0[0];
    
    if($fl_perfil == PFL_ESTUDIANTE_SELF){
      $row = RecuperaValor("SELECT nb_grupo FROM c_alumno_sp WHERE fl_alumno_sp=$fl_usuario");
      $nb_grupo = $row[0];
    }
    else{
      $nb_grupo = ObtenEtiqueta(1039);
    }
    return $nb_grupo;
  }

  
  
  
/**
 * MJD #funcion que recupera el nombre del plan seleccionado /por default soloexiste essencial en FAME.
 * @param 
 * 
 */
  
  function ObtenNombrePlanFame($p_instituto){

      $Query="SELECT P.nb_plan FROM c_instituto I
        JOIN c_plan_fame P ON P.cl_plan_fame=I.cl_plan_fame
        WHERE fl_instituto=$p_instituto ";
      $row=RecuperaValor($Query);
      $nb_plan=str_texto($row[0]);
      return $nb_plan;
  }
  
  /**
 * MJD #funcion que nos indica si el Instituto ya expiro su perido en FAME.
 * @param 
 * 
 */
   function SaberSiYaExpiroPlan($p_instituto){
  
  
     
          #Verificamos si, se ecnuantra en modo trial o en plan
          $fl_instituto=ObtenInstituto($fl_usuario);
          $Query="SELECT fg_tiene_plan FROM c_instituto WHERE fl_instituto=$p_instituto ";
          $row=RecuperaValor($Query);
          $fg_tiene_plan=$row[0];
          
          #Obtenemos fecha actual :
          $Query = "Select CURDATE() ";
          $row = RecuperaValor($Query);
          $fe_actual = str_texto($row[0]);
          $fe_actual=strtotime('+0 day',strtotime($fe_actual));
          $fe_actual= date('Y-m-d',$fe_actual);
          
          #Institutos que ya tuvieron plan
          if($fg_tiene_plan==1){ 
              $fe_terminacion= ObtenFechaFinalizacionContratoPlan($p_instituto);
          }else{
              #Institutos que se quedaron en modo de prueba.                      
              $fe_terminacion=ObtenFechaFinalizacionTrial($p_instituto); 
          }
          
          if($fe_terminacion < $fe_actual)
              $ya_expiro_fecha=true;
          else
              $ya_expiro_fecha=false;
          
          
          
    
          return $ya_expiro_fecha;
  
  
  }
  /**
 * MJD #funcion que nos indica si el Instituto realiza pagos manuales(No requiere Stripe).
 * @param 
 * 
 */ 
  function ObtenMetodoPagoInstituto($fl_instituto){
	  
	  $Query="SELECT fg_pago_manual FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
	  $ro=RecuperaValor($Query);
	  $cl_metodo_pago=$ro['fg_pago_manual'];
	  
	  return $cl_metodo_pago;
	  
	  
  }
  
  
?>
