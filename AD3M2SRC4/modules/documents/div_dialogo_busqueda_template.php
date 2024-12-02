<?php
  # Librerias
  require '../../lib/general.inc.php';
  $instituto = RecibeParametroNumerico('instituto');
  $programa = RecibeParametroNumerico('programa');
  $fg_label = RecibeParametroNumerico('fg_label');
?>  
<form action="labels.php" method='POST' name='frm_search_fame' id='frm_search_fame' class='smart-form'>
  <div class="col-sm-12 col-md-12 col-lg-12">
<?php     
    $opc = array( ObtenEtiqueta(2135), ObtenEtiqueta(2136)); // Masculino, Femenino, Neutral
    $val = array('1', '2');
    Forma_CampoSelect(ObtenEtiqueta(2137), False, 'fg_label', $opc, $val, $fg_label,'','','onchange=\"busqueda(this.value)\"');
?>
<script>
  function busqueda(instituto, programa) {

    $('#frm_div_fame_search').css('display', 'block');
    $.ajax({
      type: 'POST',
      url: 'div_dialogo_busqueda_template.php',
      async: false,
      data: 'instituto=' + instituto + '&programa='+ programa,
      success: function (html) {
          $('#frm_div_fame_search').html(html);
      }
    });
  }
  pageSetUp();
</script>
</div> 
</form>
