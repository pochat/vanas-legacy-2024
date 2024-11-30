<?php

  # <!-- Estas categorias son las que fueron seleccionadas seleccionadas  -->
  

	# Libreria de funciones
	require("../lib/self_general.php");
  
  $fl_usuario = ValidaSesion(False,0, True);
  $accion = RecibeParametroNumerico('accion');
  $fl_cat_prog_sp = RecibeParametroHTML('fl_cat_prog_sp');
  $nuevo_fto = RecibeParametroBinario('nuevo_fto');
  
  # Es nueva busqueda eliminamos historial que pueda existir 
  if(!empty($nuevo_fto))
    EjecutaQuery("DELETE FROM k_cat_prog_rel_usu_sp WHERE fl_usuario_sp = $fl_usuario");
  
  # Asignamos el que esta llegando como padre
  if($accion == 0){
    RecuperaValor("UPDATE k_cat_prog_rel_usu_sp SET fg_principal = '0' WHERE fl_usuario_sp = $fl_usuario AND fg_principal = '1'");
  }
  
  # Insertamos valor actual 
  if($accion != 2)
    EjecutaQuery("INSERT INTO k_cat_prog_rel_usu_sp (fl_usuario_sp, fl_cat_prog_sp, fg_principal) VALUES ($fl_usuario, '$fl_cat_prog_sp', '1')");
  
  # Con la clave 2 eliminamos la categoria filtrada
  if($accion == 2){
    # A) Eliminamos el registro 
      EjecutaQuery("DELETE FROM k_cat_prog_rel_usu_sp WHERE fl_usuario_sp = $fl_usuario AND fl_cat_prog_sp = '$fl_cat_prog_sp'");
    # B) Recuperamos el ultimo registro de acuerdo al orden filtrado
      $ult_fto = RecuperaValor("SELECT fl_cat_prog_rel_usu_sp FROM k_cat_prog_rel_usu_sp ORDER BY fl_cat_prog_rel_usu_sp DESC LIMIT 1");
    # C) Actualizamos como principal el ultimo filtro 
      EjecutaQuery("UPDATE k_cat_prog_rel_usu_sp SET fg_principal = '1' WHERE fl_cat_prog_rel_usu_sp = $ult_fto[0]");    
  }
  
  # Recuperamos filtro principal
  // $row_padre = RecuperaValor("SELECT fl_cat_prog_sp FROM k_cat_prog_rel_usu_sp WHERE fl_usuario_sp = $fl_usuario AND fg_principal = '1'");  
  
  // if(is_numeric($row_padre[0])){
  
    # Revisamos si existe relacion Categoria filtrada - Programa
    // $cont_val = RecuperaValor("SELECT fl_programa_sp FROM k_categoria_programa_sp WHERE fl_cat_prog_sp = $row_padre[0]");
    // echo "SELECT fl_programa_sp FROM k_categoria_programa_sp WHERE fl_cat_prog_sp = $row_padre[0]<br>";
    
    # Obtenemos valores a filtrar
    // if(!empty($cont_val[0]))
      // $Query  = "SELECT  a.nb_categoria, a.fl_cat_prog_sp, a.fg_categoria FROM c_categoria_programa_sp a, k_cat_prog_rel_usu_sp b WHERE b.fl_usuario_sp = $fl_usuario AND a.fl_cat_prog_sp = b.fl_cat_prog_sp  ";
    // else
      // $Query = "SELECT nb_programa, fl_programa_sp FROM c_programa_sp WHERE fl_programa_sp = $row_padre[0]";
    
    $Query = "SELECT fl_cat_prog_sp FROM k_cat_prog_rel_usu_sp WHERE fl_usuario_sp = $fl_usuario";
    $rs = EjecutaQuery($Query);
    
    
    ?>
     <label><?php echo ObtenEtiqueta(1256); ?></label>
  <div class="select2-container select2-container-multi select2" id="s2id_datos" style="width:100%">
    <ul class="select2-choices">
      <?php
        for($i=0;$row = RecuperaRegistro($rs);$i++){
          if(is_numeric($row[0])){
            $fl_cat_prog_sp = $row[0];
            $fl_cat_prog_sp_del = $row[0];
          }elseif((substr($row[0], 0, 2))=="P-"){
            $fl_cat_prog_sp = substr($row[0], 2);
            $fl_cat_prog_sp_del = $row[0];
          }elseif((substr($row[0], 0, 2))=="G-"){
            $fl_cat_prog_sp = $row[0];
            $fl_cat_prog_sp_del = $row[0];
          }else{
            $fl_cat_prog_sp = $row[0];
            $fl_cat_prog_sp_del = $row[0];
          }

          if(is_numeric($fl_cat_prog_sp)){          
            $cont_val = RecuperaValor("SELECT fl_programa_sp FROM k_categoria_programa_sp WHERE fl_cat_prog_sp = $fl_cat_prog_sp");
            if(!empty($cont_val[0]))
              $Query_2  = "SELECT  a.nb_categoria, a.fl_cat_prog_sp, a.fg_categoria FROM c_categoria_programa_sp a, k_cat_prog_rel_usu_sp b WHERE b.fl_usuario_sp = $fl_usuario AND b.fl_cat_prog_sp = $fl_cat_prog_sp AND a.fl_cat_prog_sp = b.fl_cat_prog_sp  ";
            else
              $Query_2 = "SELECT CONCAT(nb_programa".$sufix.", ' (code: ', ds_course_code ,')') as programa, fl_programa_sp FROM c_programa_sp WHERE fl_programa_sp = $fl_cat_prog_sp";

            $row_2 = RecuperaValor($Query_2);
            $nb_categoria = str_texto($row_2[0]);
            $fl_cat_prog_sp = ($row_2[1]);
            $fg_categoria = str_texto($row_2[2]);
            switch ($fg_categoria){
              case 'CAT': $color = "background-color:#DE82C9; border-color:#DE82C9;"; break;
              case 'SOF': $color = "background-color:#82A5DE; border-color:#82A5DE;"; break;
              case 'HAR': $color = "background-color:#8682DE; border-color:#8682DE;"; break;
              case 'CCE': $color = "background-color:#82DE82; border-color:#82DE82;"; break;
              case 'CCS': $color = "background-color:#C2DE82; border-color:#C2DE82;"; break;
              case 'FOS': $color = "background-color:#DE8294; border-color:#DE8294;"; break;
            }
          }
          else{  

            # Niveles
            switch ($row[0]){
              case 'LVB': $color = "background-color:#82D7DE; border-color:#82D7DE;"; break;
              case 'LVI': $color = "background-color:#82D7DE; border-color:#82D7DE;"; break;
              case 'LVA': $color = "background-color:#82D7DE; border-color:#82D7DE;"; break;
            }
          
            if((substr($row[0], 0, 2))=="G-"){
            $grado = substr($row[0], 2);
            $row_grado = RecuperaValor("SELECT nb_grado, fl_grado FROM k_grado_fame WHERE fl_grado = $grado");
            $nb_categoria = $row_grado[0];
                $color = "background-color:#B482DE;; border-color:#B482DE;";
              
            }else{
              switch ($fl_cat_prog_sp){
                case 'LVB': $nb_categoria = ObtenEtiqueta(1317); break;
                case 'LVI': $nb_categoria = ObtenEtiqueta(1321); break;
                case 'LVA': $nb_categoria = ObtenEtiqueta(1322); break;
                $color = "background-color:#82D7DE;; border-color:#82D7DE;"; 
              }
            }
          }

          echo '<li class="select2-search-choice" style="font-size: 100%; font-weight: normal; '."$color".' ">
                  <div style="color:#fff;">'."$nb_categoria".'</div>
                  <a href="javascript:FiltraCategorias('."'$fl_cat_prog_sp_del'".', 2, 0); ActualizaFtoCat(); MuestraFiltro();" class="select2-search-choice-close" tabindex="-1"></a>
                </li>';      
           

        }
      ?>
    </ul>
  </div>
    
    <?php
    
  // }else{


?>
  
  
  
  
  <!-- <label><?php # echo ObtenEtiqueta(1256); ?></label>
  <div class="select2-container select2-container-multi select2" id="s2id_datos" style="width:100%">
    <ul class="select2-choices">
      <?php
            // $color = "background-color:#82D7DE;; border-color:#82D7DE;"; 
            // switch ($row_padre[0]){
              // case 'LVB': $nb_level = ObtenEtiqueta(1317); break;
              // case 'LVI': $nb_level = ObtenEtiqueta(1321); break;
              // case 'LVA': $nb_level = ObtenEtiqueta(1322); break;
            // }
          
          // echo "<li class='select2-search-choice' style='font-size: 100%; font-weight: normal; {$color}'>
                  // <div style='color:#fff;'>$nb_level</div>
                  // <a href='javascript:FiltraCategorias($fl_cat_prog_sp, 2, 0); ActualizaFtoCat(); MuestraFiltro();' class='select2-search-choice-close' tabindex='-1'></a>
                // </li>";
      ?>
    </ul>
  </div>-->
<?php
  // }
?>  