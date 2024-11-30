<?php
# Libreria de funciones	
require("../lib/self_general.php");
require("../class/EnumOpcionRenovacion.php");
# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False, 0, True);

# Verifica que el usuario tenga permiso de usar esta funcion
if (!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
}
$fl_instituto = ObtenInstituto($fl_usuario);

#Recibimos parametros 
$fl_instituto = RecibeParametroNumerico('fl_instituto');
$fg_opcion = RecibeParametroNumerico('fg_opcion_');

#Identificamos que tipo de plan tiene el Instituto.
$fg_tipo_plan = ObtenPlanActualInstituto($fl_instituto);
$no_licencias_totales = ObtenNumLicencias($fl_instituto);
$no_licencias_disponibles = ObtenNumLicenciasDisponibles($fl_instituto);
$no_licencias_usadas = ObtenNumLicenciasUsadas($fl_instituto);
$fe_final_contrato = ObtenFechaFinalizacionContratoPlan($fl_instituto);
$nb_usuario = ObtenNombreUsuario($fl_usuario);

$fe_terminacion_plan = $fe_final_contrato;
#DAMOS FORMATO DIA,MES, AN?O
$date = date_create($fe_terminacion_plan);
$fe_terminacion_plan = date_format($date, 'F j, Y');


#Damos formato Fecha final del contrato.
$etq = ObtenEtiqueta(1584);
$etq_plan = ObtenEtiquetaPlanRenovacion($fl_instituto, $fe_terminacion_plan, $etq);

$etq_plan = str_replace("Auto-renew my contract", " ", $etq_plan); #nb_isntituto;



#Es para pasaber el cambio de plan
if ($fg_tipo_plan == "A") {
    $fg_tipo_plan_actual = "Annual Plan";
    $fg_nuevo_plan = "Monthly Plan";
} else {
    $fg_tipo_plan_actual = "Monthly Plan";
    $fg_nuevo_plan = "Annual Plan";
}

$fg_plan_actual = $fg_tipo_plan;


echo "<div id=\"finaliza\">
	  </div>";

#Presenta Info basica del plan como son: Numero de licencias, usadas, disponibles etc.



$informacion_plan_actual = "
                                        
                                        <tr> 
                                            <td width='50%' class='text-right' style='border:none;font-size:14px;color:#404040; '><strong><i>" . ObtenEtiqueta(984) . ":</i></strong></td>
                                            <td width='50%' class='text-left' style='border:none;font-size:14px;color:#404040;'>$fg_tipo_plan_actual </td>
                                            
                                        </tr>
                          ";
if ($fg_opcion == EnumOpcionRenovacion::AutoRenovacion) {
    $informacion_plan_actual .= "                          
                      <tr> 
                        <td  class='text-right' style='border:none;font-size:14px;color:#404040;'><strong><i>" . ObtenEtiqueta(988) . ":</i></strong>  </td>
                        <td  class='text-left' style='border:none;font-size:14px;color:#404040;'> $no_licencias_totales </td>
                                          
                      </tr>";
}
$informacion_plan_actual .= "
 
                                        <tr> 
                                            <td  class='text-right' style='border:none;font-size:14px;color:#404040;'><strong><i>" . ObtenEtiqueta(990) . ":</i></strong>  </td>
                                              <td  class='text-left' style='border:none;font-size:14px;color:#404040;'> $no_licencias_disponibles</td>
                                            
                                        </tr>
                                        <tr> 
                                            <td  class='text-right' style='border:none;font-size:14px;color:#404040;'><strong><i>" . ObtenEtiqueta(989) . ":</i></strong>  </td>
                                              <td  class='text-left' style='border:none;font-size:14px;color:#404040;'> $no_licencias_usadas </td>
                                          
                                        </tr>

 ";




if ($fg_opcion == EnumOpcionRenovacion::AutoRenovacion) {


    #PresentaModal
    $ds_contenido = "
     
     <table class='table'>
     
        <tbody>
        
                <tr>
                <td colspan='2' class='text-center' > <h3 class='alert alert-info text-center'> " . ObtenEtiqueta(1597) . "</h3></td>
                </tr>
                
               
                
                $informacion_plan_actual  
        </tbody>
     </table>   
        ";
}

if ($fg_opcion == EnumOpcionRenovacion::ReducirLicencias) {


    $ds_contenido = "<div class='smart-form'>
                      
                             <div class='table-responsive'>
                                    <table class='table' border='0' width='100%' >
                                    <tbody>
                                    
                                    
                                    
                                        <tr>
                                             <td colspan='2' class='text-center' style='border:none;font-size:12px;color:#404040;' >
                                                   <h3 class='alert alert-info'> " . ObtenEtiqueta(1593) . "</h3>
                                             </td>
                                            
                                        </tr>
                                    
                                         
                                        $informacion_plan_actual
                                    

                                        <tr> 
                                            <td  class='text-right' style='border:none;font-size:14px;color:#404040;'>
                                            
                                                        <table width='100%'>
                                                            <tr>
                                                            <td width='50%'></td>
                                                            <td width='25%'>
                                                                <div class='form-group text-right' >
																
												                    <input style='padding: 3px 15px 6px 24px;' class='form-control spinner-left'  id='no_licencias' name='no_licencias' value='$no_licencias_totales' type='text'>
											                    </div>
                                                            
                                                            </td>
                                                            <tr>
                                                        </table>
                                            
                                            
                                            
                                            
                                            
                                            </td>
                                            <td  class='text-left' style='border:none;font-size:14px;color:#404040;'>" . ObtenEtiqueta(1589) . " </td>
                                           
                                        </tr>
                                        
                                        
                                        <tr> 
                                            <td  class='text-right' style='border:none;font-size:14px;color:#404040; '> <strong><i>" . ObtenEtiqueta(988) . ":</i></strong></td>
                                            <td class='text-left' style='border:none;font-size:14px;color:#404040;'><div id='presenta_licencias_totales'>  </div> </td>
                                          
                                        </tr>

                                        <tr>
										 <td colspan='2' style='border-top: 0px solid #ddd;!important'>
												<table width='100%' style='border:none;'>
													<tr>
													 <td width='20%' class='text-right' style='border:none;font-size:14px;color:#404040; '> <br/><br/><p><i class='fa fa-warning 'style='font-size:4em;color:#f1d600;'></i></p></td>
													 <td  class='text-justify' style='border:none;font-size:13px;color:red;'><br/>" . ObtenEtiqueta(1590) . " </td>
													
													</tr>
												</table>
										 <td>
										
                                           
                                           
                                            
                                        </tr>  
                                      
                                        
                                       
                                        
                                        
                                    </body>
                            
                                    </table>
                                 </div>

                         
                        
                         ";

    $ds_contenido .= "</form>";

    echo "
  <script>
  
      function ActualizaTotalCantidadLicencias(){
	                var licencias_actuales=document.getElementById('no_total_lic_actuales').value;
                    var valor_actual=document.getElementById('no_licencias').value;
					var opc=1;
	               //pasamos por ajax los valores y presentamos modal.
					 $.ajax({
						 type: 'POST',
						 url: 'site/actualiza_no_licencias.php',
						 data: 'no_total_licencias_actuales=' + licencias_actuales + 
							   '&no_usuario_adicional =' + valor_actual +
							   '&opc=' + opc,

						 async: true,
						 success: function (html) {
							 $('#presenta_licencias_totales').html(html);
						 }
					 });
	 
	 
	 
	 }
	 

  
 $(document).ready(function () {  
  
     $('#no_licencias').spinner( );
        $('#spinner-decimal').spinner({
            step: 0.01,
            numberFormat: 'n'
        });


        $('.ui-spinner-button').click(function () {
            $(this).siblings('input').change( );
        });
        
        
             $('#no_licencias').spinner().change(function () {

                    var licencias_actuales=document.getElementById('no_total_lic_actuales').value;
                    var valor_actual=$(this).spinner('value');
					
					
					
                     if (valor_actual <= 0) {

                        $('#no_licencias').val(0);
                    }
                    if(valor_actual>licencias_actuales){//el valor simepre debe ser menor ala licencias totales actuales del instituto.
                    
                        $('#no_licencias').val(licencias_actuales)
                    }
					
					ActualizaTotalCantidadLicencias();
					
					
					
					
					
					
            
              });
            
            
        

     ActualizaTotalCantidadLicencias(); 
    
    }); 


 

	
    
	
  </script>
  
  
  ";
} else {

    echo "<input type='hidden' value='' id='no_licencias'>";
}

if ($fg_opcion == EnumOpcionRenovacion::CambioPlan) {



    $fg_plan = ObtenPlanActualInstituto($fl_instituto);

    // $fg_plan="A";

    if ($fg_plan == 'A') {
        $check_anio = " ";
        $check_mes = "";
        $disabled_anio = "";
        $disabled_mes = "";
    } else {
        $check_anio = "";
        $check_mes = " ";
        $disabled_anio = "";
        $disabled_mes = "";
    }



    $ds_contenido = "
     
     <table class='table'>
     
        <tbody>
        
                <tr>
                <td colspan='2' class='text-center' > <h3 class='alert alert-info text-center'> " . ObtenEtiqueta(1594) . "</h3></td>
                </tr>
        
        
           $informacion_plan_actual
               
        ";


    if ($fg_plan == 'A') {


        $ds_contenido .= "


										<tr> 
                                            <td  class='text-right' style='border:none;font-size:14px;color:#404040;'><strong><i>" . ObtenEtiqueta(1655) . ":</i></strong>  </td>
                                              <td  class='text-left' style='border:none;font-size:14px;color:#404040;'>   
															<div class='inline-group' style='margin-left:5px !important; margin-top:-10px !important;'>
														<label class='radio hidden'>
															<input name='radio-inline' $check_anio type='radio' $disabled_anio  id='checkanio' />
															<i></i>" . ObtenEtiqueta(1503) . "</label>
														<label class='radio'>
															<input name='radio-inline' $check_mes type='radio' $disabled_mes id='checkmes'   />
															<i></i>" . str_uso_normal(ObtenEtiqueta(1502)) . "</label>
                                                            </div>


											  </td>
                                           
                                        </tr>
		";
    } else {

        $ds_contenido .= "


										<tr> 
                                            <td  class='text-right' style='border:none;font-size:14px;color:#404040;'><strong><i>" . ObtenEtiqueta(1655) . ":</i></strong>  </td>
                                              <td  class='text-left' style='border:none;font-size:14px;color:#404040;'>   
															<div class='inline-group' style='margin-left:5px !important; margin-top:-10px !important;'>
														<label class='radio '>
															<input name='radio-inline' $check_anio type='radio' $disabled_anio  id='checkanio' />
															<i></i>" . ObtenEtiqueta(1503) . "</label>
														<label class='radio hidden'>
															<input name='radio-inline' $check_mes type='radio' $disabled_mes  id='checkmes' />
															<i></i>" . ObtenEtiqueta(1502) . "</label>
                                                            </div>


											  </td>
                                           
                                        </tr>
		";
    }






    $ds_contenido .= "        
        
        </tbody>
        </table>
     
     
     
     
     ";
}

if($fg_opcion==EnumOpcionRenovacion::CongelarPlanServicio){
    
    $ds_contenido.="<div class='row'>";
    $ds_contenido.="    <div class='col-md-12 text-center'>";
    $ds_contenido.="        <i class='fa fa-credit-card' aria-hidden='true' style='font-size:75px;'></i> <br><br> ";
    $ds_contenido.="             ".str_uso_normal(ObtenEtiqueta(2645))."";
    $ds_contenido.="    </div>";
    $ds_contenido.="</div>";


}
if($fg_opcion==EnumOpcionRenovacion::DescongelarPlanServicio){
    
    $ds_contenido.="<div class='row'>";
    $ds_contenido.="    <div class='col-md-12 text-center'>";
    $ds_contenido.="        <i class='fa fa-credit-card' aria-hidden='true' style='font-size:75px;'></i> <br><br> ";
    $ds_contenido.="             ".str_uso_normal(ObtenEtiqueta(2648))."";
    $ds_contenido.="    </div>";
    $ds_contenido.="</div>";

}



if ($fg_opcion == EnumOpcionRenovacion::CancelacionPlanServicio) {

    $ds_contenido = " <style>
     .checkbox label::before {
         border: 0px solid #cccccc !important;
     }
     </style>
     
     
        <table class='table'>
     
        <tbody>
        
                <tr>
                <td colspan='3' class='text-center' > <h3 class='alert alert-info'> " . ObtenEtiqueta(1596) . "</h3></td>
                </tr>
        
        
                <tr>
                <td colspan='3' class='text-center' style='border:none;font-size:15px;color:#404040;'>  <label class='control-label'><i>" . ObtenEtiqueta(1549) . "</i></strong><br/> $nb_usuario </label>  </td>
                
                
                </tr>
                
                
                <tr>
                <td style='border:none;'>&nbsp;</td>
                <td style='border:none;'>
                
                                            <div class='form-group text-left'>
													
													<div class='col-md-12 text-left'>
														
														<div class='checkbox'>
															<label style='width:100% !important;'>
															  <input class='checkbox style-0' type='checkbox' id='check1' onClick='HabilitaBotonCancelarCuenta();'>
															  <span style='width: 100%; text-align: left;'>" . ObtenEtiqueta(1600) . "</span>
															</label>
														</div>
				
														<div class='checkbox'>
															<label style='width:100% !important;'>
															  <input class='checkbox style-0' type='checkbox' id='check2' onClick='HabilitaBotonCancelarCuenta();'>
															  <span style='width: 100%; text-align: left;'>" . ObtenEtiqueta(1601) . "</span>
															</label>
														</div>
				
														<div class='checkbox'>
															<label style='width:100% !important;'>
															  <input class='checkbox style-0' type='checkbox' id='check3' onClick='HabilitaBotonCancelarCuenta();'>
															  <span style='width: 100%; text-align: left;'>" . ObtenEtiqueta(1602) . "</span>
															</label>
														</div>
														
														
				
													</div>
												</div>
                
                
                </td>
                <td style='border:none;'>&nbsp;</td>
                
                </tr>
                
                
                <tr>
                <td>&nbsp;</td>
                <td class='text-left' style='border:none;'>
                        <table width='100%'>
                        <tr><td><br/><i class='fa fa-exclamation-triangle' aria-hidden='true' style='font-size:6em;color:#FD3A3A;'></i></td>
                        <td> <font color='red'><i>" . ObtenEtiqueta(1599) . "</i></font></td></tr>
                        </table>
                
                
                      </td>
                <td class='text-left'>    </td>
                
                
                </tr>
                
        
        </tbody>
        </table>
     
     
     
     ";
}





?>

<input type="hidden" name="fg_opcion_renovacion" id="fg_opcion_renovacion" value="<?php echo $fg_opcion; ?>" />
<input type="hidden" name="fg_tipo_plan_actual" id="fg_tipo_plan_actual" value="<?php echo $fg_tipo_plan ?>" />
<input type="hidden" name="no_total_lic_actuales" id="no_total_lic_actuales" value="<?php echo $no_licencias_totales ?>" />
<input type="hidden" name="no_lic_usadas" id="no_lic_usadas" value="<?php echo $no_licencias_usadas ?>" />
<input type="hidden" name="no_lic_disponibles" id="no_lic_disponibles" value="<?php echo $no_licencias_disponibles ?>" />
<div id="redirigir"></div>
<?php

if (($fg_opcion == EnumOpcionRenovacion::ReducirLicencias) || ($fg_opcion == EnumOpcionRenovacion::CancelacionPlanServicio) || ($fg_opcion == EnumOpcionRenovacion::CambioPlan) || ($fg_opcion == EnumOpcionRenovacion::AutoRenovacion)||($fg_opcion == EnumOpcionRenovacion::CongelarPlanServicio)||($fg_opcion == EnumOpcionRenovacion::DescongelarPlanServicio)) {


    if (($fg_opcion == EnumOpcionRenovacion::CancelacionPlanServicio) || ($fg_opcion == EnumOpcionRenovacion::CambioPlan)) {

        $fg_disabled = "disabled";
    }
    ?>



    <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal" id="presenta_opc">
        Launch demo modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" id="cerrar" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center " id="myModalLabel" style="font-size:23px;color:#404040;"><i class="fa fa-refresh " style="color:#404040;" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(998); ?></h4>
                </div>

                <div class="modal-body ">


                    <?php

                        echo $ds_contenido;
                        ?>





                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="cerrar_modal">Cancelar</button>
                    <button type="button" class="btn btn-primary <?php echo $fg_disabled ?> " id="envio_boton" Onclick="javascript:AplicaOpcionRenovacion();"><i class="fa fa-check-circle"></i> Accept</button>


                </div>
            </div>
        </div>
    </div>



    <script>
        document.getElementById('presenta_opc').click(); //clic automatico que se ejuta y sale modal




        function HabilitaBotonCancelarCuenta() {


            if ($('#check1').is(':checked')) {
                var chek1 = 1;
            }

            if ($('#check2').is(':checked')) {
                var chek2 = 1;
            }

            if ($('#check3').is(':checked')) {
                var chek3 = 1;


            }


            if ((chek1 == 1) && (chek2 == 1) && (chek3 == 1)) {

                $('#envio_boton').removeClass('disabled');

            } else {
                $('#envio_boton').addClass('disabled');

            }
            //var fg_opcion_renovacion = document.getElementById('fg_opcion_renovacion').value;


        }




        function Redirigir() {

            var fg_redirigir = 1;

            $.ajax({
                type: 'POST',
                url: 'site/finaliza_opc_renovacion.php',
                data: 'fg_redirigir=' + fg_redirigir,

                async: true,
                success: function(html) {
                    $('#redirigir').html(html);



                }
            });

        }


        function AplicaOpcionRenovacion() {



            var fg_opcion_renovacion = document.getElementById('fg_opcion_renovacion').value;
            var fl_instituto = document.getElementById('fl_instituto').value;
            var fg_tipo_plan_actual = document.getElementById('fg_tipo_plan_actual').value;

            var no_total_lic_actuales = document.getElementById('no_total_lic_actuales').value;

            var no_licencias_eliminar = document.getElementById('no_licencias').value;
            // alert(fl_instituto);




            $.ajax({
                type: 'POST',
                url: 'site/finaliza_opc_renovacion.php',
                data: 'fl_instituto=' + fl_instituto +
                    '&fg_tipo_plan_actual=' + fg_tipo_plan_actual +
                    '&no_licencias=' + no_total_lic_actuales +
                    '&no_licencias_eliminar=' + no_licencias_eliminar +
                    '&fg_opcion_renovacion =' + fg_opcion_renovacion,

                async: true,
                success: function(html) {
                    $('#finaliza').html(html);

                    //document.getElementById('presenta_opc').click();//cierra modal
                   // Redirigir();
                }
            });



        }


        $('#checkanio').click(function() {

            $('#envio_boton').removeClass('disabled');
        });

        $('#checkmes').click(function() {
            $('#envio_boton').removeClass('disabled');
        });
    </script>





<?php

}

/*
  if(($fg_opcion==EnumOpcionRenovacion::AutoRenovacion)) {
  


      
         echo"
         <a class='btn btn-success btn-sm hidden' href='index.php#site/node.php?node=155' id='redirigir_billing'><i class='fa fa-upload'></i> redirige</a>
         
         <script>  
			$(document).ready(function() {
		 
                 $.smallBox({
			      title: '".ObtenEtiqueta(1645)."',
			      content: \"<i class='fa fa-clock-o'></i> \",
			      color: \"#5F895F\",
			      iconSmall: \"fa fa-check bounce animated\",
			      timeout: 4000
			    });
			})   
             document.getElementById('redirigir_billing').click();//clic automatico que se ejuta y sale modal
                </script>     ";
          
          
    
      
      
  
  }
  */

?>