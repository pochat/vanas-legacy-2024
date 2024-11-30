<?php
	# Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

	# Recibe parametros
  $fl_alumno = RecibeParametroNumerico('profile_id');
  if(empty($fl_alumno))
    $fl_alumno = RecibeParametroNumerico('profile_id', True);
	if(empty($fl_alumno))
    $fl_alumno = $fl_usuario;
  
	function GetProfile($fl_alumno){

		$result["profile"] = array();
    $fl_perfil_sp = ObtenPerfilUsuario($fl_alumno);
    
		# Query for student info
    if(!empty($fl_perfil_sp)){
      $Query = "SELECT a.ds_login, a.ds_nombres, a.ds_apaterno, a.fg_genero, DATE_FORMAT(a.fe_nacimiento, '%c') fe_mes, ";
      $Query .= "DATE_FORMAT(a.fe_nacimiento, '%e') fe_dia_anio, a.ds_email, f.fl_pais , b.fl_zona_horaria, b.ds_ruta_avatar, ";
      $Query .= "b.ds_ruta_foto, ds_website, ds_gustos, ds_pasatiempos, a.cl_sesion, ds_power, ds_favorite_movie  ";
      $Query .= "FROM c_usuario a ";
      # Obtenemos el perfil del usuario    
      if($fl_perfil_sp == PFL_ESTUDIANTE_SELF)
        $Query .= "LEFT JOIN c_alumno_sp b ON( b.fl_alumno_sp=a.fl_usuario) ";
      else{
        if($fl_perfil_sp == PFL_MAESTRO_SELF)
          $Query .= "LEFT JOIN c_maestro_sp b ON(b.fl_maestro_sp=a.fl_usuario)";
        else
          $Query .= "LEFT JOIN c_administrador_sp b ON(b.fl_adm_sp=a.fl_usuario)";
      }
      $Query .= "LEFT JOIN k_usu_direccion_sp f ON(f.fl_usuario_sp=a.fl_usuario) ";
    }
    else{
      $perfil = ObtenPerfil($fl_alumno);
       $Query  = "SELECT a.ds_login, a.ds_nombres, a.ds_apaterno, a.fg_genero, DATE_FORMAT(a.fe_nacimiento, '%c') fe_mes, ";
       $Query .= "DATE_FORMAT(a.fe_nacimiento, '%e') fe_dia_anio, a.ds_email, ";
       if($perfil==PFL_MAESTRO)
         $Query .= "b.fl_pais, ";
       else
         $Query .= "(SELECT c.ds_add_country FROM k_ses_app_frm_1 c WHERE c.cl_sesion=a.cl_sesion) fl_pais, ";
       $Query .= "b.fl_zona_horaria, b.ds_ruta_avatar, ";
       $Query .= "b.ds_ruta_foto, ds_website, ds_gustos, ds_pasatiempos, a.cl_sesion, '' ds_power, '' ds_favorite_movie  ";
       $Query .= "FROM c_usuario a ";
        # Obtenemos el perfil del usuario        
        if($perfil == PFL_ESTUDIANTE)
          $Query .= "LEFT JOIN c_alumno b ON( b.fl_alumno=a.fl_usuario) ";
        else
          $Query .= "LEFT JOIN c_maestro b ON(b.fl_maestro=a.fl_usuario)";
    }
    $Query .= "WHERE a.fl_usuario=$fl_alumno";
	  $row = RecuperaValor($Query);
	  $ds_login = trim(str_texto($row[0]), ' ');
	  $ds_nombres = str_texto($row[1]);
	  $ds_apaterno = str_texto($row[2]);
	  $fg_genero = $row[3];
	  $fe_nacimiento = ObtenNombreMes($row[4])." ".$row[5];
	  $ds_email = str_texto($row[6]);
	  $fl_pais = $row[7];
	  $fl_zona_horaria = $row[8];
	  $ds_ruta_avatar = $row[9];
	  $ds_ruta_foto = str_texto($row[10]);
	  $ds_website = str_texto($row[11]);
	  $ds_gustos = str_texto($row[12]);
	  $ds_pasatiempos = str_texto($row[13]);
	  $cl_sesion = $row[14];
    $ds_power = str_texto($row[15]);
    $ds_favorite_movie = str_texto($row[16]);
	  
	  switch($fg_genero){
      case "M": $ds_genero = ObtenEtiqueta(115); break;
      case "F": $ds_genero = ObtenEtiqueta(116); break;
      case "N": $ds_genero = ObtenEtiqueta(128); break;
    }

	  $row  = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais=$fl_pais");
	  $ds_pais = str_uso_normal($row[0]);
    
    # Dependiendo del usuario si es de FAME o VANAS
    if(!empty($fl_perfil_sp)){
      if(!empty($ds_ruta_avatar)){
        $ds_ruta_avatar = "<img src='".ObtenAvatarUsuario($fl_alumno)."'>";
      } 
      if(!empty($ds_ruta_foto)) {
        $ds_ruta_foto = "<img src='".ObtenFotoUsuario($fl_alumno)."'>";
      } 
    }
    else{
      $ds_ruta_avatar = "<img src='".ObtenAvatarUsrVanas($fl_alumno)."'>";
      $ds_ruta_foto = "<img src='".ObtenFotoUsrVanas($fl_alumno)."'>";
    }
    // # Si no hay foto ni avatr pondra el default
    if(empty($ds_ruta_avatar))
        $ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."'>";      
    if(empty($ds_ruta_foto))
        $ds_ruta_foto = "<img src='".PATH_N_COM_IMAGES."/vanas-family-edutisse-header.jpg'>";
    
	  $ds_web = "<a href='http://$ds_website' target='_blank' style='color:#000;'>$ds_website</a>";
    
    # Obtenemos los programas que esta cursando
    $Queryy  = "SELECT b.nb_programa, b.nb_thumb ";
    $Queryy .= "FROM k_usuario_programa a ";
    $Queryy .= "LEFT JOIN c_programa_sp b ON(a.fl_programa_sp=b.fl_programa_sp) ";
    $Queryy .= "WHERE fl_usuario_sp=$fl_alumno";
    $rsj = EjecutaQuery($Queryy);
    $programs = "";
    for($j=0;$rowj = RecuperaRegistro($rsj);$j++){
      $nb_programa = $rowj[0];
      $nb_thumb = $rowj[1];
      $ruta_img = PATH_ADM."/modules/fame/uploads/".$nb_thumb;
      $programs = 
      " <li>
        <a href='javascript:void(0);' rel='tooltip' 
          data-placement='top' data-original-title='".$nb_programa."' data-html='true'><img src='".$ruta_img."' style='width:40px; height:40px;' ></a>
      </li>".$programs;
    }
    $programs = $programs;

	  $result["profile"] += array(
	  	"fname" => $ds_nombres." ",
	  	"lname" => $ds_apaterno,
	  	"profile" => ObtenEtiqueta(424),
	  	"gender" => $gender,
	  	"birthday" => $fe_nacimiento,
	  	"email" => $ds_email,
	  	"country" => $ds_pais,
	  	"avatar" => $ds_ruta_avatar,
	  	"photo" => $ds_ruta_foto,
	  	"website" => $ds_web,
	  	"interest" => $ds_gustos,
	  	"hobby" => $ds_pasatiempos,
      "ds_power" => $ds_power,
      "ds_favorite_movie" => $ds_favorite_movie,
      "programs" => $programs,
      "perfil" => $fl_perfil_sp
	  );
  
  	echo json_encode((Object) $result);
	}
?>

<div class="row">
	<div class="col-sm-12">
			<div class="well well-sm">
				<div class="row">
					<!-- hidden for now will be implemented in the future  -->
					<div class="hidden-xs hidden-sm hidden-md hidden-lg">
						<div class="well well-sm">
							<p>world map of all the students and teachers</p>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="well well-light well-sm no-margin no-padding">
							<div class="row">
								<div class="col-sm-12">
									<div id="myCarousel" class="carousel fade profile-carousel">
										<ol class="carousel-indicators"></ol>
										<div class="carousel-inner">
											<div id="user-header-pic" class="item active"></div>
										</div>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="row">
										<div id="user-profile-pic" class="col-sm-3 profile-pic">
										</div>
										<div class="col-sm-6">
											<h1>
                        <p id="user-profile-fname"></p>
                        <span class="semi-bold" id="user-profile-lname"></span>
                      </h1>
                      <div class="chat-body no-padding profile-message">
                        <ul id="user-profile-info" class="list-unstyled">
                        </ul>				
                      </div>
										</div>
										<div class="col-sm-3" id="user_profile_programs">                      
                    </div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- portfolio panel -->
					<!-- hidden for now will be implemented in the future  -->
					<div class="hidden-xs hidden-sm hidden-md hidden-lg">
						<div class="well well-sm">
							<p>For Portfolio, Student Art work etc... </p>
						</div>
					</div> 
				</div>
			</div>
	</div>
</div>

<script type="text/javascript">  
  var student = <?php GetProfile($fl_alumno); ?>;

  $("#user-profile-pic").append(student.profile.avatar);
  $("#user-profile-fname").append(student.profile.fname);
  $("#user-profile-lname").append(student.profile.lname);	
  $("#user-header-pic").append(student.profile.photo);

  // $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-graduation-cap'>&nbsp;"+student.profile.program+"</p></li>");
  if(student.profile.gender == "Male"){
    $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-male'>&nbsp;&nbsp;"+student.profile.gender+"</p></li>");
  } else {
    $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-female'>&nbsp;&nbsp;"+student.profile.gender+"</p></li>");
  }

  $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-gift'>&nbsp;&nbsp;"+student.profile.birthday+"</p></li>");
  // $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-envelope'>&nbsp;&nbsp;"+student.profile.email+"</p></li>");
  $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-globe'>&nbsp;&nbsp;"+student.profile.country+"</p></li>");

  $("#user-profile-info").append("<br>");
  $("#user-profile-info").append("<li class='message no-margin'><span class='message-text no-margin'><a href='javascript:void(0);' class='username' style='cursor:unset;'><?php echo ObtenEtiqueta(1925); ?> </a><strong style='padding-left:5px;'>"+student.profile.website+"</strong></span></li>");	
  $("#user-profile-info").append("<li class='message no-margin'><span class='message-text no-margin'><a href='javascript:void(0);' class='username' style='cursor:unset;'><?php echo ObtenEtiqueta(1926); ?> </a><strong style='padding-left:5px;'>"+student.profile.interest+"</strong></span></li>");
  $("#user-profile-info").append("<li class='message no-margin'><span class='message-text no-margin'><a href='javascript:void(0);' class='username' style='cursor:unset;'><?php echo ObtenEtiqueta(1927); ?> </a><strong style='padding-left:5px;'>"+student.profile.hobby+"</strong></span></li>");  
  $("#user-profile-info").append("<li class='message no-margin'><span class='message-text no-margin'><a href='javascript:void(0);' class='username' style='cursor:unset;'><?php echo ObtenEtiqueta(1928); ?> </a><strong style='padding-left:5px;'>"+student.profile.ds_power+"</strong></span></li>");
  $("#user-profile-info").append("<li class='message no-margin'><span class='message-text no-margin'><a href='javascript:void(0);' class='username' style='cursor:unset;'><?php echo ObtenEtiqueta(1929); ?> </a><strong style='padding-left:5px;'>"+student.profile.ds_favorite_movie+"</strong></span></li>");
  $("#user-profile-info").append("<br>");
  
  // Programs
  if(student.profile.perfil=='<?php echo PFL_ESTUDIANTE_SELF; ?>')
    $("#user_profile_programs").append("<h1><small><?php echo ObtenEtiqueta(1937); ?></small></h1><ul class='list-inline friends-list'>"+student.profile.programs+"</ul>");
  else
    $("#user_profile_programs").append("");
  pageSetUp();
</script>
