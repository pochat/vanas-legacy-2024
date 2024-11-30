<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
 
  # Recibe Parametro
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  $fl_entregable=RecibeParametroNumerico('fl_entregable');

		#Reciupermso los comentarios.
        # Recupera los entregables del alumno
		$Query  = "SELECT  ds_comentario,fe_entregado ";
		$Query .= "FROM k_entregable_sp ";
		$Query .= "WHERE fl_entregable_sp=$fl_entregable ";
		$ro = RecuperaValor($Query);
        $ds_comentario=str_uso_normal($ro[0]);
		$fe_comen=$ro[1];
	    
        $fe_modificacion=strtotime('+0 day',strtotime($fe_comen));
		$fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
		#DAMOS FORMATO DIA,MES, AÑO.
		$date = date_create($fe_modificacion);
		$fe_comentario=date_format($date,'F j , Y , g:i a');
  
        //$ds_comentario="hgofldllsmam";
  
  
  
  echo"
  <script>
     document.getElementById('view_coment').click();//clic automatico que se ejuta y sale modal
  </script>
  ";
  
  
?>

		  <div class="modal-header">
				<h5 class="modal-title" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; <?php echo ObtenEtiqueta(1680).": ".$fe_comentario; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -19px;">
				  <span aria-hidden="true">&times;</span>
				</button>
		  </div>
		  <div class="modal-body">
			<?php echo $ds_comentario; ?>
		  </div>
		 


  
