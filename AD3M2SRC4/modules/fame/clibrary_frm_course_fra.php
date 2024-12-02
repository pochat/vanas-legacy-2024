<!-- Spanish -->
<div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiquetaLang(1298, 3); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiquetaLang(1299, 3); ?></span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_programa_fra', $ds_programa_fra, 50, 20, !empty($ds_programa_fra_err)?$ds_programa_fra_err:NULL);?>
  </div>
  </div>

  <div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiquetaLang(1300, 3); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiquetaLang(1301, 3); ?></span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_learning_fra', $ds_learning_fra, 50, 20, !empty($ds_learning_fra_err)?$ds_learning_fra_err:NULL);?>
  </div>
  </div>

  <div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiquetaLang(1302, 3); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;">
        <?php echo ObtenEtiquetaLang(1303, 3); ?>
        </span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_metodo_fra', $ds_metodo_fra, 50, 20, !empty($ds_metodo_fra_err)?$ds_metodo_fra_err:NULL);?>
  </div>
  </div>

  <div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiquetaLang(1304, 3); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;">
        <?php echo ObtenEtiquetaLang(1305, 3); ?>
        </span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_requerimiento_fra', $ds_requerimiento_fra, 50, 20, !empty($ds_requerimiento_fra_err)?$ds_requerimiento_fra_err:NULL);?>
  </div>
</div>
<!-- End Spanish -->