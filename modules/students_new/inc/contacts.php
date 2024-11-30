<?php
	# Contacts.php the right panel list of users

	#note: require("../../common/lib/cam_general.inc.php") is already called in index.php (do not need to call it again)

	// Displays the list of users for the contact list
	function DisplayUser($row, $fg_user, $fl_usuario){
		$fl_user = $row[0];
		$ds_ruta_avatar = RetrieveAvatar($row, $fg_user);
		$ds_nombre = str_uso_normal($row[2]);

		
        #Recupermaos el nombre del Usuario Logado
        $QueryU="SELECT CONCAT(ds_nombres,' ',ds_apaterno)as nombre FROM c_usuario WHERE fl_usuario=$fl_user ";
        $rowU=RecuperaValor($QueryU);
        $fnameU=$rowU[0];
		
		# profession = teacher's company(ds_empresa) in industry or student's program(nb_programa)
		if(!empty($row[3]))
			$ds_profession = str_uso_normal($row[3]);
		else
			$ds_profession = "(Not defined)";
		$ds_pais = str_uso_normal($row[4]);

		if($fl_usuario <> $fl_user){
			# link to chat message
			echo 
	   		"<li id='user-$fl_user'>
					<div class='media'>
						<a href='#ajax/messages.php?usr=$fl_user' class='pull-left media-thumb'>$ds_ruta_avatar</a>
						<div class='media-body'>
							<a href='#ajax/messages.php?usr=$fl_user'><strong>$ds_nombre</strong></a>
							<small>$ds_profession</small>
							<small>$ds_pais</small>
						</div>
					</div>
					
					
                                   <a href='#' class='div_$fl_user' 
										data-chat-id='$fl_user' 
									  	data-chat-fname='$fnameU' 
									  	data-chat-lname='$lnameU' 
									  	data-chat-status='online' 
									  	data-chat-alertmsg='' 
									  	data-chat-alertshow='false' 
									  	data-rel='popover-hover' 
									  	data-placement='right' 
									  	data-html='true' 
									  	data-content=\"
											<div class='usr-card'>
												<img src='img/avatars/1.png' alt='$ds_nombre'>
												<div class='usr-card-content'>
													<h3>$ds_nombre</h3>
													<p>Sales Administrator</p>
												</div>
											</div>
										\"> 
									  	<i></i>$ds_nombre
									</a>
                    
					
					
					
				</li>";
		} else {
			# link to profile page
			echo 
	   		"<li id='user-$fl_user'>
					<div class='media'>
						<a href='#ajax/profile_view.php?profile_id=$fl_user' class='pull-left media-thumb'>$ds_ruta_avatar</a>
						<div class='media-body'>
							<a href='#ajax/profile_view.php?profile_id=$fl_user'><strong>$ds_nombre</strong></a>
							<small>$ds_profession</small>
							<small>$ds_pais</small>
						</div>
						
						
                                   <a href='#' class='div_$fl_user' 
										data-chat-id='$fl_user' 
									  	data-chat-fname='$fnameU' 
									  	data-chat-lname='$lnameU' 
									  	data-chat-status='online' 
									  	data-chat-alertmsg='' 
									  	data-chat-alertshow='false' 
									  	data-rel='popover-hover' 
									  	data-placement='right' 
									  	data-html='true' 
									  	data-content=\"
											<div class='usr-card'>
												<img src='img/avatars/1.png' alt='$ds_nombre'>
												<div class='usr-card-content'>
													<h3>$ds_nombre</h3>
													<p>Sales Administrator</p>
												</div>
											</div>
										\"> 
									  	<i></i>$ds_nombre
									</a>
                    
						
						
					</div>
				</li>";
		}
		
	}

	function RetrieveAvatar($row, $fg_user){
		if(!empty($row[1])){
			if($fg_user == "teacher")
				$ds_ruta_avatar = "<img src='".PATH_MAE_IMAGES."/avatars/".$row[1]."' class='media-object'>";
			if($fg_user == "student")
				$ds_ruta_avatar = "<img src='".PATH_ALU_IMAGES."/avatars/".$row[1]."' class='media-object'>";
		} 
		if(empty($row[1])){
			if($fg_user == "teacher")
				$ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_T_AVATAR_DEF."' class='media-object'>";
			if($fg_user == "student")
				$ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."' class='media-object'>";
		}
		return $ds_ruta_avatar;
	}


	
	// Displays the list of users for the contact list
	function DisplayUserChat($row, $fg_user, $fl_usuario){
		$fl_user = $row[0];
		$ds_ruta_avatar = RetrieveAvatar($row, $fg_user);
		$ds_nombre = str_uso_normal($row[2]);
        
		#Recupermaos segundo nombre del usuario.
        
        $QueryU="SELECT ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_user ";
        $rowU=RecuperaValor($QueryU);
        $fnameU=str_uso_normal($rowU[0]);
        $lnameU=str_uso_normal($rowU[1]);
		
		
		# profession = teacher's company(ds_empresa) in industry or student's program(nb_programa)
		if(!empty($row[3]))
			$ds_profession = str_uso_normal($row[3]);
		else
			$ds_profession = "(Not defined)";
		$ds_pais = str_uso_normal($row[4]);

		if($fl_usuario <> $fl_user){
			# link to chat message
			echo 
	   		"<li id='user-$fl_user'>
					<div class='media'>
						<a href='#ajax/messages.php?usr=$fl_user' class='pull-left media-thumb'>$ds_ruta_avatar</a>
						<div class='media-body'>
							<a href='#ajax/messages.php?usr=$fl_user' class='hidden'><strong>$ds_nombre</strong></a>
							
							        <a href='#' id='div_$fl_user' class='div_$fl_user' 
										data-chat-id='$fl_user' 
									  	data-chat-fname='$fnameU' 
									  	data-chat-lname='$lnameU' 
									  	data-chat-status='online' 
									  	data-chat-alertmsg='' 
									  	data-chat-alertshow='false' 
									  	data-rel='popover-hover' 
									  	data-placement='right' 
									  	data-html='true' 
									  	data-content=\"
											<div class='usr-card'>
												<img src='img/avatars/1.png' alt='$ds_nombre'>
												<div class='usr-card-content'>
													<h3>$ds_nombre</h3>
													<p>&nbsp;</p>
												</div>
											</div>
										\"> 
									  	<i></i>$ds_nombre
									</a>
					                <small>$ds_profession</small>
							        <small>$ds_pais</small>
							
							
							
						</div>
					</div>
					
								
					
					
					
					
					
					
				</li>";
		} else {
			# link to profile page
			/*echo 
	   		"<li id='user-$fl_user'>
					<div class='media'>
						<a href='#ajax/profile_view.php?profile_id=$fl_user' class='pull-left media-thumb'>$ds_ruta_avatar</a>
						<div class='media-body'>
							<a href='#ajax/profile_view.php?profile_id=$fl_user'><strong>$ds_nombre</strong></a>
							<small>$ds_profession</small>
							<small>$ds_pais</small>
						</div>
					</div>
					
								<a href='#' id='div_$fl_user' class='div_$fl_user' 
										data-chat-id='$fl_user' 
									  	data-chat-fname='$fnameU' 
									  	data-chat-lname='$lnameU' 
									  	data-chat-status='online' 
									  	data-chat-alertmsg='' 
									  	data-chat-alertshow='false' 
									  	data-rel='popover-hover' 
									  	data-placement='right' 
									  	data-html='true' 
									  	data-content=\"
											<div class='usr-card'>
												<img src='img/avatars/1.png' alt='$ds_nombre'>
												<div class='usr-card-content'>
													<h3>$ds_nombre</h3>
													<p>&nbsp;</p>
												</div>
											</div>
										\"> 
									  	<i></i>$ds_nombre
									</a>
					
					
					
					
					
				</li>";*/
		}
		
	}
	




	
?>

<!---
<div id="right-container">
	<div id="right-panel" class="rightpanel">
		<div class="tab-content">
			<div class="tab-pane active">

				# Online List 

				<h4>Online Users</h4>
				<ul id="online-list" class="chatuserlist"></ul> 

				<div class="mb30"></div>

				# Offline List 
				<h4>Offline Users</h4>
				<ul id="offline-list" class="chatuserlist">
					<?php
						# have already validated $fl_usuario in header.php
				//		$rs = TeacherQuery();
				//		while($row = RecuperaRegistro($rs)) {
				//			DisplayUser($row, "teacher", $fl_usuario);	
				//		}
				//		$rs = StudentQuery();
				//		while($row = RecuperaRegistro($rs)){
				//			DisplayUser($row, "student", $fl_usuario);
				//		}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
---->





<!------layout new chat------------------->



<div class="row">
		<div class="col-md-8">
		<p>&nbsp;</p>
		</div>

        <div class="col-md-4 "  style="position:fixed;z-index:1000;bottom:0px;right:0px;">
		
			<div class="chatbox mike" id="chatbox">
					<div class="panel panel-primary cat" >
					
					
						<div class="panel-heading text-left"   id="accordion2" data-position="on" data-toggle="collapse" onClick='MuestraNoUser();' data-parent="#accordion2" href="#collapseOneChat" style="padding: 2px;cursor:pointer;height:27px !important;">
						   <small style="padding-left:15px;font-size:13px;"><i class="fa fa-comments" aria-hidden="true"></i> Chat (<span id="user_onlines">0</span>)</small> 
						   
						          
						   
						   
						   
						</div>
						
						<div class="panel-collapse collapse" id="collapseOneChat">
							<div class="panel-body" style="height:400px; margin-right:-14px;">
								<div class="scrollbar" style="height: 400px;" >
								   
									<!-----Inicia User --------->
											<div class="tab-content">
												<div class="tab-pane active">

													<!-- Online List -->

													<h4 class="text-left" style="color:#999;"><?php echo ObtenEtiqueta(1935); ?></h4>
													<ul id="online-list" class="chatuserlist"></ul> 

													<div class="mb30"></div>

													<!-- Offline List -->
													<h4 class="hidden"><?php echo ObtenEtiqueta(1936); ?></h4>
													<ul id="offline-list" class="chatuserlist hidden">
														<?php
															# have already validated $fl_usuario in header.php
															$rs = TeacherQuery();
															while($row = RecuperaRegistro($rs)) {
																DisplayUserChat($row, "teacher", $fl_usuario);	
															}
															$rs = StudentQuery();
															while($row = RecuperaRegistro($rs)){
																DisplayUserChat($row, "student", $fl_usuario);
															}
														?>
													</ul>
												</div>
											</div>

																	
										

								
								
									<!------End User----->
								
								
								
							
						
								
								</div>
								
							</div>
							<div class="panel-footer hidden">
								
								
							
								
								
							</div>
						</div>
					</div>
			</div>		
        </div>
    </div>


<!---->
<div id="chatlog" style="height: 300px; border:1px dashed #333; overflow-x: auto" class="padding-10 custom-scroll hidden">
	<p><i>mesajens</i></p>
</div>




<?php 
 #Obtenemos el sonido del chat
 $sound_chat=ObtenNombreImagen(304);
?>


<script>
function SoundChat(){

        //alert('entro');
        // creamos el objeto audio
		 var audioElement = document.createElement('audio');	
		 // indicamos el archivo de audio a cargar
		 audioElement.setAttribute('src', '<?php echo PATH_HOME."/../images/".$sound_chat; ?>');	
		 // iniciamos el audio
		 audioElement.play();


}
setTimeout(function(){ 
    //Obtenemos los usuarios en Linea actualmente
    var total_user_online=$('ul#online-list li').length;
     $("#user_onlines").html(total_user_online);


//alert(total_user_online);



 }, 8000);
 
 
 function MuestraNoUser(){
    
     var total_user_online=$('ul#online-list li').length;
     $("#user_onlines").html(total_user_online);
 
 }
 
 
</script>



	