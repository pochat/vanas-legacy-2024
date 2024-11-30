<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

	function GetProfile($fl_maestro){
		
		$result["profile"] = array();

		# Inicializa variables
    $Query  = "SELECT ds_login, ds_nombres, ds_apaterno, ds_amaterno, fg_genero, ";
    $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, ds_email, fl_pais, fl_zona_horaria, ds_ruta_avatar, ds_ruta_foto, ";
    $Query .= "ds_empresa, ds_website, ds_gustos, ds_pasatiempos, ds_biografia, ds_number ";
    $Query .= "FROM c_usuario a, c_maestro b ";
    $Query .= "WHERE a.fl_usuario=b.fl_maestro ";
    $Query .= "AND fl_usuario=$fl_maestro";
    $row = RecuperaValor($Query);
    $ds_login = str_texto($row[0]);
    $ds_nombres = str_texto($row[1]);
    $ds_apaterno = str_texto($row[2]);
    $ds_amaterno = str_texto($row[3]);
    $fg_genero = $row[4];
    $fe_nacimiento = $row[5];
    $ds_email = str_texto($row[6]);
    $fl_pais = $row[7];
    $fl_zona_horaria = $row[8];
    $ds_avatar = str_texto($row[9]);
    $ds_foto = str_texto($row[10]);
    $ds_empresa = str_texto($row[11]);
    $ds_website = str_texto($row[12]);
    $ds_gustos = str_texto($row[13]);
    $ds_pasatiempos = str_texto($row[14]);
    $ds_biografia = str_texto($row[15]);
    $ds_number = str_texto($row[16]);
    $ds_nombres_err = "";
    $ds_apaterno_err = "";
    $fe_nacimiento_err = "";
    $ds_email_err = "";
    $ds_ruta_avatar_err = "";
	  
	  if(!empty($ds_avatar)){
	  	$ds_ruta_avatar = "<img src='".PATH_MAE_IMAGES."/avatars/$ds_avatar'>";
	  } else {
	  	$ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."'>";
	  }
	  if(!empty($ds_foto)) {
	  	$ds_ruta_foto = "<img src='".PATH_MAE_IMAGES."/pictures/$ds_foto'>";
	  } else {
	  	$ds_ruta_foto = "<img src='".PATH_N_COM_IMAGES."/vanas-family-edutisse-header.jpg'>";
	  }

	  $result["profile"] += array(
	  	"clave" => $fl_maestro,
	  	"ds_login" => $ds_login,
	  	"ds_nombres" => $ds_nombres,
	  	"ds_amaterno" => $ds_amaterno,
	  	"ds_apaterno" => $ds_apaterno,
	  	"fg_genero" => $fg_genero,
	  	"fe_nacimiento" => $fe_nacimiento,
			"ds_email" => $ds_email,
			"fl_pais" => $fl_pais,
			"fl_zona_horaria" => $fl_zona_horaria,
			"ds_avatar" => $ds_avatar,
			"ds_ruta_avatar" => $ds_ruta_avatar,
			"ds_foto" => $ds_foto,
			"ds_ruta_foto" => $ds_ruta_foto,
			"ds_company" => $ds_empresa,
			"ds_website" => $ds_website,
			"ds_gustos" => $ds_gustos,
			"ds_pasatiempos" => $ds_pasatiempos,
			"ds_biografia" => $ds_biografia,
			"ds_number" => $ds_number,
			"ds_nombres_err" => ObtenMensaje($ds_nombres_err),
			"ds_apaterno_err" => ObtenMensaje($ds_apaterno_err),
			"ds_password_err" => ObtenMensaje($ds_password_err),
			"fe_nacimiento_err" => ObtenMensaje($fe_nacimiento_err),
			"ds_email_err" => ObtenMensaje($ds_email_err),
			"ds_ruta_avatar_err" => ObtenMensaje($ds_ruta_avatar_err)
	  );

	  echo json_encode((Object) $result);
	}
	function GetCountryList(){
		$Query  = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
		$rs = EjecutaQuery($Query);

		$result["name"] = array();
		while($row = RecuperaRegistro($rs)) {
	    $result["name"] += array($row[1] => $row[0]);
  	}
  	echo json_encode((Object) $result);
	}
	function GetTimeZoneList(){
		$concat = array('nb_zona_horaria', "' (GMT '", 'no_gmt', "')'");
  	$Query  = "SELECT (".ConcatenaBD($concat).") 'ds_zona', fl_zona_horaria FROM c_zona_horaria ORDER BY nb_zona_horaria";
  	$rs = EjecutaQuery($Query);

  	$result["zone"] = array();
  	while($row = RecuperaRegistro($rs)) {
	    $result["zone"] += array($row[1] => $row[0]);
  	}
  	echo json_encode((Object) $result);
	}

?>

<div class="row">
	<div class="col-xs-12">
			<div class="well well-sm">
				<div class="row">
					<!-- hidden for now will be implemented in the future  -->
					<div class="hidden-xs hidden-sm hidden-md hidden-lg">
						<div class="well well-sm">
							<p>world map of all the students and teachers</p>
						</div>
					</div>
					<!-- <div class="col-xs-12 col-sm-6 col-md-offset-3 col-lg-offset-3"> -->
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
										<div id="user-profile-pic" class="col-sm-3 profile-pic"></div>
										<div class="col-sm-6">
											<div class="well well-sm">
												<form name="datos" role="form" class="form-horizontal" id="user-form-info" method="post" action="ajax/profile_iu.php" enctype='multipart/form-data'></form>
											</div>
											<br>
											<br>

										</div>
										<div class="col-sm-3">
											<!-- <h1><small>Connections</small></h1>
											<ul class="list-inline friends-list">
												<li><img src="" alt="friend-1"></li>
												<li><img src="" alt="friend-2"></li>
												<li><img src="" alt="friend-3"></li>
												<li><img src="" alt="friend-4"></li>
												<li><img src="" alt="friend-5"></li>
												<li><img src="" alt="friend-6"></li>
												<li><a href="javascript:void(0);">413 more</a></li> 
											</ul> -->
											
											<!-- <h1><small>Recent visitors</small></h1>
											<ul class="list-inline friends-list">
												<li><img src="" alt="friend-1"></li>
												<li><img src="" alt="friend-2"></li>
												<li><img src="" alt="friend-3"></li> 
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
	var user = <?php GetProfile($fl_maestro); ?>;
	$("#user-profile-pic").append(user.profile.ds_ruta_avatar);
	$("#user-header-pic").append(user.profile.ds_ruta_foto);

	var info = "";
	info += FormInputHidden("clave", user.profile.clave);
	info += FormInputHidden("cl_sesion", user.profile.cl_sesion);
	info += FormInputStatic("User", user.profile.ds_login);
	info += FormInputHidden("ds_login", user.profile.ds_login);

	info += FormInput("ds_nombres", "text", "* First Name", user.profile.ds_nombres, "ds_nombres_err");
	info += FormInput("ds_amaterno", "text", "Mid Name", user.profile.ds_amaterno, "");
	info += FormInput("ds_apaterno", "text", "* Last Name", user.profile.ds_apaterno, "ds_apaterno_err");

	info += "<br>";

	info += FormInput("ds_password", "password", "New Password", "", "ds_password_err");
	info += FormInput("ds_password_conf", "password", "Confirm Password", "", "");

	info += "<br>";

	info += FormInputDropbox("fg_genero", "Gender", {"M":"Male","F":"Female"}, user.profile.fg_genero);
	info += FormInputStatic("Birthday", user.profile.fe_nacimiento);
	info += FormInputHidden("fe_nacimiento", user.profile.fe_nacimiento);
	info += FormInput("ds_email", "text", "* Email", user.profile.ds_email, "ds_email_err");

	info += "<br>";

	var country = <?php GetCountryList(); ?>;
	info += FormInputDropbox("fl_pais", "Country", country.name, user.profile.fl_pais);
	var time_zone = <?php GetTimeZoneList(); ?>;
	info += FormInputDropbox("fl_zona_horaria", "Time Zone", time_zone.zone, user.profile.fl_zona_horaria);

	info += FormInputHidden("ds_ruta_avatar", user.profile.ds_avatar);
	info += FormInputUpload("avatar", "Avatar 70x70", "", "jpg|jpeg");
	info += FormInputHidden("ds_ruta_foto", user.profile.ds_foto);
	info += FormInputUpload("foto", "Header 1315x150", "", "jpg|jpeg");

	info += FormInput("ds_empresa", "text", "Company", user.profile.ds_company, "");
	info += FormInput("ds_website", "text", "Website", user.profile.ds_website, "");
	info += FormInputTextArea("ds_gustos", "Interest", user.profile.ds_gustos);
	info += FormInputTextArea("ds_pasatiempos", "Hobbies", user.profile.ds_pasatiempos);
	info += FormInputTextArea("ds_biografia", "Short Bio", user.profile.ds_biografia);

	info += FormSubmit();

	$("#user-form-info").append(info);

	// jquery form
	loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/jquery-form/jquery.form.min.js", InitAjaxForm);
	function InitAjaxForm(){
		var option = {
			dataType: 'json',
			success: function(result){
				if(result.datos.fg_error){
					$("#ds_nombres_err").text(result.datos.ds_nombres_err);
					$("#ds_apaterno_err").text(result.datos.ds_apaterno_err);
					$("#ds_email_err").text(result.datos.ds_email_err);
				} else {
					location.reload();
				}
			}
		};
		$("#user-form-info").ajaxForm(option); 
	}

	function FormInputStatic(title, info){
		var input =
			"<div class='form-group'>" +
			  "<label class='col-sm-2 control-label'>"+title+"</label>" +
			  "<div class='col-sm-10'>"+
			  	"<p class='form-control-static'>"+info+"</p>"+
			  "</div>" +
			"</div>";
		return input;
	}
	function FormInput(id, type, title, value, error){
		var	input =
			"<div class='form-group'>" +
				"<label for='"+id+"' class='col-sm-2 control-label'>"+title+"</label>" +
				"<div class='col-sm-10'>" + 
			  	"<input type='"+type+"' class='form-control' name='"+id+"' id='"+id+"' value='"+value+"'>" + 
			  	"<p id='"+error+"' class='form-control-static text-danger'></p>"+
			  "</div>" +
			"</div>";
		return input;
	}
	function FormInputHidden(id, value){
		var input = "<input type='hidden' id='"+id+"' name='"+id+"' value='"+value+"'>";
		return input;
	}
	function FormInputTextArea(id, title, value){
		var	input =
			"<div class='form-group'>" +
				"<label for='"+id+"' class='col-sm-2 control-label'>"+title+"</label>" +
				"<div class='col-sm-10'>" +
			  	"<textarea class='form-control' rows='3' name='"+id+"' id='"+id+"'>"+value+"</textarea>" +
			  "</div>" +
			"</div>";
		return input;
	}
	function FormInputDropbox(id, title, list, selected){
		var option = "";
		for(var k in list){
			if(k == selected){
				option += "<option value='"+k+"' selected>"+list[k]+"</option>";
			} else {
				option += "<option value='"+k+"'>"+list[k]+"</option>";
			}
		}
		var	input =
			"<div class='form-group'>" +
				"<label for='"+id+"' class='col-sm-2 control-label'>"+title+"</label>" +
				"<div class='col-sm-10'>" +
			  	"<select id='"+id+"' name='"+id+"'>"+option+"</select>" +
			  "</div>" +
			"</div>";
		return input;
	}
	function FormSubmit(){
		var input = 
		"<div class='form-group'>" +
	    "<div class='col-sm-offset-2 col-sm-10'>" +
	      //"<button type='submit' onclick='javascript:document.datos.submit();' class='btn btn-default'>Submit</button>" +
	      "<button type='submit' class='btn btn-default'>Submit</button>" +
	      //"<button type='button' id='user-form-submit' class='btn btn-default'>Submit</button>" +
	    "</div>" +
	  "</div>";
	  return input;
	}
	function FormInputUpload(id, title, info, accept){
		if(accept.length != 0){
			var name = id+"[]";
		} else {
			var name = id;
		}
		var input =	
			"<div class='form-group'>" +
			  "<label class='col-sm-2 control-label'>"+title+"</label>" +
			  "<div class='col-sm-10'>"+
		  		"<div class='input-group'>" +
			      "<span class='input-group-btn'>" +
			        "<span class='btn btn-default btn-file'>" +
			          "Browse <input type='file' id='"+id+"' name='"+name+"' size='60' accept='"+accept+"' maxlength='1' multiple>" +
			        "</span>" +
			      "</span>" +
			      "<input type='text' class='form-control' readonly>" +
					"</div>" +
			  "</div>" +
			"</div>";
		return input;
	}
	
	// Helper functions
	// ----------------
	/* @url: http://www.surrealcms.com/blog/whipping-file-inputs-into-shape-with-bootstrap-3
	 * Provides feedback after user browse for a file
	 */
	$(document).on('change', '.btn-file :file', function() {
	  var input = $(this),
		    numFiles = input.get(0).files ? input.get(0).files.length : 1,
		    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	  input.trigger('fileselect', [numFiles, label]);
	});
	$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
	  var input = $(this).parents('.input-group').find(':text'),
	      log = numFiles > 1 ? numFiles + ' files selected' : label;
	  if( input.length ) {
	      input.val(log);
	  } else {
	      if( log ) alert(log);
	  }        
	});
</script>