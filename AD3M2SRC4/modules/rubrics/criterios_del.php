<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CRITERIO_FAME, PERMISO_BAJA)) {
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

  
  
  
  # Elimina los registro asociados
 

#Eliminamos archivos.
 $Query="SELECT fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$clave ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
	  $fl_criterio_fame=$row[0];
	  
	  EjecutaQuery("DELETE FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame");
	
	}  
  #eliminamso el criterio
  EjecutaQuery("DELETE FROM k_criterio_fame WHERE fl_criterio=$clave");
  #Elimimnaso el criterio
  EjecutaQuery("DELETE FROM c_criterio WHERE fl_criterio=$clave");
  
  
  header("Location: ".ObtenProgramaBase( ));
  
  
?>