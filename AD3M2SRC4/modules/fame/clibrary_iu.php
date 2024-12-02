<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Variable initialization to avoid errors
  $nb_programa_err=NULL;
  $ds_tipo_err=NULL;
  $no_orden_err=NULL;
  $no_horas_err=NULL;
  $no_semanas_err=NULL;
  $ds_credential_err=NULL;
  $no_creditos_err=NULL;
  $nb_thumb_err=NULL;
  $ds_course_code_err=NULL;
  $tab_prog_err=NULL;
  $tab_outline_err=NULL;
  $ds_programa_err=NULL;
  $ds_learning_err=NULL;
  $ds_metodo_err=NULL;
  $ds_requerimiento_err=NULL;
  $tab_cats_err=NULL;
  $nb_tags2_err=NULL;
  $nb_sof2_err=NULL;
  $nb_har2_err=NULL;
  $nb_fos2_err=NULL;
  $reg_programa_err=NULL;
  $tr_titulo=NULL;
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico("clave");
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CLIB_SP, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
  $ds_programa = RecibeParametroHTML("ds_programa");
  $ds_programa_esp = RecibeParametroHTML("ds_programa_esp");
  $ds_programa_fra = RecibeParametroHTML("ds_programa_fra");
  $fg_nuevo_programa = RecibeParametroBinario("fg_nuevo_programa");
  $nb_programa = RecibeParametroHTML("nb_programa");
  $nb_programa_esp = RecibeParametroHTML("nb_programa_esp");
  $nb_programa_fra = RecibeParametroHTML("nb_programa_fra");
  $no_creditos = RecibeParametroFlotante("no_creditos");
  $no_orden = RecibeParametroNumerico("no_orden");
  $fl_programa = RecibeParametroNumerico("fl_programa");
  $nb_thumb_load = RecibeParametroHTML("nb_thumb_load");
  $nb_thumb = ($_FILES["thumb"]["name"]);
  $no_horas = RecibeParametroFlotante("no_horas");
  $no_semanas = RecibeParametroNumerico("no_semanas");
  $ds_credential = RecibeParametroHTML("ds_credential");
  $cl_delivery = RecibeParametroHTML("cl_delivery");
  $ds_language = RecibeParametroHTML("ds_language");
  $cl_type = RecibeParametroNumerico("cl_type");
  $workload = RecibeParametroHTML("workload");
  $fg_fulltime = RecibeParametroBinario("fg_fulltime");
  $fg_taxes = RecibeParametroBinario("fg_taxes");
  $fg_board = RecibeParametroBinario("fg_board");
  # Recibimos datos extras del curso
  $ds_learning = RecibeParametroHTML("ds_learning");
  $ds_learning_esp = RecibeParametroHTML("ds_learning_esp");
  $ds_learning_fra = RecibeParametroHTML("ds_learning_fra");
  $ds_metodo = RecibeParametroHTML("ds_metodo");
  $ds_metodo_esp = RecibeParametroHTML("ds_metodo_esp");
  $ds_metodo_fra = RecibeParametroHTML("ds_metodo_fra");
  $ds_requerimiento = RecibeParametroHTML("ds_requerimiento");
  $ds_requerimiento_esp = RecibeParametroHTML("ds_requerimiento_esp");
  $ds_requerimiento_fra = RecibeParametroHTML("ds_requerimiento_fra");
  $ds_course_code = RecibeParametroHTML("ds_course_code");
  $nb_lvl = RecibeParametroHTML("nb_lvl");
  $fg_obligatorio = RecibeParametroBinario("fg_obligatorio");
  # Recibimos categorias
	$nb_tags = RecibeParametroHTML("nb_tags");
  $nb_tags = ( explode( ",", $nb_tags ) );  
	$nb_sof = RecibeParametroHTML("nb_sof");
  $nb_sof = ( explode( ",", $nb_sof ) );  
	$nb_har = RecibeParametroHTML("nb_har");
  $nb_har = ( explode( ",", $nb_har ) );	
	$nb_cce = RecibeParametroHTML("nb_cce");
  $nb_cce = ( explode( ",", $nb_cce ) ); 
	$nb_css = RecibeParametroHTML("nb_css");
  $nb_css = ( explode( ",", $nb_css ) );   
	$nb_fos = RecibeParametroHTML("nb_fos");
  $nb_fos = ( explode( ",", $nb_fos ) ); 
  # Clonamos para regresar en validacion
	$nb_tags2 = RecibeParametroHTML("nb_tags");
	$nb_sof2 = RecibeParametroHTML("nb_sof");
	$nb_har2 = RecibeParametroHTML("nb_har");
	$nb_cce2 = RecibeParametroHTML("nb_cce");
	$nb_css2 = RecibeParametroHTML("nb_css");
	$nb_fos2 = RecibeParametroHTML("nb_fos");
  # Recibimos los datos para la page fixed
  $nb_pagina = RecibeParametroHTML("nb_pagina");
  $ds_pagina = RecibeParametroHTML("ds_pagina");
  $ds_titulo = RecibeParametroHTML("ds_titulo");
  $ds_contenido = RecibeParametroHTML("ds_contenido");
  $tr_contenido = RecibeParametroHTML("tr_contenido");
  $archivo = RecibeParametroHTML("archivo");
  $no_videos = RecibeParametroNumerico("no_videos");
  $fg_publicar=RecibeParametroBinario("fg_publicar");
  $mn_precio=RecibeParametroFlotante("mn_precio");
  $no_email=RecibeParametroNumerico("no_email");
  $ds_contenido_curso=RecibeParametroHTML("ds_contenido_curso");
  $no_dias_trial=RecibeParametroNumerico("no_dias_trial");
  $no_dias_pago=RecibeParametroNumerico("no_dias_pago");
  
  switch ($cl_delivery){
    case 'O': $ds_tipo = "Online"; break;
    case 'S': $ds_tipo = "On-Site"; break;
    case 'C': $ds_tipo = "Combined"; break;
    case 'OB': $ds_tipo = "Online &#47; Blended"; break;
  }
  
  # Obtenemos los grados que se seleccionaron
  $programas = $_REQUEST['fl_programa_nuevo']??NULL;
  $reg_programa = "";
  if(!empty($programas)){   
    foreach ($programas as $programa){
      $programa = $programa.",";
      $reg_programa = $reg_programa.$programa;
    }
  }
  
  # Obtenemos los grados que se seleccionaron
  $programas_pre = isset($_REQUEST['fl_programa_pre'])?$_REQUEST['fl_programa_pre']:NULL; 
  $reg_programa_pre = ""; 
  if(!empty($programas_pre)){   
    foreach ($programas_pre as $programa_pre){
      $programa_pre = $programa_pre.",";
      $reg_programa_pre = $reg_programa_pre.$programa_pre;
    }
  }  
  
  # Obtenemos los grados que se seleccionaron
  $programas_sig = isset($_REQUEST['fl_programa_sig'])?$_REQUEST['fl_programa_sig']:NULL; 
  $reg_programa_sig = "";   
  if(!empty($programas_sig)){   
    foreach ($programas_sig as $programa_sig){
      $programa_sig = $programa_sig.",";
      $reg_programa_sig = $reg_programa_sig.$programa_sig;
    }
  }
  
 
  
  # Obtenemos los courses code que se seleccionaron
  $courses_codes = isset($_REQUEST['fl_course_code'])?$_REQUEST['fl_course_code']:NULL; 
  $reg_course_code = "";   
  if(!empty($courses_codes)){   
      foreach ($courses_codes as $course_code){
          $course_code = $course_code.",";
          $reg_course_code = $reg_course_code.$course_code;
      }
  }
  
  
  
  
  
  # Valida campos obligatorios
  if(empty($nb_programa))
    $nb_programa_err = ERR_REQUERIDO;
  if(empty($ds_tipo))
    $ds_tipo_err = ERR_REQUERIDO;
  if(empty($no_horas))
    $no_horas_err = ERR_REQUERIDO;
  if(empty($no_semanas))
    $no_semanas_err = ERR_REQUERIDO;
  if(empty($ds_credential))
    $ds_credential_err = ERR_REQUERIDO;
  if(empty($no_creditos))
    $no_creditos_err = ERR_REQUERIDO;
  if(empty($nb_thumb_load) AND (empty($nb_thumb)))
    $nb_thumb_err = ERR_REQUERIDO;
  if($nb_thumb_load AND (empty($nb_thumb)))
    $nb_thumb = $nb_thumb_load;
  
  if(empty($ds_programa))
    $ds_programa_err = ERR_REQUERIDO;
  if(empty($ds_learning))
    $ds_learning_err = ERR_REQUERIDO;
  if(empty($ds_metodo))
    $ds_metodo_err = ERR_REQUERIDO;
  if(empty($ds_requerimiento))
    $ds_requerimiento_err = ERR_REQUERIDO;
  
  # Valida enteros
  if($no_orden > MAX_TINYINT)
    $no_orden_err = ERR_TINYINT;
  if($no_horas > MAX_SMALLINT)
    $no_horas_err = ERR_SMALLINT;
  if($no_semanas > MAX_TINYINT)
    $no_semanas_err = ERR_TINYINT;
  
  if(empty($clave ) AND ExisteEnTabla('c_programa_sp', 'ds_course_code', $ds_course_code))
    $ds_course_code_err = ERR_DUPVAL;  
  
  if(!empty($nb_programa_err) OR !empty($no_creditos_err) OR !empty($no_orden_err) OR !empty($no_horas_err) OR !empty($no_semanas_err) OR !empty($ds_credential_err) OR !empty($nb_thumb_err))
    $tab_prog_err = 1; 
  
  if(!empty($ds_programa_err) OR !empty($ds_learning_err) OR !empty($ds_metodo_err) OR !empty($ds_requerimiento_err))
    $tab_outline_err = 1;

  if(empty($ds_course_code))
    $ds_course_code_err = ERR_REQUERIDO;
  if(empty($nb_tags2))
    $nb_tags2_err = ERR_REQUERIDO;
  if(empty($nb_sof2))
    $nb_sof2_err = ERR_REQUERIDO;
  if(empty($nb_har2))
    $nb_har2_err = ERR_REQUERIDO;
  if(empty($nb_fos2))
    $nb_fos2_err = ERR_REQUERIDO;
  if(empty($reg_programa))
    $reg_programa_err = ERR_REQUERIDO;
	
  //if(empty($reg_course_code))
   // $reg_course_code_err =ERR_REQUERIDO; 
	

  if(!empty($nb_tags2_err) OR !empty($nb_sof2_err) OR !empty($nb_har2_err) OR !empty($nb_fos2_err) OR !empty($reg_programa_err) OR !empty($ds_course_code_err))
    $tab_cats_err = ERR_REQUERIDO;
  
  # Subimos el archivo antes del error para no perderlo a pesar de de que exista error
  if(!empty($_FILES['thumb']['tmp_name'][0])) {
    $ruta = SP_HOME."/AD3M2SRC4/modules/fame/uploads";
    $ext = strtolower(ObtenExtensionArchivo($_FILES['thumb']['name']));
    $ds_ruta_foto = $_FILES['thumb']['name'];
    move_uploaded_file($_FILES['thumb']['tmp_name'], $ruta."/".$ds_ruta_foto);
    
    CreaThumb($ruta."/".$ds_ruta_foto, $ruta."/".$ds_ruta_foto, 185, 205);
  }
  
	# Regresa a la forma con error
  $fg_error = $nb_programa_err || $ds_tipo_err || $no_orden_err  ||
              $no_horas_err || $no_semanas_err || $ds_credential_err || $no_creditos_err || $nb_thumb_err || $ds_course_code_err || 
              $tab_prog_err || $tab_outline_err || $ds_programa_err || $ds_learning_err || $ds_metodo_err || $ds_requerimiento_err || 
              $tab_cats_err || $nb_tags2_err || $nb_sof2_err || $nb_har2_err || $nb_fos2_err || $reg_programa_err;

  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('nb_programa' , $nb_programa);
    Forma_CampoOculto('nb_programa_esp' , $nb_programa_esp);
    Forma_CampoOculto('nb_programa_fra' , $nb_programa_fra);
    Forma_CampoOculto('nb_programa_err' , $nb_programa_err);
    Forma_CampoOculto('ds_tipo' , $ds_tipo);
    Forma_CampoOculto('ds_tipo_err' , $ds_tipo_err);
    Forma_CampoOculto('no_orden' , $no_orden);
    Forma_CampoOculto('no_orden_err' , $no_orden_err);
    Forma_CampoOculto('no_horas', $no_horas);
    Forma_CampoOculto('no_horas_err' , $no_horas_err);
    Forma_CampoOculto('no_semanas', $no_semanas);
    Forma_CampoOculto('no_semanas_err' , $no_semanas_err);
    Forma_CampoOculto('ds_credential', $ds_credential);
    Forma_CampoOculto('ds_credential_err' , $ds_credential_err);
    Forma_CampoOculto('cl_delivery', $cl_delivery);
    Forma_CampoOculto('ds_language', $ds_language);
    Forma_CampoOculto('cl_type', $cl_type);
    Forma_CampoOculto('fl_programa' , $fl_programa);
    Forma_CampoOculto('workload', $workload);
    Forma_CampoOculto('fg_fulltime', $fg_fulltime);
    Forma_CampoOculto('no_creditos', $no_creditos);
    Forma_CampoOculto('no_creditos_err', $no_creditos_err);
    Forma_CampoOculto('nb_thumb', $nb_thumb);
    Forma_CampoOculto('nb_thumb_err', $nb_thumb_err);
    Forma_CampoOculto('fg_taxes', $fg_taxes);
    Forma_CampoOculto('nb_tags2', $nb_tags2);
    Forma_CampoOculto('nb_fos2', $nb_fos2);
    Forma_CampoOculto('nb_sof2', $nb_sof2);
    Forma_CampoOculto('nb_har2', $nb_har2);
    Forma_CampoOculto('nb_lvl', $nb_lvl);
    Forma_CampoOculto('nb_cce2', $nb_cce2);
    Forma_CampoOculto('nb_css2', $nb_css2);
    Forma_CampoOculto('fg_nuevo_programa', $fg_nuevo_programa);
    Forma_CampoOculto('ds_programa', $ds_programa);
    Forma_CampoOculto('ds_programa_esp', $ds_programa_esp);
    Forma_CampoOculto('ds_programa_fra', $ds_programa_fra);
    Forma_CampoOculto('ds_learning', $ds_learning);
    Forma_CampoOculto('ds_learning_esp', $ds_learning_esp);
    Forma_CampoOculto('ds_learning_fra', $ds_learning_fra);
    Forma_CampoOculto('ds_metodo', $ds_metodo);
    Forma_CampoOculto('ds_metodo_esp', $ds_metodo_esp);
    Forma_CampoOculto('ds_metodo_fra', $ds_metodo_fra);
    Forma_CampoOculto('ds_requerimiento', $ds_requerimiento);
    Forma_CampoOculto('ds_requerimiento_esp', $ds_requerimiento_esp);
    Forma_CampoOculto('ds_requerimiento_fra', $ds_requerimiento_fra);
    Forma_CampoOculto('fg_board', $fg_board);
    Forma_CampoOculto('ds_course_code_err', $ds_course_code_err);
    Forma_CampoOculto('ds_course_code', $ds_course_code);
    Forma_CampoOculto('nb_pagina', $nb_pagina);
    Forma_CampoOculto('ds_pagina', $ds_pagina);
    Forma_CampoOculto('ds_titulo', $ds_titulo);
    Forma_CampoOculto('ds_contenido', $ds_contenido);
    Forma_CampoOculto('tr_contenido', $tr_contenido);
    Forma_CampoOculto('tab_prog_err', $tab_prog_err);
    Forma_CampoOculto('tab_outline_err', $tab_outline_err);
    Forma_CampoOculto('ds_programa_err', $ds_programa_err);
    Forma_CampoOculto('ds_learning_err', $ds_learning_err);
    Forma_CampoOculto('ds_metodo_err', $ds_metodo_err);
    Forma_CampoOculto('ds_requerimiento_err', $ds_requerimiento_err);
    Forma_CampoOculto('reg_programa', $reg_programa);
    Forma_CampoOculto('reg_programa_pre', $reg_programa_pre);
    Forma_CampoOculto('reg_programa_sig', $reg_programa_sig);
    Forma_CampoOculto('reg_course_code', $reg_course_code);
	//Forma_CampoOculto('reg_course_code_err', $reg_course_code_err);
	
    Forma_CampoOculto('fg_obligatorio', $fg_obligatorio);

    Forma_CampoOculto('tab_cats_err', $tab_cats_err);
    Forma_CampoOculto('nb_tags2_err', $nb_tags2_err);
    Forma_CampoOculto('nb_sof2_err', $nb_sof2_err);
    Forma_CampoOculto('nb_har2_err', $nb_har2_err);
    Forma_CampoOculto('nb_fos2_err', $nb_fos2_err);
    Forma_CampoOculto('reg_programa_err', $reg_programa_err);
    Forma_CampoOculto('fg_publicar',$fg_publicar);
	Forma_CampoOculto('no_email',$no_email);
	Forma_CampoOculto('mn_precio',$mn_precio);
    Forma_CampoOculto('ds_contenido_curso',$ds_contenido_curso);
	Forma_CampoOculto('no_dias_trial',$no_dias_trial);
    Forma_CampoOculto('no_dias_pago',$no_dias_pago);
    
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  if(empty($no_email))
    $no_email=0;
  if(empty($no_dias_trial))
    $no_dias_trial=0;
  if(empty($no_dias_pago))
    $no_dias_pago=0;
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
      $Query  = "INSERT INTO c_programa_sp (nb_programa, nb_programa_esp, nb_programa_fra, ds_tipo, no_orden, fg_fulltime, no_creditos, nb_thumb, fg_taxes, ds_programa, ds_programa_esp, ds_programa_fra, fg_nuevo_programa, ds_learning, ds_learning_esp, ds_learning_fra, ds_metodo, ds_metodo_esp, ds_metodo_fra, ds_requerimiento, ds_requerimiento_esp, ds_requerimiento_fra, ds_course_code, fg_level, fg_obligatorio,fg_publico,no_email_desbloquear,mn_precio,ds_contenido,no_dias_trial,no_dias_pago, ds_duracion) ";
      $Query .= "VALUES ('$nb_programa', '$nb_programa_esp', '$nb_programa_fra', '$ds_tipo', $no_orden, '$fg_fulltime', $no_creditos, '$nb_thumb', '$fg_taxes', '$ds_programa', '$ds_programa_esp', '$ds_programa_fra', '$fg_nuevo_programa', '$ds_learning', '$ds_learning_esp', '$ds_learning_fra', '$ds_metodo', '$ds_metodo_esp', '$ds_metodo_fra', '$ds_requerimiento', '$ds_requerimiento_esp', '$ds_requerimiento_fra', '$ds_course_code', '$nb_lvl', '$fg_obligatorio','$fg_publicar',$no_email,$mn_precio,'$ds_contenido_curso',$no_dias_trial,$no_dias_pago, '')";
    EjecutaQuery($Query);

    $row = RecuperaValor("SELECT MAX(fl_programa_sp) FROM c_programa_sp");
    $clave = $row[0];
    
    $Query  = "INSERT INTO k_programa_detalle_sp (fl_programa_sp, no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_workload, fg_board, no_horas_week) ";
    $Query .= "VALUES ($clave, $no_horas, $no_semanas, '$ds_credential', '$cl_delivery', '$ds_language', $cl_type, '$workload', '1', 0)";
    EjecutaQuery($Query);
  } else {
    $Query  = "UPDATE c_programa_sp SET nb_programa = \"$nb_programa\", nb_programa_esp = \"$nb_programa_esp\", nb_programa_fra = \"$nb_programa_fra\", ";
    $Query .= "ds_tipo = \"$ds_tipo\", no_orden = \"$no_orden\", ";
    $Query .= "fg_fulltime = \"$fg_fulltime\", no_creditos = $no_creditos, nb_thumb = \"$nb_thumb\", fg_taxes =\"$fg_taxes\", ds_programa = \"$ds_programa\", ds_programa_esp = \"$ds_programa_esp\", ds_programa_fra = \"$ds_programa_fra\", fg_nuevo_programa = \"$fg_nuevo_programa\", ds_learning = \"$ds_learning\", ds_learning_esp = \"$ds_learning_esp\", ds_learning_fra = \"$ds_learning_fra\", ds_metodo = \"$ds_metodo\", ds_metodo_esp = \"$ds_metodo_esp\", ds_metodo_fra = \"$ds_metodo_fra\", ds_requerimiento = \"$ds_requerimiento\", ds_requerimiento_esp = \"$ds_requerimiento_esp\",ds_requerimiento_fra = \"$ds_requerimiento_fra\", ds_course_code = \"$ds_course_code\", fg_level = \"$nb_lvl\", fg_obligatorio=\"$fg_obligatorio\", fg_publico=\"$fg_publicar\" ";
	$Query .= ",no_email_desbloquear=$no_email ";
	$Query .=",mn_precio=$mn_precio , ds_contenido=\"$ds_contenido_curso\", no_dias_trial=$no_dias_trial, no_dias_pago=$no_dias_pago ";
	$Query .= "WHERE fl_programa_sp = \"$clave\" ";
    EjecutaQuery($Query);

    $Query  = "UPDATE k_programa_detalle_sp SET no_horas = '$no_horas', no_semanas = '$no_semanas', ";
    $Query .= "ds_credential = '$ds_credential', cl_delivery = '$cl_delivery', ds_language = '$ds_language', cl_type = '$cl_type', no_workload = '$workload', fg_board = '$fg_board' ";
    $Query .= "WHERE fl_programa_sp = '$clave' ";
    EjecutaQuery($Query);
  }
  
  # Borramos todas sus categorias para insertarlas de nuevo
  EjecutaQuery("DELETE FROM k_categoria_programa_sp WHERE fl_programa_sp = $clave");
  EjecutaQuery("DELETE FROM k_relacion_programa_sp WHERE fl_programa_sp_act = $clave");
  EjecutaQuery("DELETE FROM k_grade_programa_sp WHERE fl_programa_sp = $clave");
  
  EjecutaQuery("DELETE FROM k_course_code_prog_fame WHERE fl_programa_sp=$clave ");
  
  # Categorias principales
  foreach($nb_tags as $id=>$nb_tags){
    if(!ExisteEnTabla('c_categoria_programa_sp', 'nb_categoria', $nb_tags)){
      if(!empty($nb_tags)){
        $fl_cat_prog_sp = EjecutaInsert("INSERT INTO c_categoria_programa_sp (nb_categoria, fg_categoria) VALUES ('$nb_tags', 'CAT')");
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    } else {
      $rs = EjecutaQuery("SELECT fl_cat_prog_sp FROM c_categoria_programa_sp  WHERE nb_categoria = '$nb_tags' AND fg_categoria = 'CAT'");
      for($i=0;$row=RecuperaRegistro($rs);$i++) {
        $fl_cat_prog_sp = $row[0];
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }
	}

  # Categorias tipo hardwate
  foreach($nb_har as $id=>$nb_har){
    if(!ExisteEnTabla('c_categoria_programa_sp', 'nb_categoria', $nb_har)){
      if(!empty($nb_har)){
        $fl_cat_prog_sp = EjecutaInsert("INSERT INTO c_categoria_programa_sp (nb_categoria, fg_categoria) VALUES ('$nb_har', 'HAR')");
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    } else {
      $rs = EjecutaQuery("SELECT fl_cat_prog_sp FROM c_categoria_programa_sp  WHERE nb_categoria = '$nb_har' AND fg_categoria = 'HAR'");
      for($i=0;$row=RecuperaRegistro($rs);$i++) {
        $fl_cat_prog_sp = $row[0];
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }
	}

  # Categorias tipo software
  foreach($nb_sof as $id=>$nb_sof){
    if(!ExisteEnTabla('c_categoria_programa_sp', 'nb_categoria', $nb_sof)){
      if(!empty($nb_sof)){
        $fl_cat_prog_sp = EjecutaInsert("INSERT INTO c_categoria_programa_sp (nb_categoria, fg_categoria) VALUES ('$nb_sof', 'SOF')");
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }else{
      $rs = EjecutaQuery("SELECT fl_cat_prog_sp FROM c_categoria_programa_sp  WHERE nb_categoria = '$nb_sof' AND fg_categoria = 'SOF'");
      for($i=0;$row=RecuperaRegistro($rs);$i++) {
        $fl_cat_prog_sp = $row[0];
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }
	}

  # Categorias tipo course code
  foreach($nb_cce as $id=>$nb_cce){
    if(!ExisteEnTabla('c_categoria_programa_sp', 'nb_categoria', $nb_cce)){
      if(!empty($nb_cce)){
        $fl_cat_prog_sp = EjecutaInsert("INSERT INTO c_categoria_programa_sp (nb_categoria, fg_categoria) VALUES ('$nb_cce', 'CCE')");
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }else{
      $rs = EjecutaQuery("SELECT fl_cat_prog_sp FROM c_categoria_programa_sp  WHERE nb_categoria = '$nb_cce' AND fg_categoria = 'CCE'");
      for($i=0;$row=RecuperaRegistro($rs);$i++) {
        $fl_cat_prog_sp = $row[0];
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }
	}

  # Categorias tipo course series
  foreach($nb_css as $id=>$nb_css){
    if(!ExisteEnTabla('c_categoria_programa_sp', 'nb_categoria', $nb_css)){
      if(!empty($nb_css)){
        $fl_cat_prog_sp = EjecutaInsert("INSERT INTO c_categoria_programa_sp (nb_categoria, fg_categoria) VALUES ('$nb_css', 'CSS')");
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }else{
      $rs = EjecutaQuery("SELECT fl_cat_prog_sp FROM c_categoria_programa_sp  WHERE nb_categoria = '$nb_css' AND fg_categoria = 'CSS'");
      for($i=0;$row=RecuperaRegistro($rs);$i++) {
        $fl_cat_prog_sp = $row[0];
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }
	}
 
  # Grados relacionados con el curso
  foreach ($programas as $programa){
    EjecutaQuery("INSERT INTO k_grade_programa_sp(fl_programa_sp, fl_grado) VALUES($clave, ".$programa.")");
  }
 
  # Programas anteriores relacionados con el curso
  foreach ($programas_pre as $programa_pre){
    EjecutaQuery("INSERT INTO k_relacion_programa_sp(fl_programa_sp_act, fl_programa_sp_rel, fg_puesto) VALUES($clave, ".$programa_pre.", 'ANT')");
  } 
  
  # Programas siguientes relacionados con el curso
  foreach ($programas_sig as $programa_sig){
    EjecutaQuery("INSERT INTO k_relacion_programa_sp(fl_programa_sp_act, fl_programa_sp_rel, fg_puesto) VALUES($clave, ".$programa_sig.", 'SIG')");
  }
 
  # Courses code por pais.
  foreach($courses_codes as $course_code){
      EjecutaQuery("INSERT INTO  k_course_code_prog_fame (fl_programa_sp,fl_course_code)VALUES($clave,".$course_code.") ");
  }
  
  
  # Categorias tipo software
  foreach($nb_fos as $id=>$nb_fos){
    if(!ExisteEnTabla('c_categoria_programa_sp', 'nb_categoria', $nb_fos)){
      if(!empty($nb_fos)){
        $fl_cat_prog_sp = EjecutaInsert("INSERT INTO c_categoria_programa_sp (nb_categoria, fg_categoria) VALUES ('$nb_fos', 'FOS')");
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }else{
      $rs = EjecutaQuery("SELECT fl_cat_prog_sp FROM c_categoria_programa_sp  WHERE nb_categoria = '$nb_fos' AND fg_categoria = 'FOS'");
      for($i=0;$row=RecuperaRegistro($rs);$i++) {
        $fl_cat_prog_sp = $row[0];
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }
  }

  
  # Guardamos video de fixed pages and content
  # Parametros para convertir archivos mov en flv
  // $parametros = ObtenConfiguracion(12);
  // $ruta_tmp = $_SERVER[DOCUMENT_ROOT].PATH_FAME_TMP;
  // $ruta_str = PATH_STREAMING_FAME;
  
  # Recibe archivo de video
  /*if(!empty($archivo)) {
    $ext = strtoupper(ObtenExtensionArchivo($archivo));
    $ds_vl_ruta = $archivo;
    
    # Mueve el archivo subido al directorio para streaming
    if(file_exists($ruta_str."/".$archivo))
      unlink($ruta_str."/".$archivo);
    rename($ruta_tmp."/".$archivo, $ruta_str."/".$archivo);
    
    # Convierte archivos .mov a .flv
    if($ext == "MOV" OR $ext == "MP4") {
      $file_mov_lecture = $ruta_str."/".$archivo;
      $file_flv = 'CAM_CONTENT_FAME' . substr($archivo, 0, (strlen($archivo)-4)) . '.flv';
      if(file_exists($ruta_str."/".$file_flv))
        unlink($ruta_str."/".$file_flv);
      $comando_1 = CMD_FFMPEG_FAME." -i \"$file_mov_lecture\" $parametros \"$ruta_str/$file_flv\"";
      $ds_vl_ruta = $file_flv;
    }
    
    # Creacion de liga para servidor de streaming
    $comando_2 = "ln -s \"".$ruta_str."/".$ds_vl_ruta."\" ".PATH_LINKS_FAME;
    
  }*/
  
  # If NO exist fixed page with program insert new page
  $row = RecuperaValor("SELECT cl_pagina_sp FROM c_pagina_sp WHERE fl_programa_sp=$clave");
  $cl_pagina_sp = !empty($row[0])?$row[0]:NULL;
  if(empty($cl_pagina_sp)){
    $Query  = "INSERT INTO c_pagina_sp (fl_programa_sp,nb_pagina,ds_pagina,ds_titulo,tr_titulo,ds_contenido,tr_contenido,fg_fijo) ";
    $Query .= "VALUES ($clave, '$nb_pagina', '$ds_pagina', '$ds_titulo', '$tr_titulo', '$ds_contenido', '$tr_contenido', '0') ";
    $cl_pagina_sp = EjecutaInsert($Query);
  }
  # If exist fixed page with program update
  else{
    $Query  = "UPDATE c_pagina_sp SET nb_pagina = '$nb_pagina',ds_pagina = '$ds_pagina',ds_titulo = '$ds_titulo', tr_titulo = '$tr_titulo' ";
    $Query .= ",ds_contenido = '$ds_contenido',tr_contenido = '$tr_contenido' ,fg_fijo = '0' WHERE cl_pagina_sp = $cl_pagina_sp AND fl_programa_sp = $clave ";    
    EjecutaQuery($Query);
  }  
  
   #Inserta registros de videos nuevos
  // if(!empty($archivo) && !empty($cl_pagina_sp)){
    // $Query2 = "INSERT INTO k_video_contenido_sp (cl_pagina_sp, fl_programa_sp, ds_ruta_video) ";
    // $Query2 .= "VALUES($cl_pagina_sp, $clave, '$ds_vl_ruta')";
    // EjecutaQuery($Query2);
  // }

  # Borramos categorias que no tengan relacion con cursos
  $rs = EjecutaQuery("SELECT a.fl_cat_prog_sp FROM c_categoria_programa_sp a WHERE NOT EXISTS(SELECT * FROM k_categoria_programa_sp b WHERE a.fl_cat_prog_sp = b.fl_cat_prog_sp)");
  for($i=0;$row=RecuperaRegistro($rs);$i++)
    EjecutaQuery("DELETE FROM c_categoria_programa_sp WHERE fl_cat_prog_sp = $row[0]");
  
  # VIDEOS
  # Si existe video con datos de usuario
  $queryr = "SELECT COUNT(*)FROM k_vid_content_temp WHERE fl_usuario=$fl_usuario AND fl_clave=$fl_usuario AND fl_programa=$fl_usuario AND no_grado=$fl_usuario AND fg_fame='1' ";
  $rowr = RecuperaValor($queryr);
  if(!empty($rowr[0])){
    $Queryx = "SELECT no_orden, nb_archivo FROM k_vid_content_temp WHERE fl_clave=".$fl_usuario." AND fl_programa=".$fl_usuario." AND fg_fame='1' ORDER BY no_orden";
    $rsx = EjecutaQuery($Queryx);
    $tot_reg = CuentaRegistros($rsx);    
    if(!empty($tot_reg)){
      # Carpetas orginiales
      $ruta1 = VID_FAME_STU_LIB."/video_us".$fl_usuario."_us".$fl_usuario;
      # Carpeta nueva
      $ruta1_new = VID_FAME_STU_LIB."/video_".$cl_pagina_sp."_".$clave;
      # Reemplazamos la ruta
      rename($ruta1, $ruta1_new);
      // echo "<div>Ruta inicial:<br>
      // Ruta original: $ruta1 <br>
      // Ruta original nueva: $ruta1_new <br></div>";
      for($i=0;$rowx=RecuperaRegistro($rsx);$i++){
        $no_orden = $rowx[0];
        $nb_archivo = $rowx[1];
        $ruta2 = $ruta1_new."/video_".$no_orden;
        
        # Insertamos los registros en la tabla original
        $Query22  = "INSERT INTO k_video_contenido_sp (cl_pagina_sp, fl_programa_sp, ds_ruta_video) ";
        $Query22 .= "VALUES($cl_pagina_sp, $clave, '$nb_archivo')";
        $fl_vid_new = EjecutaInsert($Query22);
        $ruta2_new = $ruta1_new."/video_".$fl_vid_new;
        # Ruta para los videos SD
        $ruta_sd = $ruta2_new."/video_".$no_orden."_sd";
        $ruta_sd_new = $ruta2_new."/video_".$fl_vid_new."_sd";
        # Ruta para los videos HD
        $ruta_hd = $ruta2_new."/video_".$no_orden."_hd";
        $ruta_hd_new = $ruta2_new."/video_".$fl_vid_new."_hd";
        
        # Ruta para el archivo
        $output_hd = $ruta_hd_new."/output".$fl_vid_new.".php";
        $output_sd = $ruta_sd_new."/output".$fl_vid_new.".php";
        // echo "<div style='padding-left:30px;'>Rutas de los videos: <br>
        // Ruta 2 original: $ruta2 <br/> 
        // Ruta 2 nueva: $ruta2_new <br/></div>";
        // echo "<div style='padding-left:50px; color:blue;'>Rutas HD: <br>
        // Ruta HD original: $ruta_hd <br/> 
        // Ruta HD nueva: $ruta_hd_new <br/>
        // <p style='color:yellow;'>$output_hd</p>
        // </div>";
        // echo "<div style='padding-left:50px; color:green;'>Rutas SD: <br>
        // Ruta HD original: $ruta_sd <br/> 
        // Ruta HD nueva: $ruta_sd_new <br/>
        // <p style='color:yellow;'>$output_sd</p>
        // </div>";
        # Renombramos las carpteas
        rename($ruta2, $ruta2_new);
        rename($ruta_hd, $ruta_hd_new);
        rename($ruta_sd, $ruta_sd_new);
        
        
        ##  Comandos ###
        $attr_comando = "-s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 ";
        $mp4_hd = $ruta_hd_new."/".$nb_archivo.".mp4";
        $m3u8_hd = $ruta_hd_new."/".$nb_archivo.".m3u8";
        $comando_hd = VIDEOS_CMD_HLS." -i $mp4_hd $attr_comando $m3u8_hd 1>$output_hd 2>&1 ";
        $mp4_sd = $ruta_sd_new."/".$nb_archivo.".mp4";
        $m3u8_sd = $ruta_sd_new."/".$nb_archivo.".m3u8";
        $comando_sd = VIDEOS_CMD_HLS." -i $mp4_sd $attr_comando $m3u8_sd 1>$output_sd 2>&1 ";
        // echo "<div style='padding-left:50px; color:red;'>COMANDOS<br>
        // HDHDHDHDHDHD: $comando_hd <br/> 
        // SDDSDSDSDSD: $comando_sd <br/>
        // <p style='color:yellow;'>$output_sd</p>
        // </div>";
        
        ## Ejecutamos los comandos ####
        exec($comando_hd." >> /dev/null &");
        exec($comando_sd." >> /dev/null &");
      }
    }
  }
  else{
    $Queryx = "SELECT no_orden, nb_archivo FROM k_vid_content_temp WHERE fl_clave=".$cl_pagina_sp." AND fl_programa=".$clave." AND fg_fame='1' AND fl_usuario=".$fl_usuario." ORDER BY no_orden";
    $rsx = EjecutaQuery($Queryx);
    $tot_reg = CuentaRegistros($rsx);
    # Carpeta
    $ruta1 = VID_FAME_STU_LIB."/video_".$cl_pagina_sp."_".$clave;
    if(!empty($tot_reg)){
      # Reemplazamos la ruta
      // rename($ruta1, $ruta1_new);
      // echo "<div>Ruta inicial:<br>
      // Ruta original: $ruta1 <br></div>";
      for($i=0;$rowx=RecuperaRegistro($rsx);$i++){
        $no_orden = $rowx[0];
        $nb_archivo = $rowx[1];
        $ruta2 = $ruta1."/video_".$no_orden;
        
        # Insertamos los registros en la tabla original
        $Query22  = "INSERT INTO k_video_contenido_sp (cl_pagina_sp, fl_programa_sp, ds_ruta_video, ds_progreso) ";
        $Query22 .= "VALUES($cl_pagina_sp, $clave, '$nb_archivo', '0')";
        $fl_vid_new = EjecutaInsert($Query22);
        $ruta2_new = $ruta1."/video_".$fl_vid_new;
        # Ruta para los videos SD
        $ruta_sd = $ruta2_new."/video_".$no_orden."_sd";
        $ruta_sd_new = $ruta2_new."/video_".$fl_vid_new."_sd";
        # Ruta para los videos HD
        $ruta_hd = $ruta2_new."/video_".$no_orden."_hd";
        $ruta_hd_new = $ruta2_new."/video_".$fl_vid_new."_hd";
        
        # Ruta para el archivo
        $output_hd = $ruta_hd_new."/output".$fl_vid_new.".php";
        $output_sd = $ruta_sd_new."/output".$fl_vid_new.".php";
        // echo "<div style='padding-left:30px;'>Rutas de los videos: <br>
        // Ruta 2 original: $ruta2 <br/> 
        // Ruta 2 nueva: $ruta2_new <br/></div>";
        // echo "<div style='padding-left:50px; color:blue;'>Rutas HD: <br>
        // Ruta HD original: $ruta_hd <br/> 
        // Ruta HD nueva: $ruta_hd_new <br/>
        // <p style='color:yellow;'>$output_hd</p>
        // </div>";
        // echo "<div style='padding-left:50px; color:green;'>Rutas SD: <br>
        // Ruta HD original: $ruta_sd <br/> 
        // Ruta HD nueva: $ruta_sd_new <br/>
        // <p style='color:yellow;'>$output_sd</p>
        // </div>";
        # Renombramos las carpteas
        rename($ruta2, $ruta2_new);
        rename($ruta_hd, $ruta_hd_new);
        rename($ruta_sd, $ruta_sd_new);
        
        ##  Comandos ###
        $attr_comando = "-s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 ";
        $mp4_hd = $ruta_hd_new."/".$nb_archivo.".mp4";
        $m3u8_hd = $ruta_hd_new."/".$nb_archivo.".m3u8";
        $comando_hd = VIDEOS_CMD_HLS." -i $mp4_hd $attr_comando $m3u8_hd 1>$output_hd 2>&1 ";
        $mp4_sd = $ruta_sd_new."/".$nb_archivo.".mp4";
        $m3u8_sd = $ruta_sd_new."/".$nb_archivo.".m3u8";
        $comando_sd = VIDEOS_CMD_HLS." -i $mp4_sd $attr_comando $m3u8_sd 1>$output_sd 2>&1 ";
        // echo "<div style='padding-left:50px; color:red;'>COMANDOS<br>
        // HDHDHDHDHDHD: $comando_hd <br/> 
        // SDDSDSDSDSD: $comando_sd <br/>
        // <p style='color:yellow;'>$output_sd</p>
        // </div>";
        
        ## Ejecutamos los comandos ####
        exec($comando_hd." >> /dev/null &");
        exec($comando_sd." >> /dev/null &");
      }
      
    }
  }

  # ELiminamos los registros temporales
  EjecutaQuery("DELETE FROM k_vid_content_temp WHERE fl_usuario=".$fl_usuario." AND fg_fame='1'");

  # Redirige al listado
  header("Location: ".ObtenProgramaBase());
  
?>
