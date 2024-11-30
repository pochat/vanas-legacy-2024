<?php

	# Libreria de funciones
	require("../lib/self_general.php");

?>
  <div class="form-group">
                  
      <label><?php echo ObtenEtiqueta(1256); ?></label>
      <select multiple style="width:100%" class="select2" onchange="FiltraCategorias(this.value, 1, 1, 1); ActualizaFtoCat(); MuestraFiltro(); LimpiaFto2();" id="datos2" placeholder="<?php echo ObtenEtiqueta(1258); ?>">
      
        <!-- Field of Study -->
        <optgroup label="<?php echo ObtenEtiqueta(1306); ?>">
        <?php
          $Query  = "SELECT nb_categoria, fl_cat_prog_sp FROM c_categoria_programa_sp WHERE fg_categoria = 'FOS' ORDER BY fg_categoria, nb_categoria ";
          $rs = EjecutaQuery($Query);
          for($i=0;$row = RecuperaRegistro($rs);$i++){
            $nb_categoria = str_texto($row[0]);
            $fl_cat_prog_sp = ($row[1]);
              echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
          }   
        ?>
        </optgroup>
        <!-- Field of Study -->
        <optgroup label="<?php echo ObtenEtiqueta(1307); ?>">
        <?php
          $Query  = "SELECT nb_categoria, fl_cat_prog_sp FROM c_categoria_programa_sp WHERE fg_categoria = 'CAT' ORDER BY fg_categoria, nb_categoria ";
          $rs = EjecutaQuery($Query);
          for($i=0;$row = RecuperaRegistro($rs);$i++){
            $nb_categoria = str_texto($row[0]);
            $fl_cat_prog_sp = ($row[1]);
              echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
          }   
        ?>
        </optgroup>
        <!-- School Level -->
        <optgroup label="<?php echo ObtenEtiqueta(1308); ?>">
        <?php
          $Query  = "SELECT nb_grado, fl_grado FROM k_grado_fame ";
          $rs = EjecutaQuery($Query);
          for($i=0;$row = RecuperaRegistro($rs);$i++){
            $nb_categoria = str_texto($row[0]);
            $fl_cat_prog_sp = ($row[1]);
              echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
          }   
        ?>
        </optgroup>														
        <!-- Hardware -->
        <optgroup label="<?php echo ObtenEtiqueta(1309); ?>">
        <?php
          $Query  = "SELECT nb_categoria, fl_cat_prog_sp FROM c_categoria_programa_sp WHERE fg_categoria = 'HAR' ORDER BY fg_categoria, nb_categoria ";
          $rs = EjecutaQuery($Query);
          for($i=0;$row = RecuperaRegistro($rs);$i++){
            $nb_categoria = str_texto($row[0]);
            $fl_cat_prog_sp = ($row[1]);
              echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
          }   
        ?>
        </optgroup>
        <!-- Software -->
        <optgroup label="<?php echo ObtenEtiqueta(1310); ?>">
        <?php
          $Query  = "SELECT nb_categoria, fl_cat_prog_sp FROM c_categoria_programa_sp WHERE fg_categoria = 'SOF' ORDER BY fg_categoria, nb_categoria ";
          $rs = EjecutaQuery($Query);
          for($i=0;$row = RecuperaRegistro($rs);$i++){
            $nb_categoria = str_texto($row[0]);
            $fl_cat_prog_sp = ($row[1]);
              echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
          }   
        ?>
        </optgroup>
        <!-- Level -->
        <optgroup label="<?php echo ObtenEtiqueta(1311); ?>">
          <option value='<?php echo "LVB"; ?>'><?php echo ObtenEtiqueta(1317); ?></option>
          <option value='<?php echo "LVI"; ?>'><?php echo ObtenEtiqueta(1321); ?></option>
          <option value='<?php echo "LVA"; ?>'><?php echo ObtenEtiqueta(1322); ?></option>
        </optgroup>
        <!-- Course Code -->
        <optgroup label="<?php echo ObtenEtiqueta(1312); ?>">
        <?php
          $Query  = "SELECT CONCAT(nb_programa, ' (code: ', ds_course_code ,')') as programa, fl_programa_sp FROM c_programa_sp ORDER BY nb_programa ";                                  
          $rs = EjecutaQuery($Query);
          for($i=0;$row = RecuperaRegistro($rs);$i++){
            $nb_categoria = str_texto($row[0]);
            $fl_programa_sp_fto = ($row[1]);
              echo "<option value='{$fl_programa_sp_fto}'>{$nb_categoria}</option>";
          }   
        ?>
        </optgroup>
        
      </select>

  </div> 
  
<script>
$(document).ready(function(){
  pageSetUp();
  alert('Carga');
});
</script>