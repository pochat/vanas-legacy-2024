<?php
	# Bar chart
	$html_arriba .= "
	<script type='text/javascript'>
		(function($){
			// Setup bar chart container
      var charts = $('#charts');
      charts.append('<div id=\"bar-chart\"></div>');

			// General config
			var payments = ".json_encode($payments).";
			var xLabels = ".json_encode($xLabels).";

			// Construct the dataset by grouping the months and years together
			var barData = xLabels.map(function(d){
				return d3.sum(payments, function(payment){
					if(payment.label === d){
						return payment.count;
					}
				});
			});

			// Define margin and container width, height
      var margin = {top: 5, right: 0, bottom: 20, left: 40},
          width = 1300 - margin.left - margin.right,
          height = 680 - margin.bottom - margin.top,
          barWidth = width / barData.length;
     
      // x axis scale
      var x = d3.scale.ordinal()
        .domain(xLabels)
        .rangeRoundBands([0, width], 0.1);

     	// y axis scale
      var y = d3.scale.linear()
        .domain([0, d3.max(barData)])
        .range([height, 0]);

      // Tooltips
      var tooltip = d3.select('#bar-chart')
        .append('div')
        .attr('class', 'tooltip')
        .style('position', 'fixed');
      tooltip.append('div')
        .attr('class', 'label');
      tooltip.append('span')
        .attr('class', 'program');
      tooltip.append('span')
        .attr('class', 'count');
      tooltip.append('div')
        .attr('class', 'percent');

      var color = d3.scale.category20b();

      var svg = d3.select('#bar-chart')
        .append('svg')
        .attr('width', width + margin.left + margin.right)
        .attr('height', height + margin.bottom + margin.top)
        .attr('class', 'bar chart');

      // Each group is a term (e.g. Fall 2012, Winter 2012, etc..)
      var bars = svg.selectAll('.group')
        .data(barData)
        .enter()
        .append('g')
        .attr('transform', function(d, i) { return 'translate('+ margin.left +', '+ margin.top +')'; });

      bars.append('rect')
      	.attr('width', barWidth - 10) // padding 10
      	.attr('x', function(d, i){ return x(xLabels[i]); })
      	.attr('y', function(d){ return y(d); })
      	.attr('height', function(d){ return height - y(d); })
      	.attr('fill', '#0EB0FF');		// light blue

      bars.append('text')
      	.attr('x', function(d, i){ return x(xLabels[i]) + (barWidth / 2) - 10; })  // padding 10
      	.attr('y', function(d){ return y(d) + 5; })
      	.attr('dy', '.75em')
      	.text(function(d){ return d; });

      // x-axis
      var xAxis = d3.svg.axis()
        .scale(x)
        .orient('bottom');

      svg.append('g')
        .attr('class', 'x axis')
        .attr('transform', 'translate('+ margin.left +','+ (height + margin.top) +')')
        .call(xAxis);

      // y-axis
      var yAxis = d3.svg.axis()
        .scale(y)
        .orient('left')
        .tickFormat(d3.format('2f'))
        .tickSize(3);

      svg.append('g')
        .attr('class', 'y axis')
        .attr('transform', 'translate('+ margin.left +', '+ margin.top +')')
        .attr('height', height)
        .call(yAxis);
		})(jQuery);
	</script>";
?>