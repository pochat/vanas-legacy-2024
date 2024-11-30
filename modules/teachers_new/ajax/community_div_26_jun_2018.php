<?php
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

	# Recibe parametros generales
 	$category = RecibeParametroHTML('category');
	$letter = RecibeParametroHTML('letter');
	$program = RecibeParametroNumerico('program');
	$country = RecibeParametroNumerico('country');
	$classmate = RecibeParametroNumerico('classmate');

	if($category == 'T') {
		$teachers = TeacherList($letter, $country, $classmate, $fl_usuario);
		echo json_encode((Object) array("teachers" => $teachers) );
	}
	if($category == 'S') {
		$students = StudentList($letter, $country, $program, $classmate, $fl_usuario);
		echo json_encode((Object) array("students" => $students) );
	}
	if($category == '0'){
		$teachers = TeacherList($letter, $country, $classmate, $fl_usuario);
		$students = StudentList($letter, $country, $program, $classmate, $fl_usuario);

		$all = array("teachers" => $teachers, "students" => $students);
		echo json_encode((Object)$all);
	}

	# Prepares the teacher list
	function TeacherList($letter="", $country="", $classmate="", $fl_usuario=""){
		if(!empty($classmate)){
			$classmate = $fl_usuario;
		}
		$rs = TeacherQuery($letter, $country, $classmate);
		
		$teachers["size"] = array();
		$teachers["list"] = array();

		for($i = 1; $row = RecuperaRegistro($rs); $i++){
			$fl_maestro = $row[0];
			if($fl_maestro <> $fl_usuario) {
        $ds_perfil = 'Teacher';
      }
      else {
        $ds_perfil = 'Me!';
      }
			if(!empty($row[1]))
				$ds_ruta_avatar = "<img src='".PATH_MAE_IMAGES."/avatars/".$row[1]."'>";
			 else 
				$ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_T_AVATAR_DEF."'>";
			$ds_nombre = str_uso_normal($row[2]);
			if(!empty($row[3]))
				$ds_empresa = str_uso_normal($row[3]);
			else
				$ds_empresa = "(Not defined)";
			$ds_pais = str_uso_normal($row[4]);

			$teachers["list"] += array(
				"fl_maestro".$i => $fl_maestro,
				"ds_profile".$i => $ds_perfil,
				"ds_avatar".$i => $ds_ruta_avatar, 
				"ds_name".$i => $ds_nombre,
				"ds_profession".$i => $ds_empresa,
				"ds_country".$i => $ds_pais
			);
		}
		$teachers["size"] += array("count" => $i-1);
		return $teachers;
	}

	# Prepares the student list
	function StudentList($letter="", $country="", $program="", $classmate="", $fl_usuario=""){

		$students["size"] = array();
		$students["list"] = array();

		if(!empty($classmate)){
			# There could be multiple groups that a teacher is teaching in one term
			$Query = "SELECT fl_grupo FROM c_grupo WHERE fl_maestro=$fl_usuario";
			$rs2 = EjecutaQuery($Query);

			$total = 1;
			while($row2=RecuperaRegistro($rs2)){
				$classmate = $row2[0];
				$rs = StudentQuery($letter, $country, $program, $classmate);

				$ds_perfil = 'My Student';
				while($row = RecuperaRegistro($rs)){
					$fl_alumno = $row[0];
					if(!empty($row[1]))
						$ds_ruta_avatar = "<img src='".PATH_ALU_IMAGES."/avatars/".$row[1]."'>";
					 else 
						$ds_ruta_avatar =  "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."'>";
					$ds_nombre = str_uso_normal($row[2]);
					$nb_programa = str_uso_normal($row[3]);
					$ds_pais = str_uso_normal($row[4]);

					$students["list"] += array(
						"fl_alumno".$total => $fl_alumno,
						"ds_profile".$total => $ds_perfil,
						"ds_avatar".$total => $ds_ruta_avatar, 
						"ds_name".$total => $ds_nombre,
						"ds_profession".$total => $nb_programa,
						"ds_country".$total => $ds_pais
					);
					$total++;
				}
			}
		} else {
			$rs = StudentQuery($letter, $country, $program);

			$total = 1;
			while($row = RecuperaRegistro($rs)){
				$fl_alumno = $row[0];
				$fl_maestro = ObtenMaestroAlumno($fl_alumno);
				if($fl_maestro <> $fl_usuario) {
	        $ds_perfil = 'Student';
	      }
	      else {
	        $ds_perfil = 'My Student';
	      }
				if(!empty($row[1]))
					$ds_ruta_avatar = "<img src='".PATH_ALU_IMAGES."/avatars/".$row[1]."'>";
				 else 
					$ds_ruta_avatar =  "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."'>";
				$ds_nombre = str_uso_normal($row[2]);
				$nb_programa = str_uso_normal($row[3]);
				$ds_pais = str_uso_normal($row[4]);

				$students["list"] += array(
					"fl_alumno".$total => $fl_alumno,
					"ds_profile".$total => $ds_perfil,
					"ds_avatar".$total => $ds_ruta_avatar, 
					"ds_name".$total => $ds_nombre,
					"ds_profession".$total => $nb_programa,
					"ds_country".$total => $ds_pais
				);
				$total++;
			}
		}
		$students["size"] += array("count" => $total-1);
		return $students;
	}
?>