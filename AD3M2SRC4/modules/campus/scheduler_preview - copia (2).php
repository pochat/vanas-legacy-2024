<?php
	
require '../../lib/general.inc.php';

#Obtenemos datos.
$fe_inicio="2021-01-04";
$fe_final="2021-04-04";

$tot_semanas=12;

#Formato a las fechas.
$date=date_create($fe_inicio);
$fe_from=date_format($date,'F j, Y');

#Formato a las fechas.
$date=date_create($fe_final);
$fe_to=date_format($date,'F j, Y');


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
  
            <h3 class="text-center"><b>From: <?php echo $fe_from;?>.&nbsp;&nbsp;&nbsp;&nbsp;   To: <?php echo $fe_to;?>.</b></h3>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">     

          <div id="scheduler_search_preview"></div>


        <div id="table_preview" style="max-height:320px;overflow:auto;">
        
        <table class="table table-hover" width="100%">
            <thead>
                <tr>
                    <th class='text-center'>&nbsp;</th>
                    <th class='text-center'>Week</th>
                    <th class='text-center'>Tot. classes</th>
                    <th class='text-center'>Tot. students</th>
                    <th class='text-center'>Zoom Licenses</th>
                    <th class='text-center'>Published</th>                  
                </tr>
            </thead>
            <tbody>
              <?php 

              #Recuperamos los periodos a futuro.
              $Query="SELECT fl_periodo,nb_periodo FROM c_periodo WHERE fe_inicio >='$fe_inicio' AND fe_inicio <= '$fe_final'  ";
              $rs=EjecutaQuery($Query);
              for($a=1;$row=RecuperaRegistro($rs);$a++) {
                  $fl_periodo=$row['fl_periodo'];
                  $nb_periodo=$row['nb_periodo'];

                 // echo "<tr>";
                 // echo "    <td colspan='4'>$nb_periodo</td>";
                 // echo "</tr>";

                  #Recuperamos el resumen de las 12 semanas que comprende ese periodo.
                  $count=0;
                  $tot_semanas=12;
                  $a=0;
                  for($a = 0; $a < $tot_semanas; $a++){
                      $count++;
                      $no_semana=$count;


                      $Query2="SELECT COUNT(*) FROM k_clase_calendar WHERE fl_periodo=$fl_periodo AND no_semana=$no_semana ";
                      $row2=RecuperaValor($Query2);
                      $no_clases=$row2[0];
                      
                      $Query3="SELECT COUNT(*)  FROM k_clase_calendar_alumno WHERE fl_clase_calendar IN (SELECT fl_clase_calendar FROM k_clase_calendar a WHERE a.fl_periodo=$fl_periodo AND a.no_semana=$no_semana) ";
                      $row3=RecuperaValor($Query3);
                      $no_alumnos=$row3[0];

                      #Recuperamos el estsus del proceso por semana y por periodo.
                      $Query4="SELECT fg_estatus FROM k_clase_calendar_semana_status WHERE fl_periodo=$fl_periodo AND no_semana=$no_semana  ";
                      $row4=RecuperaValor($Query4);
                      $fg_status=$row4['fg_estatus'];

                      switch ($fg_status) {
                          case '0':
                              $label="NO";
                              $color="danger";
                              break;
                          case '1':
                              $label="YES";
                              $color="success";
                              break;
                          default:
                              $label="NO";
                              $color="danger";
                              break;
                      }



                      echo "<tr>";
                      echo "    <th class='text-center'><label class='checkbox no-padding no-margin'><input class='checkbox' type='checkbox' id='ch_$no_semana' value='$no_semana'><span></span></label><input type='hidden' id='use_lic_$no_semana' name='use_lic_$no_semana' value='1'> </th> ";
                      echo "    <th class='text-center'>$no_semana</th>";
                      echo "    <td class='text-center'>$no_clases</td>";
                      echo "    <td class='text-center'>$no_alumnos</td>";
                      echo "    <td>";
                      echo "    <table width='100%'  class='table'>
                                     <tr><th class='text-center'>Zoom Host Id</th><th class='text-center'>Available</th><th class='text-center'>Used</th></tr>";
                      $QueryS1="SELECT id,no_request,host_email_zoom FROM zoom WHERE fg_activo='1' ";
                      $rsS1 = EjecutaQuery($QueryS1);
                      for($tot_zo1 = 0; $row1 = RecuperaRegistro($rsS1); $tot_zo1++) {
                          $id_zoom=$row1['host_email_zoom'];
                          $no_request=$row1[1];
                          $total=100;
                          $disponible=100-$no_request;

                          echo " <tr><td class='text-left'>".$id_zoom."</td><td class='text-center'>$disponible</td><td class='text-center'>$no_request</td></tr>";
                          
                      }
                      echo"     </table>";
                      echo "    </td>";
                      echo "    <td class='text-center'><label class='label label-$color'>$label</label></td>";

                      echo "</tr>";



                  }

              }

              
             




              
              ?>
             


            </tbody>
        </table>
        </div>



      </div>
      <div class="modal-footer">
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="ActionEvent();">Save changes</button>-->
        <p class="text-center"><a class="btn btn-success" style="border-radius: 10px;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-check-circle-o"></i> Publish&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>

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

        var fe_start_date = document.getElementById('fe_start_date').value;
        var fe_end_date = document.getElementById('fe_end_date').value;

        //pasamos los valores por ajax para salvar datos.
        $.ajax({
            type: 'POST',
            url: 'scheduler_search_preview.php',
            data: 'fe_start_date=' + fe_start_date +
                  '&fe_end_date=' + fe_end_date,
            async: false,
            success: function (html) {
                $('#scheduler_search_preview').html(html);
            }
        });


    }
</script>	
