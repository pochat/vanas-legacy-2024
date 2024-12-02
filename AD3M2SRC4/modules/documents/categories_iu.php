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
  if(!ValidaPermiso(FUNC_CATEGORIAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
  $nb_categoria = RecibeParametroHTML('nb_categoria');
  $ds_categoria = RecibeParametroHTML('ds_categoria');
  
  # Valida campos obligatorios
  if(empty($nb_categoria))
    $nb_categoria_err = ERR_REQUERIDO;
  if(empty($ds_categoria))
    $ds_categoria_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
   $fg_error = $nb_categoria_err || $ds_categoria_err;
  if($fg_error) { 
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('nb_categoria' , $nb_categoria);
    Forma_CampoOculto('nb_categoria_err' , $nb_categoria_err);
    Forma_CampoOculto('ds_categoria' , $ds_categoria);
    Forma_CampoOculto('ds_categoria_err' , $ds_categoria_err);
    Forma_CampoOculto('fg_error' , $fg_error);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza o inserta el registro
  if(!empty($clave)) {
    $Query  = "UPDATE c_categoria_doc ";
    $Query .= "SET nb_categoria='$nb_categoria', ds_categoria='$ds_categoria' ";
    $Query .= "WHERE fl_categoria=$clave";
  }
  else {
    $Query  = "INSERT INTO c_categoria_doc (nb_categoria, ds_categoria) ";
    $Query .= "VALUES('$nb_categoria', '$ds_categoria')";
	}
	EjecutaQuery($Query);
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>