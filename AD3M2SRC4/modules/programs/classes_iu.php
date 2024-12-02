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
  if(!ValidaPermiso(FUNC_CLASSES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $nb_class = RecibeParametroHTML('nb_class');
  $ds_class = RecibeParametroHTML('ds_class');
  $no_grado = RecibeParametroNumerico('no_grado');
  $no_orden = RecibeParametroNumerico('no_orden');
  
  # Valida campos obligatorios
  if(empty($fl_programa))
    $fl_programa_err = ERR_REQUERIDO;
  if(empty($nb_class))
    $nb_class_err = ERR_REQUERIDO;
  if(empty($ds_class))
    $ds_class_err = ERR_REQUERIDO;
  if(empty($no_grado))
    $no_grado_err = ERR_REQUERIDO;

  # Valida enteros
  if(empty($no_grado_err) AND $no_grado > MAX_TINYINT)
    $no_grado_err = ERR_TINYINT;
  if($no_orden > MAX_TINYINT)
    $no_orden_err = ERR_TINYINT;
  
  # Regresa a la forma con error
  $fg_error = $fl_programa_err || $nb_class_err || $ds_class_err || $no_grado_err || $no_orden_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('fl_programa' , $fl_programa);
    Forma_CampoOculto('fl_programa_err' , $fl_programa_err);
    Forma_CampoOculto('nb_class' , $nb_class);
    Forma_CampoOculto('nb_class_err' , $nb_class_err);
    Forma_CampoOculto('ds_class' , $ds_class);
    Forma_CampoOculto('ds_class_err' , $ds_class_err);
    Forma_CampoOculto('no_grado' , $no_grado);
    Forma_CampoOculto('no_grado_err' , $no_grado_err);
    Forma_CampoOculto('no_orden' , $no_orden);
    Forma_CampoOculto('no_orden_err' , $no_orden_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_class (fl_programa, nb_class, ds_class, no_grado, no_orden) ";
    $Query .= "VALUES($fl_programa, '$nb_class', '$ds_class', $no_grado, $no_orden) ";
  }
  else {
    $Query  = "UPDATE c_class SET fl_programa=$fl_programa, nb_class='$nb_class', ";
    $Query .= "ds_class='$ds_class', no_grado=$no_grado, no_orden=$no_orden ";
    $Query .= "WHERE fl_class=$clave";
  }
  EjecutaQuery($Query);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>