<?php
	# Libreria de funciones
	require("../lib/self_general.php");
  
  $fl_usuario_sp = RecibeParametroNumerico('fl_usuario_sp');
  $fl_leccion_sp = RecibeParametroNumerico('fl_leccion_sp');
  
  echo "<script>
    function cambia_tab_2(val, val2){
      // btn_results = document.getElementById('btn_results2');
      // btn_score = document.getElementById('btn_score2');
      if(val == 1){
        $('#tab_12').removeClass('active');
        $('#tab_22').addClass('active');
        // btn_score.style.display='block';
        // btn_results.style.display='none';
        if(val2){
          $('#iss12').removeClass('tab-pane active');
          $('#iss12').addClass('tab-pane fade');
          $('#iss22').removeClass('tab-pane fade');
          $('#iss22').addClass('tab-pane active');
        }
      }else{
        if(val2){
          $('#iss22').removeClass('tab-pane active');
          $('#iss22').addClass('tab-pane fade');
          $('#iss12').removeClass('tab-pane fade');
          $('#iss12').addClass('tab-pane active');
        }
        $('#tab_22').removeClass('active');
        $('#tab_12').addClass('active');   
        // btn_score.style.display='none';
        // btn_results.style.display='block';
      }
    }
    </script>";

    $ds_mensaje = "
        <div class='modal-content'>
          <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>
              &times;
            </button>
            <h4 class='modal-title' id='myModalLabel'><i class='fa fa-warning'></i>&nbsp;&nbsp;<strong>".ObtenEtiqueta(1323)."</strong> </h4>
          </div>
          <div class='modal-body' style='padding-bottom:0px;'><div class='row'>
      <article class='col-sm-12 col-md-12 col-lg-12'>
        <div class='jarviswidget' id='wid-id-2' data-widget-editbutton='false' data-widget-deletebutton='false' style='margin: 0 0 15px;'>
          <div>
            <div class='jarviswidget-editbox'><!-- This area used as dropdown edit box --></div>
              <div class='widget-body fuelux'>
                <div class='actions'>
                  <div class='tab-content'>
                    <div class='tab-pane active' id='hr22'>
                      <ul class='nav nav-tabs'>
                        <li class='active' id='tab_12'>
                          <a href='#iss12' data-toggle='tab' onclick='cambia_tab_2(2, 1);'>".ObtenEtiqueta(1267)."</a>
                        </li>
                        <li id='tab_22'>
                          <a href='#iss22' data-toggle='tab' onclick='cambia_tab_2(1, 1);'>".ObtenEtiqueta(1268)."</a>
                        </li>      
                      </ul>
                      <center>
                        <div class='tab-content padding-10'>
                          <div class='tab-pane active' id='iss12'>
                            <h3 style='margin: 0px 0px;'><strong>".ObtenEtiqueta(1269)."</strong></h3>";   


                            #Recuperamos
                            $Qyi=" SELECT MAX(fl_quiz_pregunta)
                                                          FROM k_quiz_respuesta_usuario 
                                                          WHERE fl_leccion_sp =$fl_leccion_sp AND fl_usuario = $fl_usuario_sp ";
                            $ropm=RecuperaValor($Qyi);
                            $fl_quiz_pregunta_max=$ropm[0];

                            #Recuperamos el max no_intentos de esta ultima pregunta y en base a eso elimnamos los registros basura.
                            $Qui=" SELECT MAX(no_intento) FROM k_quiz_respuesta_usuario WHERE fl_leccion_sp =$fl_leccion_sp AND fl_usuario =$fl_usuario_sp AND fl_quiz_pregunta=$fl_quiz_pregunta_max ";
                            $romp=RecuperaValor($Qui);
                            $no_itent_maxquiz=$romp[0];

                            #Elimamos registros basura si existen.
                            # Genera los tabs de preguntas
                            $Query_p  = " SELECT fl_quiz_pregunta ";
                            $Query_p .= " FROM k_quiz_pregunta ";
                            $Query_p .= " WHERE fl_leccion_sp = $fl_leccion_sp"; 
                            $Query_p .= " ORDER BY no_orden ASC ";
                            $rs_p = EjecutaQuery($Query_p);
                            $registros_p = CuentaRegistros($rs_p);
                            for($i_p=0;$row_p=RecuperaRegistro($rs_p);$i_p++) {
                                $fl_quiz_pregunta=$row_p[0];

                                #Recupramos la ultima pregunta del quiz , y en base a eso elimnaos registros basura.
                                EjecutaQuery("DELETE FROM k_quiz_respuesta_usuario WHERE fl_quiz_pregunta=$fl_quiz_pregunta AND fl_leccion_sp=$fl_leccion_sp AND fl_usuario=$fl_usuario_sp AND no_intento > $no_itent_maxquiz ");


                            }






                            # Obtenemos el ultimo intento
                            $row_int = RecuperaValor("SELECT no_intento FROM k_quiz_respuesta_usuario WHERE fl_leccion_sp = $fl_leccion_sp AND fl_usuario = $fl_usuario_sp GROUP BY no_intento ORDER BY no_intento DESC LIMIT 1 ");
                            $no_intento = $row_int[0];
                            
                            $row_res = RecuperaValor(" SELECT SUM(((preg * resp)/100)) AS resultado FROM (SELECT c.ds_valor_pregunta AS preg, b.ds_valor_respuesta AS resp FROM k_quiz_respuesta_usuario a, k_quiz_respuesta b, k_quiz_pregunta c WHERE a.fl_leccion_sp = $fl_leccion_sp AND a.fl_usuario = $fl_usuario_sp AND a.no_intento = $no_intento AND a.fl_quiz_respuesta = b.fl_quiz_respuesta AND a.fl_quiz_pregunta = c.fl_quiz_pregunta) AS principal WHERE 1=1");
                            $cal_final = $row_res[0];

                            $row_count = RecuperaValor("SELECT COUNT(1) FROM k_quiz_calif_final WHERE fl_leccion_sp = $fl_leccion_sp AND fl_usuario = $fl_usuario_sp AND no_intento = $no_intento  ");
                            $tot_reg = $row_count[0];

                            $row_cal = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion_sp WHERE no_min <= ROUND($cal_final) AND no_max >= ROUND($cal_final)");
                            $cl_calificacion = $row_cal[0];
                            $fg_aprobado = $row_cal[1];
                            
                            $row_cal_min = RecuperaValor("SELECT cl_calificacion, no_min FROM c_calificacion_sp WHERE fg_aprobado = '1' ORDER BY no_min ASC LIMIT 1");
                            $cl_calificacion_min = $row_cal_min[0];
                            $no_cal_min = $row_cal_min[1];

                            if($cl_calificacion == 'A+' OR $cl_calificacion == 'A' OR $cl_calificacion == 'A-')
                              $img = "img/bien.png";
                            if($cl_calificacion == 'B+' OR $cl_calificacion == 'B' OR $cl_calificacion == 'B-')
                              $img = "img/reg.png";
                            if($cl_calificacion == 'C+' OR $cl_calificacion == 'C' OR $cl_calificacion == 'C-' OR $cl_calificacion == 'F' OR $cl_calificacion == 'NU')
                              $img = "img/mal.png";    
                                      
                            if($fg_aprobado == 1){
                              $msj = "<h1 class='text-center text-success'><i class='fa fa-check'></i> <strong>".ObtenEtiqueta(1270)."</strong></h1>";
                            }else{
                              $msj = "<h1 class='text-danger'><i class='fa-fw fa fa-times'></i> <strong>".ObtenEtiqueta(1271)."</strong></h1>";
                            }
                                      
                            $ds_mensaje .= "<br>
                            <img src='$img' width='150px'>
                            $msj
                            <div class='row'>
                              <div class='col-lg-3'></div>
                              <div class='col-sm-12 col-md-12 col-lg-3'>
                                <h1><strong>
                                  <span class='font-sm'>$cl_calificacion ($cal_final %)</span><br>
                                  <span class='font-xs'>".ObtenEtiqueta(1267)."</span>
                                </strong></h1>
                              </div>
                              <div class='col-sm-12 col-md-12 col-lg-3' style='color:gray;'>
                                <h1>
                                  <span class='font-sm'>$cl_calificacion_min ($no_cal_min %)</span><br>
                                  <span class='font-xs'>".ObtenEtiqueta(1280)."</span>
                                </h1>
                              </div>
                              <div class='col-lg-3'></div>
                            </div>
                          </div>
                          <div class='tab-pane fade' id='iss22'>
                            <div class='panel-body no-padding'>
							  <div class='table-responsive'>
							
                              <table class='table table-bordered table-condensed'>
                                <thead>
                                  <tr>
                                    <th width='5%'><center>".ObtenEtiqueta(1293)."</center></th>
                                    <th>".ObtenEtiqueta(1200)."</th>
                                    <th width='15%'><center>".ObtenEtiqueta(1610)."</center></th>
                                    <th width='15%'><center>".ObtenEtiqueta(1611)."</center></th>
                                    <th width='15%'><center>".ObtenEtiqueta(1567)."</center></th>
                                  </tr>
                                </thead>
                                <tbody>";
            
                                $row_int = RecuperaValor("SELECT no_intento FROM k_quiz_respuesta_usuario WHERE fl_leccion_sp = $fl_leccion_sp AND fl_usuario = $fl_usuario_sp GROUP BY no_intento ORDER BY no_intento DESC LIMIT 1 ");
                                $no_intento = $row_int[0];

                                $Queryf = "SELECT ds_pregunta, ((preg * resp)/100) AS pond_resp, resp, preg FROM (
                                SELECT  p.ds_pregunta, r.ds_valor_respuesta resp, p.ds_valor_pregunta preg
                                FROM k_quiz_respuesta_usuario ru, k_quiz_pregunta p, k_quiz_respuesta r 
                                WHERE ru.fl_leccion_sp = $fl_leccion_sp AND ru.fl_usuario = $fl_usuario_sp AND ru.no_intento = $no_intento 
                                AND ru.fl_quiz_pregunta = p.fl_quiz_pregunta AND ru.fl_quiz_respuesta = r.fl_quiz_respuesta 
                                ORDER BY p.no_orden ASC ) AS principal WHERE 1=1";
                                $rsf = EjecutaQuery($Queryf);
                                $registrosf = CuentaRegistros($rsf);
                                $np = 1;
                                for($if=0;$rowf=RecuperaRegistro($rsf);$if++) {
                                  $ds_pregunta = str_texto($rowf[0]);
                                  $ds_valor_respuesta = ($rowf[1]);
                                  $ds_valor_resp_org = ($rowf[2]);
                                  $ds_valor_preg_org = ($rowf[3]);

                                  $row_cal = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion_sp WHERE no_min <= ROUND($ds_valor_resp_org) AND no_max >= ROUND($ds_valor_resp_org)");
                                  $cl_calificacion = $row_cal[0];
                                  $fg_aprobado = $row_cal[1];  
                        
                                  if($cl_calificacion == 'A+' OR $cl_calificacion == 'A' OR $cl_calificacion == 'A-')
                                    $img = 'img/bien.png';
                                  if($cl_calificacion == 'B+' OR $cl_calificacion == 'B' OR $cl_calificacion == 'B-')
                                    $img = 'img/reg.png';
                                  if($cl_calificacion == 'C+' OR $cl_calificacion == 'C' OR $cl_calificacion == 'C-' OR $cl_calificacion == 'F' OR $cl_calificacion == 'NU')
                                    $img = 'img/mal.png';
                                    
                                    $ds_mensaje .=" <tr>
                                      <td><center>$np</center></td>
                                      <td>$ds_pregunta</td>
                                      <td><center>$cl_calificacion</center></td>
                                      <td><center>{$ds_valor_respuesta}% of {$ds_valor_preg_org}%</center></td>
                                      <td><center><img src=$img width='25px'></center></td>
                                    </tr>";
                                    $np++;
                                }  $ds_mensaje .=" 
                                  </tbody>
                                </table>
								
								</div><!---end table responsive--->
								
                              </div>          
                            </div>
                          </div>

                        </div>
                      </div> 
                    </div>
                  </div>
                </div>
              </div>
            </article>
          </div></div>
            <div class='modal-footer' id='muestra_footer2' style='display:block;'>
              <center>";
              // $ds_mensaje .= "
                // <div id='btn_results2' style='display:block;'>
                  // <a href='#iss22' data-toggle='tab' onclick='cambia_tab_2(1, 0);' class='btn btn-sm btn-primary'>
                    // <div id='tit_btn2'><i class='fa fa-list-ul'></i>&nbsp;&nbsp;&nbsp;&nbsp;".ObtenEtiqueta(1268)."</div>
                  // </a>
                // </div>
                // <div id='btn_score2' style='display:none;'>
                  // <a href='#iss12' data-toggle='tab' onclick='cambia_tab_2(2, 0);' class='btn btn-sm btn-primary'>
                    // <div id='tit_btn2'><i class='fa fa-list-ul'></i>&nbsp;&nbsp;&nbsp;&nbsp;".ObtenEtiqueta(1267)."</div>
                  // </a>
                // </div>";
                
                $ds_mensaje .= "<button type='button' class='btn btn-sm btn-primary' data-dismiss='modal' onclick='muestra_resultados();'><i class='fa fa-check-circle'></i>&nbsp;&nbsp;&nbsp;&nbsp;".ObtenEtiqueta(1356)."</button>";
              $ds_mensaje .= "
              </center>
            </div>
          </div>";
          
          echo $ds_mensaje;
