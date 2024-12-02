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
    <label class='hidden'><strong><?php echo ObtenEtiqueta(2061); ?> </strong></label>
    <select class="select2" name="fl_param" id="fl_param">
    <option selected="selected" value='0'> <?php echo ObtenEtiqueta(2065); ?> </option>
    <?php
     $titulos = array(ObtenEtiqueta(60),"Program Name");
     $tot_tit_arreglo = count($titulos);
	 
     for ($i = 0; $i < $tot_tit_arreglo; $i++) {
         
         if($titulos[$i]==''.ObtenEtiqueta(60).'')
            $selected = "selected";
         else
            $selected=""; 
         echo "<option ".$selected." value='" . $titulos[$i] . "'  >$titulos[$i]  </option> ";
    
     }
    ?>
    </select>
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
            url: 'muetra_filtro.php',
            data: 'fl_param='+fl_param,

            async: false,
            success: function (html) {
                $('#muestra_filtro').html(html);


            }
        });




    }

</script>

