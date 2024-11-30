<!DOCTYPE html>
<!--<html lang="en-us">-->
<html xmlns='https://www.w3.org/1999/xhtml'>
	<head>
    <!--Lo secomente para el tamaño del aimagen en facebook-->
		<meta charset="utf-8" >    
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title> <?php echo ObtenEtiqueta(1934); ?> </title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Podemos quitaras cuando queramos --->
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <link rel='shortcut icon' href='https://campus.vanas.ca/fame/img/fame.ico'>
		<!-- Basic Styles -->
		<!--<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/bootstrap.min.css" >-->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_SELF_CSS; ?>/bootstrap.min.css" >
		<!--<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/font-awesome.min.css">-->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_SELF_CSS; ?>/font-awesome.min.css">
		<!-- SmartAdmin Style -->
		<!--<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/smartadmin-production.css">-->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_SELF_CSS; ?>/smartadmin-production.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_SELF_CSS; ?>/feed.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_SELF_CSS; ?>/smartadmin-production-plugins.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_SELF_CSS; ?>/I_24102016_smartadmin-production.min.css">
    
    <!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_SELF_CSS; ?>/demo.css">

		<!-- Vanas Style -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/vanas.css">
		<!--<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_SELF_CSS; ?>/vanas.css">-->
		<!-- Flowplayer -->
		<!--<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_SELF_CSS; ?>/flowplayer/playful.css">-->
    
    <!-- player skin -->
    <link rel="stylesheet" href="<?php echo PATH_SELF_JS; ?>/flowplayer/skin/skin.css">
	
	<!-- MJD 28/02/19: Librerias para usar CKEDITOR -->
    <link rel="stylesheet" type="text/css" href="<?php echo PATH_SELF_JS;?>/ckeditor/ckeditor/samples/css/samples.css" />
    <script src="<?php echo PATH_SELF_JS;?>/ckeditor/ckeditor/ckeditor.js"></script>

    <style>
    .flowplayer {
       width: 720;
       height: 480;
    }
    .flowplayer {      
      background-image: url(../fame/img/PosterFrame_White.jpg);
    }
    </style>
<?php
	if (isset($page_css)) {
		foreach ($page_css as $css) {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.PATH_SELF_CSS.'/'.$css.'">';
		}
	}
?>
		<!-- GOOGLE FONT -->
		<link rel="stylesheet" type="text/css" href="<?php echo PATH_SELF_CSS; ?>/font_google_api.css">
		<!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<script src="<?php echo PATH_SELF_JS; ?>/actions.js.php"></script>
    <script src="<?php echo PATH_SELF_JS; ?>/fontawesome.js"></script>
	
	<!--script publicidad--->
	<script src="//36ce5f316c12438cb40996edbdfb293b.js.ubembed.com" async></script>
	
	<style>
	
	.goog-te-banner-frame.skiptranslate {
    display: none !important;
    } 
body {
    top: 0px !important; 
    }
	
	</style>
	
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-KTFH4BR');</script>
	<!-- End Google Tag Manager -->
	
	</head>
	<body >
  
    <!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KTFH4BR" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->

  	
	    <?php
		# Obtenemos el nombre y la imagen del intituto
        # Verifica que exista una sesion valida en el cookie y la resetea
        $fl_usuario = ValidaSesion(False,0, True);
        $fl_instituto = ObtenInstituto($fl_usuario);
        $ds_instituto = ObtenNameInstituto($fl_instituto);
        $fl_perfil_sp=ObtenPerfilUsuario($fl_usuario);
        $ruta_avatar = ObtenAvatarUsuario($fl_usuario);
		
		#Verficamos si es alumno de vanas para colocar boton de pagar curso o invitar a otro compadre.
        if($fl_perfil_sp==PFL_ESTUDIANTE_SELF)
	        $fg_puede_liberar_curso=PuedeLiberarCurso($fl_instituto,$fl_usuario);
	    else
	    	$fg_puede_liberar_curso=NULL;	
		
		
        #Presenta alerta, de proxima expiration de tarjeta, pra admin y teacher.
       // PresentaAlertExpirationCreditCar($fl_instituto,$fl_usuario);
	    PresentaAlertaCancelacionPlan($fl_instituto,$fl_usuario);
        ?>
	
	<!-- POSSIBLE CLASSES: minified, fixed-ribbon, fixed-header, fixed-width-->
	<?php
		if (!isset($no_main_header)) {
	?>
  <div id="loading_fame" class="superbox-show" style="display: block; z-index:5000; position:absolute; background-color: #fff; opacity:0.5; width:100%; height:100%;">
    <img src="../images/<?php echo ObtenNombreImagen(300); ?>" class="superbox-current-img" style="position:absolute; left:50%; top:30%; width:150px;" />
  </div>
	<header id="vanas-header">
		<div id="logo-group" class="pull-left">

			<!-- LOGO -->
			<!--<span id="logo"><img src="<?php echo PATH_SELF_IMG; ?>/fame_logo.png" alt="Vanas Logo"></span>-->
			<span id="logo"><a href="https://go.myfame.org/fame/index.php#site/home.php"><img src="<?php echo SP_IMAGES."/".ObtenNombreImagen(303); ?>" alt="Vanas Logo"></a></span>
      
      <?php
        
        
        # Verifica que el usuario tenga permiso de usar esta funcion
        if(!ValidaPermisoSelf(FUNC_SELF)) {
          MuestraPaginaError(ERR_SIN_PERMISO);
          exit;
        }
				# Count number of unread messages
                
                $diferencia = RecuperaDiferenciaGMT( );
				$Query="SELECT * FROM k_mensaje_directo WHERE fl_usuario_dest=$fl_usuario  AND fg_leido='0' GROUP BY fl_usuario_ori   ";
                $rs = EjecutaQuery($Query);
                    
                $no_mensaje= CuentaRegistros($rs);
				if($no_mensaje > 0)
					$no_messages = $no_mensaje;
        
        # Count number of new notifications
        $Queryn = "SELECT COUNT(*) ";
        $Queryn .= "FROM k_gallery_comment_sp a ";
        $Queryn .= "JOIN k_gallery_post_sp b ON(a.fl_gallery_post_sp=b.fl_gallery_post_sp) ";
        $Queryn .= "LEFT JOIN c_programa_sp c ON(b.fl_programa_sp=c.fl_programa_sp OR b.fl_programa_sp IS NULL) ";
        $Queryn .= "WHERE b.fl_usuario=$fl_usuario AND a.fl_usuario<>$fl_usuario AND fg_read='0'  ";
        $Queryn .= "ORDER BY a.fe_comment DESC ";
        $rsn = RecuperaValor($Queryn);
        $no_news = $rsn[0];
   
     
			#Recupera los cursos que han sido calificados por el teacher y que no han sido confirmados por el estudiante.
            
		    $QueryG="SELECT COUNT(*) FROM k_entrega_semanal_sp  a WHERE a.fl_alumno=$fl_usuario  and fg_revisado_alumno='0' and fl_promedio_semana is not null ; ";  
            $rowg=RecuperaValor($QueryG);
            if($rowg[0] > 0){
		    	$no_assigment_grade = $rowg[0];
		    }
		
		    if($fg_puede_liberar_curso==1){
            
		            #Recupermaos las noticficacies de sbloqueo de cursos. por el metodo de envio de emails.
		            $QueryP="SELECT COUNT(*) FROM k_confirmacion_email_curso WHERE fl_alumno_beneficiado=$fl_usuario AND fg_revisado_alumno='0' ";
		            $rowp=RecuperaValor($QueryP);
		            if($rowp[0]>0){
		                 $no_p=$rowp[0];
		            }
            
            }else{
                $no_p=0;
            }

		    #Recuperamos las asignaciones de cursos por parte del teacher.
            if(($fl_perfil_sp==PFL_ESTUDIANTE_SELF)||($fl_perfil_sp==PFL_MAESTRO_SELF)){



                $QueryC="SELECT COUNT(*) FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fg_revisado_alumno='0' ";
                $roc=RecuperaValor($QueryC);
                if($roc[0]>0){
                    $tot_cursos_asignado=$roc[0];
                }else{
                    $tot_cursos_asignado=0;
                }

                if($fl_perfil_sp==PFL_ESTUDIANTE_SELF){

			        #Recuperamos los Institutos en el cual esta enrolado el Alumno.
		    	    $Query="SELECT a.fl_instituto,ds_instituto,b.ds_foto,c.ds_pais   
				        FROM k_instituto_alumno a 
					    JOIN c_instituto b ON a.fl_instituto=b.fl_instituto
					    JOIN c_pais c ON c.fl_pais=b.fl_pais 
					    WHERE a.fl_usuario_sp=$fl_usuario AND a.fg_aceptado='1' ";
                }
                if($fl_perfil_sp==PFL_MAESTRO_SELF){
                    
                    $Query="SELECT a.fl_instituto,ds_instituto,b.ds_foto,c.ds_pais
                            FROM k_instituto_teacher a
                            JOIN c_instituto b ON a.fl_instituto=b.fl_instituto
					        JOIN c_pais c ON c.fl_pais=b.fl_pais 
                            WHERE a.fl_maestro_sp=$fl_usuario AND a.fg_aceptado='1'  ";
                }
				$rsi=EjecutaQuery($Query);
				$lista_school="<ul style='margin: auto; display: flex; list-style-type: none;'>";
				$data_institutos="<ul class='media-list'>";
                $contador_inst=0;
                $lista_school=NULL;
				for($i=0;$row=RecuperaRegistro($rsi);$i++){
                    $contador_inst++;
					$ds_foto=$row['ds_foto'];
					$ds_name_inst=CutText($row['ds_instituto'],25);
					$fl_institu=$row['fl_instituto'];
					$nb_pais_instituo=$row['ds_pais'];
					
					
					
					if((!empty($ds_foto))&&($ds_foto<>'null')){
						$ds_foto="".PATH_SELF_UPLOADS."/".$fl_institu."/".$ds_foto."";
					}else{
						$ds_foto="".PATH_SELF_IMG."/Partner_School_Logo.jpg";
					}
					
					$data_institutos.="<li class='media'><a href='javascript:void(0);' onclick='CambiarInstituto(".$fl_institu.")' ><img class='pull-left img-circle' src='$ds_foto' style='height: 30px;margin-right:7px;' > <div class='media-body'> <span style='font-size:11px;'>".$ds_name_inst."</span></div></a></li>";
					$lista_school.="<li style='position: relative; left: -10px; display: inline; margin-right: 40px;'>
										<div class='well admins-profile text-center' style='background: rgb(255, 255, 255);left: 0px;box-shadow: 0 0px 0px #b7b4b4; top: 0px; width:200px;'>
												
											<img src='".$ds_foto."' class='img-circle' width='100' height='100'>
												
											<div style='height:190px;font-size: 14px;'>
												<div class='text-center' style='min-height: 75px;'>
													<p class='no-margin h2' >
														<small style='color:#636161;'><i class='fa fa-graduation-cap' aria-hidden='true'></i> ".$ds_name_inst."</small> 
													</p>
													<p class='no-margin h2'>
														<small style='color:#636161;'><i class='fa fa-globe' aria-hidden='true'></i> ".$nb_pais_instituo."</small> 
													</p>
												</div>
												<div class='text-center'><br><br><br>
													<a class='btn btn-default' style='background: #fff; margin:5px;color:#0092CD;border-color:#0092CD;' href='javascript:void(0);' onclick='CambiarInstituto(".$fl_institu.")'><i class='fa fa-link' aria-hidden='true'></i> ".ObtenEtiqueta(2590)."</a>
												</div>
											</div>
										</div>
									</li>
									";
				}
				$lista_school.="</ul>";
				$data_institutos.="</ul>";
            }else{
                $tot_cursos_asignado=0;
                $contador_inst=NULL;
            }
            if($contador_inst>=2){
                $fg_tiene_institutos=1;
            } else {
            	$fg_tiene_institutos=NULL;
            }
			

            #Todas los comentarios en donde el usuario este involucrado(simplemente con comentar un post).
            $Query="SELECT COUNT(*)FROM k_feed_comentarios WHERE fl_usuario_destino=$fl_usuario AND fg_revisado='0' ";
            $row=RecuperaValor($Query);
            $tot_comentarios_post=$row[0];

            #Recuperamos los request access course.
            $Query="SELECT COUNT(*)FROM k_request_access_course WHERE fl_maestro_sp=$fl_usuario AND fg_revisado='0' ";
            $row=RecuperaValor($Query);
            $no_request_access_course=$row[0];

            #Recupermos los denegados para este usuario(alumno)
            $Query="SELECT COUNT(*)FROM k_request_access_course WHERE fl_usuario_sp=$fl_usuario AND fg_denegado='1' ";
            $row=RecuperaValor($Query);
            $no_request_access_denegado=$row[0];




			#Recuperamos las solicitudes de amistad.
			$Query="SELECT COUNT(*) FROM k_relacion_usuarios WHERE fl_usuario_destinatario=".$fl_usuario." AND fg_aceptado='0'  ";
			$ror=RecuperaValor($Query);
			$no_solitudes_pendientes=$ror[0];
			
            #Recupermaos las soslicitudes a mistas aceptadas.
            $Querya="SELECT COUNT(*) FROM k_relacion_usuarios WHERE fl_usuario_origen=$fl_usuario AND fg_aceptado='1' AND fg_revisado_alumno='0'  ";
            $rou=RecuperaValor($Querya);
            $no_solicitudes_acptadas=$rou[0];
            
            #Recuperamos los likes que tiene este usuario con respestco a sus post.
            $Query1="SELECT COUNT(*) FROM  c_feed_likes a
		            JOIN c_feed_publicaciones b ON a.fl_gallery_post_feed=b.fl_publicacion
		            JOIN k_gallery_post_sp c ON c.fl_gallery_post_sp=a.fl_gallery_post_feed
		            WHERE ( b.fl_usuario=$fl_usuario OR c.fl_usuario=$fl_usuario )
		            AND a.fl_usuario<>$fl_usuario AND fg_revisado='0' ";
			$lik=RecuperaValor($Query1);
			$no_likes=$lik[0];

            #Recupermso los likes de 2do nivel.
            $Query="SELECT COUNT(*) FROM k_feed_likes a
                    JOIN k_feed_comment b ON a.fl_gallery_comment_sp=b.fl_feed_comment
                    JOIN c_feed_publicaciones c ON c.fl_publicacion=b.fl_publicacion
                    WHERE c.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND a.fl_usuario<>$fl_usuario  ";
            $rop=RecuperaValor($Query);
            $no_likes2=$rop[0];


            #Recuperamos los likes que vienen de 3er nivel del feed.
            $Query=" SELECT COUNT(*)  
                    FROM k_feed_likes a
                    JOIN k_feed_comment_comment b ON a.fl_gallery_comment_sp_comment=b.fl_feed_comment_comment 
                    JOIN k_feed_comment c ON c.fl_feed_comment=b.fl_feed_comment
                    JOIN c_feed_publicaciones d ON d.fl_publicacion=c.fl_publicacion
                    WHERE c.fl_usuario=$fl_usuario AND a.fl_usuario<>$fl_usuario
            ";
            $rol=RecuperaValor($Query);
            $no_likes3=$rol[0];

            #Recupersmoas likes 2do nivel de post que viene del board gallery.

            #Recuperamos likes 3er nivel de post que vienen del board gallery

			#Recuperamos los followers de este usuario y que no ha sido revisado de este usuario.
			$Queryf="SELECT COUNT(*) FROM c_followers a WHERE fl_usuario_destino=$fl_usuario AND a.fg_revisado='0';";
			$fol=RecuperaValor($Queryf);
			$no_follow=$fol[0];
			
			#Recuperamos los comentarios sobre sus post que tiene este usuario(fame/feed).
			$QueryC="SELECT COUNT(*) FROM k_feed_comment a 
					JOIN c_feed_publicaciones b ON a.fl_publicacion=b.fl_publicacion
					WHERE b.fl_usuario=$fl_usuario AND a.fl_usuario<>$fl_usuario AND a.fg_revisado='0' AND b.fg_ayuda='0' ";
			$com=RecuperaValor($QueryC);
            $no_com_feed=$com[0];

            #Comentarios 3er nivel.
            $Query="SELECT COUNT(*)  
                    FROM k_feed_comment_comment b
                    JOIN k_feed_comment c ON c.fl_feed_comment=b.fl_feed_comment 
                    JOIN c_feed_publicaciones d ON d.fl_publicacion=c.fl_publicacion 
                    WHERE c.fl_usuario=$fl_usuario AND c.fg_revisado='0' AND b.fl_usuario<>$fl_usuario ";
            $com=RecuperaValor($Query);
            $no_com_feed3=$com[0];

            #Recuperamos los comentarios sobre sus post que tiene este usuario(fame/feed) ambulancia.
			$QueryAm="SELECT COUNT(*) FROM k_feed_comment a 
					JOIN c_feed_publicaciones b ON a.fl_publicacion=b.fl_publicacion
					WHERE b.fl_usuario=$fl_usuario AND a.fl_usuario<>$fl_usuario AND a.fg_revisado='0' AND b.fg_ayuda='1' ";
			$coma=RecuperaValor($QueryAm);
            $no_com_feed_ambulancia=$coma[0];

			
			#Recupermaos los comentarios de post (sobre gallery board)
			$QueryB="SELECT COUNT(*) FROM k_gallery_comment_sp a 
					JOIN k_gallery_post_sp b ON b.fl_gallery_post_sp=a.fl_gallery_post_sp
					WHERE b.fl_usuario=$fl_usuario AND a.fg_revisado='0' AND a.fg_read='0' AND b.fg_ayuda='0'  ";
			$comf=RecuperaValor($QueryB);
            $no_com_board=$comf[0];	
			
			$no_comentarios=$no_com_feed+$no_com_board+$no_com_feed_ambulancia+$no_com_feed3;
			
			#Recupermaos sus respuestas corrctas que tiene este usuario.
			$QueryA="SELECT COUNT(*) FROM k_feed_comment a 
					 WHERE a.fl_usuario=$fl_usuario  AND a.fg_revisado='0' AND a.fg_correcto='1' ";
			$res=RecuperaValor($QueryA);
            $no_respuesta=$res[0];
			
        if(empty($no_messages))
          $no_messages = 0;
        if(empty($no_news))
          $no_news = 0;
        if(empty($no_assigment_grade))
          $no_assigment_grade = 0;
        if(empty($no_p))
          $no_p=0;
		if(empty($no_solitudes_pendientes))
		  $no_solitudes_pendientes=0;
        if(empty($no_solicitudes_acptadas))
         $no_solicitudes_acptadas=0;
		if(empty($tot_not_feed))
		 $tot_not_feed=0;

		$tot_not_feed=$no_likes+$no_follow+$no_comentarios+$no_respuesta+$no_likes2+$no_likes3;
					
            
		$no_total = $no_messages + $no_news  + $no_assigment_grade + $no_p + $tot_cursos_asignado+$no_solitudes_pendientes+$no_solicitudes_acptadas+$no_likes+$no_follow+$no_comentarios+$no_respuesta+$no_likes2+$no_likes3+$no_request_access_course+$tot_comentarios_post+$no_request_access_denegado;
	 
	 
        #Para sumar las notices con los confirmaciones de email.
        $tot_notices=$no_news+$no_p+$tot_cursos_asignado+$no_solitudes_pendientes+$no_solicitudes_acptadas+$tot_not_feed+$no_request_access_course+$tot_comentarios_post+$no_request_access_denegado;
        
        # Actualizamos los dtos del usuario
        if(ExisteEnTabla('k_usu_notify', 'fl_usuario', $fl_usuario))
          EjecutaQuery("UPDATE k_usu_notify SET no_messages=$no_messages, no_notice=$no_news, no_assigments=$no_assigment_grade WHERE fl_usuario=$fl_usuario");
        else
          EjecutaQuery("INSERT INTO k_usu_notify (fl_usuario,no_messages,no_notice,no_assigments) VALUES ($fl_usuario, $no_messages, $no_news, $no_assigment_grade)");

		?>
			<span id="activity" class="activity-dropdown pull-left"> <i class="fa fa-user"></i> <b class="badge" id="tot_notity_<?php echo $fl_usuario; ?>"><?php if(!empty($no_total)) echo $no_total; else echo '0'; ?></b> </span>

			<div class="ajax-dropdown" id="mostrar_noticias">
				
				<div class="btn-group btn-group-justified" data-toggle="buttons">
					<label class="btn btn-default" style="font-size:12px;">
						<input type="radio" name="activity" id="site/notify/messages.php">
						<?php echo ObtenEtiqueta(1932); ?> <span class='notice-count' id="tot_messages_<?php echo $fl_usuario; ?>"><?php if(!empty($no_messages)){echo "(<span id='no_messages'>".$no_messages."</span>)";} ?></span>
					</label>
					<label class="btn btn-default active" style="font-size:12px;">
						<input type="radio" name="activity" id="site/notify/notifications.php">
						<?php echo ObtenEtiqueta(1933); ?> <span class='notice-count' id="tot_comentario_<?php echo $fl_usuario; ?>"><?php /*if(!empty($tot_notices)){*/echo "(<span id='no_news'>".$tot_notices."</span>)";/*}*/ ?></span>
					</label>
					<!--<label class="btn btn-default">
						<input type="radio" name="activity" id="site/notify/progress.php">
						Progress 
					</label>-->
					
					 <label class="btn btn-default text-center" style="font-size:12px;">
						<input type="radio" name="activity" id="site/notify/assigment_grade.php">
						<?php echo ObtenEtiqueta(1676); ?>  <span class='notice-count text-center' id="tot_assigment_<?php echo $fl_usuario; ?>"  ><?php if(!empty($no_assigment_grade)){echo "(<span id='no_assigment_grade'>".$no_assigment_grade."</span>)";} ?></span>
					</label>					
			
				</div>
				<div class="ajax-notifications custom-scroll"></div>
			</div>

		</div>
     <style>
	 .txt-color-orangeDark {
    color: #0071BD !important;
}
	 </style>
		<div class="pull-right mikefeed">
		
			<div id="logout" class="btn-header pull-right">
				<span> <a href="<?php echo PAGINA_SALIR; ?>"  title="Sign Out" data-logout-msg="Goodbye!"><i class="fa fa-sign-out" style="color:#0071BD;"></i></a> </span>
			</div>
			
			<div id="security" class="btn-header pull-right">
				<span> <a href="javascript:privacity(<?php echo $fl_instituto; ?>);" ><i class="fa fa-user-secret"></i> </a> </span>
			</div>
			<?php if($fg_tiene_institutos==1){ ?>
			<div id="clock" class="btn-header pull-right">
				<span> <a href="javascript:void(0)" style="" rel="popover" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(2562);?>" data-content="<?php echo $data_institutos;?>" data-html="true"><i class="fa fa-graduation-cap"></i> </a> </span>
			</div>
			<?php } ?>
			
			
      <ul class="header-dropdown-list">
        <li>
          <div  class="dropdown-toggle" data-toggle="dropdown" style="margin: 10px;"> 
          <?php
          $ds_foto_inst = ObtenFotoInstituto($fl_instituto);
		  if($ds_foto_inst=='null')
		     $ds_foto_inst="";
          if($ds_foto_inst!=""){
            echo '<img src="'.PATH_SELF_UPLOADS."/".$fl_instituto."/".$ds_foto_inst.'" class="flag flag-us" width="30px" height="30px" /> 
            <span class="h6">'.$ds_instituto.'</span>';
          }
          else{
            echo '<img src="'.PATH_SELF_IMG.'/Partner_School_Logo.jpg" class="flag flag-us" width="30px" height="30px" /> 
            <span class="h6">'.$ds_instituto.'</span>';

          }
          ?>
          </div>
        </li>
      </ul>
		</div>
    <!-- Modal para la privacidad -->
    <div class="modal fade" id="ModalPrivacity" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">      
    </div><!-- /.modal -->
    
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
            <form role="form" method="POST" action="ajax/gallery_comment_iu.php" enctype='multipart/form-data'>
              <h6>Add a Comment</h6>
              <textarea class="form-control" name="ds_comment" rows="4"></textarea>
              <div class='form-group padding-bottom-10 padding-top-10'>
                <button id='modal-comment-submit-header' class='btn btn-sm btn-primary pull-right'>Send</button>
              </div>
              <input type="hidden" id="fl_comentario_sp" name="fl_comentario_sp" >
            </form>
          </div>
        </div>
      </div>
    </div>
	
    <!---input donde se almacena el resultado de saber si es followers, es launica forma que encontre de hacerlo. ---->
     <input type='hidden' id='fg_follow' value='' >


	 <!---MJD mODAL que mostrar bien chido el contenido decontrato del estudiante fame-> vanas desbloquaer curso.--->
	  <div class="modal" id="myModales23" data-backdrop="static">
		<div class="modal-dialog">
		  <div class="modal-content"  >

				<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
				<h4 class="modal-title text-left"><i class="fa fa-file"></i> <?php echo ObtenEtiqueta(913);?></h4>
				</div>
				<div class="modal-body" style='height:400px;overflow-y: scroll;overflow-x:hidden'>

					<?php 
					
					#se genera el cuerpo del documento del contrato
					$ds_encabezado_contrato = genera_ContratoFame($fl_instituto, 1,102,$fl_usuario);
					$ds_cuerpo_contrato = genera_ContratoFame($fl_instituto, 2, 102,$fl_usuario);
					$ds_pie_contrato = genera_ContratoFame($fl_instituto, 3,102,$fl_usuario);
					
					echo $ds_encabezado_contrato."<br/> ".$ds_cuerpo_contrato."<br/> ".$ds_pie_contrato;
					
					
					?>




				</div>

			   <div class="modal-footer">
			  <a href="#" data-dismiss="modal" class="btn btn-default"><i class="fa fa-times-circle" aria-hidden="true"></i> Close</a>
			  <!--<a href="#" class="btn btn-primary">Save changes</a>-->
			</div>




		  </div>
		</div>
	</div>










<!-- Modal para mostrar un post  FAME Feed-->
<div class="modal fade mike_modal_post" id="postId" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog " role="document" style="margin-top:40px; width: 90%; height:auto;">
    <div class="modal-content"   style="height: auto;!important;  >
     
	      
		  <div class="modal-body no-padding" id="muestra_modal_post">
		  
	
		  </div>
		  
	  
    </div>
  </div>
</div>
<!---end modal--->



<!-- Modal para envio de inivitaciones-->
<div class="modal fade mike_modal_post" id="externalInvitacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm contenidoSenInvitation" role="document" style="margin-top:100px;">
    <div class="modal-content">
      <div class="modal-header ModalHeaderFeed">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-user-plus" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2501); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-27px">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="EnviarInvitacion">
        
		

		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary " data-dismiss="modal"><i class="fa fa-times-circle" aria-hidden="true"></i> Cancel</button>
        <button type="button" class="btn btn-success btn_footer_modal txt-color-white " style="background: #0071BD;color: #fff;" onclick="EnviarInvitacion();"><i class="fa fa-check-circle" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2314);?></button>
      </div>
    </div>
  </div>
</div>

<!----Modal para cambio de Instituto.--->

<!-- Modal -->
<div class="modal fade" id="modal_institutos" style="background: #000000de;margin-top:50px;z-index: 1050;" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-labelledby="modal_institutosTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="background-color: #fff0;box-shadow: 0 0px 0px rgba(0,0,0,0.5);">
      <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
	        <div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6 text-center">
					<!--<h5 class="modal-title text-center" id="modal_institutosTitle"><?php //echo ObtenEtiqueta(2563);?></h5>-->
				</div>
				<div class="col-md-3"></div>
			</div>
	        	<!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">
        			<span aria-hidden="true">&times;</span>
        		</button>-->
      </div>
      <div class="modal-body">
            <div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6 text-center">
					<?php echo !empty($lista_school)?$lista_school:NULL; ?>
				</div>
				<div class="col-md-3"></div>
			</div>
      </div>
      <div class="modal-footer" style="border-top: 0px solid #e5e5e5;">
        
      </div>
    </div>
  </div>
</div>

<!---------------------------------------->

    <!-- To node Important -->
    <script type="text/javascript">
			window.onload = function() {
				var fl_user, ds_name;
				fl_user = <?php echo json_encode($fl_usuario); ?>;
				ds_name = <?php echo json_encode(ObtenNombreUsuario($fl_usuario)); ?>;
				socket.emit('add-user', {"fl_user": fl_user, "ds_name": ds_name});
				
				
        var commentButtonSubmit = $("#modal-comment-submit-header");
        // Submitting a comment
        commentButtonSubmit.on('click', function(){
          boardController.submitComment($("#modal-item-container"));
		  boardControllerGaleria.submitComment($("#modal-item-container"));
		  
          return false;
        });        
      };   

      function privacity(p_instituto){
        var modal = $("#ModalPrivacity");
        modal.empty();
        modal.modal("toggle");
        $.ajax({
          type: 'POST',
          url : 'site/privacity_user.php',
          data: 'fl_instituto='+p_instituto
        }).done(function(html){
          modal.append(html);
        });
      }

  //MJD Se ejecuta cada que un nuevo alumno confirma su email y se registra y corresponde al usuario que lo invito  se ve bien chido.
   function EnviaNoticeDesbloquearCurso(fl_usu_destino,ds_email_confirmado,nb_programa_desbloquear){
   
      
      var fl_usuario_actual=<?php echo $fl_usuario;?>;
	  
	   if(fl_usuario_actual==fl_usu_destino){
	
	     //ejecuta modal
			$.smallBox({
				title :"<?php echo ObtenEtiqueta(2101);?> ",  
				content : "<?php echo ObtenEtiqueta(2112);?>: "+nb_programa_desbloquear+"<br/> "+ ds_email_confirmado,
				color : "#0071BD",
				timeout: 40000,
				icon : "fa fa-envelope-open"
				//number : "1"
			});
			
			//alert('entrooo');
			//id_div 	ue contienen totales de notificciones.
			var tot_notifications = $("#activity>b").text();
	        var no_news = $('#no_news').text();
	  
		    $("#activity>b").empty();	
		    $('#no_news').empty();

		    var v = parseInt(tot_notifications) + 1;
		    var n = parseInt(no_news)+1;
		    $("#activity>b").append(v);
		    $('#no_news').append(n);

	   }
  
	}

    function EnviaNotificacionAsignacionCurso(fl_usuario_destino,nb_programa_desbloquear){
	
	        var fl_usuario_actual=<?php echo $fl_usuario;?>;
			var nb_programa_desbloquear=nb_programa_desbloquear;

			if(fl_usuario_actual==fl_usuario_destino){
			
				if(nb_programa_desbloquear){
			
					 //ejecuta alert
					$.smallBox({
						title :"<?php echo ObtenEtiqueta(2134);?> ",  
						content : nb_programa_desbloquear,
						color : "#0071BD",
						timeout: 40000,
						icon : "fa fa-book -open"
						//number : "1"
					});

					var tot_notifications = $("#activity>b").text();
					var no_news = $('#no_news').text();
					$("#activity>b").empty();	
					$('#no_news').empty();
					var v = parseInt(tot_notifications) + 1;
					var n = parseInt(no_news)+1;
					$("#activity>b").append(v);
					$('#no_news').append(n);

				}
			
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
			
			var no_news = $('#no_news').text();
			$('#no_news').empty();
		    var n = parseInt(no_news)+1;
			$('#no_news').append(n);

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
						
						var no_news = $('#no_news').text();
						$('#no_news').empty();
						var n = parseInt(no_news)+1;
						$('#no_news').append(n);
						
						
						
						
			}
		
		
		
		
	}
	/*funcion para node.js*/
	function ObtenUsuarioActual(){
		
	   var fl_usuario_actual=<?php echo $fl_usuario;?>;
       return fl_usuario_actual;	   
		
	}
	/*funcion node.js*/
	function AvatarUserActualFameFeed(){
		
		
		var avatar='<img src=\"<?php echo $ruta_avatar;?>\" alt=\"img\" class=\"onlines\">';
		
		return avatar;
	}
	
	function ObtenIconoFollow(fl_usuario_posteo){
		
		var fl_usuario_actual=<?php echo $fl_usuario;?>;
		

		
		
		
		$.ajax({
				type:'POST',
				url:'site/verifica_follow_user.php',
				data:'fl_usuario_posteo='+fl_usuario_posteo+
				'&fl_usuario_actual='+fl_usuario_actual,
				async: false	 
		}).done(function (result){
			
			var resultado = JSON.parse(result);			
			var icono_folow=resultado.tipo_icono_follower;
			
			$('#fg_follow').val(icono_folow);
		});	    
		//falta obtenerlo para aber la primera instancia/pasarlo de arriba avajho
		var icono_folow=document.getElementById('fg_follow').value;
		
		return icono_folow;
		
		
	}
	
	function CambiarInstituto(fl_instituto){
		
		$.ajax({
			type:'POST',
			url:'site/cambiar_instituto.php',
			data:'fl_instituto='+fl_instituto,
			async:false
			
		}).done(function(result){
			
			
		});
		
		document.location.reload();
		
	} 	
	function SalirFAME(fl_usuario){
		
		$('#modal_institutos').modal('hide');
		
		$.ajax({
			type:'POST',
			url:'site/cambiar_instituto.php',
			data:'fl_usuario='+fl_usuario,
			async:false
			
		}).done(function(result){
			
			
		});
		
		
	}

    </script>
    
<div id="accion_addwsz"></div>	

	
	
  </header>
	<!-- END HEADER -->

	<?php
    # Este es para el reloj pero lo comentamos por momento
    // echo "<div id='clock-container' class='collapse container'></div>";
		}    
	?>
