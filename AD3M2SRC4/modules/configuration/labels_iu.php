<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $cl_etiqueta = RecibeParametroNumerico('clave');
  $cl_etiqueta_nueva = RecibeParametroNumerico('cl_etiqueta_nueva');
  
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
	$nb_etiqueta = RecibeParametroHTML('nb_etiqueta');
	$ds_etiqueta = $_POST['ds_etiqueta'];
  $ds_etiqueta_esp = str_replace(array_keys($replacements), $replacements, $_POST["ds_etiqueta_esp"]);
  $ds_etiqueta_fra = str_replace(array_keys($replacements), $replacements, $_POST["ds_etiqueta_fra"]);
  $tr_etiqueta = isset($_POST['tr_etiqueta'])?$_POST['tr_etiqueta']:NULL;
  
  # Valida campos obligatorios
  if(empty($cl_etiqueta) AND empty($cl_etiqueta_nueva))
    $cl_etiqueta_err = ERR_REQUERIDO;
  if(empty($nb_etiqueta))
    $nb_etiqueta_err = ERR_REQUERIDO;
  if(empty($ds_etiqueta))
    $ds_etiqueta_err = ERR_REQUERIDO;
  
  # Valida que no exista el registro
  if(empty($cl_etiqueta) AND ExisteEnTabla('c_etiqueta', 'cl_etiqueta', $cl_etiqueta_nueva))
    $cl_etiqueta_err = ERR_DUPVAL;
  
  # Valida enteros
  if(empty($cl_etiqueta) AND !empty($cl_etiqueta_nueva) AND !ValidaEntero($cl_etiqueta_nueva))
    $cl_etiqueta_err = ERR_ENTERO;
  if(empty($cl_etiqueta) AND !empty($cl_etiqueta_nueva) AND ($cl_etiqueta_nueva > MAX_SMALLINT))
    $cl_etiqueta_err = ERR_SMALLINT;
  
	# Regresa a la forma con error
  $fg_error = (!empty($cl_etiqueta_err)?$cl_etiqueta_err:NULL) || (!empty($nb_etiqueta_err)?$nb_etiqueta_err:NULL) || (!empty($ds_etiqueta_err)?$ds_etiqueta_err:NULL);
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $cl_etiqueta);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('cl_etiqueta_nueva' , $cl_etiqueta_nueva);
    Forma_CampoOculto('cl_etiqueta_err' , $cl_etiqueta_err);
    Forma_CampoOculto('nb_etiqueta' , $nb_etiqueta);
    Forma_CampoOculto('nb_etiqueta_err' , $nb_etiqueta_err);
    Forma_CampoOculto('ds_etiqueta' , $ds_etiqueta);
    Forma_CampoOculto('ds_etiqueta_err' , $ds_etiqueta_err);
    Forma_CampoOculto('tr_etiqueta' , $tr_etiqueta);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza o inserta el registro
  if(!empty($cl_etiqueta)) {
    $Query  = "UPDATE c_etiqueta ";
    $Query .= "SET nb_etiqueta='$nb_etiqueta', ds_etiqueta='$ds_etiqueta', ds_etiqueta_esp='".$ds_etiqueta_esp."', ds_etiqueta_fra='".$ds_etiqueta_fra."', tr_etiqueta='$tr_etiqueta' ";
    $Query .= "WHERE cl_etiqueta = $cl_etiqueta";
  }
  else {
    $Query  = "INSERT INTO c_etiqueta (cl_etiqueta, nb_etiqueta, ds_etiqueta, ds_etiqueta_esp, ds_etiqueta_fra, tr_etiqueta) ";
    $Query .= "VALUES($cl_etiqueta_nueva, '$nb_etiqueta', '$ds_etiqueta', '".$ds_etiqueta_esp."', '".$ds_etiqueta_fra."', '$tr_etiqueta')";
	}
	EjecutaQuery($Query);
  createLocaleCSVs();
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>