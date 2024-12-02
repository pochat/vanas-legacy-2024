<?php
  # Libreria de funciones
  require '../../lib/general.inc.php';

  $fg_creado_instituto=$_POST['fg_creado_instituto']??NULL;

  #Variable initialization
  $fl_programa_err=$fl_programa_err??NULL;
  $no_grado_err=$no_grado_err??NULL;
  $no_semana_err=$no_semana_err??NULL;
  $ds_titulo_err=$ds_titulo_err??NULL;
  $ds_leccion_err=$ds_leccion_err??NULL;
  $no_sketch_err=$no_sketch_err??NULL;
  $ds_learning_err=$ds_learning_err??NULL;
  $no_val_rub_err=$no_val_rub_err??NULL;
  $no_max_grade_err=$no_max_grade_err??NULL;
  $tab_description_err=$tab_description_err??NULL;
  $tab_rubric_err=$tab_rubric_err??NULL;
  $ds_animacion_err=$ds_animacion_err??NULL;
  $ds_ref_animacion_err=$ds_ref_animacion_err??NULL;
  $ds_no_sketch_err=$ds_no_sketch_err??NULL;
  $ds_ref_sketch_err=$ds_ref_sketch_err??NULL;
  $valor_ini_preg=$valor_ini_preg??NULL;
  
  if(empty($fg_creado_instituto)){
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion();

  # Recibe la clave
  $clave = RecibeParametroNumerico("clave");

  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_LMED_SP, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
	  }
  }else{
	  # Recibe la clave
	  $clave = RecibeParametroNumerico("clave");
	  
  }

  # Recibe parametros
  $fg_error = 0;

  # ------ Datos generales de Lessons and Media ------
  $fl_programa = RecibeParametroNumerico("fl_programa");
  $no_grado = RecibeParametroNumerico("no_grado");
  $no_semana = RecibeParametroNumerico("no_semana");
  $ds_titulo = RecibeParametroHTML("ds_titulo");
  $ds_titulo_esp = RecibeParametroHTML("ds_titulo_esp");
  $ds_titulo_fra = RecibeParametroHTML("ds_titulo_fra")??NULL;
  $ds_learning = RecibeParametroHTML("ds_learning");
  $ds_learning_esp = RecibeParametroHTML("ds_learning_esp")??NULL;
  $ds_learning_fra = RecibeParametroHTML("ds_learning_fra")??NULL;
  $ds_leccion = RecibeParametroHTML("ds_leccion")??NULL;
  $ds_leccion_esp = RecibeParametroHTML("ds_leccion_esp")??NULL;
  $ds_leccion_fra = RecibeParametroHTML("ds_leccion_fra")??NULL;
  $ds_vl_ruta = RecibeParametroHTML("ds_vl_ruta");
  $camp_progreso_hls = RecibeParametroHTML("camp_progreso_hls");
  $ds_vl_duracion = RecibeParametroHTML("ds_vl_duracion");
  $fe_vl_alta = RecibeParametroHTML("fe_vl_alta");
  $fg_animacion = RecibeParametroBinario("fg_animacion");
  $fg_ref_animacion = RecibeParametroBinario("fg_ref_animacion");
  $no_sketch = RecibeParametroNumerico("no_sketch");
  $fg_ref_sketch = RecibeParametroBinario("fg_ref_sketch");
  $archivo = RecibeParametroHTML("nb_video");
  $archivo_a = RecibeParametroHTML("archivo_a");
  $ds_as_ruta = RecibeParametroHTML("ds_as_ruta");
  $ds_as_duracion = RecibeParametroHTML("ds_as_duracion");
  $fe_as_alta = RecibeParametroHTML("fe_as_alta");
  $archivo1 = RecibeParametroHTML("archivo1");
  $archivo1_a = RecibeParametroHTML("archivo1_a");
  $fg_reset_video = RecibeParametroBinario("fg_reset_video");
  $ds_tiempo_tarea = RecibeParametroHTML("ds_tiempo_tarea");
  $ds_animacion = RecibeParametroHTML("ds_animacion");
  $ds_animacion_esp = RecibeParametroHTML("ds_animacion_esp");
  $ds_animacion_fra = RecibeParametroHTML("ds_animacion_fra");
  $ds_ref_animacion = RecibeParametroHTML("ds_ref_animacion");
  $ds_ref_animacion_esp = RecibeParametroHTML("ds_ref_animacion_esp");
  $ds_ref_animacion_fra = RecibeParametroHTML("ds_ref_animacion_fra");
  $ds_no_sketch = RecibeParametroHTML("ds_no_sketch");
  $ds_no_sketch_esp = RecibeParametroHTML("ds_no_sketch_esp");
  $ds_no_sketch_fra = RecibeParametroHTML("ds_no_sketch_fra");
  $ds_ref_sketch = RecibeParametroHTML("ds_ref_sketch");
  $ds_ref_sketch_esp = RecibeParametroHTML("ds_ref_sketch_esp");
  $ds_ref_sketch_fra = RecibeParametroHTML("ds_ref_sketch_fra");
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
  $fg_creado_instituto=RecibeParametroNumerico('fg_creado_instituto');
  if(!empty($fg_creado_instituto)){
	  $fl_usuario=RecibeParametroNumerico('fl_usuario');
      #Recupermaos solo el nombre del archivo
      $explosion=explode('.',$ds_vl_ruta);
      $ds_vl_ruta=array_shift($explosion);

  }

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
  }
  else{
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
    }
    else{
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

  # Valor de rubric
  $no_val_rub = RecibeParametroNumerico('no_val_rub');
  $no_ter_co = RecibeParametroNumerico('no_ter_co');
  $sum_val_grade = RecibeParametroNumerico('sum_val_grade');

  # Valida campos obligatorios
  if(empty($fl_programa))
    $fl_programa_err = ERR_REQUERIDO;
  if(empty($no_grado))
    $no_grado_err = ERR_REQUERIDO;
  if(empty($no_semana))
    $no_semana_err = ERR_REQUERIDO;
  if(empty($ds_titulo))
    $ds_titulo_err = ERR_REQUERIDO;
  if(empty($ds_learning))
    $ds_learning_err = ERR_REQUERIDO;
  if(empty($ds_leccion))
    $ds_leccion_err = ERR_REQUERIDO;
  # Validamos si prende flag de animacion debe tener texto
  if(!empty($fg_animacion) && empty($ds_animacion)){
    $ds_animacion_err = ERR_REQUERIDO;
  }
  if(!empty($fg_ref_animacion) && empty($ds_ref_animacion)){
    $ds_ref_animacion_err = ERR_REQUERIDO;
  }
  if(!empty($no_sketch) && empty($ds_no_sketch)){
    $ds_no_sketch_err = ERR_REQUERIDO;
  }
  if(!empty($fg_ref_sketch) && empty($ds_ref_sketch)){
    $ds_ref_sketch_err = ERR_REQUERIDO;
  }
  # Valida enteros
  if(empty($no_grado_err) AND !ValidaEntero($no_grado))
    $no_grado_err = ERR_ENTERO;
  if(empty($no_grado_err) AND $no_grado > MAX_TINYINT)
    $no_grado_err = ERR_TINYINT;
  if(empty($no_semana_err) AND !ValidaEntero($no_semana))
    $no_semana_err = ERR_ENTERO;
  if(empty($no_semana_err) AND $no_semana > MAX_TINYINT)
    $no_semana_err = ERR_TINYINT;
  if($no_sketch > MAX_TINYINT)
    $no_sketch_err = ERR_TINYINT;
  
  # Verifica que no exista la leccion
  if(empty($fl_programa_err) AND empty($no_grado_err) AND empty($no_semana_err)) {
    $Query  = "SELECT count(1) ";
    $Query .= "FROM c_leccion_sp ";
    $Query .= "WHERE fl_programa_sp = $fl_programa ";
    $Query .= "AND no_grado = $no_grado ";
    $Query .= "AND no_semana = $no_semana ";
    if(!empty($clave))
      $Query .= "AND fl_leccion_sp<>$clave";
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      $no_semana_err = 109; # Existing lesson found for this program.
  }
  
  /* Validacion de rubric */
  
  # 1- Validamos si NO existenten criterios y el rubric tiene valor
  if($no_val_rub > 0){
    $cont_criterios = RecuperaValor("SELECT COUNT(1) FROM k_criterio_programa_fame WHERE fl_programa_sp = $clave");
    if(empty($cont_criterios[0]))
      $no_val_rub_err = 1; // No hay registros en tabla
  }
 
  # 2- Validamos SI existenten criterios y el rubric NO tiene valor
  $cont_criterios = RecuperaValor("SELECT COUNT(1) FROM k_criterio_programa_fame WHERE fl_programa_sp = $clave");
  if(($cont_criterios[0]) AND empty($no_val_rub))
    $no_val_rub_err = 2; // Hay registros en tabla, pero el rubric no tiene valor
  
  # 3- Validamos que todos los criterios tengan un valor 
  $cont_criterios = RecuperaValor("SELECT COUNT(1) FROM k_criterio_programa_fame WHERE fl_programa_sp = $clave AND no_valor IS NULL");
  if(($cont_criterios[0]))
    $no_val_rub_err = 3; // Existen criterios sin valor asignado
  
  # 4- Validamos el max grade
  // Es mayor a 100
  if($sum_val_grade > 100)
    $no_max_grade_err = 1;
  
  // Valida el valor de los criterios
  if($sum_val_grade != 100 and $sum_val_grade > 0)
    $no_max_grade_err = 2;
  
  // Comprobamos error para marcar tab 
  if(!empty($no_grado_err) OR !empty($no_semana_err) OR !empty($ds_titulo_err) OR !empty($ds_learning_err) OR !empty($ds_leccion_err))
    $tab_description_err = 1;
  
  // Comprobamos error para marcar tab 
  if(!empty($no_val_rub_err) OR !empty($no_max_grade_err))
    $tab_rubric_err = 1;
  if($fg_creado_instituto==1){  }else{
      # Regresa a la forma con error
      $fg_error = $fl_programa_err    || $no_grado_err      || $no_semana_err       || $ds_titulo_err || $ds_leccion_err || $no_sketch_err || $ds_learning_err
                  || $no_val_rub_err  || $no_max_grade_err  || $tab_description_err || $tab_rubric_err || $ds_animacion_err  || $ds_ref_animacion_err 
                  || $ds_no_sketch_err || $ds_ref_sketch_err;

  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('fl_programa' , $fl_programa);
    Forma_CampoOculto('fl_programa_err' , $fl_programa_err);
    Forma_CampoOculto('no_grado' , $no_grado);
    Forma_CampoOculto('no_grado_err' , $no_grado_err);
    Forma_CampoOculto('no_semana' , $no_semana);
    Forma_CampoOculto('no_semana_err' , $no_semana_err);
    Forma_CampoOculto('ds_titulo' , $ds_titulo);
    Forma_CampoOculto('ds_titulo_esp' , $ds_titulo_esp);
    Forma_CampoOculto('ds_titulo_fra' , $ds_titulo_fra);
    Forma_CampoOculto('ds_titulo_err' , $ds_titulo_err);
    Forma_CampoOculto('ds_learning' , $ds_learning);
    Forma_CampoOculto('ds_learning_esp' , $ds_learning_esp);
    Forma_CampoOculto('ds_learning_fra' , $ds_learning_fra);
    Forma_CampoOculto('ds_learning_err' , $ds_learning_err);
    Forma_CampoOculto('ds_leccion' , $ds_leccion);
    Forma_CampoOculto('ds_leccion_esp' , $ds_leccion_esp);
    Forma_CampoOculto('ds_leccion_fra' , $ds_leccion_fra);
    Forma_CampoOculto('ds_leccion_err' , $ds_leccion_err);
    Forma_CampoOculto('ds_vl_ruta' , $ds_vl_ruta);
    Forma_CampoOculto('ds_vl_duracion' , $ds_vl_duracion);
    Forma_CampoOculto('fe_vl_alta' , $fe_vl_alta);
    Forma_CampoOculto('ds_as_duracion' , $ds_as_duracion);
    Forma_CampoOculto('fe_as_alta' , $fe_as_alta);
    Forma_CampoOculto('fg_reset_video' , $fg_reset_video);
    Forma_CampoOculto('ds_tiempo_tarea' , $ds_tiempo_tarea);
    Forma_CampoOculto('fg_animacion' , $fg_animacion);
    Forma_CampoOculto('ds_animacion' , $ds_animacion);
    Forma_CampoOculto('ds_animacion_esp' , $ds_animacion_esp);
    Forma_CampoOculto('ds_animacion_fra' , $ds_animacion_fra);
    Forma_CampoOculto('ds_animacion_err' , $ds_animacion_err);
    Forma_CampoOculto('fg_ref_animacion' , $fg_ref_animacion);
    Forma_CampoOculto('ds_ref_animacion' , $ds_ref_animacion);
    Forma_CampoOculto('ds_ref_animacion_esp' , $ds_ref_animacion_esp);
    Forma_CampoOculto('ds_ref_animacion_fra' , $ds_ref_animacion_fra);
    Forma_CampoOculto('ds_ref_animacion_err' , $ds_ref_animacion_err);
    Forma_CampoOculto('no_sketch' , $no_sketch);
    Forma_CampoOculto('ds_no_sketch' , $ds_no_sketch);
    Forma_CampoOculto('ds_no_sketch_esp' , $ds_no_sketch_esp);
    Forma_CampoOculto('ds_no_sketch_fra' , $ds_no_sketch_fra);
    Forma_CampoOculto('ds_no_sketch_err' , $ds_no_sketch_err);
    Forma_CampoOculto('no_sketch_err' , $no_sketch_err);
    Forma_CampoOculto('fg_ref_sketch' , $fg_ref_sketch);
    Forma_CampoOculto('ds_ref_sketch' , $ds_ref_sketch);
    Forma_CampoOculto('ds_ref_sketch_esp' , $ds_ref_sketch_esp);
    Forma_CampoOculto('ds_ref_sketch_fra' , $ds_ref_sketch_fra);
    Forma_CampoOculto('ds_ref_sketch_err' , $ds_ref_sketch_err);
    Forma_CampoOculto('ds_as_ruta' , $ds_as_ruta);
    # Quiz
    Forma_CampoOculto('nb_quiz' , $nb_quiz);
    Forma_CampoOculto('no_valor_quiz' , $no_valor_quiz);
    Forma_CampoOculto('ds_course_1' , $ds_course_1);
    Forma_CampoOculto('c_remaining' , $c_remaining);
    Forma_CampoOculto('fg_tipo_resp_1' , $fg_tipo_resp_1);
    Forma_CampoOculto('fg_tipo_img_1' , $fg_tipo_img_1);
    Forma_CampoOculto('ds_pregunta_1' , $ds_pregunta_1);
    Forma_CampoOculto('ds_pregunta_esp_1' , $ds_pregunta_esp_1);
    Forma_CampoOculto('ds_pregunta_fra_1' , $ds_pregunta_fra_1);
    Forma_CampoOculto('valor_1' , $valor_1);
    if(empty($ds_quiz_1))
      $ds_quiz_1 = $valor_1;
    Forma_CampoOculto('ds_quiz_1' , $ds_quiz_1);
    Forma_CampoOculto('q_remaining_1' , $q_remaining_1);    
    if($fg_tipo_resp_1=="T"){
      Forma_CampoOculto('ds_resp_1' , $ds_resp_1);
      Forma_CampoOculto('ds_resp_esp_1' , $ds_resp_esp_1);
      Forma_CampoOculto('ds_resp_fra_1' , $ds_resp_fra_1);
      Forma_CampoOculto('ds_grade_1' , $ds_grade_1);
      Forma_CampoOculto('ds_resp_2' , $ds_resp_2);
      Forma_CampoOculto('ds_resp_esp_2' , $ds_resp_esp_2);
      Forma_CampoOculto('ds_resp_fra_2' , $ds_resp_fra_2);
      Forma_CampoOculto('ds_grade_2' , $ds_grade_2);
      Forma_CampoOculto('ds_resp_3' , $ds_resp_3);
      Forma_CampoOculto('ds_resp_esp_3' , $ds_resp_esp_3);
      Forma_CampoOculto('ds_resp_fra_3' , $ds_resp_fra_3);
      Forma_CampoOculto('ds_grade_3' , $ds_grade_3);
      Forma_CampoOculto('ds_resp_4' , $ds_resp_4);
      Forma_CampoOculto('ds_resp_esp_4' , $ds_resp_esp_4);
      Forma_CampoOculto('ds_resp_fra_4' , $ds_resp_fra_4);
      Forma_CampoOculto('ds_grade_4' , $ds_grade_4);
    }
    else{
      Forma_CampoOculto('nb_img_prev_mydropzone_1_1' , $ds_img_1_1);
      Forma_CampoOculto('ds_grade_img_1' , $ds_grade_img_1);
      Forma_CampoOculto('nb_img_prev_mydropzone_2_1' , $ds_img_2_1);
      Forma_CampoOculto('ds_grade_img_2' , $ds_grade_img_2);
      Forma_CampoOculto('nb_img_prev_mydropzone_3_1' , $ds_img_3_1);
      Forma_CampoOculto('ds_grade_img_3' , $ds_grade_img_3);
      Forma_CampoOculto('nb_img_prev_mydropzone_4_1' , $ds_img_4_1);
      Forma_CampoOculto('ds_grade_img_4' , $ds_grade_img_4);
    }
    
    Forma_CampoOculto('no_max_tabs' , $no_max_tabs);
    
    # Regreso valores de tabs extras de preguntas
    for($x=2; $x<=$no_max_tabs; $x++){
      Forma_CampoOculto("fg_tipo_resp_$x" , $fg_tipo_resp_[$x]);
      Forma_CampoOculto("fg_tipo_img_$x" , $fg_tipo_img_[$x]);
      Forma_CampoOculto("fg_tipo_img_reg_$x" , $fg_tipo_img_reg_[$x]);
      Forma_CampoOculto("ds_pregunta_$x" , $ds_pregunta_[$x]);
      Forma_CampoOculto("ds_pregunta_esp_$x" , $ds_pregunta_esp_[$x]);
      Forma_CampoOculto("ds_pregunta_fra_$x" , $ds_pregunta_fra_[$x]);
      Forma_CampoOculto("ds_quiz_$x" , $tot_suma);
      Forma_CampoOculto("ds_course_$x" , $ds_course_[$x]);
      Forma_CampoOculto("valor_$x" , $valor_[$x]);
      Forma_CampoOculto("q_remaining_$x" , $q_remaining_[$x]);
      
      if($fg_tipo_resp_[$x]=="T"){
        # Respuestas tipo texto
        Forma_CampoOculto("ds_resp_1_$x" , $ds_resp_1_[$x]);
        Forma_CampoOculto("ds_resp_esp_1_$x" , $ds_resp_esp_1_[$x]);
        Forma_CampoOculto("ds_resp_fra_1_$x" , $ds_resp_fra_1_[$x]);
        Forma_CampoOculto("ds_grade_1_$x" , $ds_grade_1_[$x]);
        Forma_CampoOculto("ds_resp_2_$x" , $ds_resp_2_[$x]);
        Forma_CampoOculto("ds_resp_esp_2_$x" , $ds_resp_esp_2_[$x]);
        Forma_CampoOculto("ds_resp_fra_2_$x" , $ds_resp_fra_2_[$x]);
        Forma_CampoOculto("ds_grade_2_$x" , $ds_grade_2_[$x]);
        Forma_CampoOculto("ds_resp_3_$x" , $ds_resp_3_[$x]);
        Forma_CampoOculto("ds_resp_esp_3_$x" , $ds_resp_esp_3_[$x]);
        Forma_CampoOculto("ds_resp_fra_3_$x" , $ds_resp_fra_3_[$x]);
        Forma_CampoOculto("ds_grade_3_$x" , $ds_grade_3_[$x]);
        Forma_CampoOculto("ds_resp_4_$x" , $ds_resp_4_[$x]);
        Forma_CampoOculto("ds_resp_esp_4_$x" , $ds_resp_esp_4_[$x]);
        Forma_CampoOculto("ds_resp_fra_4_$x" , $ds_resp_fra_4_[$x]);
        Forma_CampoOculto("ds_grade_4_$x" , $ds_grade_4_[$x]);
      }
      else{
        # Respuestas tipo imagen    
        Forma_CampoOculto("nb_img_prev_mydropzone_1_$x" , $ds_img_1_[$x]);
        Forma_CampoOculto("ds_grade_img_1_$x" , $ds_grade_img_1_[$x]);
        Forma_CampoOculto("nb_img_prev_mydropzone_2_$x" , $ds_img_2_[$x]);
        Forma_CampoOculto("ds_grade_img_2_$x" , $ds_grade_img_2_[$x]);
        Forma_CampoOculto("nb_img_prev_mydropzone_3_$x" , $ds_img_3_[$x]);
        Forma_CampoOculto("ds_grade_img_3_$x" , $ds_grade_img_3_[$x]);
        Forma_CampoOculto("nb_img_prev_mydropzone_4_$x" , $ds_img_4_[$x]);
        Forma_CampoOculto("ds_grade_img_4_$x" , $ds_grade_img_4_[$x]);
        
      }
    }  
    # Rubric
    Forma_CampoOculto('no_ter_co' , $no_ter_co);
    Forma_CampoOculto('no_val_rub' , $no_val_rub);
    Forma_CampoOculto('no_val_rub_err' , $no_val_rub_err);
    Forma_CampoOculto('no_max_grade_err' , $no_max_grade_err);
    Forma_CampoOculto('tab_rubric_err' , $tab_rubric_err);
    Forma_CampoOculto('tab_description_err' , $tab_description_err);
    echo "\n</form>
    <script>
      document.datos.submit();
    </script>
    </body></html>";
    exit;
  }

  }
  # Prepara la fecha de alta del archivo
  $fe_vl_alta = "CURRENT_TIMESTAMP";
  # Inserta o actualiza el registro
  #ICH: Es nuevo registro
  if(empty($clave)) {
    if(ExisteEnTabla('k_video_temp','fl_usuario', $fl_usuario)){
      $row5 = RecuperaValor("SELECT nb_archivo FROM k_video_temp WHERE fl_usuario=$fl_usuario");
      $ds_vl_ruta = $row5["nb_archivo"];
    }    
    

  if($fg_creado_instituto==1){

  }else{

    $Query  = "INSERT INTO c_leccion_sp (fl_programa_sp, no_grado, no_semana, ds_titulo, ds_titulo_esp, ds_titulo_fra, ds_leccion, ds_leccion_esp, ds_leccion_fra, ";
    $Query .= "ds_vl_ruta, ds_vl_duracion, fe_vl_alta, fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch, ds_animacion, ds_animacion_esp, ds_animacion_fra, ";
    $Query .= "ds_ref_animacion, ds_ref_animacion_esp, ds_ref_animacion_fra, ds_no_sketch, ds_no_sketch_esp, ds_no_sketch_fra, ds_ref_sketch, ds_ref_sketch_esp, ds_ref_sketch_fra, ds_tiempo_tarea, ";
    $Query .= "nb_quiz, no_valor_quiz, ds_learning, ds_learning_esp, ds_learning_fra, no_valor_rubric) ";
    $Query .= "VALUES($fl_programa, $no_grado, $no_semana, \"$ds_titulo\", \"$ds_titulo_esp\", \"$ds_titulo_fra\", \"$ds_leccion\", \"$ds_leccion_esp\", \"$ds_leccion_fra\", ";
    $Query .= "\"$ds_vl_ruta\", \"$ds_vl_duracion\", $fe_vl_alta, \"$fg_animacion\", \"$fg_ref_animacion\", $no_sketch, \"$fg_ref_sketch\", ";
    $Query .= "\"$ds_animacion\", \"$ds_animacion_esp\", \"$ds_animacion_fra\", \"$ds_ref_animacion\", \"$ds_ref_animacion_esp\", \"$ds_ref_animacion_fra\", ";
    $Query .= "\"$ds_no_sketch\", \"$ds_no_sketch_esp\", \"$ds_no_sketch_fra\", \"$ds_ref_sketch\", \"$ds_ref_sketch_esp\", \"$ds_ref_sketch_fra\", \"$ds_tiempo_tarea\", ";
    $Query .= "\"$nb_quiz\", \"$no_valor_quiz\", \"$ds_learning\", \"$ds_learning_esp\", \"$ds_learning_fra\", $no_val_rub) ";

    $fl_leccion_sp = EjecutaInsert($Query);

    EjecutaQuery("UPDATE k_criterio_programa_fame SET fl_programa_sp = $fl_leccion_sp WHERE fl_programa_sp = 0");

    #ICH: Insetamos pregunta 1 con sus respuestas
    if (!empty($valor_1)){
      $Query  = "INSERT INTO k_quiz_pregunta (fl_leccion_sp, fg_tipo, ds_pregunta, ds_pregunta_esp, ds_pregunta_fra, ds_valor_pregunta, ds_course_pregunta, fg_posicion_img, no_orden) ";
      $Query .= "VALUES ($fl_leccion_sp, '$fg_tipo_resp_1', '$ds_pregunta_1', '$ds_pregunta_esp_1', '$ds_pregunta_fra_1', $valor_1, $ds_course_1, '$fg_tipo_img_1', $no_orden_pregunta)";
      $fl_quiz_pregunta = EjecutaInsert($Query);

      #ICH: Respuestas tipo imagen
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

      #ICH: Son mas de dos preguntas
      if($no_max_tabs>=2){
        for($x=2; $x<=$no_max_tabs; $x++){
          #Inserta pregunta
          $Query  = "INSERT INTO k_quiz_pregunta (fl_leccion_sp, fg_tipo, ds_pregunta, ds_pregunta_esp, ds_pregunta_fra, ds_valor_pregunta, ds_course_pregunta, fg_posicion_img, no_orden) ";
          $Query .= "VALUES ($fl_leccion_sp, '$fg_tipo_resp_[$x]', '$ds_pregunta_[$x]', '$ds_pregunta_esp_[$x]', '$ds_pregunta_fra_[$x]', $valor_[$x], $ds_course_[$x], '$fg_tipo_img_[$x]', $x)";
          $fl_quiz_pregunta = EjecutaInsert($Query);
          #ICH: Respuesta tipo imagen
          if($fg_tipo_resp_[$x] == 'I'){
            EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_1_[$x] WHERE no_orden = 1 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_1_[$x]'");
            EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_2_[$x] WHERE no_orden = 2 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_2_[$x]'");
            EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_3_[$x] WHERE no_orden = 3 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_3_[$x]'");
            EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_4_[$x] WHERE no_orden = 4 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_4_[$x]'");
          }
          else{  #ICH: Respuesta tipo texto
            EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 1, '$ds_resp_1_[$x]', '$ds_resp_esp_1_[$x]', '$ds_resp_fra_1_[$x]', $ds_grade_1_[$x], $x)");
            EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 2, '$ds_resp_2_[$x]', '$ds_resp_esp_2_[$x]', '$ds_resp_fra_2_[$x]', $ds_grade_2_[$x], $x)");
            EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 3, '$ds_resp_3_[$x]', '$ds_resp_esp_3_[$x]', '$ds_resp_fra_3_[$x]', $ds_grade_3_[$x], $x)");
            EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 4, '$ds_resp_4_[$x]', '$ds_resp_esp_4_[$x]', '$ds_resp_fra_4_[$x]', $ds_grade_4_[$x], $x)");
            //echo "INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 4, '$ds_resp_4_[$x]', $ds_grade_4_[$x], $x)";
          }
        }
      }
    }
  }

    if(ExisteEnTabla('k_video_temp','fl_usuario', $fl_usuario)){
      # Cambiamos los nombres de las carpetas
      $ruta_str_1 = SP_HOME."/vanas_videos/fame/lessons/video_us".$fl_usuario;
      $ruta_str_2 = SP_HOME."/vanas_videos/fame/lessons/video_".$fl_leccion_sp;
      rename($ruta_str_1, $ruta_str_2);
      # SD
      $ruta_sd_1 = $ruta_str_2."/video_us".$fl_usuario."_sd";
      $ruta_sd_2 = $ruta_str_2."/video_".$fl_leccion_sp."_sd";
      rename($ruta_sd_1, $ruta_sd_2);
      # HD
      $ruta_hd_1 = $ruta_str_2."/video_us".$fl_usuario."_hd";
      $ruta_hd_2 = $ruta_str_2."/video_".$fl_leccion_sp."_hd";
      rename($ruta_hd_1, $ruta_hd_2);
      
      # Nombre del archivo m3u8
      $explosion =explode('.',$ds_vl_ruta);
      $file_name = array_shift($explosion);
      $ext = ObtenExtensionArchivo($ds_vl_ruta);
      $file_name_origen = $ruta_sd_2."/".$file_name."_sd.".$ext;
      $file_name_hls = $ruta_sd_2."/".$file_name."_sd.m3u8";
      $output = $ruta_sd_2."/output".$fl_leccion_sp."_sd.txt";
      $ruta_img=$ruta_sd_2;

      # Comando para convertir el video a m3u8
      #$comando_orig_to_hls_sd  = CMD_FFMPEG." -i $file_name_origen  -vf scale=-1:1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls ";
      $comando_orig_to_hls_sd  = CMD_FFMPEG." -i '$file_name_origen' -profile:v baseline -level 3.0 -vf scale=-1:1080 -start_number 0 -hls_time 10 -hls_list_size 0 -f hls $file_name_hls ";
      $comando_orig_to_hls_sd .= " 1>$output 2>&1 ";
      
      # Convertimos el MP4 to HLS m3u8
      $file_name_orig_hd = $ruta_hd_2."/".$file_name."_hd.".#ext;
      $file_name_hls_hd = $ruta_hd_2."/".$file_name."_hd.m3u8";
      #$comando_orig_to_hls_hd = CMD_FFMPEG." -i $file_name_orig_hd  -vf scale=-1:1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls_hd";
      $comando_orig_to_hls_hd = CMD_FFMPEG." -i '$file_name_orig_hd' -profile:v baseline -level 3.0 -vf scale=-1:1080 -start_number 0 -hls_time 10 -hls_list_size 0 -f hls $file_name_hls_hd";

      //Comando para generar la caratula del video.
      $name_img = $ruta_sd_2."/img_%d.png";
      $comando_image = CMD_FFMPEG." -i $file_name_origen -ss 00:00:01 -vframes 1 $name_img";
      exec($comando_image." >> /dev/null &");

	  #Comando para generarlos thumbails del video linea de tiempo.
	  $params=ObtenConfiguracion(135);
	  exec(CMD_FFMPEG." -i $file_name_origen $params $ruta_img/img%d.jpg >> /dev/null &");
	 
    }
  }
  else{ #ICH: Es update
    # Si hay algun video 
    if(ExisteEnTabla('k_video_temp','fl_usuario', $fl_usuario, 'fl_leccion_sp', $clave, true)){
      $row5 = RecuperaValor("SELECT nb_archivo FROM k_video_temp WHERE fl_usuario=$fl_usuario AND fl_leccion_sp=".$clave.";");
      $ds_vl_ruta = $row5["nb_archivo"];
    }

    if($fg_creado_instituto==1){
        
        $Query="UPDATE c_leccion_sp SET ds_vl_ruta='$ds_vl_ruta' WHERE fl_leccion_sp = $clave ";
        EjecutaQuery($Query);

    }else{

    # ICH: Actualizamos sus datos generales
    $Query  = "UPDATE c_leccion_sp SET fl_programa_sp=$fl_programa, no_grado=$no_grado, no_semana=$no_semana, ds_titulo=\"$ds_titulo\", ds_titulo_esp=\"$ds_titulo_esp\", ds_titulo_fra=\"$ds_titulo_fra\", ";
    $Query .= "ds_leccion=\"$ds_leccion\", ds_leccion_esp=\"$ds_leccion_esp\", ds_leccion_fra=\"$ds_leccion_fra\", ";
    $Query .= "ds_vl_ruta=\"$ds_vl_ruta\", ds_vl_duracion=\"$ds_vl_duracion\", fe_vl_alta=$fe_vl_alta, ";
    $Query .= "ds_tiempo_tarea=\"$ds_tiempo_tarea\", fg_animacion=\"$fg_animacion\", fg_ref_animacion=\"$fg_ref_animacion\", no_sketch=\"$no_sketch\", fg_ref_sketch=\"$fg_ref_sketch\", ";
    $Query .= "ds_animacion=\"$ds_animacion\", ds_animacion_esp=\"$ds_animacion_esp\", ds_animacion_fra=\"$ds_animacion_fra\", ";
    $Query .= "ds_ref_animacion=\"$ds_ref_animacion\", ds_ref_animacion_esp=\"$ds_ref_animacion_esp\", ds_ref_animacion_fra=\"$ds_ref_animacion_fra\", ";
    $Query .= "ds_no_sketch=\"$ds_no_sketch\", ds_no_sketch_esp=\"$ds_no_sketch_esp\", ds_no_sketch_fra=\"$ds_no_sketch_fra\", ds_ref_sketch=\"$ds_ref_sketch\", ";
    $Query .= "ds_ref_sketch_esp=\"$ds_ref_sketch_esp\", ds_ref_sketch_fra=\"$ds_ref_sketch_fra\", nb_quiz=\"$nb_quiz\", no_valor_quiz=$no_valor_quiz, ";
    $Query .= "ds_learning=\"$ds_learning\", ds_learning_esp=\"$ds_learning_esp\", ds_learning_fra=\"$ds_learning_fra\", no_valor_rubric=$no_val_rub ";
    $Query .= " 
	WHERE fl_leccion_sp=$clave ";
    EjecutaQuery($Query);
	
	$Query=" UPDATE c_leccion_sp SET ds_vl_ruta=\"$ds_vl_ruta\", ds_vl_duracion=\"$ds_vl_duracion\", fe_vl_alta=$fe_vl_alta WHERE fl_leccion_sp=$clave ";
	EjecutaQuery($Query);
    
    #ICH: Verificamos si existen preguntas    
    $rowe = RecuperaValor("SELECT COUNT(1) FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave");        

    if(empty($rowe[0])){ #ICH: Insertamos preguntas
      #ICH: Vemos si existe por lo menos una pregunta a insertar      
      if (!empty($valor_1)){
        #ICH: Insetamos pregunta 1 con sus respuestas
        $Query  = "INSERT INTO k_quiz_pregunta (fl_leccion_sp, fg_tipo, ds_pregunta, ds_pregunta_esp, ds_pregunta_fra, ds_valor_pregunta, ds_course_pregunta, fg_posicion_img, no_orden) ";
        $Query .= "VALUES ($clave, '$fg_tipo_resp_1', '$ds_pregunta_1', '$ds_pregunta_esp_1', '$ds_pregunta_fra_1', $valor_1, $ds_course_1, '$fg_tipo_img_1', $no_orden_pregunta)";
        $fl_quiz_pregunta = EjecutaInsert($Query);
        
        #ICH: Respuestas tipo imagen
        if($fg_tipo_resp_1 == 'I'){
          EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_1 WHERE no_orden = 1 AND no_tab = 1 AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_1_1'");
          EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_2 WHERE no_orden = 2 AND no_tab = 1 AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_2_1'");
          EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_3 WHERE no_orden = 3 AND no_tab = 1 AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_3_1'");
          EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_4 WHERE no_orden = 4 AND no_tab = 1 AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_4_1'");          
        } else { #ICH: Respuestas tipo texto
          EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 1, '$ds_resp_1', '$ds_resp_esp_1', '$ds_resp_fra_1', $ds_grade_1, 1)");
          EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 2, '$ds_resp_2', '$ds_resp_esp_2', '$ds_resp_fra_2', $ds_grade_2, 1)");
          EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 3, '$ds_resp_3', '$ds_resp_esp_3', '$ds_resp_fra_3', $ds_grade_3, 1)");
          EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 4, '$ds_resp_4', '$ds_resp_esp_4', '$ds_resp_fra_4', $ds_grade_4, 1)");
        }    
        
        #ICH: Son mas de dos preguntas
        if($no_max_tabs>=2){
          for($x=2; $x<=$no_max_tabs; $x++){
            #Inserta pregunta
            $Query  = "INSERT INTO k_quiz_pregunta (fl_leccion_sp, fg_tipo, ds_pregunta, ds_pregunta_esp, ds_pregunta_fra, ds_valor_pregunta, ds_course_pregunta, fg_posicion_img, no_orden) ";
            $Query .= "VALUES ($clave, '$fg_tipo_resp_[$x]', '$ds_pregunta_[$x]', '$ds_pregunta_esp_[$x]', '$ds_pregunta_fra_[$x]', $valor_[$x], $ds_course_[$x], '$fg_tipo_img_[$x]', $x)";
            $fl_quiz_pregunta = EjecutaInsert($Query);
            #ICH: Respuesta tipo imagen
            if($fg_tipo_resp_[$x] == 'I'){
              EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_1_[$x] WHERE no_orden = 1 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_1_[$x]' ");
              EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_2_[$x] WHERE no_orden = 2 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_2_[$x]' ");
              EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_3_[$x] WHERE no_orden = 3 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_3_[$x]' ");
              EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_4_[$x] WHERE no_orden = 4 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_4_[$x]' ");
            }else{  #ICH: Respuesta tipo texto
              EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 1, '$ds_resp_1_[$x]', '$ds_resp_esp_1_[$x]', '$ds_resp_fra_1_[$x]', $ds_grade_1_[$x], $x)");
              EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 2, '$ds_resp_2_[$x]', '$ds_resp_esp_2_[$x]', '$ds_resp_fra_2_[$x]', $ds_grade_2_[$x], $x)");
              EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 3, '$ds_resp_3_[$x]', '$ds_resp_esp_3_[$x]', '$ds_resp_fra_3_[$x]', $ds_grade_3_[$x], $x)");
              EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 4, '$ds_resp_4_[$x]', '$ds_resp_esp_4_[$x]', '$ds_resp_fra_4_[$x]', $ds_grade_4_[$x], $x)");
            }
          }    
        }
      }
    }
    else{  #ICH: Actualizamos preguntas
      $Query  = "UPDATE k_quiz_pregunta SET fg_tipo ='$fg_tipo_resp_1', ds_pregunta = '$ds_pregunta_1', ds_pregunta_esp = '$ds_pregunta_esp_1', ds_pregunta_fra = '$ds_pregunta_fra_1', ds_valor_pregunta = $valor_1, ";
      $Query .= "ds_course_pregunta = $ds_course_1, fg_posicion_img = '$fg_tipo_img_1' WHERE fl_leccion_sp = $clave AND no_orden = $no_orden_pregunta ";
      EjecutaQuery($Query);
      
      # Consultamos clave de pregunta y actualizamos sus respuestas, solo de la pregunta 1      
      $row = RecuperaValor("SELECT fl_quiz_pregunta FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave AND no_orden = 1 ");
      $fl_quiz_preg_1_1 = ($row[0]);
      if($fg_tipo_resp_1=="T"){

          #verifica si existe es apregunta y si no la inserta.
          $Query="SELECT COUNT(*) FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 1 ";
          $row=RecuperaValor($Query);
          if(empty($row[0])){
              EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab)VALUES ($fl_quiz_preg_1_1, 1, '$ds_resp_1', '$ds_resp_esp_1', '$ds_resp_fra_1', $ds_grade_1, 1)");      
          }
          #verifica si existe es apregunta y si no la inserta.
          $Query="SELECT COUNT(*) FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 2 ";
          $row=RecuperaValor($Query);
          if(empty($row[0])){
              EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab)VALUES ($fl_quiz_preg_1_1, 2, '$ds_resp_2', '$ds_resp_esp_2', '$ds_resp_fra_2', $ds_grade_2, 1)");      
          }
          #verifica si existe es apregunta y si no la inserta.
          $Query="SELECT COUNT(*) FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 3 ";
          $row=RecuperaValor($Query);
          if(empty($row[0])){
              EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab)VALUES ($fl_quiz_preg_1_1, 3, '$ds_resp_3', '$ds_resp_esp_3', '$ds_resp_fra_3', $ds_grade_3, 1)");      
          }
          #verifica si existe es apregunta y si no la inserta.
          $Query="SELECT COUNT(*) FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 4 ";
          $row=RecuperaValor($Query);
          if(empty($row[0])){
              EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab)VALUES ($fl_quiz_preg_1_1, 4, '$ds_resp_4', '$ds_resp_esp_4', '$ds_resp_fra_4', $ds_grade_4, 1)");      
          }

        EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_resp_1', ds_respuesta_esp = '$ds_resp_esp_1', ds_respuesta_fra = '$ds_resp_fra_1', ds_valor_respuesta = '$ds_grade_1' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 1");
        EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_resp_2', ds_respuesta_esp = '$ds_resp_esp_2', ds_respuesta_fra = '$ds_resp_fra_2', ds_valor_respuesta = '$ds_grade_2' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 2");
        EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_resp_3', ds_respuesta_esp = '$ds_resp_esp_3', ds_respuesta_fra = '$ds_resp_fra_3', ds_valor_respuesta = '$ds_grade_3' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 3");
        EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_resp_4', ds_respuesta_esp = '$ds_resp_esp_4', ds_respuesta_fra = '$ds_resp_fra_4', ds_valor_respuesta = '$ds_grade_4' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 4");
      }
      else{
        EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_img_1_1', ds_valor_respuesta = '$ds_grade_img_1' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 1 AND ds_respuesta='$ds_img_1_1'");
        EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_img_2_1', ds_valor_respuesta = '$ds_grade_img_2' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 2 AND ds_respuesta='$ds_img_2_1'");
        EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_img_3_1', ds_valor_respuesta = '$ds_grade_img_3' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 3 AND ds_respuesta='$ds_img_3_1'");
        EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta = '$ds_img_4_1', ds_valor_respuesta = '$ds_grade_img_4' WHERE fl_quiz_pregunta = $fl_quiz_preg_1_1 AND no_tab = 1 AND no_orden = 4 AND ds_respuesta='$ds_img_4_1'");
      }

      # Actualizamos todas las respuestas de todas las preguntas
      for($x=2; $x<=$no_max_tabs; $x++){       
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
          }
          else{
            EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta='$ds_resp_1_[$x]', ds_respuesta_esp='$ds_resp_esp_1_[$x]', ds_respuesta_fra='$ds_resp_fra_1_[$x]', ds_valor_respuesta = $ds_grade_1_[$x] WHERE no_orden = 1 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x]");
            EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta='$ds_resp_2_[$x]', ds_respuesta_esp='$ds_resp_esp_2_[$x]', ds_respuesta_fra='$ds_resp_fra_2_[$x]', ds_valor_respuesta = $ds_grade_2_[$x] WHERE no_orden = 2 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x]");
            EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta='$ds_resp_3_[$x]', ds_respuesta_esp='$ds_resp_esp_3_[$x]', ds_respuesta_fra='$ds_resp_fra_3_[$x]', ds_valor_respuesta = $ds_grade_3_[$x] WHERE no_orden = 3 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x]");
            EjecutaQuery("UPDATE k_quiz_respuesta SET ds_respuesta='$ds_resp_4_[$x]', ds_respuesta_esp='$ds_resp_esp_4_[$x]', ds_respuesta_fra='$ds_resp_fra_4_[$x]', ds_valor_respuesta = $ds_grade_4_[$x] WHERE no_orden = 4 AND no_tab = $x AND fl_quiz_pregunta = $fl_quiz_preg_1_[$x]");
          }
          
        }
        else{ // NO existe, entonces insertamos
          $Query  = "INSERT INTO k_quiz_pregunta (fl_leccion_sp, fg_tipo, ds_pregunta, ds_pregunta_esp, ds_pregunta_fra, ds_valor_pregunta, ds_course_pregunta, fg_posicion_img, no_orden) ";
          $Query .= "VALUES ($clave, '$fg_tipo_resp_[$x]', '$ds_pregunta_[$x]', '$ds_pregunta_esp_[$x]', '$ds_pregunta_fra_[$x]', $valor_[$x], $ds_course_[$x], '$fg_tipo_img_[$x]', $x)";
          $fl_quiz_pregunta = EjecutaInsert($Query);
          
          #ICH: Respuesta tipo imagen
          if($fg_tipo_resp_[$x] == 'I'){
            EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_1_[$x] WHERE no_orden = 1 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_1_[$x]'");
            EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_2_[$x] WHERE no_orden = 2 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_2_[$x]'");
            EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_3_[$x] WHERE no_orden = 3 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_3_[$x]'");
            EjecutaQuery("UPDATE k_quiz_respuesta SET fl_quiz_pregunta='$fl_quiz_pregunta', ds_valor_respuesta = $ds_grade_img_4_[$x] WHERE no_orden = 4 AND no_tab = $x AND fl_quiz_pregunta = 0 AND ds_respuesta='$ds_img_4_[$x]'");
          }else{  #ICH: Respuesta tipo texto
            EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 1, '$ds_resp_1_[$x]', '$ds_resp_esp_1_[$x]', '$ds_resp_fra_1_[$x]', $ds_grade_1_[$x], $x)");
            EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 2, '$ds_resp_2_[$x]', '$ds_resp_esp_2_[$x]', '$ds_resp_fra_2_[$x]', $ds_grade_2_[$x], $x)");
            EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 3, '$ds_resp_3_[$x]', '$ds_resp_esp_3_[$x]', '$ds_resp_fra_3_[$x]', $ds_grade_3_[$x], $x)");
            EjecutaQuery("INSERT INTO k_quiz_respuesta (fl_quiz_pregunta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab) VALUES ($fl_quiz_pregunta, 4, '$ds_resp_4_[$x]', '$ds_resp_esp_4_[$x]', '$ds_resp_fra_4_[$x]', $ds_grade_4_[$x], $x)");
          }
        }
      }
    }

  }
    # Elimina el registro temporal
    if(ExisteEnTabla('k_video_temp', 'fl_usuario', $fl_usuario, 'fl_leccion_sp', $clave, true) || $fg_reset_video==1){
      EjecutaQuery("DELETE FROM k_video_temp WHERE fl_usuario=$fl_usuario AND fl_leccion_sp=$clave");
      # Tambien eliminamos la carpeta que se creo de remplzao con el video anterior
      // mkdir(SP_HOME."/vanas_videos/fame/lessons/video_re".$clave);
      if($fg_reset_video==0){#
        eliminarDir(SP_HOME."/vanas_videos/fame/lessons/video_re".$clave);
        eliminarDir(SP_HOME."/vanas_videos/fame/lessons/video".$clave);
	  }
	  # Cambiamos los nombres de las carpetas
      $ruta_str_2 = SP_HOME."/vanas_videos/fame/lessons/video_".$clave;
      # SD
      $ruta_sd_2 = $ruta_str_2."/video_".$clave."_sd";
      # HD
      $ruta_hd_2 = $ruta_str_2."/video_".$clave."_hd";

      # Nombre del archivo m3u8
      $explosion =explode('.',$ds_vl_ruta);
      $file_name = array_shift($explosion);
      $ext = ObtenExtensionArchivo($ds_vl_ruta);
      $file_name_orig = $ruta_sd_2."/".$file_name."_sd.".$ext;
      $file_name_hls = $ruta_sd_2."/".$file_name."_sd.m3u8";
      $output = $ruta_sd_2."/output".$clave."_sd.txt";
      $ruta_img=$ruta_sd_2;

      # Comando para convertir el archivo a HLS m3u8
      #$comando_orig_to_hls_sd  = CMD_FFMPEG." -i $file_name_mp4  -vf scale=-1:1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls ";
      $comando_orig_to_hls_sd  = CMD_FFMPEG." -i '$file_name_orig' -profile:v baseline -level 3.0 -vf scale=-1:1080 -start_number 0 -hls_time 10 -hls_list_size 0 -f hls $file_name_hls ";
      $comando_orig_to_hls_sd .= " 1>$output 2>&1";
      # Convertimos el archivo a HLS m3u8
      $file_name_orig_hd = $ruta_hd_2."/".$file_name."_hd.".$ex;
      $file_name_hls_hd = $ruta_hd_2."/".$file_name."_hd.m3u8";
      #$comando_orig_to_hls_hd = CMD_FFMPEG." -i $file_name_orig_hd  -vf scale=-1:1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls_hd";
      $comando_orig_to_hls_hd = CMD_FFMPEG." -i '$file_name_orig_hd' -profile:v baseline -level 3.0 -vf scale=-1:1080 -start_number 0 -hls_time 10 -hls_list_size 0 -f hls $file_name_hls_hd";
      
      //Comando para generar la caratula del video.
      $name_img = $ruta_sd_2."/img_%d.png";
      $comando_image = CMD_FFMPEG." -i $file_name_orig -ss 00:00:01 -vframes 1 $name_img";
      exec($comando_image." >> /dev/null &");

	  #Comando para generarlos thumbails del video linea de tiempo.
	  $params=ObtenConfiguracion(135);
	  exec(CMD_FFMPEG." -i $file_name_orig $params $ruta_img/img%d.jpg");
	  
    }
  }

  # si coiparon algun video de otra leccion
  if(!empty($archivo_a)){
    $row = RecuperaValor("SELECT ds_vl_ruta FROM c_leccion_sp WHERE fl_leccion_sp=$archivo_a");
    $ds_vl_ruta_copy = $row[0];
    # actualizamos el nombre del archivo
    if(empty($clave)){
      EjecutaQuery("UPDATE c_leccion_sp SET ds_vl_ruta_copy='$ds_vl_ruta_copy', fl_leccion_copy=$archivo_a WHERE fl_leccion_sp=$fl_leccion_sp");      
    }
    else{
      $query = "UPDATE c_leccion_sp SET   ";
      if($clave==$archivo_a)
        $query .= "ds_vl_ruta_copy='', fl_leccion_copy=0 ";
      else        
        $query .= "ds_vl_ruta_copy='$ds_vl_ruta_copy', fl_leccion_copy=$archivo_a ";
      echo $query .= "WHERE fl_leccion_sp=$clave";
      EjecutaQuery($query);           
    }    
  }

  # Si recibe comando lo ejecutara
  # Recordando que para recibir un comando 
  if(!empty($comando_orig_to_hls_sd) && !empty($comando_orig_to_hls_hd)){
	  
    # Ejecutamos el comando background
      if($fg_creado_instituto==1){ //solo se ejecuta el comando sd es la que unicamente utilizamos.
          exec($comando_orig_to_hls_sd." >> /dev/null &");
      }else{
          exec($comando_orig_to_hls_sd." >> /dev/null &");
          //exec($comando_orig_to_hls_hd." >> /dev/null &");
      }
    # comando para obtener la imagen del video
    $name_img = $ruta_sd_2."/img_%d.png";
    //$origen_img =  $ruta_sd_2."/".$file_name."_sd.".$ext;
    $comando_image = CMD_FFMPEG." -i $file_name_orig -ss 00:00:01 -vframes 1 $name_img";
    exec($comando_image." >> /dev/null &");

	//echo"2____file:$origen_img   ruta_img: $ruta_img ";

    #Comando para generarlos thumbails del video linea de tiempo.
	$params=ObtenConfiguracion(135);
    exec(CMD_FFMPEG." -i $file_name_orig $params $ruta_img/img%d.jpg >> /dev/null &");
  }

  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
  function eliminarDir($carpeta){
    foreach(glob($carpeta . "/*") as $archivos_carpeta){
      // echo $archivos_carpeta;
      if (is_dir($archivos_carpeta)){
        eliminarDir($archivos_carpeta);
      }
      else{
        unlink($archivos_carpeta);
      }
    }
    rmdir($carpeta);
  }

?>
