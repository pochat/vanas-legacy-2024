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
  if(!ValidaPermiso(FUNC_PARTNER_SCHOOL, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
        $Query  = "SELECT ds_titulo,nb_imagen,fl_perfil FROM k_awards WHERE fl_awards=$clave  ";
        $row = RecuperaValor($Query);
        $ds_titulo=$row[0];
		$nb_imagen=$row[1];
        $fl_perfil=$row[2];
    
        switch ($fl_perfil) {
            case '15':
                $checked_3 = 'checked';
                $checked_2 = '';
                $checked_1 = '';
                break;

            case '14':
                $checked_2 = 'checked';
                $checked_3 = '';
                $checked_1 = '';
                break;
            
            default:
                $checked_1 = 'checked';
                $checked_3 = '';
                $checked_2 = '';
                break;
        }


    }
    else { // Alta, inicializa campos
     

      
      
      
    }

  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
 
  

  }
  
 
      
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(230);
  

  
  # Inicia forma de captura
  Forma_Inicia($clave,true);
  if($fg_error)
    Forma_PresentaError( );
  
?>
       <!-- widget content -->
            <div class="widget-body">

                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#programs" data-toggle="tab"><i class="fa fa-fw fa-lg fa-info"></i>Information</a>
                    </li>			    
                </ul>

                <div id="myTabContent1" class="tab-content padding-10 no-border">


                  <div class="tab-pane fade in active" id="programs">
							
                      
                     <div class="row">
                        <div class="col-md-12">
			                <?php 
			                Forma_CampoTexto('Title', False, 'ds_titulo', $ds_titulo, 25, 25);
                            ?>
			                <br>
		                </div>

                         <div class="row">
                            <div class="col-md-4 text-right">
                                <label class="control-label"><b>Profile:</b></label>
		                    </div>		
                            <div class="col-md-6">
                                 <input type="hidden" id="fl_perfil" name="fl_perfil" value=""/>
                                 <div class="smart-form">		   
                                    <section>					
						                    <label class="checkbox">
							                    <input type="checkbox" name="ck1" id="ck1" <?php echo $checked_1;?> >
							                    <i></i>Administrator/SuperAdmin</label>
						                    <label class="checkbox">
							                    <input type="checkbox" name="ck2" id="ck2" <?php echo $checked_2;?> >
							                    <i></i>Teacher</label>
						                    <label class="checkbox">
							                    <input type="checkbox" name="ck3" id="ck3" <?php echo $checked_3;?> >
							                    <i></i>Student</label>				
				                    </section>
			                     </div>
                            </div>
                        </div>



                         <div class="col-md-12">
                             <?php 
                             $ruta = "../../../fame/site/uploads/awards/";
                             Forma_CampoUpload('Image', '', 'nb_imagen', $nb_imagen, $ruta, True, 'nb_imagen', 60, $nb_imagen_err, 'jpg|jpeg');
                             
                             ?>
                         </div>
                    </div>



                   </div> 
				</div>
                 
			</div>

<script>
    //conponentes del segunto tab
    $(document).ready(function () {
        $('#ck1').change(function () {
            if ($('#ck1').is(':checked')) {

                document.getElementById("ck2").checked = false;
                document.getElementById("ck3").checked = false;
                $('#fl_perfil').val(13);
            }
        });
        $('#ck2').change(function () {
            if ($('#ck2').is(':checked')) {
                document.getElementById("ck1").checked = false;
                document.getElementById("ck3").checked = false;
                $('#fl_perfil').val(14);
            }
        });
        $('#ck3').change(function () {
            if ($('#ck3').is(':checked')) {
                document.getElementById("ck1").checked = false;
                document.getElementById("ck2").checked = false;
                $('#fl_perfil').val(15);
            }
        });
    });
</script>

  
  <?php

 
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_PARTNER_SCHOOL, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  ?>

<script src="<?php echo PATH_LIB; ?>/fame/dropzone.min.js">
</script>	