<?php
  # Libreria de funciones
  require("../lib/self_general.php");
  
    
    
  $fl_usuario=RecibeParametroNumerico('fl_usuario',True);  
  $fl_usuario_actual=RecibeParametroNumerico('fl_usuario_origen',True);
  $fl_usuario_logueado=ValidaSesion(False,0, True);
  if(empty($fl_usuario)){
	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  }
  # Obtenemos el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  # Obtenemos el perfil del usuario
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
	
	# Receive Parameters
  $fl_programa_sp = RecibeParametroNumerico('tema', True);
  $my_posts = RecibeParametroNumerico('my_posts', True);
  $index = RecibeParametroNumerico('index', True);
	
  $fl_programa_sp=0;
  $my_posts="on";
 
  
  $index = intval($index);
  $index_end = 20;

  $diferencia = RecuperaDiferenciaGMT( );
  $Queryp  = "SELECT fl_gallery_post_sp, fl_entregable_sp, fl_usuario, ds_title, ds_name, nb_archivo, fe_post1, ds_post, fg_genero, fl_grado, ";
  $Queryp .= "cl_clasificacion_grado, fl_instituto, fl_pais, nb_programa, fl_perfil_sp, fl_grado, edad, fame, fe_post, fl_programa_sp FROM ( ";
    $Queryp .= "(SELECT  a.fl_gallery_post_sp, a.fl_entregable_sp, a.fl_usuario, a.ds_title, CONCAT(c.ds_nombres, ' ', c.ds_apaterno) as ds_name, ";
    $Queryp .= "a.nb_archivo, DATE_FORMAT(DATE_ADD(fe_post, INTERVAL $diferencia HOUR), '%M %e, %Y') fe_post1, ds_post, c.fg_genero, ";
    $Queryp .= "CASE c.fl_perfil_sp WHEN ".PFL_ESTUDIANTE_SELF." THEN al.fl_grado ELSE 0 END fl_grado, ";
    $Queryp .= "CASE c.fl_perfil_sp WHEN ".PFL_ESTUDIANTE_SELF." THEN (SELECT gf.cl_clasificacion_grado FROM k_grado_fame gf WHERE gf.fl_grado=al.fl_grado) ";
    $Queryp .= "ELSE 0 END cl_clasificacion_grado, c.fl_instituto, ''fl_pais, p.nb_programa, c.fl_perfil_sp, fe_post, ";
    $Queryp .= "TIMESTAMPDIFF(YEAR,c.fe_nacimiento,CURDATE()) AS edad, '1' fame, p.fl_programa_sp, fg_educational ";
    $Queryp .= "FROM k_gallery_post_sp a  ";
    //$Queryp .= "LEFT JOIN k_gallery_comment_sp kgcsp ON(kgcsp.fl_gallery_post_sp=a.fl_gallery_post_sp) ";
    $Queryp .= " JOIN c_usuario c ON (a.fl_usuario=c.fl_usuario) AND a.fl_usuario=".$fl_usuario."  ";
    $Queryp .= " JOIN k_programa_detalle_sp d ON (d.fl_programa_sp =a.fl_programa_sp OR a.fl_programa_sp=0) ";
    $Queryp .= " JOIN c_programa_sp p ON(p.fl_programa_sp=d.fl_programa_sp) ";
    //$Queryp .= "LEFT JOIN c_administrador_sp ad ON( ad.fl_adm_sp=c.fl_usuario) ";
    //$Queryp .= "LEFT JOIN c_maestro_sp ma ON( ma.fl_maestro_sp=c.fl_usuario) ";
    $Queryp .= "LEFT JOIN c_alumno_sp al ON( al.fl_alumno_sp=c.fl_usuario) ";
    $Queryp .= "LEFT JOIN k_usu_direccion_sp usd ON(usd.fl_usuario_sp=c.fl_usuario) ";
    $Queryp .= "JOIN k_instituto_filtro f ON ( f.fl_instituto=c.fl_instituto ) ";
    $Queryp .= "WHERE d.fg_board='1' ";    
    $Queryp .= "GROUP BY a.fl_gallery_post_sp ";
    $Queryp .= "ORDER BY fe_post DESC ) ";     
    $Queryp .= ") as MainPost WHERE 1=1 "; 
    $Queryp .= "AND fl_usuario=".$fl_usuario." ";
    $Queryp .= " ORDER BY fe_post DESC LIMIT $index_end OFFSET $index ";  
	$rs = EjecutaQuery($Queryp);
	$result = array();
	for($i=0; $row=RecuperaRegistro($rs); $i++){
		$fl_gallery_post_sp = $row[0];
		$fl_entregable_sp = $row[1];
		$fl_post_usuario = $row[2];
    $fl_instituto = ObtenInstituto($fl_post_usuario);
    $nb_instituto = ObtenNameinstituto($fl_instituto);    
		$ds_title = str_uso_normal($row[3]);
		// $ds_nombres = $row[4];
    $ds_nombres = ObtenNombreUsuario($fl_post_usuario, $fl_usuario_logueado);
		$nb_archivo = $row[5];
		$fe_post = $row[6];
		$ds_post = str_uso_normal($row[7]);
    $nb_programa = str_uso_normal($row[13]);
    $fl_grade_user = $row[15];
    $edad = $row[16];
    $fame = $row[17];

		# Initialize default post settings
		$type = "";
		$fg_my_post = false;
		$fg_tipo = "";
		$no_semana = "";
		$no_grado = "";
		$ds_pais = "";
    
    if($fame==1){

		# Find country of the author
		$Query  = "SELECT  b.nb_pais FROM k_usu_direccion_sp a ";
    $Query .= "LEFT JOIN c_pais b ON(a.fl_pais=b.fl_pais) ";
    $Query .= "WHERE fl_usuario_sp=$fl_post_usuario ";
		$row2 = RecuperaValor($Query);
		$ds_pais = $row2[0];
    #En caso de que no tenga pais el defaul es el del instituto
    if(empty($ds_pais)){
      $rowe = RecuperaValor("SELECT b.nb_pais FROM c_instituto a, c_pais b WHERE a.fl_pais = b.fl_pais AND a.fl_instituto=".$fl_instituto);
      $ds_pais = $rowe[0];
    }
		# Find number of comments for this post
		$Query = "SELECT COUNT(1) FROM k_gallery_comment_sp WHERE fl_gallery_post_sp=$fl_gallery_post_sp";
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

      $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
      if($ext == 'jpg' || $ext=='jpeg' || $ext=='png' || $ext=='PNG'){
        # A student uploaded image
        $nb_file = "<img src='".PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_post_usuario."/sketches/board_thumbs/$nb_archivo'>";
      } 
      else {
        # A student uploaded video
        $nb_file = "<img src='".PATH_SELF_UPLOADS."/gallery/thumbs/vanas-board-video-default.jpg'>";
      }

      
		}
    else {
			$type = "Board";
			$nb_file = "<img src='".PATH_SELF_UPLOADS."/gallery/thumbs/$nb_archivo'>";

			# If this post belongs to the board and is posted by this user, allow delete
			if($fl_post_usuario == $fl_usuario){
				$fg_my_post = true;
			}
		}
		
		if(empty($ds_title)){
			$ds_title = "";
		}
    $aviso = "";
    }
    else{
      # Find country of the author
      $Query0  = "SELECT c.ds_pais FROM c_usuario a ";
      $Query0 .= "LEFT JOIN k_ses_app_frm_1 b ON(a.cl_sesion = b.cl_sesion) ";
      $Query0 .= "LEFT JOIN c_pais c ON(c.fl_pais=b.ds_add_country ) ";
      $Query0 .= "WHERE fl_usuario=".$fl_post_usuario;
      $row0 = RecuperaValor($Query0);
      $ds_pais = $row0[0];

      # Find number of comments for this post
      $Query1 = "SELECT COUNT(1) FROM k_gallery_comment WHERE fl_gallery_post=$fl_gallery_post_sp";
      $row1 = RecuperaValor($Query1);
      $no_comments = $row1[0];
      if(empty($no_comments))
        $no_comments = 0;
      
      # Get last comment
      $Query2 = "SELECT MAX(fl_gallery_comment) fl_gallery_comment_sp_ultimo FROM k_gallery_comment WHERE fl_gallery_post=$fl_gallery_post_sp ORDER BY fe_comment DESC";
      $row2 = RecuperaValor($Query2);
      $fl_gallery_comment_sp_ultimo = $row2[0];
      if(empty($fl_gallery_comment_sp_ultimo))
        $fl_gallery_comment_sp_ultimo = 0;
      
      # Check if this is an upload from desktop or straight from the board
      if(!empty($fl_entregable_sp)){
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

        switch($fg_tipo) {
          case "A":		$fg_tipo = "Assignment";  break;
          case "AR":	$fg_tipo = "Assignment Reference"; break;
          case "S":   $fg_tipo = "Sketch";  break;
          case "SR":	$fg_tipo = "Sketch Reference"; break;
        }

        $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
        if($ext == 'jpg'){
          # A student uploaded image
          $nb_file = "<img src='".PATH_ALU."/sketches/board_thumbs/$nb_archivo'>";
        } else {
          # A student uploaded video
          $nb_file = "<img src='".PATH_N_COM_UPLOAD."/gallery/thumbs/vanas-board-video-default.jpg'>";
        }     
      }
      else {
        $type = "Board";
        $nb_file = "<img src='".PATH_N_COM_UPLOAD."/gallery/thumbs/$nb_archivo'>";

        # If this post belongs to the board and is posted by this user, allow delete
        if($fl_post_usuario == $fl_usuario){
          $fg_my_post = true;
        }
      }
      
      $nb_instituto = ObtenEtiqueta(2010);
      # Verificamos quienes son los usuarios que puden ver post de vanas mayores o igual a post-secundary      
      if($edad>=18)
        $aviso = "<i class='fa fa-eye'></i> ".ObtenEtiqueta(2007);     
      else
        $aviso = "";
    }

    
    $result["item".$i] = array(
      "type" => $type,
      "fg_tipo" => $fg_tipo,
      "no_semana" => $no_semana,
      "no_grado" => $no_grado,
      "ds_pais" => $ds_pais,
      "fg_my_post" => $fg_my_post,
      "fl_gallery_post" => $fl_gallery_post_sp,
      "ds_title" => $ds_title,
      "nb_instituto" => $nb_instituto,
      "nb_archivo" => $nb_file,
      "nb_usuario" => $ds_nombres,
      "fe_post" => $fe_post,
      "no_comments" => $no_comments,
      "fl_gallery_comment_sp_ultimo" => $fl_gallery_comment_sp_ultimo,
      "fl_entregable_sp" => $fl_entregable_sp,
      "fl_grade_user" => $fl_grade_user,
      "moreage" => $aviso,
      "fame" => $fame,
	  "fl_user_actual"=>$fl_usuario,
	  "fl_user_origen"=>$fl_usuario_actual,
      "nb_programa" => $nb_programa
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