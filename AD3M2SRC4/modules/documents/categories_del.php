<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CATEGORIAS, PERMISO_BAJA)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $clave = RecibeParametroHTML('clave');
  $clave = explode("_",$clave);
  
  # Verifica que se haya recibido la clave
  if(empty($clave[0])) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Valida que no sea una funcion de sistema
  $row = RecuperaValor("SELECT fg_fijo FROM c_categoria_doc WHERE fl_categoria=$clave[0]");
  if($row[0] == "1") {
    MuestraPaginaError(ERR_FG_FIJO);
    exit;
  }
  
  # Verifica si existen registros asociados
  if(ExisteEnTabla('k_template_doc', 'fl_categoria', $clave[0])) {
      MuestraPaginaError(ERR_REFERENCIADO);
      exit;
	}
  
  # Elimina el registro
  EjecutaQuery("DELETE FROM c_categoria_doc WHERE fl_categoria=$clave[0]");
	
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>