<?php 
# Libreria de funciones	
require("../lib/self_general.php");
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

$fg_opcion_renovacion=RecibeParametroNumerico('fg_opcion_renovacion_');
$fg_plan_actual=RecibeParametroHTML('fg_tipo_plan_actual');
$fg_redirigir=RecibeParametroNumerico('fg_redirigir');

$fg_tipo_plan_cancelar=RecuperaPlanActualAlumnoFame($fl_usuario);


//exit;

#Se cancela en stripe la info del cliente.
#Recuperamos el id del plan creado en stripe, para actualizar el monto y tarifa. y desues recuperalos en el cron a ejecutarse
$Query="SELECT id_plan_stripe,id_cliente_stripe,id_suscripcion_stripe,ds_email_stripe FROM k_current_plan_alumno WHERE fl_alumno=$fl_usuario ";
$row=RecuperaValor($Query);
$id_plan_creado_alumno=str_texto($row['id_plan_stripe']);
$id_custom_creado_alumno=str_texto($row['id_cliente_stripe']);
$id_suscripcion_creado_alumno=str_texto($row['id_suscripcion_stripe']);
$ds_email_custom=str_texto($row['ds_email_stripe']);   



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


  function Customer($customer)
  {
      try {
          return $id= \Stripe\Customer::retrieve($customer);
      }
      catch (Exception $e) {
          return 0;
      }
  }



			
			if($fg_opcion_renovacion==1){#Autorenovacion

                
                       $fe_terminacion_plan=ObtenFechaExpiracionPlanAlumnoFAME($fl_usuario);

                       #Elinamos los crones existentes y creamos uno donde se cancelara la cuenta del instituto.
                       EjecutaQuery("DELETE FROM k_cron_plan_fame_alumo WHERE fl_alumno=$fl_usuario");
               
               
                       #Realizamos el isert del registro 
                       $Query="INSERT INTO k_cron_plan_fame_alumno (fe_ejecucion,fl_alumno,fg_motivo_ejecucion,fe_creacion) ";                           
                       $Query.="VALUES('$fe_terminacion_plan',$fl_usuario,'1',CURRENT_TIMESTAMP) ";
                       $fl_cron=EjecutaInsert($Query);
               

                
			}
			#solo aplica para quienes tienen actualmente contratado un plan anual.
			if($fg_opcion_renovacion==2){ #Cambio Plan
			   
				 #cambia plan mensual /Anual
				// CambiarPlanMensualAnual($fl_instituto,$fg_plan_actual,$fl_usuario);
				
				
			}




			if($fg_opcion_renovacion==3){ #Cancelacion
				 
                
                
				    
                      if($fg_tipo_plan_cancelar=='M'){
                      
                          #Elinamos los crones existentes y creamos uno donde se cancelara la cuenta del instituto.
                          $Query="SELECT fl_current_plan_alumno,fe_periodo_final FROM k_current_plan_alumno WHERE fl_alumno=$fl_usuario ";
                          $row=RecuperaValor($Query);
                          $fl_current_plan_alumno=$row[0];
                          $fe_final_periodo_forzado=$row[1];  
                          
                          EjecutaQuery("DELETE FROM k_cancelacion_plan_alumno WHERE fl_usuario=$fl_usuario AND fl_current_plan=$fl_current_plan_alumno ");
 
                          #Se inserta un registro donde establce que el usuario cancelo_plan, pero los pagos seguiran manteniendose hasta finalizar un año.
                          
                          $Query="INSERT INTO k_cancelacion_plan_alumno(fl_usuario,fl_current_plan, fe_cancelacion,fe_creacion,fe_ultmod) ";
                          $Query.="VALUES($fl_usuario, $fl_current_plan_alumno,'$fe_final_periodo_forzado',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                          $fl_cancelacion=EjecutaInsert($Query);

                          EjecutaQuery("UPDATE k_current_plan_alumno SET fg_cancelado='1' WHERE fl_alumno=$fl_usuario ");
                           
                      
                          
                          
                      
                      }
                
               
                      
                
                
                      if($fg_tipo_plan_cancelar=='A'){
                
                
				
				                #Elinamos los crones existentes y creamos uno donde se cancelara la cuenta del instituto.
                                //EjecutaQuery("DELETE FROM k_cron_plan_fame_alumo WHERE fl_alumno=$fl_usuario");
                
                                #Se desactiva su cuenta del sistema.
                                EjecutaQuery("UPDATE c_usuario SET fg_activo='0' WHERE fl_usuario=$fl_usuario");
                
                                #Elinamos los crones existentes y creamos uno donde se cancelara la cuenta del instituto.
                                $Query="SELECT fl_current_plan_alumno FROM k_current_plan_alumno WHERE fl_alumno=$fl_usuario ";
                                $row=RecuperaValor($Query);
                                $fl_current_plan_alumno=$row[0];
                              //  EjecutaQuery("DELETE FROM k_current_plan_alumno WHERE fl_alumno=$fl_usuario");
                              //  EjecutaQuery("DELETE FROM k_admin_pagos_alumno WHERE current_plan=$fl_current_plan_alumno ");
                                EjecutaQuery("UPDATE k_current_plan_alumno SET fg_cancelado='1' WHERE fl_alumno=$fl_usuario ");
                        }
               
                
                      #Veridficva si existe el customer.
                      $customer= Customer($id_custom_creado_alumno);
                      $tieneplan=getPlan($id_plan_creado_alumno);
                      $getSucipcion=getSubscripcion($id_suscripcion_creado_alumno);
                      $status_suscripcion=$getSucipcion->status;

                      
                          
                        if((!empty($getSucipcion))&&($status_suscripcion<>'canceled')){

                              $id_suscripcion_creado_alumno=$getSucipcion->id;

                              #1 Se cancela la suscripcion del instituto.
                              $subscription = \Stripe\Subscription::retrieve($id_suscripcion_creado_alumno);
                              $subscription->cancel();
                          }
                          if(!empty($tieneplan)){
                              #1.Elimina Plan actual
                              $plan = \Stripe\Plan::retrieve($id_plan_creado_alumno);
                              $plan->delete();

                          }

				
			 }

            if($fg_opcion_renovacion==4){
                
                if(!empty(getSubscripcion($id_suscripcion_creado_alumno))){
                
                    \Stripe\Subscription::update(
                         ''.$id_suscripcion_creado_alumno.'',
                         [
                           'pause_collection' => [
                             'behavior' => 'void',
                           ],
                         ]
                       );
                
                    #Pasan a estatus inactivos todos los usuarios de este instituto.
                    $Query="UPDATE k_current_plan_alumno SET fg_status='F' WHERE fl_alumno=$fl_usuario ";
                    EjecutaQuery($Query);

                }


            }

            if($fg_opcion_renovacion==5){
                
                if(!empty(getSubscripcion($id_suscripcion_creado_alumno))){
                    
                    \Stripe\Subscription::update(
                      ''.$id_suscripcion_creado_alumno.'',
                      [
                        'pause_collection' => '',
                      ]
                    );
                    
                    #Pasan a estatus inactivos todos los usuarios de este instituto.
                    $Query="UPDATE k_current_plan_alumno SET fg_status=null WHERE fl_alumno=$fl_usuario ";
                    EjecutaQuery($Query);

                }
            }
			 





				
?>
				

<?php

if($fg_opcion_renovacion==3){#CANCELACION

    
    echo"<a class='btn btn-success btn-sm hidden' href='index.php#site/node.php?node=182' id='redirigir_inicio'><i class='fa fa-upload'></i> redirige</a>";

    echo"        <script>
							location.reload();
                            
                  </script>          
    ";
}else{



    if(($fg_opcion_renovacion==1)||($fg_opcion_renovacion==2)||($fg_opcion_renovacion==4)||($fg_opcion_renovacion==5)){
				   
					echo"<a class='btn btn-success btn-sm hidden' href='index.php#site/node.php?node=182' id='redirigir_billing'><i class='fa fa-upload'></i> redirige</a>";

	
	
	?>

							<script>
							$(document).ready(function() {


								 $.smallBox({
										  title: "<?php echo ObtenEtiqueta(1645); ?>",
										  content: "<i class='fa fa-clock-o'></i> ",
										  color: "#5F895F",
										  iconSmall: "fa fa-check bounce animated",
										  timeout: 4000
										});
								})
		
							    location.reload();
								document.getElementById('redirigir_billing').click();//clic automatico que se ejuta y sale modal
	
	 
	
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
