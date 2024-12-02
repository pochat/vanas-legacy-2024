<?php
  
  # Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario = ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
	
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso($func, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$fl_funcion = RecibeParametroNumerico('fl_funcion');
  $nb_funcion = RecibeParametroHTML('nb_funcion');
  $cl_template = RecibeParametroNumerico('cl_template');
  $nb_template = RecibeParametroHTML('nb_template');
  $nb_titulo = RecibeParametroHTML('nb_titulo');
  $tr_titulo = RecibeParametroHTML('tr_titulo');
  $ds_resumen = RecibeParametroHTML('ds_resumen');
  $tr_resumen = RecibeParametroHTML('tr_resumen');
  $fe_evento = RecibeParametroFecha('fe_evento');
  $fe_ini = RecibeParametroFecha('fe_ini');
  $fe_fin = RecibeParametroFecha('fe_fin');
  $fg_menu = RecibeParametroNumerico('fg_menu');
  $no_orden = RecibeParametroNumerico('no_orden');
  $fg_fijo = RecibeParametroNumerico('fg_fijo');
  if(!empty($fg_fijo))
    $fg_fijo = "1";
  $fg_activo = RecibeParametroNumerico('fg_activo');
  if(!empty($fg_activo))
    $fg_activo = "1";
  $ds_usuario_alta = RecibeParametroHTML('ds_usuario_alta');
  $ds_usuario_mod = RecibeParametroHTML('ds_usuario_mod');
  $fe_alta = RecibeParametroFecha('fe_alta');
  $fe_ultmod = RecibeParametroFecha('fe_ultmod');
  $ds_nota = RecibeParametroHTML('ds_nota');
  $no_nivel = RecibeParametroNumerico('no_nivel');
  if(empty($no_nivel))
    $no_nivel = 1;
  $fg_titulo = RecibeParametroNumerico('fg_titulo');
  $fg_resumen = RecibeParametroNumerico('fg_resumen');
  $fg_fecha_evento = RecibeParametroNumerico('fg_fecha_evento');
  $no_texto = RecibeParametroNumerico('no_texto');
  $no_imagen_dinamica = RecibeParametroNumerico('no_imagen_dinamica');
  $no_flash = RecibeParametroNumerico('no_flash');
  $no_tabla = RecibeParametroNumerico('no_tabla');
  $fg_anexo = RecibeParametroNumerico('fg_anexo');
  for($i = 1; $i <= $no_texto; $i++) {
    $fl_texto[$i] = RecibeParametroNumerico('fl_texto_'.$i);
    $ds_contenido[$i] = RecibeParametroHTML('ds_contenido_'.$i);
    $tr_contenido[$i] = RecibeParametroHTML('tr_contenido_'.$i);
  }
  for($i = 1; $i <= $no_imagen_dinamica; $i++) {
    $fl_imagen_dinamica[$i] = RecibeParametroNumerico('fl_imagen_dinamica_'.$i);
    $ds_caption_i[$i] = RecibeParametroHTML('ds_caption_i_'.$i);
    $tr_caption_i[$i] = RecibeParametroHTML('tr_caption_i_'.$i);
    $nb_archivo_i[$i] = RecibeParametroHTML('nb_archivo_i_'.$i);
    $tr_archivo_i[$i] = RecibeParametroHTML('tr_archivo_i_'.$i);
    $ds_alt_i[$i] = RecibeParametroHTML('ds_alt_i_'.$i);
    $tr_alt_i[$i] = RecibeParametroHTML('tr_alt_i_'.$i);
    $ds_liga_i[$i] = RecibeParametroHTML('ds_liga_i_'.$i);
  }
  for($i = 1; $i <= $no_flash; $i++) {
    $fl_flash[$i] = RecibeParametroNumerico('fl_flash_'.$i);
    $nb_archivo_f[$i] = RecibeParametroHTML('nb_archivo_f_'.$i);
    $tr_archivo_f[$i] = RecibeParametroHTML('tr_archivo_f_'.$i);
    $no_width_f[$i] = RecibeParametroNumerico('no_width_f_'.$i);
    $no_height_f[$i] = RecibeParametroNumerico('no_height_f_'.$i);
  }
  $archivo = RecibeParametroHTML('archivo');
  for($i = 1; $i <= $no_tabla; $i++)
    $fl_tabla[$i] = RecibeParametroNumerico('fl_tabla_'.$i);
  $regs_ini_anexos = RecibeParametroNumerico('regs_ini_anexos');
  $tot_regs_anexos = RecibeParametroNumerico('tot_regs_anexos');
  $regs_borrar_anexos = RecibeParametroHTML('regs_borrar_anexos');
  for($i = 0; $i < $tot_regs_anexos; $i++) {
    $fl_anexo[$i] = RecibeParametroNumerico('fl_anexo_'.$i);
    $no_orden_a[$i] = RecibeParametroNumerico('no_orden_a_'.$i);
    $ds_caption_a[$i] = RecibeParametroHTML("ds_caption_a_$i");
    $tr_caption_a[$i] = RecibeParametroHTML("tr_caption_a_$i");
    $nb_archivo_a[$i] = RecibeParametroHTML("nb_archivo_a_$i");
    $tr_archivo_a[$i] = RecibeParametroHTML("tr_archivo_a_$i");
    $ds_texto_a[$i] = RecibeParametroHTML("ds_texto_a_$i");
    $tr_texto_a[$i] = RecibeParametroHTML("tr_texto_a_$i");
    $nb_imagen_a[$i] = RecibeParametroHTML("nb_imagen_a_$i");
  }
  $fl_seccion = RecibeParametroNumerico('fl_seccion');
  $nb_seccion = RecibeParametroHTML('nb_seccion');
  $cl_pagina = RecibeParametroNumerico('cl_pagina');
  $nb_pagina = RecibeParametroHTML('nb_pagina');
  $ds_ruta = RecibeParametroHTML('ds_ruta');
  $tr_ruta = RecibeParametroHTML('tr_ruta');
  $fg_ventana = RecibeParametroNumerico('fg_ventana');
  if(!empty($fg_ventana))
    $fg_ventana = "1";
  
  # Valida campos obligatorios
  if(empty($nb_funcion))
    $nb_funcion_err = ERR_REQUERIDO;
  if(empty($nb_template))
    $nb_template_err = ERR_REQUERIDO;
  if($fg_fecha_evento == 1 AND empty($fe_evento))
    $fe_evento_err = ERR_REQUERIDO;
  for($i = 1; $i <= $no_flash; $i++) {
    if(empty($no_width_f[$i]))
      $no_width_f_err[$i] = ERR_REQUERIDO;
    if(empty($no_height_f[$i]))
      $no_height_f_err[$i] = ERR_REQUERIDO;
  }
  
  # Los registros existentes tienen fl_anexo=<folio>, los registros borrados tienen fl_anexo vacio
  for($i = 0; $i < $regs_ini_anexos; $i++) {
    if(!empty($fl_anexo[$i]) AND empty($ds_caption_a[$i]))
      $ds_caption_a_err[$i] = ERR_REQUERIDO;
  }
  
  # Los registros nuevos tienen fl_anexo=0, los registros borrados tienen odos los campos vacios
  for($i = $regs_ini_anexos; $i < $tot_regs_anexos; $i++) {
    if(empty($ds_caption_a[$i]) AND !empty($no_orden_a[$i]))
      $ds_caption_a_err[$i] = ERR_REQUERIDO;
  }
  
  # Valida enteros
  if(!ValidaEntero($no_orden))
    $no_orden_err = ERR_ENTERO;
  if($no_orden > MAX_SMALLINT)
    $no_orden_err = ERR_SMALLINT;
  for($i = 1; $i <= $no_flash; $i++) {
    if(!empty($no_width_f[$i]) AND !ValidaEntero($no_width_f[$i]))
      $no_width_f_err[$i] = ERR_ENTERO;
    if($no_width_f[$i] > MAX_SMALLINT)
      $no_width_f_err[$i] = ERR_SMALLINT;
    if(!empty($no_height_f[$i]) AND !ValidaEntero($no_height_f[$i]))
      $no_height_f_err[$i] = ERR_ENTERO;
    if($no_height_f[$i] > MAX_SMALLINT)
      $no_height_f_err[$i] = ERR_SMALLINT;
  }
  
  # Verifica que el template seleccionado corresponda a la funcion
  if(!empty($fl_funcion) AND !empty($cl_template)) {
    $Query  = "SELECT 1 FROM k_tipo_contenido_template a, c_funcion b ";
    $Query .= "WHERE a.cl_tipo_contenido=b.cl_tipo_contenido ";
    $Query .= "AND a.cl_template=$cl_template ";
    $Query .= "AND b.fl_funcion=$fl_funcion";
    $row = RecuperaValor($Query);
    if(empty($row[0]))
      $nb_template_err = 106; // Este template no corresponde a la seccion seleccionada.
  }
  
	# Verifica que el formato de la fecha sea valido
  if(!empty($fe_ini) AND !ValidaFecha($fe_ini))
    $fe_ini_err = ERR_FORMATO_FECHA;
  if(!empty($fe_fin) AND !ValidaFecha($fe_fin))
    $fe_fin_err = ERR_FORMATO_FECHA;
  if(!empty($fe_evento) AND !ValidaFecha($fe_evento))
    $fe_evento_err = ERR_FORMATO_FECHA;
  
  # Regresa a la forma con error
  $fg_error = $nb_funcion_err || $nb_template_err || $no_orden_err || $fe_ini_err || $fe_fin_err || $fe_evento_err;
  for($i = 0; $i < $tot_regs_anexos; $i++)
    $fg_error = $fg_error || $ds_caption_a_err[$i];
  for($i = 1; $i <= $no_flash; $i++) {
    $fg_error = $fg_error || $no_width_f_err[$i];
    $fg_error = $fg_error || $no_height_f_err[$i];
  }
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('fl_funcion', $fl_funcion);
    Forma_CampoOculto('nb_funcion', $nb_funcion);
    Forma_CampoOculto('nb_funcion_err', $nb_funcion_err);
    Forma_CampoOculto('cl_template', $cl_template);
    Forma_CampoOculto('nb_template', $nb_template);
    Forma_CampoOculto('nb_template_err', $nb_template_err);
    Forma_CampoOculto('nb_titulo', $nb_titulo);
    Forma_CampoOculto('tr_titulo', $tr_titulo);
    Forma_CampoOculto('ds_resumen', $ds_resumen);
    Forma_CampoOculto('tr_resumen', $tr_resumen);
    Forma_CampoOculto('fe_evento', $fe_evento);
    Forma_CampoOculto('fe_evento_err', $fe_evento_err);
    Forma_CampoOculto('fe_ini', $fe_ini);
    Forma_CampoOculto('fe_ini_err', $fe_ini_err);
    Forma_CampoOculto('fe_fin', $fe_fin);
    Forma_CampoOculto('fe_fin_err', $fe_fin_err);
    Forma_CampoOculto('fg_menu' , $fg_menu);
    Forma_CampoOculto('no_orden', $no_orden);
    Forma_CampoOculto('no_orden_err', $no_orden_err);
    Forma_CampoOculto('fg_fijo', $fg_fijo);
    Forma_CampoOculto('fg_activo', $fg_activo);
    Forma_CampoOculto('ds_nota', $ds_nota);
    Forma_CampoOculto('ds_usuario_alta', $ds_usuario_alta);
    Forma_CampoOculto('ds_usuario_mod', $ds_usuario_mod);
    Forma_CampoOculto('fe_alta', $fe_alta);
    Forma_CampoOculto('fe_ultmod', $fe_ultmod);
    Forma_CampoOculto('no_nivel', $no_nivel);
    Forma_CampoOculto('fg_titulo', $fg_titulo);
    Forma_CampoOculto('fg_resumen', $fg_resumen);
    Forma_CampoOculto('fg_fecha_evento', $fg_fecha_evento);
    Forma_CampoOculto('no_texto', $no_texto);
    Forma_CampoOculto('no_imagen_dinamica' , $no_imagen_dinamica);
    Forma_CampoOculto('no_flash', $no_flash);
    Forma_CampoOculto('no_tabla', $no_tabla);
    Forma_CampoOculto('fg_anexo', $fg_anexo);
    for($i = 1; $i <= $no_texto; $i++) {
      Forma_CampoOculto('fl_texto_'.$i, $fl_texto[$i]);
      Forma_CampoOculto('ds_contenido_'.$i, $ds_contenido[$i]);
      Forma_CampoOculto('tr_contenido_'.$i, $tr_contenido[$i]);
    }
    for($i = 1; $i <= $no_imagen_dinamica; $i++) {
      Forma_CampoOculto('fl_imagen_dinamica_'.$i, $fl_imagen_dinamica[$i]);
      Forma_CampoOculto('ds_caption_i_'.$i, $ds_caption_i[$i]);
      Forma_CampoOculto('tr_caption_i_'.$i, $tr_caption_i[$i]);
      Forma_CampoOculto('nb_archivo_i_'.$i, $nb_archivo_i[$i]);
      Forma_CampoOculto('tr_archivo_i_'.$i, $tr_archivo_i[$i]);
      Forma_CampoOculto('ds_alt_i_'.$i, $ds_alt_i[$i]);
      Forma_CampoOculto('tr_alt_i_'.$i, $tr_alt_i[$i]);
      Forma_CampoOculto('ds_liga_i_'.$i, $ds_liga_i[$i]);
    }
    for($i = 1; $i <= $no_flash; $i++) {
      Forma_CampoOculto('fl_flash_'.$i, $fl_flash[$i]);
      Forma_CampoOculto('nb_archivo_f_'.$i, $nb_archivo_f[$i]);
      Forma_CampoOculto('tr_archivo_f_'.$i, $tr_archivo_f[$i]);
      Forma_CampoOculto('no_width_f_'.$i, $no_width_f[$i]);
      Forma_CampoOculto('no_width_f_err_'.$i, $no_width_f_err[$i]);
      Forma_CampoOculto('no_height_f_'.$i, $no_height_f[$i]);
      Forma_CampoOculto('no_height_f_err_'.$i, $no_height_f_err[$i]);
    }
    for($i = 1; $i <= $no_tabla; $i++)
      Forma_CampoOculto('fl_tabla_'.$i, $fl_tabla[$i]);
    for($i = 0; $i < $tot_regs_anexos; $i++) {
      Forma_CampoOculto('fl_anexo_'.$i, $fl_anexo[$i]);
      Forma_CampoOculto('no_orden_a_'.$i, $no_orden_a[$i]);
      Forma_CampoOculto('ds_caption_a_'.$i, $ds_caption_a[$i]);
      Forma_CampoOculto('ds_caption_a_err_'.$i, $ds_caption_a_err[$i]);
      Forma_CampoOculto('tr_caption_a_'.$i, $tr_caption_a[$i]);
      Forma_CampoOculto('nb_archivo_a_'.$i, $nb_archivo_a[$i]);
      Forma_CampoOculto('tr_archivo_a_'.$i, $tr_archivo_a[$i]);
      Forma_CampoOculto('ds_texto_a_'.$i, $ds_texto_a[$i]);
      Forma_CampoOculto('tr_texto_a_'.$i, $tr_texto_a[$i]);
      Forma_CampoOculto('nb_imagen_a_'.$i, $nb_imagen_a[$i]);
    }
    Forma_CampoOculto('regs_ini_anexos', $regs_ini_anexos);
    Forma_CampoOculto('tot_regs_anexos', $tot_regs_anexos);
    Forma_CampoOculto('regs_borrar_anexos', $regs_borrar_anexos);
    Forma_CampoOculto('fl_seccion', $fl_seccion);
    Forma_CampoOculto('nb_seccion', $nb_seccion);
    Forma_CampoOculto('cl_pagina', $cl_pagina);
    Forma_CampoOculto('nb_pagina', $nb_pagina);
    Forma_CampoOculto('ds_ruta', $ds_ruta);
    Forma_CampoOculto('tr_ruta', $tr_ruta);
    Forma_CampoOculto('fg_ventana', $fg_ventana);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Prepara fechas en formato para insertar
  if(!empty($fe_ini))
    $fe_ini = "'".ValidaFecha($fe_ini)."'";
  else
    $fe_ini = "NULL";
  if(!empty($fe_fin))
    $fe_fin = "'".ValidaFecha($fe_fin)."'";
  else
    $fe_fin = "NULL";
  if(!empty($fe_evento))
    $fe_evento = "'".ValidaFecha($fe_evento)."'";
  else
    $fe_evento = "NULL";
  
  # Revisa si el nivel seleccionado publica el contenido
  $row = RecuperaValor("SELECT fl_flujo FROM c_funcion WHERE fl_funcion=$fl_funcion");
  $fl_flujo = $row[0];
  $row = RecuperaValor("SELECT ds_nivel, tr_nivel, fg_publica FROM k_flujo_nivel WHERE fl_flujo=$fl_flujo AND no_nivel=$no_nivel");
  $ds_nivel = $row[0];
  $tr_nivel = $row[1];
  $fg_activo = $row[2];
  
  # Revisa si se esta cambiando el estado para determinar si se inserta historia
  $cambio_nivel = True;
  if(!empty($clave)) {
    $row = RecuperaValor("SELECT no_nivel FROM c_contenido WHERE fl_contenido=$clave");
    if($row[0] == $no_nivel)
      $cambio_nivel = False;
  }
  
  # Actualiza o inserta el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_contenido (fl_funcion, cl_template, fg_menu, nb_titulo, tr_titulo, ds_resumen, tr_resumen, fe_evento, ";
    $Query .= "fg_activo, fe_ini, fe_fin, no_orden, fg_fijo, fl_usuario_alta, fl_usuario_mod, fe_alta, fe_ultmod, no_nivel) ";
    $Query .= "VALUES($fl_funcion, $cl_template, '$fg_menu', '$nb_titulo', '$tr_titulo', '$ds_resumen', '$tr_resumen', $fe_evento, ";
    $Query .= "'$fg_activo', $fe_ini, $fe_fin, $no_orden, '$fg_fijo', $fl_usuario, $fl_usuario, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, $no_nivel)";
    $clave = EjecutaInsert($Query);
  }
  else {
    $Query  = "UPDATE c_contenido ";
    $Query .= "SET fl_funcion=$fl_funcion, cl_template=$cl_template, fg_menu='$fg_menu', nb_titulo='$nb_titulo', tr_titulo='$tr_titulo', ";
    $Query .= "ds_resumen='$ds_resumen', tr_resumen='$tr_resumen', fe_evento=$fe_evento, ";
    $Query .= "fg_activo='$fg_activo', fe_ini=$fe_ini, fe_fin=$fe_fin, no_orden=$no_orden, fg_fijo='$fg_fijo', ";
    $Query .= "fl_usuario_mod=$fl_usuario, fe_ultmod=CURRENT_TIMESTAMP, no_nivel=$no_nivel ";
    $Query .= "WHERE fl_contenido=$clave";
    EjecutaQuery($Query);
  }
  
  # Actualiza o inserta los textos del contenido
  for($i = 1; $i <= $no_texto; $i++) {
    if(!empty($fl_texto[$i])) {
      $Query  = "UPDATE k_texto SET ds_contenido='$ds_contenido[$i]', tr_contenido='$tr_contenido[$i]' ";
      $Query .= "WHERE fl_texto=$fl_texto[$i]";
    }
    else {
      $Query  = "INSERT INTO k_texto (fl_contenido, no_orden, ds_contenido, tr_contenido) ";
      $Query .= "VALUES($clave, $i, '$ds_contenido[$i]', '$tr_contenido[$i]')";
    }
    EjecutaQuery($Query);
  }
  
  # Actualiza o inserta las imagenes dinamicas del contenido
  for($i = 1; $i <= $no_imagen_dinamica; $i++) {
    if(!empty($_FILES['archivo_i_'.$i]['tmp_name'])) {
      $ruta = SP_IMAGES;
      $nb_archivo = $_FILES['archivo_i_'.$i]['name'];
      move_uploaded_file($_FILES['archivo_i_'.$i]['tmp_name'], $ruta."/".$nb_archivo);
      
      # Genera thumbnail para la foto 2 si son noticias
      if($func == FUNC_NOTICIAS) {
        CreaThumb($ruta."/".$nb_archivo, $ruta."/".$nb_archivo, ObtenConfiguracion(17), 0, 0);
        if($i == 2)
          CreaThumb($ruta."/".$nb_archivo, SP_THUMBS."/".$nb_archivo, ObtenConfiguracion(15), 0, 0);
      }
    }
    else
      $nb_archivo = $nb_archivo_i[$i];
    if(!empty($_FILES['tr_archivo_it_'.$i]['tmp_name'])) {
      $ruta = SP_IMAGES;
      $tr_archivo = $_FILES['tr_archivo_it_'.$i]['name'];
      move_uploaded_file($_FILES['tr_archivo_it_'.$i]['tmp_name'], $ruta."/".$tr_archivo);
    }
    else
      $tr_archivo = $tr_archivo_i[$i];
    if(!empty($fl_imagen_dinamica[$i])) {
      $Query  = "UPDATE k_imagen_dinamica SET ds_caption='$ds_caption_i[$i]', tr_caption='$tr_caption_i[$i]', ";
      $Query .= "nb_archivo='$nb_archivo', tr_archivo='$tr_archivo', ";
      $Query .= "ds_alt='$ds_alt_i[$i]', tr_alt='$tr_alt_i[$i]', ds_liga='$ds_liga_i[$i]' ";
      $Query .= "WHERE fl_imagen_dinamica=$fl_imagen_dinamica[$i]";
    }
    else {
      $Query  = "INSERT INTO k_imagen_dinamica (fl_contenido, no_orden, ds_caption, tr_caption, nb_archivo, tr_archivo, ds_alt, tr_alt, ";
      $Query .= "ds_liga) ";
      $Query .= "VALUES($clave, $i, '$ds_caption_i[$i]', '$tr_caption_i[$i]', '$nb_archivo', '$tr_archivo', '$ds_alt_i[$i]', ";
      $Query .= "'$tr_alt_i[$i]', '$ds_liga_i[$i]')";
    }
    EjecutaQuery($Query);
  }
  
  # Actualiza o inserta los archivos flash del contenido
  for($i = 1; $i <= $no_flash; $i++) {
    if(!empty($_FILES['archivo_f_'.$i]['tmp_name'])) {
      $ruta = SP_FLASH;
      $nb_archivo = $_FILES['archivo_f_'.$i]['name'];
      move_uploaded_file($_FILES['archivo_f_'.$i]['tmp_name'], $ruta."/".$nb_archivo);
    }
    else
      $nb_archivo = $nb_archivo_f[$i];
    if(!empty($_FILES['tr_archivo_ft_'.$i]['tmp_name'])) {
      $ruta = SP_FLASH;
      $tr_archivo = $_FILES['tr_archivo_ft_'.$i]['name'];
      move_uploaded_file($_FILES['tr_archivo_ft_'.$i]['tmp_name'], $ruta."/".$tr_archivo);
    }
    else
      $tr_archivo = $tr_archivo_f[$i];
    if(!empty($fl_flash[$i])) {
      $Query  = "UPDATE k_flash SET nb_archivo='$nb_archivo', tr_archivo='$tr_archivo', no_width=$no_width_f[$i], no_height=$no_height_f[$i] ";
      $Query .= "WHERE fl_flash=$fl_flash[$i]";
    }
    else {
      $Query  = "INSERT INTO k_flash (fl_contenido, no_orden, nb_archivo, tr_archivo, no_width, no_height) ";
      $Query .= "VALUES($clave, $i, '$nb_archivo', '$tr_archivo', $no_width_f[$i], $no_height_f[$i])";
    }
    EjecutaQuery($Query);
  }
  
  
  # Parametros para convertir archivos mov en flv
  $parametros = ObtenConfiguracion(12);
  $ruta_tmp = $_SERVER[DOCUMENT_ROOT].PATH_TMP;
  $ruta_str = PATH_STREAMING;
  
  # Recibe archivo de video e inserta registros de videos nuevos para streaming
  if(!empty($archivo)) {
    $ext = strtoupper(ObtenExtensionArchivo($archivo));
    $ds_vl_ruta = $archivo;
    
    # Mueve el archivo subido al directorio para streaming
    if(file_exists($ruta_str."/".$archivo))
      unlink($ruta_str."/".$archivo);
    rename($ruta_tmp."/".$archivo, $ruta_str."/".$archivo);
    
    # Convierte archivos .mov a .flv
    if($ext == "MOV" OR $ext == "MP4") {
      $file_mov_lecture = $ruta_str."/".$archivo;
      $file_flv = 'PUBLIC_' . substr($archivo, 0, (strlen($archivo)-4)) . '.flv';
      if(file_exists($ruta_str."/".$file_flv))
        unlink($ruta_str."/".$file_flv);
      $comando_1 = CMD_FFMPEG." -i \"$file_mov_lecture\" $parametros \"$ruta_str/$file_flv\"";
      $ds_vl_ruta = $file_flv;
    }
    
    # Creacion de liga para servidor de streaming
    $comando_2 = "ln -s \"".$ruta_str."/".$ds_vl_ruta."\" ".PATH_LINKS;
    
    $Query  = "INSERT INTO k_flash (fl_contenido, nb_archivo) ";
    $Query .= "VALUES($clave, '$ds_vl_ruta')";
    EjecutaQuery($Query);
  }
  
  
  # Actualiza las tablas del contenido
  EjecutaQuery("DELETE FROM k_tabla WHERE fl_contenido=$clave");
  for($i = 1; $i <= $no_tabla; $i++) {
    if($fl_tabla[$i] > 0)
      EjecutaQuery("INSERT INTO k_tabla (fl_contenido, fl_tabla, no_orden) values($clave, $fl_tabla[$i], $i)");
  }
  
  # Actualiza los anexos
  $max_lado_th = ObtenConfiguracion(11);
  $ancho_thumb = ObtenConfiguracion(10);
  for($i = 0; $i < $regs_ini_anexos; $i++) {
    if(!empty($fl_anexo[$i])) { // Si no fue eliminado
      if(!empty($_FILES['archivo_a_'.$i]['tmp_name'])) {
        $ruta = SP_ANEXOS;
        $nb_archivo = $_FILES['archivo_a_'.$i]['name'];
        move_uploaded_file($_FILES['archivo_a_'.$i]['tmp_name'], $ruta."/".$nb_archivo);
        
        # Revisa si no se especifico una imagen y genera un thumbnail en caso de ser imagen
        if(empty($_FILES['imagen_a_'.$i]['tmp_name'])) {
          $ext = ObtenExtensionArchivo($nb_archivo);
          if(strtoupper($ext) == "JPG") {
            CreaThumb(SP_ANEXOS."/".$nb_archivo, SP_THUMBS."/".$nb_archivo, 0, 0, $max_lado_th);
            $nb_imagen_a[$i] = "";
          }
        }
      }
      else
        $nb_archivo = $nb_archivo_a[$i];
      if(!empty($_FILES['tr_archivo_at_'.$i]['tmp_name'])) {
        $ruta = SP_ANEXOS_EN;
        $tr_archivo = $_FILES['tr_archivo_at_'.$i]['name'];
        move_uploaded_file($_FILES['tr_archivo_at_'.$i]['tmp_name'], $ruta."/".$tr_archivo);
      }
      else
        $tr_archivo = $tr_archivo_a[$i];
      
      # Imagen asociada al anexo
      if(!empty($_FILES['imagen_a_'.$i]['tmp_name'])) {
        $ruta = SP_IMAGES;
        $nb_imagen = $_FILES['imagen_a_'.$i]['name'];
        move_uploaded_file($_FILES['imagen_a_'.$i]['tmp_name'], $ruta."/".$nb_imagen);
        
        # Genera thumbnail para mostrar en listado
        $ext = ObtenExtensionArchivo($nb_imagen);
        if(strtoupper($ext) == "JPG")
          CreaThumb(SP_IMAGES."/".$nb_imagen, SP_THUMBS."/".$nb_imagen, $ancho_thumb, 0, 0);
      }
      else
        $nb_imagen = $nb_imagen_a[$i];
      $Query  = "UPDATE k_anexo SET no_orden=$no_orden_a[$i], ds_caption='$ds_caption_a[$i]', tr_caption='$tr_caption_a[$i]', ";
      $Query .= "nb_archivo='$nb_archivo', tr_archivo='$tr_archivo', ds_texto='$ds_texto_a[$i]', tr_texto='$tr_texto_a[$i]', ";
      $Query .= "nb_imagen='$nb_imagen' ";
      $Query .= "WHERE fl_anexo=$fl_anexo[$i]";
      EjecutaQuery($Query);
    }
  }
  
  # Inserta los nuevos anexos
  for($i = $regs_ini_anexos; $i < $tot_regs_anexos; $i++) {
    if(!empty($ds_caption_a[$i])) { // Si no fue eliminado
      if(!empty($_FILES['archivo_a_'.$i]['tmp_name'])) {
        $ruta = SP_ANEXOS;
        $nb_archivo = $_FILES['archivo_a_'.$i]['name'];
        move_uploaded_file($_FILES['archivo_a_'.$i]['tmp_name'], $ruta."/".$nb_archivo);
        
        # Revisa si no se especifico una imagen y genera un thumbnail en caso de ser imagen
        if(empty($_FILES['imagen_a_'.$i]['tmp_name'])) {
          $ext = ObtenExtensionArchivo($nb_archivo);
          if(strtoupper($ext) == "JPG") {
            CreaThumb(SP_ANEXOS."/".$nb_archivo, SP_THUMBS."/".$nb_archivo, 0, 0, 176);
            $nb_imagen_a[$i] = "";
          }
        }
      }
      else
        $nb_archivo = $nb_archivo_a[$i];
      if(!empty($_FILES['tr_archivo_at_'.$i]['tmp_name'])) {
        $ruta = SP_ANEXOS_EN;
        $tr_archivo = $_FILES['tr_archivo_at_'.$i]['name'];
        move_uploaded_file($_FILES['tr_archivo_at_'.$i]['tmp_name'], $ruta."/".$tr_archivo);
      }
      else
        $tr_archivo = $tr_archivo_a[$i];
      
      # Imagen asociada al anexo
      if(!empty($_FILES['imagen_a_'.$i]['tmp_name'])) {
        $ruta = SP_IMAGES;
        $nb_imagen = $_FILES['imagen_a_'.$i]['name'];
        move_uploaded_file($_FILES['imagen_a_'.$i]['tmp_name'], $ruta."/".$nb_imagen);
        
        # Genera thumbnail para mostrar en listado
        $ext = ObtenExtensionArchivo($nb_imagen);
        if(strtoupper($ext) == "JPG")
          CreaThumb(SP_IMAGES."/".$nb_imagen, SP_THUMBS."/".$nb_imagen, 53, 0, 0);
      }
      else
        $nb_imagen = $nb_imagen_a[$i];
      $Query  = "INSERT INTO k_anexo (fl_contenido, no_orden, ds_caption, tr_caption, nb_archivo, tr_archivo, ds_texto, tr_texto, nb_imagen) ";
      $Query .= "VALUES ($clave, $no_orden_a[$i], '$ds_caption_a[$i]', '$tr_caption_a[$i]', '$nb_archivo', '$tr_archivo', ";
      $Query .= "'$ds_texto_a[$i]', '$tr_texto_a[$i]', '$nb_imagen')";
      EjecutaQuery($Query);
    }
  }
  
	# Borra los anexos que fueron eliminados por el usuario
  if(!empty($regs_borrar_anexos)) {
    $regs_borrar = explode(",", $regs_borrar_anexos);
    $tot_borrar = count($regs_borrar)-1;
    for($i = 0; $i < $tot_borrar; $i++) {
      if(!empty($regs_borrar[$i]))
        EjecutaQuery("DELETE FROM k_anexo WHERE fl_anexo=$regs_borrar[$i]");
    }
  }
  
  # Actualiza las ligas
  EjecutaQuery("DELETE FROM k_liga WHERE fl_contenido=$clave");
  if(!empty($ds_ruta)) {
    EjecutaQuery("INSERT INTO k_liga (fl_contenido, ds_ruta, tr_ruta, fg_ventana) VALUES($clave, '$ds_ruta', '$tr_ruta', '$fg_ventana')");
  }
  elseif(!empty($cl_pagina)) {
    EjecutaQuery("INSERT INTO k_liga (fl_contenido, cl_pagina, fg_ventana) VALUES($clave, $cl_pagina, '$fg_ventana')");
  }
  elseif(!empty($fl_seccion)) {
    EjecutaQuery("INSERT INTO k_liga (fl_contenido, fl_seccion, fg_ventana) VALUES($clave, $fl_seccion, '$fg_ventana')");
  }
  
  # Inserta o actualiza los estados del flujo de trabajo
  if($cambio_nivel OR !empty($ds_nota)) {
    $Query  = "INSERT INTO k_estado_hist (fl_contenido, no_nivel, ds_nivel, tr_nivel, fl_usuario, fe_alta, ds_nota) ";
    $Query .= "VALUES ($clave, $no_nivel, '$ds_nivel', '$tr_nivel', $fl_usuario, CURRENT_TIMESTAMP, '$ds_nota')";
    EjecutaQuery($Query);
  }
	
  # Convierte archivo de video streaming, crea liga y elimina mov
  if(!empty($comando_1))
    exec($comando_1);
  if(!empty($comando_2) AND FG_PRODUCCION)
    exec($comando_2);
  if(!empty($file_mov_lecture))
    unlink($file_mov_lecture);
  
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>