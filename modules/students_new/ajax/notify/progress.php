<?php 
	# Libreria de funciones
	require("../../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

?>

<ul class="notification-body">
	<li>
		<span>
			<div class="bar-holder no-padding">
				<span class="text lead">This week's tasks<span id="notification-week-info" class="pull-right"></span></span>
				<div class="progress progress-xs">
					<div id="notification-week-bar" class="progress-bar bg-colour-blue"></div>
				</div>
			</div>
		</span>
	</li>
	<li>
		<span>
			<div class="bar-holder no-padding">
				<span class="text lead"> Assignment <span id="notification-A-info" class="pull-right"></span></span>
				<div class="progress progress-xs">
					<div id="notification-A-bar" class="progress-bar bg-colour-blue"></div>
				</div>
			</div>
		</span>
	</li>
	<li>
		<span>
			<div class="bar-holder no-padding">
				<span class="text lead"> Assignment Reference <span id="notification-AR-info" class="pull-right"></span></span>
				<div class="progress progress-xs">
					<div id="notification-AR-bar" class="progress-bar bg-colour-blue"></div>
				</div>
			</div>
		</span>
	</li>
	<li>
		<span>
			<div class="bar-holder no-padding">
				<span class="text lead"> Sketch <span id="notification-S-info" class="pull-right"></span></span>
				<div class="progress progress-xs">
					<div id="notification-S-bar" class="progress-bar bg-colour-blue"></div>
				</div>
			</div>
		</span>
	</li>
	<li>
		<span>
			<div class="bar-holder no-padding">
				<span class="text lead"> Sketch Reference <span id="notification-SR-info" class="pull-right"></span></span>
				<div class="progress progress-xs">
					<div id="notification-SR-bar" class="progress-bar bg-colour-blue"></div>
				</div> 
			</div>
		</span>
	</li>
</ul>

<script type="text/javascript">
	// Progress Bars
	var requirements = <?php GetAssignProgress($fl_alumno); ?>;

	var total = requirements.size.total;
	var total_uploaded = requirements.size.total_uploaded;

	var A = requirements.total.A;
	var AR = requirements.total.AR;
	var S = requirements.total.S;
	var SR = requirements.total.SR;
	var A_uploaded = requirements.uploaded.A;
	var AR_uploaded = requirements.uploaded.AR;
	var S_uploaded = requirements.uploaded.S;
	var SR_uploaded = requirements.uploaded.SR;

	DisplayProgressBar("notification-week", total, total_uploaded);
	DisplayProgressBar("notification-A", A, A_uploaded);
	DisplayProgressBar("notification-AR", AR, AR_uploaded);
	DisplayProgressBar("notification-S", S, S_uploaded);
	DisplayProgressBar("notification-SR", SR, SR_uploaded);
	
	function DisplayProgressBar(type_name, type, type_uploaded){
		if(type == 0){
			$("#"+type_name+"-info").append("<i class='fa fa-ban'></i>");
			$("#"+type_name+"-bar").css("width", "100%");
		}	else {
			if(type_uploaded == type){
				$("#"+type_name+"-info").append("<i class='fa fa-check'></i>");
				$("#"+type_name+"-bar").css("width", "100%");
			} else {
				$("#"+type_name+"-info").append(type_uploaded+"/"+type);	
				var percent = (type_uploaded / type) * 100;
				$("#"+type_name+"-bar").css("width", percent+"%");
			}
		}
	}
</script>