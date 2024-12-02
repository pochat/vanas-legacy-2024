<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_PARTNER_SCHOOL, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
        $Query  = "SELECT I.fl_usuario_sp,I.ds_instituto,I.fl_pais,I.ds_codigo_pais,I.ds_codigo_area,I.no_telefono,I.fe_creacion,I.fg_export_moodle,I.fg_parent_authorization,I.cl_tipo_instituto,I.fl_instituto_rector,I.fg_export_follower,I.fg_menu_csf,I.ruta_sftp  ";
        $Query .= "FROM c_instituto I ";
        $Query .= "WHERE I.fl_instituto=$clave ";
        
          $row = RecuperaValor($Query);
          $fl_usuario=$row['fl_usuario_sp'];
          $nb_instituto = str_texto($row['ds_instituto']);
          $fl_pais = str_texto($row['fl_pais']);
          $ds_codigo_area = str_texto($row['ds_codigo_area']);
          $no_telefono=$row['no_telefono'];
          $fe_creacion=$row['fe_creacion'];
		  $fg_export_moodle=$row['fg_export_moodle'];
		  $fg_parent_authorization=$row['fg_parent_authorization'];
		  $cl_tipo_instituto=$row['cl_tipo_instituto'];
		  $fl_instituto_rector=$row['fl_instituto_rector'];
		  $fg_export_follower=$row['fg_export_follower'];
          $fg_menu_csf=$row['fg_menu_csf']; 
		  $ruta_sftp=$row['ruta_sftp'];
		  $Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto_rector ";
		  $ro=RecuperaValor($Query);
		  $nb_instituto_rector=!empty($ro['ds_instituto'])?$ro['ds_instituto']:NULL;
		  
		  
          #DAMOS FORMATO DIA,MES,AÑO
          $date=date_create($fe_creacion);
          $fe_registro=date_format($date,'F j , Y');
          #Recuperamos datos de admin del instituto.
          $Query="SELECT ds_nombres,ds_apaterno,ds_email, ds_alias FROM c_usuario WHERE fl_usuario=$fl_usuario ";
          $row=RecuperaValor($Query);
          $ds_nombres=$row['ds_nombres'];
          $ds_apaterno=$row['ds_apaterno'];
          $ds_email=$row['ds_email'];
          $ds_alias = $row['ds_alias'];

          
          #Recuperamos datos del plan fecha de expiracion , total de licencias, 
          $Query="SELECT no_licencias_disponibles,fg_plan,fe_periodo_final,no_total_licencias,fe_periodo_inicial,fg_pago_manual FROM k_current_plan WHERE fl_instituto=$clave ";
          $row=RecuperaValor($Query);
          $fg_plan=!empty($row['fg_plan'])?$row['fg_plan']:NULL;
          $no_licencia_disponibles=!empty($row['no_licencias_disponibles'])?$row['no_licencias_disponibles']:NULL;
          $fe_final_periodo=!empty($row['fe_periodo_final'])?$row['fe_periodo_final']:NULL;
          $no_total_licencias=!empty($row['no_total_licencias'])?$row['no_total_licencias']:NULL;
		  $fe_periodo_inicial=!empty($row['fe_periodo_inicial'])?$row['fe_periodo_inicial']:NULL;
		  $fg_pago_manual=!empty($row['fg_pago_manual'])?$row['fg_pago_manual']:NULL;
		  
          if($fg_plan=="A")
              $nb_plan=ObtenEtiqueta(1521);
          else
              $nb_plan=ObtenEtiqueta(1520); 
          
          
		  #Para saber si sigue activo/no.
		  #Obtenemos fecha actual :
		  $Query = "Select CURDATE() ";
		  $row = RecuperaValor($Query);
		  $fe_actual = str_texto($row[0]);
		  $fe_actual=strtotime('+0 day',strtotime($fe_actual));
		  $fe_actual= date('Y-m-d',$fe_actual);
		  
		  if($fe_final_periodo>=$fe_actual){
             $fg_vigente=1;  
		  }else{
			  
			  $fg_vigente=0;
		  }
		  
		  #DAMOS FORMATO DIA,MES,AÑO
          $date=date_create($fe_periodo_inicial);
          $fe_periodo_inicial_=date_format($date,'F j, Y');
          
		  
		  
          #DAMOS FORMATO DIA,MES,AÑO
          $date=date_create($fe_final_periodo);
          $fe_final_periodo=date_format($date,'F j, Y');
      
	      #DAMOS FORMATO DE FECHA.
		  $fe_periodo_expiracion_plan=strtotime('+0 day',strtotime($fe_final_periodo));
		  $fe_periodo_expiracion_plan= date('Y-m-d',$fe_periodo_expiracion_plan);
		
		  $date = date_create($fe_periodo_expiracion_plan);
		  $fe_renovacion=date_format($date,'F j, Y');
	
															
	      $fe_vigencia_plan=  $fe_periodo_inicial_." to ".$fe_renovacion;
		  
		  
          
          
          
    }
    else { // Alta, inicializa campos
     

      
      
      
    }

  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
 
  

  }
  
 
      
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_PARTNER_SCHOOL);
  
  echo "<script type='text/javascript' src='".PATH_JS."/frmCourses.js.php'></script>";
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
 ?>
       <!-- widget content -->
            <div class="widget-body">

         


                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#programs" data-toggle="tab"><i class="fa fa-fw fa-lg fa-info"></i><?php echo ObtenEtiqueta(928); ?></a>
                    </li>
				     <li>
                        <a href="#pricing" data-toggle="tab" id="princing1"><i class="fa fa-fw fa-lg fa-money"></i> Pricing</a>
                    </li>
					 <li>
                        <a href="#member" data-toggle="tab" id="members"><i class="fa fa-fw fa-lg fa-users"></i> <?php echo ObtenEtiqueta(1769); ?></a>
                    </li>
					<li>
                        <a href="#feature" data-toggle="tab" id="features"><i class="fa fa-fw fa-lg fa-cogs"></i> <?php echo ObtenEtiqueta(2341); ?></a>
                    </li>
					
					<?php if(($cl_tipo_instituto==2)&&(empty($fl_instituto_rector))){?>	 
					<li>
                        <a href="#institution" data-toggle="tab" id="institutions"><i class="fa fa-fw fa-lg fa-graduation-cap"></i> <?php echo ObtenEtiqueta(2547); ?></a>
                    </li>
				    <?php } ?>
					<li>
                        <a href="#award" data-toggle="tab" id="awards"><i class="fa fa-fw fa-lg fa-trophy"></i> <?php echo ObtenEtiqueta(2661); ?></a>
                    </li>

                </ul>

                
                  



                <div id="myTabContent1" class="tab-content padding-10 no-border">


                  <div class="tab-pane fade in active" id="programs">
							
                      <div class="row">
                          <div class="col-md-6">
							  <?php if(($cl_tipo_instituto==2)||(!empty($fl_instituto_rector))){ ?>
									<blockquote style='background:#f7f3f3;border-left: 5px solid #0092cd;'>
									  <span style="font-size: 26px; line-height: 1.5em;">
										<i class="fa fa-graduation-cap" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2524);?>
										<?php if($fl_instituto_rector){ 
									      echo $nb_instituto_rector;
										} ?>
										
									  </span>
									</blockquote>
								<?php } ?>



                          </div>

                          <div class="col-md-6">
                              <div class="row">
                                  <div class="col-md-10 text-right">

                                      <a href="javascript:void(0)" data-toggle="modal" data-target="#myModal"><i class="fa fa-search">&nbsp;</i>Contract Signed</a>
                                      <br />
                                      Date Signed: <?php echo $fe_registro; ?>
                                      <br />



                                  </div>
                                  <div class="col-md-2 ">
                                                           
                                                                <a href="<?php echo(isset($src_dowloand)?$src_dowloand:NULL) ; ?>" ><i class="fa fa-file-pdf-o text-success" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(346); ?>" >&nbsp;</i></a>
                                                            
                                  </div>

                              </div>

                          </div>

                      </div>
                      <br />
                        <hr />
                                        <!-------------Modal PREVIEW DEL CONTRATO--------------->
                              
                                            <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal" id="asignar">
                                              boton que se ejecua automaticamente
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                              <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                  <div class="modal-header text-center">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>

                                                    <h4 class="modal-title text-center" id="myModalLabel" style="font-size:23px;"><i class="fa fa-file" aria-hidden="true"></i>&nbsp;<?php echo $titulo_documento ?></h4>
      
                                                  </div>
                                                  <div class="modal-body" style="padding: 0px;">
                                                      

                                                      <div id="chat-body" class="chat-body custom-scroll" style="background:#fff;">
                                                      <?php 
                                                      
                                                     
                                                      
                                                      #se genera el cuerpo del documento del contrato
                                                      $ds_encabezado_contrato = genera_ContratoFame($clave, 1,102,$fl_usuario);
                                                      $ds_cuerpo_contrato = genera_ContratoFame($clave, 2,102,$fl_usuario);
                                                      $ds_pie_contrato = genera_ContratoFame($clave, 3,102,$fl_usuario);
                                                      
                                                      echo $ds_encabezado_contrato."<br/> ".$ds_cuerpo_contrato."<br/> ".$ds_pie_contrato;
                                                      
                                                      
                                                      
                                                      ?>
                                                      </div>
                                                      
          

                                                  </div>
                                                  <div class="modal-footer text-center">
         
                                                     <!--<button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrar_modal">Close</button>-->
                                                     <button type="button" class="btn btn-primary" data-dismiss="modal" id="cerrar_modal" > Close</button>
         

                                                  </div>
                                                </div>
                                              </div>
                                            </div>


                                        <!-----------end modal----------------->


                      <div class="row">
                          <div class="col-xs-12 col-sm-6">
                              <?php  
                                    Forma_Espacio();
                              
                                    $nb_admin=$ds_nombres." ".$ds_apaterno;
									Forma_CampoTexto(ObtenEtiqueta(1583), True, 'nb_admin', $nb_admin, 100, 30, !empty($nb_admin_err)?$nb_admin_err:NULL);
                              ?>
                          </div>
						   <div class="col-xs-12 col-sm-6">
                              <?php
                                    Forma_Espacio();
                                    Forma_CampoTexto(ObtenEtiqueta(870), True, 'ds_email', $ds_email, 100, 30, !empty($ds_email_err)?$ds_email_err:NULL);
                              ?>


                           </div>

                      </div>




                      <div class="row">
                            <div class="col-xs-12 col-sm-6" id="nombre_programa">


                                <?php
                               
                                  Forma_CampoTexto(ObtenEtiqueta(933), True, 'nb_instituto', $nb_instituto, 100, 30, !empty($nb_instituto_err)?$nb_instituto_err:NULL);

                                   Forma_CampoOculto('fl_usuario_sp',$fl_usuario);
                                ?>
                            </div>

                           <div class="col-xs-12 col-sm-6">
                               <?php  
                              
                               
                               ?>
                                           <!------>
                                            <style>
												.form-horizontal .form-group {
												     margin-left: 0px !important;
												}
												.smart-form {
													margin: -5px !important;
													margin-top: 0px !important;
													margin-bottom: -5px !important;
												}
												label{
												    max-width: 100% !important;
												}
												.input-group[class*=col-]{
													padding-left: 16px;
												}			
                                            </style>

                                                    
                                                                  <label class="col-sm-4 control-label text-align-right">
                                                                    <strong><?php echo ObtenEtiqueta(934)." :"; ?>
                                                                    </strong>
                                                                  </label>
                                                                                               
                                                                                                <div class="input-group col-sm-6">
                                                                                                       
                                                                                                        <span class="input-group-addon" id="codigo_pais" name="codigo_pais">&nbsp;</span>
                                                                                                     
                                                                                                         <?php
                                                                                                                                                    $Query = "SELECT CONCAT(ds_pais,' - ',cl_iso2), fl_pais FROM c_pais WHERE 1=1 and fg_activo='1' ";
                                                                                                                                                    Forma_CampoSelectBDSP3('', False, 'fl_pais', $Query, $fl_pais, '', true,'','left','col col-sm-12','col col-sm-12');
                                                                                                         ?>
                                                                                                </div>
                                                                                                
                
                                                                                       

                                            <!--------->


                               
                           </div>

                      </div>


                      <!------>
                      <div class="row">
                          <div class="col-xs-12 col-sm-6">
                              <?php
                                 Forma_CampoTexto(ObtenEtiqueta(952), False, 'ds_codigo_area', $ds_codigo_area, 10, 10, !empty($ds_codigo_area_err)?$ds_codigo_area_err:NULL);
                              ?>
                          </div>

                          <div class="col-md-6">
                           <?php
                            Forma_CampoTexto(ObtenEtiqueta(1129), True, 'ds_alias', $ds_alias, 50, 0, !empty($no_telefono_err)?$no_telefono_err:NULL, false,'',true,"onkeypress='return validarnspace(event);' onkeyup='ChangeAlias(".$fl_usuario.");'");
                            Forma_CampoOculto('ds_alias_bd', $ds_alias);
                           ?>
                          </div>

                      </div>

                      <div class="row">
                          <div class="col-md-6">
                               <?php
                                 Forma_CampoTexto(ObtenEtiqueta(953), True, 'no_telefono', $no_telefono, 20, 10, !empty($no_telefono_err)?$no_telefono_err:NULL);
                              ?>
                          </div>

                      </div>

                      <!----->



    
                  </div>
					<!---=======TAB PRINCING=======-->
                     <div class="tab-pane fade" id="pricing">

                             <div class="row">
                                    <div class="col-xs-12 col-sm-12">

                                         <table class="table table-bordered " width="100%" style="margin-bottom: 0px;" >
                                            <thead>
                                                <tr >
                                                <th class="text-center" colspan="3" style="font-size:16px;background-color:#fff;"><h1 style="font-weight: 200;color:#595656;"><?php echo ObtenEtiqueta(1512);  ?></h1> </th>
                                            
                                                </tr>
                                                <tr >
                                                <th class="text-center" width="30%" style="font-size:14px;background-color:#fff;border-width: 0px;"><?php echo ObtenEtiqueta(1501);  ?> <br>
                                                   <span style="font-size:12px;color: #A4A1A1;"> <?php echo ObtenEtiqueta(1504);  ?> </span>

                                                </th>
                                                <th class="text-center" width="30%" style="font-size:14px;background-color:#fff;border-width: 0px;"><?php echo str_uso_normal(ObtenEtiqueta(1502));  ?><br >
                                                    <span style="font-size:12px;color: #A4A1A1;"> <?php echo str_uso_normal(ObtenEtiqueta(1505));  ?> </span>
                                                </th>
                                                <th class="text-center" width="30%" style="font-size:14px;background-color:#fff;border-width: 0px;"><?php echo ObtenEtiqueta(1503);  ?><br >
                                                    <span style="font-size:12px;color: #A4A1A1;" > <?php echo ObtenEtiqueta(1506);  ?> </span>
                                                </th>
                                           
                                            </tr>
                                            </thead>

                                             <?php
                                             
                                             $no_usuario_adicional="0";
                                             Forma_CampoOculto('no_usuario_adicional',$no_usuario_adicional);
                                             Forma_CampoOculto('no_total_usuarios_actuales',$no_total_licencias);
                                             Forma_CampoOculto('fl_instituto',$clave);
											 echo"<input type='hidden' id='fg_agregar_licencias' value='' >
												  <input type='hidden' id='fg_reducir_licencias' value='' >
												  <input type='hidden' id='fl_nuevo_princing' value=''>
												  <input type='hidden' id='mn_total_sin_tax' value=''>												  
                                                  <input type='hidden' id='mn_cantidad_tax' value=''>
												  <input type='hidden' id='mn_porcentaje_tax' value=''>
												  <input type='hidden' id='mn_total_con_tax' value=''>
                                                 
												  ";
											 //Forma_CampoOculto('fg_agregar_licencias','');
											 //Forma_CampoOculto('fg_reducir_licencias','');
                                             
                                             
                                             
                                             
                                             
                                         
                                             
                                             
                                             
                                             ?>


 <style>  /*efecto para texto que aparece en el archivo presenta_lista_precios.php */
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

							.smart-form .checkbox i, .smart-form .radio i {
							top:13px;
							}
							.smart-form .radio input+i:after {    
								top: 3px;
								left: 3px;
							}
							
                            </style>



                                           <tbody id="presenta_lista_precios">
                                               <!---muestra lo que existe en presenta_lista-precios.php--->

                                            </tbody>
                                         </table>

                                    </div>

                                   

                             </div>


                            <div class="row">
                                 

                                <div class="col-xs-12 col-sm-12">
                                    <table class="table table-bordered"  width="100%" style="background:#f6f6f6;border: 1px solid #ededed;" >            
                                            <tbody>
                                                <tr>
                                                    <td colspan="3"  style="font-size:14px; color: #333;border:none;" >
                                                        
                                                            <div class="row">

																<?php 
																	if($fg_plan=='A'){
																		$checked_anio="checked";
																		$checked_mes="";
																	}
																	if($fg_plan=='M'){
																		$checked_anio="";
																		$checked_mes="checked";
																	}
															
															    ?>
															
                                                                <div class="col-xs-12 col-sm-12">
                                                                    <table class="table table-bordered" style="background:#f6f6f6; border:0px solid;" width="100%" >
                                                                        <tbody>
																		
																			<tr style="margin-top:1px;border:0px solid;">
                                                                                <td width="20%" class="text-right" style="border:none;">&nbsp; </td>
                                                                                <td width="20%" style="border:none;"> &nbsp;</td>
																				<td style="border:none;">
																				
																				<div class="smart-form">
																					<div class="inline-group">
																						<label class="radio">
																							<input type="radio" name="radio-inline" id="mes"   OnClick="PresentaListaPrecios();" <?php echo(!empty($checked_mes)?$checked_mes:NULL); ?>>
																							<i></i><?php echo str_uso_normal(ObtenEtiqueta(1502));?></label>
																						<label class="radio">
																							<input type="radio" name="radio-inline" id="anual"  OnClick="PresentaListaPrecios();" <?php echo(!empty($checked_anio)?$checked_anio:NULL); ?>>
																							<i></i><?php echo str_uso_normal(ObtenEtiqueta(1507));?></label>
																						
																					</div>
																				</div>
																				
																				
																				
																				</td>
                                                                                
                                                                            </tr>
																		
																		
																		
                                                                            <tr style="margin-top:1px;border:0px solid;">
                                                                                <td width="20%" class="text-right" style="border:none;"> Payment Plan: </td>
                                                                                <td width="20%" style="border:none;"> <?php echo $nb_plan ?></td>
                                                                                
                                                                            </tr>

                                                                            <tr style="margin-top:1px;border:none;">
                                                                                <td width="20%" class="text-right" style="border:none;">Current users:</td>
                                                                                <td width="20%" style="border:none;"> <?php echo $no_total_licencias; ?></td>
                                                                                
                                                                            </tr>

                                                                            <tr style="margin-top:1px;border:none;">
                                                                                <td width="20%" class="text-right" style="border:none;">Expiration date: </td>
                                                                               
                                                                                <td width="20%" style="border:none;"><?php echo $fe_final_periodo;  ?></td>
                                                                                
                                                                            </tr>
																			
																			
																			
																			<tr style="margin-top:1px;border:none;">
																				<td width="20%" class="text-right" style="border:none;">
																				
																				Added/Reduce Licences:
																				<br>
																				<p style="margin-top:33px;"><?php echo ObtenEtiqueta(1510).":"; ?></p>
																				</td>
																				
																				<td width="20%" style="border:none;">
																				
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
																				
																				
																				
																						<div class="form-group" style="    width: 60px;">
																							
																							<input class="form-control spinner-left"  id="spinner" name="spinner" value="<?php echo $no_total_licencias;?>" type="text">
																						</div>
																						
																						<br/>
																						<span id="tot_licencias"></span>
																				</td>
																				
																				<td style="border:none;" align="right">
																				   
																				   <div class="well well-sm  bg-color-darken txt-color-white no-border" style="width: 236px;background:#757474!important;">
																						<div class="fa-lg text-right" style="font-size:15px;">
																						   
																							Subtotal:
																							$<span id="subtotal_pa"> </span>&nbsp;<?php echo ObtenConfiguracion(113);?> 
																						</div>
																						<br>
																						<div class="fa-lg text-right" style="font-size:15px;">
																							Tax:
																							<span id="tax_pa"> </span>&nbsp;<?php echo ObtenConfiguracion(113);?> 
																						</div>
																						<br>
																						<div class="fa-lg text-right">
																							Total:
																							$<span id="total_pa"> </span>&nbsp;<?php echo ObtenConfiguracion(113);?> 
																						</div>

																					</div>
																				</td>
																			
																			</tr>
																			<!-- <tr style="margin-top:1px;border:none;">
                                                                                <td width="20%" class="text-right" style="border:none;"><?php echo ObtenEtiqueta(1510).":"; ?> 
																				
																				</td>
                                                                               
                                                                                <td width="20%" style="border:none;"><span id="tot_licencias"></span>
																				
																				
																				
																				
																				</td>
                                                                                
                                                                            </tr>-->
																			<?php
																			   
																			    if($fg_vigente){
																				$classv="success";
																				$background="#dbf1dc";
																				}else{
																				$classv="danger";
																				$background="#f1dbe0";
																				}
																				
																				
																				$etq=ObtenEtiqueta(2325);
																				$etq = str_replace("#fe_renovation#", $fe_vigencia_plan, $etq);
																				
																				
																				
																				
																			?>
																			
																			
																			
																			<tr style="margin-top:1px;border:none;">
                                                                                <td width="20%" class="text-center" style="border:none;" colspan="4"> 
																				<?php if($fg_pago_manual){ ?>
																				<p class="alert alert-<?php echo $classv?>"><i><?php echo $etq; ?></i></p>
																				<?php } ?>
																				
																				</td>
                                                                               
                                                                                
                                                                                
                                                                            </tr>
																			
																			
																			
																			
																			
																			  
																			
																			
																			
																			


                                                                        </tbody>


                                                                    </table>


                                                                </div>

                                                            </div>


                                                    </td>
                                                </tr>
												
												
												
												
												
												
												
												

                                            </tbody>
                                        </table>





                                </div>

                            </div>



							
							
							<div class="row">
							        <div class="col-xs-12 col-sm-2"></div>
									<div class="col-xs-12 col-sm-8">
										<?php 		echo Forma_CampoTexto(ObtenEtiqueta(2074). ' ' . ETQ_FMT_FECHA, True, 'fe_pago', !empty($fe_pago)?$fe_pago:NULL, 10, 0, !empty($fe_pago_err)?$fe_pago_err:NULL, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-4', 'col col-sm-5');  
														 Forma_Calendario('fe_pago');
										?>		
									</div>
									
							</div>		
							<br>
							<div class="row">
									<div class="col-xs-12 col-sm-2"></div>
									<div class="col-xs-12 col-sm-8">
																			<?php
										  $pagos = array( 'Cheque', 'Wire Transfer/Deposit','Cash');
										  $num = array('1', '2','3');
										  Forma_CampoSelect(ObtenEtiqueta(483), True, 'cl_metodo_pago', $pagos, $num, !empty($cl_metodo_pago)?$cl_metodo_pago:NULL, !empty($cl_metodo_pago_err)?$cl_metodo_pago_err:NULL, True);
										
										?>									
									</div>
							</div>
							<br>

							
							
							<div class="row">
									<div class="col-xs-12 col-sm-2"></div>
									<div class="col-xs-12 col-sm-8">
										<?php
										   Forma_CampoTexto('Reference Number', False, 'ds_cheque', !empty($ds_cheque)?$ds_cheque:NULL, 10, 10, !empty($ds_cheque_err)?$ds_cheque_err:NULL);
                              
										?>									
									</div>
							</div>


							<div class="row">
									<div class="col-xs-12 col-sm-2"></div>
									<div class="col-xs-12 col-sm-8">
										<?php
										   Forma_CampoTexto('Additional information (to be shown on the sales receipt).', False, 'ds_comentario', !empty($ds_comentario)?$ds_comentario:NULL, 100, 30, !empty($ds_comentario_err)?$ds_comentario_err:NULL);
                              
										?>									
									</div>
									
							
							</div>

							<div class="row">
									<div class="col-md-4">&nbsp;</div>
									<div class="col-md-4 text-center">
									
												<a href="javascript:void(0);"   onclick="RealizarPago();" id="btn_pagar" class="btn btn-default disabled" style="border-radius:10px;"><i class="fa fa-check-circle-o" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2320);?> </a>
												<span class="text-success hidden" id="txt_pago"><strong><i class="fa fa-check-circle-o" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2321); ?> </strong></span>
												<br/>	<img  class="hidden"  id="img_stripe" src="<?php echo PATH_SELF_IMG."/loading_stripe.gif" ?>" width="40">
											  							  
												<div id="div_pago"></div>
																				   
									
									
									
									</div>
							
							</div>

                      </div>
                    <!--========ENT TAB pRINCING========-->
					
					
				
					
				    <!---=========MEMBERS============--->
                    <div class="tab-pane fade" id="member">
						<div class="row">						
							<div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
								<!------------------------------->
								<div class="content">
									<section class="" id="widget-grid">
										<div class="row" style="margin-left: 0px; margin-right: 0px;">
											<div class="col-xs-12 col-sm-12" style="padding: 3px">
												 <!-- Widget ID (each widget will need unique ID)-->
												<div role="widget" class="jarviswidget" id="wid-id-list" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-collapsed="false">
													<header role="heading">
														<div role="menu" class="jarviswidget-ctrls">   
															  <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
														</div>
														<span class="widget-icon"> <i class="fa fa-book"></i> </span>
														<h2><?php echo ObtenEtiqueta(1773); ?> </h2>
														<div role="menu" class="widget-toolbar">
														   
														</div>
													</header>

													<!-- widget div-->
													<div role="content">
														<!-- widget edit box -->
														<div class="jarviswidget-editbox">
															<!-- This area used as dropdown edit box -->
															<input class="form-control" type="text">		
														</div>
														<!-- end widget edit box -->

														<!-- widget content -->
														 <div class="widget-body no-padding">
															<table id="example" class="display projects-table table table-striped table-bordered table-hover" cellspacing="0" width="100%">
																<thead>
																	<tr>
																		<th></th>
																		  <th><?php echo ObtenEtiqueta(1770); ?></th>
																		  <th><?php echo ObtenEtiqueta(1771); ?></th>
																		  <th><?php echo ObtenEtiqueta(1772); ?></th> 
																	</tr>
																</thead>                          
															</table>
														</div>
														<!-- end widget content -->
													</div>
													<!-- end widget div -->
												</div>
												<!-- end widget -->
											</div>
										</div>
									</section>
								</div>
										
							<!------------------------------->
							</div>
						</div>
					</div>				
					<!--==========END MEMBERS===========--->
					
					
					
					
					
					<div class="tab-pane fade" id="institution">
						<div class="row">
							<!---------------------inicia tbala de members------------------------->
							<div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
								<!------------------------------->
								<div class="content">
								    <section class="" id="widget-grid">
									     <div class="row" style="margin-left: 0px; margin-right: 0px;">
									        <div class="col-xs-12 col-sm-12" style="padding: 3px">
											    <!-- Widget ID (each widget will need unique ID)-->
											    <div role="widget" class="jarviswidget" id="wid-id-list" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-collapsed="false">
													<header role="heading">
														  <div role="menu" class="jarviswidget-ctrls">   
															  <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
														  </div>
														  <span class="widget-icon"> <i class="fa fa-book"></i> </span>
														  <h2>Institutions </h2>
														  <div role="menu" class="widget-toolbar">
														   
														  </div>
													</header>

												    <!-- widget div-->
											        <div role="content">
														<!-- widget edit box -->
													    <div class="jarviswidget-editbox">
															<!-- This area used as dropdown edit box -->
															<input class="form-control" type="text">	
														
														</div>
														<!-- end widget edit box -->

														<!-- widget content -->
														<div class="widget-body no-padding">

															<table id="example_inti" class="display projects-table table table-striped table-bordered table-hover" cellspacing="0" width="100%">
																<thead>
																    <tr>
																        <th></th>																 
																	     <th><?php echo ObtenEtiqueta(1770); ?></th>
																	     <th><?php echo ObtenEtiqueta(1772); ?></th>
															        </tr>
														        </thead>                          
													         </table>
												         </div>
												        <!-- end widget content -->
											        </div>
											        <!-- end widget div -->
											    </div>
											    <!-- end widget -->
									        </div>
									     </div>
								    </section>
								</div>
								
							    <!------------------------------->
						    </div>
					    </div>
					</div>
					
					
					
					
					
					<!---=========Feaures============--->
					
					<div class="tab-pane fade" id="feature">				
						<div class="row">
							<div class="col-md-6">
							
							 <?php Forma_CampoCheckBox(ObtenEtiqueta(2342),'fg_export_moodle', $fg_export_moodle,ObtenEtiqueta(2377),'',true,'','','col-sm-4','col-sm-8'); ?>
							</div>				
            
                            <div class="col-md-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(2651), False, 'ruta_sftp', !empty($ruta_sftp)?$ruta_sftp:NULL, 100, 30, !empty($ruta_sftp_err)?$ruta_sftp_err:NULL);
                                ?>				
                            </div>											
						</div>	
						<div class="row">
							<div class="col-md-6">
							
							 <?php Forma_CampoCheckBox(ObtenEtiqueta(2378),'fg_parent_authorization', $fg_parent_authorization,ObtenEtiqueta(2379),'',true,'','','col-sm-4','col-sm-8'); ?>
							</div>				

							
							
						</div>	
                        <div class="row">
                            <div class="col-md-6">
                                
                                <?php Forma_CampoCheckBox('Export Followers','fg_export_follower', $fg_export_follower,'','',true,'','','col-sm-4','col-sm-8'); ?>
							
                            </div>

                        </div>
						<div class="row">
                            <div class="col-md-6">
                                
                                <?php 
								if($cl_tipo_instituto==1)
									$cl_tipo_instituto=0;
								else
									$cl_tipo_instituto=1;
								Forma_CampoCheckBox(ObtenEtiqueta(2614),'cl_tipo_instituto', $cl_tipo_instituto,ObtenEtiqueta(2615),'',true,'','','col-sm-4','col-sm-8'); ?>
							
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                
                                <?php

								Forma_CampoCheckBox(ObtenEtiqueta(2649),'fg_menu_csf', $fg_menu_csf,ObtenEtiqueta(2650),'',true,'','','col-sm-4','col-sm-8'); ?>
                               						
                            </div>

                        </div>
						
						
						
						<div class="row">
                            <div class="col-md-6">
                                
								<label class="col-sm-4 control-label text-align-right">
								 <strong><?php echo ObtenEtiqueta(2616)." "; ?>
								 </strong>
							    </label>
								 <div class="input-group col-sm-6" style="padding-left: 40px;">
                                <?php 
								  $Query = "SELECT ds_instituto,fl_instituto FROM c_instituto WHERE cl_tipo_instituto='2' ";
                                  Forma_CampoSelectBDSP3('', False, 'fl_instituto_rector', $Query, $fl_instituto_rector, '', true,'','left','col-sm-4','col-sm-8');
                                                                                                         			
                                ?>
							    </div>
                            </div>

                        </div>
						
						

						
                    </div>    
					
					<!---=========End Features============--->
                    
                    
                    <!---Awards--->
                    <div class="tab-pane fade" id="award">
                        
                               
                                <?php include_once "dropoze_awards.php"; ?>	
                           
                    </div>
                    <!---end awards-->
                    				
             </div>      
               
      </div>
            
  
  
<script>
	function PresentaListaPrecios() {

        //alert('entro');

       // var no_usuario_adicional = document.getElementById('no_usuario_adicional').value;//no. actual que tiene el spinner
        var no_total_usuarios_actuales = document.getElementById('no_total_usuarios_actuales').value;
        var fl_instituto = document.getElementById('fl_instituto').value;
        var fg_tiene_plan = 1;
		var spiner=document.getElementById('spinner').value;
		
		if ($('#mes').is(':checked')) {
		    var fg_tiene_plan = 1;
		}
		if($('#anual').is(':checked')){
			 var fg_tiene_plan=2;
		
		}
		
		if(parseInt(no_total_usuarios_actuales)==parseInt(spiner)){
			var fg_accion=1;//add licences
			var no_usuario_adicional= 0;   
		}
		if(parseInt(no_total_usuarios_actuales)<parseInt(spiner)){	
	        var fg_accion=1;//add liecneces
	        var no_usuario_adicional = parseInt(spiner) - parseInt(no_total_usuarios_actuales);
	        
		}
		if(parseInt(no_total_usuarios_actuales)>parseInt(spiner)){
			var no_usuario_adicional=spiner;
			var fg_accion = 2;//reduce licencess
			$('#btn_pagar').removeClass('disabled');
		}

        $.ajax({
            type: 'POST',
            url: 'presenta_lista_precios.php',
            data: 'no_total_usuarios_actuales='+no_total_usuarios_actuales+
                  '&fg_tiene_plan='+fg_tiene_plan+
				  '&fg_accion='+fg_accion+
				  '&no_usuario_adicional='+no_usuario_adicional+
                  '&fl_instituto=' +fl_instituto ,
            async: true,
            success: function (html) {

                $('#presenta_lista_precios').html(html);

            }

        });

    }


    function MuestraCodigoPais( ) {

    


        $.ajax({
            type: 'POST',
            url: 'buscar_codigo_pais.php',
            data: 'fl_pais='+$('#fl_pais').val(),
            async: true,
            success: function (html) {

                $('#codigo_pais').html(html);

            }

        });


    }

    function HabilitaBotonPago(){
		
		var fe_pago= document.getElementById('fe_pago').value;
		var cl_metodo_pago=document.getElementById('cl_metodo_pago').value;
		
		
		if( (fe_pago.length > 0 ) && (cl_metodo_pago != 0 ) ){
			
			 $('#btn_pagar').removeClass('disabled');
			 
		}else{
			
			 $('#btn_pagar').addClass('disabled');
			
		}
	}	
	
	
   //Para realizar el pago.
   function RealizarPago(){
	   
	    $('#btn_pagar').addClass('hidden');
		$('#img_stripe').removeClass('hidden');
	   
	    var no_total_usuarios_actuales = document.getElementById('no_total_usuarios_actuales').value;
        var fl_instituto = document.getElementById('fl_instituto').value;
		var spiner=document.getElementById('spinner').value;
		
	    var fe_pago= document.getElementById('fe_pago').value;
		var cl_metodo_pago=document.getElementById('cl_metodo_pago').value;
		var ds_cheque= document.getElementById('ds_cheque').value;
		var ds_comentario=document.getElementById('ds_comentario').value;
		var fg_reducir_licencias=document.getElementById('fg_reducir_licencias').value;
		var fg_agregar_licencias=document.getElementById('fg_agregar_licencias').value;
		var mn_total_sin_tax=document.getElementById('mn_total_sin_tax').value;
		var mn_total_con_tax=document.getElementById('mn_total_con_tax').value;
		var mn_cantidad_tax=document.getElementById('mn_cantidad_tax').value;
		var mn_porcentaje_tax = document.getElementById('mn_porcentaje_tax').value;
		var fl_nuevo_princing = document.getElementById('fl_nuevo_princing').value;


		var fg_tipo_pago=1;//Autorenovacion default , depende las licencias que quiera en el spiner
		
	    if ($('#mes').is(':checked')) {
		    var fg_plan = 1;
		}
		if($('#anual').is(':checked')){
			 var fg_plan=2;
		}
		
		$.ajax({
			type:'POST',
			url:'generar_pago_instituto.php',
			data:'spiner='+spiner+
				 '&fl_instituto='+fl_instituto+
				 '&cl_metodo_pago='+cl_metodo_pago+
				 '&fe_pago='+fe_pago+
				 '&ds_cheque='+ds_cheque+
				 '&ds_comentario='+ds_comentario+
				 '&fg_plan='+fg_plan+
				 '&fg_tipo_pago='+fg_tipo_pago+
				 '&fg_agregar_licencias='+fg_agregar_licencias+
				 '&fg_reducir_licencias='+fg_reducir_licencias+
				 '&mn_total_sin_tax='+mn_total_sin_tax+
				 '&mn_total_con_tax='+mn_total_con_tax+
				 '&mn_cantidad_tax='+mn_cantidad_tax+
				 '&mn_porcentaje_tax='+mn_porcentaje_tax+
                 '&fl_nuevo_princing=' + fl_nuevo_princing,
			success: function(html){
				 $('#div_pago').html(html);

			
			}
			
	 	});
		
		
		
		
	   
   }
   
   



    $(document).ready(function () {

		$('#mes').change(function () {
			var spiner = document.getElementById('spinner').value;
			if (spiner >= 100) {

				$('#mes').prop('checked', false);
				$('#anual').prop('checked', true);
				
			}
			PresentaListaPrecios();
		});
	 
	 
	 
		//para verificar que se active btn de pagar
		$('#fe_pago').change(function () {	
		HabilitaBotonPago();	
		});
	
		$('#cl_metodo_pago').change(function (){
		HabilitaBotonPago();
		});
	 
	 
	 
	 
	 

        $('#spinner').val();
	
	    // Spinners
		$("#spinner").spinner().change(function () {
			
			var valor_actual=$(this).spinner('value');		
			
			if (valor_actual <= 0) {

                $('#spinner').val(0);
			}
			if(valor_actual>=100){
				
				$('#mes').prop('checked', false);
                $('#anual').prop('checked', true);
				
			}
			
			PresentaListaPrecios();
			
			
		});
		//cofigo neceario paa hacer funcionar el clickeo en el spiner
		$('.ui-spinner-button').click(function () {
            $(this).siblings('input').change(

                );
        });
		
		
		
      PresentaListaPrecios();//se ejecuta y muestra la lista de precios. 
  

        // Verifica el alias
        ChangeAlias(<?php echo $fl_usuario; ?>);
        ValidaInfo();
        MuestraCodigoPais();

        $('#fl_pais').change(function () {

            MuestraCodigoPais();
            ValidaInfo();
        });

        $('#nb_instituto').change(function () {
            ValidaInfo();
        });

        $('#no_telefono').change(function () {
            ValidaInfo();
        });


		document.getElementById("nb_admin").disabled = true;
        document.getElementById("ds_email").disabled = true;
      

    });




    function ValidaInfo( ) {

        var nb_instituto = document.getElementById("nb_instituto").value;
        var fl_pais = document.getElementById("fl_pais").value;
        var no_telefono = document.getElementById("no_telefono").value;
		var nb_admin = document.getElementById("nb_admin").value;
        var ds_email = document.getElementById("ds_email").value;
        var ds_alias = document.getElementById("ds_alias").value;
		if (nb_admin == '') {

            document.getElementById("nb_admin").style.borderColor = "red";
            document.getElementById("nb_admin").style.background = "#fff0f0";

        } else {
            document.getElementById("nb_admin").style.borderColor = "#739e73";
            document.getElementById("nb_admin").style.background = "#f0fff0";
        }
	   if (ds_email == '') {

            document.getElementById("ds_email").style.borderColor = "red";
            document.getElementById("ds_email").style.background = "#fff0f0";

        } else {
            document.getElementById("ds_email").style.borderColor = "#739e73";
            document.getElementById("ds_email").style.background = "#f0fff0";
        }



           if (nb_instituto == '') {
            
               document.getElementById("nb_instituto").style.borderColor = "red";
               document.getElementById("nb_instituto").style.background = "#fff0f0";

            } else{
               document.getElementById("nb_instituto").style.borderColor = "#739e73";
               document.getElementById("nb_instituto").style.background = "#f0fff0";
            }


           
            if ((fl_pais == '')||(fl_pais==0)) {
                document.getElementById("fl_pais").style.borderColor = "red";
                document.getElementById("fl_pais").style.background = "#fff0f0";

                document.getElementById("codigo_pais").style.borderColor = "red";
                document.getElementById("codigo_pais").style.background = "#fff0f0";

            } else {
                document.getElementById("fl_pais").style.borderColor = "#739e73";
                document.getElementById("fl_pais").style.background = "#f0fff0";


                document.getElementById("codigo_pais").style.borderColor = "#739e73";
                document.getElementById("codigo_pais").style.background = "#f0fff0";
            }
            if (no_telefono == '') {
                document.getElementById("no_telefono").style.borderColor = "red";
                document.getElementById("no_telefono").style.background = "#fff0f0";

            } else {
                document.getElementById("no_telefono").style.borderColor = "#739e73";
                document.getElementById("no_telefono").style.background = "#f0fff0";

            }
            
            if (ds_alias == '') {
                document.getElementById("ds_alias").style.borderColor = "red";
                document.getElementById("ds_alias").style.background = "#fff0f0";

            } else {
                document.getElementById("ds_alias").style.borderColor = "#739e73";
                document.getElementById("ds_alias").style.background = "#f0fff0";

            }
            

            if ((nb_instituto.length > 0) && (fl_pais != 0)&&(no_telefono.length>0)) {


                $("#aceptar").removeClass('disabled');



            } else {
                $("#aceptar").addClass('disabled');

            }
    





    }

</script>








<!--============DATA TABLE MEMBERS===========------>

<script type="text/javascript">

  

    $(document).ready(function () {

       


        /* DO NOT REMOVE : GLOBAL FUNCTIONS!
         *
         * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
         *
         * // activate tooltips
         * $("[rel=tooltip]").tooltip();
         *
         * // activate popovers
         * $("[rel=popover]").popover();
         *
         * // activate popovers with hover states
         * $("[rel=popover-hover]").popover({ trigger: "hover" });
         *
         * // activate inline charts
         * runAllCharts();
         *
         * // setup widgets
         * setup_widgets_desktop();
         *
         * // run form elements
         * runAllForms();
         *
         ********************************
         *
         * pageSetUp() is needed whenever you load a page.
         * It initializes and checks for all basic elements of the page
         * and makes rendering easier.
         *
         */

        pageSetUp();

        /*
         * ALL PAGE RELATED SCRIPTS CAN GO BELOW HERE
         * eg alert("my home function");
         * 
         * var pagefunction = function() {
         *   ...
         * }
         * loadScript("js/plugin/_PLUGIN_NAME_.js", pagefunction);
         * 
         * TO LOAD A SCRIPT:
         * var pagefunction = function (){ 
         *  loadScript(".../plugin.js", run_after_loaded);	
         * }
         * 
         * OR
         * 
         * loadScript(".../plugin.js", run_after_loaded);
         */
        /* Formatting function for row details - modify as you need */
        /*MJD 30082016 here
        the content of each register*/
        function format(d) {
            // `d` is the original data object for the row 



            return '<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed" width="100%">' +
                '<tr>' + 
                   
                   
                '</tr>' +
            '</table>';



        }


        // clears the variable if left blank
        var table = $('#example').on('processing.dt', function (e, settings, processing) {
            $('#datatable_fixed_column_processing').css('display', processing ? 'block' : 'none');
            $("#vanas_loader").show();
            if (processing == false)
                $("vanas_loader").hide();
            // alert(processing);
        }).DataTable({
            "ajax": { 
                "url":"member_member_list.php",
                "type": "POST",
                "dataType": "json",
                "data": function (d) {
                    //                                    d.extra_filters = {
                    //                                        'inicia_fe_pago': $("#FuaStartDate").val(),
                    //                                        'finaliza_fe_pago': $("#FuaEndDate").val()
                    //                                    };
                    d.extra_filters = {
                        'fl_instituto': $("#fl_instituto").val()

                    };
                }
            },
           
            //"serverSide": true,
            "processing": true,
            "bDestroy": true,
            "lengthMenu": [[10, 15, 50, -1], [10, 15, 50, "All"]],
            "iDisplayLength": 15,
            "columns": [
                {
                    "class": 'details-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": ''
                },
               
                { "data": "name" },
                { "data": "profile" },
                { "data": "estatus" },
               
               
              

               

            ],
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'>>" +
                          "t" +
                          "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "fnDrawCallback": function (oSettings) {
                var tot_registros_val = $("#example_info>span.text-primary").html();
                $("#example_info>span.text-primary  ").html(tot_registros_val + "<input id='tot_registros' value='" + tot_registros_val + "' type='hidden' /> " +
                "<input type='hidden' id='multiple' value='true'>");
            }
        });



        // Add event listener for opening and closing details
        $('#example tbody').on('click', 'td.details-control', function () {
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

    });


  

</script>


<!----======================-->





  
  
<script type="text/javascript">

  

    $(document).ready(function () {

       


        /* DO NOT REMOVE : GLOBAL FUNCTIONS!
         *
         * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
         *
         * // activate tooltips
         * $("[rel=tooltip]").tooltip();
         *
         * // activate popovers
         * $("[rel=popover]").popover();
         *
         * // activate popovers with hover states
         * $("[rel=popover-hover]").popover({ trigger: "hover" });
         *
         * // activate inline charts
         * runAllCharts();
         *
         * // setup widgets
         * setup_widgets_desktop();
         *
         * // run form elements
         * runAllForms();
         *
         ********************************
         *
         * pageSetUp() is needed whenever you load a page.
         * It initializes and checks for all basic elements of the page
         * and makes rendering easier.
         *
         */

        //pageSetUp();

        /*
         * ALL PAGE RELATED SCRIPTS CAN GO BELOW HERE
         * eg alert("my home function");
         * 
         * var pagefunction = function() {
         *   ...
         * }
         * loadScript("js/plugin/_PLUGIN_NAME_.js", pagefunction);
         * 
         * TO LOAD A SCRIPT:
         * var pagefunction = function (){ 
         *  loadScript(".../plugin.js", run_after_loaded);	
         * }
         * 
         * OR
         * 
         * loadScript(".../plugin.js", run_after_loaded);
         */
        /* Formatting function for row details - modify as you need */
        /*MJD 30082016 here
        the content of each register*/
        function format(m) {
            // `d` is the original data object for the row 



            return '<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed" width="100%">' +
                '<tr>' + 
                   
                   
                '</tr>' +
            '</table>';



        }


        // clears the variable if left blank
        var table = $('#example_inti').on('processing.dt', function (e, settings, processing) {
            $('#datatable_fixed_column_processing').css('display', processing ? 'block' : 'none');
            $("#vanas_loader").show();
            if (processing == false)
                $("vanas_loader").hide();
            // alert(processing);
        }).DataTable({
            "ajax": { 
                "url":"institution_list.php",
                "type": "POST",
                "dataType": "json",
                "data": function (m) {
                    //                                    d.extra_filters = {
                    //                                        'inicia_fe_pago': $("#FuaStartDate").val(),
                    //                                        'finaliza_fe_pago': $("#FuaEndDate").val()
                    //                                    };
                    m.extra_filters = {
                        'fl_instituto': $("#fl_instituto").val()

                    };
                }
            },
           
            //"serverSide": true,
            "processing": true,
            "bDestroy": true,
            "lengthMenu": [[10, 15, 50, -1], [10, 15, 50, "All"]],
            "iDisplayLength": 15,
            "columns": [
                {
                    "class": 'details-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": ''
                },
               
                { "data": "name" },
              
                { "data": "estatus" },
               
               
              

               

            ],
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'>>" +
                          "t" +
                          "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "fnDrawCallback": function (oSettings) {
                var tot_registros_val = $("#example_info>span.text-primary").html();
                $("#example_inti_info>span.text-primary  ").html(tot_registros_val + "<input id='tot_registros' value='" + tot_registros_val + "' type='hidden' /> " +
                "<input type='hidden' id='multiple' value='true'>");
            }
        });



        // Add event listener for opening and closing details
        $('#example_inti tbody').on('click', 'td.details-control', function () {
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

    });
  
  
  

</script>


<!----======================-->















  
  <?php

 
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_PARTNER_SCHOOL, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_TerminaM($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  ?>

<script src="<?php echo PATH_LIB; ?>/fame/dropzone.min.js">
</script>	


  <?php
  //function Forma_CampoSelectBDM($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $etq_align='right',$col_sm_etq = 'col col-md-4', $col_sm_cam = 'col col-md-4') {
      
  //    $ds_clase = 'form-control';
  //    if(!empty($p_error)) {
  //        $ds_error = ObtenMensaje($p_error);
  //        $ds_clase_err = 'has-error';
  //    }
  //    else {
  //        $ds_error = "";
  //        $ds_error_err = "";
  //    }
      
      
      
      
  //    echo "
  //<div class='form-group smart-form $ds_clase_err'>
  //  <label class='$col_sm_etq control-label text-align-$etq_align'>
  //    <strong>";
  //    if($p_requerido)  echo "* ";
  //    if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
  //    echo "
  //    </strong>
  //  </label>
  //  <div class='$col_sm_cam' style='padding-right: 0px;' ><label class='select'>";
  //    CampoSelectBD($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
  //    echo "<i></i>";
  //    if(!empty($p_error))
  //        echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
  //    echo "
  //  </label></div>     
  //</div>";
  //}
  
  
  
  //function Forma_CampoSelectBDM2($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $etq_align='right',$col_sm_etq = 'col col-md-4', $col_sm_cam = 'col col-md-4') {
      
  //    $ds_clase = 'form-control';
  //    if(!empty($p_error)) {
  //        $ds_error = ObtenMensaje($p_error);
  //        $ds_clase_err = 'has-error';
  //    }
  //    else {
  //        $ds_error = "";
  //        $ds_error_err = "";
  //    }
     
      
     
      
  //    echo "
  //<div class='form-group smart-form $ds_clase_err'>
  
  
  //  <label class='$col_sm_etq control-label text-align-$etq_align'>
  //    <strong>";
  //    if($p_requerido)  echo "* ";
  //    if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
  //    echo "
  //    </strong>
  //  </label>
  //  <div class='$col_sm_cam' style='padding-right: 0px;' ><label class='select'>";
  //    CampoSelectBD($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
  //    echo "<i></i>";
  //    if(!empty($p_error))
  //        echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
  //    echo "
  //  </label></div>     
  //</div>";
  //}
  
  
  
  
  
  
  function Forma_TerminaM($p_guardar=False, $p_url_cancelar='', $p_etq_aceptar=ETQ_SALVAR, $p_etq_cancelar=ETQ_CANCELAR, $p_click_cancelar='') {
      
     
      # Destino para el boton Cancelar
      if(empty($p_click_cancelar)) {
          if(empty($p_url_cancelar)) {
              $nb_programa = ObtenProgramaBase( );
              $click_cancelar = "parent.location='$nb_programa'";
          }
          else
              $click_cancelar = "parent.location='$p_url_cancelar'";
      }
      else
          $click_cancelar = $p_click_cancelar;
      
      echo "
        <footer>";

      echo "
          <div style='width: 228px; right: 0px; display: block; padding:0px 50px 10px 0px;' outline='0' class='ui-widget ui-chatbox'>";
      if($p_guardar)
          echo "<a class='btn btn-primary btn-circle btn-xl disabled' title='".$p_etq_aceptar."' name='aceptar' id='aceptar' onClick='javascript:document.datos.submit();'><i class='fa fa-save'></i></a>&nbsp;";
      echo "  <a class='btn btn-default btn-circle btn-xl' title='".$p_etq_cancelar."' name='aceptar' id='cancelar' onClick=\"$click_cancelar\"><i class='fa fa-times'></i></a>
          </div>          
        </footer>
      </form>
    </div>
  </div>";
      
  }
  
  
  
  
  
  
  function Forma_CampoSelectBDSP3($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $etq_align='right',$col_sm_etq = 'col col-md-4', $col_sm_cam = 'col col-md-4') {
      
      $ds_clase = 'form-control';
      if(!empty($p_error)) {
          $ds_error = ObtenMensaje($p_error);
          $ds_clase_err = 'has-error';
      }
      else {
          $ds_error = "";
          $ds_error_err = "";
          $ds_clase_err = "";
      }

   
      echo "
  <div class='form-group smart-form $ds_clase_err' >
    
      ";
      if($p_requerido)  echo "* ";
      if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "";
      echo "
      
    
    <div id='borderes3' class='border3' ><label class='select' required/>";
      CampoSelectBDSP($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
      echo "<i></i>";
      if(!empty($p_error))
          echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
      echo "</label>
    </div>     
  </div>";
  }
  
  function CampoSelectBDSP($p_nombre, $p_query, $p_actual, $p_clase='css_input', $p_seleccionar=False, $p_script='') {
      

      
      echo "<select id='$p_nombre' name='$p_nombre' class='select2  required/'  ";
      if(!empty($p_script)) echo " $p_script";
      echo " >\n";
      if($p_seleccionar)
          echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
      $rs = EjecutaQuery($p_query);
      while($row = RecuperaRegistro($rs)) {
          echo "<option value=\"$row[1]\"";
          if($p_actual == $row[1])
              echo " selected";
          
          # Determina si se debe elegir un valor por traduccion
          $etq_campo = DecodificaEscogeIdiomaBD($row[0]);
          echo ">$etq_campo</option>\n";
      }
      echo "</select>";
  }
  
  
?>
