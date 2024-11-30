<?php
	# Libreria de funciones
	require("../lib/self_general.php");


function ObtenSoloNombreArchivo($p_archivo) {

  $archivo = $p_archivo;
  if (substr_count($archivo, '/') > 0)
    $archivo = substr($archivo, strrpos($archivo, '/') + 1);
  if (substr_count($archivo, '.') > 0)
    $archivo = substr($archivo, 0, strpos($archivo, '.'));
  return $archivo;
}


  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True); 
  # Obtenemos el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  # Obtenemos el perfil del usuario
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
  $avatarGeneral =ObtenAvatarUsuario($fl_usuario);


  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

   #Es un usuario logueado.
   $fl_usuario_origen=$fl_usuario;
  
	# Receive Parameters
  //$fl_programa_sp = RecibeParametroNumerico('tema', True);
  //$my_posts = RecibeParametroNumerico('my_posts', True);
  $index = RecibeParametroNumerico('index', True);
	
  $index = intval($index);
  $index_end = 3;
  /*$Queryp="SELECT v.*, u.fl_perfil_sp from v_gallery_feed v
		   join c_usuario u on v.fl_usuario=u.fl_usuario 
		   WHERE fg_oculto='0' 
		   
		  ";
  $Queryp .= " ORDER BY fe_post DESC LIMIT $index_end OFFSET $index "; //fe_post
  */
  $Queryp="(		   
	SELECT 		   
	a.fl_gallery_post_sp AS fl_gallery_post_sp,
	a.fl_usuario AS fl_usuario,
	b.ds_comentario AS ds_post,
	a.nb_archivo AS nb_archivo,
	a.fe_post AS fe_post,
	'g' AS origen,
	'1' AS fame,
	a.fg_ayuda AS fg_ayuda,
	'' AS video_url,
	a.fg_oculto AS fg_oculto FROM 
	k_gallery_post_sp a JOIN 
	k_entregable_sp b ON a.fl_entregable_sp = b.fl_entregable_sp
    JOIN c_usuario c ON c.fl_usuario=a.fl_usuario	
	 ORDER BY fe_post DESC ";
    $Queryp.=" LIMIT $index_end OFFSET $index ";
   /* if($index==0){
	$Queryp.="
	 LIMIT $index_end 
	";
	 }else{
		 
		$Queryp.="
	 LIMIT $index 
	";
	 }
	 */
    $Queryp.="	
	
	 
	)UNION(	   
			   
	SELECT fl_publicacion AS fl_publicacion,
	a.fl_usuario AS fl_usuario,
	a.ds_contenido AS ds_contenido,
	a.nb_img_video AS nb_img_video,
	a.fe_alta AS fe_post,
	'p' AS origen,
	'1' AS fame,
	a.fg_ayuda AS fg_ayuda,
	a.video_url AS video_url,
	a.fg_oculto AS fg_oculto 
	from c_feed_publicaciones a
	JOIN c_usuario b ON a.fl_usuario=b.fl_usuario 
	 ORDER BY fe_post DESC ";
	/* if($index==0){
	
	$Queryp.="
	 LIMIT $index_end  ";
	 }else{
	$Queryp.="
	 LIMIT $index  ";	 
		 
	 }
	 */
	$Queryp.="  
	) ORDER BY fe_post DESC LIMIT $index_end OFFSET $index ";
	$rs = EjecutaQuery($Queryp);
	$result = array();
	for($i=0; $row=RecuperaRegistro($rs); $i++){


 

        $fl_gallery_post_sp = $row[0];
        $fl_post_usuario = $row['fl_usuario'];
        $ds_post = html_entity_decode($row['ds_post']);
        $nb_archivo = $row['nb_archivo'];
        $fe_post = $row['fe_post'];
        $origen = $row['origen'];
        $fechaFormato=time_elapsed_string($fe_post);
        $fg_ayuda = $row['fg_ayuda'];
        $video_url = $row['video_url'];
        $fl_perfil = !empty($row['fl_perfil_sp'])?$row['fl_perfil_sp']:NULL;
		$fg_ocultar_post=!empty($row['fg_ocultar'])?$row['fg_ocultar']:NULL;
		$fg_post_oculto_usuario=!empty($row['fl_usuario_origen'])?$row['fl_usuario_origen']:NULL;
		
		#reemplazamos carateres especiales.
		$ds_post=str_replace("&#039;","",$ds_post);
		
		#Verifica si ya tiene respuesta correcta.
		$Query=" SELECT COUNT(*) FROM v_gallery_feed_comments WHERE fl_gallery_post_sp =$fl_gallery_post_sp AND fg_correcto='1' ";
		$rom=RecuperaValor($Query);
		if($rom[0]){
			
			$fg_tiene_respuesta=1;
			
		}else
			$fg_tiene_respuesta=0;
		
		
	
		
		#Verifica si noesta oculto para este usuario.
		$Query="SELECT  fg_ocultar FROM c_feed_hidden_post_usuario 
			     WHERE fl_gallery_post_sp=$fl_gallery_post_sp  
				   AND fl_usuario_origen=$fl_usuario_origen ";
		$roer=RecuperaValor($Query);
		$fg_ocultar_post=!empty($roer['fg_ocultar'])?$roer['fg_ocultar']:NULL;
		
		
		if($fg_ocultar_post==1)
			$fg_post_oculto=1;
		else
			$fg_post_oculto=0;
		
	 
        if($origen=='g'){ //pertenecen los post a galeria
            $uploads_origen="gallery";

            #Recuperamos informacion adicional 
            $Querypro="SELECT nb_programa,fl_entregable_sp FROM k_gallery_post_sp a JOIN c_programa_sp b ON b.fl_programa_sp=a.fl_programa_sp WHERE a.fl_gallery_post_sp=$fl_gallery_post_sp   ";
            $rowpro=RecuperaValor($Querypro);
            $nb_programa_sp=$rowpro['nb_programa'];$fl_entregabl=$rowpro['fl_entregable_sp'];

        }
        if($origen=='p') { //pertenecen a publicaciones
            $uploads_origen="posts";
            $nb_programa_sp="";
        }

        if($video_url!='') {
            $urlVideo="<iframe src='".$video_url."' style='height: 402px' width='100%' frameborder=\"0\" allowfullscreen=\"allowfullscreen\"></iframe>";
        }else{
            $urlVideo ="";
        }

		#Solo se puede elimnar el post si pertnece al usuario que lo publico y si pertence al feed//no se puede elimnar un post que venga del fame board.
        if(($fl_post_usuario==$fl_usuario_origen)&&($origen=="p")){
            $delete_post=1;
			
        }else{
            $delete_post=0;		
			
        }
		
		#Para saber si aparece el follow o no. y el hidde post //El mismo usuario no tendra la opcion de  ocultar su misma publicacion solo elimnar.
		if($fl_post_usuario==$fl_usuario_origen){
			$fg_follow=0;$hide_post=0;
			$tipo_icono_follower="";
			
		}else{
			
			$fg_follow=1;
		    $hide_post=1;
			
			
			#Verificamos el estatus del follow.
			$Query="SELECT fl_followers FROM c_followers WHERE fl_usuario_origen=$fl_usuario_origen AND fl_usuario_destino=$fl_post_usuario ";
			$rof=RecuperaValor($Query);
			$fl_followers=!empty($rof[0])?$rof[0]:NULL;
			
			//$fl_followers=1;
			if(!empty($fl_followers)){
				
				$tipo_icono_follower=" <i class=\"fa fa-check-square-o height_user\" Onclick=\"Unfollow($fl_usuario_origen,$fl_post_usuario)\" ></i>       ";
				
			}else{
				$tipo_icono_follower="<i class=\"fa fa-user-plus height_user\" Onclick=\"Follow($fl_usuario_origen,$fl_post_usuario)\" ></i>     ";
				
			}
			
			
			
			
			
		}
		
		
				
		
        //cuenta los likes
        $Query = "SELECT COUNT(1) FROM c_feed_likes WHERE fl_gallery_post_feed=$fl_gallery_post_sp AND fg_origen='$origen' ";
        $row2 = RecuperaValor($Query);
        $no_likes = $row2[0];
        
		//Veifica si el post ya le dio like o nel.
		$Query="SELECT fl_like FROM c_feed_likes WHERE fl_gallery_post_feed=$fl_gallery_post_sp AND fl_usuario=$fl_usuario ";
		$ro=RecuperaValor($Query);
		if(!empty($ro[0])){
			$ya_dio_like=1;		
		}else{
			$ya_dio_like=0;		
		}
		
		
		//Avatar del usuario logueao.
		$AvatarUsLogueado=ObtenAvatarUsuario($fl_usuario_origen);

        $avatarUs =ObtenAvatarUsuario($fl_post_usuario);
        $fl_instituto = ObtenInstituto($fl_post_usuario);
        $nb_instituto = ObtenNameinstituto($fl_instituto);
        $ds_profesion=FAMEObtenProfesionUsuario($fl_post_usuario,$fl_perfil);
        $compania=FAMEObtenCompaniaUsuario($fl_post_usuario,$fl_perfil);

        // $ds_nombres = $row[4];
        $ds_nombres = ObtenNombreUsuario($fl_post_usuario, $fl_usuario);

        //$nb_programa = str_uso_normal($row[13]);
        //$fl_grade_user = $row[15];
        //$edad = $row[16];
        $fame = $row['fame'];

        # Initialize default post settings
        $type = "";
        $fg_my_post = false;
        $fg_tipo = "";
        $no_semana = "";
        $no_grado = "";
        $ds_pais = "";
        $fg_video_floplayer=0; 

        if($fame==1) {

            # Find country of the author
            $Query = "SELECT  b.nb_pais FROM k_usu_direccion_sp a ";
            $Query .= "LEFT JOIN c_pais b ON(a.fl_pais=b.fl_pais) ";
            $Query .= "WHERE fl_usuario_sp=$fl_post_usuario ";
            $row2 = RecuperaValor($Query);
            $ds_pais = !empty($row2[0])?$row2[0]:NULL;
            #En caso de que no tenga pais el defaul es el del instituto
            if (empty($ds_pais)) {
                $rowe = RecuperaValor("SELECT b.nb_pais FROM c_instituto a, c_pais b WHERE a.fl_pais = b.fl_pais AND a.fl_instituto=" . $fl_instituto);
                $ds_pais = $rowe[0];
            }


            //cuenta los comentarios
            # Find number of comments for this post
            $Query = "SELECT COUNT(1) FROM v_gallery_feed_comments WHERE fl_gallery_post_sp=$fl_gallery_post_sp AND origen='$origen'";
            $row2 = RecuperaValor($Query);
            $no_comments = $row2[0];

            # Get last comment
            $Query3 = "SELECT MAX(fl_gallery_comment_sp) fl_gallery_comment_sp_ultimo FROM k_gallery_comment_sp WHERE fl_gallery_post_sp=$fl_gallery_post_sp ORDER BY fe_comment DESC";
            $row3 = RecuperaValor($Query3);
            $fl_gallery_comment_sp_ultimo = $row3[0];




            if(empty($fl_gallery_comment_sp_ultimo))
                $fl_gallery_comment_sp_ultimo = 0;

			# Check if this is an upload from desktop or straight from the board
            if(!empty($fl_entregable_sp)){
                $type = "Desktop";

                # Retrieve desktop post info
                $Queryy  = "SELECT a.fg_tipo, d.no_semana  ";
                $Queryy .= "FROM k_entregable_sp a ";
                $Queryy .= "LEFT JOIN k_entrega_semanal_sp b ON b.fl_entrega_semanal_sp=a.fl_entrega_semanal_sp ";
                $Queryy .= "LEFT JOIN c_leccion_sp d ON d.fl_leccion_sp=b.fl_leccion_sp ";
                $Queryy .= "WHERE a.fl_entregable_sp=$fl_entregable_sp  ";
                $row2 = RecuperaValor($Queryy);
                $fg_tipo = $row2[0];
                $no_semana = $row2[1];

                switch($fg_tipo) {
                    case "A":		$fg_tipo = "Assignment";  break;
                    case "AR":	$fg_tipo = "Assignment Reference"; break;
                    case "S":   $fg_tipo = "Sketch";  break;
                    case "SR":	$fg_tipo = "Sketch Reference"; break;
                }
                if($nb_archivo !='') {
                    $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
                    if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'PNG') {
                        # A student uploaded image
                        $nb_file = "<img src='" . PATH_SELF_UPLOADS . "/" . $fl_instituto . "/USER_" . $fl_post_usuario . "/sketches/board_thumbs/$nb_archivo' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\" width='100%'>";
                    } else {
                        # A student uploaded video
                        $nb_file = "<img src='" . PATH_SELF_UPLOADS . "/" . $uploads_origen . "/thumbs/vanas-board-video-default.jpg' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\" width='100%'>";
                    }
                }else{
                    $nb_file ="";
                }

            }else{


                $type = "Board";
                if($nb_archivo !='') {


                    # Retrieve desktop post info
                    $Queryy  = "SELECT a.fg_tipo, d.no_semana  ";
                    $Queryy .= "FROM k_entregable_sp a ";
                    $Queryy .= "LEFT JOIN k_entrega_semanal_sp b ON b.fl_entrega_semanal_sp=a.fl_entrega_semanal_sp ";
                    $Queryy .= "LEFT JOIN c_leccion_sp d ON d.fl_leccion_sp=b.fl_leccion_sp ";
                    $Queryy .= "WHERE a.fl_entregable_sp=$fl_entregabl  ";
                    $row2 = RecuperaValor($Queryy);
                    $no_semana = $row2['no_semana']??NULL;
                    if($no_semana)
                        $no_semana=ObtenEtiqueta(1230)." ".$no_semana;

                    #Recuperamos la extnsion del archivo. esto para el floypalyer de un video subido del estudiante.
                    $ext_archivo=ObtenExtensionArchivo($nb_archivo);
                    if($ext_archivo == "jpg" || $ext_archivo == "jpeg" || $ext_archivo=="png") {
                        
                        
                        $nb_file = "<img src='" . PATH_SELF_UPLOADS . "/" . $uploads_origen . "/feed_posts/$nb_archivo'  width='100%' data-id='".$fl_gallery_post_sp."' id='".$origen."' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\"   class='muestra'>";
                        

                    }else{
						
						$nb_imh_thumb=ObtenSoloNombreArchivo($nb_archivo);
						
						
                        $fg_video_floplayer=1;
						 $nb_file = "
						  <div id='myCarousel-2' class='carousel slide'  onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\" style='cursor:pointer;' >
							<div class='carousel-inner'>
								<div class='item active' style='position:relative;'> <img src='" . PATH_SELF_UPLOADS . "/" . $fl_instituto . "/USER_".$fl_post_usuario."/videos/".$nb_imh_thumb.".png'  width='100%' data-id='".$fl_gallery_post_sp."' id='".$origen."' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\"   class='muestra'>
						 
									<div class='carousel-caption caption-right no-padding'>
										
										<div class='padding-10'>
											<a style='color:#fff;font-size:65px;' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\" ><i class='fa fa-play'></i></a>
											
										</div>                    
									</div>
						 
								</div>
							</div>
						</div>";
                        

                    }


                
                }else{
                    $nb_file ="";
                }
                # If this post belongs to the board and is posted by this user, allow delete
                if($fl_post_usuario == $fl_usuario){
                    $fg_my_post = true;
                }


                if(empty($ds_title)){
                    $ds_title = "";
                }
                $aviso = "";
				
				
				

				#Los archivos que vienen del Boardy de Fame /student.
				if($origen=='g'){
					#Recuperamos la extnsion del archivo. esto para el floypalyer de un video subido del estudiante.
                    $ext_archivo=ObtenExtensionArchivo($nb_archivo);
					if($ext_archivo == "jpg" || $ext_archivo == "jpeg" || $ext_archivo=="png") {


					# A student uploaded image
					$nb_file = "<img src='" . PATH_SELF_UPLOADS . "/" . $fl_instituto . "/USER_" . $fl_post_usuario . "/sketches/original/$nb_archivo' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\"  width='100%'>";
					}else{
						
						$nb_imh_thumb=ObtenSoloNombreArchivo($nb_archivo);
						
						
                        $fg_video_floplayer=1;
						 $nb_file = "
						  <div id='myCarousel-2' class='carousel slide' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\" style='cursor:pointer;' >
							<div class='carousel-inner'>
								<div class='item active' style='position:relative;'> <img src='" . PATH_SELF_UPLOADS . "/" . $fl_instituto . "/USER_".$fl_post_usuario."/videos/".$nb_imh_thumb.".png'  width='100%' data-id='".$fl_gallery_post_sp."' id='".$origen."' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\"   class='muestra'>
						 
									<div class='carousel-caption caption-right no-padding'>
										
										<div class='padding-10'>
											<a style='color:#fff;font-size:65px;' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\" ><i class='fa fa-play'></i></a>
											
										</div>                    
									</div>
						 
								</div>
							</div>
						</div>";
                        
						
						
					}

					
				}
				
				
				
            }



        }else{




            # Find country of the author
            $Query0  = "SELECT c.ds_pais FROM c_usuario a ";
            $Query0 .= "LEFT JOIN k_ses_app_frm_1 b ON(a.cl_sesion = b.cl_sesion) ";
            $Query0 .= "LEFT JOIN c_pais c ON(c.fl_pais=b.ds_add_country ) ";
            $Query0 .= "WHERE fl_usuario=".$fl_post_usuario;
            $row0 = RecuperaValor($Query0);
            $ds_pais = $row0[0];

            # Find number of comments for this post
            $Query1 = "SELECT COUNT(1) FROM v_gallery_feed_comments WHERE fl_gallery_post=$fl_gallery_post_sp AND origen='$origen'";
            $row1 = RecuperaValor($Query1);
            $no_comments = $row1[0];
            if(empty($no_comments))
                $no_comments = 0;

            # Get last comment
            $Query2 = "SELECT MAX(fl_gallery_comment) fl_gallery_comment_sp_ultimo FROM k_gallery_comment WHERE fl_gallery_post=$fl_gallery_post_sp ORDER BY fe_comment DESC ";
            $row2 = RecuperaValor($Query2);
            $fl_gallery_comment_sp_ultimo = $row2[0];
            if(empty($fl_gallery_comment_sp_ultimo))
                $fl_gallery_comment_sp_ultimo = 0;



            # Check if this is an upload from desktop or straight from the board
            if(!empty($fl_entregable_sp)) {
                $type = "Desktop";


                # Retrieve desktop post info
                $Query3  = "SELECT a.fg_tipo, d.no_semana ";
                $Query3 .= "FROM k_entregable a ";
                $Query3 .= "LEFT JOIN k_entrega_semanal b ON(b.fl_entrega_semanal=a.fl_entrega_semanal) ";
                $Query3 .= "LEFT JOIN k_semana c ON(c.fl_semana=b.fl_semana) ";
                $Query3 .= "LEFT JOIN c_leccion d ON(d.fl_leccion=c.fl_leccion) ";
                $Query3 .= "WHERE a.fl_entregable=$fl_entregable_sp  ";
                $row3 = RecuperaValor($Query3);
                $fg_tipo = $row3[0];
                $no_semana = $row3[1];
                if($no_semana)
                    $no_semana=ObtenEtiqueta(1230)." ".$no_semana;

                switch($fg_tipo) {
                    case "A":		$fg_tipo = "Assignment";  break;
                    case "AR":	$fg_tipo = "Assignment Reference"; break;
                    case "S":   $fg_tipo = "Sketch";  break;
                    case "SR":	$fg_tipo = "Sketch Reference"; break;
                }



                if($nb_archivo !='') {
                    $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
                    if (($ext == 'jpg')||($ext=='jpeg')||($ext=='png')) {
                        # A student uploaded image
                        $nb_file = "<img src='" . PATH_ALU . "/sketches/board_thumbs/$nb_archivo' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\"  width='100%'>";
                    } else {
                        # A student uploaded video
                        $nb_file = "<img src='" . PATH_N_COM_UPLOAD . "/" . $uploads_origen . "/thumbs/vanas-board-video-default.jpg'  onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\" width='100%'>";
                    }
                }else{
                    $nb_file ="";
                }




            }else{


                    $type = "Board";
                    if($nb_archivo!='') {
                        $nb_file = "<div ><img src='" . PATH_N_COM_UPLOAD . "/" . $uploads_origen . "/thumbs/$nb_archivo' onclick=\"ViewPost($fl_gallery_post_sp,$fl_post_usuario,'$origen');\" width='100%'><div>";
                    }else{
                        $nb_file ="";
                    }
                    # If this post belongs to the board and is posted by this user, allow delete
                    if($fl_post_usuario == $fl_usuario){
                        $fg_my_post = true;
                    }



            }


            # Verificamos quienes son los usuarios que puden ver post de vanas mayores o igual a post-secundary
            if($edad>=18)
                $aviso = "<i class='fa fa-eye'></i> ".ObtenEtiqueta(2007);
            else
                $aviso = "";








        }


        $prueba=array();
    
            //obtenemos el listado de los comentarios por post
           $QueryCz = " SELECT c.fl_gallery_comment_sp, c.fl_gallery_post_sp, c.fl_usuario, c.ds_comment, c.origen, 
						CONCAT(u.ds_nombres, ' ', u.ds_apaterno) as ds_name, c.fe_comment,c.fg_correcto 
						FROM v_gallery_feed_comments c 
						left join c_usuario u on (c.fl_usuario=u.fl_usuario) 
						WHERE c.fl_gallery_post_sp=$fl_gallery_post_sp AND c.origen='$origen' ORDER BY c.fl_gallery_comment_sp ASC  ";
            $rsComentarios = EjecutaQuery($QueryCz);

            for($x=0; $comenta=RecuperaRegistro($rsComentarios); $x++){

                $fl_gallery_comment_sp=$comenta['fl_gallery_comment_sp'];
                $fl_gallery_post_sp2=$comenta['fl_gallery_post_sp'];
                $fl_usuario=$comenta['fl_usuario'];
				$fl_usuario_pertenece_post_=$comenta['fl_usuario'];
                $ds_comment=html_entity_decode($comenta['ds_comment']);
                $origen=$comenta['origen'];
                $ds_name=$comenta['ds_name'];
                $fe_comment=time_elapsed_string($comenta['fe_comment']);
                $avatar =ObtenAvatarUsuario($fl_usuario);
				$fg_post_correcto=$comenta['fg_correcto'];
				
				#reemplazamos carateres especiales.
				$ds_comment=str_replace("&#039;","",$ds_comment);
										
				
				#Recuperamos el total de likes que tiene actualmente.
				$Query="SELECT COUNT(1) as total FROM k_feed_likes WHERE fl_gallery_comment_sp=$fl_gallery_comment_sp and fg_origen='$origen' and fg_like='1'  ";
				$ro=RecuperaValor($Query);
				$total_like_comen=$ro[0];
				
				//Veriifica si ese comentario ya le dio like o nel.
				$Query="SELECT fl_like FROM k_feed_likes WHERE fl_gallery_comment_sp=$fl_gallery_comment_sp AND fl_usuario=$fl_usuario_origen ";
				$rom=RecuperaValor($Query);
				if(!empty($rom[0])){
					$ya_dio_like_comen=1;		
				}else{
					$ya_dio_like_comen=0;		
				}
				
				#//solo el usuario que posteo tiene la posibilidad de marcar las respuestas correctas y que en su comentraios aparezca para marcar //los de gaery no tienen ayuda
				if(($fl_post_usuario==$fl_usuario_origen)&&($fg_ayuda==1))
					$fg_ambulancia_comment=1;
				else
					$fg_ambulancia_comment=0;
				
				# Find number of comments for this post
				$Queryc = "SELECT COUNT(1) FROM v_gallery_feed_comments_comments WHERE fl_gallery_comment=$fl_gallery_comment_sp AND origen='$origen'";
				$rowc = RecuperaValor($Queryc);
				$no_comments_coment = $rowc[0];
				
				
			    $prueba_tres=array();
				#Recuperamos los comentarios de estos comentarios de tercer nivel.(Pendiente).
				 //obtenemos el listado de los comentarios por post
			    $QueryCz3 = " SELECT c.fl_gallery_comment_sp_comment, c.fl_gallery_comment, c.fl_usuario, c.ds_comment, c.origen, 
							CONCAT(u.ds_nombres, ' ', u.ds_apaterno) as ds_name, c.fe_comment,''fg_correcto 
							FROM v_gallery_feed_comments_comments c 
							left join c_usuario u on (c.fl_usuario=u.fl_usuario) 
							WHERE c.fl_gallery_comment=$fl_gallery_comment_sp AND c.origen='$origen' ORDER BY c.fl_gallery_comment_sp_comment ASC  ";
				$rsComentario3 = EjecutaQuery($QueryCz3);
                
				for($x3=0; $comenta3=RecuperaRegistro($rsComentario3); $x3++){
				
				    $fl_gallery_comment_sp_comment=$comenta3['fl_gallery_comment_sp_comment'];
					$fl_comentario_pertence=$comenta3['fl_gallery_comment'];
					$ds_comment3=html_entity_decode($comenta3['ds_comment']);
				    $origen=$comenta3['origen'];
                    $fl_usuario_hizo_comentario=$comenta3['fl_usuario'];
				    $ds_ruta_avatar_hizo_comentario=ObtenAvatarUsuario($fl_usuario_hizo_comentario);
                    $nb_usuario_hizo_comentario=ObtenNombreUsuario($fl_usuario_hizo_comentario);
                    $fe_comment=time_elapsed_string($comenta3['fe_comment']);




                    
                    #Recuperamos el total de likes que tiene actualmente.
                    $Query="SELECT COUNT(1) as total FROM k_feed_likes WHERE fl_gallery_comment_sp_comment=$fl_gallery_comment_sp_comment and fg_origen='$origen' and fg_like='1'  ";
                    $ro=RecuperaValor($Query);
                    $total_like_comen3n=$ro[0];

					//Veifica si el post ya le dio like o nel.
					$Query3n="SELECT fl_like FROM k_feed_likes WHERE fl_gallery_comment_sp_comment=$fl_gallery_comment_sp_comment AND fl_usuario=$fl_usuario_origen ";
					$ro=RecuperaValor($Query3n);
					if(!empty($ro[0])){
						$ya_dio_like3n=1;		
					}else{
						$ya_dio_like3n=0;		
					}
		
                    


				    $prueba_tres["comme".$x3]=array(
				                          
						  "fl_comentario"=>$fl_gallery_comment_sp_comment,
				          "fl_comentario_pertence"=>$fl_comentario_pertence,
                          "fl_usuario_actual"=>$fl_usuario_origen,
						  "comentario" => $ds_comment3,
						  "fg_origen" => $origen,
						  "fg_tiene_like"=>$ya_dio_like3n,
                          "tot_likes"=>$total_like_comen3n,
                          "fl_usuario_hizo_comentario"=>$fl_usuario_hizo_comentario,
                          "ds_ruta_avatar_hizo_comentario"=>$ds_ruta_avatar_hizo_comentario,
                          "nb_usuario_hizo_comentario"=>$nb_usuario_hizo_comentario,
                          "fe_comment"=>$fe_comment
				    );
				
				
				}
				
				
				
				
				
				
				
                $prueba["comm".$x] = array(
				"fl_comentario" => $fl_gallery_comment_sp, 
				"fl_post_pub" => $fl_gallery_post_sp2, 
				"fl_user" => $fl_usuario, 
				"fl_usuario_act"=>$fl_usuario_origen,
				"fl_usuario_pertenece_post_"=>$fl_usuario_pertenece_post_,
				"comentario" => $ds_comment, 
				"tot_likes"=>$total_like_comen,
				"fg_ayuda"=>$fg_ayuda,
				"fg_origen" => $origen,
				"no_comments" => $no_comments_coment,				
				"fg_tiene_like"=>$ya_dio_like_comen,
				"ds_name"=>$ds_name,
				"avatar"=>$avatar,
				"avatar_user_logueado"=>$AvatarUsLogueado,
				"fg_ambulancia_comment"=>$fg_ambulancia_comment,
				"fg_post_correcto"=>$fg_post_correcto,
				"comentarioSobreEsteComentario"=>$prueba_tres,
				"fe_comment"=>$fe_comment);
            }
        

				$result["item".$i] = array(
					"type" => $type,
					"fg_tipo" => $fg_tipo,
					"fl_usuario"=> $fl_post_usuario,
					"fl_usuario_origen"=>$fl_usuario_origen,
					"no_semana" => $no_semana,
                    "nb_programa_sp"=>$nb_programa_sp,
					"no_grado" => $no_grado,
					"ds_pais" => $ds_pais,
					"fg_my_post" => $fg_my_post,
					"fl_gallery_post" => $fl_gallery_post_sp,
					"ds_title" => $ds_title,
					"nb_instituto" => $nb_instituto,
					"nb_archivo" => $nb_file,
					"nb_usuario" => $ds_nombres,
					"fe_post" => $fechaFormato,
					"no_comments" => $no_comments,
					"fl_gallery_comment_sp_ultimo" => $fl_gallery_comment_sp_ultimo,
					"avatar" => $avatarUs,
					"fg_tiene_like"=>$ya_dio_like,
					"fg_delete_post"=>$delete_post,
					"fg_hidde_post"=>$hide_post,
					"fg_follow"=>$fg_follow,
					"tipo_icono_follower"=>$tipo_icono_follower,
					"fg_tiene_respuesta"=>$fg_tiene_respuesta,
					"fame" => $fame,
					"fg_post_oculto"=>$fg_post_oculto,
					"ds_post" => $ds_post,
					"no_likes"=>$no_likes,
					"origen"=>$origen,
					"comentariosPost"=>$prueba,
					"avatarGral"=>$avatarGeneral,
					"fg_ayuda"=>$fg_ayuda,
					"video_url"=>$urlVideo,
					"profesion"=>$ds_profesion,
					"compania"=>$compania,
					"fg_video_floplayer"=>$fg_video_floplayer
				);
		

		

    }






	if($i == 0){
		$result["index"] = array("end" => 0, "message" => "No records");
		echo json_encode((Object)$result);
		exit;
	}

	$result["size"] = array("total" => $i, "querypincipal"=>$Queryp);
	$result["index"] = array("end" => $index+$index_end);

	echo json_encode((Object) $result);
?>
