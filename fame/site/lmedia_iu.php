<?php

 # Librerias
 require("../lib/self_general.php");

 # Verifica que exista una sesion valida en el cookie y la resetea
 $fl_usuario = ValidaSesion(False,0, True);

 # Verifica que el usuario tenga permiso de usar esta funcion
 if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
 }

  # Intituto del usuario.
  $clave=RecibeParametroNumerico('clave');
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  if(empty($clave)){
	  $clave=RecibeParametroNumerico('fl_leccion_nuevo_creado');
  }
  $tab = RecibeParametroHTML('tab');

  if($tab==1){
			 
	  # Datos generales de Lessons and Media
	  $fl_programa = RecibeParametroNumerico('fl_programa');
	  $no_grado = RecibeParametroNumerico('no_grado');
	  $no_semana = RecibeParametroNumerico('no_semana');
	  $ds_titulo = RecibeParametroHTML('ds_titulo');  
      $ds_titulo_esp = RecibeParametroHTML('ds_titulo_esp');
      $ds_titulo_fra = RecibeParametroHTML('ds_titulo_fra');
	  $ds_learning = RecibeParametroHTML('ds_learning');
      $ds_learning_esp = RecibeParametroHTML('ds_learning_esp');
      $ds_learning_fra = RecibeParametroHTML('ds_learning_fra');
	  $ds_leccion = RecibeParametroHTML('ds_leccion');
      $ds_leccion_esp = RecibeParametroHTML('ds_leccion_esp');
      $ds_leccion_fra = RecibeParametroHTML('ds_leccion_fra');
	  
      if(empty($ds_titulo_esp))
      $ds_titulo_esp=$ds_titulo;
	  if(empty($ds_titulo_fra))
      $ds_titulo_fra=$ds_titulo;
      if(empty($ds_learning_esp))
      $ds_learning_esp=$ds_learning;
      if(empty($ds_learning_fra))
	  $ds_learning_fra=$ds_learning;
      if(empty($ds_leccion_esp))
      $ds_leccion_esp=$ds_leccion;
      if(empty($ds_leccion_fra))
      $ds_leccion_fra=$ds_leccion;


	  /*Comentado cuando se crea una leccion desde el inciio ya traes su clave y se realizan update/insert segun sea el caso
       * $Query  = "SELECT count(1) ";
	  $Query .= "FROM c_leccion_sp ";
	  $Query .= "WHERE fl_programa_sp = $fl_programa ";
	  $Query .= "AND no_grado = $no_grado ";
	  $Query .= "AND no_semana = $no_semana ";
	  if(!empty($clave))
	  $Query .= "AND fl_leccion_sp<>$clave";
      $Query .="AND fl_instituto=$fl_instituto ";
	  $row = RecuperaValor($Query);
	  if(!empty($row[0])){
	    $no_semana_err = 109; # Existing lesson found for this program.
      
		  echo json_encode((Object)array(
			  'fg_correcto' => false
			));
	  
	    exit;
	  }*/
	  
	  if(empty($clave)){
          $Query  = "INSERT INTO c_leccion_sp (fl_programa_sp, no_grado, no_semana, ds_titulo, ds_leccion, ";
          $Query .= "ds_learning,fl_instituto,fl_usuario_creacion ) ";
          $Query .= "VALUES($fl_programa, $no_grado, $no_semana, '$ds_titulo', '$ds_leccion', ";
          $Query .= " '$ds_learning',$fl_instituto,$fl_usuario ) ";
          $fl_leccion_sp = EjecutaInsert($Query);
      } else {
          $Query ="UPDATE c_leccion_sp SET fl_programa_sp=$fl_programa, no_grado=$no_grado,no_semana=$no_semana,ds_titulo='$ds_titulo',ds_titulo_esp='$ds_titulo_esp',ds_titulo_fra='$ds_titulo_fra',ds_leccion='$ds_leccion',ds_leccion_esp='$ds_leccion_esp',ds_leccion_fra='$ds_leccion_fra',ds_learning='$ds_learning',ds_learning_esp='$ds_learning_esp',ds_learning_fra='$ds_learning_fra' ";
          $Query.="WHERE fl_leccion_sp=$clave ";

          EjecutaQuery($Query);
      }

	  echo json_encode((Object)array(
        'fg_correcto' => true,
        'Query'=>$Query,
        'fl_leccion_sp' => $fl_leccion_sp
      ));
  }

  if($tab==2){

	 $ds_vl_duracion=RecibeParametroHTML('ds_vl_duracion');
     
	 $Query="UPDATE c_leccion_sp SET ds_vl_duracion='$ds_vl_duracion' WHERE fl_leccion_sp=$clave ";	 
     EjecutaQuery($Query);
  
     echo json_encode((Object)array(
        'fg_correcto' => true
     ));
  }

  if($tab==3){
	  
	 #Recibe parametros 
     $fg_animacion=RecibeParametroBinario('fg_animacion');
     $ds_animacion=RecibeParametroHTML('ds_animacion');
     $ds_animacion_esp=RecibeParametroHTML('ds_animacion_esp');
     $ds_animacion_fra=RecibeParametroHTML('ds_animacion_fra');
     $fg_ref_animacion=RecibeParametroBinario('fg_ref_animacion');
     $ds_ref_animacion=RecibeParametroHTML('ds_ref_animacion');
	 $ds_ref_animacion_esp=RecibeParametroHTML('ds_ref_animacion_esp');
	 $ds_ref_animacion_fra=RecibeParametroHTML('ds_ref_animacion_fra');
     $no_sketch=RecibeParametroNumerico('no_sketch');
     $ds_no_sketch=RecibeParametroHTML('ds_no_sketch');
	 $ds_no_sketch_esp=RecibeParametroHTML('ds_no_sketch_esp');
	 $ds_no_sketch_fra=RecibeParametroHTML('ds_no_sketch_fra');
     $fg_ref_sketch=RecibeParametroBinario('fg_ref_sketch');
     $ds_ref_sketch=RecibeParametroHTML('ds_ref_sketch');
	 $ds_ref_sketch_esp=RecibeParametroHTML('ds_ref_sketch_esp');
	 $ds_ref_sketch_fra=RecibeParametroHTML('ds_ref_sketch_fra');
     $ds_tiempo_tarea=RecibeParametroHTML('ds_tiempo_tarea');
	
     $ds_animacion_esp=!empty($ds_animacion_esp)?$ds_animacion_esp:$ds_animacion;
	 $ds_animacion_fra=!empty($ds_animacion_fra)?$ds_animacion_fra:$ds_animacion;
     $ds_ref_animacion_esp=!empty($ds_ref_animacion_esp)?$ds_ref_animacion_esp:$ds_ref_animacion;
	 $ds_ref_animacion_fra=!empty($ds_ref_animacion_fra)?$ds_ref_animacion_fra:$ds_ref_animacion;
     $ds_no_sketch_esp=!empty($ds_no_sketch_esp)?$ds_no_sketch_esp:$ds_no_sketch;
	 $ds_no_sketch_fra=!empty($ds_no_sketch_fra)?$ds_no_sketch_fra:$ds_no_sketch;
     $ds_ref_sketch_esp=!empty($ds_ref_sketch_esp)?$ds_ref_sketch_esp:$ds_ref_sketch;
	 $ds_ref_sketch_fra=!empty($ds_ref_sketch_fra)?$ds_ref_sketch_fra:$ds_ref_sketch;

     $Query  = "UPDATE c_leccion_sp SET ";
     $Query .= "fg_animacion='$fg_animacion', fg_ref_animacion='$fg_ref_animacion', no_sketch=$no_sketch, fg_ref_sketch='$fg_ref_sketch', ";
	 $Query .= "ds_no_sketch_esp='$ds_no_sketch_esp',ds_no_sketch_fra='$ds_no_sketch_fra', ";
	 $Query .= "ds_ref_animacion_esp='$ds_ref_animacion_esp',ds_ref_animacion_fra='$ds_ref_animacion_fra', ";
	 $Query .= "ds_ref_sketch_esp='$ds_ref_sketch_esp',ds_ref_sketch_fra='$ds_ref_sketch_fra', ";
     $Query .= "ds_animacion='$ds_animacion',ds_animacion_esp='$ds_animacion_esp',ds_animacion_fra='$ds_animacion_fra', ds_ref_animacion='$ds_ref_animacion', ds_no_sketch='$ds_no_sketch', ds_ref_sketch='$ds_ref_sketch', ";
     $Query .="ds_tiempo_tarea='$ds_tiempo_tarea' ";
     $Query .= "WHERE fl_leccion_sp = $clave";

     EjecutaQuery($Query);

     echo json_encode((Object)array(
        'fg_correcto' => true
    ));
  }

  if($tab==4){

      # ------------------ Quiz -----------------------
      $nb_quiz = RecibeParametroHTML("nb_quiz");
      $no_valor_quiz = RecibeParametroNumerico("no_valor_quiz");
      $ds_course_1 = RecibeParametroNumerico("ds_course_1");
      $c_remaining = RecibeParametroNumerico("c_remaining");
      $fg_tipo_resp_1 = RecibeParametroHTML("fg_tipo_resp_1");
      $fg_tipo_img_1 = RecibeParametroHTML("fg_tipo_img_1");
      $ds_pregunta_1 = RecibeParametroHTML("ds_pregunta_1");
      $ds_pregunta_esp_1 = RecibeParametroHTML("ds_pregunta_esp_1");
      $ds_pregunta_fra_1 = RecibeParametroHTML("ds_pregunta_fra_1");
      $valor_1 = RecibeParametroNumerico("valor_1"); // valor pregunta1
      $ds_quiz_1 = RecibeParametroNumerico("ds_quiz_1"); // valor pregunta1
      $q_remaining_1 = RecibeParametroNumerico("q_remaining_1");
      $no_orden_pregunta = 1;
      $fg_creado_instituto=1;

      $ds_pregunta_esp_1=!empty($ds_pregunta_esp_1)?$ds_pregunta_esp_1:$ds_pregunta_1;
	  $ds_pregunta_fra_1=!empty($ds_pregunta_fra_1)?$ds_pregunta_fra_1:$ds_pregunta_1;

      # --------------- Text Answers Quiz ----------------
      if($fg_tipo_resp_1=="T"){
          $ds_resp_1 = RecibeParametroHTML("ds_resp_1");
          $ds_resp_esp_1 = RecibeParametroHTML("ds_resp_esp_1");
          $ds_resp_fra_1 = RecibeParametroHTML("ds_resp_fra_1");
          $ds_grade_1 = RecibeParametroNumerico("ds_grade_1");
          $ds_resp_2 = RecibeParametroHTML("ds_resp_2");
          $ds_resp_esp_2 = RecibeParametroHTML("ds_resp_esp_2");
          $ds_resp_fra_2 = RecibeParametroHTML("ds_resp_fra_2");
          $ds_grade_2 = RecibeParametroNumerico("ds_grade_2");
          $ds_resp_3 = RecibeParametroHTML("ds_resp_3");
          $ds_resp_esp_3 = RecibeParametroHTML("ds_resp_esp_3");
          $ds_resp_fra_3 = RecibeParametroHTML("ds_resp_fra_3");
          $ds_grade_3 = RecibeParametroNumerico("ds_grade_3");
          $ds_resp_4 = RecibeParametroHTML("ds_resp_4");
          $ds_resp_esp_4 = RecibeParametroHTML("ds_resp_esp_4");
          $ds_resp_fra_4 = RecibeParametroHTML("ds_resp_fra_4");
          $ds_grade_4 = RecibeParametroNumerico("ds_grade_4");
          $ds_resp_esp_1=!empty($ds_resp_esp_1)?$ds_resp_esp_1:$ds_resp_1;
		  $ds_resp_fra_1=!empty($ds_resp_fra_1)?$ds_resp_fra_1:$ds_resp_1;
          $ds_resp_esp_2=!empty($ds_resp_esp_2)?$ds_resp_esp_2:$ds_resp_2;
		  $ds_resp_fra_2=!empty($ds_resp_fra_2)?$ds_resp_fra_2:$ds_resp_2;
          $ds_resp_esp_3=!empty($ds_resp_esp_3)?$ds_resp_esp_3:$ds_resp_3;
		  $ds_resp_fra_3=!empty($ds_resp_fra_3)?$ds_resp_fra_3:$ds_resp_3;
          $ds_resp_esp_4=!empty($ds_resp_esp_4)?$ds_resp_esp_4:$ds_resp_4;
		  $ds_resp_fra_4=!empty($ds_resp_fra_4)?$ds_resp_fra_4:$ds_resp_4;

      } else {
          # Nombre y peso de la respuesta uno de tipo imagen
          $ds_img_1_1  = RecibeParametroHTML("nb_img_prev_mydropzone_1");
          $ds_grade_img_1 = RecibeParametroNumerico("ds_grade_img_1");
          $ds_img_2_1  = RecibeParametroHTML("nb_img_prev_mydropzone_2");
          $ds_grade_img_2 = RecibeParametroNumerico("ds_grade_img_2");
          $ds_img_3_1  = RecibeParametroHTML("nb_img_prev_mydropzone_3");
          $ds_grade_img_3 = RecibeParametroNumerico("ds_grade_img_3");
          $ds_img_4_1  = RecibeParametroHTML("nb_img_prev_mydropzone_4");
          $ds_grade_img_4 = RecibeParametroNumerico("ds_grade_img_4");    
      }

      # Contador de tabs de preguntas
      $no_max_tabs = RecibeParametroNumerico("no_max_tabs");

      # Recibo valores de tabs extras de preguntas
      for($x=2; $x<=$no_max_tabs; $x++){
          $fg_tipo_resp_[$x] = RecibeParametroHTML("fg_tipo_resp_$x");
          $fg_tipo_img_[$x]  = RecibeParametroHTML("fg_tipo_img_$x");
          $ds_pregunta_[$x]  = RecibeParametroHTML("ds_pregunta_$x");
          $ds_pregunta_esp_[$x]  = RecibeParametroHTML("ds_pregunta_esp_$x");
          $ds_pregunta_fra_[$x]  = RecibeParametroHTML("ds_pregunta_fra_$x");
          $ds_quiz_[$x]      = RecibeParametroNumerico("ds_quiz_$x");
          $ds_course_[$x]    = RecibeParametroNumerico("ds_course_$x");
          $valor_[$x]        = RecibeParametroNumerico("valor_$x");
          $q_remaining_[$x]  = RecibeParametroNumerico("q_remaining_$x");   
          $no_orden_pregunta_[$x] = $x;
          
          if($fg_tipo_resp_[$x]=="T"){
              # Respuestas tipo texto 
              $ds_resp_1_[$x]  = RecibeParametroHTML("ds_resp_1_$x");
              $ds_resp_esp_1_[$x]  = RecibeParametroHTML("ds_resp_esp_1_$x");
              $ds_resp_fra_1_[$x]  = RecibeParametroHTML("ds_resp_fra_1_$x");
              $ds_grade_1_[$x] = RecibeParametroNumerico("ds_grade_1_$x");
              $ds_resp_2_[$x]  = RecibeParametroHTML("ds_resp_2_$x");
              $ds_resp_esp_2_[$x]  = RecibeParametroHTML("ds_resp_esp_2_$x");
              $ds_resp_fra_2_[$x]  = RecibeParametroHTML("ds_resp_fra_2_$x");
              $ds_grade_2_[$x] = RecibeParametroNumerico("ds_grade_2_$x");
              $ds_resp_3_[$x]  = RecibeParametroHTML("ds_resp_3_$x");
              $ds_resp_esp_3_[$x]  = RecibeParametroHTML("ds_resp_esp_3_$x");
              $ds_resp_fra_3_[$x]  = RecibeParametroHTML("ds_resp_fra_3_$x");
              $ds_grade_3_[$x] = RecibeParametroNumerico("ds_grade_3_$x");
              $ds_resp_4_[$x]  = RecibeParametroHTML("ds_resp_4_$x");
              $ds_resp_esp_4_[$x]  = RecibeParametroHTML("ds_resp_esp_4_$x");
              $ds_resp_fra_4_[$x]  = RecibeParametroHTML("ds_resp_fra_4_$x");
              $ds_grade_4_[$x] = RecibeParametroNumerico("ds_grade_4_$x");
          } else {
              
              # Respuestas tipo imagen
              $ds_img_1_[$x]  = RecibeParametroHTML("nb_img_prev_mydropzone_1_$x");
              $ds_grade_img_1_[$x] = RecibeParametroNumerico("ds_grade_img_1_$x");
              $ds_img_2_[$x]  = RecibeParametroHTML("nb_img_prev_mydropzone_2_$x");
              $ds_grade_img_2_[$x] = RecibeParametroNumerico("ds_grade_img_2_$x");
              $ds_img_3_[$x]  = RecibeParametroHTML("nb_img_prev_mydropzone_3_$x");
              $ds_grade_img_3_[$x] = RecibeParametroNumerico("ds_grade_img_3_$x");
              $ds_img_4_[$x]  = RecibeParametroHTML("nb_img_prev_mydropzone_4_$x");
              $ds_grade_img_4_[$x] = RecibeParametroNumerico("ds_grade_img_4_$x");      
          }
      }

      #Verificamos si existen preguntas    
      $rowe = RecuperaValor("SELECT COUNT(1) FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave");        
      if(empty($rowe[0])){ #Insertamos preguntas
          #Vemos si existe por lo menos una pregunta a insertar      
          if (!empty($valor_1)){

              #Actualizamos el nombre de la quiz y su valor.
              EjecutaQuery("UPDATE c_leccion_sp SET  nb_quiz='$nb_quiz', no_valor_quiz=$no_valor_quiz WHERE fl_leccion_sp=$clave ");

              #Insetamos pregunta 1 con sus respuestas
              $Query  = "INSERT INTO k_quiz_pregunta (fl_leccion_sp, fg_tipo, ds_pregunta, ds_pregunta_esp, ds_pregunta_fra, ds_valor_pregunta, ds_course_pregunta, fg_posicion_img, no_orden) ";
              $Query .= "VALUES ($clave, '$fg_tipo_resp_1', '$ds_pregunta_1', '$ds_pregunta_esp_1', '$ds_pregunta_fra_1', $valor_1, $ds_course_1, '$fg_tipo_img_1', $no_orden_pregunta)";
              $fl_quiz_pregunta = EjecutaInsert($Query);
              
              #Respuestas tipo imagen
              if($fg_tipo_resp_1 == 'I'){
                  EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_1 WHERE no_orden = 1 AND no_tab = 1 AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_1_1'");
                  EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_2 WHERE no_orden = 2 AND no_tab = 1 AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_2_1'");
                  EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_3 WHERE no_orden = 3 AND no_tab = 1 AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_3_1'");
                  EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_4 WHERE no_orden = 4 AND no_tab = 1 AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_4_1'");          
              }
              else{ #ICH: Respuestas tipo texto          
                  EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 1, '$ds_resp_1', '$ds_resp_esp_1', '$ds_resp_fra_1', $ds_grade_1, 1)");
                  EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 2, '$ds_resp_2', '$ds_resp_esp_2', '$ds_resp_fra_2', $ds_grade_2, 1)");
                  EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 3, '$ds_resp_3', '$ds_resp_esp_3', '$ds_resp_fra_3', $ds_grade_3, 1)");
                  EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 4, '$ds_resp_4', '$ds_resp_esp_4', '$ds_resp_fra_4', $ds_grade_4, 1)");
              }    
              
              #Son mas de dos preguntas
              if($no_max_tabs>=2){
                  for($x=2; $x<=$no_max_tabs; $x++){
					  
					  if(empty($ds_pregunta_esp_[$x])){
						  $ds_pregunta_esp=$ds_pregunta_[$x];
					  } else {
						  $ds_pregunta_esp=$ds_pregunta_esp_[$x];
					  }
					  if(empty($ds_pregunta_fra_[$x])){
						  $ds_pregunta_fra=$ds_pregunta_[$x];						 
					  } else {
						  $ds_pregunta_fra=$ds_pregunta_fra_[$x];
					  }

					  # Spanish
					  $ds_resp_esp_1=!empty($ds_resp_esp_1_[$x])?$ds_resp_esp_1_[$x]:$ds_resp_1_[$x];
					  $ds_resp_esp_2=!empty($ds_resp_esp_2_[$x])?$ds_resp_esp_2_[$x]:$ds_resp_2_[$x];
					  $ds_resp_esp_3=!empty($ds_resp_esp_3_[$x])?$ds_resp_esp_3_[$x]:$ds_resp_3_[$x];
					  $ds_resp_esp_4=!empty($ds_resp_esp_4_[$x])?$ds_resp_esp_4_[$x]:$ds_resp_4_[$x];

					  # French
					  $ds_resp_fra_1=!empty($ds_resp_fra_1_[$x])?$ds_resp_fra_1_[$x]:$ds_resp_1_[$x];
					  $ds_resp_fra_2=!empty($ds_resp_fra_2_[$x])?$ds_resp_fra_2_[$x]:$ds_resp_2_[$x];
					  $ds_resp_fra_3=!empty($ds_resp_fra_3_[$x])?$ds_resp_fra_3_[$x]:$ds_resp_3_[$x];
					  $ds_resp_fra_4=!empty($ds_resp_fra_4_[$x])?$ds_resp_fra_4_[$x]:$ds_resp_4_[$x];
					  
                      #Inserta pregunta
                      $Query  = "INSERT INTO k_quiz_pregunta (fl_leccion_sp, fg_tipo, ds_pregunta, ds_pregunta_esp, ds_pregunta_fra, ds_valor_pregunta, ds_course_pregunta, fg_posicion_img, no_orden) ";
                      $Query .= "VALUES ($clave, '$fg_tipo_resp_[$x]', '$ds_pregunta_[$x]', '$ds_pregunta_esp', '$ds_pregunta_fra', $valor_[$x], $ds_course_[$x], '$fg_tipo_img_[$x]', $x)";
                      $fl_quiz_pregunta = EjecutaInsert($Query);

                      #Respuesta tipo imagen
                      if($fg_tipo_resp_[$x] == 'I'){
                          EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_1_[$x] WHERE no_orden = 1 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_1_[$x]' ");
                          EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_2_[$x] WHERE no_orden = 2 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_2_[$x]' ");
                          EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_3_[$x] WHERE no_orden = 3 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_3_[$x]' ");
                          EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_4_[$x] WHERE no_orden = 4 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_4_[$x]' ");
                      } else {  #ICH: Respuesta tipo texto
                          EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 1, '$ds_resp_1_[$x]', '$ds_resp_esp_1', '$ds_resp_fra_1', $ds_grade_1_[$x], $x)");
                          EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 2, '$ds_resp_2_[$x]', '$ds_resp_esp_2', '$ds_resp_fra_2', $ds_grade_2_[$x], $x)");
                          EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 3, '$ds_resp_3_[$x]', '$ds_resp_esp_3', '$ds_resp_fra_3', $ds_grade_3_[$x], $x)");
                          EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 4, '$ds_resp_4_[$x]', '$ds_resp_esp_4', '$ds_resp_fra_4', $ds_grade_4_[$x], $x)");
                      }
                  }    
              }
          }
      } else {

          #Actualizamos el nombre de la quiz y su valor.
          EjecutaQuery("UPDATE c_leccion_sp SET  nb_quiz='$nb_quiz', no_valor_quiz=$no_valor_quiz WHERE fl_leccion_sp=$clave ");

          #Actualizamos preguntas
          $Query  = "UPDATE k_quiz_pregunta SET fg_tipo ='$fg_tipo_resp_1', ds_pregunta = '$ds_pregunta_1', ds_pregunta_esp = '$ds_pregunta_esp_1', ds_pregunta_fra = '$ds_pregunta_fra_1', ds_valor_pregunta = $valor_1, ";
          $Query .= "ds_course_pregunta = $ds_course_1, fg_posicion_img = '$fg_tipo_img_1' WHERE fl_leccion_sp = $clave AND no_orden = $no_orden_pregunta ";
          EjecutaQuery($Query);
          
          # Consultamos clave de pregunta y actualizamos sus respuestas, solo de la pregunta 1      
          $row = RecuperaValor("SELECT fl_quiz_pregunta FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave AND no_orden = 1 ");
          $fl_quiz_preg_1_1 = ($row[0]);
          if($fg_tipo_resp_1=="T"){
			  
              EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_resp_1', ds_respuesta_esp = '$ds_resp_esp_1', ds_respuesta_fra = '$ds_resp_fra_1', ds_valor_respuesta = '$ds_grade_1' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 1");
              EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_resp_2', ds_respuesta_esp = '$ds_resp_esp_2', ds_respuesta_fra = '$ds_resp_fra_2', ds_valor_respuesta = '$ds_grade_2' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 2");
              EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_resp_3', ds_respuesta_esp = '$ds_resp_esp_3', ds_respuesta_fra = '$ds_resp_fra_3', ds_valor_respuesta = '$ds_grade_3' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 3");
              EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_resp_4', ds_respuesta_esp = '$ds_resp_esp_4', ds_respuesta_fra = '$ds_resp_fra_4', ds_valor_respuesta = '$ds_grade_4' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 4");
          } else {
              EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_img_1_1', ds_valor_respuesta = '$ds_grade_img_1' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 1 AND ds_respuesta='$ds_img_1_1'");
              EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_img_2_1', ds_valor_respuesta = '$ds_grade_img_2' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 2 AND ds_respuesta='$ds_img_2_1'");
              EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_img_3_1', ds_valor_respuesta = '$ds_grade_img_3' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 3 AND ds_respuesta='$ds_img_3_1'");
              EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_img_4_1', ds_valor_respuesta = '$ds_grade_img_4' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 4 AND ds_respuesta='$ds_img_4_1'");
          }

          # Actualizamos todas las respuestas de todas las preguntas
          for($x=2; $x<=$no_max_tabs; $x++){ 
		  
			  if(empty($ds_pregunta_esp_[$x])){
				  $ds_pregunta_esp=$ds_pregunta_[$x];
			  } else {
				  $ds_pregunta_esp=$ds_pregunta_esp_[$x];
			  }
			  if(empty($ds_pregunta_fra_[$x])){
				  $ds_pregunta_fra=$ds_pregunta_[$x];						 
			  } else {
				  $ds_pregunta_fra=$ds_pregunta_fra_[$x];
			  }

			  # Spanish
			  $ds_resp_esp_1=!empty($ds_resp_esp_1_[$x])?$ds_resp_esp_1_[$x]:$ds_resp_1_[$x];
			  $ds_resp_esp_2=!empty($ds_resp_esp_2_[$x])?$ds_resp_esp_2_[$x]:$ds_resp_2_[$x];
			  $ds_resp_esp_3=!empty($ds_resp_esp_3_[$x])?$ds_resp_esp_3_[$x]:$ds_resp_3_[$x];
			  $ds_resp_esp_4=!empty($ds_resp_esp_4_[$x])?$ds_resp_esp_4_[$x]:$ds_resp_4_[$x];

			  # French
			  $ds_resp_fra_1=!empty($ds_resp_fra_1_[$x])?$ds_resp_fra_1_[$x]:$ds_resp_1_[$x];
			  $ds_resp_fra_2=!empty($ds_resp_fra_2_[$x])?$ds_resp_fra_2_[$x]:$ds_resp_2_[$x];
			  $ds_resp_fra_3=!empty($ds_resp_fra_3_[$x])?$ds_resp_fra_3_[$x]:$ds_resp_3_[$x];
			  $ds_resp_fra_4=!empty($ds_resp_fra_4_[$x])?$ds_resp_fra_4_[$x]:$ds_resp_4_[$x];

              # Verificamos si existe la pregunta que se quiere actualizar
              $row = RecuperaValor("SELECT COUNT(1) FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave AND no_orden = $x ");
              if(!empty($row[0])){ // SI existe, entonces actualizamos 
                  EjecutaQuery("UPDATE k_quiz_pregunta SET ds_pregunta = '$ds_pregunta_[$x]', ds_pregunta_esp = '$ds_pregunta_esp_[$x]',ds_pregunta_fra = '$ds_pregunta_fra_[$x]', fg_tipo = '$fg_tipo_resp_[$x]', fg_posicion_img = '$fg_tipo_img_[$x]', ds_valor_pregunta = $valor_[$x] WHERE fl_leccion_sp = $clave AND no_orden = $x");
                  
                  // Recuperamos clave de pregunta
                  $row = RecuperaValor("SELECT fl_quiz_pregunta FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave AND no_orden = $x ");
                  $fl_quiz_preg_1_[$x] = ($row[0]);

                  if($fg_tipo_resp_[$x] == "I"){            
                      EjecutaQuery("UPDATE k_quiz_respuesta SET ds_valor_respuesta = $ds_grade_img_1_[$x] WHERE no_orden = 1 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x] AND ds_respuesta='$ds_img_1_[$x]'");
                      EjecutaQuery("UPDATE k_quiz_respuesta SET ds_valor_respuesta = $ds_grade_img_2_[$x] WHERE no_orden = 2 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x] AND ds_respuesta='$ds_img_2_[$x]'");
                      EjecutaQuery("UPDATE k_quiz_respuesta SET ds_valor_respuesta = $ds_grade_img_3_[$x] WHERE no_orden = 3 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x] AND ds_respuesta='$ds_img_3_[$x]'");
                      EjecutaQuery("UPDATE k_quiz_respuesta SET ds_valor_respuesta = $ds_grade_img_4_[$x] WHERE no_orden = 4 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x] AND ds_respuesta='$ds_img_4_[$x]'");
                  } else {
                      EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta='$ds_resp_1_[$x]', ds_respuesta_esp='$ds_resp_esp_1', ds_respuesta_fra='$ds_resp_fra_1', ds_valor_respuesta = $ds_grade_1_[$x] WHERE no_orden = 1 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x]");
                      EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta='$ds_resp_2_[$x]', ds_respuesta_esp='$ds_resp_esp_2', ds_respuesta_fra='$ds_resp_fra_2', ds_valor_respuesta = $ds_grade_2_[$x] WHERE no_orden = 2 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x]");
                      EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta='$ds_resp_3_[$x]', ds_respuesta_esp='$ds_resp_esp_3', ds_respuesta_fra='$ds_resp_fra_3', ds_valor_respuesta = $ds_grade_3_[$x] WHERE no_orden = 3 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x]");
                      EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta='$ds_resp_4_[$x]', ds_respuesta_esp='$ds_resp_esp_4', ds_respuesta_fra='$ds_resp_fra_4', ds_valor_respuesta = $ds_grade_4_[$x] WHERE no_orden = 4 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x]");
                  }
              } else { // NO existe, entonces insertamos
                  $Query  = "INSERT INTO k_quiz_pregunta (fl_leccion_sp, fg_tipo, ds_pregunta, ds_pregunta_esp, ds_pregunta_fra, ds_valor_pregunta, ds_course_pregunta, fg_posicion_img, no_orden) ";
                  $Query .= "VALUES ($clave, '$fg_tipo_resp_[$x]', '$ds_pregunta_[$x]', '$ds_pregunta_esp', '$ds_pregunta_fra', $valor_[$x], $ds_course_[$x], '$fg_tipo_img_[$x]', $x)";
                  $fl_quiz_pregunta = EjecutaInsert($Query);
                  
                  #Respuesta tipo imagen
                  if($fg_tipo_resp_[$x] == 'I'){
                      EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_1_[$x] WHERE no_orden = 1 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_1_[$x]'");
                      EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_2_[$x] WHERE no_orden = 2 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_2_[$x]'");
                      EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_3_[$x] WHERE no_orden = 3 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_3_[$x]'");
                      EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_4_[$x] WHERE no_orden = 4 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_4_[$x]'");
                  } else {  #ICH: Respuesta tipo texto
                      EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 1, '$ds_resp_1_[$x]', '$ds_resp_esp_1', '$ds_resp_fra_1', $ds_grade_1_[$x], $x)");
                      EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 2, '$ds_resp_2_[$x]', '$ds_resp_esp_2', '$ds_resp_fra_2', $ds_grade_2_[$x], $x)");
                      EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 3, '$ds_resp_3_[$x]', '$ds_resp_esp_3', '$ds_resp_fra_3', $ds_grade_3_[$x], $x)");
                      EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 4, '$ds_resp_4_[$x]', '$ds_resp_esp_4', '$ds_resp_fra_4', $ds_grade_4_[$x], $x)");
                  }
              }
          }
      }

    echo json_encode((Object)array(
        'fg_correcto' => true
	));
  } #end tab4

  if($tab==5){
	 #RecibeParametros 
     $no_val_rub=RecibeParametroNumerico('no_val_rub'); 
	 $Query="UPDATE c_leccion_sp SET no_valor_rubric=$no_val_rub WHERE fl_leccion_sp=$clave ";	 
     EjecutaQuery($Query);
  
     echo json_encode((Object)array(
		'fg_correcto' => true
     ));
  }

 ?>