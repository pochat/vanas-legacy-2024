<?php 
	# community.php shows the list of users (teachers / students)
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $classmate = RecibeParametroNumerico('classmate', True);

	# List of program's names for filtering
	function FilterProgram(){
		$Query  = "SELECT fl_programa, nb_programa ";
		$Query .= "FROM c_programa ";
		$Query .= "ORDER BY nb_programa";
		$Query  = "SELECT c.fl_programa, c.nb_programa, count(a.fl_usuario) ";
		$Query .= "FROM c_usuario a, k_ses_app_frm_1 b, c_programa c ";
		$Query .= "WHERE a.cl_sesion=b.cl_sesion ";
		$Query .= "AND b.fl_programa=c.fl_programa ";
		$Query .= "AND a.fg_activo='1' ";
		$Query .= "GROUP BY c.fl_programa ";
		$Query .= "ORDER BY c.nb_programa";
		$rs = EjecutaQuery($Query);

		$result = array("type" => "program");
		$program = array();

		// program list is stored as value : name (count)
		while($row = RecuperaRegistro($rs)){
			$program += array($row[0] => str_uso_normal($row[1])." (".$row[2].")");
		}
		$result += array("list" => $program);
		echo json_encode((Object) $result);
	}

	# List of countries available for filtering 
	function FilterCountry(){
		$Query  = "SELECT fl_pais, ds_pais, SUM(cuantos) FROM (";
		$Query .= "SELECT c.fl_pais, c.ds_pais, COUNT(a.fl_usuario) cuantos ";
		$Query .= "FROM c_usuario a, k_ses_app_frm_1 b, c_pais c ";
		$Query .= "WHERE a.cl_sesion=b.cl_sesion ";
		$Query .= "AND b.ds_add_country=c.fl_pais ";
		$Query .= "AND a.fg_activo='1' ";
		$Query .= "GROUP BY c.fl_pais, c.ds_pais ";
		$Query .= "UNION ";
		$Query .= "SELECT f.fl_pais, f.ds_pais, COUNT(d.fl_usuario) cuantos ";
		$Query .= "FROM c_usuario d, c_maestro e, c_pais f ";
		$Query .= "WHERE d.fl_usuario=e.fl_maestro ";
		$Query .= "AND e.fl_pais=f.fl_pais ";
		$Query .= "AND d.fg_activo='1' ";
		$Query .= "GROUP BY f.fl_pais, f.ds_pais ";
		$Query .= ") todos ";
		$Query .= "GROUP BY fl_pais, ds_pais ";
		$Query .= "ORDER BY ds_pais";
		$rs = EjecutaQuery($Query);

		$result = array("type" => "country");
		$country = array();

		// country list is stored as value : name (count)
		while($row = RecuperaRegistro($rs)){
			$country += array($row[0] => str_uso_normal($row[1])." (".$row[2].")");
		}
		$result += array("list" => $country);
		echo json_encode((Object) $result);
	}

	# Initial Names (currently not used)
	function FilterName(){
		$Query  = "SELECT DISTINCT(ASCII(UCASE(ds_nombres))) ";
		$Query .= "FROM c_usuario ";
		$Query .= "WHERE fl_perfil IN(".PFL_ESTUDIANTE.", ".PFL_MAESTRO.") ";
		$Query .= "AND fg_activo='1'";
		$rs = EjecutaQuery($Query);

		return $rs;
	}
?>
<style>

.well {
    background: #ffffff !important;
	
}

</style>
<!-- community filter bar -->
<div id="community-header">
	<div class="btn-group">
		<button id="filter-user-title" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
			User <span class="caret"></span>
		</button>
		<ul id="filter-user" class="dropdown-menu">
			<li value="0" class="active"><a href="javascript:void(0);" onClick="Category('0');">All</a></li>
			<li value="T"><a href="javascript:void(0);" onClick="Category('T');">Teacher</a></li>
			<li value="S"><a href="javascript:void(0);" onClick="Category('S');">Student</a></li>
		</ul>
	</div>
	<div class="btn-group">
		<button id="filter-program-title" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
			Program <span class="caret"></span>
		</button>
		<ul id="filter-program" class="dropdown-menu">
			<li value ="0" class="active"><a href="javascript:void(0);" onclick="Program(0);">All</a></li>
		</ul>
	</div>
	<div class="btn-group">
		<button id="filter-country-title" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
			Country <span class="caret"></span>
		</button>
		<ul id="filter-country" class="dropdown-menu">
			<li value ="0" class="active"><a href="javascript:void(0);" onclick="Country(0);">All</a></li>
		</ul>
	</div>
	<div class="btn-group">
		<button type="button" onclick="Classmate();" class="btn btn-primary">My Class</button>
	</div>
	<div class="btn-group">
		<button type="button" onclick="Reset(0);" class="btn btn-primary">Reset</button>
	</div>
</div>

<!-- community div -->
<div id="community-container" class="well well-light padding-10">
	<div id="teacher-list"></div>
	<div id="student-list"></div>
</div>


<div id='dlg_message'>
  Message to:<b><div id='msg_to'></div></b><br>
  <textarea name='ds_mensaje' id='ds_mensaje' cols=65 rows=4></textarea>
  <input type='hidden' name='fl_usuario_ori' id='fl_usuario_ori' value='<?php echo $fl_usuario; ?>'>
  <input type='hidden' name='fl_usuario_dest' id='fl_usuario_dest'>
</div>
	
	
	


<!-----Modal para enviar la invitacion------>

<!-- Modal -->
<div class="modal fade" id="invitacion_friends" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-sm" role="document" style="width:50%;">
    <div class="modal-content"  id="muestra_ifo_friends">
      



    </div>
  </div>
</div>

<!----------->
	
	
	
	
	
	
	
<script type="text/javascript">
	// Populate the filter lists
	var result;

	result = <?php FilterProgram(); ?>;
	DisplayFilter(result);

	result = <?php FilterCountry(); ?>;
	DisplayFilter(result);

	// Populate the filter lists
	function DisplayFilter(result){
		var list, filterType, options;
		filterType = result.type;
		options = result.list;

		list = "";
		if(filterType == "program"){
			for(var key in options){
				list += 
					"<li value='"+key+"'>"+
						"<a href='javascript:void(0);' onClick='Program("+key+");'>"+
							options[key]+
						"</a>"+
					"</li>";
			}
			$("#filter-program").append(list);
		}
		if(filterType == "country"){
			for(var key in options){
				list += 
					"<li value='"+key+"'>"+
						"<a href='javascript:void(0);' onClick='Country("+key+");'>"+
							options[key]+
						"</a>"+
					"</li>";
			}
			$("#filter-country").append(list);
		}
	}

	// Load helper funcitons 
	loadScript("<?php echo PATH_N_COM_JS; ?>/community.inc.js", InitCommunity);

	function InitCommunity(){
		var classmate = <?php echo json_encode($classmate); ?>;

		// Initialize the lists
		//$("#teacher-list").packery();
		//$("#student-list").packery();
		if(classmate > 0){
			Classmate();
		} else {
			Reset("0");
		}
	}
	
	// Handles teachers and students list
	function DisplayList(result, category){
		var t_container, s_container, send_icon, content;
		t_container = $("#teacher-list");
		s_container = $("#student-list");
		send_icon = <?php echo json_encode(SP_IMAGES."/".ObtenNombreImagen(217)); ?>;

		t_container.empty();
		t_container.height(0);
		s_container.empty();
		s_container.height(0);
		
		if(category == "S"){
			content = JSON.parse(result);
			DisplayStudentProfile(s_container, content.students, send_icon);
			s_container.packery('reloadItems');
		}
		if(category == "T"){
			content = JSON.parse(result);
			DisplayTeacherProfile(t_container, content.teachers, send_icon);
			t_container.packery('reloadItems');
		}
		if(category == "0"){
			content = JSON.parse(result);
			DisplayTeacherProfile(t_container, content.teachers, send_icon);
			DisplayStudentProfile(s_container, content.students, send_icon);
			t_container.packery('reloadItems');
			s_container.packery('reloadItems');
		}
	}

	// Send message emails
	$(document).ready(function() {
		$('#dlg_message').dialog({
      autoOpen: false,
      resizable: false,
      width: 530,
      height: 245,
      hide: 'highlight',
      buttons: {
        Cancel: function() {
          $(this).dialog('close');
        },
        Send: function() {
          var ds_mensaje = $('#ds_mensaje');
          if(ds_mensaje.val().length > 0) {
            SendMessage();
            /*Al enviar el correo se redirigira a messages*/
            //window.location = "index.php#ajax/messages.php";
            $(this).dialog('close');
          } else {
            alert('Please enter a message.');
          }
        }
      }
    });
    $('.ui-dialog-titlebar').hide();
	});

	// remove straying modals when loading another page
	$("aside a[href*='node.php']").on("click", function(){
		DestroyDialog();
	});
	function DestroyDialog(){
		$("#dlg_message").dialog("destroy").remove();
	}
</script>