<?php 

  if(!empty($_REQUEST['fl_quiz_pregunta'])){

    # Libreria de funciones
    require("../lib/self_general.php");

    $fl_quiz_pregunta = RecibeParametroNumerico('fl_quiz_pregunta');
    $fl_quiz_respuesta = RecibeParametroNumerico('fl_quiz_respuesta');
    $fl_usuario_sp = RecibeParametroNumerico('fl_usuario_sp');
    $fl_leccion_sp = RecibeParametroNumerico('fl_leccion_sp');
    
    $rs = EjecutaQuery("SELECT * FROM k_quiz_respuesta_usuario WHERE fl_leccion_sp = $fl_leccion_sp AND fl_quiz_pregunta =  $fl_quiz_pregunta AND fl_usuario = $fl_usuario_sp GROUP BY no_intento");
    $no_intento = CuentaRegistros($rs) + 1;  
    
    EjecutaQuery("INSERT INTO k_quiz_respuesta_usuario (fl_quiz_pregunta, fl_leccion_sp, fl_quiz_respuesta, fl_usuario, no_intento) VALUES ($fl_quiz_pregunta, $fl_leccion_sp, $fl_quiz_respuesta, $fl_usuario_sp, $no_intento) ");
    
    echo "SELECT * FROM k_quiz_respuesta_usuario WHERE fl_leccion_sp = $fl_leccion_sp AND fl_quiz_pregunta =  $fl_quiz_pregunta AND fl_usuario = $fl_usuario_sp GROUP BY no_intento<br>";
    echo "INSERT INTO k_quiz_respuesta_usuario (fl_quiz_pregunta, fl_leccion_sp, fl_quiz_respuesta, fl_usuario, no_intento) VALUES ($fl_quiz_pregunta, $fl_leccion_sp, $fl_quiz_respuesta, $fl_usuario_sp, $no_intento) <br>";
    
  }else{
    
    sleep(1);
    
    # Libreria de funciones
    require("../lib/self_general.php");
    
    $tot_preguntas = RecibeParametroNumerico('tot_preguntas');
    $fl_leccion_sp = RecibeParametroNumerico('fl_leccion_sp');
    $fl_usuario_sp = RecibeParametroNumerico('fl_usuario_sp');
    $fe_actual = date('Y-m-d');
    
    # Obtenemos el ultimo intento
    // $row_int = RecuperaValor("SELECT no_intento FROM k_quiz_respuesta_usuario WHERE fl_leccion_sp = $fl_leccion_sp AND fl_usuario = $fl_usuario_sp GROUP BY no_intento ORDER BY no_intento DESC LIMIT 1 ");
    // $no_intento = $row_int[0];
    $Query = "SELECT no_intento FROM k_quiz_respuesta_usuario WHERE fl_leccion_sp = $fl_leccion_sp AND fl_usuario = $fl_usuario_sp GROUP BY no_intento ORDER BY no_intento DESC LIMIT 1";
    $rs = EjecutaQuery($Query);
    $row_int=RecuperaRegistro($rs);
    $no_intento = $row_int[0];

    $row_res = RecuperaValor(" SELECT SUM(((preg * resp)/100)) AS resultado FROM (
                                SELECT c.ds_valor_pregunta AS preg, b.ds_valor_respuesta AS resp
                                FROM k_quiz_respuesta_usuario a, k_quiz_respuesta b, k_quiz_pregunta c
                                WHERE a.fl_leccion_sp = $fl_leccion_sp AND a.fl_usuario = $fl_usuario_sp AND a.no_intento = $no_intento
                                AND a.fl_quiz_respuesta = b.fl_quiz_respuesta
                                AND a.fl_quiz_pregunta = c.fl_quiz_pregunta) AS principal
                              WHERE 1=1");
    $cal_final = $row_res[0];
    // echo "<br>SELECT SUM(((preg * resp)/100)) AS resultado FROM (
                                // SELECT c.ds_valor_pregunta AS preg, b.ds_valor_respuesta AS resp
                                // FROM k_quiz_respuesta_usuario a, k_quiz_respuesta b, k_quiz_pregunta c
                                // WHERE a.fl_leccion_sp = $fl_leccion_sp AND a.fl_usuario = $fl_usuario_sp AND a.no_intento = $no_intento
                                // AND a.fl_quiz_respuesta = b.fl_quiz_respuesta
                                // AND a.fl_quiz_pregunta = c.fl_quiz_pregunta) AS principal
                              // WHERE 1=1<br>";

    $row_count = RecuperaValor("SELECT COUNT(1) FROM k_quiz_calif_final WHERE fl_leccion_sp = $fl_leccion_sp AND fl_usuario = $fl_usuario_sp AND no_intento = $no_intento  ");
    $tot_reg = $row_count[0];
    
    $row_cal = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion_sp WHERE no_min <= ROUND($cal_final) AND no_max >= ROUND($cal_final)");
    $cl_calificacion = $row_cal[0];
    $fg_aprobado = $row_cal[1];
    
    $row_cal_min = RecuperaValor("SELECT cl_calificacion, no_min FROM c_calificacion_sp WHERE fg_aprobado = '1' ORDER BY no_min ASC LIMIT 1");
    $cl_calificacion_min = $row_cal_min[0];
    $no_cal_min = $row_cal_min[1];
    
    if(empty($tot_reg))
      EjecutaQuery("INSERT INTO k_quiz_calif_final (fl_leccion_sp, fl_usuario, no_intento, no_calificacion, cl_calificacion, fe_final) VALUES ($fl_leccion_sp, $fl_usuario_sp, $no_intento, $cal_final, '$cl_calificacion', '$fe_actual')");
    
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
    
    ?>
    <script>
      function cambia_tab(val, val2){
        btn_results = document.getElementById("btn_results");
        btn_score = document.getElementById("btn_score");
        if(val == 1){
          $('#tab_1').removeClass('active');
          $('#tab_2').addClass('active');
          btn_score.style.display='block';
          btn_results.style.display='none';
          if(val2){
            $('#iss1').removeClass('tab-pane active');
            $('#iss1').addClass('tab-pane fade');
            $('#iss2').removeClass('tab-pane fade');
            $('#iss2').addClass('tab-pane active');
          }
        }else{
          if(val2){
            $('#iss2').removeClass('tab-pane active');
            $('#iss2').addClass('tab-pane fade');
            $('#iss1').removeClass('tab-pane fade');
            $('#iss1').addClass('tab-pane active');
          }
          $('#tab_2').removeClass('active');
          $('#tab_1').addClass('active');   
          btn_score.style.display='none';
          btn_results.style.display='block';
        }
      }
    </script>
    <br>
    <div class="tab-content">
      <div class="tab-pane active" id="hr2">
        <ul class="nav nav-tabs">
          <li class="active" id="tab_1">
            <a href="#iss1" data-toggle="tab" onclick='cambia_tab(2, 1);'><?php echo ObtenEtiqueta(1267); ?></a>
          </li>
          <li id="tab_2">
            <a href="#iss2" data-toggle="tab" onclick='cambia_tab(1, 1);'><?php echo ObtenEtiqueta(1268); ?></a>
          </li>      
        </ul>
        <div class="tab-content padding-10">
          <div class="tab-pane active" id="iss1">
          <!--------------------------------------------------------------------------------------------------------------------->
            <h3 style="margin: 0px 0px;"><strong><?php echo ObtenEtiqueta(1269); ?></strong></h3>
            <br>
            <img src="<?php echo $img; ?>" width="150px">
            <?php echo $msj; ?>
            <div class="row">
              <div class="col-lg-3"></div>
              <div class="col-sm-12 col-md-12 col-lg-3">
                <h1><strong>
                  <span class="font-sm"><?php echo "$cl_calificacion ($cal_final %)"; ?></span><br>
                  <span class="font-xs"><?php echo ObtenEtiqueta(1267); ?></span>
                </strong></h1>
              </div>
              <div class="col-sm-12 col-md-12 col-lg-3" style="color:gray;">
                <h1>
                    <span class="font-sm"><?php echo "$cl_calificacion_min ($no_cal_min %)"; ?></span><br>
                    <span class="font-xs"><?php echo ObtenEtiqueta(1280); ?></span>
                </h1>
              </div>
              <div class="col-lg-3"></div>
            </div>
            <!--h1 style="margin: 6px 0;"><small><strong><a href="#iss2" data-toggle="tab" onclick="cambia_tab();"><?php # echo ObtenEtiqueta(1268); ?></a></strong></small></h1-->
          <!--------------------------------------------------------------------------------------------------------------------->
          </div>
          <div class="tab-pane fade" id="iss2">
          <!--------------------------------------------------------------------------------------------------------------------->
            <div class="panel-body no-padding">
              <table class="table table-bordered table-condensed">
                <thead>
                  <tr>
                    <th width="5%"><center><?php echo ObtenEtiqueta(1293) ?></center></th>
                    <th><?php echo ObtenEtiqueta(1200) ?></th>
                    <th width="15%"><center><?php echo ObtenEtiqueta(1610) ?></center></th>
                    <th width="15%"><center><?php echo ObtenEtiqueta(1611) ?></center></th>
                    <th width="15%"><center><?php echo ObtenEtiqueta(1567) ?></center></th>
                  </tr>
                </thead>
                <tbody>
            <?php 
             
              $Queryf  = "SELECT ds_pregunta".$sufix.", ((preg * resp)/100) AS pond_resp, resp, preg FROM (
              SELECT  p.ds_pregunta".$sufix.", r.ds_valor_respuesta resp, p.ds_valor_pregunta preg
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
                  $img = "img/bien.png";
                if($cl_calificacion == 'B+' OR $cl_calificacion == 'B' OR $cl_calificacion == 'B-')
                  $img = "img/reg.png";
                if($cl_calificacion == 'C+' OR $cl_calificacion == 'C' OR $cl_calificacion == 'C-' OR $cl_calificacion == 'F' OR $cl_calificacion == 'NU')
                  $img = "img/mal.png";
                  
                  echo " <tr>
                    <td><center>$np</center></td>
                    <td>$ds_pregunta</td>
                    <td><center>$cl_calificacion</center></td>
                    <td><center>{$ds_valor_respuesta}% of {$ds_valor_preg_org}%</center></td>
                    <td><center><img src=$img width='25px'></center></td>
                  </tr>";
                  $np++;
              }            
            ?> 
                </tbody>
              </table>
            </div>          
          <!--------------------------------------------------------------------------------------------------------------------->
          </div>
        </div>

      </div>
    </div> 
    
    <script>
      $(document).ready(function(){
        document.getElementById('muestra_loading').style.display = 'none';
      });
    </script>
    
    
  <?php
  }
  ?>