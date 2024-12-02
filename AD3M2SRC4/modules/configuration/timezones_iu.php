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
  if(!ValidaPermiso(FUNC_ZONAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
  $nb_zona_horaria = RecibeParametroHTML('nb_zona_horaria');
  $no_gmt = RecibeParametroHTML('no_gmt');
  $fg_default = RecibeParametroBinario('fg_default');
  $no_latitude = RecibeParametroFlotante('no_latitude');
  $fg_latitude = RecibeParametroHTML('fg_latitude');
  $no_longitude = RecibeParametroFlotante('no_longitude');
  $fg_longitude = RecibeParametroHTML('fg_longitude');
  
  # Valida campos obligatorios
  if(empty($nb_zona_horaria))
    $nb_zona_horaria_err = ERR_REQUERIDO;
  
  # Valida enteros
  //if(!ValidaFlotante($no_gmt))
    //$no_gmt_err = ERR_ENTERO;
  
  
  # Regresa a la forma con error
  $fg_error = $nb_zona_horaria_err || $no_gmt_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('nb_zona_horaria', $nb_zona_horaria);
    Forma_CampoOculto('nb_zona_horaria_err', $nb_zona_horaria_err);
    Forma_CampoOculto('no_gmt', $no_gmt);
    Forma_CampoOculto('no_gmt_err', $no_gmt_err);
    Forma_CampoOculto('fg_default', $fg_default);
    Forma_CampoOculto('no_latitude', $no_latitude);
    Forma_CampoOculto('fg_latitude', $fg_latitude);
    Forma_CampoOculto('no_longitude', $no_longitude);
    Forma_CampoOculto('fg_longitude', $fg_longitude);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Marca el registro como default
  if($fg_default == '1')
    EjecutaQuery("UPDATE c_zona_horaria SET fg_default='0'");
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_zona_horaria (nb_zona_horaria, no_gmt, fg_default, no_latitude, fg_latitude, no_longitude, fg_longitude) ";
    $Query .= "VALUES('$nb_zona_horaria', $no_gmt, '$fg_default')";
  }
  else {
    $Query  = "UPDATE c_zona_horaria SET nb_zona_horaria='$nb_zona_horaria', no_gmt=$no_gmt, fg_default='$fg_default', ";
    $Query .= "no_latitude=$no_latitude, fg_latitude='$fg_latitude', no_longitude=$no_longitude, fg_longitude='$fg_longitude' ";
    $Query .= "WHERE fl_zona_horaria=$clave";
  }
  EjecutaQuery($Query);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>