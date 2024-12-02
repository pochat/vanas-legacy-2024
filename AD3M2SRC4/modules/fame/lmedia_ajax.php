<?php

$nuevo = $_REQUEST['nuevo'];
if ($nuevo) {

  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion();

  # Recover parameters from frm Quiz Tab1
  $tabCounter = RecibeParametroNumerico('tabCounter');
  $fg_tipo_resp_[$tabCounter] = "T";
  $fg_tipo_img_[$tabCounter] = "L";
  $fl_quiz_pregunta_[$tabCounter] = 0;
  $ds_quiz_[$tabCounter] = 0;
  $resta_tab = $tabCounter - 1;

  echo "<script>
            $(document).ready(function() {
            val_ant = $('#ds_quiz_$resta_tab').val();
            $('#ds_quiz_$tabCounter').val(val_ant);
            })    
          </script>";
  $nuevoo = 1;
} else {
  $tabCounter = $i;
  if ($fg_error == 0) {
    // Question Variables start here
    $row = RecuperaValor("SELECT fg_tipo, ds_pregunta, ds_pregunta_esp, ds_pregunta_fra, ds_valor_pregunta, fg_posicion_img, ds_course_pregunta, no_orden, fl_quiz_pregunta FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp  AND no_orden = $i ");
    $fg_tipo_resp_[$tabCounter] = str_texto($row["fg_tipo"]);
    $ds_pregunta_[$tabCounter] = str_texto($row["ds_pregunta"]);
    // Lang translation Question (esp, fra)
    $ds_pregunta_esp_[$tabCounter] = str_texto($row["ds_pregunta_esp"]);
    $ds_pregunta_fra_[$tabCounter] = str_texto($row["ds_pregunta_fra"]);
    // Lang translation Question (esp, fra)
    $ds_quiz_[$tabCounter] = $row["ds_valor_pregunta"];
    $fg_tipo_img_[$tabCounter] = str_texto($row["fg_posicion_img"]);
    $ds_course_[$tabCounter] = $row["ds_course_pregunta"];
    $no_orden = $row["no_orden"];
    $fl_quiz_pregunta_[$tabCounter] = $row["fl_quiz_pregunta"];
    $valor_ini_preg[$tabCounter] = $valor_ini_preg;
    // Answer1 Variables start here
    $row = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta_[$tabCounter] AND no_tab = $i AND no_orden = 1 ");
    $ds_resp_1_[$tabCounter] = str_texto($row["ds_respuesta"]);
    // Lang translation Answer (esp, fra)
    $ds_resp_esp_1_[$tabCounter] = str_texto($row["ds_respuesta_esp"]);
    $ds_resp_fra_1_[$tabCounter] = str_texto($row["ds_respuesta_fra"]);
    // Lang translation Answer (esp, fra)
    $fg_tipo_resp_[$tabCounter] == 'T' ?   $ds_grade_1_[$tabCounter] = $row["ds_valor_respuesta"] : $ds_grade_img_1_[$tabCounter] = $row["ds_valor_respuesta"];
    // Answer2 Variables start here
    $row = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta_[$tabCounter] AND no_tab = $i AND no_orden = 2 ");
    $ds_resp_2_[$tabCounter] = str_texto($row["ds_respuesta"]);
    // Lang translation Answer (esp, fra)
    $ds_resp_esp_2_[$tabCounter] = str_texto($row["ds_respuesta_esp"]);
    $ds_resp_fra_2_[$tabCounter] = str_texto($row["ds_respuesta_fra"]);
    // Lang translation Answer (esp, fra)
    $fg_tipo_resp_[$tabCounter] == 'T' ?   $ds_grade_2_[$tabCounter] = $row["ds_valor_respuesta"] : $ds_grade_img_2_[$tabCounter] = $row["ds_valor_respuesta"];
    // Answer3 Variables start here
    $row = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta_[$tabCounter] AND no_tab = $i AND no_orden = 3 ");
    $ds_resp_3_[$tabCounter] = str_texto($row["ds_respuesta"]);
    // Lang translation Answer (esp, fra)
    $ds_resp_esp_3_[$tabCounter] = str_texto($row["ds_respuesta_esp"]);
    $ds_resp_fra_3_[$tabCounter] = str_texto($row["ds_respuesta_fra"]);
    // Lang translation Answer (esp, fra)
    $fg_tipo_resp_[$tabCounter] == 'T' ?   $ds_grade_3_[$tabCounter] = $row["ds_valor_respuesta"] : $ds_grade_img_3_[$tabCounter] = $row["ds_valor_respuesta"];
    // Answer4 Variables start here
    $row = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_respuesta_esp, ds_respuesta_fra, ds_valor_respuesta, no_tab FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta_[$tabCounter] AND no_tab = $i AND no_orden = 4 ");
    $ds_resp_4_[$tabCounter] = str_texto($row["ds_respuesta"]);
    // Lang translation Answer (esp, fra)
    $ds_resp_esp_4_[$tabCounter] = str_texto($row["ds_respuesta_esp"]);
    $ds_resp_fra_4_[$tabCounter] = str_texto($row["ds_respuesta_fra"]);
    // Lang translation Answer (esp, fra)
    $fg_tipo_resp_[$tabCounter] == 'T' ?   $ds_grade_4_[$tabCounter] = $row["ds_valor_respuesta"] : $ds_grade_img_4_[$tabCounter] = $row["ds_valor_respuesta"];
  } else {
    # Recover parameters from extra Questions tabs
    for ($x = 2; $x <= $no_max_tabs; $x++) {
      $fg_tipo_resp_[$x] = RecibeParametroHTML("fg_tipo_resp_$x");
      $fg_tipo_img_[$x]  = RecibeParametroHTML("fg_tipo_img_$x");
      $ds_pregunta_[$x]  = RecibeParametroHTML("ds_pregunta_$x");
      // Lang translation Question (esp, fra)
      $ds_pregunta_esp_[$x]  = RecibeParametroHTML("ds_pregunta_esp_$x");
      $ds_pregunta_fra_[$x]  = RecibeParametroHTML("ds_pregunta_fra_$x");
      // Lang translation Question (esp, fra)
      $ds_quiz_[$x]      = RecibeParametroNumerico("ds_quiz_$x");
      $ds_course_[$x]    = RecibeParametroNumerico("ds_course_$x");
      $valor_[$x]        = RecibeParametroNumerico("valor_$x");
      if (empty($ds_quiz_[$x]))
        $ds_quiz_[$x] = $valor_[$x];
      $q_remaining_[$x]  = RecibeParametroNumerico("q_remaining_$x");
      $no_orden_pregunta_[$x] = $x;

      if ($fg_tipo_resp_[$x] == "T") {
        # Recover text type Answers from extra Tabs
        $ds_resp_1_[$x]  = RecibeParametroHTML("ds_resp_1_$x");
        // Lang translation Answer (esp, fra)
        $ds_resp_esp_1_[$x]  = RecibeParametroHTML("ds_resp_esp_1_$x");
        $ds_resp_fra_1_[$x]  = RecibeParametroHTML("ds_resp_fra_1_$x");
        // Lang translation Answer (esp, fra)
        $ds_grade_1_[$x] = RecibeParametroNumerico("ds_grade_1_$x");
        $ds_resp_2_[$x]  = RecibeParametroHTML("ds_resp_2_$x");
        // Lang translation Answer (esp, fra)
        $ds_resp_esp_2_[$x]  = RecibeParametroHTML("ds_resp_esp_2_$x");
        $ds_resp_fra_2_[$x]  = RecibeParametroHTML("ds_resp_fra_2_$x");
        // Lang translation Answer (esp, fra)
        $ds_grade_2_[$x] = RecibeParametroNumerico("ds_grade_2_$x");
        $ds_resp_3_[$x]  = RecibeParametroHTML("ds_resp_3_$x");
        // Lang translation Answer (esp, fra)
        $ds_resp_esp_3_[$x]  = RecibeParametroHTML("ds_resp_esp_3_$x");
        $ds_resp_fra_3_[$x]  = RecibeParametroHTML("ds_resp_fra_3_$x");
        // Lang translation Answer (esp, fra)
        $ds_grade_3_[$x] = RecibeParametroNumerico("ds_grade_3_$x");
        $ds_resp_4_[$x]  = RecibeParametroHTML("ds_resp_4_$x");
        // Lang translation Answer (esp, fra)
        $ds_resp_esp_4_[$x]  = RecibeParametroHTML("ds_resp_esp_4_$x");
        $ds_resp_fra_4_[$x]  = RecibeParametroHTML("ds_resp_fra_4_$x");
        // Lang translation Answer (esp, fra)
        $ds_grade_4_[$x] = RecibeParametroNumerico("ds_grade_4_$x");
      } else {
        # Recover image type Answers from extra tabs
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
  }
  // Dropzone Field
  $editar_[$tabCounter] = True;
  $nuevoo = 0;
}

echo "<input type='hidden' name='no_max_tabs' value='$tabCounter'>";
# Popover donde muestra mensaje en cada campo 
$warning = "warning";
/*function popover[$tabCounter]($accion='',  $posicion='', $title='', $content=''){
      if(empty($accion))
        $accion = "popover";
      if(empty($posicion))
        $posicion = "top";
      $popover = "rel='".$accion."' data-placement='".$posicion."' ";
      if(!empty($title))
        $popover .= "data-original-title='".$title."' ";
      if(!empty($content))
        $popover .= "data-content='".$content."' ";
      return $popover;
    }*/
?>
<!-- HTML TAB STARTS HERE -->
<div class="row">
  <script type="text/javascript">
    // Question type
    function showContent_<?php echo $tabCounter; ?>(val) {
      img_b_1_<?php echo $tabCounter; ?> = document.getElementById("img_based_1_<?php echo $tabCounter; ?>");
      img_b_3_<?php echo $tabCounter; ?> = document.getElementById("img_based_3_<?php echo $tabCounter; ?>");
      img_b_2_<?php echo $tabCounter; ?> = document.getElementById("img_based_2_<?php echo $tabCounter; ?>");
      img_b_2_esp_<?php echo $tabCounter; ?> = document.getElementById("img_based_2_esp_<?php echo $tabCounter; ?>");
      img_b_2_fra_<?php echo $tabCounter; ?> = document.getElementById("img_based_2_fra_<?php echo $tabCounter; ?>");
      document.getElementById("fg_tipo_preg_<?php echo $tabCounter; ?>").value = val;
      check = document.getElementById("fg_tipo_resp_<?php echo $tabCounter; ?>");
      if (check.checked) {
        img_b_1_<?php echo $tabCounter; ?>.style.display = 'none';
        img_b_3_<?php echo $tabCounter; ?>.style.display = 'none';
        img_b_2_<?php echo $tabCounter; ?>.style.display = 'block';
        img_b_2_esp_<?php echo $tabCounter; ?>.style.display = 'block';
        img_b_2_fra_<?php echo $tabCounter; ?>.style.display = 'block';
      } else {
        img_b_1_<?php echo $tabCounter; ?>.style.display = 'block';
        img_b_3_<?php echo $tabCounter; ?>.style.display = 'block';
        img_b_2_<?php echo $tabCounter; ?>.style.display = 'none';
        img_b_2_esp_<?php echo $tabCounter; ?>.style.display = 'none';
        img_b_2_fra_<?php echo $tabCounter; ?>.style.display = 'none';
      }
    }
    // Image Question, image type
    function TipoImagen_<?php echo $tabCounter; ?>(val) {
      document.getElementById("fg_tipo_img_prev_<?php echo $tabCounter; ?>").value = val;
    }
  </script>
  <!-- Select Type of Answer, radio button -->
  <div class="col col-xs-6 col-sm-4">
    <?php
    echo "<div class='form-group'>";
    echo "<label class='col-md-3 control-label text-align-right'>";
    echo "<strong>";
    echo ObtenEtiqueta(1201);
    echo "</strong>";
    echo "</label>";
    echo "<div class='col-md-9'>";
    echo "<div class='radio'>";
    echo "<label>";
    CampoRadio("fg_tipo_resp_$tabCounter", 'T', $fg_tipo_resp_[$tabCounter], ObtenEtiqueta(1202), True, "onchange='showContent_$tabCounter(this.value); Valida_Quiz_new_tab(" . $tabCounter . ");'");
    echo "</label>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<label>";
    CampoRadio("fg_tipo_resp_$tabCounter", 'I', $fg_tipo_resp_[$tabCounter], ObtenEtiqueta(1203), True, "onchange='showContent_$tabCounter(this.value); Valida_Quiz_new_tab(" . $tabCounter . ");'");
    echo "</label>";
    echo "</div>";
    echo "</div>";
    Forma_CampoOculto("fg_tipo_preg_$tabCounter", $fg_tipo_resp_[$tabCounter]);
    echo "</div>";
    ?>
  </div>
  <!-- Select the type of answer to show (text, image) -->
  <?php
  if ($fg_tipo_resp_[$tabCounter] == 'I') {
    $style_1 = "style='display: block;'";
  } else {
    $style_1 = "style='display: none;'";
  }
  ?>
  <!-- Select aspect ratio for the image Answer, radio button -->
  <div id="img_based_1_<?php echo $tabCounter; ?>" <?php echo $style_1; ?>>
    <div class="col col-xs-6 col-sm-6">
      <?php
      echo " <div class='form-group'>";
      echo "<label class='col-md-2 control-label text-align-right'>";
      echo "<strong></strong>";
      echo "</label>";
      echo "<div class='col-md-8'>";
      echo "<div class='radio'>";
      echo "<label>";
      CampoRadio("fg_tipo_img_$tabCounter", 'L', $fg_tipo_img_[$tabCounter], ObtenEtiqueta(1211), True, "onchange='TipoImagen_$tabCounter(this.value);'");
      echo "</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>";
      CampoRadio("fg_tipo_img_$tabCounter", 'P', $fg_tipo_img_[$tabCounter], ObtenEtiqueta(1212), True, "onchange='TipoImagen_$tabCounter(this.value);'");
      echo "</label>";
      echo "</div>";
      echo "</div>";
      Forma_CampoOculto("fg_tipo_img_prev_$tabCounter", $fg_tipo_img_[$tabCounter]);
      echo "</div>";
      ?>
    </div>
  </div>
</div>

<div class="row"><br></div>
<!-- START QUIZ Lang Tabs -->
<div class="tab-pane fade in active" id="quiz_lang_<?php echo $tabCounter; ?>">
  <!-- START WIDGET BODY -->
  <div class="widget-body">
    <ul id="myTabQuizLang" class="nav nav-tabs bordered">
      <li class="active">
        <a id="mytabQuizLang1" href="#tab-quiz_eng<?php echo $tabCounter; ?>" data-toggle="tab">
          English
        </a>
      </li>
      <li class="">
        <a id="mytabQuizLang2" href="#tab-quiz_esp<?php echo $tabCounter; ?>" data-toggle="tab">
          Spanish
        </a>
      </li>
      <li class="">
        <a id="mytabQuizLang3" href="#tab-quiz_fra<?php echo $tabCounter; ?>" data-toggle="tab">
          French
        </a>
      </li>
    </ul>
    <div id="myTabQuizCont_<?php echo $tabCounter; ?>" class="tab-content padding-10 no-border">
      <div class="tab-pane fade in active" id="tab-quiz_eng<?php echo $tabCounter; ?>">
        <!-- START English Content-->
        <div class="col col-xs-12 col-sm-5">
          <?php Forma_CampoTexto(ObtenEtiqueta(1200) . " <span id='NoPreg_$tabCounter'>$tabCounter</span>", False, "ds_pregunta_$tabCounter", $ds_pregunta_[$tabCounter], 255, 63, $err_val_pregunta_x_[$tabCounter], False, '', True, '', '', "smart-form", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
        </div>
        <div class="col col-xs-12 col-sm-1">
        </div>
        <div class="col col-xs-12 col-sm-2">
          <div id="div_no_semana3_<?php echo $tabCounter; ?>" class="row form-group ">
            <label class="col col-sm-12 control-label text-align-left">
              <strong><?php echo $var . ObtenEtiqueta(1255);
                      $tabCounter ?>:</strong>
            </label>
            <div class="col-sm-12">
              <div class="smart-form">
                <label class="input" id="error_msj3_<?php echo $tabCounter; ?>">
                  <input class="form-control" id="valor_<?php echo $tabCounter; ?>" name="valor_<?php echo $tabCounter; ?>" value="<?php echo $ds_quiz_[$tabCounter]; ?>" maxlength="3" size="12" type="text" onkeyup="Suma_val_preg_quiz(this.value); $('#muestra_valor_1').removeAttr('padding-left').css('padding-left', '20%'); Valida_Quiz_new_tab(<?php echo $tabCounter; ?>); " onKeyPress="return SoloNumeros(event);">
                </label>
                <?php
                if (!empty($err_val_preg_x_[$tabCounter]))
                  echo "<span class='help-block txt-color-red'><i class='fa fa-warning'></i>" . ObtenMensaje(3) . "</span>";
                ?>
              </div>
            </div>
          </div>
        </div>
        <?php
        if (!empty($err_val_preg_x_[$tabCounter])) {
          ?>
          <script>
            var tab = "<?php echo $tabCounter; ?>";
            $("#error_msj3_" + tab).addClass("state-error");
          </script>
        <?php
        } else {
          ?>
          <script>
            var tab = "<?php echo $tabCounter; ?>";
            $("#error_msj3_" + tab).removeClass("state-error");
          </script>
        <?php
        }
        ?>
        <div class="col col-xs-12 col-sm-2">
          <?php
          Forma_CampoOculto("q_remaining_$tabCounter", $q_remaining_[$tabCounter]);
          Forma_CampoTexto(ObtenEtiqueta(1213), False, "ds_quiz_$tabCounter", $valor_ini_preg[$tabCounter], 3, 16, $ds_quiz_err_[$tabCounter], False, '', True, 'disabled', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
          if ($q_remaining_[$tabCounter] != 0) {
            echo "<script>
                document.getElementById('ds_quiz_$tabCounter').value = document.getElementById('q_remaining_$tabCounter').value;
                </script>";
          }
          ?>
        </div>
        <div class="col col-xs-12 col-sm-2">
          <?php
          echo "<div id='div_ds_quiz_1' class='row form-group '>
                <label class='col col-sm-12 control-label text-align-left'><strong>&nbsp;&nbsp;  </strong></label>
                <div class='col col-sm-12'><label class='input'><a href='javascript:MuestraPreview_$tabCounter($tabCounter);' class='btn btn-primary'  >" . ObtenEtiqueta(1208) . "</a></div>      
                </div>";
          ?>
        </div>
        <div class="row">
          <?php
          if ($valor_ini_preg == 0)
            $style = "style='display:none;'";
          elseif ($valor_ini_preg > 0)
            $style = "style='display:block;'";
          else
            $style = "style='display:none;'";
          # Estilo para mensaje: Una respuesta debe valer 100
          if (($tabCounter == $tabCounter) and ($err_valor_repuestas_[$tabCounter]))
            $style_resp = "style='display:block;'";
          else
            $style_resp = "style='display:none;'";

          # Estilo para mensaje: REvise valor de pregunta
          if (($tabCounter == $tabCounter) and ($err_val_preg_x_[$tabCounter]))
            $style_preg_val = "style='display:block;'";
          else
            $style_preg_val = "style='display:none;'";

          # Estilo para mensaje: Revise valor de respuestas
          if (($tabCounter == $tabCounter) and ($err_resp_preg_x_[$tabCounter]))
            $style_preg_resp = "style='display:block;'";
          else
            $style_preg_resp = "style='display:none;'";
          ?>
          <!-- Warning cuando la sumatoria del valor de las preguntas es MENOR a 100 -->
          <div id="muestra_wng_preg_<?php echo $tabCounter; ?>" <?php echo $style; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1"></div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-warning fade in">
                  <i class="fa-fw fa fa-warning"></i>
                  <strong><?php echo ObtenEtiqueta(1286); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1"></div>
            </div>
          </div>
          <!-- Error cuando la sumatoria del valor de las preguntas es MAYOR a 100 -->
          <div id="muestra_err_preg_<?php echo $tabCounter; ?>" style="display:none;">
            <div class="row">
              <div class="col-xs-1 col-sm-1"></div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1287); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1"></div>
            </div>
          </div>
          <!-- Error cuando el valor de las respuestas no tiene un maximo de 100 -->
          <div id="muestra_err_res_<?php echo $tabCounter; ?>" <?php echo $style_resp; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1">
              </div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1358); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1">
              </div>
            </div>
          </div>
          <!-- Error cuando la tab no tiene pregunta ni valor -->
          <div id="muestra_err_preg_val_<?php echo $tabCounter; ?>" <?php echo $style_preg_val; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1">
              </div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1359); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1">
              </div>
            </div>
          </div>
          <!-- Error cuando la tab no tiene respuestas registradas -->
          <div id="muestra_err_preg_res_<?php echo $tabCounter; ?>" <?php echo $style_preg_resp; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1">
              </div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times">
                  </i>
                  <strong>
                    <?php echo ObtenEtiqueta(1360); ?>
                  </strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1">
              </div>
            </div>
          </div>
        </div>
        <?php
        if ($fg_tipo_resp_[$tabCounter] == 'I') {
          $style_2 = "style='display: none;'";
          $style_3 = "style='display: block;'";
        } else {
          $style_2 = "style='display: block;'";
          $style_3 = "style='display: none;'";
        }
        ?>
        <div class="row hidden" id="error_preguntas_valores_<?php echo $tabCounter; ?>">
          <div class="col col-sm-12 col-md-12 col-lg-12">
            <i class="fa fa-warning txt-color-red"></i> <code><?php echo ObtenEtiqueta(1895); ?></code>
          </div>
        </div>
        <!-------------Next Row Answers----------------->
        <div id="img_based_2_<?php echo $tabCounter; ?>" <?php echo $style_2; ?>>
          <!------------------------------------------------------------->
          <div class="row">
            <div class="col col-xs-12 col-sm-3">
              <?php Forma_CampoTexto(ObtenEtiqueta(1204) . ' 1', False, "ds_resp_1_$tabCounter", $ds_resp_1_[$tabCounter], 255, 41, $ds_resp_1_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php
              $var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='" . ObtenEtiqueta(1283) . "'><i class='fa fa-info-circle' tabindex='10000'></i></a>&nbsp;&nbsp;&nbsp;";
              Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_1_$tabCounter", $ds_grade_1_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_1_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-3">
              <?php Forma_CampoTexto(ObtenEtiqueta(1204) . ' 2', False, "ds_resp_2_$tabCounter", $ds_resp_2_[$tabCounter], 255, 41, $ds_a_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_2_$tabCounter", $ds_grade_2_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_2_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
            </div>
          </div>
          <div class="row">
            <div class="col col-xs-12 col-sm-3">
              <?php Forma_CampoTexto(ObtenEtiqueta(1204) . ' 3', False, "ds_resp_3_$tabCounter", $ds_resp_3_[$tabCounter], 255, 41, $ds_a_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_3_$tabCounter", $ds_grade_3_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_3_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
            </div>
            <div class="col col-xs-12 col-sm-3">
              <?php Forma_CampoTexto(ObtenEtiqueta(1204) . ' 4', False, "ds_resp_4_$tabCounter", $ds_resp_4_[$tabCounter], 255, 41, $ds_a_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_4_$tabCounter", $ds_grade_4_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_4_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
            </div>
          </div>
        </div>
        <!------------END OF ANSWERS-------------->
      </div>
      <!-- END English Content-->
      <div class="tab-pane fade in " id="tab-quiz_esp<?php echo $tabCounter; ?>">
        <!-- START Spanish Content-->
        <div class="col col-xs-12 col-sm-5">
          <?php
          Forma_CampoTexto(ObtenEtiqueta(1200) . " <span id='NoPreg_esp_$tabCounter'>$tabCounter</span>", False, "ds_pregunta_esp_$tabCounter", $ds_pregunta_esp_[$tabCounter], 255, 63, $err_val_pregunta_x_[$tabCounter], False, '', True, '', '', "smart-form", 'left', 'col col-sm-12', 'col col-sm-12');
          ?>
        </div>
        <div class="col col-xs-12 col-sm-1">
        </div>
        <div class="col col-xs-12 col-sm-2">
          <div id="div_no_semana3_esp_<?php echo $tabCounter; ?>" class="row form-group ">
            <label class="col col-sm-12 control-label text-align-left">
              <strong><?php echo $var . ObtenEtiqueta(1255);
                      $tabCounter ?>:</strong>
            </label>
            <div class="col-sm-12">
              <div class="smart-form">
                <label class="input" id="error_msj3_esp_<?php echo $tabCounter; ?>">
                  <input class="form-control" id="valor_esp_<?php echo $tabCounter; ?>" name="valor_<?php echo $tabCounter; ?>" value="<?php echo $ds_quiz_[$tabCounter]; ?>" maxlength="3" size="12" type="text" onkeyup="Suma_val_preg_quiz(this.value); $('#muestra_valor_1').removeAttr('padding-left').css('padding-left', '20%'); Valida_Quiz_new_tab(<?php echo $tabCounter; ?>); " onKeyPress="return SoloNumeros(event);">
                </label>
                <?php
                if (!empty($err_val_preg_x_[$tabCounter]))
                  echo "<span class='help-block txt-color-red'><i class='fa fa-warning'></i>" . ObtenMensaje(3) . "</span>";
                ?>
              </div>
            </div>
          </div>
        </div>
        <?php
        if (!empty($err_val_preg_x_[$tabCounter])) {
          ?>
          <script>
            var tab = "<?php echo $tabCounter; ?>";
            $("#error_msj3_" + tab).addClass("state-error");
          </script>
        <?php
        } else {
          ?>
          <script>
            var tab = "<?php echo $tabCounter; ?>";
            $("#error_msj3_" + tab).removeClass("state-error");
          </script>
        <?php
        }
        ?>
        <div class="col col-xs-12 col-sm-2">
          <?php
          Forma_CampoOculto("q_remaining_$tabCounter", $q_remaining_[$tabCounter]);
          Forma_CampoTexto(ObtenEtiqueta(1213), False, "ds_quiz_$tabCounter", $valor_ini_preg[$tabCounter], 3, 16, $ds_quiz_err_[$tabCounter], False, '', True, 'disabled', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
          if ($q_remaining_[$tabCounter] != 0) {
            echo "<script>
                document.getElementById('ds_quiz_$tabCounter').value = document.getElementById('q_remaining_$tabCounter').value;
                </script>";
          }
          ?>
        </div>
        <div class="col col-xs-12 col-sm-2">
          <?php
          echo "<div id='div_ds_quiz_esp_1' class='row form-group '>
                <label class='col col-sm-12 control-label text-align-left'><strong>&nbsp;&nbsp;  </strong></label>
                <div class='col col-sm-12'><label class='input'><a href='javascript:MuestraPreview_$tabCounter($tabCounter);' class='btn btn-primary'  >" . ObtenEtiqueta(1208) . "</a></div>      
                </div>";
          ?>
        </div>
        <div class="row">
          <?php
          if ($valor_ini_preg == 0)
            $style = "style='display:none;'";
          elseif ($valor_ini_preg > 0)
            $style = "style='display:block;'";
          else
            $style = "style='display:none;'";
          # Estilo para mensaje: Una respuesta debe valer 100
          if (($tabCounter == $tabCounter) and ($err_valor_repuestas_[$tabCounter]))
            $style_resp = "style='display:block;'";
          else
            $style_resp = "style='display:none;'";

          # Estilo para mensaje: REvise valor de pregunta
          if (($tabCounter == $tabCounter) and ($err_val_preg_x_[$tabCounter]))
            $style_preg_val = "style='display:block;'";
          else
            $style_preg_val = "style='display:none;'";

          # Estilo para mensaje: Revise valor de respuestas
          if (($tabCounter == $tabCounter) and ($err_resp_preg_x_[$tabCounter]))
            $style_preg_resp = "style='display:block;'";
          else
            $style_preg_resp = "style='display:none;'";
          ?>
          <!-- Warning cuando la sumatoria del valor de las preguntas es MENOR a 100 -->
          <div id="muestra_wng_preg_esp_<?php echo $tabCounter; ?>" <?php echo $style; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1"></div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-warning fade in">
                  <i class="fa-fw fa fa-warning"></i>
                  <strong><?php echo ObtenEtiqueta(1286); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1"></div>
            </div>
          </div>
          <!-- Error cuando la sumatoria del valor de las preguntas es MAYOR a 100 -->
          <div id="muestra_err_preg_esp_<?php echo $tabCounter; ?>" style="display:none;">
            <div class="row">
              <div class="col-xs-1 col-sm-1"></div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1287); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1"></div>
            </div>
          </div>
          <!-- Error cuando el valor de las respuestas no tiene un maximo de 100 -->
          <div id="muestra_err_res_esp_<?php echo $tabCounter; ?>" <?php echo $style_resp; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1">
              </div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1358); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1">
              </div>
            </div>
          </div>
          <!-- Error cuando la tab no tiene pregunta ni valor -->
          <div id="muestra_err_preg_val_esp_<?php echo $tabCounter; ?>" <?php echo $style_preg_val; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1">
              </div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1359); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1">
              </div>
            </div>
          </div>
          <!-- Error cuando la tab no tiene respuestas registradas -->
          <div id="muestra_err_preg_res_esp_<?php echo $tabCounter; ?>" <?php echo $style_preg_resp; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1">
              </div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1360); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1">
              </div>
            </div>
          </div>
        </div>
        <?php
        if ($fg_tipo_resp_[$tabCounter] == 'I') {
          $style_2 = "style='display: none;'";
          $style_3 = "style='display: block;'";
        } else {
          $style_2 = "style='display: block;'";
          $style_3 = "style='display: none;'";
        }
        ?>
        <div class="row hidden" id="error_preguntas_valores_esp_<?php echo $tabCounter; ?>">
          <div class="col col-sm-12 col-md-12 col-lg-12">
            <i class="fa fa-warning txt-color-red"></i> <code><?php echo ObtenEtiqueta(1895); ?></code>
          </div>
        </div>
        <!-------------Next Row Answers----------------->
        <div id="img_based_2_esp_<?php echo $tabCounter; ?>" <?php echo $style_2; ?>>
          <!------------------------------------------------------------->
          <div class="row">
            <div class="col col-xs-12 col-sm-3">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1204) . ' 1', False, "ds_resp_esp_1_$tabCounter", $ds_resp_esp_1_[$tabCounter], 255, 41, $ds_resp_1_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php
              $var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='" . ObtenEtiqueta(1283) . "'><i class='fa fa-info-circle' tabindex='10000'></i></a>&nbsp;&nbsp;&nbsp;";
              Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_1_$tabCounter", $ds_grade_1_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_1_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-3">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1204) . ' 2', False, "ds_resp_esp_2_$tabCounter", $ds_resp_esp_2_[$tabCounter], 255, 41, $ds_a_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php
              Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_2_$tabCounter", $ds_grade_2_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_2_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col col-xs-12 col-sm-3">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1204) . ' 3', False, "ds_resp_esp_3_$tabCounter", $ds_resp_esp_3_[$tabCounter], 255, 41, $ds_a_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php
              Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_3_$tabCounter", $ds_grade_3_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_3_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>

            <div class="col col-xs-12 col-sm-3">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1204) . ' 4', False, "ds_resp_esp_4_$tabCounter", $ds_resp_esp_4_[$tabCounter], 255, 41, $ds_a_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php
              Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_4_$tabCounter", $ds_grade_4_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_4_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
          </div>
        </div>
        <!------------END OF ANSWERS-------------->
      </div>
      <!-- END Spanish Content-->
      <div class="tab-pane fade in " id="tab-quiz_fra<?php echo $tabCounter; ?>">
        <!-- START French Content-->
        <div class="col col-xs-12 col-sm-5">
          <?php
          Forma_CampoTexto(ObtenEtiqueta(1200) . " <span id='NoPreg_fra_$tabCounter'>$tabCounter</span>", False, "ds_pregunta_fra_$tabCounter", $ds_pregunta_fra_[$tabCounter], 255, 63, $err_val_pregunta_x_[$tabCounter], False, '', True, '', '', "smart-form", 'left', 'col col-sm-12', 'col col-sm-12');
          ?>
        </div>
        <div class="col col-xs-12 col-sm-1">
        </div>
        <div class="col col-xs-12 col-sm-2">
          <div id="div_no_semana3_fra_<?php echo $tabCounter; ?>" class="row form-group ">
            <label class="col col-sm-12 control-label text-align-left">
              <strong><?php echo $var . ObtenEtiqueta(1255);
                      $tabCounter ?>:</strong>
            </label>
            <div class="col-sm-12">
              <div class="smart-form">
                <label class="input" id="error_msj3_fra_<?php echo $tabCounter; ?>">
                  <input class="form-control" id="valor_fra_<?php echo $tabCounter; ?>" name="valor_<?php echo $tabCounter; ?>" value="<?php echo $ds_quiz_[$tabCounter]; ?>" maxlength="3" size="12" type="text" onkeyup="Suma_val_preg_quiz(this.value); $('#muestra_valor_1').removeAttr('padding-left').css('padding-left', '20%'); Valida_Quiz_new_tab(<?php echo $tabCounter; ?>); " onKeyPress="return SoloNumeros(event);">
                </label>
                <?php
                if (!empty($err_val_preg_x_[$tabCounter]))
                  echo "<span class='help-block txt-color-red'><i class='fa fa-warning'></i>" . ObtenMensaje(3) . "</span>";
                ?>
              </div>
            </div>
          </div>
        </div>
        <?php
        if (!empty($err_val_preg_x_[$tabCounter])) {
          ?>
          <script>
            var tab = "<?php echo $tabCounter; ?>";
            $("#error_msj3_" + tab).addClass("state-error");
          </script>
        <?php
        } else {
          ?>
          <script>
            var tab = "<?php echo $tabCounter; ?>";
            $("#error_msj3_" + tab).removeClass("state-error");
          </script>
        <?php
        }
        ?>
        <div class="col col-xs-12 col-sm-2">
          <?php
          Forma_CampoOculto("q_remaining_$tabCounter", $q_remaining_[$tabCounter]);
          Forma_CampoTexto(ObtenEtiqueta(1213), False, "ds_quiz_$tabCounter", $valor_ini_preg[$tabCounter], 3, 16, $ds_quiz_err_[$tabCounter], False, '', True, 'disabled', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
          if ($q_remaining_[$tabCounter] != 0) {
            echo "<script>
                document.getElementById('ds_quiz_$tabCounter').value = document.getElementById('q_remaining_$tabCounter').value;
                </script>";
          }
          ?>
        </div>
        <div class="col col-xs-12 col-sm-2">
          <?php
          echo "<div id='div_ds_quiz_fra_1' class='row form-group '>
                <label class='col col-sm-12 control-label text-align-left'><strong>&nbsp;&nbsp;  </strong></label>
                <div class='col col-sm-12'><label class='input'><a href='javascript:MuestraPreview_$tabCounter($tabCounter);' class='btn btn-primary'  >" . ObtenEtiqueta(1208) . "</a></div>      
                </div>";
          ?>
        </div>
        <div class="row">
          <?php
          if ($valor_ini_preg == 0)
            $style = "style='display:none;'";
          elseif ($valor_ini_preg > 0)
            $style = "style='display:block;'";
          else
            $style = "style='display:none;'";
          # Estilo para mensaje: Una respuesta debe valer 100
          if (($tabCounter == $tabCounter) and ($err_valor_repuestas_[$tabCounter]))
            $style_resp = "style='display:block;'";
          else
            $style_resp = "style='display:none;'";

          # Estilo para mensaje: REvise valor de pregunta
          if (($tabCounter == $tabCounter) and ($err_val_preg_x_[$tabCounter]))
            $style_preg_val = "style='display:block;'";
          else
            $style_preg_val = "style='display:none;'";

          # Estilo para mensaje: Revise valor de respuestas
          if (($tabCounter == $tabCounter) and ($err_resp_preg_x_[$tabCounter]))
            $style_preg_resp = "style='display:block;'";
          else
            $style_preg_resp = "style='display:none;'";
          ?>
          <!-- Warning cuando la sumatoria del valor de las preguntas es MENOR a 100 -->
          <div id="muestra_wng_preg_fra_<?php echo $tabCounter; ?>" <?php echo $style; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1"></div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-warning fade in">
                  <i class="fa-fw fa fa-warning"></i>
                  <strong><?php echo ObtenEtiqueta(1286); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1"></div>
            </div>
          </div>
          <!-- Error cuando la sumatoria del valor de las preguntas es MAYOR a 100 -->
          <div id="muestra_err_preg_fra_<?php echo $tabCounter; ?>" style="display:none;">
            <div class="row">
              <div class="col-xs-1 col-sm-1"></div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1287); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1"></div>
            </div>
          </div>
          <!-- Error cuando el valor de las respuestas no tiene un maximo de 100 -->
          <div id="muestra_err_res_fra_<?php echo $tabCounter; ?>" <?php echo $style_resp; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1">
              </div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1358); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1">
              </div>
            </div>
          </div>
          <!-- Error cuando la tab no tiene pregunta ni valor -->
          <div id="muestra_err_preg_val_fra_<?php echo $tabCounter; ?>" <?php echo $style_preg_val; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1">
              </div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1359); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1">
              </div>
            </div>
          </div>
          <!-- Error cuando la tab no tiene respuestas registradas -->
          <div id="muestra_err_preg_res_fra_<?php echo $tabCounter; ?>" <?php echo $style_preg_resp; ?>>
            <div class="row">
              <div class="col-xs-1 col-sm-1">
              </div>
              <div class="col-xs-10 col-sm-10">
                <div class="alert alert-danger fade in">
                  <i class="fa-fw fa fa-times"></i>
                  <strong><?php echo ObtenEtiqueta(1360); ?></strong>
                </div>
              </div>
              <div class="col-xs-1 col-sm-1">
              </div>
            </div>
          </div>
        </div>
        <?php
        if ($fg_tipo_resp_[$tabCounter] == 'I') {
          $style_2 = "style='display: none;'";
          $style_3 = "style='display: block;'";
        } else {
          $style_2 = "style='display: block;'";
          $style_3 = "style='display: none;'";
        }
        ?>
        <div class="row hidden" id="error_preguntas_valores_fra_<?php echo $tabCounter; ?>">
          <div class="col col-sm-12 col-md-12 col-lg-12">
            <i class="fa fa-warning txt-color-red"></i> <code><?php echo ObtenEtiqueta(1895); ?></code>
          </div>
        </div>
        <!-------------Next Row Answers----------------->
        <div id="img_based_2_fra_<?php echo $tabCounter; ?>" <?php echo $style_2; ?>>
          <!------------------------------------------------------------->
          <div class="row">
            <div class="col col-xs-12 col-sm-3">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1204) . ' 1', False, "ds_resp_fra_1_$tabCounter", $ds_resp_fra_1_[$tabCounter], 255, 41, $ds_resp_1_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php
              $var = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='" . ObtenEtiqueta(1283) . "'><i class='fa fa-info-circle' tabindex='10000'></i></a>&nbsp;&nbsp;&nbsp;";
              Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_1_$tabCounter", $ds_grade_1_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_1_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-3">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1204) . ' 2', False, "ds_resp_fra_2_$tabCounter", $ds_resp_fra_2_[$tabCounter], 255, 41, $ds_a_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php
              Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_2_$tabCounter", $ds_grade_2_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_2_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col col-xs-12 col-sm-3">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1204) . ' 3', False, "ds_resp_fra_3_$tabCounter", $ds_resp_fra_3_[$tabCounter], 255, 41, $ds_a_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php
              Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_3_$tabCounter", $ds_grade_3_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_3_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>

            <div class="col col-xs-12 col-sm-3">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1204) . ' 4', False, "ds_resp_fra_4_$tabCounter", $ds_resp_fra_4_[$tabCounter], 255, 41, $ds_a_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
            <div class="col col-xs-12 col-sm-1">
            </div>
            <div class="col col-xs-12 col-sm-2">
              <?php
              Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_4_$tabCounter", $ds_grade_4_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_4_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              ?>
            </div>
          </div>
        </div>
        <!------------END OF ANSWERS-------------->
      </div>
      <!-- END French Content-->
    </div>
    <!-- Se paso el </div> al final, es el penmultimo para cerrar el tab de pregunta general -->
    <!-- END QUIZ Lang Tabs -->
    <!---------Start of IMAGE Answers------------>
    <div id="img_based_3_<?php echo $tabCounter; ?>" <?php echo $style_3; ?>>
      <!---------------------------------------------->
      <div class="row">
        <div class="col col-xs-12 col-sm-4">
          <?php CargaImagenDropZone(' ' . ObtenEtiqueta(1204) . ' 1', "mydropzone_1_$tabCounter", "$tabCounter", $editar_[$tabCounter], $fl_quiz_pregunta_[$tabCounter], $fg_error, $ds_img_1_[$tabCounter], "$fg_tipo_img_[$tabCounter]", "Valida_Quiz_new_tab(" . $tabCounter . ");", $fg_tipo_resp_[$tabCounter]); ?>
        </div>
        <div class="col col-xs-12 col-sm-2">
          <?php Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_img_1_$tabCounter", $ds_grade_img_1_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_img_1_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
        </div>
        <div class="col col-xs-12 col-sm-4">
          <?php CargaImagenDropZone(' ' . ObtenEtiqueta(1204) . ' 2', "mydropzone_2_$tabCounter", "$tabCounter", $editar_[$tabCounter], $fl_quiz_pregunta_[$tabCounter], $fg_error, $ds_img_2_[$tabCounter], "$fg_tipo_img_[$tabCounter]", "Valida_Quiz_new_tab(" . $tabCounter . ");", $fg_tipo_resp_[$tabCounter]); ?>
        </div>
        <div class="col col-xs-12 col-sm-2">
          <?php Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_img_2_$tabCounter", $ds_grade_img_2_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_img_2_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');"  onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
        </div>
      </div>
      <div class="row">
        <div class="col col-xs-12 col-sm-4">
          <?php CargaImagenDropZone(' ' . ObtenEtiqueta(1204) . ' 3', "mydropzone_3_$tabCounter", "$tabCounter", $editar_[$tabCounter], $fl_quiz_pregunta_[$tabCounter], $fg_error, $ds_img_3_[$tabCounter], "$fg_tipo_img_[$tabCounter]", "Valida_Quiz_new_tab(" . $tabCounter . ");", $fg_tipo_resp_[$tabCounter]); ?>
        </div>
        <div class="col col-xs-12 col-sm-2">
          <?php Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_img_3_$tabCounter", $ds_grade_img_3_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_img_3_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
        </div>
        <div class="col col-xs-12 col-sm-4">
          <?php CargaImagenDropZone(' ' . ObtenEtiqueta(1204) . ' 4', "mydropzone_4_$tabCounter", "$tabCounter", $editar_[$tabCounter], $fl_quiz_pregunta_[$tabCounter], $fg_error, $ds_img_4_[$tabCounter], "$fg_tipo_img_[$tabCounter]", "Valida_Quiz_new_tab(" . $tabCounter . ");", $fg_tipo_resp_[$tabCounter]); ?>
        </div>
        <div class="col col-xs-12 col-sm-2">
          <?php Forma_CampoTexto($var . ObtenEtiqueta(1205), False, "ds_grade_img_4_$tabCounter", $ds_grade_img_4_[$tabCounter], 3, 10, $ds_a_email_err, False, '', True, 'onkeyup="Suma_val_resp_quiz(this.value, \'ds_grade_img_4_' . $tabCounter . '\'); Valida_Quiz_new_tab(' . $tabCounter . ');" onKeyPress="return SoloNumeros(event);"', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12'); ?>
        </div>
      </div>
    </div>
    <?php
    if (!empty($err_valor_repuestas_[$tabCounter])) {
      // $err_valor_repuestas_
      if ($fg_tipo_resp_[$tabCounter] == "T") {
        ?>
        <script>
          var tab = '<?php echo $tabCounter; ?>';
          $("#div_ds_grade_1_" + tab + " > div > label").addClass("state-error");
          $("#div_ds_grade_2_" + tab + " > div > label").addClass("state-error");
          $("#div_ds_grade_3_" + tab + " > div > label").addClass("state-error");
          $("#div_ds_grade_4_" + tab + " > div > label").addClass("state-error");
        </script>
      <?php
        } else {
          ?>
        <script>
          var tab = '<?php echo $tabCounter; ?>';
          $("#div_ds_grade_img_1_" + tab + " > div > label").addClass("state-error");
          $("#div_ds_grade_img_2_" + tab + " > div > label").addClass("state-error");
          $("#div_ds_grade_img_3_" + tab + " > div > label").addClass("state-error");
          $("#div_ds_grade_img_4_" + tab + " > div > label").addClass("state-error");
        </script>
      <?php
        }
      } else {
        ?>
      <script>
        var tab = '<?php echo $tabCounter; ?>';
        $("#div_ds_grade_img_1_" + tab + " > div > label").removeClass("state-error");
        $("#div_ds_grade_img_2_" + tab + " > div > label").removeClass("state-error");
        $("#div_ds_grade_img_3_" + tab + " > div > label").removeClass("state-error");
        $("#div_ds_grade_img_4_" + tab + " > div > label").removeClass("state-error");
        $("#div_ds_grade_1_" + tab + " > div > label").removeClass("state-error");
        $("#div_ds_grade_2_" + tab + " > div > label").removeClass("state-error");
        $("#div_ds_grade_3_" + tab + " > div > label").removeClass("state-error");
        $("#div_ds_grade_4_" + tab + " > div > label").removeClass("state-error");
      </script>
    <?php
    }
    ?>
    <script>
      function MuestraPreview_<?php echo $tabCounter; ?>(tabCounter) {
        // Verificamos tipo de pregunta
        var tipo = document.getElementById('fg_tipo_preg_' + tabCounter).value;
        // Verificamos el tipo de imagen
        var fg_tipo_img_prev = document.getElementById('fg_tipo_img_prev_' + tabCounter).value;
        // Control de numeracion
        var tabCounter = tabCounter;
        // Mostramos descripcion de pregunta y valor
        document.getElementById('ds_pregunta_prev_' + tabCounter).innerHTML = document.getElementById('ds_pregunta_' + tabCounter).value;
        document.getElementById('valor_preg_' + tabCounter).innerHTML = document.getElementById('valor_' + tabCounter).value;
        document.getElementById('nb_curso_prev_a').innerHTML = document.getElementById('ds_titulo').value;
        // Validamos el tipo de imagen
        var img_land = document.getElementById('img_land_' + tabCounter);
        var img_port = document.getElementById('img_port_' + tabCounter);
        // Mostramos contenido de acuerdo al tipo de imagen      
        if (fg_tipo_img_prev == 'L') {
          img_land.style.display = 'block';
          img_port.style.display = 'none';
        } else {
          img_land.style.display = 'none';
          img_port.style.display = 'block';
        }
        // Tipo respuesta texto
        if (tipo == 'T') {
          var resp_txt = document.getElementById('resp_txt_' + tabCounter);
          resp_txt.style.display = 'block';
          for (inc = 1; inc <= 4; inc++) {
            document.getElementById('Txt_' + inc + '_' + tabCounter).value = document.getElementById('ds_resp_' + inc + '_' + tabCounter).value;
          }
          document.getElementById('img_port_' + tabCounter).style.display = 'none';
          document.getElementById('img_land_' + tabCounter).style.display = 'none';
        }
        // Tipo respuesta imagen
        if (tipo == 'I') {
          for (inc = 1; inc <= 4; inc++) {
            document.getElementById('Img_' + inc + '_' + tabCounter).src = '<?php echo PATH_MODULOS; ?>/fame/uploads/' + document.getElementById('nb_img_prev_mydropzone_' + inc + '_' + tabCounter).value;
            document.getElementById('Img2_' + inc + '_' + tabCounter).src = '<?php echo PATH_MODULOS; ?>/fame/uploads/' + document.getElementById('nb_img_prev_mydropzone_' + inc + '_' + tabCounter).value;
          }
        }
        // Abrimos modal
        $("#PreviewQuiz_<?php echo $tabCounter; ?>").modal();
      }
    </script>
    <?php
    echo "
            <div class='modal fade' id='PreviewQuiz_$tabCounter' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'  data-keyboard='false' data-backdrop='static'>
            <div class='modal-dialog' style='width:80%; align:center;'>
            <div class='modal-content'>
            <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
            <h4 class='modal-title' id='myModalLabel'><i class='fa fa-warning'></i> Lesson: <strong><span id='nb_curso_prev_a'></span></strong> </h4>
            </div>
            <div class='modal-body' style='padding-bottom:0px;'>
            <div class='row'>
            <article class='col-sm-12 col-md-12 col-lg-12'>
            <div class='jarviswidget' id='wid-id-2' data-widget-editbutton='false' data-widget-deletebutton='false' style='margin: 0 0 15px;'>
            <div>
            <div class='jarviswidget-editbox'><!-- This area used as dropdown edit box --></div>
            <div class='widget-body fuelux'>
            <div class='wizard'>
            <ul class='steps'>";
    echo "<li data-target='#preg{$no_orden}' class='active' id='preg_{$no_orden}'>
                <span class='badge badge-info'>{$no_orden}</span>" . ObtenEtiqueta(1200) . " {$no_orden} (<span id='valor_preg_$tabCounter'></span> %)<span class='chevron'></span>
                </li>
                </ul>
                <div class='actions'>";
    echo "<style>
                  .hvr-shadow:hover{
                    opacity: 0.7;
                    filter: alpha(opacity=70); 
                  }
                </style>";
    echo "<div style='display:block;' >
                <button type='button' class='btn btn-sm btn-primary' data-dismiss='modal'><i class='fa fa-times-circle'></i>&nbsp;" . ObtenEtiqueta(74) . "</button>
                </div>
                </div>
                </div>";
    echo "<div class='step-content'>";
    echo "<div class='step-pane active' align='center'>";
    echo " <br/>
                <div id='resultado_$i'>";
    echo "<center><h3><strong><span id='ds_pregunta_prev_$tabCounter'></span></strong></h3></center>
                <div class='form-group' align='center'>";
    # Pregunta tipo Texto
    echo "<div id='resp_txt_$tabCounter' style='display:none;'>";
    for ($inc = 1; $inc <= 4; $inc++) {
      echo "<div class='row'>";
      echo "<div class='col-lg-4'></div>";
      echo "<div class='col-lg-4'>";
      echo "<input type='button' class='btn btn-primary btn-sm btn-block' id='Txt_$inc" . '_' . "$tabCounter' />";
      echo "</div>";
      echo "<div class='col-lg-4'></div>";
      echo "</div><p></p>";
    }
    echo "</div>";
    # Pregunta tipo Imagen Landscape
    echo "<div id='img_land_$tabCounter' style='display:none;'>";
    for ($inc = 1; $inc <= 4; $inc++) {
      if ($inc == 1) {
        echo "<div class='row'>";
        echo "<div class='col-lg-1'></div><div class='col-lg-10' style='letter-spacing: -5px;'>";
      }
      echo "<img src='' id='Img_$inc" . '_' . "$tabCounter' width='330' height='180' 
                  style=' width: 100%; max-width: 330px; height: 100%; max-height: 180px;' class='hvr-shadow'>  ";
      if ($inc == 4)
        echo "</div>
              </div>";
    }
    echo "</div>";
    // Pregunta tipo Imagen Portrait    
    echo "<div id='img_port_$tabCounter' style='display:none;'>";
    for ($inc = 1; $inc <= 4; $inc++) {
      if ($inc == 1) {
        echo "<div class='row'>";
        echo "<div class='col-lg-12' style='letter-spacing: -5px;'>";
      }
      echo "<img src='' id='Img2_$inc" . '_' . "$tabCounter' 
                  style=' width: 100%; max-width: 180px; height: 100%; max-height: 330px;' class='hvr-shadow'>  ";
      if ($inc == 4)
        echo "</div></div>";
    }
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>
                </div>
                </div>
                </article>
                </div>
                </div>
                <div class='modal-footer' ></div>
                </div>
                </div>
                </div>";
    # Regresa error de valiacion 
    // La sumatoria del valor de las preguntas es MAYOR a 100
    if ($err_sum_val_preg_max) {
      echo "<script>
            no_lec_2 = document.getElementById('ContTabCounterLimit').value;
            no_lec   = parseInt(no_lec_2) - parseInt(1);
          
            for(x=1; x<=no_lec; x++){
              document.getElementById('valor_' + x).style.backgroundColor = '#FFF';
              document.getElementById('valor_' + x).style.borderColor = '#953b39'; 
              document.getElementById('muestra_err_preg_' + x).style.display = 'block';
            } 
            </script>";
    }
    // La sumatoria del valor de las preguntas es MENOR a 100
    if ($err_sum_val_preg_min) {
      echo "<script>
            no_lec_2 = document.getElementById('ContTabCounterLimit').value;
            no_lec   = parseInt(no_lec_2) - parseInt(1);
          
            for(x=1; x<=no_lec; x++){
              document.getElementById('valor_' + x).style.backgroundColor = '#efe1b3';
              document.getElementById('valor_' + x).style.borderColor = '#dfb56c';
              document.getElementById('muestra_wng_preg_' + x).style.display = 'block';
            } 
            </script>";
    }
    ?>
  </div>
</div>
<script>
  $(document).ready(function() {
    $('#muestra_loading_quiz').modal('hide');
    var tabb = '<?php echo $tabCounter; ?>';
    for (var r = 2; r <= tabb; r++) {
      // Agregamos a los pesos un popover
      $("#div_no_semana3_" + r).attr('rel', 'popover-hover').attr('data-placement', 'top').attr('data-original-title', '<?php echo $warning; ?>').attr('data-content', '<?php echo  ObtenEtiqueta(1284); ?>');
      Valida_Quiz_new_tab(r);
    }
  });

  function Valida_Quiz_new_tab(ntab) {
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
    $('#fg_tipo_resp_' + ntab + ':checked').each(
      function() {
        type_respuesta = $(this).val();;
      }
    );
    // pregunta y valor
    var preg1 = $("#ds_pregunta_" + ntab).val();
    var vpreg1 = $("#valor_" + ntab).val();
    var rem_pre1 = $("#ds_quiz_" + ntab).val();
    // respuestas dependiento del tipo de la respuesta
    if (type_respuesta == 'T') {
      var resp1 = $('#ds_resp_1' + '_' + ntab).val();
      var vresp1 = $('#ds_grade_1' + '_' + ntab).val();
      var resp2 = $('#ds_resp_2' + '_' + ntab).val();
      var vresp2 = $('#ds_grade_2' + '_' + ntab).val();
      var resp3 = $('#ds_resp_3' + '_' + ntab).val();
      var vresp3 = $('#ds_grade_3' + '_' + ntab).val();
      var resp4 = $('#ds_resp_4' + '_' + ntab).val();
      var vresp4 = $('#ds_grade_4' + '_' + ntab).val();

    } else {
      var resp1 = $('#nb_img_prev_mydropzone_1' + '_' + ntab).val();
      var vresp1 = $('#ds_grade_img_1' + '_' + ntab).val();
      var resp2 = $('#nb_img_prev_mydropzone_2' + '_' + ntab).val();
      var vresp2 = $('#ds_grade_img_2' + '_' + ntab).val();
      var resp3 = $('#nb_img_prev_mydropzone_3' + '_' + ntab).val();
      var vresp3 = $('#ds_grade_img_3' + '_' + ntab).val();
      var resp4 = $('#nb_img_prev_mydropzone_4' + '_' + ntab).val();
      var vresp4 = $('#ds_grade_img_4' + '_' + ntab).val();
    }
    // En los pesos de las preguntas por lo menos debe exitir un 100
    // creamos un array con los valores
    var myArr = [vresp1, vresp2, vresp3, vresp4];
    // con este idicamos que hay porlo menos un 100 en los campos
    var pesos = myArr.includes('100');
    // alert(pesos);
    // Si el remaining del quiz no debera ser menos a cer
    if (remaining < 0)
      btn_save.addClass('disabled');
    else
      btn_save.removeClass('disabled');
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
      tot_vals_pregunts = parseFloat(tot_vals_pregunts) + parseFloat(vls_pre);
    }
    for (var q = 1; q <= valores_preguntas; q++) {
      if (tot_vals_pregunts == 100) {
        $('#div_no_semana3_' + ntab).removeClass('has-error');
        document.getElementById('valor_' + q).style.backgroundColor = '#FFF';
      } else {
        $('#div_no_semana3_' + ntab).removeClass('state-error').addClass('has-error');
        document.getElementById('valor_' + q).style.backgroundColor = '#FFF0F0';
      }
    }
    // Por lo menos debe existir un 100  en los pesos
    if (pesos == false) {
      for (var h = 1; h <= 4; h++) {
        // dependiendo del tipo del respuesta
        if (type_respuesta == 'T') {
          $('#div_ds_grade_' + h + '_' + ntab).removeClass('state-error').addClass('has-error');
          document.getElementById('ds_grade_' + h + '_' + ntab).style.backgroundColor = '#FFF0F0';
        } else {
          $('#div_ds_grade_img_' + h + '_' + ntab).removeClass('state-error').addClass('has-error');
          document.getElementById('ds_grade_img_' + h + '_' + ntab).style.backgroundColor = '#FFF0F0';
        }
      }
      // btn_save.addClass('disabled');
    } else {
      for (var h = 1; h <= 4; h++) {
        // dependiendo del tipo del respuesta
        if (type_respuesta == 'T') {
          $('#div_ds_grade_' + h + '_' + ntab).removeClass('row form-group input has-error');
          document.getElementById('ds_grade_' + h + '_' + ntab).style.backgroundColor = '#FFF';
        } else {
          $('#div_ds_grade_img_' + h + '_' + ntab).removeClass('row form-group input has-error');
          document.getElementById('ds_grade_img_' + h + '_' + ntab).style.backgroundColor = '#FFF';
        }
      }
    }
    if ((tquiz != '' && vquiz > 0) || (tquiz == '' && vquiz > 0) || (tquiz != '' && (vquiz == 0 || vquiz == ''))) {
      // Si los campos estan llenos y cumplen las condiciones activamos el boton
      if (tquiz != '' && vquiz > 0 && preg1 != '' && vpreg1 > 0 && resp1 != '' && resp2 != '' && resp3 != '' && resp4 != '' && remaining >= 0 && rem_pre1 >= 0 && pesos == true && tot_vals_pregunts == 100) {
        $("#error_t_v_quiz").hide();
        $('#div_nb_quiz').removeClass('has-error');
        document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
        $('#div_no_semana2').removeClass('has-error');
        document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
        $('#div_ds_pregunta_' + ntab).removeClass('has-error');
        document.getElementById('ds_pregunta_' + ntab).style.backgroundColor = '#FFF';
        $('#div_no_semana3_' + ntab).removeClass('has-error');
        document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF';
        for (var r = 1; r <= 4; r++) {
          if (type_respuesta == 'T') {
            $('#div_ds_resp_' + r + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_resp_' + r).style.backgroundColor = '#FFF';
            $('#div_ds_grade_' + r + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_grade_' + r + '_' + ntab).style.backgroundColor = '#FFF';
          } else {
            $('#mydropzone_' + r + '_' + ntab).removeClass('bg-color-red');
            $('#div_ds_grade_img_' + r + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_grade_img_' + r + '_' + ntab).style.backgroundColor = '#FFF';
          }
        }
        // habilitamos el boton
        btn_save.removeClass('disabled');
        // Quitamos color
        $("#tab_4").removeClass("txt-color-red");
        Valida_Quiz();
        $("#error_preguntas_valores_" + ntab).addClass('hidden');
      } else {
        $('#div_ds_pregunta_' + ntab).removeClass('state-error').addClass('has-error');
        document.getElementById('ds_pregunta_' + ntab).style.backgroundColor = '#FFF0F0';
        $('#div_no_semana3_' + ntab).removeClass('state-error').addClass('has-error');
        document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF0F0';
        // Titulo y valor del quiz llenos
        if (tquiz != '' && vquiz > 0) {
          if (preg1 == '' && vpreg1 <= 0 && resp1 == '' && resp2 == '' && resp3 == '' && resp4 == '')
            $("#cont_tab_quiz_" + ntab).click();
          $('#div_nb_quiz').removeClass('has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
          $('#div_no_semana2').removeClass('has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
          btn_save.addClass('disabled');
        }
        // Titulo del quiz lleno y valor vacio
        if (tquiz != '' && (vquiz == 0 || vquiz == '')) {
          $('#div_no_semana2').removeClass('state-error').addClass('has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
          btn_save.addClass('disabled');
        }
        // Valor del quiz lleno y titulo vacio
        if (tquiz == '' && vquiz > 0) {
          $('#div_nb_quiz').removeClass('state-error').addClass('has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
          btn_save.addClass('disabled');
        }
        // Si algunos campos estan llenos entonces no los marcara dependiento del tipo de respuesta
        if (preg1 != '') {
          document.getElementById('ds_pregunta_' + ntab).style.backgroundColor = '#FFF';
          $('#div_ds_pregunta_' + ntab).removeClass('has-error');
        }
        if ((vpreg1 >= 0 || vpreg1 <= 100)) {
          if (vpreg1 == 0) {
            $('#div_no_semana3_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            if (tot_vals_pregunts == 100) {
              $('#div_no_semana3_' + ntab).removeClass('has-error');
              document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('valor_' + ntab).style.borderColor = '#E1C555';
            } else {
              if (vpreg1 == 100) {
                $('#div_no_semana3_' + ntab).removeClass('has-error');
                document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF';
              } else {
                $('#div_no_semana3_' + ntab).click();
                $('#div_no_semana3_' + ntab).removeClass('state-error').addClass('has-error');
                document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF0F0';
              }
            }
          }
          $("#error_preguntas_valores").removeClass('hidden');
        } else {
          $('#div_no_semana3_' + ntab).removeClass('state-error').addClass('has-error');
          document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF0F0';
          $("#error_preguntas_valores_" + ntab).addClass('hidden');
        }
        if (tot_vals_pregunts < 100) {
          $('#div_no_semana3_' + ntab).click();
        }
        if (type_respuesta == 'T') {
          if (resp1 == '') {
            $('#div_ds_resp_1' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_resp_1' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_1' + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_resp_1' + '_' + ntab).style.backgroundColor = '#FFF';
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_1' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_1' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_1' + '_' + ntab).removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_1' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_1' + '_' + ntab).style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_1' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_1' + '_' + ntab).style.borderColor = '#E1C555';
            }
          }
          if (resp2 == '') {
            $('#div_ds_resp_2' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_resp_2' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_2' + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_resp_2' + '_' + ntab).style.backgroundColor = '#FFF';
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_2' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_2' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_2' + '_' + ntab).removeClass('has-error');
            if (vresp2 == 100 || pesos == true) {
              document.getElementById('ds_grade_2' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_2' + '_' + ntab).style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_2' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_2' + '_' + ntab).style.borderColor = '#E1C555';
            }
          }
          if (resp3 == '') {
            $('#div_ds_resp_3' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_resp_3' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_3' + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_resp_3' + '_' + ntab).style.backgroundColor = '#FFF';
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_3' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_3' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_3' + '_' + ntab).removeClass('has-error');
            if (vresp3 == 100 || pesos == true) {
              document.getElementById('ds_grade_3' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_3' + '_' + ntab).style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_3' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_3' + '_' + ntab).style.borderColor = '#E1C555';
            }
          }
          if (resp4 == '') {
            $('#div_ds_resp_4' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_resp_4' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_4' + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_resp_4' + '_' + ntab).style.backgroundColor = '#FFF';
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_4' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_4' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_4' + '_' + ntab).removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_4' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_4' + '_' + ntab).style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_4' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_4' + '_' + ntab).style.borderColor = '#E1C555';
            }
          }
        } else {
          if (resp1 == '') {
            $('#mydropzone_1' + '_' + ntab).addClass('bg-color-red');
          } else {
            $('#mydropzone_1' + '_' + ntab).removeClass('bg-color-red');
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_img_1' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_img_1' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_1' + '_' + ntab).removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_1' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_1' + '_' + ntab).style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_1' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_1' + '_' + ntab).style.borderColor = '#E1C555';
            }
          }
          if (resp2 == '') {
            $('#mydropzone_2' + '_' + ntab).addClass('bg-color-red');
          } else {
            $('#mydropzone_2' + '_' + ntab).removeClass('bg-color-red');
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_img_2' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_img_2' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_2' + '_' + ntab).removeClass('has-error');
            if (vresp2 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_2' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_2' + '_' + ntab).style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_2' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_2' + '_' + ntab).style.borderColor = '#E1C555';
            }
          }
          if (resp3 == '') {
            $('#mydropzone_3' + '_' + ntab).addClass('bg-color-red');
          } else {
            $('#mydropzone_3' + '_' + ntab).removeClass('bg-color-red');
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_img_3' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_img_3' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_3' + '_' + ntab).removeClass('has-error');
            if (vresp3 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_3' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_3' + '_' + ntab).style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_3' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_3').style.borderColor = '#E1C555';
            }
          }
          if (resp4 == '') {
            $('#mydropzone_4' + '_' + ntab).addClass('bg-color-red');
          } else {
            $('#mydropzone_4' + '_' + ntab).removeClass('bg-color-red');
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_img_4' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_img_4' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_4' + '_' + ntab).removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_4' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_4' + '_' + ntab).style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_4' + '_' + ntab).style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_4' + '_' + ntab).style.borderColor = '#E1C555';
            }
          }
        }
        if (pesos == true) {
          for (var m = 1; m <= 4; m++) {
            // Dependiendo del tipo del respuesta
            if (type_respuesta == 'T') {
              $('#div_ds_grade_' + m + '_' + ntab).removeClass('has-error');
              document.getElementById('ds_grade_' + m + '_' + ntab).style.backgroundColor = '#FFF';
            } else {
              $('#div_ds_grade_img_' + m + '_' + ntab).removeClass('has-error');
              document.getElementById('ds_grade_img_' + m + '_' + ntab).style.backgroundColor = '#FFF';
            }
          }
          if (resp1 == '' || resp2 == '' || resp3 == '' || resp4 == '')
            $("#error_preguntas_valores_" + ntab).removeClass('hidden');
        }
      }
    } else {
      // si todo esta en blanco      
      if (tquiz == '' && (vquiz == 0 || vquiz == '') && preg1 == '' && (vpreg1 == 0 || vpreg1 == '') &&
        resp1 == '' && resp2 == '' && resp3 == '' && resp4 == '' &&
        (vresp1 == 0 || vresp1 == '') && (vresp2 == 0 || vresp2 == '') && (vresp3 == 0 && vresp3 == '') && (vresp4 == 0 || vresp4 == '') && ntab == 1
      ) {
        $('#div_nb_quiz').removeClass('has-error');
        document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
        $('#div_no_semana2').removeClass('has-error');
        document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
        $('#div_ds_pregunta_' + ntab).removeClass('has-error');
        document.getElementById('ds_pregunta_' + ntab).style.backgroundColor = '#FFF';
        $('#div_no_semana3_' + ntab).removeClass('has-error');
        document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF';
        // Muestra mensaje de warning
        $("#error_t_v_quiz").hide();
        for (var j = 1; j <= 4; j++) {
          if (type_respuesta == 'T') {
            $('#div_ds_resp_' + j + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_resp_' + j + '_' + ntab).style.backgroundColor = '#FFF';
            $('#div_ds_grade_' + j + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_grade_' + j + '_' + ntab).style.backgroundColor = '#FFF';
          } else {
            $('#mydropzone_' + j + '_' + ntab).removeClass('bg-color-red');
            $('#div_ds_grade_img_' + j + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_grade_img_' + j + '_' + ntab).style.backgroundColor = '#FFF';
          }
        }
        // habilitamos boton
        btn_save.removeClass('disabled');
        // Quitamos color
        $("#tab_4").removeClass("txt-color-red");
        $("#error_preguntas_valores_" + ntab).addClass('hidden');
      } else {
        // alert('gabo');
        if (tquiz == '') {
          $('#div_nb_quiz').removeClass('state-error').addClass('has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
        } else {
          $('#div_nb_quiz').remove('has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
        }
        if (vquiz == 0 || vquiz == '') {
          $('#div_no_semana2').removeClass('state-error').addClass('has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
        }
        if (preg1 == '') {
          $('#div_ds_pregunta_' + ntab).removeClass('state-error').addClass('has-error');
          document.getElementById('ds_pregunta_' + ntab).style.backgroundColor = '#FFF0F0';
        } else {
          $('#div_ds_pregunta_' + ntab).removeClass('has-error');
          document.getElementById('ds_pregunta_' + ntab).style.backgroundColor = '#FFF';
          if (tquiz == '') {
            $('#div_nb_quiz').removeClass('state-error').addClass('has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
          }
          if (vquiz == 0 || vquiz == '') {
            $('#div_no_semana2').removeClass('state-error').addClass('has-error');
            document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
          }
          if (vpreg1 == 0 || vpreg1 == '') {
            $('#div_no_semana3_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF0F0';
          }
          for (var j = 1; j <= 4; j++) {
            if (type_respuesta == 'T') {
              if ($('#ds_resp_' + j + '_' + ntab).val() == '') {
                $('#div_ds_resp_' + j + '_' + ntab).removeClass('state-error').addClass('has-error');
                document.getElementById('ds_resp_' + j + '_' + ntab).style.backgroundColor = '#FFF0F0';
              }
              if ($('#ds_grade_' + j + '_' + ntab).val() == 0 || $('#ds_grade_' + j + '_' + ntab).val() == '') {
                $('#div_ds_grade_' + j + '_' + ntab).removeClass('state-error').addClass('has-error');
                document.getElementById('ds_grade_' + j + '_' + ntab).style.backgroundColor = '#FFF0F0';
              }
            } else {
              if ($('#nb_img_prev_mydropzone_' + j + '_' + ntab).val() == '') {
                $('#mydropzone_' + j + '_' + ntab).addClass('bg-color-red');
              }
              if ($('#ds_grade_img_' + j + '_' + ntab).val() == 0 || $('#ds_grade_img_' + j + '_' + ntab).val() == '') {
                $('#div_ds_grade_img_' + j + '_' + ntab).removeClass('state-error').addClass('has-error');
                document.getElementById('ds_grade_img_' + j + '_' + ntab).style.backgroundColor = '#FFF0F0';
              }
            }
          }
          btn_save.addClass('disabled');
        }
        if (vpreg1 == 0 || tot_vals_pregunts < 100) {
          $('#div_no_semana3_' + ntab).removeClass('state-error').addClass('has-error');
          document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF0F0';
        } else {
          if (tquiz == '') {
            $('#div_nb_quiz').removeClass('state-error').addClass('has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
          }
          if (vquiz == 0 || vquiz == '') {
            $('#div_no_semana2').removeClass('state-error').addClass('has-error');
            document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
          }
          if (preg1 == '') {
            $('#div_ds_pregunta_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_pregunta_' + ntab).style.backgroundColor = '#FFF0F0';
          }
          $('#div_no_semana3_' + ntab).removeClass('has-error');
          document.getElementById('valor_' + ntab).style.backgroundColor = '#FFF';
          for (var j = 1; j <= 4; j++) {
            if (type_respuesta == 'T') {
              if ($('#ds_resp_' + j + '_' + ntab).val() == '') {
                $('#div_ds_resp_' + j + '_' + ntab).removeClass('state-error').addClass('has-error');
                document.getElementById('ds_resp_' + j + '_' + ntab).style.backgroundColor = '#FFF0F0';
              }
              if ($('#ds_grade_' + j + '_' + ntab).val() == 0 || $('#ds_grade_' + j + '_' + ntab).val() == '') {
                $('#div_ds_grade_' + j + '_' + ntab).removeClass('state-error').addClass('has-error');
                document.getElementById('ds_grade_' + j + '_' + ntab).style.backgroundColor = '#FFF0F0';
              }
            } else {
              if ($('#nb_img_prev_mydropzone_' + j + '_' + ntab).val() == '') {
                $('#mydropzone_' + j).addClass('bg-color-red');
              }
              if ($('#ds_grade_img_' + j + '_' + ntab).val() == 0 || $('#ds_grade_img_' + j + '_' + ntab).val() == '') {
                $('#div_ds_grade_img_' + j + '_' + ntab).removeClass('state-error').addClass('has-error');
                document.getElementById('ds_grade_img_' + j + '_' + ntab).style.backgroundColor = '#FFF0F0';
              }
            }
          }
          btn_save.addClass('disabled');
        }
        if (type_respuesta == 'T') {
          if (resp1 == '') {
            $('#div_ds_resp_1' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_resp_1' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_1' + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_resp_1' + '_' + ntab).style.backgroundColor = '#FFF';
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_1' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_1' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          }
          if (resp2 == '') {
            $('#div_ds_resp_2' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_resp_2' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_2' + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_resp_2' + '_' + ntab).style.backgroundColor = '#FFF';
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_2' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_2' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          }
          if (resp3 == '') {
            $('#div_ds_resp_3' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_resp_3' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_3' + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_resp_3' + '_' + ntab).style.backgroundColor = '#FFF';
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_3' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_3' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          }
          if (resp4 == '') {
            $('#div_ds_resp_4' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_resp_4' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_4' + '_' + ntab).removeClass('has-error');
            document.getElementById('ds_resp_4' + '_' + ntab).style.backgroundColor = '#FFF';
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_4' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_4' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          }
        } else {
          if (resp1 == '') {
            $('#mydropzone_1' + '_' + ntab).addClass('bg-color-red');
          } else {
            $('#mydropzone_1' + '_' + ntab).removeClass('bg-color-red');
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_img_1' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_img_1' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          }
          if (resp2 == '') {
            $('#mydropzone_2' + '_' + ntab).addClass('bg-color-red');
          } else {
            $('#mydropzone_2' + '_' + ntab).removeClass('bg-color-red');
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_img_2' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_img_2' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          }
          if (resp3 == '') {
            $('#mydropzone_3' + '_' + ntab).addClass('bg-color-red');
          } else {
            $('#mydropzone_3' + '_' + ntab).removeClass('bg-color-red');
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_img_3' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_img_3' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          }
          if (resp4 == '') {
            $('#mydropzone_4' + '_' + ntab).addClass('bg-color-red');
          } else {
            $('#mydropzone_4' + '_' + ntab).removeClass('bg-color-red');
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_img_4' + '_' + ntab).removeClass('state-error').addClass('has-error');
            document.getElementById('ds_grade_img_4' + '_' + ntab).style.backgroundColor = '#FFF0F0';
          }
        }
        if (pesos == true) {
          for (var m = 1; m <= 4; m++) {
            // Dependiendo del tipo del respuesta
            if (type_respuesta == 'T') {
              $('#div_ds_grade_' + m + '_' + ntab).removeClass('has-error');
              document.getElementById('ds_grade_' + m + '_' + ntab).style.backgroundColor = '#FFF';
            } else {
              $('#div_ds_grade_img_' + m + '_' + ntab).removeClass('has-error');
              document.getElementById('ds_grade_img_' + m + '_' + ntab).style.backgroundColor = '#FFF';
            }
          }
          if (resp1 == '' || resp2 == '' || resp3 == '' || resp4 == '')
            $("#error_preguntas_valores_" + ntab).removeClass('hidden');
        }
        // Deshabilitamos el boton
        btn_save.addClass('disabled');
      }
    }
    // Valida_Quiz();
  }
  var ntab = '<?php echo $tabCounter; ?>';
  $("#nb_quiz").keypress(function() {
    Valida_Quiz_new_tab(ntab);
  });
  $("#ds_pregunta_" + ntab).keypress(function() {
    Valida_Quiz_new_tab(ntab);
  })
  for (var k = 1; k <= 4; k++) {
    $("#ds_resp_" + k + "_" + ntab).keypress(function() {
      Valida_Quiz_new_tab(ntab);
    });
    // Agregamos a los pesos un popover
    $("#ds_grade_" + k + "_" + ntab).attr('rel', 'popover-hover').attr('data-placement', 'top').attr('data-original-title', '<?php echo $warning; ?>').attr('data-content', '<?php echo ObtenEtiqueta(1896); ?>');
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
      tot_vals_pregunts = parseFloat(tot_vals_pregunts) + parseFloat(vls_pre);
    }
    for (var q = 1; q <= valores_preguntas; q++) {
      if (tot_vals_pregunts == 100 || vpreg1 == 100) {
        $('#div_no_semana3').removeClass('has-error');
        document.getElementById('valor_' + q).style.backgroundColor = '#FFF';
      } else {
        $('#div_no_semana3').removeClass('state-error').addClass('row has-error');
        document.getElementById('valor_' + q).style.backgroundColor = '#FFF0F0';
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
      if (tquiz != '' && vquiz > 0 && preg1 != '' && vpreg1 > 0 && resp1 != '' && resp2 != '' && resp3 != '' && resp4 != '' && remaining >= 0 && rem_pre1 >= 0 && pesos == true && tot_vals_pregunts == 100 || vpreg1 == 100) {
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
          if (preg1 == '' && vpreg1 <= 0 && resp1 == '' && resp2 == '' && resp3 == '' && resp4 == '')
            $("#cont_tab_quiz_1").click();
          $('#div_nb_quiz').removeClass('has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
          $('#div_no_semana2').removeClass('has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
          btn_save.addClass('disabled');
        }
        // Titulo del quiz lleno y valor vacio
        if (tquiz != '' && (vquiz == 0 || vquiz == '') && (remaining < 0 || remaining == 100)) {
          $('#div_no_semana2').click();
          $('#div_no_semana2').removeClass('state-error').addClass('input has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
          btn_save.addClass('disabled');
        }
        // Valor del quiz lleno y titulo vacio
        if (tquiz == '' && vquiz > 0) {
          $('#div_nb_quiz').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
          btn_save.addClass('disabled');
        }
        // Si algunos campos estan llenos entonces no los marcara dependiento del tipo de respuesta
        if (preg1 != '') {
          if (vpreg1 <= 0 || vpreg1 == '')
            $("#div_no_semana3").click();
          $('#div_ds_pregunta_1').removeClass('has-error');
          document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF';
        }
        if (vpreg1 > 0 || vpreg1 == 100 || tot_vals_pregunts == 100) {
          $('#div_no_semana3').removeClass('has-error');
          document.getElementById('valor_1').style.backgroundColor = '#FFF';
          $("#error_preguntas_valores").removeClass('hidden');
        } else {
          $("#error_preguntas_valores").addClass('hidden');
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
            $('#div_ds_grade_4').removeClass('state-error').addClass('form-group has-error');
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
            $('#div_ds_grade_img_3').removeClass('state-error').addClass('form-grouphas-error');
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
        if (vpreg1 == 0 || tot_vals_pregunts < 100) {
          $('#div_no_semana3').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('valor_1').style.backgroundColor = '#FFF0F0';
        } else {
          if (tquiz == '') {
            $('#div_nb_quiz').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
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
</script>