<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../lib/zoom_config.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  $clave=1;
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_GLOBALCALENDAR, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!isset($fg_error)) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      
    }
    else { // Alta, inicializa campos
     
    }
   
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
   
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_GLOBALCALENDAR);
  
 
  
  # Inicia forma de captura
  Forma_Inicia($clave, True);
  if(isset($fg_error))
    Forma_PresentaError( );
  
 
  
  
  #Generamosel Query que pintara el calendario.
  function QueryGlobalCalendar(){
      require("calendar_events_verifica_zoom.php");
  }
  
  
?>
 <br />
 <div class="row">
    <div class="col-md-12">
        <table cellspacing="2">
            <tr>
                <td style="background:#036f68; height:20px;width:50px"></td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;mario@vanas.ca</td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="background:#1c1280; height:20px;width:50px"></td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;info@vanas.ca</td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="background:#8a5e03; height:20px;width:50px"></td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;admin@vanas.ca</td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                
            </tr>
          
        </table>
    </div>
</div>
<br />


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
                    <div id="send_event"></div>
					<!-- end content -->
				</div>

			</div>
			<!-- end widget div -->
		</div>
		<!-- end widget -->
  

<script src='../../bootstrap/plugins/fullcalendar-3.4.0/lib/moment.min.js'></script>


<script type="text/javascript">


    $(document).ready(function () {

        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        pageSetUp();


        /*
         * FULL CALENDAR JS
         */

        if ($("#calendar").length) {
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            //Pintamos los eventos atraves de la funcion QueryGlobalCalendar
            var events = <?php QueryGlobalCalendar(); ?>;
            var calendar = $('#calendar').fullCalendar({

                editable: true,
                draggable: false,
                selectable: false,
                selectHelper: true,
                unselectAuto: false,
                disableResizing: false,

                header: {
                    left: 'title', //,today
                    center: 'prev, next, today',
                    right: 'month, agendaWeek, agenDay' //month, agendaDay,
                },

                select: function (start, end, allDay) {
                   
                    var title = prompt('Event Title:');
                    if (title) {
                        calendar.fullCalendar('renderEvent', {
                            title: title,
                            start: start,
                            end: end,
                            allDay: false
                        }, true // make the event "stick"
                        );
                    }
                    calendar.fullCalendar('unselect');
                },
                events: events.event,//pinta todos los eventos que hay en calendar_events.php

                //Para actualizar el evento, moverlo de horario
                eventDrop: function(event, delta, revertFunc) {

                       // alert(" was dropped on " + event.start.format());
                         //if (!confirm("Are you sure about this change?")) {
                         //  revertFunc();
                        // }
                        var fe_inicio=event.start.format();
                        var id=event.id;
                        //alert(id);
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: 'guardar_evento_calentario.php',
                            data: 'id='+id+
                                  '&fe_inicio='+fe_inicio,
                            //async: false,
                            success: function (data, textStatus) {
                                if (!data)
                                {
                                    revertFunc();
                                    return;
                                }
                                calendar.fullCalendar('updateEvent', event);
                               
                               //alert('si se actualizo');
                              
                               $.smallBox({
                                   title : "<?php echo ObtenEtiqueta(1781); ?>",
                                   content : "&nbsp;",//si se actuliaza
                                   color : "#5CAA2C",
                                   timeout: 18000,
                                   icon : "fa fa-check-circle swing animated"
                               });
                               
                            },
                            error: function() {
                                //aparece mensaje de que no se puede actulizar ya que existe un evento.
                             
                                //alert('No se puede actualizar!');
                                $.smallBox({
                                    title : "<?php echo ObtenEtiqueta(1780); ?>",
                                    content : "&nbsp;",//no se actulaiza
                                    color : "#EA6B6B",
                                    timeout: 18000,
                                    icon : "fa fa-times-circle swing animated"
                                });
                                
                               
                                revertFunc();

                              
                            }
                        });






                   
                 },

                eventClick: function(event) {
                    // opens events in a popup window
                    var id=event.id;
                    $.ajax({
                        type: 'POST',
                        url: 'view_calendar_event_modal.php',
                        data: 'id='+id
                               ,
                        async: false,
                        success: function (html) {
                            $('#send_event').html(html);



                        }
                    });


                  // alert(id);
                },




                eventRender: function (event, element, icon) {
                    if (!event.description == "") {
                        element.find('.fc-event-title').append("<br/><span class='ultra-light'>" + event.description + "</span>");
                    }
                    if (!event.icon == "") {
                        element.find('.fc-event-title').append("<i class='air air-top-right fa " + event.icon + " '></i>");
                    }
                }
            });

        };

        /* hide default buttons */
        $('.fc-header-right, .fc-header-center').hide();

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

        // calendar today
        $('#calendar-buttons #btn-today').click(function () {
            $('.fc-button-today').click();
            return false;
        });

        // calendar month
        $('#mt').click(function () {
            $('#calendar').fullCalendar('changeView', 'month');
        });

        // calendar agenda week
        $('#ag').click(function () {
            $('#calendar').fullCalendar('changeView', 'agendaWeek');
        });

        // calendar agenda day
        $('#td').click(function () {
            $('#calendar').fullCalendar('changeView', 'agendaDay');
           
        });

        //Se coloca la vista de semana por default.
         $('#calendar').fullCalendar('changeView', 'agendaWeek');





    });

		</script>

<?php  
  
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
      $fg_guardar = ValidaPermiso(FUNC_GLOBALCALENDAR, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>

