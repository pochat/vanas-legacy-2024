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
  if(!ValidaPermiso(FUNC_SECCIONES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$fl_modulo = RecibeParametroNumerico('fl_modulo');
  $nb_funcion = RecibeParametroHTML('nb_funcion');
  $tr_funcion = RecibeParametroHTML('tr_funcion');
  $ds_funcion = RecibeParametroHTML('ds_funcion');
  $fg_menu = RecibeParametroNumerico('fg_menu');
  if(!empty($fg_menu))
    $fg_menu = "1";
  $no_orden = RecibeParametroNumerico('no_orden');
  $cl_tipo_contenido = RecibeParametroNumerico('cl_tipo_contenido');
  $fg_tipo_seguridad = RecibeParametroHTML('fg_tipo_seguridad');
  $fg_multiple = RecibeParametroNumerico('fg_multiple');
  if(!empty($fg_multiple))
    $fg_multiple = "1";
  $fg_tipo_orden = RecibeParametroHTML('fg_tipo_orden');
  $fg_fijo = RecibeParametroNumerico('fg_fijo');
  if(!empty($fg_fijo))
    $fg_fijo = "1";
  $fl_flujo = RecibeParametroNumerico('fl_flujo');
  $nb_flash_default = RecibeParametroHTML('nb_flash_default');
  $tr_flash_default = RecibeParametroHTML('tr_flash_default');
  $nb_submenu = RecibeParametroHTML('nb_submenu');
  $fl_menu = RecibeParametroNumerico('fl_menu');
  $nb_menu = RecibeParametroHTML('nb_menu');
  
  # Valida campos obligatorios
  if(empty($nb_funcion))
    $nb_funcion_err = ERR_REQUERIDO;
  if(empty($nb_menu))
    $nb_menu_err = ERR_REQUERIDO;
  if(empty($nb_submenu))
    $nb_submenu_err = ERR_REQUERIDO;
  
  # Verifica que el submenu seleccionado corresponda al menu
  if(!empty($fl_menu) AND !empty($fl_modulo)) {
    $fl_modulo_padre = $fl_modulo;
    $fl_modulo_base = $fl_modulo_padre;
    while(!empty($fl_modulo_base)) {
      $fl_modulo_padre = $fl_modulo_base;
      $row = RecuperaValor("SELECT fl_modulo_padre FROM c_modulo WHERE fl_modulo=$fl_modulo_padre");
      $fl_modulo_base = $row[0];
    }
    if($fl_menu <> $fl_modulo_padre)
      $nb_submenu_err = 104; // El submenu no corresponde al menu seleccionado.
  }
  
	# Valida enteros
  if(!ValidaEntero($no_orden))
    $no_orden_err = ERR_ENTERO;
  if($no_orden > MAX_TINYINT)
    $no_orden_err = ERR_TINYINT;
  
	# Regresa a la forma con error
  $fg_error = $nb_funcion_err || $nb_menu_err || $nb_submenu_err || $no_orden_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('nb_funcion' , $nb_funcion);
    Forma_CampoOculto('nb_funcion_err' , $nb_funcion_err);
    Forma_CampoOculto('tr_funcion' , $tr_funcion);
    Forma_CampoOculto('ds_funcion' , $ds_funcion);
    Forma_CampoOculto('fl_modulo' , $fl_modulo);
    Forma_CampoOculto('fg_menu' , $fg_menu);
    Forma_CampoOculto('no_orden' , $no_orden);
    Forma_CampoOculto('no_orden_err' , $no_orden_err);
    Forma_CampoOculto('cl_tipo_contenido' , $cl_tipo_contenido);
    Forma_CampoOculto('fg_tipo_seguridad' , $fg_tipo_seguridad);
    Forma_CampoOculto('fg_multiple' , $fg_multiple);
    Forma_CampoOculto('fg_tipo_orden' , $fg_tipo_orden);
    Forma_CampoOculto('fg_fijo' , $fg_fijo);
    Forma_CampoOculto('fl_flujo' , $fl_flujo);
    Forma_CampoOculto('nb_flash_default' , $nb_flash_default);
    Forma_CampoOculto('tr_flash_default' , $tr_flash_default);
    Forma_CampoOculto('nb_submenu' , $nb_submenu);
    Forma_CampoOculto('nb_submenu_err' , $nb_submenu_err);
    Forma_CampoOculto('fl_menu' , $fl_menu);
    Forma_CampoOculto('nb_menu' , $nb_menu);
    Forma_CampoOculto('nb_menu_err' , $nb_menu_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Recibe archivo flash default para la seccion
  if(!empty($_FILES['archivo_f']['tmp_name'])) {
    $ruta = $_SERVER[DOCUMENT_ROOT].SP_FLASH;
    $nb_archivo = $_FILES['archivo_f']['name'];
    move_uploaded_file($_FILES['archivo_f']['tmp_name'], $ruta."/".$nb_archivo);
  }
  else
    $nb_archivo = $nb_flash_default;
  
  # Recibe archivo flash default para la seccion
  if(!empty($_FILES['tr_archivo_ft']['tmp_name'])) {
    $ruta = $_SERVER[DOCUMENT_ROOT].SP_FLASH;
    $tr_archivo = $_FILES['tr_archivo_ft']['name'];
    move_uploaded_file($_FILES['tr_archivo_ft']['tmp_name'], $ruta."/".$tr_archivo);
  }
  else
    $tr_archivo = $tr_flash_default;
  
  # Actualiza o inserta el registro
  if(!empty($clave)) {
    $Query  = "UPDATE c_funcion ";
    $Query .= "SET fl_modulo=$fl_modulo, nb_funcion='$nb_funcion', tr_funcion='$tr_funcion', ds_funcion='$ds_funcion', ";
    $Query .= "fg_menu='$fg_menu', no_orden=$no_orden, cl_tipo_contenido=$cl_tipo_contenido, fg_tipo_seguridad='$fg_tipo_seguridad', ";
    $Query .= "fg_multiple='$fg_multiple', fg_tipo_orden='$fg_tipo_orden', fl_flujo=$fl_flujo, nb_flash_default='$nb_archivo', ";
    $Query .= "tr_flash_default='$tr_archivo' ";
    $Query .= "WHERE fl_funcion = $clave";
  }
  else {
    $Query  = "INSERT INTO c_funcion (fl_modulo, nb_funcion, tr_funcion, ds_funcion, fg_menu, no_orden, cl_tipo_contenido, ";
    $Query .= "fg_tipo_seguridad, fg_multiple, fg_tipo_orden, fl_flujo, nb_flash_default, tr_flash_default) ";
    $Query .= "VALUES($fl_modulo, '$nb_funcion', '$tr_funcion', '$ds_funcion', '$fg_menu', $no_orden, $cl_tipo_contenido, ";
    $Query .= "'$fg_tipo_seguridad', '$fg_multiple', '$fg_tipo_orden', $fl_flujo, '$nb_archivo', '$tr_archivo')";
	}
	EjecutaQuery($Query);
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>