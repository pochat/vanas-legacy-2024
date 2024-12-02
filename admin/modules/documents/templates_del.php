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
  
  # Verifica que se haya recibido la clave
  if(empty($clave[0])) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Valida que no sea un contrato
  $row = Recuperavalor("SELECT fl_categoria FROM k_template_doc WHERE fl_template=$clave");
  $fl_categoria = $row[0];
  if($fl_categoria==1){
    MuestraPaginaError(18);
    exit;
  }
  
  #Valida que no se haya enviado a algun alumno
  if(ExisteEnTabla('k_alumno_template','fl_template',$clave)){
    MuestraPaginaError(19);
    exit;
  }
  
  # Elimina el registro
  EjecutaQuery("DELETE FROM k_template_doc WHERE fl_template=$clave");

  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>