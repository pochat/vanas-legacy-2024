<?php

# Libreria de funciones
require '../../lib/general.inc.php';

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion();

# Recibe parametros
$clave = RecibeParametroNumerico('clave');
$fg_error = RecibeParametroNumerico('fg_error');

# Variable initialization
$no_orden=NULL;
$no_max_tabs=NULL;
$disabled_det=NULL;
$editar=NULL;
$valor_ini_preg=NULL;
$fl_leccion_sp = $clave;

# Determina si es alta o modificacion
if (!empty($clave))
  $permiso = PERMISO_DETALLE;
else
  $permiso = PERMISO_ALTA;

# Verifica que el usuario tenga permiso de usar esta funcion
if (!ValidaPermiso(FUNC_LMED_SP, $permiso)) {
  MuestraPaginaError(ERR_SIN_PERMISO);
  exit;
}

# Variable initialization (Eventos para validacion de campos) to avoid errors
$val_camp_obl_1 = 'onblur="ValidaCamposObligatorios(\'no_grado\', this.value);"';
$val_camp_obl_2 = 'onblur="ValidaCamposObligatorios(\'no_semana\', this.value);"';
$val_camp_obl_3 = 'onblur="ValidaCamposObligatorios(\'ds_titulo\', this.value);"';
$val_camp_obl_4 = 'onblur="ValidaCamposObligatorios(\'ds_learning\', this.value);"';

# Inicializa variables
if (!$fg_error) { // Sin error, viene del listado
  if (!empty($clave)) { // Actualizacion, recupera de la base de datos
    $Query  = "SELECT fl_programa_sp, no_grado, no_semana, ds_titulo, ds_titulo_esp, ds_titulo_fra, ds_leccion, ds_leccion_esp, ds_leccion_fra, ds_vl_ruta, ds_vl_duracion, ";
    $concat = array(ConsultaFechaBD('fe_vl_alta', FMT_FECHA), "' '", ConsultaFechaBD('fe_vl_alta', FMT_HORAMIN));
    $Query .= "(" . ConcatenaBD($concat) . ") 'fe_vl_alta', ";
    $Query .= "fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch, ";
    $Query .= "ds_as_ruta, ds_as_duracion, ";
    $concat = array(ConsultaFechaBD('fe_as_alta', FMT_FECHA), "' '", ConsultaFechaBD('fe_as_alta', FMT_HORAMIN));
    $Query .= "(" . ConcatenaBD($concat) . ") 'fe_as_alta', ds_animacion, ds_animacion_esp, ds_animacion_fra, ds_ref_animacion, ds_ref_animacion_esp, ds_ref_animacion_fra, ";
    $Query .= "ds_no_sketch, ds_no_sketch_esp, ds_no_sketch_fra, ds_ref_sketch, ds_ref_sketch_esp, ds_ref_sketch_fra, ds_tiempo_tarea, nb_quiz, no_valor_quiz, ";
    $Query .= "ds_learning, ds_learning_esp, ds_learning_fra, no_valor_rubric, fl_leccion_copy, ds_vl_ruta_copy ";
    $Query .= "FROM c_leccion_sp WHERE fl_leccion_sp = $clave";

    $row = RecuperaValor($Query);

    $fl_programa = $row["fl_programa_sp"];
    $no_grado = $row["no_grado"];
    $no_semana = $row["no_semana"];
    $ds_titulo = str_texto($row["ds_titulo"]);
    $ds_titulo_esp = str_texto($row["ds_titulo_esp"]??NULL);
    $ds_titulo_fra = str_texto($row["ds_titulo_fra"]??NULL);
    $ds_leccion = str_texto($row["ds_leccion"]);
    $ds_leccion_esp = str_texto($row["ds_leccion_esp"]);
    $ds_leccion_fra = str_texto($row["ds_leccion_fra"]);
    $ds_vl_ruta = str_texto($row["ds_vl_ruta"]);
    $ds_vl_duracion = str_texto($row["ds_vl_duracion"]);
    $fe_vl_alta = str_texto($row["fe_vl_alta"]);
    $fg_animacion = $row["fg_animacion"];
    $fg_ref_animacion = $row["fg_ref_animacion"];
    $no_sketch = $row["no_sketch"];
    $fg_ref_sketch = $row["fg_ref_sketch"];
    $ds_as_ruta = str_texto($row["ds_as_ruta"]);
    $ds_as_duracion = str_texto($row["ds_as_duracion"]);
    $fe_as_alta = str_texto($row["fe_as_alta"]);
    $ds_animacion = $row["ds_animacion"];
    $ds_animacion_esp = $row["ds_animacion_esp"];
    $ds_animacion_fra = $row["ds_animacion_fra"];
    $ds_ref_animacion = $row["ds_ref_animacion"];
    $ds_ref_animacion_esp = $row["ds_ref_animacion_esp"];
    $ds_ref_animacion_fra = $row["ds_ref_animacion_fra"];
    $ds_no_sketch = $row["ds_no_sketch"];
    $ds_no_sketch_esp = $row["ds_no_sketch_esp"];
    $ds_no_sketch_fra = $row["ds_no_sketch_fra"];
    $ds_ref_sketch = $row["ds_ref_sketch"];
    $ds_ref_sketch_esp = $row["ds_ref_sketch_esp"];
    $ds_ref_sketch_fra = $row["ds_ref_sketch_fra"];
    $ds_tiempo_tarea = $row["ds_tiempo_tarea"];
    $nb_quiz = str_texto($row["nb_quiz"]);
    $no_valor_quiz = ($row["no_valor_quiz"]);
    $ds_learning = str_texto($row["ds_learning"]);
    $ds_learning_esp = str_texto($row["ds_learning_esp"]??NULL);
    $ds_learning_fra = str_texto($row["ds_learning_fra"]??NULL);
    $no_val_rub = $row["no_valor_rubric"];
    $archivo_a = $row["fl_leccion_copy"];
    $ds_vl_ruta_copy = $row["ds_vl_ruta_copy"];
    $row = RecuperaValor("SELECT (100 - SUM(ds_valor_pregunta)) FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave ");
    $valor_ini_preg = !empty($row["fl_programa_sp"])?$row["fl_programa_sp"]:NULL;
    if (empty($valor_ini_preg))
      $valor_ini_preg = 100;
    // $valor_ini_preg = 1;
    $style_sin_criterios = "style='display:none;'";
    $style_sin_valor_rubric = "style='display:none;'";
    $style_sin_valor_criterio = "style='display:none;'";
    $style_max_grade = "style='display:none;'";
    $style_max_grade_wrg = "style='display:none;'";

    $Query  = "SELECT fl_leccion_sp, fg_tipo, ds_pregunta, ds_pregunta_esp, ds_pregunta_fra, ds_valor_pregunta, fg_posicion_img, ds_course_pregunta, no_orden, fl_quiz_pregunta ";
    $Query .= "FROM k_quiz_pregunta ";
    $Query .= "WHERE fl_leccion_sp = $clave AND no_orden = 1";
    $row = RecuperaValor($Query);
    $rsp = EjecutaQuery($Query);
    $tot_preguntas = CuentaRegistros($rsp);
    if (empty($tot_preguntas)) {
      $editar = False;
      $fl_leccion_sp = 0;
      $fg_tipo_resp_1 = "T";
      $ds_pregunta_1 = "";
      $ds_pregunta_esp_1 = "";
      $ds_pregunta_fra_1 = "";
      $ds_quiz_1 = 0;
      $fg_tipo_img_1 = "L";
      $ds_course_1 = "";
      $no_orden = 0;
      $fl_quiz_pregunta = 0;
      $ds_resp_1 = "";
      $ds_resp_esp_1 = "";
      $ds_resp_fra_1 = "";
      $ds_grade_1 = "";
      $ds_resp_2 = "";
      $ds_resp_esp_2 = "";
      $ds_resp_fra_2 = "";
      $ds_grade_2 = "";
      $ds_resp_3 = "";
      $ds_resp_esp_3 = "";
      $ds_resp_fra_3 = "";
      $ds_grade_3 = "";
      $ds_resp_4 = "";
      $ds_resp_esp_4 = "";
      $ds_resp_fra_4 = "";
      $ds_grade_4 = "";
      $ds_resp_5 = "";
      $ds_resp_esp_5 = "";
      $ds_resp_fra_5 = "";
      $ds_grade_5 = "";
    } else {
      $editar = True;
      $fl_leccion_sp = $row["fl_leccion_sp"];
      $fg_tipo_resp_1 = str_texto($row["fg_tipo"]);
      if (empty($fg_tipo_resp_1))
        $fg_tipo_resp_1 = "T";
      $ds_pregunta_1 = $row["ds_pregunta"];
      $ds_pregunta_esp_1 = $row["ds_pregunta_esp"];
      $ds_pregunta_fra_1 = $row["ds_pregunta_fra"];
      $ds_quiz_1 = $row["ds_valor_pregunta"];
      if (empty($ds_quiz_1))
        $ds_quiz_1 = 0;
      $fg_tipo_img_1 = str_texto($row["fg_posicion_img"]);
      $ds_course_1 = $row["ds_course_pregunta"];
      $no_orden = $row["no_orden"];
      $fl_quiz_pregunta = $row["fl_quiz_pregunta"];

      $row1 = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_valor_respuesta, no_tab, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta AND no_tab = 1 AND no_orden = 1 ");
      $ds_resp_1 = str_texto($row1["ds_respuesta"]);
      $ds_resp_esp_1 = str_texto($row1["ds_respuesta_esp"]);
      $ds_resp_fra_1 = str_texto($row1["ds_respuesta_fra"]);
      $ds_grade_1 = $row1["ds_valor_respuesta"];
      $row2 = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_valor_respuesta, no_tab, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta AND no_tab = 1 AND no_orden = 2 ");
      $ds_resp_2 = str_texto($row2["ds_respuesta"]);
      $ds_resp_esp_2 = str_texto($row2["ds_respuesta_esp"]);
      $ds_resp_fra_2 = str_texto($row2["ds_respuesta_fra"]);
      $ds_grade_2 = $row2["ds_valor_respuesta"];
      $row3 = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_valor_respuesta, no_tab, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta AND no_tab = 1 AND no_orden = 3 ");
      $ds_resp_3 = str_texto($row3["ds_respuesta"]);
      $ds_resp_esp_3 = str_texto($row3["ds_respuesta_esp"]);
      $ds_resp_fra_3 = str_texto($row3["ds_respuesta_fra"]);
      $ds_grade_3 = $row3["ds_valor_respuesta"];
      $row4 = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_valor_respuesta, no_tab, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta AND no_tab = 1 AND no_orden = 4 ");
      $ds_resp_4 = str_texto($row4["ds_respuesta"]);
      $ds_resp_esp_4 = str_texto($row4["ds_respuesta_esp"]);
      $ds_resp_fra_4 = str_texto($row4["ds_respuesta_fra"]);
      $ds_grade_4 = $row4["ds_valor_respuesta"];


      # Buscamos qie por lo menos en los valores de loas respuestas haya un 100
      if ($fg_tipo_resp_1 == "T") {
        $nb_img_prev_mydropzone_1_1 = "";
        $ds_grade_img_1 = "";
        $ds_grade_img_2 = "";
        $ds_grade_img_3 = "";
        $ds_grade_img_4 = "";
        $pesos = array_search(100, array(1 => $ds_grade_1, 2 => $ds_grade_2, 3 => $ds_grade_3, 4 => $ds_grade_4));
      } else {
        $ds_resp_1 = "";
        $ds_resp_esp_1 = "";
        $ds_resp_fra_1 = "";
        $ds_grade_1 = "";
        $ds_resp_2 = "";
        $ds_resp_esp_2 = "";
        $ds_resp_fra_2 = "";
        $ds_grade_2 = "";
        $ds_resp_3 = "";
        $ds_resp_esp_3 = "";
        $ds_resp_fra_3 = "";
        $ds_grade_3 = "";
        $ds_resp_4 = "";
        $ds_resp_esp_4 = "";
        $ds_resp_fra_4 = "";
        $ds_grade_4 = "";
        $ds_grade_img_1 = $row1[3];
        $ds_grade_img_2 = $row2[3];
        $ds_grade_img_3 = $row3[3];
        $ds_grade_img_4 = $row4[3];
        $pesos = array_search(100, array(1 => $ds_grade_img_1, 2 => $ds_grade_img_2, 3 => $ds_grade_img_3, 4 => $ds_grade_img_4));
      }
    }

    $disabled_no_val_rub = "";
    $disabled_det = "";
  } else { // Alta, inicializa campos
    $fl_programa = "";
    $no_grado = "";
    $no_semana = "";
    $ds_titulo = "";
    $ds_learning = "";
    $ds_leccion = "";
    # Si si esta dando de alta 
    if (ExisteEnTabla('k_video_temp', 'fl_usuario', $fl_usuario)) {
      $row5 = RecuperaValor("SELECT nb_archivo FROM k_video_temp WHERE fl_usuario=$fl_usuario");
      $ds_vl_ruta = $row5[0];
    } else
      $ds_vl_ruta = "";
    $ds_vl_duracion = "";
    $fe_vl_alta = "";
    $fg_animacion = "0";
    $fg_ref_animacion = "0";
    $no_sketch = "0";
    $fg_ref_sketch = "0";
    $ds_as_ruta = "";
    $ds_as_duracion = "";
    $fe_as_alta = "";
    $fg_tipo_resp_1 = "T";
    $fg_tipo_img_1  = "L";
    $fl_quiz_pregunta = 0;
    // Campo para Dropzone
    $editar = False;
    $ds_tiempo_tarea = "";
    $nb_quiz = "";
    $no_valor_quiz = "";
    $valor_ini_preg = 0;
    $ds_quiz_1 = 0;
    $valor_ini_preg = 100;
    $valor_inicial = 100;
    $no_val_rub = 0;
    $no_ter_co = 100;
    $style_sin_criterios = "style='display:none;'";
    $style_sin_valor_rubric = "style='display:none;'";
    $style_sin_valor_criterio = "style='display:none;'";
    $style_max_grade = "style='display:none;'";
    $style_max_grade_wrg = "style='display:none;'";

    $disabled_no_val_rub = "disabled = 'disabled'";

    # Eventos para validacion de campos
    $val_camp_obl_1 = 'onblur="ValidaCamposObligatorios(\'no_grado\', this.value);"';
    $val_camp_obl_2 = 'onblur="ValidaCamposObligatorios(\'no_semana\', this.value);"';
    $val_camp_obl_3 = 'onblur="ValidaCamposObligatorios(\'ds_titulo\', this.value);"';
    $val_camp_obl_4 = 'onblur="ValidaCamposObligatorios(\'ds_learning\', this.value);"';

    $disabled_det = "disabled = 'disabled'";
  }
  $fl_programa_err = "";
  $no_grado_err = "";
  $no_semana_err = "";
  $ds_titulo_err = "";
  $ds_learning_err = "";
  $ds_leccion_err = "";
  $no_sketch_err = "";
  $tab_description_err = "";
  $tab_quiz_err = "";
  $err_sum_val_preg_max = 0;
  $err_sum_val_preg_min = 0;
  $err_valor_repuestas_err = 0;
} else { // Con error, recibe parametros (viene de la pagina de actualizacion)
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $fl_programa_err = RecibeParametroNumerico('fl_programa_err');
  $no_grado = RecibeParametroNumerico('no_grado');
  $no_grado_err = RecibeParametroNumerico('no_grado_err');
  $no_semana = RecibeParametroNumerico('no_semana');
  $no_semana_err = RecibeParametroNumerico('no_semana_err');
  $ds_titulo = RecibeParametroHTML('ds_titulo');
  $ds_titulo_esp = RecibeParametroHTML('ds_titulo_esp')??NULL;
  $ds_titulo_fra = RecibeParametroHTML('ds_titulo_fra');
  $ds_titulo_err = RecibeParametroNumerico('ds_titulo_err');
  $ds_learning = RecibeParametroHTML('ds_learning');
  $ds_learning_esp = RecibeParametroHTML('ds_learning_esp');
  $ds_learning_fra = RecibeParametroHTML('ds_learning_fra');
  $ds_learning_err = RecibeParametroNumerico('ds_learning_err');
  $ds_leccion = RecibeParametroHTML('ds_leccion');
  $ds_leccion_esp = RecibeParametroHTML('ds_leccion_esp');
  $ds_leccion_fra = RecibeParametroHTML('ds_leccion_fra');
  $ds_leccion_err = RecibeParametroNumerico('ds_leccion_err');
  $ds_vl_ruta = RecibeParametroHTML('ds_vl_ruta');
  $ds_vl_duracion = RecibeParametroHTML('ds_vl_duracion');
  $fe_vl_alta = RecibeParametroHTML('fe_vl_alta');
  $ds_as_ruta = RecibeParametroHTML('ds_as_ruta');
  $ds_as_duracion = RecibeParametroHTML('ds_as_duracion');
  $fe_as_alta = RecibeParametroHTML('fe_as_alta');

  $ds_tiempo_tarea = RecibeParametroHTML('ds_tiempo_tarea');
  $fg_animacion = RecibeParametroBinario('fg_animacion');
  $ds_animacion = RecibeParametroHTML('ds_animacion');
  $ds_animacion_esp = RecibeParametroHTML('ds_animacion_esp');
  $ds_animacion_fra = RecibeParametroHTML('ds_animacion_fra');
  $ds_animacion_err = RecibeParametroNumerico('ds_animacion_err');
  $fg_ref_animacion = RecibeParametroBinario('fg_ref_animacion');
  $ds_ref_animacion = RecibeParametroHTML('ds_ref_animacion');
  $ds_ref_animacion_esp = RecibeParametroHTML('ds_ref_animacion_esp');
  $ds_ref_animacion_fra = RecibeParametroHTML('ds_ref_animacion_fra');
  $ds_ref_animacion_err = RecibeParametroNumerico('ds_ref_animacion_err');
  $no_sketch = RecibeParametroNumerico('no_sketch');
  $ds_no_sketch = RecibeParametroHTML('ds_no_sketch');
  $ds_no_sketch_esp = RecibeParametroHTML('ds_no_sketch_esp');
  $ds_no_sketch_fra = RecibeParametroHTML('ds_no_sketch_fra');
  $ds_no_sketch_err = RecibeParametroNumerico('ds_no_sketch_err');
  $no_sketch_err = RecibeParametroNumerico('no_sketch_err');
  $fg_ref_sketch = RecibeParametroBinario('fg_ref_sketch');
  $ds_ref_sketch = RecibeParametroHTML('ds_ref_sketch');
  $ds_ref_sketch_err = RecibeParametroNumerico('ds_ref_sketch_err');

  # Quiz
  $nb_quiz = RecibeParametroHTML('nb_quiz');
  $no_valor_quiz = RecibeParametroNumerico('no_valor_quiz');
  $ds_course_1 = RecibeParametroNumerico('ds_course_1');
  $c_remaining = RecibeParametroNumerico('c_remaining');
  $fg_tipo_resp_1 = RecibeParametroHTML('fg_tipo_resp_1');
  $fg_tipo_img_1 = RecibeParametroHTML('fg_tipo_img_1');
  $ds_pregunta_1 = RecibeParametroHTML('ds_pregunta_1');
  $ds_pregunta_esp_1 = RecibeParametroHTML('ds_pregunta_esp_1');
  $ds_pregunta_fra_1 = RecibeParametroHTML('ds_pregunta_fra_1');
  $valor_1 = RecibeParametroNumerico('valor_1'); // valor pregunta1
  $ds_quiz_1 = RecibeParametroNumerico('ds_quiz_1'); // valor pregunta1    
  $q_remaining_1 = RecibeParametroNumerico('q_remaining_1');
  if ($fg_tipo_resp_1 == "T") {
    $ds_resp_1 = RecibeParametroHTML('ds_resp_1');
    $ds_resp_esp_1 = RecibeParametroHTML('ds_resp_esp_1');
    $ds_resp_fra_1 = RecibeParametroHTML('ds_resp_fra_1');
    $ds_grade_1 = RecibeParametroNumerico('ds_grade_1');
    $ds_resp_2 = RecibeParametroHTML('ds_resp_2');
    $ds_resp_esp_2 = RecibeParametroHTML('ds_resp_esp_2');
    $ds_resp_fra_2 = RecibeParametroHTML('ds_resp_fra_2');
    $ds_grade_2 = RecibeParametroNumerico('ds_grade_2');
    $ds_resp_3 = RecibeParametroHTML('ds_resp_3');
    $ds_resp_esp_3 = RecibeParametroHTML('ds_resp_esp_3');
    $ds_resp_fra_3 = RecibeParametroHTML('ds_resp_fra_3');
    $ds_grade_3 = RecibeParametroNumerico('ds_grade_3');
    $ds_resp_4 = RecibeParametroHTML('ds_resp_4');
    $ds_resp_esp_4 = RecibeParametroHTML('ds_resp_esp_4');
    $ds_resp_fra_4 = RecibeParametroHTML('ds_resp_fra_4');
    $ds_grade_4 = RecibeParametroNumerico('ds_grade_4');
  } else {
    # Nombre y peso de la respuesta uno de tipo imagen
    $nb_img_prev_mydropzone_1_1  = RecibeParametroHTML("nb_img_prev_mydropzone_1_1");
    $ds_grade_img_1 = RecibeParametroNumerico("ds_grade_img_1");
    $nb_img_prev_mydropzone_2_1  = RecibeParametroHTML("nb_img_prev_mydropzone_2_1");
    $ds_grade_img_2 = RecibeParametroNumerico("ds_grade_img_2");
    $nb_img_prev_mydropzone_3_1  = RecibeParametroHTML("nb_img_prev_mydropzone_3_1");
    $ds_grade_img_3 = RecibeParametroNumerico("ds_grade_img_3");
    $nb_img_prev_mydropzone_4_1  = RecibeParametroHTML("nb_img_prev_mydropzone_4_1");
    $ds_grade_img_4 = RecibeParametroNumerico("ds_grade_img_4");
  }

  # Contador de tabs de preguntas
  $no_max_tabs = RecibeParametroNumerico('no_max_tabs')??NULL;

  # Recibo valores de tabs extras de preguntas
  for ($x = 2; $x <= $no_max_tabs; $x++) {
    $fg_tipo_resp_[$x] = RecibeParametroHTML("fg_tipo_resp_$x");
    $fg_tipo_img_[$x]  = RecibeParametroHTML("fg_tipo_img_$x");
    $ds_pregunta_[$x]  = RecibeParametroHTML("ds_pregunta_$x");
    $ds_pregunta_esp_[$x]  = RecibeParametroHTML("ds_pregunta_esp_$x");
    $ds_pregunta_fra_[$x]  = RecibeParametroHTML("ds_pregunta_fra_$x");
    $ds_quiz_[$x]      = RecibeParametroNumerico("ds_quiz_$x");
    $ds_course_[$x]    = RecibeParametroNumerico("ds_course_$x");
    $valor_[$x]        = RecibeParametroNumerico("valor_$x");
    if (empty($ds_quiz_[$x]))
      $ds_quiz_[$x] = $valor_[$x];
    $q_remaining_[$x]  = RecibeParametroNumerico("q_remaining_$x");
    $no_orden_pregunta_[$x] = $x;

    if ($fg_tipo_resp_[$x] == "T") {
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

  # Rubric
  $no_ter_co = RecibeParametroNumerico('no_ter_co');
  $no_val_rub = RecibeParametroNumerico('no_val_rub');
  $no_val_rub_err = RecibeParametroNumerico('no_val_rub_err');
  $tab_description_err = RecibeParametroNumerico('tab_description_err');
  $tab_quiz_err = RecibeParametroNumerico('tab_quiz_err');
  $tab_rubric_err = RecibeParametroNumerico('tab_rubric_err');
  if ($tab_quiz_err)
    $style_tab_quiz = "style='color:#b94a48;'";
  else
    $style_tab_quiz = "style='color:#333;'";

  if ($tab_description_err)
    $style_tab_desc = "style='color:#b94a48;'";
  else
    $style_tab_desc = "style='color:#333;'";

  if ($tab_rubric_err)
    $style_tab_rub = "style='color:#b94a48;'";
  else
    $style_tab_rub = "style='color:#333;'";

  if ($no_val_rub_err == 0) {
    $style_sin_criterios = "style='display:none;'";
    $style_sin_valor_rubric = "style='display:none;'";
  } else {
    if ($no_val_rub_err == 1)
      $style_sin_criterios = "style='display:block;'";
    else
      $style_sin_criterios = "style='display:none;'";

    if ($no_val_rub_err == 2)
      $style_sin_valor_rubric = "style='display:block;'";
    else
      $style_sin_valor_rubric = "style='display:none;'";

    if ($no_val_rub_err == 3)
      $style_sin_valor_criterio = "style='display:block;'";
    else
      $style_sin_valor_criterio = "style='display:none;'";
  }
}

# Presenta forma de captura
PresentaHeader();
PresentaEncabezado(FUNC_LMED_SP);

//$ds_leccion = str_replace("?", "", $ds_leccion);

# Ventana para preview
require 'preview.inc.php';

# Funciones javascript
echo "<script type='text/javascript'>
    function fuente_archivo(arch){
      if(document.datos[arch+'_a'].value == 0){
        document.datos[arch].disabled = false;
      }
      else{
        document.datos[arch].disabled = true;
        document.datos[arch].value = '';
      }
    }
  </script>";

# Eliminamos registros de este usuario si existe algun video temporal
if (ExisteEnTabla('k_video_temp', 'fl_usuario', $fl_usuario)) {
  EjecutaQuery("DELETE FROM k_video_temp WHERE fl_usuario=$fl_usuario");
}

# Inicia forma de captura
Forma_Inicia($clave, True);
if ($fg_error)
  Forma_PresentaError();

if (!empty($clave)) {
  if (empty($fg_error)) {
    $row = RecuperaValor("SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp");
    $no_preguntas = $row[0];
  } else {
      $no_preguntas = $no_max_tabs??NULL;
  }
} else {
    $no_preguntas = $no_max_tabs??NULL;
}

if (empty($no_preguntas))
  $no_preguntas = 1;
$ContTabCounter = ($no_preguntas + 1);
Forma_CampoOculto("ContTabCounter", $ContTabCounter);
Forma_CampoOculto("ContTabCounterLimit", $ContTabCounter);
Forma_CampoOculto("NoPreguntas", $no_preguntas);
Forma_CampoOculto("NoPreguntas_temporal", $no_preguntas);

# Popover donde muestra mensaje en cada campo 
$warning = "warning";
function popover($accion = '',  $posicion = '', $title = '', $content = '')
{

  if (empty($accion))
    $accion = "popover";

  if (empty($posicion))
    $posicion = "top";

  $popover = "rel='" . $accion . "' data-placement='" . $posicion . "' ";

  if (!empty($title))
    $popover .= "data-original-title='" . $title . "' ";

  if (!empty($content))
    $popover .= "data-content='" . $content . "' ";

  return $popover;
}

?>
<!-- Se muestra cuando esta guardando --->
<div class='modal fade text-align-center' id='upload_videos' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
  <div class="row" style="position:relative; top:40%;">
    <div class="col col-lg-4 col-sm-12 col-md-12 col-xs-12">&nbsp;</div>
    <div class="col col-lg-4 col-sm-12 col-md-12 col-xs-12">
      <i class="fa fa-cog fa-3x fa-spin txt-color-white"></i>
      <h2><strong class="txt-color-white"> Loading....</strong></h2>
      <div class="progress">
        <div id="progress_leccion" class="progress-bar bg-color-teal" aria-valuetransitiongoal="0" style="width: 0%; background-color:#0092cd !important;" aria-valuenow="0">
          0%
        </div>
      </div>
    </div>
    <div class="col col-lg-4 col-sm-12 col-md-12 col-xs-12">&nbsp;</div>
  </div>
</div>

<!-- UMP: Se utiliza en seccion quiz --->
<div class='modal fade text-align-center' id='muestra_loading_quiz' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
  <span id="span_upload" class="ui-widget ui-chatbox txt-color-white">
    <i class="fa fa-cog fa-4x fa-spin txt-color-white"></i>
    <h2><strong> Loading....</strong></h2>
  </span>
</div>
<!-- Muestra error si el rubric tiene valor pero no hay criterios -->
<div id="style_sin_valor_criterio" <?php echo ($style_sin_valor_criterio??NULL); ?>>
  <div class="row">
    <!-- <div class="col-xs-1 col-sm-1"></div>-->
    <div class="col-xs-12 col-sm-12">
      <div class="alert alert-danger fade in">
        <i class="fa-fw fa fa-times"></i>
        <strong><?php echo ObtenEtiqueta(1345); ?> </strong>
      </div>
    </div>
    <!-- <div class="col-xs-1 col-sm-1"></div>-->
  </div>
</div>
<div class="widget-body">
  <!-- UMP: Titulos de Tabs -->
  <ul id="myTab1" class="nav nav-tabs bordered">
    <li class="active">
      <a id="tab_1" href="#description" data-toggle="tab">
        <span <?php echo (!empty($style_tab_desc)?$style_tab_desc:NULL); ?>><i class="fa fa-fw fa-lg fa-info-circle"></i><?php echo " " . ObtenEtiqueta(19) ?></span>
      </a>
    </li>
    <li>
      <a id="tab_2" href="#video" data-toggle="tab"><i class="fa fa-fw fa-lg fa-video-camera"></i><?php echo " " . ObtenEtiqueta(457) ?></a>
    </li>
    <li>
      <a id="tab_3" href="#assignment" data-toggle="tab">
        <span <?php
              if (!empty($ds_animacion_err) || !empty($ds_ref_animacion_err) || !empty($ds_no_sketch_err) || !empty($ds_ref_sketch_err))
                echo "style='color:#b94a48;'";
              ?>><i class="fa fa-fw fa-lg fa-pencil"></i><?php echo " " . ObtenEtiqueta(393) ?></span>
      </a>
    </li>
    <li>
      <a id="tab_4" href="#quiz" data-toggle="tab">
        <span <?php echo (!empty($style_tab_quiz)?$style_tab_quiz:NULL); ?>><i class="fa fa-fw fa-lg fa-question-circle"></i><?php echo 'Quiz' ?></span>
      </a>
    </li>
    <li>
      <a id="tab_5" href="#rubric" data-toggle="tab">
        <span <?php echo (!empty($style_tab_rub)?$style_tab_rub:NULL); ?>><i class="fa fa-fw fa-lg fa-table"></i><?php echo 'Rubric' ?></span>
      </a>
    </li>
  </ul>
  <!-- UMP: Tabs Content -->
  <div id="myTabContent1" class="tab-content padding-10 no-border">
    <!-- UMP: First tab = descripcion -->
    <?php require "lmedia_frm_description_tab.php"; ?>
    <!-- UMP: Second tab = video -->
    <?php require "lmedia_frm_video_tab.php"; ?>
    <!-- UMP: Third tab = Assignment -->
    <?php require "lmedia_frm_assignment_tab.php"; ?>
    <!-- UMP: Fourth tab = Quiz -->
    <?php require "lmedia_frm_quiz_tab.php"; ?>
    <!-- UMP: Fifht tab = rubric -->
    <?php require "lmedia_frm_rubric_tab.php"; ?>
  </div>
</div>
<?php
if ($fg_error) {
  $no_max_tabs = RecibeParametroNumerico('no_max_tabs');
  for ($x = 2; $x <= $no_max_tabs; $x++) {
    $valor_preg_[$x] = RecibeParametroNumerico("valor_$x");
    $tab_num_err_[$x]  = RecibeParametroNumerico("tab_num_err_$x");
    if ($tab_num_err_[$x]) {
      echo "<script>
          document.getElementById('muestra_valor_$x').style.color = '#b94a48';
          document.getElementById('muestra_valor_$x').style.fontWeight = '900';
          </script>";
    }
  }
}
# Validaciones para el Quiz
?>
<script>
  // Identificamos si existen algun campo modificado que active el Valida_Quiz
  var tquiz = '<?php echo $nb_quiz; ?>';
  var vquiz = '<?php echo $no_valor_quiz; ?>';
  var preg1 = '<?php echo ($ds_pregunta_1??NULL); ?>';
  var vpreg1 = '<?php echo ($ds_course_1??NULL); ?>';
  var resp1 = '<?php echo ($ds_resp_1??NULL); ?>';
  var vresp1 = '<?php echo ($ds_grade_1??NULL); ?>';
  var resp2 = '<?php echo ($ds_resp_2??NULL); ?>';
  var vresp2 = '<?php echo ($ds_grade_2??NULL); ?>';
  var resp3 = '<?php echo ($ds_resp_3??NULL); ?>';
  var vresp3 = '<?php echo ($ds_grade_3??NULL); ?>';
  var resp4 = '<?php echo ($ds_resp_4??NULL); ?>';
  var vresp4 = '<?php echo ($ds_grade_4??NULL); ?>';
  var pesos = '<?php echo ($pesos??0); ?>';
  if (pesos == '')
    pesos = 0;
  if (tquiz != '' || vquiz > 0 || preg1 != '' || vpreg1 > 0 || resp1 != '' || resp2 != '' || resp3 != '' || resp4 != '' || pesos > 0) {
    $(document).ready(function() {
      Valida_Quiz();
    });
  }

  function Valida_Quiz() {
    // Activamos el tab
    $("#tab_4").addClass("txt-color-red");
    // Boton
    var btn_save = $('footer > div > div > a:first');
    var remaining = $("#ds_course_1").val();
    // titulo y valor del quiz
    var tquiz = $("#nb_quiz").val();
    var vquiz = $("#no_valor_quiz").val();
    // Tipo de pregunta
    var type_respuesta;
    $('#fg_tipo_resp_1:checked').each(
      function() {
        type_respuesta = $(this).val();
      }
    );
    // pregunta y valor
    var preg1 = $("#ds_pregunta_1").val();
    var vpreg1 = $("#valor_1").val();
    var rem_pre1 = $("#ds_quiz_1").val();
    // respuestas dependiento del tipo de la respuesta
    if (type_respuesta == 'T') {
      var resp1 = $("#ds_resp_1").val();
      var vresp1 = $("#ds_grade_1").val();
      var resp2 = $("#ds_resp_2").val();
      var vresp2 = $("#ds_grade_2").val();
      var resp3 = $("#ds_resp_3").val();
      var vresp3 = $("#ds_grade_3").val();
      var resp4 = $("#ds_resp_4").val();
      var vresp4 = $("#ds_grade_4").val();
    } else {
      var resp1 = $("#nb_img_prev_mydropzone_1").val();
      var vresp1 = $("#ds_grade_img_1").val();
      var resp2 = $("#nb_img_prev_mydropzone_2").val();
      var vresp2 = $("#ds_grade_img_2").val();
      var resp3 = $("#nb_img_prev_mydropzone_3").val();
      var vresp3 = $("#ds_grade_img_3").val();
      var resp4 = $("#nb_img_prev_mydropzone_4").val();
      var vresp4 = $("#ds_grade_img_4").val();
    }
    // En los pesos de las preguntas por lo menos debe exitir un 100
    // creamos un array con los valores
    var myArr = [vresp1, vresp2, vresp3, vresp4];
    // con este idicamos que hay porlo menos un 100 en los campos
    var pesos = myArr.includes('100');
    // alert(pesos);
    // Si el remaining del quiz no debera ser menos a cer    
    if (remaining < 0) {
      btn_save.addClass('disabled');
    } else {
      btn_save.removeClass('disabled');
    }

    // Si el remaining la pregunta1 no debera ser menos a cer
    if (rem_pre1 < 0)
      btn_save.addClass('disabled');
    else
      btn_save.removeClass('disabled');

    // Buscamos los valores de las preguntas si todas son 100 esta bien en caso contrario esta mal
    var valores_preguntas = $("#NoPreguntas_temporal").val(),
      tot_vals_pregunts = 0;
    if (valores_preguntas == 0)
      valores_preguntas = 1;
    for (var p = 1; p <= valores_preguntas; p++) {
      // Obtenemos los valores de las preguntas
      var vls_pre = $("#valor_" + p).val();
      if (($("#valor_" + p).length) == 0)
        vls_pre = 0;
      tot_vals_pregunts = parseFloat(tot_vals_pregunts) + parseFloat(vls_pre);
    }

    for (var q = 1; q <= valores_preguntas; q++) {
      if (tot_vals_pregunts == 100 || vpreg1 == 100) {
        if (q == 1)
          $('#div_no_semana3').removeClass('has-error');
        else
          $('#div_no_semana3_' + q).removeClass('has-error');
        if ($("#valor_" + p).length == 1)
          document.getElementById('valor_' + q).style.backgroundColor = '#FFF';
      } else {
        $('#div_no_semana3').addClass('gabriel has-error');
        $('#div_no_semana3_' + q).removeClass('state-error').addClass('has-error');
        if (($('#valor_' + q).length) == 1) {
          document.getElementById('valor_' + q).style.backgroundColor = '#FFF0F0';
        }
      }
    }

    // Por lo menos debe existir un 100  en los pesos
    if (pesos == false) {
      for (var h = 1; h <= 4; h++) {
        // dependiendo del tipo del respuesta
        if (type_respuesta == 'T') {
          $('#div_ds_grade_' + h).removeClass('state-error').addClass('has-error');
          document.getElementById('ds_grade_' + h).style.backgroundColor = '#FFF0F0';
        } else {
          $('#div_ds_grade_img_' + h).removeClass('state-error').addClass('has-error');
          document.getElementById('ds_grade_img_' + h).style.backgroundColor = '#FFF0F0';
        }
      }
      // btn_save.addClass('disabled');
    } else {
      for (var h = 1; h <= 4; h++) {
        // dependiendo del tipo del respuesta
        if (type_respuesta == 'T') {
          $('#div_ds_grade_' + h).removeClass('has-error');
          document.getElementById('ds_grade_' + h).style.backgroundColor = '#FFF';
        } else {
          $('#div_ds_grade_img_' + h).removeClass('has-error');
          document.getElementById('ds_grade_img_' + h).style.backgroundColor = '#FFF';
        }
      }
    }

    if ((tquiz != '' && vquiz > 0) || (tquiz == '' && vquiz > 0) || (tquiz != '' && (vquiz == 0 || vquiz == ''))) {
      // Si los campos estan llenos y cumplen las condiciones activamos el boton
      if (tquiz != '' && vquiz > 0 && preg1 != '' && vpreg1 > 0 && resp1 != '' && resp2 != '' && resp3 != '' && resp4 != '' && remaining >= 0 && rem_pre1 >= 0 && pesos == true && tot_vals_pregunts == 100) {
        $('#div_nb_quiz').removeClass('has-error');
        document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
        $('#div_no_semana2').removeClass('input has-error');
        document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
        document.getElementById('no_valor_quiz').style.borderColor = '#ccc';
        $('#div_ds_pregunta_1').removeClass('has-error');
        document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF';
        $('#div_no_semana3').removeClass('has-error');
        document.getElementById('valor_1').style.backgroundColor = '#FFF';
        document.getElementById('valor_1').style.borderColor = '#ccc';
        for (var r = 1; r <= 4; r++) {
          if (type_respuesta == 'T') {
            $('#div_ds_resp_' + r).removeClass('has-error');
            document.getElementById('ds_resp_' + r).style.backgroundColor = '#FFF';
            $('#div_ds_grade_' + r).removeClass('has-error');
            document.getElementById('ds_grade_' + r).style.backgroundColor = '#FFF';
          } else {
            $('#mydropzone_' + r).removeClass('bg-color-red');
            $('#div_ds_grade_img_' + r).removeClass('has-error');
            document.getElementById('ds_grade_img_' + r).style.backgroundColor = '#FFF';
          }
        }

        // habilitamos el boton
        btn_save.removeClass('disabled');
        // Quitamos color
        $("#tab_4").removeClass("txt-color-red");
        $("#error_preguntas_valores").addClass('hidden');
      } else {
        $('#div_ds_pregunta_1').addClass('has-error');
        document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF0F0';
        $('#div_no_semana3').addClass('has-error');
        document.getElementById('valor_1').style.backgroundColor = '#FFF0F0';

        // Titulo y valor del quiz llenos
        if (tquiz != '' && vquiz > 0) {
          if (preg1 == '' && vpreg1 <= 0 && resp1 == '' && resp2 == '' && resp3 == '' && resp4 == '') {
            $("#cont_tab_quiz_1").click();
          }
          $('#div_nb_quiz').removeClass('has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
          $('#div_no_semana2').removeClass('has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
          btn_save.addClass('disabled');
        }

        // Titulo del quiz lleno y valor vacio
        if (tquiz != '' && (vquiz == 0 || vquiz == '')) {
          $('#div_no_semana2').click();
          if (tquiz != '') {
            $('#div_nb_quiz').removeClass('has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
          }
          if ((vquiz == 0 || vquiz == '')) {
            $('#div_no_semana2').removeClass('state-error').addClass('input has-error');
            document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
          }
          btn_save.addClass('disabled');
        }


        // Valor del quiz lleno y titulo vacio
        if (tquiz == '' && vquiz > 0) {
          $('#div_nb_quiz').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
          $('#div_no_semana2').remove('has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
          btn_save.addClass('disabled');
        }


        // Si algunos campos estan llenos entonces no los marcara dependiento del tipo de respuesta
        if (preg1 != '') {
          if (vpreg1 <= 0 || vpreg1 == '')
            $("#div_no_semana3").click();
          $('#div_ds_pregunta_1').removeClass('has-error');
          document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF';
        }

        if (vpreg1 >= 0 || vpreg1 <= 100) {
          if (vpreg1 == 0) {
            $('#div_no_semana3').removeClass('state-error').addClass('has-error');
            document.getElementById('valor_1').style.backgroundColor = '#FFF0F0';
          } else {
            if (vpreg1 > 0 && vpreg1 < 100) {
              $('#div_no_semana3').removeClass('has-error');
              document.getElementById('valor_1').style.backgroundColor = '#FFF';
              document.getElementById('valor_1').style.borderColor = '#E1C555';
            }
            if (vpreg1 == 100) {
              $('#div_no_semana3').removeClass('has-error');
              document.getElementById('valor_1').style.backgroundColor = '#FFF';
            }
          }

          // $("#error_preguntas_valores").removeClass('hidden');
          $("#error_preguntas_valores").addClass('hidden');
        } else {
          $("#error_preguntas_valores").addClass('hidden');
        }

        if (tot_vals_pregunts < 100) {
          $("#div_no_semana3").click();
        }

        if (type_respuesta == 'T') {

          if (resp1 == '') {
            $('#div_ds_resp_1').removeClass('state-error').addClass('has-error');
            document.getElementById('ds_resp_1').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_1').removeClass('has-error');
            document.getElementById('ds_resp_1').style.backgroundColor = '#FFF';
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_1').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_1').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_1').removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_1').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_1').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_1').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_1').style.borderColor = '#E1C555';
            }
          }
          if (resp2 == '') {
            $('#div_ds_resp_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_2').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_2').removeClass('has-error');
            document.getElementById('ds_resp_2').style.backgroundColor = '#FFF';
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_2').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_2').removeClass('has-error');
            if (vresp2 == 100 || pesos == true) {
              document.getElementById('ds_grade_2').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_2').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_2').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_2').style.borderColor = '#E1C555';
            }
          }
          if (resp3 == '') {
            $('#div_ds_resp_3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_3').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_3').removeClass('has-error');
            document.getElementById('ds_resp_3').style.backgroundColor = '#FFF';
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_3').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_3').removeClass('has-error');
            if (vresp3 == 100 || pesos == true) {
              document.getElementById('ds_grade_3').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_3').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_3').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_3').style.borderColor = '#E1C555';
            }
          }
          if (resp4 == '') {
            $('#div_ds_resp_4').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_4').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_4').removeClass('has-error');
            document.getElementById('ds_resp_4').style.backgroundColor = '#FFF';
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_4').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_4').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_4').removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_4').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_4').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_4').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_4').style.borderColor = '#E1C555';
            }
          }
        } else {
          if (resp1 == '') {
            $('#mydropzone_1').addClass('bg-color-red');
          } else {
            $('#mydropzone_1').removeClass('bg-color-red');
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_img_1').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_1').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_1').removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_1').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_1').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_1').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_1').style.borderColor = '#E1C555';
            }
          }
          if (resp2 == '') {
            $('#mydropzone_2').addClass('bg-color-red');
          } else {
            $('#mydropzone_2').removeClass('bg-color-red');
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_img_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_2').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_2').removeClass('has-error');
            if (vresp2 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_2').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_2').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_2').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_2').style.borderColor = '#E1C555';
            }
          }
          if (resp3 == '') {
            $('#mydropzone_3').addClass('bg-color-red');
          } else {
            $('#mydropzone_3').removeClass('bg-color-red');
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_img_3').removeClass('state-error').addClass('form-grouphas-error');
            document.getElementById('ds_grade_img_3').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_3').removeClass('has-error');
            if (vresp3 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_3').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_3').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_3').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_3').style.borderColor = '#E1C555';
            }
          }
          if (resp4 == '') {
            $('#mydropzone_4').addClass('bg-color-red');
          } else {
            $('#mydropzone_4').removeClass('bg-color-red');
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_img_4').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_4').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_4').removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_4').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_4').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_4').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_4').style.borderColor = '#E1C555';
            }
          }
        }

        if (pesos == true) {
          for (var m = 1; m <= 4; m++) {
            // Dependiendo del tipo del respuesta
            if (type_respuesta == 'T') {
              $('#div_ds_grade_' + m).removeClass('input has-error');
              document.getElementById('ds_grade_' + m).style.backgroundColor = '#FFF';
            } else {
              $('#div_ds_grade_img_' + m).removeClass('input has-error');
              document.getElementById('ds_grade_img_' + m).style.backgroundColor = '#FFF';
            }
          }
          if (resp1 == '' || resp2 == '' || resp3 == '' || resp4 == '')
            $("#error_preguntas_valores").removeClass('hidden');
        }


      }
    } else {
      // si todo esta en blanco      
      if (tquiz == '' && (vquiz == 0 || vquiz == '') && preg1 == '' && (vpreg1 == 0 || vpreg1 == '') &&
        resp1 == '' && resp2 == '' && resp3 == '' && resp4 == '' &&
        (vresp1 == 0 || vresp1 == '') && (vresp2 == 0 || vresp2 == '') && (vresp3 == 0 && vresp3 == '') && (vresp4 == 0 || vresp4 == '')
      ) {
        $('#div_nb_quiz').removeClass('has-error');
        document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
        $('#div_no_semana2').removeClass('has-error');
        document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
        $('#div_ds_pregunta_1').removeClass('has-error');
        document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF';
        $('#div_no_semana3').removeClass('has-error');
        document.getElementById('valor_1').style.backgroundColor = '#FFF';
        // document.getElementById('valor_1').style.borderColor = '#ccc';   
        // Muestra mensaje de warning
        // $("#error_t_v_quiz").hide();
        for (var j = 1; j <= 4; j++) {
          if (type_respuesta == 'T') {
            $('#div_ds_resp_' + j).removeClass('has-error');
            document.getElementById('ds_resp_' + j).style.backgroundColor = '#FFF';
            $('#div_ds_grade_' + j).removeClass('has-error');
            document.getElementById('ds_grade_' + j).style.backgroundColor = '#FFF';
          } else {
            $('#mydropzone_' + j).removeClass('bg-color-red');
            $('#div_ds_grade_img_' + j).removeClass('has-error');
            document.getElementById('ds_grade_img_' + j).style.backgroundColor = '#FFF';
          }
        }

        // habilitamos boton
        btn_save.removeClass('disabled');
        // Quitamos color
        $("#tab_4").removeClass("txt-color-red");
        $("#error_preguntas_valores").addClass('hidden');
      } else {
        if (tquiz == '') {
          $('#div_nb_quiz').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
        } else {
          $('#div_nb_quiz').remove('has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
        }
        if (vquiz == 0 || vquiz == '') {
          $('#div_no_semana2').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
        }
        if (preg1 == '') {
          $('#div_ds_pregunta_1').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF0F0';
        } else {
          $('#div_ds_pregunta_1').removeClass('input has-error');
          document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF';
          if (tquiz == '') {
            $('#div_nb_quiz').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_nb_quiz').remove('has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
          }
          if (vquiz == 0 || vquiz == '') {
            $('#div_no_semana2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
          }
          if (vpreg1 == 0 || vpreg1 == '') {
            $('#div_no_semana3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('valor_1').style.backgroundColor = '#FFF0F0';
          }
          for (var j = 1; j <= 4; j++) {
            if (type_respuesta == 'T') {
              if ($('#ds_resp_' + j).val() == '') {
                $('#div_ds_resp_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_resp_' + j).style.backgroundColor = '#FFF0F0';
              }
              if ($('#ds_grade_' + j).val() == 0 || $('#ds_grade_' + j).val() == '') {
                $('#div_ds_grade_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_grade_' + j).style.backgroundColor = '#FFF0F0';
              }
            } else {
              if ($('#nb_img_prev_mydropzone_' + j).val() == '') {
                $('#mydropzone_' + j).addClass('bg-color-red');
              }
              if ($('#ds_grade_img_' + j).val() == 0 || $('#ds_grade_img_' + j).val() == '') {
                $('#div_ds_grade_img_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_grade_img_' + j).style.backgroundColor = '#FFF0F0';
              }
            }
          }
          btn_save.addClass('disabled');
        }
        if (vpreg1 == 0) {
          $('#div_no_semana3').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('valor_1').style.backgroundColor = '#FFF0F0';
        } else {
          if (tquiz == '') {
            $('#div_nb_quiz').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
          } else {

            $('#div_nb_quiz').remove('has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
          }
          if (vquiz == 0 || vquiz == '') {
            $('#div_no_semana2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
          }
          if (preg1 == '') {
            $('#div_ds_pregunta_1').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF0F0';
          }
          $('#div_no_semana3').removeClass('has-error');
          document.getElementById('valor_1').style.backgroundColor = '#FFF';
          for (var j = 1; j <= 4; j++) {
            if (type_respuesta == 'T') {
              if ($('#ds_resp_' + j).val() == '') {
                $('#div_ds_resp_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_resp_' + j).style.backgroundColor = '#FFF0F0';
              }
              if ($('#ds_grade_' + j).val() == 0 || $('#ds_grade_' + j).val() == '') {
                $('#div_ds_grade_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_grade_' + j).style.backgroundColor = '#FFF0F0';
              }
            } else {
              if ($('#nb_img_prev_mydropzone_' + j).val() == '') {
                $('#mydropzone_' + j).addClass('bg-color-red');
              }
              if ($('#ds_grade_img_' + j).val() == 0 || $('#ds_grade_img_' + j).val() == '') {
                $('#div_ds_grade_img_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_grade_img_' + j).style.backgroundColor = '#FFF0F0';
              }

            }
          }
          btn_save.addClass('disabled');
        }

        if (type_respuesta == 'T') {
          if (resp1 == '') {
            $('#div_ds_resp_1').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_1').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_1').removeClass('has-error');
            document.getElementById('ds_resp_1').style.backgroundColor = '#FFF';
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_1').removeClass('tate-error').addClass('form-group has-error');
            document.getElementById('ds_grade_1').style.backgroundColor = '#FFF0F0';
          }
          if (resp2 == '') {
            $('#div_ds_resp_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_2').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_2').removeClass('has-error');
            document.getElementById('ds_resp_2').style.backgroundColor = '#FFF';
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_2').style.backgroundColor = '#FFF0F0';
          }
          if (resp3 == '') {
            $('#div_ds_resp_3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_3').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_3').removeClass('has-error');
            document.getElementById('ds_resp_3').style.backgroundColor = '#FFF';
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_3').style.backgroundColor = '#FFF0F0';
          }
          if (resp4 == '') {
            $('#div_ds_resp_4').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_4').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_4').removeClass('has-error');
            document.getElementById('ds_resp_4').style.backgroundColor = '#FFF';
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_4').removeClass(' state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_4').style.backgroundColor = '#FFF0F0';
          }
        } else {
          if (resp1 == '') {
            $('#mydropzone_1').addClass('bg-color-red');
          } else {
            $('#mydropzone_1').removeClass('bg-color-red');
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_img_1').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_1').style.backgroundColor = '#FFF0F0';
          }
          if (resp2 == '') {
            $('#mydropzone_2').addClass('bg-color-red');
          } else {
            $('#mydropzone_2').removeClass('bg-color-red');
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_img_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_2').style.backgroundColor = '#FFF0F0';
          }
          if (resp3 == '') {
            $('#mydropzone_3').addClass('bg-color-red');
          } else {
            $('#mydropzone_3').removeClass('bg-color-red');
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_img_3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_3').style.backgroundColor = '#FFF0F0';
          }
          if (resp4 == '') {
            $('#mydropzone_4').addClass('bg-color-red');
          } else {
            $('#mydropzone_4').removeClass('bg-color-red');
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_img_4').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_4').style.backgroundColor = '#FFF0F0';
          }
        }

        if (pesos == true) {
          for (var m = 1; m <= 4; m++) {
            // Dependiendo del tipo del respuesta
            if (type_respuesta == 'T') {
              $('#div_ds_grade_' + m).removeClass('has-error');
              document.getElementById('ds_grade_' + m).style.backgroundColor = '#FFF';
            } else {
              $('#div_ds_grade_img_' + m).removeClass('has-error');
              document.getElementById('ds_grade_img_' + m).style.backgroundColor = '#FFF';
            }
          }
        }
        // Deshabilitamos el boton
        btn_save.addClass('disabled');
        $("#error_preguntas_valores").removeClass('hidden');
      }
    }
  }

  $("#nb_quiz").keypress(function() {
    Valida_Quiz();
  });
  $("#ds_pregunta_1").keypress(function() {
    Valida_Quiz();
  })
  for (var k = 1; k <= 4; k++) {
    $("#ds_resp_" + k).keypress(function() {
      Valida_Quiz();
    })
    // Agregamos a los pesos un popover
    $("#ds_grade_" + k).attr('rel', 'popover-hover').attr('data-placement', 'top').attr('data-original-title', '<?php echo $warning; ?>').attr('data-content', '<?php echo ObtenEtiqueta(1896); ?>');
  }
</script>
<?php
# Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
if ($permiso == PERMISO_DETALLE)
  $fg_guardar = ValidaPermiso(FUNC_LMED_SP, PERMISO_MODIFICACION);
else
  $fg_guardar = True;
Forma_Termina($fg_guardar);

# Incluimos el header
echo "
              </article>
              <!-- WIDGET END -->
            </div>
            <!-- End Row -->
          </section>
          <!-- end widget grid -->
        </div>
        <!-- END MAIN CONTENT -->
      </div>
      <!-- END MAIN PANEL -->";
include(SP_HOME . "/AD3M2SRC4/bootstrap/inc/scripts.php");
echo "
    <script src='" . PATH_LIB . "/fame/dropzone.min.js'></script>
    <script src='" . PATH_SELF_JS . "/plugin/x-editable/moment.min.js'></script>
    <script src='" . PATH_SELF_JS . "/plugin/x-editable/jquery.mockjax.min.js'></script>
    <script src='" . PATH_SELF_JS . "/plugin/x-editable/x-editable.min.js'></script>
    <!-- Librerias necesarias para preview de rubric -->
    <!---plugin necesario para pintar el circulo -->
    <script src='" . PATH_HOME . "/bootstrap/js/plugin/knob/jquery.knob.min.js'></script>
    <!-- Script para preview de rubric -->
    <script>";
if (!empty($ds_vl_ruta)) {
  echo "
        // Consulta el archivo convertidor
        setInterval(function(){
        var total_convertido = $('#total_convertido').val(); 
        if(total_convertido<100){
          $.ajax({
              type: 'GET',
              url : 'progreso_comando.php',
              data: 'clave=" . $clave . "'+
                    '&archivo=" . $ds_vl_ruta . "'
          }).done(function(result){
            var content, tabContainer;
            content = JSON.parse(result);
            progress = content.progress;
            if(!content.error){
              if(progress<=100){
                $('#duration').empty().append(content.duration + '&nbsp;Mins');
                $('#grl_progress').attr('data-progressbar-value', progress);
                $('#progress_hls').empty().append(progress + '%');
                $('#camp_progreso_hls').empty().val(progress);
                $('#total_convertido').empty().val(progress);
              }
            }
            else{
              $('#grl_progress1').empty().append('Error upload');
            }
          });
        }
        $('#code_info').addClass('hidden');
      }, 
      2000);";
}
echo "
      // Rubric
      $('#btnAbrePreviewRubric').on('click',function(){    
      document.getElementById('myModalLabelaa').innerHTML = \"<b>Rubric: </b>\" + document.getElementById('ds_titulo').value;
      
      $.ajax({
        type: 'POST',
        url: 'rubric_preview_modal.php',
        data:'clave=" . $clave . "',
        success: function(data) {
          $('#PreviewRubric').html(data);
        }
      })
    });
  </script>";
include($_SERVER['DOCUMENT_ROOT'] . "/AD3M2SRC4/bootstrap/inc/footer.php");
?>
