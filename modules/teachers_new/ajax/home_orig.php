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

  $fl_alumno = $fl_usuario;

	# Retrieves the current week's assignment deadline
	function GetWeekAssignDeadline($fl_alumno){

		$fl_term = ObtenTermAlumno($fl_alumno);
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);
  	$no_semana = ObtenSemanaActualAlumno($fl_alumno);

	  $Query  = "SELECT fe_entrega, fe_calificacion ";
	  $Query .= "FROM c_leccion a LEFT JOIN k_semana b ";
	  $Query .= "ON (a.fl_leccion=b.fl_leccion AND fl_term=$fl_term) ";
	  $Query .= "WHERE fl_programa=$fl_programa ";
	  $Query .= "AND no_grado=$no_grado ";
	  $Query .= "AND no_semana=$no_semana";
	  $row = RecuperaValor($Query);

	  $fe_entrega = $row[0];
	  $fe_calificacion = $row[1];

	  $submit_day = date("l", strtotime($fe_entrega));
	  $submit_date = date("F j, Y", strtotime($fe_entrega));

	  $result["deadline"] = array(
	  	"submit_day" => $submit_day, 
	  	"submit_date" => $submit_date,
	  	"grading" => $fe_calificacion
	  );

	  echo json_encode((Object) $result);
	}

	# Retrieves the next class of the student
	function GetNextClass($fl_alumno){

		$fl_term = ObtenTermAlumno($fl_alumno);
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);
  	$no_semana = ObtenSemanaActualAlumno($fl_alumno);
  	$max_semana = ObtenSemanaMaximaAlumno($fl_alumno);
  	$next_semana = $no_semana + 1;

  	// Check for valid week to know if next lesson exists
  	if($next_semana > $max_semana){
  		$result["next"] = array(
  			"lesson" => "unavailable"
  		);
  		echo json_encode((Object) $result);
  		return;
  	}
		
		# Retrieve the info next class
	  $Query  = "SELECT no_semana, ds_titulo, fe_publicacion ";
	  $Query .= "FROM c_leccion a LEFT JOIN k_semana b ";
	  $Query .= "ON (a.fl_leccion=b.fl_leccion AND fl_term=$fl_term) ";
	  $Query .= "WHERE fl_programa=$fl_programa ";
	  $Query .= "AND no_grado=$no_grado ";
	  $Query .= "AND no_semana=$next_semana";
	  $row = RecuperaValor($Query);
	  
	  $no_semana = $row[0];
	  $ds_titulo = $row[1];
	  $fe_publicacion = $row[2];

	  $day = date("l", strtotime($fe_publicacion));
	  $start = date("F j, Y", strtotime($fe_publicacion));

	  $result["next"] = array(
	  	"week" => $no_semana,
	  	"lesson" => $ds_titulo,
	  	"start" => $start,
	  	"day" => $day
	  );
	 	
	  echo json_encode((Object) $result);
	}

	function GetGradePointAverage($fl_alumno){
		$result["grade"] = array();
		$result["size"] = array();
		$result["debug"] = array();

		$fl_term = ObtenTermAlumno($fl_alumno);
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);

		# hard coded for testing
		//$fl_term = 117;
		//$no_grado = 4;
		//$fl_programa = 1;

		# Find the class the student is in
		$Query  = "SELECT fl_class FROM c_class WHERE fl_programa=$fl_programa AND no_grado=$no_grado ";
		$row = RecuperaValor($Query);
		$fl_class = $row[0];

		$result["debug"] += array("fl_programa" => $fl_programa, "fl_class" => $fl_class, "no_grado" => $no_grado);

		# Find the correct term and period for the student
		$Query  = "SELECT a.fl_term ";
		$Query .= "FROM k_alumno_term a, k_term b, c_periodo c ";
		$Query .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$fl_alumno ";
  	$Query .= "ORDER BY c.fe_inicio, b.no_grado";
  	$consulta = EjecutaQuery($Query);
  	for($i=0; $row = RecuperaRegistro($consulta); $i++){
  		$row_term[$i] = $row[0];
  	}
  	$fl_term = $row_term[$no_grado-1];

		$result["debug"] += array("row_term" => $fl_term);

		# Lessons
		$Query  = "SELECT a.fl_leccion, a.no_semana, a.ds_titulo, b.fl_semana ";
  	$Query .= "FROM c_leccion a LEFT JOIN k_semana b ";
  	$Query .= "ON (a.fl_leccion=b.fl_leccion AND b.fl_term=$fl_term) ";
  	$Query .= "WHERE a.fl_programa=$fl_programa ";
  	$Query .= "AND a.fl_class=$fl_class ";
		$Query .= "AND a.no_grado=$no_grado ";
  	$Query .= "ORDER BY a.no_semana";
  	$rs = EjecutaQuery($Query);

  	$sum_grades = 0;
  	$total_lessons = 0;

  	for($i=0; $row = RecuperaRegistro($rs); $i++){
  		$no_semana = $row[1];
  		$fl_semana = $row[3];

  		//$result["debug"] += array("fl_semana$i" => $fl_semana);

  		if(!empty($no_semana)){
  			# Grades
		  	$Query  = "SELECT b.cl_calificacion, b.ds_calificacion, b.fg_aprobado, b.no_equivalencia ";
		  	$Query .= "FROM k_entrega_semanal a, c_calificacion b ";
		  	$Query .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
		  	$Query .= "AND a.fl_alumno=$fl_alumno ";
		  	$Query .= "AND a.fl_semana=$fl_semana";
		  	$row2 = RecuperaValor($Query);

		  	if(!empty($row2[0])){
		  		$cl_calificacion = $row2[0];
		  		$no_equivalencia = $row2[3];
		  		$result["grade"] += array("cl_calificacion$i" => $cl_calificacion, "no_equivalencia$i" => $no_equivalencia);
		  		$sum_grades += $no_equivalencia;
		  		$total_lessons += 1;
		  	} else {
		  		$result["grade"] += array("cl_calificacion$i" => "", "no_equivalencia$i" => "");
		  	}
  		}
	  	
  	}
  	$grade_average = round($sum_grades / ($total_lessons *100) * 100);
  	$result["average"] = array("GPA" => $grade_average);
  	$result["size"] += array("total" => $i);

  	echo json_encode((Object) $result);
  	
	}

	function GetProgramPieChart($fl_alumno){
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);
		$actual_week = ObtenSemanaActualAlumno($fl_alumno);

		// select all the lessons in $fl_programa
		$Query  = "SELECT count(a.fl_leccion), a.no_grado, max(a.no_semana) ";
		$Query .= "FROM c_leccion a, c_programa b ";
		$Query .= "WHERE a.fl_programa=b.fl_programa ";
		$Query .= "AND a.fl_programa=$fl_programa ";
		$Query .= "GROUP BY a.no_grado ";
		$Query .= "ORDER BY a.no_grado";
		$rs = EjecutaQuery($Query);

		// add up all the lessons/week to find the maximum weeks
		// also calculate the current week of term of the student
		$chart["data"] = array();
		$current_weeks = 0;
		$max_weeks = 0;

		for($i=0; $row=RecuperaRegistro($rs); $i++){
			if($row[1] < $no_grado){
				$current_weeks += $row[2];
			}
			$max_weeks += $row[2];
		}
		$current_weeks += $actual_week;

		$chart["data"] += array(
			"current" => $current_weeks,
			"max" => $max_weeks
		);
		echo json_encode((Object) $chart);
	}
?>

<div class="well well-light">
	<h6>Work in Progress! We thank you for your patience!</h6>
</div>


<div class="row">
	<div class="col-sm-6 col-md-3 col-lg-3">
		<div id="current-week" class="well ht-150"></div>
	</div>
	<div class="col-sm-6 col-md-3 col-lg-3">
		<div id="live-session" class="well ht-150"></div>
	</div>
	<div class="col-sm-6 col-md-3 col-lg-3">
		<div id="assignment-deadline" class="well ht-150"></div>
	</div>
	<div class="col-sm-6 col-md-3 col-lg-3">
		<div id="next-class" class="well ht-150"></div>
	</div>
</div>

<div class="row">
	<!-- My Performance -->
	<div class="col-xs-12">
		<div class="jarviswidget">
			<header>
				<span class="widget-icon"> <i class="glyphicon glyphicon-stats txt-color-darken"></i> </span>
				<h2> Performance </h2>
				<ul class="nav nav-tabs pull-right in" id="myTab">
					<li class="active"><a data-toggle="tab" href="#performance-progress"><i class="fa fa-clock-o"></i> <span class="hidden-mobile hidden-tablet">Live Stats</span></a></li>
				</ul>
			</header>

			<div class="no-padding">
				<div class="widget-body" style="padding-bottom: 0;">
					<div class="row">
						<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
							<!-- Performance Chart -->
							<div id="bar-chart" class="chart"></div>
						</div>
						<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
							<!-- Assignment Progress Bars -->
							<div id="myTabContent" class="tab-content">
								<div class="tab-pane fade active in padding-10 no-padding-bottom" id="performance-progress">
									<div class="row no-space">
										<div class="col-xs-12 show-stats">
											<div class="row">
												<div class="col-xs-12"> 
													<span class="text lead">This week's tasks<span id="week-info" class="pull-right"></span></span>
													<div class="progress">
														<div id="week-bar" class="progress-bar bg-colour-blue"></div>
													</div> 
												</div>
												<div class="col-xs-12"> 
													<span class="text lead"> Assignment <span id="A-info" class="pull-right"></span> </span>
													<div class="progress">
														<div id="A-bar" class="progress-bar bg-colour-blue"></div>
													</div>
												</div>
												<div class="col-xs-12">

													<span class="text lead"> Assignment Reference <span id="AR-info" class="pull-right"></span> </span>
													<div class="progress">
														<div id="AR-bar" class="progress-bar bg-colour-blue"></div>
													</div>
												</div>
												<div class="col-xs-12"> 
													<span class="text lead"> Sketch <span id="S-info" class="pull-right"></span> </span>
													<div class="progress">
														<div id="S-bar" class="progress-bar bg-colour-blue"></div>
													</div> 
												</div>
												<div class="col-xs-12"> 
													<span class="text lead"> Sketch Reference <span id="SR-info" class="pull-right"></span> </span>
													<div class="progress">
														<div id="SR-bar" class="progress-bar bg-colour-blue"></div>
													</div> 
												</div>
											</div>
										</div>
									</div>
								</div> <!-- end tab pane -->
								
							</div>
						</div>
					</div>
						
					<!-- Overall Progress Pie Charts -->
					<div class="row no-margin" style="border: 1px solid #DADADA">
						<h6 class="text-center">Overall Progress</h6>
						<div class="show-stat-microcharts no-margin">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
								<div class="row no-margin">
									<div class="col-xs-2 col-sm-2 no-padding">
										<div id="program-percent" class="easy-pie-chart txt-color-orangeDark" data-percent="0" data-pie-size="55">
											<span class="percent percent-sign"></span>
										</div>
									</div>
									<div class="col-xs-10 col-sm-10">
										<span id="program-title" class="easy-pie-title"></span>
									</div>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
								<div class="row no-margin">
									<div class="col-xs-2 col-sm-2 no-padding">
										<div id="term-percent" class="easy-pie-chart txt-color-greenLight" data-percent="0" data-pie-size="55">
											<span class="percent percent-sign"></span>
										</div>
									</div>
									<div class="col-xs-10 col-sm-10">
										<span id="term-title" class="easy-pie-title"></span>
									</div>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
								<div class="row no-margin">
									<div class="col-xs-2 col-sm-2 no-padding">
										<div id="GPA-percent" class="easy-pie-chart txt-color-blue" data-percent="0" data-pie-size="55">
											<span class="percent percent-sign"></span>
										</div>
									</div>
									<div class="col-xs-10 col-sm-10">
										<span id="GPA-title" class="easy-pie-title"></span>
									</div>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
								<div class="row no-margin">
									<div class="col-xs-2 col-sm-2 no-padding">
										<div id="time-spend-percent" class="easy-pie-chart txt-color-darken" data-percent="0" data-pie-size="55">
											<span class="percent percent-sign"></span>
										</div>
									</div>
									<div class="col-xs-10 col-sm-10">
										<span id="time-spend-title" class="easy-pie-title"> Time Spent on Campus </span>
									</div>
								</div>
								
							</div>

						</div>
					</div>
					
				</div>

			</div>

		</div>

	</div> 	

</div> 

<script type="text/javascript">
	pageSetUp();

	// Initialize Top 4 Panels
	var studentInfo = <?php GetStudentInfo($fl_alumno); ?>;
	
	var week = studentInfo.info.week;
	var lesson = studentInfo.info.lesson;
	var live_session = studentInfo.info.live_session_title;
	var live_session_time = studentInfo.info.live_session_time;
	var day = studentInfo.info.day;
	$("#current-week").append("<h1><strong>You are on Week "+week+":</strong><br>\""+lesson+"\"</h1>");
	if(day == "unavailable"){
		$("#live-session").append("<h1><strong>Live Session Is Unavailable</strong></h1>");
	} else {
		$("#live-session").append("<h1><strong>Live Session starts on:</strong><br>"+day+",<br>"+live_session_time+"</h1>");
	}
	

	var result = <?php GetWeekAssignDeadline($fl_alumno); ?>;
	var submit_day = result.deadline.submit_day;
	var submit_date = result.deadline.submit_date;
	//var grading = result.deadline.grading;
	$("#assignment-deadline").append("<h1><strong>Assignment(s) are due on:</strong><br>"+submit_day+",<br>"+submit_date+"</h1>");
	//$("#grading-deadline").append("<h1><strong>"+lesson+" will be graded on "+grading+"</strong></h1>");

	var nextClass = <?php GetNextClass($fl_alumno); ?>;
	var nextWeek = nextClass.next.week;
	var nextLesson = nextClass.next.lesson;
	var nextStart = nextClass.next.start;
	var nextDay = nextClass.next.day;					// the day of the next class date
	if(nextLesson == "unavailable"){
		$("#next-class").append("<h1><strong>Next Class Is Unavailable</strong></h1>");
	} else {
		$("#next-class").append("<h1><strong>Next class is Week "+nextWeek+":</strong><br>\""+nextLesson+"\"<strong> starts on </strong>"+nextDay+", "+nextStart+"</h1>");
	}

	// End 4 top panels

	// chart colors default 
	var $chrt_border_color = "#efefef";
	var $chrt_grid_color = "#DDD"
	var $chrt_main = "#E24913";			/* red       */
	var $chrt_second = "#6595b4";		/* blue      */
	var $chrt_third = "#FF9F01";		/* orange    */
	var $chrt_fourth = "#7e9d3a";		/* green     */
	var $chrt_fifth = "#BD362F";		/* dark red  */
	var $chrt_mono = "#000";

	// Flot Bar Chart Dependencies
	loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/flot/jquery.flot.cust.js", loadFlotResize);
	function loadFlotResize() {
		loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/flot/jquery.flot.resize.js", loadFlotOrderBar);
	}
	function loadFlotOrderBar() {
		loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/flot/jquery.flot.orderBar.js", loadFlotToolTip);
	}	
	function loadFlotToolTip() {
		loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/flot/jquery.flot.tooltip.js", generateFlotBarChart);
	}
	// Flot Bar Chart
	function generateFlotBarChart(){
		if ($("#bar-chart").length) {

			var grades = <?php GetGradePointAverage($fl_alumno); ?>;
			var GPA = [];
			var xTicks = [];
			//console.log("grade: "+ JSON.stringify(grades.grade));
			//console.log("average: "+JSON.stringify(grades.average));
			//console.log("size: "+ JSON.stringify(grades.size.total));
			//console.log("debug: "+ JSON.stringify(grades.debug));

			// setting GPA pie chart's value here
			$("#GPA-title").append("GPA");
			$("#GPA-percent").data("easyPieChart").update(grades.average.GPA);

			for(var i = 0; i < grades.size.total; i++){
				var mark = parseInt(grades.grade["no_equivalencia"+i]);
				GPA.push([i+1, mark]);
				xTicks.push(i+1);
			}
	
			var ds = new Array();
			ds.push({
				data : GPA,
				bars : {
					show : true,
					barWidth : 0.5,
					//order : 1,
					align: "center"
				}
			});
	
			//Display graph
			$.plot($("#bar-chart"), ds, {
				xaxis : {
					ticks: xTicks			// weeks may vary
				},
				yaxis: {
          ticks: [10,20,30,40,50,60,70,80,90,100]  
        },
				colors : [$chrt_second, $chrt_fourth, "#666", "#BBB"],
				grid : {
					show : true,
					hoverable : true,
					clickable : true,
					tickColor : $chrt_border_color,
					borderWidth : 0,
					borderColor : $chrt_border_color
				},
				legend : true,
				tooltip : true,
				tooltipOpts : {
					content : "<b>Week %x</b> = <span>%y%</span>",
					defaultTheme : false
				}
			});
	
		}

		// Labels may be fixed later
		/*var xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>").text("Week").appendTo($('#bar-chart'));

		var yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>").text("Grade in Percentage").appendTo($('#bar-chart'));
		yaxisLabel.css("margin-top", yaxisLabel.width() / 2 - 20);*/

	}
	// End Flot Bar Chart

	// Progress Bars
	var requirements = <?php GetAssignProgress($fl_alumno); ?>;
	/*console.log("requirements: "+ JSON.stringify(requirements.total));
	console.log("uploaded: "+ JSON.stringify(requirements.uploaded));
	console.log("total_uploaded: "+ JSON.stringify(requirements.size.total_uploaded));
	console.log("debug: "+ JSON.stringify(requirements.debug));*/

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

	DisplayProgressBar("week", total, total_uploaded);
	DisplayProgressBar("A", A, A_uploaded);
	DisplayProgressBar("AR", AR, AR_uploaded);
	DisplayProgressBar("S", S, S_uploaded);
	DisplayProgressBar("SR", SR, SR_uploaded);
	
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
	// End Progress Bars

	// Pie Charts
	
		var program_name = <?php GetProgramName($fl_alumno); ?>;
		var chart = <?php GetProgramPieChart($fl_alumno); ?>;
		var max_program_week = chart.data.max;
		var current_program_week = chart.data.current;
		$("#program-title").append("Program Progress for \""+program_name+"\"");
		var percent = ((current_program_week/ max_program_week) * 100);
		$("#program-percent").data("easyPieChart").update(percent);	

		//console.log("current_weeks: "+chart.data.current);
		//console.log("max_weeks: "+chart.data.max);

		var no_grado = <?php GetCurrentTerm($fl_alumno); ?>;
		var max_week = <?php GetMaxWeek($fl_alumno); ?>;
		var actual_week = <?php GetActualWeek($fl_alumno); ?>;
		$("#term-title").append("You are on Term "+no_grado+" in Week "+actual_week+" with a total of "+max_week+" week(s)");
		var percent = ((actual_week / max_week) * 100);
		$("#term-percent").data("easyPieChart").update(percent);

		//console.log("max: "+max_term);
		//console.log("actual: "+actual_term);
	
	
	
	// End Pie Charts
	
</script>