<?php 
  # Libreria de funciones
  require '../../lib/general.inc.php';
   
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  #Recibe parametros 
  $fl_alumno_template = RecibeParametroNumerico('fl_alumno_template');
  $fl_sesion = RecibeParametroNumerico('fl_sesion');
  $origen = RecibeParametroHTML('origen');
  #Obtiene fl_alumno si el origen es de students
  if($origen=='students_frm.php' OR $origen=='academic_frm.php'){
    $row = RecuperaValor("SELECT fl_usuario FROM c_sesion a, c_usuario b WHERE a.cl_sesion=b.cl_sesion AND fl_sesion = $fl_sesion");
    $clave= $row[0]; //fl_sesion==clave
  }
  else
    $clave=$fl_sesion;

  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_APP_FRM, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Elimina el template enviado al alumno
  if(!empty($fl_alumno_template) AND !empty($fl_sesion)){
    $Query = "DELETE FROM k_alumno_template WHERE fl_alumno_template=$fl_alumno_template AND fl_alumno=$fl_sesion";
    EjecutaQuery($Query);
  }

  # Regresa al detalle
  echo "<html><body><form name='datos' method='post' action='".$origen."'>\n";
    Forma_CampoOculto('clave', $clave);
    echo "\n</form>
  <script>
    document.datos.submit();
  </script></body></html>";

  
?>