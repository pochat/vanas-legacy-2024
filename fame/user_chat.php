<?php
# Libreria de funciones	
require("lib/self_general.php");


$fl_usuario=RecibeParametroNumerico('fl_usuario');
$fl_usuario = ValidaSesion(False,0, True);

// Displays the list of users for the contact list
function DisplayUserChat($row, $fl_usuario){
    $fl_user = $row[0];

    $ds_nombre = str_uso_normal($row[2]);
    $ds_apaterno = str_uso_normal($row[13]);

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
	
    # blockgin last name
    $ds_nombre = ObtenNombreUsuario($fl_user,$fl_usuario);

    if($fl_usuario <> $fl_user){
        
        //echo" <li id='user-$fl_user'>user $fl_usuario</li>";
        # link to chat message  ya no se utiliza todo lo mandamos por ajax.
        echo 
        "
        <li id='user-$fl_user'>
					<div class='media'>
						<a href='#site/messages.php?usr=$fl_user' class='pull-left media-thumb'><img src='".$ds_ruta_avatar."' class='media-object'></a>
						<div class='media-body'>
							<a href='#site/messages.php?usr=$fl_user' class='hidden'><strong>$ds_nombre</strong> (".$nb_perfil.")</a>
							
							        <a href='#' id='div_$fl_user' class='usr' 
										data-chat-id='$fl_user' 
									  	data-chat-fname='$ds_nombre ' 
									  	data-chat-lname='' 
									  	data-chat-status='online' 
									  	data-chat-alertmsg='' 
									  	data-chat-alertshow='false' 
									  	data-rel='popover-hover' 
									  	data-placement='right' 
									  	data-html='true' 
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
							
							
							<small><i class='fa fa-institution'></i> $nb_instituto</small>
							<small><i class='fa fa-globe'></i> $ds_pais</small>
						</div>
					</div>
					
					               
									
									
									
									
                    
					
					
					
				</li>";
    } 
    
}

	
# have already validated $fl_usuario in header.php
   $rs = GetUserOnline($fl_usuario);
   while($row = RecuperaRegistro($rs)) {
 	  DisplayUserChat($row, $fl_usuario);	
   }
?>

						
	