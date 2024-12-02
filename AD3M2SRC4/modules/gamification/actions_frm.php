<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametro
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
	if(!empty($clave))
	$permiso = PERMISO_DETALLE;
	else
	$permiso = PERMISO_ALTA;

	# Verifica que el usuario tenga permiso de usar esta funcion
	if(!ValidaPermiso(FUNC_ACTIONES, $permiso)) {
	MuestraPaginaError(ERR_SIN_PERMISO);
	exit;
	}
  
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
        
		  $Query ="SELECT cl_evento,ds_accion,no_puntos,ds_imagen
                        FROM k_accion  
                        WHERE fl_accion=$clave ";
		  $row = RecuperaValor($Query);      
		  $cl_evento = str_texto($row[0]);
		  $ds_descripcion=str_texto($row[1]);
          $no_puntos=$row[2];
          $nb_archivo=str_texto($row[3]);
		 
    }
    else { // Alta, inicializa campos
			$nb_accion = "";
            $ds_descripcion = "";
            $no_puntos="";
    }
    $nb_archivo_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
   
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_ACTIONES);
  
  # Ventana para preview
  //require '../campus/preview.inc.php';
  
  # Forma para captura de datos
  Forma_Inicia($clave, True);  
  ?>
  
  
  
  
  
  
  <!-- widget content -->
  <div class="widget-body">          
	 <ul id="myTab1" class="nav nav-tabs bordered">
		<li class="active">
			<a href="#programs" data-toggle="tab"><i class="fa fa-fw fa-lg fa-info"></i>Actions</a>
		</li>
	 </ul>
	 
	 
	<div id="myTabContent1" class="tab-content padding-10 no-border">
		<div class="tab-pane fade in active" id="programs">
			<div class="row padding-10">
				<div class="col-md-4">
				
					<?php 
						$Query  = "SELECT nb_evento,cl_evento FROM c_evento WHERE 1=1 ";
                        Forma_CampoSelectBD('Accion', True, 'cl_evento', $Query, $cl_evento, '', False, $p_script, 'right', 'col col-md-4', 'col col-md-7', '', 'cop_co');
					?>
				
				
				</div>
				
				<div class="col-md-5">
                    <?php
                        Forma_CampoTextoM(ObtenEtiqueta(2164), False, 'ds_descripcion', $ds_descripcion, 100, 10, $ds_descripcion_err,False, 'ds_descripcion_d', True, "onkeyup='ValidaCampos(true);'", '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-6 no-padding','Please enter your Points');
					?>

				</div>
				
				<div class="col-md-3">
				    <?php
						Forma_CampoTextoM(ObtenEtiqueta(2165), true, 'no_puntos', $no_puntos, 100, 10, $no_puntos_err,False, 'no_puntos_d', True, "onkeyup='ValidaCampos(true);'", '', "smart-form form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-6', 'col-xs-12 col-sm-12 col-md-12 col-lg-4 no-padding','Please enter your Points');
				    ?>
				</div>

			</div>


            <div class="row padding-10">
                <div class="col-md-4">
                    <?php
                    
                                if(!empty($nb_archivo)) {
                                    Forma_Sencilla_Ini(ObtenEtiqueta(208));
                                    $ext = strtoupper(ObtenExtensionArchivo($nb_archivo));
                                    switch($ext) {
                                        case "JPG":  $ruta = SP_IMAGES."/gamification/accion"; break;
                                        case "PNG":  $ruta = SP_IMAGES."/gamification/accion"; break;
                                        case "JPEG": $ruta = SP_IMAGES."/gamification/accion"; break;
                                        default:     $ruta = SP_IMAGES."/gamification/accion"; break;
                                    }
                        
                        
                                    echo "<a href=\"javascript:void(0);\" data-toggle='modal' data-target='#myModal'  target='_blank'>$nb_archivo</a>";
                        
                        
                        
                                    Forma_Sencilla_Fin( );
                                    Forma_CampoArchivo(ObtenEtiqueta(216), False, 'archivo', 60);
                                    Forma_CampoOculto('nb_archivo', $nb_archivo);
                                }
                                else
                                    Forma_CampoArchivo(ObtenEtiqueta(208), True, 'archivo', 60, $nb_archivo_err);

                    
                    

                    
                    
                    
                    
                    ?>
                </div>


            </div>


		</div>
        <!---finaliza actiones--->



        <?php 
        
        if($nb_archivo){
        ?>
        

        <!-- Modal -->
	        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true">
	          <div class="modal-dialog" role="document">
		        <div class="modal-content">
		          <div class="modal-header">
			        <h5 class="modal-title" id="H1"><i class="fa fa-picture-o" aria-hidden="true"></i> <?php echo $nb_archivo; ?></h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		          </div>
		          <div class="modal-body text-center">
			
			        <img class="img-responsive"  src="<?php echo PATH_IMAGES."/../../images/gamification/accion/".$nb_archivo;?>" />
			
			
			
		          </div>
		          <div class="modal-footer text-center">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        <!--<button type="button" class="btn btn-primary">Save changes</button>-->
		          </div>
		        </div>
	          </div>
	        </div>





        
        <?php
        }
        
        ?>







	</div>	
  </div>


  
  <?php

  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_ACTIONES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  
?>
  
  
  
    
          <script>



              function ValidaCampos() {

                      var no_puntos = document.getElementById('no_puntos').value;
                      var ds_descripcion = document.getElementById('ds_descripcion').value;

                      if (no_puntos.length > 0) {
                          $('#no_puntos_d').addClass('state-success');
                          $('#err_no_puntos_d').addClass('hidden');
                      } else {
                          $('#no_puntos_d').removeClass('state-success');
                          $('#no_puntos_d').addClass('state-error');

                          $('#err_no_puntos_d').removeClass('hidden');
                      }

                      if (ds_descripcion.length > 0) {
                          $('#ds_descripcion_d').addClass('state-success');

                      } else {
                          $('#ds_descripcion_d').removeClass('state-success');
                      }


                      if ((no_puntos.length > 0)) {

                          $('#aceptar').removeClass('disabled');
                      } else {
                          $('#aceptar').addClass('disabled');

                      }



              }

   </script>
        
        
          
  
  
  
  
  
<?php 
echo"<script> 
              $('#aceptar').addClass('disabled');
              ValidaCampos();
    
    </script>";


  # Pie de Pagina
  PresentaFooter( );
 

?>


