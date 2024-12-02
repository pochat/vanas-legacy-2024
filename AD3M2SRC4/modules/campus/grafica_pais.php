<?php
  
  # Libreria general de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  $fg_pais=RecibeParametroNumerico('fg_pais');
  $fe_ini=RecibeParametroFecha('fe_ini');
  $fe_fin=RecibeParametroFecha('fe_fin');
  
  if(!empty($fe_ini))
      $fe_ini = "'".ValidaFecha($fe_ini)."'";
  if(!empty($fe_fin))
      $fe_fin = "'".ValidaFecha($fe_fin)."'";




  if($fg_pais==1){
	  
	  # Countries - basic
	  $Query2  = "SELECT ds_pais, COUNT(1) ";
	  $Query2 .= "FROM c_usuario a ";
	  $Query2 .= "LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
	  $Query2 .= "LEFT JOIN c_pais d ON d.fl_pais=b.ds_add_country ";
	  $Query2 .= "WHERE fl_perfil=3 ";
      if(  (!empty($fe_ini)) &&(!empty($fe_fin)) )
      $Query2 .="AND fe_alta>=$fe_ini AND fe_alta<=$fe_fin ";
	  $Query2 .= "GROUP BY ds_pais ";
	  $rs1 = EjecutaQuery($Query2);
	  $rs2 = EjecutaQuery($Query2);
	  $rs5 = EjecutaQuery($Query2);
	  $total_cantidad = 0;
	   for($i=0; $row5=RecuperaRegistro($rs5); $i++){

          $cantidad=$row5[1];
          $total_cantidad +=$cantidad;

      }
	  
	  
  ?>	  
	  
	  
<canvas id="pais" height="200"></canvas>


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
      for($i=0; $row1=RecuperaRegistro($rs1); $i++){

          $ds_pais = $row1[0];
          $count=$row1[1];

          echo $count.","; 
?>  

<?php  
      }
?>
    



];
 
 
    
 var colores=['rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)']
 
 
    var etiquetas=[

<?php 
      for($m=0; $row2=RecuperaRegistro($rs2); $m++){

          $ds_pais = $row2[0];
          $count=$row2[1];
		   
		  
		  $porcentaje=number_format((1000 * $count / $total_cantidad ) / 10,2);

		  

          echo"' ".$ds_pais.": ".$porcentaje."% '".","; 
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

    var ctx = document.getElementById("pais").getContext("2d");
    new Chart(ctx, config);
</script>


	  
	  
	  
  <?php	  
  }else{
	  
	  
	  # Countries - by programs
	  $Query3  = "SELECT nb_programa, ds_pais, COUNT(1) ";
	  $Query3 .= "FROM c_usuario a ";
	  $Query3 .= "LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
	  $Query3 .= "LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
	  $Query3 .= "LEFT JOIN c_pais d ON d.fl_pais=b.ds_add_country ";
	  $Query3 .= "WHERE fl_perfil=3 ";
      if(  (!empty($fe_ini)) &&(!empty($fe_fin)) )
      $Query3 .="AND fe_alta>=$fe_ini AND fe_alta<=$fe_fin ";
	  $Query3 .= "GROUP BY nb_programa, ds_pais ";
 
	  $rs1 = EjecutaQuery($Query3);
	  $rs2 = EjecutaQuery($Query3);
	  $rs3 = EjecutaQuery($Query3);

      # Variable initialization to avoid errors
      $total_cantidad =0;
      
      for($i=0; $row1=RecuperaRegistro($rs1); $i++){

          $cantidad=$row1[2];
          $total_cantidad +=$cantidad;

      }
	  $entro=2;
  ?>
  
		  
<canvas id="paisprograms" height="200"></canvas>


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
      for($j=0; $row2=RecuperaRegistro($rs2); $j++){

          $ds_programa = $row2[0];
          $count=$row2[2];

          echo $count.","; 
?>  

<?php  
      }
?>
    



];
 
 var colores=['rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)','rgba(255, 99, 132, 0.7)','rgba(54, 162, 235, 0.7)','rgba(255, 206, 86, 0.7)','rgba(75, 192, 192, 0.7)','rgba(153, 102, 255, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 159, 64, 0.7)','rgba(153, 102, 255, 0.7)','rgba(255, 99, 132, 0.7)']
 
    var etiquetas=[

<?php 
      for($m=0; $row3=RecuperaRegistro($rs3); $m++){

          $nb_programa = $row3[0];
		  $ds_pais = $row3[1];
          $count=$row3[2];
		  
		  
		  $porcentaje=number_format((1000 * $count / $total_cantidad ) / 10,2);

		  

          echo"' ".$nb_programa.", ".$ds_pais.", ".$porcentaje."%'".","; 
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

    var ctx = document.getElementById("paisprograms").getContext("2d");
    new Chart(ctx, config);
</script>
  
  
  
  
  
  <?php 
  }
  ?>
  


	
		
	

	
		





















  
  
  
  
  
  
  
  
 