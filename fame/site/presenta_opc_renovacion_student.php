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
 $fg_opcion=RecibeParametroNumerico('fg_opcion_');
 #Identificamos que tipo de plan tiene el Instituto.
 $fg_tipo_plan=RecuperaPlanActualAlumnoFame($fl_usuario);
 
 
 #Recuperamos informacion de su Plan.
 $Query="SELECT B.nb_plan, fg_plan,fe_periodo_inicial,fe_periodo_final,mn_total_plan 
                                            FROM k_current_plan_alumno A
                                            JOIN c_plan_fame B ON b.cl_plan_fame=A.cl_plan_fame 
                                            
                                            WHERE fl_alumno=$fl_usuario  ";
 $row=RecuperaValor($Query);
 $nb_plan_fame=str_texto($row[0]);
 $fg_plan=str_texto($row[1]);
 $mn_total_plan=$row[4];
 $fe_periodo_inicil_plan=str_texto($row['fe_periodo_inicial']);
 $fe_terminacion_plan=str_texto($row['fe_periodo_final']);
 
 #damos formato a fcha de terminacion del plan
 $date=date_create($fe_terminacion_plan);
 $fe_expiracion_plan=date_format($date,'F j, Y');
 
 if($fg_opcion==1){
         if($fg_plan=='A'){
             $fg_tipo_plan_actual=ObtenEtiqueta(1521);
             $mn_total_plan=number_format(($mn_total_plan/12),2);
             $mn_total_plan=  "$".$mn_total_plan." per month";
            
 
         }else{
             $fg_tipo_plan_actual=ObtenEtiqueta(1520);
             $mn_total_plan=  "$".number_format($mn_total_plan,2)." per month";
             
            
             
         }
 
 }
 
 if($fg_opcion==2){
 
 
         #Es para pasaber el cambio de plan
         if($fg_tipo_plan=="A"){
             $fg_tipo_plan=ObtenEtiqueta(1521);
             $fg_nuevo_plan="Monthly Plan";
         }else{
             $fg_tipo_plan=ObtenEtiqueta(1520);
             $fg_nuevo_plan="Annual Plan";
         }
 
 }
 
 
 if($fg_opcion==3){
 
        if($fg_tipo_plan=="A"){
        
            $date=date_create($fe_terminacion_plan);
            $fe_terminacion_plan_forzado_al_anio=date_format($date,'F j, Y');
            
        }else{
            
            #fecha de finalicion es forzozo a 1 año.
            
            $fe_terminacion_plan_forzado_al_anio=strtotime('+365 day',strtotime($fe_periodo_inicil_plan));               
            $fe_terminacion_plan_forzado_al_anio= date('Y-m-d', $fe_terminacion_plan_forzado_al_anio); 
            
            #damos formato a fcha de terminacion del plan
            $date=date_create($fe_terminacion_plan_forzado_al_anio);
            $fe_terminacion_plan_forzado_al_anio=date_format($date,'F j, Y');
        
        }
 
 
 }
 
 
 
    
 echo"<div id=\"finaliza\"></div>";    
 
 #Presenta Info basica del plan como son.

 $informacion_plan_actual="  
                                        <tr> 
                                            <td width='50%' class='text-right' style='border:none;font-size:14px;color:#404040; '><strong><i>".ObtenEtiqueta(984).":</i></strong></td>
                                            <td width='50%' class='text-left' style='border:none;font-size:14px;color:#404040;'>$fg_tipo_plan_actual </td>
                                            
                                        </tr>
                          ";    


 
 


 if($fg_opcion==1) {#Autorenew

     
     #PresentaModal
     $ds_contenido="
     
     <table class='table'>
     
        <tbody>
        
                <tr>
                <td colspan='2' class='text-center' > <h3 class='alert alert-info text-center'> ".ObtenEtiqueta(1597)."</h3></td>
                </tr>

                $informacion_plan_actual  
                
                <tr> 
                    <td width='50%' class='text-right' style='border:none;font-size:14px;color:#404040; '><strong><i>".ObtenEtiqueta(994).":</i></strong></td>
                    <td width='50%' class='text-left' style='border:none;font-size:14px;color:#404040;'>$mn_total_plan </td>
                                            
                </tr>
                
                
                <tr> 
                    <td width='50%' class='text-right' style='border:none;font-size:14px;color:#404040; '><strong><i>".ObtenEtiqueta(996).":</i></strong></td>
                    <td width='50%' class='text-left' style='border:none;font-size:14px;color:#404040;'>$fe_expiracion_plan </td>
                                            
                </tr>
                
                
        </tbody>
     </table>   
        ";
  
 }

/* 
 if($fg_opcion==2){

     
   
    $fg_plan=ObtenPlanActualInstituto($fl_instituto);
     
   // $fg_plan="A";
    
    if($fg_plan=='A'){
     $check_anio=" ";
     $check_mes="";
     $disabled_anio="";
     $disabled_mes="";
    
    
    }else{
        $check_anio="";
        $check_mes=" ";
        $disabled_anio="";
        $disabled_mes="";
        
    }
    
         
    
     $ds_contenido="
     
     <table class='table'>
     
        <tbody>
        
                <tr>
                <td colspan='2' class='text-center' > <h3 class='alert alert-info text-center'> ".ObtenEtiqueta(1594)."</h3></td>
                </tr>
        
        
           $informacion_plan_actual
               
        ";
		
		
		if($fg_plan=='A'){
		
		
		$ds_contenido.="


										<tr> 
                                            <td  class='text-right' style='border:none;font-size:14px;color:#404040;'><strong><i>".ObtenEtiqueta(1655).":</i></strong>  </td>
                                              <td  class='text-left' style='border:none;font-size:14px;color:#404040;'>   
															<div class='inline-group' style='margin-left:5px !important; margin-top:-10px !important;'>
														<label class='radio hidden'>
															<input name='radio-inline' $check_anio type='radio' $disabled_anio  id='checkanio' />
															<i></i>".ObtenEtiqueta(1503)."</label>
														<label class='radio'>
															<input name='radio-inline' $check_mes type='radio' $disabled_mes id='checkmes'   />
															<i></i>".ObtenEtiqueta(1502)."</label>
                                                            </div>


											  </td>
                                           
                                        </tr>
		";
		
		}else{
		
		$ds_contenido.="


										<tr> 
                                            <td  class='text-right' style='border:none;font-size:14px;color:#404040;'><strong><i>".ObtenEtiqueta(1655).":</i></strong>  </td>
                                              <td  class='text-left' style='border:none;font-size:14px;color:#404040;'>   
															<div class='inline-group' style='margin-left:5px !important; margin-top:-10px !important;'>
														<label class='radio '>
															<input name='radio-inline' $check_anio type='radio' $disabled_anio  id='checkanio' />
															<i></i>".ObtenEtiqueta(1503)."</label>
														<label class='radio hidden'>
															<input name='radio-inline' $check_mes type='radio' $disabled_mes  id='checkmes' />
															<i></i>".ObtenEtiqueta(1502)."</label>
                                                            </div>


											  </td>
                                           
                                        </tr>
		";
		
		
		
		}
		
		
		
		
	      
                
        $ds_contenido.="        
        
        </tbody>
        </table>
     
     
     
     
     ";
     
     
     
     
     
     
 }
 */


 if($fg_opcion==3){

     $ds_contenido=" <style>
     .checkbox label::before {
         border: 0px solid #cccccc !important;
     }
     </style>
     
     
        <table class='table'>
     
        <tbody>
        
                <tr>
                <td colspan='3' class='text-center' > <h3 class='alert alert-info'> ".ObtenEtiqueta(1596)."</h3></td>
                </tr>
        
        
                <tr>
                <td colspan='3' class='text-center' style='border:none;font-size:15px;color:#404040;'>  <label class='control-label'><i>".ObtenEtiqueta(1549)."</i></strong><br/> $nb_usuario </label>  </td>
                
                
                </tr>
                
                
                <tr>
                <td style='border:none;'>&nbsp;</td>
                <td style='border:none;'>
                
                                            <div class='form-group'>
													
													<div class='col-md-12'>
														
														<div class='checkbox'>
															<label>
															  <input class='checkbox style-0' type='checkbox' id='check1' onClick='HabilitaBotonCancelarCuenta();'>
															  <span>".ObtenEtiqueta(2127)."</span>
															</label>
														</div>
				
														<div class='checkbox'>
															<label>
															  <input class='checkbox style-0' type='checkbox' id='check2' onClick='HabilitaBotonCancelarCuenta();'>
															  <span>".ObtenEtiqueta(2128)."</span>
															</label>
														</div>
				
														<div class='checkbox'>
															<label>
															  <input class='checkbox style-0' type='checkbox' id='check3' onClick='HabilitaBotonCancelarCuenta();'>
															  <span>".ObtenEtiqueta(2129)."</span>
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
                        <td> <font color='red'><i>";
                 
    
         
         $etq=ObtenEtiqueta(2130);
         //$etq = str_replace("#fe_expiration_plan#", $fe_terminacion_plan_forzado_al_anio, $etq);  #no_dias_cuso
         
    
     
   $ds_contenido .=" $etq  </i></font></td></tr>
                        </table>
                
                
                      </td>
                <td class='text-left'>    </td>
                
                
                </tr>
                
        
        </tbody>
        </table>
     
     
     
     ";
     
     
     
     
 
 }
 
 if($fg_opcion==4){
     
     $ds_contenido.="<div class='row'>";
     $ds_contenido.="    <div class='col-md-12 text-center'>";
     $ds_contenido.="        <i class='fa fa-credit-card' aria-hidden='true' style='font-size:75px;'></i> <br><br> ";
     $ds_contenido.="             ".str_uso_normal(ObtenEtiqueta(2645))."";
     $ds_contenido.="    </div>";
     $ds_contenido.="</div>";

 }

 if($fg_opcion==5){
     
     $ds_contenido.="<div class='row'>";
     $ds_contenido.="    <div class='col-md-12 text-center'>";
     $ds_contenido.="        <i class='fa fa-credit-card' aria-hidden='true' style='font-size:75px;'></i> <br><br> ";
     $ds_contenido.="             ".str_uso_normal(ObtenEtiqueta(2648))."";
     $ds_contenido.="    </div>";
     $ds_contenido.="</div>";

 }


 

?>

<input type="hidden" name="fg_opcion_renovacion" id="fg_opcion_renovacion" value="<?php echo $fg_opcion; ?>"  />
<input type="hidden" name="fg_tipo_plan_actual" id="fg_tipo_plan_actual" value="<?php echo $fg_tipo_plan ?>" />

<div id="redirigir"></div>
<?php 

#Para desabilitar boton de acept en caso de que se muestre las opcion de cancelar o cambiar de plan.

if( ($fg_opcion==2)||($fg_opcion==3) ) {
     $fg_disabled="disabled";  
}else{
    $fg_disabled=" ";
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
                                    <h4 class="modal-title text-center " id="myModalLabel" style="font-size:23px;color:#404040;"><i class="fa fa-refresh " style  ="color:#404040;" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(998); ?></h4>
                                </div>

                                <div class="modal-body ">
																		 
                                    <?php 
                                    
                                    echo $ds_contenido;
                                    ?>

                                </div>
                                <div class="modal-footer text-center">
	                                                 <button type="button" class="btn btn-default" data-dismiss="modal" id="cerrar_modal">Cancelar</button>
                                                     <button type="button" class="btn btn-primary <?php echo $fg_disabled ?> "  id="envio_boton" Onclick="javascript:AplicaOpcionRenovacion();"><i class="fa fa-check-circle"></i> Accept</button>
         
                                                                                                                                 
                                </div>
                            </div>
                        </div>
                    </div>



<script>
    document.getElementById('presenta_opc').click();//clic automatico que se ejuta y sale modal




    function HabilitaBotonCancelarCuenta( ) {


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
        


    }


	
/*
    function Redirigir() {

        var fg_redirigir = 1;
        
        $.ajax({
            type: 'POST',
            url: 'site/finaliza_opc_renovacion_alumno.php',
            data: 'fg_redirigir=' + fg_redirigir,

            async: true,
            success: function (html) {
                $('#redirigir').html(html);

            }
        });

    }
*/

    function AplicaOpcionRenovacion() {

       

        var fg_opcion_renovacion = document.getElementById('fg_opcion_renovacion').value;
        var fg_tipo_plan_actual = document.getElementById('fg_tipo_plan_actual').value;
		
       
 
            $.ajax({
                type: 'POST',
                url:  'site/finaliza_opc_renovacion_alumno.php',
                data: 'fg_tipo_plan_actual='+fg_tipo_plan_actual+
                      '&fg_opcion_renovacion ='+fg_opcion_renovacion,

                async: true,
                success: function (html) {
                    $('#finaliza').html(html);

                   
                }
            });

            

    }


  /*  $('#checkanio').click(function() {     
	
	 $('#envio_boton').removeClass('disabled');
	});
	
	 $('#checkmes').click(function() { 
          $('#envio_boton').removeClass('disabled');
	 });
*/	

</script>




