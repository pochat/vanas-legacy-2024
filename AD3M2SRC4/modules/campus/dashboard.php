<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  # Presenta home del sistema
  PresentaHeader( );
  
  # Presenta Encabezado
  PresentaEncabezado(193);
  
  $fe_ini1=date('Y-m-d');
  $fe_dos2=date('Y-m-d');

  #Damos formato de fecha alos parametros recibidos.
  $fe_ini1 =strtotime('-2 years',strtotime($fe_ini1)); 
  $fecha1= date('Y-m-d',$fe_ini1);
  $fe_dos2=strtotime('+60 days',strtotime($fe_dos2)); 
  $fecha2= date('Y-m-d',$fe_dos2);
  
  $Query_term="
	SELECT  fl_term,no_grado 
	FROM k_term a, c_programa b, c_periodo c  
	WHERE a.fl_programa=b.fl_programa AND a.fl_periodo=c.fl_periodo AND b.fg_archive='0' 
	AND c.fe_inicio >= '$fecha1'  AND c.fe_inicio <= '$fecha2' 
	ORDER BY c.fe_inicio DESC, nb_programa, no_grado
  ";
   $rs = EjecutaQuery($Query_term);
   for($i=1;$row=RecuperaRegistro($rs);$i++) {
	   
      $fl_term=$row['fl_term'];
      $no_grado=$row['no_grado'];
	  
	   #Revisamos el grado
      if ($no_grado!='1'){

          #Revisamos que este asignado a un term inicial.
          $Query="SELECT a.fl_programa,a.fl_term_ini FROM k_term a, c_programa b, c_periodo c ";
          $Query.="WHERE a.fl_programa=b.fl_programa AND a.fl_periodo=c.fl_periodo AND fl_term=$fl_term ";
          $row=RecuperaValor($Query);
          $fl_term_i=$row[1];
		  
		  if($fl_term_i==0){ 
            $entro=1;
		  } else {
		  	$entro=0;
		  }
	  }
 
   }

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-mapael/2.2.0/js/jquery.mapael.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mapael/2.2.0/js/jquery.mapael.min.js"></script>
<script src="<?php echo PATH_JS?>/vector_map/maps/france_departments.js"></script>
<script src="<?php echo PATH_JS?>/vector_map/maps/world_countries.js"></script>
<script src="<?php echo PATH_JS?>/vector_map/maps/usa_states.js"></script>
<style>

.panel-actions {
  margin-top: -20px;
  margin-bottom: 0;
  text-align: right;
}
.panel-actions a {
  color:#333;
}
.panel-fullscreen {
    display: block;
    z-index: 9999;
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    overflow: auto;
}

</style>

<div class="row">
	<div class="col-md-12">
	<h2 class="row-seperator-header text-center"><i class="fa fa-graduation-cap"></i> Vancouver Animation School</h2>
	<br>
	</div>


	<div class="col-md-5 text-left">
	      <?php 
		  echo Forma_CampoTexto('From ' . ETQ_FMT_FECHA, False, 'fe_ini', '', 10, 0, '', False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-4', 'col col-sm-8');  
	      Forma_Calendario('fe_ini');
	      ?>
	</div>
	<div class="col-md-5 text-left">
		  <?php 
		  echo Forma_CampoTexto('To ' . ETQ_FMT_FECHA, False, 'fe_fin', '', 10, 0, '', False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-4', 'col col-sm-8');  
		  Forma_Calendario('fe_fin');
		  ?>
	</div>
	<div class="col-md-2">
		<a href="javascript:void(0);" class="btn btn-default" style="margin-top: 0px;border-radius:5px;" onclick="AplicarFiltro();"><i class="fa fa-search"></i> Apply</a>
	</div>
</div>
<br>
<div class="row">
<div class="col-md-12">
			<div class="panel panel-default">
                <div class="panel-heading" style="background-color: #ffffff;">
                    <h3 class="panel-title"><i class="fa fa-book" aria-hidden="true"></i> Number of Students Per Program</h3>
                    <ul class="list-inline panel-actions">
                        <li><a href="#" id="panel-fullscreen0" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    
					
						<div class="smart-form text-center">			
							<div class="inline-group text-center hidden" style="margin-left:135px;">
								<label class="radio hidden">
									<input type="radio" name="radio-inline 9" id="basic_students_full" onclick="PresentaGrafica5(1);" >
									<i style="width: 19px;height: 19px"></i>Show labels</label>
								<label class="radio" style="margin-top: 10px;">
									<input type="radio " name="radio-inline 10" id="programas_students_full" onclick="PresentaGrafica5(2);"  checked="checked" >
									<i style="width: 19px; height: 19px"></i>Detailed - by Programs</label>
									
									<!--<label class="radio" style="margin-top: 10px;">
									<input type="radio" name="radio-inline 11" id="programas_students_full_diplomas" onclick="PresentaGrafica5(3);"  checked="checked" >
									<i style="width: 19px; height: 19px"></i>Show only Diplomas</label>-->
							</div>
																		
						</div>
						<div class="text-center">
										<label class="checkbox-inline">
											  <input type="checkbox" id="mostrar_etiquetas" class="checkbox style-0">
											  <span>Show labels</span>
										</label>
										<label class="checkbox-inline">
											  <input type="checkbox" id="mostrar_diplomas" class="checkbox style-0">
											  <span>Show only Diplomas</span>
										</label>
										<label class="checkbox-inline">
											  <input type="checkbox" id="mostrar_certificados" class="checkbox style-0">
											  <span>Show only Certificates</span>
										</label>
						</div>
						
						
						<div class="col-md-12" id="presenta_grafica_students_full">	</div>
					
					
					
					
					
					
					
                </div>
            </div>
	</div>	




</div>








<div class="row">
	<div class="col-md-12">

			<div class="panel panel-default">
                <div class="panel-heading" style="background-color: #ffffff;">
                    <h3 class="panel-title"><i class="fa fa-globe" aria-hidden="true"></i> Country</h3>
                    <ul class="list-inline panel-actions">
                        <li><a href="#" id="panel-fullscreen6" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    
					
									<div class="smart-form text-center">
  
																					
									</div>
									
									<div class="table-responsive">
									<div class="col-md-12" id="presenta_grafica_mapa">	</div>
									</div>
					
					
					
					
					
					
                </div>
            </div>
	</div>	
</div>











<div class="row">
	<div class="col-md-6">

			<div class="panel panel-default">
                <div class="panel-heading" style="background-color: #ffffff;">
                    <h3 class="panel-title"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Gender</h3>
                    <ul class="list-inline panel-actions">
                        <li><a href="#" id="panel-fullscreen" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    
					
									<div class="smart-form text-center">

										
										<div class="inline-group text-center" style="margin-left:135px;">
											<label class="radio">
												<input type="radio" name="radio-inline 1" id="basic" onclick="PresentaGrafica1(1);"  checked="checked">
												<i style="width: 19px;height: 19px"></i>Basic</label>
											<label class="radio" style="margin-top: 10px;">
												<input type="radio" name="radio-inline 2" id="programas" onclick="PresentaGrafica1(2);" >
												<i style="width: 19px; height: 19px"></i>Detailed - by Programs</label>
											
										</div>
																					
									</div>
									
									
									<div class="col-md-12" id="presenta_grafica_genero">	</div>
					
					
					
					
					
					
					
                </div>
            </div>
	</div>	


	<div class="col-md-6">

			<div class="panel panel-default">
                <div class="panel-heading" style="background-color: #ffffff;">
                    <h3 class="panel-title"><i class="fa fa-graduation-cap" aria-hidden="true"></i>Age</h3>
                    <ul class="list-inline panel-actions">
                        <li><a href="#" id="panel-fullscreen2" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                   
				   
									<div class="smart-form text-center">

										
										<div class="inline-group text-center" style="margin-left:135px;">
											<label class="radio">
												<input type="radio" name="radio-inline 3" id="basic_edad" onclick="PresentaGrafica2(1);"  checked="checked">
												<i style="width: 19px;height: 19px"></i>Basic</label>
											<label class="radio" style="margin-top: 10px;">
												<input type="radio" name="radio-inline 4" id="programas_edad" onclick="PresentaGrafica2(2);" >
												<i style="width: 19px; height: 19px"></i>Detailed - by Programs</label>
											
										</div>
																					
									</div>
									
									
									<div class="col-md-12" id="presenta_grafica_edad">	</div>
				   
				   
				   
				   
                </div>
            </div>
	</div>



	
</div>



<div class="row">


	<div class="col-md-6">

			<div class="panel panel-default">
                <div class="panel-heading" style="background-color: #ffffff;">
                    <h3 class="panel-title"><i class="fa fa-globe" aria-hidden="true"></i> Countries</h3>
                    <ul class="list-inline panel-actions">
                        <li><a href="#" id="panel-fullscreen3" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                   
				   
					<div class="smart-form text-center">				
						<div class="inline-group text-center" style="margin-left:135px;">
							<label class="radio">
								<input type="radio" name="radio-inline 5" id="basic_pais" onclick="PresentaGrafica3(1);"  checked="checked">
								<i style="width: 19px;height: 19px"></i>Basic</label>
							<label class="radio" style="margin-top: 10px;">
								<input type="radio" name="radio-inline 6" id="programas_pais" onclick="PresentaGrafica3(2);" >
								<i style="width: 19px; height: 19px"></i>Detailed - by Programs</label>
							
						</div>
																	
					</div>
					<div class="col-md-12" id="presenta_grafica_pais">	</div>
				   
				   
				   
				   
                </div>
            </div>
	</div>





	<div class="col-md-6">

			<div class="panel panel-default">
                <div class="panel-heading" style="background-color: #ffffff;">
                    <h3 class="panel-title"><i class="fa fa-book" aria-hidden="true"></i> Number of Students Per Program</h3>
                    <ul class="list-inline panel-actions">
                        <li><a href="#" id="panel-fullscreen4" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                   
				   
						<div class="smart-form text-center">			
							<div class="inline-group text-center" style="margin-left:135px;">
								<label class="radio">
									<input type="radio" name="radio-inline 7" id="basic_students" onclick="PresentaGrafica4(1);"  checked="checked">
									<i style="width: 19px;height: 19px"></i>Basic</label>
								<label class="radio hidden" style="margin-top: 10px;">
									<input type="radio" name="radio-inline 8" id="programas_students" onclick="PresentaGrafica4(2);" >
									<i style="width: 19px; height: 19px"></i>Detailed - by Programs</label>
								
							</div>
																		
						</div>
						
						
						<div class="col-md-12" id="presenta_grafica_students">	</div>
	   
				   
				   
                </div>
            </div>
	</div>









</div>



<div class="row">
	<div class="col-md-6">

			<div class="panel panel-default">
                <div class="panel-heading" style="background-color: #ffffff;">
                    <h3 class="panel-title"><i class="fa fa-eye" aria-hidden="true"></i> Marketing</h3>
                    <ul class="list-inline panel-actions">
                        <li><a href="#" id="panel-fullscreen5" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    
					
									<div class="smart-form text-center">

										
										<!--<div class="inline-group text-center" style="margin-left:135px;">
											<label class="radio">
												<input type="radio" name="radio-inline 1" id="basic" onclick="PresentaGrafica1(1);"  checked="checked">
												<i style="width: 19px;height: 19px"></i>Basic</label>
											<label class="radio" style="margin-top: 10px;">
												<input type="radio" name="radio-inline 2" id="programas" onclick="PresentaGrafica1(2);" >
												<i style="width: 19px; height: 19px"></i>Detailed - by Programs</label>
											
										</div>-->
																					
									</div>
									
									
									<div class="col-md-12" id="presenta_grafica_marketing">	</div>
					
					
					
					
					
					
					
                </div>
            </div>
	</div>	
</div>























<script>

$(document).ready(function () {
    //Toggle fullscreen
    $("#panel-fullscreen").click(function (e) {
        e.preventDefault();
        
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('.panel').toggleClass('panel-fullscreen');
    });
	
	//Toggle fullscreen
    $("#panel-fullscreen2").click(function (e) {
        e.preventDefault();
        
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('.panel').toggleClass('panel-fullscreen');
    });
	
	
	
	//Toggle fullscreen
    $("#panel-fullscreen3").click(function (e) {
        e.preventDefault();
        
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('.panel').toggleClass('panel-fullscreen');
    });
	
	
		
	//Toggle fullscreen
    $("#panel-fullscreen4").click(function (e) {
        e.preventDefault();
        
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('.panel').toggleClass('panel-fullscreen');
    });
	
	//Toggle fullscreen
    $("#panel-fullscreen5").click(function (e) {
        e.preventDefault();
        
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('.panel').toggleClass('panel-fullscreen');
    });
	
	
	//Toggle fullscreen
    $("#panel-fullscreen0").click(function (e) {
        e.preventDefault();
        
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('.panel').toggleClass('panel-fullscreen');
    });
	
	
	
	
	
	//Toggle fullscreen
    $("#panel-fullscreen6").click(function (e) {
        e.preventDefault();
        
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('.panel').toggleClass('panel-fullscreen');
    });
	
	
	
	
});



</script>







<script>
function PresentaGrafica1(fg_gender){
	
		if(fg_gender==1){
			document.getElementById("basic").checked = true;
			document.getElementById("programas").checked = false;
			
		}else{
			document.getElementById("basic").checked = false;
			document.getElementById("programas").checked = true;
			
		}
			
	
		var fe_ini=document.getElementById('fe_ini').value;
		var fe_fin=document.getElementById('fe_fin').value;
		
	
	    //pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'grafica_genero.php',
             data: 'fg_gender=' + fg_gender+
				   '&fe_ini='+fe_ini+
				   '&fe_fin='+fe_fin,
             async: true,
             success: function (html) {
                 $('#presenta_grafica_genero').html(html);
             }
         });

}
PresentaGrafica1(1);


function PresentaGrafica2(fg_edad){
	
		if(fg_edad==1){
			document.getElementById("basic_edad").checked = true;
			document.getElementById("programas_edad").checked = false;
			
		}else{
			document.getElementById("basic_edad").checked = false;
			document.getElementById("programas_edad").checked = true;
			
		}
		
		
		var fe_ini=document.getElementById('fe_ini').value;
		var fe_fin=document.getElementById('fe_fin').value;
		
		
	    //pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'grafica_edad.php',
             data: 'fg_edad=' + fg_edad+
			       '&fe_ini='+fe_ini+
				   '&fe_fin='+fe_fin,

             async: true,
             success: function (html) {
                 $('#presenta_grafica_edad').html(html);
             }
         });

}
PresentaGrafica2(1);



function PresentaGrafica3(fg_pais){
	
		if(fg_pais==1){
			document.getElementById("basic_pais").checked = true;
			document.getElementById("programas_pais").checked = false;
			
		}else{
			document.getElementById("basic_pais").checked = false;
			document.getElementById("programas_pais").checked = true;
			
		}
		
		var fe_ini=document.getElementById('fe_ini').value;
		var fe_fin=document.getElementById('fe_fin').value;
		
	
	    //pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'grafica_pais.php',
             data: 'fg_pais=' + fg_pais+
				   '&fe_ini='+fe_ini+
				   '&fe_fin='+fe_fin,
             async: true,
             success: function (html) {
                 $('#presenta_grafica_pais').html(html);
             }
         });

}
PresentaGrafica3(1);





function PresentaGrafica4(fg_students){
	
		if(fg_students==1){
			document.getElementById("basic_students").checked = true;
			document.getElementById("programas_students").checked = false;
			
		}else{
			document.getElementById("basic_students").checked = false;
			document.getElementById("programas_students").checked = true;
			
		}
		var fe_ini=document.getElementById('fe_ini').value;
		var fe_fin=document.getElementById('fe_fin').value;
	
	    //pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'grafica_students.php',
             data: 'fg_students=' + fg_students+
				   '&fe_ini='+fe_ini+
				   '&fe_fin='+fe_fin,
             async: true,
             success: function (html) {
                 $('#presenta_grafica_students').html(html);
             }
         });

}
PresentaGrafica4(1);


function PresentaGrafica5(fg_students){
	
		if(fg_students==1){
			document.getElementById("basic_students_full").checked = true;
			document.getElementById("programas_students_full").checked = false;
			
		}else{
			document.getElementById("basic_students_full").checked = false;
			document.getElementById("programas_students_full").checked = true;
			
		}
		
		var fe_ini=document.getElementById('fe_ini').value;
		var fe_fin=document.getElementById('fe_fin').value;
		
		var fg_mostrar_etiquetas = $('#mostrar_etiquetas').is(':checked') ? 1 : 0;
		var fg_mostrar_diplomas = $('#mostrar_diplomas').is(':checked') ? 1 : 0;
		var fg_mostrar_certificados = $('#mostrar_certificados').is(':checked') ? 1 : 0;
		
		
		
		

	
	    //pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'grafica_students_barras.php',
             data: 'fg_students=' + fg_students+
			       '&fe_ini='+fe_ini+
				   '&fe_fin='+fe_fin+
				   '&fg_mostrar_etiquetas='+fg_mostrar_etiquetas+
				   '&fg_mostrar_diplomas='+fg_mostrar_diplomas+
				   '&fg_mostrar_certificados='+fg_mostrar_certificados,

             async: true,
             success: function (html) {
                 $('#presenta_grafica_students_full').html(html);
             }
         });

}
PresentaGrafica5(2,0,0,0);



function PresentaGrafica6(fg_students){
	
		
		
		var fe_ini=document.getElementById('fe_ini').value;
		var fe_fin=document.getElementById('fe_fin').value;
		
		var fg_mostrar=1;
	
	    //pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'grafica_marketing.php',
             data: 'fg_mostrar=' + fg_mostrar+
			       '&fe_ini='+fe_ini+
				   '&fe_fin='+fe_fin,
             async: true,
             success: function (html) {
                 $('#presenta_grafica_marketing').html(html);
             }
         });

}
PresentaGrafica6();




function PresentaGrafica7(fg_students){
	
		
		
		var fe_ini=document.getElementById('fe_ini').value;
		var fe_fin=document.getElementById('fe_fin').value;
		
		var fg_mostrar=1;
	
	    //pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'grafica_mapa.php',
             data: 'fg_mostrar=' + fg_mostrar+
			       '&fe_ini='+fe_ini+
				   '&fe_fin='+fe_fin,
             async: true,
             success: function (html) {
                 $('#presenta_grafica_mapa').html(html);
             }
         });

}
PresentaGrafica7();




function AplicarFiltro(){
	
	PresentaGrafica6();
	PresentaGrafica5(2);
	PresentaGrafica4(1);
	PresentaGrafica3(1);
	PresentaGrafica2(1);
	PresentaGrafica1(1);
	
	
}
$(document).ready(function () {
    //Opciones del checkboxes.
    $("#mostrar_etiquetas").click(function (e) {
		
		   PresentaGrafica5(2);
    });	
    $("#mostrar_diplomas").click(function (e) {
		
		   PresentaGrafica5(2);
    });	
	 $("#mostrar_certificados").click(function (e) {
		
		   PresentaGrafica5(2);
    });	


});

</script>
  
  <?php
  # Presenta Footer
  PresentaFooter( );
  if($entro==1){
?>

<script>
                $(document).ready(function() {
				    $.smallBox({
					    title : "<b><?php echo ObtenEtiqueta(2343);?></b>",
					    content : "<?php echo ObtenEtiqueta(2344);?> <p class='text-align-right'><a href='../programs/terms.php?d=1' class='btn btn-default btn-sm'>Yes</a> <a href='javascript:void(0);' class='btn btn-default btn-sm'>No</a></p>",
					    color : "rgb(189, 38, 35)",
					    //timeout: 8000,
					    icon : "fa fa-calendar swing animated"
				    });

                });
</script>
<?php } ?>
