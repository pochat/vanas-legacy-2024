<?php
	# Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Tabs format: "name" => "text displayed", all tabs needs to be given a name for navigation
  $tabs = array(
  	"p_grade" => ObtenEtiqueta(704),				// Assignment to Grade
  	"p_incomplete" => ObtenEtiqueta(706), 	// Incomplete Assignments
  	"p_history" => ObtenEtiqueta(705),				// Grading History
	"p_assignment_grade" => ObtenEtiqueta(1674)				// Assignment grade 
  );
?>


<!----------librerias para presentar sliders Barra azul--------->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.min.js" ></script>





<div class="row">
	<div class="col-xs-12">
		<div class="well no-padding">
			<ul id="pending-nav-tabs" class="nav nav-tabs bordered"></ul>
			<div class="table-responsive"><div id="pending-nav-content" class="tab-content no-padding"></div></div>
		</div>
	</div>
</div>
<div id='dlg_grade'><div id='dlg_grade_content'></div></div>
<div id='dlg_attendance'><div id='dlg_attendance_content'></div></div>


<script type="text/javascript">
	// Initialize the page tabs and contents
	var tab, tabs, tabs_name, content;
	tabs = <?php echo json_encode((Object)$tabs); ?>;
	tabs_name = Object.keys(tabs);

	tab = "";
	for(var i=0; i<tabs_name.length; i++){
		tab += "<li id='tab_"+i+"' onclick='RequestPending(\""+tabs_name[i]+"\");'>"+"<a data-toggle='tab' href='#"+tabs_name[i]+"'>"+"<span>"+tabs[tabs_name[i]]+"</span>"+"</a>"+"</li>";
	}
	$("#pending-nav-tabs").append(tab);
	$("li:has(a[href=#"+tabs_name[0]+"])").addClass("active");

	content = " ";
	for(var i=0; i<tabs_name.length; i++){
		content += "<div id='"+tabs_name[i]+"' class='tab-pane'><table class='table table-striped table-hover table-bordered'><tbody></tbody></table></div>";
	}
	$("#pending-nav-content").append(content);
	$("#"+tabs_name[0]).addClass("active");

	// Initialze pending tables default
	RequestPending(tabs_name[0]);
	RequestPending(tabs_name[1]);
	RequestPending(tabs_name[2]);
	RequestPending(tabs_name[3]);
  
	function RequestPending(p_tab){
		var tab;
		var container = $("#"+p_tab+"> table > tbody");

		if(p_tab == "p_grade"){ tab = 1; } 
		if(p_tab == "p_history"){ tab = 2; }
		if(p_tab == "p_incomplete"){ tab = 3; }
		if(p_tab == "p_assignment_grade"){ tab = 4; }

		$.ajax({
			type: 	'POST',
			url : 	'ajax/submitted_assignments_content.php',
			data: 	'tab='+tab,
			beforeSend : function() {
				// Loading screen
				container.html('<tr><td colspan="5" class="text-center" style="font-size:24px;"><i class="fa fa-cog fa-spin"></i> Loading...</td></tr>');
				$("html").animate({scrollTop : 0}, "fast");
			}
		}).done(function(result){
			container.empty();
			container.append(result);
		});
		
		$("#tab_3").addClass("hidden");
	}
	
	// Muestra dialogo para asignar calificacion
	function AssignGrade(entrega, tab) {
    if(tab=='')
      tab = tabs_name[0];
	  $.ajax({
	    type: "POST",
	    url: "ajax/get_assign_grades.php",
	    async: false,
	    data: "fl_entrega_semanal="+entrega+"&tab="+tab,
	    success: function(msg){
	      $('#dlg_grade_content').html(msg);
	      $('#dlg_grade').dialog('open');
	    }
	  });
	}

  // Muestra dialogo para asignar Attandance
	function AssignAttendace(entrega) {
	  $.ajax({
	    type: "POST",
	    url: "ajax/get_assign_attendance.php",
	    async: false,
	    data: "fl_entrega_semanal="+entrega,
	    success: function(msg){
	      $('#dlg_attendance_content').html(msg);
	      $('#dlg_attendance').dialog('open');
	    }
	  });
	}
	function get_grade(){    
   var datos = $('#datos1').serialize();
   $.ajax({
      type: "POST",
      url: "ajax/assign_grades.php",
      async: false,
      data: datos,
      // success: function(msg){
        // RequestPending(tabs_name[0]);
      // }
    }).done(function(result){
      var res, error, message, tab;
			res = JSON.parse(result);
      error = res.resultado.error;
      message = res.resultado.mensaje;
      tab = res.resultado.tab;
      if(error==false){
        RequestPending(tab);
      }
    });
  }
  
  $(function() {
	  $('#dlg_grade').dialog({
	  	appendTo: '#content',
	    autoOpen: false,
	    resizable: true,
	    width: 320,
	    height: 330,
	    hide: 'highlight',
	    title: 'Assign grade',
	    modal: true,
	    buttons: {
	      'Cancel': function() {
	        $(this).dialog('close');
	      },
	      'Submit': function() {
	        $(this).dialog('close');
	        // document.datos1.submit();
          get_grade();
	      }
	    }
	  });
    
    
    $('#dlg_attendance').dialog({
	  	appendTo: '#content',
	    autoOpen: false,
	    resizable: false,
	    width: 320,
	    height: 330,
	    hide: 'highlight',
	    title: 'Assign Attandance',
	    modal: true,
	    buttons: {
	      'Cancel': function() {
	        $(this).dialog('close');
	      },
	      'Submit': function() {
	        $(this).dialog('close');
	        document.datos_attendance.submit();
	      }
	    }
	  });
	});

</script>