<?php
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );

  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fl_programa = RecibeParametroNumerico('pro');
  $no_grado = RecibeParametroNumerico('grade');
  $fg_error = RecibeParametroNumerico('fg_error');
  $accion = RecibeParametroNumerico('accion'); // 1 muestra videos 2 elimina video 3 guarda titulo del video
  $fg_fame = RecibeParametroNumerico('fg_fame');
  if(empty($fg_fame))
    $fg_fame = 0;
  
  # Muestra los videos que tiene
  if($accion==1){
    # Si es nuevo y existe error y hay videos procesados
    # Identificamos si es libreria de fame o campus
    if(empty($fg_fame)){
      // $exists = ExisteEnTabla('k_vid_content_temp', 'fl_usuario', $fl_usuario, 'fg_fame', 0, true);
      $exists = RecuperaValor("SELECT fl_vid_contet_temp FROM k_vid_content_temp WHERE fl_usuario=$fl_usuario AND fg_fame='0'");
    }
    else{
      // $exists = ExisteEnTabla('k_vid_content_temp', 'fl_usuario', $fl_usuario, 'fg_fame', 1,true);
      $exists = RecuperaValor("SELECT fl_vid_contet_temp FROM k_vid_content_temp WHERE fl_usuario=$fl_usuario AND fg_fame='1'");
      
    }
    
    $campus_url = ObtenConfiguracion(121);
    $fame_url = ObtenConfiguracion(116);
    if(!empty($exists[0])){
      # Identificamos si es libreria de fame o campus
      if(empty($fg_fame)){        
        if(empty($clave)){
          $Queryx = "SELECT no_orden, nb_archivo FROM k_vid_content_temp WHERE fl_clave=".$fl_usuario." AND fl_programa=".$fl_usuario." AND no_grado=".$fl_usuario." AND fg_fame='0' ORDER BY no_orden";
          $ruta1 = $campus_url."/vanas_videos/campus/library/video_us".$fl_usuario."_us".$fl_usuario."_us".$fl_usuario;
        }
        else{
          $Queryx = "SELECT no_orden, nb_archivo FROM k_vid_content_temp WHERE fl_clave=".$clave." AND fl_programa=".$fl_programa." AND no_grado=".$no_grado." AND fg_fame='0' ORDER BY no_orden";
          // $ruta1 = ObtenConfiguracion(116)."/vannas_videos/campus/library/video_".$clave."_".$fl_programa."_".$no_grado;
          $ruta1 = $campus_url."/vanas_videos/campus/student_library";
        }
      }
      else{
        if(empty($clave)){
          $Queryx = "SELECT no_orden, nb_archivo FROM k_vid_content_temp WHERE fl_clave=".$fl_usuario." AND fl_programa=".$fl_usuario." AND no_grado=".$fl_usuario." AND fg_fame='1' ORDER BY no_orden";
          $ruta1 = $fame_url."/vanas_videos/fame/library/video_us".$fl_usuario."_us".$fl_usuario;
        }
        else{
          $Queryx = "SELECT no_orden, nb_archivo FROM k_vid_content_temp WHERE fl_clave=".$clave." AND fl_programa=".$fl_programa." AND fg_fame='1'  ORDER BY no_orden";
          $ruta1 = $fame_url."/vanas_videos/fame/library/video_".$clave."_".$fl_programa;
        }

      }
      $rsx = EjecutaQuery($Queryx);
      $tot_reg = CuentaRegistros($rsx);
      if(!empty($tot_reg)){
        for($i=0;$rowx=RecuperaRegistro($rsx);$i++){
          $no_orden = $rowx[0];
          $nb_archivo = $rowx[1];
          $ruta2 = $ruta1."/video_".$no_orden;
          $ruta_hd = $ruta2."/video_".$no_orden."_hd";
          # Mostramos el archivo
          echo "
          <div class='col-sm-12 col-md-12 col-lg-3 padding-5'>
            <div id='myCarousel-2' class='carousel slide'>
              <div class='carousel-inner'>
                <!-- Slide 1 -->
                <div class='item active'>
                  <img src='".$ruta_hd."/img_1.png' class='cursor-pointer'>
                  <div class='carousel-caption caption-right txt-color-red bg-color-white no-padding' style='opacity:0.8;'>
                    <h4>".ObtenEtiqueta(2237)."</h4>                              
                  </div>
                </div>
              </div>
            </div>
          </div>";
        }
      }
    }


    # Videos que estan existentes
    if(!empty($clave)){
      if(empty($fg_fame)){        
        $rss = EjecutaQuery("SELECT ds_ruta_video, fl_video_contenido, ds_progreso,ds_title_vid, ds_duration FROM k_video_contenido 
                      WHERE cl_pagina=$clave AND fl_programa=$fl_programa AND no_grado=$no_grado 
                      ORDER BY fl_video_contenido");
        $ruta0 = $campus_url."/vanas_videos/campus/student_library/";
        // $ruta0 = ObtenConfiguracion(116)."/vanas_videos/campus/library/video_".$clave."_".$fl_programa."_".$no_grado;
        $lib_progreso = PATH_MODULOS."/content/library_progress.php";
      }
      else{
        $rss = EjecutaQuery("SELECT ds_ruta_video, fl_video_contenido_sp, ds_progreso, ds_title_vid, ds_duration FROM k_video_contenido_sp 
                    WHERE cl_pagina_sp=$clave AND fl_programa_sp=$fl_programa
                    ORDER BY fl_video_contenido_sp");
        $ruta0 = $fame_url."/vanas_videos/fame/library/video_".$clave."_".$fl_programa;
        $lib_progreso = PATH_MODULOS."/fame/progreso_comando.php";
      }
      $no_videos =  CuentaRegistros($rss);            
      if($no_videos > 0) {        
        for($i = 1; $rows = RecuperaRegistro($rss); $i++){
          $ds_ruta_video = $rows[0];
          $fl_video_contenido = $rows[1];
          $ds_progreso = $rows[2];
          $ds_title_vid = str_texto($rows[3]);
          $ds_duration = $rows[4];
          // if(empty($ds_duration) || $ds_duration == ""){
            $rutaf = VID_CAM_STU_LIB."/video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/".array_shift(explode('.',$ds_ruta_video)).".m3u8";
            $ds_duration = VideoDuration(VIDEOS_CMD_HLS, $rutaf, $segundos=false, "CSL", $fl_video_contenido);
          // }
          $ruta = $ruta0."/video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/img_1.png";          
          $handle = @fopen($ruta,'r');
          if($handle !== false)
            $ruta = $ruta; 
          else
             $ruta = ObtenConfiguracion(121)."/images/PosterFrame_White.jpg"; 
          
          
          if(empty($fg_fame)){
            $preview = PATH_MODULOS."/programs/preview_flv.php?archivo=".$ds_ruta_video."&clave=".$clave."&fg_tipo=SL&p=".$fl_programa."&g=".$no_grado."&vid=".$fl_video_contenido."&ac=\"V\"&fg_fame=$fg_fame";
          }
          else{
            $preview = PATH_MODULOS."/fame/preview_flv.php?archivo=".$ds_ruta_video."&clave=".$clave."&fg_tipo=SL&p=".$fl_programa."&vid=".$fl_video_contenido."&ac=\"V\"&fg_fame=$fg_fame";
          }
          Forma_CampoOculto('total_convertido'.$fl_video_contenido, $ds_progreso);
          echo "
          <style>           
            [data-progressbar-value]::after{
              content: ''
            }
            [data-progressbar-value]::before{
              content: ''
            }
          </style>
          <div class='col-sm-12 col-md-12 col-lg-3 padding-5'>
            <div id='myCarousel-2' class='carousel slide'>
              <div class='carousel-inner'>
                <!-- Slide 1 -->
                <div class='item active'>
                  <img src='".$ruta."' class='cursor-pointer'>                  
                  <div class='carousel-caption caption-right no-padding'>
                    <div class='no-padding'>
                      <div class='progress' data-progressbar-value='".$ds_progreso."' id='grl_progress".$fl_video_contenido."' style='height:18;'><div class='progress-bar' id='progress_hls".$fl_video_contenido."'>".$ds_progreso."%</div></div>
                    </div>
                    <div class='padding-10'>
                    <a href='".$preview."' class='btn btn-primary btn-circle' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(2033)."' data-html='true' target='_blank'><i class='fa fa-eye'></i></a>
                    <a href='javascript:inf_vid(1, ".$fl_video_contenido.", ".$fg_fame.");' class='btn btn-primary btn-circle mouse' rel='tooltip' data-placement='top' data-original-title='Informacion' data-html='true' target='_blank'><i class='fa fa-info'></i></a>
                    <script>
                    function inf_vid(action=1, vid=0, fame=0){
                      var el_div = document.getElementById('div_title_vid'+vid);
                      var elemt = document.getElementById('ds_title_vid'+vid);
                      var ele_v = elemt.value;
                      var canc = document.getElementById('cancel'+vid);
                      var save = document.getElementById('save'+vid);
                      var editar = document.getElementById('editar'+vid);
                      title(vid, fame=0);
                      if(action==1 || action==2){                        
                        if(elemt.hasAttribute('disabled')){
                          elemt.removeAttribute('disabled');
                          canc.classList.remove('hidden');
                          save.classList.remove('hidden');
                          editar.classList.add('hidden');
                        }
                        else{
                          elemt.setAttribute('disabled', 'disabled');
                          canc.classList.add('hidden');
                          save.classList.add('hidden');
                          editar.classList.remove('hidden');
                        }
                      }
                      if(action==3){
                        if(ele_v.length==0){                          
                          el_div.classList.add('has-error');
                        }
                        else{                          
                          $.ajax({
                            type: 'POST',  
                            url : 'videos_library.php',
                            data: 'accion=3&fg_fame='+fame+'&vid='+vid+'&ds_title_vid='+ele_v
                          });
                          elemt.classList.remove('has-error');
                          inf_vid(1,vid);
                        }
                      }
                    }
                    function title(vid, fame=0){
                      var elem = document.getElementById('ds_title_vid'+vid);
                      $.ajax({
                        type: 'POST',  
                        url : 'videos_library.php',
                        data: 'accion=4&fg_fame='+fame+'&vid='+vid,
                        success:function(html){
                          elem.value='';
                          elem.value=html;
                        }
                      });
                    }
                    </script>
                    <a href='javascript:void(0);' onclick='delete_vid(".$clave.",".$fl_programa.",".$no_grado.",".$fl_video_contenido.", ".$fg_error.", ".$fg_fame.")' class='btn btn-danger btn-circle' class='btn btn-primary btn-circle' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(2034)."' data-html='true'><i class='fa fa-times'></i></a>
                    </div>                    
                  </div>                  
                </div>
              </div>
            </div> 
            <div class='smart-form padding-top-10' id='div_title_vid".$fl_video_contenido."' style='height:55px;'>
              <section>
                <label class='input'>                  
                  <input type='text'  id='ds_title_vid".$fl_video_contenido."' name='ds_title_vid".$fl_video_contenido."' placeholder='".ObtenEtiqueta(2201)."' disabled='disabled' value='".$ds_title_vid."'>                  
                  <i id='cancel".$fl_video_contenido."' onclick='inf_vid(2,".$fl_video_contenido.", 0);' class='fa fa-times txt-color-red cursor-pointer hidden' style='font-size:20px; padding-let:10px;'></i>
                  <i id='save".$fl_video_contenido."' onclick='inf_vid(3,".$fl_video_contenido.", 0);' class='fa fa-check txt-color-green cursor-pointer hidden' style='font-size:20px; padding-let:15px;'></i>
                  <i id='editar".$fl_video_contenido."' class='fa fa-pencil cursor-pointer' onclick='inf_vid(1, ".$fl_video_contenido.", 0);'></i>
                </label>
                
              </section>       
            </div>
          </div>
          
          <script>          
           pageSetUp();
          var progreso_vid = '".$ds_progreso."';
          if(progreso_vid<100 || $('#total_convertido".$fl_video_contenido."').val()<100){
            setInterval(function(){
            $.ajax({
                type: 'GET',  
                url : '".$lib_progreso."',
                data: 'clave=".$clave."'+
                      '&fl_programa=".$fl_programa."'+
                      '&no_grado=".$no_grado."'+
                      '&fl_vid_cont=".$fl_video_contenido."&fg_fame=".$fg_fame."&fg_tipo=Sl'
              }).done(function(result){
              var content, error, progress, vid;
              content = JSON.parse(result);
              progress = content.progress;
              vid =  content.fl_vid_cont;
              // if(!content.error){
                $('#grl_progress".$fl_video_contenido."').attr('data-progressbar-value', progress);
                $('#progress_hls".$fl_video_contenido."').empty().append(progress + '%');
                $('#total_convertido".$fl_video_contenido."').empty().val(progress);                      
              // }
          });
            
          }, 
          2000);
        }
        </script>";
        }
      $vid_totales = $no_videos + $tot_reg;
      echo "
      <script>
      $(function() {
        $('#tot_vid_csl_v').empty().append('".ObtenEtiqueta(2029)." (". $vid_totales .")');
        $('#tot_vid_csl_g').empty().append('".ObtenEtiqueta(2031)." (". $vid_totales .")');
      });
      </script>";
      }
    }
    if(empty($fg_fame))
      $del = "videos_library.php";
    else
      $del = PATH_MODULOS."/content/videos_library.php";
    echo
    '<script>
    function delete_vid(cla,pro,grade,video, error, fame){
      var conf = confirm("'.ObtenEtiqueta(2200).'");
      if(conf==true){        
        $.ajax({
          type: "POST",
          url : "'.$del.'",
          data: "clave="+cla+"&pro="+pro+"&grade="+grade+"&fl_vid_cont="+video+"&accion=2&fg_fame="+fame,
          success: function(){
            videos(cla,pro,grade,error);
          }
        });
      }
    }
    </script>';
    
  }
  # Eliminamos un video
  # Eliminamos de la BD y el archivo fisico
  if($accion==2){
    $fl_video_contenido = RecibeParametroNumerico('fl_vid_cont');
    if(empty($fg_fame)){
      # Liminamos el archivo fisico
      // $ruta = VID_CAM_STU_LIB."/video_".$clave."_".$fl_programa."_".$no_grado;
      $ruta = VID_CAM_STU_LIB;
      # Carpeta a eliminar
      $file_delete = $ruta."/video_".$fl_video_contenido;
      # Eliminamos carpeta
      eliminarDirec($file_delete);
      # Eliminamos datos de la BD
      EjecutaQuery("DELETE FROM k_video_contenido WHERE  fl_video_contenido=".$fl_video_contenido);
    }
    else{
      # Liminamos el archivo fisico
      $ruta = VID_FAME_STU_LIB."/video_".$clave."_".$fl_programa;
      # Carpeta a eliminar
      $file_delete = $ruta."/video_".$fl_video_contenido;
      # Eliminamos carpeta
      eliminarDirec($file_delete);
      # Eliminamos datos de la BD
      EjecutaQuery("DELETE FROM k_video_contenido_sp WHERE  fl_video_contenido_sp=".$fl_video_contenido);

    }
  }
  
  # Guarda titlo del video
  if($accion==3){
    $ds_title_vid = RecibeParametroHTML('ds_title_vid');
    $fl_video_contenido = RecibeParametroHTML('vid');
    if(empty($fg_fame))
      $Query = "UPDATE k_video_contenido SET ds_title_vid='".$ds_title_vid."' WHERE fl_video_contenido=".$fl_video_contenido;
    else
      $Query = "UPDATE k_video_contenido_sp SET ds_title_vid='".$ds_title_vid."' WHERE fl_video_contenido_sp=".$fl_video_contenido;
    
    EjecutaQuery($Query);
  }
  
  # Envia el titlo del video 
  if($accion==4){
    $fl_video_contenido = RecibeParametroHTML('vid');
    if(empty($fg_fame))
      $Query = "SELECT ds_title_vid FROM k_video_contenido WHERE fl_video_contenido=".$fl_video_contenido;
    else
      $Query = "SELECT ds_title_vid FROM k_video_contenido WHERE fl_video_contenido_sp=".$fl_video_contenido;
    
    $row = RecuperaValor($Query);
    echo $ds_title_vid = str_texto($row[0]);
    
  }
  
?>