<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");
	
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  
  $img = RecibeParametroHTML('img');
  
  ?>
 
  
   <!---======modal para cambiar lla foto-----=====--->

	 <div class='modal fade' id='change_avatar' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' 
			  style='overflow-y:scroll;overflow:auto'>
			  <div class="modal-dialog" role="document" style="width:600px; top:100px;">
				<div class="modal-content">
				  <div class="modal-body">
					<form class="form-horizontal" id="change_foto" method="post" enctype='multipart/form-data'>
					  <div class="row">
						<div class="col-md-9">
						  <div class="input-group">
							<span class="input-group-btn">
							  <span class="btn btn-primary btn-file">
								Browse <input type="file" id="ds_foto1" name="ds_foto1" accept='jpg|jpeg' maxlength="1" multiple>
							  </span>
							</span>
							<input type="text" class="form-control" id="valued"  readonly>
						  </div>
						</div>
						<div class="col-md-1">
						  <button type="submit" class="btn btn-primary disabled" id="btn_changer">Change</button>
						</div>
					  </div>                          
					</form>
				  </div>
				</div>
			  </div>
			  <input type="hidden" id="type_img" name="type_img" value="<?php echo $img; ?>" />
			</div>
  
<script>

 $('#change_avatar').modal('toggle');

 
 $(document).ready(function () {
	  /** Por dedault estara desactivado el boton **/
	  $("#btn_changer").addClass("disabled");



	  /** Activamos el botn si cargo un archivo **/
	  $("#ds_foto1").change(function () {
		  var file = $(this).val();

		  if (file != "") {
			  $("#btn_changer").removeClass("disabled");
			  $('#valued').val(file);
		  } else {
			  $("#btn_changer").addClass("disabled");
		  }

		  pageSetUp();
	  });

	  

	});


	$("#change_foto").on("submit", function (e) {
	  e.preventDefault();
	  var dato = $("#ds_foto1").prop("files")[0];
	  
	  var type_img = $("#type_img").val();
	  var formData = new FormData(document.getElementById("change_foto"));
	  formData.append("ds_foto1", dato);
	  formData.append("type_img", type_img);
	  $.ajax({
		  url: "site/change_profile.php",
		  type: "post",
		  dataType: "json",
		  data: formData,
		  cache: false,
		  contentType: false,
		  processData: false,
		  success: function (result) {
			  if (!result.datos.fg_error)
				  location.reload();
		  }
	  })

	  pageSetUp();
	});

</script>

  


<!--======fin mdoal------->