<?php
  
 # Libreria de funciones	
 require("../lib/self_general.php");
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

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
  
  $result['fg_correcto'] = 1;
  
  echo json_encode((Object) $result);
  
  
?>