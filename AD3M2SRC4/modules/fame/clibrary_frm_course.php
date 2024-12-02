<div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiqueta(1298); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiqueta(1299); ?></span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_programa', $ds_programa, 50, 20, $ds_programa_err);?>
  </div>
</div>
<div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiqueta(1300); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiqueta(1301); ?></span></dd>
      </dl>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_learning', $ds_learning, 50, 20, $ds_learning_err);?>
  </div>
</div>
<div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiqueta(1302); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;">
        <?php echo ObtenEtiqueta(1303); ?>
        </span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_metodo', $ds_metodo, 50, 20, $ds_metodo_err);?>
  </div>
</div>
<div class="row">
  <div class="col-xs-3 col-sm-3">
    <br>
    <div class="bs-example">
      <dl>
        <dt>* <?php echo ObtenEtiqueta(1304); ?></dt>
        <dd><span style="color:#9aa7af; font-style: italic;">
        <?php echo ObtenEtiqueta(1305); ?>
        </span></dd>
      </dl>
    </div>
  </div>
  <div class="col-xs-9 col-sm-9">
    <?php Forma_CampoTinyMCE("", False, 'ds_requerimiento', $ds_requerimiento, 50, 20, $ds_requerimiento_err);?>
  </div>
</div>