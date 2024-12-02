<?php
  # Librerias
  require '../../lib/general.inc.php';
  $instituto = RecibeParametroNumerico('instituto');
  $programa = RecibeParametroNumerico('programa');
  
?>
<form action="students.php" method='POST' name='frm_search_fame' id='frm_search_fame' class='smart-form'>
  <div class="col-sm-12 col-md-12 col-lg-4">
    <label><strong><?php echo ObtenEtiqueta(2061); ?> </strong></label>
    <select class="select2" name="fl_instituto_params" id="fl_instituto_params" onchange="busqueda(this.value)">
    <option selected="selected" value='0'> <?php echo ObtenEtiqueta(2065); ?> </option>
    <?php
    # Obtenemos todas las instituciones
    $Query  = "SELECT i.ds_instituto, i.fl_instituto ";
    $Query .= "FROM c_instituto i ";
    $Query .= "WHERE exists(SELECT 1 FROM c_usuario u WHERE u.fl_instituto=i.fl_instituto AND u.fl_perfil_sp=".PFL_MAESTRO_SELF.") ORDER BY i.ds_instituto;";
     $rs = EjecutaQuery($Query);
     for($i=0;$row=RecuperaRegistro($rs); $i++){
       $ds_instituto = str_texto($row[0]);
       $fl_instituto = $row[1];
       $selected = "";
       if($fl_instituto==$instituto)
         $selected = "selected";
       echo "<option ".$selected." value='".$fl_instituto."' >".$ds_instituto."</option>";
     }
    ?>
    </select>
    <script>
    function busqueda(instituto,programa){        
      $('#frm_div_fame_search').css('display', 'block');
      $.ajax({
          type: 'POST',
          url: 'div_dialogo_busqueda_tea.php',
          async: false,
          data: 'instituto=' + instituto + '&programa='+ programa,
          success: function (html) {
              $('#frm_div_fame_search').html(html);
          }
      });
    }
    pageSetUp();
    </script>
  </div>
  <div class="col-sm-12 col-md-12 col-lg-4">
    <label><strong><?php echo ObtenEtiqueta(2062); ?> </strong></label>
    <select class="select2" name="fl_programa_params" id="fl_programa_params">
    <option selected="selected" value='0'> <?php echo ObtenEtiqueta(2065); ?> </option>
    <?php
    # Obtenemos los programas que estan cursando las instituciones
    $Query  = "SELECT b.fl_programa_sp, c.nb_programa FROM c_usuario a ";
    $Query .= "JOIN k_usuario_programa b ON (b.fl_usuario_sp=a.fl_usuario) ";
    $Query .= "JOIN c_programa_sp c ON (c.fl_programa_sp=b.fl_programa_sp) ";
    $Query .= "WHERE fl_instituto=".$instituto." AND fl_perfil_sp=".PFL_MAESTRO_SELF." GROUP BY b.fl_programa_sp ";
     $rs = EjecutaQuery($Query);
     for($i=0;$row=RecuperaRegistro($rs); $i++){
       $fl_programa_sp = str_texto($row[0]);
       $nb_programa = $row[1]; 
       # Buscamos la cantidad de estudiantes que estan en el curso
       $Queryy  = "SELECT COUNT(*) FROM k_usuario_programa a ";
       $Queryy .= "LEFT JOIN c_usuario b ON(b.fl_usuario=a.fl_usuario_sp) ";
       $Queryy .= "WHERE a.fl_programa_sp=".$fl_programa_sp."  AND fl_instituto=".$instituto." AND fl_perfil_sp=".PFL_MAESTRO_SELF;
       $roww = RecuperaValor($Queryy);
      if($fl_programa_sp==$programa)
         $selected = "selected";       
       echo "<option value='".$fl_programa_sp."'>".$nb_programa." (".$roww[0].")</option>";
     }
    ?>
    </select>
  </div>
</form>