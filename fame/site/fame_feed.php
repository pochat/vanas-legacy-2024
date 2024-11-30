<?php 
  # Libreria de funciones	
  require("../lib/self_general.php");
     
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_perfil_user_logueado=ObtenPerfilUsuario($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

$avatarGeneral =ObtenAvatarUsuario($fl_usuario);
  #Recuperamos el isttituo
  $fl_instituto=ObtenInstituto($fl_usuario);
  $presentar_renew=RecibeParametroNumerico('t', True);
require("../lib/nuevas_publicaciones.php");
?>

<script>

    function AcepptInvitedOherInstituto(fl_instituto,fg_respuesta,fl_user_invitador){   

        $.ajax({
	        type: 'POST',
            url: 'site/acept_invitation_instituto.php',
            data:'fl_instituto='+fl_instituto+
                 '&fl_user_invitador='+fl_user_invitador+
                 '&fg_respuesta='+fg_respuesta,
        async: true,
	    }).done(function (result){		
			
	        var resultado = JSON.parse(result);

	        var fl_user_invitador=resultado.fl_user_invitador;
	        var nb_user_envia_respuesta=resultado.nb_alumno_acepta;
	        var etq_descripcion=resultado.etq_descripcion;

			if(fg_respuesta==1){
				
				//Enviamos via node.js la confirmacion al teacher 
			    socket.emit('invitation-instituto-aceptada', fl_user_invitador,nb_user_envia_respuesta,etq_descripcion);
				
			}
			
			
	    });
		   
	}   
</script>	 

<?php 

#Verifica que tenga una invitacion de alguna otra escuela, entonces mostrara el dialogo para aceptar/rechazar la invitacion.
$Query="SELECT b.ds_instituto,b.fl_instituto,fl_usuario_invitando  FROM k_instituto_alumno a 
				JOIN c_instituto b ON a.fl_instituto=b.fl_instituto AND a.fl_instituto<>$fl_instituto
				WHERE a.fl_usuario_sp=$fl_usuario AND a.fg_aceptado='0'  ";
$rsi=EjecutaQuery($Query);

# IMPORTANT HERE, there is cases when Query2 is not initialized!!!!!!!!!!!!
if($fl_perfil_user_logueado==PFL_ESTUDIANTE_SELF)
    $Query2="SELECT COUNT(*)FROM  k_instituto_alumno WHERE fl_usuario_sp=$fl_usuario ";
if($fl_perfil_user_logueado==PFL_MAESTRO_SELF)
    $Query2="SELECT COUNT(*)FROM  k_instituto_teacher WHERE fl_maestro_sp=$fl_usuario ";

# IMPORTANT, this to avoid the error on previous comment
if (isset($Query2)) {
    $ro=RecuperaValor($Query2);
    $fg_tiene_institutos=$ro[0];
} else {
    $fg_tiene_institutos=NULL;
}


$Query="SELECT fg_select_instituto FROM c_usuario WHERE fl_usuario=$fl_usuario ";
$row=RecuperaValor($Query);
$fg_select_instituto=$row['fg_select_instituto'];

for($x=1;$rowi=RecuperaRegistro($rsi);$x++){
    
    $nb_instituto=$rowi['ds_instituto'];
    $fl_instituto_invitador=$rowi['fl_instituto'];
    $fl_user_invitador=$rowi['fl_usuario_invitando'];
    
    echo"
                <script>
                        $(document).ready(function() {
                            $.smallBox({
					            title : \"".ObtenEtiqueta(2561)."\",
					            content : \"  ".$nb_instituto." <p class='text-align-right'><a href='javascript:void(0);' onclick='AcepptInvitedOherInstituto(".$fl_instituto_invitador.",1,".$fl_user_invitador.");' class='btn btn-primary btn-sm'>Yes</a> <a href='javascript:void(0);' onclick='AcepptInvitedOherInstituto(".$fl_instituto_invitador.",0,0);' class='btn btn-default btn-sm'>No</a></p>\",
					            color : \"#296191\",
					            //timeout: 8000,
					            icon : \"fa fa-graduation-cap\"
				            });

                         });
                </script>
             ";

    
}





?>
<link rel="stylesheet" type="text/css" media="screen" href="css/fame_feed.css">
<style>
.carousel-inner>.item {
  
    position: relative !important;
	width:100% !important;
	font-size:18px;
	top: 0px !important;
}


.carousel-caption {
	bottom: 136px !important;
}

@media only screen and (max-width: 768px) {
	
	.carousel-caption {
	bottom: 77px !important;
    }
	
}
@media (min-width: 1500px){
    .modal-dialog {
        width: 1310px !important;
    }
}

@media (max-width:768px){
	
	.mikefeed{
		    width: 190px;
		
	}
	
}
.mikebold{
	
	font-weight:bold;
}
</style>







<!-- Modal Para Post-->
<div class="modal fade mike_modal_post" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm contenidoPost" role="document" style="margin-top:100px;">
    <div class="modal-content">
      <div class="modal-header ModalHeaderFeed">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo ObtenEtiqueta(2500);?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -27px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
	  <form  id="formulario" enctype='multipart/form-data'>
		  <div class="modal-body contenidoModalBody">
				<input type="hidden" id="fl_usuario" value="<?php echo $fl_usuario;?>">
				
				<div class="row">
					<div class="col-md-12">
					<textarea class="form-control" name="video" id="video" cols="30" rows="2" placeholder="<?php echo ObtenEtiqueta(2502);?>"></textarea>
					</div>

				</div>
				<div class="row">
					<div class="col-md-12 text-center">
						<img id="previewR" src="" alt="" />
						<iframe id="frame" width="300" height="100%" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
                        <input type="hidden" value="" name="video_url" id="video_url">
					</div>
				</div>
			
		  </div>
		  <div class="modal-footer">
			
						<span class="pull-left smart-form" style="margin-top: 14px; margin-right: 10px;"> <label class="checkbox pull-right">
                            <input type="checkbox" name="ayuda_post" id="ayuda_post">
							<i></i> <i class="fa fa-ambulance height_user" style="font-size: 22px;left: 24px;border-style: inherit;cursor: default;color:#999;box-shadow: inset 0 0px 0px rgba(0,0,0,.1);"></i></label>
                        </span>
			
						<div class="btn btn-link profile-link-btn">
                            <label for="file-input" style="margin-bottom: 0px!important;font-size: 25px!important;cursor: pointer ">
                                <i class="fa fa-camera"></i>
                            </label>
                            <input id="file-input" name="qqfile" type="file" style="display: none" accept="image/png,image/jpeg,image/jpg"/>
                        </div>
			
			
			
						<button type="submit"  class="btn btn-primary" id="enviar_datos_gd"><?php echo ObtenEtiqueta(2503);?></button>
		  </div>
	  </form>
    </div>
  </div>
</div>
<!---end modal--->





<!-- Modal Para mostrar usuario que dieron likes -->
<div class="modal fade" id="ModalLikesUser" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document" style="margin-top:80px;">
    <div class="modal-content" id="likes_usuarios">
	
    
        
		 
		
		
      
	  
	  
    </div>
  </div>
</div>





















<!--Modal vista por post fin-->
<div id="mensajeEnviado" ></div>


<div class="row" id="iniciaRedSocial">
    <div class="col-sm-8 col-md-8 col-lg-8 fondoPosts" >
        <div class="col-sm-12 col-md-12 col-lg-12 padding_0">
            <!-- your contents here -->
            <div class="panel panel-default borde-inferior">
                <div class="panel-body mikelp">
				
				
					<div class="row mikels" style="position:relative;z-index:1;">
					    
						<div class="col-sm-8 col-xs-7 col-md-8" style="background:#f5f5f5 ;padding-left:0px">
						<img src="<?php echo $avatarGeneral;?>" alt="img" class=" tamano_avatar" >
						<a  class="textoLinkPost" id="btn_postear" data-toggle="modal" data-target="#myModal2" style="border: 0px;color:#696c6f; margin-left:15px;">
												<i class="fa fa-edit"></i> <?php echo ObtenEtiqueta(2504);?>
										</a>
						</div>
						<div class="col-sm-4 col-xs-5 col-md-4" style="background:#f5f5f5 ;padding-right:0px">
						<a href="#" data-toggle="modal" data-target="#myModal2" id="btn_camera" class="pull-right iconosMoodal"  style="border-right: 1px solid #d4c4c4"><i class="fa fa-camera fa-fw fa-lg margin-top15" ></i></a>
										<a href="#" data-toggle="modal" id="btn_ambulancia" data-target="#myModal2" class="pull-right iconosMoodal" ><i class="fa fa-ambulance fa-fw fa-lg margin-top15"></i></a>
									
						
						</div>
						
					</div>
				
             
					
					
					

                </div>
            </div>

        </div>
        <div id="misdatos" >
            <div class="col-sm-12 col-lg-12" id="nueva-publicacion" style="padding: 0px;margin-top: 10px;"></div>
        </div>

    </div>
    <div class="col-sm-4 col-md-4 col-lg-4 hidden-xs" id="" >
	

	    <div id="btnInvitation">
			<div class="well well-lg col-sm-12 col-md-12 col-lg-12" style="padding: 8px;margin-bottom: 14px !important;">
			
		
				<a class="btn btn-default btn-lg btn-block" href="javascript:ModalSendInvitation(0);" ><i class="fa fa-gift" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2510); ?></a>
			</div>
		</div>
		
        <div class="well well-sm col-sm-12 col-md-12 col-lg-12 titulo_feed hidden">
            <?php echo ObtenEtiqueta(2506); ?>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-12 padding_0 hidden">

            <div class="panel panel-default">
                <div class="panel-body status">
                    <div class="who clearfix">
                        <div class="clearfix margin-bottom-5">
                            <img src="../fame/img/_1.png" alt="img" class="">
                            <span class="from pull-right">
                                    <i class="fa fa-user-plus  "></i>
                                    <i class="fa fa-ambulance "></i>
                            </span>
                            <span class="name"><b>Jose Rodriguez</b></span>
                            <span class="from"><b>Dean at Area Andina </b> Foundation Area Andina</span>
                        </div>
                        <div class="clearfix margin-bottom-5">
                            <img src="../fame/img/_1.png" alt="img" class="">
                            <span class="from pull-right">
                                    <i class="fa fa-user-plus "></i>
                                    <i class="fa fa-ambulance  "></i>
                            </span>
                            <span class="name"><b>Jose Rodriguez</b></span>
                            <span class="from"><b>Dean at Area Andina </b> Foundation Area Andina</span>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>





			<div class="well well-sm col-sm-12 col-md-12 col-lg-12 titulo_feed">
				<?php echo ObtenEtiqueta(2574); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="float:right;"><i class="fa fa-ambulance" aria-hidden="true"></i></span>
				<br><small ><span class="title_post_ayuda" id="conrespuesta" onclick="MuestraPostAyuda(1);"style="cursor:pointer;font-size:11px;" ><?php echo ObtenEtiqueta(2575);?></span>&nbsp;|&nbsp;<span class="title_post_ayuda"  id="sinrespuesta" onclick="MuestraPostAyuda(2);" style="cursor:pointer;font-size:11px;"><?php echo ObtenEtiqueta(2576);?></span></small>
			</div>
			<div class="col-sm-12 col-md-12 col-lg-12 padding_0">

				<div class="panel panel-default">
					<div class="panel-body status post_help_feed">
								
								
							<div id="view_post_help">	
								<?php 
								
								#Recuperamos las personas que estan haceindo preguntas.
								$Query="SELECT DISTINCT a.fl_publicacion,a.fl_usuario,a.ds_contenido,a.nb_img_video,a.fe_alta,video_url 
										FROM c_feed_publicaciones a 
										LEFT JOIN k_feed_comment b ON a.fl_publicacion=b.fl_publicacion AND b.fg_correcto='0' OR b.fg_correcto IS NULL  
										WHERE a.fg_ayuda='1' AND a.fg_oculto='0' ORDER BY a.fl_publicacion DESC LIMIT 3 ";
								$ay=EjecutaQuery($Query);
								$no_regis=CuentaRegistros($ay);
								
									$contas=0;
									for($a=0; $row=RecuperaRegistro($ay); $a++){
										$fl_usuario_post=$row['fl_usuario'];
										$fl_perfil_post=ObtenPerfilUsuario($fl_usuario_post);
										$fl_publicacion=$row['fl_publicacion'];
										$ds_post=html_entity_decode(str_uso_normal($row['ds_contenido']));
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
														<a href="javascript:void(0);" style="text-decoration:none;" data-toggle="modal" data-target="#ModalLikesUser">
														<i class="fa fa-heart-o" style="font-size: 17px;margin-right: 7px;"></i><span onclick="MuestraLikesUser(<?php echo $fl_publicacion;?>,1);"><?php echo $no_likes; ?></span></a></span>
													<span class="from" ><i class="fa fa-comment-o" style="font-size: 17px;margin-right: 7px;"></i><?php echo $no_coment;?></span>
												</div>
											</div>
										</div>
								
								
								
								<?php }?>
							</div>	
					</div>
				</div>
			</div>
			















			<div class="well well-sm col-sm-12 col-md-12 col-lg-12 titulo_feed">
				<?php echo ObtenEtiqueta(2406); ?> <span style="float:right;"><i class="fa fa-ambulance" aria-hidden="true"></i></span>
			</div>
			<div class="col-sm-12 col-md-12 col-lg-12 padding_0">

				<div class="panel panel-default">
					<div class="panel-body status">
				
	
								
								<?php 
								
								#Recuperamos las personas mas participativas con las que tienen mas respuestas.
								$Query="SELECT  fl_usuario,COUNT(fl_usuario) AS num FROM v_gallery_feed_comments
										WHERE fg_correcto='1' 
										GROUP BY fl_usuario ORDER BY num DESC LIMIT 3 ";
								$mf=EjecutaQuery($Query);
								$no_regis=CuentaRegistros($mf);
								
									$contas=0;
									for($z=0; $ros=RecuperaRegistro($mf); $z++){
										$fl_usuario_pop=$ros['fl_usuario'];
										$fl_perfil_pop=ObtenPerfilUsuario($fl_usuario_pop);
										$total=$ros[1];
								
								?>
								
								
									
							
									<div class="who clearfix">
										<div class="clearfix margin-bottom-5">
											
											<?php MuestraPerfilFeed($fl_usuario_pop,$fl_perfil_pop,$fl_usuario,'','',1,1,1); ?>
											
											<div class="col-md-12">
											<p class=' text-muted'><i class="fa fa-check-circle" style="color:#226108;"></i> <?php echo ObtenEtiqueta(2407);?>:<?php echo $total; ?> </p>
											</div>
											
										</div>
									</div>
								
						
								<?php } ?>

				
				
			
					</div><!--end panel-body -->
				</div><!--end panel default-->
		
		    </div>
		   
			<!------presenta persona mas participativas favorite post week------->
			<div id="columnRight">
				<div class="well well-sm col-sm-12 col-md-12 col-lg-12 titulo_feed">
					<?php echo ObtenEtiqueta(2507); ?> <span style="float:right;"><i class="fa fa-heart" aria-hidden="true"></i></span>
				</div>
			
				<div class="col-sm-12 col-md-12 col-lg-12 padding_0">

					<div class="panel panel-default">
						<div class="panel-body status">
								
													<?php
							$fe_actual= date('Y-m-d');
							#Damos formato de fecha alos parametros recibidos.
							$fe_ini =strtotime('1 days',strtotime($fe_actual)); 
							$fecha1= date('Y-m-d',$fe_ini);					
							$fe_dos =strtotime('-7 days',strtotime($fe_actual)); 
							$fecha2= date('Y-m-d',$fe_dos);
				
				
							#Se recupera los personas mas popilares de la semana.
							$Query="SELECT fl_gallery_post_feed, COUNT( fl_gallery_post_feed ) AS num
									FROM c_feed_likes a
									WHERE fe_alta >='$fecha2' AND fe_alta <='$fecha1'									
									GROUP BY fl_gallery_post_feed
									ORDER BY num DESC LIMIT 5  ";
							$rf=EjecutaQuery($Query);
							$conta=0;
							for($i=0; $row=RecuperaRegistro($rf); $i++){
								
								$fl_gallery_p=$row[0];
								$nu_likes=$row[1];
								
								
										#Recuperamos los usuario, que tiene
										$Query="SELECT a.fl_usuario,a.ds_post,a.nb_archivo,origen
												FROM  v_gallery_feed a
												WHERE a.fl_gallery_post_sp=$fl_gallery_p  AND a.fe_post >='$fecha2' AND a.fe_post <='$fecha1'  ";
										$ro=RecuperaValor($Query);											
										$conta++;
										$fl_usuario_po=!empty($ro[0])?$ro[0]:NULL;
										$fl_perfil_po=ObtenPerfilUsuario($fl_usuario_po);
										$fl_instituto_po=ObtenInstituto($fl_usuario_po);
										$ds_post= !empty($ro[1])?$ro[1]:NULL;
										$nb_archivo=str_texto(!empty($ro[2])?$ro[2]:NULL);
										$orig=!empty($ro[3])?$ro[3]:NULL;
										
										#Recortamo la cadena a 50 carateres.
										if(!empty($ds_post)){
										$ds_post=substr($ds_post, 0, 30).'...';
										}
                                        
                                        #Identificamos el archivo. y convertimos en caso de que sea un video floyplayer.
                                        $ext_archivo_=strtolower(ObtenExtensionArchivo($nb_archivo));
                                        if($ext_archivo_=="m3u8"){
                                            $nb_archivo=ObtenNombreArchivo($nb_archivo);
                                            $nb_archivo=$nb_archivo.".png";

                                        }



										
										#Post feeed
										if($orig=='p'){								
											$ruta_img_post=PATH_SELF_UPLOADS."/posts/feed_posts/thumbs/".$nb_archivo;                                           
										}
										//Post del board.
										if($orig=='g'){										
											$ruta_img_post=PATH_SELF_UPLOADS."/".$fl_instituto_po."/USER_" . $fl_usuario_po."/sketches/thumbs/".$nb_archivo;											
										}
											
										if($ext_archivo_=='m3u8'){
                                            $ruta_img_post=PATH_SELF_UPLOADS."/".$fl_instituto_po."/USER_".$fl_usuario_po."/videos/".$nb_archivo;  
                                            
                                        }
                                        

										#Recupermos los comentarios totales.
										$Query="SELECT COUNT(*) FROM v_gallery_feed_comments WHERE fl_gallery_post_sp=$fl_gallery_p ";
										$rpl=RecuperaValor($Query);
										$no_comneta=$rpl[0];
											
										if(!empty($fl_usuario_po)){		
												
												
											?>	
												
												<div class="who clearfix">
													<div class="clearfix margin-bottom-5">
														
														<?php MuestraPerfilFeed($fl_usuario_po,$fl_perfil_po,$fl_usuario,'','',1,1,1); ?>
														
														<div class="col-md-12">
															<p><small class='text-muted' style="cursor:pointer;" Onclick="ViewPost(<?php echo $fl_gallery_p;?>,<?php echo$fl_usuario_po;?>,'<?php echo $orig;?>');" ><?php echo $ds_post;?></small>
															<?php if($nb_archivo){ ?>
															<img src='<?php echo $ruta_img_post;?>' Onclick="ViewPost(<?php echo $fl_gallery_p;?>,<?php echo$fl_usuario_po;?>,'<?php echo $orig;?>');" style="height:35px; float:right;cursor:pointer;" class='img-responsive'>
															<?php } ?>
															</p>
															<span class="from" style="margin-right: 38px;">
																<a href="javascript:void(0);" style="text-decoration:none;" data-toggle="modal" data-target="#ModalLikesUser"><i class="fa fa-heart-o" style="font-size: 17px;margin-right: 7px;"></i>
																<span onclick="MuestraLikesUser(<?php echo $fl_gallery_p;?>,1);"><?php echo $nu_likes; ?></span></a></span>
															<span class="from" ><i class="fa fa-comment-o" style="font-size: 17px;margin-right: 7px;"></i><?php echo $no_comneta;?></span>
														</div>
													</div>
												</div>
												
											<?php
											if($conta==3){
												break;
											}
										}
								
							}
							?>	
													
						</div>
					</div>	
				</div>
			</div>
			
						
			
			
		
			   
	   
	</div>
</div>

<script>
//para identificar cuando llega el elemnto hasta arriba y se quede visible.
window.onscroll = function() {myFunction()};
var header = document.getElementById("columnRight");
var btonInvitacion = document.getElementById("btnInvitation");
var sticky = header.offsetTop;

function myFunction() {
	
   if (window.pageYOffset > sticky+5) {
    btonInvitacion.classList.add("btnInvitamike");
  } else {
    btonInvitacion.classList.remove("btnInvitamike");
  }

 <?php if($no_regis==10){ ?>	
 
 if (window.pageYOffset > sticky+10) {
 <?php } ?>	

 <?php if($no_regis==1){ ?>	
  //sticky+390 original	
  if (window.pageYOffset > sticky+85) {
  <?php } ?>	
<?php if($no_regis==2){ ?>	
  //sticky+390 original	
  if (window.pageYOffset > sticky+170) {
  <?php } ?>  

 <?php if($no_regis==3){ ?>	
  //sticky+390 original	
  if (window.pageYOffset > sticky+991) {
  <?php } ?>  
    header.classList.add("stickymike");
  } else {
    header.classList.remove("stickymike");
  }
  
 
  
}
</script>


<script src="js/fame_feed.js"></script>
<!--<script type="text/javascript" src="js/jquery-1.4.1.js"></script>
<script type="text/javascript" src="js/coldfusion.json.js"></script>
<script type="text/javascript" src="js/phototagger.jquery.js"></script>-->
<script type="text/javascript">
/*esto se lo dejo a futuro es para poder hacer tags en fotos.
            // When the DOM is ready, initialize the scripts.
            jQuery(function( $ ){

                // Set up the photo tagger.
                $( "div.fotoTags" ).photoTagger({

                    // The API urls.
                    loadURL: "/fame/site/load_tags.cfm",
                    saveURL: "/fame/site/save_Tag.cfm",
                    deleteURL: "/fame/site/delete_tag.cfm",

                    // Default to turned on.
                    // isTagCreationEnabled: false,

                    // This will allow us to clean the response from
                    // a ColdFusion server (it will convert the
                    // uppercase keys to lowercase keys expected by
                    // the photoTagger plugin.
                    cleanAJAXResponse: cleanColdFusionJSONResponse
                });


                // Hook up the enable create links.
                $( "a.enable-create" ).click(
                    function( event ){
                        // Prevent relocation.
                        event.preventDefault();
                        console.log("aki");
                        // Get the container and enable the tag
                        // creation on it.
                        $( this ).prevAll( "div.fotoTags" )
                            .photoTagger( "enableTagCreation" )
                        ;
                    }
                );


                // Hook up the disabled create links.
                $( "a.disable-create" ).click(
                    function( event ){
                        // Prevent relocation.
                        event.preventDefault();

                        // Get the container and enable the tag
                        // creation on it.
                        $( this ).prevAll( "div.fotoTags" )
                            .photoTagger( "disableTagCreation" )
                        ;
                    }
                );


                // Hook up the enable delete links.
                $( "a.enable-delete" ).click(
                    function( event ){
                        // Prevent relocation.
                        event.preventDefault();

                        // Get the container and enable the tag
                        // deletion on it.
                        $( this ).prevAll( "div.fotoTags" )
                            .photoTagger( "enableTagDeletion" )
                        ;
                    }
                );


                // Hook up the disabled delete links.
                $( "a.disable-delete" ).click(
                    function( event ){
                        // Prevent relocation.
                        event.preventDefault();

                        // Get the container and disabled the tag
                        // deletion on it.
                        $( this ).prevAll( "div.fotoTags" )
                            .photoTagger( "disableTagDeletion" )
                        ;
                    }
                );

            });
*/
        </script>
<!--<script src="js/motor_plantillas/handlebars-v4.1.0.js">
<script src="js/axios.min.js"></script>-->

<script>
 $(document).on('click','#btn_postear', function( event ) {
	 
	 //Limpiamos el modal  //temporal en lo que encuentro otra forma
	 $('#postId').modal('hide');
	 $('#externalInvitacion').modal('hide');
	 $('#enviar_datos_gd').removeClass('disabled');
	 //colocamos la ambulacia por default
	var elem = document.getElementById('ayuda_post');
    elem.checked = false; 	
	 
	 $('#previewR').attr('src',''); 
	 $('#frame').attr('src', '');
	 $('#video_url').value('');
	 $('#video').value('');
	 
	 
	 
 });
 
 $(document).on('click','#btn_ambulancia', function( event ) {
	//colocamos la ambulacia por default
	document.getElementById('ayuda_post').checked = true;
	$('#enviar_datos_gd').removeClass('disabled');
 });
 $(document).on('click','#btn_camera',function(event){
	document.getElementById('ayuda_post').checked=false;
	$('#enviar_datos_gd').removeClass('disabled');
 });
 
function ColocarScrollAlFondo(fl_comentario_padre,fl_usuario_pertenece_post_principal){
	
	//alert(fl_comentario_padre+'llego'+fl_usuario_pertenece_post_principal);
	
	var ContendorScoll=document.getElementById('comment_list_'+fl_comentario_padre+'_'+fl_usuario_pertenece_post_principal+'');
		ContendorScoll.scrollTop = ContendorScoll.scrollHeight;
	
}


    /*OBTENIENDO TODOS LOS POST*/

    $(document).ready(function() {
        var container;
        container = $("#misdatos");

        var index, selectedPost, selectedFilter, classmate, myPosts;
        index = 0;
        selectedPost = 0;
        selectedFilter = 0;
        classmate = 0;
        myPosts = 0;
       console.log("inicia pagina");
        requestItemsboard(container);
        $(window).on('scroll.infinite', function (){
            if($(window).scrollTop() == $(document).height() - $(window).height()) {
                //boardController.requestItemsboard(container);
                requestItemsboard(container);
            }
        });

        function requestItemsboard (container) {
            $.ajax({
                type: 'GET',
                url: '/fame/site/feed_publicacion_post_items.php',
                data: //'tema=' + selectedFilter +
                // '&classmate='+classmate+
                //'&my_posts=' + myPosts +
                '&index=' + index
            }).done(function (result) {
                var elems = JSON.parse(result);
                // Check for end of board
                if (elems.index.end > 0) {
                    // update index
                    console.log("pasa aki");
                    index = elems.index.end;
                    console.log(index);
                    displayItems(container, elems);
                    // $selector.packery('reloadItems');
                }
                else {
                    $selector.append("<h2 class='row-seperator-header txt-color-red'><i class='fa  fa-warning'></i> " + elems.index.message + " </h2>");
                }
            });
        }
		
		
		
		
    });

    //pita todos los comentarios de pirmer nivel
    var displayItems = function(container, elems){
        var item, items, deleteMe, type, comentario, ayuda,total_registros_comentarios,contador;
        items = "";


        for(var i=0; i<elems.size.total; i++){
            item = elems["item"+i];
            var comentariosList=[];
			
			total_registros_comentarios=Object.keys(item.comentariosPost).length;
			
            if(Object.keys(item.comentariosPost).length>0) {
				
				
				var contador=0;
                for (var x = 0; x < Object.keys(item.comentariosPost).length; x++) {
					
					contador++;
					
					var ite;
					var ComentarioSobreComentarioList=[];
					
                    //comentario = item.comentariosPost['comm'+x];
                    //console.log(item.comentariosPost['comm'+x]);
					var fl_comentari=item.comentariosPost['comm'+x].fl_comentario;
					var fg_orige=item.comentariosPost['comm'+x].fg_origen;
					var total_likes=item.comentariosPost['comm'+x].tot_likes;
					var fg_tiene_like_comen=item.comentariosPost['comm'+x].fg_tiene_like;
					var fg_ayuda_comen=item.comentariosPost['comm'+x].fg_ayuda;
					var fl_gallery_post=item.comentariosPost['comm'+x].fl_post_pub;
					var fg_post_correcto=item.comentariosPost['comm'+x].fg_post_correcto;
					var fg_ambulancia_comment=item.comentariosPost['comm'+x].fg_ambulancia_comment;
					var fl_usuario_comenta=item.comentariosPost['comm'+x].fl_user;
					var fl_usuario_pert_post=item.comentariosPost['comm'+x].fl_usuario_act;
					var fl_usuario_pertenece_post_=item.comentariosPost['comm'+x].fl_usuario_pertenece_post_;
					var fl_usuario_actual=item.comentariosPost['comm'+x].fl_usuario_act;
					var total_comentarios=item.comentariosPost['comm'+x].no_comments;
					var ds_comentario2n=item.comentariosPost['comm'+x].comentario;
					var fl_usuario_pertenece_post_principal=item.fl_usuario;
					
					
					
					if(contador==total_registros_comentarios){
						
						var fg_forzar_scroll=1;
					}else{
						var fg_forzar_scroll=0;
					}
					
					
					
					ite=item.comentariosPost['comm'+x];
					
					if(Object.keys(item.comentariosPost['comm'+x].comentarioSobreEsteComentario).length>0){
						 
						 
						 
						 
						 for (var m = 0; m < Object.keys(ite.comentarioSobreEsteComentario).length; m++) { 
						    
							var fl_comentario_3n=ite.comentarioSobreEsteComentario['comme'+m].fl_comentario;
						    var ds_comentario_3n=ite.comentarioSobreEsteComentario['comme'+m].comentario;
						    var ds_ruta_avatar_hizo_comentario_3n=ite.comentarioSobreEsteComentario['comme'+m].ds_ruta_avatar_hizo_comentario;
							var nb_usuario_hizo_comentario_3n=ite.comentarioSobreEsteComentario['comme'+m].nb_usuario_hizo_comentario;
							var fe_comment_3n=ite.comentarioSobreEsteComentario['comme'+m].fe_comment;
							var fg_origen_3n=ite.comentarioSobreEsteComentario['comme'+m].fg_origen;
							var fl_usuario_hizo_comentario_3n=ite.comentarioSobreEsteComentario['comme'+m].fl_usuario_hizo_comentario;
							var fl_usuario_actual_3n=ite.comentarioSobreEsteComentario['comme'+m].fl_usuario_actual;
							var ya_dio_like_3n=ite.comentarioSobreEsteComentario['comme'+m].fg_tiene_like;
							var tot_likes_3n=ite.comentarioSobreEsteComentario['comme'+m].tot_likes;

							if(ya_dio_like_3n==1){
								
								var icono_3n="fa-heart";
                                var color_3n="mikelike";
							}else{
								var icono_3n="fa-heart-o";
						        var color_3n="";	
							}
							
							
							
						    ComentarioSobreComentarioList+="<li id=\"comenttario_3n_"+fl_comentario_3n+"\" class=\"comentarios_3n_"+fl_comentario_3n+"\" style=\"border-bottom: 0px;\">"+
														   "  <img src=\""+ds_ruta_avatar_hizo_comentario_3n+"\" alt=\"img\" style=\"margin-left:5px;\">"+
														   "      <span class=\"name\"><a onclick=\"location.href='#site/myprofile.php?profile_id="+fl_usuario_hizo_comentario_3n+"&c=1&uo="+fl_usuario_actual_3n+"&f=1'\" style=\"cursor:pointer;\">"+nb_usuario_hizo_comentario_3n+"</a>"+
														   "      <span class=\"name pull-right paloma_3n_"+fl_comentario_3n+" hidden\" id=\"fg_correct_3n_"+fl_comentario_3n+"\"><i class=\"fa fa-check-circle\" style=\"margin-right:10px;color:#226108;\" aria-hidden=\"true\"></i>  </span></span>"+
														   "      <span class=\"from\" style=\"opacity: 0.7;font-size: 12px;\">"+fe_comment_3n+"</span>"+
														   "  	   <br>"+ds_comentario_3n+" "+
														   "	   <br>";
														   
							ComentarioSobreComentarioList+="	   <ul class=\"list-inline\">"+
														   "			<span><a href=\"javascript:LikePostComent("+fl_comentario_3n+",'"+fg_origen_3n+"',"+fl_usuario_hizo_comentario_3n+",2);\" style=\"text-decoration:none;\"><i id=\"like_3n_"+fl_comentario_3n+""+fg_origen_3n+"\" style=\"margin:3px;\" class=\"fa "+icono_3n+" likes "+color_3n+"\" aria-hidden=\"true\" ></i></a></span><a href=\"javascript:void(0);\"  data-toggle=\"modal\" data-target=\"#ModalLikesUser\" style=\"text-decoration:none;\" ><span id=\"cont_lik3n_"+fl_comentario_3n+""+fg_origen_3n+"\" onclick=\"MuestraLikesUser("+fl_comentario_3n+",3);\" style=\"text-decoration:none;\">"+tot_likes_3n+"</span></a>&nbsp;&nbsp;"+
														   "			<span class=\"hidden\"><a href=\"javascript:void(0);\" style=\"text-decoration:none;\"><i id=\"coment_276p\" style=\"margin:3px;\" class=\"fa fa-comment-o\" aria-hidden=\"true\"></i><span id=\"tot_com_276p\" style=\"text-decoration:none;\">0</span></a></span>       &nbsp;&nbsp;"+
														   "	   </ul>"+
														   "</li> ";
						 
							
						 
						 }
						 
					}
					
                    
					
					//determines if you like the user logged in
					if(fg_tiene_like_comen==1){			
						var icono="fa-heart";
                        var color="mikelike";						
					}else{
						var icono="fa-heart-o";
						var color="";						
					}
					
					
					
					
					//para pintar como respuesta correcta en los comentarios.
					if(fg_post_correcto==1){
					   var class_post_correcto="marcado_correcto";
				       var fg_paloma_correcta="";
					}else{
					   var class_post_correcto="";
					   var fg_paloma_correcta="hidden"; 
					}
					
					
					
				
				   
				   
					
                comentariosList +=
                "<li id=\"comenttario_"+fl_comentari+"\"   class=\"comentarios_"+fl_gallery_post+" "+class_post_correcto+"\"   >"+
                  "   <img src=\" "+item.comentariosPost['comm'+x].avatar +"\" alt=\"img\"  style=\"margin-left:5px;\">"+
                  "   			  <span class=\"name\"><a onclick=\"location.href='#site/myprofile.php?profile_id="+fl_usuario_comenta+"&c=1&uo="+fl_usuario_actual+"&f=1'\"  style=\"cursor:pointer;\">  "+item.comentariosPost['comm'+x].ds_name +" </a> ";
				//la paloma que marca como respuesta correcta ala ambulabcia.
				comentariosList +="   <span class=\"name pull-right paloma_"+fl_gallery_post+" "+fg_paloma_correcta+" \" id=\"fg_correct_"+fl_comentari+"\"><i class=\"fa fa-check-circle\" style=\"margin-right:10px;color:#226108;\" aria-hidden=\"true\"></i>  </span>";
				

				comentariosList +="</span>\n";
				
				/*  
				comentariosList += 
				  "   <span class=\"name pull-right\" id=\"menu_"+fl_comentari+"\">\n";
				comentariosList +=
				  "       	<i class=\"fa fa-ellipsis-h height_user dropdown-toggle\" data-toggle=\"dropdown\"></i>";
				
				comentariosList +=
				  "        <div class=\"popover bottom dropdown-menu\" style=\"top: 49px;right:-7px;\"><div class=\"arrow\"></div>\n" +
                  "        		<div class=\"popover-content\">\n" +
                  "                <ul class=\" text-left\" style=\"list-style: none;padding: 0; \">\n";		
			    comentariosList +=
			      "					<li class=\"mike_opt\">\n"+
                  "    				  <a class=\"mike_opt\"style=\"text-decoration: none;font-weight: 400;\" href=\"javascript:MarcarCorrecto("+fl_comentari+");\"><i class=\"fa fa-check \"style=\"margin: 4px;\"></i>Marcar como respuesta correcta</a>\n" +
                  "                 </li>\n";
				comentariosList +="</ul>"+
				  "       		</div>"+
				  "       </div>";
				comentariosList +=  
				  "	  </span>";
				 */ 
				comentariosList +=  
                  "   <span class=\"from\" style=\"opacity: 0.7;font-size: 12px;\">"+item.comentariosPost['comm'+x].fe_comment +"</span>\n" +
                  "   <br>"+
				  "   </span>"+item.comentariosPost['comm'+x].comentario+"</span>\n" +
				  "<br><br>"+
				  "    <ul class=\" list-inline\">"+
				  "       <span ><a href=\"javascript:LikePostComent("+fl_comentari+",'"+fg_orige+"',"+fl_usuario_comenta+",1);\" style=\"text-decoration:none;\"><i id=\"like_"+fl_comentari+""+fg_orige+"\" style=\"margin:3px;\" class=\"fa "+icono+" likes "+color+"\" aria-hidden=\"true\"  onclick=\"\" ></i></a></span><a href=\"javascript:void(0);\" style=\"text-decoration:none;\" data-toggle=\"modal\" data-target=\"#ModalLikesUser\" ><span id=\"cont_lik"+fl_comentari+""+fg_orige+"\"  onclick=\"MuestraLikesUser("+fl_comentari+",2);\"  style=\"text-decoration:none;\"  >"+total_likes+"</span></a>"+
				  "       &nbsp;&nbsp;"+
				  "       <span class=\"c\"><a href=\"javascript:void(0);\" style=\"text-decoration:none;\"><i id=\"coment_"+fl_comentari+""+fg_orige+"\" style=\"margin:3px;\" class=\"fa fa-comment-o\" aria-hidden=\"true\"></i><span id=\"tot_com_"+fl_comentari+""+fg_orige+"\" style=\"text-decoration:none;\">"+total_comentarios+"</span></a></span>"+ 
				  "       <span ><a class=\"replyfeed\" onclick=\"Reply("+fl_comentari+","+fl_usuario_pertenece_post_+",'"+item.origen+"',"+fl_gallery_post+","+fl_usuario_pertenece_post_principal+","+fg_forzar_scroll+");\" > Reply</a></span>"+
				  "       &nbsp;&nbsp;";
				  
				  //Solo muestra la ambulabcia si el post necesita ayuda o fue marcado para pedir ayuda y que solo el usuario que hizo el post original tiene esa posibilidad de marcar.
				  if(fg_ambulancia_comment==1){
			      comentariosList +=  
				  "       <span><a href=\"javascript:MarcarCorrecto("+fl_gallery_post+","+fl_comentari+");\" style=\"text-decoration:none;\"><i id=\"ambulan_"+fl_comentari+""+fg_orige+"\" style=\"margin:3px;\" class=\"fa fa-ambulance disable_"+fl_gallery_post+" \" aria-hidden=\"true\"></i></a></span>";
				  }
																																																																																																					
				  //para comentarios de tercer nivel.
				  comentariosList +=  
				  "		  <li id=\"newCommentComment"+fl_comentari+item.origen+"\" class=\"hidden\" >"+
				  "		  	<img src=\""+item.comentariosPost['comm'+x].avatar_user_logueado+"\" alt=\"img\" class=\"onlines\">"+
				  "          <input type=\"text\" id=\"comentario_comen_"+fl_comentari+"_"+fl_usuario_pertenece_post_+"\" value=\"\" name=\"comentario\" class=\"form-control comentario mikeinput\" style=\"width:95%;\" onkeypress=\"javascript:comment_resp(event,"+fl_comentari+",'"+fg_orige+"',this,"+fl_usuario_pertenece_post_+",1,"+fl_gallery_post+","+fl_usuario_pertenece_post_principal+","+fg_forzar_scroll+");\"  placeholder=\"<?php echo ObtenEtiqueta(2509);?>\">\n" +
				  "		  </li>";
				 
				  comentariosList +=  
				  
				  "       <div id=\"all_coment_coment_"+fl_comentari+"_"+fl_usuario_pertenece_post_+"\">"+ ComentarioSobreComentarioList +
				  
				  "       </div>"+
				 
				 
				 
				 
				  "</ul>"+
                "</li>";

                }
            }
            
			
			//determines if you like the user logged in
			if(item.fg_tiene_like==1){			
				var icono="fa-heart";
				var color="mikelike";				
			}else{
				var icono="fa-heart-o";
				var color="";				
			}
			
			
            
            if(item.fg_ayuda==1){
                ayuda=" <i class=\"fa fa-ambulance height_user\"></i>";
				
				
					//Para marcar que la pregunta ya tiene respuesta.
				    if(item.fg_tiene_respuesta==1){
					  var class_paloma_marcado_respuesta="";
						
					}else{
					  var class_paloma_marcado_respuesta="hidden";	
						
					}
				
				
            }else{
                ayuda="";
				var class_paloma_marcado_respuesta="hidden";	
            }
			
			
			if(item.fg_post_oculto==1){
				
			}else{
			
			
			
            items +=
            "<div class=\"col-sm-12 col-lg-12\" style=\"padding: 0px;margin-top: 10px;\" id=\"post_"+item.fl_gallery_post+"\" >" +
            "<div class=\"panel panel-default\">" +
            "<div class=\"panel-body status\">" +
            "<div class=\"who clearfix borde_inferior\">" +
            "<img src=\""+item.avatar+"\" alt=\"img\" class=\" \">" +
            "<span class=\"name pull-right\">\n";
			
			if(item.fg_follow==1){
			items +=
            "  <span class='follow_"+item.fl_usuario_origen+"_"+item.fl_usuario+"'> "+item.tipo_icono_follower+"</span>\n";
			}
			
			items +=""+ayuda+"";
			
			
			//si pertenece al board no yeien opcion de oucltar
			if((item.origen=='g')&&(item.fl_usuario_origen==item.fl_usuario)){
				var ocultar_="hidden";
			}else{
				var ocultar_="";
			}
			
			
			
            items +="<i class=\"fa fa-ellipsis-h height_user dropdown-toggle "+ocultar_+"\" data-toggle=\"dropdown\"></i>";
			
			
			items +=
			"<div class=\"popover bottom dropdown-menu \" style=\"top: 34px;right:10px;\"><div class=\"arrow\"></div>\n" +
                "<div class=\"popover-content\">\n" +
                "<ul class=\" text-left\" style=\"list-style: none;padding: 0; \">\n";
			//El mismo usuario no tendra la opcion de  ocultar su misma publicacion solo elimnar.
			if(item.fg_hidde_post==1){
			items +=
			    "<li class=\"mike_opt\">\n"+
                "<a class=\"mike_opt\"style=\"text-decoration: none\" href=\"javascript:hide_post("+item.fl_gallery_post+","+item.fl_usuario_origen+",'"+item.origen+"');\"><i class=\"fa fa-eye-slash \"style=\"margin: 4px;\"></i> Hide this post</a>\n" +
                "</li>\n";
			}	
				
			if(item.fg_delete_post==1){	
			items +=
			    "<li class=\"mike_opt\">\n" +
                "<a class=\"mike_opt\" style=\"text-decoration: none\" href=\"javascript:remove_post("+item.fl_gallery_post+","+item.fl_usuario+",'"+item.origen+"');\"><i class=\"fa fa-trash-o \"style=\"margin: 4px;\"></i> Remove this post</a>\n" +
                "</li>\n";
			}
			items +=
			    "</ul></div>"+
             "</div>"+
            "                            </span>\n" +
            "                            <div class=\"margin-left-60\">\n" +
            "                                <span class=\"name\"><b><a   onclick=\"location.href='#site/myprofile.php?profile_id="+item.fl_usuario+"&c=1&uo="+item.fl_usuario_origen+"&f=1'\"  style=\"cursor:pointer;\" >"+item.nb_usuario+"</a></b></span>\n" +
            "                                <span class=\"from\"><b>"+item.profesion+" - "+item.compania+"</b></span><br>  \n ";
			if(item.nb_programa_sp){
			    items +="                    <span class=\"from\"><i class=\"fa fa-book\" aria-hidden=\"true\"></i> "+item.nb_programa_sp+"&nbsp;"+item.no_semana+"</span><br>\n ";
            }
            items+="                  <span class=\"from\">"+item.fe_post+"</span>\n" +
            "                            </div>" +
            "</div>\n" +
            "<div class=\"text padi_texto_comentario\">"+item.ds_post+"</div>\n" +

            "                        <div class=\"image imagen_post\" style=\"cursor:pointer;\" >\n";
            
			//cuando viene un video de  floy player
			//if(item.fg_video_floplayer==1){
			//items += " "+item.+"";
			//}else{
			
			items += 
			" "+ item.nb_archivo+item.video_url+" ";
			//}
			
			items +=
            "                        </div>\n"+


            "                        <ul class=\"links\">\n" +
            "                            <li >\n" +
            "                                <a href=\"javascript:like_post("+ item.fl_gallery_post+",'"+item.origen+"','','',"+item.fl_usuario+");\" style=\"text-decoration:none;\" ><i id=\"like_act"+ item.fl_gallery_post+item.origen+"\" class=\"fa "+icono+" "+color+" \"></i></a></span><a href=\"javascript:void(0);\" style=\"text-decoration:none;\" data-toggle=\"modal\" data-target=\"#ModalLikesUser\" ><span style=\"text-decoration:none;\"  onclick=\"MuestraLikesUser("+item.fl_gallery_post+",1);\"  id=\"link_cont"+ item.fl_gallery_post+item.origen+"\" >"+item.no_likes+"</span></a>\n" +
            "                            </li>\n" +
            "                            <li>\n" +
            "                                <a href=\"javascript:void(0);\" style=\"text-decoration:none;\" ><i class=\"fa fa-comment-o\" style=\"text-decoration:none;\" ></i><span id=\"comment_plus"+ item.fl_gallery_post+item.origen+"\" style=\"text-decoration:none;\" >"+item.no_comments+"</span></a>\n" +
            "                            </li>\n";
			//aparecera cuando se marca como correcto y en primera instacncia nos indica que ya tiene una respuesta.
			items+=
			"                            <li id=\"marcado_correcto_"+item.fl_gallery_post+"\" class=\""+class_paloma_marcado_respuesta+"\">"+
			"                                <a href=\"javascript:void(0);\" onclick=\"ViewPostCorrect("+item.fl_gallery_post+","+item.fl_usuario+")\" ><i class=\"fa fa-check-circle\" style=\"color:#236308ad;\"></i> </a>"+
			"                            </li>";
			
		   
			
		
			
			
			items+=
            "                            <!--<li>\n" +
            "                                <a href=\"javascript:void(0);\"><i class=\"fa fa-share-square-o\"></i> Share</a>\n" +
            "                            </li>-->\n" +
            "                        </ul >\n" +
            "                       <!--inician los comentarios-->"+
            "                        <ul class=\"comments style-10\" id=\"comment_list_"+item.fl_gallery_post+"_"+item.fl_usuario+"\" style=\"max-height: 333px;overflow-x: auto;\">\n" +
            
			
			"					         <div id=\"all_coment_"+item.fl_gallery_post+"_"+item.fl_usuario+"\">"+comentariosList+"</div>"+
			
            "                        </ul>\n" +
			
			"                        <ul class=\"comments style-10 sborde\" id=\"comment_list_"+item.fl_gallery_post+"_"+item.fl_usuario+"_writecoment\" >\n" +
            
			"                            <li style=\"padding-bottom: 16px;\" id=\"newComment"+ item.fl_gallery_post+item.origen+"\">\n" +
            "                                <img src=\""+item.avatarGral+"\" alt=\"img\" class=\"onlines\">\n" +
            "                                <input type=\"text\" id=\"comentario_"+item.fl_gallery_post+"_"+item.fl_usuario+"\" value=\"\" name=\"comentario_"+item.fl_gallery_post+"_"+item.fl_usuario+"\" class=\"form-control comentario mikeinput\" onkeypress=\"javascript:comment_post(event,"+ item.fl_gallery_post+",'"+item.origen+"',this,'','',"+item.fl_usuario+");\"  placeholder=\"<?php echo ObtenEtiqueta(2509);?>\">\n" +
            "                            </li>\n"+
			
			
			"                        </ul>\n" +
			
            "                    </div>" +
            "</div>" +
            "</div>   " ;
			
			
			
			
			

			}
			
			
			
			
			
			
			
        }
        // Attach items and load packery
        container.append($(items));
        container.imagesLoaded(function(){
            container.packery({ columnWidth: 320, itemSelector: ".item", gutter: 10 });
        });
		
		
    };
    /*PUBLICANDO  UN POST*/
    postContainer = $("#modal-content");
    $('#formulario').submit(function ( e ) {

        $('#enviar_datos_gd').addClass('disabled');
        
		//cerramos el modal antes de enviar los datos.
		$('#myModal2').modal('toggle');
		
        var data = new FormData(this); //Creamos los datos a enviar con el formulario
        $.ajax({
            url: '/fame/site/feed_publicacion_gd.php', //URL destino
            data: data,
            processData: false, //Evitamos que JQuery procese los datos, daría error
            contentType: false, //No especificamos ningún tipo de dato
            type: 'POST',
            success: function (result) {
                var content = JSON.parse(result);
                console.log(content.post, content.avatar);
                //dato="22436";console.log(dato);
                $('#myModal2').modal('hide');
                $('#formulario')[0].reset();
                //$("#file-input").val('');
                //$("#previewR").attr("src", "");
                socket.emit('publicacionesRecientesMarzo',content.post,content.avatar,content.profesion,content.compania,content.fe_post);
            }
        });

        e.preventDefault(); //Evitamos que se mande del formulario de forma convencional



    });
    /*LIKES POST*/
    function like_post(fl_gallery_post,fg_origen, fame=1,fg_modal,fl_usuario_post_original){
        var fl_gallery_post1=fl_gallery_post;
		
		if(fg_modal==1)
			var fg_modal=1;
		else
			var fg_modal=2;
		
		

        var parametros = {
            "item" : fl_gallery_post1,
            "fg_origen" : fg_origen,
            "fame" : fame,
			"fl_usuario_post_original" : fl_usuario_post_original,
			"fg_modal":fg_modal,
        };
        //console.log(parametros);
        $.ajax({
            url: '/fame/site/feed_post_like.php', //URL destino
            data:  parametros,
            type: 'POST',
            success: function (result) {
                var content = JSON.parse(result);
                console.log(content.post,content.origen,content.fl_usuario,content.fg_accion);
                socket.emit('likesPublicaciones',content.post,content.origen, content.fl_usuario,content.fg_accion,fg_modal,content.fl_usuario_post_inicial);
            }
        });
    }


  function remove_post(fl_gallery_post,fl_usuario,fg_tipo){
	  //Ocultamos el post.
	  $('#post_'+fl_gallery_post).addClass('hidden');
	  var fg_accion=2;  
	  
	  //Mandmos als BD para ocultar el post
	  $.ajax({
	     url: '/fame/site/feed_post_hide.php',
		 data: 'fl_gallery_post='+fl_gallery_post+
		       '&fl_usuario='+fl_usuario+
			   '&fg_accion='+fg_accion+
			   '&fg_tipo='+fg_tipo,
		 type:'POST',
	    success: function (result){
			
			var resultado = JSON.parse(result);
		    var fg_correcto=resultado.post;
			if(fg_correcto){

				 //Se genera correctamente el cambio.
				 $.smallBox({
					 title :"<?php echo ObtenEtiqueta(2398);?> ",  
					 content: "<br/>&nbsp;&nbsp;",
					 color: "#659265",
					 timeout: 30000,
					 icon: "fa fa-trash"
					 //number : "1"
				 });
				 
				 
				 socket.emit('DeletePost',fl_gallery_post,fl_usuario,fg_tipo);
			}
		 
	    } 
		 
	 });
	  
	  
	  
	   //socket.emit('RemovePost',fl_gallery_post);
	  
  }
  
  function hide_post(fl_gallery_post,fl_usuario,fg_tipo){
	  
	  //Nota: el fg_tipo define de donde pertenece la publicacion, fg_tipo=g de galeria / fg_tipo=p de los post en fame feed.
	  
	  //Ocultamos el post.
	  $('#post_'+fl_gallery_post).addClass('hidden');
	  var fg_accion=1;
	  
	  //Mandmos als BD para ocultar el post
	  $.ajax({
	     url: '/fame/site/feed_post_hide.php',
		 data: 'fl_gallery_post='+fl_gallery_post+
		       '&fl_usuario='+fl_usuario+
			   '&fg_accion='+fg_accion+
			   '&fg_tipo='+fg_tipo,
		 type:'POST',
	    success: function (result){
			
			var resultado = JSON.parse(result);
		    var fg_correcto=resultado.post;
			if(fg_correcto){

				 //Se genera correctamente el cambio.
				 $.smallBox({
					 title :"<?php echo ObtenEtiqueta(2384);?> ",  
					 content: "<br/>&nbsp;&nbsp;",
					 color: "#659265",
					 timeout: 30000,
					 icon: "fa fa-eye-slash"
					 //number : "1"
				 });
				 
				
				 
			}
		 
	    } 
		 
	 });
	  
	  
  }


    function comment_post(e,fl_gallery_post,fg_origen,data,fame=1,fg_modal,fl_usuario_post_original){
		
		
		if(fg_modal==1){
			var fg_modal=1;
		}else{
			var fg_modal=2;
		}
		
		
		

		if(e.which == 13) {
		    var parametros = {
		        "item": fl_gallery_post,
		        "fg_origen": fg_origen,
		        "fame": fame,
		        "comentario":data.value
		    };
		    //console.log(parametros);
		    $.ajax({
		        url: '/fame/site/feed_post_comment.php', //URL destino
		        data:  parametros,
		        type: 'POST',
		        success: function (result) {
		            var content = JSON.parse(result);
		            console.log(content.fl_gallery_post,content.post,content.origen,content.avatar,content.fg_post_ayuda,content.fl_usuario_post_origen,fg_modal);
					
					
		            var ds_comentario=document.getElementById("comentario_"+fl_gallery_post+"_"+fl_usuario_post_original+"").value; //es el input del comentario.
					
					
					
		            var comentario="";				
		            var container_coment=$("#all_coment_"+fl_gallery_post+"_"+fl_usuario_post_original+"");
		            var container_coment_modal=$("#all_coment_"+fl_gallery_post+"_"+fl_usuario_post_original+"_m2");
		            var fl_comentario=content.post;
					
					
					
					
		            comentario +="<li id=\"comenttario_"+fl_comentario+"\"    class=\"comentarios_"+fl_gallery_post+"\" ><img src='"+content.avatar+"' alt=\"img\" class=\"onlines\"  >"+
					             "	<span class=\"name\"><a onclick=\"location.href='#site/myprofile.php?profile_id="+fl_usuario_post_original+"&c=1&uo="+fl_usuario_post_original+"&f=1'\"  style=\"cursor:pointer;\">"+content.nombre_user_comentando+"</a>";
		            comentario +="	<span class=\"name pull-right paloma_ hidden \" id=\"fg_correct_\"><i class=\"fa fa-check-circle\" style=\"margin-right:10px;color:#226108;\" aria-hidden=\"true\"></i>  </span>";
				
		            comentario +="	   </span>"+
								 "      <span class=\"from\" style=\"opacity: 0.7;font-size: 12px;\">1s</span><br/>"+data.value+
						         "		<br><br>";
					
		            //para presentar likes 
		            comentario +="	<ul class='list-inline'>"+
								 "       <span ><a href=\"javascript:LikePostComent("+fl_comentario+",'"+content.origen+"',"+fl_usuario_post_original+", 1);\" style=\"text-decoration:none;\"><i id=\"like_"+fl_comentario+""+content.origen+"\" style=\"margin:3px;\" class=\"fa fa-heart-o likes\" aria-hidden=\"true\"  onclick=\"\" ></i></a></span><a href=\"javascript:void(0);\" style=\"text-decoration:none;\" data-toggle=\"modal\" data-target=\"#ModalLikesUser\"><span  style=\"text-decoration:none;\"  onclick=\"MuestraLikesUser("+fl_comentario+",2);\"  id=\"cont_lik"+fl_comentario+""+content.origen+"\">0</span></a>"+
								 "       &nbsp;&nbsp;"+
								 "       <span class=''><a href=\"javascript:void(0);\" style=\"text-decoration:none;\"><i id=\"coment_"+fl_comentario+""+content.origen+"\" style=\"margin:3px;\" class=\"fa fa-comment-o\" aria-hidden=\"true\"></i><span id=\"tot_com_"+fl_comentario+""+content.origen+"\">0</span></a></span>"+ 
								 "       <span ><a class=\"replyfeed\" onclick=\"Reply("+fl_comentario+","+fl_usuario_post_original+",'"+content.origen+"',"+content.fl_gallery_post+","+fl_usuario_post_original+",1);\" > Reply</a></span>"+	 
								 "       &nbsp;&nbsp;";
		            //Solo muestra la ambulabcia si el post necesita ayuda o fue marcado para pedir ayuda y que solo el usuario que hizo el post original tiene esa posibilidad de marcar.
		            if(content.fg_post_ayuda==1){
						
		                if(content.fl_usuario_esta_comentanto==fl_usuario_post_original){
		                    comentario +="         <span><a href=\"javascript:MarcarCorrecto("+fl_gallery_post+","+fl_comentario+");\" style=\"text-decoration:none;\"><i id=\"ambulan_"+fl_comentario+""+content.origen+"\" style=\"margin:3px;\" class=\"fa fa-ambulance disable_"+fl_comentario+" \" aria-hidden=\"true\"></i></a></span>";
		                }
		            }
		            comentario +="		<li  id=\"newCommentComment"+fl_comentario+""+content.origen+"\" class=\"hidden\">"+
								 "			<img src='"+content.avatar+"' alt=\"img\" style=\"margin-left:5px;\"  > "+
								 "          <input type=\"text\" id=\"comentario_comen_"+fl_comentario+"_"+content.fl_usuario_post_origen+"\" value=\"\" name=\"comentario\" class=\"form-control comentario mikeinput\" style=\"width:95%;\" onkeypress=\"javascript:comment_resp(event,"+fl_comentario+",'"+content.origen+"',this,"+content.fl_usuario_post_origen+");\"    placeholder=\"Post your comment...\">\n" +
								 "		</li>"+
								 "		<div id=\"all_coment_coment_"+fl_comentario+"_"+content.fl_usuario_post_origen+"\">"+
								 "	    </div>"+
								 "	</ul>";				
		            comentario +="</li>";
					
					
					
		            container_coment.append($(comentario));
					
		            //pinta cuando abres el modal de un post.
		            if(fg_modal==1){
		                //alert(fg_modal);
		                container_coment_modal.append($(comentario));
		            }
		            $("#comentario_"+fl_gallery_post+"_"+fl_usuario_post_original+"").val("");
		            if(content.fl_usuario_post_origen!=content.fl_usuario_esta_comentanto ){
                     
		                socket.emit('comentariosPublicaciones',content.post,content.origen,content.avatar,content.fg_post_ayuda,content.fl_usuario_post_origen,fg_modal,content);
		            }
                    data.value="";
					
					//Enviamos el scroll al final del cnuevo comentario.
					var div_all_coment=document.getElementById('comment_list_'+fl_gallery_post+'_'+fl_usuario_post_original+''); 
						div_all_coment.scrollTop = '9999';
					
					//Envia al notice los ecomentarios de todos los usuarios que estan enrolados a este post
					var total_registros_comentarios=Object.keys(content.emails_enviados).length;
					
					 for (var x = 0; x < Object.keys(content.emails_enviados).length; x++) {
						 
						 var fl_usuario_destino=content.emails_enviados['emails'+x].fl_usuario_destino;					 
						 //enviamos notificacion al monito de users.
						 socket.emit('EnviaNoticeComentariosPublicaciones',fl_usuario_destino);
                    
						 console.log('fl_usuario->'+fl_usuario_destino);
						 
					 }
					
					
					
                }
            });
        }
    }


    //comentarios de tercer nivel.event,"+fl_comentari+",'"+fg_orige+"',this,"+fl_usuario_pertenece_post_+","+fl_usuario_pertenece_post_principal+","+fg_forzar_scroll+"
    function comment_resp(e,fl_comentari,fg_origen,data,fl_usuario_comentario_original,fame=1,fl_comentario_padre,fl_usuario_pertenece_post_principal,fg_forzar_scroll){
        if(e.which == 13) {
            var parametros = {
                "fl_comentari": fl_comentari,
                "fg_origen": fg_origen,
                "fame": fame,
				"fl_usuario_post_que_se_esta_comentando":fl_usuario_comentario_original,
                "comentario":data.value
            };
            //console.log(parametros);
            $.ajax({
                url: '/fame/site/feed_comment_commentarios.php', //URL destino
                data:  parametros,
                type: 'POST',
                success: function (result) {
                    var content = JSON.parse(result);
                    console.log(content.fl_comentario,content.origen,content.avatar);
					
					var ds_comentario=document.getElementById("comentario_comen_"+fl_comentari+"_"+fl_usuario_comentario_original+"").value; //es el input del comentario.
					
					//ocultamos el input de arriba.
					//$("#comentario_comen_"+fl_comentari+"_"+fl_usuario_comentario_original+"").addClass("hidden");
					
					var comentario="";				
					var container_coment=$("#all_coment_coment_"+content.fl_comentario+"_"+fl_usuario_comentario_original+"");
					
					var container_coment_modal=$("#all_coment_coment_"+content.fl_comentario+"_"+fl_usuario_comentario_original+"_m");
					//var fl_comentario=content.post;
					
					comentario+="<li id=\"comenttario_3n_"+content.fl_comentario+"\" class=\"comentarios_3n_"+content.fl_comentario+"\" style=\"border-bottom: 0px;\">"+
								"  <img src=\""+content.avatar+"\" alt=\"img\" style=\"margin-left:5px;\">"+
				                "      <span class=\"name\"><a onclick=\"location.href='#site/myprofile.php?profile_id="+content.fl_usuario_comentando+"&c=1&uo="+content.fl_usuario_comentando+"&f=1'\" style=\"cursor:pointer;\">"+content.nb_nombre_user_comentando+"</a>"+
								"      <span class=\"name pull-right paloma_3n_"+content.fl_comentario+" hidden\" id=\"fg_correct_3n_"+content.fl_comentario+"\"><i class=\"fa fa-check-circle\" style=\"margin-right:10px;color:#226108;\" aria-hidden=\"true\"></i>  </span></span>"+
								"      <span class=\"from\" style=\"opacity: 0.7;font-size: 12px;\">1s</span>"+
								"  	   <br>"+ds_comentario+" "+
								"	   <br>"+
								"	   <ul class=\"list-inline\">"+
								"			<span><a href=\"javascript:LikePostComent("+content.fl_comentario+",'"+content.origen+"',"+content.fl_usuario_comentando+",2);\" style=\"text-decoration:none;\"><i id=\"like_3n_"+content.fl_comentario+""+content.origen+"\" style=\"margin:3px;\" class=\"fa fa-heart-o likes\" aria-hidden=\"true\" ></i></a></span><a href=\"javascript:void(0);\" style=\"text-decoration:none;\" data-toggle=\"modal\" data-target=\"#ModalLikesUser\"> <span  onclick=\"MuestraLikesUser("+content.fl_comentario+",3);\" id=\"cont_lik3n_"+content.fl_comentario+""+content.origen+"\" style=\"text-decoration:none;\">0</span></a>&nbsp;&nbsp;"+
								"			<span class=\"hidden\"><a href=\"javascript:void(0);\" style=\"text-decoration:none;\"><i id=\"coment_276p\" style=\"margin:3px;\" class=\"fa fa-comment-o\" aria-hidden=\"true\"></i><span id=\"tot_com_276p\" style=\"text-decoration:none;\">0</span></a></span>       &nbsp;&nbsp;"+
								"	   </ul>"+
								
								
				  
								"</li>";
					//comentario+="<li id=\"newCommentComment\" class=\"\" >"+
						//		"  	<img src=\"/fame/site/uploads/4/USER_642/avatar_642_24947.jpg\" alt=\"img\" class=\"onlines\">"+
						//		"   <input type=\"text\" id=\"comentario_comen_"+fl_comentari+"_"+fl_usuario_comentario_original+"_r\" value=\"\" name=\"comentario\" class=\"form-control comentario mikeinput\" style=\"width:95%;\" onkeypress=\"javascript:comment_resp(event,this);\"  placeholder=\"<?php echo ObtenEtiqueta(2509);?>\">\n" +
						//		"</li>";
					
					//limpiamos el input despues de enviar valores.
					document.getElementById("comentario_comen_"+fl_comentari+"_"+fl_usuario_comentario_original+"").value = "";
					
					container_coment.append($(comentario));
                    
					
					socket.emit('comentariosPublicacionesRespuestas',content.fl_comentario_new,content.origen,content.avatar,content.fl_usuario_comentando,fl_usuario_comentario_original);
                   
				    //se coloca el comentario si esta en modal
				    container_coment_modal.append($(comentario));
				   
				    if(fg_forzar_scroll==1){
						
						var ContendorScoll=document.getElementById('comment_list_'+fl_comentario_padre+'_'+fl_usuario_pertenece_post_principal+'');
		 
						ContendorScoll.scrollTop = ContendorScoll.scrollHeight;
						
						
					}
				   
				   
                }
            });
        }
    }





	function Follow(fl_usuario_origen,fl_usuario_destino){
		
		var fg_accion=1;
		
		 $.ajax({
	     url: '/fame/site/feed_follow_users.php',
		 data: 'fl_usuario_origen='+fl_usuario_origen+
		       '&fl_usuario_destino='+fl_usuario_destino+
			   '&fg_accion='+fg_accion,		  
		 type:'POST',
	    success: function (result){
			
			var resultado = JSON.parse(result);
		    var fg_correcto=resultado.fg_correcto;
			var name_usr_origen=resultado.name_usr_origen;
			var name_usr_destino=resultado.name_usr_destino;
			
			
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
				$(".follow_"+fl_usuario_origen+"_"+fl_usuario_destino+"").append("<i class=\"fa fa-check-square-o height_user\" Onclick=\"Unfollow("+fl_usuario_origen+","+fl_usuario_destino+");\" ></i> ");
			
			var etq_follow="<?php echo ObtenEtiqueta(2512);?>";
			//enviamos socket node.
			socket.emit('FollowUser',fl_usuario_origen,fl_usuario_destino,name_usr_origen,name_usr_destino,etq_follow);
			
			
			} 
		 
		});
		
		
		
		
	}


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
				$(".follow_"+fl_usuario_origen+"_"+fl_usuario_destino+"").append("<i class=\"fa fa-user-plus height_user\" Onclick=\"Follow("+fl_usuario_origen+","+fl_usuario_destino+");\" ></i> ");
			
				 
				}		 
			} 
		 
		});
		
		
		
		
	}

   
	function EnviarInvitacion(){
		
		
		var fname=document.getElementById('first_name').value;
		var lname=document.getElementById('last_name').value;
		var email=document.getElementById('email').value;
		
		
		if(fname.length>0){		
			var fg_exito=1;
			$("#first_name_input_error").removeClass("state-error");
		    $("#first_name_texto_error").addClass("hidden");
		}else{
			var fg_exito=0;
			$("#first_name_input_error").addClass("state-error");
		    $("#first_name_texto_error").removeClass("hidden");
			return;
		}
		if(lname.length>0){		
			var fg_exito=1;
			$("#last_name_input_error").removeClass("state-error");
		    $("#last_name_texto_error").addClass("hidden");
		}else{
			var fg_exito=0;
			$("#last_name_input_error").addClass("state-error");
		    $("#last_name_texto_error").removeClass("hidden");
			return;
		}
		
		if(email.length>0){		
			var fg_exito=1;
			$("#email_input_error").removeClass("state-error");
		    $("#email_texto_error").addClass("hidden");
		}else{
			var fg_exito=0;
			$("#email_input_error").addClass("state-error");
		    $("#email_texto_error").removeClass("hidden");
			return;
		}
		
		//validamos email.
		emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
		
		if (emailRegex.test(email)) {
	 
		  	 $("#email_input_error").removeClass("state-error");
		     $("#err_email").addClass("hidden");
			 var fg_exito=1;
		} else {
			$("#email_input_error").removeClass("state-success");
			$("#email_input_error").addClass("state-error");
			$("#err_email").removeClass("hidden");
			return;
			
		 
		}
		
		
		if(fg_exito==1){
		
		
					
					$.ajax({
						url: '/fame/site/feed_send_external_invitation.php', //URL destino
						data: 'fname='+fname+
						      '&lname='+lname+
							  '&email='+email,
						//processData: false, //Evitamos que JQuery procese los datos, daría error
						//contentType: false, //No especificamos ningún tipo de dato
						type: 'POST',
						success: function (result) {
							var content = JSON.parse(result);
							
							var fg_email_repetido=content.fg_email_repetido;
							
							
							if(fg_email_repetido==1){
								//muestra que el email es repetido ya exite.
								$("#anteriormente_ya_fue_enviado").removeClass("hidden");
								
							}else{ //cerramos modal y enviamos dialogo de exito.
								$("#anteriormente_ya_fue_enviado").addClass("hidden");
								
								//cerramos la modal
								$('#externalInvitacion').modal('hide');
								
								 //Se genera correctamente el cambio.
								 $.smallBox({
								 title :"<?php echo ObtenEtiqueta(2404);?>: "+email+" ",  
									 content: "<br/>&nbsp;&nbsp;",
									 color: "#659265",
									 timeout: 30000,
									 icon: "fa fa-envelope-o"
									 //number : "1"
								 });


								 
							}
							
						
						}
					});
					
		}
		
		
		
	}
	

		

 function ViewPost(fl_gallery_post_sp,fl_usuario_post,fg_origen){
	 
	 $('#postId').modal('show');
	 var fame=1;
	 
	 $.ajax({
            type: "POST",
            url : "/fame/site/feed_post_modal.php",
            data: 'fl_gallery_post_sp='+fl_gallery_post_sp+'&fl_usuario_post='+fl_usuario_post+'&fame='+fame+'&fg_origen='+fg_origen,
            async: false,
			success: function (html) {
				
                $('#muestra_modal_post').html(html);
            }

        });
		
		
		
		
	 
 }


	
	
	function ModalSendInvitation(){
		
		$('#externalInvitacion').modal('show');
		
		var datos=1;
		
	    $.ajax({
            type: 'POST',
            url: '/fame/site/feed_send_invitations.php',
            data: 'data='+datos,
            async: true,
            success: function (html) {
                $('#EnviarInvitacion').html(html);
            }
        });



	
	}
	
	
	//Se envia el fl_post comentario y el fg_nivel= si es 1er nive, 2:comentario sobre el post original 3: pertencece al tercer nivel de ciomentarios.
	function MuestraLikesUser(fl_post_comentario,fg_nivel_post,fg_gallery){
		
		if(fg_gallery){
			var fg_gallery=1
		}else{
			var fg_gallery=0;
		}
		
		$.ajax({
            type: 'POST',
            url: '/fame/site/feed_muestralikes.php',
            data: 'fl_post_comentario='+fl_post_comentario+
				  '&fg_nivel_post='+fg_nivel_post+
				  '&fg_gallery='+fg_gallery,
            async: true,
            success: function (html) {
                $('#likes_usuarios').html(html);
            }
        });
		
	}
	
	
	
	
	//Para los likes de los coemtarios de un post. el fg_nivel estable 1=like al comentario de segundo_nivel  ||  2=like a comentarios de 3er nivel
	function LikePostComent(fl_comentario,fg_origen,fl_usuario_pertenece_post,fg_nivel,fame=1){
		
		var fg_like_comentario=1;
        var fg_nivel=fg_nivel;
        var parametros = {
            "item" : fl_comentario,
            "fg_origen" : fg_origen,
			"fg_nivel":fg_nivel,
			"fl_usuario_post_original":fl_usuario_pertenece_post,
            "fame" : fame,
			"fg_like_comentario":fg_like_comentario
        };
        //console.log(parametros);
        $.ajax({
            url: '/fame/site/feed_post_like.php', //URL destino
            data:  parametros,
            type: 'POST',
            success: function (result) {
                var content = JSON.parse(result);
				var fl_post=parseInt(content.post);
				var origen=content.origen;
				var fl_usuario=parseInt(content.fl_usuario);
				var fl_usuario_pertenece_post=parseInt(content.fl_usuario_post_inicial);
				var fl_primer_post_original=content.fl_publicacion_original;
				
				
                //console.log(content.post,content.origen,content.fl_usuario,content.fg_accion);
				//mandmos como parametros fl_comentario/origen del post fame/board / el uusuario
                socket.emit('likesPublicacionesPost',fl_post,origen,fl_usuario,content.fg_accion,fg_nivel,content.fg_likes,fl_usuario_pertenece_post);
            }
        });
		
		
		
		
		
	}
	
	function MarcarCorrecto(fl_gallery_post,fl_comentari){
		

		var parametros = {
            "fl_gallery_post" : fl_gallery_post,
            "fl_comentari" : fl_comentari
        };
        //console.log(parametros);
        $.ajax({
            url: '/fame/site/feed_marcar_resp_like.php', //URL destino
            data:  parametros,
            type: 'POST',
            success: function (result) {
                var content = JSON.parse(result);
				
				if(content.fg_elimnar_respuesta==1){
					
					
					//quitamos el combreado a todos.
					$('.comentarios_'+fl_gallery_post+'').removeClass('marcado_correcto');
					
					
					//quitamos palomita a todos.
					$('.paloma_'+fl_gallery_post+'').addClass('hidden');
					
					//Se marca como correcto el post
					$('#marcado_correcto_'+fl_gallery_post+'').addClass('hidden');
					
					//Marcamos respuesta correcta este solo en modal.
					$('#comenttario_'+fl_comentari+'_m').removeClass('marcado_correcto');
					
					
				}else{
					
					//quitamos el combreado a todos.
					$('.comentarios_'+fl_gallery_post+'').removeClass('marcado_correcto');
					//quitamos palomita a todos.
					$('.paloma_'+fl_gallery_post+'').addClass('hidden');
					
					//Se marca como correcto el post
					$('#marcado_correcto_'+fl_gallery_post+'').removeClass('hidden');
					
					
					//Marcamos respuesta correcta.
					$('#comenttario_'+fl_comentari+'').addClass('marcado_correcto');
					$('#fg_correct_'+fl_comentari+'').removeClass('hidden');
					
					//Marcamos respuesta correcta este solo en modal.
					$('#comenttario_'+fl_comentari+'_m').addClass('marcado_correcto');
					
					
					
				}
				
				
				
                //console.log(content.post,content.origen,content.fl_usuario,content.fg_accion);
				//mandmos como parametros fl_post_padre/fl_post_comentario
                socket.emit('MerkPostCorrect',content.fl_gallery_post,content.fl_comentari,content.fl_user_resp_correcta,content.etq_dialogo,content.name_calificador,content.fg_elimnar_respuesta);
				
            }
        });
		

		//Se marca como leido.
		$.smallBox({
			 title :"<br><?php echo ObtenEtiqueta(2513);?> ",  
			 content: "<br/>&nbsp;&nbsp;",
			 color: "#659265",
			 timeout: 30000,
			 icon: "fa fa-check-square-o"
			 //number : "1"
		});

		
	}
	
	//Esta función es necesaria , para poder cerrar la modal cuando visitas un perfil, cuando abres el modal del post. 
    function CierraModal(){
		
		//cerramos la modal.
		$('#postId').modal('hide');
		
	}
	
	
	function Reply(fl_comentario,fl_usuario_pertenece_comentario,fg_origen,fl_comentario_padre,fl_usuario_pertenece_post_principal,fg_forzar_scroll){
		
		
		
		$("#newCommentComment"+fl_comentario+""+fg_origen+"").removeClass('hidden');
		
		
		
		//para poder visualizar el scroll y solo aplica para el ultimo elemento del listado.
		if(fg_forzar_scroll==1){
		
		var ContendorScoll=document.getElementById('comment_list_'+fl_comentario_padre+'_'+fl_usuario_pertenece_post_principal+'');
		 
		 ContendorScoll.scrollTop = ContendorScoll.scrollHeight;
		 
		}
		
		
	}
	
	function MuestraPostAyuda(fg_tipo_busqueda){
		
		if(fg_tipo_busqueda==1){
			$('#conrespuesta').addClass('mikebold');
			$('#sinrespuesta').removeClass('mikebold');
		}
		if(fg_tipo_busqueda==2){
			$('#sinrespuesta').addClass('mikebold');
			$('#conrespuesta').removeClass('mikebold');
			
		}
		
		
		$.ajax({
            type: 'POST',
            url: '/fame/site/muestrapostayuda.php',
            data: 'fg_tipo_busqueda='+fg_tipo_busqueda,
            async: true,
            success: function (html) {
                $('#view_post_help').html(html);
            }
        });
		
		
		
	}
	
	
	
	function ViewPostCorrect(fl_comentario,fl_usuario_pertenece_comentario,fg_viene_modal){
		
		 //buscamos la respuesta correcta.
		 //pasamos por ajax los valores y recuperamos valores.
         $.ajax({
             type: 'POST',
             url: 'site/feed_busca_resp.php',
             data: 'fl_comentario='+fl_comentario,
             async: true,
          }).done(function (result) {
            var valores = JSON.parse(result);
		    
			var fl_comentario_respuesta_correcta=valores.fl_comentario_respuesta_correcta;
		  

				if(fg_viene_modal==1){
					
							var container = $('#all_coment_'+fl_comentario+'_'+fl_usuario_pertenece_comentario+'_m'),
							scrollTo = $('#comenttario_'+fl_comentario_respuesta_correcta+'_m');container.animate({
							scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
							});
				}else{


					var container = $('#comment_list_'+fl_comentario+'_'+fl_usuario_pertenece_comentario+''),
						scrollTo = $('#comenttario_'+fl_comentario_respuesta_correcta+'');container.animate({
						scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
					});
					
				
				}
		  
		  
         });


		
		
	}
	
	
	$(document).ready(function(){
		
		//funcion que limpia modal y silencia sonido cuando abres video en  flopayer
		$("#postId").on('hidden.bs.modal', function () {
			
              $('#postId').modal('hide');
			  $("#div_flowplayer").html("");
			  $("#video_youtub").html("");
			
		});
		
		<?php 
			if(($fg_tiene_institutos>1)&&($fg_select_instituto<>1)){
               echo " $('#modal_institutos').modal('show'); ";

            }
        ?>	
		
	});
	    MuestraPostAyuda(2);
	
</script>


