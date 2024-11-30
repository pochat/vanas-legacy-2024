<?php
	# Libreria de funciones	
	require("../lib/self_general.php");

	
	$fl_usuario=RecibeParametroNumerico('fl_usuario');
	
	$rs = GetUserOnline($fl_usuario);
    $contador=0;
    while($row = RecuperaRegistro($rs)) {
			
			//DisplayUserChat($row, $fl_usuario,$contador);	
			
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
		  
				$contador++;  

				$result["item".$contador] = array(
				
					"fl_usuario" => $fl_user,
					"ds_ruta_avatar"=>$ds_ruta_avatar,
					"ds_nombre"=>$ds_nombre,
					"nb_perfil"=>$nb_perfil,
					"nb_instituto"=>$nb_instituto,
				    "ds_pais"=>$ds_pais
				
				);
				
			
			}
			
	}
	$total_reg=$contador;
	$result["size"] = array("total" => $total_reg);
	echo json_encode((Object)$result);
?>