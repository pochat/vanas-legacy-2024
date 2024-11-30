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


?>
<!----Modal Follow----->

<div class="modal fade" id="ModalAnswers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-ambulance" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2514);?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -21px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style=" max-height:450px;overflow-y: auto;">

					<div class="row">
								
								<div class="col-sm-12 col-md-12 col-lg-12">
								
								<?php							
								
									#Recupremaos la preguntsas y sus respuestas.
									$Queryp="SELECT  A.ds_post,A.nb_archivo,B.ds_comment,A.fl_usuario,A.fe_post 
												FROM v_gallery_feed A 
										        JOIN v_gallery_feed_comments B ON B.fl_gallery_post_sp=A.fl_gallery_post_sp	 
												WHERE B.fg_correcto='1' AND B.fl_usuario=$fl_usuario ORDER BY A.fe_post DESC ";
									$rs = EjecutaQuery($Queryp);
									$result = array();
									for($i=0; $row=RecuperaRegistro($rs); $i++){
									    $cont++;								
										$ds_pregunta=str_texto($row[0]);
									    $nb_archivo=str_texto($row[1]);
										$ds_comment=str_texto($row[2]);
										$fl_usu_poste=$row[3];
										$fe_post=GeneraFormatoFecha($row['fe_post']);
										$fl_perfil_poste=ObtenPerfilUsuario($fl_usu_poste);
										

										if($ds_pregunta){

										



											
									?>	<div class="col-sm-6 col-md-6 col-lg-6 padding-5">
											<div class="well well-lg" style='background:#fff ;height:220px;overflow-y: auto'>
												<div class="content">
												<?php echo MuestraPerfilFeed($fl_usu_poste,$fl_perfil_poste,$fl_usuario); ?>
											
													<div class='col-sm-12 col-md-12 col-lg-12' style='padding:3px;'>
														<p class='media-heading' style='float:left;'> <b><?php echo $ds_pregunta;?></b>
													
														<br>
														<small><i class='fa fa-check-circle' style='color:#226108;'></i> <?php echo $ds_comment;?></small>
														<br>
														<span><small class='text-muted'><?php echo $fe_post?></small></span>
														</p>
													    
												
													</div>
													
													<div class='col-sm-12 col-md-12 col-lg-12'>
													
													
														<?php if($nb_archivo){
															
															echo"<img src='".PATH_SELF_UPLOADS."/posts/feed_posts/$nb_archivo' class='img-responsive' style='float:left;'>";
															
														}?>
													
													</div>
													
											
												</div>

											
											</div>
										</div>
										
										<?php } ?>
										
										
								<?php	
										
									}


								?>
								
								
								
								
								
								
								
								
								
								
								
									
										
								</div>
								
					</div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> Close</button>
        <button type="button" class="hidden btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>

<!---Modales Follow------>
<script>

$('#ModalAnswers').modal('show');

</script>
















