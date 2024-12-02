<?php
  # Number of students per program - basic
  $Query2  = "SELECT nb_programa, COUNT(1) ";
  $Query2 .= "FROM c_usuario a ";
  $Query2 .= "LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
  $Query2 .= "LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
  $Query2 .= "WHERE fl_usuario IN ($students) ";
  $Query2 .= "GROUP BY nb_programa ";
  $rs2 = EjecutaQuery($Query2);
  for($i=0; $row2=RecuperaRegistro($rs2); $i++){
    $nb_programa = $row2[0];
    $count = $row2[1];

    $programs_basic[$i] = (Object) array(
      "label" => $nb_programa,
      "count" => (int)$count
    );
  }

  # Number of students per program - bar by Program Start Date
  $Query2  = "SELECT ";
  $Query2 .= "  nb_programa, ";
  $Query2 .= "  CASE ";
  $Query2 .= "    WHEN MONTH(start_date) IN (12, 1, 2) THEN CONCAT('Winter ', YEAR(start_date)) ";
  $Query2 .= "    WHEN MONTH(start_date) IN (3, 4, 5) THEN CONCAT('Spring ', YEAR(start_date)) ";
  $Query2 .= "    WHEN MONTH(start_date) IN (6, 7, 8) THEN CONCAT('Summer ', YEAR(start_date)) ";
  $Query2 .= "    WHEN MONTH(start_date) IN (9, 10, 11) THEN CONCAT('Fall ', YEAR(start_date)) ";
  $Query2 .= "  END AS periods, ";
  $Query2 .= "  COUNT(1) ";
  $Query2 .= "FROM ( ";
  $Query2 .= "  SELECT a.fl_usuario, b.fl_periodo, c.nb_programa, ";
  $Query2 .= "  (SELECT fe_inicio FROM k_term te, c_periodo i, k_alumno_term al WHERE te.fl_periodo=i.fl_periodo AND te.fl_term=al.fl_term AND al.fl_alumno=a.fl_usuario AND no_grado=1 LIMIT 1) AS start_date ";
  $Query2 .= "  FROM c_usuario a ";
  $Query2 .= "  LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
  $Query2 .= "  LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
  $Query2 .= "  WHERE fl_usuario IN ($students) ";
  $Query2 .= ") AS program_table ";
  $Query2 .= "WHERE program_table.start_date IS NOT NULL ";
  $Query2 .= "GROUP BY periods, nb_programa ";
  $rs2 = EjecutaQuery($Query2);

  for($i=0; $row2=RecuperaRegistro($rs2); $i++){
    $nb_programa = $row2[0];
    $period = $row2[1];
    $count = $row2[2];

    $periodTable_period[] = $period;
    $periodTable_program[] = $nb_programa;
    // Table data
    $periodTable[$period][$nb_programa] = (int)$count;
  }

  // Remove array keys, the keys are stored in xLabels and xInnerLabels
  $periodTable = array_values($periodTable);

  // Create the labels for the bar chart
  $xLabels = array_values(array_unique($periodTable_period));
  $xInnerLabels = array_values(array_unique($periodTable_program));

  # Pie chart
  $html_arriba .= "
  <script type='text/javascript'>
    (function($){
      // General configs
      var basic = ".json_encode($programs_basic).";

      var total = d3.sum(basic.map(function(d){
        return d.count;
      }));

      var width = 320,
          height = 300,
          radius = Math.min(width, height) / 2,
          donutWidth = 75;

      // Arc
      var arc = d3.svg.arc()
        .innerRadius(radius - donutWidth)
        .outerRadius(radius);

      // Pie
      var pie = d3.layout.pie()
        .value(function(d){ return d.count; })
        .sort(null);

      // Tooltips
      var tooltip = d3.select('#panel_3')
        .append('div')
        .attr('class', 'tooltip');
      tooltip.append('span')
        .attr('class', 'label');
      tooltip.append('span')
        .attr('class', 'count');
      tooltip.append('div')
        .attr('class', 'percent');

      // Legends
      var legendPanel = $('#legend_3_panel');

      // Pie chart - basic
      var svg = d3.select('#panel_3')
        .append('svg')
        .attr('width', width)
        .attr('height', height)
        .attr('class', 'basic')
        .append('g')
        .attr('transform', 'translate('+(width / 2)+','+(height / 2)+')');

      // Color scale
      var color = d3.scale.category20b();

      // Add pie slices
      var path = svg.selectAll('path')
        .data(pie(basic))
        .enter()
        .append('path')
        .attr('d', arc)
        .attr('fill', function(d, i){
          return (typeof d.data.program !== 'undefined') ? color(d.data.program) : color(d.data.label);
        });
    
      // Create legends
      var legendBasic = basic.map(function(d){
        var percent = Math.round(1000 * d.count / total) / 10;

        return (typeof d.program !== 'undefined') ? 
          '<div data-program=\"'+d.program+'\" data-label=\"'+d.label+'\" style=\"border-bottom:1px solid #D9D9D9;\">'+
            '<h3>'+d.program+'</h3>'+
            '<h3>'+d.label+': <span class=\"normal-text\">'+d.count+'</span></h3>'+
            '<h3>Percent: <span class=\"normal-text\">'+percent+'%</span></h3>'+
          '</div>'
          :
          '<div data-label=\"'+d.label+'\" style=\"border-bottom:1px solid #D9D9D9;\">'+
            '<h3>'+d.label+': <span class=\"normal-text\">'+d.count+'</span></h3>'+
            '<h3>Percent: <span class=\"normal-text\">'+percent+'%</span></h3>'+
          '</div>';
      }).join('');

      // Add legends on initialization
      legendPanel.html(legendBasic);

      // Set events
      var path = svg.selectAll('path');

      path.on('mouseover', function(d){
        var percent = Math.round(1000 * d.data.count / total) / 10;
        tooltip.select('.label').html(d.data.label+': ');
        tooltip.select('.count').html(d.data.count);
        tooltip.select('.percent').html(percent + '%');
        tooltip.style('display', 'block');

        // Highlight the slice
        this.setAttribute('class', 'highlight');

        // Highlight the info box
        var rgba = convertHexToRGBA(this.getAttribute('fill'), 0.8);
        var infoBox = legendPanel.find('[data-label=\"'+d.data.label+'\"]');

        // Set background color
        infoBox.css('background-color', rgba);

        // Reset scroll
        legendPanel.scrollTop(0);
        // Set scroll position
        legendPanel.scrollTop( infoBox.position().top - 20 ); // 20 padding
      });

      path.on('mouseout', function(d){
        tooltip.style('display', 'none');
        
        this.setAttribute('class', '');

        // De-highlight the info box
        var infoBox = legendPanel.find('[data-label=\"'+d.data.label+'\"]');
        infoBox.css('background-color', 'transparent');
      });

      path.on('mousemove', function(){
        var scrollTop = $(window).scrollTop();

        tooltip.style('top', (d3.event.pageY - scrollTop + 10) + 'px')
          .style('left', (d3.event.pageX + 10) + 'px');
      });

      // Bar chart section

      // Setup bar chart container
      var charts = $('#charts');
      charts.append('<div id=\"bar-chart\"><span title=\"Close Bar Chart\" class=\"close ui-icon ui-icon-closethick\"></span></div>');
  
      // General config
      var periods = ".json_encode($periodTable).";

      var xLabels = ".json_encode($xLabels).";
      var xInnerLabels = ".json_encode($xInnerLabels).";

      // Define margin and container width, height
      var margin = {top: 5, right: 0, bottom: 20, left: 15},
          width = 1300 - margin.left - margin.right,
          height = 360 - margin.bottom - margin.top;
      
      // Map matrix to its label and value
      periods.forEach(function(d, i){
        var term = xLabels[i];
        var labels = [];
        d.periods = [];

        // Form chart data array
        for(var k in d){
          if(typeof d[k]  === 'number'){
            labels.push(k);
            d.periods.push({term: term, label: k, count: d[k]});
          }
        }

        // Add the program name labels, to be mapped with xInnerLabels (utilizes maximum space)
        d.periods.forEach(function(arr){
          arr.labels = labels;
        });
      });

      // x axis scale
      var x = d3.scale.ordinal()
        .domain(xLabels)
        .rangeRoundBands([0, width], 0.1);

      // inner x axis scale
      var xInner = d3.scale.ordinal()
        .rangeRoundBands([0, x.rangeBand()]);

      // y axis scale
      var y = d3.scale.linear()
        .domain([0, d3.max(periods, function(d){ return d3.max(d.periods, function(d){ return d.count; }); })])
        .range([height, 0]);

      // Tooltips
      var tooltip2 = d3.select('#bar-chart')
        .append('div')
        .attr('class', 'tooltip')
        .style('position', 'fixed');
      tooltip2.append('div')
        .attr('class', 'label');
      tooltip2.append('span')
        .attr('class', 'program');
      tooltip2.append('span')
        .attr('class', 'count');
      tooltip2.append('div')
        .attr('class', 'percent');

      var color2 = d3.scale.category20b();

      var svg2 = d3.select('#bar-chart')
        .append('svg')
        .attr('width', width + margin.left + margin.right)
        .attr('height', height + margin.bottom + margin.top)
        .attr('class', 'program chart');

      // Each group is a term (e.g. Fall 2012, Winter 2012, etc..)
      var groups = svg2.selectAll('.group')
        .data(periods)
        .enter()
        .append('g')
        .attr('transform', function(d, i) { return 'translate('+ (x(xLabels[i]) + margin.left)  +', '+ margin.top +')'; });

      // Append bars
      groups.selectAll('rect')
        .data(function(d){ return d.periods; })
        .enter()
        .append('rect')
        .attr('width', function(d){ xInner.domain(d.labels); return xInner.rangeBand(); })
        .attr('x', function(d) { xInner.domain(d.labels); return xInner(d.label); })
        .attr('y', function(d) { return y(d.count); })
        .attr('height', function(d) { return height - y(d.count); })
        .attr('fill', function(d) { return color2(d.label); });

      // Append the count to the bar
      groups.selectAll('text')
        .data(function(d){ return d.periods; })
        .enter()
        .append('text')
        .attr('x', function(d) { xInner.domain(d.labels); return xInner(d.label) + (xInner.rangeBand() / 2); })
        .attr('y', function(d) { return y(d.count) + 3; })
        .attr('dy', '.70em')
        .text(function(d) { return d.count; });

      // x-axis
      var xAxis = d3.svg.axis()
        .scale(x)
        .orient('bottom');

      svg2.append('g')
        .attr('class', 'x axis')
        .attr('transform', 'translate('+ margin.left +','+ (height + margin.top) +')')
        .call(xAxis);

      // y-axis
      var yAxis = d3.svg.axis()
        .scale(y)
        .orient('left')
        .tickFormat(d3.format('d'))
        .tickSize(3);

      svg2.append('g')
        .attr('class', 'y axis')
        .attr('transform', 'translate('+ margin.left +', '+ margin.top +')')
        .attr('height', height)
        .call(yAxis);

      // Create legends
      var legendPeriods = periods.map(function(d){
        d = d.periods;

        return d.map(function(bar){
          var percent = Math.round(1000 * bar.count / total) / 10;
          return (bar.count > 0) ? 
            '<div data-term=\"'+bar.term+'\" data-label=\"'+bar.label+'\" style=\"border-bottom:1px solid #D9D9D9;\">'+
              '<h3>'+bar.term+'</h3>'+
              '<h3>'+bar.label+': <span class=\"normal-text\">'+bar.count+'</span></h3>'+
              '<h3>Percent: <span class=\"normal-text\">'+percent+'%</span></h3>'+
            '</div>'
            :
            '';
        }).join('');
      }).join('');
    
      // Set bar chart events
      var rects = svg2.selectAll('rect');

      rects.on('mouseover', function(d){
        // Highlight the bar
        this.setAttribute('class', 'highlight');

        // Highlight the info box
        var rgba = convertHexToRGBA(this.getAttribute('fill'), 0.8);
        var infoBox = legendPanel.find('[data-term=\"'+d.term+'\"][data-label=\"'+d.label+'\"]');

        // Set background color
        infoBox.css('background-color', rgba);

        var percent = Math.round(1000 * d.count / total) / 10;

        tooltip2.select('.label').html(d.term);
        tooltip2.select('.program').html(d.label + ': ');
        tooltip2.select('.count').html(d.count);
        tooltip2.select('.percent').html(percent + '%');
        tooltip2.style('display', 'block');

        // Reset scroll
        legendPanel.scrollTop(0);
        // Set scroll position
        legendPanel.scrollTop( infoBox.position().top - 20 ); // 20 padding
      });

      rects.on('mouseout', function(d){
        tooltip2.style('display', 'none');
        
        this.setAttribute('class', '');

        // De-highlight the info box
        var infoBox = legendPanel.find('[data-label=\"'+d.label+'\"]');
        infoBox.css('background-color', 'transparent');
      });
      
      rects.on('mousemove', function(){
        var scrollTop = $(window).scrollTop();

        tooltip2.style('top', (d3.event.pageY - scrollTop + 10) + 'px')
          .style('left', (d3.event.pageX + 10) + 'px');
      });

      // Toggle charts and legends
      var barChart = $('#bar-chart');
      var basicInput = $('#panel_3 > form > input[value=\"basic\"]');
      
      $('#panel_3 > form > input').click(function(){
        var input = $(this);
        
        if(input.val() === 'basic'){
          legendPanel.html(legendBasic);
        } else if(input.val() === 'program'){
          // Turn on bar chart
          barChart.css('display', 'block');
          input.attr('checked', false);
          
          legendPanel.html(legendPeriods);
        }
      });

      // Handle bar chart close event
      barChart.find('.close').click(function(){
        // Reset radio button and legend back to basic mode
        basicInput.attr('checked', 'checked');
        basicInput.trigger('click');

        // Turn off bar chart
        barChart.css('display', 'none');
      });
    })(jQuery);
  </script>";
?>