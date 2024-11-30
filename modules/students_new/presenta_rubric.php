<?php 
# Libreria de funciones
require("../common/lib/cam_general.inc.php");
 
$fl_leccion=RecibeParametroNumerico('fl_leccion');
$fl_programa=RecibeParametroNumerico('fl_programa');
$fl_alumno=RecibeParametroNumerico('fl_alumno');
$fl_grupo=RecibeParametroNumerico('fl_grupo');
$fl_semana=RecibeParametroNumerico('fl_semana');  
    
#Recupermaos el nombre de la rubric.
$Qury="SELECT ds_titulo FROM c_leccion WHERE fl_leccion=$fl_leccion ";
$ro=RecuperaValor($Qury);
$nb_rubric=str_texto($ro['ds_titulo']);





?>

 <style>
.font {
color:#333 !important;
font: 18x Arial !important;
font-weight:100 !important;
}


.chart {
    /* height: 220px; */
    margin: auto !important;
}
.easyPieChart {
    position: relative;
    text-align: center !important;
}
.easyPieChart canvas {
    position: absolute;
    top: 0;
    left: 0;
}


.border{
		border: 2px solid  #3194DA !important;
		
}

</style>



             <!-- Preview Rubric -->
                    <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal" id="asignar">
                     Launch demo modal
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                         <div class="modal-dialog" role="document" style="width:90%;">
                             <div class="modal-content">
                                <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title text-center" id="myModalLabel" style="font-size:18px;"><i class="fa fa-calendar" aria-hidden="true"></i><b>&nbsp;<?php echo "Rubric: </b>".$nb_rubric; ?></h4>
                                </div>

                                <div class="modal-body text-center">
                 
																											 

                                       <?php  echo PresentaRubric($fl_leccion,$fl_alumno,$fl_grupo,$fl_semana,'','',1);  ?> 



                                </div>
                                <div class="modal-footer text-center">
	                                <button type="button" class="btn btn-primary" data-dismiss="modal"  style="font-size: 14px;border-radius: 10px; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-check-circle"></i>&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                                                                                                                 
                                </div>
                            </div>
                        </div>
                    </div>



<script>

     document.getElementById('asignar').click();//clic automatico que se ejuta y sale modal

</script>


<?php
/*
function PresentaRubric($fl_leccion,$fl_alumno){

    
    
    #Verificamos si ya esta calificado por el teacher.(si tiene fl_promedio semna,quees llave que hace refrencia c_calificacion_sp)
    $Query="SELECT fl_promedio_semana FROM k_entrega_semanal_sp WHERE fl_alumno=$fl_alumno AND fl_leccion=$fl_leccion ";
    $row=RecuperaValor($Query);
    
    $fg_calificado=$row['fl_promedio_semana'];
    //$fg_calificado=1;
   if(empty($fg_calificado)){
   
   
        #Recuperamos todos los criterios
	    $Query="SELECT fl_criterio, no_valor FROM k_criterio_programa WHERE fl_programa = $fl_leccion ORDER BY no_orden ASC	";
        $rs_prin = EjecutaQuery($Query);
        $registros = CuentaRegistros($rs_prin);
		for($i_prin=1;$row_prin=RecuperaRegistro($rs_prin);$i_prin++) {
		
		$fl_criterio=$row_prin['fl_criterio'];
        $no_valor_criterio = $row_prin['no_valor'];
		
		$rs_nb_crit = RecuperaValor("SELECT nb_criterio FROM c_criterio WHERE fl_criterio = $fl_criterio");
        $nb_criterio = str_texto($rs_nb_crit[0]);
		
		
        
        echo "<div class='row' style='height:auto; padding-left:75px;'>";
        echo "   <div class='col-md-1' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'>				
      
                  <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
                    Criterion
                  </div>
                  <br/>
                  <div class='panel panel-default' style='height:338px;'>
                    <div  class='panel-body text-center' style='writing-mode: vertical-lr; transform: rotate(180deg); font-size:18px; font-weight:bold; padding: 15px 35px 60px 50px;'>$nb_criterio</div>
                  </div>

          </div>";
        
        
        $Query="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion_criterio ";
        $Query.="	WHERE 1=1 ORDER BY fl_calificacion_criterio DESC ";
        $rs = EjecutaQuery($Query);
        for($i=1;$row=RecuperaRegistro($rs);$i++) {
        $fl_calificacion_criterio=$row['fl_calificacion_criterio'];
        $cl_calificacion=$row['cl_calificacion'];
        $ds_calificacion=$row['ds_calificacion'];
        $fg_aprobado=$row['fg_aprobado'];
        $no_equivalencia=$row['no_equivalencia'];
        $no_min= number_format($row['no_min']);
        $no_max=number_format($row['no_max']);
        
        if($no_max==0)
            $ds_equivalencia="No Uploaded";
        else
            $ds_equivalencia=$no_min."% - ".$no_max."%"." ($cl_calificacion)";
        
        #Recupermaos la descripcion que tiene actualmente.
        $Query_c="SELECT ds_descripcion,fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
        $row_c=RecuperaValor($Query_c);
        $ds_desc=str_texto($row_c[0]);
        $fl_criterio_fame=$row_c[1];
        
        #Recuperamos las imagenes por calificacion
        $Query_img = "SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame ";
        $row_img = RecuperaValor($Query_img);
        $nb_archivo_criterio = $row_img[0];
        $src_img="../../images/rubrics/".$nb_archivo_criterio;
        
        $contador ++;
        
        if(!empty($nb_archivo_criterio)){
            $icono = "<a class='zoomimg' href='#'> 
            <i class='fa fa-file-picture-o'></i>
            <span style='left:-300px;'>
              <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-bottom: -530px;'>
                <div class='modal-content' style='width:500px;height:500px;'>
                  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
                    <img class='superbox-current-img' src='$src_img' style='width:494px;height:490px;'>
                  </div>
                </div>
              </div>
            </span>
          </a> ";
        }else{
            $icono = "";
        }
        
       
        
        
        echo"
        
         <div class='col-md-2' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'>				
      
          <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
             $ds_calificacion &nbsp;&nbsp;$icono
          </div>
          <br/>
          <div class='panel panel-default'>
            <div class='panel-body text-center'>
              <span  style='color:#8FCAE5;font-size:15px; '>$ds_equivalencia </span>  <p>&nbsp;</p>
           
              
              
              
             
              
                <div class='knobs-demo'>
                  <div>
                    <input class='knob$contador font'     value='$no_max'  disabled/>
                  </div>
                </div>
            
                
                
                        
                        <div class='form-group text-left' style='padding-left:5px; padding-right:5px;'>
                           <div id='desc$contador'></div>
                           <hr>
                    
                            <div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
                              <small class='text-muted'><i>$ds_desc</i></small>              
                            </div>
                  
                        </div>
            </div>
          </div>

        </div>
        
        
        
        ";
        
      
        
        
        
        echo"<script>
            $(document).ready(function () {
             document.getElementById('desc$contador').disabled = true;//tofos al cargar el document estan desaibiltados
              $('#char$contador').addClass('hidden');//botones desabilitados
            //<!--propiedades del input knob -->
                $('.knob$contador').knob({
                  'width':100,
                  'height':100,
                  'angleArc':360,
                  'thickness':0.16,
                  'cursor':false,
                  'readOnly':true,
                  'angleOffset':50,
                  'fgColor':'#92D099'
                 
                });
        
              });	
        </script> ";        
        
		
        }#end 2do query
        echo "   <div class='col-md-1' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'></div>";
        echo "</div>";
        echo"<br/>";
   
   
     }#end primer query.

    
    
    
   }else{
        ###############Muestr rubric calificada
       
       
       
       
       #Muestr rubric calificada
       
       
       
       #Recuperamos todos los criterios
       $Query="SELECT fl_criterio, no_valor FROM k_criterio_programa WHERE fl_programa = $fl_leccion ORDER BY no_orden ASC	";
       $rs_prin = EjecutaQuery($Query);
       $registros = CuentaRegistros($rs_prin);
       $cont1=0;
       for($i_prin=1;$row_prin=RecuperaRegistro($rs_prin);$i_prin++) {
           
           $fl_criterio=$row_prin['fl_criterio'];
           $no_valor_criterio = $row_prin['no_valor'];
           
           $rs_nb_crit = RecuperaValor("SELECT nb_criterio FROM c_criterio WHERE fl_criterio = $fl_criterio");
           $nb_criterio = str_texto($rs_nb_crit[0]);
           
           $cont1 ++;
           
           echo "<div class='row' >";
           echo "   <div class='col-md-1' style='padding-right: 0px;'>				
                     <div class='col-md-12' style='padding-left: 1px;' >
                          <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
                            Criterion
                          </div>
                          <br/>
                          <div class='panel panel-default' style='height:365px;'>
                            <div  class='panel-body text-center' style='writing-mode: vertical-lr; transform: rotate(180deg); font-size:18px; font-weight:bold; padding: 15px 26px 90px 50px;'>$nb_criterio</div>
                          </div>
                      </div>

                 </div>";
           
           
           echo"   <div class='col-md-11' style='padding-left: 1px;'>";
           
           
           $Query="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion_criterio ";
           $Query.="	WHERE 1=1 ORDER BY fl_calificacion_criterio DESC ";
           $rs = EjecutaQuery($Query);
           for($i=1;$row=RecuperaRegistro($rs);$i++) {
               $fl_calificacion_criterio=$row['fl_calificacion_criterio'];
               $cl_calificacion=$row['cl_calificacion'];
               $ds_calificacion=$row['ds_calificacion'];
               $fg_aprobado=$row['fg_aprobado'];
               $no_equivalencia=$row['no_equivalencia'];
               $no_min= number_format($row['no_min']);
               $no_max=number_format($row['no_max']);
               
               if($no_max==0)
                   $ds_equivalencia="No Uploaded";
               else
                   $ds_equivalencia=$no_min."% - ".$no_max."%"." ($cl_calificacion)";
               
               #Recupermaos la descripcion que tiene actualmente.
               $Query_c="SELECT ds_descripcion,fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
               $row_c=RecuperaValor($Query_c);
               $ds_desc=str_texto($row_c[0]);
               $fl_criterio_fame=$row_c[1];
               
               #Recuperamos las imagenes por calificacion
               $Query_img = "SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame ";
               $row_img = RecuperaValor($Query_img);
               $nb_archivo_criterio = $row_img[0];
               $src_img="../../images/rubrics/".$nb_archivo_criterio;
               
               $contador ++;
               
               if(!empty($nb_archivo_criterio)){
                   $icono = "<a class='zoomimg' href='#'> 
            <i class='fa fa-file-picture-o'></i>
            <span style='left:-300px;'>
              <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-bottom: -530px;'>
                <div class='modal-content' style='width:500px;height:500px;'>
                  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
                    <img class='superbox-current-img' src='$src_img' style='width:494px;height:490px;'>
                  </div>
                </div>
              </div>
            </span>
          </a> ";
               }else{
                   $icono = "";
               }
               
               
               
               
               echo"
       
         <div class='col-md-2' style='padding-left: 3px;padding-right: 3px;'>				
              <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
                 $ds_calificacion &nbsp;&nbsp;$icono
              </div>
              <br/>
              <div class='panel panel-default'>
                   <div class='panel-body text-center'>
                        <span  style='color:#8FCAE5;font-size:15px; '>$ds_equivalencia </span>  <p>&nbsp;</p>

                        <div class='knobs-demo'>
                          <div>
                            <input class='knob$contador font'     value='$no_max'  disabled/>
                          </div>
                        </div>
                        
                         <div class='form-group text-left' style='padding-left:5px; padding-right:5px;'>
                            <div id='desc$contador'></div>
                            <hr>
                    
                            <div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
                              <small class='text-muted'><i>$ds_desc</i></small>              
                            </div>
                  
                         </div>
                         
                          <p style='margin-left: -5px;color:#999;'>&nbsp;</p>
                   </div>
               </div>

          </div>
        
        
        
        ";

               
               echo"<script>
            $(document).ready(function () {
             document.getElementById('desc$contador').disabled = true;//tofos al cargar el document estan desaibiltados
              $('#char$contador').addClass('hidden');//botones desabilitados
            //<!--propiedades del input knob -->
                $('.knob$contador').knob({
                  'width':100,
                  'height':100,
                  'angleArc':360,
                  'thickness':0.16,
                  'cursor':false,
                  'readOnly':true,
                  'angleOffset':50,
                  'fgColor':'#B7B7B7'
                 
                });
        
              });	
        </script> ";        
               
               
               
               
               
           }#end 2do query
           
           ######################iNICIA COMENTARIOS DEL TEACHER##########################
           #Recupermaos la calificacion asignada por el estudiante.
           $porcentaje_equivalente=0;
           $no_calificacion_final=0;
           $ds_comentario_teacher="No comment";
           $fe_calificado="No date";
           $ds_comentario_final_teacher="No comment";
           $no_promedio_final=110;
           
           
           echo"<div class='col-md-2' style='padding-left: 3px;padding-right: 3px;'>";
           echo"     <div class='well well-lg text-center' style='padding:2px;background: #F2F2F2;'>
						".ObtenEtiqueta(1664)."
	              </div>
                  <br/>
                  <div class='panel panel-default'>
                      <div class='panel-body text-center'>
                        <span  style='color:#8FCAE5;font-size:15px; '>$porcentaje_equivalente </span>  <p>&nbsp;</p>
                  
                            <div class='knobs-demo'>
                              <div>
                                <input class='knob$cont1 font'     value='$no_calificacion_final'  disabled/>
                              </div>
                            </div>
                        
                            
                            
                            <div class='form-group text-left' style='padding-left:5px; padding-right:5px;'>
                               <div id='desc$cont1'></div>
                                 <hr>
                    
                                <div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
                                  <small class='text-muted'><i>$ds_comentario_teacher</i></small>              
                                </div>
                  
                            </div>
                            
                           <p style='margin-left: -5px;color:#999;'><i>".ObtenEtiqueta(1680).": $fe_calificado</i></p> 
                        
                     </div>
                  </div>
                  
                  ";
           
           
           echo"</div>";
           echo"<script>
            $(document).ready(function () {
             //document.getElementById('desc$cont1').disabled = true;//tofos al cargar el document estan desaibiltados
             // $('#char$cont1').addClass('hidden');//botones desabilitados
            //<!--propiedades del input knob -->
                $('.knob$cont1').knob({
                  'width':100,
                  'height':100,
                  'angleArc':360,
                  'thickness':0.16,
                  'cursor':false,
                  'readOnly':true,
                  'angleOffset':50,
                  'fgColor':'#92D099'
                 
                });
        
              });	
        </script> ";
        
           
           
           
           
           
           
           ###################finaliza comentarios del teacher################
           
           
           
           
           
           
           
           
           echo"</div>";
           echo "</div>";
           echo"<br/>";
           
           
       }#end primer query.

       
      #Presenta comentarios finales del teacher
       echo"<style>
              .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
                  background-color: #fff !important;
                  
                  }
            </style>";
       
       
       echo"<div class='row'>
                <div class='col-md-10'>
                    <div class='col-md-12'>";
       echo"         <textarea class='form-control' rows='4' id='desc_teacher'  style='resize:none !important;' maxlength='130' disabled>$ds_comentario_final_teacher</textarea>";	

       echo"        </div>
                </div>";
       
       echo"    <div class='col-md-2'>
                    <div class='col-md-11'>
                        <div class='panel panel-default'>
                            <div class='panel-body text-center'>
                      
                                <div class='knobs-demo'>
                                   <div>
                                    <input class='knobfin font'     value='$no_promedio_final'  disabled/>
                                   </div>
                                </div>
                                <hr />		
									        <b>".ObtenEtiqueta(1671)."</b>
                            
                            </div>
                        </div> 
                    </div>
       
                </div>";
       
       echo"
            </div>";
       
       echo"<script>
            $(document).ready(function () {
             document.getElementById('desc$cont1').disabled = true;//tofos al cargar el document estan desaibiltados
              $('#char$cont1').addClass('hidden');//botones desabilitados
            //<!--propiedades del input knob -->
                $('.knobfin').knob({
                  'width':100,
                  'height':100,
                  'angleArc':360,
                  'thickness':0.16,
                  'cursor':false,
                  'readOnly':true,
                  'angleOffset':50,
                  'fgColor':'#92D099'
                 
                });
        
              });	
        </script> ";
       
       
       
       
   }


       
       
       
       
       
       
   
   



}*/

?>

<!--
<script src="<?php echo PATH_LIB; ?>/fame/dropzone.min.js"></script>	-->
 <!-- <script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/knob/jquery.knob.min.js"></script>-->
<!---plugin necesario para pintar el circulo -->
<!----------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- EASY PIE CHARTS -->
		<script src="<?php echo PATH_N_COM_JS; ?>/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>