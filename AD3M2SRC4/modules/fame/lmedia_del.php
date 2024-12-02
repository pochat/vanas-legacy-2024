<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_LMED_SP, PERMISO_BAJA)) {
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
  
  # Valida si existen alumnos en el curso de la leccion mandara error
  $row0 = RecuperaValor("SELECT COUNT(*)FROM k_usuario_programa a, c_leccion_sp b WHERE  a.fl_programa_sp = b.fl_programa_sp AND b.fl_leccion_sp=$clave");
  if(!empty($row0[0])){
    MuestraPaginaError(300);
    exit;
  }
  
  # Ruta donde se encuentras las imagenes
  $ruta = "uploads";
  # Elimina el registro    
  $rs = EjecutaQuery("SELECT fl_quiz_pregunta, fg_tipo FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave");
  for($x=0;$row=RecuperaRegistro($rs);$x++) {
    $fl_quiz_pregunta = $row["fl_quiz_pregunta"];
    $fg_tipo = $row["fg_tipo"];
    # Eliminamos los archivos
    if($fg_tipo=="I"){
      # Buscamos los registros
      $Query1 = "SELECT ds_respuesta, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta=".$fl_quiz_pregunta;
      $rs1 = EjecutaQuery($Query1);
      for($m=0;$row1=RecuperaRegistro($rs1);$m++){
        $ds_respuesta = $row1["ds_respuesta"];
        $ds_respuesta_esp = $row1["ds_respuesta_esp"];
        $ds_respuesta_fra = $row1["ds_respuesta_fra"];
        # Eliminamos el archivo
        unlink($ruta."/".$ds_respuesta);
        unlink($ruta."/".$ds_respuesta_esp);
        unlink($ruta."/".$ds_respuesta_fra);
      }
    }
    EjecutaQuery("DELETE FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta");
  }
  
  EjecutaQuery("DELETE FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave");
  EjecutaQuery("DELETE FROM k_criterio_programa_fame WHERE fl_programa_sp = $clave");
  EjecutaQuery("DELETE FROM c_leccion_sp WHERE fl_leccion_sp=$clave");
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>