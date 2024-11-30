<?php

# Libreria de funciones	
require("../lib/self_general.php");


#Recibimos parametros
$fl_alumno=RecibeParametroNumerico('fl_alumno');
$fl_leccion_sp=RecibeParametroNumerico('fl_leccion_sp');
$fl_programa_sp=RecibeParametroNumerico('fl_programa_sp');
$nb_grupo=RecibeParametroHTML('nb_grupo');
$no_semana=RecibeParametroHTML('no_semana');
$fl_entrega_semanal_sp=RecibeParametroNumerico('fl_entrega_semanal_sp');
$fg_reset=RecibeParametroNumerico('fg_reset');
$fl_maestro = RecibeParametroNumerico('fl_teacher');
$fg_tab_assigment=RecibeParametroNumerico('fg_tab_assigment');


#Borramos los registros previos de la calificacion del teacher y reasignamos nuevamente una calificacion.
if($fg_reset==1){

   #Eliminamos los comnetarios del teacher:
   $Coment="DELETE FROM c_com_criterio_teacher WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp ";
   EjecutaQuery($Coment);
   #Eiminamos el temp calculo
   $Cal="DELETE FROM c_calculo_criterio_temp WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp ";
   EjecutaQuery($Cal);
   #Eliminamos calif teacher
   $Tea="DELETE FROM k_calificacion_teacher WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp ";
   EjecutaQuery($Tea);
   
   #Quitmos la calificacion del alumno.
   $Upt="UPDATE k_entrega_semanal_sp SET fl_promedio_semana=NULL , fg_increase_grade='0' WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp "; 
   EjecutaQuery($Upt);
   
   #Eliminamos lo que teniamos congeleado del alumno.
   $Dele="DELETE FROM k_criterio_programa_alumno_fame WHERE  fl_usuario_sp=$fl_alumno AND fl_programa_sp=$fl_leccion_sp  ";
   EjecutaQuery($Dele);
   
   
   

}




#Recupermaos el instituto del alumno seleccionado.
$fl_instituto=ObtenInstituto($fl_alumno);

#Verificamos si ya esta calificado por el teacher.(si tiene fl_promedio semna,quees llave que hace refrencia c_calificacion_sp)
$Query="SELECT fl_promedio_semana, fg_increase_grade FROM k_entrega_semanal_sp WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp ";
$row=RecuperaValor($Query);
$fg_esta_calificado_teacher=$row['fl_promedio_semana'];
$fg_increase_grade = $row['fg_increase_grade'];

if($fg_increase_grade=='1'){
    $cheked_increase="checked='checked' ";
} else {
	$cheked_increase=NULL;
}

$Qup="SELECT ds_leccion FROM c_leccion_sp WHERE fl_leccion_sp=$fl_leccion_sp  ";
$ro=RecuperaValor($Qup);
$ds_descrip_leccion=str_uso_normal($ro['ds_leccion']);




        if(empty($fg_esta_calificado_teacher)){#indica que se va calificar un estudiante que esta completo/immcompleto su trabajo.
                #Eliminamos registros basura que haya del los comentarios del teacher, y que esten asociado al alumno y sulecion y programa.
                EjecutaQuery("DELETE FROM c_com_criterio_teacher WHERE  fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp ");
                EjecutaInsert("DELETE FROM k_calificacion_teacher WHERE fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp  ");
                #Eliminamos regitros existentes
                EjecutaQuery("DELETE FROM c_calculo_criterio_temp WHERE  fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp ");

        }else{
                #Recupermaos la fecha de calificacion para presentarlo en transcript.
                $Query="SELECT fe_modificacion FROM c_com_criterio_teacher WHERE  fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp AND fg_com_final='1' ";
                $row=RecuperaValor($Query);
                $fe_calificado=ObtenFechaFormatoDiaMesAnioHora($row[0]);

    
        }





    #Recuperamos los trabajos subidos por el estudiante.
    $Query="SELECT b.fg_tipo,b.ds_ruta_entregable,b.ds_comentario,a.fl_entrega_semanal_sp ,a.fe_entregado,b.fl_entregable_sp
			FROM k_entrega_semanal_sp a
			JOIN k_entregable_sp b ON b.fl_entrega_semanal_sp=a.fl_entrega_semanal_sp
			WHERE a.fl_entrega_semanal_sp=$fl_entrega_semanal_sp  AND a.fl_leccion_sp=$fl_leccion_sp AND a.fl_alumno=$fl_alumno ";
    $rsi = EjecutaQuery($Query);
    $archivos_estudiante = CuentaRegistros($rsi);

    # Variables initialization to avoid errors
    $contador=0;
    $contadorA=0;
    $archivosA=NULL;
    $no_foundA=NULL;
    $archivosS=NULL;
    $contadorS=NULL;
    $no_foundS=NULL;
    $href=NULL;
    $href_infoS=NULL;

    for($i=1;$row=RecuperaRegistro($rsi);$i++) {
        $contador++;
        $fg_tipo=$row[0];
        $nb_archivo=$row[1];
        $ds_comentarios=$row[2];
        $fe_entregado="<b>".ObtenEtiqueta(1677).":</b> ".ObtenFechaFormatoDiaMesAnioHora($row[4]);
		$fl_entregable_sp=$row[5];
        
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
			$file = explode('.',$nb_archivo);
			$nb_archivo = $file[0].".mp4";
		}
	  
	  
        #Rutas del archivo.
        $ruta_video = "site/uploads/".$fl_instituto."/USER_".$fl_alumno."/videos/$nb_archivo";
        $ruta_thumbs = "site/uploads/".$fl_instituto."/USER_".$fl_alumno."/sketches/thumbs/$nb_archivo";
        $ruta_board_thumbs = "site/uploads/".$fl_instituto."/USER_".$fl_alumno."/sketches/board_thumbs/$nb_archivo";
        $ruta_original= "site/uploads/".$fl_instituto."/USER_".$fl_alumno."/sketches/original/$nb_archivo";
        
        
        
        
        
       
        if(($ext == "ogg")||($ext=='mp4')||($ext=='mov')||($ext=='m3u8')){
            $ruta_thumbs="site/uploads/gallery/thumbs/vanas-board-video-default.jpg";
            
        }   
		
		 $href .="<a style='text-decoration:none !important;' href='javascript:void(0);' onclick=Buscar$contador();><img src='$ruta_thumbs' class='superbox-img ' style='border:solid 1px #8a8888;width:40px; height:40px;'/> </a>";
              
         $href.="
		    <script>
			    function Buscar$contador(){          
                     var elemento = $('#dialog-trabajo');          
                     elemento.empty();
                     elemento.dialog('open');
			         elemento.dialog('option', 'width', 380);
                     elemento.dialog('option', 'height', 450);

                     var fl_entregable_sp=$fl_entregable_sp;
			         var fl_alumno=$fl_alumno;
						$.ajax({
						    type: 'POST',
						    url: 'site/presenta_trabajo.php',
						    data: 'fl_entregable_sp='+fl_entregable_sp+
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
			</script>";   
    
				
        
        
        
        
        switch($fg_tipo){
            case"A":  

				$QueA="SELECT ds_animacion FROM c_leccion_sp 
					   WHERE fl_leccion_sp=$fl_leccion_sp ";
			    $rowA=RecuperaValor($QueA);
				$ds_descripcionA=str_texto($rowA['ds_animacion']);
                $archivosA.=" $href &nbsp;";
                $contadorA++;
				
				if($contadorA==1){
						$href_infoA ="<a href='javascript:void(0);'  data-toggle='modal' data-target='#exampleModal' onclick='BuscarInfo$contador();' ><i class='fa fa-file-text' style='color:#333;' aria-hidden='true'></i></a>";
						$href_infoA.="
						
						 <script>
							function BuscarInfo$contador(){  
								var fl_leccion_sp=$fl_leccion_sp;			
								var fg_tipo='$fg_tipo';
								
								 $.ajax({
								 type: 'POST',
								 url: 'site/muestra_info_assigment.php',
								 data: 'fl_leccion_sp='+fl_leccion_sp+
									   '&fg_tipo='+fg_tipo,
								 async: false,
								 success: function (html) {
									 $('#muestra_info').html(html);

								 }
								 });
							
							}
							
						</script>	
						
						
						";
				    
				
				
				}
				
				
				
            break;
            case"AR": 
			    $QueryAR="SELECT ds_ref_animacion FROM c_leccion_sp  WHERE fl_leccion_sp=$fl_leccion_sp ";
                $rowAR=RecuperaValor($QueryAR);
				$ds_descripcionAR=str_texto($rowAR['ds_ref_animacion']);
				$archivosAR.="$href &nbsp;";
                $contadorAR++;
				if($contadorAR==1){
				
					$href_infoAR ="<a href='javascript:void(0);'  data-toggle='modal' data-target='#exampleModal' onclick='BuscarInfo$contador();' ><i class='fa fa-file-text' style='color:#333;' aria-hidden='true'></i></a>";
							$href_infoAR.="
							
							 <script>
								function BuscarInfo$contador(){  
									var fl_leccion_sp=$fl_leccion_sp;			
									var fg_tipo='$fg_tipo';
									
									 $.ajax({
									 type: 'POST',
									 url: 'site/muestra_info_assigment.php',
									 data: 'fl_leccion_sp='+fl_leccion_sp+
										   '&fg_tipo='+fg_tipo,
									 async: false,
									 success: function (html) {
										 $('#muestra_info').html(html);

									 }
									 });
								
								}
								
							</script>	
							
							
							";
				
				
				}
				
				
				
                break;
            case"S":  
				$QueryS="SELECT ds_no_sketch FROM c_leccion_sp  WHERE fl_leccion_sp=$fl_leccion_sp ";
                $rowS=RecuperaValor($QueryS);
				$ds_descripcionS=str_texto($rowS['ds_no_sketch']);
                $archivosS.="$href &nbsp;";
                $contadorS++;
				if($contadorS==1){
				
					$href_infoS ="<a href='javascript:void(0);'  data-toggle='modal' data-target='#exampleModal' onclick='BuscarInfo$contador();' ><i class='fa fa-file-text' style='color:#333;' aria-hidden='true'></i></a>";
							$href_infoS.="
							
							 <script>
								function BuscarInfo$contador(){  
									var fl_leccion_sp=$fl_leccion_sp;			
									var fg_tipo='$fg_tipo';
									
									 $.ajax({
									 type: 'POST',
									 url: 'site/muestra_info_assigment.php',
									 data: 'fl_leccion_sp='+fl_leccion_sp+
										   '&fg_tipo='+fg_tipo,
									 async: false,
									 success: function (html) {
										 $('#muestra_info').html(html);

									 }
									 });
								
								}
								
							</script>	
							
							
							";
				
				}
				
                break;
            case"SR": 
			    $QuerySR="SELECT ds_ref_sketch FROM c_leccion_sp  WHERE fl_leccion_sp=$fl_leccion_sp ";
                $rowSR=RecuperaValor($QuerySR);
				$ds_descripcionSR=str_texto($rowSR['ds_ref_sketch']);
                $archivosSR.="$href &nbsp;";
                $contadorSR++;
				if($contadorSR==1){
				
					$href_infoSR ="<a href='javascript:void(0);'  data-toggle='modal' data-target='#exampleModal' onclick='BuscarInfo$contador();' ><i class='fa fa-file-text' style='color:#333;' aria-hidden='true'></i></a>";
							$href_infoSR.="
							
							 <script>
								function BuscarInfo$contador(){  
									var fl_leccion_sp=$fl_leccion_sp;			
									var fg_tipo='$fg_tipo';
									
									 $.ajax({
									 type: 'POST',
									 url: 'site/muestra_info_assigment.php',
									 data: 'fl_leccion_sp='+fl_leccion_sp+
										   '&fg_tipo='+fg_tipo,
									 async: false,
									 success: function (html) {
										 $('#muestra_info').html(html);

									 }
									 });
								
								}
								
							</script>	
							
							
							";
				
				
				}
				
                break;
        }
        
        
   
      
        
        $href="";
        $href_info="";    
    }
 ?>


 
 
<!----Presntamos modal para info ///sketch, reference etc.--->
<!-- Modal -->
<!---<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button>--->

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="muestra_info">

    </div>
  </div>
</div>
 
 
 
<?php 

    #Verificamos check seleccionados en backend   | asiigment | assigment refrence| sketch |sketch refrence | 
    $Query10="SELECT fg_animacion,fg_ref_animacion,no_sketch,fg_ref_sketch FROM c_leccion_sp WHERE fl_leccion_sp=$fl_leccion_sp   ";
    $row10=RecuperaValor($Query10);
    $fg_assigment=$row10[0];
    $fg_asigment_reference=$row10[1];
    $fg_sketch=$row10[2];
    $fg_sketch_reference=$row10[3];    
    
    

     $div_img="<hr />
                 <div class='row'>
                 ";
 
                 #solo muestra,si tiene imagen.
                if($fg_assigment==1){
                     
                        if(empty($contadorA))
                           $no_foundA="<strong class='txt-color-red' style='float:left;'><i class='fa fa-times'></i> ".ObtenEtiqueta(1968)."  ".ObtenEtiqueta(1993)." </strong> ";
     
                           $div_img.="<div class='col-md-3'>

                                                <div class='panel panel-default'>
                                
				                                    <div class='panel-body '>
					                                    <div class='who clearfix'>
											                <span class='name col-md-6 text-left'><b>".ObtenEtiqueta(1968)."</b></p></span>
															<span class='name col-md-6 text-right'>$href_infoA </span>
															<br/>
                                                             $no_foundA
															<br/>					
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
								$no_foundAR="<strong class='txt-color-red' style='float:left;'><i class='fa fa-times'></i> ".ObtenEtiqueta(1969)." ".ObtenEtiqueta(1993)."</strong> ";
     
     
								$div_img.="
									<div class='col-md-3'>
												<div class='panel panel-default'>  
													<div class='panel-body '>
														<div class='who clearfix'>
															<span class='name col-md-6 text-left'><b>".ObtenEtiqueta(1969)."</b></p></span>
															<span class='name col-md-6 text-right'>$href_infoAR</span>
															<br/>
															$no_foundAR	
															<br/>
															<hr>
														</div>
															   $archivosAR  
													</div>
												</div>
									</div>";
						}
 
 
						if( ($fg_sketch<>0) ){
     
							if(empty($contadorS)){
								$no_foundS="<strong class='txt-color-red' style='float:left;'><i class='fa fa-times'></i> ".ObtenEtiqueta(1970)." ".ObtenEtiqueta(1993)."</strong> ";
							}
							$div_img.="
                            <div class='col-md-3'>
                                <div class='panel panel-default'>  
				                    <div class='panel-body '>
					                    <div class='who clearfix'>
						                   
										 <span class='name col-md-6 text-left'><b>".ObtenEtiqueta(1970)."</b></p></span>
										 <span class='name col-md-6 text-right'>$href_infoS</span>
										 <br/>
                                           $no_foundS
										 <br/>
						                 <hr>
					                    </div>
                                          $archivosS 
				                    </div>
			                    </div>
                            </div>";
            
						}  
 
						if(($fg_sketch_reference==1) ){
			 
								if(empty($contadorSR))
									$no_foundSR="<strong class='txt-color-red' style='float:left;'><i class='fa fa-times'></i> ".ObtenEtiqueta(1970)." ".ObtenEtiqueta(1993)."</strong> ";  
									
								 $div_img.="           
										<div class='col-md-3'>
											 <div class='panel panel-default'>  
												<div class='panel-body '>
												   <div class='who clearfix'>
													  
														<span class='name col-md-6 text-left'><b>".ObtenEtiqueta(1971)."</b></p></span>
														<span class='name col-md-6 text-right'>$href_infoSR</span>
														<br/>
														 $no_foundSR
														 <br/>&nbsp;
														<hr>
												   </div>
													  $archivosSR
												 </div>
											  </div>
										</div> 
								";
						}
                 
                 # Buscamos los archivos extras del alumno en la leccion
                 $Queryg  = "SELECT COUNT(*) FROM k_worksfiles a, c_usuario b ";
                 $Queryg .= "WHERE a.fl_alumno=b.fl_usuario AND a.fl_alumno=".$fl_alumno."  AND a.fl_leccion=".$fl_leccion_sp." AND a.fg_campus='0' ";
                 $Queryg .= "ORDER BY fe_file ";
                 $rwg = RecuperaValor($Queryg);
                 $tot = $rwg[0];
                 if(!empty($tot)){
                   $div_img.="
                   <div class='col-md-3'>
                    <div class='panel panel-default'>
                      <div class='panel-body '>
                        <div class='who clearfix'>                          
                          <span class='name'><b>".ObtenEtiqueta(2215)."</b></p></span><br/>$no_works
                          <hr>
                          <i class='fa fa-files-o fa-2x txt-color-blue cursor-pointer' onclick='works_fame(".$fl_alumno.", ".$fl_leccion_sp.", ".$fl_maestro.");'></i>
                         </div>
                        </div>
                       </div>
                      </div>
                     <script>
                     function works_fame(std, lec, tea){
                      var elemento = $('#dialog-trabajo');          
                      elemento.empty();
                      elemento.dialog('open');
                      elemento.dialog('option', 'width', 750);
                      elemento.dialog('option', 'height', 600);
                      $.ajax({
                          type: 'POST',
                          url: 'site/presenta_trabajo.php',
                          data: 'fl_leccion_sp='+lec+
                              '&fl_alumno='+std+
                              '&fl_teacher='+tea,
                             
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



<?php


#Recuermaos el nombre 
$Query="SELECT  ds_titulo,B.nb_programa FROM  c_leccion_sp A
JOIN c_programa_sp B ON B.fl_programa_sp=A.fl_programa_sp
WHERE A.fl_leccion_sp=$fl_leccion_sp ";
$row=RecuperaValor($Query);
$ds_leccion=str_texto($row[0]);
$ds_programa=str_texto($row[1]);

$nb_rubric=$ds_leccion;
    

?>

        <style>
		 /*stylos para slider*/ 
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

		/*estilos para el textatera desaibiltado*/
		input[type=text]:disabled {
			background: #fff !important;
		}
		/***iconoimagen*/
		.popover{
			
		max-width: 490px !important;
		}

		/**para los text desabilitados*/
		.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {

		background-color: #fff !important;
		}

		.border{
									border: 2px solid  #3194DA;
						/*         box-shadow: 0 0 8px #007DC3;
			transition: box-shadow 0.2s ease-in-out;
							*/	}
			.col-md-2 {

		   padding-left:2px;
		   padding-right:2px;
			}
			.hr {
				margin-bottom: 1px !important;
			}


		 /*Para aumentar tamaño de las imagenes solo con pasar el mouse*/
				.zoomimg {position: relative; z-index: 150; }
				.zoomimg:hover{ background-color: transparent; z-index: 150; }
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


		.hr{
		margin-bottom: 1px;

		}

		.chart {
			/* height: 220px; */
			margin: auto !important;
		}

		.easyPieChart {
			position: relative;
			text-align: center !important;
		}
                      </style>
   


            <div class="row padding-10">
              <div class=" col col-md-12 col-sm-12 col-lg-5">
                  <?php
                    Profile_pic_FAME($fl_alumno, $fl_programa_sp, $no_semana, $fl_maestro);
                    if(isset($fe_calificado)){
                      echo "<b>".ObtenEtiqueta(1678).":</b> ".$fe_calificado."<br >";
                    }
                    # Boton se presenta cuando ya tiene una calificacion el usuario que puede mejorar su calificacion cuando esta activado
                
                  ?>
              </div>
              <div class=" col col-md-12 col-sm-12 col-lg-7 text-align-right">
                  
				<?php
				  if(!empty($fg_esta_calificado_teacher)){
				?>
						<div class="padding-top-10 padding-bottom-10">                  
							<button id="btn_increse" rel="tooltip" data-placement="top" data-original-title="<?php echo ObtenEtiqueta(2002); ?>" class="btn 
							  <?php
							  if(empty($fg_increase_grade)){
								echo "btn-primary";
								$inc_grade = "1";
							  }
							  else{
								echo "btn-success";
								$inc_grade = "0";
							  }
							 ?>
								" onclick="increse_grade(<?php echo $fl_entrega_semanal_sp.",".$inc_grade; ?>);"><strong><i id="icon_increse" class="fa 
							<?php
							  if(empty($fg_increase_grade)){                    
								echo "fa-smile-o";
								$lbl_increse = ObtenEtiqueta(2003);
							  }
							  else{
								echo "fa-check";                  
								$lbl_increse = ObtenEtiqueta(2004);
							  }
							?>"></i></strong> <strong id="lbl_increse"><?php echo $lbl_increse; ?></strong> </button>

							<!---colocamos el boton de reset de asignar calificacion--->
							<?php 
							#Verificamos si la leccion ha sufrido modificaciones con respecto ala rubric.
							$tiene_modificaciones_rubric=VerificaCambiosRubricActual($fl_leccion_sp,$fl_alumno);
							?>
							<br /><br />
							<?php 
							 
							 if($tiene_modificaciones_rubric){
								 //echo"<span class='label label-success' style='cursor:pointer;' onclick='ResetCalificacion($fl_alumno,$fl_leccion_sp,$fl_programa_sp,$no_semana,$fl_entrega_semanal_sp);'><strong><i class='fa fa-pencil'></i>&nbsp;".ObtenEtiqueta(2045)."</strong></span> ";
								 echo"<div class='well well-lg' style='width: 60%;float: right;'>";
								 echo"<p class='text-center' style='color:red;'>".ObtenEtiqueta(2046)."</p>";
								 echo"<div class='col-md-6'>";
								 echo"	<button class='btn btn-danger' data-toggle='modal' data-target='#peligro'>".ObtenEtiqueta(2045)."</button>";
								 echo"</div>";
								 echo"<div class='col-md-6'>";
								 echo"<a href='javascript:void(0);' style='margin-top: 5px;' data-toggle='modal' data-target='#muestraRubricActual'> <i class='fa fa-search' aria-hidden='true'></i>&nbsp;".ObtenEtiqueta(2047)." </a>";
								 echo"</div>";

								echo"</div>";
							 }
							?>

							<!---Modal que muestra warning---->
							<div class="modal fade " id="peligro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
								  <div class="modal-dialog" role="document">
									<div class="modal-content">
									  <div class="modal-header text-left">
										<h5 class="modal-title " ><i class='fa fa-pencil' aria-hidden='true'></i>&nbsp;<?php echo ObtenEtiqueta(2045); ?></h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px;">
										  <span aria-hidden="true">&times;</span>
										</button>
									  </div>
									  <div class="modal-body text-center"  >                                     
												  <h1><?php echo ObtenEtiqueta(2048); ?></h1> <h1 style="color:red;"><?php echo ObtenEtiqueta(2049); ?></h1>

									  </div>
									  <div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;Cancel</button>
										<button type="button" class="btn btn-primary" data-dismiss="modal"  <?php echo"onclick='ResetCalificacion($fl_alumno,$fl_leccion_sp,$fl_programa_sp,$no_semana,$fl_entrega_semanal_sp);' "; ?>  ><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;Accept</button>
									  </div>
									</div>
								  </div>
						    </div>
							<!-- muestra modal -->
							<!----End modal--->

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
											
											#Verifica si la leccion es creada por el instituto.
										    $Query="SELECT b.fl_instituto FROM c_leccion_sp a
													JOIN c_programa_sp b ON a.fl_programa_sp=b.fl_programa_sp where a.fl_leccion_sp=$fl_leccion_sp ";
										    $rol=RecuperaValor($Query);
										    $fl_leccion_de_instituto=$rol['fl_instituto'];
   
   
											
											
											#Recuperamos todos los criterios
											$Query="SELECT fl_criterio, no_valor FROM k_criterio_programa_fame WHERE fl_programa_sp =$fl_leccion_sp ORDER BY no_orden ASC 	";
											$rs_prin = EjecutaQuery($Query);
                                            $rubric=NULL;
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
														            <div style='font-size:18px; font-weight:bold; -webkit-transform: rotate(-90deg);margin-top: 111px;width: 215px;margin-left: -63px;'>$nb_criterio</div>
															                  
									                            </div>

														    </div>";
                                        
                                        
												$Query="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion_criterio ";
												$Query.="WHERE 1=1 ";
												if(!empty($fl_leccion_de_instituto)){
												$Query.=" AND fl_instituto=$fl_instituto ";
												}else{
												$Query.=" AND fl_instituto is null ";	
													
												}
												$Query.="ORDER BY no_equivalencia ASC ";
												$rs = EjecutaQuery($Query);
                                                $contador=0;
												for($i=1;$row=RecuperaRegistro($rs);$i++) {
													$fl_calificacion_criterio=$row['fl_calificacion_criterio'];
													$cl_calificacion=$row['cl_calificacion'];
													$ds_calificacion=$row['ds_calificacion'];
													$fg_aprobado=$row['fg_aprobado'];
													$no_equivalencia=$row['no_equivalencia'];
													$no_min= number_format($row['no_min']);
													$no_max=number_format($row['no_max']);
                
													if($no_max==0){
														$ds_equivalencia="No Uploaded";
													}else{
														$ds_equivalencia=$no_min."% - ".$no_max."%"." ($cl_calificacion)";
													}
                
													#Recupermaos la descripcion que tiene actualmente.
													$Query_c="SELECT ds_descripcion,fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
													$row_c=RecuperaValor($Query_c);
													$ds_desc=$row_c[0];
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
																			 </div>";
                                                        if( (!empty($fg_esta_calificado_teacher)) && (empty($fg_tab_assigment)) ) {
                                                        $rubric .= "
																			<div class='form-group text-left' style='padding-left:5px; padding-right:5px;'>
																			   <div id='desc_$contador'></div>
																			   <hr>									
																				<div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
																				  <small class='text-muted'><i>$ds_desc</i></small>              
																				</div>
																	  
																			</div>";
                                                        }
                                                        $rubric .= "
																		</div>
																	</div>
																</div>";

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
																</script>";                                                

												}
                                            
                                            
														$rubric .= "   <div class='col-md-1' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'></div>";
														$rubric .= "</div>";
														$rubric .= "<br/>";        

											}
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

						</div>
				<?php    
                }
				?>
              </div>
            </div>
            <div class="col-md-12">
	             <?php
                    #Presenta galeria de imaganes
                    echo $div_img;
                ?>
                <br/>                
            </div>
            <br />	
	<!-------------------------------iNIIA mODAL---------------------------------->
    <!-- Div que muetsra los trabajos -->
    <style>
    .ui-dialog{
      position:fixed !important;
    }
    .ui-icon{      
      background-image: url(img/ui-icons_222222_256x240.png);
    }    
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
    // Funcion para que el usuario pueda mejora la calificacion
    function increse_grade(entregable, fg_increase_grade=0){
      $.ajax({
        type: 'POST',
        url: 'site/guardar_comentarios_criterio.php',
        data: 'increse_grade=1'+
              '&fl_entrega_semanal_sp='+entregable+
              '&fg_increase_grade='+fg_increase_grade,             
        async: true,
        success: function () {
          // Modificamos los botones
          if(fg_increase_grade==0){
            $("#btn_increse").removeClass('btn-success').addClass('btn-primary').removeAttr('onclick').attr('onclick','increse_grade('+entregable+',1)');
            $("#icon_increse").removeClass('fa-check').addClass('fa-smile-o');
            $("#lbl_increse").empty().append('<?php echo ObtenEtiqueta(2003); ?>');
          }
          else{        
            $("#btn_increse").removeClass('btn-primary').addClass('btn-success').removeAttr('onclick').attr('onclick','increse_grade('+entregable+',0)');
            $("#icon_increse").removeClass('fa-smile-o').addClass('fa-check');
            $("#lbl_increse").empty().append('<?php echo ObtenEtiqueta(2004); ?>');
          }
        }
      });
      
    }
    </script>
	<!-----------------------------End modal-------------------------------------->
			
			
			
			
			
			
			
		
			
			
			
    <div class="row">
        <div class="col-md-12">
           <div class="panel panel-default" style="border-radius:20px;">
		        <div class="panel-body text-center">
		        <p style="font-size:20px;"><?php echo $nb_rubric;?></p>
		        </div>
	        </div>
        </div>
    </div>


            <?php

    $contador=0;
    $contador_border=0;
    $contador_slider=0;
    $fl_identificador= rand(1, 300);/*identificador del documento.*/
    
    
    
    
           #Por X error de declarar una tabla,la columna fl_programa_sp de la tabla k_criterio_programa_fame corresponde a fl_leccion de la tabla c_leccion_sp y quien posteriormente vea este codigo , pues nose exactamente  lo que hace pero funciona.:  
            /**
             * MJD IMPORTANTE:
             * Por X error de declarar una tabla,la columna fl_programa_sp de la tabla k_criterio_programa_fame corresponde a fl_leccion de la tabla c_leccion_sp:
             */ 

		 
	 if(empty($fg_esta_calificado_teacher)){#		 
	
	             #Recuperamos todos los criterios
	             $Query="SELECT  K.fl_criterio,T.nb_criterio,K.no_valor,C.fl_leccion_sp,C.fl_programa_sp
                        FROM  k_criterio_programa_fame K
                        JOIN c_leccion_sp C ON C.fl_leccion_sp =K.fl_programa_sp
                        JOIN c_criterio T ON T.fl_criterio=K.fl_criterio WHERE K.fl_programa_sp=$fl_leccion_sp 	";
     }else{
     
     
     
                 #Recuperamos todos los criterios
                 $Query="SELECT  K.fl_criterio,T.nb_criterio,K.no_valor,C.fl_leccion_sp,C.fl_programa_sp
                                FROM  k_criterio_programa_alumno_fame K
                                JOIN c_leccion_sp C ON C.fl_leccion_sp =K.fl_programa_sp
                                JOIN c_criterio T ON T.fl_criterio=K.fl_criterio WHERE K.fl_programa_sp=$fl_leccion_sp AND K.fl_usuario_sp=$fl_alumno	";
     
              
     
     }

			
			
			
			
    $rs = EjecutaQuery($Query);
    $existe_rubric=CuentaRegistros($rs);
    $registros = CuentaRegistros($rs);
    
    if($existe_rubric>0){
    
    # Variable initialization to prevent errors
    $contador=0;
    $contador_border1=0;
    $contador_slider=0;
    $contador_img=0;
    $contador_border2=0;
    $contador_border3=0;
    $contador_border4=0;
    $contador_border5=0;
    
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
        $contador++;
        $contador_border1 ++;
        $contador_slider ++;
        $contador_img ++;
		
		
		
		if($contador_img == 1)
		$top="30px";
		else
		$top="-530px";
		
		
        
        $fl_criterio=$row['fl_criterio'];
        $nb_criterio=str_texto($row['nb_criterio']);
        $no_porcentaje_criterio=$row['no_valor'];
        $fl_leccion_sp=$row['fl_leccion_sp'];
        $fl_programa_sp=$row['fl_programa_sp'];
        
            #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
            $Query1="SELECT C.fl_calificacion_criterio,C.ds_calificacion, ds_descripcion ,C.no_min,C.no_max,C.cl_calificacion,K.fl_criterio_fame
									     FROM k_criterio_fame K
									     JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
									     WHERE fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=5 ";
            $row=RecuperaValor($Query1);
            $ds_calificacion1=$row[1];
            $ds_descripcion1=$row[2];
            $no_min1=$row[3];
            $no_max1=$row[4];
            $cl_calificacion1=$row[5];
            $fl_criterio_fame1=$row[6];
        
        
            if($no_max1==0)
                $ds_equivalencia1="No Uploaded";
            else
                $ds_equivalencia1=number_format($no_min1)." % -".number_format($no_max1)." % ($cl_calificacion1)";
        
            #Recuperamos las imagenes por calificacion
            $Query="SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame1 ";
            $row=RecuperaValor($Query);
            $nb_archivo_criterio1=!empty($row[0])?$row[0]:NULL;
        
            $src_img1="../AD3M2SRC4/images/rubrics/".$nb_archivo_criterio1;
        
        
        
        
        
        
        
        
        
            #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
            $Query2="SELECT C.fl_calificacion_criterio,C.ds_calificacion, ds_descripcion ,C.no_min,C.no_max,C.cl_calificacion,K.fl_criterio_fame
									     FROM k_criterio_fame K
									     JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
									     WHERE fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=4 ";
            $row=RecuperaValor($Query2);
            $ds_calificacion2=$row[1];
            $ds_descripcion2=$row[2];
            $no_min2=$row[3];
            $no_max2=$row[4];
            $cl_calificacion2=$row[5];
            $fl_criterio_fame2=$row[6];
        
        
        
            if($no_max2==0)
                $ds_equivalencia2="No Uploaded";
            else
                $ds_equivalencia2=number_format($no_min2)."% - ".number_format($no_max2)."% ($cl_calificacion2)";
        
            #Recuperamos las imagenes por calificacion
            $Query="SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame2 ";
            $row=RecuperaValor($Query);
            $nb_archivo_criterio2=!empty($row[0])?$row[0]:NULL;
        
            $src_img2="../AD3M2SRC4/images/rubrics/".$nb_archivo_criterio2;
        
        
        
        
        
        
            #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
            $Query3="SELECT C.fl_calificacion_criterio,C.ds_calificacion, ds_descripcion ,C.no_min,C.no_max,C.cl_calificacion,K.fl_criterio_fame
									     FROM k_criterio_fame K
									     JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
									     WHERE fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=3 ";
            $row=RecuperaValor($Query3);
            $ds_calificacion3=$row[1];
            $ds_descripcion3=$row[2];
            $no_min3=$row[3];
            $no_max3=$row[4];
            $cl_calificacion3=$row[5];
            $fl_criterio_fame3=$row[6];
        
        
            if($no_max3==0)
                $ds_equivalencia3="No Uploaded";
            else
                $ds_equivalencia3=number_format($no_min3)."% - ".number_format($no_max3)."% ($cl_calificacion3)";
        
        
        
        #Recuperamos las imagenes por calificacion
        $Query="SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame3 ";
        $row=RecuperaValor($Query);
        $nb_archivo_criterio3=!empty($row[0])?$row[0]:NULL;
        
        $src_img3="../AD3M2SRC4/images/rubrics/".$nb_archivo_criterio3;
        
        
        
        
        
        #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
        $Query4="SELECT C.fl_calificacion_criterio,C.ds_calificacion, ds_descripcion ,C.no_min,C.no_max,C.cl_calificacion,K.fl_criterio_fame
									 FROM k_criterio_fame K
									 JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
									 WHERE fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=2 ";
        $row=RecuperaValor($Query4);
        $ds_calificacion4=$row[1];
        $ds_descripcion4=$row[2];
        $no_min4=$row[3];
        $no_max4=$row[4];
        $cl_calificacion4=$row[5];
        $fl_criterio_fame4=$row[6];
        
        
        if($no_max4==0)
            $ds_equivalencia4="No Uploaded";
        else
            $ds_equivalencia4=number_format($no_min4)."% - ".number_format($no_max4)."% ($cl_calificacion4)";
        
        #Recuperamos las imagenes por calificacion
        $Query="SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame4 ";
        $row=RecuperaValor($Query);
        $nb_archivo_criterio4=!empty($row[0])?$row[0]:NULL;
        
        $src_img4="../AD3M2SRC4/images/rubrics/".$nb_archivo_criterio4;






        
        
        #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
        $Query5="SELECT C.fl_calificacion_criterio,C.ds_calificacion, ds_descripcion ,C.no_min,C.no_max,C.cl_calificacion,K.fl_criterio_fame
									 FROM k_criterio_fame K
									 JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
									 WHERE fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=1 ";
        $row=RecuperaValor($Query5);
        $ds_calificacion5=$row[1];
        $ds_descripcion5=$row[2];
        $no_min5=$row[3];
        $no_max5=$row[4];
        $cl_calificacion5=$row[5];
        $fl_criterio_fame5=$row[6];
        
        
        if($no_max5==0)
            $ds_equivalencia5="No Uploaded";
        else
            $ds_equivalencia5=number_format($no_min5)."% - ".number_format($no_max5)."% ($cl_calificacion5)";
        
        
        #Recuperamos las imagenes por calificacion
        $Query="SELECT nb_archivo FROM c_archivo_criterio WHERE fl_criterio_fame=$fl_criterio_fame5 ";
        $row=RecuperaValor($Query);
        $nb_archivo_criterio5=!empty($row[0])?$row[0]:NULL;
        
        $src_img5="../AD3M2SRC4/images/rubrics/".$nb_archivo_criterio5;
        
        if($contador==1){
        $posicion_img="bottom";
        }else{
        $posicion_img="top";
        } 
        
        ?>


        

    
			
	<div class="row">
				



			<div class="col-md-1" style="padding-right: 0px;">
				
			
				<div class="col-md-12" style="padding-left: 1px;padding-right: 0px;" >
				
										<div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'>
											 Criterion 
										</div>
				
								        <br>
										  
								<div class="panel panel-default" style="height:405px;">
										<div class="panel-body text-center" >
											<span  style="color:#8FCAE5;font-size:15px;font-weight:none !important;"><?php echo $no_porcentaje_criterio." %";  ?> </span>   <p>&nbsp;</p>

                                
											
											<div class="form-group"><br/>
												<label for="comment" style="writing-mode: vertical-lr;transform: rotate(180deg);font-size:16px; margin-top: 75px; font-weight:bold;"><?php echo $nb_criterio; ?></label>
												
											</div>

										</div>
								</div>
										  
										  
										  
									  
										  
				</div>
				
										
										
										
          
			
			</div>
				
			<div class="col-md-11" style="padding-left: 1px;">
                <div class="col-md-2" >



						<div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'><?php echo $ds_calificacion1;?>&nbsp;

                            <?php if (!empty($nb_archivo_criterio1)){ 
                                      
                                      
                                      $icono = "<a class='zoomimg' href='javascript:void(0);'> 
												<i class='fa fa-file-picture-o' style=color:#333;'></i>
												<span style='left:-300px;'>
												  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-top: $top;'>
													<div class='modal-content' style='width:500px;height:500px;'>
													  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
														<img class='superbox-current-img' src='$src_img1' style='width:494px;height:490px;'>
													  </div>
													</div>
												  </div>
												</span>
											 </a> ";    
                                      
                                      
                           echo $icono;       

                           } ?>
						   
						</div>
						
                    
                    
                    <br/>
						<div class="panel panel-default" style="height:405px;text-align:center !important;">
                            <div class="panel-body" style="text-align:center !important;" id="divborder_cero<?php echo $contador_border1; ?>">
                                <span  style="color:#8FCAE5;font-size:15px;"><?php echo $ds_equivalencia1;  ?> </span>   <p>&nbsp;</p>
                                <div style="text-align: center;">
                                    <div class="chart " data-percent="<?php echo $no_max1; ?>" id="easy-pie-chart<?php echo $contador; ?>">
                                        <span class="percent" style="font:18px Arial;font-weight:none !important;"><?php echo number_format($no_max1); ?></span>
                                    </div>
                                <hr />
                                </div>    
                                <div class="form-group"><br/>
								   
								    <textarea class="form-control" rows="5"   style="resize:none !important;color:#999; font-style: italic;" maxlength="130" disabled><?php echo $ds_descripcion1;?></textarea>
								</div>

                            </div>
                        </div>
               </div>

                <script>
                    $(document).ready(function () {

                        $('#easy-pie-chart<?php echo $contador; ?>').easyPieChart({
                            animate: 2000,
                            scaleColor: false,
                            lineWidth: 7.5,
                            lineCap: 'square',
                            size: 100,
                            trackColor: '#EEEEEE',
                            barColor: '#B7B7B7'
                        });

                        $('#easy-pie-chart<?php echo $contador; ?>').css({
                            width: 100 + 'px',
                            height: 100 + 'px'
                        });
                        $('#easy-pie-chart<?php echo $contador; ?> .percent').css({
                            "line-height": 100 + 'px'
                        })



                    });
                </script>





            <?php 
        $contador=$contador +1;
        $contador_border2 ++;	
            ?>

            <div class="col-md-2" >
                <div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'><?php echo $ds_calificacion2;?>&nbsp;
                        <?php  

                              if (!empty($nb_archivo_criterio2)){
							  
							  
                                  $icono2 = "<a class='zoomimg' href='javascript:void(0);'> 
												<i class='fa fa-file-picture-o' style=color:#333;'></i>
												<span style='left:-300px;'>
												  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-top: $top;'>
													<div class='modal-content' style='width:500px;height:500px;'>
													  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
														<img class='superbox-current-img' src='$src_img2' style='width:494px;height:490px;'>
													  </div>
													</div>
												  </div>
												</span>
											 </a> ";    
                                      
                                      
                           echo $icono2;
							  
							  
							  
                        } ?>
                    
                 </div>	
                 <br/>

                <div class="panel panel-default" style="height:405px;" id="divborder_dos<?php echo $contador_border2; ?>">
					<div class="panel-body text-center">
					<span href="" style="color:#8FCAE5;font-size:15px;"><?php echo $ds_equivalencia2;  ?> </span>   <p>&nbsp;</p>
						
                        <div class="chart" data-percent="<?php echo $no_max2; ?>" id="easy-pie-chart<?php echo $contador; ?>">
                                    <span class="percent" style="font:18px Arial;font-weight:none !important;"><?php echo number_format($no_max2); ?></span>
                         </div>

                        <hr />

						<div class="form-group" ><br/>
							<!--<label for="comment">Comment:</label>-->
							<textarea class="form-control" rows="5"   style="resize:none !important;color:#999;font-style: italic;" maxlength="130" disabled><?php echo $ds_descripcion2  ?></textarea>
						</div>
														
					</div>
				 </div>
            </div>
                <script>
                    $(document).ready(function () {
                        $('#easy-pie-chart<?php echo $contador; ?>').easyPieChart({
                                animate: 2000,
                                scaleColor: false,
                                lineWidth: 7.5,
                                lineCap: 'square',
                                size: 100,
                                trackColor: '#EEEEEE',
                                barColor: '#B7B7B7'
                            });

                            $('#easy-pie-chart<?php echo $contador; ?>').css({
                                width: 100 + 'px',
                                height: 100 + 'px'
                            });
                            $('#easy-pie-chart<?php echo $contador; ?> .percent').css({
                                "line-height": 100 + 'px'
                            })

                        });
                </script>

                <?php 
        $contador=$contador +1; 								
        $contador_border3 ++;
        
                ?>


             <div class="col-md-2" >
                     <div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'><?php echo $ds_calificacion3;?>&nbsp;
						<?php  
                              
                              if (!empty($nb_archivo_criterio3)){

							  
                                  $icono = "<a class='zoomimg' href='javascript:void(0);'> 
												<i class='fa fa-file-picture-o' style=color:#333;'></i>
												<span style='left:-300px;'>
												  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-top: $top;'>
													<div class='modal-content' style='width:500px;height:500px;'>
													  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
														<img class='superbox-current-img' src='$src_img3' style='width:494px;height:490px;'>
													  </div>
													</div>
												  </div>
												</span>
											 </a> ";    
                                      
                                      
                           echo $icono;
							  
							  
							  
                     
						 } ?>	
													
					</div>
                    <br/>
				    <div class="panel panel-default" style="height:405px;" id="divborder_tres<?php echo $contador_border3; ?>">
					    <div class="panel-body text-center">
					    <span href="" style="color:#8FCAE5;font-size:15px;"><?php echo $ds_equivalencia3;  ?> </span>   <p>&nbsp;</p>

						     <div class="chart" data-percent="<?php echo $no_max3; ?>" id="easy-pie-chart<?php echo $contador; ?>">
                                   <span class="percent" style="font:18px Arial;font-weight:none !important;"><?php echo number_format($no_max3); ?></span>
                             </div>
                            <hr />
						    <div class="form-group"><br/>
							    <!--<label for="comment">Comment:</label>-->
							    <textarea class="form-control" rows="5"   style="resize:none !important;color:#999;font-style: italic;" maxlength="130" disabled><?php echo $ds_descripcion3  ?></textarea>
						    </div>
														
					    </div>
				    </div>



              </div>


                <script>
                    $(document).ready(function () {
                        $('#easy-pie-chart<?php echo $contador; ?>').easyPieChart({
                            animate: 2000,
                            scaleColor: false,
                            lineWidth: 7.5,
                            lineCap: 'square',
                            size: 100,
                            trackColor: '#EEEEEE',
                            barColor: '#B7B7B7'
                        });

                        $('#easy-pie-chart<?php echo $contador; ?>').css({
                            width: 100 + 'px',
                            height: 100 + 'px'
                        });
                        $('#easy-pie-chart<?php echo $contador; ?> .percent').css({
                            "line-height": 100 + 'px'
                        })

                    });
                </script>

                <?php 
        
        $border_72=$contador_border;
        $contador=$contador +1; 
        $contador_border4 ++;
                ?>





               <div class="col-md-2" >
					<div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'><?php echo $ds_calificacion4;?>&nbsp;
						<?php 
                              if (!empty($nb_archivo_criterio4)){
							  
							  
                                  $icono = "<a class='zoomimg' href='javascript:void(0);'> 
												<i class='fa fa-file-picture-o' style=color:#333;'></i>
												<span style='left:-300px;'>
												  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-top: $top;'>
													<div class='modal-content' style='width:500px;height:500px;'>
													  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
														<img class='superbox-current-img' src='$src_img4' style='width:494px;height:490px;'>
													  </div>
													</div>
												  </div>
												</span>
											 </a> ";    
                                      
                                      
                           echo $icono;
							  
							  
                        } ?>	
					</div>
					<br/>
					<div class="panel panel-default" style="height:405px;" id="divborder_cuatro<?php echo $contador_border4; ?>">
						<div class="panel-body text-center">
						<span href="" style="color:#8FCAE5;font-size:15px;"><?php echo $ds_equivalencia4;  ?> </span>   <p>&nbsp;</p>
							
                             <div class="chart" data-percent="<?php echo $no_max4; ?>" id="easy-pie-chart<?php echo $contador; ?>">
                                   <span class="percent" style="font:18px Arial;font-weight:none !important;"><?php echo number_format($no_max4); ?></span>
                             </div>
                            <hr />
							<div class="form-group"><br/>
								<!--<label for="comment">Comment:</label>-->
								<textarea class="form-control" rows="5"   style="resize:none !important;color:#999;font-style: italic;" maxlength="130" disabled> <?php echo $ds_descripcion4  ?></textarea>
							</div>
														
						</div>
					</div>
            </div>



                <script>
                    $(document).ready(function () {
                        $('#easy-pie-chart<?php echo $contador; ?>').easyPieChart({
                            animate: 2000,
                            scaleColor: false,
                            lineWidth: 7.5,
                            lineCap: 'square',
                            size: 100,
                            trackColor: '#EEEEEE',
                            barColor: '#B7B7B7'
                        });

                        $('#easy-pie-chart<?php echo $contador; ?>').css({
                            width: 100 + 'px',
                            height: 100 + 'px'
                        });
                        $('#easy-pie-chart<?php echo $contador; ?> .percent').css({
                            "line-height": 100 + 'px'
                        })

                    });
                </script>

                <?php $contador=$contador +1; 
                      
                      $contador_border5 ++;
                ?>

                <div class="col-md-2" >
						<div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'><?php echo $ds_calificacion5;?>&nbsp;
							<?php
                                  if (!empty($nb_archivo_criterio5)){
								  
								  
								  
                                      $icono = "<a class='zoomimg' href='javascript:void(0);'> 
												<i class='fa fa-file-picture-o' style=color:#333;'></i>
												<span style='left:-300px;'>
												  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-top: $top;'>
													<div class='modal-content' style='width:500px;height:500px;'>
													  <div class='modal-body padding-5'  style='width:500px;height:500px;'>
														<img class='superbox-current-img' src='$src_img5' style='width:494px;height:490px;'>
													  </div>
													</div>
												  </div>
												</span>
											 </a> ";    
                                      
                                      
                           echo $icono;
								  
								  

                            } ?>	
						</div>
						<br/>
						<div class="panel panel-default" style="height:405px;" id="divborder_cinco<?php echo $contador_border5; ?>">
							<div class="panel-body text-center">
							<span href="" style="color:#8FCAE5;font-size:15px;font-weight:none !important;"><?php echo $ds_equivalencia5;  ?> </span>   <p>&nbsp;</p>
								
                                <div class="chart" data-percent="<?php echo $no_max5; ?>" id="easy-pie-chart<?php echo $contador; ?>">
                                   <span class="percent" style="font:18px Arial;"><?php echo number_format($no_max5); ?></span>
                                </div>
                                <hr />
								<div class="form-group"><br/>
									<!--<label for="comment">Comment:</label>-->
									<textarea class="form-control" rows="5"   style="resize:none !important;color:#999;font-style: italic;" maxlength="130" disabled><?php echo $ds_descripcion5  ?> </textarea>
								</div>
														
							</div>
						</div>
                </div>

                <script>
                    $(document).ready(function () {
                        $('#easy-pie-chart<?php echo $contador; ?>').easyPieChart({
                            animate: 2000,
                            scaleColor: false,
                            lineWidth: 7.5,
                            lineCap: 'square',
                            size: 100,
                            trackColor: '#EEEEEE',
                            barColor: '#B7B7B7'
                        });

                        $('#easy-pie-chart<?php echo $contador; ?>').css({
                            width: 100 + 'px',
                            height: 100 + 'px'
                        });
                        $('#easy-pie-chart<?php echo $contador; ?> .percent').css({
                            "line-height": 100 + 'px'
                        })

                    });
                </script>


<?php $contador=$contador +1; 

      
      
      
      #Recuperamos si sxite una calificacion asignada.
      $Query="SELECT ds_comentarios,no_porcentaje_equivalente,fe_modificacion FROM c_com_criterio_teacher WHERE fl_criterio=$fl_criterio AND fl_alumno=$fl_alumno  AND fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp   ";
      $row=RecuperaValor($Query);
      $ds_comentario_criterio=!empty($row[0])?$row[0]:NULL;
      $no_porcentaje_equivalente=!empty($row[1])?$row[1]:NULL;
      $fe_asignacion_califi=ObtenFechaFormatoDiaMesAnioHora(!empty($row[2])?$row[2]:NULL);
          
      if($ds_comentario_criterio=='undefined')
          $ds_comentario_criterio="";

      if(empty($fg_esta_calificado_teacher)){
          $fg_esta_calificado="";
          $no_porcentaj="0";
          $data_percentage="";
          $fe_asignacion_califi="";
      }else{
          $fg_esta_calificado=1;
          $no_porcentaj=$no_porcentaje_equivalente;
          $data_percentage="data-percent='$no_porcentaj' ";
          $fe_asignacion_califi="<h2 style='margin: 2px 0; line-height: 60%;font-size:20px;'><i><small>".ObtenEtiqueta(1680)." :<br/>$fe_asignacion_califi</small></i></h2>";
      }
	  
	  #solo se presenta la fecha de asignacion ,si tiene comentarios.
	  if(!empty($ds_comentario_criterio)){
	     $fe_asignacion_califi=$fe_asignacion_califi;
	  }else{
	     $fe_asignacion_califi="";
	  
	  }
	  
	  
	  
?>

						
										<div class="col-md-2" >
										        <div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'>
													<?php echo ObtenEtiqueta(1664);  ?>
												</div>
												<br/>
												<div class="panel panel-default" style="height:405px;">
													<div class="panel-body text-center">
														<span>&nbsp;</span><p>&nbsp;</p>
														
                                                         <div class="chart"   id="final_<?php echo $contador; ?>" <?php echo $data_percentage; ?>   >
                                                            <span class="percent" style="font:18px Arial;font-weight:none !important;" id="span_final<?php echo $contador; ?>"><?php echo $no_porcentaj;?></span>
                                                         </div>


														<hr />
														
														<div class="form-group">
														<span id="char<?php echo $contador; ?>" class="pull-left text-left hidden" style=""> </span>
														<a href="javascript:void(0);" class="btn btn-xs btn-default pull-right" style="border:0px;" Onclick="EditarDescripcion<?php echo $contador; ?>(<?php echo $fl_criterio; ?>);" style="top:-10px !important;"><i class="fa fa-pencil" ></i></a>
															
															<textarea class="form-control" rows="5"  id="desc_<?php echo $contador; ?>"  style="resize:none !important;" maxlength="130"  onkeydown="CuentaCaracteres<?php echo $contador; ?>()" onKeyUp="CuentaCaracteres<?php echo $contador; ?>()" ><?php echo $ds_comentario_criterio; ?></textarea>
														
                                                            <div class="text-left" id="muestra_save<?php echo $contador; ?>"><?php echo $fe_asignacion_califi; ?> </div>
														</div>
														<div class="form-group">
															<div class="col-md-6">
															
															<a class="btn btn-default btn-xs" style="font-size: 13px;" href="javascript:void(0);" id="btncancel<?php echo $contador; ?>" Onclick="CancelarEdicion<?php echo $contador; ?>(<?php echo $fl_criterio; ?>);">Cancel</a>
															</div>
														
															<div class="col-md-6"> 
															
																<a class="btn btn-primary btn-xs" style="font-size: 13px;" href="javascript:void(0);"  id="btnsave<?php echo $contador; ?>" Onclick="GuardarDescripcion<?php echo $contador; ?>(<?php echo $fl_criterio; ?>);">Save</a>
															</div>
														
														</div>
														
													
													
													</div>
												</div>
                                        </div>

												
												<script>

												    $(document).ready(function () {

												        $("#char<?php echo $contador; ?>").addClass('hidden');//contador de carateres oculto
												        document.getElementById("desc_<?php echo $contador;?>").disabled = true;//tofos al cargar el document estan desaibiltados
												        $("#btncancel<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
												        $("#btnsave<?php echo $contador; ?>").addClass('hidden');//botones desabilitados

												        $('#final_<?php echo $contador; ?>').easyPieChart({
												            animate: 2000,
												            scaleColor: false,
												            lineWidth: 7.5,
												            lineCap: 'square',
												            value: '10',
												            size: 100,
												            trackColor: '#EEEEEE',
												            barColor: '#92D099'
												        });

												        $('#final_<?php echo $contador; ?>').css({
												            width: 100 + 'px',
												            height: 100 + 'px'
												        });
												        $('#final_<?php echo $contador; ?> .percent').css({
												            "line-height": 100 + 'px'
												        })

												    });


												    function EditarDescripcion<?php echo $contador; ?>(fl_criterio) {


												        document.getElementById("desc_<?php echo $contador; ?>").disabled = false;
												        $("#btncancel<?php echo $contador; ?>").removeClass('hidden');
												        $("#btnsave<?php echo $contador; ?>").removeClass('hidden');
												        $("#char<?php echo $contador; ?>").removeClass('hidden');//se hablita contador carateres.

												}
												// <!---funcion para inabilitra la edicion de la descripcion del criterio-->
												function CancelarEdicion<?php echo $contador; ?>(fl_calificacion_criterio) {

												        document.getElementById("desc_<?php echo $contador; ?>").disabled = true;
												        $("#btncancel<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
												        $("#btnsave<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
												        $("#char<?php echo $contador; ?>").addClass('hidden');//se hablita contador carateres.

												}

												function GuardarDescripcion<?php echo $contador; ?>(fl_criterio) {

												        var ds_descripcion = document.getElementById("desc_<?php echo $contador; ?>").value;
												        var fl_criterio = fl_criterio;											        var no_calificacion = document.getElementById("ex<?php echo $contador; ?>").value;
												        var fl_alumno ='<?php echo $fl_alumno ?>';
												        var fl_leccion_sp = "<?php echo $fl_leccion_sp ?>";
												        var fl_programa_sp = "<?php echo $fl_programa_sp ?>";
												        var fg_comentario_crietrio = 1;
												        var rangeInput = document.getElementById('ex<?php echo $contador ?>').value;


												       // alert(rangeInput);

                                                     //var clave=document.getElementById("fl_re").value;
                                                         $.ajax({
                                                             type: 'POST',
                                                             url: 'site/guardar_comentarios_criterio.php',
                                                             data: 'ds_descripcion='+ds_descripcion+
                                                                   '&fl_alumno='+fl_alumno+
                                                                   '&fl_leccion_sp='+fl_leccion_sp+
                                                                   '&fl_programa_sp='+fl_programa_sp+
                                                                   '&no_calificacion='+no_calificacion+
                                                                   '&rangeInput='+rangeInput+
                                                                   '&fg_comentario_crietrio='+fg_comentario_crietrio+
                                                                   '&fl_criterio='+fl_criterio,
                                                                   
                                                             async: true,
                                                             success: function (html) {

                                                                 $('#muestra_save<?php echo $contador; ?>').html(html);

																}

															});

                                                    document.getElementById("desc_<?php echo $contador; ?>").disabled = true;
												    $("#btncancel<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
												    $("#btnsave<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
												    $("#char<?php echo $contador; ?>").addClass('hidden');//se hablita contador carateres.

												}


												function CuentaCaracteres<?php echo $contador;?>() {

												        
												    //document.datos.char$contador.value=130 -(document.datos.desc$contador.value.length);
												    var este = 130 - (document.getElementById("desc_<?php echo $contador;?>").value.length);
												    //var este =10;
												    //alert(este);
												    $("#char<?php echo $contador;?>").html(este);
												}

												function CuentaCaracteres_teacher() {

												        //document.datos.char$contador.value=130 -(document.datos.desc$contador.value.length);
												        var este = 500 - (document.getElementById("desc_teacher").value.length);

												    $("#char_teacher").html(este);
												}

                                                </script>




<style>
#ex<?php echo $contador; ?>Slider .slider-selection {
background: #BABABA;
}

</style>	

			

			</div>

</div>
			
			

<?php

        if(empty($no_porcentaje_equivalente))
            $no_porcentaje_equivalente="0";
?>


<div class="row">	
    
    <div class="col-md-1" style="padding-right: 0px;"> &nbsp;</div>

    <div class="col-md-9 " style="padding-left: 1px; padding-right:1px; ">
    		
			<input id="ex<?php echo $contador; ?>"  class="ex<?php echo $contador; ?>" data-slider-id='ex<?php echo $contador; ?>Slider'   type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="<?php echo $no_porcentaje_equivalente; ?>"/>
								    
	

                  <?php
                 //        if($contador_slider==$registros)
                 //{
                 
                 //    echo"<span id='ex6CurrentSliderValLabel'>Current Slider Value: <span id='ex6SliderVal'>3</span></span>";
                 
                 //}
                
                ?>



    </div>   

    <div class="col-md-2">

    </div>
        
        		
		
			
   
   
    
    
    
                                             

</div>


                              <script>
                                var slider = new Slider('#ex<?php echo $contador; ?>', {
                                    formatter: function (value) {
                                        return 'Current value: ' + value;

                                        
                                    }
                                });



                                  $(document).ready(function () {
                                  var intSeconds = 1;
                                  var refreshId;
                                  $('#ex<?php echo $contador; ?>').on('slideStop', function () {
                                     
                                      ObtenValor<?php echo$contador; ?>(); 
                                      
                                  });

                                 
                                  });

								</script>
                

								<?php 

        
        echo"
								<script>
										function ObtenValor$contador(){

										   var rangeInput = document.getElementById('ex$contador').value;
                                           var fl_criterio=$fl_criterio;//identificador del criterio
										   
                                           var peso_criterio=$no_porcentaje_criterio;
                                           var fl_alumno=$fl_alumno;
                                           var fl_programa_sp=$fl_programa_sp;
                                           var fl_leccion_sp=$fl_leccion_sp;
                                           var fg_calcula_promedio=1;
                                           
                            
										    //se actualiza el valor del ultimo circulo.		
										    $('#final_$contador').data('easyPieChart').update(rangeInput);		
										
                                            $('#span_final$contador').html(rangeInput);
                                       
									
											
											if(rangeInput == 0 ){
											
											    $('#divborder_cero$contador_border1').addClass('border');
												$('#divborder_dos$contador_border2').removeClass('border');
												$('#divborder_tres$contador_border3').removeClass('border');
												$('#divborder_cuatro$contador_border4').removeClass('border');
											    $('#divborder_cinco$contador_border5').removeClass('border');
											
                                               
                                                
											}else if ( (rangeInput > 0 ) && (rangeInput <= 49 )  ) {
											   
											   $('#divborder_cero$contador_border1').removeClass('border');
											   $('#divborder_dos$contador_border2').addClass('border');
											   $('#divborder_tres$contador_border3').removeClass('border');
											   $('#divborder_cuatro$contador_border4').removeClass('border');
											   $('#divborder_cinco$contador_border5').removeClass('border');
											
											}else if ((rangeInput > 49 )&& (rangeInput <= 72)  ){
												$('#divborder_cero$contador_border1').removeClass('border');
												$('#divborder_dos$contador_border2').removeClass('border');
												$('#divborder_tres$contador_border3').addClass('border');
											    $('#divborder_cuatro$contador_border4').removeClass('border');
												$('#divborder_cinco$contador_border5').removeClass('border');
											
											
											}else if((rangeInput > 72) && (rangeInput <= 85)){
												 $('#divborder_cero$contador_border1').removeClass('border');
												 $('#divborder_dos$contador_border2').removeClass('border');
												 $('#divborder_tres$contador_border3').removeClass('border');
												 $('#divborder_cuatro$contador_border4').addClass('border');
												 $('#divborder_cinco$contador_border5').removeClass('border');
											
											
											}else {
											      $('#divborder_cero$contador_border1').removeClass('border');
												  $('#divborder_dos$contador_border2').removeClass('border');
												  $('#divborder_tres$contador_border3').removeClass('border');
												  $('#divborder_cuatro$contador_border4').removeClass('border');
											      $('#divborder_cinco$contador_border5').addClass('border');
											
											}
											
											
                                          
                                            
                                            //Guardamos los datos para saber el total.
                                            
                                             $.ajax({
                                                type: 'POST',
                                                url: 'site/guardar_rango_calcular_calificacion.php',
                                                data: 'rangeInput='+rangeInput+
                                                      '&fl_alumno='+fl_alumno+
                                                      '&fl_programa_sp='+fl_programa_sp+
                                                      '&fl_leccion_sp='+fl_leccion_sp+
                                                      '&peso_criterio='+peso_criterio+
                                                      '&fl_criterio='+fl_criterio,

                                                async: true,
                                                success: function (html) {
                                                    $('#presenta_calculo').html(html);
                                                }
                                            });
                                            
                                            
										
										}
		
										
								</script>
								";	

                                ?>


<?php


    if(!empty($fg_esta_calificado_teacher)){//si esta calificado entonces ejecuta el slider azul para pintar el circulo verde final de cada criterio
     echo" <script>
      $(document).ready(function () {
      ObtenValor$contador();
         });
           </script>
     ";


        #Recuperamos comentarios finales
        $Query="SELECT ds_comentarios,fe_modificacion FROM c_com_criterio_teacher WHERE fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp AND fl_alumno=$fl_alumno AND fg_com_final='1'  ";
        $row=RecuperaValor($Query);
        $ds_comentario_final=$row[0];
        $fe_comentario_final=ObtenFechaFormatoDiaMesAnioHora($row[1]);
        $fe_comentario_final="<h2 style='margin: 2px 0; line-height: 60%;font-size:20px;'><i><small>".ObtenEtiqueta(1680)." :<br/>$fe_comentario_final</small></i></h2> ";
    }else{
         $ds_comentario_final="";
         $fe_comentario_final="";
    }

    
    }
        
    
	
	if(!empty($ds_comentario_final)){
	
	$fe_comentario_final=$fe_comentario_final;
	}else{
	
	$fe_comentario_final="";
	}
	
	
	
	
?>


<div class="col-md-10">
	<span id="char_teacher" class="pull-left text-left hidden" style=""> </span>
	<a href="javascript:void(0);" class="btn btn-xs btn-default pull-right" style="border:0px;" Onclick="EditarDescripcionFinal(<?php echo $fl_identificador; ?>);"><i class="fa fa-pencil" ></i></a>					
    <textarea class="form-control" rows="4"  id="desc_teacher" placeholder="<?php echo ObtenEtiqueta(1668); ?>" style="resize:none !important;" maxlength="500" onkeydown="CuentaCaracteres_teacher()" onKeyUp="CuentaCaracteres_teacher()" ><?php echo $ds_comentario_final; ?></textarea>	

	
	
	<div class="col-md-4" id="muestra_save_final">
        <?php echo $fe_comentario_final; ?> 

	</div>	
	<div class="col-md-4 text-center" ><br/>    
		<a href="javascript:void(0);" class="btn btn-primary" style="border-radius:10px;" id="boton_final" onclick="GuardarTranscript();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo ObtenEtiqueta(1669);?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
		

	</div>
	<div class="col-md-4">
			<br/>
			<div class="form-group" style="float:right;">
			<a class="btn btn-primary btn-xs" href="javascript:void(0);" style="float:right;font-size: 13px;" id="btnsavefinal" Onclick="GuardarDescripcionFinal(<?php echo $fl_identificador; ?>);">Save</a>
			</div>
			<div class="form-group" style="float:right;">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 <a class="btn btn-default btn-xs" href="javascript:void(0);" style="float:right;font-size: 13px; margin-right:10px;" id="btncancelfinal" Onclick="CancelarEdicionFinal(<?php echo $fl_identificador; ?>);">Cancel</a>
			</div>
	</div>
    <div class="row">
    <div class="col-md-12">
	<br/>
        <div class="smart-form text-center">
            <div class="inline-group" style="max-width: <?php echo round(strlen(ObtenEtiqueta(2613))/1.65);?>em; margin: auto;">
                <label class="checkbox" style="margin-right: 0px;">
			    <input type="checkbox" name="checkbox-inline" id="cehck_fgincrease_grade" <?php echo $cheked_increase;?>>
			    <i></i><?php echo ObtenEtiqueta(2613);?></label>
            </div>
        </div>	
            
	<br />
	</div>
    <div class="col-md-1"></div>
    </div>
</div>

<div class="col-md-2">
								<div class="panel panel-default">
								
								       <!--<span id="slidernumber">50</span>-->

									  <div class="panel-body text-center">

												    <div id="presenta_calculo">
													
                                                        <!------presentamos la forma en primera instancia----->

                                                                <div class="chart" data-percent="100" id="easy-pie-chart_final">
											                       <span class="percent" style="font:18px Arial;" id="Span1">100</span>
											                    </div>
											
											
											                    <script>
											                        $(document).ready(function () {
											                            $('#easy-pie-chart_final').easyPieChart({
											                                animate: 2000,
											                                scaleColor: false,
											                                lineWidth: 7.5,
											                                lineCap: 'square',
											                                size: 100,
											                                trackColor: '#EEEEEE',
											                                barColor: '#92D099'
											                            });

											                            $('#easy-pie-chart_final').css({
											                                width: 100 + 'px',
											                                height: 100 + 'px'
											                            });
											                            $('#easy-pie-chart_final.percent').css({
											                                "line-height": 100 + 'px'
											                            })

											                        });
											                    </script>

                                                        <!----------------->

												    </div>
									        <hr />		
									        <b><?php echo ObtenEtiqueta(1671);  ?></b>
									</div>
								
								</div>
								
							
</div>


	







                                        <script>
                                            $(document).ready(function () {

                                                $("#btncancelfinal").addClass('hidden');
                                                $("#btnsavefinal").addClass('hidden');
                                                $("#charfinal").addClass('hidden');//se hablita contador carateres.
                                                document.getElementById("desc_teacher").disabled = true;

                                            });
                                            function EditarDescripcionFinal(fl_identificador) {


                                                document.getElementById("desc_teacher").disabled = false;
                                                $('#char_teacher').removeClass('hidden');
                                                $("#btncancelfinal").removeClass('hidden');
                                                $("#btnsavefinal").removeClass('hidden');
                                                $("#charfinal").removeClass('hidden');//se hablita contador carateres.
												$("#boton_final").addClass('hidden');
                                            }

                                            function CancelarEdicionFinal(fl_identificador) {

                                                document.getElementById("desc_teacher").disabled = true;
                                                $('#char_teacher').addClass('hidden');
                                                $("#btncancelfinal").addClass('hidden');//botones desabilitados
                                                $("#btnsavefinal").addClass('hidden');//botones desabilitados
                                                $("#charfinal").addClass('hidden');//se hablita contador carateres.
												 $("#boton_final").removeClass('hidden');
													
                                            }


                                            function GuardarDescripcionFinal(fl_identificador) {

											    $("#boton_final").removeClass('hidden');
											
                                                var ds_comentarios = document.getElementById("desc_teacher").value;
                                                var fl_alumno ='<?php echo $fl_alumno ?>';
												var fl_leccion_sp = "<?php echo $fl_leccion_sp ?>";
                                                var fl_programa_sp = "<?php echo $fl_programa_sp ?>";
                                                
                                                var comen_final = 1;
                                                // var no_calificacion = document.getElementById("final_total").value;
                                                
                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'site/guardar_comentarios_criterio.php',
                                                    data: 'ds_comentarios='+ds_comentarios+
                                                            '&fl_alumno='+fl_alumno+
                                                            '&fl_leccion_sp='+fl_leccion_sp+
                                                            '&comen_final='+comen_final+
                                                            '&fl_programa_sp='+fl_programa_sp,

                                                    async: true,
                                                    success: function (html) {

                                                        $('#muestra_save_final').html(html);

                                                    }

                                                });

                                                document.getElementById("desc_teacher").disabled = true;
                                                $('#char_teacher').addClass('hidden');
                                                $("#btncancelfinal").addClass('hidden');//botones desabilitados
                                                $("#btnsavefinal").addClass('hidden');//botones desabilitados
                                                $("#charfinal").addClass('hidden');//se hablita contador carateres.

                                            }
                                            
                                            function GuardarTranscript() {


												$('#boton_final').addClass('disabled');

                                                var ds_comentarios = document.getElementById("desc_teacher").value;
                                                var fl_alumno ='<?php echo $fl_alumno ?>';
												var fl_leccion_sp = "<?php echo $fl_leccion_sp ?>";
                                                var fl_programa_sp = "<?php echo $fl_programa_sp ?>";
                                                var fg_guardar_todo = 1;
                                                var fg_increase_grade = document.getElementById("cehck_fgincrease_grade").value;
                                                
                                                var fg_calificado="<?php echo $fg_esta_calificado;?>";
                                                if( $('#fg_increase_grade').is(':checked') ) {
                                                  fg_increase_grade = 1;
                                                }
                                                if($('#cehck_fgincrease_grade').is(':checked') ) {
                                                    fg_increase_grade = 1;
                                                }else{
                                                    fg_increase_grade = 0;
                                                }

                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'site/guardar_transcript.php',
                                                    data: 'ds_comentarios='+ds_comentarios+
                                                            '&fl_alumno='+fl_alumno+
                                                            '&fl_leccion_sp='+fl_leccion_sp+
                                                            '&fg_guardar_todo='+fg_guardar_todo+
                                                            '&fg_increase_grade='+fg_increase_grade+
															'&fg_calificado='+fg_calificado+
                                                            '&fl_programa_sp='+fl_programa_sp,
                                                    async: true,
                                                    success: function (html) {

															$.smallBox({
																title : "<i class=\"fa fa-check\" aria-hidden=\"true\"></i> <?php echo ObtenEtiqueta(1672); ?>",
																//content : "Lorem ipsum dolor sit amet, test consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
																color : "#739E73",
																timeout: 4000,
																iconSmall : "fa fa-check ",
																//number : "2"
															});
													
                                                        $('#muestra_save_final').html(html);

														socket.emit('new-notify-user', fl_alumno);
														
														
                                                    }

                                                });

                                            }

</script>

<?php

}else{#end si existe rubric
     
        echo"
        		<div class='row'>
                    <div class='col-md-12 text-center'>

                        <div class='alert alert-danger' >
                            <strong><i class='fa fa-window-close-o fa-5' aria-hidden='true'></i></strong>&nbsp;".ObtenEtiqueta(1695)."
                        </div>
                    </div>

                </div>

        ";
  
    }

?>
