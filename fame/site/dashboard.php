<?php 
	# Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Function to programs from user
  function GetUserPrograms($p_user){
    $Query  = "SELECT pr.fl_programa_sp, pr.nb_programa".$sufix;
    $Query .= " FROM k_usuario_programa  usp ";
    $Query .= "LEFT JOIN c_programa_sp pr ON(pr.fl_programa_sp=usp.fl_programa_sp) ";
    $Query .= "WHERE fl_usuario_sp=$p_user ORDER BY nb_programa".$sufix." ASC ";
    $rs = EjecutaQuery($Query);
    $result = array();
  	for($i=0; $row=RecuperaRegistro($rs); $i++){
  		$fl_programa_sp = $row[0];
  		$nb_programa = htmlentities($row[1], ENT_QUOTES, "UTF-8");

  		$result[$i] = array(
  			"fl_tema" => $fl_programa_sp,
  			"name" => $nb_programa
  		);
  	}
  	$result["size"] = array("total" => $i);
  	echo json_encode((Object) $result);
  }

  # Obten el programa en que se leasigno una calificacion y realizo un quiz actualmete
  function GetProgram($p_user){
    # Buscamos si hay un calificacion
    $Query  = "SELECT fl_programa_sp FROM k_entrega_semanal_sp a, c_leccion_sp  b ";
    $Query .= "WHERE a.fl_leccion_sp=b.fl_leccion_sp AND fl_alumno=$p_user AND fl_promedio_semana IS NOT NULL ";
    $Query .= "ORDER BY  fe_entregado DESC LIMIT 1 ";
    $row = RecuperaValor($Query);
    $fl_programa_sp = $row[0];
    if(empty($fl_programa_sp)){
      $Query  = "SELECT fl_programa_sp FROM k_quiz_calif_final a, c_leccion_sp b ";
      $Query .= "WHERE a.fl_leccion_sp=b.fl_leccion_sp AND a.fl_usuario=$p_user ";
      $Query .= "ORDER BY a.fe_final DESC LIMIT 1 ";
      $row = RecuperaValor($Query);
      $fl_programa_sp = $row[0];
    }
    $name_programa = ObtenNombreCourse($fl_programa_sp);
    $result["fl_programa_actual"] = $fl_programa_sp;
    $result["name_programa_actual"] = $name_programa;
  	echo json_encode((Object) $result);
    
  }
  
?>
<div class="row">
<div class="col-xs-12 col-sm-7 col-md-7 col-lg-5">
  <h1 class="page-title txt-color-blueDark">Dashboard</h1>
</div>
</div>

<div class='row'>
  <!-- My Performance -->
  <div class='col-xs-12'>
    <div class='jarviswidget'>
      <header>
        <div class="pull-right">
          <ul class='nav nav-tabs' id='myTab'>
            <li class='active'><a data-toggle='tab' href='#performance-progress'><i class='fa fa-clock-o'></i> <span class='hidden-mobile hidden-tablet'>Live Stats</span></a></li>
          </ul>
        </div>
        <div class="widget-toolbar pull-left" role="menu">
          <div class="btn-group">
            <a id="button-programs" class="btn dropdown-toggle btn-xs btn-primary" data-toggle="dropdown" aria-expanded="false"  href="javascript:void(0);"
            rel="tooltip" 
            data-placement="top"
            data-original-title="Select your programs"
            data-html="true"
            >
              Programs  <i class="fa fa-caret-down"></i>
            </a>
            <ul id="dropdown-programs" class="dropdown-menu"></ul>
          </div>
        </div>        
      </header>      
      <!-- Main container -->
      <div class="no-margin no-padding no-border" id="art-container"></div>      
    </div>
  </div>
</div>

 <script type="text/javascript">  
	// Initialize page variables
	var programs, programa_actual;
	programs = <?php GetUserPrograms($fl_usuario); ?>;
  programa_actual = <?php GetProgram($fl_usuario); ?>;
  
	// Filter buttons
	var buttonPrograms, dropdownPrograms;	
	buttonPrograms = $("#button-programs");
	dropdownPrograms = $("#dropdown-programs");
  
  // Main container
	var container;
	container = $("#art-container");
  
  
	// Load jquery form for image uploads, required by board.inc.js
	// loadScript("<?php echo PATH_SELF_JS; ?>/plugin/jquery-form/jquery.form.min.js");

	$(document).ready(function(){    
    // Initialzes the charts and tables
    pageSetUp();
    
		// Setup filter buttons
		boardController.setupFilterDropdown(dropdownPrograms, programs, "Programs", false);
    
    // Initialize the board
		// container.packery({ columnWidth: 320, itemSelector: '.item', gutter: 10	});
    buttonPrograms.text(programa_actual.name_programa_actual).append(" <span class='caret'></span>");
    boardController.setSelectedFilter(programa_actual.fl_programa_actual);
		boardController.requestItems(container);
    
    
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
			boardController.requestItems(container);
		});
    
  });
  
 
</script>