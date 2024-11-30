<?php
 # Librerias
 require("../lib/self_general.php");

# Variable initialization
$style_tab_desc=NULL;
$style_tab_quiz=NULL;
$style_tab_rub=NULL;
$p_script=NULL;
$p_titulo=NULL;
$c_remaining=NULL;
$q_remaining_1=NULL;
$ds_grade_img_1=NULL;
$ds_grade_img_2=NULL;
$ds_grade_img_3=NULL;
$ds_grade_img_4=NULL;
$fl_criterio=NULL;
$pesos=NULL;
$fg_reset_video=NULL;

 # Verifica que exista una sesion valida en el cookie y la resetea
 $fl_usuario = ValidaSesion(False,0, True);
  
 # Recibe parametros
 $clave = RecibeParametroNumerico('c',True);
 $fg_error = RecibeParametroNumerico('fg_error'); 

 # Verifica que el usuario tenga permiso de usar esta funcion
 if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
 }
 
 # Intituto del usuario
 $fl_instituto = ObtenInstituto($fl_usuario);
 $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  

    if(!empty($clave)) { // Actualizacion, recupera de la base de datos


	 $Query  = "SELECT fl_programa_sp, no_grado, no_semana, ds_titulo, ds_leccion, ds_vl_ruta, ds_vl_duracion, ";
      $concat = array(ConsultaFechaBD('fe_vl_alta', FMT_FECHA), "' '", ConsultaFechaBD('fe_vl_alta', FMT_HORAMIN));
      $Query .= "(".ConcatenaBD($concat).") 'fe_vl_alta', ";
      $Query .= "fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch, ";
      $Query .= "ds_as_ruta, ds_as_duracion, ";
      $concat = array(ConsultaFechaBD('fe_as_alta', FMT_FECHA), "' '", ConsultaFechaBD('fe_as_alta', FMT_HORAMIN));
      $Query .= "(".ConcatenaBD($concat).") 'fe_as_alta', ds_animacion, ds_ref_animacion, ds_no_sketch, ds_tiempo_tarea, nb_quiz, no_valor_quiz, ";
      $Query .= "ds_learning, no_valor_rubric, fl_leccion_copy, ds_vl_ruta_copy,ds_titulo_esp,ds_titulo_fra,ds_learning_esp,ds_learning_fra,ds_leccion_esp,ds_leccion_fra ";
      $Query .=",ds_animacion_esp,ds_animacion_fra,ds_ref_animacion_esp,ds_ref_animacion_fra, ds_no_sketch_esp,ds_no_sketch_fra,ds_ref_sketch_esp,ds_ref_sketch_fra,ds_ref_sketch,ds_progress_video ";
	  $Query .= "FROM c_leccion_sp ";
      $Query .= "WHERE fl_leccion_sp = $clave";
      $row = RecuperaValor($Query);
      $fl_programa = $row[0];
      $no_grado = $row[1];
      $no_semana = $row[2];
      $ds_titulo = str_texto($row[3]);
      $ds_leccion = str_texto($row[4]);
      $ds_vl_ruta = str_texto($row[5]);
      $ds_vl_duracion = str_texto($row[6]);
      $fe_vl_alta = str_texto($row[7]);
      $fg_animacion = $row[8];
      $fg_ref_animacion = $row[9];
      $no_sketch = $row[10];
      $fg_ref_sketch = $row[11];
      $ds_as_ruta = str_texto($row[12]);
      $ds_as_duracion = str_texto($row[13]);
      $fe_as_alta = str_texto($row[14]);
      $ds_animacion = $row[15];
      $ds_ref_animacion = $row[16];
      $ds_no_sketch = $row[17];
      $ds_tiempo_tarea = $row[18];
      $nb_quiz = str_texto($row[19]);
      $no_valor_quiz = ($row[20]);
      $ds_learning = str_texto($row['ds_learning']);
      $no_val_rub = $row[22];
      $archivo_a = $row[23];
      $ds_vl_ruta_copy = $row[24];
	  $ds_titulo_esp=str_texto($row['ds_titulo_esp']);
	  $ds_titulo_fra=str_texto($row['ds_titulo_fra']);
	  $ds_learning_esp=str_texto($row['ds_learning_esp']);
	  $ds_learning_fra=str_texto($row['ds_learning_fra']);
	  $ds_leccion_esp=str_texto($row['ds_leccion_esp']);
	  $ds_leccion_fra=str_texto($row['ds_leccion_fra']);
	  $ds_animacion_esp=str_texto($row['ds_animacion_esp']);
	  $ds_animacion_fra=str_texto($row['ds_animacion_fra']);
	  $ds_ref_animacion_esp=str_texto($row['ds_ref_animacion_esp']);
	  $ds_ref_animacion_fra=str_texto($row['ds_ref_animacion_fra']);
	  $ds_no_sketch_esp=str_texto($row['ds_no_sketch_esp']);
	  $ds_no_sketch_fra=str_texto($row['ds_no_sketch_fra']);
	  $ds_ref_sketch_esp=str_texto($row['ds_ref_sketch_esp']);
	  $ds_ref_sketch_fra=str_texto($row['ds_ref_sketch_fra']);	  
	  $ds_ref_sketch = $row['ds_ref_sketch'];
	  $total_convertido=$row['ds_progress_video'];
	  if(empty($total_convertido))
		  $total_convertido=0;
	  
	  
	  
      $row = RecuperaValor("SELECT (100 - SUM(ds_valor_pregunta)) FROM k_quiz_pregunta WHERE fl_leccion_sp = $clave AND fl_instituto=$fl_instituto ");
      $valor_ini_preg = !empty($row[0])?$row[0]:NULL;
      if(empty($valor_ini_preg))
        $valor_ini_preg = 100;
      // $valor_ini_preg = 1;
      $style_sin_criterios = "style='display:none;'";
      $style_sin_valor_rubric = "style='display:none;'";
      $style_sin_valor_criterio = "style='display:none;'";
      $style_max_grade = "style='display:none;'";
      $style_max_grade_wrg = "style='display:none;'";

   

      $Query  = "SELECT fl_leccion_sp, fg_tipo, ds_pregunta, ds_valor_pregunta, fg_posicion_img, ds_course_pregunta, no_orden, fl_quiz_pregunta,ds_pregunta_esp,ds_pregunta_fra ";
      $Query .= "FROM k_quiz_pregunta ";
      $Query .= "WHERE fl_leccion_sp = $clave ";
      $row = RecuperaValor($Query);
      $rsp = EjecutaQuery($Query);
      $tot_preguntas = CuentaRegistros($rsp);
      if(empty($tot_preguntas)){        
          $editar = False;
          $fl_leccion_sp = 0;
          $fg_tipo_resp_1 = "T";
          $ds_pregunta_1 = "";
          $ds_pregunta_esp_1 = "";
          $ds_pregunta_fra_1 = "";
          $ds_quiz_1 = 0;
          $fg_tipo_img_1 = "L";
          $ds_course_1 = "";
          $no_orden = 0;
          $fl_quiz_pregunta = 0;
          $ds_resp_1 = "";
          $ds_resp_esp_1 = "";
          $ds_resp_fra_1 = "";
          $ds_grade_1 = "";
          $ds_resp_2 = "";
          $ds_resp_esp_2 = "";
          $ds_resp_fra_2 = "";
          $ds_grade_2 = "";
          $ds_resp_3 = "";
          $ds_resp_esp_3 = "";
          $ds_resp_fra_3 = "";
          $ds_grade_3 = "";
          $ds_resp_4 = "";
          $ds_resp_esp_4 = "";
          $ds_resp_fra_4 = "";
          $ds_grade_4 = "";
          $ds_resp_5 = "";
          $ds_resp_esp_5 = "";
          $ds_resp_fra_5 = "";
          $ds_grade_5 = "";
      }
      else{
          $editar = True;
          $fl_leccion_sp = $row["fl_leccion_sp"];
          $fg_tipo_resp_1 = str_texto($row["fg_tipo"]??NULL);
          if (empty($fg_tipo_resp_1))
              $fg_tipo_resp_1 = "T";
          $ds_pregunta_1 = $row["ds_pregunta"];
          $ds_pregunta_esp_1 = $row["ds_pregunta_esp"];
          $ds_pregunta_fra_1 = $row["ds_pregunta_fra"];
          $ds_quiz_1 = $row["ds_valor_pregunta"]??NULL;
          if (empty($ds_quiz_1))
              $ds_quiz_1 = 0;
          $fg_tipo_img_1 = str_texto($row["fg_posicion_img"]);
          $ds_course_1 = $row["ds_course_pregunta"];
          $no_orden = $row["no_orden"];
          $fl_quiz_pregunta = $row["fl_quiz_pregunta"];
          $row1 = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_valor_respuesta, no_tab, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta AND no_tab = 1 AND no_orden = 1 ");
          $ds_resp_1 = str_texto($row1["ds_respuesta"]);
          $ds_resp_esp_1 = str_texto($row1["ds_respuesta_esp"]);
          $ds_resp_fra_1 = str_texto($row1["ds_respuesta_fra"]);
          $ds_grade_1 = $row1["ds_valor_respuesta"];
          $row2 = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_valor_respuesta, no_tab, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta AND no_tab = 1 AND no_orden = 2 ");
          $ds_resp_2 = str_texto($row2["ds_respuesta"]);
          $ds_resp_esp_2 = str_texto($row2["ds_respuesta_esp"]);
          $ds_resp_fra_2 = str_texto($row2["ds_respuesta_fra"]);
          $ds_grade_2 = $row2["ds_valor_respuesta"];
          $row3 = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_valor_respuesta, no_tab, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta AND no_tab = 1 AND no_orden = 3 ");
          $ds_resp_3 = str_texto($row3["ds_respuesta"]);
          $ds_resp_esp_3 = str_texto($row3["ds_respuesta_esp"]);
          $ds_resp_fra_3 = str_texto($row3["ds_respuesta_fra"]);
          $ds_grade_3 = $row3["ds_valor_respuesta"];
          $row4 = RecuperaValor("SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_valor_respuesta, no_tab, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta AND no_tab = 1 AND no_orden = 4 ");
          $ds_resp_4 = str_texto($row4["ds_respuesta"]);
          $ds_resp_esp_4 = str_texto($row4["ds_respuesta_esp"]);
          $ds_resp_fra_4 = str_texto($row4["ds_respuesta_fra"]);
          $ds_grade_4 = $row4["ds_valor_respuesta"];

          # Buscamos qie por lo menos en los valores de loas respuestas haya un 100
          if ($fg_tipo_resp_1 == "T") {
              $nb_img_prev_mydropzone_1_1 = "";
              $ds_grade_img_1 = "";
              $ds_grade_img_2 = "";
              $ds_grade_img_3 = "";
              $ds_grade_img_4 = "";
              $pesos = array_search(100, array(1 => $ds_grade_1, 2 => $ds_grade_2, 3 => $ds_grade_3, 4 => $ds_grade_4));
          } else {

              if(empty($tot_preguntas)){  

                  $ds_resp_1 = "";
                  $ds_resp_esp_1 = "";
                  $ds_resp_fra_1 = "";
                  $ds_grade_1 = "";
                  $ds_resp_2 = "";
                  $ds_resp_esp_2 = "";
                  $ds_resp_fra_2 = "";
                  $ds_grade_2 = "";
                  $ds_resp_3 = "";
                  $ds_resp_esp_3 = "";
                  $ds_resp_fra_3 = "";
                  $ds_grade_3 = "";
                  $ds_resp_4 = "";
                  $ds_resp_esp_4 = "";
                  $ds_resp_fra_4 = "";
                  $ds_grade_4 = "";

              }
              $ds_grade_img_1 = $row1[3];
              $ds_grade_img_2 = $row2[3];
              $ds_grade_img_3 = $row3[3];
              $ds_grade_img_4 = $row4[3];
              $pesos = array_search(100, array(1 => $ds_grade_img_1, 2 => $ds_grade_img_2, 3 => $ds_grade_img_3, 4 => $ds_grade_img_4));
          }

      }
      $disabled_no_val_rub = "";
      $disabled_det = "";
	  $fg_nuevo_registro=0;
	}else{
		
	    $Query="DELETE FROM c_leccion_sp WHERE fl_programa_sp=0 OR fl_programa_sp IS null and fl_instituto=$fl_instituto ;";
		EjecutaQuery($Query);
		
		$fg_nuevo_registro=1;
		
		#/*Cuando agregas un nuevo registro, se tiene que hacer un insert ya todo lo demas seran updates, es la forma mas facil para ir agregando elemntos de cada tab.*/
		$Query="INSERT INTO c_leccion_sp(fl_programa_sp,no_grado,no_semana,ds_titulo,ds_titulo_esp,ds_titulo_fra,ds_leccion,ds_leccion_esp,ds_leccion_fra,fl_instituto,fg_animacion,fg_ref_animacion,no_sketch,fg_ref_sketch,fl_usuario_creacion)
                VALUES(0,0,0,'','','','','','',$fl_instituto,'0','0','0','0',$fl_usuario); ";
		$clave=EjecutaInsert($Query);
        
		# Si si esta dando de alta 
		if (ExisteEnTabla('k_video_temp', 'fl_usuario', $fl_usuario)) {
		  $row5 = RecuperaValor("SELECT nb_archivo FROM k_video_temp WHERE fl_usuario=$fl_usuario");
		  $ds_vl_ruta = $row5[0];
		} else
		  $ds_vl_ruta = "";
		$ds_vl_duracion = "";
		$fe_vl_alta = "";
		$fg_animacion = "0";
		$fg_ref_animacion = "0";
		$no_sketch = "0";
		$fg_ref_sketch = "0";
		$ds_as_ruta = "";
		$ds_as_duracion = "";
		$fe_as_alta = "";
		$fg_tipo_resp_1 = "T";
		$fg_tipo_img_1  = "L";
		$fl_quiz_pregunta = 0;
		// Campo para Dropzone
		$editar = False;
		$ds_tiempo_tarea = "";
		$nb_quiz = "";
		$no_valor_quiz = "";
		$valor_ini_preg = 0;
		$ds_quiz_1 = 0;
		$valor_ini_preg = 100;
		$valor_inicial = 100;
		$no_val_rub = 0;
		$no_ter_co = 100;
		$style_sin_criterios = "style='display:none;'";
		$style_sin_valor_rubric = "style='display:none;'";
		$style_sin_valor_criterio = "style='display:none;'";
		$style_max_grade = "style='display:none;'";
		$style_max_grade_wrg = "style='display:none;'";

		$disabled_no_val_rub = "disabled = 'disabled'";

		# Eventos para validacion de campos
		$val_camp_obl_1 = 'onblur="ValidaCamposObligatorios(\'no_grado\', this.value);"';
		$val_camp_obl_2 = 'onblur="ValidaCamposObligatorios(\'no_semana\', this.value);"';
		$val_camp_obl_3 = 'onblur="ValidaCamposObligatorios(\'ds_titulo\', this.value);"';
		$val_camp_obl_4 = 'onblur="ValidaCamposObligatorios(\'ds_learning\', this.value);"';

		$disabled_det = "disabled = 'disabled'";
    }
  
    # Eliminamos registros de este usuario si existe algun video temporal
    if (ExisteEnTabla('k_video_temp', 'fl_usuario', $fl_usuario)) {
        EjecutaQuery("DELETE FROM k_video_temp WHERE fl_usuario=$fl_usuario");
    }
  
    if (!empty($clave)) {
        if (empty($fg_error)) {
            $row = RecuperaValor("SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp");
            $no_preguntas = $row[0];
        } else
            $no_preguntas = $no_max_tabs;
    } else{
        $no_preguntas = $no_max_tabs;
    }
    if (empty($no_preguntas)){
        $no_preguntas = 1;
    }
    $ContTabCounter = ($no_preguntas + 1);
    echo"<input type='hidden' name='ContTabCounter' id='ContTabCounter' value='".$ContTabCounter."'>";
    echo"<input type='hidden' name='ContTabCounterLimit' id='ContTabCounterLimit' value='".$ContTabCounter."'>";
    echo"<input type='hidden' name='NoPreguntas' id='NoPreguntas' value='".$no_preguntas."'>";
    echo"<input type='hidden' name='NoPreguntas_temporal' id='NoPreguntas_temporal' value='".$no_preguntas."'>";

    # Popover donde muestra mensaje en cada campo 
    $warning = "warning";
    function popover($accion = '',  $posicion = '', $title = '', $content = '')
    {

        if (empty($accion))
            $accion = "popover";

        if (empty($posicion))
            $posicion = "top";

        $popover = "rel='" . $accion . "' data-placement='" . $posicion . "' ";

        if (!empty($title))
            $popover .= "data-original-title='" . $title . "' ";

        if (!empty($content))
            $popover .= "data-content='" . $content . "' ";

        return $popover;
    }
  
  
 ?>
 <form name="datos" method="post" action="site/lmedia_iu.php" enctype="multipart/form-data" >
 
 <input type="hidden" name="clave" id="clave" value="<?php echo $clave?>" />
   <!-- widget content -->
            <div class="widget-body">
                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active" id="tab1">
                            <a id="tab_1" href="#description" data-toggle="tab">
							  <span <?php echo $style_tab_desc; ?>><i class="fa fa-fw fa-lg fa-info-circle"></i><?php echo " ".ObtenEtiqueta(19) ?></span>
							</a>
                    </li>
                    <li id="tab2" >
                       <a  id="tab_2"  href="#video" data-toggle="tab"><i class="fa fa-fw fa-lg fa-video-camera"></i><?php echo " ".ObtenEtiqueta(457) ?></a>
                    </li>
					 <li id="tab3" >
                        <a  id="tab_3"  href="#assignment" data-toggle="tab">
						  <span 
						  <?php
						  if(!empty($ds_animacion_err) || !empty($ds_ref_animacion_err) || !empty($ds_no_sketch_err) || !empty($ds_ref_sketch_err))
							echo "style='color:#b94a48;'";
						  ?>><i class="fa fa-fw fa-lg fa-pencil"></i><?php echo " ".ObtenEtiqueta(393) ?></span>
						</a>
                    </li>
					 <li id="tab4" >
                        <a id="tab_4" href="#quiz"  data-toggle="tab">
						  <span <?php echo $style_tab_quiz; ?>><i class="fa fa-fw fa-lg fa-question-circle"></i><?php echo 'Quiz' ?></span>
						</a>
                    </li>

                     <li id="tab5" >
                        <a id="tab_5" href="#rubric"  data-toggle="tab">
						  <span <?php echo $style_tab_rub; ?>><i class="fa fa-fw fa-lg fa-table"></i><?php echo 'Rubric' ?></span>
						</a>
                    </li>
                </ul>
				
				<div id="myTabContent1" class="tab-content padding-10"><!--class='no-border'--->
                    <div class="tab-pane fade in active" id="description">

						<!-- MJD: First tab = descripcion -->
						<?php require $_SERVER['DOCUMENT_ROOT']."/fame/site/lmedia_frm_description_tab.php"; ?>
						
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-lg-1 col-md-1    ">
								<input type="hidden" id="fl_leccion_nuevo_creado" name="fl_leccion_nuevo_creado" value="<?php echo $clave ?>">
								  &nbsp;
								</div>
						
								<div class="col-xs-12 col-sm-12 col-lg-10 col-md-10">								  									
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">     
											&nbsp;
										</div>

										<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">     
											
											
														<div class="smart-form">														
														<br><br>															
																<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 60px;padding-left: 295px;">
																	
																	<li>
																		<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
																	</li>
																	
																	<li>
																		<a href="javascript:void(0);" onclick="GuardarTab1();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
																	</li>
																</ul>														   
														</div> 											
										</div>
									</div>										
								</div>
						
								<div class="col-md-1">
								  &nbsp;
								</div>
							</div>
                    </div>
					

                   

					<!----INCIIA VIDEO---->
                    <div class="tab-pane fade in" id="video">                 
                        <!-- MJD: Second tab = video -->
						<?php require "lmedia_frm_video_tab_fame.php"; ?>
					   
					    <script>
						   function GuardarTabVideo(){
							    var ds_vl_duracion=document.getElementById("ds_vl_duracion").value;
							    var tab=2;
								var clave=<?php echo $clave;?>;
							   
							   var datos = new FormData();
								   datos.append('clave',clave);
							       datos.append('tab',tab);
								   datos.append('ds_vl_duracion',ds_vl_duracion);
							   
							    $.ajax({
								  type:"post",
								  url: 'site/lmedia_iu.php',
								  contentType:false, // se envie multipart
								  data:datos,
								  processData:false, // por si vamos enviar un archivo
								}).done(function(result){
									  var result = JSON.parse(result);
									  var fg_correcto=result.fg_correcto;
									 									  
									  if(fg_correcto==true){
										  
										  
										  //alerta de exito.		  
										  $.smallBox({
										  title : "<?php echo ObtenEtiqueta(2357);?>",
										  content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
										  color : "#276627",
										  iconSmall : "fa fa-thumbs-up bounce animated",
										  timeout : 4000
										  });
						 
									  }
									
								});	
								
								
							   
						   }
						</script>
                    </div>
                    <!----FINALIZA video---->



                    <!----INCIIA COURSE OULINE---->
					<div class="tab-pane fade in" id="assignment"> 
						<?php require "lmedia_frm_assignment_tab.php"; ?> 
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">     
								&nbsp;
							</div>
							<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">     											
											<div class="smart-form">														
											<br><br>																
													<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 60px;padding-left: 295px;">																	
														<li>
															<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
														</li>																	
														<li>
															<a href="javascript:void(0);" onclick="GuardarTab3();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
														</li>
													</ul>											
											</div> 
							</div>
						</div>	
                    </div>
                    <!----FINALIZA COURSE OULINE---->

					








                    <!----INCIIA QUIZ---->
					<div class="tab-pane fade in" id="quiz">
                        
						  
						  <?php require "lmedia_frm_quiz_tab.php"; ?>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">     </div>
								<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">     											
													<div class="smart-form">														
													<br><br>																
															<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 25px;padding-left: 295px;">																	
																<li>
																	<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
																</li>																	
																<li>
																	<a href="javascript:void(0);" onclick="GuardarTabQuiz();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
																</li>
															</ul>											
													</div> 
								</div>
							</div>
						</form>
                    </div>
					<!----FINALIZA STUDENT LIBRARY---->




                    <!----INCIIA CATEGORIA---->
                    <div class="tab-pane fade in" id="rubric">
                       <?php require "lmedia_frm_rubric_tab.php"; ?>

					   <div class="row">
								<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">     </div>
								<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">     											
													<div class="smart-form">														
													<br><br>																
															<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 25px;padding-left: 295px;">																	
																<li>
																	<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
																</li>																	
																<li>
																	<a href="javascript:void(0);" onclick="GuardarTabRubric();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
																</li>
															</ul>											
													</div> 
								</div>
							</div>


                    </div>


				</div>	
				
				
				
				
			</div>
			

<script>

 /* Debemos agregarlo para el fucnionamiento de diversos  plugins*/
pageSetUp();
    $(document).ready(function () {

    });

</script>

<?php
    
                                                            echo "<script type='text/javascript'>
															
                                                            // DO NOT REMOVE : GLOBAL FUNCTIONS!
                                                            $(document).ready(function() {
                                                                $('#ocurrio_error').addClass('hidden');
                                                                // pageSetUp();
                                                                Dropzone.autoDiscover = false;
                                                                //var progress_lecc1 = $('#progress_leccion');
																var barra_progreso=$('#grl_progress');
                                                                $('#{$p_id}').dropzone({
                                                                url: '../../AD3M2SRC4/modules/fame/upload_videos.php',
                                                                // data:  'id=1',
                                                                addRemoveLinks : true,
                                                                maxFilesize: 1024,
                                                                acceptedFiles: '.mov, .MOV, .mp4, .MP4, .avi, .AVI, .3gp, .3GP, .wmv, .WMV, .flv, .FLV, .mpg, .MPG, .webm, .WEBM, .mkv, .MKV',
                                                                // Solo permite guardar un registro
                                                                maxFiles: 1, 
                                                                accept: function(file, done) {
                                                                        var filen = file.name;
                                                                              

                                                                        //validamos que el nombre no tenga espacios.
                                                                        if (filen.indexOf(' ')>0) {
                                                                            $('.dz-error-mark').css('opacity','0.8');
                                                                            $(file.previewElement).find('.dz-error-message').text('".ObtenEtiqueta(2635)."').css('opacity', '0.8').css('margin-left', '100px');
                                                                        }else {                
                                                                            done(); 
                                                                              //colocamos todo en 0.
                                                                              $('#processing_$clave').css('display','none'); 
                                                                              $('#encoding_video_$clave').css('display','none');  
                                                                              $('#completed_video_$clave').css('display','none');
                                                                              $('#ocurrio_error_$clave').css('display','none');
                                                                              $('#progress_hls_$clave').empty().append('0%');
                                                                              $('#grl_progress_$clave').attr('data-progressbar-value',0);
                                                                              $('#imgvideo_$clave').attr('src', '".ObtenConfiguracion(116)."/fame/img/PosterFrame_White.jpg');
                                                                              $('#label_duration_video').empty();
                                                                              $('#label_duration_video').append('0');

                                                                          
                                                                        }
                                                                },
                                                                init: function() {																	
                                                                      this.on('error', function(file, message) {                                                                     
                                                                        this.removeFile(file); 
                                                                      });
                                                                      this.on('beforeSend', function(){ 
																	    $('#upload_videos').modal('toggle');                     
                                                                      });
                                                                    this.on('uploadprogress', function (file, progress, bytesSent){
                                                                       
																		var progress2 = Math.round(progress);
                                                                         // alert(progress2);
                                                                          if(progress2==100){
                                                                            $('#processing_$clave').css('display','block');
                                                                            $('#ocurrio_error_$clave').css('display','none');
                                                                            $('#completed_video_$clave').css('display','none');
                                                                            $('#encoding_video_$clave').css('display','none');
                                                                            $('#progress_hls_$clave').empty().append('0%');
                                                                            $('#grl_progress_$clave').attr('data-progressbar-value',0);
                                                                          }                    
																		
																		$('.dz-progress').show();
                                                                    });
																	  //alert('eviamos message');
                                                                      // Enviamos la clave
                                                                    this.on('sending', function (file, xhr, formData, e) {
                                                                        
																		//validamos que sea un archivo valido
																	    var tipo_archivo=file.type;
																	    var no_semana= document.getElementById('no_semana').value; 
                                                                        formData.append('clave', '".$clave."');
                                                                        formData.append('no_semana',no_semana);
                                                                        formData.append('fl_usuario', '".$fl_usuario."');   
                                                                        formData.append('fg_creado_instituto', '1');
                                                                    });
                                                                    this.on('processing', function(file){
																		$('#upload_videos').modal('toggle');
                                                                    
                                                                    });
                                                                },     
                                                                complete: function(file){
                                                                    //alert(' aqui va aparecer el processing');                                                                    
                                                                    
                                                                    if(file.status == 'success'){
                                                                        $('#processing_$clave').css('display','block');
                                                                        $('#encoding_video_$clave').css('display','none');
                                                                        $('#completed_video_$clave').css('display','none'); 
                                                                        //alert('El siguiente archivo ha subido correctamente: ' + file.name);
                                                                        //document.getElementById('nb_video').value = file.name;											
																	    $('#progress_hls_$clave').empty().append('0%');
																	    $('#grl_progress_$clave').attr('data-progressbar-value',0);
																	    this.removeFile(file);
																		
                                                                    }
                                                                    
                                                                    $('#upload_videos').modal('toggle');
                                                                    // Indicamos que si el boton esta deshabilitado
                                                                    // No realiza el proceso                      
                                                                    var btn_save = $('footer > div > div > a:first').hasClass('disabled')
																   
    																var fg_reset_video=$('#fg_reset_video').is(':checked') ? 1 : 0;																
																	var fl_programa_sp= document.getElementById('fl_programa').value;
                                                                    var no_semana= document.getElementById('no_semana').value;
																	//alert(fg_reset_video);
                                                                    if(btn_save==false){
                                                                       //document.datos.submit();
                                                                       //Enviamos por Post 
                                                                       var formData = new FormData();
                                                                       formData.append('clave', '".$clave."');
                                                                       formData.append('fl_usuario', '".$fl_usuario."');
                                                                       //formData.append('fg_creado_instituto', '1');
                                                                       formData.append('fl_programa', fl_programa_sp);
                                                                       //formData.append('no_grado', '".$no_grado."');
                                                                       //formData.append('no_semana', no_semana);
                                                                       //formData.append('ds_titulo', '".$ds_titulo."');
                                                                       //formData.append('ds_titulo_esp', '');
                                                                       //formData.append('ds_titulo_fra', '');
                                                                       //formData.append('ds_learning', '".$ds_learning."');
                                                                       //formData.append('ds_learning_esp', '".$ds_learning_esp."');
                                                                       //formData.append('ds_learning_fra', '".$ds_learning_fra."');

                                                                       /*#formData.append(\"ds_leccion\", \"".$ds_leccion."\");*/
                                                                       //formData.append('ds_leccion_esp', '');
                                                                       //formData.append('ds_leccion_fra', '');
                                                                       formData.append('ds_vl_ruta0',document.getElementById('ds_vl_ruta0').value);
                                                                       formData.append('camp_progreso_hls', '');
                                                                       formData.append('ds_vl_duracion',document.getElementById('ds_vl_duracion').value);
                                                                       formData.append('fe_vl_alta',document.getElementById('fe_vl_alta').value);
                                                                       //formData.append('fg_animacion',document.getElementById('fg_animacion').value );
                                                                       //formData.append('fg_ref_animacion',document.getElementById('fg_ref_animacion').value);
                                                                       //formData.append('no_sketch',document.getElementById('no_sketch').value);
                                                                       //formData.append('fg_ref_sketch',document.getElementById('fg_ref_sketch').value);
                                                                       formData.append('nb_video',document.getElementById('nb_video').value = file.name);
                                                                       formData.append('archivo_a','".$archivo_a."');
                                                                       formData.append('ds_as_ruta', '".$ds_as_ruta."');
                                                                       formData.append('ds_as_duracion', '".$ds_as_duracion."');
                                                                       formData.append('fe_as_alta', '".$fe_as_alta."');
                                                                       //formData.append('archivo1', document.getElementById('archivo1').value);
                                                                       //formData.append('archivo1_a', document.getElementById('archivo1_a').value);
                                                                       formData.append('fg_reset_video', fg_reset_video);
                                                                       //formData.append('ds_tiempo_tarea', document.getElementById('ds_tiempo_tarea').value);
                                                                       //formData.append('ds_animacion', document.getElementById('ds_animacion').value);
                                                                       
                                                                       //formData.append('ds_animacion_esp', '');
                                                                       //formData.append('ds_animacion_fra', '');
                                                                       //formData.append('ds_ref_animacion', document.getElementById('ds_ref_animacion').value);
                                                                       //formData.append('ds_ref_animacion_esp', '');
                                                                       //formData.append('ds_ref_animacion_fra', '');
                                                                       //formData.append('ds_no_sketch', document.getElementById('ds_no_sketch').value);
                                                                       //formData.append('ds_no_sketch_esp', '');
                                                                       //formData.append('ds_no_sketch_fra', '');
                                                                       //formData.append('ds_ref_sketch', document.getElementById('ds_ref_sketch').value);
                                                                       //formData.append('ds_ref_sketch_esp', '');
                                                                       //formData.append('ds_ref_sketch_fra', '');
                                                                        
                                                                       formData.append('fg_creado_instituto', '1');

                                                                       //alert('pasara ajax');
                                                                       $.ajax({
                                                                          type:'POST',
                                                                          url:'".ObtenConfiguracion(116)."/AD3M2SRC4/modules/fame/lmedia_iu.php',
                                                                          processData: false,
                                                                          cache: false,
                                                                          async: false,
                                                                          contentType: false,
                                                                          data:formData
                                                                       }).done(function (result2) {

																			//Verifica el progreso.
																			//Empieza ver la conversion del archivo.
																			// Consulta el archivo convertidor
																			//alert('pasa ver el progresoo');
																			$('#total_convertido').val(0);
																			var total_convertido = $('#total_convertido').val();
                                                                            
																			if(total_convertido<100){
                                                                                    $('#processing_$clave').css('display','block'); 
                                                                                    $('#completed_video_$clave').css('display','none');
																				    $('#encoding_video_$clave').css('display','none');
                                                                                    $('#ocurrio_error_$clave').css('display','none');  

																				var interval=setInterval(function(){
																					//va pasar a conversion entonces se resetea a 0.
																				    var total_convertido = $('#total_convertido').val(); 
                                                                                    																							
																					$.ajax({
																						type: 'GET',
																						url : '../../AD3M2SRC4/modules/fame/progreso_comando.php',
																						data: 'clave=" . $clave . "'+
                                                                                              '&fg_creado_instituto=1'+
																							  '&archivo=" . $ds_vl_ruta . "'
																					}).done(function(result){
																					var content, tabContainer;
																					content = JSON.parse(result);
																					progress = content.progress;
																									
																					    if(content.error==1){
																						    $('#processing_$clave').css('display','none');
                                                                                            $('#encoding_video_$clave').css('display','none');
																						    //$('#grl_progress_$clave').css('display','none');
                                                                                            $('#ocurrio_error_$clave').css('display','block');                                                                                                       
                                                                                            //alert('ocurrio un error');
                                                                                            clearInterval(interval);
																						    
																					    }else{
                                                                                                    
                                                                                            $('#processing_$clave').css('display','none');
                                                                                            $('#ocurrio_error_$clave').css('display','none');
                                                                                            $('#encoding_video_$clave').css('display','block');


                                                                                            if(progress>0){
                                                                                                $('#loading_conversion_video').css('display','none');
                                                                                                $('#grl_progress_$clave').css('display','block')
                                                                                            }

                                                                                            if(progress<=100){
																						        $('#duration').empty().append(content.duration + '&nbsp;Mins');
																						        $('#grl_progress_$clave').attr('data-progressbar-value', progress);
																						        $('#progress_hls_$clave').empty().append(progress + '%');
																						        $('#camp_progreso_hls').empty().val(progress);
																						        $('#total_convertido').empty().val(progress);
																					        }

                                                                                            if(progress==100){
                                                                                                $('#processing_$clave').css('display','none');
                                                                                                $('#encoding_video_$clave').css('display','none');
                                                                                                $('#completed_video_$clave').css('display','block');
                                                                                                $('#ds_vl_duracion').val(content.time_duration);
                                                                                                $('#imgvideo_$clave').attr('src', '');
                                                                                                $('#imgvideo_$clave').attr('src', content.ruta_thumbnail_video);
                                                                                                clearInterval(interval);
                                                                                                $('#label_duration_video').empty();
                                                                                                $('#label_duration_video').append(content.time_duration);
                                                                                            }  

                                                                                        }
																					          
												                                    });
																								
																					 $('#code_info').addClass('hidden');
																				  },2000);
																							  
																			}
																	    });      																	   

                                                                    }else{
                                                                    alert('You have errors, please check his data');
                                                                    }
                                                                },
                                                                // error: function(file){
                                                                    // alert('Error subiendo el archivo ' + file.name);
                                                                // },
                                                                removedfile: function(file, serverFileName){

                                                                    $('#processing_$clave').css('display','none'); 
                                                                    $('#encoding_video_$clave').css('display','none');  
                                                                    $('#completed_video_$clave').css('display','none');
                                                                    $('#ocurrio_error_$clave').css('display','none');
                                                                    

                                                                    var name = file.name;
                
                                                                    var element;
                                                                    (element = file.previewElement) != null ? 
                                                                    element.parentNode.removeChild(file.previewElement) : 
                                                                    false;
                                                                    // alert('El elemento fue eliminado: ' + name); 
                                                                }
                                                                });
                                                            });
															
                                                            </script>";  


?>



<script>
    //
function validaNumericos(event) {
    if(event.charCode >= 48 && event.charCode <= 57){
      return true;
     }
     return false;        
}	

 //Funcion que valida el numero de sesion repecto a la leccion
function val_lesson(){
	var no_grado = document.getElementById('no_grado').value; 
	var no_semana = document.getElementById('no_semana').value; 
	var fl_programa = document.getElementById('fl_programa').value; 
	$.ajax({
	  type: 'POST',
	  url : 'site/valida_leccion.php',
	  async: false,
	  data: 'no_grado='+no_grado+
			'&no_semana='+no_semana+
			'&fl_programa='+fl_programa,
	  success: function(data) {
		$('#muestra_validacion').html(data);
		
		if(data == 1){
		  $('#err_sesion').removeClass('hidden');
		  $("#no_semana_input_error").removeClass("state-success");
		  $("#no_semana_input_error").addClass("state-error");
		 
		}else{
		 $('#err_sesion').addClass('hidden');
		 $("#no_semana_input_error").removeClass("state-error");
		 $("#no_semana_input_error").addClass("state-success");
		}
	  }
	});
}
$('#no_semana').change(function () {	
            val_lesson();
});
$('#no_grado').change(function () {	
            val_lesson();
});
       

 function GuardarTab1(){
	 
     var clave=document.getElementById("clave").value;
     var tab=1;
	 var fl_programa=document.getElementById("fl_programa").value;
	 var no_grado=document.getElementById("no_grado").value;
	 var no_semana=document.getElementById("no_semana").value;
	 var ds_titulo=document.getElementById("ds_titulo").value;
	 var ds_titulo_esp=document.getElementById("ds_titulo_esp").value;
	 var ds_titulo_fra=document.getElementById("ds_titulo_fra").value;
	 var ds_learning=document.getElementById("ds_learning").value;
	 var ds_learning_esp=document.getElementById("ds_learning_esp").value;
	 var ds_learning_fra=document.getElementById("ds_learning_fra").value;
	 var ds_leccion= CKEDITOR.instances.ds_leccion.getData();
	 var ds_leccion_esp= CKEDITOR.instances.ds_leccion_esp.getData();
	 var ds_leccion_fra= CKEDITOR.instances.ds_leccion_fra.getData();
	 var fl_leccion_nuevo_creado=document.getElementById("fl_leccion_nuevo_creado").value;
	 
	
	 
	
	 if(fl_programa==0){
		$("#s2id_fl_programa").addClass("has-error");
		$("#fl_programa_texto_error").removeClass("hidden");
		
		var fg_correcto=0;
		return;
	 }else{			
		$("#s2id_fl_programa").removeClass("has-error");
		$("#fl_programa_texto_error").addClass("hidden");
	    var fg_correcto=1;
		
	 }
	 
	 
	 if(no_grado.length>0){
	    $("#no_grado_input_error").removeClass("state-error");
		$("#no_grado_texto_error").addClass("hidden");
		var fg_correcto=1;
	   
	 }else{
		$("#no_grado_input_error").addClass("state-error");
		$("#no_grado_texto_error").removeClass("hidden"); 
		 var fg_correcto=0;
		 return;
	 }
	 
	 
	 if(no_semana.length>0){
	    $("#no_semana_input_error").removeClass("state-error");
		$("#no_semana_texto_error").addClass("hidden");
		var fg_correcto=1;
	   
	 }else{
		$("#no_semana_input_error").addClass("state-error");
		$("#no_semana_texto_error").removeClass("hidden"); 
		 var fg_correcto=0;
		 return;
	 }
	 if(ds_titulo.length>0){
	    $("#ds_titulo_input_error").removeClass("state-error");
		$("#ds_titulo_texto_error").addClass("hidden");
		var fg_correcto=1;
	   
	 }else{
		$("#ds_titulo_input_error").addClass("state-error");
		$("#ds_titulo_texto_error").removeClass("hidden"); 
		var fg_correcto=0;
		return;
	 }
	 
	 if(ds_learning.length>0){
	    $("#ds_learning_input_error").removeClass("state-error");
		$("#ds_learning_texto_error").addClass("hidden");
		var fg_correcto=1;
	   
	 }else{
		$("#ds_learning_input_error").addClass("state-error");
		$("#ds_learning_texto_error").removeClass("hidden"); 
		var fg_correcto=0;
		return;
	 }
	 
	 
	 
	 if(ds_leccion.length>0){
	    $("#err_ds_leccion").addClass("hidden");
		var fg_correcto=1;
	   
	 }else{
		$("#err_ds_leccion").removeClass("hidden");
		var fg_correcto=0;
		return;
	 }
	 
	 
	 var datos = new FormData();
	 datos.append('fl_programa',fl_programa);
	 datos.append('no_grado',no_grado);
	 datos.append('no_semana',no_semana);
	 datos.append('ds_titulo',ds_titulo);
	 datos.append('ds_titulo_esp',ds_titulo_esp);
	 datos.append('ds_titulo_fra',ds_titulo_fra);
	 datos.append('ds_learning',ds_learning);
	 datos.append('ds_learning_esp',ds_learning_esp);
	 datos.append('ds_learning_fra',ds_learning_fra);
	 datos.append('ds_leccion',ds_leccion);
	 datos.append('ds_leccion_esp',ds_leccion_esp);
	 datos.append('ds_leccion_fra',ds_leccion_fra);
	 datos.append('fl_leccion_nuevo_creado',fl_leccion_nuevo_creado);
	 datos.append('tab',tab);
	 
	 if(fg_correcto==1){
		
		$.ajax({
		  type:"post",
		  url: 'site/lmedia_iu.php',
		  contentType:false, // se envie multipart
		  data:datos,
		  processData:false, // por si vamos enviar un archivo
		}).done(function(result){			  
			  var result = JSON.parse(result);
			  var fg_correcto_=result.fg_correcto;
			  var fl_leccion_creada=result.fl_leccion_sp;
			  
			  
			  if(fg_correcto_==true){
				  //asiganmos al nput el id_ nueva_leccion_creada.
				  $("#fl_leccion_nuevo_creado").val(fl_leccion_creada);
				  $("#clave").val(fl_leccion_creada);
				  //alerta de exito.		  
			      $.smallBox({
				  title : "<?php echo ObtenEtiqueta(2357);?>",
				  content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
				  color : "#276627",
				  iconSmall : "fa fa-thumbs-up bounce animated",
				  timeout : 4000
			      });
 
			  }else{
				  //motramos error.
				   //alerta de exito.		  
			      $.smallBox({
				  title : "<?php echo ObtenEtiqueta(1288);?>",
				  content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
				  color : "#af0606",
				  iconSmall : "fa fa-exclamation-triangle bounce animated",
				  timeout : 4000
			      });
				  
				  
				  
				  
				  
			  }
			   
			  //pasamos la sigueinte tab.
			  //$('#tab1').removeClass('active');
			  //$('#description').removeClass('active');
			  //$('#tab2').addClass('active');
			  //$('#video').addClass('active in');
			  
			  
		});
		
		
		document.getElementById('tab_2').style.pointerEvents = 'auto';
	    document.getElementById('tab_3').style.pointerEvents = 'auto';
        document.getElementById('tab_4').style.pointerEvents = 'auto';
        document.getElementById('tab_5').style.pointerEvents = 'auto';
		$('#tab_2').removeClass('disabled');
		$('#tab_3').removeClass('disabled');
		$('#tab_4').removeClass('disabled');
		$('#tab_5').removeClass('disabled');
		
	 }
	 
	 
	 
	 
	 
 }


function GuardarTab3(){
	
	var clave=<?php echo $clave;?>;
    var tab=3;
	var ds_tiempo_tarea = document.getElementById("ds_tiempo_tarea").value;
	
	var no_sketch = document.getElementById("no_sketch").value;
	var ds_no_sketch=CKEDITOR.instances.ds_no_sketch.getData();
	var ds_no_sketch_esp=CKEDITOR.instances.ds_no_sketch_esp.getData();
	var ds_no_sketch_fra=CKEDITOR.instances.ds_no_sketch_fra.getData();
	
	var fg_ref_animacion = document.getElementById("fg_ref_animacion");
	var ds_ref_animacion=CKEDITOR.instances.ds_ref_animacion.getData();
	var ds_ref_animacion_esp=CKEDITOR.instances.ds_ref_animacion_esp.getData();
	var ds_ref_animacion_fra=CKEDITOR.instances.ds_ref_animacion_fra.getData();
	
	var fg_animacion = document.getElementById("fg_animacion");
	var ds_animacion=CKEDITOR.instances.ds_animacion.getData();
	var ds_animacion_esp=CKEDITOR.instances.ds_animacion_esp.getData();
	var ds_animacion_fra=CKEDITOR.instances.ds_animacion_fra.getData();
	
	var fg_ref_sketch = document.getElementById("fg_ref_sketch");
	var ds_ref_sketch=CKEDITOR.instances.ds_ref_sketch.getData();
	var ds_ref_sketch_esp=CKEDITOR.instances.ds_ref_sketch_esp.getData();
	var ds_ref_sketch_fra=CKEDITOR.instances.ds_ref_sketch_fra.getData();
	
	var fl_leccion_nuevo_creado=document.getElementById("fl_leccion_nuevo_creado").value;
	 	
	
    var element_tab_ingles = document.getElementById("mytabAssign1");	
	var element_tab_esp = document.getElementById("mytabAssign2");	
	var element_tab_fra = document.getElementById("mytabAssign3");	
	
	
		
	//Si seleeeciona debe llenar descripcion 
	if(fg_animacion.checked){
		
		var fg_animacion=1;
		var element_ingle = document.getElementById("content_p");
		if(ds_animacion.length>0){
		   $('#err_ds_animacion').addClass('hidden');
		   element_ingle.style.borderColor = "";
           element_ingle.style.color = "";
           element_ingle.style.background = "";
		   var fg_exito=1;
		}else{
		   element_ingle.style.borderColor = "red";
           element_ingle.style.color = "red";
           element_ingle.style.background = "#fff0f0";
		  $('#err_ds_animacion').removeClass('hidden');	
		  var fg_exito=0;	
		  return;	
		}
		
		var element = document.getElementById("content_p_esp");
		/*
		if(ds_animacion_esp.length>0){
		   $('#err_ds_animacion_esp').addClass('hidden');
		   element.style.borderColor = "";
           element.style.color = "";
           element.style.background = "";
		   element_tab_esp.style.background="";//quita rojo la tab espaniol
		   var fg_exito=1;
		}else{
		  $('#err_ds_animacion_esp').removeClass('hidden');	
		  element_tab_esp.style.background="#fbbdbd";//pinta rojo la tab espaniol
		  var fg_exito=0;	
		  return;	
		}
		*/
		var element_fra = document.getElementById("content_p_fra");
		/*if(ds_animacion_fra.length>0){
		   $('#err_ds_animacion_fra').addClass('hidden');
		   element_fra.style.borderColor = "";
           element_fra.style.color = "";
           element_fra.style.background = "";
		   element_tab_fra.style.background="";//quita rojo la tab espaniol
		   var fg_exito=1;
		}else{
		  $('#err_ds_animacion_fra').removeClass('hidden');	
		  element_tab_fra.style.background="#fbbdbd";//pinta rojo la tab espaniol
		  
		   element_fra.style.borderColor = "red";
           element_fra.style.color = "red";
           element_fra.style.background = "#fff0f0";
		  
		  var fg_exito=0;	
		  return;	
		}
		*/
		
		
		
		
	}else{
		var fg_animacion=0;
		$('#err_ds_animacion').addClass('hidden');
		var fg_exito=1;
		
		
	}
	
	//Si seleeeciona debe llenar descripcion 
	if(fg_ref_animacion.checked){
		var element = document.getElementById("content_2");
		var content_p_esp = document.getElementById("content_2_esp");
		var content_2_fra = document.getElementById("content_2_fra");
		
		var fg_ref_animacion=1;
		if(ds_ref_animacion.length>0){
		   $('#err_ds_ref_animacion').addClass('hidden');
		   	
		   element.style.borderColor = "";
           element.style.color = "";
           element.style.background = ""; 
		   var fg_exito=1;
		}else{
		   element.style.borderColor = "red";
           element.style.color = "red";
           element.style.background = "#fff0f0"; 
		  $('#err_ds_ref_animacion').removeClass('hidden');	
		  var fg_exito=0;
		  return;		
		}
		/*
		if(ds_ref_animacion_esp.length>0){
		   $('#err_ds_ref_animacion_esp').addClass('hidden');
		   	
		   content_p_esp.style.borderColor = "";
           content_p_esp.style.color = "";
           content_p_esp.style.background = ""; 
		   element_tab_esp.style.background="";
		   var fg_exito=1;
		}else{
		   content_p_esp.style.borderColor = "red";
           content_p_esp.style.color = "red";
           content_p_esp.style.background = "#fff0f0"; 
		  $('#err_ds_ref_animacion_esp').removeClass('hidden');	
		  element_tab_esp.style.background="#fbbdbd";//pinta rojo la tab espaniol
		  var fg_exito=0;
		  return;		
		}
		*/
		/*
		if(ds_ref_animacion_fra.length>0){
		   $('#err_ds_ref_animacion_fra').addClass('hidden');
		   	
		   content_2_fra.style.borderColor = "";
           content_2_fra.style.color = "";
           content_2_fra.style.background = ""; 
		   element_tab_fra.style.background="";
		   var fg_exito=1;
		}else{
		   content_2_fra.style.borderColor = "red";
           content_2_fra.style.color = "red";
           content_2_fra.style.background = "#fff0f0";
		   element_tab_fra.style.background="#fbbdbd";//pinta rojo la tab espaniol		   
		  $('#err_ds_ref_animacion_fra').removeClass('hidden');	
		  var fg_exito=0;
		  return;		
		}
		*/
		
	}else{
		var fg_ref_animacion=0;
		$('#err_ds_ref_animacion').addClass('hidden');	
		var fg_exito=1;
	}
	
	
	if(no_sketch>=1){
		
		if(ds_no_sketch.length>0){
		   $('#err_ds_no_sketch').addClass('hidden');
		   var element = document.getElementById("content_4");
		   element.style.borderColor = "";
           element.style.color = "";
           element.style.background = ""; 
		   var fg_exito=1;	   
		}else{			
		   var fg_exito=0;
		   $('#err_ds_no_sketch').removeClass('hidden');	   	   	   
		   return;
		}
		
		/*if(ds_no_sketch_esp.length>0){
			var element = document.getElementById("content_4_esp");
			element.style.borderColor = "";
            element.style.color = "";
            element.style.background = ""; 
			element_tab_esp.style.background="";
			$('#err_ds_no_sketch_esp').addClass('hidden');
			var fg_exito=1;
			
		}else{
			element_tab_esp.style.background="#fbbdbd";//pinta rojo la tab espaniol
			$('#err_ds_no_sketch_esp').removeClass('hidden');
			var fg_exito=0;	
			return;
		}*/
		/*
		if(ds_no_sketch_fra.length>0){
			var element = document.getElementById("content_4_fra");
			element.style.borderColor = "";
            element.style.color = "";
            element.style.background = ""; 
			element_tab_fra.style.background="";
			$('#err_ds_no_sketch_fra').addClass('hidden');
			var fg_exito=1;
			
		}else{
			element_tab_fra.style.background="#fbbdbd";//pinta rojo la tab espaniol
			$('#err_ds_no_sketch_fra').removeClass('hidden');
			var fg_exito=0;	
			return;
		}
		*/
		
		
		
		
		
	}else{
	    $('#err_ds_no_sketch').addClass('hidden');
	    var fg_exito=1;
		
	}
	
		//Si seleeeciona debe llenar descripcion 
	if(fg_ref_sketch.checked){
		
		var content_3 = document.getElementById("content_3");
		var content_3_esp = document.getElementById("content_3_esp");
		var content_3_fra = document.getElementById("content_3_fra");	
		var fg_ref_sketch=1;
		
		if(ds_ref_sketch.length>0){
		   $('#err_ds_ref_sketch').addClass('hidden');
		   content_3.style.borderColor = "";
           content_3.style.color = "";
           content_3.style.background = ""; 
		   var fg_exito=1;
		   
		   
		}else{
		   content_3.style.borderColor = "red";
           content_3.style.color = "red";
           content_3.style.background = "#fbbdbd"; 
		  $('#err_ds_ref_sketch').removeClass('hidden');	
		  var fg_exito=0;
		  return;			
		}
		
		if(ds_ref_sketch_esp.length>0){
		   $('#err_ds_ref_sketch_esp').addClass('hidden');
		   content_3_esp.style.borderColor = "";
           content_3_esp.style.color = "";
           content_3_esp.style.background = ""; 
		   element_tab_esp.style.background="";//pinta rojo la tab espaniol
		   var fg_exito=1;
		   
		   
		}else{
		   content_3_esp.style.borderColor = "red";
           content_3_esp.style.color = "red";
           content_3_esp.style.background = "#fbbdbd"; 	   
		   $('#err_ds_ref_sketch_esp').removeClass('hidden');
		   element_tab_esp.style.background="#fbbdbd";//pinta rojo la tab espaniol		   
		  var fg_exito=0;
		  return;			
		}
		
		if(ds_ref_sketch_fra.length>0){
		   $('#err_ds_ref_sketch_fra').addClass('hidden');
		   content_3_fra.style.borderColor = "";
           content_3_fra.style.color = "";
           content_3_fra.style.background = ""; 
		   element_tab_fra.style.background="";//pinta rojo la tab espaniol
		   var fg_exito=1;
		   
		   
		}else{
		   content_3_fra.style.borderColor = "red";
           content_3_fra.style.color = "red";
           content_3_fra.style.background = "#fbbdbd"; 	   
		   $('#err_ds_ref_sketch_fra').removeClass('hidden');
		   element_tab_fra.style.background="#fbbdbd";//pinta rojo la tab espaniol		   
		  var fg_exito=0;
		  return;			
		}
		
		
		
	}else{
		var fg_ref_sketch=0;
		$('#err_ds_ref_sketch').addClass('hidden');	
		var fg_exito=1;
	}

	var datos = new FormData();
	datos.append('clave',clave);
	datos.append('tab',tab);
	datos.append('ds_tiempo_tarea',ds_tiempo_tarea);
	datos.append('fl_leccion_nuevo_creado',fl_leccion_nuevo_creado);
	datos.append('fg_animacion',fg_animacion);
	datos.append('ds_animacion',ds_animacion);
	datos.append('ds_animacion_esp',ds_animacion_esp);
	datos.append('ds_animacion_fra',ds_animacion_fra);
	datos.append('fg_ref_animacion',fg_ref_animacion);
	datos.append('ds_ref_animacion',ds_ref_animacion);
	datos.append('ds_ref_animacion_esp',ds_ref_animacion_esp);
	datos.append('ds_ref_animacion_fra',ds_ref_animacion_fra);
	datos.append('no_sketch',no_sketch);
	datos.append('ds_no_sketch_esp',ds_no_sketch_esp);
	datos.append('ds_no_sketch_fra',ds_no_sketch_fra);
	datos.append('ds_no_sketch',ds_no_sketch);
	datos.append('fg_ref_sketch',fg_ref_sketch);
	datos.append('ds_ref_sketch',ds_ref_sketch);
	datos.append('ds_ref_sketch_esp',ds_ref_sketch_esp);
	datos.append('ds_ref_sketch_fra',ds_ref_sketch_fra);
	

	//alert(fg_exito);
	//var fg_exito=0;
	if(fg_exito){	
		$.ajax({
		  type:"post",
		  url: 'site/lmedia_iu.php',
		  contentType:false, // se envie multipart
		  data:datos,
		  processData:false, // por si vamos enviar un archivo
		}).done(function(result){			  
			  var result = JSON.parse(result);
			  var fg_correcto_=result.fg_correcto;
			  
			  if(fg_correcto_==true){
				  
				  //alerta de exito.		  
			      $.smallBox({
				  title : "<?php echo ObtenEtiqueta(2357);?>",
				  content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
				  color : "#276627",
				  iconSmall : "fa fa-thumbs-up bounce animated",
				  timeout : 4000
			      });
				  
				  //pasamos la sigueinte tab.
				  //$('#tab3').removeClass('active');
				  //$('#assignment').removeClass('active');
				  //$('#tab4').addClass('active');
				  //$('#quiz').addClass('active in');
			  }		  
		});	
	}	
}




/*Pertenece la tab 3 */

//show decription fg_animation
//	function MuestraDescAssig() {
		
//		var check = document.getElementById("fg_animacion");
//		var div_=document.getElementById("content_p");
//		var desc1=CKEDITOR.instances.ds_animacion.getData();
		
//		if (check.checked){ 
//			div_.style.display='block';
//			if(desc1==0){
//			   $('#err_ds_animacion').removeClass('hidden');		  
//			}else{
//			   $('#err_ds_animacion').addClass('hidden');	
//			}
//		}
//		else {
//			div_.style.display='none';
//		}
//	}
	
	
	
//	$('#fg_animacion').change(function () {	
          //  MuestraDescAssig();
//    });



	

//   function MuestraDescAssigRef() {
		
//		var check = document.getElementById("fg_ref_animacion");
//		var div_=document.getElementById("content_2");
//		var desc1=CKEDITOR.instances.ds_ref_animacion.getData();
		
//		if (check.checked){ 
//			div_.style.display='block';
//			if(desc1==0){
//			   $('#err_ds_ref_animacion').removeClass('hidden');		  
//			}else{
//			   $('#err_ds_ref_animacion').addClass('hidden');	
//			}
//		}
//		else {
//			div_.style.display='none';
//		}
//	}
//	$('#fg_ref_animacion').change(function () {	
           // MuestraDescAssigRef();
//    });


//function MuestraDescSketchNum(){
	
//	    var check = document.getElementById("no_sketch").value;
//		var div_=document.getElementById("content_4");
//		var desc1=CKEDITOR.instances.ds_no_sketch.getData();
	
//		if (check>=1){ 
//			div_.style.display='block';
//			if(desc1==0){
//			   $('#err_ds_no_sketch').removeClass('hidden');		  
//			}else{
//			   $('#err_ds_no_sketch').addClass('hidden');	
//			}
//		}
//		else {
//			div_.style.display='none';
//		}
	
	
//}
//$('#no_sketch').change(function () {	
         //   MuestraDescSketchNum();
//});


//function MuestraDescSketch(){
	
//	    var check = document.getElementById("fg_ref_sketch");
//		var div_=document.getElementById("content_3");
//		var desc1=CKEDITOR.instances.ds_ref_sketch.getData();
		
//		if (check.checked){ 
//			div_.style.display='block';
//			if(desc1==0){
//			   $('#err_ds_ref_sketch').removeClass('hidden');		  
//			}else{
//			   $('#err_ds_ref_sketch').addClass('hidden');	
//			}
//		}
//		else {
//			div_.style.display='none';
//		}
	
	
//}

//$('#fg_ref_sketch').change(function () {	
        //    MuestraDescSketch();
//});



//siempre se ejecuta para mostrra/ descripcion fg_animacion chekc
// MuestraDescAssig();  
//Siempre se ejetuta pata morstrar/descripcion fg_ref_animtaion check
//MuestraDescAssigRef();
//MuestraDescSketchNum(); 
//MuestraDescSketch();
/*end tab 3*/

/*Cuando es nuevo registro desabilitamos las tabs y cuando guarden la primer tab se desabilitan*/
<?php 
if($fg_nuevo_registro==1){
?>
$('#tab2').addClass('disabled');
$('#tab3').addClass('disabled');
$('#tab4').addClass('disabled');
$('#tab5').addClass('disabled');
document.getElementById('tab_2').style.pointerEvents = 'none';
document.getElementById('tab_3').style.pointerEvents = 'none';
document.getElementById('tab_4').style.pointerEvents = 'none';
document.getElementById('tab_5').style.pointerEvents = 'none';
<?php } ?>
</script>


<!---=========Validaciones quiz==============--->

<script>
  // Identificamos si existen algun campo modificado que active el Valida_Quiz
  var tquiz = '<?php echo $nb_quiz; ?>';
  var vquiz = '<?php echo $no_valor_quiz; ?>';
  var preg1 = '<?php echo $ds_pregunta_1; ?>';
  var vpreg1 = '<?php echo $ds_course_1; ?>';
  var resp1 = '<?php echo $ds_resp_1; ?>';
  var vresp1 = '<?php echo $ds_grade_1; ?>';
  var resp2 = '<?php echo $ds_resp_2; ?>';
  var vresp2 = '<?php echo $ds_grade_2; ?>';
  var resp3 = '<?php echo $ds_resp_3; ?>';
  var vresp3 = '<?php echo $ds_grade_3; ?>';
  var resp4 = '<?php echo $ds_resp_4; ?>';
  var vresp4 = '<?php echo $ds_grade_4; ?>';
  var pesos = '<?php echo $pesos; ?>';
  if (pesos == '')
    pesos = 0;
  if (tquiz != '' || vquiz > 0 || preg1 != '' || vpreg1 > 0 || resp1 != '' || resp2 != '' || resp3 != '' || resp4 != '' || pesos > 0) {
    $(document).ready(function() {
      Valida_Quiz();
    });
  }

  function Valida_Quiz() {
    // Activamos el tab
    //$("#tab_4").addClass("txt-color-red");
    // Boton
    var btn_save = $('footer > div > div > a:first');
    var remaining = $("#ds_course_1").val();
    // titulo y valor del quiz
    var tquiz = $("#nb_quiz").val();
    var vquiz = $("#no_valor_quiz").val();
    // Tipo de pregunta
    var type_respuesta;
    $('#fg_tipo_resp_1:checked').each(
      function() {
        type_respuesta = $(this).val();
      }
    );
    // pregunta y valor
    var preg1 = $("#ds_pregunta_1").val();
    var vpreg1 = $("#valor_1").val();
    var rem_pre1 = $("#ds_quiz_1").val();
    // respuestas dependiento del tipo de la respuesta
    if (type_respuesta == 'T') {
      var resp1 = $("#ds_resp_1").val();
      var vresp1 = $("#ds_grade_1").val();
      var resp2 = $("#ds_resp_2").val();
      var vresp2 = $("#ds_grade_2").val();
      var resp3 = $("#ds_resp_3").val();
      var vresp3 = $("#ds_grade_3").val();
      var resp4 = $("#ds_resp_4").val();
      var vresp4 = $("#ds_grade_4").val();
    } else {
      var resp1 = $("#nb_img_prev_mydropzone_1").val();
      var vresp1 = $("#ds_grade_img_1").val();
      var resp2 = $("#nb_img_prev_mydropzone_2").val();
      var vresp2 = $("#ds_grade_img_2").val();
      var resp3 = $("#nb_img_prev_mydropzone_3").val();
      var vresp3 = $("#ds_grade_img_3").val();
      var resp4 = $("#nb_img_prev_mydropzone_4").val();
      var vresp4 = $("#ds_grade_img_4").val();
    }
    // En los pesos de las preguntas por lo menos debe exitir un 100
    // creamos un array con los valores
    var myArr = [vresp1, vresp2, vresp3, vresp4];
    // con este idicamos que hay porlo menos un 100 en los campos
    var pesos = myArr.includes('100');
    // alert(pesos);
    // Si el remaining del quiz no debera ser menos a cer    
    if (remaining < 0) {
      btn_save.addClass('disabled');
    } else {
      btn_save.removeClass('disabled');
    }

    // Si el remaining la pregunta1 no debera ser menos a cer
    if (rem_pre1 < 0)
      btn_save.addClass('disabled');
    else
      btn_save.removeClass('disabled');

    // Buscamos los valores de las preguntas si todas son 100 esta bien en caso contrario esta mal
    var valores_preguntas = $("#NoPreguntas_temporal").val(),
      tot_vals_pregunts = 0;
    if (valores_preguntas == 0)
      valores_preguntas = 1;
    for (var p = 1; p <= valores_preguntas; p++) {
      // Obtenemos los valores de las preguntas
      var vls_pre = $("#valor_" + p).val();
      if (($("#valor_" + p).length) == 0)
        vls_pre = 0;
      tot_vals_pregunts = parseFloat(tot_vals_pregunts) + parseFloat(vls_pre);
    }

    for (var q = 1; q <= valores_preguntas; q++) {
      if (tot_vals_pregunts == 100 || vpreg1 == 100) {
        if (q == 1)
          $('#div_no_semana3').removeClass('has-error');
        else
          $('#div_no_semana3_' + q).removeClass('has-error');
        if ($("#valor_" + p).length == 1)
          document.getElementById('valor_' + q).style.backgroundColor = '#FFF';
      } else {
        $('#div_no_semana3').addClass('gabriel has-error');
        $('#div_no_semana3_' + q).removeClass('state-error').addClass('has-error');
        if (($('#valor_' + q).length) == 1) {
          document.getElementById('valor_' + q).style.backgroundColor = '#FFF0F0';
        }
      }
    }

    // Por lo menos debe existir un 100  en los pesos
    if (pesos == false) {
      for (var h = 1; h <= 4; h++) {
        // dependiendo del tipo del respuesta
        if (type_respuesta == 'T') {
          $('#div_ds_grade_' + h).removeClass('state-error').addClass('has-error');
          document.getElementById('ds_grade_' + h).style.backgroundColor = '#FFF0F0';
        } else {
          $('#div_ds_grade_img_' + h).removeClass('state-error').addClass('has-error');
          document.getElementById('ds_grade_img_' + h).style.backgroundColor = '#FFF0F0';
        }
      }
      // btn_save.addClass('disabled');
    } else {
      for (var h = 1; h <= 4; h++) {
        // dependiendo del tipo del respuesta
        if (type_respuesta == 'T') {
          $('#div_ds_grade_' + h).removeClass('has-error');
          document.getElementById('ds_grade_' + h).style.backgroundColor = '#FFF';
        } else {
          $('#div_ds_grade_img_' + h).removeClass('has-error');
          document.getElementById('ds_grade_img_' + h).style.backgroundColor = '#FFF';
        }
      }
    }

    if ((tquiz != '' && vquiz > 0) || (tquiz == '' && vquiz > 0) || (tquiz != '' && (vquiz == 0 || vquiz == ''))) {
      // Si los campos estan llenos y cumplen las condiciones activamos el boton
      if (tquiz != '' && vquiz > 0 && preg1 != '' && vpreg1 > 0 && resp1 != '' && resp2 != '' && resp3 != '' && resp4 != '' && remaining >= 0 && rem_pre1 >= 0 && pesos == true && tot_vals_pregunts == 100) {
        $('#div_nb_quiz').removeClass('has-error');
        document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
        $('#div_no_semana2').removeClass('input has-error');
        document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
        document.getElementById('no_valor_quiz').style.borderColor = '#ccc';
        $('#div_ds_pregunta_1').removeClass('has-error');
        document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF';
        $('#div_no_semana3').removeClass('has-error');
        document.getElementById('valor_1').style.backgroundColor = '#FFF';
        document.getElementById('valor_1').style.borderColor = '#ccc';
        for (var r = 1; r <= 4; r++) {
          if (type_respuesta == 'T') {
            $('#div_ds_resp_' + r).removeClass('has-error');
            document.getElementById('ds_resp_' + r).style.backgroundColor = '#FFF';
            $('#div_ds_grade_' + r).removeClass('has-error');
            document.getElementById('ds_grade_' + r).style.backgroundColor = '#FFF';
          } else {
            $('#mydropzone_' + r).removeClass('bg-color-red');
            $('#div_ds_grade_img_' + r).removeClass('has-error');
            document.getElementById('ds_grade_img_' + r).style.backgroundColor = '#FFF';
          }
        }

        // habilitamos el boton
        btn_save.removeClass('disabled');
        // Quitamos color
        $("#tab_4").removeClass("txt-color-red");
        $("#error_preguntas_valores").addClass('hidden');
      } else {
        $('#div_ds_pregunta_1').addClass('has-error');
        document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF0F0';
        $('#div_no_semana3').addClass('has-error');
        document.getElementById('valor_1').style.backgroundColor = '#FFF0F0';

        // Titulo y valor del quiz llenos
        if (tquiz != '' && vquiz > 0) {
          if (preg1 == '' && vpreg1 <= 0 && resp1 == '' && resp2 == '' && resp3 == '' && resp4 == '') {
            $("#cont_tab_quiz_1").click();
          }
          $('#div_nb_quiz').removeClass('has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
          $('#div_no_semana2').removeClass('has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
          btn_save.addClass('disabled');
        }

        // Titulo del quiz lleno y valor vacio
        if (tquiz != '' && (vquiz == 0 || vquiz == '')) {
          $('#div_no_semana2').click();
          if (tquiz != '') {
            $('#div_nb_quiz').removeClass('has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
          }
          if ((vquiz == 0 || vquiz == '')) {
            $('#div_no_semana2').removeClass('state-error').addClass('input has-error');
            document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
          }
          btn_save.addClass('disabled');
        }


        // Valor del quiz lleno y titulo vacio
        if (tquiz == '' && vquiz > 0) {
          $('#div_nb_quiz').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
          $('#div_no_semana2').remove('has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
          btn_save.addClass('disabled');
        }


        // Si algunos campos estan llenos entonces no los marcara dependiento del tipo de respuesta
        if (preg1 != '') {
          if (vpreg1 <= 0 || vpreg1 == '')
            $("#div_no_semana3").click();
          $('#div_ds_pregunta_1').removeClass('has-error');
          document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF';
        }

        if (vpreg1 >= 0 || vpreg1 <= 100) {
          if (vpreg1 == 0) {
            $('#div_no_semana3').removeClass('state-error').addClass('has-error');
            document.getElementById('valor_1').style.backgroundColor = '#FFF0F0';
          } else {
            if (vpreg1 > 0 && vpreg1 < 100) {
              $('#div_no_semana3').removeClass('has-error');
              document.getElementById('valor_1').style.backgroundColor = '#FFF';
              document.getElementById('valor_1').style.borderColor = '#E1C555';
            }
            if (vpreg1 == 100) {
              $('#div_no_semana3').removeClass('has-error');
              document.getElementById('valor_1').style.backgroundColor = '#FFF';
            }
          }

          // $("#error_preguntas_valores").removeClass('hidden');
          $("#error_preguntas_valores").addClass('hidden');
        } else {
          $("#error_preguntas_valores").addClass('hidden');
        }

        if (tot_vals_pregunts < 100) {
          $("#div_no_semana3").click();
        }

        if (type_respuesta == 'T') {

          if (resp1 == '') {
            $('#div_ds_resp_1').removeClass('state-error').addClass('has-error');
            document.getElementById('ds_resp_1').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_1').removeClass('has-error');
            document.getElementById('ds_resp_1').style.backgroundColor = '#FFF';
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_1').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_1').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_1').removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_1').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_1').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_1').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_1').style.borderColor = '#E1C555';
            }
          }
          if (resp2 == '') {
            $('#div_ds_resp_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_2').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_2').removeClass('has-error');
            document.getElementById('ds_resp_2').style.backgroundColor = '#FFF';
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_2').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_2').removeClass('has-error');
            if (vresp2 == 100 || pesos == true) {
              document.getElementById('ds_grade_2').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_2').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_2').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_2').style.borderColor = '#E1C555';
            }
          }
          if (resp3 == '') {
            $('#div_ds_resp_3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_3').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_3').removeClass('has-error');
            document.getElementById('ds_resp_3').style.backgroundColor = '#FFF';
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_3').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_3').removeClass('has-error');
            if (vresp3 == 100 || pesos == true) {
              document.getElementById('ds_grade_3').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_3').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_3').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_3').style.borderColor = '#E1C555';
            }
          }
          if (resp4 == '') {
            $('#div_ds_resp_4').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_4').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_4').removeClass('has-error');
            document.getElementById('ds_resp_4').style.backgroundColor = '#FFF';
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_4').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_4').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_4').removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_4').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_4').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_4').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_4').style.borderColor = '#E1C555';
            }
          }
        } else {
          if (resp1 == '') {
            $('#mydropzone_1').addClass('bg-color-red');
          } else {
            $('#mydropzone_1').removeClass('bg-color-red');
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_img_1').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_1').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_1').removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_1').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_1').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_1').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_1').style.borderColor = '#E1C555';
            }
          }
          if (resp2 == '') {
            $('#mydropzone_2').addClass('bg-color-red');
          } else {
            $('#mydropzone_2').removeClass('bg-color-red');
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_img_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_2').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_2').removeClass('has-error');
            if (vresp2 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_2').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_2').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_2').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_2').style.borderColor = '#E1C555';
            }
          }
          if (resp3 == '') {
            $('#mydropzone_3').addClass('bg-color-red');
          } else {
            $('#mydropzone_3').removeClass('bg-color-red');
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_img_3').removeClass('state-error').addClass('form-grouphas-error');
            document.getElementById('ds_grade_img_3').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_3').removeClass('has-error');
            if (vresp3 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_3').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_3').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_3').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_3').style.borderColor = '#E1C555';
            }
          }
          if (resp4 == '') {
            $('#mydropzone_4').addClass('bg-color-red');
          } else {
            $('#mydropzone_4').removeClass('bg-color-red');
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_img_4').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_4').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_grade_img_4').removeClass('has-error');
            if (vresp1 == 100 || pesos == true) {
              document.getElementById('ds_grade_img_4').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_4').style.borderColor = '#ccc';
            } else {
              document.getElementById('ds_grade_img_4').style.backgroundColor = '#FFF';
              document.getElementById('ds_grade_img_4').style.borderColor = '#E1C555';
            }
          }
        }

        if (pesos == true) {
          for (var m = 1; m <= 4; m++) {
            // Dependiendo del tipo del respuesta
            if (type_respuesta == 'T') {
              $('#div_ds_grade_' + m).removeClass('input has-error');
              document.getElementById('ds_grade_' + m).style.backgroundColor = '#FFF';
            } else {
              $('#div_ds_grade_img_' + m).removeClass('input has-error');
              document.getElementById('ds_grade_img_' + m).style.backgroundColor = '#FFF';
            }
          }
          if (resp1 == '' || resp2 == '' || resp3 == '' || resp4 == '')
            $("#error_preguntas_valores").removeClass('hidden');
        }


      }
    } else {
      // si todo esta en blanco      
      if (tquiz == '' && (vquiz == 0 || vquiz == '') && preg1 == '' && (vpreg1 == 0 || vpreg1 == '') &&
        resp1 == '' && resp2 == '' && resp3 == '' && resp4 == '' &&
        (vresp1 == 0 || vresp1 == '') && (vresp2 == 0 || vresp2 == '') && (vresp3 == 0 && vresp3 == '') && (vresp4 == 0 || vresp4 == '')
      ) {
        $('#div_nb_quiz').removeClass('has-error');
        document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
        $('#div_no_semana2').removeClass('has-error');
        document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF';
        $('#div_ds_pregunta_1').removeClass('has-error');
        document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF';
        $('#div_no_semana3').removeClass('has-error');
        document.getElementById('valor_1').style.backgroundColor = '#FFF';
        // document.getElementById('valor_1').style.borderColor = '#ccc';   
        // Muestra mensaje de warning
        // $("#error_t_v_quiz").hide();
        for (var j = 1; j <= 4; j++) {
          if (type_respuesta == 'T') {
            $('#div_ds_resp_' + j).removeClass('has-error');
            document.getElementById('ds_resp_' + j).style.backgroundColor = '#FFF';
            $('#div_ds_grade_' + j).removeClass('has-error');
            document.getElementById('ds_grade_' + j).style.backgroundColor = '#FFF';
          } else {
            $('#mydropzone_' + j).removeClass('bg-color-red');
            $('#div_ds_grade_img_' + j).removeClass('has-error');
            document.getElementById('ds_grade_img_' + j).style.backgroundColor = '#FFF';
          }
        }

        // habilitamos boton
        btn_save.removeClass('disabled');
        // Quitamos color
        $("#tab_4").removeClass("txt-color-red");
        $("#error_preguntas_valores").addClass('hidden');
      } else {
        if (tquiz == '') {
          $('#div_nb_quiz').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
        } else {
          $('#div_nb_quiz').remove('has-error');
          document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
        }
        if (vquiz == 0 || vquiz == '') {
          $('#div_no_semana2').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
        }
        if (preg1 == '') {
          $('#div_ds_pregunta_1').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF0F0';
        } else {
          $('#div_ds_pregunta_1').removeClass('input has-error');
          document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF';
          if (tquiz == '') {
            $('#div_nb_quiz').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_nb_quiz').remove('has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
          }
          if (vquiz == 0 || vquiz == '') {
            $('#div_no_semana2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
          }
          if (vpreg1 == 0 || vpreg1 == '') {
            $('#div_no_semana3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('valor_1').style.backgroundColor = '#FFF0F0';
          }
          for (var j = 1; j <= 4; j++) {
            if (type_respuesta == 'T') {
              if ($('#ds_resp_' + j).val() == '') {
                $('#div_ds_resp_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_resp_' + j).style.backgroundColor = '#FFF0F0';
              }
              if ($('#ds_grade_' + j).val() == 0 || $('#ds_grade_' + j).val() == '') {
                $('#div_ds_grade_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_grade_' + j).style.backgroundColor = '#FFF0F0';
              }
            } else {
              if ($('#nb_img_prev_mydropzone_' + j).val() == '') {
                $('#mydropzone_' + j).addClass('bg-color-red');
              }
              if ($('#ds_grade_img_' + j).val() == 0 || $('#ds_grade_img_' + j).val() == '') {
                $('#div_ds_grade_img_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_grade_img_' + j).style.backgroundColor = '#FFF0F0';
              }
            }
          }
          btn_save.addClass('disabled');
        }
        if (vpreg1 == 0) {
          $('#div_no_semana3').removeClass('state-error').addClass('form-group has-error');
          document.getElementById('valor_1').style.backgroundColor = '#FFF0F0';
        } else {
          if (tquiz == '') {
            $('#div_nb_quiz').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF0F0';
          } else {

            $('#div_nb_quiz').remove('has-error');
            document.getElementById('nb_quiz').style.backgroundColor = '#FFF';
          }
          if (vquiz == 0 || vquiz == '') {
            $('#div_no_semana2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('no_valor_quiz').style.backgroundColor = '#FFF0F0';
          }
          if (preg1 == '') {
            $('#div_ds_pregunta_1').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_pregunta_1').style.backgroundColor = '#FFF0F0';
          }
          $('#div_no_semana3').removeClass('has-error');
          document.getElementById('valor_1').style.backgroundColor = '#FFF';
          for (var j = 1; j <= 4; j++) {
            if (type_respuesta == 'T') {
              if ($('#ds_resp_' + j).val() == '') {
                $('#div_ds_resp_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_resp_' + j).style.backgroundColor = '#FFF0F0';
              }
              if ($('#ds_grade_' + j).val() == 0 || $('#ds_grade_' + j).val() == '') {
                $('#div_ds_grade_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_grade_' + j).style.backgroundColor = '#FFF0F0';
              }
            } else {
              if ($('#nb_img_prev_mydropzone_' + j).val() == '') {
                $('#mydropzone_' + j).addClass('bg-color-red');
              }
              if ($('#ds_grade_img_' + j).val() == 0 || $('#ds_grade_img_' + j).val() == '') {
                $('#div_ds_grade_img_' + j).removeClass('state-error').addClass('form-group has-error');
                document.getElementById('ds_grade_img_' + j).style.backgroundColor = '#FFF0F0';
              }

            }
          }
          btn_save.addClass('disabled');
        }

        if (type_respuesta == 'T') {
          if (resp1 == '') {
            $('#div_ds_resp_1').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_1').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_1').removeClass('has-error');
            document.getElementById('ds_resp_1').style.backgroundColor = '#FFF';
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_1').removeClass('tate-error').addClass('form-group has-error');
            document.getElementById('ds_grade_1').style.backgroundColor = '#FFF0F0';
          }
          if (resp2 == '') {
            $('#div_ds_resp_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_2').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_2').removeClass('has-error');
            document.getElementById('ds_resp_2').style.backgroundColor = '#FFF';
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_2').style.backgroundColor = '#FFF0F0';
          }
          if (resp3 == '') {
            $('#div_ds_resp_3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_3').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_3').removeClass('has-error');
            document.getElementById('ds_resp_3').style.backgroundColor = '#FFF';
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_3').style.backgroundColor = '#FFF0F0';
          }
          if (resp4 == '') {
            $('#div_ds_resp_4').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_resp_4').style.backgroundColor = '#FFF0F0';
          } else {
            $('#div_ds_resp_4').removeClass('has-error');
            document.getElementById('ds_resp_4').style.backgroundColor = '#FFF';
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_4').removeClass(' state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_4').style.backgroundColor = '#FFF0F0';
          }
        } else {
          if (resp1 == '') {
            $('#mydropzone_1').addClass('bg-color-red');
          } else {
            $('#mydropzone_1').removeClass('bg-color-red');
          }
          if ((vresp1 == 0 || vresp1 == '') && pesos == false) {
            $('#div_ds_grade_img_1').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_1').style.backgroundColor = '#FFF0F0';
          }
          if (resp2 == '') {
            $('#mydropzone_2').addClass('bg-color-red');
          } else {
            $('#mydropzone_2').removeClass('bg-color-red');
          }
          if ((vresp2 == 0 || vresp2 == '') && pesos == false) {
            $('#div_ds_grade_img_2').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_2').style.backgroundColor = '#FFF0F0';
          }
          if (resp3 == '') {
            $('#mydropzone_3').addClass('bg-color-red');
          } else {
            $('#mydropzone_3').removeClass('bg-color-red');
          }
          if ((vresp3 == 0 || vresp3 == '') && pesos == false) {
            $('#div_ds_grade_img_3').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_3').style.backgroundColor = '#FFF0F0';
          }
          if (resp4 == '') {
            $('#mydropzone_4').addClass('bg-color-red');
          } else {
            $('#mydropzone_4').removeClass('bg-color-red');
          }
          if ((vresp4 == 0 || vresp4 == '') && pesos == false) {
            $('#div_ds_grade_img_4').removeClass('state-error').addClass('form-group has-error');
            document.getElementById('ds_grade_img_4').style.backgroundColor = '#FFF0F0';
          }
        }

        if (pesos == true) {
          for (var m = 1; m <= 4; m++) {
            // Dependiendo del tipo del respuesta
            if (type_respuesta == 'T') {
              $('#div_ds_grade_' + m).removeClass('has-error');
              document.getElementById('ds_grade_' + m).style.backgroundColor = '#FFF';
            } else {
              $('#div_ds_grade_img_' + m).removeClass('has-error');
              document.getElementById('ds_grade_img_' + m).style.backgroundColor = '#FFF';
            }
          }
        }
        // Deshabilitamos el boton
        btn_save.addClass('disabled');
        $("#error_preguntas_valores").removeClass('hidden');
      }
    }
  }

  $("#nb_quiz").keypress(function() {
    Valida_Quiz();
  });
  $("#ds_pregunta_1").keypress(function() {
    Valida_Quiz();
  })
  for (var k = 1; k <= 4; k++) {
    $("#ds_resp_" + k).keypress(function() {
      Valida_Quiz();
    })
    // Agregamos a los pesos un popover
    $("#ds_grade_" + k).attr('rel', 'popover-hover').attr('data-placement', 'top').attr('data-original-title', '<?php echo $warning; ?>').attr('data-content', '<?php echo ObtenEtiqueta(1896); ?>');
  }
  
  
  function GuardarTabQuiz(){
	  
	  var nb_quiz=document.getElementById("nb_quiz").value;
	  var no_valor_quiz=document.getElementById("no_valor_quiz").value;
	  var clave=<?php echo $clave;?>;
      var tab=4;
      var fg_tipo_resp_1=$('#fg_tipo_resp_1').is(':checked') ? 'T' : 'I';
      var fg_tipo_img_1=$('#fg_tipo_img_1').is(':checked') ? 'L' : 'P';
      var ds_pregunta_1=document.getElementById("ds_pregunta_1").value;
	  var ds_pregunta_esp_1=document.getElementById("ds_pregunta_esp_1").value;
	  var ds_pregunta_fra_1=document.getElementById("ds_pregunta_fra_1").value;
      var valor_1=document.getElementById("valor_1").value;
	  var ds_quiz_1=document.getElementById("ds_quiz_1").value;
	  var q_remaining_1=document.getElementById("ds_quiz_1").value;
	  
	  var ds_grade_2=document.getElementById("ds_grade_2").value;
	  
	  var datos = new FormData();
	  datos.append('nb_quiz',nb_quiz);
	  datos.append('no_valor_quiz',no_valor_quiz);
	  datos.append('clave',clave);
	  datos.append('tab',tab);

	  datos.append('fg_tipo_resp_1',fg_tipo_resp_1);
	  datos.append('fg_tipo_img_1',fg_tipo_img_1);
	  datos.append('ds_pregunta_1',ds_pregunta_1);
	  datos.append('ds_pregunta_esp_1',ds_pregunta_esp_1);
	  datos.append('ds_pregunta_fra_1',ds_pregunta_fra_1);
	  datos.append('valor_1',valor_1);
	  datos.append('ds_quiz_1',ds_quiz_1);
	  datos.append('q_remaining_1',q_remaining_1);


	  if(fg_tipo_resp_1=='T'){

	      var ds_resp_1=document.getElementById("ds_resp_1").value;
	      var ds_resp_esp_1=document.getElementById("ds_resp_esp_1").value;
	      var ds_resp_fra_1=document.getElementById("ds_resp_fra_1").value;
	      var ds_grade_1=document.getElementById("ds_grade_1").value;
	      var ds_resp_2=document.getElementById("ds_resp_2").value;
	      var ds_resp_esp_2=document.getElementById("ds_resp_esp_2").value;
	      var ds_resp_fra_2=document.getElementById("ds_resp_fra_2").value;
	      var ds_grade_2=document.getElementById("ds_grade_2").value;
	      var ds_resp_3=document.getElementById("ds_resp_3").value;
	      var ds_resp_esp_3=document.getElementById("ds_resp_esp_3").value;
	      var ds_resp_fra_3=document.getElementById("ds_resp_fra_3").value;
	      var ds_grade_3=document.getElementById("ds_grade_3").value;
	      var ds_resp_4=document.getElementById("ds_resp_4").value;
	      var ds_resp_esp_4=document.getElementById("ds_resp_esp_4").value;
	      var ds_resp_fra_4=document.getElementById("ds_resp_fra_4").value;
	      var ds_grade_4=document.getElementById("ds_grade_4").value;
	  
	      datos.append('ds_resp_1',ds_resp_1);
	      datos.append('ds_resp_esp_1',ds_resp_esp_1);
	      datos.append('ds_resp_fra_1',ds_resp_fra_1);
	      datos.append('ds_grade_1',ds_grade_1);
	      datos.append('ds_resp_2',ds_resp_2);
	      datos.append('ds_resp_esp_2',ds_resp_esp_2);
	      datos.append('ds_resp_fra_2',ds_resp_fra_2);
	      datos.append('ds_grade_2',ds_grade_2);
	      datos.append('ds_resp_3',ds_resp_3);
	      datos.append('ds_resp_esp_3',ds_resp_esp_3);
	      datos.append('ds_resp_fra_3',ds_resp_fra_3);
	      datos.append('ds_grade_3',ds_grade_3);
	      datos.append('ds_resp_4',ds_resp_4);
	      datos.append('ds_resp_esp_4',ds_resp_esp_4);
	      datos.append('ds_resp_fra_4',ds_resp_fra_4);
	      datos.append('ds_grade_4',ds_grade_4);
	  
	  }

	  if(fg_tipo_resp_1=='I'){

	      var nb_img_prev_mydropzone_1=document.getElementById("nb_img_prev_mydropzone_1").value;
	      var ds_grade_img_1=document.getElementById("ds_grade_img_1").value;
	      var nb_img_prev_mydropzone_2=document.getElementById("nb_img_prev_mydropzone_2").value;
	      var ds_grade_img_2=document.getElementById("ds_grade_img_2").value;
	      var nb_img_prev_mydropzone_3=document.getElementById("nb_img_prev_mydropzone_3").value;
	      var ds_grade_img_3=document.getElementById("ds_grade_img_3").value;
	      var nb_img_prev_mydropzone_4=document.getElementById("nb_img_prev_mydropzone_4").value;
	      var ds_grade_img_4=document.getElementById("ds_grade_img_4").value;

	      datos.append('nb_img_prev_mydropzone_1',nb_img_prev_mydropzone_1);
	      datos.append('ds_grade_img_1',ds_grade_img_1);
	      datos.append('nb_img_prev_mydropzone_2',nb_img_prev_mydropzone_2);
	      datos.append('ds_grade_img_2',ds_grade_img_2);
	      datos.append('nb_img_prev_mydropzone_3',nb_img_prev_mydropzone_3);
	      datos.append('ds_grade_img_3',ds_grade_img_3);
	      datos.append('nb_img_prev_mydropzone_4',nb_img_prev_mydropzone_4);
	      datos.append('ds_grade_img_4',ds_grade_img_4);	      

	  }

	  var no_max_tabs=document.getElementById("tabs_add").value;
	  datos.append('no_max_tabs',no_max_tabs);
	  var contador=0;
	  var fg_tipo_resp="fg_tipo_resp";
	  for(var i=2; i<=no_max_tabs; i++){

	      var fg_tipo_resp_="fg_tipo_resp_"+[i];
	      var fg_tipo_img_="fg_tipo_img_"+[i];
	      var ds_pregunta_="ds_pregunta_"+[i];
	      var ds_pregunta_esp_="ds_pregunta_esp_"+[i];
	      var ds_pregunta_fra_="ds_pregunta_fra_"+[i];
	      var ds_quiz_="ds_quiz_"+[i];
	      //var ds_course_="ds_course_"+[i];
	      var valor_="valor_"+[i];
	      var q_remaining_="q_remaining_"+[i];

	      fg_tipo_resp_=$('#fg_tipo_resp_'+i).is(':checked') ? 'T' : 'I';
	      fg_tipo_img_=$('#fg_tipo_img_'+i).is(':checked') ? 'L' : 'P';
	      ds_pregunta_=document.getElementById("ds_pregunta_"+i).value;
	      ds_pregunta_esp_=document.getElementById("ds_pregunta_esp_"+i).value;
	      ds_pregunta_fra_=document.getElementById("ds_pregunta_fra_"+i).value;
	      ds_quiz_=document.getElementById("ds_quiz_"+i).value;
	      //ds_course_=document.getElementById("ds_course_"+i).value;
	      valor_=document.getElementById("valor_"+i).value;
	      q_remaining_=document.getElementById("ds_quiz_"+i).value;
          
	      datos.append('fg_tipo_resp_'+[i],fg_tipo_resp_);
	      datos.append('fg_tipo_img_'+[i],fg_tipo_img_);
	      datos.append('ds_pregunta_'+[i],ds_pregunta_);
	      datos.append('ds_pregunta_esp_'+[i],ds_pregunta_esp_);
	      datos.append('ds_pregunta_fra_'+[i],ds_pregunta_fra_);
	      datos.append('ds_quiz_'+[i],ds_quiz_);
	      //datos.append('ds_course_'+[i],ds_course_);
	      datos.append('valor_'+[i],valor_);
	      datos.append('q_remaining_'+[i],q_remaining_);
          

	      if(fg_tipo_resp_=="T"){
 
	          var ds_resp_1_="ds_resp_1_"+[i];
	          var ds_resp_esp_1_="ds_resp_esp_1_"+[i];
	          var ds_resp_fra_1_="ds_resp_fra_1_"+[i];
	          var ds_grade_1_="ds_grade_1_"+[i];

	          var ds_resp_2_="ds_resp_2_"+[i];
	          var ds_resp_esp_2_="ds_resp_esp_2_"+[i];
	          var ds_resp_fra_2_="ds_resp_fra_2_"+[i];
	          var ds_grade_2_="ds_grade_2_"+[i];

	          var ds_resp_3_="ds_resp_3_"+[i];
	          var ds_resp_esp_3_="ds_resp_esp_3_"+[i];
	          var ds_resp_fra_3_="ds_resp_fra_3_"+[i];
	          var ds_grade_3_="ds_grade_3_"+[i];

	          var ds_resp_4_="ds_resp_4_"+[i];
	          var ds_resp_esp_4_="ds_resp_esp_4_"+[i];
	          var ds_resp_fra_4_="ds_resp_fra_4_"+[i];
	          var ds_grade_4_="ds_grade_4_"+[i];

	          ds_resp_1_=document.getElementById("ds_resp_1_"+i).value;  //ds_resp_1_2
	          ds_resp_esp_1_=document.getElementById("ds_resp_esp_1_"+i).value;
	          ds_resp_fra_1_=document.getElementById("ds_resp_fra_1_"+i).value;
	          ds_grade_1_=document.getElementById("ds_grade_1_"+i).value;
	          
	          ds_resp_2_=document.getElementById("ds_resp_2_"+i).value;
	          ds_resp_esp_2_=document.getElementById("ds_resp_esp_2_"+i).value;
	          ds_resp_fra_2_=document.getElementById("ds_resp_fra_2_"+i).value;
	          ds_grade_2_=document.getElementById("ds_grade_2_"+i).value;

	          ds_resp_3_=document.getElementById("ds_resp_3_"+i).value;
	          ds_resp_esp_3_=document.getElementById("ds_resp_esp_3_"+i).value;
	          ds_resp_fra_3_=document.getElementById("ds_resp_fra_3_"+i).value;
	          ds_grade_3_=document.getElementById("ds_grade_3_"+i).value;

	          ds_resp_4_=document.getElementById("ds_resp_4_"+i).value;
	          ds_resp_esp_4_=document.getElementById("ds_resp_esp_4_"+i).value;
	          ds_resp_fra_4_=document.getElementById("ds_resp_fra_4_"+i).value;
	          ds_grade_4_=document.getElementById("ds_grade_4_"+i).value;

	          datos.append('ds_resp_1_'+i,ds_resp_1_);
	          datos.append('ds_resp_esp_1_'+i,ds_resp_esp_1_);
	          datos.append('ds_resp_fra_1_'+i,ds_resp_fra_1_);
	          datos.append('ds_grade_1_'+i,ds_grade_1_);

	          datos.append('ds_resp_2_'+i,ds_resp_2_);
	          datos.append('ds_resp_esp_2_'+i,ds_resp_esp_2_);
	          datos.append('ds_resp_fra_2_'+i,ds_resp_fra_2_);
	          datos.append('ds_grade_2_'+i,ds_grade_2_);

	          datos.append('ds_resp_3_'+i,ds_resp_3_);
	          datos.append('ds_resp_esp_3_'+i,ds_resp_esp_3_);
	          datos.append('ds_resp_fra_3_'+i,ds_resp_fra_3_);
	          datos.append('ds_grade_3_'+i,ds_grade_3_);

	          datos.append('ds_resp_4_'+i,ds_resp_4_);
	          datos.append('ds_resp_esp_4_'+i,ds_resp_esp_4_);
	          datos.append('ds_resp_fra_4_'+i,ds_resp_fra_4_);
	          datos.append('ds_grade_4_'+i,ds_grade_4_);


	      }else{
	          
	          var nb_img_prev_mydropzone_1_="nb_img_prev_mydropzone_1_"+[i];
	          var ds_grade_img_1_="ds_grade_img_1_"+[i];
	          var nb_img_prev_mydropzone_2_="nb_img_prev_mydropzone_2_"+[i];
	          var ds_grade_img_2_="ds_grade_img_2_"+[i];
	          var nb_img_prev_mydropzone_3_="nb_img_prev_mydropzone_3_"+[i];
	          var ds_grade_img_3_="ds_grade_img_3_"+[i];
	          var nb_img_prev_mydropzone_4_="nb_img_prev_mydropzone_4_"+[i];
	          var ds_grade_img_4_="ds_grade_img_4_"+[i];
	          
	          nb_img_prev_mydropzone_1_=document.getElementById("nb_img_prev_mydropzone_1_"+i).value;
	          ds_grade_img_1_=document.getElementById("ds_grade_img_1_"+i).value;
	          nb_img_prev_mydropzone_2_=document.getElementById("nb_img_prev_mydropzone_2_"+i).value;
	          ds_grade_img_2_=document.getElementById("ds_grade_img_2_"+i).value;
	          nb_img_prev_mydropzone_3_=document.getElementById("nb_img_prev_mydropzone_3_"+i).value;
	          ds_grade_img_3_=document.getElementById("ds_grade_img_3_"+i).value;
	          nb_img_prev_mydropzone_4_=document.getElementById("nb_img_prev_mydropzone_4_"+i).value;
	          ds_grade_img_4_=document.getElementById("ds_grade_img_4_"+i).value;
	      
	          datos.append('nb_img_prev_mydropzone_1_'+i,nb_img_prev_mydropzone_1_);
	          datos.append('ds_grade_img_1_'+i,ds_grade_img_1_);

	          datos.append('nb_img_prev_mydropzone_2_'+i,nb_img_prev_mydropzone_2_);
	          datos.append('ds_grade_img_2_'+i,ds_grade_img_2_);

	          datos.append('nb_img_prev_mydropzone_3_'+i,nb_img_prev_mydropzone_3_);
	          datos.append('ds_grade_img_3_'+i,ds_grade_img_3_);

	          datos.append('nb_img_prev_mydropzone_4_'+i,nb_img_prev_mydropzone_4_);
	          datos.append('ds_grade_img_4_'+i,ds_grade_img_4_);
	      
	      
	      }
	     

	  }
	  //var no_max_tabs=document.getElementById("no_max_tabs").value;
	 
	if((nb_quiz.length>0)&&(no_valor_quiz>0)){
	  
	  //gramados datos.
	   $.ajax({
		  type:"post",
		  url: 'site/lmedia_iu.php',
		  contentType:false, // se envie multipart
		  data:datos,
		  processData:false, // por si vamos enviar un archivo
		}).done(function(result){			  
			  var result = JSON.parse(result);
			  var fg_correcto_=result.fg_correcto;
	  
			  
			  if(fg_correcto_==true){
				  				  
				  //alerta de exito.		  
			      $.smallBox({
				  title : "<?php echo ObtenEtiqueta(2357);?>",
			          content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
			          color : "#276627",
			          iconSmall : "fa fa-thumbs-up bounce animated",
			          timeout : 4000
			      });
 
              }
			   		  
		});
	  
	  
	}
	  
	  
  }
  
  function GuardarTabRubric(){
	  
	  var no_val_rub=document.getElementById("no_val_rub").value;
	  var clave=<?php echo $clave;?>;
	  var tab=5;
	  
	  var datos = new FormData();
	  datos.append('no_val_rub',no_val_rub);
	  datos.append('clave',clave);
	  datos.append('tab',tab);

	  $.ajax({
		  type:"post",
		  url: 'site/lmedia_iu.php',
		  contentType:false, // se envie multipart
		  data:datos,
		  processData:false, // por si vamos enviar un archivo
		}).done(function(result){			  
			  var result = JSON.parse(result);
			  var fg_correcto_=result.fg_correcto;
	  
			  
			  if(fg_correcto_==true){
				  				  
				  //alerta de exito.		  
			      $.smallBox({
				  title : "<?php echo ObtenEtiqueta(2357);?>",
				  content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
				  color : "#276627",
				  iconSmall : "fa fa-thumbs-up bounce animated",
				  timeout : 4000
			      });
 
			  }
			   		  
		});
	  
  }
  
  
  //para validar enteros
  function validaNumericos(event) {
    if(event.charCode >= 48 && event.charCode <= 57){
      return true;
     }
     return false;        
 }
  
  
 function VerificaProgreso_<?php echo $clave;?>(){
	        //alert('se va ejecutar');
	var intervalo=setInterval(function(){
			var total_convertido = $('#total_convertido').val(); 
			if(total_convertido<100){
			  $.ajax({
				  type: 'GET',
				  url : '../../AD3M2SRC4/modules/fame/progreso_comando.php',
				  data: 'clave=<?php echo $clave;?>'+
						'&archivo=<?php echo $ds_vl_ruta;?>'
			  }).done(function(result){
				var content, tabContainer;
				content = JSON.parse(result);
				progress = content.progress;

				//alert('hola'+progress);
				if(!content.error){
				  if(progress<=100){
					$('#duration').empty().append(content.duration + '&nbsp;Mins');
					$('#grl_progress_<?php echo $clave;?>').attr('data-progressbar-value', progress);
				    $('#progress_hls_<?php echo $clave;?>').empty().append(progress + '%');
					$('#camp_progreso_hls').empty().val(progress);
					$('#total_convertido').empty().val(progress);
				  }

				  if(progress==100){
				      $('#processing_<?php echo $clave;?>').css('display','none');
				      $('#encoding_video_<?php echo $clave;?>').css('display','none');
				      $('#completed_video_<?php echo $clave;?>').css('display','block');
				      $('#ds_vl_duracion').val(content.time_duration);
				      $('#total_convertido').empty().val(progress);
				      $('#imgvideo_<?php echo $clave;?>').attr('src', content.ruta_thumbnail_video);
				      clearInterval(intervalo);
				  }


				}
				else{
				  //$('#grl_progress1').empty().append('Error upload');
				}
			  });
			}
			$('#code_info').addClass('hidden');
		  }, 
		  2000);

	  
  }
  
  
<?php 
if (!empty($ds_vl_ruta)) {
  echo "
        // Consulta el archivo convertidor
		var total_convertido =".$total_convertido."; 
		//alert(total_convertido);
		if(total_convertido<100){
          //  alert('entro '+total_convertido);
			VerificaProgreso_$clave();
		}else{
			
		}
		  
		  ";
}

?>
  
</script>






 	  


