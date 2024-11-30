<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");


	# Variable initializtion to avoid errors
	$style=NULL;
	
  $fg_visita_comunity=RecibeParametroNumerico('c',True);

   
  $fg_viene_feed=RecibeParametroNumerico('f',true);
  $fl_usuario_logueado=ValidaSesion(False,0, True);
  if(!empty($fg_visita_comunity)){
	  $fl_usuario_origen=RecibeParametroNumerico('uo',true);
	  $fl_usuario=RecibeParametroNumerico('profile_id',True);
	  
	  
  }else{
	  
	   # Verifica que exista una sesion valida en el cookie y la resetea
       $fl_usuario = ValidaSesion(False,0, True);

       $fl_usuario_origen=RecibeParametroNumerico('uo',true);

	  # Verifica que el usuario tenga permiso de usar esta funcion
	  if(!ValidaPermisoSelf(FUNC_SELF)) {  
		MuestraPaginaError(ERR_SIN_PERMISO);
		exit;
	  }
  }

  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  #Recuperamos el isttituo
  $fl_instituto=ObtenInstituto($fl_usuario);
  $presentar_renew=RecibeParametroNumerico('t', True); 

  
  #Recuperamos el nombre del usuario:
  
  $Query="SELECT ds_nombres,ds_apaterno,fl_perfil_sp FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Query);
  $ds_nombre=str_texto($row[0]);
  $ds_apaterno=str_texto($row[1]);
  $fl_perfil_fame=$row['fl_perfil_sp'];
  $nb_user_actual=ObtenNombreUsuario($fl_usuario,$fl_usuario_origen);
  
  #Verifica el follow del user.
  if($fl_usuario_logueado<>$fl_usuario){
  
    
  $Query="SELECT fl_followers FROM c_followers WHERE fl_usuario_destino=$fl_usuario AND fl_usuario_origen=$fl_usuario_logueado ";
  $rwo=RecuperaValor($Query);
  $fl_followers=!empty($rwo['fl_followers'])?$rwo['fl_followers']:NULL;
  
  
  }
  
  # Checamos que tabla va revisar
  if($fl_perfil == PFL_MAESTRO_SELF){
      $tbl = "c_maestro_sp ";
      $campo = "fl_maestro_sp ";
  }
  else{
      if($fl_perfil == PFL_ESTUDIANTE_SELF){
          $tbl = "c_alumno_sp";
          $campo = "fl_alumno_sp";
      }
      else{
          $tbl = "c_administrador_sp";
          $campo = "fl_adm_sp";
      }
  }
  
  $Query  = "SELECT ds_ruta_avatar,ds_ruta_foto ";
  $Query .= "FROM ".$tbl." ";
  $Query .= "WHERE ".$campo."=$fl_usuario";
  $row = RecuperaValor($Query);
  $nb_avatar=str_texto($row['ds_ruta_avatar']);
  $nb_foto_header=str_texto($row['ds_ruta_foto']);
  
  #Recuperamos los followers.
  $Query="SELECT COUNT(*) FROM c_followers WHERE fl_usuario_destino=$fl_usuario ";
  $row=RecuperaValor($Query);
  $no_followers=$row[0];
  
  
  #Recuperamos los following.
  $Query2="SELECT COUNT(*) FROM c_followers WHERE fl_usuario_origen=$fl_usuario ";
  $row2=RecuperaValor($Query2);
  $no_followed=$row2[0];
  
  
  #Recuperamos las respuestas correctas de este usuario.
  $Query="SELECT COUNT(*) FROM v_gallery_feed_comments WHERE fl_usuario=$fl_usuario AND fg_correcto='1' ";
  $rowm=RecuperaValor($Query);
  $no_ambulance=$rowm[0];
  
  
  #Recuperamos los post que tiene el usuario.
  $Query="SELECT COUNT(*) FROM v_gallery_feed WHERE fl_usuario=".$fl_usuario." AND origen='p';  ";
  $post=RecuperaValor($Query);
  $no_post=$post[0];
  
  
  #Recupermas rutas de las imagenes.
  
  if(!empty($nb_avatar)){
         $ruta =  PATH_ADM."/../fame/site/uploads/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/".$nb_avatar;
  }else{
         $ruta=SP_IMAGES."/".IMG_S_AVATAR_DEF;
  
  }
  if(!empty($nb_foto_header)){
      
         $ruta_header= PATH_ADM."/../fame/site/uploads/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/".$nb_foto_header;;
  }else{
      
      $ruta_header=PATH_SELF."/img/fame-family-edutisse-header.jpg";
  }
  
  
  
  #Recupermos al Instituto.
  $Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=".$fl_instituto ;
  $row=RecuperaValor($Query);
  $nb_instituto=str_texto($row['ds_instituto']);
  
  #Recuperamos l pais del estudiante.
  $Query="SELECT ds_pais FROM k_usu_direccion_sp U 
                  JOIN c_pais P ON U.fl_pais=P.fl_pais 
                  WHERE fl_usuario_sp=".$fl_usuario." ";
  $row=RecuperaValor($Query);
  $nb_pais=str_texto($row['ds_pais']);
  
  
  #Recuperamos el nombre del istituo
  
  
?>
	
	<link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="font-awesome-4.6.3/font-awesome-4.6.3/css/font-awesome.min.css">
	
	<style>
	
	.profile-carousel .carousel-inner {
		max-height: 350px !important;
	}
	/*.profile-pic>img {
   
		top: -100px !important;
	    max-width: 113px !important;
		left:45px !important;
	}*/

	 blockquote {
	      font-size:14px;
	        border-left: 5px solid #0071BD;
	    }
	.progress {

	    height: 10px;
        
    }

	.mikel_jd {
	    
        position: absolute;
        height: 350px ;
        width: 100%;
        left: 0px;
        top: 0px;
        background-color:rgba(105, 105, 247, 0.33);
        z-index: 2;
        
        
    }


	    @media only screen and (max-width: 479px) and (min-width: 320px) {
	        .profile-pic > img {
	            width: 90px;
	            margin-left: 0;
	            top: -58px !important;
	        }
	        .camer {
	           top: -86px !important;
            
            }


	    }



	</style>
	<div id='followers'></div>
	
    <!-- MAIN CONTENT -->
    <div id="content">

     
			<!-- row -->
				
				<div class="row">
					<div class="col-sm-12">




						<div id="myCarousel" class="carousel fade profile-carousel">
							<div class="air air-bottom-right padding-10">
								
							</div>
							<div class="air air-top-left padding-10">
								<?php if(empty($fg_visita_comunity)){ ?>
                                <i class="fa fa-camera txt-color-white" style=" cursor:pointer;"onclick="change_avatar('P');" ></i>
								<?php } ?>
								
							</div>
							
							
							
							<div class="carousel-inner">
								<!-- Slide 1 -->
								<div class="item active ">

									<img src="<?php echo $ruta_header; ?>" alt="" style="max-height:350px;   width: 100%;">
                                     
                                     <div  class="mikel_jd" >&nbsp;  </div>

								</div>
								
							</div>
						</div><!--end carrousel--->

                       
					</div>
					
					
					
						    <div class="col-sm-12">
				
							    <div class="row">
				
								    <div class="col-sm-2 col-xs-12 profile-pic text-left" style="" >
                                                    <?php if(empty($fg_visita_comunity)){ ?>
													<i class="fa fa-camera txt-color-white camer" style="position:relative; z-index:7; left:102px; top:-12px; cursor:pointer;" aria-hidden="true"  onclick="change_avatar('A');"></i>
												    <?php } ?>
													<img src="<?php echo $ruta;?>" style="width: 100px;height: 100px; top: 4px;"   >
												    <div class="padding-10 text-center" style="margin-top: -53px;">
													   

														<div id="presentModal"></div>
                                                       

                                                          <script>

                                                              function change_avatar(img) {

                                                                  
																  $.ajax({
																     type:'POST',
																	 url:'site/muestra_modal_cambiar_foto.php',
																	 data:'img='+img,
																     async: true,
																	 success: function (html) {
																		 $('#presentModal').html(html);
																	 }
																  
																  });
																  
																  

																  

                                                              }
                                                            </script>





												    </div>
									    </div>
									    <div class="col-sm-5 col-xs-12 text-left">
												    <h1 style="font-size:29px;"><span class="semi-bold"><?php echo $nb_user_actual;?> 
													<?php 
													 #Verifica el follow del user.
													if($fl_usuario_logueado<>$fl_usuario){
														
														if(!empty($fl_followers)){
				
															echo"<span class='follow_".$fl_usuario_logueado."_".$fl_usuario."' ><i class='fa fa-check-square-o height_user' style='$style; cursor:pointer;float:right;color:rgba(0,0,0,.6);' onclick='Unfollow($fl_usuario_logueado,$fl_usuario)' aria-hidden='true'></i></span>";
														}else{
															
															
															echo"<span class='follow_".$fl_usuario_logueado."_".$fl_usuario."' ><i class='fa fa-user-plus height_user' style='$style; cursor:pointer;float:right;color:rgba(0,0,0,.6);' onclick='Follow($fl_usuario_logueado,$fl_usuario);' aria-hidden='true'></i></span>";
														}
													}
													
													?>

													</span>
												    <br>
												    <small><i class="fa fa-graduation-cap" aria-hidden="true"></i> <?php echo $nb_instituto; ?></small></h1>
                                                    <?php if($nb_pais){  ?>
                                                    <p class="text-muted" style="font-size:15px;"><i class="fa fa-globe" aria-hidden="true"></i> <?php  echo $nb_pais; ?></p>
                                                    <?php } ?>
                                                    <p></p>
													<hr>
													<ul class="list-inline">
														<li>
														  <h1 style="font-size:25px;color:#4963ea;" ><a href="javascript:MuestraFollowers(<?php echo $fl_usuario;?>,1);" style="text-decoration: none;"><span id="follwer_<?php echo $fl_usuario;?>"><?php  echo $no_followers; ?></span></a></h1>
													      <span class="text-muted" style="font-size:15px;" ><?php echo ObtenEtiqueta(2403);?></span>
														</li>
														
														<li>
														  <h1 style="font-size:25px;color:#4963ea;" ><a href="javascript:MuestraFollowers(<?php echo $fl_usuario;?>,2);" style="text-decoration: none;"><span id="following_<?php echo $fl_usuario;?>"><?php  echo $no_followed; ?></span></a></h1>
													      <span class="text-muted" style="font-size:15px;" ><?php echo ObtenEtiqueta(2405);?></span>
														</li>
														
														
														<li>
														   <h1 style="font-size:25px;color:#4963ea;" ><a href="javascript:MuestraAnswers(<?php echo $fl_usuario;?>);" style="text-decoration: none;">  <?php  echo $no_ambulance; ?></a></h1>
													       <p class="text-muted" style="font-size:15px;"><?php echo ObtenEtiqueta(2514);?></p>
														</li>
														
														<li>
														   <h1 style="font-size:25px;color:#4963ea;" ><a href="javascript:MuestraPost(<?php echo $fl_usuario;?>);" style="text-decoration: none;">  <?php  echo $no_post; ?></a></h1>
													       <p class="text-muted" style="font-size:15px;"><?php echo ObtenEtiqueta(2408);?></p>
														</li>
														
														
														
														
														
														
														
													</ul>
													
													
													
													
													
													



							                      
				
								       </div>

                                      <div class="col-sm-5 col-xs-12 text-right">

                                          <h1><?php echo ObtenEtiqueta(2196);?><small></small></h1>
                                          <br />

                                              <div class="row">
                                                      <div class="col-md-12">
                                                         <ul class="list-inline friends-list"> 
                                                              <?php 
                                                                    # Obtenemos los programas que esta cursando
                                                                    $Queryy  = "SELECT b.nb_programa, b.nb_thumb ";
                                                                    $Queryy .= "FROM k_usuario_programa a ";
                                                                    $Queryy .= "LEFT JOIN c_programa_sp b ON(a.fl_programa_sp=b.fl_programa_sp) ";
                                                                    $Queryy .= "JOIN c_usuario c ON c.fl_usuario=a.fl_usuario_sp ";
                                                                    $Queryy .= "WHERE fl_usuario_sp=$fl_usuario AND fg_terminado='1' ";
                                                                    $Queryy .= "AND c.fl_instituto=$fl_instituto ";
                                                                    $rsj = EjecutaQuery($Queryy);

                                                                    for($j=0;$rowj = RecuperaRegistro($rsj);$j++){
                                                                        $nb_programa = $rowj[0];
                                                                        $nb_thumb = $rowj[1];
                                                                        $ruta_img = PATH_ADM."/modules/fame/uploads/".$nb_thumb;
                                                                        echo 
                                                                        "      
                                                                              <li>
                                                                                <a href='javascript:void(0);' rel='tooltip' 
                                                                                  data-placement='top' data-original-title='".$nb_programa."' data-html='true'><img src='".$ruta_img."' style='width:40px; height:40px;' ></a>
                                                                              </li>";
                                                                    }
                                                            
                                                            
                                                            
                                                               ?>
                                                    
                                                            </ul>
                                                      </div>
                                                     

                                              </div>

                                      </div>

							     </div>
				
						    </div>
					
					
					
					
					
				</div>	
				

        <br />

   <br/>





      <div class="row">


       <div class="tab-pane active" id="hr2">

								<ul class="nav nav-tabs" style="font-size:14px;">
									<li class="active text-center" style="height: 70px;width: 150px;">
										<a href="#galeria" data-toggle="tab" aria-expanded="true" style="height: 68px;padding:26px;cursor:pointer;" OnClick="PresentaGaleria(<?php echo $fl_usuario;?>);"><?php echo ObtenEtiqueta(2197);?></a>
									</li>
                                    <?php if(($fl_perfil==PFL_ADM_CSF)||($fl_perfil==PFL_ADMINISTRADOR)){ ?>
                                    
									<li class="text-center" style="height: 70px;width: 150px;">
										<a href="#awards" data-toggle="tab" aria-expanded="true" style="height: 68px;padding:26px;cursor:pointer;"><?php echo ObtenEtiqueta(2661);?></a>
									</li>
                                    <?php  } ?>
								</ul>
								<div class="tab-content padding-10">
									<div class="tab-pane active in" id="galeria">									
                                        <div class="row">
                                            <div class="col-md-12">
                                                 <div id="galerias"></div>
                                            </div>
                                        </div>
									</div>
                                    <div class="tab-pane fade " id="awards">
                                        <br />									
                                        <div class="row">
                                            
                                            <?php 
                                            $Query="SELECT nb_imagen FROM k_awards WHERE fl_instituto is null OR fl_instituto=$fl_instituto ";
                                            $rs=EjecutaQuery($Query);
                                            for($i=1;$row=RecuperaRegistro($rs);$i++) {
                                                $nb_imagen=$row['nb_imagen'];
                                                $ruta_awards="site/uploads/awards";

                                                $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
                                                $rand=NULL;
                                                $rand2=NULL;
                                                for($x = 0; $x < 40; $x++){
                                                    $rand .= substr($str, rand(0,62), 1);
                                                    $rand2.= substr($str, rand(0,62), 1);
                                                }
                                            ?> 
                                                    <div class="col-md-4">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="text-center">
                                                                    <img src="<?php echo $ruta_awards."/".$nb_imagen; ?>" class="rounded float-left" style="height:102px;" />
                                                                </div>
                                                                <br />
                                                                <hr />
                                                                <small class="text-muted" style="cursor:pointer;" data-toggle="collapse" data-target="#demo_<?php echo $i;?>" ><i class="fa fa-file-code-o" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2665);?></small><small class="text-muted" style="float:right;cursor:pointer;" onclick="copy_<?php echo $i; ?>();"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2666); ?></small>
                                                                <div  class="text-center collapse" id="demo_<?php echo $i;?>" >
                                                                    <textarea class="form-control text-muted" id="embed_code_<?php echo $i;?>" style="font-size: 85%;color: #999;"  rows="2"><a href="<?php echo ObtenConfiguracion(116)."/fame/accreditation.php?z=".$fl_instituto."_".$rand."&i=".$fl_instituto."_".$rand2;?>" style="cursor:pointer;" target="_blank"><embed type="image/jpg" src="<?php echo ObtenConfiguracion(116)."/fame/site/uploads/awards/".$nb_imagen; ?>" height="150px"></a></textarea>
                                                                </div>
 
                                                           </div>
                                                         
                                                        </div>
                                                    </div>

                                                    <script language="javascript">

                                                        function copy_<?php echo $i; ?>() {
                                                            var copy = document.getElementById('embed_code_<?php echo $i;?>');
                                                            copy.select();
                                                            document.execCommand("copy");
                                                        }
                                                    </script>

                                            <?php 
                                            }
                                            ?>
                                        </div>
                                        <br /><br /><br /><br />
									</div>
									
									
								</div>

							</div>





    </div>








     
    

    </div>
    <!-- END MAIN CONTENT -->








<!--====script para lasimagenes====---->
<script>

    function PresentaGaleria(fl_usuario_visita) {

        
        var my_posts="on";

        $.ajax({

            type:  'POST',
            url:   'site/galeria.php',
            async: false,
            data: 'my_posts='+my_posts+
                  '&fl_usuario=' + fl_usuario_visita,
            async: true,
            success: function (html) {

                $('#galerias').html(html);
            }

        });



    }

    $(document).ready(function () {
        /** Se tuiliza para el nombre de las imagenes **/
        $("[rel=tooltip]").tooltip();
        PresentaGaleria(<?php echo $fl_usuario; ?>);

    });


    function MuestraFollowers(fl_usuario,fg_accion) {

 

        $.ajax({
            type:  'POST',
            url:   'site/muestra_seguidores.php',
            async: false,
            data: 'fl_usuario='+fl_usuario+
				  '&fg_accion='+fg_accion,
            async: true,
            success: function (html) {
                $('#followers').html(html);
            }

        });
		
    }
	
	function MuestraAnswers(fl_usuario){
		
		 var fg_accion=1;
		 $.ajax({
            type:  'POST',
            url:   'site/muestra_answers.php',
            async: false,
		 data: 'fl_usuario='+fl_usuario+
			   '&fg_accion='+fg_accion,
            async: true,
            success: function (html) {
                $('#followers').html(html);
            }

        });
		
		
		
	}
	
	function MuestraPost(fl_usuario){
		
		 var fg_accion=1;
		 $.ajax({
            type:  'POST',
            url:   'site/muestra_post.php',
            async: false,
		 data: 'fl_usuario='+fl_usuario+
			   '&fg_accion='+fg_accion,
            async: true,
            success: function (html) {
                $('#followers').html(html);
            }

        });
		
		
		
	}
	
	
//para dejar seguir  tmbien esta en fame_feed.php
	function Unfollow(fl_usuario_origen,fl_usuario_destino){
		
		var fg_accion=2;
		
		 $.ajax({
	     url: '/fame/site/feed_follow_users.php',
		 data: 'fl_usuario_origen='+fl_usuario_origen+
		       '&fl_usuario_destino='+fl_usuario_destino+
			   '&fg_accion='+fg_accion,		  
		 type:'POST',
	    success: function (result){
			
			var resultado = JSON.parse(result);
		    var fg_correcto=resultado.fg_correcto;
			var no_follower=parseInt(resultado.follower);
			var no_following=parseInt(resultado.following);
			
			
			if(fg_correcto){
				 //Se genera correctamente el cambio.
				 $.smallBox({
					 title :"<?php echo ObtenEtiqueta(2402);?> ",  
					 content: "<br/>&nbsp;&nbsp;",
					 color: "#0071BD",
					 timeout: 30000,
					 icon: "fa fa-check-square-o"
					 //number : "1"
				 });	

				//cambiamos el icono de
				$(".follow_"+fl_usuario_origen+"_"+fl_usuario_destino+"").empty();
				$(".follow_"+fl_usuario_origen+"_"+fl_usuario_destino+"").append("<i class=\"fa fa-user-plus height_user\" style=\"cursor:pointer;float:right;color:rgba(0,0,0,.6);\" Onclick=\"Follow("+fl_usuario_origen+","+fl_usuario_destino+");\" ></i> ");
			
				 
				//Se suma coloca el nuevo total alo following
				 $('#follwer_'+fl_usuario_origen).empty();
				 $('#following_'+fl_usuario_origen).empty();
				 
				 $("#follwer_"+fl_usuario_origen).append(no_follower);
				 $("#following_"+fl_usuario_origen).append(no_following); 
				 
				 
				 
				}		 
			} 
		 
		});
		
		
		
		
	}

	
	//para seguir tambien esta en fame_feed.php
	function Follow(fl_usuario_origen,fl_usuario_destino){
		
		var fg_accion=1;
		
		 $.ajax({
	     url: 'site/feed_follow_users.php',
		 data: 'fl_usuario_origen='+fl_usuario_origen+
		       '&fl_usuario_destino='+fl_usuario_destino+
			   '&fg_accion='+fg_accion,		  
		 type:'POST',
	    success: function (result){
			
			var resultado = JSON.parse(result);
		    var fg_correcto=resultado.fg_correcto;
			var name_usr_origen=resultado.name_usr_origen;
			var name_usr_destino=resultado.name_usr_destino;
			var no_follower=parseInt(resultado.follower);
			var no_following=parseInt(resultado.following);
			
			if(fg_correcto){
				 //Se genera correctamente el cambio.
				 $.smallBox({
					 title :"<?php echo ObtenEtiqueta(2401);?> ",  
					 content: "<br/>&nbsp;&nbsp;",
					 color: "#0071BD",
					 timeout: 30000,
					 icon: "fa fa-check-square-o"
					 //number : "1"
				 });								 
				}

			//cambiamos el icono de
				$(".follow_"+fl_usuario_origen+"_"+fl_usuario_destino+"").empty();
				$(".follow_"+fl_usuario_origen+"_"+fl_usuario_destino+"").append("<i class=\"fa fa-check-square-o height_user\"  style=\"cursor:pointer;float:right;color:rgba(0,0,0,.6);\" Onclick=\"Unfollow("+fl_usuario_origen+","+fl_usuario_destino+");\" ></i> ");
			
			//Se suma coloca el nuevo total alo following
			 $('#follwer_'+fl_usuario_origen).empty();
			 $('#following_'+fl_usuario_origen).empty();
			 
		     $("#follwer_"+fl_usuario_origen).append(no_follower);
		     $("#following_"+fl_usuario_origen).append(no_following);
			 
			
			var etq_follow="<?php echo ObtenEtiqueta(2512);?>";
			//enviamos socket node.
			socket.emit('FollowUser',fl_usuario_origen,fl_usuario_destino,name_usr_origen,name_usr_destino,etq_follow);
			
			
			} 
		 
		});
		
		
		
		
	}


	
	
	
</script>

