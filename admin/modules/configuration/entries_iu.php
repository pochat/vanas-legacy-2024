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
  if(!ValidaPermiso(FUNC_REGISTROS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$fl_tabla = RecibeParametroNumerico('fl_tabla');
  $nb_tabla = RecibeParametroHTML('nb_tabla');
  $no_renglon = RecibeParametroNumerico('no_renglon');
  $no_columnas = RecibeParametroNumerico('no_columnas');
  for($i = 0; $i < $no_columnas; $i++) {
    $fl_columna[$i] = RecibeParametroNumerico('fl_columna_'.$i);
    $nb_columna[$i] = RecibeParametroHTML('nb_columna_'.$i);
    $fl_celda[$i] = RecibeParametroNumerico('fl_celda_'.$i);
    $ds_celda[$i] = RecibeParametroHTML('ds_celda_'.$i);
    $tr_celda[$i] = RecibeParametroHTML('tr_celda_'.$i);
    $ds_href[$i] = RecibeParametroHTML('ds_href_'.$i);
  }
  
  # Valida campos obligatorios
  if(empty($no_renglon))
    $no_renglon_err = ERR_REQUERIDO;
  
  # Valida enteros
  if(!empty($no_renglon) AND !ValidaEntero($no_renglon))
    $no_renglon_err = ERR_ENTERO;
  if($no_renglon > MAX_TINYINT)
    $no_renglon_err = ERR_TINYINT;
  
  # Verifica que no exista el registro
  if(!$no_renglon_err) {
    $Query  = "SELECT count(1) ";
    $Query .= "FROM k_columna_tabla a, k_celda_tabla b ";
    $Query .= "WHERE a.fl_columna=b.fl_columna ";
    $Query .= "AND a.fl_tabla=$fl_tabla ";
    $Query .= "AND a.no_orden=1 ";
    $Query .= "AND b.no_renglon=$no_renglon ";
    if(!empty($clave))
      $Query .= "AND b.fl_celda<>$clave";
    $row = RecuperaValor($Query);
    if($row[0] > 0)
      $no_renglon_err = 107; // Ya existe un registro con el mismo numero para esta columna
  }
  
	# Regresa a la forma con error
  $fg_error = $no_renglon_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('fl_tabla', $fl_tabla);
    Forma_CampoOculto('nb_tabla', $nb_tabla);
    Forma_CampoOculto('no_renglon', $no_renglon);
    Forma_CampoOculto('no_renglon_err', $no_renglon_err);
    Forma_CampoOculto('no_columnas', $no_columnas);
    for($i = 0; $i < $no_columnas; $i++) {
      Forma_CampoOculto('fl_columna_'.$i, $fl_columna[$i]);
      Forma_CampoOculto('nb_columna_'.$i, $nb_columna[$i]);
      Forma_CampoOculto('fl_celda_'.$i, $fl_celda[$i]);
      Forma_CampoOculto('ds_celda_'.$i, $ds_celda[$i]);
      Forma_CampoOculto('tr_celda_'.$i, $tr_celda[$i]);
      Forma_CampoOculto('ds_href_'.$i, $ds_href[$i]);
    }
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza o inserta el registro
  if(empty($clave)) {
    $rs = EjecutaQuery("SELECT fl_columna FROM k_columna_tabla WHERE fl_tabla=$fl_tabla ORDER BY no_orden");
    for($i = 0; $row = RecuperaRegistro($rs); $i++) {
      EjecutaQuery("INSERT INTO k_celda_tabla (fl_columna, no_renglon) VALUES ($row[0], $no_renglon)");
    }
  }
  else {
    for($i = 0; $i < $no_columnas; $i++) {
      if(!empty($fl_celda[$i])) {
        $Query  = "UPDATE k_celda_tabla ";
        $Query .= "SET no_renglon=$no_renglon, ds_celda='$ds_celda[$i]', tr_celda='$tr_celda[$i]', ds_href='$ds_href[$i]' ";
        $Query .= "WHERE fl_celda=$fl_celda[$i]";
      }
      else {
        $Query  = "INSERT INTO k_celda_tabla (fl_columna, no_renglon, ds_celda, tr_celda, ds_href) ";
        $Query .= "VALUES($fl_columna[$i], $no_renglon, '$ds_celda[$i]', '$tr_celda[$i]', '$ds_href[$i]')";
      }
      EjecutaQuery($Query);
    }
	}
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>