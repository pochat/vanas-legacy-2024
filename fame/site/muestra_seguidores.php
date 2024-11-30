<?php

	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario = ValidaSesion(False,0, True);
	$fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);

	# Obtenemos el instituto
	$fl_instituto = ObtenInstituto($fl_usuario);

	# Verifica que el usuario tenga permiso de usar esta funcion
	if(!ValidaPermisoSelf(FUNC_SELF)) {
	    MuestraPaginaError(ERR_SIN_PERMISO);
	    exit;
	}

	$fl_usuario=RecibeParametroNumerico('fl_usuario');
	$fg_accion=RecibeParametroNumerico('fg_accion');

	$Query="SELECT fg_export_follower FROM c_instituto WHERE fl_instituto=$fl_instituto ";
	$row=RecuperaValor($Query);
	$fg_btn_export=$row['fg_export_follower'];

	switch ($fg_accion) {
		case 1:
			# Recuperamos los followers de este men.
			$Query="SELECT b.fl_usuario,  b.ds_nombres,b.ds_apaterno,b.fl_perfil_sp FROM c_followers a JOIN c_usuario b ON b.fl_usuario =a.fl_usuario_origen WHERE fl_usuario_destino = $fl_usuario ";
			$fg_btn_seguir=1;
			break;

		case 2:
			# Recuperamos los followed de este men.
			$Query="SELECT b.fl_usuario,  b.ds_nombres,b.ds_apaterno,b.fl_perfil_sp FROM c_followers a JOIN c_usuario b ON b.fl_usuario =a.fl_usuario_destino WHERE fl_usuario_origen = $fl_usuario ";
			$fg_btn_seguir=1;
			break;
		
		default:
			# code...
			break;
	}

	$rs = EjecutaQuery($Query);

?>
<!----Modal Follow----->
<div class="modal fade" id="ModalFollow" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					<i class="fa fa-user-plus"></i>
					<?php echo ObtenEtiqueta(2403);?> 
	            	<?php 
	            		if( $fg_btn_export=="1"){
	            	?> 
	           		<a href="javascript:export_cvs(<?php echo $fl_usuario;?>);" class="">
	           			<i class="fa  fa-file-excel-o"></i>
	           			<?php echo ObtenEtiqueta(26)?>
	           		</a>
	            	<?php } ?>
        		</h5>
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -21px;">
          			<span aria-hidden="true">&times;</span>
        		</button>
      		</div>
    		<div class="modal-body" style="max-height:450; overflow-y: auto">
				<div class="row">
					<div class="col-sm-12 col-xs-12 col-md-12">
						<?php  

							for($i=0; $row=RecuperaRegistro($rs); $i++){
								$fl_usuario_seguidor = $row[0];
								$ds_nombres = str_texto($row[1]);
								$ds_apaterno = str_texto($row[2]);
								$fl_perfil_seguidor=$row[3];
								
								$ruta_avatar=ObtenAvatarUsuario($fl_usuario_seguidor);
								$nombre= ObtenNombreUsuario($fl_usuario_seguidor);
								$profesion= FAMEObtenProfesionUsuario($fl_usuario_seguidor,15);
								
								$Que="SELECT b.ds_pais FROM k_usu_direccion_sp a 
									  JOIN c_pais b ON a.fl_pais=b.fl_pais
									  WHERE a.fl_usuario_sp=$fl_usuario_seguidor   ";
								$rop=RecuperaValor($Que);	
								$nb_pais=str_texto(!empty($rop[0])?$rop[0]:NULL);							
						?>			
						<div class="col-sm-2 col-xs-12 col-md-4">
							<ul class="media-list">
								<?php 
									MuestraPerfilFeed($fl_usuario_seguidor,$fl_perfil_seguidor,$fl_usuario,'','ModalFollow',$fg_btn_seguir,1);
								?>					
							</ul>	
						</div>
						<?php } ?>
					</div>	
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">
					<i class="fa fa-times-circle-o" aria-hidden="true"></i>
					Close
				</button>
        		<button type="button" class="hidden btn btn-primary">
        			Save
        		</button>
			</div>
	    </div>
	</div>
</div>
<!---Modales Follow------>
<script>
	$('#ModalFollow').modal('show');
	function export_cvs(fl_usuario) {
	    var url = '/fame/site/follow_exp.php';
	    document.export.fl_usuario.value = fl_usuario;
	    document.export.action = url;
	    document.export.submit();
	}
</script>
<form name=export method=post>
    <input type=hidden name=fl_usuario>
</form>















