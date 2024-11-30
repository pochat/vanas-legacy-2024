<?php

	# Recupera los datos de la entrega de la semana
  // $fl_grupo = ObtenGrupoAlumno($fl_usuario, $fl_programa);
  // $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana, $fl_programa);
  $Query  = "SELECT fl_entrega_semanal_sp, fg_entregado, fl_promedio_semana, fg_increase_grade ";
  $Query .= "FROM k_entrega_semanal_sp ";
  $Query .= "WHERE fl_alumno=$fl_alumno ";
  // $Query .= "AND fl_grupo=$fl_grupo ";
  $Query .= "AND fl_leccion_sp=$fl_leccion_sp";
  $row = RecuperaValor($Query);
  $fl_entrega_semanal_sp = $row[0];
  $fg_entregado = $row[1];
  // $ds_critica_animacion = str_uso_normal($row[2]);
  $fl_promedio_semana = $row[2];
  $fg_increase_grade = $row[3];
  
  # Revisa si ya existe un registro para esta semana
  if(empty($fl_entrega_semanal_sp)) {
    $Query = "INSERT INTO k_entrega_semanal_sp (fl_alumno,fl_leccion_sp) VALUES ($fl_alumno, $fl_leccion_sp) ";
    $fl_entrega_semanal_sp = EjecutaInsert($Query);
  }

  # Revisa si hay entregables para esta semana
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='$fg_tipo'");
  $tot_entregables = $row[0];

  # Cuando no se requiera referencia, buscar la ultima requerida y decir de que semana es
  if($tot_entregables == 0) {
    if((($fg_tipo == 'AR' AND empty($fg_ref_animacion)) OR ($fg_tipo == 'SR' AND empty($fg_ref_sketch))) AND $no_semana > 1) {
      $Query  = "SELECT max(no_semana), ds_titulo".$sufix;
      $Query .= " FROM c_leccion ";
      $Query .= "WHERE fl_programa=$fl_programa ";
      $Query .= "AND no_grado=$no_grado ";
      if($fg_tipo == 'AR')
        $Query .= "AND fg_ref_animacion='1' ";
      else
        $Query .= "AND fg_ref_sketch='1' ";
      $Query .= "AND no_semana < $no_semana";
      $row = RecuperaValor($Query);
      $no_semana_ant = $row[0];
      $ds_titulo_ant = str_uso_normal($row[1]);
      if(!empty($no_semana_ant)) {
        $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana_ant);
        $Query  = "SELECT fl_entrega_semanal ";
        $Query .= "FROM k_entrega_semanal ";
        $Query .= "WHERE fl_alumno=$fl_alumno ";
        $Query .= "AND fl_grupo=$fl_grupo ";
        $Query .= "AND fl_semana=$fl_semana";
        $row = RecuperaValor($Query);
        $fl_entrega_semanal = $row[0];
      }
    }
  }

  # Recupera los entregables
  $Query  = "SELECT ds_ruta_entregable, ds_comentario, fl_gallery_post_sp, a.fl_entregable_sp ";
  $Query .= "FROM k_entregable_sp a ";
  $Query .= "LEFT JOIN k_gallery_post_sp b ON b.fl_entregable_sp=a.fl_entregable_sp ";
  $Query .= "WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp ";
  $Query .= "AND fg_tipo='$fg_tipo' ";
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);

  $result["size"] = array();
  # Rutas videos  y archivos de los alumnos
  $ruta_video = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_alumno."/videos";
  $ruta_thumbs = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_alumno."/sketches/thumbs";
  $ruta_board_thumbs = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_alumno."/sketches/board_thumbs";
  
  # Mostramos los trabajos que son requeridos
  switch($fg_tipo){
    case"A": if(!empty($fg_animacion)) $requeridos=1; else $requeridos = 0; break;
    case"AR": if(!empty($fg_ref_animacion)) $requeridos=1; else $requeridos = 0; break;
    case"S": $requeridos=$no_sketch; break;
    case"SR": if(!empty($fg_ref_sketch)) $requeridos=1; else $requeridos = 0; break;
  }
  $msg = "<label class='text-danger' id='msg_title'><i class='fa fa-bell text-danger txt-color-red'></i> <strong>".ObtenEtiqueta(1147)."</strong> &nbsp;</label>";
  if($requeridos==$tot_entregables){
    $msg = "<label class='text-success' id='msg_title'><i class='fa fa-check'></i> <strong> ".ObtenEtiqueta(1148)." </strong>&nbsp;</label>";
  }
  
  
  # Presenta los entregables
  for($tot_entregables = 0; $row = RecuperaRegistro($rs); $tot_entregables++) {
    $ds_ruta_entregable = str_uso_normal($row[0]);
    $ds_comentario_alu = str_texto($row[1]);
    $fl_gallery_post_sp = $row[2];
    $ext = strtolower(ObtenExtensionArchivo($ds_ruta_entregable));
    $fl_entregable_sp = $row[3];

    # Reset assignments array
    $assignment = array();

    /* fl_gallery_post is included as a safety check in case that a student uploaded 
     * the file on the old campus, and there was no modal created on the new campus 
     * for this file. Modal view is prevented and will not appear.
     */
    // Old Campus upload
    if(empty($fl_gallery_post_sp)){
      # There is no commenting from old campus
      $no_comments = 0;

      // video
      if($ext == "ogg" || $ext == "m3u8" || $ext == "mp4"){
        $assignment["type"] = "video";
        $assignment["thumbnail"] = PATH_N_COM_UPLOAD."/gallery/thumbs/vanas-board-video-default.jpg";
        $assignment["src"] = $ruta_video."/".$ds_ruta_entregable;
      }
      // image
      else {
        $assignment["type"] = "image";
        $assignment["thumbnail"] = $ruta_thumbs."/$ds_ruta_entregable";
      }
      # Common old campus variables
      $assignment["campus"] = "old";
      $assignment["comments"] = $no_comments;
    }
    // New Campus upload
    else {
      # Find number of comments for this post
      $Query  = "SELECT COUNT(1) FROM k_gallery_comment_sp WHERE fl_gallery_post_sp=$fl_gallery_post_sp";
      $row2 = RecuperaValor($Query);
      $no_comments = $row2[0];

      // video
      if($ext == "ogg" || $ext == "m3u8" || $ext == "mp4"){
        $assignment["type"] = "video";
        $assignment["thumbnail"] = PATH_N_COM_UPLOAD."/gallery/thumbs/vanas-board-video-default.jpg";
      }
      // image
      else {
        $assignment["type"] = "image";
        $assignment["thumbnail"] = $ruta_board_thumbs."/$ds_ruta_entregable";
      }
      // # Common new campus variables
      $assignment["campus"] = "new";
      $assignment["comments"] = (int) $no_comments;      
      $assignment["fl_gallery_post"] = $fl_gallery_post_sp;      
    }
    $assignment["fl_entregable_sp"] = $fl_entregable_sp;
    $assignment["lbl_uploads"] = ObtenEtiqueta(1146);
    $assignment["complete_incompleted"] = $msg;
    
    $assignments[$tot_entregables] = $assignment;
  }
  $result["size"] += array("total_assignments" => $tot_entregables);
  if($tot_entregables > 0){
    $result["assignments"] = (Object) $assignments;
  }
  
  # Boton para marcar comocompletado
  // $result["btn_completed"] = $btn_completed;
  # Verificar si desea que suban archivos
  $result["files_requiered"] = $requeridos;
  # Etiqueta  para los que no necesitan
  $result["etq_requiered"] = ObtenEtiqueta(1900);

  # Revisa si ya se entrego la asignacion
  $ds_mensaje = "
  <div id='div_error'></div>
  <div class='panel panel-default smart-accordion-default'>
    <div class='panel-heading'>
      <h4 class='panel-title'>
       <a data-toggle='collapse' data-parent='#accordion' href='#collapseOne' aria-expanded='false' class='collapsed'> 
       <i class='fa fa-lg fa-fw fa-plus-circle txt-color-green pull-left' style='padding-top:8px;'></i>
       <i class='fa fa-lg fa-fw fa-minus-circle txt-color-red pull-left'  style='padding-top:8px;'></i>
       $msg<label id='msg_title_assig' style='font-size: 8pt;'>".ObtenEtiqueta(393)." (You have uploaded $tot_entregables / $requeridos required)</label>
      </a>
      <div class='pull-right'>";
      if($preview==0)
        $ds_mensaje .= btn_complete_desktop($fl_usuario, $fl_leccion_sp);      
   $ds_mensaje .= "</div>
      </h4>      
    </div>
    <div id='collapseOne' class='panel-collapse collapse' aria-expanded='false' style='height: 0px;'>
      <div class='panel-body padding-10'>";
  $ds_caja = "
        <div class='col col-xs-12 col-md-6 padding-5'>
          <div class='panel panel-default no-margin'>
            <div class='panel-heading'>
              <h6 class='panel-title'>";          
    // $ds_mensaje .= "Required Assignment. $no_sketch <br>Submission deadline is ".ObtenLimiteEntregaSemana($fl_alumno, $no_semana, $fl_programa).".";    
    if( ($fg_tipo == 'A' AND empty($fg_animacion)) OR ($fg_tipo == 'S' AND empty($no_sketch)) OR ($fg_tipo == 'AR' AND empty($fg_ref_animacion)) OR ($fg_tipo == 'SR' AND empty($fg_ref_sketch)) ){
    	$ds_caja .= "Not required for this lesson. ";
      $fg_dropzone = false;
    }
    else{      
      # Requerimientos de la entrega para Sketch
      if($fg_tipo == 'S' AND $tot_entregables < $no_sketch) {
        if($no_sketch == 1){
          $ds_caja .= "$no_sketch sketch is required for this lesson.";
        } else {
          $ds_caja .= "$no_sketch sketches are required for this lesson.";
        }
      }
      else{
        $ds_caja .= ObtenEtiqueta(2631);
      }
      $fg_dropzone = true;
    }
    $ds_caja .= "
              </h6>
            </div>
            <div class='panel-body'>";
    # Mensaje para las Assigments
    if($fg_tipo == 'A' AND !empty($fg_animacion))
      $ds_caja .= "<br>".$ds_animacion;
    # Mensaje para las Assigments Ref
    if($fg_tipo == 'AR' AND !empty($fg_ref_animacion))
      $ds_caja .= "<br>".$ds_ref_animacion;
    # Mensaje para las numero de skecth
    if($fg_tipo == 'S' AND !empty($no_sketch))
      $ds_caja .= "<br>".$ds_no_sketch;    
    # Mensaje para las nomero de skecth
    if($fg_tipo == 'SR' AND !empty($fg_ref_sketch))
      $ds_caja .= "<br>".$ds_ref_sketch;    
    $ds_caja .= "
            </div>
          </div>
        </div>";
  # Muestra el Dropzone y la caja de las intrucciones
  $ds_drozone=NULL;
  if($fg_dropzone){
      $placeholder = ObtenEtiqueta(2410);
      $ds_drozone.="
        <div class='col-sm-12 padding-10'>
            <label><b>Additional Comments Per File</b></label>
            <textarea id='comments-".$no_semana."-".$fg_tipo."' class='form-control' rows='2' placeholder='".$placeholder."'></textarea>
        </div>";
      $ds_drozone.="
        <div class='col col-sm-12 panel-footer bg-color-white'>
            <div class='col col-sm-1'>
                <!--<button id='upload-week-".$no_semana."-".$fg_tipo."' class='btn btn-primary'>
                <i class='fa fa-arrow-circle-o-up'></i><span>".ObtenEtiqueta(2409)."</span>
                </button>-->
            </div>
        </div>";
      $ds_drozone.= "
      <div class='col col-sm-12 col-md-6 padding-5'>
        <div class='widget-body'>
          <div class='panel-heading no-padding'>      
            <div id='thumb-container-".$no_semana."-".$fg_tipo."' class='no-border no-padding'></div>      
            </div>
            <form id='upload-zone-".$no_semana."-".$fg_tipo."' role='form' action='site/upload_fame.php' method='post' class='dropzone'>
              <input name='tipo' value='".$fg_tipo."' type='hidden'>
              <input name='semana' value='".$no_semana."' type='hidden'>
              <input name='archivo' id='archivo-".$no_semana."-".$fg_tipo."' type='hidden'>
              <input name='comentarios' id='comentarios-".$no_semana."-".$fg_tipo."' value='' type='hidden'>
              <input name='fl_programa' id='fl_programa-".$no_semana."-".$fg_tipo."' value='".$fl_programa."' type='hidden'>
            </form>
           </div>
        </div>".$ds_caja;
  } else {
    $ds_drozone = $ds_caja;
  }
  # Tipo de archivo
  if($fg_tipo == 'S'){
    $acceptedFileTypes = ".jpeg, .JPEG, .jpg, .JPG, .png, .PNG,";
  } else {
    $acceptedFileTypes = ".jpeg, .JPEG, .jpg, .JPG, .png, .PNG, .mov, .MOV, .mp4, .MP4, .avi, .AVI";
  }
  
  $ds_mensaje .= $ds_drozone."
  <script>
  const uploads = []
  pageSetUp();
  $(\"#upload-zone-".$no_semana."-".$fg_tipo."\").dropzone({
    url: 'site/upload_fame.php',
    parallelUploads: 1,";
    # Si solo es preview no podra subir archivos
    if($preview==1)
      $ds_mensaje .= "clickable: false, ";
  $ds_mensaje .= "    
    paramName: 'qqfile',
	parallelUploads: ".$requeridos.", 
    autoProcessQueue: false,
    addRemoveLinks : true,
    acceptedFiles: '".$acceptedFileTypes."',
    maxFiles: ".$requeridos.",
    dictDefaultMessage: '',
    dictResponseError: 'Error uploading file!',
    dictRemoveFile: 'Remove',
    init: function(){
      var dropzone = this;
      // setup the upload buttons for each dropzone
      $('#upload-week-".$no_semana."-".$fg_tipo."').on('click', function(){
        dropzone.processQueue();
      });
    },
    accept: function(file, done) {
            var dropzone = this;
            console.time('FileOpen');
            const reader = new FileReader();
            reader.addEventListener(\"loadend\", function(evt) {
                if (evt.target.readyState === FileReader.DONE){
                    const uint = new Uint8Array(evt.target.result)
                    let bytes = []
                    uint.forEach((byte) => { bytes.push(byte.toString(16)) })
                    const hex = bytes.join('').toUpperCase()
                    uploads.push({
                        filename: file.name,
                        filetype: file.type ? file.type : 'Unknown/Extension missing',
                        binaryFileType: getMimetype(hex),
                        hex: hex
                    })
                    if (file.type != getMimetype(hex)) {
                      $(file.previewElement).find('.dz-error-message').text('".ObtenEtiqueta(2637)."').css('opacity', '0.8').css('margin-left', '100px');
                    } else { 
                    done();
                    dropzone.processQueue();
                    }
                }
                console.timeEnd('FileOpen')
            })
            const blob = file.slice(0, 4);
            reader.readAsArrayBuffer(blob);
    },
    sending: function(file){
      var dropzone, id;
      dropzone = this;
      id = dropzone.element.id.replace('upload-zone', '');
      $('#archivo'+id).val(file.name);
      /*Tomamos el valor del campo de texto para enviarlos y guardarlos*/
      $('#comentarios'+id).val($('#comments'+id).val());
      
    },
    success: function(file, result){
      var dropzone, id, type, message, feedback, thumbContainer, numThumbs, currentNum, requeridos='".$requeridos."', div_error = $('#div_error');
      message = JSON.parse(result);

      // remove the uploaded file
      dropzone = this;
      dropzone.removeFile(file);

      id = dropzone.element.id.replace('upload-zone', '');
      type = id.replace(/-\d+-/, '');
      feedback = $('#uploaded-feedback'+id);
      feedback.text('');
            
      // Adds feedback to the user, checks the return values by falsey check
      if(message.error){
        div_error.empty().addClass('alert alert-danger').append('<strong style=\'padding-right:10px;\'>Error!</strong>  '+message.error);
      } else {
        $('#comments'+id).val('');
        feedback.attr('class', 'h5 text-primary');
        feedback.text(message.success);
        // Add thumbnail to list
        thumbContainer = $('#thumb-container'+id);
        numThumbs = $('#num-thumbs'+id);
        
        // Check if thumbnail row has been setup
        if(!thumbContainer.hasClass('uploaded-thumbnails')){
          thumbContainer.addClass('uploaded-thumbnails');
        }
        if(type == 'S'){
          // Plus one more to count
          currentNum = parseInt(numThumbs.text());
          numThumbs.text(currentNum+1);
        } else {
          numThumbs.text(1);
          thumbContainer.empty();
        }
        thumbContainer.append(
          '<div class=\'preview-container no-margin\'>'+
            '<div class=\'preview-thumbnail\'>'+
              '<img class=\'fill-block\' src=\''+message.thumbnail+'\'>'+
            '</div>'+
            '<span class=\'delete-me\' onclick=\'DeleteMe('+message.key+', this.parentNode);\' title=\'Delete me!\'><i class=\'fa fa-times\'></i></span>'+
          '</div>'
        );
        /** Aggamos estilos**/
        thumbContainer.css('overflow-y', 'inherit');
        thumbContainer.css('height', '95px');
        
        if(message.tot_entregados==requeridos){
          $('#msg_title').empty().removeClass('text-danger').addClass('text-success');
          $('#msg_title').append('<i class=\'fa fa-check\'></i> <strong> ".ObtenEtiqueta(1148)." </strong>&nbsp;');
          $('#msg_title_assig').empty().append('".ObtenEtiqueta(393)." (You have uploaded '+message.tot_entregados+' / ".$requeridos." required)');
        }
        else{
          $('#msg_title_assig').empty().append('".ObtenEtiqueta(393)." (You have uploaded '+message.tot_entregados+' / ".$requeridos." required)');
        }
        var ele_btn = $('#btn_session_".$fl_leccion_sp."');
        if(message.active_btn==1){
          if(message.fg_completa==1){
            ele_btn.removeClass('btn-danger').addClass('btn-success').empty().append('<span class=\"btn-label\"><i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i></span>".ObtenEtiqueta(1901)."');
          }
          else{
            ele_btn.removeClass('btn-success disabled').addClass('btn-danger').empty().append('<span class=\"btn-label\"><i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i></span>".ObtenEtiqueta(1902)."');
          }
        }
        else{          
            ele_btn.addClass('btn-danger disabled').empty().append('<span class=\"btn-label\"><i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i></span>".ObtenEtiqueta(1902)."');        
        }
        
        /**Enviamos al board**/
        socket.emit('new-gallery-post-fame', message.post,0,1,message.ruta_avatar_user_post,message.fe_post_formato,message.compania,message);
        /** Enviamos la notificacion maestro */
        if(message.fl_perfil=='".PFL_ESTUDIANTE_SELF."'){        
          socket.emit('new-notify-assigment', message.fl_maestro);
        }
      }
    }
  });
  
  const getMimetype = (signature) => {
            switch (signature) {
                case '89504E47':
                    return 'image/png'
                case '47494638':
                    return 'image/gif'
                case '25504446':
                    return 'application/pdf'
                case 'FFD8FFDB':
                case 'FFD8FFE0':
                case 'FFD8FFE1':
                    return 'image/jpeg'
                case '504B0304':
                    return 'application/zip'
                case '00018':
                case '0001C':
                    return 'video/mp4'
                case '00014':
                case '00020':
                    return 'video/quicktime'
                case '52494646':
                    return 'video/avi'
                case '38425053':
                    return 'image/vnd.adobe.photoshop';
                case 'DA4869':
                    return 'text/plain'
                case '2E6C6473':
                    return 'text/css'
                case 'EFBBBF23':
                    return 'text/csv'
                default:
                    return 'Unknown filetype'
            }
  }
  </script>";  
  $ds_mensaje .= "
      </div>
      <style>
.dropzone .dz-preview .dz-details, .dropzone-previews .dz-preview .dz-details{
  width: 100px;
height: 0px;
position: relative;
background: #ebebeb00;
padding: 5px;
margin-bottom: 0px;
}
</style>";
if($fg_tipo=='S'){
  $ds_mensaje .= "
  <style>
  .dropzone .dz-default.dz-message{
    background-image:url('../fame/img/spritemap_img.png');
  }
  </style>";
}
else{
  $ds_mensaje .= "
  <style>
  .dropzone .dz-default.dz-message{
    background-image:url('../fame/img/spritemap.png');
  }
  </style>";
}
  
  # Si existe una calificacion solo muestra los imagenes
  // comented because $fg_complete does not exist if($fl_promedio_semana>0 || $fg_completa==1){
  if($fl_promedio_semana>0){
    if(empty($fg_increase_grade)){
      $ds_mensaje   = "
      <div id='div_error'></div>
      <div class='panel panel-default smart-accordion-default'>
        <div class='panel-heading'>
          <h4 class='panel-title'>
            <a href='javascript:void(0);' class='collapsed'> 
              <i class='fa fa-lg fa-fw fa-check-square-o txt-color-green pull-left' style='padding-top:6px;'></i>
              <strong class='txt-color-red'>".ObtenEtiqueta(1862)."</strong> <label id='msg_title_assig' style='font-size: 8pt;'>".ObtenEtiqueta(393)." (You have uploaded $tot_entregables / $requeridos required)</label>
            </a>
            <div class='pull-right' style='margin-top: -3px;'>".btn_complete_desktop($fl_usuario, $fl_leccion_sp)."</div>
          </h4>      
        </div>
      </div>";
    }
    else{
      $ds_mensaje = "<div id='fg_increase_grade_div' class='alert alert-info fade in margin-5'>
				<i class='fa-fw fa fa-info'></i>
				<strong>".ObtenEtiqueta(1882)."</strong>
			</div> ".$ds_mensaje;      
    }
  }
  $ds_mensaje .= "
  <script>
    // Delete me
    function DeleteMe(entregable, target){
      var parent, week, id, numThumbs, currentNum, fl_programa, div_error;
      parent = target.parentNode;
      id = parent.id.replace('thumb-container', '');
      div_error = $('#div_error');
      week = Math.abs(parseInt(id));
      fl_programa = '".$fl_programa."';
      
      $.ajax({	
        type: 	'POST',
        url : 	'site/upload_fame_del.php',
        data: 	'entregable='+entregable+
                '&semana='+week+
                '&fl_programa='+fl_programa
      }).done(function(result){
        var message, feedback, requeridos='".$requeridos."';
        message = JSON.parse(result);
        feedback = $('#uploaded-feedback'+id);
        feedback.text('');

        // Adds feedback to the user, checks the return values by falsey check
        if(message.error){          
          div_error.empty().addClass('alert alert-danger').append('<strong style=\'padding-right:5px;\'>Error!</strong>'+message.error);
        } else {
          // remove the image div
          parent.removeChild(target);
          $('#fg_increase_grade_div').remove();
          div_error.empty().addClass('alert alert-success').append('<strong style=\'padding-right:5px;\'></strong>'+message.success);

          // subtract one from thumb counter
          numThumbs = $('#num-thumbs'+id);
          currentNum = parseInt(numThumbs.text());
          numThumbs.text(currentNum-1);

          feedback.attr('class', 'h5 text-success');
          feedback.text(message.success);
          $('#thumb-container-".$no_semana."-".$fg_tipo."').removeAttr('style');
          $('#thumb-container-".$no_semana."-".$fg_tipo."').removeAttr('class');
          
          if(message.tot_entregados==requeridos){
            $('#msg_title').empty().removeClass('text-danger').addClass('text-success');
            $('#msg_title').append('<i class=\'fa fa-check\'></i> <strong> ".ObtenEtiqueta(1148)." </strong>&nbsp;');
            $('#msg_title_assig').empty().append('".ObtenEtiqueta(393)." (You have uploaded '+message.tot_entregados+' / ".$requeridos." required)');
          }
          else{
            $('#msg_title').empty().removeClass('text-success').addClass('text-danger');
            $('#msg_title').append('<i class=\'fa fa-bell text-danger txt-color-red\'></i> <strong>".ObtenEtiqueta(1147)."</strong> &nbsp;');
            $('#msg_title_assig').empty().append('".ObtenEtiqueta(393)." (You have uploaded '+message.tot_entregados+' / ".$requeridos." required)');
          }
          
          /** Enviamos la notificacion maestro */
          if(message.fl_perfil=='".PFL_ESTUDIANTE_SELF."'){        
            socket.emit('new-notify-assigment', message.fl_maestro);
          }
        }
      });
    }
  </script>";
  
  $result["message"] = $ds_mensaje;
  
  echo json_encode((Object) $result);
?>
