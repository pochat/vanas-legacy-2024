<?php
	
require '../../lib/general.inc.php';

#Recibe Parametros.
$fe_inicio=$_POST['fe_inicio'];
$fe_final=$_POST['fe_final'];

#Periodo actual.
$Query="SELECT * FROM c_periodo WHERE nb_periodo='April 5, 2021' ";
$row=RecuperaValor($Query);
$fl_periodo=$row['fl_periodo'];


#Realizamos formato para busqueda sql.
$fe_inicio=strtotime('0 days',strtotime($fe_inicio));
$fe_inicio= date('Y-m-d',$fe_inicio);

$fe_final=strtotime('0 days',strtotime($fe_final));
$fe_final= date('Y-m-d',$fe_final);

$tot_semanas=12;

#Formato a las fechas.
$date=date_create($fe_inicio);
$fe_from=date_format($date,'F j, Y');

#Formato a las fechas.
$date=date_create($fe_final);
$fe_to=date_format($date,'F j, Y');

$label="NO";
$fl_periodo_default=ObtenConfiguracion(143);
?>
<style>
  .parpadea {
  
  animation-name: parpadeo;
  animation-duration: 1s;
  animation-timing-function: linear;
  animation-iteration-count: infinite;

  -webkit-animation-name:parpadeo;
  -webkit-animation-duration: 1s;
  -webkit-animation-timing-function: linear;
  -webkit-animation-iteration-count: infinite;
}

@-moz-keyframes parpadeo{  
  0% { opacity: 1.0; }
  50% { opacity: 0.0; }
  100% { opacity: 1.0; }
}

@-webkit-keyframes parpadeo {  
  0% { opacity: 1.0; }
  50% { opacity: 0.0; }
   100% { opacity: 1.0; }
}

@keyframes parpadeo {  
  0% { opacity: 1.0; }
   50% { opacity: 0.0; }
  100% { opacity: 1.0; }
}
</style>
<h3 class="text-center"><b>From: <?php echo $fe_from;?>.&nbsp;&nbsp;&nbsp;&nbsp;   To: <?php echo $fe_to;?>.</b></h3>

<table class="table table-hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th class='text-center'>&nbsp;<label class='checkbox no-padding no-margin'><input class='checkbox' type='checkbox' id='sel_todo' name='sel_todo'><span></span></label></th>
                                            <th class='text-center'>Week</th>
                                            <th class='text-center'>Tot. classes</th>
                                            <th class='text-center'>Tot. students</th>
                                            <th class='text-center'>Processed classes </th>
                                            <th class='text-center'>Unprocessed classes</th>
                                            <th class='text-center'>Zoom Licenses</th>
                                            <th class='text-center'>Published</th>                  
                                        </tr>
                                    </thead>
                                    <tbody>
                                          <?php 

                                          #Recuperamos los periodos a futuro.
                                          $Query="SELECT fl_periodo,nb_periodo FROM c_periodo WHERE fl_periodo=$fl_periodo_default  ";
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


                                                  $Query2="SELECT COUNT(*) FROM k_clase_calendar WHERE fl_periodo=$fl_periodo AND no_semana=$no_semana AND  (fg_estatus<>'C' OR fg_estatus IS NULL )";
                                                  $row2=RecuperaValor($Query2);
                                                  $no_clases=$row2[0];
                                                  
                                                  $Query3="SELECT COUNT(*)  FROM k_clase_calendar_alumno WHERE fl_clase_calendar IN (SELECT fl_clase_calendar FROM k_clase_calendar a WHERE a.fl_periodo=$fl_periodo AND a.no_semana=$no_semana AND  (fg_estatus<>'C' OR fg_estatus IS NULL ) ) ";
                                                  $row3=RecuperaValor($Query3);
                                                  $no_alumnos=$row3[0];

                                                  $Query4="SELECT fg_estatus FROM k_clase_calendar WHERE no_semana=$no_semana AND fl_periodo=$fl_periodo ";
                                                  $row4=RecuperaValor($Query4);
                                                  $fg_status=$row4['fg_estatus'];

                                                   switch ($fg_status) {
                                                      case 'P':
                                                          $label="NO";
                                                          $color="danger";
                                                          $disabled_ck="";
                                                          break;
                                                      case 'C':
                                                          $label="YES";
                                                          $color="success";
                                                          $disabled_ck="";
                                                          break;
                                                      default:
                                                          $label="NO";
                                                          $color="danger";
                                                          $disabled_ck="";
                                                          break;
                                                   }

                                                   if($no_clases){
                                                       $label="NO";
                                                       $color="danger";
                                                       $disabled_ck="";

                                                   }

                                                  #Recuperamos el estsus del proceso por semana y por periodo.
                                                 // $Query4="SELECT fg_estatus FROM k_clase_calendar_semana_status WHERE fl_periodo=$fl_periodo AND no_semana=$no_semana  ";
                                                 // $row4=RecuperaValor($Query4);
                                                 // $fg_status=$row4['fg_estatus'];
                                                 /*
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
                                                  */


                                                  echo "<tr>";
                                                  echo "    <th class='text-center'><label class='checkbox no-padding no-margin'><input class='checkbox' type='checkbox' id='ch_semana_$no_semana' value='$no_semana' $disabled_ck><span></span></label><input type='hidden' id='use_lic_semana_$no_semana' name='use_lic_semana_$no_semana' value='1'> </th> ";
                                                  echo "    <th class='text-center'>$no_semana</th>";
                                                  echo "    <td class='text-center'>$no_clases</td>";
                                                  echo "    <td class='text-center'>$no_alumnos</td>";
                                                  echo "    <td class='text-center'><span id='tot_processs_$count'></span> </td>";
                                                  echo "    <td class='text-center'><span id='tot_no_process_$count'></span> </td>";
                                                  echo "    <td>";
                                                  echo "     <a class='' data-toggle='collapse' href='#collapse_$count' role='button' aria-expanded='false' aria-controls='collapseExample'>
                                                                <i class='fa fa-info-circle' aria-hidden='true'></i> Zoom
                                                             </a>
                                                            <div class='collapse' id='collapse_$count'>
                                                                <div class='card card-body'>
                                                                
                                                                ";

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

                                                  echo"</div>
                                                            </div>";

                                                  echo "    </td>";
                                                  echo "    <td class='text-center'>
                                                                <span id='label_no_$no_semana' class=''><label class='label label-$color'>$label</label></span>
                                                                <span id='label_process_$no_semana' class='parpadea hidden'><label class='label label-primary'>Processing...</label></span>
                                                                <span id='label_yes_$no_semana' class='hidden'><label class='label label-success'>Yes</label></span>
                                                            </td>";

                                                  echo "</tr>";

                                              }

                                          }
                                          
                                          ?>
                                   </tbody>
                               </table>
                               <input type="hidden" name="tot_sem" id="tot_sem" value="<?php echo $tot_semanas;?>" />
                               <input type="hidden" name="fl_periodo" id="fl_periodo" value="<?php $fl_periodo;?>" />



<script>

    // DO NOT REMOVE : GLOBAL FUNCTIONS!

    $(document).ready(function () {

        pageSetUp();

       
    });

    /** INICIO DE SELECIONAR TODOS CHECKBOX ***/
    $('#sel_todo').on('change', function () {
        var v_sel_todo = $(this).is(':checked'), i;
        var iTotalRecords = "<?php echo $tot_semanas; ?>";
        for (i = 1; i <= iTotalRecords; i++) {
            $("#ch_semana_" + i).prop('checked', v_sel_todo);
        }
    })

</script>