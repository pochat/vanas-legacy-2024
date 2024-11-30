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
$fl_instituto=ObtenInstituto($fl_usuario);

# Recibe parametros
$clave = RecibeParametroNumerico('c',True);

# Inicializa variables
if (!empty($clave)) { 
    
    #Recuperamos datos generales del plan einstituto.
    
    $Query = "SELECT  fl_course_code,fl_pais,fl_estado,cl_course_code,nb_course_code,ds_level,ds_descripcion,ds_prerequisito FROM c_course_code
            WHERE fl_course_code=$clave AND fl_instituto=$fl_instituto  ";
    $row = RecuperaValor($Query);
    $fl_instituto = str_texto($row['fl_course_code']);
	$fl_pais=$row['fl_pais'];
	$fl_estado=$row['fl_estado'];
	$cl_course_code=str_texto($row['cl_course_code']);
    $nb_course_code = str_texto($row['nb_course_code']);
	$ds_level=str_texto($row['ds_level']);
	$ds_descripcion=str_texto($row['ds_descripcion']);
	$ds_prerequisito=str_texto($row['ds_prerequisito']);
	
	
   
    
    
   
} else { // Con error, recibe parametros (viene de la pagina de actualizacion)
   
   
    
   
}

if(empty($fl_estado))
$fl_estado=0;

echo"<style>
 .input-group .form-control {
   
    z-index: 1 !important;
    
    }
	
	/**para los text desabilitados*/
	 .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {

		 background-color: #fff !important;
	 }

	.col-md-2{
	padding-left: 2px;
    padding-right: 2px;
	}
    </style>
 "; 

?>

<form name="datos" action="site/curriculum_mapping_iu.php" method="post" >
    <input type="hidden" name="clave" id="clave" value="<?php echo $clave;?>" />
 <!-- widget content -->
  <div class="widget-body">
	<ul id="myTab1" class="nav nav-tabs bordered">
		<li class="active">
			<a href="#course_cod" data-toggle="tab"><i class="fa fa-fw fa-lg fa-pencil"></i><?php echo ObtenEtiqueta(2056);?></a>
		</li>
	</ul>  
    <div id="myTabContent1" class="tab-content no-padding no-border">  
       <div class="tab-pane fade in active" id="course_cod">
		<div class="row">	
			  <div class="col-md-12">
					<div class="alert alert-danger fade in  hidden" id="msjerror" ><button class="close" data-dismiss="alert">	× </button><i class="fa-fw fa fa-times"></i>
						 <?php echo ObtenEtiqueta(2051); ?>
					</div>		
			  </div>

		</div>
        <br/><br/>
		<div class="row">
			<div class="col-md-2">&nbsp;</div>
			<div class="col-md-4">
				 <label class="control-label" style="margin: 7px;"><b>* <?php echo ObtenEtiqueta(1141);?></b></label>
				<?php
					$Query = "SELECT CONCAT(ds_pais,' - ',cl_iso2), fl_pais FROM c_pais WHERE 1=1 and fg_activo='1' ";
					#Forma_CampoSelectBD(ObtenEtiqueta(287), True, 'fl_pais', $Query, $fl_pais, $fl_pais_err, True,'', 'right', 'col col-sm-4', 'col col-sm-6');
					FAMECampoSelectBD(ObtenEtiqueta(287),'fl_pais', $Query, $fl_pais, 'select2', True, '', $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       					 
				?>	
				<div class="note hidden" id="fl_pais_texto_error" style="color:#A90329;"><?php echo ObtenEtiqueta(2350);?></div>
			 </div>
		 
			 <div class="col-md-4" id="muestra_estado">
			 </div>
			 <div class="col-md-2">&nbsp;</div>
		</div>
		<br/>
		<div class="row">
		    <div class="col-md-2">&nbsp;</div>
			<div class="col-md-4">
				<div class="smart-form">
							<?php  
							//Forma_CampoTexto(ObtenEtiqueta(2050), True, 'cl_course_code', $cl_course_code, 50, 30, $cl_course_code_err,'','','','onkeyup=\'ValidaInfo(),VerificaCode()\'');
							  FAMEInputText(ObtenEtiqueta(2050),'cl_course_code',$cl_course_code,true,"","","","VerificaCode();");
							  
							?>
			    </div>
							<div id="error"></div>	
			</div>
			<div class="col-md-4">
				<div class="smart-form">
							<?php 
							 FAMEInputText(ObtenEtiqueta(2054),'ds_descripcion',$ds_descripcion,true);
							//Forma_CampoTexto(ObtenEtiqueta(2054), False, 'ds_descripcion', $ds_descripcion, 250, 30, $ds_descripcion_err,'','','','onkeyup=\'ValidaInfo()\'');
								  //Forma_CampoTextAreaNew(ObtenEtiqueta(2054),False,'ds_descripcion',$ds_descripcion,8,3,$ds_descripcion_err);
							?>
				</div>
			</div>
			<div class="col-md-2">&nbsp;</div>
		</div>

	    <div class="row">
			<div class="col-md-2">&nbsp;</div>
			<div class="col-md-4">
				<div class="smart-form">
						<?php 
						FAMEInputText(ObtenEtiqueta(2052),'nb_course_code',$nb_course_code,true);							
						//Forma_CampoTexto(ObtenEtiqueta(2052), True, 'nb_course_code', $nb_course_code, 150, 30, $nb_course_code_err,'','','','onkeyup=\'ValidaInfo()\'');?>					
				</div>
			</div>
			
			<div class="col-md-4">
				<div class="smart-form">
						<?php
							FAMEInputText(ObtenEtiqueta(2055),'ds_prerequisito',$ds_prerequisito,false);							
						
							//Forma_CampoTexto(ObtenEtiqueta(2055), False, 'ds_prerequisito', $ds_prerequisito, 250, 30, $ds_prerequisito_err,'','','','onkeyup=\'ValidaInfo()\'');
                            //Forma_CampoTextAreaNew(ObtenEtiqueta(2055),False,'ds_prerequisito',$ds_prerequisito,8,3,$ds_prerequisito_err);
                         ?>	
				</div>
			</div>
			<div class="col-md-2">&nbsp;</div>
	    </div>


		<div class="row">
				<div class="col-md-2">&nbsp;</div>
				<div class="col-md-4">
					<div class="smart-form">
							<?php 
							//Forma_CampoTexto(ObtenEtiqueta(2053), False, 'ds_level', $ds_level, 50, 30, $ds_level_err,'','','','onkeyup=\'ValidaInfo()\''); 
							//FAMEInputText(ObtenEtiqueta(2053),'ds_level',$ds_level,true);	
							$Query = "SELECT nb_grado,fl_grado FROM k_grado_fame a JOIN c_clasificacion_grado b ON a.cl_clasificacion_grado=b.cl_clasificacion_grado ";                                                                                                                                     
							FAMECampoSelectBD(ObtenEtiqueta(2053),'ds_level', $Query, $ds_level, 'select2', True, '', $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       					 
				

							
						
                            ?>
					</div>
				</div>
				<div class="col-md-4">
					
				</div>
				<div class="col-md-2">&nbsp;</div>
		</div>
		
		
		
		
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">     </div>
			<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">     											
								<div class="smart-form">														
								<br><br>																
										<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 25px;padding-left: 295px;">																	
											<li>
												<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
											</li>																	
											<li>
												<a href="javascript:void(0);" onclick="GuardarDatos();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
											</li>
										</ul>											
								</div> 
			</div>
		</div>
		
		
		

		</div>
		</div>
		
</div>

</form>


<script>
	function Cancel(){
		window.location.href = "<?php echo ObtenConfiguracion(116);?>/fame/index.php#site/curriculum_mapping.php";
		
	}

	function GuardarDatos(){
		
		var clave=document.getElementById('clave').value;
		var fl_pais=document.getElementById('fl_pais').value;
		var fl_estado=document.getElementById('fl_estado').value;
		var cl_course_code = document.getElementById("cl_course_code").value;
		var ds_descripcion = document.getElementById("ds_descripcion").value;
		var nb_course_code=document.getElementById("nb_course_code").value;
		var ds_prerequisito=document.getElementById("ds_prerequisito").value;
		var ds_level=document.getElementById("ds_level").value;
		var fg_error=0;
		
		if(fl_pais!=0){
			
			$("#fl_pais_texto_error").addClass("hidden");
			
		}else{
			$("#fl_pais_texto_error").removeClass("hidden");
			fg_error=1;
			return;
		}
		
		if ( (cl_course_code.length < 1)){
			$("#cl_course_code_texto_error_input_error").addClass("state-error");
			$("#cl_course_code_texto_error").removeClass("hidden");
			fg_error=1;
			return;
		}else{
			$("#cl_course_code_texto_error_input_error").removeClass("state-error");
			$("#cl_course_code_texto_error").addClass("hidden");
			
		}
		
		if ( (ds_descripcion.length < 1)){
			$("#ds_descripcion_texto_error_input_error").addClass("state-error");
			$("#ds_descripcion_texto_error").removeClass("hidden");
			fg_error=1;
			return;
		}else{
			$("#ds_descripcion_texto_error_input_error").removeClass("state-error");
			$("#ds_descripcion_texto_error").addClass("hidden");
			
		}
		
		if ( (nb_course_code.length < 1)){
			$("#nb_course_code_texto_error_input_error").addClass("state-error");
			$("#nb_course_code_texto_error").removeClass("hidden");
			fg_error=1;
			return;
		}else{
			$("#nb_course_code_texto_error_input_error").removeClass("state-error");
			$("#nb_course_code_texto_error").addClass("hidden");
			
		}
		
		//if (ds_level!=0){
			//$("#ds_level_texto_error_input_error").addClass("state-error");
			//$("#ds_level_texto_error").removeClass("hidden");
		//	fg_error=1;
		//	return;
		//}else{
			//$("#ds_level_texto_error_input_error").removeClass("state-error");
			//$("#ds_level_texto_error").addClass("hidden");
			
		//}
		
		if(fg_error==1){
			return;
		}else{
			
			$.ajax({
                  type: 'POST',
                  url : 'site/curriculum_mapping_iu.php',
                  data: 'fl_pais='+fl_pais+
                        '&cl_course_code='+cl_course_code+
                        '&ds_descripcion='+ds_descripcion+
                        '&nb_course_code='+nb_course_code+
                        '&ds_prerequisito='+ds_prerequisito+
                        '&ds_level='+ds_level+
						'&clave='+clave+
				        '&fl_estado='+fl_estado,
                  async: true,
            }).done(function(result){			  
					var result = JSON.parse(result);
				    var fg_correcto_=result.fg_correcto;
					var clave=result.clave;
					
					if(fg_correcto_==1){
						//alerta de exito.		  
						  $.smallBox({
						  title : "<?php echo ObtenEtiqueta(2357);?>",
						  content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
						  color : "#276627",
						  iconSmall : "fa fa-thumbs-up bounce animated",
						  timeout : 4000
						  });
						
						//Colocamo el valor clave al input 
						$('#clave').val(clave);
						
					}
                  
           });
			
			//document.datos.submit();
		}
		
		
	}


   function VerificaCode(){
    
         var cl_course_code = document.getElementById("cl_course_code").value;
         var clave=<?php echo $clave; ?>;
         var fl_pais=document.getElementById('fl_pais').value;
         var fl_estado=<?php echo $fl_estado;?>;

		if(cl_course_code.length>0){

           $.ajax({
               type: 'POST',
               url : 'site/verifica_course_code.php',
               data: 'cl_course_code='+cl_course_code+
                     '&fl_pais='+fl_pais+
                     '&fl_estado='+fl_estado+
                     '&clave='+clave,
               async: true,
               success: function(html) {
                   $('#error').html(html);
               }
           });
		   
		}

    }

	
 $(document).ready(function () {    
     $('#fl_pais').change(function () {
         
         var fl_pais=document.getElementById('fl_pais').value;
         
		 if(fl_pais!=0){
			 $("#fl_pais_texto_error").addClass("hidden");
		 }
		 
         BuscaEstado();  
		

		 VerificaCode(); 

	  });
     BuscaEstado();
	
    $('#cl_course_code').change(function () {
		var cl_course_code = document.getElementById("cl_course_code").value;
		
		if ((cl_course_code.length > 0)){
			$("#cl_course_code_texto_error_input_error").removeClass("state-error");
			$("#cl_course_code_texto_error").addClass("hidden");		
		}	
	});	
  	   
     
	 $('#ds_descripcion').change(function () {
		var ds_descripcion = document.getElementById("ds_descripcion").value;
		
		if ((ds_descripcion.length > 0)){
			$("#ds_descripcion_texto_error_input_error").removeClass("state-error");
			$("#ds_descripcion_texto_error").addClass("hidden");		
		}	
	 });



	$('#nb_course_code').change(function (){
		var nb_course_code = document.getElementById("nb_course_code").value;
		
		if ((nb_course_code.length > 0)){
			$("#nb_course_code_texto_error_input_error").removeClass("state-error");
			$("#nb_course_code_texto_error").addClass("hidden");		
		}	
	 });	

	


 });

 
 function BuscaEstado(){
	     var fl_pais=document.getElementById('fl_pais').value;
		 var fl_estado=<?php echo $fl_estado;?>		
		 $.ajax({
                  type: 'POST',
                  url : 'site/muestra_estado.php',
                  data: 'fl_pais='+fl_pais+
				        '&fl_estado='+fl_estado,
                  async: true,
                  success: function(html) {

					     $('#muestra_estado').html(html);
                  }
           });
 }
 
</script>

<script>

 /* Debemos agregarlo para el fucnionamiento de diversos  plugins*/
pageSetUp();
    $(document).ready(function () {

    });

</script>
