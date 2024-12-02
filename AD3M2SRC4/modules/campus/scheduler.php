<?php
# La libreria de funciones
require '../../lib/general.inc.php';

PresentaHeader();
PresentaEncabezado(231);

?>
<style>
    #external-events > li > :first-child:after {
        color: #fff;
        color: rgb(0,0,0 );
    }
</style>
<div class="row">
    <div class="col-md-12">
        <table cellspacing="2">
            <tr>
               
                <td style="background:#2270b3;height:20px;width:50px;"></td>
                <td>&nbsp;&nbsp;&nbsp;Created from Groups and Schedules</td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="background:#a90329; height:20px;width:50px"></td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;Incomplete</td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="background:rgb(5, 33, 202); height:20px;width:50px"></td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;Pending to be published</td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="background:rgb(18, 105, 12); height:20px;width:50px"></td>
                <td style="margin-left:10px">&nbsp;&nbsp;&nbsp;Published</td>
            </tr>
          
        </table>
    </div>
</div>
<br />


<div class="row">
    <div class="col-md-9" id="muestra_calendar">       
						

    </div>
    <div class="col-md-3">
            <div id="fetch_student"> </div>
            <p class="text-center"><a class="btn btn-primary" style="border-radius: 10px;" href="javascript:Fetch_Students();"><i class="fa fa-user"></i> Fetch Students</a></p>
            <p class="text-center"><a class="btn btn-success" style="border-radius: 10px;" href="javascript:Preview();"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-check-circle-o"></i> Publish&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>

            <div class="panel-group smart-accordion-default" id="accordion-2">
            </div>
            <div class="panel-group smart-accordion-default" id="accordion-3">
            </div>


    </div>
</div>

<!---muestra ele evnto para editar viene de un Ajax--->
<div id="evento_modal_edit"></div>
<div id="scheduler_preview"></div>



<script src='../../bootstrap/plugins/fullcalendar-3.4.0/lib/moment.min.js'></script>
<script>
	// funcion que muestra el calendario solamente con eventos de la BD!
	function MuestraCalendar(fe_inicio){
		    
	    var fe_inicio = fe_inicio;
	    
		$.ajax({
		    type: 'POST',
		    url: 'scheduler_calendar_all.php',
		    data: 'fe_inicio=' + fe_inicio,
		    async: false,
		    success: function (html) {
		        $('#muestra_calendar').html(html);

		    }
		});		    
		    
	}
    //Funcion que hace muestra el modal para selccion de usuarios 
	function Fetch_Students(){

		$.ajax({
		    type: 'POST',
		    url: 'scheduler_modal_fetch_student.php',
		    data: '',
		    async: false,
		    success: function (html) {
		        $('#fetch_student').html(html);

		    }
		});
	}
    //Funcion que muestra modal para ver preview de horarios.
	function Preview(){
		    
		$.ajax({
		    type: 'POST',
		    url: 'scheduler_preview.php',
		    data: '',
		    async: false,
		    success: function (html) {
		        $('#scheduler_preview').html(html);
		    }
		});
                
	}
	MuestraCalendar();

//presnta lista de programas y terms
/* function ViewTermsPrograms() {

	$.ajax({
		type: 'POST',
		url: 'scheduler_relations.php',
		data: '',
		async: false,
		success: function (html) {
		    $('#accordion-2').html(html);

		}
	});
}
*/
//se inicializa inmediatanmete para mostrar el listado.
//  ViewTermsPrograms();
</script>

<?php
# Pie de Pagina
PresentaFooter( );
?>
<script src="../../bootstrap/js/plugin/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>