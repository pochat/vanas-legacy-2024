<?php
  # Librerias
  require '../../lib/general.inc.php';
  $instituto = RecibeParametroNumerico('instituto');
  $programa = RecibeParametroNumerico('programa');
  
  
  
  
  #Obtenemos fecha actual :
  $Query3 = "Select CURDATE() ";
  $row = RecuperaValor($Query3);
  $fe_consulta =strtotime('-2 years',strtotime($row[0])); #restamos 1 años.
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
     $titulos = array(ObtenEtiqueta(60));
     $tot_tit_arreglo = count($titulos);
	 
     for ($i = 0; $i < $tot_tit_arreglo; $i++) {
         $selected = "selected";
         echo "<option ".$selected." value='" . $titulos[$i] . "'  >$titulos[$i]  </option> ";
    
     }
    ?>
    </select>
  </div>
  <div class="col-sm-12 col-md-12 col-lg-4">
    <label class='hidden'><strong><?php echo ObtenEtiqueta(2062); ?> </strong></label>
    <?php echo"
	<label class='input'><input type='text' name='fe_uno' id='fe_uno' maxlength='10' class='datepicker hasDatepicker' value='$fecha1' placeholder='" . ObtenEtiqueta(643) . "'>" . Forma_Calendario("fe_uno") . "</label>
    ";
	?>	
  </div>
   <div class="col-sm-12 col-md-12 col-lg-4">
    <label class='hidden'><strong><?php echo ObtenEtiqueta(2062); ?> </strong></label>
    <?php echo"
	<label class='input'><input type='text' name='fe_dos' id='fe_dos' maxlength='10' class='datepicker hasDatepicker' value='$fecha2' placeholder='" . ObtenEtiqueta(642) . "'>" . Forma_Calendario("fe_dos") . "</label>
    ";
	?>	
  </div>
  
  
</form>