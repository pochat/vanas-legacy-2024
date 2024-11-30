<?php 
# Libreria de funciones	
require("../lib/self_general.php");
require("../class/EnumOpcionRenovacion.php");
require_once('../lib/Stripe/Stripe/init.php');

# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
}


# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);
$fl_instituto=ObtenInstituto($fl_usuario);
# Variables Stripe
$secret_key = ObtenConfiguracion(112);



 
$fl_instituto=RecibeParametroNumerico('fl_instituto');
$fg_opcion_renovacion=RecibeParametroNumerico('fg_opcion_renovacion_');
$fg_plan_actual=RecibeParametroHTML('fg_tipo_plan_actual');
$no_licencias_eliminar=RecibeParametroNumerico('no_licencias_eliminar');
$fg_redirigir=RecibeParametroNumerico('fg_redirigir');

$success=false;

#Se cancela en stripe la info del cliente.
#Recuperamos el id del plan creado en stripe, para actualizar el monto y tarifa. y desues recuperalos en el cron a ejecutarse
$Query="SELECT id_plan_stripe,id_cliente_stripe,id_suscripcion_stripe,ds_email_stripe FROM k_current_plan WHERE fl_instituto=$fl_instituto  ";
$row=RecuperaValor($Query);
$id_plan_creado_instituto=str_texto($row['id_plan_stripe']);
$id_custom_creado_instituto=str_texto($row['id_cliente_stripe']);
$id_suscripcion_creado_instituto=str_texto($row['id_suscripcion_stripe']);
$ds_email_custom=str_texto($row['ds_email']);   



// create the charge on Stripe's servers - this will charge the user's card
	try {
  # set your secret key: remember to change this to your live secret key in production
  # see your keys here https://manage.stripe.com/account
  \Stripe\Stripe::setApiKey($secret_key);

  function getPlan($plan_id)
  {
      try {
          return $id= \Stripe\Plan::retrieve($plan_id);
      }
      catch (Exception $e) {
          return 0;
      }
  }
  function getSubscripcion($id_suscripcion)
  {
      try {
          return $id= \Stripe\Subscription::retrieve($id_suscripcion);
      }
      catch (Exception $e) {
          return 0;
      }
  }
			
			if($fg_opcion_renovacion==EnumOpcionRenovacion::AutoRenovacion){

               # Renueva plan actual del Instituto || mismas tarifas, mismo numero de licencias.
               RenovarPlanActualInstituto($fl_instituto,$fg_plan_actual,$fl_usuario);
              
               
               $fe_terminacion_plan=ObtenFechaFinalizacionContratoPlan($fl_instituto);
               #Se calcula la fecha posterios a terminacion del plan para ejecucion de cron
               $fe_final_periodo=strtotime('+0 day',strtotime($fe_terminacion_plan));
               $fe_ejecucion= date('Y-m-d',$fe_final_periodo);
               
               
               
               #elimnamos los crones existentes y creamos uno donde se cancelara la cuenta del instituto.
               EjecutaQuery("DELETE FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto");
               
               
               
               
               $Query="INSERT INTO k_cron_plan_fame (fe_ejecucion,fl_instituto,id_cliente_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias,fe_creacion) ";                           
               $Query.="VALUES('$fe_ejecucion',$fl_instituto,'','','','1','','RENOVACION', 0,0,CURRENT_TIMESTAMP) ";
               $fl_cron=EjecutaInsert($Query);
               
               
               #Verificamos si el Instituto ya dio la orden de cancelar su cuenta den Fame.
               $Query="SELECT fg_motivo_pago FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto ";
               $row=RecuperaValor($Query);
               $fg_motivo=$row[0];
               
               if(($fg_motivo<>EnumOpcionRenovacion::CancelacionPlanServicio)){
                   echo"<script> $(document).ready(function () {
                      $('#msg_cancel').addClass('hidden');// 
                      
                       });
                       </script>";
               }
               $success=true; 
			}
			#solo aplica para quienes tienen actualmente contratado un plan anual.
			if($fg_opcion_renovacion==EnumOpcionRenovacion::CambioPlan){
			   
				 #cambia plan mensual /Anual
				 CambiarPlanMensualAnual($fl_instituto,$fg_plan_actual,$fl_usuario);
				
				 $success=true; 
			}




			if($fg_opcion_renovacion==EnumOpcionRenovacion::ReducirLicencias){

                     $no_licencias_totales_actuales=ObtenNumLicencias($fl_instituto);
                    
					 #Obtiene el numero de licencas que se van a eliminar.   
					 $no_licencias_eliminar=$no_licencias_totales_actuales-$no_licencias_eliminar ;
                     $tot_update=$no_licencias_totales_actuales-$no_licencias_eliminar;
                     

                     
					 #Reduce Licencias.     
					 ReducirLicenciasPlanActualInstituto($fl_instituto,$fg_plan_actual,$no_licencias_eliminar,$fl_usuario);
			   
			         #Aplica cambiso stripre misma tarifa
                     #Actualizmos tarifa en strippe
                     $update_plan = \Stripe\Subscription::retrieve($id_suscripcion_creado_instituto);
                     $update_plan->quantity = $tot_update;
                     $update_plan->save();
			   
                     $success=true; 
			 
			 }
 
            if($fg_opcion_renovacion==EnumOpcionRenovacion::CongelarPlanServicio){
                $fg_redirigir=1;

                if(!empty(getSubscripcion($id_suscripcion_creado_instituto))){
                    
                    \Stripe\Subscription::update(
                      ''.$id_suscripcion_creado_instituto.'',
                      [
                        'pause_collection' => [
                          'behavior' => 'void',
                        ],
                      ]
                    );


                    #Pasan a estatus inactivos todos los usuarios de este instituto.
                    $Query="UPDATE k_current_plan SET fg_estatus='F' WHERE fl_instituto=$fl_instituto ";
                    EjecutaQuery($Query);

                    $Query="UPDATE c_usuario SET fg_activo='0' WHERE fl_instituto=$fl_instituto AND fl_perfil_sp<>".PFL_ADMINISTRADOR." ";
                    EjecutaQuery($Query);
                    $success=true; 
                }else{
                    $success=false;
                }
            }
            if($fg_opcion_renovacion==EnumOpcionRenovacion::DescongelarPlanServicio){
                $fg_redirigir=1;

                if(!empty(getSubscripcion($id_suscripcion_creado_instituto))){
                    
                    \Stripe\Subscription::update(
                       ''.$id_suscripcion_creado_instituto.'',
                       [
                         'pause_collection' => '',
                       ]
                     );


                    #Pasan a estatus inactivos todos los usuarios de este instituto.
                    $Query="UPDATE k_current_plan SET fg_estatus='A' WHERE fl_instituto=$fl_instituto ";
                    EjecutaQuery($Query);

                    $Query="UPDATE c_usuario SET fg_activo='1' WHERE fl_instituto=$fl_instituto ";
                    EjecutaQuery($Query);
                    $success=true; 
                }else{
                    $success=false;
                }
            }

			if($fg_opcion_renovacion==EnumOpcionRenovacion::CancelacionPlanServicio){
				
               
                if(!empty(getPlan($id_plan_creado_instituto))){
                    #1.Elimina Plan actual
                      $plan = \Stripe\Plan::retrieve($id_plan_creado_instituto);
                      $plan->delete();
                }
                if(!empty(getSubscripcion($id_suscripcion_creado_instituto))){
                    #2.Se cancela la suscripcion actual
                      $subscription = \Stripe\Subscription::retrieve($id_suscripcion_creado_instituto);
                      $subscription->cancel();
                }             
			    CancelarPlanActualInstituto($fl_instituto,$fl_usuario);		
                $success=true; 
			 }
			 



				#Cierrra Modal y redirige al home.
?>
				<script>
				   document.getElementById('cerrar').click();//clic automatico que se ejuta y sale modal
				</script>

<?php

if($fg_opcion_renovacion==EnumOpcionRenovacion::CancelacionPlanServicio){

    
    echo"<a class='btn btn-success btn-sm hidden' href='".SP_HOME."/login.php?err=3' id='redirigir_inicio'><i class='fa fa-upload'></i> redirige</a>";

    echo"        <script>
							document.getElementById('redirigir_inicio').click();
                            
                  </script>          
    ";
}else{



				if($fg_redirigir==1){
				   
					echo"<a class='btn btn-success btn-sm hidden' href='index.php#site/node.php?node=155' id='redirigir_billing'><i class='fa fa-upload'></i> redirige</a>";	
	?>
							<script>
							

							    <?php if($success==true){ ?>
							    $(document).ready(function() {
							        $.smallBox({
							            title: "<?php echo ObtenEtiqueta(1645); ?>",
							            content: "<i class='fa fa-clock-o'></i> ",
							            color: "#5F895F",
							            iconSmall: "fa fa-check bounce animated",
							            timeout: 4000
							        });
							    });
								document.getElementById('redirigir_billing').click();//clic automatico que se ejuta y sale modal
							    <?php }else{ ?>
							    $(document).ready(function () {
							        $.smallBox({
							            title: "An error occurred",
							            content: "<i class='fa fa-times-circle'></i> ",
							            color: "#b90009",
							            iconSmall: "fa fa-check bounce animated",
							            timeout: 4000
							        });
							    });
                                <?php } ?>
	 
	
							</script>
<?php

				 }
    }				
				
				
    $result['message'] = "";			
				
				
}catch (\Stripe\Error\ApiConnection $e) {
    // Network problem, perhaps try again.
    $e_json = $e->getJsonBody();
    $err = $e_json['error'];
    $result['error'] = $err['message'];
  } catch (\Stripe\Error\InvalidRequest $e) {
    // You screwed up in your programming. Shouldn't happen!
    $e_json = $e->getJsonBody();
    $err = $e_json['error'];
    $result['error'] = $err['message'];
  } catch (\Stripe\Error\Api $e) {
    // Stripe's servers are down!
    $e_json = $e->getJsonBody();
    $err = $e_json['error'];
    $result['error'] = $err['message'];
  } catch (\Stripe\Error\Base $e) {
    // Something else that's not the customer's fault.
    $e_json = $e->getJsonBody();
    $err = $e_json['error'];
    $result['error'] = $err['message'];
  }
  //echo json_encode((Object) $result);
				
				
?>
