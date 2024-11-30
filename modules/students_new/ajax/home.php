<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

	# Retrieves the current week's assignment deadline
	function GetWeekAssignDeadline($fl_alumno){
	  $no_semana = ObtenSemanaActualAlumno($fl_alumno);
	  $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);

	  $Query  = "SELECT DATE_FORMAT(fe_entrega, '%W, %M %e, %Y') ";
  	$Query .= "FROM k_semana WHERE fl_semana=$fl_semana";
  	$row = RecuperaValor($Query);
  	$fe_entrega = $row[0];

	  $result["deadline"] = array("submit_date" => $fe_entrega);

	  echo json_encode((Object) $result);
	}

	# Retrieves the next class of the student
	function GetNextClass($fl_alumno){
	  $no_semana = ObtenSemanaActualAlumno($fl_alumno);
	  $max_semana = ObtenSemanaMaximaAlumno($fl_alumno);

	  // Next Week
	  $next_semana = $no_semana + 1;
	  if($next_semana > $max_semana){
	  	$result["lesson"] = array('week'=>'');
	  	echo json_encode((Object) $result);
	  	return;
	  }

	  $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $next_semana);
   	$Query  = "SELECT b.ds_titulo, DATE_FORMAT(fe_publicacion, '%W, %M %e, %Y') ";
  	$Query .= "FROM k_semana a ";
  	$Query .= "LEFT JOIN c_leccion b ON b.fl_leccion=a.fl_leccion ";
  	$Query .= "WHERE fl_semana=$fl_semana";
  	$row = RecuperaValor($Query);
  	$ds_titulo = $row[0];
  	$fe_publicacion = $row[1];

	  $result["lesson"] = array(
	  	"week" => $next_semana,
	  	"title" => $ds_titulo,
	  	"date" => $fe_publicacion
	  );
	 	
	  echo json_encode((Object) $result);
	}
  
	# Needs to be reworked
	function GetGradePointAverage($fl_alumno){
		$fl_grupo = ObtenGrupoAlumno($fl_alumno);

		$Query  = "SELECT d.no_semana, c.no_equivalencia, d.fg_animacion, d.fg_ref_animacion, d.no_sketch, d.fg_ref_sketch ";
		$Query .= "FROM k_entrega_semanal a ";
		$Query .= "LEFT JOIN k_semana b ON b.fl_semana=a.fl_semana ";
		$Query .= "LEFT JOIN c_calificacion c ON c.fl_calificacion=a.fl_promedio_semana ";
		$Query .= "LEFT JOIN c_leccion d ON d.fl_leccion=b.fl_leccion ";
		$Query .= "WHERE fl_alumno=$fl_alumno ";
		$Query .= "AND fl_grupo=$fl_grupo ";
		$Query .= "ORDER BY d.no_semana,c.no_equivalencia ";
		$rs = EjecutaQuery($Query);

		for($i=1; $row=RecuperaRegistro($rs); $i++){
			$no_semana = $row[0];
			$no_equivalencia = $row[1];
			
			if(!empty($no_equivalencia)){
				$grades["$no_semana"] = $no_equivalencia;	
			} else {
				$grades["$no_semana"] = "";
			}
		}
		$result['GPA'] = $grades;
		$result['size'] = array('total' => $i-1);

  	echo json_encode((Object) $result);
	}

	function GetPieCharts($fl_alumno){
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$current_no_grado = ObtenGradoAlumno($fl_alumno);
		$current_no_semana = ObtenSemanaActualAlumno($fl_alumno);
		$current_max_semana = ObtenSemanaMaximaAlumno($fl_alumno);
		$fl_grupo = ObtenGrupoAlumno($fl_alumno);

		// Find max weeks for each term of this program
		$Query  = "SELECT count(fl_leccion), no_grado ";
		$Query .= "FROM c_leccion ";
		$Query .= "WHERE fl_programa=$fl_programa ";
		$Query .= "GROUP BY no_grado ";
		$Query .= "ORDER BY no_grado ";
		$rs = EjecutaQuery($Query);

		// Find the total number of weeks of the program and total number of weeks the student has completed
		$total_weeks = 0;
		$total_weeks_done = 0;
		for($i=0; $row=RecuperaRegistro($rs); $i++){
			$max_weeks = $row[0];
			$no_grado = $row[1];

			// Total weeks for the full duration of the program
			$total_weeks += $max_weeks; 

			// Add up to the number of weeks the student has completed until this term
			if($no_grado < $current_no_grado){
				$total_weeks_done += $max_weeks;
			}
		}
		// Also include the weeks done in the current term
		$total_weeks_done += $current_no_semana;
		// Program Pie Chart
		$percent = $total_weeks_done / $total_weeks * 100;
    // Actualizamos los datos del alumno cada que ingresa
    $update = "UPDATE c_alumno SET mn_progreso='".round($percent,2)."', no_week_current=$current_no_semana WHERE fl_alumno=$fl_alumno ";
    EjecutaQuery($update);
		$result['program'] = array(
			'name' => ObtenNombreProgramaAlumno($fl_alumno),
			'percent' => $percent
		);

		// Term Weeks Pie Chart
		$percent = $current_no_semana / $current_max_semana * 100;
		$result['week'] = array(
			'term' => $current_no_grado,
			'current_week' => $current_no_semana,
			'max_week' => $current_max_semana,
			'percent' => $percent
		);

		// GPA Pie Chart
		$Query  = "SELECT COUNT(b.no_equivalencia), SUM(b.no_equivalencia) ";
		$Query .= "FROM k_entrega_semanal a ";
		$Query .= "LEFT JOIN c_calificacion b ON b.fl_calificacion=a.fl_promedio_semana ";
		$Query .= "WHERE fl_alumno=$fl_alumno ";
		$Query .= "AND fl_grupo=$fl_grupo ";
		$row = RecuperaValor($Query);
		$total_lessons = $row[0];
		$total_grades = !empty($row[1])?$row[1]:0;
        $percent=0;
        if($total_grades>0){
            $percent = $total_grades / $total_lessons;
        }

		$result['GPA'] = array(
			'percent' => $percent
		);

		echo json_encode((Object) $result);
	}

  function wizard($fl_alumno){
    
    $Query  = "SELECT ds_nombres, ds_apaterno, a.ds_email, ";
    $Query .= "c.ds_add_number, c.ds_add_street, c.ds_add_city, c.ds_add_state, c.ds_add_zip, ds_ruta_avatar, b.ds_ruta_foto ";
    $Query .= "FROM c_usuario a, c_alumno b, k_ses_app_frm_1 c WHERE a.fl_usuario=b.fl_alumno AND a.cl_sesion=c.cl_sesion AND a.fl_usuario=$fl_alumno ";
    $falta = 0;
    $row= RecuperaValor($Query);
    //1
    $ds_nombres = $row[0];
    if(empty($ds_nombres))
      $falta = $falta + 1;
    //2
    $ds_apaterno = $row[1];
    if(empty($ds_apaterno))
      $falta = $falta + 1;
    //3
    $ds_email = $row[2];
    if(empty($ds_email))
      $falta = $falta + 1;
    //4
    $ds_add_number = $row[3];
    if(empty($ds_add_number))
      $falta = $falta + 1;
    //5
    $ds_add_street = $row[4];
    if(empty($ds_add_street))
      $falta = $falta + 1;
    //6
    $ds_add_city = $row[5];
    if(empty($ds_add_city))
      $falta = $falta + 1;
    //7
    $ds_add_state = $row[6];
    if(empty($ds_add_state))
      $falta = $falta + 1;
    //8
    $ds_add_zip = $row[7];
    if(empty($ds_add_zip))
      $falta = $falta + 1;
    //9
    $ds_ruta_avatar = $row[8];
    if(empty($ds_ruta_avatar))
      $falta = $falta + 1;
    //10
    $ds_ruta_foto = $row[9];
    if(empty($ds_ruta_foto))
      $falta = $falta + 1;
    
    $porcentaje = round(100/10*(10-$falta));
    return $porcentaje ;
  }

  
?>

<style>
.parpadea {
  
                                animation-name: parpadeo;
                                animation-duration: 2s;
                                animation-timing-function: linear;
                                animation-iteration-count: infinite;

                                -webkit-animation-name:parpadeo;
                                -webkit-animation-duration: 2s;
                                -webkit-animation-timing-function: linear;
                                -webkit-animation-iteration-count: infinite;
                            }

                            @-moz-keyframes parpadeo{  
                                0% { opacity: 1.0; }
                                50% { opacity: 0.0; }
                                100% { opacity: 1.0; }
                            }

                            @-webkit-keyframes parpadeo {  
                                0% { opacity: 1.0; }
                                50% { opacity: 0.0; }
                                100% { opacity: 1.0; }
                            }

                            @keyframes parpadeo {  
                                0% { opacity: 1.0; }
                                50% { opacity: 0.0; }
                                100% { opacity: 1.0; }
                            }
</style>


<div class="panel panel-primary">
    <div class="panel-heading">Note: Access is enabled minutes before the class starts.</div>
    <div class="panel-body">
		<div class="row">
			<div class="col-sm-6 col-md-3 col-lg-3">
				<div id="current-week" class="well padding-10 ht-150"></div>
			</div>
			<div class="col-sm-6 col-md-3 col-lg-3" style="display:inline;">
				<div id="live-session" class="well padding-10 ht-150">
					<h1 class="no-margin">
						<strong>Live Class Is Unavailable</strong>
					</h1>
				</div>
			</div>
			<div class="col-sm-6 col-md-3 col-lg-3">
				<div id="assignment-deadline" class="well padding-10 ht-150"></div>
			</div>
			<div class="col-sm-6 col-md-3 col-lg-3">
				<div id="next-class" class="well padding-10 ht-150"></div>
			</div>
		</div>
	</div>
</div>
<!---
<div id="lecture_clases">
    <h1 class="no-margin"></h1>
</div>
<div id="review_clases">
    <h1 class="no-margin"></h1>
</div>-->
<div id="global_clases"></div>
    
<?php 
$Query = "Select CURDATE() ";
$row = RecuperaValor($Query);
$fe_actual = str_texto($row[0]);
$fe_actual=strtotime('+0 day',strtotime($fe_actual));
$fe_actual= date('Y-m-d',$fe_actual);
$date=date_create($fe_actual);
$fe_actual_humano=date_format($date,'F j, Y');


$fl_term = ObtenTermAlumno($fl_alumno);
$fl_grupo = ObtenGrupoAlumno($fl_alumno);
$diferencia = RecuperaDiferenciaGMT( );




   #Query para traer las claes grupales.

    $Queryg="
       (SELECT c.ds_titulo,DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%Y-%m-%d')fecha,DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p') hora,a.fe_clase fe_clase ,a.fl_clase fl_clase ,'Global'tipo 
    FROM k_clase a 
    LEFT JOIN k_semana b ON b.fl_semana=a.fl_semana 
    LEFT JOIN c_leccion c ON c.fl_leccion=b.fl_leccion 
    WHERE a.fl_grupo=$fl_grupo AND date_format(a.fe_clase,'%Y-%m-%d')='$fe_actual'
    )UNION
    (
    SELECT kcg.ds_titulo, DATE_FORMAT(DATE_ADD(kcg.fe_clase, INTERVAL $diferencia HOUR), '%Y-%m-%d') fecha,  DATE_FORMAT(DATE_ADD(kcg.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p') hora ,kcg.fe_clase fe_clase ,kcg.fl_clase_cg fl_clase ,'Global'tipo 
    FROM c_usuario cus, 
    k_ses_app_frm_1 frm 
    JOIN k_curso_cg  kcc ON(kcc.fl_programa=frm.fl_programa) 
    JOIN c_clase_global cc ON(cc.fl_clase_global = kcc.fl_clase_global) 
    LEFT JOIN k_clase_cg kcg ON ( kcg.fl_clase_global = cc.fl_clase_global ) 
    WHERE cus.cl_sesion = frm.cl_sesion AND fg_activo='1' AND cus.fl_usuario =$fl_alumno AND date_format(kcg.fe_clase,'%Y-%m-%d')='$fe_actual'
    )UNION 
    ( 
    SELECT a.nb_grupo ds_titulo, DATE_FORMAT(DATE_ADD(c.fe_clase, INTERVAL $diferencia HOUR), '%Y-%m-%d') fecha,DATE_FORMAT(DATE_ADD(c.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p')hora,c.fe_clase fe_clase ,c.fl_clase_grupo fl_clase ,'Review'tipo 
    FROM c_grupo a
    JOIN k_clase_grupo c ON c.fl_grupo=a.fl_grupo 
    JOIN k_alumno_grupo g ON g.fl_grupo=a.fl_grupo
    WHERE g.fl_alumno= $fl_alumno AND date_format(c.fe_clase,'%Y-%m-%d')='$fe_actual'
    ) ORDER BY fe_clase ASC
    ";
    $rsg = EjecutaQuery($Queryg);

?>

<div class="row" style="display:none;">
    <div class="col-md-12">
      <h1><b>Today's classes <?php echo $fe_actual_humano;?></b></h1>
       <table class="table" style="width:50%;">
           <thead>
               <tr>
                   <td width="25%">Class</td>
                   <td width="25%">Hour</td>
               </tr>
           </thead>
           <tbody>
               <?php 
               while($rowg=RecuperaRegistro($rsg)){

                   $ds_titulo = $rowg[0];  
                   $fe_clase = $rowg[1];
                   $hr_clase = $rowg[2];
                   

               ?>
               <tr>
                   <td><?php echo $ds_titulo;?></td>
                   <td><?php echo $hr_clase;?></td>
               </tr>
               <?php
               }
               ?>
           </tbody>
       </table>
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
										<div id="program-percent" class="easy-pie-chart txt-colour-blue" data-percent="0" data-pie-size="55">
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
										<div id="term-percent" class="easy-pie-chart txt-colour-blue" data-percent="0" data-pie-size="55">
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
										<div id="GPA-percent" class="easy-pie-chart txt-colour-blue" data-percent="0" data-pie-size="55">
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
										<div id="time-spend-percent" class="easy-pie-chart txt-colour-blue" data-percent="<?php echo wizard($fl_alumno); ?>" data-pie-size="55">
											<span class="percent percent-sign"></span>
										</div>
									</div>
									<div class="col-xs-10 col-sm-10">
										<a href="index.php#ajax/profile.php"><span id="time-spend-title" class="easy-pie-title"><?php echo ObtenEtiqueta(802); ?></span></a>
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
	// Initialzes the charts and tables
	pageSetUp();		

	// Initialize Top 4 Panels

	// Week Info Panel
	var student, weekPanel, week, lesson;
	student =<?php GetStudentProfile($fl_alumno);?>;

	weekPanel = $('#current-week');
	week = student.profile.week;
	lesson = student.profile.lesson;
	weekPanel.append(
		'<h1 class="no-margin">'+
			'<strong>You are on Week '+week+':</strong><br>'+
			'"'+lesson+'"'+
		'</h1>'
	);

	// Live Session Panel
	var student, liveSessionPanel, liveSessionExists, liveSessionLink, liveSessionStart, liveSessionClose, liveSessionReadable,liveSessionTitle;
	student = <?php GetStudentSession($fl_alumno); ?>;

	var LiveSesionGClass, liveSessionExistsGlobal, liveSessionLinkGoblalC, liveSessionStartGlobal, liveSessionCloseGlobal, liveSessionReadableGlobal, liveSessionTitleGlobal;
	student_global =<?php GetStudentSessionGlobalClass($fl_alumno); ?>;

	//var LiveSesionLecture,liveSessionExistsLecture, liveSessionLinkLecture, liveSessionStartLecture, liveSessionCloseLecture, liveSessionReadableLecture, liveSessionTitleLecture;
	//student_lecture =<?php GetStudentSessionLectureClass($fl_alumno); ?>;
	
	
    //var LiveSesionReview,liveSessionExistsReview, liveSessionLinkReview, liveSessionStartReview, liveSessionCloseReview, liveSessionReadableReview, liveSessionTitleReview;
	//student_review =<?php GetStudentSessionReviewClass($fl_alumno); ?>;
	

	liveSessionPanel = $('#live-session');
	liveSessionExists = student.session.exists;
	liveSessionLink = student.session.link;
	liveSessionStart = student.session.start.milliseconds;
	liveSessionClose = student.session.close.milliseconds;
	liveSessionReadable = student.session.readable;
	liveSessionTitle = student.session.titulo;
	type=student.session.type;

    LiveSesionGClass = $('#global_clases');
	liveSessionExistsGlobal = student_global.session.exists;
	liveSessionLinkGoblal = student_global.session.link;
	liveSessionStartGlobal = student_global.session.start.milliseconds;
	liveSessionCloseGlobal = student_global.session.close.milliseconds;
	liveSessionReadableGlobal = student_global.session.readable;
	liveSessionTitleGlobal = student_global.session.titulo;
	typeGlobal = student_global.session.type;

	/*
    LiveSesionLecture = $('#lecture_clases');
	liveSessionExistsLecture = student_lecture.session.exists;
	liveSessionLinkLecture = student_lecture.session.link;
	liveSessionStartLecture = student_lecture.session.start.milliseconds;
	liveSessionCloseLecture = student_lecture.session.close.milliseconds;
	liveSessionReadableLecture = student_lecture.session.readable;
	liveSessionTitleLecture = student_lecture.session.titulo;

    LiveSesionReview = $('#review_clases');
	liveSessionExistsReview = student_review.session.exists;
	liveSessionLinkReview = student_review.session.link;
	liveSessionStartReview = student_review.session.start.milliseconds;
	liveSessionCloseReview = student_review.session.close.milliseconds;
	liveSessionReadableReview = student_review.session.readable;
	liveSessionTitleReview = student_review.session.titulo;
	*/
	
	// If there is a live class
	if(liveSessionExists){
		// Setup panel
		liveSessionPanel.html(
			'<h1 class="no-margin"></h1>'+		
			'<a role="button" class="btn btn-sm btn-default pull-right disabled" style="position: absolute; right: 10px; bottom: 10px;">Join Class</a>'
		);

		var defaultOptions, startOptions, closeOptions;
		defaultOptions = {
			labels: ['years', 'months', 'weeks', 'days', 'gours', 'minutes', 'seconds'], 
    	labels1: ['year', 'month', 'week', 'day', 'hour', 'minute', 'second'],
    	format: 'DHMS',
    	alwaysExpire: true,
    	serverSync: function() {
				$.ajax({
					url: 'ajax/sync_server_time.php'
				}).done(function(result){
					result = JSON.parse(result);
					return new Date(result.timestamp.milliseconds) || new Date();
				}).fail(function(){
					return new Date();
				});
			}
		};
		startOptions = $.extend({}, defaultOptions, {
			until: new Date(liveSessionStart),
			//until: new Date('2015-01-12 10:39:00'),
			layout: '<div style="font-size: 20px;"><strong>Next Live Class:<br>'+liveSessionTitle+'</strong><br>'+
							liveSessionReadable+'<br>'+
							'<strong>in</strong> {dn} {dl} {hn}{sep}{mnn}{sep}{snn}</div>',
			alwaysExpire: true,
			onExpiry: function(){
				$(this).countdown('destroy');

				liveSessionPanel.html(
					'<h1 class="no-margin" >'+
						'<strong>You have a Live Class Right Now!</strong>'+
					'</h1>'+
					'<a role="button" class="btn btn-sm btn-success pull-right parpadea " '+liveSessionLink+' style="background:#0bb908; position: absolute; right: 10px; bottom: 10px;font-size:14px;" target="_blank">Join Class</a>'+
					'<h4></h4>'
				);
				// Start another countdown before closing the session
				liveSessionPanel.find('h4').countdown(closeOptions);
			}
		});
		closeOptions = $.extend({}, defaultOptions, {
			until: new Date(liveSessionClose),
			//until: new Date('2015-01-12 10:42:00'),
			layout: 'Class link closes in {hn}{sep}{mnn}{sep}{snn}',
			alwaysExpire: true,
			onExpiry: function(){
				$(this).countdown('destroy');
				liveSessionPanel.html(
					"<h1 class='no-margin'>"+
  					"<strong>Live Class link is Closed!</strong><br>"+
  					"<h4>Please update the page by pressing F5 or <a style='font-weight:500;' onclick='window.location.reload();'>here</a></h4>"+
  				"</h1>"+
				"<a role='button' class='btn btn-sm btn-default pull-right disabled' style='position: absolute; right: 10px; bottom: 10px;'>Join Class</a>"
				);
			}
		});

		// Start the countdown
		liveSessionPanel.find('h1').countdown(startOptions);
	}
	
	if (liveSessionExistsGlobal) {

    		// Setup panel start empty
    		LiveSesionGClass.html('<h1 class="no-margin"></h1>');

		var defaultOptionsG, startOptionsG, closeOptionsG;
		defaultOptionsG = {
			labels: ['years', 'months', 'weeks', 'days', 'gours', 'minutes', 'seconds'],
    	labels1: ['year', 'month', 'week', 'day', 'hour', 'minute', 'second'],
    	format: 'DHMS',
    	alwaysExpire: true,
    	serverSync: function() {
				$.ajax({
					url: 'ajax/sync_server_time.php'
				}).done(function(result){
					result = JSON.parse(result);
					return new Date(result.timestamp.milliseconds) || new Date();
				}).fail(function(){
					return new Date();
				});
			}
		};
		startOptionsG = $.extend({}, defaultOptionsG, {
			until: new Date(liveSessionStartGlobal),
			//until: new Date('2015-01-12 10:39:00'),
			layout:'<div class="panel panel-primary">'+
					'	<div class="panel-heading" style="font-size:14px;">Global Classes</div>'+
					'	<div class="panel-body">'+
					'		<div class="row">'+
					'			<div class="col-sm-6 col-md-4 col-lg-4"><div class="well padding-10 ht-150"><div style="font-size: 20px;"><strong>' + liveSessionTitleGlobal + '</strong><br>' +
    							liveSessionReadableGlobal+'<br>'+
							'<strong>in</strong> {dn} {dl} {hn}{sep}{mnn}{sep}{snn}</div></div></div></div></div></div>',
			alwaysExpire: true,
			onExpiry: function(){
				$(this).countdown('destroy');
				LiveSesionGClass.html(
					'<div class="panel panel-primary">'+
					'	<div class="panel-heading">Global Classes</div>'+
					'	<div class="panel-body">'+
					'		<div class="row">'+
					'			<div class="col-sm-6 col-md-4 col-lg-4">'+
					'				<div class="well padding-10 ht-150">'+
					'					<h1 class="no-margin" >'+
					'					<strong>'+liveSessionTitleGlobal+'</strong>'+
					'								</h1>'+
					'							<a role="button" class="btn btn-sm btn-success pull-right parpadea " '+liveSessionLinkGoblal+' style="background:#0bb908; position: absolute; right: 10px; bottom: 10px;font-size:14px;" target="_blank">Join Class</a>'+
					'<h4></h4>' +
					'				</div>'+
					'			</div>'+
					'		</div>'+
    				'	</div>'+
    				'</div>'
				);
				// Start another countdown before closing the session
    				LiveSesionGClass.find('h4').countdown(closeOptionsG);
			}
		});
		closeOptionsG = $.extend({}, defaultOptionsG, {
			until: new Date(liveSessionCloseGlobal),
			//until: new Date('2015-01-12 10:42:00'),
			layout: 'Class link closes in {hn}{sep}{mnn}{sep}{snn}',
			alwaysExpire: true,
			onExpiry: function(){
				$(this).countdown('destroy');
    			LiveSesionGClass.html('<h1 class="no-margin"></h1>');
			}
		});

		// Start the countdown
    	LiveSesionGClass.find('h1').countdown(startOptionsG);




	}

    /*if (liveSessionExistsLecture) {

    	// Setup panel start empty
        LiveSesionLecture.html('<h1 class="no-margin"></h1>');

		var defaultOptionsL, startOptionsL, closeOptionsL;
		defaultOptionsL = {
			labels: ['years', 'months', 'weeks', 'days', 'gours', 'minutes', 'seconds'],
    	labels1: ['year', 'month', 'week', 'day', 'hour', 'minute', 'second'],
    	format: 'DHMS',
    	alwaysExpire: true,
    	serverSync: function() {
				$.ajax({
					url: 'ajax/sync_server_time.php'
				}).done(function(result){
					result = JSON.parse(result);
					return new Date(result.timestamp.milliseconds) || new Date();
				}).fail(function(){
					return new Date();
				});
			}
		};
		startOptionsL = $.extend({}, defaultOptionsL, {
			until: new Date(liveSessionStartLecture),
			//until: new Date('2015-01-12 10:39:00'),
			layout: '<div class="col-sm-6 col-md-4 col-lg-4"><div class="well padding-10 ht-150"><div style="font-size: 20px;"><strong>'+liveSessionTitleLecture+'</strong><br>'+
    							liveSessionReadableLecture+'<br>'+
							'<strong>in</strong> {dn} {dl} {hn}{sep}{mnn}{sep}{snn}</div></div></div>',
			alwaysExpire: true,
			onExpiry: function(){
				$(this).countdown('destroy');
    			LiveSesionLecture.html(
					'<div class="col-sm-6 col-md-4 col-lg-4">'+
					'<div class="well padding-10 ht-150">'+
					'<h1 class="no-margin" >'+
						'<strong>'+liveSessionTitleLecture+'</strong>'+
					'</h1>'+
					'<a role="button" class="btn btn-sm btn-success pull-right parpadea " '+liveSessionLinkLecture+' style="background:#0bb908; position: absolute; right: 10px; bottom: 10px;font-size:14px;" target="_blank">Join Class</a>'+
					'<h4></h4>' +
					'</div>'+
					'</div>'
				);
				// Start another countdown before closing the session
        		LiveSesionLecture.find('h4').countdown(closeOptionsL);
			}
		});
		closeOptionsL = $.extend({}, defaultOptionsL, {
			until: new Date(liveSessionCloseLecture),
			//until: new Date('2015-01-12 10:42:00'),
			layout: 'Class link closes in {hn}{sep}{mnn}{sep}{snn}',
			alwaysExpire: true,
			onExpiry: function(){
				$(this).countdown('destroy');
        			LiveSesionLecture.html('<h1 class="no-margin"></h1>');
			}
		});

		// Start the countdown
        	LiveSesionLecture.find('h1').countdown(startOptionsL);




	}
	*/
	/*
    if (liveSessionExistsReview) {

    	// Setup panel start empty
        LiveSesionReview.html('<h1 class="no-margin"></h1>');

		var defaultOptionsR, startOptionsR, closeOptionsR;
		defaultOptionsR = {
			labels: ['years', 'months', 'weeks', 'days', 'gours', 'minutes', 'seconds'],
    	labels1: ['year', 'month', 'week', 'day', 'hour', 'minute', 'second'],
    	format: 'DHMS',
    	alwaysExpire: true,
    	serverSync: function() {
				$.ajax({
					url: 'ajax/sync_server_time.php'
				}).done(function(result){
					result = JSON.parse(result);
					return new Date(result.timestamp.milliseconds) || new Date();
				}).fail(function(){
					return new Date();
				});
			}
		};
		startOptionsR = $.extend({}, defaultOptionsR, {
			until: new Date(liveSessionStartReview),
			//until: new Date('2015-01-12 10:39:00'),
			layout: '<div class="col-sm-6 col-md-4 col-lg-4"><div class="well padding-10 ht-150"><div style="font-size: 20px;"><strong>'+liveSessionTitleReview+'</strong><br>'+
    							liveSessionReadableReview+'<br>'+
							'<strong>in</strong> {dn} {dl} {hn}{sep}{mnn}{sep}{snn}</div></div></div>',
			alwaysExpire: true,
			onExpiry: function(){
				$(this).countdown('destroy');
    			LiveSesionReview.html(
					'<div class="col-sm-6 col-md-4 col-lg-4">'+
					'<div class="well padding-10 ht-150">'+
					'<h1 class="no-margin" >'+
						'<strong>'+liveSessionTitleReview+'</strong>'+
					'</h1>'+
					'<a role="button" class="btn btn-sm btn-success pull-right parpadea " '+liveSessionLinkReview+' style="background:#0bb908; position: absolute; right: 10px; bottom: 10px;font-size:14px;" target="_blank">Join Class</a>'+
					'<h4></h4>' +
					'</div>'+
					'</div>'
				);
				// Start another countdown before closing the session
        		LiveSesionReview.find('h4').countdown(closeOptionsR);
			}
		});
		closeOptionsR = $.extend({}, defaultOptionsR, {
			until: new Date(liveSessionCloseReview),
			//until: new Date('2015-01-12 10:42:00'),
			layout: 'Class link closes in {hn}{sep}{mnn}{sep}{snn}',
			alwaysExpire: true,
			onExpiry: function(){
				$(this).countdown('destroy');
        			LiveSesionReview.html('<h1 class="no-margin"></h1>');
			}
		});

		// Start the countdown
        	LiveSesionReview.find('h1').countdown(startOptionsR);




	}
	*/

	// Assignment Deadline Panel
	var weekDeadline, submitDate;
	weekDeadline = <?php GetWeekAssignDeadline($fl_alumno); ?>;
	submitDate = weekDeadline.deadline.submit_date;
	$('#assignment-deadline').append(
		'<h1 class="no-margin">'+
			'<strong>Assignment(s) due:</strong><br>'+
			submitDate+
		'</h1>'
	);

	// Next Week's Panel
	var nextClassPanel, nextClass, nextWeek, nextLesson, nextDate;
	nextClassPanel = $("#next-class");
	nextClass = <?php GetNextClass($fl_alumno); ?>;

	nextWeek = nextClass.lesson.week || "";
	nextLesson = nextClass.lesson.title || "";
	nextDate = nextClass.lesson.date || "";

	if(nextClass.lesson.week > 0){
		nextClassPanel.append(
			'<h1 class="no-margin">'+
				'<strong>Next Topic is Week '+nextWeek+':</strong><br>'+
				'"'+nextLesson+'"'+
				'<strong> on </strong><br>'+nextDate+
			'</h1>'
		);
	} else {
		nextClassPanel.append(
			'<h1 class="no-margin">'+
				'<strong>Next Topic Is Unavailable</strong>'+
			'</h1>'
		);
	}

	// End 4 top panels

	// Chart colors default
	var $chrt_border_color = "#efefef";
	var $chrt_grid_color = "#DDD";
	var $chrt_main = "#E24913";			/* red       */
	var $chrt_second = "#6595b4";		/* blue      */
	var $chrt_third = "#FF9F01";		/* orange    */
	var $chrt_fourth = "#7e9d3a";		/* green     */
	var $chrt_fifth = "#BD362F";		/* dark red  */
	var $chrt_mono = "#000";

	// Load Flot Bar Chart Dependencies
	loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/flot/jquery.flot.cust.js", loadFlotResize);
	function loadFlotResize() {	loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/flot/jquery.flot.resize.js", loadFlotOrderBar);	}
	function loadFlotOrderBar() {	loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/flot/jquery.flot.orderBar.js", loadFlotToolTip); }	
	function loadFlotToolTip() { loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/flot/jquery.flot.tooltip.js", generateFlotBarChart);	}
	// Flot Bar Chart
	function generateFlotBarChart(){
		if($("#bar-chart").length) {
			var grades, GPA, xTicks, mark, ds;
			grades = <?php GetGradePointAverage($fl_alumno); ?>;

			GPA = [];
			xTicks = [];

			for(var i=1; i <= grades.size.total; i++){
				mark = parseInt(grades.GPA[i]);
				GPA.push([i, mark]);
				xTicks.push(i);
			}

			ds = [{
				data : GPA,
				bars : {
					show : true,
					barWidth : 0.5,
					align: "center"
				}
			}];

			// Display graph
			$.plot($("#bar-chart"), ds, {
				xaxis : {
					ticks: xTicks
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

		// Labels may be added later
		/*var xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>").text("Week").appendTo($('#bar-chart'));

		var yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>").text("Grade in Percentage").appendTo($('#bar-chart'));
		yaxisLabel.css("margin-top", yaxisLabel.width() / 2 - 20);*/

	}
	// End Flot Bar Chart

	// Progress Bars
	var requirements, total, total_uploaded, A, AR, S, SR, A_uploaded, AR_uploaded, S_uploaded, SR_uploaded;
	requirements = <?php GetAssignProgress($fl_alumno); ?>;

	total = requirements.size.total;
	total_uploaded = requirements.size.total_uploaded;

	A = requirements.total.A;
	AR = requirements.total.AR;
	S = requirements.total.S;
	SR = requirements.total.SR;
	A_uploaded = requirements.uploaded.A;
	AR_uploaded = requirements.uploaded.AR;
	S_uploaded = requirements.uploaded.S;
	SR_uploaded = requirements.uploaded.SR;

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
	var charts, name, percent, term, currentWeek, maxWeek, grades;
	charts =<?php GetPieCharts($fl_alumno);?>

	// Program progress
	name = charts.program.name;
	percent = charts.program.percent;
	$("#program-title").append("Program Progress for \""+name+"\"");
	$("#program-percent").data("easyPieChart").update(percent);	

	// Week progress
	term = charts.week.term;
	currentWeek = charts.week.current_week;
	maxWeek = charts.week.max_week;
	percent = charts.week.percent;
	$("#term-title").append("You are on Term "+term+" in Week "+currentWeek+" with a total of "+maxWeek+" week(s)");
	$("#term-percent").data("easyPieChart").update(percent);

	// GPA progress
	percent = charts.GPA.percent;
	$("#GPA-title").append("GPA");
	$("#GPA-percent").data("easyPieChart").update(percent);

	// To be implemented: Time spent on campus
	
	// End Pie Charts
    
</script>