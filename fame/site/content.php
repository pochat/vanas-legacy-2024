<?php 
	# Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

	# Settings for flowplayer
	$ds_matricula = ObtenMatriculaAlumno(!empty($fl_alumno)?$fl_alumno:NULL);
	$playerSetting["rtmp"] = "rtmp://".ObtenConfiguracion(116)."/oflaDemo";
	$playerSetting["rtmp_plugin"] = SP_FLASH."/flowplayer.rtmp-3.2.13.swf";
	$playerSetting["player"] = SP_FLASH."/flowplayer.commercial-3.2.18.swf";
	$playerSetting["player_img"] = SP_IMAGES."/PosterFrame_PlayIcon.jpg";
	$playerSetting["watermark"] = PresentWatermark($ds_matricula);
	$playerSetting["st_id"] = $ds_matricula;
  
  function GetStreamPrograms($fl_usuario){
    
    # Get the sufix for the languaje
    $sufix=langSufix();

    # Obtiene el perfil
    $fl_perfil = ObtenPerfilUsuario($fl_usuario);
    if($fl_perfil == PFL_ESTUDIANTE_SELF){
      # Obtenemos los programas que esta cursando
      $Query  = "SELECT  a.fl_programa_sp, b.nb_programa".$sufix;
      $Query .= " FROM k_usuario_programa a ";
      $Query .= "LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) ";
      $Query .= "WHERE fl_usuario_sp=".$fl_usuario." AND EXISTS(SELECT 1 FROM c_pagina_sp c WHERE c.fl_programa_sp=a.fl_programa_sp) 
	  ORDER BY b.nb_programa".$sufix." ASC";
    }
    else{
      if($fl_perfil==PFL_MAESTRO_SELF){
        $Query  = "SELECT  a.fl_programa_sp, b.nb_programa".$sufix;
        $Query .= " FROM k_usuario_programa a ";
        $Query .= "LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) ";
        $Query .= "WHERE fl_maestro=".$fl_usuario." ";
        $Query .= "AND EXISTS(SELECT 1 FROM c_pagina_sp c WHERE c.fl_programa_sp=a.fl_programa_sp) GROUP BY fl_programa_sp ORDER BY b.nb_programa".$sufix." ASC ";
      }
      else{
        $Query  = "SELECT  a.fl_programa_sp, nb_programa".$sufix." FROM c_programa_sp a ";
        $Query .= "WHERE EXISTS(SELECT 1 FROM c_pagina_sp c WHERE c.fl_programa_sp=a.fl_programa_sp)";
      }
    }
    
    $rs = EjecutaQuery($Query);

  	$result = array();
  	for($i=0; $row=RecuperaRegistro($rs); $i++){
  		$fl_programa_sp = $row[0];
  	//$nb_programa = $row[1];
      $nb_programa = htmlspecialchars($row[1], ENT_QUOTES, "UTF-8");

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
<div id="board-header" class="no-border">
	<div class="row">
    <div class="col col-sm-12 col-lg-4 col-md-12">
    <select id="dropdown-programs" class="select2">
      <option value="0"><?php echo ObtenEtiqueta(70); ?></option>
      <?php
      # Obtiene el perfil
      $fl_perfil = ObtenPerfilUsuario($fl_usuario);
      if($fl_perfil == PFL_ESTUDIANTE_SELF){
        # Obtenemos los programas que esta cursando
        $Query  = "SELECT  a.fl_programa_sp, b.nb_programa".$sufix;
        $Query .= " FROM k_usuario_programa a ";
        $Query .= "LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) ";
        $Query .= "WHERE fl_usuario_sp=".$fl_usuario." AND EXISTS(SELECT 1 FROM c_pagina_sp c WHERE c.fl_programa_sp=a.fl_programa_sp) 
		ORDER BY b.nb_programa".$sufix." ASC
		
		";
      }
      else{
        if($fl_perfil==PFL_MAESTRO_SELF){
          $Query  = "SELECT  a.fl_programa_sp, b.nb_programa".$sufix;
          $Query .= " FROM k_usuario_programa a ";
          $Query .= "LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) ";
          $Query .= "WHERE fl_maestro=".$fl_usuario." ";
          $Query .= "AND EXISTS(SELECT 1 FROM c_pagina_sp c WHERE c.fl_programa_sp=a.fl_programa_sp) GROUP BY fl_programa_sp 
		  ORDER BY b.nb_programa".$sufix." ASC
		  ";
        }
        else{
          $Query  = "SELECT  a.fl_programa_sp, nb_programa".$sufix." FROM c_programa_sp a ";
          $Query .= "WHERE EXISTS(SELECT 1 FROM c_pagina_sp c WHERE c.fl_programa_sp=a.fl_programa_sp) ORDER BY nb_programa".$sufix." ASC";
        }
      }
      
      $rs = EjecutaQuery($Query);
      for($i=0;$row=RecuperaRegistro($rs);$i++){
        $fl_programa_sp = $row[0];
        //$nb_programa = str_texto($row[1]);
        $nb_programa = htmlentities($row[1], ENT_QUOTES, "UTF-8");
        echo "<option value='".$fl_programa_sp."'> ".$nb_programa." </option>";
      }
      ?>
    </select>
    </div>
  </div>
</div>

<!-- Content container -->
<div id="art-container"></div>


<!-- Base Modal Muestra videos -->
<div class="modal fade" id="remoteModal" tabindex="-1" role="dialog" data-backdrop='static' aria-labelledby="remoteModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">  
    <div class="modal-content" id="videos_library">      
    </div>
  </div>  
</div>

<script type="text/javascript">
  // select2
  pageSetUp();
	// Initialize page variables
	var programs; 
	programs = <?php GetStreamPrograms($fl_usuario); ?>;
  // Filter buttons
	var buttonPrograms, dropdownPrograms;
	buttonPrograms = $("#button-programs");
	dropdownPrograms = $("#dropdown-programs");
  
  // Main container
	var container;
	container = $("#art-container");
  
  // Load jquery form for image uploads, required by board.inc.js
	loadScript("<?php echo PATH_SELF_JS; ?>/plugin/jquery-form/jquery.form.min.js");

	$(document).ready(function(){		
		// Setup filter buttons	
		// boardController.setupFilterDropdown(dropdownPrograms, programs, "Programs", false);
    
    // details programs
    boardController.requestStudentlibrary(container);
    
    // Selecting a new program
		dropdownPrograms.on("click", function(){
			var text, selectedFilter;
      selectedFilter = $(this).val();      
			// text = $(this).text();      
			// selectedFilter = $(this).children().data("theme") || 0;
      // alert(text);
      
			// Empty container and the settings
			boardController.emptyContainer(container);

			// Reset other buttons
			// dropdownPrograms.find("li.active").toggleClass("active");

			// Set new name for the button
			// buttonPrograms.text(text).append(" <span class='caret'></span>");
			// Set the selected filter active
			// $(this).toggleClass("active");

			// Present the board with the new selected filter
			boardController.setSelectedFilter(selectedFilter);
			boardController.requestStudentlibrary(container);
		});
  });
  
  function ShowVideos(video){
    $('#remoteModal').modal('toggle');
    $.ajax({
      type: "POST",
      url: "<?php echo PATH_SELF_SITE; ?>/library_modal.php",
      async: false,
      data: "item="+video,
      // success: function(html){
        // $('.modal-content').empty().append(html);
      // }
    }).done(function(result){
      var result, contenido, item = $("#videos_library");
      result = JSON.parse(result);
      contenido = result.video;
      item.append(contenido);
    });
    /*var modalvideos;    
    modalvideos = $("#remoteModal");
    modalvideos.on("show.bs.modal", function(){
      $(this).find(".modal-content").load("site/library_modal.php", "item="+video);
      // Disable window scroll
      // modalvideos.css('overflow-y', 'hidden');
    });
    // $('.modal-header').remove();
    // $('#item-body').remove();
    // $('#item-footer').remove();
    $('html').css('overflow-y', 'hidden');
    modalvideos.modal("show");
    modalvideos.off("show.bs.modal");*/
  }
</script>
