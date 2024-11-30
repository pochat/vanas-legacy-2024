<?php
  # gallery_post_modal is the modal part of gallery.php
  # is called when a user clicks on an item in the activity board

  # Libreria de funciones	
	require("../lib/self_general.php");
  
	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $fl_gallery_post_sp = RecibeParametroNumerico('item', True);
  $fame = RecibeParametroNumerico('fame', True);

  $diferencia = RecuperaDiferenciaGMT( );
  if($fame==1){
    $Query = "SELECT a.fl_programa_sp, '' , a.fl_usuario, c.fl_perfil_sp, fl_entregable_sp, ds_title, ds_post, ";
    $Query .= "DATE_FORMAT(DATE_ADD(fe_post, INTERVAL $diferencia HOUR), '%M %e, %Y') fe_post, nb_archivo  ";
    $Query .= "FROM k_gallery_post_sp a  ";
    $Query .= "LEFT JOIN c_usuario c ON c.fl_usuario=a.fl_usuario  ";
    $Query .= "WHERE fl_gallery_post_sp= $fl_gallery_post_sp ";
  }
  else{
    $Query = "SELECT c.fl_programa, '', a.fl_usuario, b.fl_perfil fl_perfil_sp, a.fl_entregable, a.ds_title, a.ds_post, ";
    $Query .= "DATE_FORMAT(DATE_ADD(fe_post, INTERVAL 0 HOUR), '%M %e, %Y') fe_post, nb_archivo ";
    $Query .= "FROM k_gallery_post a ";
    $Query .= "LEFT JOIN c_usuario b ON(b.fl_usuario=a.fl_usuario) ";
    $Query .= "LEFT JOIN k_ses_app_frm_1 c ON(c.cl_sesion=b.cl_sesion) ";
    $Query .= "WHERE fl_gallery_post= $fl_gallery_post_sp ";
  }
  $row = RecuperaValor($Query);
  $fl_programa_sp = $row[0];
  $nb_tema = $row[1];
  $fl_post_usuario = $row[2];
  $fl_instituto = ObtenInstituto($fl_post_usuario);
  $fl_post_perfil = $row[3];
  $fl_entregable_sp = $row[4];
  $ds_title = str_uso_normal($row[5]);
  $ds_post = str_uso_normal($row[6]);
  $fe_post = $row[7];
  $nb_file = $row[8];
  
  #Recuperamso el nombre del Instituto
  $Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $nb_instituto=str_uso_normal($row['ds_instituto']);

  # Initialize default modal settings
	$type = "Board";
	$fg_tipo = "";
	$no_semana = "";
  $no_grado = "";
  $ds_pais = "";

  # Find country of the author
  if($fame==1){
    $Query = "SELECT ds_pais FROM k_usu_direccion_sp a ";
    $Query .= "LEFT JOIN c_pais b ON(b.fl_pais=a.fl_pais) ";
    $Query .= "WHERE fl_usuario_sp=$fl_post_usuario ";
    
    #Recupermaos el  nombre del programa.
    $Quepro="SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
    $rowp=RecuperaValor($Quepro);
    $nb_programa=str_uso_normal($rowp['nb_programa']);
    
    
  }
  else{
    $fl_perfil_post_user = ObtenPerfil($fl_post_usuario);
    
    #Recupermaos el  nombre del programa.
    $Quepro="SELECT nb_programa FROM c_programa WHERE fl_programa=$fl_programa_sp ";
    $rowp=RecuperaValor($Quepro);
    $nb_programa=str_uso_normal($rowp['nb_programa']);
    
    if($fl_perfil_post_user == PFL_ESTUDIANTE){
      $Query  = "SELECT c.nb_pais ";
      $Query .= "FROM c_usuario a, k_ses_app_frm_1 b, c_pais c ";
      $Query .= "WHERE a.cl_sesion = b.cl_sesion AND b.ds_add_country= c.fl_pais AND fl_usuario= $fl_post_usuario ";
    }
    else{
      $Query = "SELECT b.nb_pais FROM c_maestro a, c_pais b  WHERE a.fl_pais = b.fl_pais AND fl_maestro=$fl_post_usuario ";
    }
  }
  $row = RecuperaValor($Query);
  $ds_pais = $row[0];
  
  # Get exetion of file
  $ext = strtolower(ObtenExtensionArchivo($nb_file));  
  
  if($fame==1){
    #En caso de que no tenga pais el defaul es el del instituto
    if(empty($ds_pais)){
      $rowe = RecuperaValor("SELECT b.nb_pais FROM c_instituto a, c_pais b WHERE a.fl_pais = b.fl_pais AND a.fl_instituto=".$fl_instituto);
      $ds_pais = $rowe[0];
    }
    if(empty($fl_entregable_sp)){
      // $nb_file = "<img class='img-responsive img-center' src='".PATH_SELF_UPLOADS."/gallery/$nb_file'>";
      $ruta_orginal = PATH_SELF_UPLOADS."/gallery";
    } 
    else {
      $type = "Desktop";

      # Retrieve desktop post info
      $Query = "SELECT a.fg_tipo, d.no_semana  ";
      $Query .= "FROM k_entregable_sp a  ";
      $Query .= "LEFT JOIN k_entrega_semanal_sp b ON b.fl_entrega_semanal_sp=a.fl_entrega_semanal_sp ";
      $Query .= "LEFT JOIN c_leccion_sp d ON d.fl_leccion_sp=b.fl_leccion_sp ";
      $Query .= "WHERE a.fl_entregable_sp=$fl_entregable_sp ";
      $row2 = RecuperaValor($Query);
      $fg_tipo = $row2[0];
      $no_semana = $row2[1]; 
      
      # Rutas 
      $ruta_orginal = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_post_usuario."/sketches/original";
    }
  }
  else{
    if(empty($fl_entregable_sp)){
      $nb_file = "<img class='img-responsive img-center' src='".PATH_N_COM_UPLOAD."/gallery/$nb_file'>";
    }
    else{
      $type = "Desktop";

      # Retrieve desktop post info
      $Query  = "SELECT a.fg_tipo, d.no_semana ";
      $Query .= "FROM k_entregable a ";
      $Query .= "LEFT JOIN k_entrega_semanal b ON(b.fl_entrega_semanal=a.fl_entrega_semanal) ";
      $Query .= "LEFT JOIN k_semana c ON (c.fl_semana=b.fl_semana) ";
      $Query .= "LEFT JOIN c_leccion d ON(d.fl_leccion=c.fl_leccion) ";
      $Query .= "where a.fl_entregable=$fl_entregable_sp";
      $row2 = RecuperaValor($Query);
      $fg_tipo = $row2[0];
      $no_semana = $row2[1];

      # Rutas 
      $ruta_orginal = PATH_ALU."/sketches/original";
    }
  }
  
  # Type file
  switch($fg_tipo) {
    case "A":		$fg_tipo = "Assignment"; $nb_tab = "assignment"; break;
    case "AR":	$fg_tipo = "Assignment Reference"; $nb_tab = "assignment_ref"; break;
    case "S":   $fg_tipo = "Sketch"; $nb_tab = "sketch"; break;
    case "SR":	$fg_tipo = "Sketch Reference"; $nb_tab = "sketch_ref"; break;
  }
      
  
  if($ext == 'jpg' || $ext == "jpeg" || $ext=='png' || $ext=='PNG'){
    $nb_file = "<img class='img-responsive img-center' src='".$ruta_orginal."/$nb_file'>";
  } 
  else {
    # present video here
    if($fame==1){
      $ruta = "/var/www/html/vanas/dev/fame/site/uploads/".$fl_instituto."/".CARPETA_USER.$fl_post_usuario."/videos/";    
      $nb_file = "
      <div id='player_std' class='flowplayer fp-edgy' ></div>
      <script>
      flowplayer.conf = {
        splash: true
      };
      var container = document.getElementById('player_std');
      var key_flowplayer = '".ObtenConfiguracion(110)."';
      // install flowplayer into selected container
      flowplayer(container, {
        key: key_flowplayer,      
        ratio: 		9/16,
        clip: {
          sources: [
            { type: 'video/mp4',
              src:  '".ObtenConfiguracion(116)."/fame/site/uploads/$fl_instituto/".CARPETA_USER."$fl_post_usuario/videos/".array_shift(explode('.',$nb_file)).".mp4' },
            { type: 'application/x-mpegURL',
              src:  '".ObtenConfiguracion(116)."/fame/site/uploads/$fl_instituto/".CARPETA_USER."$fl_post_usuario/videos/".$nb_file."' }
          ],          
          scaling: 'fit',
          // configure clip to use hddn as our provider, referring to the rtmp plugin
          provider: 'hddn'          
        },
         rtmp: 'rtmp://s3b78u0kbtx79q.cloudfront.net/cfx/st',

        // loop playlist
        loop: false,
        splash: true,
        keyboard:true,
        embed:false,
        share:false        
       })
      </script>";
    }
    else{
      $ruta = PATH_ALU."/videos/";
      $nb_file = PresentVideoHTML5($ruta, $nb_file, 865, 405, 'assign_video');
    }
  }
  
  # Replace embedded videos with responsive sizing
  # Refer to common/lib/cam_forum.inc.php, SeparaFrames() for the <p> and <iframe > tag
  if(strpos($ds_post, "iframe") !== false){
    $ds_post = str_replace("<p>", "<div class='embed-container'>", $ds_post);
    $ds_post = str_replace("</p>", "</div>", $ds_post);
    $nb_file = '';
  }

  $nb_usuario = ObtenNombreUsuario($fl_post_usuario, $fl_usuario);

  $result = array(
  	"type" => $type,
		"fg_tipo" => $fg_tipo,
    "nb_tab" => $nb_tab,
		"no_semana" => $no_semana,
    // "no_grado" => $no_grado,
    "ds_pais" => $ds_pais,
  	"fl_gallery_post" => $fl_gallery_post_sp,
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
?>

<!-- Modal Content -->
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="item-title" style="font-weight:500;"></h4>
</div>
<div class="modal-body text-align-center" id="item-body">
<input type="hidden" id="fl_usuario_actual" name="fl_usuario_actual" value="<?php echo $fl_usuario; ?>">
<input type="hidden" id="fame" name="fame" value="<?php echo $fame; ?>">
</div>
<div class="modal-footer" id="item-footer"></div>

<script type="text/javascript">
  var result, image, description, detail, name, type, title, body, footer;
	result = <?php echo json_encode((Object) $result); ?>;

  title = $("#item-title");
  body = $("#item-body");
  footer = $("#item-footer");

	title.append(result.ds_title);
  
	image = 
    "<div class='row'>"+
      "<div class='col-xs-12'>"+
        result.nb_archivo+
      "</div>"+
    "</div>";
	body.append($(image));

	description = 
    "<div class='row'>"+
      "<div class='col-xs-12'>"+
        "<div class='h4' style='white-space:pre-line;'>"+result.ds_post+"</div>"+
      "</div>"+
    "</div>";
	body.append(description);

	// Determine type of post
	if(result.type === 'Desktop'){
    name = "<span class='h5 text-primary'><a href='#site/desktop.php?student="+result.fl_post_usuario+"&week="+result.no_semana+"&tab="+result.nb_tab+"'><b>"+result.nb_usuario+"</b></a></span><br>";
		// type = "<span class='text-primary'>"+result.type+"<br> Week "+result.no_semana+" - "+result.fg_tipo+" - Term "+result.no_grado+"</span><br>";
		type = "<span class='text-primary'>"+result.type+", Session "+result.no_semana+", "+result.nb_programa+" </span><br>";
	} else {
    name = "<span class='h5 text-primary'><b>"+result.nb_usuario+"</b></span><br>";
		type = "<span class='text-primary'>"+result.type+", "+result.nb_programa+" </span><br>";
	}

	detail = 
		name+
    "<span class='text-primary'>"+result.nb_instituto+", "+result.ds_pais+"</span><br>"+
		// "<span class='h4'>"+result.nb_tema+"</span><br>"+
		type+
		"<span class='text-muted'>"+result.fe_post+"</span>";

	footer.append(detail);

  // When leaving the page with the modal view on, take out the grayed out backdrop
  $("a[href*='profile_view.php'], a[href*='desktop.php']").on("click", function(){
    // Don't need to redirect on desktop.php again
    if(/desktop.php/i.test(window.location.hash)){ return false; }
    var htmlContainer = $("html");
    // Enable window scroll
    htmlContainer.css('overflow-y', 'auto');
    $("div.modal-backdrop").remove();
    $(window).off('scroll.infinite');
    boardController.emptyContainer(container);    
  });
  $(document).ready(function(){
    // ocultamos la descarga
    $("#assign_video").attr("controlsList", "nodownload").removeAttr("width").removeAttr("height");
  });
</script>
