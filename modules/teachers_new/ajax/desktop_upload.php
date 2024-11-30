<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recibe parametros
  $fl_alumno = RecibeParametroNumerico('student', True);
  if(empty($fl_alumno))
    $fl_alumno = $fl_usuario;

  # Queries for a program's weeks (Note: weeks can vary)
	function GetNumWeek($fl_alumno){
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);

		# Find the class the student is in
		$Query  = "SELECT fl_class FROM c_class WHERE fl_programa=$fl_programa AND no_grado=$no_grado ";
		$row = RecuperaValor($Query);
		$fl_class = $row[0];

		$Query = "SELECT COUNT(1) FROM c_leccion WHERE fl_programa=$fl_programa AND fl_class=$fl_class AND no_grado=$no_grado";
		$row = RecuperaValor($Query);
		$tot_week = $row[0];

		echo json_encode($tot_week);
	}
	
	function GetAssignRequirements($fl_alumno){
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);
  	
  	# Find the class the student is in
		$Query  = "SELECT fl_class FROM c_class WHERE fl_programa=$fl_programa AND no_grado=$no_grado ";
		$row = RecuperaValor($Query);
		$fl_class = $row[0];

  	$upload["size"] = array();
  	$upload["data"] = array();
  	$upload["debug"] = array("fl_class" => $fl_class);
  	
		$Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
		$Query .= "FROM c_leccion ";
		$Query .= "WHERE fl_programa=$fl_programa ";
		$Query .= "AND fl_class=$fl_class ";
		$Query .= "AND no_grado=$no_grado ";
		$Query .= "ORDER BY no_semana";
		$rs = EjecutaQuery($Query);
		for($i=0; $row = RecuperaRegistro($rs); $i++){
			$fg_animacion = $row[0];
			$fg_ref_animacion = $row[1];
			$no_sketch = $row[2];
			$fg_ref_sketch = $row[3];

			$upload["data"] += array(
				"A".$i => $fg_animacion,
				"AR".$i => $fg_ref_animacion,
				"S".$i => $no_sketch,
				"SR".$i => $fg_ref_sketch
			);
		}
		$upload["size"] += array("total" => $i);
		
		echo json_encode((Object) $upload);
	}
  
  # Verifcamos el puerto
  # Direccionamos del http al https
  if ($_SERVER["SERVER_PORT"] == 443) {
    $url_node = "https://campus.vanas.ca:3000";
  }
  else{
    $url_node = "http://campus.vanas.ca:3000";
  }
?>

<div class="row">	
	<div class="col-xs-12">
		<div id="upload-container" class="well">
			<h3 class="page-title">
				Drag and Drop or Click on the Striped Zone to Upload Assignments 
			</h3>
		</div>
	</div>
</div>

<script type="text/javascript">
	var week = <?php GetNumWeek($fl_alumno); ?>;
	var actual_week = <?php GetActualWeek($fl_alumno); ?>;
	// initialize an accordion list for dropzones
	var accordion = "";
	for(var i=1; i<=week; i++){
		accordion +=
			"<div class='panel panel-default'>"+
				"<div class='panel-heading'>"+
					"<h6 class='panel-title'><a data-toggle='collapse' data-parent='#accordion' href='#week-"+i+"' class='collapsed'> <i class='fa fa-lg fa-angle-down pull-right'></i> <i class='fa fa-lg fa-angle-up pull-right'></i> Week "+i+"</a></h6>"+
				"</div>"+
				"<div id='week-"+i+"' class='panel-collapse collapse'>"+
					"<div id='week-"+i+"-content' class='panel-body padding-10'>"+
					"</div>"+
				"</div>"+
			"</div>";
	}
	$("#upload-container").append("<div class='panel-group smart-accordion-default' id='accordion'>"+accordion+"</div>");
	// open the week that the student is on
	$("a[href='#week-"+actual_week+"']").toggleClass("collapsed");
	$("#week-"+actual_week).toggleClass("in");
	// add current week message


	// initialize dropzone
	loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/dropzone/dropzone.min.js", initDropzone);
	// sets up the number of dropzones based on the number of weeks
	function initDropzone(){
		// Initialize a connection
		// var socket = io.connect("http://campus.vanas.ca:3000");
		var socket = io.connect("<?php echo $url_node; ?>");

		var week = <?php GetNumWeek($fl_alumno); ?>;
		Dropzone.autoDiscover = false;
		var tipo = 
			{	
				"A" : "Assignment",
				"AR" : "Assignment Reference",
				"S" : "Sketch",
				"SR" : "Sketch Reference"
			};
		var requirements = <?php GetAssignRequirements($fl_alumno); ?>;

		for(var i=0; i<week; i++){
			for(var k in tipo){
				if(requirements.data[k+i] != 0){
					$("#week-"+(i+1)+"-content").append(
						"<div class='panel panel-default'>" +
						  "<div class='panel-heading'><h6>&nbsp&nbsp"+tipo[k]+" ("+requirements.data[k+i]+" "+tipo[k].toLowerCase()+" required)</h6></div>" +
						  "<div class='panel-body no-padding'><form role='form' action='upload.php' method='post' class='dropzone' id='upload-zone-"+i+"-"+k+"'></form></div>" +
						  "<div class='panel-footer'><button id='upload-week-"+i+"-"+k+"' class='btn btn-default'><i class='fa fa-arrow-circle-o-up'></i><span>Upload</span></button></div>" +
						"</div>"
					);
					$("#upload-zone-"+i+"-"+k).append("<input type='hidden' name='tipo' value='"+k+"'><input type='hidden' name='semana' value='"+(i+1)+"'><input type='hidden' name='archivo' id='archivo-"+i+"-"+k+"'>");
					$("#upload-zone-"+i+"-"+k).append("<div class='well padding-5'><label>Add Comments to the Instructor</label><textarea id='comentarios-"+i+"-"+k+"' class='form-control' name='comentarios' rows='2'></textarea></div>");

					$("#upload-zone-"+i+"-"+k).dropzone({
						url: "ajax/upload.php",
						parallelUploads: 1,
						paramName: 'qqfile',
						autoProcessQueue: false,
						addRemoveLinks : true,
						//ignoreHiddenFiles: false, 				// dropzone by default will also upload hidden inputs in a form
						acceptedFiles: ".jpeg, .jpg, .mov",
						maxFiles: requirements.data[k+i],
						//maxFilesize: 1.0,
						//thumbnailWidth: 80,
						//thumbnailHeight: 80,
						dictDefaultMessage: "",
						dictResponseError: 'Error uploading file!',
						dictRemoveFile: "Remove",
						init: function(){
							var dropzone = this;
							// setup the upload buttons for each dropzone
							$("#upload-week-"+i+"-"+k).on("click", function(){
								dropzone.processQueue();
							});
						},
						sending: function(file){
							var dropzone = this;
							var id = dropzone.element.id.replace("upload-zone", "");
							$("#archivo"+id).val(file.name);
							//console.log("my id:"+id);
							//console.log("file: "+ file.name);
						},
						success: function(file, result){
							// remove the uploaded file
							var dropzone = this;
							dropzone.removeFile(file);
							// remove the comment text
							var id = dropzone.element.id.replace("upload-zone", "");
							$("#comentarios"+id).val("");

							//var content = JSON.parse(result);
							//socket.emit('new-gallery-post', content.data.fl_gallery_post);
							
							socket.emit('new-gallery-post', result);

							//console.log(content.data.fg_tipo + "\n" + content.data.no_semana + "\n" + content.data.ds_comentario + "\n" + content.data.nb_archivo + "\n" + content.data.ext);
							//console.log("\n" + content.data.ruta + "\n" + content.data.fl_alumno);
						}
					});
				} else {
					$("#week-"+(i+1)+"-content").append("<h5>No "+tipo[k]+" Required</h5>");
				}
			}
		}
		// changed css for testing (remember to delete/restyle later)
		$(".panel-heading").css("background-color", "#FFF");
		$(".panel-footer").css("background-color", "#FFF");
	}
	// end dropzone

</script>
