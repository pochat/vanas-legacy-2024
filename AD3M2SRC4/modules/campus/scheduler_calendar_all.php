<?php
	
require '../../lib/general.inc.php';


#Generamosel Query que pintara el calendario.
function QueryCalendar(){
    require("scheduler_calendar.php");
}
$fe_inicio=$_POST['fe_inicio'];
#Obtenemos fecha actual :
$Query = "Select CURDATE() ";
$row = RecuperaValor($Query);
$fe_actual = str_texto($row[0]);
$fe_actual=strtotime('0 day',strtotime($fe_actual));
$fe_actual= date('Y-m-d',$fe_actual);

$fe_inicio_ciclo="2021-04-05";

if(!empty($fe_inicio)&&($fe_inicio<>'undefined')){

    $fe_ini=strtotime('0 days',strtotime($fe_inicio));
    $fe_inicio_ciclo= date('Y-m-d',$fe_ini);	

}

//if($fe_actual>$fe_inicio_ciclo){
//    $fe_inicio_ciclo=$fe_actual;
//}




?>


<!-- new widget -->
<div class="jarviswidget jarviswidget-color-blueDark">
	<header style="background: #FAFAFA !important;color: #030303 !important;border-color: #fff !important;">
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
            <div id="scheduler_save_initial_event"></div>
            <div id="send_event"></div>
            <div id="scheduler_event_change_horario"></div>
            <div id="scheduler_new_event_modal"></div>
			<!-- end content -->
		</div>

	</div>
	<!-- end widget div -->
</div>
<!-- end widget -->

<script>

       $(document).ready(function () {

		        pageSetUp();


		        "use strict";

		        var date = new Date();
		        var d = date.getDate();
		        var m = date.getMonth();
		        var y = date.getFullYear();

		        var initDrag = function (e) {
		            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
		            // it doesn't need to have a start or end
		            //alert('gola');
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
		                cursor: 'pointer',
		                zIndex: 9999,
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

		        $('.external-events > li').each(function () {
		            initDrag($(this));
		        });

		        /*$('#add-event').click(function () {
		           
		            var title = $('#title').val(),
			            priority = $('input:radio[name=priority]:checked').val(),
			            description = $('#description').val(),
			            icon = $('input:radio[name=iconselect]:checked').val();

		               addEvent(title, priority, description, icon);
		        });
                */
		        /* initialize the calendar
				 -----------------------------------------------------------------*/
		        var events = <?php QueryCalendar(); ?>;
		        var calendar = $('#calendar').fullCalendar({
		            timeZone: 'America/Vancouver',
		            defaultDate: '<?php echo $fe_inicio_ciclo;?>', //Y-m-d Falta script que indique que periodo sigue para que sea fecha default.
		            editable: true,
		            droppable: true, // this allows things to be dropped onto the calendar !!!
		            //selectHelper: true,
		            //selectable: false,
		            //unselectAuto: false,
		            //disableResizing: false,
		            //defaultView: 'timeGridWeek',
		            header: {
		                left: 'title', //,today
		                center: 'prev, next, today',
		                right: 'month, agendaWeek, agenDay' //month, agendaDay,
		            },

		            events:events.event, 
		            //[{
		            /*    title: 'All Day Event',
		                start: new Date(y, m, 1),
		                description: 'long description',
		                className: ["event", "bg-color-greenLight"],
		                icon: 'fa-check'
		            }, {
		                title: 'Smartadmin Open Day',
		                start: new Date(y, m, 28),
		                end: new Date(y, m, 29),
		                className: ["event", "bg-color-darken"]
		            */
		            //}],

		            drop: function (date, allDay) { // this function is called when something is dropped	
		                
		                // retrieve the dropped element's stored Event Object
		                var originalEventObject = $(this).data('eventObject');

		                // we need to copy it, so that multiple events don't have a reference to the same object
		                var copiedEventObject = $.extend({}, originalEventObject);

		                // assign it the date that was reported
		                copiedEventObject.start = date;
		                copiedEventObject.end=(date + (30 * 60 * 1000)+(30 * 60 * 1000));
		                //copiedEventObject.allDay = allDay;

		                // render the event on the calendar
		                // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
		                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

		                // is the "remove after drop" checkbox checked?
		                if ($('#drop-remove').is(':checked')) {
		                    // if so, remove the element from the "Draggable Events" list
		                    $(this).remove();
		                }

		                var fe_inicio=date.format();
		                var fl_term=originalEventObject.description;
		                var fl_programa_users=originalEventObject.icon;
		                var defaultDuration = moment.duration($('#calendar').fullCalendar('option', 'defaultTimedEventDuration'));
		                var end = date.clone().add(defaultDuration); // 
		                var fe_final= end.format();
                        
		                //descomponemos el dato fl_programa_users para saber que tipo clase es:
		                var myarr = fl_programa_users.split("#");
		                var fg_tipo_clase=myarr[5];
                        
		                if(fg_tipo_clase=='single_term'){

		                    $.ajax({
		                        type: 'POST',
		                        url: 'scheduler_event_modal.php',
		                        data: 'fl_term='+fl_term+
                                      '&fl_programa_users='+fl_programa_users+
                                      '&fe_inicio='+fe_inicio+
                                      '&fe_final='+fe_final,
		                        async: false,
		                        success: function (html) {
		                            $('#evento_modal_edit').html(html);

		                        }
		                    });
		                }
		                if(fg_tipo_clase=='multiple_term'){
		                
		                    $.ajax({
		                        type: 'POST',
		                        url: 'scheduler_new_event_modal.php',
		                        data: 'fe_inicio='+fe_inicio+
		                              '&fl_term='+fl_term,
		                        async: false,
		                        success: function (html) {
		                            $('#scheduler_new_event_modal').html(html);

		                        }
		                    });
		                
		                }

                        /*
		                //Se manda por ajax la informacion para despues recuperarl den modal.
		                  $.ajax({
                              type: 'POST',
                              url: 'scheduler_save_initial_event.php',
                              data: 'fe_inicio='+fe_inicio+
                                    '&fl_term='+fl_term+
                                    '&data='+data,
                              async: false,
                              success: function (html) {
                                  $('#scheduler_save_initial_event').html(html);
  
                              }
                          }); 
                        */  
		                $('#calendar').fullCalendar({ events: events.event,    });
		            },

		            //Para actualizar el evento, moverlo de horario
		            eventDrop: function(event, delta, revertFunc) {
		                
		                var id=event.id;
		                var fe_inicio=event.start.format();
		                var fe_final=event.end.format();	

		                $.ajax({
		                    type: 'POST',
		                    url: 'scheduler_event_change_horario.php',
		                    data: 'id='+id+
                                  '&fe_inicio='+fe_inicio+
                                  '&fe_final='+fe_final,
		                    async: false,
		                    success: function (html) {
		                        $('#scheduler_event_change_horario').html(html);

		                    }
		                });
                       
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
		            eventClick: function (event) {	
		                var id=event.id;
		                var fl_term=event.description;
		                var fl_programa_users=event.icon;
		                var fe_inicio=event.start.format();
		                var fe_final=event.end.format();		                
                        
		                //descomponemos el dato fl_programa_users para saber que tipo clase es:
		                var myarr = fl_programa_users.split("#");
		                var fg_tipo_clase=myarr[5];
                        
		                if(fg_tipo_clase=='single_term'){
                            //groups simple term

		                    $.ajax({
		                        type: 'POST',
		                        url: 'scheduler_event_modal.php',
		                        data: 'fl_term='+fl_term+
                                      '&id='+id+
                                      '&fl_programa_users='+fl_programa_users+
                                      '&fe_inicio='+fe_inicio+
                                      '&fe_final='+fe_final,
		                        async: false,
		                        success: function (html) {
		                            $('#evento_modal_edit').html(html);

		                        }
		                    });
		                }else{
		                   //groups multiples terms
		                    var id_evento=""+event.id+"";
		                    $.ajax({
		                        type: 'POST',
		                        url:  'scheduler_new_event_modal.php',
		                        data: 'id_evento='+id_evento+
                                      '&fl_term='+fl_term+
                                      '&fl_programa_users='+fl_programa_users+
                                      '&fe_inicio='+fe_inicio+
                                      '&fe_final='+fe_final,
		                        async: false,
		                        success: function (html) {
		                            $('#scheduler_new_event_modal').html(html);

		                        }
		                    });
		                
		              
		                
		                }
		               // $(event.target).css('background-color','yellow');

		               
		            },
		            dayClick: function(date, jsEvent, view) {
		                  
		                var date=date.format();

		                    $.ajax({
		                        type: 'POST',
		                        url: 'scheduler_new_event_modal.php',
		                        data: 'fe_inicio='+date,
		                        async: false,
		                        success: function (html) {
		                            $('#scheduler_new_event_modal').html(html);

		                        }
		                    });

		            },
		            eventRender: function (event, element, icon) {
		                if (!event.description == "") {
		                    element.find('.fc-title').append("<br/><span class='ultra-light'>" + event.description +
			                    "</span>");
		                }
		                if (!event.icon == "") {
		                    element.find('.fc-title').append("<i class='air air-top-right fa " + event.icon +
			                    " '></i>");
		                }
		            },

		            windowResize: function (event, ui) {
		                $('#calendar').fullCalendar('render');
		            }
		        });

           /* hide default buttons */
		        $('.fc-header-right, .fc-header-center').hide();
		        $('.fc-right, .fc-center').hide();


           // calendar prev
		        $('#calendar-buttons #btn-prev').click(function () {
		            $('.fc-button-prev').click();
		            return false;
		        });

           // calendar next
		        $('#calendar-buttons #btn-next').click(function () {
		            $('.fc-button-next').click();
		            return false;
		        });

		        $('#calendar-buttons #btn-today').click(function () {
		            $('.fc-today-button').click();
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

           //Se coloca la vista de semana por default.
		        $('#calendar').fullCalendar('changeView', 'agendaWeek');

       });

</script>

