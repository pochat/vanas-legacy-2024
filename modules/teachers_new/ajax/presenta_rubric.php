<?php 
# Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
 
$fl_leccion=RecibeParametroNumerico('fl_leccion');
$fl_programa=RecibeParametroNumerico('fl_programa');
$fl_alumno=RecibeParametroNumerico('fl_alumno');
$no_grado=RecibeParametroNumerico('no_grado');  
$fl_entrega_semanal=RecibeParametroNumerico('fl_entrega_semanal'); 
$fl_semana=RecibeParametroNumerico('fl_semana'); 
$no_semana=RecibeParametroNumerico('no_semana');
$fl_grupo=RecibeParametroNumerico('fl_grupo');
$fg_calificado=RecibeParametroNumerico('fg_calificado');
$fg_reset=RecibeParametroNumerico('fg_reset');
 
 $fl_usuario=ObtenUsuario();
 
#Recupermaos el nombre de la rubric.
$Qury="SELECT ds_titulo FROM c_leccion WHERE fl_leccion=$fl_leccion ";
$ro=RecuperaValor($Qury);
$nb_rubric=str_texto($ro['ds_titulo']);




#Borramos los registros previos de la calificacion del teacher y reasignamos nuevamente una calificacion.
if($fg_reset==1){

    #Eliminamos los comnetarios del teacher:
    $Coment="DELETE FROM c_com_criterio_teacher_campus WHERE  fl_alumno=$fl_alumno AND fl_programa=$fl_programa AND fl_leccion=$fl_leccion  AND no_grado='$no_grado' ";
    EjecutaQuery($Coment);
    #Eiminamos el temp calculo
    $Cal="DELETE FROM c_calculo_criterio_temp_campus WHERE  fl_alumno=$fl_alumno AND fl_programa=$fl_programa AND fl_leccion=$fl_leccion  AND no_grado='$no_grado' ";
    EjecutaQuery($Cal);
    #Eliminamos calif teacher
    $Tea="DELETE FROM k_calificacion_teacher_campus  WHERE fl_alumno=$fl_alumno AND fl_programa=$fl_programa AND fl_leccion=$fl_leccion  AND no_grado='$no_grado' ";
    EjecutaQuery($Tea);
    
    #Quitmos la calificacion del alumno.
    $Upt="UPDATE k_entrega_semanal SET fl_promedio_semana=NULL   WHERE fl_alumno=$fl_alumno AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo "; 
    EjecutaQuery($Upt);
    
    #Eliminamos lo que teniamos congeleado del alumno.
    $Dele="DELETE FROM k_criterio_programa_alumno WHERE  fl_alumno=$fl_alumno AND fl_programa=$fl_programa  ";
    EjecutaQuery($Dele);
    
    
    

}









#Verificamos si ya esta calificado por el teacher.(si tiene fl_promedio semna,quees llave que hace refrencia c_calificacion_sp)
$Query="SELECT fl_promedio_semana FROM k_entrega_semanal WHERE fl_alumno=$fl_alumno AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo ";
$row=RecuperaValor($Query);
$fg_esta_calificado_teacher=$row['fl_promedio_semana'];

if(empty($fg_esta_calificado_teacher)){#indica que se va calificar un estudiante que esta completo/immcompleto su trabajo.

	#Eliminamos registros basura que haya del los comentarios del teacher, y que esten asociado al alumno y sulecion y programa.
	EjecutaQuery("DELETE FROM c_com_criterio_teacher_campus WHERE  fl_alumno=$fl_alumno AND fl_programa=$fl_programa AND fl_leccion=$fl_leccion  AND no_grado='$no_grado' ");
	EjecutaInsert("DELETE FROM k_calificacion_teacher_campus WHERE fl_alumno=$fl_alumno AND fl_programa=$fl_programa AND fl_leccion=$fl_leccion  AND no_grado='$no_grado' ");
	#Eliminamos regitros existentes
	EjecutaQuery("DELETE FROM c_calculo_criterio_temp_campus WHERE  fl_alumno=$fl_alumno AND fl_programa=$fl_programa AND fl_leccion=$fl_leccion  AND no_grado='$no_grado' ");


}else{
	#Recupermaos la fecha de calificacion para presentarlo en transcript.
	$Query="SELECT fe_modificacion FROM c_com_criterio_teacher_campus WHERE fg_com_final='1' AND fl_alumno=$fl_alumno AND fl_programa=$fl_programa AND fl_leccion=$fl_leccion  AND no_grado='$no_grado'  ";
	$row=RecuperaValor($Query);
	$fe_calificado=ObtenFechaFormatoDiaMesAnioHora($row[0]);



}


# Funcion para mostrar la informacion general del usuario
function Profile_picCampus($fl_usuario, $fl_programa, $no_session=0, $fl_maestro,$fl_grupo, $fg_front=true){
    
    # Obtenemos el nombre del usuario
    $row0 = RecuperaValor("SELECT  CONCAT( ds_nombres,' ', ds_apaterno ) FROM c_usuario WHERE fl_usuario=$fl_usuario");
    $ds_nombres = str_texto($row0[0]);
    # Obtenemos nombre del curso
    $row1 = RecuperaValor("SELECT nb_programa FROM c_programa WHERE fl_programa=$fl_programa ");
    $ds_programa = str_texto($row1[0]);
    
    # Obtenemos avatar
    $avatar = ObtenAvatarUsrFa_Va($fl_usuario);
    
    # Obtenemos nombre del maestro
    $row3 = RecuperaValor("SELECT  CONCAT( ds_nombres,' ', ds_apaterno ) FROM c_usuario WHERE fl_usuario=$fl_maestro");
    $ds_maestro = str_texto($row3[0]);
    
    # Obtenemos grupo
    $nb_grupo = ObtenGrupoCampus($fl_grupo);
    
    # Informacion
    # front
    if($fg_front==true){
      echo "
      <div class='carousel-inner profile-pic' style='background-color:rgba(255, 255, 255, 0.82);'>
        <div id='user-profile-container' class='profile-container no-margin'>
        <img class='avatar' src='".$avatar."' height='70' width='70'>
        <div class='info'>
          <div class='username no-margin'>&nbsp;".$ds_nombres."</div>
          <div class='text no-margin'><strong>".ObtenEtiqueta(1878)."</strong>&nbsp;".$ds_programa."</div>
          <div class='text no-margin'><strong>".ObtenEtiqueta(1965)."</strong>&nbsp;".$nb_grupo."</div>";
          if($no_session>0){
            echo "
            <div class='text no-margin'><strong>".ObtenEtiqueta(1879)."</strong>&nbsp;".$no_session."</div>";
          }
      echo "
          <div class='text no-margin'><strong>".ObtenEtiqueta(1880)."</strong>&nbsp;".$ds_maestro."</div>                
        </div>
        </div>                  
      </div>";
    }
    else{
      echo "
       <div class='col-sm-3'>
         <img src='".$avatar."' style='height:70px;'  alt='' class='img-rounded' >
       </div>
       <div class='col-sm-9 text-align-right'>
          <div class='info'>
            <div class='username no-margin'><h3 class='no-margin'>".$ds_nombres."</h3></div>
            <div class='text no-margin'><strong>".ObtenEtiqueta(1878)."</strong>&nbsp;".$ds_programa."</div>
            <div class='text no-margin'><strong>".ObtenEtiqueta(1880)."</strong>&nbsp;".$ds_maestro."</div> 
          </div>
       </div>";
    }
}

# Funcion para otener el grupo
function ObtenGrupoCampus($fl_grupo){
    $row = RecuperaValor("SELECT nb_grupo FROM c_grupo WHERE fl_grupo=$fl_grupo ");
    $nb_grupo = str_texto($row[0]);
    
	return $nb_grupo;
}

?>
   

	<div class="row padding-10">
		<div class=" col col-md-12 col-sm-12 col-lg-5">	
		<?php
			Profile_picCampus($fl_alumno, $fl_programa, $no_semana,$fl_usuario,$fl_grupo);
			if($fe_calificado){
			  echo "<b>".ObtenEtiqueta(1678).":</b> ".$fe_calificado."<br >";
			}
			# Boton se presenta cuando ya tiene una calificacion el usuario que puede mejorar su calificacion cuando esta activado
		 ?>
		</div>
		
		<div class=" col col-md-12 col-sm-12 col-lg-7 text-align-right">
			<?php  
			if(!empty($fg_esta_calificado_teacher)){
			
			       #Verificamos si la leccion ha sufrido modificaciones con respecto ala rubric.
                   $tiene_modificaciones_rubric=VerificaCambiosRubricActualCampus($fl_leccion,$fl_alumno);
			       //$tiene_modificaciones_rubric=1;
			       if($tiene_modificaciones_rubric){
                       
                      echo"<div class='well well-lg' style='width: 60%;float: right;'>";
                      echo"<p class='text-center' style='color:red;'>".ObtenEtiqueta(2046)."</p>"; 
					  echo"<div class='col-md-6 text-center' style='padding-left: 8px;'>";
                      echo"<button class='btn btn-danger' data-toggle='modal' data-target='#peligro'>".ObtenEtiqueta(2045)."</button>";
                      echo"</div>";
					  echo"<div class='col-md-6 text-center' style='padding-left: 8px; margin-top:5px;' >";
					  //echo"<span class='label label-success' style='cursor:pointer;' onclick='AsignarCalificacion2($fl_entrega_semanal,$fl_alumno,$fl_leccion,$fl_grupo,$fl_semana,$fl_programa,$no_grado,$no_semana);'><strong><i class='fa fa-pencil'></i>&nbsp;".ObtenEtiqueta(2045)."</strong></span> ";
					  echo"<a href='javascript:void(0);' data-toggle='modal' data-target='#muestraRubricActual'> <i class='fa fa-search' aria-hidden='true'></i>&nbsp;".ObtenEtiqueta(2047)."</a>  ";
                      echo"</div>";
					  echo"</div>";
					
					
				   }
			
			}
			?>


                <!---Modl que presentara el warning de reset grading------>
            			<!-- Modal -->
                        <div class="modal fade" id="peligro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header text-left">
                                <h5 class="modal-title " id="exampleModalLabel2"><i class='fa fa-table' aria-hidden='true'></i>&nbsp;<?php echo ObtenEtiqueta(2045); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px;">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body text-center">


                                <h1><?php echo ObtenEtiqueta(2048); ?></h1>
                                <h1 style="color:red;"><?php echo ObtenEtiqueta(2049); ?></h1>


						  
			                  </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary "  data-dismiss="modal"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;Cancel</button>
                                <button type="button" class="btn btn-primary"  data-dismiss="modal"  <?php echo"onclick='CierraModal(),AsignarCalificacion2($fl_entrega_semanal,$fl_alumno,$fl_leccion,$fl_grupo,$fl_semana,$fl_programa,$no_grado,$no_semana);' "; ?>  ><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;Accept</button>
                              </div>
                            </div>
                          </div>
                        </div>
                    <!-- muestra modal -->

            <script>
                function CierraModal() {

                    $('#peligro').modal('hide');


                }</script>

            <!----------->








			
			<!---------------------Modal que presentara la rubric modificada------>
			<!-- Modal -->
                        <div class="modal fade" id="muestraRubricActual" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header text-left">
                                <h5 class="modal-title " id="exampleModalLabel"><i class='fa fa-table' aria-hidden='true'></i>&nbsp;<?php echo $nb_rubric; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px;">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body"  style="height:500px; overflow-y:auto;overflow-x:hidden;">
							  
									<?php 
									   
									        $Query="SELECT fl_criterio, no_valor FROM k_criterio_programa WHERE fl_programa = $fl_leccion ORDER BY no_orden ASC	";
									        #Recuperamos todos los criterios
        
											$rs_prin = EjecutaQuery($Query);
											$registros = CuentaRegistros($rs_prin);
											for($i_prin=1;$row_prin=RecuperaRegistro($rs_prin);$i_prin++) {
												
												$fl_criterio=$row_prin['fl_criterio'];
												$no_valor_criterio = $row_prin['no_valor'];
												
												$rs_nb_crit = RecuperaValor("SELECT nb_criterio FROM c_criterio WHERE fl_criterio = $fl_criterio");
												$nb_criterio = str_texto($rs_nb_crit[0]);
												
												
												
												$rubric .= "<div class='row' style='height:auto; padding-left:75px;'>";
												$rubric .= "<div class='col-md-1' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'>				
															  
																		  <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
																		  
																		 
																		  
																			Criterion
																		  </div>
																		  <br/>
																		  <div class='panel panel-default text-center' style='height:358px;'> <p style='margin: 0 0 0px;'>&nbsp;</p>
																			<span  style='color:#8FCAE5;font-size:15px;' class='text-center'>$no_valor_criterio% </span>
																							<!--<section class='form-group' style='-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);margin-top: 188px;'>
																								
																								<label class='input' style='font-weight:bold;'>
																									<input  class='form-control input-lg'  style=' border: 0px solid #ccc;' name='nb_criterio' id='nb_criterio' type='text' value='$nb_criterio' />
																								</label>
																							</section>-->
																			
																							<div style='font-size:18px; font-weight:bold; -webkit-transform: rotate(-90deg);margin-top: 111px;width: 215px;margin-left: -63px;'>$nb_criterio</div>
																								<!--<div  class='panel-body text-center' style='writing-mode: vertical-lr; transform: rotate(180deg); font-size:18px; font-weight:bold; padding: 15px 29px 88px 50px;'>$nb_criterio</div>--->
																		  </div>

																  </div>";
												
												
												$Query="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion_criterio ";
												$Query.="	WHERE 1=1 AND fl_instituto is null  ORDER BY no_equivalencia ASC ";
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
													$src_img = $ruta.$nb_archivo_criterio;
													
													$contador ++;
													
													if(!empty($nb_archivo_criterio)){
														$icono = "<a class='zoomimg' href='#' Onclick='javascript:void(0);'> 
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
													
													
													
													
													$rubric .= "
																
																 <div class='col-md-2' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'>				
															  
																  <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
																	 $ds_calificacion &nbsp;&nbsp;$icono
																  </div>
																  <br/>
																  <div class='panel panel-default' style='height:358px;'>
																	<div class='panel-body text-center'>
																	  <span  style='color:#8FCAE5;font-size:15px; '>$ds_equivalencia </span>  <p>&nbsp;</p>

																		<div class='chart' data-percent='$no_max' id='easy-pie-chart_$contador'>
																			<span class='percent' style='font:18px Arial;'>$no_max</span>
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
													$rubric.=" 
														  
																	 <script>
																		$(document).ready(function () {
																			$('#easy-pie-chart_$contador').easyPieChart({
																				animate: 2000,
																				scaleColor: false,
																				lineWidth: 7.5,
																				lineCap: 'square',
																				size: 100,
																				trackColor: '#EEEEEE',
																				barColor: '#92D099'
																			});

																			$('#easy-pie-chart_$contador').css({
																				width: 100 + 'px',
																				height: 100 + 'px'
																			});
																			$('#easy-pie-chart_$contador .percent').css({
																				'line-height': 100 + 'px'
																			})

																		});
																	</script>
														  
																  ";
													
													
												}#end 2do query
												$rubric .= "   <div class='col-md-1' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'></div>";
												$rubric .= "</div>";
												$rubric .= "<br/>";
												
												
											}#end primer query.

        
									        echo $rubric;
									
									
									
									?>
							  
			                  </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary hidden" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </div>
                        </div>
                    <!-- muestra modal -->
			
			<!------------------end Modal---------------------------------------->
			
			
			
			
			
			
		</div>
		
		
		
	</div>




<?php

	#Recuperamos los trabajos subidos por el estudiante.
    $Query="SELECT b.fg_tipo,b.ds_ruta_entregable,b.ds_comentario,a.fl_entrega_semanal ,a.fe_entregado,b.fl_entregable
			FROM k_entrega_semanal a
			JOIN k_entregable b ON b.fl_entrega_semanal=a.fl_entrega_semanal
			WHERE a.fl_entrega_semanal=$fl_entrega_semanal 
			AND a.fl_semana=$fl_semana
			AND a.fl_alumno=$fl_alumno
			AND a.fl_grupo=$fl_grupo ";
    $rsi = EjecutaQuery($Query);
    $archivos_estudiante = CuentaRegistros($rsi);
    $contador=0;
    for($i=1;$row=RecuperaRegistro($rsi);$i++) {
		$contador++;
        $fg_tipo=$row[0];
        $nb_archivo=$row[1];
        $ds_comentarios=str_texto($row[2]);
        $fe_entregado="<b>".ObtenEtiqueta(1677).":</b> ".ObtenFechaFormatoDiaMesAnioHora($row[4]);
		$fl_entregable=$row[5];
		
	    $nb_archivo_ori=$nb_archivo;
	
				if($contador==1){
                        $href.="
                             <style>
                                   .modal
                                {
                                    overflow: hidden;
                                }

                             </style>

                        ";
                }
		$ext = strtolower(ObtenExtensionArchivo($nb_archivo));
    

       
        if(($ext == "ogg")||($ext=='mp4')||($ext=='mov')||($ext=='m3u8')){
            $ruta_thumbs=PATH_CAMPUS."/../fame/site/uploads/gallery/thumbs/vanas-board-video-default.jpg";
            
        }else{
            $ruta_thumbs=PATH_CAMPUS."/students/sketches/board_thumbs/$nb_archivo";        
        }   
		
		
		
		 $href .="<a style='text-decoration:none !important;' href='javascript:void(0);' onclick=Buscar$contador();><img src='$ruta_thumbs' class='superbox-img ' style='width:40px; height:40px;'/> </a>";
              
         $href.="
		    <script>
			function Buscar$contador(){          
			  var elemento = $('#dialog-trabajo');          
			  elemento.empty();
			  elemento.dialog('open');
					elemento.dialog('option', 'width', 380);
					  elemento.dialog('option', 'height', 450);
					  var fl_entregable=$fl_entregable;
							var fl_alumno=$fl_alumno;
									$.ajax({
									type: 'POST',
									url: 'ajax/presenta_trabajo.php',
									data: 'fl_entregable='+fl_entregable+
										  '&fl_alumno='+fl_alumno,
										 
									async: false   
									}).done(function(result){
						  var content, contenido, title;
						  content = JSON.parse(result);
						  // contenido 
						  contenido = content.html;
						  elemento.append(contenido);
						  // agregamos titulo
						  title = content.title;
						  elemento.dialog('option', 'title', title);
						});            
			 }
			</script>
				
		 
		 ";   
		
		
		switch($fg_tipo){
            case"A":   
                $archivosA.=" $href &nbsp;";
                $contadorA++;
            break;
            case"AR": 
                $archivosAR.="$href &nbsp;";
                $contadorAR++;
                break;
            case"S":  
                $archivosS.="$href &nbsp;";
                $contadorS++;
                break;
            case"SR": 
                $archivosSR.="$href &nbsp;";
                $contadorSR++;
                break;
        }
        
        
   
      
        
        $href="";
		
	
	}
	
	
	
	#Verificamos check seleccionados en backend   | asiigment | assigment refrence| sketch |sketch refrence | 
    $Query10="SELECT fg_animacion,fg_ref_animacion,no_sketch,fg_ref_sketch FROM c_leccion WHERE fl_leccion=$fl_leccion   ";
    $row10=RecuperaValor($Query10);
    $fg_assigment=$row10[0];
    $fg_asigment_reference=$row10[1];
    $fg_sketch=$row10[2];
    $fg_sketch_reference=$row10[3];  
	
	
	
	
	
	
	
	
	$div_img="<hr />
                 <div class='row'>
                 ";
	
                 #solo muestra,si tiene imagen.
                 if( ($fg_assigment==1) ){
                     
                       if(empty($contadorA))
                           $no_foundA="<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1968)."  ".ObtenEtiqueta(1993)." </strong> ";
     
                         $div_img.="<div class='col-md-3'>

                                                <div class='panel panel-default'>
                                
				                                    <div class='panel-body '>
					                                    <div class='who clearfix'>
						                                   <!-- <blockquote style='font-size:13px;margin: 0 0 9px;padding: 8px 9px;'>
						                                     <span class='name'><b>".ObtenEtiqueta(1968)."</b></p></span>
                                                            </blockquote>-->
											                <span class='name'><b>".ObtenEtiqueta(1968)."</b></p></span><br/>
											
                                                             $no_foundA	
						                                    <hr>
					                                    </div>
                                                             $archivosA 
				                                    </div>
			                                    </div>
                                     </div>
                                   ";
                 }
                 if( ($fg_asigment_reference==1) ){
 
                         if(empty($contadorAR))
                             $no_foundAR="<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1969)." ".ObtenEtiqueta(1993)."</strong> ";
     
     
                         $div_img.="
                            <div class='col-md-3'>
                                        <div class='panel panel-default'>  
				                            <div class='panel-body '>
					                            <div class='who clearfix'>
						                            <!--<blockquote style='font-size:13px;margin: 0 0 9px;padding: 8px 9px;'>
						                                <span class='name'><b>".ObtenEtiqueta(1969)."</b></p></span>
                                                    </blockquote>-->
									                 <span class='name'><b>".ObtenEtiqueta(1969)."</b></p></span><br/>
                                                    $no_foundAR	
						                            <hr>
					                            </div>
                                                        $archivosAR 
				                            </div>
			                            </div>
                            </div>";
                 }
 
 
                 if( ($fg_sketch<>0) ){
     
                        if(empty($contadorS))
                            $no_foundS="<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1970)." ".ObtenEtiqueta(1993)."</strong> ";
     
                        $div_img.="
                            <div class='col-md-3'>
                                <div class='panel panel-default'>  
				                    <div class='panel-body '>
					                    <div class='who clearfix'>
						                   <!-- <blockquote style='font-size:13px;margin: 0 0 9px;padding: 8px 9px;'>
						                        <span class='name'><b>".ObtenEtiqueta(1970)."</b></p></span>
                                            </blockquote>-->
							                 <span class='name'><b>".ObtenEtiqueta(1970)."</b></p></span><br/>
                                           $no_foundS
						                    <hr>
					                    </div>
                                          $archivosS 
				                    </div>
			                    </div>
                            </div>";
            
                 }  
 
                 if(($fg_sketch_reference==1) ){
     
                        if(empty($contadorSR))
                            $no_foundSR="<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1970)." ".ObtenEtiqueta(1993)."</strong> ";  
            
        
                         $div_img.="           
                                <div class='col-md-3'>
                                     <div class='panel panel-default'>  
				                        <div class='panel-body '>
					                       <div class='who clearfix'>
						                       <!-- <blockquote style='font-size:13px;margin: 0 0 9px;padding: 8px 9px;'>
						                            <span class='name'><b>".ObtenEtiqueta(1971)."</b></p></span>
                                                </blockquote>-->
								                <span class='name'><b>".ObtenEtiqueta(1971)."</b></p></span><br/>
                                                 $no_foundSR
						                        <hr>
					                       </div>
                                              $archivosSR
				                         </div>
			                          </div>
                                </div> 
                        ";
                 }


                # Verificamos si subio archivos extras el alumno
                $QueryG  = "SELECT fl_worksfiles, ds_files, ds_version, ds_descripcion, DATE_FORMAT(fe_file, '%d, %M %Y %H:%m' ), no_orden FROM k_worksfiles ";
                $QueryG .= "WHERE fl_alumno=".$fl_alumno."  AND fl_leccion=".$fl_leccion." AND fg_campus='1' ";
                $QueryG .= "ORDER BY fe_file ";
                $rsG = EjecutaQuery($QueryG);
                $totWorks = CuentaRegistros($rsG);
                if(!empty($totWorks)){
                  $div_img.="<div class='col-md-3'>

                                                <div class='panel panel-default'>
                                
				                                    <div class='panel-body '>
					                                    <div class='who clearfix'>
						                                   <!-- <blockquote style='font-size:13px;margin: 0 0 9px;padding: 8px 9px;'>
						                                     <span class='name'><b>Worl Files</b></p></span>
                                                            </blockquote>-->
											                <span class='name'><b>".ObtenEtiqueta(2208)."</b></p></span><br/>
											
                                                             $no_works
						                                    <hr>
                                              <i class='fa fa-files-o fa-2x txt-color-blue cursor-pointer' onclick='works(".$fl_alumno.", ".$fl_leccion.");'></i>
					                                    </div>
				                                    </div>
			                                    </div>
                                     </div>
                                     <script>
                                     function works(std, lec){
                                      var elemento = $('#dialog-trabajo');          
                                      elemento.empty();
                                      elemento.dialog('open');
                                      elemento.dialog('option', 'width', 700);
                                      elemento.dialog('option', 'height', 500);
                                      $.ajax({
                                          type: 'POST',
                                          url: 'ajax/presenta_trabajo.php',
                                          data: 'fl_leccion='+lec+
                                              '&fl_alumno='+std,
                                             
                                          async: false   
                                          }).done(function(result){
                                      var content, contenido, title;
                                      content = JSON.parse(result);
                                      // contenido 
                                      contenido = content.html;
                                      elemento.append(contenido);
                                      // agregamos titulo
                                      title = content.title;
                                      elemento.dialog('option', 'title', title);
                                    });
                                     }
                                     </script>
                                   ";
                 
                }

?>


	<div class="col-md-12">
		 <?php
			#Presenta galeria de imaganes
			echo $div_img;
		?>
		<br/>                
	</div>
    <br />
	
	
	<!-- Div que muetsra los trabajos -->
    <style>
    .ui-dialog{
      position:fixed !important;
    }
	
    .ui-icon{      
      background-image: url('../../fame/img/ui-icons_222222_256x240.png') !important;
    }   



  /*Para aumentar tamaño de las imagenes solo con pasar el mouse*/
.zoomimg {position: relative; z-index: 0; }
.zoomimg:hover{ background-color: transparent; z-index: 50; }
.zoomimg span{ /* Estilos para la imagen agrandada */
position: absolute;
/*background-color: black;*/
padding: 5px;
left: -50px;
/*border: 5px double gray;*/
visibility: hidden;
color: #000;
width:600px;
/*text-decoration: none;*/
}
.zoomimg span img{ border-width: 0; padding: 2px; width:600%; height:600%; }
.zoomimg:hover span{ visibility: visible; top: 0; /*left: -10px;*/ }


	
    </style>

    <div id="dialog-trabajo" style="width:100%;"></div> 

    <script>
    pageSetUp();
    // Modal Link
    $("#dialog-trabajo").dialog({
      autoOpen : false,
      modal : false,
      width : 380,
      height : 450,     
      position: { my: "left", at: "left" }
    });
	</script>

	
 <style>
.font {
color:#333 !important;
font: 18x Arial !important;
font-weight:100 !important;
}

.border{
		border: 2px solid  #3194DA;
		
}

small, .small {
		font-size: 100% !important;
}
.chart {
                            /* height: 220px; */
                            margin: auto !important;
                        }
</style>

<?php 




?>



			<!----------librerias para presentar sliders Barra azul--------->
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.min.css" />
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.css" />
			<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.js" ></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.min.js" ></script>


			
			
			
			
			

 <div class="row padding-10" >
        <div class="col-md-12">
           <div class="panel panel-default" style="border-radius:20px;">
		        <div class="panel-body text-center">
		        <p style="font-size:20px;"><?php echo $nb_rubric;?></p>
		        </div>
	        </div>
        </div>
 </div>			
			
			
			
			
			

             <!-- 
                    <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal" id="asignar">
                     Launch demo modal
                    </button>

                    
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                         <div class="modal-dialog" role="document" style="width:90%;">
                             <div class="modal-content">
                                <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" id='cerrar' aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title text-center" id="myModalLabel" style="font-size:18px;"><i class="fa fa-table" aria-hidden="true"></i><b>&nbsp;<?php echo "Rubric: </b>".$nb_rubric; ?></h4>
                                </div>

                                <div class="modal-body text-center">
                               --->       
						   
							    <!------------------------------------------------------------------------------------------------------------>
								<?php 


									
									   #Muestra Rubric para asignar calificacion del  teacher
									   
                                      if($fg_esta_calificado_teacher){
									   
                                          #Recuperamos todos los criterios
                                          $Query="SELECT fl_criterio, no_valor FROM k_criterio_programa_alumno WHERE fl_programa = $fl_leccion AND fl_alumno=$fl_alumno ORDER BY no_orden ASC	";
                                          $rs_prin = EjecutaQuery($Query);
                                          
                                      }else{
                                      
                                          #Recuperamos todos los criterios
                                          $Query="SELECT fl_criterio, no_valor FROM k_criterio_programa WHERE fl_programa = $fl_leccion ORDER BY no_orden ASC	";
                                          $rs_prin = EjecutaQuery($Query);
                                      
                                      }
                                       
                                       
                                       
									   $registros = CuentaRegistros($rs_prin);
									   $cont1=0;
									   $rubric =" ";
									   $fl_identificador= rand(1, 300);
									   for($i_prin=1;$row_prin=RecuperaRegistro($rs_prin);$i_prin++) {
										   
										   $fl_criterio=$row_prin['fl_criterio'];
										   $no_valor_criterio = $row_prin['no_valor'];
										   
										   $rs_nb_crit = RecuperaValor("SELECT nb_criterio FROM c_criterio WHERE fl_criterio = $fl_criterio");
										   $nb_criterio = str_texto($rs_nb_crit[0]);
										   
										   $cont1 ++;
										   
										   $rubric.= "<div class='row padding-10'  >";
										   $rubric.= "   <div class='col-md-1' style='padding-right: 0px;'>				
																		 <div class='col-md-12' style='padding-left: 1px;' >
																			  <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
																				Criterion
																			  </div>
																			  
																			  
																			  <br/>
																			  <div class='panel panel-default text-center' style='height:398px;'>
																			  <p style='margin: 0 0 0px;'>&nbsp;</p>
																			  <span  style='color:#8FCAE5;font-size:15px; '>$no_valor_criterio% </span>
																				<div  class='panel-body text-center' style='writing-mode: vertical-lr; transform: rotate(180deg); font-size:18px; font-weight:bold; padding: 15px 20px 90px 50px;'>$nb_criterio</div>
																			  </div>
																		  </div>

													 </div>";
										   
										   
										   $rubric.="<style>

										   
															.chart {
																/* height: 220px; */
																margin: 20% 5% 10% 15% !important;
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
										   
										   
										   
													.chart {
														margin: auto !important;
														}

													.slider-selection {
														background-image: linear-gradient(to bottom, #3194DA 0%, #3194DA 100%)!important;
													}

													.ui-slider-horizontal {
														background: #D5D5D500 !important;
													}
													.ui-slider-horizontal .ui-slider-handle:hover {
													background-color: rgba(255, 255, 255, 0) !important;
													}
													.ui-slider-horizontal .ui-slider-handle:focus {
													background-color: rgba(255, 255, 255, 0) !important;
													}
													.ui-slider-horizontal {
													background: rgba(213, 213, 213, 0) !important;
													}
													.slider.slider-horizontal .slider-handle {
													margin-top: -1px !important;
													}
										   </style>";
										   
										   
										   
										   $rubric.="   <div class='col-md-11' style='padding-left: 1px;'>";
										   
										   
										   $Query="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion_criterio ";
										   $Query.="	WHERE 1=1  AND fl_instituto is null ORDER BY no_equivalencia ASC ";
										   $rs = EjecutaQuery($Query);
										   $contador_border=0;
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
											   $src_img= PATH_HOME."/images/rubrics/".$nb_archivo_criterio;
											   
											   $contador ++;
											   $contador_border++;
											   if(!empty($nb_archivo_criterio)){
												   $icono = "<a class='zoomimg' href='#' style='color:#000;text-decoration: none;'> 
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
											   
											   
											   
											   
											   $rubric.="
								   
															 <div class='col-md-2' style='padding-left: 3px;padding-right: 3px;'>				
																  <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
																	 $ds_calificacion &nbsp;&nbsp;$icono
																  </div>
																  <br/>
																  <div class='panel panel-default'>
																	   <div class='panel-body text-center'  id='divborder_cero_".$fl_criterio."_".$contador_border."'>
																			<span  style='color:#8FCAE5;font-size:15px; '>$ds_equivalencia </span>  <p>&nbsp;</p>
																			
																			 <div class='chart ' data-percent='$no_max' id='easy-pie-chart$contador'>
																				<span class='percent' style='font:18px Arial;font-weight:none !important;'>$no_max</span>
																			 </div>
																			
																			 <div class='form-group text-left' style='padding-left:5px; padding-right:5px;'>
																				<div id='desc$contador'></div>
																				<hr>
												
																				<div class='bs-example' style='height:108px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
																				  <small class='text-muted'><i>$ds_desc</i></small>              
																				</div>
											  
																			 </div>
																			 
																			 <div class='form-group'>
																						<div class='col-md-6'>
																						&nbsp;
																						</div>
																						<div class='col-md-6'> 
																					    &nbsp;
																						</div>

																			 </div>
																			 
																			 <p>&nbsp;</p>
													 
																			  
																	   </div>
																   </div>

															  </div>
									
									
									
															";
															
														$rubric.="
															<script>  
																  $('#easy-pie-chart$contador').easyPieChart({
																	animate: 2000,
																	scaleColor: false,
																	lineWidth: 7.5,
																	lineCap: 'square',
																	size: 100,
																	trackColor: '#EEEEEE',
																	barColor: '#B7B7B7'
																});

																$('#easy-pie-chart$contador').css({
																	width: 100 + 'px',
																	height: 100 + 'px'
																});
																$('#easy-pie-chart$contador .percent').css({
																	'line-height': 100 + 'px'
																})
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
										   
										   
										   if(!empty($fg_calificado)){
										   
										    #Recuperamos si sxite una calificacion asignada.
											$Query="SELECT ds_comentarios,no_porcentaje_equivalente,fe_modificacion FROM c_com_criterio_teacher_campus WHERE fl_criterio=$fl_criterio AND fl_alumno=$fl_alumno  
											  AND fl_leccion=$fl_leccion  AND fl_semana=$fl_semana    ";
											$row=RecuperaValor($Query);
											$ds_comentario_criterio=str_texto($row[0]);
											$no_porcentaje_equivalente=$row[1];
											//$fe_asignacion_califi=ObtenFechaFormatoDiaMesAnioHora($row[2]);
											$fe_asignacion_califi= $row[2];
											$fe_modificacion=strtotime('+0 day',strtotime($fe_asignacion_califi));
											$fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
											#DAMOS FORMATO DIA,MES, AÑO.
											$date = date_create($fe_modificacion);
											$fe_asignacion_califi= ObtenEtiqueta(1680).": ".date_format($date,'F j, Y, g:i a');
										   
										   }else{
										    $ds_comentario_criterio="";
										    $fe_asignacion_califi="";
										    $no_porcentaje_equivalente=0;
										   }
										   
										   
                                           if($ds_comentario_criterio)
                                            $fe_asignacion_califi=$fe_asignacion_califi;
                                           else
                                            $fe_asignacion_califi="";                                           
                                           
										   
										   $rubric.="<div class='col-md-2' style='padding-left: 3px;padding-right: 3px;'>";
										   $rubric.="     <div class='well well-lg text-center' style='padding:2px;background: #F2F2F2;'>
																			".ObtenEtiqueta(1664)."
																	  </div>
																	  <br/>
																	  <div class='panel panel-default'>
																		  <div class='panel-body text-center'>
																			<span  style='color:#8FCAE5;font-size:15px; '>&nbsp; </span>  <p>&nbsp;</p>

																				
																				
																			 <div class='chart ' data-percent='$no_porcentaje_equivalente' id='final_$cont1'>
																				<span class='percent' style='font:18px Arial;font-weight:none !important;' id='span_final$cont1'>$no_porcentaje_equivalente</span>
																			 </div>
																				
																				
																				
																				

																				<div class='form-group'>
																					<span id='char$cont1' name='char$cont' class='pull-left text-left hidden'> </span>
																					<a href='javascript:void(0);' class='btn btn-xs btn-default pull-right' style='border:0px;' Onclick='EditarDescripcion$cont1($fl_criterio);' style='top:-10px !important;'><i class='fa fa-pencil' ></i></a>
																				   
																					 <hr>
												
																					 
																					<textarea class='form-control' rows='4'  id='desc_$cont1' name='desc_$cont1' style='resize:none !important;' maxlength='130'  onkeydown='CuentaCarteres$cont1();' onKeyUp='CuentaCarteres$cont1();' disabled>$ds_comentario_criterio</textarea>
																				</div>
																				<div class='form-group'>
																						<div class='col-md-6'>&nbsp;
																						<a class='btn btn-default btn-xs' style='font-size: 13px;' href='javascript:void(0);' id='btncancel$cont1' Onclick='CancelarEdicion$cont1($fl_criterio);'>Cancel</a>
																						</div>
																						<div class='col-md-6'>&nbsp;
																							<a class='btn btn-primary btn-xs' style='font-size: 13px;' href='javascript:void(0);'  id='btnsave$cont1' Onclick='GuardarDescripcion$cont1($fl_criterio);'>Save</a>
																						</div>

																				 </div>
																			   <div class='text-left' id='muestra_save$cont1' name='muestra_save$cont1' style='margin-left: -5px;color:#999;'><small class='text-muted'><i> $fe_asignacion_califi</i></small></p> </div> 
													
																		 </div>
																	  </div>
											  
																	  ";
										   
										   
										   $rubric.="</div>";
										   $rubric.="
										   
											<script>  
											 $(document).ready(function () {
												  
												  
												   $('#btncancel$cont1').addClass('hidden');//botones desabilitados
												   $('#btnsave$cont1').addClass('hidden');//botones desabilitados
											 
											 
												  $('#final_$cont1').easyPieChart({
													animate: 2000,
													scaleColor: false,
													lineWidth: 7.5,
													lineCap: 'square',
													size: 100,
													trackColor: '#EEEEEE',
													barColor: '#92D099'
												});

												$('#final_$cont1').css({
													width: 100 + 'px',
													height: 100 + 'px'
												});
												$('#final_$cont1 .percent').css({
													'line-height': 100 + 'px'
												})
												
											});
											</script> ";
										   
										   
										
										   #script para editar, guardar comentarios.
										   $rubric.="
										   <script>
											  function EditarDescripcion$cont1(fl_criterio) {
											  
													document.getElementById('desc_$cont1').disabled = false;
													//$('#desc$cont1').removestyle('disabled')
													
													$('#btncancel$cont1').removeClass('hidden');
													$('#btnsave$cont1').removeClass('hidden');
													$('#char$cont1').removeClass('hidden');//se hablita contador carateres.
													
											  }
											 function CancelarEdicion$cont1(fl_calificacion_criterio) {

													document.getElementById('desc_$cont1').disabled = true;
													$('#btncancel$cont1').addClass('hidden');//botones desabilitados
													$('#btnsave$cont1').addClass('hidden');//botones desabilitados
													$('#char$cont1').addClass('hidden');//se hablita contador carateres.

											 }
											 
											 
											 function GuardarDescripcion$cont1(fl_criterio) {

													var ds_descripcion = document.getElementById('desc_$cont1').value;
													var fl_criterio=fl_criterio;											        
													var fl_alumno=$fl_alumno;
													var fl_leccion=$fl_leccion;
													var no_grado=$no_grado;
													var fl_programa=$fl_programa;
													var fl_semana=$fl_semana;
													var fl_grupo=$fl_grupo;
													var fg_comentario_crietrio = 1;
													var rangeInput = document.getElementById('ex$cont1').value;

														$.ajax({
															type: 'POST',
															url: 'ajax/guardar_comentarios_criterio.php',
															data: 'ds_descripcion='+ds_descripcion+
																  '&fl_alumno='+fl_alumno+
																  '&fl_leccion='+fl_leccion+
																  '&fl_programa='+fl_programa+
																  '&no_grado='+no_grado+
																  '&rangeInput='+rangeInput+
																  '&fg_comentario_crietrio='+fg_comentario_crietrio+
																  '&fl_semana='+fl_semana+
																  '&fl_grupo='+fl_grupo+
																  '&fl_criterio='+fl_criterio,
																							   
															async: true,
															success: function (html) {

																$('#muestra_save$cont1').html(html);

															}


														});

														document.getElementById('desc_$cont1').disabled = true;
														$('#btncancel$cont1').addClass('hidden');//botones desabilitados
														$('#btnsave$cont1').addClass('hidden');//botones desabilitados
														$('#char$cont1').addClass('hidden');//se hablita contador carateres.

											 }
											 function CuentaCarteres$cont1() {
													
													var comentario=document.getElementById('desc_$cont1').value.length;
													var este = 130 - comentario;
													//alert(comentario);
													$('#char$cont1').html(este);
													//alert(este);
											 }
										   
										   </script>
										   
										   
										   ";
										   
										   
										   
										   
										   
										   
										   
										   
										   ###################finaliza comentarios del teacher################
										   $rubric.="</div><!-- end 11--> ";	   
										   $rubric.= "</div><!--end row-->";
										   
										   
										   $rubric.="<style>
										   #ex$cont1Slider .slider-selection {
												background: #BABABA;;
											}

										   </style>";
										   
										   $rubric.="
										   <div class='row'>
												<div class='col-md-1' style='padding-right: 0px;'> &nbsp;</div>
												<div class='col-md-9 ' style='padding-left: 1px; padding-right:1px; '> 
													<input id='ex$cont1' data-slider-id='ex$cont1Slider' type='text' data-slider-min='0' data-slider-max='100' data-slider-step='1' data-slider-value='$no_porcentaje_equivalente'/>
												</div>
												<div class='col-md-2'>&nbsp;</div>
										   </div>
										   ";
										   
										   $rubric.="
										   <script>
										   var slider = new Slider('#ex$cont1', {
												formatter: function(value) {
													return 'Current value: ' + value;
												}
											});
											$(document).ready(function () {
												  var intSeconds = 1;
												  var refreshId;
												  $('#ex$cont1').on('slideStop', function () { 
													  ObtenValor$cont1();    
												  });
											});
											
											function ObtenValor$cont1(){
												 var rangeInput = document.getElementById('ex$cont1').value;
												 var fl_criterio=$fl_criterio;//identificador del criterio
												 var fl_alumno=$fl_alumno;
												 var fl_programa=$fl_programa;
												 var fl_leccion=$fl_leccion;
												 var fg_calcula_promedio=1;
												 var peso_criterio=$no_valor_criterio;
												 var no_grado=$no_grado;
												 var fl_semana=$fl_semana;
												 var fl_grupo=$fl_grupo;
												 
												 
												//se actualiza el valor del ultimo circulo.		
												$('#final_$cont1').data('easyPieChart').update(rangeInput);		
												$('#span_final$cont1').html(rangeInput);
												
												
												
												//Guardamos los datos para saber el total.
													
													 $.ajax({
														type: 'POST',
														url: 'ajax/guardar_rango_calcular_calificacion.php',
														data: 'rangeInput='+rangeInput+
															  '&fl_alumno='+fl_alumno+
															  '&fl_programa='+fl_programa+
															  '&fl_leccion='+fl_leccion+
															  '&peso_criterio='+peso_criterio+
															  '&no_grado='+no_grado+
															  '&fl_semana='+fl_semana+
															  '&fl_grupo='+fl_grupo+
															  '&fl_criterio='+fl_criterio,

														async: true,
														success: function (html) {
															$('#presenta_calculo').html(html);
														}
													});

												
												
												
											}
										   </script>
										   ";
										   
										   if($fg_calificado==1){
                                           
                                           $rubric.="
                                           <div id='presenta_calculo_2_$cont1'></div>
                                           <script>
                                           
                                            function PintaBorder$cont1(){
                                                 var rangeInput = document.getElementById('ex$cont1').value;
                                                 var fl_criterio=$fl_criterio;//identificador del criterio
                                            
                                            	 $.ajax({
														type: 'POST',
														url: 'ajax/rubric_border.php',
														data: 'rangeInput='+rangeInput+
															  '&fl_criterio='+fl_criterio,

														async: true,
														success: function (html) {
															$('#presenta_calculo_2_$cont1').html(html);
														}
													});
                                            
                                            
                                            
                                            
                                            
                                            
                                            }
                                           
                                           PintaBorder$cont1();
                                           </script>
                                           
                                           
                                           ";
                                           
                                           
                                           
                                           }
										 

											
										   
										 
										   
										   
										   $rubric.="<br/>";
										   
										   
									   }#end primer query.

									   
									   #Presenta comentarios finales del teacher
									   if(!empty($fg_calificado)){
											$Query="SELECT ds_comentarios,fe_modificacion FROM c_com_criterio_teacher_campus 
											WHERE  fl_alumno=$fl_alumno AND  fl_leccion =$fl_leccion    AND fl_semana=$fl_semana  AND fg_com_final='1'  ";
											$row=RecuperaValor($Query);
											$ds_comentario_final=str_texto($row[0]);
                                            
                                            if($fe_comentario_final)
											$fe_comentario_final=ObtenEtiqueta(1680).": ".ObtenFechaFormatoDiaMesAnioHora($row[1]);
											else
                                            $fe_comentario_final="";
											#Recupermaos la calificCION FINAL:
											$Que="SELECT no_calificacion FROM k_calificacion_teacher_campus WHERE fl_alumno=$fl_alumno AND  fl_leccion =$fl_leccion AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana ";
											$r=RecuperaValor($Que);
											$no_clificacion_final=$r['no_calificacion'];
											
									   
									    }else{
										    $ds_comentario_final="";
										    $fe_comentario_final="";
										    $no_clificacion_final=0;
										    $fg_calificado=0;
										} 
										 
									   
									   
									   $rubric.="					<style>
																  .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
																	  background-color: #fff !important;
											  
																	  }
																</style>";
									   
									   
									   $rubric.="		<div class='row padding-10'>
																	<div class='col-md-10'>
																		<div class='col-md-12'>  
																				 <a href='javascript:void(0);' class='btn btn-xs btn-default pull-right' style='border:0px;' Onclick='EditarDescripcionFinal($fl_identificador);'><i class='fa fa-pencil' ></i></a> ";
									   $rubric.="        						 <textarea class='form-control' rows='4' id='desc_teacher'  placeholder='".ObtenEtiqueta(1668)."' style='resize:none !important;'  disabled>$ds_comentario_final</textarea>";	

									   
									   $rubric.="                                <div class='col-md-4 text-left' id='muestra_save_final'><small class='text-muted'><i>$fe_comentario_final</i></small></div>

																				 <div class='col-md-4 text-center' ><br/>    
																							
																						  <a href='javascript:void(0);' class='btn btn-primary' style='border-radius:10px;' id='boton_final' onclick='GuardarTranscript();'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".ObtenEtiqueta(1669)." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
									
																				 </div>
																				 
																				 <div class='col-md-4'>
																						<br/>
																						<div class='form-group' style='float:right;'>
																						<a class='btn btn-primary btn-xs' href='javascript:void(0);' style='float:right;font-size: 13px;' id='btnsavefinal' Onclick='GuardarDescripcionFinal($fl_identificador);'>Save</a>
																						</div>
																						<div class='form-group' style='float:right;'>
																						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																						 <a class='btn btn-default btn-xs' href='javascript:void(0);' style='float:right;font-size: 13px; margin-right:10px;' id='btncancelfinal' Onclick='CancelarEdicionFinal($fl_identificador);'>Cancel</a>
																						</div>
																				</div>
																				 
																				 
																				 
																				 
									   ";
									   $rubric.="   
																	<script>
																		$(document).ready(function () {

																			$('#btncancelfinal').addClass('hidden');
																			$('#btnsavefinal').addClass('hidden');
																			$('#charfinal').addClass('hidden');//se hablita contador carateres.
																			document.getElementById('desc_teacher').disabled = true;

																		});
																		function EditarDescripcionFinal(fl_identificador) {


																			document.getElementById('desc_teacher').disabled = false;
																			$('#btncancelfinal').removeClass('hidden');
																			$('#btnsavefinal').removeClass('hidden');
																			$('#charfinal').removeClass('hidden');//se hablita contador carateres.
																			$('#boton_final').addClass('hidden');
																		}

																		function CancelarEdicionFinal(fl_identificador) {

																			document.getElementById('desc_teacher').disabled = true;
																			$('#btncancelfinal').addClass('hidden');//botones desabilitados
																			$('#btnsavefinal').addClass('hidden');//botones desabilitados
																			$('#charfinal').addClass('hidden');//se hablita contador carateres.
																			 $('#boton_final').removeClass('hidden');
																				
																		}
																		
																		
																		function GuardarDescripcionFinal(fl_identificador) {

																			$('#boton_final').removeClass('hidden');
																		
																			var ds_comentarios = document.getElementById('desc_teacher').value;
																			var fl_alumno =$fl_alumno;
																			var fl_leccion =$fl_leccion;
																			var fl_programa =$fl_programa;
																			var fl_entrega_semanal=$fl_entrega_semanal;
																			var fl_semana=$fl_semana;
																			var fl_grupo=$fl_grupo;
																			var no_grado=$no_grado;
																			
																			var comen_final = 1;
																			// var no_calificacion = document.getElementById('final_total').value;
																			
																			$.ajax({
																				type: 'POST',
																				url: 'ajax/guardar_comentarios_criterio.php',
																				data: 'ds_comentarios='+ds_comentarios+
																						'&fl_alumno='+fl_alumno+
																						'&fl_leccion='+fl_leccion+
																						'&fl_entrega_semanal='+fl_entrega_semanal+
																						'&fl_semana='+fl_semana+
																						'&fl_grupo='+fl_grupo+
																						'&no_grado='+no_grado+
																						'&comen_final='+comen_final+
																						'&fl_programa='+fl_programa,

																				async: true,
																				success: function (html) {

																					$('#muestra_save_final').html(html);

																				}

																			});

																			document.getElementById('desc_teacher').disabled = true;
																			$('#btncancelfinal').addClass('hidden');//botones desabilitados
																			$('#btnsavefinal').addClass('hidden');//botones desabilitados
																			$('#charfinal').addClass('hidden');//se hablita contador carateres.

																		}
																		
																		
																		
																		
																		function GuardarTranscript() {

																			var ds_comentarios = document.getElementById('desc_teacher').value;
																			var fl_alumno =$fl_alumno;
																			var fl_leccion =$fl_leccion;
																			var fl_programa=$fl_programa;
																			var fg_guardar_todo = 1;
																			var fl_entrega_semanal=$fl_entrega_semanal;
																			var fl_semana=$fl_semana;
																			var fl_grupo=$fl_grupo;
																			var no_grado=$no_grado;
                                                                            var fg_calificado=$fg_calificado;
																			var fl_entrega_semanal=$fl_entrega_semanal;
																			
																			
																			
																			
																			$.ajax({
																				type: 'POST',
																				url: 'ajax/guardar_comentarios_criterio.php',
																				data: 'ds_comentarios='+ds_comentarios+
																						'&fl_alumno='+fl_alumno+
																						'&fl_leccion='+fl_leccion+
																						'&fg_guardar_todo='+fg_guardar_todo+
																						'&fl_entrega_semanal='+fl_entrega_semanal+
																						'&fl_semana='+fl_semana+
                                                                                        '&fg_calificado='+fg_calificado+
																						'&fl_grupo='+fl_grupo+
																						'&no_grado='+no_grado+
																						'&fl_programa='+fl_programa,

																						async: true,
																						success: function (html) {
																							  
																						 
																								$.smallBox({
																									title : '<i class=\"fa fa-check\" aria-hidden=\"true\"></i>".ObtenEtiqueta(1672)."',
																									//content : 'Lorem ipsum dolor sit amet, test consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam',
																									color : '#739E73',
																									timeout: 4000,
																									iconSmall : 'fa fa-check ',
																									//number : '2'
																								});
																						
																							$('#muestra_save_final').html(html);

																							
																							
																							
																						}
																						
																						

																			});
																			
																			
																			


																		}
																		
																		
																		
																		
																	</script>
									   
									   ";
									   
									   
									   
									   
									   
									   $rubric.="       			    </div>
																   </div>";
									   
									   $rubric.="    <div class='col-md-2'>
																		
																			<div class='panel panel-default'>
																				<div class='panel-body text-center'>
												  
																					<div id='presenta_calculo' name='presenta_calculo'>
																						<div class='chart' data-percent='$no_clificacion_final'  id='char_final'>
																							<span class='percent' style='font:18px Arial;font-weight:none !important;' id='span_char_final'>$no_clificacion_final </span>
																						</div>
																					</div>
																					<hr />		
																					<b>".ObtenEtiqueta(1671)."</b>
														
																				</div>
																			</div> 
																	
								   
																	</div>";
									   
									   $rubric.="
													</div>";
									   
									  $rubric.="
									  
										<script>  
											 $(document).ready(function () {
												   
											 
												  $('#char_final').easyPieChart({
													animate: 2000,
													scaleColor: false,
													lineWidth: 7.5,
													lineCap: 'square',
													size: 100,
													trackColor: '#EEEEEE',
													barColor: '#92D099'
												});

												$('#char_final').css({
													width: 100 + 'px',
													height: 100 + 'px'
												});
												$('#char_final .percent').css({
													'line-height': 100 + 'px'
												})
												
												  });
											</script> 
									  
									  
									  ";
									   
									   
							    
									   
							    echo $rubric;  		   
?> 





 