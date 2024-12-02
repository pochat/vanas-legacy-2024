<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(empty($clave)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CONTENIDOS, PERMISO_MODIFICACION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$nb_tipo_contenido = RecibeParametroHTML('nb_tipo_contenido');
	$ds_tipo_contenido = RecibeParametroHTML('ds_tipo_contenido');
  $tr_tipo_contenido = RecibeParametroHTML('tr_tipo_contenido');
  $tot_templates = RecibeParametroNumerico('tot_templates');
  for($i = 0; $i < $tot_templates; $i++)
    $cl_template[$i] = RecibeParametroNumerico('cl_template_'.$i);
  
  # Valida campos obligatorios
  if(empty($nb_tipo_contenido))
    $nb_tipo_contenido_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $nb_tipo_contenido_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('nb_tipo_contenido' , $nb_tipo_contenido);
    Forma_CampoOculto('nb_tipo_contenido_err' , $nb_tipo_contenido_err);
    Forma_CampoOculto('ds_tipo_contenido' , $ds_tipo_contenido);
    Forma_CampoOculto('tr_tipo_contenido' , $tr_tipo_contenido);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # En esta funcion solo se puede actualizar
  $Query  = "UPDATE c_tipo_contenido ";
  $Query .= "SET nb_tipo_contenido='$nb_tipo_contenido', ds_tipo_contenido='$ds_tipo_contenido', tr_tipo_contenido='$tr_tipo_contenido' ";
  $Query .= "WHERE cl_tipo_contenido=$clave";
  EjecutaQuery($Query);
  
  # Reinicializa los templates
  EjecutaQuery("DELETE FROM k_tipo_contenido_template WHERE cl_tipo_contenido=$clave");
  for($i = 0; $i < $tot_templates; $i++) {
    if(!empty($cl_template[$i]))
      EjecutaQuery("INSERT INTO k_tipo_contenido_template (cl_tipo_contenido, cl_template) VALUES($clave, ".$cl_template[$i].")");
  }
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>