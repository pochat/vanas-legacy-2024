<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  $cl_pagina_nueva = RecibeParametroNumerico('cl_pagina_nueva');
 
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
	# Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_FIXED, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
 
  # Recibe parametros
  $fg_error = 0;
	$nb_pagina = RecibeParametroHTML('nb_pagina');
  $ds_pagina = RecibeParametroHTML('ds_pagina');
  $ds_titulo = RecibeParametroHTML('ds_titulo');
  $tr_titulo = RecibeParametroHTML('tr_titulo');
  $ds_contenido = RecibeParametroHTML('ds_contenido');
  $tr_contenido = RecibeParametroHTML('tr_contenido');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $no_grado = RecibeParametroNumerico('no_grado');
  $archivo = RecibeParametroHTML('archivo');
  $no_videos = RecibeParametroNumerico('no_videos');
  
  
	# Valida campos obligatorios
  if(empty($clave) AND empty($cl_pagina_nueva))
    $cl_pagina_err = ERR_REQUERIDO;
  if(empty($nb_pagina))
    $nb_pagina_err = ERR_REQUERIDO;
   
  # Valida que no exista el registro
  $row = RecuperaValor("SELECT fg_fijo FROM c_pagina WHERE cl_pagina = $cl_pagina_nueva");
  $fg_fijo = $row[0];
  if($fg_fijo == 0)
  {
    $row = EjecutaQuery("SELECT cl_pagina, fl_programa, no_grado FROM c_pagina 
                         WHERE cl_pagina=$cl_pagina_nueva AND fl_programa = $fl_programa AND no_grado = $no_grado");
    if(CuentaRegistros($row) > 0)
      $cl_pagina_err = ERR_DUPVAL2;
  }
  else
  {
    if(empty($clave) AND ExisteEnTabla('c_pagina', 'cl_pagina', $cl_pagina_nueva))
      $cl_pagina_err = ERR_DUPVAL;
  }
  
  # Valida enteros
  if(empty($clave) AND !empty($cl_pagina_nueva) AND !ValidaEntero($cl_pagina_nueva))
    $cl_pagina_err = ERR_ENTERO;
  if(empty($clave) AND !empty($cl_pagina_nueva) AND ($cl_pagina_nueva > MAX_SMALLINT))
    $cl_pagina_err = ERR_SMALLINT;
  
	# Regresa a la forma con error
  $fg_error = $cl_pagina_err || $nb_pagina_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave.'_'.$fl_programa.'_'.$no_grado);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('cl_pagina_nueva' , $cl_pagina_nueva);
    Forma_CampoOculto('cl_pagina_err' , $cl_pagina_err);
    Forma_CampoOculto('nb_pagina' , $nb_pagina);
    Forma_CampoOculto('nb_pagina_err' , $nb_pagina_err);
    Forma_CampoOculto('ds_pagina' , $ds_pagina);
    Forma_CampoOculto('ds_titulo' , $ds_titulo);
    Forma_CampoOculto('tr_titulo' , $tr_titulo);
    Forma_CampoOculto('ds_contenido' , $ds_contenido);
    Forma_CampoOculto('tr_contenido' , $tr_contenido);
    Forma_CampoOculto('fl_programa' , $fl_programa);
    Forma_CampoOculto('no_grado' , $no_grado);
    Forma_CampoOculto('no_videos' , $no_videos);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  
  # Parametros para convertir archivos mov en flv
  $parametros = ObtenConfiguracion(12);
  $ruta_tmp = $_SERVER[DOCUMENT_ROOT].PATH_TMP;
  $ruta_str = PATH_STREAMING;
  
  # Recibe archivo de video
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
      $file_flv = 'CAM_CONTENT_' . substr($archivo, 0, (strlen($archivo)-4)) . '.flv';
      if(file_exists($ruta_str."/".$file_flv))
        unlink($ruta_str."/".$file_flv);
      $comando_1 = CMD_FFMPEG." -i \"$file_mov_lecture\" $parametros \"$ruta_str/$file_flv\"";
      $ds_vl_ruta = $file_flv;
    }
    
    # Creacion de liga para servidor de streaming
    $comando_2 = "ln -s \"".$ruta_str."/".$ds_vl_ruta."\" ".PATH_LINKS;
    
  }
  
  # Actualiza o inserta el registro
  if(!empty($clave)) {
    $Query  = "UPDATE c_pagina SET nb_pagina='$nb_pagina', ds_pagina='$ds_pagina', ds_titulo='$ds_titulo', tr_titulo='$tr_titulo', ";
    $Query .= "ds_contenido='$ds_contenido', tr_contenido='$tr_contenido' ";
    $Query .= "WHERE cl_pagina=$clave ";
    $Query .= "AND fl_programa=$fl_programa ";
    $Query .= "AND no_grado=$no_grado ";
    
    $cl_pagina = $clave;
  }
  else {
    $Query  = "INSERT INTO c_pagina (cl_pagina, nb_pagina, ds_pagina, ds_titulo, tr_titulo, ds_contenido, tr_contenido, fl_programa, no_grado) ";
    $Query .= "VALUES($cl_pagina_nueva, '$nb_pagina', '$ds_pagina', '$ds_titulo', '$tr_titulo', '$ds_contenido', '$tr_contenido', $fl_programa, $no_grado)";
  
    $cl_pagina = $cl_pagina_nueva;
  }
  
  EjecutaQuery($Query);
  #echo "$Query <br>";
  
  #Inserta registros de videos nuevos
  if(!empty($archivo))
  {
    $Query2 = "INSERT INTO k_video_contenido (cl_pagina, fl_programa, no_grado, ds_ruta_video) ";
    $Query2 .= "VALUES($cl_pagina, $fl_programa, $no_grado, '$ds_vl_ruta')";
    EjecutaQuery($Query2);
    #echo "$Query2 <br>";
  }

  # Convierte archivo de video, crea liga y elimina mov
  if(!empty($comando_1))
    exec($comando_1);
  if(!empty($comando_2) AND FG_PRODUCCION)
    exec($comando_2);
  if(!empty($file_mov_lecture))
    unlink($file_mov_lecture);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
 
?>