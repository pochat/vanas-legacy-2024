<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_REGISTROS, PERMISO_BAJA)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
  # Verifica que se haya recibido la clave
  if(empty($clave)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recupera la tabla y el renglon que se va a eliminar
	$Query  = "SELECT b.fl_tabla, a.no_renglon ";
  $Query .= "FROM k_celda_tabla a, k_columna_tabla b ";
  $Query .= "WHERE a.fl_columna=b.fl_columna ";
  $Query .= "AND a.fl_celda=$clave";
  $row = RecuperaValor($Query);
  $fl_tabla = $row[0];
  $no_renglon = $row[1];
  
	# Elimina el registro
  if(!empty($fl_tabla) AND !empty($no_renglon)) {
    $Query  = "DELETE FROM k_celda_tabla ";
    $Query .= "WHERE EXISTS(SELECT 1 FROM k_columna_tabla b WHERE b.fl_columna=fl_columna AND fl_tabla=$fl_tabla) ";
    $Query .= "AND no_renglon=$no_renglon";
    EjecutaQuery($Query);
  }
	
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>