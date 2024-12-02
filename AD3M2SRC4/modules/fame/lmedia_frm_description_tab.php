<div class="tab-pane fade in active" id="description">
  <!-- UMP  UMP START WIDGET BODY -->
  <div class="widget-body">
    <ul id="myTabDescription" class="nav nav-tabs bordered">
      <li class="active">
        <a id="mytabDesc1" href="#description_eng" data-toggle="tab">
          English
        </a>
      </li>
      <li class="">
        <a id="mytabDesc2" href="#description_esp" data-toggle="tab">
          Spanish
        </a>
      </li>
      <li class="">
        <a id="mytabDesc3" href="#description_fra" data-toggle="tab">
          French
        </a>
      </li>
    </ul>
    <div id="myTabDescCont" class="tab-content padding-10 no-border">
      <!--  UMP START English Content-->
      <div class="tab-pane fade in active" id="description_eng">
        <div class="row">
          <div class="col-sm-1"></div>
          <div class="col-xs-6 col-sm-7">
            <?php
            Forma_Espacio();
            if (empty($clave))
              $script_2 = "onchange='val_tot_quiz(); val_tot_rubric(); HabilitaCampos();'";
            else
              $script_2 = "";

            $Query = "SELECT CONCAT(nb_programa,' (',contador,' " . ObtenEtiqueta(1242) . ")'), fl_programa_sp
                      FROM (SELECT c.fl_programa_sp, c.nb_programa AS nb_programa, k.no_semanas AS no_semanas, (SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = c.fl_programa_sp) contador 
                      FROM k_programa_detalle_sp k, c_programa_sp c WHERE k.fl_programa_sp = c.fl_programa_sp ORDER BY c.no_orden )AS principal 
                      WHERE 1=1 ORDER BY nb_programa ASC ";
            Forma_CampoSelectBD(ObtenEtiqueta(380), True, 'fl_programa', $Query, $fl_programa, $fl_programa_err, True, "onclick='val_lesson();' {$script_2} ", 'right', 'col col-md-4', 'col col-md-8', 'unico');
            Forma_Espacio();
            ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2"></div>
          <div class="col-xs-6 col-sm-4">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(375), True, 'no_grado', $no_grado, 3, 12, $no_grado_err, '', '', True, "onkeyup='val_lesson();' $val_camp_obl_1 $disabled_det");
            ?>
          </div>
          <div class="col-xs-6 col-sm-3">
            <div id="div_no_semana" class="row form-group">
              <label class="col-sm-4 control-label text-align-right">
                <strong>* <?php echo ObtenEtiqueta(1250); ?>:</strong>
              </label>
              <div class="col-sm-4">
                <div class="smart-form">
                  <label class="input" id="error_msj">
                    <input class="form-control" id="no_semana" name="no_semana" value="<?php echo $no_semana; ?>" maxlength="3" size="12" type="text" onkeyup='val_lesson();' <?php echo $val_camp_obl_2; ?><?php echo $disabled_det; ?>>
                  </label>
                </div>
              </div>
              <div class='row' id="muestra_msj" style='display: none;'>
                <div class='col-sm-12 col-md-12'>
                  <div style='color: #A90329; font-zise:11px;'>
                    <b> <?php echo ObtenEtiqueta(1288); ?> </b>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3"></div>
        </div>
        <div class="row">
          <div class="col-sm-2">
          </div>
          <div class="col-xs-6 col-sm-4">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(385), True, 'ds_titulo', $ds_titulo, 150, 48, $ds_titulo_err, '', '', True, "$val_camp_obl_3 $disabled_det");
            ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2">
          </div>
          <div class="col-xs-6 col-sm-4">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(1297), True, 'ds_learning', $ds_learning, 150, 48, $ds_learning_err, '', '', True, "$val_camp_obl_4 $disabled_det");
            ?>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12">
            <?php
            Forma_CampoTinyMCE(ObtenEtiqueta(391), True, 'ds_leccion', $ds_leccion, 48, 20, $ds_leccion_err);
            ?>
          </div>
        </div>
      </div>
      <!-- UMP END English Content-->
      <!--  UMP START Spanish Content-->
      <div class="tab-pane fade in " id="description_esp">
        <div class="row">
          <div class="col-sm-1"></div>
          <div class="col-xs-6 col-sm-7">
            <?php
            Forma_Espacio();
            if (empty($clave))
              $script_2 = "onchange='val_tot_quiz(); val_tot_rubric(); HabilitaCampos();'";
            else
              $script_2 = "";

            $Query  = "SELECT CONCAT(nb_programa_esp,' (',contador,' " . ObtenEtiqueta(1242) . ")'), fl_programa_sp
                        FROM (SELECT c.fl_programa_sp, c.nb_programa_esp AS nb_programa_esp, k.no_semanas AS no_semanas, (SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = c.fl_programa_sp) contador 
                            FROM k_programa_detalle_sp k, c_programa_sp c WHERE k.fl_programa_sp = c.fl_programa_sp ORDER BY c.no_orden 
                            )AS principal 
                        WHERE 1=1 ORDER BY nb_programa_esp ASC ";
            Forma_CampoSelectBD(ObtenEtiqueta(380), True, 'fl_programa_esp', $Query, $fl_programa, $fl_programa_err, True, "onclick='val_lesson();' {$script_2} ", 'right', 'col col-md-4', 'col col-md-8', 'unico');
            Forma_Espacio();
            ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2"></div>
          <div class="col-xs-6 col-sm-4">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(375), True, 'no_grado_esp', $no_grado, 3, 12, $no_grado_err, '', '', True, "onkeyup='val_lesson();' $val_camp_obl_1 $disabled_det");
            ?>
          </div>
          <div class="col-xs-6 col-sm-3">
            <div id="div_no_semana_esp" class="row form-group">
              <label class="col-sm-4 control-label text-align-right">
                <strong>* <?php echo ObtenEtiqueta(1250); ?>:</strong>
              </label>
              <div class="col-sm-4">
                <div class="smart-form">
                  <label class="input" id="error_msj_esp">
                    <input class="form-control" id="no_semana_esp" name="no_semana_esp" value="<?php echo $no_semana; ?>" maxlength="3" size="12" type="text" onkeyup='val_lesson();' <?php echo $val_camp_obl_2; ?><?php echo $disabled_det; ?>>
                  </label>
                </div>
              </div>
              <div class='row' id="muestra_msj_esp" style='display: none;'>
                <div class='col-sm-12 col-md-12'>
                  <div style='color: #A90329; font-zise:11px;'>
                    <b> <?php echo ObtenEtiqueta(1288); ?> </b>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3"></div>
        </div>
        <div class="row">
          <div class="col-sm-2">
          </div>
          <div class="col-xs-6 col-sm-4">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(385), True, 'ds_titulo_esp', ($ds_titulo_esp??NULL), 150, 48, $ds_titulo_err, '', '', True, "$val_camp_obl_3 $disabled_det");
            ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2">
          </div>
          <div class="col-xs-6 col-sm-4">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(1297), True, 'ds_learning_esp', ($ds_learning_esp??NULL), 150, 48, $ds_learning_err, '', '', True, "$val_camp_obl_4 $disabled_det");
            ?>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12">
            <?php
            Forma_CampoTinyMCE(ObtenEtiqueta(391), True, 'ds_leccion_esp', ($ds_leccion_esp??NULL), 48, 20, $ds_leccion_err);
            ?>
          </div>
        </div>
      </div>
      <!-- UMP END Spanish Content-->
      <!--  UMP START French Content-->
      <div class="tab-pane fade in " id="description_fra">
        <div class="row">
          <div class="col-sm-1"></div>
          <div class="col-xs-6 col-sm-7">
            <?php
            Forma_Espacio();
            if (empty($clave))
              $script_2 = "onchange='val_tot_quiz(); val_tot_rubric(); HabilitaCampos();'";
            else
              $script_2 = "";

            $Query  = "SELECT CONCAT(nb_programa_fra,' (',contador,' " . ObtenEtiqueta(1242) . ")'), fl_programa_sp
                        FROM (SELECT c.fl_programa_sp, c.nb_programa_fra AS nb_programa_fra, k.no_semanas AS no_semanas, (SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = c.fl_programa_sp) contador 
                            FROM k_programa_detalle_sp k, c_programa_sp c WHERE k.fl_programa_sp = c.fl_programa_sp ORDER BY c.no_orden 
                            )AS principal 
                        WHERE 1=1 ORDER BY nb_programa_fra ASC ";
            Forma_CampoSelectBD(ObtenEtiqueta(380), True, 'fl_programa_fra', $Query, $fl_programa, $fl_programa_err, True, "onclick='val_lesson();' {$script_2} ", 'right', 'col col-md-4', 'col col-md-8', 'unico');
            Forma_Espacio();
            ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2"></div>
          <div class="col-xs-6 col-sm-4">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(375), True, 'no_grado_fra', $no_grado, 3, 12, $no_grado_err, '', '', True, "onkeyup='val_lesson();' $val_camp_obl_1 $disabled_det");
            ?>
          </div>
          <div class="col-xs-6 col-sm-3">
            <div id="div_no_semana_fra" class="row form-group">
              <label class="col-sm-4 control-label text-align-right">
                <strong>* <?php echo ObtenEtiqueta(1250); ?>:</strong>
              </label>
              <div class="col-sm-4">
                <div class="smart-form">
                  <label class="input" id="error_msj_fra">
                    <input class="form-control" id="no_semana_fra" name="no_semana_fra" value="<?php echo $no_semana; ?>" maxlength="3" size="12" type="text" onkeyup='val_lesson();' <?php echo $val_camp_obl_2; ?><?php echo $disabled_det; ?>>
                  </label>
                </div>
              </div>
              <div class='row' id="muestra_msj_fra" style='display: none;'>
                <div class='col-sm-12 col-md-12'>
                  <div style='color: #A90329; font-zise:11px;'>
                    <b> <?php echo ObtenEtiqueta(1288); ?> </b>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3"></div>
        </div>
        <div class="row">
          <div class="col-sm-2">
          </div>
          <div class="col-xs-6 col-sm-4">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(385), True, 'ds_titulo_fra', ($ds_titulo_fra??NULL), 150, 48, $ds_titulo_err, '', '', True, "$val_camp_obl_3 $disabled_det");
            ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2">
          </div>
          <div class="col-xs-6 col-sm-4">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(1297), True, 'ds_learning_fra', ($ds_learning_fra??NULL), 150, 48, $ds_learning_err, '', '', True, "$val_camp_obl_4 $disabled_det");
            ?>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12">
            <?php
            Forma_CampoTinyMCE(ObtenEtiqueta(391), True, 'ds_leccion_fra', ($ds_leccion_fra??NULL), 48, 20, $ds_leccion_err);
            ?>
          </div>
        </div>
      </div>
      <!-- UMP END French Content-->
      <!-- UMP END WIDGET BODY -->
    </div>
  </div>
</div>
<!-- UMP: Funcion que solo acepta numeros, se utiliza para que el campo de texto solo acepte numeros -->
<script>
  function SoloNumeros(evt) {
    if (window.event) { //asignamos el valor de la tecla a keynum
      keynum = evt.keyCode; //IE
    } else {
      keynum = evt.whUMP; //FF
    }
    //comprobamos si se encuentra en el rango numerico y que teclas no recibira.
    if ((keynum > 47 && keynum < 58) || keynum == 8 || keynum == 13 || keynum == 6) {
      return true;
    } else {
      return false;
    }
  }

  // UMP: Funcion que valida solo el rango de numeros de 0 - 100
  function Suma_val_resp_quiz(valor, name) {
    if (valor > 100)
      document.getElementById(name).value = 100;
    if (valor < 0)
      document.getElementById(name).value = 0;
  }

  // UMP: Funcion que valida el numero de sesion repecto a la leccion
  function val_lesson() {
    var no_grado = document.getElementById('no_grado').value;
    var no_semana = document.getElementById('no_semana').value;
    var fl_programa = document.getElementById('fl_programa').value;
    $.ajax({
      type: 'POST',
      url: 'valida_leccion.php',
      async: false,
      data: 'no_grado=' + no_grado +
        '&no_semana=' + no_semana +
        '&fl_programa=' + fl_programa,
      success: function(data) {
        $('#muestra_validacion').html(data);
        if (data == 1) {
          $('#error_msj').removeClass('input').addClass('input state-error');
          document.getElementById('muestra_msj').style.display = 'block';
          document.getElementById("no_valor_quiz").style.color = "red";
        } else {
          $('#error_msj').removeClass('input state-error').addClass('input');
          document.getElementById('muestra_msj').style.display = 'none';
          document.getElementById('no_valor_quiz').style.backgroundColor = '#dfb56c';
          document.getElementById('no_valor_quiz').style.borderColor = '#dfb56c';
        }
      }
    });
  }

  // UMP:
  function val_tot_quiz() {
    var fl_programa = document.getElementById('fl_programa').value;
    $.ajax({
      type: 'POST',
      url: 'valida_leccion_valor.php',
      async: false,
      data: 'fl_programa=' + fl_programa +
        '&rubric=0',
      success: function(data) {
        $('#muestra_valor_quiz').html(data);
        if (data == '')
          data = 0;
        var resta = 100 - parseInt(data);
        document.getElementById('no_valor_quiz').value = 0;
        document.getElementById('ds_course_1').value = resta;
        document.getElementById('val_sum_org').value = resta;
        // if(data == 1){
        // $('#error_msj').removeClass('input').addClass('input state-error');
        // document.getElementById('muestra_msj').style.display = 'block';
        // }else{
        // $('#error_msj').removeClass('input state-error').addClass('input');
        // document.getElementById('muestra_msj').style.display = 'none';
        // }
      }
    });
  }

  // UMP: Valida total de rubric
  function val_tot_rubric() {
    var fl_programa = document.getElementById('fl_programa').value;
    $.ajax({
      type: 'POST',
      url: 'valida_leccion_valor.php',
      async: false,
      data: 'fl_programa=' + fl_programa +
        '&rubric=1',
      success: function(data) {
        if (data == '')
          data = 0;
        var resta = 100 - parseInt(data);
        document.getElementById('no_ter_co').value = resta;
      }
    });
  }

  // Funcion para validar capos obligatorios
  function ValidaCamposObligatorios(campo_actual, valor) {
    if (valor) {
      $('#div_' + campo_actual).removeClass('row form-group has-error').addClass('row form-group ');
      document.getElementById(campo_actual).style.backgroundColor = '#FFF';
    }
  }

  function HabilitaCampos() {
    document.getElementById("no_grado").disabled = false;
    document.getElementById("no_semana").disabled = false;
    document.getElementById("ds_titulo").disabled = false;
    document.getElementById("ds_learning").disabled = false;
  }
</script>
