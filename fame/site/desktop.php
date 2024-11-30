<?php

	# Libreria de funciones	
	require("../lib/self_general.php");
	
  # Libreria de funciones
	// require("../../modules/common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_programa = RecibeParametroNumerico('fl_programa', true);
  $new = RecibeParametroNumerico('new', true);
  $preview = RecibeParametroNumerico('preview', true);
  $vista_previa=RecibeParametroNumerico('uc',true);#unlock course , alumnos de vanas que quieren comprrar un curso.

  # Put the last activity CURRENT_TIMESTAMP() when is viewed a course for the user
  $Query = EjecutaQuery("UPDATE k_usuario_programa SET last_activity = current_timestamp WHERE fl_usuario_sp = $fl_usuario AND fl_programa_sp = $fl_programa ");
  
  if(empty($preview))
    $preview=0;
  # Si recibimos el new quiere decir que el usuario se aigno solo
  if(!empty($new) AND !empty($fl_programa)){
    # Buscamos quien fue el que lo invito y lo ponemos como teacher
    $row1 = RecuperaValor("SELECT fl_usu_invita FROM c_usuario WHERE fl_usuario=$fl_usuario");
    $fl_usu_invita=!empty($row1[0])?$row1[0]:NULL;
    # Solo lo inserta una vez
    if(!ExisteEnTabla('k_usuario_programa', 'fl_usuario_sp', $fl_usuario, 'fl_programa_sp',$fl_programa, true)){
      EjecutaQuery("INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,fe_inicio_programa, fl_maestro) 
      VALUES ($fl_usuario, $fl_programa, NOW(), $fl_usu_invita)");
    }
  }
  $teacher = RecibeParametroNumerico('t', true);
  if(!empty($teacher)){
    $fl_usuario = RecibeParametroNumerico('student', true);
    $week = RecibeParametroNumerico('week', true);
    $nb_tab = RecibeParametroHTML('tab', false, true);
    $fg_otro_alumno = True;
  } else {
    $fg_otro_alumno = NULL;
    $nb_tab = NULL;
  }
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Obtenemos el perfil
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);

  #MJD 30-ene-2019. Se realiza la conulta para saber si esta asignado a un programa,esto solo para alumnos b2c que ya compraron un plan,que podran tener acceso a nuevos curso subidos por FAME.
  $Queryp="SELECT fg_plan FROM k_current_plan_alumno WHERE fl_alumno=$fl_usuario ";
  $rowplan=RecuperaValor($Queryp);
  if(isset($rowplan[0])){
      
      #Verificamos que este asignado al curso y si no lo asignamos.
      $Querypro="SELECT fl_usu_pro FROM  k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa";
      $ro=RecuperaValor($Querypro);
      if(empty($ro[0])){
          
          $fl_maestro="642";
          $Query ="INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fg_certificado,fg_status,fg_pagado,mn_pagado,fl_maestro,fg_status_pro,fe_inicio_programa) ";
          $Query.="VALUES($fl_usuario,$fl_programa,0,'0','0','RD','0',0,$fl_maestro,'0',CURRENT_TIMESTAMP)";
          $fl_usu_pro=EjecutaInsert($Query);
          
          
          # Por defaul indicamos que tendran una calificacion de quiz
          EjecutaQuery("INSERT INTO k_details_usu_pro (fl_usu_pro,fg_quizes,fg_grade_tea) VALUES ($fl_usu_pro,'1','0')");

          #Se genera el orden cronologico de desbloqueo.
          $Quert="SELECT no_orden FROM k_orden_desbloqueo_curso_alumno WHERE fl_alumno=$fl_usuario ORDER BY no_orden DESC ";
          $fl=RecuperaValor($Quert);
          $no_consecutiv=$fl['no_orden']+1;
          
          #Se genera su registro.
          $fl_consecu=EjecutaInsert("INSERT INTO k_orden_desbloqueo_curso_alumno (fl_alumno,fl_programa_sp,no_orden,fe_creacion,fg_motivo )VALUES($fl_usuario , $fl_programa,$no_consecutiv,CURRENT_TIMESTAMP,'PL') ");

      }

  }

   # Buscamos si el usuario esta inscrito al programa
  $rowq = RecuperaValor("SELECT fl_usu_pro, fl_maestro FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa");  
  $fl_usu_pro=!empty($rowq[0])?$rowq[0]:NULL;

  if(empty($rowq[0]) && $fl_perfil==PFL_ESTUDIANTE_SELF){
      
    if($vista_previa){#para vista previa del estudinate de vanas que quiere comprar un curso
        $existe_course = 1;
    }else{

    $existe_course = 0;
    $fl_maestro_sp = 0;
    $ds_maestro = "";
    } 
        
  } else {
    $existe_course = 1;
    $fl_maestro_sp = !empty($rowq[1])?$rowq[1]:NULL;
    $rowt = RecuperaValor("SELECT CONCAT(a.ds_nombres,' ', a.ds_apaterno) FROM c_usuario a, c_maestro_sp b WHERE a.fl_usuario = b.fl_maestro_sp AND a.fl_usuario=".$fl_maestro_sp);
    $ds_maestro = str_texto(!empty($rowt[0])?$rowt[0]:NULL);
    
    #Verificamos su fecha de inicio.
    $Que="SELECT fe_inicio_programa FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_usu_pro=$fl_usu_pro  ";
    $fe=RecuperaValor($Que);
    $fe_inicio_programa=!empty($fe['fe_inicio_programa'])?$fe['fe_inicio_programa']:NULL;
    
    if(empty($fe_inicio_programa)){
    #sE ASIGNA SU FECHA DE REGISTRO DEL PROGRAMA SE ROMA COMO BASE SU FECHA DE REGISTRO A FAME.
     $CON="SELECT fe_alta FROM c_usuario WHERE fl_usuario=$fl_usuario ";  
     $res=RecuperaValor($CON);
     
     #Inserta fecha
     $IN="UPDATE k_usuario_programa SET fe_inicio_programa='$res[0]' WHERE fl_usu_pro=$fl_usu_pro  ";
     EjecutaQuery($IN);

    }
    
  }
  
  # Obtenemos la informacion del student
  $ds_ruta_avatar = ObtenAvatarUsuario($fl_usuario);  
  $ds_ruta_foto = ObtenFotoUsuario($fl_usuario);  
  $ds_nombre_user = ObtenNombreUsuario($fl_usuario);
  $nb_programa = ObtenNombreCourse($fl_programa);
  $current_session = ObtenSessionActualCourse($fl_usuario, $fl_programa);  
  
  # Si el perfil es maestro no deactivara los sessiones
  # Si es estudiante desactivara las sesiones despues del primer Quiz encontrado
  $no_semenas = ObtenSemanaMaximaAlumno($fl_programa);
  if($fl_perfil == PFL_ESTUDIANTE_SELF){
        $nextquiz  = GetNextWeekQuiz($fl_usuario, $fl_programa);
        $view_certificado = true;
  }
  else{
        $nextquiz  = 0;
        $view_certificado = false;
  }
  
  #Para validad vista previa del estudiante de vanas para comprar un curso(esto desbloquea todas las tabs).#para poder todas las tabs descomentar esto.
  /*if($vista_previa){
      $nextquiz  = 0;
      $view_certificado = false;
  }*/
  
  
  # Set the number of weeks for the student to view future weeks in advance
  $weeks_advance = $current_session;
  if(empty($current_session))
    $current_session = 1;
  if(!empty($teacher)){
    $weeks_setting = array(
      "selected" => $week,
      "current" => $week,
      "max" => $no_semenas,
      "advance" => $nextquiz, 
      "next_quiz" => $nextquiz	
    );
  }
  else{
    $weeks_setting = array(
      "selected" => $current_session,
      "current" => $current_session,
      "max" => $no_semenas,
      "advance" => $nextquiz, 
      "next_quiz" => $nextquiz	
    );
  }

  # Status del programa y el usuario 
  $row = RecuperaValor("SELECT ds_progreso, fg_terminado, fg_status_pro, fg_pagado FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa");
  $ds_progreso = !empty($row[0])?$row[0]:NULL;
  $fg_terminado = !empty($row[1])?$row[1]:NULL;
  $fg_status_pro = !empty($row[2])?$row[2]:NULL;
  if(empty($fg_status_pro))
    $fg_status_pro = 0;
  $fg_pagado = !empty($row[3])?$row[3]:NULL;
  
  function GetDesktopTabs($fl_alumno, $weeks_setting, $fl_programa){
  	# Add the program the student is in, may have multiple programs in the future, only has one for now
  	$result["programs"] = array("1" =>  ObtenNombreCourse($fl_programa));

    $fl_instituto = ObtenInstituto($fl_alumno);
   
    #Verificamos que exista un plan del instituto.
    $Query = "SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
    $row = RecuperaValor($Query);
    $fl_current_plan = $row[0];


  	# Create the week array
  	$weeks = array();
  	/*$max_semana = $weeks_setting["max"];
  	for($i=1; $i<=$max_semana; $i++){
  		$weeks[$i] = "Session ".$i;
  	}*/
    $Query = "SELECT fl_leccion_sp, fg_animacion, fg_ref_animacion, no_sketch,  fg_ref_sketch,no_semana FROM c_leccion_sp WHERE fl_programa_sp=$fl_programa ORDER BY no_semana ";
    $rs = EjecutaQuery($Query);
    $max_semana = $weeks_setting["max"];
    for($i=1;$row = RecuperaRegistro($rs); $i++){
      $fl_leccion_sp = $row[0];
      $fg_animacion = $row[1];
      $fg_ref_animacion = $row[2];
      $no_sketch = $row[3];
      $fg_ref_sketch = $row[4];
      $no_semana=$row[5];
      
      $barra = "";
      # Buscamos si el alumno ya termino esta leccion si si mostrar verde en caso de que no rojo
      $row1 = RecuperaValor("SELECT fg_complete FROM  k_leccion_usu WHERE fl_leccion_sp=$fl_leccion_sp AND fl_usuario_sp=$fl_alumno");
      $fg_complete=isset($row1[0])?$row1[0]:NULL;
      $clas_barr = "";
      if($fg_complete==0)
        $clas_barr = "hidden";
        $barra = "
        <div class='progress progress-micro' style='margin-bottom:0px;'>
          <div class='progress-bar bg-color-greenLight $clas_barr' role='progressbar' style='width: 100%' id='progress_sesssion_".$fl_leccion_sp."'></div>
        </div>";

      $weeks[$i] = ObtenEtiqueta(1230)." ".$no_semana.$barra;    

    }
  	$result["weeks"] = $weeks;
	
      if(!empty($fl_current_plan)){
          $result["tabs"] = array(
               "1" => "<div  rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1887)."' data-html='true'> <i id='keys_flow' class='fa fa-question-circle'></i> ".ObtenEtiqueta(395)." </div>",
               // "2" => "Video Brief",
               "2" => "Assignment",
               "3" => "Assignment Ref",
               "4" => ObtenEtiqueta(394),
               "5" => "Sketch Ref",
               "6" => ObtenEtiqueta(1670),
               "7" => ObtenEtiqueta(2215),
               "8" => ObtenEtiqueta(2625)
           );
      }else{


          $result["tabs"] = array(
                "1" => "<div  rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1887)."' data-html='true'> <i id='keys_flow' class='fa fa-question-circle'></i> ".ObtenEtiqueta(395)." </div>",
                "2" => "Assignment",
                "3" => "Assignment Ref",
                "4" => ObtenEtiqueta(394),
                "5" => "Sketch Ref",
                "6" => ObtenEtiqueta(1670),
                "7" => ObtenEtiqueta(2215)
            );

      }
		return $result;
  }
 
  function GetDefaultTabs($nb_tab, $weeks_setting){
  
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

    # Por defaul activamos el tab de assigment
		switch($nb_tab){
			case "lecture": $result["tab"] = 1; break;
			case "assignment": $result["tab"] = 2; break;
			case "assignment_ref": $result["tab"] = 3; break;
			case "sketch": $result["tab"] = 4; break;
			case "sketch_ref": $result["tab"] = 5; break;
            case "assignments_grade": $result["tab"] = 6; break;
            case "student_library":$result["tab"] = 8;break;
            case "working_files": $result["tab"] = 9; break;
			default: $result["tab"] = 1;
		}
    
  	# All result values are returned as int
  	return $result;
  }
  
  # Creates a list of boolean of week tabs that should be disabled
  # true = open tab, false = disable tab
  function GetDisabledWeeks($weeks_setting){
  	$semana_act = $weeks_setting["current"];
  	$max_semana = $weeks_setting["max"];
  	$weeks_advance = $weeks_setting["advance"];
    $next_quiz = $weeks_setting["next_quiz"];

  	for($i=1; $i<=$max_semana; $i++){  		
      # Activamos las semanas hasta el proximo quiz
      if(!empty($next_quiz)){
        if($i <= $next_quiz){
          $list[$i] = true;
        } else {
          $list[$i] = false;
        }
      }
      else
        $list[$i] = true;
  	}
  	return (Object) $list;
  }
  
  # Creates a list of boolean of "tab" tabs that should be disabled
  # true = open tab, false = disable tab
  function GetDisabledTabs($weeks_setting, $p_programa){
    $semana_act = $weeks_setting["current"];
  	$max_semana = $weeks_setting["max"];
  	$weeks_advance = $weeks_setting["advance"];    
    /*
		# Allow to view lecture and brief 2 weeks behind, and weeks in advance  	
  	for($i=1; $i<=$max_semana; $i++){
  		if($i < $semana_act - 2 OR $i > $semana_act + $weeks_advance){
  			$list[$i] = false;
  		} else {
  			$list[$i] = true;
  		}
      // $list[$i] = false;
  	}*/
  	
    $rs = EjecutaQuery("SELECT fl_leccion_sp, fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch, no_semana FROM c_leccion_sp WHERE fl_programa_sp=$p_programa");
    # Allow to view lecture and brief 2 weeks behind, and weeks in advance  	
  	for($i=1; $i<=$max_semana; $i++){
      $row = RecuperaRegistro($rs);
      $fl_leccion_sp = $row[0];
      $fg_animacion = $row[1];
      $fg_ref_animacion = $row[2];
      $no_sketch = $row[3];
      $fg_ref_sketch = $row[4];
      $no_semana = $row[4];
  		if(empty($fg_animacion)){
  			$list[$i] = false;
  		} else {
  			$list[$i] = true;
  		}      
  	}
    return (Object) $list;
  }

  
 
  
  
  
?>



<style>


.well {
	font-family: 'Open Sans',Arial,Helvetica,Sans-Serif  !important;
	font-size: 13px !important;
	
color: #333;
letter-spacing: none !important;
font-weight:normal !important;
	}
.h2 {
   letter-spacing: 0px !important;
   
}

b, strong {
    font-weight: none !important;
}


</style>





<!-- User profile-->
<div class="row margin-bottom-10">
  <div class="col-sm-12 col-md-12 col-lg-12">
    <div id="myCarousel" class="carousel fade profile-carousel">
      <div class="carousel-inner">
        <div id="header_student" class="item active">
          <div class="col-sm-5" style="position:absolute; top: -6px;">
            <?php
            Profile_pic_FAME($fl_usuario, $fl_programa, $current_session, $fl_maestro_sp);
            ?>
          </div>
        <img src="<?php echo $ds_ruta_foto; ?>" width="100%"></div>
      </div>
    </div>
  </div>
</div>
<!-- Desktop content -->
<div class="row">
	<div class="col-xs-12">
    <input type="hidden" id="preview" name="preview" value="<?php echo $preview; ?>">
		<div id="desktop-container" class="well no-padding" style="margin-bottom:0px;"></div>
	</div>
  <?php
  SectionIni();
  MuestraModal("certificado");
  MuestraModal("pause_course", true);
  SectionFin(); 
  ?>
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
  			<form role="form" method="POST" action="site/gallery_comment_iu.php" enctype='multipart/form-data'>
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

<!-- Modal para indicar al usuario que no tiene asignado el curso -->
<!--<div class="modal fade" id="modal-empty-course" tabindex="-1" role="dialog" aria-labelledby='myModalLabel' aria-hidden='true'>
  <div class="modal-dialog">
  	<div class="modal-content" style="width: 30%; margin: 3% 10% 15% 30%;">
    </div>
  </div>
</div>-->


<style>
b, strong {
    font-weight: none !important;
}
</style>
<script type="text/javascript">
   pageSetUp();
  // Variables
  var student, programa, disabledWeeks, disabledTabs, existe_course, mensaje;
  student = <?php echo json_encode($fl_usuario); ?>;
  student_otro = <?php echo json_encode($fg_otro_alumno); ?>;
  programa = <?php echo json_encode($fl_programa); ?>;
  disabledWeeks = <?php echo json_encode(GetDisabledWeeks($weeks_setting)); ?>;
  disabledTabs = <?php echo json_encode(GetDisabledTabs($weeks_setting, $fl_programa)); ?>;
  
  // Desktop tab names
  var desktopTabs, defaultTabs;
  desktopTabs = <?php echo json_encode(GetDesktopTabs($fl_usuario, $weeks_setting, $fl_programa)); ?>;
  defaultTabs = <?php echo json_encode(GetDefaultTabs($nb_tab, $weeks_setting)); ?>;  

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
  loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/jquery-form/jquery-form.min.js");

  $(document).ready(function(){
    // Verificamos si el usuario esta asignado al curso
    existe_course = '<?php echo $existe_course; ?>';
    mensaje = '<?php echo ObtenEtiqueta(1995); ?>';
    if(existe_course==0){
      var modal = $("#modal-empty-course"), modal_content,
      container = $("#desktop-container"), content_container;
      /*// modal.modal('toggle');
      // modal_content = modal.find(".modal-content");
      
      // content = 
      // "<div class='modal-header'>"+
        // "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>"+
        // "<h4 class='modal-title'></h4>"+
      // "</div>"+
      // "<div class='modal-body'>"+
        // "<div class='col-sm-12 col-md-12 col-lg-2'>"+
          // "<i class='glyphicon glyphicon-warning-sign txt-color-red' style='font-size:90px;'></i>"+
        // "</div>"+
        // "<div class='col-sm-12 col-md-12 col-lg-10'>"+
          // "<h3>"+mensaje+"</h3>"+
        // "</div>"+
      // "</div>"+
      // "<div class='modal-footer'>"+
        // "<div class=''><a class='btn btn-primary' href='javascript:gabriel();'>Go to course Library!!</a></div>"+
      // "</div>";*/
      
      // modal_content.append(content);
      content_container = 
      "<div class='text-center error-box'>"+
        "<h3 class='error-text tada animated' style='font-size: 50px;'>"+
          "<i class='fa fa-times-circle text-danger error-icon-shadow'></i> "+mensaje+
        "</h3>"+
      "</div>";
      container.append(content_container);
      
    }
    else{
    
      // Set the student being viewed
      desktopController.setStudent(student);
      desktopController.setPrograma(programa);

      // Setup desktop view and layout
        desktopController.setupTabs(desktopContainer, {names: desktopTabs.programs, type: "program", displayContent: false,fg_comprar:"<?php echo $vista_previa; ?>",fl_programa_sp:"<?php echo $fl_programa;?>",fl_usuario:"<?php echo $fl_usuario;?>",etq:"<?php echo ObtenEtiqueta(2076);?>"});
        desktopController.setupTabs(desktopContainer, {names: desktopTabs.weeks, type: "week", displayContent: false,fg_comprar:"0",fl_programa_sp:"<?php echo $fl_programa;?>",fl_usuario:"<?php echo $fl_usuario;?>",etq:"<?php echo ObtenEtiqueta(2076);?>"});
        desktopController.setupTabs(desktopContainer, {names: desktopTabs.tabs, type: "tab", displayContent: true,fg_comprar:"0",fl_programa_sp:"<?php echo $fl_programa;?>",fl_usuario:"<?php echo $fl_usuario;?>",etq:"<?php echo ObtenEtiqueta(2076);?>"});

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
      var lectureTab = $("ul[data-type='tab'] li:nth-child(1)");
          
          // validamos que si es otro alumno no puede ver los videos
      if(student_otro == true){
         desktopController.disableTab(lectureTab, "disabled");
         // desktopController.disableTab(briefTab, "disabled");
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
              desktopController.setActiveTab(desktopContainer, "tab", defaultTabs.tab);
            } 				
            // disable lecture and brief tabs
            // los students podran ver los videos anteriores ala clase actual
            // desktopController.disableTab(lectureTab, "disabled");          

          } else {
            // enable lecture and brief tabs
            desktopController.enableTab(lectureTab, "disabled");

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
      
      // Valor de la barra de progreso del programa
      var ds_progreso = '<?php echo $ds_progreso; ?>';
      if(ds_progreso==0)
        ds_progreso = 0;
      $("#val_progreso").html(ds_progreso);
      $("#progreso").css('width',ds_progreso + "%");
      $("#span_info").html(ds_progreso + "%");
      // Mostramos el anuncio de la certificacion
      var fg_terminado = '<?php echo $fg_terminado; ?>';
      var fg_pagado = '<?php echo $fg_pagado; ?>';
      var view_certificado = '<?php echo $view_certificado; ?>';
      var course_pause = '<?php echo $fg_status_pro; ?>'; // indica si esta pausado el curso
      if(fg_terminado==1 && view_certificado && fg_pagado==0 && course_pause==0){
        var name_course = '<?php echo $nb_programa; ?>'; 
        var fl_programa = '<?php echo $fl_programa; ?>';
        var content  = 
            "<div class='row'>"+
              "<div class='col col-sm-12 col-lg-3 padding-top-10' style='margin-top:20px;'>"+
                "<i class='glyphicon glyphicon-star-empty' style='font-size:70px;'></i> <i> </i>"+
              "</div><div class='col col-sm-12 col-lg-9 text-aling-left'>"+
              "<strong><h3 class='no-margin'>SUCCESS!!</h3></strong><br/>"+
              "<div>you have completed the <strong>"+name_course+"</strong> course </div><br>"+
              "<div>Would you like to request your Certificate?</div>"+
              "<div class='row text-align-right'><a class='btn bg-color-pink txt-color-white btn-xs' id='btn_later_cer'>Later</a>&nbsp;"+
              "<a class='btn btn-success btn-xs' id='btn_yes_cer'>Yes</a></div>"+
              "</div></div>";
        $.smallBox({
          // title: 'SUCCESS',
          content: content,
          color: "#0071BD",
          // iconSmall: "fa fa-times bounce animated",
          timeout: 1000
        });
        
        // Botones
        $("#btn_yes_cer").click(function(){
          Modal_Certificado(fl_programa);
        });
      }

      /** Verifica si el usuario no tiene pausado el programa**/
      user_pause(<?php echo $fg_status_pro.",".$fl_programa.",".$fl_usuario; ?>);
    }
  });

    function gabriel(){      
      // Regresamos al course library
      // location.href="#site/courses_library.php";
      // Cerramos el modal
      $('#modal-empty-course').modal('toggle');
    }
    
</script>
