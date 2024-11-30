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

	function urlToLink($string) {
		
		# Commented because does not need it
	    //$url='@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
		//$string=str_uso_normal(preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $string));

	    return str_uso_normal($string);
	}

?>
<!----Modal Follow----->
<div class="modal fade" id="ModalAnswers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					<i class="fa fa-file-o" aria-hidden="true"></i>
					<?php echo ObtenEtiqueta(2408);?>
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -21px;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style=" max-height:450px;overflow-y: auto;">
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-12">
					<?php							
					
						#Recupremaos la preguntsas y sus respuestas.
						$Queryp="SELECT   A.ds_post,A.nb_archivo,A.fe_post,fg_ayuda,video_url,fl_gallery_post_sp FROM v_gallery_feed A WHERE A.fl_usuario=$fl_usuario AND origen='p' ORDER BY A.fe_post DESC ";
						$rs = EjecutaQuery($Queryp);
						for($i=0; $row=RecuperaRegistro($rs); $i++){
							$ds_post=str_texto($row[0]);
						    $nb_archivo=str_texto($row[1]);
						    $fe_post=GeneraFormatoFecha($row['fe_post']);
							$fl_gallery_post_sp=$row['fl_gallery_post_sp'];
							$fg_ayuda=$row['fg_ayuda'];
							$video_url=$row['video_url'];
							
							#Buscamos la respuesta correcta.
							if($fg_ayuda==1){
								
								$Query="SELECT ds_comment FROM v_gallery_feed_comments WHERE fl_gallery_post_sp=$fl_gallery_post_sp AND fg_correcto='1' ";
								$re=RecuperaValor($Query);
								$ds_respuesta=str_texto($re['ds_comment']);						
							}
						?>
						<div class="col-sm-6 col-md-6 col-lg-6 padding-5">
							<div class="well well-lg" style='background:#fff ;height:220px;overflow-y: auto'>
								<div class="content">
									<div class='col-sm-12 col-md-12 col-lg-12'style='padding:3px;'>
										<p  class='media-heading' style='float:left;'>
											<?php 
												if($fg_ayuda==1){
													echo"<i class='fa fa-ambulance' ></i>"; 
												} 
											?>
											<?php echo urlToLink($ds_post);?>
											<br>
											<?php 
												if(($fg_ayuda==1)&&(!empty($ds_respuesta))){
											?>
											<small>
												<i class='fa fa-check-circle' style='color:#226108;'></i>
												<?php echo $ds_respuesta;?>
											</small>
											<br>
											<?php } ?>
										</p>
										<span style="float:right">
											<small class='text-muted'>
												<?php echo $fe_post?>
											</small>
										</span>		
									</div>
									<?php if($video_url){ ?>
									<div class='col-sm-12 col-md-12 col-lg-12 text-center' style='padding:3px;'>	
										<div class="embed-responsive embed-responsive-16by9" >
											<iframe src="<?php echo $video_url;?>" style="margin-right: 27px;width: 350px;" frameborder="0" allowfullscreen></iframe>
											</div>
										</div>	
								    	<?php } ?>
									<div class='col-sm-12 col-md-12 col-lg-12' style='padding:3px;'>
									<?php if($nb_archivo){
										
										echo"<img src='".PATH_SELF_UPLOADS."/posts/feed_posts/$nb_archivo' class='img-responsive' style='float:left;'>";
										
									}?>
									</div>
									<div class='col-sm-12 col-md-12 col-lg-12' style='padding:3px;'>
									<?php 
										$Query=" SELECT a.fl_feed_comment,a.ds_comment, DATE_FORMAT(a.fe_alta , '%Y-%m-%d %H:%i:%s')fe_comment, b.fl_publicacion,a.fl_usuario FROM k_feed_comment a JOIN c_feed_publicaciones b ON a.fl_publicacion=b.fl_publicacion WHERE b.fl_publicacion=$fl_gallery_post_sp	 ";
										$res=EjecutaQuery($Query);
										for($m=0; $rowe=RecuperaRegistro($res); $m++){
											$ds_commententarios=$rowe['ds_comment'];
											$fl_usuario_comento=$rowe['fl_usuario'];
											$ruta_avar=ObtenAvatarUsuario($fl_usuario_comento);
											$name_come=ObtenNombreUsuario($fl_usuario_comento,$fl_usuario_comento);
											$fe_comentario=GeneraFormatoFecha($rowe[2]);
										
									?>
										<div class="col-sm-12 col-md-12 col-lg-12 " >
											<img src="<?php echo $ruta_avar;?>" alt="img" style="height:25px; ">
											<span class="name">
												<?php echo $name_come;?>
											</span>
										</div>
										<div class="col-sm-12 col-md-10 col-lg-10">
											<p class=''>
												<small>
													<?php echo $ds_commententarios;?>
												</small>
											</p>
												<small class='text-muted'>
													<?php echo $fe_comentario;?>
												</small>
												<p>&nbsp;</p>
												<!--
												<ul class=" list-inline"> 
													<span><a href="javascript:void(0);" style="text-decoration:none;"><i style="margin:3px;" class="fa fa-heart-o likes " aria-hidden="true" onclick=""></i>
														<span  style="text-decoration:none;">0</span></a>
													</span>&nbsp;&nbsp;
													<span class="c"><a href="javascript:void(0);" style="text-decoration:none;"><i  style="margin:3px;" class="fa fa-comment-o" aria-hidden="true"></i>
														<span  style="text-decoration:none;">0</span></a>
													</span> 
													
														  
												</ul>--->
										</div>
										<?php 
											}
										?>
									</div>
								</div>
							</div>
						</div>
						<?php	
							}
						?>
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
$('#ModalAnswers').modal('show');
</script>
















