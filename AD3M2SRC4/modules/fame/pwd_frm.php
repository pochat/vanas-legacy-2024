<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario_actual = ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
?>
<div class="modal-dialog" role="document" id="modal_actions" style="width:30%; margin:10% 10% 15% 40%;">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-exclamation-triangle"></i> <strong>Change password</strong></h4>
    </div>
    <div class="modal-body">
       <div class="row">
        <div class="col-xs-12 col-sm-12">
          <?php
          Forma_CampoTexto(ObtenEtiqueta(125), True, 'ds_password', $ds_password, 16, 0, '', true);          
          ?>
        </div>
        <div class="col-xs-12 col-sm-12">
          <?php
          Forma_CampoTexto(ObtenEtiqueta(126), True, 'ds_password_conf', $ds_password_conf, 16, 0, '', true);
          ?>
        </div>
        <div class="note note-error text-color-red" id="info_pwd_igual"></div>
       </div>
    </div>
    <div class="modal-footer">
      <a class="btn btn-secondary" data-dismiss="modal" ><i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(14); ?></a>
      <a class="btn btn-primary disabled" id="btn_pwd"><i class="fa fa-check-circle"></i> <?php echo ObtenEtiqueta(126); ?></a>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  $("#ds_password").on("change",function(){
    activar_btn();
  });
  $("#ds_password_conf").on("change",function(){
    activar_btn();
  });

  $("#btn_pwd").on("click",function(){
    var password , password_conf, clave='<?php echo $clave; ?>';
    password = $("#ds_password").val();
    password_conf = $("#ds_password_conf").val();
    $.ajax({
        type: 'POST',
        url: 'pwd_iu.php',
        data: 'clave='+clave+'&ds_password='+password+'&ds_password_conf='+password_conf,
        async: true,
        success: function (html) {
          $("#modal-empty-student").modal("toggle");
        }
    });
  });
  
  function activar_btn(){
    var password , password_conf;
    password = $("#ds_password").val();
    password_conf = $("#ds_password_conf").val();
    if(password!='' && password_conf!='' && password==password_conf){
      $("#btn_pwd").removeClass("disabled");
      $("#info_pwd_igual").empty();
      document.getElementById("ds_password").style.borderColor = "#739e73";
      document.getElementById("ds_password").style.background = "#f0fff0";
      document.getElementById("ds_password_conf").style.borderColor = "#739e73";
      document.getElementById("ds_password_conf").style.background = "#f0fff0";
    }
    else{
      if(password==""){
        document.getElementById("ds_password").style.borderColor = "red";
        document.getElementById("ds_password").style.background = "#fff0f0";
      }
      else{
        document.getElementById("ds_password").style.borderColor = "#739e73";
        document.getElementById("ds_password").style.background = "#f0fff0";
      }
      
      if(password_conf==""){        
        document.getElementById("ds_password_conf").style.borderColor = "red";
        document.getElementById("ds_password_conf").style.background = "#fff0f0";
      }
      else{
        document.getElementById("ds_password_conf").style.borderColor = "#739e73";
        document.getElementById("ds_password_conf").style.background = "#f0fff0";
      }
      $("#btn_pwd").addClass("disabled");
      // si los password no son identico avisara al usuario
      if(password!=password_conf){
        $("#info_pwd_igual").empty().append('<i class=\'fa fa-info\'></i> <?php echo ObtenMensaje(101); ?>').addClass('txt-color-red text-align-center');
      }
    }
  }
});  
</script>
