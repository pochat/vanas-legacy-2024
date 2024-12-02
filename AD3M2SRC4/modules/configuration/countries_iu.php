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
  if(!ValidaPermiso(FUNC_PAISES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$cl_iso2 = RecibeParametroHTML('cl_iso2');
  $nb_pais = RecibeParametroHTML('nb_pais');
  $ds_pais = RecibeParametroHTML('ds_pais');
  $cl_iso3 = RecibeParametroHTML('cl_iso3');
  $cl_iso_num = RecibeParametroHTML('cl_iso_num');
  
  # Valida campos obligatorios
  if(empty($ds_pais))
    $ds_pais_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $ds_pais_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('cl_iso2' , $cl_iso2);
    Forma_CampoOculto('nb_pais' , $nb_pais);
    Forma_CampoOculto('ds_pais' , $ds_pais);
    Forma_CampoOculto('ds_pais_err' , $ds_pais_err);
    Forma_CampoOculto('cl_iso3' , $cl_iso3);
    Forma_CampoOculto('cl_iso_num' , $cl_iso_num);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_pais (cl_iso2, nb_pais, ds_pais, cl_iso3, cl_iso_num) ";
    $Query .= "VALUES('$cl_iso2', '$nb_pais', '$ds_pais', '$cl_iso3', '$cl_iso_num') ";
  }
  else {
    $Query  = "UPDATE c_pais SET cl_iso2='$cl_iso2', nb_pais='$nb_pais', ds_pais='$ds_pais', ";
    $Query .= "cl_iso3='$cl_iso3', cl_iso_num='$cl_iso_num' ";
    $Query .= "WHERE fl_pais=$clave";
  }
  EjecutaQuery($Query);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>