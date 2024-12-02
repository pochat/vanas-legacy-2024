<?php
  # Librerias
  require '../../lib/general.inc.php';
  $instituto = RecibeParametroNumerico('instituto');
  $programa = RecibeParametroNumerico('programa');
  
  
  
  
  #Obtenemos fecha actual :
  $Query3 = "Select CURDATE() ";
  $row = RecuperaValor($Query3);
  $fe_consulta =strtotime('-1 years',strtotime($row[0])); #restamos 1 años.
  $fecha1= date('d-m-Y',$fe_consulta);
  $fecha_dos=strtotime('0 days',strtotime($row[0]));
  $fecha2= date('d-m-Y',$fecha_dos);
  
  
  
?>
<form action="cycles.php" method='POST' name='frm_search_fame' id='frm_search_fame' class='smart-form'>
   <div class="col-sm-12 col-md-12 col-lg-4">
 <?php 
 $opc = array('All', 'Active','Inactive'); // Masculino, Femenino
 $val = array('All', 'Active','Inactive');
 Forma_CampoSelect('Status', False,'fl_param', $opc, $val, $fl_param,'','','','','col-sm-4','col-sm-8');

 $opc = array('All', 'Certificate','Diploma'); // Masculino, Femenino
 $val = array('All', 'Certificate','Diploma');
 Forma_CampoSelect('Program', False,'fg_opcion', $opc, $val, $fg_opcion,'','','','','col-sm-4','col-sm-8');

 echo"<div class='row'>";
 $Query  = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
 Forma_CampoSelectBD(ObtenEtiqueta(287), False, 'fl_pais', $Query, $fl_pais, $fl_pais_err, True, '', 'left', 'col col-sm-4', 'col col-sm-8');
 echo"</div>";


 ?>
</div>

  <div id="muestra_filtro"></div>

  
  
</form>


<script>
    $(document).ready(function () {
    $('#fl_param').change(function () {

        PresentaFilter();
    });

        PresentaFilter();

    });


    function PresentaFilter(fg_inicio) {

        var fl_param = document.getElementById('fl_param').value;

        $.ajax({
            type: 'POST',
            url: 'muetra_filtro_rpt.php',
            data: 'fl_param='+fl_param,

            async: false,
            success: function (html) {
                $('#muestra_filtro').html(html);


            }
        });




    }

</script>

