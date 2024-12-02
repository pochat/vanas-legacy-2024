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

  </style>
  
  <?php 
    #Recupermos las calificaciones existentes
    $contador=0;
    
    echo PresentaRubric($clave,'','','','',true);
  
  ?>

