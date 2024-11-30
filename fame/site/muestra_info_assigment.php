<?php

  # Libreria de funciones	
  require("../lib/self_general.php");

  #Recibimos parametros
  $fl_leccion_sp=RecibeParametroNumerico('fl_leccion_sp');
  $fg_tipo=RecibeParametroHTML('fg_tipo');

	switch($fg_tipo){
		case"A":  

			$QueA="SELECT ds_animacion FROM c_leccion_sp 
				   WHERE fl_leccion_sp=$fl_leccion_sp ";
			$rowA=RecuperaValor($QueA);
			$ds_descripcion=str_uso_normal($rowA['ds_animacion']);
			$title="Assignment ";
			
		break;
		case"AR": 
			$QueryAR="SELECT ds_ref_animacion FROM c_leccion_sp  WHERE fl_leccion_sp=$fl_leccion_sp ";
			$rowAR=RecuperaValor($QueryAR);
			$ds_descripcion=str_uso_normal($rowAR['ds_ref_animacion']);
			$title="Assignment Reference";
			break;
		case"S":  
			$QueryS="SELECT ds_no_sketch FROM c_leccion_sp  WHERE fl_leccion_sp=$fl_leccion_sp ";
			$rowS=RecuperaValor($QueryS);
			$ds_descripcion=str_uso_normal($rowS['ds_no_sketch']);
			$title="Sketch";
			
			break;
		case"SR": 
			$QuerySR="SELECT ds_ref_sketch FROM c_leccion_sp  WHERE fl_leccion_sp=$fl_leccion_sp ";
			$rowSR=RecuperaValor($QuerySR);
			$ds_descripcion=str_uso_normal($rowSR['ds_ref_sketch']);
			$title="Sketch Reference";
			
			break;
	}
  
  
  
  ?>

	<div class="modal-header text-center" >
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-file-text" aria-hidden="true"></i>&nbsp;<?php echo $title; ?> </h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -23px;">
			  <span aria-hidden="true">&times;</span>
			</button>
    </div>
	
    <div class="modal-body">
        <?php echo $ds_descripcion; ?>
	</div>
	
	<div class="modal-footer">
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
    </div>
	