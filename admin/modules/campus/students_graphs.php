<?php
  # Create the list of students
  $Query2 = $Query;
  $rs2 = EjecutaQuery($Query2);
  for($i=0; $row2=RecuperaRegistro($rs2); $i++){
    $students[$i] = $row2[0];
  }
  $students = implode(",", $students);

  # Graph chart containers
  $html_arriba .= "
  <div style='position: relative; float: left; width: 100%;'>
    <br>
    <div style='position: absolute; top: 10px; right: 0;'>
      <a href='javascript:toggleCharts();'>Charts <img src='".PATH_IMAGES."/charts_students.png' border='none'/></a>
    </div>
    <br>
  </div>
  <div id='charts' title='Charts' style='display:none;'>
    <div style='width:100%; height: auto;'>
      <div style='width:25%; float: left; overflow:hidden; font-size:16px; text-align: center; height:auto;'>
        <div id='panel_1' style='position:relative;'>
          <b>Gender</b>
          <form>
            <input type='radio' name='mode' value='basic' checked='checked'>Basic
            <input type='radio' name='mode' value='program'>Detailed - by Programs
          </form>
        </div>
        <div id='legend_1' class='legend-accordion'>
          <h3 style='font-size:16px;'>Gender</h3>
          <div id='legend_1_panel'></div>
        </div>
      </div>
      <div style='width:25%; float: left; overflow:hidden; font-size:16px; text-align: center; height:auto;'>
        <div id='panel_2' style='position:relative;'>
          <b>Age</b>
          <form>
            <input type='radio' name='mode' value='basic' checked='checked'>Basic
            <input type='radio' name='mode' value='program'>Detailed - by Programs
          </form>
        </div>
        <div id='legend_2' class='legend-accordion'>
          <h3 style='font-size:16px;'>Age</h3>
          <div id='legend_2_panel'></div>
        </div>
      </div>
      <div style='width:25%; float: left; overflow:hidden; font-size:16px; text-align: center; height:auto;'>
        <div id='panel_3' style='position:relative;'>
          <b>Number of Students Per Program</b>
          <form>
            <input type='radio' name='mode' value='basic' checked='checked'>Basic
            <input type='radio' name='mode' value='program'>Bars - by Program Start Year
          </form>
        </div>
        <div id='legend_3' class='legend-accordion'>
          <h3 style='font-size:16px;'>Number of Students Per Program</h3>
          <div id='legend_3_panel'></div>
        </div>
      </div>
      <div style='width:25%; float: left; overflow:hidden; font-size:16px; text-align: center; height:auto;'>
        <div id='panel_4' style='position:relative;'>
          <b>Countries</b>
          <form>
            <input type='radio' name='mode' value='basic' checked='checked'>Basic
            <input type='radio' name='mode' value='program'>Detailed - by Programs
          </form>
        </div>
        <div id='legend_4' class='legend-accordion'>
          <h3 style='font-size:16px;'>Countries</h3>
          <div id='legend_4_panel'></div>
        </div>
      </div>
    </div>
  </div>
  <script type='text/javascript'>
    // Setup chart container
    var charts = $('#charts');
    charts.dialog({
      autoOpen : false,
      //autoOpen : true,
      width: 1320,
      height: 720
    });

    // Handles toggle events
    function toggleCharts(){
      if(charts.dialog('isOpen') === false){
        charts.dialog('open');  
      } else {
        charts.dialog('close');
      }
    }
  </script>";

  # General style
  $html_arriba .= "
  <style>
    .chart text {
      fill: white;
      font: 12px sans-serif;
      text-anchor: middle;
    }
    .axis text {
      font: 11px sans-serif;
      fill: #000;
    }
    .axis path,
    .axis line {
      fill: none;
      stroke: #000;
      shape-rendering: crispEdges;
    }
    path {
      stroke: #FFF;
      stroke-width: 1;  
    }
    .highlight {
      opacity: 0.8;
    }
    .normal-text {
      font-size: 16px;
      font-weight: normal !important;
    }
    .tooltip {
      background: #EEE;
      box-shadow: 0 0 5px #999999;
      color: #333;
      display: none;
      font-size: 12px;
      padding 10px;
      position: fixed;
      text-align: center;
      width: 150px;
      height: auto;
      z-index: 10;
    }
    .tooltip .program,
    .tooltip .label {
      font-weight: bold;
      font-size: 16px;
    }
    .tooltip .percent,
    .tooltip .count{
      font-size: 16px;
    }
    .legend-accordion > .ui-accordion-content {
      height: 280px !important;
    }
    #bar-chart {
      width: auto;
      margin-top: 2px;
      margin-left: 3px;
      overflow-x: show; 
      position: absolute;
      top: 0;
      left: 0;
      background-color: white;
      display: none;
    }
    #bar-chart > .close {
      position: absolute;
      top: 10;
      right: 0;
    }
    #bar-chart > .close:hover {
      opacity: 0.8;
    }
  </style>";

  # Helper function, converts hex to rgba
  $html_arriba .="
  <script type='text/javascript'>
    // Convert hex to decimal
    function convertHexToRGBA(hex, opacity){
      hex = hex.replace('#','').trim();
      var r = parseInt(hex.substring(0,2), 16);
      var g = parseInt(hex.substring(2,4), 16);
      var b = parseInt(hex.substring(4,6), 16);
      opacity = opacity || 1;

      return 'rgba('+r+','+g+','+b+','+opacity+')';
    }
  </script>";

  # Present the charts
  require 'students_graphs_gender.php';
  require 'students_graphs_age.php';
  require 'students_graphs_program.php';
  require 'students_graphs_country.php';

  # Create all legend containers accordion, after charts has been created
  $html_arriba .="
  <script type='text/javascript'>
    // Setup accordions
    $('.legend-accordion').accordion({ event: '' });
  </script>";
?>