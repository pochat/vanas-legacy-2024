<?php
  # Libreria de funciones	
  require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
  # Verifica que se haya recibido la clave
  if(empty($clave)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  
  # Elimina los registro asociados
  EjecutaQuery("DELETE FROM c_course_code WHERE fl_course_code=$clave AND fl_instituto=$fl_instituto ");
  
  
  
?>