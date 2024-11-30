<?php 
	# Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  # Recibe Parametros
  $fl_programa = RecibeParametroHTML('fl_programa');
  # Verificamos si la institucion sigue en modo trial
  $fg_status = Obten_Status_Trial($fl_instituto); 

  
  # Muestra el linkpara el pdf o no 
  $row =RecuperaValor("SELECT ds_valor FROM c_configuracion WHERE cl_configuracion=108");
  $ds_valor = $row[0];
  $link = "hide";
  if(!empty($ds_valor))
    $link = "";
?>
<div class="modal-dialog" role="document" id="modal_actions" style='width: 55%; margin: 3% 10% 15% 25%;'>
  <div class="modal-content">
    <!-- Header del ceritificado -->
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="gridModalLabel">
        <i class="fa fa-exclamation-triangle"></i> <strong><?php echo ObtenEtiqueta(1153); ?></strong>
      </h4>
    </div>
    <!-- Body del ceritificado PASO 1 -->
    <div class="modal-body no-padding">      
      <div class="row padding-10">
        <div class="col-md-12 col-sm-12 col-lg-7 text-align-center" style="padding-top:10%;">
          <?php
          if(!empty($fg_status)){
          ?>
          <h1><strong><?php echo ObtenEtiqueta(1172); ?></strong></h1>
          <h6>
          <small>
          <?php
            echo str_replace("#mn_certificate#", ObtenConfiguracion(106), str_uso_normal(ObtenEtiqueta(1173)));
          ?>
          </small>
          </h6>
          
          <div class="padding-10">
            <a type="button" class="btn btn-primary" data-dismiss="modal" id="btn_next_certificado_1" 
              style="background-color:#0092cd; border-color:#0092cd;"><?php echo ObtenEtiqueta(1174); ?></a>
          </div>
          <div class="padding-10 <?php echo $link; ?>">
            <a class="h6" href="site/certificado_pdf.php?u=<?php echo $fl_usuario; ?>&p=<?php echo $fl_programa; ?>&fg_tipo=2">
            <small><u><?php echo ObtenEtiqueta(1175); ?></u></small>
            </a>
          </div>
          <!--<a type="button" class="btn btn-primary" data-dismiss="modal" id="btn_next_certificado_1" style="border-radius:10px;">            
            <h4>Request certificate <br/>Valid for Academic credit transfer </h4>
            <small class="text-muted txt-color-white">Certificate have a cost $29 USD <br/>
            An official identification is required<br/>
            (i.e Passport, Drivers license, Local ID, etc.)</small>
          </a>-->
          <?php
          }
          else{
          ?>
            <!--<div style="padding-top:30px;">
              <div><i class="fa fa-warning fa-5x txt-color-red"></i><div></div></div>
              <h2 class="no-padding no-margin"><strong><?php echo ObtenEtiqueta(1859); ?></strong></h2>
              <div class="no-padding"><small  class="text-muted"><?php echo ObtenEtiqueta(1860); ?></small></div>              
            </div>-->
            <div class='text-center error-box'>
              <h3 class='error-text tada animated'  style='font-size: 50px;'><i class='fa fa-times-circle text-danger error-icon-shadow'></i> <?php echo ObtenEtiqueta(1860); ?></h3>
            </div>
          <?php
          }
          ?>
          <div style="padding-top:50px;" class="hide">
            <h4><strong>Download Electronic Certificate</strong></h4>
            <div class="no-padding"><small  class="text-muted">Not Validate for academic credit transfer</small></div>
            <div><a class="txt-color-red" href="javascript:certificado();"><i class="fa fa-file-pdf-o fa-2x"></i><div></div></a></div>            
          </div>
        </div>
        <div class="col-md-12 col-sm-12 col-lg-5">
          <img src="<?php echo PATH_SELF_IMG;?>/Sample-Diploma.jpg" class="superbox-current-img no-padding">
        </div>
      </div>
    </div>
    <!-- Footer -->
    <div class="modal-footer text-align-center">  
      <div class="col-sm-3 col-lg-3"></div>    
      <div class="col-sm-6 col-lg-6">
        <a type="button" class="btn btn-default btn-lg btn-block" data-dismiss="modal">
          <i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1152); ?>
        </a>
      </div>
      <div class="col-sm-3 col-lg-3"></div>
    </div>
  </div>
</div>
<script>
  function certificado(){
    var user = '<?php echo $fl_usuario; ?>';
    var program = '<?php echo $fl_programa; ?>';
    var adm = 0; // mencionamos si queremos la imagen de fondo o no
    var url = '<?php echo PATH_SELF_SITE; ?>/certificado_pdf.php';
    // Envia datos por forma
    document.certificado.u.value = user;
    document.certificado.p.value = program;
    document.certificado.b.value = adm;
    document.certificado.action = url;
    document.certificado.submit();
  }
</script>
<!-- Envia el programa usuario y un flag para identificar que es del back -->
<form name=certificado method=post>
  <input type=hidden name=u>
  <input type=hidden name=p>
  <input type=hidden name=b>
</form>
<script src="https://checkout.stripe.com/checkout.js"></script>
<script>
var fl_programa = '<?php echo $fl_programa; ?>';
var ds_no_codigo_area = '<?php echo $ds_no_codigo_area; ?>';
// Boton
$("#btn_next_certificado_1").click(function(){  
  var datos = "fl_programa="+fl_programa;
  $.ajax({
    type: "POST",
    url: "<?php echo PATH_SELF_SITE; ?>/desktop_cert2.php",
    async: false,
    data: datos,
    success: function(html){
      $('#certificado').html(html);      
    }
  });
});
</script>