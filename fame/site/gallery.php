<?php 
	# Libreria de funciones
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Obtenemos el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # !!!!! IMPORTANT Variable Initialization to avoid errors, this variable don't exist
  $classmate = NULL;
  
  # Receive parameters
  $fl_gallery_comment = RecibeParametroNumerico('post', True);
  $fl_gallery_post = 0;
  if(!empty($fl_gallery_comment)){
    $row = RecuperaValor("SELECT fl_gallery_post_sp FROM k_gallery_comment_sp WHERE fl_gallery_comment_sp=$fl_gallery_comment");
    $fl_gallery_post = $row[0];
  }
  
  # Actualiza contador de visualizaciones del tema
  EjecutaQuery("UPDATE k_f_usu_tema SET no_posts=0 WHERE fl_usuario=$fl_usuario");

  function GetStreamTopics($fl_usuario){
  	
    # Get the sufix for the languaje
    $sufix = langSufix();
  	// $rs = EjecutaQuery("SELECT fl_tema, nb_tema FROM c_f_tema WHERE fg_tipo='F'");
    # Obtiene el perfil
    $fl_perfil = ObtenPerfilUsuario($fl_usuario);
    if($fl_perfil == PFL_ESTUDIANTE_SELF){
      # Obtenemos los programas que esta cursando
      $Query  = "SELECT  a.fl_programa_sp, b.nb_programa".$sufix;
      $Query .= " FROM k_usuario_programa a ";
      $Query .= "LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) ";
      $Query .= "WHERE fl_usuario_sp=".$fl_usuario." ";
    }
    else{
      if($fl_perfil==PFL_MAESTRO_SELF){
        $Query  = "SELECT  a.fl_programa_sp, b.nb_programa".$sufix;
        $Query .= " FROM k_usuario_programa a ";
        $Query .= "LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) ";
        $Query .= "WHERE fl_maestro=".$fl_usuario." GROUP BY fl_programa_sp ";
      }
      else{
        $Query = "SELECT  fl_programa_sp, nb_programa".$sufix." FROM c_programa_sp ";
      }
    }
    $rs = EjecutaQuery($Query);
  	$result = array();
  	for($i=0; $row=RecuperaRegistro($rs); $i++){
  		$fl_programa_sp = $row[0];
  		$nb_programa = $row[1];

  		$result[$i] = array(
  			"fl_programa_sp" => $fl_programa_sp,
  			"nb_programa" => $nb_programa
  		);
  	}
  	$result["size"] = array("total" => $i);
  	echo json_encode((Object) $result);
  }

  function GetStreamPrograms($fl_usuario){

  	# Get the sufix for the languaje
    $sufix = langSufix();

  	// $rs = EjecutaQuery("SELECT fl_tema, nb_tema FROM c_f_tema WHERE fg_tipo='P'");
    # Obtiene el perfil
    $fl_perfil = ObtenPerfilUsuario($fl_usuario);
    if($fl_perfil == PFL_ESTUDIANTE_SELF){
      # Obtenemos los programas que esta cursando
      $Query  = "SELECT  a.fl_programa_sp, b.nb_programa".$sufix;
      $Query .= " FROM k_usuario_programa a ";
      $Query .= "LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) ";
      $Query .= "WHERE fl_usuario_sp=".$fl_usuario." ";
    }
    else{
      if($fl_perfil==PFL_MAESTRO_SELF){
        $Query  = "SELECT  a.fl_programa_sp, b.nb_programa".$sufix;
        $Query .= " FROM k_usuario_programa a ";
        $Query .= "LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) ";
        $Query .= "WHERE fl_maestro=".$fl_usuario." GROUP BY fl_programa_sp ";
      }
      else{
        $Query = "SELECT  fl_programa_sp, nb_programa".$sufix." FROM c_programa_sp ";
      }
    }
    
    $rs = EjecutaQuery($Query);

  	$result = array();
  	for($i=0; $row=RecuperaRegistro($rs); $i++){
  		$fl_programa_sp = $row[0];
  		$nb_programa = $row[1];

  		$result[$i] = array(
  			"fl_tema" => $fl_programa_sp,
  			"name" => $nb_programa
  		);
  	}
  	$result["size"] = array("total" => $i);
  	echo json_encode((Object) $result);
  }
  
?>

<!-- Board tool bar -->
<div id="board-header">
	<div class="pull-left">
		<a class="btn btn-primary" data-toggle="collapse" href="#post-container"> <?php echo ObtenEtiqueta(1939); ?> <span class="caret"></span></a>
	</div>
	<!--<div class="pull-left ml-5">
		<a id="button-topics" class="btn btn-primary" data-toggle="dropdown" href="javascript:void(0);"> Topics <span class="caret"></span></a>
		<ul id="dropdown-topics" class="dropdown-menu"></ul>
	</div>-->
	<div class="pull-left ml-5">
		<a id="button-programs" class="btn btn-primary" data-toggle="dropdown" href="javascript:void(0);"> <?php echo ObtenEtiqueta(1940); ?> <span class="caret"></span></a>
		<ul id="dropdown-programs" class="dropdown-menu"></ul>
	</div>
	<!--<div class="pull-left ml-5">
		<a id="button-myclass" class="btn btn-primary" href="javascript:void(0);"> My Class</a>
	</div>-->
	<div class="pull-left ml-5">
		<a id="button-myposts" class="btn btn-primary" href="javascript:void(0);"> <?php echo ObtenEtiqueta(1941); ?></a>
	</div>
	<div class="pull-left ml-5">
		<a id="button-reset" class="btn btn-primary" href="javascript:void(0);"> <?php echo ObtenEtiqueta(1942); ?></a>
	</div>
</div>

<!-- Post upload container for 'Share' button -->
<div id="post-container" class="collapse">
	<div class="well well-light no-margin padding-10">
		<!-- error field -->
		<div class="row">
			<div class="col-xs-12">
				<h5 id="post-error-field" class="text-danger no-margin"></h5>
			</div>
		</div>
		<!-- upload form -->
		<div class="row">
			<div class="col-xs-12">
				<form id="post-form" role="form" method="POST" action="site/gallery_post_iu.php" enctype='multipart/form-data'>
					<h6><?php echo ObtenEtiqueta(1838); ?> </h6>
					<input type="text" class="form-control" name="ds_title" placeholder="">
					<h6><?php echo ObtenEtiqueta(1839); ?> </h6>
					<textarea class="form-control" name="ds_post" rows="5"></textarea>
					<h6><?php echo ObtenEtiqueta(1840); ?></h6>
					<div class='input-group' id="input_file">
			      <span class='input-group-btn'>
			        <span class='btn btn-default btn-file'>
			          <i class="fa fa-upload" id="icon_file"></i>
			          <input type='file' name='qqfile'>
			        </span>
			      </span>
			      <input type='text' class='form-control' style='z-index:0;' onfocus='this.blur()' readonly>
					</div>
				</form>
			</div>
		</div>
		<!-- upload button -->
		<div class="row padding-top-10">
			<div class="col-xs-12">
				<button id="post-submit" class="btn btn-default btn-group-justified lead"><?php echo ObtenEtiqueta(1841); ?></button>
			</div>
		</div>
	</div>
</div>

<!-- Main container -->
<div id="art-container"></div>



<script type="text/javascript">
	// Initialize page variables
	var topics, programs, classmate, boardPost; 
	topics = <?php GetStreamTopics($fl_usuario); ?>;
	programs = <?php GetStreamPrograms($fl_usuario); ?>;
	classmate = <?php echo json_encode((int) $classmate); ?>;
	boardPost = <?php echo json_encode((int) $fl_gallery_post); ?>;

	// Html container, for turning on/off window scroll
	var htmlContainer;
	htmlContainer = $("html");

	// Filter buttons
	var buttonPrograms, dropdownPrograms;
	// buttonTopics = $("#button-topics");
	// dropdownTopics = $("#dropdown-topics");
	buttonPrograms = $("#button-programs");
	dropdownPrograms = $("#dropdown-programs");

	var buttonMyClass, buttonMyPosts, buttonReset;
	buttonMyClass = $("#button-myclass");
	buttonMyPosts = $("#button-myposts");
	buttonReset = $("#button-reset");
	
	// Main container
	var container;
	container = $("#art-container");

	// Post variables
	var postContainer, postFormContainer, postButtonSubmit;
	postContainer = $("#post-container");
	postFormContainer = $("#post-form");
	postButtonSubmit = $("#post-submit");

	// Modal variables
	var modalItemContainer, modalCommentsContainer, commentButtonSubmit;
	modalItemContainer = $("#modal-item-container");
	modalCommentsContainer = $("#modal-comments-container");
	commentButtonSubmit = $("#modal-comment-submit-header");

	// Load jquery form for image uploads, required by board.inc.js
	loadScript("<?php echo PATH_SELF_JS; ?>/plugin/jquery-form/jquery.form.min.js");

	$(document).ready(function(){
		// Set classmate flag
		boardController.setClassmate(classmate);

		// Setup the radio buttons for the upload form (share button)
		// boardController.setupUploadForm(postFormContainer, topics);

		// Setup filter buttons
		// boardController.setupFilterDropdown(dropdownTopics, topics, "Topics");
		boardController.setupFilterDropdown(dropdownPrograms, programs, "Programs");

		// If there is a default boardPost, open up the modal
		if(boardPost){
			boardController.setSelectedPost(boardPost);
			modalItemContainer.on("show.bs.modal", function(){        
				$(this).find(".modal-content").load("site/gallery_post_modal.php", "item="+boardPost);
				// Disable window scroll
			  htmlContainer.css('overflow-y', 'hidden');

			  // Request for comments related to this modal post
			  boardController.requestComments(modalCommentsContainer);
			});

			modalItemContainer.modal("show");
			modalItemContainer.off("show.bs.modal");
		}

		// Initialize the board
		//container.packery({ columnWidth: 320, itemSelector: '.item', gutter: 10	});
		boardController.requestItemsboard(container);

		// Selecting a new program
		dropdownPrograms.on("click", "li", function(){
			var text, selectedFilter;
			text = $(this).text();
			selectedFilter = $(this).children().data("theme") || 0;

			// Empty container and the settings
			boardController.emptyContainer(container);

			// Reset other buttons
			// buttonTopics.text("Topics").append(" <span class='caret'></span>");
			// dropdownTopics.find("li.active").toggleClass("active");
			dropdownPrograms.find("li.active").toggleClass("active");

			// Set new name for the button
			buttonPrograms.text(text).append(" <span class='caret'></span>");
			// Set the selected filter active
			$(this).toggleClass("active");

			// Present the board with the new selected filter
			boardController.setSelectedFilter(selectedFilter);
			boardController.requestItemsboard(container);
		});

		// Click on my class button
		buttonMyClass.on("click", function(){
			// Reset board settings and empty out container
			boardController.emptyContainer(container);

			// Reset other buttons
			// buttonTopics.text("Topics").append(" <span class='caret'></span>");
			buttonPrograms.text("Programs").append(" <span class='caret'></span>");
			// dropdownTopics.find("li.active").toggleClass("active");
			dropdownPrograms.find("li.active").toggleClass("active");

			// Present the board with the new selected filter
			boardController.setClassmate('on');
			boardController.requestItemsboard(container);
		});

		// Click on my posts button
		buttonMyPosts.on("click", function(){
			boardController.emptyContainer(container);

			// Reset other buttons
			buttonPrograms.text("Programs").append(" <span class='caret'></span>");
			// dropdownTopics.find("li.active").toggleClass("active");
			dropdownPrograms.find("li.active").toggleClass("active");

			// Present the board with the new selected filter
			boardController.setMyPosts('on');
			boardController.requestItemsboard(container);
		});

		// Click on reset button
		buttonReset.on("click", function(){
			boardController.emptyContainer(container);

			// Reset other buttons
			buttonPrograms.text("Programs").append(" <span class='caret'></span>");
			// dropdownTopics.find("li.active").toggleClass("active");
			dropdownPrograms.find("li.active").toggleClass("active");

			boardController.requestItemsboard(container);
		});

		// Select on an item post
		container.on("click", "a[data-toggle='modal']", function(){
			var post = $(this).data("selected-post");

			// Set selected post value
			boardController.setSelectedPost(post);
		});

		// Submiting a new post
		postButtonSubmit.on("click", function(){
			boardController.submitPost(postContainer);
			return false;
		});

		// Delete an item post
		container.on('click', '.item > .delete-me', function(event){
			var post = $(this).data('selected-post');
			boardController.deletePost(container, event, post);
			return false;
		});

		// Inside Post Modal 
		//-------------------

		// Submitting a comment
//		commentButtonSubmit.on('click', function(){
//      alert('gabriel');
//			boardController.submitComment(modalItemContainer);
//			return false;
//		});

		// Load comments after the modal is ready
		modalItemContainer.on('loaded.bs.modal', function (e) {
		  // Disable window scroll
		  htmlContainer.css('overflow-y', 'hidden');

		  // Request for comments related to this modal post
		  boardController.requestComments(modalCommentsContainer);
		});
		
		// When leaving the page with the modal view on, take out the grayed out backdrop
		modalCommentsContainer.on("click", "a[href*='profile_view.php'], a[href*='desktop.php']", function(){
			// Enable window scroll
			htmlContainer.css('overflow-y', 'auto');
			$("div.modal-backdrop").remove();
			$(window).off('scroll.infinite');
			boardController.emptyContainer(container);
		});
	
		// Stop infinite scrolling -- (covers 99% of the cases), may need rework
		$("a[href*='node.php'], a[href*='messages.php'], a[href*='profile.php'], a[href*='profile_view.php'], a[href*='blog.php'], a[href*='tuition_payment.php'], a[href*='desktop.php']").on("click", function(){
			$(window).off('scroll.infinite');
			boardController.emptyContainer(container);
		});

		// Check if scroll bar has reached bottom of window
		$(window).on('scroll.infinite', function (){
			if($(window).scrollTop() == $(document).height() - $(window).height()) {
		  	boardController.requestItemsboard(container);
		  }
		});
		
		// Empty out the modal content everytime the modal is closed
		$('body').on('hidden.bs.modal', '.modal', function () {
			// Enable window scroll
			htmlContainer.css('overflow-y', 'auto');

			$(this).removeData('bs.modal').find('.modal-content').empty();
			modalCommentsContainer.empty().toggle(false);
		});

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
	});
</script>