<?php
	
require '../../lib/general.inc.php';

#Obtenemos datos.
$fe_inicio="2021-04-05";
$fe_final="2021-07-05";

$fe_inicio=strtotime('0 days',strtotime($fe_inicio));
$fe_inicio= date('d-m-Y',$fe_inicio);

$fe_final=strtotime('0 days',strtotime($fe_final));
$fe_final= date('d-m-Y',$fe_final);





?>
<style>
    .checkbox input[type=checkbox].checkbox + span, .checkbox-inline input[type=checkbox].checkbox + span, .radio input[type=radio].radiobox + span, .radiobox-inline input[type=radio].radiobox + span {
        margin-left: 0px;
    }
    .checkbox input[type=checkbox], .checkbox-inline input[type=checkbox], .radio input[type=radio], .radio-inline input[type=radio] {
        margin-left: 0px;
    }
</style>
<!--------------modal para edicion de horarios y asignaciones terms y grupos. (clicka un evento del calendario)---------------->
<div class="modal fade" id="modal_scheduler_preview" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width:80%;margin:auto;margin-top:70px;">
    <div class="modal-content" id="evento_modal_edit">
      <div class="modal-header">
          <br />
          <div  class='smart-form hidden'>       
                    <div class="row">
                        <div class="col-md-3 text-right">
                             <label class=''><strong>Cycle </strong></label>
                        </div>
                        <div class="col-md-3">
                           
                                <?php echo"
	                                        <label class='input'><input type='text' name='fe_inicio' id='fe_inicio' maxlength='10' class='datepicker hasDatepicker' value='$fe_inicio' >" . Forma_Calendario("fe_inicio") . "</label>
                                        ";
                                ?>
                        </div>
                        <div class="col-md-3">
                                <?php echo"
	                                        <label class='input'><input type='text' name='fe_final' id='fe_final' maxlength='10' class='datepicker hasDatepicker' value='$fe_final'>" . Forma_Calendario("fe_final") . "</label>
                                        ";
                                ?> 
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" style="border-radius: 10px;" onclick="SearchData()"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </div>                           
          </div>

            <!--<h3 class="text-center"><b>From: <?php echo $fe_from;?>.&nbsp;&nbsp;&nbsp;&nbsp;   To: <?php echo $fe_to;?>.</b></h3>--->

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">     

          <div id="scheduler_search_preview" style="max-height:400px;overflow:auto;"></div>
          <div id="scheduler_publish"></div>

      </div>
      <div class="modal-footer">
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="ActionEvent();">Save changes</button>-->
   
        <p class="text-center"><a class="btn btn-success" id="publicar" style="border-radius: 10px;" onclick="Publish();"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-check-circle-o"></i> Publish&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>
        <p class="text-center hidden" id="img_process"><img src='../../../img/loading_stripe.gif' style='height:40px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Processing...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>

      </div>




    </div>
  </div>
</div>
<!-------------------------------------------------------------------------------------------->				
<script>
    $(document).ready(function () {
        pageSetUp();


    });

    $('#modal_scheduler_preview').modal('show');

    function SearchData(){

        var fe_inicio = document.getElementById('fe_inicio').value;
        var fe_final = document.getElementById('fe_final').value;

        //pasamos los valores por ajax para salvar datos.
        $.ajax({
            type: 'POST',
            url: 'scheduler_search_preview.php',
            data: 'fe_inicio=' + fe_inicio +
                  '&fe_final=' + fe_final,
            async: false,
            success: function (html) {
                $('#scheduler_search_preview').html(html);
            }
        });


    }
    SearchData();

    //boton publicar
    function Publish() {

        var tot_reg = document.getElementById('tot_sem').value;
        var fl_periodo = document.getElementById('fl_periodo').value;
        var seleccionados = 0, i = 1;

        $('#publicar').addClass('hidden');
        $('#img_process').removeClass('hidden');

        // Arreglo para identificar cuantod usuarios fueron seleccionados 
        var no_semanas = [];
        var allsemanas = "";
        for (i; i <= tot_reg; i++) {
            var reg = $("#ch_semana_" + i).is(':checked');
            var val = $("#ch_semana_" + i).val();
            var use_lic = $("#use_lic_semana_" + i).val();
            if (reg == true) {
                seleccionados++;
                // Solo contara a los check seleccionados
                if ((no_semanas.indexOf(val) < 0) && (use_lic == 1)) {
                    allsemanas += "'" + val + "',";
                    no_semanas.push(val);
                  
                    $('#label_no_'+val+'').addClass('hidden');
                    $('#label_process_'+val+'').removeClass('hidden');
                    //alert(val);
                   /* $('#label_no_'+val+'').addClass('hidden');
                    $('#label_process_'+val+'').removeClass('hidden');
                    
                   */
                    //alert('fua');
                    //Mandamos por ajax el resultado para despues recupera el estatus,
                    //pasamos los valores por ajax para salvar datos.                  
                  /*  $.ajax({
                        type: 'POST',
                        url: 'scheduler_publish.php',
                        data: 'fl_periodo=' + fl_periodo +
                              '&no_semana=' + val,
                        async: false,
                        success: function (html) {
                            $('#scheduler_publish').html(html);
                        }
                    });
                    */
                    
                }
                
            }
            
        }

        
        //Mandamos por ajax el resultado para despues recupera el estatus,
        //pasamos los valores por ajax para salvar datos.                  
        $.ajax({
            type: 'POST',
            url: 'scheduler_publish.php',
            data: 'fl_periodo=' + fl_periodo +
                  'seleccionados='+seleccionados+
                  '&no_semanas=' + no_semanas,
            async: false,
            }).done(function (result) {
                
                var result = JSON.parse(result);
                var no_semana = result.no_semana;
                var fg_termino = result.fg_termino;
                var no_exitos = result.no_exitosos;
                $('label_no_'+no_semana+'').addClass('hidden');
               

                $('#img_process').addClass('hidden');
                $('#publicar').removeClass('hidden');

                if (no_exitos == 1) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                }
                if (no_exitos == 2) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                }
                if (no_exitos == 3) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                    $('#label_process_3').addClass('hidden');
                    $('#label_yes_3').removeClass('hidden');
                }
                if (no_exitos == 4) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                    $('#label_process_3').addClass('hidden');
                    $('#label_yes_3').removeClass('hidden');
                    $('#label_process_4').addClass('hidden');
                    $('#label_yes_4').removeClass('hidden');
                }
                if (no_exitos == 5) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                    $('#label_process_3').addClass('hidden');
                    $('#label_yes_3').removeClass('hidden');
                    $('#label_process_4').addClass('hidden');
                    $('#label_yes_4').removeClass('hidden');
                    $('#label_process_5').addClass('hidden');
                    $('#label_yes_5').removeClass('hidden');
                }
                if (no_exitos == 6) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                    $('#label_process_3').addClass('hidden');
                    $('#label_yes_3').removeClass('hidden');
                    $('#label_process_4').addClass('hidden');
                    $('#label_yes_4').removeClass('hidden');
                    $('#label_process_5').addClass('hidden');
                    $('#label_yes_5').removeClass('hidden');
                    $('#label_process_6').addClass('hidden');
                    $('#label_yes_6').removeClass('hidden');
                }
                if (no_exitos == 7) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                    $('#label_process_3').addClass('hidden');
                    $('#label_yes_3').removeClass('hidden');
                    $('#label_process_4').addClass('hidden');
                    $('#label_yes_4').removeClass('hidden');
                    $('#label_process_5').addClass('hidden');
                    $('#label_yes_5').removeClass('hidden');
                    $('#label_process_6').addClass('hidden');
                    $('#label_yes_6').removeClass('hidden');
                    $('#label_process_7').addClass('hidden');
                    $('#label_yes_7').removeClass('hidden');
                }
                if (no_exitos == 8) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                    $('#label_process_3').addClass('hidden');
                    $('#label_yes_3').removeClass('hidden');
                    $('#label_process_4').addClass('hidden');
                    $('#label_yes_4').removeClass('hidden');
                    $('#label_process_5').addClass('hidden');
                    $('#label_yes_5').removeClass('hidden');
                    $('#label_process_6').addClass('hidden');
                    $('#label_yes_6').removeClass('hidden');
                    $('#label_process_7').addClass('hidden');
                    $('#label_yes_7').removeClass('hidden');
                    $('#label_process_8').addClass('hidden');
                    $('#label_yes_8').removeClass('hidden');
                }
                if (no_exitos == 9) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                    $('#label_process_3').addClass('hidden');
                    $('#label_yes_3').removeClass('hidden');
                    $('#label_process_4').addClass('hidden');
                    $('#label_yes_4').removeClass('hidden');
                    $('#label_process_5').addClass('hidden');
                    $('#label_yes_5').removeClass('hidden');
                    $('#label_process_6').addClass('hidden');
                    $('#label_yes_6').removeClass('hidden');
                    $('#label_process_7').addClass('hidden');
                    $('#label_yes_7').removeClass('hidden');
                    $('#label_process_8').addClass('hidden');
                    $('#label_yes_8').removeClass('hidden');
                    $('#label_process_9').addClass('hidden');
                    $('#label_yes_9').removeClass('hidden');
                }
                if (no_exitos == 10) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                    $('#label_process_3').addClass('hidden');
                    $('#label_yes_3').removeClass('hidden');
                    $('#label_process_4').addClass('hidden');
                    $('#label_yes_4').removeClass('hidden');
                    $('#label_process_5').addClass('hidden');
                    $('#label_yes_5').removeClass('hidden');
                    $('#label_process_6').addClass('hidden');
                    $('#label_yes_6').removeClass('hidden');
                    $('#label_process_7').addClass('hidden');
                    $('#label_yes_7').removeClass('hidden');
                    $('#label_process_8').addClass('hidden');
                    $('#label_yes_8').removeClass('hidden');
                    $('#label_process_9').addClass('hidden');
                    $('#label_yes_9').removeClass('hidden');
                    $('#label_process_10').addClass('hidden');
                    $('#label_yes_10').removeClass('hidden');
                }
                if (no_exitos == 11) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                    $('#label_process_3').addClass('hidden');
                    $('#label_yes_3').removeClass('hidden');
                    $('#label_process_4').addClass('hidden');
                    $('#label_yes_4').removeClass('hidden');
                    $('#label_process_5').addClass('hidden');
                    $('#label_yes_5').removeClass('hidden');
                    $('#label_process_6').addClass('hidden');
                    $('#label_yes_6').removeClass('hidden');
                    $('#label_process_7').addClass('hidden');
                    $('#label_yes_7').removeClass('hidden');
                    $('#label_process_8').addClass('hidden');
                    $('#label_yes_8').removeClass('hidden');
                    $('#label_process_9').addClass('hidden');
                    $('#label_yes_9').removeClass('hidden');
                    $('#label_process_10').addClass('hidden');
                    $('#label_yes_10').removeClass('hidden');
                    $('#label_process_11').addClass('hidden');
                    $('#label_yes_11').removeClass('hidden');
                }
                if (no_exitos == 12) {
                    $('#label_process_1').addClass('hidden');
                    $('#label_yes_1').removeClass('hidden');
                    $('#label_process_2').addClass('hidden');
                    $('#label_yes_2').removeClass('hidden');
                    $('#label_process_3').addClass('hidden');
                    $('#label_yes_3').removeClass('hidden');
                    $('#label_process_4').addClass('hidden');
                    $('#label_yes_4').removeClass('hidden');
                    $('#label_process_5').addClass('hidden');
                    $('#label_yes_5').removeClass('hidden');
                    $('#label_process_6').addClass('hidden');
                    $('#label_yes_6').removeClass('hidden');
                    $('#label_process_7').addClass('hidden');
                    $('#label_yes_7').removeClass('hidden');
                    $('#label_process_8').addClass('hidden');
                    $('#label_yes_8').removeClass('hidden');
                    $('#label_process_9').addClass('hidden');
                    $('#label_yes_9').removeClass('hidden');
                    $('#label_process_10').addClass('hidden');
                    $('#label_yes_10').removeClass('hidden');
                    $('#label_process_11').addClass('hidden');
                    $('#label_yes_11').removeClass('hidden');
                    $('#label_process_12').addClass('hidden');
                    $('#label_yes_12').removeClass('hidden');
                }



            //success: function (html) {
            //    $('#scheduler_publish').html(html);
           // }
        });
        
       
    }
</script>	
