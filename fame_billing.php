<?php

	#librerias propias de FAME.
  	/* require '/var/www/html/vanas/fame/lib/self_general.php';
	require '/var/www/html/vanas/fame/lib/Stripe/Stripe/init.php';
	require '/var/www/html/vanas/AD3M2SRC4/lib/tcpdf/config/lang/eng.php';
    require '/var/www/html/vanas/AD3M2SRC4/lib/tcpdf/tcpdf.php';*/
	
	#librerias propias de FAME.
     require 'fame/lib/self_general.php';
	 require 'fame/lib/Stripe/Stripe/init.php';
	 require 'AD3M2SRC4/lib/tcpdf/config/lang/eng.php';
     require 'AD3M2SRC4/lib/tcpdf/tcpdf.php';

	# Produccion para que funcione cronjob
	//require '/var/www/html/AWS_SES/PHP/com_email_func.inc.php';	
	//require '/var/www/html/AWS_SES/aws/aws-autoloader.php';
    //use Aws\Common\Aws;
    require '/var/www/html/AWS_SES/PHP/com_email_func.inc.php';	
	require '/var/www/html/AWS_SES/aws/aws-autoloader.php';
    use Aws\Common\Aws;

	# Include html parser
    # Produccion
	//require '/var/www/html/vanas/modules/common/new_campus/lib/simple_html_dom.php';
    require '/var/www/html/vanas/modules/common/new_campus/lib/simple_html_dom.php';

	# Load config file
	//$aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');
    $aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');

	# Get the client from the builder by namespace
	$client = $aws->get('Ses');
	
    $from = 'noreply@vanas.ca';
   
	# Variables Stripe
    $secret_key = ObtenConfiguracion(112);
    $currency=ObtenConfiguracion(113);

    #MJD Variables, de indetificacion
    #tabla k_cron_fame fg_motivo_pago
	# 1 -> En algun punto del periodo de suscripcion, el Instituo confirma que su plan actual se va autorenovar(defualt). 
    # 2 -> Se agregaron nuevas licencias, que corresponden a un  nuevo plan , con otro precio,y entonces se congela plan actual,se crea otro plan,nueva suscripcion, nuevas tarifas 	
	# 3 -> Se cambio de plan, entonces , se congela la suscripcion actual, se crea otro plan , otra nueva suscripcion , nuevas tarifas. 
	# 4 -> Se congela el plan actual,mo existe interes de renovacio y la isntitucion menciona que se va a cancelar su suscripcion a fame, se cancelan lospagos automatico en strippe
    # create the charge on Stripe's servers - this will charge the user's card
	try {
        
         # set your secret key: remember to change this to your live secret key in production
         # see your keys here https://manage.stripe.com/account
         \Stripe\Stripe::setApiKey($secret_key);	
	
        #Recuperamos la fecha actual. formato     
        $fe_actual=ObtenerFechaActual();
	
        /*
         * For debuging
         */ 
         #fecha en que finaliza su periodo 
        //$fe_actual="2017-10-21";
	
	    #Verificamos los registros existentes de la BD.
	    $Query="SELECT fe_ejecucion,fl_instituto,id_cliente_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias,fg_cambio_plan ";
        $Query.="FROM k_cron_plan_fame WHERE 1=1 ";
		$rs1 = EjecutaQuery($Query);
		for($i2=1;$rowa=RecuperaRegistro($rs1);$i2++){
			
			$fe_ejecucion=$rowa[0];
            $fl_instituto=$rowa[1];
            $id_cliente_stripe=$rowa[2];
            $id_plan_creado_instituto=$rowa[3];
            $id_suscripcion_creado_instituto=$rowa[4];
            $fg_motivo_pago=$rowa[5];
            $ds_email_custom=$rowa[6];
            $ds_descripcion_pago=$rowa[7];
            $mn_monto_por_licencia=$rowa[8];
            $no_licencias_compradas=$rowa[9];
            $fg_cambio_plan=$rowa[10];
            
            #Recuperamos el email actual del Instituto EnFAME
            $Query="SELECT B.ds_email,B.fl_usuario FROM c_instituto A 
                                    JOIN c_usuario B ON B.fl_usuario=A.fl_usuario_sp 
                                    WHERE A.fl_instituto=$fl_instituto ";
	        $row=RecuperaValor($Query);
            $ds_email_custom=str_texto($row[0]);
            $fl_usuario=$row[1];
            
            #Recuperamos el plan actual del Instituto. y de ahi consultamos sus licneicas usadas, 
            $Query="SELECT fl_current_plan,fl_princing,no_total_licencias,no_licencias_usadas,no_licencias_disponibles,fg_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
            $row=RecuperaValor($Query);
            $fl_current_plan_actual=$row[0];
            $fl_princing=$row[1];
            $no_total_licencias_=$row[2];
            $no_licencias_usadas_=$row[3];
            $no_licencias_disponibles=$row[4];
            $fg_plan_actual_instituto=$row[5];
            
            #Recupermaos las claves que tiene actualmente el Instituto y que estan ligadas a STRIPe
            #Recuperamos el id del plan creado en stripe, para actualizar el monto y tarifa. y desues recuperalos en el cron a ejecutarse
            $Query2="SELECT id_plan_stripe,id_cliente_stripe,id_suscripcion_stripe,ds_email_stripe FROM k_current_plan WHERE fl_instituto=$fl_instituto  ";
            $row2=RecuperaValor($Query2);
            $id_plan_actual_instituto=str_texto($row2[0]);
            $id_custom_actual_instituto=str_texto($row2[1]);
            $id_suscripcion_actual_instituto=str_texto($row2[2]);
            $ds_email_custom_actual=str_texto($row2[3]);   
            
			
			echo"<script>alert('entro foreach');</script>";
			
			
            /**
             * 
             * #PRUEBAS
             */ 
            
           // $fe_ejecucion="2017-10-21";
           // $fe_actual="2017-10-21";
            
            
								#Recupermaos tarifas actuales asignadas al Instituto y que corresppnden al precios actualizados. 
                               #identificamos en que rango se encuentra,PARA SABER  NUEVO PLAN y nuevas tarifas.
                                $Querym="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
                                $rs = EjecutaQuery($Querym);
                                for($i=1;$rowm=RecuperaRegistro($rs);$i++){
                                    
                                    $mn_rango_ini= $rowm[1];
                                    $mn_rango_fin= $rowm[2];

                                        if(( $no_licencias_compradas >=$mn_rango_ini)&&($no_licencias_compradas<=$mn_rango_fin) ){
                                        
                                            $fl_princing=$rowm[0];
                                            #Recuperamos costos segun el plan obtenido del nuevo rango de licencias.
                                            $Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princing ";
                                            $row=RecuperaValor($Query);
                                            $mn_costo_mensual=$row[0];
                                            $mn_descuento_anual=$row[2]; 
                                            $mn_descuento_mensual=$row[3];
                                            
                                        
                                            if($fg_cambio_plan='A'){
                                                $mn_monto_por_licencia= $row[1] * 12 ;
                                                $mn_descuentoDB=$mn_descuento_anual;
                                            }else{
                                                $mn_monto_por_licencia= $row[0] ;
                                                $mn_descuentoDB=$mn_descuento_mensual;
                                            }
                                            
                                            if($fg_motivo_pago==2){
                                            
                                               if($fg_plan_actual_instituto=='A')
                                                   $mn_monto_por_licencia=$mn_costo_anual;
                                               else 
                                                   $mn_monto_por_licencia=$mn_costo_mensual;
                                            
                                            
                                            }
                                            
                                            
                                            
                                        }
                                    
                                    
                                    
            
                                }
            
            
                                
                                
                                
                                
                                
                                
            #solo ejecuta si la fecha actual corresponde ala fecha de ejecucucion
            if($fe_ejecucion==$fe_actual){
            
						echo"<script>alert('entro depsues de igaualacion');</script>";
                
                             #Renovacion plan
                             if($fg_motivo_pago==1){ #Cancelamos su susbscripcion actual y generamos una nueva con las tarifas existentes en FAME.
                                 
                                 #Obtenemos fecha actual :
                                 $Query = "Select CURDATE() ";
                                 $row = RecuperaValor($Query);
                                 $fe_actual = str_texto($row[0]);
                                 $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                                 $fe_actual= date('Y-m-d',$fe_actual);
                                 
								 echo"<script>alert('entro fg_motio_pago =fecha_actual y va entrar a cancelar plan');</script>";
								
								$id_suscripcion_actual_instituto="";
								
								 #2.Se cancela la suscripcion actual
							    $subscription = \Stripe\Subscription::retrieve($id_suscripcion_actual_instituto);
						        $subscription->cancel();
								 
								 echo"<script>alert('se cancelao la suscripcion');</script>";
								 #Generamos el nuevo plan.
								 $Query="SELECT ds_instituto,fl_usuario_sp FROM c_instituto WHERE fl_instituto=$fl_instituto ";
								 $row=RecuperaValor($Query);
								 $nb_instituto=str_texto($row[0]);
								 $fl_usuario_instituto=$row[1];
								 $ds_plan=ObtenNombrePlanFame($fl_instituto);
								 $rand=rand(5,1000);
								 
								 $Queryem="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_instituto ";
								 $r=RecuperaValor($Queryem);
								 $ds_email_actual_instituto=$r[0];
								 
								 #Obtenemos licencias actuales y licencias disponibles
                                 $no_licencias_totales_actuales=ObtenNumLicencias($fl_instituto);
                                 $no_licencias_disponibles=ObtenNumLicenciasDisponibles($fl_instituto);
								 
								 
								 #Recupermaos en que rango de precio se encuentra actualmente.
								 $Querya="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
                                 $rs = EjecutaQuery($Querya);
								 for($i=1;$rowa=RecuperaRegistro($rs);$i++){
									 $mn_rango_ini= $rowa[1];
									 $mn_rango_fin= $rowa[2];
								 
								    if(( $no_licencias_totales_actuales >=$mn_rango_ini)&&($no_licencias_totales_actuales<=$mn_rango_fin) ){
									     $fl_princing_actual=$rowa[0];
										 #Recuperamos costos segun el plan obtenido del nuevo rango de licencias.
										 $Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princing_actual ";
										 $row=RecuperaValor($Query);
										 $mn_costo_mensual=$row[0];
										 $mn_costo_anual=$row[1];
										 $mn_descuento_anual=$row[2]; 
										 $mn_descuento_mensual=$row[3];
										 
										 if($fg_plan_actual_instituto=='M'){
										        $mn_monto_por_licencia=$mn_costo_mensual;
                                                $mn_descuentoDB=$mn_descuento_mensual;
										 }
										 
										 if($fg_plan_actual_instituto=='A'){
										        $mn_monto_por_licencia=$mn_costo_anual;
                                                $mn_descuentoDB=$mn_descuento_anual;
										 }
										 
									
									}
								 
								 
								 
								 
								 }
								 
								  
								 if($fg_plan_actual_instituto=='M'){
										$interval="month";
                                        $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                                        $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                                        $mn_costo_x_licencia=Conv_Dollars_Stripe($mn_monto_por_licencia);
                                        $mn_total_pagarBD=$mn_monto_por_licencia * $no_licencias_totales_actuales;
										$ds_descripcion_pago="Essentials Plan-Monthly ".$no_licencias_totales_actuales." licenses";

								 }
								if($fg_plan_actual_instituto=='A'){
										$interval="year";
                                        $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                                        $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                                        $mn_costo_x_licencia=Conv_Dollars_Stripe(($mn_monto_por_licencia*12));//por doce los 12 meses.esto es para stripe
                                              
                                        $mn_total_pagarBD=($mn_monto_por_licencia * $no_licencias_totales_actuales )*12;
										$ds_descripcion_pago="Essentials Plan-Annual ".$no_licencias_totales_actuales." licenses"; 

								 }

								#Recuperamos el estado/provincia del usuario para determina el monto del tax.
								$mn_tax_p=Tax_Can_User($fl_usuario_instituto);
								$mn_porcentaje_tax_=$mn_tax_p*100;
								if($mn_tax_p>0){
									$mn_tax = $mn_total_pagarBD * $mn_tax_p;
									$mn_taxBD=$mn_tax;
									$mn_amount = $mn_total_pagarBD + $mn_tax;
									$mn_tax=number_format($mn_tax,2);
								}else{
                                    $mn_taxBD=0;
                                    $mn_amount = $mn_total_pagarBD;
                                    $mn_tax=0;
									
								}


								 echo"<script>alert('va entrar cancelar_plan');</script>";
								  
								#Se cra el plan 
								$plan = \Stripe\Plan::create(array(
								   "name" => $nb_plan,
								   "id" => $id_plan,
								   "interval" => $interval,
								   "currency" => $currency,
								   "amount" => $mn_costo_x_licencia
								   )
								);
								
								#Recupermaos el id del plan generado
								$id_plan=$plan->id;
								$id_plan_creado=$plan->id; 
								echo"<script>alert('va entrar generar la suscripcion');</script>";  
                                #2. se crea nueva suscripcion 
							    $subscription= \Stripe\Subscription::create(array(
									  "customer" => $id_custom_actual_instituto,
									  "plan" => $id_plan_creado,
									  "quantity"=>$no_licencias_totales_actuales,
									  "tax_percent" => $mn_porcentaje_tax_,
									  
									));
								$id_suscripcion=$subscription->id;

								#Verificamos el eveno creado en Strippe, para despues poder recuperar elid del pago que se realizo. y actualizar su descripcion.
								$event = \Stripe\Event::all(array("limit" => 2));
								$id_charges=$event->data;    
								$id_evento=$id_charges[1]->data;
								$id_charge=$id_evento['object']->id;
								$id_invoice=$id_evento['object']->invoice;  
								#Actualizamos la descripcion del pago que se realizao.
								$ch = \Stripe\Charge::retrieve($id_charge);
								$ch->description = $ds_descripcion_pago;
								$ch->save();
								
								echo"<script>alert('se genera correctamnete');</script>";
								
                                 
                                 #Recuperamos variables generales del Instituto. 
                                 $Query="SELECT fl_current_plan,fg_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto  ";
                                 $row=RecuperaValor($Query);
                                 $fl_current_plan_actual=$row[0];
                                 $fg_plan_actual=$row[1];
                                 
                                 # 1. Recuperamos la fecha del ultimo mes a vencer,del plan.
                                 $Query="SELECT fe_periodo_final,mn_total,fl_current_plan,ds_descripcion,mn_costo_por_licencia,mn_descuento FROM k_admin_pagos WHERE fl_current_plan=$fl_current_plan_actual  ORDER BY fl_admin_pagos DESC ";
                                 $row=RecuperaValor($Query);
                                 $fe_inicio_periodo=$row['fe_periodo_final'];
                                 
                                 
                                 #2. Se calcula su fecha de inicio y fecha final del nuevo plan (es decir a la fecha le sumaos un mes.)  
                                 $p_plan_actual=ObtenPlanActualInstituto($fl_instituto);
                                 $fe_final_periodo=ObtenFechaFinalizacionRenovacionContratoPlan($fe_inicio_periodo,$p_plan_actual);
                                 
                                 
								 #actualizamo la fecha de inicio de vigencia y fevha final de vigencia del plan.
								 $Query="UPDATE k_current_plan SET fe_periodo_inicial='$fe_inicio_periodo', fe_periodo_final='$fe_final_periodo',fl_princing=$fl_princing_actual,id_plan_stripe='$id_plan_creado',id_suscripcion_stripe='$id_suscripcion' ";
								 $Query.="WHERE fl_current_plan =$fl_current_plan_actual AND fl_instituto=$fl_instituto  ";
								 EjecutaQuery($Query);
                                
                                 
                                 #Elimnaos su cron corresppndiente, para poder liberar otras opciones de cambios en su plan.
                                 $Query="DELETE FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto ";
                                 EjecutaQuery($Query);
                                 
                                 
                                 #se suma 1 dia la fecha para correr cron.
                                 #Se calcula la fecha posterios a terminacion del plan para ejecucion de cron
                                 $fe_final_periodo_=strtotime('+1 day',strtotime($fe_final_periodo));
                                 $fe_ejecucion= date('Y-m-d',$fe_final_periodo_);
                                 
                                 
							     #Por default se crea otro k_cron para la renovacion normal.
							     $Queryw="INSERT INTO k_cron_plan_fame(fe_ejecucion,fl_instituto,fg_motivo_pago,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias) ";
							     $Queryw.="VALUES('$fe_ejecucion',$fl_instituto,'1','AUTORENOVACION',0,0)";
							     $fl_cron_p=EjecutaInsert($Queryw);
								 
                                  
                                #Se genra la bitacora de pagos..
								#Guardaso el registro de pago(bitacora de pagos).
								 $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
								 $Query.="VALUES('$id_custom_actual_instituto','$id_charge','$id_plan_creado','$id_suscripcion','1','$ds_email_actual_instituto','$ds_descripcion_pago',$mn_total_pagarBD,$mn_taxBD,$mn_amount,CURRENT_TIMESTAMP, $fl_instituto)";
								 $fl_pago=EjecutaInsert($Query);
								
								 #se inserta el registro y costo por mes en su bitacora de pagos      
								$Query="INSERT INTO k_admin_pagos (fl_current_plan,fe_periodo_inicial,fe_periodo_final,mn_total,fg_publicar, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,fl_pago_stripe)";
							    $Query.="VALUES($fl_current_plan_actual,'$fe_inicio_periodo','$fe_final_periodo',$mn_amount,'1','1',CURRENT_TIMESTAMP,'$ds_descripcion_pago','1',$mn_monto_por_licencia,'$id_invoice',$mn_descuentoDB,$fl_pago) ";
								$fl_adm_pagos=EjecutaInsert($Query);
                                      
                                 
                                echo"<script>alert('envia invoice');</script>";
                                
                                /*****************************Envia Invoice************************************************/
								$fl_usuario=$fl_usuario_instituto;
                                # Recupera datos usuario
                                $Query  = "SELECT ds_email,ds_nombres,ds_apaterno ";
                                $Query .= "FROM c_usuario WHERE fl_usuario=$fl_usuario ";
                                $row = RecuperaValor($Query);
                                $ds_email=str_texto($row[0]);
                                $ds_nombres=$row['ds_nombres'];
                                $ds_apaterno=$row['ds_apaterno'];
                                
                                
                                # generador de documento cuerpo del email
                                $ds_header = GeneraInvoice($fl_instituto,$fl_usuario,$id_invoice,$id_charge,129,1);
                                $ds_cuerpo = GeneraInvoice($fl_instituto,$fl_usuario,$id_invoice,$id_charge,129,2);
                                $ds_footer = GeneraInvoice($fl_instituto,$fl_usuario,$id_invoice,$id_charge,129,3);
                                
                                
                                
                                #Recuperamos el datos del istituto:
                                $Query="SELECT ds_instituto,nb_plan,S.ds_pais,no_telefono 
                                                 FROM c_instituto I
                                                 JOIN c_plan_fame P ON P.cl_plan_fame=I.cl_plan_fame
                                                 JOIN c_pais S ON S.fl_pais=I.fl_pais
                                                 WHERE fl_instituto=$fl_instituto ";
                                $row=RecuperaValor($Query);
                                $nb_instituto=str_texto($row[0]);
                                $nb_plan_fame=str_texto($row[1]);
                                $ds_pais=str_texto($row[2]);
                                $no_telefono_instituto=$row[3];

                                
                                
                                #Recuperamos la direccion del Usuario.
                                $Query="SELECT ds_state,ds_city,ds_number,ds_street,ds_zip,ds_phone_number ";
                                $Query.="FROM k_usu_direccion_sp A
                                                   WHERE fl_usuario_sp=$fl_usuario ";
                                $row=RecuperaValor($Query);
                                $ds_estado=$row['ds_state'];
                                $ds_ciudad=$row['ds_city'];
                                $ds_numero_casa=$row['ds_number'];
                                $ds_calle=$row['ds_street'];
                                $ds_codigo_postal=$row['ds_zip'];
                                $ds_telefono=$row['ds_phone_number'];
                                
                                if(is_numeric($ds_estado)){
                                    
                                    #Recuperamos la provoincia de canada
                                    $Query4="SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$ds_estado ";
                                    $row4=RecuperaValor($Query4);
                                    $ds_estado=str_texto($row4[0]);
                                }
                                
                                #Obtenemos fecha actual :
                                $Query = "Select CURDATE() ";
                                $row = RecuperaValor($Query);
                                $fe_actual = str_texto($row[0]);
                                $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                                $fe_actual= date('Y-m-d',$fe_actual);
                                $fe_emision=GeneraFormatoFecha($fe_actual);
                                
                                
                                #Recuperamos la fecha de pago
                                $Query3="SELECT fe_pago,fl_pago_stripe,mn_descuento FROM k_admin_pagos WHERE fl_admin_pagos=$fl_adm_pagos ";
                                $row3=RecuperaValor($Query3);
                                $fe_pago=GeneraFormatoFecha(($row3[0]));
                                $fl_pago_stripe=$row3[1];
                                $mn_descuento=$row3[2];
                                
                                
                                #Recuperamos el id del pago, 
                                $Query2="SELECT id_pago_stripe FROM k_pago_stripe WHERE fl_pago=$fl_pago ";
                                $row2=RecuperaValor($Query2);
                                $id_pago=str_texto($row2[0]);
                                
                                
                                
                                
                                #guardamos el pdf
                                class ConPies extends TCPDF {
                                    //header 
                                    function Header(){
                                        
                                        
                                        $encabezado = '
                                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                <tr>
                                                                <td style="width:100%;">
                                                                    &nbsp;
                                                                </td>
                                                                </tr>
                                                ';
                                        $encabezado.='<tr>
       
        
                                                                <td rowspan="5" style="width:40%; color:#037EB7; font-family:Tahoma; font-size:32px; text-align:right;">
                                                                        <img src="../AD3M2SRC4/images/Vanas_doc_logo.jpg" />
                                                                </td>
		
		                                                        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;"></td>
		
		                                                        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;"> </td>
                                                    </tr>
	                                                ';
                                        $encabezado.='
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
		
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
                                                        '.ObtenEtiqueta(516).'  
                                                    </td>
                                                    </tr>
	                                                ';
                                        $encabezado.='
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
   
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
                                                        '.ObtenEtiqueta(518).' 
                                                    </td>
                                                    </tr>
                                                    ';
                                        $encabezado.='
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
		
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
                                                    '.ObtenEtiqueta(519).'   
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
        
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
                                                        '.ObtenEtiqueta(1741).': '.ObtenEtiqueta(1740).'
                                                    </td>
                                                    </tr>
      
                                                    <tr>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:62px; font-weight:normal; text-align:left;">
		                                            <b>'.ObtenEtiqueta(1729).'</b>
                                                    </td>
                                                    <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
        
                                                    </td>
                                                    <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
		
                                                    </td>
                                                    </tr>
      
      
		
                                                        ';
                                        $encabezado.='

                                                </table>';
                                        
                                        
                                        
                                        
                                        $this->writeHTML($encabezado, true, 0, true, 0); 
                                    }
                                    //footer
                                    function Footer(){

                                        $left_footer=ObtenEtiqueta(1746).ObtenEtiqueta(1737);
                                        $right_footer="";

                                        $this->SetY(-20);
                                        $this->SetFont('helvetica', '', 9);
                                        $this->writeHTML($left_footer, true, 0, true, 0); 
                                    }
                                }

                                // creamos un nuevo objeto usando la clase extendida ConPies 
                                $pdf = new ConPies();
                                $pdf->SetFont('helvetica', '', 10); 

                                // add a page
                                $pdf->AddPage("P"); 
                                
                                
                                /**** Inicia contenido del pdf*******/
                                #Recupermaos datos claves  del programa.
                                $Query="SELECT A.ds_descripcion,B.mn_monto,B.mn_tax,B.mn_total,A.fg_motivo_pago,B.id_pago_stripe,B.id_cliente_stripe,A.fe_periodo_inicial,A.fe_periodo_final,A.id_invoice_stripe,A.fl_current_plan  
                                                    FROM k_admin_pagos A
                                                    JOIN k_pago_stripe B ON B.fl_pago=A.fl_pago_stripe 
                                                    WHERE A.fl_admin_pagos=$fl_adm_pagos ";
                                $row = RecuperaValor($Query);
                                $ds_descripcion_pago=str_texto($row[0]);
                                $mn_monto_sin_tax=$row[1];
                                $mn_tax=$row[2];
                                $mn_total=number_format($row[3],2);
                                $fg_motivo_pago=$row[4];

                                $id_pago_stripe=$row[5];
                                $id_cliente_stripe=$row[6];
                                $fe_periodo_inicial=GeneraFormatoFecha($row[7]);
                                $fe_periodo_final=GeneraFormatoFecha($row[8]);
                                $id_invoice_stripe=$row[9];
                                $fl_current_plan=$row[10];
                                

                                #Obtenemos la cantidad para colocarlo en detalle del invoice
                                $cantidad = intval(preg_replace('/[^0-9]+/', '', $ds_descripcion_pago), 10);
                                $ds_descripcion_pago = preg_replace('/[0-9]+/', '', $ds_descripcion_pago);
                                
                                
                                #Recupermao el tipo de plan.
                                $Query="SELECT fl_princing,fg_plan FROM k_current_plan WHERE fl_current_plan=$fl_current_plan ";
                                $row=RecuperaValor($Query);
                                $fl_princing=$row[0];
                                $fg_plan=$row[1];
                                
                                $Query2="SELECT mn_anual,mn_mensual FROM c_princing WHERE fl_princing=$fl_princing ";
                                $row2=RecuperaValor($Query2);
                                if($fg_plan=='M')
                                    $mn_costo_por_licencia=$row2[1];
                                else
                                    $mn_costo_por_licencia=$row2[0];
                                
                                
                                #Agregamos el costo por licencia.
                                $ds_descripcion_pago=$ds_descripcion_pago."<br/>".ObtenEtiqueta(1765)." $".$mn_costo_por_licencia."<br/>".ObtenEtiqueta(1750)." ".$mn_descuento."%" ;
                                
                                #Eliminamos las licencias de la descripcion y se la pasamos ala cantidad del detalle del invoice.
                                //$ds_descripcion_pago = substr($ds_descripcion_pago, 0, -11); 
                                
                                
                                $interval=$fe_periodo_inicial." to ".$fe_periodo_final;
                                
                                if(empty($id_invoice_stripe)){
                                    #Recupermso el intervalo de la fecha del period actual del instituto
                                    $Query="SELECT fe_periodo_inicial,fe_periodo_final FROM k_current_plan WHERE fl_instituto=$fl_instituto";
                                    $row=RecuperaValor($Query);
                                    $fe_periodo_inicial=GeneraFormatoFecha($row[0]);
                                    $fe_periodo_final=GeneraFormatoFecha($row[1]);
                                    $interval=$fe_periodo_inicial." to ".$fe_periodo_final;
                                    
                                    $id_invoice_stripe=$id_pago_stripe;
                                }
                                
                                
                                
                                
                                /*****Termina contenido del pdf*****/
                                # Empezamos a mostrar datos
                                $htmlcontent = '<table border="0" cellpadding="1" cellspacing="0" width="100%">'; 
                                
                                
                                $htmlcontent .='<tr>';
                                $htmlcontent .='<td colspan="7" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> </td>';
                                $htmlcontent .='</tr>';
                                

                                
                                $htmlcontent .= '<tr >';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><strong>'.ObtenEtiqueta(1726).'</strong>  </td>';
                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><strong>'.ObtenEtiqueta(1727).'</strong>   </td>';
                                //$htmlcontent .='<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                //$htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .= '</tr>';
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$ds_nombres.' '.$ds_apaterno.'   </td>';
                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1728).':</td>';
                                $htmlcontent .='<td  colspan="2" style="height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$id_invoice_stripe.'</td>';
                                
                                $htmlcontent .='</tr>';
                                
                                
                                
                                
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_instituto.'   ';
                                $htmlcontent .='</td>';
                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1730).':</td>';
                                $htmlcontent .='<td colspan="2" style="height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$fe_emision.'</td>';
                                // $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='</tr>';
                                
                                
                                
                                
                                
                                
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">';
                                if($ds_numero_casa)
                                    $htmlcontent.=''.$ds_numero_casa.' ';
                                $htmlcontent.=''.$ds_calle.' '.$ds_codigo_postal.'';
                                
                                $htmlcontent .='</td>';
                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1731).':</td>';
                                $htmlcontent .='<td colspan="3" style=" height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$id_pago_stripe.' </td>';
                                
                                $htmlcontent .='</tr>';
                                
                                
                                
                                
                                
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">';
                                if($ds_estado)
                                    $htmlcontent .=''.$ds_estado.' ';
                                $htmlcontent .=''.$ds_pais.'  </td>';

                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1732).': </td>';
                                
                                $htmlcontent .='<td colspan="2" style=" height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_plan_fame.'  </td>';
                                $htmlcontent .='</tr>';
                                
                                
                                
                                
                                
                                /*
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_plan_fame.' </td>';
                                $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='</tr>';
                                 */
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">  </td>';
                                $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left; margin-lef:35px;">'.ObtenEtiqueta(1715).':</td>';
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$fe_pago.' </td>';
                                $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='</tr>';
                                
                                
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style=" width:50%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><b>'.ObtenEtiqueta(1733).':</b> '.$id_cliente_stripe.' </td>';
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"></td>';
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='</tr>';
                                

                                $htmlcontent .='<tr>';
                                $htmlcontent .='<td colspan="4" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> <br/> <br/></td>';
                                $htmlcontent .='</tr>';
                                
                                
                                
                                
                                $htmlcontent .= '<tr>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:40%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b>'.ObtenEtiqueta(1716).' </b> </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b>'.ObtenEtiqueta(1734).' </b></td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b> '.ObtenEtiqueta(1717).' </b>  </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;"><b> '.ObtenEtiqueta(1718).' ('.ObtenConfiguracion(113).'$)</b>  </td>';
                                $htmlcontent .= '</tr>';

                                
                                
                                //$htmlcontent .= '<tr >';
                                //$htmlcontent .='<td colspan="4" style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><br/>  </td>';
                                //$htmlcontent .= '</tr>';
                                
                                
                                
                                
                                $htmlcontent .= '<tr >';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:40%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:left;">'.$ds_descripcion_pago.'  </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:30%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:left;">'.$interval.' </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:10%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:center;">'.$cantidad.'</td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:right;">'.number_format($mn_monto_sin_tax,2).' </td>';
                                $htmlcontent .= '</tr>';

                                
                                
                                #Obtenemos el porcentaje del tax.
                                $mn_porcentaje=($mn_tax/$mn_monto_sin_tax) *100;
                                
                                
                                //$htmlcontent .= '<tr >';
                                //$htmlcontent .='<td colspan="4" style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><br/>  </td>';
                                //$htmlcontent .= '</tr>';
                                
                                
                                
                                #Presentamos Descuento.
                                //$htmlcontent .= '<tr >';
                                //$htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                //$htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                //$htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1750).'  </td>';
                                //$htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.$mn_descuento.' %</td>';
                                //$htmlcontent .= '</tr>';
                                
                                
                                
                                #Presentamos totale y tax.
                                $htmlcontent .= '<tr >';
                                $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                $htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1736).' '.ObtenConfiguracion(113).' </td>';
                                $htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.number_format($mn_monto_sin_tax,2).' </td>';
                                $htmlcontent .= '</tr>';
                                
                                
                                $htmlcontent .= '<tr >';
                                $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">*'.ObtenEtiqueta(1735).' ('.number_format($mn_porcentaje).'%) </td>';
                                $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.number_format($mn_tax,2).' </td>';
                                $htmlcontent .= '</tr>';
                                
                                
                                
                                
                                #Presentamos totale y tax.
                                $htmlcontent .= '<tr >';
                                $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
                                $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1742).' '.ObtenConfiguracion(113).'</td>';
                                $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.$mn_total.'  </td>';
                                $htmlcontent .= '</tr>';
                                
                                
                                
                                
                                $htmlcontent .= '</table>';
                                
                                
                                
                                
                                $ds_cuerpo2=$htmlcontent;
                                // output the HTML content contenido del pdf
                                $pdf->writeHTMLCell(180, 100, 10,60,$ds_cuerpo2, 0, 0, false, true,'',true); 
                                //nombre del archivo
                                $fileName ='Invoice_'.$nb_instituto.'_'.$id_pago_stripe.'.pdf';
                                // pasamos el archivo a base64
                                //$pdf->Output($fileName, 'F');///guarda el archivo MjD: Se deja comen tado porque se cambio el metodo para que ahora vaya como attachment
                                $fileatt = $pdf->Output($fileName, 'E'); //genera la codificacion para enviar adjuntado el archivo
                                

                                $from=MAIL_FROM;
                                # Inicializa variables de ambiente para envio de correo adjunto
                                ini_set("SMTP", MAIL_SERVER);
                                ini_set("smtp_port", MAIL_PORT);
                                ini_set("sendmail_from", MAIL_FROM);
                                $repEmail = $from;
                                $nb_nombre_dos=ObtenEtiqueta(1646);#nombre de quien envia el mensaje
                                $eol = "\n";
                                $separator = md5(time());
                                
                                $subject=ObtenEtiqueta(1768);#etiqueta de asunto del mensjae
                                $headers  = 'MIME-Version: 1.0' .$eol;
                                
                                $headers .= 'From: "'.$nb_nombre_dos.'" <'.$repEmail.'>'.$eol;
                                $admin=ObtenConfiguracion(107);
                                $headers .= "Bcc: $admin \r\n";
                                $headers .= 'Content-Type: multipart/mixed; boundary="'.$separator.'"';
                                
                                $message = "--".$separator.$eol;
                                $message .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
                               
                                #Presenta mensaje del email
                                $message .= utf8_decode($ds_header).utf8_decode($ds_cuerpo).utf8_decode($ds_footer).$eol;

                                $message .= "--".$separator.$eol;
                                $message .= $fileatt;
                                $message .= "--".$separator."--".$eol;
                                
                                # insertamos el envio del email
                                $mail=mail($ds_email, $subject, $message, $headers);
                                
                                
                                /************************************************************************************************/
                                
                                
                                
                                      
                              
                                 
                                  
                                  
                                  
                                  
                                                          
                             }
                
                
                
                
                
                
                            #si existe cambio plan quiere decir que que el plan que tenia antes se eliminara de stripe y creara uno nuevo /mensual/anaul viceversa
                           
                             if($fg_motivo_pago==3){    
                                
                                
                                       
                                 
                                 
                                 
                                 
                                        #1.Desactivamos el plan actual(falta) 
                                        #1.Elimina Plan actual                               
                                       // $plan = \Stripe\Plan::retrieve($id_plan_actual_instituto);
                                       // $plan->delete();
                                
                                        #2.Se cancela la suscripcion actual
                                        $subscription = \Stripe\Subscription::retrieve($id_suscripcion_actual_instituto);
                                        $subscription->cancel();
                                    
                                        
                                
                                        #1.Creamos su plan de pago,propio por cliente/Instituto, con el tax incluido, con datos actuales del Instituto.
                                
                                
                                        $Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
                                        $row=RecuperaValor($Query);
                                        $nb_instituto=str_texto($row[0]);
                                
                                        $ds_plan=ObtenNombrePlanFame($fl_instituto);
                                        $rand=rand(5,1000);
                                
                                        
                                            if($fg_cambio_plan='A'){
                                               $interval="year";
                                               $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                                               $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                                               $mn_costo_x_licencia=Conv_Dollars_Stripe(($mn_monto_por_licencia*12));//por doce los 12 meses.esto es para stripe
                                              
                                               $mn_total_pagarBD=($mn_monto_por_licencia * $no_licencias_compradas )*12;
                                               
                                               
                                            }else{
                                               $interval="month";
                                               $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                                               $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                                               $mn_costo_x_licencia=Conv_Dollars_Stripe($mn_monto_por_licencia);
                                               $mn_total_pagarBD=$mn_monto_por_licencia * $no_licencias_compradas;
                                               
                                            }
                                        
                                        
                                    
                                        $plan = \Stripe\Plan::create(array(
                                           "name" => $nb_plan,
                                           "id" => $id_plan,
                                           "interval" => $interval,
                                           "currency" => $currency,
                                           "amount" => $mn_costo_x_licencia
                                           )
                                         );
                                        
                                        #Recupermaos el id del plan generado
                                        $id_plan=$plan->id;
                                        $id_plan_creado=$plan->id;
                                        
                                        
                                        
                                        
                                        
                                        
                                        #Recuperamos el estado/provincia del usuario para determina el monto del tax.
                                        $mn_tax_p=Tax_Can_User($fl_usuario);
                                        $mn_porcentaje_tax_=$mn_tax_p*100;
                                        if($mn_tax_p>0){
                                            $mn_tax = $mn_total_pagarBD * $mn_tax_p;$mn_taxBD=$mn_tax;
                                            $mn_amount = $mn_total_pagarBD + $mn_tax;
                                            $mn_tax=number_format($mn_tax,2);
                                        }else{
                                            $mn_amount = $mn_total_pagarBD;
                                            
                                        }
                                        
                                        
                                        #Convertimosel monto para guardarlo en DB
                                        $mn_monto_normal= $mn_total_pagarBD;
                                        $mn_total=$mn_total_pagarBD+ $mn_taxBD;
                                        
                                        
                                        
                                       #2. se crea nueva suscripcion 
                                       $subscription= \Stripe\Subscription::create(array(
                                              "customer" => $id_cliente_stripe,
                                              "plan" => $id_plan,
                                              "quantity"=>$no_licencias_compradas,
                                              "tax_percent" => $mn_porcentaje_tax_,
                                              
                                            ));
                                        $id_suscripcion=$subscription->id;
                                      
                                        
                                        #Verificamos el eveno creado en Strippe, para despues poder recuperar elid del pago que se realizo. y actualizar su descripcion.
                                        $event = \Stripe\Event::all(array("limit" => 2));
                                        $id_charges=$event->data;    
                                        $id_evento=$id_charges[1]->data;
                                        $id_charge=$id_evento['object']->id;
                                        $id_invoice=$id_evento['object']->invoice;  
                                        #Actualizamos la descripcion del pago que se realizao.
                                        $ch = \Stripe\Charge::retrieve($id_charge);
                                        $ch->description = $ds_descripcion_pago;
                                        $ch->save();

                                  
                                        
                                        #Guardamos botacora en k_admin_pagos
                                        if($interval=="month"){
                                            
                                               
                                                #se calcula fecha de termino del plan por mes.
                                                $fe_final_periodo=strtotime('+1 month',strtotime($fe_actual));
                                                $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                                            
                                            
                                            
                                                #se inserta el registro y costo por mes en su bitacora de pagos      
                                                $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento)";
                                                $Query.="VALUES($fl_current_plan_actual,$mn_total,'1','$fe_actual','$fe_final_periodo','1','','$ds_descripcion_pago','1',$mn_monto_por_licencia,'$id_invoice',$mn_descuentoDB) ";
                                                $fl_adm_pagos=EjecutaInsert($Query);
                                                
                                                $Query="UPDATE k_current_plan SET  fg_plan='M',id_suscripcion_stripe='$id_suscripcion' , id_plan_stripe='$id_plan_creado' ";
                                                $Query.="WHERE fl_instituto=$fl_instituto ";
                                                EjecutaQuery($Query);
                                        }
                                        
                                        if($interval=="year"){
                                            
                                                #se calcula fecha de termino del plan por año.
                                                $fe_final_periodo=strtotime('+1 year',strtotime($fe_actual));
                                                $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                                                
                                                #se inserta el registro y costo por mes en su bitacora de pagos      
                                                $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento)";
                                                $Query.="VALUES($fl_current_plan_actual,$mn_total,'1','$fe_actual','$fe_final_periodo','1','','$ds_descripcion_pago','1',$mn_monto_por_licencia,'$id_invoice',$mn_descuentoDB) ";
                                                $fl_adm_pagos=EjecutaInsert($Query);
                                                
                                                
                                                $Query="UPDATE k_current_plan SET  fg_plan='A',id_suscripcion_stripe='$id_suscripcion' , id_plan_stripe='$id_plan_creado' ";
                                                $Query.="WHERE fl_instituto=$fl_instituto ";
                                                EjecutaQuery($Query);

		 										#Por default se crea otro k_cron para la renovacion normal.
		                                        $Queryw="INSERT INTO k_cron_plan_fame(fe_ejecucion,fl_instituto,fg_motivo_pago,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias) ";
		                                        $Queryw.="VALUES('$fe_final_periodo',$fl_instituto,'1','AUTORENOVACION',0,0)";
		                                        $fl_cron_p=EjecutaInsert($Queryw);
                                                                                        


                                            
                                        }
                                        
                                        #Guardamos bitacora en pago de  stripe DB FAME:  5::QUIERE DECIR QUE ES AUTORENOVACION DE PLAN
                                        $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
                                        $Query.="VALUES('$id_custom_actual_instituto','$id_charge','$id_plan_creado','$id_suscripcion','5','$ds_email_custom_actual','$ds_descripcion_pago',$mn_monto_normal,$mn_taxBD,$mn_total,CURRENT_TIMESTAMP, $fl_instituto)";
                                        $fl_pago=EjecutaInsert($Query);
                                        
                                        $Query="SELECT fe_creacion FROM k_pago_stripe WHERE fl_pago=$fl_pago ";
                                        $row=RecuperaValor($Query);
                                        $fe_pago=$row[0];
                                        
                                        $Query="UPDATE k_admin_pagos SET fe_pago='$fe_pago',fg_pagado='1',fl_pago_stripe=$fl_pago  WHERE fl_current_plan=$fl_current_plan AND fl_admin_pagos=$fl_adm_pagos  ";
                                        EjecutaQuery($Query);
                                       
                                        
                                        #Elimnaos su cron corresppndiente, para poder liberar otras opciones de cambios en su plan.
                                        $Query="DELETE FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto ";
                                        EjecutaQuery($Query);
                                       
                                        
                                        #Por default se crea otro k_cron para la renovacion normal.
                                        $Queryw="INSERT INTO k_cron_plan_fame(fe_ejecucion,fl_instituto,fg_motivo_pago,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias) ";
                                        $Queryw.="VALUES('$fe_final_periodo',$fl_instituto,'1','AUTORENOVACION',0,0)";
                                        $fl_cron_p=EjecutaInsert($Queryw);
                                        
                                        
                                        
                                        
                    
                            }else{
                            
                                        #Se agregaron mas licencias y por ende no corresponde a su plan actual ya que son nuevas,  y entonces se elimnara plan actual y se creara otro con nuevas tarifas y fechas de renivacion.
                                        if($fg_motivo_pago==2){
                                        
                                            
                                            #Obtenemos fecha actual :
                                            $Query = "Select CURDATE() ";
                                            $row = RecuperaValor($Query);
                                            $fe_actual = str_texto($row[0]);
                                            $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                                            $fe_actual= date('Y-m-d',$fe_actual);
                                            
                                            
                                            
                                        
                                            #1.Desactivamos el plan actual(falta) 
                                            #1.Elimina Plan actual                               
                                          //  $plan = \Stripe\Plan::retrieve($id_plan_actual_instituto);
                                          //  $plan->delete();
                                            
                                            #2.Se cancela la suscripcion actual
                                            $subscription = \Stripe\Subscription::retrieve($id_suscripcion_actual_instituto);
                                            $subscription->cancel();
                                            
                                            
                                            
                                            $Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
                                            $row=RecuperaValor($Query);
                                            $nb_instituto=str_texto($row[0]);
                                            
                                            $ds_plan=ObtenNombrePlanFame($fl_instituto);
                                            $rand=rand(5,1000);
                                            
                                            
                                            
                                                if($fg_plan_actual_instituto=='A'){
                                                   
                                                    
                                                
                                                
                                                    $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                                                    $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                                                    $mn_costo_x_licencia=Conv_Dollars_Stripe(($mn_monto_por_licencia*12));//por doce los 12 meses.esto es para stripe
                                                    
                                                    $mn_total_pagarBD=($mn_monto_por_licencia * $no_licencias_compradas )*12;
                                                    $interval="year";
                                                    
                                                    $ds_descripcion_pago= $ds_plan."-".ObtenEtiqueta(1705)." ".$no_licencias_compradas." licences";
                                                    
                                                    
                                            
                                                }
                                                if($fg_plan_actual_instituto=='M'){
                                            
                                                    $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                                                    $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                                                    $mn_costo_x_licencia=Conv_Dollars_Stripe($mn_monto_por_licencia);
                                                    $mn_total_pagarBD=$mn_monto_por_licencia * $no_licencias_compradas;
                                                    $interval="month";
                                                    $ds_descripcion_pago= $ds_plan."-".ObtenEtiqueta(1705)." ".$no_licencias_compradas." licences";
                                            
                                                }
                                            
                                                $plan = \Stripe\Plan::create(array(
                                                  "name" => $nb_plan,
                                                  "id" => $id_plan,
                                                  "interval" => $interval,
                                                  "currency" => $currency,
                                                  "amount" => $mn_costo_x_licencia
                                                  )
                                                );
                                                
                                                #Recupermaos el id del plan generado
                                                $id_plan=$plan->id;
                                                $id_plan_creado=$plan->id;
                                            
                                            
                                                
                                                #Recuperamos el estado/provincia del usuario para determina el monto del tax.
                                                $mn_tax_p=Tax_Can_User($fl_usuario);
                                                $mn_porcentaje_tax_=$mn_tax_p*100;
                                                if($mn_tax_p>0){
                                                    $mn_tax = $mn_total_pagarBD * $mn_tax_p;$mn_taxBD=$mn_tax;
                                                    $mn_amount = $mn_total_pagarBD + $mn_tax;
                                                    $mn_tax=number_format($mn_tax,2);
                                                }else{
                                                    $mn_amount = $mn_total_pagarBD;
                                                    
                                                }
                                                
                                                
                                                #Convertimosel monto para guardarlo en DB
                                                $mn_monto_normal= $mn_total_pagarBD;
                                                $mn_total=$mn_total_pagarBD+ $mn_taxBD;
                                                
                                                
                                                
                                                #2. se crea nueva suscripcion 
                                                $subscription= \Stripe\Subscription::create(array(
                                                       "customer" => $id_cliente_stripe,
                                                       "plan" => $id_plan,
                                                       "quantity"=>$no_licencias_compradas,
                                                       "tax_percent" => $mn_porcentaje_tax_,
                                                       
                                                     ));
                                                $id_suscripcion=$subscription->id;
                                                
                                                
                                                #Verificamos el eveno creado en Strippe, para despues poder recuperar elid del pago que se realizo. y actualizar su descripcion.
                                                $event = \Stripe\Event::all(array("limit" => 2));
                                                $id_charges=$event->data;    
                                                $id_evento=$id_charges[1]->data;
                                                $id_charge=$id_evento['object']->id;
                                                $id_invoice=$id_evento['object']->invoice;  
                                                #Actualizamos la descripcion del pago que se realizao.
                                                $ch = \Stripe\Charge::retrieve($id_charge);
                                                $ch->description = $ds_descripcion_pago;
                                                $ch->save();
                                                
                                                
                                                 if($fg_plan_actual_instituto=='A'){
                                                     #se calcula fecha de termino del plan por año.
                                                     $fe_final_periodo=strtotime('+1 year',strtotime($fe_actual));
                                                     $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                                                     
                                                     #se inserta el registro y costo por mes en su bitacora de pagos      
                                                     $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento)";
                                                     $Query.="VALUES($fl_current_plan_actual,$mn_total,'1','$fe_actual','$fe_final_periodo','1','$fe_actual','$ds_descripcion_pago','1',$mn_monto_por_licencia,'$id_invoice',$mn_descuentoDB) ";
                                                     $fl_adm_pagos=EjecutaInsert($Query);
                                                     
                                                     
                                                     $Query="UPDATE k_current_plan SET  fg_plan='A',id_suscripcion_stripe='$id_suscripcion' , id_plan_stripe='$id_plan_creado'  ";
                                                     $Query.="WHERE fl_instituto=$fl_instituto ";
                                                     EjecutaQuery($Query);
                                                     
                                                     
                                                 }
                                                 
                                                  if($fg_plan_actual_instituto=='M'){
                                                      #se calcula fecha de termino del plan por mes.
                                                      $fe_final_periodo=strtotime('+1 month',strtotime($fe_actual));
                                                      $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
                                                      
                                                      
                                                      
                                                      #se inserta el registro y costo por mes en su bitacora de pagos      
                                                      $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento)";
                                                      $Query.="VALUES($fl_current_plan_actual,$mn_total,'1','$fe_actual','$fe_final_periodo','1','$fe_actual','$ds_descripcion_pago','1',$mn_monto_por_licencia,'$id_invoice',$mn_descuentoDB) ";
                                                      $fl_adm_pagos=EjecutaInsert($Query);
                                                      
                                                      $Query="UPDATE k_current_plan SET  fg_plan='M',id_suscripcion_stripe='$id_suscripcion' , id_plan_stripe='$id_plan_creado' ";
                                                      $Query.="WHERE fl_instituto=$fl_instituto ";
                                                      EjecutaQuery($Query);



                                                  }
                                                
                                                
                                                  #Guardamos bitacora en pago de  stripe DB FAME:  5::QUIERE DECIR QUE ES AUTORENOVACION DE PLAN
                                                  $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
                                                  $Query.="VALUES('$id_custom_actual_instituto','$id_charge','$id_plan_creado','$id_suscripcion','5','$ds_email_custom_actual','$ds_descripcion_pago',$mn_monto_normal,$mn_taxBD,$mn_total,CURRENT_TIMESTAMP, $fl_instituto)";
                                                  $fl_pago=EjecutaInsert($Query);
                                                  
                                                  $Query="SELECT fe_creacion FROM k_pago_stripe WHERE fl_pago=$fl_pago ";
                                                  $row=RecuperaValor($Query);
                                                  $fe_pago=$row[0];
                                                  
                                                  $Query="UPDATE k_admin_pagos SET fe_pago='$fe_pago',fg_pagado='1',fl_pago_stripe=$fl_pago  WHERE fl_current_plan=$fl_current_plan AND fl_admin_pagos=$fl_adm_pagos  ";
                                                  EjecutaQuery($Query);
                                                  
                                                  
                                                  #Elimnaos su cron corresppndiente, para poder liberar otras opciones de cambios en su plan.
                                                  $Query="DELETE FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto ";
                                                  EjecutaQuery($Query);
                                                
                                                  #Por default se crea otro k_cron para la renovacion normal.
                                                  $Queryw="INSERT INTO k_cron_plan_fame(fe_ejecucion,fl_instituto,fg_motivo_pago,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias) ";
												  $Queryw.="VALUES('$fe_final_periodo',$fl_instituto,'1','AUTORENOVACION',0,0)";
												  $fl_cron_p=EjecutaInsert($Queryw);
                                                
                                                
                                            
                                            
                                        
                                        }
										
										#CANCELACION DE LA SUSCRIPCION
										if($fg_motivo_pago==4){
										
										
												   #1.Elimina Plan actual
                
											     //  $plan = \Stripe\Plan::retrieve($id_plan_actual_instituto);
											     //  $plan->delete();

												
												   #2.Se cancela la suscripcion actual
											       $subscription = \Stripe\Subscription::retrieve($id_suscripcion_actual_instituto);
											       $subscription->cancel();
                
										           #Se desactivan todas sus cuentas del Instituto.
												
												   #Desactivaos al Instituto.
											       $Query="UPDATE c_instituto SET fg_activo='0'  WHERE fl_instituto=$fl_instituto  ";
												   EjecutaQuery($Query);
										
										           #Desactivamos todas las cuentas de los usuarios  del Instituto EXCEPTO EL ADMIN del Instituto, el tendra acceso solo la parte del Billing.
												   $Query2="UPDATE c_usuario SET fg_activo='0' WHERE fl_instituto=$fl_instituto AND fl_perfil <> ".PFL_ADMINISTRADOR." ";
												   EjecutaQuery($Query2);
												   
                                                   
                                                  
                                                   
                                                   
												   #Desactivams el plan que tiene el Instituto.
												   $Query3="UPDATE k_current_plan SET fg_estatus='C' WHERE fl_instituto=$fl_instituto ";
												   EjecutaQuery($Query3);
										           
												   #elimnamos los crones existentes de ese Instituto.
												   EjecutaQuery("DELETE FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto ");
												   
												   #Por default se crea otro k_cron para la renovacion normal.
												   $Queryw="INSERT INTO k_cron_plan_fame(fe_ejecucion,fl_instituto,fg_motivo_pago,ds_descripcion_pago,mn_monto_por_licencia,mn_cantidad_licencias) ";
												   $Queryw.="VALUES('$fe_final_periodo',$fl_instituto,'1','AUTORENOVACION',0,0)";
												   $fl_cron_p=EjecutaInsert($Queryw);
												   
										
										}
										
										
                            
                            
                            
                            
                            }
           
           
           
           
           }
	
	    $a=1;
		$b=2;
	    
	
	}
	
	
	
	
	
	
	
	
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
  echo json_encode((Object) $result);
	
	
?>
