<?php
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibe parametros
  $fl_leccion = RecibeParametroNumerico('fl_leccion', true);
  $fg_editar = RecibeParametroNumerico('fg_editar', true);
  $fl_idioma = RecibeParametroNumerico('fl_idioma', true);
  
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
		×
	</button>
	<h6 class="modal-title" id="title_language"><i class="fa fa-language"></i> <?php echo ObtenEtiqueta(2014); ?></h6>
</div>
<div class="modal-body">
  <form class="smart-form">
    <div class="row">
      <div class="col-md-12">
      <section>
      <?php      
      # Muestra los idiomas que no ha ingresado 
      if(empty($fg_editar)){
        $Query  = "SELECT a.nb_idioma, a.fl_idioma FROM c_idioma a WHERE NOT EXISTS(SELECT * FROM k_idioma_video b WHERE b.fl_idioma=a.fl_idioma AND b.fl_leccion_sp=$fl_leccion) ";
        $Query .= "ORDER BY nb_idioma ASC";
      }
      else{
        $Query  = "SELECT a.nb_idioma, a.fl_idioma FROM c_idioma a ";
        $Query .= "ORDER BY nb_idioma ASC";
      }
      Forma_CampoSelectBD(ObtenEtiqueta(2015), true, 'fl_idioma', $Query, $fl_idioma, '', True, '', 'left','col col-md-3', 'col col-md-9', 'def');
      Forma_CampoOculto('fl_leccion', $fl_leccion);
      ?>      
      </section>
      <section>
        <label class="label no-padding"><strong> <?php echo ObtenEtiqueta(2018); ?></strong></label>        
        <label class="textarea textarea-resizable"> 										
          <textarea rows="8" class="custom-scroll" placeholder="WEBVTT ......" id="ds_language" name="ds_language"><?php
          # Consultamos la informacion del idioma
          if(!empty($fg_editar)){
            /*$row = RecuperaValor("SELECT ds_language FROM k_idioma_video WHERE  fl_leccion_sp=$fl_leccion AND fl_idioma=$fl_idioma");
            echo $ds_language = str_texto($row[0]);*/
            $row = RecuperaValor("SELECT nb_archivo FROM k_idioma_video WHERE  fl_leccion_sp=$fl_leccion AND fl_idioma=$fl_idioma");
            $nb_archivo = $row[0];
            $path = "/var/www/html/vanas/vanas_videos/fame/lessons/video_".$fl_leccion."/".$nb_archivo;
            if(!file_exists($path))
                echo"File not found";
            $file = fopen($path, "r");
            if ($file) {
                while (($line = fgets($file)) !== false) {
                    echo $line;
                }
                if (!feof($file)) {
                    echo "Error: EOF not found\n";
                }
                fclose($file);
            }
          }else echo "";         
          ?></textarea> 
        </label>    
        <div class="note">
          <strong><a href="javascript:void(0);" onclick="divLogin()"><?php echo ObtenEtiqueta(2016); ?></a></strong>
          <div id="example_vtt" class="hidden">
            <strong> WEBVTT</strong><br/><br/>          
            00:00:00.000 --> 00:00:03.572<br/>
            Editor, we will always work with that<br/><br/>
            00:00:04.573 --> 00:00:06.537<br/>
            Animation Editor and Graph Editor<br/><br/>
            00:00:07.122 --> 00:00:08.332<br/>
            Ok
          </div>
        </div>
      </section>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">
		<?php echo ObtenEtiqueta(14); ?>
	</button>
	<button type="button" class="btn btn-primary" onclick="Languages()" disabled="disabled" id="aceptar">
		<?php echo ObtenEtiqueta(2017); ?>
	</button>
</div>
<script>
  pageSetUp();
  // Valida si va editarlo
  var editar = '<?php echo $fg_editar; ?>';
  if(editar=='1'){
    $("#fl_idioma").attr('disabled', 'disabled');
    Validacion();
  }
  
  // Campo idioma
  $('#fl_idioma').change(function () {
      Validacion();
  });
  
  // Campo language
  $('#ds_language').on('keydown',function () {
      Validacion();
  });

  function Validacion(){   
    // Valores
    var fl_idioma = $("#fl_idioma").val();
    var ds_language = $("#ds_language").val();
   
    if (fl_idioma == 0) {
        $("#div_fl_idioma").addClass('has-error');
    } 
    else {
        $("#div_fl_idioma").removeClass('has-error');
    }
    
    if (ds_language == '') {
        document.getElementById("ds_language").style.borderColor = "red";
        document.getElementById("ds_language").style.background = "#fff0f0";
    } 
    else {
        document.getElementById("ds_language").style.borderColor = "#739e73";
        document.getElementById("ds_language").style.background = "#f0fff0";
    }
    
    
    // Activamos o desactivamos el boton
    if (fl_idioma > 0 && ds_language.length>0) {
        $("#aceptar").removeAttr('disabled');
    } else {
        $("#aceptar").attr('disabled', 'disabled');
    }
}


  var clic = 1;
  function divLogin(){ 
    var ele = $("#example_vtt");
     if(clic==1){
     // document.getElementById("example_vtt").style.height = "100px";
     ele.removeClass("hidden");
     clic = clic + 1;
     } else{
         // document.getElementById("example_vtt").style.height = "0px";      
         ele.addClass("hidden");
      clic = 1;
     }   
  }

</script>