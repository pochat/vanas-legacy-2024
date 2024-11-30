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
  $no_semana = RecibeParametroNumerico('week', True);
  
  # Revisa que se haya recibido un alumno
  if(empty($fl_alumno)) {
    header("Location: ".PATH_N_MAE."/index.php#ajax/home.php");
    exit;
  }
  
  # Revisa que se haya recibido un alumno
  if(empty($no_semana)){
    $no_semana = ObtenSemanaActualAlumno($fl_alumno);
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
  
  # Recupera los datos de la entrega de la semana
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);
  $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);
  $Query  = "SELECT fl_entrega_semanal ";
  $Query .= "FROM k_entrega_semanal ";
  $Query .= "WHERE fl_alumno=$fl_alumno ";
  $Query .= "AND fl_grupo=$fl_grupo ";
  $Query .= "AND fl_semana=$fl_semana";
  $row = RecuperaValor($Query);
  $fl_entrega_semanal = $row[0];
  
  # TODO: is this still used?
  echo "
    <input type='hidden' id='fl_entrega_semanal' value='$fl_entrega_semanal' />
    <input type='hidden' id='nb_archivo' value='' />
    <input type='hidden' id='fg_video' value='0' />";

  // MDB 29/JUL/2011
  // Creacion del archivo para llamar al applet de grabacion de critica
  // TODO Usar un directorio temporal, borrar los archivos, revisar que no se generen archivos nuevos cuando se cambia de semana, etc.
  $nombreArchivoJNLP = ObtenNombreArchivoJNLP($fl_entrega_semanal);
  $pathArchivosJNLP = $_SERVER['DOCUMENT_ROOT'] . SP_HOME . DIRECTORIO_JAR;
  $fileJNLP = fopen($pathArchivosJNLP . "/" . $nombreArchivoJNLP, 'w') or die("can't open file");
  
  // Contenido del archivo, configuracion para abrir el applet
  $contenido = "";
  $contenido .= "<?xml version='1.0' encoding='utf-8'?>\n";
  $contenido .= "<jnlp spec='1.0+' codebase='http://" . REMOTE_SERVER_NAME . SP_HOME . DIRECTORIO_JAR . "' href='" . $nombreArchivoJNLP . "'>\n";
  $contenido .= " <information> \n";
  $contenido .= "   <title>Vanas Record Critique</title>\n";
  $contenido .= "   <vendor>Vanas</vendor>\n";
  $contenido .= "   <homepage>http://www.vanas.ca/</homepage>\n";
  $contenido .= "   <description>Vanas Record Critique</description>\n";
  $contenido .= "   <description kind='short'>Vanas Record Critique</description>\n";
  $contenido .= "   <offline-allowed/>\n";
  $contenido .= " </information>\n";
  $contenido .= " <security>\n";
  $contenido .= "     <all-permissions/>\n";
  $contenido .= " </security>\n";
  $contenido .= " <resources>\n";
  $contenido .= " <j2se version='1.6+'/>\n";
  $contenido .= "        <jar href='screenshare.jar'/>\n";
  $contenido .= "    </resources>\n";
  $contenido .= "    <application-desc main-class='org.redfire.screen.ScreenShare'>\n";
  $contenido .= "       <argument>" . $fl_entrega_semanal . "</argument>";
  $contenido .= "    </application-desc>\n";
  $contenido .= "</jnlp>\n";
  fwrite($fileJNLP, $contenido);
  fclose($fileJNLP);

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
    );
    return $result;
  }

  function GetDefaultTabs($weeks_setting){
    # Is '1' right now as there is only one program per student, may have multiple programs in the future
    # Default program
    $result["program"] = 1;

    # Extra weeks setting
    $no_semana = $weeks_setting["selected"];
  
    # Default week    
    $result["week"] = (int)$no_semana;

    # Default tab

    /**
     * "lecture" = 1
     * "brief" = 2
     * "assignment" = 3
     * "assignment_ref" = 4
     * "sketch" = 5
     * "sketch_ref" = 6
     * "critique" = 7
     */
    $result["tab"] = 3;   // assignment tab

    # All result values are returned as int
    return $result;
  }

  # Creates a list of boolean of week tabs that should be disabled
  # true = open tab, false = disable tab
  function GetDisabledWeeks($weeks_setting){
    $no_semana = $weeks_setting["selected"];
    $max_semana = $weeks_setting["max"];

    for($i=1; $i<=$max_semana; $i++){
      if($i == $no_semana){
        $list[$i] = true;
      } else {
        $list[$i] = false;
      }
    }
    return (Object) $list;
  }

  # Creates a list of boolean of "tab" tabs that should be disabled
  # true = open tab, false = disable tab
  function GetDisabledTabs(){ 
    /**
     * "lecture" = 1
     * "brief" = 2
     * "assignment" = 3
     * "assignment_ref" = 4
     * "sketch" = 5
     * "sketch_ref" = 6
     * "critique" = 7
     */
    $list = array(
      "1" => false,
      "2" => false,
      "3" => true,
      "4" => true,
      "5" => true,
      "6" => true,
      "7" => false
    );

    return (Object) $list;
  }

?>

<!-- User profile -->
<div class="row">
  <div class="col-xs-12 col-sm-8 col-md-6 col-lg-4">
    <div class="well well-light padding-10 no-margin">
      <div class="row">
        <div class="col-xs-12">
          <div id="user-profile-container" class="profile-container"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Desktop content -->
<div class="row">
  <div class="col-xs-12" style='min-height:480px;'>
    <div id="desktop-container" class="well no-padding pull-left" ></div>
    <!-- Recording panel (webcam and toolbar) -->
    <div id="record-panel" class='well well-light pull-left' style='position:absolute; right:10px; top:0; width:320px;'>
      <div id='dlg_camara'></div>
    </div>
  </div>
</div>

<!-- Grading modal -->
<div id='dlg_grade'><div id='dlg_grade_content'></div></div>

<!-- Drawing canvas -->
<div id='canvas-container' title='Drawing Area'>
  <canvas id='canvas' width='720' height='375'></canvas>
  <canvas id='canvasInterface' width='720' height='375'></canvas>
</div>

<script type="text/javascript">
  // Record Critique Helper
  var critiqueController = (function($){

  })(jQuery);

  // Variables
  var student, disabledWeeks, disabledTabs;
  student = <?php echo json_encode($fl_alumno); ?>;
  disabledWeeks = <?php echo json_encode(GetDisabledWeeks($weeks_setting)); ?>;
  disabledTabs = <?php echo json_encode(GetDisabledTabs()); ?>;

  // Desktop tab names
  var desktopTabs, defaultTabs;
  desktopTabs = <?php echo json_encode(GetDesktopTabs($fl_alumno, $weeks_setting)); ?>;
  defaultTabs = <?php echo json_encode(GetDefaultTabs($weeks_setting)); ?>;

  // User profile
  var profileContainer, student;
  profileContainer = $("#user-profile-container");
  studentProfile = <?php GetStudentProfile($fl_alumno); ?>;

  // User desktop
  var desktopContainer;
  desktopContainer = $("#desktop-container");

  // Setup toolbar
  var recordPanel, toolbar;
  recordPanel = $("#record-panel");

  // TODO: move toolbar layout to front end
  toolbar = <?php echo json_encode(PresentToolBar()); ?>;
  recordPanel.prepend(toolbar);

  // Reposition the drawing area (canvas)
  $(window).on('resize.canvas', function(){
    // Content container
    var content;
    content = $("#content");

    // Canvas container
    var canvasContainer;
    canvasContainer = $("#canvas-container");

    // Tab index and current tab
    var index, currentTab;
    index = desktopController.getCurrentTab();
    currentTab = $("#tab"+index);

    if(currentTab.length > 0){
      canvasContainer.css('top', currentTab.offset().top - content.position().top);
      canvasContainer.css('left', currentTab.offset().left - content.position().left);
    }
  });

  // Setup webcam
  var webcam; 
  webcam = $("#dlg_camara");

  loadScript("<?php echo PATH_LIB; ?>/js_webcam/flash_resize.js");
  loadScript("<?php echo PATH_LIB; ?>/js_webcam/swfobject.js", function(){
    var presentWebcam, path, paddingLeft, paddingTop, x, y;

    // TODO: move webcam layout to front end
    presentWebcam = <?php echo PresentWebcam($fl_entrega_semanal); ?>;
    path = <?php echo json_encode(PATH_LIB."/js_webcam/broadcast.swf?folio=".$fl_entrega_semanal); ?>;
    
    // Add webcam div
    webcam.append(presentWebcam);
    
    // Find position of panel 
    paddingLeft = parseInt(recordPanel.css('padding-left'), 10);
    paddingTop = parseInt(recordPanel.css('padding-top'), 10);
    x = recordPanel.offset().left + paddingLeft;
    y = recordPanel.offset().top + paddingTop;

    // Initiate webcam dialog
    webcam.dialog({
      appendTo: '#record-panel',
      width: 275,
      height: 265,
      position: [x, y],
      closeOnEscape: false,
      title: 'My webcam',
      draggable: false,
      resizable: false,
      beforeClose: function(event, ui) { return false; }
    });
    
    // <![CDATA[
    var so = new SWFObject(path, 'broadcast', '250', '188', '8', '#FFFFFF');
    so.addParam('allowScriptAccess', 'always');
    so.addVariable('allowResize', canResizeFlash());
    so.write('broadcaster');
    // ]]>
  });
  
  // Reposition webcam
  $(window).on('resize.webcam', function(){
    var recordPanel, x, y, scrollTop;
    recordPanel = $("#record-panel");
    
    if(recordPanel.length > 0){
      scrollTop = $(window).scrollTop();
      paddingLeft = parseInt(recordPanel.css('padding-left'), 10);
      paddingTop = parseInt(recordPanel.css('padding-top'), 10);
      x = recordPanel.offset().left + paddingLeft;
      y = recordPanel.offset().top + paddingTop - scrollTop;

      webcam.dialog("option", "position", [x, y]);
    }
      
  });

  // Load jquery form, required for modal
  loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/jquery-form/jquery.form.min.js");

  $(document).ready(function(){

    // Critique handles tab content differently
    var displayAssignment = function(tabContainer, content){
      var lesson;
      
      lesson = 
        "<div class='well well-light no-margin no-padding' style='min-height:375px;'>"+
          content.deliverable+
        "</div>";
      tabContainer.append(lesson);

      // Initiate jqzoom
      $('.jqzoom').jqzoom({
        zoomType: 'drag',
        title: false,
        lens:true,
        preloadImages: true,
        zoomWidth: 425,
        zoomHeight: 305,
        xOffset: 10
      });
    };
    var url = 'ajax/rcritique_content.php';

    // Set the student being viewed
    desktopController.setStudent(student);

    // Setup user profile
    desktopController.setupProfile(profileContainer, studentProfile.profile);

    // Setup desktop view and layout
    desktopController.setupTabs(desktopContainer, {names: desktopTabs.programs, type: "program", displayContent: false});
    desktopController.setupTabs(desktopContainer, {names: desktopTabs.weeks, type: "week", displayContent: false});
    desktopController.setupTabs(desktopContainer, {names: desktopTabs.tabs, type: "tab", displayContent: true});

    // Disable week tabs in advance
    var weekList, tabList;
    weekList = $("ul[data-type='week'] li");
    desktopController.disableTabs(weekList, disabledWeeks);
    tabList = $("ul[data-type='tab'] li");
    desktopController.disableTabs(tabList, disabledTabs);

    // Set active tabs
    desktopController.setActiveTab(desktopContainer, "program", defaultTabs.program);
    desktopController.setActiveTab(desktopContainer, "week", defaultTabs.week);
    desktopController.setActiveTab(desktopContainer, "tab", defaultTabs.tab);

    // Initial request for desktop content
    desktopController.requestTabContent(desktopContainer, url, displayAssignment);

    // When a user clicks on a tab
    desktopContainer.on("click", "ul > li:not(.disabled)", function(){
      var type, index;
      type = $(this).parent().data("type");
      index = $(this).children().data("index");

      // request for new content if user clicks on different tabs
      if(desktopController.tabHasChanged(type, index)){
        // empty out current tab before updating to new tab variables
        desktopController.emptyTab();
        desktopController.setActiveVariable(type, index);
        desktopController.requestTabContent(desktopContainer, url, displayAssignment);
      }
    });

    // Setup grade modal dialog
    $('#dlg_grade').dialog({
      appendTo: '#content',
      autoOpen: false,
      resizable: false,
      width: 320,
      height: 300,
      hide: 'highlight',
      title: 'Assign grade',
      modal: true,
      buttons: {
        'Cancel': function() {
          $(this).dialog('close');
        },
        'Submit': function() {
          $(this).dialog('close');
          document.datos.submit();
        }
      }
    });
  });

  // Muestra dialogo para asignar calificacion
  function AssignGrade(entrega) {
    $.ajax({
      type: "POST",
      url: "ajax/get_assign_grades.php",
      async: false,
      data: "fl_entrega_semanal="+entrega,
      success: function(msg){
        $('#dlg_grade_content').html(msg);
        $('#dlg_grade').dialog('open');
      }
    });
  }
</script>