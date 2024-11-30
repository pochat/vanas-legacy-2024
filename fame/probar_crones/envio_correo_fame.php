<?php
  
  include '/var/www/html/cronjob/st_tuition_payment_overdue.php';
	/*#librerias propias de FAME.
  require '/var/www/html/public_html/fame/lib/self_general.php';
  # Produccion para que funcione cronjob
  // require '/var/www/html/AWS_SES/PHP/com_email_func.inc.php';	
	// require '/var/www/html/AWS_SES/aws/aws-autoloader.php';
  // use Aws\Common\Aws;  

	
	# Include html parser
    # Produccion
//	require '/var/www/html/public_html/modules/common/new_campus/lib/simple_html_dom.php';  

	# Load config file
//	$aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');

	# Get the client from the builder by namespace
//	$client = $aws->get('Ses');

	$from = 'noreply@vanas.ca';
	
	
    #MJD recuperamos la fecha actual.    
    $fe_actual=ObtenerFechaActual();
    
	#porcentaje definido para enviar email de ayuda 
	$no_porcentaje_email_ayuda=ObtenConfiguracion(104);
    
	#Porcentaje definido para enviar email de recordatorioa de su expiracion en modo Trial. 
    $no_dias_anticipacion_email_recordatorio_expiracion_trial=ObtenConfiguracion(105);
    
    //$fe_actual="2017-02-01";
    
    
    


    #MJD recuperamos todos los institutos registrados en FAME y  que esten modo trial.
	$Query="SELECT fl_instituto,fl_usuario_sp,fe_creacion, fe_trial_expiracion,fg_activo FROM c_instituto ";
	$Query.="WHERE 1=1 AND fl_instituto <> 1 AND fg_tiene_plan='0' ";
	$rs = EjecutaQuery($Query);
	while($row=RecuperaRegistro($rs)){
            $fl_instituto=$row['fl_instituto'];
            $fe_creacion=$row['fe_creacion'];
            $fl_usuario=$row['fl_usuario_sp'];
	        $fe_expiracion_modo_trial=$row['fe_trial_expiracion'];
            $fg_activo=$row['fg_activo'];
	
            
    /**
     * Para pruebas 
     * 
     * 
     
     
     /*##para envio email de ayuda.
            $fe_creacion="2017-07-01";
            $fe_expiracion_modo_trial="2017-07-08";
            $fe_actual="2017-07-05";
            
           
            
         
            #Obtengo dias que faltan para culminar mi plan trial.
            $no_dias_faltan_terminar_plan=ObtenDiasRestantesPlan($fe_expiracion_modo_trial,$fe_actual);

            #Obtengo el no. de dias que comprende el periodo Trial.
            $no_dias_permitidos_modo_trial=ObtenDiasRestantesPlan($fe_expiracion_modo_trial,$fe_creacion);
            
            #realizamos operacion para saber cuantos dias llevo de uso en modo_trial.
            $no_dias_llevo_en_modo_trial=$no_dias_permitidos_modo_trial-$no_dias_faltan_terminar_plan;
            
            #Obtenemos el dia indicado para enviar el email de ayuda que es equivalente al pocentaje en c_configuracion (104). 
            $no_dia_uso_indicado_para_enviar_email_ayuda=number_format( ($no_porcentaje_email_ayuda * $no_dias_permitidos_modo_trial) / 100);
            
            
            
            #MJD Verificamos el porcentaje que lleva de uso el Instituto en modo Trial.
            $no_porcentaje_uso=CalculaTiempoUsoModoTrial($fl_usuario);
          

            
            
            
            #MJD Solo envia email de ayuda, alas instituciones que llevan la mitad de periodo de prueba(EMAIL de ayuda). 
            if($no_dia_uso_indicado_para_enviar_email_ayuda == $no_dias_llevo_en_modo_trial ) {
                    EnviaEmailAyuda($fl_usuario,$fl_instituto);#fg_motivo2

            } 
            #MJD Solo envia email de recordatorio envia email de recordatio que su plan esta proximo a vencer.
            if( $no_dias_faltan_terminar_plan == $no_dias_anticipacion_email_recordatorio_expiracion_trial) {

                   EnviaEmailCaducidadTrial($fl_usuario,$fl_instituto,$fe_expiracion_modo_trial);#fg_motivo3
						
                     
            }
            
            #Solo enviamos una vez el email para adquirir plan.
            #Envia correo para poder elegir/adquirir  un plan.alos instituciones que ya expiraron su plan.
            if($fe_expiracion_modo_trial < $fe_actual ){
                
                
                
                        #se genera una llave de acceso.
                        # Genera una nueva clave para la liga de acceso al contrato
                        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
                        for($i = 0; $i < 40; $i++)
                            $ds_cve .= substr($str, rand(0,62), 1);
                        $ds_cve .= date("Ymd").$no_envio;
                
                        #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
                        $no_codigo_confirmacion = $ds_cve;

                        #Verificamos que no haya registro previo de envio de email. para poder enviatr el correo.
                        $Query="SELECT COUNT(*) FROM k_envio_email_fame WHERE fl_instituto=$fl_instituto AND fg_motivo='4' ";
                        $row=RecuperaValor($Query);
                        $no_=$row[0];
                       
                        if($no_==0)  
                        EnviaEmailComprarPlan($fl_usuario,$fl_instituto,$no_codigo_confirmacion);#fg_motivo4
            
                
                
            }
            
        
        
	
            
            
            
	
	
	}	
    
    
   
    

    #Envio de email para aquellas instituciones que ya tiene un plan.
	
	#Se recupera todos los Institutos registrados y que esten activos en FAME.
	$Query2="SELECT fl_instituto,fl_usuario_sp,fe_creacion, fe_trial_expiracion,fg_activo FROM c_instituto ";
	$Query2.="WHERE 1=1 AND fl_instituto <> 1  AND fg_activo='1'  AND fg_tiene_plan='1' ";
	$rs2 = EjecutaQuery($Query2);
	while($row2=RecuperaRegistro($rs2)){
	      $fl_instituto=$row2[0];
	      $fl_usuario=$row2[1];
		  $fe_creacion=$row2[2];
		  $fe_expiracion_plan=$row2[3];
		  
		  
		  #Recuperamos el No. de dias, previo a su fechA de finalizacion
		  $no_dias_aviso_anticipacion=ObtenConfiguracion(109);

		  #se cacula cuantos dias restan para finalizar su plan.
		  $no_dias_faltan_terminar_plan=ObtenDiasRestantesPlan($fe_final_vigencia,$fe_actual);
		  
		  #Obtengo el no. de dias que comprende el periodo Trial.
          $no_dias_comprende_plan=ObtenDiasRestantesPlan($fe_final_vigencia,$fe_creacion);
          

		  if($no_dias_aviso_anticipacion==$no_dias_faltan_terminar_plan){
		  
			     #Envia notificacion de expiracion para instituciones que ya tiene un plan.
				 
				 EnviarEmailNotificacionExpiracionPlan($fl_instituto,$fl_usuario);
		  
		  
		  }
	
	
	
	
	}
	
	
	*/
?>
