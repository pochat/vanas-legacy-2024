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
  
  
  #Recuperamos el isttituo
  $fl_instituto=ObtenInstituto($fl_usuario);
  $opt_disabled=NULL;
  
  
?>


  
   
      
	

        <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="font-awesome-4.6.3/font-awesome-4.6.3/css/font-awesome.min.css">
        <style>
           .morado{
               background-color: #6c15c3 !important;
               border-color: #6c15c3 !important;
               color: #fff !important;
 
  
            }
		.mikepanel3{
  
   border:0px;
   background-color:#6116BB;
   border-radius:12px;
  
  }
        </style>

    <!-- MAIN CONTENT -->
    <div id="content">

     
            <!-- widget content -->
            <div class="widget-body">
                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active" id="tab1">
                        <a href="#current_plan" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-bars"></i>&nbsp;<?php echo ObtenEtiqueta(984) ?></a>
                    </li>

                     <li id="tab2">
                        <a href="#history" data-toggle="tab" onclick="VerTabla();" ><i class="fa fa-fw fa-lg fa-money"></i>&nbsp;<?php echo ObtenEtiqueta(986) ?></a>
                    </li>
                    <li id="tab3" class="hidden" >
                        <a href="#renewal" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-refresh"></i>&nbsp; Renewal </a>
                    </li>
                </ul>


                 <div id="myTabContent1" class="tab-content padding-10 "><!--class='no-border'--->
                    <div class="tab-pane fade in active" id="current_plan">


                        <?php 
                             #Recuperamos informacion de su cuenta.
                             $Query="SELECT B.nb_plan, fg_plan,fe_periodo_inicial,fe_periodo_final,mn_total_plan,fe_periodo_final_mes,fg_cancelado,A.fg_status   
                                            FROM k_current_plan_alumno A
                                            JOIN c_plan_fame B ON B.cl_plan_fame=A.cl_plan_fame 
                                            
                                            WHERE fl_alumno=$fl_usuario  ";
                             $row=RecuperaValor($Query);
                             $nb_plan_fame=str_texto($row[0]);
                             $fg_plan=str_texto($row[1]);
                             $mn_total_plan=$row[4];
                             $fe_terminacion_plan=str_texto($row['fe_periodo_final']);
                             $fg_cancelado=$row['fg_cancelado'];
                             $fg_status=$row['fg_status'];
                             
                             
                             if($fg_plan=='A'){
                                 $fg_tipo_plan=ObtenEtiqueta(1521);
                                 $mn_total_plan=$mn_total_plan." per month";
                                 
                                 $fg_plan_etiqueta="12 months";
                             }else{
                                 $fg_tipo_plan=ObtenEtiqueta(1520);
                                 $mn_total_plan=  ($mn_total_plan)." per month";
                               
                                 $fg_plan_etiqueta="1 month";

                                 $fe_terminacion_plan=str_texto($row['fe_periodo_final_mes']);
                             }
                             
                             
                             
                             #damos formato a fcha de terminacion del plan
                             $date=date_create($fe_terminacion_plan);
                             $fe_expiracion_plan=date_format($date,'F j, Y');
                             
                             
                             $fe_proximo_pago=strtotime('+1 day',strtotime($fe_terminacion_plan));
                             $fe_proximo_pago= date('Y-m-d',$fe_proximo_pago);
                             
                             #damos formato a fecha de proximo pago
                             $date=date_create($fe_proximo_pago);
                             $fe_proximo_pago=date_format($date,'F j, Y');
                             #Label cancel
                             $cadenas= str_uso_normal(ObtenEtiqueta(2617)); 
                             $etq_format_cancel = str_replace("#fame_fe_expiration_plan#", $fe_expiracion_plan,$cadenas);
                             
                        ?>



                                 <div class="row" >
                                       <div class="col-xs-4 col-sm-4 text-right">
								            <p style="font-size:15px;"><?php echo ObtenEtiqueta(1711).":";?></p>
								   
                                            <p style="font-size:15px;"><?php echo ObtenEtiqueta(987).":";?></p>
                                        
                                        
                                            <p style="font-size:15px;"><?php echo ObtenEtiqueta(994).":";?></p>
                                           
                                            <p style="font-size:15px;"><?php echo ObtenEtiqueta(996).":";?></p>
                                            <p style="font-size:15px;"><?php echo ObtenEtiqueta(997).":";?></p>
                                            

                                       </div>

                                       <div class="col-xs-6 col-sm-6 "><!---AQUEI VAN LOS DATOS FALTANTES-->
								             <?php if($fg_plan){ ?>
								             <p style="font-size:15px;"><b><?php echo $nb_plan_fame;  ?></b></p>
                                             <p style="font-size:15px;"><b><?php echo $fg_tipo_plan;?></b></p>
                                             <p style="font-size:15px;"><b><?php echo "$".number_format($mn_total_plan,2)." ";?></b></p>
                                            
                                             <p style="font-size:15px;"><b><?php echo $fe_expiracion_plan;  ?></b></p>
                                             <p style="font-size:15px;"><b><?php echo $fe_proximo_pago;  ?></b></p>

                                             <p style="font-size:15px;"><b><?php echo ObtenEtiqueta(1552)." ".$fe_expiracion_plan;  ?></b>
                                             <a href="#renewal"  data-toggle="tab" name="btn_renew" id="btn_renew" class="btn btn-primary btn-xs" onclick="RenewOptions()" ><?php echo ObtenEtiqueta(1500);?></a>
                                             <p></p><?php if($fg_status=='F'){ ?><span style="font-size:12px;" class='label label-success'><?php echo ObtenEtiqueta(2646)?> </span> <?php } ?>
                                             <?php }else{ ?>

                                             <p style="font-size:15px;"><?php echo ObtenEtiqueta(2113);  ?></p>
                                             <p style="font-size:15px;"><?php echo ObtenEtiqueta(2113);  ?></p>
                                             <p style="font-size:15px;"><?php echo ObtenEtiqueta(2113);  ?></p>
                                             <p style="font-size:15px;"><?php echo ObtenEtiqueta(2113);  ?></p>
                                             <p style="font-size:15px;"><?php echo ObtenEtiqueta(2113);  ?></p>
                                            
                                             <button type="button" class="btn btn-sm btn-success morado"  data-toggle="modal" data-target="#PagoSuscription" style="font-size:14px; "onclick="Reset();" ><span style='color:#fff;' ><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(2114); ?></span></button>
      
      
                                            <?php } ?>
                                           <p><?php if($fg_cancelado==1){ 
                                                        echo $etq_format_cancel;
                                                    }?></p>

                                            
                                            <!------Presenta Modal para pago de una susbscripcion.-------->

                                                    <div class="modal fade" id="PagoSuscription" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                      <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                          <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-credit-card-alt"></i> <?php echo ObtenEtiqueta(2115); ?></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -23px;">
                                                              <span aria-hidden="true">&times;</span>
                                                            </button>
                                                          </div>
                                                          <div class="modal-body">
                                                            <div id="body1">
																<div class="row">
																	<div class="col-md-2 ">&nbsp;
																	</div>
																	<div class="col-md-8 ">
																		<div class="panel panel-default mikepanel3" >
																			<div class="panel-body">
																				<div style="height:410px;">
																					<h1 style="font-size:40px;color:#fff;font-style:normal;" class='text-center'><?php echo str_uso_normal(ObtenEtiqueta(2140));?></h1>
																									 <br/>
																					<p class="text-muted text-center" style="font-size:15px;font-style:normal;color: #fff;"><?php echo str_uso_normal(ObtenEtiqueta(2079)); ?></p>
																						<?php 
																						
																							 #Recupermaos el plan mes y año para el curso 
																							 $Query="SELECT ds_descuento_mensual, ds_descuento_anual,mn_mensual,mn_anual FROM c_princing_course WHERE 1=1 ";
																							 $row=RecuperaValor($Query);
																							 $porcentaje_mes=number_format($row['ds_descuento_mensual']); 
																							 $porcentaje_anio=number_format($row['ds_descuento_anual']);
																							 $mn_mes=$row['mn_mensual'];
																							 $mn_anio=$row['mn_anual']; 
																						
																						?>
																
																							<form class="smart-form text-center" style="background:transparent !important;">
																								<fieldset  style="background:transparent !important;">
																											<label class="radio" style="color: #fff;font-size:15px;">
																												<input name="radio" name="mes"  id="mes" type="radio">
																												<i></i><b><?php echo "$".$mn_mes." ".ObtenConfiguracion(113)." ".ObtenEtiqueta(1705);?></b><br /><small class="text-muted" style="color:#fff;"><?php echo "(".$porcentaje_mes."% discount)"; ?></small> </label>
																											<label class="radio" style="color: #fff;font-size:15px;">
																												<input name="radio" name="anio"  id="anio"  type="radio">
																												<i></i><b><?php echo "$".$mn_anio." ".ObtenConfiguracion(113)." ".ObtenEtiqueta(1706);?></b><br /><small class="text-muted" style="color:#fff;"><?php echo "(".$porcentaje_anio."% discount)"; ?> </small></label>
																			
																								 </fieldset>
																							</form>
																							
																							
																						    <style>
																							  .mikel {
															  
																								  background-color: #BB8DE9 !important;
																								  border-color: #BB8DE9 !important;
																								  color:#fff;
																							  }

																						    </style>
																							
																							<section class="smart-form text-center">
									                                                             <label class="checkbox" ><input name="fg_rm" id="fg_rm" type="checkbox" ><i></i><p style="font-size:13px; color:#fff;"><?php echo str_uso_normal(ObtenEtiqueta(2131)) ?></p></label>
																							</section>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="col-md-2 ">&nbsp;
																	</div>
																</div>						
																	<!--
															
															
															
                                                                      <div class="row">
                                                                          <div class="col-md-12 text-center">
                                                                                <img src="<?php echo PATH_SELF_IMG."/Thumbs_up_purple.png"?>" width="150px">
                                                                                <p></p><br/>
				                                                                <p class="text-muted" style="font-size:15px;"><?php echo str_uso_normal(ObtenEtiqueta(2079)); ?></p>
                                                                          </div>
                                                                      </div>

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
                                                                          <div class="col-md-3">&nbsp;</div>
                                                                          <div class="col-md-6">

                                                                              <form class="smart-form text-center">
                                                                                    <fieldset>
                                                                                                <label class="radio" style="color: #333;font-size:15px;">
																                                    <input name="radio" name="mes"  id="mes" type="radio">
																                                    <i></i><b><?php echo "$".$mn_mes." ".ObtenConfiguracion(113)." ".ObtenEtiqueta(1705);?></b><br /><small class="text-muted"><?php echo "(".$porcentaje_mes."% discount)"; ?></small> </label>
															                                    <label class="radio" style="color: #333;font-size:15px;">
																                                    <input name="radio" name="anio"  id="anio"  type="radio">
																                                    <i></i><b><?php echo "$".$mn_anio." ".ObtenConfiguracion(113)." ".ObtenEtiqueta(1706);?></b><br /><small class="text-muted"><?php echo "(".$porcentaje_anio."% discount)"; ?> </small></label>
															    
                                                                                     </fieldset>

                                                                                  


                                                                               </form>

                                                                              <style>
                                                                                  .mikel {
                                                  
                                                                                      background-color: #BB8DE9 !important;
                                                                                      border-color: #BB8DE9 !important;
													                                  color:#fff;
                                                                                  }

                                                                              </style>
                                                                            
                                                                          </div>
                                                                          <div class="col-md-3">&nbsp;</div>

                                                                      </div>

                                                                      <div class="row">
                                                                          <div class="col-md-2">&nbsp;</div>
                                                                           <div class="col-md-8">

                                                                                    <section class="smart-form text-center">
									                                                         <label class="checkbox" ><input name="fg_rm" id="fg_rm" type="checkbox" ><i></i><p style="font-size:13px; color:#999;"><?php echo ObtenEtiqueta(2131) ?></p></label>
								                                                    </section>


                                                                           </div>
                                                                           <div class="col-md-2">&nbsp;</div>
                                                                      </div>
																--->


                                                                                   

                                                              </div>
                                                              
                                                              <div id="stripe"> </div>

                                                          </div>
                                                          <div class="modal-footer text-center">
                                                              <button type="button" class="btn btn-sm btn-success morado mikel disabled" id="pag_plan" style="font-size:14px;" onclick="RealizarPagoPlan(<?php echo $fl_usuario; ?>);"><span style='color:#fff;'><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(2091); ?></span></button>

                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>

                                            <!----------------->


                                           <!-------Presenta 2do modal que tiene el contrato------->

                                           
                                                 <!---MJD mODAL que mostrar bien chido el contenido de el course code--->
                                                  <div class="modal" id="myModales2" data-backdrop="static">
	                                                <div class="modal-dialog">
                                                      <div class="modal-content"  >
           
		                                                    <div class="modal-header text-center">
			                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
			                                                <h4 class="modal-title text-left"><i class="fa fa-file"></i> <?php echo ObtenEtiqueta(913);?></h4>
                                                            </div>
		                                                    <div class="modal-body" style='height:400px;overflow-y: scroll;overflow-x:hidden'>

                                                                <?php 
                                                                
                                                                #se genera el cuerpo del documento del contrato
                                                                $ds_encabezado_contrato = genera_ContratoFame($fl_instituto, 1,102,$fl_usuario);
                                                                $ds_cuerpo_contrato = genera_ContratoFame($fl_instituto, 2, 102,$fl_usuario);
                                                                $ds_pie_contrato = genera_ContratoFame($fl_instituto, 3,102,$fl_usuario);
                                                                
                                                                echo $ds_encabezado_contrato."<br/> ".$ds_cuerpo_contrato."<br/> ".$ds_pie_contrato;
                                                                
                                                                
                                                                ?>




                                                            </div>

                                                           <div class="modal-footer">
                                                          <a href="#" data-dismiss="modal" class="btn btn-default"><i class="fa fa-times-circle" aria-hidden="true"></i> Close</a>
                                                          <!--<a href="#" class="btn btn-primary">Save changes</a>-->
                                                        </div>



		   
                                                      </div>
                                                    </div>
                                                </div>






                                           <!--------------->




                                    
                                       </div>
                                 </div>


                        
                    </div>

                    <!--Payment History-->
                    <div class="tab-pane fade " id="history">
                        
                                              
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
                    <!---- finaliza payment history---->

                    <!-------Renewal Options------->
                     <div class="tab-pane fade" id="renewal">

                       <?php  
                       
                       
                       $etq=str_uso_normal(ObtenEtiqueta(2100));
                       #Label 1 Formateamos etiquetas, con la fecha de expiracion.
                       $etq_radio_button1 = str_replace("#fe_expiration_plan#",$fe_expiracion_plan,$etq); # nb_isntituto 
                       $etq_radio_button1 = str_replace("#tipo_plan#",$fg_plan_etiqueta,$etq_radio_button1); # nb_isntituto
                       
                       if($fg_plan)
                           $chequed_opt_renew1=" checked='checked' ";
                       else
                           $chequed_opt_renew1="";
                       
                       if($fg_cancelado==1){
                           $chequed_opt_renew4="checked='checked'";
                       }

                       #Label cancel
                       $cadenas= str_uso_normal(ObtenEtiqueta(1587)); 
                       $etq_format_cancel = str_replace("#fame_fe_expiration_plan#", $fe_expiracion_plan,$cadenas);
                       
                       if($fg_status=='F'){
                           $opt_disabled="disabled";
                           $chequed_opt_renew1="";
                           $chequed_opt_renew5="checked='checked'";
                       }

                       ?>



                            <div class="row">
                                <div class="col-md-2">&nbsp;</div>

                                 <div class="col-md-8">
                                     <div class="smart-form">
                                         <fieldset>
												<section>
                                                            <label class="radio"  >
																<input name="radio" <?php echo $chequed_opt_renew1; ?>  id="opt1" type="radio" value="1" <?php echo $opt_disabled;?> >
																<i></i><?php echo $etq_radio_button1; ?>

															</label>

                                                    <?php if($fg_status=='F'){ ?>

                                                            <label class="radio"  >
																<input name="radio" <?php echo $chequed_opt_renew5; ?> type="radio" id="opt5" value="5"  />
																<i></i><?php echo str_uso_normal(ObtenEtiqueta(2647)); ?>


                                                            </label>
                                                    <?php }else{ ?>
                                                            <label class="radio"  >
																<input name="radio" <?php echo $chequed_opt_renew4; ?> type="radio" id="opt4" value="4"  />
																<i></i><?php echo str_uso_normal(ObtenEtiqueta(2644)); ?>


                                                            </label>


                                                    <?php } ?>

                                                            <label class="radio"  >
																<input name="radio" <?php echo $chequed_opt_renew4; ?> type="radio" id="opt3" value="3"  />
																<i></i><?php echo $etq_format_cancel; ?>


                                                            </label>


                                                </section>
                                         </fieldset>
                                     </div>
                                 </div>
                                 <div class="col-md-2">&nbsp;</div>
                            </div> 
                         
                         
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button  class="btn btn-default" href="#current_plan" data-toggle="tab"  onclick="Cancel()" style="border-radius: 10px;"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(14); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>    
                                        <a  class="btn btn-primary" name="aply" id="aply" style="border-radius: 10px;"  onclick="SelectOptionRenew()">&nbsp;&nbsp;<?php echo ObtenEtiqueta(1598); ?>&nbsp;&nbsp;</a>    

                                </div>

                            </div>
                         
                         
                            <div id="presenta_opc_renovacion"></div>

                     </div>

                    <!---->

                 </div>


            </div>
    </div>

 <!--script para realizar pago--->
<script>
   
    $('#anio').click(function () {

        if ($('#fg_rm').prop('checked')) {

            $('#pag_plan').removeClass('disabled');
            $('#pag_plan').removeClass('mikel');
           
        }


        



    });
    $('#mes').click(function () {

        if ($('#fg_rm').prop('checked')) {

        $('#pag_plan').removeClass('mikel');
        $('#pag_plan').removeClass('disabled');

        }


    });


    $('#fg_rm').click(function () {

        if ($('#fg_rm').prop('checked')) {

            if ($('#anio').prop('checked')) {
                $('#pag_plan').removeClass('mikel');
                $('#pag_plan').removeClass('disabled');
            } else if ($('#mes').prop('checked')) {
                $('#pag_plan').removeClass('mikel');
                $('#pag_plan').removeClass('disabled');

            } else {
                $('#pag_plan').addClass('mikel');
                $('#pag_plan').addClass('disabled');

            }

        } else {
            $('#pag_plan').addClass('mikel');
            $('#pag_plan').addClass('disabled');
        }

    });

    function RealizarPagoPlan(fl_alumno) {
        $('#body1').addClass('hidden');//Ocultamos el div que muetra la imagne y el selecctor del plan para posteriormente mostrar stripe.
        $('#pag_plan').addClass('hidden');

            var fl_alumno = fl_alumno;
        if ($('#mes').prop('checked')) {
            var fg_tipo_plan = 1;
        } else {
            var fg_tipo_plan = 2;
        }
            var fg_plan = 1;

            var fl_programa_sp = 1;

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

    function Reset(){

        //alert('fua');
        $('#stripe').empty();
        $('#body1').removeClass('hidden');
        $('#pag_plan').removeClass('hidden');

    }

</script>
    <!--finaliza script para realizar pago-->


<script>
    function RenewOptions() {

        $("#tab1").removeClass('active');//se quita la callse active de la tab1
        $("#current_plan").removeClass('active');
        $("#tab3").removeClass('hidden');//desocultamos latab 3
        $("#tab3").addClass('active');//se agrega y apsa a tab 3

    }

    function Cancel() {
        $("#tab3").addClass('hidden');//desocultamos latab 3
        $("#current_plan").addClass('active');//se quita la callse active de la tab1


    }
    function SelectOptionRenew() {
    
    
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


        //pasamos por ajax los valores y presentamos modal.
        $.ajax({
            type: 'POST',
            url: 'site/presenta_opc_renovacion_student.php',
            data: '&fg_opcion ='+fg_opcion,

            async: true,
            success: function (html) {
                $('#presenta_opc_renovacion').html(html);
            } 
        });


    
    }

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
            "ajax": "Querys/billing_student.php",
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
            "order": [0],
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

  
   //MJD Por alguna razon la tabla aparace con style display none y con esta funcion se la eliminoo parq eu se deje ver bien chida.
    function VerTabla() {

        $("#gabriel").removeAttr("style");

    }

<?php 


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
        echo "<th style='width:".$p_ancho[$i]."'>".$p_titulos[$i]."</th>";
        $tot_registros++;
    }
    echo "
        </tr>
      </thead>
      <tbody>";    
}
?>
