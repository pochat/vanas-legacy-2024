<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
 
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CRITERIO_FAME, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
        $Query  = "SELECT nb_criterio, nb_criterio_esp, nb_criterio_fra ";
        $Query .= "FROM c_criterio WHERE fl_criterio=$clave  ";
        $row = RecuperaValor($Query);
        $nb_criterio = str_texto($row[0]);
        $nb_criterio_esp = htmlspecialchars($row[1], ENT_QUOTES, "UTF-8");
        $nb_criterio_fra = htmlspecialchars($row[2], ENT_QUOTES, "UTF-8");
        $no_porcentaje = str_texto($row[1]);
    }
    else { // Alta, inicializa campos
      $nb_criterio = "";
      $nb_criterio_esp = "";
      $nb_criterio_fra = "";
      $no_porcentaje = "";
     
      #eLIMINAMOS EL CRITERIO que esten en null
      $Query="DELETE FROM k_criterio_fame WHERE fl_criterio IS NULL    ";
      EjecutaQuery($Query);
	  
	   #eLIMINAMOS archivos que esten en null
      $Query="DELETE FROM c_archivo_criterio WHERE fl_criterio_fame IS NULL    ";
      EjecutaQuery($Query);
    }

  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_criterio = RecibeParametroHTML('nb_criterio');
    $nb_criterio_esp = RecibeParametroHTML('nb_criterio_esp');
    $nb_criterio_fra = RecibeParametroHTML('nb_criterio_fra');
    $no_porcentaje = RecibeParametroNumerico('no_porcentaje');
  }
  
# Presenta forma de captura
PresentaHeader( );
PresentaEncabezado(165);
  
echo "<script type='text/javascript' src='".PATH_JS."/frmCourses.js.php'></script>";

  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError(); 
?>

<style>
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

.button input[type="text"]{
    height:1.5em;
    width:7em;
    -webkit-transform: rotate(-90deg); 
    -moz-transform: rotate(-90deg); 
    font-size:1.5em;
    border:0 none;
    background:none;
}
/*
.dropzone .dz-default.dz-message {
background-image: url(../../images/dropzone_small.png) !important;
width: 1px !important;
height: 1px !important;
}
*/
.dropzone .dz-default.dz-message {
	width: 1px !important;
	height:1px !important;
	background-image: url() !important;
	background-repeat: no-repeat !important;
}

.font {
    color:#333 !important;
    font: 18x Arial !important;
    font-weight:100 !important;
}
</style>

<input type="hidden" value="<?php echo $clave; ?>" id="fl_registro" name="fl_registro" />

<!-- widget content -->
<div class="widget-body">
    <ul id="myTab1" class="nav nav-tabs bordered">
        <li class="active">
            <a href="#criterio" data-toggle="tab">
            <i class="fa fa-fw fa-lg fa-info"></i>
        		English
        	</a>
        </li>
        <li class="">
            <a href="#criterio_esp" data-toggle="tab">
            <i class="fa fa-fw fa-lg fa-info"></i>
        		Spanish
    		</a>
        </li>
        <li class="">
            <a href="#criterio_fra" data-toggle="tab">
            <i class="fa fa-fw fa-lg fa-info"></i>
            	French
        	</a>
        </li>
    </ul>
<div id="myTabContent" class="tab-content padding-10 no-border">

<!-- English Content Starts Here -->
<?php include_once "criterios_frm_eng_locale.php"; ?>
<!-- END of English Content -->

<!-- Spanish Content Starts Here -->
<?php include_once "criterios_frm_esp_locale.php"; ?>
<!-- END of Spanish Content -->

<!-- French Content Starts Here -->
<?php include_once "criterios_frm_fra_locale.php"; ?>
<!-- END of French Content -->

</div>
<script>
	$(document).ready(function () {
		$("#btncancelar").addClass('disabled');//botones desabilitados
		$("#btnsaves").addClass('disabled');//botones desabilitados
		document.getElementById('nb_criterio').focus(); 
		$("#aceptar").addClass('disabled');
        $('#nb_criterio').change(function () {
            ValidaInfo();
    	});
		ValidaInfo();		
	});
 
function ValidaInfo(){
   var nb_nombre_rubric=document.getElementById('nb_criterio').value;
   //var ds_descripcion1=document.getElementById('nb_criterio').value;   
    if(nb_nombre_rubric !=''){
		$("#aceptar").removeClass('disabled');
	}else{
		$("#aceptar").addClass('disabled');
		}
   
} 
 
function EditarNombreCriterio(){
    document.getElementById("nb_criterio").disabled = false;
	document.getElementById("no_porcentaje").disabled = false;
	$("#btncancelar").removeClass('disabled');//botones desabilitados
	$("#btnsaves").removeClass('disabled');//botones desabilitados
}

function CancelarNombreCriterio(){
    document.getElementById("nb_criterio").disabled = true;
	document.getElementById("no_porcentaje").disabled = true;
	$("#btncancelar").addClass('disabled');//botones desabilitados
    $("#btnsaves").addClass('disabled');//botones desabilitados
}

function GuardarNombreCriterio(){
    document.getElementById("nb_criterio").disabled = true;
	document.getElementById("no_porcentaje").disabled = true;
	$("#btncancelar").addClass('disabled');//botones desabilitados
    $("#btnsaves").addClass('disabled');//botones desabilitados        
}
</script>

<?php
	# Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
	if($permiso == PERMISO_DETALLE)
    	$fg_guardar = ValidaPermiso(FUNC_CRITERIO_FAME, PERMISO_MODIFICACION);
	else
    	$fg_guardar = True;
	Forma_TerminaM($fg_guardar);
  
	# Pie de Pagina
	PresentaFooter( );

	function Forma_TerminaM($p_guardar=False, $p_url_cancelar='', $p_etq_aceptar=ETQ_SALVAR, $p_etq_cancelar=ETQ_CANCELAR, $p_click_cancelar='') {

      # Destino para el boton Cancelar
    	if(empty($p_click_cancelar)) {
        	if(empty($p_url_cancelar)) {
            	$nb_programa = ObtenProgramaBase( );
            	$click_cancelar = "parent.location='$nb_programa'";
        	}
			else
              $click_cancelar = "parent.location='$p_url_cancelar'";
      	}
      	else
        	$click_cancelar = $p_click_cancelar;
      
    	echo "<footer>";
    	echo "<div style='width: 228px; right: 0px; display: block; padding:0px 50px 10px 0px;' outline='0' class='ui-widget ui-chatbox'>";
    	if($p_guardar)
        	echo "<a class='btn btn-primary btn-circle btn-xl disabled' title='".$p_etq_aceptar."' name='aceptar' id='aceptar' onClick='javascript:document.datos.submit();'><i class='fa fa-save'></i></a>&nbsp;";
        echo "  <a class='btn btn-default btn-circle btn-xl' title='".$p_etq_cancelar."' name='aceptar' id='cancelar' onClick=\"$click_cancelar\"><i class='fa fa-times'></i></a>
        	</div>          
        	</footer>
    		</form>
    		</div>
    		</div>";
    	}
?>
<script src="<?php echo PATH_LIB; ?>/fame/dropzone.min.js">
</script>	
<!---plugin necesario para pintar el circulo -->
<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/knob/jquery.knob.min.js">
</script>


 