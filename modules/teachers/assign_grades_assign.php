<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_alumno = RecibeParametroNumerico('fl_alumno');
  
  # Recupera los datos de la entrega de la semana
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);
  $no_semana = ObtenSemanaActualAlumno($fl_alumno);
  $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);
  $Query  = "UPDATE k_entrega_semanal SET fl_promedio_semana=4, ds_critica_animacion='critique.flv' ";
  $Query .= "WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana";
  EjecutaQuery($Query);
  
	# Redirige a la pagina de 
  header("Location: submitted_assignments.php");
  
?>