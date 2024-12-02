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
  if(!ValidaPermiso(FUNC_CORREOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_area = RecibeParametroHTML('ds_area');
  $tr_area = RecibeParametroHTML('tr_area');
  $ds_email = RecibeParametroHTML('ds_email');
  $no_orden = RecibeParametroNumerico('no_orden');
  $fg_anexo = RecibeParametroNumerico('fg_anexo');
  if(!empty($fg_anexo))
    $fg_anexo = "1";
  $ds_etq_anexo = RecibeParametroHTML('ds_etq_anexo');
  $tr_etq_anexo = RecibeParametroHTML('tr_etq_anexo');
  
  # Valida campos obligatorios
  if(empty($ds_area))
    $ds_area_err = ERR_REQUERIDO;
  if(empty($ds_email))
    $ds_email_err = ERR_REQUERIDO;
  if($fg_anexo == "1" AND empty($ds_etq_anexo))
    $ds_etq_anexo_err = ERR_REQUERIDO;
  
  # Valida enteros
  if(!ValidaEntero($no_orden))
    $no_orden_err = ERR_ENTERO;
  if($no_orden > MAX_TINYINT)
    $no_orden_err = ERR_TINYINT;
  
	#Verifica que el formato del email sea valido
  // No se valida el formato del correo para poder poner varias direcciones separadas por coma
  //if(!empty($ds_email) AND !ValidaEmail($ds_email))
  //  $ds_email_err = ERR_FORMATO_EMAIL;
  
  # Regresa a la forma con error
  $fg_error = $ds_area_err || $ds_email_err || $ds_etq_anexo_err || $no_orden_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_area' , $ds_area);
    Forma_CampoOculto('ds_area_err' , $ds_area_err);
    Forma_CampoOculto('tr_area' , $tr_area);
    Forma_CampoOculto('ds_email' , $ds_email);
    Forma_CampoOculto('ds_email_err' , $ds_email_err);
    Forma_CampoOculto('no_orden' , $no_orden);
    Forma_CampoOculto('no_orden_err' , $no_orden_err);
    Forma_CampoOculto('fg_anexo' , $fg_anexo);
    Forma_CampoOculto('ds_etq_anexo' , $ds_etq_anexo);
    Forma_CampoOculto('ds_etq_anexo_err' , $ds_etq_anexo_err);
    Forma_CampoOculto('tr_etq_anexo' , $tr_etq_anexo);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza o inserta el registro
  if(!empty($clave)) {
    $Query  = "UPDATE c_contacto ";
    $Query .= "SET ds_area='$ds_area', tr_area='$tr_area', ds_email='$ds_email', no_orden=$no_orden, fg_anexo='$fg_anexo', ";
    $Query .= "ds_etq_anexo='$ds_etq_anexo', tr_etq_anexo='$tr_etq_anexo' ";
    $Query .= "WHERE fl_contacto=$clave";
  }
  else {
    $Query  = "INSERT INTO c_contacto (ds_area, tr_area, ds_email, no_orden, fg_anexo, ds_etq_anexo, tr_etq_anexo) ";
    $Query .= "VALUES('$ds_area', '$tr_area', '$ds_email', $no_orden, '$fg_anexo', '$ds_etq_anexo', '$tr_etq_anexo')";
	}
	EjecutaQuery($Query);
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>