<?php
	# Contacts.php the right panel list of users

	#note: require("../../common/lib/cam_general.inc.php") is already called in index.php (do not need to call it again)

	// Displays the list of users for the contact list
	function DisplayUser($row, $fg_user, $fl_usuario){
		$fl_user = $row[0];
		$ds_ruta_avatar = RetrieveAvatar($row, $fg_user);
		$ds_nombre = str_uso_normal($row[2]);

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
	
?>
<div id="right-container">
	<div id="right-panel" class="rightpanel">
		<div class="tab-content">
			<div class="tab-pane active">

				<!-- Online List -->

				<h4>Online Users</h4>
				<ul id="online-list" class="chatuserlist"></ul> 

				<div class="mb30"></div>

				<!-- Offline List -->
				<h4>Offline Users</h4>
				<ul id="offline-list" class="chatuserlist">
					<?php
						# have already validated $fl_usuario in header.php
						$rs = TeacherQuery();
						while($row = RecuperaRegistro($rs)) {
							DisplayUser($row, "teacher", $fl_usuario);	
						}
						$rs = StudentQuery();
						while($row = RecuperaRegistro($rs)){
							DisplayUser($row, "student", $fl_usuario);
						}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
	