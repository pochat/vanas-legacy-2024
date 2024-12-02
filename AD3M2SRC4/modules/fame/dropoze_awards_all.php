<style>
.dropzone .dz-preview.dz-processing .dz-progress, .dropzone-previews .dz-preview.dz-processing .dz-progress {
	display: none;
}
</style>
<input type='hidden' name='nb_archivo' id='nb_archivo' value=''>
    <div class="row">
        <div class="col-md-12">
			<?php 
			Forma_CampoTexto('Title', True, 'ds_titulo1', $ds_titulo, 25, 25);
            ?>
   
		</div>
    </div>
    <div class="row">
        <div class="col-md-4 text-right">
           <label class="control-label"><b>*Profile:</b></label>
		</div>		
        <div class="col-md-6">
             <div class="smart-form">		   
                <section>					
						<label class="checkbox">
							<input type="checkbox" name="ck1" id="ck1" >
							<i></i>Administrator/SuperAdmin</label>
						<label class="checkbox">
							<input type="checkbox" name="ck2" id="ck2" >
							<i></i>Teacher</label>
						<label class="checkbox">
							<input type="checkbox" name="ck3" id="ck3" >
							<i></i>Student</label>				
				</section>
			 </div>
        </div>
    </div>
    <br /><br />
	<div class="row">
	    <div class="col-md-3">&nbsp;</div>
	    <div class="col-md-5 text-center"> <form class='dropzone' id='awardsdropzone' style='min-height: 80px;  background-image: url(../../images/dropzone_small.png) !important; background:no-repeat;  background-size: 100% 100%; width: 100%;height: auto;'>
                                            <input name="ds_titulo" id="ds_titulo" value="<?php echo $ds_titulo;?>" type="hidden">
	                                        <input name="fl_perfil" id="fl_perfil" value="" type="hidden"/>
                                           </form>
        
           
         <br /> <button class="btn dropdown-toggle btn btn-primary" id="btn_dropzone">
              <i class="fa fa-check-circle" aria-hidden="true"></i> Upload
            </button>
        </div>
	    <div class="col-md-3">&nbsp;</div>
						
	</div>
	
  
<?php
$nombre="awardsdropzone";
echo "<script type='text/javascript'>

                            function PresentaImagenes(fl_instituto){
 
                                $.ajax({
                                    type: 'POST',
                                    url: 'upload_awards.php',
                                    data: 'fl_instituto=' + fl_instituto +
                                            '&fg_mostrar imagenes=1',                                          
                                    async: false,
                                    success: function (html) {
                                        $('#presenta_imagenes').html(html);
                                    }
                                });
                            }


							// DO NOT REMOVE : GLOBAL FUNCTIONS!
							$(document).ready(function() {
								pageSetUp();
								Dropzone.autoDiscover = false;
								  
         var dropzone1 = $('#awardsdropzone').dropzone({
			  
          url: 'upload_awards.php',
          paramName: 'file',
          autoProcessQueue: false,
          addRemoveLinks : true,
          maxFilesize: 1024,            
          maxFiles: 1,
          init: function(file) {
            var drop = this;

            $('#btn_dropzone').on('click', function(){

                //validamos que este el nombre del archivo.
                var input = document.getElementById('ds_titulo1').value;
                var fl_perfil = document.getElementById('fl_perfil').value;
                if ((input.length == 0)&&(fl_perfil)) {
                   // $('#ds_titulo_input_error').addClass('state-error');
                } else {
                  //  $('#ds_titulo_error').addClass('hidden');

                    drop.processQueue(); //envia la imagen.
                }            
            });
            drop.on('addedfile', function(file) {
              // agregramos el nombre del archivo al campo
                $('#archivo-1213').val(file.name);
                //validamos que este el nombre del archivo.
                var input = document.getElementById('ds_titulo1').value;

                if (input.length == 0) {
                 //   $('#ds_titulo_img_input_error').addClass('state-error');
                  //  $('#ds_titulo_img_texto_error').removeClass('hidden');
                } else {
                  //  $('#ds_titulo_img_texto_error').addClass('hidden');
                  //  $('#ds_titulo_img_input_error').removeClass('state-error');
                }

            
            });            
          },
          removedfile: function(file) {
             
              file.previewElement.remove();


          },
          sending: function(){
            // enviamos la informacion de la titulo y descripcion a los campos ocultos
            $('#ds_titulo').val($('#ds_titulo1').val());
          },
          success: function(file, result){
            var resultado, error;
						resultado = JSON.parse(result);
				error = resultado.error;
				if (error == true) {

				  
	
				    //Indica que falta el titutlo a ala imagen
				    if (resultado.err_titulo == 1) {
				      //  $('#ds_titulo_img_input_error').addClass('state-error');
				       // $('#ds_titulo_img_texto_error').removeClass('hidden');

				    } else {
				      //  $('#ds_titulo_img_input_error').removeClass('state-error');
				      //  $('#ds_titulo_img_texto_error').addClass('hidden');
				    }


				}else{
				
					// Cerramos y actualizamos la tabla
					if(error===false){
                      $('#ds_titulo').val('');
                      $('#ds_titulo1').val('');
                      document.getElementById('ck1').checked = false;
                      document.getElementById('ck2').checked = false;
                      document.getElementById('ck3').checked = false;
                      $('#fl_perfil').val('');
                      this.removeAllFiles();
					  file.previewElement.remove();
					   $('#example').DataTable().ajax.reload();
					  
						
                      // zoom thumbnails and add bootstrap popovers
			            // https://getbootstrap.com/javascript/#popovers
			            $('[data-toggle=\"popover\"]').popover({
			                container: 'body',
			                html: true,
			                placement: 'auto',
			                trigger: 'hover',
			                content: function() {
			                    // get the url for the full size img
			                    var url = $(this).data('full');
			                    return '<img src=\"+url+\" style=\"max-width:250px;\">'
			                }
			            });
	  
					  
					  

                        
						
					}
				}
				
				
				
				
				
          }
		  
			 			 
		});


										
								});
									</script>";



?>	
<script>
    //conponentes del segunto tab
    $(document).ready(function () {
        $('#ck1').change(function () {
            if ($('#ck1').is(':checked')) {//admin /superadmin
               
                document.getElementById("ck2").checked = false;
                document.getElementById("ck3").checked = false;
                $('#fl_perfil').val(13);
            }
        });
        $('#ck2').change(function () { //teacher
            if ($('#ck2').is(':checked')) {
                document.getElementById("ck1").checked = false;
                document.getElementById("ck3").checked = false;
                $('#fl_perfil').val(14);
            }
        });
        $('#ck3').change(function () { //students
            if ($('#ck3').is(':checked')) {
                document.getElementById("ck1").checked = false;
                document.getElementById("ck2").checked = false;
                $('#fl_perfil').val(15);
            }
        });
    });
</script>