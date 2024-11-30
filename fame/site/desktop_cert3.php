<?php 
	# Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  # Recibe Parametros
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $x = RecibeParametroNumerico("x");
  $y = RecibeParametroNumerico("y");
  $w = RecibeParametroNumerico("w");
  $h = RecibeParametroNumerico("h");
  if(!empty($x)){
  require_once '../lib/PHPThumb/ThumbLib.inc.php';
  $src = PATH_SELF_UPLOADS_F."/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/".ObtenNombreFotoOficial($fl_usuario);
  $thumb = PhpThumbFactory::create($src);
  $thumb->crop($x, $y, $w, $h);
  $foto_size = ObtenConfiguracion(80);
  
  $thumb->save($src);
  CreaThumb($src, $src, 0, 0, $foto_size);
  # Se direcciona al siguiente paso
  ?>
  <script>
  $(document).ready(function(){
    $.ajax({
      type: "POST",
      url: "<?php echo PATH_SELF_SITE; ?>/desktop_cert4.php",
      async: false,
      data: "fl_programa=<?php echo $fl_programa; ?>",
      success: function(html){
        $('#certificado').html(html);      
      }
    });
  });
  </script>
  <?php
	// exit;
  }
  else{
    # Insertamos el registro del pedido
    $Query  = "UPDATE k_usuario_doc SET fg_crop='1' WHERE fl_usuario=$fl_usuario AND fl_programa=$fl_programa AND fg_tipo_doc='2'";
    EjecutaQuery($Query);
  }

?>
<link rel="stylesheet" href="<?php echo PATH_SELF_CSS; ?>/crop.css">
<script src="<?php echo PATH_SELF_JS; ?>/crop.js"></script>

<!-- Modal cuerpo -->
<div class="modal-dialog" role="document" id="modal_actions"  style="width: 55%; margin: 3% 10% 15% 25%;">
  <div class="modal-content">
    <!-- Header del ceritificado -->
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="gridModalLabel">
        <i class="fa fa-exclamation-triangle"></i> <strong><?php echo ObtenEtiqueta(1153); ?></strong>
      </h4>
    </div>    
    <div class="modal-body">
      <?php
      # Obtenemos el nombre del programa
      $row = RecuperaValor("SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp=$fl_programa");
      $nb_programa = $row[0];
      # Obtenemos el nombre del usuario
      $row1 = RecuperaValor("SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario");
      $ds_nombres = str_texto($row1[0]);
      $ds_apaterno = str_texto($row1[1]);
      ?>
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
      <hr style="margin-top:10px;"/>
      
      <div class="row">
        <div class="col col-md-12 align-center">
          <!-- <h3 class="page-header">Demo:</h3> -->
          <div class="img-container">
            <img id="cropbox" src="<?php echo PATH_SELF_UPLOADS."/".$fl_instituto; ?>/<?php echo CARPETA_USER.$fl_usuario?>/<?php echo ObtenNombreFotoOficial($fl_usuario); ?>">
          </div>
          <input class="form-control" id="dataX" name="dataX" type="hidden">
          <input class="form-control" id="dataY" name="dataY" type="hidden">
          <input class="form-control" id="dataWidth" name="dataWidth" type="hidden">
          <input class="form-control" id="dataHeight" name="dataHeight" type="hidden">
        </div>
      </div>
      <!-- Alert -->
      <div class="row padding-10">
        <div class="docs-alert hidden"><span class="warning message" id="warning"></span></div>
      </div>
    </div>
    <div class="modal-footer text-align-center">
      <div class="col-sm-3 col-lg-3"></div>    
      <div class="col-sm-6 col-lg-6">
      <a href="javascript:void(0);" class="btn btn-primary btn-lg btn-block" onclick="return checkCoords();">
        <span class="btn-label"><i class="fa fa-crop"></i></span> <?php echo ObtenEtiqueta(1179);?>
      </a>
      </div>
      <div class="col-sm-3 col-lg-3"></div>
    </div>
  </div>
</div>

<script>
// volver a cargar las imagenes
pageSetUp();
$('#cropbox').cropper("setAspectRatio", NaN);
function checkCoords(){
  var fl_programa = "<?php echo $fl_programa; ?>";
  var x = $("#dataX").val();
  var y = $("#dataY").val();
  var w = $("#dataWidth").val();
  var h = $("#dataHeight").val();
  var datos = "fl_programa="+fl_programa+"&x="+x+"&y="+y+"&w="+w+"&h="+h;   
  if(parseInt(x)){
    $.ajax({
      type: "POST",
      url: "<?php echo PATH_SELF_SITE; ?>/desktop_cert3.php",
      async: true,
      data: datos,
      success: function(html){
        $('#certificado').html(html);      
      }
    });
  }
  else{            
    $(".docs-alert").removeClass("hidden");
    $("#warning").empty();
    var text = '<strong>Warning!</strong> You must select an area';
    $("#warning").append(text);
  }
}

</script>