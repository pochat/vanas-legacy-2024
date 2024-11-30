<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
 
  $fl_alumno=RecibeParametroNumerico('fl_alumno');
  $fg_calificado=RecibeParametroBinario('fg_calificado');
  $fl_programa=RecibeParametroNumerico('fl_pograma');  
  
  
 //$cl_sesion="c5e7d8dec215c0bc1bd63c6076fdf13a749ccb71775fd9cb964475b1b658069e";
 //$fl_programa=31;
 
  #Recupermaos su cl_sesion
  $Query="SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$fl_alumno ";
  $row=RecuperaValor($Query);
  $cl_sesion=str_uso_normal($row['cl_sesion']); 
  
 $Query="SELECT nb_programa FROM c_programa WHERE fl_programa=$fl_programa ";
 $row=RecuperaValor($Query);
 $nb_programa=str_texto($row['nb_programa']);

 #Recupermaos la fecha de calificacion para presentarlo en transcript.
 $Query="SELECT fe_modificacion FROM c_com_criterio_admin WHERE fg_com_final='1' AND cl_sesion='$cl_sesion' AND fl_programa=$fl_programa ";
 $row=RecuperaValor($Query);
 $fe_calificado=ObtenFechaFormatoDiaMesAnioHora($row[0]);
 
 
  #Se pinta modal.
  
  
?>

       

                    <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal" id="asignar">
                      Launch open modal
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                             <div class="modal-content">
                                    <div class="modal-header text-center">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<p style="font-size:20px;"><?php echo $nb_programa;?></p>
										<?php
                                                           
                                                              echo "<b>".ObtenEtiqueta(1678).":</b> ".$fe_calificado."<br >";
                                                        
                                                            ?>
                                         <!--<h4 class="modal-title text-center" id="myModalLabel" style="font-size:23px;"><i class=" fa fa-table" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(1776); ?></h4>--->
                                    </div>

                                    <div class="modal-body ">


                                        <div class="widget-body">
				                                

												
												
												
												
											<?php  echo PresentaRubric($fl_programa,$cl_sesion,'','','',1);  ?>	
												
												
												
												
												
												
												
												
												
												
												
												
												
												
												
												
												
		                                 </div><!--termina widget body--->
   
                                    </div><!---end body modal--->

                                    <div class="modal-footer text-center">
	                                    <button type="button" class="btn btn-primary" data-dismiss="modal"  style="font-size: 14px;border-radius: 10px; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-times-circle " aria-hidden="true"></i>&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                                                                                                                 
                                    </div>
                             </div>
                         </div>
                     </div>
                    <!--End Modal-->




<script>
  
    document.getElementById('asignar').click();//clic automatico que se ejuta y sale modal

</script>