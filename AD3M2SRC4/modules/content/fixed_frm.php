<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibe parametro
  $fg_error = RecibeParametroNumerico('fg_error');
  $clave = RecibeParametroHTML('clave');
  $clave = explode("_",$clave);

  # Variable initialization to avoid errors
  $style_tab_desc="";
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_FIXED, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave[0])) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT nb_pagina, ds_pagina, ds_titulo, tr_titulo, ds_contenido, tr_contenido, fl_programa, no_grado, fg_fijo ";
      $Query .= "FROM c_pagina WHERE cl_pagina=$clave[0] AND fl_programa=$clave[1] AND no_grado=$clave[2]";
      $row = RecuperaValor($Query);
      $nb_pagina = str_texto($row[0]);
      $ds_pagina = str_texto($row[1]);
      $ds_titulo = str_texto($row[2]);
      $tr_titulo = str_texto($row[3]);
      $ds_contenido = str_texto($row[4]);
      $tr_contenido = str_texto($row[5]);
      $fl_programa = $row[6];
      $no_grado = $row[7];
      $fg_fijo = $row[8];
      $Query  = "SELECT nb_programa, ds_duracion FROM c_programa WHERE fl_programa = $fl_programa";
      $row = RecuperaValor($Query);
      $nb_programa = !empty($row[0])?$row[0]:NULL;
      $ds_duracion = !empty($row[1])?$row[1]:NULL;
    }
    else { // Alta, inicializa campos
      $nb_pagina = "";
      $ds_pagina = "";
      $ds_titulo = "";
      $tr_titulo = "";
      $ds_contenido = "";
      $tr_contenido = "";
      $fl_programa = 0;
      $no_grado = 0;
      $fg_fijo = 0;
    }
    $cl_pagina_nueva = "";
    $cl_pagina_err = "";
    $nb_pagina_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $clave = RecibeParametroHTML('clave');
    $clave = explode("_",$clave);
    $fl_programa = $clave[1];
    $no_grado = $clave[2];
    $cl_pagina_nueva = RecibeParametroNumerico('cl_pagina_nueva');
    $cl_pagina_err = RecibeParametroNumerico('cl_pagina_err');
    $nb_pagina = RecibeParametroHTML('nb_pagina');
    $nb_pagina_err = RecibeParametroNumerico('nb_pagina_err');
    $ds_pagina = RecibeParametroHTML('ds_pagina');
    $ds_titulo = RecibeParametroHTML('ds_titulo');
    $tr_titulo = RecibeParametroHTML('tr_titulo');
    $ds_contenido = RecibeParametroHTML('ds_contenido');
    $tr_contenido = RecibeParametroHTML('tr_contenido');
    $fg_fijo = RecibeParametroNumerico('fg_fijo');
    $archivo_a = RecibeParametroNumerico('archivo_a');
  }

  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_FIXED);
  
  # Forma para captura de datos
  Forma_Inicia($clave[0]);
  if(!empty($fg_error))
    Forma_PresentaError( );
    
  Forma_CampoOculto('fg_fijo' , $fg_fijo);
  $rs = EjecutaQuery("SELECT ds_ruta_video, fl_video_contenido FROM k_video_contenido WHERE cl_pagina=".(!empty($clave[0])?$clave[0]:0)." AND fl_programa=".(!empty($clave[1])?$clave[1]:0)." AND no_grado=".(!empty($clave[2])?$clave[2]:0)." ORDER BY fl_video_contenido");
  $no_videos =  CuentaRegistros($rs);
  /*
  # Si se esta editando
  if(!empty($clave[0])) {
    Forma_CampoInfo(ETQ_CLAVE, $clave[0]);
    Forma_CampoOculto('cl_pagina_nueva', $cl_pagina_nueva);
    if($fg_fijo == 0)
    {
      Forma_CampoInfo(ObtenEtiqueta(380), $nb_programa."-".$ds_duracion);
      Forma_CampoOculto('fl_programa', $fl_programa);
      if($no_grado == 0)
        $grado = "";
      else
        $grado = $no_grado;
      Forma_CampoInfo(ObtenEtiqueta(375), $grado);
      Forma_CampoOculto('no_grado', $no_grado);
    }
  }
  else
  {
    Forma_CampoTexto(ETQ_CLAVE, True, 'cl_pagina_nueva', $cl_pagina_nueva, 5, 10, $cl_pagina_err);
    if($fg_fijo == 0)
    {
      $concat = array('nb_programa', "' - '", 'ds_duracion');
      $Query  = "SELECT ".ConcatenaBD($concat).", fl_programa FROM c_programa ORDER BY no_orden";
      Forma_CampoSelectBD(ObtenEtiqueta(380), False, 'fl_programa', $Query, $fl_programa, '', True);
      Forma_Espacio();
      #script para los combox dependientes del programa a elejir
      echo "
      <script language='javascript'>
        $(document).ready(function(){
           $('#fl_programa').change(function () {
             $('#fl_programa option:selected').each(function () {
                fl_programa=$(this).val();
                $.post('grados.php', { fl_programa: fl_programa }, function(data){
                $('#no_grado').html(data);
                });            
            });
           })
        });
        </script>";
      echo "
      <div class='row form-group smart-form '>
        <label class='col col-sm-4 text-align-right'><strong>".ObtenEtiqueta(375).":</strong></label>
        <div class='col col-sm-4'><label class='select'><select name='no_grado' id='no_grado' class='select2'></select><i></i></div>
      </div>";
      
    }
  }
  Forma_Espacio( );
  
  # Campos de captura
  Forma_CampoTexto(ObtenEtiqueta(270), True, 'nb_pagina', $nb_pagina, 50, 30, $nb_pagina_err);
  Forma_CampoTexto(ETQ_DESCRIPCION, False, 'ds_pagina', $ds_pagina, 255, 50);
  Forma_Espacio( );
  Forma_CampoTexto(ETQ_TITULO, False, 'ds_titulo', $ds_titulo, 255, 50);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_titulo', $tr_titulo, 255, 50);
  Forma_Espacio( );
  Forma_CampoTinyMCE(ObtenEtiqueta(271), False, 'ds_contenido', $ds_contenido, 50, 20);
  Forma_CampoTinyMCE(ObtenEtiqueta(245), False, 'tr_contenido', $tr_contenido, 50, 20);
  Forma_Espacio( );
  
  $rs = EjecutaQuery("SELECT ds_ruta_video, fl_video_contenido FROM k_video_contenido 
                          WHERE cl_pagina=$clave[0] AND fl_programa=$clave[1] AND no_grado=$clave[2] 
                          ORDER BY fl_video_contenido");
  $no_videos =  CuentaRegistros($rs);
  Forma_CampoOculto('no_videos', $no_videos);
  
  if($no_videos > 0) {
    for($i = 1; $row = RecuperaRegistro($rs); $i++)
    {
      $ds_ruta_video[$i] = $row[0];
      $fl_video_contenido[$i] = $row[1];
      $ext = strtoupper(ObtenExtensionArchivo($ds_ruta_video[$i]));
      switch($ext) {
        case "SWF": $ruta = SP_FLASH_W; break;
        case "FLV": $ruta = PATH_STREAMING; break;
        default:    $ruta = SP_VIDEOS_W; break;
      }
      
      Forma_CampoPreview(ObtenEtiqueta(457), 'ds_ruta_video'.$i, $ds_ruta_video[$i], $ruta, True, False);
      Forma_CampoOculto('fl_video_contenido'.$i, $fl_video_contenido[$i]);
    }
    Forma_FileUploader(ObtenEtiqueta(216), False, 'archivo', "'flv', 'mov'", '500 * 1024 * 1024', '', False);
  }
  else
    Forma_FileUploader(ObtenEtiqueta(457), False, 'archivo', "'flv', 'mov'", '500 * 1024 * 1024', '', False);
    Forma_CampoInfo('NOTE', ObtenEtiqueta(187)); // NOTE: (Explicacion del codigo a usar para incrustar un video en el TinyMCE)
  Forma_Espacio( );
  */
?>
<!-- MJD: Titulos de Tabs -->
<ul id="myTab1" class="nav nav-tabs bordered">
  <li class="active">
    <a id="tab_1" href="#description" data-toggle="tab">
      <span <?php echo $style_tab_desc; ?>><i class="fa fa-fw fa-lg fa-info-circle"></i><?php echo " ".ObtenEtiqueta(19) ?></span>
    </a>
  </li>
  <li>
    <a id="tab_2" href="#videos" data-toggle="tab">
      <span <?php echo $style_tab_desc; ?>><i class="fa fa-fw fa-lg fa-file-movie-o"></i><strong id="tot_vid_csl_v"><?php echo ObtenEtiqueta(2029)."&nbsp; (".$no_videos.")"?></strong></span>
    </a>
  </li>
</ul>
	
<!-- Contenido de Tabs -->
<div id="myTabContent1" class="tab-content padding-10 no-border">  
  <!--Primer tab = descripcion -->
  <div class="tab-pane fade in active" id="description">
  <?php
  # Si se esta editando
  if(!empty($clave[0])) {
    Forma_CampoInfo(ETQ_CLAVE, $clave[0]);
    Forma_CampoOculto('cl_pagina_nueva', $cl_pagina_nueva);
    if($fg_fijo == 0)
    {
      Forma_CampoInfo(ObtenEtiqueta(380), $nb_programa."-".$ds_duracion);
      Forma_CampoOculto('fl_programa', $fl_programa);
      if($no_grado == 0)
        $grado = "";
      else
        $grado = $no_grado;
      Forma_CampoInfo(ObtenEtiqueta(375), $grado);
      Forma_CampoOculto('no_grado', $no_grado);
    }
  }
  else
  {
    Forma_CampoTexto(ETQ_CLAVE, True, 'cl_pagina_nueva', $cl_pagina_nueva, 5, 10, $cl_pagina_err);
    if($fg_fijo == 0)
    {
      $concat = array('nb_programa', "' - '", 'ds_duracion');
      $Query  = "SELECT ".ConcatenaBD($concat).", fl_programa FROM c_programa ORDER BY no_orden";
      Forma_CampoSelectBD(ObtenEtiqueta(380), False, 'fl_programa', $Query, $fl_programa, '', True);
      Forma_Espacio();
      #script para los combox dependientes del programa a elejir
      echo "
      <script language='javascript'>
        $(document).ready(function(){
           $('#fl_programa').change(function () {
             $('#fl_programa option:selected').each(function () {
                fl_programa=$(this).val();
                $.post('grados.php', { fl_programa: fl_programa }, function(data){
                $('#no_grado').html(data);
                });            
            });
           })
        });
        </script>";
      echo "
      <div class='row form-group smart-form '>
        <label class='col col-sm-4 text-align-right'><strong>".ObtenEtiqueta(375).":</strong></label>
        <div class='col col-sm-4'><label class='select'><select name='no_grado' id='no_grado' class='select2'></select><i></i></div>
      </div>";
      
    }
  }
  Forma_Espacio( );
  # Campos de captura
  Forma_CampoTexto(ObtenEtiqueta(270), True, 'nb_pagina', $nb_pagina, 50, 30, $nb_pagina_err);
  Forma_CampoTexto(ETQ_DESCRIPCION, False, 'ds_pagina', $ds_pagina, 255, 50);
  Forma_Espacio( );
  Forma_CampoTexto(ETQ_TITULO, False, 'ds_titulo', $ds_titulo, 255, 50);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_titulo', $tr_titulo, 255, 50);
  Forma_Espacio( );
  Forma_CampoTinyMCE(ObtenEtiqueta(271), False, 'ds_contenido', $ds_contenido, 50, 20);
  Forma_CampoTinyMCE(ObtenEtiqueta(245), False, 'tr_contenido', $tr_contenido, 50, 20);
  Forma_Espacio( );
  ?>
  </div>
  <!--Primer tab = descripcion -->
  <div class="tab-pane fade" id="videos">
    <div class="row padding-5">          
      <div class="widget-body">
        <ul id="myTab2" class="nav nav-tabs bordered">
          <li class="active" id="csl1">
            <a href="#s1" data-toggle="tab" aria-expanded="true"><?php echo ObtenEtiqueta(2030); ?></a>
          </li>
          <li class="" onclick="videos(<?php echo (!empty($clave[0])?$clave[0]:NULL).",".(!empty($clave[1])?$clave[1]:0).",".(!empty($clave[2])?$clave[2]:0).",".$fg_error; ?>);" id="csl2">
            <a href="#s2" data-toggle="tab" aria-expanded="false" id="tot_vid_csl_g"><?php echo ObtenEtiqueta(2031)."&nbsp; (".$no_videos.")"; ?></a>
          </li>
        </ul>
        <div id="myTabContent2" class="tab-content padding-10">
          <div class="tab-pane fade in active" id="s1">
          <?php
          # Si es nuevo y hay error y existe videos en proceso
          if(/*!empty($fg_error) &&*/ ExisteEnTabla('k_vid_content_temp', 'fl_usuario', $fl_usuario)){
            echo "
              <div class='row'>
                <div class='col-sm-3'>&nbsp</div>
                <div class='col-xs-12 col-sm-5'>
                  <span class='help-block'><p><code><i class='fa fa-warning'></i> ".ObtenEtiqueta(2032)."</code></p></span>
                </div>
                <div class='col-sm-3'>&nbsp</div>
              </div>";
          }
          # Valor para actualizar
          if(!empty($ds_vl_ruta))
            $fg_actualizar = 1;
          else
            $fg_actualizar = 0;
          $par_name = array('clave', 'fl_programa', 'no_grado', 'usuario', 'fg_actualizar','fg_video_orientation');
          $par_valor = array((!empty($clave[0])?$clave[0]:0), $fl_programa, (!empty($clave[2])?$clave[2]:0), $fl_usuario, $fg_actualizar,1);
          Forma_DropzoneVideos($clave[0], !empty($ds_vl_ruta)?$ds_vl_ruta:"", !empty($p_titulo)?$p_titulo:"", "library_school", "vid_library.php", ".mov, .mp4, .flv", $par_name, $par_valor, false, isset($ruta_img)?$ruta_img:NULL, "", "", "", "",0,true, '$("#csl1").removeClass("in active"); $("#s1").removeClass("in active"); $("#csl2").addClass("in active"); $("#s2").addClass("in active"); videos('.(!empty($clave[0])?$clave[0]:0).', '.(!empty($clave[1])?$clave[1]:0).', '.(!empty($clave[2])?$clave[2]:0).', '.$fg_error.')');
          # Fin de DROPZONE
          echo"
            <script>
                $('#library_school').addClass('dz-clickable');
            </script>
            ";


          $Query  = "SELECT ds_vl_ruta, fl_leccion FROM c_leccion WHERE ds_vl_ruta <> '' GROUP BY ds_vl_ruta ORDER BY ds_vl_ruta";
          Forma_CampoSelectBD(ObtenEtiqueta(389), False, 'archivo_a', $Query, isset($archivo_a)?$archivo_a:NULL, '', True);
          Forma_Espacio();
          // Forma_CampoTexto(ObtenEtiqueta(396), False, 'ds_vl_duracion', $ds_vl_duracion, 10, 5);
          // Forma_CampoInfo(ObtenEtiqueta(397), $fe_vl_alta);
          Forma_CampoOculto('fe_vl_alta', isset($fe_vl_alta)?$fe_vl_alta:NULL);                  
          Forma_CampoOculto('ds_vl_ruta', isset($ds_vl_ruta)?$ds_vl_ruta:NULL);
          Forma_Espacio( );
          ?>
          </div>
          <div class="tab-pane fade" id="s2">
            <div class="row" id="videos_library">
              <div class="row text-align-center">
               <i class="fa fa-cog fa-spin fa-3x"></i>
              </div>
            </div>
          </div>         
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function videos(cla,pro,grade,error){
  var div = $('#videos_library');
  $.ajax({
    type: "POST",
    url : "videos_library.php",
    data: "clave="+cla+"&pro="+pro+"&grade="+grade+"&fg_error="+error+"&accion=1",
    // xhr: function () {        
    // },
    success: function(html){
      div.empty().append(html);
    }
  });
}
</script>
<?php
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_FIXED, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  echo"  
  <script src='".PATH_LIB."/fame/dropzone.min.js'></script>
   <script src='".PATH_SELF_JS."/plugin/x-editable/moment.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/jquery.mockjax.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/x-editable.min.js'></script>
  <script>
  pageSetUp();
  // pagefunction
  var pagefunction = function() {            
    $('.superbox').SuperBox();            
  };          
  // end pagefunction          
  // run pagefunction on load
  // load bootstrap-progress bar script
  loadScript('".PATH_HOME."/bootstrap/js/plugin/superbox/superbox.min.js', pagefunction);
  </script>
  ";
?>