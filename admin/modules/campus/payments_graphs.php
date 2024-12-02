<?php
	# Find all Earned records
	$Query4  = "SELECT STR_TO_DATE(fe_graphs, '%d-%m-%Y') pay_date, mn_pago, DATE_FORMAT(STR_TO_DATE(fe_graphs, '%d-%m-%Y'), '%b %Y') payment_date ";
	$Query4 .= $Query." ";
	$Query4 .= "AND fe_fin_pago1 = 'Earned' ";
	$Query4 .= "AND fe_graphs != '(To be paid)' ";
	$Query4 .= "AND YEAR(STR_TO_DATE(fe_graphs, '%d-%m-%Y')) >= YEAR(NOW()) - 1 "; // constrain the records to last year
	$Query4 .= "ORDER BY pay_date ";

	$rs4 = EjecutaQuery($Query4);
  for($i=0; $row4=RecuperaRegistro($rs4); $i++){
  	$mn_pago = $row4[1];
  	$fe_pagado = $row4[2];

		$payment_dates[$i] = $fe_pagado;
		$payments[$i] = array(
			'label' => $fe_pagado,
			'count' => $mn_pago
		);
  }

  # Create x axis labels
	$xLabels = array_values(array_unique($payment_dates));

	# Graph chart containers
	$html_arriba .= "
  <div style='position: relative; width: 100%;'>
    <br>
    <div style='float: right;'>
      <a href='javascript:toggleCharts();'>Charts <img src='".PATH_IMAGES."/charts.png' border='none'/></a>
    </div>
    <br>
  </div>
  <div id='charts' title='Charts' style='display:none;'></div>
  <script type='text/javascript'>
    // Setup chart container
    var charts = $('#charts');
    charts.dialog({
      autoOpen : false,
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
  </style>";

  # Present the charts
  require 'payments_graphs_general.php';

?>
