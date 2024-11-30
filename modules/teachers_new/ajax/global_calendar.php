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

 

  # Calendar events
  function EventsQuery( ){
		require("global_calendar_events.php");
	}

?>

 
<style>
.fc-view {
overflow: auto !important;
}
</style>

<div class="row"><div class="col-md-12">


<!-- new widget -->
		<div class="jarviswidget jarviswidget-color-blueDark">

			
			<header style="background: #FAFAFA !important;color: #4C4F53 !important;border-color:#C3C6CB !important">
				<span class="widget-icon"> <i class="fa fa-calendar"></i> </span>
				<h2> My Calendar </h2>
				<div class="widget-toolbar">
					<!-- add: non-hidden - to disable auto hide -->
					<div class="btn-group">
						<button class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown">
							Showing <i class="fa fa-caret-down"></i>
						</button>
						<ul class="dropdown-menu js-status-update pull-right">
							<li>
								<a href="javascript:void(0);" id="mt">Month</a>
							</li>
							<li>
								<a href="javascript:void(0);" id="ag">Week</a>
							</li>
							<li>
								<a href="javascript:void(0);" id="td">Today</a>
							</li>
						</ul>
					</div>
				</div>
			</header>

			<!-- widget div-->
			<div>

				<div class="widget-body no-padding">
					<!-- content goes here -->
					<div class="widget-body-toolbar">

						<div id="calendar-buttons">

							<div class="btn-group">
								<a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-prev"><i class="fa fa-chevron-left"></i></a>
								<a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-next"><i class="fa fa-chevron-right"></i></a>
							</div>
						</div>
					</div>
					<div id="calendar"></div>
					<div id="send_event"></div>
                   
					<!-- end content -->
				</div>

			</div>
			<!-- end widget div -->
		</div>
		<!-- end widget -->
    </div>
</div>


<script type="text/javascript">

	// PAGE RELATED SCRIPTS
	
	/*
	 * FULL CALENDAR JS
	 */
	loadScript("<?php echo PATH_N_COM_JS; ?>/plugin/fullcalendar/jquery.fullcalendar.min.js", iniCalendar);
	
	function iniCalendar() {
	    "use strict";
	    var events = <?php EventsQuery(); ?>;
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
	
	    /* initialize the external events
		 -----------------------------------------------------------------*/
	
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
	
	    /* initialize the calendar
		 -----------------------------------------------------------------*/
	
	    $('#calendar').fullCalendar({
	       
	        header: hdr,
	        buttonText: {
	            prev: '<i class="fa fa-chevron-left"></i>',
	            next: '<i class="fa fa-chevron-right"></i>'
	        },
	        defaultView: 'agendaWeek',
	        editable: false,
	        droppable: false, //
	
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
	                calendar.fullCalendar('renderEvent', {
	                        title: title,
	                        start: start,
	                        end: end,
	                        allDay: allDay
	                    }, true // make the event "stick"
	                );
	            }
	            calendar.fullCalendar('unselect');
	        },
	
	       events: events.event,//pinta todos los eventos que hay en calendar_events.php
	
	        eventRender: function (event, element, icon) {
	            if (!event.description == "") {
	                element.find('.fc-event-title').append("<br/><span class='ultra-light'>" + event.description +
	                    "</span>");
	            }
	            if (!event.icon == "") {
	                element.find('.fc-event-title').append("<i class='air air-top-right fa " + event.icon +
	                    " '></i>");
	            }
	        },
			
			 eventClick: function(event) {
                    // opens events in a popup window
                    var id=event.id;
					var fg_teachers=1;
                    $.ajax({
                        type: 'POST',
                        url: '../../../AD3M2SRC4/modules/campus/view_calendar_event_modal.php',
                        data: 'id='+id+
						      '&fg_teachers='+fg_teachers,
                        async: false,
                        success: function (html) {
                            $('#send_event').html(html);



                        }
                    });
					

                   //alert(id);
                },

			
			
			
	
	        windowResize: function (event, ui) {
	            $('#calendar').fullCalendar('render');
	        }
	    });
	
	    /* hide default buttons */
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
	
	$('#mt').click(function () {
	    $('#calendar').fullCalendar('changeView', 'month');
	});
	
	$('#ag').click(function () {
	    $('#calendar').fullCalendar('changeView', 'agendaWeek');
	});
	
	$('#td').click(function () {
	    $('#calendar').fullCalendar('changeView', 'agendaDay');
	});

    
	

</script>