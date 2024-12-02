<?php
  # Genders - basic
  $Query2  = "SELECT CASE fg_gender WHEN 'M' THEN 'Male' WHEN 'F' THEN 'Female' END Gender, COUNT(1) ";
  $Query2 .= "FROM c_usuario a ";
  $Query2 .= "LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
  $Query2 .= "WHERE fl_usuario IN ($students) ";
  $Query2 .= "GROUP BY fg_gender ";
  $rs2 = EjecutaQuery($Query2);
  for($i=0; $row2=RecuperaRegistro($rs2); $i++){
    $fg_gender = $row2[0];
    $count = $row2[1];

    $genders_basic[$i] = (Object) array(
      "label" => $fg_gender,
      "count" => $count
    );
  }

  # Genders - by programs
  $Query2  = "SELECT nb_programa, CASE fg_gender WHEN 'M' THEN 'Male' WHEN 'F' THEN 'Female' END Gender, COUNT(1) ";
  $Query2 .= "FROM c_usuario a ";
  $Query2 .= "LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
  $Query2 .= "LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
  $Query2 .= "WHERE fl_usuario IN ($students) ";
  $Query2 .= "GROUP BY nb_programa, fg_gender ";
  $rs2 = EjecutaQuery($Query2);
  for($i=0; $row2=RecuperaRegistro($rs2); $i++){
    $nb_programa = $row2[0];
    $fg_gender = $row2[1];
    $count = $row2[2];

    $genders_programs[$i] = (Object) array(
      "program" => $nb_programa,
      "label" => $fg_gender,
      "count" => $count
    );
  }

  # Pie chart
  $html_arriba .= "
  <script type='text/javascript'>
    (function($){
      // General configs
      var basic = ".json_encode($genders_basic).";
      var programs = ".json_encode($genders_programs).";

      // Doesn't matter if we use basic or programs, both always have the same total
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
      var tooltip = d3.select('#panel_1')
        .append('div')
        .attr('class', 'tooltip');
      tooltip.append('div')
        .attr('class', 'program');
      tooltip.append('span')
        .attr('class', 'label');
      tooltip.append('span')
        .attr('class', 'count');
      tooltip.append('div')
        .attr('class', 'percent');

      // Legends
      var legendPanel = $('#legend_1_panel');
      var legendRectSize = 18;
      var legendSpacing = 4;

      // Pie chart - basic
      var svg = d3.select('#panel_1')
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

      // Pie chart - by program
      var svg2 = d3.select('#panel_1')
        .append('svg')
        .attr('width', width)
        .attr('height', height)
        .attr('class', 'program')
        .attr('style', 'display:none;')
        .append('g')
        .attr('transform', 'translate('+(width / 2)+','+(height / 2)+')');

      // Color scale
      var color2 = d3.scale.category20b();

      // Add pie slices
      var path2 = svg2.selectAll('path')
        .data(pie(programs))
        .enter()
        .append('path')
        .attr('d', arc)
        .attr('fill', function(d, i){
          return (typeof d.data.program !== 'undefined') ? color2(d.data.program) : color2(d.data.label);
        });

      // Create legends
      var legendPrograms = programs.map(function(d){ 
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

      // Set events
      var path2 = svg2.selectAll('path');

      path2.on('mouseover', function(d){
        // Display tooltip
        var percent = Math.round(1000 * d.data.count / total) / 10;
        tooltip.select('.program').html(d.data.program);
        tooltip.select('.label').html(d.data.label+': ');
        tooltip.select('.count').html(d.data.count);
        tooltip.select('.percent').html(percent + '%');
        tooltip.style('display', 'block');

        // Highlight the slice
        this.setAttribute('class', 'highlight');

        // Highlight the info box
        var rgba = convertHexToRGBA(this.getAttribute('fill'), 0.8);
        var infoBox = legendPanel.find('[data-program=\"'+d.data.program+'\"][data-label=\"'+d.data.label+'\"]');

        // Set background color
        infoBox.css('background-color', rgba);

        // Reset scroll
        legendPanel.scrollTop(0);
        // Set scroll position
        legendPanel.scrollTop( infoBox.position().top - 20 ); // 20 padding
      });

      path2.on('mouseout', function(d){
        tooltip.select('.program').html('');
        tooltip.style('display', 'none');

        this.setAttribute('class', '');

        // De-highlight the info box
        var infoBox = legendPanel.find('[data-program=\"'+d.data.program+'\"][data-label=\"'+d.data.label+'\"]');
        infoBox.css('background-color', 'transparent');
      });

      path2.on('mousemove', function(){
        var scrollTop = $(window).scrollTop();

        tooltip.style('top', (d3.event.pageY - scrollTop + 10) + 'px')
          .style('left', (d3.event.pageX + 10) + 'px');
      });

      // Toggle charts and legends
      $('#panel_1 > form > input').click(function(){
        var input = $(this);
        var svgBasic = $('#panel_1 > svg.basic');
        var svgProgram = $('#panel_1 > svg.program');

        if(input.val() === 'basic'){
          svgBasic.css('display', 'block');
          svgProgram.css('display', 'none');

          legendPanel.html(legendBasic);
        } else if(input.val() === 'program'){
          svgBasic.css('display', 'none');
          svgProgram.css('display', 'block');

          legendPanel.html(legendPrograms);
        }
      });
    })(jQuery);
  </script>";
?>