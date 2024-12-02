<?php
	
require '../../lib/general.inc.php';

$QueryS="SELECT no_semana,fl_leccion,ds_titulo,fg_tipo_clase,fl_maestro,fl_periodo,nb_grupo,ds_titulo FROM 
                k_clase_calendar 
                WHERE fl_clase_calendar=$fl_clase_calendar ";
$rows=RecuperaValor($QueryS);
$fl_leccion=$rows['fl_leccion'];
$no_semana=$rows['no_semana'];
$nb_clase=$rows['ds_titulo'];
$fg_tipo_clase=$rows['fg_tipo_clase'];
$nb_grupo=$rows['nb_grupo'];
$fl_maestro=$rows['fl_maestro'];
$fl_periodo=$rows['fl_periodo'];
$nb_grupo=$rows['nb_grupo'];

?>
<!--------------modal para edicion de horarios y asignaciones terms y grupos. (clicka un evento del calendario)---------------->
<div class="modal fade" id="modal_scheduler_preview" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width:80%;margin:auto;margin-top:70px;">
    <div class="modal-content" id="evento_modal_edit">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Scheduler</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">     

          <div id="scheduler_search_preview"></div>



        <table class="table table-hover" width="100%">
            <thead>
                <tr>
                    <th>Week</th>
                    <th>Tot. clases</th>
                    <th>Tot. students</th>
                    <th>Zoom Licenses disponibles</th>
                    <th>Zoom Licenses used</th>
                    <th>Published</th>                  
                    
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $Query="SELECT a.fl_clase_calendar,a.nb_grupo,a.ds_titulo,DATE_FORMAT(a.fe_inicio,'%Y-%m-%d')fe_inicio,a.no_grado,a.no_semana,
                        b.nb_programa,c.ds_titulo nb_leccion,p.nb_periodo,u.ds_nombres,u.ds_apaterno,a.fg_estatus, DATE_FORMAT(a.fe_inicio,'%r')hr_inicio 
                        FROM k_clase_calendar a 
                        JOIN c_programa b ON b.fl_programa=a.fl_programa
                        JOIN c_periodo p ON p.fl_periodo=a.fl_periodo
                        JOIN c_leccion c ON c.fl_leccion=a.fl_leccion
                        JOIN c_usuario u ON u.fl_usuario=a.fl_maestro  
                        ";
                $rs=EjecutaQuery($Query);
                for($i=1;$row=RecuperaRegistro($rs);$i++){
                    $fl_clase_claendar = $row['fl_clase_calendar'];
                    $nb_grupo = $row['nb_grupo'];
                    $ds_titulo = $row['ds_titulo'];
                    $fe_inicio = $row['fe_inicio'];
                    $no_grado = $row['no_grado'];
                    $no_semana = $row['no_semana'];
                    $nb_programa = $row['nb_programa'];
                    $nb_leccion=$row['nb_leccion'];
                    $nb_periodo=$row['nb_periodo'];
                    $nb_teacher=$row['ds_nombres']." ".$row['ds_apaterno'];
                    $fg_estatus=$row['fg_estatus'];
                    $hr_inicio=$row['hr_inicio'];

                    #DAMOS FORMATO DIA,MES,AÑO
                    $date=date_create($fe_inicio);
                    $fe_inicio=date_format($date,'F j, Y');


                    switch ($fg_estatus) {
                        case 'C':
                            $label = 'NO';
                            $color = 'danger';
                            break;

                        case 'P':
                            $label = 'YES';
                            $color = 'success';
                            break;
                    }

                ?>


                <tr>
                    <th scope="row"><?php echo $i;?></th>
                    <td class="text-center"><?php echo $no_semana;?></td>
                    <td><?php echo $nb_periodo;?> <br /><small class="text-muted">Term: <?php echo $no_grado;?></small></td>
                    <td><?php echo $nb_programa;?></td>
                    <td><?php echo $ds_titulo;?></td>
                    <td><?php echo $nb_grupo;?></td>
                    <td><?php echo $nb_teacher;?></td>
                    <td><?php echo $fe_inicio;?> <?php echo $hr_inicio;?></td>
                    <td class="text-center"><label class="label label-<?php echo $color;?>"><?php echo $label;?></label></td>
                </tr>
                <?php 
                }
                ?>


            </tbody>
        </table>




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
