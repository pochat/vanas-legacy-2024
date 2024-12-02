<?php

  # Libreria de funciones
	require '../../lib/general.inc.php';

	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );

	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');

  # Verifica si se esta insertando
  if(empty($clave)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

	# Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_VARIABLES, PERMISO_MODIFICACION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recibe parametros
  $fg_error = 0;
  $ds_configuracion = RecibeParametroHTML('ds_configuracion');
  $ds_valor = RecibeParametroHTML('ds_valor');
  $fg_sistema=RecibeParametroBinario('fg_sistema');

if ($clave == 171) {

    $fe_counter = RecibeParametroFecha('fe_counter');
    $round1 = RecibeParametroFecha('round1');
    $round2 = RecibeParametroFecha('round2');
    $round3 = RecibeParametroFecha('round3');
    $fl_periodo = RecibeParametroNumerico('fl_periodo');
    $fe_start_date=RecibeParametroFecha('fe_start_date');

    $fe_counter = "" . ValidaFecha($fe_counter) . "";
    $fe_start_date = "" . ValidaFecha($fe_start_date) . "";
    $round1 = "" . ValidaFecha($round1) . "";
    $round2 = "" . ValidaFecha($round2) . "";
    $round3 = "" . ValidaFecha($round3) . "";

}



# Valida campos obligatorios
  if($ds_valor == "")
    $ds_valor_err = ERR_REQUERIDO;

  # Regresa a la forma con error
  $fg_error = $ds_valor_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_configuracion' , $ds_configuracion);
    Forma_CampoOculto('ds_valor' , $ds_valor);
	Forma_CampoOculto('fg_sistema',$fg_sistema);
    Forma_CampoOculto('ds_valor_err' , $ds_valor_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }

  # En esta funcion solo se puede actualizar
	$Query  = "UPDATE c_configuracion SET ";
	$Query .= "ds_valor='$ds_valor',fg_sistema='$fg_sistema' ";
	$Query .= "WHERE cl_configuracion = $clave";
  EjecutaQuery($Query);

if ($clave == 171) {

    $Query = "UPDATE c_configuracion SET ";
    $Query .= "fe_counter='$fe_counter',round1='$round1',round2='$round2',round3='$round3',fe_start_date='$fe_start_date', fl_periodo=$fl_periodo ";
    $Query .= "WHERE cl_configuracion = $clave";

    EjecutaQuery($Query);
}



	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));

?>