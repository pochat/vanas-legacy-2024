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

  # Receive parameters
  $open_week = RecibeParametroNumerico('week', true);
  // $open_week = 1;
  $fl_programa = RecibeParametroNumerico('fl_programa', true);
  # Si no hay programa obtiene el ultimo al que supio un archivo
  if(empty($fl_programa)){
    $Query  = "SELECT MAX(b.fl_programa_sp), c.fg_status_pro ";
    $Query .= "FROM k_entrega_semanal_sp a ";
    $Query .= "LEFT JOIN c_leccion_sp b ON (b.fl_leccion_sp=a.fl_leccion_sp) ";
    $Query .= "LEFT JOIN k_usuario_programa c ON(c.fl_programa_sp=b.fl_programa_sp) ";
    $Query .= "WHERE a.fl_alumno=$fl_usuario ORDER BY fl_entrega_semanal_sp DESC ";
    $row = RecuperaValor($Query);
    $fl_programa = $row[0];
    $fg_status_pro = $row[1];
  }
  else{
    $row = RecuperaValor("SELECT fg_status_pro FROM k_usuario_programa WHERE fl_programa_sp=$fl_programa AND fl_usuario=$fl_usuario");
    $fg_status_pro = $row[0];
  }
  
  $max_week = ObtenSemanaMaximaAlumno($fl_programa);
  $current_week = ObtenSessionActualCourse($fl_usuario);
  $label = ObtenEtiqueta(760);
  
  # Obtenemos la informacion del usuario
  $ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario);  
  $ds_ruta_foto = ObtenFotoUsuario($fl_usuario); 
  $ds_nombre_user = ObtenNombreUsuario($fl_usuario);
  $nb_programa = ObtenNombreCourse($fl_programa);
  $current_session = ObtenSessionActualCourse($fl_usuario);
  
  function GetWeeksStatus($current_week, $max_week, $fl_programa, $fl_alumno){
    # Arrys
		$result["status"] = array();
		$result["size"] = array();		
    
    # Sessiones
		for($week=1; $week<=$max_week; $week++){
			// Leave weeks in advance blank
			if($week > $current_week){
				$result["status"] += array("$week" => array('status' => '', 'graded' => '', 'color' => 'red'));
				continue;
			}

			$fl_leccion = ObtenFolioSemanaAlumno($week, $fl_programa);
			$Query  = "SELECT fg_entregado, fl_promedio_semana FROM k_entrega_semanal_sp WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion";
  		$row = RecuperaValor($Query);
  		$fg_entregado = $row[0];
  		$fl_promedio_semana = $row[1];

  		$Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
      $Query .= "FROM c_leccion_sp ";
      $Query .= "WHERE fl_programa_sp=$fl_programa ";
      $Query .= "AND no_semana=$week ";
			$row = RecuperaValor($Query);
			$fg_animacion = $row[0];
			$fg_ref_animacion = $row[1];
			$no_sketch = $row[2];
			$fg_ref_sketch = $row[3];

			// Status of the week
			$status = "";
			$graded = "";

			// Check if the week's assignment is complete or incomplete
			if(!empty($fg_entregado)){
        $color = "success";
				$status = "white";
			} else {
				$status = "Incomplete";
        $color = "yellow";
			}

			// Check if the week's assignment has been graded
			if(!empty($fl_promedio_semana)){
				$graded = true;
			} else {
				$graded = false;
			}

			// Check if the week requires assignments
			$fg_require = $fg_animacion || $fg_ref_animacion || $no_sketch || $fg_ref_sketch;
			if(!$fg_require){
				$status = "Not Required";
			}
			$result["status"] += array("$week" => array('fg_entregado' => $fg_entregado, 'fl_semana' => $fl_semana, 'fl_grupo' => $fl_grupo, 'status' => $status, 'graded' => $graded, 'color' => $color));
		}
		$result["size"] += array("total" => $week-1);

		echo json_encode((Object) $result);
  }
  
  function GetAssignRequirements($fl_programa){
    # Arrays
  	$upload["size"] = array();
  	$upload["requirements"] = array();
  	$upload["instructions"] = array();
  	
		$Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch, ";
    $Query .= "ds_animacion, ds_ref_animacion, ds_no_sketch, ds_ref_sketch ";
    $Query .= "FROM c_leccion_sp ";
    $Query .= "WHERE fl_programa_sp= $fl_programa ";
    $Query .= "ORDER BY no_semana ";
		$rs = EjecutaQuery($Query);

		for($i=1; $row=RecuperaRegistro($rs); $i++){
			$fg_animacion = $row[0];
			$fg_ref_animacion = $row[1];
			$no_sketch = $row[2];
			$fg_ref_sketch = $row[3];
      $ds_animacion = str_uso_normal($row[4]);
      $ds_ref_animacion = str_uso_normal($row[5]);
      $ds_no_sketch = str_uso_normal($row[6]);
      $ds_ref_sketch = str_uso_normal($row[7]);

			$requires = array();
      $instructions = array();
			if(!empty($fg_animacion)){
				$requires += array('A' => $fg_animacion);
        $instructions += array('A' => $ds_animacion);
			}
			if(!empty($fg_ref_animacion)){
				$requires += array('AR' => $fg_ref_animacion);
				$instructions += array('AR' => $ds_ref_animacion);
			}
			if(!empty($no_sketch)){
				$requires += array('S' => $no_sketch);
				$instructions += array('S' => $ds_no_sketch);
			}
			if(!empty($fg_ref_sketch)){
				$requires += array('SR' => $fg_ref_sketch);
				$instructions += array('SR' => $ds_ref_sketch);
			}
			$upload["requirements"] += array("week".$i => (Object)$requires);
      $upload["instructions"] += array("instructions".$i => (Object)$instructions);
		}
		$upload["size"] += array("total_weeks" => $i-1);
		
		echo json_encode((Object) $upload);
	}

  
  function GetUploadedThumbnails($fl_alumno, $max_week, $fl_programa){
		$result["thumbnails"] = array();
		$result["size"] = array();
    $fl_instituto = ObtenInstituto($fl_alumno);

		for($week=1; $week<=$max_week; $week++){
	  	$fl_leccion_sp = ObtenFolioSemanaAlumno($week, $fl_programa);
			$Query  = "	SELECT fl_entrega_semanal_sp FROM k_entrega_semanal_sp WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp";
  		$row = RecuperaValor($Query);
  		$fl_entrega_semanal_sp = $row[0];

			$Query  = "SELECT fl_entregable_sp, fg_tipo, ds_ruta_entregable, ds_comentario ";
			$Query .= "FROM k_entregable_sp ";
			$Query .= "WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp ";
			$Query .= "ORDER BY no_orden ";
			$rs = EjecutaQuery($Query);

			$assignment = array();
			$assignment_comment = array();
			$assignment_key = array();
			$tot_assignment = 1;

			$assignment_ref = array();
			$assignment_ref_comment = array();
			$assignment_ref_key = array();
			$tot_assignment_ref = 1;

			$sketch = array();
			$sketch_comment = array();
			$sketch_key = array();
			$tot_sketch = 1;

			$sketch_ref = array();
			$sketch_ref_comment = array();
			$sketch_ref_key = array();
			$tot_sketch_ref = 1;

			for($i=0; $row=RecuperaRegistro($rs); $i++){
				$fl_entregable = $row[0];
				$fg_tipo = $row[1];
				$ds_ruta_entregable = $row[2];
				$ds_comentario = $row[3];

				$ext = strtolower(ObtenExtensionArchivo($ds_ruta_entregable));
				if($ext == 'ogg'){					
					$ruta = PATH_N_COM_IMAGES."/desktop-upload-video-default.jpg";
				} else {
					$ruta = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_alumno."/sketches/thumbs/$ds_ruta_entregable";
				}

				if(empty($ds_comentario)){
					$ds_comentario = "No additional comments for this file";
				}

				if($fg_tipo == "A"){
					$assignment += array("$tot_assignment" => $ruta);
					$assignment_comment += array("$tot_assignment" => $ds_comentario); 
					$assignment_key += array("$tot_assignment" => $fl_entregable);
					$tot_assignment++;
				} else if ($fg_tipo == "AR"){
					$assignment_ref += array("$tot_assignment_ref" => $ruta);
					$assignment_ref_comment += array("$tot_assignment_ref" => $ds_comentario); 
					$assignment_ref_key += array("$tot_assignment_ref" => $fl_entregable);
					$tot_assignment_ref++;
				} else if ($fg_tipo == "S"){
					$sketch += array("$tot_sketch" => $ruta);
					$sketch_comment += array("$tot_sketch" => $ds_comentario); 
					$sketch_key += array("$tot_sketch" => $fl_entregable);
					$tot_sketch++;
				} else if ($fg_tipo == "SR"){
					$sketch_ref += array("$tot_sketch_ref" => $ruta);
					$sketch_ref_comment += array("$tot_sketch_ref" => $ds_comentario); 
					$sketch_ref_key += array("$tot_sketch_ref" => $fl_entregable);
					$tot_sketch_ref++;
				}
			}
			// Put the list of thumbnails into its fg_tipo
			$size = array(
				"total_A" => $tot_assignment-1,
				"total_AR" => $tot_assignment_ref-1,
				"total_S" => $tot_sketch-1,
				"total_SR" => $tot_sketch_ref-1,
				"total_thumbnails" => $i
			);

			$types = array(
				"A" => array("images" => (Object) $assignment, "comments" => (Object) $assignment_comment, "keys" => (Object) $assignment_key),
				"AR" => array("images" => (Object) $assignment_ref, "comments" => (Object) $assignment_ref_comment, "keys" => (Object) $assignment_ref_key),
				"S" => array("images" => (Object) $sketch, "comments" => (Object) $sketch_comment, "keys" => (Object) $sketch_key),
				"SR" => array("images" => (Object) $sketch_ref, "comments" => (Object) $sketch_ref_comment, "keys" => (Object) $sketch_ref_key),
				"size" => (Object) $size
			);

			$result["thumbnails"] += array("week".$week => (Object) $types);
		}
		$result["size"] += array("total_weeks" => $week-1);

		echo json_encode((Object) $result);
	}
  
  /* Modal para notificar al students que su curso esta pausado*/
  MuestraModal("pause_course", true);  
?>

<script type="text/javascript">  
	// Initialize an accordion list for dropzones
	var container, accordion, max_week, weeksStatus, status, graded, menssage, ruta_images,ruta_video;
	max_week = <?php echo json_encode($max_week); ?>;
	weeksStatus = <?php GetWeeksStatus($current_week, $max_week, $fl_programa, $fl_usuario); ?>;  
  menssage = <?php echo json_encode($label); ?>;
  
  /** Encabezado Info del programa **/
	accordion0 = 
  "<div class='row margin-bottom-10'>"+
    "<div class='col-sm-12 col-md-12 col-lg-12'>"+
      "<div id='myCarousel' class='carousel fade profile-carousel'>"+
        "<div class='carousel-inner'>"+
          "<div id='header_student' class='item active'>"+
            "<div class='col-sm-5 padding-5' style='position: absolute;'>"+
              "<div class='carousel-inner profile-pic' style='background-color:rgba(255, 255, 255, 0.82);'>"+
                "<div id='user-profile-container' class='profile-container no-margin'>"+
                "<img class='avatar' src='<?php echo $ds_ruta_avatar; ?>'>"+
                "<div class='info'>"+
                  "<div class='username no-margin'>Student: <?php echo $ds_nombre_user;?></div>"+
                  "<div class='text no-margin'>Course: <?php echo $nb_programa; ?></div>"+
                  "<div class='text no-margin'>You are in: Session <?php echo $current_session; ?></div>"+
                "</div>"+
                "</div>"+                
              "</div>"+
            "</div>"+
            "<img src='<?php echo $ds_ruta_foto; ?>' width='100%'></div>"+
        "</div>"+
      "</div>"+
    "</div>"+
  "</div>";
  accordion = "";
  /** Encabezado para la tabs de las sessiones **/
	for(var i=1; i<=max_week; i++){
		status = weeksStatus.status[i].status;
		graded = weeksStatus.status[i].graded ? "Graded, "+menssage+" " : "Not Graded";
    color = weeksStatus.status[i].color;
    
		accordion +=
			"<div class='panel panel-default'>"+
				"<div class='panel-heading'>"+
					"<h6 class='panel-title txt-color-"+color+"'>"+
						"<a data-toggle='collapse' data-parent='#accordion' href='#week-"+i+"' class='collapsed'> <i class='fa fa-lg fa-angle-down pull-right'></i> <i class='fa fa-lg fa-angle-up pull-right'></i> "+
							"Week "+i+"&nbsp[ "+status+" | "+graded+" ]&nbsp"+ 
						"</a>"+
					"</h6>"+
				"</div>"+
				"<div id='week-"+i+"' class='panel-collapse collapse'>"+
					"<div id='week-"+i+"-content' class='panel-body padding-10'></div>"+
				"</div>"+
			"</div>";
	}
	container = 
		"<div class='row'>"+
			"<div class='col-xs-12 no-padding padding-5'>"+
				"<div class='well well-light no-padding'>"+
					"<div class='panel-group smart-accordion-default' id='accordion'>"+accordion0 + accordion+"</div>"+
				"</div>"+
			"</div>"+
		"</div>";
	$("#content").append(container);
  
  /** Activamos **/
	$(document).ready(function(){
    /** Verifica si el usuario no tiene pausado el programa**/
    user_pause(<?php echo $fg_status_pro.",".$fl_programa.",".$fl_usuario; ?>);
		var current_week, max_week, current_tab, open_week, week;
		current_week = <?php echo json_encode($current_week); ?>;
		max_week = <?php echo json_encode($max_week); ?>;

		// Set current week to blue
		current_tab = $("a[href='#week-"+current_week+"']");
		current_tab.append(" (Current Week)");
		current_tab.addClass("bg-colour-blue txt-colour-white");

		// By default open the current week or open the selected week tab
		open_week = <?php echo json_encode($open_week); ?>;
		if(open_week === '0'){ open_week = current_week; }
		$("#week-"+open_week).toggleClass("in");

		// Disable week tabs in advance
		week = parseInt(current_week);
		for (var i=week+2; i<=max_week; i++){
			var tab = $("a[href=#week-"+i+"]");
			tab.each(function(){
				tab.css("cursor", "not-allowed");
				tab.css("color", "#999");
				tab.css("background-color", "#F8F8F8");
			});
			tab.on('click', function(){
				return false;
			});
		}    
	});
  
  
  // Initialize dropzone
	loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/dropzone/dropzone.min.js", initDropzone);
	function initDropzone(){
		var max_week, tipo, requirements, requires, thumbnails, thumbs, weeksStatus, graded, instructions;   

		max_week = <?php echo json_encode($max_week); ?>;
		tipo = {	
			"A" : "<?php echo ObtenEtiqueta(827); ?>", //Assignment
			"AR" : "<?php echo ObtenEtiqueta(828); ?>", // Assignment Reference
			"S" : "<?php echo ObtenEtiqueta(829); ?>", // Sketch
			"SR" : "<?php echo ObtenEtiqueta(830); ?>" // Sketch Reference
		};
		
    requirements = <?php GetAssignRequirements($fl_programa); ?>;
		requires = requirements['requirements'];
		instructions = requirements['instructions'];
		thumbnails = <?php GetUploadedThumbnails($fl_usuario, $max_week, $fl_programa); ?>;
		thumbs = thumbnails.thumbnails;
		weeksStatus = <?php GetWeeksStatus($current_week, $max_week, $fl_programa, $fl_usuario); ?>;
    
		// Disable auto discover on form tags
		Dropzone.autoDiscover = false;
		// For each week tab, setup a drop zone
		for(var i=1; i<=max_week; i++){
			graded = weeksStatus.status[i].graded;			
			for(var name in tipo){
				var content, acceptedFileTypes, images, thumbContainer, placeholder;
				content = $("#week-"+i+"-content");
				// If an assignment is required        
				if(requires["week"+i][name] > 0){					
					// Basic dropzone layout, if grade has been assigned do not display dropzone
					if(!graded){
            // Placeholder comment
            placeholder = "<?php echo ObtenEtiqueta(2410);?>";

            // Identificamos que los sketchs reqieren mas de uno
            var multiple = "";
            if(tipo[name]=="<?php echo ObtenEtiqueta(829); ?>" && requires["week"+i][name]>1)
              multiple = "<?php echo ObtenEtiqueta(826); ?>";
            
						content.append(             
              "<div class='panel panel-default'>"+
							  "<div class='panel-heading bg-color-white'>"+
							  	"<h1 class='semi-bold'>&nbsp&nbsp"+tipo[name]+" (You have uploaded <span id='num-thumbs-"+i+"-"+name+"' class='text-success'>"+thumbs["week"+i]["size"]["total_"+name]+"</span> / "+requires["week"+i][name]+" required)&nbsp;<strong>"+multiple+"</strong></h1>"+
							  	"<div id='thumb-container-"+i+"-"+name+"'></div>"+                  
						  	"</div>"+                
							  "<div class='panel-body row'>"+                  
                  "<div class='col-xs-12 col-md-8 row'>"+
                    "<form id='upload-zone-"+i+"-"+name+"' role='form' action='site/upload_fame.php' method='post' class='dropzone' >"+
                      "<input type='hidden' name='tipo' value='"+name+"'>"+
                      "<input type='hidden' name='semana' value='"+i+"'>"+
                      "<input type='hidden' name='archivo' id='archivo-"+i+"-"+name+"'>"+
                      "<input type='hidden' name='comentarios' id='comentarios-"+i+"-"+name+"' value=''>"+
                      "<input type='hidden' name='fl_programa' id='fl_programa-"+i+"-"+name+"' value='<?php echo $fl_programa; ?>'>"+
                    "</form>"+
                  "</div>"+ 
                  "<div class='col-xs-12 col-md-4'> "+
                    "<div class='panel panel-default padding-10' style='min-height: 200px;'>"+
                      "<div class='panel-heading'><h6 class='panel-title'><?php echo ObtenEtiqueta(1125); ?></h6></div> "+
                      "<div class='panel-body'>"+instructions["instructions"+i][name]+"</div>"+
                    "</div>"+
                  "</div>"+
							  	"<div class='well padding-5 no-margin col-sm-12'>"+
						  			"<label>&nbspAdditional Comments Per File</label>"+
						  			"<textarea id='comments-"+i+"-"+name+"' class='form-control' rows='2' placeholder='"+placeholder+"'></textarea><br />"+
                    "<div class='col-sm-12 no-padding' id='div_facebook-"+i+"-"+name+"'>"+
                    "<input type='hidden' id='facebook-"+i+"-"+name+"' name='facebook-"+i+"-"+name+"'>"+
                    "</div>"+
						  		"</div>"+
							  "</div>"+
							  "<div class='col col-sm-12 panel-footer bg-color-white'>"+
							  	"<div class='col col-sm-1'><button id='upload-week-"+i+"-"+name+"' class='btn btn-default'><i class='fa fa-arrow-circle-o-up'></i><span><?php echo ObtenEtiqueta(2409);?></span></button></div>"+              
						  	"</div>"+
							"</div>");                                
					} 
          else {
						content.append(
							"<div class='panel panel-default'>"+
							  "<div class='panel-heading bg-color-white'>"+
							  	"<h6>&nbsp&nbsp"+tipo[name]+" (You have uploaded <span id='num-thumbs-"+i+"-"+name+"' class='text-success'>"+thumbs["week"+i]["size"]["total_"+name]+"</span> / "+requires["week"+i][name]+" required)</h6>"+
							  	"<div id='thumb-container-"+i+"-"+name+"'></div>"+
						  	"</div>"+
							"</div>"
						);            
					}

					// Variable setup and uploaded thumbnail setup
					thumbContainer = $("#thumb-container-"+i+"-"+name);
					images = "";
					if(name == 'S'){
						acceptedFileTypes = ".jpeg, .jpg";
						if(Object.keys(thumbs["week"+i][name]["images"]).length > 0){
							for(var num in thumbs["week"+i][name]["images"]){
								images += 
									"<div class='preview-container'>"+
										"<div class='preview-thumbnail'>"+
											"<img class='fill-block' src='"+thumbs["week"+i][name]["images"][num]+"'>"+
										"</div>"+
										"<div class='btn-group btn-group-justified' role='group'>"+
								      "<a tabindex='0' class='btn btn-default' role='button' data-container='body' data-toggle='popover' data-placement='bottom' data-trigger='focus' title='Comments to the Instructor' data-content='"+thumbs["week"+i][name]["comments"][num]+"'>Comments</a>"+
								    "</div>"+
										"<span class='delete-me' onclick='DeleteMe("+thumbs["week"+i][name]["keys"][num]+", this.parentNode);' title='Delete me!'><i class='fa fa-times'></i></span>"+
									"</div>";
							}
							thumbContainer.addClass('uploaded-thumbnails');
							thumbContainer.append(images);
						}
					} 
          else {
						acceptedFileTypes = ".jpeg, .jpg, .mov, .mp4";
						if(Object.keys(thumbs["week"+i][name]["images"]).length > 0){
							images += 
								"<div class='preview-container'>"+
									"<div class='preview-thumbnail'>"+
										"<img class='fill-block' src='"+thumbs["week"+i][name]["images"]["1"]+"'>"+
									"</div>"+
									"<div class='btn-group btn-group-justified' role='group'>"+
							      "<a tabindex='0' class='btn btn-default' role='button' data-container='body' data-toggle='popover' data-placement='bottom' data-trigger='focus' title='Comments to the Instructor' data-content='"+thumbs["week"+i][name]["comments"]["1"]+"'>Comments</a>"+
							    "</div>"+
									"<span class='delete-me' onclick='DeleteMe("+thumbs["week"+i][name]["keys"]["1"]+", this.parentNode);' title='Delete me!'><i class='fa fa-times'></i></span>"+
								"</div>";
							thumbContainer.addClass('uploaded-thumbnails');
							thumbContainer.append(images);              
						}            
					}

					// Initiate each dropzone
					$("#upload-zone-"+i+"-"+name).dropzone({
						url: "site/upload_fame.php",
						parallelUploads: 1,
						paramName: 'qqfile',
						autoProcessQueue: false,
						addRemoveLinks : true,
						acceptedFiles: acceptedFileTypes,
						maxFiles: 20,
						dictDefaultMessage: "",
						dictResponseError: 'Error uploading file!',
						dictRemoveFile: "Remove",
						init: function(){
							var dropzone = this;
							// setup the upload buttons for each dropzone
							$("#upload-week-"+i+"-"+name).on("click", function(){
								dropzone.processQueue();
							});
						},
						sending: function(file){
							var dropzone, id,checkface;
							dropzone = this;
							id = dropzone.element.id.replace("upload-zone", "");
							$("#archivo"+id).val(file.name);
              /*Tomamos el valor del campo de texto para enviarlos y guardarlos*/
              $("#comentarios"+id).val($("#comments"+id).val());
						},
						success: function(file, result){
							var dropzone, id, type, message, feedback, thumbContainer, numThumbs, currentNum;
							message = JSON.parse(result);
              
							// remove the uploaded file
							dropzone = this;
							dropzone.removeFile(file);

							id = dropzone.element.id.replace("upload-zone", "");
							type = id.replace(/-\d+-/, "");
							feedback = $("#uploaded-feedback"+id);
							feedback.text("");
              
							// Adds feedback to the user, checks the return values by falsey check
							if(message.error){
								feedback.attr("class", "h5 text-danger");
								feedback.text(message.error);
							} else {
								$("#comments"+id).val("");
								feedback.attr("class", "h5 text-primary");
								feedback.text(message.success);
								// Add thumbnail to list
								thumbContainer = $("#thumb-container"+id);
								numThumbs = $("#num-thumbs"+id);
                
                // Check if thumbnail row has been setup
								if(!thumbContainer.hasClass('uploaded-thumbnails')){
									thumbContainer.addClass('uploaded-thumbnails');
								}
								if(type == 'S'){
									// Plus one more to count
									currentNum = parseInt(numThumbs.text());
									numThumbs.text(currentNum+1);
								} else {
									numThumbs.text(1);
									thumbContainer.empty();
								}
								thumbContainer.append(
									"<div class='preview-container'>"+
										"<div class='preview-thumbnail'>"+
											"<img class='fill-block' src='"+message.thumbnail+"'>"+
										"</div>"+
										"<div class='btn-group btn-group-justified' role='group'>"+
								      "<a tabindex='0' class='btn btn-default' role='button' data-container='body' data-toggle='popover' data-placement='bottom' data-trigger='focus' title='Comments to the Instructor' data-content='"+message.comment+"'>Comments</a>"+
								    "</div>"+
										"<span class='delete-me' onclick='DeleteMe("+message.key+", this.parentNode);' title='Delete me!'><i class='fa fa-times'></i></span>"+                    
									"</div>"
								);                
								// Send to the board
								socket.emit('new-gallery-post', message.post);               
              }
						}
					});
				}
        else {
					// Else assignment is not required
					content.append("<h5 style='border:1px solid #E3E3E3'>&nbsp No "+tipo[name]+" Required</h5>");
				}       
			}      
		}
		// Initialize popover
		$(document).ready(function(){
			$('[data-toggle="popover"]').popover();
		});
	}
	// end dropzone  

  // Delete me
	function DeleteMe(entregable, target){
		var parent, week, id, numThumbs, currentNum, fl_programa;
		parent = target.parentNode;
		id = parent.id.replace("thumb-container", "");
		week = Math.abs(parseInt(id));
  fl_programa = '<?php echo $fl_programa; ?>';
    
		$.ajax({	
			type: 	'POST',
			url : 	'site/upload_fame_del.php',
			data: 	'entregable='+entregable+
							'&semana='+week+
              '&fl_programa='+fl_programa
		}).done(function(result){
			var message, feedback;
			message = JSON.parse(result);
			feedback = $("#uploaded-feedback"+id);
			feedback.text("");

			// Adds feedback to the user, checks the return values by falsey check
			if(message.error){
				feedback.attr("class", "h5 text-danger");
				feedback.text(message.error);
			} else {
				// remove the image div
				parent.removeChild(target);

				// subtract one from thumb counter
				numThumbs = $('#num-thumbs'+id);
				currentNum = parseInt(numThumbs.text());
				numThumbs.text(currentNum-1);

				feedback.attr("class", "h5 text-success");
				feedback.text(message.success);
			}
		});
	}

</script>