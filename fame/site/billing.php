<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");
    
    # Include the Stripe library
    require_once('../lib/Stripe/Stripe/init.php');

    # Variable initialization
    $fg_tiene_plan=NULL;
    $chequeado_mes=NULL;
    $opt_btn=NULL;
    $val_btn=NULL;
    $p_ancho=NULL;
    $b=NULL;
    $tot_reg=NULL;
    $disbaled_check_options=NULL;
    $btn_disabled_frezze=null;

    
    
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  
  #Recuperamos el isttituo
  $fl_instituto=ObtenInstituto($fl_usuario);
  $presentar_renew=RecibeParametroNumerico('t', True); 

  
  #Recuperamos el nombre del usuario:
  
  $Query="SELECT ds_nombres,ds_apaterno,fl_perfil_sp FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Query);
  $ds_nombre=$row[0];
  $ds_apaterno=$row[0];
  $fl_perfil_fame=$row['fl_perfil_sp'];
  $nb_user_actual=$ds_nombre." ".$ds_apaterno;
  
  $Query="SELECT fg_plan,fe_periodo_final,fg_pago_fallido,fg_pago_manual,fe_periodo_inicial,fg_estatus FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fg_plan_actual_instituto=$row['fg_plan'];
  $fe_periodo_expiracion_plan=$row['fe_periodo_final'];
  $fg_pago_fallido=$row['fg_pago_fallido'];
  $cl_metodo_pago=$row['fg_pago_manual'];
  $fe_periodo_inicial=$row['fe_periodo_inicial'];
  $fg_estatus=$row['fg_estatus'];
  
  if(!empty($fg_plan_actual_instituto)){
      $fg_tiene_plan="1";
  }

  #Si tiene un metodo pago quiere decir que sus pagos son via manuales(deposito,cheque etc)    
  if(!empty($cl_metodo_pago)){
  
      $disabled_tab="disabled";
	  $disbaled_check_options="hidden";
  
		 #Para saber si sigue activo/no.
		  #Obtenemos fecha actual :
		  $Query = "Select CURDATE() ";
		  $row = RecuperaValor($Query);
		  $fe_actual = str_texto($row[0]);
		  $fe_actual=strtotime('+0 day',strtotime($fe_actual));
		  $fe_actual= date('Y-m-d',$fe_actual);
		  
		  if($fe_periodo_expiracion_plan>=$fe_actual){
             $fg_vigente=1;  
		  }else{
			  
			  $fg_vigente=0;
		  }
		  
		  
		  #DAMOS FORMATO DIA,MES,AÑO
          $date=date_create($fe_periodo_inicial);
          $fe_periodo_inicial_=date_format($date,'F j, Y');
		  
          
          #DAMOS FORMATO DIA,MES,AÑO
          $date=date_create($fe_periodo_expiracion_plan);
          $fe_periodo_expiracion_plan=date_format($date,'F j, Y');
		  
		  $fe_renovav=$fe_periodo_inicial_." to ".$fe_periodo_expiracion_plan;
		  
      
	      #DAMOS FORMATO DE FECHA.
		  $fe_periodo_expiracion_plan=strtotime('+0 day',strtotime($fe_final_periodo));
		  $fe_periodo_expiracion_plan= date('Y-m-d',$fe_periodo_expiracion_plan);
		
		  $date = date_create($fe_periodo_expiracion_plan);
		  $fe_renovacion=date_format($date,'F j, Y');
	
		  
		  
		  
		  
		  
		if($fg_vigente){
			$classv="success";
			$background="#dbf1dc";
			}else{
			$classv="danger";
			$background="#f1dbe0";
			} 

		$etq=ObtenEtiqueta(2325);
		$etq = str_replace("#fe_renovation#", $fe_renovav, $etq);
																							
  
  
  
  
  }    
      
   



   
?>

    <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="font-awesome-4.6.3/font-awesome-4.6.3/css/font-awesome.min.css">

    <!-- MAIN CONTENT -->
    <div id="content">
<script>
    function PresentaPlanActual() {

        var opcion = 1;
        var no_total_licencias_actuales = document.getElementById('no_total_licencias').value;

        $.ajax({
            type: 'POST',
            url: 'site/presenta_plan_actual.php',
            data: 'opcion=' + opcion +
                  '&no_total_licencias_actuales=' + no_total_licencias_actuales ,

            async: true,
            success: function (html) {
                $('#current_plan').html(html);
            }
        });

       
    }
</script>

     <!-- widget content -->
            <div class="widget-body">
                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active" id="tab1">
                        <a href="#current_plan" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-bars"></i>&nbsp;<?php echo ObtenEtiqueta(984) ?></a>
                    </li>
                    <li id="tab2">
                        <a href="#licenses" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-user-plus" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(985) ?></a>
                    </li>

                     <li id="tab3">
                        <a href="#history" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-money"></i>&nbsp;<?php echo ObtenEtiqueta(986) ?></a>
                    </li>

                    <li id="tab5" class="hidden">
                        <a href="#pago" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-credit-card"></i>Payment</a>
                    </li>
					<li id="tab6" class="hidden" >
                        <a href="#renewal" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-refresh"></i>&nbsp; Renewal </a>
                    </li>
                    
                    <?php if( ($fl_perfil_fame==PFL_ADMINISTRADOR) && ( empty($cl_metodo_pago)) ){  ?>	

                    <li id="tab7" >
                        <a href="#update" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-info-circle"></i>&nbsp; <?php echo ObtenEtiqueta(2149); ?> </a>
                    </li>
                    <?php } ?>
					
					
                   
                </ul>



                <div id="myTabContent1" class="tab-content padding-10 "><!--class='no-border'--->
                    <div class="tab-pane fade in active" id="current_plan">
                        <br />
                        <!--------=======Aqui presnta info de que se recupera atraves de ajax en el archivo presenta_tabla_Actual.php=========----->
  
                    </div>

                        <script>
                            PresentaPlanActual();

                        </script>

                            <!---------========================Presenta Modal de cancelacion de cuenta================-------------------->

                            <?php 
                            $texto_modal=ObtenEtiqueta(1549);
                            
                            ?>
                             <input type="hidden" name="fg_tiene_plan" id="fg_tiene_plan" value="<?php echo $fg_tiene_plan; ?>" />
                            <input type="hidden" name="fl_instituto" value="<?php echo $fl_instituto ?>" id="fl_instituto"  />

                            <?php  
                    
                                $Query="SELECT no_total_licencias,fg_plan FROM k_current_plan WHERE fl_instituto =$fl_instituto ";
                                $row=RecuperaValor($Query);
                                $no_total_licencias=$row['no_total_licencias'];
                                $fg_plan=$row['fg_plan'];
                                if(empty($fg_plan)){#Quiere decir que no tiene plan y por lo tanto e toma en cuenta sus usuarios en modo Trial
                        
                                    // $no_total_licencias=26;
                                    $no_total_licencias=0;
                                    $fg_tiene_plan=0;
                                }else{
                                    $fg_tiene_plan=1;
                                }
                    
                            ?>






                            <!-----------=======================fin modal de cancelacion de cuenta--------============-------------------->





                            <style>
                            .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
                                /* border: 0px solid #ddd;*/
                            }
                            .table-bordered {
                                /*  border: 0px solid #ddd;
                            */}
                                .sinborder tr th {
    
                                border: 0px solid #ddd !important;
                                }
    
                                .table thead tr, .fc-border-separate thead tr {
                                    background-color: #fff;
                                    background-image: -moz-linear-gradient(top,#fff 0,#fff 100%);
                                }
                                h1, .h1, h2, .h2, h3, .h3 {
                                    margin-top: 1px !important;
                                }

                                .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
                                    padding: 3px;
                                    padding-top: 3px;
                                    padding-right: 3px;
                                    padding-bottom: 3px;
                                    padding-left: 3px;
                                }



    
                                /*efecto para texto que aparece en el archivo presenta_lista_precios.php */
                                .label {
                                    font-size: 90% !important;
                                }
                            .parpadea {
  
                                animation-name: parpadeo;
                                animation-duration: 2s;
                                animation-timing-function: linear;
                                animation-iteration-count: infinite;

                                -webkit-animation-name:parpadeo;
                                -webkit-animation-duration: 2s;
                                -webkit-animation-timing-function: linear;
                                -webkit-animation-iteration-count: infinite;
                            }

                            @-moz-keyframes parpadeo{  
                                0% { opacity: 1.0; }
                                50% { opacity: 0.0; }
                                100% { opacity: 1.0; }
                            }

                            @-webkit-keyframes parpadeo {  
                                0% { opacity: 1.0; }
                                50% { opacity: 0.0; }
                                100% { opacity: 1.0; }
                            }

                            @keyframes parpadeo {  
                                0% { opacity: 1.0; }
                                50% { opacity: 0.0; }
                                100% { opacity: 1.0; }
                            }


                            </style>


					<div class="tab-pane fade" id="licenses">
						  <div class="row">
                              <div class="col-xs-12 col-sm-12">
                                  <table class="table table-bordered sinborder" width="90%" style="border:none;"  >
                                        <thead>
                                            <tr >
                                            <th class="text-center" colspan="3" style="font-size:16px;background-color:#fff;"><h1 style="font-weight: 200;color:#595656;"><?php echo ObtenEtiqueta(1512);  ?></h1> </th>
                                            
                                            </tr>
                                            <tr >
                                            <th class="text-center" width="30%" style="font-size:14px;background-color:#fff;"><?php echo ObtenEtiqueta(1501);  ?> <br>
                                               <span style="font-size:12px;color: #A4A1A1;"> <?php echo ObtenEtiqueta(1504);  ?> </span>

                                            </th>
                                            <th class="text-center" width="30%" style="font-size:14px;background-color:#fff;"><?php echo ObtenEtiqueta(1763);  ?><br >
                                                <span style="font-size:12px;color: #A4A1A1;"> <?php echo ObtenEtiqueta(1764);  ?> </span>
                                            </th>
                                            <th class="text-center" width="30%" style="font-size:14px;background-color:#fff;"  ><a href="javascript:void();" style="font-size:14px;color:#000;text-decoration:none!imporatnt;" > <?php echo ObtenEtiqueta(1503);  ?></a>&nbsp;&nbsp;<a href="javascript:void(0);"><i class="fa fa-info-circle" aria-hidden="true" data-placement="top" data-original-title="<?php echo ObtenEtiqueta(1588); ?>" rel="tooltip"></i><a><br >
                                                <span style="font-size:12px;color: #A4A1A1;" style="text-decoration:none!imporatnt;" > <?php echo ObtenEtiqueta(1506);  ?> </span>
                                            </th>
                                           
                                        </tr>
                                        </thead>

<script>

   
	    $(document).ready(function () {

	        $('#optionsRadios3').change(function () {
	           
	            PresentaTablaPrecios();//habilta el boton solo si se seleeciona la primer opcion

	            ActualizaMontoPagar();
	        });

	        $('#optionsRadios4').change(function () {

	            PresentaTablaPrecios();//habilta el boton solo si se seleeciona la primer opcion
	            ActualizaMontoPagar();
	        });

	    });
</script>


                                        <tbody id="presenta_lista_precios">

                                            <!-----lo que se encuentra en presenta lista_precios.php----->
                                        </tbody>
										

                                    </table>
									
								<?php 
								if(!empty($cl_metodo_pago)){
									
									
									
								?>	
								<div class="row"><div class="col-md-12 text-center">
									<p class="alert alert-<?php echo $classv?>"><i><?php echo $etq; ?></i></p>
								</div></div>	
								<?php	
								}	

								?>
                                
<!---dESABILITAMOS EL ENTER---->                                  
<script>
function disableEnterKey(e){
var key; 
if(window.event){
key = window.event.keyCode; //IE
}else{
key = e.which; //firefox 
}
if(key==13){
return false;
}else{
return true;
}
}
</script>

        <input type="hidden" name="no_total_licencias" id="no_total_licencias" value="<?php  echo $no_total_licencias ?>" />
        <input type="hidden" name="mn_total_pagar" id="mn_total_pagar" value="0" />                             
<?php  
      
   if(empty($cl_metodo_pago)){

?>                                     
                                <form class="smart-form" action="<?php echo SP_HOME."fame/lib/Stripe/charge.php" ?>"  onKeyPress="return disableEnterKey(event)" >
 
                                         
     
                                                                                                                             <!--ededed-->
                                        <table class="table table-bordered"  width="100%" style="background:#f6f6f6;border: 1px solid #ededed;" >            
                                            <tbody>
                                                <tr>
                                                    <td colspan="3"  style="font-size:14px; color: #333;border:none;" >
                                                                         <style>
		                                                                        /*para alinear check_radio centro*/
		                                                                        .smart-form .inline-group {
                                                                                     margin: 0 -30px -4px 115px !important;
                                                                                    margin-top: 0px !important;
                                                                                    margin-right: -30px !important;
                                                                                    margin-bottom: -4px !important;
                                                                                    margin-left: 115px !important;
                                                                                }
                                                                                /*es para alinear el cechk radio*/
                                                                                .smart-form .radio + .radio, .smart-form .checkbox + .checkbox {
                                                                                    margin-top: 10px !important;
                                                                                }
                                                                                table {
                                                                                     border-color: #DEDDDD !important;
	                                                                            }

                                                                            </style>
                                                    
                                                                <!---Presenta chekc box--->

                                                                <div ="info_plan">		
																						
																						<table  width="100%">
																						<tbody >
																							<tr>
																							<td width="15%" style="border:none;" >
																							
																							
																							
																							
																							</td>
																							<td width="55%" align="center" style="border:none;" >
																							
                                                                                                <?php
                                                                                                   if($fg_plan){

                                                                                                       if($fg_plan_actual_instituto=='M'){
                                                                                                         $chequeado_mes="checked='checked' ";
                                                                                                           
                                                                                                       }else if($fg_plan_actual_instituto='A'){
                                                                                                       
                                                                                                         $chequeado_anio="checked='checked' ";
                                                                                                         //$add_class_disable="disabled";
                                                                                                       }
                                                                                                        $add_class_disable="disabled";

                                                                                                   
                                                                                                   }
                                                                                                
                                                                                                   #vERIFICAMOS SI EXISTE UN CRON PARA CAMBIO DE PLAN que sea 3 o 2., Esto quiere decir que ya altero su plan actual, entonces solo podra cancelar el plan, y se habailitar una vez que se hayan aplicado esos cambios.
                                                                                                   $Query="SELECT COUNT(*),fg_cambio_plan,mn_cantidad_licencias,fg_motivo_pago FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto AND fg_motivo_pago IN(2,3)AND fg_cambio_plan IS NOT NULL  ";															  $row=RecuperaValor($Query);
                                                                                                   $existe_cambio_plan=$row[0];
                                                                                                   $fg_cambio_plan=$row[1];
                                                                                                   $mn_cantidad_licencias=$row[2];
                                                                                                  
																								
                                                                                                   if($existe_cambio_plan)
                                                                                                   $renwOpt="";
                                                                                                   else
                                                                                                   $renwOpt=2;
                                                                                                   
                                                                                                if($fg_tiene_plan){#Presenta mensaje de que no puede elegir el otro checkbox a menos que le de en click boton azul change.
																								      if($fg_plan_actual_instituto=='M'){
                                                                                                          
																								          $tooltip_infoA="rel='popover' data-html='true' data-content='<div class=\"row\"> <div class=\"col-md-12 text-left\"> <a href=\"#renewal\"  data-toggle=\"tab\" name=\"btn_renew\" id=\"btn_renew\" class=\"btn btn-primary btn-xs\" onclick=\"RenewOptions$renwOpt()\" >".ObtenEtiqueta(1500)."</a></div></div> ' ";
																										  $tooltip_infoM=" ";
																									   }else{
                                                                                                           $tooltip_infoM="rel='popover' data-html='true' data-content='<div class=\"row\"> <div class=\"col-md-12 text-left\"><a href=\"#renewal\"  data-toggle=\"tab\" name=\"btn_renew\" id=\"btn_renew\" class=\"btn btn-primary btn-xs\" onclick=\"RenewOptions$renwOpt()\" >".ObtenEtiqueta(1500)."</a></div></div> ' ";
																										  $tooltip_infoA=" ";
																									   
																									   }
																									   
																								}else{
																							     $tooltip_info="";
                                                                                                }
                                                                                                
                                                                                                
                                                                                                
                                                                                                
                                                                                                
                                                                                                
                                                                                                
																								?>

																								<div class="inline-group">
																									    <label class="radio" style="font-size:13px;line-height: 16px;" aria-hidden="true" data-placement="top"  data-original-title="<?php echo ObtenEtiqueta(1703) ?>"  <?php echo $tooltip_infoM; ?>>
																										<input type="radio" name="radio-inline" id="optionsRadios3" <?php echo $chequeado_mes; ?>  <?php echo $add_class_disable; ?>  />
																										<i></i>    <?php echo str_uso_normal(ObtenEtiqueta(1502));?>  </label>
																									
																									   <label class="radio <?php echo $add_class_disable; ?>" style="font-size:13px;line-height: 16px;"   aria-hidden="true" data-placement="top" data-original-title="<?php echo ObtenEtiqueta(1703) ?>" <?php echo $tooltip_infoA; ?> >
																										<input type="radio" name="radio-inline" id="optionsRadios4" <?php echo $chequeado_anio; ?>  <?php echo $add_class_disable; ?>/>
																										<i></i> <?php echo str_uso_normal(ObtenEtiqueta(1507)); ?> </label>   
                                                                                                       
																								</div>
																							
																										<?php 
																										if($fg_plan_actual_instituto=='M'){
																										   $etq_mostrar='muestra_etiqueta2'; 
																										}else{
																										   $etq_mostrar='muestra_etiqueta';
																										}
																										
																										
																										echo"
																										<script>
																											$( document ).ready(function() {
																											    $('#$etq_mostrar').removeClass('hidden');
																											});
																										</script>
																										";
																										?>
																							
																							
																										<script>
														
																												$('#optionsRadios3').click(function () {
							
																													$('#continuar').removeClass('disabled');
																													$('#muestra_etiqueta2').removeClass('hidden');
																													$('#muestra_etiqueta').addClass('hidden');
																													
																													
																												});
																												
																												$('#optionsRadios4').click(function () {
																												
																													$('#continuar').removeClass('disabled');
																													$('#muestra_etiqueta').removeClass('hidden');
																													$('#muestra_etiqueta2').addClass('hidden');
																												});

																										</script>

																										
																							</td>

																							<td width="20%">&nbsp;</td>
																							</tr>
																						</tbody>
																						</table>
				                                                         </div>
                                                                        <!---Finaliza la presentacion checkbox--->


                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td colspan="2" style="font-size:14px;color: #333;border:none;">
                                                        <table  width="100%" >
                                                            <tbody>
                                                                <tr>
                                                                <td class="text-right" width="25%" style="font-size:14px; color: #333;border:none;" id="user_actual">
                                                                <!------aqui presenta los usuarios actuales en el archivo actualiza_current_users.php-------->
                                                    
                                                                   <?php echo $no_total_licencias ?> 
                                                                </td>

                                                                <td class="text-left" style="font-size:15px;color: #333;border:none;padding-left:10px;"><?php echo ObtenEtiqueta(1508); ?></td>

                                                                 

                                                                </tr>

                                                                <tr>
                                                                        <td class="text-right" width="25%" style="font-size:14px;color: #333;border:none;">
																					<table width="100%">
                                                                                        <tr>
                                                                                            <td width="70%" style="font-size:14px;color: #333;border:none;">&nbsp;</td>
                                                                                            <td width="20%" style="font-size:14px;color: #333;border:none;">
                                                                                                <!--------==============================================------>
                                                                                                <!--------==============================================------>
                                                                        
                                                                                                 <!---estos estilos son para el spinner --->
                                                                                                                <style>

                                                                                                                        .smart-form .ui-widget-content .ui-spinner-input {
                                                                                                                            height: 20px !important;
                                                                                                                        }
                                                                                                                        .form-group {
                                                                                                                            margin-bottom: 1px !important;
                                                                                                                            }
                                                                                                                          .ui-spinner-down, .ui-spinner-up {
                                                                                                                            background: #0092cd !important;
                                                                                                                        }
                                                                                                                        .ui-spinner-down {
                                                                                                                            background: #0092cd !important;
                                                                                                                        }
                                                                                                                        .ui-spinner-down:active, .ui-spinner-down:focus, .ui-spinner-down:hover {
                                                                                                                            background: #0092cd !important;
                                                                                                                        }
                                                                                                                 </style>


                                                                                                                <div class='form-group' >
																
																				                                    <input class='form-control spinner-left'  id='no_usuario_adicional' name='no_usuario_adicional' value='1' type='text'>
																			                                    </div>



                                                                                                </td>



                                                                                            </tr>
                                                                                    </table>

                                                                            </td>

                                                                            <td class="text-left" style="font-size:14px;color: #333;border:none;padding-left:10px;"><?php echo ObtenEtiqueta(1509); ?>
                                                                                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;  
                                                                                <?php 
                                                                            /*if($fg_tiene_plan){
                                                                            ?>
                                                                               <a href="#renewal"  data-toggle="tab" name="btn_renew" id="btn_renew" class="btn btn-primary btn-xs" onclick="RenewOptions()" ><?php echo ObtenEtiqueta(1500);?></a> 
                                                                            <?php    
                                                                            }*/
                                                                            ?>

                                                                            </td>

                                                                            




                                                                     </tr>

                                                                <tr>
                                                                        <td class="text-right" style="font-size:14px;color: #333;border:none;" id="actualiza_licencias" name="actualiza_licencias" >


                                                                        </td>

                                                                        <td class="text-left" style="font-size:14px;color: #333;border:none;padding-left:10px;"><?php echo ObtenEtiqueta(1510); ?></td>
                                                                    </tr>

                                                                        <tr>
                                                                        <td class="text-right" style="font-size:14px;color: #333;border:none;" id="muestra_precio">  </td>
                                                                        <td class="text-left hidden" style="font-size:14px;color: #333;border:none;padding-left:10px;" id="muestra_etiqueta"><?php echo html_entity_decode(ObtenEtiqueta(1511)); ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        </td>
																		<td class="text-left hidden" style="font-size:14px;color: #333;border:none;padding-left:10px;" id="muestra_etiqueta2"><?php echo ObtenEtiqueta(1709); ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        </td>
																		
																		
                                                                    </tr>














                                                            </tbody>

                                                        </table>
                                                        
                                                    </td>

                                                    

                                                    <td style="font-size:14px;color: #333;border:none;">
                                                        &nbsp;
                                                    </td>

                                                </tr>

                                                <tr style="font-size:14px;color: #333;border:none;"><td style="border:none;"><small style='color:#999; font-size:12px !important;'>*<?php echo ObtenEtiqueta(1760); ?></small> <br /><small style='color:#999; font-size:12px !important;'>*<?php echo ObtenEtiqueta(1761);  ?></small></td></tr>
                                            </tbody>
                                        </table>
                                    <!---===end table add licecias ===--->

									<?php
									
									if($fg_tiene_plan)
									   $disabled_btn="";
									else
									   $disabled_btn="disabled";

									
									
									?>

                                     <p class="text-center"  >  <a class="btn btn-primary <?php echo $disabled_btn ?>"  data-toggle="tab" id="continuar" href="#pago" onclick="RealizarPago()" style="padding: 6px 12px;border-radius: 10px;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(962); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>                  



<?php  }else{
           echo"<input type='hidden' id='no_usuario_adicional' value='0' >";
           
   
   } ?>







                                                                      
                              </div>


						  </div>
					</div>

                    <!----=============Aqui se presentara el listado de los pagos===========================---->
                   


                    <div class="tab-pane fade" id="history">
                      
                       <div class="row" style="padding:5px;">
    
    <?php      
      SectionIni();
        # Valores para el boton de actions
        //$opt_btn = array('Add Student', 'Import Student', 'Add Teacher', 'Import Teacher', 'Activate', 'Desactive', 'Delete');
        //$val_btn = array(ADD_STD,IMP_STD,ADD_MAE,IMP_MAE,ACTIVE,DESACTIVE,DELETE);
      ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "gabriel", "fa-table", " ", true, true, false, false, false, "Actions", "default", $opt_btn, $val_btn, $b);
          # Muestra Inicio de la tabla
        $titulos = array("".ObtenEtiqueta(1548)."", "".ObtenEtiqueta(1543)."", "".ObtenEtiqueta(1544)."" );
        
        echo"    
     <style>
                      table.table-bordered.dataTable {
    border-collapse: collapse !important;
}
                    </style>
    ";    
        
          MuestraTablaIni2("tbl_users", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos);
          
          
          # Muestra Fin de la tabla
          MuestraTablaFin(false);
          # Campos para el total de registros
          CampoOculto('tot_reg', $tot_reg);
          # Muestra el modal para las acciones
          MuestraModal("Actions"); 
        ArticleFin();
      SectionFin();
    ?>
  </div>
                        


                    </div>



                    <div class="tab-pane fade" id="pago">


                        
                        <div class="row" >
                            <div class="col-md-3">

                            </div>
                            <div class="col-md-6 text-center" >
                                 <br />
                             <!----------Aquie va lo de la funcion de PresentaMetodoRealizarPago------------->
                                   
                             <!--------------------->      

                                   <!--  <p class="text-center"  >  <a class="btn btn-primary"  data-toggle="tab" id="pagar" href="#pagar" onclick="Pagar()" style="padding: 6px 12px;border-radius: 10px;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Process Payment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>    -->              

                               
                            </div>
                            <div class="col-md-3" name="refresch" id="refresch">

                            </div>

                        </div>

                        <div id="presenta_strippe" name="presenta_strippe">

                        </div>



                       
                    </div>
					<div class="tab-pane fade" id="renewal">
                          <div class="row">
                                 <div class="col-md-12">
                                     <div class="smart-form">
                                         <fieldset>
												<section>
													<!--<label class="label">Columned radios</label>-->
													<div class="row">
 														<div class="col-md-1">&nbsp;</div>
	                                                        <?php
															    #DAMOS FORMATO DE FECHA.
	                                                            $fe_periodo_expiracion_plan=strtotime('+1 day',strtotime($fe_periodo_expiracion_plan));
	                                                            $fe_periodo_expiracion_plan= date('Y-m-d',$fe_periodo_expiracion_plan);
	                                                            
	                                                            $date = date_create($fe_periodo_expiracion_plan);
	                                                            $fe_terminacion_plan=date_format($date,'F j, Y');

															  if($existe_cambio_plan){
															      $disable_cambio_plan="disabled";
																  $etq=ObtenEtiqueta(1725);
															      $etq_formato=ObtenEtiquetaPlanRenovacion('',$fe_terminacion_plan,$etq);
                                                                  //$etq_formato="fua";
																  $tooltip_info_chek="rel='popover' data-html='true' data-content='<style>.popover.top { left:06px!important; }</style> ".$etq_formato." ' "; 
																}else{
															     $disable_cambio_plan="";
																 $tooltip_info_chek="";
															    }
	                                                        /**
	                                                         * #fe_expiration_plan# 
	                                                         * #tipo_plan#
	                                                         * #no_licencias_actuales#
	                                                         */ 
															 
															/*********************
                                                             *Para Institutciones que ya vecnio su plan y quieren volver a FAME.
                                                             *****/
                                                            #Obtenemos fecha actual :
                                                            $Query = "Select CURDATE() ";
                                                            $row = RecuperaValor($Query);
                                                            $fe_actual = str_texto($row[0]);
                                                            $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                                                            $fe_actual= date('Y-m-d',$fe_actual);
                                                             
                                                             
															#Verificamos situacion actual del instituto.
                                                            $Querya="SELECT fe_periodo_inicial,fe_periodo_final,B.fg_tiene_plan   
                                                                     FROM k_current_plan A
                                                                     JOIN c_instituto B ON B.fl_instituto=A.fl_instituto 
                                                                     WHERE A.fl_instituto=$fl_instituto ";
                                                            $rowa=RecuperaValor($Querya);
                                                            $fe_periodo_ini=$rowa[0];
                                                            $fe_periodo_fin=$rowa[1];
                                                            $fg_ti_plan=$rowa[2];
                                                              
                                                            if(($fe_periodo_fin < $fe_actual)&&($fg_ti_plan==1)){
                                                                $date = date_create($fe_actual);
	                                                            $fe_terminacion_plan=date_format($date,'F j, Y');        
                                                            } 
															  
															 
															 
															 
	                                                            
	                                                            
	                                                            
	                                                            $etq=str_uso_normal(ObtenEtiqueta(1584));
	                                                            $radio_button1=ObtenEtiquetaPlanRenovacion($fl_instituto,$fe_terminacion_plan,$etq);
	                                                            $etq=str_uso_normal(ObtenEtiqueta(1585));
	                                                            $radio_button2=ObtenEtiquetaPlanRenovacion($fl_instituto,$fe_terminacion_plan,$etq);
	                                                            $etq=str_uso_normal(ObtenEtiqueta(1586));
	                                                            $radio_button3=ObtenEtiquetaPlanRenovacion($fl_instituto,$fe_terminacion_plan,$etq);
	
   
                                                                #Si eligio cancela cuenta , entonces cuando el admin revise estara chequeado cancel
                                                                $Query="SELECT fg_motivo_pago FROM k_cron_plan_fame WHERE fl_instituto=$fl_instituto ";
                                                                $row=RecuperaValor($Query);
                                                                $fg_motivo_pago=$row[0];
                                                                
                                                                if($fg_motivo_pago==1){
                                                                    $chequed_opt_renew1=" checked='checked' ";
                                                                    $chequed_opt_renew4="";
                                                                }
                                                                
                                                                if($fg_motivo_pago==4){
                                                                    
                                                                    $chequed_opt_renew4="checked='checked' ";
                                                                    $chequed_opt_renew1="";
                                                                }

                                                                #Si el plan esta congelado Frezze todo aparecera que esta desabilitado hasta que descongeleen el plan.
                                                                if($fg_estatus=='F'){
                                                                    $chequed_opt_renew1="";
                                                                    $chequed_opt_renew4="";
                                                                    $btn_disabled_frezze='disabled';
                                                                    $chequed_opt_renew6="checked='checked' ";
                                                                }
                                                                
	                                                        ?>

                                       

														<div class="col col-10">
															<label class="radio <?php echo $disbaled_check_options; ?>"  >
																<input name="radio" <?php echo $chequed_opt_renew1; ?>  id="opt1" type="radio" value="1"  <?php echo $btn_disabled_frezze;?> >
																<i></i><?php echo $radio_button1; ?>

															</label>
                                                            
															<label class="radio <?php echo $disbaled_check_options; ?>"   aria-hidden="true" data-placement="top"  data-original-title=""   <?php echo $tooltip_info_chek; ?> >
																<input name="radio" type="radio"   id="opt2" value="2" <?php echo $disable_cambio_plan; ?>  <?php echo $btn_disabled_frezze;?> />
																<i></i><?php echo $radio_button2; ?>
															</label>
															<br><br>
															<?php 
															
															if($fg_plan_actual_instituto=="A"){
																$tooltip_info="<a href=''><i class='fa fa-info-circle' aria-hidden='true' data-placement='top' data-original-title='".ObtenEtiqueta(1588)."' rel='tooltip'></i></a>";
															    $disabled_check_renew="";
                                                                
															}
                                                            
             												$cadenas= str_uso_normal(ObtenEtiqueta(1587)); 
															$etq_format_cancel = str_replace("#fame_fe_expiration_plan#", $fe_terminacion_plan,$cadenas);
															                                               
															
															?>
															
															<label class="radio <?php echo $disbaled_check_options; ?>" aria-hidden="true" data-placement="top"  data-original-title=""   <?php echo $tooltip_info_chek; ?>  >
																<input name="radio" type="radio" id="opt3" value="3" <?php echo $check_disable2; echo $disable_cambio_plan; ?>  <?php echo $btn_disabled_frezze;?> />
																<i></i><?php echo $radio_button3; ?>

															</label>

                                                            <?php if($fg_estatus=='F'){ ?>

                                                            <label class="radio" aria-hidden="true" data-placement="top"  data-original-title="">
																<input name="radio" <?php echo $chequed_opt_renew6; ?> type="radio" id="opt6" value="6" />
																<i></i><?php echo str_uso_normal(ObtenEtiqueta(2647));?>
															</label>

                                                            <?php }else{ ?>

                                                            <label class="radio" aria-hidden="true" data-placement="top"  data-original-title="">
																<input name="radio" type="radio" id="opt5" value="5" />
																<i></i><?php echo str_uso_normal(ObtenEtiqueta(2644));?>
															</label>

                                                            <?php } ?>
                                                            <label class="radio"  >
																<input name="radio" <?php echo $chequed_opt_renew4; ?> type="radio" id="opt4" value="4"  />
																<i></i><?php echo $etq_format_cancel; ?>


                                                            </label>
                                                            <?php 
                                                            //if($existe_cambio_plan)
                                                            //echo "<br/><font color='red'><i class='fa fa-info' aria-hidden='true'></i> ".ObtenEtiqueta(1725)."</font>";
                                                            ?>

                                                            <br /><br />

                                                            

                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button  class="btn btn-default" href="#current_plan" data-toggle="tab"  onclick="Cancel()" style="border-radius: 10px;"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(14); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>    

                                                                     <a  class="btn btn-primary" name="aply" id="aply" style="border-radius: 10px;"  onclick="SelectOptionRenew()">&nbsp;&nbsp;<?php echo ObtenEtiqueta(1598); ?>&nbsp;&nbsp;</a>

                                                                </div>

                                                            </div>
                                                               

                                                            
                                                            


														</div>

                                                        <div class="col-md-1" id="presenta_opc_renovacion">&nbsp;</div>
														
														
													</div>
												</section>
				
												
											</fieldset>

                                         


                                     </div>
                                     
                                 </div>

                         </div>




                     </div>
					 
	</form>	
                    				 
			<?php if( ($fl_perfil_fame==PFL_ADMINISTRADOR) &&  (empty($cl_metodo_pago))){  
                      
					  
                      function Customer($customer)
                      {
                          try {
                              return $id= \Stripe\Customer::retrieve($customer);
                          }
                          catch (Exception $e) {
                              return 0;
                          }
                      }	  
					  
					  
                      #Recuperamos datos de la cuenta de Billing
                      $Query="SELECT fl_current_plan,id_cliente_stripe,no_tarjeta,ds_tipo,fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta  
                              FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
                      $row=RecuperaValor($Query);
                      $fl_current_plan=$row['fl_current_plan'];
                      $id_cliente_stripe=str_texto($row['id_cliente_stripe']);
                      $no_tarjeta=$row['no_tarjeta'];
                      $fe_mes_expiracion_tarjeta=$row['fe_mes_expiracion_tarjeta'];
                      $fe_anio_expiracion_tarjeta=$row['fe_anio_expiracion_tarjeta'];
                      $tipo_tarjeta=str_texto($row['ds_tipo']);
                     
                      
                      if(!empty($fl_current_plan)){
                      
                      
                      if(empty($no_tarjeta)){
                      
                          # Variables Stripe
                          $secret_key = ObtenConfiguracion(112);
                          
                          #Recuperamos datos de la tarjeta que tiene actulmente el cliente.
                          \Stripe\Stripe::setApiKey($secret_key);
                         
						
						  if($fl_instituto<>4){
						 
                            #verifica si existe customer

                              $customer=Customer($id_cliente_stripe);

                            if(!empty($customer)){
                              ##Actualizamos la tarjeta con la pagara.      
                              $cu = \Stripe\Customer::retrieve($id_cliente_stripe); // stored in your application
                              $dta=$cu->sources;
                              $id_tarjeta=$dta['data']['0']->id;
                              $no_tarjeta=$dta['data']['0']->last4;
                              $fe_mes_expiracion_tarjeta=$dta['data']['0']->exp_month;
                              $fe_anio_expiracion_tarjeta=$dta['data']['0']->exp_year;
                              $tipo_tarjeta=$dta['data']['0']->brand;

                          
                                          
                              #Actualizamos fecha de vencimeinto de la tarjeta en su plan de pago.
                              $Query="UPDATE k_current_plan SET fe_mes_expiracion_tarjeta='$fe_mes_expiracion_tarjeta',no_tarjeta=$no_tarjeta ,ds_tipo='$tipo_tarjeta', fe_anio_expiracion_tarjeta='$fe_anio_expiracion_tarjeta'  
                                      WHERE fl_current_plan=$fl_current_plan AND fl_instituto=$fl_instituto ";
                              EjecutaQuery($Query);
                          }                          
                        }
                      }
                      
                     
                      $no_=strlen($fe_mes_expiracion_tarjeta);
                      if($no_==1)
                          $fe_mes_expiracion_tarjeta="0".$fe_mes_expiracion_tarjeta;
                      
                     
                      }
                       
                      
                      
                    
                      
            ?>	
                    


                    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_SELF_CSS."/stripe.css";?> ">

                    			
					    <div class="tab-pane fade" id="update">


                            
                            <div class="row" id="updcard1">
                                <br />
                                <div class="col-md-2">&nbsp;</div>
                                <div class="col-md-8">
                                            
                                            <div class="well" style="background-color: #fdfdfd;border: 1px solid #ececec; font-size:14px;">
                                                <span class=' text-left' ><b><?php echo ObtenEtiqueta(2146);?></b></span><br /><br />
                                                <div class="row">
                                                    <!--<div class="col-md-2 text-center">
                                                         <i class="fa fa-credit-card-alt" aria-hidden="true" style="font-size: 30px;margin-top: 6px;"></i>
                                                    </div>-->
                                                    <div class="col-xs-12 col-sm-12	col-md-6 text-left">  
                                                        
                                                             <i class="fa fa-credit-card-alt" aria-hidden="true" style="font-size: 30px;margin-top: 6px;padding-right:5px; float:left;"></i>
                                                                                               
                                                            <span class=''><b><?php echo $tipo_tarjeta." ".ObtenEtiqueta(2147);?></b></span> <?php echo $no_tarjeta;?><br />
                                                            <span class=''><b><?php echo ObtenEtiqueta(2148);?></b></span> <?php echo $fe_mes_expiracion_tarjeta."/".$fe_anio_expiracion_tarjeta; ?>
                                                            
                                                           
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-6 text-center"><a class="btn btn-default" OnClick="MuestraUpdateCar();"  style="margin-top:8px;"><?php echo ObtenEtiqueta(2143);?></a></div>
                                                </div>
                                            </div> 

                                            
                                            
                                </div>
                                <div class="col-md-2">&nbsp;</div>



                            </div>






                              <div class="row padding-10 hidden" id="updcard2" >

                                   <div class="col-md-12 text-center">  <img src='<?php echo PATH_SELF_IMG."/creditCards_small.jpg";?> ' class='superbox-img' style='width:250px;' /><br /><br /></div>



                                    <div class="col-md-3">&nbsp;</div>

                                     <div class="col-md-6">

                                        

                                         <div class="row ">

                                             
                                              <div class='col-md-12 text-center'>
  
                                         
                                                    <form  id="update_card">
                                                        <!---<div class="form-row">
                                                            <label for="card-element" class='field' >
                                                              Credit or debit card
                                                            </label>
                                                            <input name='cardholder-name'/>

                                                        </div>--->
                                                        
                                                        <div class="group" style="padding-top: 14px;">
                                                            <label style='font-size: 14px;line-height: 13px;' >
                                                                <span style='margin-top:4px;'><?php echo ObtenEtiqueta(1707);?>:</span>
                                                                <input name='cardholder-name' style="height:21px;" class="field" placeholder='Jane Doe' />
                                                            </label>

                                                        </div>

                                                        <div class="group" style="padding-top: 14px;" >
                                                             <label style='font-size: 14px;line-height: 13px;' >
                                                                <span >
                                                                   <?php echo ObtenEtiqueta(1708);?>:
                                                                </span>
                                                           
                                                                <div id="card-element" class="field"></div>
                                                            </label>
                                                        </div>
                                                        <!-- Used to display form errors. -->
                                                            <div id="card-errors" role="alert"></div>

                                                        <input type="hidden" name="ds_customer" value="<?php echo $id_customer; ?>" />

                                                         <div class='col-md-12 text-center hidden' id='presenta_gif'><h5><img src='img/loading_stripe.gif' style='height:40px;'><strong><?php echo ObtenEtiqueta(2141); ?> </strong></h5> </div>
                                                        <div class='col-md-12 text-center'><strong class="hidden" id="msg_error_card"><?php echo ObtenEtiqueta(2145); ?></strong></div> 
                                                         
                                                        
                                                         <div class='col-md-12 text-center'> <button class="button2" id="btn_pago" style="border-radius: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(2142); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </button></div>
                                                         
                                                          <a class='btn btn-success btn-sm hidden' href='index.php#site/node.php?node=155' id='redirigir_billing'><i class='fa fa-upload'></i> redirige billing</a>
                                                         
              
                                                    </form>

                                                      <?php
                                                            $public_key=ObtenConfiguracion(111);
                                                      ?>

                                         <script>

                                             // Create a Stripe client.
                                             var stripe = Stripe('<?php echo $public_key;?>');

                                             // Create an instance of Elements.
                                             var elements = stripe.elements();

                                             // Custom styling can be passed to options when creating an Element.
                                             // (Note that this demo uses a wider set of styles than the guide below.)
                                             var style = {
                                                 iconColor: '#666EE8',
                                                 color: '#31325F',
                                                 lineHeight: '5px',
                                                 //fontWeight: 300,
                                                 fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                                                 fontSize: '15px',

                                                 '::placeholder': {
                                                     color: '#8898aa',
                                                 },
                                                 invalid: {
                                                     color: '#fa755a',
                                                     iconColor: '#fa755a'
                                                 }
                                             };
                                         
                                             // Create an instance of the card Element.
                                             var card = elements.create('card', {  });

                                             // Add an instance of the card Element into the `card-element` <div>.
                                             card.mount('#card-element');

                                             // Handle real-time validation errors from the card Element.
                                             card.addEventListener('change', function (event) {
                                                 var displayError = document.getElementById('card-errors');
                                                 if (event.error) {
                                                     displayError.textContent = event.error.message;
                                                 } else {
                                                     displayError.textContent = '';
                                                 }
                                             });


                                             // Handle form submission.
                                             var form = document.getElementById('update_card');
                                             //var frm_stripe = form.serialize();
                                             form.addEventListener('submit', function (event) {
                                                 event.preventDefault();

                                                 stripe.createToken(card).then(function (result) {

                                                     if (result.error) {
                                                         // Inform the user if there was an error.
                                                         var errorElement = document.getElementById('card-errors');
                                                         errorElement.textContent = result.error.message;
                                                     } else {


                                                         $('#presenta_gif').removeClass('hidden');
                                                         $('#btn_pago').addClass('hidden');

                                                         // Send the token to your server.
                                                         //stripeTokenHandler(result.token);
                                                         var forma = $('#update_card');
                                                         var token = result.token.id;
                                                         // Agregamos el token a la forma
                                                         forma.append($('<input type="hidden" name="stripeToken" name="stripeToken" />').val(token));
                                                         // datos de la forma
                                                         var frm_stripe = forma.serialize();

                                                         $.ajax({
                                                             type: 'POST',
                                                             url: 'site/update_card.php',
                                                             async: false,
                                                             data: frm_stripe,
                                                         }).done(function (result2) {
                                                             var stripe_result = JSON.parse(result2);
                                                             var error = stripe_result.error;
                                                             var exito = stripe_result.correct;
                                                             if (error == 0) {

                                                                 //Se genera correctamente el cambio.
                                                                 $.smallBox({
				                                                     title :"<?php echo ObtenEtiqueta(2144);?> ",  
                                                                     content: "<br/>&nbsp;&nbsp;",
                                                                     color: "#659265",
                                                                     timeout: 40000,
                                                                     icon: "fa fa-save"
                                                                     //number : "1"
                                                                 });
                                                                 document.getElementById('redirigir_billing').click();


                                                             } else {
                                                                 $('#presenta_gif').addClass('hidden');
                                                                 $('#msg_error_card').removeClass('hidden');
                                                                 $('#btn_pago').removeClass('hidden'); 
                                                                 //alert('fallo');

                                                             }


                                                         });





                                                     }
                                                 });
                                             });




                                         </script>


 


                                                </div>

                                        </div>              
                                         </div>

                                   <div class="col-md-3"></div>
                                  
                                    		
						      </div>
                        </div>

                    <script>
                        function MuestraUpdateCar() {

                            $('#updcard1').addClass('hidden');
                            $('#updcard2').removeClass('hidden');


                        }


                    </script>




                    <?php } ?>

                        
                           		
								
								
						
                        <!--------=======Aqui presnta info de que se recupera atraves de ajax en el archivo presenta_tabla_Actual.php=========----->
  
  
  
  
		

						

				
  
  
                    </div>				 
					 

































					



















               
                </div>
            </div>

    </div>
    <!-- END MAIN CONTENT -->




         <!-- JQUERY MASKED INPUT -->
        <!-------plugin del tags para colocar last atrejetas de credito-------->
		<script src="../fame/js/plugin/masked-input/jquery.maskedinput.min.js"></script>

<?php 
if($presentar_renew==1){

    echo"<script>  
  $(document).ready(function () {
       
        $('#tab1').removeClass('active');//se quita la callse active de la tab1
        $('#current_plan').removeClass('in active');//se quita la callse active de la tab1
        $('#tab6').removeClass('hidden');//desocultamos latab 6
        $('#tab6').addClass('active');//se agrega y apsa a tab 6
        $('#renewal').addClass('in active');//
  });
      </script>";



}

?>


 <script>
  //funcion que presenta el modal con las opciones a seguir para renovar plan.
     function SelectOptionRenew() {

         var fl_instituto = document.getElementById('fl_instituto').value;
         var fg_opcion = $('#opt1').is(':checked') ? 1 : 0;

         if (fg_opcion == 0) {
             var fg_opcion = $('#opt2').is(':checked') ? 2 : 0;
         }
         if (fg_opcion == 0) {
             var fg_opcion = $('#opt3').is(':checked') ? 3 : 0;
         }

         if (fg_opcion == 0) {
             var fg_opcion = $('#opt4').is(':checked') ? 4 : 0;
         }
         if (fg_opcion == 0) {
             var fg_opcion = $('#opt5').is(':checked') ? 5 : 0;
         }
         if (fg_opcion == 0) {
             var fg_opcion = $('#opt6').is(':checked') ? 6 : 0;
         }
        
        // alert(fg_opcion);

         //pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'site/presenta_opc_renovacion.php',
             data: 'fl_instituto=' + fl_instituto + 
                   '&fg_opcion =' + fg_opcion,

             async: true,
             success: function (html) {
                 $('#presenta_opc_renovacion').html(html);
             }
         });





     }

     function PresentaMetodoRealizarPago() {
       
         var no_usuario_adicional = document.getElementById('no_usuario_adicional').value;
         var no_total_licencias_actuales = document.getElementById('no_total_licencias').value;//no_licencias actuales.
         //var no_total_licencias = document.getElementById('no_total_licencias').value;

            


             $.ajax({
                 type: 'POST',
                 url: 'site/presenta_metodo_pago.php',
                 data: 'no_usuario_adicional=' + no_usuario_adicional +
                       '&no_total_licencias_actuales=' + no_total_licencias_actuales,

                 async: true,
                 success: function (html) {
                     $('#presenta_strippe').html(html);
                 }
             });


     }

 </script>


<script>
    function Cancel() {
        $("#tab6").addClass('hidden');//desocultamos latab 6

      

    }



   // PresentaMetodoRealizarPago();
</script>


<script>
    $(document).ready(function () {


      

     



        $('#add_license').change(function () {
            AddLicenses();
        });

        $('#continuar').change(function () {
            RealizarPago();
        });

       

        $('#no_usuario_adicional').spinner(

            );
        $('#spinner-decimal').spinner({
            step: 0.01,
            numberFormat: 'n'
        });


        $('.ui-spinner-button').click(function () {
            $(this).siblings('input').change(

                );
        });










        $('#no_usuario_adicional').spinner().change(function () {

            var valor_actual=$(this).spinner('value');

            if (valor_actual <= 0) {

                $('#no_usuario_adicional').val(1);
            }



           

             PresentaTablaPrecios();//funcion que muestra la tabla de tarifas indicando el rango actual 
            ActualizaTotalUsuarios();//funcion que actualiza el No. total de usuarios que esta abajo de spinner.

            ActualizaMontoPagar();
        });

		
		
		
		
    });










    function ActualizaMontoPagar() {

            var no_usuarios = document.getElementById('no_usuario_adicional').value;//indica el numero actual del spinner
            var fg_tiene_plan = document.getElementById('fg_tiene_plan').value;
            if ($('#optionsRadios3').is(':checked')) {
                var fg_plan = 1;
            } else {
                var fg_plan = 2;
            }
            var fg_tipo_funcion = 1;
            var no_usuarios_actuales = document.getElementById('no_total_licencias').value;//indica el numero actual del licencias que tiene el  instituto.

            $.ajax({
                type: 'POST',
                url: 'site/calcula_costo.php',
                data: 'no_usuarios=' + no_usuarios + 
                      '&no_usuarios_actuales=' + no_usuarios_actuales +
                      '&fg_tiene_plan=' + fg_tiene_plan +
                      '&fg_tipo_funcion =' + fg_tipo_funcion +
                      '&fg_plan=' + fg_plan,


                async: true,
                success: function (html) {
                    $('#muestra_precio').html(html);
                }
            });


    }





    function ActualizaTotalUsuarios() {
        //alert('entro');
        var no_usuario_adicional = document.getElementById('no_usuario_adicional').value;//no. actual que tiene el spinner contador de numeros

        
        var no_total_licencias_actuales = document.getElementById('no_total_licencias').value;//no_licencias actuales.
        //var mn_total_pagars = document.getElementById('mn_total_pagar').value;//no_licencias actuales.
       
       // alert(mn_total_pagar);

        $.ajax({
            type: 'POST',
            url: 'site/actualiza_no_licencias.php',
            data: 'no_usuario_adicional=' + no_usuario_adicional +
                  '&no_total_licencias_actuales=' + no_total_licencias_actuales,

            async: true,
            success: function (html) {
                $('#actualiza_licencias').html(html);
            }
        });

    }






    function AddLicenses() {//boton para pasar a tab 2.
        $("#tab1").removeClass('active');//se quita la callse active de la tab1
        $("#tab2").addClass('active');//se agrega y apsa a tab 2
    }

function RenewOptions() {
        $("#tab1").removeClass('active');//se quita la callse active de la tab1
        $("#tab6").removeClass('hidden');//desocultamos latab 6
        $("#tab6").addClass('active');//se agrega y apsa a tab 6

       
		
		
    }
	//misma funcion pero chequea /cambio de plan
	function RenewOptions2() {
	
        $("#tab1").removeClass('active');//se quita la callse active de la tab1
        $("#tab6").removeClass('hidden');//desocultamos latab 6
        $("#tab6").addClass('active');//se agrega y apsa a tab 6

        //se genera un click automatico en checkbox para camobo de plan.	
		document.getElementById('opt3').click();
		document.getElementById('aply').click();//clic automatico que se ejuta y sale modal
		
		//alert('fua');
		
		
	
    }
	
	
    function HabilitaBoton() {
        
        var no_usuario_adicional = document.getElementById('no_usuario_adicional').value;

        if (no_usuario_adicional == '') {
           // $('#continuar').attr('disabled', true);//se desahabilita el boton
          
        }else {

            $('#continuar').attr('disabled', false);//se habilita el boton
        }


    }

    function CancelarSuscription() {

        var fl_current_plan = document.getElementById('fl_current_plan').value;
       // alert('entro');


      
        $.ajax({
            type: 'POST',
            url: 'site/func_cancelar_suscription.php',
            data: 'fl_current_plan=' + fl_current_plan,
            async: true,
            success: function (html) {
                $('#div_cancelar_plan').html(html);



            }
        });

    }

    function ActualizaCurrentUser() {
        var fg_option = 1;

        $.ajax({
            type: 'POST',
            url: 'site/actualiza_current_users.php',
            data: 'fg_option=' + fg_option ,

            async: true,
            success: function (html) {
                $('#user_actual').html(html);

               $("#gabriel").removeAttr("style");
            }
        });


    }



    function RealizarPago() {

        $('#presenta_strippe').empty();

        var no_usuario_adicional = document.getElementById('no_usuario_adicional').value;
        var no_total_user = document.getElementById('no_total_licencias').value;
        var fg_tiene_plan = document.getElementById('fg_tiene_plan').value;


        if ($('#optionsRadios3').is(':checked')) {
            var fg_option = 1;
        } else {
            var fg_option = 2;
        }

       // alert(no_usuario_adicional);

        //genera el proceso de agregar licencias


        $.ajax({
            type: 'POST',
            url: 'site/func_agregar_licencias.php',
            data: 'no_usuario_adicional=' + no_usuario_adicional +
                  '&no_total_user=' + no_total_user +
                  '&fg_tiene_plan=' + fg_tiene_plan + //indica si es trial  o tien plan
                  '&fg_option=' + fg_option

                                        ,


            async: true,
            success: function (html) {
                $('#refresch').html(html);

                //$('#refresch').html().ajax.reload();
                
                $('#tbl_users').DataTable().ajax.reload()
                ActualizaTotalUsuarios();
                PresentaPlanActual();
                ActualizaCurrentUser();
				
				

               
            }
        });



                    $("#tab2").removeClass('active');// y apsa a tab 2
                    $("#tab5").addClass('active');//se agrega y apsa a tab 5
                    PresentaPlanActual();
                    //PresentaMetodoRealizarPago();//prsenta form strippe
    }



    ActualizaTotalUsuarios();
    ActualizaCurrentUser();
    ActualizaMontoPagar();

</script>








  <script type="text/javascript">

      pageSetUp();

      /** INICIO DE SCRIPT PARA DATATABLE **/
      var pagefunction = function () {
          // alert('ola');
          /* Formatting function for row details - modify as you need */
          function format(d) {
              // `d` is the original data object for the row
                return '<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed" width="100%">'+
            
                        '<tr>'+
               
                '<td>' + d.information + '</td>' +
            '</tr>' +
            
        '</table>';



             
          }

          // clears the variable if left blank
          var table = $('#tbl_users').on('processing.dt', function (e, settings, processing) {
              $('#datatable_fixed_column_processing').css('display', processing ? 'block' : 'inline');
              $("#vanas_loader").show();
              if (processing == false)
                  $("vanas_loader").hide();
          }).DataTable({
              "ajax": "Querys/billing.php",
              "bDestroy": true,
              "iDisplayLength": 25,
              "columns": [

                   {
                       "class": 'details-control',
                       "orderable": false,
                       "data": null,
                       "defaultContent": ''
                   },



             
                  { "data": "fe_pago" },
                  { "data": "mn_total" },
                 
                  { "data": "status","class":"text-center" },
                  { "data": "espacio","class":"text-center" },
                 
              ],
              "order": [],
              "fnDrawCallback": function (oSettings) {
                  runAllCharts();
                  /** Se tuiliza para el nombre de las imagenes **/
                  $("[rel=tooltip]").tooltip();
                  /** Total de registros **/
                  var oSettings = this.fnSettings();
                  var iTotalRecords = oSettings.fnRecordsTotal();
                  /** Es necesario si vamos a selelecionar muchos registros en la tabla **/
                  $("#tot_reg").val(iTotalRecords);
              }
          });

          // Add event listener for opening and closing details
          $('#tbl_users tbody').on('click', 'td.details-control', function () {
              var tr = $(this).closest('tr');
              var row = table.row(tr);

              if (row.child.isShown()) {
                  // This row is already open - close it
                  row.child.hide();
                  tr.removeClass('shown');
              }
              else {
                  // Open this row
                  row.child(format(row.data())).show();
                  tr.addClass('shown');
              }
          });

          /** INICIO DE SELECIONAR TODOS ***/
          $('#sel_todo').on('change', function () {
              var v_sel_todo = $(this).is(':checked'), i;
              var iTotalRecords = $('#tot_reg').val();
              for (i = 1; i <= iTotalRecords; i++) {
                  $("#ch_" + i).prop('checked', v_sel_todo);
              }
          })
          /** FIN DE SELECIONAR TODOS ***/

          /*** INICIO DE BUSQUEDA AVANZADA ***/
          /** OBTENEMOS EL VALOR DEL  TIPO DE USUARIO A BUSCAR **/
          // Typo de usuarios
          $("#fl_users").on('change', function () {
              var v = $(this).val();
              // if(v == 'ALL')
              // $('#fl_status').addClass('hidden');
              // else
              // $('#fl_status').removeClass('hidden');
              // busca en la columna del tupo         
              table.columns(8).search(v).draw();
              // alert(v);
          });
          /** OBTENEMOS EL VALOR DEL  TIPO DE STATUS A BUSCAR **/
          // Usuarios activos o inactivos
          $("#fl_status").on('change', function () {
              var v = $(this).val();
              // busca en la columna del tupo  
              table.columns(9).search(v).draw();
              // alert(v);        
          });
          /*** FIN DE BUSQUEDA AVANZADA ***/


      };

      /** Accion para actualizar la tabla**/
      /*$("#actions_ADD").click(function(){        
        table.ajax.reload();
        // $(this).removeClass("modal-open");
        // alert('ola');
      });*/

      /** FIN DE SCRIPT PARA DATATABLE **/
      // end pagefunction

      // load related plugins & run pagefunction
      /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/
      /** IMPORTANTES Y NECESARIAS EN DONDE SE UTILIZEN **/
      loadScript("../fame/js/plugin/datatables/jquery.dataTables.min.js", function () {
          loadScript("../fame/js/plugin/datatables/dataTables.colVis.min.js", function () {
              loadScript("../fame/js/plugin/datatables/dataTables.tableTools.min.js", function () {
                  loadScript("../fame/js/plugin/datatables/dataTables.bootstrap.min.js", function () {
                      loadScript("../fame/js/plugin/datatable-responsive/datatables.responsive.min.js", pagefunction)
                  });
              });
          });
      });
      /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/
<?php









function Forma_CampoInfo($p_prompt, $p_texto, $etq_align='right', $col_sm_etq='col-sm-4') {
    
    /*echo "
    <tr>
    <td align='right' valign='top' class='css_prompt'>";
    if(!empty($p_prompt))
    echo "$p_prompt:";
    else
    echo "&nbsp;";
    echo "</td>
    <td align='left' valign='top' class='css_etq_texto'>$p_texto</td>
    </tr>\n";*/
    echo "
  <div class='row form-group'>
    <label class='$col_sm_etq control-label text-align-$etq_align'>
      <strong>";
    if(!empty($p_prompt))
        echo "$p_prompt:";
    echo "
      </strong>
    </label>
    <div class='col $col_sm_etq'><label class='padding-top-10'>$p_texto</label></div>
  </div>";
}


# Funcion Tabla Encabezado
function MuestraTablaIni2($p_idtable="example", $p_class="", $p_width = "100%", $p_titulos = array(), $p_seleccionar = True){ 
    
    
    # Por default esta esta clase para las tablas
    if(empty($p_class))
        $p_class = "display projects-table table table-striped table-bordered table-hover";
    # Total de los registros
    $tot_registros = 0;
    echo "
    <table id='$p_idtable' class='$p_class' cellspacing='0' width='$p_width' >
      <thead>
        <tr>";
    if($p_seleccionar)
        echo "<th class='align-center text-align-center' style='width:0px'> </th>";

    # Muetsra los titulos de la tabla
    for($i=0;$i<=sizeof($p_titulos);$i++){        
        echo "<th style='width:".(!empty($p_ancho[$i])?$p_ancho[$i]:NULL)."'>".(!empty($p_titulos[$i])?$p_titulos[$i]:NULL)."</th>";
        $tot_registros++;
    }
    echo "
        </tr>
      </thead>
      <tbody>";    
}
?>
