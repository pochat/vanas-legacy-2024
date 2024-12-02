<style>
.dropzone .dz-preview.dz-processing .dz-progress, .dropzone-previews .dz-preview.dz-processing .dz-progress {
	display: none;
}
</style>
<input type='hidden' name='nb_archivo' id='nb_archivo' value=''>
<div class="row">
     <div class="col-md-6">
        <div class='dropzone' id='awardsdropzone' style='min-height: 100px;  background-image: url(../../images/dropzone_small.png) !important; background:no-repeat;  background-size: 100% 100%; width: 100%;height: auto;'>
        </div>	
     </div>
    <div class="col-md-6" id="presenta_imagenes" name="presenta_imagenes">

    </div>
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
								$('#{$nombre}').dropzone({
								url: 'upload_awards.php', ";                             
                                echo"params: {fl_instituto:$clave}, ";                               
                                echo "// data:  'id=1',
								addRemoveLinks : false,
								maxFilesize: 1024,
								acceptedFiles: '.png,.jpeg,.jpg',
								// Solo permite guardar un registro
								maxFiles: 5,           
								init: function() {
										this.on('error', function(file, message) { 
											alert('".ObtenEtiqueta(1239)."');
											this.removeFile(file); 
										});
									   }, 
										success: function(file,result){
										    

                                            PresentaImagenes($clave);

                                        var message, status;
										message = JSON.parse(result);		
										status = message.valores.status;
										nb_archivo=message.valores.nb_archivo;
                                            if(status==true){
												//document.getElementById('nb_archivo_').value = nb_archivo;
                                                 
											}else{
												alert('File already exists !');
												this.removeFile(file);
											}
										},
										// complete: function(file,result){
											// if(file.status == 'success'){
                                                // prev = $('#nb_archivos').val();
												// document.getElementById('nb_archivo_').value = file.name;
												// if (prev != '')
													// $('#nb_archivos').val(prev + ',' + file.name);
												// else
													// $('#nb_archivos').val(file.name);
										// }
										// },
										// error: function(file){
											// alert('Error subiendo el archivo ' + file.name);
										// },
										removedfile: function(file, serverFileName){
											var name = file.name;
											var element;
											(element = file.previewElement)!=null ? 
											element.parentNode.removeChild(file.previewElement) : 
												false;
												// alert('El elemento fué eliminado: ' + name); 
											}
										});
									})
									</script>";



?>	