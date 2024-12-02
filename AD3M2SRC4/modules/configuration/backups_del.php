<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MAESTROS, PERMISO_BAJA)) {
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

  # Consutamos la tabla para obtener el nombre del archvo y descargarlo
  $row = RecuperaValor("SELECT ds_archivo,fe_ini_back,fe_fin_back FROM c_backups WHERE fl_backups=$clave");
  $ds_archivo = str_texto($row[0]);
  $fe_ini_back = $row[1];
  $fe_fin_back = $row[2];
  $ruta = SP_HOME."/backups/".$ds_archivo;
  # Si existe el archivo lo borra y los datos de la BD
  if(file_exists($ruta)){    
    //Eliminamos el zip
    unlink($ruta);
    
    # Eliminamos el regitros del zip
    EjecutaQuery("DELETE FROM c_backups WHERE fl_backups=".$clave."");
    
  }
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>
