<?php 
  # Libreria de funciones	
  require("../lib/self_general.php");
 
 

  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  $fl_programa_sp = RecibeParametroNumerico('fl_programa');
  
  #Recuperamos la imegrn del curso.
  $Query="SELECT nb_thumb,nb_programa FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
  $row=RecuperaValor($Query);
  $nb_thumb=str_texto($row[0]);
  $ds_titulo=$row['nb_programa'];
  
  $nb_leccion_sp="FAME_".$ds_titulo;

  #Creamos el nombre del archivo zip
  $nb_leccion_sp =str_replace(":","_",$nb_leccion_sp); 
  $nb_leccion_sp =str_replace(" ","_",$nb_leccion_sp); 
  $nb_leccion_sp =str_replace("-","_",$nb_leccion_sp);
  $nb_leccion_sp =str_replace("(","_",$nb_leccion_sp);
  $nb_leccion_sp =str_replace(")","_",$nb_leccion_sp);
  
  
  
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.mikel_mkl{
	margin-top:30px;
	
}
@media only screen and (max-width: 600px) {
  .mikel_mkl {
    margin-top: 45px !important;
  }
}
</style>

<!-- Button trigger modal -->
<!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button>-->

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Export Moodle <?php echo $ds_titulo;?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  
	    <div class="row">
			<div class="col-md-4 col-xs-4 col-sm-4 text-center">
			  <img src="<?php echo PATH_HOME."/modules/fame/uploads/".$nb_thumb;?>"  style="margin:auto;margin-top:7px; height:120px;" >   
			</div>

			<div class="col-md-4 col-xs-4 col-sm-4 text-center">
				<i class="fa fa-angle-double-right" aria-hidden="true" style="font-size: 55px; margin-top: 30px;"></i>
			</div>

			<div class="col-md-4 col-xs-4 col-sm-4 text-center">
			  <img src="<?php echo  PATH_SELF_IMG."/moodle_logo.png";?>"   class="img-responsive mikel_mkl" height="50%">
			  <br><p>&nbsp;</p><p><i><?php echo ObtenEtiqueta();?></i></p>
			</div>
			
			
		
		</div>
		
		<div class="row">
			<div class="col-md-12 text-center">
					<!--<button class="btn btn-primary buttonload" id="btn_exoor" onclick="ExportarMoodle(<?php echo $fl_programa_sp;?>);">
						<i class="fa fa-spinner fa-spin hidden" id="loadingn"></i>&nbsp; Export
					</button>-->
					<div id="presenta_dowload_zip"></div>
					
					
					
			</div>
		
		</div>
	  
	  
        
		

		
		
      </div>
      <div class="modal-footer text-center">
        
		
					<button class="btn btn-primary buttonload"  onclick="ExportarMoodle(<?php echo $fl_programa_sp;?>);">
						<i class="fa fa-external-link" ></i>&nbsp; Export
					</button>
		
		<a  class="hidden" id="btn_descargar_billing" href="<?php echo "/fame/site/".$nb_leccion_sp."_".$fl_instituto.".zip"; ?>">DescargarZip</a>


		
		
      </div>
    </div>
  </div>
</div>





<script>

$('#exampleModal').modal('show');

function ExportarMoodle(fl_programa){
	
	 
	
	 $.ajax({
		 type:'POST',
		 url:'site/export_moodle.php',
		 data:'fl_programa='+fl_programa,
		 async:true,
		 success:function(html){
			 
			  $('#presenta_dowload_zip').html(html);
			  //$("#loadingn").addClass("hidden");
			  //$("#loadingn_success").removeClass("hidden");
  
		 } 
	 });

	          document.getElementById('btn_descargar_billing').click(); 
			  Eliminar_zip(fl_programa);

	 
}


function Eliminar_zip(fl_programa){
	
	 
	
	 $.ajax({
		 type:'POST',
		 url:'site/elimnar_archivos_zip.php',
		 data:'fl_programa='+fl_programa,
		 async:true,
		 success:function(html){
		
			 
		 }
		 
		 
		 
	 });
	
    
	
	
	
}




</script>

<?php
/*
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=prueba_curso.zip");
        header("Content-Transfer-Encoding: binary");
        readfile("prueba_curso.zip");
        unlink("prueba_curso.zip");//Destruye el archivo temporal
**/
?>



