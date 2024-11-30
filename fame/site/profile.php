<?php
	# Librerias
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Parametro para verificar si es tu perfil o veras el de otro
 $fg_otro = true;
  
	# Recibe parametros
  $otro = RecibeParametroNumerico('otro', true);
  if(!empty($otro)){
    $fl_usuario = RecibeParametroNumerico('profile_id', true);
    $fg_otro = false;
  }
 
  # Intituto del usuario
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  
  # Si no hay registro en las tablas de informacion general del usrioa las inserta
  if(ExisteEnTabla('k_usu_direccion_sp', 'fl_usuario_sp', $fl_usuario))
    EjecutaQuery("INSERT INTO k_usu_direccion_sp (fl_usuario_sp) VALUES ($fl_usuario)");
 
  if($fl_perfil == PFL_MAESTRO_SELF){
    if(ExisteEnTabla('c_maestro_sp', 'fl_maestro_sp', $fl_usuario))
      $QueryI = "INSERT INTO c_maestro_sp (fl_maestro_sp) VALUES ($fl_usuario)";
  }
  else{
    if($fl_perfil == PFL_ESTUDIANTE_SELF){
      if(ExisteEnTabla('c_alumno_sp', 'fl_alumno_sp', $fl_usuario))
      $QueryI = "INSERT INTO c_alumno_sp (fl_alumno_sp) VALUES ($fl_usuario)";
    }
    else{
      if(ExisteEnTabla('c_administrador_sp', 'fl_adm_sp', $fl_usuario))
      $QueryI = "INSERT INTO c_administrador_sp (fl_adm_sp) VALUES ($fl_usuario)";
    }
  }
  EjecutaQuery($QueryI);
    
  # SOlo muestra la informacion no podra editar
	function GetProfileOtro($fl_usuario){

		$result["profile"] = array();
    # Verificamos el perfil del usuario
    $fl_perfil = ObtenPerfilUsuario($fl_usuario);
    $fl_instituto = ObtenInstituto($fl_usuario);

    # Checamos que tabla va revisar
    if($fl_perfil == PFL_MAESTRO_SELF){
      $tbl = "c_maestro_sp ";
      $campo = "fl_maestro_sp ";
    }
    else{
      if($fl_perfil == PFL_ESTUDIANTE_SELF){
        $tbl = "c_alumno_sp";
        $campo = "fl_alumno_sp";
      }
      else{
        $tbl = "c_administrador_sp";
        $campo = "fl_adm_sp";
      }
    }
		# Query for student info
		$Query  = "SELECT ds_login, ds_nombres, ds_apaterno, fg_genero, DATE_FORMAT(fe_nacimiento, '%c') 'fe_mes', ";
    $Query .= "DATE_FORMAT(fe_nacimiento, '%e') 'fe_dia_anio', a.ds_email, c.fl_pais , fl_zona_horaria, ";
    $Query .= "ds_ruta_avatar, b.ds_ruta_foto, ds_website, ds_gustos, ds_pasatiempos, a.cl_sesion, ";
    $Query .= "c.ds_state, c.ds_city, c.ds_number, c.ds_street, c.ds_zip,b.fl_grado ";
    $Query .= "FROM c_usuario a, ".$tbl." b, k_usu_direccion_sp c ";
    $Query .= "WHERE a.fl_usuario=b.".$campo." AND a.fl_usuario=c.fl_usuario_sp AND fl_usuario=$fl_usuario ";
	  $row = RecuperaValor($Query);
	  $ds_login = str_texto($row[0]);
	  $ds_nombres = str_texto($row[1]);
	  $ds_apaterno = str_texto($row[2]);
	  $fg_genero = $row[3];
	  $fe_nacimiento = ObtenNombreMes($row[4])." ".$row[5];
	  $ds_email = str_texto($row[6]);
	  $fl_pais = $row[7];
    # Por default mostrara el pais de la escuela
    if(empty($fl_pais)){
      $row0 = RecuperaValor("SELECT fl_pais FROM c_instituto WHERE fl_instituto=$fl_instituto");
      $fl_pais = $row0[0];
    }
	  $fl_zona_horaria = $row[8];
	  $ds_ruta_avatar = $row[9];
	  $ds_ruta_foto = str_texto($row[10]);
	  $ds_website = str_texto($row[11]);
	  $ds_gustos = str_texto($row[12]);
	  $ds_pasatiempos = str_texto($row[13]);
	  $cl_sesion = $row[14];
	  $fl_grado=$row['fl_grado'];
	  if(empty($fl_grado))
		  $fl_grado=0;
	  
	  switch($fg_genero){
      case "M": $ds_genero = ObtenEtiqueta(115); break;
      case "F": $ds_genero = ObtenEtiqueta(116); break;
      case "N": $ds_genero = ObtenEtiqueta(128); break;
    }

	  $row  = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais=$fl_pais");
	  $ds_pais = str_uso_normal($row[0]);
    
    # Ruta del avatar
    $ruta = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_usuario;

	  if(!empty($ds_ruta_avatar)){
	  	$ds_ruta_avatar = "<img src='".$ruta."/$ds_ruta_avatar'>";
	  } else {
	  	$ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."'>";
	  }
	  if(!empty($ds_ruta_foto)) {
	  	$ds_ruta_foto = "<img src='".$ruta."/$ds_ruta_foto'>";
	  } else {
	  	$ds_ruta_foto = "<img src='".PATH_N_COM_IMAGES."/vanas-family-edutisse-header.jpg'>";
	  }
	  $ds_web = "<a href='http://$ds_website' target='_blank'>$ds_website</a>";

	  $result["profile"] += array(
	  	"name" => $ds_nombres." ".$ds_apaterno,
	  	"gender" => $gender,
	  	"birthday" => $fe_nacimiento,
	  	"email" => $ds_email,	  	
	  	"avatar" => $ds_ruta_avatar,
	  	"photo" => $ds_ruta_foto,
      "country" => $ds_pais,
      "state" => $state,
      "city" => $city,
      "number" => $ds_number,
      "street" => $ds_street,
	  "fl_grado"=>$fl_grado,
      "zip" => $ds_zip    
	  );    
    
  	echo json_encode((Object) $result);
	}

  # Muestra la informacion del usuario
  function GetProfile($fl_usuario){
		
		$result["profile"] = array();
    
    # Obtenemos el perfil del usuario
    $fl_perfil = ObtenPerfilUsuario($fl_usuario);
    $fl_instituto = ObtenInstituto($fl_usuario);
    
    # Checamos que tabla va revisar
    if($fl_perfil == PFL_MAESTRO_SELF){
      $tbl = "c_maestro_sp ";
      $campo = "fl_maestro_sp ";
    }
    else{
      if($fl_perfil == PFL_ESTUDIANTE_SELF){
        $tbl = "c_alumno_sp";
        $campo = "fl_alumno_sp";
      }
      else{
        $tbl = "c_administrador_sp";
        $campo = "fl_adm_sp";
      }
    }
    
		# Inicializa variables
    $Query  = "SELECT ds_login, ds_nombres, ds_apaterno, ds_amaterno, fg_genero, ";
    $Query .= "DATE_FORMAT(fe_nacimiento, '%d-%m-%Y'), a.ds_email, c.fl_pais, fl_zona_horaria, ds_ruta_avatar, ";
    $Query .= "b.ds_ruta_foto, a.cl_sesion, c.ds_number,ds_street,ds_city,ds_state,ds_zip, ds_alias, ds_instituto, ";
    $Query .= "d.ds_foto, ds_website, ds_gustos, ds_pasatiempos, ds_power, ds_favorite_movie,d.ds_rfc, b.ds_profesion, b.ds_compania, a.fl_language,d.school_id ";
    if($fl_perfil== PFL_ESTUDIANTE_SELF)
    $Query .=",b.fl_grado ";
    $Query .= "FROM c_usuario a ";
    $Query .= "LEFT JOIN ".$tbl." b ON(b.".$campo."=a.fl_usuario) ";
    $Query .= "LEFT JOIN k_usu_direccion_sp c ON(c.fl_usuario_sp=a.fl_usuario) ";
    $Query .= "LEFT JOIN c_instituto d ON(d.fl_instituto=a.fl_instituto) ";
    $Query .= "WHERE a.fl_usuario=$fl_usuario";
    $row = RecuperaValor($Query);
    $ds_login = str_texto($row[0]);
    $ds_nombres = str_texto($row[1]);
    $ds_apaterno = str_texto($row[2]);
    $ds_amaterno = str_texto($row[3]);
    $fg_genero = $row[4];
    $fe_nacimiento = $row[5];
    $ds_email = str_texto($row[6]);
    $fl_pais = $row[7];
    $ds_rfc=$row['ds_rfc'];
    $ds_profesion=str_texto($row['ds_profesion']);
    $ds_compania=str_texto($row['ds_compania']);
    $fl_language = $row['fl_language'];
	if(empty($ds_profesion))
    $ds_profesion="";
    if(empty($ds_compania))
    $ds_compania="";
    $school_id=$row['school_id'];
    if(empty($school_id))
        $school_id="";
	$fl_grado=$row['fl_grado'];
	if(empty($fl_grado))
		$fl_grado=0;
	  
    #Recuperamos los flags de notificaciones.
	$Query="SELECT fg_nuevo_post,fg_coment_post,fg_like_post,fg_ayuda_post,fg_follow,fg_ayuda_post_all_comunity,fg_session_completed FROM k_notify_fame_feed WHERE fl_usuario=$fl_usuario ";
	$rof=RecuperaValor($Query);
	$fg_nuevo_post=$rof['fg_nuevo_post'];
	$fg_coment_post=$rof['fg_coment_post'];
	$fg_like_post=$rof['fg_like_post'];
	$fg_ayuda_post=$rof['fg_ayuda_post'];
	$fg_follow=$rof['fg_follow'];
	$fg_ayuda_post_all_comunity=$rof['fg_ayuda_post_all_comunity'];
    $fg_session_completed=$rof['fg_session_completed'];

	


	if(empty($fe_nacimiento))
	$fe_nacimiento="";	
    # Por default mostrara el pais de la escuela
    if(empty($fl_pais)){
      $row0 = RecuperaValor("SELECT fl_pais FROM c_instituto WHERE fl_instituto=$fl_instituto");
      $fl_pais = $row0[0];
    }
    $fl_zona_horaria = $row[8];
    $ds_avatar = str_texto($row[9]);
    $ds_foto = str_texto($row[10]);  
    $cl_sesion = $row[11];
    $ds_add_number = $row[12];
    $ds_add_street = $row[13];
    $ds_add_city = str_texto($row[14]);
    $ds_add_state = str_texto($row[15]);
    $ds_add_zip = $row[16];
    $ds_alias = trim(str_texto(str_ascii($row[17])), ' ');
    # Si no hay alias entonces por default sera el login
    if(empty($ds_alias))
      $ds_alias = trim($ds_login, ' ' );
    $ds_instituto = str_texto($row[18]);
    $ds_foto_inst = $row[19];
    $ds_website = str_texto($row[20]);
    $ds_gustos = str_texto($row[21]);
    $ds_pasatiempos = str_texto($row[22]);
    $ds_power = str_texto($row[23]);
    $ds_favorite_movie = str_texto($row[24]);
    $ds_alias_err = "";
    $ds_nombres_err = "";
    $ds_apaterno_err = "";
    $ds_password_err = "";
    $fe_nacimiento_err = "";
    $ds_email_err = "";
    $fl_pais_err = "";
    $ds_ruta_avatar_err = "";
    $ds_add_number_err = "";
    $ds_add_street_err = "";
    $ds_add_city_err = "";
    $ds_add_state_err = "";
    $ds_add_zip_err = "";
	  
    # Ruta del avatar
    $ruta = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_usuario;
    
	  if(!empty($ds_avatar)){
	  	$ds_ruta_avatar = "<img src='".$ruta."/$ds_avatar'>";
	  } else {
	  	$ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."'>";
	  }
	  if(!empty($ds_foto)) {
	  	$ds_ruta_foto = "<img src='".$ruta."/$ds_foto'>";
	  } else {
	  	$ds_ruta_foto = "<img src='".PATH_SELF."/img/fame-family-edutisse-header.jpg'>";
	  }     
	  $result["profile"] += array(
	  	"clave" => $fl_usuario,
	  	"cl_sesion" => $cl_sesion, 
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
      "ds_add_number" => $ds_add_number,
      "ds_add_street" => $ds_add_street,
      "ds_add_city" => $ds_add_city,
      "ds_add_state" => $ds_add_state,
      "ds_add_zip" => $ds_add_zip,
      "ds_alias" => $ds_alias,
			"ds_nombres_err" => ObtenMensaje($ds_nombres_err),
			"ds_apaterno_err" => ObtenMensaje($ds_apaterno_err),
			"ds_password_err" => ObtenMensaje($ds_password_err),
			"fe_nacimiento_err" => ObtenMensaje($fe_nacimiento_err),
			"ds_email_err" => ObtenMensaje($ds_email_err),
			"ds_ruta_avatar_err" => ObtenMensaje($ds_ruta_avatar_err),
			"ds_add_number_err" => ObtenMensaje($ds_add_number_err),
			"ds_add_street_err" => ObtenMensaje($ds_add_street_err),
			"ds_add_city_err" => ObtenMensaje($ds_add_city_err),
			"ds_add_state_err" => ObtenMensaje($ds_add_state_err),
			"ds_add_zip_err" => ObtenMensaje($ds_add_zip_err),
			"fl_pais_err" => ObtenMensaje($fl_pais_err),
			"ds_instituto" =>$ds_instituto,
			"ds_foto_inst" =>$ds_foto_inst,
			"ds_website" =>$ds_website,
			"ds_gustos" =>$ds_gustos,
			"ds_pasatiempos" =>$ds_pasatiempos,
      "ds_power" => $ds_power,
      "ds_favorite_movie" => $ds_favorite_movie,
	  "ds_rfc"=> $ds_rfc,
      "ds_profesion"=>$ds_profesion,
	  "ds_compania"=>$ds_compania,
	  "fg_nuevo_post"=>$fg_nuevo_post,
	  "fg_coment_post"=>$fg_coment_post,
	  "fg_like_post"=>$fg_like_post,
	  "fg_ayuda_post"=>$fg_ayuda_post,
	  "fg_follow"=>$fg_follow,
      "school_id"=>$school_id,
	  "fl_language"=>$fl_language,
	  "fg_ayuda_post_all_comunity"=>$fg_ayuda_post_all_comunity,
      "fg_session_completed"=>$fg_session_completed,
	  "fl_grado"=>$fl_grado 
	  );
    
	  echo json_encode((Object) $result);
	}

  function GetLanguage(){
    $result["langname"] = array();
    $result["langname"] += array(
      "1" => "Spanish",
      "2" => "English",
      "3" => "French"
    );
    echo json_encode((Object) $result);
  }
	
  # Zona horaria del usuario
  function GetCountryList(){
		$Query  = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
		$rs = EjecutaQuery($Query);

		$result["name"] = array();
		while($row = RecuperaRegistro($rs)) {
	    $result["name"] += array($row[1] => $row[0]);
  	}
  	echo json_encode((Object) $result);
	}
	
  function GetGradeList(){
	  
	$Query  = "SELECT nb_grado, fl_grado FROM k_grado_fame ORDER BY fl_grado ";
	$rs = EjecutaQuery($Query);
	$result["grado"] = array();
	while($row = RecuperaRegistro($rs)) {
	    $result["grado"] += array($row[1] => $row[0]);
  	}
	echo json_encode((Object) $result);
	  
  }
  function  GetProvinciasList(){
    $Query  = "SELECT ds_provincia, fl_provincia FROM k_provincias ORDER BY ds_provincia";
    $rs = EjecutaQuery($Query);

    $result["name_prov"] = array();
    while($row = RecuperaRegistro($rs)) {
      $result["name_prov"] += array($row[1] => $row[0]);
    }
    echo json_encode((Object) $result);
  }
	function GetTimeZoneList(){
		$concat = array('nb_zona_horaria', "' (GMT '", no_gmt, "')'");
  	$Query  = "SELECT (".ConcatenaBD($concat).") 'ds_zona', fl_zona_horaria FROM c_zona_horaria ORDER BY nb_zona_horaria";
  	$rs = EjecutaQuery($Query);

  	$result["zone"] = array();
  	while($row = RecuperaRegistro($rs)) {
	    $result["zone"] += array($row[1] => $row[0]);
  	}
  	echo json_encode((Object) $result);
	}
  
  ?>
  <!-- Ver bie g y j en los campos --->
  <style>
    .smart-form .input input, .smart-form .select select, .smart-form .textarea textarea {
      padding: 6px 10px;
    }
	/*para que se vean bien los input*/
	.smart-form .icon-append {
    right: 19px !important;
    padding-left: 9px !important;
	}
  </style>
  
  <?php
  # Muestra los campos  si es el mismo usuario y es otro mostra solo informacion
  if($fg_otro==false){
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
            <div class="col-xs-10">
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
                        <h1 class="font-xl semi-bold" id="user-profile-name"></h1>
                        <ul id="user-profile-info" class="list-unstyled"></ul>
                      </div>
                      <div class="col-sm-3">
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
    var student = <?php GetProfileOtro($fl_usuario); ?>;

    $("#user-profile-pic").append(student.profile.avatar);
    $("#user-profile-name").append(student.profile.name);
    $("#user-header-pic").append(student.profile.photo);
    // $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-graduation-cap'> "+student.profile.program+"</p></li>");
    if(student.profile.gender == "Male"){
      $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-male'> "+student.profile.gender+"</p></li>");
    } else {
      $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-female'> "+student.profile.gender+"</p></li>");
    }
    $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-gift'> "+student.profile.birthday+"</p></li>");
    $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-envelope'> "+student.profile.email+"</p></li>");
    $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-globe'> "+student.profile.country+"</p></li>");
  </script>
<?php
  }
  else{
?>
  <div style="opacity: 1;" id="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="well well-light padding-10">
          <div class="row">
          
            <div class="col-sm-12">
              <div id="myCarousel" class="carousel fade profile-carousel" >
                <div class="air air-top-right padding-10">
                  <h4 class="txt-color-white font-md">
                    <i class="fa fa-pencil txt-color-white cursor-pointer" style="position:relative; z-index:7; top:-6px;"></i>
                  </h4>
                </div>
                <div class="carousel-inner cursor-pointer" onclick="change_avatar('P');">
                  <div id="user-header-pic" class="item active"></div>
                </div>
              </div>
            </div>
          
            <div class="col-xs-1">
              <div id="user-profile-pic" class="col-sm-3 profile-pic" style="float:right; cursor:pointer;" onclick="change_avatar('A');">
                <i class="fa fa-pencil txt-color-white" style="position:relative; z-index:7; left: 90px; top:-6px; cursor:pointer;" ></i>                
              </div>
            </div>
            <div class='modal fade' id='change_avatar' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' 
                  style='overflow-y:scroll;overflow:auto'>
                  <div class="modal-dialog" role="document" style="width:600px; top:100px;">
                    <div class="modal-content">
                      <div class="modal-body">
                        <form class="form-horizontal" id="change_foto" method="post" enctype='multipart/form-data'>
                          <div class="row">
                            <div class="col-sm-9">
                              <div class="input-group">
                                <span class="input-group-btn">
                                  <span class="btn btn-primary btn-file">
                                    Browse <input type="file" id="ds_foto1" name="ds_foto1" accept='jpg|jpeg' maxlength="1" multiple>
                                  </span>
                                </span>
                                <input type="text" class="form-control" readonly>
                              </div>
                            </div>
                            <div class="col-sm-1">
                              <button type="submit" class="btn btn-primary" id="btn_changer">Change</button>
                            </div>
                          </div>                          
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
                <script>
                /** Funcion para mostrar la forma de cargar el archivo*/
                function change_avatar(img){
                  $('#change_avatar').modal('toggle');
                  $('#change_avatar').append('<input type=\'hidden\' id=\'type_img\' name=\'type_img\' value=\''+img+'\'>');
                }
                $(function(){
                  /** Por dedault estara desactivado el boton **/
                  $("#btn_changer").addClass("disabled");
                  
                  /** Activamos el botn si cargo un archivo **/
                  $("#ds_foto1").change(function(){
                      var file = $(this).val();
                      if(file != "")
                        $("#btn_changer").removeClass("disabled");
                      else
                        $("#btn_changer").addClass("disabled");
                  });
                });
                
                $("#change_foto").on("submit", function(e){
                  e.preventDefault();
                  var dato = $("#ds_foto1").prop("files")[0];
                  var type_img = $("#type_img").val();
                  var formData = new FormData(document.getElementById("change_foto"));
                  formData.append("ds_foto1", dato);
                  formData.append("type_img", type_img);
                  $.ajax({
                      url: "site/change.php",
                      type: "post",
                      dataType: "json",
                      data: formData,
                      cache: false,
                      contentType: false,
                      processData: false,
                      success: function(result){
                        if(!result.datos.fg_error)
                          location.reload();
                      }
                  })
                });
                </script>
            <div class="col-xs-12">            
              <div class="well well-light no-margin no-padding">
                <div class="row padding-10">                
                  <div class="row padding-10">
                  <!-- <div class="col-sm-1"></div>                   -->
                  <div class="tabs-left col-sm-3 text-right">
                    <ul class="nav nav-tabs">
                      <?php
                        # Consulta los tabs para los students
                        $Query = "SELECT nb_tab".$sufix.", no_orden, fl_tab FROM k_tabs_profile WHERE fl_tab IN(1,2,3,4,5,10,11 ";
                        if($fl_perfil ==PFL_ADMINISTRADOR)
                          $Query.=", 9 ";
                          $Query.=" ) and fg_tipo='S' ORDER BY no_orden";
                          $rs = EjecutaQuery($Query);
                        for($i=0;$row=RecuperaRegistro($rs);$i++){
                          $class_dft = "";
                          if($row[1]==1)
                          $class_dft = "active";
                          echo "
                          <li class='".$class_dft."' id='li-".$row[1]."'>
                            <a href='#tab-".$row[1]."' data-toggle='tab' onclick=tabs(".$row[1].");>".str_texto($row[0])."
                              <i class='fa'></i>
                            </a>
                          </li>";
                        }
                      ?>
                    </ul>
                  </div>
                  <div class="col-sm-8">
                    <form name="datos" role="form" id="formstudents" class="form-horizontal" action="site/profile_iu.php" method="post" enctype='multipart/form-data'>
                      <div class="tab-content" id="user-form-info" style="padding-top:30px;">                     
                      </div>
                    </form>
                  </div>
                </div>
                </div>
              </div>
            </div>
            <!-- <div class="col-xs-1"></div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--SCRIPT PARA EL FORMULARIO -->
  <script type="text/javascript">
    var user = <?php GetProfile($fl_usuario); ?>;
    var perfil = <?php echo $fl_perfil; ?>;
    $("#user-profile-pic").append(user.profile.ds_ruta_avatar);
    $("#user-header-pic").append(user.profile.ds_ruta_foto);
	
    var info = "";
    info += "<div class='tab-pane active' id='tab-1'>";
      info += FormInputHidden("clave", user.profile.clave);
      info += FormInputHidden("cl_sesion", user.profile.cl_sesion);
      info += FormInputHidden("ds_login", user.profile.ds_login);
      if((perfil == <?php echo PFL_ADMINISTRADOR; ?>)||(perfil == <?php echo PFL_ADM_CSF; ?>)){
        info += FormInput("ds_instituto", "text", "<?php echo ObtenEtiqueta(1127); ?>", user.profile.ds_instituto, "ds_instituto_err", true, 'fa-institution');   
          info += FormInputHidden("school_id", "text", "School ID", user.profile.school_id, "school_id_err", true, 'fa-institution');               
      }
      info += FormInputStatic("<?php echo ObtenEtiqueta(1128); ?>", user.profile.ds_login);      
      info += FormInput("ds_alias", "text", "<?php echo ObtenEtiqueta(1129); ?>", user.profile.ds_alias, "ds_alias_err", true, "fa-user", "onkeypress='return validarnn(event);' onkeyup='ChangeAlias();'");
      info += FormInputHidden("ds_alias_bd", user.profile.ds_alias);
      info += FormInput("ds_nombres", "text", "<?php echo ObtenEtiqueta(1130); ?>", user.profile.ds_nombres, "ds_nombres_err", true, "fa-user");
      info += FormInput("ds_amaterno", "text", "<?php echo ObtenEtiqueta(1131); ?>", user.profile.ds_amaterno, "",false, "fa-user");
      info += FormInput("ds_apaterno", "text", "<?php echo ObtenEtiqueta(1132); ?>", user.profile.ds_apaterno, "ds_apaterno_err",true, "fa-user");
      info += FormInput("ds_email", "text", "<?php echo ObtenEtiqueta(1133); ?>", user.profile.ds_email, "ds_email_err",true, 'fa-envelope-o');
      info += FormInputDropbox("fg_genero", "<?php echo ObtenEtiqueta(1134); ?>", {"M":"<?php echo ObtenEtiqueta(115); ?>","F":"<?php echo ObtenEtiqueta(116); ?>", "N": "<?php echo ObtenEtiqueta(128); ?>"}, user.profile.fg_genero);
      info += FormInput("fe_nacimiento", "text", "<?php echo ObtenEtiqueta(1135); ?>", user.profile.fe_nacimiento, "fe_nacimiento_err",true, 'fa-calendar');
	  var grade =<?php GetGradeList();?>;
	  info += FormInputDropbox("fl_grado", "<?php echo ObtenEtiqueta(1308); ?>", grade.grado, user.profile.fl_grado);
	  info += FormInput("ds_profesion", "text", "<?php echo ObtenEtiqueta(2364); ?>", user.profile.ds_profesion, "ds_profesion_err",true, 'fa-graduation-cap');
	  info += FormInput("ds_compania", "text", "<?php echo ObtenEtiqueta(2368); ?>", user.profile.ds_compania, "ds_compania_err",true, 'fa-bank');
      
	info += "</div><div class='tab-pane' id='tab-2'>";
        info += FormInput("ds_website", "text", "<?php echo ObtenEtiqueta(1925); ?>", user.profile.ds_website, "", false, " fa-globe");
        // El ds_gustos sera nombrado a  super power
        info += FormInput("ds_gustos","text", "<?php echo ObtenEtiqueta(1926); ?>", user.profile.ds_gustos, "", false, "fa-heart");
        info += FormInput("ds_pasatiempos","text", "<?php echo ObtenEtiqueta(1927); ?>", user.profile.ds_pasatiempos, "", false, " fa-headphones ");
        info += FormInput("ds_power","text", "<?php echo ObtenEtiqueta(1928); ?>", user.profile.ds_power, "", false, "fa-pinterest-square");
        info += FormInput("ds_favorite_movie","text", "<?php echo ObtenEtiqueta(1929); ?>", user.profile.ds_favorite_movie, "", false, "fa-file-movie-o");
    info += "</div><div class='tab-pane' id='tab-4'>";
      info += FormInput("ds_number", "text", "<?php echo ObtenEtiqueta(1136); ?>", user.profile.ds_add_number, "ds_number_err",true, 'fa-sort-numeric-asc ');
      info += FormInput("ds_street", "text", "<?php echo ObtenEtiqueta(1137); ?>", user.profile.ds_add_street, "ds_street_err",true, 'fa-angle-double-right');
      info += FormInput("ds_city", "text", "<?php echo ObtenEtiqueta(1138); ?>", user.profile.ds_add_city, "ds_city_err",true, 'fa-angle-double-right');
      var provincia = <?php GetProvinciasList(); ?>;
      info += FormInput("ds_state", "text", "<?php echo ObtenEtiqueta(1139); ?>", user.profile.ds_add_state, "ds_state_err",true, 'fa-angle-double-right');
      info += FormInputDropbox("ds_state", "<?php echo ObtenEtiqueta(1139); ?>", provincia.name_prov, user.profile.ds_add_state);     
      info += FormInput("ds_zip", "text", "<?php echo ObtenEtiqueta(1140); ?>", user.profile.ds_add_zip, "ds_zip_err",true, 'fa-angle-double-right');
      var country = <?php GetCountryList(); ?>;
      info += FormInputDropbox("fl_pais", "<?php echo ObtenEtiqueta(1141); ?>", country.name, user.profile.fl_pais);
    info += "</div><div class='tab-pane' id='tab-5'>";
      info += FormInput("ds_password", "password", "<?php echo ObtenEtiqueta(1142); ?>", "", "ds_password_err", false, "fa-lock");
      info += FormInput("ds_password_conf", "password", "<?php echo ObtenEtiqueta(1143); ?>", "", "", false, "fa-lock");
    info += "</div><div class='tab-pane' id='tab-6' >";
      info += FormInputHidden("ds_ruta_avatar", user.profile.ds_avatar);
      info += FormInputUpload("avatar", "<?php echo ObtenEtiqueta(1144); ?>", "", "jpg|jpeg", "ds_ruta_avatar_err");
      info += FormInputHidden("ds_ruta_foto", user.profile.ds_foto);
      info += FormInputUpload("foto", "<?php echo ObtenEtiqueta(1145); ?>", "", "jpg|jpeg", "ds_ruta_foto_err");
      if(perfil == <?php echo PFL_ADMINISTRADOR ?>){      
        info += FormInputHidden("ds_foto_inst", user.profile.ds_foto_inst);
        info += FormInputUpload("foto_inst", "<?php echo ObtenEtiqueta(1170); ?>", "", "jpg|jpeg", "ds_foto_inst_err");
      }
    info += "</div>";
	if(perfil == <?php echo PFL_ADMINISTRADOR ?>){
	info +="<div  class='tab-pane' id='tab-9'>";
	info += FormInput("ds_rfc", "text", "<?php echo ObtenEtiqueta(1792); ?>", user.profile.ds_rfc, "",false, "fa-file-text");
	info+="</div>";
	}
	info +="<div  class='tab-pane' id='tab-10'>";
	info +=FormInputCheckBox('fg_nuevo_post',"<?php echo ObtenEtiqueta(2369);?>",'<?php echo ObtenEtiqueta(2373);?>',user.profile.fg_nuevo_post);
	info +=FormInputCheckBox('fg_coment_post',"<?php echo ObtenEtiqueta(2370);?>",'<?php echo ObtenEtiqueta(2374);?>',user.profile.fg_coment_post);
	info +=FormInputCheckBox('fg_like_post',"<?php echo ObtenEtiqueta(2371);?>",'<?php echo ObtenEtiqueta(2375);?>',user.profile.fg_like_post);
	info +=FormInputCheckBox('fg_ayuda_post',"<?php echo ObtenEtiqueta(2372);?>",'<?php echo ObtenEtiqueta(2376);?>',user.profile.fg_ayuda_post);
	info +=FormInputCheckBox('fg_follow',"<?php echo ObtenEtiqueta(2556);?>",'<?php echo ObtenEtiqueta(2557);?>',user.profile.fg_follow);
	info +=FormInputCheckBox('fg_ayuda_post_all_comunity',"<?php echo ObtenEtiqueta(2579);?>",'<?php echo ObtenEtiqueta(2376);?>',user.profile.fg_ayuda_post_all_comunity);
    info +=FormInputCheckBox('fg_session_completed',"<?php echo ObtenEtiqueta(2597);?>",'<?php echo ObtenEtiqueta(2596);?>',user.profile.fg_session_completed);
	

      
	info+="</div>";
  info +="<div  class='tab-pane' id='tab-11'>";
  var idiom = <?php GetLanguage(); ?>;
  info += FormInputDropbox("fl_language", "Select Language", idiom.langname, user.profile.fl_language);
  info+="</div>";

    info += FormSubmit();
    
    $("#user-form-info").append(info);
    

    // jquery form
    loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/jquery-form/jquery.form.min.js", InitAjaxForm);
    function InitAjaxForm(){
      var option = {
        dataType: 'json',
        success: function(result){        
          if(result.datos.fg_error){
            /** Eliminamos todas las tabs y los div activados **/
            $("#li-1").removeClass("active");  $("#tab-1").removeClass("active");
            $("#li-2").removeClass("active");  $("#tab-2").removeClass("active");
            $("#li-4").removeClass("active");  $("#tab-4").removeClass("active");
            $("#li-5").removeClass("active");  $("#tab-5").removeClass("active");
            $("#li-6").removeClass("active");  $("#tab-6").removeClass("active");
            $("#li-10").removeClass("active");  $("#tab-10").removeClass("active");
            $("#li-11").removeClass("active");  $("#tab-11").removeClass("active");
            /** Se activa la tab del error **/
            $("#li-"+result.datos.no_tab).addClass("active");  $("#tab-"+result.datos.no_tab).addClass("active");
            /** Mostramos el error **/
            /* tab 1 */
            $("#ds_instituto_err").text(result.datos.ds_instituto_err);
            if(result.datos.ds_instituto_err)
              $("#div_ds_instituto").addClass("state-error");
            $("#ds_alias_err").text(result.datos.ds_alias_err);
            if(result.datos.ds_alias_err)
              $("#div_ds_alias").addClass("state-error");            
            $("#ds_nombres_err").text(result.datos.ds_nombres_err);
            if(result.datos.ds_nombres_err)
              $("#div_ds_nombres").addClass("state-error");
            $("#ds_apaterno_err").text(result.datos.ds_apaterno_err);
            if(result.datos.ds_apaterno_err)
              $("#div_ds_apaterno").addClass("state-error");
            $("#ds_email_err").text(result.datos.ds_email_err);
            if(result.datos.ds_email_err)
              $("#div_ds_email").addClass("state-error");
            $("#fe_nacimiento_err").text(result.datos.fe_nacimiento_err);
            if(result.datos.ds_email_err)
              $("#div_fe_nacimiento").addClass("state-error");
            /* tab 4 */
            $("#ds_number_err").text(result.datos.ds_number_err);
            if(result.datos.ds_number_err)
              $("#div_ds_number").addClass("state-error");
            $("#ds_street_err").text(result.datos.ds_street_err);
            if(result.datos.ds_street_err)
              $("#div_ds_street").addClass("state-error");
            $("#ds_city_err").text(result.datos.ds_city_err);
            if(result.datos.ds_city_err)
              $("#div_ds_city").addClass("state-error");
            $("#ds_state_err").text(result.datos.ds_state_err);
            if(result.datos.ds_state_err)
              $("#div_ds_state").addClass("state-error");
            $("#ds_zip_err").text(result.datos.ds_zip_err);
            if(result.datos.ds_zip_err)
              $("#div_ds_zip").addClass("state-error");
            /* tab 5 */
            $("#ds_password_err").text(result.datos.ds_password_err);
            if(result.datos.ds_password_err)
              $("#div_ds_password").addClass("state-error");
            /* tab 6*/
            $("#ds_ruta_avatar_err").text(result.datos.ds_ruta_avatar_err);
            if(result.datos.ds_ruta_avatar_err)
              $("#span_avatar").removeClass("btn-primary").addClass("btn-danger");
            $("#ds_ruta_foto_err").text(result.datos.ds_ruta_foto_err);
            if(result.datos.ds_ruta_foto_err)
              $("#span_foto").removeClass("btn-primary").addClass("btn-danger");
            $("#ds_foto_inst_err").text(result.datos.ds_foto_inst_err);
            if(result.datos.ds_foto_inst_err)
              $("#span_foto_inst").removeClass("btn-primary").addClass("btn-danger");
          } 
          else {
            location.reload();
          }
        }
      };
      $("#user-form-info").ajaxForm(option); 
      $("#formstudents").ajaxForm(option);
      /** Funcion para el calendario de la fecha de nacimiento **/
      $('#fe_nacimiento').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        showAnim: 'slideDown',
        showOtherMonths: true,
        selectOtherMonths: true,
        showMonthAfterYear: false,
        yearRange: 'c-50:c+50',
        autoSize: true,
        prevText : '<',
				nextText : '>'
      });   
    
      $('#fe_nacimiento').addClass('hasDatepicker');
    }

    function FormInputStatic(title, info, clas_lbl='col-sm-3 padding-10 control-label', clas_cmp='col-sm-8 input'){
      var input =
        "<div class='row padding-10 smart-form'>" +
          "<label class='"+clas_lbl+"'>"+title+"</label>" +
          "<div class='"+clas_cmp+"'>"+         
            "<p class='form-control-static'>"+info+"</p>"+
          "</div>" +
        "</div>" ;
      return input;
    }
    function FormInput(id, type, title, value, error, requerido, icono, script=''){
      //  Mostramos que campos son requeridos
      var required, req;
      if(requerido){
        required = 'required';
        req = '*';
      }
      else{
        required = '';
        req = '';  
      }
      var icono;
      if(icono == "")
        icono = "";
      else
        icono = "<i class='icon-append fa "+icono+"'></i>";
      
      var	input =
        "<div class='row padding-10 smart-form' id='input_"+id+"'>" +
          "<label for='"+id+"' class='col-sm-3 padding-10 control-label'>"+req+" "+title+"</label>" +
          "<div class='col-sm-8 input' id='div_"+id+"'> "+icono+" "+
          
            "<input type='"+type+"' class='form-control' name='"+id+"' id='"+id+"' value='"+value+"' "+script+">" + 
            "<p id='"+error+"' class='form-control-static text-danger'></p>"+
            
          "</div>"+
        "</div>";
      return input;
    }
    function FormInputHidden(id, value){
      var input = "<input type='hidden' id='"+id+"' name='"+id+"' value='"+value+"'>";
      return input;
    }
    function FormInputTextArea(id, title, value){
      var	input =
        "<div class='row padding-10'>" +
          "<label for='"+id+"' class='col-sm-3 control-label'>"+title+"</label>" +
          "<div class='col-sm-9'>" +
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
        "<div class='row padding-10 smart-form' id='drop_"+id+"'>" +
          "<label for='"+id+"' class='col-sm-3 padding-10 control-label'>"+title+"</label>" +
          "<div class='col-sm-8 input'>" +
            "<select id='"+id+"' name='"+id+"' class='select2'>"+option+"</select>" +
          "</div>" +
        "</div>" ;
      return input;
    }
	
	
	
    function FormInputCheckBox(id,ds_etiqueta,ds_texto,chequed){
		
		if(chequed==1)
			var cheked=" checked='checked' ";
		else
			var cheked="";
		
		var input=
		"<div class='smart-form'>"+
		"<label class='checkbox'>"+
		 " <input type='checkbox' id="+id+" name="+id+" "+cheked+">"+
		"<i style='margin-top:6px;'></i>"+ds_etiqueta+": <small class='text-muted'>"+ds_texto+"</small></label>"+
		"</div>";
		
		return input;
		
	}
	
	
	
	
    function FormSubmit(){
      var input = 
      "<div class='form-group' style='padding-bottom:10px;'>" +
        "<div class='col-sm-offset-2 col-sm-10'>" +
          //"<button type='submit' onclick='javascript:document.datos.submit();' class='btn btn-default'>Submit</button>" +
          "<br><br><button type='submit' class='btn btn-primary' id='profile_save'><?php echo ObtenEtiqueta(13); ?></button>" +
          //"<button type='button' id='user-form-submit' class='btn btn-default'>Submit</button>" +
        "</div>" +
      "</div>";
      return input;
    }
    function FormInputUpload(id, title, info, accept, error){
      if(accept.length != 0){
        var name = id+"[]";
      } else {
        var name = id;
      }
      var input =	
        "<div class='row padding-10'>" +
          "<label class='col-sm-3 control-label'>"+title+"</label>" +
          "<div class='col-sm-9'>"+
            "<div class='input-group'>" +
              "<span class='input-group-btn'>" +
                "<span class='btn btn-primary btn-file' id='span_"+id+"'>" +
                  "Browse <input type='file' id='"+id+"' name='"+name+"' size='60' accept='"+accept+"' maxlength='1' multiple>" +
                "</span>" +
              "</span>" +
              "<input type='text' class='form-control' readonly>" +              
            "</div>" +
            "<p id='"+error+"' class='form-control-static text-danger'></p>"+
          "</div>" +
        "</div>";
      return input;
    }
    // aparecer o ocultar el boton submit
    function tabs(tab){
      // En el tabs de las conexiones no mostrara el boton
      if(tab!=7)
        $('#profile_save').css('display','inline');
      else
        $('#profile_save').css('display','none');
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

    /*Obtenemos el pais*/
    pais = $("#fl_pais").val();
    /*Si el pais es direfente de canada mostrara el campo*/
    /*Si es canada mostrar las provincias*/
    if(pais == '38'){
      $("#drop_ds_state").show();
      $("#input_ds_state").hide();
      $("select[id=ds_state]").attr("name","ds_state");
      $(":input#ds_state").removeAttr("name");
    }
    else{
      $("#drop_ds_state").hide();
      $("#input_ds_state").show();
      $(":input#ds_state").attr("name","ds_state");
      $("select[id=ds_state]").removeAttr("name");
    }
      
    $("#fl_pais").change(function(){
      var seleccionado = $(this).val();
      if(seleccionado == '38'){
        $("#drop_ds_state").show();
        $("#input_ds_state").hide();
        $("select[id=ds_state]").attr("name","ds_state");
        $(":input#ds_state").removeAttr("name");
      }
      else{
        $("#drop_ds_state").hide();
        $("#input_ds_state").show();
        $(":input#ds_state").attr("name","ds_state");
        $("select[id=ds_state]").removeAttr("name");
      }
    });

    function ChangeAlias() {
        var x = document.getElementById("ds_alias");
        var val = x.value;
        var user = '<?php echo $fl_usuario; ?>';        
        if(val.length>0){
          $.ajax({
            type: "POST",
            dataType: 'json',
            url: "site/valida_alias.php",
            async: false,
            data: "ds_alias="+val+
                  "&fl_usuario="+user,          
            success: function(result){
              var error = result.resultado.fg_error;
              if(error==true){
                $("#div_ds_alias").addClass('state-error');
                $("#ds_alias_err").empty().append('<?php echo ObtenEtiqueta(2011); ?>');
              }
              else{
                $("#div_ds_alias").removeClass('state-error');
                $("#ds_alias_err").empty();
              }
            }
          });
        }
        else{
          $("#div_ds_alias").addClass('state-error');
          $("#ds_alias_err").empty().append('<?php echo ObtenMensaje(ERR_REQUERIDO); ?>');
        }
        
    }

  </script>
<?php
  }
?>
<script>
pageSetUp();
$(".modal-backdrop").removeClass('in');
</script>
