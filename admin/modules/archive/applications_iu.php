<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
  # Applications archive
  define('FUNC_APP_FRM_AR',123);
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  $fg_archive = RecibeParametroBinario('fg_archive');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_APP_FRM_AR, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_archive = RecibeParametroBinario('fg_archive');
  
  # Actualiza o inserta el registro
  $Query  = "UPDATE c_sesion ";
  $Query .= "SET fg_archive='$fg_archive' ";
  $Query .= "WHERE fl_sesion=$clave";
  EjecutaQuery($Query);
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>