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

  # Tabs format: "name" => "text displayed", all tabs needs to be given a name for navigation
  $tabs = array(
  	"p_grade" => ObtenEtiqueta(1947),				// Assignment to Grade 1
  	"p_incomplete" => ObtenEtiqueta(1948), 	// Incomplete Assignments 3
	"p_history" => ObtenEtiqueta(1949),				// Grading History 2 
  	"p_assignment_grade" => ObtenEtiqueta(1674)				// Assignment grade 
  );
  
?>



 <!----------librerias para presentar sliders Barra azul--------->
						<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.min.css" />
						<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.css" />
						<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.js" ></script>
						<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.min.js" ></script>


						
<!-- Button presentar coemntarios assigment -->
<button type="button" class="btn btn-primary hidden"  id='view_coment'  data-toggle="modal" data-target="#myModalgrade">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="myModalgrade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div id='view' name='view'>
			  
		</div>	  
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
						
						
						
						



<div class="row">
	<div class="col-xs-12">
		<div class="well no-padding">
			<ul id="pending-nav-tabs" class="nav nav-tabs bordered"></ul>
			<div id="pending-nav-content" class="tab-content no-padding"></div>
		</div>
	</div>
</div>
<!--<div id='dlg_grade'>-->
  <div class='modal fade' id='dlg_grade_content' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='overflow-y:scroll;overflow:auto'>
  </div>
<!--</div>-->
<div id='dlg_attendance'><div id='dlg_attendance_content'></div></div>

<script type="text/javascript">
  pageSetUp();
  
	// Initialize the page tabs and contents
	var tab, tabs, tabs_name, content;
	tabs = <?php echo json_encode((Object)$tabs); ?>;  
	tabs_name = Object.keys(tabs);
  
	tab = "";
	for(var i=0; i<tabs_name.length; i++){
		tab += "<li id='tab_"+i+"'>"+"<a data-toggle='tab' href='#"+tabs_name[i]+"' onclick='RequestPending(\""+tabs_name[i]+"\")'>"+"<span>"+tabs[tabs_name[i]]+"</span>"+"</a>"+"</li>";
	}
	$("#pending-nav-tabs").append(tab);
	$("li:has(a[href=#"+tabs_name[0]+"])").addClass("active");

	content = "";
	for(var i=0; i<tabs_name.length; i++){
		content += "<div id='"+tabs_name[i]+"' class='tab-pane'></div>";
	}
	$("#pending-nav-content").append(content);
	$("#"+tabs_name[0]).addClass("active");

	// Initialze pending tables
	RequestPending(tabs_name[0]);
	// RequestPending(tabs_name[1]);
	// RequestPending(tabs_name[2]);
	RequestPending(tabs_name[3]);

	function RequestPending(p_tab){
		var tab;
		var container = $("#"+p_tab+"");

		if(p_tab == "p_grade"){ tab = 1; } 
		if(p_tab == "p_history"){ tab = 2; }
		if(p_tab == "p_incomplete"){ tab = 3; }
		if(p_tab == "p_assignment_grade"){ tab = 4; }

		$.ajax({
			type: 	'POST',
			url : 	'site/submitted_assignments_content.php',
			data: 	'tab='+tab,
			beforeSend : function() {
				// Loading screen
				container.html('<div class="col col-sm-12 col-lg-12 col-xs-12"><i class="fa fa-cog fa-spin"></i> Loading...</div>');
				$("html").animate({scrollTop : 0}, "fast");
			}
		}).done(function(result){
			container.empty();
			container.append(result);
		});
		
		
		$("#tab_3").addClass("hidden");
		
	}
</script>