<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($cl_etiqueta))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ETIQUETAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $replacements = array(
    "'"=>"\'",
    '"'=>'\"'
  );
  $fg_error = 0;
  $id=RecibeParametroNumerico('id');
  $host_email_zoom = RecibeParametroHTML('host_email_zoom');
  $host_id = RecibeParametroHTML('host_id');
  $client_id_zoom = RecibeParametroHTML('client_id_zoom');
  $client_secret_zoom = RecibeParametroHTML('client_secret_zoom');
  $fg_activo = RecibeParametroBinario('fg_activo');

  # Valida campos obligatorios
  if(empty($host_email_zoom))
    $host_email_zoom_err = ERR_REQUERIDO;
  
  
	# Regresa a la forma con error
  $fg_error = (!empty($host_email_zoom_err)?$host_email_zoom_err:NULL);
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('host_email_zoom' , $host_email_zoom);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('host_email_zoom_err' , $host_email_zoom_err);
    Forma_CampoOculto('host_id' , $host_id);
    Forma_CampoOculto('client_id_zoom' , $client_id_zoom);
    Forma_CampoOculto('client_secret_zoom' , $client_secret_zoom);
    Forma_CampoOculto('fg_activo' , $fg_activo);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza o inserta el registro
  if(!empty($clave)) {
    $Query  = "UPDATE zoom ";
    $Query .= "SET host_email_zoom='$host_email_zoom', host_id='$host_id', client_id_zoom='$client_id_zoom', client_secret_zoom='$client_secret_zoom', fg_activo='$fg_activo' ";
    $Query .= "WHERE id = $clave ";
  }
  else {
      $Query  = "INSERT INTO zoom (id, host_email_zoom, host_id, client_id_zoom, client_secret_zoom, fg_activo) ";
      $Query .= "VALUES($id, '$host_email_zoom', '$host_id', '$client_id_zoom', '$client_secret_zoom', '$fg_activo')";
  }
  EjecutaQuery($Query);


	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>