<?php

	# Libreria de funciones
	require("../lib/self_general.php");

  $fl_usuario_sp = ValidaSesion(False,0, True);
  
  /* OBTENEMOS ULTIMA CATEGORIA FILTRADA */
  $Query  = "SELECT fl_cat_prog_sp, fl_fto_cat_sp FROM k_filtro_categoria_fame WHERE fl_usuario_sp = $fl_usuario_sp ORDER BY no_filtro DESC LIMIT 1";
  $rs = EjecutaQuery($Query);
  $row = RecuperaRegistro($rs);
  $fl_cat_prog_sp = $row[0]??NULL;
  $fl_fto_cat_sp = $row[1]??NULL;

  /* OBTENEMOS CATEGORIAS BUSCADAS (tipo CAT) */
  $Query  = "SELECT d.fl_cat_prog_sp, d.nb_categoria, d.fg_categoria, a.fl_fto_cat_sp ";
  $Query .= "FROM k_filtro_sugerencia_fame a, k_categoria_programa_sp b, c_categoria_programa_sp d ";
  $Query .= "WHERE a.fl_fto_cat_sp = $fl_fto_cat_sp AND a.fl_programa_sp = b.fl_programa_sp ";
  $Query .= "AND NOT EXISTS (SELECT c.fl_cat_prog_sp FROM k_filtro_categoria_fame c WHERE c.fg_tipo = 'CAT' AND c.fl_usuario_sp = $fl_usuario_sp AND b.fl_cat_prog_sp = c.fl_cat_prog_sp) ";
  $Query .= "AND b.fl_cat_prog_sp = d.fl_cat_prog_sp ";
  $Query .= "GROUP BY b.fl_cat_prog_sp ORDER BY d.fg_categoria, d.nb_categoria";
  // echo "<br><br>$Query<br><br>";
  $rs = EjecutaQuery($Query);
  $reg_rs = CuentaRegistros($rs);
  $opt = "";
  for($i=0;$row = RecuperaRegistro($rs);$i++){ 
    $fl_cat_prog_sp = $row[0];
    $nb_categoria = $row[1];
    $fg_categoria = $row[2];
    $fl_fto_fame_sp = $row[3];
    switch ($fg_categoria){
    case 'CAT': $color = "background-color:#DE82C9; border-color:#DE82C9;"; break;
    case 'SOF': $color = "background-color:#82A5DE; border-color:#82A5DE;"; break;
    case 'HAR': $color = "background-color:#8682DE; border-color:#8682DE;"; break;
    case 'CCE': $color = "background-color:#82DE82; border-color:#82DE82;"; break;
    case 'CCS': $color = "background-color:#C2DE82; border-color:#C2DE82;"; break;
    case 'FOS': $color = "background-color:#DE8294; border-color:#DE8294;"; break;
    case 'MAP': $color = "background-color:#5f9dca; border-color:#5f9dca"; break;
    
    }
    # SelSug($fl_cat_prog_sp, $fl_fto_fame_sp);
    $opt = $opt . "<li class='select2-search-choice' style='font-size: 100%; font-weight: normal; {$color} padding-right: 8px;'>
      <a href='javascript:FtaCat($fl_cat_prog_sp, 0); MtraFtoCatSel(); FtaCatSugerencias(); MtraResFtos();' >
        <div style='color:#fff;'>".$nb_categoria."</div>
      </a>  
    </li>";
  }
  
  
  /* ARMAMOS SUGERENCIAS DE GRADO */ 
  $Query_gdo  = " SELECT c.fl_grado, c.nb_grado FROM k_filtro_sugerencia_fame a, k_grade_programa_sp b, k_grado_fame c WHERE a.fl_fto_cat_sp = $fl_fto_cat_sp AND a.fl_programa_sp = b.fl_programa_sp ";
  $Query_gdo .= " AND b.fl_grado = c.fl_grado ";
  $Query_gdo .= " AND NOT EXISTS (SELECT d.fl_cat_prog_sp FROM k_filtro_categoria_fame d WHERE d.fg_tipo = 'GDO' AND d.fl_usuario_sp = $fl_usuario_sp AND SUBSTRING(d.fl_cat_prog_sp, 3) = c.fl_grado) ";
  $Query_gdo .= " GROUP BY b.fl_grado ORDER BY c.nb_grado ASC ";
  // echo "<br><br>$Query_gdo<br><br>";
  $rs_gdo = EjecutaQuery($Query_gdo);
  $reg_rs = CuentaRegistros($rs_gdo);
  $opt_gdo = "";
  for($i_gdo=0;$row_gdo = RecuperaRegistro($rs_gdo);$i_gdo++){ 
    $fl_grado = $row_gdo[0];
    $ds_gde = $row_gdo[1];
    # FiltraCategorias(\'G-'.$fl_grado.'\', 0, 0); ActualizaFtoCat(); MuestraFiltro();
    $opt_gdo = $opt_gdo . '<li class="select2-search-choice" style="font-size: 100%; font-weight: normal; background-color:#B482DE; border-color:#B482DE; padding-right: 8px;">
                      <a href="javascript:FtaCat(\'G-'.$fl_grado.'\', 0); MtraFtoCatSel(); FtaCatSugerencias(); MtraResFtos();" >
                        <div style="color:#fff;">'.$ds_gde.'</div>
                      </a>  
                    </li>';
  }
   
  
  /* ARMAMOS SUGERENCIAS DE LAVEL */ 
  $Query_gdo  = "SELECT b.fg_level FROM k_filtro_sugerencia_fame a, c_programa_sp b ";
  $Query_gdo .= "WHERE a.fl_fto_cat_sp = $fl_fto_cat_sp ";
  $Query_gdo .= "AND a.fl_programa_sp = b.fl_programa_sp ";
  $Query_gdo .= "AND NOT EXISTS (SELECT c.fl_cat_prog_sp FROM k_filtro_categoria_fame c WHERE c.fg_tipo = 'LVL' AND c.fl_usuario_sp = $fl_usuario_sp AND b.fg_level= c.fl_cat_prog_sp) AND b.fg_publico='1' ";
  $Query_gdo .= "GROUP BY b.fg_level ORDER BY b.fg_level ASC ";
  // echo "<br><br>$Query_gdo<br><br>";
  $rs_gdo = EjecutaQuery($Query_gdo);
  $reg_rs = CuentaRegistros($rs_gdo);
  $opt_lvl = "";
  for($i_gdo=0;$row_gdo = RecuperaRegistro($rs_gdo);$i_gdo++){ 
    $fl_grado = $row_gdo[0];
    $ds_gde = $row_gdo[1];
    
      switch ($row_gdo[0]){
          case 'LVB': $ds_level = ObtenEtiqueta(1317); break;
          case 'LVI': $ds_level = ObtenEtiqueta(1321); break;
          case 'LVA': $ds_level = ObtenEtiqueta(1322); break;
        }
        # FiltraCategorias(\''.$row_gdo[1].'\', 0, 0); ActualizaFtoCat(); MuestraFiltro();
        $opt_lvl = $opt_lvl . '
          <li class="select2-search-choice" style="font-size: 100%; font-weight: normal; background-color:#82D7DE; border-color:#82D7DE; padding-right: 8px;">
                    <a href="javascript:FtaCat(\''.$row_gdo[0].'\', 0); MtraFtoCatSel(); FtaCatSugerencias(); MtraResFtos();" >
                      <div style="color:#fff;">'.$ds_level.'</div>
                    </a>  
                  </li>
        ';
  }
  
  
  /*Arma sugerencias de programas. de courses code*/
  $Querycourse="SELECT B.fl_course_code,CONCAT (C.nb_course_code,' (code: ',C.cl_course_code,') - ',P.ds_pais,' ',K.ds_provincia) as course_code 
                FROM k_filtro_sugerencia_fame A 
                JOIN k_course_code_prog_fame B ON B.fl_programa_sp=A.fl_programa_sp 
                JOIN c_course_code C ON C.fl_course_code=B.fl_course_code 
                JOIN c_pais P ON P.fl_pais=C.fl_pais 
                JOIN k_provincias K ON K.fl_provincia=C.fl_estado 
                WHERE A.fl_fto_cat_sp = $fl_fto_cat_sp ";
  $Querycourse.="AND NOT EXISTS ( SELECT m.fl_cat_prog_sp  FROM k_filtro_categoria_fame m  WHERE m.fg_tipo = 'MAP' AND m.fl_usuario_sp =$fl_usuario_sp AND SUBSTRING(m.fl_cat_prog_sp, 3) = B.fl_course_code ) ";
  $rs_c = EjecutaQuery($Querycourse);
  $reg_rc = CuentaRegistros($rs_c);
  for($i_c=0;$row_c = RecuperaRegistro($rs_c);$i_c++){ 
      $fl_course_code = $row_c[0];
      $ds_course_code = str_texto($row_c[1]);
      
      $opt_cour = $opt_cour . '
          <li class="select2-search-choice" style="font-size: 100%; font-weight: normal; background-color:#5f9dca; border-color:#5f9dca; padding-right: 8px;">
                    <a href="javascript:FtaCat(\'K-'.$row_c[0].'\', 0); MtraFtoCatSel(); FtaCatSugerencias(); MtraResFtos();" >
                      <div style="color:#fff;">'.$ds_course_code.'</div>
                    </a>  
                  </li>
        ';
      
      
      
  }
  

?>  
    <div class="select2-container select2-container-multi select2" id="s2id_datos" style="width:100%">
      <ul class="select2-choices">  
        <?php echo $opt.$opt_gdo.$opt_lvl; ?> 
      </ul>
    </div>
