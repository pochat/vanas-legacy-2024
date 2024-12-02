<?php
	# Bar chart
	$html_arriba .= "
	<script type='text/javascript'>
		(function($){
			// Setup bar chart container
      var charts = $('#charts');
      charts.append('<div id=\"monthly-chart\"></div>');

			// General config
			var payments = ".json_encode($monthly).";
			var xLabels = ".json_encode($monthlyLabels).";

      var months = ".json_encode($months).";
      var years = ".json_encode($years).";

      // Construct a data set for tooltip
      var dataset = payments.map(function(d, i){
        return {label: xLabels[i], month: months[i], year: years[i], amount: d};
      });
  
      // Revenue total
      var total = d3.sum(payments);
      $('#revenue-total').append('Total earned amount: <span style=\"font-weight: normal;\">$' + total.toLocaleString() + '</span>' );

			// Define margin and container width, height
      var margin = {top: 15, right: 0, bottom: 20, left: 40},
          width = 1280 - margin.left - margin.right,
          height = 620 - margin.bottom - margin.top,
          barWidth = width / payments.length;

      // Tooltips
      var tooltip = d3.select('#monthly-chart')
        .append('div')
        .attr('class', 'tooltip');
      tooltip.append('div')
        .attr('class', 'date');
      tooltip.append('div')
        .attr('class', 'percent');
     
      // x axis scale
      var x = d3.scale.ordinal()
        .domain(xLabels)
        .rangeRoundBands([0, width], 0.1);

     	// y axis scale
      var y = d3.scale.linear()
        .domain([0, d3.max(payments)])
        .range([height, 0]);

      var svg = d3.select('#monthly-chart')
        .append('svg')
        .attr('width', width + margin.left + margin.right)
        .attr('height', height + margin.bottom + margin.top)
        .attr('class', 'bar chart');

      // Each group is a term (e.g. Fall 2012, Winter 2012, etc..)
      var bars = svg.selectAll('.group')
        .data(payments)
        .enter()
        .append('g')
        .attr('transform', function(d, i) { return 'translate('+ margin.left +', '+ margin.top +')'; });

      bars.append('rect')
      	.attr('width', barWidth - 10) // padding 10
      	.attr('x', function(d, i){ return x(xLabels[i]); })
      	.attr('y', function(d){ return y(d); })
      	.attr('height', function(d){ return height - y(d); })
      	.attr('fill', '#0681C7');		// blue

      bars.append('text')
      	.attr('x', function(d, i){ return x(xLabels[i]) + (barWidth / 2) - 5; })  // padding 5
      	.attr('y', function(d){ return y(d) - 10; })
      	.attr('dy', '.75em')
      	.text(function(d){ return '$' + d.toLocaleString(); });

      // Set bar chart events
      var bars = svg.selectAll('rect');

      bars.on('mouseover', function(d, i){
        this.setAttribute('class', 'highlight');

        var monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
				  'July', 'August', 'September', 'October', 'November', 'December'
				];

        var month = dataset[i].month;
        var year = dataset[i].year;
        var lastMonth = dataset[i].month;
        var lastYear = dataset[i].year - 1;

        // Find the previous year and month object
        var previousAmount = dataset.map(function(d){
          if(d.month === lastMonth && d.year === lastYear){
            return d;
          }
        });
        previousAmount = previousAmount.filter(function(n){
          return typeof n !== 'undefined';
        })[0];

        var amount = dataset[i].amount;
        var percent = ((amount - previousAmount.amount) / amount * 100).toFixed(2);
        var text = (percent > 0) ? 'increase' : 'decrease';

        tooltip.select('.date').html(monthNames[month - 1] + ' ' + year);
        tooltip.select('.percent').html(percent + '% ' + text);
        tooltip.style('display', 'block');

      });

      bars.on('mouseout', function(d){
        this.setAttribute('class', '');

        tooltip.style('display', 'none');
      });

      bars.on('mousemove', function(d){
        var scrollTop = $(window).scrollTop();

        tooltip.style('top', (d3.event.pageY - scrollTop + 10) + 'px')
          .style('left', (d3.event.pageX + 10) + 'px');
      })

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