<?php
 
	# community.php shows the list of users (teachers / students)
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario = ValidaSesion(False);

	# Verifica que el usuario tenga permiso de usar esta funcion
    if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
       MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
    }
 
	

	$fl_usuario_origen=RecibeParametroNumerico('fl_usuario_origen');
	$fl_usuario_destino=RecibeParametroNumerico('fl_usuario_destino');

	
	
	#Recuperamos el nombre del usuario de origen
	$Query  = "SELECT ds_nombres, ds_apaterno, ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_origen ";
	$row = RecuperaValor($Query);
	$ds_fname_origen = str_texto($row[0]);
	$ds_lname_origen = str_texto($row[1]);
	$ds_email_origen=str_texto($row[2]);
	$nb_usuario_origen=$ds_fname_origen." ".$ds_lname_origen;
	
	
  ?>

<div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo ObtenEtiqueta(2184);?> </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -18px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<div class="row"  id="pant1">
				<div class="col-md-12 text-center">
						<p><?php echo ObtenEtiqueta(2185);?></p>
				</div>
			
			</div>	
			
			<div class="row hidden"  id="pant2">
				<div class="col-md-1"></div>
					<div class="col-md-10">
							<div class="smart-form">
					
							
					
					
								<div class="form-group has-feedback" id="text_coment">
									    <label class="control-label"><?php echo ObtenEtiqueta(2186);?></label>
										<textarea rows="3" class="form-control" onkeyup="Pinta();" id="comentario_friends" style="width: 90%;"></textarea> 

								</div>
							
							</div>
					</div>
				<div class="col-md-1"></div>
			</div>
			
			
		
		
		
		
      </div>
      <div class="modal-footer text-center">
			<a href="javascript:void(0);" class="btn btn-default" id="add_note"  Onclick="MostrarRedaccion();"><?php echo ObtenEtiqueta(2187);?></a>
			<a href="javascript:void(0);" class="btn btn-default hidden" id="close_modal" data-dismiss="modal" >Cancel</a>
			<a href="javascript:void(0);" class="btn btn-primary"  Onclick="SendConect(<?php echo $fl_usuario; ?>,<?php echo $fl_usuario_destino;?>,'<?php echo $nb_usuario_origen;?>' )"><?php echo ObtenEtiqueta(2188);?></a>
      </div>
