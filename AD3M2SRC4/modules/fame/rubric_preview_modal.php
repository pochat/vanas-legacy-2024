<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  $clave = RecibeParametroNumerico('clave');
?>
  <!----------------------------------------------------------------------------------------------------------------------------------------------------------->
  <input type="hidden" value="<?php echo $clave; ?>" id="fl_registro" name="fl_registro" />
  <!-- widget content -->
  <style>
    .button input[type="text"]{
      height:1.5em;
      width:7em;
      -webkit-transform: rotate(-90deg); 
      -moz-transform: rotate(-90deg); 
      font-size:1.5em;
      border:0 none;
      background:none;
    }

    /*
    .dropzone .dz-default.dz-message {
    background-image: url(../../images/dropzone_small.png) !important;
    width: 1px !important;
    height: 1px !important;
    }
    */


    .dropzone .dz-default.dz-message {
      width: 1px !important;
      height:1px !important;
      background-image: url() !important;
      background-repeat: no-repeat !important;
    }

    .font {
      color:#333 !important;
      font: 18x Arial !important;
      font-weight:100 !important;
    }
  </style>
  
  <?php 
    #Recupermos las calificaciones existentes
    $contador=0;
    
    $Query_prin = "SELECT fl_criterio, no_valor FROM k_criterio_programa_fame WHERE fl_programa_sp = $clave ORDER BY no_orden ASC ";
    $rs_prin = EjecutaQuery($Query_prin);
    for($i_prin=1;$row_prin=RecuperaRegistro($rs_prin);$i_prin++) {
      $fl_criterio = $row_prin[0];
      $no_valor_criterio = $row_prin[1];
      
      $rs_nb_crit = RecuperaValor("SELECT nb_criterio FROM c_criterio WHERE fl_criterio = $fl_criterio");
      $nb_crit = str_texto($rs_nb_crit[0]);
      
      echo "<div class='row' style='height:auto; padding-left:75px;'>";
      echo "   <div class='col-md-1' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'>				
      
          <div class='well well-lg text-center' style='padding: 2px;background: #F2F2F2;'>
            Criterion
          </div>
          <br/>
          <div class='panel panel-default' style='height:338px;'>
            <div  class='panel-body text-center' style='writing-mode: vertical-lr; transform: rotate(180deg); font-size:18px; font-weight:bold; padding: 15px 35px 60px 50px;'>$nb_crit</div>
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
        }else
          $icono = "";

        ?>
   
        <div class="col-md-2" style="margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;">				
      
          <div class="well well-lg text-center" style='padding: 2px;background: #F2F2F2;'>
            <?php echo $ds_calificacion."&nbsp;&nbsp;".$icono;  ?>
          </div>
          <br/>
          <div class="panel panel-default">
            <div class="panel-body text-center">
              <span  style="color:#8FCAE5;font-size:15px; "><?php echo $ds_equivalencia;  ?> </span>  <p>&nbsp;</p>
                <div class="knobs-demo">
                  <div>
                    <input class="knob<?php echo $contador; ?> font"     value="<?php echo $no_max; ?>"  disabled/>
                  </div>
                </div>
            
                <div class="form-group text-left" style="padding-left:5px; padding-right:5px;">
                   <div id="desc<?php echo $contador; ?>"></div>
                   <hr>
                   <?php 
                    echo "<div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
                      <small class='text-muted'><i>$ds_desc</i></small>              
                    </div>";
                  ?>
                </div>
            </div>
          </div>

        </div>
    
    
    
        <script>
            $(document).ready(function () {
             document.getElementById("desc<?php echo $contador;?>").disabled = true;//tofos al cargar el document estan desaibiltados
              $("#char<?php echo $contador; ?>").addClass('hidden');//botones desabilitados
            //<!--propiedades del input knob -->
                $('.knob<?php echo $contador; ?>').knob({
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
        </script>
            
        <?php
      }
            echo "   <div class='col-md-1' style='margin-left:1px;margin-right:1px;padding-left: 1px;padding-right: 1px;'></div>";
      echo "</div>";  
    }
    
  ?>
  <script>
   $(document).ready(function () {
    //document.getElementById("nb_criterio").disabled = true;
    //document.getElementById("no_porcentaje").disabled = true;
    $("#btncancelar").addClass('disabled');//botones desabilitados
    $("#btnsaves").addClass('disabled');//botones desabilitados
   });
   
  function EditarNombreCriterio(){

      document.getElementById("nb_criterio").disabled = false;
    document.getElementById("no_porcentaje").disabled = false;
    $("#btncancelar").removeClass('disabled');//botones desabilitados
     $("#btnsaves").removeClass('disabled');//botones desabilitados
  }

  function CancelarNombreCriterio(){
      document.getElementById("nb_criterio").disabled = true;
    document.getElementById("no_porcentaje").disabled = true;
    $("#btncancelar").addClass('disabled');//botones desabilitados
      $("#btnsaves").addClass('disabled');//botones desabilitados
  }

  function GuardarNombreCriterio(){
      document.getElementById("nb_criterio").disabled = true;
    document.getElementById("no_porcentaje").disabled = true;
    $("#btncancelar").addClass('disabled');//botones desabilitados
      $("#btnsaves").addClass('disabled');//botones desabilitados
              
  }

  </script>

  <script src="<?php echo PATH_LIB; ?>/fame/dropzone.min.js"></script>	
  <script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/knob/jquery.knob.min.js"></script><!---plugin necesario para pintar el circulo -->
<!----------------------------------------------------------------------------------------------------------------------------------------------------------->
