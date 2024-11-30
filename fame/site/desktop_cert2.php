<?php 
	# Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  # Recibe Parametros
  $fl_programa = RecibeParametroNumerico('fl_programa');
  
  # Informamos al administrador que el usuario desea un certificado valido
  # Validamos si existe no volvera a enviar ni insertar nada
  $row = RecuperaValor("SELECT COUNT(*) FROM k_usuario_doc WHERE fl_usuario=$fl_usuario AND fl_programa=$fl_programa AND fg_tipo_doc='2'");
  $existe_certificado = $row[0];
  # Muestra en el listado del BACK
  EjecutaQuery("UPDATE k_usuario_programa SET fg_certificado = '1', fg_status='RD', fe_enviado=now() WHERE fl_usuario_sp = $fl_usuario  AND fl_programa_sp = $fl_programa");
  if(empty($existe_certificado)){
    # Insertamos el registro del pedido
    $Query  = "INSERT INTO k_usuario_doc (fl_usuario,fl_programa,fe_enviado,fg_oficial,fg_tipo_doc) ";
    $Query .= "VALUES ($fl_usuario, $fl_programa, CURDATE(), '1', '2')";
    $fl_usuario_doc = EjecutaInsert($Query);    
  }
  else{
    # Insertamos el registro del pedido
    $Query  = "UPDATE k_usuario_doc SET fe_enviado=CURDATE(), fg_oficial='1', fg_crop='0', fg_info_user='0', fg_card='0', fg_pagado='0' ";
    $Query .= "WHERE fl_usuario=$fl_usuario AND fl_programa=$fl_programa AND fg_tipo_doc='2'";
    EjecutaQuery($Query);
  }
  
  # Obtenemos el nombre del programa 
  $nb_programa = ObtenNombreCourse($fl_programa);
  # Obtenemos el nombre del usuario
  $Query1  = "SELECT ds_nombres, ds_apaterno, ds_amaterno, ds_email, kusd.ds_phone_number ";
  $Query1 .= "FROM c_usuario us ";
  $Query1 .= "LEFT JOIN k_usu_direccion_sp kusd ON(kusd.fl_usuario_sp=us.fl_usuario) ";
  $Query1 .= "WHERE fl_usuario=$fl_usuario ";
  $row1 = RecuperaValor($Query1);
  $ds_nombres = str_texto($row1[0]);
  $ds_apaterno = str_texto($row1[1]);
  $ds_amaterno = str_texto($row1[2]);
  $ds_email = str_texto($row1[3]);
  $ds_phone_number = str_texto($row1[4]);  
  
  # Enviamos al administrador una notificacion
  # Inicializa variables de ambiente para envio de correo adjunto
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);

  # Separadores
  $eol = "\n";
  $separator = md5(time());
  
  // Envia al administrador
  $repEmail = MAIL_FROM;
  $admin = ObtenConfiguracion(107);
  
  # Headers
  $headers  = 'MIME-Version: 1.0' .$eol;
  $headers .= 'From: "'.$repEmail.'" <'.$repEmail.'>'.$eol;
  // Send copy to test
  $headers .= "Bcc: $admin \r\n";
  $headers .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
  
  # Message
  $p_message  = genera_documento_sp($fl_usuario, 1, 108, $fl_programa);
  $p_message .= genera_documento_sp($fl_usuario, 2, 108, $fl_programa);
  $p_message .= genera_documento_sp($fl_usuario, 3, 108, $fl_programa);
  
  # Envia email
  mail($repEmail, "Started Process to Request Certificate", $p_message, $headers);
?>
<!-- Paso 2 -->
<div class="modal-dialog" role="document" id="modal_actions" style='width: 55%; margin: 3% 10% 15% 25%;'>
  <div class="modal-content">
    <!-- Header del ceritificado -->
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="gridModalLabel">
        <i class="fa fa-exclamation-triangle"></i> <strong> <?php echo ObtenEtiqueta(1153); ?></strong>
      </h4>
    </div>
    <div class="modal-body no-padding-bottom" style="margin-bottom: -10px;">
      <div class="row">
        <div class="col col-md-12 col-lg-6">
        <small><h4 class="no-margin"><?php echo ObtenEtiqueta(1192); ?></h4></small>
        <h3 class="no-margin"><strong><?php echo $nb_programa; ?></strong></h3>
        </div>
        <div class="col col-md-12 col-lg-3">
        <small><h4 class="no-margin"><?php echo ObtenEtiqueta(1193); ?></h4></small>
        <h3 class="no-margin"><strong><?php echo $ds_nombres; ?></strong></h3>
        </div>
        <div class="col col-md-12 col-lg-3">
        <small><h4 class="no-margin"><?php echo ObtenEtiqueta(1194); ?></h4></small>
        <h3 class="no-margin"><strong><?php echo $ds_apaterno; ?></strong></h3>
        </div>
      </div>
      <hr class="no-margin-bottom" style="margin-top:10px;"/>
      <!-- Titulo -->
      <div class="row text-align-center">
        <h3 class="no-padding no-margin"><strong><?php echo ObtenEtiqueta(1154); ?></strong></h3>
      </div>
        
      <!--- Imagen precargada -->
      <div class="row padding-top-10 text-align-center" id="review-official">        
        <div class="col col-sm-12 col-lg-12 col-md-12">
          <?php echo ObtenEtiqueta(1176); ?>
        </div>
        <div class="col col-sm-12 col-lg-1 col-md-12"></div>
        <div class="col col-sm-12 col-lg-10 col-md-12">
        <?php
        # Si existe una foto la mostramos en caso de que no, no se muestra nada
        $ds_oficial = ObtenNombreFotoOficial($fl_usuario);
        if($ds_oficial != ""){
        ?>          
          <div class="panel-image padding-10">
            <img src="<?php echo ObtenFotoOficial($fl_usuario); ?>" class="img-responsive no-padding" width="100%" style="height:300px;">
          </div>
        <?php
        }
        ?>
        </div>
        <div class="col col-sm-12 col-lg-1 col-md-12"></div>
      </div> 
      
      <!-- Forma para el dropzone-->
      <div class="row" id="form-dropzone">
        <div class="col-md-12 col-lg-9" style="padding-right: 20px;">
          <!-- widget grid -->
          <section id="widget-grid" class="">
            <!-- row -->
            <div class="row">
              <!-- widget div-->
              <div>
                <!-- widget edit box -->
                <div class="jarviswidget-editbox">
                  <!-- This area used as dropdown edit box -->
                </div>
                <!-- end widget edit box -->
                <!-- widget content -->
                <div class="widget-body padding-10">
                  <form action="site/upload.php" class="dropzone" id="mydropzone" enctype="multipart/form-data">
                  </form>
                </div>
                <!-- end widget content -->
              </div>
              <!-- end widget div -->
            </div>
            <!-- end row -->
          </section>
          <!-- end widget grid -->
        </div>
        <div class="col-md-12 col-lg-3">
          <div class="col col-lg-12 col-sm-12">
            <div class="superbox-show bg-color-white no-padding" style="display:block; margin-top:40px;">
              <img src="<?php echo PATH_SELF_IMG;?>/Sample-ID.jpg" class="superbox-current-img no-padding">            
            </div>          
          </div>
          <div class="col col-lg-12 col-sm-12" style="top: -10px; left: 30px;">
            <strong style="position:relative; margin-top:-10px;"><?php echo ObtenEtiqueta(1178);?></strong>
          </div>
        </div>
      </div>
      
      <!-- Remplazar la imagen-->
      <div class="row no-margin" id="div_check_official" style="position: inherit;top: -30px;">
        <div class="col col-sm-12 col-lg-4 col-md-12"></div>
        <div class="col col-sm-12 col-lg-8 col-md-12 smart-form">
          <label class="checkbox no-padding no-margin">
            <input id="check_official" name="check_official"type="checkbox">
            <p class="fa fa-camera-retro fa-lg" style="position: relative;top: 25px;left: -25px;"></p><h6><small><u id="txt-img-drop"><?php echo ObtenEtiqueta(1155); ?></u><small></h6>
          </label>
        </div>
        <div class="col col-sm-12 col-lg-1 col-md-12"></div>
      </div>
      
    </div>
    <div class="modal-footer text-align-center">  
      <div class="col-sm-3 col-lg-3"></div>    
      <div class="col-sm-6 col-lg-6">
      <a href="javascript:void(0);" class="btn btn-primary btn-lg btn-block" data-dismiss="modal" id="btn_next_certificado_2_crop">
        <span class="btn-label"><i class="fa fa-check-circle "></i></span> <?php echo ObtenEtiqueta(1179);?>
      </a>
      <a href="javascript:void(0);" class="btn btn-primary btn-lg btn-block" data-dismiss="modal" id="btn_next_go_datos">
        <span class="btn-label"><i class="fa fa-check-circle "></i></span> <?php echo ObtenEtiqueta(1179);?>
      </a>
      </div>
      <div class="col-sm-3 col-lg-3"></div>
    </div>
  </div>
</div>
<style>
.dz-default .dz-message{
  width:100%;
  margin-left:-190px;
}
.dropzone .dz-preview .dz-details, .dropzone-previews .dz-preview .dz-details{
  width: 100px;
height: 0px;
position: relative;
background: #ebebeb00;
padding: 5px;
margin-bottom: 0px;
}

</style>
<script>
/*** Inicio volvemos a cargar todos las funciones del bootstrap **/
pageSetUp();
/** DROPZONE ***/
$(document).ready(function() {
  // Siempre estar deshabilitado hasta que exista un archivo  
  $('#btn_next_certificado_2').addClass('disabled');
  $("#mydropzone").dropzone({
    // url: "/file/post",
    paramName: "file",
    addRemoveLinks : true,
    maxFiles:1, // solo puede subir un archivo
    maxfilesexceeded: function(file) { // si quiere subir mas lo remplazara
      this.removeAllFiles(file);
      this.addFile(file);
    },
    maxFilesize: 5,
    acceptedFiles: ".jpg, .jpeg, .png",
    init: function(){      
      this.on("success", function(file, result) {        
        if(result){
          // Activa el boton para que pueda seguir adelante
          $('#btn_next_certificado_2_crop').removeClass('disabled');
        }        
      });
      this.on("removedfile", function(file) {
        // Si es removido el archivo deshabilitamos el boton nuevamente
        $('#btn_next_certificado_2_crop').addClass('disabled');
      });
      // Mostramos el error y desabilita el boton
      this.on("error", function(file, message) { 
        $('#btn_next_certificado_2_crop').addClass('disabled');
      });
    },
    dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> Drop files <span class="font-xs">to upload</span></span><span>&nbsp&nbsp<h4 class="display-inline"> (Or Click)</h4></span>',
    dictResponseError: 'Error uploading file!'
  });  
});

/*Valor para mandar al siguiente paso*/
var datos = "fl_programa=<?php echo $fl_programa; ?>";
/*Si requiere recortar la imagen*/
$("#btn_next_certificado_2_crop").click(function(){    
  $.ajax({
    type: "POST",
    url: "<?php echo PATH_SELF_SITE; ?>/desktop_cert3.php",
    async: false,
    data: datos,
    success: function(html){
      $('#certificado').html(html);      
    }
  });
});
/**Si utiliza la imagen por default**/
$("#btn_next_go_datos").click(function(){    
  $.ajax({
    type: "POST",
    url: "<?php echo PATH_SELF_SITE; ?>/desktop_cert4.php",
    async: false,
    data: datos+"&no_cropping=1",
    success: function(html){
      $('#certificado').html(html);      
    }
  });
});

/** Eliminar la foto de oficial **/
$('#delete_oficial').on('click', function(){
  var user = '<?php echo $fl_usuario; ?>';
  $.ajax({
    type: "POST",
    url: "<?php echo PATH_SELF_SITE; ?>/upload.php",
    async: false,
    data: 'delete=1',
    success: function(html){
      $('#div_img_oficial').hide();
      $('#btn_next_certificado_2_crop').addClass('disabled');      
    }
  });
})

/** Inicialmente el boton estara desactivado **/
var ds_oficial = '<?php echo $ds_oficial; ?>';
if(ds_oficial==''){
  $('#form-dropzone').removeClass("hide");
  $('#review-official').addClass("hide");
  $('#div_check_official').addClass("hide");
  $("#btn_next_certificado_2_crop").show();
  $("#btn_next_certificado_2_crop").addClass("disabled");
  $("#btn_next_go_datos").hide();
}
else{
  $('#form-dropzone').addClass("hide");
  $('#review-official').removeClass("hide");
  $('#div_check_official').removeClass("hide");
  $("#btn_next_certificado_2_crop").hide();
  $("#btn_next_go_datos").show();
}

/*** Muestra el Dopzone o lo oculta **/
$("#check_official").change(function(){
    var val = $(this).prop("checked");
    /** Mostramos el Dropzone**/
    if(val==true){
      $("#review-official").addClass("hide");
      $("#form-dropzone").removeClass("hide");
      $("#title_official").removeClass("col-lg-8").addClass("col-lg-12 text-align-center");
      $("#txt-img-drop").html("<?php echo ObtenEtiqueta(1177); ?>");
      $("#btn_next_certificado_2_crop").show();
      $("#btn_next_certificado_2_crop").addClass("disabled");
      $("#btn_next_go_datos").hide();
    }
    /** Ocultamos el Dropzone **/
    else{
      $("#review-official").removeClass("hide");
      $("#form-dropzone").addClass("hide");
      $("#title_official").addClass("col-lg-8").removeClass("col-lg-12 text-align-center");
      $("#txt-img-drop").html("<?php echo ObtenEtiqueta(1155); ?>");
      $("#btn_next_certificado_2_crop").hide();
      $("#btn_next_go_datos").show();
    }
});
</script>