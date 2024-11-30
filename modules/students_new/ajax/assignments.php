<?php

  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recupera el programa y term que esta cursando el alumno
  $fl_programa = ObtenProgramaAlumno($fl_alumno);
?>

  <!-- widget content -->
  <div class="row">
    <div class="col-xs-12 col-sm-12">      
      <div class="well well-light no-margin" style="padding:8px;">
        <h3 class="text-align-center"><b><?php echo ObtenNombreProgramaAlumno($fl_alumno); ?></b></h3>
        <div class="row">  
          <?php 
          PresentaAcademiHistory($fl_alumno, $fl_programa, False);
          ?>
        </div>
      </div>
    </div>
  </div>
 

  <!-- end widget content -->

  <!-- PAGE RELATED PLUGIN(S) DATETABLES -->
  <script src="<?php echo PATH_N_COM_JS; ?>/plugin/datatables/jquery.dataTables-cust.min.js"></script>
  <script src="<?php echo PATH_N_COM_JS; ?>/plugin/datatables/DT_bootstrap.js"></script>
		
  <!-- Complemento para la tabla search -->
  <script type="text/javascript">
  $(document).ready(function() {
    $('#assigment_list').dataTable({
      "bPaginate": false, // No mostramos la paginacion
	    "bSort": false,  
      "bInfo":false  // No mostramos la cantidad de registros
    });
  })
  </script>