<!DOCTYPE html>
<html lang="en-us">
	<head>
		<meta charset="utf-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->
		<title> Vancouver Animation School Online Campus </title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel='shortcut icon' href='https://vanas.ca/templates/jm-me/favicon.ico'>
		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/bootstrap.min.css">
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/font-awesome.min.css">
		<!-- SmartAdmin Style -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/smartadmin-production.css">

		<!-- Vanas Style -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/vanas.css">
		<!-- jQZoom -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_COM_CSS; ?>/jquery.jqzoom.css">
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
		if (empty($no_main_header)) {
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
				//if($row[0] > 0)
					$no_messages = !empty($row[0])?$row[0]:0;

				# Count number of new streams
				$rs = EjecutaQuery("SELECT fl_tema FROM c_f_tema ORDER BY no_orden");
				$no_streams = 0;
				while($row = RecuperaRegistro($rs)){
					$fl_tema = $row[0];
					$rs2 = EjecutaQuery("SELECT no_posts FROM k_f_usu_tema WHERE fl_usuario = $fl_usuario AND fl_tema = $fl_tema");
					$row2 = RecuperaRegistro($rs2);
					$no_streams += !empty($row2[0])?$row2[0]:0;
				}

				# Count number of new notifications
				$fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
				$Query  = "SELECT COUNT(1) FROM k_not_blog a, c_blog b ";
		    $Query .= "WHERE a.fl_blog=b.fl_blog ";
		    $Query .= "AND b.fe_blog <= $fe_actual ";
		    $Query .= "AND a.fl_usuario=$fl_usuario";
		    $row = RecuperaValor($Query);
		    //if($row[0] > 0){
		    	$no_news = !empty($row[0])?$row[0]:0;
		    //}

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
						<input type="radio" name="activity" id="<?php echo PATH_N_MAE_PAGES; ?>/notify/messages.php">
						Messages <span class='notice-count'><?php if(!empty($no_messages)){echo "(<span id='no_messages'>".$no_messages."</span>)";} ?></span>
					</label>
					<label class="btn btn-default active">
						<input type="radio" name="activity" id="<?php echo PATH_N_MAE_PAGES; ?>/notify/notifications.php">
						Notices <span class='notice-count'><?php 
						//if(!empty($no_notices)){
							echo "(<span id='no_notices'>".$no_notices."</span>)";
							//} 
							?>
							</span>
					</label>
					<!-- <label class="btn btn-default">
						<input type="radio" name="activity" id="<?php #echo PATH_N_MAE_PAGES; ?>/notify/progress.php">
						Progress 
					</label> -->
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
      
      <div id="session_cg" class="hidden-mobile btn-header pull-right">
        <div id="logo-group" class="pull-left">
				<span id="activity" class="activity-dropdown pull-left" data-toggle="collapse"> 
          <a href="#session-global" data-toggle="collapse" id="globales">
            <i class='fa fa-globe'></i>
            <?php
            # Si la clase empieza en 3 horas indicara la alerta
            # Diferencia de horas
            $diferencia = RecuperaDiferenciaGMT( );
            # Horas antes para mostrar la alerta
            $hours_alert = ObtenConfiguracion(95);
            # Query para mostrar la notificacion
            $Query  = "SELECT ";         
            $Query .= " '".ObtenFechaActual()."'";
            $Query .= " BETWEEN ";
            $Query .= "SUBDATE(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), INTERVAL $hours_alert HOUR)  ";
            $Query .= "AND DATE_ADD(fe_clase, INTERVAL $diferencia HOUR) ";
            $Query .= "FROM k_clase_cg WHERE fl_maestro=$fl_usuario ";
            $Query .= "AND DATE_FORMAT(fe_clase, '%Y-%m-%d') =  DATE_FORMAT(CURDATE(), '%Y-%m-%d') ";
            $row = RecuperaValor($Query);
            $class_alert = !empty($row[0])?$row[0]:NULL;
            if(isset($class_alert)){
              echo "              
              <b class='badge bg-color-red bounceIn animated'>1</b>";
            }
            ?>
          </a>
        </span> 
        </div>
			</div>
      <div id="session-global" class="collapse container">
        <h1 class="no-margin">
          <strong><?php echo ObtenEtiqueta(1015); ?></strong>
        </h1>
      </div>
		</div>

		<div id="clock-container" class="collapse container"></div>
		<!-- <div id="sessions-container" class="collapse container"></div> -->

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

        $("#serverclock").clock({"timestamp":servetTime, "calendar":"false"});
        $("#localclock").clock({"timestamp":servetLocal, "calendar":"false"});
        
				// Live sessions (TODO)
        
        // Obtenemos las clases que tiene el maestro
        var student, liveSessionPanel, liveSessionExists, liveSessionLink, liveSessionStart, liveSessionClose, liveSessionReadable;
				student = <?php GetTeachersSessionGC($fl_usuario); ?>;

				liveSessionPanel = $('#session-global');
				liveSessionExists = student.session.exists;
				liveSessionLink = student.session.link;
				liveSessionStart = student.session.start.milliseconds;
				liveSessionClose = student.session.close.milliseconds;
				liveSessionReadable = student.session.readable;
				liveSessionTitleClase = student.session.titleclass;
				liveSessionTitle = student.session.title;
        // If there is a live Global class
				if(liveSessionExists){
					// Setup panel
					liveSessionPanel.html(
						'<h1 class="no-margin"></h1>'+
						'<a role="button" class="btn btn-sm btn-default pull-right disabled" style="position: absolute; right: 10px; bottom: 10px;"><?php echo  ObtenEtiqueta(1017); ?></a>'
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
						layout: '<strong><?php echo ObtenEtiqueta(1016); ?></strong><br>'+
                    liveSessionTitleClase+'<br>'+
                    liveSessionTitle+'<br>'+
										liveSessionReadable+'<br>'+
										'<strong>in</strong> {dn} {dl} {hn}{sep}{mnn}{sep}{snn}'+
										'<br><br>',
						alwaysExpire: true,
						onExpiry: function(){
							$(this).countdown('destroy');
							$('#session_cg').html('');
                $('#session_cg').removeClass('btn-header');
                $('#session_cg').append(
                  '<span id="activity_cg" class="activity-dropdown pull-left" style="padding-top: 10px;"> '+
                    '<a role="button" class="btn btn-success pull-right padding-5" '+liveSessionLink+' target="_blank"><i class="fa fa-globe" ></i> '+
                    '<strong><?php echo ObtenEtiqueta(1017);?></strong></a>'+
                  '</span>'
                );
				$("#btn_join_disabled").addClass('hidden');
                $("#btn_join").removeClass('hidden');
				
							// Start another countdown before closing the session
							liveSessionPanel.find('h4').countdown(closeOptions);
						}
					});
					closeOptions = $.extend({}, defaultOptions, {
						until: new Date(liveSessionClose),
						layout: '<?php echo ObtenEtiqueta(1020); ?> {hn}{sep}{mnn}{sep}{snn}'+
										'<br><br><br>',
						alwaysExpire: true,
						onExpiry: function(){
							$(this).countdown('destroy');
							liveSessionPanel.html(
								'<h1 class="no-margin">'+
			  					'<strong><?php echo ObtenEtiqueta(1021); ?></strong><br>'+
			  					'<h4>Please update the page by pressing F5 or <a style="font-weight:500;" onclick="window.location.reload();">here</a></h4>'+
			  				'</h1>'+
			  				'<br><br>'+
			  				'<a role="button" class="btn btn-sm btn-default pull-right disabled" style="position: absolute; right: 10px; bottom: 10px;"><?php echo  ObtenEtiqueta(1017); ?></a>'
		  				);
						}
					});

					// Start the countdown
					liveSessionPanel.find('h1').countdown(startOptions);
          
          // Add alert class
          // Solo mostrar la alerta unas horas antes del inicio sesion
          // if(liveSessionactive)
            // $('#alert_cg').after('<b class="badge bg-color-red bounceIn animated">1</b>');
				}

			};
			
  
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
	<!-- END HEADER -->


	<?php
		}
	?>