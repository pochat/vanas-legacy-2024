<?php

  # Libreria de funciones	
  require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $fg_tipo_busqueda = RecibeParametroNumerico('fg_tipo_busqueda');
  

  #Muestra resueltos.
  if($fg_tipo_busqueda==1){
	  
	  	
	#Recuperamos post con respuesta
	$Query="SELECT  a.fl_publicacion,a.fl_usuario,a.ds_contenido,a.nb_img_video,a.fe_alta,video_url 
	FROM c_feed_publicaciones a
		 JOIN k_feed_comment b ON a.fl_publicacion=b.fl_publicacion
			WHERE a.fg_ayuda='1' AND a.fg_oculto='0' AND b.fg_correcto='1'  ORDER BY a.fl_publicacion DESC LIMIT 15 ";
	$ay=EjecutaQuery($Query);
	$no_regis=CuentaRegistros($mf);

		$contas=0;
		for($a=0; $row=RecuperaRegistro($ay); $a++){
			$fl_usuario_post=$row['fl_usuario'];
			$fl_perfil_post=ObtenPerfilUsuario($fl_usuario_post);
			$fl_publicacion=$row['fl_publicacion'];
			$ds_post=html_entity_decode($row['ds_contenido'],ENT_QUOTES);
			$nb_img_video=$row['nb_img_video'];
			$fe_alta=$row['fe_alta'];
			$video_url=$row['video_url'];
			
            #reemplazamos carateres especiales.
            $ds_post=str_replace("&#039;","",$ds_post);
			$ruta_img_post=PATH_SELF_UPLOADS."/posts/feed_posts/thumbs/".$nb_img_video;
			
			#Recuperamos el no.de like que tiene esa publicacion.
			$Query="SELECT COUNT(*)FROM k_feed_likes WHERE fl_gallery_comment_sp=$fl_publicacion ";
			$rop=RecuperaValor($Query);
			$no_likes=$rop[0];
			
			#Recuperamos el No. de comentarios que tiene esa publicacion.
			$Query="SELECT COUNT(*)FROM k_feed_comment WHERE fl_publicacion=$fl_publicacion ";
			$rop=RecuperaValor($Query);
			$no_coment=$rop[0];
?>		 
	  

			<div class="who clearfix">
				<div class="clearfix margin-bottom-5">
					
					<?php MuestraPerfilFeed($fl_usuario_post,$fl_perfil_post,$fl_usuario,'','',1,1,1); ?>
					
					<div class="col-md-12">
						<p><a href="javascript:void(0);" style="cursor:pointer;text-decoration:none;" Onclick="ViewPost(<?php echo $fl_publicacion;?>,<?php echo$fl_usuario_post;?>,'p');"><small class='text-muted'><?php echo $ds_post;?></small></a>
						
						<?php if($nb_img_video){ ?>
						<img src='<?php echo $ruta_img_post;?>' Onclick="ViewPost(<?php echo $fl_publicacion;?>,<?php echo$fl_usuario_post;?>,'p');" style="height:35px; float:right;cursor:pointer;" class='img-responsive'>
						<?php } ?>
						
						<?php
						if($video_url){
							
							echo $url_video="<iframe src='".$video_url."' id='video_youtub' style='height: 125px' width='100%' frameborder=\"0\" allowfullscreen=\"allowfullscreen\"></iframe>";														
						}									
						?>
						
						
						</p>
						<span class="from" style="margin-right: 38px;">
							<i class="fa fa-heart-o" style="font-size: 17px;margin-right: 7px;"></i><?php echo $no_likes; ?></span>
						<span class="from" ><i class="fa fa-comment-o" style="font-size: 17px;margin-right: 7px;"></i><?php echo $no_coment;?></span>
						&nbsp;&nbsp;<span><i class="fa fa-check-circle" style="color:#236308ad;" aria-hidden="true"></i></span>
					</div>
				</div>
			</div>
									  
	  
	  
	  
	  
<?php	  
		}
	  
  }
  
  #Muestra los que no estan resueltos.
  if($fg_tipo_busqueda==2){
	  
	#Recuperamos los post sin respuesta
      $Query="SELECT DISTINCT a.fl_publicacion,a.fl_usuario,a.ds_contenido,a.nb_img_video,a.fe_alta,a.video_url 
			FROM c_feed_publicaciones a
			LEFT JOIN k_feed_comment b ON a.fl_publicacion=b.fl_publicacion  
			WHERE a.fg_ayuda='1' AND a.fg_oculto='0'  AND ( b.fg_correcto='0' OR b.fg_correcto IS NULL )  ORDER BY a.fl_publicacion DESC ";
	$ay=EjecutaQuery($Query);
	$no_regis=CuentaRegistros($ay);
	
		$contas=0;
		for($a=0; $row=RecuperaRegistro($ay); $a++){
			
			$fl_usuario_post=$row['fl_usuario'];
			$fl_perfil_post=ObtenPerfilUsuario($fl_usuario_post);
			$fl_publicacion=$row['fl_publicacion'];
			$ds_post=html_entity_decode($row['ds_contenido']);
			$nb_img_video=$row['nb_img_video'];
			$fe_alta=$row['fe_alta'];
			$video_url=$row['video_url'];
			
            #reemplazamos carateres especiales.
            $ds_post=str_replace("&#039;","",$ds_post);
			$ruta_img_post=PATH_SELF_UPLOADS."/posts/feed_posts/thumbs/".$nb_img_video;
			
			#Recuperamos el no.de like que tiene esa publicacion.
			$Query="SELECT COUNT(*)FROM k_feed_likes WHERE fl_gallery_comment_sp=$fl_publicacion ";
			$rop=RecuperaValor($Query);
			$no_likes=$rop[0];
			
			#Recuperamos el No. de comentarios que tiene esa publicacion.
			$Query="SELECT COUNT(*)FROM k_feed_comment WHERE fl_publicacion=$fl_publicacion ";
			$rop=RecuperaValor($Query);
			$no_coment=$rop[0];

            #vERIFICAMOS SI YA TIENE RESPUESTA.
            $Query="SELECT fg_correcto FROM k_feed_comment where fl_publicacion=$fl_publicacion AND fg_correcto='1'  ";
            $rop=RecuperaValor($Query);
            $exite_respuesta=!empty($rop['fg_correcto'])?$rop['fg_correcto']:NULL;

            if(!empty($exite_respuesta)){
				
			}else{	
			
			$contas ++;
?>			
			

			<div class="who clearfix">
				<div class="clearfix margin-bottom-5">
					
					<?php MuestraPerfilFeed($fl_usuario_post,$fl_perfil_post,$fl_usuario,'','',1,1,1); ?>
					
					<div class="col-md-12">
						<p><a href="javascript:void(0);" style="cursor:pointer;text-decoration:none;" Onclick="ViewPost(<?php echo $fl_publicacion;?>,<?php echo$fl_usuario_post;?>,'p');"><small class='text-muted'><?php echo $ds_post;?></small></a>
						
						<?php if($nb_img_video){ ?>
						<img src='<?php echo $ruta_img_post;?>' Onclick="ViewPost(<?php echo $fl_publicacion;?>,<?php echo$fl_usuario_post;?>,'p');" style="height:35px; float:right;cursor:pointer;" class='img-responsive'>
						<?php } ?>
						
						<?php
                if($video_url){
                    
                    echo $url_video="<iframe src='".$video_url."' id='video_youtub' style='height: 125px' width='100%' frameborder=\"0\" allowfullscreen=\"allowfullscreen\"></iframe>";														
                }									
                        ?>
						
						
						</p>
						<span class="from" style="margin-right: 38px;">
							<i class="fa fa-heart-o" style="font-size: 17px;margin-right: 7px;"></i><?php echo $no_likes; ?></span>
						<span class="from" ><i class="fa fa-comment-o" style="font-size: 17px;margin-right: 7px;"></i><?php echo $no_coment;?></span>
					</div>
				</div>
			</div>


		
			
<?php	  
                    if($contas==15){
                        break;

                    }

            }



		}  
	  
	  
  }

?>


