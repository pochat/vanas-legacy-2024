<?php
	# Libreria de funciones
  require("../lib/self_general.php");

	# gallery_post_items queries for the list of post items for the board to display
	# only used when user is filtering the board

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
	
	# Receive Parameters
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp', True);
  
  $result['fl_programa_sp'] = $fl_programa_sp;	
  
  /** START INFO CALIFICIACIONES GRAFICA BARRAS****/  
  $Query = "SELECT fl_leccion_sp, no_semana FROM c_leccion_sp WHERE fl_programa_sp=$fl_programa_sp ORDER BY no_semana";
  $rs = EjecutaQuery($Query);
  for($i=1; $row=RecuperaRegistro($rs); $i++){
    $fl_leccion_sp = $row[0];
    $no_semana = $row[1];
    # Buscamos si existe algun registro en la tabla k_entrega_semanal_sp
    // $Query1  = "SELECT c.no_equivalencia FROM k_entrega_semanal_sp a LEFT JOIN c_calificacion_sp c ON c.fl_calificacion=a.fl_promedio_semana ";
    // $Query1 .= "WHERE a.fl_alumno=$fl_usuario AND a.fl_leccion_sp=$fl_leccion_sp ";
    $Query1 = "SELECT  no_calificacion FROM k_calificacion_teacher WHERE fl_alumno=$fl_usuario AND  fl_leccion_sp=$fl_leccion_sp ";
    $row1 = RecuperaValor($Query1);
    $no_equivalencia = $row1[0];
    if(!empty($no_equivalencia)){
      $grades["$no_semana"] = $no_equivalencia;	
    } else {
      $grades["$no_semana"] = 0;
    }
    # Buscamos los numeros de intentos de esta leccion
    $row9 = RecuperaValor("SELECT COUNT(*) FROM k_quiz_calif_final  WHERE fl_usuario=$fl_usuario AND fl_leccion_sp=$fl_leccion_sp");
    $no_intentos = $row9[0];
    if(!empty($no_intentos)){
      # Buscamos si tiene quiz
      $Queryy  = "SELECT b.no_intento, b.cl_calificacion, b.no_calificacion ";
      $Queryy .= "FROM k_quiz_pregunta a ";
      $Queryy .= "LEFT JOIN k_quiz_calif_final b ON(b.fl_leccion_sp=a.fl_leccion_sp) WHERE a.fl_leccion_sp=$fl_leccion_sp AND b.fl_usuario=$fl_usuario ";
      $Queryy .= "AND no_intento =$no_intentos ";
      $roww = RecuperaValor($Queryy);
      if(!empty($roww[0]))      
        $quiz["$no_semana"] = $roww[2];
      else
        $quiz["$no_semana"] = 0; 
    }
    else{
      $quiz["$no_semana"] = 0; 
    }
  }
  $result['GPA'] = $grades;
  $result['QUIZ'] = $quiz;
  $result['size'] = array('total' => $i-1);
  /*** END INFO CALIFICIACIONES GRAFICA BARRAS ***/
  
  /*** START Info Barras laterales Assigments **/
  $no_semana = ObtenSessionActualCourse($fl_usuario, $fl_programa_sp);
  $fl_leccion_sp = ObtenFolioSemanaAlumno($no_semana, $fl_programa_sp);
  $max_semanas = ObtenSemanaMaximaAlumno($fl_programa_sp);

  $result["size_1"] = array();
    $result['labels'] = array(
                  'sessiones' => ObtenEtiqueta(1836), 
                  'quiz' => ObtenEtiqueta(1837),
                  'etq_0' => ObtenEtiqueta(1843)."&nbsp;".$no_semana,
                  'etq_1' => ObtenEtiqueta(1844),
                  'etq_2' => ObtenEtiqueta(1845),
                  'etq_3' => ObtenEtiqueta(1846),
                  'etq_4' => ObtenEtiqueta(1847),                
                  'etq_5' => ObtenEtiqueta(1848)
                  );
  
  // Find total assignment requirements
  $Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
  $Query .= "FROM c_leccion_sp ";
  $Query .= "WHERE fl_programa_sp=$fl_programa_sp ";
  $Query .= "AND no_semana=$no_semana ";
  $row = RecuperaValor($Query);

  $fg_animacion = $row[0];
  $fg_ref_animacion = $row[1];
  $no_sketch = $row[2];
  $fg_ref_sketch = $row[3];

  $total = $fg_animacion + $fg_ref_animacion + $no_sketch + $fg_ref_sketch;
  
  $result["total_0"] = array(
			"A" => $fg_animacion,
			"AR" => $fg_ref_animacion,
			"S" => $no_sketch,
			"SR" => $fg_ref_sketch
  );
  
  $result["size_1"] = array("total_1" => $total);
  
  // Find the number of uploads the student has done
  $Query  = "SELECT fl_entrega_semanal_sp FROM k_entrega_semanal_sp WHERE fl_alumno=$fl_usuario AND fl_leccion_sp=$fl_leccion_sp";
  $row = RecuperaValor($Query);
  $fl_entrega_semanal_sp = $row[0];
  if(empty($fl_entrega_semanal_sp)) {
    // student has not done any uplaods before
  }
  
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='A' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
  $tot_assignment = $row[0];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='AR' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
  $tot_assignment_ref = $row[0];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='S' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
  $tot_sketch = $row[0];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='SR' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
  $tot_sketch_ref = $row[0];

  $result["debug"] = array(
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

  $result["uploaded"] = array(
    "A" => $animacion_ok,
    "AR" => $animacion_ref_ok,
    "S" => $sketch_ok,
    "SR" => $sketch_ref_ok
  );

	$result["size_2"] = array("total_uploaded" => $total_uploaded);
  /******* END infor Assigemnt barrar lado derecho *********/
  
  $row0 = RecuperaValor("SELECT ds_progreso, no_promedio_t, fl_maestro FROM k_usuario_programa WHERE fl_usuario_sp = $fl_usuario  AND fl_programa_sp = $fl_programa_sp");
  # Obtenemos el nombre del maestro
  $ds_maestro = ObtenNombreUsuario($row0[2]);
  $ds_avatar_ma = ObtenAvatarUsuario($row0[2]);
  /*** START GRAFICAS CIRCULARES ***/
  $percent = $no_semana / $max_semanas * 100;
  $result['program'] = array(
    'name' => ObtenNombreCourse($fl_programa_sp),
    'percent_course' => $row0[0],
    'current_week' => $no_semana,
    'max_week' => $max_semanas,
    'percent_week' => $percent,
    'percent_gpa' => $row0[1],
    'ds_maestro' => $ds_maestro,
    'ds_avatar_ma' => $ds_avatar_ma
  );
  
    /*** START GRAFICA PARA EL PERFIL **/

    $Query  = "SELECT usr.ds_alias, usr.ds_nombres, usr.ds_apaterno, usr.ds_email, usr.fe_nacimiento, ";
    $Query .= "usrd.ds_number, usrd.ds_street, usrd.ds_city, usrd.ds_state, usrd.ds_zip, ";
    $Query .= "usrd.fl_pais, al.ds_ruta_avatar, al.ds_ruta_foto ";
    $Query .= "FROM c_usuario usr ";
    $Query .= "LEFT JOIN c_alumno_sp al ON(al.fl_alumno_sp=usr.fl_usuario) ";
    $Query .= "LEFT JOIN k_usu_direccion_sp usrd ON(usrd.fl_usuario_sp=usr.fl_usuario) ";
    $Query .= "WHERE usr.fl_usuario=$fl_usuario ";
    $falta = 0;
    $row= RecuperaValor($Query);
    //0
    $ds_alias = $row[0];
    if(empty($ds_alias))
      $falta = $falta + 1;
    //1
    $ds_nombres = $row[1];
    if(empty($ds_nombres))
      $falta = $falta + 1;
    //2
    $ds_apaterno = $row[2];
    if(empty($ds_apaterno))
      $falta = $falta + 1;
    //3
    $ds_email = $row[3];
    if(empty($ds_email))
      $falta = $falta + 1;
    //4
    $fe_nacimiento = $row[4];
    if(empty($fe_nacimiento))
      $falta = $falta + 1;
    //5
    $ds_number = $row[5];
    if(empty($ds_number))
      $falta = $falta + 1;
    //6
    $ds_street = $row[6];
    if(empty($ds_street))
      $falta = $falta + 1;
    //7
    $ds_city = $row[7];
    if(empty($ds_city))
      $falta = $falta + 1;
    //8
    $ds_state = $row[8];
    if(empty($ds_state))
      $falta = $falta + 1;
    //9
    $ds_zip = $row[9];
    if(empty($ds_zip))
      $falta = $falta + 1;
    //10
    $fl_pais = $row[10];
    if(empty($fl_pais))
      $falta = $falta + 1;
    //11
    $ds_ruta_avatar = $row[11];
    if(empty($ds_ruta_avatar))
      $falta = $falta + 1;
    //12
    $ds_ruta_foto = $row[12];
    if(empty($ds_ruta_foto))
      $falta = $falta +1;
    
    $porcentaje = round(100/13*(13-$falta));

    
  /*** end GRAFICA PARA EL PERFIL**/
  $result['percent_profile'] = $porcentaje;
  $result['time_spend'] = ObtenEtiqueta(802);


  /**** END INFO GRAFICAS CIRCULARES **/
  
  
  

  echo json_encode((Object) $result);
?>