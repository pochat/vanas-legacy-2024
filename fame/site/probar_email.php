<?php
# Libreria de funciones
require("../lib/self_general.php");

 //$entro=AsignarTodosLosCursosAlAlumno(11230);
 
 //echo"ya";
 //exit;

/*

$Query="INSERT INTO c_etiqueta(cl_etiqueta,nb_etiqueta,ds_etiqueta,fg_sistema)VALUES(2642,'TEST','TESTING','1')";
$FL=EjecutaInsert($Query);

$Query="SELECT fl_usuario from c_usuario WHERE fl_usuario=10";
$row=RecuperaValor($Query);


$Entro=1;
*/

/*
# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);

# Obtenemo el instituto
$fl_instituto = ObtenInstituto($fl_usuario);


echo "GeyDirectory".$espacio=GetDirectorySize(PATH_SELF_UPLOADS_F."/".$fl_instituto );

echo "Filezise".$espacio=File_Size(PATH_SELF_UPLOADS_F."/".$fl_instituto);


$Query="SELECT cl_calificacion,ds_calificacion,ds_calificacion_esp,ds_calificacion_fra,fg_aprobado,no_equivalencia,no_min,no_max,fl_instituto FROM c_calificacion_criterio WHERE fl_instituto IS NULL order by no_equivalencia ASC ";
$rs1 = EjecutaQuery($Query);
	for($i=1;$row=RecuperaRegistro($rs1);$i++) {
            $cl_calificacion=$row['cl_calificacion'];
			$ds_calificacion=$row['ds_calificacion'];
			$ds_calificacion_esp=$row['ds_calificacion_esp'];
			$ds_calificacion_fra=$row['ds_calificacion_fra'];
			$fg_aprobado=$row['fg_aprobado'];
			$no_equivalencia=$row['no_equivalencia'];
			$no_min=$row['no_min'];
			$no_max=$row['no_max'];
			
			
			$fl_instituto=503;
			
			$Query="INSERT INTO c_calificacion_criterio(cl_calificacion,ds_calificacion,ds_calificacion_esp,ds_calificacion_fra,fg_aprobado,no_equivalencia,no_min,no_max,fl_instituto)
					VALUES('$cl_calificacion','$ds_calificacion','$ds_calificacion_esp','$ds_calificacion_fra','$fg_aprobado',$no_equivalencia,$no_min,$no_max,$fl_instituto)";
			EjecutaQuery($Query);
			

	}
    
    */



require_once('../lib/Stripe2022/vendor/stripe/stripe-php/init.php');
 
# Variables Stripe FAME
$secret_key = ObtenConfiguracion(112);

#vnas usa
#$secret_key = ObtenConfiguracion(167);


 /**
 *Este funciona para cuando se quiere agregar un pago en stripe y generar o actualizar el cronjob.
 */
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

  /*$fecha = new DateTime();
  $dat= $fecha->format('U = Y-m-d H:i:s') . "\n";

  $fecha->setTimestamp(1635549803);
  $date= $fecha->format('U = Y-m-d H:i:s') . "\n";


  $fe_actual=strtotime('+0 day',strtotime('2021-10-29'));
  */
  $fe_actual=strtotime('+0 day',strtotime('2021-11-10'));
  $customer=Customer('cus_Luw0V8ayic15XI');
  //$customer=Customer('cus_LFWXbPkamu1WZz');
  ##$tieneplan=getPlan('Essentials Plan_Hayden_Yerbury_Annual_656');
  $getSucipcion=getSubscripcion('sub_1LD68QA80LPiOk15BmOSgW5a');
  
  //$charge = \Stripe\Charge::all(['created[gte]'=>'1638899614']);//1. cus_KV4ZOLxiZ9AWJI   2.
 // $charge = \Stripe\Charge::retrieve('ch_3KWaVXA80LPiOk151gMZ5gFL'); //ch_3Jq4OJA80LPiOk150pj7z13J
  $charge = \Stripe\Charge::retrieve('ch_3M2osAA80LPiOk151W9VkgSF'); 
  //$payment_i=$charge->payment_intent;
  
  $stripe = new \Stripe\StripeClient($secret_key);
  $stripe->paymentIntents->retrieve('pi_3LvL9QA80LPiOk151Rl1Gt44');    
#  
  
  $getSucipcion=getSubscripcion('sub_1LD68QA80LPiOk15BmOSgW5a');
  
  if(!empty($getSucipcion)){
      
      #Recupermo el plan.
      $plan_id=$getSucipcion->plan->id;


      if(!empty($plan_id)){
          
          
              #1 Se cancela la suscripcion del instituto.
              $subscription = \Stripe\Subscription::retrieve('sub_1LD68QA80LPiOk15BmOSgW5a');
              //$subscription->cancel();
         
              #2 Se cancela plan del instituto.
              $plan = \Stripe\Plan::retrieve($plan_id);
              //$plan->delete();
 /*            
        #     $charge = \Stripe\Charge::retrieve('ch_3KZ1UDA80LPiOk151drj2Ymd');
       #       $payment_i=$charge->payment_intent;


              
       #       $re = \Stripe\Refund::create([
       #           'payment_intent' => $payment_i,
       #         ]);
            
              

              $entro=1;
             */
      }
      

            


  }
  
  
  $entor=1;

  /*

  $stripe = new \Stripe\StripeClient(
  'sk_test_4eC39HqLyjWDarjtT1zdp7dc'
   );
    $stripe->refunds->create([
        'charge' => 'ch_1HAc0y2eZvKYlo2CzRRPiJH3',
    ]);

  */


  /*  
	$mn_amount=3000;
	$no_licencias_compradas=100;
	$id_customer="cus_DgqbuPT2zekZ6a";
	$mn_porcentaje_tax_=0;
	$fg_tipo_plan="A";
	
	$Query="SELECT * FROM c_instituto where fl_instituto=15";# 
	$row=RecuperaValor($Query);
	$fl_instituto=$row['fl_instituto'];
	$fl_usuario=$row['fl_usuario_sp'];
	$nb_instituto=$row['ds_instituto'];
	
	$Query="SELECT ds_nombres,ds_apaterno,fl_perfil_sp,ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
	$row=RecuperaValor($Query);
	$ds_nombre=$row[0];
	$ds_apaterno=$row[0];
	$fl_perfil_fame=$row['fl_perfil_sp'];
	$ds_email=$row['ds_email'];
	$nb_user_actual=$ds_nombre." ".$ds_apaterno;
	
	#Obtenemos fecha actual :
	$Query = "Select CURDATE() ";
	$row = RecuperaValor($Query);
	$fe_actual = str_texto($row[0]);
	$fe_actual=strtotime('+0 day',strtotime($fe_actual));
	$fe_actual= date('Y-m-d',$fe_actual);
    //$fe_actual="2019-09-28";
		
	#identificamos en que rango se encuentra, e identificamos el tipo de plan.
	$Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
	$rs = EjecutaQuery($Query);
	for($i=1;$row=RecuperaRegistro($rs);$i++) {

		$mn_rango_ini= $row['no_ini'];
		$mn_rango_fin= $row['no_fin'];

		if(( $no_licencias_compradas >=$mn_rango_ini)&&($no_licencias_compradas<=$mn_rango_fin) ){
			$fl_princing=$row['fl_princing'];

			#Recuperamos costos segun el plan .
			$Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princing";
			$row=RecuperaValor($Query);
			$mn_costo_mensual=$row[0];
			$mn_descuento_anual=$row[2];
			$mn_descuento_licencia=$row[3];
			$mn_costo_anual=$row[1] * 12;

			if(empty($mn_descuento_anual))
				$mn_descuento_anual=0;
			if(empty($mn_descuento_licencia))
				$mn_descuento_licencia=0;
			
		}

	}


	#para crear el nombre del plan en strippe.
	if($fg_tipo_plan=='A'){	
		#Se asignan nombres para crear plan en strippe
		$interval="year";
		$mn_costo_x_licencia=Conv_Dollars_Stripe($mn_costo_anual);
		$mn_descuentoDB=$mn_descuento_anual;		
	}
	if($fg_tipo_plan=='M'){		
		#Se asignan nombres para crear plan en strippe
		$interval="month";
		$mn_costo_x_licencia=Conv_Dollars_Stripe($mn_costo_mensual);
		$mn_descuentoDB=$mn_descuento_licencia;
		
	}	
	$nb_plan=$nb_instituto;
	$id_plan=$nb_instituto;
	$currency=ObtenConfiguracion(113);
		
	/*$product = \Stripe\Product::create([
    'name' => $nb_plan,
    'type' => 'service',
     ]);	
	$id_prod=$product->id;	
		*/
	/*	
	$plan = \Stripe\Plan::create(array(
    "name" => $nb_plan,
    "id" => $id_plan,
    "interval" => $interval,
    "currency" => $currency,
    "amount" => $mn_costo_x_licencia
    )
    );
    
    $id_plan=$plan->id;
    $id_plan_creado=$plan->id;

	 
	$subscription= \Stripe\Subscription::create(array(
		  "customer" => $id_customer,
		  "plan" => $id_plan,
		  "quantity"=>$no_licencias_compradas,
		  "tax_percent" => $mn_porcentaje_tax_,
		  
		));
	$id_suscripcion=$subscription->id;
	
	
	#Verificamos el eveno creado en Strippe, para despues poder recuperar elid del pago que se realizo. y actualizar su descripcion.
	$event = \Stripe\Event::all(array("limit" => 2));
	$id_charges=$event->data;    
	$id_evento=$id_charges[1]->data;
	$id_charge=$id_evento['object']->charge;
	$id_invoice=$id_evento['object']->id;  
	
	
	if($fg_tipo_plan=='M'){
	
			#se calcula fecha de termino del plan por mes.
			$fe_final_periodo=strtotime('+30 day',strtotime($fe_actual));
			$fe_final_periodo= date('Y-m-d',$fe_final_periodo);
			
	}
	if($fg_tipo_plan=='A'){
		
			#se calcula fecha de termino del plan por año.
			$fe_final_periodo=strtotime('+1 year',strtotime($fe_actual));
			$fe_final_periodo= date('Y-m-d',$fe_final_periodo);
			
	}
*/
	
	#Se genera el plan del Instituto. 
	//$Query="INSERT INTO k_current_plan  (fl_instituto,fl_princing,  mn_total_plan,fg_plan,no_total_licencias,no_licencias_disponibles,no_licencias_usadas,no_total_storage,fg_estatus,fe_periodo_inicial,fe_periodo_final,fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta )  ";
	//$Query.="VALUES ($fl_instituto,$fl_princing,$mn_total,'$fg_tipo_plan',$no_licencias_compradas,$no_licencias_disponibles,$no_licencias_usadas,'','A','$fe_actual','$fe_final_periodo','$fe_mes_expiracion_tarjeta','$fe_anio_expiracion_tarjeta')";
	//$fl_current_plan=EjecutaInsert($Query);
 /*   $Query="UPDATE k_current_plan SET fe_periodo_final='$fe_final_periodo',id_plan_stripe='$id_plan',id_suscripcion_stripe='$id_suscripcion'
		    WHERE fl_current_plan=11 ";
	EjecutaQuery($Query);

	if($fg_tipo_plan=='M'){
				#Se calcula el costo total    #no_licencias * el costo
				$mn_mensual_total=$no_licencias_compradas * $mn_costo_mensual ;
				$ds_descripcion=$ds_plan."-".ObtenEtiqueta(1705)." ".$no_licencias_compradas." licences";
				
				#se inserta el registro y costo por mes en su bitacora de pagos      
				$Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento)";
				$Query.="VALUES(11,$mn_total,'1','$fe_actual','$fe_final_periodo','1','','$ds_descripcion','1',$mn_costo_mensual,'$id_invoice',$mn_descuentoDB) ";
				$fl_adm_pagos=EjecutaInsert($Query);			
	}
	if($fg_tipo_plan=='A'){	
				#se calcula el monto mensual a pagar .  mn_menual * No_licencias_contratado / 12 meses.   
				$mn_costo_total_anual= ($mn_costo_anual * $no_licencias_compradas) ;
				#Obtenemos el AÑO actual
				$anio_actual=date ("y"); 
				$ds_descripcion=$nb_plan."-".ObtenEtiqueta(1706)." ".$no_licencias_compradas." licences";
				
				#se inserta el registro y costo por mes       
				$Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento)";
				$Query.="VALUES(11,3000,'1','$fe_actual','$fe_final_periodo','1','','$ds_descripcion','1',$mn_costo_anual,'$id_invoice',$mn_descuentoDB) ";
				$fl_adm_pagos=EjecutaInsert($Query);			
	}
	
	
	
	

	#Guardaso el registro de pago(bitacora de pagos).
	$Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
	$Query.="VALUES('$id_customer','$id_charge','$id_plan_creado','$id_suscripcion','1','$ds_email','$ds_descripcion',3000,0,3000,CURRENT_TIMESTAMP, $fl_instituto)";
	$fl_pago=EjecutaInsert($Query);

	$Query="SELECT fe_creacion FROM k_pago_stripe WHERE fl_pago=$fl_pago ";
	$row=RecuperaValor($Query);
	$fe_pago=$row[0];

	#Conslutamos el registro previamente creado.
	$Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
	$row1=RecuperaValor($Query);
	$fl_current_plan=$row1[0];

	$Query="UPDATE k_admin_pagos SET fe_pago='$fe_pago',fg_pagado='1',fl_pago_stripe=$fl_pago  WHERE fl_current_plan=11 AND fg_motivo_pago='1'  ";
	EjecutaQuery($Query);

	
	*/
	
								
                                
	
	
	
	
	
	
	







 
 
 
 
 
 
 
 
 
 
 
 
 /*
$Query="SELECT ds_first_name,ds_last_name,ds_email,no_registro,fl_invitado_por_instituto,fg_genero,fl_envio_correo  
		FROM k_envio_email_reg_selfp WHERE fl_envio_correo>5644 AND fl_envio_correo<=5668 ";
$rs = EjecutaQuery($Query);
for($i=1;$row=RecuperaRegistro($rs);$i++){
    $fl_envio_correo=$row['fl_envio_correo'];
    $ds_first_name=$row['ds_first_name'];
	$ds_last_name=$row['ds_last_name'];
	$ds_email=$row['ds_email'];
	$no_registro=$row['no_registro'];
	$fl_invitado_por_instituto=$row['fl_invitado_por_instituto'];
	$fg_genero=$row['fg_genero'];
	
	
	# Genera un identificador de sesion
	$cl_sesion_nueva = sha256($ds_email.$ds_first_name.$ds_last_name);
	$fg_activo=1;//se activa cu cuenta
	 
	    # Inserta el usuario
        $Query  = "INSERT INTO c_usuario(ds_login, ds_password,ds_alias, cl_sesion, fg_activo, fe_alta, no_accesos, ";
        $Query .= "ds_nombres, ds_apaterno, ds_email, ";
        $Query.="fg_genero,fe_nacimiento, ";
        $Query.="fl_perfil_sp,fl_instituto, fl_usu_invita) ";
        $Query .= "VALUES('$ds_email', '".sha256($ds_email)."','$ds_email', '$cl_sesion_nueva', '1', CURRENT_TIMESTAMP, 0, ";
        $Query .= "'$ds_first_name', '$ds_last_name','$ds_email', ";
        $Query .= "'$fg_genero','', ";
        $Query .=" 15,77,2694) ";
        
		$fl_usuario_sp=EjecutaInsert($Query);
		
		
		$qu="UPDATE k_envio_email_reg_selfp SET fl_usuario=$fl_usuario_sp  where fl_envio_correo=$fl_envio_correo ";
		EjecutaQuery($qu);
	

        #Se inserta por default el Gettin Started y por defalu lo asignamos en el orden 1,para que parezca hasta arriba.
		$Query ="INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fg_certificado,fg_status,fg_pagado,mn_pagado,fl_maestro,fg_status_pro,fe_inicio_programa) ";
		$Query.="VALUES($fl_usuario_sp,33,0,'0','0','RD','0',0,2694,'0',CURRENT_TIMESTAMP)";
		$fl_usu_pro=EjecutaInsert($Query);

        EjecutaQuery("INSERT INTO c_alumno_sp(fl_alumno_sp)values($fl_usuario_sp) ");
        EjecutaQuery("INSERT INTO k_usu_direccion_sp(fl_alumno_sp)values($fl_usuario_sp) ");

        $qUERY="insert into c_grupo_fame(nb_grupo,fl_alumno_sp,fl_instituto,fl_programa_sp,fl_usu_pro) 
                values('Group-A',$fl_usuario_sp,77,33,$fl_usu_pro) ";
        $fl_uisu=EjecutaInsert($qUERY);

	
	
}
*/




/* ASIGNAR ADMINS A QUE TENGAM DIRECCION
$Query="SELECT fl_usuario FROM c_usuario WHERE fl_perfil_sp=".PFL_ADMINISTRADOR." ";
$rs = EjecutaQuery($Query);
for($i=1;$row=RecuperaRegistro($rs);$i++){

    $fl_usuario=$row['fl_usuario'];

    $Query="SELECT COUNT(*) FROM k_usu_direccion_sp WHERE fl_usuario_sp=$fl_usuario  ";
    $rop=RecuperaValor($Query);
    if(empty($rop[0])){

        $QU="insert into k_usu_direccion_sp (fl_usuario_sp)values($fl_usuario)";
        EjecutaQuery($QU);


    }
}*/

/* para generar registros dependientes de un usuario por si no existe.

$Query="SELECT fl_usuario FROM c_usuario WHERE fl_perfil_sp=".PFL_ADMINISTRADOR." ";
$rs = EjecutaQuery($Query);
for($i=1;$row=RecuperaRegistro($rs);$i++){

    $fl_usuario=$row['fl_usuario'];

    $Query="SELECT COUNT(*) FROM c_administrador_sp WHERE fl_adm_sp=$fl_usuario  ";
    $rop=RecuperaValor($Query);
    if(empty($rop[0])){

        $QU="insert into c_administrador_sp (fl_adm_sp)values($fl_usuario)";
        EjecutaQuery($QU);


    }


}
*/

/*
#Para crear nuevos usuarios de cada instituto.
$Query="SELECT * FROM c_instituto WHERE fg_scf='1' ";
$rs = EjecutaQuery($Query);
for($i=1;$row=RecuperaRegistro($rs);$i++){
    
    $fl_instituto=$row['fl_instituto'];
    $ds_id=$row['school_id'];
    $nb_instituto=$row['ds_instituto'];

    # Genera un identificador de sesion
    $cl_sesion_nueva = sha256($ds_id.$nb_instituto);

    #VerificaSi no existe usuario
    $Query="SELECT count(*) from c_usuario WHERE fl_perfil_sp=13 AND fl_instituto=$fl_instituto ";
    $rowp=RecuperaValor($Query);

    //if(empty($rowp[0])){

        # Inserta el usuario
        $Query  = "INSERT INTO c_usuario(ds_login, ds_password,ds_alias, cl_sesion, fg_activo, fe_alta, no_accesos, ";
        $Query .= "ds_nombres, ds_apaterno, ds_email, ";
        $Query.="fg_genero,fe_nacimiento, ";
        $Query.="fl_perfil_sp,fl_instituto, fl_usu_invita) ";
        $Query .= "VALUES('$ds_id', '".sha256($ds_id)."','$ds_id', '$cl_sesion_nueva', '1', CURRENT_TIMESTAMP, 0, ";
        $Query .= "'Alain', 'Rondel','arondel@csf.bc.ca', ";
        $Query .= "'M','', ";
        $Query .=" 13,$fl_instituto,185) ";
        $fl_usuario_sp=EjecutaInsert($Query);



        $Query="UPDATE c_instituto SET fl_usuario_sp=$fl_usuario_sp WHERE fl_instituto=$fl_instituto ";
        EjecutaQuery($Query);

    //}




}
*/



/*  se utilizo para asignar grupos a nueva modalidad.
$Query="SELECT fl_alumno_sp,nb_grupo FROM c_alumno_sp WHERE nb_grupo IS NOT NULL AND nb_grupo<>'' ";
  $rs = EjecutaQuery($Query);
  for($i=1;$row=RecuperaRegistro($rs);$i++){
	  
	  $fl_usuario_std=$row['fl_alumno_sp'];
      $nb_grupo=$row['nb_grupo'];

      $Query="SELECT fl_instituto,fl_usu_invita FROM c_usuario WHERE fl_usuario=$fl_usuario_std ";
      $row=RecuperaValor($Query);
      $fl_insitut=$row['fl_instituto'];
      $fl_usuario=$row['fl_usu_invita'];

      $Querypro="SELECT fl_programa_sp,fl_usu_pro FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario_std ";
      $topi=EjecutaQuery($Querypro);
      for($i2=1;$row2=RecuperaRegistro($topi);$i2++){
          $fl_programa_sp=$row2['fl_programa_sp'];
          $fl_usu_pro=$row2['fl_usu_pro'];

              #Verficia si existe.
          $Query='SELECT COUNT(*)FROM c_grupo_fame WHERE fl_alumno_sp='.$fl_usuario_std.' AND nb_grupo="'.$nb_grupo.'" AND fl_programa_sp='.$fl_programa_sp.'  AND fl_usu_pro='.$fl_usu_pro.' ';
              $rol=RecuperaValor($Query);
              if(empty($rol[0])){  
              
                  if(!empty($nb_grupo)){

                      $Query='INSERT INTO c_grupo_fame(nb_grupo,fl_alumno_sp,fl_usuario_creacion,fl_instituto,fe_creacion,fe_ulmod,fl_programa_sp,fl_usu_pro) 
                                  VALUES("'.$nb_grupo.'",'.$fl_usuario_std.','.$fl_usuario.','.$fl_insitut.',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'.$fl_programa_sp.','.$fl_usu_pro.') ';
                      $fl_group_insrt=EjecutaInsert($Query);
                  }

              }
      }

  }
 */
 
  $entro=AsignarTodosLosCursosAlAlumno(2847);
  /*
  
   $Query="SELECT fl_usuario FROM c_usuario WHERE fl_perfil_sp IS NOT NULL;";
  $rs = EjecutaQuery($Query);
  for($i=1;$row=RecuperaRegistro($rs);$i++){
	  
	  $fl_usuario=$row['fl_usuario'];
	  
	  
	 EjecutaQuery("DELETE FROM k_notify_fame_feed WHERE fl_usuario=$fl_usuario "); 
	   #Inserta por default las notificaciones.
	 $Query="INSERT INTO k_notify_fame_feed (fl_usuario,fg_nuevo_post,fg_coment_post,fg_like_post,fg_ayuda_post)";
	 $Query.="VALUES($fl_usuario,'1','1','1','1')";
	 EjecutaQuery($Query);
	  
	  
	  
  }
  */
  
  
  
  /*  //07_jul_2019,, no se guaradba el app_fee se hixo neceraior esta recuperacion
  
  $Query="SELECT cl_sesion,fg_pago,mn_pagado FROM c_sesion where 1=1 ";
  $rs = EjecutaQuery($Query);
  for($i=1;$row=RecuperaRegistro($rs);$i++){
	  
	  $cl_se=$row[0];
	  $fg_pago=$row[1];
	  echo$mn_pago=$row[2];
	  echo"===";
	  
	    echo$Query2="SELECT mn_app_fee FROM k_app_contrato WHERE cl_sesion='$cl_se' ";
		$rop=RecuperaValor($Query2);
		echo$mn_app_fee=$rop[0];
		
	echo"<br>";
		
		if($mn_pago=='0.00'){
			echo"entro <br>";
			$Wi="UPDATE c_sesion SET mn_pagado=$mn_app_fee WHERE cl_sesion='$cl_se'";
			EjecutaQuery($Wi);
			echo $Wi;
			
		} 
	  
	  
	 echo"<br>"; 
	  
	  
	  
  }
  echo"termino";
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  

      $fl_usuario_std=938;
	  $nb_grupo="GPD-2839";
  

	  # Obtenmos el template
	  $ds_header = genera_documento_sp($fl_usuario_std, 1, 151, $nb_grupo);
	  $ds_body = genera_documento_sp($fl_usuario_std, 2, 151, $nb_grupo);
	  $ds_footer = genera_documento_sp($fl_usuario_std, 3, 151, $nb_grupo);
	  
	  # Nombre del template
	  $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=121 AND fg_activo='1'";
	  $template = RecuperaValor($Query0);
	  $nb_template = str_uso_normal($template[0]);

	  # Mensaje
	  $ds_mensaje = $ds_header.$ds_body.$ds_footer;
		
      # Este email es necesario
      $from = "noreply@myfame.org"; # ObtenConfiguracion(4);##de donde sale el email.
      $ds_email_alu="mike@vanas";	
	  $from_fame="terrylonz@hotmail.com";
	  $cc="mike@vanas.ca";
	  
	  $ds_mensaje="hola";
	  
	  # Enviamos el correo al usuario dependiendo de la accion
      $enviado=EnviaMailHTML2($from, $from, $ds_email_alu, $nb_template, $ds_mensaje."_alumno");
	  
      # Enviamos otro a fame
      $enviado2=EnviaMailHTML2($from, $from, $from_fame, $nb_template, $ds_mensaje."_admin");	  
		
      if($enviado){
		  echo"enviadoss  <script>alert('enviados  $ds_email_alu');</script>";
      }

	  if($enviado2){
		  echo"enviadoss  <script>alert('enviados 2 $from_fame');</script>";
      }
	  
	  
	  
# Envia correo con HTML
function EnviaMailHTML2($p_from_name, $p_from_mail, $p_to, $p_subject, $p_message, $p_bcc='') {
  
  # Inicializa variables de ambiente para envio de correo
 // ini_set("SMTP", MAIL_SERVER);
  //ini_set("smtp_port", MAIL_PORT);
  //ini_set("sendmail_from","miguel@loomtek.mx");
  
  $to = str_ascii($p_to);
  $subject = str_ascii($p_subject);
  $headers = "From: $p_from_name<$p_from_mail>\r\nReply-To: $p_bcc\r\n";
  if(!empty($p_bcc))
    $headers .= "Bcc: $p_bcc\r\n";
  $headers = str_ascii($headers);
  $message = ConvierteHTMLenMail($p_message, $headers);
  return mail($to, $subject, $message['multipart'], $message['headers']);
}

    */



    }
    catch (\Stripe\Error\ApiConnection $e) {
        // Network problem, perhaps try again.
        $e_json = $e->getJsonBody();
        $err = $e_json['error'];
        $result['error'] = $err['message'];
    }
    catch (\Stripe\Error\InvalidRequest $e) {
        // You screwed up in your programming. Shouldn't happen!
        $e_json = $e->getJsonBody();
        $err = $e_json['error'];
        $result['error'] = $err['message'];
    }
    catch (\Stripe\Error\Api $e) {
        // Stripe's servers are down!
        $e_json = $e->getJsonBody();
        $err = $e_json['error'];
        $result['error'] = $err['message'];
    }
    catch (\Stripe\Error\Base $e) {
        // Something else that's not the customer's fault.
        $e_json = $e->getJsonBody();
        $err = $e_json['error'];
        $result['error'] = $err['message'];
        
        
    }
    echo json_encode((Object) $result);
    
?>				  
