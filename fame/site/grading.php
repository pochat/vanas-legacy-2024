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
  # Obtenemos el perfil
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
?>
<style>
div.dataTables_filter label {
    float: left !important;
}
.tab-pane::after {
    clear: both;
    display: inline !important;
}

</style>



<div class="row">
   <div class="widget-body">
       <ul id="myTabAssignment" class="nav nav-tabs bordered">
        <li class="active">
          <a id="mytabAssign1" href="#assignment_eng" data-toggle="tab">
            English
          </a>
        </li>
        <li class="">
          <a id="mytabAssign2" href="#assignment_esp" data-toggle="tab">
            Spanish
          </a>
        </li>
        <li class="">
          <a id="mytabAssign3" href="#assignment_fra" data-toggle="tab">
            French
          </a>
        </li>
      </ul>

     <div id="myTabAssignCont" class="tab-content padding-10 no-border">


   

        <div class="col-md-1">	
	        <div class="smart-form">
			  <?php FAMEInputText(ObtenEtiqueta(2618),'cl_calificacion','',false,'','',"class='form-control'");?>
		    </div>
	    </div>
        
            <div class="tab-pane fade in active" id="assignment_eng">
               <div class="col-md-2">	
	                <div class="smart-form">
			            <?php FAMEInputText(ObtenEtiqueta(2619),'ds_calificacion','',false,'','',"class='form-control'");?>
		            </div>
	            </div>
            </div>
            <div class="tab-pane fade in " id="assignment_esp">	
                <div class="col-md-2">         
	                <div class="smart-form">
			          <?php FAMEInputText(ObtenEtiqueta(2619),'ds_calificacion_esp','',false,'','',"class='form-control'");?>
		            </div>
	            </div>
            </div>
            <div class="tab-pane fade in " id="assignment_fra">	
                <div class="col-md-2">          
	                <div class="smart-form">
			          <?php FAMEInputText(ObtenEtiqueta(2619),'ds_calificacion_fra','',false,'','',"class='form-control'");?>
		            </div>
	             </div>
            </div>
 
         
        <div class="col-md-1">	
	        <div class="smart-form">
			  <?php FAMEInputText(ObtenEtiqueta(2621),'no_min','',false,'','',"class='form-control'");?>
		    </div>
	    </div>

        
        <div class="col-md-1">	
	        <div class="smart-form">
			  <?php FAMEInputText(ObtenEtiqueta(2622),'no_max','',false,'','',"class='form-control'");?>
		    </div>
	    </div>

     
        <div class="col-md-2">	
	            <div class="smart-form">
			      <?php FAMEInputText(ObtenEtiqueta(2623),'no_equivalencia','',false,'','',"class='form-control'");?>
		        </div>
	    </div>
        <div class="col-md-2"><p>&nbsp;</p>
	            <div class="smart-form">
			        <?php  FAMECheckBox(ObtenEtiqueta(2620),'fg_aprobado','',false,'');  ?>	    
		       </div> 	
	    </div>

	    <div class="col-md-2"><p>&nbsp;</p>	
	      <a class="btn btn-primary" href="javascript:void(0)" onclick="AddGrading()"><i class="fa fa-plus fa-1x"></i> <?php echo ObtenEtiqueta(10); ?></a>
	    </div>
     </div>
  </div>
</div>
 


<br><br><br>
<div class="row">
	<div class="col-md-12">
		<div id="muestra_escalas"></div>
		
	</div>	
</div>		

  <script type="text/javascript">
	
	
	function AddGrading(){
		
	    var ds_calificacion = document.getElementById('ds_calificacion').value;
	    var ds_calificacion_esp = document.getElementById('ds_calificacion_esp').value;
	    var ds_calificacion_fra = document.getElementById('ds_calificacion_fra').value;
		var cl_calificacion = document.getElementById('cl_calificacion').value;
		var no_min = document.getElementById('no_min').value;
		var no_max = document.getElementById('no_max').value;
		var no_equivalencia = document.getElementById('no_equivalencia').value;
		var fg_aprobado = document.getElementById('fg_aprobado');
		
		 if (fg_aprobado.checked) {
			 var fg_aprobado=1;
		 }else{
			 var fg_aprobado=0;
		 }
		 
		 $('#ds_calificacion').val('');
		 $('#ds_calificacion_esp').val('');
		 $('#ds_calificacion_fra').val('');
		 $('#cl_calificacion').val('');
		 $('#no_min').val('');
		 $('#no_max').val('');
		 $('#no_equivalencia').val('');
		 $('#fg_aprobado').prop('checked', false);
		

		//pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'site/muestra_escalas.php',
             data: 'cl_calificacion=' + cl_calificacion + 
                   '&ds_calificacion='+ds_calificacion+
                   '&ds_calificacion_esp='+ds_calificacion_esp+
                   '&ds_calificacion_fra='+ds_calificacion_fra+
				   '&no_min='+no_min+
				   '&no_max='+no_max+
				   '&no_equivalencia='+no_equivalencia+
                   '&fg_action=100'+
				   '&fg_aprobado='+fg_aprobado,

             async: true,
             success: function (html) {
                 $('#muestra_escalas').html(html);
             }
         });
		 

		
		
	}


    function MuestraGrading() {
        //pasamos por ajax los valores y presentamos modal.
        $.ajax({
            type: 'POST',
            url: 'site/muestra_escalas.php',
            data: '',
            async: true,
            success: function (html) {
                $('#muestra_escalas').html(html);
            }
        });
		


    }


	function DeleteGrading(fl_calificacion_criterio) {

		var answer = confirm('<?php echo str_ascii(ObtenMensaje(13)); ?>');
  
		if(answer) {
			//pasamos por ajax los valores y presentamos modal.
			$.ajax({
				type: 'POST',
				url: 'site/muestra_escalas.php',
				data: 'fl_calificacion_criterio='+fl_calificacion_criterio+
					  '&fg_action=101',
				async: true,
				success: function (html) {
					$('#muestra_escalas').html(html);
				}
			});
		
	       $.smallBox({
	           title : "<i class=\"fa fa-check\" aria-hidden=\"true\"></i> <?php echo ObtenEtiqueta(2536); ?>",
	           //content : "Lorem ipsum dolor sit amet, test consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
	           color : "#739E73",
	           timeout: 4000,
	           iconSmall : "fa fa-check ",
	           //number : "2"
	       });


		}
		
	}
  
	// load related plugins & run pagefunction
  /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/      
  /** IMPORTANTES Y NECESARIAS EN DONDE SE UTILIZEN **/
	loadScript("../fame/js/plugin/datatables/jquery.dataTables.min.js", function(){
		loadScript("../fame/js/plugin/datatables/dataTables.colVis.min.js", function(){
			loadScript("../fame/js/plugin/datatables/dataTables.tableTools.min.js", function(){
				loadScript("../fame/js/plugin/datatables/dataTables.bootstrap.min.js", function(){
					//loadScript("../fame/js/plugin/datatable-responsive/datatables.responsive.min.js", pagefunction)
				});
			});
		});
	});
  /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/      
	MuestraGrading();
</script>