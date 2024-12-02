<div class="tab-pane fade in " id="assignment">
  <!-- START WIDGET BODY -->
  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-xs-6 col-sm-4">
    <?php
      Forma_CampoTexto(ObtenEtiqueta(1252), False, 'ds_tiempo_tarea', $ds_tiempo_tarea, 25, 25);
    ?>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-xs-6 col-sm-4">
    <?php
      $descripcion3 = 0;
      if(!empty($ds_no_sketch))
        $descripcion3 = 1;
      Forma_CampoTexto(ObtenEtiqueta(394), False, 'no_sketch', $no_sketch, 3, 5, $no_sketch_err,'','',True,'onkeyup="javascript:MuestraDescSketchNum('.$descripcion3.')"');
    ?>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-xs-6 col-sm-4">
      <?php
      $descripcion1 = 0;
      if(!empty($ds_animacion))
        $descripcion1 = 1;
      Forma_CampoCheckbox(ObtenEtiqueta(393), 'fg_animacion', $fg_animacion,'','',True,'onchange="javascript:MuestraDescAssig('.$descripcion1.')"'); ?>
    </div>
  </div>
  <?php
    if($fg_animacion == 1){
      $style_a = "style='display:block;";
      if(!empty($ds_animacion_err))
        $style_a .= "color:red;background-color:#fff0f0;'";
      $style_a .= "'";
    }
    else
      $style_a = "style='display:none;'";
  ?>                  
  <div class="row">
    <div class="col-sm-3"></div>        
    <div class="col-xs-6 col-sm-4">
    <?php
      $descripcion2 = 0;
      if(!empty($ds_ref_animacion))
        $descripcion2 = 1;
      Forma_CampoCheckbox(ObtenEtiqueta(398), 'fg_ref_animacion', $fg_ref_animacion,'','',True,'onchange="javascript:MuestraDescAssigRef('.$descripcion2.')"');
    ?>
    </div>
  </div>
  <?php        
    if($fg_ref_animacion == 1){
      $style_r = "style='display:block;";
      if(!empty($ds_ref_animacion_err))
        $style_r .= "color:red;background-color:#fff0f0;";
      $style_r .= "'";
    }
    else
      $style_r = "style='display:none;'";
  ?>                  
  <?php
    if($no_sketch >= 1){
      $style_n = "style='display:block;";
      if(!empty($ds_no_sketch_err))
        $style_n .= "color:red;background-color:#fff0f0;";
      $style_n .= "'";
    }
    else
      $style_n = "style='display:none;'";
  ?>               
  <div class="row">
    <div class="col-sm-3"></div>        
    <div class="col-xs-6 col-sm-4">
      <?php
      $descripcion4 = 0;
      if(!empty($ds_ref_sketch))
        $descripcion4 = 1;
      Forma_CampoCheckbox(ObtenEtiqueta(399), 'fg_ref_sketch', $fg_ref_sketch,'','',True,'onchange="javascript:MuestraDescSketch('.$descripcion4.')"'); ?>
    </div>
  </div> 
  <?php
    if($fg_ref_sketch == 1){
      $style_s = "style='display:block;";
      if(!empty($ds_ref_sketch_err))
        $style_s .= "color:red;background-color:#fff0f0;";
      $style_s .= "'";
    }
    else
      $style_s = "style='display:none;'";
  ?>          
  <br><br>
  <div class="widget-body">
  <ul id="myTabAssignment" class="nav nav-tabs bordered">
    <li class="active">
      <a id="mytabAssign1" href="#assignment_eng" data-toggle="tab">
        English
      </a>
    </li>
    <li class="">
      <a id="mytabAssign2" href="#assignment_esp" data-toggle="tab">
        Spanish
      </a>
    </li>
    <li class="">
      <a id="mytabAssign3" href="#assignment_fra" data-toggle="tab">
        French
      </a>
    </li>
  </ul>
    <div id="myTabAssignCont" class="tab-content padding-10 no-border">
      <div class="tab-pane fade in active" id="assignment_eng">
        <!-- START English Content-->
        <div id='content_p' <?php echo $style_a; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(393), False, 'ds_animacion', ($ds_animacion??NULL), 50, 20, ($ds_animacion_err??NULL));
              ?>
            </div>
          </div>
        </div>
        <div id='content_2' <?php echo $style_r; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(1290), False, 'ds_ref_animacion', ($ds_ref_animacion??NULL), 50, 20, ($ds_ref_animacion_err??NULL)); ?>
            </div>
          </div>
        </div>
        <div id='content_3' <?php echo $style_s; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(1292), False, 'ds_ref_sketch', ($ds_ref_sketch??NULL), 50, 20, ($ds_ref_sketch_err??NULL)); ?>
            </div>
          </div>
        </div>
        <div id='content_4' <?php echo $style_n; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(1291), False, 'ds_no_sketch', ($ds_no_sketch??NULL), 50, 20, ($ds_no_sketch_err??NULL));?>
            </div>
          </div>
        </div>
        <!-- END English Content-->
      </div>
      <div class="tab-pane fade in " id="assignment_esp">
        <!-- START Spanish Content-->
        <div id='content_p_esp' <?php echo $style_a; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(393), False, 'ds_animacion_esp', ($ds_animacion_esp??NULL), 50, 20, ($ds_animacion_err??NULL));
              ?>
            </div>
          </div>
        </div>
        <div id='content_2_esp' <?php echo $style_r; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(1290), False, 'ds_ref_animacion_esp', ($ds_ref_animacion_esp??NULL), 50, 20, ($ds_ref_animacion_err??NULL)); ?>
            </div>
          </div>
        </div>
        <div id='content_3_esp' <?php echo $style_s; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(1292), False, 'ds_ref_sketch_esp', ($ds_ref_sketch_esp??NULL), 50, 20, ($ds_ref_sketch_err??NULL)); ?>
            </div>
          </div>
        </div>
        <div id='content_4_esp' <?php echo $style_n; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(1291), False, 'ds_no_sketch_esp', ($ds_no_sketch_esp??NULL), 50, 20, ($ds_no_sketch_err??NULL));?>
            </div>
          </div>
        </div>
        <!-- END Spanish Content-->
      </div>
      <div class="tab-pane fade in " id="assignment_fra">
        <!-- START French Content-->
      <div id='content_p_fra' <?php echo $style_a; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(393), False, 'ds_animacion_fra', ($ds_animacion_fra??NULL), 50, 20, ($ds_animacion_err??NULL));
              ?>
            </div>
          </div>
        </div>
        <div id='content_2_fra' <?php echo $style_r; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(1290), False, 'ds_ref_animacion_fra', ($ds_ref_animacion_fra??NULL), 50, 20, ($ds_ref_animacion_err??NULL)); ?>
            </div>
          </div>
        </div>
        <div id='content_3_fra' <?php echo $style_s; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(1292), False, 'ds_ref_sketch_fra', ($ds_ref_sketch_fra??NULL), 50, 20, ($ds_ref_sketch_err??NULL)); ?>
            </div>
          </div>
        </div>
        <div id='content_4_fra' <?php echo $style_n; ?>>
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <?php
                Forma_CampoTinyMCE(ObtenEtiqueta(1291), False, 'ds_no_sketch_fra', ($ds_no_sketch_fra??NULL), 50, 20, ($ds_no_sketch_err??NULL));?>
            </div>
          </div>
        </div>
        <!-- END French Content-->
      </div>
    </div>
    <!-- END WIDGET BODY -->
  </div>
  <script type="text/javascript">
    function MuestraDescAssig(desc1) {
              element = document.getElementById("content_p");
              element_esp = document.getElementById("content_p_esp");
              element_fra = document.getElementById("content_p_fra");
              check = document.getElementById("fg_animacion");
              if (check.checked){ 
                  element.style.display='block';
                  element_esp.style.display='block';
                  element_fra.style.display='block';
                  if(desc1==0){
                    element.style.borderColor = "red";
                    element.style.color = "red";
                    element.style.background = "#fff0f0";
                    element_esp.style.borderColor = "red";
                    element_esp.style.color = "red";
                    element_esp.style.background = "#fff0f0";
                    element_fra.style.borderColor = "red";
                    element_fra.style.color = "red";
                    element_fra.style.background = "#fff0f0";
                  }
              }
              else {
                element.style.display='none';
                element_esp.style.display='none';
                element_fra.style.display='none';
              }     
          }
    function MuestraDescAssigRef(desc2) {
        element = document.getElementById("content_2");
        element_esp = document.getElementById("content_2_esp");
        element_fra = document.getElementById("content_2_fra");
        check = document.getElementById("fg_ref_animacion");
        if (check.checked){ 
            element.style.display='block';
            element_esp.style.display='block';
            element_fra.style.display='block';
            if(desc2==0){
              element.style.borderColor = "red";
              element.style.color = "red";
              element.style.background = "#fff0f0";
              element_esp.style.borderColor = "red";
              element_esp.style.color = "red";
              element_esp.style.background = "#fff0f0";
              element_fra.style.borderColor = "red";
              element_fra.style.color = "red";
              element_fra.style.background = "#fff0f0";
            }
        }
        else {
          element.style.display='none';
          element_esp.style.display='none';
          element_fra.style.display='none';
        }
    }
    function MuestraDescSketch(desc3) {
        element = document.getElementById("content_3");
        element_esp = document.getElementById("content_3_esp");
        element_fra = document.getElementById("content_3_fra");
        check = document.getElementById("fg_ref_sketch");
        if (check.checked){ 
            element.style.display='block';
            element_esp.style.display='block';
            element_fra.style.display='block';
            if(desc3==0){
              element.style.borderColor = "red";
              element.style.color = "red";
              element.style.background = "#fff0f0";
              element_esp.style.borderColor = "red";
              element_esp.style.color = "red";
              element_esp.style.background = "#fff0f0";
              element_fra.style.borderColor = "red";
              element_fra.style.color = "red";
              element_fra.style.background = "#fff0f0";
            }
        }
        else {
          element.style.display='none';
          element_esp.style.display='none';
          element_fra.style.display='none';
        }
    }
    function MuestraDescSketchNum(desc4) {
        element = document.getElementById("content_4");
        check = document.getElementById("no_sketch").value;
        if (check >= 1) {
            element.style.display='block';
            if(desc4==0){
              element.style.borderColor = "red";
              element.style.color = "red";
              element.style.background = "#fff0f0";
            }
        }
        else {
          element.style.display='none';
          element_esp.style.display='none';
          element_fra.style.display='none';
        }
    }    
  </script>
</div>
