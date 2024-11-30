<?php
  /*solo para pagos recurrentes**/
  # Libreria de funciones
  require("../lib/self_general.php");
  //require_once('../../AD3M2SRC4/lib/tcpdf/config/lang/eng.php');
  //require_once('../../AD3M2SRC4/lib/tcpdf/tcpdf.php');


  //$fe_periodo_final="2020-09-18";
  //$fe_periodo_inicial="2020-08-18";

  //$mes_anio_act = date("Y-m-d",strtotime($fe_periodo_final."+ 1 month"));

  # Include the Stripe library
  require_once('../lib/Stripe/Stripe/init.php');
  # Variables Stripe
  $secret_key = ObtenConfiguracion(112);



 // Set your secret key: remember to change this to your live secret key in production
 // See your keys here: https://dashboard.stripe.com/account/apikeys
 \Stripe\Stripe::setApiKey($secret_key);

 // You can find your endpoint's secret in your webhook settings
 $endpoint_secret = ObtenConfiguracion(117);
 // retrieve the request's body and parse it as JSON
 $body = @file_get_contents('php://input');
 $event_json = json_decode($body);



    #Solo si el tipo de evento es un invoice y corrspode a un cargo en automatico.
    if( ($event_json->type == 'invoice.payment_succeeded')&&($event_json->data->object->billing=='charge_automatically') ) {


	    #Recupermaos datos del evento en especifico el invoice que se genero.
	    $type_evento=$event_json->type;
		$id_evento=$event_json->id;
		$id_invoice=$event_json->data->object->id;
		$id_customer=$event_json->data->object->customer;
		$id_charge=$event_json->data->object->charge;
		$mn_monto=$event_json->data->object->lines->data->amount /100;
		$id_suscripcion=$event_json->data->object->subscription;
		$mn_tax=$event_json->data->object->tax /100;
		$mn_porcentaje_tax=$event_json->data->object->tax_percent;
		$mn_subtotal=$event_json->data->object->subtotal /100;
		$mn_total=$event_json->data->object->total/100;

		echo $mn_monto=$event_json->data->object->lines->data;

	    #Identificamos al Instituto atraves del charge:
		$Query="SELECT fl_instituto,id_plan_stripe,fg_plan,no_total_licencias,fl_current_plan,fl_princing,fe_periodo_inicial,fe_periodo_final FROM k_current_plan WHERE id_cliente_stripe='$id_customer' ";
		$row=RecuperaValor($Query);
		$fl_instituto=$row['fl_instituto'];
		$id_plan_stripe=str_texto($row['id_plan_stripe']);
		$fg_plan=str_texto($row['fg_plan']);
		$no_licencia=$row['no_total_licencias'];
		$fl_current_plan=$row['fl_current_plan'];
		$fl_princing=$row['fl_princing'];
		$fe_periodo_inicial=$row['fe_periodo_inicial'];
		$fe_periodo_final=$row['fe_periodo_final'];

		if(empty($fl_instituto))
		$fl_instituto=1;


	    #Verificamos si tiene plan.(por logica aqui ya debe de tener)
	    $Query2="SELECT fg_tiene_plan,fl_usuario_sp FROM c_instituto WHERE fl_instituto=$fl_instituto ";
		$row2=RecuperaValor($Query2);
        $fg_tiene_plan=$row2['fg_tiene_plan'];
	    $fl_usuario_sp_instituto=$row2['fl_usuario_sp'];

		$Query="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_sp_instituto ";
		$rop=RecuperaValor($Query);
		$ds_email_client=$rop['ds_email'];

		$Query="INSERT INTO k_log_stripe(id_evento,id_invoice,id_customer,id_charge,mn_monto,id_suscripcion,mn_subtotal,mn_total,fe_creacion ) ";
		$Query.="VALUES('$id_evento','$id_invoice','$id_customer','$id_charge','$mn_monto','$id_suscripcion','$mn_subtotal','$mn_total',CURRENT_TIMESTAMP )";
		$fl_pago=EjecutaInsert($Query);



		if($fg_tiene_plan==1){


			#VERIFICAMOS SI YA EXISTE EL PAGO Y SI NO LA GENERAMOS.
			$Quer="SELECT fl_admin_pagos FROM k_admin_pagos WHERE id_invoice_stripe='$id_invoice' ";
			$r=RecuperaValor($Quer);
			$fl_admin_pagos=$r['fl_admin_pagos'];

			if(empty($fl_admin_pagos)){

				if($fg_plan=='M'){
				  $ds_descripcion="Essentials Plan-Monthly ".$no_licencia." licenses";
				  $mn_descuentoDB=$mn_decuento_mensual;
				  $mn_costo_licencia=$mn_mensual;

				  $fe_periodo_inicial=$fe_periodo_final;
				  $fe_periodo_final = date("Y-m-d",strtotime($fe_periodo_final."+ 1 month"));
                }
                if($Fg_plan=='A'){
                    $ds_descripcion="Essentials Plan-Annual ".$no_licencia." licenses";
				  $mn_descuentoDB=$mn_decuento_anual;
				  $mn_costo_licencia=$mn_anual;
				  $fe_periodo_inicial=$fe_periodo_final;
				  $fe_periodo_final = date("Y-m-d",strtotime($fe_periodo_final."+ 1 year"));
				 }

				#Guardaso el registro de pago(bitacora de pagos).
				$Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
				$Query.="VALUES('$id_customer','$id_charge','$id_plan_stripe','$id_suscripcion','1','$ds_email_client','$ds_descripcion',$mn_subtotal,$mn_tax,$mn_total,CURRENT_TIMESTAMP, $fl_instituto)";
				$fl_pago=EjecutaInsert($Query);

				#Recupermaos la fecha de inicio y fin de periodo.
				$Query1="SELECT fe_periodo_inicial,fe_periodo_final FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
				$rows=RecuperaValor($Query1);
				$fe_periodo_inicial=$rows['fe_periodo_inicial'];
				$fe_final_periodo=$rows['fe_periodo_final'];



				#se inserta el registro y costo por mes en su bitacora de pagos
				$Query="INSERT INTO k_admin_pagos (fl_current_plan,fe_periodo_inicial,fe_periodo_final,mn_total,fg_publicar, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,fl_pago_stripe)";
				$Query.="VALUES($fl_current_plan,'$fe_periodo_inicial','$fe_periodo_final',$mn_total,'1','1',CURRENT_TIMESTAMP,'$ds_descripcion','1',$mn_costo_licencia,'$id_invoice',$mn_descuentoDB,$fl_pago) ";
				$fl_adm_pagos=EjecutaInsert($Query);



				#Actualizamos el plan del instituto para que tenga failed.
				$Query=" UPDATE k_current_plan SET fe_periodo_inicial='$fe_periodo_inicial',fe_periodo_final='$fe_periodo_final' WHERE fl_instituto=$fl_instituto  ";
				EjecutaQuery($Query);



			}



		}
		/*
				#VERIFICAMOS SI YA EXISTE EL PAGO Y SI NO LA GENERAMOS.
				$Quer="SELECT fl_admin_pagos FROM k_admin_pagos WHERE id_invoice_stripe='$id_invoice' ";
		        $r=RecuperaValor($Quer);
		        $fl_admin_pagos=$r['fl_admin_pagos'];

			     #Recupermaos el costo mensual y descuento.
				 $Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princing ";
				 $row=RecuperaValor($Query);
				 $mn_mensual=$row['mn_mensual'];
				 $mn_anual=$row['mn_anual'];
				 $mn_decuento_mensual=$row['ds_descuento_mensual'];
				 $mn_decuento_anual=$row['mn_descuento_licencia'];




				if(empty($fl_admin_pagos)){


					 if($fg_plan=='M'){
				      $ds_descripcion="Essentials Plan-Monthly ".$no_licencia." licenses";
				      $mn_descuentoDB=$mn_decuento_mensual;
					  $mn_costo_licencia=$mn_mensual;
					 }
					 if($Fg_plan=='A'){
					  $ds_descripcion="Essentials Plan-Annual ".$no_licencia." licenses";
					  $mn_descuentoDB=$mn_decuento_anual;
					  $mn_costo_licencia=$mn_anual;
					 }
					 #Guardaso el registro de pago(bitacora de pagos).
					 $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
					 $Query.="VALUES('$id_customer','$id_charge','$id_plan_stripe','$id_suscripcion','1','@gmail','$ds_descripcion',$mn_subtotal,$mn_tax,$mn_total,CURRENT_TIMESTAMP, $fl_instituto)";
					 $fl_pago=EjecutaInsert($Query);


					 #Recupermaos la fecha de inicio y fin de periodo.
					 $Query1="SELECT fe_periodo_inicial,fe_periodo_final FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
					 $rows=RecuperaValor($Query1);
					 $fe_periodo_inicial=$rows['fe_periodo_inicial'];
					 $fe_final_periodo=$rows['fe_periodo_final'];



					 #se inserta el registro y costo por mes en su bitacora de pagos
					 $Query="INSERT INTO k_admin_pagos (fl_current_plan,fe_periodo_inicial,fe_periodo_final,mn_total,fg_publicar, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,fl_pago_stripe)";
					 echo $Query.="VALUES($fl_current_plan,'$fe_periodo_inicial','$fe_final_periodo',$mn_total,'1','1',CURRENT_TIMESTAMP,'$ds_descripcion','1',$mn_costo_licencia,'$id_invoice',$mn_descuentoDB,$fl_pago) ";
					 $fl_adm_pagos=EjecutaInsert($Query);




				}




		}

	   */

	    ############################################ PARA PAGOS REALIZADOS DE ALUMNOS QUE ADQUIRIERON UN PLAN POR CURSOS #####################################
		/****
	    $Query="SELECT fl_current_plan,id_plan_stripe,ds_email_stripe,fg_plan
			   FROM k_current_plan_alumno WHERE id_cliente_stripe='$id_customer' AND id_suscripcion_stripe='$id_suscripcion' ";
		$row=RecuperaValor($Query);
		$fl_current_plan=$row[0];
		$id_plan_stripe=str_texto($row[1]);
		$fg_plan=str_texto($row[2]);

		#Obtenemos fecha actual :
		$Query = "Select CURDATE() ";
		$row = RecuperaValor($Query);
		$fe_actual = str_texto($row[0]);

		$Query="SELECT mn_descuento FROM k_admin_pagos_alumno WHERE fl_current_plan_alumno=$fl_current_plan ORDER BY fl_admin_pagos_alumno DESC ";
		$row=RecuperaValor($Query);
		$mn_descuento=$row[0];

		#Verificmos que no exista el registro.
		$Query="SELECT fl_admin_pagos_alumno FROM k_admin_pagos_alumno WHERE id_invoice_stripe='$id_invoice' ";
		$row=RecuperaValor($Query);
		$fl_admin_pago=$row[0];



		if($fg_plan=='M'){

			$fe_periodo_final=strtotime('+1 month',strtotime($fe_actual));
			$fe_periodo_final= date('Y-m-d',$fe_periodo_final);

		    $mn_porcentaje_descuento=0;


			if($fl_current_plan){



		        if(empty($fl_admin_pago)){
				#Generamos su recibo de pago en BD del alumno.
				#Se genera sus pagos correspondientes.
				$Query="INSERT INTO k_admin_pagos_alumno (fl_current_plan_alumno,mn_total,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion,id_invoice_stripe,id_pago_stripe,mn_subtotal,mn_tax,mn_descuento)";
				$Query.="VALUES($fl_current_plan,$mn_total,CURRENT_TIMESTAMP,'$fe_periodo_final','1',CURRENT_TIMESTAMP,'$id_plan_stripe','$id_invoice','$id_charge',$mn_monto,$mn_tax,$mn_descuento) ";
				$fl_adm_pagos=EjecutaInsert($Query);
				}

			}


		}

		if($fg_plan=='A'){

		    $fe_periodo_final=strtotime('+1 year',strtotime($fe_actual));
			$fe_periodo_final= date('Y-m-d',$fe_periodo_final);

			if($fl_current_plan){
			    if(empty($fl_admin_pago)){
				#Se genera sus pagos correspondientes.
				$Query="INSERT INTO k_admin_pagos_alumno (fl_current_plan_alumno,mn_total,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion,id_invoice_stripe,id_pago_stripe,mn_subtotal,mn_tax,mn_descuento)";
				$Query.="VALUES($fl_current_plan,$mn_total,CURRENT_TIMESTAMP,'$fe_periodo_final','1',CURRENT_TIMESTAMP,'$id_plan_stripe','$id_invoice','$id_charge',$mn_monto,$mn_tax,$mn_descuento) ";
				$fl_adm_pagos=EjecutaInsert($Query);
				}



			}



		}
		****/








	}
	else{
		echo "fallo";

	}

    #Para cuando un pago sea failed.
	if($event_json->type == 'charge.failed'){


	    #Recupermaos datos del evento en especifico el invoice que se genero.
	    $type_evento=$event_json->type;
		$id_evento=$event_json->id;
		$id_charge=$event_json->data->object->id;

		$mn_monto=$event_json->data->object->amount;
		$id_customer=$event_json->data->object->customer;




	    #Identificamos al Instituto atraves del charge:
		$Query="SELECT fl_instituto,id_plan_stripe,fg_plan,no_total_licencias,fl_current_plan,fl_princing FROM k_current_plan WHERE id_cliente_stripe='$id_customer' ";
		$row=RecuperaValor($Query);
		$fl_instituto=$row['fl_instituto'];
		$id_plan_stripe=str_texto($row['id_plan_stripe']);
		$fg_plan=str_texto($row['fg_plan']);
		$no_licencia=$row['no_total_licencias'];
		$fl_current_plan=$row['fl_current_plan'];
		$fl_princing=$row['fl_princing'];

		if(empty($fl_instituto))
		$fl_instituto=1;

		#Se genra el registro de chargo failed
		$Query="INSERT INTO k_log_stripe(id_evento,id_invoice,id_customer,id_charge,mn_monto,id_suscripcion,mn_subtotal,mn_total,fe_creacion ) ";
		$Query.="VALUES('$id_evento','$type_evento','$id_customer','$id_charge','$mn_monto','','','',CURRENT_TIMESTAMP )";
		$fl_pago=EjecutaInsert($Query);


	    #Actualizamos el plan del instituto para que tenga failed.
		$Query=" UPDATE k_current_plan SET fg_pago_fallido='1' WHERE fl_instituto=$fl_instituto  ";
		EjecutaQuery($Query);





	}











?>