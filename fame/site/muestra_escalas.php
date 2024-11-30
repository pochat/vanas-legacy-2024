<?php 
  # Libreria de funciones	
  require("../lib/self_general.php"); 
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  # Obtenemos el perfil
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);

  $fg_accion=RecibeParametroNumerico('fg_action');
  
  if($fg_accion==100){
      $_POST[''];
      $cl_calificacion=RecibeParametroHTML('cl_calificacion');
      $ds_calificacion=RecibeParametroHTML('ds_calificacion');
      $ds_calificacion_esp=RecibeParametroHTML('ds_calificacion_esp');
      $ds_calificacion_fra=RecibeParametroHTML('ds_calificacion_fra');
      $fg_aprobado=RecibeParametroBinario('fg_aprobado');
      $no_equivalencia=RecibeParametroHTML('no_equivalencia');
      $no_min=RecibeParametroNumerico('no_min');
      $no_max=RecibeParametroNumerico('no_max');
      

      $Query ='INSERT INTO c_calificacion_criterio(cl_calificacion,ds_calificacion,ds_calificacion_esp,ds_calificacion_fra,fg_aprobado,no_equivalencia,no_min,no_max,fl_instituto) ';
      $Query.='VALUES("'.$cl_calificacion.'","'.$ds_calificacion.'","'.$ds_calificacion_esp.'","'.$ds_calificacion_fra.'","'.$fg_aprobado.'",'.$no_equivalencia.','.$no_min.','.$no_max.','.$fl_instituto.')';
      EjecutaInsert($Query);
?>	  
	  
	  <script>
	   $.smallBox({
            title : "<i class=\"fa fa-check\" aria-hidden=\"true\"></i> <?php echo ObtenEtiqueta(1645); ?>",
            //content : "Lorem ipsum dolor sit amet, test consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
            color : "#739E73",
            timeout: 4000,
            iconSmall : "fa fa-check ",
            //number : "2"
          });
	  </script>
<?php 	  
	  
  }

  if($fg_accion==101){

      $fl_calificacion_criterio=RecibeParametroNumerico('fl_calificacion_criterio');

      EjecutaQuery("DELETE FROM c_calificacion_criterio where fl_calificacion_criterio=$fl_calificacion_criterio ");

  }


?>


		
 <div class="tabla table-responsive">
            <table class="table table-hover" id="tabla_clientes2" name="tabla_clientes2" >             
                  <thead>
                    <tr>
                      <th><?php echo ObtenEtiqueta(2618);?></th>
                      <th><?php echo ObtenEtiqueta(2619)." (English)";?></th>
                         <th><?php echo ObtenEtiqueta(2619)." (Spanish)";?></th>
                         <th><?php echo ObtenEtiqueta(2619)." (French)";?></th>
                      <th><?php echo ObtenEtiqueta(2620);?></th>				  
					  <th><?php echo ObtenEtiqueta(2621);?></th>	
					  <th><?php echo ObtenEtiqueta(2622);?></th>
					  <th><?php echo ObtenEtiqueta(2623);?></th>	
					  <th></th>	 
                    </tr>
                  </thead>
				  
				  <tbody>
				  
					<?php 
					 $Query  = " SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,ds_calificacion_esp,ds_calificacion_fra,fg_aprobado,no_equivalencia,no_min,no_max ";
					 $Query .= "FROM c_calificacion_criterio WHERE fl_instituto=$fl_instituto ORDER BY no_equivalencia DESC ";
					 $rs = EjecutaQuery($Query);
					 
					 for($i=1;$row=RecuperaRegistro($rs);$i++){
						 $fl_calificacion_criterio=$row['fl_calificacion_criterio'];
						 $cl_calificacion=$row['cl_calificacion'];
						 $ds_calificacion=$row['ds_calificacion'];
						 $ds_calificacion_esp=$row['ds_calificacion_esp'];
						 $ds_calificacion_fra=$row['ds_calificacion_fra'];
						 $fg_aprobado=$row['fg_aprobado'];
						 $no_equivalencia=$row['no_equivalencia'];
						 $no_min=$row['no_min'];
						 $no_max=$row['no_max'];
					   
					     $valor_order=number_format($no_equivalencia);
						  
						  if($fg_aprobado=='1') {
                              $color = "#07bb2e";
							$etq=ObtenEtiqueta(16);
						  }else{
                              $color ="#e80404";
							$etq=ObtenEtiqueta(17);
						  }
      
					?>
				  
					<tr>					
					 <td><a href="form-x-editable.html#" id="grade_<?php echo $i;?>" data-type="text" data-pk="<?php echo $fl_calificacion_criterio;?>" data-original-title="&nbsp;"><?php echo $cl_calificacion;?></a>
							
							<?php 
							echo"<script>						
									$('#grade_$i').editable({
										url: 'site/update_grading.php',
										type: 'text',
										pk: $fl_calificacion_criterio,
										name: 'cl_calificacion',
										title: 'Enter username'
									});
								</script>";
							?>
							
					 
					 </td>                     
				     
					 <td><a href="form-x-editable.html#" id="descripction_<?php echo $i;?>" data-type="text" data-pk="<?php echo $fl_calificacion_criterio;?>" data-original-title="&nbsp;"><?php echo $ds_calificacion;?></a>
							<?php 
								echo"<script>						
										$('#descripction_$i').editable({
											url: 'site/update_grading.php',
											type: 'text',
											pk: $fl_calificacion_criterio,
											name: 'ds_calificacion',
											title: 'Enter username'
										});
									</script>";
                            ?>
					 </td>


                        <td><a href="form-x-editable.html#" id="descripction_esp_<?php echo $i;?>" data-type="text" data-pk="<?php echo $fl_calificacion_criterio;?>" data-original-title="&nbsp;"><?php echo $ds_calificacion_esp;?></a>
							<?php 
                         echo"<script>						
										$('#descripction_esp_$i').editable({
											url: 'site/update_grading.php',
											type: 'text',
											pk: $fl_calificacion_criterio,
											name: 'ds_calificacion_esp',
											title: 'Enter username'
										});
									</script>";
                            ?>
					 </td>


                        <td><a href="form-x-editable.html#" id="descripction_fra_<?php echo $i;?>" data-type="text" data-pk="<?php echo $fl_calificacion_criterio;?>" data-original-title="&nbsp;"><?php echo $ds_calificacion_fra;?></a>
							<?php 
                         echo"<script>						
										$('#descripction_fra_$i').editable({
											url: 'site/update_grading.php',
											type: 'text',
											pk: $fl_calificacion_criterio,
											name: 'ds_calificacion_fra',
											title: 'Enter username'
										});
									</script>";
                            ?>
					 </td>




						     <td><a href="form-x-editable.html#" id="aproving_<?php echo $i;?>"  data-value="<?php echo $fg_aprobado;?>" data-name="fg_aprobado" data-type="select2" data-pk="<?php echo $fl_calificacion_criterio;?>" data-original-title="&nbsp;" style="color:<?php echo $color;?> "><?php echo $etq;?></a>
									 <?php 
										echo"<script>

												var options = [];
												$.each({	
													 '0': '".ObtenEtiqueta(17)."',
													 '1': '".ObtenEtiqueta(16)."'
													}, function (k, v) {
														options.push({
															id: k,
															text: v,
                                                            name: 'fg_aprobado',
															pk: $fl_calificacion_criterio
														});
													});
													$('#aproving_$i').editable({
														url: 'site/update_grading.php',
														source: options,
														select2: {
															width: 200
														}
													});
											</script>";
										?>
							 </td>						  
						     <td>
									<a href="form-x-editable.html#" id="no_min_<?php echo $i;?>" data-type="text" data-pk="<?php echo $fl_calificacion_criterio;?>" data-original-title="&nbsp;"><?php echo $no_min;?></a>
									 <?php 
										echo"<script>						
												$('#no_min_$i').editable({
													url: 'site/update_grading.php',
													type: 'text',
													pk: $fl_calificacion_criterio,
													name: 'no_min',
													title: 'Enter username'
												});
											</script>";
										?>
							 
							 </td>
							 <td>
									<a href="form-x-editable.html#" id="no_max_<?php echo $i;?>" data-type="text" data-pk="<?php echo $fl_calificacion_criterio;?>" data-original-title="&nbsp;"><?php echo $no_max;?></a>
									 <?php 
										echo"<script>						
												$('#no_max_$i').editable({
													url: 'site/update_grading.php',
													type: 'text',
													pk: $fl_calificacion_criterio,
													name: 'no_max',
													title: 'Enter username'
												});
											</script>";
										?>
							 
							 </td>
							 <td>
									<a href="form-x-editable.html#" id="equivalence_<?php echo $i;?>" data-type="text" data-pk="<?php echo $fl_calificacion_criterio;?>" data-original-title="&nbsp;"><?php echo $no_equivalencia;?></a>
									 <?php 
										echo"<script>						
												$('#equivalence_$i').editable({
													url: '/post',
													type: 'text',
													pk: $fl_calificacion_criterio,
													name: 'no_equivalencia',
													title: 'Enter username'
												});
											</script>";
                                     ?>
							 
							 </td>
							 <td><a href='javascript:DeleteGrading(<?php echo $fl_calificacion_criterio;?>)' class="btn btn-xs btn-default"> <i class="fa  fa-trash-o"></i></a><span class='hidden'><?php echo $valor_order;?></span></td>
					</tr>
					
					
					<?php 
					
					}
					?>
				  
				  </tbody>
				  
			</table>
</div>

<script>
    $(document).ready(function(){
        $('#tabla_clientes2').DataTable({
			"order": [[ 8, "DESC" ]],
        });
	});	
</script>

