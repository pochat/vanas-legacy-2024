<?php

  # <!-- Estas categorias son las sugerencias -->

	# Libreria de funciones
	require("../lib/self_general.php");
  
  $fl_usuario = ValidaSesion(False,0, True);
  
  $perfil_usuario = ObtenPerfilUsuario($fl_usuario);
  # Accion principal
  $accion = $_REQUEST['accion']; 
  # Valor (cadena)a buscar
  $valor = $_REQUEST['valor']; 
  # Valor (cadena)a buscar
  $valor = $_REQUEST['valor'];
  
  # 1.- Obtenemos categoria padre
  $row_padre = RecuperaValor("SELECT fl_cat_prog_sp FROM k_cat_prog_rel_usu_sp WHERE fl_usuario_sp = $fl_usuario AND fg_principal = '1'");  
  $fl_cat_prog_sp_padre = $row_padre[0];
  
  if(empty($fl_cat_prog_sp_padre)){
    echo "<script>
      document.getElementById('muestra_div_ftos2').style.display = 'none';
      document.getElementById('test_prueba').style.display = 'block';
      LimpiaFto2();
    </script>";
  }
  
  if(is_numeric($fl_cat_prog_sp_padre)){
  
    # 2.- Obtenemos todos los cursos de la categoria principal
    $Query_2  = "SELECT fl_programa_sp FROM k_categoria_programa_sp WHERE fl_cat_prog_sp = $fl_cat_prog_sp_padre";
    $rs_2 = EjecutaQuery($Query_2);
    $reg_rs2 = CuentaRegistros($rs_2);
    $Query_comp = "";
    for($i_2=0;$row_2 = RecuperaRegistro($rs_2);$i_2++){
      $fl_programa_sp = $row_2[0];
      if($i_2 == 0)
        $Query_comp .= " k.fl_programa_sp = $fl_programa_sp ";
      else
        $Query_comp .= " OR k.fl_programa_sp = $fl_programa_sp ";
    }  
  
    if(!empty($reg_rs2)){
      # 3.- Obtenemos las demas categorias de los cursos que tiene la categoria padre
      $Query  = " SELECT c.nb_categoria, c.fl_cat_prog_sp, c.fg_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE ($Query_comp) AND k.fl_cat_prog_sp = c.fl_cat_prog_sp ";
      $Query .= " AND NOT EXISTS (SELECT * FROM k_cat_prog_rel_usu_sp b WHERE b.fl_usuario_sp = $fl_usuario AND c.fl_cat_prog_sp = b.fl_cat_prog_sp) ";
      $Query .= " GROUP BY c.fl_cat_prog_sp ORDER BY fg_categoria, nb_categoria   ";
        
      # Para mostrar los niveles pertenecientes a los cursos sugeridos
      $Query_lvl  = " SELECT k.fl_programa_sp, p.fg_level FROM k_categoria_programa_sp k, c_categoria_programa_sp c, c_programa_sp p  WHERE ($Query_comp) AND k.fl_cat_prog_sp = c.fl_cat_prog_sp ";
      $Query_lvl .= " AND NOT EXISTS (SELECT * FROM k_cat_prog_rel_usu_sp b WHERE b.fl_usuario_sp = $fl_usuario AND c.fl_cat_prog_sp = b.fl_cat_prog_sp) AND k.fl_programa_sp = p.fl_programa_sp ";
      $Query_lvl .= " GROUP BY p.fg_level ";
      $rs_lvl = EjecutaQuery($Query_lvl);
      $arma_opt_2 = "";
      for($i_lvl=0;$row_lvl = RecuperaRegistro($rs_lvl);$i_lvl++){ 
        switch ($row_lvl[1]){
          case 'LVB': $ds_level = ObtenEtiqueta(1317); break;
          case 'LVI': $ds_level = ObtenEtiqueta(1321); break;
          case 'LVA': $ds_level = ObtenEtiqueta(1322); break;
        }
        $arma_opt = '
          <li class="select2-search-choice" style="font-size: 100%; font-weight: normal; background-color:#82D7DE; border-color:#82D7DE; padding-right: 8px;">
                    <a href="javascript:FiltraCategorias(\''.$row_lvl[1].'\', 0, 0); ActualizaFtoCat(); MuestraFiltro();" >
                      <div style="color:#fff;">'.$ds_level.'</div>
                    </a>  
                  </li>
        ';
        $arma_opt_2 = $arma_opt_2.$arma_opt;
      }
        
      # Para mostrar los grados pertenecientes a los cursos sugeridos
      $Query_gde  = " SELECT g.fl_grado, g.nb_grado FROM k_categoria_programa_sp k, c_categoria_programa_sp c, k_grade_programa_sp p, k_grado_fame g WHERE ($Query_comp) AND k.fl_cat_prog_sp = c.fl_cat_prog_sp ";
      $Query_gde .= " AND NOT EXISTS (SELECT * FROM k_cat_prog_rel_usu_sp b WHERE b.fl_usuario_sp = $fl_usuario AND c.fl_cat_prog_sp = b.fl_cat_prog_sp) AND k.fl_programa_sp = p.fl_programa_sp AND g.fl_grado = p.fl_grado ";
      $Query_gde .= " GROUP BY p.fl_grado ";
      $rs_gde = EjecutaQuery($Query_gde);
      $arma_gde_2 = "";
      for($i_gde=0;$row_gde = RecuperaRegistro($rs_gde);$i_gde++){ 
        $ds_gde = str_texto($row_gde[1]);

        $arma_gde = '
          <li class="select2-search-choice" style="font-size: 100%; font-weight: normal; background-color:#B482DE; border-color:#B482DE; padding-right: 8px;">
                    <a href="javascript:FiltraCategorias(\'G-'.$row_gde[0].'\', 0, 0); ActualizaFtoCat(); MuestraFiltro();" >
                      <div style="color:#fff;">'.$ds_gde.'</div>
                    </a>  
                  </li>
        ';
        $arma_gde_2 = $arma_gde_2.$arma_gde;
      }
      
    }else{
      # 3.- Obtenemos las demas categorias de los cursos que tiene la categoria padre
      $Query  = " SELECT nb_programa".$sufix.", fl_programa_sp FROM c_programa_sp WHERE fl_programa_sp = $fl_cat_prog_sp_padre";
    }
    $rs = EjecutaQuery($Query);
    $cuent_cuantos = CuentaRegistros($rs);

?>
    <!-- Estas categorias son las sugerencias -->
    <p></p>
    <div class="select2-container select2-container-multi select2" id="s2id_datos" style="width:100%">
      <ul class="select2-choices">  
        <?php
          for($i=0;$row = RecuperaRegistro($rs);$i++){
            $nb_categoria = str_texto($row[0]);
            $fl_cat_prog_sp = ($row[1]);
            $fg_categoria = str_texto($row[2]);
            switch ($fg_categoria){
              case 'CAT': $color = "background-color:#DE82C9; border-color:#DE82C9;"; break;
              case 'SOF': $color = "background-color:#82A5DE; border-color:#82A5DE;"; break;
              case 'HAR': $color = "background-color:#8682DE; border-color:#8682DE;"; break;
              case 'CCE': $color = "background-color:#82DE82; border-color:#82DE82;"; break;
              case 'CCS': $color = "background-color:#C2DE82; border-color:#C2DE82;"; break;
              case 'FOS': $color = "background-color:#DE8294; border-color:#DE8294;"; break;
            }
            
            echo "<li class='select2-search-choice' style='font-size: 100%; font-weight: normal; {$color} padding-right: 8px;'>
                    <a href='javascript:FiltraCategorias($fl_cat_prog_sp, 0, 0); ActualizaFtoCat(); MuestraFiltro();' >
                      <div style='color:#fff;'>".$nb_categoria."</div>
                    </a>  
                  </li>";
          }
          echo $arma_opt_2;
          echo $arma_gde_2;
        ?>
      </ul>
    </div>
<?php
  }else{
    
    if((substr($fl_cat_prog_sp_padre, 0, 2))=="G-"){
      
      $grado = substr($fl_cat_prog_sp_padre, 2);
      
      # Busca por grado
      $Query  = " SELECT cc.nb_categoria, cc.fl_cat_prog_sp, cc.fg_categoria ";
      $Query .= " FROM k_grade_programa_sp kg, k_categoria_programa_sp kc, c_categoria_programa_sp cc ";
      $Query .= " WHERE kg.fl_grado = $grado ";
      $Query .= " AND kg.fl_programa_sp = kc.fl_programa_sp AND kc.fl_cat_prog_sp = cc.fl_cat_prog_sp ";
      $Query .= " GROUP BY kc.fl_cat_prog_sp ORDER BY cc.fg_categoria, cc.nb_categoria ";
      $rs = EjecutaQuery($Query);
      $cuent_cuantos = CuentaRegistros($rs);
      
      
      # Sugerencias de grados
        #1.- Obtenemos los programas que tienen el grado filtrado
        $rs_prog_gdo = EjecutaQuery("SELECT kg.fl_programa_sp FROM k_grade_programa_sp kg WHERE kg.fl_grado = $grado");
        $cont_prog_gdo = CuentaRegistros($rs_prog_gdo);
        $concat = "(";
        for($i_prog_gdo=0;$row_prog_gdo = RecuperaRegistro($rs_prog_gdo);$i_prog_gdo++){
            $concat = $concat."kg.fl_programa_sp = ".$row_prog_gdo[0];
            if($i_prog_gdo != ($cont_prog_gdo - 1))
              $concat = $concat." OR ";
            else
              $concat = $concat." ) ";
        }
      
        # 2.- Para mostrar los grados pertenecientes a los cursos sugeridos
        $Query_gde = " SELECT g.fl_grado, g.nb_grado FROM k_grade_programa_sp kg, k_grado_fame g WHERE kg.fl_grado != $grado AND $concat AND kg.fl_grado = g.fl_grado GROUP BY kg.fl_grado ";    
        $rs_gde = EjecutaQuery($Query_gde);
        $arma_gde_2 = "";
        for($i_gde=0;$row_gde = RecuperaRegistro($rs_gde);$i_gde++){ 
          $ds_gde = str_texto($row_gde[1]);
          $arma_gde = '
            <li class="select2-search-choice" style="font-size: 100%; font-weight: normal; background-color:#B482DE; border-color:#B482DE; padding-right: 8px;">
                      <a href="javascript:FiltraCategorias(\'G-'.$row_gde[0].'\', 0, 0); ActualizaFtoCat(); MuestraFiltro();" >
                        <div style="color:#fff;">'.$ds_gde.'</div>
                      </a>  
                    </li>
          ';
          $arma_gde_2 = $arma_gde_2.$arma_gde;
        }
      
    }
    else{
      
      // echo "fl_cat_prog_sp_padre = ' $fl_cat_prog_sp_padre'<br>";
      
      # 3.- Obtenemos las demas categorias de los cursos que tiene la categoria padre
      $Query  = " SELECT c.nb_categoria, c.fl_cat_prog_sp, c.fg_categoria FROM c_programa_sp p, k_categoria_programa_sp k, c_categoria_programa_sp c WHERE p.fg_level = '$fl_cat_prog_sp_padre' ";
      $Query .= " AND (SELECT COUNT(1) FROM c_leccion_sp l WHERE p.fl_programa_sp = l.fl_programa_sp ) > 0 ";
      $Query .= " AND p.fl_programa_sp = k.fl_programa_sp AND k.fl_cat_prog_sp = c.fl_cat_prog_sp ";
      $Query .= " GROUP BY c.fl_cat_prog_sp ORDER BY c.fg_categoria, c.nb_categoria ";
      $rs = EjecutaQuery($Query);
      $cuent_cuantos = CuentaRegistros($rs);
    }
    // echo $Query;
?>
    
    <p></p>
    <div class="select2-container select2-container-multi select2" id="s2id_datos" style="width:100%">
      <ul class="select2-choices">  
        <?php
          for($i=0;$row = RecuperaRegistro($rs);$i++){
            $nb_categoria = str_texto($row[0]);
            $fl_cat_prog_sp = ($row[1]);
            $fg_categoria = str_texto($row[2]);
            switch ($fg_categoria){
              case 'CAT': $color = "background-color:#DE82C9; border-color:#DE82C9;"; break;
              case 'SOF': $color = "background-color:#82A5DE; border-color:#82A5DE;"; break;
              case 'HAR': $color = "background-color:#8682DE; border-color:#8682DE;"; break;
              case 'CCE': $color = "background-color:#82DE82; border-color:#82DE82;"; break;
              case 'CCS': $color = "background-color:#C2DE82; border-color:#C2DE82;"; break;
              case 'FOS': $color = "background-color:#DE8294; border-color:#DE8294;"; break;
            }
            
            echo "<li class='select2-search-choice' style='font-size: 100%; font-weight: normal; {$color} padding-right: 8px;'>
                    <a href='javascript:FiltraCategorias($fl_cat_prog_sp, 0, 0); ActualizaFtoCat(); MuestraFiltro();' >
                      <div style='color:#fff;'>".$nb_categoria."</div>
                    </a>  
                  </li>";
          }

          echo $arma_gde_2;
        ?>
      </ul>
    </div>
<?php
  }
  
  
  if(empty($cuent_cuantos)){
    echo "<script>
      document.getElementById('muestra_div_ftos').style.display = 'none';
    </script>";
  }else{
    echo "<script>
      document.getElementById('muestra_div_ftos').style.display = 'block';
    </script>";    
  }
?>  