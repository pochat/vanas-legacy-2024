<?php 
	# This class sets up the nav tabs for a student's courses, weeks, and assignments
	# NOTE 1: The class model is abandoned half way in the development. there was a loop setup to display multiple classes for a student
	# but are now removed until further notice.
	# class names will be replaced by program names
	# now we're back to one class per student 

	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recibe parametros
  $fl_alumno = RecibeParametroNumerico('student', True);
  if(empty($fl_alumno)){
    $fl_alumno = $fl_usuario;
  }
  $no_semana = RecibeParametroNumerico('week', True);
  $nb_tab = RecibeParametroHTML('tab', False, True);
  //$no_err = RecibeParametroNumerico('err', True);
  # Revisa si el alumno solicitado es el usuario de la sesion
  if($fl_usuario <> $fl_alumno) {
    $fg_otro_alumno = True;
  } else {
    $fg_otro_alumno = False;
  }

  # Set the number of weeks for the student to view future weeks in advance
  $weeks_advance = 2;

  # Default variables
  $semana_act = ObtenSemanaActualAlumno($fl_alumno);
	$max_semana = ObtenSemanaMaximaAlumno($fl_alumno);
  $weeks_setting = array(
  	"selected" => $no_semana,
  	"current" => $semana_act,
  	"max" => $max_semana,
  	"advance" => $weeks_advance
  );

  function GetDesktopTabs($fl_alumno, $weeks_setting){
  	# Add the program the student is in, may have multiple programs in the future, only has one for now
  	$result["programs"] = array("1" =>  ObtenNombreProgramaAlumno($fl_alumno));

  	# Create the week array
  	$weeks = array();
  	$max_semana = $weeks_setting["max"];
  	for($i=1; $i<=$max_semana; $i++){
  		//$weeks[$i] = "<i class='fa fa-lg fa-arrow-circle-o-down'></i><span class='hidden-mobile hidden-tablet'> Week </span>".$i;
  		$weeks[$i] = "Week ".$i;
  	}
  	$result["weeks"] = $weeks;

  	$result["tabs"] = array(
			"1" => "Video Lecture",
			"2" => "Video Brief",
			"3" => "Assignment",
			"4" => "Assignment Ref",
			"5" => "Sketch",
			"6" => "Sketch Ref",
			"7" => "Critique"
			//"8" => ObtenEtiqueta(1670)
		);
		return $result;
  }

  function GetDefaultTabs($nb_tab, $weeks_setting, $fg_otro_alumno){
  
  	# Is '1' right now as there is only one program per student, may have multiple programs in the future
  	# Default program
  	$result["program"] = 1;

  	# Extra weeks setting
  	$no_semana = $weeks_setting["selected"];
  	$semana_act = $weeks_setting["current"];
  	$max_semana = $weeks_setting["max"];
  	$weeks_advance = $weeks_setting["advance"];

  	# Default week
    /*if(empty($no_semana) OR $no_semana > $max_semana OR $no_semana > $semana_act)
      $result["week"] = (int)$semana_act;*/
  	if(!empty($no_semana)){
  		# Check if input week is smaller than max weeks and max visible weeks
  		# max visible weeks = current week + weeks in advance
  		if($no_semana <= $max_semana && $no_semana <= $semana_act + $weeks_advance){
				$result["week"] = (int)$no_semana;
  		} else {
  			$result["week"] = (int)$semana_act + $weeks_advance;
  		}
		} else {
			$result["week"] = (int)$semana_act;
		}

		# Default tab
		/*switch($nb_tab){
			case "lecture": $result["tab"] = 1; break;
			case "brief": $result["tab"] = 2; break;
			case "assignment": $result["tab"] = 3; break;
			case "assignment_ref": $result["tab"] = 4; break;
			case "sketch": $result["tab"] = 5; break;
			case "sketch_ref": $result["tab"] = 6; break;
			case "critique": $result["tab"] = 7; break;
			default: $result["tab"] = 1;
		}*/
    if(($no_semana < $semana_act-2 AND empty($fg_otro_alumno)) OR (!empty($fg_otro_alumno)))
      $result["tab"] = 3;
  	# All result values are returned as int
  	return $result;
  }

  # Creates a list of boolean of week tabs that should be disabled
  # true = open tab, false = disable tab
  function GetDisabledWeeks($weeks_setting){
  	$semana_act = $weeks_setting["current"];
  	$max_semana = $weeks_setting["max"];
  	$weeks_advance = $weeks_setting["advance"];

  	for($i=1; $i<=$max_semana; $i++){
  		if($i <= $semana_act){
	  		$list[$i] = true;
	  	} else {
	  		$list[$i] = false;
	  	}
  	}
  	return (Object) $list;
  }

  # Creates a list of boolean of "tab" tabs that should be disabled
  # true = open tab, false = disable tab
  function GetDisabledTabs($weeks_setting, $fg_otro_alumno){
		$semana_act = $weeks_setting["current"];
  	$max_semana = $weeks_setting["max"];
  	$weeks_advance = $weeks_setting["advance"];

		# Allow to view lecture and brief 2 weeks behind, and weeks in advance  	
  	for($i=1; $i<=$max_semana; $i++){
  		if($i < $semana_act - 2 OR $i > $semana_act + $weeks_advance OR $fg_otro_alumno){
  			$list[$i] = false;
  		} else {
  			$list[$i] = true;
  		}
  	}
    
  	return (Object) $list;
  }

  # Function para obtener el header
  # Obtenemos datos del students
  function HeaderProfile($fl_alumno){
    
    $result["profile"] = array();
    
    $Query = "SELECT ds_ruta_avatar, ds_ruta_foto FROM c_alumno WHERE fl_alumno=".$fl_alumno."";
    $row = RecuperaValor($Query);
    $ds_avatar = $row[0];
    $ds_foto = $row[1];
    
    if(!empty($ds_avatar)){
	  	$ds_ruta_avatar = "<img  src='".PATH_ALU_IMAGES."/avatars/$ds_avatar'>";
	  } else {
	  	$ds_ruta_avatar = "<img  src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."'>";
	  }
	  if(!empty($ds_foto)) {
	  	$ds_ruta_foto = "<img width='100%' src='".PATH_ALU_IMAGES."/pictures/$ds_foto'>";
	  } else {
	  	$ds_ruta_foto = "<img width='100%' src='".PATH_N_COM_IMAGES."/vanas-family-edutisse-header.jpg' >";
	  }
     $result["profile"] += array(
			"ds_avatar" => $ds_avatar,
			"ds_ruta_avatar" => $ds_ruta_avatar,
			"ds_foto" => $ds_foto,
			"ds_ruta_foto" => $ds_ruta_foto,			
	  );

	  echo json_encode((Object) $result);
  }
  
?>

<!-- User profile-->
<div class="row margin-bottom-10">
  <div class="col-sm-12 col-md-12 col-lg-12">
    <div id="myCarousel" class="carousel fade profile-carousel">
      <div class="carousel-inner">
        <div id="header_student" class="item active">
          <div class="col-sm-5 padding-5" style="position:absolute;">
            <div class="carousel-inner profile-pic" style="background-color:rgba(255, 255, 255, 0.82);">
              <div id="user-profile-container" class="profile-container no-margin">
              <?php
              # Muestra el icono de senemail si es otro alumno de quien ve su desktop
              if($fg_otro_alumno){
               echo '<a class="pull-right" href="'.PATH_N_ALU.'/index.php#ajax/messages.php?usr='.$fl_alumno.'">
                    <img src="'.SP_IMAGES."/".ObtenNombreImagen(217).'" title="Send message" height="16" width="16" style="margin:4px;">
                  </a>';
              }
              ?>
              </div>                  
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Desktop content -->
<div class="row">
	<div class="col-xs-12">
		<div id="desktop-container" class="well no-padding"></div>
	</div>
</div>

<!-- Base Modal -->
<div class="modal fade" id="modal-item-container" tabindex="-1" role="dialog" aria-labelledby="item-title" aria-hidden="true">
  <div class="modal-dialog">
  	<!-- contents -->
  	<div class="modal-content"></div>
  	<!-- comments -->
  	<div id='modal-comments-container' class='modal-box' style="display:none;"></div>
  	<!-- comment form -->
  	<div class="modal-box">
  		<div class="modal-body">
  			<form role="form" method="POST" action="ajax/gallery_comment_iu.php" enctype='multipart/form-data'>
  				<h6>Add a Comment</h6>
					<textarea class="form-control" name="ds_comment" rows="4"></textarea>
					<div class='form-group padding-bottom-10 padding-top-10'>
						<button id='modal-comment-submit' class='btn btn-sm btn-primary pull-right'>Send</button>
					</div>
  			</form>
  		</div>
  	</div>
  </div>
</div>

<!-- Modal for old campus -->
<div class="modal fade" id="modal-empty-container" tabindex="-1" role="dialog" aria-labelledby="item-title" aria-hidden="true">
  <div class="modal-dialog">
  	<div class="modal-content"></div>
  </div>
</div>

<script type="text/javascript">
	// Variables
	var student, disabledWeeks, disabledTabs, user;
	student = <?php echo json_encode($fl_alumno); ?>;
	student_otro = <?php echo json_encode($fg_otro_alumno); ?>;
	disabledWeeks = <?php echo json_encode(GetDisabledWeeks($weeks_setting)); ?>;
	disabledTabs = <?php echo json_encode(GetDisabledTabs($weeks_setting, $fg_otro_alumno)); ?>;  
  user = <?php HeaderProfile($fl_alumno); ?>;

  $("#header_student").append(user.profile.ds_ruta_foto);
 
	// Desktop tab names
	var desktopTabs, defaultTabs;
	desktopTabs = <?php echo json_encode(GetDesktopTabs($fl_alumno, $weeks_setting)); ?>;
	defaultTabs = <?php echo json_encode(GetDefaultTabs($nb_tab, $weeks_setting, $fg_otro_alumno)); ?>;

	// User profile
	var profileContainer, student;
	profileContainer = $("#user-profile-container");
	studentProfile = <?php GetStudentProfile($fl_alumno); ?>;

	// User desktop
	var desktopContainer;
	desktopContainer = $("#desktop-container");

	// Modal variables
	var modalItemContainer, modalCommentsContainer, commentButtonSubmit, modalEmptyContainer;
	modalItemContainer = $("#modal-item-container");
	modalCommentsContainer = $("#modal-comments-container");
	commentButtonSubmit = $("#modal-comment-submit");
	modalEmptyContainer = $("#modal-empty-container");

	// Load jquery form, required for modal
	loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/jquery-form/jquery.form.min.js");

	$(document).ready(function(){
		// Set the student being viewed
		desktopController.setStudent(student);

		// Setup user profile
		desktopController.setupProfile(profileContainer, studentProfile.profile);

		// Setup desktop view and layout
		desktopController.setupTabs(desktopContainer, {names: desktopTabs.programs, type: "program", displayContent: false});
		desktopController.setupTabs(desktopContainer, {names: desktopTabs.weeks, type: "week", displayContent: false});
		desktopController.setupTabs(desktopContainer, {names: desktopTabs.tabs, type: "tab", displayContent: true});

		// Disable week tabs in advance
		var weekList = $("ul[data-type='week'] li");
		desktopController.disableTabs(weekList, disabledWeeks);

		// Set active tabs
		desktopController.setActiveTab(desktopContainer, "program", defaultTabs.program);
		desktopController.setActiveTab(desktopContainer, "week", defaultTabs.week);
		desktopController.setActiveTab(desktopContainer, "tab", defaultTabs.tab);

		// Initial request for desktop content
		desktopController.requestTabContent(desktopContainer);

		// Lecture and brief tabs
		var lectureTab = $("ul[data-type='tab'] li:nth-child(1)"),
				briefTab = $("ul[data-type='tab'] li:nth-child(2)"),
				assigmentTab = $("ul[data-type='tab'] li:nth-child(3)");
    
    // validamos que si es otro alumno no puede ver los videos
    if(student_otro == true){
       desktopController.disableTab(lectureTab, "disabled");
       desktopController.disableTab(briefTab, "disabled");
	   //desktopController.disableTab(assigmentTab, "disabled hidden");  
    }

		// When a user clicks on a tab
		desktopContainer.on("click", "ul > li:not(.disabled)", function(){
			var type, index;
			type = $(this).parent().data("type");
			index = $(this).children().data("index");

			// check to disable lecture and brief tabs
			if(type === "week"){
				if(disabledTabs[index] === false){
					// move tabs out of video or brief if needed, remove inner contents as well
					if(lectureTab.hasClass("active")){
						desktopController.removeActiveTab(lectureTab, "tab", lectureTab.find("a").data("index"));
						desktopController.emptyTab();
						desktopController.setActiveTab(desktopContainer, "tab", defaultTabs.tab);
					} 
					else if(briefTab.hasClass("active")){
						desktopController.removeActiveTab(briefTab, "tab", briefTab.find("a").data("index"));
						desktopController.emptyTab();
						desktopController.setActiveTab(desktopContainer, "tab", defaultTabs.tab);
					}
          // disable lecture and brief tabs
          // los students podran ver los videos anteriores ala clase actual
          //desktopController.disableTab(lectureTab, "disabled");
          //desktopController.disableTab(briefTab, "disabled");

				} else {
					// enable lecture and brief tabs
					desktopController.enableTab(lectureTab, "disabled");
					desktopController.enableTab(briefTab, "disabled");
        }
			}

			// request for new content if user clicks on different tabs
			if(desktopController.tabHasChanged(type, index)){
				// empty out current tab before updating to new tab variables
				desktopController.emptyTab();
				desktopController.setActiveVariable(type, index);
				desktopController.requestTabContent(desktopContainer);
			}
		});

		// Prevent disabled tabs from being clicked
		desktopContainer.on("click", "ul > li.disabled", function(){
			return false;
		});

		// Select on an item post uploaded on new campus
		desktopContainer.on("click", "a[data-target='#modal-item-container']", function(){
			var post = $(this).data("selected-post");

			// Set selected post value
			boardController.setSelectedPost(post);
		});

		// Select on an item post uploaded on old campus, to be deleted in the future
		desktopContainer.on("click", "a[data-target='#modal-empty-container']", function(){
			// Handles old uploads manually here
			var src, type, modalContent, content, modalBody;
			src = $(this).data("src");
			type = $(this).data("type");

			if(type === "video"){
				modalBody = 
					"<video class='center-block' width='720' height='405' controls='controls'>"+
						"<source src='"+src+"' type='video/ogg'>"+
					"</video>";
			} else {
				modalBody = 
					"<img class='img-responsive center-block' src='"+src+"'>";
			}

			modalContent = modalEmptyContainer.find(".modal-content");

			content = 
				"<div class='modal-header'>"+
					"<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>"+
					"<h4 class='modal-title'></h4>"+
				"</div>"+
				"<div class='modal-body'>"+
					modalBody+
				"</div>"+
				"<div class='modal-footer'>"+
					"<h6>This assignment was uploaded from the old campus, so commenting is not available.</h6><h6>Please remember to upload assignments on the new campus in the future.</h6>"+
				"</div>";

			modalContent.append(content);
		});

		// Load comments after the modal is ready
		modalItemContainer.on('loaded.bs.modal', function (e) {
		  // Request for comments related to this modal post
		  boardController.requestComments(modalCommentsContainer);
		});

		// Empty out the modal content everytime the modal is closed
		$('body').on('hidden.bs.modal', '.modal', function () {
			$(this).removeData('bs.modal').find('.modal-content').empty();
			modalCommentsContainer.empty().toggle(false);
		});

		// Inside Post Modal 
		//-------------------

		// Submitting a comment
		commentButtonSubmit.on('click', function(){
			boardController.submitComment(modalItemContainer);
			return false;
		});
	});
</script>