<?php
	# Contacts.php the right panel list of users

	// Displays the list of users for the contact list
	function DisplayUser($row, $fl_usuario){
		$fl_user = $row[0];

		$ds_nombre = str_uso_normal($row[2]);    

		# profession = teacher's company(ds_empresa) in industry or student's program(nb_programa)		
		$ds_profession = str_uso_normal($row[3]);
    
    # Identificamos si es FAME O VANAS
    $edad = $row[11];
    $fame = $row[12];

    # Institute
    $fl_instituto = ObtenInstituto($fl_user);
    if($fame==1){
      $nb_instituto = ObtenNameInstituto($fl_instituto); 
      $ds_ruta_avatar = ObtenAvatarUsuario($fl_user);
    }
    else{
      $nb_instituto = ObtenEtiqueta(2010);
      $ds_ruta_avatar = ObtenAvatarUsrVanas($fl_user);
    }
    if(empty($ds_ruta_avatar))
      $ds_ruta_avatar =  "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."' class='".$class."' width='70' height='70'>";
		$ds_pais = str_uso_normal($row[4]);
    # Pais de la institution que lo invito
    if(empty($ds_pais) && $fame==1){
      $row1 = RecuperaValor("SELECT b.ds_pais FROM c_instituto a, c_pais b WHERE a.fl_pais = b.fl_pais AND a.fl_instituto=$fl_instituto");
      $ds_pais = $row1[0];
    }
    
    # Obtenemos el perfil del usuario
    $fl_perfil = ObtenPerfilUsuario($fl_user); 
    if(empty($fl_perfil))
      $fl_perfil = ObtenPerfil($fl_user);
    # Perfil
    $row0 = RecuperaValor("SELECT nb_perfil FROM  c_perfil WHERE fl_perfil=$fl_perfil");
    $nb_perfil = $row0[0];   
    
	#Recupermaos el nombre del Usuario Logado
    $QueryU="SELECT CONCAT(ds_nombres,' ',ds_apaterno)as nombre FROM c_usuario WHERE fl_usuario=$fl_usuario ";
    $rowU=RecuperaValor($QueryU);
    $fnameU=$rowU[0];
	
	
		if($fl_usuario <> $fl_user){
      
			# link to chat message
			echo 
	   		"
        <li id='user-$fl_user'>
					<div class='media'>
						<a href='#site/messages.php?usr=$fl_user' class='pull-left media-thumb'><img src='".$ds_ruta_avatar."' class='media-object'></a>
						<div class='media-body'>
							<a href='#site/messages.php?usr=$fl_user'><strong>$ds_nombre</strong> (".$nb_perfil.")</a>
							<small><i class='fa fa-institution'></i> $nb_instituto</small>
							<small><i class='fa fa-globe'></i> $ds_pais</small>
						</div>
					</div>
					
					                <a href='#' id='div_$fl_user' class='usr' 
										data-chat-id='$fl_user' 
									  	data-chat-fname='$ds_nombre' 
									  	data-chat-lname='$fnameU' 
									  	data-chat-status='online' 
									  	data-chat-alertmsg='' 
									  	data-chat-alertshow='false' 
									  	data-rel='popover-hover' 
									  	data-placement='right' 
									  	data-html='false' 
									  	data-content=\"
											<div class='usr-card'>
												<div class='usr-card-content'>
													<h3>Jessica Dolof</h3>
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
						<a href='#site/profile_view.php?profile_id=$fl_user' class='pull-left media-thumb'><img src='".$ds_ruta_avatar."' class='media-object'></a>
						<div class='media-body'>
							<a href='#site/profile_view.php?profile_id=$fl_user'><strong>$ds_nombre</strong> (".$nb_perfil.")</a>
							<small><i class='fa fa-institution'></i> $nb_instituto</small>
							<small><i class='fa fa-globe'></i> $ds_pais</small>
						</div>
					</div>
					
								 <a href='#' class='usr' 
										data-chat-id='$fl_user' 
									  	data-chat-fname='$ds_nombre' 
									  	data-chat-lname='$fnameU' 
									  	data-chat-status='online' 
									  	data-chat-alertmsg='' 
									  	data-chat-alertshow='false' 
									  	data-rel='popover-hover' 
									  	data-placement='right' 
									  	data-html='false' 
									  	data-content=\"
											<div class='usr-card'>
												<div class='usr-card-content'>
													<h3>Jessica Dolof</h3>
													<p>Sales Administrator</p>
												</div>
											</div>
										\"> 
									  	<i></i>$ds_nombre
								</a>	
									
									
									
					
					
					
				</li>";
		}
		
	}
	

	
	
	
	
	
?>


									




<!---estilos del nuevo chat--->
<style>
.ui-chatbox-titlebar {
	background: #0071BD !important;
	 border-radius: 4px !important;
	 box-shadow: -3px 2px 5px #888;
}

.ui-chatbox-log{
background: #f2f2f2 !important;
}
.ui-chatbox {
z-index: 1004!important;
}
.ui-chatbox-input-box {
border-color: #e6e0e0 !important;
}

.ui-chatbox-titlebar.ui-state-focus{

background:#0071BD !important;
}
.bubble-left {
color:#000 !important;
}
.bubble-right {
color:#000 !important;
}

.ui-chatbox-content {
box-shadow: -3px 2px 5px #888;
}

.panel-primary > .panel-heading {
background-color: #006FBA;
border-color: #006FBA;
}

</style>






<!--estilos de chatbox-->
<style>
.cat{
	margin-bottom:0px !important;
	margin-right:-13px !important;
}
.mike{
width: 240px;
float:right;
border: 0px solid transparent;
box-shadow: -3px 2px 5px #888;
}
.scrollbar {
overflow-y: scroll;

}
.ui-chatbox-titlebar>span {
width:131px;

}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>



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
	 
 }, 7000);
 
 function MuestraNoUser(){
    
     var total_user_online=$('ul#online-list li').length;
     $("#user_onlines").html(total_user_online);
 
 }
 
 

   /* 
    $(document).ready(function () {     
		 
		//var container = $("#offline-list");
        //MuestraAvatar(<?php echo $fl_usuario;?>);

//		function MuestraAvatar(fl_usuario){
			  
			   // $('#offline-list').empty();
			    
			 //pasamos por ajax los y presenta chat.
/*				$.ajax({
					 type: 'POST',
					 url: 'lib/presenta_listado_chat.php',
					 data: 'fl_usuario='+fl_usuario,

					 async: true,
					 }).done(function (result) {
						var elems = JSON.parse(result);			
						displayChat(container, elems);
			
					});

			
		}; 
	*/	 
		  //pita todos los comentarios de pirmer nivel
//		  var displayChat = function(container, elems){
	//		  var item;
//			  var items="";
//			  var fl_usuario=<?php echo $fl_usuario;?>;
//			  var ds_nombre="<?php echo ObtenNombreUsuario($fl_usuario);?>";
			  
/*			  for(var i=1; i<elems.size.total; i++){
				
				
				
     			  item = elems["item"+i];
				  var fl_usuario=elems["item"+i].fl_usuario;
				  var ds_ruta_avatar=elems["item"+i].ds_ruta_avatar;
				  var ds_nombre=elems["item"+i].ds_nombre;
				  var nb_perfil=elems["item"+i].nb_perfil;
				  var nb_instituto=elems["item"+i].nb_instituto;
				  var ds_pais=elems["item"+i].ds_pais;
				items+=
				"<li id=\"user-"+fl_usuario+"\">" +
				"	<div class='media'>"+
				"    <a href=\"#site/messages.php?usr="+fl_usuario+"\" class='pull-left media-thumb'><img src=\""+ds_ruta_avatar+"\" class=\"media-object\"></a>"+
				"     <div class=\"media-body\"> "+
				"         <a href=\"#site/messages.php?usr="+fl_usuario+"\" class=\"hidden\"><strong>"+ds_nombre+"</strong> ("+nb_perfil+")</a>"+
				"         <a href=\"#\" id=\"div_"+fl_usuario+"\" class=\"usr\" "+
				"            data-chat-id=\""+fl_usuario+"\" "+
				"			 data-chat-fname=\""+ds_nombre+"\" "+
				"		     data-chat-lname=\"\" "+
				"            data-chat-status=\"online\" "+
				"            data-chat-alertmsg=\"\" "+
				"            data-chat-alertshow=\"false\" "+
				"            data-rel=\"popover-hover\" "+
				"            data-placement=\"right\" "+
				"            data-html=\"true\" "+
				"            <i></i>"+ds_nombre+" "+
				"         </a>"+
				"         <small><i class=\"fa fa-institution\"></i>"+nb_instituto+"</small> "+
				"         <small><i class=\"fa fa-globe\"></i>"+ds_pais+"</small>"+
				
				"     <div>"+
				"   </div>"+
                "</li>";			
				 
				  
			  }
			  
			  container.append($(items));
			  
			 
			  socket.emit('add-user', {"fl_user": fl_usuario, "ds_name": ds_nombre},elems);
			  
		  }
 
	});
	
	*/
	
</script>


	