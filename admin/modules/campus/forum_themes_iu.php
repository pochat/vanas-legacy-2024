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
  if(!ValidaPermiso(FUNC_FORO, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
  $nb_tema = RecibeParametroHTML('nb_tema');
  $fg_tipo = RecibeParametroHTML('fg_tipo');
  $no_orden = RecibeParametroNumerico('no_orden');
  $ds_ruta_imagen = RecibeParametroHTML('ds_ruta_imagen');
  $no_posts = RecibeParametroNumerico('no_posts');
   
  # Valida campos obligatorios
  if(empty($nb_tema))
    $nb_tema_err = ERR_REQUERIDO;
  if(empty($no_orden))
    $no_orden_err = ERR_REQUERIDO;
   
	# Valida enteros
  if(!ValidaEntero($no_orden))
    $no_orden_err = ERR_ENTERO;
  if($no_orden > MAX_TINYINT)
    $no_orden_err = ERR_TINYINT;
  
	# Regresa a la forma con error
  $fg_error = $nb_tema_err || $no_orden_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('nb_tema' , $nb_tema);
    Forma_CampoOculto('nb_tema_err' , $nb_tema_err);
    Forma_CampoOculto('fg_tipo' , $fg_tipo);
    Forma_CampoOculto('no_orden' , $no_orden);
    Forma_CampoOculto('no_orden_err' , $no_orden_err);
    Forma_CampoOculto('ds_ruta_imagen' , $ds_ruta_imagen);
    Forma_CampoOculto('no_posts' , $no_posts);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
   
  # Recibe archivo de imagen para icono en el menu de la seccion
  if(!empty($_FILES['archivo_f']['tmp_name'])) {
    $ruta = SP_IMAGES;
    $ds_ruta_imagen = $_FILES['archivo_f']['name'];
    move_uploaded_file($_FILES['archivo_f']['tmp_name'], $ruta."/".$ds_ruta_imagen);
  }
  
  # Actualiza o inserta el registro
  if(!empty($clave)) {
    $Query  = "UPDATE c_f_tema ";
    $Query .= "SET nb_tema='$nb_tema', fg_tipo='$fg_tipo', no_orden=$no_orden, ds_ruta_imagen='$ds_ruta_imagen' ";
    $Query .= "WHERE fl_tema = $clave";
  }
  else {
    $Query  = "INSERT INTO c_f_tema (nb_tema, fg_tipo, no_orden, ds_ruta_imagen) ";
    $Query .= "VALUES('$nb_tema', '$fg_tipo', $no_orden, '$ds_ruta_imagen')";
	}
	EjecutaQuery($Query);
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>