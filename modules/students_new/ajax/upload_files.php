<?php
  # Librerias
  require("../../common/lib/cam_general.inc.php");  
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  // if(!ValidaPermisoCampus(FUNC_ALUMNOS) || !ValidaPermisoCampus(FUNC_MAESTROS)) {
    // MuestraPaginaError(ERR_SIN_PERMISO);
    // exit;
  // }
  
  # Recibe Parametros
  $fl_alumno = RecibeParametroNumerico('fl_alumno');
  $fl_leccion = RecibeParametroNumerico('fl_leccion');
  $fl_usu_upload = RecibeParametroNumerico('fl_usu_upload');  
  $fg_fame = RecibeParametroNumerico('fg_fame');
  $fg_accion = RecibeParametroNumerico('fg_accion');  
  
  # Tipos de archivos
  $file_tiposDro = "image/*, application/*";
  $file_tipos = array('jpg', 'JPG', 'png', 'PNG', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'txt', 'rar', 'zip', 'psd', 'gif');
  
  # Presenta Forma
  if($fg_accion==1){
    # Frm
    echo '
     <!-- Header -->
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridModalLabel">
          <i class="fa fa-exclamation-triangle"></i> <strong>'.ObtenEtiqueta(2208).'</strong>
        </h4>
      </div>
      <!-- Body -->
      <div class="modal-body">
        <div class="row padding-10">
          <div class="col col-sm-12 col-lg-1 col-md-12"></div>
          <div class="col col-sm-12 col-lg-10 col-md-12">
            <div class="widget-body padding-10">
              <form id="upload-zone" role="form" action="'.PATH_N_ALU_PAGES.'/upload_files.php" method="post" class="dropzone">
                <input name="archivo" id="archivo-1213"  type="hidden">
                <input name="fg_accion" id="fg_accion" value="2" type="hidden">
                <input name="fg_fame" value="'.$fg_fame.'" type="hidden">
                <input name="fl_alumno" id="fl_alumno" value="'.$fl_alumno.'" type="hidden">
                <input name="fl_leccion" id="fl_leccion" value="'.$fl_leccion.'" type="hidden">
                <input name="fl_usu_upload" id="fl_usu_upload" value="'.$fl_usu_upload.'" type="hidden">                
                <input name="ds_version" id="ds_version-01" type="hidden">
                <input name="ds_descripcion" id="ds_descripcion-01" type="hidden">
              </form>            
            </div>
            <div class="col-md-12 text-center hidden" id="arch_repetido">
                    <h5 class="alert alert-danger">'.ObtenEtiqueta(2345).'</h5>
            </div>
            <div class="col-md-12" id="input_ver">
              <label class="control-label"><strong>'.ObtenEtiqueta(2212).'</strong></label>
              <input type="text" class="form-control" name="ds_ver" id="ds_ver" onkeydown="frm_files()" onkeyup="frm_files()">
            </div>
            <div class="col-md-12" id="input_des">
              <label class="control-label"><strong>'.ObtenEtiqueta(2213).'</strong></label>
              <textarea class="form-control" name="ds_descr" id="ds_descr" onkeydown="frm_files()" onkeyup="frm_files()"></textarea>
            </div>
          </div>
          <div class="col col-sm-12 col-lg-1 col-md-12"></div>
        </div>
      </div>      
      <!-- FOOTER -->
      <div class="modal-footer text-align-center">    
        <div class="col-sm-12 col-lg-12">
          <a href="javascript:void(0);" class="btn btn-default" data-dismiss="modal">
            <i class="fa fa-times-circle-o-up"></i> '.ObtenEtiqueta(14).'
          </a>
          <a id="btns-files" class="btn btn-primary btns-files disabled"><i class="fa fa-arrow-circle-o-up"></i>&nbsp;<span>'.ObtenEtiqueta(2209).'</span></a>
        </div>
      </div>
      <script>
      pageSetUp();
      loadScript("'.PATH_N_COM_JS.'/plugin/dropzone/dropzone.min.js", function(){
         var dropzone1 = $("#upload-zone").dropzone({
          url: "'.PATH_N_ALU_PAGES.'/upload_files.php",
          paramName: "qqfile",
          autoProcessQueue: false,
          addRemoveLinks : true,
          maxFilesize: 1024,            
          // acceptedFiles: "'.$file_tiposDro.'",
          maxFiles: 20,
          init: function(file) {
            var drop = this;
            $("#btns-files").on("click", function(){
              drop.processQueue();
            });
            drop.on("addedfile", function(file) {
              // agregramos el nombre del archivo al campo
              $("#archivo-1213").val(file.name);
              frm_files();
            });            
          },
          removedfile: function(file) {
            $("#archivo-1213").val("");
            file.previewElement.remove();
            frm_files();
          },
          sending: function(){
            // enviamos la informacion de la version y descripcion a los campos ocultos
            $("#ds_version-01").val($("#ds_ver").val());
            $("#ds_descripcion-01").val($("#ds_descr").val());
          },
          success: function(file, result){
            var resultado, error;
						resultado = JSON.parse(result);
            error = resultado.error;

            if(error==true){

                //le decimos que el archivo ya se encuentra tiene que remombrar.
                $("#arch_repetido").removeClass("hidden");

            }else{
                $("#arch_repetido").addClass("hidden");

                // Cerramos y actualizamos la tabla
                if(error===false){
                  $("#modal-frm-files").modal("toggle"); 
                  $("#dt_works").DataTable().ajax.reload();
                }
            }

          }
        });
      });
      function frm_files(){
        var archivo = $("#archivo-1213").val();
        var Alength = archivo.length;
        var ver = $("#ds_ver").val();
        var Vlength = ver.length;
        var desc = $("#ds_descr").val();
        var Dlength = desc.length;
        var btn = $("#btns-files");
        var frm = $("#upload-zone");
        var Iversion = $("#input_ver");
        var Idescr = $("#input_des");
        // correcto
        if(Alength>1 && Vlength>1 && Dlength>1){
            btn.removeClass("disabled");
            frm.removeAttr("style");
            Iversion.removeClass("has-error");
            Iversion.addClass("has-success");
            Idescr.removeClass("has-error");
            Idescr.addClass("has-success");
        }
        else{
          btn.addClass("disabled");
          if(Alength==0){
            frm.css("background-color", "#ff00008a");
            Iversion.addClass("has-error");
            Idescr.addClass("has-error");
          }
          else{
            frm.removeAttr("style");
          }
          if(Vlength>1){
            Iversion.removeClass("has-error");
            Iversion.addClass("has-success");
          }
          else{
            Iversion.addClass("has-error");
            Iversion.removeClass("has-success");
          }
          if(Dlength>1){
            Idescr.removeClass("has-error");
            Idescr.addClass("has-success");
          }
          else{
            Idescr.addClass("has-error");
            Idescr.removeClass("has-success");
          }
        }
        
      }
      </script>
      <style>
        .dropzone .dz-default.dz-message{
          background-image:url("'.SP_IMAGES.'/dropzone-working-files.png");
        }
        .dz-error-message, .dropzone-previews .dz-preview .dz-error-message{
          opacity:1 !important;
        }
      </style>';
  }

  # Upload file
  # Ruta de los archivos extras de los students
  $ruta_std = PATH_ALU_WORKS_F."/works_".$fl_alumno;
  if($fg_accion==2){
    # Recibe parametros
    $archivo = RecibeParametroHTML('archivo');
    $ext = strtolower(ObtenExtensionArchivo($archivo));
    $ds_version = RecibeParametroHTML('ds_version');
    $ds_descripcion = RecibeParametroHTML('ds_descripcion');
    
	 
	#Elimnamos saltos de linea/para evitar erroes en los datatable.
    $ds_descripcion = preg_replace("/[\r\n|\n|\r]+/", " ", $ds_descripcion);
	
    # Agregamos caracteres al nombre del archivo para identificarlo
    $archivo1 = explode(".", $archivo);
    # NEW NAME FAILE
    $archivo_new = $archivo1[0].".".$ext; // lo subio el estudiante antes asi 19_ene_2019 mjd $archivo_new = $archivo1[0]."-".$fl_alumno."-".$fl_leccion.".".$ext; // lo subio el estudiante    

    #Si existe el archivo se renombra.
    if (file_exists($ruta_std."/".$archivo_new)) {

        $res=true;

        //$archivo_new = $archivo1[0].rand(10,100).".".$ext; 



    }else{



            # Nombre original del archivo
            $file_name_ori = $_FILES['qqfile']['name'];
            # Nombre de la official
            $tempFile = $_FILES['qqfile']['tmp_name'];    
        
            # Creamos la carpeta si no existe
            if (!file_exists($ruta_std)) {
                mkdir($ruta_std, 0777, true);
            }
        
            # uplad file
            // $upload = rename($url_handleUpload.$archivo, $ruta_std."/".$archivo_new);
            $upload = move_uploaded_file($tempFile, $ruta_std."/".$archivo_new);
            if($upload){
                $row = RecuperaValor("SELECT COUNT(*) FROM k_worksfiles WHERE fl_alumno=".$fl_alumno." AND fl_leccion=".$fl_leccion." AND fg_campus='1'");
                $no_orden = $row[0]+1;        
                $Query  = "INSERT INTO k_worksfiles(fl_alumno, fl_leccion, ds_files, ds_version, ds_descripcion, fe_file, no_orden, fg_campus, fl_usu_upload) ";
                $Query .= "VALUES($fl_alumno, $fl_leccion, '".$archivo_new."', '".$ds_version."', '".$ds_descripcion."', NOW(), $no_orden, '1', $fl_usu_upload) ";
                $folio = EjecutaInsert($Query);
                if($fl_alumno==$fl_usu_upload)
                    $fl_usu_get = ObtenMaestroAlumno($fl_alumno);
                else
                    $fl_usu_get = $fl_alumno;
            
                # Enviamos email
                document_wrokfiles($folio, $fl_usu_get, 146);
            
                $res = false;
            }
            else{
                $res = true;
            }


    }
    
    $result["error"] = $res;
    echo json_encode((Object) $result);
  }
  
  # Elimina  el archivo
  if($fg_accion==3){
    # Recibe Parametros
    $fl_worksfiles =RecibeParametroNumerico('fl_worksfiles');
    
    $rw = RecuperaValor("SELECT ds_files FROM k_worksfiles WHERE fl_worksfiles=".$fl_worksfiles);
    $ds_files = $rw[0];
    
    # Si existe el archivo lo eliminamos
    $rt = $ruta_std."/".$ds_files;
    if(file_exists($rt)){
      $del = unlink($rt);
      if($del==true){
        EjecutaQuery("DELETE FROM k_worksfiles WHERE fl_worksfiles=".$fl_worksfiles);
        $eliminado = true;
      }
      else
        $eliminado = false;
    }
    else{
      $eliminado = false;
    }
    
    $result["success"] = $eliminado;
    echo json_encode((Object) $result);
  }
?>