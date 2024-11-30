<?php
  
  # Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $perfil_usuario = ObtenPerfilUsuario($fl_usuario);
  
  # Recibe Parametros
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp');
  $no_emails=RecibeParametroNumerico('no_email');
  
  #Verificamos el precio del programa.
  $Query="SELECT mn_precio,no_email_desbloquear,no_dias_pago FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
  $row=RecuperaValor($Query);
  $mn_precio=$row['mn_precio'];
  $no_email_desbloquear=$row['no_email_desbloquear'];
  $no_dias_pago=$row['no_dias_pago'];
  
  if(empty($mn_precio))
    $mn_precio=ObtenConfiguracion(123);
  
  #Descomonemos y sabes el decimal.
  if($mn_precio){
      $rmn = explode(".",$mn_precio);
      $mn_precio_entero=$rmn[0];
      $mn_precio_decimal=$rmn[1];
  
  }
  
  
  
  
  
  if(empty($no_dias_pago))
    $no_dias_pago=ObtenConfiguracion(128);  
  
  $price_desbloquear_stripe=ObtenConfiguracion(123);
  
 
  $no_invitaciones_enviadas=CuentaEmailEnviadosDesbloquearCurso($fl_usuario,$fl_programa_sp);
  if(!empty($no_invitaciones_enviadas)){
  $no_emails=$no_invitaciones_enviadas;
  $fg_esperando_confirmacion=1;
  }else{
  $no_emails=$no_email_desbloquear;
  $fg_esperando_confirmacion=0;  
  }
  if(empty($no_emails))
      $no_emails=ObtenConfiguracion(122);
  
  echo "
  <style>
  .btn.disabled, .btn[disabled], fieldset[disabled] .btn{
  opacity: 1;
  }
  .disabled {
    color: #fff;
  }
  .azul {
   background-color: #00A3E8;
   border-color: #00A3E8;
   color: #fff;
  }
  .azul:hover { background-color: #00A3E8 !important;
   border-color: #00A3E8 !important;
   color: #fff !important; }
  .btn-rosa2 {
    color: #fff;
    background-color: #FF00C4 !important;
    border-color: #FF00C4 !important;
	
  }

  .morado{
   background-color: #4060FF !important;
   border-color: #4060FF !important;
   color: #fff !important;
  
  
  }
  
  .mikepanel1{
  
    border:0px;
    background-color:#F3F5FB;
    border-radius:12px; 
   
    
  }
  .mikepanel2{
  
   border:0px;
   background-color:#1ecbf1;
   border-radius:12px;
   
  }
  .mikepanel3{
  
   border:0px;
   background-color:#6116BB;
   border-radius:12px;
  
  }
  
  </style>
  <script>
  $('.modal-dialog').css('width', '75%');
  $('.modal-dialog').css('margin', '5% 10% 15% 13%');
  </script>";
 
?>

<!-- Modal del programa que requiere ----->
<div class="modal-dialog " role="document" id="modal_actions">
  <div class="modal-content">
  <!--- Header -->
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <b id="title_1"><h4 class="modal-title" id="gridModalLabel"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2077); ?></h4></b>
      <b class="hidden" id="title_2"><h4 class="modal-title" id="H1"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2080); ?></h4></b>
    </div>
    <!-- Body --->
    <div class="modal-body" >



        
            <div id="info_body_1">
                 
	             <div class="row">

                        <!-------Finaliza Envio Email--->
                        <div class="col-md-4 text-center">
                            <div class="panel panel-default mikepanel1" >
                              <div class="panel-body">
                                  <div style="height:410px;">
                                        <?php 
										#Recuperamos el numero maximo permitido
                                        $no_maximo_desbloqueo_curso_por_metodo_email=ObtenConfiguracion(126);
                                        
                                        #Damos formato ala etiqueta.
                                        $etq=str_replace("#number_of_friends#",$no_emails,str_uso_normal(ObtenEtiqueta(2078)));
                                        $etq=str_replace("#limit_email#",$no_maximo_desbloqueo_curso_por_metodo_email,$etq);
                                        
                                        $fg_activo_periodo_trial_curso=FAMEVerificaFechaExpiracionTrialCursoAlumno($fl_usuario,$fl_programa_sp);
                                
                                        
                                        #Verificamoscuantos que los email ya esten confirmados.
                                        $Query="SELECT COUNT(*)  
                                                FROM c_desbloquear_curso_alumno A 
                                                LEFT JOIN k_envio_email_reg_selfp B ON B.fl_envio_correo=A.fl_envio_correo 
                                                WHERE  A.fl_invitado_por_usuario=$fl_usuario AND A.fl_programa_sp=$fl_programa_sp  ";
                                        $Query.=" AND B.fg_confirmado='1' ";
                                        $ro=RecuperaValor($Query);
                                        $no_mails_confirmados=$ro[0];
                                        
                                        
                                        #Verificamos is puede seguir mandando emails
                                        $fg_btn_habilitado=VerificaBotonParaDesbloquearCursoPorMetodoEnvioEmail($fl_usuario,$fl_programa_sp);
                                        
                                        $exp=explode("#",$fg_btn_habilitado);
                                        $fg_btn_habilitado=$exp[0];
                                        $etq_disabled=$exp[1];
                                        
                                        
                                        if(empty($fg_btn_habilitado)){
                                            
                                            $btn_disabled="disabled";
                                            $back="background-color: #f182d7 !important; border-color: #ef91f1 !important; ";
                                            
                                        }
                                        
                                        
                                        
                                        ?>
                                        <!--<img src="<?php echo PATH_SELF_IMG."/Thumbs_up_pink.png"?>" width="150px">-->
										<br>
                                        <h1 style="font-size:40px;"><?php echo ObtenEtiqueta(2138);?></h1>
										<br/>
				                        <p class="text-muted" style="font-size:15px; color:#696767;"><?php echo $etq; ?></p>
                                        
                                        <p><?php echo EmailConfirmadosDesbloquearCurso($fl_usuario,$fl_programa_sp);?></p>
										<?php 
										if($etq_disabled){
												
                                            echo"<br><br><br><i><p class='text-muted' style='color:#FF00BC;'>$etq_disabled </p><i>";     
                                        }else{
										?>
                                        <br /><br /><br />
                                       
                                        <?php 
                                        }

                                              
                                              
                                              
                                        ?>
                                  </div>      
										<br><br /><br />
                                        <button type="button"   class="btn btn-sm btn-rosa2 <?php echo $btn_disabled;?>" style="font-size:14px;<?php echo $back; ?> color:#fff;" onclick="InvitarCompadres(<?php echo $fl_programa_sp;?>,<?php echo $fl_usuario; ?>);"><span style='color:#fff;'><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(2090); ?></span></button>
                                        <br /><small>&nbsp;</small>
                                  


                              </div>
                            </div>
                        </div>
                     <!-------Finaliza Envio Email--->

                     <!-------Inicia pago indivual------>
                        <div class="col-md-4 text-center">
                               <div class="panel panel-default mikepanel2">
                                  <div class="panel-body">
                                      <div style="height:410px;">
                                           <!--<img src="<?php echo PATH_SELF_IMG."/Thumbs_up_blue.png"?>" width="150px">-->
										   <br>
										   <h1 style="font-size:40px;color:#fff;font-style:normal;"><?php echo str_uso_normal(ObtenEtiqueta(2139));?></h1>
                                           <br/>
                                           <?php
                                                
                                                #Damos formato ala etiqueta.
                                                $etq_d=str_replace("#dias#",$no_dias_pago." days",str_uso_normal(ObtenEtiqueta(2099)));
                                               
                                                if($mn_precio_decimal)
                                                    $punto=".";
                                                else
                                                    $punto="";
                                                
                                           ?>
				                            <p class="text-muted" style="font-size:15px;font-style:normal;color: #fff;"><?php echo $etq_d; ?></p>
											<br><br><br><br>
                                            <p class="" style="font-size:32px; color: #fff;"><b><?php echo "$".$mn_precio_entero."<sup style='font-size:15px;top: -11px;'>$punto".$mn_precio_decimal."</sup> ".ObtenConfiguracion(113); ?></b></p>
                                       </div>
									   <br><br /><br />
                                       <button type="button" class="btn btn-sm btn-primary azul" style="font-size:14px; color:#fff;" onclick="RealizarPagoIndividualCurso(<?php echo $fl_programa_sp;?>,<?php echo $fl_usuario; ?>,<?php echo $mn_precio;?>);"><span style='color:#fff;'><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;<?php echo str_uso_normal(ObtenEtiqueta(2105)); ?></span></button>
                                       <br /><small>&nbsp;</small>

                                  </div>
                               </div> 

                        </div>
                     <!-------Termina pago indivual------>




                        <!-------Inicia pago Plan------>
                        <div class="col-md-4 text-center">
                                <div class="panel panel-default mikepanel3">
                                      <div class="panel-body">
                                          <div style="height:410px;">
                                                <!--<img src="<?php echo PATH_SELF_IMG."/Thumbs_up_purple.png"?>" width="150px">-->
												<br>
												<h1 style="font-size:40px;color:#fff;font-style:normal;"><?php echo str_uso_normal(ObtenEtiqueta(2140));?></h1>
                                               <br/>
				                                <p class="text-muted" style="font-size:15px;font-style:normal;color: #fff;"><?php echo str_uso_normal(ObtenEtiqueta(2079)); ?></p>

                                              <?php
                                              #Recupermaos el plan mes y año para el curso 
                                              $Query="SELECT ds_descuento_mensual, ds_descuento_anual,mn_mensual,mn_anual FROM c_princing_course WHERE 1=1 ";
                                              $row=RecuperaValor($Query);
                                              $porcentaje_mes=number_format($row['ds_descuento_mensual']); 
                                              $porcentaje_anio=number_format($row['ds_descuento_anual']);
                                              $mn_mes=$row['mn_mensual'];
                                              $mn_anio=$row['mn_anual'];
                                              
                                              
                                              
                                              
                                              ?>

                                              <div class="row"> 
													    <div class="col-md-1">&nbsp;</div>
														<div class="col-md-10">
														  <form class="smart-form text-center" style="background:transparent !important;" >
																<fieldset style="background:transparent !important;">
																			<label class="radio" style="color: #fff;font-size:15px;">
																				<input name="radio" name="mes"  id="mes" type="radio">
																				<i></i><b><?php echo "$".$mn_mes." ".ObtenConfiguracion(113)." ".ObtenEtiqueta(1705);?></b><br /><small class="text-muted" style="color:#fff;"><?php echo "(".$porcentaje_mes."% discount)"; ?></small> </label>
																			<label class="radio" style="color: #fff;font-size:15px;">
																				<input name="radio" name="anio"  id="anio"  type="radio">
																				<i></i><b><?php echo "$".$mn_anio." ".ObtenConfiguracion(113)." ".ObtenEtiqueta(1706);?></b><br /><small class="text-muted" style="color:#fff;"><?php echo "(".$porcentaje_anio."% discount)"; ?> </small></label>
																			
																 </fieldset>




														   </form>
														   
                                                           


														</div>   
											   </div>


                                                            <section class="smart-form text-left" style="margin-left:40px;">
									                                 <label class="checkbox" style="padding-left: 24px;"><input name="fg_rm" id="fg_rm" type="checkbox"  ><i></i><p style="font-size:13px; color:#fff;"><?php echo str_uso_normal(ObtenEtiqueta(2131)) ?><!--<font color="#0092dc"><a href="javascript:void(0);"  data-toggle="modal" data-target="#myModales23" >&nbsp;<?php echo ObtenEtiqueta(913); ?></a></font>--></p></label>
								                            </section>
                                                           
                                              <style>
                                                  .mikelntn {
                                                  
                                                      background-color: #7C8EEC !important;
                                                      border-color: #7C8EEC !important;
													  color:#fff;
                                                  }

                                              </style>
                                          </div> 
										  <br/>
										  <br /><br />
                                           <button type="button" class="btn btn-sm btn-success morado mikelntn disabled" id="pag_plan" style="font-size:14px;" onclick="RealizarPagoPlan(<?php echo $fl_programa_sp;?>,<?php echo $fl_usuario; ?>);"><span style='color:#fff;'><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(2091); ?></span></button>
                                          <br /> <small class="text-muted" style="font-size:13px;color:#fff"><?php echo ObtenEtiqueta(2125); ?></small>
                                       </div>

                                      



                               </div>

                        </div>
                        <!-------Termina pago Plan------>
	             </div>


	             <!--end info inicial-->
	        </div> 
    
            <div class="hidden" id="info_body_2">
	                <!--PresentaInfo para envio email-->
                    <div class="row">
                            
                            <div class="col-md-12 text-center">
                                <img src="<?php echo PATH_SELF_IMG."/Thumbs_up_pink.png";?>" width="150px" align="center">
                            
                            
                                <br /><br />
                                <p class="text-muted" style="font-size:17px;"><?php echo ObtenEtiqueta(2081); ?></p>
                                 <!--<p class="text-muted" style="font-size:17px;"><?php echo ObtenEtiqueta(2081); ?></p>-->
                            </div>
                           

                    </div>

                	<div class="row">
			                
					        <div class="col-md-12 text-center">
                                
                           <?php if(!empty($no_invitaciones_enviadas)){ 
                                     
                                     
                                     $Query="SELECT A.fl_envio_correo,A.ds_email,fl_invitado_por_usuario,B.fg_confirmado 
                                            FROM c_desbloquear_curso_alumno A 
                                            LEFT JOIN k_envio_email_reg_selfp B ON B.fl_envio_correo=A.fl_envio_correo 
                                            WHERE  A.fl_invitado_por_usuario=$fl_usuario AND A.fl_programa_sp=$fl_programa_sp  ORDER BY A.fl_envio_correo ASC ";           
                                     $rs1 = EjecutaQuery($Query);
                                     
                                     for($tot=1;$row2=RecuperaRegistro($rs1);$tot++) {
                                         $email_enviado=str_texto($row2[1]);
                                         $fg_confirmado=$row2[3];
                                         $fl_envi_correo=$row2[0];
                                         
                                         $ya_envio_emails=1;
                                         
                           ?>  

                                        <div class="row" ><!---ini row--->
                                        <div class="col-md-1">&nbsp;</div>
                                        <div class="col-md-6">
                                       
                                                 <div class="smart-form">
									                 <fieldset style="margin-top:0px;margin-bottom:0px;padding:5px;">
                                                            <section >
										                        <label class="input"> <i class="icon-append fa fa-envelope-o"></i>
											                        <input placeholder="example@email.com" type="text" id="email_<?php echo $tot;?>"  value="<?php echo $email_enviado; ?>">   
										                        </label>
																 <!--error formato email-->
											                            <div class="alert alert-danger fade in hidden" style="margin:2px;padding:5px;" id="error_email_<?php echo $fl_envi_correo;?>">
								                                            <i class="fa-fw fa fa-times"></i>
								                                            <?php echo ObtenEtiqueta(1533); ?>.
							                                            </div>
                                                                        <!--error duplicate emil -->
                                                                        <div class="alert alert-danger fade in hidden" style="margin:2px;padding:5px;" id="duplicate_email_<?php echo $fl_envi_correo;?>">
								                                            <i class="fa-fw fa fa-times"></i>
								                                            <?php echo ObtenEtiqueta(2086); ?>.
							                                            </div>
                                                                        <!--error email ya esixte en FAME--> 
                                                                        <div class="alert alert-danger fade in hidden" style="margin:2px;padding:5px;" id="otro_email_<?php echo $fl_envi_correo;?>">
								                                            <i class="fa-fw fa fa-times"></i>
								                                            <?php echo ObtenEtiqueta(2083); ?>.
							                                            </div>
                                                                        <!--este email ya se envio para desbloquear curso YA EXISTE EN DB de ebvios de emails-->
                                                                        <div class="alert alert-danger fade in hidden" style="margin:2px;padding:5px;" id="anteriormente_ya_fue_enviado_<?php echo $fl_envi_correo;?>">
								                                            <i class="fa-fw fa fa-times"></i>
								                                            <?php echo ObtenEtiqueta(2084); ?>.
							                                            </div> 
									                        </section>
                                                     </fieldset>
                                                </div>
                                            </div>

                                
                                            <div class="col-md-4 text-left">
                                                      <?php if($fg_confirmado){ ?>
                                                      <button class="btn btn-success disabled"  style="margin-top:6px; float:left;" id="btn_active<?php echo $tot;?>"><?php echo ObtenEtiqueta(2087); ?></button>
                                                      <?php }else{ ?>
                                                      <button class="btn btn-danger disabled"  style="margin-top:6px;float:left;" ><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2088); ?></button>
                                                      <button class="btn btn-secondary" style="margin-left:5px;margin-top:6px;background-color: #ebebeb;" id="resend_<?php echo $tot;?>"  onclick="ReenviarEmail(<?php echo $fl_envi_correo?>,<?php echo $fl_programa_sp;?>,<?php echo $tot;?>)"><i class="fa fa-share" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(2089); ?></button>
                                                      <?php } ?>
                                            </div>
                                        </div>
                                 <?php } ?>



                                
                           <?php }else{ ?>        
                                
                                      
                                        <?php
                                          for($i = 1; $i <= $no_emails; $i++){
                                  
                                        ?>
                               
                                        
                                        <div class="row" ><!---ini row--->
                                        <div class="col-md-2">&nbsp;</div>
                                        <div class="col-md-8">
                                      
                                                <div class="smart-form">
									                 <fieldset> <!--style="margin-top:0px;margin-bottom:0px;padding:5px;"--->
                                                            <section >
										                        <label class="input"> <i class="icon-append fa fa-envelope-o"></i>
											                        <input placeholder="example@email.com" type="text" id="email_<?php echo $i;?>"  >      
										                        </label>
																<!--error formato email-->
											                            <div class="alert alert-danger fade in hidden" style="margin:2px;padding:5px;" id="error_email_<?php echo $i;?>">
								                                            <i class="fa-fw fa fa-times"></i>
								                                            <?php echo ObtenEtiqueta(1533); ?>.
							                                            </div>
                                                                        <!--error duplicate emil -->
                                                                        <div class="alert alert-danger fade in hidden" style="margin:2px;padding:5px;" id="duplicate_email_<?php echo $i;?>">
								                                            <i class="fa-fw fa fa-times"></i>
								                                            <?php echo ObtenEtiqueta(2086); ?>.
							                                            </div>
                                                                        <!--error email ya esixte en FAME--> 
                                                                        <div class="alert alert-danger fade in hidden" style="margin:2px;padding:5px;" id="otro_email_<?php echo $i;?>">
								                                            <i class="fa-fw fa fa-times"></i>
								                                            <?php echo ObtenEtiqueta(2083); ?>.
							                                            </div>
                                                                        <!--este email ya se envio para desbloquear curso YA EXISTE EN DB de ebvios de emails-->
                                                                        <div class="alert alert-danger fade in hidden" style="margin:2px;padding:5px;" id="anteriormente_ya_fue_enviado_<?php echo $i;?>">
								                                            <i class="fa-fw fa fa-times"></i>
								                                            <?php echo ObtenEtiqueta(2084); ?>.
							                                            </div> 
									                        </section>
                                                     </fieldset>
                                                   </div>
                                            </div>
                                            </div>
                                        <?php } ?>               
    
                                                
                                       

                               





                                   
                                <?php
                                }
                                ?>
                                

                                
                            </div>

				    </div>
			</div>

			
           <div class="hidden" id="info_body_3">
                   <!--PresentaStripe-->
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">

                            <div id="stripe"> </div>

                           <!--   <?php 
                            #Stripe no permite mas de dos decimales y se da formato a dos decimales nadamas.
                            if(is_int($price_desbloquear_stripe))	
                                $mn_monto_pagar=$price_desbloquear_stripe;
                            else
                                $mn_monto_pagar=number_format((float)$price_desbloquear_stripe,2,'.',''); 
                            
                            #Recuperamos el estado/provincia del usuario para determina el monto del tax.
                            $mn_tax=Tax_Can_User($fl_usuario); 
                            if(empty($mn_tax))
                                $mn_tax=0;
                            
                            $url_charge="site/charge_desbloquear_curso.php";
                            $ds_decripcion_pago=ObtenEtiqueta(2093);
                            
                            
                            
                            ?>
                            
                            
                          FormaStripe('frm_stripe',$mn_monto_pagar,$mn_tax,$url_charge,$fl_programa_sp,$ds_decripcion_pago,'','','','',1); ?>
	                        -->
                        </div>


                    </div>
                  <!--end stripe--->

           </div>







	 <div id="send_invitacion"></div>  
	 
    </div>
	
	<div class="modal-footer">
        <?php 
        ?>
         <div class="row hidden" id="info_footer_1"> 
             <?php if($ya_envio_emails){ ?>

              <div class="col-md-12 text-center">
                     <button type="button" class="btn btn-sm btn-default" style="font-size:14px;" data-dismiss="modal" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                    
               </div>


             <?php }else{ ?>
               
              
               <div class="col-md-12 text-center">
                    <style>
                        .mikedisabled {
                            background-color: #f5ace4  !important;
                            border-color: #f5ace4  !important;
                        }

                    </style>

                     <button type="button" id="btn_evio_email" class="btn btn-sm btn-rosa2 <?php echo $btn_disabled;?> " style="font-size:14px;color:#fff;<?php echo $back; ?>" onclick="Send_Invitaciones(<?php echo $fl_programa_sp;?>);"><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(2090); ?></button>
               
                   
               </div>
              

             <?php }?>

          </div>



           <div class="row " id="info_footer_2">
                <div class="col-md-12 text-center">
                    <button type="button" class="btn btn-sm btn-default hidden" style="font-size:14px;" data-dismiss="modal" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                    
                </div>
                 
           </div>

         
 
     </div>
	
	
	
  </div>



</div>












<script>


    $('#anio').click(function() {

        if ($('#fg_rm').prop('checked')) {

            $('#pag_plan').removeClass('disabled');
            $('#pag_plan').removeClass('mikelntn');
        }


    });
    $('#mes').click(function() {

        if ($('#fg_rm').prop('checked')) {

            $('#pag_plan').removeClass('mikelntn');
            $('#pag_plan').removeClass('disabled');
        }

    });


    $('#fg_rm').click(function () {

        if ($('#fg_rm').prop('checked')) {

            if ($('#anio').prop('checked')) {
                $('#pag_plan').removeClass('mikelntn');
                $('#pag_plan').removeClass('disabled');
            } else if ($('#mes').prop('checked')) {
                $('#pag_plan').removeClass('mikelntn');
                $('#pag_plan').removeClass('disabled');

            } else {
                $('#pag_plan').addClass('mikelntn');
                $('#pag_plan').addClass('disabled');

            }


        } else {
            $('#pag_plan').addClass('mikelntn');
            $('#pag_plan').addClass('disabled ');
        }

    });


    function InvitarCompadres(){
        $('#info_body_1').addClass('hidden');
        $('#info_body_2').removeClass('hidden');

        $('#info_footer_1').removeClass('hidden');
        $('#info_footer_2').addClass('hidden');
    }
    function RealizarPagoPlan(fl_programa_sp,fl_alumno) {
        $('#info_body_1').addClass('hidden');
        $('#info_body_2').addClass('hidden');
        $('#info_body_3').removeClass('hidden');

       
       

        var fl_programa_sp=fl_programa_sp;
        var fl_alumno=fl_alumno;
        
        

        if( $('#mes').prop('checked') ) {
            var fg_tipo_plan=1;
           
        }else{
            var fg_tipo_plan=2;          
        }

        var fg_plan=1;

     

       $.ajax({
            type: 'POST',
            url: 'site/presenta_pago_curso.php',
            async: false,
            data: 'fl_programa_sp='+fl_programa_sp+
                  '&fl_alumno='+fl_alumno+
                  '&fg_tipo_plan='+fg_tipo_plan+
                  '&fg_plan='+fg_plan,
            success: function (data) {
                $('#stripe').html(data);
            }
        });

      


    }


    function RealizarPagoIndividualCurso(fl_programa_sp,fl_alumno,mn_precio){
        $('#info_body_1').addClass('hidden');
        $('#info_body_2').addClass('hidden');
        $('#info_body_3').removeClass('hidden');

       
       
        var fg_pago_individual_curso=1;

            $.ajax({
                type: 'POST',
                url: 'site/presenta_pago_curso.php',
                async: false,
                data: 'fl_programa_sp='+fl_programa_sp+
                      '&fg_pago_individual_curso='+fg_pago_individual_curso+
                      '&fl_alumno='+fl_alumno+
                      '&mn_precio='+mn_precio,
                success: function (data) {
                    $('#stripe').html(data);
                }
            });
      
    }



    function ReenviarEmail(fl_envio_correo,fl_programa_sp,no_input){
        var ds_email=document.getElementById('email_'+no_input).value;
        var fg_reenviar=1;
            $.ajax({
                    type: 'POST',
                    url: 'site/send_email_friends.php',
                    async: false,
                    data: 'fl_programa_sp='+fl_programa_sp+
                          '&fl_envio_correo='+fl_envio_correo+
                          '&fg_reenviar='+fg_reenviar+
                          '&ds_email='+ds_email,

                success: function (data) {
                    $('#send_invitacion').html(data);
                }
            });
       

    
    }


    function Send_Invitaciones(fl_programa_sp) {

        <?php
        for($i = 1; $i <= $no_emails; $i++){
        ?>
        var email_<?php echo $i;?> = document.getElementById("email_<?php echo $i;?>").value;

        <?php } ?>

        var fl_programa_sp = fl_programa_sp;
        var total_email =<?php echo $no_emails?>;
        var fg_esperando_confirmacion=<?php echo $fg_esperando_confirmacion ?>;

        $.ajax({
            type: 'POST',
            url: 'site/send_email_friends.php',
            async: false,
            data: 'fl_programa_sp='+fl_programa_sp+
                 <?php
                 for($i = 1; $i <= $no_emails; $i++){
                 ?>
                   '&email_<?php echo $i;?>='+email_<?php echo $i;?>+
                 <?php } ?>
                  '&total_email='+total_email+
                  '&fg_esperando_confirmacion='+fg_esperando_confirmacion,

            success: function (data) {
                   $('#send_invitacion').html(data);
               
                //$("#text").val('');
                //document.getElementById("text").focus();
            }
        });


    }


</script>