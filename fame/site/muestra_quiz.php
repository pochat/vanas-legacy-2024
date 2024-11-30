<?php

	# Libreria de funciones
	require("../lib/self_general.php");

  $fl_usuario_sp = RecibeParametroNumerico('fl_usuario_sp');
  $fl_leccion_sp = RecibeParametroNumerico('fl_leccion_sp');


  $row = RecuperaValor("SELECT p.nb_programa".$sufix." FROM c_leccion_sp l, c_programa_sp p WHERE l.fl_leccion_sp = $fl_leccion_sp AND l.fl_programa_sp = p.fl_programa_sp");
  $nb_curso = str_texto($row[0]);

  echo "<div class='modal-content'>
    <div class='modal-header'>
            <button type='button' class='close hidden' data-dismiss='modal' aria-hidden='true' onclick='muestra_resultados();'>
              &times;
            </button>
            <h4 class='modal-title' id='myModalLabel'><i class='fa fa-warning'></i> Lesson: <strong>$nb_curso</strong> </h4>
          </div>
          <div class='modal-body' style='padding-bottom:0px;'>
            <div class='row'>
              <article class='col-sm-12 col-md-12 col-lg-12'>
                <div class='jarviswidget' id='wid-id-2' data-widget-editbutton='false' data-widget-deletebutton='false' style='margin: 0 0 15px;'>
                  <!--header><h2>Fuel Wizard </h2></header-->
                  <!-- widget div-->
                  <div>
                    <div class='jarviswidget-editbox'><!-- This area used as dropdown edit box --></div>
                    <div class='widget-body fuelux'>
                      <div class='wizard'>
                        <ul class='steps'>";
  
  
                        #Verificamos si la la leccion ya tiene marcado como completo.
                        $Query="SELECT fg_quiz_complete,fg_complete FROM k_leccion_usu WHERE fl_usuario_sp=$fl_usuario_sp AND fl_leccion_sp=$fl_leccion_sp ";
                        $rw=RecuperaValor($Query);
                        $fg_complete_quiz=$rw['fg_quiz_complete'];
                        $fg_complete_lesson=$rw['fg_complete'];
                        
                        /*
                        if($fg_complete_lesson==1){
                            
                            #Mostrara las quiz propia del usuario, en el momento en que contesto la quiz
                            # Genera los tabs de preguntas
                            $Query_p  = " SELECT fl_quiz_pregunta, ds_pregunta, ds_valor_pregunta, fg_posicion_img, ds_course_pregunta, no_orden";
                            $Query_p .= " FROM k_quiz_pregunta_leccion_sp_usuario ";
                            $Query_p .= " WHERE fl_leccion_sp = $fl_leccion_sp AND fl_usuario_sp=$fl_usuario_sp "; 
                            $Query_p .= " ORDER BY no_orden ASC ";
                            $rs_p = EjecutaQuery($Query_p);
                          
                        
                        }else{
                         */   
                            #Mostrara la quiz que actualmente existe en la lección
                        
                           #Recuperamos
                           $Qyi=" SELECT MAX(fl_quiz_pregunta)
                                  FROM k_quiz_respuesta_usuario 
                                  WHERE fl_leccion_sp =$fl_leccion_sp AND fl_usuario = $fl_usuario_sp ";
                           $ropm=RecuperaValor($Qyi);
                           $fl_quiz_pregunta_max=$ropm[0];


                          


                           




                            # Genera los tabs de preguntas
                            $Query_p  = " SELECT fl_quiz_pregunta, ds_pregunta".$sufix.", ds_valor_pregunta, fg_posicion_img, ds_course_pregunta, no_orden";
                            $Query_p .= " FROM k_quiz_pregunta ";
                            $Query_p .= " WHERE fl_leccion_sp = $fl_leccion_sp"; 
                            $Query_p .= " ORDER BY no_orden ASC ";
                            $rs_p = EjecutaQuery($Query_p);
                            $rs_p2 = EjecutaQuery($Query_p);
                        
                        // }
                            #Para recuperar todas las preguntas.
                            $fl_quiz=array();
                            for($i_m=0;$row_m=RecuperaRegistro($rs_p2);$i_m++) {

                                $fl_quiz[]=array(

                                    "fl_quiz_pregunta"=>"".$row_m['fl_quiz_pregunta'].""
                                    
                                    );


                            }
                       
                          
                         
                          $registros_p = CuentaRegistros($rs_p);
                          for($i_p=0;$row_p=RecuperaRegistro($rs_p);$i_p++) {
                            $fl_quiz_pregunta=$row_p[0];
                            $ds_valor_pregunta = $row_p[2];
                            $no_orden = $row_p[5];
                            
                            if($i_p==0)
                              $active = "class='active'";
                            else
                              $active = "";
                            
                            $ds_mensaje .= "<li data-target='#preg{$no_orden}' class='$no_orden' {$active} id='preg_{$no_orden}'>
                                    <span class='badge badge-info'>{$no_orden}</span>".ObtenEtiqueta(1200)." {$no_orden} ({$ds_valor_pregunta} %)<span class='chevron'></span>
                            </li>";




                            #Recuperamos el max no_intentos de esta ultima pregunta y en base a eso elimnamos los registros basura.
                            $Qui=" SELECT MAX(no_intento) FROM k_quiz_respuesta_usuario WHERE fl_leccion_sp =$fl_leccion_sp AND fl_usuario =$fl_usuario_sp AND fl_quiz_pregunta=$fl_quiz_pregunta ";
                            $romp=RecuperaValor($Qui);
                            $no_itent_maxquiz=$romp[0];



                                    #Para eliminar registros basura que se acomulan por que el estudiante llega a abandonar la quiz y no hay consistencia en los numeros de intentos. 
                                    foreach($fl_quiz as $fl_quix){                               
                                        $fl_quiz_pregut=$fl_quix;
                                        #Recupramos la ultima pregunta del quiz , y en base a eso elimnaos registros basura.
                                        EjecutaQuery("DELETE FROM k_quiz_respuesta_usuario WHERE fl_quiz_pregunta=".$fl_quiz_pregut['fl_quiz_pregunta']." AND fl_leccion_sp=$fl_leccion_sp AND fl_usuario=$fl_usuario_sp AND no_intento > $no_itent_maxquiz ");
                                    }


                          }

                         
                          



                          $cf = $i_p + 1;
                          $ds_mensaje .= "                     
                          <li data-target='#step5' id='step_5'>
                            <span class='badge'>$cf</span><b><u>".ObtenEtiqueta(1265)."</u></b><span class='chevron'></span>
                          </li>
                        </ul>"; 
                        $ds_mensaje .= "<div class='actions'>";
                          $ds_mensaje .= "<!--button type='button' class='btn btn-sm btn-primary btn-prev'>
                            <i class='fa fa-arrow-left'></i>Prev
                          </button-->";
                          $ds_mensaje .= "
                          <script>
                            function TerminaQuiz(tot_preguntas, fl_leccion_sp, fl_usuario_sp,fg_complete_lesson){
                              var parametros = {
                                      'tot_preguntas' : tot_preguntas,
                                      'fl_leccion_sp' : fl_leccion_sp,
                                      'fl_usuario_sp' : fl_usuario_sp,
                                      'fg_complete_lesson' :fg_complete_lesson
                              };
                              
                              muestra_footer = document.getElementById('muestra_footer');
                              $.ajax({
                                data:  parametros,
                                url:   '".PATH_SELF_SITE."/view_quiz_proceso.php',
                                type:  'post',
                                success:  function (response) {
                                        $('#muestra_resultado').html(response);
                                        muestra_footer.style.display='block';
                                }
                              });
                            }
                            
                            // Funcion para mostrar boton view results a partir del primer quiz contestado
                            function muestra_resultados(){
                              // Habilita boton de resultados
                              btn_cunsulta_res_quiz = document.getElementById('btn_cunsulta_res_quiz');
                              btn_cunsulta_res_quiz.style.display='inline-block';
                              
                              // Habilita o deshabilita boton mark as complete
                              $.ajax({
                                data:  'fl_leccion_sp='+$fl_leccion_sp+
                                       '&fl_usuario_sp='+$fl_usuario_sp,
                                url:   '".PATH_SELF_SITE."/valida_calif_quiz.php',
                                type:  'POST',
                                success:  function (response) {
                                        $('#valida_calif_quiz').html(response);
                                        
                                }
                              });
                            }
                          </script>";
                          
                           $ds_mensaje .= "<style>
                              .hvr-shadow:hover{
                                opacity: 0.7;
                                filter: alpha(opacity=70); 
                              }
                              </style>";
                        
                          $ds_mensaje .= "
                          <div style='display:none;' id='btn_oculto'>
                            <!--button type='button' class='btn btn-sm btn-success btn-next' data-last='Finish - Done' onclick='TerminaQuiz($registros_p, $fl_leccion_sp, $fl_usuario_sp,$fg_complete_lesson);'>
                              Next<i class='fa fa-arrow-right'></i>
                            </button-->";
                              // $ds_pregunta .= "<button type='button' class='btn btn-sm btn-primary' data-dismiss='modal' onclick='muestra_resultados();'><i class='fa fa-check-circle'></i>&nbsp;&nbsp;&nbsp;&nbsp;".ObtenEtiqueta(1273)."</button>";
                          $ds_mensaje .= "</div>";
                          $ds_mensaje .= "
                        </div>
                      </div>";                  
                      $ds_mensaje .= "        
                      <div class='step-content'>
                        <form class='form-horizontal' id='fuelux-wizard' method='post'>";
                        
                         
                     /*    if($fg_complete_lesson==1){
                              #Muestra las preguntas por cada quiz especifico por alumno.
                            
                             $Query  = " SELECT fl_quiz_pregunta, ds_pregunta, ds_valor_pregunta, fg_posicion_img, ds_course_pregunta, no_orden, fg_tipo ";
                             $Query .= " FROM k_quiz_pregunta_leccion_sp_usuario  ";
                             $Query .= " WHERE fl_leccion_sp = $fl_leccion_sp AND fl_usuario_sp=$fl_usuario_sp "; 
                             $Query .= " ORDER BY no_orden ASC ";
                             
                             
                         }else{
                       */       #Muestra las pregutas por cda qquiz
                              $Query  = " SELECT fl_quiz_pregunta, ds_pregunta".$sufix.", ds_valor_pregunta, fg_posicion_img, ds_course_pregunta, no_orden, fg_tipo ";
                              $Query .= " FROM k_quiz_pregunta ";
                              $Query .= " WHERE fl_leccion_sp = $fl_leccion_sp"; 
                              $Query .= " ORDER BY no_orden ASC ";
                          
                        // }
                          
                          
                          $rs = EjecutaQuery($Query);
                          $registros = CuentaRegistros($rs);
                          for($i=0;$row=RecuperaRegistro($rs);$i++) {
                            $fl_quiz_pregunta = $row[0];
                            $ds_pregunta = str_texto($row[1]); 
                            $ds_valor_pregunta = $row[2];
                            $fg_posicion_img = str_texto($row[3]); 
                            $ds_course_pregunta = $row[4];
                            $no_orden = $row[5];
                            $no_orden_preg = $no_orden;
                            $fg_tipo = str_texto($row[6]); 
                            
                            if($i==0)
                              $active = "active";
                            else
                              $active = "";
                          
                            if($no_orden_preg == $registros)
                              $siguiente = "#step5";
                            else
                              $siguiente = "#preg".($no_orden_preg + 1);

                            $ds_mensaje .= "<div class='step-pane $active' id='preg{$no_orden}' align='center'>";
                            
                           /* if($fg_complete_lesson==1){
                                 # Query para obtener respuestas de cada pregunta
                                   $Query2  = " SELECT fl_quiz_respuesta, no_orden, ds_respuesta, ds_valor_respuesta FROM k_quiz_respuesta_leccion_sp_usuario WHERE fl_quiz_pregunta = $fl_quiz_pregunta GROUP BY no_orden ORDER BY RAND() ";
                                   $rs2 = EjecutaQuery($Query2);
                            }else{
                            */
                                # Query para obtener respuestas de cada pregunta
                                $Query2  = " SELECT fl_quiz_respuesta, no_orden, ds_respuesta".$sufix.", ds_valor_respuesta FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta GROUP BY no_orden ORDER BY RAND() ";
                                $rs2 = EjecutaQuery($Query2);
                            
                           // }  
                              
                              
                              $registros2 = CuentaRegistros($rs2); 

                              $ds_mensaje .= "
                              <script>
                                function realizaProceso_$i(fl_quiz_pregunta, fl_quiz_respuesta, fl_usuario_sp, fl_leccion_sp, nueva_func){
                                    
                                    // alert(fl_quiz_pregunta + ', ' + fl_quiz_respuesta + ', ' + fl_usuario_sp + ', ' + fl_leccion_sp + ', ' + nueva_func);
                                    var parametros = {
                                      'fl_quiz_pregunta' : fl_quiz_pregunta,
                                      'fl_quiz_respuesta' : fl_quiz_respuesta,
                                      'fl_usuario_sp' : fl_usuario_sp,
                                      'fl_leccion_sp' : fl_leccion_sp
                                    };
                                    $.ajax({
                                      data:  parametros,
                                      url:   '".PATH_SELF_SITE."/view_quiz_proceso.php',
                                      type:  'POST',                                      
                                      success:  function (response) {
                                              $('#resultado_$i').html(response);
                                      }
                                    });
                                    inc = 1;
                                    for(x=1; x<=".$registros_p."; x++){
                                      if(x == 1)
                                        $('#preg_1').removeClass('active');
                                      
                                      inc = ".($no_orden_preg + 1).";
                                      $('#preg_'+inc).addClass('active');
                                      
                                      ant = inc - 1;
                                      $('#preg_'+ant).removeClass('active');
                                    }                                   
                                    if('".$registros_p."' == '".$no_orden_preg."'){
                                      document.getElementById('btn_oculto').style.display = 'block';
                                      $('#step_5').addClass('active');
                                      $('#step5').removeClass('step-pane').addClass('step-pane active');
                                    }      
                                    
                                    // alert($fl_quiz_pregunta + ', ' + $fl_quiz_respuesta + ', ' + $fl_usuario_sp + ', ' + $fl_leccion_sp);
                                    // alert($registros_p + ', ' + $no_orden_preg);
                                    
                                    if(nueva_func == 1){
                                      document.getElementById('muestra_loading').style.display = 'block';
                                      TerminaQuiz($registros_p, $fl_leccion_sp, $fl_usuario_sp,$fg_complete_lesson);
                                    }
                                    
                                }
                              </script>";     
                              // $ds_mensaje .= "<script>alert($fl_quiz_pregunta + ', ' + $fl_quiz_respuesta + ', ' + $fl_usuario_sp + ', ' + $fl_leccion_sp);</script>";   
                              // $ds_mensaje .= "<script>alert($registros_p + ', ' + $no_orden_preg);</script>";   
                              $script_fin_2 = 0;
                              if($registros_p == $no_orden_preg){
                                $script_fin = "";
                                // $script_fin = "TerminaQuiz($registros_p, $fl_leccion_sp, $fl_usuario_sp);";
                                $script_fin_2 = 1; 
                              }
                              
                              $ds_mensaje .=" 
                              <div id='resultado_$i'>"; 
                                $ds_mensaje .= "<center><h3><strong>{$ds_pregunta} </strong></h3></center>
                                <div class='form-group' align='center'>";                                  
                                  $cont = 0;
                                  for($i2=0;$row2=RecuperaRegistro($rs2);$i2++) {
                                    $fl_quiz_respuesta = $row2[0];
                                    $no_orden = $row2[1];
                                    $ds_respuesta = str_texto($row2[2]); 
                                    $ds_valor_respuesta = $row2[3];   
                                    $cont = $cont + 1;

                                    // Pregunta tipo Texto
                                    if($fg_tipo == 'T'){
                                      $ds_mensaje .= "<div class='row'>";
                                        $ds_mensaje .= "<div class='col-lg-2'></div>";
                                        $ds_mensaje .= "<div class='col-lg-8 text-center'>";
                                          $ds_mensaje .= "<input style='font-size: 14px!important;' type='button' class='btn btn-primary btn-sm btn-block' data-toggle='tab' href='{$siguiente}' onclick='realizaProceso_$i($fl_quiz_pregunta,$fl_quiz_respuesta,$fl_usuario_sp,$fl_leccion_sp, $script_fin_2); {$script_fin}' value='{$ds_respuesta}' />";
                                        $ds_mensaje .= "</div>";
                                        $ds_mensaje .= "<div class='col-lg-2'></div>";
                                      $ds_mensaje .= "</div>";
                                      $ds_mensaje .= "<p></p>";
                                    }else{ // Pregunta tipo Imagen
                                      if($fg_posicion_img == 'L'){ // Posicion tipo Landscape
                                        // if($cont == 3)
                                          // $cont = 1;
                                        if($cont==1){
                                          $ds_mensaje .= "<div class='row'>";
                                          // $ds_mensaje .= "<div class='col-lg-1'></div>";
                                        $ds_mensaje .= "<div class='col-lg-1'></div><div class='col-lg-10' style='letter-spacing: -5px;'>";
                                        }
                                        // $ds_mensaje .= "<p></p>";
                                        $ds_mensaje .= "<a data-toggle='tab' href='{$siguiente}' onclick='realizaProceso_$i($fl_quiz_pregunta,$fl_quiz_respuesta,$fl_usuario_sp,$fl_leccion_sp, $script_fin_2); {$script_fin}'>";
                                        $ds_mensaje .= "<img src='../AD3M2SRC4/modules/fame/uploads/$ds_respuesta' width='330' height='180' style='margin: 5px; width: 100%; max-width: 330px; height: 100%; max-height: 180px; border: 1px; border-style: solid;' class='hvr-shadow'>  ";
                                        $ds_mensaje .= "</a>";
                                        // $ds_mensaje .= "</div>";
                                        // if($cont==1)
                                           // $ds_mensaje .= "<div class='col-lg-2'></div>";
                                        
                                        // if($cont == 2){
                                          // $ds_mensaje .= "<div class='col-lg-1'></div>";
                                          // $ds_mensaje .= "</div>";
                                        // }

                                        if($i2 == ($registros2 - 1))
                                          $ds_mensaje .= "</div></div>";
                                          // $ds_mensaje .= "<div class='col-lg-1'></div></div></div>";
                                      }else{ // Posicion tipo Portrait
                                        if($cont==1){
                                          $ds_mensaje .= "<div class='row'>";
                                          // $ds_mensaje .= "<div class='col-lg-1'></div>";
                                        $ds_mensaje .= "<div class='col-lg-12' style='letter-spacing: -5px;'>";
                                        }
                                        // $ds_mensaje .= "<div class='col-lg-2'>";
                                        // $ds_mensaje .= "<p></p>";
                                        $ds_mensaje .= "<a data-toggle='tab' href='{$siguiente}' onclick='realizaProceso_$i($fl_quiz_pregunta,$fl_quiz_respuesta,$fl_usuario_sp,$fl_leccion_sp, $script_fin_2); {$script_fin}'>";
                                          $ds_mensaje .= "<img src='../AD3M2SRC4/modules/fame/uploads/$ds_respuesta' 
                                            style=' width: 100%; max-width: 180px; height: 100%; max-height: 330px;' class='hvr-shadow'>  ";
                                        $ds_mensaje .= "</a>";
                                        // $ds_mensaje .= "</div>";
                                        if($i2 == ($registros2 - 1))
                                          $ds_mensaje .= "</div></div>";
                                      }                                  
                                    }
                                  }
                                $ds_mensaje .= "</div>";
                              $ds_mensaje .= "</div>";                                
                            $ds_mensaje .= "</div>";




                          }  
                          $ds_mensaje .= "
                            <div class='step-pane' id='step5' align='center'>
                            
                                                               
                              <br/>
                              <div id='muestra_loading' style='display: block;'>
                                <br><br><br><br><br>
                                <center>
                                  <span id='gabriel' class='ui-widget  txt-color-black'>
                                    <i class='fa fa-cog fa-4x  fa-spin txt-color-black'></i><h2><strong></strong></h2>
                                  </span>
                                </center>
                              </div>
                            
                            
                            
                            
                              <div id='muestra_resultado'></div>
                            </div>";                             
                          $ds_mensaje .= "
                          </div>
                        </form>";
                      $ds_mensaje .= "
                      </div>
                    </div>
                  </div>
                </article>
              </div>
            </div>
            <div class='modal-footer' id='muestra_footer' style='display:none;'>
              <center>";
                // $ds_mensaje .= "
                // <div id='btn_results' style='display:block;'>
                  // <a href='#iss2' data-toggle='tab' onclick='cambia_tab(1, 0);' class='btn btn-sm btn-primary'>
                    // <div id='tit_btn'><i class='fa fa-list-ul'></i>&nbsp;&nbsp;&nbsp;&nbsp;".ObtenEtiqueta(1268)."</div>
                  // </a>
                // </div>
                // <div id='btn_score' style='display:none;'>
                  // <a href='#iss1' data-toggle='tab' onclick='cambia_tab(2, 0);' class='btn btn-sm btn-primary'>
                    // <div id='tit_btn'><i class='fa fa-list-ul'></i>&nbsp;&nbsp;&nbsp;&nbsp;".ObtenEtiqueta(1267)."</div>
                  // </a>
                // </div>";
                
                $ds_mensaje .= "<button type='button' class='btn btn-sm btn-primary' data-dismiss='modal' onclick='muestra_resultados();'><i class='fa fa-check-circle'></i>&nbsp;&nbsp;&nbsp;&nbsp;".ObtenEtiqueta(1356)."</button>";
              $ds_mensaje .= "</center>
            </div>
          </div>
            ";
            
            
            echo $ds_mensaje;
?>