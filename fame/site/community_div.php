<?php
	# Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

	# Recibe parametros generales
 	$category = RecibeParametroHTML('category');
	$program = RecibeParametroNumerico('program');

	# variables initialized to avoid error, not enough to resolve logic problems *** IMPORTANT ***
	$letter = !empty($letter)?$letter:NULL;
	$country = !empty($country)?$country:NULL;
  	$classmate = !empty($classmate)?$classmate:NULL;
  	$fl_mi_maestro = !empty($fl_mi_maestro)?$fl_mi_maestro:NULL;// HERE IS THE PRINCIPAL LOGIC PROBLEM

  if($category == 'A') {
		$admins = AdministradorList($fl_usuario);
		echo json_encode((Object) array("admins" => $admins));
	}
	if($category == 'T') {
		$teachers = TeacherList($letter, $country, $classmate, $fl_usuario);
		echo json_encode((Object) array("teachers" => $teachers));
	}
	if($category == 'S') {
		$students = StudentList($letter, $country, $program, $classmate, $fl_usuario);
		echo json_encode((Object) array("students" => $students));
	}
	if($category == '0'){
		$admins = AdministradorList($fl_usuario);
		$teachers = TeacherList($letter, $country, $classmate, $fl_usuario);
		$students = StudentList($letter, $country, $program, $classmate, $fl_usuario);

		$all = array("teachers" => $teachers, "students" => $students, "admins" => $admins);
        //$all = array("admins" => $admins);
		echo json_encode((Object)$all);
	}
  
  # Prepares the teacher list
	function AdministradorList($fl_usuario){
		
		$rs = AdminQuery($fl_usuario);
		
		$admins["size"] = array();
		$admins["list"] = array();

		for($i = 1; $row = RecuperaRegistro($rs); $i++){
			$fl_admin = $row[0];      
			$row1 = RecuperaValor("SELECT  nb_perfil FROM c_perfil WHERE fl_perfil=".PFL_ADMINISTRADOR);
            $ds_perfil = $row1[0];
            $fg_activo = $row[6];

           # Verificamos si esta actvado o desactivado
           $class = "busy";
           if(!empty($row[6]))
           $class = "online";
		   $class="";
		
			if(!empty($row[1]))
				$ds_ruta_avatar = "<img   src='".ObtenAvatarUsuario($fl_admin)."' class='img-circle   ".$class."' width='100' height='100'>";
			 else 
				$ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_T_AVATAR_DEF."' class='img-circle ".$class."' width='100' height='100'>";
			$ds_nombre = str_uso_normal($row[2]);		
			$ds_pais = str_uso_normal($row[4]);
            
            #Nombre del intituto
            $fl_instituto =$row[9];
            $nb_instituto = $row[10];

            # Default pais de la nstitucion
            if(empty($ds_pais)){
                $rows = RecuperaValor("SELECT b.nb_pais FROM c_instituto a, c_pais b WHERE a.fl_pais = b.fl_pais AND fl_instituto=".$fl_instituto);
                $ds_pais = $rows[0];
            }
      
      
      #Verificamos que se le haya enviado una invitacion.
      $Que="SELECT fg_enviado,fg_aceptado FROM  k_relacion_usuarios WHERE fl_usuario_origen=".$fl_usuario." AND fl_usuario_destinatario=".$fl_admin." OR fl_usuario_origen=".$fl_admin." AND fl_usuario_destinatario=".$fl_usuario."  ";
      $roq=RecuperaValor($Que);
      $fg_enviado=!empty($roq['fg_enviado'])?$roq['fg_enviado']:NULL;
      $fg_aceptado=!empty($roq['fg_aceptado'])?$roq['fg_aceptado']:NULL;
	  
       if($fg_enviado){
			$status="Enviado";
			$boton_para_enviar="hidden";
			$btn_enviado="";
			$fg_son_amigos="";
			$boton_para_enviar_sms="hidden";
			
			if($fg_aceptado){
			   $status="Aceptado";
			   $boton_para_enviar="hidden";
			   $btn_enviado="hidden";
			   $fg_son_amigos=1;
			   $boton_para_enviar_sms="";
			}
			
			
	    }else{
			$status="FaltaEnviar";
			$boton_para_enviar="";
			$btn_enviado="hidden";
	        $fg_son_amigos="";
			$boton_para_enviar_sms="hidden";
			
		}

			$admins["list"] += array(
				"fl_admin".$i => $fl_admin,
				"ds_profile".$i => $ds_perfil,
				"ds_avatar".$i => $ds_ruta_avatar, 
				"ds_name".$i => $ds_nombre,
				"status_solicitud".$i=> $status,
				"boton_para_enviar".$i=> $boton_para_enviar,
		        "btn_enviado".$i=> $btn_enviado,
				"boton_para_enviar_sms".$i=>$boton_para_enviar_sms,
				"fg_son_amigos".$i=>$fg_son_amigos,
				"ds_profession".$i => $nb_instituto,
				"ds_country".$i => $ds_pais
			);

		}
		$admins["size"] += array("count" => $i-1);
		return $admins;
	}
  
	# Prepares the teacher list
	function TeacherList($letter="", $country="", $classmate="", $fl_usuario){
		//$fl_mi_maestro = ObtenMaestroAlumno($fl_usuario);
		// if(!empty($classmate)){
			// $classmate = $fl_mi_maestro;
		// }

		$fl_mi_maestro = NULL;

		$rs = TeacherQuery($letter, $country, $fl_usuario);
		
		$teachers["size"] = array();
		$teachers["list"] = array();

		for($i = 1; $row = RecuperaRegistro($rs); $i++){
			
			$fl_maestro = !empty($row[0])?$row[0]:NULL;
			
			if($fl_maestro != $fl_mi_maestro) 
        		$ds_perfil = 'Teacher';
     		else
        		$ds_perfil = 'My Teacher';
      		
      		$fame = $row[11];
      		# Verificamos si esta actvado o desactivado
      		$class = "busy";
	 
			if(!empty($row[6]))
				$class = "online";

			$class="";
			if($fame==1)
				$ds_ruta_avatar = "<img src='".ObtenAvatarUsuario($fl_maestro)."' class='img-circle  ".$class."' width='100' height='100'>";
			else
		        $ds_ruta_avatar = "<img src='".ObtenAvatarUsrVanas($fl_maestro)."' class='img-circle  ".$class."' width='100' height='100'>";
			if(empty($ds_ruta_avatar))
				$ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_T_AVATAR_DEF."' class='img-circle  ".$class."' width='100' height='100'>";
			$ds_nombre = str_uso_normal($row[2]);
			if(!empty($row[3]))
				$ds_empresa = str_uso_normal($row[3]);
			else
				$ds_empresa = "(Not defined)";
			$ds_pais = str_uso_normal($row[4]);
			#Nombre del intituto
			$fl_instituto = $row[9];
			if($fame==1)
			$nb_instituto = ObtenNameInstituto($fl_instituto);
			else
			$nb_instituto = ObtenEtiqueta(2010);
			# Default pais de la nstitucion
			if(empty($ds_pais)){
				$rows = RecuperaValor("SELECT b.nb_pais FROM c_instituto a, c_pais b WHERE a.fl_pais = b.fl_pais AND fl_instituto=".$fl_instituto);
			$ds_pais = $rows[0];
		}
	  
	  
      #Verificamos que se le haya enviado una invitacion.
      $Que="SELECT fg_enviado,fg_aceptado FROM  k_relacion_usuarios WHERE fl_usuario_origen=".$fl_usuario." AND fl_usuario_destinatario=".$fl_maestro." OR fl_usuario_origen=".$fl_maestro." AND fl_usuario_destinatario=".$fl_usuario."  ";
      $roq=RecuperaValor($Que);
      $fg_enviado=!empty($roq['fg_enviado'])?$roq['fg_enviado']:NULL;
      $fg_aceptado=!empty($roq['fg_aceptado'])?$roq['fg_aceptado']:NULL;
	  
       if($fg_enviado){
			$status="Enviado";
			$boton_para_enviar="hidden";
			$btn_enviado="";
			$fg_son_amigos="";
			$boton_para_enviar_sms="hidden";
			
			if($fg_aceptado){
			   $status="Aceptado";
			   $boton_para_enviar="hidden";
			   $btn_enviado="hidden";
			   $fg_son_amigos=1;
			   $boton_para_enviar_sms="";
			}
			
			
	    }else{
			$status="FaltaEnviar";
			$boton_para_enviar="";
			$btn_enviado="hidden";
	        $fg_son_amigos="";
			$boton_para_enviar_sms="hidden";
			
		}
	  


			$teachers["list"] += array(
				"fl_maestro".$i => $fl_maestro,
				"ds_profile".$i => $ds_perfil,
				"ds_avatar".$i => $ds_ruta_avatar, 
				"ds_name".$i => $ds_nombre,
				"status_solicitud".$i=> $status,
				"boton_para_enviar".$i=> $boton_para_enviar,
		        "btn_enviado".$i=> $btn_enviado,
				"boton_para_enviar_sms".$i=>$boton_para_enviar_sms,
				"fg_son_amigos".$i=>$fg_son_amigos,
				"ds_profession".$i => $nb_instituto,
				"ds_country".$i => $ds_pais
			);
		}
		$teachers["size"] += array("count" => $i-1);
		return $teachers;
	}

	# Prepares the student list
	function StudentList($letter="", $country="", $program="", $classmate="", $fl_usuario){		
		$rs = StudentQuery($letter, $country, $program, $fl_usuario);
		
		$students["size"] = array();
		$students["list"] = array();

		for($i = 1; $row = RecuperaRegistro($rs); $i++){
			$fl_alumno = $row[0];
      $edad = $row[11];
      $fame = $row[12];
      
			if($fl_alumno <> $fl_usuario) {
        $ds_perfil = 'Student';
      } else {
	      $ds_perfil = 'Me!';
	    }
      
      # Verificamos si esta actvado o desactivado
      $class = "busy";
      if(!empty($row[6]))
        $class = "online";
		
		$class="";
      if($fame==1){        
        $ds_ruta_avatar = "<img src='".ObtenAvatarUsuario($fl_alumno)."' class='img-circle ".$class."' width='100' height='100'>";
      }
      else{
        $ds_ruta_avatar = "<img src='".ObtenAvatarUsrVanas($fl_alumno)."' class='img-circle ".$class."' width='100' height='100'>";
      }
      if(empty($ds_ruta_avatar))
        $ds_ruta_avatar =  "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."' class='img-circle ".$class."' width='100' height='100'>";
			$ds_nombre = str_uso_normal($row[2]);
			$ds_pais = str_uso_normal($row[3]);
      # Default pais de la nstitucion
      if($fame==1){
        if(empty($ds_pais)){
          $rows = RecuperaValor("SELECT b.nb_pais FROM c_instituto a, c_pais b WHERE a.fl_pais = b.fl_pais AND fl_instituto=".$row[8]);
          $ds_pais = $rows[0];
        }
      }
      # Nombre de la institucion
      if($fame==1)
        $nb_instituto = ObtenNameInstituto($row[8]);      
      else
        $nb_instituto = ObtenEtiqueta(2010);       
      
      # Blocking Last Name      
      $ds_nombre = ObtenNombreUsuario($fl_alumno, $fl_usuario);

	  $ds_nombre = str_replace("*", " ", $ds_nombre);
      $ds_nombre = str_replace("‡", " ", $ds_nombre);
      $ds_nombre = str_replace("Ã‘", "&ntilde;", $ds_nombre);
      $ds_nombre = str_replace("-", " ", $ds_nombre);
      $ds_nombre = str_replace("?", " ", $ds_nombre); 
	   
      #Verificamos que se le haya enviado una invitacion.
      $Que="SELECT fg_enviado,fg_aceptado FROM  k_relacion_usuarios WHERE fl_usuario_origen=".$fl_usuario." AND fl_usuario_destinatario=".$fl_alumno." OR fl_usuario_origen=".$fl_alumno." AND fl_usuario_destinatario=".$fl_usuario." ";
      $roq=RecuperaValor($Que);
      $fg_enviado=!empty($roq['fg_enviado'])?$roq['fg_enviado']:NULL;
      $fg_aceptado=!empty($roq['fg_aceptado'])?$roq['fg_aceptado']:NULL;
	  
       if($fg_enviado){
			$status="Enviado";
			$boton_para_enviar="hidden";
			$btn_enviado="";
			$fg_son_amigos="";
			$boton_para_enviar_sms="hidden";
			
			if($fg_aceptado){
			   $status="Aceptado";
			   $boton_para_enviar="hidden";
			   $btn_enviado="hidden";
			   $fg_son_amigos=1;
			   $boton_para_enviar_sms="";
			}
			
			
	    }else{
			$status="FaltaEnviar";
			$boton_para_enviar="";
			$btn_enviado="hidden";
	        $fg_son_amigos="";
			$boton_para_enviar_sms="hidden";
			
		}
	  
	 // $fl_alumno="";
	 // $ds_perfil="";
	 // $ds_ruta_avatar;
	 // $ds_nombre="Estudent";
	  //$ds_pais="";
	  
	 // $edad="";
	//  $status="";
	 // $boton_para_enviar="";
	 // $btn_enviado="";
	 // $boton_para_enviar_sms="";
	 // $fg_son_amigos="";
	 // $fame="";
	  
      $students["list"] += array(
        "fl_alumno".$i => $fl_alumno,
        "ds_profile".$i => $ds_perfil,
        "ds_avatar".$i => $ds_ruta_avatar, 
        "ds_name".$i => $ds_nombre,
        "ds_apaterno".$i => "",
        "ds_profession".$i => $nb_instituto,
        "ds_country".$i => $ds_pais,
        "edad".$i => $edad,
		"status_solicitud".$i=> $status,
		"boton_para_enviar".$i=> $boton_para_enviar,
		"btn_enviado".$i=> $btn_enviado,
		"boton_para_enviar_sms".$i=>$boton_para_enviar_sms,
		"fg_son_amigos".$i=>$fg_son_amigos,
        "fame".$i => $fame
      );
     
		}
		$students["size"] += array("count" => $i-1);
		return $students;
	}
  
 
?>
