<?php
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
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

		# Revisa si el alumno esta inscrito en un grupo
  	$fl_gurpo = ObtenGrupoAlumno($fl_alumno);

		# Query for student info
		$Query  = "SELECT ds_login, ds_nombres, ds_apaterno, fg_genero, DATE_FORMAT(fe_nacimiento, '%c') 'fe_mes', ";
	  $Query .= "DATE_FORMAT(fe_nacimiento, '%e') 'fe_dia_anio', a.ds_email, ds_add_country, fl_zona_horaria, ds_ruta_avatar, ";
	  $Query .= "b.ds_ruta_foto, ds_website, ds_gustos, ds_pasatiempos, a.cl_sesion ";
	  $Query .= "FROM c_usuario a, c_alumno b, k_ses_app_frm_1 c ";
	  $Query .= "WHERE a.fl_usuario=b.fl_alumno ";
	  $Query .= "AND a.cl_sesion=c.cl_sesion ";
	  $Query .= "AND fl_usuario=$fl_alumno";
	  $row = RecuperaValor($Query);
	  $ds_login = str_texto($row[0]);
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
	  
	  if($fg_genero == 'M')
	  	$gender = ObtenEtiqueta(115);
	  else
	  	$gender = ObtenEtiqueta(116);

	  $row  = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais=$fl_pais");
	  $ds_pais = str_uso_normal($row[0]);

	  if(!empty($ds_ruta_avatar)){
	  	$ds_ruta_avatar = "<img src='".PATH_ALU_IMAGES."/avatars/$ds_ruta_avatar'>";
	  } else {
	  	$ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."'>";
	  }
	  if(!empty($ds_ruta_foto)) {
	  	$ds_ruta_foto = "<img src='".PATH_ALU_IMAGES."/pictures/$ds_ruta_foto'>";
	  } else {
	  	$ds_ruta_foto = "<img src='".PATH_N_COM_IMAGES."/vanas-family-edutisse-header.jpg'>";
	  }
	  $ds_web = "<a href='http://$ds_website' target='_blank'>$ds_website</a>";

	  $result["profile"] += array(
	  	"name" => $ds_nombres." ".$ds_apaterno,
	  	"profile" => ObtenEtiqueta(424),
	  	"gender" => $gender,
	  	"birthday" => $fe_nacimiento,
	  	"email" => $ds_email,
	  	"country" => $ds_pais,
	  	"avatar" => $ds_ruta_avatar,
	  	"photo" => $ds_ruta_foto,
	  	"website" => $ds_web,
	  	"interest" => $ds_gustos,
	  	"hobby" => $ds_pasatiempos
	  );

	  if(!empty($fl_gurpo)) {
	    $fl_programa = ObtenProgramaAlumno($fl_alumno);
	    $nb_programa = ObtenNombreProgramaAlumno($fl_alumno);
	    $nb_gurpo = ObtenNombreGrupoAlumno($fl_alumno);
	    $nb_maestro = ObtenNombreMaestroAlumno($fl_alumno);
	    $no_grado = ObtenGradoAlumno($fl_alumno);
	    $no_semana = ObtenSemanaActualAlumno($fl_alumno);
	    $ds_titulo = ObtenTituloLeccion($fl_programa, $no_grado, $no_semana);

	    $result["profile"] += array(
	    	"program" => $nb_programa,
	    	"group" => $nb_gurpo,
	    	"instructor" => $nb_maestro,
	    	"term" => $no_grado,
	    	"week" => $no_semana,
	    	"title" => $ds_titulo
	    );
  	} else {
  		$result["profile"] += array(
	    	"program" => "",
	    	"group" => "",
	    	"instructor" => "",
	    	"term" => "",
	    	"week" => "",
	    	"title" => ""
	    );
  	}

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

					<!-- <div class="col-xs-12 col-sm-6 col-md-offset-3 col-lg-offset-3"> -->
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
											<!-- <div class="padding-10">
												<h4 class="font-md"><strong>1,543</strong>
												<br>
												<small>Followers</small></h4>
												<br>
												<h4 class="font-md"><strong>419</strong>
												<br>
												<small>Connections</small></h4>
											</div> -->
										</div>
										<div class="col-sm-6">
											<h1 class="font-xl semi-bold" id="user-profile-name"></h1>

											<ul id="user-profile-info" class="list-unstyled"></ul>
											<br>
											<!-- <a href="javascript:void(0);" class="btn btn-default btn-xs"><i class="fa fa-envelope-o"></i> Send Message</a> -->
											<br>
											<br>

										</div>
										<div class="col-sm-3">
											<!-- <h1><small>Connections</small></h1>
											<ul class="list-inline friends-list">
												<li><img src=""></li>
												<li><img src=""></li>
												<li><img src=""></li>
												<li><img src=""></li>
												<li><img src=""></li>
												<li><img src=""></li>
												<li><a href="javascript:void(0);">413 more</a></li>
											</ul> -->

											<!-- <h1><small>Recent visitors</small></h1>
											<ul class="list-inline friends-list">
												<li><img src=""></li>
												<li><img src=""></li>
												<li><img src=""></li>
											</ul> -->
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
	$("#user-profile-name").append(student.profile.name);
	$("#user-header-pic").append(student.profile.photo);

	$("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-graduation-cap'>&nbsp;"+student.profile.program+"</p></li>");
	if(student.profile.gender == "Male"){
		//$("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-male'>&nbsp;&nbsp;"+student.profile.gender+"</p></li>");
	} else {
		//$("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-female'>&nbsp;&nbsp;"+student.profile.gender+"</p></li>");
	}

	$("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-gift'>&nbsp;&nbsp;"+student.profile.birthday+"</p></li>");
	$("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-envelope'>&nbsp;&nbsp;"+student.profile.email+"</p></li>");
	$("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-globe'>&nbsp;&nbsp;"+student.profile.country+"</p></li>");

	$("#user-profile-info").append("<br>");

	$("#user-profile-info").append("<li><p class='text-muted font-md'>Website: "+student.profile.website+"</p></li>");
	$("#user-profile-info").append("<li><p class='text-muted font-md'>Interest: "+student.profile.interest+"</p></li>");
	$("#user-profile-info").append("<li><p class='text-muted font-md'>Hobby: "+student.profile.hobby+"</p></li>");

	$("#user-profile-info").append("<br>");

	//$("#user-profile-info").append("<li><p class='text-muted font-md'>Group: "+student.profile.group+"</p></li>");
	$("#user-profile-info").append("<li><p class='text-muted font-md'>Instructor: "+student.profile.instructor+"</p></li>");
	$("#user-profile-info").append("<li><p class='text-muted font-md'>Term: "+student.profile.term+"</p></li>");
	$("#user-profile-info").append("<li><p class='text-muted font-md'>Week: "+student.profile.week+"</p></li>");
	$("#user-profile-info").append("<li><p class='text-muted font-md'>Lesson: "+student.profile.title+"</p></li>");
</script>
