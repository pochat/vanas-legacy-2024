<?php 
# Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
 
  
  
  $no_licencias_actuales=RecibeParametroNumerico('no_licencias_actuales'); 
  $fl_instituto=RecibeParametroNumerico('fl_instituto');
  $fg_tipo_plan=RecibeParametroNumerico('fg_plan'); 
  $no_spiner=RecibeParametroNumerico('no_licencias_agregadas');
  $fe_pago=RecibeParametroFecha('fe_pago');
  $fe_pago=ValidaFecha($fe_pago);
  $cl_metodo_pago=RecibeParametroNumerico('cl_metodo_pago');
  $ds_cheque=RecibeParametroHTML('ds_cheque');
  $ds_comentario=RecibeParametroHTML('ds_comentario');


  $no_licencias_compradas=$no_licencias_actuales+$no_spiner;
  
  
  #Recuperamos los usuarios activosde esta institucion.
  $Query="SELECT COUNT(*) FROM c_usuario WHERE fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $no_usuarios_activos=$row[0];
  $rand=rand(5,1000);
  
  #Recupremos datos_del instituto.
  #Recuperamos el Nombre del Istituto yel nombre del plan.
  $Query2="SELECT ds_instituto,B.ds_descripcion 
            FROM c_instituto A
            LEFT JOIN c_plan_fame B ON A.cl_plan_fame=B.cl_plan_fame 
            WHERE fl_instituto=$fl_instituto ";
  $row2=RecuperaValor($Query2);
  $nb_instituto=str_texto($row2[0]);
  $ds_plan=str_texto($row2[1]);
  
  if(empty($ds_plan)){
      
    $Query="SELECT ds_descripcion FROM c_plan_fame WHERE cl_plan_fame=1 ";
    $ro=RecuperaValor($Query);
    $ds_plan=str_texto($ro[0]);
    
  }
      
  
  
  $Query="SELECT fl_usuario_sp FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fl_usuario=$row['fl_usuario_sp'];
  
  #Verificamos si el usuario pagaria tax
  $Query  = "SELECT  b.fl_pais, b.ds_state ";
  $Query .= "FROM c_usuario a ";
  $Query .= "JOIN k_usu_direccion_sp b ON(a.fl_usuario=b.fl_usuario_sp) ";
  $Query .= "WHERE a.fl_usuario=$fl_usuario ";
  $row = RecuperaValor($Query);
  $fl_pais = $row[0];
  $fl_provincia = $row[1];
  
  //$fl_pais=38;
  //$fl_provincia=2;
  
  $pais_tax=38;
  
  # Si el pais de canada paga tax
  if($fl_pais==$pais_tax){
      # Obtenemos la provincia
      $row0 = RecuperaValor("SELECT mn_tax FROM k_provincias WHERE fl_provincia=$fl_provincia");
      $mn_porcentaje_tax = $row0[0]/100;
  }
  else{
      $mn_porcentaje_tax = 0.0;
  }
  $mn_porcentaje_tax_=$mn_porcentaje_tax*100;
  
  #Recuperamos el email del cliente logeado/ para envio de email.
  $Query="Select ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Query);
  $email_cliente=$row[0];
  
  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual=strtotime('+0 day',strtotime($fe_actual));
  $fe_actual= date('Y-m-d',$fe_actual);
  //$fe_actual=$fe_pago;
  
  
  
  
  #identificamos en que rango se encuentra,PARA SABER  NUEVO PLAN y nuevas tarifas.
  $Query="SELECT fl_princing,no_ini, no_fin FROM c_princing WHERE 1=1 and fl_instituto=$fl_instituto ";
  $rs = EjecutaQuery($Query);
  for($i=1;$row=RecuperaRegistro($rs);$i++){
                
       $mn_rango_ini= $row['no_ini'];
       $mn_rango_fin= $row['no_fin'];
                            
        if(( $no_licencias_compradas >=$mn_rango_ini)&&($no_licencias_compradas<=$mn_rango_fin) ){
      
        
            $fl_princing=$row['fl_princing'];
            
            #Recuperamos costos segun el plan obtenido del nuevo rango de licencias.
            $Query="SELECT mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princing ";
            $row=RecuperaValor($Query);
            $mn_costo_mensual=$row[0];
            $mn_costo_anual=$row[1];
            
            $mn_descuento_anual=$row[2];
            $mn_descuento_licencia=$row[3];
            
            if(empty($mn_descuento_anual))
                $mn_descuento_anual=0;
            if(empty($mn_descuento_licencia))
                $mn_descuento_licencia=0;
            
            if($fg_tipo_plan==1){
                
                $mn_total_nuevo_plan=$mn_costo_mensual * $no_licencias_compradas;
                $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1705)."-".$rand;
                $interval="month";
                
                #Valores default stripe
                $mn_costo_x_licencia_bd=$mn_costo_mensual;
                $mn_descuentoDB=$mn_descuento_licencia;
                
                
            }
            if($fg_tipo_plan==2){
                
                $mn_total_nuevo_plan= ($mn_costo_anual*$no_licencias_compradas)*12;
                $nb_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                $id_plan=$ds_plan."-".$nb_instituto."-".ObtenEtiqueta(1706)."-".$rand;
                $interval="year";
                
                
                $mn_costo_x_licencia_bd=$mn_costo_anual;
                $mn_descuentoDB=$mn_descuento_anual;
            }
            
            $mn_tax= ($mn_total_nuevo_plan * $mn_porcentaje_tax_)/100 ;
            $mn_total_nuevo_plan_con_tax=$mn_total_nuevo_plan + $mn_tax;
            
            
        }
        
  }
  
  
  
 
  #Se calculan fechas de finalizacion del plan.
  if($fg_tipo_plan==1){
      
      $fg_tipo_plan="M";
      
      #se calcula fecha de termino del plan por mes.
      $fe_final_periodo=strtotime('+1 month',strtotime($fe_actual));
      $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
      
  }
  if($fg_tipo_plan==2){
      
      $fg_tipo_plan="A";
      
      #se calcula fecha de termino del plan por año.
      $fe_final_periodo=strtotime('+1 year',strtotime($fe_actual));
      $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
      
  } 
  
  
  
  
  $no_licencias_usadas=$no_usuarios_activos;
  $no_licencias_disponibles=$no_licencias_compradas-$no_licencias_usadas;
  
  
  #Se genera el plan del Instituto. 
  $Query="INSERT INTO k_current_plan(fl_instituto,fl_princing,  mn_total_plan,fg_plan,no_total_licencias,no_licencias_disponibles,no_licencias_usadas,no_total_storage,fg_estatus,fe_periodo_inicial,fe_periodo_final,fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta,fg_pago_manual)  ";
  $Query.="VALUES ($fl_instituto,$fl_princing,$mn_total_nuevo_plan_con_tax,'$fg_tipo_plan',$no_licencias_compradas,$no_licencias_disponibles,$no_licencias_usadas,0,'A','$fe_actual','$fe_final_periodo','','','1')";
  $fl_current_plan=EjecutaInsert($Query);
  
  $rand=rand(9,1000);
  $id_invoice="inv_1".$rand."-".$fl_instituto;
  
  
  if($fg_tipo_plan=='M'){
      
      #Se calcula el costo total    #no_licencias * el costo
      $mn_mensual_total=$no_licencias_compradas * $mn_costo_mensual ;
      $ds_descripcion=$ds_plan."-".ObtenEtiqueta(1705)." ".$no_licencias_compradas." licences";
      
      
      //if(!empty($ds_comentario))
        // $ds_descripcion=$ds_comentario;
      
      $mints=date('h:i:s');
      $fe_pago_bd=$fe_actual." ".$mints;
          
      #se inserta el registro y costo por mes en su bitacora de pagos      
      $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,ds_comentario,ds_cheque,cl_metodo_pago)";
      $Query.="VALUES($fl_current_plan,$mn_total_nuevo_plan_con_tax,'1','$fe_actual','$fe_final_periodo','1','','$ds_descripcion','1',$mn_costo_mensual,'$id_invoice',$mn_descuentoDB,'$ds_comentario','$ds_cheque',$cl_metodo_pago) ";
      $fl_adm_pagos=EjecutaInsert($Query);
      
      
      
  }
  if($fg_tipo_plan=='A'){
      
      #se calcula el monto mensual a pagar .  mn_menual * No_licencias_contratado / 12 meses.   
      $mn_costo_total_anual= ($mn_costo_anual * $no_licencias_compradas)*12 ;
      #Obtenemos el AÑO actual
      $anio_actual=date ("y"); 
      $ds_descripcion=$ds_plan."-".ObtenEtiqueta(1706)." ".$no_licencias_compradas." licences";
      
      //if(!empty($ds_comentario))
        //  $ds_descripcion=$ds_comentario;
      
      $mints=date('h:i:s');
      $fe_pago_bd=$fe_actual." ".$mints;
      
      
      #se inserta el registro y costo por mes       
      $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,ds_comentario,ds_cheque,cl_metodo_pago)";
      $Query.="VALUES($fl_current_plan,$mn_total_nuevo_plan_con_tax,'1','$fe_actual','$fe_final_periodo','1',CURRENT_TIMESTAMP,'$ds_descripcion','1',$mn_costo_anual,'$id_invoice',$mn_descuentoDB,'$ds_comentario','$ds_cheque',$cl_metodo_pago) ";
      $fl_adm_pagos=EjecutaInsert($Query);
      
      
  }
  
  
  
  #Actualizaamsos el plan elegido. por default lo dejamos en Basico
  $Query="UPDATE c_instituto SET cl_plan_fame='1' WHERE fl_instituto=$fl_instituto ";
  EjecutaQuery($Query);
  
  
  
  #Actualizamos registro del instituto y le decimos que el instituto ya tiene un plan,entonces pasa de modo trial  a Member.
  $Query="UPDATE c_instituto SET fg_tiene_plan='1'  WHERE fl_instituto=$fl_instituto ";
  EjecutaQuery($Query);
  
  
  $id_cliente_stripe="cus_".$email_cliente;
  $id_charge="ch_".$rand."I".$fl_instituto."U".$fl_usuario;
  $id_plan_creado="Essential Plan ".$nb_instituto."I".$fl_instituto;
  $id_suscripcion="sub_".$rand."I".$fl_instituto;
  $ds_email_custom=$email_cliente;
  $mn_monto_normal=$mn_total_nuevo_plan_con_tax-$mn_tax;
 
  
  #Guardaso el registro de pago(bitacora de pagos).
  $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
  $Query.="VALUES('$id_cliente_stripe','$id_charge','$id_plan_creado','$id_suscripcion','1','$ds_email_custom','$ds_descripcion',$mn_monto_normal,$mn_tax,$mn_total_nuevo_plan_con_tax,CURRENT_TIMESTAMP, $fl_instituto)";
  $fl_pago=EjecutaInsert($Query);
  

  
  #Conslutamos el registro previamente creado.
  $Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
  $row1=RecuperaValor($Query);
  $fl_current_plan=$row1[0];
  
  $Query="UPDATE k_admin_pagos SET fe_pago='$fe_pago_bd',fg_pagado='1',fl_pago_stripe=$fl_pago  WHERE fl_current_plan=$fl_current_plan AND fg_motivo_pago='1'  ";
  EjecutaQuery($Query);
  
  #GUARMADOS EL ID DEL CLIENTE EN
  $Query="UPDATE k_current_plan SET id_cliente_stripe='$id_cliente_stripe',ds_email_stripe='$ds_email_custom',id_suscripcion_stripe='$id_suscripcion' , id_plan_stripe='$id_plan_creado' WHERE fl_current_plan=$fl_current_plan AND fl_instituto=$fl_instituto ";
  EjecutaQuery($Query);
  
  
  
  
  
  
  #Enviamos email de notificacion de que se ha suscrito a un plan.
  #Se recupera el contenido del template/correo.
  
  $ds_encabezado = genera_documento_spb($fl_usuario, 1,125,$fl_instituto);
  $ds_cuerpo = genera_documento_spb($fl_usuario, 2,125,$fl_instituto);
  $ds_pie = genera_documento_spb($fl_usuario,3,125,$fl_instituto);
  $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;
  
  
  $ds_titulo=ObtenEtiqueta(1743);#etiqueta de asunto del mensjae FAME Alert Expiracion de plan 
  $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);  
  $ds_email_destinatario=$ds_email;
  $nb_nombre_dos=ObtenEtiqueta(1646);#nombre de quien envia el mensaje         
  $bcc=ObtenConfiguracion(107);#envio de copia
  $message  = $ds_contenido;
  $message = utf8_decode(str_ascii(str_uso_normal($message)));
  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
  
  
  
  
  echo"
  <script>
  
        $('#img_stripe').addClass('hidden');
  
		$.smallBox({
					title : 'Payment completed &nbsp;',
					content : \"".ObtenEtiqueta(2322)." <p class='text-align-right'><a href='".ObtenConfiguracion(121)."/AD3M2SRC4/modules/fame/billing_frm.php?c=$fl_instituto' class='btn btn-default btn-sm'>Yes</a> <a href='javascript:void(0);' class='btn btn-default btn-sm'>No</a></p><br>\",
					color : \"#659265\",
					//timeout: 8000,
					icon : \"fa fa-thumbs-up bounce animated\"
				});
		
		
		
  
		$('#txt_pago').removeClass('hidden');
  
        //window.location.href = \"".ObtenConfiguracion(121)."/AD3M2SRC4/modules/fame/members.php\";
        
  </script>
  ";
  
  
 
 
 #Verificamos si tiene cuentas y se trata de un Instituto Rector y se genera su mismo invoice, para todos los institutos.
 $Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
 $rs5 = EjecutaQuery($Query);
 for($k=1;$rowe=RecuperaRegistro($rs5);$k++){
	 
	 $fl_instituto=$rowe['fl_instituto'];
	 
 
	#Se genera el plan del Instituto. 
    $Query="INSERT INTO k_current_plan(fl_instituto,fl_princing,  mn_total_plan,fg_plan,no_total_licencias,no_licencias_disponibles,no_licencias_usadas,no_total_storage,fg_estatus,fe_periodo_inicial,fe_periodo_final,fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta,fg_pago_manual)  ";
    $Query.="VALUES ($fl_instituto,$fl_princing,$mn_total_nuevo_plan_con_tax,'$fg_tipo_plan',$no_licencias_compradas,$no_licencias_disponibles,$no_licencias_usadas,'','A','$fe_actual','$fe_final_periodo','','','1')";
    $fl_current_plan=EjecutaInsert($Query);
 
    #se inserta el registro y costo por mes       
    $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final,fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,ds_comentario,ds_cheque,cl_metodo_pago)";
    $Query.="VALUES($fl_current_plan,$mn_total_nuevo_plan_con_tax,'1','$fe_actual','$fe_final_periodo','1','','$ds_descripcion','1',$mn_costo_anual,'$id_invoice',$mn_descuentoDB,'$ds_comentario','$ds_cheque',$cl_metodo_pago) ";
    $fl_adm_pagos=EjecutaInsert($Query);
 
	#Actualizaamsos el plan elegido. por default lo dejamos en Basico
	$Query="UPDATE c_instituto SET cl_plan_fame='1' WHERE fl_instituto=$fl_instituto ";
	EjecutaQuery($Query);
 
	#Actualizamos registro del instituto y le decimos que el instituto ya tiene un plan,entonces pasa de modo trial  a Member.
    $Query="UPDATE c_instituto SET fg_tiene_plan='1'  WHERE fl_instituto=$fl_instituto ";
    EjecutaQuery($Query);
	
	#Guardaso el registro de pago(bitacora de pagos).
    $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
    $Query.="VALUES('$id_cliente_stripe','$id_charge','$id_plan_creado','$id_suscripcion','1','$ds_email_custom','$ds_descripcion',$mn_monto_normal,$mn_tax,$mn_total_nuevo_plan_con_tax,CURRENT_TIMESTAMP, $fl_instituto)";
    $fl_pago=EjecutaInsert($Query);
  

  
    #Conslutamos el registro previamente creado.
    $Query="SELECT fl_current_plan FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
    $row1=RecuperaValor($Query);
    $fl_current_plan=$row1[0];
  
    $Query="UPDATE k_admin_pagos SET fe_pago='$fe_pago_bd',fg_pagado='1',fl_pago_stripe=$fl_pago  WHERE fl_current_plan=$fl_current_plan AND fg_motivo_pago='1'  ";
    EjecutaQuery($Query);
  
    #GUARMADOS EL ID DEL CLIENTE EN
    $Query="UPDATE k_current_plan SET id_cliente_stripe='$id_cliente_stripe',ds_email_stripe='$ds_email_custom',id_suscripcion_stripe='$id_suscripcion' , id_plan_stripe='$id_plan_creado' WHERE fl_current_plan=$fl_current_plan AND fl_instituto=$fl_instituto ";
    EjecutaQuery($Query);
  
	
	
  
 } 
  
  
  
  #
  function genera_documento_spb($clave, $opc, $fl_template=0, $fl_instituto='') {  
      
      # Recupera datos del template del documento
      switch($opc){
          case 1: $campo = "ds_encabezado"; break;
          case 2: $campo = "ds_cuerpo"; break;
          case 3: $campo = "ds_pie"; break;
          case 4: $campo = "nb_template"; break;
      }
      
      # Obtenemos la informacion del template header body or footer
      $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
      $row = RecuperaValor($Query1);
      
      $cadena = $row[0];
      # Sustituye caracteres especiales
      $cadena = $row[0];
      $cadena = str_replace("&lt;", "<", $cadena);
      $cadena = str_replace("&gt;", ">", $cadena);
      $cadena = str_replace("&quot;", "\"", $cadena);
      $cadena = str_replace("&#039;", "'", $cadena);
      $cadena = str_replace("&#061;", "=", $cadena);
      
      # Recupera datos usuario
      $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno,ds_login, fg_genero, ds_email, ".ConsultaFechaBD('fe_nacimiento', FMT_FECHA)." fe_nacimiento, fl_usu_invita ";
      $Query .= "FROM c_usuario WHERE fl_usuario=$clave ";
      $row = RecuperaValor($Query);
      $ds_fname = str_texto($row[0]);
      $ds_lname = str_texto($row[1]);
      $ds_mname = str_texto($row[2]);
      $ds_login = str_texto($row[3]);
      $fg_genero = str_texto($row[4]);
      switch($fg_genero){
          case "M": $ds_genero = ObtenEtiqueta(115); break;
          case "F": $ds_genero = ObtenEtiqueta(116); break;
          case "N": $ds_genero = ObtenEtiqueta(128); break;
      }
      $ds_email = $row[5];
      $fe_nacimiento = $row[6];
      $fl_usu_invita = $row[7];
      
      
      if(empty($clave)){#se coloca en dado caso de que la clave venga vacia.(se utiliza para envio de correo de registro de menor de edad.)
          
          #Recuperamos el nombre del estudinate que se registro
          $Query="SELECT ds_first_name,ds_last_name FROM k_envio_email_reg_selfp 
			 WHERE fl_envio_correo=$fl_envio_correo ";
          $row=RecuperaValor($Query);
          $ds_fname=str_texto($row[0]);
          $ds_lname=str_texto($row[1]);
          
          
          
          
          
          
          
          $Query3  = "SELECT b.ds_nombres, b.ds_apaterno, b.ds_amaterno ";
          $Query3 .= "FROM k_noconfirmados_pro a, c_usuario b WHERE a.fl_maestro=b.fl_usuario AND a.fl_envio_correo=$fl_envio_correo ";
          $row3 = RecuperaValor($Query3);
          $fame_te_fname = str_texto($row3[0]);
          $fame_te_lname = str_texto($row3[1]);  
          $cadena = str_replace("#fame_te_fname#",$fame_te_fname, $cadena);  # fname teacher
          $cadena = str_replace("#fame_te_lname#",$fame_te_lname, $cadena);  # lname teacher   
      }
      
      if($ds_fname)
          $cadena = str_replace("#fame_fname#", $ds_fname, $cadena);                        # Student first name 
      $cadena = str_replace("#fame_mname#", $ds_mname, $cadena);                        # Student middle name 
      if($ds_lname)
          $cadena = str_replace("#fame_lname#", $ds_lname, $cadena);                        # Student last name
      $cadena = str_replace("#fame_login#", $ds_login, $cadena);                        # Student login
      $cadena = str_replace("#fame_gender#", $ds_gender, $cadena);                      # Student gender female
      $cadena = str_replace("#fame_email#", $ds_email, $cadena);                        # Student email address
      $cadena = str_replace("#fame_byear#", substr($fe_nacimiento,6,4), $cadena);    #Student year of birth 
      $cadena = str_replace("#fame_bmonth#", substr($fe_nacimiento,3,2), $cadena);   #Student month of birth 
      $cadena = str_replace("#fame_bday#", substr($fe_nacimiento,0,2), $cadena);     #Student day of birth 
      
     
      
      # Obtenemos iinformacion de la direccion
      $row = RecuperaValor("SELECT a.fl_pais, nb_pais, ds_state, ds_city, ds_number, ds_street, ds_zip, ds_phone_number  
FROM k_usu_direccion_sp a, c_pais b WHERE a.fl_pais=b.fl_pais AND a.fl_usuario_sp=$clave");
      $fl_pais = $row[0];
      $nb_pais = str_texto($row[1]);
      if($fl_pais==38){
          $row1 = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$row[2]");
          $ds_state = $row1[0];
      }
      else
          $ds_state = str_texto($row[2]);
      $ds_city = str_texto($row[3]);
      $ds_number = str_texto($row[4]);
      $ds_street = str_texto($row[5]);
      $ds_zip = str_texto($row[6]);
      $ds_phone_number = str_texto($row[7]);
      
      $cadena = str_replace("#fame_street_no#", $ds_number, $cadena);                   # Student number street
      $cadena = str_replace("#fame_street_name#", $ds_street, $cadena);                 # Student name street
      $cadena = str_replace("#fame_city#", $ds_city, $cadena);                          # Student city
      $cadena = str_replace("#fame_state#", $ds_state, $cadena);                        # Student state
      $cadena = str_replace("#fame_country#", $nb_pais, $cadena);                       # Student country
      $cadena = str_replace("#fame_code_zip#", $ds_zip, $cadena);                       # Student zip
      $cadena = str_replace("#fame_phone#", $ds_phone_number, $cadena);                 # Student phone number
      
      
      
      
      
      /***********************************/ 
      $Query="SELECT fg_plan ,no_licencias_usadas,no_licencias_disponibles,no_total_licencias, fl_princing,fe_periodo_final FROM k_current_plan where fl_instituto=$fl_instituto ";
      $row=RecuperaValor($Query);
      $fg_plan=$row[0];
      $no_licencias_usadas=$row[1];
      $no_licencias_disponibles=$row[2];
      $total_licencias=$row[3];
      $fl_princi=$row[4];
      $fecha_termino_plan=$row[5];
      
      $Query="SELECT ds_descuento_mensual,mn_descuento_licencia FROM c_princing WHERE fl_princing=$fl_princi ";
      $row=RecuperaValor($Query);
      $mn_descuento_anual=number_format($row[0])."%";
      $mn_descuento_mensual=number_format($row[1])."%";
      
      
      
      if($fg_plan=='A'){
          $plan_actual=ObtenEtiqueta(1503);
          $mn_descuento_plan=$mn_descuento_anual;
      }else{
          $plan_actual=ObtenEtiqueta(1763);
          $mn_descuento_plan=$mn_descuento_mensual;
      }
      
      
      
      #Verificamos si el Instituto ha solicitado cambiado de plan, e identificmos su nuevo pan/nueva suscripcion. el fg_motivo=3 quiere decir que es cambio de plan.
      $QueryP="SELECT fg_cambio_plan FROM  k_cron_plan_fame WHERE fg_motivo_pago='3' AND fl_instituto=$fl_instituto ";
      $rowP=RecuperaValor($QueryP);
      $fg_nuevo_plan=$rowP[0];
      
      
      
      if($fg_nuevo_plan=='A')
          $nuevo_plan=ObtenEtiqueta(1503);
      if($fg_nuevo_plan=='M')
          $nuevo_plan=ObtenEtiqueta(1763);
      
      

     
      
      $dominio_campus = ObtenConfiguracion(116);
      $link_login_fame=$dominio_campus;#bueno#fame_link_login#;
      
      
      
      
      #damos formato ala fecha de finalizacion.
      #DAMOS FORMATO DIA,MES, ANÑO
      
      $fe_termino=strtotime('+0 day',strtotime($fecha_termino_plan));
      $fe_termino= date('Y-m-d',$fe_termino);
      
      $date = date_create($fe_termino);
      $fe_terminacion_plan=date_format($date,'F j , Y');
      
      
      
      
      #Varibales para sustituir para nitificaciones realizadas en billing.
      $cadena = str_replace("#fame_current_plan#", $plan_actual, $cadena);  #plan actual/mont/anual
      $cadena = str_replace("#fame_new_plan#", $nuevo_plan, $cadena);  #nuevo_plan
      $cadena = str_replace("#fame_available_licenses#", $no_licencias_disponibles, $cadena);  #licencisas disponibles
      $cadena = str_replace("#fame_licenses_used#", $no_licencias_usadas, $cadena); #lidcenias usadas
      $cadena = str_replace("#fame_total_licenses#", $total_licencias, $cadena);  #total de licencias 
      $cadena = str_replace("#fame_link_login#", $link_login_fame, $cadena);  #total de licencias 
      $cadena = str_replace("#fame_fe_expiration_plan#", $fe_terminacion_plan, $cadena);  #total de licencias 
      $cadena = str_replace("#fame_discount_plan#", $mn_descuento_plan, $cadena);  #total de licencias 
      
      # Obtenemos los datos del maestro
      $Query3  = "SELECT b.ds_nombres, b.ds_apaterno, b.ds_amaterno FROM k_usuario_programa a ";
      $Query3 .= "LEFT JOIN c_usuario b ON(a.fl_maestro=b.fl_usuario) ";
      $Query3 .= "WHERE fl_programa_sp=$programa AND fl_usuario_sp=$clave ";
      $row3 = RecuperaValor($Query3);
      $fame_te_fname = str_texto($row3[0]);
      $fame_te_lname = str_texto($row3[1]);      
      if(empty($fame_te_fname) || empty($fame_te_lname)){
          $row00 = RecuperaValor("SELECT ds_nombres, ds_apaterno, ds_amaterno FROM c_usuario WHERE fl_usuario=$fl_usu_invita");
          $fame_te_fname = str_texto($row00[0]);
          $fame_te_lname = str_texto($row00[1]);
          $cadena = str_replace("#fame_te_fname#",$fame_te_fname, $cadena);  # fname teacher
          $cadena = str_replace("#fame_te_lname#",$fame_te_lname, $cadena);  # lname teacher 
      }
      else{
          $cadena = str_replace("#fame_te_fname#",$fame_te_fname, $cadena);  # fname teacher
          $cadena = str_replace("#fame_te_lname#",$fame_te_lname, $cadena);  # lname teacher 
      }
      
      
      
      #Recuperamos datos del administrado del Instituto.
      $Query="SELECT A.fl_usuario_sp,U.ds_nombres,U.ds_apaterno FROM c_instituto A 
          JOIN c_usuario U ON U.fl_usuario=A.fl_usuario_sp
           WHERE A.fl_instituto =$fl_instituto ";
      $row=RecuperaValor($Query);
      $fame_fname_admin=str_texto($row[1]);
      $fame_lname_admin=str_texto($row[2]);
      
      $cadena = str_replace("#fame_adm_fname#",$fame_fname_admin, $cadena);  # fname teacher
      $cadena = str_replace("#fame_adm_lname#",$fame_lname_admin, $cadena);  # lname teacher 
      

      return (str_uso_normal($cadena));
  }

  
  
?>




