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
  $fl_post = RecibeParametroNumerico('fl_post_comentario');
  $fg_nivel_post=RecibeParametroNumerico('fg_nivel_post');
  $fg_gallery=RecibeParametroNumerico('fg_gallery');
  
  

  if($fg_nivel_post==1){	  
	  $Query="SELECT fl_usuario FROM c_feed_likes WHERE fl_gallery_post_feed=$fl_post  ";  
  }
 if($fg_nivel_post==2){	
	  $Query="SELECT fl_usuario FROM k_feed_likes WHERE fl_gallery_comment_sp=$fl_post  ";	  
  }
  if($fg_nivel_post==3){	
	  $Query="SELECT fl_usuario FROM k_feed_likes WHERE fl_gallery_comment_sp_comment=$fl_post  ";	  
  }
  $rs = EjecutaQuery($Query);
  $total=CuentaRegistros($rs);
  
?>

	<div class="modal-header ModalHeaderFeed" >
        <h7 class="modal-title" id="exampleModalLabel"><?php echo ObtenEtiqueta(2577)." ".$total;?> <i style="margin:3px;" class="fa fa-heart likes mikelike" aria-hidden="true"></i></h7>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-7px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="likes_usuarios" style="max-height:450; overflow-y: auto">


		<div class="row">
			
			<div class="col-sm-12 col-xs-12 col-md-12">
<?php   
  
  for($x=0; $row=RecuperaRegistro($rs); $x++){
	$fl_usuario_like=$row['fl_usuario'];
	$ruta_avatar=ObtenAvatarUsuario($fl_usuario_like);
	$nombre= ObtenNombreUsuario($fl_usuario_like);
    $fl_perfil_like=ObtenPerfilUsuario($fl_usuario_like);
	$profesion= FAMEObtenProfesionUsuario($fl_usuario_like,PFL_ESTUDIANTE_SELF);
	$fg_btn_seguir=1;
	echo "<div class='col-sm-2 col-xs-12 col-md-4'>
			<ul class='media-list'>";
	
	
    MuestraPerfilFeed($fl_usuario_like,$fl_perfil_like,$fl_usuario,'','ModalLikesUser',$fg_btn_seguir,1);					
			 							   
										
	echo"	</ul>	
		</div>";



  }	  
?>			</div>
        
		</div>
		<br><br>


	</div>
    
	<div class="modal-footer hidden">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary">Save changes</button>
	</div>

