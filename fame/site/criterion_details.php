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


	#Recuperamos el isttituo
	$fl_instituto=ObtenInstituto($fl_usuario);
    $clave = RecibeParametroNumerico('clave');
	if(empty($clave))
    $clave = RecibeParametroNumerico('clave',true);

    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
        $Query  = "SELECT nb_criterio, nb_criterio_esp, nb_criterio_fra ";
        $Query .= "FROM c_criterio WHERE fl_criterio=$clave  ";
        $row = RecuperaValor($Query);
        $nb_criterio = str_texto($row[0]);
        $nb_criterio_esp = htmlspecialchars($row[1], ENT_QUOTES, "UTF-8");
        $nb_criterio_fra = htmlspecialchars($row[2], ENT_QUOTES, "UTF-8");
        $no_porcentaje = str_texto($row[1]);
    }else{
		
		
		
		EjecutaQuery("DELETE FROM c_criterio where fl_instituto=$fl_instituto AND nb_criterio='Criterion' ");
		
		
		$Query="INSERT INTO c_criterio (nb_criterio,nb_criterio_esp,nb_criterio_fra, fl_instituto,fl_usuario_creacion,fe_creacion)VALUES('Criterion','Criterio','critère',$fl_instituto,$fl_usuario,CURRENT_TIMESTAMP) ";
		$clave=EjecutaInsert($Query);
		$nb_criterio = "Criterion";
        $nb_criterio_esp = "Criterio";
        $nb_criterio_fra = "Critère";
   
		
	}
	
	
	
?>



<style>
.input-group .form-control {
    z-index: 1 !important;
    }

	/**para los text desabilitados*/
	
.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #fff !important;
	}
.form-control {
    display: block;
     width: auto;
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



/*Para aumentar tamaño de las imagenes solo con pasar el mouse*/
.zoomimg {position: relative; z-index: 150; }
.zoomimg:hover{ background-color: transparent; z-index: 150; }
.zoomimg span{ /* Estilos para la imagen agrandada */
position: absolute;
/*background-color: black;*/
padding: 5px;
left: -50px;
/*border: 5px double gray;*/
visibility: hidden;
color: #000;
width:600px;
/*text-decoration: none;*/
}
.zoomimg span img{ border-width: 0; padding: 2px; width:600%; height:600%; }
.zoomimg:hover span{ visibility: visible; top: 0; /*left: -10px;*/ }


</style>


<form name="datos" method="post" action="site/criterion_iu.php" >
    <input type="hidden" name="clave" id="clave" value="<?php echo $clave;?>"/>
	 <input type="hidden" name="fl_registro" id="fl_registro" value="<?php echo $clave;?>"/>
<div class="row">
    <div class="col-md-12">			
		
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
		
					<div class="tab-pane fade in active" id="criterio">
					   <?php require "criterios_frm_eng_locale.php"; ?>
					</div>
					
					<div class="tab-pane fade in" id="criterio_esp">
						<?php require "criterios_frm_esp_locale.php"; ?>
					</div>
					
					<div class="tab-pane fade in" id="criterio_fra">
						<?php require "criterios_frm_fra_locale.php"; ?>
					</div>
		
		
			
			</div>
		</div>
		
    </div>
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
										<a href="javascript:void(0);" onclick="GuardarCriterion();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
									</li>
								</ul>											
						</div> 
	</div>
</div>
</form>
<script>
function GuardarCriterion(){
	
    var tot_registros = document.getElementById("tot_registros").value;
    var clave = document.getElementById("clave").value;
    var nb_criterio = document.getElementById("nb_criterio").value;
    var nb_criterio_esp = document.getElementById("nb_criterio_esp").value;
    var nb_criterio_fra = document.getElementById("nb_criterio_fra").value;

    var datos = new FormData();
    datos.append('clave', clave);
    datos.append('nb_criterio', nb_criterio);
    datos.append('nb_criterio_esp', nb_criterio_esp);
    datos.append('nb_criterio_fra', nb_criterio_fra);
    datos.append('tot_registros', tot_registros);

    for (var i = 1; i <= tot_registros; i++) {

        var archivo = document.getElementById("nb_archivo_"+i).value;
        var fl_criterio = document.getElementById("fl_citerio_fame_"+i).value;
        datos.append('nb_archivo_'+i, archivo);
        datos.append('fl_criterio_1_'+i, fl_criterio);

        var data = 1;
        var dat2 = 2;
    }

    $.ajax({
        type:"post",
        url: 'site/criterion_iu.php',
        contentType:false, // se envie multipart
        data:datos,
        processData:false, // por si vamos enviar un archivo
    }).done(function(result){			  
        var result = JSON.parse(result);
        var fg_correcto_=result.fg_correcto;
		

        if(fg_correcto_==true){
				  
           $.smallBox({
				  title : "<?php echo ObtenEtiqueta(2357);?>",
               content: "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
               color: "#276627",
               iconSmall: "fa fa-thumbs-up bounce animated",
               timeout: 4000
           });

            
       }
     });
	
	
}

</script>


<script src="../../AD3M2SRC4/lib/fame/dropzone.min.js"></script>
	
