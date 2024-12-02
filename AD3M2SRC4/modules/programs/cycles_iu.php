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
  if(!ValidaPermiso(FUNC_CICLOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$nb_periodo = RecibeParametroHTML('nb_periodo');
  $fe_inicio = RecibeParametroFecha('fe_inicio');
  $fg_activo = RecibeParametroNumerico('fg_activo');
  
  # Valida campos obligatorios
  if(empty($nb_periodo))
    $nb_periodo_err = ERR_REQUERIDO;
  if(empty($fe_inicio))
    $fe_inicio_err = ERR_REQUERIDO;
  
  # Verifica que el formato de la fecha sea valido
  if(!empty($fe_inicio) AND !ValidaFecha($fe_inicio))
    $fe_inicio_err = ERR_FORMATO_FECHA;
  
	# Regresa a la forma con error
  $fg_error = $nb_periodo_err || $fe_inicio_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('nb_periodo', $nb_periodo);
    Forma_CampoOculto('nb_periodo_err', $nb_periodo_err);
    Forma_CampoOculto('fe_inicio', $fe_inicio);
    Forma_CampoOculto('fe_inicio_err', $fe_inicio_err);
    Forma_CampoOculto('fg_activo', $fg_activo);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Prepara fechas en formato para insertar
  if(!empty($fe_inicio))
    $fe_inicio = "'".ValidaFecha($fe_inicio)."'";
  else
    $fe_inicio = "NULL";
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_periodo (nb_periodo, fe_inicio, fg_activo) ";
    $Query .= "VALUES('$nb_periodo', $fe_inicio, '$fg_activo') ";
  }
  else {
    $Query  = "UPDATE c_periodo SET nb_periodo='$nb_periodo', fe_inicio=$fe_inicio, fg_activo='$fg_activo' ";
    $Query .= "WHERE fl_periodo=$clave";
  }
  EjecutaQuery($Query);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase());
  
?>