
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

# Variable Initialization to avoid errors
$nb_archivo=NULL;

# Receive parameters
//$ds_title = RecibeParametroHTML('ds_title');
$ds_contenido=RecibeParametroHTML('video');
$ds_login = ObtenMatriculaAlumno($fl_usuario);
$fg_ayuda_post=RecibeParametroBinario('ayuda_post');
$video_url=RecibeParametroHTML('video_url');
$fl_programa_sp = RecibeParametroNumerico('fl_programa_sp');

# The default thumbnail width
$thumbnail_width = 900;
if(!empty($_FILES['qqfile']['tmp_name'])) {
    # Validate the uploaded file then move it to new_campus' common tmp
    require("../../modules/common/new_campus/lib/fileuploader.php");
    // list of valid extensions
    $allowedExtensions = array('jpeg', 'jpg', 'png', 'PNG');
    // max file size in bytes
    $sizeLimit = 500 * 1024 * 1024;

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $error = $uploader->handleUpload(PATH_SELF_F."/tmp/", True);
    if(isset($error['error'])){
        echo json_encode((Object)$error);
        exit;
    }

    $nb_archivo = $uploader->getName();

    # Malicious file names, exiting IU
    if(strpos($nb_archivo, '<!') !== false OR strpos($nb_archivo, '<?') !== false OR strpos($nb_archivo, '<script') !== false OR strpos($nb_archivo, '</script') !== false){
        echo json_encode((Object)array('error' => 'Input Error. Filename not accepted.'));
        exit;
    }

    $ruta = PATH_SELF_UPLOADS_F."/posts";
    $ext = strtolower(ObtenExtensionArchivo($nb_archivo));

    # Keep the original name;
    $nb_archivo_ant = $nb_archivo;
    $nb_archivo = $ds_login."_stream_".$fl_tema."_".rand(1, 32000).".$ext";

    rename(PATH_SELF_F."/tmp/".$nb_archivo_ant, $ruta."/".$nb_archivo);

    if($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "PNG"){
        if($ext == "jpg" || $ext == "jpeg"){
            CreaThumb($ruta."/".$nb_archivo, $ruta."/feed_posts/".$nb_archivo, $thumbnail_width);
	    
		    $thumbna_width=150;
		    CreaThumb($ruta."/".$nb_archivo, $ruta."/feed_posts/thumbs/".$nb_archivo, $thumbna_width);
        }
		if($ext == "png" || $ext == "PNG"){
            CreaThumbpng($ruta."/".$nb_archivo, $ruta."/feed_posts/".$nb_archivo, $thumbnail_width);
			$thumbna_width=150;
			CreaThumbpng($ruta."/".$nb_archivo, $ruta."/feed_posts/thumbs/".$nb_archivo, $thumbna_width);
		}
			
    }

    # Upload videos (in the future)

}
else {
    $nb_imagen = '';
}

if(!empty($ds_contenido)) {
    $ds_contenido = rawurldecode($ds_contenido);
    $ds_contenido = PorcesaCadena($ds_contenido);
}

$Query  = "INSERT INTO c_feed_publicaciones ";
$Query .= "(fl_usuario, ds_contenido,nb_img_video, fe_alta, fg_ayuda, video_url ) ";
$Query .= "VALUES ($fl_usuario,'$ds_contenido','$nb_archivo', now(),$fg_ayuda_post, '$video_url')";
$fl_gallery_post = EjecutaInsert($Query);

$QueryR="SELECT fe_alta FROM c_feed_publicaciones WHERE fl_publicacion=$fl_gallery_post";
$valor=RecuperaValor($QueryR);
$fe_post=$valor['fe_alta'];

$avatar =ObtenAvatarUsuario($fl_usuario);
$ds_profesion=FAMEObtenProfesionUsuario($fl_usuario,$fl_perfil_sp);
$compania=FAMEObtenCompaniaUsuario($fl_usuario,$fl_perfil_sp);
$fechaFormato=time_elapsed_string($fe_post);
# Check if the insert or update was successful
if(empty($fl_gallery_post)){
    $error = array('error' => "Server Error. This post cannot be uploaded.");
    echo json_encode((Object)$error);
    exit;
}




##Como es un post directo de feed el origen es p
$origen="p";
echo json_encode((Object)array(
	'post' => $fl_gallery_post,
	'avatar'=>$avatar,
	'profesion'=>$ds_profesion,
	'compania'=>$compania,
	'origen'=>$origen,
	"fe_post"=>$fechaFormato
	
	));



if($fg_ayuda_post==1){

    
    
    $dominio_campus = ObtenConfiguracion(118);
    $src_redireccion=$dominio_campus;#bueno  
    $ruta_avatar_comment=ObtenAvatarUsuario($fl_usuario);
   

    # Nombre del template
    $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=177 AND fg_activo='1'";
    $template = RecuperaValor($Query0);
    $nb_template = str_uso_normal($template[0]);
    # Este email es necesario
    $from = ObtenConfiguracion(107);#de donde sale el email.


    ##Uusuario que esta dado ese like.
	$Query="SELECT ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario ";
    $ro=RecuperaValor($Query);
    $fname_like=str_texto($ro[0]);
    $lname_like=str_texto($ro[1]);



    #Realizamos la consulta a todos los usuarios de su comunidad y enviamos email a todos..
    $rs = StudentQuery("","", "", $fl_usuario);
    for($a = 1; $row = RecuperaRegistro($rs); $a++){
        $fl_alumno = $row[0];
        $ds_nombres=$row['ds_nombres'];$ds_apaterno=$row['ds_apaterno'];

        #Recuiperamos el email 
        $Query="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_alumno ";
        $ro=RecuperaValor($Query);
        $ds_email_destin=$ro['ds_email'];


        #Enviamos el email
		if(VerificaPermisoEnvioEmail($fl_alumno,'fg_ayuda_post_all_comunity')){					
			    
            # Obtenmos el template para decirle que alguien le dio like su post.
            $ds_header = genera_documento_sp('', 1, 177);
            $ds_body = genera_documento_sp('', 2, 177);
            $ds_footer = genera_documento_sp('', 3, 177);
            $ds_mensaje=$ds_header.$ds_body.$ds_footer;


            $ds_mensaje = str_replace("#fame_fname#", $ds_nombres, $ds_mensaje);
            $ds_mensaje = str_replace("#fame_lname#", $ds_apaterno, $ds_mensaje);

			$ds_mensaje = str_replace("#fame_fname_friends#", $fname_like, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_lname_friends#", $lname_like, $ds_mensaje);
				  
			$ds_mensaje = str_replace("#fame_ds_avatar_ori#", $dominio_campus.$ruta_avatar_comment, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_link#", $src_redireccion, $ds_mensaje);
		 
            $ds_mensaje = str_replace("#ds_post#", $ds_contenido, $ds_mensaje);

			# Enviamos el correo al usuario dependiendo de la accion
			EnviaMailHTML($from, $from, $ds_email_destin, $nb_template, $ds_mensaje);
				

        }
    }

    $rs1 = TeacherQuery("", "", $fl_usuario);
    for($b = 1; $row = RecuperaRegistro($rs1); $b++){
        $fl_maestro = $row[0];
        $ds_nombres=$row['ds_nombres'];

        #Recuiperamos el email 
        $Query="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_maestro ";
        $ro=RecuperaValor($Query);
        $ds_email_destin=$ro['ds_email'];

        #Enviamos el email
		if(VerificaPermisoEnvioEmail($fl_maestro,'fg_ayuda_post_all_comunity')){					
            
            # Obtenmos el template para decirle que alguien le dio like su post.
            $ds_header = genera_documento_sp('', 1, 177);
            $ds_body = genera_documento_sp('', 2, 177);
            $ds_footer = genera_documento_sp('', 3, 177);
            $ds_mensaje=$ds_header.$ds_body.$ds_footer;



            $ds_mensaje = str_replace("#fame_fname#", $ds_nombres, $ds_mensaje);
            $ds_mensaje = str_replace("#fame_lname#", "", $ds_mensaje);

			$ds_mensaje = str_replace("#fame_fname_friends#", $fname_like, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_lname_friends#", $lname_like, $ds_mensaje);
            
			$ds_mensaje = str_replace("#fame_ds_avatar_ori#", $dominio_campus.$ruta_avatar_comment, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_link#", $src_redireccion, $ds_mensaje);
            
            $ds_mensaje = str_replace("#ds_post#", $ds_contenido, $ds_mensaje);

			# Enviamos el correo al usuario dependiendo de la accion
			EnviaMailHTML($from, $from, $ds_email_destin, $nb_template, $ds_mensaje);
            

        }




    }

    $rs2 = AdminQuery($fl_usuario);
    for($c = 1; $row = RecuperaRegistro($rs2); $c++){
        $fl_admin = $row[0];  
        $ds_nombres=$row['ds_nombres'];


        
        #Recuiperamos el email 
        $Query="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_admin ";
        $ro=RecuperaValor($Query);
        $ds_email_destin=$ro['ds_email'];

        #Enviamos el email
		if(VerificaPermisoEnvioEmail($fl_admin,'fg_ayuda_post_all_comunity')){					
            
            # Obtenmos el template para decirle que alguien le dio like su post.
            $ds_header = genera_documento_sp('', 1, 177);
            $ds_body = genera_documento_sp('', 2, 177);
            $ds_footer = genera_documento_sp('', 3, 177);
            $ds_mensaje=$ds_header.$ds_body.$ds_footer;



            $ds_mensaje = str_replace("#fame_fname#", $ds_nombres, $ds_mensaje);
            $ds_mensaje = str_replace("#fame_lname#", "", $ds_mensaje);

			$ds_mensaje = str_replace("#fame_fname_friends#", $fname_like, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_lname_friends#", $lname_like, $ds_mensaje);
            
			$ds_mensaje = str_replace("#fame_ds_avatar_ori#", $dominio_campus.$ruta_avatar_comment, $ds_mensaje);
			$ds_mensaje = str_replace("#fame_link#", $src_redireccion, $ds_mensaje);
            
            $ds_mensaje = str_replace("#ds_post#", $ds_contenido, $ds_mensaje);

			# Enviamos el correo al usuario dependiendo de la accion
			EnviaMailHTML($from, $from, $ds_email_destin, $nb_template, $ds_mensaje);
            

        }





    }


}



?>







