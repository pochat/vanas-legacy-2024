<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
  # En esta funcion solo se permite modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(124, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Consutamos la tabla para obtener el nombre del archvo y descargarlo
  $row = RecuperaValor("SELECT ds_archivo FROM c_backups WHERE fl_backups=$clave");
  $ds_archivo = $row[0];
  
  # Buscamos el archivo en la ruta 
  $ruta = SP_HOME."/backups/".$ds_archivo;
  if(file_exists($ruta)){
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=".$ds_archivo." ");
    header("Content-Type: application/foctet-stream");
    header('Content-Transfer-Encoding: binary');
    header("Content-Length: ".filesize($ruta));
    readfile($ruta);
  }else{
    # Redirige al 
    header("Location: ".ObtenProgramaBase( ));
  }
?>
