<?php
  # gallery_post_modal is the modal part of gallery.php
  # is called when a user clicks on an item in the activity board

	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $fl_gallery_post = RecibeParametroNumerico('item', True);

  $diferencia = RecuperaDiferenciaGMT( );
  $Query  = "SELECT a.fl_tema, nb_tema, a.fl_usuario, c.fl_perfil, fl_entregable, ds_title, ds_post, DATE_FORMAT(DATE_ADD(fe_post, INTERVAL $diferencia HOUR), '%M %e, %Y') fe_post, nb_archivo ";
  $Query .= "FROM k_gallery_post a ";
  $Query .= "LEFT JOIN c_f_tema b ON b.fl_tema=a.fl_tema ";
  $Query .= "LEFT JOIN c_usuario c ON c.fl_usuario=a.fl_usuario ";
  $Query .= "WHERE fl_gallery_post=$fl_gallery_post";
  $row = RecuperaValor($Query);
  $fl_tema = $row[0];
  $nb_tema = $row[1];
  $fl_post_usuario = $row[2];
  $fl_post_perfil = $row[3];
  $fl_entregable = $row[4];
  $ds_title = str_uso_normal($row[5]);
  $ds_post = str_uso_normal($row[6]);
  $fe_post = $row[7];
  $nb_file = $row[8];

  # Initialize default modal settings
	$type = "Board";
	$fg_tipo = "";
	$no_semana = "";
  $no_grado = "";
  $ds_pais = "";

  # Find country of the author
  if($fl_post_perfil == PFL_ESTUDIANTE){
    $Query  = "SELECT ds_pais ";
    $Query .= "FROM c_usuario a ";
    $Query .= "LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
    $Query .= "LEFT JOIN c_pais c ON c.fl_pais=b.ds_add_country ";
    $Query .= "WHERE fl_usuario=$fl_post_usuario ";
  } else {
    $Query  = "SELECT ds_pais ";
    $Query .= "FROM c_maestro a ";
    $Query .= "LEFT JOIN c_pais b ON b.fl_pais=a.fl_pais ";
    $Query .= "WHERE fl_maestro=$fl_post_usuario ";
  }
  $row = RecuperaValor($Query);
  $ds_pais = $row[0];

  if(empty($fl_entregable)){
    $nb_file = "<img class='img-responsive img-center' src='".PATH_N_COM_UPLOAD."/gallery/$nb_file'>";
  } else {
  	$type = "Desktop";

    # Retrieve term of the post
    $no_grado = ObtenGradoAlumno($fl_post_usuario);

  	# Retrieve desktop post info
		$Query  = "SELECT a.fg_tipo, d.no_semana ";
		$Query .= "FROM k_entregable a ";
		$Query .= "LEFT JOIN k_entrega_semanal b ON b.fl_entrega_semanal=a.fl_entrega_semanal ";
		$Query .= "LEFT JOIN k_semana c ON c.fl_semana=b.fl_semana ";
		$Query .= "LEFT JOIN c_leccion d ON d.fl_leccion=c.fl_leccion ";
		$Query .= "WHERE a.fl_entregable=$fl_entregable ";
		$row2 = RecuperaValor($Query);
		$fg_tipo = $row2[0];
		$no_semana = $row2[1];

		switch($fg_tipo) {
			case "A":		$fg_tipo = "Assignment"; $nb_tab = "assignment"; break;
	    case "AR":	$fg_tipo = "Assignment Reference"; $nb_tab = "assignment_ref"; break;
	    case "S":   $fg_tipo = "Sketch"; $nb_tab = "sketch"; break;
	    case "SR":	$fg_tipo = "Sketch Reference"; $nb_tab = "sketch_ref"; break;
		}

    $ext = strtolower(ObtenExtensionArchivo($nb_file));
    if($ext == 'jpg'){
      $nb_file = "<img class='img-responsive img-center' src='".PATH_ALU."/sketches/original/$nb_file'>";
    } else {
      # present video here
      $ruta = PATH_ALU."/videos/";
      $nb_file = PresentVideoHTML5($ruta, $nb_file, 865, 405, 'assign_video');
    }
  }

  # Replace embedded videos with responsive sizing
  # Refer to common/lib/cam_forum.inc.php, SeparaFrames() for the <p> and <iframe > tag
  if(strpos($ds_post, "iframe") !== false){
    $ds_post = str_replace("<p>", "<div class='embed-container'>", $ds_post);
    $ds_post = str_replace("</p>", "</div>", $ds_post);
    $nb_file = '';
  }

  $nb_usuario = ObtenNombreUsuario($fl_post_usuario);

  $result = array(
  	"type" => $type,
		"fg_tipo" => $fg_tipo,
    "nb_tab" => $nb_tab,
		"no_semana" => $no_semana,
    "no_grado" => $no_grado,
    "ds_pais" => $ds_pais,
  	"fl_gallery_post" => $fl_gallery_post,
  	"nb_tema" => $nb_tema,
  	"fl_post_usuario" => $fl_post_usuario,
  	"nb_usuario" => $nb_usuario,
  	"ds_title" => $ds_title,
  	"ds_post" => $ds_post,
  	"fe_post" => $fe_post,
  	"nb_archivo" => $nb_file
  );
?>

<!-- Modal Content -->
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="item-title" style="font-weight:500;"></h4>
</div>
<div class="modal-body text-align-center" id="item-body"></div>
<div class="modal-footer" id="item-footer"></div>

<script type="text/javascript">
  var result, image, description, detail, name, type, title, body, footer;
	result = <?php echo json_encode((Object) $result); ?>;

  title = $("#item-title");
  body = $("#item-body");
  footer = $("#item-footer");

	title.append(result.ds_title);

	image = 
    "<div class='row'>"+
      "<div class='col-xs-12'>"+
        result.nb_archivo+
      "</div>"+
    "</div>";
	body.append($(image));

	description = 
    "<div class='row'>"+
      "<div class='col-xs-12'>"+
        "<div class='h4' style='white-space:pre-line;'>"+result.ds_post+"</div>"+
      "</div>"+
    "</div>";
	body.append(description);

	// Determine type of post
	if(result.type === 'Desktop'){
    name = "<span class='h5 text-primary'><a href='#ajax/desktop.php?student="+result.fl_post_usuario+"&week="+result.no_semana+"&tab="+result.nb_tab+"'><b>"+result.nb_usuario+"</b></a></span><br>";
		type = "<span class='text-primary'>"+result.type+"<br> Week "+result.no_semana+" - "+result.fg_tipo+" - Term "+result.no_grado+"</span><br>";
	} else {
    name = "<span class='h5 text-primary'><b>"+result.nb_usuario+"</b></span><br>";
		type = "<span class='text-primary'>"+result.type+"</span><br>";
	}

	detail = 
		name+
    "<span class='text-primary'>"+result.ds_pais+"</span><br>"+
		"<span class='h4'>"+result.nb_tema+"</span><br>"+
		type+
		"<span class='text-muted'>"+result.fe_post+"</span>";

	footer.append(detail);

  // When leaving the page with the modal view on, take out the grayed out backdrop
  $("a[href*='profile_view.php'], a[href*='desktop.php']").on("click", function(){
    // Don't need to redirect on desktop.php again
    if(/desktop.php/i.test(window.location.hash)){ return false; }
    var htmlContainer = $("html");
    // Enable window scroll
    htmlContainer.css('overflow-y', 'auto');
    $("div.modal-backdrop").remove();
    $(window).off('scroll.infinite');
    boardController.emptyContainer(container);
  });
</script>