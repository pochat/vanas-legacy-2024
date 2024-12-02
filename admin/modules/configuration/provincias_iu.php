<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
   # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CONTENIDOS, PERMISO_MODIFICACION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_provincia = RecibeParametroHTML('ds_provincia');
  $fl_pais = RecibeParametroNumerico('fl_pais');
  $ds_pais = RecibeParametroHTML('ds_pais');
  $ds_type = RecibeParametroHTML('ds_type');
  $mn_PST = RecibeParametroHTML('mn_PST');
  $mn_GST = RecibeParametroHTML('mn_GST');
  $mn_HST = RecibeParametroHTML('mn_HST');
  $mn_tax = RecibeParametroHTML('mn_tax');
  $ds_notas = RecibeParametroHTML('ds_notas');
  
  # Valida campos obligatorios
  if(empty($ds_provincia))
    $ds_provincia_err = ERR_REQUERIDO;
  if(empty($ds_type))
    $ds_type_err = ERR_REQUERIDO;
  if(empty($mn_tax))
    $mn_tax_err = ERR_REQUERIDO;
  if(empty($fl_pais) AND empty($clave))
    $fl_pais_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $ds_provincia_err || $ds_type_err || $mn_tax_err || $fl_pais_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_provincia' , $ds_provincia);
    Forma_CampoOculto('ds_provincia_err' , $ds_provincia_err);
    Forma_CampoOculto('fl_pais' , $fl_pais);
    Forma_CampoOculto('fl_pais_err' , $fl_pais_err);
    Forma_CampoOculto('ds_pais' , $ds_pais);
    Forma_CampoOculto('ds_type' , $ds_type);
    Forma_CampoOculto('ds_type_err' , $ds_type_err);
    Forma_CampoOculto('mn_PST' , $mn_PST);
    Forma_CampoOculto('mn_GST' , $mn_GST);
    Forma_CampoOculto('mn_HST' , $mn_HST);
    Forma_CampoOculto('mn_tax' , $mn_tax);
    Forma_CampoOculto('mn_tax_err' , $mn_tax_err);
    Forma_CampoOculto('ds_notas' , $ds_notas);
    echo "\n</form>
    <script>
      document.datos.submit();
    </script></body></html>";
    exit;
  }
  
  # Insertamos los datos
  if(empty($clave)){
    $Query  = "INSERT INTO k_provincias (fl_pais,nb_provincia,ds_provincia,ds_type,mn_PST,mn_GST,mn_HST,mn_tax,ds_notas) ";
    $Query .= "VALUES ($fl_pais,'".strtoupper($ds_provincia)."','".$ds_provincia."','".$ds_type."','".$mn_PST."','".$mn_GST."','".$mn_HST."','".$mn_tax."','".$ds_notas."')";
  }
  else{# Actualizamos los datos
    $Query  = "UPDATE k_provincias SET ds_provincia='$ds_provincia', ds_type='$ds_type', mn_PST=$mn_PST, mn_GST=$mn_GST, mn_HST=$mn_HST, mn_tax=$mn_tax, ds_notas='$ds_notas' ";
    $Query .= "WHERE fl_provincia=$clave";
  }
  EjecutaQuery($Query);

  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>