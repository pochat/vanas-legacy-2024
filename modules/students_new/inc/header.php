<!DOCTYPE html>
<!--<html lang="en-us">-->
<html xmlns='https://www.w3.org/1999/xhtml'>
	<head>
    <!--Lo secomente para el tamaño del aimagen en facebook-->
		<meta charset="utf-8" >    
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title> Vancouver Animation School Online Campus </title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel='shortcut icon' href='https://vanas.ca/templates/jm-me/favicon.ico'>
		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/bootstrap.min.css" >
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/font-awesome.min.css">
		<!-- SmartAdmin Style -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/smartadmin-production.css">
		<!-- Vanas Style -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/vanas.css">
    <?php
    if(VIDEOS_FLASH==true){
      echo '
      <!-- Flowplayer -->
      <link rel="stylesheet" type="text/css" media="screen" href="'.PATH_N_COM_CSS.'/flowplayer/playful.css">';
    }
    else{
      echo '
      <!-- player skin -->
      <link rel="stylesheet" href="'.PATH_SELF_JS.'/flowplayer/skin/skin.css">
      <style>
      .flowplayer {
         width: 720;
         height: 480;
      }
      .flowplayer {      
        background-image: url("'.SP_IMAGES.'/PosterFrame_White.jpg");
      }
      </style>';
    }

			if (!empty($page_css)) {
				foreach ($page_css as $css) {
					echo '<link rel="stylesheet" type="text/css" media="screen" href="'.PATH_N_COM_CSS.'/'.$css.'">';
				}
			}
		?>
		<!-- GOOGLE FONT -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
		<!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- FACEBOOK JS -->
		<!--<script src="<?php echo PATH_N_COM_JS; ?>/facebook.js.php"></script>-->
		
		<!--script publicidad-->
		<script src="//36ce5f316c12438cb40996edbdfb293b.js.ubembed.com" async></script>
		
		
		<!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-MLCQWKN');</script>
		<!-- End Google Tag Manager -->
	
		
		
	</head>
	<body 
		<?php
			if (!empty($page_body_prop)) {
				foreach ($page_body_prop as $prop_name => $value) {
					echo $prop_name.'="'.$value.'" ';
				}
			}
		?>
	>
	  
 	<!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MLCQWKN" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

	
	
	<!-- POSSIBLE CLASSES: minified, fixed-ribbon, fixed-header, fixed-width-->
	<?php
	    $no_main_header=null;
		$no_messages=null;
		if (!$no_main_header) {
	?>
  <div id="loading_vanas" class="superbox-show" style="display: none; z-index:5000; position:absolute; background-color: #fff; opacity:0.5; width:100%; height:100%;">
    <img src="../../images/<?php echo ObtenNombreImagen(300); ?>" class="superbox-current-img" style="position:absolute; left:50%; top:30%; width:150px;" />
  </div>
	<header id="vanas-header">
		<div id="logo-group" class="pull-left">

			<!-- LOGO -->
			<span id="logo"><img src="<?php echo PATH_N_COM_IMAGES; ?>/logo.jpg" alt="Vanas Logo"></span>

			<?php
				# Count number of unread messages
				$row = RecuperaValor("SELECT COUNT(1) FROM k_mensaje_directo WHERE fl_usuario_dest=$fl_usuario AND fg_leido='0'");
				if($row[0] > 0)
					$no_messages = $row[0];

				# Count number of new streams
				$rs = EjecutaQuery("SELECT fl_tema FROM c_f_tema ORDER BY no_orden");
				$no_streams = 0;
				while($row = RecuperaRegistro($rs)){
					$fl_tema = $row[0];
					$rs2 = EjecutaQuery("SELECT no_posts FROM k_f_usu_tema WHERE fl_usuario = $fl_usuario AND fl_tema = $fl_tema");
					$row2 = RecuperaRegistro($rs2);
                    if(!empty($row2[0])){
                        $no_post=!empty($row[0])?$row[0]:0;
                        $no_streams += $no_post;
                    }
				}

				# Count number of new notifications
				$fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
				$Query  = "SELECT COUNT(1) FROM k_not_blog a, c_blog b ";
		    $Query .= "WHERE a.fl_blog=b.fl_blog ";
		    $Query .= "AND b.fe_blog <= $fe_actual ";
		    $Query .= "AND a.fl_usuario=$fl_usuario";
		    $row = RecuperaValor($Query);
		    if($row[0] > 0){
		    	$no_news = $row[0];
		    }

			#Recuperamos las solicitudes de amistad.
			$Query="SELECT COUNT(*) FROM k_relacion_usuarios WHERE fl_usuario_destinatario=".$fl_usuario." AND fg_aceptado<>'1' ";
			$ror=RecuperaValor($Query);
			$no_solitudes_pendientes=$ror[0];
			
            
            #Recupermaos las soslicitudes a mistas aceptadas.
            $Querya="SELECT COUNT(*) FROM k_relacion_usuarios WHERE fl_usuario_origen=$fl_usuario AND fg_aceptado='1' AND fg_revisado_alumno='0'  ";
            $rou=RecuperaValor($Querya);
            $no_solicitudes_acptadas=$rou[0];
			
			
			
			
		    $no_notices = $no_streams + $no_news + $no_solicitudes_acptadas + $no_solitudes_pendientes ;
		    $no_total = $no_messages + $no_notices + $no_solicitudes_acptadas + $no_solitudes_pendientes ;
			?>

			<span id="activity" class="activity-dropdown pull-left"> <i class="fa fa-user"></i> <b class="badge" id="tot_notity_<?php echo $fl_usuario;?>"><?php echo $no_total; ?></b> </span>

			<div class="ajax-dropdown">
				<div class="btn-group btn-group-justified" data-toggle="buttons">
					<label class="btn btn-default">
						<input type="radio" name="activity" id="<?php echo PATH_N_ALU_PAGES; ?>/notify/messages.php">
						Messages <span class='notice-count'><?php if(!empty($no_messages)){echo "(<span id='no_messages'>".$no_messages."</span>)";} ?></span>
					</label>
					<label class="btn btn-default active">
						<input type="radio" name="activity" id="<?php echo PATH_N_ALU_PAGES; ?>/notify/notifications.php">
						Notices <span class='notice-count'><?php 
						//if(!empty($no_notices)){
							echo "(<span id='no_notices'>".$no_notices."</span>)";
						//	} 
						?>
						</span>
					</label>
					<label class="btn btn-default">
						<input type="radio" name="activity" id="<?php echo PATH_N_ALU_PAGES; ?>/notify/progress.php">
						Progress 
					</label>
				</div>
				<div class="ajax-notifications custom-scroll"></div>
			</div>

		</div>

		<div class="pull-right">
			<div id="logout" class="btn-header pull-right">
				<span> <a href="<?php echo PAGINA_SALIR; ?>" title="Sign Out" data-logout-msg="Good Bye!"><i class="fa fa-sign-out"></i></a> </span>
			</div>

			<div id="clock" class="btn-header pull-right">
				<span> <a href="#clock-container" data-toggle="collapse"><i class="fa fa-clock-o"></i> </a> </span>
			</div>

			<div id="profile" class="btn-header pull-right">
				<span> <a href="#profile-container" data-toggle="collapse"> <i class="fa fa-user"></i> </a> </span>
			</div>

      <div id="session_cg" class="hidden-mobile btn-header pull-right">
        <div id="logo-group" class="pull-left">
				<span id="activity" class="activity-dropdown pull-left" data-toggle="collapse"> 
          <a href="#session-container" data-toggle="collapse"> 
            <i class="fa fa-desktop"></i>
            <?php
            # Si la clase empieza en 3 horas indicara la alerta
            # Diferencia de horas
            $diferencia = RecuperaDiferenciaGMT( );
            # Horas antes para mostrar la alerta
            $hours_alert = ObtenConfiguracion(95);
            # Query para mostrar la notificacion
			$Query ="(";
            $Query .= "SELECT ";
            $Query .= "'".ObtenFechaActual()."' ";
            $Query .= "BETWEEN ";
            $Query .= "SUBDATE(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), INTERVAL $hours_alert HOUR) ";
            $Query .= "AND DATE_ADD(fe_clase, INTERVAL $diferencia HOUR) ";
            $Query .= "FROM k_alumno_grupo a ";
            $Query .= "LEFT JOIN k_clase b ON(b.fl_grupo=b.fl_grupo) ";
            $Query .= "WHERE DATE_FORMAT(fe_clase, '%Y-%m-%d') =  DATE_FORMAT(CURDATE(), '%Y-%m-%d') ";
            $Query .= "AND a.fl_alumno=$fl_usuario ";
			$Query .=")UNION(";
            $Query .= "SELECT ";
            $Query .= "'".ObtenFechaActual()."' ";
            $Query .= "BETWEEN ";
            $Query .= "SUBDATE(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), INTERVAL $hours_alert HOUR) ";
            $Query .= "AND DATE_ADD(fe_clase, INTERVAL $diferencia HOUR) ";
            $Query .= "FROM k_alumno_grupo a ";
            $Query .= "LEFT JOIN k_clase_grupo b ON(b.fl_grupo=b.fl_grupo) ";
            $Query .= "WHERE DATE_FORMAT(fe_clase, '%Y-%m-%d') =  DATE_FORMAT(CURDATE(), '%Y-%m-%d') ";
            $Query .= "AND a.fl_alumno=$fl_usuario ";
            $Query .=")";
            $row = RecuperaValor($Query);
            $rs = EjecutaQuery($Query);
            $no_sessiones = CuentaRegistros($rs);
            $session = $row[0];
            if($session){
              echo "              
              <b class='badge bg-color-red bounceIn animated'>$no_sessiones</b>";
            }
            ?>
          </a> 
        </span>
			</div>
      </div>
      
      <div id="session_cg-g" class="hidden-mobile btn-header pull-right">
        <div id="logo-group" class="pull-left">
				<span id="activity" class="activity-dropdown pull-left" data-toggle="collapse">
          <a href="#session-global" data-toggle="collapse" id="globales">
            <i class="fa fa-globe"></i>
            <?php
            # Si la clase empieza en 3 horas indicara la alerta
            # Diferencia de horas
            $diferencia = RecuperaDiferenciaGMT( );
            # Horas antes para mostrar la alerta
            $hours_alert = ObtenConfiguracion(95);
            # Query para mostrar la notificacion
			$Query ="( ";
            $Query .= "SELECT ";
            $Query .= "'".ObtenFechaActual()."' ";
            $Query .= "BETWEEN ";
            $Query .= "SUBDATE(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), INTERVAL $hours_alert HOUR) ";
            $Query .= "AND DATE_ADD(fe_clase, INTERVAL $diferencia HOUR) ";
            $Query .= "FROM k_alumno_cg kacg ";
            $Query .= "LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=kcg.fl_clase_global) ";
            $Query .= "WHERE DATE_FORMAT(fe_clase, '%Y-%m-%d') =  DATE_FORMAT(CURDATE(), '%Y-%m-%d') ";
            $Query .= "AND kacg.fl_usuario=$fl_usuario ";
			$Query .=")UNION(";
            $Query .= "SELECT ";
            $Query .= "'".ObtenFechaActual()."' ";
            $Query .= "BETWEEN ";
            $Query .= "SUBDATE(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), INTERVAL $hours_alert HOUR) ";
            $Query .= "AND DATE_ADD(fe_clase, INTERVAL $diferencia HOUR) ";
            $Query .= "FROM k_alumno_grupo kacg ";
            $Query .= "LEFT JOIN k_clase_grupo kcg ON(kcg.fl_grupo=kacg.fl_grupo) ";
            $Query .= "WHERE DATE_FORMAT(fe_clase, '%Y-%m-%d') =  DATE_FORMAT(CURDATE(), '%Y-%m-%d') ";
            $Query .= "AND kacg.fl_alumno=$fl_usuario ";
            $Query .=")";
            $row = RecuperaValor($Query);
            $class_alert = $row[0];
            if($class_alert){
              echo "              
              <b class='badge bg-color-red bounceIn animated'>1</b>";

            }
            ?>
          </a>
            <?php 
            #Temporal

            $Query="
                     SELECT distinct kcg.fl_clase_cg
                    FROM k_alumno_cg kacg 
                    LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=kcg.fl_clase_global) 
                    WHERE DATE_FORMAT(fe_clase, '%Y-%m-%d') >=  DATE_FORMAT(CURDATE(), '%Y-%m-%d') 
                    
                    AND kacg.fl_usuario=$fl_usuario;
            ";
            $row=RecuperaValor($Query);
            $fl_clase=$row[0];
            $Query="SELECT fl_clase_global,ds_clase,DATE_FORMAT(hr_session,'%H:%m') FROM clases_globales WHERE fl_clase_cg=$fl_clase ";
            $row=RecuperaValor($Query);
            $fl_clase_globval=$row[0];
            $ds_titutlo_clase=$row[1];
            $hr_session=$row[2];
            $Query="SELECT fg_zoom FROM c_clase_global WHERE fl_clase_global=$fl_clase_globval ";
            $row=RecuperaValor($Query);
            $fg_zoom=$row['fg_zoom'];

            $Query  = "SELECT fl_live_session_cg, cl_estatus, ds_meeting_id, ds_password_asistente,zoom_url,zoom_id ";
            $Query .= "FROM k_live_sesion_cg ";
            $Query .= "WHERE fl_clase_cg=".$fl_clase ;	  
            $row = RecuperaValor($Query);
            $zoom_url=$row[4];

            
            ?>
            
        </span>
                   
        </div>
			</div>
      <div class="hidden" style="float: left;padding: 10px;"><a href='<?php echo "../liveclass/LiveSession_gc.php?folio=$fl_clase";?>' title='Join Live Classroom' target='_blank'>Join live classroom at <?php echo $hr_session." PST <br>";?>Topic: <?php echo $ds_titutlo_clase;?></a></div>
      <div id="session-global" class="collapse container">
        <h1 class="no-margin">
          <strong><?php echo ObtenEtiqueta(1015); ?></strong>
        </h1>
      </div>
		</div>

		<div id="clock-container" class="collapse container"></div>
		<div id="profile-container" class="collapse container"></div>
		<div id="session-container" class="collapse container">
			<h1 class="no-margin">
				<strong><?php echo ObtenEtiqueta(1014); ?></strong>
			</h1>
		</div>

		<script type="text/javascript">
			window.onload = function() {

				var fl_user, ds_name;
				fl_user = <?php echo json_encode($fl_usuario); ?>;
				ds_name = <?php echo json_encode(ObtenNombreUsuario($fl_usuario)); ?>;

				socket.emit('add-user', {"fl_user": fl_user, "ds_name": ds_name});

				// Setup clock container
				var container, todayDate, localTime, localReadableDate, serverTime, serverReadableDate, layout;
				container = $('#clock-container');
				todayDate = <?php GetDateAndTime(); ?>;
				
				localTime = todayDate.local.milliseconds;
				localReadableDate = todayDate.local.readable;
				serverTime = todayDate.server.milliseconds;
				serverReadableDate = todayDate.server.readable;

				layout = 
					'<h6>Server:</h6>'+
					'<h5>'+serverReadableDate+'</h5>'+
					'<h5 id="serverclock"></h5>'+
					'<h6>Local:</h6>'+
					'<h5>'+localReadableDate+'</h5>'+
					'<h5 id="localclock"></h5>';

				container.append(layout);
        // Muestra la hora del servidor y el profesor
        var servetTime = new Date(serverTime);
        var servetLocal = new Date(localTime);
        // Muestra las horas
        $("#serverclock").clock({"timestamp":servetTime, "calendar":"false"});
        $("#localclock").clock({"timestamp":servetLocal, "calendar":"false"});
				
				// Program Info
				var student, panel;
				student = <?php GetStudentProfile($fl_usuario); ?>;
				panel = 
					'<h6>Program: '+student.profile.course+'</h6>'+
					'<h6>Term: </h6><h5>'+student.profile.term+'</h5>'+
					'<h6>Week: </h6><h5>'+student.profile.week+'</h5>'+
					'<h6>Lesson: </h6><h5>'+student.profile.lesson+'</h5>'+
					'<h6>Status: </h6><h5>'+student.profile.status+'</h5>';
				document.getElementById('profile-container').innerHTML = panel;
				
				var student, liveSessionPanel, liveSessionExists, liveSessionLink, liveSessionStart, liveSessionClose, liveSessionReadable;
				student = <?php GetStudentSession($fl_usuario); ?>;

				liveSessionPanel = $('#session-container');
				liveSessionExists = student.session.exists;
				liveSessionLink = student.session.link;
				liveSessionStart = student.session.start.milliseconds;
				liveSessionClose = student.session.close.milliseconds;
				liveSessionReadable = student.session.readable;

				// If there is a live class
				if(liveSessionExists){
					// Setup panel
					liveSessionPanel.html(
						'<h1 class="no-margin"></h1>'+
						'<a role="button" class="btn btn-sm btn-default pull-right disabled" style="position: absolute; right: 10px; bottom: 10px;">Join Class</a>'
					);
					// Setup timer options
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
						layout: '<strong>Next Live Class:</strong><br>'+
										liveSessionReadable+'<br>'+
										'<strong>in</strong> {dn} {dl} {hn}{sep}{mnn}{sep}{snn}'+
										'<br><br>',
						alwaysExpire: true,
						onExpiry: function(){
							$(this).countdown('destroy');
							liveSessionPanel.html(
								'<h1 class="no-margin">'+
									'<strong>You have a Live Class Right Now!</strong>'+
								'</h1>'+
								'<a role="button" class="btn btn-sm btn-primary pull-right" '+liveSessionLink+' style="position: absolute; right: 10px; bottom: 10px;" target="_blank">Join Class</a>'+
								'<h4></h4>'
							);
							// Start another countdown before closing the session
							liveSessionPanel.find('h4').countdown(closeOptions);
						}
					});
					closeOptions = $.extend({}, defaultOptions, {
						until: new Date(liveSessionClose),
						layout: 'Class link closes in {hn}{sep}{mnn}{sep}{snn}'+
										'<br><br><br>',
						alwaysExpire: true,
						onExpiry: function(){
							$(this).countdown('destroy');
							liveSessionPanel.html(
								'<h1 class="no-margin">'+
			  					'<strong>Live Class link is Closed!</strong><br>'+
			  					'<h4>Please update the page by pressing F5 or <a style="font-weight:500;" onclick="window.location.reload();">here</a></h4>'+
			  				'</h1>'+
			  				'<br><br>'+
			  				'<a role="button" class="btn btn-sm btn-default pull-right disabled" style="position: absolute; right: 10px; bottom: 10px;">Join Class</a>'
		  				);
						}
					});

					// Start the countdown
					liveSessionPanel.find('h1').countdown(startOptions);
          
          // Add alert class
          // $('#alert_class').after('<b class="badge" style="position:absolute;top:5px;background:#a90329 !important;margin-left:-8px">1</b>');
				}
        
        // Clases Globales JGFL
        var student_cg, liveSessionPanel_cg, liveSessionExists_cg, liveSessionLink_cg, liveSessionStart_cg, liveSessionClose_cg, liveSessionReadable_cg;
        student_cg = <?php GetStudentSessionGC($fl_usuario); ?>;
        
        liveSessionPanel_cg = $('#session-global');
        liveSessionExists_cg = student_cg.session.exists;
        liveSessionLink_cg = student_cg.session.link;
        liveSessionStart_cg = student_cg.session.start.milliseconds;
        liveSessionClose_cg = student_cg.session.close.milliseconds;
        liveSessionReadable_cg = student_cg.session.readable;
        liveSessionTitleClase = student_cg.session.titleclass;
        liveSessionTitle = student_cg.session.title
        
        
        
        // Next class global
        var student_cg_next, liveSessionPanel_cg_next, liveSessionExists_cg_next, liveSessionLink_cg_next, liveSessionStart_cg_next,
        liveSessionClose_cg_next, liveSessionReadable_cg_next, existe="";
        student_cg_next = <?php GetStudentSessionGC_NEXT($fl_usuario,1); ?>;
        
        liveSessionPanel_cg_next = $('#session-global');
        liveSessionExists_cg_next = student_cg_next.session_next.exists_next;
        liveSessionLink_cg_next = student_cg_next.session_next.link_next;
        liveSessionStart_cg_next = student_cg_next.session_next.start_next.milliseconds_next;
        liveSessionClose_cg_next = student_cg_next.session_next.close_next.milliseconds_next;
        liveSessionReadable_cg_next = student_cg_next.session_next.readable_next;
        liveSessionTitleClase_next = student_cg_next.session_next.titleclass_next;
        liveSessionTitle_next = student_cg_next.session_next.title_next;
        
        if(liveSessionExists_cg_next){
          existe = '<?php echo ObtenEtiqueta(1016); ?>:</strong><br>'+
                      liveSessionTitleClase_next+'<br>'+
                      liveSessionTitle_next+'<br>'+
                      liveSessionReadable_cg_next+'<br>';
        }

        // alert(liveSessionTitle_next);
        if(liveSessionExists_cg){
          var defaultOptionsGC, startOptionsGC, closeOptionsGC, startOptionsGC_next;
          defaultOptionsGC = {
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
          // clase global
          startOptionsGC = $.extend({}, defaultOptionsGC, {
              until: new Date(liveSessionStart_cg),
              layout: 
                '<strong><?php echo ObtenEtiqueta(1016); ?>:</strong><br>'+
                liveSessionTitleClase+'<br>'+
                liveSessionTitle+'<br>'+
                liveSessionReadable_cg+'<br>'+
                '<strong>in</strong> {dn} {dl} {hn}{sep}{mnn}{sep}{snn}'+
                '<br><br>',
              alwaysExpire: true,
              onExpiry: function(){
                $(this).countdown('destroy');                
                $('#session_cg-g').append(
                  '<span id="activity_cg" class="activity-dropdown pull-left" style="padding-top: 10px; padding-left:8px;"> '+
                    '<a role="button" class="btn btn-success pull-right padding-5" rel="tooltip" data-placement="bottom" data-html="true" '+liveSessionLink_cg+' target="_blank"><i class="fa fa-globe" ></i> '+
                    '<strong><?php echo ObtenEtiqueta(1017);?></strong></a>'+
                  '</span>'
                );
                // Start another countdown before closing the session
                liveSessionPanel_cg.find('h1').countdown(closeOptionsGC);                
              }
          });
          // Cierra la clase actual
          closeOptionsGC = $.extend({}, defaultOptionsGC, {
						until: new Date(liveSessionClose_cg),
						layout: 'Class link closes in {hn}{sep}{mnn}{sep}{snn}'+
										'<hr><strong>'+existe+
                      '<br><br>',
						alwaysExpire: true,
						onExpiry: function(){
							$(this).countdown('destroy');
              // Clear botton
              $('#activity_cg').empty();
              // Review Next Class
              liveSessionPanel_cg.html(
              '<h1><strong>'+existe);
						}
					});
          // Start the countdown
          liveSessionPanel_cg.find('h1').countdown(startOptionsGC);        
        };
      };
          
    </script>
    
    <!-- Script para la conexion a  Facebook-->
    <script>
    // Si esta activado las redes entonces funcionfacebook
    var networks = <?php echo ObtenConfiguracion(78); ?>;
    if(networks == 1){
    // This is called with the results from from FB.getLoginStatus().
    function statusChangeCallback(response) {
      console.log(response);
      if (response.status === 'connected') {
          // Logged into your app and Facebook.        
          $('#conected_facebook').css('display','none');
          $('#desconected_facebook').css('display','inline');          
          API();
          $('input[name=facebookst').val("connected");
      } else if (response.status === 'not_authorized') {
          // The person is logged into Facebook, but not your app.
          $('#conected_facebook').css('display','inline');
          // FB.api('/me', function(response) {
            // document.getElementById('status_face').innerHTML =
            // 'estas logeado pero no con esta app';
          // });
          $('input[name=facebookst').val("unknown");
          update_facebook();
      } else {
          // The person is not logged into Facebook, so we're not sure if
          // they are logged into this app or not.
          $('#conected_facebook').css('display','inline');
          $('#desconected_facebook').css('display','none');
          FB.api('/me', function(response) {
            document.getElementById('status_face').innerHTML =
            "<strong><?php echo ObtenEtiqueta(787);?></strong>";
          });
          $('input[name=facebookst').val("unknown");
          update_facebook();
      }
    }       
    
    window.fbAsyncInit = function() {
      FB.init({
        appId      : '<?php echo ObtenConfiguracion(76); ?>',
        app_secret : '<?php echo ObtenConfiguracion(85); ?>',
        status     : true,
        cookie     : true,  // enable cookies to allow the server to access the session
        xfbml      : true,  // parse social plugins on this page
        version    : 'v2.2' // use version 2.2
      });
      FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
        if(response.status=="connected")
          $('input[name=facebookst').val("connected");
        else
          $('input[name=facebookst').val("unknown");
      },true);
    };

    // Load the SDK asynchronously
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    
    // Here we run a very simple test of the Graph API after login is
    function API() {
      // Save Perfil user
      FB.api('/me', function(response) {
        var fl_facebook = response.id;
        var name = response.name;
        // Guardamos el id del perfil del usuario
        user_share_face_save(fl_facebook, "PF",'', name);        
        document.getElementById('status_face').innerHTML =
        "<strong><?php echo ObtenEtiqueta(786); ?>:</strong><br />"+response.name;
      });
      // Save Pages Fans User
      FB.api('/me/accounts', function (accountsResult) {            
        if (accountsResult != null && accountsResult.data.length != 0) {
          var data = accountsResult['data'];
          if (data != null) {
            for (var i = 0; i < data.length; i++) {
              var fl_facebook_pg = data[i].id;
              var name = data[i].name;
              //Si es su face del usuario Guardamos las paginas que tiene el usuario
              user_share_face_save(fl_facebook_pg, "PG",'', name);             
            }
          }
        }
      });
    }
    
    // This function is called when someone finishes with the Login
    // Button.  See the onlogin handler attached to it in the sample
    // code below.
    function loginn(origen='') {
      FB.login(function(){
        FB.getLoginStatus(function(response) {
          statusChangeCallback(response);
          if(origen=='upload.php'){
            location.reload();
            API();
          }
        },true);
      },{scope: '<?php echo ObtenConfiguracion(86); ?>'});      
    }
   
    // Function Logout
    function logout(){
      FB.logout(function(response) {
        statusChangeCallback(response);
      });    
    }
  
    // Function Save User and AccessToken*/
    function user_share_face_save(id,type,entregable='',name){
      $.ajax({
        type: "POST",
        url: "<?php echo PATH_N_ALU_PAGES; ?>/savefaceuser.php",
        data: "id_face_user="+id+"&type="+type+"&entregable="+entregable+"&name="+name
      });
    }
    
    // Funcion ara publicar en las diferentes paginas
    function postear(page_id, comentario, link_url, picture_url, name, caption, description, entregable){
      FB.api('/' + page_id, {fields: 'access_token'}, function(resp) {
        if(resp.access_token){      
          FB.api('/' + page_id + '/feed','post',{
            access_token: resp.access_token,
            // message     : "It's awesome ...",
            message     : ""+comentario+"",
            // link        : 'http://csslight.com',
            link        : ''+link_url+'',
            // picture     : 'http://csslight.com/application/upload/WebsitePhoto/567-grafmiville.png',
            picture     : ''+picture_url+'',
            // name        : 'Featured of the Day',
            name        : ''+name+'',
            caption        : ''+caption+'',
            // description : 'CSS Light is a showcase for web design encouragement, submitted by web designers of all over the world. We simply accept the websites with high quality and professional touch.',
            description : ''+description+'',
            method: 'post'}
            ,function(response) {
              if(response.id != 0 || response.id != '')
                user_share_face_save(response.id,type='S',entregable);
              else
                alert(JSON.stringify(response));
              return;
            }
          );
        }
      });
    }
    // Funcio para publicar en el perfil del usuario
    function postear_perfil(page_id, comentario, link_url, picture_url, name, caption, description, entregable){
      FB.api('/' + page_id + '/feed','post',{
        message     : ""+comentario+"",
        link        : ''+link_url+'',
        picture     : ''+picture_url+'',
        name        : ''+name+'',
        caption        : ''+caption+'',        
        description : ''+description+'',
        method: 'post'}
      ,function(response) {
        // Guardamos la publicacion si fue exitosa en caso contrario eniara una advertencia
        if(response.id != 0 || response.id != '')
          user_share_face_save(response.id,type='S',entregable);
        else
          alert(JSON.stringify(response));
        return;
      });
    }
    
    // Actualiza los datos de usuario de facebook
    function update_facebook(){
      var update = true;
      $.ajax({
        type: "POST",
        url: "<?php echo PATH_N_ALU_PAGES; ?>/savefaceuser.php",
        data: "update="+update
      });
    }
    }
  
    
  function EnviaNotificacionSolicitudAmistad(fl_usuario_origen,fl_usuario_destino,nb_usuario_origen){
	
	     var fl_usuario_actual=<?php echo $fl_usuario;?>;
		 
		 //alert(fl_usuario_actual);
		 
		 if(fl_usuario_actual==fl_usuario_destino){
		 
				$.smallBox({
					title : "<b>"+nb_usuario_origen+" </b> ",
					content : "<?php echo ObtenEtiqueta(2189);?>",
					color : "#296191",
					//timeout: 8000,
					icon : "fa fa-user swing animated"
				});
		
			
		 
			
			var tot_notity=$("#tot_notity_"+fl_usuario_actual).text();//representa el primer circulo 
			$('#tot_notity_'+fl_usuario_actual).empty();//limpiamos el numerador
			var t = parseInt(tot_notity)+1;//obtenemos el nuevo total.
			$('#tot_notity_'+fl_usuario_actual).append(t);//Colocamos el nuevo total.
			
			var no_news = $('#no_notices').text();
			$('#no_notices').empty();
		    var n = parseInt(no_news)+1;
			$('#no_notices').append(n);
			
		   
		 
		 
		 }
	
	
	}
	
  
  	function EnviaNotificacionSolicitudAceptada(fl_usuario_confirma_solicitud,fl_usuario_destino,nb_usuario_confirma_solicitud){
		    var fl_usuario_actual=<?php echo $fl_usuario;?>;
			
			if(fl_usuario_actual==fl_usuario_destino){
		 
						//alert('fua');
						$.smallBox({
							title : "<b>"+nb_usuario_confirma_solicitud+" </b> ",
							content : "<?php echo ObtenEtiqueta(2193);?>",
							color : "#296191",
							//timeout: 8000,
							icon : "fa fa-user swing animated"
						});
						
						
						
						
						
						
						var tot_notity=$("#tot_notity_"+fl_usuario_actual).text();//representa el primer circulo 
						$('#tot_notity_'+fl_usuario_actual).empty();//limpiamos el numerador
						var t = parseInt(tot_notity)+1;//obtenemos el nuevo total.
						$('#tot_notity_'+fl_usuario_actual).append(t);//Colocamos el nuevo total.
						
						var no_news = $('#no_notices').text();
						$('#no_notices').empty();
						var n = parseInt(no_news)+1;
						$('#no_notices').append(n);
						
						
						
						
			}
		
		
		
		
	}
		
  
  </script>
	</header>
  <input type="hidden" id="facebookst" name="facebookst">
	<!-- END HEADER -->

	<?php
		}
	?>