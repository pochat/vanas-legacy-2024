<?php

  # Libreria de funciones	
  require("../lib/self_general.php");

  # Variable initialization
  $fg_post_tiene_respuesta = null;
  
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
  $fl_gallery_post_sp = RecibeParametroNumerico('fl_gallery_post_sp');
  $fl_usuario_post=RecibeParametroNumerico('fl_usuario_post');
  $fame = RecibeParametroNumerico('fame');
  $fg_origen=RecibeParametroHTML('fg_origen');
  $fl_usuario_logueado=$fl_usuario;

  if($fg_origen=='p'){
	  
	$Query="SELECT fl_publicacion,fl_usuario,ds_contenido,nb_img_video,fe_alta,fg_ayuda,video_url,fg_oculto FROM c_feed_publicaciones WHERE fl_publicacion=$fl_gallery_post_sp ";  
	    
	  
  }
  if($fg_origen=='g'){
	
	$Query="SELECT a.fl_gallery_post_sp,a.fl_usuario,b.ds_comentario,a.nb_archivo,fe_post,fg_ayuda,''video_url,fg_oculto 
	        FROM k_gallery_post_sp a
            LEFT JOIN k_entregable_sp b ON a.fl_entregable_sp=b.fl_entregable_sp
			WHERE fl_gallery_post_sp=$fl_gallery_post_sp ";
	  
  }



  #Recuperamos datos generales del post , se omite la consulta ala vista , tarda mucho., se lo dejo por si sirve a futuro.
  //$Query = "SELECT v.*, u.fl_perfil_sp 
	//	     FROM v_gallery_feed v  JOIN c_usuario u on v.fl_usuario=u.fl_usuario ";
  //$Query .= "WHERE fl_gallery_post_sp= $fl_gallery_post_sp  ";
  $row=RecuperaValor($Query);
  $fl_usuario_post=$row['fl_usuario'];
  $fl_perfil_post=ObtenPerfilUsuario($fl_usuario_post);
  $ds_post=html_entity_decode($row[2],ENT_QUOTES);
  $fe_post=time_elapsed_string($row[4]);
  $nb_archivo=str_texto($row[3]);
  $fg_post_ayuda=$row['fg_ayuda'];
  $video_url=$row['video_url'];
   
  #reemplazamos carateres especiales.
  $ds_post=str_replace('&#039;','',$ds_post);
  #Recupermaos el nombre de quien esta posteando.
  $nombre_user=ObtenNombreUsuario($fl_usuario_post,$fl_usuario);
  
  #Recuperamos el avatar del uduario que hizo el post.
  $ruta_avatar=ObtenAvatarUsuario($fl_usuario_post);
  $fl_instituto=ObtenInstituto($fl_usuario_post);
  $name_instituto=ObtenNameInstituto($fl_instituto);

  
  
  
  #Obtenemos la extesion del archivo para identificar si es de un  video floplayer.
  $ext_archivo=strtolower(ObtenExtensionArchivo($nb_archivo));
 
  
  #Recupermaos la ruta del video floplayer.
  if($ext_archivo=='m3u8'){

	$ruta_video_floplayer=PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_usuario_post."/videos/".$nb_archivo;
	
	#Obtenemos solo el nombre del archivo.
	$nb_archiv_sin_ext=ObtenNombreArchivo($nb_archivo);
	$thumb_video=PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_usuario_post."/videos/".$nb_archiv_sin_ext.".png";
	
	echo"
	<style>
	
	.flowplayer {
		background-image: url($thumb_video) !important;
	}
	
	</style>
	
	";
	
  }
  
  

  //cuenta los likes
  $Query = "SELECT COUNT(1) FROM c_feed_likes WHERE fl_gallery_post_feed=$fl_gallery_post_sp AND fg_origen='$fg_origen' ";
  $row2 = RecuperaValor($Query);
  $no_likes = $row2[0];
	
  # Find number of comments for this post
  $Query = "SELECT COUNT(1) FROM v_gallery_feed_comments WHERE fl_gallery_post_sp=$fl_gallery_post_sp AND origen='$fg_origen'";
  $row2 = RecuperaValor($Query);
  $no_comments = $row2[0];	
	
	
	
  //Veifica si el post ya le dio like o nel.
  $Query="SELECT fl_like FROM c_feed_likes WHERE fl_gallery_post_feed=$fl_gallery_post_sp AND fl_usuario=$fl_usuario ";
  $ro=RecuperaValor($Query);
  if(!empty($ro[0])){
		$icono_like="fa-heart";	
		$class_icono="mikelike";
  }else{
		$icono_like="fa-heart-o";
		$class_icono="";		
  }



	
  #Recuperamos el icono del follow segun estatus actual.
  if($fl_usuario<>$fl_usuario_post){


  $Query="SELECT fl_followers FROM c_followers WHERE fl_usuario_destino=$fl_usuario_post AND fl_usuario_origen=$fl_usuario ";
  $rwo=RecuperaValor($Query);
  $fl_followers=$rwo['fl_followers'];
	
	
   	
  if(!empty($fl_followers)){
		
		$icono_follow="<span class='follow_".$fl_usuario."_".$fl_usuario_post."'><i class='fa fa-check-square-o height_user' style='cursor:pointer;color:rgba(0,0,0,.6);' onclick='Unfollow($fl_usuario,$fl_usuario_post)' aria-hidden='true'></i></span>";
  }else{
		
		
		$icono_follow="<span class='follow_".$fl_usuario."_".$fl_usuario_post."'><i class='fa fa-user-plus height_user' style='cursor:pointer;color:rgba(0,0,0,.6);' onclick='Follow($fl_usuario,$fl_usuario_post);' aria-hidden='true'></i></span>";
  }
  //si es el mimso usuario no tendra icono de follow.
  }else
	$icono_follow="";  
  
  #Para post de ayudas
  if($fg_post_ayuda==1){	  
	 
	 $Query="SELECT fl_gallery_comment_sp FROM v_gallery_feed_comments WHERE fl_gallery_post_sp=$fl_gallery_post_sp AND fg_correcto='1' ";
	 $po=RecuperaValor($Query);
	 if(!empty($po[0]))
		 $fg_post_tiene_respuesta=1;
	 
  }	
 
  if($fg_origen=='p'){
     $ruta_file=ObtenConfiguracion(116).PATH_SELF_UPLOADS."/posts/feed_posts/$nb_archivo";
  }
  if($fg_origen=='g'){
	  
	$ruta_file=ObtenConfiguracion(116).PATH_SELF_UPLOADS. "/".$fl_instituto."/".CARPETA_USER.$fl_usuario_post."/sketches/original/$nb_archivo";  
  }	  
 
  #Determinamos el ancho y alto de la imagen  esto es para determina el alto de la imagen en modal y  no se vea feo.
  $imagen = getimagesize($ruta_file);//Sacamos la información
  $ancho = $imagen[0]??NULL;              //Ancho
  $alto = $imagen[1]??NULL;
    
  $ruta_avatar_usr_logueado=ObtenAvatarUsuario($fl_usuario);  

  //$default_comentaios="250";
  if(($alto > $ancho)){
    $style_width="";
  }else{
    $style_width="width='100%' ";
  }

  #Recuperamos la imagen original.
  #Es un post en feed.
  if($fg_origen=='p'){	
      if($nb_archivo){ 
		  $nb_file = "<img id='photo3' src='" . PATH_SELF_UPLOADS . "/posts/feed_posts/$nb_archivo' class='img-responsive' ".$style_width."   style='max-width:772px;margin:auto;max-height:772px;'  >";
		  $ruta_file=ObtenConfiguracion(116).PATH_SELF_UPLOADS."/posts/feed_posts/$nb_archivo";
	  }
  }
  #Es un post del board
  if($fg_origen=='g'){	
     if($nb_archivo){ 
		 $nb_file = "<img id='photo3' src='" . PATH_SELF_UPLOADS . "/".$fl_instituto."/".CARPETA_USER.$fl_usuario_post."/sketches/original/$nb_archivo' class='img-responsive'  ".$style_width." style='max-width:772px;margin:auto;max-height:772px;'   >"; 
		 $ruta_file=ObtenConfiguracion(116).PATH_SELF_UPLOADS. "/".$fl_instituto."/".CARPETA_USER.$fl_usuario_post."/sketches/original/$nb_archivo";
	 }
  }
 


  #Recuperamos los comentarios del post origial.
  //obtenemos el listado de los comentarios por post
  $QueryCz = " SELECT c.fl_gallery_comment_sp, c.fl_gallery_post_sp, c.fl_usuario, c.ds_comment, c.origen, 
				CONCAT(u.ds_nombres, ' ', u.ds_apaterno) as ds_name, c.fe_comment,c.fg_correcto 
				FROM v_gallery_feed_comments c 
				join c_usuario u on (c.fl_usuario=u.fl_usuario) 
				WHERE c.fl_gallery_post_sp=$fl_gallery_post_sp AND c.origen='$fg_origen' ORDER BY c.fl_gallery_comment_sp ASC  ";
  $rsComentarios = EjecutaQuery($QueryCz);
  $comentariosList="";
  for($x=0; $comenta=RecuperaRegistro($rsComentarios); $x++){

                $fl_gallery_comment_sp=$comenta['fl_gallery_comment_sp'];
                $fl_gallery_post_sp=$comenta['fl_gallery_post_sp'];
                $fl_usuario_comentario=$comenta['fl_usuario'];
                $ds_comment=html_entity_decode($comenta['ds_comment'],ENT_QUOTES);
                $origen=$comenta['origen'];
                $ds_name_usuario_comenta=$comenta['ds_name'];
                $fe_comment=time_elapsed_string($comenta['fe_comment']);
                $avatar_user_comento =ObtenAvatarUsuario($fl_usuario_comentario);
				$fg_post_correcto=$comenta['fg_correcto'];
  
                #reemplazamos carateres especiales.
                $ds_comment=str_replace('&#039;','',$ds_comment);


				#Recuperamos el total de likes que tiene actualmente.
				$Query="SELECT COUNT(1) as total FROM k_feed_likes WHERE fl_gallery_comment_sp=$fl_gallery_comment_sp and fg_origen='$origen' and fg_like='1'  ";
				$ro=RecuperaValor($Query);
				$total_like_comen=$ro[0];
				
				//Veriifica si ese comentario ya le dio like o nel.
				$Query="SELECT fl_like FROM k_feed_likes WHERE fl_gallery_comment_sp=$fl_gallery_comment_sp AND fl_usuario=$fl_usuario ";
				$rom=RecuperaValor($Query);
				if(!empty($rom[0])){
					$ya_dio_like_comen=1;		
				}else{
					$ya_dio_like_comen=0;		
				}
				
				#//solo el usuario que posteo tiene la posibilidad de marcar las respuestas correctas y que en su comentraios aparezca para marcar //los de gaery no tienen ayuda
				if(($fl_usuario_post==$fl_usuario)&&($fg_post_ayuda==1))
					$fg_ambulancia_comment=1;
				else
					$fg_ambulancia_comment=0;
  
				
				if($fg_post_correcto==1){
					
					$class_post_correcto="marcado_correcto";
					$fg_paloma_correcta="";
				}else{
					$class_post_correcto="";
					$fg_paloma_correcta="hidden";
					
				}
				
				//determines if you like the user logged in
					if($ya_dio_like_comen==1){			
						$icono="fa-heart";
                        $color="mikelike";						
					}else{
						$icono="fa-heart-o";
						$color="";						
					}
				
				

                #Recuperamos los comentarios de estos comentarios de tercer nivel.(Pendiente).
                //obtenemos el listado de los comentarios por post
                $QueryCz3 = " SELECT c.fl_gallery_comment_sp_comment, c.fl_gallery_comment, c.fl_usuario, c.ds_comment, c.origen, 
						CONCAT(u.ds_nombres, ' ', u.ds_apaterno) as ds_name, c.fe_comment,''fg_correcto 
						FROM v_gallery_feed_comments_comments c 
						left join c_usuario u on (c.fl_usuario=u.fl_usuario) 
						WHERE c.fl_gallery_comment=$fl_gallery_comment_sp AND c.origen='$origen' ORDER BY c.fl_gallery_comment_sp_comment ASC  ";
                $rsComentario3 = EjecutaQuery($QueryCz3);
                $comentariosList3="";    
                for($x3=0; $comenta3=RecuperaRegistro($rsComentario3); $x3++){
                        
                    $fl_gallery_comment_sp_comment=$comenta3['fl_gallery_comment_sp_comment'];
                    $fl_comentario_pertence=$comenta3['fl_gallery_comment'];
                    $ds_comment3=html_entity_decode($comenta3['ds_comment'],ENT_QUOTES);
                    $origen=$comenta3['origen'];
                    $fl_usuario_hizo_comentario=$comenta3['fl_usuario'];
                    $ds_ruta_avatar_hizo_comentario=ObtenAvatarUsuario($fl_usuario_hizo_comentario);
                    $nb_usuario_hizo_comentario=ObtenNombreUsuario($fl_usuario_hizo_comentario);
                    $fe_comment=time_elapsed_string($comenta3['fe_comment']);

					#reemplazamos carateres especiales.
					$ds_comment3=str_replace('&#039;','',$ds_comment3);
                    #Recuperamos el total de likes que tiene actualmente.
                    $Query="SELECT COUNT(1) as total FROM k_feed_likes WHERE fl_gallery_comment_sp_comment=$fl_gallery_comment_sp_comment and fg_origen='$origen' and fg_like='1'  ";
                    $ro=RecuperaValor($Query);
                    $total_like_comen3n=$ro[0];


                    //Veifica si el post ya le dio like o nel.
					$Query3n="SELECT fl_like FROM k_feed_likes WHERE fl_gallery_comment_sp_comment=$fl_gallery_comment_sp_comment AND fl_usuario=$fl_usuario_origen ";
					$ro=RecuperaValor($Query3n);
					if(!empty($ro[0])){
						$icono_3n="fa-heart";
                        $color_3n="mikelike";
                        
					}else{
						$icono_3n="fa-heart-o";
                        $color_3n="";
					}


                    



                     $comentariosList3 .="<li id=\"comenttario_3n_".$fl_gallery_comment_sp_comment."\" class=\"comentarios_3n_".$fl_gallery_comment_sp_comment."\" style=\"border-bottom: 0px;\">  ";
                     $comentariosList3 .="   <img src=\"".$ds_ruta_avatar_hizo_comentario."\" alt=\"img\" style=\"margin-left:5px;\">";
                     $comentariosList3 .="   <span class=\"name\"><a onclick=\"location.href='#site/myprofile.php?profile_id=".$fl_usuario_hizo_comentario."&c=1&uo=".$fl_usuario."&f=1'\" style=\"cursor:pointer;\">".$nb_usuario_hizo_comentario."</a>";
                     $comentariosList3 .="   <span class=\"name pull-right paloma_3n_".$fl_gallery_comment_sp_comment." hidden\" id=\"fg_correct_3n_".$fl_gallery_comment_sp_comment."\"><i class=\"fa fa-check-circle\" style=\"margin-right:10px;color:#226108;\" aria-hidden=\"true\"></i>  </span></span>";
                     $comentariosList3 .="   <span class=\"from\" style=\"opacity: 0.7;font-size: 12px;\">".$fe_comment."</span>";
                     $comentariosList3 .="   <br/>".$ds_comment3."";
                     $comentariosList3 .="   <br/>";
                     $comentariosList3 .="   <ul class=\"list-inline\">";
                     $comentariosList3 .="       <span><a href=\"javascript:LikePostComent(".$fl_gallery_comment_sp_comment.",'".$origen."',".$fl_usuario_hizo_comentario.",2);\" style=\"text-decoration:none;\"><i id=\"like_3n_".$fl_gallery_comment_sp_comment."".$origen."\" style=\"margin:3px;\" class=\"fa ".$icono_3n." likes ".$color_3n."\" aria-hidden=\"true\" ></i><span id=\"cont_lik3n_".$fl_gallery_comment_sp_comment."".$origen."\" style=\"text-decoration:none;\">".$total_like_comen3n."</span></a></span>&nbsp;&nbsp;";
                     $comentariosList3 .="       <span class=\"hidden\"><a href=\"javascript:void(0);\" style=\"text-decoration:none;\"><i id=\"coment_276p\" style=\"margin:3px;\" class=\"fa fa-comment-o\" aria-hidden=\"true\"></i><span id=\"tot_com_276p\" style=\"text-decoration:none;\">0</span></a></span>       &nbsp;&nbsp;";
                     $comentariosList3 .="   </ul>";
                     $comentariosList3 .="</li>";

                 }
				
				
			    $comentariosList .=
				  "<li id=\"comenttario_".$fl_gallery_comment_sp."_m\"   class=\"comentarios_".$fl_gallery_post_sp." ".$class_post_correcto." \"  >
                                <img src=\" ".$avatar_user_comento."\" alt=\"img\" class=\"onlines\" style=\"margin-left:5px;\">
                    			    <span class=\"name\"><a  onclick=\"location.href='#site/myprofile.php?profile_id=".$fl_usuario_comentario."&c=1&uo=".$fl_usuario."&f=1';CierraModal();\" style=\"cursor:pointer;\">  ".$ds_name_usuario_comenta." </a> ";
				//la paloma que marca como respuesta correcta ala ambulabcia.
				$comentariosList .="   <span class=\"name pull-right paloma_".$fl_gallery_post_sp." ".$fg_paloma_correcta." \" id=\"fg_correct_$fl_gallery_comment_sp\"><i class=\"fa fa-check-circle\" style=\"margin-right:10px;color:#226108;\" aria-hidden=\"true\"></i>  </span>";
				$comentariosList .="</span>\n";
                $comentariosList .=  
                               "    <span class=\"from\" style=\"opacity: 0.7;font-size: 12px;\">".$fe_comment."</span>\n
                                     <br>
				                    </span>".$ds_comment."</span>\n 
									<br><br>
									<ul class=\" list-inline\">
										<span ><a href=\"javascript:LikePostComent(".$fl_gallery_comment_sp.",'".$origen."',".$fl_usuario_comentario.",1);\" style=\"text-decoration:none;\"><i id=\"like_".$fl_gallery_comment_sp."".$origen."m\" style=\"margin:3px;\" class=\"fa ".$icono." likes ".$color."\" aria-hidden=\"true\"  onclick=\"\" ></i><span id=\"cont_lik".$fl_gallery_comment_sp."".$origen."m\" style=\"text-decoration:none;\"  >".$total_like_comen."</span></a></span> 
										&nbsp;&nbsp;
										<span><a href=\"javascript:void(0);\" style=\"text-decoration:none;\"><i id=\"coment_".$fl_gallery_comment_sp."".$origen."m\" style=\"margin:3px;\" class=\"fa fa-comment-o\" aria-hidden=\"true\"></i><span id=\"tot_com_".$fl_gallery_comment_sp."".$origen."m\" style=\"text-decoration:none;\">0</span></a></span>  
										<span ><a class=\"replyfeed\" onclick=\"Reply(".$fl_gallery_comment_sp.",".$fl_usuario_comentario.",'".$origen."');\" > Reply</a></span>				  
										&nbsp;&nbsp; ";
  
                //Solo muestra la ambulabcia si el post necesita ayuda o fue marcado para pedir ayuda y que solo el usuario que hizo el post original tiene esa posibilidad de marcar y ver.
				 if(($fl_usuario_post==$fl_usuario)&&($fg_post_ayuda==1)){
			    $comentariosList .=  
							   "       <span><a href=\"javascript:MarcarCorrecto(".$fl_gallery_post_sp.",".$fl_gallery_comment_sp.");\" style=\"text-decoration:none;\"><i id=\"ambulan_".$fl_gallery_comment_sp."".$origen."\" style=\"margin:3px;\" class=\"fa fa-ambulance disable_".$fl_gallery_post_sp." \" aria-hidden=\"true\"></i></a></span>";
				  }
				  
				$comentariosList .=  
				            "     	   <li id=\"newCommentComment".$fl_gallery_comment_sp."".$origen."\" class=\"hidden\" >
											<img src=\"".$ruta_avatar_usr_logueado."\" alt=\"img\" class=\"onlines\">
											<input type=\"text\" id=\"comentario_comen_".$fl_gallery_comment_sp."_".$fl_usuario_comentario."\" value=\"\" name=\"comentario\" class=\"form-control comentario mikeinput\" style=\"width:100%;\" onkeypress=\"javascript:comment_resp(event,".$fl_gallery_comment_sp.",'".$origen."',this,".$fl_usuario_comentario.");\"  placeholder=\"".ObtenEtiqueta(2509)."\">\n
									    </li>
										<div id=\"all_coment_coment_".$fl_gallery_comment_sp."_".$fl_usuario_comentario."_m\">
                                                 ".$comentariosList3."
                                        
                                        </div>
										
									</ul>
								</li>";
  
  
  
  
  }
  
  
 
  
/*
  $result = array(
  	"nb_imagen_post" => $nb_file,
    "icono_follow" => $icono_follow,
    "fg_post_ayuda" => $fg_post_ayuda,
	"fl_gallery_post_sp" => $fl_gallery_post_sp,
	"fl_usuario"=>$fl_usuario,
	"fg_origen"=>$fg_origen,
	"fl_usuario_post"=>$fl_usuario_post,
	"fl_perfil_post"=>$fl_perfil_post,
	"ds_post"=>$ds_post,
	"icono_like"=>$icono_like,
	"class_icono"=>$class_icono,
	"no_likes"=>$no_likes,
	"no_comments"=>$no_comments,
	"fg_post_tiene_respuesta"=>$fg_post_tiene_respuesta,
	"ruta_avatar_usr_logueado"=>$ruta_avatar_usr_logueado,
	
	
	"no_semana" => $no_semana,
    // "no_grado" => $no_grado,
    "ds_pais" => $ds_pais,
  	
  	 "nb_instituto" => $nb_instituto,
     "nb_programa"=> $nb_programa,
  	"fl_post_usuario" => $fl_post_usuario,
  	"nb_usuario" => $nb_usuario,
  	"ds_title" => $ds_title,
  	"ds_post" => $ds_post,
  	"fe_post" => $fe_post,
  	"nb_archivo" => $nb_file,
    "extension" => $ext
  );
   
*/

//si no trae imagen es un post de solo texto.
if(empty($nb_archivo)){
	
	//se determina el col-md  
    $col_md="col-sm-12 col-xs-12 col-md-12";
    $clase_sin_imagen="hidden";	
	$fg_sin_imagen=1;
}else{
	$col_md="col-sm-4 col-xs-12 col-md-4";	
	$clase_sin_imagen="";
	$fg_sin_imagen=0;
}

  
?>



	<div class="container-content" style="height: auto; min-height: 100%;max-height:772px;">
    
		<div class="row" style="height:auto; min-height: 100%;max-height:772px; margin-left: 0px;margin-right: 0px;">
			<div class="col-sm-8 col-xs-12 col-md-8 text-center <?php echo $clase_sin_imagen;?>" id="row_imganen" style="background:#000;height: auto;min-height: 584px;max-height:772px;display:flex;  align-items: center"  >

							<?php
							//es un video floplayer.
							if($ext_archivo=="m3u8"){
								echo"<div id='div_flowplayer' class='flowplayer fp-edgy' style=\"height:100%;\"></div>";	
							}else{

                                if(!empty($video_url)){
                                      echo"<iframe src='".$video_url."' id='video_youtub' style='height: 402px' width='100%' frameborder=\"0\" allowfullscreen=\"allowfullscreen\"></iframe>";
                                }else{                                  
                                      echo $nb_file;
                                }


								
								
							}
							?>

			</div>
			
					    
			
			<div class="<?php echo $col_md;?>" style="height: auto; min-height: 100%;max-height:772px;" >
			
								<div class="panel panel-default" style="auto; min-height: 100%;max-height:772px; border: 0px solid transparent;">
							
									<div class="panel-body status" id="content_info" style="height: auto; min-height: 100%;max-height:772px;overflow-y:auto"   >
	
				
	
										<div class="who clearfix borde_inferior">
							
							
											<?php MuestraPerfilFeed($fl_usuario_post,$fl_perfil_post,$fl_usuario_logueado,$fe_post,'','','','',$fg_sin_imagen,1); ?>
							
											<span class="name pull-right">
												<?php echo $icono_follow;?>
												<?php if($fg_post_ayuda==1){ 	  
													  echo "<i class=\"fa fa-ambulance height_user\"></i>";  
												  }	
												?>  
												<i class="fa fa-ellipsis-h height_user dropdown-toggle " data-toggle="dropdown"></i>
												<div class="popover bottom dropdown-menu " style="top: 34px;right:22px;"><div class="arrow"></div>
													<div class="popover-content">
														<ul class=" text-left" style="list-style: none;padding: 0;">
															
															
															
															<!--ocultar post-->
															<li class="mike_opt">
															<a class="mike_opt"style="text-decoration: none" href="javascript:hide_post(<?php echo $fl_gallery_post_sp;?>,<?php echo $fl_usuario;?>,'<?php echo $fg_origen;?>');"><i class="fa fa-eye-slash"style="margin: 4px;"></i> Hide this post</a>
															</li>
															
															<!--eleimnar post--->
															<?php if(($fg_origen=='p')&&($fl_usuario_post==$fl_usuario)){ ?>
															
															<li class="mike_opt">
																<a class="mike_opt" style="text-decoration: none" href="javascript:remove_post(<?php echo $fl_gallery_post_sp;?>,<?php echo $fl_usuario;?>,'<?php echo $fg_origen;?>');"><i class="fa fa-trash-o" style="margin: 4px;"></i> Remove this post</a>
															</li>
															<?php } ?>
															
															
															
														</ul>
													</div>	
												</div>										
											</span>
											
										</div>	
											
											
											
										<div class="text padi_texto_comentario"><?php echo $ds_post;?> </div>
										 <br />
                                        <?php 
                                        if(!empty($video_url)){
                                            echo"<iframe src='".$video_url."' id='video_youtub' style='height: 402px' width='100%' frameborder=\"0\" allowfullscreen=\"allowfullscreen\"></iframe>";

                                        }
                                        
                                        ?>


										 <ul class="links">
											<li >											
												  <a href="javascript:like_post(<?php echo $fl_gallery_post_sp;?>,'<?php echo $fg_origen;?>',1,1);" style="text-decoration:none;" ><i id="like_act<?php echo $fl_gallery_post_sp.$fg_origen?>_m" class="fa <?php echo $icono_like;?> <?php echo $class_icono;?>"></i> <span style="text-decoration:none" id="link_cont<?php echo $fl_gallery_post_sp.$fg_origen?>_m"><?php echo $no_likes;?></span></a>
											</li>
											<li
											
												  <a href="javascript:void(0);" style="text-decoration:none;color:#3276b1;"><i class="fa fa-comment-o" style="text-decoration:none;" ></i> <span id="comment_plus<?php echo $fl_gallery_post_sp.$fg_origen;?>_m" style="text-decoration:none;" ><?php echo $no_comments;?></span></a>
											</li>
											<!--aparecera cuando se marca como correcto y en primera instacncia nos indica que ya tiene una respuesta.--->
											<?php if($fg_post_tiene_respuesta==1){?>
											<li id="marcado_correcto_fl_gallery_post" class="class_paloma_marcado_respuesta">
												<a href="javascript:void(0);"  onclick="ViewPostCorrect(<?php echo $fl_gallery_post_sp;?>,<?php echo $fl_usuario_post;?>,1)" ><i class="fa fa-check-circle" style="color:#236308ad;"></i> </a>
											</li>
											<?php } ?>
			
										 </ul>
										 
										 <div id="all_coment_<?php echo $fl_gallery_post_sp;?>_<?php echo $fl_usuario_post;?>_m" style="max-height: 330px; overflow-y: auto !important;">
										 <!--inician los comentariossss-->
										 <ul class="comments style-10" >
											
											
											<!----aqui todos los coemntariosde ese post--->
										    <div id="all_coment_<?php echo $fl_gallery_post_sp;?>_<?php echo $fl_usuario_post;?>_m2">											
													<?php echo $comentariosList;?>
											</div>
											<!---finaliza comentarios de ese post--->
										 </ul>
										<!--------finaliza comentarios----->
										 <ul class="comments style-10 sborde" id="_writecoment" >
										
										    <li style="padding-bottom: 40px;" id="newComment<?php echo $fl_gallery_post_sp;?><?php echo $fg_origen;?>_m">
												<img src="<?php echo $ruta_avatar_usr_logueado;?>" alt="img" class="onlines">
												<input type="text" id="comentario_<?php echo $fl_gallery_post_sp;?>_<?php echo $fl_usuario_post;?>" value="" name="comentario" class="form-control comentario mikeinput" onkeypress="javascript:comment_post(event,<?php echo $fl_gallery_post_sp; ?>,'<?php echo $fg_origen;?>',this,1,1,<?php echo $fl_usuario_post;?>);"  placeholder="<?php echo ObtenEtiqueta(2509);?>">
											</li>
										 </ul>
										
										</div>
										
										 
										 
										 
									</div> 
							
								</div>
			
			
			
			</div>
			
			
			
			
		</div>
	
	
		
	</div>


<?php 
//es un video floplayer.
if($ext_archivo=="m3u8"){

echo"
	
<script>




// Neccesary to watermarker
    flowplayer.conf = {
      splash: true
    };
	flowplayer(function (api) {
      $('#cerrar_modal_video').on('click', function () {
          api.stop();
      });
	  $('#cerrar_modal_video2').on('click', function () {
          api.stop();
      });
  
    });
	
	
	
// select the above element as player container
var container = document.getElementById('div_flowplayer'), watermarkTimer, timer;    
var sources_m3u8 = '".$ruta_video_floplayer."';
var key_flowplayer = '".ObtenConfiguracion(110)."';

// opciones
      var optionss = {
        key: key_flowplayer,      
        ratio: 9/16,
		
        clip: {
          sources: [
            // { type: 'video/mp4',
              // src:  sources_mp4 },
            { type: 'application/x-mpegURL',		  
              src:  sources_m3u8
			}
          ], 
";
	/*	subtitles: [
            { 'default': true,       // note the quotes around 'default'!
              kind: 'subtitles', srclang: 'en', label: 'English',
              src:  '//edge.flowplayer.org/subtitles/subtitles-en.vtt' },
            { kind: 'subtitles', srclang: 'de', label: 'Deutsch',
              src:  '//edge.flowplayer.org/subtitles/subtitles-de.vtt' }
        ],*/
echo"
		  
          scaling: 'fit',
          // configure clip to use hddn as our provider, referring to the rtmp plugin
          provider: 'hddn'          
        },
		 //esto es para generar la vista previa en la linea de tiempo de los videos. 
        thumbnails: {
          width: 120,
          height: 100,
          columns: 5,
          rows: 8,
          template: '$thumb_video/img{time}.jpg'
		  //template: 'img1.jpg'
        },

        rtmp: 'rtmp://s3b78u0kbtx79q.cloudfront.net/cfx/st',
        // loop playlist
        loop: false,
		autoplay:true,
        keyboard:true,
        embed:false,
        share:false,
		
        volume: 1.0
      };

      // install flowplayer into selected container
      flowplayer(container, optionss)
       // WaterMarke fullscreen and fullscreen-exit
       .on('fullscreen fullscreen-exit', function (e, api) {
          if (/exit/.test(e.type)) { // sale
             // do something after leaving fullscreen 
             // no working
          } else { // entra
            // do something after going fullscreen
            // Start the watermark interval
            watermarkTimer = setInterval(function() {
              var width, height, min, x, y, css;
            
              // Show or hide watermark
              // $('#div_watermark').toggle();

              // Screen size
              width = window.innerWidth;
              height = window.innerHeight;
              min = 20; // 20 padding

              // Generate random width and height
              x = Math.floor(Math.random() * (width - min)) + min;
              y = Math.floor(Math.random() * (height - min)) + min;
              // Move watermark to new positions
              css = {left: x, top: y};
              $('#div_watermark').animate(css, 0);              
            }, 10000);
          }
      });
	  
	  
	  
	
	  
	  
</script>
";
	
	
}

?>


