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
  
  # Recibe parametros
  $fl_course_code= RecibeParametroNumerico('fl_course_code');
  $fl_programa_sp=RecibeParametroNumerico('fl_programa_sp');
  
  $Query="SELECT cl_course_code,nb_course_code FROM c_course_code WHERE fl_course_code=$fl_course_code ";
  $row=RecuperaValor($Query);
  $cl_clave=str_texto($row[1]);
  
  $Querye="SELECT nb_programa,ds_contenido FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
  $roe=RecuperaValor($Querye);
  $nb_programa=str_texto($roe[0]);
  $ds_contenido=str_uso_normal($roe[1]);
  
  
  ?>




        <div class="modal-header text-center">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h4 class="modal-title text-center"><i class="fa fa-table"></i> <?php echo $nb_programa;?></h4>
        </div>
        <div class="modal-body" style='height:400px;overflow-y: scroll;overflow-x:hidden'>
          <?php echo $ds_contenido; ?>
        </div>
        <div class="modal-footer">
          <a href="#" data-dismiss="modal" class="btn btn-default"><i class="fa fa-times-circle" aria-hidden="true"></i> Close</a>
          <!--<a href="#" class="btn btn-primary">Save changes</a>-->
        </div>












