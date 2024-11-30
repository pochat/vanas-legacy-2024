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

  # Receive parameters
  $open_week = RecibeParametroNumerico('week', true);

  $max_week = ObtenSemanaMaximaAlumno($fl_alumno);
  $current_week = ObtenSemanaActualAlumno($fl_alumno);
  $label = ObtenEtiqueta(760);

  function GetWeeksStatus($fl_alumno, $current_week, $max_week){
		$result["status"] = array();
		$result["size"] = array();
		$fl_grupo = ObtenGrupoAlumno($fl_alumno);
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);

		for($week=1; $week<=$max_week; $week++){
			// Leave weeks in advance blank
			if($week > $current_week){
				$result["status"] += array("$week" => array('status' => '', 'graded' => ''));
				continue;
			}

			$fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $week);
			$Query  = "SELECT fg_entregado, fl_promedio_semana FROM k_entrega_semanal WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana";
  		$row = RecuperaValor($Query);
  		$fg_entregado = $row[0];
  		$fl_promedio_semana = $row[1];

  		$Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
			$Query .= "FROM c_leccion ";
			$Query .= "WHERE fl_programa=$fl_programa ";
			$Query .= "AND no_grado=$no_grado ";
			$Query .= "AND no_semana=$week";
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
				$status = "Complete";
			} else {
				$status = "Incomplete";
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
			$result["status"] += array("$week" => array('fg_entregado' => $fg_entregado, 'fl_semana' => $fl_semana, 'fl_grupo' => $fl_grupo, 'status' => $status, 'graded' => $graded));
  		//$result["status"] += array("$week" => array('status' => $status, 'graded' => $graded));
		}
		$result["size"] += array("total" => $week-1);

		echo json_encode((Object) $result);
  }

	function GetAssignRequirements($fl_alumno){
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);

  	$upload["size"] = array();
  	$upload["requirements"] = array();

		$Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
		$Query .= "FROM c_leccion ";
		$Query .= "WHERE fl_programa=$fl_programa ";
		$Query .= "AND no_grado=$no_grado ";
		$Query .= "ORDER BY no_semana";
		$rs = EjecutaQuery($Query);

		for($i=1; $row=RecuperaRegistro($rs); $i++){
			$fg_animacion = $row[0];
			$fg_ref_animacion = $row[1];
			$no_sketch = $row[2];
			$fg_ref_sketch = $row[3];

			$requires = array();
			if(!empty($fg_animacion)){
				$requires += array('A' => $fg_animacion);
			}
			if(!empty($fg_ref_animacion)){
				$requires += array('AR' => $fg_ref_animacion);
			}
			if(!empty($no_sketch)){
				$requires += array('S' => $no_sketch);
			}
			if(!empty($fg_ref_sketch)){
				$requires += array('SR' => $fg_ref_sketch);
			}
			$upload["requirements"] += array("week".$i => (Object)$requires);
		}
		$upload["size"] += array("total_weeks" => $i-1);

		echo json_encode((Object) $upload);
	}

	function GetUploadedThumbnails($fl_alumno, $max_week){
		$result["thumbnails"] = array();
		$result["size"] = array();
		$fl_grupo = ObtenGrupoAlumno($fl_alumno);

		for($week=1; $week<=$max_week; $week++){
	  	$fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $week);
			$Query  = "SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana";
  		$row = RecuperaValor($Query);
  		$fl_entrega_semanal = $row[0];

			$Query  = "SELECT fl_entregable, fg_tipo, ds_ruta_entregable, ds_comentario ";
			$Query .= "FROM k_entregable ";
			$Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal ";
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
				if($ext == 'ogg'||$ext == 'mp4') {
                $ruta = PATH_N_COM_IMAGES."/desktop-upload-video-default.jpg";
				} else {
					$ruta = PATH_ALU."/sketches/thumbs/$ds_ruta_entregable";
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
  function face_pages($fl_alumno){
    $result["size"] = array();
    $Query = "SELECT fl_facebook, ds_name, type FROM k_use_facebook WHERE fl_usuario=$fl_alumno ORDER BY type";
    $rs = EjecutaQuery($Query);
    $total = CuentaRegistros($rs);
    for($i=0;$row=RecuperaRegistro($rs);$i++){
      $result["pages".$i] = array(
        'fl_facebook' => $row[0],
        'ds_name' => $row[1],
        'tipo' => $row[2]
      );
    }
    $result["size"] += array("totalpages" => $total);

    echo json_encode((Object) $result);
  }

?>
<script type="text/javascript">  
	// Initialize an accordion list for dropzones
	var container, accordion, max_week, weeksStatus, status, graded, menssage, ruta_images,ruta_video, facebook_status;
	max_week = <?php echo json_encode($max_week); ?>;
	weeksStatus = <?php GetWeeksStatus($fl_alumno, $current_week, $max_week); ?>;
  menssage = <?php echo json_encode($label); ?>;
  facebook_status = $("#facebookst").val(); 
  
	accordion = "";
	for(var i=1; i<=max_week; i++){
		status = weeksStatus.status[i].status;
		graded = weeksStatus.status[i].graded ? "Graded, "+menssage+" " : "Not Graded";    
    
		accordion +=
			"<div class='panel panel-default'>"+
				"<div class='panel-heading'>"+
					"<h6 class='panel-title'>"+
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
			"<div class='col-xs-12'>"+
				"<div class='well well-light'>"+
					"<div class='panel-group smart-accordion-default' id='accordion'>"+accordion+"</div>"+
				"</div>"+
			"</div>"+
		"</div>";
	$("#content").append(container);

	// Configure week tabs
	$(document).ready(function(){
		var current_week, max_week, current_tab, open_week, week;
		current_week = <?php echo json_encode($current_week); ?>;
		max_week = <?php echo json_encode($max_week); ?>;
    // Verifica a nivel sistema si se muestra la conexion de redes sociales
    social_networks = <?php echo SOCIAL_NETWORKS; ?>;    

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
		var max_week, tipo, requirements, requires, thumbnails, thumbs, weeksStatus, graded,face_form;   

		max_week = <?php echo json_encode($max_week); ?>;
		tipo = {	
			"A" : "<?php echo ObtenEtiqueta(827); ?>", //Assignment
			"AR" : "<?php echo ObtenEtiqueta(828); ?>", // Assignment Reference
			"S" : "<?php echo ObtenEtiqueta(829); ?>", // Sketch
			"SR" : "<?php echo ObtenEtiqueta(830); ?>" // Sketch Reference
		};
		requirements = <?php GetAssignRequirements($fl_alumno); ?>;
		requires = requirements['requirements'];
		thumbnails = <?php GetUploadedThumbnails($fl_alumno, $max_week); ?>;
		thumbs = thumbnails.thumbnails;
		weeksStatus = <?php GetWeeksStatus($fl_alumno, $current_week, $max_week); ?>;
    var fpages = <?php face_pages($fl_alumno); ?>;
    var pages_total  = fpages.size.totalpages;
    var check_pages = "";
    // Agregamos el perfil y las paginas del usuario          
    for(var i=0; i<pages_total; i++){
      var pages = fpages["pages"+i];
      var idface = pages.fl_facebook;
      var nameface = pages.ds_name;
      var typeface = pages.tipo;
      // Dividimos el perfil y sus paginas
      if(typeface == "PF")        
        check_pages += "<div class='col-md-3'><input type='checkbox' id='"+idface+"#' name='"+idface+"'></input><label for='"+idface+"#'>"+nameface+"<strong>&nbsp;(<?php echo ObtenEtiqueta(840); ?>)</strong></label></div>";
      else
        check_pages += "<div class='col-md-3'><input type='checkbox' id='"+idface+"#' name='"+idface+"'></input><label for='"+idface+"#'>"+nameface+"<strong>&nbsp;(<?php echo ObtenEtiqueta(841); ?>)</strong></label></div>";
    }

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
            placeholder = "Comments here are only seen by the instructor. You can also include additional links. (Dropbox, YouTube, iCloud, Google Drive) ";
            // Validamos que este activo por default el checkbox de face si esta conectado
            if(social_networks==1){              
              face_form = "<input type='hidden' id='fbhidden-"+i+"-"+name+"' name='fbhidden'>"+
              "<input type='hidden' id='comentshare-"+i+"-"+name+"' name='comentshare'>";
            }
            else{
              face_form = "";
            }            
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
							  "<div class='panel-body no-padding'>"+
							  	"<form id='upload-zone-"+i+"-"+name+"' role='form' action='upload.php' method='post' class='dropzone'>"+
							  		"<input type='hidden' name='tipo' value='"+name+"'>"+
							  		"<input type='hidden' name='semana' value='"+i+"'>"+
							  		"<input type='hidden' name='archivo' id='archivo-"+i+"-"+name+"'>"+
							  		"<input type='hidden' name='comentarios' id='comentarios-"+i+"-"+name+"' value=''>"+face_form+
							  	"</form>"+
							  	"<div class='well padding-5 no-margin col-sm-12'>"+
						  			"<label>&nbspAdditional Comments Per File</label>"+
						  			"<textarea id='comments-"+i+"-"+name+"' class='form-control' rows='2' placeholder='"+placeholder+"'></textarea><br />"+
                    "<div class='col-sm-12 no-padding' id='div_facebook-"+i+"-"+name+"'>"+
                    "<input type='hidden' id='facebook-"+i+"-"+name+"' name='facebook-"+i+"-"+name+"'>"+
                    /*Muestra Primero el checkbox*/
                      "<div class='btn-group col-sm-12'>"+
                        "<div class='col-xs-12 col-md-12 col-sm-12 h3 alert-heading no-padding no-margin'><?php echo ObtenEtiqueta(843); ?></div>"+
                        "<div class='col-xs-12 col-md-12 col-sm-12'>"+                      
                          "<div class='col-xs-12 col-md-12 col-sm-12 checkbox checkbox-primary'><input type='checkbox' name='face-"+i+"-"+name+"' id='face-"+i+"-"+name+"'><label for='face-"+i+"-"+name+"'><strong><?php echo ObtenEtiqueta(788); ?></strong></label></div>"+          
                        "</div>"+                                                  
                      "</div>"+
                      /*Div para mostra el textarea y la lista de paginas*/
                      "<div class='btn-group col-sm-12 margin-left-5' id='pages-"+i+"-"+name+"'>"+
                          /*Muestra Segundo TextArea*/
                          "<div class='col-xs-5 col-md-5 col-sm-5 demo-icon-font smart-form' style='padding-left:30px;'>"+
                            "<label class='textarea textarea-resizable' ><?php echo ObtenEtiqueta(842); ?><br>"+
                            "<textarea  id='fface-"+i+"-"+name+"' name='fface-"+i+"-"+name+"' class='custom-scroll' placeholder='<?php echo ObtenEtiqueta(790); ?>'></textarea></label>"+
                          "</div>"+
                          /*Pages Facebook*/
                          "<div id='pagesface-"+i+"-"+name+"' class='col-xs-12 col-md-12 col-sm-12 checkbox checkbox-primary' style='padding-left:30px;'><br/>"+                          
                          "</div>"+
                      "</div>"+                    
                    "</div>"+
						  		"</div>"+
							  "</div>"+
							  "<div class='col col-sm-12 panel-footer bg-color-white'>"+
							  	"<div class='col col-sm-1'><button id='upload-week-"+i+"-"+name+"' class='btn btn-default'><i class='fa fa-arrow-circle-o-up'></i><span>Upload</span></button></div>"+              
						  	"</div>"+
							"</div>");           
            // CONTROL DEL FACEBOOK CHECKBOX Y PAGINAS            
            $(document).ready(function(){
              // Ocultamos a no mostramos el div para postear en facebook
              if(social_networks==1)
                $('#div_facebook-'+i+'-'+name).css('display','inline');
              else
                $('#div_facebook-'+i+'-'+name).css('display','none');

              // Colocamos las diferentes paginas en el div              
              // Agregamos el nombre de cada seccion y elnumero de semana
              $("#pagesface-"+i+"-"+name).append(check_pages.split('#').join("-"+i+"-"+name));
              
              // Si esta conectado el usuario debera de estar activo el checkbox y comentarios de facebook 
              // En caso contrario nos estarn activo ninguno de los dos
              if(facebook_status == "connected"){
                $('#face-'+i+'-'+name).prop( "checked", true );          
                $('#pages-'+i+'-'+name).css( "display", "inline" );
                $("#pages-"+i+"-"+name).css("display","inline");
              }else{
                $('#face-'+i+'-'+name).prop( "checked", false );
                $('#pages-'+i+'-'+name).css( "display", "none");
                $("#pages-"+i+"-"+name).css("display","none");              
              }
              
              // Acciones del click checkbox 
              // False no mostrar los comentarios y no podra postear en facebook
              // True mostrar los comentarios y automaticamente publicara en face
              // Validamos si esta conectado tendra activado el checkbox               
              $('input[name=face-'+i+'-'+name+']').click(function(){
                var coment_id = $(this).attr('id');
                var coment_id2 = $(this).attr('id').substring(5);
                if($(this).is(':checked')){
                  if(facebook_status=="connected")
                    $("#pages-"+coment_id2).css("display","inline");
                  else{
                    if(facebook_status != "connected")
                      loginn('upload.php');
                    $("#pages-"+coment_id2).css("display","none");
                  }
                }
                else{
                  $("#pages-"+coment_id2).css("display","none");
                }
              });            
            });                                  
					} else {
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
					} else {
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
						url: "ajax/upload.php",
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
            
              /*// Validamos que el nombre del archivo no contenga la palabra script
              this.on("addedfile", function(file) {
                var filename = file.name; 
                var existe = strripos(filename,'script');
                if(existe>=0)
                  alert("change word 'script' file name to save"); 
              });*/
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
              /*Tomamos los Valores del checkbox de face y textarea*/
              /*Para despues enviarlos*/
							$("#comentarios"+id).val($("#comments"+id).val());
              if($("#face"+id).is(':checked'))
                checkface = 1;
              else
                checkface = 0;
							$("#fbhidden"+id).val(checkface);
              $("#comentshare"+id).val($("#fface"+id).val());
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
                // Posteamos en las paginas del usuario o el su perfil
                if(message.fbhidden == '1'){             
                  // url para postear en facebok
                  var linkfb = document.location.protocol +"//"+ message.link_share+"/public/preview.php?k="+message.key;                
                  if(message.ext == 'mov' || message.ext == 'mp4'){
                    picturefb = document.location.protocol +"//"+ message.link_share+"/modules/common/new_campus/images/desktop-upload-video-default.jpg";
                  }
                  else{                                   
                    picturefb = document.location.protocol +"//"+ message.link_share+"/modules/students/sketches/"+message.nb_archivo+"";                     
                  }
                  // Verificamos en que paginas o perfil desea publicar el usuario
                  for(var i=0; i<pages_total; i++){
                    var pages = fpages["pages"+i];
                    var page_id = pages.fl_facebook;
                    var typeface = pages.tipo;
                    if($('#'+page_id+id).is(":checked")){
                      // Publica en las paginas seleccionadas
                      if(typeface == "PG")
                        postear(page_id, message.comentshare, linkfb, picturefb, message.name, message.caption, message.ds_description, message.key);                        
                      else //publica en el perfil
                        postear_perfil('me', message.comentshare, linkfb, picturefb, message.name, message.caption, message.ds_description, message.key);
                    }                    
                  }                 
                  
                  /*Borramos el comentario y los checkbox de las paginas una vez que se postea*/
                  $("#fface"+id).val("");
                }
                
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
				}	else {
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
		var parent, week, id, numThumbs, currentNum;
		parent = target.parentNode;
		id = parent.id.replace("thumb-container", "");
		week = Math.abs(parseInt(id));

		$.ajax({	
			type: 	'POST',
			url : 	'ajax/desktop_upload_del.php',
			data: 	'entregable='+entregable+
							'&semana='+week
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