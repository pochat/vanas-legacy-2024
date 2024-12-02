<?php  
  
	# Libreria de funciones
	require '../../lib/general.inc.php';  

	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe parametros
	$fg_error = 0;
	$clave=RecibeParametroNumerico('clave');
	$nb_cupon = RecibeParametroHTML('nb_cupon');
	$ds_code = RecibeParametroHTML('ds_code');
	$ds_descuento = RecibeParametroHTML('ds_descuento');
	$fe_start = RecibeParametroFecha('fe_start');
	$fe_end = RecibeParametroFecha('fe_end');

    $fe_start = "".ValidaFecha($fe_start)."";
    $fe_end = "".ValidaFecha($fe_end)."";
	 
	 
    
    
    
	 
?>


							<table class="table table-striped" id="table1" width="100%" style="border-width: 1px 1px 2px;border-style: solid;border-top-color: #CCC!important;">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th><i class="fa fa-fw fa-files-o text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(2154);?></th>
                                        <th><i class="fa fa-fw fa-calendar text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(2152);?></th><!---Date-->
                                        <th><i class="fa fa-fw fa-trophy text-muted hidden-md hidden-sm hidden-xs"></i><?php echo ObtenEtiqueta(2151); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
								
								
								<?php 
								 
									$Query="SELECT cl_plan_fame_cupon,nb_plan FROM c_plan_fame_cupon WHERE 1=1 ";
									
									$rs = EjecutaQuery($Query);
									$registros = CuentaRegistros($rs); 
									$cont=0;
									
									for($i=1;$row=RecuperaRegistro($rs);$i++){
									$cl_plan_fame_cupon=$row[0];
									$nb_plan=$row[1];
									
                                    $cont++;
                                    
									if($cl_plan_fame_cupon==1){
										$fg_pago_unico=1;
										#Verificamos el registro ultimo
										$Qury="SELECT DATE_FORMAT(fe_start, '%M %D, %Y')fe_start,DATE_FORMAT(fe_end, '%M %D, %Y') fe_end,ds_code,fl_cupon FROM c_cupones_b2c ";
										$Qury.="WHERE fg_pago_unico='1' AND fe_end BETWEEN '".$fe_start."' AND '".$fe_end."' ";
                                           
									}
									if($cl_plan_fame_cupon==2){
										$fg_plan_mensual=1;
										#Verificamos el registro ultimo
										$Qury="SELECT DATE_FORMAT(fe_start, '%M %D, %Y')fe_start,DATE_FORMAT(fe_end, '%M %D, %Y') fe_end,ds_code,fl_cupon FROM c_cupones_b2c ";
										$Qury.="WHERE fg_plan_mensual='1' AND fe_end BETWEEN '".$fe_start."' AND '".$fe_end."'  ";
                                        
									}
									
									if($cl_plan_fame_cupon==3){
										$fg_plan_anual=1;
										#Verificamos el registro ultimo
										$Qury="SELECT DATE_FORMAT(fe_start, '%M %D, %Y')fe_start,DATE_FORMAT(fe_end, '%M %D, %Y') fe_end,ds_code,fl_cupon FROM c_cupones_b2c ";
										$Qury.="WHERE fg_plan_anual='1' AND fe_end BETWEEN '".$fe_start."' AND '".$fe_end."' ";
									}
									    if($clave)
                                        $Qury.="AND fl_cupon=$clave ";
									
									
									$ro=RecuperaValor($Qury);
									$fe_startt=isset($ro[0])?$ro[0]:NULL;
									$fe_endd=isset($ro[1])?$ro[1]:NULL;
									$ds_code=str_texto(isset($ro[2])?$ro[2]:NULL);
                                    $fl_cupon=isset($ro[3])?$ro[3]:NULL;
									
									if($fe_endd){
									   
                                        if($clave){
                                            $check_disabled="";
                                            $cheked="checked";
                                            $tool="";
                                            $txt_color="";
                                            $fe_cha="<strong>".ObtenEtiqueta(2272).":</strong>   ".$fe_startt." <br/><small class=\'text-muted\'><i>".ObtenEtiqueta(2273).": ".$fe_endd."<i></i></i></small>";
                                            
                                            
                                            
                                        }else{
                                            $check_disabled="disabled";
                                            $cheked="checked";
                                            $tool = "rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(2303)."' data-html='true' ";
                                            $txt_color="txt-color-blue";
                                            $fe_cha="<a href='javascript:cupon(".$fl_cupon.");'><strong>".ObtenEtiqueta(2272).":</strong>   ".$fe_startt." </a><br/><small class=\'text-muted\'><i>".ObtenEtiqueta(2273).": ".$fe_endd."<i></i></i></small>";
                                            
                                            
                                            
                                        }
                                    }else{
                                        $check_disabled="";
                                        $fe_cha="";
                                        $cheked="";
                                        $tool="";
                                        $txt_color="";
                                    }
									
								?>
								
                                    <tr><td class="text-center"><div <?php echo $tool; ?>><label><input class='checkbox' name='cl_plan_<?php echo $i;?>' id='cl_plan_<?php echo $i;?>' value='<?php echo $cl_plan_fame_cupon;?>' type='checkbox' <?php echo $check_disabled;?> <?php echo $cheked;?> /><span></span></label></div></td>
                                        <td><p class="<?php echo $txt_color ?>" <?php echo $tool?> ><?php echo $nb_plan;?></p></td>
                                        <td><?php echo $fe_cha;?></td>
                                        <td><p class="<?php echo $txt_color ?>" <?php echo $tool?> ><?php echo $ds_code?></p></td>
                                    </tr>

                                <?php 
									}
								?>								


                                </tbody>

                            </table>
							
	<script>						
	 $(document).ready(function () {

		$('#table1').dataTable({
				  
		});
		$("[rel=tooltip]").tooltip();		
	});
	</script>
                  
							
							