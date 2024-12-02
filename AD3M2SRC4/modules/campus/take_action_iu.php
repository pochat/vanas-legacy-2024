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
  if(!ValidaPermiso(FUNC_TAKE, $permiso) OR $permiso == PERMISO_ALTA) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_login = RecibeParametroHTML('ds_login');
  $fg_activo = RecibeParametroBinario('fg_activo');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $fl_periodo = RecibeParametroNumerico('fl_periodo');
  $fg_desercion = RecibeParametroBinario('fg_desercion');
  $fg_dismissed = RecibeParametroBinario('fg_dismissed');
  $fg_job = RecibeParametroBinario('fg_job');
  $fg_graduacion = RecibeParametroBinario('fg_graduacion');
  $fg_certificado = RecibeParametroBinario('fg_certificado');
  $fg_honores = RecibeParametroBinario('fg_honores');
  $ds_notas = RecibeParametroHTML('ds_notas');
  
    
  # Verifica si se esta actualizando los datos
  if(!empty($clave)) {    
    # Actualiza los datos del usuario
    $Query  = "UPDATE c_usuario SET fg_activo='$fg_activo' ";
    $Query .= "WHERE fl_usuario=$clave";
    EjecutaQuery($Query);

    # Actualiza los datos de k_pctia
    $Query  = "UPDATE k_pctia ";
    $Query .= "SET ";
    $Query .= "fg_certificado='$fg_certificado', fg_honores='$fg_honores', fg_desercion='$fg_desercion', fg_dismissed='$fg_dismissed', fg_job='$fg_job', fg_graduacion='$fg_graduacion' ";
    $Query .= "WHERE fl_alumno=$clave ";
    $Query .= "AND fl_programa=$fl_programa";
    EjecutaQuery($Query);
    
    # Actualiza el registro del alumno
    $Query  = "UPDATE c_alumno ";
    $Query .= "SET ds_notas='$ds_notas' ";
    $Query .= "WHERE fl_alumno='$clave'";
    EjecutaQuery($Query);
    
  }
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>