<?php
  
  # Libreria general de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  $fg_students=RecibeParametroNumerico('fg_students');
  $fe_ini=RecibeParametroFecha('fe_ini');
  $fe_fin=RecibeParametroFecha('fe_fin');
  
  if(!empty($fe_ini))
  $fe_ini = "'".ValidaFecha($fe_ini)."'";
  if(!empty($fe_fin))
  $fe_fin = "'".ValidaFecha($fe_fin)."'";
  
  
  if($fg_students==1){
	  
	  # Number of students per program - basic
	  $Query2  = "SELECT nb_programa, COUNT(1) ";
	  $Query2 .= "FROM c_usuario a ";
	  $Query2 .= "LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
	  $Query2 .= "LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
	  $Query2 .= "WHERE fl_perfil=3 ";
      if(  (!empty($fe_ini)) &&(!empty($fe_fin)) )
      $Query2 .="AND fe_alta>=$fe_ini AND fe_alta<=$fe_fin ";
	  $Query2 .= "GROUP BY nb_programa ";
	  $rs2 = EjecutaQuery($Query2);
	  $rs3 = EjecutaQuery($Query2);
	  $rs4 = EjecutaQuery($Query2);

    # Variable initialization to avid error
	  $total_cantidad=0;
    
	  for($i=0; $row2=RecuperaRegistro($rs2); $i++){

          $cantidad=$row2[1];
          $total_cantidad +=$cantidad;

      }
	  
  ?>	  
	  
	

	  
<canvas id="students" height="200"></canvas>


<script>



    Chart.defaults.doughnutLabels = Chart.helpers.clone(Chart.defaults.doughnut);

    var helpers = Chart.helpers;
    var defaults = Chart.defaults;

    Chart.controllers.doughnutLabels = Chart.controllers.doughnut.extend({
        updateElement: function(arc, index, reset) {
            var _this = this;
            var chart = _this.chart,
                chartArea = chart.chartArea,
                opts = chart.options,
                animationOpts = opts.animation,
                arcOpts = opts.elements.arc,
                centerX = (chartArea.left + chartArea.right) / 2,
                centerY = (chartArea.top + chartArea.bottom) / 2,
                startAngle = opts.rotation, // non reset case handled later
                endAngle = opts.rotation, // non reset case handled later
                dataset = _this.getDataset(),
                circumference = reset && animationOpts.animateRotate ? 0 : arc.hidden ? 0 : _this.calculateCircumference(dataset.data[index]) * (opts.circumference / (2.0 * Math.PI)),
                innerRadius = reset && animationOpts.animateScale ? 0 : _this.innerRadius,
                outerRadius = reset && animationOpts.animateScale ? 0 : _this.outerRadius,
                custom = arc.custom || {},
                valueAtIndexOrDefault = helpers.getValueAtIndexOrDefault;

            helpers.extend(arc, {
                // Utility
                _datasetIndex: _this.index,
                _index: index,

                // Desired view properties
                _model: {
                    x: centerX + chart.offsetX,
                    y: centerY + chart.offsetY,
                    startAngle: startAngle,
                    endAngle: endAngle,
                    circumference: circumference,
                    outerRadius: outerRadius,
                    innerRadius: innerRadius,
                    label: valueAtIndexOrDefault(dataset.label, index, chart.data.labels[index])
                },

                draw: function () {
                    var ctx = this._chart.ctx,
                                    vm = this._view,
                                    sA = vm.startAngle,
                                    eA = vm.endAngle,
                                    opts = this._chart.config.options;
				
                    var labelPos = this.tooltipPosition();
                    var segmentLabel = vm.circumference / opts.circumference * 100;
					
                    ctx.beginPath();
					
                    ctx.arc(vm.x, vm.y, vm.outerRadius, sA, eA);
                    ctx.arc(vm.x, vm.y, vm.innerRadius, eA, sA, true);
					
                    ctx.closePath();
                    ctx.strokeStyle = vm.borderColor;
                    ctx.lineWidth = vm.borderWidth;
					
                    ctx.fillStyle = vm.backgroundColor;
					
                    ctx.fill();
                    ctx.lineJoin = 'bevel';
					
                    if (vm.borderWidth) {
                        ctx.stroke();
                    }
					
                    if (vm.circumference > 0.15) { // Trying to hide label when it doesn't fit in segment
                        ctx.beginPath();
                        ctx.font = helpers.fontString(opts.defaultFontSize, opts.defaultFontStyle, opts.defaultFontFamily);
                        ctx.fillStyle = "#fff";
                        ctx.textBaseline = "top";
                        ctx.textAlign = "center";
            
                        // Round percentage in a way that it always adds up to 100%
                        ctx.fillText(segmentLabel.toFixed(0) + "%", labelPos.x, labelPos.y);
                    }
                }
            });

            var model = arc._model;
            model.backgroundColor = custom.backgroundColor ? custom.backgroundColor : valueAtIndexOrDefault(dataset.backgroundColor, index, arcOpts.backgroundColor);
            model.hoverBackgroundColor = custom.hoverBackgroundColor ? custom.hoverBackgroundColor : valueAtIndexOrDefault(dataset.hoverBackgroundColor, index, arcOpts.hoverBackgroundColor);
            model.borderWidth = custom.borderWidth ? custom.borderWidth : valueAtIndexOrDefault(dataset.borderWidth, index, arcOpts.borderWidth);
            model.borderColor = custom.borderColor ? custom.borderColor : valueAtIndexOrDefault(dataset.borderColor, index, arcOpts.borderColor);

            // Set correct angles if not resetting
            if (!reset || !animationOpts.animateRotate) {
                if (index === 0) {
                    model.startAngle = opts.rotation;
                } else {
                    model.startAngle = _this.getMeta().data[index - 1]._model.endAngle;
                }

                model.endAngle = model.startAngle + model.circumference;
            }

            arc.pivot();
        }
    });

    var valores=[
<?php 
      for($i=0; $row3=RecuperaRegistro($rs3); $i++){

          $nb_programa = $row3[0];
          $count=$row3[1];

          echo $count.","; 
?>  

<?php  
      }
?>
    



];
 
  var colores=['rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)']
 
    var etiquetas=[

<?php 
      for($m=0; $row4=RecuperaRegistro($rs4); $m++){

          $nb_programa = $row4[0];
          $count=$row4[1];
		  
		 $porcentaje=number_format((1000 * $count / $total_cantidad ) / 10,2);

		  
		  

          echo"' ".$nb_programa." ".$porcentaje."%'".","; 
?>  

<?php  
      }
?>



];

    var config = {
        type: 'doughnutLabels',
        data: {
            datasets: [{
                data: valores,
                backgroundColor: colores,
                label: 'Dataset 1'
            }],
            labels:etiquetas,
	
        },
        options: {
            responsive: true,
            legend: {
                position: 'top',
				display:false
            },
            title: {
                display: true,
                text: ''
            },
	
	
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    };

    var ctx = document.getElementById("students").getContext("2d");
    new Chart(ctx, config);
</script>





	
	  
	  
	  
	  
  <?php	  
  }else{
	  
	  
	    
	# Number of students per program - bar by Program Start Date
	  $Query3  = "SELECT ";
	  $Query3 .= "  nb_programa, ";
	  $Query3 .= "  CASE ";
	  $Query3 .= "    WHEN MONTH(start_date) IN (12, 1, 2) THEN CONCAT('Winter ', YEAR(start_date)) ";
	  $Query3 .= "    WHEN MONTH(start_date) IN (3, 4, 5) THEN CONCAT('Spring ', YEAR(start_date)) ";
	  $Query3 .= "    WHEN MONTH(start_date) IN (6, 7, 8) THEN CONCAT('Summer ', YEAR(start_date)) ";
	  $Query3 .= "    WHEN MONTH(start_date) IN (9, 10, 11) THEN CONCAT('Fall ', YEAR(start_date)) ";
	  $Query3 .= "  END AS periods, ";
	  $Query3 .= "  COUNT(1) ";
	  $Query3 .= "FROM ( ";
	  $Query3 .= "  SELECT a.fl_usuario, b.fl_periodo, c.nb_programa, ";
	  $Query3 .= "  (SELECT fe_inicio FROM k_term te, c_periodo i, k_alumno_term al WHERE te.fl_periodo=i.fl_periodo AND te.fl_term=al.fl_term AND al.fl_alumno=a.fl_usuario AND no_grado=1 LIMIT 1) AS start_date ";
	  $Query3 .= "  FROM c_usuario a ";
	  $Query3 .= "  LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
	  $Query3 .= "  LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
	  $Query3 .= "  WHERE fl_perfil=3 ";
	  $Query3 .= ") AS program_table ";
	  $Query3 .= "WHERE program_table.start_date IS NOT NULL ";
	  $Query3 .= "GROUP BY periods, nb_programa ";
	  $rs3 = EjecutaQuery($Query3);
	  $rs_3 = EjecutaQuery($Query3);
    
    # Variable initialization to avoid error
    $total_cantidad = 0;

      for($i=0; $row_3=RecuperaRegistro($rs_3); $i++){

          $cantidad=$row_3[2];
          $total_cantidad +=$cantidad;

      }
	  $entro=2;
	  
           
			
            
      ?>
	  

    <div id="studentsprograms" style="min-width:300px; max-width: 1800px; margin: 0 auto"></div>
  
   
   <?php
   
   
		for($i=0; $row3=RecuperaRegistro($rs3); $i++){
				
				 $nb_programa = $row3[0];
				 $fg_periodo= $row3[1];
				 $count = $row3[2];
				 
				 $periodTable_period[] = $fg_periodo;
				 $nameTable_program[] = $nb_programa;
				 // Table data
				 $periodTable[$fg_periodo][$nb_programa] = (int)$count;
	
		}
		  // Remove array keys, the keys are stored in xLabels and xInnerLabels
		  $periodTable = array_values($periodTable);

		  // Create the labels for the bar chart
		  $xperiodo = array_values(array_unique($periodTable_period));
		  $logintudxPeriodo=count($xperiodo);
		  $longitudPeridoTable=count($nameTable_program);
		  $xprogramas = array_values(array_unique($nameTable_program));
		  $longitud_programas=count($xprogramas);

   



#todo
          
#  {
#    name: 'Tokyo',
#    data: [49.9, 0, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4,2,2,2,2,2,2,2,2]

#  },

   
   ?>
   
   
  
  <script>
  
  
Highcharts.chart('studentsprograms', {
  chart: {
    type: 'column',
	height: 600,
	hide:true
  },
  title: {
    text: ''
  },
  subtitle: {
    text: ''
  },
  xAxis: {
    categories: [
	

	<?php
      for($i=0; $i<$logintudxPeriodo;$i++){

               $fg_fecha=$xperiodo[$i];
               
   ?>
     
	
          '<?php echo $fg_fecha ?>',


    <?php } 
      
      ?>
	
    
	  
	  
	  
    ],
    crosshair: true
  },
  yAxis: {
    min: 0,
    title: {
      text: 'Rainfall (mm)'
    }
  },
  
 /*tooltip: { 
   formatter: function () {
        return '<b>' + this.series.name + '</b><br>Total:<b>' + this.series.myData + '</b><br>';
    }
 }, 
  tooltip: {
    headerFormat: '<span style="font-size:10px">{point.key}</span><table width="100%">',
    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
      '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
    footerFormat: '</table>',
    shared: true,
    useHTML: true
	
  },
  */
   tooltip: {
        formatter: function () {
            return '<b>' + this.series.name +
                '</b><br> Total: <b>' + this.y + '</b><br>'+
				'';
        },
		useHTML: true
    },
  
  
   plotOptions: {
    column: {
      pointPadding: 0.2,
      borderWidth: 0
    }
  },
  series: [
      <?php
     
      for($i=0; $i<$longitud_programas;$i++){

          $nb_programa=$xprogramas[$i];

          $name=$nb_programa;

          $cantidad=$periodTable[$i][$nb_programa];  
      ?>
       {
           name: '<?php echo $nb_programa; ?>',
           data: [

					
						<?php 
						  for($m=0;$m<$logintudxPeriodo;$m++){
							  
							  $periodo=$xperiodo[$m];
							  $cantidad=$periodTable[$m][$nb_programa];
						  
							  if(empty($cantidad))
								  $cantidad=0;

							  if($m<=($logintudxPeriodo-1))
								  echo $cantidad.",";
							  else
								  echo $cantidad;
						  }  
						?>
					
					
					
					
           ]
		   ,
		    myData : 'ppttt',
		 <?php if($i<=6){ ?>  
		   visible: true
		 <?php }else{ ?>
		   visible: false
		 <?php } ?>
		 
       },

      <?php
          

      }

      ?>



 
  
  
  ]
});
  
  </script>
  
  
  
  <!--
  
		<script>

	
// Create the chart
Highcharts.chart('studentsprograms', {
  chart: {
    type: 'column'
  },
  title: {
    text: 'Browser market shares. January, 2018'
  },
  subtitle: {
    text: 'Click the columns to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
  },
  xAxis: {
    type: 'category'
  },
  yAxis: {
    title: {
      text: 'Total percent market share'
    }

  },
  legend: {
    enabled: false
  },
  plotOptions: {
    series: {
      borderWidth: 0,
      dataLabels: {
        enabled: true,
        format: '{point.y:.1f}%'
      }
    }
  },

  tooltip: {
    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
  },

  "series": [
    {
      "name": "Browsers",
      "colorByPoint": true,
      "data": [
	  
	    <?php

		for($i=0; $row3=RecuperaRegistro($rs3); $i++){
				
				 $nb_programa = $row3[0];
				 $fg_periodo= $row3[1];
				 $count = $row3[2];
				 
				 $periodTable_period[] = $fg_periodo;
				 $periodTable_program[] = $nb_programa;
				 // Table data
				 $periodTable[$fg_periodo][$nb_programa] = (int)$count;
	
		}
		  // Remove array keys, the keys are stored in xLabels and xInnerLabels
		  $periodTable = array_values($periodTable);

		  // Create the labels for the bar chart
		  $xLabels = array_values(array_unique($periodTable_period));
          $logintudxLabels=count($xLabels);
          $longitudPeridoTable=count($periodTable);
		  $xInnerLabels = array_values(array_unique($periodTable_program));
		

          for($i=0; $i<$logintudxLabels;$i++){


              $fg_fecha=$xLabels[$i];
		      $periodos=$periodTable[$i];
              $count=5;

        ?>

        {
			"name": "<?php echo $fg_fecha;?>",
            "y": <?php echo $count; ?>,
            "drilldown": "Chrome"
        },


        <?php
		  }
        ?>
		
		
			
		
		
		
		
		
	  
       
	   
      ]
    }
  ]
  
});
		  
		</script>
  
  
 --->
  
  <?php 
  }
  ?>
  


	
		
	

	
		





















  
  
  
  
  
  
  
  
 