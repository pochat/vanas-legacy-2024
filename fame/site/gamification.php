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
  
  
  #Recuperamos el isttituo
  $fl_instituto=ObtenInstituto($fl_usuario);
  $presentar_renew=RecibeParametroNumerico('t', True); 

  
  #Recuperamos el nombre del usuario:
  
  $Query="SELECT ds_nombres,ds_apaterno,fl_perfil_sp FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Query);
  $ds_nombre=str_texto($row[0]);
  $ds_apaterno=str_texto($row[1]);
  $fl_perfil_fame=$row['fl_perfil_sp'];
  $nb_user_actual=$ds_nombre." ".$ds_apaterno;
  
  
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
  
  
  
  #Recupermas rutas de las imagenes.
  
  if(!empty($nb_avatar)){
         $ruta =  PATH_ADM."/../fame/site/uploads/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/".$nb_avatar;
  }else{
         $ruta=SP_IMAGES."/".IMG_S_AVATAR_DEF;
  
  }
  if(!empty($nb_foto_header)){
      
         $ruta_header= PATH_ADM."/../fame/site/uploads/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/".$nb_foto_header;;
  }else{
      
         $ruta_header=PATH_N_COM_IMAGES."/vanas-family-edutisse-header.jpg";
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
		max-height: 300px !important;
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
        height: 300px ;
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
	
	
    <!-- MAIN CONTENT -->
    <div id="content">

     
			<!-- row -->
				
				<div class="row">
					<div class="col-sm-12">




						<div id="myCarousel" class="carousel fade profile-carousel">
							<div class="air air-bottom-right padding-10">
									
                                <!--<h4 class="txt-color-white font-md"><?php echo $nb_user_actual;  ?></h4>-->
                                <!--
                                <a href="javascript:void(0);" class="btn txt-color-white bg-color-teal btn-sm"><i class="fa fa-check"></i> Follow</a>
                                &nbsp; <a href="javascript:void(0);" class="btn txt-color-white bg-color-pinkDark btn-sm"><i class="fa fa-link"></i> Connect</a>
                                -->
							</div>
							<div class="air air-top-left padding-10">

                                <i class="fa fa-camera txt-color-white" style=" cursor:pointer;"onclick="change_avatar('P');" ></i>

								<!--<h4 class="txt-color-white font-md">Mike JD</h4>-->
							</div>
							
							
							
							<div class="carousel-inner">
								<!-- Slide 1 -->
								<div class="item active">

									<img src="<?php echo $ruta_header; ?>" alt="" style="  max-height: 300px !important;    width: 100%;">
                                     
                                     <div  class="mikel_jd" >&nbsp;  </div>

								</div>
								
							</div>
						</div><!--end carrousel--->

                       
					</div>
					
					
					
						    <div class="col-sm-12">
				
							    <div class="row">
				
								    <div class="col-sm-2 col-xs-12 profile-pic text-left" style="" >
                                                     <i class="fa fa-camera txt-color-white camer" style="position:relative; z-index:7; left:105px; top:-61px; cursor:pointer;" aria-hidden="true"  onclick="change_avatar('A');"></i>
												    <img src="<?php echo $ruta;?>" style="width: 100px;height: 123px;"   >
												    <div class="padding-10 text-center" style="margin-top: -53px;">
													    <!--<h4 class="font-md"><strong>1,543</strong>
													    <br>
													    <small>Followers</small></h4>
													    <br>
													    <h4 class="font-md"><strong>419</strong>
													    <br>
													    <small>Connections</small></h4>--->

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
									    <div class="col-sm-4 col-xs-12 text-left">
												    <h1 style="font-size:29px;"><span class="semi-bold"><?php echo $ds_nombre;?></span> <?php echo $ds_apaterno;?>
												    <br>
												    <small><i class="fa fa-graduation-cap" aria-hidden="true"></i> <?php echo $nb_instituto; ?></small></h1>
                                                    <?php if($nb_pais){  ?>
                                                    <p class="text-muted" style="font-size:15px;"><i class="fa fa-globe" aria-hidden="true"></i> <?php  echo $nb_pais; ?></p>
                                                    <?php } ?>
                                                    <p></p>

							                        <!--
												    <ul class="list-unstyled">
													    <li>
														    <p class="text-muted">
															    <i class="fa fa-phone"></i>&nbsp;&nbsp;(<span class="txt-color-darken">313</span>) <span class="txt-color-darken">464</span> - <span class="txt-color-darken">6473</span>
														    </p>
													    </li>
													    <li>
														    <p class="text-muted">
															    <i class="fa fa-envelope"></i>&nbsp;&nbsp;<a href="mailto:simmons@smartadmin">miguel@loomtek.mx</a>
														    </p>
													    </li>
																			
												    </ul>
												    <br>
												    <p class="font-md">
													    <i>About me...</i>
												    </p>
												    <p>
							
													    Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio
													    cumque nihil impedit quo minus id quod maxime placeat facere
							
												    </p>
																		
												    <br>
												    <br>--->
				
								       </div>

                                      <div class="col-sm-6 col-xs-12 text-left">

                                          <h1>Programs<small></small></h1>
                                          <br />

                                              <div class="row">
                                                      <div class="col-md-12">
                                                         <ul class="list-inline friends-list"> 
                                                              <?php 
                                                                    # Obtenemos los programas que esta cursando
                                                                    $Queryy  = "SELECT b.nb_programa, b.nb_thumb ";
                                                                    $Queryy .= "FROM k_usuario_programa a ";
                                                                    $Queryy .= "LEFT JOIN c_programa_sp b ON(a.fl_programa_sp=b.fl_programa_sp) ";
                                                                    $Queryy .= "WHERE fl_usuario_sp=$fl_usuario ";
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

   





      <div class="row">


       <div class="tab-pane active" id="hr2">

								<ul class="nav nav-tabs" style="font-size:14px;">
									<li class="active" style="height: 70px;width: 150px;">
										<a href="#galeria" data-toggle="tab" aria-expanded="true" style="height: 68px;padding:26px;cursor:pointer;" OnClick="PresentaGaleria();">Galeria</a>
									</li>
									<!---<li class="" style="height: 70px;width: 150px;">
										<a href="#iss2" data-toggle="tab" aria-expanded="false" style="height: 68px;padding:26px;">Programs</a>
									</li>
									--->
								</ul>
								<div class="tab-content padding-10">
									<div class="tab-pane active in" id="galeria">
										

                                        <div class="row">
                                            <div class="col-md-12">
                                                 <div id="galerias"></div>
                                            </div>


                                        </div>

                                       

									</div>
									
									
								</div>

							</div>





    </div>








     
    

    </div>
    <!-- END MAIN CONTENT -->


<!--====script para lasimagenes====---->
<script>

    function PresentaGaleria( ) {

        var my_posts="on";

        $.ajax({

            type:  'POST',
            url:   'site/galeria.php',
            async: false,
            data: 'my_posts='+my_posts,
            async: true,
            success: function (html) {

                $('#galerias').html(html);
            }

        });



    }

    $(document).ready(function () {
        /** Se tuiliza para el nombre de las imagenes **/
        $("[rel=tooltip]").tooltip();
        PresentaGaleria();

    });


</script>

