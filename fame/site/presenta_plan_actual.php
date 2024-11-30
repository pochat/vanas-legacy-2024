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
  $no_total_licencias_actuales= RecibeParametroNumerico('no_total_licencias_actuales'); 
 
  #Recuperamos el Plan.
  $Query="SELECT  B.nb_plan FROM c_instituto A
		JOIN c_plan_fame B on B.cl_plan_fame=A.cl_plan_fame
		WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $nb_plan_instituto=$row[0];  
  
  
  #Verificamos si el Instituto paga via trasfrenecia/deposito abancario.
  $fg_pago_manual=ObtenMetodoPagoInstituto($fl_instituto);
  
  if(!empty($fg_pago_manual))
  $btn_hidden="hidden";
  else
  $btn_hidden="";    
  
  #vERIFICAMOS SI EXISTE UN CRON PARA CAMBIO DE PLAN que sea 3 o 2.
  $Query="SELECT COUNT(*),fg_cambio_plan,mn_cantidad_licencias FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto AND fg_motivo_pago IN(2,3) AND fg_cambio_plan IS NOT NULL ";
  $row=RecuperaValor($Query);
  $existe=$row[0];
  $fg_cambio_plan=$row[1];
  $mn_cantidad_licencias=$row[2];


  echo"
  <style>
  .popover {
  font-size:13px;
  }
  </style>
  ";
  
  if($existe){
      
      if($fg_cambio_plan=='A')
          $nb_plan_fame=$nb_plan_fame." ".ObtenEtiqueta(1503);
      else
          $nb_plan_fame=$nb_plan_fame." ".ObtenEtiqueta(1502);
      
      $dts="<b>New Plan:</b>$nb_plan_fame <br/>
            <b>".ObtenEtiqueta(988).":</b> $mn_cantidad_licencias  ";
      
      $presenta_tooltip_cambio_plan="<a href='javascript:void(0);' style='font-size: 13px;' rel='popover' data-placement='bottom' data-original-title='".ObtenEtiqueta(1720)."' data-content='$dts'  data-html='true'><i class='fa fa-info-circle'></i> </a>";
      
      
  }else{
      $presenta_tooltip_cambio_plan="";
  }
  
  
  #Recuperamos el peso que tiene sus archivos del Instituto.
  $mn_espacio_usado= size("uploads/".$fl_instituto);
  if(empty($mn_espacio_usado))
      $mn_espacio_usado="0 GB";
  
  
?>

  <div class="row" >
                                   <div class="col-xs-4 col-sm-4 text-right">
								        <p style="font-size:15px;"><?php echo ObtenEtiqueta(1711)." : ";  ?></p>
								   
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(987)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(988)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(989)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(990)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(991)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(992)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(993)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(994)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(995)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(996)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(997)." : ";  ?></p>
                                        <p style="font-size:15px;"><?php echo ObtenEtiqueta(998)." : ";  ?></p>

                                   </div>



                                   
                                   <?php 
                                  
                                        #Se obtiene registros actuales de la cuenta.filtrado por el usuario que se esta logeando 
                                        $Query="SELECT K.fl_instituto,K.fl_princing,K.fg_plan,K.no_total_licencias,K.no_licencias_usadas,K.no_licencias_disponibles,K.no_total_storage,K.fe_periodo_inicial,K.fe_periodo_final,";
                                        $Query.="no_ini,no_fin,ds_descuento_mensual,mn_mensual,mn_descuento_licencia,mn_anual,fl_current_plan,K.fg_estatus ";
                                        $Query.="FROM k_current_plan K
                                        JOIN c_princing P ON P.fl_princing=K.fl_princing               
                                        WHERE K.fl_instituto=$fl_instituto  ";
                                        $data=RecuperaValor($Query);
                                        
                                        $fl_instituto_cuenta=$data['fl_instituto'];
                                        $mn_espacio_disco=$data['no_total_storage'];
                                        $fl_precio=$data['fl_princing'];
                                        $fg_tipo_plan=$data['fg_plan'];
                                        $fl_current_plan=$data['fl_current_plan'];
                                        $no_total_licencias=$data['no_total_licencias'];
                                        if(empty($no_total_licencias))
                                            $no_total_licencias=$no_total_licencias_actuales;
                                        $no_licencias_usadas=$data['no_licencias_usadas'];
                                        $no_licencias_disponibles=$data['no_licencias_disponibles'];
                                        $fe_inicio_plan=$data['fe_periodo_inicial'];
                                        $fe_terminacion_plan=$data['fe_periodo_final'];
                                        $mn_descuento_anual=$data['ds_descuento_mensual'];
                                        $mn_descuento_mensual=$data['mn_descuento_licencia'];
                                        $fg_estatus=$data['fg_estatus'];
                                        
                                        
                                        
                                        if($fg_tipo_plan=="M"){
                                            $fg_plan=ObtenEtiqueta(1520); 
                                            //$mn_costo_otro="$".number_format( $data['mn_mensual'] )." ";// $mn_costo="$".number_format($data['mn_mensual'])." per license per month";
                                        
                                            #SE CALCULA EL COSTO POR MES
                                            $mn_costo="$".number_format($data['mn_mensual'],2)." per license per month";///$mn_costo_otro="$".number_format(($data['mn_mensual'] * $data['no_total_licencias']),2);
											$mn_costo_otro="$".number_format(($data['mn_mensual'] * $data['no_total_licencias']),2)." per month";
                                            
                                            $mn_descuento="<small style='color:#999;font-size:15px;'>(".number_format($mn_descuento_mensual)."% ".ObtenEtiqueta(1751).")</small>";
                                        }
                                        
                                        if($fg_tipo_plan=="A"){
                                            $fg_plan=ObtenEtiqueta(1521);
                                            

                                            #se calcula su costo por mes.
                                            $mn_costo="$".$data['mn_anual']." ".ObtenEtiqueta(2332).""; ///$mn_costo_otro="$".number_format(  (  ($data['mn_anual'] * $data['no_total_licencias']) / 12) ,2)." per month" ;
											$mn_costo_otro="$".number_format(  (  ($data['mn_anual'] * $data['no_total_licencias'])*12 / 12) ,2)." per month" ; // $mn_costo="$".$data['mn_anual']." per license per year";
                                            $mn_descuento="<small style='color:#999;font-size:15px;'>(".number_format($mn_descuento_anual)."% ".ObtenEtiqueta(1751).")</small>";
  
                                        }
                                        if(empty($fg_tipo_plan)){
                                            $fg_plan="&nbsp;"; 
											$mn_costo="&nbsp;";
											$mn_costo_otro="&nbsp;";
											$nb_plan_instituto="&nbsp;";
											
                                        }
                                        if(empty($mn_descuento))
                                            $mn_descuento="&nbsp;";

										if(empty($no_total_licencias))
                                            $no_total_licencias="0";
											
                                        
										 if(empty($no_licencias_usadas))
                                            $no_licencias_usadas="0";
											
                                       
										if(empty($no_licencias_disponibles))
                                            $no_licencias_disponibles="0";

										if(empty($fe_inicio_plan))
                                            $fe_inicio_plan="&nbsp;";

										if(empty($fe_terminacion_plan))
                                        $fe_terminacion_plan="&nbsp;";
                                        
                                        
                                        if($fl_instituto_cuenta){
                                            
                                            
                                               if($fg_tipo_plan=='M'){
                                            
                                                        $fe_proximo_pagoe=strtotime('+1 day',strtotime($fe_terminacion_plan));
                                                        $fe_proximo_pago= date('Y-m-d',$fe_proximo_pagoe);
                                        
                                                        #DAMOS FORMATO DIA,MES, ANÑO
                                                        $date = date_create($fe_terminacion_plan);
                                                        $fe_terminacion_plan=date_format($date,'F j, Y');
                                       
                                                        #DAMOS FORMATO DIA,MES,AÑO
                                                        $date=date_create($fe_proximo_pago);
                                                        $fe_proximo_pago=date_format($date,'F j, Y');

                                                        $fe_reduccion_contrato=$fe_proximo_pago;
                                                        
                                                }
                                                else if($fg_tipo_plan=='A'){
                                                
                                                   
                                                   
                                                   
                                                    $Query="SELECT A.fl_admin_pagos, A.fe_periodo_inicial,A.fg_pagado ";
                                                    $Query.="FROM k_current_plan K ";
                                                    $Query.="JOIN k_admin_pagos A ON A.fl_current_plan=K.fl_current_plan ";
                                                    $Query.="WHERE A.fl_current_plan =$fl_current_plan AND K.fl_instituto=$fl_instituto AND fg_pagado='0' ORDER BY fl_admin_pagos ASC  ";
                                                    $row=RecuperaValor($Query);
                                                    $fe_proximo_pago_mes=!empty($row['fe_periodo_inicial'])?$row['fe_periodo_inicial']:NULL;
                                                    
                                                    
                                                    #DAMOS FORMATO DIA,MES,AÑO
                                                    $date=date_create($fe_proximo_pago_mes);
                                                    $fe_proximo_pago_mes=date_format($date,'F j, Y'); //l jS F Y
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    
                                                        $fe_proximo_pagoe=strtotime('+1 day',strtotime($fe_terminacion_plan));
                                                        $fe_proximo_pagoe= date('Y-m-d',$fe_proximo_pagoe);
                                                        #damos formato
                                                        $date=date_create($fe_proximo_pagoe);
                                                        $fe_proximo_pagoe=date_format($date,'F j, Y');
                                                        
                                                        
                                                        
                                                        #DAMOS FORMATO DIA,MES, ANÑO
                                                        $date = date_create($fe_terminacion_plan);
                                                        $fe_terminacion_plan=date_format($date,'F j, Y');
                                                   
                                                        $fe_proximo_pagoe=$fe_terminacion_plan;
                                                          
                                                        
                                                       $fe_reduccion_contrato=$fe_proximo_pagoe;
                                                }
                                                
                                                
                                        }
                                      
                                        if($fl_instituto==77)
										$mn_tipo_moneda="CAD";
									    else
                                        $mn_tipo_moneda=ObtenConfiguracion(113);

                                        if(empty($mn_espacio_disco))
                                            $mn_espacio_disco=$mn_espacio_disco="0 GB";
                                        else
                                            $mn_espacio_disco=$mn_espacio_disco." GB";                                        
                                        
                                         $ds_option_renovacion=ObtenEtiqueta(1552);//reduce my contarct
                                        
                                         #si el plan esta congelado(Freze) muestra leyenda que esta congelado.
                                         if($fg_estatus=='F'){
                                             $fe_proximo_pago="<span class='label label-success'>".ObtenEtiqueta(2646)."</span>";
                                         }
																				
                                   ?>

                                   <div class="col-xs-6 col-sm-6 "><!---AQUEI VAN LOS DATOS FALTANTES-->
								   
								       <p style="font-size:15px;"><b><?php echo $nb_plan_instituto;  ?></b></p>
								   
                                       <p style="font-size:15px;"><b><?php echo $fg_plan;  ?></b> &nbsp;<?php echo $presenta_tooltip_cambio_plan ?></p>
                                       <p style="font-size:15px;"><b><?php echo  $no_total_licencias;  ?></b>
                                           
                                           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                           <a href="#licenses" data-toggle="tab" name="add_license" id="add_license" class="btn btn-primary btn-xs <?php echo $btn_hidden;?>" onclick="AddLicenses()"><?php echo ObtenEtiqueta(985);?></a> </p>
                                       <p style="font-size:15px;"><b><?php echo $no_licencias_usadas;  ?></b></p>
                                       <p style="font-size:15px;"><b><?php echo $no_licencias_disponibles;  ?></b></p>
                                       <p style="font-size:15px;"><b><?php echo $mn_espacio_disco;  ?></b></p>
                                       <p style="font-size:15px;"><b><?php echo $mn_espacio_usado;  ?></b></p>
                                       <p style="font-size:15px;"><b><?php echo $mn_tipo_moneda;  ?></b></p>
                                       <p style="font-size:15px;"><b><?php echo $mn_costo  ?> </b><?php echo $mn_descuento; ?></p>
                                       <p style="font-size:15px;"><b><?php echo $mn_costo_otro  ?></b></p>
                                       <p style="font-size:15px;"><b><?php echo $fe_terminacion_plan;  ?></b></p>
                                       <p style="font-size:15px;"><b><?php echo $fe_proximo_pago;  ?></b></p>

                                           <?php 
                                            if($fg_tipo_plan){
                                           ?>
                                                <p style="font-size:15px;"><b><?php echo $ds_option_renovacion." ".$fe_reduccion_contrato;  ?></b>


                                            
            
                                                <a href="#renewal"  data-toggle="tab" name="btn_renew" id="btn_renew" class="btn btn-primary btn-xs <?php echo $btn_hidden;?>" onclick="RenewOptions()" ><?php echo ObtenEtiqueta(1500);?></a>
                                                    

                                           <?php
                                            }
                                           ?>

                                       </p>
                                   </div>
                                         <input type="hidden" name="fl_current_plan" id="fl_current_plan" value="<?php  echo $fl_current_plan ?>" />
                                         <input type="hidden" name="no_total_licencias" id="no_total_licencias" value="<?php  echo $no_total_licencias ?>" />
                                    <div class="col-xs-2 col-sm-2 text-right" id="div_cancelar_plan">

                                    </div>



                               </div>
  
  <script>


      function PresentaTablaPrecios() {

          var fl_accion = 1;

          if ($('#optionsRadios3').is(':checked')) {
              var fg_option = 1;
          } else {
              var fg_option = 2;
          }

          var no_usuario_adicional = document.getElementById('no_usuario_adicional').value;
          var no_total_licencias = document.getElementById('no_total_licencias').value;

          //alert(no_total_licencias);
          $.ajax({
              type: 'POST',
              url: 'site/presenta_lista_precios.php',
              data: 'fl_accion=' + fl_accion +
                    '&no_usuario_adicional=' + no_usuario_adicional +
                    '&no_total_licencias=' + no_total_licencias +
                    '&fg_option=' + fg_option,

              async: true,
              success: function (html) {
                  $('#presenta_lista_precios').html(html);
              }
          });

      }




  </script>
  
  <?php
//funcion que hace el llamado por ajax y permite visualixzar la tabla actual de precios .
echo"
<script>
PresentaTablaPrecios();


</script>





";
?>
  
  
    





