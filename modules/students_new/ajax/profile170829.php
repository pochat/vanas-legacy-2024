<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");
	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
	if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
  	MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
	}

	function GetProfile($fl_alumno){
		
		$result["profile"] = array();

		# Inicializa variables
    $Query  = "SELECT ds_login, ds_nombres, ds_apaterno, ds_amaterno, fg_genero, ";
    $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, a.ds_email, ds_add_country, fl_zona_horaria, ds_ruta_avatar, ";
    $Query .= "b.ds_ruta_foto, ds_website, ds_gustos, ds_pasatiempos, a.cl_sesion, ds_link_to_portfolio, ";
    $Query .= "ds_add_number,ds_add_street,ds_add_city,ds_add_state,ds_add_zip, ds_alias ";
    $Query .= "FROM c_usuario a, c_alumno b, k_ses_app_frm_1 c ";
    $Query .= "WHERE a.fl_usuario=b.fl_alumno ";
    $Query .= "AND a.cl_sesion=c.cl_sesion ";
    $Query .= "AND fl_usuario=$fl_alumno";
    $row = RecuperaValor($Query);
    $ds_login = str_texto(str_ascii($row[0]));
    $ds_nombres = str_texto(str_ascii($row[1]));
    $ds_apaterno = str_texto(str_ascii($row[2]));
    $ds_amaterno = str_texto(str_ascii($row[3]));
    $fg_genero = $row[4];
    $fe_nacimiento = $row[5];
    $ds_email = str_texto($row[6]);
    $fl_pais = $row[7];
    $fl_zona_horaria = $row[8];
    $ds_avatar = str_texto($row[9]);
    $ds_foto = str_texto($row[10]);
    $ds_website = str_texto(str_ascii($row[11]));
    $ds_gustos = str_texto(str_ascii($row[12]));
    $ds_pasatiempos = str_texto(str_ascii($row[13]));   
    $cl_sesion = $row[14];
    $ds_link_to_portfolio = str_texto($row[15]);
    $ds_add_number = $row[16];
    $ds_add_street = $row[17];
    $ds_add_city = str_texto(str_ascii($row[18]));
    $ds_add_state = str_texto(str_ascii($row[19]));
    $ds_add_zip = $row[20];
    $ds_alias = str_texto(str_ascii($row[21]));
    # Si no hay alias entonces por default sera el login
    if(empty($ds_alias))
      $ds_alias = $ds_login;
    $ds_alias_err = "";
    $ds_nombres_err = "";
    $ds_apaterno_err = "";
    $fe_nacimiento_err = "";
    $ds_email_err = "";
    $fl_pais_err = "";
    $ds_ruta_avatar_err = "";
    $ds_add_number_err = "";
    $ds_add_street_err = "";
    $ds_add_city_err = "";
    $ds_add_state_err = "";
    $ds_add_zip_err = "";
	  
	  if(!empty($ds_avatar)){
	  	$ds_ruta_avatar = "<img src='".PATH_ALU_IMAGES."/avatars/$ds_avatar'>";
	  } else {
	  	$ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."'>";
	  }
	  if(!empty($ds_foto)) {
	  	$ds_ruta_foto = "<img src='".PATH_ALU_IMAGES."/pictures/$ds_foto'>";
	  } else {
	  	$ds_ruta_foto = "<img src='".PATH_N_COM_IMAGES."/vanas-family-edutisse-header.jpg'>";
	  }

	  $result["profile"] += array(
	  	"clave" => $fl_alumno,
	  	"cl_sesion" => $cl_sesion, 
	  	"ds_login" => $ds_login,
	  	"ds_nombres" => $ds_nombres,
	  	"ds_amaterno" => $ds_amaterno,
	  	"ds_apaterno" => $ds_apaterno,
	  	"fg_genero" => $fg_genero,
	  	"fe_nacimiento" => $fe_nacimiento,
			"ds_email" => $ds_email,
      "ds_link_to_portfolio" => $ds_link_to_portfolio,
			"fl_pais" => $fl_pais,
			"fl_zona_horaria" => $fl_zona_horaria,
			"ds_avatar" => $ds_avatar,
			"ds_ruta_avatar" => $ds_ruta_avatar,
			"ds_foto" => $ds_foto,
			"ds_ruta_foto" => $ds_ruta_foto,
			"ds_website" => $ds_website,
			"ds_gustos" => $ds_gustos,
			"ds_pasatiempos" => $ds_pasatiempos,
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
<div style="opacity: 1;" id="content">
  <div class="row">
    <!-- Payment history -->
    <div class="col-xs-12">
      <div class="well well-light padding-10">
        <div class="row">
        
          <div class="col-sm-12">
            <div id="myCarousel" class="carousel fade profile-carousel">
              <div class="carousel-inner">
                <div id="user-header-pic" class="item active"></div>
              </div>
            </div>
          </div>
        
          <div class="col-xs-1">
            <div id="user-profile-pic" class="col-sm-3 profile-pic" style="float:right;"></div>
          </div>
          <div class="col-xs-10">            
            <div class="well well-light no-margin no-padding">
              <div class="well well-light no-margin no-padding">                
                <div class="row padding-top-10">
                <div class="col-sm-1"></div>
                <div class="tabs-left col-sm-2 text-right">
                  <ul class="nav nav-tabs">
                    <?php                    
                    # Consulta los tabs para los students
                    $Query = "SELECT nb_tab, no_orden, fl_tab FROM k_tabs_profile WHERE fg_tipo='S' ORDER BY no_orden";
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row=RecuperaRegistro($rs);$i++){
                      if(($row[2]<=6 AND (SOCIAL_NETWORKS==1 OR SOCIAL_NETWORKS==0))){
                        echo "
                        <li>
                          <a href='#tab-".$row[1]."' data-toggle='tab' onclick=tabs(".$row[1].");>".str_texto($row[0])."
                            <i class='fa'></i>
                          </a>
                        </li>";
                      }
                      if($row[2]==7 AND SOCIAL_NETWORKS==1){
                        echo "
                        <li>
                          <a href='#tab-".$row[1]."' data-toggle='tab' onclick=tabs(".$row[1].");>".str_texto($row[0])."
                            <i class='fa'></i>
                          </a>
                        </li>";
                      }                      
                    }
                    ?>
                  </ul>
                </div>
                <div class="col-sm-8">
                  <form name="datos" role="form" id="formstudents" class="form-horizontal" action="ajax/profile_iu.php" method="post" enctype='multipart/form-data'>
                    <div class="tab-content" id="user-form-info" style="padding-top:30px;">
                    </div>
                  </form>
                </div>
              </div>
              </div>
            </div>
          </div>
          <div class="col-xs-1"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--SCRIPT PARA EL FORMULARIO -->
<script type="text/javascript">
	var user = <?php GetProfile($fl_alumno); ?>;
  
	$("#user-profile-pic").append(user.profile.ds_ruta_avatar);
	$("#user-header-pic").append(user.profile.ds_ruta_foto);
	var info = "";
  info += "<div class='tab-pane active' id='tab-1'>";
    info += FormInputHidden("clave", user.profile.clave);
    info += FormInputHidden("cl_sesion", user.profile.cl_sesion);
    info += FormInputHidden("ds_login", user.profile.ds_login);
    info += FormInputStatic("<?php echo ObtenEtiqueta(762); ?>", user.profile.ds_login);
    info += FormInput("ds_alias", "text", "<?php echo ObtenEtiqueta(803); ?>", user.profile.ds_alias, "ds_alias_err", true);
    info += FormInput("ds_nombres", "text", "<?php echo ObtenEtiqueta(763); ?>", user.profile.ds_nombres, "ds_nombres_err", true);
    info += FormInput("ds_amaterno", "text", "<?php echo ObtenEtiqueta(764); ?>", user.profile.ds_amaterno, "");
    info += FormInput("ds_apaterno", "text", "<?php echo ObtenEtiqueta(765); ?>", user.profile.ds_apaterno, "ds_apaterno_err",true);
    info += FormInput("ds_email", "text", "<?php echo ObtenEtiqueta(766); ?>", user.profile.ds_email, "ds_email_err",true);
    info += FormInputDropbox("fg_genero", "<?php echo ObtenEtiqueta(767); ?>", {"M":"Male","F":"Female"}, user.profile.fg_genero);
    info += FormInputStatic("<?php echo ObtenEtiqueta(768); ?>", user.profile.fe_nacimiento);
    info += FormInputHidden("fe_nacimiento", user.profile.fe_nacimiento);    
  info += "</div><div class='tab-pane' id='tab-2' >";
    info += FormInput("ds_website", "text", "<?php echo ObtenEtiqueta(775); ?>", user.profile.ds_website, "");
    // El ds_gustos sera nombrado a  super power
    info += FormInput("ds_gustos","text", "<?php echo ObtenEtiqueta(776); ?>", user.profile.ds_gustos);
    info += FormInput("ds_pasatiempos","text", "<?php echo ObtenEtiqueta(777); ?>", user.profile.ds_pasatiempos);
  info += "</div><div class='tab-pane' id='tab-3'>";     
    var time_zone = <?php GetTimeZoneList(); ?>;
    info += FormInputDropbox("fl_zona_horaria", "<?php echo ObtenEtiqueta(782); ?>", time_zone.zone, user.profile.fl_zona_horaria);  
  info += "</div>";
  info += "</div><div class='tab-pane' id='tab-4'>";
    info += FormInput("ds_add_number", "text", "<?php echo ObtenEtiqueta(769); ?>", user.profile.ds_add_number, "ds_add_number_err",true);
    info += FormInput("ds_add_street", "text", "<?php echo ObtenEtiqueta(770); ?>", user.profile.ds_add_street, "ds_add_street_err",true);
    info += FormInput("ds_add_city", "text", "<?php echo ObtenEtiqueta(771); ?>", user.profile.ds_add_city, "ds_add_city_err",true);
    var provincia = <?php GetProvinciasList(); ?>;
    info += FormInput("ds_add_state", "text", "<?php echo ObtenEtiqueta(772); ?>", user.profile.ds_add_state, "ds_add_state_err",true);
    info += FormInputDropbox("ds_add_state", "<?php echo ObtenEtiqueta(772); ?>", provincia.name_prov, user.profile.ds_add_state);       
    info += FormInput("ds_add_zip", "text", "<?php echo ObtenEtiqueta(773); ?>", user.profile.ds_add_zip, "ds_add_zip_err",true);
    var country = <?php GetCountryList(); ?>;
    info += FormInputDropbox("fl_pais", "<?php echo ObtenEtiqueta(774); ?>", country.name, user.profile.fl_pais);       
  info += "</div><div class='tab-pane' id='tab-5'>";
    info += FormInput("ds_password", "password", "<?php echo ObtenEtiqueta(778); ?>", "", "ds_password_err");
    info += FormInput("ds_password_conf", "password", "<?php echo ObtenEtiqueta(779); ?>", "", "");
  info += "</div><div class='tab-pane' id='tab-6' >";
    info += FormInputHidden("ds_ruta_avatar", user.profile.ds_avatar);
    info += FormInputUpload("avatar", "<?php echo ObtenEtiqueta(780); ?>", "", "jpg|jpeg");
    info += FormInputHidden("ds_ruta_foto", user.profile.ds_foto);
    info += FormInputUpload("foto", "<?php echo ObtenEtiqueta(781); ?>", "", "jpg|jpeg");
  info += "</div>";
  info += FormSubmit();
  info += "<div class='tab-pane margin-10' id='tab-7'>";
   info += "<table class='table'> " +
          "<tbody>"+
            "<tr><td><strong><?php echo ObtenEtiqueta(785); ?><strong></td></tr>"+
            "<tr>"+
              //"<td class='text-right'><i class='fa fa-facebook-o'></i><strong class='menssage-text'><?php echo ObtenEtiqueta(783);?></strong></td>"+
              "<td class='text-center'><div class='widget-body demo-btns'>"+
                //Connected to Facebook                           
                "<a id='conected_facebook' style='display:inline;' href='javascript:loginn();' class='btn btn-primary'><span class='btn-label'><i class='fa fa-facebook'></i></span><?php echo ObtenEtiqueta(783);?></a>"+
                "<a id='desconected_facebook'  style='display:none;' href='javascript:logout();' class='btn btn-primary'><span class='btn-label'><i class='fa fa-facebook'></i></span><?php echo ObtenEtiqueta(784);?></a> " +
                "<div id='status_face' class='col-xs-12 padding-10 margin-10'></div></div>"+
              "</td>"+
            "</tr>"+
          "</tbody>"+
        "</table>";
  info += "</div>";
  
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
					$("#ds_add_number_err").text(result.datos.ds_add_number_err);
					$("#ds_add_street_err").text(result.datos.ds_add_street_err);
					$("#ds_add_city_err").text(result.datos.ds_add_city_err);
					$("#ds_add_state_err").text(result.datos.ds_add_state_err);
					$("#ds_add_zip_err").text(result.datos.ds_add_zip_err);
				} else {
					location.reload();
				}
			}
		};
		//$("#user-form-info").ajaxForm(option); 
		$("#formstudents").ajaxForm(option); 
	}

	function FormInputStatic(title, info){
		var input =
      "<div class='form-group'>" +
        "<label class='col-sm-3 control-label'>"+title+"</label>" +
			  "<div class='col-sm-9'>"+         
			  	"<p class='form-control-static'>"+info+"</p>"+
			  "</div>" +
			"</div>" ;
		return input;
	}
	function FormInput(id, type, title, value, error, requerido){
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

		var	input =
      "<div class='form-group' id='input_"+id+"'>" +
				"<label for='"+id+"' class='col-sm-3 control-label '>"+req+"&nbsp;"+title+"</label>" +
				"<div class='col-sm-9'>" + 
			  	"<input type='"+type+"' class='form-control' name='"+id+"' id='"+id+"' value='"+value+"' "+required+">" + 
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
			"<div class='form-group'>" +
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
      "<div class='form-group' id='drop_"+id+"'>" +
				"<label for='"+id+"' class='col-sm-3 control-label'>"+title+"</label>" +
				"<div class='col-sm-9'>" +
			  	"<select id='"+id+"' name='"+id+"'>"+option+"</select>" +
			  "</div>" +
			"</div>" ;
		return input;
	}
	function FormSubmit(){
		var input = 
		"<div class='form-group' style='padding-bottom:10px;'>" +
	    "<div class='col-sm-offset-2 col-sm-10'>" +
	      //"<button type='submit' onclick='javascript:document.datos.submit();' class='btn btn-default'>Submit</button>" +
	      "<button type='submit' class='btn btn-primary' id='profile_save'><?php echo ObtenEtiqueta(13); ?></button>" +
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
			  "<label class='col-sm-3 control-label'>"+title+"</label>" +
			  "<div class='col-sm-9'>"+
		  		"<div class='input-group'>" +
			      "<span class='input-group-btn'>" +
			        "<span class='btn btn-primary btn-file'>" +
			          "Browse <input type='file' id='"+id+"' name='"+name+"' size='60' accept='"+accept+"' maxlength='1' multiple>" +
			        "</span>" +
			      "</span>" +
			      "<input type='text' class='form-control' readonly>" +
					"</div>" +
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
  $(document).ready(function(){
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    },true); 
  });

  /*Obtenemos el pais*/
  pais = $("#fl_pais").val();
  /*Si el pais es direfente de canada mostrara el campo*/
  /*Si es canada mostrar las provincias*/
  if(pais == '38'){
    $("#drop_ds_add_state").show();
    $("#input_ds_add_state").hide();
    $("select[id=ds_add_state]").attr("name","ds_add_state");
    $(":input#ds_add_state").removeAttr("name");
  }
  else{
    $("#drop_ds_add_state").hide();
    $("#input_ds_add_state").show();
    $(":input#ds_add_state").attr("name","ds_add_state");
    $("select[id=ds_add_state]").removeAttr("name");
  }
    
  $("#fl_pais").change(function(){
    var seleccionado = $(this).val();
    if(seleccionado == '38'){
      $("#drop_ds_add_state").show();
      $("#input_ds_add_state").hide();
      $("select[id=ds_add_state]").attr("name","ds_add_state");
      $(":input#ds_add_state").removeAttr("name");
    }
    else{
      $("#drop_ds_add_state").hide();
      $("#input_ds_add_state").show();
      $(":input#ds_add_state").attr("name","ds_add_state");
      $("select[id=ds_add_state]").removeAttr("name");
    }
  });
</script>