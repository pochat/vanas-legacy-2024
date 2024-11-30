<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  // if(!ValidaPermisoCampus(FUNC_ALUMNOS) || !ValidaPermisoCampus(FUNC_MAESTROS)) {
    // MuestraPaginaError(ERR_SIN_PERMISO);
    // echo "gabril error";
    // exit;
  // }
  
  # Recibe parametro
  $fl_video_contenido = RecibeParametroNumerico('item', true);
  
  # Obtenemos la informacion del video
  $Query = "SELECT cl_pagina, fl_programa, no_grado, ds_ruta_video, ds_title_vid, ds_duration FROM k_video_contenido WHERE fl_video_contenido=".$fl_video_contenido;
  $row = RecuperaValor($Query);
  $cl_pagina = $row[0];
  $fl_programa = $row[1];
  $no_grado = $row[2];
  $ds_ruta_video = $row[3];
  $ds_title_vid = str_texto($row[4]);
  $ds_duration = $row[5];
  $ds_ruta_video = array_shift(explode('.',$ds_ruta_video));
  // $ruta = ObtenConfiguracion(116)."/vanas_videos/campus/library/video_".$cl_pagina."_".$fl_programa."_".$no_grado."/video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/".$ds_ruta_video;
  $ruta = ObtenConfiguracion(121)."/vanas_videos/campus/student_library/video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/".$ds_ruta_video;
  $ruta_img = ObtenConfiguracion(121)."/vanas_videos/campus/student_library/video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/img_1.png";
  $mp4 = $ruta.".mp4";
  $m3u8 = $ruta.".m3u8";
?>
<!-- Modal Content -->
<div class="modal-header text-align-center">
  <button type="button" class="close" id="cerrar_modal_video" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="item-title" style="font-weight:500;"><strong><?php echo $ds_title_vid; ?></strong></h4>
</div>
<div class="modal-body text-align-center" id="item-body">
  <div class="row">
    <div class="col col-sm-12 col-lg-1 col-md-12"></div>
    <div class="col col-sm-12 col-lg-10 col-md-12">
      <div id='div_flowplayer' class='flowplayer fp-edgy' style="background-image:url(<?php echo $ruta_img; ?>);border-style:solid; border-width:2px;border-color:#BDBDBD;">        
      </div>
      <span id="lbl_time" class='label label-info bg-color-darken pull-right' style='position:relative;bottom:35px;right:5px;font-size:15px;'><?php echo $ds_duration; ?></span>
    </div>
    <div class="col col-sm-12 col-lg-1 col-md-12"></div>
  </div>
</div>
<div class="modal-footer" id="item-footer"></div>

<!-- Flowplayer library -->
<script src="<?php echo PATH_SELF_JS; ?>/flowplayer/flowplayer.min.js"></script>
<!-- Flowplayer hlsjs engine -->
<script src="//releases.flowplayer.org/hlsjs/flowplayer.hlsjs.min.js"></script>
<!-- Flowplayer quality selector plugin -->
<script src="//releases.flowplayer.org/vod-quality-selector/flowplayer.vod-quality-selector.js"></script>
<script src="<?php echo PATH_N_ALU_PAGES; ?>/flowplayer.inc.js"></script>
<script>
// Variables
var pagina = <?php echo json_encode($cl_pagina); ?>;
var programa = <?php echo json_encode($fl_programa); ?>;
var grado = <?php echo json_encode($no_grado); ?>;
var video = <?php echo json_encode($fl_video_contenido); ?>;
var m3u8 = <?php echo json_encode($m3u8); ?>;
var key = <?php echo json_encode(ObtenConfiguracion(110)); ?>;

// Set the student being viewed
ViodesController.setPagina(pagina);
ViodesController.setPrograma(programa);
ViodesController.setGrado(grado);
ViodesController.setVideoContenido(video);
ViodesController.seturl(m3u8);
ViodesController.setKeyFlowplayer(key);

// Initial request for desktop content
ViodesController.requestTabContent();
</script>