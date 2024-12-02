<?php
  
  # Libreria general de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  $fg_edad=RecibeParametroNumerico('fg_edad');
  $fe_ini=RecibeParametroFecha('fe_ini');
  $fe_fin=RecibeParametroFecha('fe_fin');
  
  if(!empty($fe_ini))
      $fe_ini = "'".ValidaFecha($fe_ini)."'";
  if(!empty($fe_fin))
      $fe_fin = "'".ValidaFecha($fe_fin)."'";


  if($fg_edad==1){
	  
	  # Ages - basic
	  $Query2  = "SELECT ";
	  $Query2 .= "  CASE ";
	  $Query2 .= "    WHEN age < 15 THEN 'under 15' ";
	  $Query2 .= "    WHEN age >= 15 AND age <= 18 THEN '15-18' ";
	  $Query2 .= "    WHEN age >= 19 AND age <= 22 THEN '19-22' ";
	  $Query2 .= "    WHEN age >= 23 AND age <= 25 THEN '23-25' ";
	  $Query2 .= "    WHEN age >= 26 AND age <= 35 THEN '26-35' ";
	  $Query2 .= "    WHEN age >= 36 AND age <= 40 THEN '36-40' ";
	  $Query2 .= "    WHEN age >= 41 AND age <= 55 THEN '41-55' ";
	  $Query2 .= "    WHEN age > 55 THEN '55+' ";
	  $Query2 .= "  END AS age_range, ";
	  $Query2 .= "  COUNT(1) ";
	  $Query2 .= "FROM( ";
	  $Query2 .= "  SELECT TIMESTAMPDIFF(YEAR, fe_nacimiento ,CURDATE()) AS age ";
	  $Query2 .= "  FROM c_usuario a ";
	  $Query2 .= "  LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
	  $Query2 .= "  WHERE fl_perfil=3 ";
      if(  (!empty($fe_ini)) &&(!empty($fe_fin)) )
      $Query2 .="AND fe_alta>=$fe_ini AND fe_alta<=$fe_fin ";

	  $Query2 .= ") as age_table ";
	  $Query2 .= "GROUP BY age_range ";
	  $rs2 = EjecutaQuery($Query2);
	  $rs3 = EjecutaQuery($Query2);
	  $rs4 = EjecutaQuery($Query2);
	  $total_cantidad = 0;
	  for($i=0; $row4=RecuperaRegistro($rs4); $i++){

      $cantidad=$row4[1];
      $total_cantidad += $cantidad;

      }
		
       ?>
	  
<canvas id="edad" height="200"></canvas>


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
      for($i=0; $row2=RecuperaRegistro($rs2); $i++){

          $fg_edad = $row2[0];
          $count=$row2[1];

          echo $count.","; 
?>  

<?php  
      }
?>
    



];
 
 
    var colores=['rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)']
 
 
    var etiquetas=[

<?php 
      for($m=0; $row3=RecuperaRegistro($rs3); $m++){

          $fg_edad = $row3[0];
          $count=$row3[1];
		  
		 $porcentaje=number_format((1000 * $count / $total_cantidad ) / 10,2);

		  
		  

          echo"' Age: ".$fg_edad." ".$porcentaje."%'".","; 
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

    var ctx = document.getElementById("edad").getContext("2d");
    new Chart(ctx, config);
</script>





	
	  
	  
	  
	  
		
	  
	  
  <?php	  
  }else{
	  
	  
	    
	   # Ages - by programs
	  $Query3  = "SELECT ";
	  $Query3 .= "  nb_programa, ";
	  $Query3 .= "  CASE ";
	  $Query3 .= "    WHEN age < 15 THEN 'under 15' ";
	  $Query3 .= "    WHEN age >= 15 AND age <= 18 THEN '15-18' ";
	  $Query3 .= "    WHEN age >= 19 AND age <= 22 THEN '19-22' ";
	  $Query3 .= "    WHEN age >= 23 AND age <= 25 THEN '23-25' ";
	  $Query3 .= "    WHEN age >= 26 AND age <= 35 THEN '26-35' ";
	  $Query3 .= "    WHEN age >= 36 AND age <= 40 THEN '36-40' ";
	  $Query3 .= "    WHEN age >= 41 AND age <= 55 THEN '41-55' ";
	  $Query3 .= "    WHEN age > 55 THEN '55+' ";
	  $Query3 .= "  END AS age_range, ";
	  $Query3 .= "  COUNT(1) ";
	  $Query3 .= "FROM( ";
	  $Query3 .= "  SELECT nb_programa, TIMESTAMPDIFF(YEAR, fe_nacimiento ,CURDATE()) AS age ";
	  $Query3 .= "  FROM c_usuario a ";
	  $Query3 .= "  LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
	  $Query3 .= "  LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
	  $Query3 .= "  WHERE fl_perfil=3 ";
      if((!empty($fe_ini)) &&(!empty($fe_fin)) )
      $Query3 .="AND fe_alta>=$fe_ini AND fe_alta<=$fe_fin ";
	  $Query3 .= ") as age_table ";
	  $Query3 .= "GROUP BY nb_programa, age_range ";
	  $rs5 = EjecutaQuery($Query3);
	  $rs6 = EjecutaQuery($Query3);
	  $rs7 = EjecutaQuery($Query3);
    $total_cantidad = 0;

      for($i=0; $row5=RecuperaRegistro($rs5); $i++){

          $cantidad=$row5[2];
          $total_cantidad +=$cantidad;

      }
	  $entro=2;
  ?>
  
		
		
<canvas id="edadprograms" height="200"></canvas>
  

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
      for($h=0; $row6=RecuperaRegistro($rs6); $h++){
				
				 $nb_programa = $row6[0];
				 $fg_gender = $row6[1];
				 $count = $row6[2];

         

          echo $count.","; 
?>  

<?php  
      }
?>
    



];
 
 
 var colores=['rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)']
 
 
 var etiquetas=[

<?php 
       for($k=0; $row7=RecuperaRegistro($rs7); $k++){
				
				 $nb_programa = $row7[0];
				 $fg_gender = $row7[1];
				 $count = $row7[2];
                
				$porcentaje=number_format((1000 * $count / $total_cantidad ) / 10,2);


          echo "' ".$nb_programa."  Age: ".$fg_gender.", ".$porcentaje."% '".","; 
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
	  display:false   //para eliminar las labels de hasta aariba
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

var ctx = document.getElementById("edadprograms").getContext("2d");
new Chart(ctx, config);
</script>



  
  
  <?php 
  }
  ?>
  


	
		
	

	
		





















  
  
  
  
  
  
  
  
 
