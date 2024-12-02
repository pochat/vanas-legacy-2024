<!-- Spanish -->
<div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiquetaLang(1298, 1); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiquetaLang(1299, 1); ?></span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_programa_esp', $ds_programa_esp, 50, 20, !empty($ds_programa_esp_err)?$ds_programa_esp_err:NULL);?>
  </div>
  </div>

  <div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiquetaLang(1300, 1); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiquetaLang(1301, 1); ?></span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_learning_esp', $ds_learning_esp, 50, 20, !empty($ds_learning_esp_err)?$ds_learning_esp_err:NULL);?>
  </div>
  </div>

  <div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiquetaLang(1302, 1); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;">
        <?php echo ObtenEtiquetaLang(1303, 1); ?>
        </span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_metodo_esp', $ds_metodo_esp, 50, 20, !empty($ds_metodo_esp_err)?$ds_metodo_esp_err:NULL);?>
  </div>
  </div>

  <div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiquetaLang(1304, 1); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;">
        <?php echo ObtenEtiquetaLang(1305, 1); ?>
        </span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_requerimiento_esp', $ds_requerimiento_esp, 50, 20, !empty($ds_requerimiento_esp_err)?$ds_requerimiento_esp_err:NULL);?>
  </div>
</div>
<!-- End Spanish -->