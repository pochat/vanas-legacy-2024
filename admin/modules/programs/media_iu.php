<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MEDIA, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $no_grado = RecibeParametroNumerico('no_grado');
  $no_semana = RecibeParametroNumerico('no_semana');
  $ds_titulo = RecibeParametroHTML('ds_titulo');
  $ds_leccion = RecibeParametroHTML('ds_leccion');
  $ds_vl_ruta = RecibeParametroHTML('ds_vl_ruta');
  $ds_vl_duracion = RecibeParametroHTML('ds_vl_duracion');
  $fe_vl_alta = RecibeParametroHTML('fe_vl_alta');
  $fg_animacion = RecibeParametroBinario('fg_animacion');
  $fg_ref_animacion = RecibeParametroBinario('fg_ref_animacion');
  $no_sketch = RecibeParametroNumerico('no_sketch');
  $fg_ref_sketch = RecibeParametroBinario('fg_ref_sketch');
  $archivo = RecibeParametroHTML('archivo');
  $archivo_a = RecibeParametroHTML('archivo_a');
  $ds_as_ruta = RecibeParametroHTML('ds_as_ruta');
  $ds_as_duracion = RecibeParametroHTML('ds_as_duracion');
  $fe_as_alta = RecibeParametroHTML('fe_as_alta');
  $archivo1 = RecibeParametroHTML('archivo1');
  $archivo1_a = RecibeParametroHTML('archivo1_a');
  
  # Valida campos obligatorios
  if(empty($fl_programa))
    $fl_programa_err = ERR_REQUERIDO;
  if(empty($no_grado))
    $no_grado_err = ERR_REQUERIDO;
  if(empty($no_semana))
    $no_semana_err = ERR_REQUERIDO;
  if(empty($ds_titulo))
    $ds_titulo_err = ERR_REQUERIDO;
  if(empty($ds_leccion))
    $ds_leccion_err = ERR_REQUERIDO;
  
  # Valida enteros
  if(empty($no_grado_err) AND !ValidaEntero($no_grado))
    $no_grado_err = ERR_ENTERO;
  if(empty($no_grado_err) AND $no_grado > MAX_TINYINT)
    $no_grado_err = ERR_TINYINT;
  if(empty($no_semana_err) AND !ValidaEntero($no_semana))
    $no_semana_err = ERR_ENTERO;
  if(empty($no_semana_err) AND $no_semana > MAX_TINYINT)
    $no_semana_err = ERR_TINYINT;
  if($no_sketch > MAX_TINYINT)
    $no_sketch_err = ERR_TINYINT;
  
  # Verifica que no exista la leccion
  if(empty($fl_programa_err) AND empty($no_grado_err) AND empty($no_semana_err)) {
    $Query  = "SELECT count(1) ";
    $Query .= "FROM c_leccion ";
    $Query .= "WHERE fl_programa=$fl_programa ";
    $Query .= "AND no_grado=$no_grado ";
    $Query .= "AND no_semana=$no_semana ";
    if(!empty($clave))
      $Query .= "AND fl_leccion<>$clave";
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      $no_semana_err = 109; # Existing lesson found for this program.
  }
  
  
  # Regresa a la forma con error
  $fg_error = $fl_programa_err || $no_grado_err || $no_semana_err || $ds_titulo_err || $ds_leccion_err || $no_sketch_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('fl_programa' , $fl_programa);
    Forma_CampoOculto('fl_programa_err' , $fl_programa_err);
    Forma_CampoOculto('no_grado' , $no_grado);
    Forma_CampoOculto('no_grado_err' , $no_grado_err);
    Forma_CampoOculto('no_semana' , $no_semana);
    Forma_CampoOculto('no_semana_err' , $no_semana_err);
    Forma_CampoOculto('ds_titulo' , $ds_titulo);
    Forma_CampoOculto('ds_titulo_err' , $ds_titulo_err);
    Forma_CampoOculto('ds_leccion' , $ds_leccion);
    Forma_CampoOculto('ds_leccion_err' , $ds_leccion_err);
    Forma_CampoOculto('ds_vl_ruta' , $ds_vl_ruta);
    Forma_CampoOculto('ds_vl_duracion' , $ds_vl_duracion);
    Forma_CampoOculto('fe_vl_alta' , $fe_vl_alta);
    Forma_CampoOculto('fg_animacion' , $fg_animacion);
    Forma_CampoOculto('fg_ref_animacion' , $fg_ref_animacion);
    Forma_CampoOculto('no_sketch' , $no_sketch);
    Forma_CampoOculto('no_sketch_err' , $no_sketch_err);
    Forma_CampoOculto('fg_ref_sketch' , $fg_ref_sketch);
    Forma_CampoOculto('ds_as_ruta' , $ds_as_ruta);
    Forma_CampoOculto('ds_as_duracion' , $ds_as_duracion);
    Forma_CampoOculto('fe_as_alta' , $fe_as_alta);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Inicializa variables para procesamiento de archivos
  $parametros = ObtenConfiguracion(12); // Para convertir archivos mov en flv
  $ruta_tmp = $_SERVER[DOCUMENT_ROOT].PATH_TMP;
  $ruta = PATH_STREAMING;
  
  # Recibe archivo de lecture
  if(!empty($archivo)) {
    $ds_vl_ruta = $archivo;
    
    # Mueve el archivo subido al directorio para streaming
    if(file_exists($ruta."/".$archivo))
      unlink($ruta."/".$archivo);
    rename($ruta_tmp."/".$archivo, $ruta."/".$archivo);
    
    # Creacion de liga para servidor de streaming
    $comando_2 = "ln -s \"".$ruta."/".$ds_vl_ruta."\" ".PATH_LINKS;
    
    # Prepara la fecha de alta del archivo
    $fe_vl_alta = "CURRENT_TIMESTAMP";
  }
  else {
    if(!empty($archivo_a))
    {
      $row = RecuperaValor("SELECT ds_vl_ruta FROM c_leccion WHERE fl_leccion = $archivo_a");
      $ds_vl_ruta = $row[0];
    }
    if(empty($ds_vl_ruta)) {
      $ds_vl_duracion = "";
      $fe_vl_alta = "NULL";
    }
    else
      $fe_vl_alta = "fe_vl_alta";
  }
  
  # Recibe archivo de brief
  if(!empty($_FILES['archivo1']['tmp_name'])) {
    $ds_as_ruta = $_FILES['archivo1']['name'];
    
    # Mueve el archivo subido al directorio para streaming
    if(file_exists($ruta."/".$ds_as_ruta))
      unlink($ruta."/".$ds_as_ruta);
    move_uploaded_file($_FILES['archivo1']['tmp_name'], $ruta."/".$ds_as_ruta);
    
    # Creacion de liga para servidor de streaming
    $comando_4 = "ln -s \"".$ruta."/".$ds_as_ruta."\" ".PATH_LINKS;
    
    # Prepara la fecha de alta del archivo
    $fe_as_alta = "CURRENT_TIMESTAMP";
  }
  else {
    if(!empty($archivo1_a))
    {
      $row1 = RecuperaValor("SELECT ds_as_ruta FROM c_leccion WHERE fl_leccion = $archivo1_a");
      $ds_as_ruta = $row1[0];
    }
    if(empty($ds_as_ruta)) {
      $ds_as_duracion = "";
      $fe_as_alta = "NULL";
    }
    else
      $fe_as_alta = "fe_as_alta";
  }
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_leccion (fl_programa, no_grado, no_semana, ds_titulo, ds_leccion, ds_vl_ruta, ds_vl_duracion, fe_vl_alta, ";
    $Query .= "ds_as_ruta, ds_as_duracion, fe_as_alta, fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch) ";
    $Query .= "VALUES($fl_programa, $no_grado, $no_semana, '$ds_titulo', '$ds_leccion', '$ds_vl_ruta', '$ds_vl_duracion', $fe_vl_alta, ";
    $Query .= "'$ds_as_ruta', '$ds_as_duracion', $fe_as_alta, '$fg_animacion', '$fg_ref_animacion', $no_sketch, '$fg_ref_sketch') ";
  }
  else {
    $Query  = "UPDATE c_leccion SET fl_programa=$fl_programa, no_grado=$no_grado, no_semana=$no_semana, ds_titulo='$ds_titulo', ";
    $Query .= "ds_leccion='$ds_leccion', ds_vl_ruta='$ds_vl_ruta', ds_vl_duracion='$ds_vl_duracion', fe_vl_alta=$fe_vl_alta, ";
    $Query .= "ds_as_ruta='$ds_as_ruta', ds_as_duracion='$ds_as_duracion', fe_as_alta=$fe_as_alta, ";
    $Query .= "fg_animacion='$fg_animacion', fg_ref_animacion='$fg_ref_animacion', no_sketch=$no_sketch, fg_ref_sketch='$fg_ref_sketch' ";
    $Query .= "WHERE fl_leccion=$clave";
  }
  EjecutaQuery($Query);
  
  # Crea liga archivo 1
  if(!empty($comando_2) AND FG_PRODUCCION)
    exec($comando_2);
  
  # Crea liga archivo 2
  if(!empty($comando_4) AND FG_PRODUCCION)
    exec($comando_4);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>