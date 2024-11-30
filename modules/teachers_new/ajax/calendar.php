<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  $fl_alumno = $fl_usuario;

  # Calendar events
  function EventsQuery($fl_alumno){
		require("calendar_events.php");
	}

?>

<!-- Calendar -->
<div class="row">
	<div class="col-xs-12">
		<div class="well well-light no-margin padding-10">
			<div class="jarviswidget no-margin">
				<div class="no-margin">
					<div class="widget-body no-padding">
						<div class="widget-body-toolbar">
							<div id="calendar-buttons">
								<div class="btn-group">
									<a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-prev"><i class="fa fa-chevron-left"></i></a>
									<a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-next"><i class="fa fa-chevron-right"></i></a>
								</div>
							</div>
						</div>
						<div id="calendar"></div> 
					</div>
				</div>
			</div>	
		</div>
	</div>

	<!-- This is the event creation widget, might be used in the future -->
	<!-- <div class="col-sm-3 col-md-3 col-lg-3">
		
		<div class="jarviswidget jarviswidget-color-blueDark" data-widget-fullscreenbutton="false" data-widget-editbutton="false" data-widget-deletebutton="false">
			<header>
				<h2> Add Events </h2>
			</header>
			
			<div>
				<div class="widget-body">
					
					<form id="add-event-form">
						<fieldset>
							<div class="form-group">
								<label>Select Event Icon</label>
								<div class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default active">
										<input type="radio" name="iconselect" id="icon-1" value="fa-info" checked>
										<i class="fa fa-info text-muted"></i> </label>
									<label class="btn btn-default">
										<input type="radio" name="iconselect" id="icon-2" value="fa-warning">
										<i class="fa fa-warning text-muted"></i> </label>
									<label class="btn btn-default">
										<input type="radio" name="iconselect" id="icon-3" value="fa-check">
										<i class="fa fa-check text-muted"></i> </label>
									<label class="btn btn-default">
										<input type="radio" name="iconselect" id="icon-4" value="fa-user">
										<i class="fa fa-user text-muted"></i> </label>
									<label class="btn btn-default">
										<input type="radio" name="iconselect" id="icon-5" value="fa-lock">
										<i class="fa fa-lock text-muted"></i> </label>
									<label class="btn btn-default">
										<input type="radio" name="iconselect" id="icon-6" value="fa-clock-o">
										<i class="fa fa-clock-o text-muted"></i> </label>
								</div>
							</div>
							<div class="form-group">
								<label>Event Title</label>
								<input class="form-control"  id="title" name="title" maxlength="40" type="text" placeholder="Event Title">
							</div>
							<div class="form-group">
								<label>Event Description</label>
								<textarea class="form-control" placeholder="Please be brief" rows="3" maxlength="40" id="description"></textarea>
								<p class="note">Maxlength is set to 40 characters</p>
							</div>
							<div class="form-group">
								<label>Select Event Color</label>
								<div class="btn-group btn-group-justified btn-select-tick" data-toggle="buttons">
									<label class="btn bg-color-darken active">
										<input type="radio" name="priority" id="option1" value="bg-color-darken txt-color-white" checked>
										<i class="fa fa-check txt-color-white"></i> </label>
									<label class="btn bg-color-blue">
										<input type="radio" name="priority" id="option2" value="bg-color-blue txt-color-white">
										<i class="fa fa-check txt-color-white"></i> </label>
									<label class="btn bg-color-orange">
										<input type="radio" name="priority" id="option3" value="bg-color-orange txt-color-white">
										<i class="fa fa-check txt-color-white"></i> </label>
									<label class="btn bg-color-greenLight">
										<input type="radio" name="priority" id="option4" value="bg-color-greenLight txt-color-white">
										<i class="fa fa-check txt-color-white"></i> </label>
									<label class="btn bg-color-blueLight">
										<input type="radio" name="priority" id="option5" value="bg-color-blueLight txt-color-white">
										<i class="fa fa-check txt-color-white"></i> </label>
									<label class="btn bg-color-red">
										<input type="radio" name="priority" id="option6" value="bg-color-red txt-color-white">
										<i class="fa fa-check txt-color-white"></i> </label>
								</div>
							</div>

						</fieldset>
						<div class="form-actions">
							<div class="row">
								<div class="col-md-12">
									<button class="btn btn-default" type="button" id="add-event" >
										Add Event
									</button>
								</div>
							</div>
						</div>
					</form>
					
					<div class="well well-sm" id="event-container">
						<form>
							<legend>Draggable Events</legend>
							<ul id='external-events' class="list-unstyled">
								<li>
									<span class="bg-color-darken txt-color-white" data-description="Currently busy" data-icon="fa-time">Office Meeting</span>
								</li>
								<li>
									<span class="bg-color-blue txt-color-white" data-description="No Description" data-icon="fa-pie">Lunch Break</span>
								</li>
								<li>
									<span class="bg-color-red txt-color-white" data-description="Urgent Tasks" data-icon="fa-alert">URGENT</span>
								</li>
							</ul>
							<div class="checkbox">
								<label>
									<input type="checkbox" id="drop-remove" class="checkbox style-0" checked="checked">
									<span>remove after drop</span> 
								</label>
							</div>
						</form>
					</div>
				</div>
			</div>
			
		</div>

	</div> -->
</div><!-- End Calendar -->

<script type="text/javascript">
	loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/fullcalendar/jquery.fullcalendar.min.js", initCalendar);

	function initCalendar() {
		"use strict";
	
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    var hdr = {
      left: 'title',
      center: 'month,agendaWeek,agendaDay',
      right: 'prev,today,next'
    };
		var initDrag = function (e) {
      // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
      // it doesn't need to have a start or end

      var eventObject = {
          title: $.trim(e.children().text()), // use the element's text as the event title
          description: $.trim(e.children('span').attr('data-description')),
          icon: $.trim(e.children('span').attr('data-icon')),
          className: $.trim(e.children('span').attr('class')) // use the element's children as the event class
      };
      // store the Event Object in the DOM element so we can get to it later
      e.data('eventObject', eventObject);

      // make the event draggable using jQuery UI
      e.draggable({
        zIndex: 999,
        revert: true, // will cause the event to go back to its
        revertDuration: 0 //  original position after the drag
      });
  	};

    var addEvent = function (title, priority, description, icon) {
      title = title.length === 0 ? "Untitled Event" : title;
      description = description.length === 0 ? "No Description" : description;
      icon = icon.length === 0 ? " " : icon;
      priority = priority.length === 0 ? "label label-default" : priority;

      var html = $('<li><span class="' + priority + '" data-description="' + description + '" data-icon="' +
        icon + '">' + title + '</span></li>').prependTo('ul#external-events').hide().fadeIn();

      $("#event-container").effect("highlight", 800);

      initDrag(html);
    };

	  // initialize the external events
	  $('#external-events > li').each(function () {
	      initDrag($(this));
	  });

	  $('#add-event').click(function () {
      var title = $('#title').val(),
        priority = $('input:radio[name=priority]:checked').val(),
        description = $('#description').val(),
        icon = $('input:radio[name=iconselect]:checked').val();
      	addEvent(title, priority, description, icon);
	  });

	  // Retrieve events of the student
	  var events = <?php EventsQuery($fl_alumno); ?>;
	  //console.log("events: "+ JSON.stringify(events.event));
	  //console.log("date: "+JSON.stringify(events.debug));

	  // initialize the calendar
	  $('#calendar').fullCalendar({
	  	header: hdr,
    	buttonText: {
    		prev: '<i class="fa fa-chevron-left"></i>',
    		next: '<i class="fa fa-chevron-right"></i>'
    	},

      editable: false,
      droppable: false, // this allows things to be dropped onto the calendar !!!

      drop: function (date, allDay) { // this function is called when something is dropped

        // retrieve the dropped element's stored Event Object
        var originalEventObject = $(this).data('eventObject');

        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);

        // assign it the date that was reported
        copiedEventObject.start = date;
        copiedEventObject.allDay = allDay;

        // render the event on the calendar
        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

        // is the "remove after drop" checkbox checked?
        if ($('#drop-remove').is(':checked')) {
          // if so, remove the element from the "Draggable Events" list
          $(this).remove();
        }

      },
      select: function (start, end, allDay) {
        var title = prompt('Event Title:');
        if (title) {
          calendar.fullCalendar('renderEvent', 
          	{
              title: title,
              start: start,
              end: end,
              allDay: allDay
            }, true // make the event "stick"
          );
        }
        calendar.fullCalendar('unselect');
      },

	    events: events.event,

      eventRender: function (event, element, icon) {
        if (!event.description == "") {
          element.find('.fc-event-title').append("<br/><span class='ultra-light'>" + event.description + "</span>");
        }
        if (!event.icon == "") {
          element.find('.fc-event-title').append("<i class='air air-top-right fa " + event.icon + " '></i>");
        }
      },

      windowResize: function (event, ui) {
      	$('#calendar').fullCalendar('render');
      }
	  });

	  // hide default buttons 
    $('.fc-header-right, .fc-header-center').hide();
	}

	$('#calendar-buttons #btn-prev').click(function () {
	    $('.fc-button-prev').click();
	    return false;
	});
	$('#calendar-buttons #btn-next').click(function () {
	    $('.fc-button-next').click();
	    return false;
	});
	$('#calendar-buttons #btn-today').click(function () {
	    $('.fc-button-today').click();
	    return false;
	});
</script>