<?php
# Libreria de funciones
require '../../lib/general.inc.php';


# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion();

# Recibe parametros

$clave = RecibeParametroNumerico('clave');
$fg_error = RecibeParametroNumerico('fg_error');
$error = RecibeParametroNumerico('error');


# Determina si es alta o modificacion
if(!empty($clave))
    $permiso = PERMISO_DETALLE;
else
    $permiso = PERMISO_ALTA;

# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermiso(FUNC_COURSESCODE, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
}

//programa actual
$programa = ObtenProgramaActual();




# Inicializa variables
if (!$fg_error) { // Sin error, viene del listado
    
    #Recuperamos datos generales del plan einstituto.
    
    $Query = "SELECT  fl_course_code,fl_pais,fl_estado,cl_course_code,nb_course_code,ds_level,ds_descripcion,ds_prerequisito FROM c_course_code
            WHERE fl_course_code=$clave  ";
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
   
   
    $ds_contenido=RecibeParametroHMTL('ds_contenido');
   
}

if(empty($fl_estado))
$fl_estado=0;

# Presenta forma de captura
PresentaHeader();

PresentaEncabezado(FUNC_COURSESCODE);



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



# Forma para captura de datos
Forma_Inicia($clave, True);
Forma_CampoOculto('fl_course_code',$clave);
?>



 <!-- widget content -->
  <div class="widget-body">
  
									
  
  
	<ul id="myTab1" class="nav nav-tabs bordered">
		<li class="active">
			<a href="#course_cod" data-toggle="tab"><i class="fa fa-fw fa-lg fa-pencil"></i>Student Information</a>
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
			<div class="col-md-6">
									<?php
										$Query = "SELECT CONCAT(ds_pais,' - ',cl_iso2), fl_pais FROM c_pais WHERE 1=1 and fg_activo='1' ";
										Forma_CampoSelectBD(ObtenEtiqueta(287), True, 'fl_pais', $Query, $fl_pais, !empty($fl_pais_err)?$fl_pais_err:NULL, True,'', 'right', 'col col-sm-4', 'col col-sm-6');
									?>	  
			 </div>
		 
			 <div class="col-md-6" id="muestra_estado">
			 </div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-6">
							<?php  Forma_CampoTexto(ObtenEtiqueta(2050), True, 'cl_course_code', $cl_course_code, 50, 30, !empty($cl_course_code_err)?$cl_course_code_err:NULL,'','','','onkeyup=\'ValidaInfo(),VerificaCode()\'');
							?>
							<div id="error"></div>	
			</div>
			<div class="col-md-6">
							<?php Forma_CampoTexto(ObtenEtiqueta(2054), False, 'ds_descripcion', $ds_descripcion, 250, 30, !empty($ds_descripcion_err)?$ds_descripcion_err:NULL,'','','','onkeyup=\'ValidaInfo()\'');
								  //Forma_CampoTextAreaNew(ObtenEtiqueta(2054),False,'ds_descripcion',$ds_descripcion,8,3,$ds_descripcion_err);
							?>				
			</div>
			
		</div>

	    <div class="row">

			<div class="col-md-6">	
						<?php Forma_CampoTexto(ObtenEtiqueta(2052), True, 'nb_course_code', $nb_course_code, 150, 30, !empty($nb_course_code_err)?$nb_course_code_err:NULL,'','','','onkeyup=\'ValidaInfo()\'');?>					
			</div>
			
			<div class="col-md-6">
						<?php Forma_CampoTexto(ObtenEtiqueta(2055), False, 'ds_prerequisito', $ds_prerequisito, 250, 30, !empty($ds_prerequisito_err)?$ds_prerequisito_err:NULL,'','','','onkeyup=\'ValidaInfo()\'');
                            //Forma_CampoTextAreaNew(ObtenEtiqueta(2055),False,'ds_prerequisito',$ds_prerequisito,8,3,$ds_prerequisito_err);
                         ?>			
			</div>
	    </div>


		<div class="row">
				<div class="col-md-6">	
							<?php Forma_CampoTexto(ObtenEtiqueta(2053), False, 'ds_level', $ds_level, 50, 30, !empty($ds_level_err)?$ds_level_err:NULL,'','','','onkeyup=\'ValidaInfo()\''); ?>
				</div>
				<div class="col-md-6">
					
				</div>
		</div>
		
		
		

		</div>
		</div>
		
</div>






<script>
    function ValidaInfo() {

        var cl_course_code = document.getElementById("cl_course_code").value;
        var clave=<?php echo $clave; ?>;
		var fl_pais=document.getElementById('fl_pais').value;
		var fl_estado=<?php echo $fl_estado;?>;
		var nb_course_code=document.getElementById("nb_course_code").value;

		
		
		
		if ( (cl_course_code.length > 0)&&(nb_course_code.length>0)&&(fl_pais>0)){
		      $("#aceptar").removeClass('disabled');	
		}else{
		      $("#aceptar").addClass('disabled');
		}
		
		/*
        if (cl_course_code == '') {
            document.getElementById("cl_course_code").style.borderColor = "red";
            document.getElementById("cl_course_code").style.background = "#fff0f0";
        } else {
            document.getElementById("cl_course_code").style.borderColor = "#739e73";
            document.getElementById("cl_course_code").style.background = "#f0fff0";

        }
		if(nb_course_code==''){
		    document.getElementById("nb_course_code").style.borderColor = "red";
            document.getElementById("nb_course_code").style.background = "#fff0f0";
		
		}else{
		    document.getElementById("nb_course_code").style.borderColor = "#739e73";
            document.getElementById("nb_course_code").style.background = "#f0fff0";
		}
		*/


    }
   function VerificaCode(){
    
         var cl_course_code = document.getElementById("cl_course_code").value;
         var clave=<?php echo $clave; ?>;
         var fl_pais=document.getElementById('fl_pais').value;
         var fl_estado=<?php echo $fl_estado;?>;

           $.ajax({
               type: 'POST',
               url : 'verifica_course_code.php',
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

	
 $(document).ready(function () {    
     $('#fl_pais').change(function () {
         VerificaCode();
	      BuscaEstado();  
	  });
	     BuscaEstado();
	     ValidaInfo();
 });

 
 function BuscaEstado(){
	     var fl_pais=document.getElementById('fl_pais').value;
		 var fl_estado=<?php echo $fl_estado;?>		
		 $.ajax({
                  type: 'POST',
                  url : 'muestra_estado.php',
                  data: 'fl_pais='+fl_pais+
				        '&fl_estado='+fl_estado,
                  async: true,
                  success: function(html) {

					     $('#muestra_estado').html(html);
                  }
           });
 }
 
</script>






<?php

# Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
if ($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_COURSESCODE, PERMISO_MODIFICACION);
else
    $fg_guardar = True;
?>

<?php
Forma_Termina($fg_guardar);

# Pie de Pagina
PresentaFooter();






function Forma_CampoTextAreaNew($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows, $p_error='', $p_editar=True, $p_puntos=True) {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      $ds_clase_err = 'has-error';
      $ds_clase = 'custom-scroll';
    }
    else {
      $ds_clase_err = '';
      $ds_clase = 'custom-scroll';
      $ds_error = "";
    }
    if($p_puntos)
      $align = 'right';
    else
      $align = 'left';
   
    echo "
    <div class='smart-form $ds_clase_err'>
      <label class='col col-sm-4 control-label text-align-right'>
        <strong>";
        if($p_requerido) echo "* ";
        echo $p_prompt;
        if($p_puntos)  echo ":";
    echo "
        </strong>
      </label>
      <div class='col col-sm-6'>
        <label class='textarea'>";
        CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase, $p_editar);
        if(!empty($p_error)){          
          echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span>";
        }
    echo "</label>
      </div>      
    </div>";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}



function Forma_CampoTextAreaM($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows, $p_error='', $p_editar=True, $p_puntos=True) {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      $ds_clase_err = 'has-error';
      $ds_clase = 'custom-scroll';
    }
    else {
      $ds_clase_err = '';
      $ds_clase = 'custom-scroll';
      $ds_error = "";
    }
    if($p_puntos)
      $align = 'right';
    else
      $align = 'left';
    /*echo "
    <tr>
      <td align='$align' valign='top' class='css_prompt'>";
    if($p_requerido) echo "* ";
    echo $p_prompt;
    if($p_puntos) echo ":";
    echo "</td>
      <td align='left' valign='top' class='css_msg_error'>";
    CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase, $p_editar);
    if(!empty($p_error))
      echo "\n<br>$ds_error";
    echo "</td>
    </tr>\n";*/
    echo "
    <div class='smart-form $ds_clase_err'>
      <label class='col col-sm-4 control-label text-align-right'>
        <strong>";
        if($p_requerido) echo "* ";
        echo $p_prompt;
        if($p_puntos)  echo ":";
    echo "
        </strong>
      </label>
      <div class='col col-sm-8'>
        <label class='textarea'>";
        CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase, $p_editar);
        if(!empty($p_error)){          
          echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span>";
        }
    echo "</label>
      </div>      
    </div>";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}


?>

