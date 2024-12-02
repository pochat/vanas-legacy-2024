<?php
	
require '../../lib/general.inc.php';

#Falta definidir una funcion que detecte el periodo a tarabajar.

$fl_periodo=ObtenConfiguracion(143);
$Query="SELECT nb_periodo FROM c_periodo WHERE fl_periodo=$fl_periodo ";
$row=RecuperaValor($Query);
$nb_periodo=$row['nb_periodo'];

?>
<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width: 85%;margin: 30px auto;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><b><i class="fa fa-users" aria-hidden="true"></i> Cycle: <?php echo $nb_periodo;?></b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="max-height:660px;overflow:auto;">


           <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#application" data-toggle="tab" onclick="MuestraData(1)"><i class="fa fa-fw fa-lg fa-file-text-o"></i>Application forms</a>
                    </li>
				     <li>
                        <a href="#student" data-toggle="tab" id="princing1" onclick="MuestraData(2)"><i class="fa fa-fw fa-lg fa-users"></i> Students</a>
                    </li>
           </ul>

          <div id="myTabContent1" class="tab-content padding-10 no-border">
               <div class="tab-pane fade " id="tab_application">
                   
                   <div  id="application"></div>
               </div>
               <div class="tab-pane fade" id="tab_student">
                  
                   <div id="student"></div>
               </div>
          </div>



      </div>
      <div class="modal-footer text-center">
        <div class="text-center"><a href="javascript:void(0);" class="btn btn-primary" style="border-radius: 10px;" onclick="AsignarStudentTerms();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-cog"></i> Fetch&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></div> 
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" <i class="fa fa-file-text-o" aria-hidden="true"></i> ASSIGN TO A TERM</button>-->
      </div>
    </div>
  </div>
</div>
					
<script>
    $(document).ready(function () {
        pageSetUp();
    });


    function MuestraData(fg_data) {

        /*limpiamos data para evitar errores de id's */
        $('#application').empty();
        $('#student').empty();

        /*Muestra tabla de usuarios.*/
        $.ajax({
            type: 'POST',
            url: 'scheduler_steps.php',
            data: 'fg_data=' + fg_data,
            async: false,
            success: function (html) {
                if(fg_data==1){
                    $('#application').html(html);
                    $('#tab_application').addClass('in active');
                    $('#tab_student').removeClass('in active');
                }
                if(fg_data == 2) {
                    $('#student').html(html);
                    $('#tab_student').addClass('in active');
                    $('#tab_application').removeClass('in active');
                }

            }
        });


    }
    $('#exampleModal').modal('show');
    MuestraData(1);
	
</script>	
