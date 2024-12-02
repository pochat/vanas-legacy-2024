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
  if(!ValidaPermiso(FUNC_TEMPLATES, PERMISO_MODIFICACION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$nb_template = RecibeParametroHTML('nb_template');
	$ds_template = RecibeParametroHTML('ds_template');
  $tr_template = RecibeParametroHTML('tr_template');
  $fg_titulo = RecibeParametroNumerico('fg_titulo');
  $fg_resumen = RecibeParametroNumerico('fg_resumen');
  $fg_fecha_evento = RecibeParametroNumerico('fg_fecha_evento');
  $no_texto = RecibeParametroNumerico('no_texto');
  $no_imagen_dinamica = RecibeParametroNumerico('no_imagen_dinamica');
  $no_flash = RecibeParametroNumerico('no_flash');
  $no_tabla = RecibeParametroNumerico('no_tabla');
  $fg_anexo = RecibeParametroNumerico('fg_anexo');
  
  # Valida campos obligatorios
  if(empty($nb_template))
    $nb_template_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $nb_template_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('nb_template' , $nb_template);
    Forma_CampoOculto('nb_template_err' , $nb_template_err);
    Forma_CampoOculto('ds_template' , $ds_template);
    Forma_CampoOculto('tr_template' , $tr_template);
    Forma_CampoOculto('fg_titulo' , $fg_titulo);
    Forma_CampoOculto('fg_resumen' , $fg_resumen);
    Forma_CampoOculto('fg_fecha_evento' , $fg_fecha_evento);
    Forma_CampoOculto('no_texto' , $no_texto);
    Forma_CampoOculto('no_imagen_dinamica' , $no_imagen_dinamica);
    Forma_CampoOculto('no_flash' , $no_flash);
    Forma_CampoOculto('no_tabla' , $no_tabla);
    Forma_CampoOculto('fg_anexo' , $fg_anexo);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # En esta funcion solo se puede actualizar
  $Query  = "UPDATE c_template ";
  $Query .= "SET nb_template='$nb_template', ds_template='$ds_template', tr_template='$tr_template' ";
  $Query .= "WHERE cl_template=$clave";
  EjecutaQuery($Query);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>