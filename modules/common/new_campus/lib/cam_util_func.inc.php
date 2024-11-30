<?php
	# Query for list of teachers used in community_div.php and contacts.php
	function TeacherQuery($letter="", $country="", $classmate="") {
		$Query  = "SELECT a.fl_maestro, a.ds_ruta_avatar, ";
		$concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
		$Query .= ConcatenaBD($concat)." 'ds_nombre', a.ds_empresa, ds_pais, b.no_accesos ";
		$Query .= "FROM c_maestro a, c_usuario b, c_pais c ";
		$Query .= "WHERE a.fl_maestro=b.fl_usuario ";
		$Query .= "AND a.fl_pais=c.fl_pais ";
		$Query .= "AND b.fg_activo='1' ";
		if(!empty($letter))
			$Query .= "AND ASCII(UCASE(b.ds_nombres))=".ord($letter)." ";
		if(!empty($country))
			$Query .= "AND a.fl_pais=$country ";
		if(!empty($classmate))
		//$Query .= "ORDER BY b.ds_nombres";
			$Query .= "AND a.fl_maestro=$classmate ";
		$Query .= "ORDER BY b.no_accesos DESC";
		$rs = EjecutaQuery($Query);

		return $rs;
	}

	# Query for list of students used in community_div.php and contacts.php
	function StudentQuery($letter="", $country="", $program="", $classmate="",$fg_grupo_global="",$fg_comunity="") {

        $Query =" ";
		if($fg_grupo_global==1){
            $Query.="(";
        }

		$Query .= "SELECT a.fl_alumno, a.ds_ruta_avatar, ";
		$concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
		$Query .= ConcatenaBD($concat)." 'ds_nombre', e.nb_programa, d.ds_pais, a.fl_alumno, b.no_accesos,b.fg_genero,TIMESTAMPDIFF(YEAR,b.fe_nacimiento,CURDATE()) AS edad ,'0'fg_clase_glo ";
        if(empty($fg_comunity)){
            $Query .= ",f.fl_grupo  ";
        }
		$Query .= "FROM c_alumno a, c_usuario b, k_ses_app_frm_1 c, c_pais d, c_programa e ";
        if(empty($fg_comunity)){
            $Query .=" , k_alumno_grupo f ";
        }
		$Query .= "WHERE a.fl_alumno=b.fl_usuario ";
		$Query .= "AND b.cl_sesion=c.cl_sesion ";
		$Query .= "AND c.ds_add_country=d.fl_pais ";
		$Query .= "AND c.fl_programa=e.fl_programa ";
        if(empty($fg_comunity)){
            $Query .= "AND a.fl_alumno=f.fl_alumno ";
        }
        $Query .= "AND b.fg_activo='1' ";
		if(!empty($letter))
			$Query .= "AND ASCII(UCASE(b.ds_nombres))=".ord($letter)." ";
		if(!empty($country))
			$Query .= "AND d.fl_pais=$country ";
		if(!empty($program))
			$Query .= "AND c.fl_programa=$program ";
        if(empty($fg_comunity)){
            if(!empty($classmate)){
                $Query .= "AND f.fl_grupo=$classmate ";
            }
        }

        if($fg_grupo_global==1){
            $Query.="AND f.fg_grupo_global<>'1' ";
        }
		//$Query .= "ORDER BY b.ds_nombres";
		$Query .= "ORDER BY b.no_accesos DESC";

        if($fg_grupo_global==1){

            $Query.=" )UNION( ";
            $Query.="
                     SELECT a.fl_alumno,a.ds_ruta_avatar,CONCAT(b.ds_nombres, ' ', b.ds_apaterno) 'ds_nombre' ,
                     ''nb_programa,d.ds_pais,a.fl_alumno,b.no_accesos,b.fg_genero,
                     TIMESTAMPDIFF(YEAR,b.fe_nacimiento,CURDATE()) AS edad ,'1'fg_clase_glo ,p.fl_grupo
                     FROM c_alumno a
                     JOIN c_usuario b ON a.fl_alumno=b.fl_usuario
                     JOIN k_alumno_grupo f ON f.fl_alumno=a.fl_alumno
                     JOIN c_grupo p ON p.fl_grupo=f.fl_grupo
                     JOIN k_ses_app_frm_1 c on c.cl_sesion =b.cl_sesion
                     JOIN c_pais d ON d.fl_pais=c.ds_add_country
                     AND f.fl_grupo=$classmate
                     WHERE b.fg_activo='1' AND p.fg_grupo_global='1'
                     ORDER BY b.no_accesos DESC


            ";
            $Query.=" ) ";
        }



		$rs = EjecutaQuery($Query);

		return $rs;
	}
	# Get all the sessions for a teacher, organized by groups, header.php (not used yet)
	function GetTeacherSessions($fl_maestro){
		# Find all the active groups for the teacher
		$Query  = "SELECT DISTINCT(a.fl_grupo) ";
		$Query .= "FROM c_grupo a ";
		$Query .= "LEFT JOIN k_alumno_grupo b ON b.fl_grupo=a.fl_grupo ";
		$Query .= "LEFT JOIN c_usuario c ON c.fl_usuario=b.fl_alumno ";
		$Query .= "WHERE fl_maestro=$fl_maestro ";
		$Query .= "AND c.fg_activo='1'";
		$rs = EjecutaQuery($Query);

		for($i=0; $row=RecuperaRegistro($rs); $i++){
			$fl_grupo = $row[0];

			$liveSession = ObtainFechaGroupSessionTeacher($fl_maestro, $fl_grupo);
			$folioClase = ObtainFolioGroupSessionTeacher($fl_maestro, $fl_grupo);

			$liveSessionTime = $liveSession["actual"];
	    $liveSessionStart = "";
	    $liveSessionClose = "";
	    $liveSessionReadable = $liveSession["readable"];

	    // If class is unavailable
	    if(empty($liveSession["actual"])){
	    	$liveSessionLink = "";
	    } else {
	    	// Else there is a class coming up
	    	$liveSessionLink = "href='../liveclass/LiveSession.php?folio=$folioClase'";
	    	$liveSessionStart = $liveSession["early"];
	    	$liveSessionClose = $liveSession["after"];
	    }

	    $result["group".$i] = array(
	    	"time" => $liveSessionTime,
	    	"readable" => $liveSessionReadable,
	    	"link" => $liveSessionLink,
	    	"start" => $liveSessionStart,
	    	"close" => $liveSessionClose
	    );
		}
		$result["size"] = array("total" => $i);

		echo json_encode((Object) $result);
	}

	# Find Teacher's live session by group
	function ObtainGroupSessionTeacher($p_teacher, $p_grupo) {
  	$access_early = ObtenConfiguracion(34);
  	$access_after = ObtenConfiguracion(35);
	  $tolerancia_link = ObtenConfiguracion(36);
	  $diferencia = RecuperaDiferenciaGMT( );
    # Dividimos las horas
    $enteros = intval($diferencia);
    $decimales = ($diferencia-intval($diferencia))*100;
    if(!empty($decimales))
      $decimales = 60/(100/$decimales);
    else
      $decimales = 0;

	  $Query  = "SELECT fl_clase, fe_clase, ";
	  $Query .= "UNIX_TIMESTAMP( DATE_SUB(fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
		$Query .= "UNIX_TIMESTAMP( DATE_ADD(fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
    if($decimales>0)
      $Query .= "DATE_FORMAT(DATE_ADD(DATE_ADD(fe_clase, INTERVAL $enteros HOUR), INTERVAL $decimales MINUTE), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
    else
      $Query .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
	  $Query .= "FROM k_clase a, c_grupo b ";
	  $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
	  $Query .= "AND b.fl_maestro=$p_teacher ";
	  $Query .= "AND b.fl_grupo=$p_grupo ";
	  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
	  $Query .= "ORDER BY fe_clase";
	  $row = RecuperaValor($Query);
	  return $row;
	}



	# Live session time
	function ObtainFechaGroupSessionTeacher($p_teacher, $p_grupo) {
	  $row = ObtainGroupSessionTeacher($p_teacher, $p_grupo);
	  $fe_clase = !empty($row['fe_clase'])?$row['fe_clase']:NULL;
	  $fe_early = !empty($row['fe_early'])?$row['fe_early']:NULL;
	  $fe_after = !empty($row['fe_after'])?$row['fe_after']:NULL;
	  $fe_readable = !empty($row['fe_readable'])?$row['fe_readable']:NULL;
	  return array(
	  	"actual" => $fe_clase,
	  	"early" => $fe_early,
	  	"after" => $fe_after,
	  	"readable" => $fe_readable
	  );
	}

    # Live session time
	function ObtainFechaGroupSessionTeacherGG($p_teacher, $p_grupo) {
        $row = ObtainGroupSessionTeacherGG($p_teacher, $p_grupo);
        $fe_clase = !empty($row['fe_clase'])?$row['fe_clase']:NULL;
        $fe_early = !empty($row['fe_early'])?$row['fe_early']:NULL;
        $fe_after = !empty($row['fe_after'])?$row['fe_after']:NULL;
        $fe_readable = !empty($row['fe_readable'])?$row['fe_readable']:NULL;
        return array(
            "actual" => $fe_clase,
            "early" => $fe_early,
            "after" => $fe_after,
            "readable" => $fe_readable
        );
	}



	# Live session id
	function ObtainFolioGroupSessionTeacher($p_teacher, $p_grupo) {
	  $row = ObtainGroupSessionTeacher($p_teacher, $p_grupo);
	  $result = !empty($row["fl_clase"])?$row["fl_clase"]:NULL;
	  return $result;
	}

    # Live session id
	function ObtainFolioGroupSessionTeacherGG($p_teacher, $p_grupo) {
        $row = ObtainGroupSessionTeacherGG($p_teacher, $p_grupo);
        $result = !empty($row["fl_clase_grupo"])?$row["fl_clase_grupo"]:NULL;
        return $result;
	}

    # Find Teacher's live session by group
	function ObtainGroupSessionTeacherGG($p_teacher, $p_grupo) {
        $access_early = ObtenConfiguracion(34);
        $access_after = ObtenConfiguracion(35);
        $tolerancia_link = ObtenConfiguracion(36);
        $diferencia = RecuperaDiferenciaGMT( );
        # Dividimos las horas
        $enteros = intval($diferencia);
        $decimales = ($diferencia-intval($diferencia))*100;
        if(!empty($decimales))
            $decimales = 60/(100/$decimales);
        else
            $decimales = 0;

        $Query  = "SELECT fl_clase_grupo, fe_clase, ";
        $Query .= "UNIX_TIMESTAMP( DATE_SUB(fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
		$Query .= "UNIX_TIMESTAMP( DATE_ADD(fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
        if($decimales>0)
            $Query .= "DATE_FORMAT(DATE_ADD(DATE_ADD(fe_clase, INTERVAL $enteros HOUR), INTERVAL $decimales MINUTE), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
        else
            $Query .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
        $Query .= "FROM k_clase_grupo a, c_grupo b ";
        $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
        $Query .= "AND a.fl_maestro=$p_teacher ";
        $Query .= "AND b.fl_grupo=$p_grupo ";
        $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
        $Query .= "ORDER BY fe_clase";
        $row = RecuperaValor($Query);
        return $row;
	}



	# Get the student's profile
	function GetStudentProfile($fl_alumno){
		$ds_nombres = ObtenNombreUsuario($fl_alumno);
		$ds_ruta_avatar = ObtenAvatarUsuario($fl_alumno);
		$fl_programa = ObtenProgramaAlumno($fl_alumno);
    $nb_programa = ObtenNombreProgramaAlumno($fl_alumno);
    $no_grado = ObtenGradoAlumno($fl_alumno);
    $no_semana = ObtenSemanaActualAlumno($fl_alumno);
    $ds_titulo = ObtenTituloLeccion($fl_programa, $no_grado, $no_semana);
    $ds_status = ObtenStatusAlumno($fl_alumno);
    # get teacher of student
    $fl_grupo = ObtenGrupoAlumno($fl_alumno);
    $row = RecuperaValor("SELECT fl_maestro FROM c_grupo WHERE fl_grupo=$fl_grupo");
    $ds_teacher = ObtenNombreUsuario($row[0]);

    $result["profile"] = array(
    	"name" => $ds_nombres,
    	"avatar" => $ds_ruta_avatar,
    	"course" => $nb_programa,
    	"term" => $no_grado,
    	"week" => $no_semana,
    	"lesson" => $ds_titulo,
    	"status" => $ds_status,
      "teacher" => $ds_teacher
    );
		echo json_encode((Object) $result);
	}

	# Get the student's live session
	function GetStudentSession($fl_alumno){
    $no_semanaAux = ObtenSemanaLiveSessionStudent($fl_alumno);
		$grupo = ObtenGrupoAlumno($fl_alumno);
    $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semanaAux);
    $liveSession = ObtainLiveSessionStudent($grupo, $fl_semana);
    $folioClase = ObtenFolioLiveSessionStudent($grupo, $fl_semana);

    // Default live session variables
    $liveSessionExists = false;
    $liveSessionTime = $liveSession["actual"];
    $liveSessionReadable = $liveSession["readable"];
    $liveSessionLink = "";
    $liveSessionStart = "";
    $liveSessionClose = "";

		// If class is available
    if(!empty($liveSession["actual"])){
    	$liveSessionExists = true;
    	$liveSessionLink = "href='../liveclass/LiveSession.php?folio=$folioClase'";
    	$liveSessionStart = $liveSession["early"];
    	$liveSessionClose = $liveSession["after"];
    }

    if($liveSession['type']=='lecture'){
        $liveSessionExists = true;
        $liveSessionStart = $liveSession["early"];
        $liveSessionClose = $liveSession["after"];
        $liveSessionLink = "href='../liveclass/LiveSession.php?folio=$folioClase'";

    }
    if($liveSession['type']=='review'){
        $liveSessionExists = true;
        $liveSessionStart = $liveSession["early"];
        $liveSessionClose = $liveSession["after"];
        $liveSessionLink = "href='../liveclass/LiveSession_gg.php?folio=".$liveSession['fl_clase']."'";
    }
    if($liveSession['type']=='global'){

        $liveSessionLink = "href='../liveclass/LiveSession_gc.php?folio=".$liveSession['fl_clase']."'";
    }



        $result["session"] = array(
    	    "exists" => $liveSessionExists,
    	    "time" => $liveSessionTime,
    	    "readable" => $liveSessionReadable,
            "titulo"=>$liveSession['titulo'],
    	    "link" => $liveSessionLink,
            "type" =>$liveSession['type'],
    	    "start" => array(
  			    "seconds" => (int)$liveSessionStart,
  			    "milliseconds" => (int)$liveSessionStart * 1000
    	    ),
    	    "close" => array(
  			    "seconds" => (int)$liveSessionClose,
  			    "milliseconds" => (int)$liveSessionClose * 1000
    	    )
        );
		    echo json_encode((Object) $result);
	}

    # Get the student's live session
	function GetStudentSessionGlobalClass($fl_alumno){
        $no_semanaAux = ObtenSemanaLiveSessionStudent($fl_alumno);
		$grupo = ObtenGrupoAlumno($fl_alumno);
        $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semanaAux);
        $liveSession = ObtainLiveSessionGlobalClassStudent($grupo, $fl_semana);
        $folioClase = ObtenFolioLiveSessionStudent($grupo, $fl_semana);

        // Default live session variables
        $liveSessionExists = false;
        $liveSessionTime = $liveSession["actual"];
        $liveSessionReadable = $liveSession["readable"];
        $liveSessionLink = "";
        $liveSessionStart = "";
        $liveSessionClose = "";

		// If class is available
        if(!empty($liveSession["actual"])){
            $liveSessionExists = true;
            $liveSessionLink = "href='../liveclass/LiveSession.php?folio=$folioClase'";
            $liveSessionStart = $liveSession["early"];
            $liveSessionClose = $liveSession["after"];
        }

        if($liveSession['type']=='lecture'){


            $liveSessionLink = "href='../liveclass/LiveSession.php?folio=$folioClase'";
        }
        if($liveSession['type']=='review'){

            $liveSessionLink = "href='../liveclass/LiveSession_gg.php?folio=".$liveSession['fl_clase']."'";
        }
        if($liveSession['type']=='global'){

            $liveSessionLink = "href='../liveclass/LiveSession_gc.php?folio=".$liveSession['fl_clase']."'";
        }



        $result["session"] = array(
            "exists" => $liveSessionExists,
            "time" => $liveSessionTime,
            "readable" => $liveSessionReadable,
            "titulo"=>$liveSession['titulo'],
            "link" => $liveSessionLink,
            "type" =>'global',
            "start" => array(
                  "seconds" => (int)$liveSessionStart,
                  "milliseconds" => (int)$liveSessionStart * 1000
            ),
            "close" => array(
                  "seconds" => (int)$liveSessionClose,
                  "milliseconds" => (int)$liveSessionClose * 1000
            )
        );
		echo json_encode((Object) $result);
	}

    # Get the student's live session
	function GetStudentSessionLectureClass($fl_alumno){
        $no_semanaAux = ObtenSemanaLiveSessionStudent($fl_alumno);
		$grupo = ObtenGrupoAlumno($fl_alumno);
        $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semanaAux);
        $liveSession = ObtainLiveSessionLectureClassStudent($grupo, $fl_semana);
        $folioClase = ObtenFolioLiveSessionStudent($grupo, $fl_semana);

        // Default live session variables
        $liveSessionExists = false;
        $liveSessionTime = $liveSession["actual"];
        $liveSessionReadable = $liveSession["readable"];
        $liveSessionLink = "";
        $liveSessionStart = "";
        $liveSessionClose = "";

		// If class is available
        if(!empty($liveSession["actual"])){

            $liveSessionLink = "href='../liveclass/LiveSession.php?folio=$folioClase'";
            $liveSessionStart = $liveSession["early"];
            $liveSessionClose = $liveSession["after"];
        }

        if($liveSession['type']=='lecture'){

            $liveSessionExists = true;
            $liveSessionLink = "href='../liveclass/LiveSession.php?folio=$folioClase'";
        }
        if($liveSession['type']=='review'){

            $liveSessionLink = "href='../liveclass/LiveSession_gg.php?folio=".$liveSession['fl_clase']."'";
        }
        if($liveSession['type']=='global'){

            $liveSessionLink = "href='../liveclass/LiveSession_gc.php?folio=".$liveSession['fl_clase']."'";
        }



        $result["session"] = array(
            "exists" => $liveSessionExists,
            "time" => $liveSessionTime,
            "readable" => $liveSessionReadable,
            "titulo"=>$liveSession['titulo'],
            "link" => $liveSessionLink,
            "type" =>$liveSession['type'],
            "start" => array(
                  "seconds" => (int)$liveSessionStart,
                  "milliseconds" => (int)$liveSessionStart * 1000
            ),
            "close" => array(
                  "seconds" => (int)$liveSessionClose,
                  "milliseconds" => (int)$liveSessionClose * 1000
            )
        );
		echo json_encode((Object) $result);
	}

    # Get the student's live session
	function GetStudentSessionReviewClass($fl_alumno){
        $no_semanaAux = ObtenSemanaLiveSessionStudent($fl_alumno);
		$grupo = ObtenGrupoAlumno($fl_alumno);
        $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semanaAux);
        $liveSession = ObtainLiveSessionReviewClassStudent($grupo, $fl_semana);
        $folioClase = ObtenFolioLiveSessionStudent($grupo, $fl_semana);

        // Default live session variables
        $liveSessionExists = false;
        $liveSessionTime = $liveSession["actual"];
        $liveSessionReadable = $liveSession["readable"];
        $liveSessionLink = "";
        $liveSessionStart = "";
        $liveSessionClose = "";

		// If class is available
        if(!empty($liveSession["actual"])){

            $liveSessionLink = "href='../liveclass/LiveSession.php?folio=$folioClase'";
            $liveSessionStart = $liveSession["early"];
            $liveSessionClose = $liveSession["after"];
        }

        if($liveSession['type']=='lecture'){


            $liveSessionLink = "href='../liveclass/LiveSession.php?folio=$folioClase'";
        }
        if($liveSession['type']=='review'){
            $liveSessionExists = true;
            $liveSessionLink = "href='../liveclass/LiveSession_gg.php?folio=".$liveSession['fl_clase']."'";
        }
        if($liveSession['type']=='global'){

            $liveSessionLink = "href='../liveclass/LiveSession_gc.php?folio=".$liveSession['fl_clase']."'";
        }



        $result["session"] = array(
            "exists" => $liveSessionExists,
            "time" => $liveSessionTime,
            "readable" => $liveSessionReadable,
            "titulo"=>$liveSession['titulo'],
            "link" => $liveSessionLink,
            "type" =>$liveSession['type'],
            "start" => array(
                  "seconds" => (int)$liveSessionStart,
                  "milliseconds" => (int)$liveSessionStart * 1000
            ),
            "close" => array(
                  "seconds" => (int)$liveSessionClose,
                  "milliseconds" => (int)$liveSessionClose * 1000
            )
        );
		echo json_encode((Object) $result);
	}

	# Find student's live session
	function ObtainLiveSessionStudent($p_grupo, $p_semana) {
		$access_early = ObtenConfiguracion(34);
  	    #$access_after = ObtenConfiguracion(35);
	  #$tolerancia_link = ObtenConfiguracion(36);
        $access_after = "30";
        $tolerancia_link = "30";
	  $fl_alumno = ValidaSesion(False);
	  $diferencia = RecuperaDiferenciaGMT( );

      $Query ="(";

      $Query .= "SELECT a.fe_clase, ";
		$Query .= "UNIX_TIMESTAMP( DATE_SUB(a.fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
		$Query .= "UNIX_TIMESTAMP( DATE_ADD(a.fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
	  $Query .= "DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %h:%i %p') fe_readable ";
      $Query .=",'lecture' type,a.fl_clase,d.ds_titulo nb_clase ";
	  $Query .= "FROM k_clase a JOIN c_grupo b ON b.fl_grupo=a.fl_grupo
                 JOIN k_semana c ON c.fl_semana=a.fl_semana
                 JOIN c_leccion d ON d.fl_leccion=c.fl_leccion ";
	  $Query .= "WHERE a.fl_grupo=$p_grupo ";
      if(($fl_alumno==7228)||($fl_alumno==7246)){
          $Query.=" ";
      }else{
          $Query .= "AND a.fl_semana=$p_semana ";
      }
	  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(a.fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
	  $Query .= "ORDER BY a.fe_clase";

      $Query .=")UNION ALL (";
/*
      $Query .=" SELECT kcg.fe_clase,";
      $Query .= "UNIX_TIMESTAMP( DATE_SUB(kcg.fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
      $Query .= "UNIX_TIMESTAMP( DATE_ADD(kcg.fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
	  $Query .= "DATE_FORMAT(DATE_ADD(kcg.fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %h:%i %p') fe_readable ";
      $Query .=",'global' type, kcg.fl_clase_cg, CONCAT('Global Class',' ',kcg.ds_titulo) nb_clase  ";
      $Query .="FROM c_usuario cus, k_ses_app_frm_1 frm
                JOIN k_curso_cg  kcc ON(kcc.fl_programa=frm.fl_programa)
                JOIN c_clase_global cc ON(cc.fl_clase_global = kcc.fl_clase_global)
                LEFT JOIN k_clase_cg kcg ON ( kcg.fl_clase_global = cc.fl_clase_global )
                WHERE cus.cl_sesion = frm.cl_sesion AND fg_activo='1'  AND cus.fl_usuario = $fl_alumno ";
      $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(kcg.fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
	  $Query .= "ORDER BY kcg.fe_clase ";

      $Query .=")UNION ALL (";
*/
      $Query .="SELECT a.fe_clase, ";
      $Query .= "UNIX_TIMESTAMP( DATE_SUB(a.fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
      $Query .= "UNIX_TIMESTAMP( DATE_ADD(a.fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
	  $Query .= "DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %h:%i %p') fe_readable ";
      $Query .=",'review' type,a.fl_clase_grupo,a.nb_clase  ";
      $Query .= "FROM k_clase_grupo a
                JOIN k_alumno_grupo b ON b.fl_grupo = a.fl_grupo
                WHERE b.fl_alumno=$fl_alumno ";
      $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(a.fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
	  $Query .= "ORDER BY a.fe_clase ";
      $Query .=")ORDER BY fe_clase ASC ";

      $row = RecuperaValor($Query);



      return array(
	  	"actual" => $row[0],
	  	"early" => $row[1],
	  	"after" => $row[2],
	  	"readable" => $row[3],
        "type" => $row[4],
        "fl_clase"=>$row[5],
        "titulo"=>$row[6]
	  );
	}

    function ObtainLiveSessionGlobalClassStudent($p_grupo, $p_semana) {
		$access_early = ObtenConfiguracion(34);
        #$access_after = ObtenConfiguracion(35);
        #$tolerancia_link = ObtenConfiguracion(36);
        $access_after = "30";
        $tolerancia_link = "30";
        $fl_alumno = ValidaSesion(False);
        $diferencia = RecuperaDiferenciaGMT( );

        $Query =" SELECT kcg.fe_clase,";
        $Query .= "UNIX_TIMESTAMP( DATE_SUB(kcg.fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
        $Query .= "UNIX_TIMESTAMP( DATE_ADD(kcg.fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
        $Query .= "DATE_FORMAT(DATE_ADD(kcg.fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %h:%i %p') fe_readable ";
        $Query .=",'global' type, kcg.fl_clase_cg, CONCAT('Global Class',' ',kcg.ds_titulo) nb_clase  ";
        $Query .="FROM c_usuario cus, k_ses_app_frm_1 frm
                JOIN k_curso_cg  kcc ON(kcc.fl_programa=frm.fl_programa)
                JOIN c_clase_global cc ON(cc.fl_clase_global = kcc.fl_clase_global)
                LEFT JOIN k_clase_cg kcg ON ( kcg.fl_clase_global = cc.fl_clase_global )
                WHERE cus.cl_sesion = frm.cl_sesion AND fg_activo='1'  AND cus.fl_usuario = $fl_alumno ";
        $Query .= " AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(kcg.fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
        //$Query .= " AND NOW() <= DATE_ADD(kcg.fe_clase,INTERVAL $tolerancia_link MINUTE) ";
        //$Query .= " AND DATE_FORMAT(kcg.fe_clase, '%Y-%m-%d') = 	DATE_FORMAT(NOW(), '%Y-%m-%d') ";
        $Query .= " ORDER BY kcg.fe_clase ";

        $row = RecuperaValor($Query);



        return array(
            "actual" => $row[0],
            "early" => $row[1],
            "after" => $row[2],
            "readable" => $row[3],
          "type" => $row[4],
          "fl_clase"=>$row[5],
          "titulo"=>$row[6]
        );
	}

    # Find student's live session
	function ObtainLiveSessionLectureClassStudent($p_grupo, $p_semana) {
		$access_early = ObtenConfiguracion(34);
        #$access_after = ObtenConfiguracion(35);
        #$tolerancia_link = ObtenConfiguracion(36);
        $access_after = "30";
        $tolerancia_link = "30";
        $fl_alumno = ValidaSesion(False);
        $diferencia = RecuperaDiferenciaGMT( );

        $Query = "SELECT a.fe_clase, ";
		$Query .= "UNIX_TIMESTAMP( DATE_SUB(a.fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
		$Query .= "UNIX_TIMESTAMP( DATE_ADD(a.fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
        $Query .= "DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %h:%i %p') fe_readable ";
        $Query .=",'lecture' type,a.fl_clase,d.ds_titulo nb_clase ";
        $Query .= "FROM k_clase a JOIN c_grupo b ON b.fl_grupo=a.fl_grupo
                 JOIN k_semana c ON c.fl_semana=a.fl_semana
                 JOIN c_leccion d ON d.fl_leccion=c.fl_leccion ";
        $Query .= "WHERE a.fl_grupo=$p_grupo ";
        if(($fl_alumno==7228)||($fl_alumno==7246)){
            $Query.=" ";
        }else{
            $Query .= "AND a.fl_semana=$p_semana ";
        }
        $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(a.fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
        $Query .= " AND NOW() <= DATE_ADD(a.fe_clase,INTERVAL $tolerancia_link MINUTE) ";
        $Query .= " AND DATE_FORMAT(a.fe_clase, '%Y-%m-%d') = 	DATE_FORMAT(NOW(), '%Y-%m-%d') ";

        $Query .= "ORDER BY a.fe_clase ";



        $row = RecuperaValor($Query);



        return array(
            "actual" => $row[0],
            "early" => $row[1],
            "after" => $row[2],
            "readable" => $row[3],
          "type" => $row[4],
          "fl_clase"=>$row[5],
          "titulo"=>$row[6]
        );
	}

	# Find student's live session
	function ObtainLiveSessionReviewClassStudent($p_grupo, $p_semana) {
		$access_early = ObtenConfiguracion(34);
        #$access_after = ObtenConfiguracion(35);
        #$tolerancia_link = ObtenConfiguracion(36);
        $access_after = "30";
        $tolerancia_link = "30";
        $fl_alumno = ValidaSesion(False);
        $diferencia = RecuperaDiferenciaGMT( );



        $Query ="SELECT DISTINCT a.fe_clase, ";
        $Query .= "UNIX_TIMESTAMP( DATE_SUB(a.fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
        $Query .= "UNIX_TIMESTAMP( DATE_ADD(a.fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
        $Query .= "DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %h:%i %p') fe_readable ";
        $Query .=",'review' type,a.fl_clase_grupo, c.nb_grupo nb_clase  ";
        $Query .= "FROM k_clase_grupo a
                JOIN k_alumno_grupo b ON b.fl_grupo = a.fl_grupo
                JOIN c_grupo c ON c.fl_grupo=a.fl_grupo and c.fg_grupo_global='1'
                WHERE b.fl_alumno=$fl_alumno ";
        $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(a.fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
        $Query .= " AND NOW() <= DATE_ADD(a.fe_clase,INTERVAL $tolerancia_link MINUTE) ";
        $Query .= " AND DATE_FORMAT(a.fe_clase, '%Y-%m-%d') = 	DATE_FORMAT(NOW(), '%Y-%m-%d') ";
        $Query .= "ORDER BY a.fe_clase ASC ";


        $row = RecuperaValor($Query);



        return array(
            "actual" => $row[0],
            "early" => $row[1],
            "after" => $row[2],
            "readable" => $row[3],
          "type" => $row[4],
          "fl_clase"=>$row[5],
          "titulo"=>$row[6]
        );
	}

	# The date and time of server and local user
	function GetDateAndTime(){
		# Revisa si se debe usar una fecha para debug o la fecha actual
	  $fg_degub = ObtenConfiguracion(21);
	  $diferencia = RecuperaDiferenciaGMT( );
    # Dividimos las horas
    $enteros = intval($diferencia);
    $decimales = ($diferencia-intval($diferencia))*100;
    if(!empty($decimal))
      $decimales = 60/(100/$decimales);
    else
      $decimal = 0;
	  if($fg_degub <> "1") {
	  	//$Query  = "SELECT UNIX_TIMESTAMP() AS fe_server, ";
	  	$Query  = "SELECT NOW() AS fe_server, ";
	    //$Query .= "UNIX_TIMESTAMP( DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $diferencia HOUR) ) AS fe_local, ";
      /*if(!empty($decimales))
        $Query .= "DATE_ADD(DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $enteros HOUR), INTERVAL $decimales MINUTE) AS fe_local, ";
      else
        $Query .= "DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $enteros HOUR) AS fe_local, ";*/
      $Query .= "DATE_ADD(DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $enteros HOUR), INTERVAL $decimales MINUTE) AS fe_local, ";
	    $Query .= "DATE_FORMAT(CURRENT_TIMESTAMP, '%W, %M %e, %Y') AS fe_server_readable, ";
	    $Query .= "DATE_FORMAT(DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $diferencia HOUR), '%W, %M %e, %Y') AS fe_local_readable ";
	    $row = RecuperaValor($Query);
	  } else {
	    $fe_actual = ObtenConfiguracion(22);
	    $Query  = "SELECT UNIX_TIMESTAMP('$fe_actual') AS fe_server, ";
	    $Query .= "UNIX_TIMESTAMP( DATE_ADD('$fe_actual', INTERVAL $diferencia HOUR) ) AS fe_local, ";
	    $Query .= "DATE_FORMAT(CURRENT_TIMESTAMP, '%W, %M %e, %Y') AS fe_server_readable, ";
	    $Query .= "DATE_FORMAT(DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $diferencia HOUR), '%W, %M %e, %Y') AS fe_local_readable ";
	    $row = RecuperaValor($Query);
	  }
	  $fe_server = $row[0];
    $fe_local = $row[1];
    $fe_server_readable = $row[2];
    $fe_local_readable = $row[3];

	  echo json_encode((Object)array(
	  	"server" => array(
	  		"readable" => $fe_server_readable,
	  		"seconds" => (int)$fe_server,
        "milliseconds" => date('Y-m-d H:i:s', strtotime($fe_server)) // Ojo hay que mandar el \T para qe pueda ser bien representado en clock
	  		//"milliseconds" => (int)$fe_server*1000
	  	),
	  	"local" => array(
	  		"readable" => $fe_local_readable,
	  		"seconds" => (int)$fe_local,
	  		"milliseconds" => date('Y-m-d H:i:s', strtotime($fe_local))
	  		//"milliseconds" => (int)$fe_local*100
	  	)
	  ));
	}

	# Progress Bars for notification notify/progress.php and home.php
	function GetAssignProgress($fl_alumno){

		$fl_programa = ObtenProgramaAlumno($fl_alumno);
		$no_grado = ObtenGradoAlumno($fl_alumno);
		$no_semana = ObtenSemanaActualAlumno($fl_alumno);
		$fl_grupo = ObtenGrupoAlumno($fl_alumno);
  	$fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);

  	$requirement["size"] = array();
  	$requirement["total"] = array();
  	$requirement["uploaded"] = array();
  	$requirement["debug"] = array();

  	// Find total assignment requirements
		$Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
		$Query .= "FROM c_leccion ";
		$Query .= "WHERE fl_programa=$fl_programa ";
		$Query .= "AND no_grado=$no_grado ";
		$Query .= "AND no_semana=$no_semana ";
		$row = RecuperaValor($Query);

		$fg_animacion = $row[0];
		$fg_ref_animacion = $row[1];
		$no_sketch = $row[2];
		$fg_ref_sketch = $row[3];

		$total = $fg_animacion + $fg_ref_animacion + $no_sketch + $fg_ref_sketch;

		$requirement["total"] += array(
			"A" => $fg_animacion,
			"AR" => $fg_ref_animacion,
			"S" => $no_sketch,
			"SR" => $fg_ref_sketch
		);

		$requirement["size"] += array("total" => $total);

		// Find the number of uploads the student has done
		$Query  = "SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana";
  	$row = RecuperaValor($Query);
  	$fl_entrega_semanal = $row[0];
  	if(empty($fl_entrega_semanal)) {
  		// student has not done any uplaods before
  	}

  	$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='A' AND fl_entrega_semanal=$fl_entrega_semanal");
  	$tot_assignment = $row[0];
  	$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='AR' AND fl_entrega_semanal=$fl_entrega_semanal");
  	$tot_assignment_ref = $row[0];
  	$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='S' AND fl_entrega_semanal=$fl_entrega_semanal");
  	$tot_sketch = $row[0];
  	$row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='SR' AND fl_entrega_semanal=$fl_entrega_semanal");
  	$tot_sketch_ref = $row[0];

  	$requirement["debug"] += array(
  		"no_semana" => $no_semana,
  		"tot_A" => $tot_assignment,
  		"tot_AR" => $tot_assignment_ref,
  		"tot_S" => $tot_sketch,
  		"tot_SR" => $tot_sketch_ref
  	);

  	$total_uploaded = 0;

  	if($fg_animacion == "0" OR ($fg_animacion == "1" AND $tot_assignment > 0)){
    	$animacion_ok = 1;
    	$total_uploaded += $fg_animacion;
  	} else {
  		$animacion_ok = 0;
  	}

	  if($fg_ref_animacion == "0" OR ($fg_ref_animacion == "1" AND $tot_assignment_ref > 0)){
	    $animacion_ref_ok = 1;
	    $total_uploaded += $fg_ref_animacion;
	  } else {
	  	$animacion_ref_ok = 0;
	  }

	  if($tot_sketch >= $no_sketch){
	    $sketch_ok = $no_sketch;
	    $total_uploaded += $no_sketch;
	  } else {
	  	$sketch_ok = $tot_sketch;
	  	$total_uploaded += $tot_sketch;
	  }

	  if($fg_ref_sketch == "0" OR ($fg_ref_sketch == "1" AND $tot_sketch_ref > 0)){
	    $sketch_ref_ok = 1;
	    $total_uploaded += $fg_ref_sketch;
	  } else {
	  	$sketch_ref_ok = 0;
	  }

	  $requirement["uploaded"] += array(
	  	"A" => $animacion_ok,
	  	"AR" => $animacion_ref_ok,
	  	"S" => $sketch_ok,
	  	"SR" => $sketch_ref_ok
	  );

	  $requirement["size"] += array("total_uploaded" => $total_uploaded);

		echo json_encode((Object) $requirement);
	}
	# End progress bars

	# Get the list of chat users for messages.php and notify/messages.php
	function GetChatUsers($fl_usuario){
	  # Recupera usuarios que han enviado o se les ha enviado mensajes
	  $diferencia = RecuperaDiferenciaGMT( );

	  $Query  = "SELECT usr_interaccion, DATE_FORMAT(MAX(fe_mensaje), '%M %e, %Y at %l:%i %p') 'fe_message', MAX(fe_mensaje) cuando ";
	  $Query .= "FROM( SELECT CASE WHEN fl_usuario_ori<>$fl_usuario then fl_usuario_ori ELSE fl_usuario_dest END usr_interaccion, ";
	  $Query .= "DATE_ADD(fe_mensaje, INTERVAL $diferencia HOUR) fe_mensaje ";
	  $Query .= "FROM k_mensaje_directo ";
	  $Query .= "WHERE fl_usuario_ori=$fl_usuario ";
	  $Query .= "OR fl_usuario_dest=$fl_usuario) usuarios ";
	  $Query .= "GROUP BY usr_interaccion ";
	  $Query .= "ORDER BY cuando DESC";
	  $rs = EjecutaQuery($Query);

	  $result = array();
	  for($i=0; $row = RecuperaRegistro($rs); $i++) {
	    $usr_interaccion = $row[0];
	    $fe_mensaje = $row[1];
	    $no_mensaje = $row[2];
	    $ds_ruta_avatar = ObtenAvatarUsrFa_Va($usr_interaccion);
	    $ds_nombre = ObtenNombreUsuario($usr_interaccion);

	    # Check if there's unread messages
	    $Query = "SELECT COUNT(1) FROM k_mensaje_directo WHERE fl_usuario_ori=$usr_interaccion AND fl_usuario_dest=$fl_usuario AND fg_leido='0'";
	    $row2 = RecuperaValor($Query);
	    $no_unread = $row2[0];
	    if($no_unread > 0){
	      $ds_notificar = "(Unread Messages)";
	    } else {
	      $ds_notificar = "";
	    }

      $result["user".$i] = array(
      	"id" => $usr_interaccion,
      	"time" => $fe_mensaje,
      	"total" => $no_mensaje,
      	"avatar" => $ds_ruta_avatar,
      	"name" => $ds_nombre,
      	"unread" => $ds_notificar
      );
	  }
	  $result["size"] = array("total" => $i);
	  echo json_encode((Object) $result);
	}
	# end messages


  # Find student's live session
	function ObtainLiveSessionStudentCG($p_clase_global, $p_clase) {
		$access_early = ObtenConfiguracion(34);
  	$access_after = ObtenConfiguracion(35);
	  $tolerancia_link = ObtenConfiguracion(36);

	  $diferencia = RecuperaDiferenciaGMT( );
	  $Query  = "SELECT fe_clase, ";
		$Query .= "UNIX_TIMESTAMP( DATE_SUB(fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
		$Query .= "UNIX_TIMESTAMP( DATE_ADD(fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
	  $Query .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
	  $Query .= "FROM k_clase_cg ";
	  $Query .= "WHERE fl_clase_global=$p_clase_global ";
	  $Query .= "AND fl_clase_cg=$p_clase ";
	  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
	  $Query .= "ORDER BY fe_clase";
	  $row = RecuperaValor($Query);
	  return array(
	  	"actual" => $row[0],
	  	"early" => $row[1],
	  	"after" => $row[2],
	  	"readable" => $row[3]
	  );
	}

  # Get the student's live session Global Class
	function GetStudentSessionGC($fl_alumno){
    $access_early = ObtenConfiguracion(34);
  	$access_after = ObtenConfiguracion(35);
	  $tolerancia_link = ObtenConfiguracion(36);
    // $no_semanaAux = ObtenSemanaLiveSessionStudentCG($fl_alumno);
    // $clase_global = ObtenClaseGlobalStudent($fl_alumno);
    $Query="( ";
    $Query .= "SELECT b.fl_clase_cg,  a.fl_clase_global, b.fe_clase,'0'fg_grupo_global ";
    $Query .= "FROM k_alumno_cg a, k_clase_cg b ";
    $Query .= "WHERE a.fl_clase_global = b.fl_clase_global AND  a.fl_usuario=$fl_alumno ";
    // $Query .= "AND DATE_FORMAT(b.fe_clase, '%Y-%m-%d') >= DATE_FORMAT(CURDATE(), '%Y-%m-%d')  ORDER BY a.fl_clase_global ";
    // $Query .= "AND DATE_FORMAT(b.fe_clase, '%Y-%m-%d %H:%i:%s') >= '".ObtenFechaActual(false)."' ORDER BY b.fe_clase ";
    // $Query .= "AND b.fe_clase >= CURDATE() ORDER BY a.fl_clase_global ";
    $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
    $Query .= "ORDER BY fe_clase ";
    $Query .=")UNION( ";

    $Query .= "SELECT b.fl_clase_grupo fl_clase_cg,  b.fl_clase_grupo fl_clase_global, b.fe_clase,'1'fg_grupo_global ";
    $Query .= "FROM k_alumno_grupo a, k_clase_grupo b ";
    $Query .= "WHERE a.fl_grupo = b.fl_grupo AND  a.fl_alumno=$fl_alumno ";
    // $Query .= "AND DATE_FORMAT(b.fe_clase, '%Y-%m-%d') >= DATE_FORMAT(CURDATE(), '%Y-%m-%d')  ORDER BY a.fl_clase_global ";
    // $Query .= "AND DATE_FORMAT(b.fe_clase, '%Y-%m-%d %H:%i:%s') >= '".ObtenFechaActual(false)."' ORDER BY b.fe_clase ";
    // $Query .= "AND b.fe_clase >= CURDATE() ORDER BY a.fl_clase_global ";
    $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
    $Query .= "ORDER BY fe_clase ";


    $Query .=")ORDER BY fe_clase ";
    // $Query .= "LIMIT 1 ";
    $row = RecuperaValor($Query);
    $no_semanaAux = $row[0];
    $clase_global = $row[1];
    $fg_grupo_global=$row[3];
    // $liveSession = ObtainLiveSessionStudentCG($clase_global, $no_semanaAux);


	  $diferencia = RecuperaDiferenciaGMT( );

      if($fg_grupo_global==1){

          $Querys  = "SELECT fe_clase, ";
          $Querys .= "UNIX_TIMESTAMP( DATE_SUB(fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
          $Querys .= "UNIX_TIMESTAMP( DATE_ADD(fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
          $Querys .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
          $Querys .= "FROM k_clase_grupo ";
          $Querys .= "WHERE fl_clase_grupo=$clase_global ";
          $Querys .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
          $Querys .= "ORDER BY fe_clase";


      }else{

          $Querys  = "SELECT fe_clase, ";
          $Querys .= "UNIX_TIMESTAMP( DATE_SUB(fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
          $Querys .= "UNIX_TIMESTAMP( DATE_ADD(fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
          $Querys .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
          $Querys .= "FROM k_clase_cg ";
          $Querys .= "WHERE fl_clase_global=$clase_global ";
          $Querys .= "AND fl_clase_cg=$no_semanaAux ";
          $Querys .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
          $Querys .= "ORDER BY fe_clase";

      }


	  $row = RecuperaValor($Querys);
	  $actual = $row[0];
	  $early = $row[1];
	  $after = $row[2];
	  $readable = $row[3];

    // $folioClase = ObtenFolioLiveSessionStudentCG($clase_global, $no_semanaAux);
    $folioClase = $no_semanaAux;

    # Obtenemos el titulo de la clase global y titilo de la sesion

    if($fg_grupo_global==1){

       // $row = RecuperaValor("SELECT nb_grupo,nb_clase
       // FROM k_clase_cg kcg, c_clase_global cg WHERE kcg.fl_clase_global=cg.fl_clase_global AND kcg.fl_clase_global=$clase_global AND kcg.fl_clase_cg=$no_semanaAux");
        $row=RecuperaValor(" SELECT nb_clase,c.nb_grupo FROM k_clase_grupo a JOIN k_semana_grupo b  ON a.fl_semana_grupo=b.fl_semana_grupo JOIN c_grupo c ON c.fl_grupo=b.fl_grupo WHERE fl_clase_grupo=$clase_global ");

    }else{

        $row = RecuperaValor("SELECT ds_clase, ds_titulo
        FROM k_clase_cg kcg, c_clase_global cg WHERE kcg.fl_clase_global=cg.fl_clase_global AND kcg.fl_clase_global=$clase_global AND kcg.fl_clase_cg=$no_semanaAux");
    }

    $ds_clase = $row[0];
    $ds_titulo = $row[1];

    // Default live session variables
    $liveSessionExists = false;
    $liveSessionTime = $actual;
    $liveSessionReadable = $readable;
    $liveSessionLink = "";
    $liveSessionStart = "";
    $liveSessionClose = "";
    $liveSessionTitleClase = "";
    $liveSessionTitle = "";
		// If class is available
    if(!empty($actual)){
    	$liveSessionExists = true;

        if($fg_grupo_global==1)
        $liveSessionLink = "href='../liveclass/LiveSession_gg.php?folio=$folioClase'";
        else
    	$liveSessionLink = "href='../liveclass/LiveSession_gc.php?folio=$folioClase'";


        $liveSessionStart = $early;
    	$liveSessionClose = $after;
      $liveSessionTitleClase = $ds_clase;
      $liveSessionTitle = $ds_titulo;
    }






    $diferencia = RecuperaDiferenciaGMT( );
    $result["session"] = array(
    	"exists" => $liveSessionExists,
    	"time" => $liveSessionTime,
    	"readable" => $liveSessionReadable,
    	"link" => $liveSessionLink,
    	"start" => array(
  			"seconds" => (int)$liveSessionStart,
  			"milliseconds" => (int)$liveSessionStart * 1000
    	),
    	"close" => array(
  			"seconds" => (int)$liveSessionClose,
  			"milliseconds" => (int)$liveSessionClose * 1000
    	),
      "titleclass" => $liveSessionTitleClase,
      "title" => $liveSessionTitle
    );
		echo json_encode((Object) $result);
	}

  # Get the student's live session Global Class
	function GetStudentSessionGC_NEXT($fl_alumno,$fg_grupo_global=""){
    // -------------------
    $Query ="";
    if($fg_grupo_global==1){
        $Query ="( ";
    }


    $Query .= "SELECT DATE_FORMAT(b.fe_clase, '%Y-%m-%d %H:%i:%s') AS fe_clase ";
    $Query .= "FROM k_alumno_cg a, k_clase_cg b ";
    $Query .= "WHERE a.fl_clase_global = b.fl_clase_global AND  a.fl_usuario=$fl_alumno ";
    // $Query .= "AND DATE_FORMAT(b.fe_clase, '%Y-%m-%d %H') >= DATE_FORMAT(CURDATE(), '%Y-%m-%d %H')  ORDER BY a.fl_clase_global ";
    $Query .= "AND DATE_FORMAT(b.fe_clase, '%Y-%m-%d %H:%i:%s') >= '".ObtenFechaActual(false)."' ORDER BY b.fe_clase ";
    // $Query .= "AND b.fe_clase >= CURDATE() ORDER BY a.fl_clase_global ";
    $Query .= "LIMIT 1 ";
    if($fg_grupo_global==1){
        $Query .=") UNION( ";
        $Query .= "SELECT DATE_FORMAT(b.fe_clase, '%Y-%m-%d %H:%i:%s') AS fe_clase ";
        $Query .= "FROM k_alumno_grupo a, c_grupo c ,k_clase_grupo b ";
        $Query .= "WHERE a.fl_grupo = b.fl_grupo AND b.fl_grupo=c.fl_grupo  AND  a.fl_alumno=$fl_alumno ";
        $Query .= "AND DATE_FORMAT(b.fe_clase, '%Y-%m-%d %H:%i:%s') >= '".ObtenFechaActual(false)."' ORDER BY b.fe_clase ";
        $Query .= "LIMIT 1 ";
        $Query .=") ORDER BY fe_clase ";
    }
    $row = RecuperaValor($Query);
    $fe_clase_act = $row[0];

    // next

    if($fg_grupo_global==1){

        $Queryn = "SELECT b.fl_clase_grupo,  b.fl_clase_grupo, b.fe_clase ";
        $Queryn .= "FROM k_alumno_grupo a,
                    c_grupo c,
                    k_clase_grupo b
                    WHERE a.fl_grupo = b.fl_grupo
                    AND b.fl_grupo=c.fl_grupo
                    AND  a.fl_alumno=$fl_alumno  ";
        $Queryn .= "AND DATE_FORMAT(b.fe_clase, '%Y-%m-%d %H:%i:%s') >= '".$fe_clase_act."' ORDER BY b.fe_clase ";
        $Queryn .= "LIMIT 1 ";
    }else{

        $Queryn = "SELECT b.fl_clase_cg,  a.fl_clase_global, b.fe_clase ";
        $Queryn .= "FROM k_alumno_cg a, k_clase_cg b ";
        $Queryn .= "WHERE a.fl_clase_global = b.fl_clase_global AND  a.fl_usuario=$fl_alumno ";
        $Queryn .= "AND DATE_FORMAT(b.fe_clase, '%Y-%m-%d %H:%i:%s') >= '".$fe_clase_act."' ORDER BY b.fe_clase ";
        $Queryn .= "LIMIT 1 ";

    }
    $rown = RecuperaValor($Queryn);
    $no_semanaAux = $rown[0];
    $clase_global = $rown[1];





    // ------------------------------
    $access_early = ObtenConfiguracion(34);
  	$access_after = ObtenConfiguracion(35);
	  $tolerancia_link = ObtenConfiguracion(36);

	  $diferencia = RecuperaDiferenciaGMT( );

      if($fg_grupo_global==1){

          $Querys  = "SELECT fe_clase, ";
          $Querys .= "UNIX_TIMESTAMP( DATE_SUB(fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
          $Querys .= "UNIX_TIMESTAMP( DATE_ADD(fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
          $Querys .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
          $Querys .= "FROM k_clase_grupo ";
          $Querys .= "WHERE fl_clase_grupo=$clase_global ";
          $Querys .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
          $Querys .= "ORDER BY fe_clase";

      }else{

          $Querys  = "SELECT fe_clase, ";
          $Querys .= "UNIX_TIMESTAMP( DATE_SUB(fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
          $Querys .= "UNIX_TIMESTAMP( DATE_ADD(fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
          $Querys .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
          $Querys .= "FROM k_clase_cg ";
          $Querys .= "WHERE fl_clase_global=$clase_global ";
          $Querys .= "AND fl_clase_cg=$no_semanaAux ";
          $Querys .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
          $Querys .= "ORDER BY fe_clase";
      }


    $row = RecuperaValor($Querys);
    $actual = $row[0];
	  $early = $row[1];
	  $after = $row[2];
	  $readable = $row[3];
    // --------------------------
    $folioClase = $no_semanaAux;

    if($fg_grupo_global==1){

        # Obtenemos el titulo de la clase global y titilo de la sesion
        $row = RecuperaValor("SELECT cg.nb_grupo, kcg.nb_clase FROM k_clase_grupo kcg, c_grupo cg WHERE kcg.fl_grupo=cg.fl_grupo AND kcg.fl_clase_grupo=$clase_global ");

    }else{


        # Obtenemos el titulo de la clase global y titilo de la sesion
        $row = RecuperaValor("SELECT ds_clase, ds_titulo
                 FROM k_clase_cg kcg, c_clase_global cg WHERE kcg.fl_clase_global=cg.fl_clase_global AND kcg.fl_clase_global=$clase_global AND kcg.fl_clase_cg=$no_semanaAux");
    }

    $ds_clase = $row[0];
    $ds_titulo = $row[1];

    // Default live session variables
    $liveSessionExists_next = false;
    $liveSessionTime_next = $actual;
    $liveSessionReadable_next = $readable;
    $liveSessionLink_next = "";
    $liveSessionStart_next = "";
    $liveSessionClose_next = "";
    $liveSessionTitleClase_next = "";
    $liveSessionTitle_next = "";

		// If class is available
    if(!empty($actual)){
    	$liveSessionExists_next = true;
        if($fg_grupo_global==1){
            $liveSessionLink_next = "href='../liveclass/LiveSession_gg.php?folio=$folioClase'";
        }else{

            $liveSessionLink_next = "href='../liveclass/LiveSession_gc.php?folio=$folioClase'";
        }

        $liveSessionStart_next = $early;
    	$liveSessionClose_next = $after;
      $liveSessionTitleClase_next = $ds_clase;
      $liveSessionTitle_next = $ds_titulo;
    }
    $diferencia = RecuperaDiferenciaGMT( );
    $result["session_next"] = array(
    	"exists_next" => $liveSessionExists_next,
    	"time_next" => $liveSessionTime_next,
    	"readable_next" => $liveSessionReadable_next,
    	"link_next" => $liveSessionLink_next,
    	"start_next" => array(
  			"seconds_next" => (int)$liveSessionStart_next,
  			"milliseconds_next" => (int)$liveSessionStart_next * 1000
    	),
    	"close_next" => array(
  			"seconds_next" => (int)$liveSessionClose_next,
  			"milliseconds_next" => (int)$liveSessionClose_next * 1000
    	),
      "titleclass_next" => $liveSessionTitleClase_next,
      "title_next" => $liveSessionTitle_next
    );
		echo json_encode((Object) $result);
	}

  # Find Teacher's live session by group
	function ObtainGlobalClassSessionTeacher($p_teacher, $fl_clase_global) {
  	$access_early = ObtenConfiguracion(34);
  	$access_after = ObtenConfiguracion(35);
	  $tolerancia_link = ObtenConfiguracion(36);
	  $diferencia = RecuperaDiferenciaGMT( );
    # Dividimos las horas
    $enteros = intval($diferencia);
    $decimales = ($diferencia-intval($diferencia))*100;
    if(!empty($decimales))
      $decimales = 60/(100/$decimales);
    else
      $decimales = 0;

	  $Query  = "SELECT fl_clase_cg, fe_clase, ";
	  $Query .= "UNIX_TIMESTAMP( DATE_SUB(fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
		$Query .= "UNIX_TIMESTAMP( DATE_ADD(fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
    if($decimales>0)
      $Query .= "DATE_FORMAT(DATE_ADD(DATE_ADD(fe_clase, INTERVAL $enteros HOUR), INTERVAL $decimales MINUTE), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
    else
      $Query .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %l:%i %p') fe_readable ";
	  $Query .= "FROM k_clase_cg a, c_clase_global b ";
	  $Query .= "WHERE a.fl_clase_global=b.fl_clase_global ";
	  $Query .= "AND b.fl_maestro=$p_teacher ";
	  $Query .= "AND b.fl_clase_global=$fl_clase_global ";
	  $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
	  $Query .= "ORDER BY fe_clase";
	  $row = RecuperaValor($Query);
	  return $row;
	}

	# Live session time
	function ObtainFechaGlobalClassSessionTeacher($p_teacher, $fl_clase_global) {
	  $row = ObtainGlobalClassSessionTeacher($p_teacher, $fl_clase_global);
	  return array(
	  	"actual" => $row["fe_clase"],
	  	"early" => $row["fe_early"],
	  	"after" => $row["fe_after"],
	  	"readable" => $row["fe_readable"]
	  );
	}

	# Live session id
	function ObtainFolioGlobalClassSessionTeacher($p_teacher, $fl_clase_global) {
	  $row = ObtainGlobalClassSessionTeacher($p_teacher, $fl_clase_global);
	  return $row["fl_clase_cg"];
	}

  # Get the teachers's live session Global Class
	function GetTeachersSessionGC($fl_teacher){
    // Obtenemos el livesession
    $access_early = ObtenConfiguracion(34);
  	$access_after = ObtenConfiguracion(35);
	  $tolerancia_link = ObtenConfiguracion(36);

      #Obtenemos fecha actual :
      $Query = "Select CURDATE() ";
      $row = RecuperaValor($Query);
      $fe_actual = str_texto($row[0]);
      $fe_actual=strtotime('+0 day',strtotime($fe_actual));
      $fe_actual= date('Y-m-d',$fe_actual);


    // Obtenemos la clase que debe partir el maestro
    $Query  = "SELECT fl_clase_cg, fl_clase_global FROM k_clase_cg ";
    $Query .= "WHERE fl_maestro=".$fl_teacher." ";
    // $Query .= "AND fe_clase>=NOW() ";
    #$Query .="AND DATE_FORMAT(fe_clase, '%Y-%m-%d') ='$fe_actual' ";
    $Query .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
    $Query .= "ORDER BY fe_clase ASC ";
    $row = RecuperaValor($Query);
    $fl_clase_cg = $row[0];
    $fl_clase_global = $row[1];

    # Obtenemos el nombre de la clase
    $rowc = RecuperaValor("SELECT ds_clase FROM c_clase_global WHERE fl_clase_global=$fl_clase_global ");
    $ds_clase = $rowc[0];


	  $diferencia = RecuperaDiferenciaGMT( );
	  $Query1  = "SELECT fe_clase, ";
		$Query1 .= "UNIX_TIMESTAMP( DATE_SUB(fe_clase, INTERVAL $access_early MINUTE) ) fe_early, ";
		$Query1 .= "UNIX_TIMESTAMP( DATE_ADD(fe_clase, INTERVAL $access_after MINUTE) ) fe_after, ";
	  $Query1 .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y at %l:%i %p') fe_readable, DATE_FORMAT(fe_clase, '%Y-%m-%d'), ds_titulo ";
	  $Query1 .= "FROM k_clase_cg ";
	  $Query1 .= "WHERE fl_clase_global=$fl_clase_global ";
	  $Query1 .= "AND fl_clase_cg=$fl_clase_cg ";
	  $Query1 .= "AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 ";
    $Query1 .= "ORDER BY fe_clase";
	  $row1 = RecuperaValor($Query1);
    $folioClase = $fl_clase_cg;

    // Default live session variables
    $liveSessionExists = false;
    $liveSessionTime = $row1[0];
    $liveSessionReadable = $row1[3];
    $liveSessionLink = "";
    $liveSessionStart = "";
    $liveSessionClose = "";
    $liveSessionClase = "";
    $liveSessionTitle = "";

		// If class is available
    if(!empty($row1[0])){
    	$liveSessionExists = true;
    	$liveSessionLink = "href='../liveclass/LiveSession_gc.php?folio=$folioClase'";
    	$liveSessionStart = $row1[1];
    	$liveSessionClose = $row1[2];
      $liveSessionTitleClase= $ds_clase;
      $liveSessionTitle= $row1[5];
    }
    $diferencia = RecuperaDiferenciaGMT( );
    $result["session"] = array(
    	"exists" => $liveSessionExists,
    	"time" => $liveSessionTime,
    	"readable" => $liveSessionReadable,
    	"link" => $liveSessionLink,
    	"start" => array(
  			"seconds" => (int)$liveSessionStart,
  			"milliseconds" => (int)$liveSessionStart * 1000
    	),
    	"close" => array(
  			"seconds" => (int)$liveSessionClose,
  			"milliseconds" => (int)$liveSessionClose * 1000
    	),
      "titleclass" => $liveSessionTitleClase,
      "title" => $liveSessionTitle
    );
		echo json_encode((Object) $result);
	}
?>