<?php
  # Libreria de funciones	
	require("../lib/self_general.php");

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Recibe Parametros
  $clave=RecibeParametroNumerico('clave');
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_nuevo_creado');
  $cl_pagina = RecibeParametroNumerico('cl_pagina_creada');
  
  if(empty($cl_pagina)){
      
      #Recuperamos el nombre del programa y asignamos a pagina, esto para mantener integridad de los datos y poder mostrar los archivos del student library.
      $Query="SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp=$fl_programa ";
      $row=RecuperaValor($Query);
      $nb_programa=str_texto($row[0]);

      #Realizamos el insert de la pagina
      $Queryp="INSERT INTO c_pagina_sp(fl_programa_sp,nb_pagina,ds_pagina,ds_titulo,tr_titulo,ds_contenido,tr_contenido,fg_fijo)";
      $Queryp.="VALUES ($fl_programa_sp, '$nb_programa', '', '', '', '', '', '0') ";
      $cl_pagina = EjecutaInsert($Queryp);
  }



  $_POST[''];
  # Obtenemos el intituto del alumno
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Tipos de archivos
  $file_tiposDro = "image/*, application/*, rar, zip";
  $file_tipos = array('jpg', 'JPG', 'png', 'PNG', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'txt', 'rar', 'zip', 'psd', 'gif');
  ?>
  <style>
  
  .dropzone .dz-preview .dz-details, .dropzone-previews .dz-preview .dz-details {
    width: 100% !important;
	height: 42px !important;
  }
  
  
  </style>
  
  <!-- Button trigger modal -->
  <button type="button " id="btn_asigment_student_library" class="btn btn-primary hidden" data-toggle="modal" data-target="#exampleModalLong">
	Launch demo modal
  </button>
  
  <!-- Modal -->
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-exclamation-triangle"></i> <strong>Student Library</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -17px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  
			<div class="row padding-10">
				<div class="col col-sm-12 col-lg-1 col-md-12"></div>
				<div class="col col-sm-12 col-lg-10 col-md-12">
					<div class="widget-body padding-10">
					  <form id="upload-zone-fame" role="form" action="upload_files_student_library.php" method="post" class="dropzone">
					  
					    <input name="archivo" id="archivo-1213"  type="hidden">
						<input name="fl_programa_sp_1" id="fl_programa_sp_1" value="<?php echo $fl_programa_sp;?>" type="hidden">
						<input name="cl_pagina_1" id="cl_pagina_1" value="<?php echo $cl_pagina;?>" type="hidden">						
						<input name="ds_descr_1" id="ds_descr_1" value="" type="hidden">
						<input name="ds_titulo_1" id="ds_titulo_1" value="" type="hidden">
                        <input name="fg_accion_1" id="fg_accion_1" value="1" type="hidden">
					  
					  </form> 
                        
						
						<div class="col-md-12 text-center hidden" id="arch_repetido">
								<h5 class="alert alert-danger"><?php echo ObtenEtiqueta(2345);?></h5>
						</div>
						
						<div class="row">
							<div class="col-md-2">&nbsp;</div>
						
								<div class="col-md-8" id="input_ver">
												<div class="smart-form">								
													<?php FAMEInputText(ObtenEtiqueta(2360),'ds_titulo_img',$ds_titulo_img,true,'','','','validatest();'); ?>
												</div>  
								</div>
							<div class="col-md-2">&nbsp;</div>	
						</div>
						<div class="row">
							<div class="col-md-2">&nbsp;</div>
						
								<div class="col-md-8" id="input_des">

								  <label class="control-label"><strong><?php echo ObtenEtiqueta(2220);?></strong></label>
								  <textarea class="form-control" name="ds_descr" id="ds_descr"></textarea>
								</div>
							<div class="col-md-2">&nbsp;</div>
						</div>
					  
					</div>
				</div>
				<div class="col col-sm-12 col-lg-1 col-md-12"></div>
			</div>	
				
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancel_ulo" data-dismiss="modal"><i class="fa fa-times-circle-o-up"></i> <?php echo ObtenEtiqueta(14);?></button>
        <button type="button"  id="btns-files" class="btn btn-primary btns-files"><i class="fa fa-arrow-circle-o-up"></i>&nbsp;<span><?php echo ObtenEtiqueta(2216);?> </span></button>
      </div>
    </div>
  </div>
</div>
  
  <script>
      //colocamos la pagina creada.
      $("#cl_pagina_creada").val(<?php echo $cl_pagina;?>);
     
         var dropzone1 = $("#upload-zone-fame").dropzone({
			  
          url: "site/upload_files_student_library.php",
          paramName: "qqfile",
          autoProcessQueue: false,
          addRemoveLinks : true,
          maxFilesize: 1024,            
          // acceptedFiles: "'.$file_tiposDro.'",
          maxFiles: 1,
          init: function(file) {
            var drop = this;
            $("#btns-files").on("click", function(){

                //validamos que este el nombre del archivo.
                var input = document.getElementById('ds_titulo_img').value;

                if (input.length == 0) {
                    $('#ds_titulo_img_input_error').addClass('state-error');
                    $('#ds_titulo_img_texto_error').removeClass('hidden');
                } else {
                    $('#ds_titulo_img_texto_error').addClass('hidden');
                    $('#ds_titulo_img_input_error').removeClass('state-error');

                    drop.processQueue(); //envia la imagen.
                }            
            });
            drop.on("addedfile", function(file) {
              // agregramos el nombre del archivo al campo
                $("#archivo-1213").val(file.name);
                //validamos que este el nombre del archivo.
                var input = document.getElementById('ds_titulo_img').value;

                if (input.length == 0) {
                    $('#ds_titulo_img_input_error').addClass('state-error');
                    $('#ds_titulo_img_texto_error').removeClass('hidden');
                } else {
                    $('#ds_titulo_img_texto_error').addClass('hidden');
                    $('#ds_titulo_img_input_error').removeClass('state-error');
                }

            
            });            
          },
          removedfile: function(file) {
              $("#archivo-1213").val("");
              $("#arch_repetido").addClass("hidden");
              file.previewElement.remove();


          },
          sending: function(){
            // enviamos la informacion de la titulo y descripcion a los campos ocultos
            $("#ds_descr_1").val($("#ds_descr").val());
            $("#ds_titulo_1").val($("#ds_titulo_img").val());
          },
          success: function(file, result){
            var resultado, error;
						resultado = JSON.parse(result);
				error = resultado.error;
				if (error == true) {

				    if (resultado.err_imagen_repetida == 1) {

				        //le decimos que el archivo ya se encuentra tiene que remombrar.
				        $("#arch_repetido").removeClass("hidden");
				    } else {
				        $("#arch_repetido").addClass("hidden");
				    }
	
				    //Indica que falta el titutlo a ala imagen
				    if (resultado.err_titulo == 1) {
				        $('#ds_titulo_img_input_error').addClass('state-error');
				        $('#ds_titulo_img_texto_error').removeClass('hidden');

				    } else {
				        $('#ds_titulo_img_input_error').removeClass('state-error');
				        $('#ds_titulo_img_texto_error').addClass('hidden');
				    }


				}else{
				
					// Cerramos y actualizamos la tabla
					if(error===false){
					  
					   document.getElementById("cancel_ulo").click();
					  // $("#exampleModalLong").modal("toggle");
					  //	$("#exampleModalLong").modal('hide');
						
                     
					   $("#tbl_users").DataTable().ajax.reload();
					    // zoom thumbnails and add bootstrap popovers
					    // https://getbootstrap.com/javascript/#popovers
					   $('[data-toggle="popover"]').popover({
					       container: 'body',
					       html: true,
					       placement: 'auto',
					       trigger: 'hover',
					       content: function () {
					           // get the url for the full size img
					           var url = $(this).data('full');
					           return '<img src="' + url + '" style="max-width:250px;">'
					       }
					   });

                        
						
					}
				}
				
				
				
				
				
          }
		  
			 			 
		});

  </script>
  
  
  
  
  <script>
  document.getElementById('btn_asigment_student_library').click();//clic automatico que se ejuta y sale modal

  function validatest() {

      var input = document.getElementById('ds_titulo_img').value;

      if (input.length > 0) {  
          $('#ds_titulo_img_texto_error').addClass('hidden');

      }


  }
	
  </script>
  
  
